<?php

namespace App\Pipeline;

use App\Models\Article;
use App\Services\Contracts\ArticleFetcher;
use Exception;
use Illuminate\Support\Facades\Cache;
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
     * Executes the pipeline by processing each fetcher and saving the articles one by one.
     * To improve performance and reduce redundancy, a caching mechanism is used to
     * avoid inserting duplicate articles into the database. Each article has multiple
     * attributes, and all attributes are created at once for each new article that is not
     * already cached.
     */
    public function execute(): void
    {
        foreach ($this->fetchers as $fetcher) {
            try {
                foreach ($fetcher->fetchAndTransform() as $article) {
                    // Create a unique key for the article using hash to store in cache
                    $dateAttrib = collect($article['attributes'])->filter(fn (array $attrib) => $attrib['name'] == 'date');
                    $hash = hash('sha256', $article['url'].($dateAttrib->first()['value'] ?? null));

                    // Skip this article if it already exists in the cache
                    if (Cache::has($hash)) {
                        continue;
                    }

                    /** @var Article $model */
                    $model = Article::create([...$article, 'hash' => $hash]);
                    $model->attributes()->createMany($article['attributes']);

                    // Add the article key to the cache
                    Cache::put($hash, true, config('cache.ttl'));
                }
            } catch (Exception $ex) {
                Log::error($ex);
            }
        }
    }
}
