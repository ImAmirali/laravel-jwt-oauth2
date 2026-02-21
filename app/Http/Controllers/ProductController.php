<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function Pest\Laravel\json;

/**
 * @OA\Info(
 *     title="Laravel Product API",
 *     version="1.0.0",
 *     description="API documentation for managing products and categories in a Laravel application"
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local development server"
 * )
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Sample Product"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="brand_id", type="integer", example=1),
 *     @OA\Property(property="description", type="string", example="A sample product description"),
 *     @OA\Property(property="price", type="number", format="float", example=29.99),
 *     @OA\Property(property="stock", type="integer", example=100),
 *     @OA\Property(property="sku", type="string", example="AB12CD34"),
 *     @OA\Property(property="owner", type="object", description="User who owns the product"),
 *     @OA\Property(property="brand", type="object", description="Brand of the product"),
 *     @OA\Property(property="category", type="object", description="Category of the product"),
 *     @OA\Property(property="dimensions", type="object", description="Product dimensions"),
 *     @OA\Property(property="tags", type="array", @OA\Items(type="object", description="Product tags")),
 *     @OA\Property(property="images", type="array", @OA\Items(type="object", description="Product images")),
 *     @OA\Property(property="reviews", type="array", @OA\Items(type="object", description="Product reviews"))
 * )
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="slug", type="string", example="electronics"),
 *     @OA\Property(property="name", type="string", example="Electronics"),
 *     @OA\Property(property="url", type="string", example="http://localhost:8000/categories/electronics")
 * )
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     @OA\Property(
 *         property="error",
 *         type="object",
 *         description="Validation error details",
 *         example={"field_name": "The field is required."}
 *     )
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Retrieve a list of products",
     *     description="Returns a paginated list of products with optional filtering and sorting.",
     *     operationId="getProducts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of products to return per page (1-100)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, default=30)
     *     ),
     *     @OA\Parameter(
     *         name="skip",
     *         in="query",
     *         description="Number of products to skip for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=0, default=0)
     *     ),
     *     @OA\Parameter(
     *         name="select",
     *         in="query",
     *         description="Comma-separated list of fields to include in response",
     *         required=false,
     *         @OA\Schema(type="string", example="id,title,price")
     *     ),
     *     @OA\Parameter(
     *         name="sortBy",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *         @OA\Schema(type="string", enum={"id", "title", "price", "created_at"}, default="id")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, default="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="total", type="integer", description="Total number of products"),
     *             @OA\Property(property="skip", type="integer", description="Number of products skipped"),
     *             @OA\Property(property="limit", type="integer", description="Number of products per page")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'integer|min:1|max:100',
            'skip' => 'integer|min:0',
            'select' => 'string',
            'sortBy' => 'string|in:id,title,price,created_at',
            'order' => 'string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }
        $limit = (int)$request->get('limit', 30);
        $skip = (int)$request->get('skip', 0);
        $select = $request->get('select', '*');
        $sortBy = $request->get('sortBy', 'id');
        $order = $request->get('order', 'asc');
        $columns = $select === '*' ? ['*'] : explode(',', $select);

        if ($select !== '*') {
            array_unshift($columns, 'id', 'title');
            $products = Product::select($columns)->skip($skip)->take($limit)
                ->orderBy($sortBy, $order)->get();
        }
        else{
            $products = Product::with([
                'owner',
                'brand',
                'category',
                'dimensions',
                'tags',
                'images',
                'reviews',
            ])->skip($skip)->take($limit)->orderBy($sortBy, $order)->get();
        }

        if ($select !== ['*'])
        {
            return response()->json([
                'products' => $products,
                'total' => Product::count(),
                'skip' => $skip,
                'limit' => $limit,
            ]);
        }
        return response()->json([
            'products' => ProductResource::collection($products),
            'total' => count($products),
            'skip' => $skip,
            'limit' => $limit,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     *     description="Creates a new product with the provided data.",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","user_id","category_id","brand_id","description","price","stock"},
     *             @OA\Property(property="title", type="string", example="Sample Product"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="brand_id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", example="A sample product description"),
     *             @OA\Property(property="price", type="number", format="float", example=29.99),
     *             @OA\Property(property="stock", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'user_id' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }
        $product = Product::query()->create([
            'title' => $request->get('title'),
            'user_id' => $request->get('user_id'),
            'category_id' => $request->get('category_id'),
            'brand_id' => $request->get('brand_id'),
            'description' => $request->get('description'),
            'price' => $request->get('price'),
            'stock' => $request->get('stock'),
            'sku' => strtoupper(fake()->unique()->bothify('??##??##')),
        ]);

        return response()->json($product);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Retrieve a specific product",
     *     description="Returns details of a product by ID, including related data (brand, category, etc.).",
     *     operationId="getProductById",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="product", ref="#/components/schemas/Product")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function show($id)
    {
        $product = Product::with([
            'brand',
            'category',
            'dimensions',
            'tags',
            'images',
            'reviews',
            'owner'
        ])->findOrFail($id);

        return response()->json([
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Update a product",
     *     description="Updates an existing product by ID.",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Product"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="price", type="integer", example=39.99),
     *             @OA\Property(property="weight", type="integer", example=500),
     *             @OA\Property(property="discount_percentage", type="integer", minimum=0, maximum=100, example=10),
     *             @OA\Property(property="stock", type="integer", minimum=0, example=50),
     *             @OA\Property(property="warranty_information", type="string", example="1 year warranty"),
     *             @OA\Property(property="shipping_information", type="string", example="Ships in 2-3 days"),
     *             @OA\Property(property="availability_status", type="string", example="In stock"),
     *             @OA\Property(property="return_policy", type="string", example="30-day return"),
     *             @OA\Property(property="minimum_order_quantity", type="integer", example=1),
     *             @OA\Property(property="barcode", type="string", example="123456789012"),
     *             @OA\Property(property="qr_code", type="string", example="http://example.com/qr"),
     *             @OA\Property(property="thumbnail", type="string", example="http://example.com/thumbnail.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'description' => 'string',
            'price' => 'integer',
            'weight' => 'integer',
            'discount_percentage' => 'integer|min:0|max:100',
            'stock' => 'integer|min:0',
            'warranty_information' => 'string',
            'shipping_information' => 'string',
            'availability_status' => 'string',
            'return_policy' => 'string',
            'minimum_order_quantity' => 'integer',
            'barcode' => 'string',
            'qr_code' => 'string',
            'thumbnail' => 'string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $product = Product::find($id);
        $product->update($request->all());
        return response()->json($product);
    }

    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Retrieve a list of categories",
     *     description="Returns a list of categories with slug, name, and URL.",
     *     operationId="getCategories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             )
     *         )
     *     )
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = Category::select('slug', 'name', 'url')->get();
        return response()->json([$categories]);
    }

    /**
     * @OA\Get(
     *     path="/categories/list",
     *     summary="Retrieve a list of category names",
     *     description="Returns a list of category names.",
     *     operationId="getCategoryList",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="Electronics")
     *         )
     *     )
     * )
     */
    public function categoryList(): JsonResponse
    {
        $categories = Category::pluck('name');
        return response()->json($categories);
    }

    /**
     * @OA\Get(
     *     path="/category/{category}",
     *     summary="Retrieve products by category",
     *     description="Returns a list of products for a specific category.",
     *     operationId="getCategoryProducts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="Name of the category",
     *         required=true,
     *         @OA\Schema(type="string", example="electronics")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="total", type="integer", description="Total number of products"),
     *             @OA\Property(property="skip", type="integer", description="Number of products skipped"),
     *             @OA\Property(property="limit", type="integer", description="Number of products per page")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function categoryProducts($category): JsonResponse
    {
        $validator = Validator::make(['category' => $category], [
            'category' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $categoryId = Category::where('name', ucwords($category))->value('id');
        $products = Product::with([
            'owner',
            'brand',
            'category',
            'dimensions',
            'tags',
            'images',
            'reviews',
        ])->where('category_id', $categoryId)->take(16)->get();
        return response()->json([
            'products' => ProductResource::collection($products),
            'total' => count($products),
            'skip' => 0,
            'limit' => 16,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/products/search",
     *     summary="Search products",
     *     description="Returns a list of products matching the search query.",
     *     operationId="searchProducts",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for product titles",
     *         required=true,
     *         @OA\Schema(type="string", example="laptop")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="products", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="total", type="integer", description="Total number of products"),
     *             @OA\Property(property="skip", type="integer", description="Number of products skipped"),
     *             @OA\Property(property="limit", type="integer", description="Number of products per page")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $q = $request->get('q');
        $products = Product::where('title', 'LIKE', '%' . $q . '%')->get();
        $total = count($products);
        return response()->json([
            'products' => ProductResource::collection($products),
            'total' => $total,
            'skip' => 0,
            'limit' => $total,
        ]);
    }
}
