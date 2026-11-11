<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenseDevice extends Model
{
   

     protected $fillable = [
        'license_id',
        'name',
        'k_sign',
        'registered_at',
        'vault_activated_at',
        'revoked_at',
        'revoke_reason',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'vault_activated_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];


     public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }


}
