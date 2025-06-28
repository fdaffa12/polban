<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuPanduan extends Model
{
    protected $table = 'buku_panduan';

    protected $fillable = [
        'title',
        'file_path',
        'description'
    ];
}
