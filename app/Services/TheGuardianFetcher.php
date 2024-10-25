<?php

namespace App\Services;

use App\Exceptions\FetchFailedException;
use App\Services\Contracts\ArticleFetcher;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Schema(
 *     schema="TheGuardianFetcher",
 *     type="object",
 *     title="TheGuardianFetcher",
 *     description="Service to fetch articles from The Guardian",
 * )
 */
class TheGuardianFetcher implements ArticleFetcher
{
    /**
     * Timeout for invoking the New York Times API.
     */
    private const TIMEOUT = 10;

    /**
     * The API endpoint to fetch news articles from The Guardian API.
     */
    private string $apiUrl = 'https://content.guardianapis.com/search';

    /**
     * The query parameters for invoking the API.
     */
    private array $queryParameters = [
        'api-key' => '9e93b78e-7a45-4432-ba13-757a336f6a1b',
    ];

    /**
     * Fetch and transform news articles from The Guardian API.
     *
     * @return iterable an array containing data for article and its attributes.
     */
    public function fetchAndTransform(): iterable
    {
        try {
            $response = Http::timeout(static::TIMEOUT)->get($this->apiUrl, $this->queryParameters);

            if ($response->successful()) {
                $articles = $response->json()['response']['results'];

                foreach ($articles as $article) {
                    $modelData = [
                        'title' => $article['webTitle'],
                        'url' => $article['webUrl'],
                        'description' => null,
                        'attributes' => [
                            ['name' => 'date', 'value' => Carbon::parse($article['webPublicationDate'])->toDateTimeString()],
                            ['name' => 'category', 'value' => $article['sectionId']],
                            ['name' => 'source', 'value' => 'The Guardian'],
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
