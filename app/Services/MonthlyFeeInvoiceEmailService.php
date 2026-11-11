<?php

namespace App\Services;

use App\Models\CartManagement;
use App\Models\Course;
use App\Models\Currency;
use App\Models\MonthlyFeeEmailLog;
use App\Models\Order;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;

class MonthlyFeeInvoiceEmailService
{
    public const TEMPLATE = 'Payment-create-student';
    public const BATCH_SIZE = 5;
    public const BATCH_DELAY_MINUTES = 15;
    public const CONFIRM_TOKEN_TTL_SECONDS = 3600;

    /**
     * Parse comma/semicolon/newline separated emails from a setting string.
     *
     * @return array<int, string>
     */
    public function parseEmailList(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $parts = preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $emails = [];
        foreach ($parts as $part) {
            $email = strtolower(trim($part));
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }

        return array_values(array_unique($emails));
    }

    /**
     * @param array{
     *   include_student?: bool|null,
     *   include_cc?: bool|null,
     *   include_bcc?: bool|null,
     *   cc_override?: string|null,
     *   bcc_override?: string|null,
     *   student_email?: string|null
     * }|null $overrides
     * @return array{to: array<int,string>, cc: array<int,string>, bcc: array<int,string>}
     */
    public function recipientsFromSettings(?array $overrides = null, ?string $studentEmail = null): array
    {
        $overrides = $overrides ?? [];

        $includeStudent = array_key_exists('include_student', $overrides) && $overrides['include_student'] !== null
            ? (bool) $overrides['include_student']
            : (string) get_option('monthly_fee_invoice_send_to_student', '1') === '1';

        $includeCc = array_key_exists('include_cc', $overrides) && $overrides['include_cc'] !== null
            ? (bool) $overrides['include_cc']
            : true;

        $includeBcc = array_key_exists('include_bcc', $overrides) && $overrides['include_bcc'] !== null
            ? (bool) $overrides['include_bcc']
            : true;

        $student = $studentEmail
            ?? ($overrides['student_email'] ?? null);

        $to = [];
        if ($includeStudent && $student && filter_var($student, FILTER_VALIDATE_EMAIL)) {
            $to[] = strtolower(trim($student));
        }

        $ccRaw = $overrides['cc_override'] ?? null;
        $bccRaw = $overrides['bcc_override'] ?? null;
        $ccSource = (is_string($ccRaw) && trim($ccRaw) !== '')
            ? $ccRaw
            : (string) get_option('monthly_fee_invoice_cc', '');
        $bccSource = (is_string($bccRaw) && trim($bccRaw) !== '')
            ? $bccRaw
            : (string) get_option('monthly_fee_invoice_bcc', '');

        $cc = $includeCc ? $this->parseEmailList($ccSource) : [];
        $bcc = $includeBcc ? $this->parseEmailList($bccSource) : [];

        // Deduplicate across roles
        $toLookup = $to;
        $cc = array_values(array_filter($cc, fn ($e) => !in_array($e, $toLookup, true)));
        $ccLookup = array_merge($toLookup, $cc);
        $bcc = array_values(array_filter($bcc, fn ($e) => !in_array($e, $ccLookup, true)));

        return [
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc,
        ];
    }

    public function shouldAttachPdf(): bool
    {
        return (string) get_option('monthly_fee_invoice_attach_pdf', '1') === '1';
    }

