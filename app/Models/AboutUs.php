<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    protected $table = 'about_us';

    protected $fillable = [
        'au_title',
        'au_desc',
        'au_image',
        'au_multiple_image',
        'au_values',
        'history'
    ];

    protected $casts = [
        'au_multiple_image' => 'array',
        'au_values' => 'string',
        'history' => 'string'
    ];

    // Add accessor to ensure au_multiple_image always returns array of objects
    protected function getAuMultipleImageAttribute($value)
    {
        $images = json_decode($value, true) ?: [];
        return array_map(function ($item) {
            if (is_string($item)) {
                return [
                    'path' => $item,
                    'title' => ''
                ];
            }
            return $item;
        }, $images);
    }
}