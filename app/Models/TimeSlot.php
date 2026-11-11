<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'is_active'
    ];

    protected $attributes = [
        'is_active' => true
    ];
    
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];


    public function bookings()
    {
        return $this->hasMany(BookedSlot::class);
    }

    public function isAvailable()
    {
        return $this->is_active;
    }

    public function formattedTime()
    {
        return $this->start_time->format('h:i A') . ' - ' . $this->end_time->format('h:i A');
    }

}
