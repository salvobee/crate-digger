<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

class Record extends Model
{
    /** @use HasFactory<\Database\Factories\RecordFactory> */
    use HasFactory, HasUuids;

    public $casts = [
        'artists' => 'array',
        'meta' => SchemalessAttributes::class,
    ];

    protected $appends = [
        'display_title',
    ];

    public function scopeWithMeta(): Builder
    {
        return $this->meta->modelScope();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'record_genre', 'record_id', 'genre_id');
    }

    public function styles(): BelongsToMany
    {
        return $this->belongsToMany(Style::class, 'record_style', 'record_id', 'style_id');
    }

    public function getDisplayTitleAttribute(): string
    {
        $artists = collect($this->artists)
            ->map(fn ($artist) => $artist['name'])
            ->implode(', ');

        return "$artists - $this->title";
    }
}
