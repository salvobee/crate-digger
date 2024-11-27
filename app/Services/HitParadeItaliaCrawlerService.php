<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class HitParadeItaliaCrawlerService
{
    const BASE_URL = "https://hitparadeitalia.it";
    const DANCE_CHARTS_INDEX_URL = "/mono/dancecharts/weeks.htm";

    /**
     * @throws \Throwable
     */
    public function listCharts(): Collection
    {
        $response = Http::get(self::BASE_URL . self::DANCE_CHARTS_INDEX_URL);

        throw_unless($response->successful(), new \Exception("Unable to fetch the dance charts index."));

        $crawler = new Crawler($response->body());

        $links = $crawler->filter('tr.titoli a')->each(function (Crawler $node) {
            return $node->attr('href');
        });

        return collect($links)
            ->filter(fn ($link) => !Str::contains($link, 'mailto:'))
            ->map(function ($link) {
                $basePath = dirname(self::DANCE_CHARTS_INDEX_URL); // Estrae il base path dalla costante
                return self::BASE_URL . $basePath . '/' . ltrim($link, '/');
            })->values();
    }

    // Metodo per elaborare una singola classifica
    public function fetchChart($link)
    {
        $response = Http::get($link);

        throw_unless($response->successful(), new \Exception("Unable to fetch the dance charts index."));

        $crawler = new Crawler($response->body());

        $list = $crawler->filter('ol')->first();


        // Estrai i dati dei brani dalla lista
        $songs = $list->filter('li')->each(function (Crawler $node) {
            // Estrarre il titolo e l'artista (assumendo che seguano lo schema del <b>)
            $songInfo = $node->filter('b')->text();
            [$title, $artist] = explode(' - ', $songInfo, 2);

            // Estrarre i dettagli aggiuntivi (BPM e label)
            $details = $node->filter('small')->text();
            preg_match('/\[(\d+) BPM - ([^]]+)]/', $details, $matches);

            return [
                'title' => trim($title),
                'artist' => trim($artist),
                'bpm' => $matches[1] ?? null,
                'label' => $matches[2] ?? null,
            ];
        });

        return $songs;
    }

    // Metodo per iterare su tutti i link e processarli
    public function fetchAllCharts(array $links): void
    {

    }

    private function saveChartEntry(mixed $entry)
    {

    }
}

