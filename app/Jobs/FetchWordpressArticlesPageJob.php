<?php

namespace App\Jobs;

use App\Services\WordpressCrawlerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchWordpressArticlesPageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $listPageUrl)
    {
        $this->onQueue('articles');
    }

    /**
     * Execute the job.
     */
    public function handle(WordpressCrawlerService $crawlerService): void
    {
        $crawlerService->fetchArticlesListPage($this->listPageUrl);
    }
}
