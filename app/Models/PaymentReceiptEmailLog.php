<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceiptEmailLog extends Model
{
    protected $table = 'payment_receipt_email_logs';

    protected $fillable = [
        'order_id',
        'admin_id',
        'mode',
        'recipients',
        'status',
        'error',
    ];

    protected $casts = [
        'recipients' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
