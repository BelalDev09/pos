<?php

namespace App\Services\Inventory;

use App\Events\StockTransferred;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Repositories\InventoryRepository;
use Illuminate\Support\Facades\DB;

class StockTransferService
{
    public function __construct(
        private readonly InventoryRepository $inventoryRepository
    ) {}

    public function create(array $data, int $userId, int $tenantId): StockTransfer
    {
        return DB::transaction(function () use ($data, $userId, $tenantId) {

            // Validate sufficient stock at source store
            foreach ($data['items'] as $item) {
                $this->inventoryRepository->findByProductAndStore(
                    $item['product_id'],
                    $data['from_store_id']
                ) ?? throw new \RuntimeException(
                    "No inventory found for product #{$item['product_id']} in source store."
                );
            }

            $transfer = StockTransfer::create([
                'tenant_id'       => $tenantId,
                'from_store_id'   => $data['from_store_id'],
                'to_store_id'     => $data['to_store_id'],
                'requested_by'    => $userId,
                'transfer_number' => $this->generateTransferNumber($tenantId),
                'status'          => 'pending',
                'notes'           => $data['notes'] ?? null,
                'requested_at'    => now(),
            ]);

            foreach ($data['items'] as $item) {
                StockTransferItem::create([
                    'stock_transfer_id'  => $transfer->id,
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'quantity_requested' => $item['quantity'],
                    'quantity_sent'      => 0,
                    'quantity_received'  => 0,
                ]);
            }

            return $transfer->load(['items.product', 'fromStore', 'toStore']);
        });
    }

    /**
     * Dispatch transfer — deducts stock from source store.
     */
    public function dispatch(int $transferId, int $userId): StockTransfer
    {
        return DB::transaction(function () use ($transferId, $userId) {
            $transfer = StockTransfer::with(['items.product', 'fromStore', 'toStore'])
                ->findOrFail($transferId);

            if ($transfer->status !== 'pending') {
                throw new \RuntimeException('Only pending transfers can be dispatched.');
            }

            foreach ($transfer->items as $item) {
                // Validate stock availability
                $inventory = $this->inventoryRepository
                    ->findByProductAndStore($item->product_id, $transfer->from_store_id);

                if (!$inventory || $inventory->quantity < $item->quantity_requested) {
                    throw new \RuntimeException(
                        "Insufficient stock for {$item->product->name} at source store."
                    );
                }

                // Deduct from source
                $this->inventoryRepository->adjustStock(
                    productId: $item->product_id,
                    storeId: $transfer->from_store_id,
                    delta: -(float) $item->quantity_requested,
                    type: 'transfer_out',
                    referenceNumber: $transfer->transfer_number,
                    userId: $userId,
                    tenantId: $transfer->tenant_id,
                    referenceType: StockTransfer::class,
                    referenceId: $transfer->id
                );

                $item->update(['quantity_sent' => $item->quantity_requested]);
            }

            $transfer->update([
                'status'        => 'in_transit',
                'dispatched_at' => now(),
            ]);

            return $transfer->fresh();
        });
    }

    /**
     * Receive transfer — adds stock to destination store.
     */
    public function receive(int $transferId, array $receivedItems, int $userId): StockTransfer
    {
        return DB::transaction(function () use ($transferId, $receivedItems, $userId) {
            $transfer = StockTransfer::with('items.product')->findOrFail($transferId);

            if ($transfer->status !== 'in_transit') {
                throw new \RuntimeException('Only in-transit transfers can be received.');
            }

            foreach ($receivedItems as $receivedItem) {
                $item = $transfer->items->find($receivedItem['transfer_item_id']);
                if (!$item) continue;

                $qtyReceived = (float) $receivedItem['quantity_received'];

                // Add to destination store
                $this->inventoryRepository->adjustStock(
                    productId: $item->product_id,
                    storeId: $transfer->to_store_id,
                    delta: $qtyReceived,
                    type: 'transfer_in',
                    referenceNumber: $transfer->transfer_number,
                    userId: $userId,
                    tenantId: $transfer->tenant_id,
                    referenceType: StockTransfer::class,
                    referenceId: $transfer->id
                );

                $item->update(['quantity_received' => $qtyReceived]);
            }

            $transfer->update([
                'status'      => 'received',
                'received_at' => now(),
            ]);

            event(new StockTransferred($transfer->fresh()));

            return $transfer->fresh(['items.product', 'fromStore', 'toStore']);
        });
    }

    private function generateTransferNumber(int $tenantId): string
    {
        $count = StockTransfer::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'TRF-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
