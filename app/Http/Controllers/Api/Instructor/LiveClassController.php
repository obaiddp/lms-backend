<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Enrollment;
use App\Models\GmeetSetting;
use App\Models\Instructor;
use App\Models\LiveClass;
use App\Models\LiveClassStudentLink;
use App\Models\Student;
use App\Models\ZoomSetting;
use Illuminate\Http\Request;
use App\Tools\Repositories\Crud;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LiveClassController extends Controller
{
    protected $model, $courseModel;

    public function __construct(LiveClass $liveClass, Course $course)
    {
        $this->model = new CRUD($liveClass);
        $this->courseModel = new CRUD($course);
    }

    public function courseLiveClassIndexApi()
    {
        $courseInstructorIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())
            ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
            ->whereNull('courses.organization_id')
            ->where('course_instructor.status', STATUS_APPROVED)
            ->select('course_id')
            ->get()
            ->pluck('course_id')
            ->toArray();

        $mainCourseIds = Course::where('user_id', auth()->id())->select('id')->pluck('id')->toArray();

        $courseInstructorIds = array_unique(array_merge($mainCourseIds, $courseInstructorIds));

        $courses = Course::select('id', 'uuid', 'user_id', 'image', 'title')
            ->whereIn('id', $courseInstructorIds)
            ->whereNull('organization_id')
            ->withCount([
                'liveClasses as total_upcoming' => function ($q) {
                    $q->select(DB::raw("COUNT(id)"))
                        ->where(function ($q) {
                            $q->whereDate('date', now())
                                ->whereTime('time', '>=', now());
                        })
                        ->orWhere(function ($q) {
                            $q->whereDate('date', '>', now());
                        });
                },
                'liveClasses as total_current' => function ($q) {
                    $q->select(DB::raw("COUNT(id)"))
                        ->where(function ($q) {
                            $q->whereDate('date', now())
                                ->whereTime('time', '<=', now())
                                ->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
                        });
                },
                'liveClasses as total_past' => function ($q) {
                    $q->select(DB::raw("COUNT(id)"))
                        ->where(function ($q) {
                            $q->whereDate('date', now())
                                ->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
                        })
                        ->orWhere(function ($q) {
                            $q->whereDate('date', '<', now());
                        });
                },
            ])
            ->orderBy('id', 'DESC')
            ->get(); // use ->get() if you don’t want pagination
                
        $findInstructor = Instructor::where('user_id', auth()->id())->first();

        return response()->json([
            'instructor_id' => $findInstructor->id ?? null,
            'instructor_name' => $findInstructor ? $findInstructor->first_name . ' ' . $findInstructor->last_name : null,
            'courses' => $courses,
        ]);
    }


    public function liveClassIndexApi(Request $request, $course_uuid)
    {
        try {
            // Get the course
            $course = Course::where('uuid', $course_uuid)
                ->select('id', 'uuid', 'title', 'user_id')
                ->first();

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            // Get the instructor info
            $instructor = Instructor::where('user_id', Auth::id())->first();
            $instructorData = $instructor ? [
                'instructor_id' => $instructor->id,
                'instructor_name' => $instructor->first_name . ' ' . $instructor->last_name
            ] : null;

       
            // Get upcoming live classes
            $upcomingLiveClasses = LiveClass::whereCourseId($course->id)
                ->whereUserId(Auth::id())
                ->where(function ($q) {
                    $q->whereDate('date', now());
                    $q->whereTime('time', '>=', now());
                })
                ->orWhere(function ($q) {
                    $q->whereDate('date', '>', now());
                })
                ->latest()
                ->get();

            // Get current live classes
            $currentLiveClasses = LiveClass::whereCourseId($course->id)
                ->whereUserId(Auth::id())
                ->where(function ($q) {
                    $q->whereDate('date', now());
                    $q->whereTime('time', '<=', now());
                    $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '>=', now());
                })
                ->latest()
                ->get();

            // Get past live classes
            $pastLiveClasses = LiveClass::whereCourseId($course->id)
                ->whereUserId(Auth::id())
                ->where(function ($q) {
                    $q->whereDate('date', now());
                    $q->whereTime(DB::raw('SEC_TO_TIME((duration*60) + TIME_TO_SEC(time))'), '<', now());
                })
                ->orWhere(function ($q) {
                    $q->whereDate('date', '<', now());
                })
                ->latest()
                ->get();

                    $zoomSettings = ZoomSetting::where('status',1)->latest()->first();
                        if($zoomSettings){
                            $sdk_key = $zoomSettings->sdk_key;
                            $sdk_secret = $zoomSettings->sdk_secret;
                            $api_key = $zoomSettings->api_key;
                            $api_secret = $zoomSettings->api_secret;
                            $api_jwt_token = $zoomSettings->api_jwt_token;
                        }
                        else{
                            $sdk_key = null;
                            $sdk_secret = null;
                            $api_key = null;
                            $api_secret = null;
                            $api_jwt_token = null;
                        }


            return response()->json([
                'success' => true,
                'data' => [
                    'course' => $course,
                    'instructor' => $instructorData,
                    // 'zoomApiKey'=> $api_key,
                    // 'zoomApiSecret'=> $api_secret,
                    'zoomSdkKey'=> $sdk_key,
                    'zoomSdkSecret'=> $sdk_secret,
                    // 'zoomApiJwtToken'=> $api_jwt_token,
                    'upcoming_live_classes' => $upcomingLiveClasses,
                    'current_live_classes' => $currentLiveClasses,
                    'past_live_classes' => $pastLiveClasses
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createLiveClassApi($course_uuid)
    {
        try {
            // Get the course with only necessary fields
            $course = Course::where('uuid', $course_uuid)
                ->select('id', 'uuid', 'title', 'user_id')
                ->first();

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            // Get authorized Gmeet settings for the authenticated user
            $gmeet = GmeetSetting::where('user_id', Auth::id())
                ->where('status', GMEET_AUTHORIZE)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => 'Live Class Create',
                    'course' => $course,
                    'gmeet_settings' => $gmeet
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeLiveClassApi(Request $request, $course_uuid)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'class_topic' => 'required|max:255',
            'date' => 'required',
            'duration' => 'required|numeric',
            'learning_tool' => 'required|in:bbb,zoom,jitsi,gmeet',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check learning tool availability
        $learningTool = $request->learning_tool;
        if ($learningTool == 'bbb' && get_option('bbb_status') == 0) {
            return response()->json([
                'success' => false,
                'message' => 'BigBlueButton Status is disabled in application settings'
            ], 400);
        }

        if ($learningTool == 'zoom') {
            $zoomSettings = ZoomSetting::where('status', 1)->latest()->first();
            if (!$zoomSettings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zoom Account is disabled in application settings'
                ], 400);
            }
        }

        try {
            DB::beginTransaction();

            // Get course
            $course = Course::where('uuid', $course_uuid)->first();
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Course not found'
                ], 404);
            }

            // Parse date and time from the request
            $dateTime = strtotime($request->date);
            $date = date('Y-m-d', $dateTime);
            $time = date('H:i:s', $dateTime);

            // Calculate time values
            $start_time_unix = strtotime($time);
            $end_time_unix = $start_time_unix + ($request->duration * 60);
            $start_time_formatted = Carbon::createFromTimestamp($start_time_unix)->format('H:i');
            $end_time_formatted = Carbon::createFromTimestamp($end_time_unix)->format('H:i');

            // Check if class is in the past
            $classDateFormatted = date("d-m-Y", $dateTime);
            $currentDateFormatted = date('d-m-Y');
            $classTimeFormatted = date("h:i:sa", $dateTime);
            $currentTimeFormatted = date('h:i:sa');

            if (
                strtotime($classDateFormatted) < strtotime($currentDateFormatted) ||
                (strtotime($classDateFormatted) == strtotime($currentDateFormatted) &&
                    strtotime($classTimeFormatted) <= strtotime($currentTimeFormatted))
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot schedule a class in the past'
                ], 400);
            }

            // Determine class status
            $status = strtotime($classDateFormatted) > strtotime($currentDateFormatted) ? 'Scheduled' : 'Pending';

            // Create live class
            $liveClass = new LiveClass();
            $liveClass->course_id = $course->id;
            $liveClass->user_id = Auth::id();
            $liveClass->class_topic = $request->class_topic;
            $liveClass->date = $date;
            $liveClass->time = $time;
            $liveClass->duration = $request->duration;
            $liveClass->meeting_host_name = $learningTool;
            $liveClass->status = $status;
            $liveClass->start_time_unix = $start_time_unix;
            $liveClass->end_time_unix = $end_time_unix;
            $liveClass->start_time = $start_time_formatted;
            $liveClass->end_time = $end_time_formatted;
            $liveClass->save();

            // Create student links
            $enrollments = Enrollment::where('course_id', $course->id)
                ->where('status', ACCESS_PERIOD_ACTIVE)
                ->where('course_type', 1)
                ->orderBy('course_id', 'ASC')
                ->orderBy('id', 'DESC')
                ->get();

            $studentIds = [];
            foreach ($enrollments as $enrollment) {
                $student = Student::where('user_id', $enrollment->user_id)->first();
                if ($student) {
                    $studentIds[] = $student->id;

                    $classLink = new LiveClassStudentLink();
                    $classLink->live_class_id = $liveClass->id;
                    $classLink->student_id = $student->id;
                    $classLink->course_id = $course->id;
                    $classLink->save();
                }
            }

            // Send notifications (implementation depends on your notification system)
            $userIds = array_unique($studentIds);
            $targetUrl = route('student.my-course.show', $course->slug);

            // Here you would add your notification sending logic
            // Example:
            // $emailService = new EmailSendService();
            // $emailService->sendNewLiveClassToStudent($userIds, $targetUrl);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Live class created successfully',
                'data' => $liveClass
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create live class',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($uuid)
    {
        try {
            // Find the live class
            $liveClass = LiveClass::where('uuid', $uuid)->first();

            if (!$liveClass) {
                return response()->json([
                    'success' => false,
                    'message' => 'Live class not found'
                ], 404);
            }

            // Delete associated student links
            LiveClassStudentLink::where('live_class_id', $liveClass->id)->delete();

            // Delete the live class
            $liveClass->delete();

            return response()->json([
                'success' => true,
                'message' => 'Live class deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete live class',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
