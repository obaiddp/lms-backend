<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Country;
use App\Models\Enrollment;
use App\Models\Instructor;
use App\Models\ZoomSetting;
use App\Models\AffiliateHistory;
use App\Models\BookingHistory;
use App\Models\RankingLevel;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Lesson;
use App\Models\BlogTag;
use App\Models\ClassDetail;
use App\Models\Order;
use App\Models\InstructorCoordinator;
use App\Models\Order_item;
use App\Models\State;
use App\Models\Course;
use App\Models\CoordinatorCourse;
use App\Models\Coordinator;
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
use Ramsey\Uuid\Uuid;

class CoordinatorController extends Controller
{
    use General, ImageSaveTrait;

    protected $coordinatorModel;
    public function __construct( Coordinator $coordinator)
    {
        $this->bb=new Bbb();
        $this->coordinatorModel = new Crud($coordinator);
    }

    public function index()
    {
        if (!Auth::user()->can('all_coordinator')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Coordinators';
        $data['coordinators'] = $this->coordinatorModel->getOrderById('DESC', 25);
      
        return view('admin.coordinator.index', $data);
    }

    public function view($uuid)
    {
        $total_instructors = 0;
        $published_courses = 0;
        $pending_courses = 0;
        $instructor_ids = array();
        $data['title'] = 'Coordinator Profile';
        $data['coordinator'] = $this->coordinatorModel->getRecordByUuid($uuid);
        $coordinatorID = $data['coordinator']->id;
        $checkTutors = InstructorCoordinator::where('coordinator_id',$coordinatorID)->get();
        if(!$checkTutors->isEmpty()){
            foreach ($checkTutors as $key => $value) {
                $total_instructors += 1;
                $instructor_ids[] = (int)$value->instructor_id;
            }
        }
        if(!empty($instructor_ids)){
            $instructors = Instructor::whereIn('id',$instructor_ids)->get();
            if(!$instructors->isEmpty()){
                foreach ($instructors as $key => $value2) {
                    $published_courses += $value2->publishedCourses->count();
                    $pending_courses += $value2->pendingCourses->count();
                }
            }
        }
        $data['total_instructors'] = $total_instructors;
        $data['published_courses'] = $published_courses;
        $data['pending_courses'] = $pending_courses;
        // dd($pending_courses);
        // $userCourseIds = Course::whereUserId($data['instructor']->user->id)->pluck('id')->toArray();
        // if (count($userCourseIds) > 0){
        //     $orderItems = Order_item::whereIn('course_id', $userCourseIds)
        //         ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
        //         ->whereHas('order', function ($q) {
        //             $q->where('payment_status', 'paid');
        //         });
        //     $data['total_earning'] = $orderItems->sum('owner_balance');
        // }

        return view('admin.coordinator.view', $data);
    }


    public function create()
    {
        $data['title'] = 'Add Coordinator';
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id')) {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id')) {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        return view('admin.coordinator.add', $data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'professional_title' => 'required',
            'area_code' => 'required',
            'phone_number' => 'bail|numeric|unique:users,mobile_number',
            'address' => 'required',
            'gender' => 'required',
            'about_me' => 'required',
            'image' => 'mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
        ]);

        $user = new User();
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->email_verified_at = now();
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->mobile_number = $request->phone_number;
        $user->phone_number = $request->phone_number;
        $user->password = Hash::make($request->password);
        $user->role = 5;
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   null;
        $user->save();

        if (Coordinator::where('slug', getSlug($user->name))->count() > 0)
        {
            $slug = getSlug($user->name) . '-'. rand(100000, 999999);
        } else {
            $slug = getSlug($user->name);
        }


