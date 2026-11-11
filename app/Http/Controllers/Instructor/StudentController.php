<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function allStudentIndex(Request $request)
    {
        $data['title'] = 'All Student';
        $data['navAllStudentActiveClass'] = 'active';

        $courseInstructorIds = CourseInstructor::where('course_instructor.instructor_id', auth()->id())->join('courses', 'courses.id', '=', 'course_instructor.course_id')->select('user_packages.*')->whereNull('courses.organization_id')->where('course_instructor.status',STATUS_APPROVED)->select('course_id')->get()->pluck('course_id')->toArray();
        $mainCourseIds = Course::where('user_id', auth()->id())->select('id')->pluck('id')->toArray();
        $courseInstructorIds = array_unique(array_merge($mainCourseIds, $courseInstructorIds));
      
        $data['courses'] = Course::whereIn('id', $courseInstructorIds)->get();
        $userCourseIds = Course::whereIn('id', $courseInstructorIds)->pluck('id')->toArray();
        $enrollments = Enrollment::whereIn('course_id', $userCourseIds);
     
   
        if ($request->course_id){
            $enrollments = $enrollments->whereCourseId($request->course_id);
        }
 

        $data['enrollments'] = $enrollments->join('users', function ($join) {
            $join->on('users.id', '=', 'enrollments.user_id')
                 ->whereNull('users.deleted_at');
        })->select('enrollments.*')->with('user')->with('course')->paginate();
   
        return view('instructor.all-student')->with($data);
    }

}