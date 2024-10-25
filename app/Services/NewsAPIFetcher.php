<?php

namespace App\Services;

use App\Exceptions\FetchFailedException;
use App\Services\Contracts\ArticleFetcher;
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
     */
    public function fetchAndTransform(): iterable
    {
        $response = Http::get($this->apiUrl, $this->queryParameters);

        if ($response->successful()) {
            $articles = $response->json()['articles'];

            foreach ($articles as $article) {
                $modelData = [
                    'title' => $article['title'],
                    'url' => $article['url'],
                    'description' => $article['description'],
                    'attributes' => [
                        ['name' => 'date', 'value' => $article['publishedAt']],
                        ['name' => 'category', 'value' => 'business'],
                        ['name' => 'source', 'value' => $article['source']['name']],
                        ['name' => 'author', 'value' => $article['author']],
                    ],
                ];

                $modelData['attributes'] = collect($modelData['attributes'])->filter(function (array $attrib) {
                    return $attrib['value'] != null;
                })->all();

                yield $modelData;
            }
        } else {
            throw new FetchFailedException('Failed to fetch data from NewsAPI: ' . $response->body());
        }
    }
}
