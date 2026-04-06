<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use App\Models\Store;
use App\Models\User;
use App\Repositories\TenantRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantService
{
    public function __construct(
        private readonly TenantRepository $tenantRepository
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->tenantRepository->paginate($perPage, $filters);
    }

    public function findById(int $id): Tenant
    {
        return $this->tenantRepository->findById($id);
    }

    /**
     * Provisions a complete new tenant: tenant record + HQ store + owner user.
     */
    public function provision(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            // 1. Create tenant
            $tenant = $this->tenantRepository->create([
                'name'          => $data['name'],
                'slug'          => Str::slug($data['name']),
                'subdomain'     => $data['subdomain'],
                'plan'          => $data['plan'] ?? 'trial',
                'trial_ends_at' => now()->addDays(14),
                'business_type' => $data['business_type'] ?? 'retail',
                'email'         => $data['email'],
                'phone'         => $data['phone'] ?? null,
                'country'       => $data['country'] ?? 'US',
                'timezone'      => $data['timezone'] ?? 'UTC',
                'currency'      => $data['currency'] ?? 'USD',
                'is_active'     => true,
            ]);

            // 2. Create headquarters store
            $store = Store::create([
                'tenant_id'        => $tenant->id,
                'name'             => $data['name'] . ' — HQ',
                'code'             => 'HQ',
                'currency'         => $data['currency'] ?? 'USD',
                'timezone'         => $data['timezone'] ?? 'UTC',
                'is_headquarters'  => true,
                'is_active'        => true,
            ]);

            // 3. Create tenant owner user
            $user = User::create([
                'tenant_id'   => $tenant->id,
                'store_id'    => $store->id,
                'name'        => $data['owner_name'],
                'email'       => $data['owner_email'],
                'password'    => Hash::make($data['owner_password']),
                'is_active'   => true,
            ]);

            $user->assignRole('tenant_owner');

            return $tenant;
        });
    }

    public function update(int $id, array $data): Tenant
    {
        return $this->tenantRepository->update($id, $data);
    }

    public function toggleStatus(int $id): Tenant
    {
        $tenant = $this->tenantRepository->findById($id);
        return $this->tenantRepository->update($id, [
            'is_active' => !$tenant->is_active,
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->tenantRepository->delete($id);
    }
}
