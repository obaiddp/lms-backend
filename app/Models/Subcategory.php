<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subcategory extends Model
{
    use HasFactory;

    protected $table = 'subcategories';
    protected $appends = ['cover_image_url'];
    protected $primaryKey = 'id';
    protected $fillable = [
        'category_id',
        'sort_order',
        'name',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'cover_image',
    ];

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset($this->cover_image);
        } else {
            return asset('uploads/default/no-image-found.png');
        }
    }


    public function courses()
    {
        return $this->hasMany(Course::class, 'subcategory_id');
    }

    public function activeCourses()
    {
        return $this->hasMany(Course::class, 'subcategory_id')->where('status', 1);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getCoverImagePathAttribute()
    {
        if ($this->cover_image)
        {
            return $this->cover_image;
        } else {
            return 'uploads/default/no-cover-image-found.png';
        }
    }
    
    protected static function boot()
    {
        parent::boot();
        self::creating(function($model){
            $model->uuid =  Str::uuid()->toString();
        });
    }

}
