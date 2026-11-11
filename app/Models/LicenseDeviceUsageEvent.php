<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseDeviceUsageEvent extends Model
{
    protected $fillable = [
        'license_id',
        'license_device_id',
        'device_key',
        'event',
        'detail',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function licenseDevice(): BelongsTo
    {
        return $this->belongsTo(LicenseDevice::class);
    }
}
