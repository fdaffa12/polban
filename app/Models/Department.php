<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['dept_name', 'image'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
