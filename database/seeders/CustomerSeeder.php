<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Tenant;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        // Walk-in customer
        Customer::create([
            'tenant_id' => $tenant->id,
            'name' => 'Walk-in Customer',
            'is_walk_in' => true,
            'tier' => 'standard',
            'is_active' => true
        ]);

        for ($i = 1; $i <= 20; $i++) {

            Customer::create([
                'tenant_id' => $tenant->id,
                'name' => "Customer $i",
                'email' => "customer$i@gmail.com",
                'phone' => '017000000' . $i,
                'tier' => 'standard',
                'is_active' => true
            ]);
        }
    }
}
