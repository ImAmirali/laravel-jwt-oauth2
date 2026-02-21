<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureTokenIsValid;
use Illuminate\Support\Facades\Route;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel API Documentation",
 *     description="API for user auth and product management."
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Schema(
 *     schema="Error",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(property="errors", type="object")
 * )
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com")
 * )
 * @OA\Schema(
 *     schema="LoginRequest",
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="password", type="string", example="password123"),
 *     required={"email", "password"}
 * )
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="password", type="string", example="password123"),
 *     required={"name", "email", "password"}
 * )
 * @OA\Schema(
 *     schema="AuthResponse",
 *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
 *     @OA\Property(property="token_type", type="string", example="bearer"),
 *     @OA\Property(property="expires_in", type="integer", example=3600),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample Product"),
 *     @OA\Property(property="description", type="string", example="Description"),
 *     @OA\Property(property="price", type="number", example=29.99),
 *     @OA\Property(property="category", type="string", example="Electronics")
 * )
 * @OA\Schema(
 *     schema="ProductRequest",
 *     @OA\Property(property="name", type="string", example="Sample Product"),
 *     @OA\Property(property="description", type="string", example="Description"),
 *     @OA\Property(property="price", type="number", example=29.99),
 *     @OA\Property(property="category", type="string", example="Electronics"),
 *     required={"name", "price", "category"}
 * )
 * @OA\Schema(
 *     schema="ProductListResponse",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="total", type="integer", example=50)
 * )
 * @OA\Schema(
 *     schema="Category",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Electronics")
 * )
 * @OA\Schema(
 *     schema="CategoryListResponse",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
 * )
 */

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/RegisterRequest")),
     *     @OA\Response(response=201, description="Success", @OA\JsonContent(ref="#/components/schemas/AuthResponse")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/LoginRequest")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/AuthResponse")),
     *     @OA\Response(response=401, description="Invalid credentials", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(@OA\Property(property="message", type="string", example="Logged out"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh token",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/AuthResponse")),
     *     @OA\Response(response=401, description="Invalid token", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->middleware(['api', EnsureTokenIsValid::class.':refresh'])->name('refresh');

    /**
     * @OA\Get(
     *     path="/api/auth/user",
     *     summary="Get authenticated user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/User")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::get('/user', [AuthController::class, 'user'])
        ->middleware(['auth:api',EnsureTokenIsValid::class.':access'])->name('user');
});

/*
|--------------------------------------------------------------------------
| Product API Routes
|--------------------------------------------------------------------------
*/
Route::middleware('api')->group(function () {
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="List products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", default=1)),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/ProductListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::get('/products', [ProductController::class, 'index']);

    /**
     * @OA\Get(
     *     path="/api/products/search",
     *     summary="Search products",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="q", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/ProductListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::get('/products/search', [ProductController::class, 'search']);

    /**
     * @OA\Get(
     *     path="/api/category-list",
     *     summary="List categories",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/CategoryListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::get('/category-list', [ProductController::class, 'categoryList']);

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get categories",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/CategoryListResponse")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::get('/categories', [ProductController::class, 'categories']);

    /**
     * @OA\Get(
     *     path="/api/category/{category}",
     *     summary="Products by category",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="string", example="Electronics")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/ProductListResponse")),
     *     @OA\Response(response=404, description="Not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::get('/category/{category}', [ProductController::class, 'categoryProducts']);

    /**
     * @OA\Post(
     *     path="/api/products/add",
     *     summary="Create product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProductRequest")),
     *     @OA\Response(response=201, description="Success", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::post('/products/add', [ProductController::class, 'store']);

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get product by ID",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=404, description="Not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::get('/products/{id}', [ProductController::class, 'show']);

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ProductRequest")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(ref="#/components/schemas/Product")),
     *     @OA\Response(response=404, description="Not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::put('/products/{id}', [ProductController::class, 'update']);

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(@OA\Property(property="message", type="string", example="Deleted"))),
     *     @OA\Response(response=404, description="Not found", @OA\JsonContent(ref="#/components/schemas/Error")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Error"))
     * )
     */
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});
