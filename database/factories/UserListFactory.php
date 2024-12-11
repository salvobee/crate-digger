<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserList>
 */
class UserListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discogs_id' => $this->faker->numberBetween(1,10000),
            'discogs_url' => $this->faker->url(),
            'name' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
