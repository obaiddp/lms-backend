<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Course;
use App\Models\Course_lecture;
use App\Models\Course_lesson;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\User;
use App\Models\ClassDetail;
use App\Models\Student;
use App\Models\CartManagement;
use App\Models\Lesson;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\General;
use Illuminate\Support\Facades\View;
use Firebase\JWT\JWT;

use Firebase\JWT\Key;
use GuzzleHttp\Client;
use App\Models\ZoomSetting;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    use General;

    public function testFunction(){
        $today_date = date('Y-m-d');

        // Log::info('Zoom Account Check Cron Started111', [
        //     'timestamp' => now()->format('Y-m-d H:i:s'),
        //     'timezone' => config('app.timezone')
        // ]);

        // $getRecords = ClassDetail::where('class_id',411)->where('class_date','>=',$today_date)->get();
        // if(!empty($getRecords)){
        //     // dd($getRecords);
        //     foreach ($getRecords as $key => $value) {
        //         $value->meeting_id = null;
        //         $value->InstructorURI = null;
        //         $value->StudentURI = null;
        //         $value->AcademicURI = null;
        //         $value->is_instructor_joined = 0;
        //         $value->is_coordinator_joined = 0;
        //         $value->update();
        //     }  
        // }

        // $template_name = 'class-schedule-student';
        // $viewData = [
        //     '{{USER_NAME}}' => $userName,
        //     '{{TOTAL_LESSON}}' => $totalClass,
        //     '{{START_DATE}}' => $starting_date,
        //     '{{END_DATE}}' => $ending_date,
        //     '{{EMAIL}}' => $userEmail,
        //     '{{INSTRUCTOR_NAME}}' => $instructorName,
        //     '{{CLASS_DAYS}}' => $days_name,
        //     '{{CLASS_TIME}}' => $updated_12h_time,
        //     '{{BASE_URL}}' => env('APP_URL'),
        // ];
     


    //     $template_name = 'class-schedule-instructor';
    //     $viewData = [
    //        '{{USER_NAME}}' => 'CEF Online',
    //        '{{TOTAL_LESSON}}' => 2,
    //        '{{START_DATE}}' => '30-01-2025',
    //        '{{END_DATE}}' => '06-02-2025',
    //        '{{EMAIL}}' => $userEmail,
    //        '{{STUDENT_NAMES}}' => 'Anum Lahore',
    //        '{{CLASS_DAYS}}' => 'Thursday',
    //        '{{CLASS_TIME}}' => '04:10 PM',
    //        '{{CLASS_TYPE}}' => 'Group',
    //        '{{LEARNING_TOOL}}' => 'Zoom',
    //        '{{BASE_URL}}' => env('APP_URL'),
    //    ];



    // $template_name = 'Payment-create-student';
    // $viewData = [
    //     '{{USER_NAME}}' => 'CEF Online',
    //     '{{AMOUNT}}' => 1500,
    //     '{{COURSE_NAME}}' => 'Tajweed ul Quran the easy way (English)',
    // ];




  
    

        // $userEmail = 'nouman.hmnt@gmail.com';
        // $template_name = 'student-sign-up-with-trial';
        // $viewData = [
        //     '{{USER_NAME}}' => 'CEF Online2',
        //     '{{EMAIL}}' => $userEmail,
        //     '{{PASSWORD}}' => 123456,
        // ];
       
    
    $userEmail = 'nouman.hmnt@gmail.com';
    $template_name = 'student-sign-up';
    $viewData = [
        '{{USER_NAME}}' => 'CEF Online',
        '{{EMAIL}}' => $userEmail,
        '{{PASSWORD}}' => 1234567,
    ];
        $response =sendEmailNotification($viewData,$userEmail,$template_name);
        dd("Done50");
    }




       public function index222(Request $request)
    {
        // base query - unpaid according to your rule:
        $base = CartManagement::query()
            ->where('is_monthly_cron', 1)
            ->where('request_enrollment', 1)
            ->with(['user', 'student']);

        // filters from UI (optional)
        if ($request->filled('month')) {
            $base->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $base->where('year', $request->year);
        }

        $items = $base->get();

        $now = Carbon::now();

        // compute totals and buckets
        $totalOutstanding = $items->sum('main_price');
        $studentsWithDues = $items->pluck('user_id')->unique()->count();

        // detailed rows
        $rows = $items->map(function ($row) use ($now) {
            $daysOverdue = $now->diffInDays($row->created_at);
            $student = $row->student;
            $user = $row->user;

            return [
                'id' => $row->id,
                'user_id' => $row->user_id,
                'student_name' => $student ? trim($student->first_name . ' ' . $student->last_name) : ($user->name ?? '—'),
                'email' => $user->email ?? null,
                'phone' => $user->phone ?? null,
                'roll_no' => $student->roll_no ?? null,
                'class' => $student->class ?? null,
                'due_date' => $row->created_at->format('Y-m-d'),
                'days_overdue' => $daysOverdue,
                'amount' => (float) $row->main_price,
                'raw' => $row,
            ];
        });

        // buckets: 1-7, 8-15, 16-30, 30+
        $buckets = [
            '1_7' => ['count'=>0,'amount'=>0],
            '8_15' => ['count'=>0,'amount'=>0],
            '16_30' => ['count'=>0,'amount'=>0],
            '30_plus' => ['count'=>0,'amount'=>0],
        ];

        foreach ($rows as $r) {
            $d = $r['days_overdue'];
            if ($d <= 7) {
                $buckets['1_7']['count']++;
                $buckets['1_7']['amount'] += $r['amount'];
            } elseif ($d <= 15) {
                $buckets['8_15']['count']++;
                $buckets['8_15']['amount'] += $r['amount'];
            } elseif ($d <= 30) {
                $buckets['16_30']['count']++;
                $buckets['16_30']['amount'] += $r['amount'];
            } else {
                $buckets['30_plus']['count']++;
                $buckets['30_plus']['amount'] += $r['amount'];
            }
        }

        $criticalCount = $buckets['30_plus']['count'];
        $criticalAmount = $buckets['30_plus']['amount'];

        return view('overdue.index', [
            'totalOutstanding' => $totalOutstanding,
            'studentsWithDues' => $studentsWithDues,
            'criticalCount' => $criticalCount,
            'criticalAmount' => $criticalAmount,
            'buckets' => $buckets,
            'rows' => $rows,
        ]);
    }

    /**
     * Send a reminder (placeholder). You can plug in email/WhatsApp logic here.
     */
    public function sendReminder(Request $request, $cartId)
    {
        $row = CartManagement::with(['user','student'])->findOrFail($cartId);
        $user = $row->user;

        // example: send an email using a mailable (create one if you want)
        // Mail::to($user->email)->send(new \App\Mail\FeeReminderMail($row));

        // For demo: record that reminder was sent by adding a column or logging.
        // We'll return success JSON and pretend we sent it.

        return response()->json([
            'success' => true,
            'message' => 'Reminder sent (placeholder). Integrate email/WhatsApp here.',
            'cart_id' => $row->id,
        ]);
    }


    // public function dashboard()
    // {
    
    //     $data['title'] = 'Dashboard';
    //     $data['total_admins'] = User::whereRole(1)->count();
    //     $data['total_instructors'] = User::whereRole(2)->count();
    //     $data['total_coordinators'] = User::whereRole(4)->count();
    //     // $data['total_students'] = User::whereNotIn('role', [1])->count();
    //     // $data['total_students'] = User::whereNotIn('role', [1])->whereNotIn('role', [4])->count();
    //     $data['total_students'] = User::whereNotIn('role', [5,4,2,1])->count();
    //     $data['total_enrolments'] = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
    //         $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
    //     })->count();
    //     $data['total_general_courses'] = Course::Where('course_type',1)->orWhere('course_type',2)->count();
    //     $data['total_standard_courses'] = Course::Where('course_type',3)->count();
    //     $data['total_pending_courses'] = Course::where('status', 0)->count();
    //     $data['total_active_courses'] = Course::where('status', 1)->count();
    //     $data['total_review_pending_courses'] = Course::where('status', 2)->count();
    //     $data['total_hold_courses'] = Course::where('status', 3)->count();
    //     $data['total_draft_courses'] = Course::where('status', 4)->count();
    //     $data['total_free_courses'] = Course::where('learner_accessibility', 'free')->count();
    //     $data['total_paid_courses'] = Course::where('learner_accessibility', 'paid')->count();
    //     $data['total_lessons'] = Course_lesson::count();
    //     $data['total_lectures'] = Course_lecture::count();
    //     $data['total_blogs'] = Blog::count();
    //     $data['total_paid_sales'] = Order::where('payment_status', 'paid')->sum('grand_total');
    //     $data['total_free_sales'] = Order::where('payment_status', 'free')->count();

    //     $data['total_platform_charge'] = Order::where('payment_status', 'paid')->sum('platform_charge');
    //     $data['total_platform_charge_this_month'] = Order::where('payment_status', 'paid')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('platform_charge');

    //     $total_order_admin_commission = Order::where('payment_status', 'paid')->withCount([
    //         'items as total_admin_commission' => function ($q) {
    //             $q->select(DB::raw('SUM(admin_commission)'));
    //         }
    //     ])->get();
    //     $data['total_admin_commission'] = $total_order_admin_commission->sum('total_admin_commission');

    //     $total_order_admin_commission_this_month = Order::where('payment_status', 'paid')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->withCount([
    //         'items as total_admin_commission' => function ($q) {
    //             $q->select(DB::raw('SUM(admin_commission)'));
    //         }
    //     ])->get();
    //     $data['total_admin_commission_this_month'] = $total_order_admin_commission_this_month->sum('total_admin_commission');

    //     //Start:: Revenue & Withdraw
    //     $total_admin_commission = Order::where('payment_status', 'paid')->withCount([
    //         'items as total_admin_commission' => function ($q) {
    //             $q->select(DB::raw("SUM(admin_commission)"));
    //         },
    //     ])->get();

    //     $data['total_revenue'] = $total_admin_commission->sum(function ($q) {
    //         return $q->platform_charge + $q->total_admin_commission;
    //     });

    //     $data['total_new_withdraws'] = Withdraw::whereStatus(0)->sum('amount');
    //     $data['total_complete_withdraws'] = Withdraw::whereStatus(1)->sum('amount');
    //     //End:: Revenue & Withdraw

    //     //Start:: Chart
    //     $totalMonths = collect([]);
    //     $totalMonthlyEnroll = collect([]);
    //     Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
    //         $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
    //     })->select(DB::raw('COUNT(id) as total'), DB::raw('MONTHNAME(created_at) month'))
    //         ->groupby('month')
    //         ->get()
    //         ->map(function ($q) use ($totalMonths, $totalMonthlyEnroll) {
    //             $totalMonths->push($q->month);
    //             $totalMonthlyEnroll->push($q->total);
    //     });
    //     $data['totalMonths'] = $totalMonths;
    //     $data['totalMonthlyEnroll'] = $totalMonthlyEnroll;

    //     $totalYears = collect([]);
    //     $totalYearlyEnroll = collect([]);

    //     Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
    //         $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
    //     })->select(DB::raw('COUNT(id) as total'), DB::raw('YEAR(created_at) year'))
    //     ->groupby('year')
    //     ->get()
    //     ->map(function ($q) use ($totalYears, $totalYearlyEnroll) {
    //         $totalYears->push($q->year);
    //         $totalYearlyEnroll->push($q->total);
    //     });

    //     $data['totalYears'] = $totalYears;
    //     $data['totalYearlyEnroll'] = $totalYearlyEnroll;
    //     // End:: Chart

    //     //Start:: Top Seller Chart
    //     $allName = collect([]);
    //     $allPercentage = collect([]);

    //     $total_order_item = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
    //         $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
    //     })->count();

    //     $data['TopSellersDetails'] = User::withCount([
    //         'orderItems as totalItems' => function ($q) {
    //             $q->select(DB::raw("COUNT(owner_user_id)"));
    //         }
    //     ])->orderBy('totalItems', 'desc')->take(5)->get();

    //     foreach($data['TopSellersDetails'] as $topSellersDetail)
    //     {
    //         if($total_order_item) {
    //             $percentage = (float)number_format((($topSellersDetail->totalItems / $total_order_item) * 100), 1);
    //             if ($percentage){
    //                 $allName->push(@$topSellersDetail->instructor->full_name ?? $topSellersDetail->name);
    //                 $allPercentage->push($percentage);
    //             }
    //         }
    //     }

    //     $data['allName'] = $allName;
    //     $data['allPercentage'] = $allPercentage;

    //     //End:: Top Seller Chart
    //     $checkUpdate =check_and_update_zoom_account_minutes();
        
    //     // Start:: Top courses & Requested Withdrawal
    //     $courseIds = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
    //         $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
    //     })->pluck('course_id')->toArray();
    //     $data['total_ten_courses'] = Course::whereIn('id', $courseIds)->withCount([
    //         'orderItems as totalOrder' => function ($q) {
    //             $q->select(DB::raw("COUNT(course_id)"));
    //         }
    //     ])->orderBy('totalOrder', 'desc')->take(10)->get();

    //     $data['withdraws'] = Withdraw::whereStatus(0)->orderBy('id', 'DESC')->paginate(20);
    //     // End:: Top courses & Requested Withdrawal

    //     // $checkActivate =$this->check_and_activate_zoom_account();
    //     $checkActivate =check_and_activate_zoom_account();
    //     // dd($checkActivate);
    //     if($checkActivate == 2){ // Means No Available Future Accounts
    //         $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
    //     }
    //     else if($checkActivate == 1){  // Activated
    //         $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
    //     }
    //     else{
    //         // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
    //     }



    //     $zoomErrorMessage = null;
    //     $zoomResultArray = null;
    //     $zoomSliderPercentage = 0;
    //     // $zoomVideoSDKJWT = $this->generateZoomToken();
    //     $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //     if($zoomSettings != null){
    //     $zoomVideoSDKJWT = $zoomSettings->api_jwt_token;
    //     $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
    //     if(empty($zoomResult)){
    //         $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
    //     }
    //     else{
          
    //         if(isset($zoomResult['code']) && isset($zoomResult['message'])){
    //             $zoomErrorMessage =__($zoomResult['message']);
    //         }
    //         else{
    //             $zoomResultArray = $zoomResult;
    //             $zoomSliderPercentage = $this->showZoomSlider($zoomResultArray['Total_Minutes'],10000);

    //             $zoomSettings->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
    //             $zoomSettings->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
    //             $zoomSettings->update();
    //         }
           
    //     }
    //     }
    //     else{
    //         $zoomErrorMessage =__('Zoom Account Is Disabled');
    //     }

 
    //     $data['zoomErrorMessage'] = $zoomErrorMessage;
    //     $data['zoomSliderPercentage'] = $zoomSliderPercentage;
    //     $data['zoomResultArray'] = $zoomResultArray;
   
      

    //     return view('admin.dashboard', $data);
    // }








     public function dashboardBackUp()
    {
    
        $data['title'] = 'Dashboard';
        $data['total_admins'] = User::whereRole(1)->count();
        $data['total_instructors'] = User::whereRole(2)->count();
        $data['total_coordinators'] = User::whereRole(4)->count();
        // $data['total_students'] = User::whereNotIn('role', [1])->count();
        // $data['total_students'] = User::whereNotIn('role', [1])->whereNotIn('role', [4])->count();
        $data['total_students'] = User::whereNotIn('role', [5,4,2,1])->count();
        $data['total_enrolments'] = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->count();
        $data['total_general_courses'] = Course::Where('course_type',1)->orWhere('course_type',2)->count();
        $data['total_standard_courses'] = Course::Where('course_type',3)->count();
        $data['total_pending_courses'] = Course::where('status', 0)->count();
        $data['total_active_courses'] = Course::where('status', 1)->count();
        $data['total_review_pending_courses'] = Course::where('status', 2)->count();
        $data['total_hold_courses'] = Course::where('status', 3)->count();
        $data['total_draft_courses'] = Course::where('status', 4)->count();
        $data['total_free_courses'] = Course::where('learner_accessibility', 'free')->count();
        $data['total_paid_courses'] = Course::where('learner_accessibility', 'paid')->count();
        $data['total_lessons'] = Course_lesson::count();
        $data['total_lectures'] = Course_lecture::count();
        $data['total_blogs'] = Blog::count();
        $data['total_paid_sales'] = Order::where('payment_status', 'paid')->sum('grand_total');
        $data['total_free_sales'] = Order::where('payment_status', 'free')->count();

        $data['total_platform_charge'] = Order::where('payment_status', 'paid')->sum('platform_charge');
        $data['total_platform_charge_this_month'] = Order::where('payment_status', 'paid')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('platform_charge');

        $total_order_admin_commission = Order::where('payment_status', 'paid')->withCount([
            'items as total_admin_commission' => function ($q) {
                $q->select(DB::raw('SUM(admin_commission)'));
            }
        ])->get();
        $data['total_admin_commission'] = $total_order_admin_commission->sum('total_admin_commission');

        $total_order_admin_commission_this_month = Order::where('payment_status', 'paid')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->withCount([
            'items as total_admin_commission' => function ($q) {
                $q->select(DB::raw('SUM(admin_commission)'));
            }
        ])->get();
        $data['total_admin_commission_this_month'] = $total_order_admin_commission_this_month->sum('total_admin_commission');

        //Start:: Revenue & Withdraw
        $total_admin_commission = Order::where('payment_status', 'paid')->withCount([
            'items as total_admin_commission' => function ($q) {
                $q->select(DB::raw("SUM(admin_commission)"));
            },
        ])->get();

        $data['total_revenue'] = $total_admin_commission->sum(function ($q) {
            return $q->platform_charge + $q->total_admin_commission;
        });

        $data['total_new_withdraws'] = Withdraw::whereStatus(0)->sum('amount');
        $data['total_complete_withdraws'] = Withdraw::whereStatus(1)->sum('amount');
        //End:: Revenue & Withdraw

        //Start:: Chart
        $totalMonths = collect([]);
        $totalMonthlyEnroll = collect([]);
        Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->select(DB::raw('COUNT(id) as total'), DB::raw('MONTHNAME(created_at) month'))
            ->groupby('month')
            ->get()
            ->map(function ($q) use ($totalMonths, $totalMonthlyEnroll) {
                $totalMonths->push($q->month);
                $totalMonthlyEnroll->push($q->total);
        });
        $data['totalMonths'] = $totalMonths;
        $data['totalMonthlyEnroll'] = $totalMonthlyEnroll;

        $totalYears = collect([]);
        $totalYearlyEnroll = collect([]);

        Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->select(DB::raw('COUNT(id) as total'), DB::raw('YEAR(created_at) year'))
        ->groupby('year')
        ->get()
        ->map(function ($q) use ($totalYears, $totalYearlyEnroll) {
            $totalYears->push($q->year);
            $totalYearlyEnroll->push($q->total);
        });

        $data['totalYears'] = $totalYears;
        $data['totalYearlyEnroll'] = $totalYearlyEnroll;
        // End:: Chart

        //Start:: Top Seller Chart
        $allName = collect([]);
        $allPercentage = collect([]);

        $total_order_item = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->count();

        $data['TopSellersDetails'] = User::withCount([
            'orderItems as totalItems' => function ($q) {
                $q->select(DB::raw("COUNT(owner_user_id)"));
            }
        ])->orderBy('totalItems', 'desc')->take(5)->get();

        foreach($data['TopSellersDetails'] as $topSellersDetail)
        {
            if($total_order_item) {
                $percentage = (float)number_format((($topSellersDetail->totalItems / $total_order_item) * 100), 1);
                if ($percentage){
                    $allName->push(@$topSellersDetail->instructor->full_name ?? $topSellersDetail->name);
                    $allPercentage->push($percentage);
                }
            }
        }

        $data['allName'] = $allName;
        $data['allPercentage'] = $allPercentage;

        //End:: Top Seller Chart
        $checkUpdate =check_and_update_zoom_account_minutes();
        
        // Start:: Top courses & Requested Withdrawal
        $courseIds = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->pluck('course_id')->toArray();
        $data['total_ten_courses'] = Course::whereIn('id', $courseIds)->withCount([
            'orderItems as totalOrder' => function ($q) {
                $q->select(DB::raw("COUNT(course_id)"));
            }
        ])->orderBy('totalOrder', 'desc')->take(10)->get();

        $data['withdraws'] = Withdraw::whereStatus(0)->orderBy('id', 'DESC')->paginate(20);
        // End:: Top courses & Requested Withdrawal

        // $checkActivate =$this->check_and_activate_zoom_account();
        $checkActivate =check_and_activate_zoom_account();
        // dd($checkActivate);
        if($checkActivate == 2){ // Means No Available Future Accounts
            $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
        }
        else if($checkActivate == 1){  // Activated
            $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
        }
        else{
            // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
        }



        $zoomErrorMessage = null;
        $zoomResultArray = null;
        $zoomSliderPercentage = 0;
        // $zoomVideoSDKJWT = $this->generateZoomToken();
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
        if($zoomSettings != null){
        $zoomVideoSDKJWT = $zoomSettings->api_jwt_token;
        $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
        if(empty($zoomResult)){
            $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
        }
        else{
          
            if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                $zoomErrorMessage =__($zoomResult['message']);
            }
            else{
                $zoomResultArray = $zoomResult;
                $zoomSliderPercentage = $this->showZoomSlider($zoomResultArray['Total_Minutes'],10000);

                $zoomSettings->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                $zoomSettings->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
                $zoomSettings->update();
            }
           
        }
        }
        else{
            $zoomErrorMessage =__('Zoom Account Is Disabled');
        }

 
        $data['zoomErrorMessage'] = $zoomErrorMessage;
        $data['zoomSliderPercentage'] = $zoomSliderPercentage;
        $data['zoomResultArray'] = $zoomResultArray;
   
      

        return view('admin.dashboard', $data);
    }








    public function dashboard(Request $request)
    {
        $data['title'] = 'Dashboard';
        $data['total_admins'] = User::whereRole(1)->count();
        $data['total_instructors'] = User::whereRole(2)->count();
        $data['total_coordinators'] = User::whereRole(4)->count();
        $data['total_students'] = User::whereNotIn('role', [5,4,2,1])->count();
        $data['total_enrolments'] = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->count();
        $data['total_general_courses'] = Course::Where('course_type',1)->orWhere('course_type',2)->count();
        $data['total_standard_courses'] = Course::Where('course_type',3)->count();
        $data['total_pending_courses'] = Course::where('status', 0)->count();
        $data['total_active_courses'] = Course::where('status', 1)->count();
        $data['total_review_pending_courses'] = Course::where('status', 2)->count();
        $data['total_hold_courses'] = Course::where('status', 3)->count();
        $data['total_draft_courses'] = Course::where('status', 4)->count();
        $data['total_free_courses'] = Course::where('learner_accessibility', 'free')->count();
        $data['total_paid_courses'] = Course::where('learner_accessibility', 'paid')->count();
        $data['total_lessons'] = Course_lesson::count();
        $data['total_lectures'] = Course_lecture::count();
        $data['total_blogs'] = Blog::count();
        $data['total_paid_sales'] = Order::where('payment_status', 'paid')->sum('grand_total');
        $data['total_free_sales'] = Order::where('payment_status', 'free')->count();

        $data['total_platform_charge'] = Order::where('payment_status', 'paid')->sum('platform_charge');
        $data['total_platform_charge_this_month'] = Order::where('payment_status', 'paid')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('platform_charge');

        $total_order_admin_commission = Order::where('payment_status', 'paid')->withCount([
            'items as total_admin_commission' => function ($q) {
                $q->select(DB::raw('SUM(admin_commission)'));
            }
        ])->get();
        $data['total_admin_commission'] = $total_order_admin_commission->sum('total_admin_commission');

        $total_order_admin_commission_this_month = Order::where('payment_status', 'paid')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->withCount([
            'items as total_admin_commission' => function ($q) {
                $q->select(DB::raw('SUM(admin_commission)'));
            }
        ])->get();
        $data['total_admin_commission_this_month'] = $total_order_admin_commission_this_month->sum('total_admin_commission');

        //Start:: Revenue & Withdraw
        $total_admin_commission = Order::where('payment_status', 'paid')->withCount([
            'items as total_admin_commission' => function ($q) {
                $q->select(DB::raw("SUM(admin_commission)"));
            },
        ])->get();

        $data['total_revenue'] = $total_admin_commission->sum(function ($q) {
            return $q->platform_charge + $q->total_admin_commission;
        });

        $data['total_new_withdraws'] = Withdraw::whereStatus(0)->sum('amount');
        $data['total_complete_withdraws'] = Withdraw::whereStatus(1)->sum('amount');
        //End:: Revenue & Withdraw

        //Start:: Chart (existing)
        $totalMonths = collect([]);
        $totalMonthlyEnroll = collect([]);
        $monthExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%m', created_at)"
            : 'MONTHNAME(created_at)';
        Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->select(DB::raw('COUNT(id) as total'), DB::raw("{$monthExpression} month"))
            ->groupby('month')
            ->get()
            ->map(function ($q) use ($totalMonths, $totalMonthlyEnroll) {
                $totalMonths->push($q->month);
                $totalMonthlyEnroll->push($q->total);
        });
        $data['totalMonths'] = $totalMonths;
        $data['totalMonthlyEnroll'] = $totalMonthlyEnroll;

        $totalYears = collect([]);
        $totalYearlyEnroll = collect([]);
        $yearExpression = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y', created_at)"
            : 'YEAR(created_at)';

        Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->select(DB::raw('COUNT(id) as total'), DB::raw("{$yearExpression} year"))
        ->groupby('year')
        ->get()
        ->map(function ($q) use ($totalYears, $totalYearlyEnroll) {
            $totalYears->push($q->year);
            $totalYearlyEnroll->push($q->total);
        });

        $data['totalYears'] = $totalYears;
        $data['totalYearlyEnroll'] = $totalYearlyEnroll;
        // End:: Chart

        //Start:: Top Seller Chart (existing)
        $allName = collect([]);
        $allPercentage = collect([]);

        $total_order_item = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->count();

        $data['TopSellersDetails'] = User::withCount([
            'orderItems as totalItems' => function ($q) {
                $q->select(DB::raw("COUNT(owner_user_id)"));
            }
        ])->orderBy('totalItems', 'desc')->take(5)->get();

        foreach($data['TopSellersDetails'] as $topSellersDetail)
        {
            if($total_order_item) {
                $percentage = (float)number_format((($topSellersDetail->totalItems / $total_order_item) * 100), 1);
                if ($percentage){
                    $allName->push(@$topSellersDetail->instructor->full_name ?? $topSellersDetail->name);
                    $allPercentage->push($percentage);
                }
            }
        }

        $data['allName'] = $allName;
        $data['allPercentage'] = $allPercentage;
        //End:: Top Seller Chart

        $checkUpdate = check_and_update_zoom_account_minutes();

        // ------------------------------------------------------------------
        // NEW: Overdue fee tracker (based on your cart_managements table)
        // condition: is_monthly_cron = 1 AND request_enrollment = 1
        // ------------------------------------------------------------------
        // $now = Carbon::now();

        // $overdues = CartManagement::with(['user'])
        //     // ->where('is_monthly_cron', 1)
        //     ->where('is_donation', 0)
        //     ->where('request_enrollment', 1)
        //     ->orderBy('id','DESC')
        //     ->limit(20)
        //     ->get();
   
        // // totals
        // $data['overdue_total_outstanding'] = (float) $overdues->sum('main_price');
        // $data['overdue_students_with_dues'] = $overdues->pluck('user_id')->unique()->count();

           

        // // compute rows and buckets
        // $rows = $overdues->map(function($row) use ($now) {
        //     $daysOverdue = $now->diffInDays($row->created_at);
          
        //     if($row->user){
        //         $getStudent = Student::where('user_id',$row->user->id)->first();
        //         $getCourse = Course::where('id',$row->course_id)->first();
        //         $studentName = $getStudent ? trim($getStudent->first_name . ' ' . $getStudent->last_name) : ($row->user->name ?? null);
        //         return [
        //             'id' => $row->id,
        //             'user_id' => $row->user_id,
        //             'student_uuid' => $getStudent->uuid,
        //             'student_name' => $studentName ?: '—',
        //             'course_id' => $getCourse->id,
        //             'course_name' => $getCourse->title ?: '—',
        //             'email' => $row->user->email ?? null,
        //             'phone' => $row->user->mobile_number ?? null,
        //             // 'roll_no' => $row->student->roll_no ?? null,
        //             // 'class' => $row->student->class ?? null,
        //             'issue_date' => $row->created_at ? $row->created_at->toDateString() : null,
        //             'days_overdue' => $daysOverdue,
        //             'amount' => (float) $row->main_price,
        //             'raw' => $row,
        //         ];
        //     }
        
         
        // });

        // // buckets
        // $buckets = [
        //     '1_7' => ['count' => 0, 'amount' => 0],
        //     '8_15' => ['count' => 0, 'amount' => 0],
        //     '16_30' => ['count' => 0, 'amount' => 0],
        //     '30_plus' => ['count' => 0, 'amount' => 0],
        // ];

        // foreach ($rows as $r) {
        //     $d = (int) $r['days_overdue'];
        //     if ($d <= 7) {
        //         $buckets['1_7']['count']++;
        //         $buckets['1_7']['amount'] += $r['amount'];
        //     } elseif ($d <= 15) {
        //         $buckets['8_15']['count']++;
        //         $buckets['8_15']['amount'] += $r['amount'];
        //     } elseif ($d <= 30) {
        //         $buckets['16_30']['count']++;
        //         $buckets['16_30']['amount'] += $r['amount'];
        //     } else {
        //         $buckets['30_plus']['count']++;
        //         $buckets['30_plus']['amount'] += $r['amount'];
        //     }
        // }

        // $data['overdue_buckets'] = $buckets;
        // $data['overdue_rows'] = $rows;
        // $data['overdue_critical_count'] = $buckets['30_plus']['count'];
        // $data['overdue_critical_amount'] = $buckets['30_plus']['amount'];




        $now = now();

        // Base query (you can add ->with(['user','course','student']) if relationships exist)
        $query = CartManagement::with('user')
            ->where('is_donation', 0)
            ->where('request_enrollment', 1)
            ->orderBy('id', 'DESC');

        // Optional: search on student name or user's email (simple example)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('created_at', 'LIKE', "%{$searchTerm}%")
                ->orwhere('price', 'LIKE', "%{$searchTerm}%")
                ->orWhereHas('user', function($subQuery2) use ($searchTerm) {
                        $subQuery2->where('name', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('course', function($subQuery) use ($searchTerm) {
                    $subQuery->where('title', 'LIKE', "%{$searchTerm}%");
                });  
            });
            // Add more whereHas conditions if you want to search course title, etc.
        }

        // paginate (20 per page)
        $overdues = $query->paginate(15)->appends($request->except('page'));

        // Map/transform items into the shape your view expects.
        // Note: using getCollection()->map then setCollection preserves pagination metadata.
        $mapped = $overdues->getCollection()->map(function ($row) use ($now) {
            // avoid extra queries if possible: if you have relations, eager load them
            $getStudent = \App\Models\Student::where('user_id', $row->user_id)->first();
            $getCourse  = \App\Models\Course::find($row->course_id);

            $studentName = $getStudent ? trim(($getStudent->first_name ?? '') . ' ' . ($getStudent->last_name ?? '')) : ($row->user->name ?? '—');
            $studentUuid = $getStudent ? ($getStudent->uuid ?? null) : null;
            $courseName  = $getCourse ? ($getCourse->title ?? '—') : '—';
            $courseId    = $getCourse ? $getCourse->id : null;

            $daysOverdue = $now->diffInDays($row->created_at);

            return [
                'id'              => $row->id,
                'user_id'         => $row->user_id,
                'student_uuid'    => $studentUuid,
                'student_name'    => $studentName ?: '—',
                'course_id'       => $courseId,
                'course_name'     => $courseName,
                'email'           => $row->user->email ?? null,
                'phone'           => $row->user->mobile_number ?? null,
                'issue_date'      => $row->created_at ? $row->created_at->toDateString() : null,
                'days_overdue'    => $daysOverdue,
                'amount'          => (float) $row->main_price,
                'raw'             => $row,
            ];
        });

        // Replace paginator's collection with mapped rows
        $overdues->setCollection($mapped);


        $overduesAll = CartManagement::with(['user'])
            // ->where('is_monthly_cron', 1)
            ->where('is_donation', 0)
            ->where('request_enrollment', 1)
            ->orderBy('id','DESC')
            ->get();
   
        // totals
        $overdue_total_outstanding = (float) $overduesAll->sum('main_price');
        $overdue_students_with_dues = $overduesAll->pluck('user_id')->unique()->count();

        $rowsAll = $overduesAll->map(function($row) use ($now) {
            $daysOverdue = $now->diffInDays($row->created_at);
            if($row->user){
                return [
                    'days_overdue' => $daysOverdue,
                    'amount' => (float) $row->main_price,
                ];
            }
        });
        // Buckets (1-7, 8-15, 16-30, 30+)
        $buckets = [
            '1_7' => ['count' => 0, 'amount' => 0],
            '8_15' => ['count' => 0, 'amount' => 0],
            '16_30' => ['count' => 0, 'amount' => 0],
            '30_plus' => ['count' => 0, 'amount' => 0],
        ];

        foreach ($rowsAll as $r) {
            if(!isset($r['days_overdue']) && !isset($r['amount'])){
                continue;
            }
            
            if(isset($r['days_overdue'])){
                $d = (int) $r['days_overdue'];
            }
            else{
                $d = null;
            }
            
            if ($d <= 7) {
                $buckets['1_7']['count']++;
                $buckets['1_7']['amount'] += $r['amount'];
            } elseif ($d <= 15) {
                $buckets['8_15']['count']++;
                $buckets['8_15']['amount'] += $r['amount'];
            } elseif ($d <= 30) {
                $buckets['16_30']['count']++;
                $buckets['16_30']['amount'] += $r['amount'];
            } else {
                $buckets['30_plus']['count']++;
                $buckets['30_plus']['amount'] += $r['amount'];
            }
        }

      
        $data['overdue_buckets'] = $buckets;

        $data['overdue_total_outstanding'] = $overdue_total_outstanding;
        $data['overdue_students_with_dues'] = $overdue_students_with_dues;
        $data['overdue_critical_count'] = $buckets['30_plus']['count'];
        $data['overdue_critical_amount'] = $buckets['30_plus']['amount'];
  
        // ------------------------------------------------------------------

        // Start:: Top courses & Requested Withdrawal (existing)
        $courseIds = Order_item::where('course_id', '!=', null)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')->orWhere('payment_status', 'free');
        })->pluck('course_id')->toArray();
        $data['total_ten_courses'] = Course::whereIn('id', $courseIds)->withCount([
            'orderItems as totalOrder' => function ($q) {
                $q->select(DB::raw("COUNT(course_id)"));
            }
        ])->orderBy('totalOrder', 'desc')->take(10)->get();

        $data['withdraws'] = Withdraw::whereStatus(0)->orderBy('id', 'DESC')->paginate(20);
        // End:: Top courses & Requested Withdrawal

        $checkActivate = check_and_activate_zoom_account();
        if($checkActivate == 2){
            $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
        }
        else if($checkActivate == 1){
            $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
        }

        $zoomErrorMessage = null;
        $zoomResultArray = null;
        $zoomSliderPercentage = 0;
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
        if($zoomSettings != null){
            $zoomVideoSDKJWT = $zoomSettings->api_jwt_token;
            $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
            if(empty($zoomResult)){
                $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
            }
            else{
                if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                    $zoomErrorMessage =__($zoomResult['message']);
                }
                else{
                    $zoomResultArray = $zoomResult;
                    $zoomSliderPercentage = $this->showZoomSlider($zoomResultArray['Total_Minutes'],10000);

                    $zoomSettings->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                    $zoomSettings->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
                    $zoomSettings->update();
                }
            }
        }
        else{
            $zoomErrorMessage =__('Zoom Account Is Disabled');
        }

        $data['zoomErrorMessage'] = $zoomErrorMessage;
        $data['zoomSliderPercentage'] = $zoomSliderPercentage;
        $data['zoomResultArray'] = $zoomResultArray;

        return view('admin.dashboard', $data);
    }


    // public function check_and_activate_zoom_account()
    // {
    //     $activated = 0;
    //     $currentZoomAccount = ZoomSetting::where('status',1)->latest()->first();
    //     if($currentZoomAccount){
    //         $current_active_zoom_id = $currentZoomAccount->id;
    //         $current_active_zoom_minutes =  $currentZoomAccount->meeting_minutes_used;
    //         if($current_active_zoom_minutes >= 9990){       // Means current accounts minutes used time to shift
    //             $activated = 2;
    //             $getAvailableZoomAccounts = ZoomSetting::where('id','<>',$current_active_zoom_id)
    //             ->where('meeting_minutes_used','<',9990)->get();
    //             if(!empty($getAvailableZoomAccounts)){
    //                 foreach ($getAvailableZoomAccounts as $key => $newActiveZoomAccount) {
    //                     $zoomVideoSDKJWT = $newActiveZoomAccount->api_jwt_token;
    //                     $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
    //                     if(empty($zoomResult)){
    //                         // $zoomErrorMessage =__('Invalid Or Expired Zoom Access Token');
    //                     }
    //                     else{                      
    //                         if(isset($zoomResult['code']) && isset($zoomResult['message'])){
    //                             // $zoomErrorMessage =__($zoomResult['message']);
    //                         }
    //                         else{
    //                             $currentZoomAccount->status = 0;
    //                             $currentZoomAccount->update();

    //                             $zoomResultArray = $zoomResult;
    //                             $newActiveZoomAccount->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
    //                             $newActiveZoomAccount->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
    //                             $newActiveZoomAccount->status = 1;
    //                             $newActiveZoomAccount->update();

    //                             $activated = 1;
    //                             break;
    //                             // dd(($zoomResultArray));
    //                         }       
    //                     }
    //                 }       
    //             }        
    //         }
    //     }
      
    //     return $activated;        
    // } 


    public function showZoomSlider($current, $max)
{
    $min = 0;
    // $current = 2149;
    // $max = 10000;
    // Calculate percentage based on the current value
    $percentage = ($current - $min) / ($max - $min) * 100;
    return $percentage;
    // return view('slider', compact('percentage'));
}

    public function getZoomAccountData(Request $request){
        $accessToken = $request->accessToken;
        $response = $this->zoomCurl($accessToken);
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

    public function generateZoomToken()
    {
     
        // // Retrieve API Key and Secret from environment variables
        // $apiKey = env('ZOOM_CLIENT_ID');
        // $apiSecret = env('ZOOM_CLIENT_SECRET');

        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
        $apiKey = $zoomSettings->api_key;
        $apiSecret = $zoomSettings->api_secret;
   
    
        // Set token payload
        $payload = [
            'app_key' => $apiKey,
            "role_type"=> 1,
            "tpc"=> "CEF Online",
            "version"=> 1,
            "geo_regions"=> "US,AU,CA,IN,CN,BR,MX,HK,SG,JP,DE,NL",
            'iat' => Carbon::now()->timestamp,         // Issued at time
            'exp' => Carbon::now()->addMinutes(60)->timestamp, // Expiration time (1 hour later)
            'tokenExp' => Carbon::now()->addHour()->timestamp
        ];
    
        
        // Encode the JWT using the secret
        $jwt = JWT::encode($payload, $apiSecret, 'HS256');
        return $jwt;
        // Return the JWT token as JSON
        return response()->json(['token' => $jwt]);
    }

}
