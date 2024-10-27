<?php

namespace Tests\Unit;

use App\Exceptions\FetchFailedException;
use App\Services\NYTimesFetcher;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NYTimesFetcherTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_handles_exception_when_fetching_from_new_york_times(): void
    {
        $guzzleRequest = new GuzzleRequest('GET', 'https://newsapi.org/v2/top-headlines');
        Http::shouldReceive('timeout')->andThrow(new RequestException(new Response(new GuzzleResponse(400), $guzzleRequest)));

        $fetcher = app()->make(NYTimesFetcher::class);
        $this->expectException(FetchFailedException::class);

        //Call the fetchAndTransform method to trigger the exception
        iterator_to_array($fetcher->fetchAndTransform());
    }
}
