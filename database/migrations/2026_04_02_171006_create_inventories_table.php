<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->decimal('quantity', 15, 4)->default(0);
            $table->decimal('reserved_quantity', 15, 4)->default(0);  // held for pending orders
            $table->decimal('available_quantity', 15, 4)
                ->storedAs('quantity - reserved_quantity');           // computed column

            $table->decimal('reorder_level', 15, 4)->default(0);      // alert threshold
            $table->decimal('reorder_quantity', 15, 4)->default(0);   // auto PO quantity

            // Location within store (for larger warehouses)
            $table->string('location_code', 50)->nullable();           // Aisle A, Shelf 3

            $table->timestamp('last_counted_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // One inventory record per product per store
            $table->unique(['product_id', 'store_id'], 'uq_inventory_product_store');

            $table->index(['store_id', 'quantity', 'reorder_level'], 'idx_inventory_low_stock');
            $table->index(['tenant_id', 'store_id'], 'idx_inventory_tenant_store');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
