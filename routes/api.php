<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
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

/*
|--------------------------------------------------------------------------
| Product API Routes
|--------------------------------------------------------------------------
|
| All routes are grouped using the 'auth:api' middleware.
| These endpoints handle CRUD operations for the products.
| And get the categories and products in that category.
*/
Route::middleware('auth:api')->group(function () {
    /**
     * Get the Products
     * Method: GET
     * Endpoint: /api/products
     */
    Route::get('/products', [ProductController::class, 'index']);
    /**
     * Search by given name and return the products
     * Method: GET
     * Endpoint: /api/products/search
     */
    Route::get('/products/search', [ProductController::class, 'search']);
    /**
     * Get the list of categories
     * Method: GET
     * Endpoint: /api/products/category-list
     */
    Route::get('/products/category-list', [ProductController::class, 'categoryList']);
    /**
     * Get the categories
     * Method: GET
     * Endpoint: /api/products/categories
     */
    Route::get('/products/categories', [ProductController::class, 'categories']);
    /**
     * Get the Products by given category
     * Method: GET
     * Endpoint: /api/products/category/{Category_Name}
     */
    Route::get('/products/category/{category}', [ProductController::class, 'categoryProducts']);

    /**
     * Add a new product by given attributes
     * Method: POST
     * Endpoint: /api/products/add
     */
    Route::post('/products/add', [ProductController::class, 'store']);
    /**
     * Get the Product by given id
     * Method: GET
     * Endpoint: /api/products/{product_id}
     */
    Route::get('/products/{id}', [ProductController::class, 'show']);
    /**
     * update the product by given Product id
     * Method: PUT
     * Endpoint: /api/products/{product_id}
     */
    Route::put('/products/{id}', [ProductController::class, 'update']);
    /**
     * SoftDelete the Product by given id
     * Method: DELETE
     * Endpoint: /api/products/{product_id}
     */
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

});
