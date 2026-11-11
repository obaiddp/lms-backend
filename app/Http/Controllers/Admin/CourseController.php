<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\EmailSendService;
use App\Services\CourseVaultManifestService;
use App\Services\LmsContentProcessor;
use App\Models\Course;
use App\Models\Course_lecture;
use App\Models\Course_language;
use App\Models\Resource;
use App\Models\CourseResource;
use App\Models\CoordinatorCourse;
use App\Models\Coordinator;
use App\Models\Course_lecture_views;
use App\Models\Course_lesson;
use App\Models\CourseInstructor;
use App\Models\Instructor;
use App\Models\CourseUploadRule;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Order_item;
use App\Models\Order_billing_address;
use App\Models\City;
use App\Models\ClassDetail;
use App\Models\Lesson;
use App\Models\Country;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Hamcrest\Core\AllOf;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    use General, ImageSaveTrait, SendNotification;
    protected $model, $lectureModel, $lessonModel;

    public function __construct(Course $course, Course_lesson $course_lesson,  Course_lecture $course_lecture)
    {
        $this->model = new Crud($course);
        $this->lectureModel = new Crud($course_lecture);
        $this->lessonModel = new Crud($course_lesson);
    }

    public function index()
    {
        if (!Auth::user()->can('all_course')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Courses';
        $data['courses'] = $this->model->getOrderById('DESC', 25);
        return view('admin.course.index', $data);
    }
///MAria ADDED this
public function create()
{
    $data['categories'] = Category::active()->orderBy('name', 'asc')->select('id', 'name')->get();
    $data['course_languages'] = Course_language::orderBy('name', 'asc')->select('id', 'name')->get();
    $data['title'] = 'Add Live Course';
    return view('admin.course.add', $data);
}



public function assignResources($course_id)
{
    $given_resource_ids = array();
    $data['title'] = 'Assign Resources';
    $data['courseID'] = $course_id;
    $data['resources'] = Resource::all();
    $getCourseResource = CourseResource::where('course_id',$course_id)->get();
    if(!$getCourseResource->isEmpty()){
        foreach ($getCourseResource as $key => $value) {
            $given_resource_ids[] = $value->resource_id;
        }
    }
    $given_resource_ids = array_unique($given_resource_ids);
    $data['resourceIDs'] = $given_resource_ids;
    return view('admin.course.assign_resource', $data);
}


public function storeAssignedResources(Request $request)
{
 
    $courseID = $request->course_id;
    $resources = $request->resource;
    if(empty($resources)){
        $this->showToastrMessage('error', __('Please Check Atleast One Resource!'));
        return redirect()->back();
    }
    // Str::uuid()->toString();
    $delete = CourseResource::where('course_id',$courseID)->delete();
    foreach ($resources as $key => $resource) {
        $getResourceDetail = Resource::where('id',$resource)->first();
        $courseResource = new CourseResource();
        $courseResource->course_id = $courseID;
        $courseResource->resource_id = $resource;
        $courseResource->original_filename = $getResourceDetail->file_name;
        $courseResource->file =   'uploads/course_resource/' .$getResourceDetail->file_name;
        $courseResource->save();
      
    }
    app(CourseVaultManifestService::class)->markStaleAndRebuild((int) $courseID);
     $this->showToastrMessage('success', __('Resources Assigned Successfully'));
    return redirect()->route('admin.course.index');
}




public function store(Request $request,\App\Services\LmsContentProcessor $processor)
{
    $request->validate([
        'title' => ['required', 'string', 'max:100'],
        'subtitle' => ['required', 'string', 'max:100'], 
        'category_id' => 'required|integer', 
        'subcategory_id' => 'required|integer', 
        'price' => 'required|integer',
        'learner_accessibility' => 'required',
        'course_lang_id' => 'required',
        'description' => 'required',
        'image'      => 'required|image|mimes:jpeg,png,jpg,gif', 
    ]);

    if (Course::where('slug', Str::slug($request->title))->count() > 0)
    {
        $slug = Str::slug($request->title) . '-'. rand(100000, 999999);
    } else {
        $slug = Str::slug($request->title);
    }

   
    
    if ($request->image) {
        $request->validate([
            'image' => 'mimes:jpg,png,jpeg,gif,svg'
        ]);
        // $this->deleteFile($course->image); // delete file from server
        $image = $this->saveImage('course', $request->image, null, null); // new file upload into server
    } else {
        $image = null;
    }



    if ($request->cover_image) {
        $request->validate([
            'cover_image' => 'mimes:jpg,png,jpeg,gif,svg'
        ]);
        // $this->deleteFile($course->image); // delete file from server
        $cover_image = $this->saveImage('course', $request->cover_image, null, null); // new file upload into server
    } else {
        $cover_image = null;
    }


    if($request->hasFile('og_image')){
        $og_image = $this->saveImage('meta', $request->og_image, null, null);
    }
    else{

        $og_image = $image; 
    }



    if ($request->home_image_1) {
        $request->validate([
            'home_image_1' => 'mimes:jpg,png,jpeg'
        ]);
        // $this->deleteFile($course->image); // delete file from server
        $home_image_1 = $this->saveImage('course', $request->home_image_1, null, null); // new file upload into server
    } else {
        $home_image_1 = null;
    }





    if ($request->home_image_2) {
        $request->validate([
            'home_image_2' => 'mimes:jpg,png,jpeg'
        ]);
        // $this->deleteFile($course->image); // delete file from server
        $home_image_2 = $this->saveImage('course', $request->home_image_2, null, null); // new file upload into server
    } else {
        $home_image_2 = null;
    }




    $course = new Course();
    $course->title = $request->title;
    $course->slug = $slug;
    $course->course_language_id = (int)$request->course_lang_id;
    $course->subtitle = $request->subtitle;
    $course->price =  $request->price;
    $course->image =  $image;
    $course->classes =  $request->classes;
    $course->duration =  $request->duration;
    if($request->duration && $request->duration_type){
        $course->duration_type =  (int)$request->duration_type;
    }
    $course->cover_image =  $cover_image;
    $course->og_image =  $og_image;
    $course->home_image_1 =  $home_image_1;
    $course->home_image_2 =  $home_image_2;
    $course->category_id = $request->category_id;
    $course->subcategory_id = $request->subcategory_id;
    $course->learner_accessibility = $request->learner_accessibility;

    if($request->description){
        $processedHtml = $processor->process($request->description); // user paste
    }
    else{
        $processedHtml =  $request->description;
    }

    $course->description = $processedHtml;

    $course->short_description = $request->short_description;
    $course->meta_title = $request->meta_title;
    $course->meta_description = $request->meta_description;
    $course->meta_keywords = $request->meta_keywords;
   
    // 'meta_title' => $request->meta_title,
    // 'meta_description' => $request->meta_description,
    // 'meta_keywords' => $request->meta_keywords,


    $course->course_type = 3;
    $course->save();

    app(CourseVaultManifestService::class)->markStaleAndRebuild((int) $course->id);

   

    $this->showToastrMessage('success', __('Standard Course created successfully'));
    return redirect()->route('admin.course.index');
}
public function edit($uuid)
{
    $data['title'] = 'Edit Live Course';
    $data['categories'] = Category::active()->orderBy('name', 'asc')->select('id', 'name')->get();
    // $data['subcategories'] = Subcategory::orderBy('name', 'asc')->select('id', 'name')->get();
    $data['course_languages'] = Course_language::orderBy('name', 'asc')->select('id', 'name')->get();
    $data['course'] = $this->model->getRecordByUuid($uuid);
    $CategoryID = $data['course']->category_id;
    $category_id = (int)$CategoryID;
    $category = Category::where('id',$category_id)->first();
    if($category){
        $category_id = $category->id;
    }
    else{
        $category_id = 0;
    }
    $data['instructors'] = Instructor::where('status',1)->get();
    $data['subcategories'] = Subcategory::where('category_id',$category_id)->orderBy('name', 'asc')->select('id', 'name')->get();

    $data['coordinators'] = Coordinator::where('status',1)->orderBy('id','DESC')->get();
    $checkCoordinators = CoordinatorCourse::where('course_id',$data['course']->id)->get();
    if(!$checkCoordinators->isEmpty()){
        foreach ($checkCoordinators as $key => $checkCourse) {
            $coordinators[]=$checkCourse->coordinator_id;
        }
        $coordinators= array_unique($coordinators);
    }
    else{
        $coordinators = []; 
    }
    $data['check_coordinators'] = $coordinators;


    return view('admin.course.edit', $data);
}

public function update(Request $request, $uuid,\App\Services\LmsContentProcessor $processor)
{
    $course = $this->model->getRecordByUuid($uuid);

    $request->validate([
        'title' => ['required', 'string', 'max:100'],
        'subtitle' => ['required', 'string', 'max:100'], 
        'category_id' => 'required|integer', 
        'course_instructor_id' => 'required|integer', 
        'subcategory_id' => 'required|integer', 
        'price' => 'required|integer',
        'learner_accessibility' => 'required',
        'course_lang_id' => 'required',
        'description' => 'required',
     
    ]);

    if($course->image == null){
         $request->validate([
            'image'      => 'required|image|mimes:jpeg,png,jpg,gif', 
        ]);
    }
    else{
        $request->validate([
            'image'      => 'image|mimes:jpeg,png,jpg,gif', 
        ]);
    }
  $checkExists = Course::where('title', $request->title)->first();
  if($checkExists == null){
    if (Course::where('slug', Str::slug($request->title))->count() > 0)
    {
        $slug = Str::slug($request->title) . '-'. rand(100000, 999999);
    } else {
        $slug = Str::slug($request->title);
    }

  }
  else{
    $slug=$checkExists->slug;
  }
   
 
    if($request->course_instructor_id){
            $getInstructor = Instructor::where('id',$request->course_instructor_id)->first();
            $getUser = User::where('id',$getInstructor->user_id)->first();    
    }


    $course->title = $request->title;
    $course->subtitle = $request->subtitle;
    $course->price =  $request->price;
    $course->course_language_id = (int)$request->course_lang_id;
    $course->learner_accessibility = $request->learner_accessibility;
    $course->description = $request->description;
    $course->short_description = $request->short_description;
    $course->meta_title = $request->meta_title;
    $course->meta_description = $request->meta_description;
    $course->meta_keywords = $request->meta_keywords;
    $course->course_type = 3;

  

  
    if ($request->image) {
        $request->validate([
            'image' => 'mimes:jpg,png,jpeg,gif,svg'
        ]);
        $this->deleteFile($course->image); // delete file from server
        $image = $this->saveImage('course', $request->image, null, null); // new file upload into server
    } else {
        $image = $course->image;
    }


    if($request->hasFile('og_image')){
        $this->deleteFile($course->og_image); // delete file from server
        $og_image = $this->saveImage('meta', $request->og_image, null, null);
    }
    else{
         
        if($course->og_image){
            $og_image = $course->og_image; 
        }
        else{
            $og_image = $image;
        }
    }

  
    if ($request->cover_image) {
        $request->validate([
            'cover_image' => 'mimes:jpg,png,jpeg,gif,svg'
        ]);
        $this->deleteFile($course->cover_image); // delete file from server
        $cover_image = $this->saveImage('course', $request->cover_image, null, null); // new file upload into server
    } else {
        $cover_image = $course->cover_image;
    }


    
    if ($request->home_image_1) {
        $request->validate([
            'home_image_1' => 'mimes:jpg,png,jpeg'
        ]);
        $this->deleteFile($course->home_page_1); // delete file from server
        $home_image_1 = $this->saveImage('course', $request->home_image_1, null, null); // new file upload into server
    } else {
        $home_image_1 = $course->home_page_1;
    }





    if ($request->home_image_2) {
        $request->validate([
            'home_image_2' => 'mimes:jpg,png,jpeg'
        ]);
        $this->deleteFile($course->home_page_2); // delete file from server
        $home_image_2 = $this->saveImage('course', $request->home_image_2, null, null); // new file upload into server
    } else {
        $home_image_2 = $course->home_page_2;
    }


    if($request->duration && $request->duration_type){
        $durationType =  (int)$request->duration_type;
        // if($durationType == 1){ // Hour
        //     $durationInHours = $request->duration;
        // }
        // else if($durationType == 2){ // Days
        //     $durationInHours = $request->duration;
        // }
        // else{ // Months
        //     $durationInHours = $request->duration;
        // }
    }
    else{
        $durationType =  $course->duration_type;
        // $durationInHours =  $course->duration_in_hours;
    }



    if($request->description){
        $processedHtml = $processor->process($request->description); // user paste
    }
    else{
        $processedHtml =  $request->description;
    }




    $coordinator_ids = $request->coordinator_id;
    if(!empty($coordinator_ids)){
        $checkExisting = CoordinatorCourse::where('course_id',$course->id)->first();
        if($checkExisting){
            $deleteExisting = CoordinatorCourse::where('course_id',$course->id)->delete();
        }
        foreach ($coordinator_ids as $key => $id) {
        
            $newICData = new CoordinatorCourse;
            $newICData->course_id = $course->id;
            $newICData->coordinator_id = (int)$id;
            $newICData->created_at = Carbon::now();
            $newICData->save();
        }
      
    }



    $course_data = [
       
        'title' => $request->title,
        'subtitle' => $request->subtitle,
        'slug' => $slug,
        'price' => $request->price,
        'learner_accessibility' => $request->learner_accessibility,
        'course_language_id' => (int)$request->course_lang_id,
        'description' => $processedHtml,
        'short_description' => $request->short_description,
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
        'classes' => $request->classes,
        'duration' => $request->duration,
        'duration_type'=> $durationType,
        // 'duration_in_hours' => $durationInHours,
        'image' => $image,
        'cover_image' => $cover_image,
        'og_image' => $og_image,
        'home_image_1' => $home_image_1,
        'home_image_2' => $home_image_2,
        'meta_title' => $request->meta_title,
        'meta_description' => $request->meta_description,
        'meta_keywords' => $request->meta_keywords,
      
        'course_type' => 3,
      
    ];
 
    $this->model->updateByUuid($course_data, $uuid);

    app(CourseVaultManifestService::class)->markStaleAndRebuild((int) $course->id);

    if($request->course_instructor_id){
          $checkExists2 = Course::where('id', $checkExists->id)->first();
          if($checkExists2){
                $checkExists2->instructor_id = $getInstructor->id;
                $checkExists2->save();
          }
    }
    $this->showToastrMessage('success', __('Updated Successfully'));
    return redirect()->route('admin.course.index');
}
//////////
    public function view($uuid)
    {
        $data['title'] = "Course Details";
        $data['course'] = $this->model->getRecordByUuid($uuid);

        $check = Enrollment::join('students', 'students.user_id', 'enrollments.user_id')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'enrollments.user_id')
                 ->whereNull('users.deleted_at');
        })
        ->where('enrollments.course_id', $data['course']->id)
        ->select('enrollments.*', 'students.uuid', DB::raw('CONCAT(students.first_name, " ", students.last_name) as name'))
        ->with('user')
        ->get();

        if(!$check->isEmpty()){
            foreach ($check as $key => $studentEnrollment) {
             
                if(checkIfExpired($studentEnrollment)){
                    if(checkIfLifetime($studentEnrollment->end_date)){

                    }
                }
                else{    //Expired
                    $studentEnrollment->status = 0;
                    $studentEnrollment->save(); 
                }
              
            }
        }



        // $check2 = Enrollment::join('students', 'students.user_id', 'enrollments.user_id')
        // ->join('users', function ($join) {
        //     $join->on('users.id', '=', 'enrollments.user_id')
        //          ->whereNull('users.deleted_at');
        // })
        // ->where('enrollments.course_id', $data['course']->id)
        // ->select('enrollments.*', 'students.uuid', DB::raw('CONCAT(students.first_name, " ", students.last_name) as name'))
        // ->with('user')
        // ->get();


        // dd($check2);
        $data['students'] = Enrollment::join('students', 'students.user_id', 'enrollments.user_id')
        ->join('users', function ($join) {
            $join->on('users.id', '=', 'enrollments.user_id')
                 ->whereNull('users.deleted_at');
        })
        ->where('enrollments.course_id', $data['course']->id)
        ->select('enrollments.*', 'students.uuid', DB::raw('CONCAT(students.first_name, " ", students.last_name) as name'))
        ->with('user')
        ->latest()
        ->paginate(15);

        return view('admin.course.view', $data);
    }

    public function approved()
    {
        if (!Auth::user()->can('approved_course')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Approved Courses';
        $data['courses'] = Course::where('status', 1)->paginate(25);
        return view('admin.course.approved', $data);
    }

    
    public function reviewPending()
    {
        if (!Auth::user()->can('pending_course')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Review Pending Courses';
        $data['courses'] = Course::where('status', 2)->paginate(25);
        return view('admin.course.review-pending', $data);
    }

    public function reviewUpcoming()
    {
        if (!Auth::user()->can('pending_course')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Upcoming Courses';
        $data['courses'] = Course::where('status', STATUS_UPCOMING_REQUEST)->orWhere('status', STATUS_UPCOMING_APPROVED)->paginate(25);
        return view('admin.course.review-upcoming', $data);
    }

    public function hold()
    {
        if (!Auth::user()->can('hold_course')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Hold Courses';
        $data['courses'] = Course::where('status', 3)->paginate(25);
        return view('admin.course.hold', $data);
    }

    public function statusChange($uuid, $status)
    {
        $course = $this->model->getRecordByUuid($uuid);
        $course->status = $status;
        $course->save();

        $emailSend = new EmailSendService();
        if ($status == 1) {
            setBadge($course->user_id);
            $text = __("Course has been approved");
            $target_url = route('course-details', $course->slug);
            $this->send($text, 2, $target_url, $course->user_id);
            $emailSend->sendCommonUserAndLink($course->user, $target_url, 'new-course-approved-instructor');
            /** ====== send notification to student ===== */
            $students = Student::where('user_id', '!=', $course->user_id)->select('user_id')->get();
            foreach ($students as $student) {
                $text = __("New course has been published");
                $target_url = route('course-details', $course->slug);
                $this->send($text, 3, $target_url, $student->user_id);
            }
            $emails = $students->pluck('user_id');
            $emailSend->sendCommonLink($emails, $target_url, 'new-course-approved-student');
            /** ====== send notification to student ===== */
        }

        if ($status == 3) {
            $text = __("Course has been hold");
            $target_url = route('instructor.course');
            $this->send($text, 2, $target_url, $course->user_id);
            $emailSend->sendCommonUserAndLink($course->user, $target_url, 'course-hold-student');
        }


        $this->showToastrMessage('success', __('Status has been changed'));
        return redirect()->back();

    }

    public function featureChange(Request $request)
    {
        $course = $this->model->getRecordById($request->id);
        $course->is_featured = $request->status;
        $course->save();
    }

    public function delete($uuid)
    {
        $course = $this->model->getRecordByUuid($uuid);
        // $order_item = Order_item::whereCourseId($course->id)->first();
        // if ($order_item)
        // {
        //     $this->showToastrMessage('error', __('Course can not deleted. Because already student purchased this course!'));
        //     return redirect()->back();
        // }
        $course_class_details = ClassDetail::where('course_id',$course->id)->get();
        if (count($course_class_details) > 0)
        {
            $classIDs = array();
            foreach ($course_class_details as $key => $class_detail) {
               $classIDs[] = $class_detail->class_id;
               $class_detail->delete();
            }
            $classIDs = array_unique($classIDs);
            if(!empty($classIDs)){
                $getClass = Lesson::where('id',$classID)->first();
                if($getClass){
                    $getClass->delete();
                }
            }
        }



        $course_order_items = Order_item::whereCourseId($course->id)->get();
        if (count( $course_order_items) > 0)
        {
            $orderIDs = array();
            foreach ($course_order_items as $key => $order_item) {
               $orderIDs[] = $order_item->order_id;
               $order_item->delete();
            }
            $orderIDs = array_unique($orderIDs);
            if(!empty($orderIDs)){
                foreach ($orderIDs as $key => $orderID) {
                    Order_billing_address::where('order_id', $orderID)->get()->map(function ($q) {
                        $q->delete();
                    });
                }
                $getOrder = Order::where('id',$orderID)->first();
                if($getOrder){
                    $getOrder->delete();
                }
            }
        }

        //start:: Course lesson delete
        $lessons = Course_lesson::where('course_id', $course->id)->get();
        if (count($lessons) > 0)
        {
            foreach ($lessons as $lesson)
            {
                //start:: lecture delete
                $lectures = Course_lecture::where('lesson_id', $lesson->id)->get();
                if (count($lectures) > 0)
                {
                    foreach ($lectures as $lecture)
                    {
                        $lecture = Course_lecture::find($lecture->id);
                        if ($lecture)
                        {
                            if($lecture->resource_id == null || $lecture->quiz_id == null || $lecture->assignment_id == null){
                                $this->deleteFile($lecture->resource?->file); // delete file from server
                                if ($lecture->type == 'vimeo')
                                {
                                    if ($lecture->url_path)
                                    {
                                        $this->deleteVimeoVideoFile($lecture->url_path);
                                    }
                                }
                            }
                        
                            Course_lecture_views::where('course_lecture_id', $lecture->id)->get()->map(function ($q) {
                                $q->delete();
                            });

                            $this->lectureModel->delete($lecture->id); // delete record
                        }
                    }
                }
                //end:: lecture delete
                $this->lessonModel->delete($lesson->id);
            }
        }
        //end: lesson delete

        $this->deleteFile($course->image);
        $this->deleteVideoFile($course->video);
        $course->delete();
        $this->showToastrMessage('success', __('Course has been deleted.'));
        return redirect()->back();
    }

    public function courseUploadRuleIndex()
    {
        $data['title'] = 'Courses Upload Rules';
        $data['courseRules'] = CourseUploadRule::all();
        return view('admin.course.upload-rules', $data);
    }

    public function courseUploadRuleStore(Request $request)
    {
        $courseUploadRuleTitle = $request->courseUploadRuleTitle;
        if ($courseUploadRuleTitle) {
            $inputs = Arr::except($request->all(), ['_token']);
            $keys = [];

            foreach ($inputs as $k => $v) {
                $keys[$k] = $k;
            }

            foreach ($inputs as $key => $value) {
                $option = Setting::firstOrCreate(['option_key' => $key]);
                $option->option_value = $value;
                $option->save();
            }
        }


        $now = now();
        if ($request['course_upload_rules']) {

            if (count(@$request['course_upload_rules']) > 0) {
                foreach ($request['course_upload_rules'] as $course_upload_rules) {
                    if (@$course_upload_rules['description']) {
                        if (@$course_upload_rules['id']) {
                            $rule = CourseUploadRule::find($course_upload_rules['id']);
                        } else {
                            $rule = new CourseUploadRule();
                        }
                        $rule->description = @$course_upload_rules['description'];
                        $rule->updated_at = $now;
                        $rule->save();
                    }
                }
            }
        }

        CourseUploadRule::where('updated_at', '!=', $now)->get()->map(function ($q) {
            $q->delete();
        });

        $this->showToastrMessage('success', __('Updated Successful'));
        return redirect()->back();
    }

    public function courseEnroll()
    {
        $data['title'] = 'Course Enroll';
        $data['users'] = User::where('role','!=', 1)->get();
        $data['courses'] = Course::all();

        return view('admin.course.enroll-student', $data);
    }

    public function courseEnrollStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'course_id' => 'required',
            'expired_after_days' => 'bail|nullable|integer|min:1',
        ]);

        if ($request->course_id) {
            $courseOrderExits = Enrollment::where(['user_id' => $request->user_id, 'course_id' => $request->course_id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();

            if ($courseOrderExits) {
                $order = Order::find($courseOrderExits->order_id);
                if ($order) {
                    if ($order->payment_status == 'due') {
                        Order_item::whereOrderId($courseOrderExits->order_id)->get()->map(function ($q) {
                            $q->delete();
                        });
                        $order->delete();
                    } else {
                        $this->showToastrMessage('error', __("Student has already purchased the course!"));
                        return redirect()->back();
                    }
                }
            }
        }

        $ownCourseCheck = CourseInstructor::where('course_id', $request->course_id)->where('instructor_id', $request->user_id)->delete();

        if ($ownCourseCheck) {
            $this->showToastrMessage('error', __("He is a owner of the course. Can't purchase this course!"));
            return redirect()->back();
        }
        $course = Course::find($request->course_id);
        $order = new Order();
        $order->user_id = $request->user_id;
        $order->order_number = rand(100000, 999999);
        $order->payment_status = 'free';
        $order->created_by_type = 2;
        $order->save();

        $order_item = new Order_item();
        $order_item->order_id = $order->id;
        $order_item->user_id = $request->user_id;
        $order_item->course_id = $request->course_id;
        $order_item->owner_user_id = $course->user_id ?? null;
        $order_item->unit_price = 0;
        $order_item->admin_commission = 0;
        $order_item->owner_balance = 0;
        $order_item->sell_commission = 0;
        $order_item->save();


        set_instructor_ranking_level($course->user_id);

        /** ====== Send notification =========*/
        $text = __("New student enrolled");
        $target_url = route('instructor.all-student');
        foreach ($order->items as $item)
        {
            if ($item->course)
            {
                $this->send($text, 2, $target_url, $item->course->user_id);
            }

            $expiredDays = !is_null($request->expired_after_days) && $request->expired_after_days > 0 ? $request->expired_after_days : NULL;
            setEnrollment($item, $expiredDays);
        }

        $text = __("Course has been sold");
        $this->send($text, 1, null, null);

        $text = __("New course enrolled by Admin");
        $target_url = route('student.my-learning');
        $this->send($text, 3, $target_url, $request->user_id);

        /** ====== Send notification =========*/

        $this->showToastrMessage('success', __('Student enroll in course'));
        return redirect()->back();
    }


    public function getCoursePrice(Request $request)
    {
        $course_id = (int)$request->course_id;
        $course = Course::where('id',$course_id)->first();
        if($course){
            $course_price = (int)$course->price;
        }
        else{
            $course_price = null;
        }
            // dd( $result_array);
            return response()->json($course_price);
        // return view('admin.classes.add', $data);
    }

    public function courseOrders(Request $request)
    {
        $data['title'] = __('Course Orders');
        $data['subNavOrderProductActiveClass'] = 'active';
        $data['navProductActiveClass'] = 'has-open';

        $data['orderItems'] = Order_item::whereHas('order', function ($q) {
            $q->where('payment_status', 'paid')
                ->orWhere('payment_status', 'free')
                ->orWhere('payment_status', 'due');
            $q->where('cart_type',1);
        })->whereNotNull('course_id')->orderBy('id', 'DESC')->paginate(10);

        return view('admin.course.orders', $data);
    }


}
