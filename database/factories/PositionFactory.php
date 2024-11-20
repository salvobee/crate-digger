<?php

namespace Database\Factories;

use App\Models\Chart;
use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chart_id' => Chart::factory(),
            'order' => $this->faker->numberBetween(1, 100),
            'name' => $this->faker->name() . ' - ' . $this->faker->sentence(3),
            'song_id' => Song::factory(),
            'last_week_position' => $this->faker->optional()->numberBetween(1, 100),
            'song_position_peak' => $this->faker->optional()->numberBetween(1, 100),
        ];
    }
}
