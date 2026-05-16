<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->firstOrFail();
        $mainStore = Store::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('id')
            ->first();

        $superAdmin = User::query()->updateOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Platform Admin',
            'password' => Hash::make('12345678'),
            'is_super_admin' => true,
            'is_active' => true,
        ]);

        $owner = User::query()->updateOrCreate([
            'email' => 'owner@gmail.com',
        ], [
            'tenant_id' => $tenant->id,
            'store_id' => $mainStore?->id,
            'name' => 'John Owner',
            'password' => Hash::make('12345678'),
            'is_active' => true,
        ]);

        $admin = User::query()->updateOrCreate([
            'email' => 'admin.tenant@gmail.com',
        ], [
            'tenant_id' => $tenant->id,
            'store_id' => $mainStore?->id,
            'name' => 'Tenant Admin',
            'password' => Hash::make('12345678'),
            'is_active' => true,
        ]);

        $storeManager = User::query()->updateOrCreate([
            'email' => 'manager@gmail.com',
        ], [
            'tenant_id' => $tenant->id,
            'store_id' => $mainStore?->id,
            'name' => 'Store Manager',
            'password' => Hash::make('12345678'),
            'is_active' => true,
        ]);

        $cashier = User::query()->updateOrCreate([
            'email' => 'cashier@gmail.com',
        ], [
            'tenant_id' => $tenant->id,
            'store_id' => $mainStore?->id,
            'name' => 'Jane Cashier',
            'password' => Hash::make('12345678'),
            'is_active' => true,
        ]);

        $inventoryManager = User::query()->updateOrCreate([
            'email' => 'inventory@gmail.com',
        ], [
            'tenant_id' => $tenant->id,
            'store_id' => $mainStore?->id,
            'name' => 'Inventory Manager',
            'password' => Hash::make('12345678'),
            'is_active' => true,
        ]);

        $accountant = User::query()->updateOrCreate([
            'email' => 'accountant@gmail.com',
        ], [
            'tenant_id' => $tenant->id,
            'store_id' => $mainStore?->id,
            'name' => 'Accountant User',
            'password' => Hash::make('12345678'),
            'is_active' => true,
        ]);

        $superAdmin->syncRoles(['super_admin']);
        $owner->syncRoles(['tenant_owner']);
        $admin->syncRoles(['admin']);
        $storeManager->syncRoles(['store_manager']);
        $cashier->syncRoles(['cashier']);
        $inventoryManager->syncRoles(['inventory_manager']);
        $accountant->syncRoles(['accountant']);
    }
}
