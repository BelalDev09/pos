<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('name', 100);                     // GST 15%, VAT 20%, Zero-rated
            $table->decimal('rate', 8, 4);                   // 15.0000, 20.0000, 0.0000
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->boolean('is_inclusive')->default(false); // tax included in price?
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
