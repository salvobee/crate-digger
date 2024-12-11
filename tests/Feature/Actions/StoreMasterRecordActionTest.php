<?php

namespace Tests\Feature\Actions;

use App\Actions\StoreMasterRecordAction;
use App\Models\Record;
use App\Services\DiscogsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class StoreMasterRecordActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_creates_record_with_genres_and_styles()
    {
        $discogsApiService = $this->mock(DiscogsApiService::class);
        $test_master_id = "";
        $this->mockDiscogsResponse();

        $action = app(StoreMasterRecordAction::class);
        $record = $action->execute($test_master_id);

        $this->assertDatabaseHas(Record::class, [
            'title' => 'Test Album',
            'year' => '1999',
            'discogs_id' => '1050282',
            'discogs_url' => 'https://discogs.com/master/123',
            'discogs_main_release_id' => '123',
            'discogs_most_recent_release_id' => '456',
        ]);

        $this->assertDatabaseHas('genres', ['name' => 'Electronic']);
        $this->assertDatabaseHas('styles', ['name' => 'Techno']);
        $this->assertDatabaseHas('styles', ['name' => 'House']);

        $this->assertTrue($record->genres->contains('name', 'Electronic'));
        $this->assertTrue($record->styles->contains('name', 'Techno'));
        $this->assertTrue($record->styles->contains('name', 'House'));

        $this->assertEquals([
            [
                "name" => "Alphatek (2)",
                "anv" => "",
                "join" => "",
                "role" => "",
                "tracks" => "",
                "id" => 251017,
                "resource_url" => "https://api.discogs.com/artists/251017",
            ]
        ], $record->artists);

        $this->assertEquals('Alphatek (2) - Test Album', $record->display_title);
    }

    private function mockDiscogsResponse()
    {
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $this->app->instance(DiscogsApiService::class, $discogsApiService);
        $discogsApiService
            ->shouldReceive('fetchMasterData')
            ->once()
            ->andReturn([
            'id' => 1050282,
            'title' => 'Test Album',
            'year' => 1999,
            'uri' => 'https://discogs.com/master/123',
            'main_release' => 123,
            'most_recent_release' => 456,
            'genres' => ['Electronic'],
            'artists' => [
                [
                    "name" => "Alphatek (2)",
                    "anv" => "",
                    "join" => "",
                    "role" => "",
                    "tracks" => "",
                    "id" => 251017,
                    "resource_url" => "https://api.discogs.com/artists/251017",
                ]
            ],
            'styles' => ['Techno', 'House'],
            'notes' => 'A classic album.',
        ]);
    }

    public function test_it_will_update_existing_record()
    {
        $existing_record = Record::factory()->create();
        $this->mockDiscogsResponse();

        $action = app(StoreMasterRecordAction::class);
        $record = $action->execute($existing_record->discogs_id);

        $this->assertDatabaseHas(Record::class, [
            'title' => 'Test Album',
            'year' => '1999',
            'discogs_id' => '1050282',
            'discogs_url' => 'https://discogs.com/master/123',
            'discogs_main_release_id' => '123',
            'discogs_most_recent_release_id' => '456',
        ]);

        $this->assertDatabaseHas('genres', ['name' => 'Electronic']);
        $this->assertDatabaseHas('styles', ['name' => 'Techno']);
        $this->assertDatabaseHas('styles', ['name' => 'House']);

        $this->assertTrue($record->genres->contains('name', 'Electronic'));
        $this->assertTrue($record->styles->contains('name', 'Techno'));
        $this->assertTrue($record->styles->contains('name', 'House'));

        $this->assertEquals([
            [
                "name" => "Alphatek (2)",
                "anv" => "",
                "join" => "",
                "role" => "",
                "tracks" => "",
                "id" => 251017,
                "resource_url" => "https://api.discogs.com/artists/251017",
            ]
        ], $record->artists);

        $this->assertEquals('Alphatek (2) - Test Album', $record->display_title);
    }
}
