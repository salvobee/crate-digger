<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source' => $this->faker->word(),
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(5, true),
            'tags' => $this->faker->words(5),
            'published_at' => $this->faker->dateTime(),
            'author' => $this->faker->name(),
        ];
    }
}
