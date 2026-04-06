<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Tenant;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        Store::create([
            'tenant_id' => $tenant->id,
            'name' => 'ACME Main Store',
            'code' => 'MAIN',
            'address' => 'Downtown',
            'city' => 'New York',
            'country' => 'US',
            'phone' => '+1-800-111',
            'is_active' => true
        ]);

        Store::create([
            'tenant_id' => $tenant->id,
            'name' => 'ACME Branch Store',
            'code' => 'BRANCH',
            'address' => 'Brooklyn',
            'city' => 'New York',
            'country' => 'US',
            'phone' => '+1-800-222',
            'is_active' => true
        ]);
    }
}
