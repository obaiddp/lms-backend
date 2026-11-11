<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AffiliateHistory;
use App\Models\Blog;
use App\Models\BookingHistory;
use App\Models\ClassDetail;
use App\Models\LiveClass;
use App\Models\LiveClassStudentLink;
use App\Models\Course;
use App\Models\Course_lecture;
use App\Models\CourseInstructor;
use App\Models\InstructorAttendance;
use App\Models\ZoomSetting;
use App\Models\Discussion;
use App\Models\Coordinator;
use App\Models\CoordinatorCourse;
use App\Models\Instructor;
use Illuminate\Support\Str;
use App\Models\Order_item;
use App\Models\Enrollment;
use App\Models\Course_lecture_views;
use App\Models\CourseResource;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Review;
use App\Models\Assignment;
use App\Models\AssignmentSubmit;
use App\Models\Exam;

use App\Models\Lesson;
use App\Models\NoticeBoard;
use App\Models\Order;
use App\Models\Addon\Product\ProductReview;
use App\Models\Bundle;
use App\Models\Question;
use App\Models\Question_option;
use App\Models\Refund;
use App\Models\Student_certificate;
use App\Models\Certificate;
use App\Models\Certificate_by_instructor;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Notification;
use Ramsey\Uuid\Uuid;
use App\Libraries\Bbb;
use App\Models\RankingLevel;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiStatusTrait;
use Carbon\Carbon;


use Illuminate\Support\Facades\Log;
use App\Http\Requests\Student\ProfileRequest;
use App\Http\Services\EmailSendService;
use App\Models\Wishlist;
use App\Models\City;
use App\Models\Country;
use App\Models\Chat;
use App\Events\ChatEvent;
use Illuminate\Support\Facades\Mail;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Http\Requests\Student\TicketRequest;
use App\Models\SupportTicketQuestion;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketMessages;
use App\Models\TicketPriority;
use App\Models\TicketRelatedService;
use App\Models\BlogTag;
use App\Models\Bank;
use App\Models\Take_exam;
use App\Models\AffiliateRequest;
use App\Models\UserPackage;
use App\Models\Answer;
use App\Models\InstructorFeature;

use App\Models\Product;
use App\Models\InstructorProcedure;
use App\Models\Organization;
use App\Models\CartManagement;
use App\Models\State;
use App\Models\Student;
use App\Models\User;



class DashboardController extends Controller
{
    public function __construct()
    {
        $this->bb=new Bbb();
    }

    use ApiStatusTrait;
    public function dashboard()
    {
        //Start::  Cancel Consultation Money Calculation
        $cancelConsultationOrderItemIds = BookingHistory::where(['instructor_user_id' => Auth::id(), 'status' => BOOKING_HISTORY_STATUS_CANCELLED, 'send_back_money_status' => SEND_BACK_MONEY_STATUS_YES])
            ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
            ->whereHas('order', function ($q) {
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
        })->groupBy("course_id")->selectRaw("COUNT(course_id) as total_course_id, course_id")->orderByRaw("COUNT(course_id) DESC")->with('course')->first();

        $data['recentCourses'] = Course::whereIn('id', $userCourseIds)->latest()->limit(2)->get();
        $data['updatedCourses'] = Course::whereIn('id', $userCourseIds)->orderBy('updated_at', 'desc')->limit(2)->get();

        // Start:: Sale Statistics
        $months = collect([]);
        $totalPrice = collect([]);

        //Start::  Cancel Consultation Money Calculation
        $cancelConsultationOrderItemIds = BookingHistory::where(['instructor_user_id' => Auth::id(), 'status' => 2, 'send_back_money_status' => SEND_BACK_MONEY_STATUS_YES])
            ->whereHas('order', function ($q) {
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
        return $this->success($data);
    }

    // public function UpdateInstructorMeetingStatusBackUp(Request $request)
    // {
    //     $user = Auth::user();

    //     // Check authentication and role
    //     if (!$user) {
    //         return response()->json(['message' => 'User Is Not Logged In'], 401);
    //     }

    //     if ($user->role != 2) { // Instructor
    //         return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 401);
    //     }


    //     $messages = [
    //           'lesson_id.required' => __('The Lesson ID  Is Required'),
    //         'meeting_id.required' => __('The Meeting ID Is Required'),
          
    //     ];
    
    //     $validator = Validator::make($request->all(), [
    //           'lesson_id' => 'required',
    //         'meeting_id' => 'required',
          
    //     ],$messages);
     
    //     if($validator->fails()){
         
    //         return response(['status'=>0,'error' => $validator->errors()],500);
    //     }


      

    //     $checkLesson = ClassDetail::where('id',$request->lesson_id)->first();
    //     if($checkLesson == null){
    //           return response()->json(['message' => 'Invalid Lesson ID.'], 302);
    //     }

    //     $checkMeeting = ClassDetail::where('meeting_id',$request->meeting_id)->first();
    //     if($checkMeeting == null){
    //           return response()->json(['message' => 'Invalid Meeting ID.'], 302);
    //     }

    //     if($request->group_id){
    //         $checkGroup = ClassDetail::where('group_id',$request->group_id)->first();
    //         if($checkGroup == null){
    //               return response()->json(['message' => 'Invalid Group ID.'], 302);
    //         } 
    //          $check2 = ClassDetail::where('group_id',$request->group_id)->where('id',$request->lesson_id)->
    //          where('meeting_id',$request->meeting_id)->first();
    //          if($check2 == null){
    //              return response()->json(['message' => 'Invalid Group ID and meeting ID For current Class ID.'], 302);
    //          }
    //     }
    //     else{
    //          $check2 = ClassDetail::where('id',$request->lesson_id)->
    //          where('meeting_id',$request->meeting_id)->first();
    //          if($check2 == null){
    //              return response()->json(['message' => 'Invalid Meeting ID For current Class ID.'], 302);
    //          }
    //     }
    //      if($request->group_id){
    //         $result=updateTutorJoinStatus($request->lesson_id,$request->meeting_id,$request->group_id); 
    //      }
    //      else{
    //          $result=updateTutorJoinStatus($request->lesson_id,$request->meeting_id,null); 
    //      }
  
    //      if($result){
    //           return response()->json(['message' => 'Join Status Of Instructor Updated.'], 200);
          
    //      }
    //      else{
    //           return response()->json(['message' => 'Something Went Wrong.'], 500);
    //      }  
    
    // }





      public function UpdateInstructorMeetingStatus(Request $request)
    {
        $user = Auth::user();

        // Check authentication and role
        if (!$user) {
            return response()->json(['message' => 'User Is Not Logged In'], 401);
        }

        if ($user->role != 2 && $user->role != 5) { // Instructor
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 401);
        }


        $messages = [
              'lesson_id.required' => __('The Lesson ID  Is Required'),
            'meeting_id.required' => __('The Meeting ID Is Required'),
          
        ];
    
        $validator = Validator::make($request->all(), [
              'lesson_id' => 'required',
            'meeting_id' => 'required',
          
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }


      

        $checkLesson = ClassDetail::where('id',$request->lesson_id)->first();
        if($checkLesson == null){
              return response()->json(['message' => 'Invalid Lesson ID.'], 302);
        }

        $checkMeeting = ClassDetail::where('meeting_id',$request->meeting_id)->first();
        if($checkMeeting == null){
              return response()->json(['message' => 'Invalid Meeting ID.'], 302);
        }

        if($request->group_id){
            $checkGroup = ClassDetail::where('group_id',$request->group_id)->first();
            if($checkGroup == null){
                  return response()->json(['message' => 'Invalid Group ID.'], 302);
            } 
             $check2 = ClassDetail::where('group_id',$request->group_id)->where('id',$request->lesson_id)->
             where('meeting_id',$request->meeting_id)->first();
             if($check2 == null){
                 return response()->json(['message' => 'Invalid Group ID and meeting ID For current Class ID.'], 302);
             }
        }
        else{
             $check2 = ClassDetail::where('id',$request->lesson_id)->
             where('meeting_id',$request->meeting_id)->first();
             if($check2 == null){
                 return response()->json(['message' => 'Invalid Meeting ID For current Class ID.'], 302);
             }
        }

        if($user->role == 2){
            if($request->group_id){
                $result=updateTutorJoinStatus($request->lesson_id,$request->meeting_id,$request->group_id); 
             }
             else{
                 $result=updateTutorJoinStatus($request->lesson_id,$request->meeting_id,null); 
             }
    
             if($result){
                  return response()->json(['message' => 'Join Status Of Instructor Updated.'], 200);

             }
             else{
                  return response()->json(['message' => 'Something Went Wrong.'], 500);
             }  
        }
        else{
            if($request->group_id){
                $result=updateCoordinatorJoinStatus($request->lesson_id,$request->meeting_id,$request->group_id); 
             }
             else{
                 $result=updateCoordinatorJoinStatus($request->lesson_id,$request->meeting_id,null); 
             }
    
             if($result){
                  return response()->json(['message' => 'Join Status Of Coordinator Updated.'], 200);

             }
             else{
                  return response()->json(['message' => 'Something Went Wrong.'], 500);
             }  
        }
    
    }





    //  public function UpdateInstructorLiveMeetingStatusBackUp(Request $request)
    // {
    //     $user = Auth::user();

    //     // Check authentication and role
    //     if (!$user) {
    //         return response()->json(['message' => 'User Is Not Logged In'], 200);
    //     }

    //     if ($user->role != 2) { // Instructor
    //         return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
    //     }


    //     $messages = [
    //           'lesson_id.required' => __('The Lesson ID  Is Required'),
    //         'meeting_id.required' => __('The Meeting ID Is Required'),
          
    //     ];
    
    //     $validator = Validator::make($request->all(), [
    //            'lesson_id' => 'required',
    //         'meeting_id' => 'required',
         
    //     ],$messages);
     
    //     if($validator->fails()){
         
    //         return response(['status'=>0,'error' => $validator->errors()],500);
    //     }

      
    //     $checkLesson = LiveClass::where('id',$request->lesson_id)->first();
    //     if($checkLesson == null){
    //           return response()->json(['message' => 'Invalid Lesson ID.'], 302);
    //     }


    //     $checkMeeting = LiveClass::where('meeting_id',$request->meeting_id)->first();
    //     if($checkMeeting == null){
    //           return response()->json(['message' => 'Invalid Meeting ID.'], 302);
    //     }


    //          $check2 = LiveClass::where('id',$request->lesson_id)->
    //          where('meeting_id',$request->meeting_id)->first();
    //          if($check2 == null){
    //              return response()->json(['message' => 'Invalid Meeting ID For current Class ID.'], 302);
    //          }
        
    
    //          $result=updateTutorJoinStatusLiveClass($request->lesson_id,$request->meeting_id,null); 
  
    //      if($result){
    //           return response()->json(['message' => 'Join Status Of Instructor Updated.'], 200);
          
    //      }
    //      else{
    //           return response()->json(['message' => 'Something Went Wrong.'], 500);
    //      }  
    
    // }


       public function UpdateInstructorLiveMeetingStatus(Request $request)
    {
        $user = Auth::user();

        // Check authentication and role
        if (!$user) {
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

         if ($user->role != 2 && $user->role != 5) { 
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
        }


        $messages = [
              'lesson_id.required' => __('The Lesson ID  Is Required'),
            'meeting_id.required' => __('The Meeting ID Is Required'),
          
        ];
    
        $validator = Validator::make($request->all(), [
               'lesson_id' => 'required',
            'meeting_id' => 'required',
         
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }

      
        $checkLesson = LiveClass::where('id',$request->lesson_id)->first();
        if($checkLesson == null){
              return response()->json(['message' => 'Invalid Lesson ID.'], 302);
        }


        $checkMeeting = LiveClass::where('meeting_id',$request->meeting_id)->first();
        if($checkMeeting == null){
              return response()->json(['message' => 'Invalid Meeting ID.'], 302);
        }


             $check2 = LiveClass::where('id',$request->lesson_id)->
             where('meeting_id',$request->meeting_id)->first();
             if($check2 == null){
                 return response()->json(['message' => 'Invalid Meeting ID For current Class ID.'], 302);
             }
        
    
             if($user->role == 2){
                $result=updateTutorJoinStatusLiveClass($request->lesson_id,$request->meeting_id,null); 

                 if($result){
                      return response()->json(['message' => 'Join Status Of Instructor Updated.'], 200);

                 }
                 else{
                      return response()->json(['message' => 'Something Went Wrong.'], 500);
                 }  
             }
             else{
                $result=updateCoordinatorJoinStatusLiveClass($request->lesson_id,$request->meeting_id,null); 

                 if($result){
                      return response()->json(['message' => 'Join Status Of Coordinator Updated.'], 200);

                 }
                 else{
                      return response()->json(['message' => 'Something Went Wrong.'], 500);
                 }  
             }
           
    
    }



    // public function instructorDashboardDataBackUp()
    // {
    //     $user = Auth::user();

    //     // Check authentication and role
    //     if (!$user) {
    //         return response()->json(['message' => 'User Is Not Logged In'], 200);
    //     }

    //     if ($user->role != 2 && $user->role != 5) { // Instructor
    //         return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
    //     }


    //     if($user->role == 2){
    //           // Initialize the response array
    //         $response = [
    //             'latest_enrolled_courses' => [],
    //             'latest_blogs' => [],
    //             'latest_resources' => [],
    //             'monthly_classes' => [],
    //             'today_live_class' => []
    //         ];

    //         // 1. Latest Enrolled Courses
    //         $courseInstructorIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())
    //             ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
    //             ->whereNull('courses.organization_id')
    //             ->where('course_instructor.status', STATUS_APPROVED)
    //             ->select('course_id')
    //             ->get()
    //             ->pluck('course_id')
    //         ->toArray();

    //         $mainCourseIds = Course::where('user_id', $user->id)
    //             ->select('id')
    //             ->pluck('id')
    //         ->toArray();

    //         $courseInstructorIds = array_unique(array_merge($mainCourseIds, $courseInstructorIds));
    //         $courses = Course::whereIn('id', $courseInstructorIds)
    //             ->whereNull('organization_id')
    //             ->orderBy('id', 'DESC')
    //             ->limit(5)
    //         ->get();

    //         if (!$courses->isEmpty()) {
    //             foreach ($courses as $key => $course) {
    //                 $courseDetailLink = route('instructor-course-details', ['slug' => $course->slug]);
    //                 if($course->course_type == 1 || $course->course_type == 2){
    //                     $courseType = __('Self Learning');
    //                     $response['latest_enrolled_courses'][] = [
    //                     'id' => $key + 1,
    //                     'courseID' => $course->id,
    //                     'courseSlug' => $course->slug,
    //                     'courseName' => $course->title,
    //                     'courseType' => $courseType,
    //                     'TotalLessons' => $course->lessons->count(),
    //                     'TotalLessonsLectures' => $course->lectures->count(),
    //                     // 'course_DetailPage_Link' => $courseDetailLink,
    //                 ];
    //                 }
    //                 else{
    //                      $courseType = __('Live');
    //                     $response['latest_enrolled_courses'][] = [
    //                     'id' => $key + 1,
    //                     'courseID' => $course->id,
    //                     'courseSlug' => $course->slug,
    //                     'courseName' => $course->title,
    //                     'courseType' => $courseType,
    //                     'TotalResources' => $course->resources->count(),
    //                 ];
    //                 }
                


    //             }
    //         }

    //         // 2. Latest Blogs
    //         $blogs = Blog::where('status', 1)
    //             ->orderBy('id', 'DESC')
    //             ->limit(5)
    //         ->get();

    //         if (!$blogs->isEmpty()) {
    //             foreach ($blogs as $blog) {
    //                 $imagePath = getImageFile($blog->image_path);
    //                 $blogDate = Carbon::parse($blog->created_at)->format('d-m-Y');

    //                 $response['latest_blogs'][] = [
    //                     'blog_id' => $blog->id,
    //                     'blog_title' => $blog->title,
    //                     'blog_details' => $blog->details,
    //                     'blog_image' => $imagePath,
    //                     'blog_date' => $blogDate,
    //                 ];
    //             }
    //         }

    //         // 3. Latest Resources
    //         $courseIDs = [];
    //         if (!empty($courseInstructorIds)) {
    //             $courses = Course::whereIn('id', $courseInstructorIds)
    //                 ->whereNull('organization_id')
    //                 ->orderBy('id', 'DESC')
    //                 ->get();

    //             if (!$courses->isEmpty()) {
    //                 foreach ($courses as $course) {
    //                     $courseIDs[] = $course->id;
    //                 }
    //                 $courseIDs = array_unique($courseIDs);
    //             }

    //             if (!empty($courseIDs)) {
    //                 $courseLectures = Course_lecture::whereIn('course_id', $courseIDs)
    //                     ->where('resource_id', '<>', null)
    //                     ->orderBy('id', 'DESC')
    //                     ->limit(10)
    //                     ->get();

    //                 if (!$courseLectures->isEmpty()) {
    //                     foreach ($courseLectures as $lecture) {
    //                         if ($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube') {
    //                             $lectureViewPath = route('resource.view_full_other', ['resource_id' => $lecture->resource_id]);
    //                         } else {
    //                             $lectureViewPath = route('resource.view_full', [
    //                                 'ext' => $lecture->resource?->file_extension,
    //                                 'path' => $lecture->resource?->file_name
    //                             ]);
    //                         }

    //                         $response['latest_resources'][] = [
    //                             'lecture_id' => $lecture->id,
    //                             'lecture_title' => $lecture->title,
    //                             'lecture_type' => $lecture->resource?->category?->name,
    //                             'lecture_view' => $lectureViewPath,
    //                         ];
    //                     }
    //                 }
    //             }
    //         }

    //         // 4. Monthly Classes
    //         $today = Carbon::today();
    //         $todayUpdated = $today->format('Y-m-d');
    //         $sixMonthsAgo = $today->copy()->subMonths(6);
    //         $sixMonthsAgoUpdated = $sixMonthsAgo->format('Y-m-d');

    //         $getInstructor = Instructor::where('user_id', $user->id)->first();
    //         $instructor_id = $getInstructor->id;

    //         for ($i = 5; $i >= 0; $i--) {
    //             $date = $today->copy()->subMonths($i)->startOfMonth();
    //             $monthNumber = $date->format('m');

    //             $MonthCompleteClasses = ClassDetail::where('instructor_id', $instructor_id)
    //                 ->whereDate('class_date', '>=', $sixMonthsAgoUpdated)
    //                 ->whereDate('class_date', '<=', $todayUpdated)
    //                 ->whereMonth('class_date', $monthNumber)
    //                 ->where('status', 'Completed')
    //                 ->orderBy('id', 'DESC')
    //                 ->orderBy('class_date', 'ASC')
    //                 ->groupBy('class_id')
    //                 ->get()
    //                 ->count();

    //             $MonthMissedClasses = ClassDetail::where('instructor_id', $instructor_id)
    //                 ->whereDate('class_date', '>=', $sixMonthsAgoUpdated)
    //                 ->whereDate('class_date', '<=', $todayUpdated)
    //                 ->whereMonth('class_date', $monthNumber)
    //                 ->where('status', 'Missed')
    //                 ->orderBy('id', 'DESC')
    //                 ->orderBy('class_date', 'ASC')
    //                 ->groupBy('class_id')
    //                 ->get()
    //                 ->count();

    //             $response['monthly_classes'][] = [
    //                 'month_Number' => $date->format('m'),
    //                 'month_Name' => $date->format('M'),
    //                 'total_Completed_Classes' => $MonthCompleteClasses,
    //                 'total_Missed_Classes' => $MonthMissedClasses,
    //             ];
    //         }

    //         // 5. Today's Live Class
    //         $today_date = date('Y-m-d');
    //         // $getTodayLiveClass = ClassDetail::where('instructor_id', $instructor_id)
    //         //     ->where('class_date', $today_date)
    //         //     ->whereNotIn('status', ['Completed', 'Missed'])
    //         //     ->orderBy('class_date', 'ASC')
    //         //     ->orderBy('class_time', 'ASC')
    //         //     ->groupBy('class_id')
    //         //     ->first()
    //         $now          = Carbon::now()->format('H:i');
    //         $allToday = ClassDetail::where('instructor_id', $instructor_id)
    //                   ->where('class_date', $today_date)
    //                   ->get();
    //           // 2) pick out the “waiting” ones
    //         $waiting = $allToday->whereIn('status', ['Pending','Scheduled','In Progress']);
    //         if ($waiting->isNotEmpty()) {
            
    //             if ($waiting->count() === 1) {
    //                 $getTodayLiveClass = $waiting->first();
    //             }
    //             else {
    //                 // 3) more than one – pick the one whose class_time is nearest to now
    //                 $getTodayLiveClass = $waiting->sortBy(function($lesson) use ($now) {
    //                     return abs(strtotime($lesson->class_time) - strtotime($now));
    //                 })->first();
    //             }
    //         }
    //         else {
    //             // 4) no waiting: find latest completed or missed
    //             $fallback = $allToday->whereIn('status', ['Completed','Missed']);
    //             $getTodayLiveClass = $fallback->sortByDesc('class_time')->first();
    //         }


    //         if ($getTodayLiveClass) {
    //             $BBB_Meeting_Details = array();
    //             $Zoom_Meeting_Details = array();
    //             $Meet_Meeting_Details = array();

    //             $btnStatus = __($getTodayLiveClass->status);
    //             $DateTime = changetimeByZone($getTodayLiveClass->class_date.' '.date('h:i A',strtotime($getTodayLiveClass->class_time)),$getTodayLiveClass->instructor_id,'instructor');
    //             $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$getTodayLiveClass->instructor_id,'instructor');
    //             $updated_time = date('h:i A',strtotime($DateTime));
    //             $lsntime = strtotime($updated_time);
    //             $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //             $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //             $curdate = strtotime($curdate);
    //             $lsndate = date('Y-m-d',strtotime($DateTime));
    //             $lsndate = strtotime($lsndate); 
    //             $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //             $curtime = strtotime($updated_cur_time);
    //             $lesson_duration = $getTodayLiveClass->class?->duration;
    //             $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //             $lsnendtime = $lsntimestamp;


            
    //             // Determine button status and meeting details (simplified for brevity)
    //             // This would include all the logic from the original TodayLiveClass method
    //             // to determine the button status and meeting details


    //             if ($getTodayLiveClass->class?->type == 'Group') {
                
    //                 // Group class logic
    //                 if($curdate <  $lsndate) {
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                 }
    //                 elseif($curdate == $lsndate){
    //                     if($curtime < $lsntime) {
    //                          $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->InstructorURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
    //                        $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                        
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                         $Zoom_Meeting_Details = [
    //                         'group_id' =>$getTodayLiveClass->group_id,
    //                         'meeting_id' =>$getTodayLiveClass->meeting_id,
    //                         'lesson_id' =>$getTodayLiveClass->id,
    //                         'user_type' =>__('Tutor'),
    //                         'user_name' =>$getTodayLiveClass->instructor?->user?->name,
    //                         'user_id' =>$getTodayLiveClass->instructor_id,
    //                         ];
    //                     }
    //                         else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
    //                                     $btnStatus = __($getTodayLiveClass->status);

    //                                     $BBB_Meeting_Details = [
    //                                         'group_id' =>$getTodayLiveClass->group_id,
    //                                         'tutor_uri' =>$getTodayLiveClass->InstructorURI,
    //                                         'lesson_id' =>$getTodayLiveClass->id,
    //                                         'meeting_id' =>$getTodayLiveClass->meeting_id,
                                        
    //                                     ];
                                    
    //                         }
    //                         else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
    //                                  $btnStatus = __($getTodayLiveClass->status);

    //                                     $Meet_Meeting_Details = [
    //                                         'group_id' =>$getTodayLiveClass->group_id,
    //                                         'tutor_uri' =>$getTodayLiveClass->google_meet_link,
    //                                         'lesson_id' =>$getTodayLiveClass->id,
    //                                         'meeting_id' =>$getTodayLiveClass->meeting_id,
                                        
    //                                     ];
                                    
    //                         }

    //                         else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
    //                                 $btnStatus = __($getTodayLiveClass->status);

    //                                 $Zoom_Meeting_Details = [
    //                                     'group_id' =>$getTodayLiveClass->group_id,
    //                                     'meeting_id' =>$getTodayLiveClass->meeting_id,
    //                                     'lesson_id' =>$getTodayLiveClass->id,
    //                                     'user_type' =>__('Tutor'),
    //                                     'user_name' =>$getTodayLiveClass->instructor?->user?->name,
    //                                     'user_id' =>$getTodayLiveClass->instructor_id,
                                    
    //                                 ];
    //                         }
    //                         else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') {
    //                                 $btnStatus = __($getTodayLiveClass->status);

    //                                 $BBB_Meeting_Details = [
    //                                     'group_id' =>$getTodayLiveClass->group_id,
    //                                     'tutor_uri' =>$getTodayLiveClass->InstructorURI,
    //                                     'lesson_id' =>$getTodayLiveClass->id,
    //                                     'meeting_id' =>$getTodayLiveClass->meeting_id,
                                    
    //                                 ];
    //                         }
    //                         else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
    //                                 $btnStatus = __($getTodayLiveClass->status);

    //                                 $Meet_Meeting_Details = [
    //                                     'group_id' =>$getTodayLiveClass->group_id,
    //                                     'tutor_uri' =>$getTodayLiveClass->google_meet_link,
    //                                     'lesson_id' =>$getTodayLiveClass->id,
    //                                     'meeting_id' =>$getTodayLiveClass->meeting_id,
    //                                 ];
    //                         }

    //                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
    //                             $btnStatus = __($getTodayLiveClass->status);
    //                         }
    //                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'BBB') {
    //                             $btnStatus = __($getTodayLiveClass->status);
    //                         }
    //                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
    //                             $btnStatus = __($getTodayLiveClass->status);
    //                         }
    //                         else{
    //                             $btnStatus = __($getTodayLiveClass->status);
    //                         }
    //                 }
    //                 else{
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                 }

    //                 if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
    //                         $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                         if($zoomSettings){
    //                             $sdk_key = $zoomSettings->sdk_key;
    //                             $sdk_secret = $zoomSettings->sdk_secret;
    //                             $api_key = $zoomSettings->api_key;
    //                             $api_secret = $zoomSettings->api_secret;
    //                             $api_jwt_token = $zoomSettings->api_jwt_token;
    //                         }
    //                         else{
    //                             $sdk_key = null;
    //                             $sdk_secret = null;
    //                             $api_key = null;
    //                             $api_secret = null;
    //                             $api_jwt_token = null;
    //                         }
    //                     $response['today_live_class'] = [
    //                         'id'=>$getTodayLiveClass->id,
    //                         'class_id'=>$getTodayLiveClass->class_id,
    //                         'type' => 'Zoom',
    //                         'courseID'=>$getTodayLiveClass->course_id,
    //                         'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
    //                         'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
    //                         'Date_Time'=>$classTime,
    //                         'Btn_Status'=>$btnStatus,
                        
    //                         'learningTool'=>$getTodayLiveClass->class?->learning_tool,
    //                         'classType'=>$getTodayLiveClass->class?->type,
                        
    //                         'zoomSdkKey'=> $sdk_key,
    //                         'zoomSdkSecret'=> $sdk_secret,
                        
    //                         'Meeting_Details'=>$Zoom_Meeting_Details,
    //                     ];
    //                 }
    //                 else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
    //                     $response['today_live_class'] = [
    //                         'id'=>$getTodayLiveClass->id,
    //                         'class_id'=>$getTodayLiveClass->class_id,
    //                         'type' => 'Google_Meet',
    //                         'courseID'=>$getTodayLiveClass->course_id,
    //                         'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
    //                         'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
    //                         'Date/Time'=>$classTime,
    //                         'Btn_Status'=>$btnStatus,
                        
    //                         'learningTool'=>$getTodayLiveClass->class?->learning_tool,
    //                         'classType'=>$getTodayLiveClass->class?->type,
    //                         'Meeting_Details'=>$Meet_Meeting_Details,
    //                     ];
    //                 }
    //                 else{
    //                     $response['today_live_class'] = [
    //                         'id'=>$getTodayLiveClass->id,
    //                         'class_id'=>$getTodayLiveClass->class_id,
    //                         'type' => 'BBB',
    //                         'courseID'=>$getTodayLiveClass->course_id,
    //                         'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
    //                         'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
    //                         'Date/Time'=>$classTime,
    //                         'Btn_Status'=>$btnStatus,
                        
    //                         'learningTool'=>$getTodayLiveClass->class?->learning_tool,
    //                         'classType'=>$getTodayLiveClass->class?->type,
    //                         'Meeting_Details'=>$BBB_Meeting_Details,
    //                     ];
    //                 }
    //             } 
    //             else {
                

    //                 if($curdate <  $lsndate) {
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                 }
    //                 elseif($curdate == $lsndate){
                    
    //                     if($curtime < $lsntime) {
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->InstructorURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                     }

    //                     else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                        
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                         $Zoom_Meeting_Details = [
    //                             'lesson_id' =>$getTodayLiveClass->id,
    //                             'meeting_id' =>$getTodayLiveClass->meeting_id,
    //                             'user_type' =>__('Tutor'),
    //                             'user_name' =>$getTodayLiveClass->instructor?->user?->name,
    //                             'user_id' =>$getTodayLiveClass->instructor_id,
    //                         ];
    //                     }
    //                     else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                         $BBB_Meeting_Details = [
    //                             'tutor_uri' =>$getTodayLiveClass->InstructorURI,
    //                             'lesson_id' =>$getTodayLiveClass->id,
    //                             'meeting_id' =>$getTodayLiveClass->meeting_id,
    //                         ];
                        
    //                     }
    //                     else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                         $Meet_Meeting_Details = [
    //                             'tutor_uri' =>$getTodayLiveClass->google_meet_link,
    //                             'lesson_id' =>$getTodayLiveClass->id,
    //                             'meeting_id' =>$getTodayLiveClass->meeting_id,
    //                         ];
                        
    //                     }
    //                     else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'BBB') {
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                     else{
    //                         $btnStatus = __($getTodayLiveClass->status);
    //                     }
    //                 }
    //                 else{
    //                     $btnStatus = __($getTodayLiveClass->status);
    //                 }

    //                 if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
    //                         $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                         if($zoomSettings){
    //                             $sdk_key = $zoomSettings->sdk_key;
    //                             $sdk_secret = $zoomSettings->sdk_secret;
    //                             $api_key = $zoomSettings->api_key;
    //                             $api_secret = $zoomSettings->api_secret;
    //                             $api_jwt_token = $zoomSettings->api_jwt_token;
    //                         }
    //                         else{
    //                             $sdk_key = null;
    //                             $sdk_secret = null;
    //                             $api_key = null;
    //                             $api_secret = null;
    //                             $api_jwt_token = null;
    //                         }
    //                     $response['today_live_class'] = [
    //                         'id'=>$getTodayLiveClass->id,
    //                         'type' => 'Zoom',
    //                         'courseID'=>$getTodayLiveClass->course_id,
    //                         'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
    //                         'studentID'=>$getTodayLiveClass->student_id,
    //                         'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
    //                         'Date_Time'=>$classTime,
    //                         'Btn_Status'=>$btnStatus,
                        
    //                         'learningTool'=>$getTodayLiveClass->class?->learning_tool,
    //                         'classType'=>$getTodayLiveClass->class?->type,
                        
    //                         'zoomSdkKey'=> $sdk_key,
    //                         'zoomSdkSecret'=> $sdk_secret,
                        
    //                         'Meeting_Details'=>$Zoom_Meeting_Details,
    //                     ];
    //                 }
    //                 else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
    //                     $response['today_live_class'] = [
    //                         'id'=>$getTodayLiveClass->id,
    //                         'type' => 'Google_Meet',
    //                         'courseID'=>$getTodayLiveClass->course_id,
    //                         'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
    //                         'studentID'=>$getTodayLiveClass->student_id,
    //                         'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
    //                         'Date/Time'=>$classTime,
    //                         'Btn_Status'=>$btnStatus,
    //                         'learningTool'=>$getTodayLiveClass->class?->learning_tool,
    //                         'classType'=>$getTodayLiveClass->class?->type,
    //                         'Meeting_Details'=>$Meet_Meeting_Details,
    //                     ];
    //                 }
    //                 else{
    //                     $response['today_live_class'] = [
    //                         'id'=>$getTodayLiveClass->id,
    //                         'type' => 'BBB',
    //                         'courseID'=>$getTodayLiveClass->course_id,
    //                         'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
    //                         'studentID'=>$getTodayLiveClass->student_id,
    //                         'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
    //                         'Date/Time'=>$classTime,
    //                         'Btn_Status'=>$btnStatus,
    //                         'learningTool'=>$getTodayLiveClass->class?->learning_tool,
    //                         'classType'=>$getTodayLiveClass->class?->type,
    //                         'Meeting_Details'=>$BBB_Meeting_Details,
    //                     ];
    //                 }
    //             }
    //         }

    //         return response()->json($response);
    //     }
    //     else{   // Role  = 5

    //     }



    // }



        public function instructorDashboardData()
    {
        $user = Auth::user();

        // Check authentication and role
        if (!$user) {
            return response()->json(['message' => 'User Is Not Logged In'], 403);
        }

        if ($user->role != 2 && $user->role != 5) { // Instructor
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 403);
        }


        if($user->role == 2){
              // Initialize the response array
            $response = [
                'latest_enrolled_courses' => [],
                'latest_blogs' => [],
                'latest_resources' => [],
                'monthly_classes' => [],
                'today_live_class' => [],
                'role' => $user->role,
            ];

            // 1. Latest Enrolled Courses
            $courseInstructorIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())
                ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
                ->whereNull('courses.organization_id')
                ->where('course_instructor.status', STATUS_APPROVED)
                ->select('course_id')
                ->get()
                ->pluck('course_id')
            ->toArray();

            $mainCourseIds = Course::where('user_id', $user->id)
                ->select('id')
                ->pluck('id')
            ->toArray();

            $courseInstructorIds = array_unique(array_merge($mainCourseIds, $courseInstructorIds));
            $courses = Course::whereIn('id', $courseInstructorIds)
                ->whereNull('organization_id')
                ->orderBy('id', 'DESC')
                ->limit(5)
            ->get();

            if (!$courses->isEmpty()) {
                foreach ($courses as $key => $course) {
                    $courseDetailLink = route('instructor-course-details', ['slug' => $course->slug]);
                    if($course->course_type == 1 || $course->course_type == 2){
                        $courseType = __('Self Learning');
                        $response['latest_enrolled_courses'][] = [
                        'id' => $key + 1,
                        'courseID' => $course->id,
                        'courseSlug' => $course->slug,
                        'courseName' => $course->title,
                        'courseType' => $courseType,
                        'TotalLessons' => $course->lessons->count(),
                        'TotalLessonsLectures' => $course->lectures->count(),
                        // 'course_DetailPage_Link' => $courseDetailLink,
                    ];
                    }
                    else{
                         $courseType = __('Live');
                        $response['latest_enrolled_courses'][] = [
                        'id' => $key + 1,
                        'courseID' => $course->id,
                        'courseSlug' => $course->slug,
                        'courseName' => $course->title,
                        'courseType' => $courseType,
                        'TotalResources' => $course->resources->count(),
                    ];
                    }
                


                }
            }

            // 2. Latest Blogs
            $blogs = Blog::where('status', 1)
                ->orderBy('id', 'DESC')
                ->limit(5)
            ->get();

            if (!$blogs->isEmpty()) {
                foreach ($blogs as $blog) {
                    $imagePath = getImageFile($blog->image_path);
                    $blogDate = Carbon::parse($blog->created_at)->format('d-m-Y');

                    $response['latest_blogs'][] = [
                        'blog_id' => $blog->id,
                        'blog_title' => $blog->title,
                        'blog_details' => $blog->details,
                        'blog_image' => $imagePath,
                        'blog_date' => $blogDate,
                    ];
                }
            }

            // 3. Latest Resources
            $courseIDs = [];
            if (!empty($courseInstructorIds)) {
                $courses = Course::whereIn('id', $courseInstructorIds)
                    ->whereNull('organization_id')
                    ->orderBy('id', 'DESC')
                    ->get();

                if (!$courses->isEmpty()) {
                    foreach ($courses as $course) {
                        $courseIDs[] = $course->id;
                    }
                    $courseIDs = array_unique($courseIDs);
                }

                if (!empty($courseIDs)) {
                    $courseLectures = Course_lecture::whereIn('course_id', $courseIDs)
                        ->where('resource_id', '<>', null)
                        ->orderBy('id', 'DESC')
                        ->limit(10)
                        ->get();

                    if (!$courseLectures->isEmpty()) {
                        foreach ($courseLectures as $lecture) {
                            if ($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube') {
                                $lectureViewPath = route('resource.view_full_other', ['resource_id' => $lecture->resource_id]);
                            } else {
                                $lectureViewPath = route('resource.view_full', [
                                    'ext' => $lecture->resource?->file_extension,
                                    'path' => $lecture->resource?->file_name
                                ]);
                            }

                            $response['latest_resources'][] = [
                                'lecture_id' => $lecture->id,
                                'lecture_title' => $lecture->title,
                                'lecture_type' => $lecture->resource?->category?->name,
                                'lecture_view' => $lectureViewPath,
                            ];
                        }
                    }
                }
            }

            // 4. Monthly Classes
            $today = Carbon::today();
            $todayUpdated = $today->format('Y-m-d');
            $sixMonthsAgo = $today->copy()->subMonths(6);
            $sixMonthsAgoUpdated = $sixMonthsAgo->format('Y-m-d');

            $getInstructor = Instructor::where('user_id', $user->id)->first();
            $instructor_id = $getInstructor->id;

            for ($i = 5; $i >= 0; $i--) {
                $date = $today->copy()->subMonths($i)->startOfMonth();
                $monthNumber = $date->format('m');

                $MonthCompleteClasses = ClassDetail::where('instructor_id', $instructor_id)
                    ->whereDate('class_date', '>=', $sixMonthsAgoUpdated)
                    ->whereDate('class_date', '<=', $todayUpdated)
                    ->whereMonth('class_date', $monthNumber)
                    ->where('status', 'Completed')
                    ->orderBy('id', 'DESC')
                    ->orderBy('class_date', 'ASC')
                    ->groupBy('class_id')
                    ->get()
                    ->count();

                $MonthMissedClasses = ClassDetail::where('instructor_id', $instructor_id)
                    ->whereDate('class_date', '>=', $sixMonthsAgoUpdated)
                    ->whereDate('class_date', '<=', $todayUpdated)
                    ->whereMonth('class_date', $monthNumber)
                    ->where('status', 'Missed')
                    ->orderBy('id', 'DESC')
                    ->orderBy('class_date', 'ASC')
                    ->groupBy('class_id')
                    ->get()
                    ->count();

                $response['monthly_classes'][] = [
                    'month_Number' => $date->format('m'),
                    'month_Name' => $date->format('M'),
                    'total_Completed_Classes' => $MonthCompleteClasses,
                    'total_Missed_Classes' => $MonthMissedClasses,
                ];
            }

            // 5. Today's Live Class
            $today_date = date('Y-m-d');
            // $getTodayLiveClass = ClassDetail::where('instructor_id', $instructor_id)
            //     ->where('class_date', $today_date)
            //     ->whereNotIn('status', ['Completed', 'Missed'])
            //     ->orderBy('class_date', 'ASC')
            //     ->orderBy('class_time', 'ASC')
            //     ->groupBy('class_id')
            //     ->first()
            $now          = Carbon::now()->format('H:i');
            $allToday = ClassDetail::where('instructor_id', $instructor_id)
                      ->where('class_date', $today_date)
                      ->get();
              // 2) pick out the “waiting” ones
            $waiting = $allToday->whereIn('status', ['Pending','Scheduled','In Progress']);
            if ($waiting->isNotEmpty()) {
            
                if ($waiting->count() === 1) {
                    $getTodayLiveClass = $waiting->first();
                }
                else {
                    // 3) more than one – pick the one whose class_time is nearest to now
                    $getTodayLiveClass = $waiting->sortBy(function($lesson) use ($now) {
                        return abs(strtotime($lesson->class_time) - strtotime($now));
                    })->first();
                }
            }
            else {
                // 4) no waiting: find latest completed or missed
                $fallback = $allToday->whereIn('status', ['Completed','Missed']);
                $getTodayLiveClass = $fallback->sortByDesc('class_time')->first();
            }


            if ($getTodayLiveClass) {
                $BBB_Meeting_Details = array();
                $Zoom_Meeting_Details = array();
                $Meet_Meeting_Details = array();

                $btnStatus = __($getTodayLiveClass->status);
                $DateTime = changetimeByZone($getTodayLiveClass->class_date.' '.date('h:i A',strtotime($getTodayLiveClass->class_time)),$getTodayLiveClass->instructor_id,'instructor');
                $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$getTodayLiveClass->instructor_id,'instructor');
                $updated_time = date('h:i A',strtotime($DateTime));
                $lsntime = strtotime($updated_time);
                $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                $curdate = strtotime($curdate);
                $lsndate = date('Y-m-d',strtotime($DateTime));
                $lsndate = strtotime($lsndate); 
                $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                $curtime = strtotime($updated_cur_time);
                $lesson_duration = $getTodayLiveClass->class?->duration;
                $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                $lsnendtime = $lsntimestamp;


            
                // Determine button status and meeting details (simplified for brevity)
                // This would include all the logic from the original TodayLiveClass method
                // to determine the button status and meeting details


                if ($getTodayLiveClass->class?->type == 'Group') {
                
                    // Group class logic
                    if($curdate <  $lsndate) {
                        $btnStatus = __($getTodayLiveClass->status);
                    }
                    elseif($curdate == $lsndate){
                        if($curtime < $lsntime) {
                             $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->InstructorURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                           $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                        
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                            $btnStatus = __($getTodayLiveClass->status);
                            $Zoom_Meeting_Details = [
                            'group_id' =>$getTodayLiveClass->group_id,
                            'meeting_id' =>$getTodayLiveClass->meeting_id,
                            'lesson_id' =>$getTodayLiveClass->id,
                            'user_type' =>__('Tutor'),
                            'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                            'user_id' =>$getTodayLiveClass->instructor_id,
                            ];
                        }
                            else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
                                        $btnStatus = __($getTodayLiveClass->status);

                                        $BBB_Meeting_Details = [
                                            'group_id' =>$getTodayLiveClass->group_id,
                                            'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                                            'lesson_id' =>$getTodayLiveClass->id,
                                            'meeting_id' =>$getTodayLiveClass->meeting_id,
                                        
                                        ];
                                    
                            }
                            else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
                                     $btnStatus = __($getTodayLiveClass->status);

                                        $Meet_Meeting_Details = [
                                            'group_id' =>$getTodayLiveClass->group_id,
                                            'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                                            'lesson_id' =>$getTodayLiveClass->id,
                                            'meeting_id' =>$getTodayLiveClass->meeting_id,
                                        
                                        ];
                                    
                            }

                            else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                                    $btnStatus = __($getTodayLiveClass->status);

                                    $Zoom_Meeting_Details = [
                                        'group_id' =>$getTodayLiveClass->group_id,
                                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                                        'lesson_id' =>$getTodayLiveClass->id,
                                        'user_type' =>__('Tutor'),
                                        'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                                        'user_id' =>$getTodayLiveClass->instructor_id,
                                    
                                    ];
                            }
                            else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                                    $btnStatus = __($getTodayLiveClass->status);

                                    $BBB_Meeting_Details = [
                                        'group_id' =>$getTodayLiveClass->group_id,
                                        'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                                        'lesson_id' =>$getTodayLiveClass->id,
                                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                                    
                                    ];
                            }
                            else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                                    $btnStatus = __($getTodayLiveClass->status);

                                    $Meet_Meeting_Details = [
                                        'group_id' =>$getTodayLiveClass->group_id,
                                        'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                                        'lesson_id' =>$getTodayLiveClass->id,
                                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                                    ];
                            }

                            else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                            else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'BBB') {
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                            else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                            else{
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                    }
                    else{
                        $btnStatus = __($getTodayLiveClass->status);
                    }

                    if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
                            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                            if($zoomSettings){
                                $sdk_key = $zoomSettings->sdk_key;
                                $sdk_secret = $zoomSettings->sdk_secret;
                                $api_key = $zoomSettings->api_key;
                                $api_secret = $zoomSettings->api_secret;
                                $api_jwt_token = $zoomSettings->api_jwt_token;
                            }
                            else{
                                $sdk_key = null;
                                $sdk_secret = null;
                                $api_key = null;
                                $api_secret = null;
                                $api_jwt_token = null;
                            }
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'class_id'=>$getTodayLiveClass->class_id,
                            'type' => 'Zoom',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                            'Date_Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                        
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                        
                            'Meeting_Details'=>$Zoom_Meeting_Details,
                        ];
                    }
                    else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'class_id'=>$getTodayLiveClass->class_id,
                            'type' => 'Google_Meet',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$Meet_Meeting_Details,
                        ];
                    }
                    else{
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'class_id'=>$getTodayLiveClass->class_id,
                            'type' => 'BBB',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$BBB_Meeting_Details,
                        ];
                    }
                } 
                else {
                

                    if($curdate <  $lsndate) {
                        $btnStatus = __($getTodayLiveClass->status);
                    }
                    elseif($curdate == $lsndate){
                    
                        if($curtime < $lsntime) {
                        $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                        $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->InstructorURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                        $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                        $btnStatus = __($getTodayLiveClass->status);
                        }

                        else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                        
                            $btnStatus = __($getTodayLiveClass->status);
                            $Zoom_Meeting_Details = [
                                'lesson_id' =>$getTodayLiveClass->id,
                                'meeting_id' =>$getTodayLiveClass->meeting_id,
                                'user_type' =>__('Tutor'),
                                'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                                'user_id' =>$getTodayLiveClass->instructor_id,
                            ];
                        }
                        else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
                            $btnStatus = __($getTodayLiveClass->status);
                            $BBB_Meeting_Details = [
                                'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                                'lesson_id' =>$getTodayLiveClass->id,
                                'meeting_id' =>$getTodayLiveClass->meeting_id,
                            ];
                        
                        }
                        else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
                            $btnStatus = __($getTodayLiveClass->status);
                            $Meet_Meeting_Details = [
                                'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                                'lesson_id' =>$getTodayLiveClass->id,
                                'meeting_id' =>$getTodayLiveClass->meeting_id,
                            ];
                        
                        }
                        else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'BBB') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else{
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                    }
                    else{
                        $btnStatus = __($getTodayLiveClass->status);
                    }

                    if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
                            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                            if($zoomSettings){
                                $sdk_key = $zoomSettings->sdk_key;
                                $sdk_secret = $zoomSettings->sdk_secret;
                                $api_key = $zoomSettings->api_key;
                                $api_secret = $zoomSettings->api_secret;
                                $api_jwt_token = $zoomSettings->api_jwt_token;
                            }
                            else{
                                $sdk_key = null;
                                $sdk_secret = null;
                                $api_key = null;
                                $api_secret = null;
                                $api_jwt_token = null;
                            }
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'type' => 'Zoom',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'studentID'=>$getTodayLiveClass->student_id,
                            'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                            'Date_Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                        
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                        
                            'Meeting_Details'=>$Zoom_Meeting_Details,
                        ];
                    }
                    else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'type' => 'Google_Meet',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'studentID'=>$getTodayLiveClass->student_id,
                            'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$Meet_Meeting_Details,
                        ];
                    }
                    else{
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'type' => 'BBB',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'studentID'=>$getTodayLiveClass->student_id,
                            'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$BBB_Meeting_Details,
                        ];
                    }
                }
            }

            return response()->json($response);
        }
        else{   // Role  = 5
            $response = [
                'latest_enrolled_courses' => [],
                'latest_blogs' => [],
                'latest_resources' => [],
                'monthly_classes' => [],
                'today_live_class' => [],
                'role' => $user->role,
            ];
        
            // Get coordinator ID
            $coordinatorId = Coordinator::where('user_id', $user->id)->value('id');
            if (!$coordinatorId) {
                return response()->json($response);
            }
        
            // Courses handled by coordinator
            $courseIds = CoordinatorCourse::where('coordinator_id', $coordinatorId)
                ->pluck('course_id')
                ->toArray();
        
            // if (empty($courseIds)) {
            //     return response()->json($response);
            // }


            /** =======================
             * 1. Latest Enrolled Courses
             * ======================= */
            $courses = Course::whereIn('id', $courseIds)
                ->whereNull('organization_id')
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get();
            foreach ($courses as $key => $course) {
                $courseType = in_array($course->course_type, [1, 2])
                    ? __('Self Learning')
                    : __('Live');
            
                $response['latest_enrolled_courses'][] = [
                    'id' => $key + 1,
                    'courseID' => $course->id,
                    'courseSlug' => $course->slug,
                    'courseName' => $course->title,
                    'courseType' => $courseType,
                    'TotalResources' => $course->resources->count(),
                ];
            }

            /** =======================
             * 2. Latest Blogs (same as instructor)
             * ======================= */
            $blogs = Blog::where('status', 1)
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->get();

            foreach ($blogs as $blog) {
                $response['latest_blogs'][] = [
                    'blog_id' => $blog->id,
                    'blog_title' => $blog->title,
                    'blog_details' => $blog->details,
                    'blog_image' => getImageFile($blog->image_path),
                    'blog_date' => Carbon::parse($blog->created_at)->format('d-m-Y'),
                ];
            }


            /** =======================
             * 3. Latest Resources (course-based)
             * ======================= */
            $lectures = Course_lecture::whereIn('course_id', $courseIds)
                ->whereNotNull('resource_id')
                ->orderBy('id', 'DESC')
                ->limit(10)
                ->get();

             

            foreach ($lectures as $lecture) {
                $response['latest_resources'][] = [
                    'lecture_id' => $lecture->id,
                    'lecture_title' => $lecture->title,
                    'lecture_type' => $lecture->resource?->category?->name,
                ];
            }



            /** =======================
             * 4. Monthly Classes (course-based)
             * ======================= */

            $today = Carbon::today();
            $todayUpdated = $today->format('Y-m-d');
            $sixMonthsAgo = $today->copy()->subMonths(6);
            $sixMonthsAgoUpdated = $sixMonthsAgo->format('Y-m-d');

            for ($i = 5; $i >= 0; $i--) {
                $date = $today->copy()->subMonths($i)->startOfMonth();
                $monthNumber = $date->format('m');

                $MonthCompleteClasses = ClassDetail::whereIn('course_id', $courseIds)
                    ->whereDate('class_date', '>=', $sixMonthsAgoUpdated)
                    ->whereDate('class_date', '<=', $todayUpdated)
                    ->whereMonth('class_date', $monthNumber)
                    ->where('status', 'Completed')
                    ->orderBy('id', 'DESC')
                    ->orderBy('class_date', 'ASC')
                    ->groupBy('class_id')
                    ->get()
                    ->count();

                $MonthMissedClasses = ClassDetail::whereIn('course_id', $courseIds)
                    ->whereDate('class_date', '>=', $sixMonthsAgoUpdated)
                    ->whereDate('class_date', '<=', $todayUpdated)
                    ->whereMonth('class_date', $monthNumber)
                    ->where('status', 'Missed')
                    ->orderBy('id', 'DESC')
                    ->orderBy('class_date', 'ASC')
                    ->groupBy('class_id')
                    ->get()
                    ->count();

                $response['monthly_classes'][] = [
                    'month_Number' => $date->format('m'),
                    'month_Name' => $date->format('M'),
                    'total_Completed_Classes' => $MonthCompleteClasses,
                    'total_Missed_Classes' => $MonthMissedClasses,
                ];
            }



           
            /** =======================
             * 5. Today’s Live Class (course-based)
             * ======================= */
            $today_date = date('Y-m-d');
            $now          = Carbon::now()->format('H:i');
            $allToday = ClassDetail::whereIn('course_id', $courseIds)
                      ->where('class_date', $today_date)
                      ->get();
              // 2) pick out the “waiting” ones
            $waiting = $allToday->whereIn('status', ['Pending','Scheduled','In Progress']);
            if ($waiting->isNotEmpty()) {
            
                if ($waiting->count() === 1) {
                    $getTodayLiveClass = $waiting->first();
                }
                else {
                    // 3) more than one – pick the one whose class_time is nearest to now
                    $getTodayLiveClass = $waiting->sortBy(function($lesson) use ($now) {
                        return abs(strtotime($lesson->class_time) - strtotime($now));
                    })->first();
                }
            }
            else {
                // 4) no waiting: find latest completed or missed
                $fallback = $allToday->whereIn('status', ['Completed','Missed']);
                $getTodayLiveClass = $fallback->sortByDesc('class_time')->first();
            }


            if ($getTodayLiveClass) {
                $BBB_Meeting_Details = array();
                $Zoom_Meeting_Details = array();
                $Meet_Meeting_Details = array();

                $btnStatus = __($getTodayLiveClass->status);
                $DateTime = changetimeByZone($getTodayLiveClass->class_date.' '.date('h:i A',strtotime($getTodayLiveClass->class_time)),$coordinatorId,'coordinator');
                $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinatorId,'coordinator');
                $updated_time = date('h:i A',strtotime($DateTime));
                $lsntime = strtotime($updated_time);
                $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                $curdate = strtotime($curdate);
                $lsndate = date('Y-m-d',strtotime($DateTime));
                $lsndate = strtotime($lsndate); 
                $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                $curtime = strtotime($updated_cur_time);
                $lesson_duration = $getTodayLiveClass->class?->duration;
                $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                $lsnendtime = $lsntimestamp;


            
                // Determine button status and meeting details (simplified for brevity)
                // This would include all the logic from the original TodayLiveClass method
                // to determine the button status and meeting details


                if ($getTodayLiveClass->class?->type == 'Group') {
                
                    // Group class logic
                    if($curdate <  $lsndate) {
                        $btnStatus = __($getTodayLiveClass->status);
                    }
                    elseif($curdate == $lsndate){
                        if($curtime < $lsntime) {
                             $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->AcademicURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                           $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                        
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                            $btnStatus = __($getTodayLiveClass->status);
                            $Zoom_Meeting_Details = [
                                'group_id' =>$getTodayLiveClass->group_id,
                                'meeting_id' =>$getTodayLiveClass->meeting_id,
                                'lesson_id' =>$getTodayLiveClass->id,
                                'user_type' =>__('Coordinator'),
                                'user_name' =>$user->name,
                                'user_id' =>$coordinatorId,
                            ];
                        }
                            else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
                                $btnStatus = __($getTodayLiveClass->status);
                                $BBB_Meeting_Details = [
                                    'group_id' =>$getTodayLiveClass->group_id,
                                    'tutor_uri' =>$getTodayLiveClass->AcademicURI,
                                    'lesson_id' =>$getTodayLiveClass->id,
                                    'meeting_id' =>$getTodayLiveClass->meeting_id,
                                
                                ];
                                    
                            }
                            else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
                                $btnStatus = __($getTodayLiveClass->status);
                                $Meet_Meeting_Details = [
                                    'group_id' =>$getTodayLiveClass->group_id,
                                    'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                                    'lesson_id' =>$getTodayLiveClass->id,
                                    'meeting_id' =>$getTodayLiveClass->meeting_id,
                                
                                ];
                                    
                            }

                            else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                                    $btnStatus = __($getTodayLiveClass->status);

                                    $Zoom_Meeting_Details = [
                                        'group_id' =>$getTodayLiveClass->group_id,
                                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                                        'lesson_id' =>$getTodayLiveClass->id,
                                        'user_type' =>__('Tutor'),
                                        'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                                        'user_id' =>$getTodayLiveClass->instructor_id,
                                    
                                    ];
                            }
                            else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                                    $btnStatus = __($getTodayLiveClass->status);

                                    $BBB_Meeting_Details = [
                                        'group_id' =>$getTodayLiveClass->group_id,
                                        'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                                        'lesson_id' =>$getTodayLiveClass->id,
                                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                                    
                                    ];
                            }
                            else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                                    $btnStatus = __($getTodayLiveClass->status);

                                    $Meet_Meeting_Details = [
                                        'group_id' =>$getTodayLiveClass->group_id,
                                        'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                                        'lesson_id' =>$getTodayLiveClass->id,
                                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                                    ];
                            }

                            else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                            else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'BBB') {
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                            else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                            else{
                                $btnStatus = __($getTodayLiveClass->status);
                            }
                    }
                    else{
                        $btnStatus = __($getTodayLiveClass->status);
                    }

                    if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
                            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                            if($zoomSettings){
                                $sdk_key = $zoomSettings->sdk_key;
                                $sdk_secret = $zoomSettings->sdk_secret;
                                $api_key = $zoomSettings->api_key;
                                $api_secret = $zoomSettings->api_secret;
                                $api_jwt_token = $zoomSettings->api_jwt_token;
                            }
                            else{
                                $sdk_key = null;
                                $sdk_secret = null;
                                $api_key = null;
                                $api_secret = null;
                                $api_jwt_token = null;
                            }
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'class_id'=>$getTodayLiveClass->class_id,
                            'type' => 'Zoom',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                            'Date_Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                        
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                        
                            'Meeting_Details'=>$Zoom_Meeting_Details,
                        ];
                    }
                    else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'class_id'=>$getTodayLiveClass->class_id,
                            'type' => 'Google_Meet',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$Meet_Meeting_Details,
                        ];
                    }
                    else{
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'class_id'=>$getTodayLiveClass->class_id,
                            'type' => 'BBB',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'no_of_students'=>$getTodayLiveClass->class?->no_of_students,
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$BBB_Meeting_Details,
                        ];
                    }
                } 
                else {
                

                    if($curdate <  $lsndate) {
                        $btnStatus = __($getTodayLiveClass->status);
                    }
                    elseif($curdate == $lsndate){
                    
                        if($curtime < $lsntime) {
                        $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->meeting_id == NULL && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                        $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->InstructorURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                        $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                        $btnStatus = __($getTodayLiveClass->status);
                        }

                        else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                        
                            $btnStatus = __($getTodayLiveClass->status);
                            $Zoom_Meeting_Details = [
                                'lesson_id' =>$getTodayLiveClass->id,
                                'meeting_id' =>$getTodayLiveClass->meeting_id,
                                'user_type' =>__('Tutor'),
                                'user_name' =>$getTodayLiveClass->instructor?->user?->name,
                                'user_id' =>$getTodayLiveClass->instructor_id,
                            ];
                        }
                        else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
                            $btnStatus = __($getTodayLiveClass->status);
                            $BBB_Meeting_Details = [
                                'tutor_uri' =>$getTodayLiveClass->InstructorURI,
                                'lesson_id' =>$getTodayLiveClass->id,
                                'meeting_id' =>$getTodayLiveClass->meeting_id,
                            ];
                        
                        }
                        else if(($curtime == $lsntime || $curtime > $lsntime && $curtime < $lsnendtime) && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
                            $btnStatus = __($getTodayLiveClass->status);
                            $Meet_Meeting_Details = [
                                'tutor_uri' =>$getTodayLiveClass->google_meet_link,
                                'lesson_id' =>$getTodayLiveClass->id,
                                'meeting_id' =>$getTodayLiveClass->meeting_id,
                            ];
                        
                        }
                        else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'BBB') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $getTodayLiveClass->class->learning_tool == 'Google_Meet') {
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                        else{
                            $btnStatus = __($getTodayLiveClass->status);
                        }
                    }
                    else{
                        $btnStatus = __($getTodayLiveClass->status);
                    }

                    if($getTodayLiveClass->class?->learning_tool == 'Zoom'){
                            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                            if($zoomSettings){
                                $sdk_key = $zoomSettings->sdk_key;
                                $sdk_secret = $zoomSettings->sdk_secret;
                                $api_key = $zoomSettings->api_key;
                                $api_secret = $zoomSettings->api_secret;
                                $api_jwt_token = $zoomSettings->api_jwt_token;
                            }
                            else{
                                $sdk_key = null;
                                $sdk_secret = null;
                                $api_key = null;
                                $api_secret = null;
                                $api_jwt_token = null;
                            }
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'type' => 'Zoom',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'studentID'=>$getTodayLiveClass->student_id,
                            'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                            'Date_Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                        
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                        
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                        
                            'Meeting_Details'=>$Zoom_Meeting_Details,
                        ];
                    }
                    else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'type' => 'Google_Meet',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'studentID'=>$getTodayLiveClass->student_id,
                            'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$Meet_Meeting_Details,
                        ];
                    }
                    else{
                        $response['today_live_class'] = [
                            'id'=>$getTodayLiveClass->id,
                            'type' => 'BBB',
                            'courseID'=>$getTodayLiveClass->course_id,
                            'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                            'studentID'=>$getTodayLiveClass->student_id,
                            'studentName'=>Str::limit($getTodayLiveClass->student?->user?->name,50),
                            'Date/Time'=>$classTime,
                            'Btn_Status'=>$btnStatus,
                            'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                            'classType'=>$getTodayLiveClass->class?->type,
                            'Meeting_Details'=>$BBB_Meeting_Details,
                        ];
                    }
                }
            }

            return response()->json($response);



        }



    }


    // public function myCoursesBackUp(Request $request)
    // {
    //     $user = auth()->user();

    //     if (!$user) {
    //         return response()->json(['message' => 'User Is Not Logged In'], 200);
    //     }

    //     if ($user->role != 2) {
    //         return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
    //     }

    //     $instructor = Instructor::where('user_id', $user->id)->first();

    //     if (!$instructor) {
    //         return response()->json(['message' => 'Instructor not found'], 404);
    //     }
    
    //     if($request->type){
    //         if($request->type == 1){
    //             $courseType = 1;  // SELF
    //         }
    //         else if($request->type == 2){
    //             $courseType = 3;  // LIVE
    //         }
    //         else{
    //              $courseType = 1;    // SELF
    //         }
    //     }
    //     else{
    //         $courseType = 1;    // SELF
    //     }

        
    //     // Courses where user is main instructor
    //     $mainCourseIds = Course::where('user_id', $user->id)->pluck('id')->toArray();

    //     // Courses where user is co-instructor
    //     $coInstructorIds = CourseInstructor::where('course_instructor.instructor_id', $user->id)
    //         ->where('course_instructor.status', STATUS_APPROVED)
    //         ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
    //         ->whereNull('courses.organization_id')
    //         ->pluck('course_instructor.course_id')
    //         ->toArray();
    //     // Merge and unique
    //     $courseIds = array_unique(array_merge($mainCourseIds, $coInstructorIds));

    //     // Get courses with limited fields
    //     $courses = Course::whereIn('id', $courseIds)
    //         ->whereNull('organization_id')
    //         ->where('course_type',$courseType)
    //         ->withCount('reviews')
    //         ->withAvg('reviews', 'rating')
    //         ->orderBy('id', 'DESC')
    //         ->get()
    //         ->map(function ($course) {
    //             if($course->course_type == 1 || $course->course_type == 2){
    //                 $generalClassCourseEnrollments = Enrollment::where('course_id', $course->id)
    //                 ->where('status', ACCESS_PERIOD_ACTIVE)
    //                 // ->where('course_type', 1)
    //                 ->orderBy('course_id', 'ASC')
    //                 ->orderBy('id', 'DESC') // Ensure latest record is selected
    //                 ->count();

    //                 $course_type = __('Self Learning');
    //                  return [
    //                 'id' => $course->id,
    //                 'uuid' => $course->uuid,
    //                 'slug' => $course->slug,
    //                 'title' => $course->title,
    //                 'type' => $course_type,
    //                 'image' => asset($course->image_path),
    //                 'lessons' => count($course->lessons),
    //                 'lectures' => count($course->lectures),
    //                 'price' => (int)$course->price,
    //                 'enrolled' => $generalClassCourseEnrollments,
    //             ];
    //             }
    //             else{
    //                 $liveClassCourseEnrollments = Enrollment::where('course_id', $course->id)
    //                 ->where('status', ACCESS_PERIOD_ACTIVE)
    //                 // ->where('course_type', 1)
    //                 ->orderBy('course_id', 'ASC')
    //                 ->orderBy('id', 'DESC') // Ensure latest record is selected
    //                 ->count();

    //                 $course_type = __('Live'); 
    //                  return [
    //                 'id' => $course->id,
    //                 'uuid' => $course->uuid,
    //                 'slug' => $course->slug,
    //                 'title' => $course->title,
    //                 'type' => $course_type,
    //                 'image' => asset($course->image_path),
    //                 'resources' => count($course->resources),
    //                 'enrolled' => $liveClassCourseEnrollments,
    //                 'price' => (int)$course->price,
    //                 'average_rating' => number_format($course->reviews_avg_rating ?? 0, 1),
               
    //             ];
    //             }
               
    //         });

    //     return response()->json([
    //         'success' => true,
    //         'data' => $courses
    //     ]);
    // }

     public function myCourses(Request $request)
{
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

        if ($user->role != 2 && $user->role != 5) {
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
        }


    if($user->role == 2){
        $instructor = Instructor::where('user_id', $user->id)->first();

        if (!$instructor) {
            return response()->json(['message' => 'Instructor not found'], 404);
        }
    
        if($request->type){
            if($request->type == 1){
                $courseType = 1;  // SELF
            }
            else if($request->type == 2){
                $courseType = 3;  // LIVE
            }
            else{
                 $courseType = 1;    // SELF
            }
        }
        else{
            $courseType = 1;    // SELF
        }

        
        // Courses where user is main instructor
        $mainCourseIds = Course::where('user_id', $user->id)->pluck('id')->toArray();

        // Courses where user is co-instructor
        $coInstructorIds = CourseInstructor::where('course_instructor.instructor_id', $user->id)
            ->where('course_instructor.status', STATUS_APPROVED)
            ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
            ->whereNull('courses.organization_id')
            ->pluck('course_instructor.course_id')
            ->toArray();
        // Merge and unique
        $courseIds = array_unique(array_merge($mainCourseIds, $coInstructorIds));

        // Get courses with limited fields
        $courses = Course::whereIn('id', $courseIds)
            ->whereNull('organization_id')
            ->where('course_type',$courseType)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($course) {
                if($course->course_type == 1 || $course->course_type == 2){
                    $generalClassCourseEnrollments = Enrollment::where('course_id', $course->id)
                    ->where('status', ACCESS_PERIOD_ACTIVE)
                    // ->where('course_type', 1)
                    ->orderBy('course_id', 'ASC')
                    ->orderBy('id', 'DESC') // Ensure latest record is selected
                    ->count();

                    $course_type = __('Self Learning');
                     return [
                    'id' => $course->id,
                    'uuid' => $course->uuid,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'type' => $course_type,
                    'image' => asset($course->image_path),
                    'lessons' => count($course->lessons),
                    'lectures' => count($course->lectures),
                    'price' => (int)$course->price,
                    'enrolled' => $generalClassCourseEnrollments,
                ];
                }
                else{
                    $liveClassCourseEnrollments = Enrollment::where('course_id', $course->id)
                    ->where('status', ACCESS_PERIOD_ACTIVE)
                    // ->where('course_type', 1)
                    ->orderBy('course_id', 'ASC')
                    ->orderBy('id', 'DESC') // Ensure latest record is selected
                    ->count();

                    $course_type = __('Live'); 
                     return [
                    'id' => $course->id,
                    'uuid' => $course->uuid,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'type' => $course_type,
                    'image' => asset($course->image_path),
                    'resources' => count($course->resources),
                    'enrolled' => $liveClassCourseEnrollments,
                    'price' => (int)$course->price,
                    'average_rating' => number_format($course->reviews_avg_rating ?? 0, 1),
               
                ];
                }
               
            });

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }
    else{
        $coordinator = Coordinator::where('user_id', $user->id)->first();

        if (!$coordinator) {
            return response()->json(['message' => 'Coordinator not found'], 404);
        }
    
        if($request->type){
            if($request->type == 1){
                $courseType = 3;  // SELF
            }
            else if($request->type == 2){
                $courseType = 3;  // LIVE
            }
            else{
                 $courseType = 3;    // SELF
            }
        }
        else{
            $courseType = 3;    // SELF
        }

        $coordinatorCourseIds = CoordinatorCourse::where('coordinator_id', $coordinator->id)
                ->pluck('course_id')
                ->toArray();

        // Courses where user is main instructor
        $mainCourseIds = Course::whereIn('id', $coordinatorCourseIds)->pluck('id')->toArray();

        // Merge and unique
        $courseIds = array_unique(array_merge($mainCourseIds, $coordinatorCourseIds));

        // Get courses with limited fields
        $courses = Course::whereIn('id', $courseIds)
            ->whereNull('organization_id')
            ->where('course_type',$courseType)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($course) {
                    $liveClassCourseEnrollments = Enrollment::where('course_id', $course->id)
                        ->where('status', ACCESS_PERIOD_ACTIVE)
                        // ->where('course_type', 1)
                        ->orderBy('course_id', 'ASC')
                        ->orderBy('id', 'DESC') // Ensure latest record is selected
                        ->count();

                    $course_type = __('Live'); 
                    return [
                        'id' => $course->id,
                        'uuid' => $course->uuid,
                        'slug' => $course->slug,
                        'title' => $course->title,
                        'type' => $course_type,
                        'image' => asset($course->image_path),
                        'resources' => count($course->resources),
                        'enrolled' => $liveClassCourseEnrollments,
                        'price' => (int)$course->price,
                        'average_rating' => number_format($course->reviews_avg_rating ?? 0, 1),

                    ]; 
            });

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

}


    // public function classScheduleApiBackUp()
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return response()->json(['message' => 'User Is Not Logged In'], 200);
    //     }

    //     if ($user->role != 2) {
    //         return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
    //     }

    //     $instructor_user_id = $user->id;
    //     $getInstructor = Instructor::where('user_id', $instructor_user_id)->first();

    //     if (!$getInstructor) {
    //         return response()->json(['message' => 'Instructor not found'], 404);
    //     }

    //     $instructor_id = $getInstructor->id;
    //     $today_date = date('Y-m-d');

    //     // Individual Classes (No Pagination)
    //     $getIndividualClasses = ClassDetail::where('instructor_id', $instructor_id)
    //         ->where('class_date', '>=', $today_date)
    //         ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
    //         ->orderBy('class_date', 'ASC')
    //         ->orderBy('class_time', 'DESC')
    //         ->get();

    //     // Group Classes (No Pagination)
    //     $getGroupClasses = ClassDetail::where('instructor_id', $instructor_id)
    //         ->where('class_date', '>=', $today_date)
    //         ->whereHas('class', fn($query) => $query->where('type', 'Group'))
    //         ->orderBy('group_id', 'DESC')
    //         ->get()
    //         ->unique('class_id');

    //     // Format Individual
    //     $individualClasses = $getIndividualClasses->map(function ($individual, $key) {
    //         // $statusDetails = $this->determinateClassStatus($individual);

    //           $statusArray = [
    //                                 'btnText' => __($individual->status),
    //             ];
    //         $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->instructor_id,'instructor');
    //         $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$individual->instructor_id,'instructor');
    //         $updated_time = date('h:i A',strtotime($DateTime));
    //         $lsntime = strtotime($updated_time);
    //         $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //         $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //         $curdate = strtotime($curdate);
    //         $lsndate = date('Y-m-d',strtotime($DateTime));
    //         $lsndate = strtotime($lsndate); 
    //         $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //         $curtime = strtotime($updated_cur_time);
    //         $lesson_duration = $individual->class?->duration;
    //         $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //         $lsnendtime = $lsntimestamp;

    //             if($curdate <  $lsndate){
    //                     $statusArray = [
    //                         'btnText' => __($individual->status),
    //                     ];
    //             }
    //             else if($curdate == $lsndate)
    //             {
    //                     if($curtime < $lsntime) {
    //                             $statusArray = [
    //                                  'btnText' => __($individual->status),
    //                                 'btnID' => 'lec_link1_'.$individual->id,
    //                             ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
    //                          $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->InstructorURI == NULL && $individual->class?->learning_tool == 'BBB')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL && $individual->class?->learning_tool == 'Google_Meet')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                       else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class?->learning_tool == 'Zoom')    
    //                     {
                          
    //                             $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class?->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                  'user_type' =>__('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],
                              
    //                         ];
                         
                          
    //                     }
    //                      else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')    
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->InstructorURI,
    //                             'classType' => $individual->class?->learning_tool,     
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],           
    //                         ];
    //                     }
    //                      else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')    
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,  
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],              
    //                         ];
    //                     }
    //                     else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
    //                     { 
                            
    //                             $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB')
    //                     { 

    //                           $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
                        
    //                     }
    //                      else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet')
    //                     { 
    //                           $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Zoom')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'btnClass' =>  __('create_zoom_meeting_link'),
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class?->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],
                              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->InstructorURI == NULL) && $individual->class->learning_tool == 'BBB')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->InstructorURI,
    //                             'classType' => $individual->class?->learning_tool, 
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],               
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Google_Meet')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,   
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],             
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
    //                     { 
    //                         $statusArray = [
    //                              'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'btnClass' =>  __('create_zoom_meeting_link'),
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],
                              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->InstructorURI,
    //                             'classType' => $individual->class?->learning_tool,  
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],            
    //                         ];
    //                     }
                    
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Zoom')
    //                     {
    //                        $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'BBB')
    //                     {
    //                        $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Google_Meet')
    //                     {
    //                       $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                     else{
    //                             $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //             }
    //             else{
    //                   $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //             }
    //               if($individual->class?->learning_tool == 'Zoom'){
    //                     $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                     if($zoomSettings){
    //                         $sdk_key = $zoomSettings->sdk_key;
    //                         $sdk_secret = $zoomSettings->sdk_secret;
    //                         $api_key = $zoomSettings->api_key;
    //                         $api_secret = $zoomSettings->api_secret;
    //                         $api_jwt_token = $zoomSettings->api_jwt_token;
    //                     }
    //                     else{
    //                         $sdk_key = null;
    //                         $sdk_secret = null;
    //                         $api_key = null;
    //                         $api_secret = null;
    //                         $api_jwt_token = null;
    //                     }

    //                     return [
    //                         'sr_no' => $key + 1,
    //                         'id' => $individual->id,
    //                         'class_id' => $individual->class_id,
    //                         'course_id' => $individual->course_id,
    //                         'course_title' => $individual->course?->title,
    //                         'student_id' => $individual->student_id,
    //                         'student_name' => $individual->student?->user?->name,
    //                         'class_date' => $individual->class_date,
    //                         'class_time' => $individual->class_time,
    //                         // 'zoomApiKey'=> $api_key,
    //                         // 'zoomApiSecret'=> $api_secret,
    //                         'zoomSdkKey'=> $sdk_key,
    //                         'zoomSdkSecret'=> $sdk_secret,
    //                         // 'zoomApiJwtToken'=> $api_jwt_token,
    //                         'status' => $statusArray,
    //                         'learning_tool' => $individual->class?->learning_tool,
    //                     ];
    //                 }
    //                 else{
    //                     return [
    //                         'sr_no' => $key + 1,
    //                         'id' => $individual->id,
    //                         'class_id' => $individual->class_id,
    //                         'course_id' => $individual->course_id,
    //                         'course_title' => $individual->course?->title,
    //                         'student_id' => $individual->student_id,
    //                         'student_name' => $individual->student?->user?->name,
    //                         'class_date' => $individual->class_date,
    //                         'class_time' => $individual->class_time,
    //                         'status' => $statusArray,
    //                         'learning_tool' => $individual->class?->learning_tool,
    //                     ];
    //                 }
         
    //     });

    //     // Format Group
    //     $groupClasses = $getGroupClasses->map(function ($group, $key) {

    //            $statusArray = [
    //                                 'btnText' => __($group->status),
    //             ];

    //         $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->instructor_id,'instructor');
    //         $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$group->instructor_id,'instructor');
    //         $updated_time = date('h:i A',strtotime($DateTime));
    //         $lsntime = strtotime($updated_time);
    //         $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //         $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //         $curdate = strtotime($curdate);
    //         $lsndate = date('Y-m-d',strtotime($DateTime));
    //         $lsndate = strtotime($lsndate); 
    //         $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //         $curtime = strtotime($updated_cur_time);
    //         $lesson_duration = $group->class?->duration;
    //         $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //         $lsnendtime = $lsntimestamp;

    //         // $statusDetails = $this->determinateClassStatus($group);

    //         if($curdate <  $lsndate){
    //                     $statusArray = [
    //                         'btnText' => __($group->status),
    //                     ];
    //         }
    //         else if($curdate == $lsndate)
    //         {
    //                     if($curtime < $lsntime) {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                                 'btnID' => 'lec_link2_'.$group->group_id,
    //                             ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL  && $group->class->learning_tool == 'Zoom') {
    //                          $statusArray = [
    //                             'btnText' => __($group->status),
    //                              'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->InstructorURI == NULL  && $group->class?->learning_tool == 'BBB')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                              'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->google_meet_link == NULL  && $group->class?->learning_tool == 'Google_Meet')
    //                     {
    //                         $statusArray = [
    //                           'btnText' => __($group->status),
    //                             'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                       else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class?->learning_tool == 'Zoom')    
    //                     {
                          
    //                             $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'classDetails' => [
    //                                 'type' => $group->class->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],
                              
    //                             ];
                          
                          
    //                     }
    //                      else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB')    
    //                     { 
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->InstructorURI,
    //                             'classType' => $group->class?->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],              
    //                         ];
                        
    //                     }
    //                      else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')    
    //                     { 
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->google_meet_link,
    //                             'classType' => $group->class->learning_tool,      
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],            
    //                         ];
                        
    //                     }
    //                     else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Zoom')
    //                     { 
    //                            $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'BBB')
    //                     { 
    //                          $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Google_Meet')
    //                     { 
    //                          $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed"&& $group->class->learning_tool == 'Zoom')
    //             		{
                            
    //                          $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
                              
    //                             'classDetails' => [
    //                                 'type' => $group->class->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],
                              
    //                         ];
                        
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed"  && $group->class->learning_tool == 'BBB')
    //             		{
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->InstructorURI,
    //                             'classType' => $group->class?->learning_tool,   
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],             
    //                         ];
                           
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')
    //             		{
                             
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->google_meet_link,
    //                             'classType' => $group->class->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],              
    //                         ];
                         
    //                     }
                    
    //                       else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Zoom')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'BBB')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Google_Meet')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
                    
    //             }
    //             if($group->class?->learning_tool == 'Zoom'){
    //                     $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                     if($zoomSettings){
    //                         $sdk_key = $zoomSettings->sdk_key;
    //                         $sdk_secret = $zoomSettings->sdk_secret;
    //                         $api_key = $zoomSettings->api_key;
    //                         $api_secret = $zoomSettings->api_secret;
    //                         $api_jwt_token = $zoomSettings->api_jwt_token;
    //                     }
    //                     else{
    //                         $sdk_key = null;
    //                         $sdk_secret = null;
    //                         $api_key = null;
    //                         $api_secret = null;
    //                         $api_jwt_token = null;
    //                     }
    //                 return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $group->id,
    //                 'class_id' => $group->class_id,
    //                 'course_id' => $group->course_id,
    //                 'course_title' => $group->course?->title,
    //                 'class_date' => $group->class_date,
    //                 'class_time' => $group->class_time,
    //                 // 'zoomApiKey'=> $api_key,
    //                 // 'zoomApiSecret'=> $api_secret,
    //                 'zoomSdkKey'=> $sdk_key,
    //                 'zoomSdkSecret'=> $sdk_secret,
    //                 // 'zoomApiJwtToken'=> $api_jwt_token,
    //                 'status' => $statusArray,
    //                 'learning_tool' => $group->class?->learning_tool,
    //                 'no_of_students' => $group->class?->no_of_students,
    //                 ];
    //             }
    //             else{
    //                 return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $group->id,
    //                 'class_id' => $group->class_id,
    //                 'course_id' => $group->course_id,
    //                 'course_title' => $group->course?->title,
    //                 'class_date' => $group->class_date,
    //                 'class_time' => $group->class_time,
    //                 'status' => $statusArray,
    //                 'learning_tool' => $group->class?->learning_tool,
    //                 'no_of_students' => $group->class?->no_of_students,
    //                 ];
    //             }
          
    //     });

   
    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'title' => 'Class Schedule',
    //             'individual_classes' => $individualClasses,
    //             'group_classes' => $groupClasses,
    //             'base_url' => url('/'),
    //         ]
    //     ]);
    // }



//  public function classScheduleApi()
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         return response()->json(['message' => 'User Is Not Logged In'], 200);
    //     }

    //     if ($user->role != 2 && $user->role != 5) {
    //         return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
    //     }

    //     if($user->role == 2){
    //     $instructor_user_id = $user->id;
    //     $getInstructor = Instructor::where('user_id', $instructor_user_id)->first();

    //     if (!$getInstructor) {
    //         return response()->json(['message' => 'Instructor not found'], 404);
    //     }

    //     $instructor_id = $getInstructor->id;
    //     $today_date = date('Y-m-d');

    //     // Individual Classes (No Pagination)
    //     $getIndividualClasses = ClassDetail::where('instructor_id', $instructor_id)
    //         ->where('class_date', '>=', $today_date)
    //         ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
    //         ->orderBy('class_date', 'ASC')
    //         ->orderBy('class_time', 'DESC')
    //         ->get();

    //     // Group Classes (No Pagination)
    //     $getGroupClasses = ClassDetail::where('instructor_id', $instructor_id)
    //         ->where('class_date', '>=', $today_date)
    //         ->whereHas('class', fn($query) => $query->where('type', 'Group'))
    //         ->orderBy('group_id', 'DESC')
    //         ->get()
    //         ->unique('class_id');

    //     // Format Individual
    //     $individualClasses = $getIndividualClasses->map(function ($individual, $key) {
    //         // $statusDetails = $this->determinateClassStatus($individual);

    //           $statusArray = [
    //                                 'btnText' => __($individual->status),
    //             ];
    //         $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->instructor_id,'instructor');
    //         $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$individual->instructor_id,'instructor');
    //         $updated_time = date('h:i A',strtotime($DateTime));
    //         $lsntime = strtotime($updated_time);
    //         $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //         $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //         $curdate = strtotime($curdate);
    //         $lsndate = date('Y-m-d',strtotime($DateTime));
    //         $lsndate = strtotime($lsndate); 
    //         $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //         $curtime = strtotime($updated_cur_time);
    //         $lesson_duration = $individual->class?->duration;
    //         $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //         $lsnendtime = $lsntimestamp;

    //             if($curdate <  $lsndate){
    //                     $statusArray = [
    //                         'btnText' => __($individual->status),
    //                     ];
    //             }
    //             else if($curdate == $lsndate)
    //             {
    //                     if($curtime < $lsntime) {
    //                             $statusArray = [
    //                                  'btnText' => __($individual->status),
    //                                 'btnID' => 'lec_link1_'.$individual->id,
    //                             ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
    //                          $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->InstructorURI == NULL && $individual->class?->learning_tool == 'BBB')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL && $individual->class?->learning_tool == 'Google_Meet')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                       else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class?->learning_tool == 'Zoom')    
    //                     {
                          
    //                             $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class?->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                  'user_type' =>__('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],
                              
    //                         ];
                         
                          
    //                     }
    //                      else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')    
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->InstructorURI,
    //                             'classType' => $individual->class?->learning_tool,     
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],           
    //                         ];
    //                     }
    //                      else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')    
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,  
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],              
    //                         ];
    //                     }
    //                     else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
    //                     { 
                            
    //                             $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB')
    //                     { 

    //                           $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
                        
    //                     }
    //                      else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet')
    //                     { 
    //                           $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Zoom')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'btnClass' =>  __('create_zoom_meeting_link'),
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class?->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],
                              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->InstructorURI == NULL) && $individual->class->learning_tool == 'BBB')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->InstructorURI,
    //                             'classType' => $individual->class?->learning_tool, 
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],               
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Google_Meet')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,   
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],             
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
    //                     { 
    //                         $statusArray = [
    //                              'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'btnClass' =>  __('create_zoom_meeting_link'),
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],
                              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->InstructorURI,
    //                             'classType' => $individual->class?->learning_tool,  
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
    //                                 'user_id' => $individual->instructor_id,
    //                             ],            
    //                         ];
    //                     }
                    
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Zoom')
    //                     {
    //                        $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'BBB')
    //                     {
    //                        $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Google_Meet')
    //                     {
    //                       $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                     else{
    //                             $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //             }
    //             else{
    //                   $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //             }
    //               if($individual->class?->learning_tool == 'Zoom'){
    //                     $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                     if($zoomSettings){
    //                         $sdk_key = $zoomSettings->sdk_key;
    //                         $sdk_secret = $zoomSettings->sdk_secret;
    //                         $api_key = $zoomSettings->api_key;
    //                         $api_secret = $zoomSettings->api_secret;
    //                         $api_jwt_token = $zoomSettings->api_jwt_token;
    //                     }
    //                     else{
    //                         $sdk_key = null;
    //                         $sdk_secret = null;
    //                         $api_key = null;
    //                         $api_secret = null;
    //                         $api_jwt_token = null;
    //                     }

    //                     return [
    //                         'sr_no' => $key + 1,
    //                         'id' => $individual->id,
    //                         'class_id' => $individual->class_id,
    //                         'course_id' => $individual->course_id,
    //                         'course_title' => $individual->course?->title,
    //                         'student_id' => $individual->student_id,
    //                         'student_name' => $individual->student?->user?->name,
    //                         'class_date' => $individual->class_date,
    //                         'class_time' => $individual->class_time,
    //                         // 'zoomApiKey'=> $api_key,
    //                         // 'zoomApiSecret'=> $api_secret,
    //                         'zoomSdkKey'=> $sdk_key,
    //                         'zoomSdkSecret'=> $sdk_secret,
    //                         // 'zoomApiJwtToken'=> $api_jwt_token,
    //                         'status' => $statusArray,
    //                         'learning_tool' => $individual->class?->learning_tool,
    //                     ];
    //                 }
    //                 else{
    //                     return [
    //                         'sr_no' => $key + 1,
    //                         'id' => $individual->id,
    //                         'class_id' => $individual->class_id,
    //                         'course_id' => $individual->course_id,
    //                         'course_title' => $individual->course?->title,
    //                         'student_id' => $individual->student_id,
    //                         'student_name' => $individual->student?->user?->name,
    //                         'class_date' => $individual->class_date,
    //                         'class_time' => $individual->class_time,
    //                         'status' => $statusArray,
    //                         'learning_tool' => $individual->class?->learning_tool,
    //                     ];
    //                 }
         
    //     });

    //     // Format Group
    //     $groupClasses = $getGroupClasses->map(function ($group, $key) {

    //            $statusArray = [
    //                                 'btnText' => __($group->status),
    //             ];

    //         $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->instructor_id,'instructor');
    //         $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$group->instructor_id,'instructor');
    //         $updated_time = date('h:i A',strtotime($DateTime));
    //         $lsntime = strtotime($updated_time);
    //         $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //         $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //         $curdate = strtotime($curdate);
    //         $lsndate = date('Y-m-d',strtotime($DateTime));
    //         $lsndate = strtotime($lsndate); 
    //         $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //         $curtime = strtotime($updated_cur_time);
    //         $lesson_duration = $group->class?->duration;
    //         $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //         $lsnendtime = $lsntimestamp;

    //         // $statusDetails = $this->determinateClassStatus($group);

    //         if($curdate <  $lsndate){
    //                     $statusArray = [
    //                         'btnText' => __($group->status),
    //                     ];
    //         }
    //         else if($curdate == $lsndate)
    //         {
    //                     if($curtime < $lsntime) {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                                 'btnID' => 'lec_link2_'.$group->group_id,
    //                             ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL  && $group->class->learning_tool == 'Zoom') {
    //                          $statusArray = [
    //                             'btnText' => __($group->status),
    //                              'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->InstructorURI == NULL  && $group->class?->learning_tool == 'BBB')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                              'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->google_meet_link == NULL  && $group->class?->learning_tool == 'Google_Meet')
    //                     {
    //                         $statusArray = [
    //                           'btnText' => __($group->status),
    //                             'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                       else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class?->learning_tool == 'Zoom')    
    //                     {
                          
    //                             $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'classDetails' => [
    //                                 'type' => $group->class->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],
                              
    //                             ];
                          
                          
    //                     }
    //                      else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB')    
    //                     { 
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->InstructorURI,
    //                             'classType' => $group->class?->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],              
    //                         ];
                        
    //                     }
    //                      else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')    
    //                     { 
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->google_meet_link,
    //                             'classType' => $group->class->learning_tool,      
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],            
    //                         ];
                        
    //                     }
    //                     else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Zoom')
    //                     { 
    //                            $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'BBB')
    //                     { 
    //                          $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Google_Meet')
    //                     { 
    //                          $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed"&& $group->class->learning_tool == 'Zoom')
    //             		{
                            
    //                          $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
                              
    //                             'classDetails' => [
    //                                 'type' => $group->class->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],
                              
    //                         ];
                        
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed"  && $group->class->learning_tool == 'BBB')
    //             		{
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->InstructorURI,
    //                             'classType' => $group->class?->learning_tool,   
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],             
    //                         ];
                           
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')
    //             		{
                             
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->google_meet_link,
    //                             'classType' => $group->class->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' => __('Tutor'),
    //                                 'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
    //                                 'user_id' => $group->instructor_id,
    //                             ],              
    //                         ];
                         
    //                     }
                    
    //                       else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Zoom')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'BBB')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Google_Meet')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
                    
    //             }
    //             if($group->class?->learning_tool == 'Zoom'){
    //                     $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                     if($zoomSettings){
    //                         $sdk_key = $zoomSettings->sdk_key;
    //                         $sdk_secret = $zoomSettings->sdk_secret;
    //                         $api_key = $zoomSettings->api_key;
    //                         $api_secret = $zoomSettings->api_secret;
    //                         $api_jwt_token = $zoomSettings->api_jwt_token;
    //                     }
    //                     else{
    //                         $sdk_key = null;
    //                         $sdk_secret = null;
    //                         $api_key = null;
    //                         $api_secret = null;
    //                         $api_jwt_token = null;
    //                     }
    //                 return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $group->id,
    //                 'class_id' => $group->class_id,
    //                 'course_id' => $group->course_id,
    //                 'course_title' => $group->course?->title,
    //                 'class_date' => $group->class_date,
    //                 'class_time' => $group->class_time,
    //                 // 'zoomApiKey'=> $api_key,
    //                 // 'zoomApiSecret'=> $api_secret,
    //                 'zoomSdkKey'=> $sdk_key,
    //                 'zoomSdkSecret'=> $sdk_secret,
    //                 // 'zoomApiJwtToken'=> $api_jwt_token,
    //                 'status' => $statusArray,
    //                 'learning_tool' => $group->class?->learning_tool,
    //                 'no_of_students' => $group->class?->no_of_students,
    //                 ];
    //             }
    //             else{
    //                 return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $group->id,
    //                 'class_id' => $group->class_id,
    //                 'course_id' => $group->course_id,
    //                 'course_title' => $group->course?->title,
    //                 'class_date' => $group->class_date,
    //                 'class_time' => $group->class_time,
    //                 'status' => $statusArray,
    //                 'learning_tool' => $group->class?->learning_tool,
    //                 'no_of_students' => $group->class?->no_of_students,
    //                 ];
    //             }
          
    //     });

   
    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'title' => 'Class Schedule',
    //             'individual_classes' => $individualClasses,
    //             'group_classes' => $groupClasses,
    //             'base_url' => url('/'),
    //         ]
    //     ]);
    //     }
    //     else{
       
    //     $coordinator_user_id = $user->id;
    //     $getCoordinator = Coordinator::where('user_id', $coordinator_user_id)->first();

    //     if (!$getCoordinator) {
    //         return response()->json(['message' => 'Coordinator not found'], 404);
    //     }

    //     $coordinator_id = $getCoordinator->id;
    //     $today_date = date('Y-m-d');


    //     // Courses handled by coordinator
    //     $coordinatorCourseIds = CoordinatorCourse::where('coordinator_id', $coordinator_id)
    //         ->pluck('course_id')
    //         ->toArray();

          

    //     // Individual Classes (No Pagination)
    //     $getIndividualClasses = ClassDetail::whereIn('course_id', $coordinatorCourseIds)
    //         ->where('class_date', '>=', $today_date)
    //         ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
    //         ->orderBy('class_date', 'ASC')
    //         ->orderBy('class_time', 'DESC')
    //         ->get();

    //     // Group Classes (No Pagination)
    //     $getGroupClasses = ClassDetail::whereIn('course_id', $coordinatorCourseIds)
    //         ->where('class_date', '>=', $today_date)
    //         ->whereHas('class', fn($query) => $query->where('type', 'Group'))
    //         ->orderBy('group_id', 'DESC')
    //         ->get()
    //         ->unique('class_id');


            

    //     // Format Individual
    //     $individualClasses = $getIndividualClasses->map(function ($individual, $key) use ($coordinator_id) {
    //         // $statusDetails = $this->determinateClassStatus($individual);

    //           $statusArray = [
    //                                 'btnText' => __($individual->status),
    //             ];
    //         $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$coordinator_id,'coordinator');
    //         $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
    //         $updated_time = date('h:i A',strtotime($DateTime));
    //         $lsntime = strtotime($updated_time);
    //         $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //         $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //         $curdate = strtotime($curdate);
    //         $lsndate = date('Y-m-d',strtotime($DateTime));
    //         $lsndate = strtotime($lsndate); 
    //         $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //         $curtime = strtotime($updated_cur_time);
    //         $lesson_duration = $individual->class?->duration;
    //         $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //         $lsnendtime = $lsntimestamp;

    //             if($curdate <  $lsndate){
    //                     $statusArray = [
    //                         'btnText' => __($individual->status),
    //                     ];
    //             }
    //             else if($curdate == $lsndate)
    //             {
    //                     if($curtime < $lsntime) {
    //                             $statusArray = [
    //                                  'btnText' => __($individual->status),
    //                                 'btnID' => 'lec_link1_'.$individual->id,
    //                             ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
    //                          $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->AcademicURI == NULL && $individual->class?->learning_tool == 'BBB')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL && $individual->class?->learning_tool == 'Google_Meet')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'btnID' => 'lec_link1_'.$individual->id,
    //                         ];
    //                     }
    //                       else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class?->learning_tool == 'Zoom')    
    //                     {
                          
    //                             $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class?->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],
                              
    //                         ];
                         
                          
    //                     }
    //                      else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')    
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->AcademicURI ?? $individual->StudentURI,
    //                             'classType' => $individual->class?->learning_tool,     
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],           
    //                         ];
    //                     }
    //                      else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')    
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,  
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],              
    //                         ];
    //                     }
    //                     else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
    //                     { 
                            
    //                             $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB')
    //                     { 

    //                           $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
                        
    //                     }
    //                      else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet')
    //                     { 
    //                           $statusArray = [
    //                                     'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Zoom')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'btnClass' =>  __('create_zoom_meeting_link'),
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class?->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],
                              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->AcademicURI == NULL) && $individual->class->learning_tool == 'BBB')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->AcademicURI ?? $individual->StudentURI,
    //                             'classType' => $individual->class?->learning_tool, 
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],               
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Google_Meet')
    //             		{
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,   
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],             
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
    //                     { 
    //                         $statusArray = [
    //                              'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'btnClass' =>  __('create_zoom_meeting_link'),
    //                             // 'on_click_api_route' => route('student-start-zoom-class'),
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],
                              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->AcademicURI ?? $individual->StudentURI,
    //                             'classType' => $individual->class?->learning_tool,  
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],              
    //                         ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')
    //                     { 
    //                         $statusArray = [
    //                             'btnText' => __($individual->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $individual->google_meet_link,
    //                             'classType' => $individual->class?->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $individual->class->learning_tool,
    //                                 'lesson_id' => $individual->id,
    //                                 'meeting_id' => $individual->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],            
    //                         ];
    //                     }
                    
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Zoom')
    //                     {
    //                        $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'BBB')
    //                     {
    //                        $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Google_Meet')
    //                     {
    //                       $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //                     else{
    //                             $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //                     }
    //             }
    //             else{
    //                   $statusArray = [
    //                                 'btnText' => __($individual->status),
    //                             ];
    //             }
    //               if($individual->class?->learning_tool == 'Zoom'){
    //                     $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                     if($zoomSettings){
    //                         $sdk_key = $zoomSettings->sdk_key;
    //                         $sdk_secret = $zoomSettings->sdk_secret;
    //                         $api_key = $zoomSettings->api_key;
    //                         $api_secret = $zoomSettings->api_secret;
    //                         $api_jwt_token = $zoomSettings->api_jwt_token;
    //                     }
    //                     else{
    //                         $sdk_key = null;
    //                         $sdk_secret = null;
    //                         $api_key = null;
    //                         $api_secret = null;
    //                         $api_jwt_token = null;
    //                     }

    //                     return [
    //                         'sr_no' => $key + 1,
    //                         'id' => $individual->id,
    //                         'class_id' => $individual->class_id,
    //                         'course_id' => $individual->course_id,
    //                         'course_title' => $individual->course?->title,
    //                         'student_id' => $individual->student_id,
    //                         'student_name' => $individual->student?->user?->name,
    //                         'instructor_id' => $individual->instructor_id,
    //                         'instructor_name' => $individual->instructor?->user?->name,
    //                         'class_date' => $individual->class_date,
    //                         'class_time' => $individual->class_time,
    //                         // 'zoomApiKey'=> $api_key,
    //                         // 'zoomApiSecret'=> $api_secret,
    //                         'zoomSdkKey'=> $sdk_key,
    //                         'zoomSdkSecret'=> $sdk_secret,
    //                         // 'zoomApiJwtToken'=> $api_jwt_token,
    //                         'status' => $statusArray,
    //                         'learning_tool' => $individual->class?->learning_tool,
    //                     ];
    //                 }
    //                 else{
    //                     return [
    //                         'sr_no' => $key + 1,
    //                         'id' => $individual->id,
    //                         'class_id' => $individual->class_id,
    //                         'course_id' => $individual->course_id,
    //                         'course_title' => $individual->course?->title,
    //                         'student_id' => $individual->student_id,
    //                         'student_name' => $individual->student?->user?->name,
    //                         'instructor_id' => $individual->instructor_id,
    //                         'instructor_name' => $individual->instructor?->user?->name,
    //                         'class_date' => $individual->class_date,
    //                         'class_time' => $individual->class_time,
    //                         'status' => $statusArray,
    //                         'learning_tool' => $individual->class?->learning_tool,
    //                     ];
    //                 }
         
    //     });

    //     // Format Group
    //     $groupClasses = $getGroupClasses->map(function ($group, $key) use ($coordinator_id) {

    //            $statusArray = [
    //                 'btnText' => __($group->status),
    //             ];

    //         $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$coordinator_id,'coordinator');
    //         $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
    //         $updated_time = date('h:i A',strtotime($DateTime));
    //         $lsntime = strtotime($updated_time);
    //         $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //         $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //         $curdate = strtotime($curdate);
    //         $lsndate = date('Y-m-d',strtotime($DateTime));
    //         $lsndate = strtotime($lsndate); 
    //         $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //         $curtime = strtotime($updated_cur_time);
    //         $lesson_duration = $group->class?->duration;
    //         $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //         $lsnendtime = $lsntimestamp;

    //         // $statusDetails = $this->determinateClassStatus($group);

    //         if($curdate <  $lsndate){
    //             $statusArray = [
    //                 'btnText' => __($group->status),
    //             ];
    //         }
    //         else if($curdate == $lsndate)
    //         {
    //                     if($curtime < $lsntime) {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                                 'btnID' => 'lec_link2_'.$group->group_id,
    //                             ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL  && $group->class->learning_tool == 'Zoom') {
    //                          $statusArray = [
    //                             'btnText' => __($group->status),
    //                              'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->AcademicURI == NULL  && $group->class?->learning_tool == 'BBB')
    //                     {
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                              'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                     else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->google_meet_link == NULL  && $group->class?->learning_tool == 'Google_Meet')
    //                     {
    //                         $statusArray = [
    //                           'btnText' => __($group->status),
    //                             'btnID' => 'lec_link2_'.$group->group_id,
    //                         ];
    //                     }
    //                       else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class?->learning_tool == 'Zoom')    
    //                     {
                          
    //                             $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
    //                             'classDetails' => [
    //                                 'type' => $group->class->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],
                              
    //                             ];
                          
                          
    //                     }
    //                      else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB')    
    //                     { 
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->AcademicURI ?? $group->StudentURI,
    //                             'classType' => $group->class?->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],              
    //                         ];
                        
    //                     }
    //                      else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')    
    //                     { 
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->google_meet_link,
    //                             'classType' => $group->class->learning_tool,      
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],            
    //                         ];
                        
    //                     }
    //                     else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Zoom')
    //                     { 
    //                            $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'BBB')
    //                     { 
    //                          $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Google_Meet')
    //                     { 
    //                          $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed"&& $group->class->learning_tool == 'Zoom')
    //             		{
                            
    //                          $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  "javascript:void(0);",
                              
    //                             'classDetails' => [
    //                                 'type' => $group->class->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],
                              
    //                         ];
                        
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed"  && $group->class->learning_tool == 'BBB')
    //             		{
                           
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->AcademicURI ?? $group->StudentURI,
    //                             'classType' => $group->class?->learning_tool,   
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],             
    //                         ];
                           
    //                     }
    //                      else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')
    //             		{
                             
    //                         $statusArray = [
    //                             'btnText' => __($group->status),
    //                             'started' => 1,
    //                             'btnHref' =>  $group->google_meet_link,
    //                             'classType' => $group->class->learning_tool,    
    //                             'classDetails' => [
    //                                 'type' => $group->class?->learning_tool,
    //                                 'lesson_id' => $group->id,
    //                                 'group_id' =>$group->group_id,
    //                                 'meeting_id' => $group->meeting_id,
    //                                 'user_type' =>__('Coordinator'),
    //                                 'user_name' => getCoordinatorNameFromID($coordinator_id),
    //                                 'user_id' => $coordinator_id,
    //                             ],              
    //                         ];
                         
    //                     }
                    
    //                       else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Zoom')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'BBB')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
    //                      else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Google_Meet')
    //                     {
    //                             $statusArray = [
    //                                 'btnText' => __($group->status),
    //                             ];
    //                     }
                    
    //             }
    //             if($group->class?->learning_tool == 'Zoom'){
    //                     $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
    //                     if($zoomSettings){
    //                         $sdk_key = $zoomSettings->sdk_key;
    //                         $sdk_secret = $zoomSettings->sdk_secret;
    //                         $api_key = $zoomSettings->api_key;
    //                         $api_secret = $zoomSettings->api_secret;
    //                         $api_jwt_token = $zoomSettings->api_jwt_token;
    //                     }
    //                     else{
    //                         $sdk_key = null;
    //                         $sdk_secret = null;
    //                         $api_key = null;
    //                         $api_secret = null;
    //                         $api_jwt_token = null;
    //                     }
    //                 return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $group->id,
    //                 'class_id' => $group->class_id,
    //                 'course_id' => $group->course_id,
    //                 'course_title' => $group->course?->title,
    //                 'class_date' => $group->class_date,
    //                 'class_time' => $group->class_time,
    //                 // 'zoomApiKey'=> $api_key,
    //                 // 'zoomApiSecret'=> $api_secret,
    //                 'zoomSdkKey'=> $sdk_key,
    //                 'zoomSdkSecret'=> $sdk_secret,
    //                 // 'zoomApiJwtToken'=> $api_jwt_token,
    //                 'status' => $statusArray,
    //                 'learning_tool' => $group->class?->learning_tool,
    //                 'no_of_students' => $group->class?->no_of_students,
    //                 ];
    //             }
    //             else{
    //                 return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $group->id,
    //                 'class_id' => $group->class_id,
    //                 'course_id' => $group->course_id,
    //                 'course_title' => $group->course?->title,
    //                 'class_date' => $group->class_date,
    //                 'class_time' => $group->class_time,
    //                 'status' => $statusArray,
    //                 'learning_tool' => $group->class?->learning_tool,
    //                 'no_of_students' => $group->class?->no_of_students,
    //                 ];
    //             }
          
    //     });

   
    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'title' => 'Class Schedule',
    //             'individual_classes' => $individualClasses,
    //             'group_classes' => $groupClasses,
    //             'base_url' => url('/'),
    //         ]
    //     ]);
    //     }
        

// }





       public function classScheduleApi()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

        if ($user->role != 2 && $user->role != 5) {
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
        }

          // Shared pagination controls
        $page     = max((int) request()->input('page', 1), 1);
        $perPage  = max((int) request()->input('per_page', 50), 1);
        $offset   = ($page - 1) * $perPage;

    if($user->role == 2){
        $instructor_user_id = $user->id;
        $getInstructor = Instructor::where('user_id', $instructor_user_id)->first();

        if (!$getInstructor) {
            return response()->json(['message' => 'Instructor not found'], 404);
        }

        $instructor_id = $getInstructor->id;
        $today_date = date('Y-m-d');


        $individualBase = ClassDetail::where('instructor_id', $instructor_id);
        $individualQuery = $individualBase
            ->where('class_date', '>=', $today_date)
            ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
            ->orderBy('class_date', 'ASC')
            ->orderBy('class_time', 'DESC');
        $individualTotal = $individualQuery->count();
        $getIndividualClasses = $individualQuery
            ->skip($offset)
            ->take($perPage)
            ->get();

        // Individual Classes (No Pagination)
        // $getIndividualClasses = ClassDetail::where('instructor_id', $instructor_id)
        //     ->where('class_date', '>=', $today_date)
        //     ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
        //     ->orderBy('class_date', 'ASC')
        //     ->orderBy('class_time', 'DESC')
        //     ->get();


        $groupBase      = ClassDetail::where('instructor_id', $instructor_id);
        $groupCollection = $groupBase
            ->where('class_date', '>=', $today_date)
            ->whereHas('class', fn($query) => $query->where('type', 'Group'))
            ->orderBy('group_id', 'DESC')
            ->get()
            ->unique('class_id')
            ->values(); // 🔴 REQUIRED;;
        $groupTotal = $groupCollection->count();
        $getGroupClasses = $groupCollection
            ->slice($offset, $perPage)
            ->values();

        // Group Classes (No Pagination)
        // $getGroupClasses = ClassDetail::where('instructor_id', $instructor_id)
        //     ->where('class_date', '>=', $today_date)
        //     ->whereHas('class', fn($query) => $query->where('type', 'Group'))
        //     ->orderBy('group_id', 'DESC')
        //     ->get()
        //     ->unique('class_id');

        // Format Individual
        $individualClasses = $getIndividualClasses->map(function ($individual, $key) use ($page, $perPage) {
            // $statusDetails = $this->determinateClassStatus($individual);

              $statusArray = [
                                    'btnText' => __($individual->status),
                ];
            $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->instructor_id,'instructor');
            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$individual->instructor_id,'instructor');
            $updated_time = date('h:i A',strtotime($DateTime));
            $lsntime = strtotime($updated_time);
            $classTime = date('d-m-Y h:i A',strtotime($DateTime));
            $curdate = date("Y-m-d",strtotime($CurrentDateTime));
            $curdate = strtotime($curdate);
            $lsndate = date('Y-m-d',strtotime($DateTime));
            $lsndate = strtotime($lsndate); 
            $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
            $curtime = strtotime($updated_cur_time);
            $lesson_duration = $individual->class?->duration;
            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
            $lsnendtime = $lsntimestamp;

                if($curdate <  $lsndate){
                        $statusArray = [
                            'btnText' => __($individual->status),
                        ];
                }
                else if($curdate == $lsndate)
                {
                        if($curtime < $lsntime) {
                                $statusArray = [
                                     'btnText' => __($individual->status),
                                    'btnID' => 'lec_link1_'.$individual->id,
                                ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
                             $statusArray = [
                                'btnText' => __($individual->status),
                                'btnID' => 'lec_link1_'.$individual->id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->InstructorURI == NULL && $individual->class?->learning_tool == 'BBB')
                        {
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'btnID' => 'lec_link1_'.$individual->id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL && $individual->class?->learning_tool == 'Google_Meet')
                        {
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'btnID' => 'lec_link1_'.$individual->id,
                            ];
                        }
                          else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class?->learning_tool == 'Zoom')    
                        {
                          
                                $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                // 'on_click_api_route' => route('student-start-zoom-class'),
                                'classDetails' => [
                                    'type' => $individual->class?->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                     'user_type' =>__('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],
                              
                            ];
                         
                          
                        }
                         else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')    
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->InstructorURI,
                                'classType' => $individual->class?->learning_tool,     
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],           
                            ];
                        }
                         else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')    
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,  
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],              
                            ];
                        }
                        else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
                        { 
                            
                                $statusArray = [
                                        'btnText' => __($individual->status),
                                ];
                        }
                         else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB')
                        { 

                              $statusArray = [
                                        'btnText' => __($individual->status),
                                ];
                        
                        }
                         else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet')
                        { 
                              $statusArray = [
                                        'btnText' => __($individual->status),
                                ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Zoom')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                'btnClass' =>  __('create_zoom_meeting_link'),
                                // 'on_click_api_route' => route('student-start-zoom-class'),
                                'classDetails' => [
                                    'type' => $individual->class?->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],
                              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->InstructorURI == NULL) && $individual->class->learning_tool == 'BBB')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->InstructorURI,
                                'classType' => $individual->class?->learning_tool, 
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],               
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Google_Meet')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,   
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],             
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                        { 
                            $statusArray = [
                                 'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                'btnClass' =>  __('create_zoom_meeting_link'),
                                // 'on_click_api_route' => route('student-start-zoom-class'),
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],
                              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->InstructorURI,
                                'classType' => $individual->class?->learning_tool,  
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,    
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $individual->instructor->first_name.' '.$individual->instructor->last_name,
                                    'user_id' => $individual->instructor_id,
                                ],            
                            ];
                        }
                    
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Zoom')
                        {
                           $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'BBB')
                        {
                           $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Google_Meet')
                        {
                          $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                        else{
                                $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                }
                else{
                      $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                }
                  if($individual->class?->learning_tool == 'Zoom'){
                        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                        if($zoomSettings){
                            $sdk_key = $zoomSettings->sdk_key;
                            $sdk_secret = $zoomSettings->sdk_secret;
                            $api_key = $zoomSettings->api_key;
                            $api_secret = $zoomSettings->api_secret;
                            $api_jwt_token = $zoomSettings->api_jwt_token;
                        }
                        else{
                            $sdk_key = null;
                            $sdk_secret = null;
                            $api_key = null;
                            $api_secret = null;
                            $api_jwt_token = null;
                        }

                        return [
                            // 'sr_no' => $key + 1,
                            'sr_no' =>   (($page - 1) * $perPage) + ($key + 1),
                            'id' => $individual->id,
                            'class_id' => $individual->class_id,
                            'course_id' => $individual->course_id,
                            'course_title' => $individual->course?->title,
                            'student_id' => $individual->student_id,
                            'student_name' => $individual->student?->user?->name,
                            'class_date' => $individual->class_date,
                            'class_time' => $individual->class_time,
                            // 'zoomApiKey'=> $api_key,
                            // 'zoomApiSecret'=> $api_secret,
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                            // 'zoomApiJwtToken'=> $api_jwt_token,
                            'status' => $statusArray,
                            'learning_tool' => $individual->class?->learning_tool,
                        ];
                    }
                    else{
                        return [
                            // 'sr_no' => $key + 1,
                            'sr_no' =>   (($page - 1) * $perPage) + ($key + 1),
                            'id' => $individual->id,
                            'class_id' => $individual->class_id,
                            'course_id' => $individual->course_id,
                            'course_title' => $individual->course?->title,
                            'student_id' => $individual->student_id,
                            'student_name' => $individual->student?->user?->name,
                            'class_date' => $individual->class_date,
                            'class_time' => $individual->class_time,
                            'status' => $statusArray,
                            'learning_tool' => $individual->class?->learning_tool,
                        ];
                    }
         
        });

        // Format Group
        $groupClasses = $getGroupClasses->map(function ($group, $key) use ($page, $perPage) {

               $statusArray = [
                                    'btnText' => __($group->status),
                ];

            $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->instructor_id,'instructor');
            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$group->instructor_id,'instructor');
            $updated_time = date('h:i A',strtotime($DateTime));
            $lsntime = strtotime($updated_time);
            $classTime = date('d-m-Y h:i A',strtotime($DateTime));
            $curdate = date("Y-m-d",strtotime($CurrentDateTime));
            $curdate = strtotime($curdate);
            $lsndate = date('Y-m-d',strtotime($DateTime));
            $lsndate = strtotime($lsndate); 
            $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
            $curtime = strtotime($updated_cur_time);
            $lesson_duration = $group->class?->duration;
            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
            $lsnendtime = $lsntimestamp;

            // $statusDetails = $this->determinateClassStatus($group);

            if($curdate <  $lsndate){
                        $statusArray = [
                            'btnText' => __($group->status),
                        ];
            }
            else if($curdate == $lsndate)
            {
                        if($curtime < $lsntime) {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                    'btnID' => 'lec_link2_'.$group->group_id,
                                ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL  && $group->class->learning_tool == 'Zoom') {
                             $statusArray = [
                                'btnText' => __($group->status),
                                 'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->InstructorURI == NULL  && $group->class?->learning_tool == 'BBB')
                        {
                            $statusArray = [
                                'btnText' => __($group->status),
                                 'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->google_meet_link == NULL  && $group->class?->learning_tool == 'Google_Meet')
                        {
                            $statusArray = [
                              'btnText' => __($group->status),
                                'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                          else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class?->learning_tool == 'Zoom')    
                        {
                          
                                $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                'classDetails' => [
                                    'type' => $group->class->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
                                    'user_id' => $group->instructor_id,
                                ],
                              
                                ];
                          
                          
                        }
                         else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB')    
                        { 
                           
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->InstructorURI,
                                'classType' => $group->class?->learning_tool,    
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
                                    'user_id' => $group->instructor_id,
                                ],              
                            ];
                        
                        }
                         else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')    
                        { 
                           
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->google_meet_link,
                                'classType' => $group->class->learning_tool,      
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
                                    'user_id' => $group->instructor_id,
                                ],            
                            ];
                        
                        }
                        else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Zoom')
                        { 
                               $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'BBB')
                        { 
                             $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Google_Meet')
                        { 
                             $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed"&& $group->class->learning_tool == 'Zoom')
                		{
                            
                             $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                              
                                'classDetails' => [
                                    'type' => $group->class->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
                                    'user_id' => $group->instructor_id,
                                ],
                              
                            ];
                        
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed"  && $group->class->learning_tool == 'BBB')
                		{
                           
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->InstructorURI,
                                'classType' => $group->class?->learning_tool,   
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
                                    'user_id' => $group->instructor_id,
                                ],             
                            ];
                           
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')
                		{
                             
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->google_meet_link,
                                'classType' => $group->class->learning_tool,    
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Tutor'),
                                    'user_name' => $group->instructor->first_name.' '.$group->instructor->last_name,
                                    'user_id' => $group->instructor_id,
                                ],              
                            ];
                         
                        }
                    
                          else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Zoom')
                        {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'BBB')
                        {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Google_Meet')
                        {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                    
                }
                if($group->class?->learning_tool == 'Zoom'){
                        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                        if($zoomSettings){
                            $sdk_key = $zoomSettings->sdk_key;
                            $sdk_secret = $zoomSettings->sdk_secret;
                            $api_key = $zoomSettings->api_key;
                            $api_secret = $zoomSettings->api_secret;
                            $api_jwt_token = $zoomSettings->api_jwt_token;
                        }
                        else{
                            $sdk_key = null;
                            $sdk_secret = null;
                            $api_key = null;
                            $api_secret = null;
                            $api_jwt_token = null;
                        }
                    return [
                    // 'sr_no' => $key + 1,
                    'sr_no' =>   (($page - 1) * $perPage) + ($key + 1),
                    'id' => $group->id,
                    'class_id' => $group->class_id,
                    'course_id' => $group->course_id,
                    'course_title' => $group->course?->title,
                    'class_date' => $group->class_date,
                    'class_time' => $group->class_time,
                    // 'zoomApiKey'=> $api_key,
                    // 'zoomApiSecret'=> $api_secret,
                    'zoomSdkKey'=> $sdk_key,
                    'zoomSdkSecret'=> $sdk_secret,
                    // 'zoomApiJwtToken'=> $api_jwt_token,
                    'status' => $statusArray,
                    'learning_tool' => $group->class?->learning_tool,
                    'no_of_students' => $group->class?->no_of_students,
                    ];
                }
                else{
                    return [
                    // 'sr_no' => $key + 1,
                    'sr_no' =>   (($page - 1) * $perPage) + ($key + 1),
                    'id' => $group->id,
                    'class_id' => $group->class_id,
                    'course_id' => $group->course_id,
                    'course_title' => $group->course?->title,
                    'class_date' => $group->class_date,
                    'class_time' => $group->class_time,
                    'status' => $statusArray,
                    'learning_tool' => $group->class?->learning_tool,
                    'no_of_students' => $group->class?->no_of_students,
                    ];
                }
          
        });


        
         /*
        |--------------------------------------------------------------------------
        | SHARED PAGINATION META (DRIVEN BY LARGEST DATASET)
        |--------------------------------------------------------------------------
        */
        $overallTotal = max($individualTotal, $groupTotal);
        $lastPage = (int) ceil($overallTotal / $perPage);

        $from = $overallTotal > 0
            ? (($page - 1) * $perPage) + 1
            : null;

        $to = min($page * $perPage, $overallTotal);

        $baseUrl = url()->current();
        $queryParams = request()->except('page');

        $buildUrl = function ($pageNumber) use ($baseUrl, $queryParams) {
            return $pageNumber > 0
                ? $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $pageNumber]))
                : null;
        };

        return response()->json([
            'success' => true,
            'pagination' => [
                // 'individual_total' => $individualTotal,
                // 'group_total' => $groupTotal,
                'total' => $overallTotal,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $from,
                'to' => $to,
                'links' => [
                    'first' => $buildUrl(1),
                    'last'  => $buildUrl($lastPage),
                    'prev'  => $page > 1 ? $buildUrl($page - 1) : null,
                    'next'  => $page < $lastPage ? $buildUrl($page + 1) : null,
                ],
            ],
            'data' => [
                'title' => 'Class Schedule',
                'individual_classes' => $individualClasses,
                'individual_message' => $individualClasses->isEmpty()
                    ? 'No individual classes found on this page.'
                    : null,
                'group_classes' => $groupClasses,
                'group_message' => $groupClasses->isEmpty()
                    ? 'No group classes found on this page.'
                    : null,
                'base_url' => url('/'),
            ]
        ]);
    }
    else{
       
        $coordinator_user_id = $user->id;
        $getCoordinator = Coordinator::where('user_id', $coordinator_user_id)->first();

        if (!$getCoordinator) {
            return response()->json(['message' => 'Coordinator not found'], 404);
        }

        $coordinator_id = $getCoordinator->id;
        $today_date = date('Y-m-d');


        // Courses handled by coordinator
        $coordinatorCourseIds = CoordinatorCourse::where('coordinator_id', $coordinator_id)
            ->pluck('course_id')
            ->toArray();

          

        $individualBase = ClassDetail::whereIn('course_id', $coordinatorCourseIds);
        $individualQuery = $individualBase
            ->where('class_date', '>=', $today_date)
            ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
            ->orderBy('class_date', 'ASC')
            ->orderBy('class_time', 'DESC');
        $individualTotal = $individualQuery->count();
        $getIndividualClasses = $individualQuery
            ->skip($offset)
            ->take($perPage)
            ->get();
        
        // Individual Classes (No Pagination)
        // $getIndividualClasses = ClassDetail::whereIn('course_id', $coordinatorCourseIds)
        //     ->where('class_date', '>=', $today_date)
        //     ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
        //     ->orderBy('class_date', 'ASC')
        //     ->orderBy('class_time', 'DESC')
        //     ->get();

        $groupBase      = ClassDetail::whereIn('course_id', $coordinatorCourseIds);
        $groupCollection = $groupBase
            ->where('class_date', '>=', $today_date)
            ->whereHas('class', fn($query) => $query->where('type', 'Group'))
            ->orderBy('group_id', 'DESC')
            ->get()
            ->unique('class_id')
            ->values(); // 🔴 REQUIRED;
        $groupTotal = $groupCollection->count();
        $getGroupClasses = $groupCollection
            ->slice($offset, $perPage)
            ->values();

        // Group Classes (No Pagination)
        // $getGroupClasses = ClassDetail::whereIn('course_id', $coordinatorCourseIds)
        //     ->where('class_date', '>=', $today_date)
        //     ->whereHas('class', fn($query) => $query->where('type', 'Group'))
        //     ->orderBy('group_id', 'DESC')
        //     ->get()
        //     ->unique('class_id');


            

        // Format Individual
        $individualClasses = $getIndividualClasses->map(function ($individual, $key) use ($coordinator_id,$page, $perPage) {
            // $statusDetails = $this->determinateClassStatus($individual);

              $statusArray = [
                                    'btnText' => __($individual->status),
                ];
            $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$coordinator_id,'coordinator');
            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
            $updated_time = date('h:i A',strtotime($DateTime));
            $lsntime = strtotime($updated_time);
            $classTime = date('d-m-Y h:i A',strtotime($DateTime));
            $curdate = date("Y-m-d",strtotime($CurrentDateTime));
            $curdate = strtotime($curdate);
            $lsndate = date('Y-m-d',strtotime($DateTime));
            $lsndate = strtotime($lsndate); 
            $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
            $curtime = strtotime($updated_cur_time);
            $lesson_duration = $individual->class?->duration;
            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
            $lsnendtime = $lsntimestamp;

                if($curdate <  $lsndate){
                        $statusArray = [
                            'btnText' => __($individual->status),
                        ];
                }
                else if($curdate == $lsndate)
                {
                        if($curtime < $lsntime) {
                                $statusArray = [
                                     'btnText' => __($individual->status),
                                    'btnID' => 'lec_link1_'.$individual->id,
                                ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->meeting_id == NULL && $individual->class->learning_tool == 'Zoom') {
                             $statusArray = [
                                'btnText' => __($individual->status),
                                'btnID' => 'lec_link1_'.$individual->id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->AcademicURI == NULL && $individual->class?->learning_tool == 'BBB')
                        {
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'btnID' => 'lec_link1_'.$individual->id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->google_meet_link == NULL && $individual->class?->learning_tool == 'Google_Meet')
                        {
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'btnID' => 'lec_link1_'.$individual->id,
                            ];
                        }
                          else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class?->learning_tool == 'Zoom')    
                        {
                          
                                $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                // 'on_click_api_route' => route('student-start-zoom-class'),
                                'classDetails' => [
                                    'type' => $individual->class?->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],
                              
                            ];
                         
                          
                        }
                         else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')    
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->AcademicURI ?? $individual->StudentURI,
                                'classType' => $individual->class?->learning_tool,     
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],           
                            ];
                        }
                         else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')    
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,  
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],              
                            ];
                        }
                        else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Zoom')
                        { 
                            
                                $statusArray = [
                                        'btnText' => __($individual->status),
                                ];
                        }
                         else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'BBB')
                        { 

                              $statusArray = [
                                        'btnText' => __($individual->status),
                                ];
                        
                        }
                         else if($curtime == $lsntime &&($individual->status == "Completed" || $individual->status == "Missed") && $individual->class->learning_tool == 'Google_Meet')
                        { 
                              $statusArray = [
                                        'btnText' => __($individual->status),
                                ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Zoom')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                'btnClass' =>  __('create_zoom_meeting_link'),
                                // 'on_click_api_route' => route('student-start-zoom-class'),
                                'classDetails' => [
                                    'type' => $individual->class?->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],
                              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->AcademicURI == NULL) && $individual->class->learning_tool == 'BBB')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->AcademicURI ?? $individual->StudentURI,
                                'classType' => $individual->class?->learning_tool, 
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],               
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && ($individual->meeting_id == NULL) && $individual->class->learning_tool == 'Google_Meet')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,   
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],             
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                        { 
                            $statusArray = [
                                 'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                'btnClass' =>  __('create_zoom_meeting_link'),
                                // 'on_click_api_route' => route('student-start-zoom-class'),
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],
                              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->AcademicURI ?? $individual->StudentURI,
                                'classType' => $individual->class?->learning_tool,  
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')
                        { 
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'started' => 1,
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,    
                                'classDetails' => [
                                    'type' => $individual->class->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],            
                            ];
                        }
                    
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Zoom')
                        {
                           $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'BBB')
                        {
                           $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $individual->class->learning_tool == 'Google_Meet')
                        {
                          $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                        else{
                                $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                        }
                }
                else{
                      $statusArray = [
                                    'btnText' => __($individual->status),
                                ];
                }
                  if($individual->class?->learning_tool == 'Zoom'){
                        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                        if($zoomSettings){
                            $sdk_key = $zoomSettings->sdk_key;
                            $sdk_secret = $zoomSettings->sdk_secret;
                            $api_key = $zoomSettings->api_key;
                            $api_secret = $zoomSettings->api_secret;
                            $api_jwt_token = $zoomSettings->api_jwt_token;
                        }
                        else{
                            $sdk_key = null;
                            $sdk_secret = null;
                            $api_key = null;
                            $api_secret = null;
                            $api_jwt_token = null;
                        }

                        return [
                            // 'sr_no' => $key + 1,
                            'sr_no' =>   (($page - 1) * $perPage) + ($key + 1),
                            'id' => $individual->id,
                            'class_id' => $individual->class_id,
                            'course_id' => $individual->course_id,
                            'course_title' => $individual->course?->title,
                            'student_id' => $individual->student_id,
                            'student_name' => $individual->student?->user?->name,
                            'instructor_id' => $individual->instructor_id,
                            'instructor_name' => $individual->instructor?->user?->name,
                            'class_date' => $individual->class_date,
                            'class_time' => $individual->class_time,
                            // 'zoomApiKey'=> $api_key,
                            // 'zoomApiSecret'=> $api_secret,
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                            // 'zoomApiJwtToken'=> $api_jwt_token,
                            'status' => $statusArray,
                            'learning_tool' => $individual->class?->learning_tool,
                        ];
                    }
                    else{
                        return [
                            // 'sr_no' => $key + 1,
                            'sr_no' =>   (($page - 1) * $perPage) + ($key + 1),
                            'id' => $individual->id,
                            'class_id' => $individual->class_id,
                            'course_id' => $individual->course_id,
                            'course_title' => $individual->course?->title,
                            'student_id' => $individual->student_id,
                            'student_name' => $individual->student?->user?->name,
                            'instructor_id' => $individual->instructor_id,
                            'instructor_name' => $individual->instructor?->user?->name,
                            'class_date' => $individual->class_date,
                            'class_time' => $individual->class_time,
                            'status' => $statusArray,
                            'learning_tool' => $individual->class?->learning_tool,
                        ];
                    }
         
        });

        // Format Group
        $groupClasses = $getGroupClasses->map(function ($group, $key) use ($coordinator_id,$page, $perPage) {

               $statusArray = [
                    'btnText' => __($group->status),
                ];

            $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$coordinator_id,'coordinator');
            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
            $updated_time = date('h:i A',strtotime($DateTime));
            $lsntime = strtotime($updated_time);
            $classTime = date('d-m-Y h:i A',strtotime($DateTime));
            $curdate = date("Y-m-d",strtotime($CurrentDateTime));
            $curdate = strtotime($curdate);
            $lsndate = date('Y-m-d',strtotime($DateTime));
            $lsndate = strtotime($lsndate); 
            $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
            $curtime = strtotime($updated_cur_time);
            $lesson_duration = $group->class?->duration;
            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
            $lsnendtime = $lsntimestamp;

            // $statusDetails = $this->determinateClassStatus($group);

            if($curdate <  $lsndate){
                $statusArray = [
                    'btnText' => __($group->status),
                ];
            }
            else if($curdate == $lsndate)
            {
                        if($curtime < $lsntime) {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                    'btnID' => 'lec_link2_'.$group->group_id,
                                ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL  && $group->class->learning_tool == 'Zoom') {
                             $statusArray = [
                                'btnText' => __($group->status),
                                 'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->AcademicURI == NULL  && $group->class?->learning_tool == 'BBB')
                        {
                            $statusArray = [
                                'btnText' => __($group->status),
                                 'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->google_meet_link == NULL  && $group->class?->learning_tool == 'Google_Meet')
                        {
                            $statusArray = [
                              'btnText' => __($group->status),
                                'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                          else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class?->learning_tool == 'Zoom')    
                        {
                          
                                $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                                'classDetails' => [
                                    'type' => $group->class->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],
                              
                                ];
                          
                          
                        }
                         else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB')    
                        { 
                           
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->AcademicURI ?? $group->StudentURI,
                                'classType' => $group->class?->learning_tool,    
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],              
                            ];
                        
                        }
                         else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')    
                        { 
                           
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->google_meet_link,
                                'classType' => $group->class->learning_tool,      
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],            
                            ];
                        
                        }
                        else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Zoom')
                        { 
                               $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'BBB')
                        { 
                             $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if($curtime == $lsntime &&($group->status == "Completed" || $group->status == "Missed") && $group->class->learning_tool == 'Google_Meet')
                        { 
                             $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed"&& $group->class->learning_tool == 'Zoom')
                		{
                            
                             $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  "javascript:void(0);",
                              
                                'classDetails' => [
                                    'type' => $group->class->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],
                              
                            ];
                        
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed"  && $group->class->learning_tool == 'BBB')
                		{
                           
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->AcademicURI ?? $group->StudentURI,
                                'classType' => $group->class?->learning_tool,   
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],             
                            ];
                           
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')
                		{
                             
                            $statusArray = [
                                'btnText' => __($group->status),
                                'started' => 1,
                                'btnHref' =>  $group->google_meet_link,
                                'classType' => $group->class->learning_tool,    
                                'classDetails' => [
                                    'type' => $group->class?->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' =>__('Coordinator'),
                                    'user_name' => getCoordinatorNameFromID($coordinator_id),
                                    'user_id' => $coordinator_id,
                                ],              
                            ];
                         
                        }
                    
                          else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Zoom')
                        {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'BBB')
                        {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Google_Meet')
                        {
                                $statusArray = [
                                    'btnText' => __($group->status),
                                ];
                        }
                    
                }
                if($group->class?->learning_tool == 'Zoom'){
                        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                        if($zoomSettings){
                            $sdk_key = $zoomSettings->sdk_key;
                            $sdk_secret = $zoomSettings->sdk_secret;
                            $api_key = $zoomSettings->api_key;
                            $api_secret = $zoomSettings->api_secret;
                            $api_jwt_token = $zoomSettings->api_jwt_token;
                        }
                        else{
                            $sdk_key = null;
                            $sdk_secret = null;
                            $api_key = null;
                            $api_secret = null;
                            $api_jwt_token = null;
                        }
                    return [
                    'sr_no' => $key + 1,
                    'id' => $group->id,
                    'class_id' => $group->class_id,
                    'course_id' => $group->course_id,
                    'course_title' => $group->course?->title,
                    'class_date' => $group->class_date,
                    'class_time' => $group->class_time,
                    // 'zoomApiKey'=> $api_key,
                    // 'zoomApiSecret'=> $api_secret,
                    'zoomSdkKey'=> $sdk_key,
                    'zoomSdkSecret'=> $sdk_secret,
                    // 'zoomApiJwtToken'=> $api_jwt_token,
                    'status' => $statusArray,
                    'learning_tool' => $group->class?->learning_tool,
                    'no_of_students' => $group->class?->no_of_students,
                    ];
                }
                else{
                    return [
                    'sr_no' => $key + 1,
                    'id' => $group->id,
                    'class_id' => $group->class_id,
                    'course_id' => $group->course_id,
                    'course_title' => $group->course?->title,
                    'class_date' => $group->class_date,
                    'class_time' => $group->class_time,
                    'status' => $statusArray,
                    'learning_tool' => $group->class?->learning_tool,
                    'no_of_students' => $group->class?->no_of_students,
                    ];
                }
          
        });

   


         /*
        |--------------------------------------------------------------------------
        | SHARED PAGINATION META (DRIVEN BY LARGEST DATASET)
        |--------------------------------------------------------------------------
        */
        $overallTotal = max($individualTotal, $groupTotal);
        $lastPage = (int) ceil($overallTotal / $perPage);

        $from = $overallTotal > 0
            ? (($page - 1) * $perPage) + 1
            : null;

        $to = min($page * $perPage, $overallTotal);

        $baseUrl = url()->current();
        $queryParams = request()->except('page');

        $buildUrl = function ($pageNumber) use ($baseUrl, $queryParams) {
            return $pageNumber > 0
                ? $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $pageNumber]))
                : null;
        };


        return response()->json([
            'success' => true,
            'pagination' => [
                // 'individual_total' => $individualTotal,
                // 'group_total' => $groupTotal,
                'total' => $overallTotal,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $from,
                'to' => $to,
                'links' => [
                    'first' => $buildUrl(1),
                    'last'  => $buildUrl($lastPage),
                    'prev'  => $page > 1 ? $buildUrl($page - 1) : null,
                    'next'  => $page < $lastPage ? $buildUrl($page + 1) : null,
                ],
            ],
            'data' => [
                'title' => 'Class Schedule',
                'individual_classes' => $individualClasses,
                'individual_message' => $individualClasses->isEmpty()
                    ? 'No individual classes found on this page.'
                    : null,
                'group_classes' => $groupClasses,
                'group_message' => $groupClasses->isEmpty()
                    ? 'No group classes found on this page.'
                    : null,
                'base_url' => url('/'),
            ]
        ]);
    }
        
    }

    protected function determinateClassStatus($class)
    {

        $DateTime = changetimeByZone($class->class_date.' '.date('h:i A',strtotime($class->class_time)),$class->instructor_id,'instructor');
        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$class->instructor_id,'instructor');
        $curdate = date("Y-m-d",strtotime($CurrentDateTime));
        $currentDate = strtotime($curdate);
        $lsndate = date('Y-m-d',strtotime($DateTime));
        $updated_time = date('h:i A',strtotime($DateTime));
        $classDate = strtotime($lsndate);
        $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
        $currentTime = strtotime($updated_cur_time);
        $classTime = strtotime($updated_time);
        $classEndTime = $classTime + ($class->class->duration * 60);



        $tool = $class->class->learning_tool;
        $isInstructorJoined = $class->is_instructor_joined == '1' || $class->is_instructor_joined == 1;
        $isCompleted = $class->status == "Completed";
        $isMissed = $class->status == "Missed";
        $isPending = $class->status == "Pending";
        $isScheduled = $class->status == "Scheduled";
     
        // Future class
        if ($currentDate < $classDate) {
            return ['btnText' => __('Scheduled')];
        }
        else if ($currentDate == $classDate) {
            // Class hasn't started yet
            $waitingWindowBefore = $classTime - 120; // 2 mins before class
            $waitingWindowAfter = $classTime + 600; // 10 mins after class
            // if ($currentTime < $classTime && $currentTime < $waitingWindowAfter) {
            //     return ['btnText' => __('Waiting')];
            // }
            if ($currentTime < $classTime) {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime && $class->meeting_id == NULL && $tool == 'Zoom') {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime && $class->InstructorURI == NULL && $tool == 'BBB') {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime && $class->google_meet_link == NULL && $tool == 'Google_Meet') {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime) {    // Class is ongoing
                if ($isCompleted || $isMissed) {
                    return ['btnText' => __($class->status)];
                }
                else{
                    switch ($tool) {
                        case 'Zoom':
                            return [
                                'btnText' => __('Start Class'),
                                'btnHref' => "javascript:void(0);",
                                'on_click_api_route' => route('student-start-zoom-class'),
                                'btnClass' => __('create_zoom_meeting_link'),
                                'classDetails' => [
                                    'type' => $tool,
                                    'lesson_id' => $class->id,
                                    'meeting_id' => (int)$class->meeting_id,
                                    'user_type' => __('Student'),
                                    'user_name' => $class->student->first_name.' '.$class->student->last_name,
                                    'user_id' => $class->student_id,
                                ],
                            ];
                            
                        case 'BBB':
                            return [
                                'btnText' => __('Start Class'),
                                'btnHref' => $class->InstructorURI,
                                'classType' => $tool,                
                            ];
                            
                        case 'Google_Meet':
                            return [
                                'btnText' => __('Start Class'),
                                'btnHref' => $class->google_meet_link,
                                'classType' => $tool,                
                            ];
                            
                        default:
                            return ['btnText' => __('Waiting')];
                    }
                }   
            }  
            else if ($currentTime == $classEndTime) {  // Class just ended
                if ($isCompleted || $isMissed) {
                    return ['btnText' => __($class->status)];
                }
                else if ($isInstructorJoined) {
                    $this->updateClassStatus($class, $tool, 'Completed');
                    return ['btnText' => __('Completed')];
                } 
                else {
                    $this->updateClassStatus($class, $tool, 'Missed');
                    return ['btnText' => __('Missed')];
                }
            }
            else if ($currentTime > $classEndTime) {  // Class ended earlier today
                if ($isCompleted) {
                    return ['btnText' => __('Completed')];
                }
                else if ($isMissed) {
                    if (!empty($class->meeting_id)) {
                        $res = check_missed_status($class->meeting_id);
                        return ['btnText' => __($res == 'Completed' ? 'Completed' : 'Missed')];
                    }
                    else{
                        return ['btnText' => __('Missed')];
                    }    
                }
                else if ($isPending) {
                    if ($isInstructorJoined) {
                        $this->updateClassStatus($class, $tool, 'Completed');
                        return ['btnText' => __('Completed')];
                    }
                     else {
                        $this->updateClassStatus($class, $tool, 'Missed');
                        return ['btnText' => __('Missed')];
                    }
                }
                else{
                    return ['btnText' => __('Missed')];
                }        
            }
        }
        else if ($currentDate > $classDate) {   // Past class 
            if ($isCompleted) {
                return ['btnText' => __('Completed')];
            }
            else if ($isMissed) {
                if (!empty($class->meeting_id)) {
                    $res = check_missed_status($class->meeting_id);
                    return ['btnText' => __($res == 'Completed' ? 'Completed' : 'Missed')];
                }
                else{
                    return ['btnText' => __('Missed')];
                }    
            }
            else if ($isPending) {
                if ($isInstructorJoined) {
                    $this->updateClassStatus($class, $tool, 'Completed');
                    return ['btnText' => __('Completed')];
                } 
                else {
                    $this->updateClassStatus($class, $tool, 'Missed');
                    return ['btnText' => __('Missed')];
                }
            }
            else{
                return ['btnText' => __('Missed')];
            } 
        }
        else if ($isCompleted) {      // Default case
            return ['btnText' => __('Completed')];
        }
        else if ($isMissed) {
            if (!empty($class->meeting_id)) {
                $res = check_missed_status($class->meeting_id);
                return ['btnText' => __($res == 'Completed' ? 'Completed' : 'Missed')];
            }
            else{
                return ['btnText' => __('Missed')];
            }   
        }
        else{
            return ['btnText' => __('Missed')];
        }
      
    }

    protected function updateClassStatus($class, $tool, $status)
    {
        if ($tool == 'Zoom') {
            updateLessonStatusCompleteZoom($class->id, $class->meeting_id, null);
        } else {
            updateLessonStatusComplete($class->id, $class->meeting_id, null);
        }
    }


// public function classHistoryApiBackUp()
    // {
    //     try {
    //         $user = auth()->user();

    //         if (!$user || $user->role != 2) {
    //             return response()->json(['message' => 'Unauthorized access.'], 403);
    //         }

    //         $instructor = Instructor::where('user_id', $user->id)->first();

    //         if (!$instructor) {
    //             return response()->json(['message' => 'Instructor not found.'], 404);
    //         }

    //         $instructor_id = $instructor->id;
    //         $today_date = date('Y-m-d');

    //         // Individual Class History (past)
    //         // $getIndividualClassesHistory = ClassDetail::where('instructor_id', $instructor_id)
    //         //     ->where('class_date', '<=', $today_date)
    //         //     ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
    //         //     ->orderBy('id', 'DESC')
    //         //     ->orderBy('class_date', 'ASC')
    //         //     ->orderBy('class_time', 'ASC')
    //         //     ->get();
    //         $getIndividualClassesHistory = ClassDetail::where('instructor_id', $instructor_id)
    //             ->where('class_date', '<', $today_date)
    //             ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
    //             // ->orderBy('id', 'DESC')
    //             ->orderBy('class_date', 'ASC')
    //             ->orderBy('class_time', 'DESC')
    //             ->get();

    //         // Group Class History (past)
    //         $getGroupClassesHistory = ClassDetail::where('instructor_id', $instructor_id)
    //             ->where('class_date', '<', $today_date)
    //             ->whereHas('class', fn($query) => $query->where('type', 'Group'))
    //             // ->orderBy('id', 'DESC')
    //             ->orderBy('group_id', 'DESC')
    //             ->get()
    //             ->unique('class_id');
    //             // ->unique(fn($item) => $item->class_id . '_' . $item->class_date);
    //         $status1 = 'Pending';
    //         $status2 = 'Pending';
    //         // Format Individual Classes
    //         $individualClasses = $getIndividualClassesHistory->map(function ($individual, $key) {

    //             $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->instructor_id,'instructor');
    //             $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$individual->instructor_id,'instructor');
    //             $updated_time = date('h:i A',strtotime($DateTime));
    //             $lsntime = strtotime($updated_time);
    //             $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //             $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //             $curdate = strtotime($curdate);
    //             $lsndate = date('Y-m-d',strtotime($DateTime));
    //             $lsndate = strtotime($lsndate); 
    //             $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //             $curtime = strtotime($updated_cur_time);
    //             $lesson_duration = $individual->class?->duration;
    //             $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //             $lsnendtime = $lsntimestamp;

    //              if($curdate > $lsndate)
    //                 {
    //                     if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == 1)
    //                     {
                        
    //                         $status1 = 'Completed';
    //                     }
    //                     else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == 1 ) {
                         
    //                         $status1 = 'Completed';
    //                     }
    //                     else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' &&  $individual->is_instructor_joined == 1) {
                       
    //                         $status1 = 'Completed';
    //                     }
    //                     else if($individual->status == "Completed"){
    //                         $status1 = 'Completed';
    //                     }
    //                     else if(($individual->status == "Pending" || $individual->status == "Scheduled")){
    //                         $status1 = $individual->status;
    //                     }
    //                     else
    //                         {
    //                             if($individual->status == "Missed"){
    //                                 $status1 = 'Missed';
    //                             }
    //                             else{
    //                                 $status1 = $individual->status;
    //                             }
    //                         }
    //                 }
    //                 else{
    //                     if($individual->status == "Completed"){
    //                         $status1 = 'Completed';
    //                     }
    //                     else{
    //                         {
    //                             if($individual->status == "Missed"){
    //                                 $status1 = 'Missed';
    //                             }
    //                             else{
    //                                 $status1 = $individual->status;
    //                             }
    //                         }
    //                     }
    //                 }
    //             return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $individual->id,
    //                 'class_id' => $individual->class_id,
    //                 'course_id' => $individual->course_id,
    //                 'course_title' => $individual->course?->title,
    //                 'student_id' => $individual->student_id,
    //                 'student_name' => $individual->student?->user?->name,
    //                 'class_date_time' => $individual->class_date . ' ' . $individual->class_time,
    //                 'status' => $status1,
    //                 'learning_tool' => $individual->class?->learning_tool,
    //             ];
    //         });

    //         // Format Group Classes
    //         $groupClasses = $getGroupClassesHistory->values()->map(function ($group, $key) {
    //             $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->instructor_id,'instructor');
    //             $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$group->instructor_id,'instructor');
    //             $updated_time = date('h:i A',strtotime($DateTime));
    //             $lsntime = strtotime($updated_time);
    //             $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //             $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //             $curdate = strtotime($curdate);
    //             $lsndate = date('Y-m-d',strtotime($DateTime));
    //             $lsndate = strtotime($lsndate); 
    //             $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //             $curtime = strtotime($updated_cur_time);
    //             $lesson_duration = $group->class?->duration;
    //             $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //             $lsnendtime = $lsntimestamp;

    //             if($curdate > $lsndate)
    //                 {
    //                     if($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == 1)
    //                         {	
    //                             $status2 = 'Completed';
    //                         }
    //                         else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == 1 ) {
                              
    //                             $status2 = 'Completed';
    //                         }
    //                         else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == 1) {
                             
    //                             $status2 = 'Completed';
    //                         }
    //                         else if($group->status == "Completed"){
    //                             $status2 = 'Completed';
    //                         }
    //                         else if(($group->status == "Pending" || $group->status == "Scheduled")){
    //                             $status2 = $group->status;
    //                         }
    //                         else{
    //                             if($group->status == "Missed"){
    //                                  $status2 = 'Missed';
    //                             }
    //                             else{
    //                                  $status2 = $group->status;
    //                             }
    //                         }
    //                 }
    //                 else{
    //                     if($group->status == "Completed"){
    //                         $status2 = 'Completed';
    //                     }
    //                     else{
    //                          if($group->status == "Missed"){
    //                                  $status2 = 'Missed';
    //                             }
    //                             else{
    //                                  $status2 = $group->status;
    //                             }
    //                     }
    //                 }
    //             return [
    //                 'sr_no' => $key + 1,
    //                 'id' => $group->id,
    //                 'class_id' => $group->class_id,
    //                 'course_id' => $group->course_id,
    //                 'course_title' => $group->course?->title,
    //                 'class_date_time' => $group->class_date . ' ' . $group->class_time,
    //                 'status' => $status2,
    //                 'learning_tool' => $group->class?->learning_tool,
    //                 'no_of_students' => $group->class?->no_of_students,
    //             ];
    //         })->values();

    //         return response()->json([
    //             'success' => true,
    //             'data' => [
    //                 'title' => 'Class History',
    //                 'individual_classes' => $individualClasses,
    //                 'group_classes' => $groupClasses,
    //                 'base_url' => url('/'),
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         dd($e);
    //         return response()->json([
    //             'message' => 'Something went wrong while fetching class history.',
    //             'error' => $e->getMessage()
    //         ], 400);
    //     }
// }




//   public function classHistoryApi()
//     {
//         try {
//             $user = auth()->user();

//             if (!$user || ($user->role != 2 && $user->role != 5)) {
//                 return response()->json(['message' => 'Unauthorized access.'], 403);
//             }


//         if($user->role == 2){
//             $instructor = Instructor::where('user_id', $user->id)->first();

//             if (!$instructor) {
//                 return response()->json(['message' => 'Instructor not found.'], 404);
//             }

//             $instructor_id = $instructor->id;
//             $today_date = date('Y-m-d');

         
//             $getIndividualClassesHistory = ClassDetail::where('instructor_id', $instructor_id)
//                 ->where('class_date', '<', $today_date)
//                 ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
//                 // ->orderBy('id', 'DESC')
//                 ->orderBy('class_date', 'ASC')
//                 ->orderBy('class_time', 'DESC')
//                 ->get();

//             // Group Class History (past)
//             $getGroupClassesHistory = ClassDetail::where('instructor_id', $instructor_id)
//                 ->where('class_date', '<', $today_date)
//                 ->whereHas('class', fn($query) => $query->where('type', 'Group'))
//                 // ->orderBy('id', 'DESC')
//                 ->orderBy('group_id', 'DESC')
//                 ->get()
//                 ->unique('class_id');
//                 // ->unique(fn($item) => $item->class_id . '_' . $item->class_date);
//             $status1 = 'Pending';
//             $status2 = 'Pending';
//             // Format Individual Classes
//             $individualClasses = $getIndividualClassesHistory->map(function ($individual, $key) {

//                 $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->instructor_id,'instructor');
//                 $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$individual->instructor_id,'instructor');
//                 $updated_time = date('h:i A',strtotime($DateTime));
//                 $lsntime = strtotime($updated_time);
//                 $classTime = date('d-m-Y h:i A',strtotime($DateTime));
//                 $curdate = date("Y-m-d",strtotime($CurrentDateTime));
//                 $curdate = strtotime($curdate);
//                 $lsndate = date('Y-m-d',strtotime($DateTime));
//                 $lsndate = strtotime($lsndate); 
//                 $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
//                 $curtime = strtotime($updated_cur_time);
//                 $lesson_duration = $individual->class?->duration;
//                 $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
//                 $lsnendtime = $lsntimestamp;

//                  if($curdate > $lsndate)
//                     {
//                         if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == 1)
//                         {
                        
//                             $status1 = 'Completed';
//                         }
//                         else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == 1 ) {
                         
//                             $status1 = 'Completed';
//                         }
//                         else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' &&  $individual->is_instructor_joined == 1) {
                       
//                             $status1 = 'Completed';
//                         }
//                         else if($individual->status == "Completed"){
//                             $status1 = 'Completed';
//                         }
//                         else if(($individual->status == "Pending" || $individual->status == "Scheduled")){
//                             $status1 = $individual->status;
//                         }
//                         else
//                             {
//                                 if($individual->status == "Missed"){
//                                     $status1 = 'Missed';
//                                 }
//                                 else{
//                                     $status1 = $individual->status;
//                                 }
//                             }
//                     }
//                     else{
//                         if($individual->status == "Completed"){
//                             $status1 = 'Completed';
//                         }
//                         else{
//                             {
//                                 if($individual->status == "Missed"){
//                                     $status1 = 'Missed';
//                                 }
//                                 else{
//                                     $status1 = $individual->status;
//                                 }
//                             }
//                         }
//                     }
//                 return [
//                     'sr_no' => $key + 1,
//                     'id' => $individual->id,
//                     'class_id' => $individual->class_id,
//                     'course_id' => $individual->course_id,
//                     'course_title' => $individual->course?->title,
//                     'student_id' => $individual->student_id,
//                     'student_name' => $individual->student?->user?->name,
//                     'class_date_time' => $individual->class_date . ' ' . $individual->class_time,
//                     'status' => $status1,
//                     'learning_tool' => $individual->class?->learning_tool,
//                 ];
//             });

//             // Format Group Classes
//             $groupClasses = $getGroupClassesHistory->values()->map(function ($group, $key) {
//                 $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->instructor_id,'instructor');
//                 $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$group->instructor_id,'instructor');
//                 $updated_time = date('h:i A',strtotime($DateTime));
//                 $lsntime = strtotime($updated_time);
//                 $classTime = date('d-m-Y h:i A',strtotime($DateTime));
//                 $curdate = date("Y-m-d",strtotime($CurrentDateTime));
//                 $curdate = strtotime($curdate);
//                 $lsndate = date('Y-m-d',strtotime($DateTime));
//                 $lsndate = strtotime($lsndate); 
//                 $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
//                 $curtime = strtotime($updated_cur_time);
//                 $lesson_duration = $group->class?->duration;
//                 $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
//                 $lsnendtime = $lsntimestamp;

//                 if($curdate > $lsndate)
//                     {
//                         if($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == 1)
//                             {	
//                                 $status2 = 'Completed';
//                             }
//                             else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == 1 ) {
                              
//                                 $status2 = 'Completed';
//                             }
//                             else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == 1) {
                             
//                                 $status2 = 'Completed';
//                             }
//                             else if($group->status == "Completed"){
//                                 $status2 = 'Completed';
//                             }
//                             else if(($group->status == "Pending" || $group->status == "Scheduled")){
//                                 $status2 = $group->status;
//                             }
//                             else{
//                                 if($group->status == "Missed"){
//                                      $status2 = 'Missed';
//                                 }
//                                 else{
//                                      $status2 = $group->status;
//                                 }
//                             }
//                     }
//                     else{
//                         if($group->status == "Completed"){
//                             $status2 = 'Completed';
//                         }
//                         else{
//                              if($group->status == "Missed"){
//                                      $status2 = 'Missed';
//                                 }
//                                 else{
//                                      $status2 = $group->status;
//                                 }
//                         }
//                     }
//                 return [
//                     'sr_no' => $key + 1,
//                     'id' => $group->id,
//                     'class_id' => $group->class_id,
//                     'course_id' => $group->course_id,
//                     'course_title' => $group->course?->title,
//                     'class_date_time' => $group->class_date . ' ' . $group->class_time,
//                     'status' => $status2,
//                     'learning_tool' => $group->class?->learning_tool,
//                     'no_of_students' => $group->class?->no_of_students,
//                 ];
//             })->values();

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'title' => 'Class History',
//                     'individual_classes' => $individualClasses,
//                     'group_classes' => $groupClasses,
//                     'base_url' => url('/'),
//                 ]
//             ]);
//         }
//         else{
//             $coordinator = Coordinator::where('user_id', $user->id)->first();

//             if (!$coordinator) {
//                 return response()->json(['message' => 'Coordinator not found.'], 404);
//             }

//             $coordinator_id = $coordinator->id;
//             $today_date = date('Y-m-d');

         

//             // Courses handled by coordinator
//             $coordinatorCourseIds = CoordinatorCourse::where('coordinator_id', $coordinator_id)
//                 ->pluck('course_id')
//                 ->toArray();

//             $getIndividualClassesHistory = ClassDetail::whereIn('course_id', $coordinatorCourseIds)
//                 ->where('class_date', '<', $today_date)
//                 ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
//                 // ->orderBy('id', 'DESC')
//                 ->orderBy('class_date', 'ASC')
//                 ->orderBy('class_time', 'DESC')
//                 ->get();

//             // Group Class History (past)
//             $getGroupClassesHistory = ClassDetail::whereIn('course_id', $coordinatorCourseIds)
//                 ->where('class_date', '<', $today_date)
//                 ->whereHas('class', fn($query) => $query->where('type', 'Group'))
//                 // ->orderBy('id', 'DESC')
//                 ->orderBy('group_id', 'DESC')
//                 ->get()
//                 ->unique('class_id');
//                 // ->unique(fn($item) => $item->class_id . '_' . $item->class_date);
//             $status1 = 'Pending';
//             $status2 = 'Pending';
//             // Format Individual Classes
//             $individualClasses = $getIndividualClassesHistory->map(function ($individual, $key) use ($coordinator_id) {

//                 $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$coordinator_id,'coordinator');
//                 $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
//                 $updated_time = date('h:i A',strtotime($DateTime));
//                 $lsntime = strtotime($updated_time);
//                 $classTime = date('d-m-Y h:i A',strtotime($DateTime));
//                 $curdate = date("Y-m-d",strtotime($CurrentDateTime));
//                 $curdate = strtotime($curdate);
//                 $lsndate = date('Y-m-d',strtotime($DateTime));
//                 $lsndate = strtotime($lsndate); 
//                 $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
//                 $curtime = strtotime($updated_cur_time);
//                 $lesson_duration = $individual->class?->duration;
//                 $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
//                 $lsnendtime = $lsntimestamp;

//                  if($curdate > $lsndate)
//                     {
//                         if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == 1)
//                         {
                        
//                             $status1 = 'Completed';
//                         }
//                         else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == 1 ) {
                         
//                             $status1 = 'Completed';
//                         }
//                         else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' &&  $individual->is_instructor_joined == 1) {
                       
//                             $status1 = 'Completed';
//                         }
//                         else if($individual->status == "Completed"){
//                             $status1 = 'Completed';
//                         }
//                         else if(($individual->status == "Pending" || $individual->status == "Scheduled")){
//                             $status1 = $individual->status;
//                         }
//                         else
//                             {
//                                 if($individual->status == "Missed"){
//                                     $status1 = 'Missed';
//                                 }
//                                 else{
//                                     $status1 = $individual->status;
//                                 }
//                             }
//                     }
//                     else{
//                         if($individual->status == "Completed"){
//                             $status1 = 'Completed';
//                         }
//                         else{
//                             {
//                                 if($individual->status == "Missed"){
//                                     $status1 = 'Missed';
//                                 }
//                                 else{
//                                     $status1 = $individual->status;
//                                 }
//                             }
//                         }
//                     }
//                 return [
//                     'sr_no' => $key + 1,
//                     'id' => $individual->id,
//                     'class_id' => $individual->class_id,
//                     'course_id' => $individual->course_id,
//                     'course_title' => $individual->course?->title,
//                     'student_id' => $individual->student_id,
//                     'student_name' => $individual->student?->user?->name,
//                     'instructor_id' => $individual->instructor_id,
//                     'instructor_name' => $individual->instructor?->user?->name,
//                     'class_date_time' => $individual->class_date . ' ' . $individual->class_time,
//                     'status' => $status1,
//                     'learning_tool' => $individual->class?->learning_tool,
//                 ];
//             });

//             // Format Group Classes
//             $groupClasses = $getGroupClassesHistory->values()->map(function ($group, $key) use ($coordinator_id) {
//                 $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$coordinator_id,'coordinator');
//                 $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
//                 $updated_time = date('h:i A',strtotime($DateTime));
//                 $lsntime = strtotime($updated_time);
//                 $classTime = date('d-m-Y h:i A',strtotime($DateTime));
//                 $curdate = date("Y-m-d",strtotime($CurrentDateTime));
//                 $curdate = strtotime($curdate);
//                 $lsndate = date('Y-m-d',strtotime($DateTime));
//                 $lsndate = strtotime($lsndate); 
//                 $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
//                 $curtime = strtotime($updated_cur_time);
//                 $lesson_duration = $group->class?->duration;
//                 $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
//                 $lsnendtime = $lsntimestamp;

//                 if($curdate > $lsndate)
//                     {
//                         if($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == 1)
//                             {	
//                                 $status2 = 'Completed';
//                             }
//                             else if ($group->class->learning_tool == 'BBB' && $group->is_instructor_joined == 1 ) {
                              
//                                 $status2 = 'Completed';
//                             }
//                             else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == 1) {
                             
//                                 $status2 = 'Completed';
//                             }
//                             else if($group->status == "Completed"){
//                                 $status2 = 'Completed';
//                             }
//                             else if(($group->status == "Pending" || $group->status == "Scheduled")){
//                                 $status2 = $group->status;
//                             }
//                             else{
//                                 if($group->status == "Missed"){
//                                      $status2 = 'Missed';
//                                 }
//                                 else{
//                                      $status2 = $group->status;
//                                 }
//                             }
//                     }
//                     else{
//                         if($group->status == "Completed"){
//                             $status2 = 'Completed';
//                         }
//                         else{
//                              if($group->status == "Missed"){
//                                      $status2 = 'Missed';
//                                 }
//                                 else{
//                                      $status2 = $group->status;
//                                 }
//                         }
//                     }
//                 return [
//                     'sr_no' => $key + 1,
//                     'id' => $group->id,
//                     'class_id' => $group->class_id,
//                     'course_id' => $group->course_id,
//                     'course_title' => $group->course?->title,
//                     'class_date_time' => $group->class_date . ' ' . $group->class_time,
//                     'status' => $status2,
//                     'learning_tool' => $group->class?->learning_tool,
//                     'no_of_students' => $group->class?->no_of_students,
//                 ];
//             })->values();

//             return response()->json([
//                 'success' => true,
//                 'data' => [
//                     'title' => 'Class History',
//                     'individual_classes' => $individualClasses,
//                     'group_classes' => $groupClasses,
//                     'base_url' => url('/'),
//                 ]
//             ]);
//         }
            
//         } catch (\Exception $e) {
//             dd($e);
//             return response()->json([
//                 'message' => 'Something went wrong while fetching class history.',
//                 'error' => $e->getMessage()
//             ], 400);
//         }
// }






public function classHistoryApi()
{
    try {
        $user = auth()->user();

        if (!$user || !in_array($user->role, [2, 5])) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        $today_date = date('Y-m-d');

        // Shared pagination controls
        $page     = max((int) request()->input('page', 1), 1);
        $perPage  = max((int) request()->input('per_page', 50), 1);
        $offset   = ($page - 1) * $perPage;

        /*
        |--------------------------------------------------------------------------
        | Resolve Actor Context
        |--------------------------------------------------------------------------
        */
        if ($user->role == 2) {
            $actor = Instructor::where('user_id', $user->id)->first();
            if (!$actor) {
                return response()->json(['message' => 'Instructor not found.'], 404);
            }

            $individualBase = ClassDetail::where('instructor_id', $actor->id);
            $groupBase      = ClassDetail::where('instructor_id', $actor->id);
            $timezoneId     = $actor->id;
            $timezoneType   = 'instructor';
        } else {
            $actor = Coordinator::where('user_id', $user->id)->first();
            if (!$actor) {
                return response()->json(['message' => 'Coordinator not found.'], 404);
            }

            $courseIds = CoordinatorCourse::where('coordinator_id', $actor->id)
                ->pluck('course_id')
                ->toArray();

            $individualBase = ClassDetail::whereIn('course_id', $courseIds);
            $groupBase      = ClassDetail::whereIn('course_id', $courseIds);
            $timezoneId     = $actor->id;
            $timezoneType   = 'coordinator';
        }

        /*
        |--------------------------------------------------------------------------
        | INDIVIDUAL CLASSES
        |--------------------------------------------------------------------------
        */
        $individualQuery = $individualBase
            ->where('class_date', '<', $today_date)
            ->whereHas('class', fn ($q) => $q->where('type', 'Individual'))
            ->orderBy('class_date', 'ASC')
            ->orderBy('class_time', 'DESC');

        $individualTotal = $individualQuery->count();

        $individualRows = $individualQuery
            ->skip($offset)
            ->take($perPage)
            ->get();

        $individualClasses = $individualRows->map(function ($row, $index) use ($page, $perPage) {
            return [
                'sr_no' => (($page - 1) * $perPage) + ($index + 1),
                'id' => $row->id,
                'class_id' => $row->class_id,
                'course_id' => $row->course_id,
                'course_title' => $row->course?->title,
                'student_id' => $row->student_id,
                'student_name' => $row->student?->user?->name,
                'instructor_id' => $row->instructor_id,
                'instructor_name' => $row->instructor?->user?->name,
                'class_date_time' => $row->class_date . ' ' . $row->class_time,
                'status' => $row->status,
                'learning_tool' => $row->class?->learning_tool,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | GROUP CLASSES (unique by class_id)
        |--------------------------------------------------------------------------
        */
        $groupCollection = $groupBase
            ->where('class_date', '<', $today_date)
            ->whereHas('class', fn ($q) => $q->where('type', 'Group'))
            ->orderBy('group_id', 'DESC')
            ->get()
            ->unique('class_id')
            ->values();

        $groupTotal = $groupCollection->count();

        $groupSlice = $groupCollection
            ->slice($offset, $perPage)
            ->values();

        $groupClasses = $groupSlice->map(function ($row, $index) use ($page, $perPage) {
            return [
                'sr_no' => (($page - 1) * $perPage) + ($index + 1),
                'id' => $row->id,
                'class_id' => $row->class_id,
                'course_id' => $row->course_id,
                'course_title' => $row->course?->title,
                'class_date_time' => $row->class_date . ' ' . $row->class_time,
                'status' => $row->status,
                'learning_tool' => $row->class?->learning_tool,
                'no_of_students' => $row->class?->no_of_students,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | SHARED PAGINATION META (DRIVEN BY LARGEST DATASET)
        |--------------------------------------------------------------------------
        */
        $overallTotal = max($individualTotal, $groupTotal);
        $lastPage = (int) ceil($overallTotal / $perPage);

        $from = $overallTotal > 0
            ? (($page - 1) * $perPage) + 1
            : null;

        $to = min($page * $perPage, $overallTotal);

        $baseUrl = url()->current();
        $queryParams = request()->except('page');

        $buildUrl = function ($pageNumber) use ($baseUrl, $queryParams) {
            return $pageNumber > 0
                ? $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $pageNumber]))
                : null;
        };

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'success' => true,
            'pagination' => [
                // 'individual_total' => $individualTotal,
                // 'group_total' => $groupTotal,
                'total' => $overallTotal,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $from,
                'to' => $to,
                'links' => [
                    'first' => $buildUrl(1),
                    'last'  => $buildUrl($lastPage),
                    'prev'  => $page > 1 ? $buildUrl($page - 1) : null,
                    'next'  => $page < $lastPage ? $buildUrl($page + 1) : null,
                ],
            ],
            'data' => [
                'title' => 'Class History',

                'individual_classes' => $individualClasses,
                'individual_message' => $individualClasses->isEmpty()
                    ? 'No individual classes found on this page.'
                    : null,

                'group_classes' => $groupClasses,
                'group_message' => $groupClasses->isEmpty()
                    ? 'No group classes found on this page.'
                    : null,
                'base_url' => url('/'),
            ]
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Something went wrong while fetching class history.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function instructorCheckLessonStartTimeBackUp(){
        try {
            $return = array();
            $user = auth()->user();
            if (!$user || $user->role != 2) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }

            $instructor = Instructor::where('user_id', $user->id)->first();

            if (!$instructor) {
                return response()->json(['message' => 'Instructor not found.'], 404);
            }

          
            $existingMeetings = ClassDetail::whereNotNull('meeting_id')->pluck('meeting_id')->toArray();
            $today_date = date("Y-m-d");
            $user_id = $user->id;
            $findInstructor = Instructor::where('user_id',$user_id)->first();
            $instructor_id =  $findInstructor->id;

            $getLessonScheduleOfTutor1 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id',null)
            ->where('class_date',$today_date)->where('meeting_id',null)->get();
         
           

        //Individual
        if(!$getLessonScheduleOfTutor1->isEmpty())
            {
                    foreach ($getLessonScheduleOfTutor1 as $key=> $bokdlsn) 
                        {
                           
                            $DateTime = changetimeByZone($bokdlsn->class_date.' '.date('h:i A',strtotime($bokdlsn->class_time)),$bokdlsn->instructor_id,'instructor');
                            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$bokdlsn->instructor_id,'instructor');
                            $updated_time = date('h:i A',strtotime($DateTime));
                            $lsntime = strtotime($updated_time);
                            $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                            $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                            $curdate = strtotime($curdate);
                            $lsndate = date('Y-m-d',strtotime($DateTime));
                            $lsndate = strtotime($lsndate); 
                            $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                            $curtime = strtotime($updated_cur_time);
                            $lesson_duration = $bokdlsn->class?->duration;
                            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                            $lsnendtime = $lsntimestamp;
                            $lsntimestamp2 = strtotime($updated_time) + ($lesson_duration-1)*60;
                            $lsnendtime2 = $lsntimestamp2 ;

                            
                         
                         
                            if($curdate == $lsndate){      
                              
                                if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->class->learning_tool == 'Zoom')
                                {
                                    if($bokdlsn->group_id == NULL){
                                        $met_id = generateUniqueMeetingId($existingMeetings);
                                        $existingMeetings[] = $met_id;
                                        $data = startzoomclassindividual($bokdlsn->id,$bokdlsn->student_id,$bokdlsn->instructor_id,$met_id,null,$bokdlsn->class->learning_tool);
                                        if($data){
                                            $return['id'] = $data['l_id'];
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

                                            // echo json_encode($return);
                                            return response()->json($return);  
                                        
                                        }
                                        else{
                                            // echo json_encode("Some Issue While Creating Zoom Individual Class. Try again");
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
                                            $return['id'] = $data['l_id'];
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
                                        
                                            // echo json_encode($return);
                                            return response()->json($return);  
                                        }
                                        else{
                                            // echo json_encode("Some Issue While Creating BBB Individual Class. Try again");
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
                                                    $return['id'] = $data['l_id'];
                                                    $return['learning_tool'] = 'Google_Meet';
                                                    $return['is_missed'] = 'No';
                                                    $return['url'] = $data['google_meet_link'];
                                                    $return['is_group'] = $data['is_group'];
                                                    $return['meeting_id'] = $data['meet_meeting_id'];
                                                    $return['user_name'] = $data['tutor_name'];
                                                    $return['user_id'] = $data['tutor_id'];
                                                    $return['user_type'] = $data['tutor_type'];
                                                    
                                                    $meetingID = $data['meet_meeting_id'];
                                                    $lesson_id = $data['l_id'];
                                                    // echo json_encode($return); 
                                                    return response()->json($return);  
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
                                                $return['id'] = $data['l_id'];
                                                $return['learning_tool'] = 'Google_Meet';
                                                $return['is_missed'] = 'No';
                                                $return['url'] = $data['google_meet_link'];
                                                $return['is_group'] = $data['is_group'];
                                                $return['meeting_id'] = $data['meet_meeting_id'];
                                                $return['user_name'] = $data['tutor_name'];
                                                $return['user_id'] = $data['tutor_id'];
                                                $return['user_type'] = $data['tutor_type'];
                                            
                                                $meetingID = $data['meet_meeting_id'];
                                                $lesson_id = $data['l_id'];
                                                // echo json_encode($return); 
                                                return response()->json($return);   
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
                                    // return response()->json($return);
                                }
                            }
                            else{
                                // return response()->json($return);
                            }
                        }
            }  
            else{
                    // return response()->json($return);
            }
             
                
              //Group
                $getLessonScheduleOfTutor2 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id','<>',null)
                ->where('class_date',$today_date)->where('meeting_id',null)->groupBy('group_id')->orderBy('class_id','ASC')
                ->orderBy('class_date','ASC')->get();
             
                if(!$getLessonScheduleOfTutor2->isEmpty())
                {
                   
                    $group_id = NULL;
                    foreach ($getLessonScheduleOfTutor2 as $key=> $bokdlsn2) 
                    {

                        $DateTime = changetimeByZone($bokdlsn2->class_date.' '.date('h:i A',strtotime($bokdlsn2->class_time)),$bokdlsn2->instructor_id,'instructor');
                        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$bokdlsn2->instructor_id,'instructor');
                        $updated_time = date('h:i A',strtotime($DateTime));
                        $lsntime = strtotime($updated_time);
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                        $curdate = strtotime($curdate);
                        $lsndate = date('Y-m-d',strtotime($DateTime));
                        $lsndate = strtotime($lsndate); 
                        $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                        $curtime = strtotime($updated_cur_time);
                        $lesson_duration = $bokdlsn2->class?->duration;
                        $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp;
                        $lsntimestamp2 = strtotime($updated_time) + ($lesson_duration-1)*60;
                        $lsnendtime2 = $lsntimestamp2 ;


                   
                     
                        if($curdate == $lsndate){
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->meeting_id == NULL && $bokdlsn2->class->learning_tool == 'Zoom')
                            {
                                $met_id = generateUniqueMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                $data = startzoomclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id,$bokdlsn2->group_id,$bokdlsn2->class->learning_tool);
                                if($data){
                                    $return['id'] = $data['l_id'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['group_id'] = $data['group_id'];
                                    $return['learning_tool'] = 'Zoom';
                                
                                    $return['is_missed'] = 'No';
                                    $return['url'] = $data['TutorURI'];
                                
                                    $return['meeting_id'] = $data['zoom_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['zoom_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    // echo json_encode($return);
                                    return response()->json($return);  
                                }
                                else{
                                    // return response()->json($return);  
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->InstructorURI == NULL && $bokdlsn2->class->learning_tool == 'BBB')
                            {    
                                $met_id = generateUniqueBBBMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                
                                $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $bokdlsn2->class?->duration);
                                $data = startbbbclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id, $this->bb,$bokdlsn2->group_id,$bokdlsn2->class?->learning_tool,$meeting);

                                if($data){
                                    $return['id'] = $data['l_id'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['group_id'] = $data['group_id'];
                                    $return['learning_tool'] = 'BBB';
                                    $return['is_missed'] = 'No';
                                    $return['url'] = $data['TutorURI'];
                                    $return['meeting_id'] = $data['zoom_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['zoom_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    // echo json_encode($return);
                                    return response()->json($return);  
                                }
                                else{
                                    // return response()->json($return);  
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->meeting_id == NULL && $bokdlsn2->class->learning_tool == 'Google_Meet')
                            {
                                
                                $met_id = generateUniqueMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                $data = startmeetclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id,$bokdlsn2->group_id,$bokdlsn2->class->learning_tool);
                              
                                if($data){
                                    $return['l_id'] = $data['l_id'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['group_id'] = $data['group_id'];
                                    $return['learning_tool'] = 'Google_Meet';
                                  
                                    $return['is_missed'] = 'No';
                                   
                                    $return['url'] = $data['google_meet_link'];
                                 
                                    $return['meeting_id'] = $data['meet_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['meet_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    // echo json_encode($return);
                                    return response()->json($return);  
                                }
                                else{
                                    // return response()->json($return);  
                                }

                            }
                            else if($curtime >= $lsnendtime){
                                // if($bokdlsn2->is_instructor_joined == '1' || $bokdlsn2->is_instructor_joined == "1" || $bokdlsn2->is_instructor_joined == 1 || $bokdlsn2->is_instructor_joined){
                                //     $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->get();
                                //     foreach ($results as $key => $value) {
                                //         $value->status = "Completed";
                                //         $response = $value->update();
                                //     }
                                    
                                // }
                                // else{
                                //     $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->get();
                                //     foreach ($results as $key => $value) {
                                //         $value->status = "Missed";
                                //         $response = $value->update();
                                //     }
                                // }
                            }
                            else{
                                // return response()->json($return);  
                            }
                        }
                        else{
                            // return response()->json($return);  
                        }
                    }
                }
                else{
                    // return response()->json($return);
                }

            return response()->json($return);
        
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
}
    
    public function instructorCheckLessonStartTime(){
        try {
            $return = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 2 && $role != 5){ // Instructor
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 403);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 403);
            }

          
        if($user->role == 2){
            $instructor = Instructor::where('user_id', $user->id)->first();

            if (!$instructor) {
                return response()->json(['message' => 'Instructor not found.'], 404);
            }

          
            $existingMeetings = ClassDetail::whereNotNull('meeting_id')->pluck('meeting_id')->toArray();
            $today_date = date("Y-m-d");
            $user_id = $user->id;
            $findInstructor = Instructor::where('user_id',$user_id)->first();
            $instructor_id =  $findInstructor->id;

            $getLessonScheduleOfTutor1 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id',null)
            ->where('class_date',$today_date)->where('meeting_id',null)->get();
         
           

            //Individual
            if(!$getLessonScheduleOfTutor1->isEmpty())
            {
                    foreach ($getLessonScheduleOfTutor1 as $key=> $bokdlsn) 
                        {
                           
                            $DateTime = changetimeByZone($bokdlsn->class_date.' '.date('h:i A',strtotime($bokdlsn->class_time)),$bokdlsn->instructor_id,'instructor');
                            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$bokdlsn->instructor_id,'instructor');
                            $updated_time = date('h:i A',strtotime($DateTime));
                            $lsntime = strtotime($updated_time);
                            $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                            $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                            $curdate = strtotime($curdate);
                            $lsndate = date('Y-m-d',strtotime($DateTime));
                            $lsndate = strtotime($lsndate); 
                            $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                            $curtime = strtotime($updated_cur_time);
                            $lesson_duration = $bokdlsn->class?->duration;
                            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                            $lsnendtime = $lsntimestamp;
                            $lsntimestamp2 = strtotime($updated_time) + ($lesson_duration-1)*60;
                            $lsnendtime2 = $lsntimestamp2 ;

                            
                         
                         
                            if($curdate == $lsndate){      
                              
                                if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->class->learning_tool == 'Zoom')
                                {
                                    if($bokdlsn->group_id == NULL){
                                        $met_id = generateUniqueMeetingId($existingMeetings);
                                        $existingMeetings[] = $met_id;
                                        $data = startzoomclassindividual($bokdlsn->id,$bokdlsn->student_id,$bokdlsn->instructor_id,$met_id,null,$bokdlsn->class->learning_tool);
                                        if($data){
                                            $return['id'] = $data['l_id'];
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

                                            // echo json_encode($return);
                                            return response()->json($return);  
                                        
                                        }
                                        else{
                                            // echo json_encode("Some Issue While Creating Zoom Individual Class. Try again");
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
                                            $return['id'] = $data['l_id'];
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
                                        
                                            // echo json_encode($return);
                                            return response()->json($return);  
                                        }
                                        else{
                                            // echo json_encode("Some Issue While Creating BBB Individual Class. Try again");
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
                                                    $return['id'] = $data['l_id'];
                                                    $return['learning_tool'] = 'Google_Meet';
                                                    $return['is_missed'] = 'No';
                                                    $return['url'] = $data['google_meet_link'];
                                                    $return['is_group'] = $data['is_group'];
                                                    $return['meeting_id'] = $data['meet_meeting_id'];
                                                    $return['user_name'] = $data['tutor_name'];
                                                    $return['user_id'] = $data['tutor_id'];
                                                    $return['user_type'] = $data['tutor_type'];
                                                    
                                                    $meetingID = $data['meet_meeting_id'];
                                                    $lesson_id = $data['l_id'];
                                                    // echo json_encode($return); 
                                                    return response()->json($return);  
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
                                                $return['id'] = $data['l_id'];
                                                $return['learning_tool'] = 'Google_Meet';
                                                $return['is_missed'] = 'No';
                                                $return['url'] = $data['google_meet_link'];
                                                $return['is_group'] = $data['is_group'];
                                                $return['meeting_id'] = $data['meet_meeting_id'];
                                                $return['user_name'] = $data['tutor_name'];
                                                $return['user_id'] = $data['tutor_id'];
                                                $return['user_type'] = $data['tutor_type'];
                                            
                                                $meetingID = $data['meet_meeting_id'];
                                                $lesson_id = $data['l_id'];
                                                // echo json_encode($return); 
                                                return response()->json($return);   
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
                                    // return response()->json($return);
                                }
                            }
                            else{
                                // return response()->json($return);
                            }
                        }
            }  
            else{
                    // return response()->json($return);
            }
             
                
              //Group
                $getLessonScheduleOfTutor2 = ClassDetail::where('instructor_id',$instructor_id)->where('group_id','<>',null)
                ->where('class_date',$today_date)->where('meeting_id',null)->groupBy('group_id')->orderBy('class_id','ASC')
                ->orderBy('class_date','ASC')->get();
             
                if(!$getLessonScheduleOfTutor2->isEmpty())
                {
                   
                    $group_id = NULL;
                    foreach ($getLessonScheduleOfTutor2 as $key=> $bokdlsn2) 
                    {

                        $DateTime = changetimeByZone($bokdlsn2->class_date.' '.date('h:i A',strtotime($bokdlsn2->class_time)),$bokdlsn2->instructor_id,'instructor');
                        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$bokdlsn2->instructor_id,'instructor');
                        $updated_time = date('h:i A',strtotime($DateTime));
                        $lsntime = strtotime($updated_time);
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                        $curdate = strtotime($curdate);
                        $lsndate = date('Y-m-d',strtotime($DateTime));
                        $lsndate = strtotime($lsndate); 
                        $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                        $curtime = strtotime($updated_cur_time);
                        $lesson_duration = $bokdlsn2->class?->duration;
                        $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp;
                        $lsntimestamp2 = strtotime($updated_time) + ($lesson_duration-1)*60;
                        $lsnendtime2 = $lsntimestamp2 ;


                   
                     
                        if($curdate == $lsndate){
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->meeting_id == NULL && $bokdlsn2->class->learning_tool == 'Zoom')
                            {
                                $met_id = generateUniqueMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                $data = startzoomclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id,$bokdlsn2->group_id,$bokdlsn2->class->learning_tool);
                                if($data){
                                    $return['id'] = $data['l_id'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['group_id'] = $data['group_id'];
                                    $return['learning_tool'] = 'Zoom';
                                
                                    $return['is_missed'] = 'No';
                                    $return['url'] = $data['TutorURI'];
                                
                                    $return['meeting_id'] = $data['zoom_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['zoom_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    // echo json_encode($return);
                                    return response()->json($return);  
                                }
                                else{
                                    // return response()->json($return);  
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->InstructorURI == NULL && $bokdlsn2->class->learning_tool == 'BBB')
                            {    
                                $met_id = generateUniqueBBBMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                
                                $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $bokdlsn2->class?->duration);
                                $data = startbbbclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id, $this->bb,$bokdlsn2->group_id,$bokdlsn2->class?->learning_tool,$meeting);

                                if($data){
                                    $return['id'] = $data['l_id'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['group_id'] = $data['group_id'];
                                    $return['learning_tool'] = 'BBB';
                                    $return['is_missed'] = 'No';
                                    $return['url'] = $data['TutorURI'];
                                    $return['meeting_id'] = $data['zoom_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['zoom_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    // echo json_encode($return);
                                    return response()->json($return);  
                                }
                                else{
                                    // return response()->json($return);  
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->meeting_id == NULL && $bokdlsn2->class->learning_tool == 'Google_Meet')
                            {
                                
                                $met_id = generateUniqueMeetingId($existingMeetings);
                                $existingMeetings[] = $met_id;
                                $data = startmeetclassgroup($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$met_id,$bokdlsn2->group_id,$bokdlsn2->class->learning_tool);
                              
                                if($data){
                                    $return['l_id'] = $data['l_id'];
                                    $return['is_group'] = $data['is_group'];
                                    $return['group_id'] = $data['group_id'];
                                    $return['learning_tool'] = 'Google_Meet';
                                  
                                    $return['is_missed'] = 'No';
                                   
                                    $return['url'] = $data['google_meet_link'];
                                 
                                    $return['meeting_id'] = $data['meet_meeting_id'];
                                    $return['user_name'] = $data['tutor_name'];
                                    $return['user_id'] = $data['tutor_id'];
                                    $return['user_type'] = $data['tutor_type'];
                                
                                    $meetingID = $data['meet_meeting_id'];
                                    $lesson_id = $data['l_id'];
                            
                                    // echo json_encode($return);
                                    return response()->json($return);  
                                }
                                else{
                                    // return response()->json($return);  
                                }

                            }
                            else if($curtime >= $lsnendtime){
                                // if($bokdlsn2->is_instructor_joined == '1' || $bokdlsn2->is_instructor_joined == "1" || $bokdlsn2->is_instructor_joined == 1 || $bokdlsn2->is_instructor_joined){
                                //     $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->get();
                                //     foreach ($results as $key => $value) {
                                //         $value->status = "Completed";
                                //         $response = $value->update();
                                //     }
                                    
                                // }
                                // else{
                                //     $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->get();
                                //     foreach ($results as $key => $value) {
                                //         $value->status = "Missed";
                                //         $response = $value->update();
                                //     }
                                // }
                            }
                            else{
                                // return response()->json($return);  
                            }
                        }
                        else{
                            // return response()->json($return);  
                        }
                    }
                }
                else{
                    // return response()->json($return);
                }

            return response()->json($return);
        }
        else{
            $today_date = date("Y-m-d");
            $user_id = $user->id;
            $findCoordinator = Coordinator::where('user_id',$user_id)->first();
            $coordinator_id =  $findCoordinator->id;

            $coordinatorCourseIds = CoordinatorCourse::where('coordinator_id', $coordinator_id)
                ->pluck('course_id')
                ->toArray();

            $getLessonScheduleOfCoordinator = ClassDetail::whereIn('course_id',$coordinatorCourseIds)->where('class_date','>=',$today_date)
            ->orderBy('class_date','ASC')->get();
         
            if(!$getLessonScheduleOfCoordinator->isEmpty())
                {
                    foreach ($getLessonScheduleOfCoordinator as $key=> $bokdlsn) 
                        {
                           
                            $DateTime = changetimeByZone($bokdlsn->class_date.' '.date('h:i A',strtotime($bokdlsn->class_time)),$coordinator_id,'coordinator');
                            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
                            $updated_time = date('h:i A',strtotime($DateTime));
                            $lsntime = strtotime($updated_time);
                            $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                            $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                            $curdate = strtotime($curdate);
                            $lsndate = date('Y-m-d',strtotime($DateTime));
                            $lsndate = strtotime($lsndate); 
                            $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                            $curtime = strtotime($updated_cur_time);
                            $lesson_duration = $bokdlsn->class->duration;
                            $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                            $lsnendtime = $lsntimestamp;
    
                         
                            if($curdate == $lsndate){      
                              
                                if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id != NULL && $bokdlsn->class->learning_tool == 'Zoom')
                                {
                                
                                    $meetingID = $bokdlsn->meeting_id;
                                    $lesson_id = $bokdlsn->id;
                                    $return['id'] = $bokdlsn->id;
                                    $return['learning_tool'] = $bokdlsn->class->learning_tool;
                                    $return['url'] = null;
                                    $return['meeting_id'] = $bokdlsn->meeting_id;
                                    $return['user_name'] = getCoordinatorNameFromID($coordinator_id);
                                    $return['user_id'] = $coordinator_id;
                                    $return['user_type'] = 'Coordinator';
                                    return response()->json($return);
      
                                }
                                else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->AcademicURI != NULL && $bokdlsn->class->learning_tool == 'BBB')
                                {
                                    $meetingID = $bokdlsn->meeting_id;
                                    $lesson_id = $bokdlsn->id;
                                    $return['id'] = $bokdlsn->id;
                                    $return['learning_tool'] = $bokdlsn->class->learning_tool;
                                    $return['url'] = $bokdlsn->AcademicURI;
                                    $return['meeting_id'] = $bokdlsn->meeting_id;
                                    $return['user_name'] = getCoordinatorNameFromID($coordinator_id);
                                    $return['user_id'] = $coordinator_id;
                                    $return['user_type'] = 'Coordinator';
                                    return response()->json($return);
                                }
                                else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->google_meet_link != NULL && $bokdlsn->class->learning_tool == 'Google_Meet')
                                {
                                    $meetingID = $bokdlsn->meeting_id;
                                    $lesson_id = $bokdlsn->id;
                                    $return['id'] = $bokdlsn->id;
                                    $return['learning_tool'] = $bokdlsn->class->learning_tool;
                                    $return['url'] = $bokdlsn->google_meet_link;
                                    $return['meeting_id'] = $bokdlsn->meeting_id;
                                    $return['user_name'] = getCoordinatorNameFromID($coordinator_id);
                                    $return['user_id'] = $coordinator_id;
                                    $return['user_type'] = 'Coordinator';
                                    return response()->json($return);
                                }
                                else{
                                }
                            }
                            else{
                            }
                        }
                }  
                else{
                } 
                return response()->json($return);
        }
            
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }





    // public function instructrCheckLessonStartTimeLiveClassBackUp(){
    //     try {
    //         $return = array();
    //         $user = auth()->user();
    //         if (!$user || $user->role != 2) {
    //             return response()->json(['message' => 'Unauthorized access.'], 403);
    //         }
    
    //         $instructor = Instructor::where('user_id', $user->id)->first();
    
    //         if (!$instructor) {
    //             return response()->json(['message' => 'Instructor not found.'], 404);
    //         }
    
    //         $existingMeetings = LiveClass::whereNotNull('meeting_id')->pluck('meeting_id')->toArray();
    //       $today_date = date("Y-m-d");
    //       $user_id = Auth::id();
    //       $findInstructor = Instructor::where('user_id',$user_id)->first();
    //       $instructor_id =  $findInstructor->id;
    //       $getLessonScheduleOfTutor = LiveClass::where('user_id',$user_id)->where('date','>=',$today_date)
    //       ->where('meeting_id',null)->get();
       
    //       if(!$getLessonScheduleOfTutor->isEmpty())
    //       {
    //               foreach ($getLessonScheduleOfTutor as $key=> $bokdlsn) 
    //                   {

    //                     $DateTime = changetimeByZone($bokdlsn->date.' '.date('h:i A',strtotime($bokdlsn->time)),$instructor_id,'instructor');
    //                     $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$instructor_id,'instructor');
    //                     $updated_time = date('h:i A',strtotime($DateTime));
    //                     $lsntime = strtotime($updated_time);
    //                     $classTime = date('d-m-Y h:i A',strtotime($DateTime));
    //                     $curdate = date("Y-m-d",strtotime($CurrentDateTime));
    //                     $curdate = strtotime($curdate);
    //                     $lsndate = date('Y-m-d',strtotime($DateTime));
    //                     $lsndate = strtotime($lsndate); 
    //                     $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
    //                     $curtime = strtotime($updated_cur_time);
    //                     $lesson_duration = $bokdlsn->duration;
    //                     $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
    //                     $lsnendtime = $lsntimestamp;
    //                     $lsntimestamp2 = strtotime($updated_time) + ($lesson_duration-1)*60;
    //                     $lsnendtime2 = $lsntimestamp2 ;


                       
    //                       if($curdate == $lsndate){
                           
    //                           if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->meeting_host_name == 'zoom')
    //                           {
    //                                   $met_id = generateUniqueMeetingIdLiveClass($existingMeetings);
    //                                   $existingMeetings[] = $met_id;
                                  
    //                                   $data = startzoomliveclass($bokdlsn->id,null,$instructor_id,$met_id,null,$bokdlsn->meeting_host_name);
    //                                   if($data){
    //                                       $return['id'] = $data['l_id'];
    //                                       $return['learning_tool'] = 'Zoom';
                                      
    //                                       $return['url'] = $data['TutorURI'];
                                       
    //                                       $return['meeting_id'] = $data['zoom_meeting_id'];
    //                                       $return['user_name'] = $data['tutor_name'];
    //                                       $return['user_id'] = $data['tutor_id'];
    //                                       $return['user_type'] = $data['tutor_type'];
                                      
    //                                       $meetingID = $data['zoom_meeting_id'];
    //                                       $lesson_id = $data['l_id'];
    
    //                                     //   echo json_encode($return);
    //                                       response()->json($return);
                                      
    //                                   }
    //                                   else{     
    //                                     //   response()->json($return);
    //                                   }
    //                           }
    //                           if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->meeting_host_name == 'google_meet')
    //                           {
    //                                   $met_id = generateUniqueMeetingIdLiveClass($existingMeetings);
    //                                   $existingMeetings[] = $met_id;
                                  
    //                                   $data = startmeetliveclass($bokdlsn->id,null,$instructor_id,$met_id,null,$bokdlsn->meeting_host_name);
    //                                   if($data){
    //                                       $return['id'] = $data['l_id'];
    //                                       $return['learning_tool'] = 'Google_Meet';
                                      
    //                                       $return['url'] = $data['google_meet_link'];
                                      
    //                                       $return['meeting_id'] = $data['meet_meeting_id'];
    //                                       $return['user_name'] = $data['tutor_name'];
    //                                       $return['user_id'] = $data['tutor_id'];
    //                                       $return['user_type'] = $data['tutor_type'];
                                      
    //                                       $meetingID = $data['meet_meeting_id'];
    //                                       $lesson_id = $data['l_id'];
    
    //                                     //   echo json_encode($return);
    //                                       return response()->json($return);    
                                      
    //                                   }
    //                                   else{ 
    //                                     //  return response()->json($return);    
    //                                   }
    //                           }
    //                           else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->InstructorURI == NULL && $bokdlsn->meeting_host_name == 'bbb')
    //                           {
                                   
    //                                   $met_id = generateUniqueBBBMeetingIdLiveClass($existingMeetings);
    //                                   $existingMeetings[] = $met_id;
    //                                   $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $bokdlsn->duration);
    //                                   $data = startbbbliveclass($bokdlsn->id,null,$instructor_id,$met_id, $this->bb,null,$bokdlsn->meeting_host_name,$meeting);
                                  
    //                                   if($data){
    //                                       $return['id'] = $data['l_id'];
    //                                       $return['learning_tool'] = 'BBB';
                                      
    //                                       $return['url'] = $data['TutorURI'];
                                     
    //                                       $return['meeting_id'] = $data['bbb_meeting_id'];
    //                                       $return['user_name'] = $data['tutor_name'];
    //                                       $return['user_id'] = $data['tutor_id'];
    //                                       $return['user_type'] = $data['tutor_type'];
                                      
    //                                       $meetingID = $data['bbb_meeting_id'];
    //                                       $lesson_id = $data['l_id'];
                                  
    //                                     //   echo json_encode($return);
    //                                       return response()->json($return);    
    //                                   }
    //                                   else{
    //                                     //  return response()->json($return);
    //                                   }
                                  
    //                           }
                          
    //                           else if($curtime >= $lsnendtime){
                           
    //                             //   if($bokdlsn->is_instructor_joined == '1' || $bokdlsn->is_instructor_joined == "1" || $bokdlsn->is_instructor_joined == 1 || $bokdlsn->is_instructor_joined){
    //                             //       $results = LiveClass::where('id',$bokdlsn->id)->first();
    //                             //       $results->status = "Completed";
    //                             //       $response = $results->update();
    //                             //   }
    //                             //   else{
    //                             //       $results = LiveClass::where('id',$bokdlsn->id)->first();
    //                             //       $results->status = "Missed";
    //                             //       $response = $results->update();
    //                             //   }
    //                           }
    //                           else{
    //                                 // return response()->json($return);
    //                           }
                          
    //                       }
    //                   }
    //       }
    //       else{
    //         // return response()->json($return);
    //       }
    //       return response()->json($return);
    //     } catch (\Throwable $th) {
    //         return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    //     }
     
    // } 



    public function instructrCheckLessonStartTimeLiveClass(){
        try {
            $return = array();
            $user = auth()->user();
            if (!$user || ($user->role != 2 && $user->role != 5)) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }
    
    if($user->role == 2){
        $instructor = Instructor::where('user_id', $user->id)->first();
  
          if (!$instructor) {
              return response()->json(['message' => 'Instructor not found.'], 404);
          }
  
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

                        $DateTime = changetimeByZone($bokdlsn->date.' '.date('h:i A',strtotime($bokdlsn->time)),$instructor_id,'instructor');
                        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$instructor_id,'instructor');
                        $updated_time = date('h:i A',strtotime($DateTime));
                        $lsntime = strtotime($updated_time);
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                        $curdate = strtotime($curdate);
                        $lsndate = date('Y-m-d',strtotime($DateTime));
                        $lsndate = strtotime($lsndate); 
                        $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                        $curtime = strtotime($updated_cur_time);
                        $lesson_duration = $bokdlsn->duration;
                        $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp;
                        $lsntimestamp2 = strtotime($updated_time) + ($lesson_duration-1)*60;
                        $lsnendtime2 = $lsntimestamp2 ;


                       
                          if($curdate == $lsndate){
                           
                              if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->meeting_host_name == 'zoom')
                              {
                                      $met_id = generateUniqueMeetingIdLiveClass($existingMeetings);
                                      $existingMeetings[] = $met_id;
                                  
                                      $data = startzoomliveclass($bokdlsn->id,null,$instructor_id,$met_id,null,$bokdlsn->meeting_host_name);
                                      if($data){
                                          $return['id'] = $data['l_id'];
                                          $return['learning_tool'] = 'Zoom';
                                      
                                          $return['url'] = $data['TutorURI'];
                                       
                                          $return['meeting_id'] = $data['zoom_meeting_id'];
                                          $return['user_name'] = $data['tutor_name'];
                                          $return['user_id'] = $data['tutor_id'];
                                          $return['user_type'] = $data['tutor_type'];
                                      
                                          $meetingID = $data['zoom_meeting_id'];
                                          $lesson_id = $data['l_id'];
    
                                        //   echo json_encode($return);
                                          response()->json($return);
                                      
                                      }
                                      else{     
                                        //   response()->json($return);
                                      }
                              }
                              if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id == NULL && $bokdlsn->meeting_host_name == 'google_meet')
                              {
                                      $met_id = generateUniqueMeetingIdLiveClass($existingMeetings);
                                      $existingMeetings[] = $met_id;
                                  
                                      $data = startmeetliveclass($bokdlsn->id,null,$instructor_id,$met_id,null,$bokdlsn->meeting_host_name);
                                      if($data){
                                          $return['id'] = $data['l_id'];
                                          $return['learning_tool'] = 'Google_Meet';
                                      
                                          $return['url'] = $data['google_meet_link'];
                                      
                                          $return['meeting_id'] = $data['meet_meeting_id'];
                                          $return['user_name'] = $data['tutor_name'];
                                          $return['user_id'] = $data['tutor_id'];
                                          $return['user_type'] = $data['tutor_type'];
                                      
                                          $meetingID = $data['meet_meeting_id'];
                                          $lesson_id = $data['l_id'];
    
                                        //   echo json_encode($return);
                                          return response()->json($return);    
                                      
                                      }
                                      else{ 
                                        //  return response()->json($return);    
                                      }
                              }
                              else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->InstructorURI == NULL && $bokdlsn->meeting_host_name == 'bbb')
                              {
                                   
                                      $met_id = generateUniqueBBBMeetingIdLiveClass($existingMeetings);
                                      $existingMeetings[] = $met_id;
                                      $meeting = $this->bb->create_meeting($met_id,"Learn Quran with Technology", $bokdlsn->duration);
                                      $data = startbbbliveclass($bokdlsn->id,null,$instructor_id,$met_id, $this->bb,null,$bokdlsn->meeting_host_name,$meeting);
                                  
                                      if($data){
                                          $return['id'] = $data['l_id'];
                                          $return['learning_tool'] = 'BBB';
                                      
                                          $return['url'] = $data['TutorURI'];
                                     
                                          $return['meeting_id'] = $data['bbb_meeting_id'];
                                          $return['user_name'] = $data['tutor_name'];
                                          $return['user_id'] = $data['tutor_id'];
                                          $return['user_type'] = $data['tutor_type'];
                                      
                                          $meetingID = $data['bbb_meeting_id'];
                                          $lesson_id = $data['l_id'];
                                  
                                        //   echo json_encode($return);
                                          return response()->json($return);    
                                      }
                                      else{
                                        //  return response()->json($return);
                                      }
                                  
                              }
                          
                              else if($curtime >= $lsnendtime){
                           
                                //   if($bokdlsn->is_instructor_joined == '1' || $bokdlsn->is_instructor_joined == "1" || $bokdlsn->is_instructor_joined == 1 || $bokdlsn->is_instructor_joined){
                                //       $results = LiveClass::where('id',$bokdlsn->id)->first();
                                //       $results->status = "Completed";
                                //       $response = $results->update();
                                //   }
                                //   else{
                                //       $results = LiveClass::where('id',$bokdlsn->id)->first();
                                //       $results->status = "Missed";
                                //       $response = $results->update();
                                //   }
                              }
                              else{
                                    // return response()->json($return);
                              }
                          
                          }
                      }
          }
          else{
          }
          return response()->json($return);
    }
    else{
        $today_date = date("Y-m-d");
        $user_id = $user->id;
        $findCoordinator = Coordinator::where('user_id',$user_id)->first();
        $coordinator_id =  $findCoordinator->id;
        // Courses handled by coordinator
        $coordinatorCourseIds = CoordinatorCourse::where('coordinator_id', $coordinator_id)
            ->pluck('course_id')
            ->toArray();

        $getLiveClassesCoordinator = LiveClassStudentLink::whereIn('course_id',$coordinatorCourseIds)->get();
        $liveClassIds = array();
        if(!$getLiveClassesCoordinator->isEmpty()){
            foreach ($getLiveClassesCoordinator as $key => $liveClassCoordinator) {
                $liveClassIds[] = $liveClassCoordinator->live_class_id;
            }
            $liveClassIds = array_unique($liveClassIds);
        }
        // $getLessonScheduleOfStudent = LiveClass::whereIn('id',$liveClassIds)->where('date','>=',$today_date)->get();
        $getLessonScheduleOfCoordinator = LiveClass::whereIn('id',$liveClassIds)->where('date','>=',$today_date)
        ->where('meeting_id','<>',null)->get();

        if(!$getLessonScheduleOfCoordinator->isEmpty())
            {
                foreach ($getLessonScheduleOfCoordinator as $key=> $bokdlsn) 
                    {
                        $findInstructor = Instructor::where('user_id',$bokdlsn->user_id)->first();
                        $instructor_id =  $findInstructor->id;
                        // $AcademicURI = getStudentURI($bokdlsn->id,$coordinator_id);
                        // $timezone = 'Asia/Karachi';
                        $DateTime = changetimeByZone($bokdlsn->date.' '.date('h:i A',strtotime($bokdlsn->time)),$coordinator_id,'coordinator');
                        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$coordinator_id,'coordinator');
                        $updated_time = date('h:i A',strtotime($DateTime));
                        $lsntime = strtotime($updated_time);
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                        $curdate = strtotime($curdate);
                        $lsndate = date('Y-m-d',strtotime($DateTime));
                        $lsndate = strtotime($lsndate); 
                        $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                        $curtime = strtotime($updated_cur_time);
                        $lesson_duration = $bokdlsn->duration;
                        $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp;    
                        if($curdate == $lsndate){
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id != NULL && $bokdlsn->meeting_host_name == 'zoom')
                            {

                                $meetingID = $bokdlsn->meeting_id;
                                $lesson_id = $bokdlsn->id;
                                $return['id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                $return['url'] = null;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getCoordinatorNameFromID($coordinator_id);
                                $return['user_id'] = $coordinator_id;
                                $return['user_type'] = 'Coordinator';
                            
                                return response()->json($return);

                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $StudentURI != NULL && $bokdlsn->meeting_host_name == 'bbb')
                            {
                                $meetingID = $bokdlsn->meeting_id;
                                $lesson_id = $bokdlsn->id;
                                $return['id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                $return['url'] = $bokdlsn->AcademicURI;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getCoordinatorNameFromID($coordinator_id);
                                $return['user_id'] = $coordinator_id;
                                $return['user_type'] = 'Coordinator';
                              
                                return response()->json($return);
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->google_meet_link != NULL && $bokdlsn->meeting_host_name == 'google_meet')
                            {
                                $meetingID = $bokdlsn->meeting_id;
                                $lesson_id = $bokdlsn->id;
                                $return['id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                $return['url'] = $bokdlsn->AcademicURI;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getCoordinatorNameFromID($coordinator_id);
                                $return['user_id'] = $coordinator_id;
                                $return['user_type'] = 'Coordinator';
                             
                                return response()->json($return);
                            }
                            else{
                            }
                        }
                    }
            }     
            else{
            }
            return response()->json($return);
    }
          
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
     
    } 


    // public function discussionIndexBackUp(Request $request)
    // {
    //     $user = Auth::user();
    //     if($user){
    //         $role = $user->role;
    //         if($role != 2){ // Instructor
    //             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
    //         }
    //     }
    //     else{
    //         return response()->json(['message' => 'User Is Not Logged In'], 302);
    //     }
    //     $searchTitle = $request->input('search_title');

    //     $courseIds = Discussion::leftJoin('course_instructor', function ($join) {
    //         $join->on('discussions.course_id', '=', 'course_instructor.course_id')
    //             ->where('course_instructor.status', '=', 1);
    //     })
    //         ->join('courses', 'courses.id', '=', 'discussions.course_id')
    //         ->where(function ($q) {
    //             $q->where('courses.user_id', auth()->id())
    //                 ->orWhere('course_instructor.instructor_id', auth()->id());
    //         })
    //         ->whereNull('parent_id')
    //         ->where('discussions.status', 1)
    //         ->NotView()
    //         ->select('discussions.course_id')
    //         ->groupBy('discussions.course_id')
    //         ->pluck('course_id')
    //         ->toArray();

    //     $courses = Course::whereIn('id', $courseIds)
    //         ->when($searchTitle, function ($q) use ($searchTitle) {
    //             $q->where('title', 'LIKE', "%{$searchTitle}%");
    //         })
    //         ->select('id', 'title', 'image', 'user_id', 'uuid')
    //         ->get();
    //     $result_array = array();
    //     if(!$courses->isEmpty()){
    //         foreach ($courses as $key => $course) {
    //              $discussionCount =Discussion::where('course_id',$course->id)
    //              ->whereNull('parent_id')
    //               ->NotView()
    //               ->where('status', 1)->count();
    //             $result_array[] = [
    //                 'id' => $course->id,
    //                 'title' => $course->title,
    //                 'image' => asset($course->image),
    //                 'user_id' => $course->user_id,
    //                 'uuid' => $course->uuid,
    //                 'image_url' => $course->image_url,
    //                 'discussions_count' => $discussionCount,
    //             ];
    //         }
    //     }
    //     return response()->json([
    //         'success' => true,
    //         'courses' => $result_array
    //     ]);
    // }





      public function discussionIndex(Request $request)
    {
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        if($user->role == 2){
        $searchTitle = $request->input('search_title');

        $courseIds = Discussion::leftJoin('course_instructor', function ($join) {
            $join->on('discussions.course_id', '=', 'course_instructor.course_id')
                ->where('course_instructor.status', '=', 1);
        })
            ->join('courses', 'courses.id', '=', 'discussions.course_id')
            ->where(function ($q) {
                $q->where('courses.user_id', auth()->id())
                    ->orWhere('course_instructor.instructor_id', auth()->id());
            })
            ->whereNull('parent_id')
            ->where('discussions.status', 1)
            ->NotView()
            ->select('discussions.course_id')
            ->groupBy('discussions.course_id')
            ->pluck('course_id')
            ->toArray();

        $courses = Course::whereIn('id', $courseIds)
            ->when($searchTitle, function ($q) use ($searchTitle) {
                $q->where('title', 'LIKE', "%{$searchTitle}%");
            })
            ->select('id', 'title', 'image', 'user_id', 'uuid')
            ->get();
        $result_array = array();
        if(!$courses->isEmpty()){
            foreach ($courses as $key => $course) {
                 $discussionCount =Discussion::where('course_id',$course->id)
                 ->whereNull('parent_id')
                  ->NotView()
                  ->where('status', 1)->count();
                $result_array[] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'image' => asset($course->image),
                    'user_id' => $course->user_id,
                    'uuid' => $course->uuid,
                    'image_url' => $course->image_url,
                    'discussions_count' => $discussionCount,
                ];
            }
        }
        return response()->json([
            'success' => true,
            'courses' => $result_array
        ]);
        }
        else{
        $coordinatorId = Coordinator::where('user_id', $user->id)->value('id');
        if (!$coordinatorId) {
            return response()->json($response);
        }
        $searchTitle = $request->input('search_title');

        $courseIds = Discussion::leftJoin('coordinator_courses', function ($join) {
            $join->on('discussions.course_id', '=', 'coordinator_courses.course_id');
        })
            ->join('courses', 'courses.id', '=', 'discussions.course_id')
            ->where(function ($q) use ($coordinatorId) {
                $q->Where('coordinator_courses.coordinator_id', $coordinatorId);
            })
            ->whereNull('parent_id')
            ->where('discussions.status', 1)
            ->NotView()
            ->select('discussions.course_id')
            ->groupBy('discussions.course_id')
            ->pluck('course_id')
            ->toArray();

      
        $courses = Course::whereIn('id', $courseIds)
            ->when($searchTitle, function ($q) use ($searchTitle) {
                $q->where('title', 'LIKE', "%{$searchTitle}%");
            })
            ->select('id', 'title', 'image', 'user_id', 'uuid')
            ->get();
        $result_array = array();
        if(!$courses->isEmpty()){
            foreach ($courses as $key => $course) {
                 $discussionCount =Discussion::where('course_id',$course->id)
                 ->whereNull('parent_id')
                  ->NotView()
                  ->where('status', 1)->count();
                $result_array[] = [
                    'id' => $course->id,
                    'title' => $course->title,
                    'image' => asset($course->image),
                    'user_id' => $course->user_id,
                    'uuid' => $course->uuid,
                    'image_url' => $course->image_url,
                    'discussions_count' => $discussionCount,
                ];
            }
        }
        return response()->json([
            'success' => true,
            'courses' => $result_array
        ]);
        }
        
    }

   
//     public function courseDiscussionListApiBackUp(Request $request,$course_id)
// {
   
//     $user = Auth::user();
//     if($user){
//         $role = $user->role;
//         if($role != 2){ // Instructor
//             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
//         }
//     }
//     else{
//         return response()->json(['message' => 'User Is Not Logged In'], 302);
//     }
  
//     $discussions = Discussion::whereCourseId($course_id)
//         ->whereNull('parent_id')
//         ->active()
//         ->NotView()
//         ->with([
//             'user:id,name,image', // load user with selected fields
//             'replies' => function ($query) {
//                 $query->active()->with('user:id,name,image'); // load user inside replies
//             }
//         ])
//         ->get();

//     // dd($discussions);
//     return response()->json([
//         'success' => true,
//         'discussions' => $discussions
//     ]);
// }


    public function courseDiscussionListApi(Request $request,$course_id)
{
   
    $user = Auth::user();
    if($user){
        $role = $user->role;
        if($role != 2 && $role != 5){
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
        }
    }
    else{
        return response()->json(['message' => 'User Is Not Logged In'], 302);
    }
  
    $discussions = Discussion::whereCourseId($course_id)
        ->whereNull('parent_id')
        ->active()
        ->NotView()
        ->with([
            'user:id,name,image', // load user with selected fields
            'replies' => function ($query) {
                $query->active()->with('user:id,name,image'); // load user inside replies
            }
        ])
        ->get();

    // dd($discussions);
    return response()->json([
        'success' => true,
        'discussions' => $discussions
    ]);
}

    // public function instructorCourseDiscussionReplyApiBackUp(Request $request, $discussion_id)
    // {
       
    //     $user = Auth::user();
    //     if($user){
    //         $role = $user->role;
    //         if($role != 2){ // Instructor
    //             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
    //         }
    //     }
    //     else{
    //         return response()->json(['message' => 'User Is Not Logged In'], 302);
    //     }

    //     $checkDiscussion= Discussion::where('id',$discussion_id)->first();
    //     if($checkDiscussion == null){
    //          return response()->json(['message' => 'Invalid Discussion ID. Does not Exists'], 302);
    //     }
     

    //      $messages = [
    //                 'course_id.required' => __('The Course ID Is Required'),
    //                 'course_id.exists' => __('The Course ID Does Not Exists'),
    //                 'commentReply.required' => __('The Comment Reply Is Required'),
    //      ];
    //     $validator = Validator::make($request->all(), [
    //       'course_id' => 'required|exists:courses,id',
    //         'commentReply' => 'required|string',
    //     ], $messages);

    
    //     if ($validator->fails()) {
    //         return response(['status' => 0, 'error' => $validator->errors()], 500);
    //     }



    //     $checkCourse= Course::where('id',$request->course_id)->first();
    //     if($checkCourse == null){
    //          return response()->json(['message' => 'Invalid Course ID. Does not Exists'], 302);
    //     }
    //     else{
    //         $checkCourse2 = Discussion::where('course_id',$request->course_id)->first();
    //         if($checkCourse2 == null){
    //              return response()->json(['message' => 'Invalid Course ID. Does not exists in discussions'], 302);
    //         }
    //     }





    //     $checkDiscussion2 = Discussion::where('id',$discussion_id)->where('course_id',$request->course_id)->first();
    //     if($checkDiscussion2 == null){
    //          return response()->json(['message' => 'Invalid Discussion ID For given course'], 302);
    //     }

    //     // Create new reply
    //     $discussion = new Discussion();
    //     $discussion->user_id = Auth::id();
    //     $discussion->course_id = $request->course_id;
    //     $discussion->comment = $request->commentReply;
    //     $discussion->status = 1;
    //     $discussion->parent_id = $discussion_id;
    //     $discussion->comment_as = 1;
    //     $discussion->save();

    //     // Mark parent and its replies as viewed
    //     Discussion::where('id', $discussion_id)->update(['view' => 1]);
    //     Discussion::where('parent_id', $discussion_id)->update(['view' => 1]);

    //     // Fetch updated parent discussions for the course (excluding replies)
    //     $discussions = Discussion::where('course_id', $request->course_id)
    //         ->whereNull('parent_id')
    //         ->active()
    //         ->NotView()
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Reply added successfully',
    //         'discussions' => $discussions
    //     ]);
    // }



      public function instructorCourseDiscussionReplyApi(Request $request, $discussion_id)
    {
       
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        $checkDiscussion= Discussion::where('id',$discussion_id)->first();
        if($checkDiscussion == null){
             return response()->json(['message' => 'Invalid Discussion ID. Does not Exists'], 302);
        }
     

         $messages = [
                    'course_id.required' => __('The Course ID Is Required'),
                    'course_id.exists' => __('The Course ID Does Not Exists'),
                    'commentReply.required' => __('The Comment Reply Is Required'),
         ];
        $validator = Validator::make($request->all(), [
          'course_id' => 'required|exists:courses,id',
            'commentReply' => 'required|string',
        ], $messages);

    
        if ($validator->fails()) {
            return response(['status' => 0, 'error' => $validator->errors()], 500);
        }



        $checkCourse= Course::where('id',$request->course_id)->first();
        if($checkCourse == null){
             return response()->json(['message' => 'Invalid Course ID. Does not Exists'], 302);
        }
        else{
            $checkCourse2 = Discussion::where('course_id',$request->course_id)->first();
            if($checkCourse2 == null){
                 return response()->json(['message' => 'Invalid Course ID. Does not exists in discussions'], 302);
            }
        }





        $checkDiscussion2 = Discussion::where('id',$discussion_id)->where('course_id',$request->course_id)->first();
        if($checkDiscussion2 == null){
             return response()->json(['message' => 'Invalid Discussion ID For given course'], 302);
        }

        // Create new reply
        $discussion = new Discussion();
        $discussion->user_id = $user->id;
        $discussion->course_id = $request->course_id;
        $discussion->comment = $request->commentReply;
        $discussion->status = 1;
        $discussion->parent_id = $discussion_id;

        if($user->role == 2){
            $discussion->comment_as = 1;
        }
        else{
            $discussion->comment_as = 3;
        }
        $discussion->save();

        // Mark parent and its replies as viewed
        Discussion::where('id', $discussion_id)->update(['view' => 1]);
        Discussion::where('parent_id', $discussion_id)->update(['view' => 1]);

        // Fetch updated parent discussions for the course (excluding replies)
        $discussions = Discussion::where('course_id', $request->course_id)
            ->whereNull('parent_id')
            ->active()
            ->NotView()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
            'discussions' => $discussions
        ]);
    }




    public function myCourseShowApiBackUp(Request $request, $slug, $action_type = null, $quiz_uuid = null, $answer_id = null)
{
        try {
            //code...
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }
      
        $result_array = array();
        $action_type = $request->action_type;
        $quiz_uuid = $request->quiz_uuid;
        ///////////////////////////
        $data['pageTitle'] = "Course Details";
        $data['course'] = Course::with('publishedExams')->whereSlug($slug)->first();
        if($data['course'] == null){
            return response()->json(['message' => 'Given Course Slug Does Not Exists'], 302);
        }
            //Current User Given Course Active Period Enrollment Info
            $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => auth()->id(), 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
     
            // End:: Checking enrolled or not
            // Current Given Course Total Student Enrollments Count
            $data['total_enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();
    
            // 4 Out of Total Enrollments Data For Current Given Course
            $data['enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->take(4)->get();

            if($data['enrollment']){
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $data['enrollment']->id),2);
            }
            else{
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, 0),2);
            }
        
      
        //  //Start:: Live Class
        //  $now = now();

        //  $data['upcoming_live_classes'] = LiveClass::whereCourseId($data['course']->id)
        //      ->where(function($q){    // Current Today Pending Live Classes
        //          $q->whereDate('date', now());
        //          $q->whereTime('time', '>', now());
        //      })
        //      ->orWhere(function($q){     // Upcomming Scheduled Live Classes
        //          $q->whereDate('date', '>', now());
        //      })
        //      ->latest()->get();
 
        //  $data['current_live_classes'] = LiveClass::whereCourseId($data['course']->id)
        //      ->where(function($q){         //  Current Today Live Active Classes
        //          $q->whereDate('date', now());
        //          $q->whereTime('time', '<=', now());
        //          $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
        //      })
        //      ->latest()->get();
 
        //  $data['past_live_classes'] = LiveClass::whereCourseId($data['course']->id)
        //  ->where(function($q){
        //          $q->whereDate('date', now());
        //          $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
        //      })
        //      ->orWhere(function($q){
        //          $q->whereDate('date', '<', now());
        //      })
        //      ->latest()->get();

     

            if (!is_null($action_type) && !is_null($quiz_uuid)) {
                $data['exam'] = Exam::whereUuid($quiz_uuid)->first();
                if ($action_type == 'start-quiz') { 
                    if (Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count() == 0) {
                        $take_exam = new Take_exam();
                        $take_exam->exam_id = $data['exam']->id;
                        $take_exam->save();
                    }
                    else{
                        $takeExam = Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                        $isPassed = $takeExam->isPassed;
                        if(!$isPassed){
                            $totalQuestions = Question::whereExamId($data['exam']->id)->count();
                            $attemptedAnswers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$takeExam->id)->count();
                            if($attemptedAnswers == $totalQuestions){
                                $take_exam = new Take_exam();
                                $take_exam->exam_id = $data['exam']->id;
                                $take_exam->save();
                            }
                        }
                    }
    
                    $data['take_exam'] = Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
    
                    // $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                    $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();
                    $data['question'] = Question::whereExamId($data['exam']->id)->whereNotIn('id', $question_ids)->first();
                    // $data['number_of_answer'] = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count();
                    $data['number_of_answer'] = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$data['take_exam']->id)->count();
    
    
                    $now = Carbon::now();
                    $expend_second = $now->diffInSeconds($data['take_exam']->created_at);
    
                    if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                        return redirect(route('student.my-course.show', [$data['course']->slug, 'quiz-result', $data['exam']->uuid]));
                    }
                }
              
                if ($action_type == 'leaderboard') {
                 
                    $data['top5_take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->take(5)->get();
                    $data['take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->skip(5)->take(500)->get();
                
                }
            }
         
            if (!is_null($answer_id)) {
                $data['answer'] = Answer::find($answer_id);
            }
            
            $data['action_type'] = $action_type;
            $data['quiz_uuid'] = $quiz_uuid;
            $data['answer_id'] = $answer_id;
    
            /** ------- save certificate ----------- */
    
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', $user->id)->get();
            $isFirst = count($data['course_lecture_views']) ? false : true;
            $data['isFirst'] = $isFirst;
         
            $Base_Route = route('student.my-course.show', [$data['course']->slug]);
            $data['BaseRoute'] = $Base_Route;

          
          
                ////////////////////////////////////////////////////////////////////////////////////////////////////////
            $navbar_array = array();
            $review_tab_array = array();
            $content_tab_array = array();
            $quiz_tab_array = array();
            $assignment_tab_array = array();
            $notice_tab_array = array();
            $liveclass_tab_array = array();
            $liveclass_tab_array = [
                'upcomming' => array(),
                'current' => array(),
                'past' => array(),
            ];
           
            $discussion_tab_array = array();
            $certificate_tab_array = array();
            $enrolled_student_image_array = array();
            $course_content_view_section = array();
            $content_details_array = array();
            $course_content_list_section = array();

            $data['course'] = Course::with('publishedExams')->whereSlug($slug)->firstOrfail();
               //Current User Given Course Active Period Enrollment Info
            $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
              //Current User Given Course Total Lecture Views
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', $user->id)->get();



              ////////////////////////////COURSE CATEGORY BASE RESOURCES FOR LIVE TYPE START//////////////////////////
            $resources_array = array();
            $category_ids_array = array();
            $course_id = $data['course']->id;
            $data['courseID'] = $course_id;
            $getCourseResources =  CourseResource::where('course_id',$course_id)->where('resource_id','<>',null)->get();
            if(!$getCourseResources->isEmpty()){
                foreach ($getCourseResources as $key => $course_resource) {
                    $resources_array[] = $course_resource->resource_id;
                }
            }
            $resources_array = array_unique($resources_array);
            if(!empty($resources_array)){
                $getResourceCategories = Resource::whereIn('id',$resources_array)->get();
                if(!$getResourceCategories->isEmpty()){
                    foreach ($getResourceCategories as $key => $resource) {
                        $category_ids_array[] = $resource->category_id;
                    }
                }
            }
            $result_data = array();
            $category_ids_array = array_unique($category_ids_array);
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            if(!$getCategories->isEmpty()){
                foreach ($getCategories as $key => $value) {
                    $resource_array = array();
                    $categoryID = $value->id;
                    $resources = CourseResource::where('course_id',$course_id)
                    ->whereHas('resource', function ($query) use ($categoryID) {
                        $query->where('category_id', $categoryID);
                    })->get();
                    if(!$resources->isEmpty()){
                        foreach ($resources as $key => $value2) {
                            if($value2->resource?->type == 'Video' || $value2->resource?->type == 'video'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Youtube' || $value2->resource?->type == 'youtube'){
                                if (Str::contains($value2->resource?->youtube_url_path, 'https://www.youtube.com')) {
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else if(Str::contains($value2->resource?->youtube_url_path, 'https://youtu.be')){
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else{
                                     $preview_src = "https://www.youtube.com/embed/".$value2->resource?->youtube_url_path;
                                 }
                            
                            }
                            elseif($value2->resource?->type == 'Text' || $value2->resource?->type == 'text'){
                                $preview_src =  $value2->resource?->text;
                            }
                            elseif($value2->resource?->type == 'Image' || $value2->resource?->type == 'image'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'PDF' || $value2->resource?->type == 'pdf'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Slide Document' || $value2->resource?->type == 'slide document'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name.'/index.html';
                            }
                            elseif($value2->resource?->type == 'Audio' || $value2->resource?->type == 'audio'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            else{
                                $preview_src = null;
                            }
                            $resource_array[] = [
                                'resource_id'=>$value2->resource->id,
                                'resource_name'=>$value2->resource->name,
                                'resource_ext'=>$value2->resource->file_extension,
                                'resource_path'=>$value2->resource->file_name,
                                'resource_type'=>$value2->resource->type,
                                'resource_preview_src'=>$preview_src,
                            ];
                        }
                    }
                    $result_data[] = [
                        'category_id' => $categoryID,
                        'category_name' => $value->name,
                        'category_resources' => $resource_array,
                    ];
                }
            }
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            $data['categories'] = $getCategories;
            $data['categories_with_resources'] = $result_data;
            ////////////////////////////COURSE CATEGORY BASE RESOURCES FOR LIVE TYPE END//////////////////////////

              ////////////////////////////COURSE CONTENT RIGHT SIDE SECTION START//////////////////////////
            if ($request->get('lecture_uuid')) {
                $lecture = Course_lecture::where('uuid', $request->get('lecture_uuid'))->firstOrFail(); //Get Lecture Data Through Given Lecture UUID    
                $isFirst = count($data['course_lecture_views']) ? false : true;  // Count Current User Total Views For Lectures OF gIVEN Course
            
            
                if(!checkStudentCourseIsLock( $data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $isFirst)){
              
                    $nextLecture = $this->getNextId($data['course_lecture_views'], $data['course'],  $lecture, $data['enrollment']);
               
                    if ($nextLecture) {
                        $data['nextLectureUuid'] = $nextLecture->uuid;
                    } else {
                        $data['nextLectureUuid'] = null;
                    }
               
                    if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        $data['video_src'] =  $lecture->resource?->file_name;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $data['youtube_video_src'] = $lecture->resource?->youtube_url_path;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    } 
                    elseif ($lecture->type == 'vimeo') {
                        $data['vimeo_video_src'] = $lecture->url_path;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                            }
                        }
    
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        $data['text_src'] = $lecture->resource?->text;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                        $lecture = Course_lecture::find($lecture->id);
                   
                        if ($lecture) {
                         
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                              
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                             
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        // $data['image_src'] = $lecture->image;
                        $data['image_src'] =  $lecture->resource?->file_name;
                    
                    } 
                    elseif ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $data['pdf_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        // return redirect(getImageFile($pdf_src));
                    } 
                    elseif ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $data['slide_document_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $data['audio_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    }
                    elseif($lecture->quiz){
                        $data['exam'] = Exam::whereUuid($lecture->quiz->uuid)->first();
                        if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() > 0) {
                            $take_exam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                            $take_exam->lecture_id = $lecture->id;
                           
                        
                            $answered_question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                            $total_questions = Question::whereExamId($data['exam']->id)->count();
                            // $number_of_attempted_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count();
                            $number_of_attempted_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$take_exam->id)->count();
        
                            if($number_of_attempted_answers == $total_questions){  // USER Attempted All Question Before EndTime
                                if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                                    //NEW CODE
                                    if($lecture->quiz){
                                        $quizPassedPercentage = $lecture->quiz->passing_percentage;
                                        $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                                        ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                        $cal_percentage = ($correct_answers/$total_questions)*100;
                                        $cal_percentage = (int)$cal_percentage;
                                        if($cal_percentage >= $quizPassedPercentage){
                                            $lecture = Course_lecture::find($lecture->id);
                                            if ($lecture) {
                                                if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                                    if($data['enrollment']){
                                                        $enrollment_id = $data['enrollment']->id;
                                                    }
                                                    else{
                                                        $enrollment_id = null;
                                                    }
                                                    $course_lecture_views = new Course_lecture_views();
                                                    $course_lecture_views->course_id = $lecture->course_id;
                                                    $course_lecture_views->course_lecture_id = $lecture->id;
                                                    $course_lecture_views->enrollment_id = $enrollment_id;
                                                    $course_lecture_views->save();
                                                }
                                                $take_exam->obtained_percentage = $cal_percentage;
                                                $take_exam->passing_percentage = $quizPassedPercentage;
                                                $take_exam->passed = 1;
                                                $take_exam->update();
                                            }
                                        }
                                        else{
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 0;
                                            $take_exam->update();
                                        }
                                    }
                                 
                                }
                            }
                            else{  // Quiz Time Is Ended
                                $now = Carbon::now();
                                $expend_second = $now->diffInSeconds($take_exam->created_at);
                                if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                                    if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                                        //NEW CODE
                                        if($lecture->quiz){
                                            $quizPassedPercentage = $lecture->quiz->passing_percentage;
                                            $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                                            ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                            $cal_percentage = ($correct_answers/$total_questions)*100;
                                            $cal_percentage = (int)$cal_percentage;
                                        }
                                        if($cal_percentage >= $quizPassedPercentage){
                                            $lecture = Course_lecture::find($lecture->id);
                                            if ($lecture) {
                                            
                                                if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                                    if($data['enrollment']){
                                                        $enrollment_id = $data['enrollment']->id;
                                                    }
                                                    else{
                                                        $enrollment_id = null;
                                                    }
                                                    $course_lecture_views = new Course_lecture_views();
                                                    $course_lecture_views->course_id = $lecture->course_id;
                                                    $course_lecture_views->course_lecture_id = $lecture->id;
                                                    $course_lecture_views->enrollment_id = $enrollment_id;
                                                    $course_lecture_views->save();
                                                 
                                                }
                                                $take_exam->obtained_percentage = $cal_percentage;
                                                $take_exam->passing_percentage = $quizPassedPercentage;
                                                $take_exam->passed = 1;
                                                $take_exam->update();
                                            }
                                        }
                                        else{
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 0;
                                            $take_exam->update();
                                        }   
                                }
                                }
                            } 
                            $take_exam->update();
                        }   
                    }
                    
                    // $data['lecture_type'] = $lecture->type;
                    if($lecture->resource){
                        $data['lecture_type'] = $lecture->resource?->category?->name;
                    }
                    elseif($lecture->quiz){
                        $data['lecture_type'] = $lecture->type;
                    }
                    elseif($lecture->assignment){
                        $data['lecture_type'] = $lecture->type;
                    }
                 
                    $data['lesson_id_check'] = @$lecture->lesson->id;
                    $data['lecture_id_check'] = $lecture->id;
                    $data['navLessonActive'] = 'on';
                    $data['subNavLectureActiveClass'] = 'show';
                    $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', auth()->id())->get();
                }
            }
            else{
                $data['lecture_type'] = null;
                $data['lesson_id_check'] = null;
                $data['lecture_id_check'] = null;
                $data['navLessonActive'] = 'off';
                $data['subNavLectureActiveClass'] = '';
            }
          
            if($data['course']->course_type == COURSE_TYPE_GENERAL){
                if($data['course']->lectures->count() > 0){
                    $course_video_duration = $data['course']->video_duration;
                }
                else{
                    $course_video_duration = '0 min';
                }

                if($data['lecture_type']){
                    if($data['lecture_type'] == 'Youtube' || $data['lecture_type'] == 'youtube'){
                        $content_details_array = [
                            // 'youtube_video_src' => "https://www.youtube.com/embed/".$data['youtube_video_src'],
                            'preview_src' => "https://www.youtube.com/embed/".$data['youtube_video_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Video' || $data['lecture_type'] == 'video'){
                        $content_details_array = [
                            // 'video_src' => asset('uploads/course_resource').'/'.$data['video_src'],
                            'preview_src' => asset('uploads/course_resource').'/'.$data['video_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Text' || $data['lecture_type'] == 'text'){
                        $content_details_array = [
                            'preview_src' => $data['text_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Image' || $data['lecture_type'] == 'image'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['image_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'PDF' || $data['lecture_type'] == 'pdf'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['pdf_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Slide Document' || $data['lecture_type'] == 'slide document'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['slide_document_src'].'/index.html',
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Audio' || $data['lecture_type'] == 'audio'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['audio_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                }
                else{
                    if($data['course']->intro_video_check == 1 && getVideoFile($data['course']->video)){
                        $content_details_array = [
                            // 'course_image_poster' => getImageFile($data['course']->image),
                            'preview_src' => getVideoFile($data['course']->video),
                        ];
                        $course_content_view_section = [
                            'content_type' => __('Course Intro Video'),
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['course']->intro_video_check == 2 && $data['course']->youtube_video_id){
                        if (Str::contains($data['course']->youtube_video_id, 'https://www.youtube.com')) {
                            $content_details_array = [
                                // 'youtube_video_src' => $data['course']->youtube_video_id,
                                'preview_src' => $data['course']->youtube_video_id,
                            ];
                        }
                        else if(Str::contains($data['course']->youtube_video_id, 'https://youtu.be')){
                            $content_details_array = [
                                // 'youtube_video_src' => $data['course']->youtube_video_id,
                                'preview_src' => $data['course']->youtube_video_id,
                            ];
                        }
                        else{
                            $content_details_array = [
                                // 'youtube_video_src' => "https://www.youtube.com/embed/".$data['course']->youtube_video_id,
                                'preview_src' => "https://www.youtube.com/embed/".$data['course']->youtube_video_id,
                            ];
                        } 
                      
                        $course_content_view_section = [
                            'content_type' => __('Course Intro Youtube Video'),
                            'content_details' => $content_details_array,
                        ];
                    }
                    else{
                        $content_details_array = [
                            'preview_src' => $data['course']->cover_image ? getImageFile($data['course']->cover_image) : getImageFile($data['course']->image),
                        ];
                        $course_content_view_section = [
                            'content_type' => __('Course Image'),
                            'content_details' => $content_details_array,
                        ];
                    }
                }

              

                $lockCount = 0;
                $outerLoopKey = 0;
                $innerLoopKey = 0;
                foreach ($data['course']->lessons as $key => $lesson) {
                    $lesson_lectures_array = array();
                    if($key == 0){
                        $outerLoopKey = 1;
                    }
                    else{
                        $outerLoopKey = 0;
                    }
                    foreach ($lesson->lectures as $key2 => $lecture) {
                        if($key2 == 0){
                            $innerLoopKey = 1;
                        }
                        else{
                            $innerLoopKey = 0;
                        }

                        if($innerLoopKey && $outerLoopKey){
                            $loopFirst = True;
                        }
                        else{
                            $loopFirst = False;
                        }
                        // $lock = checkStudentCourseIsLock($data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $loop->first);
                        $lock = checkStudentCourseIsLock($data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $innerLoopKey);
                        if(checkStudentCourseView($data['course_lecture_views'], $data['course']->id, $lecture->id)){
                            $class1 = 'watchFinishedCourse';
                            $data_icon = 'ant-design:check-circle-filled';
                        } 
                        else if($lecture->id == $data['lecture_id_check']){
                            $class1 = 'watchContinuingCourse';
                            $data_icon = 'fluent:radio-button-24-filled';
                        } 
                        else{
                            $class1 = 'watchNewLecture';
                            $data_icon = 'fluent:radio-button-24-regular';
                        }
                        if($lecture->quiz_id != null){
                            $lectureType = 'Quiz';
                        }
                        else if($lecture->assignment_id != null){
                            $lectureType = 'Assignment';
                        }
                        else if($lecture->resource_id != null){
                            $lectureType = 'Resource';
                        }
                        else{
                            $lectureType = $lecture->type;
                        }
                            $isLocked = 'No';
                            if($lectureType == 'Resource'){
                                if ($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube'){
                                    $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource->id , 'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                                else{
                                    $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                            }
                            elseif($lectureType == 'Quiz'){
                                if(checkStudentCourseView($data['course_lecture_views'],
                                                                        $data['course']->id,
                                                                        $lecture->id)){
                                $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]);
                                $onClickRoute = route('click-lecture-course');
                                $onClickRouteName = __('(Student) Click Lecture');
                                }
                                else{
                                    if(check_take_attempt_exam($lecture->quiz_id) == 'no'){
                                        $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'start-quiz', $lecture->quiz?->uuid]); 
                                        $onClickRoute = route('click-lecture-course');  
                                        $onClickRouteName = __('(Student) Click Lecture');
                                    }
                                    else{
                                        $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]); 
                                        $onClickRoute = route('click-lecture-course');
                                        $onClickRouteName = __('(Student) Click Lecture');
                                    }
                                }
                            }
                            elseif($lectureType == 'Assignment'){
                                if(checkStudentCourseView($data['course_lecture_views'],
                                $data['course']->id,
                                $lecture->id)){
                                    $redirectURLRoute = route('student.assignment-result');
                                    $onClickRoute = route('assignment-result-course');
                                    $onClickRouteName = __('(Student) Assignment Detail');
                                }
                                else{
                                    $redirectURLRoute = route('student.assignment-details'); 
                                    $onClickRoute = route('assignment-detail-course');
                                    $onClickRouteName = __('(Student) Assignment Result');
                                }
                            }
                            else{
                                //NEW CASE IN WHICH ALL 3 QUIZ,ASSIGNMENT AND RESOURCE_ID ARE NULL AND LECTURE ARE WITHIN IT SELF TABLE
                                // LIKE IMAGE,AUDIO,VIDEO, YOUTUBE, SLIDE ETC
                                if ($lecture->type == 'text' || $lecture->type == 'Text' || $lecture->type == 'Youtube' || $lecture->type == 'youtube'){
                                    // $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource?->id , 'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                                else{
                                    // $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                            }

                            if($lectureType == 'Assignment'){
                            }
                            elseif($lectureType == 'Quiz'){
                            }
                            else if($lectureType == 'Resource'){
                                if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                    $previewType = $lecture->resource?->type;
                                }
                                else if ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                                      $previewType = $lecture->resource?->type;
                                    if (Str::contains($lecture->resource?->youtube_url_path, 'https://www.youtube.com')) {
                                        $previewSrc = $lecture->resource?->youtube_url_path;    
                                    }
                                    else if(Str::contains($lecture->resource?->youtube_url_path, 'https://youtu.be')){
                                        $previewSrc = $lecture->resource?->youtube_url_path;    
                                    }
                                    else{
                                        $previewSrc = "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
                                    }
                                }
                                else if ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
                                      $previewType = $lecture->resource?->type;
                                    // $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                    $previewSrc = $lecture->resource?->text;
                                }
                                else if ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                }
                                else if ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                }
                                else if ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name.'/index.html';
                                }
                                else if ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                }
                                $lesson_lectures_array[]=[
                                    'sr_no'=>$key2+1,
                                    'name'=>$lecture->title,
                                    // 'type'=>$lectureType,
                                    'type'=> $previewType,
                                    'preview_src'=>$previewSrc,
                               ]; 
                            }
                            else{
                                if ($lecture->type == 'Video' || $lecture->type == 'video') {
                                     $previewSrc = asset($lecture->file_path);
                                }
                                else if ($lecture->type == 'Youtube' || $lecture->type == 'youtube') {
                                     $previewSrc = $lecture->url_path;
                                }
                                else if ($lecture->type == 'Image' || $lecture->type == 'image') {
                                     $previewSrc = asset($lecture->image);
                                }
                                else if ($lecture->type == 'Image' || $lecture->type == 'image') {
                                     $previewSrc = asset($lecture->image);
                                }
                                elseif ($lecture->type == 'PDF' || $lecture->type == 'pdf') {
                                      $previewSrc = asset($lecture->pdf);
                                }
                                else if ($lecture->type == 'Slide Document' || $lecture->type == 'slide_document') {
                                     $previewSrc = $lecture->slide_document;
                                }
                                else if ($lecture->type == 'Audio' || $lecture->type == 'audio') {
                                     $previewSrc = asset($lecture->audio);
                                }
                                else if ($lecture->type == 'Text' || $lecture->type == 'text') {
                                    $previewSrc = $lecture->text;
                                }
                                $lesson_lectures_array[]=[
                                    'sr_no'=>$key2+1,
                                    'name'=>$lecture->title,
                                    'type'=>$lecture->type,
                                    'preview_src'=>$previewSrc,
                               ]; 
                            }
                    }
                    $course_content_list_section[]=[
                        'sr_no' => $key+1,
                        'name' => $lesson->name,
                        'data'=> $lesson_lectures_array,
                    ];
                }
            }
            elseif($data['course']->course_type == COURSE_TYPE_SCORM){
                if($data['course']->scorm_course->duration){
                    $course_video_duration = $data['course']->scorm_course->duration;
                }
                else{
                    $course_video_duration = '0 min';
                }

                if ($data['course']->scorm_course){
                    $content_details_array = [
                        'scorm_src' => asset("scorm/".$data['course']->scorm_course?->uuid.'/'.$course->scorm_course?->entry_url),
                    ];
                    $course_content_view_section = [
                        'content_type' => __('Scorm Course Content'),
                        'content_details' => $content_details_array,
                    ];
                }
            }
            else{
                $course_video_duration = null;

                foreach ($data['categories_with_resources'] as $key => $category) {
                    $category_resources_array = array();
                    foreach ($category['category_resources'] as $key2=> $resource) {
                        if ($resource['resource_type'] == 'Text' || $resource['resource_type'] == 'Youtube'){
                            // $redirectURL = route('student.resource.view_full_other', ['resource_id'=>$resource['resource_id']]);
                            $redirectSRC =  $resource['resource_preview_src']; 
                        }
                        else{
                            // $redirectURL = route('student.resource.view_full', ['ext'=>$resource['resource_ext'] , 'path'=> $resource['resource_path']]);
                            $redirectSRC =  $resource['resource_preview_src'];
                        }
                        $category_resources_array[] = [
                            // 'resource_id' => $resource['resource_id'],
                            'sr_no' => $key2 + 1,
                            'name' => $resource['resource_name'],
                            'type' => $resource['resource_type'],
                            'preview_src' => $redirectSRC,
                            // 'redirect_url' => $redirectURL,
                        ];
                    }
                    $course_content_list_section[]=[
                        'sr_no' => $key+1,
                        'name' => $category['category_name'],
                        'data' => $category_resources_array,
                    ];
                }
            }

              if($data['course']->intro_video_check == 1 && getVideoFile($data['course']->video)){
                    $featured_image_or_video = getVideoFile($data['course']->video);
                    $featured_image_or_video_type= 1;
             }
                else if($data['course']->intro_video_check == 2 && $data['course']->youtube_video_id){
                    if (Str::contains($data['course']->youtube_video_id, 'https://www.youtube.com')) {
                        $featured_image_or_video =  $data['course']->youtube_video_id;
                    }
                    else if(Str::contains($data['course']->youtube_video_id, 'https://youtu.be')){
                        $featured_image_or_video =  $data['course']->youtube_video_id;
                    }
                    else{
                        $featured_image_or_video = "https://www.youtube.com/embed/".$data['course']->youtube_video_id;
                    }
                     $featured_image_or_video_type= 2;
                }
                else{
                       $featured_image_or_video =  $data['course']->cover_image ? getImageFile($data['course']->cover_image) : getImageFile($data['course']->image);
                        $featured_image_or_video_type= 3;
                }


            if (!is_null($action_type) && !is_null($quiz_uuid)) {
                $navbar_array = [
                    'course_title' =>$data['course']->title,
                    'return_url' =>route('student.my-course.show',['slug'=>$data['course']->slug]),
                    'total_course_lectures' =>$data['course']->lectures->count(),
                    'total_course_lessons' =>$data['course']->lessons->count(),
                    'total_course_video_duration' =>$course_video_duration,
                ];
            }
            else{
                $navbar_array = [
                    'course_title' =>$data['course']->title,
                    'total_course_lectures' =>$data['course']->lectures->count(),
                    'total_course_lessons' =>$data['course']->lessons->count(),
                    'total_course_video_duration' =>$course_video_duration,
                ];
            }
             ////////////////////////////COURSE CONTENT RIGHT SIDE SECTION END//////////////////////////

            $total_enrolled_students = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();
            $enrolled_students = Enrollment::where('course_id', $data['course']->id)->take(4)->get();
            foreach ($enrolled_students as $key => $enroll) {
               $enrolled_student_image_array[] = getImageFile($enroll->user?->image_path);
            }

            if($data['enrollment']){
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $data['enrollment']->id),2);
            }
            else{
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, 0),2);
            }

            ////////////////////////////OVERVIEW TAB SECTION START//////////////////////////
            $overview = array();
            if($data['course']){
                if(count($data['course']->keyPoints) > 0){
                    $overview = [
                        'keyPoints' => $data['course']->keyPoints,
                        'description' => $data['course']->description,
                    ];
                }
                else{
                    $overview = [
                        'keyPoints' => array(),
                        'description' => $data['course']->description,
                    ];
                }
            }
            $data['overview'] = $overview;
            //////////////////////////OVERVIEW TAB SECTION END/////////////////////////////




            $liveclass_tab_array = [
                'upcomming' => array(),
                'current' => array(),
                'past' => array(),
                'footer_section' => [
                    'enrolled_students_image' => $enrolled_student_image_array,
                    'total_enrolled_students' => $total_enrolled_students,
                    'course_last_update' => $data['course']->updated_at->format('d M Y'),
                ],
            ];



            //////////////////////////REVIEW TAB SECTION START/////////////////////////////
            $data['reviews'] = Review::whereCourseId($data['course']->id)->latest()->paginate(3);
            $data['loadMoreShowButtonReviews'] = Review::whereCourseId($data['course']->id)->paginate(4);
            $data['five_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(5)->count();
            $data['four_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(4)->count();
            $data['three_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(3)->count();
            $data['two_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(2)->count();
            $data['first_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(1)->count();
    
            $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
                (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
            $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];
        
            if ($data['total_user_review'] > 0) {
                $data['average_rating'] = $data['total_reviews'] / $data['total_user_review'];
            } else {
                $data['average_rating'] = 0;
            }
          
            $total_reviews = Review::whereCourseId($data['course']->id)->count();
    
            if ($total_reviews > 0) {
                $data['five_star_percentage'] = ($data['five_star_count'] / $total_reviews) * 100;
    
                $data['four_star_percentage'] = 100 * ($data['four_star_count'] / $total_reviews);
                $data['three_star_percentage'] = 100 * ($data['three_star_count'] / $total_reviews);
                $data['two_star_percentage'] = 100 * ($data['two_star_count'] / $total_reviews);
                $data['first_star_percentage'] = 100 * ($data['first_star_count'] / $total_reviews);
            } else {
                $data['five_star_percentage'] = 0;
                $data['four_star_percentage'] = 0;
                $data['three_star_percentage'] = 0;
                $data['two_star_percentage'] = 0;
                $data['first_star_percentage'] = 0;
            }

            if($data['course']->average_rating >= 1){

            }
            $top_section_review_array = [
                // 'average_rating'=> number_format($data['course']->average_rating, 1),
                'average_rating'=> number_format($data['average_rating'], 1),
                'total_reviews'=> $data['total_user_review'],
            ];
        
            $progress_bars_section_review_array = [
                'five_star_percentage'=> $data['five_star_percentage'],
                'five_star_count'=> $data['five_star_count'],
                'four_star_percentage'=> $data['four_star_percentage'],
                'four_star_count'=> $data['four_star_count'],
                'three_star_percentage'=> $data['three_star_percentage'],
                'three_star_count'=> $data['three_star_count'],
                'two_star_percentage'=> $data['two_star_percentage'],
                'two_star_count'=> $data['two_star_count'],
                'first_star_percentage'=> $data['first_star_percentage'],
                'first_star_count'=> $data['first_star_count'],
            ];
            if(!$data['reviews']->isEmpty()){
                $customerReviewsArray = array();
                foreach ($data['reviews'] as $key => $cust_review) {
                    $customerReviewsArray[]=[
                        'user_id' => $cust_review->user?->id,
                        'user_name' => $cust_review->user?->name,
                        'user_image' => asset($cust_review->user?->image_path),
                        'review' => $cust_review->comment,
                        'rating' => $cust_review->rating,      
                        'submission_at' => $cust_review->created_at->diffForHumans(),
                    
                    ];
                }
            }
            else{
                $customerReviewsArray = array();
            }
            $review_tab_array =[
                // 'top-section'=>$top_section_review_array,
                // 'progress-bars-section'=>$progress_bars_section_review_array,
                // 'customers-reviews-section'=>$customerReviewsArray,

                // 'average_rating' => number_format($course->average_rating, 1),
                'total_reviews'=> $data['total_user_review'],
                // 'average_rating'=> number_format($data['average_rating'], 1),
                'five_star_percentage'=> (int)$data['five_star_percentage'],
                'four_star_percentage' => (int)$data['four_star_percentage'],
                'three_star_percentage' => (int)$data['three_star_percentage'],
                'two_star_percentage' => (int)$data['two_star_percentage'],
                'first_star_percentage' => (int)$data['first_star_percentage'],
                'user_reviews' => $customerReviewsArray,
            ];
            //////////////////////////////REVIEW TAB SECTION END //////////////////////////////////////

            /////////////////////////////QUIZ TAB SECTION START////////////////////////////////////////
            $data['quiz'] = $data['course']->publishedExams;
            if($data['quiz']->count() > 0){
                foreach ($data['quiz'] as $key => $quiz) {
                    if(take_exam($quiz->id) == 'no'){
                        $action_tab_api_routes = [
                            // 'start_quiz' => route('start-quiz-course'),
                            // 'leaderboard' => route('quiz-leaderboard-course')
                            'action_btn_1_api_route' => route('start-quiz-course'),
                            'action_btn_1_api_name' => __('(Student)Start Quiz'),
                            'action_btn_2_api_route' => route('quiz-leaderboard-course'),
                            'action_btn_2_api_name' => __('(Student) Quiz LeaderBoard')
                           
                        ];
                    }
                    else{
                        $action_tab_api_routes = [
                            // 'see_result' => route('quiz-result-course'),
                            // 'leaderboard' => route('quiz-leaderboard-course')
                            'action_btn_1_api_route' => route('quiz-result-course'),
                            'action_btn_1_api_name' => __('(Student)Complete Quiz Result'),
                            'action_btn_2_api_route' => route('quiz-leaderboard-course'),
                            'action_btn_2_api_name' => __('(Student) Quiz LeaderBoard')
                        ];
                    }
                  
                    $quiz_tab_array[] = [
                        'quiz_id' => $quiz->id,
                        'quiz_uuid' => $quiz->uuid,
                        'quiz_name' => $quiz->name,
                        'quiz_type' => ucfirst(str_replace("_", " ", $quiz->type)),
                        'total_questions' => $quiz->questions->count(),
                        'time_duration' => $quiz->duration.__(' minutes'),
                        'action_api_routes' => $action_tab_api_routes,
                    ];
                }
            }
            /////////////////////////////QUIZ TAB SECTION END//////////////////////////////////////////


            ////////////////////////////ASSIGNMENT TAB SECTION START///////////////////////////////////
            $data['assignments'] = Assignment::where('course_id', $data['course']->id)->get();
            if(count($data['assignments'])){
                foreach ($data['assignments'] as $key => $assignment) {
                    $action_btn_routes = [
                        'view_details_api_route'=> route('assignment-detail-course'),
                        'view_details_api_name'=> __('(Student) Assignment Detail'),
                        'see_result_api_route'=> route('assignment-result-course'),
                        'see_result_api_name'=> __('(Student) Assignment Result'),
                    ];
                    $assignment_tab_array[] = [
                        'assignment_id' => $assignment->id,
                        'assignment_topic' => Str::limit($assignment->name, 50),
                        'assignment_total_marks' => $assignment->marks,
                        'action_btn_api_routes'=> $action_btn_routes,
                    ];
                }
            }
            else{
                // $assignment_tab_array = [
                //     __('Empty Assignments')
                // ];
            }
            ////////////////////////////ASSIGNMENT TAB SECTION END/////////////////////////////////////


            //////////////////////////NOTICE TAB SECTION START/////////////////////////////
            $data['notices'] = NoticeBoard::whereCourseId($data['course']->id)->latest()->get();
            if(count($data['notices'])){
                foreach ($data['notices'] as $key => $notice) {
                    $notice_tab_array[]=[
                        'notice_id'=>$notice->id,
                        'created_at'=>$notice->created_at->format('d - m - Y'),
                        'notice_topic'=>$notice->topic,
                        'notice_details'=>$notice->details,
                       
                    ];
                }
               
            }
            //////////////////////////NOTICE TAB SECTION END/////////////////////////////

             //////////////////////////DISCUSSION TAB SECTION START/////////////////////////////
             $data['discussions'] = Discussion::whereCourseId($data['course']->id)->whereNull('parent_id')->active()->get();
             if(count($data['discussions']) > 0){
                $userID = Auth::id();
                if($userID){
                   $getUser = User::where('id',$userID)->first();
                   $Image =  getImageFile($getUser->image_path);
                   $auth_user_images = [
                        'discussion_form_user_image' => $Image,
                        'reply_form_user_image' => $Image,
                   ];
                }
                else{
                    $auth_user_images = [
                        'discussion_form_user_image' => null,
                        'reply_form_user_image' => null,
                   ];
                }
                 foreach ($data['discussions'] as $key => $discussion) {
                    // if($key == 1){
                    //     dd($discussion_replies_array);
                    // }   
                    if($discussion->comment_as == 1){
                        $discussion_user_type = 'Instructor';
                    }
                    else{
                        $discussion_user_type = 'Student'; 
                    }
                   
                    if(!$discussion->replies->isEmpty()){
                        $discussion_replies_array = array();
                        foreach ($discussion->replies as $reply) {
                            if($reply->comment_as == 1){
                                $reply_user_type = 'Instructor';
                            }
                            else{
                                $reply_user_type = 'Student'; 
                            }
                            $discussion_replies_array[]=[
                                'reply_id'=>$reply->id,
                                'reply_user_id'=>$reply->user?->id,
                                'reply_user_image'=>getImageFile($reply->user?->image_path),
                                'reply_user_name'=>$reply->user?->name,
                                'reply_user_type'=>$reply_user_type,
                                'reply_comment'=>$reply->comment,
                                'reply_created_at'=>$reply->created_at->diffforhumans(),
                            ];
                        }
                    }
                    else{
                        $discussion_replies_array = []; 
                    }

                  
                     $discussion_tab_array[]=[
                         'discussion_id'=>$discussion->id,
                         'discussion_user_id'=>$discussion->user?->id,
                         'discussion_user_name'=>$discussion->user?->name,
                         'discussion_user_image'=>getImageFile($discussion->user?->image_path),
                         'discussion_user_type'=>$discussion_user_type,
                         'discussion_comment'=>$discussion->comment,
                         'discussion_created_at'=>$discussion->created_at->diffforhumans(),
                         'discussion_total_replies'=>count($discussion->replies),
                         'discussion_replies_list'=> $discussion_replies_array,
                       
                     ];
                 }
                 $discussion_tab_array[] = [
                    'auth_user_images'=> $auth_user_images,
                 ];
             }
             //////////////////////////DISCUSSION TAB SECTION END/////////////////////////////

                //////////////////////////CERTIFICATE TAB SECTION START/////////////////////////////
                $data['certificate'] = $data['course']->studentCertificate;
                if($data['certificate']){
                    $certificate_tab_array=[
                        'certificate_id' => $data['certificate']->id,
                        'certificate_file_path' => asset($data['certificate']->path),
                        'certificate_file_extension' => pathinfo(public_path($data['certificate']->path), PATHINFO_EXTENSION),
                    ];
                }
                else{
                    // $certificate_tab_array=[
                    //     __('After completing the course, you will receive a certificate.')
                    // ];
                }
                //////////////////////////CERTIFICATE TAB SECTION END/////////////////////////////

                if($data['course']->course_type == 1 || $data['course']->course_type == 2){
                    $content_tab_array =$course_content_list_section;
                }
                else{
                    $content_tab_array =$course_content_list_section;
                }
               
                if($data['course']->course_type == 1 || $data['course']->course_type == 2){
                    $CourseType = __('General');
                    $result_array = [
                        'course_id' => $data['course']->id,
                        'course_title' => $data['course']->title,
                        'course_type' => $CourseType,
                        'lessons' => $data['course']->lessons->count(),
                        'lectures' => $data['course']->lectures->count(),
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        'course_content' => $content_tab_array,
                        'overview_tab' => $data['overview'],
                        'notice_tab' => $notice_tab_array,
                        'review_tab' => $review_tab_array,
                        // 'discussion_tab' => $discussion_tab_array,
                    ];
                }
                else{
                    $CourseType = __('Live');
               
                    $result_array = [
                        'course_id' => $data['course']->id,
                        'course_title' => $data['course']->title,
                        'course_type' => $CourseType,
                        'resources' => $data['course']->resources->count(),
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        'course_content' => $content_tab_array,
                        'overview_tab' => $data['overview'],
                        'notice_tab' => $notice_tab_array,
                        'review_tab' => $review_tab_array,
                        // 'discussion_tab' => $discussion_tab_array,
                        // 'navbar_header'=>$navbar_array,
                        // 'enrolled_students_image' => $enrolled_student_image_array,
                        // 'total_enrolled_students' => $total_enrolled_students,
                        // 'course_last_update' => $data['course']->updated_at->format('d M Y'),
                    
                        // 'course_content_tab' => $content_tab_array,
                 
                        // 'course_liveclass_tab' => $liveclass_tab_array,
                  
                        // // 'course_certificate_tab' => $certificate_tab_array,
                   
                    ];
                }
           
        return response()->json($result_array);
    } catch (\Throwable $th) {
        dd($th);
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
      
}




    public function myCourseShowApi(Request $request, $slug, $action_type = null, $quiz_uuid = null, $answer_id = null)
{
        try {
            //code...
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }
      
        $result_array = array();
        $action_type = $request->action_type;
        $quiz_uuid = $request->quiz_uuid;
        ///////////////////////////
        $data['pageTitle'] = "Course Details";
        $data['course'] = Course::with('publishedExams')->whereSlug($slug)->first();
        if($data['course'] == null){
            return response()->json(['message' => 'Given Course Slug Does Not Exists'], 302);
        }

        if($role == 2){
            //Current User Given Course Active Period Enrollment Info
            $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => auth()->id(), 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
     
            // End:: Checking enrolled or not
            // Current Given Course Total Student Enrollments Count
            $data['total_enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();
    
            // 4 Out of Total Enrollments Data For Current Given Course
            $data['enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->take(4)->get();

            if($data['enrollment']){
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $data['enrollment']->id),2);
            }
            else{
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, 0),2);
            }
        
      
            //  //Start:: Live Class
            //  $now = now();

            //  $data['upcoming_live_classes'] = LiveClass::whereCourseId($data['course']->id)
            //      ->where(function($q){    // Current Today Pending Live Classes
            //          $q->whereDate('date', now());
            //          $q->whereTime('time', '>', now());
            //      })
            //      ->orWhere(function($q){     // Upcomming Scheduled Live Classes
            //          $q->whereDate('date', '>', now());
            //      })
            //      ->latest()->get();
                        
            //  $data['current_live_classes'] = LiveClass::whereCourseId($data['course']->id)
            //      ->where(function($q){         //  Current Today Live Active Classes
            //          $q->whereDate('date', now());
            //          $q->whereTime('time', '<=', now());
            //          $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
            //      })
            //      ->latest()->get();
                        
            //  $data['past_live_classes'] = LiveClass::whereCourseId($data['course']->id)
            //  ->where(function($q){
            //          $q->whereDate('date', now());
            //          $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
            //      })
            //      ->orWhere(function($q){
            //          $q->whereDate('date', '<', now());
            //      })
            //      ->latest()->get();

     

            if (!is_null($action_type) && !is_null($quiz_uuid)) {
                $data['exam'] = Exam::whereUuid($quiz_uuid)->first();
                if ($action_type == 'start-quiz') { 
                    if (Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count() == 0) {
                        $take_exam = new Take_exam();
                        $take_exam->exam_id = $data['exam']->id;
                        $take_exam->save();
                    }
                    else{
                        $takeExam = Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                        $isPassed = $takeExam->isPassed;
                        if(!$isPassed){
                            $totalQuestions = Question::whereExamId($data['exam']->id)->count();
                            $attemptedAnswers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$takeExam->id)->count();
                            if($attemptedAnswers == $totalQuestions){
                                $take_exam = new Take_exam();
                                $take_exam->exam_id = $data['exam']->id;
                                $take_exam->save();
                            }
                        }
                    }
    
                    $data['take_exam'] = Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
    
                    // $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                    $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();
                    $data['question'] = Question::whereExamId($data['exam']->id)->whereNotIn('id', $question_ids)->first();
                    // $data['number_of_answer'] = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count();
                    $data['number_of_answer'] = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$data['take_exam']->id)->count();
    
    
                    $now = Carbon::now();
                    $expend_second = $now->diffInSeconds($data['take_exam']->created_at);
    
                    if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                        return redirect(route('student.my-course.show', [$data['course']->slug, 'quiz-result', $data['exam']->uuid]));
                    }
                }
              
                if ($action_type == 'leaderboard') {
                 
                    $data['top5_take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->take(5)->get();
                    $data['take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->skip(5)->take(500)->get();
                
                }
            }
         
            if (!is_null($answer_id)) {
                $data['answer'] = Answer::find($answer_id);
            }
            
            $data['action_type'] = $action_type;
            $data['quiz_uuid'] = $quiz_uuid;
            $data['answer_id'] = $answer_id;
    
            /** ------- save certificate ----------- */
    
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', $user->id)->get();
            $isFirst = count($data['course_lecture_views']) ? false : true;
            $data['isFirst'] = $isFirst;
         
            $Base_Route = route('student.my-course.show', [$data['course']->slug]);
            $data['BaseRoute'] = $Base_Route;

          
          
                ////////////////////////////////////////////////////////////////////////////////////////////////////////
            $navbar_array = array();
            $review_tab_array = array();
            $content_tab_array = array();
            $quiz_tab_array = array();
            $assignment_tab_array = array();
            $notice_tab_array = array();
            $liveclass_tab_array = array();
            $liveclass_tab_array = [
                'upcomming' => array(),
                'current' => array(),
                'past' => array(),
            ];
           
            $discussion_tab_array = array();
            $certificate_tab_array = array();
            $enrolled_student_image_array = array();
            $course_content_view_section = array();
            $content_details_array = array();
            $course_content_list_section = array();

            $data['course'] = Course::with('publishedExams')->whereSlug($slug)->firstOrfail();
               //Current User Given Course Active Period Enrollment Info
            $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
              //Current User Given Course Total Lecture Views
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', $user->id)->get();



              ////////////////////////////COURSE CATEGORY BASE RESOURCES FOR LIVE TYPE START//////////////////////////
            $resources_array = array();
            $category_ids_array = array();
            $course_id = $data['course']->id;
            $data['courseID'] = $course_id;
            $getCourseResources =  CourseResource::where('course_id',$course_id)->where('resource_id','<>',null)->get();
            if(!$getCourseResources->isEmpty()){
                foreach ($getCourseResources as $key => $course_resource) {
                    $resources_array[] = $course_resource->resource_id;
                }
            }
            $resources_array = array_unique($resources_array);
            if(!empty($resources_array)){
                $getResourceCategories = Resource::whereIn('id',$resources_array)->get();
                if(!$getResourceCategories->isEmpty()){
                    foreach ($getResourceCategories as $key => $resource) {
                        $category_ids_array[] = $resource->category_id;
                    }
                }
            }
            $result_data = array();
            $category_ids_array = array_unique($category_ids_array);
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            if(!$getCategories->isEmpty()){
                foreach ($getCategories as $key => $value) {
                    $resource_array = array();
                    $categoryID = $value->id;
                    $resources = CourseResource::where('course_id',$course_id)
                    ->whereHas('resource', function ($query) use ($categoryID) {
                        $query->where('category_id', $categoryID);
                    })->get();
                    if(!$resources->isEmpty()){
                        foreach ($resources as $key => $value2) {
                            if($value2->resource?->type == 'Video' || $value2->resource?->type == 'video'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Youtube' || $value2->resource?->type == 'youtube'){
                                if (Str::contains($value2->resource?->youtube_url_path, 'https://www.youtube.com')) {
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else if(Str::contains($value2->resource?->youtube_url_path, 'https://youtu.be')){
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else{
                                     $preview_src = "https://www.youtube.com/embed/".$value2->resource?->youtube_url_path;
                                 }
                            
                            }
                            elseif($value2->resource?->type == 'Text' || $value2->resource?->type == 'text'){
                                $preview_src =  $value2->resource?->text;
                            }
                            elseif($value2->resource?->type == 'Image' || $value2->resource?->type == 'image'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'PDF' || $value2->resource?->type == 'pdf'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Slide Document' || $value2->resource?->type == 'slide document'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name.'/index.html';
                            }
                            elseif($value2->resource?->type == 'Audio' || $value2->resource?->type == 'audio'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            else{
                                $preview_src = null;
                            }
                            $resource_array[] = [
                                'resource_id'=>$value2->resource->id,
                                'resource_name'=>$value2->resource->name,
                                'resource_ext'=>$value2->resource->file_extension,
                                'resource_path'=>$value2->resource->file_name,
                                'resource_type'=>$value2->resource->type,
                                'resource_preview_src'=>$preview_src,
                            ];
                        }
                    }
                    $result_data[] = [
                        'category_id' => $categoryID,
                        'category_name' => $value->name,
                        'category_resources' => $resource_array,
                    ];
                }
            }
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            $data['categories'] = $getCategories;
            $data['categories_with_resources'] = $result_data;
            ////////////////////////////COURSE CATEGORY BASE RESOURCES FOR LIVE TYPE END//////////////////////////

              ////////////////////////////COURSE CONTENT RIGHT SIDE SECTION START//////////////////////////
            if ($request->get('lecture_uuid')) {
                $lecture = Course_lecture::where('uuid', $request->get('lecture_uuid'))->firstOrFail(); //Get Lecture Data Through Given Lecture UUID    
                $isFirst = count($data['course_lecture_views']) ? false : true;  // Count Current User Total Views For Lectures OF gIVEN Course
            
            
                if(!checkStudentCourseIsLock( $data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $isFirst)){
              
                    $nextLecture = $this->getNextId($data['course_lecture_views'], $data['course'],  $lecture, $data['enrollment']);
               
                    if ($nextLecture) {
                        $data['nextLectureUuid'] = $nextLecture->uuid;
                    } else {
                        $data['nextLectureUuid'] = null;
                    }
               
                    if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        $data['video_src'] =  $lecture->resource?->file_name;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $data['youtube_video_src'] = $lecture->resource?->youtube_url_path;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    } 
                    elseif ($lecture->type == 'vimeo') {
                        $data['vimeo_video_src'] = $lecture->url_path;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                            }
                        }
    
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        $data['text_src'] = $lecture->resource?->text;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                        $lecture = Course_lecture::find($lecture->id);
                   
                        if ($lecture) {
                         
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                              
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                             
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        // $data['image_src'] = $lecture->image;
                        $data['image_src'] =  $lecture->resource?->file_name;
                    
                    } 
                    elseif ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $data['pdf_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        // return redirect(getImageFile($pdf_src));
                    } 
                    elseif ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $data['slide_document_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $data['audio_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    }
                    elseif($lecture->quiz){
                        $data['exam'] = Exam::whereUuid($lecture->quiz->uuid)->first();
                        if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() > 0) {
                            $take_exam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                            $take_exam->lecture_id = $lecture->id;
                           
                        
                            $answered_question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                            $total_questions = Question::whereExamId($data['exam']->id)->count();
                            // $number_of_attempted_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count();
                            $number_of_attempted_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$take_exam->id)->count();
        
                            if($number_of_attempted_answers == $total_questions){  // USER Attempted All Question Before EndTime
                                if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                                    //NEW CODE
                                    if($lecture->quiz){
                                        $quizPassedPercentage = $lecture->quiz->passing_percentage;
                                        $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                                        ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                        $cal_percentage = ($correct_answers/$total_questions)*100;
                                        $cal_percentage = (int)$cal_percentage;
                                        if($cal_percentage >= $quizPassedPercentage){
                                            $lecture = Course_lecture::find($lecture->id);
                                            if ($lecture) {
                                                if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                                    if($data['enrollment']){
                                                        $enrollment_id = $data['enrollment']->id;
                                                    }
                                                    else{
                                                        $enrollment_id = null;
                                                    }
                                                    $course_lecture_views = new Course_lecture_views();
                                                    $course_lecture_views->course_id = $lecture->course_id;
                                                    $course_lecture_views->course_lecture_id = $lecture->id;
                                                    $course_lecture_views->enrollment_id = $enrollment_id;
                                                    $course_lecture_views->save();
                                                }
                                                $take_exam->obtained_percentage = $cal_percentage;
                                                $take_exam->passing_percentage = $quizPassedPercentage;
                                                $take_exam->passed = 1;
                                                $take_exam->update();
                                            }
                                        }
                                        else{
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 0;
                                            $take_exam->update();
                                        }
                                    }
                                 
                                }
                            }
                            else{  // Quiz Time Is Ended
                                $now = Carbon::now();
                                $expend_second = $now->diffInSeconds($take_exam->created_at);
                                if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                                    if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                                        //NEW CODE
                                        if($lecture->quiz){
                                            $quizPassedPercentage = $lecture->quiz->passing_percentage;
                                            $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                                            ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                            $cal_percentage = ($correct_answers/$total_questions)*100;
                                            $cal_percentage = (int)$cal_percentage;
                                        }
                                        if($cal_percentage >= $quizPassedPercentage){
                                            $lecture = Course_lecture::find($lecture->id);
                                            if ($lecture) {
                                            
                                                if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                                    if($data['enrollment']){
                                                        $enrollment_id = $data['enrollment']->id;
                                                    }
                                                    else{
                                                        $enrollment_id = null;
                                                    }
                                                    $course_lecture_views = new Course_lecture_views();
                                                    $course_lecture_views->course_id = $lecture->course_id;
                                                    $course_lecture_views->course_lecture_id = $lecture->id;
                                                    $course_lecture_views->enrollment_id = $enrollment_id;
                                                    $course_lecture_views->save();
                                                 
                                                }
                                                $take_exam->obtained_percentage = $cal_percentage;
                                                $take_exam->passing_percentage = $quizPassedPercentage;
                                                $take_exam->passed = 1;
                                                $take_exam->update();
                                            }
                                        }
                                        else{
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 0;
                                            $take_exam->update();
                                        }   
                                }
                                }
                            } 
                            $take_exam->update();
                        }   
                    }
                    
                    // $data['lecture_type'] = $lecture->type;
                    if($lecture->resource){
                        $data['lecture_type'] = $lecture->resource?->category?->name;
                    }
                    elseif($lecture->quiz){
                        $data['lecture_type'] = $lecture->type;
                    }
                    elseif($lecture->assignment){
                        $data['lecture_type'] = $lecture->type;
                    }
                 
                    $data['lesson_id_check'] = @$lecture->lesson->id;
                    $data['lecture_id_check'] = $lecture->id;
                    $data['navLessonActive'] = 'on';
                    $data['subNavLectureActiveClass'] = 'show';
                    $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', auth()->id())->get();
                }
            }
            else{
                $data['lecture_type'] = null;
                $data['lesson_id_check'] = null;
                $data['lecture_id_check'] = null;
                $data['navLessonActive'] = 'off';
                $data['subNavLectureActiveClass'] = '';
            }
          
            if($data['course']->course_type == COURSE_TYPE_GENERAL){
                if($data['course']->lectures->count() > 0){
                    $course_video_duration = $data['course']->video_duration;
                }
                else{
                    $course_video_duration = '0 min';
                }

                if($data['lecture_type']){
                    if($data['lecture_type'] == 'Youtube' || $data['lecture_type'] == 'youtube'){
                        $content_details_array = [
                            // 'youtube_video_src' => "https://www.youtube.com/embed/".$data['youtube_video_src'],
                            'preview_src' => "https://www.youtube.com/embed/".$data['youtube_video_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Video' || $data['lecture_type'] == 'video'){
                        $content_details_array = [
                            // 'video_src' => asset('uploads/course_resource').'/'.$data['video_src'],
                            'preview_src' => asset('uploads/course_resource').'/'.$data['video_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Text' || $data['lecture_type'] == 'text'){
                        $content_details_array = [
                            'preview_src' => $data['text_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Image' || $data['lecture_type'] == 'image'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['image_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'PDF' || $data['lecture_type'] == 'pdf'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['pdf_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Slide Document' || $data['lecture_type'] == 'slide document'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['slide_document_src'].'/index.html',
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Audio' || $data['lecture_type'] == 'audio'){
                        $content_details_array = [
                            'preview_src' => asset('uploads/course_resource').'/'.$data['audio_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                }
                else{
                    if($data['course']->intro_video_check == 1 && getVideoFile($data['course']->video)){
                        $content_details_array = [
                            // 'course_image_poster' => getImageFile($data['course']->image),
                            'preview_src' => getVideoFile($data['course']->video),
                        ];
                        $course_content_view_section = [
                            'content_type' => __('Course Intro Video'),
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['course']->intro_video_check == 2 && $data['course']->youtube_video_id){
                        if (Str::contains($data['course']->youtube_video_id, 'https://www.youtube.com')) {
                            $content_details_array = [
                                // 'youtube_video_src' => $data['course']->youtube_video_id,
                                'preview_src' => $data['course']->youtube_video_id,
                            ];
                        }
                        else if(Str::contains($data['course']->youtube_video_id, 'https://youtu.be')){
                            $content_details_array = [
                                // 'youtube_video_src' => $data['course']->youtube_video_id,
                                'preview_src' => $data['course']->youtube_video_id,
                            ];
                        }
                        else{
                            $content_details_array = [
                                // 'youtube_video_src' => "https://www.youtube.com/embed/".$data['course']->youtube_video_id,
                                'preview_src' => "https://www.youtube.com/embed/".$data['course']->youtube_video_id,
                            ];
                        } 
                      
                        $course_content_view_section = [
                            'content_type' => __('Course Intro Youtube Video'),
                            'content_details' => $content_details_array,
                        ];
                    }
                    else{
                        $content_details_array = [
                            'preview_src' => $data['course']->cover_image ? getImageFile($data['course']->cover_image) : getImageFile($data['course']->image),
                        ];
                        $course_content_view_section = [
                            'content_type' => __('Course Image'),
                            'content_details' => $content_details_array,
                        ];
                    }
                }

              

                $lockCount = 0;
                $outerLoopKey = 0;
                $innerLoopKey = 0;
                foreach ($data['course']->lessons as $key => $lesson) {
                    $lesson_lectures_array = array();
                    if($key == 0){
                        $outerLoopKey = 1;
                    }
                    else{
                        $outerLoopKey = 0;
                    }
                    foreach ($lesson->lectures as $key2 => $lecture) {
                        if($key2 == 0){
                            $innerLoopKey = 1;
                        }
                        else{
                            $innerLoopKey = 0;
                        }

                        if($innerLoopKey && $outerLoopKey){
                            $loopFirst = True;
                        }
                        else{
                            $loopFirst = False;
                        }
                        // $lock = checkStudentCourseIsLock($data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $loop->first);
                        $lock = checkStudentCourseIsLock($data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $innerLoopKey);
                        if(checkStudentCourseView($data['course_lecture_views'], $data['course']->id, $lecture->id)){
                            $class1 = 'watchFinishedCourse';
                            $data_icon = 'ant-design:check-circle-filled';
                        } 
                        else if($lecture->id == $data['lecture_id_check']){
                            $class1 = 'watchContinuingCourse';
                            $data_icon = 'fluent:radio-button-24-filled';
                        } 
                        else{
                            $class1 = 'watchNewLecture';
                            $data_icon = 'fluent:radio-button-24-regular';
                        }
                        if($lecture->quiz_id != null){
                            $lectureType = 'Quiz';
                        }
                        else if($lecture->assignment_id != null){
                            $lectureType = 'Assignment';
                        }
                        else if($lecture->resource_id != null){
                            $lectureType = 'Resource';
                        }
                        else{
                            $lectureType = $lecture->type;
                        }
                            $isLocked = 'No';
                            if($lectureType == 'Resource'){
                                if ($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube'){
                                    $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource->id , 'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                                else{
                                    $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                            }
                            elseif($lectureType == 'Quiz'){
                                if(checkStudentCourseView($data['course_lecture_views'],
                                                                        $data['course']->id,
                                                                        $lecture->id)){
                                $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]);
                                $onClickRoute = route('click-lecture-course');
                                $onClickRouteName = __('(Student) Click Lecture');
                                }
                                else{
                                    if(check_take_attempt_exam($lecture->quiz_id) == 'no'){
                                        $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'start-quiz', $lecture->quiz?->uuid]); 
                                        $onClickRoute = route('click-lecture-course');  
                                        $onClickRouteName = __('(Student) Click Lecture');
                                    }
                                    else{
                                        $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]); 
                                        $onClickRoute = route('click-lecture-course');
                                        $onClickRouteName = __('(Student) Click Lecture');
                                    }
                                }
                            }
                            elseif($lectureType == 'Assignment'){
                                if(checkStudentCourseView($data['course_lecture_views'],
                                $data['course']->id,
                                $lecture->id)){
                                    $redirectURLRoute = route('student.assignment-result');
                                    $onClickRoute = route('assignment-result-course');
                                    $onClickRouteName = __('(Student) Assignment Detail');
                                }
                                else{
                                    $redirectURLRoute = route('student.assignment-details'); 
                                    $onClickRoute = route('assignment-detail-course');
                                    $onClickRouteName = __('(Student) Assignment Result');
                                }
                            }
                            else{
                                //NEW CASE IN WHICH ALL 3 QUIZ,ASSIGNMENT AND RESOURCE_ID ARE NULL AND LECTURE ARE WITHIN IT SELF TABLE
                                // LIKE IMAGE,AUDIO,VIDEO, YOUTUBE, SLIDE ETC
                                if ($lecture->type == 'text' || $lecture->type == 'Text' || $lecture->type == 'Youtube' || $lecture->type == 'youtube'){
                                    // $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource?->id , 'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                                else{
                                    // $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                    $onClickRouteName = __('(Student) Click Lecture');
                                }
                            }

                            if($lectureType == 'Assignment'){
                            }
                            elseif($lectureType == 'Quiz'){
                            }
                            else if($lectureType == 'Resource'){
                                if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                    $previewType = $lecture->resource?->type;
                                }
                                else if ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                                      $previewType = $lecture->resource?->type;
                                    if (Str::contains($lecture->resource?->youtube_url_path, 'https://www.youtube.com')) {
                                        $previewSrc = $lecture->resource?->youtube_url_path;    
                                    }
                                    else if(Str::contains($lecture->resource?->youtube_url_path, 'https://youtu.be')){
                                        $previewSrc = $lecture->resource?->youtube_url_path;    
                                    }
                                    else{
                                        $previewSrc = "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
                                    }
                                }
                                else if ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
                                      $previewType = $lecture->resource?->type;
                                    // $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                    $previewSrc = $lecture->resource?->text;
                                }
                                else if ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                }
                                else if ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                }
                                else if ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name.'/index.html';
                                }
                                else if ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                                      $previewType = $lecture->resource?->type;
                                    $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                                }
                                $lesson_lectures_array[]=[
                                    'sr_no'=>$key2+1,
                                    'name'=>$lecture->title,
                                    // 'type'=>$lectureType,
                                    'type'=> $previewType,
                                    'preview_src'=>$previewSrc,
                               ]; 
                            }
                            else{
                                if ($lecture->type == 'Video' || $lecture->type == 'video') {
                                     $previewSrc = asset($lecture->file_path);
                                }
                                else if ($lecture->type == 'Youtube' || $lecture->type == 'youtube') {
                                     $previewSrc = $lecture->url_path;
                                }
                                else if ($lecture->type == 'Image' || $lecture->type == 'image') {
                                     $previewSrc = asset($lecture->image);
                                }
                                else if ($lecture->type == 'Image' || $lecture->type == 'image') {
                                     $previewSrc = asset($lecture->image);
                                }
                                elseif ($lecture->type == 'PDF' || $lecture->type == 'pdf') {
                                      $previewSrc = asset($lecture->pdf);
                                }
                                else if ($lecture->type == 'Slide Document' || $lecture->type == 'slide_document') {
                                     $previewSrc = $lecture->slide_document;
                                }
                                else if ($lecture->type == 'Audio' || $lecture->type == 'audio') {
                                     $previewSrc = asset($lecture->audio);
                                }
                                else if ($lecture->type == 'Text' || $lecture->type == 'text') {
                                    $previewSrc = $lecture->text;
                                }
                                $lesson_lectures_array[]=[
                                    'sr_no'=>$key2+1,
                                    'name'=>$lecture->title,
                                    'type'=>$lecture->type,
                                    'preview_src'=>$previewSrc,
                               ]; 
                            }
                    }
                    $course_content_list_section[]=[
                        'sr_no' => $key+1,
                        'name' => $lesson->name,
                        'data'=> $lesson_lectures_array,
                    ];
                }
            }
            elseif($data['course']->course_type == COURSE_TYPE_SCORM){
                if($data['course']->scorm_course->duration){
                    $course_video_duration = $data['course']->scorm_course->duration;
                }
                else{
                    $course_video_duration = '0 min';
                }

                if ($data['course']->scorm_course){
                    $content_details_array = [
                        'scorm_src' => asset("scorm/".$data['course']->scorm_course?->uuid.'/'.$course->scorm_course?->entry_url),
                    ];
                    $course_content_view_section = [
                        'content_type' => __('Scorm Course Content'),
                        'content_details' => $content_details_array,
                    ];
                }
            }
            else{
                $course_video_duration = null;

                foreach ($data['categories_with_resources'] as $key => $category) {
                    $category_resources_array = array();
                    foreach ($category['category_resources'] as $key2=> $resource) {
                        if ($resource['resource_type'] == 'Text' || $resource['resource_type'] == 'Youtube'){
                            // $redirectURL = route('student.resource.view_full_other', ['resource_id'=>$resource['resource_id']]);
                            $redirectSRC =  $resource['resource_preview_src']; 
                        }
                        else{
                            // $redirectURL = route('student.resource.view_full', ['ext'=>$resource['resource_ext'] , 'path'=> $resource['resource_path']]);
                            $redirectSRC =  $resource['resource_preview_src'];
                        }
                        $category_resources_array[] = [
                            // 'resource_id' => $resource['resource_id'],
                            'sr_no' => $key2 + 1,
                            'name' => $resource['resource_name'],
                            'type' => $resource['resource_type'],
                            'preview_src' => $redirectSRC,
                            // 'redirect_url' => $redirectURL,
                        ];
                    }
                    $course_content_list_section[]=[
                        'sr_no' => $key+1,
                        'name' => $category['category_name'],
                        'data' => $category_resources_array,
                    ];
                }
            }

              if($data['course']->intro_video_check == 1 && getVideoFile($data['course']->video)){
                    $featured_image_or_video = getVideoFile($data['course']->video);
                    $featured_image_or_video_type= 1;
             }
                else if($data['course']->intro_video_check == 2 && $data['course']->youtube_video_id){
                    if (Str::contains($data['course']->youtube_video_id, 'https://www.youtube.com')) {
                        $featured_image_or_video =  $data['course']->youtube_video_id;
                    }
                    else if(Str::contains($data['course']->youtube_video_id, 'https://youtu.be')){
                        $featured_image_or_video =  $data['course']->youtube_video_id;
                    }
                    else{
                        $featured_image_or_video = "https://www.youtube.com/embed/".$data['course']->youtube_video_id;
                    }
                     $featured_image_or_video_type= 2;
                }
                else{
                       $featured_image_or_video =  $data['course']->cover_image ? getImageFile($data['course']->cover_image) : getImageFile($data['course']->image);
                        $featured_image_or_video_type= 3;
                }


            if (!is_null($action_type) && !is_null($quiz_uuid)) {
                $navbar_array = [
                    'course_title' =>$data['course']->title,
                    'return_url' =>route('student.my-course.show',['slug'=>$data['course']->slug]),
                    'total_course_lectures' =>$data['course']->lectures->count(),
                    'total_course_lessons' =>$data['course']->lessons->count(),
                    'total_course_video_duration' =>$course_video_duration,
                ];
            }
            else{
                $navbar_array = [
                    'course_title' =>$data['course']->title,
                    'total_course_lectures' =>$data['course']->lectures->count(),
                    'total_course_lessons' =>$data['course']->lessons->count(),
                    'total_course_video_duration' =>$course_video_duration,
                ];
            }
             ////////////////////////////COURSE CONTENT RIGHT SIDE SECTION END//////////////////////////

            $total_enrolled_students = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();
            $enrolled_students = Enrollment::where('course_id', $data['course']->id)->take(4)->get();
            foreach ($enrolled_students as $key => $enroll) {
               $enrolled_student_image_array[] = getImageFile($enroll->user?->image_path);
            }

            if($data['enrollment']){
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $data['enrollment']->id),2);
            }
            else{
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, 0),2);
            }

            ////////////////////////////OVERVIEW TAB SECTION START//////////////////////////
            $overview = array();
            if($data['course']){
                if(count($data['course']->keyPoints) > 0){
                    $overview = [
                        'keyPoints' => $data['course']->keyPoints,
                        'description' => $data['course']->description,
                    ];
                }
                else{
                    $overview = [
                        'keyPoints' => array(),
                        'description' => $data['course']->description,
                    ];
                }
            }
            $data['overview'] = $overview;
            //////////////////////////OVERVIEW TAB SECTION END/////////////////////////////




            $liveclass_tab_array = [
                'upcomming' => array(),
                'current' => array(),
                'past' => array(),
                'footer_section' => [
                    'enrolled_students_image' => $enrolled_student_image_array,
                    'total_enrolled_students' => $total_enrolled_students,
                    'course_last_update' => $data['course']->updated_at->format('d M Y'),
                ],
            ];



            //////////////////////////REVIEW TAB SECTION START/////////////////////////////
            $data['reviews'] = Review::whereCourseId($data['course']->id)->latest()->paginate(3);
            $data['loadMoreShowButtonReviews'] = Review::whereCourseId($data['course']->id)->paginate(4);
            $data['five_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(5)->count();
            $data['four_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(4)->count();
            $data['three_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(3)->count();
            $data['two_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(2)->count();
            $data['first_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(1)->count();
    
            $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
                (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
            $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];
        
            if ($data['total_user_review'] > 0) {
                $data['average_rating'] = $data['total_reviews'] / $data['total_user_review'];
            } else {
                $data['average_rating'] = 0;
            }
          
            $total_reviews = Review::whereCourseId($data['course']->id)->count();
    
            if ($total_reviews > 0) {
                $data['five_star_percentage'] = ($data['five_star_count'] / $total_reviews) * 100;
    
                $data['four_star_percentage'] = 100 * ($data['four_star_count'] / $total_reviews);
                $data['three_star_percentage'] = 100 * ($data['three_star_count'] / $total_reviews);
                $data['two_star_percentage'] = 100 * ($data['two_star_count'] / $total_reviews);
                $data['first_star_percentage'] = 100 * ($data['first_star_count'] / $total_reviews);
            } else {
                $data['five_star_percentage'] = 0;
                $data['four_star_percentage'] = 0;
                $data['three_star_percentage'] = 0;
                $data['two_star_percentage'] = 0;
                $data['first_star_percentage'] = 0;
            }

            if($data['course']->average_rating >= 1){

            }
            $top_section_review_array = [
                // 'average_rating'=> number_format($data['course']->average_rating, 1),
                'average_rating'=> number_format($data['average_rating'], 1),
                'total_reviews'=> $data['total_user_review'],
            ];
        
            $progress_bars_section_review_array = [
                'five_star_percentage'=> $data['five_star_percentage'],
                'five_star_count'=> $data['five_star_count'],
                'four_star_percentage'=> $data['four_star_percentage'],
                'four_star_count'=> $data['four_star_count'],
                'three_star_percentage'=> $data['three_star_percentage'],
                'three_star_count'=> $data['three_star_count'],
                'two_star_percentage'=> $data['two_star_percentage'],
                'two_star_count'=> $data['two_star_count'],
                'first_star_percentage'=> $data['first_star_percentage'],
                'first_star_count'=> $data['first_star_count'],
            ];
            if(!$data['reviews']->isEmpty()){
                $customerReviewsArray = array();
                foreach ($data['reviews'] as $key => $cust_review) {
                    $customerReviewsArray[]=[
                        'user_id' => $cust_review->user?->id,
                        'user_name' => $cust_review->user?->name,
                        'user_image' => asset($cust_review->user?->image_path),
                        'review' => $cust_review->comment,
                        'rating' => $cust_review->rating,      
                        'submission_at' => $cust_review->created_at->diffForHumans(),
                    
                    ];
                }
            }
            else{
                $customerReviewsArray = array();
            }
            $review_tab_array =[
                // 'top-section'=>$top_section_review_array,
                // 'progress-bars-section'=>$progress_bars_section_review_array,
                // 'customers-reviews-section'=>$customerReviewsArray,

                // 'average_rating' => number_format($course->average_rating, 1),
                'total_reviews'=> $data['total_user_review'],
                // 'average_rating'=> number_format($data['average_rating'], 1),
                'five_star_percentage'=> (int)$data['five_star_percentage'],
                'four_star_percentage' => (int)$data['four_star_percentage'],
                'three_star_percentage' => (int)$data['three_star_percentage'],
                'two_star_percentage' => (int)$data['two_star_percentage'],
                'first_star_percentage' => (int)$data['first_star_percentage'],
                'user_reviews' => $customerReviewsArray,
            ];
            //////////////////////////////REVIEW TAB SECTION END //////////////////////////////////////

            /////////////////////////////QUIZ TAB SECTION START////////////////////////////////////////
            $data['quiz'] = $data['course']->publishedExams;
            if($data['quiz']->count() > 0){
                foreach ($data['quiz'] as $key => $quiz) {
                    if(take_exam($quiz->id) == 'no'){
                        $action_tab_api_routes = [
                            // 'start_quiz' => route('start-quiz-course'),
                            // 'leaderboard' => route('quiz-leaderboard-course')
                            'action_btn_1_api_route' => route('start-quiz-course'),
                            'action_btn_1_api_name' => __('(Student)Start Quiz'),
                            'action_btn_2_api_route' => route('quiz-leaderboard-course'),
                            'action_btn_2_api_name' => __('(Student) Quiz LeaderBoard')
                           
                        ];
                    }
                    else{
                        $action_tab_api_routes = [
                            // 'see_result' => route('quiz-result-course'),
                            // 'leaderboard' => route('quiz-leaderboard-course')
                            'action_btn_1_api_route' => route('quiz-result-course'),
                            'action_btn_1_api_name' => __('(Student)Complete Quiz Result'),
                            'action_btn_2_api_route' => route('quiz-leaderboard-course'),
                            'action_btn_2_api_name' => __('(Student) Quiz LeaderBoard')
                        ];
                    }
                  
                    $quiz_tab_array[] = [
                        'quiz_id' => $quiz->id,
                        'quiz_uuid' => $quiz->uuid,
                        'quiz_name' => $quiz->name,
                        'quiz_type' => ucfirst(str_replace("_", " ", $quiz->type)),
                        'total_questions' => $quiz->questions->count(),
                        'time_duration' => $quiz->duration.__(' minutes'),
                        'action_api_routes' => $action_tab_api_routes,
                    ];
                }
            }
            /////////////////////////////QUIZ TAB SECTION END//////////////////////////////////////////


            ////////////////////////////ASSIGNMENT TAB SECTION START///////////////////////////////////
            $data['assignments'] = Assignment::where('course_id', $data['course']->id)->get();
            if(count($data['assignments'])){
                foreach ($data['assignments'] as $key => $assignment) {
                    $action_btn_routes = [
                        'view_details_api_route'=> route('assignment-detail-course'),
                        'view_details_api_name'=> __('(Student) Assignment Detail'),
                        'see_result_api_route'=> route('assignment-result-course'),
                        'see_result_api_name'=> __('(Student) Assignment Result'),
                    ];
                    $assignment_tab_array[] = [
                        'assignment_id' => $assignment->id,
                        'assignment_topic' => Str::limit($assignment->name, 50),
                        'assignment_total_marks' => $assignment->marks,
                        'action_btn_api_routes'=> $action_btn_routes,
                    ];
                }
            }
            else{
                // $assignment_tab_array = [
                //     __('Empty Assignments')
                // ];
            }
            ////////////////////////////ASSIGNMENT TAB SECTION END/////////////////////////////////////


            //////////////////////////NOTICE TAB SECTION START/////////////////////////////
            $data['notices'] = NoticeBoard::whereCourseId($data['course']->id)->latest()->get();
            if(count($data['notices'])){
                foreach ($data['notices'] as $key => $notice) {
                    $notice_tab_array[]=[
                        'notice_id'=>$notice->id,
                        'created_at'=>$notice->created_at->format('d - m - Y'),
                        'notice_topic'=>$notice->topic,
                        'notice_details'=>$notice->details,
                       
                    ];
                }
               
            }
            //////////////////////////NOTICE TAB SECTION END/////////////////////////////

             //////////////////////////DISCUSSION TAB SECTION START/////////////////////////////
             $data['discussions'] = Discussion::whereCourseId($data['course']->id)->whereNull('parent_id')->active()->get();
             if(count($data['discussions']) > 0){
                $userID = Auth::id();
                if($userID){
                   $getUser = User::where('id',$userID)->first();
                   $Image =  getImageFile($getUser->image_path);
                   $auth_user_images = [
                        'discussion_form_user_image' => $Image,
                        'reply_form_user_image' => $Image,
                   ];
                }
                else{
                    $auth_user_images = [
                        'discussion_form_user_image' => null,
                        'reply_form_user_image' => null,
                   ];
                }
                 foreach ($data['discussions'] as $key => $discussion) {
                    // if($key == 1){
                    //     dd($discussion_replies_array);
                    // }   
                    if($discussion->comment_as == 1){
                        $discussion_user_type = 'Instructor';
                    }
                    else{
                        $discussion_user_type = 'Student'; 
                    }
                   
                    if(!$discussion->replies->isEmpty()){
                        $discussion_replies_array = array();
                        foreach ($discussion->replies as $reply) {
                            if($reply->comment_as == 1){
                                $reply_user_type = 'Instructor';
                            }
                            else{
                                $reply_user_type = 'Student'; 
                            }
                            $discussion_replies_array[]=[
                                'reply_id'=>$reply->id,
                                'reply_user_id'=>$reply->user?->id,
                                'reply_user_image'=>getImageFile($reply->user?->image_path),
                                'reply_user_name'=>$reply->user?->name,
                                'reply_user_type'=>$reply_user_type,
                                'reply_comment'=>$reply->comment,
                                'reply_created_at'=>$reply->created_at->diffforhumans(),
                            ];
                        }
                    }
                    else{
                        $discussion_replies_array = []; 
                    }

                  
                     $discussion_tab_array[]=[
                         'discussion_id'=>$discussion->id,
                         'discussion_user_id'=>$discussion->user?->id,
                         'discussion_user_name'=>$discussion->user?->name,
                         'discussion_user_image'=>getImageFile($discussion->user?->image_path),
                         'discussion_user_type'=>$discussion_user_type,
                         'discussion_comment'=>$discussion->comment,
                         'discussion_created_at'=>$discussion->created_at->diffforhumans(),
                         'discussion_total_replies'=>count($discussion->replies),
                         'discussion_replies_list'=> $discussion_replies_array,
                       
                     ];
                 }
                 $discussion_tab_array[] = [
                    'auth_user_images'=> $auth_user_images,
                 ];
             }
             //////////////////////////DISCUSSION TAB SECTION END/////////////////////////////

                //////////////////////////CERTIFICATE TAB SECTION START/////////////////////////////
                $data['certificate'] = $data['course']->studentCertificate;
                if($data['certificate']){
                    $certificate_tab_array=[
                        'certificate_id' => $data['certificate']->id,
                        'certificate_file_path' => asset($data['certificate']->path),
                        'certificate_file_extension' => pathinfo(public_path($data['certificate']->path), PATHINFO_EXTENSION),
                    ];
                }
                else{
                    // $certificate_tab_array=[
                    //     __('After completing the course, you will receive a certificate.')
                    // ];
                }
                //////////////////////////CERTIFICATE TAB SECTION END/////////////////////////////

                if($data['course']->course_type == 1 || $data['course']->course_type == 2){
                    $content_tab_array =$course_content_list_section;
                }
                else{
                    $content_tab_array =$course_content_list_section;
                }
               
                if($data['course']->course_type == 1 || $data['course']->course_type == 2){
                    $CourseType = __('General');
                    $result_array = [
                        'course_id' => $data['course']->id,
                        'course_title' => $data['course']->title,
                        'course_type' => $CourseType,
                        'lessons' => $data['course']->lessons->count(),
                        'lectures' => $data['course']->lectures->count(),
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        'course_content' => $content_tab_array,
                        'overview_tab' => $data['overview'],
                        'notice_tab' => $notice_tab_array,
                        'review_tab' => $review_tab_array,
                        // 'discussion_tab' => $discussion_tab_array,
                    ];
                }
                else{
                    $CourseType = __('Live');
               
                    $result_array = [
                        'course_id' => $data['course']->id,
                        'course_title' => $data['course']->title,
                        'course_type' => $CourseType,
                        'resources' => $data['course']->resources->count(),
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        'course_content' => $content_tab_array,
                        'overview_tab' => $data['overview'],
                        'notice_tab' => $notice_tab_array,
                        'review_tab' => $review_tab_array,
                        // 'discussion_tab' => $discussion_tab_array,
                        // 'navbar_header'=>$navbar_array,
                        // 'enrolled_students_image' => $enrolled_student_image_array,
                        // 'total_enrolled_students' => $total_enrolled_students,
                        // 'course_last_update' => $data['course']->updated_at->format('d M Y'),
                    
                        // 'course_content_tab' => $content_tab_array,
                 
                        // 'course_liveclass_tab' => $liveclass_tab_array,
                  
                        // // 'course_certificate_tab' => $certificate_tab_array,
                   
                    ];
                }
           
            return response()->json($result_array);
        }
        else{
            //Current User Given Course Active Period Enrollment Info
            $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
     
            // End:: Checking enrolled or not
            // Current Given Course Total Student Enrollments Count
            $data['total_enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();
    
            // 4 Out of Total Enrollments Data For Current Given Course
            $data['enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->take(4)->get();

            if($data['enrollment']){
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $data['enrollment']->id),2);
            }
            else{
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, 0),2);
            }
        
      
      

     

            if (!is_null($action_type) && !is_null($quiz_uuid)) {
                $data['exam'] = Exam::whereUuid($quiz_uuid)->first();
                if ($action_type == 'start-quiz') { 
                    if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() == 0) {
                        $take_exam = new Take_exam();
                        $take_exam->exam_id = $data['exam']->id;
                        $take_exam->save();
                    }
                    else{
                        $takeExam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                        $isPassed = $takeExam->isPassed;
                        if(!$isPassed){
                            $totalQuestions = Question::whereExamId($data['exam']->id)->count();
                            $attemptedAnswers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$takeExam->id)->count();
                            if($attemptedAnswers == $totalQuestions){
                                $take_exam = new Take_exam();
                                $take_exam->exam_id = $data['exam']->id;
                                $take_exam->save();
                            }
                        }
                    }
    
                    $data['take_exam'] = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
    
                 
                    $question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();
                    $data['question'] = Question::whereExamId($data['exam']->id)->whereNotIn('id', $question_ids)->first();
                  
                    $data['number_of_answer'] = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$data['take_exam']->id)->count();
    
    
                    $now = Carbon::now();
                    $expend_second = $now->diffInSeconds($data['take_exam']->created_at);
    
                    if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                        return redirect(route('student.my-course.show', [$data['course']->slug, 'quiz-result', $data['exam']->uuid]));
                    }
                }
              
                if ($action_type == 'leaderboard') {
                 
                    $data['top5_take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->take(5)->get();
                    $data['take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->skip(5)->take(500)->get();
                
                }
            }
         
            if (!is_null($answer_id)) {
                $data['answer'] = Answer::find($answer_id);
            }
            
            $data['action_type'] = $action_type;
            $data['quiz_uuid'] = $quiz_uuid;
            $data['answer_id'] = $answer_id;
    
            /** ------- save certificate ----------- */
    
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', $user->id)->get();
            $isFirst = count($data['course_lecture_views']) ? false : true;
            $data['isFirst'] = $isFirst;
         
            $Base_Route = route('student.my-course.show', [$data['course']->slug]);
            $data['BaseRoute'] = $Base_Route;

          
          
                ////////////////////////////////////////////////////////////////////////////////////////////////////////
            $navbar_array = array();
            $review_tab_array = array();
            $content_tab_array = array();
            $quiz_tab_array = array();
            $assignment_tab_array = array();
            $notice_tab_array = array();
            $liveclass_tab_array = array();
            $liveclass_tab_array = [
                'upcomming' => array(),
                'current' => array(),
                'past' => array(),
            ];
           
            $discussion_tab_array = array();
            $certificate_tab_array = array();
            $enrolled_student_image_array = array();
            $course_content_view_section = array();
            $content_details_array = array();
            $course_content_list_section = array();

            $data['course'] = Course::with('publishedExams')->whereSlug($slug)->firstOrfail();
               //Current User Given Course Active Period Enrollment Info
            $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
              //Current User Given Course Total Lecture Views
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', $user->id)->get();



              ////////////////////////////COURSE CATEGORY BASE RESOURCES FOR LIVE TYPE START//////////////////////////
            $resources_array = array();
            $category_ids_array = array();
            $course_id = $data['course']->id;
            $data['courseID'] = $course_id;
            $getCourseResources =  CourseResource::where('course_id',$course_id)->where('resource_id','<>',null)->get();
            if(!$getCourseResources->isEmpty()){
                foreach ($getCourseResources as $key => $course_resource) {
                    $resources_array[] = $course_resource->resource_id;
                }
            }
            $resources_array = array_unique($resources_array);
            if(!empty($resources_array)){
                $getResourceCategories = Resource::whereIn('id',$resources_array)->get();
                if(!$getResourceCategories->isEmpty()){
                    foreach ($getResourceCategories as $key => $resource) {
                        $category_ids_array[] = $resource->category_id;
                    }
                }
            }
            $result_data = array();
            $category_ids_array = array_unique($category_ids_array);
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            if(!$getCategories->isEmpty()){
                foreach ($getCategories as $key => $value) {
                    $resource_array = array();
                    $categoryID = $value->id;
                    $resources = CourseResource::where('course_id',$course_id)
                    ->whereHas('resource', function ($query) use ($categoryID) {
                        $query->where('category_id', $categoryID);
                    })->get();
                    if(!$resources->isEmpty()){
                        foreach ($resources as $key => $value2) {
                            if($value2->resource?->type == 'Video' || $value2->resource?->type == 'video'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Youtube' || $value2->resource?->type == 'youtube'){
                                if (Str::contains($value2->resource?->youtube_url_path, 'https://www.youtube.com')) {
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else if(Str::contains($value2->resource?->youtube_url_path, 'https://youtu.be')){
                                     $preview_src = $value2->resource?->youtube_url_path;    
                                 }
                                 else{
                                     $preview_src = "https://www.youtube.com/embed/".$value2->resource?->youtube_url_path;
                                 }
                            
                            }
                            elseif($value2->resource?->type == 'Text' || $value2->resource?->type == 'text'){
                                $preview_src =  $value2->resource?->text;
                            }
                            elseif($value2->resource?->type == 'Image' || $value2->resource?->type == 'image'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'PDF' || $value2->resource?->type == 'pdf'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            elseif($value2->resource?->type == 'Slide Document' || $value2->resource?->type == 'slide document'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name.'/index.html';
                            }
                            elseif($value2->resource?->type == 'Audio' || $value2->resource?->type == 'audio'){
                                $preview_src =  asset('uploads/course_resource').'/'.$value2->resource?->file_name;
                            }
                            else{
                                $preview_src = null;
                            }
                            $resource_array[] = [
                                'resource_id'=>$value2->resource->id,
                                'resource_name'=>$value2->resource->name,
                                'resource_ext'=>$value2->resource->file_extension,
                                'resource_path'=>$value2->resource->file_name,
                                'resource_type'=>$value2->resource->type,
                                'resource_preview_src'=>$preview_src,
                            ];
                        }
                    }
                    $result_data[] = [
                        'category_id' => $categoryID,
                        'category_name' => $value->name,
                        'category_resources' => $resource_array,
                    ];
                }
            }
            $getCategories = ResourceCategory::whereIn('id',$category_ids_array)->get();
            $data['categories'] = $getCategories;
            $data['categories_with_resources'] = $result_data;
            ////////////////////////////COURSE CATEGORY BASE RESOURCES FOR LIVE TYPE END//////////////////////////

              ////////////////////////////COURSE CONTENT RIGHT SIDE SECTION START//////////////////////////
            if ($request->get('lecture_uuid')) {
                $lecture = Course_lecture::where('uuid', $request->get('lecture_uuid'))->firstOrFail(); //Get Lecture Data Through Given Lecture UUID    
                $isFirst = count($data['course_lecture_views']) ? false : true;  // Count Current User Total Views For Lectures OF gIVEN Course
            
            
                if(!checkStudentCourseIsLock( $data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $isFirst)){
              
                    $nextLecture = $this->getNextId($data['course_lecture_views'], $data['course'],  $lecture, $data['enrollment']);
               
                    if ($nextLecture) {
                        $data['nextLectureUuid'] = $nextLecture->uuid;
                    } else {
                        $data['nextLectureUuid'] = null;
                    }
               
                    if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        $data['video_src'] =  $lecture->resource?->file_name;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $data['youtube_video_src'] = $lecture->resource?->youtube_url_path;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    } 
                    elseif ($lecture->type == 'vimeo') {
                        $data['vimeo_video_src'] = $lecture->url_path;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                            }
                        }
    
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        $data['text_src'] = $lecture->resource?->text;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                        $lecture = Course_lecture::find($lecture->id);
                   
                        if ($lecture) {
                         
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                              
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                             
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        // $data['image_src'] = $lecture->image;
                        $data['image_src'] =  $lecture->resource?->file_name;
                    
                    } 
                    elseif ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $data['pdf_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                        // return redirect(getImageFile($pdf_src));
                    } 
                    elseif ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
    
                        $data['slide_document_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    } 
                    elseif ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture) {
                        
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                if($data['enrollment']){
                                    $enrollment_id = $data['enrollment']->id;
                                }
                                else{
                                    $enrollment_id = null;
                                }
                                $course_lecture_views = new Course_lecture_views();
                                $course_lecture_views->course_id = $lecture->course_id;
                                $course_lecture_views->course_lecture_id = $lecture->id;
                                $course_lecture_views->enrollment_id = $enrollment_id;
                                $course_lecture_views->save();
                             
                            }
                        }
                        $data['audio_src'] = $lecture->resource?->file_name;
                        $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                        $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    }
                    elseif($lecture->quiz){
                        $data['exam'] = Exam::whereUuid($lecture->quiz->uuid)->first();
                        if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() > 0) {
                            $take_exam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                            $take_exam->lecture_id = $lecture->id;
                           
                        
                            $answered_question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                            $total_questions = Question::whereExamId($data['exam']->id)->count();
                            // $number_of_attempted_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count();
                            $number_of_attempted_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$take_exam->id)->count();
        
                            if($number_of_attempted_answers == $total_questions){  // USER Attempted All Question Before EndTime
                                if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                                    //NEW CODE
                                    if($lecture->quiz){
                                        $quizPassedPercentage = $lecture->quiz->passing_percentage;
                                        $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                                        ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                        $cal_percentage = ($correct_answers/$total_questions)*100;
                                        $cal_percentage = (int)$cal_percentage;
                                        if($cal_percentage >= $quizPassedPercentage){
                                            $lecture = Course_lecture::find($lecture->id);
                                            if ($lecture) {
                                                if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                                    if($data['enrollment']){
                                                        $enrollment_id = $data['enrollment']->id;
                                                    }
                                                    else{
                                                        $enrollment_id = null;
                                                    }
                                                    $course_lecture_views = new Course_lecture_views();
                                                    $course_lecture_views->course_id = $lecture->course_id;
                                                    $course_lecture_views->course_lecture_id = $lecture->id;
                                                    $course_lecture_views->enrollment_id = $enrollment_id;
                                                    $course_lecture_views->save();
                                                }
                                                $take_exam->obtained_percentage = $cal_percentage;
                                                $take_exam->passing_percentage = $quizPassedPercentage;
                                                $take_exam->passed = 1;
                                                $take_exam->update();
                                            }
                                        }
                                        else{
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 0;
                                            $take_exam->update();
                                        }
                                    }
                                 
                                }
                            }
                            else{  // Quiz Time Is Ended
                                $now = Carbon::now();
                                $expend_second = $now->diffInSeconds($take_exam->created_at);
                                if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                                    if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                                        //NEW CODE
                                        if($lecture->quiz){
                                            $quizPassedPercentage = $lecture->quiz->passing_percentage;
                                            $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                                            ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                            $cal_percentage = ($correct_answers/$total_questions)*100;
                                            $cal_percentage = (int)$cal_percentage;
                                        }
                                        if($cal_percentage >= $quizPassedPercentage){
                                            $lecture = Course_lecture::find($lecture->id);
                                            if ($lecture) {
                                            
                                                if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                                    if($data['enrollment']){
                                                        $enrollment_id = $data['enrollment']->id;
                                                    }
                                                    else{
                                                        $enrollment_id = null;
                                                    }
                                                    $course_lecture_views = new Course_lecture_views();
                                                    $course_lecture_views->course_id = $lecture->course_id;
                                                    $course_lecture_views->course_lecture_id = $lecture->id;
                                                    $course_lecture_views->enrollment_id = $enrollment_id;
                                                    $course_lecture_views->save();
                                                 
                                                }
                                                $take_exam->obtained_percentage = $cal_percentage;
                                                $take_exam->passing_percentage = $quizPassedPercentage;
                                                $take_exam->passed = 1;
                                                $take_exam->update();
                                            }
                                        }
                                        else{
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 0;
                                            $take_exam->update();
                                        }   
                                }
                                }
                            } 
                            $take_exam->update();
                        }   
                    }
                    
                    // $data['lecture_type'] = $lecture->type;
                    if($lecture->resource){
                        $data['lecture_type'] = $lecture->resource?->category?->name;
                    }
                    elseif($lecture->quiz){
                        $data['lecture_type'] = $lecture->type;
                    }
                    elseif($lecture->assignment){
                        $data['lecture_type'] = $lecture->type;
                    }
                 
                    $data['lesson_id_check'] = @$lecture->lesson->id;
                    $data['lecture_id_check'] = $lecture->id;
                    $data['navLessonActive'] = 'on';
                    $data['subNavLectureActiveClass'] = 'show';
                    $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', auth()->id())->get();
                }
            }
            else{
                $data['lecture_type'] = null;
                $data['lesson_id_check'] = null;
                $data['lecture_id_check'] = null;
                $data['navLessonActive'] = 'off';
                $data['subNavLectureActiveClass'] = '';
            }
          
            // if($data['course']->course_type == COURSE_TYPE_GENERAL){
            //     if($data['course']->lectures->count() > 0){
            //         $course_video_duration = $data['course']->video_duration;
            //     }
            //     else{
            //         $course_video_duration = '0 min';
            //     }

            //     if($data['lecture_type']){
            //         if($data['lecture_type'] == 'Youtube' || $data['lecture_type'] == 'youtube'){
            //             $content_details_array = [
            //                 // 'youtube_video_src' => "https://www.youtube.com/embed/".$data['youtube_video_src'],
            //                 'preview_src' => "https://www.youtube.com/embed/".$data['youtube_video_src'],
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => $data['lecture_type'],
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else if($data['lecture_type'] == 'Video' || $data['lecture_type'] == 'video'){
            //             $content_details_array = [
            //                 // 'video_src' => asset('uploads/course_resource').'/'.$data['video_src'],
            //                 'preview_src' => asset('uploads/course_resource').'/'.$data['video_src'],
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => $data['lecture_type'],
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else if($data['lecture_type'] == 'Text' || $data['lecture_type'] == 'text'){
            //             $content_details_array = [
            //                 'preview_src' => $data['text_src'],
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => $data['lecture_type'],
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else if($data['lecture_type'] == 'Image' || $data['lecture_type'] == 'image'){
            //             $content_details_array = [
            //                 'preview_src' => asset('uploads/course_resource').'/'.$data['image_src'],
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => $data['lecture_type'],
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else if($data['lecture_type'] == 'PDF' || $data['lecture_type'] == 'pdf'){
            //             $content_details_array = [
            //                 'preview_src' => asset('uploads/course_resource').'/'.$data['pdf_src'],
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => $data['lecture_type'],
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else if($data['lecture_type'] == 'Slide Document' || $data['lecture_type'] == 'slide document'){
            //             $content_details_array = [
            //                 'preview_src' => asset('uploads/course_resource').'/'.$data['slide_document_src'].'/index.html',
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => $data['lecture_type'],
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else if($data['lecture_type'] == 'Audio' || $data['lecture_type'] == 'audio'){
            //             $content_details_array = [
            //                 'preview_src' => asset('uploads/course_resource').'/'.$data['audio_src'],
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => $data['lecture_type'],
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //     }
            //     else{
            //         if($data['course']->intro_video_check == 1 && getVideoFile($data['course']->video)){
            //             $content_details_array = [
            //                 // 'course_image_poster' => getImageFile($data['course']->image),
            //                 'preview_src' => getVideoFile($data['course']->video),
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => __('Course Intro Video'),
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else if($data['course']->intro_video_check == 2 && $data['course']->youtube_video_id){
            //             if (Str::contains($data['course']->youtube_video_id, 'https://www.youtube.com')) {
            //                 $content_details_array = [
            //                     // 'youtube_video_src' => $data['course']->youtube_video_id,
            //                     'preview_src' => $data['course']->youtube_video_id,
            //                 ];
            //             }
            //             else if(Str::contains($data['course']->youtube_video_id, 'https://youtu.be')){
            //                 $content_details_array = [
            //                     // 'youtube_video_src' => $data['course']->youtube_video_id,
            //                     'preview_src' => $data['course']->youtube_video_id,
            //                 ];
            //             }
            //             else{
            //                 $content_details_array = [
            //                     // 'youtube_video_src' => "https://www.youtube.com/embed/".$data['course']->youtube_video_id,
            //                     'preview_src' => "https://www.youtube.com/embed/".$data['course']->youtube_video_id,
            //                 ];
            //             } 
                      
            //             $course_content_view_section = [
            //                 'content_type' => __('Course Intro Youtube Video'),
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //         else{
            //             $content_details_array = [
            //                 'preview_src' => $data['course']->cover_image ? getImageFile($data['course']->cover_image) : getImageFile($data['course']->image),
            //             ];
            //             $course_content_view_section = [
            //                 'content_type' => __('Course Image'),
            //                 'content_details' => $content_details_array,
            //             ];
            //         }
            //     }

              

            //     $lockCount = 0;
            //     $outerLoopKey = 0;
            //     $innerLoopKey = 0;
            //     foreach ($data['course']->lessons as $key => $lesson) {
            //         $lesson_lectures_array = array();
            //         if($key == 0){
            //             $outerLoopKey = 1;
            //         }
            //         else{
            //             $outerLoopKey = 0;
            //         }
            //         foreach ($lesson->lectures as $key2 => $lecture) {
            //             if($key2 == 0){
            //                 $innerLoopKey = 1;
            //             }
            //             else{
            //                 $innerLoopKey = 0;
            //             }

            //             if($innerLoopKey && $outerLoopKey){
            //                 $loopFirst = True;
            //             }
            //             else{
            //                 $loopFirst = False;
            //             }
            //             // $lock = checkStudentCourseIsLock($data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $loop->first);
            //             $lock = checkStudentCourseIsLock($data['course_lecture_views'], $data['course'], $lecture, $data['enrollment'], $innerLoopKey);
            //             if(checkStudentCourseView($data['course_lecture_views'], $data['course']->id, $lecture->id)){
            //                 $class1 = 'watchFinishedCourse';
            //                 $data_icon = 'ant-design:check-circle-filled';
            //             } 
            //             else if($lecture->id == $data['lecture_id_check']){
            //                 $class1 = 'watchContinuingCourse';
            //                 $data_icon = 'fluent:radio-button-24-filled';
            //             } 
            //             else{
            //                 $class1 = 'watchNewLecture';
            //                 $data_icon = 'fluent:radio-button-24-regular';
            //             }
            //             if($lecture->quiz_id != null){
            //                 $lectureType = 'Quiz';
            //             }
            //             else if($lecture->assignment_id != null){
            //                 $lectureType = 'Assignment';
            //             }
            //             else if($lecture->resource_id != null){
            //                 $lectureType = 'Resource';
            //             }
            //             else{
            //                 $lectureType = $lecture->type;
            //             }
            //                 $isLocked = 'No';
            //                 if($lectureType == 'Resource'){
            //                     if ($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube'){
            //                         $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource->id , 'lecture_id'=>$lecture->id]);
            //                         $onClickRoute = route('click-lecture-course');
            //                         $onClickRouteName = __('(Student) Click Lecture');
            //                     }
            //                     else{
            //                         $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
            //                         $onClickRoute = route('click-lecture-course');
            //                         $onClickRouteName = __('(Student) Click Lecture');
            //                     }
            //                 }
            //                 elseif($lectureType == 'Quiz'){
            //                     if(checkStudentCourseView($data['course_lecture_views'],
            //                                                             $data['course']->id,
            //                                                             $lecture->id)){
            //                     $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]);
            //                     $onClickRoute = route('click-lecture-course');
            //                     $onClickRouteName = __('(Student) Click Lecture');
            //                     }
            //                     else{
            //                         if(check_take_attempt_exam($lecture->quiz_id) == 'no'){
            //                             $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'start-quiz', $lecture->quiz?->uuid]); 
            //                             $onClickRoute = route('click-lecture-course');  
            //                             $onClickRouteName = __('(Student) Click Lecture');
            //                         }
            //                         else{
            //                             $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]); 
            //                             $onClickRoute = route('click-lecture-course');
            //                             $onClickRouteName = __('(Student) Click Lecture');
            //                         }
            //                     }
            //                 }
            //                 elseif($lectureType == 'Assignment'){
            //                     if(checkStudentCourseView($data['course_lecture_views'],
            //                     $data['course']->id,
            //                     $lecture->id)){
            //                         $redirectURLRoute = route('student.assignment-result');
            //                         $onClickRoute = route('assignment-result-course');
            //                         $onClickRouteName = __('(Student) Assignment Detail');
            //                     }
            //                     else{
            //                         $redirectURLRoute = route('student.assignment-details'); 
            //                         $onClickRoute = route('assignment-detail-course');
            //                         $onClickRouteName = __('(Student) Assignment Result');
            //                     }
            //                 }
            //                 else{
            //                     //NEW CASE IN WHICH ALL 3 QUIZ,ASSIGNMENT AND RESOURCE_ID ARE NULL AND LECTURE ARE WITHIN IT SELF TABLE
            //                     // LIKE IMAGE,AUDIO,VIDEO, YOUTUBE, SLIDE ETC
            //                     if ($lecture->type == 'text' || $lecture->type == 'Text' || $lecture->type == 'Youtube' || $lecture->type == 'youtube'){
            //                         // $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource?->id , 'lecture_id'=>$lecture->id]);
            //                         $onClickRoute = route('click-lecture-course');
            //                         $onClickRouteName = __('(Student) Click Lecture');
            //                     }
            //                     else{
            //                         // $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
            //                         $onClickRoute = route('click-lecture-course');
            //                         $onClickRouteName = __('(Student) Click Lecture');
            //                     }
            //                 }

            //                 if($lectureType == 'Assignment'){
            //                 }
            //                 elseif($lectureType == 'Quiz'){
            //                 }
            //                 else if($lectureType == 'Resource'){
            //                     if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
            //                         $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
            //                         $previewType = $lecture->resource?->type;
            //                     }
            //                     else if ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
            //                           $previewType = $lecture->resource?->type;
            //                         if (Str::contains($lecture->resource?->youtube_url_path, 'https://www.youtube.com')) {
            //                             $previewSrc = $lecture->resource?->youtube_url_path;    
            //                         }
            //                         else if(Str::contains($lecture->resource?->youtube_url_path, 'https://youtu.be')){
            //                             $previewSrc = $lecture->resource?->youtube_url_path;    
            //                         }
            //                         else{
            //                             $previewSrc = "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
            //                         }
            //                     }
            //                     else if ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
            //                           $previewType = $lecture->resource?->type;
            //                         // $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
            //                         $previewSrc = $lecture->resource?->text;
            //                     }
            //                     else if ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
            //                           $previewType = $lecture->resource?->type;
            //                         $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
            //                     }
            //                     else if ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
            //                           $previewType = $lecture->resource?->type;
            //                         $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
            //                     }
            //                     else if ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
            //                           $previewType = $lecture->resource?->type;
            //                         $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name.'/index.html';
            //                     }
            //                     else if ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
            //                           $previewType = $lecture->resource?->type;
            //                         $previewSrc = asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
            //                     }
            //                     $lesson_lectures_array[]=[
            //                         'sr_no'=>$key2+1,
            //                         'name'=>$lecture->title,
            //                         // 'type'=>$lectureType,
            //                         'type'=> $previewType,
            //                         'preview_src'=>$previewSrc,
            //                    ]; 
            //                 }
            //                 else{
            //                     if ($lecture->type == 'Video' || $lecture->type == 'video') {
            //                          $previewSrc = asset($lecture->file_path);
            //                     }
            //                     else if ($lecture->type == 'Youtube' || $lecture->type == 'youtube') {
            //                          $previewSrc = $lecture->url_path;
            //                     }
            //                     else if ($lecture->type == 'Image' || $lecture->type == 'image') {
            //                          $previewSrc = asset($lecture->image);
            //                     }
            //                     else if ($lecture->type == 'Image' || $lecture->type == 'image') {
            //                          $previewSrc = asset($lecture->image);
            //                     }
            //                     elseif ($lecture->type == 'PDF' || $lecture->type == 'pdf') {
            //                           $previewSrc = asset($lecture->pdf);
            //                     }
            //                     else if ($lecture->type == 'Slide Document' || $lecture->type == 'slide_document') {
            //                          $previewSrc = $lecture->slide_document;
            //                     }
            //                     else if ($lecture->type == 'Audio' || $lecture->type == 'audio') {
            //                          $previewSrc = asset($lecture->audio);
            //                     }
            //                     else if ($lecture->type == 'Text' || $lecture->type == 'text') {
            //                         $previewSrc = $lecture->text;
            //                     }
            //                     $lesson_lectures_array[]=[
            //                         'sr_no'=>$key2+1,
            //                         'name'=>$lecture->title,
            //                         'type'=>$lecture->type,
            //                         'preview_src'=>$previewSrc,
            //                    ]; 
            //                 }
            //         }
            //         $course_content_list_section[]=[
            //             'sr_no' => $key+1,
            //             'name' => $lesson->name,
            //             'data'=> $lesson_lectures_array,
            //         ];
            //     }
            // }
            // elseif($data['course']->course_type == COURSE_TYPE_SCORM){
            //     if($data['course']->scorm_course->duration){
            //         $course_video_duration = $data['course']->scorm_course->duration;
            //     }
            //     else{
            //         $course_video_duration = '0 min';
            //     }

            //     if ($data['course']->scorm_course){
            //         $content_details_array = [
            //             'scorm_src' => asset("scorm/".$data['course']->scorm_course?->uuid.'/'.$course->scorm_course?->entry_url),
            //         ];
            //         $course_content_view_section = [
            //             'content_type' => __('Scorm Course Content'),
            //             'content_details' => $content_details_array,
            //         ];
            //     }
            // }
            // else{
                $course_video_duration = null;

                foreach ($data['categories_with_resources'] as $key => $category) {
                    $category_resources_array = array();
                    foreach ($category['category_resources'] as $key2=> $resource) {
                        if ($resource['resource_type'] == 'Text' || $resource['resource_type'] == 'Youtube'){
                            // $redirectURL = route('student.resource.view_full_other', ['resource_id'=>$resource['resource_id']]);
                            $redirectSRC =  $resource['resource_preview_src']; 
                        }
                        else{
                            // $redirectURL = route('student.resource.view_full', ['ext'=>$resource['resource_ext'] , 'path'=> $resource['resource_path']]);
                            $redirectSRC =  $resource['resource_preview_src'];
                        }
                        $category_resources_array[] = [
                            // 'resource_id' => $resource['resource_id'],
                            'sr_no' => $key2 + 1,
                            'name' => $resource['resource_name'],
                            'type' => $resource['resource_type'],
                            'preview_src' => $redirectSRC,
                            // 'redirect_url' => $redirectURL,
                        ];
                    }
                    $course_content_list_section[]=[
                        'sr_no' => $key+1,
                        'name' => $category['category_name'],
                        'data' => $category_resources_array,
                    ];
                }
            // }

                if($data['course']->intro_video_check == 1 && getVideoFile($data['course']->video)){
                    $featured_image_or_video = getVideoFile($data['course']->video);
                    $featured_image_or_video_type= 1;
                }
                else if($data['course']->intro_video_check == 2 && $data['course']->youtube_video_id){
                    if (Str::contains($data['course']->youtube_video_id, 'https://www.youtube.com')) {
                        $featured_image_or_video =  $data['course']->youtube_video_id;
                    }
                    else if(Str::contains($data['course']->youtube_video_id, 'https://youtu.be')){
                        $featured_image_or_video =  $data['course']->youtube_video_id;
                    }
                    else{
                        $featured_image_or_video = "https://www.youtube.com/embed/".$data['course']->youtube_video_id;
                    }
                     $featured_image_or_video_type= 2;
                }
                else{
                       $featured_image_or_video =  $data['course']->cover_image ? getImageFile($data['course']->cover_image) : getImageFile($data['course']->image);
                        $featured_image_or_video_type= 3;
                }


            if (!is_null($action_type) && !is_null($quiz_uuid)) {
                $navbar_array = [
                    'course_title' =>$data['course']->title,
                    'return_url' =>route('student.my-course.show',['slug'=>$data['course']->slug]),
                    'total_course_lectures' =>$data['course']->lectures->count(),
                    'total_course_lessons' =>$data['course']->lessons->count(),
                    'total_course_video_duration' =>$course_video_duration,
                ];
            }
            else{
                $navbar_array = [
                    'course_title' =>$data['course']->title,
                    'total_course_lectures' =>$data['course']->lectures->count(),
                    'total_course_lessons' =>$data['course']->lessons->count(),
                    'total_course_video_duration' =>$course_video_duration,
                ];
            }
             ////////////////////////////COURSE CONTENT RIGHT SIDE SECTION END//////////////////////////

            $total_enrolled_students = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();
            $enrolled_students = Enrollment::where('course_id', $data['course']->id)->take(4)->get();
            foreach ($enrolled_students as $key => $enroll) {
               $enrolled_student_image_array[] = getImageFile($enroll->user?->image_path);
            }

            if($data['enrollment']){
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $data['enrollment']->id),2);
            }
            else{
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, 0),2);
            }

            ////////////////////////////OVERVIEW TAB SECTION START//////////////////////////
            $overview = array();
            if($data['course']){
                if(count($data['course']->keyPoints) > 0){
                    $overview = [
                        'keyPoints' => $data['course']->keyPoints,
                        'description' => $data['course']->description,
                    ];
                }
                else{
                    $overview = [
                        'keyPoints' => array(),
                        'description' => $data['course']->description,
                    ];
                }
            }
            $data['overview'] = $overview;
            //////////////////////////OVERVIEW TAB SECTION END/////////////////////////////




            $liveclass_tab_array = [
                'upcomming' => array(),
                'current' => array(),
                'past' => array(),
                'footer_section' => [
                    'enrolled_students_image' => $enrolled_student_image_array,
                    'total_enrolled_students' => $total_enrolled_students,
                    'course_last_update' => $data['course']->updated_at->format('d M Y'),
                ],
            ];



            //////////////////////////REVIEW TAB SECTION START/////////////////////////////
            $data['reviews'] = Review::whereCourseId($data['course']->id)->latest()->paginate(3);
            $data['loadMoreShowButtonReviews'] = Review::whereCourseId($data['course']->id)->paginate(4);
            $data['five_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(5)->count();
            $data['four_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(4)->count();
            $data['three_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(3)->count();
            $data['two_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(2)->count();
            $data['first_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(1)->count();
    
            $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
                (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
            $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];
        
            if ($data['total_user_review'] > 0) {
                $data['average_rating'] = $data['total_reviews'] / $data['total_user_review'];
            } else {
                $data['average_rating'] = 0;
            }
          
            $total_reviews = Review::whereCourseId($data['course']->id)->count();
    
            if ($total_reviews > 0) {
                $data['five_star_percentage'] = ($data['five_star_count'] / $total_reviews) * 100;
    
                $data['four_star_percentage'] = 100 * ($data['four_star_count'] / $total_reviews);
                $data['three_star_percentage'] = 100 * ($data['three_star_count'] / $total_reviews);
                $data['two_star_percentage'] = 100 * ($data['two_star_count'] / $total_reviews);
                $data['first_star_percentage'] = 100 * ($data['first_star_count'] / $total_reviews);
            } else {
                $data['five_star_percentage'] = 0;
                $data['four_star_percentage'] = 0;
                $data['three_star_percentage'] = 0;
                $data['two_star_percentage'] = 0;
                $data['first_star_percentage'] = 0;
            }

            if($data['course']->average_rating >= 1){

            }
            $top_section_review_array = [
                // 'average_rating'=> number_format($data['course']->average_rating, 1),
                'average_rating'=> number_format($data['average_rating'], 1),
                'total_reviews'=> $data['total_user_review'],
            ];
        
            $progress_bars_section_review_array = [
                'five_star_percentage'=> $data['five_star_percentage'],
                'five_star_count'=> $data['five_star_count'],
                'four_star_percentage'=> $data['four_star_percentage'],
                'four_star_count'=> $data['four_star_count'],
                'three_star_percentage'=> $data['three_star_percentage'],
                'three_star_count'=> $data['three_star_count'],
                'two_star_percentage'=> $data['two_star_percentage'],
                'two_star_count'=> $data['two_star_count'],
                'first_star_percentage'=> $data['first_star_percentage'],
                'first_star_count'=> $data['first_star_count'],
            ];
            if(!$data['reviews']->isEmpty()){
                $customerReviewsArray = array();
                foreach ($data['reviews'] as $key => $cust_review) {
                    $customerReviewsArray[]=[
                        'user_id' => $cust_review->user?->id,
                        'user_name' => $cust_review->user?->name,
                        'user_image' => asset($cust_review->user?->image_path),
                        'review' => $cust_review->comment,
                        'rating' => $cust_review->rating,      
                        'submission_at' => $cust_review->created_at->diffForHumans(),
                    
                    ];
                }
            }
            else{
                $customerReviewsArray = array();
            }
            $review_tab_array =[
                // 'top-section'=>$top_section_review_array,
                // 'progress-bars-section'=>$progress_bars_section_review_array,
                // 'customers-reviews-section'=>$customerReviewsArray,

                // 'average_rating' => number_format($course->average_rating, 1),
                'total_reviews'=> $data['total_user_review'],
                // 'average_rating'=> number_format($data['average_rating'], 1),
                'five_star_percentage'=> (int)$data['five_star_percentage'],
                'four_star_percentage' => (int)$data['four_star_percentage'],
                'three_star_percentage' => (int)$data['three_star_percentage'],
                'two_star_percentage' => (int)$data['two_star_percentage'],
                'first_star_percentage' => (int)$data['first_star_percentage'],
                'user_reviews' => $customerReviewsArray,
            ];
            //////////////////////////////REVIEW TAB SECTION END //////////////////////////////////////

            /////////////////////////////QUIZ TAB SECTION START////////////////////////////////////////
            $data['quiz'] = $data['course']->publishedExams;
            if($data['quiz']->count() > 0){
                foreach ($data['quiz'] as $key => $quiz) {
                    if(take_exam($quiz->id) == 'no'){
                        $action_tab_api_routes = [
                            // 'start_quiz' => route('start-quiz-course'),
                            // 'leaderboard' => route('quiz-leaderboard-course')
                            'action_btn_1_api_route' => route('start-quiz-course'),
                            'action_btn_1_api_name' => __('(Student)Start Quiz'),
                            'action_btn_2_api_route' => route('quiz-leaderboard-course'),
                            'action_btn_2_api_name' => __('(Student) Quiz LeaderBoard')
                           
                        ];
                    }
                    else{
                        $action_tab_api_routes = [
                            // 'see_result' => route('quiz-result-course'),
                            // 'leaderboard' => route('quiz-leaderboard-course')
                            'action_btn_1_api_route' => route('quiz-result-course'),
                            'action_btn_1_api_name' => __('(Student)Complete Quiz Result'),
                            'action_btn_2_api_route' => route('quiz-leaderboard-course'),
                            'action_btn_2_api_name' => __('(Student) Quiz LeaderBoard')
                        ];
                    }
                  
                    $quiz_tab_array[] = [
                        'quiz_id' => $quiz->id,
                        'quiz_uuid' => $quiz->uuid,
                        'quiz_name' => $quiz->name,
                        'quiz_type' => ucfirst(str_replace("_", " ", $quiz->type)),
                        'total_questions' => $quiz->questions->count(),
                        'time_duration' => $quiz->duration.__(' minutes'),
                        'action_api_routes' => $action_tab_api_routes,
                    ];
                }
            }
            /////////////////////////////QUIZ TAB SECTION END//////////////////////////////////////////


            ////////////////////////////ASSIGNMENT TAB SECTION START///////////////////////////////////
            $data['assignments'] = Assignment::where('course_id', $data['course']->id)->get();
            if(count($data['assignments'])){
                foreach ($data['assignments'] as $key => $assignment) {
                    $action_btn_routes = [
                        'view_details_api_route'=> route('assignment-detail-course'),
                        'view_details_api_name'=> __('(Student) Assignment Detail'),
                        'see_result_api_route'=> route('assignment-result-course'),
                        'see_result_api_name'=> __('(Student) Assignment Result'),
                    ];
                    $assignment_tab_array[] = [
                        'assignment_id' => $assignment->id,
                        'assignment_topic' => Str::limit($assignment->name, 50),
                        'assignment_total_marks' => $assignment->marks,
                        'action_btn_api_routes'=> $action_btn_routes,
                    ];
                }
            }
            else{
                // $assignment_tab_array = [
                //     __('Empty Assignments')
                // ];
            }
            ////////////////////////////ASSIGNMENT TAB SECTION END/////////////////////////////////////


            //////////////////////////NOTICE TAB SECTION START/////////////////////////////
            $data['notices'] = NoticeBoard::whereCourseId($data['course']->id)->latest()->get();
            if(count($data['notices'])){
                foreach ($data['notices'] as $key => $notice) {
                    $notice_tab_array[]=[
                        'notice_id'=>$notice->id,
                        'created_at'=>$notice->created_at->format('d - m - Y'),
                        'notice_topic'=>$notice->topic,
                        'notice_details'=>$notice->details,
                       
                    ];
                }
               
            }
            //////////////////////////NOTICE TAB SECTION END/////////////////////////////

             //////////////////////////DISCUSSION TAB SECTION START/////////////////////////////
             $data['discussions'] = Discussion::whereCourseId($data['course']->id)->whereNull('parent_id')->active()->get();
             if(count($data['discussions']) > 0){
                $userID = $user->id;
                if($userID){
                   $getUser = User::where('id',$userID)->first();
                   $Image =  getImageFile($getUser->image_path);
                   $auth_user_images = [
                        'discussion_form_user_image' => $Image,
                        'reply_form_user_image' => $Image,
                   ];
                }
                else{
                    $auth_user_images = [
                        'discussion_form_user_image' => null,
                        'reply_form_user_image' => null,
                   ];
                }
                 foreach ($data['discussions'] as $key => $discussion) {
                    // if($key == 1){
                    //     dd($discussion_replies_array);
                    // }   
                    if($discussion->comment_as == 1){
                        $discussion_user_type = 'Instructor';
                    }
                    else if($discussion->comment_as == 3){
                        $discussion_user_type = 'Coordinator';
                    }
                    else{
                        $discussion_user_type = 'Student'; 
                    }
                   
                    if(!$discussion->replies->isEmpty()){
                        $discussion_replies_array = array();
                        foreach ($discussion->replies as $reply) {
                            if($reply->comment_as == 1){
                                $reply_user_type = 'Instructor';
                            }
                            else if($reply->comment_as == 3){
                                $reply_user_type = 'Coordinator';
                            }
                            else{
                                $reply_user_type = 'Student'; 
                            }
                            $discussion_replies_array[]=[
                                'reply_id'=>$reply->id,
                                'reply_user_id'=>$reply->user?->id,
                                'reply_user_image'=>getImageFile($reply->user?->image_path),
                                'reply_user_name'=>$reply->user?->name,
                                'reply_user_type'=>$reply_user_type,
                                'reply_comment'=>$reply->comment,
                                'reply_created_at'=>$reply->created_at->diffforhumans(),
                            ];
                        }
                    }
                    else{
                        $discussion_replies_array = []; 
                    }

                  
                     $discussion_tab_array[]=[
                         'discussion_id'=>$discussion->id,
                         'discussion_user_id'=>$discussion->user?->id,
                         'discussion_user_name'=>$discussion->user?->name,
                         'discussion_user_image'=>getImageFile($discussion->user?->image_path),
                         'discussion_user_type'=>$discussion_user_type,
                         'discussion_comment'=>$discussion->comment,
                         'discussion_created_at'=>$discussion->created_at->diffforhumans(),
                         'discussion_total_replies'=>count($discussion->replies),
                         'discussion_replies_list'=> $discussion_replies_array,
                       
                     ];
                 }
                 $discussion_tab_array[] = [
                    'auth_user_images'=> $auth_user_images,
                 ];
             }
             //////////////////////////DISCUSSION TAB SECTION END/////////////////////////////

                //////////////////////////CERTIFICATE TAB SECTION START/////////////////////////////
                $data['certificate'] = $data['course']->studentCertificate;
                if($data['certificate']){
                    $certificate_tab_array=[
                        'certificate_id' => $data['certificate']->id,
                        'certificate_file_path' => asset($data['certificate']->path),
                        'certificate_file_extension' => pathinfo(public_path($data['certificate']->path), PATHINFO_EXTENSION),
                    ];
                }
                else{
                    // $certificate_tab_array=[
                    //     __('After completing the course, you will receive a certificate.')
                    // ];
                }
                //////////////////////////CERTIFICATE TAB SECTION END/////////////////////////////

                if($data['course']->course_type == 1 || $data['course']->course_type == 2){
                    $content_tab_array =$course_content_list_section;
                }
                else{
                    $content_tab_array =$course_content_list_section;
                }
               
                if($data['course']->course_type == 1 || $data['course']->course_type == 2){
                    $CourseType = __('General');
                    $result_array = [
                        'course_id' => $data['course']->id,
                        'course_title' => $data['course']->title,
                        'course_type' => $CourseType,
                        'lessons' => $data['course']->lessons->count(),
                        'lectures' => $data['course']->lectures->count(),
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        'course_content' => $content_tab_array,
                        'overview_tab' => $data['overview'],
                        'notice_tab' => $notice_tab_array,
                        'review_tab' => $review_tab_array,
                        // 'discussion_tab' => $discussion_tab_array,
                    ];
                }
                else{
                    $CourseType = __('Live');
               
                    $result_array = [
                        'course_id' => $data['course']->id,
                        'course_title' => $data['course']->title,
                        'course_type' => $CourseType,
                        'resources' => $data['course']->resources->count(),
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        'course_content' => $content_tab_array,
                        'overview_tab' => $data['overview'],
                        'notice_tab' => $notice_tab_array,
                        'review_tab' => $review_tab_array,
                      
                   
                    ];
                }
           
            return response()->json($result_array);
        }
            
    } catch (\Throwable $th) {
        dd($th);
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
      
}


public function checkAuthorization()
{
    $user = Auth::user();
    if ($user) {
        $role = $user->role;
        if ($role != 2 && $role != 5) { // Instructor or coordinator
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
        }
    } else {
        return response()->json(['message' => 'User Is Not Logged In'], 302);
    }
    if($user->instructor_google_access_token){
        return response()->json(['isGoogleAuthorized' => true , 'role' => $role], 200);
    }
    else{
         return response()->json(['isGoogleAuthorized' => false , 'role' => $role], 200);
    }
   
}

// public function myScheduleBackUp()
// {
//     $user = Auth::user();
//     if ($user) {
//         $role = $user->role;
//          if ($role != 2 && $role != 5) { // Instructor
//             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
//         }
//     } else {
//         return response()->json(['message' => 'User Is Not Logged In'], 302);
//     }

//     $instructorIds = Instructor::where('user_id', Auth::id())->pluck('id');

//     $startDate = Carbon::now()->startOfMonth(); // start of current month
//     $endDate = Carbon::now()->addMonth()->endOfMonth(); // end of next month

//     $classDetails = ClassDetail::whereIn('instructor_id', $instructorIds)
//         ->whereBetween('class_date', [$startDate->toDateString(), $endDate->toDateString()])
//         ->select('id', 'class_date', 'class_time', 'status')
//         ->get();

//     $completed = $classDetails->where('status', 'Completed')->values();
//     $missed = $classDetails->where('status', 'Missed')->values();
//     $upcoming = $classDetails->where('status', 'Scheduled')->values();

//     return response()->json([
//         'attend' => $completed,
//         'missed' => $missed,
//         'upcoming' => $upcoming,
//     ]);
// }



public function mySchedule()
{
    $user = Auth::user();
    if ($user) {
        $role = $user->role;
         if ($role != 2 && $role != 5) { // Instructor or Coordinator
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
        }
    } else {
        return response()->json(['message' => 'User Is Not Logged In'], 302);
    }

    $startDate = Carbon::now()->startOfMonth(); // start of current month
    $endDate = Carbon::now()->addMonth()->endOfMonth(); // end of next month

    $classDetails = collect();

      // Instructor path (role 2)
    if ($role == 2) {
        $instructorIds = Instructor::where('user_id', Auth::id())->pluck('id')->toArray();

        if (!empty($instructorIds)) {
            $classDetails = ClassDetail::whereIn('instructor_id', $instructorIds)
                ->whereBetween('class_date', [$startDate, $endDate])
                ->select('id', 'class_date', 'class_time', 'status')
                ->get();
        }

    // Coordinator path (role 5)
    } 
    else {
         // coordinator IDs for this user (could be multiple)
        $coordinatorId = Coordinator::where('user_id', $user->id)->value('id');

        if ($coordinatorId) {

            // ONLY thing coordinator cares about → course IDs
            $courseIds = CoordinatorCourse::where('coordinator_id', $coordinatorId)
                ->pluck('course_id')
                ->toArray();

            if (!empty($courseIds)) {
                $classDetails = ClassDetail::whereIn('course_id', $courseIds)
                    ->whereBetween('class_date', [$startDate, $endDate])
                    ->select('id', 'class_date', 'class_time', 'status')
                    ->get();
            }
        }

    }

    
     return response()->json([
        'attend'   => $classDetails->where('status', 'Completed')->values(),
        'missed'   => $classDetails->where('status', 'Missed')->values(),
        'upcoming' => $classDetails->where('status', 'Scheduled')->values(),
    ]);
   
}


              


    public function getTQCLessons(Request $request)
    {
        $user = auth()->user();
    
        if (!$user || $user->role != 2) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
   
        $result_array = array();
        $mainCourseIds= Course::where('title', 'Tajweed ul Quran the easy way (English)')->pluck('id')->toArray();
     
        $resources = DB::table('course_resources as cr')
                    ->join('resources as r', 'cr.resource_id', '=', 'r.id')
                    ->join('courses as c', 'cr.course_id', '=', 'c.id')
                    ->whereIn('cr.course_id', $mainCourseIds)
                    ->select('r.*', 'cr.course_id', 'c.title as course_title', 'c.slug as course_slug', 'c.image as course_image')
                    ->get();
    
        foreach ($resources as $key=> $resource) {
            $result_array[] = [
                // 'sr_no' => $key+1,
                'name' => $resource->name ?? 'N/A',
            ];
        }
     
         
    
        return response()->json([
            'success' => true,
            'message' => 'TQC Lessons fetched successfully.',
            'data' => $result_array
        ]);
    }




     public function getNQLessons(Request $request)
    {
        $user = auth()->user();
    
        if (!$user || $user->role != 2) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
   
        $result_array = array();
      
    
        for ($i = 1; $i <= 30; $i++) {
            $result_array[] = "Para $i [1st Half]";
            $result_array[] = "Para $i [2nd Half]";
        }
        
     
         
    
        return response()->json([
            'success' => true,
            'message' => 'NQ Lessons fetched successfully.',
            'data' => $result_array
        ]);
    }



      public function getHifzLessons(Request $request)
    {
        $user = auth()->user();
    
        if (!$user || $user->role != 2) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
   
        $result_array = array();
      
    
        for ($i = 1; $i <= 30; $i++) {
            $result_array[] = "Para $i [1st Half]";
            $result_array[] = "Para $i [2nd Half]";
        }
        
     
         
    
        return response()->json([
            'success' => true,
            'message' => 'Customized Hifz Lessons fetched successfully.',
            'data' => $result_array
        ]);
    }



      public function getUQCLessons(Request $request)
    {
        $user = auth()->user();
    
        if (!$user || $user->role != 2) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
   
        $result_array = array();
      
    
        for ($i = 1; $i <= 100; $i++) {
            $result_array[] = "Lesson $i";

            // Add Revision every 20 lessons
            if ($i % 20 == 0) {
                $start = $i - 19;
                $result_array[] = "Revision and Assessment [Lesson $start - $i]";
            }
        }
        
     
         
    
        return response()->json([
            'success' => true,
            'message' => 'UQC Lessons fetched successfully.',
            'data' => $result_array
        ]);
    }


    public function dailyAttendanceForm(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User Is Not Logged In'], 401);
        }

        if ($user->role != 2) { // Instructor
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 401);
        }


        $messages = [
            'attendance_date.required' => __('The Attendance Date Is Required'),
            'attendance_date.date' => __('The Attendance Date Must Be A Valid Date'),
            'student_id.required' => __('The Student ID Is Required'),
            'student_id.numeric' => __('The Student ID Must Be Numeric'),
            'lesson_delivered.required' => __('The Lesson Delivered Is Required'),
            'lesson_delivered.in' => __('The Lesson Delivered Must Be Either "YES" or "Tutor was not available" or "Student was not available" or "Student was on Leave"'),
    
        ];
    
        $validator = Validator::make($request->all(), [
            'attendance_date' => 'required|date',
            'student_id' => 'required|numeric',
            'lesson_delivered' => 'required|in:YES,Tutor was not available,Student was not available,Student was on Leave'
            
          
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],422);
        }


      

        $checkStudent = Student::where('id',$request->student_id)->first();
        if($checkStudent == null){
              return response()->json(['message' => 'Invalid Student ID. Does Not Exists'], 302);
        }

        $checkInstructor = Instructor::where('user_id',$user->id)->first();
        if($checkInstructor == null){
              return response()->json(['message' => 'Invalid User ID.'], 302);
        }

        $attendance_date = $request->attendance_date;
        $updated_attendace_date = date('Y-m-d',strtotime($attendance_date));
        $updated_month = date('m',strtotime($attendance_date));
        $updated_year = date('Y',strtotime($attendance_date));

        $checkExisting = InstructorAttendance::where('student_id',$request->student_id)->where('instructor_id',$checkInstructor->id)
                        ->whereDate('attendance_date',$updated_attendace_date)->first();

        if($checkExisting){
            $checkExisting->tqc_lesson = $request->tqc_lesson;
            $checkExisting->nq_lesson = $request->nq_lesson;
            $checkExisting->uqc_lesson = $request->uqc_lesson;
            $checkExisting->hifz_lesson = $request->hifz_lesson;
            $checkExisting->lesson_delivered = $request->lesson_delivered;
            $checkExisting->updated_at = Carbon::now();
            $checkExisting->save();
        }
        else{
            $checkExisting = new InstructorAttendance;
            $checkExisting->student_id = $request->student_id;
            $checkExisting->instructor_id = $checkInstructor->id;
            $checkExisting->attendance_date = $updated_attendace_date;
            $checkExisting->month = $updated_month;
            $checkExisting->year = $updated_year;
            $checkExisting->tqc_lesson = $request->tqc_lesson;
            $checkExisting->nq_lesson = $request->nq_lesson;
            $checkExisting->uqc_lesson = $request->uqc_lesson;
            $checkExisting->hifz_lesson = $request->hifz_lesson;
            $checkExisting->lesson_delivered = $request->lesson_delivered;
            $checkExisting->save();
        }


        return response()->json(['message' => 'Instructor Attendance Saved For Given Date.'], 200);
    
      

     
    
    }



       public function getTeacherAttendanceReport(Request $request)
    {
        $user = auth()->user();
    
        if ($user->role != 5) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
   
     
        $result_array = array();
      
    
        // 1. Query start karo with relationships
       // 1. Query with relationships
        // $query = InstructorAttendance::with(['student', 'instructor']);
        $query = InstructorAttendance::orderBy('id', 'desc');

        // 2. Filters apply karein
        $query->when($request->attendance_date, function ($q) use ($request) {
            return $q->whereDate('attendance_date', $request->attendance_date);
        })
        ->when($request->month, function ($q) use ($request) {
            return $q->where('month', $request->month);
        })
        ->when($request->year, function ($q) use ($request) {
            return $q->where('year', $request->year);
        })
        ->when($request->student_id, function ($q) use ($request) {
            return $q->where('student_id', $request->student_id);
        })
        ->when($request->instructor_id, function ($q) use ($request) {
            return $q->where('instructor_id', $request->instructor_id);
        });

        // 3. Paginate (50 per page)
        $paginatedData = $query->paginate(50);

        // 4. Transform the data into your CUSTOM array structure
        $customItems = $paginatedData->getCollection()->transform(function ($row) {
            return [
                'id'   => $row->id,
                'date'            => date('d-m-Y',strtotime($row->attendance_date)),
                'month'           => date('F',strtotime($row->attendance_date)),
                'year'           => date('Y',strtotime($row->attendance_date)),
                'student_id' => $row->student?->id ?? null,
                'student_name' => $row->student?->user?->name ?? 'N/A',
                'instructor_id' => $row->instructor?->id ?? null,
                'instructor_name' => $row->instructor?->user?->name ?? 'N/A',
                'tqc_lesson_name' =>$row->tqc_lesson,
                'nq_lesson_name' =>$row->nq_lesson,
                'lesson_delivered' =>$row->lesson_delivered,
                // 'timing'          => $row->month . ' ' . $row->year,
                // 'student_details' => [
                //     'id'    => $row->student->id ?? null,
                //     'name'  => $row->student->name ?? 'N/A',
                //     'image' => $row->student->image ?? null,
                // ],
                // 'instructor_details' => [
                //     'id'    => $row->instructor->id ?? null,
                //     'name'  => $row->instructor->name ?? 'N/A',
                // ],
                // 'lessons' => [
                //     'tqc'       => $row->tqc_lesson,
                //     'nq'        => $row->nq_lesson,
                //     'delivered' => $row->lesson_delivered,
                // ],
                // Add any extra logic here (e.g., status colors or calculated fields)
                // 'is_complete' => ($row->lesson_delivered > 0) ? true : false,
            ];
        });

        // 5. Return custom response with pagination metadata
        return response()->json([
            'success'  => true,
            'message' => 'Teacher Attendance Report Fetched Successfully.',
            'results' => $customItems, // Yeh hamara transformed array hai
            'pagination' => [
                'total'        => $paginatedData->total(),
                'count'        => $paginatedData->count(),
                'per_page'     => $paginatedData->perPage(),
                'current_page' => $paginatedData->currentPage(),
                'total_pages'  => $paginatedData->lastPage(),
                'next_page'    => $paginatedData->nextPageUrl(),
                'prev_page'    => $paginatedData->previousPageUrl(),
            ]
        ], 200);
    }




   public function allStudentsList(Request $request)
    {
        // $user = auth()->user();
    
        // if (!$user || $user->role != 2) {
        //     return response()->json(['message' => 'Unauthorized access.'], 403);
        // }
   
        $result_array = array();
      
    
        $query = Student::where('status',1)->orderBy('id', 'DESC')->get(); // Assuming you have these relationships

       

      foreach ($query as $key => $value) {
        $result_array[] = [
            'id' => $value->id,
            'name' => $value->user?->name,
        ];
      }
        
     
         
    
        return response()->json([
            'success' => true,
            'message' => 'Students List fetched successfully.',
            'data' => $result_array
        ]);
    }




     public function allInstructorsList(Request $request)
    {
        // $user = auth()->user();
    
        // if (!$user || $user->role != 2) {
        //     return response()->json(['message' => 'Unauthorized access.'], 403);
        // }
   
        $result_array = array();
      
    
        $query = Instructor::where('status',1)->orderBy('id', 'DESC')->get(); // Assuming you have these relationships

       

      foreach ($query as $key => $value) {
        $result_array[] = [
            'id' => $value->id,
            'name' => $value->user?->name,
        ];
      }
        
     
         
    
        return response()->json([
            'success' => true,
            'message' => 'Instructors List fetched successfully.',
            'data' => $result_array
        ]);
    }

}
