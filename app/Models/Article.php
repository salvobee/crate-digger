<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasUuids, HasFactory;

    protected $casts = [
        'tags' => 'array',
        'categories' => 'array',
        'published_at' => 'datetime'
    ];
}
