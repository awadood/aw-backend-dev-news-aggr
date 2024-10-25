<?php

namespace App\Pipeline;

use App\Exceptions\FetchFailedException;
use App\Models\Article;
use App\Services\Contracts\ArticleFetcher;
use Exception;
use Illuminate\Support\Facades\Log;

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
     * one by one. Each article has multiple attributes, so create all the
     * attributes at once for each article. Efficient insertion of articles is
     * compromised for the accuracy of attributes.
     */
    public function execute(): void
    {
        foreach ($this->fetchers as $fetcher) {
            try {
                foreach ($fetcher->fetchAndTransform() as $article) {
                    /** @var Article $model */
                    $model = Article::create($article);
                    $model->attributes()->createMany($article['attributes']);
                }
            } catch (Exception $ex) {
                Log::error($ex);
            }
        }
    }
}
