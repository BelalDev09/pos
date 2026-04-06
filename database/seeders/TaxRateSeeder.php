<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaxRate;
use App\Models\Tenant;

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        TaxRate::create([
            'tenant_id' => $tenant->id,
            'name' => 'Standard VAT',
            'rate' => 15,
            'type' => 'percentage',
            'is_inclusive' => false,
            'is_default' => true,
            'is_active' => true
        ]);

        TaxRate::create([
            'tenant_id' => $tenant->id,
            'name' => 'Zero Tax',
            'rate' => 0,
            'type' => 'percentage',
            'is_inclusive' => false,
            'is_active' => true
        ]);
    }
}
