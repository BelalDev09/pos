<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PurchaseOrderRepository
{
    public function __construct(
        private readonly PurchaseOrder $model
    ) {}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->with(['supplier', 'store', 'createdBy'])
            ->when(
                !empty($filters['search']),
                fn($q) =>
                $q->where('po_number', 'like', "%{$filters['search']}%")
                    ->orWhereHas(
                        'supplier',
                        fn($s) =>
                        $s->where('name', 'like', "%{$filters['search']}%")
                    )
            )
            ->when(
                !empty($filters['status']),
                fn($q) =>
                $q->where('status', $filters['status'])
            )
            ->when(
                !empty($filters['store_id']),
                fn($q) =>
                $q->where('store_id', $filters['store_id'])
            )
            ->when(
                !empty($filters['supplier_id']),
                fn($q) =>
                $q->where('supplier_id', $filters['supplier_id'])
            )
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): PurchaseOrder
    {
        return $this->model
            ->with(['items.product', 'items.variant', 'supplier', 'store', 'createdBy', 'approvedBy'])
            ->findOrFail($id);
    }

    public function create(array $data, array $items): PurchaseOrder
    {
        $po = $this->model->create($data);

        foreach ($items as $item) {
            $po->items()->create($item);
        }

        return $po->fresh(['items.product', 'supplier', 'store']);
    }

    public function update(int $id, array $data, ?array $items = null): PurchaseOrder
    {
        $po = $this->model->findOrFail($id);
        $po->update($data);

        if ($items !== null) {
            $po->items()->delete();
            foreach ($items as $item) {
                $po->items()->create($item);
            }
        }

        return $po->fresh(['items.product', 'supplier', 'store']);
    }

    public function delete(int $id): bool
    {
        $po = $this->model->findOrFail($id);

        if (!in_array($po->status, ['draft', 'cancelled'])) {
            throw new \RuntimeException('Only draft or cancelled purchase orders can be deleted.');
        }

        return (bool) $po->delete();
    }

    public function generatePoNumber(int $tenantId): string
    {
        $count = $this->model
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->count() + 1;

        return 'PO-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
