<?php

namespace App\Http\Controllers\Web\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Brand;
use App\Models\TaxRate;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $products = $this->productService->paginate(20, $request->only([
            'search',
            'category_id',
            'brand_id',
            'is_active',
            'product_type',
        ]));

        $categories = Category::active()->ordered()->get();
        $brands     = Brand::active()->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function create(): View
    {
        $categories = Category::active()->ordered()->get();
        $brands     = Brand::active()->get();
        $taxRates   = TaxRate::where('is_active', true)->get();

        return view('products.create', compact('categories', 'brands', 'taxRates'));
    }

    public function store(StoreProductRequest $request): JsonResponse|RedirectResponse
    {
        $product = $this->productService->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data'    => $product,
            ], 201);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(int $id): View
    {
        $product = $this->productService->findById($id);
        return view('products.show', compact('product'));
    }

    public function edit(int $id): View|JsonResponse
    {
        $product    = $this->productService->findById($id);
        $categories = Category::active()->ordered()->get();
        $brands     = Brand::active()->get();
        $taxRates   = TaxRate::where('is_active', true)->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $product->load(['variants', 'category', 'brand']),
            ]);
        }

        return view('products.edit', compact('product', 'categories', 'brands', 'taxRates'));
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $product = $this->productService->update($id, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data'    => $product,
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(int $id): JsonResponse|RedirectResponse
    {
        $this->productService->delete($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted.');
    }
}
