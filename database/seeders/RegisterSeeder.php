<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Register;
use App\Models\Store;
use App\Models\Tenant;

class RegisterSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $store = Store::where('tenant_id', $tenant->id)->first();

        Register::create([
            'tenant_id' => $tenant->id,
            'store_id' => $store?->id,
            'name' => 'Main Register',
            'device_id' => 'POS-001',
            'is_active' => true,
            'printer_settings' => json_encode([
                'printer_name' => 'Receipt Printer',
                'paper_size' => '80mm'
            ]),
        ]);
    }
}
