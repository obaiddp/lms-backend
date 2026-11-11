<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class License extends Model
{
    protected $fillable = [
        'user_id',
        'vault_user_id',
        'key',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at'];

    /** @deprecated Legacy academy student link; prefer vaultUser */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vaultUser(): BelongsTo
    {
        return $this->belongsTo(VaultUser::class, 'vault_user_id');
    }

    public function holderName(): string
    {
        if ($this->relationLoaded('vaultUser') && $this->vaultUser) {
            return (string) $this->vaultUser->name;
        }
        if ($this->vault_user_id) {
            return (string) (VaultUser::find($this->vault_user_id)?->name ?? 'User');
        }
        if ($this->relationLoaded('user') && $this->user) {
            return (string) $this->user->name;
        }
        if ($this->user_id) {
            return (string) (User::find($this->user_id)?->name ?? 'User');
        }

        return 'User';
    }

    public function holderId(): int
    {
        if ($this->vault_user_id) {
            return (int) $this->vault_user_id;
        }

        return (int) ($this->user_id ?? 0);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'license_courses')->withTimestamps();
    }

    public function licenseDevices(): HasMany
    {
        return $this->hasMany(LicenseDevice::class);
    }

    public function licenseCourses(): HasMany
    {
        return $this->hasMany(LicenseCourse::class);
    }

    public function deviceUsageEvents(): HasMany
    {
        return $this->hasMany(LicenseDeviceUsageEvent::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(VaultSubscription::class, 'license_id')
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(VaultSubscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VaultPayment::class);
    }

    public function offlinePacks(): HasMany
    {
        return $this->hasMany(VaultOfflinePack::class);
    }
}
