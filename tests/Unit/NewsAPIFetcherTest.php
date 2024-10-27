<?php

namespace Tests\Unit;

use App\Exceptions\FetchFailedException;
use App\Services\NewsAPIFetcher;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewsAPIFetcherTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_handles_exception_when_fetching_from_news_api(): void
    {
        $guzzleRequest = new GuzzleRequest('GET', 'https://mock-link.com');
        Http::shouldReceive('timeout')->andThrow(new RequestException(new Response(new GuzzleResponse(400), $guzzleRequest)));

        $fetcher = app()->make(NewsAPIFetcher::class);
        $this->expectException(FetchFailedException::class);

        //Call the fetchAndTransform method to trigger the exception
        iterator_to_array($fetcher->fetchAndTransform());
    }
}
