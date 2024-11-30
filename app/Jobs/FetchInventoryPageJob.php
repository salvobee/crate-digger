<?php

namespace App\Jobs;

use App\Actions\FetchInventoryPageAction;
use App\Models\Analysis;
use App\Models\Inventory;
use App\Models\Listing;
use App\Models\Release;
use App\Services\DiscogsApiService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class FetchInventoryPageJob implements ShouldQueue
{
    use Batchable, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Inventory $inventory,
        public int $pageNumber,
        public string $sortField = 'listed',
        public string $sortOrder = 'desc'
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(FetchInventoryPageAction $fetchInventoryPageAction): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $fetchInventoryPageAction
            ->setBatch($this->batch())
            ->execute($this->inventory, $this->pageNumber, $this->sortField, $this->sortOrder);


    }
}
