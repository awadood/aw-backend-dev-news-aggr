<?php

namespace App\Services;

use App\Exceptions\FetchFailedException;
use App\Services\Contracts\ArticleFetcher;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * Class TheGuardianFetcher
 *
 * This class fetches articles from The Guardian and transforms them into a uniform
 * format. It implements the ArticleFetcher interface, ensuring consistency in
 * how articles are retrieved and structured across different sources.
 */
class TheGuardianFetcher implements ArticleFetcher
{
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
     * Fetch and transform news articles from The Guardian.
     *
     * This method sends an HTTP request to The Guardian, retrieves the articles,
     * and transforms them into a consistent structure that includes the article's
     * title, URL, description, and related attributes such as author and source.
     *
     * @return iterable An iterable collection of transformed articles.
     *
     * @throws FetchFailedException if there is an error while fetching data from The Guardian.
     */
    public function fetchAndTransform(): iterable
    {
        try {
            $response = Http::timeout(config('articles.timeout'))->get($this->apiUrl, $this->queryParameters);
            if (! $response->successful()) {
                throw new FetchFailedException('Failed to fetch data from NewsAPI: '.$response->body());
            }

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
        } catch (RequestException $e) {
            throw new FetchFailedException('Network error occurred while fetching data from NewsAPI: '.$e->getMessage());
        }
    }
}
