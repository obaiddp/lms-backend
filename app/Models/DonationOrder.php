<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DonationOrder extends Model
{
    use HasFactory;

    protected $table = 'donation_orders';
    protected $primaryKey = 'id';


      protected $fillable = [
        'payment_currency',
        'current_currency',
        'order_number',
        'sub_total',
        'discount',
        'shipping_cost',
        'tax',
        'platform_charge',
        'grand_total',
        'payment_method',
        'customer_comment',
        'error_msg',
        'cart_id',
        'month',
        'cart_id',
        'year',
        'meezan_transaction_id',
        'meezan_payment_source',
        'meezan_order_number',
        'meezan_payment_date',
        'meezan_trans_response',
        'meezan_recurrence_id',
        'payment_id',
        'donation_user_id',
        'cart_session_id',
        'cart_type',
        'request_enrollment',
        'faysal_transaction_id',
        'faysal_payment_source',
        'faysal_payment_date',
        'meezan_order_id',
        'faysal_order_id',
        'faysal_ipn_payload',
        'faysal_hash_original',
        'faysal_hash_calculated',
        'trial_id',
        'is_monthly_cron',
        'recurring_schedule_id',
        'recurring_period',
        'source_site',

    ];



     public function donation_user()
    {
        return $this->belongsTo(DonationUser::class, 'donation_user_id');
    }


       public function donation_items()
    {
        return $this->hasMany(DonationOrder_item::class, 'order_id');
    }

    public function receipt_email_logs()
    {
        return $this->hasMany(DonationReceiptEmailLog::class, 'donation_order_id');
    }

      protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->uuid =  Str::uuid()->toString();
            $model->created_by =  auth()->id();
        });
    }

    
}
