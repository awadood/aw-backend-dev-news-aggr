<?php

namespace App\Pipeline;

use App\Models\Article;
use App\Services\Contracts\NewsFetcher;

class NewsFetcherPipeline
{
    /**
     * The fetchers that fetches news data sources
     *
     * @var array<int, NewsFetcher>
     */
    private array $fetchers = [];

    /**
     * 
     */
    public function addFetcher(NewsFetcher $fetcher): NewsFetcherPipeline
    {
        $this->fetchers[] = $fetcher;

        // return for chaining
        return $this;
    }

    /**
     * Execute the pipeline, process each fetcher, and yield articles one by one
     */
    public function execute(): void
    {
        $batch = [];
        $batchSize = 100; // Define batch size

        foreach ($this->fetchers as $fetcher) {
            foreach ($fetcher->fetchAndTransform() as $article) {
                $batch[] = [
                    'title' => $article['title'],
                    'content' => $article['content'],
                    'source' => $article['source'],
                    'author' => $article['author'],
                    'published_at' => $article['published_at'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // If batch size is reached, save to database and reset batch
                if (count($batch) >= $batchSize) {
                    Article::insert($batch);
                    $batch = []; // Reset the batch
                }
            }
        }

        // Save any remaining articles in the batch
        if (! empty($batch)) {
            Article::insert($batch);
        }
    }
}
