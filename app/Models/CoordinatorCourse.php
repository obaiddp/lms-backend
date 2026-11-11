<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoordinatorCourse extends Model
{
    use HasFactory;

    protected $table = 'coordinator_courses';

    protected $fillable = [
        'course_id',
        'coordinator_id',
    ];

    public function course(){
        return $this->belongsTo(Course::class);
    }
    
    public function coordinator(){
        return $this->belongsTo(Coordinator::class, 'coordinator_id');
    }

  

}
