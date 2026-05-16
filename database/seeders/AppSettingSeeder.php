<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\Store;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | SYSTEM SETTINGS
        |--------------------------------------------------------------------------
        */
        SystemSetting::truncate();

        SystemSetting::create([
            'system_title'         => 'My Application',
            'system_short_title'   => 'App',
            'tag_line'             => 'Best Ecommerce Platform',
            'company_name'         => 'My Company Ltd.',
            'phone_code'           => '+880',
            'phone_number'         => '1234567890',
            'email'                => 'admin@admin.com',
            'copyright_text'       => '© 2026 My Company',
            'admin_title'          => 'Admin Panel',
            'admin_short_title'    => 'Admin',
            'admin_copyright_text' => '© 2026 Admin Panel',
        ]);


        /*
        |--------------------------------------------------------------------------
        | TENANT / STORE SETTINGS
        |--------------------------------------------------------------------------
        */
        $tenant = Tenant::first();

        if (!$tenant) {
            return;
        }

        $store = Store::where('tenant_id', $tenant->id)->first();

        Setting::truncate();

        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'My POS System'],
            ['group' => 'pos', 'key' => 'default_discount', 'value' => '0'],
            ['group' => 'receipt', 'key' => 'show_logo', 'value' => 'true'],
        ];

        foreach ($settings as $set) {
            Setting::create([
                'tenant_id' => $tenant->id,
                'store_id'  => $store?->id,
                'group'     => $set['group'],
                'key'       => $set['key'],
                'value'     => $set['value'],
                'type'      => 'string',
            ]);
        }
    }
}
