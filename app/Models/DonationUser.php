<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationUser extends Model
{
    use HasFactory;

    protected $fillable = [
            'name',
            'email',
            'email_verified_at',
            'company_name',
            'country',
            'address',
            'area',
            'city',
            'postal_code',
            'phone_number',
            'created_at',
            'updated_at'
    ];


     public function cart_websites()
    {
        return $this->hasMany(CartManagementWebsite::class, 'donation_user_id', 'id');
    }


     public function donation_orders()
    {
        return $this->hasMany(DonationOrder::class, 'donation_user_id', 'id');
    }


       public function donation_order_items()
    {
        return $this->hasMany(DonationOrder_item::class, 'donation_user_id', 'id');
    }


}
