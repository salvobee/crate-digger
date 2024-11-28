<?php

namespace Tests\Feature\Schema;

use App\Models\Genre;
use App\Models\Format;
use App\Models\Inventory;
use App\Models\Listing;
use App\Models\Release;
use App\Models\Style;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleaseAndListingSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_listing_belongs_to_a_release_with_genre_style_and_format()
    {
        // Crea una release con generi, stili e formati
        $release = Release::factory()
            ->withGenreStylesAndFormats()
            ->create();

        // Crea una listing che appartiene alla release creata
        $listing = Listing::factory()
            ->withGenreStylesAndFormats()
            ->create([
                'release_id' => $release->id,
            ]);

        // Verifica che la listing appartenga alla release
        $this->assertEquals($release->id, $listing->release_id);

        // Verifica che la release abbia i generi, gli stili e i formati associati
        $this->assertCount(1, $release->genres);
        $this->assertCount(3, $release->styles);
        $this->assertCount(3, $release->formats);

        // Verifica che la listing abbia la relazione corretta con i generi, stili e formati
        $this->assertCount(1, $listing->release->genres);
        $this->assertCount(3, $listing->release->styles);
        $this->assertCount(3, $listing->release->formats);
    }

    public function test_a_release_can_have_multiple_listings()
    {
        // Crea una release con generi, stili e formati
        $release = Release::factory()
            ->withGenreStylesAndFormats()
            ->create();

        // Crea più listings per questa release
        $listings = Listing::factory()
            ->count(3)
            ->withGenreStylesAndFormats()
            ->create([
                'release_id' => $release->id,
            ]);

        // Verifica che la release abbia 3 listings associati
        $this->assertCount(3, $release->listings);
    }

    public function test_a_listing_belongs_to_inventory()
    {
        // Crea una release e un inventory
        $release = Release::factory()->create();
        $inventory = Inventory::factory()->create();

        // Crea una listing che appartiene all'inventory
        $listing = Listing::factory()->create([
            'release_id' => $release->id,
            'inventory_id' => $inventory->id,
        ]);

        // Verifica che la listing appartenga all'inventory
        $this->assertEquals($inventory->id, $listing->inventory_id);
    }

    public function test_genres_and_styles_are_attached_correctly_to_release_and_listing()
    {
        // Crea una release con generi, stili e formati
        $release = Release::factory()
            ->withGenreStylesAndFormats()
            ->create();

        // Crea una listing
        $listing = Listing::factory()
            ->create([
                'release_id' => $release->id,
            ]);

        // Recupera i generi e gli stili associati alla release tramite la listing
        $genres = $listing->release->genres;
        $styles = $listing->release->styles;

        // Verifica che i generi e gli stili siano correttamente associati
        $this->assertNotEmpty($genres);
        $this->assertNotEmpty($styles);
        $this->assertCount(1, $genres);
        $this->assertCount(3, $styles);
    }

    public function test_release_and_listing_have_videos()
    {
        // Crea una release con un video
        $release = Release::factory()->create([
            'videos' => ['https://www.youtube.com/watch?v=example'],
        ]);

        // Crea una listing
        $listing = Listing::factory()->create([
            'release_id' => $release->id,
        ]);

        // Verifica che la release abbia il video associato
        $this->assertNotEmpty($release->videos);
        $this->assertCount(1, $release->videos);
        $this->assertEquals('https://www.youtube.com/watch?v=example', $release->videos[0]);

        // Verifica che la listing faccia riferimento correttamente al video della release
        $this->assertEquals($release->videos, $listing->release->videos);
    }
}
