<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository
{
    public function __construct(
        private readonly Product $model
    ) {}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with(['category', 'brand', 'taxRate'])
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $q->where(function ($inner) use ($filters) {
                    $inner->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('sku', 'like', "%{$filters['search']}%")
                        ->orWhere('barcode', 'like', "%{$filters['search']}%");
                });
            })
            ->when(
                !empty($filters['category_id']),
                fn($q) =>
                $q->where('category_id', $filters['category_id'])
            )
            ->when(
                !empty($filters['brand_id']),
                fn($q) =>
                $q->where('brand_id', $filters['brand_id'])
            )
            ->when(
                isset($filters['is_active']),
                fn($q) =>
                $q->where('is_active', $filters['is_active'])
            )
            ->when(
                !empty($filters['product_type']),
                fn($q) =>
                $q->where('product_type', $filters['product_type'])
            )
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(int $id): Product
    {
        return $this->model
            ->with(['category', 'brand', 'taxRate', 'variants', 'inventories'])
            ->findOrFail($id);
    }

    public function findByBarcode(string $barcode): ?Product
    {
        return $this->model
            ->with(['defaultVariant', 'taxRate', 'variants'])
            ->where(function ($q) use ($barcode) {
                $q->where('barcode', $barcode)
                    ->orWhereHas(
                        'variants',
                        fn($v) =>
                        $v->where('barcode', $barcode)
                    );
            })
            ->where('is_active', true)
            ->first();
    }

    public function searchForPos(string $term, int $storeId): Collection
    {
        return Cache::remember(
            "pos_search_{$term}_{$storeId}",
            30,
            fn() => $this->model
                ->with(['defaultVariant', 'taxRate'])
                ->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('sku', 'like', "%{$term}%")
                        ->orWhere('barcode', 'like', "%{$term}%");
                })
                ->where('is_active', true)
                ->where('is_pos_visible', true)
                ->limit(20)
                ->get()
        );
    }

    public function getPosGrid(int $storeId, ?int $categoryId = null): Collection
    {
        $cacheKey = "pos_grid_{$storeId}_{$categoryId}";

        return Cache::remember(
            $cacheKey,
            300,
            fn() =>
            $this->model
                ->with(['defaultVariant', 'taxRate', 'category'])
                ->withSum([
                    'inventories as stock_qty' => fn($q) =>
                    $q->where('store_id', $storeId)
                ], 'quantity')
                ->where('is_active', true)
                ->where('is_pos_visible', true)
                ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
        );
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = $this->model->findOrFail($id);
        $product->update($data);
        return $product->fresh(['category', 'brand', 'taxRate', 'variants']);
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->findOrFail($id)->delete();
    }

    /**
     * Invalidate the POS grid cache for a store when products change.
     */
    public function bustPosCache(int $storeId): void
    {
        Cache::forget("pos_grid_{$storeId}_");
        // Also bust category-specific caches
        Category::where('tenant_id', auth()->user()->tenant_id)
            ->pluck('id')
            ->each(fn($catId) => Cache::forget("pos_grid_{$storeId}_{$catId}"));
    }
}
