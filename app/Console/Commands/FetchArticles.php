<?php

namespace App\Console\Commands;

use App\Factories\NewsFetcherFactory;
use App\Pipeline\NewsFetcherPipeline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch-and-store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from external news APIs and store in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create fetchers using the factory
        $fetchers = NewsFetcherFactory::getConfiguredFetchers();

        // Create a pipeline and add fetchers to it
        $pipeline = App::make(NewsFetcherPipeline::class);
        foreach ($fetchers as $fetcher) {
            $pipeline->addFetcher($fetcher);
        }

        // Execute the pipeline to fetch and store articles
        $pipeline->execute();

        Log::info('Articles have been successfully fetched and updated.');
    }
}
