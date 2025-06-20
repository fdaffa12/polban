<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'nama',
        'logo',
        'email',
        'phone',
        'address',
        'social_media'
    ];

    protected $casts = [
        'social_media' => 'array'
    ];
}