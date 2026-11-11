<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultOfflinePack extends Model
{
    protected $fillable = [
        'license_id',
        'pack_id',
        'validity_mode',
        'duration_seconds',
        'status',
        'storage_path',
        'byte_size',
        'error',
        'created_by',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'byte_size' => 'integer',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
