<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Student\ProfileRequest;
use App\Http\Services\EmailSendService;
use App\Models\Wishlist;
use App\Models\City;
use App\Models\Country;
use App\Models\Enrollment;
use App\Models\Chat;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Events\ChatEvent;
use Illuminate\Support\Facades\Mail;
use App\Models\Blog;
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
use Illuminate\Support\Facades\DB;
use App\Models\AffiliateHistory;
use App\Models\AffiliateRequest;
use App\Models\BookingHistory;
use App\Models\UserPackage;
use Carbon\Carbon;
use App\Models\ZoomSetting;
use App\Models\Answer;
use App\Models\Assignment;
use App\Models\AssignmentSubmit;
use App\Models\Instructor;
use App\Models\InstructorFeature;
use App\Models\ClassDetail;
use App\Models\Course_lecture;
use App\Models\Course_lecture_views;
use App\Models\Course;
use App\Models\Product;
use App\Models\InstructorProcedure;
use App\Models\Organization;
use App\Models\CartManagement;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Models\Resource;
use App\Models\ResourceCategory;

use App\Models\CourseResource;

use App\Models\Discussion;

use App\Models\Exam;
use App\Models\LiveClass;
use App\Models\Lesson;
use App\Models\LiveClassStudentLink;
use App\Models\NoticeBoard;
use App\Models\Order;
use App\Models\Addon\Product\ProductReview;
use App\Models\Bundle;
use App\Models\Order_item;
use App\Models\Question;
use App\Models\Question_option;
use App\Models\Refund;
use App\Models\Review;
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
use Illuminate\Support\Str;
use App\Models\Notification;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{

    use  ImageSaveTrait, General, SendNotification;

    protected $studentModel;
    protected $organizationModel;
    protected $instructorModel;

    public function __construct(Student $student, Instructor $instructor, Organization $organization)
    {
      
        $this->studentModel = new Crud($student);
        $this->instructorModel = new Crud($instructor);
        $this->organizationModel = new Crud($organization);
    }


    public function dashboard()
    {
        $data['base_url'] = url('/');
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('frontend.student.studentDashboard', $data);
    }

    public function home(){
        $data['base_url'] = url('/');
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('frontend.student.dashboard_components.home', $data);
    }

    public function latestBlogs(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
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
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
      

        $result_array = array();
        $courseIDs = array();
        $enrollments = Enrollment::where('user_id', auth()->id())
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->orderBy('id','DESC')->get();

        if(!$enrollments->isEmpty()){
            foreach ($enrollments as $key => $enroll) {
                $courseIDs[] = $enroll->course_id;
            }
            $courseIDs = array_unique($courseIDs);
        }

        if(!empty($courseIDs)){
            $courseLectures = Course_lecture::whereIn('course_id',$courseIDs)->where('resource_id','<>',null)->orderBy('id','DESC')->limit('10')->get();
     
            if(!$courseLectures->isEmpty()){
                foreach ($courseLectures as $key => $lecture) {
                    # code...
                
                    if( $lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube'){
                        $lectureViewPath = route('student.resource.view_full_other',['resource_id'=>$lecture->resource_id,'lecture_id'=>$lecture->id]);
                    }
                    else{
                        $lectureViewPath = route('student.resource.view_full',['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name,'lecture_id'=>$lecture->id]);
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


    public function studentInfo(){
        $user = Auth::user();
        $role = $user->role;
        if($role != 3){ // Student
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route '], 500);
        }
        $result_array = array();
        $user = auth::user();
        $student = $user->student;
        if($user){
            $userImage = getImageFile($user->image_path);
            $result_array = [
                'userName'=>$user->name,
                'userEmail'=>$user->email,
                'userImage'=>$userImage,
            ];
        }
        return response()->json($result_array);
    }



    
    public function latestLeaderBoard(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
    
        $top5_take_exams = Take_exam::where('user_id',$user->id)->orderBy('number_of_correct_answer', 'DESC')->take(5)->get();
      
        if(!$top5_take_exams->isEmpty()){
            foreach ($top5_take_exams as $key => $leader_board) {
                if (is_null($leader_board->exam?->course)) {
                    continue;
                }
    
                $imagePath = getImageFile($leader_board->user?->image_path);
                $studentName = $leader_board->user?->name;
                $examCourseName = $leader_board->exam?->course?->title;
                if($leader_board->passed){
                    $examStatus = "Passed";
                }
                else{
                    $examStatus = "Fail";
                }
             
                $result_array[]=[
                    'id' => $key+1,
                    'studentImage' => $imagePath,
                    'studentName' => $studentName,
                    'courseName' => $examCourseName,
                    'obtainedPercentage' => $leader_board->obtained_percentage,
                    'passingPercentage' => $leader_board->passing_percentage,
                    'status' => $examStatus,
                ];
            }
        }
        // dd($result_array);
        return response()->json($result_array);
    
      
    }











    public function TodayLiveClass(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
      
        $student_user_id=$user->id;
        $getStudent = Student::where('user_id',$student_user_id)->first();
        $student_id = $getStudent->id;
        $today_date = date('Y-m-d');
        $now          = Carbon::now()->format('H:i');

        
        ///////////////////////////////////////////////////////////////////////////////
         // 1) get **all** today’s lessons
        $allToday = ClassDetail::where('student_id', $student_id)
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



        if($getTodayLiveClass){
            $BBB_Meeting_Details = array();
            $Meet_Meeting_Details = array();
            $Zoom_Meeting_Details = array();

            $DateTime = changetimeByZone($getTodayLiveClass->class_date.' '.date('h:i A',strtotime($getTodayLiveClass->class_time)),$getTodayLiveClass->student_id,'student');
            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$getTodayLiveClass->student_id,'student');
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
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->StudentURI == NULL && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                  $btnStatus = __($getTodayLiveClass->status);
                  
                }
                else if($curtime >= $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->google_meet_link == NULL && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                    $btnStatus = __($getTodayLiveClass->status);
                  
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                   $btnStatus = __($getTodayLiveClass->status);
                   
                    $Zoom_Meeting_Details = [
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'user_type' =>__('Student'),
                        'user_name' =>$getTodayLiveClass->student?->user?->name,
                        'user_id' =>$getTodayLiveClass->student_id,
                    ];
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed"  && $getTodayLiveClass->class?->learning_tool == 'BBB') { 
                   $btnStatus = __($getTodayLiveClass->status);
                 
                    $BBB_Meeting_Details = [
                        'student_uri' =>$getTodayLiveClass->StudentURI,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                  
                }
                else if($curtime == $lsntime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Google_Meet') { 
                   $btnStatus = __($getTodayLiveClass->status);
                
                    $Meet_Meeting_Details = [
                        // 'student_uri' =>$getTodayLiveClass->StudentURI,
                        'student_uri' =>$getTodayLiveClass->google_meet_link,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                  
                }
                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                   $btnStatus = __($getTodayLiveClass->status);
                   
                    $Zoom_Meeting_Details = [
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'user_type' =>__('Student'),
                        'user_name' =>$getTodayLiveClass->student?->user?->name,
                        'user_id' =>$getTodayLiveClass->student_id,
                     
                    ];
                }
                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" && $getTodayLiveClass->class?->learning_tool == 'BBB') {
                   $btnStatus = __($getTodayLiveClass->status);
                  
                    $BBB_Meeting_Details = [
                        'student_uri' =>$getTodayLiveClass->StudentURI,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                }
                else if($curtime > $lsntime && $curtime < $lsnendtime && $getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed" &&  $getTodayLiveClass->class?->learning_tool == 'Google_Meet') {
                    $btnStatus = __($getTodayLiveClass->status);
                 
                    $Meet_Meeting_Details = [
                        'student_uri' =>$getTodayLiveClass->google_meet_link,
                        'lesson_id' =>$getTodayLiveClass->id,
                        'meeting_id' =>$getTodayLiveClass->meeting_id,
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                }
                else if($curtime == $lsnendtime && $getTodayLiveClass->class?->learning_tool == 'Zoom' && $getTodayLiveClass->meeting_id != NULL) {
                     $btnStatus = __($getTodayLiveClass->status);
                     
                }
                else if($curtime == $lsnendtime && $getTodayLiveClass->class->learning_tool == 'BBB' && $getTodayLiveClass->StudentURI != NULL) {
                     $btnStatus = __($getTodayLiveClass->status);
                     
                }
                else if($curtime == $lsnendtime && $getTodayLiveClass->class->learning_tool == 'Google_Meet' && $getTodayLiveClass->google_meet_link != NULL) {
                   $btnStatus = __($getTodayLiveClass->status);
                        $btnID = 'lec_link1_'.$getTodayLiveClass->id;
                }
                else if($curtime > $lsnendtime) {
                   
                    $btnStatus = __($getTodayLiveClass->status);
                   
                    
                }
                else{
                     $btnStatus = __($getTodayLiveClass->status);
                }
            }
            else{
                 $btnStatus = __($getTodayLiveClass->status);
              
            }

            // Google Meet: time-window branches often miss; ensure join data is returned when link exists.
            if ($getTodayLiveClass->class?->learning_tool == 'Google_Meet'
                && filled($getTodayLiveClass->google_meet_link)
            ) {
                $statusNorm = strtolower(trim((string) ($getTodayLiveClass->status ?? '')));
                $isInProgress = ($statusNorm === 'in progress');
                $isPendingOrScheduled = in_array($statusNorm, ['pending', 'scheduled'], true);

                // In Progress: always attach Meeting_Details (DB says live; student must get the link).
                // Pending / Scheduled: fill only if time logic did not already set details.
                if ($isInProgress || ($isPendingOrScheduled && empty($Meet_Meeting_Details))) {
                    $Meet_Meeting_Details = [
                        'student_uri' => $getTodayLiveClass->google_meet_link,
                        'lesson_id' => $getTodayLiveClass->id,
                        'meeting_id' => $getTodayLiveClass->meeting_id,
                        'hrefUrl' => 'javascript::void(0)',
                    ];
                }
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

                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'type' => 'Zoom',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'instructorID'=>$getTodayLiveClass->instructor_id,
                    'instructorName'=>Str::limit($getTodayLiveClass->instructor?->user?->name,50),
                    'Date_Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                  
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    // 'zoomApiKey'=> $api_key,
                    // 'zoomApiSecret'=> $api_secret,
                    'zoomSdkKey'=> $sdk_key,
                    'zoomSdkSecret'=> $sdk_secret,
                    // 'zoomApiJwtToken'=> $api_jwt_token,
                    // 'zoomMeetingSignature' => $getTodayLiveClass->zoom_meeting_signature,
                    'Meeting_Details'=>$Zoom_Meeting_Details,
                ];
            }
            else if($getTodayLiveClass->class?->learning_tool == 'Google_Meet'){
                $result_array = [
                    'id'=>$getTodayLiveClass->id,
                    'type' => 'Google_Meet',
                    'courseID'=>$getTodayLiveClass->course_id,
                    'courseName'=>Str::limit($getTodayLiveClass->course?->title,50),
                    'instructorID'=>$getTodayLiveClass->instructor_id,
                    'instructorName'=>Str::limit($getTodayLiveClass->instructor?->user?->name,50),
                    'Date_Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
                   
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
                    'instructorID'=>$getTodayLiveClass->instructor_id,
                    'instructorName'=>Str::limit($getTodayLiveClass->instructor?->user?->name,50),
                    'Date_Time'=>$classTime,
                    'Btn_Status'=>$btnStatus,
               
                    'learningTool'=>$getTodayLiveClass->class?->learning_tool,
                    'classType'=>$getTodayLiveClass->class?->type,
                    'Meeting_Details'=>$BBB_Meeting_Details,
                ];
            }
           
        }
   
        // dd($result_array);
        return response()->json($result_array);   
    }


    public function latestEnrolledCourses(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
     
        // $courseEnrollments = Enrollment::where('user_id', $user->id)
        // ->whereHas('course', function ($query) {
        //     $query->where('id','<>',null);
        // })
        // ->where('status', ACCESS_PERIOD_ACTIVE)
        // ->where('course_type',1)
        // ->orderBy('course_id','ASC')
        // ->groupBy('course_id')
        // ->limit(5)->get();


        // $courseEnrollments = Enrollment::where('user_id', $user->id)
        // ->where('status', ACCESS_PERIOD_ACTIVE)
        // ->whereHas('course', function ($query) {
        //     $query->where('id','<>',null);
        // })
        // ->where('course_type', 1)
        // ->orderBy('course_id', 'ASC')
        // ->orderBy('id', 'DESC') // Ensure latest record is selected
        // ->get()
        // ->unique('course_id') // Group by course_id in memory
        // ->take(5); // Limit to 5 results

        $courseEnrollments = Enrollment::where('user_id', $user->id)
        ->where('status', ACCESS_PERIOD_ACTIVE)
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->where('course_type', 1)
        ->orderBy('course_id', 'ASC')
        ->orderBy('id', 'DESC') // Ensure latest record is selected
        ->groupBy('course_id') // Group by course_id in memory
        ->limit(5)
        ->get();
     
        
        // dd($courseEnrollments);
        if(!$courseEnrollments->isEmpty()){
            foreach ($courseEnrollments as $key => $enroll) {
            
                $courseDetailLink = route('student.my-course.show',['slug'=>$enroll->course?->slug]);
                $courseProgress = (int)round(studentCourseProgress($enroll->course_id, $enroll->id));
                // {{number_format(studentCourseProgress(@$course->id, @$enrollment->id),
                //     2)}}% Done
    
                $result_array[]=[
                    'id' => $key+1,
                    'courseID' => $enroll->course_id,
                    'courseSlug' => $enroll->course?->slug,
                    'courseName' => $enroll->course?->title,
                    'courseProgress' => $courseProgress,
                    'TotalLessons' => $enroll->course?->lessons->count(),
                    'TotalLessonsLectures' => $enroll->course?->lectures->count(),
                    'course_DetailPage_Link' =>  $courseDetailLink,
                ];
            }
         
        }
        // dd($result_array);
        return response()->json($result_array);
    
      
    }



    public function monthlyClasses(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
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
  
        $getStudent = Student::where('user_id',$user->id)->first();
        $student_id = $getStudent->id;
      
        $getMonthlyClassHistory = ClassDetail::where('student_id',$student_id)
        ->whereDate('class_date' ,'>=',$sixMonthsAgoUpdated)
        ->whereDate('class_date' ,'<=',$todayUpdated)
        ->whereIn('status',['Completed','Missed'])
        ->orderBy('id','DESC')
        ->orderBy('class_date','ASC');
        // ->get();
     
      

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $today->copy()->subMonths($i)->startOfMonth();
            $monthName = $date->format('F');
            $monthNumber = $date->format('m');
            $MonthCompleteClasses = ClassDetail::where('student_id',$student_id)
            ->whereDate('class_date' ,'>=',$sixMonthsAgoUpdated)
            ->whereDate('class_date' ,'<=',$todayUpdated)
            ->whereMonth('class_date', $monthNumber)
            ->where('status','Completed')
            ->orderBy('id','DESC')
            ->orderBy('class_date','ASC')
            ->count();
          
            $MonthMissedClasses = ClassDetail::where('student_id',$student_id)
            ->whereDate('class_date' ,'>=',$sixMonthsAgoUpdated)
            ->whereDate('class_date' ,'<=',$todayUpdated)
            ->whereMonth('class_date', $monthNumber)
            ->where('status','Missed')
            ->orderBy('id','DESC')
            ->orderBy('class_date','ASC')
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


    public function studentEnrollCourse(Request $request){
      
        try {
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

           
        $messages = [
            'course_id.required' => __('The Course ID Is Required'),
        ];
    
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],302);
        }

        $checkCourse = Course::where('id',$request->course_id)->first();
        if($checkCourse == null){
           
            return response()->json([
                'status' => 302,
                'message' => 'Given Course ID Does Not Exists.'
            ],302);
        }
        $checkCourseType = $checkCourse->course_type;
        if($checkCourseType == 1 || $checkCourseType == 2){  // Self Learning Type Courses
            $enrollmentCourseType = 1;
            $enrollment = Enrollment::
            where(['course_id' => $request->course_id,
             'user_id' => Auth::user()->id,
              'status' => ACCESS_PERIOD_ACTIVE])
              ->where('end_date', '>=', now())->first();
            if ($enrollment) {
                $order = Order::find($enrollment->order_id);
                if ($order) {
                    if ($order->payment_status == 'due') {
                        Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                            $q->delete();
                        });
                        $order->delete();
                    } elseif ($order->payment_status == 'pending') {
                        $response['msg'] = __("You've already request the course & status is pending!");
                        $response['status'] = 302;
                        DB::rollBack();
                        return response()->json($response,302);
                    } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                        $response['msg'] = __("You've already enrolled the course!");
                        $response['status'] = 302;
                        DB::rollBack();
                        return response()->json($response,302);
                    }
                }
                else {
                    $response['msg'] = __("Something is wrong! Try again.");
                    $response['status'] = 500;
                    DB::rollBack();
                    return response()->json($response,500);
                }
            }

            if ($checkCourse->learner_accessibility == 'free') {
                $order = new Order();
                $order->user_id = Auth::user()->id;
                $order->order_number = rand(100000, 999999);
                $order->payment_status = 'free';
                $order->delivery_status = 1;
                $order->save();

                $order_item = new Order_item();
                $order_item->order_id = $order->id;
                $order_item->user_id = Auth::user()->id;
                $order_item->course_id = $checkCourse->id;
                $order_item->owner_user_id = $checkCourse->user_id ?? null;
                $order_item->unit_price = 0;
                $order_item->admin_commission = 0;
                $order_item->owner_balance = 0;
                $order_item->sell_commission = 0;
                $order_item->delivery_status = 3;
                $order_item->save();
                
                $enrollment = new Enrollment();
                $enrollment->order_id = $order->id;
                $enrollment->user_id = auth()->id();
                $enrollment->course_id = $request->course_id;
                $enrollment->owner_user_id = $checkCourse->user_id ?? null;
                $enrollment->start_date = now();
                $enrollment->end_date = ($checkCourse->access_period) ? Carbon::now()->addDays($checkCourse->access_period) : MAX_EXPIRED_DATE;
                $enrollment->save();

                $response['msg'] = __("Free Course added to your my learning list!");
                $response['status'] = 200;
              
                return response()->json($response,200);
            }
           
        }
        else{       // Live Type Courses
            $enrollmentCourseType = 2;
            $courseExists = Enrollment::where(['user_id' => Auth::user()->id, 'course_id' => $request->course_id, 'status' => ACCESS_PERIOD_ACTIVE])->first();
            DB::beginTransaction();
            try {
                if ($courseExists) {
                    $response['msg'] = __("Live Course Already Enrolled!");
                    $response['status'] = 302;
                    DB::rollBack();
                    return response()->json($response,302);
                }
                if ($request->course_id) {
                    $courseExits = Course::find($request->course_id);
                    if (!$courseExits) {
                        $response['msg'] = __("Course not found!");
                        $response['status'] = 302;
                        DB::rollBack();
                        return response()->json($response,302);
                    }
    
    
                    $enrollment = new Enrollment();
                    $enrollment->order_id = 0;
                    $enrollment->user_id = Auth::user()->id;
                    $enrollment->course_id = $request->course_id;
                    $enrollment->owner_user_id = $courseExits->user_id ?? null;
                    $enrollment->course_type = 2;
                    // $enrollment->start_date = date("Y-m-d H:i:s");
                    $enrollment->start_date = now();
                    $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                    $enrollment->save();
                }
                // $response['quantity'] = Enrollment::where(['user_id' => Auth::user()->id,'status' => ACCESS_PERIOD_ACTIVE])->count();
                $response['msg'] = __("Live Course Enrolled");
                // $response['msgInfoChange'] = __("Live Course Enrolled");
                $response['status'] = 200;
                DB::commit();
                return response()->json($response,200);
            } catch (\Throwable $th) {
                DB::rollBack();
                $response['msg'] = __("Something is wrong! Try again.");
                $response['status'] = 402;
                return response()->json($response,402);
            }
        }
       
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }

    public function myLearningCoursesList(){
     
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }
        $result_array = array();
        $self_learning = array();
        $live = array();
        $on_going = array();
        $completed = array();
     
        // $enrollments = Enrollment::where('enrollments.user_id', auth()->id())->
        // select('enrollments.*', 'order_items.unit_price')
        // ->join('orders', 'orders.id', '=', 'enrollments.order_id')
        // ->join('order_items', 'order_items.order_id', '=', 'orders.id')
        // ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        // ->whereHas('course', function ($query) {
        //     $query->where('id','<>',null);
        // })
        // ->where('enrollments.course_type',1)
        // ->groupBy('enrollments.id')
        // ->latest()->get();


        $enrollments = Enrollment::where('enrollments.user_id', auth()->id())->
        select('enrollments.*', 'order_items.unit_price')
        ->join('orders', 'orders.id', '=', 'enrollments.order_id')
        ->join('order_items', 'order_items.order_id', '=', 'orders.id')
        ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->where('enrollments.course_type',1)
        ->orderBy('enrollments.course_id', 'ASC')
        ->orderBy('enrollments.id', 'DESC') // Ensure latest record is selected
        ->groupBy('enrollments.course_id')
        ->latest()->get();


   

        // $enrollmentsLive = Enrollment::where('enrollments.user_id', auth()->id())->select('enrollments.*')
        // ->whereHas('course', function ($query) {
        //     $query->where('id','<>',null);
        // })
        // ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        // ->where('enrollments.course_type',2)
        // ->groupBy('enrollments.id')
        // ->latest()->get();



        $enrollmentsLive = Enrollment::where('enrollments.user_id', auth()->id())->select('enrollments.*')
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        ->where('enrollments.course_type',2)
        ->orderBy('enrollments.course_id', 'ASC')
        ->orderBy('enrollments.id', 'DESC') // Ensure latest record is selected
        ->groupBy('enrollments.course_id')
        ->latest()->get();



     
        if(!$enrollments->isEmpty()){
            foreach ($enrollments as $key => $value) {
                if($value->course?->course_type == 3){
                    $courseType = __('Live');
                }
                else if($value->course?->course_type == 1){
                    $courseType = __('General');
                }
                else{
                    $courseType = __('Scorm');
                }
                // $check_progress = (int)round(studentCourseProgress($value->course->id, $value->id));
                $check_progress = number_format(studentCourseProgress($value->course->id, $value->id),2);

                if(checkIfExpired($value)){
                    $actionRoute = route('my-course-show-student',['slug'=>$value->course?->slug]);
                }
                else{
                    $actionRoute = route('frontend-course-detail',['slug'=>$value->course?->slug]);
                }
                if($check_progress  == 100){ // Completed Course
                 
                    $completed[] = [
                        'courseID'=> $value->course_id,
                        'courseUUID'=> $value->course?->uuid,
                        'courseName'=> $value->course?->title,
                        'courseSlug'=> $value->course?->slug,
                        'courseType'=> $courseType,
                        'courseImage'=> getImageFile($value->course?->image_path),
                        'coursePrice'=> $value->course->price,
                        'learner_accessibility'=> $value->course->learner_accessibility,
                        // 'progress'=> (int)round(studentCourseProgress($value->course->id, $value->id)),
                        'progress'=>  number_format(studentCourseProgress($value->course->id, $value->id),2),
                        'validity'=> $value->course?->access_period,
                    ];
                }
                else{       // OnGoing Course
         
                    $on_going[] = [
                        'courseID'=> $value->course_id,
                        'courseUUID'=> $value->course?->uuid,
                        'courseName'=> $value->course?->title,
                        'courseSlug'=> $value->course?->slug,
                        'courseType'=> $courseType,
                        'courseImage'=> getImageFile($value->course?->image_path),
                        'coursePrice'=> $value->course->price,
                        'learner_accessibility'=> $value->course->learner_accessibility,
                        // 'progress'=> (int)round(studentCourseProgress($value->course->id, $value->id)),
                        'progress'=>  number_format(studentCourseProgress($value->course->id, $value->id),2),
                        'lecturesRemaining'=> (int)round(studentCourseProgressRemaining($value->course->id, $value->id)),
                        'totalLessons'=> $value->course ? count($value->course->lessons) : 0,
                        'validity'=> $value->course?->access_period,                
                        'onClickApiRoute'=> $actionRoute,
                    ];
                 
                }
          
                    
            }
         
        }

    


        if(!$enrollmentsLive->isEmpty()){
            foreach ($enrollmentsLive as $key => $value) {
                // $check_progress = (int)round(studentCourseProgress($value->course->id, $value->id));
                $check_progress = number_format(studentCourseProgress($value->course->id, $value->id),2);
             
                if($value->course?->course_type == 3){
                    $courseType = __('Live');
                }
                else if($value->course?->course_type == 1){
                    $courseType = __('General');
                }
                else{
                    $courseType = __('Scorm');
                }

                if(checkIfExpired($value)){
                    $actionRoute = route('my-course-show-student',['slug'=>$value->course?->slug]);
                }
                else{
                    $actionRoute = route('frontend-course-detail',['slug'=>$value->course?->slug]);
                }

                 if($check_progress  == 100){ // Completed Course
                    $completed[] = [
                        'courseID'=> $value->course_id,
                        'courseUUID'=> $value->course?->uuid,
                        'courseName'=> $value->course?->title,
                        'courseSlug'=> $value->course?->slug,
                        'courseType'=> $courseType,
                        'courseImage'=> getImageFile($value->course?->image_path),
                        'coursePrice'=> $value->course->price,
                        'learner_accessibility'=> $value->course->learner_accessibility,  
                        'rating'=> $value->course?->average_rating,
                        'totalLessons'=> $value->course ? count($value->course->lessons) : 0,
                       'resources' => CourseResource::where('course_id', $value->course_id)->count(),
                       'enrollment_count' => Enrollment::where('course_id', $value->course_id)
                                                        ->where('user_id', $user->id)
                                                        ->count(),
                        'onClickApiRoute'=> $actionRoute,
                    ];
                }
                else{       // OnGoing Course
                    $on_going[] = [
                        'courseID'=> $value->course_id,
                        'courseUUID'=> $value->course?->uuid,
                        'courseName'=> $value->course?->title,
                        'courseSlug'=> $value->course?->slug,
                        'courseType'=> $courseType,
                        'courseImage'=> getImageFile($value->course?->image_path),
                        'coursePrice'=> $value->course->price,
                        'learner_accessibility'=> $value->course->learner_accessibility,
                        'rating'=> $value->course?->average_rating,
                        'totalLessons'=> $value->course ? count($value->course->lessons) : 0,
                        'resources' => CourseResource::where('course_id', $value->course_id)->count(),
                        'enrollment_count' => Enrollment::where('course_id', $value->course_id)
                                                        ->where('user_id', $user->id)
                                                        ->count(),
                       
                        'onClickApiRoute'=> $actionRoute,
                    ];
                 
                }


            }
        }
        
        $result_array = [
            'on_going' => $on_going,
            'completed' => $completed,
        ];
        return response()->json($result_array);
     
        // $result_array = [
        //     'self_learning' => $self_learning,
        //     'live' => $live,
        // ];
      
        if(empty($self_learning) && empty($live)){
            return response()->json(['message' =>  __('Empty Courses'), 
            'data'=>$result_array],200);
        }
        else{
            return response()->json($result_array);
        }
    
     
     
    
      
    }





    public function myLearningCoursesList2(){
     
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
        $self_learning = array();
        $live = array();
     
        $enrollments = Enrollment::where('enrollments.user_id', auth()->id())->select('enrollments.*', 'order_items.unit_price')->join('orders', 'orders.id', '=', 'enrollments.order_id')->join('order_items', 'order_items.order_id', '=', 'orders.id')->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->where('enrollments.course_type',1)
        ->groupBy('enrollments.id')
        ->latest()->get();
        if(!$enrollments->isEmpty()){
            foreach ($enrollments as $key => $value) {
                if($value->unit_price > 0){
                    if(get_currency_placement() == 'after'){
                        $Price = $value->unit_price.' '.get_currency_symbol();
                    }
                    else{
                        $Price = get_currency_symbol().' '.$value->unit_price;
                    }
                }
                else{
                    $Price = 'Free';
                }
                if(checkIfExpired($value)){
                    if(checkIfLifetime($value->end_date)){
                        $validity = __('Lifetime');
                    }
                    else{
                        $validity = Carbon::now()->diffInDays($value->end_date, false).' '.__('days left');
                    }
                }
                else{
                    $validity = __('Expired');
                }

                if(checkIfExpired($value)){
                    $actionRoute = route('student.my-course.show',['slug'=>$value->course?->slug]);
                }
                else{
                    $actionRoute = route('course-details',['slug'=>$value->course?->slug]);
                }
                $self_learning[]=[
                    'courseID'=> $value->course_id,
                    'courseName'=> $value->course?->title,
                    'courseImage'=> getImageFile($value->course?->image_path),
                    'coursePrice'=> $Price,
                    'orderID'=> $value->order?->order_number,
                    'validity'=> $validity,
                    'progress'=> (int)round(studentCourseProgress($value->course->id, $value->id)),
                    'actionRoute'=> route('course-details',['slug'=>$value->course?->slug]),
                ];
            }
         
        }

    

        $enrollmentsLive = Enrollment::where('enrollments.user_id', auth()->id())->select('enrollments.*')
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        ->where('enrollments.course_type',2)
        ->groupBy('enrollments.id')
        ->latest()->get();
        if(!$enrollmentsLive->isEmpty()){
            foreach ($enrollmentsLive as $key => $value) {
                if($value->unit_price > 0){
                    if(get_currency_placement() == 'after'){
                        $Price = $value->unit_price.' '.get_currency_symbol();
                    }
                    else{
                        $Price = get_currency_symbol().' '.$value->unit_price;
                    }
                }
                else{
                    $Price = 'Free';
                }

                if(checkIfExpired($value)){
                    $actionRoute = route('student.my-course.show',['slug'=>$value->course?->slug]);
                }
                else{
                    $actionRoute = route('course-details',['slug'=>$value->course?->slug]);
                }

                $live[]=[
                    'courseID'=> $value->course_id,
                    'courseName'=> $value->course?->title,
                    'courseImage'=> getImageFile($value->course?->image_path),
                    'coursePrice'=> $Price,
                    'actionRoute'=> route('course-details',['slug'=>$value->course?->slug]),
                ];
            }
        }
        
        $result_array = [
            'self_learning' => $self_learning,
            'live' => $live,
        ];

        if(empty($self_learning) && empty($live)){
            return response()->json(['message' =>  __('Empty Courses'), 
            'data'=>$result_array],200);
        }
        else{
            return response()->json($result_array);
        }
    
     
     
    
      
    }

    public function resourceList($course_uuid)
    {
        try {
            // Get course by UUID
            $course = Course::where('uuid', $course_uuid)->first();
            
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }
    
            // Get unique categories that have resources in this course
            $categories = ResourceCategory::select('resource_categories.id', 'resource_categories.name')
                ->join('resources', 'resource_categories.id', '=', 'resources.category_id')
                ->join('course_resources', 'resources.id', '=', 'course_resources.resource_id')
                ->where('course_resources.course_id', $course->id)
                ->distinct()
                ->get()
                ->map(function ($category) use ($course) {
                    return [
                        'category_id' => $category->id,
                        'category_name' => $category->name,
                        'course_id' => $course->id,
                        'course_uuid' => $course->uuid,
                    ];
                });
    
            return response()->json([
                'success' => true,
                'message' => 'Resource categories retrieved successfully',
                'resources' => $categories,
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getResources($courseId, $categoryId)
{
    try {
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

        // Validate IDs are integers
        if (!is_numeric($courseId) || !is_numeric($categoryId)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid course or category ID'
            ], 422);
        }

        $resources = CourseResource::with('resource')
            ->where('course_id', $courseId)
            ->whereHas('resource', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->get()
            ->map(function ($courseResource) {
                if($courseResource->resource->type == 'Youtube' || $courseResource->resource->type == 'youtube'){
                     if (Str::contains($courseResource->resource?->youtube_url_path, 'https://www.youtube.com')) {
                        $fileUrl = $courseResource->resource?->youtube_url_path;    
                    }
                    else if(Str::contains($courseResource->resource?->youtube_url_path, 'https://youtu.be')){
                        $fileUrl = $courseResource->resource?->youtube_url_path;    
                    }
                    else{
                        $fileUrl = "https://www.youtube.com/embed/".$courseResource->resource?->youtube_url_path;
                    }
                }
                else if($courseResource->resource->type == 'Slide Document' || $courseResource->resource->type == 'slide document'){
                       $fileUrl =  asset('uploads/course_resource').'/'.$courseResource->resource?->file_name.'/index.html';
                }
                else if($courseResource->resource->type == 'Text' || $courseResource->resource->type == 'text'){
                       $fileUrl =  $courseResource->resource?->text;
                }
                else{
                    $fileUrl =  asset('uploads/course_resource').'/'.$courseResource->resource?->file_name;
                }
                return [
                    'resource_id' => $courseResource->resource->id,
                    'resource_name' => $courseResource->resource->name,
                    'resource_ext' => $courseResource->resource->file_extension,
                    'resource_path' => $courseResource->resource->file_name,
                    'resource_type' => $courseResource->resource->type,
                    'file_url' => $fileUrl ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $resources,
            'message' => 'Resources retrieved successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve resources: ' . $e->getMessage()
        ], 500);
    }
}

public function getResourcesApi($courseId, $categoryId)
{
    try {
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }

        // Validate IDs are integers
        if (!is_numeric($courseId) || !is_numeric($categoryId)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid course or category ID'
            ], 422);
        }

        $resources = CourseResource::with('resource')
            ->where('course_id', $courseId)
            ->whereHas('resource', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->get()
            ->map(function ($courseResource) {
                $resource = $courseResource->resource;
                $type = strtolower($resource->type);
                
                // Handle non-file resources directly
                if(in_array($type, ['youtube', 'text'])) {
                    if ($type == 'youtube') {
                        if (Str::contains($resource->youtube_url_path, ['https://www.youtube.com', 'https://youtu.be'])) {
                            $fileUrl = $resource->youtube_url_path;    
                        } else {
                            $fileUrl = "https://www.youtube.com/embed/".$resource->youtube_url_path;
                        }
                    } else { // text
                        $fileUrl = $resource->text;
                    }
                    
                    return [
                        'resource_id' => $resource->id,
                        'resource_name' => $resource->name,
                        'resource_type' => $resource->type,
                        'file_url' => $fileUrl,
                        'needs_secure' => false // Frontend can use directly
                    ];
                }
                
                // For file-based resources, return secure endpoint
                return [
                    'resource_id' => $resource->id,
                    'resource_name' => $resource->name,
                    'resource_ext' => $resource->file_extension,
                    'resource_type' => $resource->type,
                    'file_url' => route('resource.stream', ['id' => $resource->id]),
                    'needs_secure' => true // Frontend should use this secured URL
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $resources,
            'message' => 'Resources retrieved successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve resources: ' . $e->getMessage()
        ], 500);
    }
}


       public function stream($id)
    {
        try {
            $resource = Resource::findOrFail($id);
            $user = Auth::user();
            // Check if the resource is associated with the course
              $hasAccess = Enrollment::where('user_id', $user->id)
            ->whereHas('course.resources', function($query) use ($resource) {
                $query->where('resource_id', $resource->id);
            })
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You are not enrolled in any course that contains this resource');
        }
          
         
            $type = strtolower($resource->type);
            $filePath = public_path('uploads/course_resource/'.$resource->file_name);
            switch($type) {
                case 'slide document':
                    $filePath .= '/index.html';
                    break;
                    
                case 'image':
                case 'pdf':
                case 'audio':
                case 'video':
                
                    // File path already set
                    break;
                    
                default:
                    abort(404, 'Resource type not supported');
            }

            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }
            return $this->streamFile($filePath, $resource->file_extension);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
   private function streamFile($fullPath, $extension): StreamedResponse
{
    $headers = [
        'Content-Type' => $this->getMimeType($extension),
        'Content-Disposition' => 'inline',
        'Content-Length' => filesize($fullPath),
        'Accept-Ranges' => 'bytes'
    ];

    return new StreamedResponse(function() use ($fullPath) {
        $stream = fopen($fullPath, 'rb');
        fpassthru($stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
    }, 200, $headers);
}

private function getMimeType($extension): string
{
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'mp4' => 'video/mp4',
        'mp3' => 'audio/mpeg',
        'webm' => 'video/webm',
        // Add more as needed
    ];

    return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
}
    public function myCourseShowApiBackUp(Request $request, $slug, $action_type = null, $quiz_uuid = null, $answer_id = null)
{
        try {
            //code...
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }
      
        $result_array = array();
        ////////////////////////////
        $action_type = $request->action_type;
        $quiz_uuid = $request->quiz_uuid;
        ///////////////////////////
      
        $data['course'] = Course::with('publishedExams')->whereSlug($slug)->first();
        if($data['course'] == null){
            return response()->json(['message' => 'Given Course Slug Does Not Exists'], 302);
        }
        $data['pageTitle'] = $data['course']->title;
        if($data['course']->course_type == 1 || $data['course']->course_type == 2){
            $data['courseType'] = 'General';
        }
        else{
            $data['courseType'] = 'Live'; 
        }
      
      
            //Current User Given Course Active Period Enrollment Info
            $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => auth()->id(), 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
     
            // End:: Checking enrolled or not
            // Current Given Course Total Student Enrollments Count
            $data['total_enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();
    
            // 4 Out of Total Enrollments Data For Current Given Course
            $data['enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->take(4)->get();
            if($data['enrollment']){
                $enrollmentID = $data['enrollment']->id;
            }
            else{
                $enrollmentID = 0;
            }
        $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $enrollmentID),2);

     

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
            $content_tab_array = array();
    
    
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
                                $preview_src =  "https://www.youtube.com/embed/".$value2->resource?->youtube_url_path;
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
                                'resource_id'=>$value2->resource?->id,
                                'resource_name'=>$value2->resource?->name,
                                'resource_ext'=>$value2->resource?->file_extension,
                                'resource_path'=>$value2->resource?->file_name,
                                'resource_type'=>$value2->resource?->type,
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
                            'youtube_video_src' => "https://www.youtube.com/embed/".$data['youtube_video_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Video' || $data['lecture_type'] == 'video'){
                        $content_details_array = [
                            'video_src' => asset('uploads/course_resource').'/'.$data['video_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Text' || $data['lecture_type'] == 'text'){
                        $content_details_array = [
                            'text' => asset('uploads/course_resource').'/'.$data['text_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Image' || $data['lecture_type'] == 'image'){
                        $content_details_array = [
                            'image_src' => asset('uploads/course_resource').'/'.$data['image_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Image' || $data['lecture_type'] == 'image'){
                        $content_details_array = [
                            'image_src' => asset('uploads/course_resource').'/'.$data['image_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'PDF' || $data['lecture_type'] == 'pdf'){
                        $content_details_array = [
                            'pdf_src' => asset('uploads/course_resource').'/'.$data['pdf_src'],
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Slide Document' || $data['lecture_type'] == 'slide document'){
                        $content_details_array = [
                            'slide_document_src' => asset('uploads/course_resource').'/'.$data['slide_document_src'].'/index.html',
                        ];
                        $course_content_view_section = [
                            'content_type' => $data['lecture_type'],
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['lecture_type'] == 'Audio' || $data['lecture_type'] == 'audio'){
                        $content_details_array = [
                            'audio_src' => asset('uploads/course_resource').'/'.$data['audio_src'],
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
                            'course_image_poster' => getImageFile($data['course']->image),
                            'video_src' => getVideoFile($data['course']->video),
                        ];
                        $course_content_view_section = [
                            'content_type' => __('Course Intro Video'),
                            'content_details' => $content_details_array,
                        ];
                    }
                    else if($data['course']->intro_video_check == 2 && $data['course']->youtube_video_id){
                        $content_details_array = [
                            'youtube_video_src' => "https://www.youtube.com/embed/".$data['course']->youtube_video_id,
                        ];
                        $course_content_view_section = [
                            'content_type' => __('Course Intro Youtube Video'),
                            'content_details' => $content_details_array,
                        ];
                    }
                    else{
                        $content_details_array = [
                            'image_src' => getImageFile($data['course']->image),
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
                        if($lock){
                            $isLocked = 'Yes';
                            $lesson_lectures_array[]=[
                                'lecture_no'=>$key2+1,
                                'lecture_id'=>$lecture->id,
                                'lecture_title'=>$lecture->title,
                                'lecture_type'=>$lectureType,
                                'is_locked'=>$isLocked,
                                // 'lecture_div_class'=>$class1,
                                // 'data_icon'=>$data_icon,
                           ];
                        }
                        else{
                            $isLocked = 'No';
                            if($lectureType == 'Resource'){
                                if ($lecture->resource?->type == 'Text' || $lecture->resource?->type == 'Youtube'){
                                    $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource?->id , 'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                }
                                else{
                                    $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                }
                            }
                            elseif($lectureType == 'Quiz'){
                                if(checkStudentCourseView($data['course_lecture_views'],
                                                                        $data['course']->id,
                                                                        $lecture->id)){
                                $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]);
                                $onClickRoute = route('click-lecture-course');
                                }
                                else{
                                    if(check_take_attempt_exam($lecture->quiz_id) == 'no'){
                                        $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'start-quiz', $lecture->quiz?->uuid]); 
                                        $onClickRoute = route('click-lecture-course');  
                                    }
                                    else{
                                        $redirectURLRoute = route('student.my-course.show', [$lecture->course?->slug, 'quiz-result', $lecture->quiz?->uuid]); 
                                        $onClickRoute = route('click-lecture-course');
                                    }
                                }
                            }
                            elseif($lectureType == 'Assignment'){
                                if(checkStudentCourseView($data['course_lecture_views'],
                                $data['course']->id,
                                $lecture->id)){
                                    $redirectURLRoute = route('student.assignment-result');
                                    $onClickRoute = route('assignment-result-course');
                                }
                                else{
                                    $redirectURLRoute = route('student.assignment-details'); 
                                    $onClickRoute = route('assignment-detail-course');
                                }
                            }
                            else{
                                //NEW CASE IN WHICH ALL 3 QUIZ,ASSIGNMENT AND RESOURCE_ID ARE NULL AND LECTURE ARE WITHIN IT SELF TABLE
                                // LIKE IMAGE,AUDIO,VIDEO, YOUTUBE, SLIDE ETC
                                if ($lecture->type == 'text' || $lecture->type == 'Text' || $lecture->type == 'Youtube' || $lecture->type == 'youtube'){
                                    // $redirectURLRoute = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource?->id , 'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                }
                                else{
                                    // $redirectURLRoute = route('student.resource.view_full', ['ext'=>$lecture->resource?->file_extension , 'path'=> $lecture->resource?->file_name ,'lecture_id'=>$lecture->id]);
                                    $onClickRoute = route('click-lecture-course');
                                }
                            }

                            if($lectureType == 'Assignment'){
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lectureType,
                                    'assignment_id'=>$lecture->assignment_id,
                                    'is_locked'=>$isLocked,
                                    // 'lecture_div_class'=>$class1,
                                    // 'data_icon'=>$data_icon,
                                    'onclick_api_url'=>$onClickRoute
                               ];
                            }
                            else if($lectureType == 'Quiz'){
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lectureType,
                                    'quiz_id'=>$lecture->quiz_id,
                                    'is_locked'=>$isLocked,
                                    // 'lecture_div_class'=>$class1,
                                    // 'data_icon'=>$data_icon,
                                    'onclick_api_url'=>$onClickRoute
                               ];
                            }
                            else if($lectureType == 'Resource'){
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lectureType,
                                    'resource_id'=>$lecture->resource_id,
                                    'is_locked'=>$isLocked,
                                    // 'lecture_div_class'=>$class1,
                                    // 'data_icon'=>$data_icon,
                                    'onclick_api_url'=>$onClickRoute
                               ]; 
                            }
                            else{
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lecture->type,
                                    'is_locked'=>$isLocked,
                                    // 'lecture_div_class'=>$class1,
                                    // 'data_icon'=>$data_icon,
                                    'onclick_api_url'=>$onClickRoute
                               ]; 
                            }
                        }
                    
                    }
                    $course_content_list_section[]=[
                        'lesson_id' => $lesson->id,
                        'lesson_name' => $lesson->name,
                        'lesson_lectures'=> $lesson_lectures_array,
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
                            'resource_no' => $key2 + 1,
                            'resource_name' => $resource['resource_name'],
                            'resource_type' => $resource['resource_type'],
                            'redirect_preview_src' => $redirectSRC,
                        ];
                    }
                    $course_content_list_section[]=[
                        'category_id' => $category['category_id'],
                        'category_name' => $category['category_name'],
                        'category_resources' => $category_resources_array,
                    ];
                }
            }

          
             ////////////////////////////COURSE CONTENT RIGHT SIDE SECTION END//////////////////////////
       
            if($data['enrollment']){
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, $data['enrollment']->id),2);
            }
            else{
                $data['course_content_progress'] = number_format(studentCourseProgress($data['course']->id, 0),2);
            }
        

                $content_tab_array = [
                   'course_content_view' =>  $course_content_view_section,
                   'course_content_progress' =>  $data['course_content_progress'],
                   'course_content_list_section' =>  $course_content_list_section,
                ];
            $result_array = [
                'pageTitle'=> $data['pageTitle'],
                'course_type'=>$data['courseType'],
                'course_content_list_section' => $course_content_list_section,
                // 'course_last_update' => $data['course']->updated_at->format('d M Y'),
                // 'course_content_tab' => $content_tab_array,
            ];
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
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }
      
        $result_array = array();
        ////////////////////////////
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
            // $liveclass_tab_array = [
            //     'upcomming' => array(),
            //     'current' => array(),
            //     'past' => array(),
            // ];
           
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
                            $class1 = 1;
                            $data_icon = 'ant-design:check-circle-filled';
                        } 
                        else if($lecture->id == $data['lecture_id_check']){
                            $class1 = 2;
                            $data_icon = 'fluent:radio-button-24-filled';
                        } 
                        else{
                            $class1 = 3;
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

                        if($lock){
                            if($lectureType == 'Resource'){
                                $TYPE = $lecture->resource?->type;
                            }
                            else if($lectureType = 'Assignment'){
                                $TYPE = 'Assignment';
                            }
                            else if($lectureType = 'Quiz'){
                                $TYPE = 'Quiz';
                            }
                            else{
                                $TYPE = $lecture->type; 
                            }

                            $isLocked = 'Yes';
                            $lesson_lectures_array[]=[
                                'lecture_no'=>$key2+1,
                                'lecture_id'=>$lecture->id,
                                'lecture_title'=>$lecture->title,
                                'lecture_type'=>$lectureType,
                                'type'=>$TYPE,
                                'is_locked'=>$isLocked,
                                'checkbox_icon_class'=> $data_icon,
                                'checkbox_icon_class_type'=> $class1,
                                // 'lecture_div_class'=>$class1,
                                // 'data_icon'=>$data_icon,
                           ];
                        }
                        else{
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

                                if($lectureType == 'Resource'){
                                    $TYPE = $lecture->resource?->type;
                                }
                                else if($lectureType = 'Assignment'){
                                    $TYPE = 'Assignment';
                                }
                                else if($lectureType = 'Quiz'){
                                    $TYPE = 'Quiz';
                                }
                                else{
                                    $TYPE = $lecture->type; 
                                }
                            if($lectureType == 'Assignment'){
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lectureType,
                                    'type'=>$TYPE,
                                    'assignment_id'=>$lecture->assignment_id,
                                    'is_locked'=>$isLocked,
                                    'checkbox_icon_class'=> $data_icon,
                                    'checkbox_icon_class_type'=> $class1,
                                    'onclick_api_url'=>$onClickRoute,
                                    'onclick_api_name'=>$onClickRouteName
                               ];
                            }
                            elseif($lectureType == 'Quiz'){
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lectureType,
                                    'type'=>$TYPE,
                                    'is_locked'=>$isLocked,
                                    'checkbox_icon_class'=> $data_icon,
                                    'checkbox_icon_class_type'=> $class1,
                                    'onclick_api_url'=>$onClickRoute,
                                    'onclick_api_name'=>$onClickRouteName
                               ]; 
                            }
                            else if($lectureType == 'Resource'){
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lectureType,
                                    'type'=>$TYPE,
                                    'resource_id'=>$lecture->resource_id,
                                    'is_locked'=>$isLocked,
                                    'checkbox_icon_class'=> $data_icon,
                                    'checkbox_icon_class_type'=> $class1,
                                    'onclick_api_url'=>$onClickRoute,
                                    'onclick_api_name'=>$onClickRouteName
                               ]; 
                            }
                            else{
                                $lesson_lectures_array[]=[
                                    'lecture_no'=>$key2+1,
                                    'lecture_id'=>$lecture->id,
                                    'lecture_title'=>$lecture->title,
                                    'lecture_type'=>$lecture->type,
                                    'type'=>$TYPE,
                                    'is_locked'=>$isLocked,
                                    'checkbox_icon_class'=> $data_icon,
                                    'checkbox_icon_class_type'=> $class1,
                                    'onclick_api_url'=>$onClickRoute,
                                    'onclick_api_name'=>$onClickRouteName
                               ]; 
                            }
                        }
                    
                    }
                    $course_content_list_section[]=[
                        'lesson_id' => $lesson->id,
                        'lesson_name' => $lesson->name,
                        'lesson_lectures'=> $lesson_lectures_array,
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
                        // $category_resources_array[] = [
                        //     'resource_no' => $key2 + 1,
                        //     'resource_name' => $resource['resource_name'],
                        //     'resource_type' => $resource['resource_type'],
                        //     'redirect_preview_src' => $redirectSRC,
                        // ];
                        $category_resources_array[] = [
                            'sr_no' => $key2 + 1,
                            'name' => $resource['resource_name'],
                            'type' => $resource['resource_type'],
                            'preview_src' => $redirectSRC,
                        ];
                    }
                    // $course_content_list_section[]=[
                    //     'category_id' => $category['category_id'],
                    //     'category_name' => $category['category_name'],
                    //     'category_resources' => $category_resources_array,
                    // ];
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
                        'footer_section' => [
                            'enrolled_students_image' => $enrolled_student_image_array,
                            'total_enrolled_students' => $total_enrolled_students,
                            'course_last_update' => $data['course']->updated_at->format('d M Y'),
                        ],
                    ];
                }
                else{
                    $overview = [
                        'keyPoints' => array(),
                        'description' => $data['course']->description,
                        'footer_section' => [
                            'enrolled_students_image' => $enrolled_student_image_array,
                            'total_enrolled_students' => $total_enrolled_students,
                            'course_last_update' => $data['course']->updated_at->format('d M Y'),
                        ],
                    ];
                }
            }
            $data['overview'] = $overview;
            //////////////////////////OVERVIEW TAB SECTION END/////////////////////////////




        $upcoming_live_classes = LiveClass::whereCourseId($data['course']->id)
            ->where(function($q){    // Current Today Pending Live Classes
                $q->whereDate('date', now());
                $q->whereTime('time', '>', now());
            })
            ->orWhere(function($q){     // Upcomming Scheduled Live Classes
                $q->whereDate('date', '>', now());
            })
            ->latest()->get();

        $current_live_classes = LiveClass::whereCourseId($data['course']->id)
            ->where(function($q){         //  Current Today Live Active Classes
                $q->whereDate('date', now());
                $q->whereTime('time', '<=', now());
                $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
            })
            ->latest()->get();

        $past_live_classes = LiveClass::whereCourseId($data['course']->id)
        ->where(function($q){
                $q->whereDate('date', now());
                $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
            })
            ->orWhere(function($q){
                $q->whereDate('date', '<', now());
            })
            ->latest()->get();
        $upcomming_array = array();
        $current_array = array();
        $past_array = array();
        if(!$upcoming_live_classes->isEmpty()){
            foreach ($upcoming_live_classes as $key => $upcoming_live_class) {
                if($upcoming_live_class->meeting_host_name == 'zoom'){
                    $learningTool = __('Zoom');
                }
                elseif($upcoming_live_class->meeting_host_name == 'bbb'){
                     $learningTool = __('BigBlueButton');
                }
                elseif($upcoming_live_class->meeting_host_name == 'google_meet'){
                     $learningTool = __('Google Meet');
                }


                $getStudent = Student::where('user_id',$user->id)->first();


                $DateTime = changetimeByZone($upcomming_live_class->date.' '.date('h:i A',strtotime($upcomming_live_class->time)),$getStudent->id,'student');
                $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$getStudent->id,'student');
                $updated_time = date('h:i A',strtotime($DateTime));
                $lsntime = strtotime($updated_time);
                $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                $curdate = strtotime($curdate);
                $lsndate = date('Y-m-d',strtotime($DateTime));
                $lsndate = strtotime($lsndate); 
                $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                $curtime = strtotime($updated_cur_time);
                $lesson_duration = $upcomming_live_class->duration;
                $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                $lsnendtime = $lsntimestamp;


                 if($curdate <  $lsndate) {
                    $statusInfo = __('Scheduled');
                 }
                 else if($curdate == $lsndate) {
                    if($curtime < $lsntime) {
                         $statusInfo = __('Waiting');
                    }
                }
                $upcomming_array[] = [
                    'id' =>  $upcoming_live_class->id,
                    'date' =>  date('d-m-Y',strtotime($upcoming_live_class->date)),
                    'time' =>  date('h:i A',strtotime($upcoming_live_class->time)),
                    'duration' => $upcoming_live_class->duration,
                    'topic' => Str::limit($upcoming_live_class->class_topic, 50),
                    'learning_tool' => $learningTool,
                    'status' => $statusInfo
                ];
            }
        }
      

        if(!$current_live_classes->isEmpty()){
            foreach ($current_live_classes as $key => $current_live_class) {
                if($current_live_class->meeting_host_name == 'zoom'){
                    $learningTool = __('Zoom');
                }
                elseif($current_live_class->meeting_host_name == 'bbb'){
                     $learningTool = __('BigBlueButton');
                }
                elseif($current_live_class->meeting_host_name == 'google_meet'){
                     $learningTool = __('Google Meet');
                }


                $getStudent = Student::where('user_id',$user->id)->first();

                $DateTime = changetimeByZone($current_live_class->date.' '.date('h:i A',strtotime($current_live_class->time)),$getStudent->id,'student');
                $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$getStudent->id,'student');
                $updated_time = date('h:i A',strtotime($DateTime));
                $lsntime = strtotime($updated_time);
                $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                $curdate = strtotime($curdate);
                $lsndate = date('Y-m-d',strtotime($DateTime));
                $lsndate = strtotime($lsndate); 
                $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                $curtime = strtotime($updated_cur_time);
                $lesson_duration = $current_live_class->duration;
                $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                $lsnendtime = $lsntimestamp;

                $StudentURI = getStudentURI($current_live_class->id,$getStudent->id);

                $meetingInfo = array();
                if ($curdate == $lsndate) {
                    if($curtime >= $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_id == NULL && $current_live_class->meeting_host_name == 'zoom') {
                        $statusInfo = __('Waiting');
                    }
                    else if($curtime >= $lsntime && $curtime < $lsnendtime && $StudentURI ==NULL && $current_live_class->meeting_host_name == 'bbb') {
                         $statusInfo = __('Waiting');
                    }
                    else if($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'zoom') {
                         $statusInfo = __('Join Class');
                         $meetingInfo = [
                            'lesson_id' =>  $current_live_class->id,
                            'meeting_id' => $current_live_class->meeting_id,
                            'user_type' => __('Student'),
                            'user_name' => $user->name,
                            'user_id' =>   $getStudent->id,
                         ];
                    }
                    else if($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'zoom') {
                         if($current_live_class->status == "Completed" && $current_live_class->is_moderator_left == 1) {
                             $statusInfo = __('Completed');
                         }
                         else if($current_live_class->status == "Missed" && $current_live_class->is_moderator_left == 1) {
                            $statusInfo = __('Missed');
                         } 
                    }
                    else if($curtime == $lsntime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'bbb') { 
                        $statusInfo = __('Join Class');
                        $meetingInfo = [
                            'student_url' =>  $StudentURI,
                        ];
                    }
                    else if($curtime == $lsntime && ($current_live_class->status == "Completed" || $current_live_class->status == "Missed") && $current_live_class->meeting_host_name == 'google_meet') {
                        if($current_live_class->status == "Completed" && $current_live_class->meeting_id != null) {
                            $statusInfo = __('Completed');
                        } 
                        else if($current_live_class->status == "Missed" && $current_live_class->meeting_id != null) {
                            $statusInfo = __('Missed');
                        }
                    }
                    else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'zoom') {
                        $statusInfo = __('Join Class');
                        $meetingInfo = [
                            'lesson_id' =>  $current_live_class->id,
                            'meeting_id' => $current_live_class->meeting_id,
                            'user_type' => __('Student'),
                            'user_name' => $user->name,
                            'user_id' =>   $getStudent->id,
                        ];
                    }
                    else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'zoom') {
                        if($current_live_class->status == "Completed" && $current_live_class->is_moderator_left == 1) {
                            $statusInfo = __('Completed');
                        } 
                        else if($current_live_class->status == "Missed" && $current_live_class->is_moderator_left == 1) {
                            $statusInfo = __('Missed');
                        } 
                        else { 
                            $statusInfo = __('Join Class');
                            $meetingInfo = [
                                'lesson_id' =>  $current_live_class->id,
                                'meeting_id' => $current_live_class->meeting_id,
                                'user_type' => __('Student'),
                                'user_name' => $user->name,
                                'user_id' =>   $getStudent->id,
                            ];
                        }
                    }
                    else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->status != "Completed" && $current_live_class->status != "Missed" && $current_live_class->meeting_host_name == 'bbb') { 
                        $statusInfo = __('Join Class');
                        $meetingInfo = [
                            'student_url' =>  $StudentURI,
                        ];
                    } 
                    else if($curtime > $lsntime && $curtime < $lsnendtime && $current_live_class->meeting_host_name == 'bbb') {
                        if($current_live_class->status == "Completed" && $StudentURI != null) {
                             $statusInfo = __('Completed');
                        } else if($current_live_class->status == "Missed" && $StudentURI != null) {
                             $statusInfo = __('Missed');
                        } else { 
                            $statusInfo = __('Join Class');
                            $meetingInfo = [
                                'student_url' =>  $StudentURI,
                            ];
                        }
                    }
                    else if($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'zoom') {
                        if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'zoom' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                            // updateLessonStatusCompleteZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                            $statusInfo = __('Completed');
                        } else if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'zoom' && $current_live_class->is_instructor_joined == 0) {
                            // updateLessonStatusMissedZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                            $statusInfo = __('Missed');
                        } else {
                            if($current_live_class->status == "Completed") {
                                $statusInfo = __('Completed');
                            } else {
                                $statusInfo = __('Missed');   
                            }
                        }
                    }
                     else if($curtime == $lsnendtime && $current_live_class->meeting_host_name == 'bbb') {
                        if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'bbb' && ($current_live_class->is_instructor_joined == '1' || $current_live_class->is_instructor_joined == "1" || $current_live_class->is_instructor_joined == 1 || $current_live_class->is_instructor_joined)) {
                            // updateLessonStatusCompleteZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                            $statusInfo = __('Completed');
                        } else if(($current_live_class->status != "Completed" && $current_live_class->status != "Missed") && $current_live_class->meeting_host_name == 'bbb' && $current_live_class->is_instructor_joined == 0) {
                            // updateLessonStatusMissedZoomInstructor($current_live_class->id,$current_live_class->meeting_id,null);
                            $statusInfo = __('Missed');
                        } else {
                            if($current_live_class->status == "Completed") {
                                $statusInfo = __('Completed');
                            } else {
                                $statusInfo = __('Missed');    
                            }
                        }
                    }
                }

                  if($learningTool == 'Zoom'){
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

                         $current_array[] = [
                            'id' =>  $current_live_class->id,
                            'date' =>  date('d-m-Y',strtotime($current_live_class->date)),
                            'time' =>  date('h:i A',strtotime($current_live_class->time)),
                            'duration' => $current_live_class->duration,
                            'topic' => Str::limit($current_live_class->class_topic, 50),
                            'learning_tool' => $learningTool,
                            'status' => $statusInfo,
                            // 'zoomApiKey'=> $api_key,
                            // 'zoomApiSecret'=> $api_secret,
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                            // 'zoomApiJwtToken'=> $api_jwt_token,
                            // 'zoomMeetingSignature' => $current_live_class->zoom_meeting_signature,
                            'meetingInfo' => $meetingInfo
                        ];
                    }
                    else{
                         $current_array[] = [
                            'id' =>  $current_live_class->id,
                            'date' =>  date('d-m-Y',strtotime($current_live_class->date)),
                            'time' =>  date('h:i A',strtotime($current_live_class->time)),
                            'duration' => $current_live_class->duration,
                            'topic' => Str::limit($current_live_class->class_topic, 50),
                            'learning_tool' => $learningTool,
                            'status' => $statusInfo,
                            'meetingInfo' => $meetingInfo
                        ];
                    }
               

            }
        }

          if(!$past_live_classes->isEmpty()){
            foreach ($past_live_classes as $key => $past_live_class) {
                if($past_live_class->meeting_host_name == 'zoom'){
                    $learningTool = __('Zoom');
                }
                elseif($past_live_class->meeting_host_name == 'bbb'){
                     $learningTool = __('BigBlueButton');
                }
                elseif($past_live_class->meeting_host_name == 'google_meet'){
                     $learningTool = __('Google Meet');
                }


                $getStudent = Student::where('user_id',$user->id)->first();


                $DateTime = changetimeByZone($past_live_class->date.' '.date('h:i A',strtotime($past_live_class->time)),$getStudent->id,'student');
                $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$getStudent->id,'student');
                $updated_time = date('h:i A',strtotime($DateTime));
                $lsntime = strtotime($updated_time);
                $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                $curdate = strtotime($curdate);
                $lsndate = date('Y-m-d',strtotime($DateTime));
                $lsndate = strtotime($lsndate); 
                $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                $curtime = strtotime($updated_cur_time);
                $lesson_duration = $past_live_class->duration;
                $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                $lsnendtime = $lsntimestamp;

                $StudentURI = getStudentURI($past_live_class->id,$getStudent->id);
               

                if ($curdate == $lsndate) {
                    if($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'zoom') {
                        if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'zoom' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                            // updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                            $statusInfo = __('Completed');
                        } else if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == 0) {
                            // updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                             $statusInfo = __('Missed');
                        } else {
                            if($past_live_class->status == "Completed") {
                                 $statusInfo = __('Completed');
                            } else {
                                 $statusInfo = __('Missed'); 
                            }
                        }
                    }
                    else if($curtime == $lsnendtime && $past_live_class->meeting_host_name == 'bbb') {
                        if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'bbb' && ($past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined)) {
                            // updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                             $statusInfo = __('Completed');
                        } else if(($past_live_class->status != "Completed" && $past_live_class->status != "Missed") && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == 0) {
                            // updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                             $statusInfo = __('Missed');
                        } else {
                            if($past_live_class->status == "Completed") {
                                 $statusInfo = __('Completed');
                            } else {
                                 $statusInfo = __('Missed');
                            }
                        }
                    }
                    else if($curtime > $lsnendtime) {
                        if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                            // updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                            $statusInfo = __('Completed');
                        } 
                        else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                            // updateLessonStatusCompleteInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                            $statusInfo = __('Missed');
                        }
                        else if($past_live_class->status == "Completed") {
                            $statusInfo = __('Completed');
                        } 
                        else {
                            if($past_live_class->status == "Missed") {
                                if(!empty($past_live_class->meeting_id)) {
                                    $res = check_missed_status_instructor($past_live_class->meeting_id);
                                    if($res == 'Completed') {
                                        $statusInfo = __('Completed');
                                    } else {
                                        $statusInfo = __('Missed');  
                                    }  
                                } else {
                                    $statusInfo = __('Missed');
                                }
                            } 
                            else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "zoom" && $past_live_class->is_instructor_joined == 0) {
                                // updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                $statusInfo = __('Missed'); 
                            }
                            else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == "bbb" && $past_live_class->is_instructor_joined == 0) {
                                // updateLessonStatusMissedZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                                $statusInfo = __('Missed');
                            }
                             else {
                                $statusInfo = __('Missed');   
                            }
                        }
                    }
                }
                else if($curdate > $lsndate) 
                {
                    if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'zoom' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                        // updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                        $statusInfo = __('Completed');
                    } 
                    else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'bbb' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                        // updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                        $statusInfo = __('Completed');
                    } 
                    else if($past_live_class->status == "Pending" && $past_live_class->meeting_host_name == 'google_meet' && $past_live_class->is_instructor_joined == '1' || $past_live_class->is_instructor_joined == "1" || $past_live_class->is_instructor_joined == 1 || $past_live_class->is_instructor_joined) {	
                        // updateLessonStatusCompleteZoomInstructor($past_live_class->id,$past_live_class->meeting_id,null);
                        $statusInfo = __('Completed');
                    } 
                    else if($past_live_class->status == "Completed") {
                        $statusInfo = __('Completed');
                    } 
                    else if($past_live_class->status == "Pending") {
                        $statusInfo = __('Missed');   
                    } 
                    else {
                        if($past_live_class->status == "Missed") {
                            if(!empty($past_live_class->meeting_id)) {
                                $res = check_missed_status_instructor($past_live_class->meeting_id);
                                if($res == 'Completed') {
                                    $statusInfo = __('Completed');
                                } else {
                                    $statusInfo = __('Missed');   
                                }  
                            } else {
                                $statusInfo = __('Missed');    
                            }
                        }
                    }
                }
                else 
                {
                    if($past_live_class->status == "Completed") {  
                        $statusInfo = __('Completed');
                    } else {
                        if($past_live_class->status == "Missed") {
                            if(!empty($past_live_class->meeting_id)) {
                                $res = check_missed_status_instructor($past_live_class->meeting_id);
                                if($res == 'Completed') {
                                    $statusInfo = __('Completed');
                                } else {
                                    $statusInfo = __('Missed');    
                                }
                            } else {
                               $statusInfo = __('Missed');      
                            }
                        }
                    }
                }
                $past_array[] = [
                    'id' =>  $past_live_class->id,
                    'date' =>  date('d-m-Y',strtotime($past_live_class->date)),
                    'time' =>  date('h:i A',strtotime($past_live_class->time)),
                    'duration' => $past_live_class->duration,
                    'topic' => Str::limit($past_live_class->class_topic, 50),
                    'learning_tool' => $learningTool,
                    'status' => $statusInfo,
                    // 'meetingInfo' => $meetingInfo
                ];

            }
        }
            $liveclass_tab_array = [
                'upcomming' => $upcomming_array,
                'current' => $current_array,
                'past' => $past_array,
                'footer_section' => [
                    'enrolled_students_image' => $enrolled_student_image_array,
                    'total_enrolled_students' => $total_enrolled_students,
                    'course_last_update' => $data['course']->updated_at->format('d M Y'),
                ],
            ];



            //////////////////////////REVIEW TAB SECTION START/////////////////////////////
            // $data['reviews'] = Review::whereCourseId($data['course']->id)->latest()->paginate(3);
            // $data['loadMoreShowButtonReviews'] = Review::whereCourseId($data['course']->id)->paginate(4);
            // $data['five_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(5)->count();
            // $data['four_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(4)->count();
            // $data['three_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(3)->count();
            // $data['two_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(2)->count();
            // $data['first_star_count'] = Review::whereCourseId($data['course']->id)->whereRating(1)->count();
    
            // $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
            //     (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
            // $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];
        
            // if ($data['total_user_review'] > 0) {
            //     $data['average_rating'] = $data['total_reviews'] / $data['total_user_review'];
            // } else {
            //     $data['average_rating'] = 0;
            // }
          
            // $total_reviews = Review::whereCourseId($data['course']->id)->count();
    
            // if ($total_reviews > 0) {
            //     $data['five_star_percentage'] = ($data['five_star_count'] / $total_reviews) * 100;
    
            //     $data['four_star_percentage'] = 100 * ($data['four_star_count'] / $total_reviews);
            //     $data['three_star_percentage'] = 100 * ($data['three_star_count'] / $total_reviews);
            //     $data['two_star_percentage'] = 100 * ($data['two_star_count'] / $total_reviews);
            //     $data['first_star_percentage'] = 100 * ($data['first_star_count'] / $total_reviews);
            // } else {
            //     $data['five_star_percentage'] = 0;
            //     $data['four_star_percentage'] = 0;
            //     $data['three_star_percentage'] = 0;
            //     $data['two_star_percentage'] = 0;
            //     $data['first_star_percentage'] = 0;
            // }

            // if($data['course']->average_rating >= 1){
            // }
       
            // if(!$data['reviews']->isEmpty()){
            //     $customerReviewsArray = array();
            //     foreach ($data['reviews'] as $key => $cust_review) {
            //         $customerReviewsArray[]=[
                     
            //             'id' => $cust_review->id,
            //             'user_id' => $cust_review->user?->id,
            //             'user_image' => asset($cust_review->user?->image_path),
            //             'user_name' => $cust_review->user?->name,
            //             'comment' => $cust_review->comment,
            //             'created_at' => $cust_review->created_at->diffForHumans(),
            //             'rating' => $cust_review->rating,      
            //         ];
            //     }
            // }
            // else{
            //     $customerReviewsArray = array();
            // }


            $checkReview = Review::where('user_id',$user->id)->where('course_id',$data['course']->id)->first();
            if($checkReview){
                $flag = false;

            }
            else{
                 $flag = true;
            }



                  // Base query
            $allReviewsQuery = Review::where('course_id', $data['course']->id);

            // Stats
            $total_reviews = $allReviewsQuery->count();
            $ratingCounts = [];
            for ($i = 1; $i <= 5; $i++) {
                $ratingCounts[$i] = (clone $allReviewsQuery)->where('rating', $i)->count();
            }

            $total_rating_sum = array_sum(array_map(fn($rating, $count) => $rating * $count, array_keys($ratingCounts), $ratingCounts));
            $average_rating = $total_reviews > 0 ? $total_rating_sum / $total_reviews : 0;

            $ratingPercentages = [];
            foreach ($ratingCounts as $stars => $count) {
                $ratingPercentages[$stars] = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
            }

            // User's review (if exists)
            if($user){
                $userReview = (clone $allReviewsQuery)->where('user_id', $user->id)->first();
            }
            else{
                $userReview = null;
            }
        
            $userReviewData = null;
            if ($userReview) {
                $userReviewData = [
                    'id' => $userReview->id,
                    'user_id' => $userReview->user?->id,
                    'user_image' => asset($userReview->user?->image_path),
                    'user_name' => $userReview->user?->name,
                    'comment' => $userReview->comment,
                    'created_at' => $userReview->created_at->diffForHumans(),
                    'rating' => $userReview->rating,
                ];
            }

            // Paginate other reviews
             if($user){
                $otherReviewsQuery = (clone $allReviewsQuery)->when($userReview, fn($q) => $q->where('user_id', '!=', $user->id));
                $paginatedReviews = $otherReviewsQuery->orderBy('id', 'DESC')->paginate(4);
             }
             else{
                $paginatedReviews = $allReviewsQuery->orderBy('id', 'DESC')->paginate(4);
             }
         
            $customerReviewsArray = $paginatedReviews->getCollection()->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user_id' => $review->user?->id,
                    'user_image' => asset($review->user?->image_path),
                    'user_name' => $review->user?->name,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at->diffForHumans(),
                    'rating' => $review->rating,
                ];
            })->toArray();

            // If user has a review, put it first
            if ($userReviewData) {
                array_unshift($customerReviewsArray, $userReviewData);
            }


              // Recalculate pagination meta so it accounts for user's review
            $paginationMeta = [
                'current_page' => $paginatedReviews->currentPage(),
                'last_page' => $paginatedReviews->lastPage(),
                'per_page' => $paginatedReviews->perPage(),
                'total' => $total_reviews,
                'has_more_pages' => $paginatedReviews->currentPage() < $paginatedReviews->lastPage(),
                'next_page_url' => $paginatedReviews->currentPage() < $paginatedReviews->lastPage()
                    ? $paginatedReviews->nextPageUrl()
                    : null,
            ];

             // Special adjustment for page 1 when user review is added on top
            if ($userReview && $paginatedReviews->currentPage() === 1) {
                $paginationMeta['has_more_pages'] = $total_reviews > ($paginatedReviews->perPage() + 1);
                $paginationMeta['next_page_url'] = $paginationMeta['has_more_pages']
                    ? $paginatedReviews->url(2)
                    : null;
            }


            $review_tab_array =[
                'can_review' => $flag,
                'average_rating' => number_format($average_rating, 1),
                'total_user_reviews' => $total_reviews,
                'five_star_percentage' => round($ratingPercentages[5], 2),
                'four_star_percentage' => round($ratingPercentages[4], 2),
                'three_star_percentage' => round($ratingPercentages[3], 2),
                'two_star_percentage' => round($ratingPercentages[2], 2),
                'first_star_percentage' => round($ratingPercentages[1], 2),
                'user_reviews' => $customerReviewsArray,
                'pagination' => $paginationMeta,
                'footer_section' => [
                    'enrolled_students_image' => $enrolled_student_image_array,
                    'total_enrolled_students' => $total_enrolled_students,
                    'course_last_update' => $data['course']->updated_at->format('d M Y'),
                ],
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
                        $quizStatus = __('UnAttempted');
                        $quizStatusNumber = 1;
                    }
                    else{
                        if(check_take_attempt_exam($quiz->id) == 'no'){
                            $resultURL = route('start-quiz-course');
                            $quizStatus = __('start_quiz');
                            $resultName = __('(Student)Start Quiz');
                            $quizStatus = __('Failed. ReAttempt');
                            $quizStatusNumber = 2;
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
                            $resultURL = route('quiz-result-course'); 
                            $quizStatus = __('quiz_finished');
                            $resultName = __('(Student)Complete Quiz Result');
                            $quizStatus = __('Passed');
                            $quizStatusNumber = 3;

                            $action_tab_api_routes = [
                                // 'see_result' => route('quiz-result-course'),
                                // 'leaderboard' => route('quiz-leaderboard-course')
                                'action_btn_1_api_route' => route('quiz-result-course'),
                                'action_btn_1_api_name' => __('(Student)Complete Quiz Result'),
                                'action_btn_2_api_route' => route('quiz-leaderboard-course'),
                                'action_btn_2_api_name' => __('(Student) Quiz LeaderBoard')
                            ];
                        }
                    }
                  
                    $quiz_tab_array[] = [
                        'quiz_id' => $quiz->id,
                        'quiz_uuid' => $quiz->uuid,
                        'quiz_name' => $quiz->name,
                        'quiz_type' => ucfirst(str_replace("_", " ", $quiz->type)),
                        'status_number' =>$quizStatusNumber,
                        'status_text' => $quizStatus,
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
                    $checkassignmentSubmit = AssignmentSubmit::whereUserId($user->id)
                    ->whereCourseId($data['course']->id)
                    ->whereAssignmentId($assignment->id)
                    ->first();
                    $student_marks = null;
                    $assignment_notes= null;
                    if($checkassignmentSubmit){
                        if($checkassignmentSubmit->marks){
                            $assignmentStatus = 3;
                            $assignmentStatusText = __('Checked');
                            $student_marks = $checkassignmentSubmit->marks;
                            $assignment_notes = $checkassignmentSubmit->notes;
                        }
                        $assignmentStatus = 2;
                        $assignmentStatusText = __('Submitted');
                    }
                    else{
                        $assignmentStatus = 1;
                        $assignmentStatusText = __('Pending');
                    }

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
                        'student_marks' => $student_marks,
                        'notes' => $assignment_notes,
                        'assignment_status_number' => $assignmentStatus,
                        'assignment_status_text' => $assignmentStatusText,
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
                    $content_tab_array = [
                        'course_content_view' =>  $course_content_view_section,
                        'course_content_progress' =>  $data['course_content_progress'],
                        'course_content_list_section' =>  $course_content_list_section,
                        'footer_section' => [
                            'enrolled_students_image' => $enrolled_student_image_array,
                            'total_enrolled_students' => $total_enrolled_students,
                            'course_last_update' => $data['course']->updated_at->format('d M Y'),
                        ],
                     
                     ];
                }
                else{
                    $content_tab_array = [
                        'course_content_list_section' =>  $course_content_list_section,
                        'footer_section' => [
                            'enrolled_students_image' => $enrolled_student_image_array,
                            'total_enrolled_students' => $total_enrolled_students,
                            'course_last_update' => $data['course']->updated_at->format('d M Y'),
                        ],
                     ];
                }
               
                if($data['course']->course_type == 1 || $data['course']->course_type == 2){
                    $CourseType = __('General');
                    $result_array = [
                        'pageTitle'=>$data['course']->title,
                        'course_type' => $CourseType,
                        'course_id' => $data['course']->id,
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        // 'navbar_header'=>$navbar_array,
                        'enrolled_students_image' => $enrolled_student_image_array,
                        'total_enrolled_students' => $total_enrolled_students,
                        'course_last_update' => $data['course']->updated_at->format('d M Y'),
                        'course_overview_tab' => $data['overview'],
                        'course_content_tab' => $content_tab_array,
                        'course_quiz_tab' => $quiz_tab_array,
                        'course_assignment_tab' => $assignment_tab_array,
                        'course_notice_tab' => $notice_tab_array,  
                        'course_liveclass_tab' => $liveclass_tab_array,
                        'course_discussion_tab' => $discussion_tab_array,
                        // 'course_certificate_tab' => $certificate_tab_array,
                        'course_review_tab' => $review_tab_array,
                    ];
                }
                else{
                    $CourseType = __('Live');
                    $result_array = [
                        'pageTitle'=>$data['course']->title,
                        'course_type' => $CourseType,
                        'course_id' => $data['course']->id,
                        'featured_image_or_video' => $featured_image_or_video,
                        'featured_image_or_video_type' => $featured_image_or_video_type,
                        // 'navbar_header'=>$navbar_array,
                        'enrolled_students_image' => $enrolled_student_image_array,
                        'total_enrolled_students' => $total_enrolled_students,
                        'course_last_update' => $data['course']->updated_at->format('d M Y'),
                        'course_overview_tab' => $data['overview'],
                        'course_content_tab' => $content_tab_array,
                        'course_notice_tab' => $notice_tab_array,
                        // 'course_liveclass_tab' => $liveclass_tab_array,
                        'course_discussion_tab' => $discussion_tab_array,
                        // 'course_certificate_tab' => $certificate_tab_array,
                        'course_review_tab' => $review_tab_array,
                    ];
                }
           
        return response()->json($result_array);
    } catch (\Throwable $th) {
        dd($th);
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
      
}

