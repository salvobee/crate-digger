<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'chart_id',
        'order',
        'song_id',
        'last_week_position',
        'song_position_peak'
    ];
    protected $casts = [
        'order' => 'integer',
        'last_week_position' => 'integer',
        'song_position_peak' => 'integer',
    ];

    public function chart(): BelongsTo
    {
        return $this->belongsTo(Chart::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
