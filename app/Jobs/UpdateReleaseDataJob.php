<?php

namespace App\Jobs;

use App\Actions\UpdateReleaseDataAction;
use App\Models\Release;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateReleaseDataJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Release $release
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(UpdateReleaseDataAction $action): void
    {
        $action->execute($this->release);
    }
}
