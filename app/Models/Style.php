<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Style extends Model
{
    /** @use HasFactory<\Database\Factories\StyleFactory> */
    use HasFactory, HasUuids;

    public function releases(): BelongsToMany
    {
        return $this->belongsToMany(Release::class, 'release_style', 'style_id', 'release_id');
    }
}
