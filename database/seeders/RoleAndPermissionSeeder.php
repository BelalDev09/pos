<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Platform / tenant
            'tenants.view',
            'tenants.create',
            'tenants.update',
            'tenants.delete',

            // Admin management
            'dashboard.view',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.assign_roles',
            'roles.list',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.list',
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
            'settings.view',
            'settings.update',
            'profile.update',
            'faq.view',
            'faq.create',
            'faq.update',
            'faq.delete',
            'dynamic_pages.view',
            'dynamic_pages.create',
            'dynamic_pages.update',
            'dynamic_pages.delete',

            // Core business
            'stores.view',
            'stores.create',
            'stores.update',
            'stores.delete',
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'products.import',
            'products.export',
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            'brands.view',
            'brands.create',
            'brands.update',
            'brands.delete',
            'inventory.view',
            'inventory.adjust',
            'inventory.transfer',
            'inventory.stock_count',
            'pos.access',
            'pos.open_register',
            'pos.close_register',
            'pos.apply_discount',
            'pos.void_order',
            'pos.process_refund',
            'pos.price_override',
            'orders.view',
            'orders.view_all',
            'orders.create',
            'orders.update',
            'orders.delete',
            'purchases.view',
            'purchases.create',
            'purchases.update',
            'purchases.approve',
            'purchases.receive',
            'purchases.delete',
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',
            'reports.view_sales',
            'reports.view_inventory',
            'reports.view_financial',
            'reports.export',
            'discounts.view',
            'discounts.create',
            'discounts.update',
            'discounts.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $allPermissionNames = Permission::query()->pluck('name')->all();

        $rolePermissions = [
            'super_admin' => $allPermissionNames,
            'admin' => array_values(array_diff($allPermissionNames, [
                'tenants.delete',
            ])),
            'tenant_owner' => array_values(array_diff($allPermissionNames, [
                'tenants.create',
                'tenants.delete',
            ])),
            'store_manager' => [
                'dashboard.view',
                'stores.view',
                'products.view',
                'products.create',
                'products.update',
                'categories.view',
                'categories.create',
                'categories.update',
                'brands.view',
                'brands.create',
                'brands.update',
                'inventory.view',
                'inventory.adjust',
                'inventory.transfer',
                'orders.view',
                'orders.view_all',
                'orders.create',
                'orders.update',
                'customers.view',
                'customers.create',
                'customers.update',
                'suppliers.view',
                'purchases.view',
                'purchases.create',
                'purchases.update',
                'reports.view_sales',
                'reports.view_inventory',
                'settings.view',
                'pos.access',
                'pos.open_register',
                'pos.close_register',
                'pos.apply_discount',
                'pos.process_refund',
            ],
            'cashier' => [
                'dashboard.view',
                'pos.access',
                'pos.open_register',
                'pos.close_register',
                'pos.apply_discount',
                'products.view',
                'inventory.view',
                'orders.view',
                'orders.create',
                'customers.view',
                'customers.create',
            ],
            'inventory_manager' => [
                'dashboard.view',
                'products.view',
                'products.create',
                'products.update',
                'products.import',
                'categories.view',
                'categories.create',
                'categories.update',
                'brands.view',
                'brands.create',
                'brands.update',
                'inventory.view',
                'inventory.adjust',
                'inventory.transfer',
                'inventory.stock_count',
                'suppliers.view',
                'suppliers.create',
                'suppliers.update',
                'purchases.view',
                'purchases.create',
                'purchases.update',
                'purchases.receive',
                'reports.view_inventory',
            ],
            'accountant' => [
                'dashboard.view',
                'orders.view',
                'orders.view_all',
                'purchases.view',
                'customers.view',
                'suppliers.view',
                'reports.view_sales',
                'reports.view_inventory',
                'reports.view_financial',
                'reports.export',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($permissionNames);
        }

        $superAdminUser = User::query()->where('is_super_admin', true)->first();
        if ($superAdminUser !== null) {
            $superAdminUser->syncRoles(['super_admin']);
        }
    }
}
