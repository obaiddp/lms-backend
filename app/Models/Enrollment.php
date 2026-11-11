<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'order_id',
        'user_id',
        'owner_user_id',
        'course_id',
        'consultation_slot_id',
        'bundle_id',
        'user_package_id',
        'completed_time',
        'start_date',
        'trial_id',
        'end_date',
        'status',
        'course_type',
        'main_price',
        'price',
        'original_price',
        'discount',
        'discount_type',
        'admission_fee',
        'net_original_price',
        'discount_type_admission_fee',
        'discount_admission_fee',
        'net_admission_fee',
        'request_enrollment',
        'from_license',
        'students_count',
        'invoice_generated_at'
    ];

    public function students()
    {
        return $this->belongsTo(Student::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
   
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
  
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
    public function consultation_slot()
    {
        return $this->belongsTo(ConsultationSlot::class, 'consultation_slot_id');
    }
    
    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id');
    }
}
