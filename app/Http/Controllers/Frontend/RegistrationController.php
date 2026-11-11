<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\SignUpRequest;
use App\Http\Requests\Frontend\SignUpAsInstructorRequest;
use App\Http\Services\EmailSendService;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\TimeSlot;
use App\Models\Enrollment;
use App\Models\Lead;
use App\Models\BookedSlot;
use App\Models\TrialClass;
use App\Models\Order;
use App\Models\Order_item;
use App\Models\CourseInstructor;
use App\Models\CartManagement;
use App\Models\Course_lecture;
use App\Models\Course_lecture_views;
use App\Models\Course;
use App\Models\User;
use App\Tools\Repositories\Crud;
use Illuminate\Support\Facades\Validator;
use App\Traits\General;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use App\Traits\ImageSaveTrait;
use App\Traits\SendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    use  ImageSaveTrait, General, SendNotification;

    protected $model;
    protected $studentModel;
    protected $organizationModel;
    protected $instructorModel;

    public function __construct(User $user, Student $student , Instructor $instructor, Organization $organization)
    {
        $this->model = new Crud($user);
        $this->studentModel = new Crud($student);
        $this->instructorModel = new Crud($instructor);
        $this->organizationModel = new Crud($organization);
      
    }


          //Google login
          public function redirectToGoogleLogin()
          {
          
                $redirectUrl = 'http://localhost:8000/meet-callback';
              return Socialite::driver('google')->redirectUrl($redirectUrl)->redirect();
              
          }
          // Google callback
          public function handleGoogleCallback()
          {
              $user = Socialite::driver('google')->user();
              $this->_registerOrLoginUser($user);
            
              // Return home after login
              return redirect()->route('main.index');
          }




            //Facebook login
            public function redirectToFacebookLogin()
            {
                return Socialite::driver('facebook')->redirect();
            }
            // Facebook callback
            public function handleFacebookCallback()
            {
                $user = Socialite::driver('facebook')->user();
            
                $this->_registerOrLoginUser($user);
            
                // Return home after login
                return redirect()->route('main.index');
            }



            
            //Twitter login
            public function redirectToTwitterLogin()
            {
                return Socialite::driver('twitter')->redirect();
            }
            // Twitter callback
            public function handleTwitterCallback()
            {
                $user = Socialite::driver('twitter')->user();
            
                $this->_registerOrLoginUser($user);
            
                // Return home after login
                return redirect()->route('main.index');
            }



          public function _registerOrLoginUser($data)
          {
              $user = User::where('email', '=', $data->email)->first();
      
              if (!$user) {
                  $user = new User();
                  $user->name = $data->name;
                  $user->email = $data->email;
                  $user->provider_id = $data->id;
                  $user->avatar = $data->avatar;
                  $user->role = 3;
                  $user->email_verified_at = now();
                  $user->save();
      
                  $full=$data->name;
                  $full1=explode(' ', $full);
                  $first=$full1[0];
                  $rest=ltrim($full, $first.' ');
      
                  $student  = new Student();
                  $student->user_id = $user->id;
                  $student->first_name = $first;
                  $student->last_name = $rest;
                  $student->status = get_option('private_mode') ? STATUS_PENDING : STATUS_ACCEPTED;
                  $student->save();
              }else{
                  $student = $user->student;
              }
      
              if($student->status != STATUS_PENDING){
                  Auth::login($user);
              }
          }


    public function signUp()
    {
        $data['pageTitle'] = __('Sign Up');
        $data['title'] = __('Sign Up');
        $data['countries'] = Country::all();
        return view('auth.sign-up', $data);
    }


    public function signUpAsInstructor()
    {
        $data['pageTitle'] = __('Sign Up As Instructor');
        $data['title'] = __('Instructor Sign Up');
        $data['countries'] = Country::all();
        return view('auth.sign-up-as-instructor', $data);
    }


    public function signUpAsStudent(Request $request, $type = null , $slug = null)
    {

      
      
        $form1Data = $request->session()->get('form1Data', []);
        $data['form1Data'] = $form1Data;
     
        $data['pageTitle'] = __('Sign Up As Student');
        $data['title'] = __('Student Sign Up');
        $data['countries'] = Country::all();
        // Get both live (type 3) and self-learning (type 2) courses
        $data['courses'] = Course::whereIn('course_type', [1,2, 3])
                                ->where('status', 1)
                                ->orderBy('id', 'DESC')
                                ->get();
       
        $coursesQuery = Course::whereIn('course_type', [1, 2, 3])
            ->where('status', 1)
            ->orderBy('id', 'DESC');

        // If slug is provided, find the matching course
        $selectedCourse = null;
        // 1. Priority 1: Check for slug in URL
        if (!is_null($slug)) {
            $selectedCourse = Course::where('slug', $slug)->first();
            if ($selectedCourse) {
                $type = ($selectedCourse->course_type == 3) ? 1 : 2;
            }
        } 
        // 2. Priority 2: Check for course in session data
        elseif (isset($form1Data['course']) && !empty($form1Data['course'])) {
            $selectedCourse = Course::find($form1Data['course']);
            if ($selectedCourse) {
                $type = ($selectedCourse->course_type == 3) ? 1 : 2;
                $slug = $selectedCourse->slug;
            }
        }
      
          // Apply course type filter if type is set
        if (!is_null($type)) {
            if ($type == 1) {   // live
                $coursesQuery->where('course_type', 3);
            } else {
                $coursesQuery->whereIn('course_type', [1, 2]);
            }
        }

        $courses = $coursesQuery->get();
        $data['courses'] = $courses;
    
        $data['selectedCourse'] = $selectedCourse;
        $data['type'] = $type;
        $data['slug'] = $slug;
        $timezones_array = timezones_array();
        $data['timeZonesArray'] = $timezones_array;

         // Get available time slots (next 30 days)
         $startDate = now();
         $endDate = now()->addDays(30);

     
         $availableSlots = TimeSlot::where('is_active', true)
         ->orderBy('start_time')
         ->get();
   
         $data['availableSlots'] = $availableSlots;
        //  dd($data);
        return view('frontend.auth.signup',$data); 
     
        // return view('auth.sign-up-as-instructor', $data);
    }



  






    public function signUpAsStudentHome(Request $request, $type = null , $slug = null)
    {

     
     
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            // 'country' => 'required|string',
            'course' => 'required|string',
        ]);
     
        $form1Data = [
            'fullname' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            // 'country' => $request->input('country'),
            'course' => $request->input('course'),
            'free_signup' => 1,
        ];
        
        $lead = new Lead;
        $lead->name = $request->input('name');
        $lead->email = $request->input('email');
        $lead->phone = $request->input('phone');
        $lead->course_id = $request->input('course');
        $lead->save();

        return redirect()->route('sign-up-as-student')->with('form1Data', $form1Data);
        // return redirect()->route('sign-up-as-student')->withInput($form1Data);
    }


    

    public function signUpAsStudentLive(Request $request, $type = null , $slug = null)
    {
        $form1Data = $request->session()->get('form1Data', []);
        $data['form1Data'] = $form1Data;

        $user = Auth::user();
    
        if($user){
            $data['isUser'] = 1;
            $data['userID'] = $user->id;
            $checkStudent = Student::where('user_id',$data['userID'])->first();
            if($checkStudent){
                $data['studentID'] = $checkStudent->id;
            }
            else{
                $data['studentID'] = null;
            }
        }
        else{
            $data['isUser'] = 0;
            $data['userID'] = null; 
            $data['studentID'] = null;
        }
        $data['pageTitle'] = __('Sign Up As Student');
        $data['title'] = __('Student Sign Up');


        $data['countries'] = Country::all();
        $data['courses'] = Course::where('course_type',3)->where('status',1)->orderBy('id','DESC')->get();
        $data['type'] = $type;
        $data['slug'] = $slug;
        $timezones_array = timezones_array();
        $data['timeZonesArray'] = $timezones_array;

         // Get available time slots (next 30 days)
         $startDate = now();
         $endDate = now()->addDays(30);
         $availableSlots = TimeSlot::where('is_active', true)
         ->orderBy('start_time')
         ->get();
   
         $data['availableSlots'] = $availableSlots;
        return view('frontend.auth.signup',$data); 


        // $data['countries'] = Country::all();
        // $data['type'] = $type;
        // $data['slug'] = $slug;
        // $timezones_array = timezones_array();
        // $data['timeZonesArray'] = $timezones_array;

        // if($data['isUser']){
        //     return view('auth.register-live-with-user',$data); 
        // }
        // else{
        //     return view('auth.register-live',$data); 
        // }
    }


    public function signUpAsStudentBackUp()
    {
        $data['pageTitle'] = __('Sign Up As Student');
        $data['title'] = __('Student Sign Up');
        $data['countries'] = Country::all();
        $timezones_array = timezones_array();
        $data['timeZonesArray'] = $timezones_array;
        return view('auth.register',$data); 
        // return view('auth.sign-up-as-instructor', $data);
    }
    public function checkStudentEmail(Request $request)
    {
        $email = $request->input('email');
        $exists = User::where('email', $email)->first();
        if($exists){
            $unique = false;
        }
        else{
            $unique = true;
        }

        return response()->json(['unique' => $unique]);
    }


    public function checkStudentPhone(Request $request)
    {
        $phone = $request->input('phone');
    
        $exists = User::where('mobile_number', $phone)->first();
      
        if($exists){
            $unique = false;
        }
        else{
            $unique = true;
        }

        return response()->json(['unique' => $unique]);
    }



    public function checkStudentEmailUpdate(Request $request)
    {
        $email = $request->input('email');
        $studentID = $request->input('given_student_id');
        $getStudent = Student::where('id',$studentID)->first();
        $userID = $getStudent->user_id;

        $exists = User::where('email', $email)->where('id','<>',$userID)->first();
        if($exists){
            $unique = false;
        }
        else{
            $unique = true;
        }

        return response()->json(['unique' => $unique]);
    }




    public function checkStudentPhoneUpdate(Request $request)
    {
        $phone = $request->input('phone');
        $studentID = $request->input('given_student_id');
        $getStudent = Student::where('id',$studentID)->first();
        $userID = $getStudent->user_id;
     
        $exists = User::where('mobile_number', $phone)->where('id','<>',$userID)->first();
        if($exists){
            $unique = false;
        }
        else{
            $unique = true;
        }

        return response()->json(['unique' => $unique]);
    }


  



  




    public function storeSignUpAsStudentStep1(Request $request)
    {
        $user = new User();
        $type = 1;
        $email = $request->input('email');
        $user->name = $request->input('fname') . ' '. $request->input('lname');
        $user->email = $request->input('email');
        // $user->area_code =  str_replace("+","",$request->input('area_code'));
        $user->area_code =  $request->input('area_code');
        $user->mobile_number = $request->input('phone');
        $user->phone_number = $request->input('phone');
        $user->time_zone = $request->input('time_zone');
        $user->password = Hash::make($request->input('password'));
        $user->random_password = $request->input('password');
        $user->remember_token = $request->input('token');
        $user->email_verified_at = get_option('registration_email_verification') == 1 ? NULL : now();
        $user->role = 3;
    
        $st_type = $request->input('st_type');
        $st_type_count =  count($st_type);
        if($st_type_count == 2){
            $type = 3;
        }
        else{
            if(isset($st_type[0])){
                $type =  (int)$st_type[0];
            }
        }
   
        $user->save();
        $student_data = [
            'user_id' => $user->id,
            'first_name' => $request->input('fname'),
            'last_name' => $request->input('lname'),
            'phone_number' => $user->phone_number,
            'referral_code' => $request->input('referral_code') ?  $request->input('referral_code') : null,
            // 'status' => get_option('private_mode') ? STATUS_PENDING : STATUS_ACCEPTED
            'status' => 0,
            'type' => $type
        ];

        $this->studentModel->create($student_data);
        $getStudent = Student::where('user_id',$user->id)->first();
        $studentID = $getStudent->id;
     
        return response()->json(['status' => true , 'student_id' => $studentID , 'stType' => $type]);
        // $this->showToastrMessage('success', __('Your registration is successful.'));
        // return redirect(route('login'));
        // if(get_option('registration_system_bonus_mode', 0)){
        //     $balance = get_option('registration_bonus_amount');
        //     $user->increment('balance', decimal_to_int($balance));
        //     createTransaction($user->id, $balance, TRANSACTION_REGISTRATION_BONUS, 'Registration Bonus');
        // }

        // if (get_option('registration_email_verification') == 1){
        //     $sendEmail = new EmailSendService();
        //     if($sendEmail->sendVerifyEmail($user, route('user.email.verification',$user->remember_token))){
        //         $this->showToastrMessage('success', __('Sent verification mail your account. Please check your email.'));
        //     }else{
        //         toastrMessage('error', 'Something is wrong. Try after few minutes!');
        //         return redirect()->back();
        //     }
        // }
      
    }





    public function updateSignUpAsStudentStep1(Request $request)
    {
        $type = 1;
        $studentID = $request->input('new_student_id');
        $getStudent = Student::where('id',$studentID)->first();
        $userID = $getStudent->user_id;
        $getUser = User::where('id',$userID)->first();
     
        $email = $request->input('email');
        $getUser->name = $request->input('fname') . ' '. $request->input('lname');
        $getUser->email = $request->input('email');
        // $user->area_code =  str_replace("+","",$request->input('area_code'));
        $getUser->area_code =  $request->input('area_code');
        $getUser->mobile_number = $request->input('phone');
        $getUser->phone_number = $request->input('phone');
        $getUser->time_zone = $request->input('time_zone');
        $getUser->password = Hash::make($request->input('password'));
        $getUser->random_password = $request->input('password');
        $getUser->remember_token = $request->input('token');
        $getUser->email_verified_at = get_option('registration_email_verification') == 1 ? NULL : now();
        $getUser->role = 3;
    
        $st_type = $request->input('st_type');
        $st_type_count =  count($st_type);
        if($st_type_count == 2){
            $type = 3;
        }
        else{
            if(isset($st_type[0])){
                $type =  (int)$st_type[0];
            }
        }

        $getUser->update();
        $student_data = [
            'user_id' => $userID,
            'first_name' => $request->input('fname'),
            'last_name' => $request->input('lname'),
            'phone_number' => $getUser->phone_number,
            'referral_code' => $request->input('referral_code') ?  $request->input('referral_code') : null,
            // 'status' => get_option('private_mode') ? STATUS_PENDING : STATUS_ACCEPTED
            'status' => 0,
            'type' => $type
        ];

        $this->studentModel->update($student_data,$studentID);
        return response()->json(['status' => true , 'student_id' => $studentID , 'stType' => $type]);
      
      
    }





    public function updateSignUpAsStudentStep2(Request $request)
    {
      
        $studentID = $request->input('new_student_id');
        $stTypeUpdated = (int)$request->input('st_type_updated');
        $slugUpdated = $request->input('slug_updated');
        $getStudent = Student::where('id',$studentID)->first();
        $userID = $getStudent->user_id;
        $getUser = User::where('id',$userID)->first();
        $randomPassword = $getUser->random_password;
        $Email = $getUser->email;

        $no_of_joined_students = $request->input('no_of_joined_students');
        $preferred_teacher = $request->input('preferred_teacher');
        $learn_about_lms = $request->input('learn_about_lms');
    
     
        if($stTypeUpdated == 2){   // Self Learning Course Checkbox at Sign Up  First Step
            $route = '';
            $student_data = [
                'learn_about_lms' => $learn_about_lms,
                'status' => 1,
            ];
            $this->studentModel->update($student_data,$studentID);
            $userPassword = $getUser->random_password;
            $getUser->random_password = null;
            $getUser->update();
            if($slugUpdated != null){
                $route = route('course-details',['slug'=>$slugUpdated]);

                $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,$slugUpdated);
           
            }
            else{
                $route = route('get-thank-you-page');

                $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,null);
            
            }
            $redirectUrl = $response;
        
            if(get_option('registration_system_bonus_mode', 0)){
                $balance = get_option('registration_bonus_amount');
                $getUser->increment('balance', decimal_to_int($balance));
                createTransaction($getUser->id, $balance, TRANSACTION_REGISTRATION_BONUS, 'Registration Bonus');
            }

            $userEmail = $getUser->email;
            $template_name = 'student-sign-up';
            $viewData = [
                '{{USER_NAME}}' => $getUser->name,
                '{{EMAIL}}' => $userEmail,
                '{{PASSWORD}}' => $userPassword,
            ];
           
            sendEmailNotification($viewData,$userEmail,$template_name);

            //    return response()->json(['url'=>$route,'status' => true , 'student_id' => $studentID ,'message'=>__('Your registration is successful.') , 'stTypeUpdated' => $stTypeUpdated]);
            return response()->json(['url'=>$redirectUrl,'status' => true , 'student_id' => $studentID ,'message'=>__('Your registration is successful.') , 'stTypeUpdated' => $stTypeUpdated]);
        }  
        else{   // Live Course Checkbox at Sign Up  First Step
            $student_data = [
                'no_of_joined_students' => $no_of_joined_students,
                'preferred_teacher' => $preferred_teacher,
                'learn_about_lms' => $learn_about_lms,
            ];
    
            $this->studentModel->update($student_data,$studentID);
            return response()->json(['status' => true , 'student_id' => $studentID ,'stTypeUpdated' => $stTypeUpdated]);
        }
      
      
      
    }

    public function updateSignUpAsStudentStep4(Request $request)
    {
     
      
       
        $studentID = $request->input('new_student_id');
        $slugUpdated = $request->input('slug_updated');
        $getCourse = Course::where('slug',$slugUpdated)->first();
        if($getCourse){
            $course_id = $getCourse->id;
        }
        else{
            $course_id = null; 
        }
     
        $getStudent = Student::where('id',$studentID)->first();
        $userID = $getStudent->user_id;
        $getUser = User::where('id',$userID)->first();
        $randomPassword = $getUser->random_password;
        $userPassword = $randomPassword;
        $Email = $getUser->email;
        $stTypeUpdated = $getStudent->type;
        $getUser->random_password = null;
        $getUser->update();

        // $route = route('get-thank-you-page');
        $preferred_date = $request->input('preferred_date');
        $date = Carbon::createFromFormat('Y-n-j', $preferred_date);
        $formattedDate = $date->format('Y-m-d');
        $preferred_time = $request->input('preferred_time');
        $time = Carbon::createFromFormat('g:i A', $preferred_time);
        $formattedTime = $time->format('H:i A');
     
      
        $student_data = [
            'status' => 1,
        ];
        $this->studentModel->update($student_data,$studentID);
      
        $trialClass = new TrialClass;
        $trialClass->student_id = $studentID;
        $trialClass->prefer_date = $formattedDate;
        $trialClass->prefer_time = $formattedTime;
        $trialClass->course_id = $course_id;
        $trialClass->save();
      


        $route = '';
        if($slugUpdated != null){
            $route = route('course-details',['slug'=>$slugUpdated]);
            $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,$slugUpdated);
        }
        else{
            $route = route('get-thank-you-page');
            $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,$slugUpdated);
        }
        $redirectUrl = $response;
        
        $text = __("Student Free Class Request");
        $target_url = route('student.free_classes');

        if(get_option('registration_system_bonus_mode', 0)){
            $balance = get_option('registration_bonus_amount');
            $getUser->increment('balance', decimal_to_int($balance));
            createTransaction($getUser->id, $balance, TRANSACTION_REGISTRATION_BONUS, 'Registration Bonus');
        }

        $userEmail = $getUser->email;
        $template_name = 'student-sign-up-with-trial';
        $viewData = [
            '{{USER_NAME}}' => $getUser->name,
            '{{EMAIL}}' => $userEmail,
            '{{PASSWORD}}' => $userPassword,
        ];
       
        sendEmailNotification($viewData,$userEmail,$template_name);

        // if (get_option('registration_email_verification') == 1){
        //     $sendEmail = new EmailSendService();
        //     if($sendEmail->sendVerifyEmail($getUser, route('user.email.verification',$getUser->remember_token))){
        //         $this->send($text, 1, $target_url, null , 1);
        //         // $this->showToastrMessage('success', __('Sent verification mail your account. Please check your email.'));
        //         return response()->json(['url'=>$route,'status' => true , 'student_id' => $studentID ,'message'=>__('Sent verification mail your account. Please check your email.')]);
        //     }else{
        //         $this->send($text, 1, $target_url, null , 1);
        //         // toastrMessage('error', 'Something is wrong. Try after few minutes!');
        //         return response()->json(['url'=>$route,'status' => false , 'student_id' => $studentID ,'message'=>__('Something went wrong during verification mail. Try after few minutes!')]);
        //         // return redirect()->back();
        //     }
        // }
        $this->send($text, 1, $target_url, null , 1);
        // return response()->json(['url'=>$route,'status' => true , 'student_id' => $studentID ,'message'=>__('Your registration is successful.')]);
        return response()->json(['url'=>$redirectUrl,'status' => true , 'student_id' => $studentID ,'message'=>__('Your registration is successful.')]);
        // $this->showToastrMessage('success', __('Your registration is successful.'));
      
      
      
    }









    public function updateSignUpAsStudentLiveStep4(Request $request)
    {
     
      
       
        $studentID = $request->input('new_student_id');
        $slugUpdated = $request->input('slug_updated');
        $getCourse = Course::where('slug',$slugUpdated)->first();
        if($getCourse){
            $course_id = $getCourse->id;
        }
        else{
            $course_id = null; 
        }
     
        $getStudent = Student::where('id',$studentID)->first();
        $userID = $getStudent->user_id;
        $getUser = User::where('id',$userID)->first();
        $randomPassword = $getUser->random_password;
        $Email = $getUser->email;
        $stTypeUpdated = $getStudent->type;
        $userPassword = $getUser->random_password;
        $getUser->random_password = null;
        $getUser->update();

        // $route = route('get-thank-you-page');
        $preferred_date = $request->input('preferred_date');
        $date = Carbon::createFromFormat('Y-n-j', $preferred_date);
        $formattedDate = $date->format('Y-m-d');
        $preferred_time = $request->input('preferred_time');
        $time = Carbon::createFromFormat('g:i A', $preferred_time);
        $formattedTime = $time->format('H:i A');
     
      
        $student_data = [
            'status' => 1,
        ];
        $this->studentModel->update($student_data,$studentID);
      
        $trialClass = new TrialClass;
        $trialClass->student_id = $studentID;
        $trialClass->prefer_date = $formattedDate;
        $trialClass->prefer_time = $formattedTime;
        $trialClass->course_id = $course_id;
        $trialClass->save();
      


        $route = '';
        if($slugUpdated != null){
            $route = route('course-details',['slug'=>$slugUpdated]);
            $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,$slugUpdated);
        }
        else{
            $route = route('get-thank-you-page');
            $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,$slugUpdated);
        }
        $redirectUrl = $response;
        
        $text = __("Student Free Class Request");
        $target_url = route('student.free_classes');

        if(get_option('registration_system_bonus_mode', 0)){
            $balance = get_option('registration_bonus_amount');
            $getUser->increment('balance', decimal_to_int($balance));
            createTransaction($getUser->id, $balance, TRANSACTION_REGISTRATION_BONUS, 'Registration Bonus');
        }
      
        $this->send($text, 1, $target_url, null , 1);
   
        $userEmail = $getUser->email;
        $template_name = 'student-sign-up-with-trial';
        $viewData = [
            '{{USER_NAME}}' => $getUser->name,
            '{{EMAIL}}' => $userEmail,
            '{{PASSWORD}}' => $userPassword,
        ];
       
        sendEmailNotification($viewData,$userEmail,$template_name);

        return response()->json(['url'=>$redirectUrl,'status' => true , 'student_id' => $studentID ,'message'=>__('Your registration is successful with free class request.')]);    
    }




    public function updateSignUpAsStudentLiveStep4WithUser(Request $request)
    {
     
      
       
        $studentID = $request->input('new_student_id');
        $slugUpdated = $request->input('slug_updated');
        $getCourse = Course::where('slug',$slugUpdated)->first();
        if($getCourse){
            $course_id = $getCourse->id;
        }
        else{
            $course_id = null; 
        }
     
        $getStudent = Student::where('id',$studentID)->first();
        $userID = $getStudent->user_id;
        $getUser = User::where('id',$userID)->first();
        $randomPassword = $getUser->random_password;
        $Email = $getUser->email;
        $stTypeUpdated = $getStudent->type;
        $getUser->random_password = null;
        $getUser->update();

        // $route = route('get-thank-you-page');
        $preferred_date = $request->input('preferred_date');
        $date = Carbon::createFromFormat('Y-n-j', $preferred_date);
        $formattedDate = $date->format('Y-m-d');
        $preferred_time = $request->input('preferred_time');
        $time = Carbon::createFromFormat('g:i A', $preferred_time);
        $formattedTime = $time->format('H:i A');
     
      
        $student_data = [
            'status' => 1,
        ];
        $this->studentModel->update($student_data,$studentID);
      
        $trialClass = new TrialClass;
        $trialClass->student_id = $studentID;
        $trialClass->prefer_date = $formattedDate;
        $trialClass->prefer_time = $formattedTime;
        $trialClass->course_id = $course_id;
        $trialClass->save();
      


        $route = '';
        // if($slugUpdated != null){
        //     $route = route('course-details',['slug'=>$slugUpdated]);
        //     $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,$slugUpdated);
        // }
        // else{
        //     $route = route('get-thank-you-page');
        //     $response = $this->redirectLogin($Email,$randomPassword,$stTypeUpdated,$slugUpdated);
        // }
        // $redirectUrl = $response;
        
        $text = __("Student Free Class Request");
        $target_url = route('student.free_classes');

        if(get_option('registration_system_bonus_mode', 0)){
            $balance = get_option('registration_bonus_amount');
            $getUser->increment('balance', decimal_to_int($balance));
            createTransaction($getUser->id, $balance, TRANSACTION_REGISTRATION_BONUS, 'Registration Bonus');
        }
      
        $this->send($text, 1, $target_url, null , 1);
        if($slugUpdated != null){
            $redirectUrl = route('course-details',['slug'=>$slugUpdated]);
            // return redirect(route('course-details',['slug'=>$slugUpdated]));
        }
        else{
            // return redirect(route('main.index'));
            $redirectUrl = route('course-details',['slug'=>$slugUpdated]);
        }
        return response()->json(['url'=>$redirectUrl,'status' => true , 'student_id' => $studentID ,'message'=>__('Successfully Booked free class.')]);    
    }

    public function redirectLogin($email,$password,$stTypeUpdated,$slugUpdated = null){
        $credentials = [
            'email'=>$email,
            'password'=>$password,
        ];
   
        if (Auth::attempt($credentials)) {
         
        
            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_REJECTED){
                Auth::logout();
               
                if($slugUpdated != null){
                    $this->showToastrMessage('error', __('Your account has been blocked!'));
                    // return redirect(route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]));
                    return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
                }
                else{
                    $this->showToastrMessage('error', __('Your account has been blocked!'));
                    // return redirect("login");
                    return route('login');
                }
            }

            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_PENDING){
                Auth::logout();
                if($slugUpdated != null){
                    $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                    // return redirect(route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]));
                    return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
                }
                else{
                    $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                    return route('login');
                }
            }


            if (Auth::user()->role == USER_ROLE_INSTRUCTOR && Auth::user()->instructor->status == STATUS_REJECTED){
                Auth::logout();
                $this->showToastrMessage('error', __('Your account has been blocked!'));
                return route('login');
            }
            if (Auth::user()->role == 5 && Auth::user()->coordinator->status == 0){
                Auth::logout();
                $this->showToastrMessage('error', __('Your account has been blocked!'));
                return route('login');
            }

          
            if (Auth::user()->is_admin())
            {
                // return redirect(route('admin.dashboard'));
                return route('admin.dashboard');

            } 
            else {
             
                if($slugUpdated != null){
                
                    // return redirect(route('course-details',['slug'=>$slugUpdated]));
                    return route('course-details',['slug'=>$slugUpdated]);
                }
                else{
                    // return redirect(route('main.index'));
                    return route('main.index');
                }
            }
        }
        if($slugUpdated != null){
            $this->showToastrMessage('error', __('Ops! You have entered invalid credentials'));
            // return redirect(route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]));
            return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
        }
        else{
            $this->showToastrMessage('error', __('Ops! You have entered invalid credentials'));
            return route('login');
        }  
    }

    
    public function getThankYouPage(Request $request){
        return view('auth.thankyou');
    }

  



     public function redirectLogin2($email,$password,$redirectType,$stTypeUpdated,$slugUpdated = null){
        $credentials = [
            'email'=>$email,
            'password'=>$password,
        ];
   
        if (Auth::attempt($credentials)) {
         
        
            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_REJECTED){
                Auth::logout();
               
                if($slugUpdated != null){
                    $this->showToastrMessage('error', __('Your account has been blocked!'));
                    // return redirect(route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]));
                    // return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
                    return redirect(route('course-details',['slug'=>$slugUpdated]));
                }
                else{
                    $this->showToastrMessage('error', __('Your account has been blocked!'));
                    // return redirect("login");
                    // return route('login');
                    return redirect("student-login");
                }
            }

            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_PENDING){
              
                if($slugUpdated != null){
                    if($redirectType == 'course-detail-page'){
                        Auth::logout();
                        $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                        return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
                    }
                    else if($redirectType == 'course-detail-page-live'){
                        $this->showToastrMessage('success', __("Registration successful. Enrollment Request Sent To Admin. Wait For Approval"));
                        return route('course-details',['slug'=>$slugUpdated]);
                    }
                    else if($redirectType == 'check-out-page'){
                        $this->showToastrMessage('success', 'Welcome to CEF Online, please complete paid self learning course payment.');
                        return route('student.cartList');
                    }
                    else if($redirectType == 'main-page'){
                        Auth::logout();
                        $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                        return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
                    }
                    else{
                        Auth::logout();
                        $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                        return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
                    }
                
                
                }
                else{
                    if($redirectType == 'course-detail-page-live'){
                        $this->showToastrMessage('success', __("Registration successful. Enrollment Request Sent To Admin. Wait For Approval"));
                        return route('main.index');
                    }
                    else{
                        Auth::logout();
                        $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                        return route('login');
                    }
                   
                }
           
            }


            if (Auth::user()->role == USER_ROLE_INSTRUCTOR && Auth::user()->instructor->status == STATUS_REJECTED){
                Auth::logout();
                $this->showToastrMessage('error', __('Your account has been blocked!'));
                return route('login');
            }
            if (Auth::user()->role == 5 && Auth::user()->coordinator->status == 0){
                Auth::logout();
                $this->showToastrMessage('error', __('Your account has been blocked!'));
                return route('login');
            }

          
            if (Auth::user()->is_admin())
            {
                // return redirect(route('admin.dashboard'));
                return route('admin.dashboard');

            } 
            else {
             
                if($slugUpdated != null){
                    
                    if($redirectType == 'course-detail-page'){
                        return route('course-details',['slug'=>$slugUpdated]);
                    }
                    else if($redirectType == 'course-detail-page-live'){
                        return route('course-details',['slug'=>$slugUpdated]);
                    }
                    else if($redirectType == 'check-out-page'){
                        return route('student.cartList');
                    }
                    else if($redirectType == 'main-page'){
                        return route('main.index');
                    }
                    else{
                         return route('course-details',['slug'=>$slugUpdated]);
                    }
                   
                   
                }
                else{
                    // return redirect(route('main.index'));
                    return route('main.index');
                }
            }
        }

        if($slugUpdated != null){
            $this->showToastrMessage('error', __('Ops! You have entered invalid credentials'));
            // return redirect(route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]));
            return route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]);
        }
        else{
            $this->showToastrMessage('error', __('Ops! You have entered invalid credentials'));
            return route('login');
        }  
    }



    // New Sign Up As Student 

