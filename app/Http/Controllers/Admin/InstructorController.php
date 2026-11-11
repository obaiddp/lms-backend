<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Instructor;
use App\Models\Order_item;
use App\Models\Coordinator;
use App\Services\InstructorGoogleAuthService;
use App\Models\InstructorCoordinator;
use App\Models\Package;
use App\Models\State;
use App\Models\Student;
use App\Models\User;
use App\Models\UserPackage;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstructorController extends Controller
{
    use General, ImageSaveTrait;

    protected $instructorModel, $studentModel, $instructorCoordinatorModel;
    public function __construct(Instructor $instructor, Student $student,InstructorCoordinator $instructor_coordinator)
    {
        $this->instructorModel = new Crud($instructor);
        $this->instructorCoordinatorModel = new Crud($instructor_coordinator);
        $this->studentModel = new Crud($student);
    }

    public function index()
    {
        if (!Auth::user()->can('all_instructor')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'All Instructors';
        $data['instructors'] = $this->instructorModel->getOrderById('DESC', 25);
        return view('admin.instructor.index', $data);
    }

    public function view($uuid)
    {
        $is_google_authorized = 0;
        $data['title'] = 'Instructor Profile';
        $data['instructor'] = $this->instructorModel->getRecordByUuid($uuid);
        $userID = $data['instructor']->user_id;
        $getUser = User::where('id',$userID)->first();
        if($getUser){
            if($getUser->instructor_google_access_token){
                $is_google_authorized = 1;
            }
        }
        else{
            $is_google_authorized = 0;
        }
        $data['userID'] = $userID;
        $data['isGoogleAuthorized'] = $is_google_authorized;
   
        $userCourseIds = Course::whereUserId($data['instructor']->user->id)->pluck('id')->toArray();
        if (count($userCourseIds) > 0){
            $orderItems = Order_item::whereIn('course_id', $userCourseIds)
                ->whereYear("created_at", now()->year)->whereMonth("created_at", now()->month)
                ->whereHas('order', function ($q) {
                    $q->where('payment_status', 'paid');
                });
            $data['total_earning'] = $orderItems->sum('owner_balance');
        }

        return view('admin.instructor.view', $data);
    }


    public function removeGoogleAuthorization($userID, InstructorGoogleAuthService $googleAuthService)
    {
        $userID = (int) $userID;
        $getUser = User::where('id', $userID)->first();
        if ($getUser) {
            $googleAuthService->clearForUser($getUser);
        }

        $this->showToastrMessage('success', __("Google Authorization Removed Now"));

        return redirect()->back();
    }



    public function pending()
    {
        if (!Auth::user()->can('pending_instructor')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Pending for Review';
        $data['instructors'] = Instructor::pending()->orderBy('id', 'desc')->paginate(25);
        return view('admin.instructor.pending', $data);
    }

    public function approved()
    {
        if (!Auth::user()->can('approved_instructor')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Approved Instructor';
        $data['instructors'] = Instructor::approved()->orderBy('id', 'desc')->paginate(25);
        return view('admin.instructor.approved', $data);
    }

    public function blocked()
    {
        if (!Auth::user()->can('approved_instructor')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Blocked Instructor';
        $data['instructors'] = Instructor::blocked()->orderBy('id', 'desc')->paginate(25);
        return view('admin.instructor.blocked', $data);
    }

    public function changeStatus($uuid, $status)
    {
        try {
            DB::beginTransaction();
            $instructor = $this->instructorModel->getRecordByUuid($uuid);
            $instructor->status = $status;
            $instructor->save();

            if ($status == 1)
            {
                $user = User::find($instructor->user_id);

                if(!UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->where('package_type', PACKAGE_TYPE_SAAS_INSTRUCTOR)->where('user_packages.user_id', $user->id)->select('user_packages.*')->first()){
                    //set default package
                    $package = Package::where('id',get_option('default_saas_for_ins'))->first();
                    // dd($package);
                    if(is_null($package) && get_option('saas_mode')){
                        DB::rollBack();
                        $this->showToastrMessage('error', __("You Don't have default SAAS Package For Instructor"));
                        return redirect()->back();
                    }
                    elseif(!is_null($package)){
                        $userPackageData['user_id'] = $user->id;
                        $userPackageData['is_default'] = 1;
                        $userPackageData['package_id'] = $package->id;
                        $userPackageData['subscription_type'] = get_option('saas_ins_default_package_type', 'monthly') == 'yearly' ?  SUBSCRIPTION_TYPE_YEARLY : SUBSCRIPTION_TYPE_MONTHLY;
                        $userPackageData['student'] = $package->student;
                        $userPackageData['instructor'] = $package->instructor;
                        $userPackageData['course'] = $package->course;
                        $userPackageData['consultancy'] = $package->consultancy;
                        $userPackageData['subscription_course'] = $package->subscription_course;
                        $userPackageData['bundle_course'] = $package->bundle_course;
                        $userPackageData['product'] = $package->product;
                        $userPackageData['admin_commission'] = $package->admin_commission;
                        $userPackageData['payment_id'] = NULL;
                        $userPackageData['enroll_date'] = now();
                        $userPackageData['expired_date'] = get_option('saas_ins_default_package_type', 'monthly') == 'yearly' ? Carbon::now()->addYear() : Carbon::now()->addMonth();
                        UserPackage::create($userPackageData);
                    }
                }

                $user->role = USER_ROLE_INSTRUCTOR;
                $user->save();
                setBadge($user->id);
            }
            DB::commit();
            $this->showToastrMessage('success', __('Status has been changed'));
            return redirect()->back();
        }catch (\Exception $e){
            DB::rollBack();
            $this->showToastrMessage('error', $e->getMessage());
            return redirect()->back();
        }

    }

    public function create()
    {
        $data['title'] = 'Add Instructor';
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id')) {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id')) {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        return view('admin.instructor.add', $data);
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
            'is_featured' => 'required',
            'public_profile' => 'required',
            'phone_number' => 'bail|numeric|unique:users,mobile_number',
            'address' => 'required',
            'gender' => 'required',
            'about_me' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
        ]);

  
        $user = new User();
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->email_verified_at = now();
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->mobile_number = $request->phone_number;
        $user->phone_number = $request->phone_number;
        $user->is_featured = $request->is_featured;
        $user->is_public = $request->public_profile;
        $user->password = Hash::make($request->password);
        $user->role = 2;
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   null;
      
       
        $user->og_image = $user->image;
        $user->save();

        $student_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'phone_number' => $user->phone_number,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
        ];

        $this->studentModel->create($student_data);

        if (Instructor::where('slug', getSlug($user->name))->count() > 0)
        {
            $slug = getSlug($user->name) . '-'. rand(100000, 999999);
        } else {
            $slug = getSlug($user->name);
        }

        $instructor_data = [
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
            'is_featured' => $request->is_featured,
            'is_public' => $request->public_profile,
            'about_me' => $request->about_me,
            'postal_code' => $request->postal_code,
            'social_link' => json_encode($request->social_link),
        ];

        $this->instructorModel->create($instructor_data);

        $this->showToastrMessage('success', __('Instructor created successfully'));
        return redirect()->route('instructor.index');
    }

    public function edit($uuid)
    {
        $coordinators = array();
        $courses = array();
        $data['title'] = 'Edit Instructor';
        $data['instructor'] = $this->instructorModel->getRecordByUuid($uuid);
        $instructorID = $data['instructor']->id;
        $data['user'] = User::findOrfail($data['instructor']->user_id);
        $data['coordinators'] = Coordinator::where('status',1)->where('deleted_at',null)->get();
        $checkCoordinators = InstructorCoordinator::where('instructor_id',$instructorID)->get();
        if(!$checkCoordinators->isEmpty()){
            foreach ($checkCoordinators as $key => $checkCoordinator) {
                $coordinators[]=$checkCoordinator->coordinator_id;
            }
            $coordinators= array_unique($coordinators);
        }
        else{
            $coordinators = []; 
        }

        $instructor = Instructor::where('id',$instructorID)->first();
        if($instructor){
            $instructor_user_id = $instructor->user_id;
        }
        else{
            $instructor_user_id = 0;
        }
     
      

        $checkCourses = CourseInstructor::where('instructor_id',$instructor_user_id)->get();
        if(!$checkCourses->isEmpty()){
            foreach ($checkCourses as $key => $checkCourse) {
                $courses[]=$checkCourse->course_id;
            }
            $courses= array_unique($courses);
        }
        else{
            $courses = []; 
        }
    
        $data['check_coordinators'] = $coordinators;
        $data['check_courses'] = $courses;
    
        $data['allCourses'] = Course::where('status',1)->get();
        // dd($data['allCourses']);
        $data['countries'] = Country::orderBy('country_name', 'asc')->get();

        if (old('country_id'))
        {
            $data['states'] = State::where('country_id', old('country_id'))->orderBy('name', 'asc')->get();
        }

        if (old('state_id'))
        {
            $data['cities'] = City::where('state_id', old('state_id'))->orderBy('name', 'asc')->get();
        }

        $timezones_array = timezones_array();
        $data['timeZonesArray'] = $timezones_array;
     
    
        return view('admin.instructor.edit', $data);
    }

    public function update(Request $request, $uuid)
    {
        $instructor = $this->instructorModel->getRecordByUuid($uuid);
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$instructor->user_id],
            'professional_title' => 'required',
            'is_featured' => 'required',
            'public_profile' => 'required',
            'area_code' => 'required',
            'phone_number' => 'bail|numeric|unique:users,mobile_number,'.$instructor->user_id,
            'time_zone' => 'required',
            'address' => 'required',
            'gender' => 'required',
            'about_me' => 'required',
            'image' => 'mimes:jpeg,png,jpg|file|dimensions:min_width=300,min_height=300,max_width=300,max_height=300|max:1024'
        ]);

        $instructorID = $instructor->id;
        $instructordata = Instructor::where('id',$instructorID)->first();
        if($instructordata){
            $instructor_user_id = $instructordata->user_id;
        }
        else{
            $instructor_user_id = 0;
        }
        $user = User::findOrfail($instructor->user_id);
        if (User::where('id', '!=', $instructor->user_id)->where('email', $request->email)->count() > 0) {
            $this->showToastrMessage('warning', __('Email already exist'));
            return redirect()->back();
        }

        $coordinators_ids = $request->ac_id;
        if(!empty($coordinators_ids)){
            $checkExisting = InstructorCoordinator::where('instructor_id',$instructorID)->first();
            if($checkExisting){
                $deleteExisting = InstructorCoordinator::where('instructor_id',$instructorID)->delete();
            }
            foreach ($coordinators_ids as $key => $id) {
            
                $ic_data = [
                    'instructor_id' => $instructorID,
                    'coordinator_id' => (int)$id,
                ];
                $this->instructorCoordinatorModel->create($ic_data);
            }
          
        }
      

        $courses_ids = $request->course_id;
  
        if(!empty($courses_ids)){
            // $checkExisting = CourseInstructor::where('instructor_id',$instructor_user_id)->get();
            $checkExisting = CourseInstructor::where('instructor_id',$instructor_user_id)->whereHas('course', function ($query) {
                $query->where('status', 1);
            })->get();
         
            if(!$checkExisting->isEmpty()){
                foreach ($checkExisting as $key => $value) {
                    $value->delete();
                }
                // $deleteExisting = CourseInstructor::where('instructor_id',$instructor_user_id)->delete();
            }
            foreach ($courses_ids as $key => $id) {
                $new_course_enroll = new CourseInstructor;
                $new_course_enroll->instructor_id = $instructor_user_id;
                $new_course_enroll->course_id = (int)$id;
                $new_course_enroll->status = 1;
                $new_course_enroll->share = 25;
                $new_course_enroll->save();
            }
          
        }
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->mobile_number = $request->phone_number;
        $user->phone_number = $request->phone_number;
        $user->time_zone =  $request->time_zone;
        $user->is_featured = $request->is_featured;
        $user->is_public = $request->public_profile;
        if ($request->password){
            $request->validate([
                'password' => 'required|string|min:6'
            ]);
            $user->password = Hash::make($request->password);
        }
        $user->image =  $request->image ? $this->saveImage('user', $request->image, null, null) :   $user->image;
        if($user->og_image == null){
            $user->og_image = $user->image;
        }
        else{  
        }

        $user->save();

        if (Instructor::where('slug', getSlug($user->name))->count() > 0)
        {
            $slug = getSlug($user->name) . '-'. rand(100000, 999999);
        } else {
            $slug = getSlug($user->name);
        }


        $video = null;
        if ($request->video) {
            $this->deleteVideoFile($instructordata->video); // delete file from server
            $file_details = $this->uploadFileWithDetails('instructor', $request->video);
            if (!$file_details['is_uploaded']) {
                $this->showToastrMessage('error', __('Something went wrong! Failed to upload file'));
                return redirect()->back();
            }
            $video = $file_details['path'];
        } else {
            $video = $instructordata->video;
        }

        if($request->youtube_video_id){
            $video = $request->youtube_video_id;
        }


        $instructor_data = [
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
            'is_featured' => $request->is_featured,
            'is_public' => $request->public_profile,
            'about_me' => $request->about_me,
            'video' => $video ?? null,
            'video_check' => $request->intro_video_check ?? null,
            'postal_code' => $request->postal_code,
            'social_link' => json_encode($request->social_link),
        ];

        $this->instructorModel->updateByUuid($instructor_data, $uuid);

        $this->showToastrMessage('success', __('Updated Successfully'));
        return redirect()->route('instructor.index');
    }

    public function delete($uuid)
    {
        if (!Auth::user()->can('manage_instructor')) {
            abort('403');
        } // end permission checking

        $instructor = $this->instructorModel->getRecordByUuid($uuid);
        $user = User::findOrfail($instructor->user_id);

        if ($instructor && $user){
            //Start:: Course Delete
            $courses = Course::whereUserId($user->id)->get();
            if(count($courses)){
                return response()->json(['message' =>  __('This user have courses. Please delete those course before delete the user.'), 'status' => false], 200);
            }

            // foreach ($courses as $course)
            // {
            //     //start:: Course lesson delete
            //     $lessons = Course_lesson::where('course_id', $course->id)->get();
            //     if (count($lessons) > 0)
            //     {
            //         foreach ($lessons as $lesson)
            //         {
            //             //start:: lecture delete
            //             $lectures = Course_lecture::where('lesson_id', $lesson->id)->get();
            //             if (count($lectures) > 0)
            //             {
            //                 foreach ($lectures as $lecture)
            //                 {
            //                     $lecture = Course_lecture::find($lecture->id);
            //                     if ($lecture)
            //                     {
            //                         $this->deleteFile($lecture->file_path); // delete file from server

            //                         if ($lecture->type == 'vimeo')
            //                         {
            //                             if ($lecture->url_path)
            //                             {
            //                                 $this->deleteVimeoVideoFile($lecture->url_path);
            //                             }
            //                         }

            //                         Course_lecture_views::where('course_lecture_id', $lecture->id)->get()->map(function ($q) {
            //                             $q->delete();
            //                         });

            //                         Course_lecture::find($lecture->id)->delete(); // delete lecture record
            //                     }
            //                 }
            //             }
            //             //end:: delete lesson record
            //             Course_lesson::find($lesson->id)->delete();
            //         }
            //     }
            //     //end

            //     $this->deleteFile($course->image);
            //     $this->deleteVideoFile($course->video);
            //     $course->delete();
            // }
            //End:: Course Delete
        }
        $this->instructorModel->deleteByUuid($uuid);

        $user->role = 3;
        $user->save();

        return response()->json(['message' =>  __('Instructor Deleted Successfully'), 'status' => true], 200);
    }

    public function getStateByCountry($country_id)
    {
        return State::where('country_id', $country_id)->orderBy('name', 'asc')->get()->toJson();
    }

    public function getCityByState($state_id)
    {
        return City::where('state_id', $state_id)->orderBy('name', 'asc')->get()->toJson();
    }

    public function changeInstructorStatus(Request $request)
    {
        $instructor = Instructor::findOrFail($request->id);
        $instructor->status = $request->status;
        $instructor->save();

        return response()->json([
            'data' => 'success',
        ]);
    }
    
    public function changeAutoContentStatus(Request $request)
    {
        $instructor = Instructor::findOrFail($request->id);
        $instructor->auto_content_approval = $request->auto_content_approval;
        $instructor->save();

        return response()->json([
            'data' => 'success',
        ]);
    }
}
