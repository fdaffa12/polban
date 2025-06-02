<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenteraRestorasiCoreValue extends Model
{
    protected $fillable = ['title', 'description'];
    protected $table = 'lentera_restorasi_core_values';
}
