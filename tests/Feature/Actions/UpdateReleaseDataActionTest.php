<?php

namespace Tests\Feature\Actions;

use App\Actions\UpdateReleaseDataAction;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Release;
use App\Models\Style;
use App\Services\DiscogsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UpdateReleaseDataActionTest extends TestCase
{
    use RefreshDatabase;

    private $trackList;

    public function test_it_updates_release_data_correctly(): void
    {
        // Arrange
        $release = Release::factory()->create([
            'discogs_id' => 1975455,
        ]);

        $this->trackList = [
            [
                "position" => "A1",
                "type_" => "track",
                "title" => "A New Error",
                "duration" => "6:07",
            ],
            [
                "position" => "A2",
                "type_" => "track",
                "title" => "Rusty Nails",
                "extraartists" => [
                    [
                        "name" => "Sascha Ring",
                        "anv" => "S. Ring",
                        "join" => "",
                        "role" => "Vocals",
                        "tracks" => "",
                        "id" => 66303,
                        "resource_url" => "https://api.discogs.com/artists/66303",
                    ],
                ],
                "duration" => "4:32",
            ],
            [
                "position" => "A3",
                "type_" => "track",
                "title" => "Seamonkey",
                "duration" => "6:14",
            ],
            [
                "position" => "A4",
                "type_" => "track",
                "title" => "Slow Match",
                "extraartists" => [
                    [
                        "name" => "Paul St. Hilaire",
                        "anv" => "",
                        "join" => "",
                        "role" => "Featuring, Vocals",
                        "tracks" => "",
                        "id" => 104604,
                        "resource_url" => "https://api.discogs.com/artists/104604",
                    ],
                ],
                "duration" => "5:08",
            ],
        ];
        $discogsApiResponse = [
            'country' => 'Italy',
            'community' => [
                'rating' => [
                    'average' => 4.5,
                    'count' => 12,
                ],
            ],
            'master_id' => 1201262,
            'num_for_sale' => 13,
            'lowest_price' => 1.54,
            'videos' => [
                [
                    'title' => 'Title',
                    'description' => 'description',
                    'uri' => 'https://www.youtube.com/watch?v=MKk-a7qamdk',
                    'embed' => true,
                ],
                [
                    'title' => 'Title',
                    'description' => 'description',
                    'uri' => 'https://www.youtube.com/watch?v=YVlG6GIMBTY',
                    'embed' => true,
                ],
            ],
            'tracklist' => $this->trackList,
            'genres' => ['Electronic'],
            'styles' => ['Euro House'],
            'formats' => [
                [
                    'name' => 'Vinyl',
                    'descriptions' => ['12"'],
                ],
            ],
            'images' => [
                [
                    "type" => "primary",
                    "uri" =>  "https://discogs.com/images/test-01.png",
                    "resource_url" => "https://api.discogs.com/images/test-01.png",
                    "uri150" =>  "https://discogs.com/images/150/test-01.png",
                    "width" => 600,
                    "height" => 480
                ],
                [
                    "type" => "secondary",
                    "uri" =>  "https://discogs.com/images/test-02.png",
                    "resource_url" => "https://api.discogs.com/images/test-02.png",
                    "uri150" =>  "https://discogs.com/images/150/test-02.png",
                    "width" => 600,
                    "height" => 480
                ],
                [
                    "type" => "secondary",
                    "uri" =>  "https://discogs.com/images/test-03.png",
                    "resource_url" => "https://api.discogs.com/images/test-03.png",
                    "uri150" =>  "https://discogs.com/images/150/test-03.png",
                    "width" => 600,
                    "height" => 480
                ],
                [
                    "type" => "secondary",
                    "uri" =>  "https://discogs.com/images/test-04.png",
                    "resource_url" => "https://api.discogs.com/images/test-04.png",
                    "uri150" =>  "https://discogs.com/images/150/test-04.png",
                    "width" => 600,
                    "height" => 480
                ],

            ]
        ];

        // Mock DiscogsApiService
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $discogsApiService
            ->shouldReceive('fetchReleaseData')
            ->with($release->discogs_id)
            ->andReturn($discogsApiResponse);

        $action = new UpdateReleaseDataAction($discogsApiService);

        // Act
        $action->execute($release->discogs_id);

        // Assert
        $this->assertDatabaseHas('releases', [
            'id' => $release->id,
            'country' => 'Italy',
            'rating_average' => 4.5,
            'rating_count' => 12,
            'master_id' => 1201262,
            'num_for_sale' => 13,
            'lowest_price' => 1.54,
        ]);

        $this->assertEquals($this->trackList, $release->fresh()->tracks_list);

        $this->assertEquals([[
            'title' => 'Title',
            'description' => 'description',
            'uri' => 'https://www.youtube.com/watch?v=MKk-a7qamdk',
            'embed' => true,
        ],
            [
                'title' => 'Title',
                'description' => 'description',
                'uri' => 'https://www.youtube.com/watch?v=YVlG6GIMBTY',
                'embed' => true,
            ]], $release->fresh()->videos);

        $this->assertEquals([
            [
                "type" => "primary",
                "uri" =>  "https://discogs.com/images/test-01.png",
                "resource_url" => "https://api.discogs.com/images/test-01.png",
                "uri150" =>  "https://discogs.com/images/150/test-01.png",
                "width" => 600,
                "height" => 480
            ],
            [
                "type" => "secondary",
                "uri" =>  "https://discogs.com/images/test-02.png",
                "resource_url" => "https://api.discogs.com/images/test-02.png",
                "uri150" =>  "https://discogs.com/images/150/test-02.png",
                "width" => 600,
                "height" => 480
            ],
            [
                "type" => "secondary",
                "uri" =>  "https://discogs.com/images/test-03.png",
                "resource_url" => "https://api.discogs.com/images/test-03.png",
                "uri150" =>  "https://discogs.com/images/150/test-03.png",
                "width" => 600,
                "height" => 480
            ],
            [
                "type" => "secondary",
                "uri" =>  "https://discogs.com/images/test-04.png",
                "resource_url" => "https://api.discogs.com/images/test-04.png",
                "uri150" =>  "https://discogs.com/images/150/test-04.png",
                "width" => 600,
                "height" => 480
            ],
        ], $release->fresh()->images);

        $this->assertCount(1, Genre::all());
        $this->assertDatabaseHas('genres', ['name' => 'Electronic']);
        $this->assertEquals(['Electronic'], $release->genres->pluck('name')->toArray());

        $this->assertCount(1, Style::all());
        $this->assertDatabaseHas('styles', ['name' => 'Euro House']);
        $this->assertEquals(['Euro House'], $release->styles->pluck('name')->toArray());

        $this->assertCount(2, Format::all()); // Vinyl and 12"
        $this->assertDatabaseHas('formats', ['name' => 'Vinyl']);
        $this->assertDatabaseHas('formats', ['name' => '12"']);
        $this->assertEquals(['Vinyl', '12"'], $release->formats->pluck('name')->toArray());
    }
}
