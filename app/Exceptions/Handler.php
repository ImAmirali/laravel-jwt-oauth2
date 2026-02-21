<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use PDOException;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Handle database connection errors
        if ($exception instanceof PDOException) {
            // Log the error for debugging
            Log::error('Database connection failed: ' . $exception->getMessage());

            // Return a JSON response for API consumers
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Database connection failed',
                    'message' => 'Unable to connect to the database. Please try again later.',
                ], 503);
            }
        }

        return parent::render($request, $exception);

    }
}
