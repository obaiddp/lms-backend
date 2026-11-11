<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AffiliateHistory;
use Laravel\Socialite\Facades\Socialite;
use App\Models\BookingHistory;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\LiveClass;
use App\Models\LiveClassStudentLink;
use App\Models\Student;
use App\Models\ZoomSetting;
use App\Models\CourseInstructor;
use App\Models\Order_item;
use Illuminate\Support\Str;
use App\Models\RankingLevel;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogTag;
use App\Models\Take_exam;
use App\Models\ClassDetail;
use App\Libraries\Bbb;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\Answer;
use App\Models\Assignment;
use App\Models\AssignmentSubmit;
use App\Models\InstructorFeature;
use App\Models\Course_lecture;
use App\Models\Course_lecture_views;
use App\Models\InstructorProcedure;
use App\Models\Organization;
use App\Models\CartManagement;
use App\Models\State;
use App\Models\User;
use App\Models\Resource;
use App\Models\ResourceCategory;

use Ramsey\Uuid\Uuid;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->bb=new Bbb();
    }


    public function redirectTutorToGoogle()
{
    return Socialite::driver('google')
        ->scopes(['https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/calendar.events'])
        ->with(['access_type' => 'offline', 'prompt' => 'consent']) // 👈 Important!
        ->stateless()
        ->redirect();

    // return Socialite::driver('google')->scopes(['https://www.googleapis.com/auth/calendar'])->redirect();
}

