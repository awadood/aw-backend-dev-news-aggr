<?php

namespace App\Factories;

use App\Services\Contracts\NewsFetcher;

class NewsFetcherFactory
{
    public static function getConfiguredFetchers(): array
    {
        $config = config('articlas.fetchers', []);
        $fetchers = [];

        foreach ($config as $fetcherClass) {

            /** @var NewsFetch $fetcher */
            $fetcher = app()->make($fetcherClass);

            if ($fetcher instanceof NewsFetcher) {
                $fetchers[] = $fetcher;
            }
        }

        return $fetchers;
    }
}
