<?php

namespace App\Http\Controllers;

use App\Models\UserPackage;
use App\Traits\General;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use IvanoMatteo\LaravelDeviceTracking\Models\Device;
use App\Http\Controllers\Controller;
use App\Models\AffiliateHistory;
use Laravel\Socialite\Facades\Socialite;
use App\Models\BookingHistory;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\LiveClass;
use App\Models\LiveClassStudentLink;
use App\Models\Student;
use App\Models\ZoomSetting;
use App\Models\CourseInstructor;
use App\Models\Order_item;
use Illuminate\Support\Str;
use App\Models\RankingLevel;
use App\Models\Enrollment;

use Illuminate\Support\Facades\DB;
use App\Models\Lesson;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogTag;
use App\Models\Take_exam;
use App\Models\ClassDetail;
use App\Libraries\Bbb;
use App\Models\Notification;
use Carbon\Carbon;
use App\Models\Answer;
use App\Models\Assignment;
use App\Models\AssignmentSubmit;
use App\Models\InstructorFeature;
use App\Models\Course_lecture;
use App\Models\Course_lecture_views;
use App\Models\InstructorProcedure;
use App\Models\Organization;
use App\Models\CartManagement;
use App\Models\State;
use App\Models\User;
use App\Models\Resource;
use App\Models\ResourceCategory;
use App\Models\MeezanIpnNotification;

class HomeController extends Controller
{
    use General;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    
   
     public function checkTimeZones(){
        $data = timezones_array();
        foreach ($data as $key => $dt) {
            dd($dt);
        }
      
     }

    public function index()
    {
        if (Auth::user()->is_admin())
        {
            return redirect(route('admin.dashboard'));

        } else {
            return redirect(route('main.index'));
        }
    }

    /**
     * Show all the login device of current user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function allLoginDevice()
    {
        $data['devices'] = auth()->user()->device;
        $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->where('package_type', PACKAGE_TYPE_SUBSCRIPTION)->where('user_packages.user_id', auth()->id())->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->with('enrollments')->select('user_packages.device')->first();
        if(!is_null($userPackage)){
            $limit =  $userPackage->device;
        }
        else{
            $limit =  get_option('device_limit');
        }

        $data['limit'] = $limit;
        return view('frontend.logout_devices', $data);
    }

    /**
     * Show all the login device of current user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function logoutDevice($device_id = NULL)
    {
        $userPackage = UserPackage::join('packages', 'packages.id', '=', 'user_packages.package_id')->where('package_type', PACKAGE_TYPE_SUBSCRIPTION)->where('user_packages.user_id', auth()->id())->where('user_packages.status', PACKAGE_STATUS_ACTIVE)->where('enroll_date', '<=', now())->where('expired_date', '>=', now())->with('enrollments')->select('user_packages.device')->first();
        if(!is_null($userPackage)){
            $limit =  $userPackage->device;
        }
        else{
            $limit =  get_option('device_limit');
        }

        if($device_id){
            Device::join('device_user', 'devices.id', '=', 'device_user.device_id')->where('devices.id', $device_id)->update(['deleted_at' => now()]);
            Cookie::queue(Cookie::forget('_uuid_d'));
            $this->showToastrMessage('success', 'Logout device successfully.');
            $device_count = auth()->user()->device->count();
            if($device_count < $limit){
                \DeviceTracker::detectFindAndUpdate();
            }
        }
        else{
            Device::join('device_user', 'devices.id', '=', 'device_user.device_id')->where('user_id', auth()->id())->update(['deleted_at' => now()]);
            $this->showToastrMessage('success', 'Logout from all device successfully. Please login to continue.');
            Cookie::queue(Cookie::forget('_uuid_d'));
            Auth::logout();
        }

        return redirect('/');
    }



    public function checkLessons(){
        
        $data['title'] = 'Class Schedule';
        $instructor_user_id=auth()->id();
        dd($instructor_user_id);
        $getInstructor = Instructor::where('user_id',$instructor_user_id)->first();
        $instructor_id = $getInstructor->id;
        $today_date = date('Y-m-d');
        $getIndividualClasses = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Individual');
        })->orderBy('class_date','ASC')
          ->orderBy('class_time','ASC')->paginate(25);

          $getGroupClasses = ClassDetail::where('instructor_id',$instructor_id)->where('class_date' ,'>=',$today_date)->wherehas('class' , function($query){
            $query->where('type', 'Group');
        })->orderBy('class_id','DESC')
        ->orderBy('class_date','DESC')
        ->groupBy('class_id')
        ->paginate(25);

        $data['getIndividualClasses'] = $getIndividualClasses;
        dd("123");
        $data['getGroupClasses'] = $getGroupClasses;
        $data['base_url'] = url('/');

      
      
     }




}
