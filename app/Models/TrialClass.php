<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrialClass extends Model
{
    use HasFactory;
    protected $table = 'trial_classes';

    protected $fillable = [
        'student_id',
        'course_id',
        'prefer_date',
        'prefer_time',
        'status',
        'approved',
        'student_enrolled',
        'created_at',
        'updated_at',
        'invoice_pdf_path'
    ];
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

}
