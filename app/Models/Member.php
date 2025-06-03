<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'image',
        'job_type',
        'job_titles',
        'position'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
