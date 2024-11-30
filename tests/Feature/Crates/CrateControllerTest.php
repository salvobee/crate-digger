<?php

namespace Tests\Feature\Crate;

use App\Actions\UpdateReleaseDataAction;
use App\Models\Crate;
use App\Models\Listing;
use App\Models\Release;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CrateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_a_list_of_crates()
    {
        $user = User::factory()->create();
        $crate = Crate::factory()->for($user)->create();

        $this->actingAs($user)
            ->get(route('crates.index'))
            ->assertOk()
            ->assertInertia(fn ($page) =>
            $page->component('Crates/Index')
                ->has('crates', 1)
                ->where('crates.0.id', $crate->id)
            );
    }

    public function test_it_stores_a_new_crate_and_updates_release_data()
    {
        $user = User::factory()->create();
        $listing = Listing::factory()->create();
        $release = Release::factory()->create();
        $listing->release()->associate($release)->save();

        $mockAction = Mockery::mock(UpdateReleaseDataAction::class);
        $mockAction->shouldReceive('execute')->once()->with(Mockery::type(Release::class));

        $this->instance(UpdateReleaseDataAction::class, $mockAction);

        $this->actingAs($user)
            ->post(route('crates.store'), [
                'listing' => $listing->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crates', [
            'user_id' => $user->id,
            'listing_id' => $listing->id,
        ]);
    }

    public function test_it_updates_a_crate()
    {
        $user = User::factory()->create();
        $crate = Crate::factory()->for($user)->create(['is_liked' => false]);

        $this->actingAs($user)
            ->put(route('crates.update', $crate), [
                'is_liked' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('crates', [
            'id' => $crate->id,
            'is_liked' => true,
        ]);
    }

    public function test_it_destroys_a_crate()
    {
        $user = User::factory()->create();
        $crate = Crate::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('crates.destroy', $crate))
            ->assertRedirect();

        $this->assertModelMissing($crate);
    }

    public function test_it_denies_destroying_a_crate_not_owned_by_the_user()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $crate = Crate::factory()->for($anotherUser)->create();

        $this->actingAs($user)
            ->delete(route('crates.destroy', $crate))
            ->assertStatus(401);

        $this->assertModelExists($crate);
    }
}
