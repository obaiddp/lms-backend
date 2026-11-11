<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Services\EmailSendService;
use App\Models\Answer;
use App\Models\Assignment;
use App\Models\AssignmentSubmit;
use App\Models\BookingHistory;
use App\Models\Certificate;
use App\Models\Certificate_by_instructor;
use App\Models\Course;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\Course_lecture;
use App\Models\CourseResource;
use App\Models\Course_lecture_views;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\LiveClass;
use App\Models\NoticeBoard;
use App\Models\Order;
use App\Models\Student;
use App\Models\Order_item;
use App\Models\Question;
use App\Models\Question_option;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Student_certificate;
use App\Models\Take_exam;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use JoisarJignesh\Bigbluebutton\Facades\Bigbluebutton;
use Peopleaps\Scorm\Model\ScormModel;
use Stripe\OrderItem;
use App\Models\Notification;

class MyCourseController extends Controller
{
    use ImageSaveTrait, General, SendNotification;

    public function myLearningCourseList(Request $request)
    {
        $data['action_type'] = null;
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();
        $data['pageTitle'] = 'My Courses';
        
        // $data['enrollments'] = Enrollment::where('enrollments.user_id', auth()->id())->select('enrollments.*', 'order_items.unit_price')->join('orders', 'orders.id', '=', 'enrollments.order_id')->join('order_items', 'order_items.order_id', '=', 'orders.id')->where('enrollments.status', ACCESS_PERIOD_ACTIVE)->groupBy('enrollments.id');
        $data['enrollments'] = Enrollment::where('enrollments.user_id', auth()->id())->select('enrollments.*', 'order_items.unit_price')->join('orders', 'orders.id', '=', 'enrollments.order_id')->join('order_items', 'order_items.order_id', '=', 'orders.id')->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->where('enrollments.course_type',1)
        ->orderBy('enrollments.course_id', 'ASC')
        ->orderBy('enrollments.id', 'DESC') // Ensure latest record is selected
        // ->unique('enrollments.course_id');
        // ->groupBy('enrollments.id');
        ->groupBy('enrollments.course_id');

    

        $data['enrollmentsLive'] = Enrollment::where('enrollments.user_id', auth()->id())->select('enrollments.*')
        ->whereHas('course', function ($query) {
            $query->where('id','<>',null);
        })
        ->where('enrollments.status', ACCESS_PERIOD_ACTIVE)
        ->where('enrollments.course_type',2)
        ->groupBy('enrollments.id');

      
   
     
        if ($request->ajax()) {
            $sortByID = $request->sortByID; // 1=newest, 2=Oldest
            if ($sortByID) {
                if ($sortByID == 1) {

                    $data['enrollments'] = $data['enrollments']->latest()->paginate();
                    $data['enrollmentsLive'] = $data['enrollmentsLive']->latest()->paginate();
                }

                if ($sortByID == 2) {
                    $data['enrollments'] = $data['enrollments']->paginate();
                    $data['enrollmentsLive'] = $data['enrollmentsLive']->paginate();
                }
            }

            return view('frontend.student.course.render-courses-list', $data);
        }

        $data['enrollments'] = $data['enrollments']->latest()->paginate();
        $data['enrollmentsLive'] = $data['enrollmentsLive']->latest()->paginate();

        return view('frontend.student.course.courses-list', $data);
    }

    public function organizationCourses(Request $request)
    {
        $data['pageTitle'] = 'Organizational Courses';

        $data['courses'] = Course::query()
        ->where('user_id', auth()->user()->student->organization->user_id)
        ->whereNotNull('organization_id')
        ->paginate(10);
        return view('frontend.student.course.organizational-courses-list', $data);
    }

    public function myConsultationList(Request $request)
    {
        $data['pageTitle'] = 'My Consultation Courses';

        $allUserOrder = Order::where('user_id', auth()->id());
        $paidOrderIds = $allUserOrder->where('payment_status', 'paid')->pluck('id')->toArray();

        $allUserOrder = Order::where('user_id', auth()->id());
        $freeOrderIds = $allUserOrder->where('payment_status', 'free')->pluck('id')->toArray();

        $orderIds = array_merge($paidOrderIds, $freeOrderIds);

        $data['orderItems'] = Order_item::whereIn('order_id', $orderIds);

        if ($request->ajax()) {
            $sortByID = $request->sortByID; // 1=newest, 2=Oldest
            if ($sortByID) {
                if ($sortByID == 1) {
                    $bookingOrderIds = BookingHistory::whereIn('order_id', $orderIds)->where('status', 1)->pluck('order_item_id')->toArray();
                    $data['orderItems'] =  Order_item::whereIn('id', $bookingOrderIds)->paginate();
                } elseif ($sortByID == 2) {
                    $bookingOrderIds = BookingHistory::whereIn('order_id', $orderIds)->where('status', 2)->pluck('order_item_id')->toArray();

                    $data['orderItems'] =  Order_item::whereIn('id', $bookingOrderIds)->paginate();
                } elseif ($sortByID == 3) {
                    $bookingOrderIds = BookingHistory::whereIn('order_id', $orderIds)->where('status', 3)->pluck('order_item_id')->toArray();
                    $data['orderItems'] =  Order_item::whereIn('id', $bookingOrderIds)->paginate();
                } else {
                    $bookingOrderIds = BookingHistory::whereIn('order_id', $orderIds)->where('status', 0)->pluck('order_item_id')->toArray();
                    $data['orderItems'] =  Order_item::whereIn('id', $bookingOrderIds)->paginate();
                }
            }

            return view('frontend.student.consultation.render-consultation-list', $data);
        }

        $data['orderItems'] = $data['orderItems']->latest()->paginate();

        return view('frontend.student.consultation.consultation-list', $data);
    }

