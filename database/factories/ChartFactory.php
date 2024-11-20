<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chart>
 */
class ChartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'previous_chart_id' => null,
            'next_chart_id' => null,
            'valid_from' => $this->faker->optional()->date(),
            'valid_to' => $this->faker->date(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
