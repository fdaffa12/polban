<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormatAdministrasi extends Model
{
    protected $table = 'format_administrasi';

    protected $fillable = [
        'title',
        'file_path',
        'description'
    ];
}
