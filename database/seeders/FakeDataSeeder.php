<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Category;
use App\Models\Chart;
use App\Models\Label;
use App\Models\Position;
use App\Models\Song;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creazione delle categorie
        $categories = Category::factory(5)->create();

        foreach ($categories as $category) {
            // Creazione di 5 classifiche per ogni categoria
            $previousChart = null;

            for ($i = 1; $i <= 5; $i++) {
                $validFrom = now()->setDate(1995, 1, $i * 7 - 6)->startOfDay(); // Esempio: 1, 8, 15, 22, 29 gennaio 1995
                $validTo = $validFrom->copy()->addDays(6)->endOfDay();

                $chart = Chart::factory()->create([
                    'category_id' => $category->id,
                    'previous_chart_id' => $previousChart?->id,
                    'valid_from' => $validFrom,
                    'valid_to' => $validTo,
                ]);

                if ($previousChart) {
                    $previousChart->update(['next_chart_id' => $chart->id]);
                }

                $previousChart = $chart;

                // Creazione di 10 posizioni per ogni classifica
                foreach (range(1, 10) as $order) {
                    $artist = Artist::factory()->create();
                    $label = Label::factory()->create();

                    $song = Song::factory()->create([
                        'artist_id' => $artist->id,
                        'label_id' => $label->id,
                    ]);

                    Position::factory()->create([
                        'chart_id' => $chart->id,
                        'order' => $order,
                        'song_id' => $song->id,
                        'last_week_position' => $order > 1 ? $order - 1 : null,
                        'song_position_peak' => $order,
                    ]);
                }
            }
        }
    }
}
