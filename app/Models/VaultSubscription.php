<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaultSubscription extends Model
{
    protected $fillable = [
        'vault_user_id',
        'license_id',
        'plan_name',
        'amount',
        'currency',
        'billing_interval',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function vaultUser(): BelongsTo
    {
        return $this->belongsTo(VaultUser::class, 'vault_user_id');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VaultPayment::class, 'vault_subscription_id');
    }
}
