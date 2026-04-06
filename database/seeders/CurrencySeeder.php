<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Models\Tenant;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        $currencies = [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'exchange_rate' => 1,
                'is_default' => true
            ],
            [
                'code' => 'BDT',
                'name' => 'Bangladeshi Taka',
                'symbol' => '৳',
                'symbol_position' => 'before',
                'decimal_places' => 2,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
                'exchange_rate' => 110
            ],
        ];

        foreach ($currencies as $currency) {

            Currency::create(array_merge(
                $currency,
                ['tenant_id' => $tenant->id]
            ));
        }
    }
}
