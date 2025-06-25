<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenteraRestorasiVision extends Model
{
    protected $fillable = ['vision', 'type', 'image'];
    protected $table = 'lentera_restorasi_visions';
}
