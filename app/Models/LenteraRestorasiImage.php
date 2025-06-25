<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenteraRestorasiImage extends Model
{
    protected $fillable = ['title', 'image', 'description'];
    protected $table = 'lentera_restorasi_images';
}