<?php

namespace App\Models\Addon\Product;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_feature',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    public function getImagePathAttribute()
    {
        if ($this->image) {
            return $this->image;
        } else {
            return 'uploads/default/book.jpg';
        }
    }

    public function products(){
        return $this->hasMany(Product::class, 'product_category_id');
    }
}
