<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultPayment extends Model
{
    protected $fillable = [
        'vault_user_id',
        'vault_subscription_id',
        'license_id',
        'amount',
        'currency',
        'paid_at',
        'method',
        'reference_number',
        'receipt_path',
        'period_start',
        'period_end',
        'status',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    public function vaultUser(): BelongsTo
    {
        return $this->belongsTo(VaultUser::class, 'vault_user_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(VaultSubscription::class, 'vault_subscription_id');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
