<?php

namespace Tests\Feature\Actions;

use App\Actions\FetchInventoryPageAction;
use App\Jobs\UpdateReleaseDataJob;
use App\Models\Analysis;
use App\Models\Inventory;
use App\Services\DiscogsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Tests\TestCase;

class FetchInventoryPageActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_fetches_inventory_and_updates_database()
    {
        Bus::fake();
        // Mock del servizio DiscogsApiService
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $this->app->instance(DiscogsApiService::class, $discogsApiService);

        // Mock dei dati restituiti dall'API Discogs
        $inventoryData = [
            'listings' => [
                [
                    'id' => 1,
                    'release' => [
                        'id' => 101,
                        'artist' => 'Test Artist',
                        'title' => 'Test Title',
                        'label' => 'Test Label',
                        'catalog_number' => 'TEST123',
                        'year' => 2022,
                        'videos' => [],
                        'stats' => [
                            'community' => [
                                'in_wantlist' => 10,
                                'in_collection' => 20,
                            ]
                        ]
                    ],
                    'price' => [
                        'value' => 15.99,
                        'currency' => 'USD',
                    ],
                    'condition' => 'Mint',
                    'sleeve_condition' => 'Near Mint',
                    'comments' => 'Test Comment',
                    'ships_from' => 'USA',
                    'allow_offers' => true,
                    'posted' => now()->toISOString(),
                ]
            ]
        ];

        // Configurazione del mock
        $discogsApiService
            ->shouldReceive('fetchInventoryData')
            ->once()
            ->andReturn($inventoryData);

        // Creazione del modello Inventory
        $inventory = Inventory::factory()->create([
            'seller_username' => 'test_seller',
        ]);


        // Inizializza l'action
        $action = new FetchInventoryPageAction($discogsApiService);

        // Esegui l'action
        $action->execute($inventory, 1);

        // Assert che Release e Listing sono stati creati nel database
        $this->assertDatabaseHas('releases', [
            'discogs_id' => 101,
            'artist' => 'Test Artist',
            'title' => 'Test Title',
        ]);

        $this->assertDatabaseHas('listings', [
            'discogs_id' => 1,
            'inventory_id' => $inventory->id,
            'price_value' => 15.99,
        ]);


        Bus::assertDispatched(UpdateReleaseDataJob::class);
    }
}
