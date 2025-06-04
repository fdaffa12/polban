<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'department_id',
        'event_name',
        'event_detail',
        'fee_type',
        'fee_amount',
        'event_flyer',
        'event_gallery',
        'event_doc',
        'status'
    ];

    protected $casts = [
        'event_gallery' => 'array'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function dates()
    {
        return $this->hasMany(EventDate::class);
    }
}
