<?php

// database/migrations/2024_01_01_000015_create_stock_movements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            // Movement type
            $table->enum('type', [
                'purchase',          // goods received from PO
                'sale',              // sold via POS
                'return_in',         // customer return restocked
                'return_out',        // returned to supplier
                'adjustment_in',     // manual positive adjustment
                'adjustment_out',    // manual negative adjustment
                'transfer_in',       // received from another store
                'transfer_out',      // sent to another store
                'opening_stock',     // initial stock entry
                'damage',            // written off as damaged
                'expired',           // expired (pharmacy/food)
            ]);

            // Positive = stock increase, Negative = stock decrease
            $table->decimal('quantity', 15, 4);
            $table->decimal('quantity_before', 15, 4);   // snapshot for audit
            $table->decimal('quantity_after', 15, 4);    // snapshot for audit

            $table->decimal('unit_cost', 15, 4)->nullable();  // cost at time of movement

            // Polymorphic reference to the source document
            $table->string('reference_type')->nullable();     // App\Models\Order, PurchaseOrder etc
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_number', 100)->nullable(); // human-readable ref

            // Batch/expiry tracking (pharmacy/food)
            $table->string('batch_number', 100)->nullable();
            $table->date('expiry_date')->nullable();

            $table->text('notes')->nullable();
            $table->timestamp('moved_at');

            // NO soft deletes — stock movements are immutable audit records
            $table->timestamp('created_at')->nullable();

            // Indexes optimized for reporting and audit queries
            $table->index(['tenant_id', 'product_id', 'moved_at'], 'idx_movements_product_date');
            $table->index(['tenant_id', 'store_id', 'type', 'moved_at'], 'idx_movements_store_type');
            $table->index(['reference_type', 'reference_id'], 'idx_movements_reference');
            $table->index('batch_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
