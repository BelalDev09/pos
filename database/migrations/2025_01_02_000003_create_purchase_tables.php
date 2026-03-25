<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Suppliers ─────────────────────────────────────────────────────
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('company', 150)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 80)->nullable();
            $table->string('tax_number', 60)->nullable();

            $table->decimal('outstanding_balance', 15, 4)->default(0);
            $table->decimal('total_purchased', 15, 4)->default(0);

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'is_active']);
        });

        // ─── Purchases ─────────────────────────────────────────────────────
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->string('reference_no', 60)->unique();
            $table->enum('status', [
                'ordered', 'partial', 'received', 'returned', 'cancelled'
            ])->default('ordered');

            $table->date('purchase_date');
            $table->date('expected_date')->nullable();

            // Totals
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('shipping_cost', 15, 4)->default(0);
            $table->decimal('grand_total', 15, 4)->default(0);
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->decimal('due_amount', 15, 4)->default(0);

            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->default(1);

            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'purchase_date']);
            $table->index(['business_id', 'supplier_id']);
            $table->index(['business_id', 'payment_status']);
        });

        // ─── Purchase Items ────────────────────────────────────────────────
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variation_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('product_name');              // snapshot
            $table->string('product_sku', 100)->nullable();

            $table->decimal('quantity', 15, 4);
            $table->decimal('received_quantity', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('subtotal', 15, 4);
            $table->timestamps();

            $table->index('purchase_id');
            $table->index('product_id');
        });

        // ─── Purchase Returns ──────────────────────────────────────────────
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->string('reference_no', 60)->unique();
            $table->date('return_date');
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'purchase_id']);
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4);
            $table->decimal('subtotal', 15, 4);
            $table->timestamps();

            $table->index('purchase_return_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
    }
};
