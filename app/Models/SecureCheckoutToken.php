<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SecureCheckoutToken extends Model
{
    protected $fillable = [
        'token', 'user_id',   'cart_ids', 'payment_method','created_by_type', 'used', 'expires_at'
    ];

    protected $casts = [
        'cart_ids' => 'array',   // Automatically JSON encode/decode
        'used' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public static function generate($userId, $cartIds, $paymentMethod,$createdByType, $expiresMinutes = 20)
    {
        $token = Str::random(64);

        return static::create([
            'token' => $token,
            'user_id' => $userId,
            'cart_ids' => is_array($cartIds) ? $cartIds : [$cartIds], // Always store as array
            'payment_method' => $paymentMethod,
            'created_by_type' => $createdByType,
            'used' => false,
            'expires_at' => now()->addMinutes($expiresMinutes),
        ]);
    }

     // Optional: Helper to get first cart ID (e.g., for legacy cases)
    public function getFirstCartIdAttribute()
    {
        return $this->cart_ids ? (int)collect($this->cart_ids)->first() : null;
    }
    
}