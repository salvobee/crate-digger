<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chart extends Model
{
    /** @use HasFactory<\Database\Factories\ChartFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'previous_chart_id',
        'next_chart_id',
        'valid_from',
        'valid_to',
        'notes'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function previousChart(): BelongsTo
    {
        return $this->belongsTo(Chart::class, 'previous_chart_id');
    }

    public function nextChart(): BelongsTo
    {
        return $this->belongsTo(Chart::class, 'next_chart_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}
