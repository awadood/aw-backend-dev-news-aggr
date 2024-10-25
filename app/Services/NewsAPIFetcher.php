<?php

namespace App\Services;

use App\Exceptions\FetchFailedException;
use App\Services\Contracts\ArticleFetcher;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Schema(
 *     schema="NewsAPIFetcher",
 *     type="object",
 *     title="NewsAPIFetcher",
 *     description="Service to fetch articles from NewsAPI",
 * )
 */
class NewsAPIFetcher implements ArticleFetcher
{
    /**
     * Timeout for invoking the newsapi.org API.
     */
    private const TIMEOUT = 10;

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
     * @return iterable an array containing data for article and its attributes.
     */
    public function fetchAndTransform(): iterable
    {
        try {
            $response = Http::timeout(static::TIMEOUT)->get($this->apiUrl, $this->queryParameters);

            if ($response->successful()) {
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
            } else {
                throw new FetchFailedException('Failed to fetch data from NewsAPI: '.$response->body());
            }
        } catch (RequestException $e) {
            throw new FetchFailedException('Network error occurred while fetching data from NewsAPI: '.$e->getMessage());
        }
    }
}
