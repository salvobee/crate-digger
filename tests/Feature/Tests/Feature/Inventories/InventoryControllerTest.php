<?php

namespace Tests\Feature\Inventories;

use App\Models\Inventory;
use App\Models\User;
use App\Services\DiscogsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
                            "html_url" => "https://www.discogs.com/user/some-username",
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
            'html_url' => "https://api.discogs.com/users/some-username",
            'avatar_url' => "https://i.discgs.com/some-username.jpeg",
            'rating' => 98.4,
            'stars' => 4.5,
            'total_feedbacks' => 512,
            'min_order_total' => 9,
            'total_listings_count' => 5521,
        ]);

        $response->assertRedirect(route('inventories.index'));
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
