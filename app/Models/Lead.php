<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'course_id',
        'lead_from',
    ];


    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    

}
