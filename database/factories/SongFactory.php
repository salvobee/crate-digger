<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Label;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Song>
 */
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'artist_id' => Artist::factory(),
            'label_id' => Label::factory(),
            'artist_name' => $this->faker->name(),
            'name' => $this->faker->sentence(3),
            'version' => $this->faker->optional()->word(),
            'year' => $this->faker->year(),
            'label_name' => $this->faker->optional()->company(),
            'discogs_master_id' => $this->faker->optional()->uuid(),
        ];
    }
}
