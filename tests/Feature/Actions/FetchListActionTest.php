<?php

namespace Tests\Feature\Actions;

use App\Actions\FetchListAction;
use App\Enums\UserListItemType;
use App\Models\UserList;
use App\Models\UserListItem;
use App\Services\DiscogsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class FetchListActionTest extends TestCase
{
    use RefreshDatabase;
    public function test_it_will_fetch_a_list_of_releases_and_its_items()
    {
        $action = app(FetchListAction::class);

        $test_list_id = "1565197";
        $action->execute($test_list_id);

        $this->assertDatabaseHas(UserList::class, [
            'discogs_id' => $test_list_id,
            'discogs_url' => 'https://www.discogs.com/lists/1990-Global-Dance-Albums/1565197',
            'name' => '1990 Global Dance Albums',
            'description' => 'A list of world famous Dance Albums from year 1990',
        ]);

        $list = UserList::whereDiscogsId($test_list_id)->first();
        $this->assertCount(9, $list->items);
        $this->assertDatabaseHas(UserListItem::class, [
            "user_list_id" => $list->id,
            "type" => UserListItemType::RELEASE->value,
            "discogs_id" => 168546,
            "discogs_url" => "https://www.discogs.com/release/168546-Black-Box-Dreamland",
            "display_title" => "Black Box - Dreamland",
            "comment" => "On the italian label Groove Groove Melody",
        ]);
    }

    private function mockReleaseListResponse(): void
    {
        $discogsApiService = Mockery::mock(DiscogsApiService::class);
        $this->app->instance(DiscogsApiService::class, $discogsApiService);
        $discogsApiService
            ->shouldReceive('fetchList')
            ->once()
            ->andReturn([
                "id" => 1565197,
                "user" => [
                    "id" => 443684,
                    "avatar_url" => "https://gravatar.com/avatar/2554c1ede6753423021a1061e2800c5d2052c296deba97cbaad17a0286f66aab?s=500&r=pg&d=mm",
                    "username" => "djsalvob",
                    "resource_url" => "https://api.discogs.com/users/djsalvob",
                ],
                "name" => "1990 Global Dance Albums",
                "description" => "A list of world famous Dance Albums from year 1990",
                "public" => true,
                "date_added" => "2024-11-25T23:51:33-08:00",
                "date_changed" => "2024-12-11T02:14:52-08:00",
                "uri" => "https://www.discogs.com/lists/1990-Global-Dance-Albums/1565197",
                "resource_url" => "https://api.discogs.com/lists/1565197",
                "image_url" => "",
                "items" => [
                    [
                        "type" => "release",
                        "id" => 168546,
                        "comment" => "On the italian label Groove Groove Melody",
                        "uri" => "https://www.discogs.com/release/168546-Black-Box-Dreamland",
                        "resource_url" => "https://api.discogs.com/releases/168546",
                        "image_url" => "",
                        "display_title" => "Black Box - Dreamland",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 272,
                                "in_collection" => 761,
                            ],
                        ],
                    ],
                    [
                        "type" => "release",
                        "id" => 151971,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/release/151971-Technotronic-Pump-Up-The-Jam",
                        "resource_url" => "https://api.discogs.com/releases/151971",
                        "image_url" => "",
                        "display_title" => "Technotronic - Pump Up The Jam",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 342,
                                "in_collection" => 697,
                            ],
                        ],
                    ],
                    [
                        "type" => "release",
                        "id" => 140963,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/release/140963-Dr-Alban-Hello-Afrika-The-Album",
                        "resource_url" => "https://api.discogs.com/releases/140963",
                        "image_url" => "",
                        "display_title" => "Dr. Alban - Hello Afrika (The Album)",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 233,
                                "in_collection" => 1392,
                            ],
                        ],
                    ],
                    [
                        "type" => "master",
                        "id" => 106945,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/master/106945-Inner-City-Paradise",
                        "resource_url" => "https://api.discogs.com/masters/106945",
                        "image_url" => "",
                        "display_title" => "Inner City - Paradise",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 651,
                                "in_collection" => 2287,
                            ],
                        ],
                    ],
                    [
                        "type" => "release",
                        "id" => 2387919,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/release/2387919-Snap-World-Power",
                        "resource_url" => "https://api.discogs.com/releases/2387919",
                        "image_url" => "",
                        "display_title" => "Snap! - World Power",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 371,
                                "in_collection" => 1196,
                            ],
                        ],
                    ],
                    [
                        "type" => "master",
                        "id" => 299478,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/master/299478-49ers-Touch-Me",
                        "resource_url" => "https://api.discogs.com/masters/299478",
                        "image_url" => "",
                        "display_title" => "49ers - Touch Me",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 41,
                                "in_collection" => 79,
                            ],
                        ],
                    ],
                    [
                        "type" => "master",
                        "id" => 364221,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/master/364221-Twenty-4-Seven-Featuring-Capt-Hollywood-Street-Moves",
                        "resource_url" => "https://api.discogs.com/masters/364221",
                        "image_url" => "",
                        "display_title" => "Twenty 4 Seven Featuring Capt. Hollywood* - Street Moves",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 2,
                                "in_collection" => 7,
                            ],
                        ],
                    ],
                    [
                        "type" => "release",
                        "id" => 1178304,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/release/1178304-C-C-Music-Factory-Gonna-Make-You-Sweat",
                        "resource_url" => "https://api.discogs.com/releases/1178304",
                        "image_url" => "",
                        "display_title" => "C & C Music Factory* - Gonna Make You Sweat",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 458,
                                "in_collection" => 526,
                            ],
                        ],
                    ],
                    [
                        "type" => "release",
                        "id" => 7379288,
                        "comment" => "",
                        "uri" => "https://www.discogs.com/release/7379288-Madonna-The-Immaculate-Collection",
                        "resource_url" => "https://api.discogs.com/releases/7379288",
                        "image_url" => "",
                        "display_title" => "Madonna - The Immaculate Collection",
                        "stats" => [
                            "community" => [
                                "in_wantlist" => 70,
                                "in_collection" => 129,
                            ],
                        ],
                    ],
                ],
            ]);
    }

}
