<?php

namespace App\Services;

use App\Exceptions\FetchFailedException;
use App\Services\Contracts\ArticleFetcher;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * @OA\Schema(
 *     schema="NYTimesFetcher",
 *     type="object",
 *     title="NYTimesFetcher",
 *     description="Service to fetch articles from New York Times",
 * )
 */
class NYTimesFetcher implements ArticleFetcher
{
    /**
     * Timeout for invoking the New York Times API.
     */
    private const TIMEOUT = 10;

    /**
     * The API endpoint to fetch news articles from New York Times API.
     */
    private string $apiUrl = 'https://api.nytimes.com/svc/search/v2/articlesearch.json';

    /**
     * The query parameters for invoking the API.
     */
    private array $queryParameters = [
        'api-key' => 'AIBrVeWq86EuWUGCLnzSq5MVQHx5pl3O',
    ];

    /**
     * Fetch and transform news articles from New York Times API.
     *
     * @return iterable an array containing data for article and its attributes.
     */
    public function fetchAndTransform(): iterable
    {
        try {
            $response = Http::timeout(static::TIMEOUT)->get($this->apiUrl, $this->queryParameters);

            if ($response->successful()) {
                $articles = $response->json()['response']['docs'];

                foreach ($articles as $article) {
                    $modelData = [
                        'title' => $article['headline']['main'],
                        'url' => $article['web_url'],
                        'description' => $article['lead_paragraph'],
                        'attributes' => [
                            ['name' => 'date', 'value' => Carbon::parse($article['pub_date'])->toDateTimeString()],
                            ['name' => 'category', 'value' => $article['section_name']],
                            ['name' => 'source', 'value' => $article['source']],
                            ['name' => 'author', 'value' => Str::replaceFirst('By ', '', $article['byline']['original'])],
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