    public function downloadInvoice($item_id)
    {
        $item = Order_item::find($item_id);

        $invoice_name = 'invoice' . '.pdf';
        // make sure email invoice is checked.
//        $customPaper = array(0, 0, 612, 792);
//        $pdf = PDF::loadView('frontend.student.course.invoice', ['item' => $item])->setPaper($customPaper, 'portrait');
        //$pdf->save(public_path() . '/uploads/receipt/' . $invoice_name);
        // return $pdf->stream($invoice_name);
            return view('frontend.student.course.invoice', ['item' => $item]);

//        return $pdf->download($invoice_name);
    }

    public function myCourseCompleteDuration(Request $request, $course_id)
    {
        $enrollment = Enrollment::where('course_id', $course_id)->where('user_id', auth()->id())->where('end_date', '>=', now())->first();
        $scorm = ScormModel::where('course_id', $course_id)->select('duration_in_second')->first();
        if ($enrollment && $enrollment->completed_time < $scorm->duration_in_second) {
            $enrollment->completed_time += $request->duration;
            $enrollment->completed_time = ($enrollment->completed_time > $scorm->duration_in_second) ? $scorm->duration_in_second : $enrollment->completed_time;
            $enrollment->save();

        }
        $data = [
            'success' => 'success'
        ];

        /** === make pdf certificate ===== */
        $data['data'] = $this->makePdfCertificate($course_id, $enrollment->id);
        /** ------- end save certificate ----------- */

        return response()->json($data);
    }

    public function getResources(Request $request){
        $courseID = $request->course_id;
        $categoryID = $request->category_id;
        $result_array = array();
        $resources = CourseResource::where('course_id',$courseID)
        ->whereHas('resource', function ($query) use ($categoryID) {
            $query->where('category_id', $categoryID);
        })->get();
        if(!$resources->isEmpty()){
            foreach ($resources as $key => $value) {
                $result_array[] = [
					'resource_id'=>$value->resource->id,
					'resource_name'=>$value->resource->name,
					'resource_ext'=>$value->resource->file_extension,
					'resource_path'=>$value->resource->file_name,
                    'resource_type'=>$value->resource->type,
				];
            }
        }
        echo json_encode($result_array);
        // dd($result_array);
    }



    public function viewFullResource($file_ext , $file_name, $lecture_id = null){
      
        if($lecture_id){
            $lectureData = Course_lecture::where('id',$lecture_id)->first();
            if($lectureData){
                $data['courseName'] = $lectureData->course?->title;
                $data['courseSlug'] = $lectureData->course?->slug;
                $data['lectureTitle'] = $lectureData->title;
            }
        }
        $data['lectureID'] = $lecture_id;
        $base_url = env('APP_URL');
        if($base_url == "http://localhost:8000"){
            $base_url = "http://localhost:8000/";
        }
        $data['baseURL'] = $base_url;
        $data['file_ext']=$file_ext;
		$data['file_name']=$file_name;
        // dd($data['baseURL']);
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();
        
        return view('frontend.student.course.viewfull', $data);
        // return view('instructor.course.resources.viewfull', $data);
        // frontend.student.course.course-details
      
    }


    public function viewFullResourceOther($resource_id, $lecture_id = null){
        // dd($lecture_id);
        if($lecture_id){
            $lectureData = Course_lecture::where('id',$lecture_id)->first();
            if($lectureData){
                $data['courseName'] = $lectureData->course?->title;
                $data['courseSlug'] = $lectureData->course?->slug;
                $data['lectureTitle'] = $lectureData->title;
            }
        }
        $data['lectureID'] = $lecture_id;
        $base_url = env('APP_URL');
        if($base_url == "http://localhost:8000"){
            $base_url = "http://localhost:8000/";
        }
        $data['baseURL'] = $base_url;
        $getResource = Resource::where('id',$resource_id)->first();
        $data['resourceType']=$getResource->type;
        if($getResource->type == 'Youtube'){
            $data['youtube_url_path']=$getResource->youtube_url_path;
            $data['youtube_video_duration']=$getResource->youtube_video_duration;
            $data['youtube_video_duration_second']=$getResource->youtube_video_duration_second;
        }
        $data['text_description']=$getResource->text;
    //  dd($data);

    
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();
        return view('frontend.student.course.viewfullother', $data);
      
    }



    public function testFunction(){
        
    }

