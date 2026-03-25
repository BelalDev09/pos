<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();               // used for subdomain: slug.app.com
            $table->string('business_code', 20)->unique(); // short internal code e.g. BIZ-00123
            $table->enum('type', [
                'retail', 'restaurant', 'supermarket',
                'pharmacy', 'salon', 'cafe', 'other'
            ])->default('retail');

            // Contact
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->string('website')->nullable();

            // Address
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->default('US');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // Branding
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('brand_color', 7)->nullable();   // hex

            // Locale / Finance
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->string('currency_symbol', 5)->default('$');
            $table->enum('currency_position', ['before', 'after'])->default('before');
            $table->string('date_format', 20)->default('Y-m-d');
            $table->string('time_format', 5)->default('H:i');
            $table->string('locale', 10)->default('en');

            // Tax / Invoice
            $table->string('tax_number', 60)->nullable();
            $table->string('invoice_prefix', 10)->default('INV');
            $table->unsignedInteger('invoice_start')->default(1000);

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('is_active');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
