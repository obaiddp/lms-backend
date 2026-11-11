<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Assignment extends Model
{
    use HasFactory;

    protected $appends = ['file_url'];

    protected $hidden = [
        'file',
    ];

    public function getFileUrlAttribute()
    {
        if ($this->file) {
            return getVideoFile($this->file);
        } else {
            return asset('uploads/default/course.jpg');
        }
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignmentFiles()
    {
        return $this->hasMany(AssignmentFile::class);
    }

    public function course_lectures()
    {
        return $this->hasMany(Course_lecture::class, 'assignment_id');
    }

    public function assignmentSubmits()
    {
        return $this->hasMany(AssignmentSubmit::class);
    }


    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->uuid =  Str::uuid()->toString();
            $model->user_id =  auth()->id();
        });
    }
}
