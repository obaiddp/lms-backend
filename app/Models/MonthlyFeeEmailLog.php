<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyFeeEmailLog extends Model
{
    protected $table = 'monthly_fee_email_logs';

    protected $fillable = [
        'cart_id',
        'admin_id',
        'mode',
        'recipients',
        'status',
        'error',
    ];

    protected $casts = [
        'recipients' => 'array',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(CartManagement::class, 'cart_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
