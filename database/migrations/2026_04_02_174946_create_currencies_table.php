<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('code', 3);                       // USD, EUR, BDT
            $table->string('name', 100);                     // US Dollar
            $table->string('symbol', 10);                    // $, €, ৳
            $table->string('symbol_position', 6)->default('before'); // before|after
            $table->integer('decimal_places')->default(2);
            $table->string('decimal_separator', 5)->default('.');
            $table->string('thousand_separator', 5)->default(',');
            $table->decimal('exchange_rate', 20, 8)->default(1.00000000); // relative to base currency
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
