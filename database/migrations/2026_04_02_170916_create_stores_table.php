<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            // Identity
            $table->string('name');
            $table->string('code', 20)->nullable();          // STORE-01, HQ, BRANCH-NORTH
            $table->enum('type', ['retail', 'warehouse', 'popup', 'kiosk'])->default('retail');

            // Location
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Contact
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();

            // Settings
            $table->string('currency', 3)->default('USD');
            $table->string('timezone', 50)->default('UTC');
            $table->boolean('tax_inclusive')->default(false); // prices include tax?
            $table->decimal('default_tax_rate', 8, 4)->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_headquarters')->default(false);

            // Receipt config
            $table->json('receipt_settings')->nullable();    // header, footer, logo, show_tax, etc.

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'type']);
            $table->unique(['tenant_id', 'code']);           // code unique per tenant
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
