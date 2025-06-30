<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapotHimpunan extends Model
{
    protected $fillable = [
        'user_id',
        'jabatan',
        'periode_awal',
        'periode_akhir',
        'description',
        'file_path',
        'department_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
