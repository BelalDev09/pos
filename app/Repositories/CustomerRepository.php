<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository
{
    public function __construct(
        private readonly Customer $model
    ) {}

    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->when(
                !empty($filters['search']),
                fn($q) =>
                $q->search($filters['search'])
            )
            ->when(
                !empty($filters['tier']),
                fn($q) =>
                $q->where('tier', $filters['tier'])
            )
            ->when(
                isset($filters['is_active']),
                fn($q) =>
                $q->where('is_active', $filters['is_active'])
            )
            ->where('is_walk_in', false)
            ->withCount('orders')
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): Customer
    {
        return $this->model
            ->with(['orders' => fn($q) => $q->latest()->limit(10)])
            ->findOrFail($id);
    }

    public function findByPhone(string $phone): ?Customer
    {
        return $this->model
            ->where('phone', $phone)
            ->first();
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }

    public function searchForPos(string $term): Collection
    {
        return $this->model
            ->search($term)
            ->active()
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email', 'loyalty_points', 'tier']);
    }

    public function getWalkInCustomer(int $tenantId): Customer
    {
        return $this->model
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('is_walk_in', true)
            ->firstOrFail();
    }

    public function create(array $data): Customer
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Customer
    {
        $customer = $this->model->findOrFail($id);
        $customer->update($data);
        return $customer->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->findOrFail($id)->delete();
    }

    public function adjustLoyaltyPoints(int $id, float $points, string $operation = 'add'): Customer
    {
        $customer = $this->model->findOrFail($id);

        match ($operation) {
            'add'    => $customer->increment('loyalty_points', abs($points)),
            'deduct' => $customer->decrement('loyalty_points', abs($points)),
            default  => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
        };

        return $customer->fresh();
    }
}
