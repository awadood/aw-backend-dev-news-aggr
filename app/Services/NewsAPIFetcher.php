<?php

namespace App\Services;

use App\Exceptions\FetchFailedException;
use App\Services\Contracts\ArticleFetcher;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * Class NewsAPIFetcher
 *
 * This class fetches articles from NewsAPI and transforms them into a uniform
 * format. It implements the ArticleFetcher interface, ensuring consistency in
 * how articles are retrieved and structured across different sources.
 */
class NewsAPIFetcher implements ArticleFetcher
{
    /**
     * The API endpoint to fetch news articles from NewsAPI.
     */
    private string $apiUrl = 'https://newsapi.org/v2/top-headlines';

    /**
     * The query parameters for invoking the API.
     */
    private array $queryParameters = [
        'apiKey' => 'd6b67438cfbd4b2b95114a6b5a578776',
        'category' => 'business',
        'country' => 'us',
    ];

    /**
     * Fetch and transform news articles from NewsAPI.
     *
     * This method sends an HTTP request to NewsAPI, retrieves the articles, and transforms
     * them into a consistent structure that includes the article's title, URL, description,
     * and related attributes such as author and source.
     *
     * @return iterable An iterable collection of transformed articles.
     *
     * @throws FetchFailedException if there is an error while fetching data from NewsAPI.
     */
    public function fetchAndTransform(): iterable
    {
        try {
            $response = Http::timeout(config('articles.timeout'))->get($this->apiUrl, $this->queryParameters);
            if (! $response->successful()) {
                throw new FetchFailedException('Failed to fetch data from NewsAPI: '.$response->body());
            }

            $articles = $response->json()['articles'];
            foreach ($articles as $article) {
                $modelData = [
                    'title' => $article['title'],
                    'url' => $article['url'],
                    'description' => $article['description'],
                    'attributes' => [
                        ['name' => 'date', 'value' => Carbon::parse($article['publishedAt'])->toDateTimeString()],
                        ['name' => 'category', 'value' => 'business'],
                        ['name' => 'source', 'value' => $article['source']['name']],
                        ['name' => 'author', 'value' => $article['author']],
                    ],
                ];

                // Exclude all the attributes if the value is null.
                $modelData['attributes'] = collect($modelData['attributes'])
                    ->filter(fn (array $attrib) => $attrib['value'] != null)
                    ->all();

                yield $modelData;
            }
        } catch (RequestException $e) {
            throw new FetchFailedException('Network error occurred while fetching data from NewsAPI: '.$e->getMessage());
        }
    }
}
