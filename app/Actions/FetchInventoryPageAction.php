<?php

namespace App\Actions;

use App\Jobs\UpdateReleaseDataJob;
use App\Models\Analysis;
use App\Models\Inventory;
use App\Models\Listing;
use App\Models\Release;
use App\Services\DiscogsApiService;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\DB;

class FetchInventoryPageAction
{


    public function __construct(
        protected DiscogsApiService $discogsApiService
    )
    {
    }

    protected Batch $batch;
    public function execute(Inventory $inventory, int $pageNumber, string $sortField = null, string $sortOrder = null)
    {
        $inventory_data = $this->discogsApiService
            ->fetchInventoryData(
                $inventory->seller_username,
                $pageNumber,
                $sortField,
                $sortOrder
            );

        DB::beginTransaction();

        try {
            collect($inventory_data['listings'])->each(function (array $listing_data) use ($inventory) {
                $release = Release::updateOrCreate(
                    ['discogs_id' => $listing_data['release']['id']],
                    [
                        'artist' => $listing_data['release']['artist'],
                        'title' => $listing_data['release']['title'],
                        'label' => $listing_data['release']['label'],
                        'catalog_number' => $listing_data['release']['catalog_number'],
                        'year' => $listing_data['release']['year'],
                        'videos' => $listing_data['release']['videos'] ?? [],
                        'want' => $listing_data['release']['stats']['community']['in_wantlist'] ?? null,
                        'have' => $listing_data['release']['stats']['community']['in_collection'] ?? null,
                    ]
                );

                // Creare la listing
                Listing::updateOrCreate(
                    [
                        'discogs_id' => $listing_data['id'],
                    ],
                    [
                        'inventory_id' => $inventory->id, // Assuming inventory_id matches the seller's id
                        'release_id' => $release->id,
                        'price_value' => $listing_data['price']['value'],
                        'price_currency' => $listing_data['price']['currency'],
                        'media_condition' => $listing_data['condition'],
                        'sleeve_condition' => $listing_data['sleeve_condition'],
                        'comments' => $listing_data['comments'],
                        'ships_from' => $listing_data['ships_from'],
                        'allow_offers' => $listing_data['allow_offers'],
                        'listed_at' => $listing_data['posted']
                    ]);
//                if (isset($this->batch)) {
//                    $this->batch->add(new UpdateReleaseDataJob($release));
//                    $analysis = Analysis::whereBatchId($this->batch->id)->first();
//                    $analysis->jobs++;
//                    $analysis->save();
//                }
                UpdateReleaseDataJob::dispatch($release);
            });
            DB::commit();
        } catch (\Exception $e)
        {
            // Rollback della transazione in caso di errore
            DB::rollBack();
            throw $e;
        }
    }

    public function setBatch(Batch $batch): FetchInventoryPageAction
    {
        $this->batch = $batch;
        return $this;
    }

    public function getBatch(): Batch
    {
        return $this->batch;
    }
}
