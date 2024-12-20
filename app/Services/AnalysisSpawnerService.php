<?php

namespace App\Services;

use App\Enums\AnalysisType;
use App\Jobs\FetchInventoryPageJob;
use App\Models\Analysis;
use App\Models\Inventory;
use Illuminate\Bus\Batch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Throwable;

class AnalysisSpawnerService
{
    public function __construct(
        private Analysis $analysis
    )
    {
    }

    public function spawn(): void
    {
        switch ($this->analysis->type) {
            case AnalysisType::FETCH_INVENTORY->value:
                $this->spawnFetchInventoryJobs();
                break;
            default:
                break;
        };
    }

    private function discogsService(): DiscogsApiService
    {
        return app(DiscogsApiService::class);
    }

    private function spawnFetchInventoryJobs()
    {
        $inventory = Inventory::find($this->analysis->resource_id);
        $inventory_data = $this->discogsService()->fetchInventoryData($inventory->seller_username);
        $pagination_data = $inventory_data['pagination'];
        $pages = $pagination_data['pages'];
        $listings_count = $pagination_data['items'];
        $inventory->update(['total_listings_count' => $listings_count]);

        // DISCOGS will block you to fetch over 100 pages for inventories, so getting 100 items per page
        // you can list only 10k records
        // but if you run two crawls ordering the records for the same field but inverting the order in the two
        // batches you can fetch the double of the records

        // Listing count is less or equal than 10k
        if ($listings_count <= 10000) {
            $this->analysis->update(['jobs' => $pages]);
            $jobs = Collection::times($pages)
                ->map(fn($page_number) => new FetchInventoryPageJob($inventory, $page_number))
                ->toArray();
        } else {

            // Listing count is over 20k

            $this->analysis->update(['jobs' => 200]);
            $desc_jobs = Collection::times(100)
                ->map(fn($page_number) => new FetchInventoryPageJob($inventory, $page_number, 'listed', 'desc'))
                ->toArray();
            $asc_jobs = Collection::times(100)
                ->map(fn($page_number) => new FetchInventoryPageJob($inventory, $page_number, 'listed', 'asc'))
                ->toArray();


            $jobs = array_merge($desc_jobs, $asc_jobs);
        }

        $batch = Bus::batch($jobs)
            ->before(function (Batch $batch) {
                // The batch has been created but no jobs have been added...
            })->progress(function (Batch $batch) {
                Analysis::query()->whereBatchId($batch->id)->first()->progress();
            })->then(function (Batch $batch) {
                // All jobs completed successfully...
                Analysis::query()->whereBatchId($batch->id)->first()->complete();
            })->catch(function (Batch $batch, Throwable $e) {
                // First batch job failure detected...
                Analysis::query()->whereBatchId($batch->id)->first()->fail();
            })->finally(function (Batch $batch) {
                // Batch is complete
                Analysis::query()->whereBatchId($batch->id)->first()->complete();
            })->name('Fetch ' . $inventory->seller_username . ' inventory')
            ->dispatch();

        $this->analysis->update(['batch_id' => $batch->id]);
    }
}
