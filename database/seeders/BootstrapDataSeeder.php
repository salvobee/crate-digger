<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BootstrapDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Deejay Parade',
                'country' => 'it',
            ],
            [
                'name' => 'UK Dance Chart',
                'country' => 'uk',
            ],
            [
                'name' => 'France Dance Chart',
                'country' => 'fr',
            ],
            [
                'name' => 'Germany Dance Chart',
                'country' => 'de',
            ],
            [
                'name' => 'Billboard Dance Chart',
                'country' => 'us',
            ],
        ];

        collect($categories)->each(fn ($category_attributes) => Category::create($category_attributes));
    }
}
