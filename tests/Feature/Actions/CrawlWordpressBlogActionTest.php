<?php

namespace Tests\Feature\Actions;

use App\Actions\CrawlWordpressBlogAction;
use App\Jobs\FetchWordpressArticlesPageJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Traits\HasDecaDanceFixtures;

class CrawlWordpressBlogActionTest extends TestCase
{
    use HasDecaDanceFixtures;

    public function test_it_will_dispatch_jobs_for_crawling_blog_archive()
    {
        $example_blog_url = 'https://example.com';
        Bus::fake();
        Http::fake([
            $example_blog_url => Http::response($this->getHomePageMockedHtml(), 200),
        ]);
        /** @var CrawlWordpressBlogAction $action */
        $action = app(CrawlWordpressBlogAction::class);

        $action->execute($example_blog_url);
        Bus::assertDispatchedTimes(FetchWordpressArticlesPageJob::class, 96);
    }
}
