<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorAttendance extends Model
{
    use HasFactory;

    protected $table = 'instructor_attendance';
    protected $fillable = [
        'student_id',
        'instructor_id',
        'attendance_date',
        'month',
        'year',
        'tqc_lesson',
        'nq_lesson',
        'uqc_lesson',
        'hifz_lesson',
        'lesson_delivered',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }


}
