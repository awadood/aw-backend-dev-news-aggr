<?php

namespace App\Console\Commands;

use App\Factories\ArticleFetcherFactory;
use App\Pipeline\ArticleFetcherPipeline;
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
        $fetchers = ArticleFetcherFactory::getConfiguredFetchers();

        // Create a pipeline and add fetchers to it
        $pipeline = App::make(ArticleFetcherPipeline::class);
        foreach ($fetchers as $fetcher) {
            $pipeline->addFetcher($fetcher);
        }

        // Execute the pipeline to fetch and store articles
        $pipeline->execute();

        Log::info('The pipeline of fetchers has been executed.');
    }
}
