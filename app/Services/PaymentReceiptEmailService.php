<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_item;
use App\Models\PaymentReceiptEmailLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentReceiptEmailService
{
    public const BATCH_SIZE = 5;
    public const BATCH_DELAY_MINUTES = 15;
    public const CONFIRM_TOKEN_TTL_SECONDS = 3600;
    public const SUBJECT = 'Payment Received | CEF Online';
    public const VIEW = 'frontend.student.emails.completed_payment';

    /**
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
     * Default CC from live payment path: admin_email + financial_email + other_emails.
     *
     * @return array<int, string>
     */
    public function defaultCcEmails(): array
    {
        $adminEmail = get_option('admin_email');
        $financialEmail = get_option('financial_email');
        $otherEmails = json_decode(get_option('other_emails'), true) ?? [];
        if (!is_array($otherEmails)) {
            $otherEmails = [];
        }

        $merged = array_merge([$adminEmail, $financialEmail], $otherEmails);
        $valid = [];
        foreach ($merged as $email) {
            $email = is_string($email) ? strtolower(trim($email)) : '';
            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $valid[] = $email;
            }
        }

        return array_values(array_unique($valid));
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
            : (string) get_option('payment_receipt_send_to_student', '1') === '1';

        $includeCc = array_key_exists('include_cc', $overrides) && $overrides['include_cc'] !== null
            ? (bool) $overrides['include_cc']
            : true;

        $includeBcc = array_key_exists('include_bcc', $overrides) && $overrides['include_bcc'] !== null
            ? (bool) $overrides['include_bcc']
            : true;

        $student = $studentEmail ?? ($overrides['student_email'] ?? null);

        $to = [];
        if ($includeStudent && $student && filter_var($student, FILTER_VALIDATE_EMAIL)) {
            $to[] = strtolower(trim($student));
        }

        $ccRaw = $overrides['cc_override'] ?? null;
        $bccRaw = $overrides['bcc_override'] ?? null;

        if (is_string($ccRaw) && trim($ccRaw) !== '') {
            $cc = $includeCc ? $this->parseEmailList($ccRaw) : [];
        } else {
            $cc = $includeCc ? $this->defaultCcEmails() : [];
        }

        $bccSource = (is_string($bccRaw) && trim($bccRaw) !== '')
            ? $bccRaw
            : (string) get_option('payment_receipt_bcc', '');
        $bcc = $includeBcc ? $this->parseEmailList($bccSource) : [];

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

    public function paidOrderQuery(array $filters = []): Builder
    {
        $query = Order::query()
            ->with(['user', 'items.course', 'items.product', 'items.cart'])
            ->whereRaw('LOWER(TRIM(payment_status)) = ?', ['paid'])
            ->whereHas('items');

        if (!empty($filters['month']) && !empty($filters['year'])) {
            $month = (int) $filters['month'];
            $year = (int) $filters['year'];
            $query->where(function ($q) use ($month, $year) {
                $q->whereHas('items', function ($iq) use ($month, $year) {
                    $iq->whereRaw('CAST(`month` AS UNSIGNED) = ?', [$month])
                        ->whereRaw('CAST(`year` AS UNSIGNED) = ?', [$year]);
                })->orWhere(function ($oq) use ($month, $year) {
                    $oq->whereRaw('CAST(COALESCE(`month`, 0) AS UNSIGNED) = ?', [$month])
                        ->whereRaw('CAST(COALESCE(`year`, 0) AS UNSIGNED) = ?', [$year]);
                })->orWhere(function ($fq) use ($month, $year) {
                    // Fallback: created_at month/year when item/order month/year empty
                    $fq->whereDoesntHave('items', function ($iq) {
                        $iq->whereNotNull('month')->where('month', '!=', '')
                            ->whereNotNull('year')->where('year', '!=', '');
                    })
                    ->where(function ($inner) {
                        $inner->whereNull('month')->orWhere('month', '')->orWhereRaw('CAST(`month` AS UNSIGNED) = 0');
                    })
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
                });
            });
        } elseif (!empty($filters['month'])) {
            $month = (int) $filters['month'];
            $query->where(function ($q) use ($month) {
                $q->whereHas('items', fn ($iq) => $iq->whereRaw('CAST(`month` AS UNSIGNED) = ?', [$month]))
                    ->orWhereRaw('CAST(COALESCE(`month`, 0) AS UNSIGNED) = ?', [$month])
                    ->orWhereMonth('created_at', $month);
            });
        } elseif (!empty($filters['year'])) {
            $year = (int) $filters['year'];
            $query->where(function ($q) use ($year) {
                $q->whereHas('items', fn ($iq) => $iq->whereRaw('CAST(`year` AS UNSIGNED) = ?', [$year]))
                    ->orWhereRaw('CAST(COALESCE(`year`, 0) AS UNSIGNED) = ?', [$year])
                    ->orWhereYear('created_at', $year);
            });
        }

        if (!empty($filters['course_id'])) {
            $query->whereHas('items', function ($iq) use ($filters) {
                $iq->where('course_id', (int) $filters['course_id']);
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }

        if (!empty($filters['student_search'])) {
            $search = trim((string) $filters['student_search']);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        return $query->orderByDesc('id');
    }

    /**
     * Build Blade view data matching legacy sendPaymentEmail.
     *
     * @return array{mainArray: array, student_email: ?string}|null
     */
    public function buildPayload(Order $order, ?string $paymentOrderId = null): ?array
    {
        $order->loadMissing(['user', 'items.course', 'items.product', 'items.cart']);

        $getItems = $order->items;
        if ($getItems->isEmpty()) {
            return null;
        }

        /** @var Order_item $getPaymentData */
        $getPaymentData = $getItems->first();
        $itemsArray = [];

        foreach ($getItems as $item) {
            if ($item->course) {
                $name = $item->course->title;
            } elseif ($item->product) {
                $name = $item->product->title;
            } else {
                $name = 'Item';
            }

            if ($item->month && $item->year) {
                $month = $item->month;
                $updatedMonth = date('F', strtotime('2024-' . $month . '-01'));
                $year = $item->year;
            } elseif ($item->cart) {
                $month = $item->cart->month;
                $updatedMonth = date('F', strtotime('2024-' . $month . '-01'));
                $year = $item->cart->year;
            } else {
                $month = date('n', strtotime($item->created_at));
                $updatedMonth = date('F', strtotime($item->created_at));
                $year = date('Y', strtotime($item->created_at));
            }

            $itemsArray[] = [
                'name' => $name,
                'monthYear' => $updatedMonth . '/' . $year,
                'amount' => 'RS ' . (int) $item->unit_price,
            ];
        }

        $data2 = [];
        if ($order->payment_method == 'meezan_bank') {
            $data2['order_payment_date'] = $order->meezan_payment_date ?: date('Y-m-d h:i:s');
            $data2['payment_source'] = $order->meezan_payment_source;
            $data2['payment_method'] = $order->payment_method;
        } elseif ($order->payment_method == 'faysal_bank') {
            $data2['order_payment_date'] = $order->faysal_payment_date ?: date('Y-m-d h:i:s');
            $data2['payment_source'] = $order->faysal_payment_source;
            $data2['payment_method'] = $order->payment_method;
        } else {
            $data2['order_payment_date'] = date('Y-m-d h:i:s');
            $data2['payment_source'] = 'Card';
            $data2['payment_method'] = $order->payment_method;
        }

        if ($paymentOrderId !== null && $paymentOrderId !== '') {
            $id = $paymentOrderId;
        } elseif (!empty($order->meezan_order_id)) {
            $id = $order->meezan_order_id;
        } elseif (!empty($order->faysal_order_id)) {
            $id = $order->faysal_order_id;
        } else {
            $id = 'LMS-P-' . $order->id;
        }

        $data2['result'] = $getPaymentData;
        $data2['receipt_number_new'] = rand(0, 9999999);
        $data2['app_email'] = get_option('app_email') ?: env('MAIL_FROM_ADDRESS');
        $data2['app_contact_number'] = get_option('app_contact_number') ?: env('APP_CONTACT_NUMBER');
        $data2['facebook_url'] = (get_option('facebook_url') && get_option('facebook_url') != '#')
            ? get_option('facebook_url')
            : env('FACEBOOK_URL');
        $data2['twitter_url'] = (get_option('twitter_url') && get_option('twitter_url') != '#')
            ? get_option('twitter_url')
            : env('TWITTER_URL');
        $data2['instagram_url'] = (get_option('instagram_url') && get_option('instagram_url') != '#')
            ? get_option('instagram_url')
            : env('INSTAGRAM_URL');
        $data2['youtube_url'] = (get_option('youtube_url') && get_option('youtube_url') != '#')
            ? get_option('youtube_url')
            : env('YOUTUBE_URL');
        $data2['app_location'] = get_option('app_location') ?: env('APP_LOCATION');

        $studentEmail = $order->user->email
            ?? ($getPaymentData->user->email ?? null)
            ?? optional(User::find($order->user_id))->email;

        return [
            'mainArray' => [
                'data2' => $data2,
                'Items' => $itemsArray,
                'ID' => $id,
            ],
            'student_email' => $studentEmail,
        ];
    }

    public function send(
        Order $order,
        ?array $recipientOverrides = null,
        string $mode = 'generate',
        ?int $adminId = null,
        ?string $paymentOrderId = null
    ): bool {
        $payload = $this->buildPayload($order, $paymentOrderId);
        if ($payload === null) {
            $this->logSend($order->id, $adminId, $mode, [], 'skipped', 'Order has no items');
            return false;
        }

        $recipients = $this->recipientsFromSettings($recipientOverrides, $payload['student_email']);

        if (empty($recipients['to']) && empty($recipients['cc']) && empty($recipients['bcc'])) {
            $this->logSend($order->id, $adminId, $mode, $recipients, 'skipped', 'No recipients resolved');
            Log::warning('[PaymentReceiptEmail] Order #' . $order->id . ' skipped: no recipients');
            return false;
        }

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

        $primaryTo = $to[0];
        if (count($to) > 1) {
            $cc = array_values(array_unique(array_merge($cc, array_slice($to, 1))));
        }

        try {
            $mainArray = $payload['mainArray'];
            Mail::send(self::VIEW, $mainArray, function ($message) use ($primaryTo, $cc, $bcc) {
                $message->to($primaryTo)->subject(self::SUBJECT);
                if (!empty($cc)) {
                    $message->cc($cc);
                }
                if (!empty($bcc)) {
                    $message->bcc($bcc);
                }
            });

            $this->logSend(
                $order->id,
                $adminId,
                $mode,
                ['to' => [$primaryTo], 'cc' => $cc, 'bcc' => $bcc],
                'sent'
            );

            return true;
        } catch (\Throwable $e) {
            Log::error('[PaymentReceiptEmail] Order #' . $order->id . ' send failed: ' . $e->getMessage());
            $this->logSend(
                $order->id,
                $adminId,
                $mode,
                ['to' => [$primaryTo], 'cc' => $cc, 'bcc' => $bcc],
                'failed',
                $e->getMessage()
            );

            return false;
        }
    }

    /**
     * Live-payment entry: order id + optional bank order id (meezan/faysal).
     */
    public function sendByOrderId($paymentId, $paymentOrderId = null, string $mode = 'generate', ?int $adminId = null): bool
    {
        $order = Order::with(['user', 'items.course', 'items.product', 'items.cart'])->find($paymentId);
        if (!$order) {
            Log::warning('[PaymentReceiptEmail] Order #' . $paymentId . ' not found');
            return false;
        }

        return $this->send($order, null, $mode, $adminId, $paymentOrderId);
    }

    protected function logSend(
        ?int $orderId,
        ?int $adminId,
        string $mode,
        array $recipients,
        string $status,
        ?string $error = null
    ): void {
        try {
            PaymentReceiptEmailLog::create([
                'order_id' => $orderId,
                'admin_id' => $adminId,
                'mode' => $mode,
                'recipients' => $recipients,
                'status' => $status,
                'error' => $error,
            ]);
        } catch (\Throwable $e) {
            Log::warning('[PaymentReceiptEmail] Failed to write email log: ' . $e->getMessage());
        }
    }

    public function makeConfirmToken(array $orderIds, array $recipientOptions): string
    {
        $ids = array_map('intval', $orderIds);
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

    public function verifyConfirmToken(string $token, array $orderIds, array $recipientOptions): bool
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

            $ids = array_map('intval', $orderIds);
            sort($ids);
            $tokenIds = array_map('intval', $payload['ids'] ?? []);
            sort($tokenIds);

            if ($ids !== $tokenIds) {
                return false;
            }

            return $this->normalizeRecipientOptions($recipientOptions)
                === $this->normalizeRecipientOptions($payload['recipients'] ?? []);
        } catch (\Throwable $e) {
            return false;
        }
    }

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
     * @return array{
     *   count:int,
     *   order_ids:array<int,int>,
     *   confirm_token:string,
     *   batch_estimate:int,
     *   recipients_summary:array,
     *   rows:array<int,array>
     * }
     */
    public function preview(array $filters, array $recipientOptions = [], ?int $limit = null): array
    {
        $query = $this->paidOrderQuery($filters);
        if ($limit !== null) {
            $query->limit($limit);
        }

        /** @var Collection<int, Order> $orders */
        $orders = $query->get();
        $orderIds = $orders->pluck('id')->map(fn ($id) => (int) $id)->values()->all();

        $sampleEmail = optional($orders->first()?->user)->email;
        $resolved = $this->recipientsFromSettings($recipientOptions, $sampleEmail);

        $rows = [];
        foreach ($orders as $order) {
            $user = $order->user;
            $firstItem = $order->items->first();
            $courseTitle = $firstItem?->course?->title
                ?? $firstItem?->product?->title
                ?? 'Item';
            $month = $firstItem->month ?? $order->month ?? date('n', strtotime($order->created_at));
            $year = $firstItem->year ?? $order->year ?? date('Y', strtotime($order->created_at));
            $perRecipients = $this->recipientsFromSettings($recipientOptions, $user->email ?? null);

            $rows[] = [
                'order_id' => (int) $order->id,
                'student_name' => $user->name ?? '',
                'student_email' => $user->email ?? '',
                'course_title' => $courseTitle,
                'month' => $month,
                'year' => $year,
                'amount' => (float) ($order->grand_total ?? 0),
                'payment_method' => $order->payment_method,
                'to' => $perRecipients['to'],
                'cc' => $perRecipients['cc'],
                'bcc' => $perRecipients['bcc'],
            ];
        }

        $count = count($orderIds);

        return [
            'count' => $count,
            'order_ids' => $orderIds,
            'confirm_token' => $this->makeConfirmToken($orderIds, $recipientOptions),
            'batch_estimate' => $count > 0 ? (int) ceil($count / self::BATCH_SIZE) : 0,
            'batch_size' => self::BATCH_SIZE,
            'batch_delay_minutes' => self::BATCH_DELAY_MINUTES,
            'recipients_summary' => [
                'sample_to' => $resolved['to'],
                'cc' => $resolved['cc'],
                'bcc' => $resolved['bcc'],
                'note' => 'One SMTP message per receipt uses To + CC + BCC together. Default CC = admin_email + financial_email + other_emails.',
            ],
            'rows' => $rows,
        ];
    }
}
