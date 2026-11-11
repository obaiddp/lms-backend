<?php

namespace App\Models;

use App\Models\Addon\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Order_item extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'order_id',
        'receiver_info',
        'course_id',
        'product_id',
        'unit_price',
        'type',
        'created_by_type',
        'cart_type',
        'cart_id',
        'request_enrollment',
        'trial_id',
        'delivery_status',
        'original_price',
        'discount',
        'admission_fee',
        'discount_type',
        'net_original_price',
        'discount_type_admission_fee',
        'discount_admission_fee',
        'net_admission_fee',
        'is_monthly_cron',
        'month',
        'year',
        'students_count',
        'is_donation'
    ];

    protected $casts = [
        'consultation_details' => 'object',
        'receiver_info' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


     public function cart()
    {
        return $this->belongsTo(CartManagement::class, 'cart_id');
    }


    public function bookingHistory()
    {
        return $this->hasOne(BookingHistory::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function bundle()
    {
        return $this->belongsTo(Bundle::class, 'bundle_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function owner_user()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function consultationSlot()
    {
        return $this->belongsTo(ConsultationSlot::class, 'consultation_slot_id');
    }

}