        $coordinator_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'professional_title' => $request->professional_title,
            'phone_number' => $user->phone_number,
            'slug' => $slug,
            'status' => 1,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
            'social_link' => json_encode($request->social_link),
        ];

        $this->coordinatorModel->create($coordinator_data);

        $this->showToastrMessage('success', __('Coordinator created successfully'));
        return redirect()->route('coordinator.index');
    }


    public function edit($uuid)
    {
        $data['title'] = 'Edit Coordinator';
        $data['coordinator'] = $this->coordinatorModel->getRecordByUuid($uuid);
        $data['user'] = User::findOrfail($data['coordinator']->user_id);

        $data['courses'] = Course::where('course_type',3)->get();
        $checkCourses = CoordinatorCourse::where('coordinator_id',$data['coordinator']->id)->get();

        if(!$checkCourses->isEmpty()){
            foreach ($checkCourses as $key => $checkCourse) {
                $courses[]=$checkCourse->course_id;
            }
            $courses= array_unique($courses);
        }
        else{
            $courses = []; 
        }
        $data['check_courses'] = $courses;

        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id'))
        {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id'))
        {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        return view('admin.coordinator.edit', $data);
    }



    public function update(Request $request, $uuid)
    {
        $coordinator = $this->coordinatorModel->getRecordByUuid($uuid);
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$coordinator->user_id],
            'professional_title' => 'required',
            'area_code' => 'required',
            'phone_number' => 'bail|numeric|unique:users,mobile_number,'.$coordinator->user_id,
            'time_zone' => 'required',
            'address' => 'required',
            'gender' => 'required',
            'about_me' => 'required',
            'image' => 'mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
        ]);


        $user = User::findOrfail($coordinator->user_id);
        if (User::where('id', '!=', $coordinator->user_id)->where('email', $request->email)->count() > 0) {
            $this->showToastrMessage('warning', __('Email already exist'));
            return redirect()->back();
        }

        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->mobile_number = $request->phone_number;
        $user->phone_number = $request->phone_number;
        $user->time_zone =  $request->time_zone;
        if ($request->password){
            $request->validate([
                'password' => 'required|string|min:6'
            ]);
            $user->password = Hash::make($request->password);
        }
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   $user->image;
        $user->save();

        if (Coordinator::where('slug', getSlug($user->name))->count() > 0)
        {
            $slug = getSlug($user->name) . '-'. rand(100000, 999999);
        } else {
            $slug = getSlug($user->name);
        }


        $course_ids = $request->cour_id;
        if(!empty($course_ids)){
            $checkExisting = CoordinatorCourse::where('coordinator_id',$coordinator->id)->first();
            if($checkExisting){
                $deleteExisting = CoordinatorCourse::where('coordinator_id',$coordinator->id)->delete();
            }
            foreach ($course_ids as $key => $id) {
            
                $newICData = new CoordinatorCourse;
                $newICData->course_id = (int)$id;
                $newICData->coordinator_id = $coordinator->id;
                $newICData->created_at = Carbon::now();
                $newICData->save();
            }
          
        }


        $coordinator_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'professional_title' => $request->professional_title,
            'phone_number' => $user->phone_number,
            'slug' => $slug,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
            'social_link' => json_encode($request->social_link),
        ];

        $this->coordinatorModel->updateByUuid($coordinator_data, $uuid);

        $this->showToastrMessage('success', __('Updated Successfully'));
        return redirect()->route('coordinator.index');
    }


    public function changeCoordinatorStatus(Request $request)
    {
        // dd("123");
        $coordinator = Coordinator::findOrFail($request->id);
        $coordinator->status = $request->status;
        $coordinator->save();

        return response()->json([
            'data' => 'success',
        ]);
    }


    public function delete($id)
    {
        if (!Auth::user()->can('manage_coordinator')) {
            abort('403');
        } // end permission checking

        $coordinator = $this->coordinatorModel->getRecordById($id);
        $user = User::findOrfail($coordinator->user_id);

        // if ($coordinator && $user){
        //     //Start:: Course Delete
        //     $courses = Course::whereUserId($user->id)->get();
        //     if(count($courses)){
        //         return response()->json(['message' =>  __('This user have courses. Please delete those course before delete the user.'), 'status' => false], 200);
        //     }

        // }
        $this->coordinatorModel->delete($id);

        // $user->role = 4;
        $user->delete();

        return response()->json(['message' =>  __('Coordinator Deleted Successfully'), 'status' => true], 200);
    }


    public function changeAutoContentStatus(Request $request)
    {
      
        $coordinator = Coordinator::findOrFail($request->id);
        $coordinator->auto_content_approval = $request->auto_content_approval;
        $coordinator->save();

        return response()->json([
            'data' => 'success',
        ]);
    }


    public function getStateByCountry($country_id)
    {
        return State::where('country_id', $country_id)->orderBy('name', 'asc')->get()->toJson();
    }

    public function getCityByState($state_id)
    {
        return City::where('state_id', $state_id)->orderBy('name', 'asc')->get()->toJson();
    }


    public function blocked()
    {
        if (!Auth::user()->can('blocked_coordinator')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Blocked Coordinators';
        $data['coordinators'] = Coordinator::blocked()->orderBy('id', 'desc')->paginate(25);
        return view('admin.coordinator.blocked', $data);
    }

   
    
    public function classSchedule() {
        $data['title'] = 'Class Schedule';
        $coordinator_user_id=auth()->id();
        $getCoordinator = Coordinator::where('user_id',$coordinator_user_id)->first();
        $coordinator_id = $getCoordinator->id;
        $today_date = date('Y-m-d');

        $getcoordinatortutors = InstructorCoordinator::where('coordinator_id',$coordinator_id)->get();
		if(!$getcoordinatortutors->isEmpty()){
			foreach ($getcoordinatortutors as $key => $value) {
				$instructor_ids[] = $value->instructor_id;
			}
		}
		else{
			$instructor_ids = [];
		}

        $getIndividualClasses = ClassDetail::whereIn('instructor_id',$instructor_ids)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })->orderBy('class_date','ASC')
          ->orderBy('class_time','ASC')->paginate(25);

          $getGroupClasses = ClassDetail::whereIn('instructor_id',$instructor_ids)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Group');
        })->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')
        ->groupBy('class_id')
        ->paginate(25);

        $data['getIndividualClasses'] = $getIndividualClasses;
        $data['getGroupClasses'] = $getGroupClasses;
        $data['base_url'] = url('/');
        $data['coordinator_id'] = $coordinator_id;
  
        // dd($getGroupClasses);
        return view('coordinator.class-schedule',$data);

    }




    public function CheckLessonStartTimeCoordinator(){
        $today_date = date("Y-m-d");
        $user_id = Auth::id();
        $findCoordinator = Coordinator::where('user_id',$user_id)->first();
        $coordinator_id =  $findCoordinator->id;
        $getcoordinatortutors = InstructorCoordinator::where('coordinator_id',$coordinator_id)->get();
		if(!$getcoordinatortutors->isEmpty()){
			foreach ($getcoordinatortutors as $key => $value) {
				$instructor_ids[] = $value->instructor_id;
			}
		}
		else{
			$instructor_ids = [];
		}

        // $getLessonScheduleOfTutor1 = ClassDetail::whereIn('instructor_id',$instructor_ids)->where('group_id',null)
        // ->where('class_date','>=',$today_date)->get();
        // $getLessonScheduleOfTutor2 = ClassDetail::whereIn('instructor_id',$instructor_ids)->where('group_id','<>',null)
        // ->where('class_date','>=',$today_date)->groupBy('group_id')->orderBy('class_id','DESC')
        // ->orderBy('class_date','DESC')->get();



        $getLessonScheduleOfTutor1 = ClassDetail::whereIn('instructor_id',$instructor_ids)->where('group_id',null)
        ->where('class_date','>=',$today_date)->where('meeting_id','<>',null)->get();
        $getLessonScheduleOfTutor2 = ClassDetail::whereIn('instructor_id',$instructor_ids)->where('group_id','<>',null)
        ->where('class_date','>=',$today_date)->where('meeting_id','<>',null)->groupBy('group_id')->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')->get();



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
                        $updated_cur_time = getCurrentServerTimeByZone($coordinator_id,'coordinator');
                     
                        $curtime = strtotime($updated_cur_time);
                
                        $updated_lesson_time = changetimeByZone($bokdlsn->class_time,$coordinator_id,'coordinator');
                        $lsntime = strtotime($updated_lesson_time) ;

                        $lsntimestamp = strtotime($updated_lesson_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp ;

                        $lsntimestamp2 = strtotime($updated_lesson_time) + ($lesson_duration-1)*60;
                        $lsnendtime2 = $lsntimestamp2 ;
            
                        if($curdate == $lsndate){
                         
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->meeting_id != NULL && $bokdlsn->class->learning_tool == 'Zoom')
                            {
                                if($bokdlsn->group_id == NULL){
                                    // $data = startzoomclassindividual2($bokdlsn->id,$bokdlsn->student_id,$bokdlsn->instructor_id,$meetingID,null,$bokdlsn->class->learning_tool);
                                        // $return['l_id'] = $data['l_id'];
                                        // $return['learning_tool'] = 'Zoom';
                                        // $return['url'] = null;
                                        // $return['is_group'] = $data['is_group'];
                                        // $return['meeting_id'] = $data['zoom_meeting_id'];
                                        // $return['user_name'] = $data['academic_name'];
                                        // $return['user_id'] = $data['academic_id'];
                                        // $return['user_type'] = $data['academic_type'];
                                        // $meetingID = $data['zoom_meeting_id'];
                                        // $lesson_id = $data['l_id'];
                                        // echo json_encode($return);

                                        $meetingID = $bokdlsn->meeting_id;
								        $lesson_id = $bokdlsn->id;
                                        $return['l_id'] = $bokdlsn->id;
                                        $return['learning_tool'] = 'Zoom';
                                        $return['url'] = null;
                                        $return['is_group'] = 'No';
                                        $return['meeting_id'] = $bokdlsn->meeting_id;
                                        $return['user_name'] = "Quran Education Coordinator";
                                        $return['user_id'] = null;
                                        $return['user_type'] = 'Academic';
                                        echo json_encode($return);
                                }
                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn->status == 'Scheduled' || $bokdlsn->status == 'Pending') &&  $bokdlsn->AcademicURI != Null && $bokdlsn->class->learning_tool == 'BBB')
                            {
                                if($bokdlsn->group_id == NULL){
                               
                                        $meetingID = $bokdlsn->meeting_id;
								        $lesson_id = $bokdlsn->id;
                                        $return['l_id'] = $bokdlsn->id;
                                        $return['learning_tool'] = 'BBB';
                                        $return['url'] = $bokdlsn->AcademicURI;
                                        $return['is_group'] = 'No';
                                        $return['meeting_id'] = $bokdlsn->meeting_id;
                                        $return['user_name'] = "Quran Education Coordinator";
                                        $return['user_id'] = null;
                                        $return['user_type'] = 'Academic';
                                        echo json_encode($return);
                                }
                            }
                            // else if($curtime >= $lsnendtime){
                            //     if($bokdlsn->is_instructor_joined == '1' || $bokdlsn->is_instructor_joined == "1" || $bokdlsn->is_instructor_joined == 1 || $bokdlsn->is_instructor_joined){
                            //         $results = ClassDetail::where('id',$bokdlsn->id)->first();
                            //         $results->status = "Completed";
                            //         $response = $results->update();
                            //     }
                            //     else{
                            //         $results = ClassDetail::where('id',$bokdlsn->id)->first();
                            //         $results->status = "Missed";
                            //         $response = $results->update();
                            //     }
                            // }
                        }
                    }
            }


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
                        $updated_cur_time = getCurrentServerTimeByZone($coordinator_id,'coordinator');
                        $curtime = strtotime($updated_cur_time);
                
                        $updated_lesson_time = changetimeByZone($bokdlsn2->class_time,$coordinator_id,'coordinator');
                        $lsntime = strtotime($updated_lesson_time) ;

                        $lsntimestamp = strtotime($updated_lesson_time) + $lesson_duration*60;
                        $lsnendtime = $lsntimestamp ;

                        if($curdate == $lsndate){
                            if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->meeting_id != NULL && $bokdlsn2->class->learning_tool == 'Zoom')
                            {
                                // $data = startzoomclassgroup2($bokdlsn2->id,$bokdlsn2->student_id,$bokdlsn2->instructor_id,$meetingID,$bokdlsn2->group_id,$bokdlsn2->class->learning_tool);
                                  
                                    $meetingID = $bokdlsn2->meeting_id;
                                    $lesson_id = $bokdlsn2->id;
                                    $return['l_id'] = $bokdlsn2->id;
                                    $return['learning_tool'] = 'Zoom';
                                    $return['group_id'] = $bokdlsn2->group_id;
                                    $return['url'] = null;
                                    $return['is_group'] = 'Yes';
                                    $return['meeting_id'] = $bokdlsn2->meeting_id;
                                    $return['user_name'] = "Quran Education Coordinator";
                                    $return['user_id'] = null;
                                    $return['user_type'] = 'Academic';
                                    echo json_encode($return);



                            }
                            else if(($curtime == $lsntime || ($curtime > $lsntime && $curtime < $lsnendtime)) && ($bokdlsn2->status == 'Scheduled' || $bokdlsn2->status == 'Pending') &&  $bokdlsn2->AcademicURI != Null && $bokdlsn2->class->learning_tool == 'BBB')
                            {
                              
                                    $meetingID = $bokdlsn2->meeting_id;
                                    $lesson_id = $bokdlsn2->id;
                                    $return['l_id'] = $bokdlsn2->id;
                                    $return['learning_tool'] = 'BBB';
                                    $return['group_id'] = $bokdlsn2->group_id;
                                    $return['url'] = $bokdlsn2->AcademicURI;
                                    $return['is_group'] = 'Yes';
                                    $return['meeting_id'] = $bokdlsn2->meeting_id;
                                    $return['user_name'] = "Quran Education Coordinator";
                                    $return['user_id'] = null;
                                    $return['user_type'] = 'Academic';
                                    echo json_encode($return);
                            }
                            // else if($curtime >= $lsnendtime){
                            //     if($bokdlsn2->is_instructor_joined == '1' || $bokdlsn2->is_instructor_joined == "1" || $bokdlsn2->is_instructor_joined == 1 || $bokdlsn2->is_instructor_joined){
                            //         $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->first();
                            //         $results->status = "Completed";
                            //         $response = $results->update();
                            //     }
                            //     else{
                            //         $results = ClassDetail::where('meeting_id',$bokdlsn2->meeting_id)->where('group_id',$bokdlsn2->group_id)->first();
                            //         $results->status = "Missed";
                            //         $response = $results->update();
                            //     }
                            // }
                        }
                    }

            }



    }




    public function UpdateCoordinatorMeetingStatusIndividual($meeting_id,$lesson_id)
    {
        $result=updateCoordinatorJoinStatus($lesson_id,$meeting_id,null); 
        echo json_encode($result);    
    }




    public function UpdateCoordinatorMeetingStatusGroup($group_id,$meeting_id,$lesson_id)
    {
        $result=updateCoordinatorJoinStatus($lesson_id,$meeting_id,$group_id); 
        echo json_encode($result);    
    }



