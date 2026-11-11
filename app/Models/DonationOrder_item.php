<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationOrder_item extends Model
{
    use HasFactory;

     protected $table = 'donation_order_items';
    protected $primaryKey = 'id';

     protected $fillable = [
        'quantity',
        'cycle',
        'type',
         'sponsor_type',
        'custom_message',
        'error_trans_response',
        
        'order_id',
        'receiver_info',
        'cart_session_id',
        'donation_user_id',
        'item_id',
        'item_name',
        'course_id',
        'product_id',
        'unit_price',
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


     public function donation_order()
    {
        return $this->belongsTo(DonationOrder::class, 'order_id');
    }


       public function donation_user()
    {
        return $this->belongsTo(DonationUser::class, 'donation_user_id');
    }

     public function cart()
    {
        return $this->belongsTo(CartManagementWebsite::class, 'cart_id');
    }



}
