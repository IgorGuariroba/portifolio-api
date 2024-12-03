<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'technologies',
        'link',
        'image_path',
    ];

    protected $casts = [
        'technologies' => 'array',
    ];
}
