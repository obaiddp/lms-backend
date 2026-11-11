<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\LessionRequest;
use App\Http\Services\EmailSendService;
use App\Models\Course;
use App\Models\Course_lecture;
use App\Models\Course_lecture_views;
use App\Models\Course_lesson;
use App\Models\Enrollment;
use App\Models\ResourceCategory;
use App\Models\Resource;
use App\Models\Exam;
use App\Models\Assignment;
use App\Models\Order_item;
use App\Services\CourseVaultManifestService;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use File;
use Vimeo\Vimeo;


class LessonController extends Controller
{

    use General, ImageSaveTrait, SendNotification;

    protected $model;
    protected $courseModel;
    protected $lectureModel;

    public function __construct(Course_lesson $course_lesson, Course $course, Course_lecture $course_lecture)
    {
        $this->model = new Crud($course_lesson);
        $this->courseModel = new Crud($course);
        $this->lectureModel = new Crud($course_lecture);
    }

    public function store(LessionRequest $request, $course_uuid)
    {
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $data = [
            'course_id' => $course->id,
            'name' => $request->name,
            'short_description' => $request->short_description ?  : null,
        ];

        $this->model->create($data);
        $this->rebuildVaultManifest((int) $course->id);
        $this->showToastrMessage('success', __('Created successful.'));
        return redirect()->back();

    }

    public function updateLesson(LessionRequest $request, $course_uuid, $lesson_uuid)
    {
        $lesson = $this->model->getRecordByUuid($lesson_uuid);
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $data = [
            'course_id' => $course->id,
            'name' => $request->name,
            'short_description' => $request->short_description ?  : null,
        ];

        $this->model->update($data, $lesson->id);
        $this->rebuildVaultManifest((int) $course->id);
        $this->showToastrMessage('success', __('Updated successful.'));
        return redirect()->back();

    }

    public function deleteLesson($lesson_uuid)
    {
        $lesson = $this->model->getRecordByUuid($lesson_uuid);
        $courseId = (int) $lesson->course_id;
        $this->model->deleteByUuid($lesson_uuid);
        $this->rebuildVaultManifest($courseId);
        $this->showToastrMessage('success', __('Deleted successful'));
        return redirect()->back();
    }


