<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenteraRestorasiMission extends Model
{
    protected $fillable = ['mission', 'type', 'image'];
    protected $table = 'lentera_restorasi_missions';
}
