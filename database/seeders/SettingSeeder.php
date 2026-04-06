<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\Store;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $store = Store::where('tenant_id', $tenant->id)->first();

        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'My POS System'],
            ['group' => 'pos', 'key' => 'default_discount', 'value' => '0'],
            ['group' => 'receipt', 'key' => 'show_logo', 'value' => 'true'],
        ];

        foreach ($settings as $set) {
            Setting::create([
                'tenant_id' => $tenant->id,
                'store_id' => $store?->id,
                'group' => $set['group'],
                'key' => $set['key'],
                'value' => $set['value'],
                'type' => 'string',
            ]);
        }
    }
}
