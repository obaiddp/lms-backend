<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaultUser extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'notes',
    ];

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'vault_user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(VaultSubscription::class, 'vault_user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VaultPayment::class, 'vault_user_id');
    }
}