public function mySchedule()
{
    $user = Auth::user();
    if ($user) {
        $role = $user->role;
        if ($role != 3) { // Student
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
        }
    } else {
        return response()->json(['message' => 'User Is Not Logged In'], 200);
    }

    $studentIds = Student::where('user_id', Auth::id())->pluck('id');

    $startDate = Carbon::now()->startOfMonth(); // start of current month
    $endDate = Carbon::now()->addMonth()->endOfMonth(); // end of next month

    $classDetails = ClassDetail::whereIn('student_id', $studentIds)
        ->whereBetween('class_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->select('id', 'class_date', 'class_time', 'status')
        ->get();

    $completed = $classDetails->where('status', 'Completed')->values();
    $missed = $classDetails->where('status', 'Missed')->values();
    $upcoming = $classDetails->where('status', 'Scheduled')->values();

    return response()->json([
        'attend' => $completed,
        'missed' => $missed,
        'upcoming' => $upcoming,
    ]);
}


    public function billingHistory(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $role = $user->role;
            if ($role != 3) { // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        } else {
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        


        $errCode = $request->err_code;
        $errMsg = $request->err_msg;
          
        if(isset($errCode) && isset($errMsg)){
            if ($errCode == '001') { 
                // $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                // return redirect()->route('student.pending-payments.index');
                return response()->json(['message' => "<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>"], 302);    
            }
            else if($errCode == '90'){
                // $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                // return redirect()->route('student.pending-payments.index');
                return response()->json(['message' => "<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>"], 302);    
            }
        }


        // $billingHistory = DB::table('order_items')
        // ->join('users', 'order_items.user_id', '=', 'users.id')
        // ->join('orders', 'order_items.order_id', '=', 'orders.id')
        // ->join('courses', 'order_items.course_id', '=', 'courses.id') // Join courses table
        // ->where('order_items.user_id', Auth::id())
        // ->select(
        //     'orders.id as payment_id',
        //     'order_items.user_id',
        //     'users.name as user_name',
        //     'orders.grand_total',
        //     'orders.payment_status',
        //     'courses.title as course_name',
        //     DB::raw("DATE_FORMAT(orders.created_at, '%d-%m-%Y %h:%i %p') as issue_date")
        // )
        // ->get();
    
        $billingHistory = array();
        $student_user_id=$user->id;
        $getAllPendingPayments = CartManagement::where('user_id',$student_user_id)->where('created_by_type',2)
        ->orderBy('id','DESC')
        ->limit(10)
        ->get();

        if(!$getAllPendingPayments->isEmpty()){
            foreach ($getAllPendingPayments as $key => $payment) {
                $billingHistory[] = [
                    'payment_id' => $payment->id,
                    'user_id' =>  $payment->user?->id,
                    'user_name' =>  $payment->user?->name,
                    'grand_total' => $payment->price,
                    'payment_status' => 'due',
                    'course_name' => $payment->course?->title,
                    'issue_date' => date('d-m-Y h:i A',strtotime($payment->created_at)),
                ];
            }
        }
        return response()->json([
            'billing_history' => $billingHistory,
        ]);
    }

    public function getNextId($course_lecture_views, $course, $old_lecture, $enrollment)
    {
        $lecture = Course_lecture::where('lesson_id', $old_lecture->lesson_id)->where('id', '>', $old_lecture->id)->select('uuid', 'id', 'pre_ids', 'lesson_id')->first();
        $return = NULL;
        if ($lecture) {
            $return = $lecture;
        }

        return $return;
    }


    public function makePdfCertificate($course_id, $enrollment_id)
    {
        /** === make pdf certificate ===== */
        $course = Course::find($course_id);
        if (studentCourseProgress($course->id, $enrollment_id) == 100) {
            if (Certificate_by_instructor::where('course_id', $course->id)->count() > 0 && Student_certificate::where('course_id', $course->id)->where('user_id', auth()->id())->count() == 0) {
                $certificate_by_instructor = Certificate_by_instructor::where('course_id', $course->id)->orderBy('id', 'DESC')->first();
                $certificate = Certificate::find($certificate_by_instructor->certificate_id);
                if ($certificate) {
                    $certificate_number = $enrollment_id.'-'.mt_rand(1000000000, 9999999999);
                    $certificate->certificate_number = $certificate_number;
                    $html = view('frontend.student.course.certificate.pdf', ['certificate' => $certificate, 'certificate_by_instructor' => $certificate_by_instructor, 'course_title' => $course->title])->render();

                    $data = [
                        'html' => $html,
                        'certificate_number' => $certificate_number,
                    ];

                    return $data;
                }
            }
        }
        /** ------- end save certificate ----------- */
    }



   public function reviewListApi($course_id)
{
    $user = Auth::user();
    if (!$user) {
        return response()->json(['message' => 'User Is Not Logged In'], 302);
    }

    if ($user->role != 3) { // Student role check
        return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
    }

    $course = Course::find($course_id);
    if (!$course) {
        return response()->json(['message' => 'Invalid Course ID'], 302);
    }

    // Base query
    $allReviewsQuery = Review::where('course_id', $course_id);

    // Stats
    $total_reviews = $allReviewsQuery->count();
    $ratingCounts = [];
    for ($i = 1; $i <= 5; $i++) {
        $ratingCounts[$i] = (clone $allReviewsQuery)->where('rating', $i)->count();
    }

    $total_rating_sum = array_sum(array_map(fn($rating, $count) => $rating * $count, array_keys($ratingCounts), $ratingCounts));
    $average_rating = $total_reviews > 0 ? $total_rating_sum / $total_reviews : 0;

    $ratingPercentages = [];
    foreach ($ratingCounts as $stars => $count) {
        $ratingPercentages[$stars] = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
    }

    // User's review (if exists)
    $userReview = (clone $allReviewsQuery)->where('user_id', $user->id)->first();
    $userReviewData = null;
    if ($userReview) {
        $userReviewData = [
            'id' => $userReview->id,
            'user_id' => $userReview->user?->id,
            'user_image' => asset($userReview->user?->image_path),
            'user_name' => $userReview->user?->name,
            'comment' => $userReview->comment,
            'created_at' => $userReview->created_at->diffForHumans(),
            'rating' => $userReview->rating,
        ];
    }

    // Paginate other reviews
    $otherReviewsQuery = (clone $allReviewsQuery)
        ->when($userReview, fn($q) => $q->where('user_id', '!=', $user->id));

     $paginatedReviews = $otherReviewsQuery->orderBy('id', 'DESC')->paginate(4);

    $customerReviewsArray = $paginatedReviews->getCollection()->map(function ($review) {
        return [
            'id' => $review->id,
            'user_id' => $review->user?->id,
            'user_image' => asset($review->user?->image_path),
            'user_name' => $review->user?->name,
            'comment' => $review->comment,
            'created_at' => $review->created_at->diffForHumans(),
            'rating' => $review->rating,
        ];
    })->toArray();

    // If user has a review, put it first
    if ($userReviewData) {
        array_unshift($customerReviewsArray, $userReviewData);
    }

     // Recalculate pagination meta so it accounts for user's review
    $paginationMeta = [
        'current_page' => $paginatedReviews->currentPage(),
        'last_page' => $paginatedReviews->lastPage(),
        'per_page' => $paginatedReviews->perPage(),
        'total' => $total_reviews,
        'has_more_pages' => $paginatedReviews->currentPage() < $paginatedReviews->lastPage(),
        'next_page_url' => $paginatedReviews->currentPage() < $paginatedReviews->lastPage()
            ? $paginatedReviews->nextPageUrl()
            : null,
    ];

     // Special adjustment for page 1 when user review is added on top
    if ($userReview && $paginatedReviews->currentPage() === 1) {
        $paginationMeta['has_more_pages'] = $total_reviews > ($paginatedReviews->perPage() + 1);
        $paginationMeta['next_page_url'] = $paginationMeta['has_more_pages']
            ? $paginatedReviews->url(2)
            : null;
    }


    // Build response
    $review_tab_array = [
        'average_rating' => number_format($average_rating, 1),
        'total_user_reviews' => $total_reviews,
        'five_star_percentage' => round($ratingPercentages[5], 2),
        'four_star_percentage' => round($ratingPercentages[4], 2),
        'three_star_percentage' => round($ratingPercentages[3], 2),
        'two_star_percentage' => round($ratingPercentages[2], 2),
        'first_star_percentage' => round($ratingPercentages[1], 2),
        'user_reviews' => $customerReviewsArray,
        'pagination' => $paginationMeta,
    ];

    return response()->json($review_tab_array);
}




    // public function reviewListApi($course_id){
     
    //     $user = Auth::user();
    //     if($user){
    //         $role = $user->role;
    //         if($role != 3){ // Student
    //             return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
    //         }
    //     }
    //     else{
    //         return response()->json(['message' => 'User Is Not Logged In'], 302);
    //     }

    //     $course = Course::where('id',$course_id)->first();
    //     if($course == null){
    //          return response()->json(['message' => 'Invalid Course ID'], 302);
    //     }
      
    //     $review_tab_array = array();
    
    //      // Fetch total reviews for stats (all reviews, not paginated)
    //         $allReviewsQuery = Review::where('course_id', $course_id);
    //         $total_reviews = $allReviewsQuery->count();

    //          // Stats calculation
    //         $five_star = $allReviewsQuery->clone()->where('rating', 5)->count();
    //         $four_star = $allReviewsQuery->clone()->where('rating', 4)->count();
    //         $three_star = $allReviewsQuery->clone()->where('rating', 3)->count();
    //         $two_star = $allReviewsQuery->clone()->where('rating', 2)->count();
    //         $one_star = $allReviewsQuery->clone()->where('rating', 1)->count();

    //         $total_user_review = $total_reviews;
    //         $total_rating_sum = (5 * $five_star) + (4 * $four_star) + (3 * $three_star) + (2 * $two_star) + (1 * $one_star);
    //         $average_rating = $total_user_review > 0 ? $total_rating_sum / $total_user_review : 0;

    //          // Percentages
    //         $five_star_percent = $total_reviews > 0 ? ($five_star / $total_reviews) * 100 : 0;
    //         $four_star_percent = $total_reviews > 0 ? ($four_star / $total_reviews) * 100 : 0;
    //         $three_star_percent = $total_reviews > 0 ? ($three_star / $total_reviews) * 100 : 0;
    //         $two_star_percent = $total_reviews > 0 ? ($two_star / $total_reviews) * 100 : 0;
    //         $one_star_percent = $total_reviews > 0 ? ($one_star / $total_reviews) * 100 : 0;


    //         // Get logged-in user's review (if exists)
    //         $userReview = null;
          

         
    //          if ($userReviewRecord = $allReviewsQuery->clone()->where('user_id', $user->id)->first()) {
    //             $userReview = [
    //                 'id' => $userReviewRecord->id,
    //                 'user_id' => $userReviewRecord->user?->id,
    //                 'user_image' => asset($userReviewRecord->user?->image_path),
    //                 'user_name' => $userReviewRecord->user?->name,
    //                 'comment' => $userReviewRecord->comment,
    //                 'created_at' => $userReviewRecord->created_at->diffForHumans(),
    //                 'rating' => $userReviewRecord->rating,
    //             ];
    //         }
              
    //         $otherReviewsQuery = $allReviewsQuery->where('user_id', '!=', $user->id); // Others
    //         // Paginate other reviews (4 per page)
    //         $paginatedReviews = $otherReviewsQuery->orderBy('id', 'DESC')->paginate(4);
          
          
    //          // Format paginated reviews
    //         $customerReviewsArray = $paginatedReviews->getCollection()->map(function ($review) {
    //             return [
    //                 'id' => $review->id,
    //                 'user_id' => $review->user?->id,
    //                 'user_image' => asset($review->user?->image_path),
    //                 'user_name' => $review->user?->name,
    //                 'comment' => $review->comment,
    //                 'created_at' => $review->created_at->diffForHumans(),
    //                 'rating' => $review->rating,
    //             ];
    //         })->toArray();

            
           
    //         // Prepend user's own review if exists
    //         if ($userReview) {
    //             array_unshift($customerReviewsArray, $userReview);
    //         }
         
    //         // Final review tab array
    //         $review_tab_array = [
    //             'average_rating' => number_format($average_rating, 1),
    //             'total_user_reviews' => $total_user_review,
    //             'five_star_percentage' => round($five_star_percent, 2),
    //             'four_star_percentage' => round($four_star_percent, 2),
    //             'three_star_percentage' => round($three_star_percent, 2),
    //             'two_star_percentage' => round($two_star_percent, 2),
    //             'first_star_percentage' => round($one_star_percent, 2),
    //             'user_reviews' => $customerReviewsArray,
    //             'pagination' => [
    //                 'current_page' => $paginatedReviews->currentPage(),
    //                 'last_page' => $paginatedReviews->lastPage(),
    //                 'per_page' => $paginatedReviews->perPage(),
    //                 'total' => $paginatedReviews->total(),
    //                 'has_more_pages' => $paginatedReviews->hasMorePages(),
    //                 'next_page_url' => $paginatedReviews->nextPageUrl(),
    //             ],
    //         ];
    //          return response()->json($review_tab_array);
         
      
   
    // }

    public function reviewCreateApi(Request $request)
    {
        try {
            //code...
      
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }


        $messages = [
            'course_id.required' => __('The Course ID Is Required'),
            'rating.required' => __('The Rating Field Is Required'),
            'comment.required' => __('The Comment Field Is Required'),
        ];
    
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],302);
        }

        $checkCourse = Course::where('id',$request->course_id)->first();
        if($checkCourse == null){
           
            return response()->json([
                'status' => 302,
                'message' => 'Given Course ID Does Not Exists.'
            ],302);
        }


        $review_exists_user = Review::whereUserId(auth()->id())->whereCourseId($request->course_id)->first();
        if ($review_exists_user) {
            return response()->json([
                'status' => 302,
                'message' => 'Already you have reviewed. Thank you.'
            ],302);
        }
      
      

        $review = new Review();
        $review->user_id = Auth::user()->id;
        $review->course_id = $request->course_id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        // Review Calculation and Update

        $data['five_star_count'] = Review::whereCourseId($request->course_id)->whereRating(5)->count();
        $data['four_star_count'] = Review::whereCourseId($request->course_id)->whereRating(4)->count();
        $data['three_star_count'] = Review::whereCourseId($request->course_id)->whereRating(3)->count();
        $data['two_star_count'] = Review::whereCourseId($request->course_id)->whereRating(2)->count();
        $data['first_star_count'] = Review::whereCourseId($request->course_id)->whereRating(1)->count();

        $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
            (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
        $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];

        if ($data['total_user_review'] > 0) {
            $average_rating = $data['total_reviews'] / $data['total_user_review'];
        } else {
            $average_rating = 0;
        }

        $course = Course::findOrFail($request->course_id);
        $course->average_rating = number_format($average_rating, 1);
        $course->save();

        // End:: Review Calculation and Update

        return response()->json([
            'status' => 200,
            'message' => 'Review Created Successfully.'
        ],200);
    } catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
    }


    public function startCourseQuizApi(Request $request, $uuid)
    {
        try {
        
            $result_array = array();
            $ques_array = array();
            $ques_opt_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $checkExam = Exam::where('uuid',$uuid)->first();
            if($checkExam == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Exam Uuid Does Not Exists.'
                ]);
            }
        

         
            if (Take_exam::whereUserId($user->id)->whereExamId($checkExam->id)->count() == 0) {
           
                $take_exam = new Take_exam();
                $take_exam->exam_id = $checkExam->id;
                $take_exam->save();
            }
            else{
            
                $takeExam = Take_exam::whereUserId($user->id)->whereExamId($checkExam->id)->orderBy('id', 'DESC')->first();
                $isPassed = $takeExam->passed;
           
                if(!$isPassed){
                    $totalQuestions = Question::whereExamId($checkExam->id)->count();
                    $attemptedAnswers = Answer::whereUserId($user->id)->whereExamId($checkExam->id)
                    ->where('take_exam_id',$takeExam->id)->count();
                    if($attemptedAnswers == $totalQuestions){
                        $take_exam = new Take_exam();
                        $take_exam->exam_id = $checkExam->id;
                        $take_exam->save();
                    }
                }
           
            }

            $data['take_exam'] = Take_exam::whereUserId($user->id)->whereExamId($checkExam->id)->orderBy('id', 'DESC')->first();

            $question_ids = Answer::whereUserId($user->id)->whereExamId($checkExam->id)
            ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();
            $data['question'] = Question::whereExamId($checkExam->id)->whereNotIn('id', $question_ids)->first();

            $data['number_of_answer'] = Answer::whereUserId($user->id)->whereExamId($checkExam->id)
            ->where('take_exam_id',$data['take_exam']->id)->count();


            $now = Carbon::now();
            $expend_second = $now->diffInSeconds($data['take_exam']->created_at);
        
            if (Carbon::parse($checkExam->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($checkExam->duration * 60)->format('H:i:s')) {
                $checkAttempt=check_take_attempt_exam($checkExam->id);
                if($checkAttempt == 'yes'){
                    return response()->json([
                        'status' => 200,
                        'message' => 'Given Student Has Already Attempted And Passed This Quiz.'
                    ]);
                }
                else{
                    return response()->json($data);
                }
            }
            else{
                $checkAttempt=check_take_attempt_exam($checkExam->id);
                if($checkAttempt == 'yes'){
                    return response()->json([
                        'status' => 200,
                        'message' => 'Given Student Has Already Attempted And Passed This Quiz.'
                    ]);
                }
                else{
                    return response()->json($data);
                }
            }


      
        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }



       public function discussionListApi($course_id){
     
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        $checkCourse = Course::where('id',$course_id)->first();
        if($checkCourse == null){
             return response()->json(['message' => 'Invalid Course ID'], 302);
        }
      
        $discussion_tab_array = array();
    
            //////////////////////////DISCUSSION TAB SECTION START/////////////////////////////
             $data['discussions'] = Discussion::whereCourseId($course_id)->whereNull('parent_id')->active()->get();
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


        
    
        return response()->json($discussion_tab_array);

      
   
    }


    public function createDiscussionApi(Request $request)
    {
        try {
         
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }


        $messages = [
            'course_id.required' => __('The Course ID Is Required'),
            'discussion_comment.required' => __('The Discussion Comment Field Is Required'),
        ];
    
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'discussion_comment' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }
      
        $checkCourse = Course::where('id',$request->course_id)->first();
        if($checkCourse == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Course ID Does Not Exists.'
            ]);
        }
        $discussion = new Discussion();
        $discussion->user_id = $user->id;
        $discussion->course_id = $request->course_id;
        $discussion->comment = $request->discussion_comment;
        $discussion->status = 1;
        $discussion->comment_as = 2;   //Student
        $discussion->save();


        return response()->json([
            'status' => 200,
            'message' => 'Discussion Created Successfully.'
        ]);
    } catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
    }


    public function createDiscussionReplyApi(Request $request)
    {
        try {
         
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }


        $messages = [
            'discussion_id.required' => __('The Discussion ID Is Required'),
            'course_id.required' => __('The Course ID Is Required'),
            'reply_comment.required' => __('The Reply Comment Field Is Required'),
        ];
    
        $validator = Validator::make($request->all(), [
            'discussion_id' => 'required',
            'course_id' => 'required',
            'reply_comment' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }
        $checkDiscussion = Discussion::where('id',$request->discussion_id)->first();
        if($checkDiscussion == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Discussion ID Does Not Exists.'
            ]);
        }

        $checkCourse = Course::where('id',$request->course_id)->first();
        if($checkCourse == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Course ID Does Not Exists.'
            ]);
        }
        $discussion = new Discussion();
        $discussion->user_id = $user->id;
        $discussion->course_id = $request->course_id;
        $discussion->comment = $request->reply_comment;
        $discussion->status = 1;
        $discussion->parent_id = $request->discussion_id;
        $discussion->comment_as = 2;
        $discussion->save();

        Discussion::where('id', $request->discussion_id)
            ->update([
                'view' => 2
            ]);
        Discussion::where('parent_id', $request->discussion_id)->update([
            'view' => 2
        ]);



        return response()->json([
            'status' => 200,
            'message' => 'Discussion Reply Created Successfully.'
        ]);
    } catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
    }



    public function viewAssignmentsListApi(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $messages = [
                'course_id.required' => __('The Course ID Field Is Required'),
                'course_id.numeric' => __('The Course ID Must Be An Integer'),
            ];
        
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|numeric',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $checkCourse = Course::where('id',$request->course_id)->first();
            if( $checkCourse  == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Course ID Does Not Exists.'
                ]);
            }

            $assignments = Assignment::where('course_id', $request->course_id)->get();
            if(count($assignments)){
                foreach ($assignments as $key => $assignment) {
                    $action_btn_routes = [
                        'view_details'=> route('assignment-detail-course'),
                        'see_result'=> route('assignment-result-course'),
                    ];
                    $result_array[] = [
                        'assignment_id' => $assignment->id,
                        'assignment_topic' => Str::limit($assignment->name, 50),
                        'assignment_total_marks' => $assignment->marks,
                        'action_btn_api_routes'=> $action_btn_routes,
                    ];
                }
            }
            else{
                $result_array = [
                    __('Empty Assignments')
                ];
            }
            return response()->json($result_array); 
        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }



    public function assignmentDetailApi(Request $request)
    {
        try {
        
            $result_array = array();
            $ques_array = array();
            $ques_opt_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $messages = [
                'assignment_id.required' => __('The Assignment ID Field Is Required'),
                'assignment_id.numeric' => __('The Assignment ID Must Be An Integer'),
            ];
        
            $validator = Validator::make($request->all(), [
                'assignment_id' => 'required|numeric',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $checkAssignment = Assignment::where('id',$request->assignment_id)->first();
            if( $checkAssignment  == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Assignment ID Does Not Exists.'
                ]);
            }

            $assignment_id = $request->assignment_id;
            
            if($request->lecture_id){
                $lecture_id  = $request->lecture_id;
            }
            else{
                $lecture_id  = null;
            }
            $lectureID = $lecture_id;
            $assignment = Assignment::whereCourseId($checkAssignment->course_id)->whereId($assignment_id)->first();
            $assignmentSubmit = AssignmentSubmit::whereUserId($user->id)->whereCourseId($checkAssignment->course_id)->whereAssignmentId($assignment_id)->first();
            
            $action_routes = [
                'back'=> route('course-viewassignmentslist-student'),
                'submit_assignment'=> route('course-submit-assignment-store-student'),
            ];

            $result_array = [
                'lecture_id' => $lectureID,
                'assignment_id' => $assignment->id,
                'assignment_topic' => $assignment->name,
                'assignment_description' => $assignment->description,
                'marks' => $assignment->marks,
                'assignment_file' => getVideoFile($assignment->file),
                'assignment_file_name' => $assignment->original_filename,
                'your_submitted_file' => $assignmentSubmit ? getVideoFile($assignmentSubmit->file) : null,
                'your_submitted_file_name' => $assignmentSubmit ? $assignmentSubmit->original_filename : null,
                'action_api_routes'=> $action_routes,
            ];
            return response()->json($result_array);

         
        
          
        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


    public function assignmentResultApi(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $messages = [
                'assignment_id.required' => __('The Assignment ID Field Is Required'),
                'assignment_id.numeric' => __('The Assignment ID Must Be An Integer'),
            ];
        
            $validator = Validator::make($request->all(), [
                'assignment_id' => 'required|numeric',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $checkAssignment = Assignment::where('id',$request->assignment_id)->first();
            if( $checkAssignment  == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Assignment ID Does Not Exists.'
                ]);
            }

            $assignment_id = $request->assignment_id;
            
            if($request->lecture_id){
                $lecture_id  = $request->lecture_id;
            }
            else{
                $lecture_id  = null;
            }
            $lectureID = $lecture_id;
            $assignment = Assignment::whereCourseId($checkAssignment->course_id)->whereId($assignment_id)->first();
            $assignmentSubmit = AssignmentSubmit::whereUserId($user->id)->whereCourseId($checkAssignment->course_id)->whereAssignmentId($assignment_id)->first();
            
            $action_routes = [
                'back'=> route('course-viewassignmentslist-student'),
            ];

            $result_array = [
                'lecture_id' => $lectureID,
                'assignment_id' => $assignment->id,
                'assignment_topic' => Str::limit($assignment->name, 40),
                'assignment_total_marks' => $assignment->marks,
                'your_marks' => $assignmentSubmit ? $assignmentSubmit->marks : null,
                'notes' => $assignmentSubmit ? $assignmentSubmit->notes : null,
                'action_api_routes'=> $action_routes,
            ];

            return response()->json($result_array);
    
        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


    public function assignmentSubmitStoreApi(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            

            $messages = [
                'course_id.required' => __('The Course ID Field Is Required'),
                'assignment_id.required' => __('The Assignment ID Field Is Required'),
                'file.required' => __('The File Field Is Required'),
                'file.mimes' => __('The File Field Must Be OF Type "PDF" or "Zip"'),
            ];
        
            $validator = Validator::make($request->all(), [
                'course_id' => 'required',
                'assignment_id' => 'required',
                'file' => 'required|mimes:pdf,zip'
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
        
            $checkCourse = Course::where('id',$request->course_id)->first();
            if( $checkCourse  == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Course ID Does Not Exists.'
                ]);
            }

            $checkAssignment = Assignment::where('id',$request->assignment_id)->first();
            if( $checkAssignment  == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Assignment ID Does Not Exists.'
                ]);
            }
        
            if($request->lecture_id){
                $lecture_id  = $request->lecture_id;
            }
            else{
                $lecture_id  = null;
            }
        

            $courseData = Course::where('id',$request->course_id)->first();
            $lectureID = $request->lecture_id;
       
         
            if ($request->hasFile('file')) {
                $assignmentSubmit = AssignmentSubmit::whereUserId($user->id)->whereCourseId($request->course_id)->whereAssignmentId($request->assignment_id)->first();
           
                if (!$assignmentSubmit) {
                    $assignmentSubmit = new AssignmentSubmit();
                } else {
                    $this->deleteFile($assignmentSubmit->file);
                }
              
                $assignmentSubmit->course_id = $request->course_id;
                $assignmentSubmit->assignment_id = $request->assignment_id;
           
                $file_details = $this->uploadFileWithDetails('assignment_submit', $request->file);
                if (!$file_details['is_uploaded']) {
                    return response()->json([
                        'message' => 'Something went wrong! Failed to upload file',
                        'status' => 302
                    ]);
                }
             
                $assignmentSubmit->file = $file_details['path'];
                $assignmentSubmit->original_filename = $file_details['original_filename'];
              
                $assignmentSubmit->save();
            }

            $result_array['assignment_id'] = $request->assignment_id;
            $result_array['assignment'] = Assignment::whereCourseId($request->course_id)->whereId($request->assignment_id)->first();
            $result_array['assignmentSubmit'] = AssignmentSubmit::whereUserId($user->id)->whereCourseId($request->course_id)->whereAssignmentId($request->assignment_id)->first();
            if($lectureID != null){
                $Base_Route = route('assignment-detail-course');
                // $getLecture = Course_lecture::where('uuid',$lectureUUID)->first();
                $getLecture = Course_lecture::where('id',$lectureID)->first();
                if($getLecture){
                    if ($getLecture->type == 'assignment' || $getLecture->type == 'Assignment') {
                        $lecture = Course_lecture::find($getLecture->id);
                        if ($lecture) {
                            if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                $latest_active_enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $lecture->course_id)->orderBy('id','DESC')->first();
                                if($latest_active_enrollment){
                                    $enrollment_id = $latest_active_enrollment->id;
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
                    }
                }  

                $result_array['lectureID'] = $lecture_id;
                return response()->json([
                    'message' => 'Assignment Submitted Successfully.',
                    'data' => $result_array,
                    'returnApiRoute'=>$Base_Route,
                    'status' => 200
                ]);
            }
            else{
                $Base_Route = route('assignment-detail-course');
                return response()->json([
                    'message' => 'Assignment Submitted Successfully.',
                    'data' => $result_array,
                    'returnApiRoute'=>$Base_Route,
                    'status' => 200
                ]);
            }
            
        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


     public function clickLectureApi(Request $request)
    {
        try {
            $result_array = array();
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }


        $messages = [
            'lecture_id.required' => __('The Lecture ID Is Required'),
            'lecture_id.numeric' => __('The Lecture ID Must Be An Integer'),
        ];
    
        $validator = Validator::make($request->all(), [
            'lecture_id' => 'required|numeric',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }
      
        $lecture = Course_lecture::where('id',$request->lecture_id)->first(); //Get Lecture Data Through Given Lecture ID    
        if($lecture == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Lecture ID Does Not Exists.'
            ]);
        }
    
        $courseID =  $lecture->course_id;
        $course = Course::where('id',$courseID)->first();
        $course_lecture_views = Course_lecture_views::where('course_id', $courseID)->where('user_id', $user->id)->get();
        $isFirst = count($course_lecture_views) ? false : true;  // Count Current User Total Views For Lectures OF gIVEN Course
        $enrollment = Enrollment::where(['course_id' => $course->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
     
        if(!checkStudentCourseIsLock( $course_lecture_views, $course, $lecture, $enrollment, $isFirst)){
            $nextLecture = $this->getNextId($course_lecture_views, $course,  $lecture, $enrollment);
            if ($nextLecture) {
                $nextLectureUuid = $nextLecture->uuid;
            } else {
                $nextLectureUuid = null;
            }

           
            if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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
                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                $video_src =  $lecture->resource?->file_name;
                $lecture_preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
            } 
            elseif ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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
                // $youtube_video_src = $lecture->resource?->youtube_url_path;
                if (Str::contains($lecture->resource?->youtube_url_path, 'https://www.youtube.com')) {
                    $lecture_preview_src = $lecture->resource?->youtube_url_path;    
                }
                else if(Str::contains($lecture->resource?->youtube_url_path, 'https://youtu.be')){
                    $lecture_preview_src = $lecture->resource?->youtube_url_path;    
                }
                else{
                    $lecture_preview_src = "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
                }
            
                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
            } 
            elseif ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                $text_src = $lecture->resource?->text;
                $lecture_preview_src =  $lecture->resource?->text;
            } 
            elseif ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                 
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $data['image_src'] = $lecture->image;
                $image_src =  $lecture->resource?->file_name;
                $lecture_preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
            
            } 
            elseif ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $pdf_src = $lecture->resource?->file_name;
                $lecture_preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // return redirect(getImageFile($pdf_src));
            } 
            elseif ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $slide_document_src = $lecture->resource?->file_name;
                $lecture_preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name.'/index.html';
                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
            } 
            elseif ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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
                $audio_src = $lecture->resource?->file_name;
                $lecture_preview_src =  asset('uploads/course_resource').'/'.$lecture->resource?->file_name;
                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
            }
            elseif($lecture->quiz){
                    $quizResponse = $this->checkQuizCompletion($lecture,$user,$enrollment);
                  

                    // $data['exam'] = Exam::whereUuid($lecture->quiz->uuid)->first();
                    // if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() > 0) { // User Has Already Attempted This Quiz Previously Multiple Times
                    //     // Now From That Multiple Attempts Get Data OF Latest Attempt
                    //     $take_exam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                    //     $take_exam->lecture_id = $lecture->id;
                       
                    //     $answered_question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                    //     $total_questions = Question::whereExamId($data['exam']->id)->count();
            
                    //     $number_of_attempted_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                    //     ->where('take_exam_id',$take_exam->id)->count();
    
                    //     if($number_of_attempted_answers == $total_questions){  // USER Attempted All Question Before EndTime
                    //         if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                    //             //NEW CODE
                    //             if($lecture->quiz){
                    //                 $quizPassedPercentage = $lecture->quiz->passing_percentage;
                    //                 $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                    //                 ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                    //                 $cal_percentage = ($correct_answers/$total_questions)*100;
                    //                 $cal_percentage = (int)$cal_percentage;
                    //                 if($cal_percentage >= $quizPassedPercentage){
                    //                     $lecture = Course_lecture::find($lecture->id);
                    //                     if ($lecture) {
                    //                         if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                    //                             if($enrollment){
                    //                                 $enrollment_id = $enrollment->id;
                    //                             }
                    //                             else{
                    //                                 $enrollment_id = null;
                    //                             }
                    //                             $course_lecture_views = new Course_lecture_views();
                    //                             $course_lecture_views->course_id = $lecture->course_id;
                    //                             $course_lecture_views->course_lecture_id = $lecture->id;
                    //                             $course_lecture_views->enrollment_id = $enrollment_id;
                    //                             $course_lecture_views->save();
                    //                         }
                    //                         $take_exam->obtained_percentage = $cal_percentage;
                    //                         $take_exam->passing_percentage = $quizPassedPercentage;
                    //                         $take_exam->passed = 1;
                    //                         $take_exam->update();
                    //                     }
                    //                 }
                    //                 else{
                    //                     $take_exam->obtained_percentage = $cal_percentage;
                    //                     $take_exam->passing_percentage = $quizPassedPercentage;
                    //                     $take_exam->passed = 0;
                    //                     $take_exam->update();
                    //                 }
                    //             }
                    //         }
                    //     }
                    //     else{  // Quiz Time Is Ended
                    //         $now = Carbon::now();
                    //         $expend_second = $now->diffInSeconds($take_exam->created_at);
                    //         if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                    //             if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                    //                 //NEW CODE
                    //                 if($lecture->quiz){
                    //                     $quizPassedPercentage = $lecture->quiz->passing_percentage;
                    //                     $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                    //                     ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                    //                     $cal_percentage = ($correct_answers/$total_questions)*100;
                    //                     $cal_percentage = (int)$cal_percentage;
                    //                     if($cal_percentage >= $quizPassedPercentage){
                    //                         $lecture = Course_lecture::find($lecture->id);
                    //                         if ($lecture) {
                                            
                    //                             if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                    //                                 if($enrollment){
                    //                                     $enrollment_id = $enrollment->id;
                    //                                 }
                    //                                 else{
                    //                                     $enrollment_id = null;
                    //                                 }
                    //                                 $course_lecture_views = new Course_lecture_views();
                    //                                 $course_lecture_views->course_id = $lecture->course_id;
                    //                                 $course_lecture_views->course_lecture_id = $lecture->id;
                    //                                 $course_lecture_views->enrollment_id = $enrollment_id;
                    //                                 $course_lecture_views->save();
                                                 
                    //                             }
                    //                             $take_exam->obtained_percentage = $cal_percentage;
                    //                             $take_exam->passing_percentage = $quizPassedPercentage;
                    //                             $take_exam->passed = 1;
                    //                             $take_exam->update();
                    //                         }
                    //                     }
                    //                     else{
                    //                         $take_exam->obtained_percentage = $cal_percentage;
                    //                         $take_exam->passing_percentage = $quizPassedPercentage;
                    //                         $take_exam->passed = 0;
                    //                         $take_exam->update();
                    //                     }   
                    //                 }
                                   
                    //         }
                    //         }
                    //     } 
                    //     $take_exam->update();
                    // }  
                    
                    

            }
            elseif ($lecture->type == 'Video' || $lecture->type == 'video') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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
                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $lecture_preview_src =  "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
                // $video_src =  $lecture->resource?->file_name;
                $lecture_preview_src =  asset($lecture->file_path);
         
            } 
            elseif ($lecture->type == 'Youtube' || $lecture->type == 'youtube') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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
                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $lecture_preview_src =  "https://www.youtube.com/embed/".$lecture->resource?->youtube_url_path;
                // $video_src =  $lecture->resource?->file_name;
                $lecture_preview_src =  $lecture->url_path;
         
            } 
            elseif ($lecture->type == 'Image' || $lecture->type == 'image') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                 
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $data['image_src'] = $lecture->image;
                // $image_src =  $lecture->resource?->file_name;
                $lecture_preview_src = asset($lecture->image);
         
            }  
            elseif ($lecture->type == 'PDF' || $lecture->type == 'pdf') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                 
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $data['image_src'] = $lecture->image;
                // $image_src =  $lecture->resource?->file_name;
                $lecture_preview_src = asset($lecture->pdf);
         
            }   
            elseif ($lecture->type == 'Slide Document' || $lecture->type == 'slide_document') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                 
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $data['image_src'] = $lecture->image;
                // $image_src =  $lecture->resource?->file_name;
                // $lecture_preview_src = asset($lecture->pdf);
                $lecture_preview_src = $lecture->slide_document;        
            }  
            elseif ($lecture->type == 'Audio' || $lecture->type == 'audio') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                 
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $data['image_src'] = $lecture->image;
                // $image_src =  $lecture->resource?->file_name;
                $lecture_preview_src = asset($lecture->audio);
         
            }   
            elseif ($lecture->type == 'Text' || $lecture->type == 'text') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                 
                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                        if($enrollment){
                            $enrollment_id = $enrollment->id;
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

                $certificateData = $this->makePdfCertificate($lecture->course_id, $enrollment->id);
                $certificateData = ($certificateData == NULL) ? [] : $certificateData;
                // $data['image_src'] = $lecture->image;
                // $image_src =  $lecture->resource?->file_name;
                $lecture_preview_src = $lecture->text;
         
            }     
        }

        if($lecture->resource){     //Lecture Is Resource Type
            // if ($lecture->resource->type == 'Text' || $lecture->resource->type == 'Youtube'){
            //     $resultURL = route('student.resource.view_full_other', ['resource_id'=>$lecture->resource->id , 'lecture_id'=>$lecture->id]);
            // }
            // else{
            //     $resultURL = route('student.resource.view_full', ['ext'=>$lecture->resource->file_extension , 'path'=> $lecture->resource->file_name ,'lecture_id'=>$lecture->id]);
            // }
            $result_array = [
                'lecture_id' => $lecture->id,
                'lecture_type' => $lecture->type,
                'lecture_resource_id' => $lecture->resource->id,
                'lecture_resource_type' => $lecture->resource->type,
                'lecture_preview_src' => $lecture_preview_src,
                // 'redirect_url' => $resultURL,
            ];
        }
        elseif($lecture->quiz_id != null){  //Lecture Is Quiz Type
            if(checkStudentCourseView($course_lecture_views,$course->id,$lecture->id)){
                $resultURL = route('quiz-result-course');
                $quizStatus = __('quiz_finished');
                $resultName = __('(Student)Complete Quiz Result');
            }
            else if(check_take_attempt_exam($lecture->quiz_id) == 'no'){
                $resultURL = route('start-quiz-course');
                $quizStatus = __('start_quiz');
                $resultName = __('(Student)Start Quiz');
            }
            else{
                $resultURL = route('quiz-result-course'); 
                $quizStatus = __('quiz_finished');
                $resultName = __('(Student)Complete Quiz Result');
            }
            $result_array = [
                'lecture_id' => $lecture->id,
                'lecture_type' => $lecture->type,
                // 'quiz_uuid' => $lecture->quiz?->uuid,
                'quiz_id' => $lecture->quiz?->id,
                'quiz_status' => $quizStatus,
                'redirect_api_url' => $resultURL,
                'redirect_api_name' => $resultName,
            ];
        }
        elseif($lecture->assignment_id != null){  //Lecture Is Assignment Type
            if(checkStudentCourseView($course_lecture_views,$course->id,$lecture->id)){
                $resultURL = route('assignment-result-course');
            }
            else{
                $resultURL = route('assignment-detail-course'); 
            }
            $result_array = [
                'lecture_id' => $lecture->id,
                'lecture_type' => $lecture->type,
                'assignment_id' => $lecture->assignment_id,
                'redirect_api_url' => $resultURL,
            ];
        }
        else{  //Lecture Is Other Type
          
            $result_array = [
                'lecture_id' => $lecture->id,
                'lecture_type' => $lecture->type,
                'lecture_preview_src' => $lecture_preview_src,
            ];
        }

    
        return response()->json([
            'status' => 200,
            'data' => $result_array,
            // 'message' => 'Discussion Created Successfully.'
        ]);
    } catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
    }


    public function startQuizApi(Request $request)
    {
        try {
            $result_array = array();
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }


        $messages = [
            'quiz_uuid.required' => __('The Quiz UUID Is Required'),
        ];
    
        $validator = Validator::make($request->all(), [
            'quiz_uuid' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }
        $uuid = $request->quiz_uuid;
        $checkExam = Exam::where('uuid',$uuid)->first();
        if($checkExam == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Quiz Uuid Does Not Exists.'
            ]);
        }
    
        if($request->lecture_id){
            $lecture_id  = $request->lecture_id;
            $lecture = Course_lecture::where('id',$request->lecture_id)->first(); //Get Lecture Data Through Given Lecture ID    
            if($lecture == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Lecture ID Does Not Exists.'
                ]);
            }
        }
        else{
            $lecture_id  = null;
        }
        $lectureID = $lecture_id;
        $courseExam = Course::where('id',$checkExam->course_id)->first();
        $data['exam'] = Exam::whereUuid($request->quiz_uuid)->first();
   
        if($request->answer_id){
            $answer = Answer::find($request->answer_id);
            if($answer == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Answer ID Does Not Exists.'
                ]);
            }
        }
        else{
            $answer = null;
        }
       

            if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() == 0) {
                $take_exam = new Take_exam();
                $take_exam->exam_id = $data['exam']->id;
                $take_exam->save();
            }
            else{
                $takeExam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                $now = Carbon::now();
                $expend_second = $now->diffInSeconds($takeExam->created_at);
                $isPassed = $takeExam->passed;
                if(!$isPassed){   // Means Previous Test Is Failed Or Not Passed Yet
                    $totalQuestions = Question::whereExamId($data['exam']->id)->count();
                    $attemptedAnswers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$takeExam->id)->count();
                    if($attemptedAnswers == $totalQuestions){
                        $take_exam = new Take_exam();
                        $take_exam->exam_id = $data['exam']->id;
                        $take_exam->save();
                    }
                    else if(Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                        if(check_take_attempt_exam($data['exam']->id) == 'no'){
                            $take_exam = new Take_exam();
                            $take_exam->exam_id = $data['exam']->id;
                            $take_exam->save();
                        }
                    }
                }
            }
      
            $data['take_exam'] = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();

            // $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
            $question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
            ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();
            $data['question'] = Question::whereExamId($data['exam']->id)->whereNotIn('id', $question_ids)->first();
            $data['number_of_answer'] = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
            ->where('take_exam_id',$data['take_exam']->id)->count();
        

            $now = Carbon::now();
            $expend_second = $now->diffInSeconds($data['take_exam']->created_at);
         
         
            if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                if(check_take_attempt_exam($data['exam']->id) == 'no'){
                    // $resultURL = route('start-quiz-course');
                    $resultURL = route('submit-quiz-answer');
                    ///////////////////////////////////////
                }
                else{
                    // $resultURL = route('quiz-result-course'); 
                    // $result_array = [
                    //     'quiz_id' => $data['exam']->id,
                    //     'quiz_uuid' => $data['exam']->uuid,
                    //     'redirect_api_route' => $resultURL,
                    // ];
                    // return response()->json([
                    //     'status' => 200,
                    //     'message' => __('Given Quiz Is Already Attempted And Passed By Student'),
                    //     'data' => $result_array,
                    // ]);
                    $message1 = __('Given Quiz Is Already Attempted And Passed By Student');
                    $response1 = $this->quizResultFunction($data['exam']->uuid,$lectureID, $message1 );
                    return response()->json([
                        'status' => 200,
                        'message' => __($response1['message']),
                        'data' => $response1['data'],
                    ]);
                }
            }
            else{
             
                if(check_take_attempt_exam($data['exam']->id) == 'no'){
                    // $resultURL = route('start-quiz-course');
                    $resultURL = route('submit-quiz-answer');
                    ///////////////////////////////////////
                }
                else{
                    $message2 = __('Given Quiz Is Already Attempted And Passed By Student');
                    $response2 = $this->quizResultFunction($data['exam']->uuid,$lectureID, $message2 );
                    return response()->json([
                        'status' => 200,
                        'message' => __($response2['message']),
                        'data' => $response2['data'],
                    ]);
                }
            }

            // $resultURL = route('start-quiz-course');
            $resultURL = route('submit-quiz-answer');
            if(isset($answer)){
            }
            else{
                $now = \Carbon\Carbon::now();
                $expend_second =   $now->diffInSeconds($data['take_exam']->created_at);
                foreach ($data['question']->options as $key => $option) {
                    $quesOptionsArray[] = [
                        'option_id' => $option->id,
                        'option_uuid' => $option->uuid,
                        'option_name' => $option->name,
                    ];
                }
                $questionArray = [
                    'question_id'=>$data['question']->id,
                    'question_uuid'=>$data['question']->uuid,
                    'question_name'=>$data['question']->name,
                    'question_options'=>$quesOptionsArray,
                ];
                $result_array = [
                    'lecture_id' => (int)$lectureID,
                    'quiz_id'=>$data['exam']->id,
                    'course_uuid'=>$courseExam->uuid,
                    'question_uuid'=>$data['question']->uuid,
                    'take_exam_id'=>$data['take_exam']->id,
                    'quiz_name'=>$data['exam']->name,
                    'current_question_no'=>$data['number_of_answer'] + 1,
                    'total_questions'=>$data['exam']->questions->count(),
                    'quiz_attempt_progress'=>(($data['number_of_answer']) * 100 ) / $data['exam']->questions->count().'%',
                    'time_remaining'=>\Carbon\Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s'),
                    'total_time'=> \Carbon\Carbon::parse($data['exam']->duration * 60)->format('H:i:s'),
                    'question' => $questionArray,
                    'onclick_api_route' => $resultURL,
                ];
            }
          
            return response()->json([
                'status' => 200,
                'message' => __('Quiz Is Started You Can Now Attempt it Now'),
                'data' => $result_array,
            ]);

            
    
    
    } catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
    }






    public function startQuizFunction($quiz_uuid,$lecture_id = null, $responseMessage = null)
    {
        try {
          
            $result_array = array();
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }


        $uuid = $quiz_uuid;
        $checkExam = Exam::where('uuid',$uuid)->first();
        if($checkExam == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Quiz Uuid Does Not Exists.'
            ]);
        }
    
        if($lecture_id){
            $lecture_id  = $lecture_id;
        }
        else{
            $lecture_id  = null;
        }
        $lectureID = $lecture_id;

        $courseExam = Course::where('id',$checkExam->course_id)->first();
        $data['exam'] = Exam::whereUuid($quiz_uuid)->first();
   
    
            if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() == 0) {
                $take_exam = new Take_exam();
                $take_exam->exam_id = $data['exam']->id;
                $take_exam->save();
            }
            else{
                $takeExam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                $now = Carbon::now();
                $expend_second = $now->diffInSeconds($takeExam->created_at);
                $isPassed = $takeExam->passed;
                if(!$isPassed){   // Means Previous Test Is Failed Or Not Passed Yet
                    $totalQuestions = Question::whereExamId($data['exam']->id)->count();
                    $attemptedAnswers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                    ->where('take_exam_id',$takeExam->id)->count();
                    if($attemptedAnswers == $totalQuestions){
                        $take_exam = new Take_exam();
                        $take_exam->exam_id = $data['exam']->id;
                        $take_exam->save();
                    }
                    else if(Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                        if(check_take_attempt_exam($data['exam']->id) == 'no'){
                            $take_exam = new Take_exam();
                            $take_exam->exam_id = $data['exam']->id;
                            $take_exam->save();
                        }
                    }
                }
            }
    
            $data['take_exam'] = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();

            // $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
            $question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
            ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();
            $data['question'] = Question::whereExamId($data['exam']->id)->whereNotIn('id', $question_ids)->first();
            $data['number_of_answer'] = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
            ->where('take_exam_id',$data['take_exam']->id)->count();


            $now = Carbon::now();
            $expend_second = $now->diffInSeconds($data['take_exam']->created_at);
         
           
            if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
                if(check_take_attempt_exam($data['exam']->id) == 'no'){
                    // $resultURL = route('start-quiz-course');
                    $resultURL = route('submit-quiz-answer');
                    ///////////////////////////////////////
                }
                else{
                    // $resultURL = route('quiz-result-course'); 
                    // $result_array = [
                    //     'quiz_id' => $data['exam']->id,
                    //     'quiz_uuid' => $data['exam']->uuid,
                    //     'redirect_api_route' => $resultURL,
                    // ];
                    // return response()->json([
                    //     'status' => 200,
                    //     'message' => __('Given Quiz Is Already Attempted And Passed By Student'),
                    //     'data' => $result_array,
                    // ]);

                    $response1 = $this->quizResultFunction($data['exam']->uuid,$lectureID, $responseMessage);
                    return response()->json([
                        'status' => 200,
                        'message' => __( $response1['message']),
                        'data' =>  $response1['data'],
                    ]);
                }
            }
            // $resultURL = route('start-quiz-course');
            $resultURL = route('submit-quiz-answer');
            if(isset($answer)){
            }
            else{
                $now = \Carbon\Carbon::now();
                $expend_second =   $now->diffInSeconds($data['take_exam']->created_at);
                foreach ($data['question']->options as $key => $option) {
                    $quesOptionsArray[] = [
                        'option_id' => $option->id,
                        'option_uuid' => $option->uuid,
                        'option_name' => $option->name,
                    ];
                }
                $questionArray = [
                    'question_id'=>$data['question']->id,
                    'question_uuid'=>$data['question']->uuid,
                    'question_name'=>$data['question']->name,
                    'question_options'=>$quesOptionsArray,
                ];
                $result_array = [
                    'quiz_id'=>$data['exam']->id,
                    'course_uuid'=>$courseExam->uuid,
                    'question_uuid'=>$data['question']->uuid,
                    'take_exam_id'=>$data['take_exam']->id,
                    'quiz_name'=>$data['exam']->name,
                    'current_question_no'=>$data['number_of_answer'] + 1,
                    'total_questions'=>$data['exam']->questions->count(),
                    'quiz_attempt_progress'=>(($data['number_of_answer']) * 100 ) / $data['exam']->questions->count().'%',
                    'time_remaining'=>\Carbon\Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s'),
                    'total_time'=> \Carbon\Carbon::parse($data['exam']->duration * 60)->format('H:i:s'),
                    'question' => $questionArray,
                ];
            }
          
            $mainArray = [
                'status' => 200,
                'message' => __($responseMessage),
                'data' => $result_array,
            ];
            return $mainArray; 
    
    } catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
    }


    public function submitQuizAnswerApi(Request $request)
    {
        try {
            $result_array = array();
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 3){ // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }


        $messages = [
            'question_uuid.required' => __('The Question UUID Is Required'),
            'take_exam_id.required' => __('The TakeExam ID Is Required'),
            'selected_option_uuid.required' => __('The Selected Option UUID Is Required'),
        ];
    
        $validator = Validator::make($request->all(), [
            'question_uuid' => 'required',
            'take_exam_id' => 'required',
            'selected_option_uuid' => 'required',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }
        $question_uuid = $request->question_uuid;
        $question = Question::where('uuid',$question_uuid)->first();
        if($question == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Question Uuid Does Not Exists.'
            ]);
        }
     
        $take_exam_id = $request->take_exam_id;
        $checkTakeExam = Take_exam::where('id',$take_exam_id)->first();
        if($checkTakeExam == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given TakeExam ID Does Not Exists.'
            ]);
        }


        $selected_option_uuid = $request->selected_option_uuid;
        $option = Question_option::where('uuid',$selected_option_uuid)->first();
        if($option == null){
            return response()->json([
                'status' => 302,
                'message' => 'Given Selected Option Uuid Does Not Exists.'
            ]);
        }

        if($request->lecture_id){
            $lecture_id  = $request->lecture_id;
        }
        else{
            $lecture_id  = null;
        }
        $lectureID = $lecture_id;

        $exam = Exam::where('id',$question->exam_id)->first();
        $now = Carbon::now();
        $expend_second = $now->diffInSeconds($checkTakeExam->created_at);
        if(Carbon::parse($exam->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($exam->duration * 60)->format('H:i:s')) {
            return response()->json([
                'status' => 302,
                'message' => 'Given Take Exam Time Is Already Expired.'
            ]);
        }
    
        $checkSubmitAlready = Answer::where('exam_id',$question->exam_id)->where('question_id',$question->id)
                            ->where('question_option_id',$option->id)->where('take_exam_id',$take_exam_id)->first();
        if($checkSubmitAlready){
            return response()->json([
                'status' => 302,
                'message' => 'Question Already Submitted For Current Quiz Attempt.'
            ]);
        }
    
        $answer = new Answer();
        $answer->exam_id = $question->exam_id;
        $answer->question_id = $question->id;
        $answer->question_option_id = $option->id;
        $answer->take_exam_id = $take_exam_id;
        $answer->is_correct = $option->is_correct_answer;
        $answer->save();

        if ($option->is_correct_answer == 'yes') {
            $take_exam = Take_exam::find($take_exam_id);
            $take_exam->number_of_correct_answer = $take_exam->number_of_correct_answer + 1;
            $take_exam->lecture_id = $lectureID;
            $take_exam->save();
        }

        $question_ids = Answer::whereUserId($user->id)->whereExamId($question->exam_id)
        ->where('take_exam_id',$take_exam_id)->pluck('question_id')->toArray();

        if (Question::whereExamId($question->exam_id)->whereNotIn('id', $question_ids)->count() > 0) {
            // $getQuestion = Question::whereExamId($question->exam_id)->whereNotIn('id', $question_ids)->orderBy('id','ASC')->first();
            // $result_array = [
            //     'quiz_id' => $question->exam?->id,
            //     'quiz_uuid' => $question->exam?->uuid,
            //     'next_question_id' => $getQuestion->id,
            //     'next_question_uuid' => $getQuestion->uuid,
            //     'onclick_api_route' => route('start-quiz-course'),
            // ];
            // return response()->json([
            //     'status' => 200,
            //     'message' => __('Question Submitted Successfully'),
            //     'data' => $result_array,
            // ]);
            $message1 = __('Question Submitted Successfully');
            $response1 = $this->startQuizFunction($question->exam?->uuid,$lectureID,$message1);
            return response()->json([
                'status' => 200,
                'message' => __($response1['message']),
                'data' => $response1['data'],
            ]);
        
            // return redirect(route('student.my-course.show', [$course->slug, 'start-quiz', $question->exam->uuid, $answer->id]));
        } 
        else {
            $take_exam = Take_exam::find($take_exam_id);
            $total_questions = Question::whereExamId($take_exam->exam?->id)->count();
            $quizPassedPercentage = $take_exam->exam?->passing_percentage;
            $correct_answers = Answer::whereUserId($user->id)->whereExamId($take_exam->exam?->id)
            ->where('take_exam_id',$take_exam_id)->where('is_correct','yes')->count();
            $cal_percentage = ($correct_answers/$total_questions)*100;
            $cal_percentage = (int)$cal_percentage;
            if($cal_percentage >= $quizPassedPercentage){
                $take_exam->obtained_percentage = $cal_percentage;
                $take_exam->passing_percentage = $quizPassedPercentage;
                $take_exam->passed = 1;
                $take_exam->update();
            }
            else{
                $take_exam->obtained_percentage = $cal_percentage;
                $take_exam->passing_percentage = $quizPassedPercentage;
                $take_exam->passed = 0;
                $take_exam->update();
            }
            $message2 = __('Question Submitted Successfully. Given Quiz Attempt Is Completed By Student');
            $response2 = $this->quizResultFunction($question->exam?->uuid,$lectureID,$message2);
      
            return response()->json([
                'status' => 200,
                'message' => __($response2['message']),
                'data' => $response2['data'],
            ]);
            // return redirect(route('student.my-course.show', [$course->slug, 'quiz-result', $question->exam->uuid]));
        }
    
    } catch (\Throwable $th) {
        dd($th);
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
    }
    }


    public function quizResultApi(Request $request)
    {
        try {
            $result_array = array();
            $ques_array = array();
            $ques_opt_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $messages = [
                'quiz_id.required' => __('The Quiz ID Is Required'),
                'lecture_id.numeric' => __('The Lecture ID Is Numeric'),
            ];
        
            $validator = Validator::make($request->all(), [
                'quiz_id' => 'required',
                'lecture_id' => 'nullable|numeric',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
            $id = $request->quiz_id;
            $checkExam = Exam::where('id',$id)->first();
            if($checkExam == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Quiz ID Does Not Exists.'
                ]);
            }
        
            if($request->lecture_id){
                $checkLecture = Course_lecture::where('id',$request->lecture_id)->first();
                if($checkLecture == null){
                    return response()->json([
                        'status' => 302,
                        'message' => 'Given Lecture ID Does Not Exists.'
                    ]);
                }
                $lecture_id = $request->lecture_id;
                $lectureID = $lecture_id;
            }
            else{
                $lectureID = null;
            }
          


            $courseExam = Course::where('id',$checkExam->course_id)->first();
            if(!$checkExam->questions->isEmpty()){
                foreach ($checkExam->questions as $key0 => $question) {
                    $ques_opt_array = array();
                    foreach ($question->options as $key1 => $option) {
                        $check_user_answer = check_user_answer($checkExam->id, $question->id, $option->id);
                        if($check_user_answer == 'given-answer-right'){
                            $ques_opt_array[]=[
                                'sr_no' =>  $key1+1,
                                'id' =>  $option->id,
                                'name' =>  $option->name,
                                'user_answer'=> true,
                            ];    
                        }
                        else if($check_user_answer == 'given-answer-wrong'){
                            $ques_opt_array[]=[
                                'sr_no' =>  $key1+1,
                                'id' =>  $option->id,
                                'name' =>  $option->name,
                                'user_answer'=> false,
                            ];
                        }
                        else{
                            $ques_opt_array[]=[
                                'sr_no' =>  $key1+1,
                                'id' =>  $option->id,
                                'name' =>  $option->name,
                            ];
                        }

                    }
                    $ques_array[]=[
                        'sr_no' =>  $key0+1,
                        'id' =>  $question->id,
                        'name' =>  $question->name,
                        'options'=> $ques_opt_array,
                    ];
                }
            }
        
            $action_api_routes = [
                'back_btn_route' => route('my-course-show-student',['slug' => $courseExam->slug]),
                'back_btn_route_name' => __('(Student) Course Detail'),
                'leaderboard_btn_route' => route('quiz-leaderboard-course'),
                'leaderboard_btn_route_name' => route('quiz-leaderboard-course'),
            ];
            if($lectureID != null){
                $lecture = Course_lecture::where('id',$lectureID)->first();
                $course = Course::where('id',$lecture->course_id)->first();
                $enrollment = Enrollment::where(['course_id' => $course->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                $isComplete = $this->checkQuizCompletion($lecture,$user,$enrollment);
                $result_array = [
                    'lectureID'=>(int)$lectureID,
                    'quizID'=>$checkExam->id,
                    'courseSlug'=>$courseExam->slug,
                    'quizName'=>$checkExam->name,
                    'TotalScore'=>get_total_score($checkExam->id),
                    'YourScore'=>get_student_score($checkExam->id),
                    'examQuestions'=>$ques_array,
                    'action_api_routes' => $action_api_routes
                ];
            
            }
            else{
                $result_array = [
                    'quizID'=>$checkExam->id,
                    'courseSlug'=>$courseExam->slug,
                    'quizName'=>$checkExam->name,
                    'TotalScore'=>get_total_score($checkExam->id),
                    'YourScore'=>get_student_score($checkExam->id),
                    'examQuestions'=>$ques_array,
                    'action_api_routes' => $action_api_routes
                ];
            }
            // dd($ques_array);
            return response()->json($result_array);

        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }





    public function quizResultApiBackUp(Request $request)
    {
        try {
            $result_array = array();
            $ques_array = array();
            $ques_opt_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $messages = [
                'quiz_uuid.required' => __('The Quiz UUID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'quiz_uuid' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
            $uuid = $request->quiz_uuid;
            $checkExam = Exam::where('uuid',$uuid)->first();
            if($checkExam == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Quiz Uuid Does Not Exists.'
                ]);
            }
        
            if($request->lecture_id){
                $lecture_id  = $request->lecture_id;
            }
            else{
                $lecture_id  = null;
            }
            $lectureID = $lecture_id;
            $courseExam = Course::where('id',$checkExam->course_id)->first();
            if(!$checkExam->questions->isEmpty()){
                foreach ($checkExam->questions as $key => $question) {
                    $ques_opt_array = array();
                    foreach ($question->options as $key => $option) {
                        $ques_opt_array[]=[
                            'id' =>  $option->id,
                            'name' =>  $option->name,
                            'answer_class'=> get_answer_class($checkExam->id, $question->id, $option->id),
                        ];
                    }
                    $ques_array[]=[
                        'id' =>  $question->id,
                        'name' =>  $question->name,
                        'options'=> $ques_opt_array,
                    ];
                }
            }

            $action_api_routes = [
                'back' => route('my-course-show-student',['slug' => $courseExam->slug]),
                'leaderboard' => route('quiz-leaderboard-course'),
            ];
            if($lectureID != null){
                $lecture = Course_lecture::where('id',$lectureID)->first();
                $course = Course::where('id',$lecture->course_id)->first();
                $enrollment = Enrollment::where(['course_id' => $course->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                $isComplete = $this->checkQuizCompletion($lecture,$user,$enrollment);
                $result_array = [
                    'lectureID'=>(int)$lectureID,
                    'quizID'=>$checkExam->id,
                    'quizName'=>$checkExam->name,
                    'TotalScore'=>get_total_score($checkExam->id),
                    'YourScore'=>get_student_score($checkExam->id),
                    'examQuestions'=>$ques_array,
                    'action_api_routes' => $action_api_routes
                ];
            
            }
            else{
                $result_array = [
                    'quizID'=>$checkExam->id,
                    'quizName'=>$checkExam->name,
                    'TotalScore'=>get_total_score($checkExam->id),
                    'YourScore'=>get_student_score($checkExam->id),
                    'examQuestions'=>$ques_array,
                    'action_api_routes' => $action_api_routes
                ];
            }
        
            return response()->json($result_array);

        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }

    public function quizResultFunction($quiz_uuid,$lecture_id = null,$responseMessage = null)
    {
        try {
            $result_array = array();
            $ques_array = array();
            $ques_opt_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
         
            $uuid = $quiz_uuid;
            $checkExam = Exam::where('uuid',$uuid)->first();
            if($checkExam == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Quiz Uuid Does Not Exists.'
                ]);
            }
        
            if($lecture_id){
                $lecture_id  = $lecture_id;
            }
            else{
                $lecture_id  = null;
            }
            $lectureID = $lecture_id;
            $courseExam = Course::where('id',$checkExam->course_id)->first();
            if(!$checkExam->questions->isEmpty()){
                foreach ($checkExam->questions as $key => $question) {
                    $ques_opt_array = array();
                    foreach ($question->options as $key => $option) {
                        $ques_opt_array[]=[
                            'id' =>  $option->id,
                            'name' =>  $option->name,
                            'answer_class'=> get_answer_class($checkExam->id, $question->id, $option->id),
                        ];
                    }
                    $ques_array[]=[
                        'id' =>  $question->id,
                        'name' =>  $question->name,
                        'options'=> $ques_opt_array,
                    ];
                }
            }

            $action_api_routes = [
                'back' => route('my-course-show-student',['slug' => $courseExam->slug]),
                'leaderboard' => route('quiz-leaderboard-course'),
            ];
            if($lectureID != null){
                $lecture = Course_lecture::where('id',$lectureID)->first();
                $course = Course::where('id',$lecture->course_id)->first();
                $enrollment = Enrollment::where(['course_id' => $course->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                $isComplete = $this->checkQuizCompletion($lecture,$user,$enrollment);
                $result_array = [
                    'lectureID'=>(int)$lectureID,
                    'quizID'=>$checkExam->id,
                    'quizName'=>$checkExam->name,
                    'TotalScore'=>get_total_score($checkExam->id),
                    'YourScore'=>get_student_score($checkExam->id),
                    'examQuestions'=>$ques_array,
                    'action_api_routes' => $action_api_routes
                ];
            
            }
            else{
                $result_array = [
                    'quizID'=>$checkExam->id,
                    'quizName'=>$checkExam->name,
                    'TotalScore'=>get_total_score($checkExam->id),
                    'YourScore'=>get_student_score($checkExam->id),
                    'examQuestions'=>$ques_array,
                    'action_api_routes' => $action_api_routes
                ];
            }
            
            $mainArray = [
                'status' => 200,
                'message' => __($responseMessage),
                'data' => $result_array,
            ];
            return $mainArray;
            // return response()->json($result_array);

        } catch (\Throwable $th) {
         
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


    public function quizLeaderBoardApi(Request $request)
    {
        try {
            $result_array = array();
            $ques_array = array();
            $ques_opt_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 302);
            }
            
            $messages = [
                'quiz_id.required' => __('The Quiz ID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'quiz_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],302);
            }
            $id = $request->quiz_id;

            $checkExam = Exam::where('id',$id)->first();
            if($checkExam == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Quiz ID Does Not Exists.'
                ]);
            }
         
            if($request->lecture_id){
                $lecture_id  = $request->lecture_id;
            }
            else{
                $lecture_id  = null;
            }
            $lectureID = $lecture_id;
            $courseExam = Course::where('id',$checkExam->course_id)->first();
            $action_api_routes = [
                'back_btn_route_name' => __('(Student) Course Detail'),
                'back_btn_route' => route('my-course-show-student',['slug' => $courseExam->slug]),
            ];

            $top5_take_exams = Take_exam::whereExamId($checkExam ->id)->orderBy('number_of_correct_answer', 'DESC')->take(5)->get();
            $take_exams = Take_exam::whereExamId($checkExam ->id)->orderBy('number_of_correct_answer', 'DESC')->skip(5)->take(500)->get();

            $student_own_leatherboard = array();
            $merit_list_leatherboard = array();
            $leatherboard_for_all = array();

            if (get_student_exam_status($checkExam->id)){
                $student_test_status = __('Passed');
            }
            else{
                $student_test_status = __('Fail');
            }
        
            $student_own_leatherboard = [
                'your_position' => get_position($checkExam->id),
                'student_id' => $user->id,
                'student_image' => getImageFile($user->image_path),
                'student_name' => $user->name,
                'quiz_total_marks' => get_total_score($checkExam->id).'('.get_total_percentage($checkExam->id).'%)',
                'your_marks' => get_student_score($checkExam->id).'('.get_student_percentage($checkExam->id).'%)',
                'status' => $student_test_status,
            ];
          
            foreach ($top5_take_exams as $key => $top5) {
                if($top5->user){
                    if(get_student_take_exam_status($top5->id)){
                        $status =  __('Passed');
                    }
                    else{
                        $status =  __('Fail'); 
                    }
                $merit_list_leatherboard[] = [
                    'position' => $key+1,
                    'student_id' => $top5->user?->id,
                    'student_image' => getImageFile($top5->user?->image_path),
                    'student_name' => $top5->user->name,
                    'quiz_total_marks' => get_total_score($checkExam->id).'('.get_total_percentage($checkExam->id).'%)',
                    'obtained_marks' => get_student_by_student_score($checkExam->id, $top5->user_id,$top5->id).'('.get_student_percentage_with_take_exam($top5->id).'%)',
                    'status' => $status,
                ];
                }
            }
           
        if($take_exams->count() > 0){
            foreach ($take_exams as $k => $take_exam) {
                if($take_exam->user){
                $leatherboard_for_all[] = [
                    'position' => $k+1,
                    'student_id' => $take_exam->user?->id,
                    'student_image' => getImageFile($take_exam->user?->image_path),
                    'student_name' => $take_exam->user->name,
                    'quiz_total_marks' => get_total_score($checkExam->id),
                    'obtained_marks' => get_student_by_student_score($checkExam->id, $take_exam->user_id,$take_exam->id),
                ];
                }
            }
        }
            if($lectureID != null){
                $lecture = Course_lecture::where('id',$lectureID)->first();
                $course = Course::where('id',$lecture->course_id)->first();
                $enrollment = Enrollment::where(['course_id' => $course->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                $isComplete = $this->checkQuizCompletion($lecture,$user,$enrollment);
                $result_array = [
                    'lectureID'=>(int)$lectureID,
                    'quizID'=>$checkExam->id,
                    'courseSlug'=>$courseExam->slug,
                    'student_leaderboard_section' => $student_own_leatherboard,
                    'merit_list_leaderboard_section' => $merit_list_leatherboard,
                    'leaderboard_for_all' => $leatherboard_for_all,
                    'action_api_routes' => $action_api_routes,
                ];
            
            }
            else{
                $result_array = [
                    'quizID'=>$checkExam->id,
                    'courseSlug'=>$courseExam->slug,
                    'student_leaderboard_section' => $student_own_leatherboard,
                    'merit_list_leaderboard_section' => $merit_list_leatherboard,
                    'leaderboard_for_all' => $leatherboard_for_all,
                    'action_api_routes' => $action_api_routes,
                ];
            }

            return response()->json($result_array);

        } catch (\Throwable $th) {
          
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }




    public function quizLeaderBoardApiBackUp(Request $request)
    {
        try {
            $result_array = array();
            $ques_array = array();
            $ques_opt_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $messages = [
                'quiz_uuid.required' => __('The Quiz UUID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'quiz_uuid' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
            $uuid = $request->quiz_uuid;

            $checkExam = Exam::where('uuid',$uuid)->first();
            if($checkExam == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Exam Uuid Does Not Exists.'
                ]);
            }
         
            if($request->lecture_id){
                $lecture_id  = $request->lecture_id;
            }
            else{
                $lecture_id  = null;
            }
            $lectureID = $lecture_id;
            $courseExam = Course::where('id',$checkExam->course_id)->first();
            $action_api_routes = [
                'back' => route('my-course-show-student',['slug' => $courseExam->slug]),
            ];

            $top5_take_exams = Take_exam::whereExamId($checkExam ->id)->orderBy('number_of_correct_answer', 'DESC')->take(5)->get();
            $take_exams = Take_exam::whereExamId($checkExam ->id)->orderBy('number_of_correct_answer', 'DESC')->skip(5)->take(500)->get();

            $student_own_leatherboard = array();
            $merit_list_leatherboard = array();
            $leatherboard_for_all = array();

            if (get_student_exam_status($checkExam->id)){
                $student_test_status = __('Passed');
            }
            else{
                $student_test_status = __('Fail');
            }
        
            $student_own_leatherboard = [
                'your_position' => get_position($checkExam->id),
                'student_id' => $user->id,
                'student_image' => getImageFile($user->image_path),
                'student_name' => $user->name,
                'quiz_total_marks' => get_total_score($checkExam->id).'('.get_total_percentage($checkExam->id).'%)',
                'your_marks' => get_student_score($checkExam->id).'('.get_student_percentage($checkExam->id).'%)',
                'status' => $student_test_status,
            ];
          
            foreach ($top5_take_exams as $key => $top5) {
                if($top5->user){
                    if(get_student_take_exam_status($top5->id)){
                        $status =  __('Passed');
                    }
                    else{
                        $status =  __('Fail'); 
                    }
                $merit_list_leatherboard[] = [
                    'position' => $key+1,
                    'student_id' => $top5->user?->id,
                    'student_image' => getImageFile($top5->user?->image_path),
                    'student_name' => $top5->user->name,
                    'quiz_total_marks' => get_total_score($checkExam->id).'('.get_total_percentage($checkExam->id).'%)',
                    'obtained_marks' => get_student_by_student_score($checkExam->id, $top5->user_id,$top5->id).'('.get_student_percentage_with_take_exam($top5->id).'%)',
                    'status' => $status,
                ];
                }
            }
           
        if($take_exams->count() > 0){
            foreach ($take_exams as $k => $take_exam) {
                if($take_exam->user){
                $leatherboard_for_all[] = [
                    'position' => $k+1,
                    'student_id' => $take_exam->user?->id,
                    'student_image' => getImageFile($take_exam->user?->image_path),
                    'student_name' => $take_exam->user->name,
                    'quiz_total_marks' => get_total_score($checkExam->id),
                    'obtained_marks' => get_student_by_student_score($checkExam->id, $take_exam->user_id,$take_exam->id),
                ];
                }
            }
        }
            if($lectureID != null){
                $lecture = Course_lecture::where('id',$lectureID)->first();
                $course = Course::where('id',$lecture->course_id)->first();
                $enrollment = Enrollment::where(['course_id' => $course->id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
                $isComplete = $this->checkQuizCompletion($lecture,$user,$enrollment);
                $result_array = [
                    'lectureID'=>(int)$lectureID,
                    'quizID'=>$checkExam->id,
                    'student_leaderboard_section' => $student_own_leatherboard,
                    'merit_list_leaderboard_section' => $merit_list_leatherboard,
                    'leaderboard_for_all' => $leatherboard_for_all,
                    'action_api_routes' => $action_api_routes,
                ];
            
            }
            else{
                $result_array = [
                    'quizID'=>$checkExam->id,
                    'student_leaderboard_section' => $student_own_leatherboard,
                    'merit_list_leaderboard_section' => $merit_list_leatherboard,
                    'leaderboard_for_all' => $leatherboard_for_all,
                    'action_api_routes' => $action_api_routes,
                ];
            }

            return response()->json($result_array);

        } catch (\Throwable $th) {
          
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }



    public function checkQuizCompletion($lecture,$user,$enrollment){
            $response = false;
            $data['exam'] = Exam::whereUuid($lecture->quiz->uuid)->first();
            if (Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->count() > 0) { // User Has Already Attempted This Quiz Previously Multiple Times
                // Now From That Multiple Attempts Get Data OF Latest Attempt
                $take_exam = Take_exam::whereUserId($user->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                $take_exam->lecture_id = $lecture->id;
               
                $answered_question_ids = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                $total_questions = Question::whereExamId($data['exam']->id)->count();
    
                $number_of_attempted_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                ->where('take_exam_id',$take_exam->id)->count();

                if($number_of_attempted_answers == $total_questions){  // USER Attempted All Question Before EndTime
                    if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                        //NEW CODE
                        if($lecture->quiz){
                            $quizPassedPercentage = $lecture->quiz->passing_percentage;
                            if($quizPassedPercentage == null || $quizPassedPercentage == 0){
                                $quizPassedPercentage = 100;
                            }
                            $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                            $cal_percentage = ($correct_answers/$total_questions)*100;
                            $cal_percentage = (int)$cal_percentage;
                            if($cal_percentage >= $quizPassedPercentage){
                                $lecture = Course_lecture::find($lecture->id);
                                if ($lecture) {
                                    if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                        if($enrollment){
                                            $enrollment_id = $enrollment->id;
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
                                    $response = true;
                                }
                            }
                            else{
                                $take_exam->obtained_percentage = $cal_percentage;
                                $take_exam->passing_percentage = $quizPassedPercentage;
                                $take_exam->passed = 0;
                                $take_exam->update();
                                $response = false;
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
                                if($quizPassedPercentage == null || $quizPassedPercentage == 0){
                                    $quizPassedPercentage = 100;
                                }
                                $correct_answers = Answer::whereUserId($user->id)->whereExamId($data['exam']->id)
                                ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                $cal_percentage = ($correct_answers/$total_questions)*100;
                                $cal_percentage = (int)$cal_percentage;
                                if($cal_percentage >= $quizPassedPercentage){
                                    $lecture = Course_lecture::find($lecture->id);
                                    if ($lecture) {
                                    
                                        if (Course_lecture_views::where('user_id', $user->id)->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                                            if($enrollment){
                                                $enrollment_id = $enrollment->id;
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
                                        $response = true;
                                    }
                                }
                                else{
                                    $take_exam->obtained_percentage = $cal_percentage;
                                    $take_exam->passing_percentage = $quizPassedPercentage;
                                    $take_exam->passed = 0;
                                    $take_exam->update();
                                    $response = false;
                                }   
                            }
                           
                    }
                    }
                } 
                $take_exam->update(); 
            }   
            return $response;
    }



    public function productPurchaseList(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $data['pageTitle'] = __('My Purchase Product');
          
            $allUserOrder = Order::where('user_id', $user->id);
        
            // $OrderIds = $allUserOrder->where('payment_status', 'paid')->pluck('id')->toArray();
            // $OrderIds = $allUserOrder->whereIn('payment_status', ['paid','free'])->pluck('id')->toArray();
            $OrderIds = $allUserOrder->whereIn('payment_status', ['paid','free','due'])->pluck('id')->toArray();
            $data['orderItems'] = Order_item::whereIn('order_id', $OrderIds)->whereNotNull('product_id');
          
            if(isset($request->sortByID)){
               if($request->sortByID != 1 && $request->sortByID != 2){
                    return response()->json(['message' => 'sortBy Must have value either "1" or "2"'], 500);
               }
               $sortByID = $request->sortByID; // 1=newest, 2=Oldest
               $data['orderItems'] = Order_item::whereIn('order_id', $OrderIds)->whereNotNull('product_id');
                if ($sortByID == 1) {
                    $data['orderItems'] = $data['orderItems']->latest()->paginate();
                }

                if ($sortByID == 2) {
                    $data['orderItems'] = $data['orderItems']->paginate();
                }
            }
            else{
                $data['orderItems'] = $data['orderItems']->latest()->paginate(); 
            }
          
            foreach ($data['orderItems'] as $key => $order_item) {
                 if(get_currency_placement() == 'after'){
                    $productPrice=$order_item->unit_price.' '.get_currency_symbol();
                 }
                else{
                    $productPrice= get_currency_symbol().' '.$order_item->unit_price;
                }

                if($order_item->product?->type == 1){
                    $productType=__('Physical');
                 }
                else{
                    $productType=__('Digital');
                }

                if($order_item->order->payment_status == 'due'){
                    $paymentStatus=__('Due');
                 }
                elseif($order_item->order->payment_status == 'free'){
                    $paymentStatus=__('Free');
                }
                elseif($order_item->order->payment_status == 'paid'){
                    $paymentStatus=__('Paid');
                }


                if($order_item->delivery_status == 0){
                    $deliveryStatus=__('Pending');
                 }
                elseif($order_item->delivery_status == 1){
                    $deliveryStatus=__('Processing');
                }
                elseif($order_item->delivery_status == 2){
                    $deliveryStatus=__('Shipped');
                }
                elseif($order_item->delivery_status == 3){
                    $deliveryStatus=__('Delivered');
                }
                elseif($order_item->delivery_status == 4){
                    $deliveryStatus=__('Cancelled');
                }

                $productDetailLink= route('product-view', $order_item->product?->slug);
                if($order_item->unit_price > 0 && $order_item->product->type == PHYSICAL_PRODUCT){
                    $invoiceDetailsLink= route('product-invoice-detail', [$order_item->id]);
                    $result_array[] = [
                        'product_view_api_route' => $productDetailLink,
                        'product_id' => $order_item->product_id,
                        'product_name' => $order_item->product?->title,
                        'product_type' => $productType,
                        'product_quantity' => $order_item->unit,
                        'product_added_by' => $order_item->product?->user?->name,
                        'product_price' => $productPrice,
                        'order_id' => $order_item->order?->order_number,
                        'payment_status' => $paymentStatus,
                        'delivery_status' => $deliveryStatus,
                        'invoice_detail_api_route' => $invoiceDetailsLink,
                    ];
                 }
                 else{
                    $result_array[] = [
                        'product_view_api_route' => $productDetailLink,
                        'product_id' => $order_item->product_id,
                        'product_name' => $order_item->product?->title,
                        'product_type' => $productType,
                        'product_quantity' => $order_item->unit,
                        'product_added_by' => $order_item->product?->user?->name,
                        'product_price' => $productPrice,
                        'order_id' => $order_item->order?->order_number,
                        'payment_status' => $paymentStatus,
                        'delivery_status' => $deliveryStatus,
                    ];
                 }
               
            }
            return response()->json($result_array);
         
          
        } catch (\Throwable $th) {
           
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }


    }  




    public function productReviewCreate(Request $request)
    {
        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $messages = [
                'product_id.required' => __('The Product ID Is Required'),
                'rating_sum.required' => __('The Rating Sum Is Required'),
                'feedback.required' => __('The Feedback Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'product_id' => 'required',
                'rating_sum' => 'required',
                'feedback' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
          
            $checkProduct = Product::where('id',$request->product_id)->first();
            if($checkProduct == null){
                return response()->json([
                    'status' => 302,
                    'message' => 'Given Product ID Does Not Exists.'
                ]);
            }

            if($request->rating_sum < 1 || $request->rating_sum > 5){
                return response()->json([
                    'status' => 302,
                    'message' => 'Invalid Rating Sum Value.'
                ]);
            }

        
            $review_exists_user = ProductReview::whereUserId($user->id)->whereProductId($request->product_id)->first();
            if ($review_exists_user) {
                return response()->json([
                    'status' => 302,
                    'msg' => 'Already you have reviewed. Thank you.'
                ]);
            }
        
    
            $review = new ProductReview();
            $review->user_id = $user->id;
            $review->product_id = $request->product_id;
            $review->rating = $request->rating_sum;
            $review->comment = $request->feedback;
            $review->save();
    
            // Review Calculation and Update
    
            $data['five_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(5)->count();
            $data['four_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(4)->count();
            $data['three_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(3)->count();
            $data['two_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(2)->count();
            $data['first_star_count'] = ProductReview::whereProductId($request->product_id)->whereRating(1)->count();
    
            $data['total_reviews'] = (5 * $data['five_star_count']) + (4 * $data['four_star_count']) + (3 * $data['three_star_count']) +
                (2 * $data['two_star_count']) + (1 * $data['first_star_count']);
            $data['total_user_review'] = $data['five_star_count'] + $data['four_star_count'] + $data['three_star_count'] + $data['two_star_count'] + $data['first_star_count'];
    
            if ($data['total_user_review'] > 0) {
                $average_rating = $data['total_reviews'] / $data['total_user_review'];
            } else {
                $average_rating = 0;
            }
    
            $product = Product::findOrFail($request->product_id);
            $product->average_review = number_format($average_rating, 1);
            $product->update();
    
            // End:: Review Calculation and Update
    
            return response()->json([
                'status' => 200,
                'msg' => 'Review Created Successfully.'
            ]);
        } catch (\Throwable $th) {
        
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }

    }



    public function productInvoiceDetail($item_id)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $item = Order_item::find($item_id);
            if($item){
                $upperSection = [
                    'invoice_number' => $item->order->order_number,
                    // 'purchase_date' => $item->created_at->format(get_option('app_date_format')),
                    'purchase_date' => $item->created_at->format('d-m-Y'),
                    'app_name' => get_option('app_name'),
                    'app_email' => get_option('app_email'),
                    'app_contact_number' => get_option('app_contact_number'),
                ];
                $name = $item->product?->title;
                if(get_currency_placement() == 'after'){
                    $amount = $item->unit_price.' '.get_currency_code();
                }
                else{
                    $amount = get_currency_code().' '.$item->unit_price;
                }
                $innerTableSection = [
                    'name' => $name,
                    // 'purchase_date' => $item->created_at->format(get_option('app_date_format')),
                    'purchase_date' => $item->created_at->format('d-m-Y'),
                    'payment_method' => ucwords($item->order?->payment_method),
                    'amount' => $amount,
                ];


                $result_array = [
                    'upperSection' => $upperSection,
                    'innerTableSection' => $innerTableSection,
                ];
                return response()->json($result_array);
            }
            else{
                return response()->json(['message' => 'Invalid Order Item ID in Url'], 302); 
            }
         
            return view('frontend.student.course.invoice', ['item' => $item]);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
       
    }



    public function productView($slug)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $data['pageTitle'] = __("Product Details");
            $data['product'] = Product::whereSlug($slug)->with('user')->first();
            if($data['product'] == null){
                return response()->json(['message' => 'Given Product With Slug Does Not Exists'], 302);
            }
           
            if (($user && $user->role != USER_ROLE_ADMIN) && $data['product']->status != STATUS_APPROVED && $data['product']->status != STATUS_UPCOMING_APPROVED){
                return response()->json(['message' => 'This product is not active'], 302);
            }

            if($data['product']->user->role == USER_ROLE_INSTRUCTOR){
                $userType = 'instructor';
            }
            else if($data['product']->user->role == USER_ROLE_ORGANIZATION){
                $userType = 'organization';
            }
            else if($data['product']->user->role == USER_ROLE_STUDENT){
               
                $userType = 'student';
            }
            else if($data['product']->user->role == USER_ROLE_ADMIN){
                $userType = 'admin';
            }

            $totalReviews = ProductReview::where('product_id',$data['product']->id)->count();

            if($data['product']->old_price > $data['product']->current_price){
                if(get_currency_placement() == 'after'){
                    $productPrice =   $data['product']->old_price .' '.get_currency_symbol();
                }
                else{
                    $productPrice =   get_currency_symbol().' '.$data['product']->old_price;
                }
            }
            else{
                if(get_currency_placement() == 'after'){
                    $productPrice =   $data['product']->current_price .' '.get_currency_symbol();
                }
                else{
                    $productPrice =   get_currency_symbol().' '.$data['product']->current_price;
                }
            }

            if($data['product']->quantity > 0){
                $isStock = $data['product']->quantity;
            }
            else{
                $isStock = __('Out of stock');
            }

            $description = [
                $data['product']->description,
            ];

            $additional_information = [
                $data['product']->additional_information,
            ];
            $shipping_return = [
                $data['product']->shipping_return,
            ];


            $result_array = [
                'product_id' => $data['product']->id,
                'product_image_1' => getImageFile($data['product']->image_1),
                'product_image_2' => getImageFile($data['product']->image_2),
                'product_image_3' => getImageFile($data['product']->image_3),
                'product_image_4' => getImageFile($data['product']->image_4),
                'product_title' => $data['product']->title,
                'product_created_by' => $data['product']->user?->name,
                'total_reviews' => $totalReviews,
                'average_reviews' => number_format($data['product']->average_review, 1),
                'average_percent' => $data['product']->average_review * 20,
                'product_price' => $productPrice,
                'product_stock' => $isStock,
                'description' => $description,
                'additional_information' => $additional_information,
                'shipping_return' => $shipping_return,
              
            ];
            return response()->json($result_array);
        } catch (\Throwable $th) {
           
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


    public function studentClassSchedule()
    {
        try {
            $user = Auth::user();
            
            // Authentication and authorization checks
            if (!$user) {
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            if ($user->role != 3) { // Student role check
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
    
            // Get student data
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return response()->json(['message' => 'Student record not found'], 404);
            }
    
            // Get upcoming classes
            $today = date('Y-m-d');
            $classes = ClassDetail::where('student_id', $student->id)
                ->where('class_date', '>=', $today)
                ->wherehas('class' , function($query){
                    $query->where('type', 'Individual');
                })
                ->orderBy('class_date', 'ASC')
                ->orderBy('class_time','DESC')
                ->paginate(25);
    

          $groupclasses = ClassDetail::where('student_id',$student->id)
          ->where('class_date' ,'>=',$today)->wherehas('class' , function($query){
            $query->where('type', 'Group');
        })->orderBy('group_id','DESC')
        ->groupBy('class_id')
        ->paginate(25);



            $individualArray = [];
            $groupArray = [];
            $counter = 1;
    
            if (!$classes->isEmpty()) {
                foreach ($classes as $individual) {

                    $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->student_id,'student');
                    $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$individual->student_id,'student');
                    $updated_time = date('h:i A',strtotime($DateTime));
                    $lsntime = strtotime($updated_time);
                    $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                    $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                    $curdate = strtotime($curdate);
                    $lsndate = date('Y-m-d',strtotime($DateTime));
                    $lsndate = strtotime($lsndate); 
                    $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                    $curtime = strtotime($updated_cur_time);
                    $lesson_duration = $individual->class->duration;
                    $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                    $lsnendtime = $lsntimestamp;

                    // $status = $this->determinateClassStatus($individual);
                    $classType = strtolower($individual->class?->type);

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
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $individual->StudentURI == NULL && $individual->class?->learning_tool == 'BBB')
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
                                'btnHref' =>  "javascript:void(0);",
                                'classDetails' => [
                                    'type' => $individual->class?->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Student'),
                                    'user_name' => $individual->student->first_name.' '.$individual->student->last_name,
                                    'user_id' => $individual->student_id,
                                ],
                              
                            ];
                        }
                         else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'BBB')    
                        { 
                            $statusArray = [
                                 'btnText' => __($individual->status),
                                'btnHref' =>  $individual->StudentURI,
                                'classType' => $individual->class?->learning_tool,                
                            ];
                        }
                         else if($curtime == $lsntime && $individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')    
                        { 
                            $statusArray = [
                              'btnText' => __($individual->status),
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,                
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
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed" && $individual->status != "Missed" && $individual->class->learning_tool == 'Zoom')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'btnHref' =>  "javascript:void(0);",
                              
                                'classDetails' => [
                                    'type' => $individual->class?->learning_tool,
                                    'lesson_id' => $individual->id,
                                    'meeting_id' => $individual->meeting_id,
                                    'user_type' => __('Student'),
                                    'user_name' => $individual->student->first_name.' '.$individual->student->last_name,
                                    'user_id' => $individual->student_id,
                                ],
                              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed"  && $individual->class->learning_tool == 'BBB')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'btnHref' =>  $individual->StudentURI,
                                'classType' => $individual->class?->learning_tool,                
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$individual->status != "Completed"&&$individual->status != "Missed" && $individual->class->learning_tool == 'Google_Meet')
                		{
                            $statusArray = [
                                'btnText' => __($individual->status),
                                'btnHref' =>  $individual->google_meet_link,
                                'classType' => $individual->class?->learning_tool,                
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

                        $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->student_id,'student');
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $classData = [
                            'sr_no' => $counter++,
                            'id' => $individual->id,
                            'instructor' => Str::limit($individual->instructor?->first_name.' '.$individual->instructor?->last_name, 50),
                            'course' => Str::limit($individual->course?->title, 50),
                            'date_time' => $classTime,
                            'type' => $individual->class?->type,
                            'learning_tool' => $individual->class?->learning_tool,
                            // 'zoomApiKey'=> $api_key,
                            // 'zoomApiSecret'=> $api_secret,
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                            // 'zoomApiJwtToken'=> $api_jwt_token,
                            // 'zoomMeetingSignature' => $individual->zoom_meeting_signature,
                            'status' => $statusArray,
                        ];
                    }
                    else{
                        $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->student_id,'student');
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $classData = [
                            'sr_no' => $counter++,
                            'id' => $individual->id,
                            'instructor' => Str::limit($individual->instructor?->first_name.' '.$individual->instructor?->last_name, 50),
                            'course' => Str::limit($individual->course?->title, 50),
                            'date_time' => $classTime,
                            'type' => $individual->class?->type,
                            'learning_tool' => $individual->class?->learning_tool,
                            'status' => $statusArray,
                        ];
                    }

                    $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->student_id,'student');
                    $classTime = date('d-m-Y h:i A',strtotime($DateTime));

                    $classData = [
                        'sr_no' => $counter++,
                        'id' => $individual->id,
                        'instructor' => Str::limit($individual->instructor?->first_name.' '.$individual->instructor?->last_name, 50),
                        'course' => Str::limit($individual->course?->title, 50),
                        'date_time' => $classTime,
                        'type' => $individual->class?->type,
                        'learning_tool' => $individual->class?->learning_tool,
                        'status' => $statusArray,
                    ];
                    
                    if ($classType === 'individual') {
                        $individualArray[] = $classData;
                    } else {
                        $groupArray[] = $classData;
                    }
                }
            }
    
             if (!$groupclasses->isEmpty()) {
                foreach ($groupclasses as $group) {
                    $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->student_id,'student');
                    $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$group->student_id,'student');
                    $updated_time = date('h:i A',strtotime($DateTime));
                    $lsntime = strtotime($updated_time);
                    $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                    $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                    $curdate = strtotime($curdate);
                    $lsndate = date('Y-m-d',strtotime($DateTime));
                    $lsndate = strtotime($lsndate); 
                    $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                    $curtime = strtotime($updated_cur_time);
                    $lesson_duration = $group->class->duration;
                    $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                    $lsnendtime = $lsntimestamp;

                    // $status = $this->determinateClassStatus($group);
                    $classType = strtolower($group->class?->type);

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
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->meeting_id == NULL && $group->class->learning_tool == 'Zoom') {
                             $statusArray = [
                                'btnText' => __($group->status),
                                 'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->StudentURI == NULL && $group->class?->learning_tool == 'BBB')
                        {
                            $statusArray = [
                                'btnText' => __($group->status),
                                 'btnID' => 'lec_link2_'.$group->group_id,
                            ];
                        }
                        else if($curtime >= $lsntime && $curtime < $lsnendtime && $group->google_meet_link == NULL && $group->class?->learning_tool == 'Google_Meet')
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
                                'btnHref' =>  "javascript:void(0);",
                                'classDetails' => [
                                    'type' => $group->class->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Student'),
                                    'user_name' => $group->student->first_name.' '.$group->student->last_name,
                                    'user_id' => $group->student_id,
                                ],
                              
                            ];
                        }
                         else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'BBB')    
                        { 
                            $statusArray = [
                                'btnText' => __($group->status),
                                'btnHref' =>  $group->StudentURI,
                                'classType' => $group->class?->learning_tool,                
                            ];
                        }
                         else if($curtime == $lsntime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')    
                        { 
                            $statusArray = [
                                'btnText' => __($group->status),
                                'btnHref' =>  $group->google_meet_link,
                                'classType' => $group->class->learning_tool,                
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
                         else if($curtime > $lsntime && $curtime < $lsnendtime && $group->status != "Completed" && $group->status != "Missed" && $group->class->learning_tool == 'Zoom')
                		{
                             $statusArray = [
                                'btnText' => __($group->status),
                                'btnHref' =>  "javascript:void(0);",
                              
                                'classDetails' => [
                                    'type' => $group->class->learning_tool,
                                    'lesson_id' => $group->id,
                                    'group_id' =>$group->group_id,
                                    'meeting_id' => $group->meeting_id,
                                    'user_type' => __('Student'),
                                    'user_name' => $group->student->first_name.' '.$group->student->last_name,
                                    'user_id' => $group->student_id,
                                ],
                              
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed" && $group->class->learning_tool == 'BBB')
                		{
                            $statusArray = [
                                'btnText' => __($group->status),
                                'btnHref' =>  $group->StudentURI,
                                'classType' => $group->class->learning_tool,                
                            ];
                        }
                         else if($curtime > $lsntime && $curtime < $lsnendtime &&$group->status != "Completed"&&$group->status != "Missed" && $group->class->learning_tool == 'Google_Meet')
                		{
                            $statusArray = [
                                'btnText' => __($group->status),
                                'btnHref' =>  $group->google_meet_link,
                                'classType' => $group->class->learning_tool,                
                            ];
                        }
                     
                    
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Zoom')
                        {
                             $statusArray = [
                                    'btnText' => __($group->status),
                                      'btnID' => 'lec_link2_'.$group->group_id,
                                ]; 
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'BBB')
                        {
                            $statusArray = [
                                    'btnText' => __($group->status),
                                      'btnID' => 'lec_link2_'.$group->group_id,
                                ]; 
                        }
                         else if(($curtime == $lsnendtime || $curtime > $lsnendtime) && $group->class->learning_tool == 'Google_Meet')
                        {
                             $statusArray = [
                                    'btnText' => __($group->status),
                                      'btnID' => 'lec_link2_'.$group->group_id,
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

                        $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->student_id,'student');
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $classData = [
                            'sr_no' => $counter++,
                            'id' => $group->id,
                            'instructor' => Str::limit($group->instructor?->first_name.' '.$group->instructor?->last_name, 50),
                            'course' => Str::limit($group->course?->title, 50),
                            'date_time' => $classTime,
                            'type' => $group->class?->type,
                            'learning_tool' => $group->class?->learning_tool,
                            // 'zoomApiKey'=> $api_key,
                            // 'zoomApiSecret'=> $api_secret,
                            'zoomSdkKey'=> $sdk_key,
                            'zoomSdkSecret'=> $sdk_secret,
                            // 'zoomApiJwtToken'=> $api_jwt_token,
                            // 'zoomMeetingSignature' => $group->zoom_meeting_signature,
                            'status' => $statusArray,
                        ];
                    }
                    else{

                        $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->student_id,'student');
                        $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                        $classData = [
                            'sr_no' => $counter++,
                            'id' => $group->id,
                            'instructor' => Str::limit($group->instructor?->first_name.' '.$group->instructor?->last_name, 50),
                            'course' => Str::limit($group->course?->title, 50),
                            'date_time' => $classTime,
                            'type' => $group->class?->type,
                            'learning_tool' => $group->class?->learning_tool,
                            'status' => $statusArray,
                        ];
                    }
                    
                    if ($classType === 'individual') {
                        $individualArray[] = $classData;
                    } else {
                        $groupArray[] = $classData;
                    }
                }
            }
            return response()->json([
                'individual' => $individualArray,
                'group' => $groupArray,
            ]);
        } catch (\Throwable $th) {
            dd($th);
            return response(['status' => 0, 'message' => __($th->getMessage())], 500); 
        }
    }
    
    protected function determinateClassStatus($class)
    {
        
    
        $DateTime = changetimeByZone($class->class_date.' '.date('h:i A',strtotime($class->class_time)),$class->student_id,'student');
        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$class->student_id,'student');
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
    
        // Future class
        if ($currentDate < $classDate) {
            return ['btnText' => __('Scheduled')];
        }
        else if ($currentDate == $classDate) {     // Today's class
            // Class hasn't started yet
            if ($currentTime < $classTime) {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime && $class->meeting_id == NULL && $tool == 'Zoom') {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime && $class->StudentURI == NULL && $tool == 'BBB') {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime && $class->google_meet_link == NULL && $tool == 'Google_Meet') {
                return ['btnText' => __('Waiting')];
            }
            else if ($currentTime >= $classTime && $currentTime < $classEndTime) {   // Class is ongoing
                if ($isCompleted || $isMissed) {
                    return ['btnText' => __($class->status)];
                }
                else{
                    switch ($tool) {
                        case 'Zoom':
                            return [
                                // 'btnText' => __('Start Class'),
                                'btnText' => __('Join Class'),
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
                                'btnText' => __('Join Class'),
                                'btnHref' => $class->StudentURI,
                                'classType' => $tool,                
                            ];
                            
                        case 'Google_Meet':
                            return [
                                'btnText' => __('Join Class'),
                                'btnHref' => $class->google_meet_link,
                                'classType' => $tool,                
                            ];
                            
                        default:
                            return ['btnText' => __('Waiting')];
                    }
                }
            }
            else if ($currentTime == $classEndTime) {    // Class just ended
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
        else if ($currentDate > $classDate) {    // Past class
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
        else if ($isCompleted) {    // Default case
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




    public function studentClassHistory() {

        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

         
            $groupArray = [];
            $individualArray = [];
            $data['pageTitle'] = __("Class-History");
            $student_user_id=$user->id;
        
            $getStudent = Student::where('user_id',$student_user_id)->first();
            $student_id = $getStudent->id;
            $today_date = date('Y-m-d');

            $getAllClassesHistory = ClassDetail::where('student_id',$student_id)
            ->where('class_date' ,'<',$today_date)
            ->whereHas('class', fn($query) => $query->where('type', 'Individual'))
            // ->whereIn('status',['Completed','Missed'])
            ->orderBy('class_date', 'ASC')
            ->orderBy('class_time', 'DESC')
            // ->paginate(10);
            ->get();



            $getAllGroupClassesHistory = ClassDetail::where('student_id', $student_id)
                ->where('class_date', '<', $today_date)
                ->whereHas('class', fn($query) => $query->where('type', 'Group'))
                // ->orderBy('id', 'DESC')
                ->orderBy('group_id', 'DESC')
                ->get()
                ->unique('class_id');

            $data['getAllClassesHistory'] = $getAllClassesHistory;
            $data['getAllGroupClassesHistory'] = $getAllGroupClassesHistory;
            $data['base_url'] = url('/');
      
            if(!$data['getAllClassesHistory']->isEmpty()){
                foreach ($data['getAllClassesHistory'] as $key => $individual) {
                    $statusArray = array();
                    
                    $DateTime = changetimeByZone($individual->class_date.' '.date('h:i A',strtotime($individual->class_time)),$individual->student_id,'student');
                    $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$individual->student_id,'student');
                    $updated_time = date('h:i A',strtotime($DateTime));
                    $lsntime = strtotime($updated_time);
                    $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                    $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                    $curdate = strtotime($curdate);
                    $lsndate = date('Y-m-d',strtotime($DateTime));
                    $lsndate = strtotime($lsndate); 
                    $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                    $curtime = strtotime($updated_cur_time);
                    $lesson_duration = $individual->class->duration;
                    $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                    $lsnendtime = $lsntimestamp;

                    if($curdate > $lsndate)
                    {
                        if(($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Zoom' && $individual->is_instructor_joined == 1)
                        {
                        
                            $status1 = $individual->status;
                        }
                        else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'BBB' && $individual->is_instructor_joined == 1 ) {
                         
                            $status1 = $individual->status;
                        }
                        else if (($individual->status == "Pending" || $individual->status == "Scheduled") && $individual->class->learning_tool == 'Google_Meet' && $individual->is_instructor_joined == 1) {
                        
                            $status1 = $individual->status;
                        }
                        else if($individual->status == "Completed"){
                             $status1 = $individual->status;
                        }
                        else if(($individual->status == "Pending" || $individual->status == "Scheduled")){
                             $status1 = $individual->status;
                        }
                        else
                            {
                                if($individual->status == "Missed"){
                                     $status1 = $individual->status;
                                }
                                else{
                                    $status1 = $individual->status;
                                }
                            }
                    }
                    else{
                        if($individual->status == "Completed"){
                             $status1 = $individual->status;
                        }
                        else{
                            {
                                if($individual->status == "Missed"){
                                     $status1 = $individual->status;
                                }
                                else{
                                     $status1 = $individual->status;
                                }
                            }
                        }
                    }

                $classType = strtolower($individual->class->type); // 'individual' or 'group'
                    $individualArray[] = [
                        'sr_no' => $key + 1,
                        'type' => $classType,
                        'id' => $individual->id,
                        'instructor'=> Str::limit($individual->instructor->first_name.' '.$individual->instructor->last_name, 50),
                        'status' => $status1,
                        'course'=> Str::limit($individual->course ? $individual->course->title : null, 50),
                        'date_time'=> date('d-m-Y', strtotime($individual->class_date)).' '.date("D", strtotime($individual->class_date)).' '.$updated_time,
                    ];
                }
            }

            if(!$data['getAllGroupClassesHistory']->isEmpty()){
                foreach ($data['getAllGroupClassesHistory'] as $key => $group) {
                    $DateTime = changetimeByZone($group->class_date.' '.date('h:i A',strtotime($group->class_time)),$group->student_id,'student');
                    $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$group->student_id,'student');
                    $updated_time = date('h:i A',strtotime($DateTime));
                    $lsntime = strtotime($updated_time);
                    $classTime = date('d-m-Y h:i A',strtotime($DateTime));
                    $curdate = date("Y-m-d",strtotime($CurrentDateTime));
                    $curdate = strtotime($curdate);
                    $lsndate = date('Y-m-d',strtotime($DateTime));
                    $lsndate = strtotime($lsndate); 
                    $updated_cur_time = date("h:i A",strtotime($CurrentDateTime));
                    $curtime = strtotime($updated_cur_time);
                    $lesson_duration = $group->class->duration;
                    $lsntimestamp = strtotime($updated_time) + $lesson_duration*60;
                    $lsnendtime = $lsntimestamp;


                      if($curdate > $lsndate)
                    {
                        if($group->class->learning_tool == 'Zoom' && $group->is_instructor_joined == 1)
                            {	
                             
                                $status2 = $group->status;
                            }
                            else if ($group->class->learning_tool == 'BBB' &&  $group->is_instructor_joined == 1 ) {
                            
                                 $status2 = $group->status;
                            }
                            else if ($group->class->learning_tool == 'Google_Meet' && $group->is_instructor_joined == 1) {
                            
                                $status2 = $group->status;
                            }
                            else if($group->status == "Completed"){
                                 $status2 = $group->status;
                            }
                            else if(($group->status == "Pending" || $group->status == "Scheduled")){
                                $status2 = $group->status;
                            }
                            else{
                                if($group->status == "Missed"){
                                      $status2 = $group->status;
                                }
                                else{
                                    $status2 = $group->status;
                                }
                            }
                    }
                    else{
                        if($group->status == "Completed"){
                             $status2 = $group->status;
                        }
                        else{
                            if($group->status == "Missed"){
                                      $status2 = $group->status;
                            }
                            else{
                                 $status2 = $group->status;
                            }
                        }
                    }
                  
                    $classType = strtolower($group->class->type); // 'individual' or 'group'
                    $groupArray[] = [
                        'sr_no' => $key + 1,
                        'type' => $classType,
                        'id' => $group->id,
                        'instructor'=> Str::limit($group->instructor->first_name.' '.$group->instructor->last_name, 50),
                        'status' => $status1,
                        'course'=> Str::limit($group->course ? $group->course->title : null, 50),
                        'date_time'=> date('d-m-Y', strtotime($group->class_date)).' '.date("D", strtotime($group->class_date)).' '.$updated_time,
                    ];
                }
            }



            return response()->json([
                'individual' => $individualArray,
                'group' => $groupArray,
            ]);
                      
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }







    public function studentPendingPayments(Request $request) {

        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

         
            $errCode = $request->err_code;
            $errMsg = $request->err_msg;
          
            if(isset($errCode) && isset($errMsg)){
                if ($errCode == '001') { 
                    // $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                    // return redirect()->route('student.pending-payments.index');
                    return response()->json(['message' => "<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>"], 302);    
                }
                else if($errCode == '90'){
                    // $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                    // return redirect()->route('student.pending-payments.index');
                    return response()->json(['message' => "<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>"], 302);    
                }
            }
        
            $data['pageTitle'] = __("Pending-Payments");
            $student_user_id=$user->id;
            $getStudent = Student::where('user_id',$student_user_id)->first();
            $student_id = $getStudent->id;
            $today_date = date('Y-m-d');
       
    
            $getAllPendingPayments = CartManagement::where('user_id',$student_user_id)->where('created_by_type',2)
            ->orderBy('id','DESC')
            // ->get();
            ->paginate(10);
    
            $data['getAllPendingPayments'] = $getAllPendingPayments;
         
            $data['base_url'] = url('/');
      
            $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
                ->where('user_type', 3)
                ->where('is_seen', 'no')
                ->orderBy('created_at', 'DESC')
                ->get();
    
         
            if(!$data['getAllPendingPayments']->isEmpty()){
              
                foreach ($data['getAllPendingPayments'] as $key => $payment) {
                      $monthName = \Carbon\Carbon::create()->month($payment->month)->format('F');
                      $shortMonthName = \Carbon\Carbon::create()->month($payment->month)->format('M');
                    $result_array[] = [
                        'base_url' =>$data['base_url'],
                        'payment_id' => $payment->id,
                        'payment_type' => $payment->created_by_type,
                        'payment_user_id' =>  $payment->user?->id,
                        'payment_user_name' =>  $payment->user?->name,
                        'payment_course_id' => $payment->course?->id,
                        'payment_course_name' => $payment->course?->title,
                        'payment_amount' => get_currency_symbol().' '.$payment->price,
                        'issue_date' => date('d-m-Y',strtotime($payment->created_at)),
                        'issue_month' => $shortMonthName,
                        'on_action_btn_api_route' => route('student-pending-payment-checkout-details'),
                    ];
                }
            }
            return response()->json($result_array);
            // return view('frontend.student.settings.pending-payments',$data);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }






    public function studentPendingPaymentCheckoutDetail(Request $request)
    {
        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $messages = [
                'payment_id.required' => __('The Payment ID Is Required'),
                'payment_type.required' => __('The Payment Type Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'payment_id' => 'required',
                'payment_type' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
          

            $isPaymentExists = CartManagement::where('id',$request->payment_id)->first();
            if($isPaymentExists == null){
                return response()->json(['message' => 'Given Payment ID Does Not Exists'], 302); 
            }

            if($isPaymentExists->created_by_type != $request->payment_type){
                return response()->json(['message' => 'Invalid Payment Type For Current Given Payment'], 302); 
            }

            $cartID = $request->payment_id;
            $type = $request->payment_type;

            $data['createdByType'] = $type;
            $data['cartID'] = $cartID;
            $data['pageTitle'] = "Checkout";
            $cart = CartManagement::where('id',$cartID)->first();
            $courseName = $cart->course->title;
            $data['courseName'] = $courseName;
            $data['userInfo'] =  $cart->user;
    
            if($data['cartID'] == null){
                $data['carts'] = CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$type)->get();
            }
            else{
                $data['carts'] = CartManagement::whereUserId(@Auth::id())
                ->where('id',$data['cartID'])
                ->where('created_by_type',$type)->get();
            }
    
            $data['student'] = auth::user()->student;
            $data['countries'] = Country::orderBy('country_name', 'asc')->get();
            $data['banks'] = Bank::orderBy('name', 'asc')->where('status', 1)->get();
    
            if($data['cartID'] == null){
                $cart = CartManagement::whereUserId(@Auth::id())
                ->where('created_by_type',$type)->first();
                $data['paymentCartID'] = $cart->id;
            }
            else{
                $cart = CartManagement::where('id',$data['cartID'])
                ->where('created_by_type',$type)->first();
                $data['paymentCartID'] = $data['cartID']; 
            }
    
    
            if (old('country_id')) {
                $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
            }
    
            if (old('state_id')) {
                $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
            }
    
            $razorpay_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('razorpay_conversion_rate') ? get_option('razorpay_conversion_rate') : 0);
            $data['razorpay_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($razorpay_grand_total_with_conversion_rate, 2));
    
            $paystack_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0);
            $data['paystack_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($paystack_grand_total_with_conversion_rate, 2));
    
            $sslcommerz_grand_total_with_conversion_rate = ($data['carts']->sum('price') + $data['carts']->sum('shipping_charge')  + get_platform_charge($data['carts']->sum('price'))) * (get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0);
            $data['sslcommerz_grand_total_with_conversion_rate'] = (float)preg_replace("/[^0-9.]+/", "", number_format($sslcommerz_grand_total_with_conversion_rate, 2));
         
            if(empty(get_option('faysal_base_url'))){
                $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
            }
            else{
                // $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
                $data['faysalBankUrl']= get_option('faysal_base_url');
            }
    
            if(empty(get_option('faysal_merchant_id'))){
                $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
            }
            else{
                // $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
                $data['faysalBankMerchantID']= get_option('faysal_merchant_id'); 
            }
    
            if(empty(get_option('faysal_secure_key'))){
                $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
            }
            else{
                // $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
                $faysal_bank_secure_key= get_option('faysal_secure_key'); 
            }
    
            $basketID = (int)$cart->id.time();
            $tranAmount = (int)$cart->price;
            $Currency = get_option('faysal_currency')?get_option('faysal_currency'):'PKR';

            $faysal_bank_token = $this->getFaysalBankAccessToken($data['faysalBankMerchantID'], $faysal_bank_secure_key,$tranAmount,$basketID,$Currency); 
            $data['faysalBankToken'] = $faysal_bank_token;
            $data['randSignature'] =  uniqid();
            $data['generalUrl']= route('student.pay');
          
            $billing_address_section = [
                'student_first_name' => $data['student']->first_name,
                'student_last_name' => $data['student']->last_name,
                'student_email' => $data['student']->user?->email,
                'student_address' => $data['student']->address,
                'student_country_id' => $data['student']->country_id,
                'student_country_name' => $data['student']->country?->country_name,
                // 'student_state_id' => $data['student']->state_id,
                // 'student_state_name' => $data['student']->state?->name,
                // 'student_city_id' => $data['student']->city_id,
                // 'student_city_name' => $data['student']->city?->name,
                'student_zip_code' => $data['student']->postal_code,
                'student_phone_number' => $data['student']->phone_number,
            ];
            $item_details_array = array();
            foreach ($data['carts'] as $key => $cart) {
                if (get_currency_placement() == 'after'){
                    $price = get_number_format($cart->price, 2).' '.get_currency_symbol();
                }
                else{
                    $price = get_currency_symbol().' '.get_number_format($cart->price, 2);
                }

                $item_details_array[] = [
                    'course_id' => $cart->course?->id,
                    'course_image' => getImageFile($cart->course?->image_path),
                    'course_title' => $cart->course?->title,
                    'course_detail_web_route' => route('course-details', $cart->course?->slug),
                    'course_detail_api_route' => route('my-course-show-student', $cart->course?->slug),
                    'course_price' => $price
                ];
            }
            $order_review_section = [
                'total_items_in_cart' => $data['carts']->count(),
                'items_details' => $item_details_array,
            ];
            if (get_currency_placement() == 'after'){
                $subTotal = get_number_format($data['carts']->sum('price')).' '.get_currency_symbol();
            }
            else{
                $subTotal = get_currency_symbol().' '.get_number_format($data['carts']->sum('price'));
            }
        
            if (get_currency_placement() == 'after'){
                $discount = '-'.get_number_format($data['carts']->sum('discount')).' '.get_currency_symbol();
            }
            else{
                $discount = '-'.get_currency_symbol().' '.get_number_format($data['carts']->sum('discount'));
            }

            if (get_currency_placement() == 'after'){
                $grandTotal = get_number_format($data['carts']->sum('price')).' '.get_currency_symbol();
            }
            else{
                $grandTotal = get_currency_symbol().' '.get_number_format($data['carts']->sum('price'));
            }

            $billing_summary_section = [
                'sub_total' => $subTotal,
                'discount' => $discount,
                'grand_total' => $grandTotal,
                'conversion_rate' => '1 ' .get_currency_code(). ' = ?',
            ];
            if (get_option('cash_on_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('cash_on'),
                    'conversion_rate'=> null,
                    'currency'=> null,
                ];
            }
           
            if (get_option('faysal_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('faysal_bank'),
                    'conversion_rate'=> get_option('faysal_conversion_rate'),
                    'currency'=> get_option('faysal_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('faysal_conversion_rate'),
                ];
            }
            if (get_option('meezan_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('meezan_bank'),
                    'conversion_rate'=> get_option('meezan_conversion_rate'),
                    'currency'=> get_option('meezan_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('meezan_conversion_rate'),
                ];
            }
            if (get_option('paypal_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('paypal'),
                    'conversion_rate'=> get_option('paypal_conversion_rate'),
                    'currency'=> get_option('paypal_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('paypal_conversion_rate'),
                ];
            }
            if (get_option('stripe_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('stripe'),
                    'conversion_rate'=> get_option('stripe_conversion_rate'),
                    'currency'=> get_option('stripe_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('stripe_conversion_rate'),
                ];
            }
            if (get_option('mollie_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('mollie'),
                    'conversion_rate'=> get_option('mollie_conversion_rate'),
                    'currency'=> get_option('mollie_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('mollie_conversion_rate'),
                ];
            }
            if (get_option('im_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('instamojo'),
                    'conversion_rate'=> get_option('im_conversion_rate'),
                    'currency'=> get_option('im_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('im_conversion_rate'),
                ];
            }
            if (get_option('razorpay_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('razorpay'),
                    'conversion_rate'=> get_option('razorpay_conversion_rate'),
                    'currency'=> get_option('razorpay_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('razorpay_conversion_rate'),
                ];
            }
            if (get_option('paystack_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('paystack'),
                    'conversion_rate'=> get_option('paystack_conversion_rate'),
                    'currency'=> get_option('paystack_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('paystack_conversion_rate'),
                ];
            }
            if (get_option('sslcommerz_status') == 1){
                $availablePaymentMethods[] = [
                    'method'=>__('sslcommerz'),
                    'conversion_rate'=> get_option('sslcommerz_conversion_rate'),
                    'currency'=> get_option('sslcommerz_currency'),
                    'calculated_conversion_rate_price' => (int)$data['carts']->sum('price') * (int)get_option('sslcommerz_conversion_rate'),
                ];
            }
            $result_array = [
                'billingAddressSectionData' => $billing_address_section,
                'availablePaymentMethods' => $availablePaymentMethods,
                'orderReviewSectionData' => $order_review_section,
                'billingSummarySectionData' => $billing_summary_section,
            ];
            return response()->json($result_array);
            dd($data);
          
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }







    public function studentPendingPaymentPayCheckout(Request $request)
{

        try {
           
            $fromApi = 1;
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $messages = [
                'payment_id.required' => __('The Payment ID Is Required'),
                'payment_type.required' => __('The Payment Type Is Required'),
                'payment_method.required' => __('The Payment Method Is Required'),
                'payment_method.in' => __('The Payment Method Must Have Either of Following Values: "faysal_bank" or "meezan_bank"'),
            ];
        
            $validator = Validator::make($request->all(), [
                'payment_id' => 'required',
                'payment_type' => 'required',
                'payment_method' => 'required|in:faysal_bank,meezan_bank',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
          

            $isPaymentExists = CartManagement::where('id',$request->payment_id)->first();
            if($isPaymentExists == null){
                return response()->json(['message' => 'Given Payment ID Does Not Exists'], 302); 
            }

            if($isPaymentExists->created_by_type != $request->payment_type){
                return response()->json(['message' => 'Invalid Payment Type For Current Given Payment'], 302); 
            }

           
        // $createdByType = $request->payment_type;
        $cartID = $request->payment_id;

       
   
        if ($request->payment_method == 'paypal') {
            if (empty(env('PAYPAL_CLIENT_ID')) || empty(env('PAYPAL_SECRET')) || empty(env('PAYPAL_MODE'))) {
                return response()->json(['message' => __('Paypal payment gateway is off!')], 302); 
            }
        }
     
        if ($request->payment_method == 'faysal_bank') {
            if (empty(get_option('faysal_merchant_id')) || empty(get_option('faysal_secure_key')) || empty(get_option('faysal_base_url'))
            || empty(get_option('faysal_access_token_url'))) {
                return response()->json(['message' => __('Faysal bank payment gateway is off!')], 302); 
            }
        }

        if ($request->payment_method == 'meezan_bank') {
            if (empty(get_option('meezan_user_name')) || empty(get_option('meezan_user_password')) || empty(get_option('meezan_port_address'))) {
                return response()->json(['message' => __('Meezan bank payment gateway is off!')], 302); 
            }
        }
   
      
    if ($request->payment_method != 'faysal_bank')  {
        if($cartID != null){
            $is_order_already_placed = $this->checkOrderExists($request->payment_method , $cartID);
            if($is_order_already_placed){
                   $checkORDER = Order::where('cart_id',$cartID)->first();
                   if($checkORDER->payment_status == 'paid'){
                        return response()->json(['message' => __('Order For Given Payment ID Already Being Paid!')], 302); 
                   }
                   else{
                        $order_data = [
                            'status' => true,
                            'data' => $checkORDER
                        ];
                   }
            }
            else{
                   $order_data = $this->placeOrder($request->payment_method , $cartID);
            }
        }
        else{
            $order_data = $this->placeOrder($request->payment_method); 
        }

        if($order_data['status']){
            $order = $order_data['data'];
        }else{
            return response()->json(['message' =>  __('Something went wrong while creating order for payment!')], 302);
        }
    }
      
  
        if ($request->payment_method == 'paypal') {
            $total = $order->grand_total * (get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');
            $object = [
                'id' => $order->uuid,
                'payment_method' => PAYPAL,
                'currency' => get_option('paypal_currency')
            ];
            $getWay = new BasePaymentService($object);
            $responseData = $getWay->makePayment($total);
            if($responseData['success']){
                $order->payment_id = $responseData['payment_id'];
                $order->save();
                $result_array[] = [
                    'paypal_payment_flow_redirect_url' => $responseData['redirect_url'],
                    'other_details' => $responseData,
                ];
                return response()->json($result_array);
                // return Redirect::away($responseData['redirect_url']);
            }else{
                $getOrderItemsData2 = Order_item::where('order_id',$order->id)->get();
                if(!$getOrderItemsData2->isEmpty()){
                    foreach ($getOrderItemsData2 as $key => $item) {
                        $item->delete();
                    }
                }
                $getOrderData2 = Order::where('id',$order->id)->first();
                $getOrderData2->delete();
                return response()->json(['message' =>  __('Something went wrong during paypal payment process flow! Please try again')], 302);
            }

        }
        else if ($request->payment_method == 'faysal_bank')  {
         
            if(empty(get_option('faysal_base_url'))){
                $data['faysalBankUrl']= env('FAYSAL_BANK_BASE_URL');
            }
            else{
                $data['faysalBankUrl']= get_option('faysal_base_url');
            }
            if(empty(get_option('faysal_merchant_id'))){
                $data['faysalBankMerchantID']= env('FAYSAL_BANK_MERCHANT_ID');
            }
            else{
                $data['faysalBankMerchantID']= get_option('faysal_merchant_id'); 
            }
            if(empty(get_option('faysal_secure_key'))){
                $faysal_bank_secure_key= env('FAYSAL_BANK_SECURE_KEY');
            }
            else{
                $faysal_bank_secure_key= get_option('faysal_secure_key'); 
            }
        
            $cart = CartManagement::where('id',$cartID)->first();
            // $basketID = (int)$cartID.time();
            $basketID = 'LMS-P-'. time();
            $tranAmount = (int)$cart->price;
            $Currency = get_option('faysal_currency')?get_option('faysal_currency'):'PKR';

            $faysal_bank_token = $this->getFaysalBankAccessToken($data['faysalBankMerchantID'], $faysal_bank_secure_key,$tranAmount,$basketID,$Currency); 
            $data['randSignature'] =  uniqid();
          
            // $carts = CartManagement::whereUserId($user->id)
            // ->where('id',$cartID)
            // ->where('created_by_type',$createdByType)->get();
            $userInfo =  $cart->user;
            $courseName = $cart->course->title;
            $formParametersArray = [
                'CURRENCY_CODE' => $Currency,
                'MERCHANT_ID' => $data['faysalBankMerchantID'],
                'TOKEN' => $faysal_bank_token,
                'SUCCESS_URL' => route('student.successfulFaysalApi',['user_id'=>$user->id,'cart_id'=>$cartID]),
                'FAILURE_URL' => route('student.failureFaysalApi'),
                'CANCEL_URL' => route('student.failureFaysalApi'),
                'CHECKOUT_URL' => route('student.checkoutFaysalApi',['user_id'=>$user->id,'cart_id'=>$cartID]),
                'BASKET_ID' => $basketID,
                'SIGNATURE' => $data['randSignature'],
                'VERSION' => "MERCHANT-CART 0.1",
                'PROCCODE' => '00',
                'TRAN_TYPE' => 'ECOMM_PURCHASE',
                'STORE_ID' => '',
                'RECURRING_TXN' => false,
                'CUSTOMER_NAME' => $userInfo ? $userInfo->name : '',
                'MERCHANT_CUSTOMER_ID' => $userInfo? $userInfo->id : '',
                'CUSTOMER_EMAIL_ADDRESS' => $userInfo? $userInfo->email : '',
                'CUSTOMER_MOBILE_NO' => $userInfo? $userInfo->area_code.$userInfo->mobile_number : '',
                'TXNAMT' => $tranAmount,
                'ORDER_DATE' => date('Y-m-d H:i:s', time()),
                'TXNDESC' => $courseName,
                
            ];
            $result_array = [
                'user_id' => $user->id,
                'cart_id' => (int)$cartID,
                'frontend_form-action_url' => $data['faysalBankUrl'],
                'form_required_parameters_values'=> $formParametersArray,
            ];
            return response()->json($result_array);
        }
        else if ($request->payment_method == 'meezan_bank')  {

            $total = $order->grand_total * (get_option('meezan_conversion_rate') ? get_option('meezan_conversion_rate') : 0);
            $total = number_format($total, 2,'.','');

            $responseData = $this->makeMeezanPayment($order->user_id,$total ,null ,$order->id , $cartID,$fromApi);
            if(isset($responseData['errorMessage'])){
                $getOrderItemsData2 = Order_item::where('order_id',$order->id)->get();
                if(!$getOrderItemsData2->isEmpty()){
                    foreach ($getOrderItemsData2 as $key => $item) {
                        $item->delete();
                    }
                }
                $getOrderData2 = Order::where('id',$order->id)->first();
                $getOrderData2->delete();
                return response()->json(['message' =>  __($responseData['errorMessage'])], 302);
            }
            else{
                $result_array[] = [
                    // 'user_id' => $user->id,
                    'cart_id' => (int)$cartID,
                    'order_id' => $order->id,
                    'meezan_payment_flow_redirect_url' => $responseData['redirectURL'],
                ];
                return response()->json($result_array);
            }
            // $responseData = $getWay->makePayment($total ,null ,$order->id , $cartID);       
    }
        } catch (\Throwable $th) {
            dd($th);
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
}


    public function getFaysalBankAccessToken($merchant_id,$secured_key,$trans_amount = null, $basket_id = null, $Currency = null){
        if(empty(get_option('faysal_access_token_url'))){
            $accessTokenURL= env('FAYSAL_BANK_ACCESS_TOKEN_URL');
        }
        else{
            $accessTokenURL= get_option('faysal_access_token_url'); 
        }
        $tokenApiUrl = $accessTokenURL;
    
        $urlPostParams = sprintf( 
        'MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s',
        $merchant_id, 
        $secured_key,
        $trans_amount, 
        $basket_id,
        $Currency
        ); 
        $array = array();
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPostParams); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP PayFast Example'); 
        $response = curl_exec($ch); 
        curl_close($ch); 
        $payload = json_decode($response); 
        $token = isset($payload->ACCESS_TOKEN) ? $payload->ACCESS_TOKEN: ''; 
        return $token;
    }




    private function makeMeezanPayment($user_id,$amount,$post_data = null,$order_id = null , $cart_id = null, $fromApi = 0)
{
        try {
         
        $meezan_port_address = get_option('meezan_port_address');
        $meezan_user_name = get_option('meezan_user_name');
        $meezan_user_password = get_option('meezan_user_password');
        $meezan_pkr_currency_code = 586;
        $meezan_usd_currency_code = 840;

            $data['success'] = false;
            $data['redirect_url'] = '';
            $data['payment_id'] = '';
            $data['message'] = '';
            $amount         =(int)$amount; 
            $amountUpdated         =$amount * 100; 
            $orderIDupdated = rand(1,99999).$order_id;

            if($fromApi == 1){
                $returnURLUpdated = 'https://portal.cefonlineacademy.com/billing/payment-confirmation/'.$cart_id.'/'.$order_id;
            }
            else{
                $returnURLUpdated = route('student.successfulMeezan') .'/'.$order_id .'/'. $cart_id;
            }
        
        
            $tokenApiUrl =  $meezan_port_address.'register.do';
            $urlPostParams = array( 
                'userName'=>$meezan_user_name, 
                'password'=>$meezan_user_password, 
                'orderNumber'=>$orderIDupdated, 
                'amount'=>$amountUpdated, 
                'currency'=>$meezan_pkr_currency_code, 
                'returnUrl'=>$returnURLUpdated, 
            ); 
        
            $query=urldecode(http_build_query($urlPostParams));
            $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Example'); 
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch); 
        $arrayResult = json_decode($response, true);
        curl_close($ch); 
    
        $generatedErrorCode = $arrayResult['errorCode'];
        if($generatedErrorCode != 0){
            $generatedErrorMessage = $arrayResult['errorMessage'];
            $data = [
                'errorMessage' => "<strong>". $generatedErrorMessage."</strong><br/>",
            ];
            return $data;
        }
        else{
            $generatedOrderID = $arrayResult['orderId'];
            $generatedFormUrl = $arrayResult['formUrl'];
            // $this->OrderId = $generatedOrderID;
        
            $getOrderData = Order::where('id',$order_id)->first();
            $getOrderData->meezan_transaction_id = $generatedOrderID;
            $getOrderData->update();
         
            $data = [
                'redirectURL' => $generatedFormUrl,
            ];
            return $data;

            // return $data['redirectURL'] = $generatedFormUrl;
            // header("Location: $generatedFormUrl");
            // exit();
        }
          
    } catch (\Exception $ex) {
            Log::info($ex);
            $data = [
                'errorMessage' => "<strong>". $ex->getMessage()."</strong><br/>",
            ];
            return $data;
          
    }
}







    public function successfulMeezanApi(Request $request){
    
          $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            
            $messages = [
                'cart_id.required' => __('The Cart ID Is Required'),
                'order_id.required' => __('The Order ID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'cart_id' => 'required',
                'order_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $cart_id = $request->cart_id;
            $order_id = $request->order_id;
            $user_id = $user->id;
        
        $checkOrder = Order::where('id',$order_id)->first();
   
        if($checkOrder == null){
            $result_array = [
                        'status' => false,
                        'message' => 'Given Order ID Does not exists in records',
                    ];
            return response()->json($result_array,302);
        }
         if($checkOrder->user_id != $user_id){
             $result_array = [
                        'status' => false,
                        'message' => 'Invalid Order ID For Given User',
                    ];
            return response()->json($result_array,302);
        }
        if($checkOrder->payment_status == 'paid'){
               $successMsg0 = "Amount already deposited and paid.";
                $result_array = [
                        'status' => true,
                        'message' => __($successMsg0),
                        'data' => $checkOrder
                ];
                   return response()->json($result_array);
        }
        $checkCart = CartManagement::where('id',$cart_id)->first();
        if($checkCart == null){
            $result_array = [
                        'status' => false,
                        'message' => 'Given Cart ID Does not exists in records',
                    ];
            return response()->json($result_array,302);
        }
    
        if($checkCart->user_id != $user_id){
             $result_array = [
                        'status' => false,
                        'message' => 'Invalid Cart ID For Given User',
                    ];
            return response()->json($result_array,302);
        }
       
       
          
      $result_array = array();
        $getOrderData = Order::where('id',$order_id)->first();
        $transactionID = $getOrderData->meezan_transaction_id;
        $meezanTransactionID = $getOrderData->meezan_transaction_id;
        $portAddress= get_option('meezan_port_address');
		$apiUserName= get_option('meezan_user_name');
		$apiUserPassword= get_option('meezan_user_password');
		$tokenApiUrl = $portAddress.'getOrderStatusExtended.do';
		$urlPostParams = array( 
			'userName'=>$apiUserName, 
			'password'=>$apiUserPassword, 
			'orderId'=>$meezanTransactionID, 
			); 
          
        $query=urldecode(http_build_query($urlPostParams));
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Example'); 
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $arrayResult = json_decode($response, true);
        curl_close($ch);  
        // dd($arrayResult);
        $errorCode = $arrayResult['errorCode'];
        $errorMsg = $arrayResult['errorMessage'];
        if($errorCode == 0){
            $orderStatus = $arrayResult['orderStatus'];
            $actionCode = $arrayResult['actionCode'];
            $actionDescription = $arrayResult['actionCodeDescription'];
            $amount = $arrayResult['amount'];
            $date = $arrayResult['date'];
            $formatted_date = date('y-m-d h:i:sa', $date);
            
            $orderNumber = $arrayResult['orderNumber'];
            if($orderStatus == 2){  //SUCCESSFULL
                $getPaymentData = Order::where('id',$order_id)->first();
                // $successMsg = $errorMsg."<br>Amount was deposited successfully.";
                $successMsg = "Amount was deposited successfully.";
                $getPaymentData->meezan_transaction_id = $transactionID;
                $getPaymentData->payment_id = $transactionID;
                $getPaymentData->meezan_payment_source = 'Card';
                $getPaymentData->meezan_order_number = $orderNumber;
                $getPaymentData->meezan_payment_date = date('y-m-d h:i:sa');
                $getPaymentData->meezan_trans_response = $response;
                if($cart_id != null){
                $getPaymentData->cart_type = 2;
                $getPaymentData->created_by_type = 2;
                }
                else{
                $getPaymentData->cart_type = 1;
                $getPaymentData->created_by_type = 1;
                }
                $getPaymentData->payment_status = 'paid';
                $getPaymentData->delivery_status = 1;
                $getPaymentData->update();
                // $enrollment = new Enrollment();
                // $enrollment->order_id = $getOrderData->id;
                // $enrollment->user_id = auth()->id();
                // $enrollment->course_id = $request->course_id;
                // $enrollment->owner_user_id = $courseExits->user_id ?? null;
                // $enrollment->start_date = now();
                // $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                // $enrollment->save();

               
                    $email_sent = $this->sendPaymentEmail($order_id);

                    $target_url = route('student_payment.completed');
                    $text = __("Student payment has been accepted by meezan bank.");
                    $this->send($text, 1, $target_url, null);

                    //Email Send Code
                 
                    // $email_sent = $this->sendPaymentEmailAdmin($order_id);
                    // $this->showToastrMessage('success',"<strong>". $successMsg."</strong><br/>");

                if($cart_id != null){
                    $getCartData = CartManagement::where('id',$cart_id)->first();
                    if($getCartData){
                        $getCartData->delete();
                    }
                }
                else{
                    // $userID = $getPaymentData->user_id;
                      CartManagement::whereUserId($user_id)
                        ->where('created_by_type',1)->delete();
                }

                    $result_array = [
                        'status' => true,
                        'message' => $successMsg,
                        'data' => $getPaymentData
                    ];
                   return response()->json($result_array);
                // return redirect()->route('student.pending-payments.index');
            }
            else{
                if($orderStatus == 6){
                    $errorMsg = "Authorization is declined.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 0){
                    $errorMsg = "Order registered, but not paid.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 1){
                    $errorMsg = "Transaction has been approved.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 3){
                    $errorMsg = "Authorization has been reversed.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }
                else if($orderStatus == 4){
                    $errorMsg = "Transaction has been refunded.<br>";
                    if($actionCode != 0){
                        $errorMsg .= $actionDescription;	
                    }
                }

                if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                if($getCartData){
                    $getCartData->error_trans_response = $response;
                    $getCartData->update();
                }
                }
                else{
                $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
                if(!$getOrderItemsData2->isEmpty()){
                    foreach ($getOrderItemsData2 as $key => $item) {
                        $item->delete();
                    }
                }
                $getOrderData2 = Order::where('id',$order_id)->first();
                $getOrderData2->delete();

                $getEnrollmentData = Enrollment::where('order_id',$order_id)->first();
                if($getEnrollmentData){
                    $getEnrollmentData->delete();
                }

                $getCartData = CartManagement::whereUserId($user_id)
                        ->where('created_by_type',1)->get();
                        
                    if(!$getCartData->isEmpty()){
                        foreach ($getCartData as $key => $value) {
                            $value->error_trans_response = $response;
                            $value->update();
                        }
                    }
                }

                $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
                  $result_array = [
                        'status' => false,
                        'message' => $errorMsg,
                        'data' => []
                    ];
                   return response()->json($result_array);

                // return redirect()->route('student.pending-payments.index');
                // return redirect()->back();
            }
        }
        else{
            if($cart_id != null){
                $getCartData = CartManagement::where('id',$cart_id)->first();
                if($getCartData){
                    $getCartData->error_trans_response = $response;
                    $getCartData->update();
                }
                }
                else{
                 $getCartData = CartManagement::whereUserId($user_id)
                        ->where('created_by_type',1)->get();
                        
                    if(!$getCartData->isEmpty()){
                        foreach ($getCartData as $key => $value) {
                            $value->error_trans_response = $response;
                            $value->update();
                        }
                    }
                }

            $getOrderItemsData2 = Order_item::where('order_id',$order_id)->get();
            if(!$getOrderItemsData2->isEmpty()){
                foreach ($getOrderItemsData2 as $key => $item) {
                    $item->delete();
                }
            }
            $getOrderData2 = Order::where('id',$order_id)->first();
            $getOrderData2->delete();
    
            $this->showToastrMessage('warning',"<strong>". $errorMsg."</strong><br/>");
               $result_array = [
                        'status' => false,
                        'message' => $errorMsg,
                        'data' => []
                    ];
            return response()->json($result_array);
            // return redirect()->route('student.pending-payments.index');
        }
}






  public function sendPaymentEmail($payment_id = null,$payment_order_id = null){
     
        $receipt_number = rand(0,9999999);
        $getPaymentData = Order_item::where('order_id',$payment_id)->first();
        if($getPaymentData->order->payment_method == 'meezan_bank'){
            $data2['order_payment_date'] = $getPaymentData->order->meezan_payment_date;
            $data2['payment_source'] = $getPaymentData->order->meezan_payment_source;
            $data2['payment_method'] = $getPaymentData->order->payment_method;
        }
        else if($getPaymentData->order->payment_method == 'faysal_bank'){
            $data2['order_payment_date'] = $getPaymentData->order->faysal_payment_date;
            $data2['payment_source'] = $getPaymentData->order->faysal_payment_source;
            $data2['payment_method'] = $getPaymentData->order->payment_method;
        }
        else{
            $data2['order_payment_date'] = date('Y-m-d h:i:s');
            $data2['payment_source'] = 'Card';
            $data2['payment_method'] = $getPaymentData->order?->payment_method;
        }
        if($payment_order_id != null){
            $ID = $payment_order_id;
        }
        else{
            // $ID = $getPaymentData->order_id; 
            $ID = 'LMS-P-'.$getPaymentData->order_id; 
        }

        $data2['result'] = $getPaymentData;
        $data2['receipt_number_new']  = $receipt_number;
        $toEmail = $getPaymentData->user?->email;
        if(get_option('app_email')){
            $data2['app_email']  = get_option('app_email');
        }
        else{
            $data2['app_email']  = env('MAIL_FROM_ADDRESS');
        }
        if(get_option('app_contact_number')){
            $data2['app_contact_number']  = get_option('app_contact_number');
        }
        else{
            $data2['app_contact_number']  = env('APP_CONTACT_NUMBER');
        }
        if(get_option('facebook_url') && get_option('facebook_url') != '#'){
            $data2['facebook_url']  = get_option('facebook_url');
        }
        else{
            $data2['facebook_url']  = env('FACEBOOK_URL');
        }
        if(get_option('twitter_url') && get_option('twitter_url') != '#'){
            $data2['twitter_url']  = get_option('twitter_url');
        }
        else{
            $data2['twitter_url']  = env('TWITTER_URL');
        }
        if(get_option('instagram_url') && get_option('instagram_url') != '#'){
            $data2['instagram_url']  = get_option('instagram_url');
        }
        else{
            $data2['instagram_url']  = env('INSTAGRAM_URL');
        }
        if(get_option('youtube_url') && get_option('youtube_url') != '#'){
            $data2['youtube_url']  = get_option('youtube_url');
        }
        else{
            $data2['youtube_url']  = env('YOUTUBE_URL');
        }
        if(get_option('app_location')){
            $data2['app_location']  = get_option('app_location');
        }
        else{
            $data2['app_location']  = env('APP_LOCATION');
        }
        $mainArray = array();
        $mainArray = [
            'data2' => $data2,
            'ID' => $ID,
        ];
        // $view = view('frontend.student.emails.completed_payment',compact('data2'));
     
        $adminEmail = get_option('admin_email');
        $FinancialEmail = get_option('financial_email');
      
      
     
        if($adminEmail && $FinancialEmail){
            Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$adminEmail,$FinancialEmail) {
                $message->to($toEmail)
                        ->cc([
                        
                            $adminEmail,
                            $FinancialEmail
                        ])
                        ->subject('Payment Received | CEF Online');
            });
        }
        else if($adminEmail){
             Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$adminEmail) {
                $message->to($toEmail)
                        ->cc([
                        
                            $adminEmail,
                        ])
                        ->subject('Payment Received | CEF Online');
            });
        }
        else if($FinancialEmail){
             Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail,$FinancialEmail) {
                $message->to($toEmail)
                        ->cc([
                        
                            $FinancialEmail,
                        ])
                        ->subject('Payment Received | CEF Online');
            });
        }
        else{
            Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
            $message->to($toEmail)
                    ->subject('Payment Received | CEF Online');
            });
        }
   
        
        return true;
}






  public function sendPaymentEmailAdmin($payment_id = null,$payment_order_id = null){
     
        $user = Auth::user();
        if($user){
            $role = $user->role;
            $adminEmail = $user->email;
            if($role == 1){ // Admin
                $receipt_number = rand(0,9999999);
                $getPaymentData = Order_item::where('order_id',$payment_id)->first();
                if($getPaymentData->order->payment_method == 'meezan_bank'){
                    $data2['order_payment_date'] = $getPaymentData->order->meezan_payment_date;
                    $data2['payment_source'] = $getPaymentData->order->meezan_payment_source;
                    $data2['payment_method'] = $getPaymentData->order->payment_method;
                }
                else if($getPaymentData->order->payment_method == 'faysal_bank'){
                    $data2['order_payment_date'] = $getPaymentData->order->faysal_payment_date;
                    $data2['payment_source'] = $getPaymentData->order->faysal_payment_source;
                    $data2['payment_method'] = $getPaymentData->order->payment_method;
                }
                else{
                    $data2['order_payment_date'] = date('Y-m-d h:i:s');
                    $data2['payment_source'] = 'Card';
                    $data2['payment_method'] = $getPaymentData->order?->payment_method;
                }
                if($payment_order_id != null){
                    $ID = $payment_order_id;
                }
                else{
                    // $ID = $getPaymentData->order_id; 
                    $ID = 'LMS-P-'.$getPaymentData->order_id; 
                }

                $data2['result'] = $getPaymentData;
                $data2['receipt_number_new']  = $receipt_number;
                // $toEmail = $getPaymentData->user?->email;
                $toEmail = $adminEmail;
                if(get_option('app_email')){
                    $data2['app_email']  = get_option('app_email');
                }
                else{
                    $data2['app_email']  = env('MAIL_FROM_ADDRESS');
                }
                if(get_option('app_contact_number')){
                    $data2['app_contact_number']  = get_option('app_contact_number');
                }
                else{
                    $data2['app_contact_number']  = env('APP_CONTACT_NUMBER');
                }
                if(get_option('facebook_url') && get_option('facebook_url') != '#'){
                    $data2['facebook_url']  = get_option('facebook_url');
                }
                else{
                    $data2['facebook_url']  = env('FACEBOOK_URL');
                }
                if(get_option('twitter_url') && get_option('twitter_url') != '#'){
                    $data2['twitter_url']  = get_option('twitter_url');
                }
                else{
                    $data2['twitter_url']  = env('TWITTER_URL');
                }
                if(get_option('instagram_url') && get_option('instagram_url') != '#'){
                    $data2['instagram_url']  = get_option('instagram_url');
                }
                else{
                    $data2['instagram_url']  = env('INSTAGRAM_URL');
                }
                if(get_option('youtube_url') && get_option('youtube_url') != '#'){
                    $data2['youtube_url']  = get_option('youtube_url');
                }
                else{
                    $data2['youtube_url']  = env('YOUTUBE_URL');
                }
                if(get_option('app_location')){
                    $data2['app_location']  = get_option('app_location');
                }
                else{
                    $data2['app_location']  = env('APP_LOCATION');
                }
                $mainArray = array();
                $mainArray = [
                    'data2' => $data2,
                    'ID' => $ID,
                ];
                // $view = view('frontend.student.emails.completed_payment',compact('data2'));

                $adminEmail = get_option('admin_email');
                $FinancialEmail = get_option('financial_email');

            if($adminEmail && $FinancialEmail){
                Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
                    $message->to($toEmail)
                            ->cc([
                              
                                $adminEmail,
                                $FinancialEmail
                            ])
                            ->subject('Payment Received | CEF Online');
                });
            }
            else if($adminEmail){
                Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
                    $message->to($toEmail)
                            ->cc([
                              
                                $adminEmail,
                            ])
                            ->subject('Payment Received | CEF Online');
                });
            }
            else if($FinancialEmail){
                Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
                    $message->to($toEmail)
                            ->cc([
                             
                                $FinancialEmail,
                            ])
                            ->subject('Payment Received | CEF Online');
                });
            }
            else{
                Mail::send('frontend.student.emails.completed_payment', $mainArray, function($message) use($toEmail) {
                    $message->to($toEmail)
                       
                            ->subject('Payment Received | CEF Online');
                });
            }

                return true;

            }
        }
        else{
          
        }

  
}




    private function checkOrderExists($payment_method , $cartID = null)
    {
        $check = Order::where('cart_id',$cartID)->first();
        if($check){
            return true;
        }
        else{
            return false;
        }
    }


    private function placeOrder($payment_method , $cartID = null)
    {
     
        DB::beginTransaction();
        try{
            if($cartID != null){
                $carts = CartManagement::whereUserId(@Auth::id())->where('id',$cartID)->get();
                $cart = CartManagement::whereUserId(@Auth::id())->where('id',$cartID)->first();
            }
            else{
                $carts = CartManagement::whereUserId(@Auth::id())->get();
                $cart = null;
            }
         
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->cart_id = $cartID;
            $order->order_number = rand(100000, 999999);
            $order->sub_total = $carts->sum('price');
            $order->discount = $carts->sum('discount');
            // $order->platform_charge = get_platform_charge($carts->sum('price'));
            $order->platform_charge = 0.0;
            if($payment_method == 'cash_on'){
                $order->shipping_cost = $carts->sum('shipping_charge');
            }
            else{
                $order->shipping_cost = 0.0;
            }
         
            $order->current_currency = get_currency_code();
            $order->grand_total = $order->sub_total + $order->shipping_cost + $order->platform_charge;
            $order->payment_method = $payment_method;
            if($cartID == null){
                $order->created_by_type = 1;
                $order->cart_type = 1;
            }
            else{
                $order->created_by_type = 2; 
                $order->cart_type = 2;
            }
            if($cart != null){
                $order->month = $cart->month;
                $order->year = $cart->year;
            }
            $payment_currency = '';
            $conversion_rate = '';
  
            if ($payment_method == 'paypal') {
                $payment_currency = get_option('paypal_currency');
                $conversion_rate = get_option('paypal_conversion_rate') ? get_option('paypal_conversion_rate') : 0;
            }elseif ($payment_method == 'faysal_bank') {
                $payment_currency = get_option('faysal_currency');
                $conversion_rate = get_option('faysal_conversion_rate') ? get_option('faysal_conversion_rate') : 0;
            }elseif ($payment_method == 'meezan_bank') {
                $payment_currency = get_option('meezan_currency');
                $conversion_rate = get_option('meezan_conversion_rate') ? get_option('meezan_conversion_rate') : 0;
            } elseif ($payment_method == 'stripe') {
                $payment_currency = get_option('stripe_currency');
                $conversion_rate = get_option('stripe_conversion_rate') ? get_option('stripe_conversion_rate') : 0;
            } elseif ($payment_method == 'bank') {
                $payment_currency = get_option('bank_currency');
                $conversion_rate = get_option('bank_conversion_rate') ? get_option('bank_conversion_rate') : 0;
            } elseif ($payment_method == 'mollie') {
                $payment_currency = get_option('mollie_currency');
                $conversion_rate = get_option('mollie_conversion_rate') ? get_option('mollie_conversion_rate') : 0;
            } elseif ($payment_method == 'instamojo') {
                $payment_currency = get_option('im_currency');
                $conversion_rate = get_option('im_conversion_rate') ? get_option('im_conversion_rate') : 0;
            } elseif ($payment_method == 'paystack') {
                $payment_currency = get_option('paystack_currency');
                $conversion_rate = get_option('paystack_conversion_rate') ? get_option('paystack_conversion_rate') : 0;
            } elseif ($payment_method == 'sslcommerz') {
                $payment_currency = get_option('sslcommerz_currency');
                $conversion_rate = get_option('sslcommerz_conversion_rate') ? get_option('sslcommerz_conversion_rate') : 0;
            } elseif ($payment_method == 'mercadopago') {
                $payment_currency = get_option('mercado_currency');
                $conversion_rate = get_option('mercado_conversion_rate') ? get_option('mercado_conversion_rate') : 0;
            } elseif ($payment_method == 'flutterwave') {
                $payment_currency = get_option('flutterwave_currency');
                $conversion_rate = get_option('flutterwave_conversion_rate') ? get_option('flutterwave_conversion_rate') : 0;
            } elseif ($payment_method == 'coinbase') {
                $payment_currency = get_option('coinbase_currency');
                $conversion_rate = get_option('coinbase_conversion_rate') ? get_option('coinbase_conversion_rate') : 0;
            } elseif ($payment_method == 'zitopay') {
                $payment_currency = get_option('zitopay_currency');
                $conversion_rate = get_option('zitopay_conversion_rate') ? get_option('zitopay_conversion_rate') : 0;
            } elseif ($payment_method == IYZIPAY) {
                $payment_currency = get_option('iyzipay_currency');
                $conversion_rate = get_option('iyzipay_conversion_rate') ? get_option('iyzipay_conversion_rate') : 0;
            } elseif ($payment_method == BITPAY) {
                $payment_currency = get_option('bitpay_currency');
                $conversion_rate = get_option('bitpay_conversion_rate') ? get_option('bitpay_conversion_rate') : 0;
            } elseif ($payment_method == BRAINTREE) {
                $payment_currency = get_option('braintree_currency');
                $conversion_rate = get_option('braintree_conversion_rate') ? get_option('braintree_conversion_rate') : 0;
            }

            $order->payment_currency = $payment_currency;
            $order->conversion_rate = $conversion_rate;
            if ($conversion_rate) {
                $order->grand_total_with_conversation_rate = ($order->sub_total + $order->platform_charge + $order->shipping_cost) * $conversion_rate;
            }
        
            $order->save();
     
            foreach ($carts as $cart) {
             
                if ($cart->course_id) {
                    $courseExits = Course::find($cart->course_id);
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                  
                    $order_item->user_id = Auth::user()->id;
                    $order_item->product_id = $cart->product_id;
                    $order_item->course_id = $cart->course_id;
                    $order_item->type = 1;
                    $order_item->receiver_info = $cart->receiver_info;
                    $order_item->owner_user_id = $cart->course ? $cart->course->user_id : null;
                    $order_item->unit_price = $cart->price;
                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                    if ($adminCommission) {
                        $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->sell_commission = $adminCommission;
                    } else {
                        $order_item->owner_balance = $cart->price;
                    }
                
                    $order_item->save();

                    if($cart->created_by_type == 1){        // Student Made Purchase Himself Of Course
                        $enrollment = new Enrollment();
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = Auth::id();
                        $enrollment->course_id = $cart->course_id;
                        $enrollment->owner_user_id = $courseExits->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($courseExits->access_period) ? Carbon::now()->addDays($courseExits->access_period) : MAX_EXPIRED_DATE;
                        $enrollment->save();
                    }
                    else if($cart->created_by_type == 2){  // Admin made manual course payment for student 
                    }
                 

                    $this->addAffiliateHistory($cart,$order,$order_item);

                } 
                elseif ($cart->bundle_id) {
                   
                    // $bundleIds = Enrollment::where('user_id', auth()->id())->whereNotIn('course_id', $cart->bundle_course_ids)->where('end_date', '<', now())->select('course_id')->get()->toArray();
                    $courses = Course::whereIn('id', $cart->bundle_course_ids)->get();
                    $bundleUserId = $cart->bundle->user_id;
                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $bundleUserId)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                    if($adminCommission){
                        $adminCommissionAmount = admin_commission_by_percentage($cart->price, $adminCommission);
                        $totalDistributeAmount = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    }
                    else{
                        $adminCommissionAmount = admin_commission_by_percentage($cart->price, $adminCommission);
                        $totalDistributeAmount = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                    }

                    $totalCourseAmount = $courses->sum('price');
                    $finalPriceRatio = $totalDistributeAmount / $totalCourseAmount;
                    $adminCommissionRatio = ($totalCourseAmount) ? $adminCommissionAmount / $totalCourseAmount : 0;
                    $sellCommissionRatio = ($totalCourseAmount) ? $adminCommission / $totalCourseAmount : 0;

                    /*
                     * All bundle course added but not calculate balance, commission etc
                     */
                    foreach ($courses as $course) {
                        /*
                        need to add bundle course in order list
                        Old paid course check with bundle course
                        */

                        $order_item = new Order_item();
                        $order_item->order_id = $order->id;
                        $order_item->user_id = Auth::user()->id;
                        $order_item->bundle_id = $cart->bundle_id;
                        $order_item->owner_user_id = $bundleUserId;
                        $order_item->unit_price = $course->price;
                        $order_item->owner_balance = $finalPriceRatio * $course->price;
                        $order_item->sell_commission = $sellCommissionRatio * $course->price;
                        $order_item->admin_commission = $adminCommissionRatio * $course->price;
                        $order_item->course_id = $course->id;
                        $order_item->type = 3; //bundle course
                        $order_item->save();
                        $this->addAffiliateHistory($cart,$order,$order_item);
                    }

                } elseif ($cart->consultation_slot_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::id();
                    $order_item->owner_user_id = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->instructor_user_id : null;
                    $order_item->consultation_slot_id = $cart->consultation_slot_id;
                    $order_item->consultation_date = $cart->consultation_date;
                    $order_item->unit_price = $cart->price;

                    $userPackage =  UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->whereIn('packages.package_type', [PACKAGE_TYPE_SAAS_INSTRUCTOR, PACKAGE_TYPE_SAAS_ORGANIZATION])->where('user_packages.user_id', $order_item->owner_user_id)->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->select('user_packages.*')->first();
                    $adminCommission = ($userPackage && $userPackage->admin_commission && get_option('saas_mode')) ? $userPackage->admin_commission : get_option('sell_commission');
                    if ($adminCommission) {
                        $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->sell_commission = $adminCommission;
                    } else {
                        $order_item->owner_balance = $cart->price;
                    }

                    $order_item->type = 4;
                    $order_item->save();

                    //Start:: Need to add Booking History
                    $booking = new BookingHistory();
                    $booking->order_id = $order->id;
                    $booking->order_item_id = $order_item->id;
                    $booking->instructor_user_id = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->instructor_user_id : null;
                    $booking->student_user_id = Auth::id();
                    $booking->consultation_slot_id = $cart->consultation_slot_id;
                    $booking->date = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->date : null;
                    $booking->day = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->day : null;
                    $booking->time = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->time : null;
                    $booking->duration = is_array($cart['consultation_details']) ? $cart['consultation_details'][0]->duration : null;
                    $booking->type = $cart->consultation_available_type;
                    $booking->status = 0; //Pending
                    $booking->save();

                    //End:: Add Booking History
                } elseif ($cart->product_id) {
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = Auth::id();
                    $order_item->type = 2;
                    $order_item->product_id = $cart->product_id;
                    $order_item->receiver_info = $cart->receiver_info;
                    $order_item->owner_user_id = $cart->product ? $cart->product->user_id : null;
                    $order_item->unit_price = $cart->price;
                    $order_item->quantity = $cart->quantity;
                    $order_item->unit = $cart->quantity;
                    $adminCommission = get_option('sell_commission');
                    if ($adminCommission) {
                        $order_item->admin_commission = admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->owner_balance = $cart->price - admin_commission_by_percentage($cart->price, $adminCommission);
                        $order_item->sell_commission = $adminCommission;
                    } else {
                        $order_item->owner_balance = $cart->price;
                    }

                    $order_item->save();

                }
              
            }
            DB::commit();
            return ['status' => true,'data' => $order];
        }catch (\Exception $e){
            DB::rollBack();
            $this->logger->log('Cannot Create Order', $e->getMessage());
            return ['status' => false,'data' => null];
        }

    }







    private function addAffiliateHistory($cart, $order, $order_item){
        if(get_option('referral_status')){
            $refRequest = AffiliateRequest::where(['affiliate_code' => $cart->reference,'status' => STATUS_APPROVED])->first();
            if(!is_null($refRequest)) {
                $refUser = User::where(['id' => $refRequest->user_id])->first();
                $alreadyAffiliated = AffiliateHistory::where(['user_id'=>$refUser->id,'course_id' =>$cart->course_id])->first();
                if($refUser->is_affiliator == AFFILIATOR && is_null($alreadyAffiliated)) {
                    $commission = referral_sell_commission($cart->price - $cart->discount);
                    $affiliate = new AffiliateHistory();
                    $affiliate->hash = Str::uuid()->getHex();
                    $affiliate->user_id = $refUser->id;
                    $affiliate->buyer_id = Auth::id();
                    $affiliate->order_id = $order->id;
                    $affiliate->order_item_id = $order_item->id;
                    if ($cart->course_id) {
                        $affiliate->course_id = $cart->course_id;
                    } elseif ($cart->bundle_id) {
                        $affiliate->bundle_id = $cart->bundle_id;
                    }
                    $affiliate->actual_price = $cart->price;
                    $affiliate->discount = $cart->discount;
                    $affiliate->commission = $commission;
                    $affiliate->commission_percentage = get_option('referral_commission_percentage');
                    $affiliate->save();
                }
            }
        }

    }


    public function studentUpdateProfile(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $messages = [
                // 'student_id.required' => __('The Student ID Is Required'),
                // 'first_name.required' => __('The First Name Is Required'),
                // 'first_name.string' => __('The First Name Must Be String'),
                // 'first_name.max' => __('The First Name Must Be At Most 100 characters long'),
                // 'last_name.required' => __('The Last Name Is Required'),
                // 'last_name.string' => __('The Last Name Must Be String'),
                // 'last_name.max' => __('The Last Name Must Be At Most 100 characters long'),
                // 'phone_number.required' => __('The Phone Number Is Required'),
                'email.email' => __('The Email Must Be Valid'),
                // 'gender.required' => __('The Gender Is Required'),
                'gender.in' => __('The Gender Must be either "Male" or "Female" or "Others"'),
                'image.mimes' => __('The Image Must be either "JPEG" or "PNG" or "JPG" type'),
                'image.max' => __('The Image Size Must Be atmost 1 MB'),
            ];
        
            $validator = Validator::make($request->all(), [
                // 'student_id' => 'required',
                // 'first_name' => 'required|string|max:100',
                // 'last_name' => 'required|string|max:100',
                // 'phone_number' => 'required',
                'email' => 'nullable|email',
                'gender' => 'nullable|in:Male,Female,Others',
                'image' => 'nullable|mimes:jpeg,png,jpg|max:1024',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

               
            $getStudent = Student::where('user_id',$user->id)->first();
            if($getStudent == null){
                 return response()->json(['message' => 'Given Student Does Not Exists'], 302); 
            }
        
            $student = $this->studentModel->getRecordByID($getStudent->id);
            if($student == null){
                return response()->json(['message' => 'Given Student ID Does Not Exists'], 302); 
            }
         
          
            
            // $user = User::find($student->user_id);
        
            if($request->email){
                
                if (User::where('id', '!=', $user->id)->where('email', $request->email)->count() > 0)
                {
                    
                    return response()->json(['message' =>  __('Given Email already exist')], 302); 
                } 
                else if (User::where('id', '!=', $user->id)->where('mobile_number', $request->phone_number)->count() > 0)
                {
                    return response()->json(['message' =>  __('Given Mobile Number already exist')], 302); 
                } 
                else {
                    $user->email = $request->email;
                }
            }
            else if($request->phone_number){
                if (User::where('id', '!=', $user->id)->where('mobile_number', $request->phone_number)->count() > 0)
                {
                    return response()->json(['message' =>  __('Given Mobile Number already exist')], 302); 
                }
                else{
                    $user->mobile_number = $request->phone_number;
                }
            }
           
         
               
            if ($request->image)
            {
              
                $this->deleteFile($user->image); // delete file from server
    
                $image = $this->saveImage('user', $request->image, null, 'null'); // new file upload into server
    
            } else {
                $image = $user->image;
            }
    
            if($request->first_name && $request->last_name){
                $user->name = $request->first_name . ' ' . $request->last_name;
            }
            else if($request->first_name){
                $user->name = $request->first_name;
            }
            else if($request->last_name){
                $user->name = $request->last_name;
            }
         
            $user->image = $image;

              if($request->phone_number){
                $user->mobile_number = $request->phone_number;
              }
        
            // $user->email = $request->email;
            $user->meta_title = $request->meta_title;
            $user->meta_description = $request->meta_description;
            $user->meta_keywords = $request->meta_keywords;
            if($request->hasFile('og_image')){
                $user->og_image = $this->saveImage('meta', $request->og_image, null, null);
            }
    
            $user->save();
            $user->refresh();
    
            $student_data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number ? $request->phone_number : $getStudent->phone_number,
                'about_me' => $request->about_me,
                'gender' => $request->gender ?  $request->gender : $getStudent->gender
            ];
    
            $student = $this->studentModel->updateById($student_data, $getStudent->id); // update category
    
            // $this->showToastrMessage('success', __('Successfully updated'));
            // return redirect()->back();
            return response()->json([
                'message' => __('Successfully updated'),
                'image_url' => $user->image_url,
            ], 200);
        } catch (\Throwable $th) {
           
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }


    public function getstudentAddressLocation() {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $country = Country::all()->pluck('country_name', 'id');
            $state = State::all()->pluck('name', 'id');
            $city = City::all()->pluck('name', 'id');

            $payload= [
                'country' => $country,
                'state' => $state,
                'city' => $city,
            ];

            return response()->json(['message' => __('Successfully fetched'), 'data' => $payload], 200);

        } catch (\Throwable $th) {
          
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }

    public function studentUpdateAddressLocation(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if ($user) {
                $role = $user->role;
                if ($role != 3) { // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }

                // Now that we have the authenticated student user, we can directly
                // use their ID to update their record. Assuming your User model
                // has a direct relationship to the Student model (e.g., one-to-one).

                $student = $user->student; // Assuming you have a 'student' relationship defined in your User model

                if (!$student) {
                    return response()->json(['message' => 'Student record not found for the logged-in user'], 404);
                }

                $messages = [
                    'country_id.required' => __('The Country ID Is Required'),
                    'postal_code.required' => __('The Postal Code Is Required'),
                    'address.required' => __('The Address Is Required'),
                ];

                $validator = Validator::make($request->all(), [
                    'country_id' => 'required',
                    'postal_code' => 'required',
                    'address' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return response(['status' => 0, 'error' => $validator->errors()], 500);
                }

                // Update the user's latitude and longitude
                $user->lat = $request->lat ?? $user->lat;
                $user->long = $request->long ?? $user->long;
                $user->save();

                $getStudent = Student::where('user_id',$user->id)->first();
                $data = [
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id ?? $getStudent->state_id,
                    'city_id' => $request->city_id ?? $getStudent->city_id,
                    'postal_code' => $request->postal_code,
                    'address' => $request->address,
                ];

                // Update the student's address using the retrieved $student model
                $this->studentModel->updateById($data, $student->id);

                return response()->json(['message' => __('Successfully updated')], 200);

            } else {
                return response()->json(['message' => 'User Is Not Logged In'], 401); // Changed status code to 401 for unauthorized
            }
        } catch (\Throwable $th) {
            return response(['status' => 0, 'message' => __($th->getMessage())], 500);
        }
    }




    public function studentUpdatePassword(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
    
            // Ensure the user has the "student" role
            if ($user->role != 3) { // Assuming role 3 is for students
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
    
            // Validate the input
            $messages = [
                'old_password.required' => __('The Old Password Is Required'),
                'new_password.required' => __('The New Password Is Required'),
            ];
    
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required',
            ], $messages);
    
            if ($validator->fails()) {
                return response(['status' => 0, 'error' => $validator->errors()], 500);
            }
    
            // Check if the old password matches
            if (Hash::check($request->old_password, $user->password)) {
                // Update password
                $user->password = Hash::make($request->new_password);
                $user->save();
                
                return response()->json(['message' => __('Password changed successfully')], 200);
            } else {
                return response()->json(['message' => __('Your old password does not match.')], 302);
            }
    
        } catch (\Throwable $th) {
            return response(['status' => 0, 'message' => __($th->getMessage())], 500);
        }
    }
    


        public function startZoomClassLive(Request $request){

        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3 && $role != 2){ // Both Student Teacher
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }



            $messages = [
                'lesson_id.required' => __('The Lesson ID Is Required'),
                'meeting_id.required' => __('The Meeting ID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'lesson_id' => 'required',
                'meeting_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }


            $isLessonExists = LiveClass::where('id',$request->lesson_id)->first();
            if($isLessonExists == null){
                return response()->json(['message' => 'Given Lesson ID Does Not Exists'], 302); 
            }

            $isMeetingExists = LiveClass::where('meeting_id',$request->meeting_id)->first();
            if($isMeetingExists == null){
                return response()->json(['message' => 'Given Meeting ID Does Not Exists'], 302); 
            }

            if($role == 2){
                    $isTutorExists = Instructor::where('user_id',$user->id)->first();
                    if($isTutorExists == null){
                        return response()->json(['message' => 'Given User ID Does Not Exists'], 302); 
                    }
            }
            else{
                  $isStudentExists = Student::where('user_id',$user->id)->first();
                    if($isStudentExists == null){
                        return response()->json(['message' => 'Given User ID Does Not Exists'], 302); 
                    }
            }
        

            $meeting_id = $request->meeting_id;
            if($role == 2){
                $user_type = __('Student');
                $user_name = $isTutorExists->first_name.' '.$isTutorExists->last_name;
            }
            else{
                $user_type = __('Tutor');
                $user_name = $isStudentExists->first_name.' '.$isStudentExists->last_name;
            }
           
            $user_id = $user->id;
            $lesson_id = $request->lesson_id;
       
            $lesson_duration = 30;
         
            $tutor_name = "New Tutor";
            $student_name = "New Student";
            $old_start_time = null;
            $old_end_time = null;
            $data['meeting_id'] = $meeting_id;
            $data['user_type'] = $user_type;
            $data['user_name'] = $user_name;
            $data['user_id'] = $user_id;
            $bklsnid = $lesson_id;
            // $zoomSettings = ZoomSetting::first();
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            $sdk_key = $zoomSettings->sdk_key;
            $sdk_secret = $zoomSettings->sdk_secret;
            $data['sdkKey'] = $sdk_key;
            $data['sdkSecret'] = $sdk_secret;
            $data['baseURL'] = url('/');
            $data['student'] = Student::where('id',$user_id)->first();
            // $data['student'] = $this->Generic_model->getSpecificRecord('qu_students',array('s_id' =>$_SESSION['qu_user_id']));
            $getLessonDetails = LiveClass::where('meeting_id',$meeting_id)->first();
            $instructor_id = $getLessonDetails->instructor_id;
            if($getLessonDetails != null){
                    $data['meeting_id'] = $getLessonDetails->meeting_id;
                    // $data['meeting_password'] = '12345';
                    $data['meeting_password'] = $getLessonDetails->meeting_id;
                    $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
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
                    $instructor_id = $getLessonDetails->user_id;
                    $tutor_name =getTutorNameFromID($instructor_id);
            }
        
            if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
                $given_start_time = $old_start_time;
                $given_end_time = $old_end_time;
            }
            else{
                $data_record_lesson = LiveClass::where('meeting_id',$meeting_id)->first();
               $student_id = $data_record_lesson->student_id;
                $given_end_time = $data_record_lesson->end_time_zoom;
            
                   if($role == 2){
                        $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
                        $given_start_time = strtotime($updated_current_time);
                   }
                   else{
                        $updated_current_time = getCurrentServerTimeByZone($student_id,'student');
                        $given_start_time = strtotime($updated_current_time);
                   }
              
    
            }
        
            if($given_start_time == null){
                $given_start_time = 0;
            }
            if($given_end_time == null){
                $given_end_time = 0;
            }
         
            // $data['lesson_duration'] = $lesson_duration;
            $data['lesson_id'] = $bklsnid;
            // $data['student_name'] = $student_name;
            $data['tutor_name'] = $tutor_name;
            $data['start_time'] = $given_start_time;
            $data['end_time'] = $given_end_time;
            $data['isMeetingPage'] = 'Yes';
            $result_array = [
                'lesson_id' => $data['lesson_id'],
                'tutor_name' => $data['tutor_name'],
                'user_name' => $data['user_name'],
                'meeting_id' => $data['meeting_id'],
                'meeting_password' => $data['meeting_password'],
                'meeting_signature' => $data['meeting_signature'],
                'session_name' => $data['session_name'],
                'user_type' => $data['user_type'],
                'user_id' => $data['user_id'],
                'base_url' => $data['baseURL'],
                'meeting_end_url' => $data['baseURL'].'/student/class-schedule',
                'startTime' => $data['start_time'],
                'endTime' => $data['end_time'],
                'sdkKey' => $data['sdkKey'],
                'sdkSecret' => $data['sdkSecret'],
                'role' => 0,
            ];
            return response()->json($result_array);
            // echo view('frontend.student.settings.meeting-page-zoom-ui',$data);
        } catch (\Throwable $th) {
             return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }


    }




       public function generateZoomToken(Request $request){

        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3 && $role != 2){ // Both Student Teacher
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }



            $messages = [
                'sdk_key.required' => __('The SDK Key Is Required'),
                'sessionName.required' => __('The Session Name Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
               'sdk_key'      => 'required|string',
                'sessionName' => 'required|string',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }


            $apiKey    = $request->sdk_key;
            $session   = $request->sessionName;
            if($role == 2){
                $roleType = 1;
            }
            else{
                  $roleType = 0;
            }
          
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
             $apiSecret = $zoomSettings->sdk_secret;

          
             // Issue at time
            $iat = time();
            // Expiration time (here: 2 hours)
            $exp = $iat + 2 * 60 * 60;

            // Build JWT payload
            $payload = [
                'app_key'   => $apiKey,
                'tpc'       => $session,
                'role_type' => $roleType,
                'version'   => 1,
                'iat'       => $iat,
                'exp'       => $exp,
            ];

            // Optional: custom header (Firebase\JWT sets these by default)
            $headers = [
                'alg' => 'HS256',
                'typ' => 'JWT',
            ];

            // Generate the JWT
            $signature = JWT::encode($payload, $apiSecret, 'HS256', null, $headers);

            return response()->json([ 'signature' => $signature ]);

        } catch (\Throwable $th) {
             return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }


    }




  
      public function startZoomClass(Request $request){

        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3 && $role != 2){ // Both Student Teacher
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }



            $messages = [
                'lesson_id.required' => __('The Lesson ID Is Required'),
                'meeting_id.required' => __('The Meeting ID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'lesson_id' => 'required',
                'meeting_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }


            $isLessonExists = ClassDetail::where('id',$request->lesson_id)->first();
            if($isLessonExists == null){
                return response()->json(['message' => 'Given Lesson ID Does Not Exists'], 302); 
            }

            $isMeetingExists = ClassDetail::where('meeting_id',$request->meeting_id)->first();
            if($isMeetingExists == null){
                return response()->json(['message' => 'Given Meeting ID Does Not Exists'], 302); 
            }

            if($role == 2){
                    $isTutorExists = Instructor::where('user_id',$user->id)->first();
                    if($isTutorExists == null){
                        return response()->json(['message' => 'Given User ID Does Not Exists'], 302); 
                    }
            }
            else{
                  $isStudentExists = Student::where('user_id',$user->id)->first();
                    if($isStudentExists == null){
                        return response()->json(['message' => 'Given User ID Does Not Exists'], 302); 
                    }
            }
        

            $meeting_id = $request->meeting_id;
            if($role == 2){
                $user_type = __('Student');
                $user_name = $isTutorExists->first_name.' '.$isTutorExists->last_name;
            }
            else{
                $user_type = __('Tutor');
                $user_name = $isStudentExists->first_name.' '.$isStudentExists->last_name;
            }
           
            $user_id = $user->id;
            $lesson_id = $request->lesson_id;
       
            $lesson_duration = 30;
         
            $tutor_name = "New Tutor";
            $student_name = "New Student";
            $old_start_time = null;
            $old_end_time = null;
            $data['meeting_id'] = $meeting_id;
            $data['user_type'] = $user_type;
            $data['user_name'] = $user_name;
            $data['user_id'] = $user_id;
            $bklsnid = $lesson_id;
            // $zoomSettings = ZoomSetting::first();
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            $sdk_key = $zoomSettings->sdk_key;
            $sdk_secret = $zoomSettings->sdk_secret;
            $data['sdkKey'] = $sdk_key;
            $data['sdkSecret'] = $sdk_secret;
            $data['baseURL'] = url('/');
            $data['student'] = Student::where('id',$user_id)->first();
            // $data['student'] = $this->Generic_model->getSpecificRecord('qu_students',array('s_id' =>$_SESSION['qu_user_id']));
            $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
            $instructor_id = $getLessonDetails->instructor_id;
            if($getLessonDetails != null){
                    $data['meeting_id'] = $getLessonDetails->meeting_id;
                    // $data['meeting_password'] = '12345';
                    $data['meeting_password'] = $getLessonDetails->meeting_id;
                    $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
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
                    $instructor_id = $getLessonDetails->instructor_id;
                    $tutor_name =getTutorNameFromID($instructor_id);
            }
        
            if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
                $given_start_time = $old_start_time;
                $given_end_time = $old_end_time;
            }
            else{
                $data_record_lesson = ClassDetail::where('meeting_id',$meeting_id)->first();
               $student_id = $data_record_lesson->student_id;
                $given_end_time = $data_record_lesson->end_time_zoom;
            
                   if($role == 2){
                        $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
                        $given_start_time = strtotime($updated_current_time);
                   }
                   else{
                        $updated_current_time = getCurrentServerTimeByZone($student_id,'student');
                        $given_start_time = strtotime($updated_current_time);
                   }
              
    
            }
        
            if($given_start_time == null){
                $given_start_time = 0;
            }
            if($given_end_time == null){
                $given_end_time = 0;
            }
         
            // $data['lesson_duration'] = $lesson_duration;
            $data['lesson_id'] = $bklsnid;
            // $data['student_name'] = $student_name;
            $data['tutor_name'] = $tutor_name;
            $data['start_time'] = $given_start_time;
            $data['end_time'] = $given_end_time;
            $data['isMeetingPage'] = 'Yes';
            $result_array = [
                'lesson_id' => $data['lesson_id'],
                'tutor_name' => $data['tutor_name'],
                'user_name' => $data['user_name'],
                'meeting_id' => $data['meeting_id'],
                'meeting_password' => $data['meeting_password'],
                'meeting_signature' => $data['meeting_signature'],
                'session_name' => $data['session_name'],
                'user_type' => $data['user_type'],
                'user_id' => $data['user_id'],
                'base_url' => $data['baseURL'],
                'meeting_end_url' => $data['baseURL'].'/student/class-schedule',
                'startTime' => $data['start_time'],
                'endTime' => $data['end_time'],
                'sdkKey' => $data['sdkKey'],
                'sdkSecret' => $data['sdkSecret'],
                'role' => 0,
            ];
            return response()->json($result_array);
            // echo view('frontend.student.settings.meeting-page-zoom-ui',$data);
        } catch (\Throwable $th) {
             return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }


    }



    
    public function startZoomClass2(Request $request){

        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $messages = [
                'lesson_id.required' => __('The Lesson ID Is Required'),
                'meeting_id.required' => __('The Meeting ID Is Required'),
                'user_id.required' => __('The User ID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'lesson_id' => 'required',
                'meeting_id' => 'required',
                'user_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }


            $isLessonExists = ClassDetail::where('id',$request->lesson_id)->first();
            if($isLessonExists == null){
                return response()->json(['message' => 'Given Lesson ID Does Not Exists'], 302); 
            }

            $isMeetingExists = ClassDetail::where('meeting_id',$request->meeting_id)->first();
            if($isMeetingExists == null){
                return response()->json(['message' => 'Given Meeting ID Does Not Exists'], 302); 
            }

            $isStudentExists = Student::where('id',$request->user_id)->first();
            if($isStudentExists == null){
                return response()->json(['message' => 'Given User ID Does Not Exists'], 302); 
            }

            $meeting_id = $request->meeting_id;
            $user_type = __('Student');
            $user_id = $request->user_id;
            $lesson_id = $request->lesson_id;
            $user_name = $isStudentExists->first_name.' '.$isStudentExists->last_name;
            $lesson_duration = 30;
         
            $tutor_name = "New Tutor";
            $student_name = "New Student";
            $old_start_time = null;
            $old_end_time = null;
            $data['meeting_id'] = $meeting_id;
            $data['user_type'] = $user_type;
            $data['user_name'] = $user_name;
            $data['user_id'] = $user_id;
            $bklsnid = $lesson_id;
            // $zoomSettings = ZoomSetting::first();
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            $sdk_key = $zoomSettings->sdk_key;
            $sdk_secret = $zoomSettings->sdk_secret;
            $data['sdkKey'] = $sdk_key;
            $data['sdkSecret'] = $sdk_secret;
            $data['baseURL'] = url('/');
            $data['student'] = Student::where('id',$user_id)->first();
            // $data['student'] = $this->Generic_model->getSpecificRecord('qu_students',array('s_id' =>$_SESSION['qu_user_id']));
            $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
            $instructor_id = $getLessonDetails->instructor_id;
            if($getLessonDetails != null){
                    $data['meeting_id'] = $getLessonDetails->meeting_id;
                    // $data['meeting_password'] = '12345';
                    $data['meeting_password'] = $getLessonDetails->meeting_id;
                    $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
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
                    $instructor_id = $getLessonDetails->instructor_id;
                    $tutor_name =getTutorNameFromID($instructor_id);
            }
        
            if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
                $given_start_time = $old_start_time;
                $given_end_time = $old_end_time;
            }
            else{
                $data_record_lesson = ClassDetail::where('meeting_id',$meeting_id)->first();
               $student_id = $data_record_lesson->student_id;
                $given_end_time = $data_record_lesson->end_time_zoom;
            
                $updated_current_time = getCurrentServerTimeByZone($student_id,'student');
                $given_start_time = strtotime($updated_current_time);
    
            }
        
            if($given_start_time == null){
                $given_start_time = 0;
            }
            if($given_end_time == null){
                $given_end_time = 0;
            }
         
            // $data['lesson_duration'] = $lesson_duration;
            $data['lesson_id'] = $bklsnid;
            // $data['student_name'] = $student_name;
            $data['tutor_name'] = $tutor_name;
            $data['start_time'] = $given_start_time;
            $data['end_time'] = $given_end_time;
            $data['isMeetingPage'] = 'Yes';
            $result_array = [
                'lesson_id' => $data['lesson_id'],
                'tutor_name' => $data['tutor_name'],
                'user_name' => $data['user_name'],
                'meeting_id' => $data['meeting_id'],
                'meeting_password' => $data['meeting_password'],
                'meeting_signature' => $data['meeting_signature'],
                'session_name' => $data['session_name'],
                'user_type' => $data['user_type'],
                'user_id' => $data['user_id'],
                'base_url' => $data['baseURL'],
                'meeting_end_url' => $data['baseURL'].'/student/class-schedule',
                'startTime' => $data['start_time'],
                'endTime' => $data['end_time'],
                'sdkKey' => $data['sdkKey'],
                'sdkSecret' => $data['sdkSecret'],
                'role' => 0,
            ];
            return response()->json($result_array);
            // echo view('frontend.student.settings.meeting-page-zoom-ui',$data);
        } catch (\Throwable $th) {
             return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }


    }



    public function UpdateMeetingSignatureApi(Request $request){
        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3 && $role != 2){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $messages = [
                'meeting_id.required' => __('The Meeting ID Is Required'),
                'meeting_signature.required' => __('The Meeting Signature Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'meeting_id' => 'required',
                'meeting_signature' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isMeetingExists = ClassDetail::where('meeting_id',$request->meeting_id)->first();
            if($isMeetingExists == null){
                return response()->json(['message' => 'Given Meeting ID Does Not Exists'], 302); 
            }
            $meeting_id = $request->meeting_id;
         
            $l_id = null;
            $meetingSignature = $request->meetingSignature;
          
            $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
            if(!$classes->isEmpty()){
                foreach ($classes as $key => $value) {
                    $l_id = $value->id;
                    $res = ClassDetail::where('id',$l_id)->first();
                    if($res){
                        if($res->zoom_meeting_signature == null){
                            $res->zoom_meeting_signature = $meetingSignature;
                            $res->update();
                        }
                    }
                    
                }
            }
           
            return response()->json(['message' => 'Zoom Meeting Signature Updated Successfully'], 200); 

        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
      
    }




    public function GetCurrentTimeZoomStudentApi(Request $request){
      
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $messages = [
                'meeting_id.required' => __('The Meeting ID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'meeting_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isMeetingExists = ClassDetail::where('meeting_id',$request->meeting_id)->first();
            if($isMeetingExists == null){
                return response()->json(['message' => 'Given Meeting ID Does Not Exists'], 302); 
            }
            $meeting_id = $request->meeting_id;
        

            $data_record_timezone = ClassDetail::where('meeting_id',$meeting_id)->first();
            $instructor_id =  $data_record_timezone->instructor_id;
            // $given_timezone = $data_record_timezone['timezone'];
            // date_default_timezone_set($given_timezone);
           
            $updated_current_time = getCurrentServerTimeByZone($instructor_id,'instructor');
            $curtime = strtotime($updated_current_time);
            
            $result_array = [
                'Current_Time' => $curtime
            ];
        
            return response()->json($result_array);
            return response()->json($curtime);
            // echo json_encode($curtime);
      } catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500); 
      }
    }




    public function UpdateUrlZoomStudentApi(Request $request){
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $messages = [
                'meeting_id.required' => __('The Meeting ID Is Required'),
                'current_time.required' => __('The Current Time Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'meeting_id' => 'required',
                'current_time' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isMeetingExists = ClassDetail::where('meeting_id',$request->meeting_id)->first();
            if($isMeetingExists == null){
                return response()->json(['message' => 'Given Meeting ID Does Not Exists'], 302); 
            }
            $meeting_id = $request->meeting_id;
            $currentStartTime = $request->current_time;
          
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
            return response()->json(['message' => 'Student Current Time Updated Successfully'], 200); 
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }



    public function CheckModeratorLeftZoomStudentApi(Request $request){
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $messages = [
                'meeting_id.required' => __('The Meeting ID Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'meeting_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isMeetingExists = ClassDetail::where('meeting_id',$request->meeting_id)->first();
            if($isMeetingExists == null){
                return response()->json(['message' => 'Given Meeting ID Does Not Exists'], 302); 
            }
            $meeting_id = $request->meeting_id;

            $is_moderator_left = null;
            $is_left = 'No';
            $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
            if(!$classes->isEmpty()){
                foreach ($classes as $key => $value) {
                    $is_moderator_left = $value->is_moderator_left;
                }
            }
            if($is_moderator_left == "1" || $is_moderator_left == '1' || $is_moderator_left === "1" || $is_moderator_left === '1' || $is_moderator_left == 1 || $is_moderator_left === 1){
                $is_left = 'Yes';
            }
            else if($is_moderator_left == "0"){
                $is_left = 'NO';
            }

            $result_array = [
                'is_left' => $is_left,
            ];
            return response()->json($result_array);
            return $is_left; 
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
      
    }



    public function studentCheckLessonStartTime(){
        try {
            $return = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

          
            $today_date = date("Y-m-d");
            $user_id = $user->id;
            $findStudent = Student::where('user_id',$user_id)->first();
            $student_id =  $findStudent->id;
            $getLessonScheduleOfStudent = ClassDetail::where('student_id',$student_id)->where('class_date','>=',$today_date)
            ->orderBy('class_date','ASC')->get();
            // $getLessonScheduleOfStudent = ClassDetail::where('student_id',$student_id)->where('class_date','>=',$today_date)
            // ->where('meeting_id','<>',null)->orderBy('class_date','ASC')->get();

            if(!$getLessonScheduleOfStudent->isEmpty())
                {
                    foreach ($getLessonScheduleOfStudent as $key=> $bokdlsn) 
                        {
                           
                            $DateTime = changetimeByZone($bokdlsn->class_date.' '.date('h:i A',strtotime($bokdlsn->class_time)),$student_id,'student');
                            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$student_id,'student');
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
                                    $return['user_name'] = getStudentNameFromID($bokdlsn->student_id);
                                    $return['user_id'] = $bokdlsn->student_id;
                                    $return['user_type'] = 'Student';
                                    return response()->json($return);
      
                                }
                                else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->StudentURI != NULL && $bokdlsn->class->learning_tool == 'BBB')
                                {
                                    $meetingID = $bokdlsn->meeting_id;
                                    $lesson_id = $bokdlsn->id;
                                    $return['id'] = $bokdlsn->id;
                                    $return['learning_tool'] = $bokdlsn->class->learning_tool;
                                    $return['url'] = $bokdlsn->StudentURI;
                                    $return['meeting_id'] = $bokdlsn->meeting_id;
                                    $return['user_name'] = getStudentNameFromID($bokdlsn->student_id);
                                    $return['user_id'] = $bokdlsn->student_id;
                                    $return['user_type'] = 'Student';
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
                                    $return['user_name'] = getStudentNameFromID($bokdlsn->student_id);
                                    $return['user_id'] = $bokdlsn->student_id;
                                    $return['user_type'] = 'Student';
                                    return response()->json($return);
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




    public function chatIndex()
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $users = Enrollment::where('enrollments.user_id', $user->id)
            ->join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->join('users', 'enrollments.owner_user_id', '=', 'users.id')
            ->select(
                'courses.id as course_id',
                'courses.title as course_title',
                'users.id as user_id',
                'users.name as user_name',
                'users.image as user_image'
            )->groupBy('enrollments.course_id')
            ->get();

        $data['title'] = 'Chat';
        $data['pageTitle'] = 'Chat';
        $data['data'] = $users->groupBy('user_id');
        $Admin_with_courses = array();
        if (!empty($data['data'])){
            foreach ($data['data'] as $key => $item) {
                $owner_courses = array();
                foreach ($item as $key => $course) {
                    $owner_courses[] =[
                        'course_id' => $course->course_id,
                        'course_title' => $course->course_title,
                        'sender_id' => $user->id,
                        'receiver_id' => $course->user_id,
                    ];
                }
                $Admin_with_courses[] = [
                    'owner_user_id' => $item[0]->user_id,
                    'owner_user_image' => getImageFile($item[0]->user_image),
                    'owner_user_name' => $item[0]->user_name,
                    'owner_courses' => $owner_courses,
                ];
            }
        }
        $result_array = [
            'senderRole' => $user->role,
            'senderId' => $user->id,
            'pusherKey' => get_option('pusher_app_key'),
            'pusherCluster' => get_option('pusher_app_cluster'),
            'Administrator with courses' => $Admin_with_courses,
        ];
        return response()->json($result_array);
        return view('frontend.student.chat.index',  $data);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
      
    }





    public function studentChatGetMessages(Request $request)
    {
        try {

            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            $messages = [
                'receiverId.required' => __('The Receiver ID Is Required'),
                'senderId.required' => __('The Sender ID Is Required'),
                'courseId.required' => __('The Course ID Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'receiverId' => 'required',
                'senderId' => 'required',
                'courseId' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isCourseExists = Course::where('id',$request->courseId)->first();
            if($isCourseExists == null){
                return response()->json(['message' => 'Given Course ID Does Not Exists'], 302); 
            }

            $receiverId = $request->receiverId;
            $senderId = $request->senderId;
            $courseId = $request->courseId;
            $messages = Chat::where(function ($query) use ($senderId, $receiverId, $courseId) {
                $query->where('sender_id', $senderId)
                    ->where('receiver_id', $receiverId)
                    ->where('course_id', $courseId);
            })
                ->orWhere(function ($query) use ($senderId, $receiverId, $courseId) {
                    $query->where('sender_id', $receiverId)
                        ->where('receiver_id', $senderId)
                        ->where('course_id', $courseId);
                })
                ->orderByDesc('id')
                ->limit(100)
                ->get();
            foreach ($messages as $key => $message) {
                   $messageClass = $message->sender_id == $senderId ? 'sender' : 'receiver';
                $result_array[] =[
                    'message_class' => "chat-message ".$messageClass,
                    'message' => $message->message,
                ];
            }
            return response()->json([
                $result_array
            ]);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
     
    }




      public function studentSendMessage(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            $messages = [
                'receiver_id.required' => __('The Receiver ID Is Required'),
                'sender_id.required' => __('The Sender ID Is Required'),
                'course_id.required' => __('The Course ID Is Required'),
                'message.required' => __('The Message Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'sender_id' => 'required',
                'receiver_id' => 'required',
                'message' => 'required',
                'course_id' => 'required',  
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isCourseExists = Course::where('id',$request->course_id)->first();
            if($isCourseExists == null){
                return response()->json(['message' => 'Given Course ID Does Not Exists'], 302); 
            }

                $chat = new Chat();
                $chat->sender_id = $request->sender_id;
                $chat->receiver_id = $request->receiver_id;
                $chat->course_id = $request->course_id;
                $chat->message = $request->message;
                $chat->is_seen = 0;
                $chat->save();
    
                $messageData = [
                    'receiverId' => $request->receiver_id,
                    'senderId' => $request->sender_id,
                    'courseId' => $request->course_id,
                    'message' => $request->message,
                ];
                event(new ChatEvent($messageData));
                return response()->json(['success' => 'Message Sent Successfully!'],200);
            
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }





    public function wishlistIndex()
    {
        try {
            $result_array = [];
            $user = Auth::user();
            if (!$user) {
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            if ($user->role != 3) { // Student
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
    
            $data['pageTitle'] = 'Wishlist';
    
            // Start:: Check if course or bundle exists, if not, delete it.
            $wishlists = Wishlist::whereUserId($user->id)->get();
            foreach ($wishlists as $wishlist) {
                if ($wishlist->course_id) {
                    $course = Course::find($wishlist->course_id);
                    if (!$course) {
                        $wishlist->delete();
                        continue; // Skip processing this wishlist item
                    }
                } elseif ($wishlist->bundle_id) {
                    $bundle = Bundle::find($wishlist->bundle_id);
                    if (!$bundle) {
                        $wishlist->delete();
                        continue; // Skip processing this wishlist item
                    }
                }
            }
            // End:: Check course, bundle exists or not.
    
            $data['wishlists'] = Wishlist::whereUserId($user->id)->paginate();
    
            foreach ($data['wishlists'] as $wishlist) {
                $special = null;
                $item_reviews = null;
                $item_avg_rating = null;
    
                if ($wishlist->course_id && $wishlist->course) {
                    $course = $wishlist->course;
                    $special = $course->specialPromotionTagCourse->specialPromotionTag->name ?? null;
                    $detail_web_route = route('course-details', $course->slug);
                    $image = getImageFile($course->image_path);
                    $detail_api_route = route('course-details', $course->slug);
                    $item_name = $course->title;
                    $item_reviews = $course->reviews->count();
                    $item_avg_rating = $course->average_rating;
                    $item_type = __('Course');
                    $item_price = (get_currency_placement() == 'after') 
                        ? $course->price . ' ' . get_currency_symbol()
                        : get_currency_symbol() . ' ' . $course->price;
                } elseif ($wishlist->bundle_id && $wishlist->bundle) {
                    $bundle = $wishlist->bundle;
                    $detail_web_route = route('bundle-details', [$bundle->slug]);
                    $image = getImageFile($bundle->image);
                    $detail_api_route = route('bundle-details', $bundle->slug);
                    $item_name = $bundle->name;
                    $item_type = __('Bundle Offer');
                    $item_price = (get_currency_placement() == 'after') 
                        ? $bundle->price . ' ' . get_currency_symbol()
                        : get_currency_symbol() . ' ' . $bundle->price;
                } else {
                    continue; // Skip items that no longer exist
                }
    
                $addToCartRoute = route('student-course-add-to-wishlist', [$wishlist->id]);
                $deleteRoute = route('student-wishlist-delete', [$wishlist->id]);
    
                $result_array[] = [
                    'special' => $special,
                    'wishlist_id' => $wishlist->id,
                    'wishlist_item_image' => $image,
                    'wishlist_item_detail_web_route' => $detail_web_route,
                    'wishlist_item_detail_api_route' => $detail_api_route,
                    'wishlist_item_name' => $item_name,
                    'wishlist_item(course)_average_rating' => $item_avg_rating,
                    'wishlist_item(course)_total_reviews' => $item_reviews,
                    'wishlist_item_type' => $item_type,
                    'wishlist_item_price' => $item_price,
                    'wishlist_item_addToCart_api_route' => $addToCartRoute,
                    'wishlist_item_delete_api_route' => $deleteRoute,
                ];
            }
    
            return response()->json($result_array);
        } catch (\Throwable $th) {
            return response(['status' => 0, 'message' => __($th->getMessage())], 500);
        }
    }
    



    public function wishlistDelete(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            $messages = [
                'wishlist_id.required' => __('The WishList ID Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'wishlist_id' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isExists = Wishlist::where('id',$request->wishlist_id)->first();
            if($isExists == null){
                return response()->json(['message' => 'Given WishList ID Does Not Exists'], 302); 
            }

            $wishlist = Wishlist::findOrFail($request->wishlist_id);
            $wishlist->delete();
            return response()->json(['message' =>  __('Removed From Wishlist!')], 200); 
            // $this->showToastrMessage('success', __('Removed From Wishlist!'));
            // return redirect()->back();
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
     
    }


    public function supportFAQs()
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $data['pageTitle'] = 'Support Ticket';
            $data['metaData'] = staticMeta(9);
            $data['faqQuestions'] = SupportTicketQuestion::all();
            foreach ($data['faqQuestions'] as $key => $faqQuestion) {
                $result_array[] =[
                    'faq_id' => $faqQuestion->id,
                    'faq_question_no' => $key+1,
                    'faq_question' => __($faqQuestion->question),
                    'faq_question_answer' => __($faqQuestion->answer),
                ];
            }
            $result_array[] = [
                'create_support_ticket_web_route' => route('student.support-ticket.create'),
                'create_support_ticket_api_route' => route('support-tickets-index'),
            ];
            return response()->json($result_array);
            // return view('frontend.student.support_ticket.faq', $data);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
     
    }






    public function supportTicketCreate(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            $messages = [
                'name.required' => __('The Name Is Required'),
                'name.string' => __('The Name Must Be String'),
                'name.max' => __('The Name Must Be Atmost 100 Characters Long'),
                'email.required' => __('The Email Is Required'),
                'email.string' => __('The Email Must Be String'),
                'email.email' => __('The Email Must Be Valid'),
                'email.max' => __('The Email Must Be Atmost 255 Characters Long'),
                'subject.required' => __('The Subject Is Required'),
                'subject.string' => __('The Subject Must Be String'),
                'subject.max' => __('The Subject Must Be Atmost 100 Characters Long'),
                'message.required' => __('The Message Is Required'),
                'message.string' => __('The Message Must Be String'),
                'file.mimes' => __('The File Must Be Of Type "jpeg" or "jpg" or "png" or "gif"'),
                'file.max' => __('The File Size Must Be At Most 10 MB'),
            ];
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:255',
                'subject' => 'required|string|max:100',
                'message' => 'required|string',
                'file' => 'mimes:jpeg,jpg,png,gif|max:10000',
            ],$messages);
         
            if($validator->fails()){
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            if($request->department_id){
                $isExists = TicketDepartment::where('id',$request->department_id)->first();
                if($isExists == null){
                    return response()->json(['message' => 'Given Ticket Department ID Does Not Exists'], 302); 
                }
            }
            if($request->related_service_id){
                $isExists = TicketRelatedService::where('id',$request->related_service_id)->first();
                if($isExists == null){
                    return response()->json(['message' => 'Given Ticket Related Service ID Does Not Exists'], 302); 
                }
            }
            if($request->priority_id){
                $isExists = TicketPriority::where('id',$request->priority_id)->first();
                if($isExists == null){
                    return response()->json(['message' => 'Given Ticket Priority ID Does Not Exists'], 302); 
                }
            }
           

            $ticket = new Ticket();
            $ticket->ticket_number = 100;
            $ticket->name = $request->name;
            $ticket->email = $request->email;
            $ticket->subject = $request->subject;
            $ticket->department_id = $request->department_id;
            $ticket->related_service_id = $request->related_service_id;
            $ticket->priority_id = $request->priority_id;
            $ticket->save();
            $ticket->ticket_number = $ticket->id + 100;
            $ticket->save();
    
            $message = new TicketMessages();
            $message->ticket_id = $ticket->id;
            $message->sender_user_id = $user->id;
            $message->message = $request->message;
    
            if ($request->hasFile('file')) {
                $message->file = $this->saveImage('ticket_message', $request->file, 'null', 'null');
            }
    
            $message->save();
    
            return response()->json(['message' => __('Ticket Created Successfully'), 'redirect_api_route'=> route('support-faqs')
            , 'redirect_web_route'=> route('support-ticket-faq')], 200);
            // $this->showToastrMessage('success', __('Ticket Created Successfully'));
            // return redirect()->back();
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }  
    }




    public function supportTicketIndex(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }

            $data['pageTitle'] = 'Support Ticket';
            $data['metaData'] = staticMeta(9);
            $data['tickets'] = Ticket::where('user_id', $user->id)->get();
            $data['departments'] = TicketDepartment::all();
            $data['priorities'] = TicketPriority::all();
            $data['services'] = TicketRelatedService::all();

            $departments = array();
            $related_services = array();
            $priorities = array();
            $view_tickets_tab_data = array();
            foreach ($data['departments'] as $key => $department) {
                $departments[] =[
                    'id' => $department->id,
                    'name' => $department->name,
                ];
            }
            foreach ($data['services'] as $key => $service) {
                $related_services[] =[
                    'id' => $service->id,
                    'name' => $service->name,
                ];
            }
            foreach ($data['priorities'] as $key => $priority) {
                $priorities[] =[
                    'id' => $priority->id,
                    'name' => $priority->name,
                ];
            }
            $create_new_ticket_tab_data =[
                'student_id' => $user->id,
                'student_name' => $user->name,
                'student_email' => $user->email,
                'departments' => $departments,
                'related_services' => $related_services,
                'priorities' => $priorities,
            ];
            foreach ($data['tickets'] as $key => $ticket) {
                if($ticket->status == 1){
                    $ticket_status = __('Open');  
                }
                else if($ticket->status == 1){
                    $ticket_status = __('Closed');  
                }
                $ticket_view_web_route = route('student.support-ticket.show', $ticket->uuid);
                $ticket_view_api_route = route('support-ticket-view');
                $view_tickets_tab_data[] = [
                    'ticket_id' => $ticket->id,
                    'ticket_uuid' => $ticket->uuid,
                    'ticket_number' => $ticket->ticket_number,
                    'ticket_subject' => $ticket->subject,
                    'ticket_last_response' => date('Y-m-d',strtotime($ticket->updated_at)),
                    'ticket_priority' => $ticket->priority?->name,
                    'ticket_status' => $ticket_status,
                    'ticket_view_web_route' => $ticket_view_web_route,
                    'ticket_view_api_route' => $ticket_view_api_route,
                ];
            }
            $result_array[] =[
                'create_new_support_ticket_api_route' => route('support-ticket-create'),
                'create_new_ticket_tab_data' => $create_new_ticket_tab_data,
                'view_tickets_tab_data' => $view_tickets_tab_data,
            ];
            return response()->json($result_array);
            // return view('frontend.student.support_ticket.create-tickets', $data);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 

        }
   
    }



    public function supportTicketView(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            $messages = [
                'ticket_uuid.required' => __('The Ticket UUID Is Required'),
            ];
            $validator = Validator::make($request->all(), [
                'ticket_uuid' => 'required',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isExists = Ticket::where('uuid',$request->ticket_uuid)->first();
            if($isExists == null){
                return response()->json(['message' => 'Given Ticket UUID Does Not Exists'], 302); 
            }

        $data['pageTitle'] = 'Support Ticket Details';
        $ticket = Ticket::where('uuid', $request->ticket_uuid)->firstOrFail();
        $data['ticketMessages'] = TicketMessages::where('ticket_id', $ticket->id)->get();
        $data['last_message'] = TicketMessages::where('ticket_id', $ticket->id)->whereNotNull('reply_admin_user_id')->latest()->first();
        $support_ticket_replies = array();
        foreach($data['ticketMessages'] as $ticketMessage){
            if($ticketMessage->reply_admin_user_id){
                $user_id = $ticketMessage->reply_admin_user_id;
                $user_name = $ticketMessage->replyUser->name;
                $user_type = __('Replier');
            }
            else{
                $user_id = $ticketMessage->sender_user_id;
                $user_name = $ticketMessage->sendUser->name;
                $user_type = __('Sender');
            }
            $support_ticket_replies[] =[
                'id' => $ticketMessage->id,
                'user_id' => $user_id,
                'user_name' => $user_name,
                'type' => $user_type,
                'message' => $ticketMessage->message,
            ];
        }

        if($ticket->status == 1){
            $status = __('Open');
        }
        else{
            $status = __('Closed');
        }
        $ticket_info = [
            'id' => $ticket->id,
            'status' => $status,
            'subject' => "#".$ticket->ticket_number .'-'. $ticket->subject,
            'department' => $ticket->department?->name,
            'priority' => $ticket->priority?->name,
            'service' => $ticket->service?->name,
            'opened' => $ticket->created_at,
            'last_response' => $data['last_message']?$data['last_message']->created_at : null,
        ];
        $result_array[] =[
            'create_support_ticket_web_route' => route('student.support-ticket.create'),
            'create_support_ticket_api_route' => route('support-tickets-index'),
            'support_ticket_send_reply_api_route' => route('support-ticket-send-reply'),
            'support_ticket_replies' => $support_ticket_replies,
            'ticket_info' => $ticket_info,
        ];
        return response()->json($result_array);
        // return view('frontend.student.support_ticket.ticket-details',$data);
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }   
    }




    public function supportTicketSendReply(Request $request)
    {
        try {
            $result_array = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }
            $messages = [
                'ticket_id.required' => __('The Ticket ID Is Required'),
                'message.required' => __('The Message Is Required'),
                'file.mimes' => __('The File Must Be Of Type "jpeg" or "jpg" or "png" or "gif"'),
                'file.max' => __('The File Size Must Be At Most 10 MB'),
            ];
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required',
                'message' => 'required',
                'file' => 'mimes:jpeg,jpg,png,gif|max:10000',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }

            $isExists = Ticket::where('id',$request->ticket_id)->first();
            if($isExists == null){
                return response()->json(['message' => 'Given Ticket ID Does Not Exists'], 302); 
            }

            $message = new TicketMessages();
            $message->ticket_id = $request->ticket_id;
            $message->sender_user_id = $user->id;
            $message->message = $request->message;
            if ($request->hasFile('file')) {
                $message->file = $this->saveImage('ticket_message', $request->file, 'null', 'null');
            }
            $message->save();
    
            return response()->json(['message' => __('Support Ticket Reply Message Sent Successfully'), 'redirect_api_route'=> route('support-tickets-index')
            , 'redirect_web_route'=> route('student.support-ticket.create')], 200); 
            return redirect()->back();
    
        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }  
    }





    public function classSchedule() {

        $data['pageTitle'] = __("Class-Schedule");
        $student_user_id=auth()->id();
    
        $getStudent = Student::where('user_id',$student_user_id)->first();
        $student_id = $getStudent->id;
        $today_date = date('Y-m-d');
        $getAllClasses = ClassDetail::where('student_id',$student_id)->where('class_date' ,'>=',$today_date)->orderBy('class_date','ASC')->paginate(25);

        $data['getAllClasses'] = $getAllClasses;
     
        $data['base_url'] = url('/');
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

        // dd($getGroupClasses);
        return view('frontend.student.settings.class-schedule',$data);

    }





    public function classHistory() {

        $data['pageTitle'] = __("Class-History");
        $student_user_id =  Auth::id();
        // $student_user_id=auth()->id();
    
        $getStudent = Student::where('user_id',$student_user_id)->first();
        $student_id = $getStudent->id;
        $today_date = date('Y-m-d');
        // $getAllClassesHistory = ClassDetail::where('student_id',$student_id)->where('class_date' ,'<=',$today_date)
        // ->whereIn('status',['Completed','Missed'])
        // ->orderBy('id','DESC')
        // ->orderBy('class_date','ASC')
        // ->paginate(10);

        // $getAllClassesHistory = ClassDetail::where('student_id',$student_id)->where('class_date' ,'<',$today_date)
        // ->whereIn('status',['Completed','Missed'])
        // ->orderBy('id','DESC')
        // ->orderBy('class_date','ASC')
        // ->paginate(10);

      
    

        $getAllClassesHistory = ClassDetail::where('student_id',$student_id)->where('class_date' ,'<',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })->orderBy('id','DESC')
          ->orderBy('class_date','ASC')
          ->orderBy('class_time','ASC')->paginate(10);

        $data['getAllClassesHistory'] = $getAllClassesHistory;


        //   $getGroupClassesHistory = ClassDetail::where('student_id',$student_id)->where('class_date' ,'<',$today_date)->wherehas('class' , function($query){
        //     $query->where('type', 'Group');
        // })
        // ->orderBy('id','DESC')
        // ->orderBy('class_id','DESC')
        // ->orderBy('class_date','DESC')
        // ->groupBy('class_id')
        // ->paginate(10);

        $getGroupClassesHistory = ClassDetail::where('student_id',$student_id)->where('class_date' ,'<',$today_date)
        ->whereHas('class', function($query) {
            $query->where('type', 'Group');
        })
        ->orderBy('id','DESC')
        ->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')
        ->groupBy('class_id','class_date')
        ->havingRaw('COUNT(*) >= 1')
        ->paginate(10);

        $data['getAllClassesHistoryGroup'] = $getGroupClassesHistory;

        $data['base_url'] = url('/');
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

  
      
        return view('frontend.student.settings.class-history',$data);

    }




    public function pendingPayments(Request $request) {

        
        $errCode = $request->err_code;
		$errMsg = $request->err_msg;
		if(isset($errCode) && isset($errMsg)){
        
			if ($errCode == '001') { 
                $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                return redirect()->route('student.pending-payments.index');
				// $session->setFlashdata('error', "<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
				// return redirect()->to(base_url('student/payment-history'))->withCookies();
			}
            else if($errCode == '90'){
                $this->showToastrMessage('warning',"<strong>Transaction Failed. Message: " . $errMsg."</strong><br/>");
                return redirect()->route('student.pending-payments.index');
            }
	}
    
        $data['pageTitle'] = __("Pending-Payments");
        $student_user_id=auth()->id();
        $getStudent = Student::where('user_id',$student_user_id)->first();
        $student_id = $getStudent->id;
        $today_date = date('Y-m-d');
        // $getAllClassesHistory = ClassDetail::where('student_id',$student_id)->where('class_date' ,'<=',$today_date)
        // ->orderBy('id','DESC')
        // ->orderBy('class_date','ASC')
        // ->paginate(10);

        $getAllPendingPayments = CartManagement::where('user_id',$student_user_id)->where('created_by_type',2)
        ->orderBy('id','DESC')
        // ->get();
        ->paginate(10);

        $data['getAllPendingPayments'] = $getAllPendingPayments;
     
        $data['base_url'] = url('/');
  
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

        // dd($getGroupClasses);
        return view('frontend.student.settings.pending-payments',$data);

    }


    public function CheckLessonStartTimeStudent(){
        $today_date = date("Y-m-d");
        $user_id = Auth::id();
        $findStudent = Student::where('user_id',$user_id)->first();
        $student_id =  $findStudent->id;
        // $getLessonScheduleOfStudent = ClassDetail::where('student_id',$student_id)->where('class_date','>=',$today_date)->orderBy('class_date','ASC')->get();
        $getLessonScheduleOfStudent = ClassDetail::where('student_id',$student_id)->where('class_date','>=',$today_date)
        ->where('meeting_id','<>',null)->orderBy('class_date','ASC')->get();
      
        if(!$getLessonScheduleOfStudent->isEmpty())
			{
				foreach ($getLessonScheduleOfStudent as $key=> $bokdlsn) 
				    {

                        $DateTime = changetimeByZone($bokdlsn->class_date.' '.date('h:i A',strtotime($bokdlsn->class_time)),$student_id,'student');
                        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$student_id,'student');
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
                                $return['l_id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->class->learning_tool;
                                $return['url'] = null;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getStudentNameFromID($bokdlsn->student_id);
                                $return['user_id'] = $bokdlsn->student_id;
                                $return['user_type'] = 'Student';
                                echo json_encode($return);
  
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->StudentURI != NULL && $bokdlsn->class->learning_tool == 'BBB')
                            {
                                $meetingID = $bokdlsn->meeting_id;
								$lesson_id = $bokdlsn->id;
                                $return['l_id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->class->learning_tool;
                                $return['url'] = $bokdlsn->StudentURI;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getStudentNameFromID($bokdlsn->student_id);
                                $return['user_id'] = $bokdlsn->student_id;
                                $return['user_type'] = 'Student';
                                echo json_encode($return);
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->google_meet_link != NULL && $bokdlsn->class->learning_tool == 'Google_Meet')
                            {
                                $meetingID = $bokdlsn->meeting_id;
								$lesson_id = $bokdlsn->id;
                                $return['l_id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->class->learning_tool;
                                $return['url'] = $bokdlsn->google_meet_link;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getStudentNameFromID($bokdlsn->student_id);
                                $return['user_id'] = $bokdlsn->student_id;
                                $return['user_type'] = 'Student';
                                echo json_encode($return);
                            }
                            else{
                                // echo json_encode(false);
                            }
                        }
                    }
            }     
    }



    public function CheckLessonStartTimeStudentLiveClass(){
        $today_date = date("Y-m-d");
        $user_id = Auth::id();
        $findStudent = Student::where('user_id',$user_id)->first();
        $student_id =  $findStudent->id;
        $getLiveClassesStudent = LiveClassStudentLink::where('student_id',$student_id)->get();
        $liveClassIds = array();
        if(!$getLiveClassesStudent->isEmpty()){
            foreach ($getLiveClassesStudent as $key => $liveClassStudent) {
                $liveClassIds[] = $liveClassStudent->live_class_id;
            }
            $liveClassIds = array_unique($liveClassIds);
        }
        // $getLessonScheduleOfStudent = LiveClass::whereIn('id',$liveClassIds)->where('date','>=',$today_date)->get();
        $getLessonScheduleOfStudent = LiveClass::whereIn('id',$liveClassIds)->where('date','>=',$today_date)
        ->where('meeting_id','<>',null)->get();
      
        if(!$getLessonScheduleOfStudent->isEmpty())
			{
				foreach ($getLessonScheduleOfStudent as $key=> $bokdlsn) 
				    {
                        $findInstructor = Instructor::where('user_id',$bokdlsn->user_id)->first();
                        $instructor_id =  $findInstructor->id;
                        $StudentURI = getStudentURI($bokdlsn->id,$student_id);
                        // $timezone = 'Asia/Karachi';

                        $DateTime = changetimeByZone($bokdlsn->date.' '.date('h:i A',strtotime($bokdlsn->time)),$student_id,'student');
                        $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$student_id,'student');
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
                                $return['l_id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                $return['url'] = null;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getStudentNameFromID($student_id);
                                $return['user_id'] = $student_id;
                                $return['user_type'] = 'Student';
                                echo json_encode($return);
  
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $StudentURI != NULL && $bokdlsn->meeting_host_name == 'bbb')
                            {
                                $meetingID = $bokdlsn->meeting_id;
								$lesson_id = $bokdlsn->id;
                                $return['l_id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                $return['url'] = $StudentURI;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getStudentNameFromID($student_id);
                                $return['user_id'] = $student_id;
                                $return['user_type'] = 'Student';
                                echo json_encode($return);
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->google_meet_link != NULL && $bokdlsn->meeting_host_name == 'google_meet')
                            {
                                $meetingID = $bokdlsn->meeting_id;
								$lesson_id = $bokdlsn->id;
                                $return['l_id'] = $bokdlsn->id;
                                $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                $return['url'] = $StudentURI;
                                $return['meeting_id'] = $bokdlsn->meeting_id;
                                $return['user_name'] = getStudentNameFromID($student_id);
                                $return['user_id'] = $student_id;
                                $return['user_type'] = 'Student';
                                echo json_encode($return);
                            }
                            else{
                                // echo json_encode(false);
                            }
                        }
                    }
            }     
    }




    public function studentCheckLessonStartTimeLiveClass(){
        try {
            $return = array();
            $user = Auth::user();
            if($user){
                $role = $user->role;
                if($role != 3){ // Student
                    return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
                }
            }
            else{
                return response()->json(['message' => 'User Is Not Logged In'], 200);
            }


            $today_date = date("Y-m-d");
            $user_id = Auth::id();
            $findStudent = Student::where('user_id',$user_id)->first();
            $student_id =  $findStudent->id;
            $getLiveClassesStudent = LiveClassStudentLink::where('student_id',$student_id)->get();
            $liveClassIds = array();
            if(!$getLiveClassesStudent->isEmpty()){
                foreach ($getLiveClassesStudent as $key => $liveClassStudent) {
                    $liveClassIds[] = $liveClassStudent->live_class_id;
                }
                $liveClassIds = array_unique($liveClassIds);
            }
            // $getLessonScheduleOfStudent = LiveClass::whereIn('id',$liveClassIds)->where('date','>=',$today_date)->get();
            $getLessonScheduleOfStudent = LiveClass::whereIn('id',$liveClassIds)->where('date','>=',$today_date)
            ->where('meeting_id','<>',null)->get();
          
            if(!$getLessonScheduleOfStudent->isEmpty())
                {
                    foreach ($getLessonScheduleOfStudent as $key=> $bokdlsn) 
                        {
                            $findInstructor = Instructor::where('user_id',$bokdlsn->user_id)->first();
                            $instructor_id =  $findInstructor->id;
                            $StudentURI = getStudentURI($bokdlsn->id,$student_id);
                            // $timezone = 'Asia/Karachi';
                            $DateTime = changetimeByZone($bokdlsn->date.' '.date('h:i A',strtotime($bokdlsn->time)),$student_id,'student');
                            $CurrentDateTime = changetimeByZone(date("Y-m-d h:i A"),$student_id,'student');
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
                                    $return['user_name'] = getStudentNameFromID($student_id);
                                    $return['user_id'] = $student_id;
                                    $return['user_type'] = 'Student';
                                    // echo json_encode($return);
                                    return response()->json($return);
      
                                }
                                else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $StudentURI != NULL && $bokdlsn->meeting_host_name == 'bbb')
                                {
                                    $meetingID = $bokdlsn->meeting_id;
                                    $lesson_id = $bokdlsn->id;
                                    $return['id'] = $bokdlsn->id;
                                    $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                    $return['url'] = $StudentURI;
                                    $return['meeting_id'] = $bokdlsn->meeting_id;
                                    $return['user_name'] = getStudentNameFromID($student_id);
                                    $return['user_id'] = $student_id;
                                    $return['user_type'] = 'Student';
                                    // echo json_encode($return);
                                    return response()->json($return);
                                }
                                else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->google_meet_link != NULL && $bokdlsn->meeting_host_name == 'google_meet')
                                {
                                    $meetingID = $bokdlsn->meeting_id;
                                    $lesson_id = $bokdlsn->id;
                                    $return['id'] = $bokdlsn->id;
                                    $return['learning_tool'] = $bokdlsn->meeting_host_name;
                                    $return['url'] = $StudentURI;
                                    $return['meeting_id'] = $bokdlsn->meeting_id;
                                    $return['user_name'] = getStudentNameFromID($student_id);
                                    $return['user_id'] = $student_id;
                                    $return['user_type'] = 'Student';
                                    // echo json_encode($return);
                                    return response()->json($return);
                                }
                                else{
                                    // return response()->json($return);
                                }
                            }
                        }
                }     
                else{
                    // return response()->json($return);
                }
                return response()->json($return);
        }
        catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
     
      
    }



    public function CreateNewZoomMeetingStudent($meeting_id = null , $user_type = null , $user_name = null , $user_id = null,$lesson_id = null){

    
        $lesson_duration = 30;
 
        $tutor_name = "New Tutor";
        $student_name = "New Student";
        $old_start_time = null;
        $old_end_time = null;
        $data['meeting_id'] = $meeting_id;
        $data['user_type'] = $user_type;
        $data['user_name'] = $user_name;
        $data['user_id'] = $user_id;
        $bklsnid = $lesson_id;
        // $zoomSettings = ZoomSetting::first();
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
     
        $sdk_key = $zoomSettings->sdk_key;
        $sdk_secret = $zoomSettings->sdk_secret;
        $data['sdkKey'] = $sdk_key;
        $data['sdkSecret'] = $sdk_secret;
        $data['baseURL'] = url('/');
        $data['student'] = Student::where('id',$user_id)->first();
    	// $data['student'] = $this->Generic_model->getSpecificRecord('qu_students',array('s_id' =>$_SESSION['qu_user_id']));
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
        $instructor_id = $getLessonDetails->instructor_id;
        $isGroup = 0;
        $isGroupID = null;
        $getLesson = null;
        if($getLessonDetails != null){
                if($getLessonDetails->group_id != null){
                    $isGroup = 1;
                    $isGroupID = $getLessonDetails->group_id;
                }
                else{
                    $isGroup = 0; 
                    $isGroupID = null; 
                }
                $getLesson = Lesson::where('id',$getLessonDetails->class_id)->first();

                $data['meeting_id'] = $getLessonDetails->meeting_id;
                // $data['meeting_password'] = '12345';
                $data['meeting_password'] = $getLessonDetails->zoom_meeting_password;;
              
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
                $instructor_id = $getLessonDetails->instructor_id;
			    $tutor_name =getTutorNameFromID($instructor_id);

                $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->class_id.')';
                
        }
    

      
         //////////////////NEW CODE//////////////////////
         if($getLessonDetails->zoom_meeting_name != null){
            $data['session_name'] = $getLessonDetails->zoom_meeting_name;
        }
        elseif($getLessonDetails->class?->zoom_meeting_name != null){
            $data['session_name'] = $getLessonDetails->class?->zoom_meeting_name;
        }
        else{
            if($isGroup == 1){
                $getCheckDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$isGroupID)->get();
                if(!$getCheckDetails->isEmpty()){
                    $sessionName =Uuid::uuid4()->toString();
                    foreach ($getCheckDetails as $key => $value2) {
                        $data['session_name'] = $sessionName;
                        $value2->zoom_meeting_name =  $data['session_name'];
                        $value2->update();
                    }
                }
            }
            else{
                $data['session_name'] = Uuid::uuid4()->toString();
                $getLessonDetails->zoom_meeting_name =  $data['session_name'];
                $getLessonDetails->update();
            }      
            // $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
        }

        if($getLesson){
            $getLesson->zoom_meeting_name = $data['session_name'];
            $getLesson->update();
        }

        //////////////////NEW CODE//////////////////////
        if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
            $given_start_time = $old_start_time;
            $given_end_time = $old_end_time;
        }
        else{
            $data_record_lesson = ClassDetail::where('meeting_id',$meeting_id)->first();
           $student_id = $data_record_lesson->student_id;
            $given_end_time = $data_record_lesson->end_time_zoom;
        
            $updated_current_time = getCurrentServerTimeByZone($student_id,'student');
            $given_start_time = strtotime($updated_current_time);

        }
    
        if($given_start_time == null){
            $given_start_time = 0;
        }
        if($given_end_time == null){
            $given_end_time = 0;
        }
     
        // $data['lesson_duration'] = $lesson_duration;
        $data['lesson_id'] = $bklsnid;
        // $data['student_name'] = $student_name;
        $data['tutor_name'] = $tutor_name;
        $data['start_time'] = $given_start_time;
        $data['end_time'] = $given_end_time;
        $data['isMeetingPage'] = 'Yes';
        echo view('frontend.student.settings.meeting-page-zoom-ui',$data);
    }


    public function UpdateMeetingSignature(Request $request, $meeting_id = null){
        $l_id = null;
        $meetingSignature = $request->meetingSignature;
      
        $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
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



    public function GetCurrentTimeZoomStudent($meeting_id = null){
      
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


    public function CheckModeratorLeftZoomStudent($meeting_id = null){
        $is_moderator_left = null;
        $is_left = 'No';
        $classes = ClassDetail::where('meeting_id',$meeting_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $is_moderator_left = $value->is_moderator_left;
            }
        }
        if($is_moderator_left == "1" || $is_moderator_left == '1' || $is_moderator_left === "1" || $is_moderator_left === '1' || $is_moderator_left == 1 || $is_moderator_left === 1){
            $is_left = 'Yes';
        }
        else if($is_moderator_left == "0"){
            $is_left = 'NO';
        }
        return $is_left;
    }



    public function UpdateUrlZoomStudent($meeting_id = null , $currentStartTime = null){
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
            if($getLessonDetails->group_id != null){
                $classes = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$getLessonDetails->group_id)->get();
                if(!$classes->isEmpty()){
                    foreach ($classes as $key => $value) {
                        $value->start_time_zoom = $currentStartTime;
                        $value->update();
                    }
                }
            }
            else{
                $res = ClassDetail::where('meeting_id',$meeting_id)->first();
                $res->start_time_zoom = $currentStartTime;
                $res->update();
            }   
        }

    }


































    public function CreateNewZoomMeetingStudentLiveClass($meeting_id = null , $user_type = null , $user_name = null , $user_id = null,$lesson_id = null){

    
        $lesson_duration = 30;
 
        $tutor_name = "New Tutor";
        $student_name = "New Student";
        $old_start_time = null;
        $old_end_time = null;
        $data['meeting_id'] = $meeting_id;
        $data['user_type'] = $user_type;
        $data['user_name'] = $user_name;
        $data['user_id'] = $user_id;
        $bklsnid = $lesson_id;
        // $zoomSettings = ZoomSetting::first();
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
     
        $sdk_key = $zoomSettings->sdk_key;
        $sdk_secret = $zoomSettings->sdk_secret;
        $data['sdkKey'] = $sdk_key;
        $data['sdkSecret'] = $sdk_secret;
        $data['baseURL'] = url('/');

     
        
        $data['student'] = Student::where('id',$user_id)->first();
    	// $data['student'] = $this->Generic_model->getSpecificRecord('qu_students',array('s_id' =>$_SESSION['qu_user_id']));
        $getLessonDetails = LiveClass::where('meeting_id',$meeting_id)->first();

        $data['course_slug'] = $getLessonDetails->course?->slug;
        $meeting_end_url = route('student.my-course.show',['slug'=>$data['course_slug']]);
        $data['meeting_end_url'] = $meeting_end_url;

        $instructor_user_id = $getLessonDetails->user_id;
        $findInstructor = Instructor::where('user_id',$instructor_user_id)->first();
        $instructor_id = $findInstructor->id;
        if($getLessonDetails != null){
                $data['meeting_id'] = $getLessonDetails->meeting_id;
                // $data['meeting_password'] = '12345';
                $data['meeting_password'] = $getLessonDetails->meeting_id;
              
                if($getLessonDetails->zoom_meeting_signature != null){
                    $data['meeting_signature'] = $getLessonDetails->zoom_meeting_signature;
                }
                else{
                    $data['meeting_signature'] = null;
                }


                
                //////////////////NEW CODE//////////////////////
                if($getLessonDetails->zoom_meeting_name != null){
                    $data['session_name'] = $getLessonDetails->zoom_meeting_name;
                }
                else{
                    $data['session_name'] = Uuid::uuid4()->toString();
                    $getLessonDetails->zoom_meeting_name =  $data['session_name'];
                    $getLessonDetails->update();
                    // $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
                }
                //////////////////NEW CODE//////////////////////

                $lesson_duration =$getLessonDetails->duration;
                $old_start_time = $getLessonDetails->start_time_zoom;
                $old_end_time = $getLessonDetails->end_time_zoom;
                $lesson_id = $getLessonDetails->id;
            
			    $tutor_name =getTutorNameFromID($instructor_id);

                $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->id.')';
        }
    
        if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
            $given_start_time = $old_start_time;
            $given_end_time = $old_end_time;
        }
        else{
            $data_record_lesson = LiveClass::where('meeting_id',$meeting_id)->first();
           $student_id = $data['student']->id;
            $given_end_time = $data_record_lesson->end_time_zoom;
        
            $updated_current_time = getCurrentServerTimeByZone($student_id,'student');
            $given_start_time = strtotime($updated_current_time);

        }
    
        if($given_start_time == null){
            $given_start_time = 0;
        }
        if($given_end_time == null){
            $given_end_time = 0;
        }
     
        $data['lesson_id'] = $bklsnid;
        // $data['student_name'] = $student_name;
        $data['tutor_name'] = $tutor_name;
        $data['start_time'] = $given_start_time;
        $data['end_time'] = $given_end_time;
        $data['isMeetingPage'] = 'Yes';
        echo view('frontend.student.settings.meeting-page-zoom-ui-liveclass',$data);
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

    public function GetCurrentTimeZoomStudentLiveClass($meeting_id = null){
      
        $data_record_timezone = LiveClass::where('meeting_id',$meeting_id)->first();
        $instructor_user_id =  $data_record_timezone->user_id;
        $findInstructor = Instructor::where('user_id',$instructor_user_id)->first();
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

    public function CheckModeratorLeftZoomStudentLiveClass($meeting_id = null){
        $is_moderator_left = null;
        $is_left = 'No';
        $classes = LiveClass::where('meeting_id',$meeting_id)->get();
        if(!$classes->isEmpty()){
            foreach ($classes as $key => $value) {
                $is_moderator_left = $value->is_moderator_left;
            }
        }
        if($is_moderator_left == "1" || $is_moderator_left == '1' || $is_moderator_left === "1" || $is_moderator_left === '1' || $is_moderator_left == 1 || $is_moderator_left === 1){
            $is_left = 'Yes';
        }
        else if($is_moderator_left == "0"){
            $is_left = 'NO';
        }
        return $is_left;
    }

    public function UpdateUrlZoomStudentLiveClass($meeting_id = null , $currentStartTime = null){
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

    }



























    public function profile()
    {
        $data['pageTitle'] = __("Profile");
        $data['user'] = auth::user();
        $data['student'] = $data['user']->student;
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('frontend.student.settings.profile', $data);
    }

    public function address()
    {
        $data['pageTitle'] = __("Address");
        $data['user'] = auth::user();
        $data['student'] = $data['user']->student;
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();
        if (old('country_id'))
        {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }
        if (old('state_id'))
        {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('frontend.student.settings.address', $data);
    }

    public function address_update(Request $request, $uuid)
    {
        $request->validate([
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'postal_code' => 'required',
            'address' => 'required',
            'lat' => 'required',
            'long' => 'required',
        ]);

        $student = $this->studentModel->getRecordByUuid($uuid);
        $user = User::find($student->user_id);
        $user->lat = $request->lat;
        $user->long = $request->long;
        $user->save();

        $data = [
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
        ];

        $this->studentModel->updateByUuid($data, $uuid);
        $this->showToastrMessage('success', __('Successfully Updated!'));
        return redirect()->back();
    }

    public function changePassword()
    {
        $data['pageTitle'] = "Change Password";
        $data['user'] = auth::user();
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();


        return view('frontend.student.settings.change-password', $data);
    }

    public function changePasswordUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::find(Auth::id());

        if (Hash::check($request->old_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            $this->showToastrMessage('success', __('Password changed successfully'));
            return redirect()->back();
        } else {
            $this->showToastrMessage('error', __('Your old password does not match.'));
            return redirect()->back();
        }
    }


    public function becomeAnInstructor()
    {
        if (auth()->user()->role == USER_ROLE_INSTRUCTOR)
        {
            $this->showToastrMessage('error', __('You are already an instructor!'));
            return redirect()->back();
        }
        elseif(auth()->user()->role == USER_ROLE_ORGANIZATION){
            $this->showToastrMessage('error', __('You are already an organization!'));
            return redirect()->back();
        }

        $data['pageTitle'] = 'Become an Instructor';
        $data['instructorFeatures'] = InstructorFeature::take(3)->get();
        $data['instructorProcedures'] = InstructorProcedure::all();
        $data['total_students'] = Student::count();
        $data['total_enrollments'] = Enrollment::count();
        $data['total_instructors'] = Instructor::count();

        return view('frontend.student.settings.become-an-instructor', $data);
    }

    public function saveInstructorInfo(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'account_type' => 'required',
            'last_name' => 'required',
            'professional_title' => 'required',
            'about_me' => 'required',
            'cv_file' => 'required|max:5000|mimes:pdf',
        ],
        [
            'required'  => 'The :attribute field is required.',
        ]);

        $authUser = Auth::user();

        if($request->account_type == USER_ROLE_ORGANIZATION){
            $object = Organization::where('user_id', $authUser->id)->get();
        }
        else{
            $object = Instructor::where('user_id', $authUser->id)->get();
        }

        if ($object->count() > 0){
            $this->showToastrMessage('success', __('Request already send'));
            return redirect(route('student.dashboard'));
        } else {

            if($request->account_type == USER_ROLE_ORGANIZATION){
                $slugCount = Organization::where('slug', getSlug($authUser->name))->count();
            }
            else{
                $slugCount = Instructor::where('slug', getSlug($authUser->name))->count();
            }

            if ($slugCount)
            {
                $slug = getSlug($authUser->name) . '-'. rand(100000, 999999);
            } else {
                $slug = getSlug($authUser->name);
            }

            $cv_file_data = $this->uploadFileWithDetails('user', $request->cv_file);
            if (!$cv_file_data['is_uploaded']) {
                $this->showToastrMessage('error', __('Something went wrong! Failed to upload file'));
                return redirect()->back();
            }
            $data = [
                'user_id' => Auth::user()->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'professional_title' => $request->professional_title,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'about_me' => $request->about_me,
                'slug' => $slug,
                'cv_file' => $cv_file_data['path'],
                'cv_filename' => $cv_file_data['original_filename'],
            ];

            $emailSend = new EmailSendService();
            if($request->account_type == USER_ROLE_ORGANIZATION){
                $this->organizationModel->create($data);
                $text = __("New instructor request");
                $target_url = route('instructor.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url);
            }
            else{
                $this->instructorModel->create($data);
                $text = __("New organization request");
                $target_url = route('organizations.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url, false);
            }

            $this->send($text, 1, $target_url, null);

            $this->showToastrMessage('success', __('Request successfully send'));
            return redirect(route('student.dashboard'));
        }


    }


    public function saveProfile(ProfileRequest $request, $uuid)
    {
        $student = $this->studentModel->getRecordByUuid($uuid);

        $user = User::find($student->user_id);
        if (User::where('id', '!=', $user->id)->where('email', $request->email)->count() > 0)
        {
            $this->showToastrMessage('warning', __('Email already exist'));
            return redirect()->back();
        } else {
            $user->email = $request->email;
        }
        if ($request->image)
        {
            $this->deleteFile($user->image); // delete file from server

            $image = $this->saveImage('user', $request->image, null, 'null'); // new file upload into server

        } else {
            $image = $user->image;
        }

        $user->name = $request->first_name . ' ' . $request->last_name;
        $user->image = $image;
        $user->image = $image;
        $user->meta_title = $request->meta_title;
        $user->meta_description = $request->meta_description;
        $user->meta_keywords = $request->meta_keywords;
        if($request->hasFile('og_image')){
            $user->og_image = $this->saveImage('meta', $request->og_image, null, null);
        }

        $user->save();

        $student_data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'about_me' => $request->about_me,
            'gender' => $request->gender
        ];

        $student = $this->studentModel->updateByUuid($student_data, $uuid); // update category

        $this->showToastrMessage('success', __('Successfully save'));
        return redirect()->back();
    }


    public function getStateByCountry($country_id)
    {
        return State::where('country_id', $country_id)->orderBy('name', 'asc')->get()->toJson();
    }

    public function getCityByState($state_id)
    {
        return City::where('state_id', $state_id)->orderBy('name', 'asc')->get()->toJson();
    }



    public function dashboardApi()
    {
        $user = Auth::user();
        $role = $user->role;
        if($role != 3){ // Student
            return response()->json(['message' => 'You Do Not Have The Permission To Access This Route '], 500);
        }
        $view = view('frontend.student.dashboard_components.home')->render();
        dd($view);
        // return redirect(route('student.class-schedule.index'));
        // return redirect(route('student.profile'));
        // return redirect(route('student.dashboard'));
        return view('frontend.student.dashboard_components.home');
    }

}
