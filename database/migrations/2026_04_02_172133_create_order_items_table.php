<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->nullOnDelete();

            // Snapshot product details at time of sale (important — products can change)
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('product_barcode')->nullable();
            $table->string('variant_name')->nullable();

            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 15, 4);            // price at time of sale
            $table->decimal('unit_cost', 15, 4)->default(0); // cost at time of sale (for profit calc)

            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('discount_percent', 8, 4)->default(0);
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('subtotal', 15, 4)
                ->storedAs('quantity * unit_price');
            $table->decimal('total', 15, 4);                 // after discount + tax

            // Refund tracking
            $table->decimal('quantity_refunded', 15, 4)->default(0);
            $table->boolean('is_refunded')->default(false);

            $table->timestamps();

            $table->index(['order_id', 'product_id'], 'idx_order_items_product');
            $table->index('product_id');                     // for product sales report
            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
