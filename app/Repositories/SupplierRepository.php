<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SupplierRepository
{
    public function __construct(
        private readonly Supplier $model
    ) {}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->when(
                !empty($filters['search']),
                fn($q) =>
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('company_name', 'like', "%{$filters['search']}%")
                    ->orWhere('phone', 'like', "%{$filters['search']}%")
            )
            ->when(
                isset($filters['is_active']),
                fn($q) =>
                $q->where('is_active', $filters['is_active'])
            )
            ->withCount('purchaseOrders')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): Supplier
    {
        return $this->model
            ->with(['purchaseOrders' => fn($q) => $q->latest()->limit(5)])
            ->withCount('purchaseOrders')
            ->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->model->active()->orderBy('name')->get(['id', 'name', 'company_name']);
    }

    public function create(array $data): Supplier
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Supplier
    {
        $supplier = $this->model->findOrFail($id);
        $supplier->update($data);
        return $supplier->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->findOrFail($id)->delete();
    }
}
