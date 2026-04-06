<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        $categories = [
            'Beverages',
            'Snacks',
            'Electronics',
            'Groceries'
        ];

        foreach ($categories as $cat) {

            Category::create([
                'tenant_id' => $tenant->id,
                'name' => $cat,
                'slug' => Str::slug($cat),
                'is_active' => true
            ]);
        }
    }
}
