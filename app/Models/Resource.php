<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $casts = [
        'vault_download_attempted_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'type',
        'category_id',
        'file_name',
        'file_extension',
        'sort_number',
        'youtube_url_path',
        'youtube_video_duration',
        'youtube_video_duration_second',
        'text',
        'vault_video_path',
        'vault_download_status',
        'vault_download_error',
        'vault_download_attempted_at',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];


    public function course_resources()
    {
        return $this->hasMany(CourseResource::class);
    }

    public function course_lectures()
    {
        return $this->hasMany(CourseLecture::class);
    }

    public function category()
    {
        return $this->belongsTo(ResourceCategory::class);
    }

}
