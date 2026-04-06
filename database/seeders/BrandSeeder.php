<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Tenant;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        $brands = [
            'Coca Cola',
            'Pepsi',
            'Samsung',
            'Apple'
        ];

        foreach ($brands as $brandName) {
            Brand::create([
                'tenant_id' => $tenant->id,
                'name' => $brandName,
                'slug' => Str::slug($brandName),
            ]);
        }
    }
}
