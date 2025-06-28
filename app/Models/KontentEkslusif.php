<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontentEkslusif extends Model
{
    protected $table = 'konten_ekslusif';

    protected $fillable = [
        'title',
        'file_path',
        'description'
    ];
}
