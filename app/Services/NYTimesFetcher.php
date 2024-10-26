<?php

namespace App\Services;

use App\Exceptions\FetchFailedException;
use App\Services\Contracts\ArticleFetcher;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Class NYTimesFetcher
 *
 * This class fetches articles from NewsAPI and transforms them into a uniform
 * format. It implements the ArticleFetcher interface, ensuring consistency in
 * how articles are retrieved and structured across different sources.
 */
class NYTimesFetcher implements ArticleFetcher
{
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
     * Fetch and transform news articles from New York Times.
     *
     * This method sends an HTTP request to New York Times, retrieves the articles,
     * and transforms them into a consistent structure that includes the article's
     * title, URL, description, and related attributes such as author and source.
     *
     * @return iterable An iterable collection of transformed articles.
     *
     * @throws FetchFailedException if there is an error while fetching data from New York Times.
     */
    public function fetchAndTransform(): iterable
    {
        try {
            $response = Http::timeout(config('articles.timeout'))->get($this->apiUrl, $this->queryParameters);

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
