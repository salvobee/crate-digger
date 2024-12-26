<?php

namespace App\Actions;

use App\Jobs\FetchWordpressArticlesPageJob;
use App\Services\WordpressCrawlerService;

class CrawlWordpressBlogAction
{


    public function __construct(protected WordpressCrawlerService $crawlerService)
    {
    }

    public function execute(string $blogUrl): int
    {
        $articles = $this->crawlerService->listArticlesPages($blogUrl);
        $articles->each(fn (string $listPageUrl) => FetchWordpressArticlesPageJob::dispatch($listPageUrl));

        return $articles->count();
    }
}
