<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Record>
 */
class RecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'artists' => $this->fakeArtists(2),
            'title' => $this->faker->words(3, true) . ' - ' . $this->faker->word(),
            'year' => $this->faker->year(),
            'discogs_id' => $this->faker->randomNumber(6, true),
            'discogs_url' => $this->faker->url(),
            'discogs_main_release_id' => $this->faker->randomNumber(6, true),
            'discogs_most_recent_release_id' => $this->faker->randomNumber(6, true),
            'meta' => [
                'example_meta_key' => $this->faker->word(),
                'another_meta_key' => $this->faker->word(),
            ],
        ];
    }

    private function fakeArtists(int $count = 1): array
    {
        return Collection::times($count)->map(fn ($i) => [
            "name" => $this->faker->name(),
            "anv" => "",
            "join" => "",
            "role" => "",
            "tracks" => "",
            "id" => $this->faker->randomNumber(6, true),
            "resource_url" => $this->faker->url( true),
        ])->toArray();
    }
}
