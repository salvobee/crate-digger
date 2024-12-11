<?php

namespace App\Services;

use App\Models\Service;
use Discogs\DiscogsClient;

class DiscogsApiService
{
    public DiscogsClient $client;

    public function __construct(protected Service $service)
    {
        $this->setupOauthClient();
    }

    private function setupOauthClient(): void
    {
        $oauth = new \GuzzleHttp\Subscriber\Oauth\Oauth1([
            'consumer_key'    => config('services.discogs.client_id'),
            'consumer_secret' => config('services.discogs.client_secret'),
            'token'           => $this->service->meta->token,
            'token_secret'    => $this->service->meta->tokenSecret
        ]);
        $handler = \GuzzleHttp\HandlerStack::create();
        $throttle = new \Discogs\Subscriber\ThrottleSubscriber;
        $handler->push(\GuzzleHttp\Middleware::retry($throttle->decider(), $throttle->delay()));
        $handler->push($oauth);

        $this->client = \Discogs\ClientFactory::factory([
            'handler' => $handler,
            'auth' => 'oauth'
        ]);
    }

    public function fetchInventoryData(mixed $username, int $pageNumber = 1, string $sort = null, string $order = null): array
    {
        return $this->client
            ->getInventory([
                'username' => $username,
                'page' => $pageNumber,
                'per_page' => 100,
                'sort' => $sort ?? 'listed',
                'sort_order' => $order ?? 'desc',
            ])
            ->toArray();
    }

    public function fetchReleaseData(string $releaseId)
    {
        return $this->client->getRelease(['id' => $releaseId])->toArray();
    }

    public function fetchList(string $listId): array
    {
        return $this->client->getLists(['list_id' => $listId])->toArray();
    }

    public function fetchMasterData(string $masterId): array
    {
        return $this->client->getMaster(['id' => $masterId])->toArray();
    }
}
