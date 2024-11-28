<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Release extends Model
{
    /** @use HasFactory<\Database\Factories\ReleaseFactory> */
    use HasFactory, HasUuids;

    protected $casts = [
        'videos' => 'array'
    ];

    protected $with = [
        'formats',
        'genres',
        'styles',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'release_genre', 'release_id', 'genre_id');
    }

    public function styles(): BelongsToMany
    {
        return $this->belongsToMany(Style::class, 'release_style', 'release_id', 'style_id');
    }

    public function formats(): BelongsToMany
    {
        return $this->belongsToMany(Format::class, 'release_format', 'release_id', 'format_id');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

}
