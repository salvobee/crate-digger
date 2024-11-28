<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),

            'seller_username' => $this->faker->userName(),
            'seller_id' => $this->faker->unique()->randomNumber(5, true),
            'html_url' => $this->faker->url(),
            'avatar_url' => $this->faker->optional()->imageUrl(500, 500, 'avatar'),

            'rating' => $this->faker->optional()->randomFloat(2, 0, 100),
            'stars' => $this->faker->optional()->randomFloat(1, 0, 5),
            'total_feedbacks' => $this->faker->optional()->numberBetween(0, 1000),

            'min_order_total' => $this->faker->optional()->randomFloat(2, 0, 100),

            'total_listings_count' => $this->faker->optional()->numberBetween(0, 500),
            'total_listings_count_updated_at' => $this->faker->optional()->dateTimeThisMonth(),
        ];
    }
}
