<?php

namespace App\Services\Inventory;

use App\Events\StockLow;
use App\Exceptions\InsufficientStockException;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use Illuminate\Database\Eloquent\Collection;

class InventoryService
{
    public function __construct(
        private readonly InventoryRepository $inventoryRepository
    ) {}

    public function getForStore(int $storeId, array $filters = []): Collection
    {
        return $this->inventoryRepository->getForStore($storeId, $filters);
    }

    /**
     * Assert that sufficient stock exists before allowing a sale.
     */
    public function assertStock(int $productId, int $storeId, float $quantity): void
    {
        $inventory = $this->inventoryRepository->findByProductAndStore($productId, $storeId);

        if (!$inventory) {
            throw new InsufficientStockException("No inventory record found for product #{$productId}.");
        }

        $product = $inventory->product;

        if (!$product->allow_negative_stock && $inventory->available_quantity < $quantity) {
            throw new InsufficientStockException(
                "Insufficient stock for {$product->name}. Available: {$inventory->available_quantity}, Requested: {$quantity}"
            );
        }
    }

    /**
     * Deduct stock after a confirmed sale.
     */
    public function deduct(
        int    $productId,
        int    $storeId,
        float  $quantity,
        string $reference,
        int    $userId,
        int    $tenantId,
        ?int   $orderId = null,
        ?float $unitCost = null
    ): Inventory {
        $inventory = $this->inventoryRepository->adjustStock(
            productId: $productId,
            storeId: $storeId,
            delta: -abs($quantity),
            type: 'sale',
            referenceNumber: $reference,
            userId: $userId,
            tenantId: $tenantId,
            referenceType: $orderId ? \App\Models\Order::class : null,
            referenceId: $orderId,
            unitCost: $unitCost
        );

        $this->checkAndFireLowStockAlert($inventory);

        return $inventory;
    }

    /**
     * Restock inventory (from purchase order, return, or manual adjustment).
     */
    public function restock(
        int    $productId,
        int    $storeId,
        float  $quantity,
        string $type,
        string $reference,
        int    $userId,
        int    $tenantId,
        ?float $unitCost = null,
        ?int   $referenceId = null,
        ?string $referenceType = null
    ): Inventory {
        return $this->inventoryRepository->adjustStock(
            productId: $productId,
            storeId: $storeId,
            delta: abs($quantity),
            type: $type,
            referenceNumber: $reference,
            userId: $userId,
            tenantId: $tenantId,
            referenceType: $referenceType,
            referenceId: $referenceId,
            unitCost: $unitCost
        );
    }

    /**
     * Manual stock adjustment (positive or negative).
     */
    public function adjust(
        int    $productId,
        int    $storeId,
        float  $newQuantity,
        string $reason,
        int    $userId,
        int    $tenantId
    ): Inventory {
        $inventory = $this->inventoryRepository->findOrCreateForProductStore(
            $productId,
            $storeId,
            $tenantId
        );

        $delta = $newQuantity - (float) $inventory->quantity;
        $type  = $delta >= 0 ? 'adjustment_in' : 'adjustment_out';

        $inventory = $this->inventoryRepository->adjustStock(
            productId: $productId,
            storeId: $storeId,
            delta: $delta,
            type: $type,
            referenceNumber: 'ADJ-' . now()->format('YmdHis'),
            userId: $userId,
            tenantId: $tenantId,
            notes: $reason
        );

        $this->checkAndFireLowStockAlert($inventory);

        return $inventory;
    }

    private function checkAndFireLowStockAlert(Inventory $inventory): void
    {
        if ($inventory->is_low_stock) {
            event(new StockLow($inventory->fresh(['product', 'store'])));
        }
    }
}