    public function uploadLecture($course_uuid, $lesson_uuid)
    {
       
        $data['title'] = 'Upload Lecture';
        $data['navCourseUploadActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $data['lesson'] = $this->model->getRecordByUuid($lesson_uuid);
        $data['lessons'] = Course_lesson::where('course_id', $data['course']->id)->with('lectures')->get();
        $data['lesson_categories'] = ResourceCategory::all();
        $getImageCategoryID = ResourceCategory::where('name','Image')->first();
        $categoryID = $getImageCategoryID->id;
    
     
        $lesson_category_resources = Resource::where('category_id',$categoryID)->get();
   
 
        $data['lesson_category_resources'] = Resource::where('category_id',$categoryID)->get();   //Get First Image Category Resources By Default On Page Load Up
        // dd( $data['lesson_category_resources']);
  
        return view('instructor.course.upload-lecture', $data);
    }









    public function uploadQuizLecture($course_uuid, $lesson_uuid)
    {
       
        $data['title'] = 'Upload Quiz';
        $data['navCourseUploadActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $courseID = $data['course']->id;
        $data['lesson'] = $this->model->getRecordByUuid($lesson_uuid);
        $data['lessons'] = Course_lesson::where('course_id', $data['course']->id)->with('lectures')->get();
        $data['lesson_categories'] = ResourceCategory::all();
        $getImageCategoryID = ResourceCategory::where('name','Image')->first();
        $categoryID = $getImageCategoryID->id;
    
     
        $lesson_category_resources = Resource::where('category_id',$categoryID)->get();
   
        $data['lesson_quizzes'] = Exam::where('course_id',$courseID)->get();   //Get All Quizess Of Current Course
        // dd( $data['lesson_category_resources']);
  
        // return view('instructor.course.upload-lecture', $data);
        return view('instructor.course.upload-quiz-lecture', $data);
    }


    public function storeQuizLecture(Request $request, $course_uuid, $lesson_uuid)
    {
        $request->validate([
            'title' => ['required','string'],
            'lecture_type' => ['required','integer'],
            'lecture_quiz_id' => ['required','integer'],
        ]);

        $lectureQuizID = $request->lecture_quiz_id;
        if($lectureQuizID == null || $lectureQuizID == ""){
            $this->showToastrMessage('error', __('Please Select Your Lecture Quiz'));
            return redirect()->back();
        }
     
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $lesson = $this->model->getRecordByUuid($lesson_uuid);

        $lecture = new Course_lecture();
        $lecture->fill($request->all());
        $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
        $lecture->course_id = $course->id;
        $lecture->lesson_id = $lesson->id;
        $lecture->quiz_id = (int)$lectureQuizID;
        $lecture->type = "quiz";
        $lecture->vimeo_upload_type = null;
        $lecture->save();
        $this->rebuildVaultManifest((int) $course->id);
     
   
  
    
        $emailSend = new EmailSendService();
  
        if ($course->status == 1) {
        
            /** ====== send notification to student ===== */
            $target_url = route('student.my-course.show', $course->slug);
            $students = Enrollment::where('course_id', $course->id)->select('user_id')->get();
            foreach ($students as $student)
            {
                $text = __("New lesson lecture has been added");
                // $target_url = route('student.my-course.show', $course->slug);
                $this->send($text, 3, $target_url, $student->user_id);
            }
         
            $emails = $students->pluck('user_id');
            // $emailSend->sendCommonLink($emails, $target_url, 'new-lesson-added-student');
            /** ====== send notification to student ===== */
        }
   
        if ($course->status != 0) {
            $text = __("New lesson lectyre has been added");
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }

    
        if(auth()->user()->role == 2){ 
           return redirect(route('instructor.course.edit', [$course->uuid, 'step=lesson']));
        }else if(auth()->user()->role == 1){

            return redirect(route('admin.course.edit', [$course->uuid, 'step=lesson']));
        }
    }

    public function editQuizLecture($course_uuid, $lesson_uuid, $lecture_uuid, $lecture_quizid = null)
    {
        $lecture_quizid = (int)$lecture_quizid;
        $data['title'] = 'Edit Quiz';
        $data['navCourseActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $courseID = $data['course']->id;
        $data['lesson'] = $this->model->getRecordByUuid($lesson_uuid);
        $data['lecture'] = $this->lectureModel->getRecordByUuid($lecture_uuid);
        $data['lessons'] = Course_lesson::where('course_id', $data['course']->id)->with(['lectures' => function($q) use($lecture_uuid){
            $q->where('uuid', '!=', $lecture_uuid);
        }])->get();
   
     
     
   
        $data['lesson_quizzes'] = Exam::where('course_id',$courseID)->get();   
        
        // return view('instructor.course.edit-lecture', $data);
        return view('instructor.course.edit-quiz-lecture', $data);
    }

    public function updateQuizLecture(Request $request,  $lecture_uuid , $course_uuid , $lesson_uuid)
    {
     
        $request->validate([
            'title' => ['required','string'],
            'lecture_type' => ['required','integer'],
            'lecture_quiz_id' => ['required','integer'],
        ]);
    
        $lectureQuizID = $request->lecture_quiz_id;
      
        if($lectureQuizID == null || $lectureQuizID == ""){
            $this->showToastrMessage('error', __('Please Select Your Lecture Quiz'));
            return redirect()->back();
        }
     
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $lesson = $this->model->getRecordByUuid($lesson_uuid);

            $lecture = Course_lecture::whereUuid($lecture_uuid)->firstOrFail();
            $lecture->fill($request->all());
            $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
            $lecture->course_id = $course->id;
            $lecture->lesson_id = $lesson->id;
            $lecture->quiz_id = (int)$lectureQuizID;
            $lecture->type = "quiz";
            $lecture->vimeo_upload_type = null;
            $lecture->update();
            $this->rebuildVaultManifest((int) $course->id);
            $courseID =  $lecture->course_id;
            $courseSlug =  $lecture->course->slug;
            $courseUUID =  $lecture->course->uuid;
        
        // $lecture = Course_lecture::whereUuid($lecture_uuid)->firstOrFail();
        // $lecture->fill($request->all());
        // $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
        // $lecture->save();

        /** ====== send notification to student ===== */
        $students = Order_item::where('course_id', $courseID)->select('user_id')->get();
        $emailSend = new EmailSendService();
        foreach ($students as $student)
        {
            $text = __("Lesson lecture has been updated");
            $target_url = route('student.my-course.show', $courseSlug);
            $this->send($text, 3, $target_url, $student->user_id);
        }

        $emails = $students->pluck('user_id');

        //$emailSend->sendCommonLink($emails, $target_url, 'new-lesson-updated-student');
        /** ====== send notification to student ===== */

        if ($lecture->course->status != 0)
        {
            $text = __("New lesson lecture has been added");
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }
        if(auth()->user()->role == 1){
            return redirect(route('admin.course.edit', [$courseUUID, 'step=lesson']));
        }elseif(auth()->user()->role == 2){
        
            return redirect(route('instructor.course.edit', [$courseUUID, 'step=lesson']));
        }
       
    }












    public function uploadAssignmentLecture($course_uuid, $lesson_uuid)
    {
       
        $data['title'] = 'Upload Assignment';
        $data['navCourseUploadActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $courseID = $data['course']->id;
        $data['lesson'] = $this->model->getRecordByUuid($lesson_uuid);
        $data['lessons'] = Course_lesson::where('course_id', $data['course']->id)->with('lectures')->get();
        $data['lesson_categories'] = ResourceCategory::all();
        $getImageCategoryID = ResourceCategory::where('name','Image')->first();
        $categoryID = $getImageCategoryID->id;
    
     
        $lesson_category_resources = Resource::where('category_id',$categoryID)->get();
   
        $data['lesson_assignments'] = Assignment::where('course_id',$courseID)->get();   //Get All Assignments Of Current Course
        // dd( $data['lesson_category_resources']);
  
        // return view('instructor.course.upload-lecture', $data);
        return view('instructor.course.upload-assignment-lecture', $data);
    }

    public function storeAssignmentLecture(Request $request, $course_uuid, $lesson_uuid)
    {
        $request->validate([
            'title' => ['required','string'],
            'lecture_type' => ['required','integer'],
            'lecture_assignment_id' => ['required','integer'],
        ]);

        $lectureAssignmentID = $request->lecture_assignment_id;
        if($lectureAssignmentID == null || $lectureAssignmentID == ""){
            $this->showToastrMessage('error', __('Please Select Your Lecture Assignment'));
            return redirect()->back();
        }
     
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $lesson = $this->model->getRecordByUuid($lesson_uuid);

        $lecture = new Course_lecture();
        $lecture->fill($request->all());
        $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
        $lecture->course_id = $course->id;
        $lecture->lesson_id = $lesson->id;
        $lecture->assignment_id = (int)$lectureAssignmentID;
        $lecture->type = "assignment";
        $lecture->vimeo_upload_type = null;
        $lecture->save();
        $this->rebuildVaultManifest((int) $course->id);
     
   
  
    
        $emailSend = new EmailSendService();
  
        if ($course->status == 1) {
        
            /** ====== send notification to student ===== */
            $target_url = route('student.my-course.show', $course->slug);
            $students = Enrollment::where('course_id', $course->id)->select('user_id')->get();
            foreach ($students as $student)
            {
                $text = __("New lesson lecture has been added");
                // $target_url = route('student.my-course.show', $course->slug);
                $this->send($text, 3, $target_url, $student->user_id);
            }
         
            $emails = $students->pluck('user_id');
            // $emailSend->sendCommonLink($emails, $target_url, 'new-lesson-added-student');
            /** ====== send notification to student ===== */
        }
   
        if ($course->status != 0) {
            $text = __("New lesson lectyre has been added");
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }

    
        if(auth()->user()->role == 2){ 
           return redirect(route('instructor.course.edit', [$course->uuid, 'step=lesson']));
        }else if(auth()->user()->role == 1){

            return redirect(route('admin.course.edit', [$course->uuid, 'step=lesson']));
        }
    }
    public function editAssignmentLecture($course_uuid, $lesson_uuid, $lecture_uuid, $lecture_assignmentid = null)
    {
        $lecture_assignmentid = (int)$lecture_assignmentid;
        $data['title'] = 'Edit Assignment';
        $data['navCourseActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $courseID = $data['course']->id;
        $data['lesson'] = $this->model->getRecordByUuid($lesson_uuid);
        $data['lecture'] = $this->lectureModel->getRecordByUuid($lecture_uuid);
        $data['lessons'] = Course_lesson::where('course_id', $data['course']->id)->with(['lectures' => function($q) use($lecture_uuid){
            $q->where('uuid', '!=', $lecture_uuid);
        }])->get();
   
        // dd($lecture_resourceid);
      
     
   
        $data['lesson_assignments'] = Assignment::where('course_id',$courseID)->get();   
        
        // return view('instructor.course.edit-lecture', $data);
        return view('instructor.course.edit-assignment-lecture', $data);
    }
    public function updateAssignmentLecture(Request $request,  $lecture_uuid , $course_uuid , $lesson_uuid)
    {
        $request->validate([
            'title' => ['required','string'],
            'lecture_type' => ['required','integer'],
            'lecture_assignment_id' => ['required','integer'],
        ]);

        $lectureAssignemtID = $request->lecture_assignment_id;
      
        if($lectureAssignemtID == null || $lectureAssignemtID == ""){
            $this->showToastrMessage('error', __('Please Select Your Lecture Quiz'));
            return redirect()->back();
        }
    
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $lesson = $this->model->getRecordByUuid($lesson_uuid);

            $lecture = Course_lecture::whereUuid($lecture_uuid)->firstOrFail();
            $lecture->fill($request->all());
            $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
            $lecture->course_id = $course->id;
            $lecture->lesson_id = $lesson->id;
            $lecture->assignment_id = (int)$lectureAssignemtID;
            $lecture->type = "assignment";
            $lecture->vimeo_upload_type = null;
            $lecture->update();
            $this->rebuildVaultManifest((int) $course->id);
            $courseID =  $lecture->course_id;
            $courseSlug =  $lecture->course->slug;
            $courseUUID =  $lecture->course->uuid;
        
        // $lecture = Course_lecture::whereUuid($lecture_uuid)->firstOrFail();
        // $lecture->fill($request->all());
        // $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
        // $lecture->save();

        /** ====== send notification to student ===== */
        $students = Order_item::where('course_id', $courseID)->select('user_id')->get();
        $emailSend = new EmailSendService();
        foreach ($students as $student)
        {
            $text = __("Lesson lecture has been updated");
            $target_url = route('student.my-course.show', $courseSlug);
            $this->send($text, 3, $target_url, $student->user_id);
        }

        $emails = $students->pluck('user_id');

        //$emailSend->sendCommonLink($emails, $target_url, 'new-lesson-updated-student');
        /** ====== send notification to student ===== */

        if ($lecture->course->status != 0)
        {
            $text = __("New lesson lecture has been added");
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }
        if(auth()->user()->role == 1){
            return redirect(route('admin.course.edit', [$courseUUID, 'step=lesson']));
        }elseif(auth()->user()->role == 2){

            return redirect(route('instructor.course.edit', [$courseUUID, 'step=lesson']));
        }
       
    }









    public function getLessonCategoryResources(Request $request , $category_type)
    {
      
        $result_array = array();
        $types = '';
        $categoryType = $request->categoryType;
        $find = ResourceCategory::where('name',$categoryType)->first();
      
        if($find){
            $categoryID = $find->id;
            $findResources = Resource::where('category_id',$categoryID)->get();
            if(!$findResources->isEmpty()){
                foreach ($findResources as $key => $resource) {
                    $result_array[] = [
                        'resource_id' =>  $resource->id,
                        'resource_name' =>  $resource->name,
                    ];
                }
            }
        
        }
        // dd($result_array);   
        echo json_encode($result_array);
    }

    public function storeLecture(Request $request, $course_uuid, $lesson_uuid)
    {
        $request->validate([
            'title' => ['required','string'],
            'lecture_type' => ['required','integer'],
            'lecture_resource_id' => ['required','integer'],
        ]);

        $lectureResourceID = $request->lecture_resource_id;
        if($lectureResourceID == null || $lectureResourceID == ""){
            $this->showToastrMessage('error', __('Please Select Your Lecture Resource'));
            return redirect()->back();
        }
     
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $lesson = $this->model->getRecordByUuid($lesson_uuid);

        $lecture = new Course_lecture();
        $lecture->fill($request->all());
        $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
        $lecture->course_id = $course->id;
        $lecture->lesson_id = $lesson->id;
        $lecture->resource_id = (int)$lectureResourceID;
        $lecture->type = "resource";
        $lecture->vimeo_upload_type = null;
        $lecture->save();
        $this->rebuildVaultManifest((int) $course->id);
     
   
  
    
        $emailSend = new EmailSendService();
  
        if ($course->status == 1) {
        
            /** ====== send notification to student ===== */
            $target_url = route('student.my-course.show', $course->slug);
            $students = Enrollment::where('course_id', $course->id)->select('user_id')->get();
            foreach ($students as $student)
            {
                $text = __("New lesson lecture has been added");
                // $target_url = route('student.my-course.show', $course->slug);
                $this->send($text, 3, $target_url, $student->user_id);
            }
         
            $emails = $students->pluck('user_id');
            // $emailSend->sendCommonLink($emails, $target_url, 'new-lesson-added-student');
            /** ====== send notification to student ===== */
        }
   
        if ($course->status != 0) {
            $text = __("New lesson lectyre has been added");
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }

    
        if(auth()->user()->role == 2){ 
           return redirect(route('instructor.course.edit', [$course->uuid, 'step=lesson']));
        }else if(auth()->user()->role == 1){

            return redirect(route('admin.course.edit', [$course->uuid, 'step=lesson']));
        }
    }

    public function editLecture($course_uuid, $lesson_uuid, $lecture_uuid, $lecture_resourceid = null)
    {
        $lecture_resourceid = (int)$lecture_resourceid;
        $data['title'] = 'Edit Lecture';
        $data['navCourseActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $data['lesson'] = $this->model->getRecordByUuid($lesson_uuid);
        $data['lecture'] = $this->lectureModel->getRecordByUuid($lecture_uuid);
        $data['lessons'] = Course_lesson::where('course_id', $data['course']->id)->with(['lectures' => function($q) use($lecture_uuid){
            $q->where('uuid', '!=', $lecture_uuid);
        }])->get();
   
        // dd($lecture_resourceid);
        if($lecture_resourceid != null || $lecture_resourceid){
            $getResource = Resource::where('id',$lecture_resourceid)->first();
            $getResourceCategoryID = $getResource->category_id;
        }
        else{
            $getResourceCategoryID = 0;
        }
     
   
        $data['lesson_categories'] = ResourceCategory::all();
        $data['lesson_category_resources'] = Resource::where('category_id',$getResourceCategoryID)->get();   //Get First Image Category Resources By Default On Page Load Up
        
        return view('instructor.course.edit-lecture', $data);
    }

    public function updateLecture(Request $request,  $lecture_uuid , $course_uuid , $lesson_uuid)
    {
        $request->validate([
            'title' => ['required','string'],
            'lecture_type' => ['required','integer'],
            'lecture_resource_id' => ['required','integer'],
        ]);

        $lectureResourceID = $request->lecture_resource_id;
      
        if($lectureResourceID == null || $lectureResourceID == ""){
            $this->showToastrMessage('error', __('Please Select Your Lecture Resource'));
            return redirect()->back();
        }
    
        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $lesson = $this->model->getRecordByUuid($lesson_uuid);

            $lecture = Course_lecture::whereUuid($lecture_uuid)->firstOrFail();
            $lecture->fill($request->all());
            $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
            $lecture->course_id = $course->id;
            $lecture->lesson_id = $lesson->id;
            $lecture->resource_id = (int)$lectureResourceID;
            $lecture->type = "resource";
            $lecture->vimeo_upload_type = null;
            $lecture->update();
            $this->rebuildVaultManifest((int) $course->id);
            $courseID =  $lecture->course_id;
            $courseSlug =  $lecture->course->slug;
            $courseUUID =  $lecture->course->uuid;
        
        // $lecture = Course_lecture::whereUuid($lecture_uuid)->firstOrFail();
        // $lecture->fill($request->all());
        // $lecture->pre_ids = ($lecture->pre_ids) ? json_encode($lecture->pre_ids) : NULL;
        // $lecture->save();

        /** ====== send notification to student ===== */
        $students = Order_item::where('course_id', $courseID)->select('user_id')->get();
        $emailSend = new EmailSendService();
        foreach ($students as $student)
        {
            $text = __("Lesson lecture has been updated");
            $target_url = route('student.my-course.show', $courseSlug);
            $this->send($text, 3, $target_url, $student->user_id);
        }

        $emails = $students->pluck('user_id');

        //$emailSend->sendCommonLink($emails, $target_url, 'new-lesson-updated-student');
        /** ====== send notification to student ===== */

        if ($lecture->course->status != 0)
        {
            $text = __("New lesson lecture has been added");
            $target_url = route('admin.course.index');
            $this->send($text, 1, $target_url, null);
        }
        if(auth()->user()->role == 1){
            return redirect(route('admin.course.edit', [$courseUUID, 'step=lesson']));
        }elseif(auth()->user()->role == 2){

            return redirect(route('instructor.course.edit', [$courseUUID, 'step=lesson']));
        }
       
    }

    public function deleteLecture($course_uuid, $lecture_uuid)
    {
        $lecture = $this->lectureModel->getRecordByUuid($lecture_uuid);
        $courseId = (int) $lecture->course_id;
        $this->deleteFile($lecture->file_path); // delete file from server

        if ($lecture->type == 'vimeo')
        {
            if ($lecture->url_path)
            {
                $this->deleteVimeoVideoFile($lecture->url_path);
            }
        }

        Course_lecture_views::where('course_lecture_id', $lecture->id)->get()->map(function ($q) {
            $q->delete();
        });

        $this->lectureModel->deleteByUuid($lecture_uuid); // delete record
        $this->rebuildVaultManifest($courseId);

        $this->showToastrMessage('success', __('Lecture has been deleted'));
        if(auth()->user()->role == 1){
            return redirect(route('admin.course.edit', [$course_uuid, 'step=lesson']));
        }elseif(auth()->user()->role == 2){

            return redirect(route('instructor.course.edit', [$course_uuid, 'step=lesson']));
        }

       
    }


    private function saveLectureVideo($request, $lecture)
    {

//        $lecture->file_path = $this->uploadFile('video', $request->video_file); // new file upload into server;
        $file_details = $this->uploadFileWithDetails('video', $request->video_file);
        if ($file_details['is_uploaded']) {
            $lecture->file_path = $file_details['path'];
        }
//        $lecture->file_size = number_format(File::size($lecture->file_path) / 1048576, 2);
            try {
                $duration = gmdate("i:s", $request->file_duration);
                $lecture->file_duration = $duration;

                $file_duration_second = $request->file_duration;
                $lecture->file_duration_second =(int)$file_duration_second;
            } catch (\Exception $exception)
            {
                //
            }

    }

    function timeToSeconds(string $time): int
    {
        $arr = explode(':', $time);
        if (count($arr) === 3) {
            return $arr[0] * 3600 + $arr[1] * 60 + $arr[2];
        }
        return $arr[0] * 60 + $arr[1];
    }

    private function rebuildVaultManifest(int $courseId): void
    {
        app(CourseVaultManifestService::class)->markStaleAndRebuild($courseId);
    }



}
