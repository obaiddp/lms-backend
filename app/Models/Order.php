<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $appends = ['deposit_slip_url'];
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
        'payment_id',
        'user_id',
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
        'is_monthly_cron'

    ];

    public function getDepositSlipUrlAttribute()
    {
        if ($this->deposit_slip) {
            return getVideoFile($this->deposit_slip);
        } else {
            return NULL;
        }
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function items()
    {
        return $this->hasMany(Order_item::class, 'order_id');
    }

    public function getAdminCommissionAttribute()
    {
        return Order_item::where('order_id', $this->id)->sum('admin_commission');
    }

    public function getTotalRevenueAttribute()
    {
        return $this->admin_commission + $this->platform_charge;
    }

    public function jj()
    {

    }

    public function activity()
    {
        return $this->morphMany('App\Models\UserPackageActivity', 'activityable');
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
