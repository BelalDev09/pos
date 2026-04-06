<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Tenant;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'Global Wholesale Ltd',
            'company_name' => 'Global Wholesale Ltd',
            'email' => 'sales@gmail.com',
            'phone' => '+1-800-333',
            'city' => 'New York',
            'country' => 'US',
            'payment_terms' => 'net_30',
            'is_active' => true
        ]);

        Supplier::create([
            'tenant_id' => $tenant->id,
            'name' => 'Food Distribution Inc',
            'company_name' => 'Food Distribution Inc',
            'email' => 'contact@gmail.com',
            'phone' => '+1-800-444',
            'city' => 'New York',
            'country' => 'US',
            'payment_terms' => 'net_15',
            'is_active' => true
        ]);
    }
}
