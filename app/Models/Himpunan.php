<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Himpunan extends Model
{
    protected $table = 'himpunan';

    protected $fillable = [
        'name',
        'description',
        'logo',
        'yt_link'
    ];
}
