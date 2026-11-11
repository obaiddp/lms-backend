<?php

namespace App\Models;

use App\Models\Addon\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartManagement extends Model
{
    use HasFactory;

    protected $casts = [
        'receiver_info' => 'array',
        'bundle_course_ids' => 'array',
        'consultation_details' => 'object'
    ];

    protected $fillable = [
        'error_trans_response', 
        'faysal_ipn_payload',
        'faysal_hash_original',
        'faysal_hash_calculated',
        'request_enrollment',
        'user_id',
        'coupon_id',
        'is_generated',
        'trial_id',
        'is_donation',
        'course_id',
        'original_price',
        'discount_type',
        'discount',
        'admission_fee',
        'students_count',
        'net_original_price',
        'discount_type_admission_fee',
        'discount_admission_fee',
        'net_admission_fee',
        'invoice_pdf_path',
        'is_monthly_cron',
        'is_cef_website_donation',
        'cart_session_id',
        'item_name',
        'cycle',
        'type',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

      public function order_item()
    {
        return $this->hasOne(Order_item::class);
    }


    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class, 'bundle_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function consultationSlot(): BelongsTo
    {
        return $this->belongsTo(ConsultationSlot::class, 'consultation_slot_id');
    }
}
