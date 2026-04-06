<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        // Super Admin
        User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'is_super_admin' => true,
        ]);

        // Tenant Owner
        User::create([
            'tenant_id' => $tenant->id,
            'name' => 'John Owner',
            'email' => 'owner@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        // Cashier
        User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Jane Cashier',
            'email' => 'cashier@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
