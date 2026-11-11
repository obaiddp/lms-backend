<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Country;
use App\Models\Enrollment;
use App\Models\Instructor;
use App\Models\CourseInstructor;
use App\Models\Student;
use App\Models\Order;
use App\Models\InstructorCoordinator;
use App\Models\Order_item;
use App\Models\State;
use App\Models\Coordinator;
use App\Models\Lesson;
use App\Models\TrialClass;
use App\Models\ZoomSetting;
use App\Models\Course;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ClassDetail;
use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Libraries\Bbb;
use App\Services\GoogleMeetService;
use App\Services\GoogleOAuthService;


class ClassController extends Controller
{
    use General, ImageSaveTrait;
    protected $classModel;
    protected $classDetailModel;
    private $meetService;
    public function __construct(Lesson $class, ClassDetail $class_detail, Student $student)
    {
       
       
        
     
        // $this->meetService = $meetService;
        $this->studentModel = new Crud($student);
        $this->classModel = new Crud($class);
        $this->classDetailModel = new Crud($class_detail);
        $this->bb=new Bbb();
    }

   
    public function index(Request $request)
    {
        if (!Auth::user()->can('all_classes')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Classes';

        // $data['classes'] = $this->classModel->getOrderById('DESC', 25);

        $query = Lesson::query(); // Assuming you have these relationships
        $data['classes'] = $query->orderBy('id', 'DESC')->paginate(25);

        if ($request->has('search')) {
            $searchTerm = $request->search;
         
            $query->where(function($q) use ($searchTerm) {
                $q->where('no_of_students', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('no_of_classes', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('duration', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('type', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('learning_tool', 'LIKE', "%{$searchTerm}%");
                    // ->orWhereHas('order', function($subQuery) use ($searchTerm) {
                    //     $subQuery->where('id', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('grand_total', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('payment_method', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('payment_id', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('year', 'LIKE', "%{$searchTerm}%")
                    //     ->orWhereHas('user', function($subQuery2) use ($searchTerm) {
                    //         $subQuery2->where('name', 'LIKE', "%{$searchTerm}%");
                    //     });
                    // });  
            });
        }
        $data['classes'] = $query->orderBy('id', 'DESC')->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'classes' => view('admin.classes.table-data-index', $data)->render(),
                'pagination' => $data['classes']->links()->toHtml()
            ]);
        }


        // $data['classes'] = $this->classModel->getOrderById('DESC', 25);
      
        return view('admin.classes.index', $data);
    }

    public function viewIndividualClassesHistory()
    {
        $today_date = date('Y-m-d');
        $student_id = null;
        $instructor_id = null;
        if (!Auth::user()->can('all_individual')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Individual Classes History';
        // $data['classes'] = $this->classModel->getOrderById('DESC', 25);
     
        $getAllClassesHistory = ClassDetail::where('class_date' ,'<',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })->orderBy('id','DESC')
          ->orderBy('class_date','DESC')
          ->orderBy('class_time','DESC')
          ->paginate(100);

        $data['getAllClassesHistory'] = $getAllClassesHistory;
    
  

     
        return view('admin.classes.view_individual_classes_history', $data);
    }


    public function viewGroupClassesHistory()
    {
        $today_date = date('Y-m-d');
        $student_id = null;
        $instructor_id = null;
        if (!Auth::user()->can('all_group')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Group Classes History';
        // $data['classes'] = $this->classModel->getOrderById('DESC', 25);
     
        $getGroupClassesHistory = ClassDetail::where('class_date' ,'<',$today_date)
    ->whereHas('class', function($query) {
        $query->where('type', 'Group');
    })
    ->orderBy('id','DESC')
    ->orderBy('class_id','DESC')
    ->orderBy('class_date','DESC')
    ->groupBy('class_id','class_date')
    ->havingRaw('COUNT(*) >= 1')
    ->paginate(100);

        // $getGroupClassesHistory = ClassDetail::where('class_date' ,'<',$today_date)->wherehas('class' , function($query){
        //     $query->where('type', 'Group');
        // })
        // ->orderBy('id','DESC')
        // ->orderBy('class_id','DESC')
        // ->orderBy('class_date','DESC')
        // ->groupBy('class_id')
        // ->paginate(100);

     
        // $getAllClassesHistory = ClassDetail::where('class_date' ,'<',$today_date)->wherehas('class' , function($query){
        //     $query->where('type', 'Individual');
        // })->orderBy('id','DESC')
        //   ->orderBy('class_date','DESC')
        //   ->orderBy('class_time','DESC')
        //   ->paginate(100);

        $data['getAllClassesHistory'] = $getGroupClassesHistory;
    
  

     
        return view('admin.classes.view_group_classes_history', $data);
    }


    public function viewIndividualClasses(Request $request)
    {
        $student_id = null;
        $instructor_id = null;
        if (!Auth::user()->can('all_individual')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Individual Classes';
        // $data['classes'] = $this->classModel->getOrderById('DESC', 25);
     
     
        // $data['classes'] = Lesson::where('type','Individual')->orderBy('id','DESC')->paginate(25);

        $query = Lesson::query(); // Assuming you have these relationships
        $data['classes'] = $query->where('type','Individual')->orderBy('id', 'DESC')->paginate(25);

        if ($request->has('search')) {
            $searchTerm = $request->search;
         
            $query->where(function($q) use ($searchTerm) {
                $q->WhereHas('student', function($subQuery) use ($searchTerm) {
                        $subQuery->WhereHas('user', function($subQuery2) use ($searchTerm) {
                            $subQuery2->where('name', 'LIKE', "%{$searchTerm}%");
                        });
                    })
                    ->orWhereHas('instructor', function($subQuery) use ($searchTerm) {
                        $subQuery->WhereHas('user', function($subQuery2) use ($searchTerm) {
                            $subQuery2->where('name', 'LIKE', "%{$searchTerm}%");
                        });
                    })
                    ->orwhere('no_of_students', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('no_of_classes', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('duration', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('type', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('learning_tool', 'LIKE', "%{$searchTerm}%");
                    // ->orWhereHas('order', function($subQuery) use ($searchTerm) {
                    //     $subQuery->where('id', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('grand_total', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('payment_method', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('payment_id', 'LIKE', "%{$searchTerm}%")
                    //     ->orwhere('year', 'LIKE', "%{$searchTerm}%")
                    //     ->orWhereHas('user', function($subQuery2) use ($searchTerm) {
                    //         $subQuery2->where('name', 'LIKE', "%{$searchTerm}%");
                    //     });
                    // });  
            });
        }
        $data['classes'] = $query->where('type','Individual')->orderBy('id', 'DESC')->paginate(25);

        if ($request->ajax()) {
            return response()->json([
                'individual_classes' => view('admin.classes.table-data-individual', $data)->render(),
                'pagination' => $data['classes']->links()->toHtml()
            ]);
        }


     
        return view('admin.classes.view_individual_classes', $data);
    }


    public function viewGroupClasses()
    {
        if (!Auth::user()->can('all_group')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Group Classes';
        // $data['classes'] = $this->classModel->getOrderById('DESC', 25);
        $data['classes'] = Lesson::where('type','Group')->orderBy('id','DESC')->paginate(25);
      
        return view('admin.classes.view_group_classes', $data);
    }



    public function createIndividual()
    {
      
        $checkUpdate =check_and_update_zoom_account_minutes();
        $checkActivate =check_and_activate_zoom_account();
        if($checkActivate == 2){ // Means No Available Future Accounts
            $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
        }
        else if($checkActivate == 1){  // Activated
            $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
        }
        else{
            // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
        }

        $data['title'] = 'Add Individual Class';
        $userids = array();
        $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
        if(!$data['instructors']->isEmpty()){
            foreach ($data['instructors'] as $key => $value) {
                $userids[] = $value->user_id;
            }
        }
        // $data['students'] = Student::orderBy('id', 'asc')->get();
        $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();
        // dd($data['students']);


        return view('admin.classes.add_individual', $data);
    }




    public function createFreeClassIndividual($id)
    {
        $checkUpdate =check_and_update_zoom_account_minutes();
        $checkActivate =check_and_activate_zoom_account();
        if($checkActivate == 2){ // Means No Available Future Accounts
            $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
        }
        else if($checkActivate == 1){  // Activated
            $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
        }
        else{
            // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
        }

        $free_class_id = (int)$id;
        $getFreeClass = TrialClass::where('id',$id)->first();
        $data['given_free_class_id'] =  $free_class_id ;
        $data['given_student_id'] =  $getFreeClass->student_id;
        $data['given_student_name'] =  $getFreeClass->student?->name;
        $data['given_lesson_date'] =  $getFreeClass->prefer_date;
        // $data['given_lesson_time'] =  $getFreeClass->prefer_time;
        $currentDate = Carbon::now()->toDateString();
        $data['currentDate'] =  $currentDate;
    
        $givenDate = Carbon::createFromFormat('Y-m-d', $getFreeClass->prefer_date);
        $data['given_lesson_time'] = $getFreeClass->prefer_time;
    
        $dayName = $givenDate->format('l');
        $prederDaysArray = explode(',',$dayName);
        $data['prefer_day_array']  = $prederDaysArray;
     
     

     
        $data['title'] = 'Add Free Class';
        $userids = array();
        $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
        if(!$data['instructors']->isEmpty()){
            foreach ($data['instructors'] as $key => $value) {
                $userids[] = $value->user_id;
            }
        }
        // $data['students'] = Student::orderBy('id', 'asc')->get();
        $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();
     
        return view('admin.classes.add_free_class_individual', $data);
    }



    public function storeFreeClassIndividual(Request $request)
    {
        $request->validate([
            'student_id' => ['required', 'numeric'],
            'instructor_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            // 'total_classes' => ['required', 'numeric'],
            'prefer_time' => ['required'],
            'prefer_date' => ['required', 'date'],
            'duration' => ['required', 'numeric'],
            'learning_tool' => ['required'],
        ]);
        $free_class_id = $request->free_class_id;
        $preferday = $request->preferdays;
        $preferdayString = implode(',',$preferday);
        if (empty($preferday)) {
            $this->showToastrMessage('error', __('Select Prefer Days field is required.'));
            return redirect()->route('classes.create_free_class_individual',['id'=>$free_class_id]);
        }

        $Learning_Tool = $request->learning_tool;
        if($Learning_Tool == 'BBB'){
            if(get_option('bbb_status') == 0){
                $this->showToastrMessage('error', __('BigBlueButton Status is disabled in application_settings.'));
                return redirect()->route('classes.create_free_class_individual',['id'=>$free_class_id]);
            }
        }
        else if($Learning_Tool == 'Zoom'){
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            if($zoomSettings == null){
                $this->showToastrMessage('error', __('Zoom Account is disabled in application_settings.'));
                return redirect()->route('classes.create_free_class_individual',['id'=>$free_class_id]);
            }
            else{
                // $checkActivate =  check_and_activate_zoom_account();
                // if($checkActivate == 2){ // Means No Available Future Accounts
                //     $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
                //     return redirect()->route('classes.create_free_class_individual',['id'=>$free_class_id]);
                // }
                // else if($checkActivate == 1){  // Activated
                //     // $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
                // }
                // else{
                //     // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
                // }
            }
        }

        // $totalClass = $request->total_classes;
      
        $totalClass = 1;
        $totalStudents = 1;
        $Duration = $request->duration;
        $Type = 'Individual';
        $Learning_Tool = $request->learning_tool;
        $master_record = new Lesson();
        $master_record->student_id = $request->student_id;
        $master_record->instructor_id = $request->instructor_id;
        $master_record->no_of_students = $totalStudents;
        $master_record->no_of_classes = $totalClass;
        $master_record->duration = $Duration;
        $master_record->type = $Type;
        $master_record->prefer_days = $preferdayString;
        $master_record->learning_tool = $Learning_Tool;
        $master_record->free_class = 1;
        $master_record->save();
        $class_id=$master_record->id;

        $getTrailData = TrialClass::where('id',$free_class_id)->first();
        $getTrailData->status = 1;
        $getTrailData->update();

        $classDate = $request->prefer_date;
        $classTime = $request->prefer_time;
        $student_id = $request->student_id;
        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;
        $days_name = array();
        if(!empty($preferday[0]))
        {
            $day1 = $preferday[0] ;
            $days_name[] = $preferday[0] ;
        }
        if(!empty($preferday[1]))
        {
            $day2 = $preferday[1] ;
            $days_name[] = $preferday[1] ;
        }
        if(!empty($preferday[2]))
        {
            $day3 = $preferday[2] ;
            $days_name[] = $preferday[2] ;
        }
        if(!empty($preferday[3]))
        {
            $day4 = $preferday[3] ;
            $days_name[] = $preferday[3] ;
        }
        if(!empty($preferday[4]))
        {
            $day5 = $preferday[4] ;
            $days_name[] = $preferday[4] ;
        }
        if(!empty($preferday[5]))
        {
            $day6 = $preferday[5] ;
            $days_name[] = $preferday[5] ;
        }
        if(!empty($preferday[6]))
        {
            $day7 = $preferday[6] ;
            $days_name[] = $preferday[6] ;
        }
        $days_name = implode(",",$days_name);
		$timestamp = strtotime($classTime);
		$time_12h_format = date('h:i A', $timestamp);
        $end_time = strtotime($classTime) + $Duration*60; //added 40 mint in selected time
        $start_time = strtotime($classTime); //added 40 mints selected time
        $start_time_2 = Carbon::createFromTimestamp($start_time);
        $end_time_2 = Carbon::createFromTimestamp($end_time);
        $start_time_2 = $start_time_2->format('H:i');
        $end_time_2 = $end_time_2->format('H:i');
        $ClassTimeUpdated = date("h:i:sa", strtotime($classTime));
		$CurrentTimeUpdated = date('h:i:sa') ;
        $i= 0;
    
        $count = $totalClass;
        $datesforstartandend = array();//for getting the start and end date for email.
        while ($i < $count) {
          
            $lesson_date =  date('d-m-Y', strtotime($classDate)); // get date
            $day_name = date('l', strtotime($lesson_date)); //get day name like monday
            $holi_day =  date('d', strtotime($lesson_date)); //get day in number like 01
			$holi_month =  date('m', strtotime($lesson_date)); // get month in number like 01
            foreach ($preferday as $key => $day) {
                if($day_name == $day){
                    $lesson_date =  date('d-m-Y', strtotime($lesson_date));
                    $month = date('m',strtotime($lesson_date)); //change the 
					$year = date('Y',strtotime($lesson_date)); //change the format
                    $datesforstartandend[] = $lesson_date;
                    $ClassDate1 = date("d-m-Y", strtotime($lesson_date));
                    $CurrentDate1 = date('d-m-Y');
                    $classdatendtime = $ClassDate1. " " .$ClassTimeUpdated;
                    $currentdatendtime = $CurrentDate1. " " .$CurrentTimeUpdated;
                    if(strtotime($ClassDate1) > strtotime($CurrentDate1))    //Schedules Class On Next Day From Current Date
                    {
                        $classStatus = 'Scheduled';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                        $class_detail_data = [
                            'class_id' => $class_id,
                            'student_id' => $student_id,
                            'instructor_id' => $instructor_id,
                            'course_id' => $course_id,
                            'class_date' => $lesson_date_updated,
                            'class_time' => $classTime,
                            'status' => $classStatus,
                            'start_time_unix' => $start_time,
                            'end_time_unix' => $end_time,
                            'start_time' => $start_time_2,
                            'end_time' => $end_time_2,
                        ];
                     
                        $this->classDetailModel->create($class_detail_data);
                    }
                    else if(strtotime($ClassDate1) == strtotime($CurrentDate1) && strtotime($ClassTimeUpdated) >  strtotime($CurrentTimeUpdated) ) //Pending Class On Current Day
				    {
                       
                        $classStatus = 'Pending';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                        $class_detail_data = [
                            'class_id' => $class_id,
                            'student_id' => $student_id,
                            'instructor_id' => $instructor_id,
                            'course_id' => $course_id,
                            'class_date' => $lesson_date_updated,
                            'class_time' => $classTime,
                            'status' => $classStatus,
                            'start_time_unix' => $start_time,
                            'end_time_unix' => $end_time,
                            'start_time' => $start_time_2,
                            'end_time' => $end_time_2,
                        ];
                     
                        $this->classDetailModel->create($class_detail_data);
                     
                    
                    }
                    else
				    {
                        $this->showToastrMessage('error', __('You Can not assign Class in Previous Time.'));
                        return redirect()->route('classes.create_free_class_individual',['id'=>$free_class_id]);
				    }
                    $i++; //add +1 when preferday found .
                }
             
            }
            $classDate = date("Y-m-d", strtotime("+1 day", strtotime($classDate)));
        }

        $this->showToastrMessage('success', __('Free Class Assigned Successfully.'));
        return redirect()->route('classes.view_individual_classes');
      

       

    }




    public function viewFreeClassIndividual($student_id , $trial_id)
    {
     
        $getTrialClass = TrialClass::where('id',$trial_id)->first();
        $data['getTrialClass'] = $getTrialClass;
    
        $data['title'] = 'Free Class';
        $data['student'] = $this->studentModel->getRecordByID($student_id);
        // $data['student'] = $this->studentModel->getRecordByUuid($uuid);
     
        $data['enrollments'] = Enrollment::where('user_id', $data['student']->user_id)->whereNotNull('course_id')->latest()->paginate(15);
     
        return view('admin.classes.view_free_class_detail', $data);
    }


    public function getTutorCourses(Request $request)
    {
        $result_array = array();
        $courseids = array();
        $instructor_id = (int)$request->instructor_id;
        $instructor = Instructor::where('id',$instructor_id)->first();
        if($instructor){
            $instructor_user_id = $instructor->user_id;
        }
        else{
            $instructor_user_id = 0;
        }
        $instructorCourses = CourseInstructor::where('instructor_id',$instructor_user_id)->get();
        if(!$instructorCourses->isEmpty()){
            foreach ($instructorCourses as $key => $value) {
                $courseids[] = $value->course_id;
            }
            $courseids = array_unique($courseids);
        }
        // $courses = Course::whereIn('id',$courseids)->where('status',1)->where('course_type',3)->get();
        $courses = Course::whereIn('id',$courseids)->where('status',1)->get();
        if(!$courses->isEmpty()){
            foreach ($courses as $key => $value) {
                $result_array[] =[
                    'id'=>$value->id,
                    'name'=>$value->title,
                ];
            }
        }
            // dd( $result_array);
            return response()->json($result_array);
        // return view('admin.classes.add', $data);
    }







    public function getCategorySubCategories(Request $request)
    {
        $result_array = array();
        $sub_category_ids = array();
        $category_id = (int)$request->category_id;
        $category = Category::where('id',$category_id)->first();
        if($category){
            $category_id = $category->id;
        }
        else{
            $category_id = 0;
        }
     
        $categorySubCategories = Subcategory::where('category_id',$category_id)->get();
        if(!$categorySubCategories->isEmpty()){
            foreach ($categorySubCategories as $key => $value) {
                $sub_category_ids[] = $value->id;
            }
            $sub_category_ids = array_unique($sub_category_ids);
        }
        $sub_categories = Subcategory::whereIn('id',$sub_category_ids)->get();
        if(!$sub_categories->isEmpty()){
            foreach ($sub_categories as $key => $value) {
                $result_array[] =[
                    'id'=>$value->id,
                    'name'=>$value->name,
                ];
            }
        }
            return response()->json($result_array);
        // return view('admin.classes.add', $data);
    }

public function storeIndividual(Request $request)
    {
     
        $request->validate([
            'student_id' => ['required', 'numeric'],
            'instructor_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            'total_classes' => ['required', 'numeric'],
            'prefer_time' => ['required'],
            'prefer_date' => ['required', 'date'],
            'duration' => ['required', 'numeric'],
            'learning_tool' => ['required'],
        ]);
        $preferday = $request->preferdays;
        $preferdayString = implode(',',$preferday);
        if (empty($preferday)) {
            $this->showToastrMessage('error', __('Select Prefer Days field is required.'));
            return redirect()->route('classes.create_individual');
        }
        $Learning_Tool = $request->learning_tool;
        if($Learning_Tool == 'BBB'){
            if(get_option('bbb_status') == 0){
                $this->showToastrMessage('error', __('BigBlueButton Status is disabled in application_settings.'));
                return redirect()->route('classes.create_individual');
            }
        }
        else if($Learning_Tool == 'Zoom'){
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            if($zoomSettings == null){
                $this->showToastrMessage('error', __('Zoom Account is disabled in application_settings.'));
                return redirect()->route('classes.create_individual');
            }
            else{
                // $checkActivate =  check_and_activate_zoom_account();
                // if($checkActivate == 2){ // Means No Available Future Accounts
                //     $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
                //     return redirect()->route('classes.create_individual');
                // }
                // else if($checkActivate == 1){  // Activated
                //     // $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
                // }
                // else{
                //     // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
                // }
            }
        }
      
     
        $totalClass = $request->total_classes;
        $totalStudents = 1;
        $Duration = $request->duration;
        $Type = 'Individual';
     
        $master_record = new Lesson();
        $master_record->student_id = $request->student_id;
        $master_record->instructor_id = $request->instructor_id;
        $master_record->no_of_students = $totalStudents;
        $master_record->no_of_classes = $totalClass;
        $master_record->duration = $Duration;
        $master_record->type = $Type;
        $master_record->prefer_days = $preferdayString;
        $master_record->learning_tool = $Learning_Tool;
        $master_record->save();
        $class_id=$master_record->id;
        // $class_id = 295;

        $classDate = $request->prefer_date;
        $classTime = $request->prefer_time;
        $student_id = $request->student_id;
        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;
        $days_name = array();
        if(!empty($preferday[0]))
        {
            $day1 = $preferday[0] ;
            $days_name[] = $preferday[0] ;
        }
        if(!empty($preferday[1]))
        {
            $day2 = $preferday[1] ;
            $days_name[] = $preferday[1] ;
        }
        if(!empty($preferday[2]))
        {
            $day3 = $preferday[2] ;
            $days_name[] = $preferday[2] ;
        }
        if(!empty($preferday[3]))
        {
            $day4 = $preferday[3] ;
            $days_name[] = $preferday[3] ;
        }
        if(!empty($preferday[4]))
        {
            $day5 = $preferday[4] ;
            $days_name[] = $preferday[4] ;
        }
        if(!empty($preferday[5]))
        {
            $day6 = $preferday[5] ;
            $days_name[] = $preferday[5] ;
        }
        if(!empty($preferday[6]))
        {
            $day7 = $preferday[6] ;
            $days_name[] = $preferday[6] ;
        }
        $days_name = implode(",",$days_name);
		$timestamp = strtotime($classTime);
		$time_12h_format = date('h:i A', $timestamp);
        $end_time = strtotime($classTime) + $Duration*60; //added 40 mint in selected time
        $start_time = strtotime($classTime); //added 40 mints selected time
        $start_time_2 = Carbon::createFromTimestamp($start_time);
        $end_time_2 = Carbon::createFromTimestamp($end_time);
        $start_time_2 = $start_time_2->format('H:i');
        $end_time_2 = $end_time_2->format('H:i');
        $ClassTimeUpdated = date("h:i:sa", strtotime($classTime));
		$CurrentTimeUpdated = date('h:i:sa') ;
        $i= 0;
    
        $count = $totalClass;
        $datesforstartandend = array();//for getting the start and end date for email.
        while ($i < $count) {
          
            $lesson_date =  date('d-m-Y', strtotime($classDate)); // get date
            $day_name = date('l', strtotime($lesson_date)); //get day name like monday
            $holi_day =  date('d', strtotime($lesson_date)); //get day in number like 01
			$holi_month =  date('m', strtotime($lesson_date)); // get month in number like 01
            foreach ($preferday as $key => $day) {
                if($day_name == $day){
                    $lesson_date =  date('d-m-Y', strtotime($lesson_date));
                    $month = date('m',strtotime($lesson_date)); //change the 
					$year = date('Y',strtotime($lesson_date)); //change the format
                    $datesforstartandend[] = $lesson_date;
                    $ClassDate1 = date("d-m-Y", strtotime($lesson_date));
                    $CurrentDate1 = date('d-m-Y');
                    $classdatendtime = $ClassDate1. " " .$ClassTimeUpdated;
                    $currentdatendtime = $CurrentDate1. " " .$CurrentTimeUpdated;
                    if(strtotime($ClassDate1) > strtotime($CurrentDate1))    //Schedules Class On Next Day From Current Date
                    {
                        $classStatus = 'Scheduled';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date

                        ////// GOOGLE MEET ////////

                        if($Learning_Tool == 'Google_Meet'){
                            $instructorInfo = Instructor::where('id',$instructor_id)->first();
                            if($instructorInfo){
                                $instructor_user_id = $instructorInfo->user_id;
                            }
                            else{
                                $instructor_user_id = 0;
                            }
                        
                            $this->meetService = new GoogleMeetService(new GoogleOAuthService, new ClassDetail,$instructor_user_id);
                         
                            if (!$this->meetService->isTutorValid()) {
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Invalid tutor id passed to google meet api.'));
                                return redirect()->route('classes.create_individual');
                            }
                            else if(!$this->meetService->isTutorToken()){
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Selected Instructor Has not yet authorize with google meet with gmail address.'));
                                return redirect()->route('classes.create_individual');
                            }
                            else if(!$this->meetService->isTutorRefreshToken()){
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Selected Instructor Expired Access token not refreshed successfully.'));
                                return redirect()->route('classes.create_individual');
                            }

                            $coordinator_ids = array();
                            $coordinator_user_ids = array();
                            $courseInfo = Course::where('id',$course_id)->first();
                            $studentInfo = Student::where('id',$student_id)->first();
                    
                            $getInstructorCoordinators = InstructorCoordinator::where('instructor_id',$instructor_id)->get();
                            if($courseInfo){
                                $courseNameMeet = "Course: ".$courseInfo->title;
                            }
                            else{
                                $courseNameMeet = "CEF ONLINE Course";
                            }
                            if($studentInfo){
                                $student_user_id = $studentInfo->user_id;
                            }
                            else{
                                $student_user_id = 0;
                            }
                           
                            if(!$getInstructorCoordinators->isEmpty()){
                                foreach ($getInstructorCoordinators as $key => $ins_coor) {
                                    $coordinator_ids[] = $ins_coor->coordinator_id;
                                }
                                $coordinator_ids = array_unique($coordinator_ids);
                                $coordinatorsInfo = Coordinator::whereIn('id',$coordinator_ids)->get();
                                if(!$coordinatorsInfo->isEmpty()){
                                    foreach ($coordinatorsInfo as $key => $coor_info) {
                                        $coordinator_user_ids[] = $coor_info->user_id;
                                    }
                                    $coordinator_user_ids = array_unique($coordinator_user_ids);
                                }
                            }

                            

                            // Build base timestamps
                            $startTimeParsed = Carbon::parse($lesson_date_updated . ' ' . $start_time_2);
                            $endTimeParsed = Carbon::parse($lesson_date_updated . ' ' . $end_time_2);

                            // Fix: If end time is before or equal to start time, assume it’s on the next day
                            if ($endTimeParsed->lessThanOrEqualTo($startTimeParsed)) {
                                $endTimeParsed->addDay(); // move to next day
                            }


                            $classData = [
                                'class_id' => $class_id,
                                'student_id' => $student_id,
                                'instructor_id' => $instructor_id,
                                'course_id' => $course_id,
                                'class_date' => $lesson_date_updated,
                                'class_time' => $classTime,
                                'class_status' => $classStatus,
                                'start_time_unix' => $start_time,
                                'end_time_unix' => $end_time,
                                'start_time_2' => $start_time_2,
                                'end_time_2' => $end_time_2,

                                'title_meet' => "Individual Google Meet Class",
                                'description_meet' => "Individual Google Meet Class For ".$courseNameMeet,
                                // 'start_time_meet' => Carbon::parse($lesson_date_updated . ' ' . $start_time_2),
                                // 'end_time_meet' => Carbon::parse($lesson_date_updated . ' ' . $end_time_2),
                                'start_time_meet' => $startTimeParsed,
                                'end_time_meet' => $endTimeParsed,
                                'instructor_user_id_meet' => $instructor_user_id,
                                // 'coordinator_user_id_meet' => $request->coordinator_id,
                                'coordinator_user_ids_meet' => $coordinator_user_ids, // Mutiple Coordinators
                                'student_user_ids_meet' => [$student_user_id] // Single student
                            ];

                            $meet_classes = $this->meetService->createClass($classData);
                        }
                        else{
                            $class_detail_data = [
                                'class_id' => $class_id,
                                'student_id' => $student_id,
                                'instructor_id' => $instructor_id,
                                'course_id' => $course_id,
                                'class_date' => $lesson_date_updated,
                                'class_time' => $classTime,
                                'status' => $classStatus,
                                'start_time_unix' => $start_time,
                                'end_time_unix' => $end_time,
                                'start_time' => $start_time_2,
                                'end_time' => $end_time_2,
                            ];
                         
                            $this->classDetailModel->create($class_detail_data);
                        }
                    
                    }
                    else if(strtotime($ClassDate1) == strtotime($CurrentDate1) && strtotime($ClassTimeUpdated) >  strtotime($CurrentTimeUpdated) ) //Pending Class On Current Day
				    {
                       
                        $classStatus = 'Pending';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                     
                        if($Learning_Tool == 'Google_Meet'){
                            $instructorInfo = Instructor::where('id',$instructor_id)->first();
                            if($instructorInfo){
                                $instructor_user_id = $instructorInfo->user_id;
                            }
                            else{
                                $instructor_user_id = 0;
                            }
                        
                            $this->meetService = new GoogleMeetService(new GoogleOAuthService, new ClassDetail,$instructor_user_id);
                         
                            if (!$this->meetService->isTutorValid()) {
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Invalid tutor id passed to google meet api.'));
                                return redirect()->route('classes.create_individual');
                            }
                            else if(!$this->meetService->isTutorToken()){
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Selected Instructor Has not yet authorize with google meet with gmail address.'));
                                return redirect()->route('classes.create_individual');
                            }
                            else if(!$this->meetService->isTutorRefreshToken()){
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Selected Instructor Expired Access token not refreshed successfully.'));
                                return redirect()->route('classes.create_individual');
                            }
                          
                            $coordinator_ids = array();
                            $coordinator_user_ids = array();
                            $courseInfo = Course::where('id',$course_id)->first();
                            $studentInfo = Student::where('id',$student_id)->first();
                            $instructorInfo = Instructor::where('id',$instructor_id)->first();
                            $getInstructorCoordinators = InstructorCoordinator::where('instructor_id',$instructor_id)->get();
                            if($courseInfo){
                                $courseNameMeet = "Course: ".$courseInfo->title;
                            }
                            else{
                                $courseNameMeet = "CEF ONLINE Course";
                            }
                            if($studentInfo){
                                $student_user_id = $studentInfo->user_id;
                            }
                            else{
                                $student_user_id = 0;
                            }
                            if($instructorInfo){
                                $instructor_user_id = $instructorInfo->user_id;
                            }
                            else{
                                $instructor_user_id = 0;
                            }
                            if(!$getInstructorCoordinators->isEmpty()){
                                foreach ($getInstructorCoordinators as $key => $ins_coor) {
                                    $coordinator_ids[] = $ins_coor->coordinator_id;
                                }
                                $coordinator_ids = array_unique($coordinator_ids);
                                $coordinatorsInfo = Coordinator::whereIn('id',$coordinator_ids)->get();
                                if(!$coordinatorsInfo->isEmpty()){
                                    foreach ($coordinatorsInfo as $key => $coor_info) {
                                        $coordinator_user_ids[] = $coor_info->user_id;
                                    }
                                    $coordinator_user_ids = array_unique($coordinator_user_ids);
                                }
                            }

                            // Build base timestamps
                            $startTimeParsed = Carbon::parse($lesson_date_updated . ' ' . $start_time_2);
                            $endTimeParsed = Carbon::parse($lesson_date_updated . ' ' . $end_time_2);

                            // Fix: If end time is before or equal to start time, assume it’s on the next day
                            if ($endTimeParsed->lessThanOrEqualTo($startTimeParsed)) {
                                $endTimeParsed->addDay(); // move to next day
                            }


                            $classData = [
                                'class_id' => $class_id,
                                'student_id' => $student_id,
                                'instructor_id' => $instructor_id,
                                'course_id' => $course_id,
                                'class_date' => $lesson_date_updated,
                                'class_time' => $classTime,
                                'class_status' => $classStatus,
                                'start_time_unix' => $start_time,
                                'end_time_unix' => $end_time,
                                'start_time_2' => $start_time_2,
                                'end_time_2' => $end_time_2,
                                
                                'title_meet' => "Individual Google Meet Class",
                                'description_meet' => "Individual Google Meet Class For ".$courseNameMeet,
                                // 'start_time_meet' => Carbon::parse($lesson_date_updated . ' ' . $start_time_2),
                                // 'end_time_meet' => Carbon::parse($lesson_date_updated . ' ' . $end_time_2),
                                'start_time_meet' => $startTimeParsed,
                                'end_time_meet' => $endTimeParsed,
                                'instructor_user_id_meet' => $instructor_user_id,
                                // 'coordinator_user_id_meet' => $request->coordinator_id,
                                'coordinator_user_ids_meet' => $coordinator_user_ids, // Mutiple Coordinators
                                'student_user_ids_meet' => [$student_user_id] // Single student
                            ];
                            $meet_classes = $this->meetService->createClass($classData);
                            
                        }
                        else{
                            $class_detail_data = [
                                'class_id' => $class_id,
                                'student_id' => $student_id,
                                'instructor_id' => $instructor_id,
                                'course_id' => $course_id,
                                'class_date' => $lesson_date_updated,
                                'class_time' => $classTime,
                                'status' => $classStatus,
                                'start_time_unix' => $start_time,
                                'end_time_unix' => $end_time,
                                'start_time' => $start_time_2,
                                'end_time' => $end_time_2,
                            ];
                         
                            $this->classDetailModel->create($class_detail_data);
                        } 
                    }
                    else
				    {
                        $master_record->delete();
                        $this->showToastrMessage('error', __('You Can not assign Class in Previous Time.'));
                        return redirect()->route('classes.create_individual');
				    }
                    $i++; //add +1 when preferday found .
                }
             
            }
            $classDate = date("Y-m-d", strtotime("+1 day", strtotime($classDate)));
        }

    $findInstructor = Instructor::where('id',$instructor_id)->first();
    $findStudent = Student::where('id',$student_id)->first();
    $userName = $findStudent->user?->name;
    $studentName = $findStudent->user?->name;
    $userEmail = $findStudent->user?->email;
    $instructorName = $findInstructor->user?->name;
    $updated_12h_time=changetimeByZone($time_12h_format,$student_id,'student');
    $date_arr= $datesforstartandend;
		usort($date_arr, function($a, $b) {
			$dateTimestamp1 = strtotime($a);
			$dateTimestamp2 = strtotime($b);
			
			return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
		});
		$starting_date =  $date_arr[0];
		$ending_date =  $date_arr[count($date_arr) - 1];
    $viewData = [
        '{{USER_NAME}}' => $userName,
        // '{{TOTAL_LESSON}}' => $totalClass,
        '{{START_DATE}}' => $starting_date,
        '{{END_DATE}}' => $ending_date,
        // '{{EMAIL}}' => $userEmail,
        '{{INSTRUCTOR_NAME}}' => $instructorName,
        '{{CLASS_DAYS}}' => $days_name,
        '{{CLASS_TIME}}' => $updated_12h_time,
        '{{LEARNING_TOOL}}' => $Learning_Tool,
        // '{{BASE_URL}}' => env('APP_URL'),
    ];
 
    $emailData = getEmailTemplate('class-schedule-student', $viewData);
    if ($emailData['status']) {
       commonEmailSend($userEmail, $emailData,1);
    }
  

    $findInstructor = Instructor::where('id',$instructor_id)->first();
    if($findInstructor){
        $findInstructorUser = User::where('id',$findInstructor->user_id)->first();
        $instructorEmail = $findInstructorUser->email;
        $template_name = 'class-schedule-instructor';
        $viewData = [
            '{{USER_NAME}}' => $findInstructorUser->name,
            '{{TOTAL_LESSON}}' => $totalClass,
            '{{START_DATE}}' => $starting_date,
            '{{END_DATE}}' => $ending_date,
            '{{EMAIL}}' => $instructorEmail,
            '{{STUDENT_NAMES}}' => $studentName,
            '{{CLASS_DAYS}}' => $days_name,
            '{{CLASS_TIME}}' => $updated_12h_time,
            '{{CLASS_TYPE}}' => 'Individual',
            '{{LEARNING_TOOL}}' => $Learning_Tool,
            '{{BASE_URL}}' => env('APP_URL'),
        ];
        $emailResponse =sendEmailNotification($viewData,$instructorEmail,$template_name);
        if($emailResponse){
            $this->showToastrMessage('success', __('Individual Class Assigned Successfully and Email Notifications Sent.'));
            return redirect()->route('classes.view_individual_classes');
        }
        else{
            $this->showToastrMessage('success', __('Individual Class Assigned Successfully but Email Notifications Not Sent Due To Email Server Issue.'));
            return redirect()->route('classes.view_individual_classes');
        }
    }
    else{
        $this->showToastrMessage('success', __('Individual Class Assigned Successfully but Email Notifications Not Sent Due To Email Server Issue.'));
        return redirect()->route('classes.view_individual_classes');
    }

    }



    public function delete($id)
    {
        if (!Auth::user()->can('manage_classes')) {
            abort('403');
        } // end permission checking

        $findClassDetail = ClassDetail::where('class_id',$id)->get();
        if(!$findClassDetail->isEmpty()){
            foreach ($findClassDetail as $key => $value) {
                $value->delete();
            }
        }
        $this->classModel->delete($id);
        // $class = $this->classModel->getRecordById($id);
     
        return response()->json(['message' =>  __('Class Deleted Successfully'), 'status' => true], 200);
    }


    public function createGroup()
    {
        $checkUpdate =check_and_update_zoom_account_minutes();
        $checkActivate =check_and_activate_zoom_account();
        if($checkActivate == 2){ // Means No Available Future Accounts
            $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
        }
        else if($checkActivate == 1){  // Activated
            $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
        }
        else{
            // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
        }

        $data['title'] = 'Add Group Class';
        $userids = array();
        $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
        if(!$data['instructors']->isEmpty()){
            foreach ($data['instructors'] as $key => $value) {
                $userids[] = $value->user_id;
            }
        }
        // $data['students'] = Student::orderBy('id', 'asc')->get();
        $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();
        // dd($data['students']);


        return view('admin.classes.add_group', $data);
    }


    public function storeGroup(Request $request)
    {
        $students = $request->student_id;
        $request->validate([
            'instructor_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            'total_classes' => ['required', 'numeric'],
            'prefer_time' => ['required'],
            'prefer_date' => ['required', 'date'],
            'duration' => ['required', 'numeric'],
            'learning_tool' => ['required'],
        ]);
        if (empty($students)) {
            $this->showToastrMessage('error', __('Select At least one student.'));
            return redirect()->route('classes.create_group');
        }
        $preferday = $request->preferdays;
        $preferdayString = implode(',',$preferday);
        // dd($preferdayString );
        if (empty($preferday)) {
            $this->showToastrMessage('error', __('Select Prefer Days field is required.'));
            return redirect()->route('classes.create_group');
        }

        $Learning_Tool = $request->learning_tool;
        if($Learning_Tool == 'BBB'){
            if(get_option('bbb_status') == 0){
                $this->showToastrMessage('error', __('BigBlueButton Status is disabled in application_settings.'));
                return redirect()->route('classes.create_group');
            }
        }
        else if($Learning_Tool == 'Zoom'){
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            if($zoomSettings == null){
                $this->showToastrMessage('error', __('Zoom Account is disabled in application_settings.'));
                return redirect()->route('classes.create_group');
            }
            else{
                // $checkActivate =  check_and_activate_zoom_account();
                // if($checkActivate == 2){ // Means No Available Future Accounts
                //     $this->showToastrMessage('error', __('Please Add More Active And Valid Zoom Accounts With Minutes Available As Current Account Has Been Fully Used !!'));
                //     return redirect()->route('classes.create_group');
                // }
                // else if($checkActivate == 1){  // Activated
                //     // $this->showToastrMessage('success', __('Current Zoom Account Minutes Have Been Used. New Zoom Account Activated Now'));
                // }
                // else{
                //     // $this->showToastrMessage('success', __('Current Zoom Account Is Being Used')); 
                // }
            }
        }

        $totalStudents = 0;
            $group_id = ClassDetail::max('group_id');
            $group_id=(int)$group_id;
        	$group_id++;
            // dd($group_id);
			foreach ($students as $key => $value) {
				$totalStudents++;
			}
            $totalClass = $request->total_classes;
            $totalStudents = $totalStudents;
            $Duration = $request->duration;
            $Type = 'Group';
            $Learning_Tool = $request->learning_tool;
            $master_record = new Lesson();
            $master_record->instructor_id = $request->instructor_id;
            $master_record->no_of_students = $totalStudents;
            $master_record->no_of_classes = $totalClass;
            $master_record->duration = $Duration;
            $master_record->type = $Type;
            $master_record->prefer_days = $preferdayString;
            $master_record->learning_tool = $Learning_Tool;
            $master_record->save();
            $class_id=$master_record->id;
            $student_names_array = array();

            foreach ($students as $key => $student_id) {
                $std_id=$student_id;
                $findStudentNew = Student::where('id',$std_id)->first();
                $userNameNew = $findStudentNew->user?->name;
                $student_names_array[] = $userNameNew;

                $classDate = $request->prefer_date;
                $classTime = $request->prefer_time;
                $instructor_id = $request->instructor_id;
                $course_id = $request->course_id;
         
                $days_name = array();
                if(!empty($preferday[0]))
                {
                    $day1 = $preferday[0] ;
                    $days_name[] = $preferday[0] ;
                }
                if(!empty($preferday[1]))
                {
                    $day2 = $preferday[1] ;
                    $days_name[] = $preferday[1] ;
                }
                if(!empty($preferday[2]))
                {
                    $day3 = $preferday[2] ;
                    $days_name[] = $preferday[2] ;
                }
                if(!empty($preferday[3]))
                {
                    $day4 = $preferday[3] ;
                    $days_name[] = $preferday[3] ;
                }
                if(!empty($preferday[4]))
                {
                    $day5 = $preferday[4] ;
                    $days_name[] = $preferday[4] ;
                }
                if(!empty($preferday[5]))
                {
                    $day6 = $preferday[5] ;
                    $days_name[] = $preferday[5] ;
                }
                if(!empty($preferday[6]))
                {
                    $day7 = $preferday[6] ;
                    $days_name[] = $preferday[6] ;
                }
                $days_name = implode(",",$days_name);
                $timestamp = strtotime($classTime);
                $time_12h_format = date('h:i A', $timestamp);
         
                $end_time = strtotime($classTime) + $Duration*60; //added 40 mint in selected time
                $start_time = strtotime($classTime); //added 40 mints selected time
                $start_time_2 = Carbon::createFromTimestamp($start_time);
                $end_time_2 = Carbon::createFromTimestamp($end_time);
                $start_time_2 = $start_time_2->format('H:i');
                $end_time_2 = $end_time_2->format('H:i');
                $ClassTimeUpdated = date("h:i:sa", strtotime($classTime));
                $CurrentTimeUpdated = date('h:i:sa') ;
                $i= 0;
            
                $count = $totalClass;
                $datesforstartandend = array();//for getting the start and end date for email.
                while ($i < $count) {
                    $lesson_date =  date('d-m-Y', strtotime($classDate)); // get date
                    $day_name = date('l', strtotime($lesson_date)); //get day name like monday
                    $holi_day =  date('d', strtotime($lesson_date)); //get day in number like 01
                    $holi_month =  date('m', strtotime($lesson_date)); // get month in number like 01
                    foreach ($preferday as $key => $day) {
                        if($day_name == $day){
                            $lesson_date =  date('d-m-Y', strtotime($lesson_date));
                            $month = date('m',strtotime($lesson_date)); //change the 
                            $year = date('Y',strtotime($lesson_date)); //change the format
                            $datesforstartandend[] = $lesson_date;
                            $ClassDate1 = date("d-m-Y", strtotime($lesson_date));
                            $CurrentDate1 = date('d-m-Y');
                            $classdatendtime = $ClassDate1. " " .$ClassTimeUpdated;
                            $currentdatendtime = $CurrentDate1. " " .$CurrentTimeUpdated;
                            if(strtotime($ClassDate1) > strtotime($CurrentDate1))    //Schedules Class On Next Day From Current Date
                            {
                                $classStatus = 'Scheduled';
                                $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                                
                                  ////// GOOGLE MEET ////////

                        if($Learning_Tool == 'Google_Meet'){
                            $instructorInfo = Instructor::where('id',$instructor_id)->first();
                            if($instructorInfo){
                                $instructor_user_id = $instructorInfo->user_id;
                            }
                            else{
                                $instructor_user_id = 0;
                            }
                            $this->meetService = new GoogleMeetService(new GoogleOAuthService, new ClassDetail,$instructor_user_id);
                            
                            if (!$this->meetService->isTutorValid()) {
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Invalid tutor id passed to google meet api.'));
                                return redirect()->route('classes.create_group');
                            }
                            else if(!$this->meetService->isTutorToken()){
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Selected Instructor Has not yet authorize with google meet with gmail address.'));
                                return redirect()->route('classes.create_group');
                            }
                            else if(!$this->meetService->isTutorRefreshToken()){
                                $master_record->delete();
                                $this->showToastrMessage('error', __('Selected Instructor Expired Access token not refreshed successfully.'));
                                return redirect()->route('classes.create_group');
                            }
                            else{
                                $class_detail_data = [
                                    'class_id' => $class_id,
                                    'student_id' => $std_id,
                                    'instructor_id' => $instructor_id,
                                    'course_id' => $course_id,
                                    'group_id' => $group_id,
                                    'class_date' => $lesson_date_updated,
                                    'class_time' => $classTime,
                                    'status' => $classStatus,
                                    'start_time_unix' => $start_time,
                                    'end_time_unix' => $end_time,
                                    'start_time' => $start_time_2,
                                    'end_time' => $end_time_2,
                                ];
                             
                                $this->classDetailModel->create($class_detail_data);
                            }
                        }
                        else{
                            $class_detail_data = [
                                'class_id' => $class_id,
                                'student_id' => $std_id,
                                'instructor_id' => $instructor_id,
                                'course_id' => $course_id,
                                'group_id' => $group_id,
                                'class_date' => $lesson_date_updated,
                                'class_time' => $classTime,
                                'status' => $classStatus,
                                'start_time_unix' => $start_time,
                                'end_time_unix' => $end_time,
                                'start_time' => $start_time_2,
                                'end_time' => $end_time_2,
                            ];
                         
                            $this->classDetailModel->create($class_detail_data);
                        }
                              
                        // $class_detail_data = [
                        //     'class_id' => $class_id,
                        //     'student_id' => $std_id,
                        //     'instructor_id' => $instructor_id,
                        //     'course_id' => $course_id,
                        //     'group_id' => $group_id,
                        //     'class_date' => $lesson_date_updated,
                        //     'class_time' => $classTime,
                        //     'status' => $classStatus,
                        //     'start_time_unix' => $start_time,
                        //     'end_time_unix' => $end_time,
                        //     'start_time' => $start_time_2,
                        //     'end_time' => $end_time_2,
                        // ];
                     
                        // $this->classDetailModel->create($class_detail_data);


                            }
                            else if(strtotime($ClassDate1) == strtotime($CurrentDate1) && strtotime($ClassTimeUpdated) >  strtotime($CurrentTimeUpdated) ) //Pending Class On Current Day
                            {
                                $classStatus = 'Pending';
                                $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                                
                                if($Learning_Tool == 'Google_Meet'){
                                    $instructorInfo = Instructor::where('id',$instructor_id)->first();
                                    if($instructorInfo){
                                        $instructor_user_id = $instructorInfo->user_id;
                                    }
                                    else{
                                        $instructor_user_id = 0;
                                    }
                                    $this->meetService = new GoogleMeetService(new GoogleOAuthService, new ClassDetail,$instructor_user_id);
                                    
                                    if (!$this->meetService->isTutorValid()) {
                                        $master_record->delete();
                                        $this->showToastrMessage('error', __('Invalid tutor id passed to google meet api.'));
                                        return redirect()->route('classes.create_group');
                                    }
                                    else if(!$this->meetService->isTutorToken()){
                                        $master_record->delete();
                                        $this->showToastrMessage('error', __('Selected Instructor Has not yet authorize with google meet with gmail address.'));
                                        return redirect()->route('classes.create_group');
                                    }
                                    else if(!$this->meetService->isTutorRefreshToken()){
                                        $master_record->delete();
                                        $this->showToastrMessage('error', __('Selected Instructor Expired Access token not refreshed successfully.'));
                                        return redirect()->route('classes.create_group');
                                    }
                                    else{
                                        $class_detail_data = [
                                            'class_id' => $class_id,
                                            'student_id' => $std_id,
                                            'instructor_id' => $instructor_id,
                                            'course_id' => $course_id,
                                            'group_id' => $group_id,
                                            'class_date' => $lesson_date_updated,
                                            'class_time' => $classTime,
                                            'status' => $classStatus,
                                            'start_time_unix' => $start_time,
                                            'end_time_unix' => $end_time,
                                            'start_time' => $start_time_2,
                                            'end_time' => $end_time_2,
                                        ];
                                     
                                        $this->classDetailModel->create($class_detail_data);
                                    }
                                }
                                else{
                                    $class_detail_data = [
                                        'class_id' => $class_id,
                                        'student_id' => $std_id,
                                        'instructor_id' => $instructor_id,
                                        'course_id' => $course_id,
                                        'group_id' => $group_id,
                                        'class_date' => $lesson_date_updated,
                                        'class_time' => $classTime,
                                        'status' => $classStatus,
                                        'start_time_unix' => $start_time,
                                        'end_time_unix' => $end_time,
                                        'start_time' => $start_time_2,
                                        'end_time' => $end_time_2,
                                    ];
                                 
                                    $this->classDetailModel->create($class_detail_data);
                                }          
                            }
                            else
                            {
                                $master_record->delete();
                                $this->showToastrMessage('error', __('You Can not assign Class in Previous Time.'));
                                return redirect()->route('classes.create_group');
                            }
                            $i++; //add +1 when preferday found .
                        }
                    }
                    $classDate = date("Y-m-d", strtotime("+1 day", strtotime($classDate)));
                }



                $findInstructor = Instructor::where('id',$instructor_id)->first();
                $findStudent = Student::where('id',$std_id)->first();
                $userName = $findStudent->user?->name;
                $userEmail = $findStudent->user?->email;
                $instructorName = $findInstructor->user?->name;
                $updated_12h_time=changetimeByZone($time_12h_format,$std_id,'student');
                $date_arr= $datesforstartandend;
                    usort($date_arr, function($a, $b) {
                        $dateTimestamp1 = strtotime($a);
                        $dateTimestamp2 = strtotime($b);
                        
                        return $dateTimestamp1 < $dateTimestamp2 ? -1: 1;
                    });
                    $starting_date =  $date_arr[0];
                    $ending_date =  $date_arr[count($date_arr) - 1];
                $viewData = [
                    '{{USER_NAME}}' => $userName,
                    // '{{TOTAL_LESSON}}' => $totalClass,
                    '{{START_DATE}}' => $starting_date,
                    '{{END_DATE}}' => $ending_date,
                    // '{{EMAIL}}' => $userEmail,
                    '{{INSTRUCTOR_NAME}}' => $instructorName,
                    '{{CLASS_DAYS}}' => $days_name,
                    '{{CLASS_TIME}}' => $updated_12h_time,
                    '{{LEARNING_TOOL}}' => $Learning_Tool,
                    // '{{BASE_URL}}' => env('APP_URL'),
                ];
             
                $emailData = getEmailTemplate('class-schedule-student', $viewData);
                if ($emailData['status']) {
                   commonEmailSend($userEmail, $emailData,1);
                }



            }

            $findInstructor = Instructor::where('id',$instructor_id)->first();
            if($findInstructor){
                $student_names_string = implode(',',$student_names_array);
                $findInstructorUser = User::where('id',$findInstructor->user_id)->first();
                $instructorEmail = $findInstructorUser->email;
                $template_name = 'class-schedule-instructor';
                $viewData = [
                    '{{USER_NAME}}' => $findInstructorUser->name,
                    '{{TOTAL_LESSON}}' => $totalClass,
                    '{{START_DATE}}' => $starting_date,
                    '{{END_DATE}}' => $ending_date,
                    '{{EMAIL}}' => $instructorEmail,
                    '{{STUDENT_NAMES}}' => $student_names_string,
                    '{{CLASS_DAYS}}' => $days_name,
                    '{{CLASS_TIME}}' => $updated_12h_time,
                    '{{CLASS_TYPE}}' => 'Group',
                    '{{LEARNING_TOOL}}' => $Learning_Tool,
                    '{{BASE_URL}}' => env('APP_URL'),
                ];
                $emailResponse =sendEmailNotification($viewData,$instructorEmail,$template_name);
                if($emailResponse){
                    $this->showToastrMessage('success', __('Group Class Assigned Successfully and Email Notifications Sent.'));
                    return redirect()->route('classes.view_group_classes');
                }
                else{
                    $this->showToastrMessage('success', __('Group Class Assigned Successfully but Email Notifications Not Sent Due To Email Server Issue.'));
                    return redirect()->route('classes.view_group_classes');
                }
            }
            else{
                $this->showToastrMessage('success', __('Group Class Assigned Successfully but Email Notifications Not Sent Due To Email Server Issue.'));
                return redirect()->route('classes.view_group_classes');
            }
         
    }

    public function viewDetail($class_id){

        if (!Auth::user()->can('all_classes')) {
            abort('403');
        } 

        $data['title'] = 'Class Details';

        $data['class_details']  = ClassDetail::where('class_id',$class_id)->orderBy('id','ASC')->paginate(25);
     
        // dd($data);
        return view('admin.classes.view_details', $data);

        
    }


    public function edit($class_id){

        if (!Auth::user()->can('manage_classes')) {
            abort('403');
        }

        $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
        if(!$data['instructors']->isEmpty()){
            foreach ($data['instructors'] as $key => $value) {
                $userids[] = $value->user_id;
            }
        }
        // $data['students'] = Student::orderBy('id', 'asc')->get();
        $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();

        $findClassType = Lesson::where('id',$class_id)->first();
        $data['class']  =   $findClassType;
        $data['class_id']  = $class_id;
        if($findClassType != null){
            $classType = $findClassType->type;
        }
        if($classType == "Individual"){
            $data['title'] = 'Edit Individual Class';
        }
        else{
            $data['title'] = 'Edit Group Class';
        }
        $currentDate = Carbon::now()->toDateString();
        $data['currentDate'] =  $currentDate;
        $prederDays = $findClassType->prefer_days;
        $prederDaysArray = explode(',',$prederDays);
        $data['class_type']  = $classType;
        $data['class_detail']  = ClassDetail::where('class_id',$class_id)->first();
        $old_instructor_id =$data['class_detail']->instructor_id;
        $detail  = ClassDetail::where('class_id',$class_id)->get();
        foreach ($detail as $key => $d) {
                $student1[]=$d->student_id;
        }
        $data['prefer_day_array']  = $prederDaysArray;
        $student1= array_unique($student1);
        $data['student1']  = $student1;


        $instructor = Instructor::where('id',$old_instructor_id)->first();
        if($instructor){
            $instructor_user_id = $instructor->user_id;
        }
        else{
            $instructor_user_id = 0;
        }
        $instructorCourses = CourseInstructor::where('instructor_id',$instructor_user_id)->get();
        if(!$instructorCourses->isEmpty()){
            foreach ($instructorCourses as $key => $value) {
                $courseids[] = $value->course_id;
            }
            $courseids = array_unique($courseids);
        }
        $courses = Course::whereIn('id',$courseids)->where('status',1)->get();
        $data['courses'] = $courses;
    

        if($classType == "Individual"){
            return view('admin.classes.edit_individual', $data);
        }
        else{
            return view('admin.classes.edit_group', $data);
        }
        
    }






    public function editFreeClass($class_id){

        if (!Auth::user()->can('manage_classes')) {
            abort('403');
        }

        $data['instructors'] = Instructor::orderBy('id', 'asc')->get();
        if(!$data['instructors']->isEmpty()){
            foreach ($data['instructors'] as $key => $value) {
                $userids[] = $value->user_id;
            }
        }

      
      
      
     
   
    
        // $givenDate = Carbon::createFromFormat('Y-m-d', $getFreeClass->prefer_date);
        // $data['given_lesson_time'] = $getFreeClass->prefer_time;
        // $data['students'] = Student::orderBy('id', 'asc')->get();
        $data['students'] = Student::whereNotIn('user_id',$userids)->orderBy('id', 'asc')->get();

        $findClassType = Lesson::where('id',$class_id)->first();
        $data['given_student_id'] =  $findClassType->student_id;
        $data['given_student_name'] =  $findClassType->student->name;


        $data['class']  =   $findClassType;
        $data['class_id']  = $class_id;
        if($findClassType != null){
            $classType = $findClassType->type;
        }
        $data['title'] = 'Edit Free Class';
      
        $currentDate = Carbon::now()->toDateString();
        $data['currentDate'] =  $currentDate;
        $prederDays = $findClassType->prefer_days;
        $prederDaysArray = explode(',',$prederDays);
        $data['class_type']  = $classType;
        $data['class_detail']  = ClassDetail::where('class_id',$class_id)->first();

        $givenDate = Carbon::createFromFormat('Y-m-d', $data['class_detail']->class_date);
        $data['given_lesson_date'] = $data['class_detail']->class_date;

        $old_instructor_id =$data['class_detail']->instructor_id;
        $detail  = ClassDetail::where('class_id',$class_id)->get();
        foreach ($detail as $key => $d) {
                $student1[]=$d->student_id;
        }
        $data['prefer_day_array']  = $prederDaysArray;
        $student1= array_unique($student1);
        $data['student1']  = $student1;


        $instructor = Instructor::where('id',$old_instructor_id)->first();
        if($instructor){
            $instructor_user_id = $instructor->user_id;
        }
        else{
            $instructor_user_id = 0;
        }
        $instructorCourses = CourseInstructor::where('instructor_id',$instructor_user_id)->get();
        if(!$instructorCourses->isEmpty()){
            foreach ($instructorCourses as $key => $value) {
                $courseids[] = $value->course_id;
            }
            $courseids = array_unique($courseids);
        }
        $courses = Course::whereIn('id',$courseids)->where('status',1)->get();
        $data['courses'] = $courses;
    
        return view('admin.classes.edit_free_class_individual', $data);
        // if($classType == "Individual"){
        //     return view('admin.classes.edit_individual', $data);
        // }
        // else{
        //     return view('admin.classes.edit_group', $data);
        // }
        
    }






    
    public function updateFreeClassIndividual(Request $request,$id)
    {
        $given_class_id = $id;
        $class_detail_id = 0;

        $request->validate([
            'student_id' => ['required', 'numeric'],
            'instructor_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            // 'total_classes' => ['required', 'numeric'],
            'prefer_time' => ['required'],
            'prefer_date' => ['required', 'date'],
            'duration' => ['required', 'numeric'],
            'learning_tool' => ['required'],
        ]);
        $preferday = $request->preferdays;
        $preferdayString = implode(',',$preferday);
        if (empty($preferday)) {
            $this->showToastrMessage('error', __('Select Prefer Days field is required.'));
            return redirect()->route('classes.edit',['id'=>$given_class_id]);
        }

        $Learning_Tool = $request->learning_tool;
        if($Learning_Tool == 'BBB'){
            if(get_option('bbb_status') == 0){
                $this->showToastrMessage('error', __('BigBlueButton Status is disabled in application_settings.'));
                return redirect()->route('classes.edit',['id'=>$given_class_id]);
            }
        }
        else if($Learning_Tool == 'Zoom'){
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            if($zoomSettings == null){
                $this->showToastrMessage('error', __('Zoom Account is disabled in application_settings.'));
                return redirect()->route('classes.edit',['id'=>$given_class_id]);
            }
        }
        
        // $totalClass = $request->total_classes;
        $totalClass = 1;
        $totalStudents = 1;
        $Duration = $request->duration;
        $Type = 'Individual';
        $Learning_Tool = $request->learning_tool;
        $master_record = new Lesson();
        $master_record->no_of_students = $totalStudents;
        $master_record->no_of_classes = $totalClass;
        $master_record->student_id = $request->student_id;
        $master_record->instructor_id = $request->instructor_id;
        $master_record->duration = $Duration;
        $master_record->type = $Type;
        $master_record->prefer_days = $preferdayString;
        $master_record->free_class = 1;
        $master_record->learning_tool = $Learning_Tool;
        $master_record->save();
        $class_id=$master_record->id;

        $classDate = $request->prefer_date;
        $classTime = $request->prefer_time;
        $student_id = $request->student_id;
        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;
        $days_name = array();
        if(!empty($preferday[0]))
        {
            $day1 = $preferday[0] ;
            $days_name[] = $preferday[0] ;
        }
        if(!empty($preferday[1]))
        {
            $day2 = $preferday[1] ;
            $days_name[] = $preferday[1] ;
        }
        if(!empty($preferday[2]))
        {
            $day3 = $preferday[2] ;
            $days_name[] = $preferday[2] ;
        }
        if(!empty($preferday[3]))
        {
            $day4 = $preferday[3] ;
            $days_name[] = $preferday[3] ;
        }
        if(!empty($preferday[4]))
        {
            $day5 = $preferday[4] ;
            $days_name[] = $preferday[4] ;
        }
        if(!empty($preferday[5]))
        {
            $day6 = $preferday[5] ;
            $days_name[] = $preferday[5] ;
        }
        if(!empty($preferday[6]))
        {
            $day7 = $preferday[6] ;
            $days_name[] = $preferday[6] ;
        }
        $days_name = implode(",",$days_name);
		$timestamp = strtotime($classTime);
		$time_12h_format = date('h:i A', $timestamp);
        $end_time = strtotime($classTime) + $Duration*60; //added 40 mint in selected time
        $start_time = strtotime($classTime); //added 40 mints selected time
        $start_time_2 = Carbon::createFromTimestamp($start_time);
        $end_time_2 = Carbon::createFromTimestamp($end_time);
        $start_time_2 = $start_time_2->format('H:i');
        $end_time_2 = $end_time_2->format('H:i');
        $ClassTimeUpdated = date("h:i:sa", strtotime($classTime));
		$CurrentTimeUpdated = date('h:i:sa') ;
        $i= 0;
    
        $count = $totalClass;
        $datesforstartandend = array();//for getting the start and end date for email.
        while ($i < $count) {
          
            $lesson_date =  date('d-m-Y', strtotime($classDate)); // get date
            $day_name = date('l', strtotime($lesson_date)); //get day name like monday
            $holi_day =  date('d', strtotime($lesson_date)); //get day in number like 01
			$holi_month =  date('m', strtotime($lesson_date)); // get month in number like 01
            foreach ($preferday as $key => $day) {
                if($day_name == $day){
                    $lesson_date =  date('d-m-Y', strtotime($lesson_date));
                    $month = date('m',strtotime($lesson_date)); //change the 
					$year = date('Y',strtotime($lesson_date)); //change the format
                    $datesforstartandend[] = $lesson_date;
                    $ClassDate1 = date("d-m-Y", strtotime($lesson_date));
                    $CurrentDate1 = date('d-m-Y');
                    $classdatendtime = $ClassDate1. " " .$ClassTimeUpdated;
                    $currentdatendtime = $CurrentDate1. " " .$CurrentTimeUpdated;
                    if(strtotime($ClassDate1) > strtotime($CurrentDate1))    //Schedules Class On Next Day From Current Date
                    {
                        $classStatus = 'Scheduled';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                        $class_detail_data = [
                            'class_id' => $class_id,
                            'student_id' => $student_id,
                            'instructor_id' => $instructor_id,
                            'course_id' => $course_id,
                            'class_date' => $lesson_date_updated,
                            'class_time' => $classTime,
                            'status' => $classStatus,
                            'start_time_unix' => $start_time,
                            'end_time_unix' => $end_time,
                            'start_time' => $start_time_2,
                            'end_time' => $end_time_2,
                        ];
                     
                       $class_detail_id= $this->classDetailModel->create($class_detail_data);
                    }
                    else if(strtotime($ClassDate1) == strtotime($CurrentDate1) && strtotime($ClassTimeUpdated) >  strtotime($CurrentTimeUpdated) ) //Pending Class On Current Day
				    {
                       
                        $classStatus = 'Pending';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                        $class_detail_data = [
                            'class_id' => $class_id,
                            'student_id' => $student_id,
                            'instructor_id' => $instructor_id,
                            'course_id' => $course_id,
                            'class_date' => $lesson_date_updated,
                            'class_time' => $classTime,
                            'status' => $classStatus,
                            'start_time_unix' => $start_time,
                            'end_time_unix' => $end_time,
                            'start_time' => $start_time_2,
                            'end_time' => $end_time_2,
                        ];
                     
                       $class_detail_id = $this->classDetailModel->create($class_detail_data);
                     
                    
                    }
                    else
				    {
                        $this->classModel->delete($class_id);
                        $this->showToastrMessage('error', __('You Can not update Class in Previous Time.'));
                        return redirect()->route('classes.edit',['id'=>$given_class_id]);
				    }
                    $i++; //add +1 when preferday found .
                }
             
            }
            $classDate = date("Y-m-d", strtotime("+1 day", strtotime($classDate)));
        }
        if($class_id  && $class_detail_id){
            $findClassDetail = ClassDetail::where('class_id',$given_class_id)->get();
            if(!$findClassDetail->isEmpty()){
                foreach ($findClassDetail as $key => $value) {
                    $value->delete();
                }
            }
            $this->classModel->delete($given_class_id);
        }
        
        $this->showToastrMessage('success', __('Free Class Updated Successfully.'));
        return redirect()->route('classes.view_individual_classes');
    
    }



    public function updateGroup(Request $request,$id)
    {
        $given_class_id = $id;
        $students = $request->student_id;
        $class_detail_id = 0;
        $request->validate([
            'instructor_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            'total_classes' => ['required', 'numeric'],
            'prefer_time' => ['required'],
            'prefer_date' => ['required', 'date'],
            'duration' => ['required', 'numeric'],
            'learning_tool' => ['required'],
        ]);
        if (empty($students)) {
            $this->showToastrMessage('error', __('Select At least one student.'));
            return redirect()->route('classes.edit',['id'=>$given_class_id]);
        }
        $preferday = $request->preferdays;
        $preferdayString = implode(',',$preferday);
        // dd($preferdayString );
        if (empty($preferday)) {
            $this->showToastrMessage('error', __('Select Prefer Days field is required.'));
            return redirect()->route('classes.edit',['id'=>$given_class_id]);
        }

        $Learning_Tool = $request->learning_tool;
        if($Learning_Tool == 'BBB'){
            if(get_option('bbb_status') == 0){
                $this->showToastrMessage('error', __('BigBlueButton Status is disabled in application_settings.'));
                return redirect()->route('classes.edit',['id'=>$given_class_id]);
            }
        }
        else if($Learning_Tool == 'Zoom'){
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            if($zoomSettings == null){
                $this->showToastrMessage('error', __('Zoom Account is disabled in application_settings.'));
                return redirect()->route('classes.edit',['id'=>$given_class_id]);
            }
        }

        $totalStudents = 0;
  

     
			foreach ($students as $key => $value) {
				$totalStudents++;
			}
            $totalClass = $request->total_classes;
            $totalStudents = $totalStudents;
            $Duration = $request->duration;
            $Type = 'Group';
            $Learning_Tool = $request->learning_tool;
            $master_record = new Lesson();
            $master_record->no_of_students = $totalStudents;
            $master_record->instructor_id = $request->instructor_id;
            $master_record->no_of_classes = $totalClass;
            $master_record->duration = $Duration;
            $master_record->type = $Type;
            $master_record->prefer_days = $preferdayString;
            $master_record->learning_tool = $Learning_Tool;
            $master_record->save();
            $class_id=$master_record->id;

            foreach ($students as $key => $student_id) {
                $std_id=$student_id;
                $classDate = $request->prefer_date;
                $classTime = $request->prefer_time;
                $instructor_id = $request->instructor_id;
                $course_id = $request->course_id;
         
                $days_name = array();
                if(!empty($preferday[0]))
                {
                    $day1 = $preferday[0] ;
                    $days_name[] = $preferday[0] ;
                }
                if(!empty($preferday[1]))
                {
                    $day2 = $preferday[1] ;
                    $days_name[] = $preferday[1] ;
                }
                if(!empty($preferday[2]))
                {
                    $day3 = $preferday[2] ;
                    $days_name[] = $preferday[2] ;
                }
                if(!empty($preferday[3]))
                {
                    $day4 = $preferday[3] ;
                    $days_name[] = $preferday[3] ;
                }
                if(!empty($preferday[4]))
                {
                    $day5 = $preferday[4] ;
                    $days_name[] = $preferday[4] ;
                }
                if(!empty($preferday[5]))
                {
                    $day6 = $preferday[5] ;
                    $days_name[] = $preferday[5] ;
                }
                if(!empty($preferday[6]))
                {
                    $day7 = $preferday[6] ;
                    $days_name[] = $preferday[6] ;
                }
                $days_name = implode(",",$days_name);
                $timestamp = strtotime($classTime);
                $time_12h_format = date('h:i A', $timestamp);
         
                $end_time = strtotime($classTime) + $Duration*60; //added 40 mint in selected time
                $start_time = strtotime($classTime); //added 40 mints selected time
                $start_time_2 = Carbon::createFromTimestamp($start_time);
                $end_time_2 = Carbon::createFromTimestamp($end_time);
                $start_time_2 = $start_time_2->format('H:i');
                $end_time_2 = $end_time_2->format('H:i');
                $ClassTimeUpdated = date("h:i:sa", strtotime($classTime));
                $CurrentTimeUpdated = date('h:i:sa') ;
                $i= 0;
            
                $count = $totalClass;
                $datesforstartandend = array();//for getting the start and end date for email.
                while ($i < $count) {
                    $lesson_date =  date('d-m-Y', strtotime($classDate)); // get date
                    $day_name = date('l', strtotime($lesson_date)); //get day name like monday
                    $holi_day =  date('d', strtotime($lesson_date)); //get day in number like 01
                    $holi_month =  date('m', strtotime($lesson_date)); // get month in number like 01
                    foreach ($preferday as $key => $day) {
                        if($day_name == $day){
                            $lesson_date =  date('d-m-Y', strtotime($lesson_date));
                            $month = date('m',strtotime($lesson_date)); //change the 
                            $year = date('Y',strtotime($lesson_date)); //change the format
                            $datesforstartandend[] = $lesson_date;
                            $ClassDate1 = date("d-m-Y", strtotime($lesson_date));
                            $CurrentDate1 = date('d-m-Y');
                            $classdatendtime = $ClassDate1. " " .$ClassTimeUpdated;
                            $currentdatendtime = $CurrentDate1. " " .$CurrentTimeUpdated;
                            if(strtotime($ClassDate1) > strtotime($CurrentDate1))    //Schedules Class On Next Day From Current Date
                            {
                                $classStatus = 'Scheduled';
                                $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                                $class_detail_data = [
                                    'class_id' => $class_id,
                                    'student_id' => $std_id,
                                    'instructor_id' => $instructor_id,
                                    'course_id' => $course_id,
                                    'class_date' => $lesson_date_updated,
                                    'class_time' => $classTime,
                                    'status' => $classStatus,
                                    'start_time_unix' => $start_time,
                                    'end_time_unix' => $end_time,
                                    'start_time' => $start_time_2,
                                    'end_time' => $end_time_2,
                                ];
                             
                                $class_detail_id =  $this->classDetailModel->create($class_detail_data);

                            }
                            else if(strtotime($ClassDate1) == strtotime($CurrentDate1) && strtotime($ClassTimeUpdated) >  strtotime($CurrentTimeUpdated) ) //Pending Class On Current Day
                            {
                                $classStatus = 'Pending';
                                $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                                $class_detail_data = [
                                    'class_id' => $class_id,
                                    'student_id' => $std_id,
                                    'instructor_id' => $instructor_id,
                                    'course_id' => $course_id,
                                    'class_date' => $lesson_date_updated,
                                    'class_time' => $classTime,
                                    'status' => $classStatus,
                                    'start_time_unix' => $start_time,
                                    'end_time_unix' => $end_time,
                                    'start_time' => $start_time_2,
                                    'end_time' => $end_time_2,
                                ];
                             
                                $class_detail_id =$this->classDetailModel->create($class_detail_data);
                             
                            
                            }
                            else
                            {
                                $this->classModel->delete($class_id);
                                $this->showToastrMessage('error', __('You Can not update Class in Previous Time.'));
                                return redirect()->route('classes.edit',['id'=>$given_class_id]);
                            }
                            $i++; //add +1 when preferday found .
                        }
                    }
                    $classDate = date("Y-m-d", strtotime("+1 day", strtotime($classDate)));
                }
            }

    if($class_id  && $class_detail_id){
        $findClassDetail = ClassDetail::where('class_id',$given_class_id)->get();
        if(!$findClassDetail->isEmpty()){
            foreach ($findClassDetail as $key => $value) {
                $value->delete();
            }
        }
        $this->classModel->delete($given_class_id);
    }
            $this->showToastrMessage('success', __('Group Class Updated Successfully.'));
            return redirect()->route('classes.view_group_classes');
    }



    public function updateIndividual(Request $request,$id)
    {
        $given_class_id = $id;
        $class_detail_id = 0;

        $request->validate([
            'student_id' => ['required', 'numeric'],
            'instructor_id' => ['required', 'numeric'],
            'course_id' => ['required', 'numeric'],
            'total_classes' => ['required', 'numeric'],
            'prefer_time' => ['required'],
            'prefer_date' => ['required', 'date'],
            'duration' => ['required', 'numeric'],
            'learning_tool' => ['required'],
        ]);
        $preferday = $request->preferdays;
        $preferdayString = implode(',',$preferday);
        if (empty($preferday)) {
            $this->showToastrMessage('error', __('Select Prefer Days field is required.'));
            return redirect()->route('classes.edit',['id'=>$given_class_id]);
        }
        $Learning_Tool = $request->learning_tool;
        if($Learning_Tool == 'BBB'){
            if(get_option('bbb_status') == 0){
                $this->showToastrMessage('error', __('BigBlueButton Status is disabled in application_settings.'));
                return redirect()->route('classes.edit',['id'=>$given_class_id]);
            }
        }
        else if($Learning_Tool == 'Zoom'){
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            if($zoomSettings == null){
                $this->showToastrMessage('error', __('Zoom Account is disabled in application_settings.'));
                return redirect()->route('classes.edit',['id'=>$given_class_id]);
            }
        }
        $totalClass = $request->total_classes;
        $totalStudents = 1;
        $Duration = $request->duration;
        $Type = 'Individual';
        $Learning_Tool = $request->learning_tool;
        $master_record = new Lesson();
        $master_record->no_of_students = $totalStudents;
        $master_record->student_id = $request->student_id;
        $master_record->instructor_id = $request->instructor_id;
        $master_record->no_of_classes = $totalClass;
        $master_record->duration = $Duration;
        $master_record->type = $Type;
        $master_record->prefer_days = $preferdayString;
        $master_record->learning_tool = $Learning_Tool;
        $master_record->save();
        $class_id=$master_record->id;

        $classDate = $request->prefer_date;
        $classTime = $request->prefer_time;
        $student_id = $request->student_id;
        $course_id = $request->course_id;
        $instructor_id = $request->instructor_id;
        $days_name = array();
        if(!empty($preferday[0]))
        {
            $day1 = $preferday[0] ;
            $days_name[] = $preferday[0] ;
        }
        if(!empty($preferday[1]))
        {
            $day2 = $preferday[1] ;
            $days_name[] = $preferday[1] ;
        }
        if(!empty($preferday[2]))
        {
            $day3 = $preferday[2] ;
            $days_name[] = $preferday[2] ;
        }
        if(!empty($preferday[3]))
        {
            $day4 = $preferday[3] ;
            $days_name[] = $preferday[3] ;
        }
        if(!empty($preferday[4]))
        {
            $day5 = $preferday[4] ;
            $days_name[] = $preferday[4] ;
        }
        if(!empty($preferday[5]))
        {
            $day6 = $preferday[5] ;
            $days_name[] = $preferday[5] ;
        }
        if(!empty($preferday[6]))
        {
            $day7 = $preferday[6] ;
            $days_name[] = $preferday[6] ;
        }
        $days_name = implode(",",$days_name);
		$timestamp = strtotime($classTime);
		$time_12h_format = date('h:i A', $timestamp);
        $end_time = strtotime($classTime) + $Duration*60; //added 40 mint in selected time
        $start_time = strtotime($classTime); //added 40 mints selected time
        $start_time_2 = Carbon::createFromTimestamp($start_time);
        $end_time_2 = Carbon::createFromTimestamp($end_time);
        $start_time_2 = $start_time_2->format('H:i');
        $end_time_2 = $end_time_2->format('H:i');
        $ClassTimeUpdated = date("h:i:sa", strtotime($classTime));
		$CurrentTimeUpdated = date('h:i:sa') ;
        $i= 0;
    
        $count = $totalClass;
        $datesforstartandend = array();//for getting the start and end date for email.
        while ($i < $count) {
          
            $lesson_date =  date('d-m-Y', strtotime($classDate)); // get date
            $day_name = date('l', strtotime($lesson_date)); //get day name like monday
            $holi_day =  date('d', strtotime($lesson_date)); //get day in number like 01
			$holi_month =  date('m', strtotime($lesson_date)); // get month in number like 01
            foreach ($preferday as $key => $day) {
                if($day_name == $day){
                    $lesson_date =  date('d-m-Y', strtotime($lesson_date));
                    $month = date('m',strtotime($lesson_date)); //change the 
					$year = date('Y',strtotime($lesson_date)); //change the format
                    $datesforstartandend[] = $lesson_date;
                    $ClassDate1 = date("d-m-Y", strtotime($lesson_date));
                    $CurrentDate1 = date('d-m-Y');
                    $classdatendtime = $ClassDate1. " " .$ClassTimeUpdated;
                    $currentdatendtime = $CurrentDate1. " " .$CurrentTimeUpdated;
                    if(strtotime($ClassDate1) > strtotime($CurrentDate1))    //Schedules Class On Next Day From Current Date
                    {
                        $classStatus = 'Scheduled';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                        $class_detail_data = [
                            'class_id' => $class_id,
                            'student_id' => $student_id,
                            'instructor_id' => $instructor_id,
                            'course_id' => $course_id,
                            'class_date' => $lesson_date_updated,
                            'class_time' => $classTime,
                            'status' => $classStatus,
                            'start_time_unix' => $start_time,
                            'end_time_unix' => $end_time,
                            'start_time' => $start_time_2,
                            'end_time' => $end_time_2,
                        ];
                     
                       $class_detail_id= $this->classDetailModel->create($class_detail_data);
                    }
                    else if(strtotime($ClassDate1) == strtotime($CurrentDate1) && strtotime($ClassTimeUpdated) >  strtotime($CurrentTimeUpdated) ) //Pending Class On Current Day
				    {
                       
                        $classStatus = 'Pending';
                        $lesson_date_updated = date('Y-m-d', strtotime($lesson_date)); // get date
                        $class_detail_data = [
                            'class_id' => $class_id,
                            'student_id' => $student_id,
                            'instructor_id' => $instructor_id,
                            'course_id' => $course_id,
                            'class_date' => $lesson_date_updated,
                            'class_time' => $classTime,
                            'status' => $classStatus,
                            'start_time_unix' => $start_time,
                            'end_time_unix' => $end_time,
                            'start_time' => $start_time_2,
                            'end_time' => $end_time_2,
                        ];
                     
                       $class_detail_id = $this->classDetailModel->create($class_detail_data);
                     
                    
                    }
                    else
				    {
                        $this->classModel->delete($class_id);
                        $this->showToastrMessage('error', __('You Can not update Class in Previous Time.'));
                        return redirect()->route('classes.edit',['id'=>$given_class_id]);
				    }
                    $i++; //add +1 when preferday found .
                }
             
            }
            $classDate = date("Y-m-d", strtotime("+1 day", strtotime($classDate)));
        }
        if($class_id  && $class_detail_id){
            $findClassDetail = ClassDetail::where('class_id',$given_class_id)->get();
            if(!$findClassDetail->isEmpty()){
                foreach ($findClassDetail as $key => $value) {
                    $value->delete();
                }
            }
            $this->classModel->delete($given_class_id);
        }
        
        $this->showToastrMessage('success', __('Individual Class Updated Successfully.'));
        return redirect()->route('classes.view_individual_classes');
    
    }
}
