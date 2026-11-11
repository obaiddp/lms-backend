<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseVaultManifest extends Model
{
    protected $fillable = [
        'course_id',
        'payload',
        'is_ready',
        'manifest_version',
        'last_built_at',
        'last_error',
    ];

    protected $casts = [
        'payload' => 'array',
        'is_ready' => 'boolean',
        'last_built_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
