<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Format extends Model
{
    /** @use HasFactory<\Database\Factories\FormatFactory> */
    use HasFactory, HasUuids;

    public function releases(): BelongsToMany
    {
        return $this->belongsToMany(Release::class, 'release_format', 'format_id', 'release_id');
    }
}
