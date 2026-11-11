<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationReceiptEmailLog extends Model
{
    use HasFactory;

    protected $table = 'donation_receipt_email_logs';

    protected $fillable = [
        'donation_order_id',
        'recipient_email',
        'status',
        'sent_at',
        'error_message',
        'trigger',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function donation_order()
    {
        return $this->belongsTo(DonationOrder::class, 'donation_order_id');
    }
}