    /**
     * Base query for monthly fee invoice carts.
     */
    public function invoiceCartQuery(array $filters = []): Builder
    {
        $query = CartManagement::query()
            ->with(['user', 'course'])
            ->where('created_by_type', 2)
            ->where('is_monthly_cron', 1);

        if (!empty($filters['month'])) {
            $query->whereRaw('CAST(`month` AS UNSIGNED) = ?', [(int) $filters['month']]);
        }

        if (!empty($filters['year'])) {
            $query->whereRaw('CAST(`year` AS UNSIGNED) = ?', [(int) $filters['year']]);
        }

        if (!empty($filters['course_id'])) {
            $query->where('course_id', (int) $filters['course_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (!empty($filters['student_email'])) {
            $email = trim((string) $filters['student_email']);
            $query->whereHas('user', function ($q) use ($email) {
                $q->where('email', 'like', '%' . $email . '%');
            });
        }

        if (!empty($filters['student_search'])) {
            $search = trim((string) $filters['student_search']);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (isset($filters['has_pdf'])) {
            if ((string) $filters['has_pdf'] === '1') {
                $query->whereNotNull('invoice_pdf_path')->where('invoice_pdf_path', '!=', '');
            } elseif ((string) $filters['has_pdf'] === '0') {
                $query->where(function ($q) {
                    $q->whereNull('invoice_pdf_path')->orWhere('invoice_pdf_path', '');
                });
            }
        }

        if (!empty($filters['payment_status'])) {
            if ($filters['payment_status'] === 'unpaid') {
                $query->where('request_enrollment', 1);
            } elseif ($filters['payment_status'] === 'paid') {
                // Paid monthly fees typically leave cart or clear request_enrollment; also check paid orders.
                $query->where(function ($q) {
                    $q->where('request_enrollment', '!=', 1)
                        ->orWhereExists(function ($sub) {
                            $sub->selectRaw('1')
                                ->from('orders')
                                ->join('order_items as oi', 'oi.order_id', '=', 'orders.id')
                                ->whereColumn('oi.user_id', 'cart_management.user_id')
                                ->whereColumn('oi.course_id', 'cart_management.course_id')
                                ->whereRaw('LOWER(TRIM(orders.payment_status)) = ?', ['paid'])
                                ->whereRaw('CAST(COALESCE(oi.`month`, orders.`month`) AS UNSIGNED) = CAST(cart_management.`month` AS UNSIGNED)')
                                ->whereRaw('CAST(COALESCE(oi.`year`, orders.`year`) AS UNSIGNED) = CAST(cart_management.`year` AS UNSIGNED)');
                        });
                });
            }
        }

        if (empty($filters['include_zero_price'])) {
            $query->where('main_price', '>', 0);
        }

        return $query->orderByDesc('id');
    }

    public function isCartPaid(CartManagement $cart): bool
    {
        if ((int) $cart->request_enrollment !== 1) {
            return true;
        }

        $month = (int) ($cart->month ?: 0);
        $year = (int) ($cart->year ?: 0);
        if ($month < 1 || $year < 1) {
            return false;
        }

        return Order::query()
            ->join('order_items as oi', 'oi.order_id', '=', 'orders.id')
            ->where('oi.user_id', $cart->user_id)
            ->where('oi.course_id', $cart->course_id)
            ->whereRaw('LOWER(TRIM(orders.payment_status)) = ?', ['paid'])
            ->whereRaw('CAST(COALESCE(oi.`month`, orders.`month`) AS UNSIGNED) = ?', [$month])
            ->whereRaw('CAST(COALESCE(oi.`year`, orders.`year`) AS UNSIGNED) = ?', [$year])
            ->exists();
    }

    public function pdfExists(CartManagement $cart): bool
    {
        if (empty($cart->invoice_pdf_path)) {
            return false;
        }

        return is_file(public_path($cart->invoice_pdf_path));
    }

    /**
     * @param array|null $recipientOverrides
     * @return array{viewData: array, attachments: array, recipients: array, checkout_link: string}
     */
    public function buildPayload(CartManagement $cart, ?array $recipientOverrides = null): array
    {
        $cart->loadMissing(['user', 'course']);
        $user = $cart->user ?? User::find($cart->user_id);
        $course = $cart->course ?? Course::find($cart->course_id);

        $checkoutLink = route('student.checkout', ['type' => 1, 'location' => 1]);
        $viewData = [
            '{{USER_NAME}}' => $user->name ?? 'Student',
            '{{COURSE_NAME}}' => $course->title ?? 'COURSE',
            '{{COURSE_FEE}}' => (int) ($cart->main_price ?? 0),
            '{{CHECKOUT}}' => $checkoutLink,
        ];

        $recipients = $this->recipientsFromSettings($recipientOverrides, $user->email ?? null);

        $attachments = [];
        if ($this->shouldAttachPdf()) {
            $absolute = $this->ensureInvoicePdf($cart);
            if ($absolute) {
                $attachments[] = [
                    'path' => $absolute,
                    'name' => 'Invoice-' . ($course->title ?? 'course') . '.pdf',
                    'mime' => 'application/pdf',
                ];
            }
        }

        return [
            'viewData' => $viewData,
            'attachments' => $attachments,
            'recipients' => $recipients,
            'checkout_link' => $checkoutLink,
        ];
    }

    /**
     * Ensure PDF exists on disk; regenerate from challan view if missing.
     * Returns absolute path or null.
     */
    public function ensureInvoicePdf(CartManagement $cart): ?string
    {
        if ($this->pdfExists($cart)) {
            return public_path($cart->invoice_pdf_path);
        }

        try {
            if (!file_exists(public_path('invoices'))) {
                mkdir(public_path('invoices'), 0755, true);
            }
            if (!file_exists(public_path('mpdf-temp'))) {
                mkdir(public_path('mpdf-temp'), 0755, true);
            }

            $cart->loadMissing(['user', 'course']);
            $user = $cart->user ?? User::find($cart->user_id);
            $course = $cart->course ?? Course::find($cart->course_id);
            $student = Student::where('user_id', $cart->user_id)->first();

            $TIME = time();
            $trialPart = $cart->trial_id ?: $cart->id;
            $relativePath = 'invoices/invoice_' . $trialPart . '_' . $TIME . '.pdf';
            $pdfFilePath = public_path($relativePath);

            $getCurrency = Currency::where('current_currency', 'on')->first();
            $fullData = [
                'year' => $cart->year ?: date('Y', strtotime($cart->created_at ?? now())),
                'id' => $cart->id,
                's_name' => $user->name ?? '',
                's_gender' => $student->gender ?? '',
                's_parent_name' => $student->parent_name ?? '',
                'payment_date' => $cart->created_at,
                'month' => $cart->month ?: date('F', strtotime($cart->created_at ?? now())),
                'course_name' => $course->title ?? '',
                'currency' => $getCurrency ? $getCurrency->symbol : 'Rs',
                'amount' => $cart->main_price,
                'course_price_1' => $cart->original_price,
                'discount' => $cart->discount,
                'discount_type' => $cart->discount_type,
                'net_course_price_1' => $cart->net_original_price,
                'admission_fee' => $cart->admission_fee ?? 0,
                'discount_admission_fee' => $cart->discount_admission_fee ?? 0,
                'discount_type_admission_fee' => $cart->discount_type_admission_fee ?? 0,
                'net_admission_fee' => $cart->net_admission_fee ?? 0,
                'students_count' => $cart->students_count ?? 1,
            ];

            $mpdf = new Mpdf([
                'tempDir' => public_path('mpdf-temp'),
                'default_font' => 'dejavusans',
            ]);
            $html = view('emails.invoice-challan', ['data' => $fullData])->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

            $cart->invoice_pdf_path = $relativePath;
            $cart->save();

            return $pdfFilePath;
        } catch (\Throwable $e) {
            Log::error('[MonthlyFeeInvoiceEmail] Failed to regenerate PDF for cart #' . $cart->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send invoice email for a cart. Returns true on success.
     *
     * @param array|null $recipientOverrides
     */
    public function send(
        CartManagement $cart,
        ?array $recipientOverrides = null,
        string $mode = 'generate',
        ?int $adminId = null
    ): bool {
        $payload = $this->buildPayload($cart, $recipientOverrides);
        $recipients = $payload['recipients'];

        if (empty($recipients['to']) && empty($recipients['cc']) && empty($recipients['bcc'])) {
            $this->logSend($cart->id, $adminId, $mode, $recipients, 'skipped', 'No recipients resolved');
            Log::warning('[MonthlyFeeInvoiceEmail] Cart #' . $cart->id . ' skipped: no recipients');
            return false;
        }

        // Laravel Mail requires a To; if only CC/BCC, put first CC or BCC as To
        $to = $recipients['to'];
        $cc = $recipients['cc'];
        $bcc = $recipients['bcc'];
        if (empty($to)) {
            if (!empty($cc)) {
                $to = [array_shift($cc)];
            } elseif (!empty($bcc)) {
                $to = [array_shift($bcc)];
            }
        }

        // commonEmailSend treats array $email as [to, ...legacyBcc] — always pass a single To string
        $primaryTo = $to[0];
        if (count($to) > 1) {
            $cc = array_values(array_unique(array_merge($cc, array_slice($to, 1))));
        }

        $ok = sendEmailNotification(
            $payload['viewData'],
            $primaryTo,
            self::TEMPLATE,
            $payload['attachments'],
            $cc,
            $bcc
        );

        $this->logSend(
            $cart->id,
            $adminId,
            $mode,
            ['to' => [$primaryTo], 'cc' => $cc, 'bcc' => $bcc],
            $ok ? 'sent' : 'failed',
            $ok ? null : 'sendEmailNotification returned false'
        );

        return (bool) $ok;
    }

    protected function logSend(
        ?int $cartId,
        ?int $adminId,
        string $mode,
        array $recipients,
        string $status,
        ?string $error = null
    ): void {
        try {
            MonthlyFeeEmailLog::create([
                'cart_id' => $cartId,
                'admin_id' => $adminId,
                'mode' => $mode,
                'recipients' => $recipients,
                'status' => $status,
                'error' => $error,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[MonthlyFeeInvoiceEmail] Failed to write email log: ' . $e->getMessage());
        }
    }

    /**
     * Build confirm token from sorted cart IDs + recipient options.
     */
    public function makeConfirmToken(array $cartIds, array $recipientOptions): string
    {
        $ids = array_map('intval', $cartIds);
        sort($ids);

        $payload = json_encode([
            'ids' => $ids,
            'recipients' => $this->normalizeRecipientOptions($recipientOptions),
        ], JSON_UNESCAPED_SLASHES);

        $signature = hash_hmac('sha256', $payload, (string) config('app.key'));

        return base64_encode(json_encode([
            'payload' => $payload,
            'sig' => $signature,
            'exp' => time() + self::CONFIRM_TOKEN_TTL_SECONDS,
        ]));
    }

    public function verifyConfirmToken(string $token, array $cartIds, array $recipientOptions): bool
    {
        try {
            $decoded = json_decode(base64_decode($token, true), true);
            if (!is_array($decoded) || empty($decoded['payload']) || empty($decoded['sig'])) {
                return false;
            }
            if (!empty($decoded['exp']) && (int) $decoded['exp'] < time()) {
                return false;
            }

            $expectedSig = hash_hmac('sha256', $decoded['payload'], (string) config('app.key'));
            if (!hash_equals($expectedSig, $decoded['sig'])) {
                return false;
            }

            $payload = json_decode($decoded['payload'], true);
            if (!is_array($payload)) {
                return false;
            }

            $ids = array_map('intval', $cartIds);
            sort($ids);
            $tokenIds = array_map('intval', $payload['ids'] ?? []);
            sort($tokenIds);

            if ($ids !== $tokenIds) {
                return false;
            }

            $normalized = $this->normalizeRecipientOptions($recipientOptions);
            $tokenRecipients = $this->normalizeRecipientOptions($payload['recipients'] ?? []);

            return $normalized === $tokenRecipients;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function normalizeRecipientOptions(array $options): array
    {
        $ccOverride = array_key_exists('cc_override', $options) ? (string) ($options['cc_override'] ?? '') : null;
        $bccOverride = array_key_exists('bcc_override', $options) ? (string) ($options['bcc_override'] ?? '') : null;
        if ($ccOverride !== null && trim($ccOverride) === '') {
            $ccOverride = null;
        }
        if ($bccOverride !== null && trim($bccOverride) === '') {
            $bccOverride = null;
        }

        return [
            'include_student' => array_key_exists('include_student', $options) && $options['include_student'] !== null
                ? (bool) $options['include_student']
                : null,
            'include_cc' => array_key_exists('include_cc', $options) && $options['include_cc'] !== null
                ? (bool) $options['include_cc']
                : null,
            'include_bcc' => array_key_exists('include_bcc', $options) && $options['include_bcc'] !== null
                ? (bool) $options['include_bcc']
                : null,
            'cc_override' => $ccOverride,
            'bcc_override' => $bccOverride,
        ];
    }

    /**
     * Dry-run preview rows for admin UI / API.
     *
     * @return array{
     *   count:int,
     *   cart_ids:array<int,int>,
     *   confirm_token:string,
     *   batch_estimate:int,
     *   recipients_summary:array,
     *   rows:array<int,array>
     * }
     */
    public function preview(array $filters, array $recipientOptions = [], ?int $limit = null): array
    {
        $query = $this->invoiceCartQuery($filters);
        if ($limit !== null) {
            $query->limit($limit);
        }

        /** @var Collection<int, CartManagement> $carts */
        $carts = $query->get();
        $cartIds = $carts->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

        $sampleStudentEmail = optional($carts->first()?->user)->email;
        $resolved = $this->recipientsFromSettings($recipientOptions, $sampleStudentEmail);

        $rows = [];
        foreach ($carts as $cart) {
            $user = $cart->user;
            $course = $cart->course;
            $perCartRecipients = $this->recipientsFromSettings($recipientOptions, $user->email ?? null);
            $rows[] = [
                'cart_id' => (int) $cart->id,
                'student_name' => $user->name ?? '',
                'student_email' => $user->email ?? '',
                'course_id' => (int) $cart->course_id,
                'course_title' => $course->title ?? '',
                'month' => $cart->month,
                'year' => $cart->year,
                'amount' => (float) $cart->main_price,
                'pdf_exists' => $this->pdfExists($cart),
                'paid' => $this->isCartPaid($cart),
                'to' => $perCartRecipients['to'],
                'cc' => $perCartRecipients['cc'],
                'bcc' => $perCartRecipients['bcc'],
            ];
        }

        $count = count($cartIds);
        $batchEstimate = $count > 0 ? (int) ceil($count / self::BATCH_SIZE) : 0;

        return [
            'count' => $count,
            'cart_ids' => $cartIds,
            'confirm_token' => $this->makeConfirmToken($cartIds, $recipientOptions),
            'batch_estimate' => $batchEstimate,
            'batch_size' => self::BATCH_SIZE,
            'batch_delay_minutes' => self::BATCH_DELAY_MINUTES,
            'recipients_summary' => [
                'sample_to' => $resolved['to'],
                'cc' => $resolved['cc'],
                'bcc' => $resolved['bcc'],
                'note' => 'One SMTP message per invoice uses To + CC + BCC together.',
            ],
            'rows' => $rows,
        ];
    }
}
