<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDate extends Model
{
    protected $fillable = [
        'event_id',
        'event_date',
        'event_time'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
