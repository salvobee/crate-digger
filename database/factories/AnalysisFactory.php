<?php

namespace Database\Factories;

use App\Enums\AnalysisType;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Analysis>
 */
class AnalysisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'type' => $this->faker->randomElement(AnalysisType::values()),
            'resource_id' => Inventory::factory(),
            'jobs' => $this->faker->numberBetween(1,100),
            'processed' => $this->faker->numberBetween(1,100),
            'failed' => $this->faker->numberBetween(1,10),
            'batch_id' => $this->faker->uuid(),
        ];
    }
}
