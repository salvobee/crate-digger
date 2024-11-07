<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

use Illuminate\Database\Eloquent\Builder;


class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    public $casts = [
        'meta' => SchemalessAttributes::class,
    ];

    public function scopeWithMeta(): Builder
    {
        return $this->meta->modelScope();
    }

}