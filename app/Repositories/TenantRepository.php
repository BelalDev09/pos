<?php

namespace App\Repositories;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TenantRepository
{
    public function __construct(
        private readonly Tenant $model
    ) {}

    public function all(): Collection
    {
        return $this->model->with('headquarters')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            ->when(
                !empty($filters['search']),
                fn($q) =>
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%")
            )
            ->when(
                !empty($filters['plan']),
                fn($q) =>
                $q->where('plan', $filters['plan'])
            )
            ->when(
                isset($filters['is_active']),
                fn($q) =>
                $q->where('is_active', $filters['is_active'])
            )
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): Tenant
    {
        return $this->model->with(['stores', 'users'])->findOrFail($id);
    }

    public function findBySubdomain(string $subdomain): ?Tenant
    {
        return $this->model
            ->where('subdomain', $subdomain)
            ->where('is_active', true)
            ->first();
    }

    public function create(array $data): Tenant
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Tenant
    {
        $tenant = $this->model->findOrFail($id);
        $tenant->update($data);
        return $tenant->fresh();
    }

    public function delete(int $id): bool
    {
        return (bool) $this->model->findOrFail($id)->delete();
    }

    public function getWithStoreCount(): Collection
    {
        return $this->model
            ->withCount('stores')
            ->withCount('users')
            ->active()
            ->get();
    }
}
