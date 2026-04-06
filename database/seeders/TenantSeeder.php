<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::create([
            'name'          => 'ACME Retail Group',
            'slug'          => 'acme-retail',
            'subdomain'     => 'acme',
            'plan'          => 'professional',
            'business_type' => 'retail',
            'phone'         => '+1-800-555-0100',
            'email'         => 'admin@acme.com',
            'address'       => '123 Commerce Street',
            'city'          => 'New York',
            'country'       => 'US',
            'timezone'      => 'America/New_York',
            'locale'        => 'en',
            'currency'      => 'USD',
            'is_active'     => true,
            'settings'      => [
                'logo_url'       => null,
                'receipt_footer' => 'Thank you for shopping with us!',
            ],
        ]);
    }
}
