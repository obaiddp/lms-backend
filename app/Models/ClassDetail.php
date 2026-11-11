<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDetail extends Model
{
    use HasFactory;
    protected $table = 'class_details';

    protected $fillable = [
        'class_id',
        'student_id',
        'instructor_id',
        'course_id',
        'slo_detail_id',
        'class_date',
        'class_time',
        'slo_progress',
        'slo_rank',
        'class_participation',
        'InstructorURI',
        'StudentURI',
        'AcademicURI',
        'meeting_id',
        'status',
        'std_classfeedback',
        'inst_classfeedback',
        'std_feedback_email_sent',
        'inst_feedback_email_sent',
        'instructor_start_class_time',
        'start_time',
        'end_time',
        'start_time_zoom',
        'end_time_zoom',
        'start_time_unix',
        'end_time_unix',
        'is_instructor_joined',
        'is_coordinator_joined',
        'is_moderator_left',
        'created_at',
        'updated_at',
        'group_id',
        'zoom_meeting_signature',
        'zoom_meeting_password',
        'zoom_meeting_name',
        'google_meet_link',
        'is_meet_class_started',
        'google_event_id'
    ];


    public function class()
    {
        return $this->belongsTo(Lesson::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

}
