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

    public function test_it_updates_release_data_correctly(): void
    {
        // Arrange
        $release = Release::factory()->create([
            'discogs_id' => 1975455,
        ]);

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
                ['uri' => 'https://www.youtube.com/watch?v=MKk-a7qamdk'],
                ['uri' => 'https://www.youtube.com/watch?v=YVlG6GIMBTY'],
            ],
            'genres' => ['Electronic'],
            'styles' => ['Euro House'],
            'formats' => [
                [
                    'name' => 'Vinyl',
                    'descriptions' => ['12"'],
                ],
            ],
        ];

        // Mock DiscogsApiService
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $discogsApiService
            ->shouldReceive('fetchReleaseData')
            ->with($release->discogs_id)
            ->andReturn($discogsApiResponse);

        $action = new UpdateReleaseDataAction($discogsApiService);

        // Act
        $action->execute($release);

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

        $this->assertEquals([
            'https://www.youtube.com/watch?v=MKk-a7qamdk',
            'https://www.youtube.com/watch?v=YVlG6GIMBTY',
        ], $release->fresh()->videos);

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
