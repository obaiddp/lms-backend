<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookedSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'time_slot_id',
        'user_id',
        'status'
    ];


    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
