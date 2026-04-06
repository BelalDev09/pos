<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->string('name');                          // "Small / Red", "XL / Blue"
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->json('attributes');                      // {"size": "XL", "color": "Blue"}

            // Pricing overrides (if null, inherits from product)
            $table->decimal('cost_price', 15, 4)->nullable();
            $table->decimal('selling_price', 15, 4)->nullable();
            $table->decimal('price_adjustment', 15, 4)->default(0); // +/- from base price

            // Physical
            $table->decimal('weight', 10, 4)->nullable();

            $table->string('image')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index(['product_id', 'is_active']);
            $table->index('barcode');                        // barcode scans happen without tenant context
            $table->unique(['product_id', 'sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
