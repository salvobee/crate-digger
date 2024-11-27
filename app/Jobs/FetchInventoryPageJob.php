<?php

namespace App\Jobs;

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
        public int $pageNumber
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(DiscogsApiService $discogsApiService): void
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...

            return;
        }

        $inventory_data = $discogsApiService->fetchInventoryData($this->inventory->seller_username, $this->pageNumber);

        DB::beginTransaction();

        try {
            collect($inventory_data['listings'])->each(function (array $listing_data) {
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
                        'inventory_id' => $this->inventory->id, // Assuming inventory_id matches the seller's id
                        'release_id' => $release->id,
                        'price_value' => $listing_data['price']['value'],
                        'price_currency' => $listing_data['price']['currency'],
                        'media_condition' => $listing_data['condition'],
                        'sleeve_condition' => $listing_data['sleeve_condition'],
                        'comments' => $listing_data['comments'],
                        'ships_from' => $listing_data['ships_from'],
                        'allow_offers' => $listing_data['allow_offers'],
                    ]);
            });
            DB::commit();
        } catch (\Exception $e)
        {
            // Rollback della transazione in caso di errore
            DB::rollBack();
            throw $e;
        }

    }
}
