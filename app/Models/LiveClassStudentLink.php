<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveClassStudentLink extends Model
{
    use HasFactory;
    protected $table = 'live_class_student_links';
    protected $fillable = [
        'live_class_id',
        'student_id',
        'course_id',
        'StudentURI',
        'created_at',
        'updated_at',
    ];


    public function live_class()
    {
        return $this->belongsTo(LiveClass::class, 'live_class_id');
    }

    public function student_id()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function course_id()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

}
