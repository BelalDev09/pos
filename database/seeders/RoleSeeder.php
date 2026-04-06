<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all permissions
        $allPermissions = Permission::all();

        // Roles and their permissions
        $rolesPermissions = [
            'superadmin' => $allPermissions,
            'admin'      => $allPermissions,
            'manager'    => Permission::whereIn('name', [
                'manage.management',
                'orders.approve',
                'payments.refund',
                'payments.void',
                'payments.apply_discount',
                'tables.manage',
                'staff.shift',
                'staff.assign_section',
                'staff.lock_ordering',
            ])->orWhere('name', 'like', 'reports.%')->get(),

            'cashier' => Permission::whereIn('name', [
                'manage.cash',
                'payments.view',
                'payments.create',
                'payments.split_bill',
                'payments.open_cash_drawer',
                'payments.end_shift',
                'payments.apply_discount',
                'orders.view',
                'orders.create',
                'orders.edit',
            ])->get(),

            'waiter' => Permission::whereIn('name', [
                'waiter.open_table',
                'waiter.assign_seats',
                'waiter.take_order',
                'waiter.fire_course',
                'waiter.send_to_kitchen',
                'waiter.reorder',
                'waiter.transfer_table',
                'waiter.bill_preview',
                'waiter.notify_cashier',
                'orders.create',
                'orders.view',
            ])->get(),

            'kitchen' => Permission::whereIn('name', [
                'kitchen.view',
                'kitchen.update_status',
                'kitchen.bump',
                'kitchen.recall',
                'kitchen.partial_complete',
                'kitchen.notify_waiter',
            ])->get(),

            'customer' => Permission::whereIn('name', [
                'customer.menu_browse',
                'customer.add_notes',
                'customer.select_service_type',
                'customer.pay',
                'customer.view_status',
            ])->get(),

            'staff' => Permission::whereIn('name', [
                'staff.view',
                'staff.create',
                'staff.edit',
                'staff.delete',
                'staff.shift',
                'staff.assign_section',
                'staff.lock_ordering',
            ])->get(),
        ];

        // Create roles and assign permissions
        foreach ($rolesPermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
