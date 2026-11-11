<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\EmailNotify;
use App\Models\Addon\AI\OpenAIPrompt;
use App\Models\AffiliateHistory;
use App\Models\BookingHistory;
use App\Models\Bundle;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

use App\Models\CartManagement;
use App\Models\Course;
use App\Models\Order;
use App\Models\Course_lecture;
use App\Models\CourseInstructor;
use App\Models\Currency;
use App\Models\TrialClass;
use App\Models\Enrollment;
use App\Models\LiveClass;
use App\Models\LiveClassStudentLink;
use App\Models\ForumPostComment;
use App\Models\Instructor;
use App\Models\Coordinator;
use App\Models\InstructorConsultationDayStatus;
use App\Models\Language;
use App\Models\Meta;
use App\Models\Exam;
use App\Models\Take_exam;
use App\Models\Answer;
use App\Models\Question;
use App\Models\NotificationEmailTemplate;
use App\Models\Order_item;
use App\Models\RankingLevel;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Lesson;
use App\Models\ClassDetail;
use App\Models\UserBadge;
use App\Models\Withdraw;
use App\Models\ZoomSetting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\UserPackage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use App\Models\Student;
// use DateTimeZone;
// use DateTime;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Libraries\Bbb;
// use IntlTimeZone;


class ScheduleJobController extends Controller
{



  

    public function __construct()
    {
        $this->bb=new Bbb();
    }


//      /**
//      * Process trial-based enrollments monthly.
//      * This method should be scheduled to run at every 1st of month 00:00.
//      */
//     public function processTrialEnrollments()
//     {
//         Log::info('[ScheduleJob] processTrialEnrollments started at '.now());

//         // First day of current month (00:00)
//         $firstOfMonth = Carbon::now()->startOfMonth()->startOfDay();
      
//         // get enrollments which are trial-based and active (status = 1)
//         $enrollments = Enrollment::whereNotNull('trial_id')
//             ->where('status', 1)
//             ->orderBy('id','ASC')
//             ->get();

           
//         Log::info('[ScheduleJob] Found '. $enrollments->count() .' trial enrollments to evaluate.');

//         foreach ($enrollments as $enrollment) {
         
//             // Determine enrollment start date (fall back to created_at)
//             $start = $enrollment->start_date ?? $enrollment->created_at;
            
//             if (!$start) {
//                 Log::warning("[ScheduleJob] Enrollment #{$enrollment->id} has no start_date/created_at — skipped.");
//                 continue;
//             }

//             $startDate = Carbon::parse($start)->startOfDay();

               

//             // skip if enrollment start is in future relative to firstOfMonth
//             if ($startDate->greaterThan($firstOfMonth)) {
//                 Log::info("[ScheduleJob] Enrollment #{$enrollment->id} start date is after {$firstOfMonth->toDateString()} — skipped.");
//                 continue;
//             }

//             $diffDays = $firstOfMonth->diffInDays($startDate);

           

//             if ($diffDays < 15) {
//                 Log::info("[ScheduleJob] Enrollment #{$enrollment->id} diffDays={$diffDays} (<15) — skipped.");
//                 continue;
//             }
               
//             Log::info("[ScheduleJob] Enrollment #{$enrollment->id} eligible (diffDays={$diffDays}). Processing...");

//             // Wrap processing for each enrollment in its own transaction
//             DB::beginTransaction();
//             try {
                
//                 // Fetch related models
//                 $trial = TrialClass::find($enrollment->trial_id);
             
//                 $course = Course::find($enrollment->course_id);
//                 $userId = $enrollment->user_id;
//                 $student = Student::where('user_id', $userId)->first();

//                 if (!$trial || !$course || !$student) {
//                     Log::warning("[ScheduleJob] Enrollment #{$enrollment->id} missing related models (trial/course/student). Skipped.");
//                     DB::rollBack();
//                     continue;
//                 }
               
//                 // Prepare requestData (admission_fee = 0 for cron run)
//                 $originalPrice = $enrollment->original_price ?? 0;
//                 $discount = $enrollment->discount ?? 0;
//                 $discountType = $enrollment->discount_type ?? 0; // 0 = Amount, 1 = Percentage
//                 // Calculate net course price based on discount type
//                 if ($discountType == 1) {
//                     // Percentage discount
//                     $netCoursePrice = $originalPrice - (($discount / 100) * $originalPrice);
//                 } else {
//                     // Fixed amount discount
//                     $netCoursePrice = $originalPrice - $discount;
//                 }
                
//                 // Ensure it doesn’t go negative
//                 if ($netCoursePrice < 0) {
//                     $netCoursePrice = 0;
//                 }
             
//                 $requestData = [
//                     'course_price' => $originalPrice,
//                     'discount_course_price' => $discount,
//                     'discount_type_course_price' => $discountType,
//                     'net_course_price' => $netCoursePrice,
//                     'admission_fee' => 0.00,
//                     'discount_admission_fee' => 0.00,
//                     'discount_type_admission_fee' => 0.00,
//                     'net_admission_fee' => 0.00,
//                     'net_price' => $netCoursePrice,
//                 ];

                 
             


//                 // Call existing function to create cart etc.
//                 // NOTE: your live_course_checkout in the app accepts 4 args in your code earlier.
//                 // $result = live_course_checkout($course, $student, $trial->id, $requestData);
//                 $checkEnrollment = Enrollment::where(['course_id' => $course->id , 'user_id' => $userId, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
//                 if($checkEnrollment) {
//                     $order = Order::find($checkEnrollment->order_id);
//                     if ($order) {
//                         if ($order->payment_status == 'paid') {
                             
//                             // ✅ Ensure unique prefer_date + prefer_time combination
//                             $preferDate = Carbon::parse($trial->prefer_date)->startOfDay();
//                             $preferTime = $trial->prefer_time;

//                             // Get today's date (start of day for safe comparison)
//                             $today = Carbon::today();

//                             // Loop until we find a unique AND future date/time slot
//                             while (
//                                 $preferDate->lessThanOrEqualTo($today) || // ensure date > today
//                                 TrialClass::where('prefer_date', $preferDate->toDateString())
//                                           ->where('prefer_time', $preferTime)
//                                           ->exists()
//                             ) {
//                                 $preferDate->addDay(); // Increment date until unique + future
//                             }


//                             $newTrial = new TrialClass();
//                             $newTrial->student_id = $student->id;
//                             $newTrial->prefer_date = $preferDate->toDateString();
//                             $newTrial->prefer_time = $preferTime;
//                             $newTrial->course_id = $trial->course_id;
//                             $newTrial->save();
                          
//                             $cartExists = CartManagement::whereUserId($userId)->whereCourseId($course->id)
//                                             ->where('request_enrollment',1)
//                                             ->where('trial_id',$newTrial->id)->first();
                           
//                             if (!$cartExists) {
//                                 $result = live_course_checkout_cron($course, $student, $newTrial->id, $requestData);
//                                 $checkoutLink = isset($result['is_true']) ? route('student.checkout', ['type' => 1, 'location' => 1]) : null;

//                                 if (!$checkoutLink) {
//                                     Log::warning("[ScheduleJob] live_course_checkout for Enrollment #{$enrollment->id} did not return checkout link. Skipped.");
//                                     DB::rollBack();
//                                     continue;
//                                 }

//                                   // Find user to send email
//                                 $getUser = User::find($student->user_id);
//                                 if (!$getUser) {
//                                     Log::warning("[ScheduleJob] User not found for student (enrollment #{$enrollment->id}). Skipped.");
//                                     DB::rollBack();
//                                     continue;
//                                 }

//                                 // Generate invoice PDF (3 copies) with mPDF
//                                 // Ensure folders exist (public/invoices, public/mpdf-temp)
//                                 if (!file_exists(public_path('invoices'))) {
//                                     mkdir(public_path('invoices'), 0755, true);
//                                 }
//                                 if (!file_exists(public_path('mpdf-temp'))) {
//                                     mkdir(public_path('mpdf-temp'), 0755, true);
//                                 }
                            
//                                 $TIME = time();
//                                 $pdfFilePath = public_path('invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf');
                            
//                                 $mpdf = new Mpdf([
//                                     'tempDir' => public_path('mpdf-temp'),
//                                     'default_font' => 'dejavusans',
//                                 ]);

//                                 $fullData = [];
//                                 $cart = \App\Models\CartManagement::where('trial_id', $newTrial->id)->first();
//                                 if($cart->year){
//                                     $fullData['year'] = $cart->year;
//                                 }
//                                 else{
//                                     $fullData['year'] = date('Y',strtotime($cart->created_at));
//                                 }
//                                 $fullData['id'] = $cart->id;
//                                 $fullData['s_name'] = $getUser->name;
//                                 $fullData['s_gender'] = $student->gender;
//                                 $fullData['s_parent_name'] = $student->parent_name;
//                                 $fullData['payment_date'] = $cart->created_at;
//                                 if($cart->month){
//                                     $fullData['month'] = $cart->month;
//                                 }
//                                 else{
//                                     $fullData['month'] = date('F',strtotime($cart->created_at));
//                                 }
//                                 $fullData['course_name'] = $course->title;
//                                 $getCurrency = Currency::where('current_currency','on')->first();
//                                 if($getCurrency){
//                                     $fullData['currency'] = $getCurrency->symbol;
//                                 }
//                                 else{
//                                     $fullData['currency'] = 'Rs';
//                                 }
                               
//                                 $fullData['amount'] = $netCoursePrice;
//                                 $fullData['course_price_1'] = $originalPrice;
//                                 $fullData['discount'] = $discount;
//                                 $fullData['discount_type'] = $discountType;
//                                 $fullData['net_course_price_1'] = $netCoursePrice;
//                                 $fullData['admission_fee'] = 0.00;
//                                 $fullData['discount_admission_fee'] = 0.00;
//                                 $fullData['discount_type_admission_fee'] = 0.00;
//                                 $fullData['net_admission_fee'] = 0.00;


//                                 // $copies = ['Bank Copy', 'CEF Copy', 'Student Copy'];
//                                 // $html = '';
                                            
//                                 // foreach ($copies as $copyName) {
//                                 //     $html .= view('emails.invoice', [
//                                 //         'student' => $student,
//                                 //         'course' => $course,
//                                 //         'data' => $requestData,
//                                 //         'copy_type' => $copyName,
//                                 //     ])->render();
                                    
//                                 //     if ($copyName !== end($copies)) {
//                                 //         // mPDF page break tag
//                                 //         $html .= '<pagebreak />';
//                                 //     }
//                                 // }
//                                 $html = view('emails.invoice-challan', ["data" => $fullData])->render();
//                                 $mpdf->WriteHTML($html);
//                                 $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);
                            
//                                 // Save relative path and mark approved on trial
//                                 $relativePath = 'invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf';
//                                 $newTrial->invoice_pdf_path = $relativePath;
//                                 $newTrial->approved = 1;
//                                 $newTrial->save();

//                                  // Update cart record created by live_course_checkout (if any)
//                                 $cart = CartManagement::where('trial_id', $newTrial->id)->first();
//                                 if ($cart) {
//                                     $cart->invoice_pdf_path = $relativePath;
//                                     $cart->save();
//                                 }
                            
//                                 // Send email with attachments using your helper
//                                 $attachments = [
//                                     [
//                                         'path' => $pdfFilePath,
//                                         'name' => 'Invoice-' . ($course->title ?? 'course') . '.pdf',
//                                         'mime' => 'application/pdf',
//                                     ],
//                                 ];
                            
//                                 $template_name = 'enrollment-request-approved';
//                                 sendEmailNotification([
//                                     '{{USER}}' => $getUser->name,
//                                     '{{COURSE}}' => $course->title ?? '',
//                                     '{{CHECKOUT}}' => $checkoutLink,
//                                 ], $getUser->email, $template_name, $attachments);
                            
//                                 DB::commit();
                                
//                                 Log::info("[ScheduleJob] Enrollment #{$enrollment->id} processed successfully. Invoice saved: {$relativePath}");
                                
//                             }
//                             else{
//                                 Log::warning("[ScheduleJob] Enrollment #{$enrollment->id} next cart entry already craeted (trial/course/student). Skipped.");
//                             }
//                         }
//                         else{
//                              Log::warning("[ScheduleJob] Enrollment #{$enrollment->id} order is not paid (trial/course/student). Skipped.");
//                         }
//                     }
//                     else{
//                          Log::warning("[ScheduleJob] Enrollment #{$enrollment->id} missing order (trial/course/student). Skipped.");
//                     }
//                 }
//                 else{
//                     Log::warning("[ScheduleJob] No Paid Existing Enrollment Found");
//                 }
//             } catch (\Throwable $ex) {
//                 DB::rollBack();
//                 Log::error("[ScheduleJob] Error processing enrollment #{$enrollment->id}: " . $ex->getMessage());
//                 // continue with next enrollment
//             }
//         } // end foreach

//         Log::info('[ScheduleJob] processTrialEnrollments finished at '.now());
// }





