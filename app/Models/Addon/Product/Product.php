<?php

namespace App\Models\Addon\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'type',
        'title',
        'slug',
        'product_category_id',
        'old_price',
        'discount_percentage',
        'current_price',
        'quantity',
        'shipping_charge',
        'average_review',
        'thumbnail',
        'main_file',
        'image_1',
        'image_2',
        'image_3',
        'image_4',
        'description',
        'shipping_return',
        'additional_information',
        'user_id',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
    ];

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
   
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
   
    public function tags()
    {
        return $this->hasMany(TagProduct::class);
    }

    public function getThumbnailPathAttribute()
    {
        if ($this->thumbnail) {
            return $this->thumbnail;
        } else {
            return 'uploads/default/book.jpg';
        }
    }
    public function getMainFilePathAttribute()
    {
        if ($this->main_file) {
            return $this->main_file;
        } else {
            return 'uploads/default/book.jpg';
        }
    }
    public function getImage1PathAttribute()
    {
        if ($this->image_1) {
            return $this->image_1;
        } else {
            return 'uploads/default/book.jpg';
        }
    }
    public function getImage2PathAttribute()
    {
        if ($this->image_2) {
            return $this->image_2;
        } else {
            return 'uploads/default/book.jpg';
        }
    }
   
    public function getImage3PathAttribute()
    {
        if ($this->image_3) {
            return $this->image_3;
        } else {
            return 'uploads/default/book.jpg';
        }
    }
    public function getImage4PathAttribute()
    {
        if ($this->image_4) {
            return $this->image_4;
        } else {
            return 'uploads/default/book.jpg';
        }
    }

    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->user_id =  auth()->id();
            $model->status =  auth()->user()->roll == USER_ROLE_ADMIN ? 1 : 0;
        });
    }

}
