<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Services\EmailSendService;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Enrollment;
use App\Models\Coordinator;
use App\Models\NoticeBoard;
use App\Tools\Repositories\Crud;
use App\Traits\ApiStatusTrait;
use App\Traits\General;
use App\Traits\SendNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoticeBoardApiController extends Controller
{
    use General, SendNotification, ApiStatusTrait;
    protected $model, $courseModel;
    //
    public function __construct(NoticeBoard $noticeBoard, Course $course)
    {
        $this->model = new Crud($noticeBoard);
        $this->courseModel = new CRUD($course);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function courseNoticeIndexApiBackUp()
    // {
    //     $instructorCourseIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())
    //         ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
    //         ->whereNull('courses.organization_id')
    //         ->where('course_instructor.status', STATUS_APPROVED)
    //         ->pluck('course_instructor.course_id')
    //         ->toArray();

    //     $mainCourseIds = Course::where('user_id', auth()->id())
    //         ->pluck('id')
    //         ->toArray();

    //     $courseIds = array_unique(array_merge($mainCourseIds, $instructorCourseIds));

    //     $courses = Course::whereIn('id', $courseIds)
    //         ->whereNull('organization_id')
    //         ->orderBy('id', 'DESC')
    //         ->select('id', 'title', 'uuid', 'image')
    //         ->get()
    //         ->map(function ($course) {
    //             $course->image = asset($course->image);
    //             $course->total_notices = $course->notices()->count();
    //             return $course;
    //         });

    //     $data = [
    //         'title' => 'Notice Board',
    //         'courses' => $courses,
    //     ];

    //     return $this->success($data);
    // }



       public function courseNoticeIndexApi()
    {

        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        if($user->role == 2){
            $instructorCourseIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())
                ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
                ->whereNull('courses.organization_id')
                ->where('course_instructor.status', STATUS_APPROVED)
                ->pluck('course_instructor.course_id')
                ->toArray();

            $mainCourseIds = Course::where('user_id', auth()->id())
                ->pluck('id')
                ->toArray();

            $courseIds = array_unique(array_merge($mainCourseIds, $instructorCourseIds));

            $courses = Course::whereIn('id', $courseIds)
                ->whereNull('organization_id')
                ->orderBy('id', 'DESC')
                ->select('id', 'title', 'uuid', 'image')
                ->get()
                ->map(function ($course) {
                    $course->image = asset($course->image);
                    $course->total_notices = $course->notices()->count();
                    return $course;
                });

            $data = [
                'title' => 'Notice Board',
                'courses' => $courses,
            ];

            return $this->success($data);
        }
        else{
             // Get coordinator ID
            $coordinatorId = Coordinator::where('user_id', $user->id)->value('id');
            if (!$coordinatorId) {
                return response()->json($response);
            }
            $coordinatorCourseIds = CourseInstructor::where('coordinator_courses.coordinator_id', $coordinatorId)
                ->join('courses', 'courses.id', '=', 'coordinator_courses.course_id')
                ->whereNull('courses.organization_id')
                ->pluck('coordinator_courses.course_id')
                ->toArray();

            $mainCourseIds = Course::whereIn('id', $coordinatorCourseIds)
                ->pluck('id')
                ->toArray();

            $courseIds = array_unique(array_merge($mainCourseIds, $coordinatorCourseIds));

            $courses = Course::whereIn('id', $courseIds)
                ->whereNull('organization_id')
                ->orderBy('id', 'DESC')
                ->select('id', 'title', 'uuid', 'image')
                ->get()
                ->map(function ($course) {
                    $course->image = asset($course->image);
                    $course->total_notices = $course->notices()->count();
                    return $course;
                });

            $data = [
                'title' => 'Notice Board',
                'courses' => $courses,
            ];

            return $this->success($data);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function noticeIndexApi($course_uuid)
    {
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        // Get the course
        $course = $this->courseModel->getRecordByUuid($course_uuid);

        if (!$course) {
            return $this->error([], 'Course not found.', 404);
        }

        // Get notices for this course and user
        $notices = NoticeBoard::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'uuid', 'topic', 'created_at']); // Only fetch topic and date

        // Format notices with date
        $formattedNotices = $notices->map(function ($notice) {
            return [
                'id' => $notice->id,
                'uuid' => $notice->uuid,
                'topic' => $notice->topic,
                'date' => $notice->created_at->format('Y-m-d'),
            ];
        });

        $data = [
            'course_title' => $course->title,
            'notices' => $formattedNotices,
        ];

        return $this->success($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createNoticeApi($course_uuid)
    {

        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        $course = $this->courseModel->getRecordByUuid($course_uuid);

        if (!$course) {
            return $this->error([], 'Course not found.', 404);
        }

        $data = [
            'course_title' => $course->title,
            'course_uuid' => $course->uuid,
            'course_id' => $course->id,
        ];

        return $this->success($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storeNoticeApi(Request $request, $course_uuid)
    {

        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        $validator = Validator::make($request->all(), [
            'topic' => 'required',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors(), 'Validation error');
        }

        $course = $this->courseModel->getRecordByUuid($course_uuid);
        if (!$course) {
            return $this->error([], 'Course not found.', 404);
        }

        $notice = new NoticeBoard();
        $notice->course_id = $course->id;
        $notice->user_id = $user->id; // assuming you want to store the user who created it
        $notice->topic = $request->topic;
        $notice->details = $request->details;
        $notice->save();

        // Notify enrolled students
        $students = Enrollment::where('course_id', $course->id)->select('user_id')->get();
        $userIds = $students->pluck('user_id')->toArray();
        $target_url = route('student.my-course.show', $course->slug);
        
        // Send individual notifications
        foreach ($userIds as $userId) {
            $this->send(__('New notice has been added'), 3, $target_url, $userId);
        }
        
        // Send bulk email
        (new EmailSendService())->sendNewNoticeToStudent($userIds, $target_url);

        return $this->success(['message' => 'Notice created successfully']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $course_uuid
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */

    public function viewNoticeApi($course_uuid, $uuid)
    {

        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $notice = $this->model->getRecordByUuid($uuid);

        if (!$course || !$notice) {
            return $this->error([], 'Course or Notice not found.', 404);
        }

        return $this->success([
            'course_title' => $course->title,
            'notice' => [
                'topic' => $notice->topic,
                'details' => $notice->details,
                'date' => $notice->created_at->format('Y-m-d'),
            ],
        ]);
    }

    public function editNoticeApi($course_uuid, $uuid)
    {

        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        $course = $this->courseModel->getRecordByUuid($course_uuid);
        $notice = $this->model->getRecordByUuid($uuid);

        if (!$course || !$notice) {
            return $this->error([], 'Course or Notice not found.', 404);
        }

        return $this->success([
            'course_title' => $course->title,
            'notice' => [
                'topic' => $notice->topic,
                'details' => $notice->details,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $course_uuid
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */

    public function updateNoticeApi(Request $request, $course_uuid, $uuid)
    {
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }

        $request->validate([
            'topic' => 'required',
            'details' => 'required',
        ]);

        $notice = $this->model->getRecordByUuid($uuid);
        if (!$notice) {
            return $this->error([], 'Notice not found.', 404);
        }

        $notice->topic = $request->topic;
        $notice->details = $request->details;
        $notice->save();

        return $this->success(['message' => 'Notice updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */

    public function deleteNoticeApi($uuid)
    {
        $user = Auth::user();
        if($user){
            $role = $user->role;
            if($role != 2 && $role != 5){ // Instructor
                return response()->json(['message' => 'You Do Not Have The Permission To Access This Route'], 302);
            }
        }
        else{
            return response()->json(['message' => 'User Is Not Logged In'], 302);
        }
        
        $notice = $this->model->getRecordByUuid($uuid);
        if (!$notice) {
            return $this->error([], 'Notice not found.', 404);
        }

        $notice->delete();

        return $this->success(['message' => 'Notice deleted successfully']);
    }
}
