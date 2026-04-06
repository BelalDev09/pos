<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class InventoryRepository
{
    public function __construct(
        private readonly Inventory $model
    ) {}

    public function getForStore(int $storeId, array $filters = []): Collection
    {
        return $this->model
            ->with(['product.category', 'product.brand'])
            ->where('store_id', $storeId)
            ->when(!empty($filters['low_stock']), fn($q) => $q->lowStock())
            ->when(!empty($filters['out_of_stock']), fn($q) => $q->outOfStock())
            ->when(
                !empty($filters['search']),
                fn($q) =>
                $q->whereHas(
                    'product',
                    fn($p) =>
                    $p->where('name', 'like', "%{$filters['search']}%")
                        ->orWhere('sku', 'like', "%{$filters['search']}%")
                )
            )
            ->get();
    }

    public function findByProductAndStore(int $productId, int $storeId): ?Inventory
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('store_id', $storeId)
            ->first();
    }

    public function findOrCreateForProductStore(int $productId, int $storeId, int $tenantId): Inventory
    {
        return $this->model->firstOrCreate(
            ['product_id' => $productId, 'store_id' => $storeId],
            ['tenant_id' => $tenantId, 'quantity' => 0, 'updated_at' => now()]
        );
    }

    /**
     * Atomically adjusts stock using a database-level update with pessimistic lock.
     */
    public function adjustStock(
        int     $productId,
        int     $storeId,
        float   $delta,
        string  $type,
        string  $referenceNumber,
        int     $userId,
        int     $tenantId,
        ?string $notes = null,
        ?string $referenceType = null,
        ?int    $referenceId = null,
        ?float  $unitCost = null
    ): Inventory {
        return DB::transaction(function () use (
            $productId,
            $storeId,
            $delta,
            $type,
            $referenceNumber,
            $userId,
            $tenantId,
            $notes,
            $referenceType,
            $referenceId,
            $unitCost
        ) {
            $inventory = $this->model
                ->where('product_id', $productId)
                ->where('store_id', $storeId)
                ->lockForUpdate()
                ->firstOrFail();

            $before = (float) $inventory->quantity;
            $after  = $before + $delta;

            $inventory->update([
                'quantity'   => $after,
                'updated_at' => now(),
            ]);

            StockMovement::create([
                'product_id'       => $productId,
                'store_id'         => $storeId,
                'tenant_id'        => $tenantId,
                'created_by'       => $userId,
                'type'             => $type,
                'quantity'         => $delta,
                'quantity_before'  => $before,
                'quantity_after'   => $after,
                'unit_cost'        => $unitCost,
                'reference_type'   => $referenceType,
                'reference_id'     => $referenceId,
                'reference_number' => $referenceNumber,
                'notes'            => $notes,
                'moved_at'         => now(),
                'created_at'       => now(),
            ]);

            return $inventory->fresh();
        });
    }

    public function getLowStockItems(int $tenantId): Collection
    {
        return $this->model
            ->with(['product', 'store'])
            ->where('tenant_id', $tenantId)
            ->lowStock()
            ->get();
    }
}
