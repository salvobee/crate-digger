<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasUuids;

    public function articles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'taggable');
    }
}
