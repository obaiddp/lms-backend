<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ResendMonthlyFeeInvoiceBatch;
use App\Models\Course;
use App\Services\MonthlyFeeInvoiceEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonthlyFeeInvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || (!$user->can('resend_monthly_fee_invoice') && !$user->can('manage_student'))) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(MonthlyFeeInvoiceEmailService $emailService)
    {
        $data['title'] = __('Monthly Fee Invoice Emails');
        $data['navStudentParentActiveClass'] = 'mm-active';
        $data['subNavMonthlyFeeInvoiceActiveClass'] = 'mm-active';
        $data['allCourses'] = Course::where('course_type', 3)->where('status', 1)->orderBy('title')->get();
        $data['defaultMonth'] = (int) date('n');
        $data['defaultYear'] = (int) date('Y');
        $data['settingsCc'] = get_option('monthly_fee_invoice_cc', '');
        $data['settingsBcc'] = get_option('monthly_fee_invoice_bcc', '');
        $data['sendToStudentDefault'] = (string) get_option('monthly_fee_invoice_send_to_student', '1') === '1';
        $data['batchSize'] = MonthlyFeeInvoiceEmailService::BATCH_SIZE;
        $data['batchDelay'] = MonthlyFeeInvoiceEmailService::BATCH_DELAY_MINUTES;

        return view('admin.student.monthly-fee-invoices', $data);
    }

    public function preview(Request $request, MonthlyFeeInvoiceEmailService $emailService)
    {
        $filters = $this->extractFilters($request);
        $recipientOptions = $this->extractRecipientOptions($request);

        if (empty($filters['month']) || empty($filters['year'])) {
            return response()->json([
                'success' => false,
                'message' => __('Month and year are required for preview.'),
            ], 422);
        }

        $preview = $emailService->preview($filters, $recipientOptions);

        return response()->json([
            'success' => true,
            'message' => __('Dry-run preview ready. No emails were sent.'),
            'data' => $preview,
        ]);
    }

    public function resend(Request $request, MonthlyFeeInvoiceEmailService $emailService)
    {
        $cartIds = array_values(array_unique(array_map('intval', (array) $request->input('cart_ids', []))));
        $recipientOptions = $this->extractRecipientOptions($request);
        $confirmToken = (string) $request->input('confirm_token', '');

        if (empty($cartIds)) {
            return response()->json([
                'success' => false,
                'message' => __('No invoice cart IDs provided. Run Preview first.'),
            ], 422);
        }

        if ($confirmToken === '' || !$emailService->verifyConfirmToken($confirmToken, $cartIds, $recipientOptions)) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid or expired confirm token. Please run Preview again, then Queue Send.'),
            ], 422);
        }

        $hardMax = 500;
        if (count($cartIds) > $hardMax) {
            return response()->json([
                'success' => false,
                'message' => __('Too many invoices in one request (max :max). Narrow filters and preview again.', ['max' => $hardMax]),
            ], 422);
        }

        $adminId = auth()->id();
        $chunks = array_chunk($cartIds, MonthlyFeeInvoiceEmailService::BATCH_SIZE);
        $dispatched = 0;

        foreach ($chunks as $index => $chunk) {
            $delayUntil = Carbon::now()->addMinutes($index * MonthlyFeeInvoiceEmailService::BATCH_DELAY_MINUTES);
            ResendMonthlyFeeInvoiceBatch::dispatch($chunk, $recipientOptions, $adminId)
                ->delay($delayUntil)
                ->onQueue('default');
            $dispatched++;

            Log::info(sprintf(
                '[MonthlyFeeResend] Dispatched batch #%d (%d carts) at %s by admin #%s',
                $index + 1,
                count($chunk),
                $delayUntil->toDateTimeString(),
                $adminId
            ));
        }

        return response()->json([
            'success' => true,
            'message' => __('Queued :count invoice email(s) in :batches batch(es).', [
                'count' => count($cartIds),
                'batches' => $dispatched,
            ]),
            'data' => [
                'cart_count' => count($cartIds),
                'batches' => $dispatched,
            ],
        ]);
    }

    protected function extractFilters(Request $request): array
    {
        return [
            'month' => $request->filled('month') ? (int) $request->input('month') : null,
            'year' => $request->filled('year') ? (int) $request->input('year') : null,
            'course_id' => $request->filled('course_id') ? (int) $request->input('course_id') : null,
            'user_id' => $request->filled('user_id') ? (int) $request->input('user_id') : null,
            'student_search' => $request->input('student_search'),
            'student_email' => $request->input('student_email'),
            'payment_status' => $request->input('payment_status'),
            'has_pdf' => $request->input('has_pdf'),
            'include_zero_price' => $request->boolean('include_zero_price'),
        ];
    }

    protected function extractRecipientOptions(Request $request): array
    {
        $includeStudent = $request->has('include_student')
            ? $request->boolean('include_student')
            : null;
        $includeCc = $request->has('include_cc')
            ? $request->boolean('include_cc')
            : null;
        $includeBcc = $request->has('include_bcc')
            ? $request->boolean('include_bcc')
            : null;

        $ccOverride = $request->has('cc_override') ? (string) $request->input('cc_override') : null;
        $bccOverride = $request->has('bcc_override') ? (string) $request->input('bcc_override') : null;
        if ($ccOverride !== null && trim($ccOverride) === '') {
            $ccOverride = null;
        }
        if ($bccOverride !== null && trim($bccOverride) === '') {
            $bccOverride = null;
        }

        return [
            'include_student' => $includeStudent,
            'include_cc' => $includeCc,
            'include_bcc' => $includeBcc,
            'cc_override' => $ccOverride,
            'bcc_override' => $bccOverride,
            'include_zero_price' => $request->boolean('include_zero_price'),
        ];
    }
}
