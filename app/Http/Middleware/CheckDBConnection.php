<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;
use Symfony\Component\HttpFoundation\Response;

class CheckDBConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            DB::connection()->getPdo();
        } catch (QueryException | PDOException $e) {
            Log::error('Database connection failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Database Error',
                'message' => 'Unable to connect to the database. Please try again later.',
            ], 503);
        }

        return $next($request);
    }
}