public function CreateNewZoomMeetingCoordinator($meeting_id = null , $user_type = null){

        $user_id = Auth::id();
        $findCoordinator = Coordinator::where('user_id',$user_id)->first();
        $coordinator_id =  $findCoordinator->id;

        $lesson_duration = 30;
        $lesson_id = null;
        $group_id = 0;
        $student_name = "New Student";
        $tutor_name = "New Instructor";
        $old_start_time = null;
        $old_end_time = null;
        $data['meeting_id'] = $meeting_id;
        $data['user_type'] = $user_type;
      
        // $zoomSettings = ZoomSetting::first();
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
     
        $sdk_key = $zoomSettings->sdk_key;
        $sdk_secret = $zoomSettings->sdk_secret;
        $data['sdkKey'] = $sdk_key;
        $data['sdkSecret'] = $sdk_secret;
        $data['baseURL'] = url('/');
    
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->first();
        $instructor_id = $getLessonDetails->instructor_id;
        if($getLessonDetails != null){
            $getLesson = Lesson::where('id',$getLessonDetails->class_id)->first();

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
                elseif($getLessonDetails->class?->zoom_meeting_name != null){
                    $data['session_name'] = $getLessonDetails->class?->zoom_meeting_name;
                }
                else{
                    $data['session_name'] = Uuid::uuid4()->toString();
                    $getLessonDetails->zoom_meeting_name =  $data['session_name'];
                    $getLessonDetails->update();
                    if($getLesson){
                        $getLesson->zoom_meeting_name = $data['session_name'];
                        $getLesson->update();
                    }
                    // $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
                }
                //////////////////NEW CODE//////////////////////
                $lesson_duration =$getLessonDetails->class->duration;
                $old_start_time = $getLessonDetails->start_time_zoom;
                $old_end_time = $getLessonDetails->end_time_zoom;
                $lesson_id = $getLessonDetails->id;
                $student_id = $getLessonDetails->student_id;
                $student_name =getStudentNameFromID($student_id);
                $instructor_id = $getLessonDetails->instructor_id;
                $instructor_name =getTutorNameFromID($instructor_id);

               
        }
    
    $getAcademicDetail = Coordinator::where('id',$coordinator_id)->first();
	if($getAcademicDetail != null){
		$data['user_name'] =$getAcademicDetail->first_name.' '.$getAcademicDetail->last_name;
		$data['user_id'] = $getAcademicDetail->id;
		$user_name = $getAcademicDetail->first_name.' '.$getAcademicDetail->last_name;
		$user_id = $getAcademicDetail->id;
	}
	else{
		$data['user_name'] ="New Academic Coordinator";
		$unique_id = uniqid();
		$int_number = hexdec($unique_id);
		$data['user_id'] =$int_number;
		$user_name = "New Academic Coordinator";
		$user_id = $int_number;
	}
    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->class_id.')';
        if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
            $given_start_time = $old_start_time;
            $given_end_time = $old_end_time;
        }
        else{
            $data_record_lesson = ClassDetail::where('meeting_id',$meeting_id)->first();
            $given_end_time = $data_record_lesson->end_time_zoom;
            $updated_current_time = getCurrentServerTimeByZone($coordinator_id,'coordinator');
            $given_start_time = strtotime($updated_current_time);	
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
        $data['tutor_name'] = $instructor_name;
        $data['start_time'] = $given_start_time;
        $data['end_time'] = $given_end_time;
        
        echo view('coordinator.meeting-page-zoom-ui',$data);
}






