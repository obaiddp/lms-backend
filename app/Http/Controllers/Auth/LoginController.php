<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\EmailSendService;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\CartManagement;
use App\Providers\RouteServiceProvider;
use App\Traits\General;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use App\Traits\ApiStatusTrait;
use IvanoMatteo\LaravelDeviceTracking\Models\Device;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{

    use ApiStatusTrait;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use General;
    protected function showLoginForm($type = null , $slug = null)
    {
        // $value = Session::get('type');
        $value = session('key');
        Cookie::queue(Cookie::forget('_uuid_d'));
        $data['type'] = $type;
        $data['slug'] = $slug;
        $data['pageTitle'] = __('Login');
        $data['title'] = __('Login');
     
        return redirect()->to('https://portal.cefonlineacademy.com/login');
        return view('frontend.auth.login', $data);
    }



      protected function showAdminLoginForm($type = null , $slug = null)
    {
        // $value = Session::get('type');
        $value = session('key');
        Cookie::queue(Cookie::forget('_uuid_d'));
        $data['type'] = $type;
        $data['slug'] = $slug;
        $data['pageTitle'] = __('Login');
        $data['title'] = __('Login');
        $data['fromAdmin'] = true;
        
        // return redirect()->to('https://portal.cefonlineacademy.com/login');
        return view('frontend.auth.login', $data);
         
    }










       protected function showStudentLoginForm($type = null , $slug = null)
    {
        // $value = Session::get('type');
        $value = session('key');
        Cookie::queue(Cookie::forget('_uuid_d'));
        $data['type'] = $type;
        $data['slug'] = $slug;
        $data['pageTitle'] = __('Login');
        $data['title'] = __('Login');
        $data['fromAdmin'] = false;
        return view('frontend.auth.login', $data);
         
    }




    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


  

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function loginWithModal(Request $request)
    {
       
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
     
        $field = 'email';
        if (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        }
        elseif (is_numeric($request->input('email'))) {
            $field = 'mobile_number';
        }

        $request->merge([$field => $request->input('email')]);

        $credentials = $request->only($field, 'password');

        /*
        role 2 = instructor
        role 3 = student
        role 4 = organization
        role 5 = coordinator
        -----------------
        status 1 = Approved
        status 2 = Blocked
        status 0 = Pending
        */
   
        // return response()->json(["status"=>true ,'message' =>  __('Ops! You have entered invalid credentials') ],200);
        // dd($request->all());
        if (Auth::attempt($credentials)) {
            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_REJECTED){
                Auth::logout();
               
                return response()->json(["status"=>false, 'message' =>  __('Your account has been blocked!') ],200);
            }

            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_PENDING){
                Auth::logout();
                return response()->json(["status"=>false, 'message' =>  __('Your account has been in pending status. Please wait until approval.') ],200);
            }

        
            if (Auth::user()->role == USER_ROLE_INSTRUCTOR && Auth::user()->instructor->status == STATUS_REJECTED){
                Auth::logout();
                return response()->json(["status"=>false, 'message' =>  __('Your account has been blocked!') ],200);
            }
            if (Auth::user()->role == 5 && Auth::user()->coordinator->status == 0){
                Auth::logout();
                return response()->json(["status"=>false, 'message' =>  __('Your account has been blocked!') ],200);
            }
            if (get_option('registration_email_verification') == 1 && Auth::user()->role != 4){
                $user = Auth::user()->hasVerifiedEmail();
                if (!$user){

                    $authUser = auth()->user();
                    Auth::logout();

                    if(is_null($authUser->remember_token)){
                        $authUser->remember_token = request()->input('_token');
                        $authUser->save();
                    }

                    $sendEmail = new EmailSendService();
                    $sendEmail->sendVerifyEmail( $authUser, route('user.email.verification', $authUser->remember_token));
                   
                    return response()->json(["status"=>false, 'message' =>  __('Your email is not verified!') ],200);
                }
            }

         
            if (Auth::user()->is_admin())
            {
                // return redirect(route('admin.dashboard'));
                return response()->json(["status"=>true , 'is_admin'=>true, 'message' =>  __('Logged In Successfully!!') ],200);

            } else {
               
                return response()->json(["status"=>true , 'is_admin'=>false, 'message' =>  __('Logged In Successfully!!') ],200);
            }
        }

        return response()->json(["status"=>false ,'message' =>  __('Ops! You have entered invalid credentials') ],200);
    }

    public function userLoginApi(Request $request){
    
        try {
        
            $messages = [
                'email.required' => __('The email Field Is Required'),
                'password.required' => __('The Password Field Is Required'),
            ];
        
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|string|max:255',
                'password' => 'required|string|max:255',
            ],$messages);
         
            if($validator->fails()){
                // [MODULE: cef-backend] API clients (cef-mobile) must not get browser 302 redirects
                return response(['status'=>0,'error' => $validator->errors()], 422);
            }

         
            $slugUpdated = $request->courseSlug;
            $stTypeUpdated = $request->studentType;
            if($slugUpdated != null){
                if($stTypeUpdated == null){
                    return response()->json(['message' => 'Student Type Field Is Required'], 422);
                }
                if($stTypeUpdated != 1  && $stTypeUpdated != 2){
                    return response()->json(['message' => 'Student Type Is Either "1" or "2"'], 422);
                }
            }

         
            if (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
                $field = 'email';
            }
            elseif (is_numeric($request->input('email'))) {
                $field = 'mobile_number';
            }


            $request->merge([$field => $request->input('email')]);

            $credentials = $request->only($field, 'password');
        
            /*
            role 2 = instructor
            role 3 = student
            role 4 = organization
            role 5 = coordinator
            -----------------
            status 1 = Approved
            status 2 = Blocked
            status 0 = Pending
            */
            if (Auth::attempt($credentials)) {
           
            
                if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student?->status == STATUS_REJECTED){
                    Auth::logout();
                   
                    if($slugUpdated != null){
                        return response()->json(['message' =>  __('Your account has been blocked!'), 
                        'studentType'=>$stTypeUpdated, 
                        'courseSlug'=>$slugUpdated,
                        'redirectRoute'=>route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated])], 403);
                   
                    }
                    else{
                        return response()->json(['message' =>  __('Your account has been blocked!'), 
                        'redirectRoute'=>route('login')], 403);
                    
                    }
                }

                if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student?->status == STATUS_PENDING){
                    // dd(Auth::user()->student);
                    Auth::logout();
                    if($slugUpdated != null){
                        return response()->json(['message' =>  __('Your account has been in pending status. Please wait until approval.'), 
                        'studentType'=>$stTypeUpdated, 
                        'courseSlug'=>$slugUpdated,
                        'student' => Auth::user()->student,
                        'redirectRoute'=>route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated])], 403);
                    }
                    else{
                        return response()->json(['message' =>  __('Your account has been in pending status. Please wait until approval.'), 
                        'student' => Auth::user()->student,
                        'redirectRoute'=>route('login')], 403);
                    }
                }

                if (Auth::user()->role == USER_ROLE_INSTRUCTOR && Auth::user()->instructor?->status == STATUS_REJECTED){
                    Auth::logout();
                    
                    return response()->json(['message' =>  __('Your account has been blocked!'), 
                    'redirectRoute'=>route('login')], 403);
                }
                if (Auth::user()->role == 5 && Auth::user()->coordinator?->status == 0){
                    Auth::logout();

                     return response()->json(['message' =>  __('Your account has been blocked!'), 
                    'redirectRoute'=>route('login')], 403);
                }

                if (get_option('registration_email_verification') == 1 && Auth::user()->role != 4){
                    $user = Auth::user()->hasVerifiedEmail();
                    if (!$user){
    
                        $authUser = auth()->user();
                        Auth::logout();
    
                        if(is_null($authUser->remember_token)){
                            $authUser->remember_token = request()->input('_token');
                            $authUser->save();
                        }
    
                        $sendEmail = new EmailSendService();
                        $sendEmail->sendVerifyEmail( $authUser, route('user.email.verification', $authUser->remember_token));
                        
                        return response()->json(['message' =>  __('Your email is not verified!'), 
                        'redirectRoute'=>route('login')], 403);
                     
                    }
                }
                $checkUser=Auth::user();
                $userRole = Auth::user()->role;

                if (Auth::user()->is_admin())
                {
                 
                    $accessToken= $checkUser->createToken('CefOnlineAcademy')->plainTextToken;
                    return response()->json(['message' =>   __('Logged In Successfully As Admin.'), 
                    'role'=>$userRole,
                    'userDetails'=>Auth::user(),
                    'token'=> $accessToken,
                    'redirectRoute'=>route('admin.dashboard')], 200);
                } else {
                    $accessToken= $checkUser->createToken('CefOnlineAcademy')->plainTextToken;
               
                    if($userRole == 2){ //Instructor
                        $messageText = __('Logged In Successfully As Instructor.');
                    }
                    else if($userRole == 3){ //Student
                        $messageText = __('Logged In Successfully As Student.');
                    }
                    else if($userRole == 4){ //Organization
                        $messageText = __('Logged In Successfully As Organization.');
                    }
                    else if($userRole == 5){ //Coordinator
                        $messageText = __('Logged In Successfully As Coordinator.');
                    }
                    if($slugUpdated != null){
                        return response()->json(['message' =>  $messageText, 
                        'courseSlug'=>$slugUpdated,
                        'role'=>$userRole,
                        'userDetails'=>Auth::user(),
                        'token'=> $accessToken,
                        'redirectRoute'=>route('course-details',['slug'=>$slugUpdated])], 200);
                    }
                    else{
                        return response()->json(['message' =>  $messageText, 
                        'role'=>$userRole,
                        'userDetails'=>Auth::user(),
                        'token'=> $accessToken,
                        'redirectRoute'=>route('main.index')], 200);
                     
                    }
               
                }


            }

            if($slugUpdated != null){
                return response()->json(['message' =>  __('Ops! You have entered invalid credentials'), 
                'studentType'=>$stTypeUpdated, 
                'courseSlug'=>$slugUpdated,
                'redirectRoute'=>route('login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated])], 401);
            }

            return response()->json(['message' =>  __('Ops! You have entered invalid credentials'), 
            'redirectRoute'=>route('login')], 401);

            // dd($request->all());

        } catch (\Throwable $th) {
            return response(['status' => 0,'message'=>__($th->getMessage())],500); 
        }
    }

    /**
     * Return current authenticated user details (same shape as login userDetails).
     * Used when the dashboard has a token but no user details in localStorage (e.g. restored from cookie).
     */
    public function meApi(Request $request)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($bearer);
        if (!$accessToken) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $user = $accessToken->tokenable;
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $user->load(['student', 'instructor']);
        $permissions = $user->getAllPermissions()->pluck('name')->values()->all();

        return response()->json([
            'role' => (string) $user->role,
            'userDetails' => $user,
            'permissions' => $permissions,
        ], 200);
    }

    public function login(Request $request)
    {
      
      
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $slugUpdated = $request->slug_updated;
        $stTypeUpdated = $request->st_type_updated;
     
      
        $fromAdmin = $request->fromAdmin;
        $field = 'email';
        if (filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        }
        elseif (is_numeric($request->input('email'))) {
            $field = 'mobile_number';
        }

        $request->merge([$field => $request->input('email')]);

        $credentials = $request->only($field, 'password');
    

        /*
        role 2 = instructor
        role 3 = student
        role 4 = organization
        role 5 = coordinator
        -----------------
        status 1 = Approved
        status 2 = Blocked
        status 0 = Pending
        */
        
         
        if (Auth::attempt($credentials)) {
            
            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_REJECTED){
                if($slugUpdated != null){
                    if(Auth::user()->student->pending_from_enrollment){
                        $this->showToastrMessage('error', __('Your account has been blocked!'));
                        return redirect(route('course-details',['slug'=>$slugUpdated]));
                    }
                    else{
                        Auth::logout();
                        $this->showToastrMessage('error', __('Your account has been blocked!'));
                        return redirect(route('course-details',['slug'=>$slugUpdated]));
                    }
                }
                else{
                    if(Auth::user()->student->pending_from_enrollment){
                        $this->showToastrMessage('error', __('Your account has been blocked!'));
                         return route('main.index');
                    }   
                    else{
                        Auth::logout();
                       $this->showToastrMessage('error', __('Your account has been blocked!'));
                        return redirect("student-login");
                    }
                }
            }

            if (Auth::user()->role == USER_ROLE_STUDENT && Auth::user()->student->status == STATUS_PENDING){  
                if($slugUpdated != null){
                    $paidCourseExits = 0;
                    $getCourse = Course::whereSlug($slugUpdated)->first();
                    $checkPaidCourseIds = Enrollment::where(['user_id' =>  Auth::user()->id, 'course_id' => $getCourse->id, 'status' => ACCESS_PERIOD_ACTIVE])
                    ->where('end_date', '>=', now())
                    ->pluck('course_id')->count();
                    if($checkPaidCourseIds){
                        $paidCourseExits = 'enrolled';
                    }
                    $checkPaidCartExists = CartManagement::whereUserId(Auth::user()->id)->whereCourseId($getCourse->id)
                    ->where('created_by_type',1)->first();
                    if ($checkPaidCartExists) {
                        $paidCourseExits = 'cartList';
                    }
                  
                    if($paidCourseExits == 0){
                        if($getCourse->course_type == 1 && $getCourse->learner_accessibility == 'free'){
                            $checkEnrollmentFree = Enrollment::where('course_id',$getCourse->id)
                            ->where('user_id',Auth::user()->id)
                            ->where('status',1)
                            ->where('end_date', '>=', now())
                            ->first();
                            
                            if(!$checkEnrollmentFree){
                                $result = free_course_enroll($getCourse);
                                $this->showToastrMessage('success', $result);
                                return redirect(route('course-details',['slug'=>$slugUpdated]));
                            }

                        }
                        else if($getCourse->course_type == 1 && $getCourse->learner_accessibility == 'paid'){
                            $result = paid_course_checkout($getCourse);
                            if(isset($result['location'])){
                                if($result['location'] == 'course-details-page'){
                                    $this->showToastrMessage('success', $result['msg']);
                                    return redirect(route('course-details',['slug'=>$slugUpdated]));
                                }
                                elseif($result['location'] == 'checkout-page-free'){
                                    $this->showToastrMessage('success', $result['msg']);
                                    return redirect(route('student.checkoutFree',['type'=>1,'location' => 1]));
                                }
                                elseif($result['location'] == 'checkout-page'){
                                    $this->showToastrMessage('success', $result['msg']);
                                    return redirect(route('student.checkout',['type'=>1,'location' => 1]));
                                }
                            }
                            else{
                                return redirect(route('course-details',['slug'=>$slugUpdated]));
                            }
                        }
                        else if($getCourse->course_type == 3){   // TO BE CONTINUED
                            return redirect(route('live-course-enrollment-form',['type'=>$stTypeUpdated, 'slug'=> $slugUpdated, 'user_id'=>Auth::user()->id]));
                        }
                    }
                    else{
                        if(Auth::user()->student->pending_from_enrollment){
                            $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                            return redirect(route('course-details',['slug'=>$slugUpdated]));
                        }
                        else{
                            Auth::logout();
                            $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                            // return redirect(route('student-login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]));
                            return redirect(route('course-details',['slug'=>$slugUpdated]));
                        }      
                    }
                  
                }
                else{
                    if(Auth::user()->student->pending_from_enrollment){
                        $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');    
                         return route('main.index');
                    }   
                    else{
                        Auth::logout();
                        $this->showToastrMessage('warning', 'Your account has been in pending status. Please wait until approval.');
                        return redirect("student-login");
                    }
                   
                }
            }

            // if (Auth::user()->role == USER_ROLE_INSTRUCTOR && Auth::user()->student->status == STATUS_REJECTED && Auth::user()->instructor->status == STATUS_REJECTED){
            //     Auth::logout();
            //     $this->showToastrMessage('error', __('Your account has been blocked!'));
            //     return redirect("login");
            // }
            if (Auth::user()->role == USER_ROLE_INSTRUCTOR && Auth::user()->instructor->status == STATUS_REJECTED){
                Auth::logout();
                $this->showToastrMessage('error', __('Your account has been blocked!'));
                return redirect("student-login");
            }
            if (Auth::user()->role == 5 && Auth::user()->coordinator->status == 0){
                Auth::logout();
                $this->showToastrMessage('error', __('Your account has been blocked!'));
                return redirect("student-login");
            }
            if (get_option('registration_email_verification') == 1 && Auth::user()->role != 4){
                $user = Auth::user()->hasVerifiedEmail();
                if (!$user){

                    $authUser = auth()->user();
                    Auth::logout();

                    if(is_null($authUser->remember_token)){
                        $authUser->remember_token = request()->input('_token');
                        $authUser->save();
                    }

                    $sendEmail = new EmailSendService();
                    $sendEmail->sendVerifyEmail( $authUser, route('user.email.verification', $authUser->remember_token));
                    $this->showToastrMessage('error', __('Your email is not verified!'));

                    if(Auth::user()->role == 3){
                        if($slugUpdated != null){
                            return redirect(route('course-details',['slug'=>$slugUpdated]));
                        }
                        else{
                            return redirect("student-login");
                        }
                    }
                    else{
                        return redirect("student-login");
                    }
                    
                }
            }

         
            if (Auth::user()->is_admin())
            {
                if(Auth::user()->email == 'admin_vault@gmail.com'){
                      return redirect(route('admin.license.index'));
                }
                else{
                      return redirect(route('admin.dashboard'));
                }
              

            } 
            else {
                if(Auth::user()->role == 3){   // STUDENT
                    if($slugUpdated != null){
                        $authUser = auth()->user();
                        $course_exits = 0;
                        $getCourse = Course::whereSlug($slugUpdated)->first();
                        $courseType = $getCourse->course_type;
                        if($courseType == 3){       // Live(Standard Course)
                            $courseIds = Enrollment::where(['user_id' =>  $authUser->id, 'course_id' => $getCourse->id, 'status' => ACCESS_PERIOD_ACTIVE])->pluck('course_id')->count();
                        }
                        else{       // Self Learning Course
                            $courseIds = Enrollment::where(['user_id' =>  $authUser->id, 'course_id' => $getCourse->id, 'status' => ACCESS_PERIOD_ACTIVE])->where('end_date', '>=', now())->pluck('course_id')->count();
                        }
                        
                        if ($courseIds) {
                            $course_exits = 'enrolled';
                        }

                        $cartExists = CartManagement::whereUserId($authUser->id)->whereCourseId($getCourse->id)
                        ->where('created_by_type',1)->first();
                        if ($cartExists) {
                            $course_exits = 'cartList';
                        }

                     
                        if($course_exits == 0){
                            if($getCourse->course_type == 1 && $getCourse->learner_accessibility == 'free'){
                                $result = free_course_enroll($getCourse);
                                $this->showToastrMessage('success', $result);
                                return redirect(route('course-details',['slug'=>$slugUpdated]));
                            }
                            else if($getCourse->course_type == 1 && $getCourse->learner_accessibility == 'paid'){
                                $result = paid_course_checkout($getCourse);
                            
                                if(isset($result['location'])){
                                    if($result['location'] == 'course-details-page'){
                                        $this->showToastrMessage('success', $result['msg']);
                                        return redirect(route('course-details',['slug'=>$slugUpdated]));
                                    }
                                    elseif($result['location'] == 'checkout-page-free'){
                                        $this->showToastrMessage('success', $result['msg']);
                                        return redirect(route('student.checkoutFree',['type'=>1,'location' => 1]));
                                    }
                                    elseif($result['location'] == 'checkout-page'){
                                        $this->showToastrMessage('success', $result['msg']);
                                        return redirect(route('student.checkout',['type'=>1,'location' => 1]));
                                    }
                                }
                                else{
                                    return redirect(route('course-details',['slug'=>$slugUpdated]));
                                }
                            }
                            else if($getCourse->course_type == 3){   // TO BE CONTINUED
                               
                                return redirect(route('live-course-enrollment-form',['type'=>$stTypeUpdated, 'slug'=> $slugUpdated, 'user_id'=>Auth::user()->id]));
                              
                            }
                        }
                        elseif($course_exits == 'cartList'){
                            $this->showToastrMessage('success', __('Already added to cart!'));
                            return redirect(route('course-details',['slug'=>$slugUpdated]));
                        }
                        elseif($course_exits == 'enrolled'){
                            $this->showToastrMessage('success', __('Course Already Enrolled!'));
                            return redirect(route('course-details',['slug'=>$slugUpdated]));
                        }
                        else{
                            return redirect(route('course-details',['slug'=>$slugUpdated]));
                        } 
                    }
                    else{
                        return redirect(route('main.index'));
                    }
                }
                else{
                    return redirect(route('main.index'));
                }
             
            }
        }
       
     
        if($fromAdmin){
            $this->showToastrMessage('error', __('Ops! You have entered invalid credentials'));
            return redirect("manager");
        }
        else{
            if($slugUpdated != null){
                    $this->showToastrMessage('error', __('Ops! You have entered invalid credentials'));
                    return redirect(route('student-login',['id'=>$stTypeUpdated, 'slug'=>$slugUpdated]));
            }
            else{
                $this->showToastrMessage('error', __('Ops! You have entered invalid credentials'));
                return redirect("student-login");
            }  
        }
     
    }


  

    protected function _registerOrLoginUser($data)
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

    public function forgetPassword()
    {
        $data = array();
        $data['title'] = __("Forget Password");
        return view('auth.forgot', $data);
    }

    public function forgetPasswordEmail(Request $request)
    {
        $email = $request->email;

        $user = User::whereEmail($email)->first();
        if ($user)
        {
            $verification_code = rand(10000, 99999);
            if ($verification_code)
            {
                $user->forgot_token = $verification_code;
                $user->save();
            }

            $sendEmail = new EmailSendService();
            if(!($sendEmail->sendForgetPasswordEmail($user, $verification_code))){
                toastrMessage('error', 'Something is wrong. Try after few minutes!');
                return redirect()->back();
            }

            Session::put('email', $email);
            Session::put('verification_code', $verification_code);

            $this->showToastrMessage('success', __('Verification code sent your email. Please check.'));
            return redirect()->route('reset-password');
        }

        $this->showToastrMessage('error', __('Your Email is incorrect!'));
        return redirect()->back();
    }


      /**
     * Handle sending a password reset email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
    
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(),302);
        }

      
        $user = User::whereEmail($request->input('email'))->first();
        if ($user)
        {
            $verification_code = rand(10000, 99999);
            if ($verification_code)
            {
                $user->forgot_token = $verification_code;
                $user->save();
            }

            $sendEmail = new EmailSendService();
            if(!($sendEmail->sendForgetPasswordEmail($user, $verification_code))){
                // toastrMessage('error', 'Something is wrong. Try after few minutes!');
                // return redirect()->back();
                return response()->json(['message' => __('Something is wrong. Try after few minutes!')], 302);
            }

            return response()->json(['message' => __('Verification code sent your email. Please check.')], 200);
        }
        else{
            // $this->showToastrMessage('error', __('Your Email is incorrect!'));
            return response()->json(['message' => __('Your Email is incorrect!')], 302);
        }
    }


    public function resetPassword()
    {
        $data['title'] = __("Reset Password");
        return view('auth.reset-password', $data);
    }

    public function resetPasswordCheck(Request $request)
    {
        $request->validate([
            'verification_code' => 'required',
        ]);

        $email = Session::get('email');
        $verification_code = Session::get('verification_code');
        if ($request->verification_code == $verification_code)
        {
            $user = User::whereEmail($email)->whereForgotToken($verification_code)->first();

            if (!$user) {
                $this->showToastrMessage('error', __('Your verification code is incorrect!'));
            } else {
                $request->validate([
                    'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                    'password_confirmation' => 'min:6'
                ]);

                $user->password = Hash::make($request->password);
                $user->email_verified_at = now();
                $user->forgot_token = null;
                $user->save();
                Session::put('email', '');
                Session::put('verification_code', '');
                $this->showToastrMessage('success', 'Successfully changed your password.');
                return redirect()->route('login');
            }
        } else {
            $this->showToastrMessage('error', __('Your verification code is incorrect!'));
        }


        return redirect()->back();
    }



       /**
     * Handle resetting the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPasswordApi(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $email = $request->input('email');
        $otp = (string) $request->input('otp');
        $testOtpAllowed = app()->environment(['local', 'testing'])
            || filter_var(env('ALLOW_TEST_OTP', false), FILTER_VALIDATE_BOOLEAN);

        if ($testOtpAllowed && $otp === '123456') {
            $user = User::whereEmail($email)->first();
        } else {
            $user = User::whereEmail($email)->whereForgotToken($otp)->first();
        }

        if (!$user) {
                return response()->json(['message' => __('Your OTP Verification Code Is Incorrect!')], 302);
        } else {
            // $request->validate([
            //     'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            //     'password_confirmation' => 'min:6'
            // ]);
            $user->password = Hash::make($request->input('password'));
            $user->email_verified_at = now();
            $user->forgot_token = null;
            $user->save();
            return response()->json(['message' => __('Successfully changed your password.')], 200);
           
        }
    }


     /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
     
        //remove device tracking session
        if(get_option('device_control')){
            $device_uuid = $request->cookie('_uuid_d');
            Cookie::queue(Cookie::forget('_uuid_d'));
            Device::join('device_user', 'devices.id', '=', 'device_user.device_id')->where('devices.device_uuid', $device_uuid)->update(['deleted_at' => now()]);
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    public function logoutApiPost(Request $request){  
        
        $user = Auth::guard('sanctum')->user();
        if($user == null){
            return response()->json(['message' =>  __('Unauthenticated!')], 401);
        }
    
        try {
            $user->tokens()->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to log out'], 500);
        }

        return response()->json(['message' =>  __('Logged Out Successfully!'), 
        'redirectRoute'=>route('login')], 200);

    }

}
