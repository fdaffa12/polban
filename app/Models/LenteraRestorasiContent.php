<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenteraRestorasiContent extends Model
{
    protected $fillable = [
        'vision',
        'mission',
        'core_values_title',
        'core_values_desc'
    ];
}