public function storeSignUpAsStudent(Request $request)
{
   
    // Form validation
    $request->validate([
        'fullname' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:6',
        'phone' => 'nullable|string|unique:users,mobile_number',
        // 'timezone' => 'required|string',
        'country' => 'required|exists:countries,id',
        'study_type' => 'required|in:1,2',
        'slug' => 'string|nullable',
        // 'referral' => 'string|nullable',
   
    ], 
    [
        'fullname.required' => 'Your full name is required.',
        'email.required' => 'Your email address is required.',
        'email.email' => 'Please provide a valid email address.',
        'email.unique' => 'This email is already registered.',
        'password.required' => 'Please enter a password.',
        'password.confirmed' => 'Passwords do not match.',
        'password.min' => 'Your password must be at least 6 characters long.',
        // 'timezone.required' => 'Please select your timezone.',
        'country.required' => 'Please select your country.',
        'study_type.required' => 'Please select a study type (Live or Self-Learning).',
        'study_type.in'       => 'Invalid study type selection.',
      
     
    ]);

  
    $study_type = (int) $request->input('study_type'); // 1 for live, 2 for self-learning
    $type = $study_type;
    if($type == 1){
        $request->validate([
            'phone' => 'required|string|unique:users,mobile_number',
            'city' => 'required|string|max:255',
            'age' => 'required|numeric',
            'school_grade' => 'nullable|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            'learning_goals' => 'required',
            'how_many_students' => 'numeric|min:1',
            'how_many_students' => 'numeric|nullable',
            'preferred_teacher' => 'string|nullable',
            'preferDate' => 'required|date',
            'preferSlot' => 'required|numeric',
        ], 
        [
            'phone.required' => 'Your phone number is required.',
            'city.required' => 'City Field is required.',
            'age.required' => 'Age Field is required.',
            'age.numeric' => 'Age Field Must Be Numeric.',
            'learning_goals.required' => 'Learning Goals is required.',
            'how_many_students.numeric' => 'Please enter a valid number of students.',
            'how_many_students.min' => 'You must add at least 1 student.',
            'preferDate.required' => 'Please select calender date.',
            'preferDate.date' => 'Please select a valid date.',
            'preferSlot.required' => 'Please select time slot.',
            'preferSlot.numeric' => 'Please choose a valid slot.',
        ]);
    }
    else{
        
    }
 

    try {
   
        DB::beginTransaction();  // Start transaction to ensure atomicity
     
        $checkEmail = User::where('email',$request->email)->first();
        if($checkEmail){
            $this->showToastrMessage('error', __('Given Email Already Exists'));
            return redirect()->back();
     
        }


      
        
        $study_type = $request->input('study_type');
        if(empty($study_type)){
            $this->showToastrMessage('error', ___('Please select at least one student type.'));
            return redirect()->back();
         
        }

       
        // Extract first and last name from fullname
        $nameParts = explode(' ', $request->input('fullname'), 2);
        $fname = $nameParts[0];
        $lname = $nameParts[1] ?? '';

        $preferDateInput = $request->input('preferDate'); // Example: "March 2025 25"
        // $formattedPreferDate = $preferDateInput ? Carbon::createFromFormat('F Y d', $preferDateInput)->format('Y-m-d') : null;


      if($request->input('phone')){
            $areaCode2 = $request->input('area_code_2');
            // If the value doesn't start with '+', prepend it
            if (!str_starts_with($areaCode2, '+')) {
                $areaCode2 = '+' . ltrim($areaCode2, '+'); // ltrim removes any existing + just in case
            }
            $PhoneNumber = $request->input('phone');

            // Case 4: Already has + and area code (+923125577435) - do nothing
            if (str_starts_with($PhoneNumber, $areaCode2)) {
                // Format is correct, no changes needed
            }
            // Case 3: Has area code without + (923125577435) - just add +
            elseif (str_starts_with($PhoneNumber, ltrim($areaCode2, '+'))) {
                $PhoneNumber = '+' . $PhoneNumber;
            }
            // Case 2: Starts with 0 (03125577435) - remove 0 and add area code
            elseif (str_starts_with($PhoneNumber, '0')) {
                $PhoneNumber = $areaCode2 . substr($PhoneNumber, 1);
            }
            // Case 1: Just local number (3125577435) - add full area code
            else {
                $PhoneNumber = $areaCode2 . $PhoneNumber;
            }

            $checkPhone = User::where('mobile_number',$PhoneNumber)->first();
            if($checkPhone){
                $this->showToastrMessage('error', __('Given WhatsApp Number Already Exists'));
                return redirect()->back();
            
            }
      }
      else{
            $areaCode2 = null;
            $PhoneNumber = null;
      }
      
    
    
        $timeZONE = 'Asia/Karachi';
        // Create new User
        $user = new User();
        $user->name = $request->input('fullname');
        $user->email = $request->input('email');
        // $user->area_code = $request->input('area_code_2');
        $user->area_code = $areaCode2;
        // $user->area_code = '+1'; // Can be handled if added in the form
        // $user->mobile_number = $request->input('phone');
        $user->mobile_number = $PhoneNumber;
        $user->time_zone = $timeZONE;
        $user->password = Hash::make($request->input('password'));
        $user->random_password = $request->input('password');
        $user->remember_token = $request->input('_token');
        $user->email_verified_at = get_option('registration_email_verification') ? null : now();
        $user->role = 3;


        $study_type = (int) $request->input('study_type'); // 1 for live, 2 for self-learning
        $type = $study_type;
      
        $user->save();
      
        $course_id_given = $request->input('course');
     
        // Create Student Record
        $student = $this->studentModel->create([
            'user_id' => $user->id,
            'first_name' => $fname,
            'last_name' => $lname,
            'phone_number' => $user->mobile_number,
            // 'referral_code' => $request->input('referral') ?? null,
            'status' => 1,  
            'type' => $type,
            'city' => $request->input('city'),
            'age' => $request->input('age'),
            'school_grade' => $request->input('school_grade'),
            'parent_name' => $request->input('parent_name'),
            'country_id' => $request->input('country'),
            'learning_goals' => $request->input('learning_goals'),
            // 'lms_uses' => $request->input('what_will_you_use'),
            'no_of_joined_students' => $request->input('how_many_students'),
            'preferred_teacher' => $request->input('preferred_teacher'),
            // 'learn_about_lms' => $request->input('learn_about_lms'),
        ]);

        $slugUpdated = null;
        $course = Course::where('id', $request->input('slug'))->first();
        if($course){
            $course_id = $course->id;
        }
        else{
            $course_id = $course_id_given; 
        }
        $course = Course::find($course_id);
        if (!$course) {
            return back()->withErrors(__('Course ID Not Found.'));
                // return $this->error([], __("Course not found!"), 404);
        }
        else{
            $slugUpdated = $course->slug;
        }

        // Handle study preferences and trial classes
        if ($type == 1 || $type == 3) { // Live course type with trial setup
          
            // $course = Course::where('slug', $request->input('slug'))->first();
            $course = Course::where('id', $request->input('slug'))->first();
            if($course){
                $course_id = $course->id;
            }
            else{
                $course_id = $course_id_given; 
            }
            $getCourse = Course::where('id',$course_id)->first();
            if($getCourse){
                $courseTITLE = $getCourse->title;
            }
            else{
                $courseTITLE = 'NONE';
            }
            $getTimeSlotInfo = TimeSlot::where('id',$request->preferSlot)->first();
           
            $preferTime = $getTimeSlotInfo->start_time;
            TrialClass::create([
                'student_id' => $student->id,
                'prefer_date' => $preferDateInput, // Fixed date format
                'prefer_time' => $preferTime,
                'course_id' => $course_id,
            ]);

            BookedSlot::create([
                'time_slot_id' => $getTimeSlotInfo->id,
                'user_id' => $user->id,
                'status' => 'booked'
            ]);

            // $student->update(['status' => 1]); // Update status to active
              // Send Welcome Email
            // $student->update(['status' => 0]); 
            $student->update(['pending_from_enrollment' => 1]); 
       
            DB::commit();

            if($type == 1 || $type == 3){
                $template_name =  'student-sign-up-with-trial';
            }
            else{
                $template_name =  'student-sign-up'; 
            }
        
            
            sendEmailNotification([
                '{{USER_NAME}}' => $user->name,
                '{{COURSE}}' => $courseTITLE,
                // '{{EMAIL}}' => $user->email,
                // '{{PASSWORD}}' => $request->input('password'),
            ], $user->email, $template_name);

            // $this->send(__('Student Free Class Request'), 1, route('student.free_classes'), null, 1);
            $this->send(__('Student Enrollment Request For Live Course'), 1, route('student.free_classes'), null, 1);

            $getStudentSchedule = TrialClass::where('student_id',$student->id)->where('course_id',$course_id)->first();
            if($getStudentSchedule){
                $studentSchedule =  date('d-m-Y',strtotime($getStudentSchedule->prefer_date)).' ( '.$getStudentSchedule->prefer_time. ')';
            }
            else{
                $studentSchedule = 'NONE';
            }
            $getAdminUsers = User::where('role',1)->get();
            if(!$getAdminUsers->isEmpty()){
                $template_name2 =  'student-enrollment-request'; 
                foreach ($getAdminUsers as $key => $getAdminUser) {
                    sendEmailNotification([
                        '{{NAME}}' => $user->name,
                        '{{AGE}}' => $student->age,
                        '{{CONTACT}}' => $student->phone_number,
                        '{{SCHEDULE}}' => $studentSchedule,
                        '{{COURSE}}' => $courseTITLE,
                        '{{GOALS}}' => $student->learning_goals,
                    ], $getAdminUser->email, $template_name2);
                }      
            }

            $msg = __("Registration successful. Enrollment Request Sent To Admin. Wait For Approval");
            $this->showToastrMessage('success', __($msg));
            if($request->freeSignUp){
                $lead = new Lead;
                $lead->name = $user->name;
                $lead->email = $user->email;
                $lead->phone = $user->mobile_number;
                $lead->course_id = $course_id;
                $lead->lead_from = 'free_signup_form';
                $lead->save();
            }
            if($type && $slugUpdated){
                $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'course-detail-page-live',$type,$slugUpdated);
                return redirect()->to($redirectUrl);
            }
            else{
                $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'main-page',$type,$slugUpdated);
                return redirect()->to($redirectUrl);
            }
            // DB::commit();
            // $msg = __("Registration successful. Trial Class Created For Live Course !");
            // $this->showToastrMessage('success', __($msg));
            // $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'main-page',$type,$slugUpdated);
            // return redirect()->to($redirectUrl);
        } 
        else {
          
            $course = Course::where('id', $request->input('slug'))->first();
            if($course){
                $course_id = $course->id;
            }
            else{
                $course_id = $course_id_given; 
            }
            $course = Course::find($course_id);
          
             if (!$course) {
                return back()->withErrors(__('Course ID Not Found.'));
                    // return $this->error([], __("Course not found!"), 404);
            }
        
            $enrollment = Enrollment::where(['course_id' => $course_id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
            if ($enrollment) {
                 $order = Order::find($enrollment->order_id);
                    if ($order) {
                        if ($order->payment_status == 'due') {
                            Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                                $q->delete();
                            });
                            $order->delete();
                        } elseif ($order->payment_status == 'pending') {
                            DB::rollBack();
                            $this->showToastrMessage('success', __("You've already request the course & payment_status is pending!"));
                             return redirect()->route('sign-up-as-student');
                            // $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'sign-up-page',$type,$slugUpdated);
                            // return redirect()->to($redirectUrl);
                         
                        } 
                        elseif ($order->payment_status == 'paid') {
                            $msg = __("You've already bought the course!");
                            DB::rollBack();
                             $this->showToastrMessage('success', __($msg));
                            //  return redirect()->route('sign-up-as-student');
                            $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'course-detail-page',$type,$slugUpdated);
                            return redirect()->to($redirectUrl);
                        }
                        elseif ($order->payment_status == 'free') {
                            $msg = __("You've already enrolled the course!");
                            DB::rollBack();
                             $this->showToastrMessage('success', __($msg));
                            //  return redirect()->route('sign-up-as-student');
                            $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'course-detail-page',$type,$slugUpdated);
                            return redirect()->to($redirectUrl);
                        }
                    } else {
                        $msg = __("Something is wrong! Try again.");
                        DB::rollBack();
                        $this->showToastrMessage('error', __($msg));
                        return redirect()->route('sign-up-as-student');
                        // return $this->failed([], $msg);
                    }
            }
            $ownCourseCheck = CourseInstructor::where('course_id', $course_id)->where('instructor_id', $user->id)->delete();
            if ($ownCourseCheck) {
                $msg = __("This is your course. No need to add to cart.");
                DB::rollBack();
                  $this->showToastrMessage('success', __($msg));
                return redirect()->route('sign-up-as-student');
                // return $this->success([], $msg);
            }
             $cartExists = CartManagement::whereUserId($user->id)->whereCourseId($course_id)->first();
              if ($cartExists) {
                $msg = __("Already added to cart!");
                DB::rollBack();
                $this->showToastrMessage('success', __($msg));
                // return redirect()->route('sign-up-as-student');
                 $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'check-out-page',$type,$slugUpdated);
                return redirect()->to($redirectUrl);
            }

            if ($course->learner_accessibility == 'free') {
                    $order = new Order();
                    $order->user_id = $user->id;
                    $order->order_number = rand(100000, 999999);
                    $order->payment_status = 'free';
                    $order->save();
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = $user->id;
                    $order_item->course_id = $course->id;
                    $order_item->owner_user_id = $course->user_id ?? null;
                    $order_item->unit_price = 0;
                    $order_item->admin_commission = 0;
                    $order_item->owner_balance = 0;
                    $order_item->sell_commission = 0;
                    $order_item->request_enrollment = 0;
                    $order_item->save();
                    $enrollment = Enrollment::where('user_id',$user->id)->where('course_id',$course_id)->orderBy('id','DESC')->first();
                    if($enrollment){
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = $user->id;
                        $enrollment->course_id = $course_id;
                        $enrollment->owner_user_id = $course->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($course->access_period) ? Carbon::now()->addDays($course->access_period) : MAX_EXPIRED_DATE;
                        
                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;
                        $enrollment->request_enrollment = (int) ($order_item->request_enrollment ?? 0);
    
                        $enrollment->save();
                    }
                    else{
                        $enrollment = new Enrollment();
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = $user->id;
                        $enrollment->course_id = $course_id;
                        $enrollment->owner_user_id = $course->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($course->access_period) ? Carbon::now()->addDays($course->access_period) : MAX_EXPIRED_DATE;

                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;
                        $enrollment->request_enrollment = (int) ($order_item->request_enrollment ?? 0);

                        $enrollment->save();
                        Log::info("NEW ENROLLMENT BEING CREATED IN (storeSignApAsStudent) IN FRONTEND/REGISTRATIONCONTROLLER AT ".Carbon::now());
                        Log::info("NEW ENROLLMENT DETAILS IN (storeSignApAsStudent) IN FRONTEND/REGISTRATIONCONTROLLER:- ".$enrollment);
                    }
                   

                    $msg = __("Registration successful. Free Course added to your dashboard!");

                     // Send Welcome Email
                    if($type == 1 || $type == 3){
                        $template_name =  'student-sign-up-with-trial';
                    }
                    else{
                        $template_name =  'student-sign-up'; 
                    }
                
                    sendEmailNotification([
                        '{{USER_NAME}}' => $user->name,
                        '{{EMAIL}}' => $user->email,
                        '{{PASSWORD}}' => $request->input('password'),
                    ], $user->email, $template_name);
                
                    $this->send(__('Student Free Class Request'), 1, route('student.free_classes'), null, 1);
                    $student->update(['status' => 1]); // Activate self-learning type

                    DB::commit();
                     $this->showToastrMessage('success', __($msg));
                     $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'course-detail-page',$type,$slugUpdated);
                      return redirect()->to($redirectUrl);
                  
            }
               
            $cart = new CartManagement();
            $cart->user_id = $user->id;
            $cart->course_id = $course_id;
            $cart->is_subscription_enable = $course->is_subscription_enable;
            $cart->main_price = $course->price;
            $cart->price = $course->price;
            $cart->month = date('m');
            $cart->year = date('Y');
            $cart->save();
            $msg = __("Welcome to CEF Online, please complete paid self learning course payment.");



             // Send Welcome Email
            // if($type == 1 || $type == 3){
            //     $template_name =  'student-sign-up-with-trial';
            // }
            // else{
            //     $template_name =  'student-sign-up'; 
            // }
        
            // sendEmailNotification([
            //     '{{USER_NAME}}' => $user->name,
            //     '{{EMAIL}}' => $user->email,
            //     '{{PASSWORD}}' => $request->input('password'),
            // ], $user->email, $template_name);

            // $this->send(__('Student Free Class Request'), 1, route('student.free_classes'), null, 1);


            $student->update(['status' => 0]);
            $student->update(['pending_from_enrollment' => 1]);  
            DB::commit();
            $this->showToastrMessage('success', __($msg));
            $redirectUrl = $this->redirectLogin2($user->email,$user->random_password,'check-out-page',$type,$slugUpdated);
            return redirect()->to($redirectUrl);

        }

        // Send Bonus if enabled
        // if (get_option('registration_system_bonus_mode', 0)) {
        //     $balance = get_option('registration_bonus_amount');
        //     $user->increment('balance', decimal_to_int($balance));
        //     createTransaction($user->id, $balance, TRANSACTION_REGISTRATION_BONUS, 'Registration Bonus');
        // }

    
      
       return redirect()->route('main.index');
        // return redirect()->to($redirectUrl);
    } catch (\Exception $e) {
        DB::rollBack();  // Rollback transaction on error
        dd('Student Registration Error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        // Log the error for debugging
        Log::error('Student Registration Error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);

        return back()->withErrors(__('An unexpected error occurred. Please try again later.'));
    }
}





    public function storeSignUpAsInstructor(Request $request)
{
            // Form validation
    $formType = $request->input('signup_type','teacher');
    $request->session()->flash('action_type', $formType);
  
    $validationRules = [
        'first_name' => 'required|string|min:2|max:100',
        'last_name' => 'required|string|min:2|max:100',
        'account_type' => 'required',
        'professional_title' => 'required|string|min:2|max:100',
        'address' => 'required|string',
        'cv_file' => 'required|max:10000|mimetypes:application/pdf',
        'about_me' => 'required|string',
        'email' => 'required|string|email|max:255|unique:users,email',
        'password' => 'required|confirmed|min:6',
    ];
    $messages = [
        'first_name.required' => 'Your first name is required.',
        'last_name.required' => 'Your last name is required.',
        'account_type.required' => 'Please select your account type.',
        'professional_title.required' => 'Please enter your professional title.',
        'address.required' => 'Please enter your address.',
        'cv_file.required' => 'Please select your cv_file.',
        'cv_file.max' => 'CV_file must be under 10 mb.',
        'cv_file.mimetypes' => 'CV_file must be a pdf type.',
        'about_me.required' => 'Please enter your bio.',
        'email.required' => 'Your email address is required.',
        'email.email' => 'Please provide a valid email address.',
        'email.unique' => 'This email is already registered.',
        'password.required' => 'Please enter a password.',
        'password.confirmed' => 'Passwords do not match.',
        'password.min' => 'Your password must be at least 6 characters long.',
    ];
   
     // Validate the request
     $validatedData = $request->validate($validationRules, $messages);
    // Store which form was active in session
    $request->session()->flash('action_type', $formType);

   

    if(!is_null($request->input('mobile_number')) || !is_null($request->input('area_code'))){
        $validationRules2 = [
            'area_code' => 'required',
            'mobile_number' => 'bail|numeric|unique:users,mobile_number',
        ];
        $messages2 = [
            'area_code.required' => 'Please select your area code.',
            'mobile_number.required' => 'Please enter your phone number.',
            'mobile_number.unique' => 'This phone number already exists.',
        ];
        $validatedData2 = $request->validate($validationRules2, $messages2);
        $request->session()->flash('action_type', $formType);
    }

        $user = new User();
        $accountType = (int)$request->account_type;
      
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        // $user->area_code =  str_replace("+","",$request->area_code);
        $user->area_code =  $request->area_code_3;
        $user->mobile_number = $request->mobile_number;
        $user->phone_number = $request->mobile_number;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        $user->remember_token = $request->_token;
        $user->email_verified_at = get_option('registration_email_verification') == 1 ? NULL : now();
        if($accountType == 2){
            $user->role = 2;
        }
        else{
            $user->role = 4; 
        }
        $user->save();
        $userID = $user->id;
        $getUser = User::where('id',$userID)->first();

        if($request->account_type == USER_ROLE_ORGANIZATION){
            $object = Organization::where('user_id', $getUser->id)->get();
        }
        else{
            $object = Instructor::where('user_id', $getUser->id)->get();
        }

        if ($object->count() > 0){
            $this->showToastrMessage('success', __('Request already send'));
            return redirect(route('login'));
        } else {
            if($request->account_type == USER_ROLE_ORGANIZATION){
                $slugCount = Organization::where('slug', getSlug($getUser->name))->count();
            }
            else{
                $slugCount = Instructor::where('slug', getSlug($getUser->name))->count();
            }

            if ($slugCount)
            {
                $slug = getSlug($getUser->name) . '-'. rand(100000, 999999);
            } else {
                $slug = getSlug($getUser->name);
            }

            $cv_file_data = $this->uploadFileWithDetails('user', $request->cv_file);
            if (!$cv_file_data['is_uploaded']) {
                $this->showToastrMessage('error', __('Something went wrong! Failed to upload file'));
                return redirect()->back();
            }

            $data = [
                'user_id' => $getUser->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'professional_title' => $request->professional_title,
                'phone_number' => $request->mobile_number,
                'address' => $request->address,
                'about_me' => $request->about_me,
                'slug' => $slug,
                'cv_file' => $cv_file_data['path'],
                'cv_filename' => $cv_file_data['original_filename'],
            ];

            $emailSend = new EmailSendService();
            if($request->account_type == USER_ROLE_ORGANIZATION){
                $this->organizationModel->create($data);
                $text = __("New Organization Request");
                $target_url = route('organizations.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url, false);
            }
            else{
                $this->instructorModel->create($data);
                $text = __("New Instructor Request");
                $target_url = route('instructor.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url, false);
            }

            $this->send($text, 1, $target_url, null , 1);

            $this->showToastrMessage('success', __('Request to join as instructor is submitted, please wait for our response via email you provided.'));
            return redirect(route('login'));
        }

    }




    public function storeSignUpAsInstructorBackUp(SignUpAsInstructorRequest $request)
    {
        $user = new User();
        $accountType = (int)$request->account_type;
      
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->mobile_number = $request->mobile_number;
        $user->phone_number = $request->mobile_number;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        $user->remember_token = $request->_token;
        $user->email_verified_at = get_option('registration_email_verification') == 1 ? NULL : now();
        if($accountType == 2){
            $user->role = 2;
        }
        else{
            $user->role = 4; 
        }
        $user->save();
        $userID = $user->id;
        $getUser = User::where('id',$userID)->first();

        if($request->account_type == USER_ROLE_ORGANIZATION){
            $object = Organization::where('user_id', $getUser->id)->get();
        }
        else{
            $object = Instructor::where('user_id', $getUser->id)->get();
        }

        if ($object->count() > 0){
            $this->showToastrMessage('success', __('Request already send'));
            return redirect(route('login'));
        } else {
            if($request->account_type == USER_ROLE_ORGANIZATION){
                $slugCount = Organization::where('slug', getSlug($getUser->name))->count();
            }
            else{
                $slugCount = Instructor::where('slug', getSlug($getUser->name))->count();
            }

            if ($slugCount)
            {
                $slug = getSlug($getUser->name) . '-'. rand(100000, 999999);
            } else {
                $slug = getSlug($getUser->name);
            }

            $cv_file_data = $this->uploadFileWithDetails('user', $request->cv_file);
            if (!$cv_file_data['is_uploaded']) {
                $this->showToastrMessage('error', __('Something went wrong! Failed to upload file'));
                return redirect()->back();
            }

            $data = [
                'user_id' => $getUser->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'professional_title' => $request->professional_title,
                'phone_number' => $request->mobile_number,
                'address' => $request->address,
                'about_me' => $request->about_me,
                'slug' => $slug,
                'cv_file' => $cv_file_data['path'],
                'cv_filename' => $cv_file_data['original_filename'],
            ];

            $emailSend = new EmailSendService();
            if($request->account_type == USER_ROLE_ORGANIZATION){
                $this->organizationModel->create($data);
                $text = __("New Organization Request");
                $target_url = route('organizations.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url, false);
            }
            else{
                $this->instructorModel->create($data);
                $text = __("New Instructor Request");
                $target_url = route('instructor.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url, false);
            }

            $this->send($text, 1, $target_url, null , 1);

            $this->showToastrMessage('success', __('Request to join as instructor is submitted, please wait for our response via email you provided.'));
            return redirect(route('login'));
        }

    }


    public function storeSignUp(SignUpRequest $request)
    {
        $user = new User();
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->mobile_number = $request->mobile_number;
        $user->phone_number = $request->mobile_number;
        $user->password = Hash::make($request->password);
        $user->remember_token = $request->_token;
        $user->email_verified_at = get_option('registration_email_verification') == 1 ? NULL : now();
        $user->role = 3;
        $user->save();
        $student_data = [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $user->phone_number,
            'status' => get_option('private_mode') ? STATUS_PENDING : STATUS_ACCEPTED
        ];

        $this->studentModel->create($student_data);

        if(get_option('registration_system_bonus_mode', 0)){
            $balance = get_option('registration_bonus_amount');
            $user->increment('balance', decimal_to_int($balance));
            createTransaction($user->id, $balance, TRANSACTION_REGISTRATION_BONUS, 'Registration Bonus');
        }

        if (get_option('registration_email_verification') == 1){
            $sendEmail = new EmailSendService();
            if($sendEmail->sendVerifyEmail($user, route('user.email.verification',$user->remember_token))){
                $this->showToastrMessage('success', __('Sent verification mail your account. Please check your email.'));
            }else{
                toastrMessage('error', 'Something is wrong. Try after few minutes!');
                return redirect()->back();
            }
        }
        $this->showToastrMessage('success', __('Your registration is successful.'));
        return redirect(route('login'));
    }


    public function emailVerification($token)
    {
        if (User::where('remember_token', $token)->count() > 0)
        {
            $user = User::where('remember_token', $token)->first();
            $user->email_verified_at = Carbon::now()->format("Y-m-d H:i:s");
            $user->remember_token = null;
            $user->save();
            if(!$user->student || ($user->student && $user->student->status != STATUS_PENDING)){
                Auth::login($user);
            }

            $this->showToastrMessage('success', __('Congratulations! Successfully verified your email.'));
            return redirect()->route('home');

        } else {
            return redirect(route('login'));
        }

    }




public function storeSignUpAsInstructorApi(Request $request)
{

    try {
    
        $messages = [
            'first_name.required' => __('The First Name Field Is Required'),
            'first_name.string' => __('The First Name Field Must Be A String'),
            'first_name.min' => __('The First Name Field Must Be At Least 2 Characters'),
            'first_name.max' => __('The First Name Field Must Be At Most 100 Characters'),
            'last_name.required' => __('The Last Name Field Is Required'),
            'last_name.string' => __('The Last Name Field Must Be A String'),
            'last_name.min' => __('The Last Name Field Must Be At Least 2 Characters'),
            'last_name.max' => __('The Last Name Field Must Be At Most 100 Characters'),
            'account_type.required' => __('The Account Type Field Is Required'),
            'professional_title.required' => __('The Professional Title Field Is Required'),
            'professional_title.string' => __('The Professional Title Field Must Be A String'),
            'professional_title.min' => __('The Professional Title Field Must Be At Least 2 Characters'),
            'professional_title.max' => __('The Professional Title Field Must Be At Most 100 Characters'),
            'address.required' => __('The Address Field Is Required'),
            'address.string' => __('The Address Field Must Be A String'),
            'cv_file.required' => __('The CV File Field Is Required'),
            'cv_file.max' => __('The CV File Must Be Atmost 10mb'),
            'cv_file.mimetypes' => __('The CV File Must Be a PDF'),
            'about_me.required' => __('The About me Field Is Required'),
            'about_me.string' => __('The About me Field Must Be A String'),
            'email.required' => __('The email Field Is Required'),
            'email.email' => __('The email Field Must Be A Valid Email Type'),
            'email.string' => __('The Email Field Must Be A String'),
            'email.max' => __('The Email Field Must Be At Most 100 Characters'),
            'email.unique' => __('The Email Field Must Be Unique'),
            'password.required' => __('The Password Field Is Required'),
            'password.string' => __('The Password Field Must Be A String'),
            'password.min' => __('The Password Field Must Be At Least 6 Characters'),
            'mobile_number.numeric' => __('The Mobile Number Field Must Be Numeric'),
            'mobile_number.unique' => __('The Mobile Number Field Must Be Unique'),
        ];

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'account_type' => 'required',
            'professional_title' => 'required|string|min:2|max:100',
            'address' => 'required|string',
            'cv_file' => 'required|max:10000|mimetypes:application/pdf',
            'about_me' => 'required|string',
            'email' => 'required|email|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],500);
        }
        
        if(!is_null(request()->input('mobile_number'))){
            $validator = Validator::make($request->all(), [
                'mobile_number' => 'bail|numeric|unique:users',
            ],$messages);
         
            if($validator->fails()){
             
                return response(['status'=>0,'error' => $validator->errors()],500);
            }
        }

        if($request->account_type != 2 &&  $request->account_type != 4){
            return response()->json(['message' =>  __('Account Type Must Be Either "2" Or "4".')], 500);
        }
     
   
        $user = new User();
        $accountType = (int)$request->account_type;
     
        $user->name = $request->first_name . ' '. $request->last_name;
        $user->email = $request->email;
        $user->area_code =  str_replace("+","",$request->area_code);
        $user->phone_number = $request->mobile_number;
        $user->address = $request->address;
        $user->password = Hash::make($request->password);
        $user->remember_token = $request->_token;
        $user->email_verified_at = get_option('registration_email_verification') == 1 ? NULL : now();
        if($accountType == 2){
            $user->role = 2;
        }
        else{
            $user->role = 4; 
        }
    
        $user->save();
        $userID = $user->id;
        $getUser = User::where('id',$userID)->first();

        if($request->account_type == USER_ROLE_ORGANIZATION){
            $object = Organization::where('user_id', $getUser->id)->get();
          
        }
        else{
            $object = Instructor::where('user_id', $getUser->id)->get();
        }

        if ($object->count() > 0){
            return response()->json(['message' =>  __('Sign Up Request already send'), 
            'redirectRoute'=>route('login')], 500);
        } else {
        
            if($request->account_type == USER_ROLE_ORGANIZATION){
                $slugCount = Organization::where('slug', getSlug($getUser->name))->count();
            }
            else{
                $slugCount = Instructor::where('slug', getSlug($getUser->name))->count();
            }

            if ($slugCount)
            {
                $slug = getSlug($getUser->name) . '-'. rand(100000, 999999);
            } else {
                $slug = getSlug($getUser->name);
            }

            $cv_file_data = $this->uploadFileWithDetails('user', $request->cv_file);
            if (!$cv_file_data['is_uploaded']) {
                return response()->json(['message' =>  __('Something went wrong! Failed to upload file'), 
                'redirectRoute'=>route('login')], 500);
            }
        
            $data = [
                'user_id' => $getUser->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'professional_title' => $request->professional_title,
                'phone_number' => $request->mobile_number,
                'address' => $request->address,
                'about_me' => $request->about_me,
                'slug' => $slug,
                'cv_file' => $cv_file_data['path'],
                'cv_filename' => $cv_file_data['original_filename'],
            ];
      
            $emailSend = new EmailSendService();
            if($request->account_type == USER_ROLE_ORGANIZATION){
                $this->organizationModel->create($data);
                $text = __("New Organization Request");
                $target_url = route('organizations.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url, false);
            }
            else{
                $this->instructorModel->create($data);
                $text = __("New Instructor Request");
                $target_url = route('instructor.pending');
                $emailSend->sendInstructorRequestToAdmin($target_url, false);
            }

            $this->send($text, 1, $target_url, null , 1);

            return response()->json(['message' =>  __('Request to join as instructor is submitted, please wait for our response via email you provided.'),
            'redirectRoute'=>route('login')], 200);
         
        }
    } 
    catch (\Throwable $th) {
        return response(['status' => 0,'message'=>__($th->getMessage())],500);    
    }
    
}



public function storeSignUpAsStudentApi(Request $request)
{

    try {
        //code...
      
        $messages = [
            'course_type.required' => 'Please select at least one course type (Live or Self-Learning).',
            'course_type.in' => 'Invalid course type selection.',
            'course.required' => 'Please Select Course',
             'course.numeric' => 'Course ID must be numeric',
            'fullname.required' => 'Your full name is required.',
              'phone.required' => 'Your phone number is required.',
            //   'area_code.required' => 'Area Code Is Required.',
             'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'city.required' => 'City Field is required.',
            'country.required' => 'Please select your country.',
            //  'age.required' => 'Age Field is required.',
            //    'age.numeric' => 'Age Field Must Be Numeric.',
            'password.required' => 'Please enter a password.',
            // 'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Your password must be at least 6 characters long.',
            'how_many_students.numeric' => 'Please enter a valid number of students.',
            'preferDate.date' => 'Please select a valid date.',
            'preferSlot.numeric' => 'Please choose a valid slot.',
        ];

        $validator = Validator::make($request->all(), [
            'course_type' => 'required',
            'course_type' => 'in:1,2',
            'course' => 'required|numeric',
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,mobile_number',
            // 'area_code' => 'required',
            'email' => 'required|email|unique:users,email',
            'city' => 'required|string|max:255',
            'country' => 'required|exists:countries,id',
            // 'age' => 'required|numeric',
            'school_grade' => 'nullable|string|max:255',
            'parent_name' => 'nullable|string|max:255',
            // 'password' => 'required|confirmed|min:6',
            'password' => 'required|min:6',
            'preferred_teacher' => 'string|nullable',
            'how_many_students' => 'numeric|nullable',
            'preferDate' => 'string|nullable',
            'preferSlot' => 'numeric|nullable',
            'slug' => 'string|nullable',
        ],$messages);
     
        if($validator->fails()){
         
            return response(['status'=>0,'error' => $validator->errors()],302);
        }
  
         
        $course_type = $request->input('course_type'); // 1 for live, 2 for self-learning
  
        if($course_type == 1){
            $type = 1;
        }
        else{
            $type = 2;
        }
        if($type == 1 || $type == 3){
             $messages2 = [
                'preferDate.required' => 'Please select calender date.',
                'preferDate.date' => 'Please select a valid date.',
                'preferSlot.required' => 'Please select time slot.',
                'preferSlot.numeric' => 'Please choose a valid slot.',
            ];
            $validator2 = Validator::make($request->all(), [
                'preferDate' => 'required|date',
                'preferSlot' => 'required|numeric',
            ],$messages2);
            if($validator2->fails()){
                return response(['status'=>0,'error' => $validator2->errors()],302);
            }
        }

    try {
        DB::beginTransaction();  // Start transaction to ensure atomicity
     
        $checkEmail = User::where('email',$request->email)->first();
        if($checkEmail){
            return response()->json(['status' => false,
                'message' =>  __('Given Email Already Exists')], 302);
     
        }

        $course_type = $request->input('course_type');
        if(empty($course_type)){
            // $this->showToastrMessage('error', ___('Please select at least one course type.'));
            // return redirect()->back();
            return response()->json(['status' => false,
                'message' =>  __('Please select at least one course type.')], 302);
        }

          // Extract first and last name from fullname
        $nameParts = explode(' ', $request->input('fullname'), 2);
        $fname = $nameParts[0];
        $lname = $nameParts[1] ?? '';

        $preferDateInput = $request->input('preferDate'); // Example: "March 2025 25"

        // $areaCode2 = $request->input('area_code');
        // // If the value doesn't start with '+', prepend it
        // if (!str_starts_with($areaCode2, '+')) {
        //     $areaCode2 = '+' . ltrim($areaCode2, '+'); // ltrim removes any existing + just in case
        // }

        $PhoneNumber = $request->input('phone');
        $phone = trim($request->input('phone'));

        // Check if starts with + followed by digits (area code present)
        if (!preg_match('/^\+\d+/', $phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Phone number. Must start with an area code (e.g., +44, +92, +1)'
            ], 302);
        }

        // // Case 4: Already has + and area code (+923125577435) - do nothing
        // if (str_starts_with($PhoneNumber, $areaCode2)) {
        //     // Format is correct, no changes needed
        // }
        // // Case 3: Has area code without + (923125577435) - just add +
        // elseif (str_starts_with($PhoneNumber, ltrim($areaCode2, '+'))) {
        //     $PhoneNumber = '+' . $PhoneNumber;
        // }
        // // Case 2: Starts with 0 (03125577435) - remove 0 and add area code
        // elseif (str_starts_with($PhoneNumber, '0')) {
        //     $PhoneNumber = $areaCode2 . substr($PhoneNumber, 1);
        // }
        // // Case 1: Just local number (3125577435) - add full area code
        // else {
        //     $PhoneNumber = $areaCode2 . $PhoneNumber;
        // }

        $checkPhone = User::where('mobile_number',$PhoneNumber)->first();
        if($checkPhone){
            return response()->json(['status' => false,
                'message' =>  __('Given WhatsApp Number Already Exists')], 302);
          
        }

        $timeZONE = 'Asia/Karachi';
        // Create new User
        $user = new User();
        $user->name = $request->input('fullname');
        $user->email = $request->input('email');
        // $user->area_code = $areaCode2;
        $user->mobile_number = $PhoneNumber;
        $user->time_zone = $timeZONE;
        $user->password = Hash::make($request->input('password'));
        $user->random_password = $request->input('password');
        $user->remember_token = $request->input('_token');
        $user->email_verified_at = get_option('registration_email_verification') ? null : now();
        $user->role = 3;

        $course_type = (int) $request->input('course_type'); // 1 for live, 2 for self-learning
        $type = $course_type;
    
        $user->save();
        $course_id_given = $request->input('course');

         // Create Student Record
        $student = $this->studentModel->create([
            'user_id' => $user->id,
            'first_name' => $fname,
            'last_name' => $lname,
            'phone_number' => $user->mobile_number,
            'status' => 1,  
            'type' => $type,
            'city' => $request->input('city'),
            'age' => $request->input('age'),
            'school_grade' => $request->input('school_grade'),
            'parent_name' => $request->input('parent_name'),
            'country_id' => $request->input('country'),
            'no_of_joined_students' => $request->input('how_many_students'),
            'preferred_teacher' => $request->input('preferred_teacher'),
        ]);

     
        $slugUpdated = null;
        $course = Course::where('id', $request->input('slug'))->first();
        if($course){
            $course_id = $course->id;
        }
        else{
            $course_id = $course_id_given; 
        }
        $course = Course::find($course_id);
        if (!$course) {
            return response()->json(['status' => false,
                'message' =>  __('Given Course ID Not Found.')], 302);
        }
        else{
            $slugUpdated = $course->slug;
        }

        if ($type == 1 || $type == 3) { // Live class with trial setup
            // $course = Course::where('slug', $request->input('slug'))->first();
            $course = Course::where('id', $request->input('slug'))->first();
            if($course){
                $course_id = $course->id;
            }
            else{
                $course_id = $course_id_given; 
            }
         
            $getTimeSlotInfo = TimeSlot::where('id',$request->preferSlot)->first();
           
            $preferTime = $getTimeSlotInfo->start_time;
            TrialClass::create([
                'student_id' => $student->id,
                'prefer_date' => $preferDateInput, // Fixed date format
                'prefer_time' => $preferTime,
                'course_id' => $course_id,
            ]);

            BookedSlot::create([
                'time_slot_id' => $getTimeSlotInfo->id,
                'user_id' => $user->id,
                'status' => 'booked'
            ]);

            $student->update(['status' => 1]); // Update status to active

            DB::commit();

            // Send welcome email (fail-safe: registration already committed)
            try {
                if ($type == 1 || $type == 3) {
                    $template_name = 'student-sign-up-with-trial';
                } else {
                    $template_name = 'student-sign-up';
                }
                sendEmailNotification([
                    '{{USER_NAME}}' => $user->name,
                    '{{EMAIL}}' => $user->email,
                    '{{PASSWORD}}' => $request->input('password'),
                ], $user->email, $template_name);
                $this->send(__('Student Free Class Request'), 1, route('student.free_classes'), null, 1);
            } catch (\Throwable $mailEx) {
                Log::warning('Registration: welcome email failed (user already registered)', [
                    'user_id' => $user->id,
                    'error' => $mailEx->getMessage(),
                ]);
            }

            $accessToken = $user->createToken('CefOnlineAcademy')->plainTextToken;
            $msg = __("Registration successful. Trial Class Created For Live Course. You Can Now Login To Your Panel.");
            return response()->json(['status' => true,
            'destination' => 'login_page',
            'message' =>  __($msg),
            'token' => $accessToken,
            'role' => $user->role,
            ], 200);
        }
        else{
            $student->update(['status' => 1]); // Activate self-learning type
            $course = Course::where('id', $request->input('slug'))->first();
            if($course){
                $course_id = $course->id;
            }
            else{
                $course_id = $course_id_given; 
            }
            $course = Course::find($course_id);
          
             if (!$course) {
               return response()->json(['status' => false,
                'message' =>  __('Given Course ID Not Found.')], 302);
            }
        
            $enrollment = Enrollment::where(['course_id' => $course_id, 'user_id' => $user->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->first();
            if ($enrollment) {
                 $order = Order::find($enrollment->order_id);
                    if ($order) {
                        if ($order->payment_status == 'due') {
                            Order_item::whereOrderId($enrollment->order_id)->get()->map(function ($q) {
                                $q->delete();
                            });
                            $order->delete();
                        } elseif ($order->payment_status == 'pending') {
                            DB::rollBack();
                            // $this->showToastrMessage('success', __("You've already request the course & payment_status is pending!"));
                            //  return redirect()->route('sign-up-as-student');
                           return response()->json(['status' => false,
                            'message' =>  __("You've already request the course & payment_status is pending!")], 302);
                         
                        } elseif ($order->payment_status == 'paid' || $order->payment_status == 'free') {
                            $msg = __("You've already purchased the course!");
                            DB::rollBack();
                            //  $this->showToastrMessage('success', __($msg));
                            //  return redirect()->route('sign-up-as-student');
                            return response()->json(['status' => false,
                            'message' =>  __($msg)], 302);
                        }
                    } else {
                        $msg = __("Something is wrong! Try again1.");
                        DB::rollBack();
                        // $this->showToastrMessage('error', __($msg));
                        // return redirect()->route('sign-up-as-student');
                           return response()->json(['status' => false,
                            'message' =>  __($msg)], 302);
                    }
            }
            $ownCourseCheck = CourseInstructor::where('course_id', $course_id)->where('instructor_id', $user->id)->delete();
            if ($ownCourseCheck) {
                $msg = __("This is your course. No need to add to cart.");
                DB::rollBack();
                //   $this->showToastrMessage('success', __($msg));
                // return redirect()->route('sign-up-as-student');
                  return response()->json(['status' => false,
                            'message' =>  __($msg)], 302);
            }
             $cartExists = CartManagement::whereUserId($user->id)->whereCourseId($course_id)->first();
              if ($cartExists) {
                $msg = __("Already added to cart!");
                DB::rollBack();
                // $this->showToastrMessage('success', __($msg));
                // return redirect()->route('sign-up-as-student');
                 return response()->json(['status' => false,
                            'message' =>  __($msg)], 302);
            }
            if ($course->learner_accessibility == 'free') {
                    $order = new Order();
                    $order->user_id = $user->id;
                    $order->order_number = rand(100000, 999999);
                    $order->payment_status = 'free';
                    $order->save();
                    $order_item = new Order_item();
                    $order_item->order_id = $order->id;
                    $order_item->user_id = $user->id;
                    $order_item->course_id = $course->id;
                    $order_item->owner_user_id = $course->user_id ?? null;
                    $order_item->unit_price = 0;
                    $order_item->admin_commission = 0;
                    $order_item->owner_balance = 0;
                    $order_item->sell_commission = 0;
                    $order_item->request_enrollment = 0;
                    $order_item->save();

                    $enrollment = Enrollment::where('user_id',$user->id)->where('course_id',$course_id)->orderBy('id','DESC')->first();
                    if($enrollment){
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = $user->id;
                        $enrollment->course_id = $course_id;
                        $enrollment->owner_user_id = $course->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($course->access_period) ? Carbon::now()->addDays($course->access_period) : MAX_EXPIRED_DATE;
                        
                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;
                        $enrollment->request_enrollment = (int) ($order_item->request_enrollment ?? 0);
    
                        $enrollment->save();
                    }
                    else{
                        $enrollment = new Enrollment();
                        $enrollment->order_id = $order->id;
                        $enrollment->user_id = $user->id;
                        $enrollment->course_id = $course_id;
                        $enrollment->owner_user_id = $course->user_id ?? null;
                        $enrollment->start_date = now();
                        $enrollment->end_date = ($course->access_period) ? Carbon::now()->addDays($course->access_period) : MAX_EXPIRED_DATE;

                        $enrollment->main_price = $order_item->unit_price;
                        $enrollment->price = $order_item->unit_price;
                        $enrollment->original_price = $order_item->original_price;
                        $enrollment->discount = $order_item->discount;
                        $enrollment->admission_fee = $order_item->admission_fee;
                        $enrollment->discount_type = $order_item->discount_type;
                        $enrollment->net_original_price = $order_item->net_original_price;
                        $enrollment->discount_type_admission_fee = $order_item->discount_type_admission_fee;
                        $enrollment->discount_admission_fee = $order_item->discount_admission_fee;
                        $enrollment->net_admission_fee = $order_item->net_admission_fee;
                        $enrollment->request_enrollment = (int) ($order_item->request_enrollment ?? 0);
                        $enrollment->save();
                        Log::info("NEW ENROLLMENT BEING CREATED IN (storeSignApAsStudentApi) IN FRONTEND/REGISTRATIONCONTROLLER AT ".Carbon::now());
                        Log::info("NEW ENROLLMENT DETAILS IN (storeSignApAsStudentApi) IN FRONTEND/REGISTRATIONCONTROLLER:- ".$enrollment);
                    }
                   

                    $msg = __("Registration successful. Free Course added to your my learning list. You Can Now Login To Your Panel.");

                     DB::commit();

                    try {
                        if ($type == 1 || $type == 3) {
                            $template_name = 'student-sign-up-with-trial';
                        } else {
                            $template_name = 'student-sign-up';
                        }
                        sendEmailNotification([
                            '{{USER_NAME}}' => $user->name,
                            '{{EMAIL}}' => $user->email,
                            '{{PASSWORD}}' => $request->input('password'),
                        ], $user->email, $template_name);
                        $this->send(__('Student Free Class Request'), 1, route('student.free_classes'), null, 1);
                    } catch (\Throwable $mailEx) {
                        Log::warning('Registration: welcome email failed (user already registered)', [
                            'user_id' => $user->id,
                            'error' => $mailEx->getMessage(),
                        ]);
                    }

                    $accessToken = $user->createToken('CefOnlineAcademy')->plainTextToken;
                    return response()->json(['status' => true,
                            'destination' => 'login_page',
                            'message' =>  __($msg),
                            'token' => $accessToken,
                            'role' => $user->role,
                            ], 200);
                  
            }
               
            $cart = new CartManagement();
            $cart->user_id = $user->id;
            $cart->course_id = $course_id;
            $cart->is_subscription_enable = $course->is_subscription_enable;
            $cart->main_price = $course->price;
            $cart->price = $course->price;
            $cart->month = date('m');
            $cart->year = date('Y');
            $cart->save();
            $msg = __("Welcome to CEF Online, please complete course enrolment.");


             DB::commit();

            try {
                if ($type == 1 || $type == 3) {
                    $template_name = 'student-sign-up-with-trial';
                } else {
                    $template_name = 'student-sign-up';
                }
                sendEmailNotification([
                    '{{USER_NAME}}' => $user->name,
                    '{{EMAIL}}' => $user->email,
                    '{{PASSWORD}}' => $request->input('password'),
                ], $user->email, $template_name);
                $this->send(__('Student Free Class Request'), 1, route('student.free_classes'), null, 1);
            } catch (\Throwable $mailEx) {
                Log::warning('Registration: welcome email failed (user already registered)', [
                    'user_id' => $user->id,
                    'error' => $mailEx->getMessage(),
                ]);
            }

            $accessToken = $user->createToken('CefOnlineAcademy')->plainTextToken;
            return response()->json(['status' => true,
                            'destination' => 'cart_page',
                            'message' =>  __($msg),
                            'token' => $accessToken,
                            'role' => $user->role,
                            ], 200);
        }

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Student Registration Error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return response()->json([
            'status' => false,
            'message' => __('Registration failed. Please try again.'),
        ], 500); 
    }



    } catch (\Throwable $th) {
        Log::error('Student Registration Error: ' . $th->getMessage(), [
            'file' => $th->getFile(),
            'line' => $th->getLine(),
        ]);
        return response()->json([
            'status' => false,
            'message' => __('Registration failed. Please try again.'),
        ], 500);
    }
  
}






}
