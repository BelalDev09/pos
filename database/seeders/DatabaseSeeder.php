<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,        // Creates 1 demo tenant
            StoreSeeder::class,         // 2 stores for demo tenant
            RoleAndPermissionSeeder::class, // Spatie roles/permissions
            UserSeeder::class,          // Super admin + tenant users
            CurrencySeeder::class,      // USD, EUR, BDT etc.
            TaxRateSeeder::class,       // Standard tax rates
            CategorySeeder::class,      // Product categories
            BrandSeeder::class,         // Product brands
            SupplierSeeder::class,      // Sample suppliers
            ProductSeeder::class,       // 50 sample products
            CustomerSeeder::class,      // 20 sample customers
            RegisterSeeder::class,      // 1 register per store
            AppSettingSeeder::class,       // Default system settings
        ]);
    }
}
