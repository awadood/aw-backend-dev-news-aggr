<?php

namespace App\Pipeline;

use App\Models\Article;
use App\Services\Contracts\ArticleFetcher;

class ArticleFetcherPipeline
{
    /**
     * This array stores the instances of all fetcher classes added to the
     * pipeline. Each fetcher is responsible for fetching and transforming
     * article data from a specific source.
     *
     * @var array<int, ArticleFetcher>
     */
    private array $fetchers = [];

    /**
     * Adds an article fetcher to the pipeline. This allows chaining multiple
     * fetchers together for processing. The method returns the current
     * instance for fluent interface usage.
     *
     * @param  ArticleFetcher  $input
     * @return ArticleFetcherPipeline the current instance
     */
    public function addFetcher(ArticleFetcher $fetcher): ArticleFetcherPipeline
    {
        $this->fetchers[] = $fetcher;

        // return for chaining
        return $this;
    }

    /**
     * Executes the pipeline by processing each fetcher and saving the articles
     * in batches to reduce memory usage. This approach prevents memory
     * exhaustion by handling the articles in manageable chunks.
     */
    public function execute(): void
    {
        $batch = [];
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
                if (count($batch) >= config('articles.batch_size')) {
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
