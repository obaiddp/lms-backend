<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseCourse extends Model
{
   

    protected $fillable = ['license_id', 'course_id'];



     public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }



     public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}
