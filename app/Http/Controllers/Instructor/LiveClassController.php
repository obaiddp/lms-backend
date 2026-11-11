<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Services\EmailSendService;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Enrollment;
use App\Models\GmeetSetting;
use App\Models\LiveClass;
use App\Models\Student;
use App\Models\LiveClassStudentLink;
use App\Models\Order_item;
use App\Models\ZoomSetting;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use Carbon\Carbon;
use App\Traits\SendNotification;
use App\Traits\ZoomMeetingTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JoisarJignesh\Bigbluebutton\Facades\Bigbluebutton;

class LiveClassController extends Controller
{
    use General, SendNotification;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;
    protected $model, $courseModel;

    public function __construct(LiveClass $liveClass, Course $course)
    {
        $this->model = new CRUD($liveClass);
        $this->courseModel = new CRUD($course);
    }

    public function courseLiveClassIndex()
    {
        $data['title'] = 'Live Class';
        $data['navLiveClassActiveClass'] = 'active';

        $courseInstructorIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())->join('courses', 'courses.id', '=', 'course_instructor.course_id')->select('user_packages.*')->whereNull('courses.organization_id')->where('course_instructor.status',STATUS_APPROVED)->select('course_id')->get()->pluck('course_id')->toArray();
        $mainCourseIds = Course::where('user_id', auth()->id())->select('id')->pluck('id')->toArray();

        $courseInstructorIds = array_unique(array_merge($mainCourseIds, $courseInstructorIds));
        $data['courses'] = Course::whereIn('id', $courseInstructorIds)
        ->whereNull('organization_id')
        ->where('course_type',1)
        ->orWhere('course_type',2)
        ->orderBy('id', 'DESC');
        // $data['courses'] = Course::whereUserId(Auth::user()->id);
    
        $data['courses'] = $data['courses']->withCount([
            'liveClasses as total_upcoming' => function ($q) {
                $q->select(DB::raw("COUNT(id) as total_upcoming"));
                $q->where(function ($q) {
                    $q->whereDate('date', now());
                    $q->whereTime('time', '>=', now());
                });
                $q->orWhere(function ($q) {
                    $q->whereDate('date', '>', now());
                });
            },
            'liveClasses as total_current' => function ($q) {
                $q->select(DB::raw("COUNT(id) as total_current"));
                $q->where(function ($q) {
                    $q->whereDate('date', now());
                    $q->whereTime('time', '<=', now());
                    $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
                });
            },
            'liveClasses as total_past' => function ($q) {
                $q->select(DB::raw("COUNT(id) as total_past"));
                $q->where(function ($q) {
                    $q->whereDate('date', now());
                    $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
                });
                $q->orWhere(function ($q) {
                    $q->whereDate('date', '<', now());
                });
            },

        ])->paginate();
      
     

        $findInstructor = Instructor::where('user_id',auth()->id())->first();
        $data['instructor_id'] = $findInstructor->id;
        $data['instructor_name'] = $findInstructor->first_name.' '.$findInstructor->last_name;
     
