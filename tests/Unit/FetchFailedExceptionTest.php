<?php

namespace Tests\Unit;

use App\Exceptions\FetchFailedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FetchFailedExceptionTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated_with_a_message(): void
    {
        $expectedMessage = 'An error occurred while fetching data';
        $exception = new FetchFailedException($expectedMessage);

        $this->assertEquals($expectedMessage, $exception->getMessage());

        $this->assertInstanceOf(FetchFailedException::class, $exception);
    }

    #[Test]
    public function it_can_be_reported(): void
    {
        $expectedMessage = 'Failed to fetch data from API.';

        Log::shouldReceive('error')->once()->with($expectedMessage);

        $exception = new FetchFailedException($expectedMessage);
        $exception->report();
    }

    #[Test]
    public function it_can_render_to_json_response(): void
    {
        $expectedMessage = 'Data fetching failed due to server error.';
        $exception = new FetchFailedException($expectedMessage);

        $response = $exception->render();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->status());

        $this->assertEquals([
            'error' => 'API Fetch Failed',
            'message' => $expectedMessage,
        ], $response->getData(true));
    }
}