public function createNewZoomGroupMeetingCoordinator($meeting_id = null , $user_type = null , $group_id = null){

        $user_id = Auth::id();
        $findCoordinator = Coordinator::where('user_id',$user_id)->first();
        $coordinator_id =  $findCoordinator->id;

        $lesson_duration = 30;
        $student_names_array = array();
        $lesson_id = null;
        $student_name = "New Student";
        $tutor_name = "New Instructor";
        $old_start_time = null;
        $old_end_time = null;
        $data['meeting_id'] = $meeting_id;
        $data['user_type'] = $user_type;
        $data['group_id'] = $group_id;
      
        // $zoomSettings = ZoomSetting::first();
        $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
     
        $sdk_key = $zoomSettings->sdk_key;
        $sdk_secret = $zoomSettings->sdk_secret;
        $data['sdkKey'] = $sdk_key;
        $data['sdkSecret'] = $sdk_secret;
        $data['baseURL'] = url('/');
    
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->get();
        if(!$getLessonDetails->isEmpty()){
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
                    $data['session_name'] = Uuid::uuid4()->toString();
                    $value2->zoom_meeting_name =  $data['session_name'];
                    $value2->update();
                    // if($getLesson){
                    //     $getLesson->zoom_meeting_name = $data['session_name'];
                    //     $getLesson->update();
                    // }
                    // $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
                }
                //////////////////NEW CODE//////////////////////
            }
        }

        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();
        if($getLessonDetails != null){
        //////////////////NEW CODE//////////////////////
        $getLesson = Lesson::where('id',$getLessonDetails->class_id)->first();
        if($getLesson){
            $getLesson->zoom_meeting_name = $data['session_name'];
            $getLesson->update();
        }
         //////////////////NEW CODE//////////////////////

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
                elseif($getLessonDetails->class?->zoom_meeting_name != null){
                    $data['session_name'] = $getLessonDetails->class?->zoom_meeting_name;
                }
                else{
                    $data['session_name'] = Uuid::uuid4()->toString();
                    $getLessonDetails->zoom_meeting_name =  $data['session_name'];
                    $getLessonDetails->update();
                    if($getLesson){
                        $getLesson->zoom_meeting_name = $data['session_name'];
                        $getLesson->update();
                    }
                    // $data['session_name'] = "Online Meeting-$getLessonDetails->meeting_id";
                }
                //////////////////NEW CODE//////////////////////
                $lesson_duration =$getLessonDetails->class->duration;
                $old_start_time = $getLessonDetails->start_time_zoom;
                $old_end_time = $getLessonDetails->end_time_zoom;
                $group_id = $getLessonDetails->group_id;
                $instructor_id = $getLessonDetails->instructor_id;
                $instructor_name =getTutorNameFromID($instructor_id);
            
        }
      
    $getAcademicDetail = Coordinator::where('id',$coordinator_id)->first();
	if($getAcademicDetail != null){
		$data['user_name'] =$getAcademicDetail->first_name.' '.$getAcademicDetail->last_name;
		$data['user_id'] = $getAcademicDetail->id;
		$user_name = $getAcademicDetail->first_name.' '.$getAcademicDetail->last_name;
		$user_id = $getAcademicDetail->id;
	}
	else{
		$data['user_name'] ="New Academic Coordinator";
		$unique_id = uniqid();
		$int_number = hexdec($unique_id);
		$data['user_id'] =$int_number;
		$user_name = "New Academic Coordinator";
		$user_id = $int_number;
	}
    $data['user_name'] = $data['user_name'].' ('.$getLessonDetails->class_id.')';
        if(($old_start_time != null && $old_start_time != 0) &&  ($old_end_time != null && $old_end_time != 0)){
            $given_start_time = $old_start_time;
            $given_end_time = $old_end_time;
        }
        else{
            $data_record_lesson = ClassDetail::where('meeting_id',$meeting_id)->first();
            $given_end_time = $data_record_lesson->end_time_zoom;
            $updated_current_time = getCurrentServerTimeByZone($coordinator_id,'coordinator');
            $given_start_time = strtotime($updated_current_time);	
        }
    
        if($given_start_time == null){
            $given_start_time = 0;
        }
        if($given_end_time == null){
            $given_end_time = 0;
        }

        $student_names_json = implode(',',$student_names_array);
	    $data['student_names_json'] = $student_names_json;
        $data['lesson_duration'] = $lesson_duration;
        $data['lesson_id'] = $lesson_id;
        $data['group_id'] = $group_id;
        $data['student_name'] = $student_name;
        $data['tutor_name'] = $instructor_name;
        $data['start_time'] = $given_start_time;
        $data['end_time'] = $given_end_time;
        
        echo view('coordinator.meeting-page-zoom-ui',$data);
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


    public function CheckModeratorLeftZoomCoordinator($meeting_id = null){
        $is_moderator_left = '0';
        $is_left = "No";
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


    public function CheckModeratorLeftGroupZoomCoordinator($meeting_id = null , $group_id = null){
        $is_moderator_left = null;
        $is_left = 'No';
        $classes = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->get();
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



    public function UpdateUrlZoomCoordinator($meeting_id = null , $currentStartTime = null){
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
                    // $res->is_coordintor_joined = '0';
                    $res->is_coordintor_joined = 0;
                }
                else{
                    // $res->is_coordinator_joined = '1';
                    $res->is_coordinator_joined = 1;
                }
                $res->update();
            }
        }
    
    }




    public function UpdateUrlGroupZoomCoordinator($meeting_id = null , $group_id = null , $currentStartTime = null){
        $old_start_time = null;
        $old_end_time = null;
        $getLessonDetails = ClassDetail::where('meeting_id',$meeting_id)->where('group_id',$group_id)->first();
        if($getLessonDetails!= null){
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
        $l_id = null;

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
                    // $res->is_coordinator_joined = '0';
                    $res->is_coordinator_joined = 0;
                }
                else{
                    $res->is_coordinator_joined = 1;
                }
                $res->update();
            }
        }
    
    }







        ////////////////////////////////// NEW FUNCTION/////////////////////////////////////
        public function UpdateJoinedStatusZoomCoordinator($meeting_id = null , $group_id = null){
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
                    $currentStartTime = $this->GetCurrentTimeZoomCoordinator($meeting_id);
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
                            $currentStartTime = $this->GetCurrentTimeZoomCoordinator($meeting_id);
                            $value->start_time_zoom = $currentStartTime;
                            $value->update();
                        }
                    }
    
                    $l_id = $value->id;
                    $start_time = $value->start_time_zoom;
                    $end_time = $value->end_time_zoom;
                    $res = ClassDetail::where('id',$l_id)->first();
                    if($start_time >= $end_time){
                        $res->is_coordinator_joined = 0;
                    }
                    else{
                        $res->is_coordinator_joined = 1;
                    }
                    $res->update();
                }
            }
        
        }
       ///////////////////////////////////// NEW FUNCTION//////////////////////////////////







    public function GetCurrentTimeZoomCoordinator($meeting_id = null){
      
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


    public function dashboard()
    {
        $data['title'] = 'Dashboard';
      
        // //Start::  Cancel Consultation Money Calculation
        // $cancelConsultationOrderItemIds = BookingHistory::where(['instructor_user_id' => Auth::id(),'status' => BOOKING_HISTORY_STATUS_CANCELLED, 'send_back_money_status' => SEND_BACK_MONEY_STATUS_YES])
        //     ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
        //     ->whereHas('order', function ($q){
        //     $q->where('payment_status', 'paid');
        // })->pluck('order_item_id')->toArray();
        // $consultationOrderItems = Order_item::whereIn('id', $cancelConsultationOrderItemIds);
        //End::  Cancel Consultation Money Calculation

        // $userCourseIds = CourseInstructor::where('instructor_id', auth()->id())->select('course_id')->get()->pluck('course_id')->toArray();
        // $userCourseIds = Course::whereUserId(auth()->user()->id)->pluck('id')->toArray();

        // $orderBundleItemsCount = Order_item::where('owner_user_id', Auth::id())->whereNotNull('bundle_id')->whereNull('course_id')
        //     ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
        //     ->whereHas('order', function ($q) {
        //         $q->where('payment_status', 'paid');
        //     })->count();

        // $orderItems = Order_item::whereIn('course_id', $userCourseIds)
        //     ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
        //     ->whereHas('order', function ($q) {
        //         $q->where('payment_status', 'paid');
        //     });
        //Start::Affiliate Earning from AffiliateHistory
        $affiliateHistoryTotalCommission = AffiliateHistory::where(['user_id' => Auth::id(), 'status' => AFFILIATE_HISTORY_STATUS_PAID])->sum('commission');
        //End::Affiliate Earning from AffiliateHistory

        // $data['total_earning_this_month'] = $orderItems->sum('owner_balance') + $affiliateHistoryTotalCommission - $consultationOrderItems->sum('owner_balance');
        // $data['total_enroll_this_month'] = $orderItems->count('id') - $orderBundleItemsCount - count($cancelConsultationOrderItemIds);

        // $data['best_selling_course'] = Order_item::whereIn('course_id', $userCourseIds)->whereHas('order', function ($q) {
        //     $q->where('payment_status', 'paid');
        // })->groupBy("course_id")->selectRaw("COUNT(course_id) as total_course_id, course_id")->orderByRaw("COUNT(course_id) DESC")->first();

        // $data['recentCourses'] = Course::whereIn('id', $userCourseIds)->latest()->limit(2)->get();
        // $data['updatedCourses'] = Course::whereIn('id', $userCourseIds)->orderBy('updated_at', 'desc')->limit(2)->get();

        // Start:: Sale Statistics
        $months = collect([]);
        $totalPrice = collect([]);

        //Start::  Cancel Consultation Money Calculation
        $cancelConsultationOrderItemIds = BookingHistory::where(['instructor_user_id' => Auth::id(),'status' => 2, 'send_back_money_status' => SEND_BACK_MONEY_STATUS_YES])
            ->whereHas('order', function ($q){
                $q->where('payment_status', 'paid');
            })->pluck('order_item_id')->toArray();
        //End
        // Order_item::whereNotIn('id', $cancelConsultationOrderItemIds)->whereIn('course_id', $userCourseIds)->whereHas('order', function ($q) {
        //     $q->where('payment_status', 'paid');
        // })->select(DB::raw('SUM(owner_balance) as total'), DB::raw('MONTHNAME(created_at) month'))
        //     ->groupby('month')
        //     ->get()
        //     ->map(function ($q) use ($months, $totalPrice) {
        //         $months->push($q->month);
        //         $totalPrice->push($q->total);
        //     });

        $data['months'] = $months;
        $data['totalPrice'] = $totalPrice;
        //End:: Sale Statistics

        //Start:: ranking Level
        // $allOrderItems = Order_item::whereIn('course_id', $userCourseIds)->whereHas('order', function ($q) {
        //     $q->where('payment_status', 'paid');
        // });

        // $data['grand_total_earning'] = $allOrderItems->sum('owner_balance');
        // $data['grand_total_enroll'] = $allOrderItems->count('id') - $orderBundleItemsCount;

        $currentLevel = get_instructor_ranking_level(auth()->user()->badges);
        
        $data['rankingLevels'] = RankingLevel::orderBy('serial_no', 'DESC')->where('type', RANKING_LEVEL_EARNING)->get();
        $data['currentLevel'] = $data['rankingLevels']->where('name', $currentLevel)->first();
        $data['secondLevel'] = $data['rankingLevels']->where('id', '>', @$data['currentLevel']->id)->first();
        //End:: ranking Level
        return view('coordinator.coordinatorDashboard', $data);
    }

    public function home() {
        $data['base_url'] = url('/');
        return view('coordinator.home',$data);
    }


    public function classHistory() {

        $data['title'] = 'Class History';
        $coordinator_user_id=auth()->id();
        $Coordinator = Coordinator::where('user_id',$coordinator_user_id)->first();
        $coordinator_id = $Coordinator->id;
        $today_date = date('Y-m-d');
        $coordinatorInstructors = InstructorCoordinator::where('coordinator_id',$coordinator_id)->get();
        $instructorIDs = array();
        if(!$coordinatorInstructors->isEmpty()){
            foreach ($coordinatorInstructors as $key => $data) {
                $instructorIDs[] = $data->instructor_id;
            }
            $instructorIDs = array_unique($instructorIDs);
        }
   

        $getIndividualClassesHistory = ClassDetail::whereIn('instructor_id',$instructorIDs)->where('class_date' ,'<=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })
        ->orderBy('id','DESC')
          ->orderBy('class_date','ASC')
          ->orderBy('class_time','ASC')->paginate(10);

      

        $getGroupClassesHistory = ClassDetail::whereIn('instructor_id',$instructorIDs)->where('class_date' ,'<=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Group');
        })
        ->orderBy('id','DESC')
        ->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')
        ->groupBy('class_id')
        ->paginate(10);

        // $data['getIndividualClassesHistory'] = $getIndividualClassesHistory;
        $data['getIndividualClassesHistory'] = $getIndividualClassesHistory;
        $data['getGroupClassesHistory'] = $getGroupClassesHistory;
        $data['base_url'] = url('/');
  
        // dd($getGroupClasses);
        return view('coordinator.class-history',$data);

    }


    public function latestBlogs(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 5){ // Coordinator
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




    public function monthlyClasses(){
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 5){ // Coordinator
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
     
        $coordinator_user_id=auth()->id();
        $Coordinator = Coordinator::where('user_id',$coordinator_user_id)->first();
        $coordinator_id = $Coordinator->id;
        $coordinatorInstructors = InstructorCoordinator::where('coordinator_id',$coordinator_id)->get();
        $instructorIDs = array();
        if(!$coordinatorInstructors->isEmpty()){
            foreach ($coordinatorInstructors as $key => $data) {
                $instructorIDs[] = $data->instructor_id;
            }
            $instructorIDs = array_unique($instructorIDs);
        }
   
        $today = Carbon::today();
        $todayUpdated = $today->format('Y-m-d');
        // Date six months ago
        $sixMonthsAgo = $today->copy()->subMonths(6);
        $sixMonthsAgoUpdated = $sixMonthsAgo->format('Y-m-d');
    
         // Get last six months' start dates, names, and numbers
  
        // $getInstructor = Instructor::where('user_id',$user->id)->first();
        // $instructor_id = $getInstructor->id;
    
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $today->copy()->subMonths($i)->startOfMonth();
            $monthName = $date->format('F');
            $monthNumber = $date->format('m');

            // $MonthCompleteClasses = ClassDetail::where('instructor_id',$instructor_id)
            $MonthCompleteClasses = ClassDetail::whereIn('instructor_id',$instructorIDs)
            ->whereDate('class_date' ,'>=',$sixMonthsAgoUpdated)
            ->whereDate('class_date' ,'<=',$todayUpdated)
            ->whereMonth('class_date', $monthNumber)
            ->where('status','Completed')
            ->orderBy('id','DESC')
            ->orderBy('class_date','ASC')
            ->groupBy('class_id')
            ->get()
            ->count();
        
            $MonthMissedClasses = ClassDetail::whereIn('instructor_id',$instructorIDs)
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
            if($role != 5){ // Coordinator
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 200);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 200);
        }
        $result_array = array();
      
        $coordinator_user_id=auth()->id();
        $Coordinator = Coordinator::where('user_id',$coordinator_user_id)->first();
        $coordinator_id = $Coordinator->id;
        $coordinatorInstructors = InstructorCoordinator::where('coordinator_id',$coordinator_id)->get();
        $instructorIDs = array();
        if(!$coordinatorInstructors->isEmpty()){
            foreach ($coordinatorInstructors as $key => $data) {
                $instructorIDs[] = $data->instructor_id;
            }
            $instructorIDs = array_unique($instructorIDs);
        }

        // $instructor_user_id=$user->id;
        // $getInstructor = Instructor::where('user_id',$instructor_user_id)->first();
        // $instructor_id = $getInstructor->id;


        $today_date = date('Y-m-d');
      
        // $getTodayLiveClass = ClassDetail::where('instructor_id',$instructor_id)->where('class_date',$today_date)
        // ->whereNotIn('status',['Completed','Missed'])
        // ->orderBy('class_date','ASC')->first();
        // $getTodayLiveClass = ClassDetail::where('instructor_id',$instructor_id)->where('class_date',$today_date)
        $getTodayLiveClass = ClassDetail::whereIn('instructor_id',$instructorIDs)->where('class_date',$today_date)
        ->whereNotIn('status',['Completed','Missed'])
        ->orderBy('class_date','ASC')
        ->orderBy('class_time','ASC')
        ->groupBy('class_id')
        ->first();
        // dd($getTodayLiveClass);

        if($getTodayLiveClass){
            $individual = array();
            $group = array();
            $BBB_Meeting_Details = array();
            $Zoom_Meeting_Details = array();

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
                else if($curtime == $lsnendtime && $getTodayLiveClass->class?->learning_tool == 'Zoom') {
                    if(($getTodayLiveClass->status != "Completed" && $getTodayLiveClass->status != "Missed") && $getTodayLiveClass->class?->learning_tool == 'Zoom' && ($getTodayLiveClass->is_instructor_joined == '1' || $getTodayLiveClass->is_instructor_joined == "1" || $getTodayLiveClass->is_instructor_joined == 1 || $getTodayLiveClass->is_instructor_joined)) {
                        // updateLessonStatusCompleteZoom($getTodayLiveClass->id,$getTodayLiveClass->meeting_id,$getTodayLiveClass->group_id);
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


}
