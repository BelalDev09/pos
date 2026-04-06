<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define all permissions 
        $permissions = [
            // Tenants
            'tenants.view',
            'tenants.create',
            'tenants.update',
            'tenants.delete',

            // Stores
            'stores.view',
            'stores.create',
            'stores.update',
            'stores.delete',

            // Users
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.assign_roles',

            // Products
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'products.import',
            'products.export',

            // Categories & Brands
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            'brands.view',
            'brands.create',
            'brands.update',
            'brands.delete',

            // Inventory
            'inventory.view',
            'inventory.adjust',
            'inventory.transfer',
            'inventory.stock_count',

            // POS
            'pos.access',          // open POS terminal
            'pos.open_register',   // open cash register
            'pos.close_register',  // close cash register
            'pos.apply_discount',  // apply manual discount
            'pos.void_order',      // void an order
            'pos.process_refund',  // issue refunds
            'pos.price_override',  // override item price

            // Orders
            'orders.view',
            'orders.view_all',
            'orders.create',
            'orders.update',
            'orders.delete',

            // Purchases
            'purchases.view',
            'purchases.create',
            'purchases.update',
            'purchases.approve',
            'purchases.receive',
            'purchases.delete',

            // Customers
            'customers.view',
            'customers.create',
            'customers.update',
            'customers.delete',

            // Suppliers
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',

            // Reports
            'reports.view_sales',
            'reports.view_inventory',
            'reports.view_financial',
            'reports.export',

            // Settings
            'settings.view',
            'settings.update',

            // Discounts & Promotions
            'discounts.view',
            'discounts.create',
            'discounts.update',
            'discounts.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Define roles and assign permissions 

        // Super Admin — platform-level, no tenant restriction
        $super_admin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $super_admin->syncPermissions(Permission::all());

        // Tenant Owner — full access within their tenant
        $tenantOwner = Role::firstOrCreate(['name' => 'tenant_owner', 'guard_name' => 'web']);
        $tenantOwner->syncPermissions(Permission::whereNotIn('name', [
            'tenants.create',
            'tenants.delete',
        ])->get());

        // Store Manager — manages their store
        $storeManager = Role::firstOrCreate(['name' => 'store_manager', 'guard_name' => 'web']);
        $storeManager->syncPermissions([
            'pos.access',
            'pos.open_register',
            'pos.close_register',
            'pos.apply_discount',
            'pos.void_order',
            'pos.process_refund',
            'products.view',
            'products.create',
            'products.update',
            'inventory.view',
            'inventory.adjust',
            'inventory.transfer',
            'orders.view',
            'orders.view_all',
            'orders.create',
            'customers.view',
            'customers.create',
            'customers.update',
            'suppliers.view',
            'purchases.view',
            'purchases.create',
            'reports.view_sales',
            'reports.view_inventory',
            'settings.view',
        ]);

        // Cashier — POS only
        $cashier = Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions([
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
        ]);

        // Inventory Manager
        $inventoryManager = Role::firstOrCreate(['name' => 'inventory_manager', 'guard_name' => 'web']);
        $inventoryManager->syncPermissions([
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
        ]);

        // Accountant — reports and finances only, read-heavy
        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->syncPermissions([
            'orders.view',
            'orders.view_all',
            'purchases.view',
            'customers.view',
            'suppliers.view',
            'reports.view_sales',
            'reports.view_inventory',
            'reports.view_financial',
            'reports.export',
        ]);

        // ── Assign super_admin role to the first user 
        $super_adminUser = User::where('is_super_admin', true)->first();
        if ($super_adminUser) {
            $super_adminUser->assignRole('super_admin');
        }
    }
}
