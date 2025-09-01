<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function Pest\Laravel\json;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
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
//        $columns = $this->parseSelectColumns($select);

        $query = Product::query();
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Update the specified resource in storage.
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

    public function categories(): JsonResponse
    {
        $categories = Category::select('slug', 'name', 'url')->get();
        return response()->json([$categories]);
    }
    /**
     * Return list of categories
     */
    public function categoryList(): JsonResponse
    {
        $categories = Category::pluck('name');
        return response()->json($categories);
    }

    /**
     * @param $category
     * Return products by category
     * @return JsonResponse
     */
    public function categoryProducts($category): JsonResponse
    {
        $validator = Validator::make($category, [
            'category' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ]);
        }

        $categoryId = Category::where('name', ucwords($category))->value('id');
        $products = Product::with(
            'owner',
            'brand',
            'category',
            'dimensions',
            'tags',
            'images',
            'reviews',
        )->where('category_id', $categoryId)->take(16)->get();
        return response()->json([
            'products' => ProductResource::collection($products),
            'total' => count($products),
            'skip' => 0,
            'limit' => 16,
        ]);
    }
    /**
     * For filtered products
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
