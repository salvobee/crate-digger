<?php

namespace App\Jobs;

use App\Actions\StoreMasterRecordAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchMasterDataJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $masterId
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(StoreMasterRecordAction $action): void
    {
        $action->execute($this->masterId);
    }
}
