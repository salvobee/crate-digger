<?php

namespace Database\Factories;

use App\Enums\UserListItemType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserListItem>
 */
class UserListItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement([
                UserListItemType::MASTER->value,
                UserListItemType::RELEASE->value
            ]),
            'discogs_id' => $this->faker->word(),
            'discogs_url' => $this->faker->word(),
            'display_title' => $this->faker->word(),
            'comment' => $this->faker->word(),
        ];
    }
}
