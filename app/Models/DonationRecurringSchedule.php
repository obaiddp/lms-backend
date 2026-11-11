<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationRecurringSchedule extends Model
{
    use HasFactory;

    protected $table = 'donation_recurring_schedules';

    protected $fillable = [
        'donation_user_id',
        'order_id',
        'order_item_id',
        'cycle',
        'amount',
        'currency',
        'next_due_date',
        'status',
        'last_charged_at',
        'gateway_recurrence_id',
    ];

    protected $casts = [
        'next_due_date' => 'date',
        'last_charged_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function donation_user()
    {
        return $this->belongsTo(DonationUser::class, 'donation_user_id');
    }

    public function donation_order()
    {
        return $this->belongsTo(DonationOrder::class, 'order_id');
    }

    public function donation_order_item()
    {
        return $this->belongsTo(DonationOrder_item::class, 'order_item_id');
    }
}
