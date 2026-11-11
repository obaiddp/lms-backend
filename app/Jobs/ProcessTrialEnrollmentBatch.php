<?php

namespace App\Jobs;

use App\Models\TrialClass;
use App\Models\Student;
use App\Services\MonthlyFeeBillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Currency;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;
use App\Models\CartManagement;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Course;
use Carbon\Carbon;
use Exception;

class ProcessTrialEnrollmentBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1200;
    public $tries = 3;

    protected $paymentIds;

    public function __construct(array $ids)
    {
        $this->paymentIds = $ids;
    }

    public function handle(MonthlyFeeBillingService $billingService)
    {
        Log::info('[ScheduleJob] ProcessTrialEnrollmentBatch started for ' . count($this->paymentIds) . ' items at ' . now());
        $now = Carbon::now();

        foreach ($this->paymentIds as $paymentId) {
            $payment = Enrollment::with(['course', 'user'])->find($paymentId);

            if (!$payment) {
                Log::warning("[ScheduleJob] Trial Request Enrollment #{$paymentId} not found. Skipping.");
                continue;
            }

            $evaluation = $billingService->evaluate($payment, $now);

            if (!$evaluation->shouldCreate()) {
                Log::info(sprintf(
                    '[ScheduleJob] Enrollment #%d skipped: %s (user=%d course=%d period=%s)',
                    $payment->id,
                    $evaluation->reason,
                    $payment->user_id,
                    $payment->course_id,
                    $evaluation->billingPeriodLabel()
                ));
                continue;
            }

            DB::beginTransaction();
            try {
                $trial = $payment->trial_id ? TrialClass::find($payment->trial_id) : null;
                $course = $payment->course ?? Course::find($payment->course_id);
                $userId = $payment->user_id;
                $student = Student::where('user_id', $userId)->first();

                if (!$course || !$student) {
                    Log::warning("[ScheduleJob] Enrollment #{$payment->id} missing course or student — skipped.");
                    DB::rollBack();
                    continue;
                }

                $reevaluation = $billingService->evaluate($payment, $now);
                if (!$reevaluation->shouldCreate()) {
                    Log::info("[ScheduleJob] Enrollment #{$payment->id} skipped on re-check: {$reevaluation->reason}");
                    DB::rollBack();
                    continue;
                }

                $originalPrice = $payment->original_price ?? 0;
                $discount = $payment->discount ?? 0;
                $discountType = $payment->discount_type ?? 0;
                $netCoursePrice = $billingService->calculateNetPrice($payment);

                $requestData = [
                    'course_price' => $originalPrice,
                    'discount_course_price' => $discount,
                    'discount_type_course_price' => $discountType,
                    'net_course_price' => $netCoursePrice,
                    'admission_fee' => 0.00,
                    'discount_admission_fee' => 0.00,
                    'discount_type_admission_fee' => 0.00,
                    'net_admission_fee' => 0.00,
                    'net_price' => $netCoursePrice,
                ];

                if ($trial) {
                    $preferDate = Carbon::parse($trial->prefer_date)->startOfDay();
                    $preferTime = $trial->prefer_time;
                } else {
                    $preferDate = Carbon::today()->addDay()->startOfDay();
                    $preferTime = '09:00 AM';
                }

                $today = Carbon::today();
                while (
                    $preferDate->lessThanOrEqualTo($today) ||
                    TrialClass::where('prefer_date', $preferDate->toDateString())
                        ->where('prefer_time', $preferTime)
                        ->exists()
                ) {
                    $preferDate->addDay();
                }

                $newTrial = new TrialClass();
                $newTrial->student_id = $student->id;
                $newTrial->prefer_date = $preferDate->toDateString();
                $newTrial->prefer_time = $preferTime;
                $newTrial->course_id = $trial ? $trial->course_id : $course->id;
                $newTrial->save();

                $cartExists = CartManagement::whereUserId($userId)
                    ->whereCourseId($course->id)
                    ->where('request_enrollment', 1)
                    ->where('trial_id', $newTrial->id)
                    ->first();

                if (!$cartExists) {
                    $result = live_course_checkout_cron($course, $student, $newTrial->id, $requestData);
                    $checkoutLink = isset($result['is_true']) ? route('student.checkout', ['type' => 1, 'location' => 1]) : null;

                    if (!$checkoutLink) {
                        Log::warning("[ScheduleJob] live_course_checkout for Enrollment #{$payment->id} did not return checkout link. Skipped.");
                        DB::rollBack();
                        continue;
                    }

                    $getUser = User::find($student->user_id);
                    if (!$getUser) {
                        Log::warning("[ScheduleJob] User not found for student (enrollment #{$payment->id}). Skipped.");
                        DB::rollBack();
                        continue;
                    }

                    if (!file_exists(public_path('invoices'))) {
                        mkdir(public_path('invoices'), 0755, true);
                    }
                    if (!file_exists(public_path('mpdf-temp'))) {
                        mkdir(public_path('mpdf-temp'), 0755, true);
                    }

                    $TIME = time();
                    $pdfFilePath = public_path('invoices/invoice_' . $newTrial->id . '_' . $TIME . '.pdf');

                    $mpdf = new Mpdf([
                        'tempDir' => public_path('mpdf-temp'),
                        'default_font' => 'dejavusans',
                    ]);

                    $fullData = [];
                    $cart = CartManagement::where('trial_id', $newTrial->id)->first();
                    $fullData['year'] = $cart->year ?? date('Y', strtotime($cart->created_at ?? now()));
                    $fullData['id'] = $cart->id ?? null;
                    $fullData['s_name'] = $getUser->name;
                    $fullData['s_gender'] = $student->gender;
                    $fullData['s_parent_name'] = $student->parent_name;
                    $fullData['payment_date'] = $cart->created_at ?? now();
                    $fullData['month'] = $cart->month ?? date('F', strtotime($cart->created_at ?? now()));
                    $fullData['course_name'] = $course->title;
                    $getCurrency = Currency::where('current_currency', 'on')->first();
                    $fullData['currency'] = $getCurrency ? $getCurrency->symbol : 'Rs';
                    $fullData['amount'] = $netCoursePrice;
                    $fullData['course_price_1'] = $originalPrice;
                    $fullData['discount'] = $discount;
                    $fullData['discount_type'] = $discountType;
                    $fullData['net_course_price_1'] = $netCoursePrice;
                    $fullData['admission_fee'] = 0.00;
                    $fullData['discount_admission_fee'] = 0.00;
                    $fullData['discount_type_admission_fee'] = 0.00;
                    $fullData['net_admission_fee'] = 0.00;

                    $html = view('emails.invoice-challan', ["data" => $fullData])->render();
                    $mpdf->WriteHTML($html);
                    $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

                    $relativePath = 'invoices/invoice_' . $newTrial->id . '_' . $TIME . '.pdf';
                    $newTrial->invoice_pdf_path = $relativePath;
                    $newTrial->approved = 1;
                    $newTrial->save();

                    $cart = CartManagement::where('trial_id', $newTrial->id)->first();
                    if ($cart) {
                        $cart->invoice_pdf_path = $relativePath;
                        $cart->save();
                    }

                    if ($cart) {
                        app(\App\Services\MonthlyFeeInvoiceEmailService::class)
                            ->send($cart, null, 'generate', null);
                    }

                    $payment->invoice_generated_at = Carbon::now();
                    $payment->save();

                    DB::commit();

                    Log::info("[ScheduleJob] Enrollment #{$payment->id} processed successfully. Invoice saved: {$relativePath}");
                } else {
                    Log::warning("[ScheduleJob] Enrollment #{$payment->id} next cart entry already created — skipped.");
                    DB::rollBack();
                }
            } catch (\Throwable $ex) {
                DB::rollBack();
                Log::error("[ScheduleJob] Error processing enrollment #{$payment->id}: " . $ex->getMessage());
            }
        }

        Log::info('[ScheduleJob] ProcessTrialEnrollmentBatch completed at ' . now());
    }

    public function failed(Exception $exception)
    {
        Log::error('[ScheduleJob] ProcessTrialEnrollmentBatch failed: ' . $exception->getMessage());
    }
}