 public function processTrialEnrollments()
{
        Log::warning('[ScheduleJob] Deprecated HTTP endpoint processTrialEnrollments was called. Use: php artisan process-trial-enrollments');

        return response(
            'This endpoint is disabled to prevent duplicate monthly fee generation. '
            . 'Use: php artisan process-trial-enrollments (or php artisan monthly-fees:preview for a dry run).',
            410
        );
}
   public function processTrialEnrollments2()
    {
        Log::info('[ScheduleJob] processTrialEnrollments started at '.now());

        // First day of current month (00:00)
        $firstOfMonth = Carbon::now()->startOfMonth()->startOfDay();
      
        // Use Enrollment records (course_type = 3), active and not from license, with end_date in future
        // $payments = Enrollment::with('course')
        //     ->where('from_license', 0)
        //     // ->whereNotNull('trial_id')
        //     ->where('status', 1)
        //     ->where('end_date', '>', Carbon::now())
        //     ->whereHas('course', function ($q) {
        //         $q->where('course_type', 3);
        //     })
        //     ->whereHas('user', function ($q) {
        //     })
        //     ->orderBy('id', 'ASC')
        //     ->get();


        $payments = Enrollment::with('course')
            ->where('from_license', 0)
            ->whereNotNull('main_price')
            ->whereNotNull('price')
            // ->whereNotNull('trial_id')
            ->where('status', 1)
            ->where('end_date', '>', Carbon::now())
            ->whereHas('course', function ($q) {
                $q->where('course_type', 3);
            })
            ->whereHas('user', function ($q) {
            })
            ->orderBy('id', 'ASC')
            ->get();

     
        Log::info('[ScheduleJob] Found '. $payments->count() .' trial enrollments to evaluate.');

        $Checktemplate = NotificationEmailTemplate::where(['slug' => 'payment-create-student'])->where('status', 1)->first();
        if($Checktemplate == null){
            Log::info("[ScheduleJob] processTrialEnrollments exited because given template #payment-create-student does not exists in db .");
            exit;
        }

        foreach ($payments as $payment) {
        
           // pick start = invoice_generated_at (if set) else created_at
            $start = $payment->invoice_generated_at ?? $payment->created_at;
            
            if (!$start) {
                Log::warning("[ScheduleJob] Enrollment #{$payment->id} has no start_date/created_at — skipped.");
                continue;
            }
            $startDate = Carbon::parse($start)->startOfDay();
            // skip if enrollment start is in future relative to firstOfMonth
            if ($startDate->greaterThan($firstOfMonth)) {
                Log::info("[ScheduleJob] Enrollment #{$payment->id} start date is after {$firstOfMonth->toDateString()} — skipped.");
                continue;
            }
             
            $diffDays = $firstOfMonth->diffInDays($startDate);  
           
            if ($diffDays < 15) {
                Log::info("[ScheduleJob] Enrollment #{$payment->id} diffDays={$diffDays} (<15) — skipped.");
                continue;
            }
               
            Log::info("[ScheduleJob] Enrollment #{$payment->id} eligible (diffDays={$diffDays}). Processing...");

            

            // Wrap processing for each enrollment in its own transaction
            DB::beginTransaction();
            try {
                // Fetch related models
                $trial = TrialClass::find($payment->trial_id);
                $course = Course::find($payment->course_id);
                $userId = $payment->user_id;
                $student = Student::where('user_id', $userId)->first();
                 
                // if (!$trial || !$course || !$student) {
                //     Log::warning("[ScheduleJob] Enrollment #{$payment->id} missing related models (trial/course/student). Skipped.");
                //     DB::rollBack();
                //     continue;
                // }
                  
                if (!$course || !$student) {
                    Log::warning("[ScheduleJob] Enrollment #{$payment->id} missing related models (trial/course/student). Skipped.");
                    DB::rollBack();
                    continue;
                }
              
                // Prepare requestData (admission_fee = 0 for cron run)
                $originalPrice = $payment->original_price ?? 0;
                $discount = $payment->discount ?? 0;
                $discountType = $payment->discount_type ?? 0; // 0 = Amount, 1 = Percentage
                // Calculate net course price based on discount type
                if ($discountType == 1) {
                    // Percentage discount
                    $netCoursePrice = $originalPrice - (($discount / 100) * $originalPrice);
                } else {
                    // Fixed amount discount
                    $netCoursePrice = $originalPrice - $discount;
                }
                
                // Ensure it doesn’t go negative
                if ($netCoursePrice < 0) {
                    $netCoursePrice = 0;
                }
             
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

                 
                
                // $checkPayment = Order_item::where(['course_id' => $course->id , 'user_id' => $userId])
                //     ->whereHas('order', function ($q) {
                //         $q->where('payment_status', 'paid');
                //     })
                //     ->first();
                $checkPayment = Enrollment::with('course')
                ->where('from_license', 0)
                // ->whereNotNull('trial_id')
                ->where('status', 1)
                ->where('end_date', '>', Carbon::now())
                ->whereHas('course', function ($q) {
                    $q->where('course_type', 3);
                })
                ->whereHas('user', function ($q) {
                })
                ->where('course_id',$course->id)
                ->where('user_id',$userId)
                ->first();
    
                if($checkPayment) {
                            $existingMonthly = existing_pending_monthly_cron_cart($userId, $course->id);
                            if ($existingMonthly !== null) {
                                Log::info("[ScheduleJob] processTrialEnrollments2 Enrollment #{$payment->id}: monthly cart already exists for this period (cart id {$existingMonthly->id}) — skipping.");
                                DB::rollBack();
                                continue;
                            }

                            // ✅ Ensure unique prefer_date + prefer_time combination
                            if ($trial) {
                                // use trial's date/time
                                $preferDate = Carbon::parse($trial->prefer_date)->startOfDay();
                                $preferTime = $trial->prefer_time;
                            } else {
                                // fallback defaults when no trial exists
                                $preferDate = Carbon::today()->addDay()->startOfDay(); // start from tomorrow
                                $preferTime = '09:00 AM'; // or any default slot you want
                            }


                            // Get today's date (start of day for safe comparison)
                            $today = Carbon::today();

                            // Loop until we find a unique AND future date/time slot
                            while (
                                $preferDate->lessThanOrEqualTo($today) || // ensure date > today
                                TrialClass::where('prefer_date', $preferDate->toDateString())
                                          ->where('prefer_time', $preferTime)
                                          ->exists()
                            ) {
                                $preferDate->addDay(); // Increment date until unique + future
                            }


                           
                            $newTrial = new TrialClass();
                            $newTrial->student_id = $student->id;
                            $newTrial->prefer_date = $preferDate->toDateString();
                            $newTrial->prefer_time = $preferTime;
                            if($trial){
                                $newTrial->course_id = $trial->course_id;
                            }
                            else{
                                $newTrial->course_id = $course->id;
                            }
                            $newTrial->save();
                          
                            $cartExists = CartManagement::whereUserId($userId)->whereCourseId($course->id)
                                            ->where('request_enrollment',1)
                                            ->where('trial_id',$newTrial->id)->first();
                           
                            if (!$cartExists) {
                                $result = live_course_checkout_cron($course, $student, $newTrial->id, $requestData);
                                $checkoutLink = isset($result['is_true']) ? route('student.checkout', ['type' => 1, 'location' => 1]) : null;

                                if (!$checkoutLink) {
                                    Log::warning("[ScheduleJob] live_course_checkout for Enrollment #{$payment->id} did not return checkout link. Skipped.");
                                    DB::rollBack();
                                    continue;
                                }

                                  // Find user to send email
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
                                $pdfFilePath = public_path('invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf');
                            
                                $mpdf = new Mpdf([
                                    'tempDir' => public_path('mpdf-temp'),
                                    'default_font' => 'dejavusans',
                                ]);

                                $fullData = [];
                                $cart = \App\Models\CartManagement::where('trial_id', $newTrial->id)->first();
                                if($cart->year){
                                    $fullData['year'] = $cart->year;
                                }
                                else{
                                    $fullData['year'] = date('Y',strtotime($cart->created_at));
                                }
                                $fullData['id'] = $cart->id;
                                $fullData['s_name'] = $getUser->name;
                                $fullData['s_gender'] = $student->gender;
                                $fullData['s_parent_name'] = $student->parent_name;
                                $fullData['payment_date'] = $cart->created_at;
                                if($cart->month){
                                    $fullData['month'] = $cart->month;
                                }
                                else{
                                    $fullData['month'] = date('F',strtotime($cart->created_at));
                                }
                                $fullData['course_name'] = $course->title;
                                $getCurrency = Currency::where('current_currency','on')->first();
                                if($getCurrency){
                                    $fullData['currency'] = $getCurrency->symbol;
                                }
                                else{
                                    $fullData['currency'] = 'Rs';
                                }
                               
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
                            
                                // Save relative path and mark approved on trial
                                $relativePath = 'invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf';
                                $newTrial->invoice_pdf_path = $relativePath;
                                $newTrial->approved = 1;
                                $newTrial->save();

                                 // Update cart record created by live_course_checkout (if any)
                                $cart = CartManagement::where('trial_id', $newTrial->id)->first();
                                if ($cart) {
                                    $cart->invoice_pdf_path = $relativePath;
                                    $cart->save();
                                }
                            
                                // Send email with attachments using your helper
                                $attachments = [
                                    [
                                        'path' => $pdfFilePath,
                                        'name' => 'Invoice-' . ($course->title ?? 'course') . '.pdf',
                                        'mime' => 'application/pdf',
                                    ],
                                ];
                            
                                // $template_name = 'enrollment-request-approved';
                                // sendEmailNotification([
                                //     '{{USER}}' => $getUser->name,
                                //     '{{COURSE}}' => $course->title ?? '',
                                //     '{{CHECKOUT}}' => $checkoutLink,
                                // ], $getUser->email, $template_name, $attachments);
                            
                                $template_name = 'Payment-create-student';
                                $viewData = [
                                    '{{USER_NAME}}' => $getUser->name,
                                    '{{COURSE_NAME}}' => $course->title ?? 'COURSE',
                                    // show total amount paid (totalNet) or per-student? I keep per previous behaviour - pass total
                                    '{{COURSE_FEE}}' => (int)$netCoursePrice,
                                    '{{CHECKOUT}}' => $checkoutLink,
                                
                                ];
                            
                                $emailResponse = sendEmailNotification($viewData, $getUser->email, $template_name);

                                $payment->invoice_generated_at = Carbon::now();
                                $payment->save();
                                DB::commit();
                                
                                Log::info("[ScheduleJob] Enrollment #{$payment->id} processed successfully. Invoice saved: {$relativePath}");
                                
                            }
                            else{
                                Log::warning("[ScheduleJob] Enrollment #{$payment->id} next cart entry already craeted (trial/course/student). Skipped.");
                            }
                }
                else{
                    Log::warning("[ScheduleJob] No Paid Existing Payment Found");
                }
            } catch (\Throwable $ex) {
                DB::rollBack();
                Log::error("[ScheduleJob] Error processing enrollment #{$payment->id}: " . $ex->getMessage());
                // continue with next enrollment
            }
        } // end foreach

        Log::info('[ScheduleJob] processTrialEnrollments finished at '.now());
}





//    public function processTrialEnrollments()
//     {
//         Log::info('[ScheduleJob] processTrialEnrollments started at '.now());

//         // First day of current month (00:00)
//         $firstOfMonth = Carbon::now()->startOfMonth()->startOfDay();
      
//         // get enrollments which are trial-based and active (status = 1)
//         $payments = Order_item::whereNotNull('trial_id')
//             ->whereHas('order', function ($q) {
//                 $q->where('payment_status', 'paid');
//             })
//             ->orderBy('id','DESC')
//             ->get();

       
//         Log::info('[ScheduleJob] Found '. $payments->count() .' trial enrollments to evaluate.');

//         foreach ($payments as $payment) {
           
//             // Determine enrollment start date (fall back to created_at)
//             $start = $payment->created_at;
//             if (!$start) {
//                 Log::warning("[ScheduleJob] Enrollment #{$payment->id} has no start_date/created_at — skipped.");
//                 continue;
//             }
//             $startDate = Carbon::parse($start)->startOfDay();
//             // skip if enrollment start is in future relative to firstOfMonth
//             if ($startDate->greaterThan($firstOfMonth)) {
//                 Log::info("[ScheduleJob] Enrollment #{$payment->id} start date is after {$firstOfMonth->toDateString()} — skipped.");
//                 continue;
//             }
//             $diffDays = $firstOfMonth->diffInDays($startDate);    
//             if ($diffDays < 15) {
//                 Log::info("[ScheduleJob] Enrollment #{$payment->id} diffDays={$diffDays} (<15) — skipped.");
//                 continue;
//             }
               
//             Log::info("[ScheduleJob] Enrollment #{$payment->id} eligible (diffDays={$diffDays}). Processing...");

//             // Wrap processing for each enrollment in its own transaction
//             DB::beginTransaction();
//             try {
                
//                 // Fetch related models
//                 $trial = TrialClass::find($payment->trial_id);
                
//                 $course = Course::find($payment->course_id);
//                 $userId = $payment->user_id;
//                 $student = Student::where('user_id', $userId)->first();
                 
//                 if (!$trial || !$course || !$student) {
//                     Log::warning("[ScheduleJob] Enrollment #{$payment->id} missing related models (trial/course/student). Skipped.");
//                     DB::rollBack();
//                     continue;
//                 }
              
//                 // Prepare requestData (admission_fee = 0 for cron run)
//                 $originalPrice = $payment->original_price ?? 0;
//                 $discount = $payment->discount ?? 0;
//                 $discountType = $payment->discount_type ?? 0; // 0 = Amount, 1 = Percentage
//                 // Calculate net course price based on discount type
//                 if ($discountType == 1) {
//                     // Percentage discount
//                     $netCoursePrice = $originalPrice - (($discount / 100) * $originalPrice);
//                 } else {
//                     // Fixed amount discount
//                     $netCoursePrice = $originalPrice - $discount;
//                 }
                
//                 // Ensure it doesn’t go negative
//                 if ($netCoursePrice < 0) {
//                     $netCoursePrice = 0;
//                 }
             
//                 $requestData = [
//                     'course_price' => $originalPrice,
//                     'discount_course_price' => $discount,
//                     'discount_type_course_price' => $discountType,
//                     'net_course_price' => $netCoursePrice,
//                     'admission_fee' => 0.00,
//                     'discount_admission_fee' => 0.00,
//                     'discount_type_admission_fee' => 0.00,
//                     'net_admission_fee' => 0.00,
//                     'net_price' => $netCoursePrice,
//                 ];

                 
                


//                 // Call existing function to create cart etc.
//                 // NOTE: your live_course_checkout in the app accepts 4 args in your code earlier.
//                 // $result = live_course_checkout($course, $student, $trial->id, $requestData);
//                 // $checkEnrollment = Enrollment::where(['course_id' => $course->id , 'user_id' => $userId, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
//                 $checkPayment = Order_item::where(['course_id' => $course->id , 'user_id' => $userId])
//                     ->whereHas('order', function ($q) {
//                         $q->where('payment_status', 'paid');
//                     })
//                     ->first();
//                 if($checkPayment) {
//                     $order = Order::find($checkPayment->order_id);
//                     if ($order) {
//                         if ($order->payment_status == 'paid') {
                             
//                             // ✅ Ensure unique prefer_date + prefer_time combination
//                             $preferDate = Carbon::parse($trial->prefer_date)->startOfDay();
//                             $preferTime = $trial->prefer_time;

//                             // Get today's date (start of day for safe comparison)
//                             $today = Carbon::today();

//                             // Loop until we find a unique AND future date/time slot
//                             while (
//                                 $preferDate->lessThanOrEqualTo($today) || // ensure date > today
//                                 TrialClass::where('prefer_date', $preferDate->toDateString())
//                                           ->where('prefer_time', $preferTime)
//                                           ->exists()
//                             ) {
//                                 $preferDate->addDay(); // Increment date until unique + future
//                             }


                           
//                             $newTrial = new TrialClass();
//                             $newTrial->student_id = $student->id;
//                             $newTrial->prefer_date = $preferDate->toDateString();
//                             $newTrial->prefer_time = $preferTime;
//                             $newTrial->course_id = $trial->course_id;
//                             $newTrial->save();
                          
//                             $cartExists = CartManagement::whereUserId($userId)->whereCourseId($course->id)
//                                             ->where('request_enrollment',1)
//                                             ->where('trial_id',$newTrial->id)->first();
                           
//                             if (!$cartExists) {
//                                 $result = live_course_checkout_cron($course, $student, $newTrial->id, $requestData);
//                                 $checkoutLink = isset($result['is_true']) ? route('student.checkout', ['type' => 1, 'location' => 1]) : null;

//                                 if (!$checkoutLink) {
//                                     Log::warning("[ScheduleJob] live_course_checkout for Enrollment #{$enrollment->id} did not return checkout link. Skipped.");
//                                     DB::rollBack();
//                                     continue;
//                                 }

//                                   // Find user to send email
//                                 $getUser = User::find($student->user_id);
//                                 if (!$getUser) {
//                                     Log::warning("[ScheduleJob] User not found for student (enrollment #{$enrollment->id}). Skipped.");
//                                     DB::rollBack();
//                                     continue;
//                                 }

//                                 // Generate invoice PDF (3 copies) with mPDF
//                                 // Ensure folders exist (public/invoices, public/mpdf-temp)
//                                 if (!file_exists(public_path('invoices'))) {
//                                     mkdir(public_path('invoices'), 0755, true);
//                                 }
//                                 if (!file_exists(public_path('mpdf-temp'))) {
//                                     mkdir(public_path('mpdf-temp'), 0755, true);
//                                 }
                            
//                                 $TIME = time();
//                                 $pdfFilePath = public_path('invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf');
                            
//                                 $mpdf = new Mpdf([
//                                     'tempDir' => public_path('mpdf-temp'),
//                                     'default_font' => 'dejavusans',
//                                 ]);

//                                 $fullData = [];
//                                 $cart = \App\Models\CartManagement::where('trial_id', $newTrial->id)->first();
//                                 if($cart->year){
//                                     $fullData['year'] = $cart->year;
//                                 }
//                                 else{
//                                     $fullData['year'] = date('Y',strtotime($cart->created_at));
//                                 }
//                                 $fullData['id'] = $cart->id;
//                                 $fullData['s_name'] = $getUser->name;
//                                 $fullData['s_gender'] = $student->gender;
//                                 $fullData['s_parent_name'] = $student->parent_name;
//                                 $fullData['payment_date'] = $cart->created_at;
//                                 if($cart->month){
//                                     $fullData['month'] = $cart->month;
//                                 }
//                                 else{
//                                     $fullData['month'] = date('F',strtotime($cart->created_at));
//                                 }
//                                 $fullData['course_name'] = $course->title;
//                                 $getCurrency = Currency::where('current_currency','on')->first();
//                                 if($getCurrency){
//                                     $fullData['currency'] = $getCurrency->symbol;
//                                 }
//                                 else{
//                                     $fullData['currency'] = 'Rs';
//                                 }
                               
//                                 $fullData['amount'] = $netCoursePrice;
//                                 $fullData['course_price_1'] = $originalPrice;
//                                 $fullData['discount'] = $discount;
//                                 $fullData['discount_type'] = $discountType;
//                                 $fullData['net_course_price_1'] = $netCoursePrice;
//                                 $fullData['admission_fee'] = 0.00;
//                                 $fullData['discount_admission_fee'] = 0.00;
//                                 $fullData['discount_type_admission_fee'] = 0.00;
//                                 $fullData['net_admission_fee'] = 0.00;


//                                 // $copies = ['Bank Copy', 'CEF Copy', 'Student Copy'];
//                                 // $html = '';
                                            
//                                 // foreach ($copies as $copyName) {
//                                 //     $html .= view('emails.invoice', [
//                                 //         'student' => $student,
//                                 //         'course' => $course,
//                                 //         'data' => $requestData,
//                                 //         'copy_type' => $copyName,
//                                 //     ])->render();
                                    
//                                 //     if ($copyName !== end($copies)) {
//                                 //         // mPDF page break tag
//                                 //         $html .= '<pagebreak />';
//                                 //     }
//                                 // }
//                                 $html = view('emails.invoice-challan', ["data" => $fullData])->render();
//                                 $mpdf->WriteHTML($html);
//                                 $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);
                            
//                                 // Save relative path and mark approved on trial
//                                 $relativePath = 'invoices/invoice_' . $newTrial->id .'_'.$TIME.'.pdf';
//                                 $newTrial->invoice_pdf_path = $relativePath;
//                                 $newTrial->approved = 1;
//                                 $newTrial->save();

//                                  // Update cart record created by live_course_checkout (if any)
//                                 $cart = CartManagement::where('trial_id', $newTrial->id)->first();
//                                 if ($cart) {
//                                     $cart->invoice_pdf_path = $relativePath;
//                                     $cart->save();
//                                 }
                            
//                                 // Send email with attachments using your helper
//                                 $attachments = [
//                                     [
//                                         'path' => $pdfFilePath,
//                                         'name' => 'Invoice-' . ($course->title ?? 'course') . '.pdf',
//                                         'mime' => 'application/pdf',
//                                     ],
//                                 ];
                            
//                                 $template_name = 'enrollment-request-approved';
//                                 sendEmailNotification([
//                                     '{{USER}}' => $getUser->name,
//                                     '{{COURSE}}' => $course->title ?? '',
//                                     '{{CHECKOUT}}' => $checkoutLink,
//                                 ], $getUser->email, $template_name, $attachments);
                            
//                                 DB::commit();
                                
//                                 Log::info("[ScheduleJob] Enrollment #{$payment->id} processed successfully. Invoice saved: {$relativePath}");
                                
//                             }
//                             else{
//                                 Log::warning("[ScheduleJob] Enrollment #{$payment->id} next cart entry already craeted (trial/course/student). Skipped.");
//                             }
//                         }
//                         else{
//                              Log::warning("[ScheduleJob] Enrollment #{$payment->id} order is not paid (trial/course/student). Skipped.");
//                         }
//                     }
//                     else{
//                          Log::warning("[ScheduleJob] Enrollment #{$payment->id} missing order (trial/course/student). Skipped.");
//                     }
//                 }
//                 else{
//                     Log::warning("[ScheduleJob] No Paid Existing Payment Found");
//                 }
//             } catch (\Throwable $ex) {
//                 DB::rollBack();
//                 Log::error("[ScheduleJob] Error processing enrollment #{$payment->id}: " . $ex->getMessage());
//                 // continue with next enrollment
//             }
//         } // end foreach

//         Log::info('[ScheduleJob] processTrialEnrollments finished at '.now());
// }





