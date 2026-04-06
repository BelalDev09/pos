<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage, $filters);
    }

    public function findById(int $id): Product
    {
        return $this->productRepository->findById($id);
    }

    public function findByBarcode(string $barcode): ?Product
    {
        return $this->productRepository->findByBarcode($barcode);
    }

    public function getPosGrid(int $storeId, ?int $categoryId = null): array
    {
        return $this->productRepository
            ->getPosGrid($storeId, $categoryId)
            ->toArray();
    }

    public function searchForPos(string $term, int $storeId): array
    {
        return $this->productRepository
            ->searchForPos($term, $storeId)
            ->toArray();
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            // Handle image upload
            if (!empty($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $data['image']->store('products', 'public');
            }

            $product = $this->productRepository->create($data);

            // Create default variant if product has variants
            if (!empty($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            return $product;
        });
    }

    public function update(int $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            if (isset($data['name'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
            }

            if (!empty($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $existing = $this->productRepository->findById($id);
                if ($existing->image) {
                    Storage::disk('public')->delete($existing->image);
                }
                $data['image'] = $data['image']->store('products', 'public');
            }

            $product = $this->productRepository->update($id, $data);

            if (isset($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            // Bust POS cache for all stores
            $product->inventories->each(
                fn($inv) =>
                $this->productRepository->bustPosCache($inv->store_id)
            );

            return $product;
        });
    }

    public function delete(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $existingIds = [];

        foreach ($variants as $variantData) {
            if (!empty($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
                $variant?->update($variantData);
                $existingIds[] = $variantData['id'];
            } else {
                $variant = $product->variants()->create($variantData);
                $existingIds[] = $variant->id;
            }
        }

        // Remove variants not in the updated list
        $product->variants()->whereNotIn('id', $existingIds)->delete();

        // Ensure at least one default variant
        if (!$product->variants()->where('is_default', true)->exists()) {
            $product->variants()->first()?->update(['is_default' => true]);
        }
    }

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug  = Str::slug($name);
        $count = 0;

        do {
            $testSlug = $count > 0 ? "{$slug}-{$count}" : $slug;
            $exists   = Product::withoutTenantScope()
                ->where('slug', $testSlug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists();
            $count++;
        } while ($exists);

        return $testSlug;
    }
}
