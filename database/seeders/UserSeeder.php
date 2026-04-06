<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // superadmin
        $superadmin = User::firstOrCreate([
            'name' => 'Super Admin',
            'email' => 'superadmin@admin.com',
            'phone' => '01234567890',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'status' => 'active',
            'is_admin' => true,
        ]);
        $superadmin->assignRole('superadmin');
        // Admin
        $admin = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '01234567890',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'status' => 'active',
            'is_admin' => true,
        ]);
        $admin->assignRole('admin');


        // Manager
        $manager = User::firstOrCreate([
            'name' => 'Manager',
            'email' => 'manager@gmail.com',
            'phone' => '01700000001',
            'password' => Hash::make('12345678'),
            'status' => 'active',
        ]);
        $manager->assignRole('manager');


        // Staff
        $staff = User::firstOrCreate([
            'name' => 'Staff',
            'email' => 'staff@gmail.com',
            'phone' => '01700000002',
            'password' => Hash::make('12345678'),
            'status' => 'active',
        ]);
        $staff->assignRole('staff');


        // Waiter
        $waiter = User::firstOrCreate([
            'name' => 'Waiter',
            'email' => 'waiter@gmail.com',
            'phone' => '01700000003',
            'password' => Hash::make('12345678'),
            'status' => 'active',
        ]);
        $waiter->assignRole('waiter');


        // Cashier
        $cashier = User::firstOrCreate([
            'name' => 'Cashier',
            'email' => 'cashier@gmail.com',
            'phone' => '01700000004',
            'password' => Hash::make('12345678'),
            'status' => 'active',
        ]);
        $cashier->assignRole('cashier');


        // Customer
        $customer = User::firstOrCreate([
            'name' => 'Customer',
            'email' => 'customer@gmail.com',
            'phone' => '01700000005',
            'password' => Hash::make('12345678'),
            'status' => 'active',
        ]);
        $customer->assignRole('customer');

        $staff = User::firstOrCreate(
            ['email' => 'staff@gmail.com'], // check by email
            [
                'name' => 'Staff',
                'phone' => '01700000002',
                'password' => Hash::make('12345678'),
                'status' => 'active',
            ]
        );
        $staff->assignRole('staff');
    }
}
