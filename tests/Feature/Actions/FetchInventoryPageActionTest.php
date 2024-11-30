<?php

namespace Tests\Feature\Actions;

use App\Actions\FetchInventoryPageAction;
use App\Models\Analysis;
use App\Models\Inventory;
use App\Services\DiscogsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class FetchInventoryPageActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_fetches_inventory_and_updates_database()
    {
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

        // Simula il batch per i job
        $batch = Mockery::mock(\Illuminate\Bus\Batch::class);
        $batch->id = 1;
        $batch->shouldReceive('add')->andReturnNull();

        // Simula l'analisi associata al batch
        $analysis = Analysis::factory()->create([
            'batch_id' => $batch->id,
            'jobs' => 0,
        ]);

        // Inizializza l'action
        $action = new FetchInventoryPageAction($discogsApiService);
        $action->setBatch($batch);

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

        // Assert che il batch abbia aggiornato l'analisi
        $this->assertDatabaseHas('analyses', [
            'batch_id' => $batch->id,
            'jobs' => 1,
        ]);
    }
}