    public function updateInvoiceEmailTemplates(Request $request)
    {
        // Template for id = 52 (Pending reminder)
        $bodyPending = <<<'HTML'
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#F3F7F4; padding:20px; color:#333;">

<div style="max-width:650px; margin:auto; background:#ffffff; padding:25px; border-radius:12px; border:1px solid #e2e2e2;">

 <h2 style="color:#1A6F4C; text-align:center; margin-top:0;">
 ⏳ Friendly Reminder – Your Invoice Is Still Pending
 </h2>

 <p>Assalamu Alaikum <strong>{{USER}}</strong> 🌿,</p>

 <p style="font-size:15px;">
 This is a gentle reminder that your invoice for the following course is still pending. 
 We kindly request you to complete your payment whenever convenient so your learning journey can begin smoothly.
 </p>

 <!-- Two Column Box -->
 <table width="100%" cellspacing="0" cellpadding="12" 
 style="border-radius:12px; background:#F3F7F4; border:2px solid #1A6F4C;">
 <tr>
 <td width="50%" style="vertical-align: top;">
 <strong style="color:#1A6F4C;">📘 Course Name:</strong><br>
 {{COURSE}}
 </td>
 <td width="50%" style="vertical-align: top;">
 <strong style="color:#1A6F4C;">💰 Fee:</strong><br>
 Rs. {{FEE}}
 </td>
 </tr>
 <tr>
 <td colspan="2" style="padding-top:10px;">
 <strong style="color:#1A6F4C;">🔗 Payment Link:</strong><br>
 <a href="{{CHECKOUT}}" 
 style="color:#C9A857; font-weight:bold; font-size:16px;">
 Click here to complete your payment
 </a>
 </td>
 </tr>
 </table>

 <br>

 <p style="font-size:15px;">
 🌙 Your commitment to learning the Qur’an is deeply appreciated, and we are eager to welcome you into the class as soon as the payment is completed.
 </p>

 <p style="font-size:15px;">
 If you have already paid, kindly ignore this message — may Allah reward you abundantly for your efforts. 🤲
 </p>

 <p style="margin-top:25px;">
 Warm regards,<br>
 <strong style="color:#1A6F4C;">CEF Online Academy</strong><br>
 📚 Inspiring Character • Enriching Minds • Strengthening Faith
 </p>

</div>

</body>
</html>
HTML;

        $variablesPending = '{{USER}},{{COURSE}},{{FEE}},{{CHECKOUT}}';

        // Template for id = 44 (Invoice ready)
        $bodyReady = <<<'HTML'
<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#F3F7F4; padding:20px; color:#333;">

<div style="max-width:650px; margin:auto; background:#ffffff; padding:25px; border-radius:12px; border:1px solid #e2e2e2;">

 <h2 style="color:#1A6F4C; text-align:center; margin-top:0;">
 🌟 Your Invoice is Ready – Let's Begin Your Learning Journey!
 </h2>

 <p>Assalamu Alaikum <strong>{{USER_NAME}}</strong> 🌿,</p>

 <p style="font-size:15px;">
 We are delighted to inform you that your invoice for the course below has been generated. 
 Please review the details and complete your payment at your earliest convenience.
 </p>

 <!-- Two Column Box -->
 <table width="100%" cellspacing="0" cellpadding="12" 
 style="border-radius:12px; background:#F3F7F4; border:2px solid #1A6F4C;">
 <tr>
 <td width="50%" style="vertical-align: top;">
 <strong style="color:#1A6F4C;">📘 Course Name:</strong><br>
 {{COURSE_NAME}}
 </td>
 <td width="50%" style="vertical-align: top;">
 <strong style="color:#1A6F4C;">💰 Fee:</strong><br>
 Rs. {{COURSE_FEE}}
 </td>
 </tr>
 <tr>
 <td colspan="2" style="padding-top:10px;">
 <strong style="color:#1A6F4C;">🔗 Payment Link:</strong><br>
 <a href="{{CHECKOUT}}" 
 style="color:#C9A857; font-weight:bold; font-size:16px;">
 Click here to pay your invoice
 </a>
 </td>
 </tr>
 </table>

 <br>

 <p style="font-size:15px;">
 🌙 We are excited to accompany you on this beautiful journey of learning the Qur’an. 
 May Allah make this experience meaningful, beneficial, and full of barakah for you.
 </p>

 <p style="margin-top:25px;">
 Warm regards,<br>
 <strong style="color:#1A6F4C;">CEF Online Academy</strong> 
 <br>📚 Inspiring Character • Enriching Minds • Strengthening Faith
 </p>

</div>

</body>
</html>
HTML;

        $variablesReady = '{{USER_NAME}},{{COURSE_NAME}},{{COURSE_FEE}},{{CHECKOUT}}';

        // Perform updates within a transaction
        DB::beginTransaction();

        try {
            // DB::table('notification_email_templates')
            //     // ->where('id', 52)
            //     ->where('slug','pending-enrollment-request-reminder')
            //     ->update([
            //         'body'      => $bodyPending,
            //         'variables' => $variablesPending,
            //         'updated_at' => now(), // optional: keep timestamps consistent if table uses them
            //     ]);

            // DB::table('notification_email_templates')
            //     // ->where('id', 44)
            //     ->where('slug','payment-create-student')
            //     ->update([
            //         'body'      => $bodyReady,
            //         'variables' => $variablesReady,
            //         'updated_at' => now(),
            //     ]);

            NotificationEmailTemplate::updateOrCreate(
                ['slug' => 'pending-enrollment-request-reminder'],
                [
                    'category' => 'Pending Enrollment Request Reminder',
                    'subject' => 'Pending Enrollment Request Reminder For Live Course',
                    'body'      => $bodyPending,
                    'variables' => $variablesPending,
                    'status' => 1,
                    'updated_at' => now(),
                ]
            );
            
            NotificationEmailTemplate::updateOrCreate(
                ['slug' => 'payment-create-student'],
                [
                    'category' => 'Payment Create(Student)',
                    'subject' => 'Payment Create(Student)',
                    'body'      => $bodyReady,
                    'variables' => $variablesReady,
                     'status' => 1,
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Email templates updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update templates: ' . $e->getMessage(),
            ], 500);
        }
    } 

//   public function sendReminderEmailEnrollments()
// {
//         Log::info('[ScheduleJob] sendReminderEmailEnrollments started at '.now());

//           // get enrollments which are trial-based and active (status = 1)
//         $pendingRequests = CartManagement::
//             // whereNotNull('trial_id')
//             where('is_donation', 0)
//             ->where('request_enrollment', 1)
//             ->orderBy('id','ASC')
//             ->get();

       

      
     
//         $Checktemplate = NotificationEmailTemplate::where(['slug' => 'pending-enrollment-request-reminder'])->where('status', 1)->first();
     
//         if($Checktemplate == null){
//             Log::info("[ScheduleJob] sendReminderEmailEnrollments exited because given template #pending-enrollment-request-reminder does not exists in db .");
//             exit;
//         }

//         Log::info('[ScheduleJob] Found '. $pendingRequests->count() .' pending requests enrollments to resend email.');

      
//         foreach ($pendingRequests as $key => $pending) {
//             $counter = $key + 1;
//             Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} SR NO # {$counter}.");
//             // ✅ Calculate how many days passed since creation
//             $daysPassed = Carbon::parse($pending->created_at)->diffInDays(Carbon::now());
//             Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} was created {$daysPassed} days ago.");
          
//             // Example: Skip if less than 2 days old
//             if ($daysPassed < 2) {
//                 Log::info("[ScheduleJob] Skipping Pending Request Enrollment #{$pending->id}, only {$daysPassed} days old.");
//                 continue;
//             }
           

            
//                   // Find user to send email
//             $getUser = User::find($pending->user_id);

             
            

//             if (!$getUser) {
//                 Log::warning("[ScheduleJob] User not found for student (pending request enrollment #{$pending->id}). Skipped.");
//                 DB::rollBack();
//                 continue;
//             }

//             $getCourse = Course::find($pending->course_id);



//             if (!$getCourse) {
//                 Log::warning("[ScheduleJob] Course not found for student (pending request enrollment #{$pending->id}). Skipped.");
//                 DB::rollBack();
//                 continue;
//             }

              
//             $template_name = 'pending-enrollment-request-reminder';
//             $checkoutLink = route('student.checkout', ['type' => 1, 'location' => 1]);

//             Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} user name #{$getUser->name}.");
//             Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} user email #{$getUser->email}.");
//             Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} course name #{$getCourse->title}.");
//             Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} fee #{$pending->main_price}.");

            
          
            
           

//             $checkResponse = sendEmailNotification([
//                 '{{USER}}' => $getUser->name,
//                 '{{COURSE}}' => $getCourse->title ?? '',
//                 // '{{DAYS}}' => $daysPassed,
//                 '{{FEE}}' => (int)$pending->main_price,
//                 '{{CHECKOUT}}' => $checkoutLink,
//             ], $getUser->email, $template_name);

//             if($checkResponse){
//                 Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} email resent successfully.");
//             }
//             else{
//                 Log::info("[ScheduleJob] Pending Request Enrollment #{$pending->id} email failed to sent.");
//             }
//         }
// }



