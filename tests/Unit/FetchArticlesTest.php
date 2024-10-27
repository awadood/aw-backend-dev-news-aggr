<?php

namespace Tests\Unit;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FetchArticlesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function fetch_articles_command_logs_error_on_exception(): void
    {
        Log::shouldReceive('info')->andReturnNull();
        Log::shouldReceive('error')->andReturnNull();
        Cache::shouldReceive('has')->andReturnFalse();
        Cache::shouldReceive('put')->andThrow(new Exception('mocked exception'));
        $this->artisan('articles:fetch-and-store')->assertExitCode(0);
    }

    #[Test]
    public function fetch_articles_command_skip_articles_creation_if_already_created(): void
    {
        Log::shouldReceive('info')->andReturns('The pipeline of fetchers has been executed.');
        Cache::shouldReceive('has')->andReturnTrue();
        $this->artisan('articles:fetch-and-store')->assertExitCode(0);
    }
}
