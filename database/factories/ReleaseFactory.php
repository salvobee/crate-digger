<?php

namespace Database\Factories;

use App\Models\Format;
use App\Models\Genre;
use App\Models\Release;
use App\Models\Style;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Release>
 */
class ReleaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discogs_id' => $this->faker->unique()->randomNumber(8),
            'artist' => $this->faker->name(),
            'title' => $this->faker->word(),
            'label' => $this->faker->company(),
            'catalog_number' => $this->faker->word(),

            'want' => $this->faker->optional()->numberBetween(0, 100),
            'have' => $this->faker->optional()->numberBetween(0, 100),

            'rating_average' => $this->faker->optional()->randomFloat(1, 0, 5),
            'rating_count' => $this->faker->optional()->numberBetween(0, 500),

            'videos' => $this->faker->optional()->randomElements(['https://www.youtube.com/watch?v=example']),

            'master_id' => $this->faker->optional()->randomNumber(),
            'num_for_sale' => $this->faker->optional()->numberBetween(0, 100),
            'lowest_price' => $this->faker->optional()->randomFloat(2, 5, 50),
        ];
    }

    public function withGenreStylesAndFormats(): static
    {
        return $this
            ->hasAttached(Genre::factory()->count(1))
            ->hasAttached(Style::factory()->count(3))
            ->hasAttached(Format::factory()->count(3));
    }
}
