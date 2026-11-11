<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartManagementWebsite extends Model
{
    use HasFactory;


    protected $table = 'cart_management_website';
     protected $fillable = [
        'cart_session_id',
        'item_id',
        'item_name',
        'main_price',
        'price',
        'cycle',
        'type',
        'sponsor_type',
        'custom_message',
         'error_trans_response',
         'faysal_ipn_payload',
         'faysal_hash_original',
         'faysal_hash_calculated',
         'faysal_order_id',
         'meezan_order_id',
         'donation_user_id',
         'source_site',
        'created_at',
        'updated_at'
     ];


      public function donation_user(): BelongsTo
    {
        return $this->belongsTo(DonationUser::class, 'donation_user_id');
    }


}



