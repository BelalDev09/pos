<?php

namespace App\Services\Purchase;

use App\Models\PurchaseOrder;
use App\Repositories\PurchaseOrderRepository;
use App\Services\Inventory\InventoryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(
        private readonly PurchaseOrderRepository $poRepository,
        private readonly InventoryService        $inventoryService
    ) {}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->poRepository->paginate($perPage, $filters);
    }

    public function findById(int $id): PurchaseOrder
    {
        return $this->poRepository->findById($id);
    }

    public function create(array $data, int $userId, int $tenantId): PurchaseOrder
    {
        $items     = $data['items'] ?? [];
        $totals    = $this->calculateTotals($items);

        $poData = array_merge($data, [
            'created_by' => $userId,
            'tenant_id'  => $tenantId,
            'po_number'  => $this->poRepository->generatePoNumber($tenantId),
            'status'     => 'draft',
        ], $totals);

        return $this->poRepository->create($poData, $items);
    }

    public function update(int $id, array $data): PurchaseOrder
    {
        $po = $this->poRepository->findById($id);

        if (!in_array($po->status, ['draft', 'pending'])) {
            throw new \RuntimeException('Only draft or pending POs can be edited.');
        }

        $items  = $data['items'] ?? null;
        $totals = $items ? $this->calculateTotals($items) : [];

        return $this->poRepository->update($id, array_merge($data, $totals), $items);
    }

    public function approve(int $id, int $approverId): PurchaseOrder
    {
        $po = $this->poRepository->findById($id);

        if ($po->status !== 'pending') {
            throw new \RuntimeException('Only pending POs can be approved.');
        }

        return $this->poRepository->update($id, [
            'status'      => 'approved',
            'approved_by' => $approverId,
        ]);
    }

    /**
     * Receive goods — updates inventory for each received item.
     * Supports partial receiving.
     */
    public function receiveGoods(int $id, array $receivedItems, int $userId): PurchaseOrder
    {
        return DB::transaction(function () use ($id, $receivedItems, $userId) {
            $po = $this->poRepository->findById($id);

            if (!in_array($po->status, ['approved', 'ordered', 'partial'])) {
                throw new \RuntimeException('This PO cannot receive goods in its current status.');
            }

            foreach ($receivedItems as $receivedItem) {
                $poItem = $po->items->find($receivedItem['purchase_order_item_id']);

                if (!$poItem) {
                    continue;
                }

                $qtyReceived = (float) $receivedItem['quantity_received'];
                $remaining   = $poItem->remaining_quantity;

                if ($qtyReceived > $remaining) {
                    throw new \InvalidArgumentException(
                        "Cannot receive {$qtyReceived} units of {$poItem->product->name}. Only {$remaining} remaining."
                    );
                }

                // Update item received qty
                $poItem->increment('quantity_received', $qtyReceived);

                // Restock inventory
                $this->inventoryService->restock(
                    productId: $poItem->product_id,
                    storeId: $po->store_id,
                    quantity: $qtyReceived,
                    type: 'purchase',
                    reference: $po->po_number,
                    userId: $userId,
                    tenantId: $po->tenant_id,
                    unitCost: (float) $poItem->unit_cost,
                    referenceId: $po->id,
                    referenceType: PurchaseOrder::class
                );
            }

            // Update PO status
            $po->refresh();
            $newStatus = $po->is_fully_received ? 'received' : 'partial';

            $this->poRepository->update($id, [
                'status'      => $newStatus,
                'received_at' => $newStatus === 'received' ? now() : null,
            ]);

            return $po->fresh(['items.product', 'supplier']);
        });
    }

    public function delete(int $id): bool
    {
        return $this->poRepository->delete($id);
    }

    private function calculateTotals(array $items): array
    {
        $subtotal = 0.0;
        $tax      = 0.0;

        foreach ($items as &$item) {
            $lineSubtotal = (float) $item['quantity_ordered'] * (float) $item['unit_cost'];
            $lineTax      = $lineSubtotal * ((float) ($item['tax_rate'] ?? 0) / 100);
            $lineDiscount = $lineSubtotal * ((float) ($item['discount_rate'] ?? 0) / 100);
            $lineTotal    = $lineSubtotal + $lineTax - $lineDiscount;

            $item['tax_amount']      = round($lineTax, 4);
            $item['discount_amount'] = round($lineDiscount, 4);
            $item['total']           = round($lineTotal, 4);

            $subtotal += $lineSubtotal;
            $tax      += $lineTax;
        }

        return [
            'subtotal'   => round($subtotal, 4),
            'tax_amount' => round($tax, 4),
            'total'      => round($subtotal + $tax, 4),
        ];
    }
}
