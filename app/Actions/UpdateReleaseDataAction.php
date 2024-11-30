<?php

namespace App\Actions;

use App\Models\Format;
use App\Models\Genre;
use App\Models\Release;
use App\Models\Style;
use App\Services\DiscogsApiService;

class UpdateReleaseDataAction
{

    public function __construct(
        private DiscogsApiService $discogsApiService)
    {
    }

    public function execute(Release $release): void
    {
        $release_data = $this->discogsApiService->fetchReleaseData($release->discogs_id);

        $release->country = $release_data['country'];
        $release->rating_average = $release_data['community']['rating']['average'];
        $release->rating_count = $release_data['community']['rating']['count'];
        $release->num_for_sale = $release_data['num_for_sale'];
        $release->lowest_price = $release_data['lowest_price'];

        if (array_key_exists("master_id", $release_data))
            $release->master_id = $release_data['master_id'];

        $release->videos = collect($release_data['videos'])->values()->toArray();
        $release->tracks_list = collect($release_data['tracklist'])->toArray();

        $release->save();

        // Attach genres
        $genres = [];
        foreach ($release_data['genres'] as $genre_name) {
            $genres[] = Genre::firstOrCreate(['name' => $genre_name]);
        }
        $release->genres()->sync(collect($genres)->map(fn (Genre $genre) => $genre->id)->values()->toArray());

        // Attach styles
        $styles = [];
        foreach ($release_data['styles'] as $style_name) {
            $styles[] = Style::firstOrCreate(['name' => $style_name]);
        }

        $release->styles()->sync(collect($styles)->map(fn (Style $style) => $style->id)->values()->toArray());

        $formats = [];
        // Attach formats
        foreach ($release_data['formats'] as $format) {
            $main_format_name = $format['name'];
            $formats[] = Format::firstOrCreate(['name' => $main_format_name]);

            $other_formats = $format['descriptions'];
            foreach ($other_formats as $other_format_name) {
                $formats[]  = Format::firstOrCreate(['name' => $other_format_name]);
            }
        }
        $release->formats()->sync(collect($formats)->map(fn (Format $format) => $format->id)->values()->toArray());
    }
}
