<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationWebhookLog extends Model
{
    use HasFactory;

    protected $table = 'donation_webhook_logs';

    protected $fillable = [
        'donation_order_id',
        'event',
        'url',
        'payload',
        'request_headers',
        'response_status',
        'response_body',
        'response_headers',
        'duration_ms',
        'error_message',
    ];

    protected $casts = [
        'request_headers' => 'array',
        'response_headers' => 'array',
    ];

    public function donation_order()
    {
        return $this->belongsTo(DonationOrder::class, 'donation_order_id');
    }
}
