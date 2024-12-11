<?php

namespace App\Actions;

use App\Models\Genre;
use App\Models\Record;
use App\Models\Style;
use App\Services\DiscogsApiService;
use Illuminate\Support\Facades\DB;

class StoreMasterRecordAction
{
    public function __construct(
        private DiscogsApiService $discogsApiService
    ) {
    }

    public function execute(string $masterId): Record
    {
        $masterData = $this->discogsApiService->fetchMasterData($masterId);

        return DB::transaction(function () use ($masterData) {
            $record = Record::firstOrCreate([
                'discogs_id' => $masterData['id'],
            ], [
                'artists' => $masterData['artists'],
                'title' => $masterData['title'],
                'year' => $masterData['year'] ?? null,
                'discogs_url' => $masterData['uri'],
                'discogs_main_release_id' => $masterData['main_release'],
                'discogs_most_recent_release_id' => $masterData['most_recent_release'] ?? $masterData['main_release'],
                'meta' => $masterData,
            ]);

            $record->genres()->sync($this->getOrCreateGenres($masterData['genres'] ?? []));
            $record->styles()->sync($this->getOrCreateStyles($masterData['styles'] ?? []));

            return $record;
        });
    }

    private function getOrCreateGenres(array $genres): array
    {
        return collect($genres)->map(fn($genre) => Genre::firstOrCreate(['name' => $genre])->id)->toArray();
    }

    private function getOrCreateStyles(array $styles): array
    {
        return collect($styles)->map(fn($style) => Style::firstOrCreate(['name' => $style])->id)->toArray();
    }
}
