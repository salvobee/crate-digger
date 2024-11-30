<?php

namespace Tests\Feature\Services;

use App\Enums\AnalysisType;
use App\Jobs\FetchInventoryPageJob;
use App\Models\Analysis;
use App\Models\Inventory;
use App\Services\AnalysisSpawnerService;
use App\Services\DiscogsApiService;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Tests\TestCase;

class AnalysisSpawnerServiceTest extends TestCase
{
    public function test_spawn_creates_fetch_inventory_jobs_within_limit()
    {
        // Mock del DiscogsApiService
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $this->app->instance(DiscogsApiService::class, $discogsApiService);

        // Mock dei dati restituiti dall'API Discogs
        $inventoryData = [
            'pagination' => [
                'pages' => 5,
                'items' => 500,
            ],
        ];
        $discogsApiService
            ->shouldReceive('fetchInventoryData')
            ->once()
            ->andReturn($inventoryData);

        // Creazione del modello Inventory
        $inventory = Inventory::factory()->create(['seller_username' => 'test_seller']);

        // Creazione dell'analisi
        $analysis = Analysis::factory()->create([
            'type' => AnalysisType::FETCH_INVENTORY->value,
            'resource_id' => $inventory->id,
        ]);

        // Mock di Batch e Bus
        Bus::fake();

        // Inizializza il servizio
        $service = new AnalysisSpawnerService($analysis);

        // Esegui il metodo spawn
        $service->spawn();

        // Assert che i job FetchInventoryPageJob siano stati creati
        Bus::assertBatched(function (PendingBatch $batch) use ($inventory) {
            return $batch->name == ('Fetch ' . $inventory->seller_username . ' inventory') &&
                $batch->jobs->count() === 5;
        });

        // Verifica aggiornamenti su Inventory e Analysis
        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'total_listings_count' => 500,
        ]);

        $this->assertDatabaseHas('analyses', [
            'id' => $analysis->id,
            'jobs' => 5,
        ]);
    }
    public function test_spawn_creates_fetch_inventory_jobs_between_10k_and_20k()
    {
        // Mock del DiscogsApiService
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $this->app->instance(DiscogsApiService::class, $discogsApiService);

        // Mock dei dati restituiti dall'API Discogs per un inventario con 15k articoli
        $inventoryData = [
            'pagination' => [
                'pages' => 150,
                'items' => 15000,
            ],
        ];
        $discogsApiService
            ->shouldReceive('fetchInventoryData')
            ->once()
            ->andReturn($inventoryData);

        // Creazione del modello Inventory
        $inventory = Inventory::factory()->create(['seller_username' => 'test_seller']);

        // Creazione dell'analisi
        $analysis = Analysis::factory()->create([
            'type' => AnalysisType::FETCH_INVENTORY->value,
            'resource_id' => $inventory->id,
        ]);

        // Mock di Bus
        Bus::fake();

        // Inizializza il servizio
        $service = new AnalysisSpawnerService($analysis);

        // Esegui il metodo spawn
        $service->spawn();

        // Assert che i job FetchInventoryPageJob siano stati divisi correttamente
        Bus::assertBatched(function (PendingBatch $batch) use ($inventory) {
            return $batch->name == ('Fetch ' . $inventory->seller_username . ' inventory') &&
                $batch->jobs->count() === 150;
        });

        // Verifica che l'analisi abbia aggiornato correttamente il numero di job
        $this->assertDatabaseHas('analyses', [
            'id' => $analysis->id,
            'jobs' => 150, // 150 pagine totali distribuite tra ASC e DESC
        ]);

        // Verifica che l'inventario abbia aggiornato il conteggio totale degli articoli
        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'total_listings_count' => 15000,
        ]);
    }

    public function test_spawn_creates_fetch_inventory_jobs_over_limit()
    {
        // Mock del DiscogsApiService
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $this->app->instance(DiscogsApiService::class, $discogsApiService);

        // Mock dei dati restituiti dall'API Discogs per un inventario con oltre 10k articoli
        $inventoryData = [
            'pagination' => [
                'pages' => 200,
                'items' => 20000,
            ],
        ];
        $discogsApiService
            ->shouldReceive('fetchInventoryData')
            ->once()
            ->andReturn($inventoryData);

        // Creazione del modello Inventory
        $inventory = Inventory::factory()->create(['seller_username' => 'test_seller']);

        // Creazione dell'analisi
        $analysis = Analysis::factory()->create([
            'type' => AnalysisType::FETCH_INVENTORY->value,
            'resource_id' => $inventory->id,
        ]);

        // Mock di Batch e Bus
        Bus::fake();

        // Inizializza il servizio
        $service = new AnalysisSpawnerService($analysis);

        // Esegui il metodo spawn
        $service->spawn();

        // Assert che i job FetchInventoryPageJob siano stati creati correttamente con ordini ASC e DESC
        Bus::assertBatched(function (PendingBatch $batch) use ($inventory) {
            return $batch->name == ('Fetch ' . $inventory->seller_username . ' inventory') &&
                $batch->jobs->count() === 200;
        });

        // Verifica aggiornamenti su Inventory e Analysis
        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'total_listings_count' => 20000,
        ]);

        $this->assertDatabaseHas('analyses', [
            'id' => $analysis->id,
            'jobs' => 200,
        ]);
    }
}
