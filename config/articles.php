<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Article Attributes
    |--------------------------------------------------------------------------
    |
    | This is a collection of potential attributes that can be used to tag
    | and categorize an article. These attributes help in classifying articles
    | for easy searching and filtering. An article can have any combination of
    | these attributes, including all, none, or just some.
    |
    */

    'attributes' => [
        'keyword',
        'date',
        'category',
        'source',
    ],

    /*
    |--------------------------------------------------------------------------
    | Article Fetchers
    |--------------------------------------------------------------------------
    |
    | Each fetcher in this array is used to fetch articles from an external
    | news source and transform the data before saving it to the database.
    | Each fetcher class must implement the App\Services\Contracts\NewsFetcher
    | interface to ensure consistency in how data is fetched and transformed.
    |
    */

    'fetchers' => [
        App\Services\NewsAPIFetcher::class,
        App\Services\NYTimesFetcher::class,
        App\Services\TheGuardianFetcher::class,
    ],

];
