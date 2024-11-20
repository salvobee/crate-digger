<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Label extends Model
{
    /** @use HasFactory<\Database\Factories\LabelFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'country',
        'discogs_id'
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }
}