    public function check_and_update_class_statuses(){
        Log::info('[ScheduleJob] check_and_update_class_statuses started at '.now());
        Log::info("<!-----------------------------------------------------------------------------------!>");
                    
        $now = Carbon::now();
        $today = $now->toDateString();
        $nowTime = $now->format('H:i:s');

        // ---- 0) Normalize accepted status variants for checks ----
        // Some places used "In Progress" and others "InProgress" — accept both when checking.
        $inProgressVariants = ['InProgress', 'In Progress'];


        try {
             // --------------------------
            // 1) If current DATE equals class_date, update Scheduled -> Pending
            // --------------------------
            $scheduledToPending = ClassDetail::whereDate('class_date', $today)
                ->where('status', 'Scheduled')
                ->get();

            // $check111 = ClassDetail::where('id','37387')->first();
            // dd($check111);
            foreach ($scheduledToPending as $c) {
                Log::info("[ScheduleJob] Date matched for class id {$c->id}. Status Scheduled -> Pending");
                Log::info("<!-----------------------------------------------------------------------------------!>");
             
                $c->update(['status' => 'Pending']);
            }

             // --------------------------
            // 2) When current TIME equals class_time: if Pending and any of the meeting columns exist => Pending -> InProgress
            // --------------------------
            // Individual classes (group_id is NULL)
            $existingMeetings = ClassDetail::whereNotNull('meeting_id')->pluck('meeting_id')->toArray();

            $individualTimeMatches = ClassDetail::whereNull('group_id')
                ->whereDate('class_date', $today)
                ->whereTime('class_time', '<=', $nowTime)
                ->where('status', 'Pending')
            ->get();

        
            foreach ($individualTimeMatches as $individualTimeMatch) {
                if($individualTimeMatch->class?->learning_tool == 'Google_Meet' || $individualTimeMatch->class?->learning_tool == 'Google Meet'){
                    if (empty($individualTimeMatch->meeting_id)) {
                        if(!empty($individualTimeMatch->google_meet_link)){
                            $met_id = generateUniqueMeetingId($existingMeetings);
                            $existingMeetings[] = $met_id;
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} met_id before entering helper (startmeetclassindividual) :- {{$met_id}}");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            $data = startmeetclassindividual($individualTimeMatch->id,$individualTimeMatch->student_id,$individualTimeMatch->instructor_id,$met_id,null,$individualTimeMatch->class?->learning_tool);
                            if($data){
                                // $return['id'] = $data['l_id'];
                                // $return['learning_tool'] = 'Zoom';
                                // $return['is_missed'] = 'No';
                                // $return['url'] = $data['TutorURI'];
                                // $return['is_group'] = $data['is_group'];
                                // $return['meeting_id'] = $data['meet_meeting_id'];
                                // $return['user_name'] = $data['tutor_name'];
                                // $return['user_id'] = $data['tutor_id'];
                                // $return['user_type'] = $data['tutor_type'];
                                $meetingID = $data['meet_meeting_id'];
                                $lesson_id = $data['l_id'];
                                $individualTimeMatch->update(['status' => 'In Progress', 'meeting_id' => $meetingID]);
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has google_meet_link, Creating Class InProgress.");
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has google_meet_link", ['data' => $data]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has google_meet_link", ['class' => $individualTimeMatch->toArray()]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");  
                            }
                            else{
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has google_meet_link, ERROR While Creating Class InProgress.");
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has google_meet_link, ERROR", ['class' => $individualTimeMatch->toArray()]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                            }
                        }    
                        else{
                            $met_id = generateUniqueMeetingId($existingMeetings);
                            $existingMeetings[] = $met_id;
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} met_id before entering helper (startmeetclassindividual) :- {{$met_id}}");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            $data = startmeetclassindividual($individualTimeMatch->id,$individualTimeMatch->student_id,$individualTimeMatch->instructor_id,$met_id,null,$individualTimeMatch->class?->learning_tool,0);
                            if($data){
                                // $return['id'] = $data['l_id'];
                                // $return['learning_tool'] = 'Google_Meet';
                                // $return['is_missed'] = 'No';
                                // $return['url'] = $data['google_meet_link'];
                                // $return['is_group'] = $data['is_group'];
                                // $return['meeting_id'] = $data['meet_meeting_id'];
                                // $return['user_name'] = $data['tutor_name'];
                                // $return['user_id'] = $data['tutor_id'];
                                // $return['user_type'] = $data['tutor_type'];
                            
                                $meetingID = $data['meet_meeting_id'];
                                $lesson_id = $data['l_id'];
                                $individualTimeMatch->update(['status' => 'In Progress', 'meeting_id' => $meetingID]);
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no google_meet_link, Creating Class InProgress.");
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no google_meet_link", ['data' => $data]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no google_meet_link", ['class' => $individualTimeMatch->toArray()]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");  
                            }
                            else{
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no google_meet_link, ERROR WHILE Creating Class InProgress.");
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no google_meet_link, ERROR", ['class' => $individualTimeMatch->toArray()]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                            }
                        }
                    }
                }
                else if($individualTimeMatch->class?->learning_tool == 'Zoom'){
                    if (empty($individualTimeMatch->meeting_id)) {
                        $met_id = generateUniqueMeetingId($existingMeetings);
                        $existingMeetings[] = $met_id;
                        Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} met_id before entering helper (startzoomclassindividual) :- {{$met_id}}");
                        Log::info("<!-----------------------------------------------------------------------------------!>");
                        $data = startzoomclassindividual($individualTimeMatch->id,$individualTimeMatch->student_id,$individualTimeMatch->instructor_id,$met_id,null,$individualTimeMatch->class?->learning_tool);
                        if($data){
                            // $return['id'] = $data['l_id'];
                            // $return['learning_tool'] = 'Zoom';
                            // $return['is_missed'] = 'No';
                            // $return['url'] = $data['TutorURI'];
                            // $return['is_group'] = $data['is_group'];
                            // $return['meeting_id'] = $data['zoom_meeting_id'];
                            // $return['user_name'] = $data['tutor_name'];
                            // $return['user_id'] = $data['tutor_id'];
                            // $return['user_type'] = $data['tutor_type'];
                        
                            $meetingID = $data['zoom_meeting_id'];
                            $lesson_id = $data['l_id'];
                            $individualTimeMatch->update(['status' => 'In Progress', 'meeting_id' => $meetingID]);
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no meeting_id, Creating Class InProgress.");  
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no meeting_id", ['data' => $data]);
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no meeting_id", ['class' => $individualTimeMatch->toArray()]);
                            Log::info("<!-----------------------------------------------------------------------------------!>");  
                        }
                        else{
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has ERROR While Creating Class InProgress.");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has ERROR", ['class' => $individualTimeMatch->toArray()]);
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                        }            
                       
                    } 
                }
                else if($individualTimeMatch->class?->learning_tool == 'BBB'){
                    if (empty($individualTimeMatch->InstructorURI)) {
                        $met_id = generateUniqueBBBMeetingId($existingMeetings);
                        $existingMeetings[] = $met_id;
                        $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $individualTimeMatch->class?->duration);
                        Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} met_id before entering helper (startbbbclassindividual) :- {{$met_id}}");
                        Log::info("<!-----------------------------------------------------------------------------------!>");
                        $data = startbbbclassindividual($individualTimeMatch->id,$individualTimeMatch->student_id,$individualTimeMatch->instructor_id,$met_id, $this->bb,null,$individualTimeMatch->class?->learning_tool,$meeting);
                    
                        if($data){
                            // $return['id'] = $data['l_id'];
                            // $return['learning_tool'] = 'BBB';
                            // $return['is_missed'] = 'No';
                            // $return['group_id'] = $data['group_id'];
                            // $return['url'] = $data['TutorURI'];
                            // $return['is_group'] = $data['is_group'];
                            // $return['meeting_id'] = $data['zoom_meeting_id'];
                            // $return['user_name'] = $data['tutor_name'];
                            // $return['user_id'] = $data['tutor_id'];
                            // $return['user_type'] = $data['tutor_type'];
                        
                            $meetingID = $data['zoom_meeting_id'];
                            $lesson_id = $data['l_id'];
                            $individualTimeMatch->update(['status' => 'In Progress', 'meeting_id' => $meetingID]);
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no InstructorURI, creating Class InProgress.");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no InstructorURI", ['data' => $data]);
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has no InstructorURI", ['class' => $individualTimeMatch->toArray()]);
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                        }
                        else{
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has ERROR While creating Class InProgress.");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            Log::info("[ScheduleJob] Class id {$individualTimeMatch->id} has ERROR", ['class' => $individualTimeMatch->toArray()]);
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                        }
                        
                    }
                }
            }



             // Group classes (group_id is NOT NULL) - handle time-based InProgress transition
            $groupTimeMatches = ClassDetail::whereNotNull('group_id')
                ->whereDate('class_date', $today)
                ->whereTime('class_time', '<=', $nowTime)
                ->where('status', 'Pending')
                ->select('group_id', 'class_date', 'class_time', 'end_time')
                ->distinct()
            ->get();

            foreach ($groupTimeMatches as $groupTimeMatch) {
                $groupTimeMatchclasses = ClassDetail::where('group_id', $groupTimeMatch->group_id)
                    ->where('class_date', $groupTimeMatch->class_date)
                    ->where('class_time', $groupTimeMatch->class_time)
                    ->where('status', 'Pending')
                    ->get();

                foreach ($groupTimeMatchclasses as $groupTimeMatchClass) {
                  
                    if($groupTimeMatchClass->class?->learning_tool == 'Google_Meet' || $groupTimeMatchClass->class?->learning_tool == 'Google Meet'){
                        if (empty($groupTimeMatchClass->meeting_id)) {
                            $met_id = generateUniqueMeetingId($existingMeetings);
                            $existingMeetings[] = $met_id;
                            Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) met_id before entering helper (startmeetclassgroup) :- {{$met_id}}");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            $data = startmeetclassgroup($groupTimeMatchClass->id,$groupTimeMatchClass->student_id,$groupTimeMatchClass->instructor_id,$met_id,$groupTimeMatchClass->group_id,$groupTimeMatchClass->class?->learning_tool);
                            if($data){
                                // $return['l_id'] = $data['l_id'];
                                // $return['is_group'] = $data['is_group'];
                                // $return['group_id'] = $data['group_id'];
                                // $return['learning_tool'] = 'Google_Meet';
                                // $return['is_missed'] = 'No';
                                // $return['url'] = $data['google_meet_link'];
                                // $return['meeting_id'] = $data['meet_meeting_id'];
                                // $return['user_name'] = $data['tutor_name'];
                                // $return['user_id'] = $data['tutor_id'];
                                // $return['user_type'] = $data['tutor_type'];
                            
                                $meetingID = $data['meet_meeting_id'];
                                $lesson_id = $data['l_id'];
                                $groupTimeMatchClass->update(['status' => 'In Progress', 'meeting_id' => $meetingID]);
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) Creating Class InProgress.");
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id})", ['data' => $data]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id})", ['class' => $groupTimeMatchClass->toArray()]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");

                            }
                            else{
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) at class_time {$groupTimeMatchClass->class_time} had no meeting identifiers; ERROR CREATING Creation.");
                                Log::info("<!---------------------------------------------------------------------------------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) at class_time {$groupTimeMatchClass->class_time}", ['class' => $groupTimeMatchClass->toArray()]);
                                Log::info("<!-------------------------------------------------------------------------------------------------------------------------------!>");
                            }
                        }
                    }
                    else if($groupTimeMatchClass->class?->learning_tool == 'Zoom'){
                        if (empty($groupTimeMatchClass->meeting_id)) {
                            $met_id = generateUniqueMeetingId($existingMeetings);
                            $existingMeetings[] = $met_id;
                            Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) met_id before entering helper (startzoomclassgroup) :- {{$met_id}}");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            $data = startzoomclassgroup($groupTimeMatchClass->id,$groupTimeMatchClass->student_id,$groupTimeMatchClass->instructor_id,$met_id,$groupTimeMatchClass->group_id,$groupTimeMatchClass->class?->learning_tool);
                            if($data){
                                // $return['id'] = $data['l_id'];
                                // $return['is_group'] = $data['is_group'];
                                // $return['group_id'] = $data['group_id'];
                                // $return['learning_tool'] = 'Zoom';
                                // $return['is_missed'] = 'No';
                                // $return['url'] = $data['TutorURI'];
                                // $return['meeting_id'] = $data['zoom_meeting_id'];
                                // $return['user_name'] = $data['tutor_name'];
                                // $return['user_id'] = $data['tutor_id'];
                                // $return['user_type'] = $data['tutor_type'];
                            
                                $meetingID = $data['zoom_meeting_id'];
                                $lesson_id = $data['l_id'];
                                $groupTimeMatchClass->update(['status' => 'In Progress', 'meeting_id' => $meetingID]);
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) Creating Class InProgress.");
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id})", ['data' => $data]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id})", ['class' => $groupTimeMatchClass->toArray()]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                            }
                            else{
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) at class_time {$groupTimeMatchClass->class_time} had no meeting identifiers; ERROR CREATING Creation.");
                                Log::info("<!----------------------------------------------------------------------------------------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) at class_time {$groupTimeMatchClass->class_time}", ['class' => $groupTimeMatchClass->toArray()]);
                                Log::info("<!---------------------------------------------------------------------------------------------------------------------------------!>");
                            }
                        } 
                    }
                    else if($groupTimeMatchClass->class?->learning_tool == 'BBB'){
                        if (empty($groupTimeMatchClass->InstructorURI)) {
                            $met_id = generateUniqueBBBMeetingId($existingMeetings);
                            $existingMeetings[] = $met_id;
                            $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $groupTimeMatchClass->class?->duration);
                            Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) met_id before entering helper (startbbbclassgroup) :- {{$met_id}}");
                            Log::info("<!-----------------------------------------------------------------------------------!>");
                            $data = startbbbclassgroup($groupTimeMatchClass->id,$groupTimeMatchClass->student_id,$groupTimeMatchClass->instructor_id,$met_id, $this->bb,$groupTimeMatchClass->group_id,$groupTimeMatchClass->class?->learning_tool,$meeting);
                            if($data){
                                // $return['id'] = $data['l_id'];
                                // $return['is_group'] = $data['is_group'];
                                // $return['group_id'] = $data['group_id'];
                                // $return['learning_tool'] = 'BBB';
                                // $return['is_missed'] = 'No';
                                // $return['url'] = $data['TutorURI'];
                                // $return['meeting_id'] = $data['zoom_meeting_id'];
                                // $return['user_name'] = $data['tutor_name'];
                                // $return['user_id'] = $data['tutor_id'];
                                // $return['user_type'] = $data['tutor_type'];
                            
                                $meetingID = $data['zoom_meeting_id'];
                                $lesson_id = $data['l_id'];
                                $groupTimeMatchClass->update(['status' => 'In Progress', 'meeting_id' => $meetingID]);
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) Creating Class InProgress.");
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id})", ['data' => $data]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id})", ['class' => $groupTimeMatchClass->toArray()]);
                                Log::info("<!-----------------------------------------------------------------------------------!>");
                            }
                            else{
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) at class_time {$groupTimeMatchClass->class_time} had no meeting identifiers; ERROR CREATING Creation.");
                                Log::info("<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------!>");
                                Log::info("[ScheduleJob] Group class id {$groupTimeMatchClass->id} (group {$groupTimeMatchClass->group_id}) at class_time {$groupTimeMatchClass->class_time}", ['class' => $groupTimeMatchClass->toArray()]);
                                Log::info("<!----------------------------------------------------------------------------------------------------------------------------------!>");
                            }
                        }
                    }

                }
            }














            $individualTimeMatchesUpdate = ClassDetail::whereNull('group_id')
                ->whereDate('class_date', $today)
                ->whereTime('class_time', '<=', $nowTime)
                ->where('status', 'Pending')
            ->get();

        

            foreach ($individualTimeMatchesUpdate as $individualTimeMatchesUpdatePer) {
                $shouldStart = false;

                if($individualTimeMatchesUpdatePer->class?->learning_tool == 'Google_Meet' || $individualTimeMatchesUpdatePer->class?->learning_tool == 'Google Meet'){
                    if (!empty($individualTimeMatchesUpdatePer->google_meet_link)) {
                        Log::info("[ScheduleJob] Class id {$individualTimeMatchesUpdatePer->id} has google_meet_link, starting InProgress.");
                        $shouldStart = true;
                    }
                }
                else if($individualTimeMatchesUpdatePer->class?->learning_tool == 'Zoom'){
                    if (!empty($individualTimeMatchesUpdatePer->meeting_id)) {
                        Log::info("[ScheduleJob] Class id {$individualTimeMatchesUpdatePer->id} has meeting_id, starting InProgress.");
                        $shouldStart = true;
                    } 
                }
                else if($individualTimeMatchesUpdatePer->class?->learning_tool == 'BBB'){
                    if (!empty($individualTimeMatchesUpdatePer->StudentURI)) {
                        Log::info("[ScheduleJob] Class id {$individualTimeMatchesUpdatePer->id} has StudentURI, starting InProgress.");
                        $shouldStart = true;
                    }
                }
                if ($shouldStart) {
                    $individualTimeMatchesUpdatePer->update(['status' => 'In Progress']);
                } else {
                    Log::info("[ScheduleJob] Class id {$individualTimeMatchesUpdatePer->id} at class_time {$individualTimeMatchesUpdatePer->class_time} did not have meeting identifiers; left as Pending.");
                }
            }



               // Group classes (group_id is NOT NULL) - handle time-based InProgress transition
            $groupTimeMatchesUpdate = ClassDetail::whereNotNull('group_id')
                ->whereDate('class_date', $today)
                ->whereTime('class_time', '<=', $nowTime)
                ->where('status', 'Pending')
                ->select('group_id', 'class_date', 'class_time', 'end_time')
                ->distinct()
            ->get();


            foreach ($groupTimeMatchesUpdate as $groupTimeMatchesUpdatePer) {
                $groupTimeMatchesUpdatePerClasses = ClassDetail::where('group_id', $groupTimeMatchesUpdatePer->group_id)
                    ->where('class_date', $groupTimeMatchesUpdatePer->class_date)
                    ->where('class_time', $groupTimeMatchesUpdatePer->class_time)
                    ->where('status', 'Pending')
                    ->get();

                foreach ($groupTimeMatchesUpdatePerClasses as $groupTimeMatchesUpdatePerClass) {
                    $shouldStart = false;

                 

                    if($groupTimeMatchesUpdatePerClass->class?->learning_tool == 'Google_Meet' || $groupTimeMatchesUpdatePerClass->class?->learning_tool == 'Google Meet'){
                        if (!empty($groupTimeMatchesUpdatePerClass->google_meet_link)) {
                            $shouldStart = true;
                        }
                    }
                    else if($groupTimeMatchesUpdatePerClass->class?->learning_tool == 'Zoom'){
                        if (!empty($groupTimeMatchesUpdatePerClass->meeting_id)) {
                            $shouldStart = true;
                        } 
                    }
                    else if($groupTimeMatchesUpdatePerClass->class?->learning_tool == 'BBB'){
                        if (!empty($groupTimeMatchesUpdatePerClass->StudentURI)) {
                            $shouldStart = true;
                        }
                    }

                    if ($shouldStart) {
                        Log::info("[ScheduleJob] Group class id {$groupTimeMatchesUpdatePerClass->id} (group {$groupTimeMatchesUpdatePerClass->group_id}) starting InProgress.");
                        $groupTimeMatchesUpdatePerClass->update(['status' => 'In Progress']);
                    } else {
                        Log::info("[ScheduleJob] Group class id {$groupTimeMatchesUpdatePerClass->id} (group {$groupTimeMatchesUpdatePerClass->group_id}) at class_time {$groupTimeMatchesUpdatePerClass->class_time} had no meeting identifiers; left Pending.");
                    }
                }
            }
            // --------------------------
            // 3) The existing end-time based Completed / Missed transition
            //    (keeps original logic but cleaned up and unified)
            // --------------------------
            // Process Individual Classes (group_id is NULL)
            $individualClasses = ClassDetail::whereNull('group_id')
                ->whereIn('status', ['Pending', 'Scheduled', 'In Progress', 'InProgress'])
            ->get();

            foreach ($individualClasses as $individualClass) {
                try {
                    // parse class date and times robustly
                    $classDate = Carbon::createFromFormat('Y-m-d', $individualClass->class_date);
                    $startTime = Carbon::parse($individualClass->class_time);
                    $endTime = Carbon::parse($individualClass->end_time);

                    // Adjust end date if end_time is earlier than start_time (spans midnight)
                    $endDate = $endTime->greaterThanOrEqualTo($startTime)
                        ? $classDate->copy()
                        : $classDate->copy()->addDay();

                    $classEndDateTime = $endDate->setTime(
                        $endTime->hour,
                        $endTime->minute,
                        $endTime->second
                    );

                    if ($now->greaterThanOrEqualTo($classEndDateTime)) {
                        // if instructor joined -> Completed else Missed
                        $status = $individualClass->is_instructor_joined ? 'Completed' : 'Missed';
                        Log::info("<!--------------------------------------------------------------------------------------------------------------------------------!>");
                        Log::info("[ScheduleJob] Individual class id {$individualClass->id} ended. Instructor joined: {$individualClass->is_instructor_joined}. Setting status: {$status}");
                        Log::info("<!-----------------------------------------------------------------------------------------------------------------------------------!>");
                        $individualClass->update(['status' => $status]);
                    }
                } catch (\Throwable $e) {
                    Log::error("Error processing individual class ID {$individualClass->id}: " . $e->getMessage());
                }
            }


               // Process Group Classes (group_id is NOT NULL) for Completed/Missed
            $groupClasses = ClassDetail::whereNotNull('group_id')
                ->whereIn('status', ['Pending', 'Scheduled', 'In Progress', 'InProgress'])
                ->select('group_id', 'class_date', 'class_time', 'end_time')
                ->distinct()
            ->get();

            foreach ($groupClasses as $groupClass) {
                try {
                    $classDate = Carbon::createFromFormat('Y-m-d', $groupClass->class_date);
                    $startTime = Carbon::parse($groupClass->class_time);
                    $endTime = Carbon::parse($groupClass->end_time);

                    $endDate = $endTime->greaterThanOrEqualTo($startTime)
                        ? $classDate->copy()
                        : $classDate->copy()->addDay();

                    $groupEndDateTime = $endDate->setTime(
                        $endTime->hour,
                        $endTime->minute,
                        $endTime->second
                    );

                    if ($now->greaterThanOrEqualTo($groupEndDateTime)) {
                        $classesGroupClass = ClassDetail::where('group_id', $groupClass->group_id)
                            ->where('class_date', $groupClass->class_date)
                            ->where('end_time', $groupClass->end_time)
                            ->get();

                        foreach ($classesGroupClass as $classesGroupClassPer) {
                            $status = $classesGroupClassPer->is_instructor_joined ? 'Completed' : 'Missed';
                            Log::info("<!----------------------------------------------------------------------------------------------------------------------------------------------------------!>");
                            Log::info("[ScheduleJob] Group class id {$classesGroupClassPer->id} (group {$groupClass->group_id}) ended. Instructor joined: {$classesGroupClassPer->is_instructor_joined}. Setting status: {$status}");
                            Log::info("<!----------------------------------------------------------------------------------------------------------------------------------------------------------------------!>");
                            $classesGroupClassPer->update(['status' => $status]);
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error("Error processing group {$groupClass->group_id}: " . $e->getMessage());
                }
            }
        

        } catch (\Throwable $e) {
             Log::error('[ScheduleJob] Unexpected error in check_and_update_class_statuses: ' . $e->getMessage());
        }
      
        Log::info('[ScheduleJob] check_and_update_class_statuses finished at ' . now());
    }






    // public function check_and_update_class_statuses(){
    //      Log::info('[ScheduleJob] check_and_update_class_statuses started at '.now());
    //     $now = Carbon::now();
    //     // Process Individual Classes (group_id is NULL)
    //     $individualClasses = ClassDetail::whereNull('group_id')
    //         ->whereIn('status', ['Pending', 'Scheduled','In Progress'])
    //         ->get();
    //     foreach ($individualClasses as $class) {
    //         try {
    //             // Parse class date and time
    //             $classDate = Carbon::createFromFormat('Y-m-d', $class->class_date);
    //             $startTime = Carbon::createFromFormat('H:i:s', $class->class_time);
    //             $endTime = Carbon::createFromFormat('H:i:s', $class->end_time);
               
    //             // Adjust end date if end_time is earlier than start_time (i.e. spans midnight)
    //             $endDate = $endTime->greaterThanOrEqualTo($startTime)
    //             ? $classDate->copy()
    //             : $classDate->copy()->addDay();

             
    //             $classEndDateTime = $endDate->setTime(
    //                 $endTime->hour,
    //                 $endTime->minute,
    //                 $endTime->second
    //             );

                   
    //             if ($now >= $classEndDateTime) {
                   
    //                 $status = $class->is_instructor_joined ? 'Completed' : 'Missed';
    //                 Log::info("[ScheduleJob] check_and_update_class_statuses Class Time Ended. Instructor Joined {$class->is_instructor_joined}. Class Status {$status}");
    //                 $class->update(['status' => $status]);
    //             }
              
    //         } catch (\Throwable $th) {
    //             Log::error("Error processing individual class ID {$class->id}: " . $e->getMessage());
    //         }
    //     }

    //      // Process Group Classes (group_id is NOT NULL)
    //     $groups = ClassDetail::whereNotNull('group_id')
    //         ->whereIn('status', ['Pending', 'Scheduled','In Progress'])
    //         ->select('group_id', 'class_date', 'class_time','end_time')
    //         ->distinct()
    //         ->get();

     
    //     foreach ($groups as $group) {
    //         try {
               
    //             $classDate = Carbon::createFromFormat('Y-m-d', $group->class_date);
    //             $startTime = Carbon::createFromFormat('H:i:s', $group->class_time);
    //             $endTime = Carbon::createFromFormat('H:i:s', $group->end_time);

                 
    //             $endDate = $endTime->greaterThanOrEqualTo($startTime)
    //                 ? $classDate->copy()
    //                 : $classDate->copy()->addDay();

                    

    //             $groupEndDateTime = $endDate->setTime(
    //                 $endTime->hour,
    //                 $endTime->minute,
    //                 $endTime->second
    //             );
    //             $groupEndDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "{$group->class_date} {$group->end_time}");
              
    //             if ($now >= $groupEndDateTime) {
    //                 $classes = ClassDetail::where('group_id', $group->group_id)
    //                     ->where('class_date', $group->class_date)
    //                     ->where('end_time', $group->end_time)
    //                     ->get();
    //                 foreach ($classes as $class) {
    //                     $status = $class->is_instructor_joined ? 'Completed' : 'Missed';
    //                      Log::info("[ScheduleJob] check_and_update_class_statuses Class Time Ended. Instructor Joined {$class->is_instructor_joined}. Class Status {$status}");
    //                     $class->update(['status' => $status]);
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Error processing group {$group->group_id}: " . $e->getMessage());
    //         }
    //     }
    // }


    // public function check_and_update_class_statuses(){
    //     $now = Carbon::now();
    //     // Process Individual Classes (group_id is NULL)
    //     $individualClasses = ClassDetail::whereNull('group_id')
    //         ->whereIn('status', ['Pending', 'Scheduled','In Progress'])
    //         ->get();
    //     foreach ($individualClasses as $class) {
    //         try {
    //             // Combine class_date and end_time
    //             $classEndDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "{$class->class_date} {$class->end_time}");

    //             if ($now >= $classEndDateTime) {
    //                 $status = $class->is_instructor_joined ? 'Completed' : 'Missed';
    
    //                 $class->update(['status' => $status]);
    //             }
    //         } catch (\Throwable $th) {
    //             Log::error("Error processing individual class ID {$class->id}: " . $e->getMessage());
    //         }
    //     }

    //      // Process Group Classes (group_id is NOT NULL)
    //     $groups = ClassDetail::whereNotNull('group_id')
    //         ->whereIn('status', ['Pending', 'Scheduled','In Progress'])
    //         ->select('group_id', 'class_date', 'class_time')
    //         ->distinct()
    //         ->get();

    //     foreach ($groups as $group) {
    //         try {
    //             $groupEndDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "{$group->class_date} {$group->end_time}");
    //             if ($now >= $groupEndDateTime) {
    //                 $classes = ClassDetail::where('group_id', $group->group_id)
    //                     ->where('class_date', $group->class_date)
    //                     ->where('end_time', $group->end_time)
    //                     ->get();
    //                 foreach ($classes as $class) {
    //                     $status = $class->is_instructor_joined ? 'Completed' : 'Missed';
    //                     $class->update(['status' => $status]);
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             Log::error("Error processing group {$group->group_id}: " . $e->getMessage());
    //         }
    //     }
    // }
    public function check_and_activate_zoom_account()
{
    
    // Add detailed logging at the start
    Log::info('Zoom Account Check Cron Started', [
        'timestamp' => now()->format('Y-m-d H:i:s'),
    ]);

        $activated = 0;
        $currentZoomAccount = ZoomSetting::where('status',1)->latest()->first();
     
        if($currentZoomAccount){
            $current_active_zoom_id = $currentZoomAccount->id;
            $current_active_zoom_minutes =  $currentZoomAccount->meeting_minutes_used;
            if((int)$current_active_zoom_minutes >= 9800  || (int)$current_active_zoom_minutes > 9800){       // Means current accounts minutes used time to shift
                  // Add detailed logging at the start
                    Log::info('Zoom Current Account Has Minutes Used Fully. Now Account Is Being Shited', [
                        'current_account_api_key' => $currentZoomAccount->api_key,
                        'current_account_sdk_key' => $currentZoomAccount->sdk_key,
                        'current_account_minutes_used' => $currentZoomAccount->meeting_minutes_used,
                        'data_type' => gettype($current_active_zoom_minutes),
                        'timestamp' => now()->format('Y-m-d H:i:s'),
                      
                    ]);
                $activated = 2;
                $getAvailableZoomAccounts = ZoomSetting::where('id','<>',$current_active_zoom_id)
                ->where('meeting_minutes_used','<',9800)->get();
                if(!empty($getAvailableZoomAccounts)){
                    foreach ($getAvailableZoomAccounts as $key => $newActiveZoomAccount) {
                        $zoomVideoSDKJWT = $newActiveZoomAccount->api_jwt_token;
                        $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
                        if(empty($zoomResult)){
                            // $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
                            Log::info('Not Able To Shift Zoom Account. Invalid or Expired Zoom Access Token', [
                                'new_account_api_key' => $newActiveZoomAccount->api_key,
                                'new_account_sdk_key' => $newActiveZoomAccount->sdk_key,
                                'new_account_minutes_used' => $newActiveZoomAccount->meeting_minutes_used,
                                'data_type' => gettype($newActiveZoomAccount->meeting_minutes_used),
                                'timestamp' => now()->format('Y-m-d H:i:s'),     
                            ]);
                        }
                        else{                      
                            if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                                // $zoomErrorMessage =__($zoomResult['message']);
                            }
                            else{
                                $currentZoomAccount->status = 0;
                                $currentZoomAccount->update();
    
                                $zoomResultArray = $zoomResult;
                                $newActiveZoomAccount->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                                $newActiveZoomAccount->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
                                $newActiveZoomAccount->status = 1;
                                $newActiveZoomAccount->update();
    
                                $activated = 1;

                                Log::info('Zoom Account Shifted Successfully', [
                                    'new_account_api_key' => $newActiveZoomAccount->api_key,
                                    'new_account_sdk_key' => $newActiveZoomAccount->sdk_key,
                                    'new_account_minutes_used' => $newActiveZoomAccount->meeting_minutes_used,
                                    'data_type' => gettype($newActiveZoomAccount->meeting_minutes_used),
                                    'timestamp' => now()->format('Y-m-d H:i:s'),        
                                ]);

                                break;
                                // dd(($zoomResultArray));
                            }       
                        }
                    }       
                }
                else{
                    Log::info('No Other Zoom Accounts Available For Shifting As All Have Miniutes Fully Used. Please Add More', [
                        'current_account_api_key' => $currentZoomAccount->api_key,
                        'current_account_sdk_key' => $currentZoomAccount->sdk_key,
                        'current_account_minutes_used' => $currentZoomAccount->meeting_minutes_used,
                        'data_type' => gettype($currentZoomAccount->meeting_minutes_used),
                        'timestamp' => now()->format('Y-m-d H:i:s'),
                    ]);
                }        
            }
            else{
                Log::info('No Shifting Yet As Current Active Zoom Account Has Still Less Than 9800 Minutes in (check_and_activate_zoom_account)', [
                    'current_account_api_key' => $currentZoomAccount->api_key,
                    'current_account_sdk_key' => $currentZoomAccount->sdk_key,
                    'current_account_minutes_used' => $currentZoomAccount->meeting_minutes_used,
                    'data_type' => gettype($currentZoomAccount->meeting_minutes_used),
                    'timestamp' => now()->format('Y-m-d H:i:s'),
                  
                ]);
            }
        }
        else{
            Log::info('No Active Zoom Account Currently Set in (check_and_activate_zoom_account)', [
                'timestamp' => now()->format('Y-m-d H:i:s'),
                
            ]);
        }
        return $activated;        
} 

