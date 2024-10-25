<?php

namespace App\Factories;

use App\Services\Contracts\ArticleFetcher;

class ArticleFetcherFactory
{
    public static function getConfiguredFetchers(): array
    {
        $config = config('articles.fetchers', []);
        $fetchers = [];

        foreach ($config as $fetcherClass) {

            /** @var NewsFetch $fetcher */
            $fetcher = app()->make($fetcherClass);

            if ($fetcher instanceof ArticleFetcher) {
                $fetchers[] = $fetcher;
            }
        }

        return $fetchers;
    }
}
