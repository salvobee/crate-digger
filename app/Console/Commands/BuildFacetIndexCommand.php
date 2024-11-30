<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Mgussekloo\FacetFilter\Indexer;

class BuildFacetIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facet:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Indexer $indexer)
    {
        $this->info('Build facet index for listings');
        try {
            $listings = Listing::all();
            $indexer->resetRows($listings);
            $indexer->buildIndex($listings);
            $this->info('Done');
        } catch (\Exception $exception)
        {
            $this->error($exception->getMessage());
        }
    }
}
