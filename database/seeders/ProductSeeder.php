<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\TaxRate;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $category = Category::where('tenant_id', $tenant->id)->first();
        $brand = Brand::where('tenant_id', $tenant->id)->first();
        $taxRate = TaxRate::where('tenant_id', $tenant->id)->first();

        $products = [
            ['name' => 'Coca Cola 500ml', 'sku' => 'CC500'],
            ['name' => 'Pepsi 500ml', 'sku' => 'PEP500'],
            ['name' => 'Samsung Galaxy S21', 'sku' => 'SGS21'],
            ['name' => 'Apple iPhone 15', 'sku' => 'IP15']
        ];

        foreach ($products as $prod) {
            Product::create([
                'tenant_id' => $tenant->id,
                'category_id' => $category?->id,
                'brand_id' => $brand?->id,
                'tax_rate_id' => $taxRate?->id,
                'name' => $prod['name'],
                'slug' => Str::slug($prod['name']),
                'sku' => $prod['sku'],
                'selling_price' => rand(50, 500),
                'cost_price' => rand(30, 400),
            ]);
        }
    }
}
