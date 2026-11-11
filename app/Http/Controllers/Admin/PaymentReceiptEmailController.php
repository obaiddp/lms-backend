<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ResendPaymentReceiptBatch;
use App\Models\Course;
use App\Services\PaymentReceiptEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentReceiptEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || (!$user->can('resend_payment_receipt') && !$user->can('manage_student'))) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(PaymentReceiptEmailService $emailService)
    {
        $data['title'] = __('Payment Receipt Emails');
        $data['navStudentParentActiveClass'] = 'mm-active';
        $data['subNavPaymentReceiptEmailActiveClass'] = 'mm-active';
        $data['allCourses'] = Course::where('course_type', 3)->where('status', 1)->orderBy('title')->get();
        $data['defaultMonth'] = (int) date('n');
        $data['defaultYear'] = (int) date('Y');
        $data['settingsCc'] = implode(', ', $emailService->defaultCcEmails());
        $data['settingsBcc'] = get_option('payment_receipt_bcc', '');
        $data['sendToStudentDefault'] = (string) get_option('payment_receipt_send_to_student', '1') === '1';
        $data['batchSize'] = PaymentReceiptEmailService::BATCH_SIZE;
        $data['batchDelay'] = PaymentReceiptEmailService::BATCH_DELAY_MINUTES;

        return view('admin.student.payment-receipt-emails', $data);
    }

    public function preview(Request $request, PaymentReceiptEmailService $emailService)
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

    public function resend(Request $request, PaymentReceiptEmailService $emailService)
    {
        $orderIds = array_values(array_unique(array_map('intval', (array) $request->input('order_ids', []))));
        $recipientOptions = $this->extractRecipientOptions($request);
        $confirmToken = (string) $request->input('confirm_token', '');

        if (empty($orderIds)) {
            return response()->json([
                'success' => false,
                'message' => __('No order IDs provided. Run Preview first.'),
            ], 422);
        }

        if ($confirmToken === '' || !$emailService->verifyConfirmToken($confirmToken, $orderIds, $recipientOptions)) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid or expired confirm token. Please run Preview again, then Queue Send.'),
            ], 422);
        }

        $hardMax = 500;
        if (count($orderIds) > $hardMax) {
            return response()->json([
                'success' => false,
                'message' => __('Too many receipts in one request (max :max). Narrow filters and preview again.', ['max' => $hardMax]),
            ], 422);
        }

        $adminId = auth()->id();
        $chunks = array_chunk($orderIds, PaymentReceiptEmailService::BATCH_SIZE);
        $dispatched = 0;

        foreach ($chunks as $index => $chunk) {
            $delayUntil = Carbon::now()->addMinutes($index * PaymentReceiptEmailService::BATCH_DELAY_MINUTES);
            ResendPaymentReceiptBatch::dispatch($chunk, $recipientOptions, $adminId)
                ->delay($delayUntil)
                ->onQueue('default');
            $dispatched++;

            Log::info(sprintf(
                '[PaymentReceiptResend] Dispatched batch #%d (%d orders) at %s by admin #%s',
                $index + 1,
                count($chunk),
                $delayUntil->toDateTimeString(),
                $adminId
            ));
        }

        return response()->json([
            'success' => true,
            'message' => __('Queued :count payment receipt email(s) in :batches batch(es).', [
                'count' => count($orderIds),
                'batches' => $dispatched,
            ]),
            'data' => [
                'order_count' => count($orderIds),
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
            'payment_method' => $request->input('payment_method'),
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
        ];
    }
}
