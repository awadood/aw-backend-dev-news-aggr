<?php

namespace App\Services\Contracts;

/**
 * Interface ArticleFetcher
 *
 * This interface defines the contract for any service that fetches and
 * transforms articles from an external source. Implementations of this
 * interface should handle the retrieval of data and transform it into a
 * uniform structure that can be processed and stored by the application.
 */
interface ArticleFetcher
{
    /**
     * Fetch and transform news articles.
     *
     * This method should be implemented to fetch articles from an external
     * source and return an iterable collection of transformed data, ensuring
     * that the data structure is consistent across different implementations.
     *
     * @return iterable An iterable collection of transformed articles.
     */
    public function fetchAndTransform(): iterable;
}