    public function myCourseShow(Request $request, $slug, $action_type = null, $quiz_uuid = null, $answer_id = null)
    {
     
    
        // dd($request->all());
        $data['pageTitle'] = "Course Details";
        $data['course'] = Course::whereSlug($slug)->firstOrfail();
      
        //Current User Given Course Total Lecture Views
        $data['course_lecture_views'] = Course_lecture_views::where('course_id', $data['course']->id)->where('user_id', auth()->id())->get();
     
        //Current User Given Course Active Period Enrollment Info
        $data['enrollment'] = Enrollment::where(['course_id' => $data['course']->id, 'user_id' => auth()->id(), 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
     
   
        // End:: Checking enrolled or not
        // Current Given Course Total Student Enrollments Count
        $data['total_enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->where('end_date', '>=', now())->count();

        // 4 Out of Total Enrollments Data For Current Given Course
        $data['enrolled_students'] = Enrollment::where('course_id', $data['course']->id)->take(4)->get();

        //Start:: Assignment
        // Current Given Course Total Number Of Assignments Data
        $data['assignments'] = Assignment::where('course_id', $data['course']->id)->get();

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
                        $resource_array[] = [
                            'resource_id'=>$value2->resource->id,
                            'resource_name'=>$value2->resource->name,
                            'resource_ext'=>$value2->resource->file_extension,
                            'resource_path'=>$value2->resource->file_name,
                            'resource_type'=>$value2->resource->type,
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
   
        //End:: Assignment
        //Start:: Notice
        $data['notices'] = NoticeBoard::whereCourseId($data['course']->id)->latest()->get();
        //End:: Notice
        //Start:: Live Class
        $now = now();

        $data['upcoming_live_classes'] = LiveClass::whereCourseId($data['course']->id)
            ->where(function($q){    // Current Today Pending Live Classes
                $q->whereDate('date', now());
                $q->whereTime('time', '>', now());
            })
            ->orWhere(function($q){     // Upcomming Scheduled Live Classes
                $q->whereDate('date', '>', now());
            })
            ->latest()->get();

        $data['current_live_classes'] = LiveClass::whereCourseId($data['course']->id)
            ->where(function($q){         //  Current Today Live Active Classes
                $q->whereDate('date', now());
                $q->whereTime('time', '<=', now());
                $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
            })
            ->latest()->get();

        $data['past_live_classes'] = LiveClass::whereCourseId($data['course']->id)
        ->where(function($q){
                $q->whereDate('date', now());
                $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
            })
            ->orWhere(function($q){
                $q->whereDate('date', '<', now());
            })
            ->latest()->get();

        //End:: Live Class

        //Start:: Review
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

        //End:: Review
        $data['discussions'] = Discussion::whereCourseId($data['course']->id)->whereNull('parent_id')->active()->get();

    
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
                    $isPassed = $takeExam->passed;
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
                    $checkAttempt=check_take_attempt_exam($data['exam']->id);
                    if($checkAttempt == 'yes'){
                        return redirect(route('student.my-course.show', [$data['course']->slug, 'quiz-result', $data['exam']->uuid]));
                    }
                    else{}
                }
                else{
                    $checkAttempt=check_take_attempt_exam($data['exam']->id);
                    if($checkAttempt == 'yes'){
                        return redirect(route('student.my-course.show', [$data['course']->slug, 'quiz-result', $data['exam']->uuid]));
                    }
                    else{}
                }

            }
            // if ($action_type == 'restart-quiz') {
                
            //     if (Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
            //     ->where('passed',1)->count() == 0) {
            //         $take_exam = new Take_exam();
            //         $take_exam->exam_id = $data['exam']->id;
            //         $take_exam->save();
            //     }

            //     $data['take_exam'] = Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();

            //     // $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
            //     $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
            //     ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();
            //     $data['question'] = Question::whereExamId($data['exam']->id)->whereNotIn('id', $question_ids)->first();
            //     // $data['number_of_answer'] = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count();
            //     $data['number_of_answer'] = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
            //     ->where('take_exam_id',$data['take_exam']->id)->count();
            
            //     $now = Carbon::now();
            //     $expend_second = $now->diffInSeconds($data['take_exam']->created_at);

            //     $action_type = 'start-quiz';
            //     if (Carbon::parse($data['exam']->duration * 60)->subSecond($expend_second)->format('H:i:s') > Carbon::parse($data['exam']->duration * 60)->format('H:i:s')) {
            //         return redirect(route('student.my-course.show', [$data['course']->slug, 'quiz-result', $data['exam']->uuid]));
            //     }
                
            // }
            if ($action_type == 'leaderboard') {
             
                $data['top5_take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->take(5)->get();
                $data['take_exams'] = Take_exam::whereExamId($data['exam']->id)->orderBy('number_of_correct_answer', 'DESC')->skip(5)->take(500)->get();
            
            }

            //NEW CODE
          
          
        }
     
        if (!is_null($answer_id)) {
            $data['answer'] = Answer::find($answer_id);
        }
        
        $data['action_type'] = $action_type;
        $data['quiz_uuid'] = $quiz_uuid;
        $data['answer_id'] = $answer_id;

        /** ------- save certificate ----------- */

    
        $isFirst = count($data['course_lecture_views']) ? false : true;
        $data['isFirst'] = $isFirst;
     
        $Base_Route = route('student.my-course.show', [$data['course']->slug]);
        $data['BaseRoute'] = $Base_Route;
      
     
  
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
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                            $course_lecture_views->is_viewed_recently = 1;
                            $course_lecture_views->save();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$course_lecture_views->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                         
                        }
                        else{
                            $lectureView = Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->first();
                            $lectureView->is_viewed_recently = 1;
                            $lectureView->update();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$lectureView->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                    }
                    $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                    $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    $data['video_src'] =  $lecture->resource?->file_name;
                } 
                elseif ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                    $lecture = Course_lecture::find($lecture->id);
                    if ($lecture) {
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                            $course_lecture_views->is_viewed_recently = 1;
                            $course_lecture_views->save();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$course_lecture_views->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }        
                        }
                        else{
                            $lectureView = Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->first();
                            $lectureView->is_viewed_recently = 1;
                            $lectureView->update();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$lectureView->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
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
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                            $course_lecture_views->is_viewed_recently = 1;
                            $course_lecture_views->save();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$course_lecture_views->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                        else{
                            $lectureView = Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->first();
                            $lectureView->is_viewed_recently = 1;
                            $lectureView->update();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$lectureView->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                    }

                    $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                    $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                    $data['text_src'] = $lecture->resource?->text;
                } 
                elseif ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                    $lecture = Course_lecture::find($lecture->id);
                    if ($lecture) {
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                            $course_lecture_views->is_viewed_recently = 1;
                            $course_lecture_views->save();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$course_lecture_views->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }        
                        }
                        else{
                            $lectureView = Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->first();
                            $lectureView->is_viewed_recently = 1;
                            $lectureView->update();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$lectureView->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
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
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                            $course_lecture_views->is_viewed_recently = 1;
                            $course_lecture_views->save();
                         
                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$course_lecture_views->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                        else{
                            $lectureView = Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->first();
                            $lectureView->is_viewed_recently = 1;
                            $lectureView->update();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$lectureView->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
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
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                            $course_lecture_views->is_viewed_recently = 1;
                            $course_lecture_views->save();
                         
                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$course_lecture_views->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                        else{
                            $lectureView = Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->first();
                            $lectureView->is_viewed_recently = 1;
                            $lectureView->update();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$lectureView->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                    }

                    $data['slide_document_src'] = $lecture->resource?->file_name;
                    $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                    $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                } 
                elseif ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                    $lecture = Course_lecture::find($lecture->id);
                    if ($lecture) {
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                            $course_lecture_views->is_viewed_recently = 1;
                            $course_lecture_views->save();
                            
                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$course_lecture_views->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                        else{
                            $lectureView = Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->first();
                            $lectureView->is_viewed_recently = 1;
                            $lectureView->update();

                            $otherLectureViews = Course_lecture_views::where('user_id', auth()->id())
                            ->where('id','<>',$lectureView->id)
                            ->where('course_id', $lecture->course_id)
                            ->get();
                            if(!$otherLectureViews->isEmpty()){
                                foreach ($otherLectureViews as $key => $otherView) {
                                    $otherView->is_viewed_recently = 0;
                                    $otherView->update();
                                }
                            }
                        }
                    }
                    $data['audio_src'] = $lecture->resource?->file_name;
                    $certificateData = $this->makePdfCertificate($lecture->course_id, $data['enrollment']->id);
                    $data['certificateData'] = ($certificateData == NULL) ? [] : $certificateData;
                }
                elseif($lecture->quiz){
                 
                    $data['exam'] = Exam::whereUuid($lecture->quiz->uuid)->first();

                    if (Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count() > 0) {
                        $take_exam = Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                        $isPassed = $take_exam->passed;
                        if(!$isPassed){
                            $totalQuestions = Question::whereExamId($data['exam']->id)->count();
                            $attemptedAnswers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                            ->where('take_exam_id',$take_exam->id)->count();
                            if($attemptedAnswers == $totalQuestions){
                                $take_exam = new Take_exam();
                                $take_exam->lecture_id = $lecture->id;
                                $take_exam->exam_id = $data['exam']->id;
                                $take_exam->save();
                            }
                        }



                        $take_exam = Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->orderBy('id', 'DESC')->first();
                        $take_exam->lecture_id = $lecture->id;
                       
                    
                        $answered_question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->pluck('question_id')->toArray();
                        $total_questions = Question::whereExamId($data['exam']->id)->count();
                        // $number_of_attempted_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count();
                        $number_of_attempted_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                        ->where('take_exam_id',$take_exam->id)->count();
    
                        if($number_of_attempted_answers == $total_questions){  // USER Attempted All Question Before EndTime
                            if ($lecture->type == 'quiz' || $lecture->type == 'Quiz') {
                                //NEW CODE
                                if($lecture->quiz){
                                    $quizPassedPercentage = $lecture->quiz->passing_percentage;
                                    $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                                    ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                    $cal_percentage = ($correct_answers/$total_questions)*100;
                                    $cal_percentage = (int)$cal_percentage;
                                    if($cal_percentage >= $quizPassedPercentage){
                                        $lecture = Course_lecture::find($lecture->id);
                                        if ($lecture) {
                                            if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                                            $take_exam->lecture_id = $lecture->id;
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 1;
                                            $take_exam->update();
                                        }
                                    }
                                    else{
                                        $take_exam->lecture_id = $lecture->id;
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
                                        $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
                                        ->where('take_exam_id',$take_exam->id)->where('is_correct','yes')->count();
                                        $cal_percentage = ($correct_answers/$total_questions)*100;
                                        $cal_percentage = (int)$cal_percentage;
                                    }
                                    if($cal_percentage >= $quizPassedPercentage){
                                        $lecture = Course_lecture::find($lecture->id);
                                        if ($lecture) {
                                        
                                            if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
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
                                            $take_exam->lecture_id = $lecture->id;
                                            $take_exam->obtained_percentage = $cal_percentage;
                                            $take_exam->passing_percentage = $quizPassedPercentage;
                                            $take_exam->passed = 1;
                                            $take_exam->update();
                                        }
                                    }
                                    else{
                                        $take_exam->lecture_id = $lecture->id;
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
                    else{           //NEW CODE HEREEEEEEEEEEEEEEEEE///////////////////////////////////////
                        if (Take_exam::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)->count() == 0) {
                            $take_exam = new Take_exam();
                            $take_exam->exam_id = $data['exam']->id;
                            $take_exam->lecture_id = $lecture->id;
                            $take_exam->save();
                        }
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

        /// NEW CODE///////////////////////////////
        if ($request->get('tab') && $request->get('tab') == 'content') {
            $latestViewedLecture = $this->checkLastViewedLecture($data['course']->id);
            $data['lecture_type'] = $latestViewedLecture['lecture_type'];
            $data['lesson_id_check'] = $latestViewedLecture['lesson_id_check'];
            $data['lecture_id_check'] = $latestViewedLecture['lecture_id_check'];
            // $data['navLessonActive'] = $latestViewedLecture['navLessonActive'];
            // $data['subNavLectureActiveClass'] = $latestViewedLecture['subNavLectureActiveClass'];
            $data['course_lecture_views'] = $latestViewedLecture['course_lecture_views'];
            $data['nextLectureUuid'] = $latestViewedLecture['nextLectureUuid'];
            $data['youtube_video_src'] = $latestViewedLecture['youtube_video_src'];
            $data['slide_document_src'] = $latestViewedLecture['slide_document_src'];
            $data['video_src'] =  $latestViewedLecture['video_src'];
            $data['text_src'] = $latestViewedLecture['text_src'];
            $data['image_src'] = $latestViewedLecture['image_src'];
            $data['pdf_src'] = $latestViewedLecture['pdf_src'];
            $data['audio_src'] = $latestViewedLecture['audio_src'];
        }
        else{
            $latestViewedLecture = $this->checkLastViewedLecture($data['course']->id);
            $data['lecture_type'] = $latestViewedLecture['lecture_type'];
            $data['lesson_id_check'] = $latestViewedLecture['lesson_id_check'];
            $data['lecture_id_check'] = $latestViewedLecture['lecture_id_check'];
            // $data['navLessonActive'] = $latestViewedLecture['navLessonActive'];
            // $data['subNavLectureActiveClass'] = $latestViewedLecture['subNavLectureActiveClass'];
            $data['course_lecture_views'] = $latestViewedLecture['course_lecture_views'];
            $data['nextLectureUuid'] = $latestViewedLecture['nextLectureUuid'];
            $data['youtube_video_src'] = $latestViewedLecture['youtube_video_src'];
            $data['slide_document_src'] = $latestViewedLecture['slide_document_src'];
            $data['video_src'] =  $latestViewedLecture['video_src'];
            $data['text_src'] = $latestViewedLecture['text_src'];
            $data['image_src'] = $latestViewedLecture['image_src'];
            $data['pdf_src'] = $latestViewedLecture['pdf_src'];
            $data['audio_src'] = $latestViewedLecture['audio_src'];
        }
        $data['student_notifications'] = Notification::where('user_id', @auth()->user()->id)
            ->where('user_type', 3)
            ->where('is_seen', 'no')
            ->orderBy('created_at', 'DESC')
            ->get();

            $findStudent = Student::where('user_id',auth()->user()->id)->first();
            $data['student_id'] = $findStudent->id;
            $data['student_name'] = $findStudent->first_name.' '.$findStudent->last_name;
            
          
            // if($data['course']->lessons){
            //     foreach ($data['course']->lessons as $key => $value) {
            //         dd($value->lectures);
            //     }
            // }
        return view('frontend.student.course.course-details', $data);
    }

    public function checkLastViewedLecture($course_id){
        $data['slide_document_src'] = null;
        $data['youtube_video_src'] = null;
        $data['video_src'] = null;
        $data['text_src'] = null;
        $data['image_src'] = null;
        $data['pdf_src'] = null;
        $data['audio_src'] = null;
        $latestViewedLecture = Course_lecture_views::where('course_id',$course_id)
        ->where('user_id', auth()->id())->where('is_viewed_recently', 1)->orderBy('id','DESC')->first();
        if($latestViewedLecture){
            $lecture = Course_lecture::where('id', $latestViewedLecture->course_lecture_id)->firstOrFail(); //Get Lecture Data Through Given Lecture ID  
            
            if ($lecture->resource?->category?->name == 'Youtube' || $lecture->resource?->category?->name == 'youtube') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    $data['youtube_video_src'] = $lecture->resource?->youtube_url_path;
                }   
                else{
                    $data['youtube_video_src'] = null;
                }    
            }
            elseif ($lecture->resource?->category?->name == 'Slide Document' || $lecture->resource?->category?->name == 'slide document') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    $data['slide_document_src'] = $lecture->resource?->file_name;
                }
                else{
                    $data['slide_document_src'] = null;
                }
            }
            else if ($lecture->resource?->category?->name == 'Video' || $lecture->resource?->category?->name == 'video') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    $data['video_src'] =  $lecture->resource?->file_name;
                }    
                else{
                    $data['video_src'] = null;
                }         
            }
            elseif ($lecture->resource?->category?->name == 'Text' || $lecture->resource?->category?->name == 'text') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    $data['text_src'] = $lecture->resource?->text;
                } 
                else{
                    $data['text_src'] = null;
                }               
            }
            elseif ($lecture->resource?->category?->name == 'Image' || $lecture->resource?->category?->name == 'image') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    // $data['image_src'] = $lecture->image;
                    $data['image_src'] =  $lecture->resource?->file_name;
                } 
                else{
                    $data['image_src'] = null;
                }        
            }
            elseif ($lecture->resource?->category?->name == 'PDF' || $lecture->resource?->category?->name == 'pdf') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    $data['pdf_src'] = $lecture->resource?->file_name;
                } 
                else{
                    $data['pdf_src'] = null;
                }                
            } 
            elseif ($lecture->resource?->category?->name == 'Audio' || $lecture->resource?->category?->name == 'audio') {
                $lecture = Course_lecture::find($lecture->id);
                if ($lecture) {
                    $data['audio_src'] = $lecture->resource?->file_name;
                }
                else{
                    $data['audio_src'] = null;
                }                
            } 

            if($lecture->resource){
                $data['lecture_type'] = $lecture->resource?->category?->name;
            }
            elseif($lecture->quiz){
                $data['lecture_type'] = $lecture->type;
            }
            elseif($lecture->assignment){
                $data['lecture_type'] = $lecture->type;
            }
            // $data['lesson_id_check'] = @$lecture->lesson->id;
            $data['lesson_id_check'] = @$lecture->lesson->id;
            $data['lecture_id_check'] = $lecture->id;
            $data['navLessonActive'] = 'on';
            $data['subNavLectureActiveClass'] = 'show';
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $course_id)->where('user_id', auth()->id())->get();
            $data['nextLectureUuid'] = null;
        }
        else{
            $data['lecture_type'] = null;
            $data['lesson_id_check'] = null;
            $data['lecture_id_check'] = null;
            $data['course_lecture_views'] = Course_lecture_views::where('course_id', $course_id)->where('user_id', auth()->id())->get();
            $data['nextLectureUuid'] = null;
        }
        return $data;
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

    public function assignmentList(Request $request)
    {
        $data['assignments'] = Assignment::where('course_id', $request->course_id)->get();
        return view('frontend.student.course.partial.assignment.partial-assignment-list', $data);
    }

    public function assignmentDetails(Request $request)
    {
        if($request->lecture_uuid){
            $lecture_uuid  = $request->lecture_uuid;
        }
        else{
            $lecture_uuid  = null;
        }
        $data['lectureUUID'] = $lecture_uuid;
     
        $data['assignment'] = Assignment::whereCourseId($request->course_id)->whereId($request->assignment_id)->first();
        $data['assignmentSubmit'] = AssignmentSubmit::whereUserId(auth()->id())->whereCourseId($request->course_id)->whereAssignmentId($request->assignment_id)->first();
        return view('frontend.student.course.partial.assignment.partial-assignment-details', $data);
    }

    public function assignmentResult(Request $request)
    {
        if($request->lecture_uuid){
            $lecture_uuid  = $request->lecture_uuid;
        }
        else{
            $lecture_uuid  = null;
        }
        $data['lectureUUID'] = $lecture_uuid;
        
        $data['assignment'] = Assignment::whereCourseId($request->course_id)->whereId($request->assignment_id)->first();
        $data['assignmentSubmit'] = AssignmentSubmit::whereUserId(auth()->id())->whereCourseId($request->course_id)->whereAssignmentId($request->assignment_id)->first();
        return view('frontend.student.course.partial.assignment.partial-assignment-result', $data);
    }

    public function assignmentSubmit(Request $request)
    {
        $data['course_id'] = $request->course_id;
        if($request->lecture_uuid){
            $lecture_uuid  = $request->lecture_uuid;
        }
        else{
            $lecture_uuid  = null;
        }
        $data['lectureUUID'] = $lecture_uuid;
        $data['assignment'] = Assignment::whereCourseId($request->course_id)->whereId($request->assignment_id)->first();
        $data['assignmentSubmit'] = AssignmentSubmit::whereUserId(auth()->id())->whereCourseId($request->course_id)->whereAssignmentId($request->assignment_id)->first();

        return view('frontend.student.course.partial.assignment.partial-assignment-submit', $data);
    }

    public function assignmentSubmitStore(Request $request, $course_id, $assignment_id, $lectureUUID = null)
    {
        $validation = Validator::make($request->all(), [
            "file" => "mimes:pdf,zip"
        ]);

        if ($validation->fails()) {
            return response()->json([
                'messages' => $validation->errors()->all(),
            ]);
        }
        $courseData = Course::where('id',$course_id)->first();
        $lectureuuid = $request->lectureUUID;
        $Base_Route = route('student.my-course.show', ['slug'=>$courseData->slug, 'lecture_uuid'=>$lectureUUID]);
    

        if ($request->hasFile('file')) {
            $assignmentSubmit = AssignmentSubmit::whereUserId(auth()->id())->whereCourseId($course_id)->whereAssignmentId($assignment_id)->first();

            if (!$assignmentSubmit) {
                $assignmentSubmit = new AssignmentSubmit();
            } else {
                $this->deleteFile($assignmentSubmit->file);
            }

            $assignmentSubmit->course_id = $course_id;
            $assignmentSubmit->assignment_id = $assignment_id;

            $file_details = $this->uploadFileWithDetails('assignment_submit', $request->file);
            if (!$file_details['is_uploaded']) {
                return response()->json([
                    'message' => 'Something went wrong! Failed to upload file',
                    'status' => '404'
                ]);
            }
            $assignmentSubmit->file = $file_details['path'];
            $assignmentSubmit->original_filename = $file_details['original_filename'];
            $assignmentSubmit->save();
        }


        $data['assignment'] = Assignment::whereCourseId($course_id)->whereId($assignment_id)->first();
        $data['assignmentSubmit'] = AssignmentSubmit::whereUserId(auth()->id())->whereCourseId($course_id)->whereAssignmentId($assignment_id)->first();


        if($lectureUUID != null){
            $getLecture = Course_lecture::where('uuid',$lectureUUID)->first();
            if($getLecture){
                if ($getLecture->type == 'assignment' || $getLecture->type == 'Assignment') {
                    $lecture = Course_lecture::find($getLecture->id);
                    if ($lecture) {
                        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
                            $latest_active_enrollment = Enrollment::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->orderBy('id','DESC')->first();
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
            // $this->showToastrMessage('success', __('Assignment uploaded222 successfully'));
            // // return redirect($Base_Route);
            // return redirect()->to($Base_Route);
            // $result = [
            //     'BaseRoute' => $Base_Route,
            //     'from'=>'lecture',
            // ];
            return $Base_Route;
        }
        else{
            return view('frontend.student.course.partial.assignment.partial-assignment-details', $data);
        }
     
    }

    public function saveExamAnswer(Request $request, $course_uuid, $question_uuid, $take_exam_id)
    {
        $course = Course::whereUuid($course_uuid)->firstOrfail();
        $question = Question::whereUuid($question_uuid)->firstOrfail();
        $option = Question_option::whereUuid($request->selected_option_uuid)->firstOrfail();

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
            $take_exam->save();
        }

        // $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($data['exam']->id)
        // ->where('take_exam_id',$data['take_exam']->id)->pluck('question_id')->toArray();

        $question_ids = Answer::whereUserId(auth()->user()->id)->whereExamId($question->exam_id)
        ->where('take_exam_id',$take_exam_id)->pluck('question_id')->toArray();

        if (Question::whereExamId($question->exam_id)->whereNotIn('id', $question_ids)->count() > 0) {
            // return redirect(route('student.my-course.show', [$course->slug, 'start-quiz', $question->exam->uuid, $answer->id]));
            return redirect(route('student.my-course.show', [$course->slug, 'start-quiz', $question->exam->uuid]));
        } 
        else {
            $take_exam = Take_exam::find($take_exam_id);
            $total_questions = Question::whereExamId($take_exam->exam?->id)->count();
            $quizPassedPercentage = $take_exam->exam?->passing_percentage;
            $correct_answers = Answer::whereUserId(auth()->user()->id)->whereExamId($take_exam->exam?->id)
            ->where('take_exam_id',$take_exam_id)->where('is_correct','yes')->count();
            $cal_percentage = ($correct_answers/$total_questions)*100;
            $cal_percentage = (int)$cal_percentage;
            if($cal_percentage >= $quizPassedPercentage){
                $lecture = Course_lecture::where('id',$take_exam->lecture_id)->first();
                $user = Auth::user();
                $enrollment = Enrollment::where(['course_id' => $take_exam->lecture?->course_id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
              
                $take_exam->obtained_percentage = $cal_percentage;
                $take_exam->passing_percentage = $quizPassedPercentage;
                $take_exam->passed = 1;
                $take_exam->update();

                $quizResponse = $this->checkQuizCompletion($lecture,$user,$enrollment);

            }
            else{
                $take_exam->obtained_percentage = $cal_percentage;
                $take_exam->passing_percentage = $quizPassedPercentage;
                $take_exam->passed = 0;
                $take_exam->update();
            }
            return redirect(route('student.my-course.show', [$course->slug, 'quiz-result', $question->exam->uuid]));
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

    public function reviewCreate(Request $request)
    {
     
        $review_exists_user = Review::whereUserId(auth()->id())->whereCourseId($request->course_id)->first();
        if ($review_exists_user) {
            return response()->json([
                'status' => 302,
                'msg' => 'Already you have reviewed. Thank you.'
            ]);
        }

        $request->validate([
            'course_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
        ]);
        $video = null;
        if ($request->video) {
            $file_details = $this->uploadFileWithDetails('review', $request->video);
            if (!$file_details['is_uploaded']) {
                return response()->json([
                    'status' => 302,
                    'msg' => __('Something went wrong! Failed to upload video file')
                ]);
            }
            $video = $file_details['path'];
        }

        if($request->youtube_video_id){
            $video = $request->youtube_video_id;
        }
       


     
        $review = new Review();
        $review->video_check = $request->intro_video_check ?? null;
        $review->video =  $video ?? null;
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
            'msg' => 'Review Created Successfully.'
        ]);
    }

    public function reviewPaginate(Request $request, $courseId)
    {
        $data['reviews'] = Review::whereCourseId($courseId)->latest()->paginate(3);
        $response['appendReviews'] = View::make('frontend.student.course.partial.render-partial-review-list', $data)->render();
        $response['reviews'] = Review::whereCourseId($courseId)->latest()->paginate(3);
        return response()->json($response);
    }

    public function discussionCreate(Request $request)
    {
        $discussion = new Discussion();
        $discussion->user_id = Auth::id();
        $discussion->course_id = $request->course_id;
        $discussion->comment = $request->discussionComment;
        $discussion->status = 1;
        $discussion->comment_as = $request->comment_as;
        $discussion->save();

        return redirect()->back();
    }

    public function discussionReply(Request $request, $id)
    {
        $discussion = new Discussion();
        $discussion->user_id = Auth::id();
        $discussion->course_id = $request->course_id;
        $discussion->comment = $request->commentReply;
        $discussion->status = 1;
        $discussion->parent_id = $id;
        $discussion->comment_as = $request->comment_as;
        $discussion->save();

        Discussion::where('id', $id)
            ->update([
                'view' => 2
            ]);
        Discussion::where('parent_id', $id)->update([
            'view' => 2
        ]);

        return redirect()->back();
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

    public function saveCertificate(Request $request)
    {
        /** === make pdf certificate ===== */
        $course = Course::find($request->course_id);
        if (studentCourseProgress($course->id, $request->enrollment_id) == 100) {
            if (Certificate_by_instructor::where('course_id', $course->id)->count() > 0 && Student_certificate::where('course_id', $course->id)->where('user_id', auth()->id())->count() == 0) {
                $certificate_by_instructor = Certificate_by_instructor::where('course_id', $course->id)->orderBy('id', 'DESC')->first();
                $certificate = Certificate::find($certificate_by_instructor->certificate_id);
                if ($certificate) {
                    $certificate_name = 'certificate-' . $course->uuid . '.png';

                    $certificateFile = $request->file;  // your base64 encoded
                    $certificateFile = str_replace('data:image/png;base64,', '', $certificateFile);
                    $certificateFile = str_replace(' ', '+', $certificateFile);
                    \File::put(public_path('/uploads/certificate/student'). '/' . $certificate_name, base64_decode($certificateFile));

                    $student_certificate = new Student_certificate();
                    // $student_certificate->path = $this->saveImage('',  base64_decode($image), 'null', 'null');
                    $student_certificate->course_id = $course->id;
                    $student_certificate->certificate_number = $request->certificate_number;
                    $student_certificate->path = "/uploads/certificate/student/$certificate_name";
                    $student_certificate->save();

                    return response()->json([
                        'status' => 200
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 500
        ]);
        /** ------- end save certificate ----------- */
    }

    public function videoCompleted(Request $request)
    {
        $lecture = Course_lecture::find($request->lecture_id);

        if (Course_lecture_views::where('user_id', auth()->id())->where('course_id', $lecture->course_id)->where('course_lecture_id', $lecture->id)->count() == 0) {
            $course_lecture_views = new Course_lecture_views();
            $course_lecture_views->course_id = $lecture->course_id;
            $course_lecture_views->course_lecture_id = $lecture->id;
            $course_lecture_views->enrollment_id = $request->enrollment_id;
            $course_lecture_views->save();
        }


        $data = [
            'success' => 'success'
        ];

        /** === make pdf certificate ===== */
        $data['data'] = $this->makePdfCertificate($lecture->course_id, $request->enrollment_id);
        /** ------- end save certificate ----------- */

        return response()->json($data);
    }

    public function thankYou()
    {
        $data['pageTitle'] = __('Successfully Purchased');
        $new_order = Order::whereUserId(auth()->id())->latest()->first();

        if ($new_order) {
            $data['orderCourses'] = Order_item::whereOrderId($new_order->id)->whereNotNull('course_id')->get();
            $data['orderProducts'] = Order_item::whereOrderId($new_order->id)->whereNotNull('product_id')->get();
            $data['new_consultations'] = Order_item::whereOrderId($new_order->id)->whereNotNull('consultation_slot_id')->get();
        }

        return view('frontend.thankyou', $data);
    }


    public function bigBlueButtonJoinMeeting($liveClassId)
    {
        $liveClass = LiveClass::find($liveClassId);
        if ($liveClass) {
            return redirect()->to(
                Bigbluebutton::join([
                    'meetingID' => $liveClass->meeting_id,
                    'userName' => auth()->user()->student()->name ?? auth()->user()->name,
                    'password' => $liveClass->attendee_pw //which user role want to join set password here
                ])
            );
        } else {
            $this->showToastrMessage('error', __('Live Class is not found'));
            return redirect()->back();
        }
    }

    public function refundRequest(Request $request)
    {
        $enrollment = Enrollment::where('enrollments.status', STATUS_ACCEPTED)
        ->join('orders', 'orders.id', '=', 'enrollments.order_id')
        ->join('order_items', ['orders.id' => 'order_items.order_id', 'order_items.course_id' => 'enrollments.course_id'])
        ->where('enrollments.id', $request->enrollment_id)
        ->select(['enrollments.id', 'enrollments.order_id', 'enrollments.end_date', 'enrollments.owner_user_id', 'order_items.unit_price', 'order_items.id as order_item_id'])
        ->first();

        if(is_null($enrollment)){
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $exist = Refund::where('order_item_id', $enrollment->order_item_id)->whereIn('status', [STATUS_ACCEPTED, STATUS_PENDING])->first();

        if(!is_null($exist)){
            return response()->json(['status' => true, 'message' => 'Already in Refund request'], 200);
        }

        if($enrollment->end_date < now()){
            return response()->json(['status' => false, 'message' => 'This order has already expired'], 404);
        }

        Refund::create([
            'enrollment_id' => $enrollment->id,
            'order_item_id' => $enrollment->order_item_id,
            'user_id' => auth()->id(),
            'instructor_user_id' => $enrollment->owner_user_id,
            'reason' => $request->reason,
            'amount' => $enrollment->unit_price
        ]);

        $sendEmail = new EmailSendService();
        if($enrollment->owner->role == USER_ROLE_INSTRUCTOR){
            $url = route('instructor.refund.index');
        }
        else{
            $url = route('organization.refund.index');
        }

        $sendEmail->sendInstructorRefundRequest($enrollment->owner, $url);

        $this->send('Refund Request', 2, $url, $enrollment->owner_user_id);

        return response()->json(['status' => true, 'message' => 'Successfully requested'], 200);
    }
}
