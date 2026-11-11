<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $table = 'classes';
    protected $fillable = [
        'no_of_students',
        'student_id',
        'instructor_id',
        'no_of_classes',
        'duration',
        'type',
        'prefer_days',
        'free_class',
        'learning_tool',
        'InstructorURI',
        'StudentURI',
        'AcademicURI',
        'meeting_id',
        'zoom_meeting_signature',
        'zoom_meeting_name',
        'created_at',
        'updated_at',
    ];

    public function class_details()
    {
        return $this->hasMany(ClassDetail::class, 'class_id');
    }


    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }
    
}
