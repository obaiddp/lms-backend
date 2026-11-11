<?php

namespace App\Models\Addon\Product;

use Illuminate\Database\Eloquent\Model;

class TagProduct extends Model
{
    protected $fillable = [
        'product_id',
        'product_tag_id',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
   
    public function tag(){
        return $this->belongsTo(ProductTag::class);
    }
}
