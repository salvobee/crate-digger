<?php

namespace Database\Factories;

use App\Models\Format;
use App\Models\Genre;
use App\Models\Inventory;
use App\Models\Release;
use App\Models\Style;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inventory_id' => Inventory::factory(),
            'release_id' => Release::factory(),

            'discogs_id' => $this->faker->numberBetween(1,100000),

            'price_value' => $this->faker->randomFloat(2, 5, 100),
            'price_currency' => 'EUR',

            'media_condition' => $this->faker->word(),
            'sleeve_condition' => $this->faker->word(),
            'comments' => $this->faker->optional()->sentence(),
            'ships_from' => $this->faker->country(),
            'allow_offers' => $this->faker->boolean(),

            'listed_at' => $this->faker->dateTime(),
        ];
    }

    public function withGenreStylesAndFormats(): static
    {
        return $this->state(fn (array $attributes) => [
            'release_id' => Release::factory()
                ->hasAttached(Genre::factory()->count(1))
                ->hasAttached(Style::factory()->count(3))
                ->hasAttached(Format::factory()->count(3))
        ]);
    }
}
