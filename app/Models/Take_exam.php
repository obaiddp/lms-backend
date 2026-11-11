<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Take_exam extends Model
{
    use HasFactory;

    protected $table = 'take_exams';
    protected $primaryKey = 'id';
    protected $fillable = [
        'exam_id',
        'lecture_id',
        'obtained_percentage',
        'passing_percentage',
        'passed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }


    public function lecture()
    {
        return $this->belongsTo(Course_lecture::class, 'lecture_id');
    }


    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->user_id =  auth()->id();
        });
    }


}
