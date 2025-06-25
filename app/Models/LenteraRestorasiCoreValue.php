<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenteraRestorasiCoreValue extends Model
{
    protected $fillable = ['title', 'description', 'type', 'image'];
    protected $table = 'lentera_restorasi_core_values';
}