public function check_and_update_zoom_account_minutes()
{
    $activated = 0;
    $activated = 2;
    $errorCount = 0;

    Log::info('Checking And Updating Zoom Account Minutes Cron Job Function Executed', [
        'timestamp' => now()->format('Y-m-d H:i:s'),  
    ]);

            $getAvailableZoomAccounts = ZoomSetting::orderBy('id','DESC')->get();
         
            if(!$getAvailableZoomAccounts->isEmpty()){
                foreach ($getAvailableZoomAccounts as $key => $newActiveZoomAccount) {
                    $zoomVideoSDKJWT = $newActiveZoomAccount->api_jwt_token;
                    // $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
                    $zoomResult = zoomCurl($zoomVideoSDKJWT);
                    if(empty($zoomResult)){
                        $errorCount++;
                        // $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
                        Log::info('Invalid Or Expired Access Token Of Following Zoom Account in ( CheckUpdateZoomMinutesFunction )', [
                            'zoom_table_id' => $newActiveZoomAccount->id,
                            'api_key' => $newActiveZoomAccount->api_key,
                            'sdk_key' => $newActiveZoomAccount->sdk_key,
                            'timestamp' => now()->format('Y-m-d H:i:s'),
                        
                        ]);
                    }
                    else{                      
                        if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                            $errorCount++;
                            // $zoomErrorMessage =__($zoomResult['message']);
                            Log::info('Something Went wrong after getting minutes Of Following Zoom Account in ( CheckUpdateZoomMinutesFunction )', [
                                'zoom_table_id' => $newActiveZoomAccount->id,
                                'api_key' => $newActiveZoomAccount->api_key,
                                'sdk_key' => $newActiveZoomAccount->sdk_key,
                                'timestamp' => now()->format('Y-m-d H:i:s'),
                            
                            ]);
                        }
                        else{
                            $zoomResultArray = $zoomResult;
                            $newActiveZoomAccount->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                            $newActiveZoomAccount->meeting_minutes_used = $zoomResultArray['Total_Minutes'];

                            // dd($newActiveZoomAccount);
                            $client = new Client();
                            $accountApiKey = $newActiveZoomAccount->api_key;
                            $accountSecretKey = $newActiveZoomAccount->api_secret;
                            $restApiJwt = $this->generateZoomRestApiJWT($accountApiKey, $accountSecretKey);
                            try {
                                $response = $client->request('GET', 'https://api.zoom.us/v2/accounts/me/plans', [
                                    'headers' => [
                                        'Authorization' => 'Bearer ' . $restApiJwt,
                                        'Content-Type' => 'application/json'
                                    ]
                                ]);
                                
                                $planInfo = json_decode($response->getBody());
                                // Get current date
                                $currentDate2 = Carbon::now();
                                // $LastRenewalDate = Carbon::parse($planInfo->plan_base->service_effective_date)->format('d M Y h:i A');
                                // $nextRenewalDate = Carbon::parse($planInfo->plan_base->next_invoice_date)->format('d M Y h:i A');
                                $LastRenewalDate = Carbon::parse($planInfo->plan_base->service_effective_date)->format('Y-m-d H:i:s');
                                $nextRenewalDate = Carbon::parse($planInfo->plan_base->next_invoice_date)->format('Y-m-d H:i:s');
                                // Get difference in days

                                $serviceDate = Carbon::parse($planInfo->plan_base->service_effective_date ?? null);
                                $diffInDays = $serviceDate->diffInDays($currentDate2);
                               
                                $newActiveZoomAccount->last_renewed_on = $LastRenewalDate;
                                $newActiveZoomAccount->days_passed = $diffInDays;
                                $newActiveZoomAccount->next_renewel_on = $nextRenewalDate;
                             
                            } 
                            catch (\Exception $e) {
                                $errorCount++;
                                Log::info('Error Occured When Checking Renewal Info Of Following Zoom Account', [
                                    'zoom_table_id' => $newActiveZoomAccount->id,
                                    'api_key' => $newActiveZoomAccount->api_key,
                                    'sdk_key' => $newActiveZoomAccount->sdk_key,
                                    'timestamp' => now()->format('Y-m-d H:i:s'),
                                
                                ]);
                            }
                          
                            $newActiveZoomAccount->update();
                            $activated = 1;
                          
                        }       
                    }
                }       
            } 
            else{
                $errorCount++;
                Log::info('No Zoom Accounts Available In Records in DB Error in (CheckUpdateZoomMinutesFunction)', [
                    'timestamp' => now()->format('Y-m-d H:i:s'),  
                ]);
            }       
  
            if($errorCount == 0){
                Log::info('All Zoom Account Minutes and Renewal Info Updated Successfully in (CheckUpdateZoomMinutesFunction)', [
                    'timestamp' => now()->format('Y-m-d H:i:s'),  
                ]);
            }
    return $activated;        
}







    public function zoomCurl($zoomVideoSDKJWT)
    {
    
        $result_array = array();
        $currentMonth = date('m');
        $currentMonth = (int)$currentMonth;
        $currentYear = date('Y');
        $currentYear = (int)$currentYear;
     
        $curl = curl_init();
        curl_setopt_array($curl, [
            // CURLOPT_URL => "https://api.zoom.us/v2/videosdk/report/daily",
            CURLOPT_URL => "https://api.zoom.us/v2/videosdk/report/daily?year=$currentYear&month=$currentMonth",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $zoomVideoSDKJWT,
          
            ],
            // CURLOPT_HTTPHEADER=>[
            //     "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IllvM18tZzFDVG4ycDMxbk4wTndsNVEiLCJleHAiOjIwNTEyMDQxMDAsImlhdCI6MTczMDExOTIwNH0.seU7u-7VUEKrMjP4d0nzVtooLdlMJq2TiW7afLNgal8",
            // ],
          ]);

          $response = curl_exec($curl);
          $err = curl_error($curl);
          
          curl_close($curl);
          $result = json_decode($response);
     
          if(isset($result->code) && isset($result->message)){
             
            $result_array = [
                'code' => $result->code,
                'message' => $result->message,
            ];
            return $result_array;
          }
          else{
            if(isset($result->dates) && !empty($result->dates)){
                $TotalSessions = 0;
                $TotalMinutes = 0;
                foreach ($result->dates as $key => $value) {
                    $TotalSessions += $value->sessions;
                    $TotalMinutes += $value->session_minutes;
                }
                $result_array = [
                    'Total_Sessions'=> $TotalSessions,
                    'Total_Minutes'=> $TotalMinutes,
                ]; 
                return $result_array;  
            }
            else{
                $result_array = [
                    'Total_Sessions'=> 0,
                    'Total_Minutes'=> 0,
                ]; 
                return $result_array;   
            }
          }
          dd($result->code);
    
    }


    function generateZoomRestApiJWT($apiKey, $apiSecret)
    {
        $token = array(
            "iss" => $apiKey,
            "exp" => time() + 3600
        );
        
        return \Firebase\JWT\JWT::encode($token, $apiSecret, 'HS256');
    }
    
   
    


     /**
     * Run raw INSERT queries to add permissions.
     *
     * Note: This executes raw SQL. Prefer seeders/migrations for repeatable deployments.
     */
    public function seedPermissions(Request $request)
    {
        // Your two INSERT statements (kept as you provided)
        $sql1 = "INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'all_licenses', 'web', '2022-12-01 07:48:19', '2022-12-01 07:48:19'), (NULL, 'add_license', 'web', '2022-12-01 07:48:19', '2022-12-01 07:48:19'), (NULL, 'manage_license', 'web', '2022-12-01 07:48:19', '2022-12-01 07:48:19');";

        $sql2 = "INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'update_license', 'web', '2022-12-01 07:48:19', '2022-12-01 07:48:19'), (NULL, 'delete_license', 'web', '2022-12-01 07:48:19', '2022-12-01 07:48:19');";

        try {
            // DB::transaction(function () use ($sql1, $sql2) {
            //     // Run each statement separately
            //     DB::statement($sql1);
            //     DB::statement($sql2);
            // });

            $names = [
                'manage_dashboard',
                'all_dashboard',
                'all_licenses',
                'add_license',
                'manage_license',
                'update_license',
                'delete_license',
            ];

            $existing = DB::table('permissions')->whereIn('name', $names)->pluck('name')->toArray();
            $toInsert = array_diff($names, $existing);

            if (!empty($toInsert)) {
                $now = now()->toDateTimeString();
                $rows = [];
                foreach ($toInsert as $n) {
                    $rows[] = [
                        'name' => $n,
                        'guard_name' => 'web',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('permissions')->insert($rows);
            }

            return response()->json([
                'success' => true,
                'message' => 'Permissions inserted successfully.'
            ], 200);

        } catch (\Exception $e) {
            // Log error and return a helpful response
            Log::error('Permission insert failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to insert permissions.',
                'error' => $e->getMessage()
            ], 500);
        }
    }





    public function deleteAllJobs()
    {
        DB::table('jobs')->truncate();  
        // Alternative:
        // DB::table('jobs')->delete();

        return "All jobs deleted successfully!";
    }




        public function deleteAllCartManagementWebsite()
    {
        DB::table('cart_management_website')->truncate();  
        // Alternative:
        // DB::table('jobs')->delete();

        return "All data deleted successfully!";
    }






          public function deleteAllDonationOrderItem()
    {
        DB::table('donation_order_items')->truncate();  
        // Alternative:
        // DB::table('jobs')->delete();

        return "All data deleted successfully!";
    }




            public function deleteAllDonationOrder()
    {
        DB::table('donation_orders')->truncate();  
     

        return "All data deleted successfully!";
    }



     public function downloadPDFChallan($cart_id = null)
    {

            $cart = CartManagement::where('id',$cart_id)->first();
            if($cart == null){
                return "Cart ID Does Not Exists!";
            }
            $givenUserID = $cart->user_id;
            $student = Student::where('user_id', $givenUserID)->first();
            if ($student == null) {
                return 'Given Student Does Not Exists.';
            }
            $student_user_id = (int)$student->user_id;
            $studentUser = User::where('id', $student_user_id)->first();


            if($cart->course){
                $NAME = $cart->course?->title;
                $course = Course::where('id',$cart->course_id);
                if ($course == null) {
                    return "Given course not found!";
                }
            }
            else if($cart->product){
                $NAME = $cart->product?->title;
            }

        
         

            // Generate PDF invoice
            $TIME = time();
            $pdfFilePath = public_path('invoices/invoice_manual_'.$cart->id.'_'.$TIME.'.pdf');

            $mpdf = new Mpdf([
                'tempDir' => public_path('mpdf-temp'),
                'default_font' => 'dejavusans',
                
            ]);

            $fullData = [];
            $fullData['year'] = $cart->year ?: date('Y', strtotime($cart->created_at));
            $fullData['id'] = $cart->id;
            $fullData['s_name'] = $studentUser->name;
            $fullData['s_gender'] = $student->gender;
            $fullData['s_parent_name'] = $student->parent_name;
            $fullData['payment_date'] = $cart->created_at;
            $fullData['month'] = $cart->month ?: date('F', strtotime($cart->created_at));
            $fullData['course_name'] = $NAME;
            $getCurrency = Currency::where('current_currency','on')->first();
            $fullData['currency'] = $getCurrency ? $getCurrency->symbol : 'Rs';

            // include students related fields
            $fullData['students_count'] = $cart->students_count;
            $fullData['amount'] = $cart->main_price; // total paid
            $fullData['course_price_1'] = (float)$cart->original_price;
            $fullData['discount'] = $cart->discount;
            $fullData['discount_type'] = $cart->discount_type;
            $fullData['net_course_price_1'] = $cart->net_original_price;
            $fullData['admission_fee'] = (float)$cart->admission_fee;
            $fullData['discount_admission_fee'] = $cart->discount_admission_fee;
            $fullData['discount_type_admission_fee'] = (float)$cart->discount_type_admission_fee;
            $fullData['net_admission_fee'] = $cart->net_admission_fee;

          


            $html = view('emails.invoice-challan', ["data" => $fullData])->render();
          
            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

              return $html;

        return "Done!";
    }



    public function regenerateDonationPDFChallans()
    {
        // Get all pending carts (adjust condition if needed)
        // $pendingCarts = CartManagement::where('payment_status', 'pending')->get();
        $pendingCarts = CartManagement::where('is_donation',1)->where('is_generated',0)->get();  //Pending Donations
  

      

      
     

        if ($pendingCarts->isEmpty()) {
            return 'No pending challans found.';
        }

        foreach ($pendingCarts as $cart) {

            // Validate cart relations
            if (!$cart->user_id) {
                echo 'userID does not exists for Cart:- '.$cart->id;
                continue;
            }

            $student = Student::where('user_id', $cart->user_id)->first();
            if (!$student) {
                 echo 'user STUDENT does not exists for Cart:- '.$cart->id;
                continue;
            }

            $studentUser = User::find($student->user_id);
            if (!$studentUser) {
                 echo 'user does not exists for Cart:- '.$cart->id;
                continue;
            }

            // Course / Product title
            if ($cart->course) {
                $NAME = $cart->course->title;
            } elseif ($cart->product) {
                $NAME = $cart->product->title;
            } else {
                 echo 'ITEM Name does not exists for Cart:- '.$cart->id;
                continue;
            }

            /** -----------------------------------
             *  DELETE PREVIOUS PDF (if exists)
             * ----------------------------------- */
            if ($cart->invoice_pdf_path && File::exists(public_path($cart->invoice_pdf_path))) {
                File::delete(public_path($cart->invoice_pdf_path));
            }

            /** -----------------------------------
             *  GENERATE NEW PDF
             * ----------------------------------- */
            $TIME = time();
            $relativePath = 'invoices/invoice_manual_' . $cart->id . '_' . $TIME . '.pdf';
            $pdfFilePath = public_path($relativePath);

            $mpdf = new Mpdf([
                'tempDir'      => public_path('mpdf-temp'),
                'default_font' => 'dejavusans',
            ]);

            $currency = Currency::where('current_currency', 'on')->first();

            $fullData = [
                'year'                         => $cart->year ?: date('Y', strtotime($cart->created_at)),
                'id'                           => $cart->id,
                's_name'                       => $studentUser->name,
                's_gender'                     => $student->gender,
                's_parent_name'                => $student->parent_name,
                'payment_date'                 => $cart->created_at,
                'month'                        => $cart->month ?: date('F', strtotime($cart->created_at)),
                'course_name'                  => $NAME,
                'currency'                     => $currency?->symbol ?? 'Rs',
                'students_count'               => $cart->students_count,
                'amount'                       => $cart->main_price,
                'course_price_1'               => (float) $cart->original_price,
                'discount'                     => $cart->discount,
                'discount_type'                => $cart->discount_type,
                'net_course_price_1'           => $cart->net_original_price,
                'admission_fee'                => (float) $cart->admission_fee,
                'discount_admission_fee'       => $cart->discount_admission_fee,
                'discount_type_admission_fee'  => (float) $cart->discount_type_admission_fee,
                'net_admission_fee'            => $cart->net_admission_fee,
            ];

            $html = view('emails.invoice-challan', ['data' => $fullData])->render();

            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

            /** -----------------------------------
             *  SAVE NEW PDF PATH
             * ----------------------------------- */
            $cart->invoice_pdf_path = $relativePath;
            $cart->is_generated = 1;
            $cart->save();
        }

        return 'All pending challans regenerated successfully.';
    }




      public function regenerateManualPDFChallans()
    {
        // Get all pending carts (adjust condition if needed)
    
         $pendingCarts = CartManagement::where('created_by_type',2)->where('is_monthly_cron',0)->where('is_generated',0)->get();  ////Manual
   

      

      
     

        if ($pendingCarts->isEmpty()) {
            return 'No pending challans found.';
        }

        foreach ($pendingCarts as $cart) {

            // Validate cart relations
            if (!$cart->user_id) {
                echo 'userID does not exists for Cart:- '.$cart->id;
                continue;
            }

            $student = Student::where('user_id', $cart->user_id)->first();
            if (!$student) {
                 echo 'user STUDENT does not exists for Cart:- '.$cart->id;
                continue;
            }

            $studentUser = User::find($student->user_id);
            if (!$studentUser) {
                 echo 'user does not exists for Cart:- '.$cart->id;
                continue;
            }

            // Course / Product title
            if ($cart->course) {
                $NAME = $cart->course->title;
            } elseif ($cart->product) {
                $NAME = $cart->product->title;
            } else {
                 echo 'ITEM Name does not exists for Cart:- '.$cart->id;
                continue;
            }

            /** -----------------------------------
             *  DELETE PREVIOUS PDF (if exists)
             * ----------------------------------- */
            if ($cart->invoice_pdf_path && File::exists(public_path($cart->invoice_pdf_path))) {
                File::delete(public_path($cart->invoice_pdf_path));
            }

            /** -----------------------------------
             *  GENERATE NEW PDF
             * ----------------------------------- */
            $TIME = time();
            $relativePath = 'invoices/invoice_manual_' . $cart->id . '_' . $TIME . '.pdf';
            $pdfFilePath = public_path($relativePath);

            $mpdf = new Mpdf([
                'tempDir'      => public_path('mpdf-temp'),
                'default_font' => 'dejavusans',
            ]);

            $currency = Currency::where('current_currency', 'on')->first();

            $fullData = [
                'year'                         => $cart->year ?: date('Y', strtotime($cart->created_at)),
                'id'                           => $cart->id,
                's_name'                       => $studentUser->name,
                's_gender'                     => $student->gender,
                's_parent_name'                => $student->parent_name,
                'payment_date'                 => $cart->created_at,
                'month'                        => $cart->month ?: date('F', strtotime($cart->created_at)),
                'course_name'                  => $NAME,
                'currency'                     => $currency?->symbol ?? 'Rs',
                'students_count'               => $cart->students_count,
                'amount'                       => $cart->main_price,
                'course_price_1'               => (float) $cart->original_price,
                'discount'                     => $cart->discount,
                'discount_type'                => $cart->discount_type,
                'net_course_price_1'           => $cart->net_original_price,
                'admission_fee'                => (float) $cart->admission_fee,
                'discount_admission_fee'       => $cart->discount_admission_fee,
                'discount_type_admission_fee'  => (float) $cart->discount_type_admission_fee,
                'net_admission_fee'            => $cart->net_admission_fee,
            ];

            $html = view('emails.invoice-challan', ['data' => $fullData])->render();

            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

            /** -----------------------------------
             *  SAVE NEW PDF PATH
             * ----------------------------------- */
            $cart->invoice_pdf_path = $relativePath;
            $cart->is_generated = 1;
            $cart->save();
        }

        return 'All pending challans regenerated successfully.';
    }





      public function regenerateOverDuePDFChallans()
    {
        // Get all pending carts (adjust condition if needed)
    
        $pendingCarts = CartManagement::where('request_enrollment',1)->where('is_donation',0)->where('is_generated',0)->get();  ////Overdue

      

      
     

        if ($pendingCarts->isEmpty()) {
            return 'No pending challans found.';
        }

        foreach ($pendingCarts as $cart) {

            // Validate cart relations
            if (!$cart->user_id) {
                echo 'userID does not exists for Cart:- '.$cart->id;
                continue;
            }

            $student = Student::where('user_id', $cart->user_id)->first();
            if (!$student) {
                 echo 'user STUDENT does not exists for Cart:- '.$cart->id;
                continue;
            }

            $studentUser = User::find($student->user_id);
            if (!$studentUser) {
                 echo 'user does not exists for Cart:- '.$cart->id;
                continue;
            }

            // Course / Product title
            if ($cart->course) {
                $NAME = $cart->course->title;
            } elseif ($cart->product) {
                $NAME = $cart->product->title;
            } else {
                 echo 'ITEM Name does not exists for Cart:- '.$cart->id;
                continue;
            }

            /** -----------------------------------
             *  DELETE PREVIOUS PDF (if exists)
             * ----------------------------------- */
            if ($cart->invoice_pdf_path && File::exists(public_path($cart->invoice_pdf_path))) {
                File::delete(public_path($cart->invoice_pdf_path));
            }

            /** -----------------------------------
             *  GENERATE NEW PDF
             * ----------------------------------- */
            $TIME = time();
            $relativePath = 'invoices/invoice_manual_' . $cart->id . '_' . $TIME . '.pdf';
            $pdfFilePath = public_path($relativePath);

            $mpdf = new Mpdf([
                'tempDir'      => public_path('mpdf-temp'),
                'default_font' => 'dejavusans',
            ]);

            $currency = Currency::where('current_currency', 'on')->first();

            $fullData = [
                'year'                         => $cart->year ?: date('Y', strtotime($cart->created_at)),
                'id'                           => $cart->id,
                's_name'                       => $studentUser->name,
                's_gender'                     => $student->gender,
                's_parent_name'                => $student->parent_name,
                'payment_date'                 => $cart->created_at,
                'month'                        => $cart->month ?: date('F', strtotime($cart->created_at)),
                'course_name'                  => $NAME,
                'currency'                     => $currency?->symbol ?? 'Rs',
                'students_count'               => $cart->students_count,
                'amount'                       => $cart->main_price,
                'course_price_1'               => (float) $cart->original_price,
                'discount'                     => $cart->discount,
                'discount_type'                => $cart->discount_type,
                'net_course_price_1'           => $cart->net_original_price,
                'admission_fee'                => (float) $cart->admission_fee,
                'discount_admission_fee'       => $cart->discount_admission_fee,
                'discount_type_admission_fee'  => (float) $cart->discount_type_admission_fee,
                'net_admission_fee'            => $cart->net_admission_fee,
            ];

            $html = view('emails.invoice-challan', ['data' => $fullData])->render();

            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfFilePath, \Mpdf\Output\Destination::FILE);

            /** -----------------------------------
             *  SAVE NEW PDF PATH
             * ----------------------------------- */
            $cart->invoice_pdf_path = $relativePath;
            $cart->is_generated = 1;
            $cart->save();
        }

        return 'All pending challans regenerated successfully.';
    }

}