public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    auth()->user()->update([
        'instructor_google_access_token' => $googleUser->token,
        'instructor_google_refresh_token' => $googleUser->refreshToken, // Save refresh token
        'instructor_google_token_expires_at' => now()->addSeconds($googleUser->expiresIn),
    ]);

    return redirect('instructor/dashboard');
}



    public function dashboard()
    {
        $data['title'] = 'Dashboard';
        $data['base_url'] = url('/');
        $data['instructor_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 2)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();
        //Start::  Cancel Consultation Money Calculation
        $cancelConsultationOrderItemIds = BookingHistory::where(['instructor_user_id' => Auth::id(),'status' => BOOKING_HISTORY_STATUS_CANCELLED, 'send_back_money_status' => SEND_BACK_MONEY_STATUS_YES])
            ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
            ->whereHas('order', function ($q){
            $q->where('payment_status', 'paid');
        })->pluck('order_item_id')->toArray();
        $consultationOrderItems = Order_item::whereIn('id', $cancelConsultationOrderItemIds);
        //End::  Cancel Consultation Money Calculation

        $userCourseIds = CourseInstructor::where('instructor_id', auth()->id())->select('course_id')->get()->pluck('course_id')->toArray();
        // $userCourseIds = Course::whereUserId(auth()->user()->id)->pluck('id')->toArray();

        $orderBundleItemsCount = Order_item::where('owner_user_id', Auth::id())->whereNotNull('bundle_id')->whereNull('course_id')
            ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            })->count();

        $orderItems = Order_item::whereIn('course_id', $userCourseIds)
            ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
            ->whereHas('order', function ($q) {
                $q->where('payment_status', 'paid');
            });
        //Start::Affiliate Earning from AffiliateHistory
        $affiliateHistoryTotalCommission = AffiliateHistory::where(['user_id' => Auth::id(), 'status' => AFFILIATE_HISTORY_STATUS_PAID])->sum('commission');
        //End::Affiliate Earning from AffiliateHistory

        $data['total_earning_this_month'] = $orderItems->sum('owner_balance') + $affiliateHistoryTotalCommission - $consultationOrderItems->sum('owner_balance');
        $data['total_enroll_this_month'] = $orderItems->count('id') - $orderBundleItemsCount - count($cancelConsultationOrderItemIds);

        $data['best_selling_course'] = Order_item::whereIn('course_id', $userCourseIds)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->groupBy("course_id")->selectRaw("COUNT(course_id) as total_course_id, course_id")->orderByRaw("COUNT(course_id) DESC")->first();

        $data['recentCourses'] = Course::whereIn('id', $userCourseIds)->latest()->limit(2)->get();
        $data['updatedCourses'] = Course::whereIn('id', $userCourseIds)->orderBy('updated_at', 'desc')->limit(2)->get();

        // Start:: Sale Statistics
        $months = collect([]);
        $totalPrice = collect([]);

        //Start::  Cancel Consultation Money Calculation
        $cancelConsultationOrderItemIds = BookingHistory::where(['instructor_user_id' => Auth::id(),'status' => 2, 'send_back_money_status' => SEND_BACK_MONEY_STATUS_YES])
            ->whereHas('order', function ($q){
                $q->where('payment_status', 'paid');
            })->pluck('order_item_id')->toArray();
        //End
        Order_item::whereNotIn('id', $cancelConsultationOrderItemIds)->whereIn('course_id', $userCourseIds)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        })->select(DB::raw('SUM(owner_balance) as total'), DB::raw('MONTHNAME(created_at) month'))
            ->groupby('month')
            ->get()
            ->map(function ($q) use ($months, $totalPrice) {
                $months->push($q->month);
                $totalPrice->push($q->total);
            });

        $data['months'] = $months;
        $data['totalPrice'] = $totalPrice;
        //End:: Sale Statistics

        //Start:: ranking Level
        $allOrderItems = Order_item::whereIn('course_id', $userCourseIds)->whereHas('order', function ($q) {
            $q->where('payment_status', 'paid');
        });

        $data['grand_total_earning'] = $allOrderItems->sum('owner_balance');
        $data['grand_total_enroll'] = $allOrderItems->count('id') - $orderBundleItemsCount;

        $currentLevel = get_instructor_ranking_level(auth()->user()->badges);
        
        $data['rankingLevels'] = RankingLevel::orderBy('serial_no', 'DESC')->where('type', RANKING_LEVEL_EARNING)->get();
        $data['currentLevel'] = $data['rankingLevels']->where('name', $currentLevel)->first();
        $data['secondLevel'] = $data['rankingLevels']->where('id', '>', @$data['currentLevel']->id)->first();
        //End:: ranking Level
        return view('instructor.instructorDashboard', $data);
    }

    public function home() {
        $data['base_url'] = url('/');
        return view('instructor.home',$data);
    }









    public function latestEnrolledCourses(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
     
    


        $courseInstructorIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())->join('courses', 'courses.id', '=', 'course_instructor.course_id')->select('user_packages.*')->whereNull('courses.organization_id')->where('course_instructor.status',STATUS_APPROVED)->select('course_id')->get()->pluck('course_id')->toArray();
        $mainCourseIds = Course::where('user_id', $user->id)->select('id')->pluck('id')->toArray();

        $courseInstructorIds = array_unique(array_merge($mainCourseIds, $courseInstructorIds));
        $courses = Course::whereIn('id', $courseInstructorIds)->whereNull('organization_id')->orderBy('id', 'DESC')->limit(5)->get();
     
        if(!$courses->isEmpty()){
            foreach ($courses as $key => $course) {
            
                $courseDetailLink = route('instructor-course-details',['slug'=>$course->slug]);
    
                $result_array[]=[
                    'id' => $key+1,
                    'courseID' => $course->id,
                    'courseName' => $course->title,
                    'TotalLessons' => $course->lessons->count(),
                    'TotalLessonsLectures' => $course->lectures->count(),
                    'course_DetailPage_Link' =>  $courseDetailLink,
                ];
            }
         
        }
        // dd($result_array);
        return response()->json($result_array);
    
      
    }



    public function latestBlogs(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
        $blogs = Blog::where('status',1)->orderBy('id','DESC')->limit('5')->get();
        if(!$blogs->isEmpty()){
            foreach ($blogs as $key => $blog) {
                $imagePath = getImageFile($blog->image_path);
                $blogDate = Carbon::parse($blog->created_at)->format('d-m-Y');
             
                $result_array[]=[
                    'blog_id' => $blog->id,
                    'blog_title' => $blog->title,
                    'blog_details' => $blog->details,
                    'blog_image' => $imagePath,
                    'blog_date' => $blogDate,
                ];
            }
        }
        return response()->json($result_array);
    
      
    }





    public function latestResources(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
      

        $result_array = array();
        $courseIDs = array();
        $courseInstructorIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())->join('courses', 'courses.id', '=', 'course_instructor.course_id')->select('user_packages.*')->whereNull('courses.organization_id')->where('course_instructor.status',STATUS_APPROVED)->select('course_id')->get()->pluck('course_id')->toArray();
        $mainCourseIds = Course::where('user_id', $user->id)->select('id')->pluck('id')->toArray();

        $courseInstructorIds = array_unique(array_merge($mainCourseIds, $courseInstructorIds));
        $courses = Course::whereIn('id', $courseInstructorIds)->whereNull('organization_id')->orderBy('id', 'DESC')->get();

        if(!$courses->isEmpty()){
            foreach ($courses as $key => $course) {
                $courseIDs[] = $course->id;
            }
            $courseIDs = array_unique($courseIDs);
        }

        if(!empty($courseIDs)){
            $courseLectures = Course_lecture::whereIn('course_id',$courseIDs)->where('resource_id','<>',null)->orderBy('id','DESC')->limit('10')->get();
     
            if(!$courseLectures->isEmpty()){
                foreach ($courseLectures as $key => $lecture) {
                    # code...
                
                    if( $lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube'){
                        // $lectureViewPath = route('student.resource.view_full_other',['resource_id'=>$lecture->resource_id,'lecture_id'=>$lecture->id]);
                        $lectureViewPath = route('resource.view_full_other',['resource_id'=>$lecture->resource_id]);
                    }
                    else{
                        $lectureViewPath = route('resource.view_full',['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name]);
                    }
                      
                    $result_array[]=[
                        'lecture_id' => $lecture->id,
                        'lecture_title' => $lecture->title,
                        'lecture_type' => $lecture->resource?->category?->name,
                        'lecture_view' => $lectureViewPath,
                    ];
                }
            }
          
        }
      
    
        return response()->json($result_array);
    
      
    }





    public function monthlyClasses(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
     
        $today = Carbon::today();
        $todayUpdated = $today->format('Y-m-d');
        // Date six months ago
        $sixMonthsAgo = $today->copy()->subMonths(6);
        $sixMonthsAgoUpdated = $sixMonthsAgo->format('Y-m-d');
    
         // Get last six months' start dates, names, and numbers
  
        $getInstructor = Instructor::where('user_id',$user->id)->first();
        $instructor_id = $getInstructor->id;
      
        // $getMonthlyClassHistory = ClassDetail::where('instructor_id',$instructor_id)
        // ->whereDate('class_date' ,'>=',$sixMonthsAgoUpdated)
        // ->whereDate('class_date' ,'<=',$todayUpdated)
        // ->whereIn('status',['Completed','Missed'])
        // ->orderBy('id','DESC')
        // ->orderBy('class_date','ASC')
        // ->get();
  
      

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $today->copy()->subMonths($i)->startOfMonth();
            $monthName = $date->format('F');
            $monthNumber = $date->format('m');
            $MonthCompleteClasses = ClassDetail::where('instructor_id',$instructor_id)
            ->whereDate('class_date' ,'>=',$sixMonthsAgoUpdated)
            ->whereDate('class_date' ,'<=',$todayUpdated)
            ->whereMonth('class_date', $monthNumber)
            ->where('status','Completed')
            ->orderBy('id','DESC')
            ->orderBy('class_date','ASC')
            ->groupBy('class_id')
            ->get()
            ->count();
        
            $MonthMissedClasses = ClassDetail::where('instructor_id',$instructor_id)
            ->whereDate('class_date' ,'>=',$sixMonthsAgoUpdated)
            ->whereDate('class_date' ,'<=',$todayUpdated)
            ->whereMonth('class_date', $monthNumber)
            ->where('status','Missed')
            ->orderBy('id','DESC')
            ->orderBy('class_date','ASC')
            ->groupBy('class_id')
            ->get()
            ->count();
                      
            $result_array[] = [
                'month_Number' => $date->format('m'),         // Month number (e.g., "11")
                'month_Name' => $date->format('M'),           // Full month name (e.g., "November")
                'total_Completed_Classes' => $MonthCompleteClasses,
                'total_Missed_Classes' => $MonthMissedClasses,
            ];
        }
   
        // dd($result_array);
        return response()->json($result_array);
    
      
    }






    public function TodayLiveClass(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
      
        $instructor_user_id=$user->id;
        $getInstructor = Instructor::where('user_id',$instructor_user_id)->first();
        $instructor_id = $getInstructor->id;
        $today_date = date('Y-m-d');
      
        // $getTodayLiveClass = ClassDetail::where('instructor_id',$instructor_id)->where('class_date',$today_date)
        // ->whereNotIn('status',['Completed','Missed'])
        // ->orderBy('class_date','ASC')->first();
        $getTodayLiveClass = ClassDetail::where('instructor_id',$instructor_id)->where('class_date',$today_date)
        ->whereNotIn('status',['Completed','Missed'])
        ->orderBy('class_date','ASC')
        ->orderBy('class_time','ASC')
        ->groupBy('class_id')
        ->first();
     

        if($getTodayLiveClass){
            $individual = array();
            $group = array();
            $BBB_Meeting_Details = array();
            $Zoom_Meeting_Details = array();
            $Meet_Meeting_Details = array();

            $updated_time = changetimeByZone($getTodayLiveClass->class_time,$getTodayLiveClass->instructor_id,'instructor');
            $classTime = date('d-m-Y',strtotime($getTodayLiveClass->class_date)).' '.$updated_time;
            $curdate = strtotime(date("Y-m-d"));
            $lsndate = strtotime($getTodayLiveClass->class_date); 
            $updated_cur_time = changetimeByZone(date("H:i"),$getTodayLiveClass->instructor_id,'instructor');
            $curtime = strtotime($updated_cur_time);
            $lsntime = strtotime($updated_time);
            $lesson_duration = $getTodayLiveClass->class?->duration;
            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
            $lsnendtime = $lsntimestamp;
      
        if($getTodayLiveClass->class?->type == 'Group'){        // Group Class Code
        
            if($curdate <  $lsndate) {
                $btnStatus = __('Scheduled');
            }
            elseif($curdate == $lsndate){
                if($curtime < $lsntime) {
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                }
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                }
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->InstructorURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                }
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                  
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                    $Zoom_Meeting_Details = [
                        'group_id' =>$getTodayLiveClass->group_id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'user_type' =>__('Tutor'),
                        'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                        'user_id' =>$getTodayLiveClass->instructor_id,
                        'btnEventClass'=>'create_zoom_meeting_link_2',
                        'hrefUrl' => 'javascript::void(0)',
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                    ];
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                    $BBB_Meeting_Details = [
                        'group_id' =>$getTodayLiveClass->group_id,
                        'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_bbb_group',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                  
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                    $Meet_Meeting_Details = [
                        'group_id' =>$getTodayLiveClass->group_id,
                        'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_meet_group',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                  
                }

                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                    $Zoom_Meeting_Details = [
                        'group_id' =>$getTodayLiveClass->group_id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'user_type' =>__('Tutor'),
                        'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                        'user_id' =>$getTodayLiveClass->instructor_id,
                        'btnEventClass'=>'create_zoom_meeting_link_2',
                        'hrefUrl' => 'javascript::void(0)',
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                    ];
                }
                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                    $BBB_Meeting_Details = [
                        'group_id' =>$getTodayLiveClass->group_id,
                        'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_bbb_group',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                }
                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
                    $Meet_Meeting_Details = [
                        'group_id' =>$getTodayLiveClass->group_id,
                        'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_meet_group',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                }

                else if($curtime == $lsnendtime && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class?->learning_tool == 'Zoom' && ($getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined)) {
                     
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class->learning_tool == 'Zoom' && $getTodayLiveClass->is_instructor_joined == '0') {
                        // updateLessonStatusMissedZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }
                else if($curtime == $lsnendtime && $getTodayLiveClass->class->learning_tool == 'BBB') {
                    if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class?->learning_tool == 'BBB' && ($getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined)) {
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class->learning_tool == 'BBB' && $getTodayLiveClass->is_instructor_joined == '0') {
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }
                else if($curtime == $lsnendtime && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
                    if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class?->learning_tool == 'Google_Meet' && ($getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined)) {
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class->learning_tool == 'Google_Meet' && $getTodayLiveClass->is_instructor_joined == '0') {
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }

                else if($curtime > $lsnendtime) {
                    if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Zoom' && $getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined) {	
                        // updateLessonStatusCompleteZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'BBB' && $getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined) {	
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet' && $getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined) {	
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }

                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Zoom' && $getTodayLiveClass->is_instructor_joined == '0') {	
                        // updateLessonStatusMissedZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'BBB' && $getTodayLiveClass->is_instructor_joined == '0') {	
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet' && $getTodayLiveClass->is_instructor_joined == '0') {	
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }
            }
            else{
                $btnStatus = __('Waiting');
                $btnCssClass = 'btn btn-warning m-1';
                $btnBackGround='orange';
                $btnColor='#fff';
                $btnID = 'lec_link2_'.$getTodayLiveClass->group_id;
            }

            if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'class_id'=>$getTodayLiveClass->class_id,
                    'type' => 'Zoom',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                    'Date/Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                    'Btn_Css_Class'=>$btnCssClass,
                    'Btn_BackGround'=>$btnBackGround,
                    'Btn_Color'=>$btnColor,
                    'Btn_ID'=>$btnID,
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    'Meeting_Details'=>$Zoom_Meeting_Details,
                ];
            }
            else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'class_id'=>$getTodayLiveClass->class_id,
                    'type' => 'Google_Meet',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                    'Date/Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                    'Btn_Css_Class'=>$btnCssClass,
                    'Btn_BackGround'=>$btnBackGround,
                    'Btn_Color'=>$btnColor,
                    'Btn_ID'=>$btnID,
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    'Meeting_Details'=>$Meet_Meeting_Details,
                ];
            }
            else{
                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'class_id'=>$getTodayLiveClass->class_id,
                    'type' => 'BBB',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                    'Date/Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                    'Btn_Css_Class'=>$btnCssClass,
                    'Btn_BackGround'=>$btnBackGround,
                    'Btn_Color'=>$btnColor,
                    'Btn_ID'=>$btnID,
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    'Meeting_Details'=>$BBB_Meeting_Details,
                ];
            }
        }
        else{       // Individual Class Code
            if($curdate <  $lsndate) {
                $btnStatus = __('Scheduled');
            }
            elseif($curdate == $lsndate){
                if($curtime < $lsntime) {
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                }
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                }
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->InstructorURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                }
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                    $btnStatus = __('Waiting');
                    $btnCssClass = 'btn btn-warning m-1';
                    $btnBackGround='orange';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                }

                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                    $Zoom_Meeting_Details = [
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'user_type' =>__('Tutor'),
                        'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                        'user_id' =>$getTodayLiveClass->instructor_id,
                        'btnEventClass'=>'create_zoom_meeting_link',
                        'hrefUrl' => 'javascript::void(0)',
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                    ];
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                    $BBB_Meeting_Details = [
                        'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_bbb_individual',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                  
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                    $Meet_Meeting_Details = [
                        'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_meet_individual',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                  
                }


                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                    $Zoom_Meeting_Details = [
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'user_type' =>__('Tutor'),
                        'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                        'user_id' =>$getTodayLiveClass->instructor_id,
                        'btnEventClass'=>'create_zoom_meeting_link',
                        'hrefUrl' => 'javascript::void(0)',
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                    ];
                }
                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                    $BBB_Meeting_Details = [
                        'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_bbb_individual',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                }
                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                    $btnStatus = __('Start Class');
                    $btnCssClass = 'btn btn-info m-1';
                    $btnBackGround='#499619';
                    $btnColor='#fff';
                    $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                    $Meet_Meeting_Details = [
                        'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'btnBackGround'=>'#499619',
                        'btnColor'=>'#fff',
                        'btnEventClass'=>'start_meet_individual',
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                }


                else if($curtime == $lsnendtime && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class?->learning_tool == 'Zoom' && ($getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined)) {
                        // updateLessonStatusCompleteZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class->learning_tool == 'Zoom' && $getTodayLiveClass->is_instructor_joined == '0') {
                        // updateLessonStatusMissedZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }
                else if($curtime == $lsnendtime && $getTodayLiveClass->class->learning_tool == 'BBB') {
                    if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class?->learning_tool == 'BBB' && ($getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined)) {
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class->learning_tool == 'BBB' && $getTodayLiveClass->is_instructor_joined == '0') {
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }
                else if($curtime == $lsnendtime && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
                    if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class?->learning_tool == 'Google_Meet' && ($getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined)) {
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class->learning_tool == 'Google_Meet' && $getTodayLiveClass->is_instructor_joined == '0') {
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }
                else if($curtime > $lsnendtime) {
                    if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Zoom' && $getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined) {	
                        // updateLessonStatusCompleteZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'BBB' && $getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined) {	
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet' && $getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined) {	
                        // updateLessonStatusComplete($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Completed');
                        $btnCssClass = 'btn btn-success m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Zoom' && $getTodayLiveClass->is_instructor_joined == '0') {	
                        // updateLessonStatusMissedZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'BBB' && $getTodayLiveClass->is_instructor_joined == '0') {	
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                    else if($getTodayLiveClass->status == "Pending" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet' && $getTodayLiveClass->is_instructor_joined == '0') {	
                        // updateLessonStatusMissed($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,null);
                        $btnStatus = __('Missed');
                        $btnCssClass = 'btn btn-danger m-1';
                    }
                }
            }
            else{
                $btnStatus = __('Waiting');
                $btnCssClass = 'btn btn-warning m-1';
                $btnBackGround='orange';
                $btnColor='#fff';
                $btnID = 'lec_link1_'.$getTodayLiveClass->id;
            }

            if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'type' => 'Zoom',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'studentID'=>$getTodayLiveClass->student_id,
                    'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                    'Date/Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                    'Btn_Css_Class'=>$btnCssClass,
                    'Btn_BackGround'=>$btnBackGround,
                    'Btn_Color'=>$btnColor,
                    'Btn_ID'=>$btnID,
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    'Meeting_Details'=>$Zoom_Meeting_Details,
                ];
            }
            else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'type' => 'Google_Meet',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'studentID'=>$getTodayLiveClass->student_id,
                    'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                    'Date/Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                    'Btn_Css_Class'=>$btnCssClass,
                    'Btn_BackGround'=>$btnBackGround,
                    'Btn_Color'=>$btnColor,
                    'Btn_ID'=>$btnID,
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    'Meeting_Details'=>$Meet_Meeting_Details,
                ];
            }
            else{
                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'type' => 'BBB',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'studentID'=>$getTodayLiveClass->student_id,
                    'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                    'Date/Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                    'Btn_Css_Class'=>$btnCssClass,
                    'Btn_BackGround'=>$btnBackGround,
                    'Btn_Color'=>$btnColor,
                    'Btn_ID'=>$btnID,
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    'Meeting_Details'=>$BBB_Meeting_Details,
                ];
            }
        }
         
           
        }
   
        // dd($result_array);
        return response()->json($result_array);   
    }

    public function classSchedule() {

        $data['title'] = 'Class Schedule';
        $instructor_user_id=auth()->id();
        $getInstructor = Instructor::where('user_id',$instructor_user_id)->first();
        $instructor_id = $getInstructor->id;
        $today_date = date('Y-m-d');
        $getIndividualClasses = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })->orderBy('class_date','ASC')
          ->orderBy('class_time','ASC')->paginate(25);

          $getGroupClasses = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Group');
        })->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')
        ->groupBy('class_id')
        ->paginate(25);

        $data['getIndividualClasses'] = $getIndividualClasses;
        $data['getGroupClasses'] = $getGroupClasses;
        $data['base_url'] = url('/');
  
        // dd($getGroupClasses);
        return view('instructor.class-schedule',$data);

    }







    public function classHistory() {

        $data['title'] = 'Class History';
        $instructor_user_id=auth()->id();
        $getInstructor = Instructor::where('user_id',$instructor_user_id)->first();
        $instructor_id = $getInstructor->id;
        $today_date = date('Y-m-d');

   
        $getIndividualClassesHistory = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'<=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })->orderBy('id','DESC')
          ->orderBy('class_date','ASC')
          ->orderBy('class_time','ASC')->paginate(10);

        // $getGroupClassesHistory = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'<=',$today_date)->wherehas('class' , function($query){
        //     $query->where('type', 'Group');
        // })
        // ->orderBy('id','DESC')
        // ->orderBy('class_id','DESC')
        // ->orderBy('class_date','DESC')
        // ->groupBy('class_id')
        // ->paginate(10);

        $getGroupClassesHistory = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'<',$today_date)
        ->whereHas('class', function($query) {
            $query->where('type', 'Group');
        })
        ->orderBy('id','DESC')
        ->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')
        ->groupBy('class_id','class_date')
        ->havingRaw('COUNT(*) >= 1')
        ->paginate(10);

        // $data['getIndividualClassesHistory'] = $getIndividualClassesHistory;
        $data['getIndividualClassesHistory'] = $getIndividualClassesHistory;
        $data['getGroupClassesHistory'] = $getGroupClassesHistory;
        $data['base_url'] = url('/');
  
        // dd($getGroupClasses);
        return view('instructor.class-history',$data);

    }



    public function getStudentNames(Request $request){
        $student_ids = array();
        $sr = 0;
        $classID = $request->class_id;
        $output = '<table class="display table table-striped table-bordered">
        <thead>
        <tr>
            <th>SR.</th>
            <th>Name</th>
        </tr>
        </thead>
        <tbody>  
    ';
    $getStudents = ClassDetail::where('class_id',$classID)->get();
    foreach ($getStudents as $key => $stu) {
        $student_ids[] = $stu->student_id;
    }
    $student_ids = array_unique($student_ids);
    if(!empty($student_ids)){
    foreach ($student_ids as $key => $value) {
        $getStudentData = Student::where('id',$value)->first();
        if($getStudentData){
            $output .='
            <tr>
                <td>
                    '.++$sr.'
                </td>
                <td>
                   <span>'.$getStudentData->first_name.' '.$getStudentData->last_name.'</span>
                    
                </td>
            </tr>

        
            ';
        }
        // <a target="_blank" href="'.$url.'">'.$getStudentData->first_name.' '.$getStudentData->last_name.'</a>
    }
    $output .= '
</tbody>
</table>';

}
else{
  $output  = '';
}	
echo json_encode($output);
        // dd( $output);
    }


    public function CheckLessonStartTimeInstructor(){
         // Collect all existing meeting IDs before generating new ones
        $existingMeetings = ClassDetail::whereNotNull('meeting_id')->pluck('meeting_id')->toArray();

        $today_date = date("Y-m-d");
        $user_id = Auth::id();
        $findInstructor = Instructor::where('user_id',$user_id)->first();
        $instructor_id =  $findInstructor->id;
        // $getLessonScheduleOfTutor1 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id',null)
        // ->where('class_date','>=',$today_date)->where('meeting_id',null)->get();
        $getLessonScheduleOfTutor1 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id',null)
        ->where('class_date',$today_date)->where('meeting_id',null)->get();
       
        if(!$getLessonScheduleOfTutor1->isEmpty())
			{
				foreach ($getLessonScheduleOfTutor1 as $key=> $bokdlsn) 
				    {
                        // $timezone = 'Asia/Karachi';
                        $lesson_time = date('h:i A',strtotime($bokdlsn->class_time));
                        $lesson_duration = $bokdlsn->class->duration;
                        // date_default_timezone_set($timezone);
                        $curdate = strtotime(date("Y-m-d"));
                        $lsndate = strtotime($bokdlsn->class_date); 
                        $updated_cur_time = getCurrentServerTimeByZone($bokdlsn->instructor_id,'instructor');
                     
                        $curtime = strtotime($updated_cur_time);
                
                        $updated_lesson_time = changetimeByZone($bokdlsn->class_time,$bokdlsn->instructor_id,'instructor');
                        $lsntime = strtotime($updated_lesson_time) ;

                        $lsntimestamp = strtotime($updated_lesson_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp ;

                        $lsntimestamp2 = strtotime($updated_lesson_time) + ($lesson_duration-1)*60;
                        $lsnendtime2 = $lsntimestamp2 ;
            
                        if($curdate == $lsndate){
                          
                            
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->class->learning_tool == 'Zoom')
                            {
                                if($bokdlsn->group_id == NULL){
                                 
                                    // $met_id = rand(1,9999999);
                                    // $met_id = Uuid::uuid4()->toString();
                                    // $met_id = $this->generateAdvancedPasscode();
                                     // Generate a unique meeting ID
                                    $met_id = generateUniqueMeetingId($existingMeetings);
                                    // $met_id=checkuniqueid($met_id);
                                    $existingMeetings[] = $met_id;
                                 
                                    $data = startzoomclassindividual($bokdlsn->id,$bokdlsn->student_id,$bokdlsn->instructor_id,$met_id,null,$bokdlsn->class->learning_tool);
                                    if($data){
                                        $return['l_id'] = $data['l_id'];
                                        $return['learning_tool'] = 'Zoom';
                                        $return['is_missed'] = 'No';
                                        $return['url'] = $data['TutorURI'];
                                        $return['is_group'] = $data['is_group'];
                                        $return['meeting_id'] = $data['zoom_meeting_id'];
                                        $return['user_name'] = $data['tutor_name'];
                                        $return['user_id'] = $data['tutor_id'];
                                        $return['user_type'] = $data['tutor_type'];
                                    
                                        $meetingID = $data['zoom_meeting_id'];
                                        $lesson_id = $data['l_id'];

                                        echo json_encode($return);
                                    
                                    }
                                    else{
                                    
                                        // echo json_encode("Some Issue try again");
                                        // echo json_encode(false);
                                    }
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->InstructorURI == NULL && $bokdlsn->class?->learning_tool == 'BBB')
                            {
                                if($bokdlsn->group_id == NULL){
                                 
                                    $met_id = generateUniqueBBBMeetingId($existingMeetings);
                                    $existingMeetings[] = $met_id;
                                    $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $bokdlsn->class?->duration);
                                    $data = startbbbclassindividual($bokdlsn->id,$bokdlsn->student_id,$bokdlsn->instructor_id,$met_id, $this->bb,null,$bokdlsn->class?->learning_tool,$meeting);
                                
                                    if($data){
                                        $return['l_id'] = $data['l_id'];
                                        $return['learning_tool'] = 'BBB';
                                        $return['is_missed'] = 'No';
                                        $return['group_id'] = $data['group_id'];
                                        $return['url'] = $data['TutorURI'];
                                        $return['is_group'] = $data['is_group'];
                                        $return['meeting_id'] = $data['zoom_meeting_id'];
                                        $return['user_name'] = $data['tutor_name'];
                                        $return['user_id'] = $data['tutor_id'];
                                        $return['user_type'] = $data['tutor_type'];
                                    
                                        $meetingID = $data['zoom_meeting_id'];
                                        $lesson_id = $data['l_id'];
                                
                                        echo json_encode($return);
                                    }
                                    else{
                                      
                                    }
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->class?->learning_tool == 'Google_Meet')
                            {
                                if($bokdlsn->google_meet_link != null){     // MEANS  EVERYTHING OK
                                    if($bokdlsn->group_id == NULL){
                                        $met_id = generateUniqueMeetingId($existingMeetings);
                                        $existingMeetings[] = $met_id;
                                   
                                       $data = startmeetclassindividual($bokdlsn->id,$bokdlsn->student_id,$bokdlsn->instructor_id,$met_id,null,$bokdlsn->class->learning_tool);
                                       if($data){
                                           $return['l_id'] = $data['l_id'];
                                           $return['learning_tool'] = 'Google_Meet';
                                           $return['is_missed'] = 'No';
                                           // $return['url'] = $data['TutorURI'];
                                           $return['url'] = $data['google_meet_link'];
                                           $return['is_group'] = $data['is_group'];
                                           $return['meeting_id'] = $data['meet_meeting_id'];
                                           $return['user_name'] = $data['tutor_name'];
                                           $return['user_id'] = $data['tutor_id'];
                                           $return['user_type'] = $data['tutor_type'];
                                       
                                           $meetingID = $data['meet_meeting_id'];
                                           $lesson_id = $data['l_id'];
   
                                           echo json_encode($return);
                                       
                                       }
                                       else{
                                       }
                                   }
                                }
                                else{ // MEANS  EVERYTHING NOT OK TUTOR WHITE BLANK SCREEN ISSUE
                                    if($bokdlsn->group_id == NULL){
                                        $met_id = generateUniqueMeetingId($existingMeetings);
                                        $existingMeetings[] = $met_id;
                                        $data = startmeetclassindividual($bokdlsn->id,$bokdlsn->student_id,$bokdlsn->instructor_id,$met_id,null,$bokdlsn->class->learning_tool,0);
                                        if($data){
                                            $return['l_id'] = $data['l_id'];
                                            $return['learning_tool'] = 'Google_Meet';
                                            $return['is_missed'] = 'No';
                                            // $return['url'] = $data['TutorURI'];
                                            $return['url'] = $data['google_meet_link'];
                                            $return['is_group'] = $data['is_group'];
                                            $return['meeting_id'] = $data['meet_meeting_id'];
                                            $return['user_name'] = $data['tutor_name'];
                                            $return['user_id'] = $data['tutor_id'];
                                            $return['user_type'] = $data['tutor_type'];
                                        
                                            $meetingID = $data['meet_meeting_id'];
                                            $lesson_id = $data['l_id'];
                                            echo json_encode($return);    
                                        }
                                        else{
                                        }
                                    }
                                }   
                            }          
                            else if($curtime >= $lsnendtime){
                         
                                // if($bokdlsn->is_instructor_joined == '1' || $bokdlsn->is_instructor_joined == "1" || $bokdlsn->is_instructor_joined == 1 || $bokdlsn->is_instructor_joined){
                                //     $results = ClassDetail::where('id',$bokdlsn->id)->first();
                                //     $results->status = "Completed";
                                //     $response = $results->update();
                                // }
                                // else{
                                //     $results = ClassDetail::where('id',$bokdlsn->id)->first();
                                //     $results->status = "Missed";
                                //     $response = $results->update();
                                // }
                            }
                            else{
                                // echo json_encode("Some Issue try again");
                                // echo json_encode(false);
                            }
                        
                        }
                    }
            }



            // $getLessonScheduleOfTutor2 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id','<>',null)
            // ->where('class_date','>=',$today_date)->where('meeting_id',null)->groupBy('group_id')->orderBy('class_id','DESC')
            // ->orderBy('class_date','DESC')->get();
            $getLessonScheduleOfTutor2 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id','<>',null)
            ->where('class_date',$today_date)->where('meeting_id',null)->groupBy('group_id')->orderBy('class_id','ASC')
            ->orderBy('class_date','ASC')->get();


            if(!$getLessonScheduleOfTutor2->isEmpty())
			{
                $group_id = NULL;
				foreach ($getLessonScheduleOfTutor2 as $key=> $bokdlsn2) 
				    {
                        // $timezone = 'Asia/Kabul';
                        $lesson_time = date('h:i A',strtotime($bokdlsn2->class_time));
                        $lesson_duration = $bokdlsn2->class->duration;
                        // date_default_timezone_set($timezone);
                        $curdate = strtotime(date("d-m-Y"));
                        $lsndate = strtotime($bokdlsn2->class_date); 
                        $updated_cur_time = getCurrentServerTimeByZone($bokdlsn2->instructor_id,'instructor');
                        $curtime = strtotime($updated_cur_time);
                
                        $updated_lesson_time = changetimeByZone($bokdlsn2->class_time,$bokdlsn2->instructor_id,'instructor');
                        $lsntime = strtotime($updated_lesson_time) ;

                        $lsntimestamp = strtotime($updated_lesson_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp ;

                        $lsntimestamp2 = strtotime($updated_lesson_time) + ($lesson_duration-1)*60;
                        $lsnendtime2 = $lsntimestamp2 ;
                        if($curdate == $lsndate){
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->meeting_id == NULL && $bokdlsn2->class->learning_tool == 'Zoom')
                            {
                              
                                // $met_id = rand(1,9999999);
                                // $met_id = Uuid::uuid4()->toString();
                                // $met_id = $this->generateAdvancedPasscode();
                                // $met_id=checkuniqueid($met_id);

                                $met_id = generateUniqueMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                $data = startzoomclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id,$bokdlsn2->group_id,$bokdlsn2->class->learning_tool);
                                // dd($data);
                                if($data){
                                    // {"l_id":81,"learning_tool":"Zoom","group_id":2,"is_missed":"No","url":null,"is_group":"Yes","meeting_id":7961488,"user_name":"CEF Online","user_id":8,"user_type":"Student"}
                                    $return['l_id'] = $data['l_id'];
                                    $return['learning_tool'] = 'Zoom';
                                    $return['group_id'] = $data['group_id'];
                                    $return['is_missed'] = 'No';
                                    $return['url'] = $data['TutorURI'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['meeting_id'] = $data['zoom_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['zoom_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    echo json_encode($return);
                                }
                                else{
                                    // echo json_encode("Some Issue try again");
                                    // echo json_encode(false);
                                }

                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->InstructorURI == NULL && $bokdlsn2->class->learning_tool == 'BBB')
                            {
                              
                                $met_id = generateUniqueBBBMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                
                                $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $bokdlsn2->class?->duration);
                                $data = startbbbclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id, $this->bb,$bokdlsn2->group_id,$bokdlsn2->class?->learning_tool,$meeting);

                                if($data){
                                    $return['l_id'] = $data['l_id'];
                                    $return['learning_tool'] = 'BBB';
                                    $return['is_missed'] = 'No';
                                    $return['url'] = $data['TutorURI'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['group_id'] = $data['group_id'];
                                    $return['meeting_id'] = $data['zoom_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['zoom_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    echo json_encode($return);
                                }
                                else{
                                  
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->meeting_id == NULL && $bokdlsn2->class->learning_tool == 'Google_Meet')
                            {
                                
                                $met_id = generateUniqueMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                $data = startmeetclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id,$bokdlsn2->group_id,$bokdlsn2->class->learning_tool);
                              
                                if($data){
                                    $return['l_id'] = $data['l_id'];
                                    $return['learning_tool'] = 'Google_Meet';
                                    $return['group_id'] = $data['group_id'];
                                    $return['is_missed'] = 'No';
                                    // $return['url'] = $data['TutorURI'];
                                    $return['url'] = $data['google_meet_link'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['meeting_id'] = $data['meet_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['meet_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    echo json_encode($return);
                                }
                                else{
                                    // echo json_encode("Some Issue try again");
                                    // echo json_encode(false);
                                }

                            }
                            else if($curtime >= $lsnendtime){
                                // if($bokdlsn2->is_instructor_joined == '1' || $bokdlsn2->is_instructor_joined == "1" || $bokdlsn2->is_instructor_joined == 1 || $bokdlsn2->is_instructor_joined){
                                //     // $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->first();
                                //     $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->get();
                                //     foreach ($results as $key => $value) {
                                //         $value->status = "Completed";
                                //         $response = $value->update();
                                //     }
                                    
                                // }
                                // else{
                                //     // $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->first();
                                //     $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->get();
                                //     foreach ($results as $key => $value) {
                                //         $value->status = "Missed";
                                //         $response = $value->update();
                                //     }
                                // }
                            }
                            else{
                                // echo json_encode(false);
                            }
                        }
                    }

            }
     
    }




    public function CheckLessonStartTimeInstructorLiveClass(){
          // Collect all existing meeting IDs before generating new ones
          $existingMeetings = LiveClass::whereNotNull('meeting_id')->pluck('meeting_id')->toArray();
        $today_date = date("Y-m-d");
        $user_id = Auth::id();
        $findInstructor = Instructor::where('user_id',$user_id)->first();
        $instructor_id =  $findInstructor->id;
        $getLessonScheduleOfTutor = LiveClass::where('user_id',$user_id)->where('date','>=',$today_date)
        ->where('meeting_id',null)->get();
     
        if(!$getLessonScheduleOfTutor->isEmpty())
	    {
				foreach ($getLessonScheduleOfTutor as $key=> $bokdlsn) 
				    {
                       
                        $lesson_time = date('h:i A',strtotime($bokdlsn->time));
                        $lesson_duration = $bokdlsn->duration;
                        $curdate = strtotime(date("Y-m-d"));
                        $lsndate = strtotime($bokdlsn->date); 
                        $updated_cur_time = getCurrentServerTimeByZone($instructor_id,'instructor');
                        $curtime = strtotime($updated_cur_time);
                        $updated_lesson_time = changetimeByZone($bokdlsn->time,$instructor_id,'instructor');
                        $lsntime = strtotime($updated_lesson_time) ;
                        $lsntimestamp = strtotime($updated_lesson_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp ;
                        $lsntimestamp2 = strtotime($updated_lesson_time) + ($lesson_duration-1)*60;
                        $lsnendtime2 = $lsntimestamp2 ;
                     
                        if($curdate == $lsndate){
                         
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->meeting_host_name == 'zoom')
                            {
                                    // $met_id = rand(1,9999999);
                                    // $met_id = $this->generateAdvancedPasscode();
                                    // $met_id=checkuniqueidliveclass($met_id);
                                    $met_id = generateUniqueMeetingIdLiveClass($existingMeetings);
                                    $existingMeetings[] = $met_id;
                                
                                    $data = startzoomliveclass($bokdlsn->id,null,$instructor_id,$met_id,null,$bokdlsn->meeting_host_name);
                                    if($data){
                                        $return['l_id'] = $data['l_id'];
                                        $return['learning_tool'] = 'Zoom';
                                        // $return['is_missed'] = 'No';
                                        $return['url'] = $data['TutorURI'];
                                        // $return['is_group'] = $data['is_group'];
                                        $return['meeting_id'] = $data['zoom_meeting_id'];
                                        $return['user_name'] = $data['tutor_name'];
                                        $return['user_id'] = $data['tutor_id'];
                                        $return['user_type'] = $data['tutor_type'];
                                    
                                        $meetingID = $data['zoom_meeting_id'];
                                        $lesson_id = $data['l_id'];

                                        echo json_encode($return);
                                    
                                    }
                                    else{     
                                    }
                            }
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->meeting_host_name == 'google_meet')
                            {
                                    $met_id = generateUniqueMeetingIdLiveClass($existingMeetings);
                                    $existingMeetings[] = $met_id;
                                
                                    $data = startmeetliveclass($bokdlsn->id,null,$instructor_id,$met_id,null,$bokdlsn->meeting_host_name);
                                    if($data){
                                        $return['l_id'] = $data['l_id'];
                                        $return['learning_tool'] = 'Google_Meet';
                                        // $return['is_missed'] = 'No';
                                        $return['url'] = $data['google_meet_link'];
                                        // $return['is_group'] = $data['is_group'];
                                        $return['meeting_id'] = $data['meet_meeting_id'];
                                        $return['user_name'] = $data['tutor_name'];
                                        $return['user_id'] = $data['tutor_id'];
                                        $return['user_type'] = $data['tutor_type'];
                                    
                                        $meetingID = $data['meet_meeting_id'];
                                        $lesson_id = $data['l_id'];

                                        echo json_encode($return);
                                    
                                    }
                                    else{     
                                    }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->InstructorURI == NULL && $bokdlsn->meeting_host_name == 'bbb')
                            {
                                    // $met_id = rand(1,9999999);
                                    // $met_id = $this->generateAdvancedPasscode();
                                    // $met_id=checkuniqueidliveclass($met_id,$getAllMeetings);
                                    // $met_id = generateUniqueMeetingIdLiveClass($existingMeetings);
                                    // $getAllMeetings = $this->bb->get_meetings();
                                    $met_id = generateUniqueBBBMeetingIdLiveClass($existingMeetings);
                                    $existingMeetings[] = $met_id;
                                    $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $bokdlsn->duration);
                                    $data = startbbbliveclass($bokdlsn->id,null,$instructor_id,$met_id, $this->bb,null,$bokdlsn->meeting_host_name,$meeting);
                                
                                    if($data){
                                        $return['l_id'] = $data['l_id'];
                                        $return['learning_tool'] = 'BBB';
                                        // $return['is_missed'] = 'No';
                                        // $return['group_id'] = $data['group_id'];
                                        $return['url'] = $data['TutorURI'];
                                        // $return['is_group'] = $data['is_group'];
                                        $return['meeting_id'] = $data['bbb_meeting_id'];
                                        $return['user_name'] = $data['tutor_name'];
                                        $return['user_id'] = $data['tutor_id'];
                                        $return['user_type'] = $data['tutor_type'];
                                    
                                        $meetingID = $data['bbb_meeting_id'];
                                        $lesson_id = $data['l_id'];
                                
                                        echo json_encode($return);
                                    }
                                    else{
                                      
                                    }
                                
                            }
                        
                            else if($curtime >= $lsnendtime){
                         
                                // if($bokdlsn->is_instructor_joined == '1' || $bokdlsn->is_instructor_joined == "1" || $bokdlsn->is_instructor_joined == 1 || $bokdlsn->is_instructor_joined){
                                //     $results = LiveClass::where('id',$bokdlsn->id)->first();
                                //     $results->status = "Completed";
                                //     $response = $results->update();
                                // }
                                // else{
                                //     $results = LiveClass::where('id',$bokdlsn->id)->first();
                                //     $results->status = "Missed";
                                //     $response = $results->update();
                                // }
                            }
                            else{
                           
                            }
                        
                        }
                    }
        }

     
    }

    public function UpdateTutorMeetingStatusLiveClass($meeting_id,$lesson_id)
    {
        $result=updateTutorJoinStatusLiveClass($lesson_id,$meeting_id,null); 
        echo json_encode($result);    
    }



    public function CreateNewZoomMeetingInstructorLiveClass($meeting_id = null , $user_type = null , $user_name = null , $user_id = null){

    
        $lesson_duration = 30;
        $lesson_id = null;
        $group_id = 0;
        $student_name = "New Student";
        $old_start_time = null;
        $old_end_time = null;
        $data['meeting_id'] = $meeting_id;
        $data['user_type'] = $user_type;
        $data['user_name'] = $user_name;
        $data['user_id'] = $user_id;
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
     
        $sdk_key = $zoomSettings->sdk_key;
        $sdk_secret = $zoomSettings->sdk_secret;
        $data['sdkKey'] = $sdk_key;
        $data['sdkSecret'] = $sdk_secret;
        $data['baseURL'] = url('/');
        $sessionName = null;
        $getLessonDetails = LiveClass::where('meeting_id',$meeting_id)->first();
        $data['course_uuid'] = $getLessonDetails->course?->uuid;
     
        $meeting_end_url = route('live-class.index',['course_uuid'=>$data['course_uuid']]);
   
        $data['meeting_end_url'] = $meeting_end_url;
        $getLinkDetails = LiveClassStudentLink::where('live_class_id',$getLessonDetails->id)->get();
     
        if(!$getLinkDetails->isEmpty()){
            $sessionName =Uuid::uuid4()->toString();
            foreach ($getLinkDetails as $key => $value2) {
                $student_id = $value2->student_id;
                $student_name =getStudentNameFromID($student_id);
                $student_names_array[] = $student_name;
            }
        }

    
        $findInstructor = Instructor::where('user_id',$getLessonDetails->user_id)->first();
        $instructor_id = $findInstructor->id;
        if($getLessonDetails != null){
            $data['course_uuid'] = $getLessonDetails->course?->uuid;
                $data['meeting_id'] = $getLessonDetails->meeting_id;
                // $data['meeting_password'] = '12345';
                $data['meeting_password'] = $getLessonDetails->meeting_id;
                // $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
                if($getLessonDetails->zoom_meeting_signature != null){
                    $data['meeting_signature'] = $getLessonDetails->zoom_meeting_signature;
                }
                else{
                    $data['meeting_signature'] = null;
                }
                $lesson_duration =$getLessonDetails->duration;
                $old_start_time = $getLessonDetails->start_time_zoom;
                $old_end_time = $getLessonDetails->end_time_zoom;
                $lesson_id = $getLessonDetails->id;
                // $student_id = $getLessonDetails->student_id;
                // $student_name =getStudentNameFromID($student_id);


                  //////////////////NEW CODE//////////////////////
                if($getLessonDetails->zoom_meeting_name != null){
                    $data['session_name'] = $getLessonDetails->zoom_meeting_name;
                    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->id.')';
                }
                else{
                    $data['session_name'] = Uuid::uuid4()->toString();
                    $getLessonDetails->zoom_meeting_name =  $data['session_name'];
                    $getLessonDetails->update();
                    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->id.')';
                }
                 //////////////////NEW CODE//////////////////////

        }
        else{
            $data['course_uuid'] = $getLessonDetails->course?->uuid;
        }
    
        if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
            $given_start_time = $old_start_time;
            $given_end_time = $old_end_time;
        }
        else{
            $data_record_lesson = LiveClass::where('meeting_id',$meeting_id)->first();
            $given_end_time = $data_record_lesson->end_time_zoom;
            // $given_timezone = $data_record_lesson['timezone'];
            // date_default_timezone_set($given_timezone);
            $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
            $given_start_time = strtotime($updated_current_time);
    	
        }
    
        $student_names_json = implode(',',$student_names_array);
        
      
        if($given_start_time == null){
            $given_start_time = 0;
        }
        if($given_end_time == null){
            $given_end_time = 0;
        }
    
        // dd($given_end_time);
        $data['lesson_duration'] = $lesson_duration;
        $data['lesson_id'] = $lesson_id;
        $data['group_id'] = $group_id;
        $data['student_name'] = $student_name;
        $data['student_names_json'] = $student_names_json;
        $data['start_time'] = $given_start_time;
        $data['end_time'] = $given_end_time;
        
        echo view('instructor.meeting-page-zoom-ui-liveclass',$data);
    }


    public function UpdateTutorMeetingStatusIndividual($meeting_id,$lesson_id)
    {
        $result=updateTutorJoinStatus($lesson_id,$meeting_id,null); 
        echo json_encode($result);    
    }




    public function UpdateTutorMeetingStatusGroup($group_id,$meeting_id,$lesson_id)
    {
        $result=updateTutorJoinStatus($lesson_id,$meeting_id,$group_id); 
        echo json_encode($result);    
    }




    public function CreateNewZoomMeetingInstructor($meeting_id = null , $user_type = null , $user_name = null , $user_id = null){

    
        $lesson_duration = 30;
        $lesson_id = null;
        $group_id = 0;
        $student_name = "New Student";
        $old_start_time = null;
        $old_end_time = null;
        $data['meeting_id'] = $meeting_id;
        $data['user_type'] = $user_type;
        $data['user_name'] = $user_name;
        $data['user_id'] = $user_id;
        // $zoomSettings = ZoomSetting::first();
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
     
        $sdk_key = $zoomSettings->sdk_key;
        $sdk_secret = $zoomSettings->sdk_secret;
        $data['sdkKey'] = $sdk_key;
        $data['sdkSecret'] = $sdk_secret;
        $data['baseURL'] = url('/');
    
      
    $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
   
    if($getLessonDetails != null){
        $getLesson = Lesson::where('id',$getLessonDetails->class_id)->first();

        if($getLessonDetails->zoom_meeting_password != null){
            $data['meeting_password'] = $getLessonDetails->zoom_meeting_password;
        }
        else{
            $data['meeting_password'] = $this->generateAdvancedPasscode();
            $getLessonDetails->zoom_meeting_password = $data['meeting_password'];
            $getLessonDetails->update();
        }


        //////////////////NEW CODE//////////////////////
        if($getLessonDetails->zoom_meeting_name != null){
            $data['session_name'] = $getLessonDetails->zoom_meeting_name;
        }
        elseif($getLessonDetails->class?->zoom_meeting_name != null){
            $data['session_name'] = $getLessonDetails->class?->zoom_meeting_name;
        }
        else{
            // $meetingID = $meeting_id;
            // $data['session_name'] = "Online Meeting-$meetingID";
            $data['session_name'] = Uuid::uuid4()->toString();
            $getLessonDetails->zoom_meeting_name =  $data['session_name'];
            $getLessonDetails->update();
            if($getLesson){
                $getLesson->zoom_meeting_name = $data['session_name'];
                $getLesson->update();
            }
        }
          //////////////////NEW CODE//////////////////////
    }

        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
        $instructor_id = $getLessonDetails->instructor_id;
        if($getLessonDetails != null){
                $data['course_uuid'] = $getLessonDetails->course?->uuid;
             

                
              
             
                if($getLessonDetails->zoom_meeting_signature != null){
                    $data['meeting_signature'] = $getLessonDetails->zoom_meeting_signature;
                }
                else{
                    $data['meeting_signature'] = null;
                }

               

                $lesson_duration =$getLessonDetails->class->duration;
                $old_start_time = $getLessonDetails->start_time_zoom;
                $old_end_time = $getLessonDetails->end_time_zoom;
                $lesson_id = $getLessonDetails->id;
                $student_id = $getLessonDetails->student_id;
                $student_name =getStudentNameFromID($student_id);

                //NEW CODEEEE///////////////////////////////
                if($getLessonDetails->class?->meeting_id != $getLessonDetails->meeting_id){
                    $data['meeting_id'] = $getLessonDetails->class?->meeting_id;
                    $meetingID = $getLessonDetails->class?->meeting_id;
                    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->class_id.')';
                }
                else{
                    $data['meeting_id'] = $getLessonDetails->meeting_id;
                    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->class_id.')';
                }
                //NEW CODEEEE ENDDDD///////////////////////////////
        }
        else{
            $data['course_uuid'] = null;
        }
      
        if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
            $given_start_time = $old_start_time;
            $given_end_time = $old_end_time;
        }
        else{
            $data_record_lesson = ClassDetail::where('meeting_id',$meeting_id)->first();
            $given_end_time = $data_record_lesson->end_time_zoom;
            // $given_timezone = $data_record_lesson['timezone'];
            // date_default_timezone_set($given_timezone);
            $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
            $given_start_time = strtotime($updated_current_time);
    
            
            // $this->Generic_model->updateRecord('qu_lesson_schedule' , array('start_time' => $given_start_time,'end_time'=>$given_end_time) , array('classid' =>$meeting_id)) ;
            // $this->Generic_model->updateRecord('qu_lesson_schedule' , array('start_time' => $given_start_time) , array('classid' =>$meeting_id)) ;		
        }
    
        if($given_start_time == null){
            $given_start_time = 0;
        }
        if($given_end_time == null){
            $given_end_time = 0;
        }
        // dd($given_end_time);
        $data['lesson_duration'] = $lesson_duration;
        $data['lesson_id'] = $lesson_id;
        $data['group_id'] = $group_id;
        $data['student_name'] = $student_name;
        $data['start_time'] = $given_start_time;
        $data['end_time'] = $given_end_time;
        
        echo view('instructor.meeting-page-zoom-ui',$data);
    }


    function generateAdvancedPasscode($length = 6) {
        // Combine current timestamp with random number
        $timestamp = time();
        $random = rand(10000, 999999999);
        return substr($timestamp . $random, -$length);
    }

    function generateUniquePasscode($length = 6) {
        // Cryptographically secure random number
        return substr(str_shuffle(str_repeat('0123456789', $length)), 0, $length);
    }
    

    public function CreateNewZoomGroupMeetingInstructor($meeting_id = null , $user_type = null , $user_name = null , $user_id = null , $group_id = null){

    
        $lesson_duration = 30;
        $lesson_id = null;
        $student_name = "New Student";
        $old_start_time = null;
        $old_end_time = null;
        $data['meeting_id'] = $meeting_id;
        $data['user_type'] = $user_type;
        $data['user_name'] = $user_name;
        $data['user_id'] = $user_id;
        $data['group_id'] = $group_id;
        // $zoomSettings = ZoomSetting::first();
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
     
        $sdk_key = $zoomSettings->sdk_key;
        $sdk_secret = $zoomSettings->sdk_secret;
        $data['sdkKey'] = $sdk_key;
        $data['sdkSecret'] = $sdk_secret;
        $data['baseURL'] = url('/');
        $sessionName = null;
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->get();
        
	if(!$getLessonDetails->isEmpty()){
        $sessionName =Uuid::uuid4()->toString();
		foreach ($getLessonDetails as $key => $value2) {
			$student_id = $value2->student_id;
			$student_name =getStudentNameFromID($student_id);
			$student_names_array[] = $student_name;

        //////////////////NEW CODE//////////////////////
        if($value2->zoom_meeting_name != null){
            $data['session_name'] = $value2->zoom_meeting_name;
        }
        elseif($value2->class?->zoom_meeting_name != null){
            $data['session_name'] = $value2->class?->zoom_meeting_name;
        }
        else{
            $data['session_name'] = $sessionName;
            $value2->zoom_meeting_name =  $data['session_name'];
            $value2->update();
        }
          //////////////////NEW CODE//////////////////////
		}

	}


   
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();
      


        $instructor_id = $getLessonDetails->instructor_id;
        if($getLessonDetails != null){

        //////////////////NEW CODE//////////////////////
        $getLesson = Lesson::where('id',$getLessonDetails->class_id)->first();
        if($getLesson){
            $getLesson->zoom_meeting_name = $data['session_name'];
            $getLesson->update();
        }
         //////////////////NEW CODE//////////////////////


            $data['course_uuid'] = $getLessonDetails->course?->uuid;
                $data['meeting_id'] = $getLessonDetails->meeting_id;
            
                $data['meeting_password'] = $getLessonDetails->meeting_id;
                if($getLessonDetails->zoom_meeting_signature != null){
                    $data['meeting_signature'] = $getLessonDetails->zoom_meeting_signature;
                }
                else{
                    $data['meeting_signature'] = null;
                }
                $lesson_duration =$getLessonDetails->class->duration;
                $old_start_time = $getLessonDetails->start_time_zoom;
                $old_end_time = $getLessonDetails->end_time_zoom;
                $lesson_id = $getLessonDetails->id;

     


                 //NEW CODEEEE///////////////////////////////
                 if($getLessonDetails->class?->meeting_id != $getLessonDetails->meeting_id){
                    $data['meeting_id'] = $getLessonDetails->class?->meeting_id;
                    $meetingID = $getLessonDetails->class?->meeting_id;
                    // $data['session_name'] = "Online Meeting-$meetingID";
                    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->class_id.')';
                }
                else{
                    $data['meeting_id'] = $getLessonDetails->meeting_id;
                    // $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
                    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->class_id.')';
                }
                //NEW CODEEEE ENDDDD///////////////////////////////

            
        }
        else{
            $data['course_uuid'] = $getLessonDetails->course?->uuid;
        }
    
        if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
            $given_start_time = $old_start_time;
            $given_end_time = $old_end_time;
        }
        else{
            $data_record_lesson = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();
            $given_end_time = $data_record_lesson->end_time_zoom;
            // $given_timezone = $data_record_lesson['timezone'];
            // date_default_timezone_set($given_timezone);
            $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
            $given_start_time = strtotime($updated_current_time);
    	
        }
    	$student_names_json = implode(',',$student_names_array);
        

        $data['lesson_duration'] = $lesson_duration;
        $data['lesson_id'] = $lesson_id;
        $data['group_id'] = $group_id;
        $data['student_name'] = $student_name;
        $data['student_names_json'] = $student_names_json;
        if($given_start_time == null){
            $given_start_time = 0;
        }
        if($given_end_time == null){
            $given_end_time = 0;
        }
        $data['start_time'] = $given_start_time;
        $data['end_time'] = $given_end_time;
        
        echo view('instructor.meeting-page-zoom-ui',$data);
    }

    public function GetCurrentTimeZoomInstructor($meeting_id = null){ 
        $data_record_timezone = ClassDetail::where('meeting_id',$meeting_id)->first();
        $instructor_id =  $data_record_timezone->instructor_id;
        // $given_timezone = $data_record_timezone['timezone'];
        // date_default_timezone_set($given_timezone);
        $result = array();
        $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
        $curtime = strtotime($updated_current_time);
        
        $result = [
            'Current_Time' => $curtime
        ];
    
        echo json_encode($curtime);

    }


    ////////////////////////////////// NEW FUNCTION/////////////////////////////////////
    public function UpdateJoinedStatusZoomInstructor($meeting_id = null , $group_id = null){
        $old_start_time = null;
        $old_end_time = null;
        if($group_id == null){
            $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
        }
        else{
            $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();  
        }

        if($getLessonDetails!= null){
            $old_start_time = $getLessonDetails->start_time_zoom;
            $old_end_time = $getLessonDetails->end_time_zoom;
        }

        if($group_id == null){
            if(($old_start_time != null && $old_start_time != '0') &&  ($old_end_time != null && $old_end_time != '0')){
            }
            else{
                $currentStartTime = $this->GetCurrentTimeZoomInstructor($meeting_id);
                $res = ClassDetail::where('meeting_id',$meeting_id)->first();
                $res->start_time_zoom = $currentStartTime;
                $res->update();
            }
            $l_id = null;
            $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
        }
        else{
            $classes = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->get();
        }

        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                if($group_id != null){
                    if(($old_start_time != null && $old_start_time != '0') &&  ($old_end_time != null && $old_end_time != '0')){   
                    }
                    else{
                        $currentStartTime = $this->GetCurrentTimeZoomInstructor($meeting_id);
                        $value->start_time_zoom = $currentStartTime;
                        $value->update();
                    }
                }

                $l_id = $value->id;
                $start_time = $value->start_time_zoom;
                $end_time = $value->end_time_zoom;
                $res = ClassDetail::where('id',$l_id)->first();
                if($start_time >= $end_time){
                    $res->is_instructor_joined = 0;
                    // $res->status = 'Missed';
                }
                else{
                    $res->is_instructor_joined = 1;
                }
                $res->update();
            }
        }
    
    }
   ///////////////////////////////////// NEW FUNCTION//////////////////////////////////





 ////////////////////////////////// NEW FUNCTION/////////////////////////////////////
   public function UpdateJoinedStatusZoomInstructorLiveClass($meeting_id = null){
    $old_start_time = null;
    $old_end_time = null;
    $getLessonDetails = LiveClass::where('meeting_id',$meeting_id)->first();
    if($getLessonDetails!= null){
        $old_start_time = $getLessonDetails->start_time_zoom;
        $old_end_time = $getLessonDetails->end_time_zoom;
    }
    if(($old_start_time != null && $old_start_time != '0') &&  ($old_end_time != null && $old_end_time != '0')){
    }
    else{
        $currentStartTime = $this->GetCurrentTimeZoomInstructorLiveClass($meeting_id);
        $res = LiveClass::where('meeting_id',$meeting_id)->first();
        $res->start_time_zoom = $currentStartTime;
        $res->update();
    }
    $l_id = null;

    $classes = LiveClass::where('meeting_id',$meeting_id)->get();
    if(!$classes->isEmpty()){
        foreach ($classes as $key => $value) {
            $l_id = $value->id;
            $start_time = $value->start_time_zoom;
            $end_time = $value->end_time_zoom;
            $res = LiveClass::where('id',$l_id)->first();
            if($start_time >= $end_time){
                $res->is_instructor_joined = 0;
                // $res->status = 'Missed';
            }
            else{
                $res->is_instructor_joined = 1;
            }
            $res->update();
        }
    }

}
   ///////////////////////////////////// NEW FUNCTION//////////////////////////////////



    public function UpdateUrlZoomInstructor($meeting_id = null , $currentStartTime = null){
        $old_start_time = null;
        $old_end_time = null;
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
        if($getLessonDetails!= null){
            $old_start_time = $getLessonDetails->start_time_zoom;
            $old_end_time = $getLessonDetails->end_time_zoom;
        }
        if(($old_start_time != null && $old_start_time != '0') &&  ($old_end_time != null && $old_end_time != '0')){
        }
        else{
            $res = ClassDetail::where('meeting_id',$meeting_id)->first();
            $res->start_time_zoom = $currentStartTime;
            $res->update();
        }
        $l_id = null;

        $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $l_id = $value->id;
                $start_time = $value->start_time_zoom;
                $end_time = $value->end_time_zoom;

                $res = ClassDetail::where('id',$l_id)->first();
             

                if($start_time >= $end_time){
                    // $res->is_instructor_joined = '0';
                    $res->is_instructor_joined = 0;
                    // $res->status = 'Missed';
                }
                else{
                    $res->is_instructor_joined = 1;
                }
                $res->update();
            }
        }
    
    }


    public function UpdateUrlGroupZoomInstructor( $meeting_id = null , $group_id = null, $currentStartTime = null){
        $old_start_time = null;
        $old_end_time = null;
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();
        if($getLessonDetails != null){
            $old_start_time = $getLessonDetails->start_time_zoom;
            $old_end_time = $getLessonDetails->end_time_zoom;
        }

        // if(($old_start_time != null && $old_start_time != '0') &&  ($old_end_time != null && $old_end_time != '0')){    
        // }
        // else{
        //     $res = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();
        //     $res->start_time_zoom = $currentStartTime;
        //     $res->update();
        // }
     
        $classes = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {


                if(($old_start_time != null && $old_start_time != '0') &&  ($old_end_time != null && $old_end_time != '0')){   
                }
                else{
                    // $res = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();
                    $value->start_time_zoom = $currentStartTime;
                    $value->update();
                }


                $l_id = $value->id;
                $start_time = $value->start_time_zoom;
                $end_time = $value->end_time_zoom;
                $res = ClassDetail::where('id',$l_id)->first();
                if($start_time >= $end_time){
                    // $res->is_instructor_joined = '0';
                    $res->is_instructor_joined = 0;
                    // $res->status = 'Missed';
                }
                else{
                    // $res->is_instructor_joined = '1';
                    $res->is_instructor_joined = 1;
                }
                $res->update();
            }
        }
    
    
    }

    
    public function UpdateStatusZoomInstructor($meeting_id = null){
        $l_id = null;
        $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $l_id = $value->id;
                $res = ClassDetail::where('id',$l_id)->first();
                $res->is_moderator_left = 1;
                // $res->status = 'Completed';
                $res->update();
            }
        }    
    }

    public function UpdateStatusGroupZoomInstructor($meeting_id = null , $group_id = null){
    
      
        $classes = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $l_id = $value->id;
                $res = ClassDetail::where('id',$l_id)->first();
                $res->is_moderator_left = 1;
                // $res->status = 'Completed';
                $res->update();
            }
        }
    }
    
    public function UpdateMeetingSignature(Request $request, $meeting_id = null,$group_id = null){
      
        $l_id = null;
        $meetingSignature = $request->meetingSignature;
        if($group_id != null){
            $classes = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->get();
        }
        else{
            $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
        }
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $l_id = $value->id;
                $res = ClassDetail::where('id',$l_id)->first();
                if($res){

                    if($res->zoom_meeting_signature == null){
                        $res->zoom_meeting_signature = $meetingSignature;
                        $res->update();
                    }
                    else{
                        $res->zoom_meeting_signature = $meetingSignature;
                        $res->update(); 
                    }

                //NEW CODEEEE///////////////////////////////
                 if($res->class){
                    $getLesson = Lesson::where('id',$res->class_id)->first();
                    if($getLesson){
                        $getLesson->zoom_meeting_signature = $meetingSignature;
                        $getLesson->update();
                    }
                }
                //NEW CODEEEE ENDDDD///////////////////////////////

                }
                
            }
        }    
    }


    public function UpdateMeetingSignatureLiveClass(Request $request, $meeting_id = null){
      
        $l_id = null;
        $meetingSignature = $request->meetingSignature;
            $classes = LiveClass::where('meeting_id',$meeting_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $l_id = $value->id;
                $res = LiveClass::where('id',$l_id)->first();
           
                if($res){
                    if($res->zoom_meeting_signature == null){
                        $res->zoom_meeting_signature = $meetingSignature;
                        $res->update();
                    }
                    else{
                        $res->zoom_meeting_signature = $meetingSignature;
                        $res->update();
                    }
                }
                
            }
        }    
    }
    public function UpdateStatusZoomInstructorLiveClass($meeting_id = null){
        $l_id = null;
        $classes = LiveClass::where('meeting_id',$meeting_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $l_id = $value->id;
                $res = LiveClass::where('id',$l_id)->first();
                // $res->is_moderator_left = '1';
                $res->is_moderator_left = 1;
                // $res->status = 'Completed';
                $res->update();
            }
        }    
    }

    public function GetCurrentTimeZoomInstructorLiveClass($meeting_id = null){
      
        $data_record_timezone = LiveClass::where('meeting_id',$meeting_id)->first();
        $user_id =  $data_record_timezone->user_id;
        $findInstructor = Instructor::where('user_id',$user_id)->first();
        $instructor_id = $findInstructor->id;
        // $given_timezone = $data_record_timezone['timezone'];
        // date_default_timezone_set($given_timezone);
        $result = array();
        $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
        $curtime = strtotime($updated_current_time);
        
        $result = [
            'Current_Time' => $curtime
        ];
    
        echo json_encode($curtime);

    }
    public function UpdateUrlZoomInstructorLiveClass($meeting_id = null , $currentStartTime = null){
        $old_start_time = null;
        $old_end_time = null;
        $getLessonDetails = LiveClass::where('meeting_id',$meeting_id)->first();
        if($getLessonDetails!= null){
            $old_start_time = $getLessonDetails->start_time_zoom;
            $old_end_time = $getLessonDetails->end_time_zoom;
        }
        if(($old_start_time != null && $old_start_time != '0') &&  ($old_end_time != null && $old_end_time != '0')){
        }
        else{
            $res = LiveClass::where('meeting_id',$meeting_id)->first();
            $res->start_time_zoom = $currentStartTime;
            $res->update();
        }
        $l_id = null;

        $classes = LiveClass::where('meeting_id',$meeting_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $l_id = $value->id;
                $start_time = $value->start_time_zoom;
                $end_time = $value->end_time_zoom;

                $res = LiveClass::where('id',$l_id)->first();
             

                if($start_time >= $end_time){
                    // $res->is_instructor_joined = '0';
                    $res->is_instructor_joined = 0;
                    // $res->status = 'Missed';
                }
                else{
                    // $res->is_instructor_joined = '1';
                    $res->is_instructor_joined = 1;
                }
                $res->update();
            }
        }
    
    }

    public function rankingLevelList()
    {
        $data['title'] = 'Dashboard';
        $data['navDashboardActiveClass'] = 'active';
        $data['levels'] = RankingLevel::orderBy('serial_no', 'asc')->where('type', RANKING_LEVEL_EARNING)->get();
        return view('instructor.ranking-badge-list', $data);
    }

    public function checkLessonsTest(){
        
        $data['title'] = 'Class Schedule';
        $instructor_user_id=auth()->id();
      
        $getInstructor = Instructor::where('user_id',$instructor_user_id)->first();
        $instructor_id = $getInstructor->id;
        $today_date = date('Y-m-d');
        $getIndividualClasses = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })->orderBy('class_date','ASC')
          ->orderBy('class_time','ASC')->paginate(25);

          $getGroupClasses = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Group');
        })->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')
        ->groupBy('class_id')
        ->paginate(25);

        $data['getIndividualClasses'] = $getIndividualClasses;
        dd("123");
        $data['getGroupClasses'] = $getGroupClasses;
        $data['base_url'] = url('/');

      
      
     }

}
