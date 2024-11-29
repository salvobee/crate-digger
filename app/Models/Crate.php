<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Crate extends Model
{
    /** @use HasFactory<\Database\Factories\CrateFactory> */
    use HasUuids, HasFactory;

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
