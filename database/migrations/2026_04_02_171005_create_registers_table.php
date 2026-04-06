<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('name', 100);                     // "Register 1", "Counter A"
            $table->string('device_id', 100)->nullable();    // hardware terminal identifier
            $table->boolean('is_active')->default(true);

            // Receipt printer config
            $table->json('printer_settings')->nullable();

            $table->timestamps();

            $table->index(['store_id', 'is_active']);
            $table->index(['tenant_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registers');
    }
};
