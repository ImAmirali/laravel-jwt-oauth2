<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
|
| All authentication-related routes are grouped under the 'auth' prefix
| and use the 'api' middleware group (which includes JSON response formatting,
| throttling, etc.). These endpoints handle registration, login, logout,
| token refresh, and retrieving the authenticated user's info.
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    /**
     * Register a new user.
     * Method: POST
     * Endpoint: /api/auth/register
     * Access: Public
     */
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    /**
     * Log in an existing user and receive access & refresh tokens.
     * Method: POST
     * Endpoint: /api/auth/login
     * Access: Public
     */
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    /**
     * Log out the authenticated user.
     * Invalidates the access token and removes the refresh token cookie.
     * Method: POST
     * Endpoint: /api/auth/logout
     * Access: Authenticated (auth:api)
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * Refresh access token using a valid refresh token (stored in cookie).
     * Returns a new access token and refresh token.
     * Method: POST
     * Endpoint: /api/auth/refresh
     * Access: Authenticated (auth:api)
     */
    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->middleware(['auth:api', EnsureTokenIsValid::class.':refresh'])->name('refresh');

    /**
     * Get the currently authenticated user's data.
     * Method: GET
     * Endpoint: /api/auth/user
     * Access: Authenticated (auth:api)
     */
    Route::get('/user', [AuthController::class, 'user'])
        ->middleware(['auth:api',EnsureTokenIsValid::class.':access'])->name('user');
});
