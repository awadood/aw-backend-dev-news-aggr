<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *     schema="FetchFailedException",
 *     type="object",
 *     title="FetchFailedException",
 *     description="Exception thrown when there is an error fetching data from NewsAPI",
 * )
 */
class FetchFailedException extends Exception
{
    /**
     * FetchFailedException constructor.
     *
     * @param  string  $message
     * @param  int  $code
     */
    public function __construct($message = 'Failed to fetch data from API.', $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     *
     * This method can be used to log the exception or perform any additional reporting actions.
     */
    public function report(): void
    {
        // Log the exception or send it to an external error tracking service
        Log::error($this->getMessage());
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'error' => 'API Fetch Failed',
            'message' => $this->getMessage(),
        ], 500);
    }
}
