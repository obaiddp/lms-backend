<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeezanIpnNotification extends Model
{
    protected $table = 'meezan_ipn_notifications';

    // allow mass‐assignment of the payload
    protected $fillable = [
        'payload',
    ];

    // cast the JSON column to array automatically
    protected $casts = [
        'payload' => 'array',
    ];
}