<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Dashboard
            'dashboard.view',
            // 'dashboard.stats',
            /**
             * manage permissions
             */
            'manage.management',
            'manage.cash',
            'users.manage',
            'manage.category',
            'manage.setting',
            'manage.manu',
            'manage.odite',
            'manage.page',
            'dashboard.viewAdmin',
            // Users/Roles/Permissions
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Restaurants / Business setup
            'restaurants.view',
            'restaurants.create',
            'restaurants.edit',
            'restaurants.delete',
            'restaurants.manage',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'menu_items.view',
            'menu_items.create',
            'menu_items.edit',
            'menu_items.delete',
            'modifiers.view',
            'modifiers.create',
            'modifiers.edit',
            'modifiers.delete',
            'allergens.view',
            'allergens.create',
            'allergens.edit',
            'allergens.delete',
            'settings.tax',
            'settings.discount',
            'settings.restaurant',
            'settings.system',
            'settings.smtp',
            'settings.admin',
            'settings.mail',

            /**
             * Restaurant Tables
             */
            'tables.view',
            'tables.create',
            'tables.edit',
            'tables.delete',

            // Orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.complete',
            'orders.cancel',
            'orders.approve',
            'tables.view',
            'tables.create',
            'tables.edit',
            'tables.delete',
            'tables.manage',

            // Payments
            'payments.view',
            'payments.create',
            'payments.refund',
            'payments.void',
            'payments.apply_discount',
            'payments.open_cash_drawer',
            'payments.end_shift',
            'payments.split_bill',
            'payments.print_receipt',

            // Staff
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'staff.shift',
            'staff.assign_table',
            'staff.quick_assign',

            'staff.assign_table',
            'staff.quick_assign',
            'staff.lock_ordering',

            // Inventory
            'inventory.view',
            'inventory.update',
            'inventory.transactions',
            'inventory.stock_count',

            // Kitchen / Waiter
            'kitchen.view',
            'kitchen.advance',
            'kitchen.refresh',

            'kitchen.update_status',
            'kitchen.bump',
            'kitchen.recall',
            'kitchen.partial_complete',
            'kitchen.notify_waiter',
            'waiter.open_table',
            'waiter.assign_seats',
            'waiter.take_order',
            'waiter.fire_course',
            'waiter.send_to_kitchen',
            'waiter.reorder',
            'waiter.transfer_table',
            'waiter.bill_preview',
            'waiter.notify_cashier',

            // Customer / QR
            'customer.menu_browse',
            'customer.add_notes',
            'customer.select_service_type',
            'customer.pay',
            'customer.view_status',

            // Reports
            'reports.view',

            'reports.sales',
            'reports.orders',
            'reports.inventory',
            'reports.staff',
            'reports.customers',
            'reports.popular_items',
            'reports.hourly',
            'reports.daily',

            /**
             * Cashier
             */
            'cashier.dashboard',
            'cashier.orders',

        ];

        foreach (array_unique($permissions) as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}