        return view('instructor.live_class.live-class-course-list', $data);
    }

    public function liveClassIndex(Request $request, $course_uuid)
    {
        $data['title'] = 'Live Class List';
        $data['navLiveClassActiveClass'] = 'active';
        if ($request->past) {
            $data['navPastActive'] = 'active';
            $data['tabPastActive'] = 'show active';
        } 
        else if ($request->current) {
            $data['navCurrentActive'] = 'active';
            $data['tabCurrentActive'] = 'show active';
        } 
        else {
            $data['navUpcomingActive'] = 'active';
            $data['tabUpcomingActive'] = 'show active';
        }

        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);

        $data['upcoming_live_classes'] = LiveClass::whereCourseId($data['course']->id)->whereUserId(Auth::user()->id)
            ->where(function ($q) {
                $q->whereDate('date', now());
                $q->whereTime('time', '>', now());
            })
            ->orWhere(function ($q) {
                $q->whereDate('date', '>', now());
            })
            ->latest()->paginate(15, '*', 'upcoming');

        $data['current_live_classes'] = LiveClass::whereCourseId($data['course']->id)->whereUserId(Auth::user()->id)
            ->where(function ($q) {
                $q->whereDate('date', now());
                $q->whereTime('time', '<=', now());
                $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
            })
            ->latest()->paginate(15, '*', 'current');

        $data['past_live_classes'] = LiveClass::whereCourseId($data['course']->id)->whereUserId(Auth::user()->id)
            ->where(function ($q) {
                $q->whereDate('date', now());
                $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
            })
            ->orWhere(function ($q) {
                $q->whereDate('date', '<', now());
            })
            ->latest()->paginate(15, '*', 'past');

            $findInstructor = Instructor::where('user_id',auth()->id())->first();
            $data['instructor_id'] = $findInstructor->id;
            $data['instructor_name'] = $findInstructor->first_name.' '.$findInstructor->last_name;
        return view('instructor.live_class.live-class-list', $data);
    }

    public function createLiveCLass($course_uuid)
    {
        $data['title'] = 'Live Class Create';
        $data['navLiveClassActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $data['gmeet'] = GmeetSetting::whereUserId(Auth::id())->where('status', GMEET_AUTHORIZE)->first();
        return view('instructor.live_class.create', $data);
    }

    public function store(Request $request, $course_uuid)
    {
        $request->validate([
            'class_topic' => 'required|max:255',
            'date' => 'required',
            'duration' => 'required',
            'learning_tool' => 'required',
            // 'moderator_pw' => 'nullable|min:6',
            // 'attendee_pw' => 'nullable|min:6',
        ]);
      

        $Learning_Tool = $request->learning_tool;
        if($Learning_Tool == 'bbb'){
            if(get_option('bbb_status') == 0){
                $this->showToastrMessage('error', __('BigBlueButton Status is disabled in application_settings.'));
                return redirect()->back()->withInput();
                // return redirect()->route('classes.create_individual');
            }
        }
        else if($Learning_Tool == 'zoom'){
            $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
            if($zoomSettings == null){
                $this->showToastrMessage('error', __('Zoom Account is disabled in application_settings.'));
                return redirect()->back()->withInput();
                // return redirect()->route('classes.create_individual');
            }
        }

        try {
       
            DB::beginTransaction();
            $course = $this->courseModel->getRecordByUuid($course_uuid);
       
            $class = new LiveClass();
      
            $class->course_id = $course->id;
            $class->class_topic = $request->class_topic;
            $dateTime = strtotime($request->date);
            $date = date('Y-m-d', $dateTime);
            $time = date('H:i:s', $dateTime);
            $class->date = $date;
            $class->time = $time;
            $class->duration = $request->duration;
       
            // $class->start_url = $request->start_url;
            // $class->join_url = $request->join_url;
            $class->meeting_host_name = $Learning_Tool;
            // $class->meeting_id = $request->learning_tool == 'jitsi' ? $request->jitsi_meeting_id : $class->id . rand();
            // $class->moderator_pw = $request->moderator_pw;
            // $class->attendee_pw = $request->attendee_pw;
            $ClassDate1 = date("d-m-Y", $dateTime);
            $CurrentDate1 = date('d-m-Y');
            $ClassTimeUpdated = date("h:i:sa", $dateTime);
            $CurrentTimeUpdated = date('h:i:sa') ;
            $end_time = strtotime($time) + $request->duration*60; //added 40 mint in selected time
            $start_time = strtotime($time); //added 40 mints selected time
            $start_time_2 = Carbon::createFromTimestamp($start_time);
            $end_time_2 = Carbon::createFromTimestamp($end_time);
            $start_time_2 = $start_time_2->format('H:i');
            $end_time_2 = $end_time_2->format('H:i');
         
        
            if(strtotime($ClassDate1) > strtotime($CurrentDate1))    //Schedules Class On Next Day From Current Date
            {
                $classStatus = 'Scheduled';
                $lesson_date_updated = date('Y-m-d', $dateTime); // get date
                $class->status = $classStatus;
                $class->start_time_unix = $start_time;
                $class->end_time_unix = $end_time;
                $class->start_time = $start_time_2;
                $class->end_time = $end_time_2;
            }
            else if(strtotime($ClassDate1) == strtotime($CurrentDate1) && strtotime($ClassTimeUpdated) >  strtotime($CurrentTimeUpdated) )
            { //Pending Class On Current Day
                $classStatus = 'Pending';
                $lesson_date_updated = date('Y-m-d', $dateTime); // get date
                $class->status = $classStatus;
                $class->start_time_unix = $start_time;
                $class->end_time_unix = $end_time;
                $class->start_time = $start_time_2;
                $class->end_time = $end_time_2;
            }
            else{
                $this->showToastrMessage('error', __('You Can not assign Class in Previous Time.'));
                return redirect()->back()->withInput();
            }
      
            $class->save();

            $student_ids = array();
            $liveClassCourseEnrollments = Enrollment::where('course_id', $course->id)
            ->where('status', ACCESS_PERIOD_ACTIVE)
            // ->where('course_type', 1)
            ->orderBy('course_id', 'ASC')
            ->orderBy('id', 'DESC') // Ensure latest record is selected
            ->get();
      
            if(!$liveClassCourseEnrollments->isEmpty()){
                foreach ($liveClassCourseEnrollments as $key => $enrollment) {
                    $student = Student::where('user_id',$enrollment->user_id)->first();
                    if($student){
                        $student_id =  $student->id;
                        $student_ids[] = $student_id;
                    }
                
                }
                $student_ids = array_unique($student_ids);
            }
     
            if(!empty($student_ids)){
                foreach ($student_ids as $key => $student_id) {
                    $classLink = new LiveClassStudentLink();
                    $classLink->live_class_id = $class->id;
                    $classLink->student_id = $student_id;
                    $classLink->course_id = $course->id;
                    $classLink->save();
                }
            }
         
            
            /** ====== send notification to student ===== */
            $students = Enrollment::where('course_id', $course->id)->select('user_id')->get();
            $sendEmail = new EmailSendService();
            foreach ($students as $student) {
                $text = __("New Live Class Added By Instructor");
                $target_url = route('student.my-course.show', $course->slug);
                $this->send($text, 3, $target_url, $student->user_id);

            }

            $userIds = $students->pluck('user_id')->toArray();
            $sendEmail->sendNewLiveClassToStudent($userIds, $target_url);
            /** ====== send notification to student ===== */

            DB::commit();
            $this->showToastrMessage('success', 'Live Class Created Successfully');
            return redirect()->route('live-class.index', $course_uuid);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            $this->showToastrMessage('error', 'Please check your credentials');
            return redirect()->back()->withInput();
        }
    }

    public function view($course_uuid, $uuid)
    {
        $data['title'] = 'Live Class Details';
        $data['navLiveClassActiveClass'] = 'active';
        $data['course'] = $this->courseModel->getRecordByUuid($course_uuid);
        $data['liveClasses'] = $this->model->getRecordByUuid($uuid);
        return view('instructor.live_class.view', $data);
    }

    public function delete($uuid)
    {
        $getLiveClass = LiveClass::where('uuid',$uuid)->first();
        $live_class_id = $getLiveClass->id;
        $getLiveClassStudentLinks = LiveClassStudentLink::where('live_class_id',$live_class_id)->get();
        if(!$getLiveClassStudentLinks->isEmpty()){
            foreach ($getLiveClassStudentLinks as $key => $link) {
                $link->delete();
            }
        }
        $this->model->deleteByUuid($uuid);
        $this->showToastrMessage('error', __('Deleted Successfully'));
        return redirect()->back();
    }

    public function getZoomMeetingLink(Request $request)
    {
        $data = $request->all();
     
        // $zoom = ZoomSetting::find(auth()->id());
        $zoom = ZoomSetting::where('status',1)->latest()->first();
     
     
        $meeting = \Jubaer\Zoom\Facades\Zoom::createMeeting([
            "agenda" => (!empty($data['agenda'])) ? $data['agenda'] : null,
            "topic" => $data['topic'],
            "type" => 2, // 1 => instant, 2 => scheduled, 3 => recurring with no fixed time, 8 => recurring with fixed time
            "duration" => (int)$data['duration'], // in minutes
            "timezone" => @$zoom->timezone ?? 'Asia/Dhaka', // set your timezone
            "start_time" => $this->toZoomTimeFormat($data['start_date']), // set your start time
            "settings" => [
                'host_video' => @$zoom->host_video ? true : false,
                'participant_video' => @$zoom->participant_video ? true : false,
                'waiting_room' => @$zoom->waiting_room ? true : false,
                'join_before_host' => false, // if you want to join before host set true otherwise set false
                'mute_upon_entry' => false, // if you want to mute participants when they join the meeting set true otherwise set false
                'audio' => 'both', // values are 'both', 'telephony', 'voip'. default is both.
                'auto_recording' => 'none', // values are 'none', 'local', 'cloud'. default is none.
                'approval_type' => 0, // 0 => Automatically Approve, 1 => Manually Approve, 2 => No Registration Required
            ],

        ]);
     
        return response()->json([
            'start_url' => $meeting['data']['start_url'],
            'join_url' => $meeting['data']['join_url']
        ]);
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        try {
            $date = new \DateTime($dateTime);

            return $date->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : ' . $e->getMessage());

            return '';
        }
    }

    public function bigBlueButtonJoinMeeting($liveClassId)
    {
        $liveClass = LiveClass::find($liveClassId);
        if ($liveClass) {
            return redirect()->to(
                Bigbluebutton::join([
                    'meetingID' => $liveClass->meeting_id,
                    'userName' => auth()->user()->instructor()->name ?? auth()->user()->student()->name ?? auth()->user()->name,
                    'password' => $liveClass->attendee_pw //which user role want to join set password here
                ])
            );
        } else {
            $this->showToastrMessage('error', __('Live Class is not found'));
            return redirect()->back();
        }
    }

    public function jitsiJoinMeeting($liveClassUUId)
    {
        $data['title'] = 'Jitsi Meet';
        $data['liveClass'] = LiveClass::where('uuid', $liveClassUUId)->firstOrFail();
        return view('instructor.live_class.jitsi-live-class')->with($data);
    }
}
