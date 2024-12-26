<?php

namespace App\Console\Commands;

use App\Actions\CrawlWordpressBlogAction;
use Illuminate\Console\Command;

class CrawlWordpressBlogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wordpress:crawl {blog_url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawls a wordpress blog';

    /**
     * Execute the console command.
     */
    public function handle(CrawlWordpressBlogAction $crawlWordpressBlogAction)
    {
        $blog_url = $this->argument('blog_url');
        $this->info("Crawling $blog_url");
        $count = $crawlWordpressBlogAction->execute($blog_url);
        $this->info("Enqueued $count list pages fetch jobs for $blog_url");
    }
}
