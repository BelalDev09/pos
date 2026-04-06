<?php

namespace App\Http\Controllers\Api\V1\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $products = $this->productService->paginate(20, $request->only([
            'search',
            'category_id',
            'brand_id',
            'is_active',
        ]));

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product created.',
            'data'    => new ProductResource($product),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product),
        ]);
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Product updated.',
            'data'    => new ProductResource($product),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted.',
        ]);
    }

    public function findByBarcode(string $barcode): JsonResponse
    {
        $product = $this->productService->findByBarcode($barcode);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => new ProductResource($product),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:1', 'store_id' => 'required|integer']);

        $results = $this->productService->searchForPos(
            $request->q,
            $request->store_id
        );

        return response()->json(['success' => true, 'data' => $results]);
    }
}
