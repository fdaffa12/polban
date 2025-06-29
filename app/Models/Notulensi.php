<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notulensi extends Model
{
    protected $table = 'notulensi';

    protected $fillable = [
        'title',
        'file_path',
        'description',
        'department_id'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}