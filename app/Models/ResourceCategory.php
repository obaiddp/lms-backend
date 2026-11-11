<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_types',
        'description',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];


    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

}
