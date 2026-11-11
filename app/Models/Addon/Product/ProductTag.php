<?php

namespace App\Models\Addon\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function product(){
        return $this->hasMany(TagProduct::class, 'product_tag_id');
    }
}
