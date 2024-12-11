<?php

namespace App\Actions;

use App\Enums\UserListItemType;
use App\Jobs\FetchMasterDataJob;
use App\Jobs\UpdateReleaseDataJob;
use App\Models\UserList;
use App\Services\DiscogsApiService;

class FetchListAction
{

    public function __construct(
        protected DiscogsApiService $discogsApiService
    )
    {
    }

    public function execute(string $listId, bool $fetchItemsData = false)
    {
        $list_data = $this->discogsApiService->fetchList($listId);


        $list = UserList::create([
            'discogs_id' => $list_data['id'],
            'discogs_url' => $list_data['uri'],
            'name' => $list_data['name'],
            'description' => $list_data['description'],
        ]);

        foreach ($list_data['items'] as $list_item) {
            $list->items()->create([
                "type" => $list_item['type'],
                "discogs_id" => $list_item['id'],
                "discogs_url" => $list_item['uri'],
                "display_title" => $list_item['display_title'],
                "comment" => $list_item['comment'],
            ]);

            if ($fetchItemsData)
            {
                switch ($list_item['type']) {
                    case UserListItemType::RELEASE->value:
                        UpdateReleaseDataJob::dispatch($list_data['id']);
                        break;
                    case UserListItemType::MASTER->value:
                        FetchMasterDataJob::dispatch($list_data['id']);
                        break;
                    default:
                        break;
                }
            }
        }
    }
}
