<?php

namespace Tests\Feature\Inventories;

use App\Models\Inventory;
use App\Models\User;
use App\Services\DiscogsApiService;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_inventory_list()
    {
        $user = User::factory()->create();
        $inventories = Inventory::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('inventories.index'));

        $response->assertInertia(fn(AssertableInertia $inertia) => $inertia
            ->component('Inventories/Index')
            ->has('inventories', 3)
        );
    }

    public function test_it_creates_inventory()
    {
        Bus::fake();
        $user = User::factory()->create();
        $discogsApiServiceMock = $this->mock(DiscogsApiService::class);
        $discogsApiServiceMock->shouldReceive('fetchInventoryData')
            ->once()
            ->with('some-username')
            ->andReturn([
                "pagination" => [
                    "page" => 1,
                    "pages" => 56,
                    "per_page" => 100,
                    "items" => 5521,
                    "urls" => [
                        "last" => "https://api.discogs.com/users/some-username/inventory?page=56&per_page=100",
                        "next" => "https://api.discogs.com/users/some-username/inventory?page=2&per_page=100"
                    ]
                ],
                'listings' => [
                    [
                        'seller' => [
                            "id" => 4991262,
                            "username" => "some-username",
                            "avatar_url" => "https://i.discgs.com/some-username.jpeg",
                            "stats" => [
                                "rating" => "98.4",
                                "stars" => 4.5,
                                "total" => 512
                            ],
                            "min_order_total" => 9.0,
                            "html_url" => "https://www.discogs.com/users/some-username",
                            "uid" => 4991262,
                            "url" => "https://api.discogs.com/users/some-username",
                            "payment" => "PayPal",
                            "shipping" => "",
                            "resource_url" => "https://api.discogs.com/users/some-username"
                        ],
                    ],
                ]]);

        $inventoryData = ['username' => 'some-username'];

        $response = $this->actingAs($user)
            ->post(route('inventories.store'), $inventoryData);

        $response->assertRedirect();

        $this->assertDatabaseHas('inventories', [
            'user_id' => $user->id,
            'seller_username' => 'some-username',
            'seller_id' => "4991262",
            'html_url' => "https://www.discogs.com/users/some-username",
            'avatar_url' => "https://i.discgs.com/some-username.jpeg",
            'rating' => 98.4,
            'stars' => 4.5,
            'total_feedbacks' => 512,
            'min_order_total' => 9,
            'total_listings_count' => 5521,
        ]);

        $response->assertRedirect(route('inventories.index'));
    }

    public function test_it_creates_inventory_and_dispatches_batch()
    {
        $user = User::factory()->create();

        // Mock del servizio API
        $this->mock(DiscogsApiService::class, function ($mock) {
            $mock->shouldReceive('fetchInventoryData')
                ->times(2)
                ->with('some-username')
                ->andReturn([
                    'pagination' => ['items' => 5521, 'pages' => 56],
                    'listings' => [
                        [
                            'seller' => [
                                'id' => 4991262,
                                'username' => 'some-username',
                                'avatar_url' => 'https://i.discgs.com/some-username.jpeg',
                                'stats' => ['rating' => '98.4', 'stars' => 4.5, 'total' => 512],
                                'html_url' => 'https://www.discogs.com/users/some-username',
                                'min_order_total' => 9.0,
                            ],
                        ],
                    ],
                ]);
        });

        // Mock del batch per asserire il dispatch
        Bus::fake();

        // Dati di input
        $inventoryData = ['username' => 'some-username', 'fetch_inventory' => true];

        // Effettua la richiesta
        $response = $this->actingAs($user)
            ->post(route('inventories.store'), $inventoryData);

        // Verifica la redirezione
        $response->assertRedirect(route('inventories.index'));

        // Asserisce che l'inventario è stato creato
        $this->assertDatabaseHas('inventories', [
            'seller_id' => 4991262,
        ]);
        $inventory = Inventory::whereSellerId(4991262)->first();

        // Asserisce che il batch è stato dispatchato
        Bus::assertBatched(function (PendingBatch $batch) use ($inventory) {
            return $batch->name == 'Fetch ' . $inventory->seller_username . ' inventory' &&
                $batch->jobs->count() === 56;
        });
    }

    public function test_it_deletes_inventory()
    {
        $user = User::factory()->create();
        $inventory = Inventory::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete(route('inventories.destroy', $inventory));

        $this->assertDatabaseMissing('inventories', [
            'id' => $inventory->id,
        ]);

        $response->assertRedirect(route('inventories.index'));
    }
}
