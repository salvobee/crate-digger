<?php

namespace App\Models;

use App\Services\AnalysisSpawnerService;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    /** @use HasFactory<\Database\Factories\AnalysisFactory> */
    use HasFactory, HasUuids;

    public function progress(): void
    {
        $this->status = 'processing';
        $this->processed = $this->processed + 1;
        $this->save();
    }

    public function fail(): void
    {
        $this->status = 'processing (with fails)';
        $this->failed = $this->failed + 1;
        $this->save();
    }

    public function complete()
    {
        $this->status = 'processed';
        $this->save();
    }

    public function spawn(): void
    {
        (new AnalysisSpawnerService($this))->spawn();
    }
}
