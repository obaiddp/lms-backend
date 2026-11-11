<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultPaymentLink extends Model
{
    protected $fillable = [
        'token',
        'vault_payment_id',
        'license_id',
        'vault_user_id',
        'expires_at',
        'status',
        'gateway',
        'gateway_reference',
        'meezan_transaction_id',
        'meezan_order_id',
        'include_offline_pack',
        'offline_pack_id',
        'download_token',
        'download_expires_at',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'download_expires_at' => 'datetime',
        'include_offline_pack' => 'boolean',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(VaultPayment::class, 'vault_payment_id');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function vaultUser(): BelongsTo
    {
        return $this->belongsTo(VaultUser::class, 'vault_user_id');
    }

    public function offlinePack(): BelongsTo
    {
        return $this->belongsTo(VaultOfflinePack::class, 'offline_pack_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
