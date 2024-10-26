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
        'author',
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

    /*
    |--------------------------------------------------------------------------
    | Batch Size
    |--------------------------------------------------------------------------
    |
    | Defines the maximum number of articles to accumulate before saving them
    | to the database. Adjusting this value helps in optimizing memory usage
    | and database performance. However, it is not in use for now.
    |
    */

    'batch_size' => env('BATCH_SIZE', 100),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Set the timeout for each call to the external data source. Adjusting this
    | value helps in optimizing API calls.
    |
    */

    'timeout' => env('TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | pagination
    |--------------------------------------------------------------------------
    |
    | write the appropriate comments
    |
    */

    'page_size' => env('ARTICLE_PAGE_SIZE', 10),

];
