<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InstructorCoordinator extends Model
{
    use HasFactory;
    protected $table = 'instructor_coordinators';
    protected $fillable = [
        'instructor_id',
        'coordinator_id',
    ];

    public function instructor()
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    public function coordinator()
    {
        return $this->belongsTo(Coordinator::class, 'coordinator_id');
    }
    
}
