<?php

namespace App\Http\Controllers\Api\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Enrollment;
use App\Models\User;
use App\Traits\ApiStatusTrait;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    use ApiStatusTrait;
    
    public function allStudentsApi(Request $request)
    {
        $user = auth()->user();
    
        if (!$user || $user->role != 2) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
    
        $instructorId = $user->id;
    
        $mainCourseIds = Course::where('user_id', $instructorId)->pluck('id')->toArray();
    
        $coInstructorIds = CourseInstructor::where('course_instructor.instructor_id', $instructorId)
            ->where('course_instructor.status', STATUS_APPROVED)
            ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
            ->whereNull('courses.organization_id')
            ->pluck('course_instructor.course_id')
            ->toArray();
    
        $courseIds = array_unique(array_merge($mainCourseIds, $coInstructorIds));
    
        $enrollments = Enrollment::whereIn('course_id', $courseIds);
    
        if ($request->has('course_id')) {
            $enrollments->where('course_id', $request->course_id);
        }
    
        $allEnrollments = $enrollments
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'enrollments.user_id')
                     ->whereNull('users.deleted_at');
            })
            ->select('enrollments.*')
            ->with(['user', 'course'])
            ->get()
            ->map(function ($enrollment) {
                return [
                    'user_id' => $enrollment->user?->id,
                    'name' => $enrollment->user?->name,
                    'email' => $enrollment->user?->email,
                    'image' => $enrollment->user?->image ? asset($enrollment->user->image) : null,
                    'phone_number' => $enrollment->user?->phone_number,
                    'country' => $enrollment->user->student?->country?->country_name,
                    'course_id' => $enrollment->course?->id,
                    'course_title' => $enrollment->course?->title,
                    'slug' => $enrollment->course?->slug,
                    'uuid' => $enrollment->course?->uuid,
                    'enrolled_date' => $enrollment->created_at->format('d M, Y'),
                ];
            });
    
        return response()->json([
            'success' => true,
            'message' => 'Student list fetched successfully.',
            'data' => $allEnrollments
        ]);
    }
    



      public function getStudentsApi(Request $request)
    {
        $user = auth()->user();
    
        if (!$user || $user->role != 2) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }
    
        $instructorId = $user->id;
    
        $mainCourseIds = Course::where('user_id', $instructorId)->pluck('id')->toArray();
    
        $coInstructorIds = CourseInstructor::where('course_instructor.instructor_id', $instructorId)
            ->where('course_instructor.status', STATUS_APPROVED)
            ->join('courses', 'courses.id', '=', 'course_instructor.course_id')
            ->whereNull('courses.organization_id')
            ->pluck('course_instructor.course_id')
            ->toArray();
    
        $courseIds = array_unique(array_merge($mainCourseIds, $coInstructorIds));
    
        $enrollments = Enrollment::whereIn('course_id', $courseIds);
    
        if ($request->has('course_id')) {
            $enrollments->where('course_id', $request->course_id);
        }
    
        $allEnrollments = $enrollments
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'enrollments.user_id')
                     ->whereNull('users.deleted_at')
                     ->where('users.role', 3);
            })
            ->select('enrollments.*')
            ->with(['user.student', 'course'])
            ->latest('enrollments.created_at') // Sorts by newest
            ->groupBy('enrollments.user_id')  // Forces uniqueness at DB level
            ->get()
            ->map(function ($enrollment) {
                return [
                    'id'   => $enrollment->user?->student?->id,
                    'name' => $enrollment->user?->name,
                ];
            });
    
        return response()->json([
            'success' => true,
            'message' => 'Student list fetched successfully.',
            'data' => $allEnrollments
        ]);
    }
}
