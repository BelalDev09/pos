<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every stock change writes an immutable ledger row
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variation_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->enum('type', [
                'purchase',           // stock in from purchase
                'sale',               // stock out from sale
                'sale_return',        // stock back from return
                'purchase_return',    // stock out on purchase return
                'adjustment',         // manual adjustment
                'transfer_in',        // from another location
                'transfer_out',
                'opening',            // opening stock entry
                'damage',
                'expiry',
            ]);

            $table->decimal('quantity', 15, 4);         // positive = in, negative = out
            $table->decimal('quantity_before', 15, 4);
            $table->decimal('quantity_after', 15, 4);

            $table->decimal('unit_cost', 15, 4)->nullable();

            // Polymorphic reference to source document
            $table->string('reference_type')->nullable(); // App\Models\Sale etc
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('note')->nullable();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index(['business_id', 'product_id']);
            $table->index(['business_id', 'type']);
            $table->index(['business_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        // Manual stock adjustments (wraps stock_movements)
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->string('reference_no', 60)->unique();
            $table->date('adjustment_date');
            $table->enum('type', ['add', 'deduct', 'set'])->default('add');
            $table->enum('reason', [
                'damage', 'theft', 'expiry', 'count_correction',
                'opening_stock', 'transfer', 'other'
            ]);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('business_id');
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('quantity_before', 15, 4);
            $table->decimal('quantity_adjusted', 15, 4);
            $table->decimal('quantity_after', 15, 4);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->timestamps();

            $table->index('stock_adjustment_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
    }
};
