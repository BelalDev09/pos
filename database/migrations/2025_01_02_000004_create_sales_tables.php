<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Sales (POS + eCommerce) ───────────────────────────────────────
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('cashier_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('invoice_number', 60)->unique();
            $table->enum('sale_type', ['pos', 'online', 'phone'])->default('pos');
            $table->enum('status', [
                'draft',
                'pending',
                'completed',
                'cancelled',
                'returned'
            ])->default('completed');

            $table->date('sale_date');

            // Financials
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('shipping_amount', 15, 4)->default(0);
            $table->decimal('loyalty_discount', 15, 4)->default(0);
            $table->decimal('grand_total', 15, 4)->default(0);
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->decimal('change_amount', 15, 4)->default(0);
            $table->decimal('due_amount', 15, 4)->default(0);

            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('paid');

            // Discount
            $table->string('promocode', 40)->nullable();
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 10, 4)->nullable();

            // Loyalty
            $table->decimal('loyalty_points_earned', 15, 2)->default(0);
            $table->decimal('loyalty_points_redeemed', 15, 2)->default(0);

            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->default(1);

            // Shipping (online orders)
            $table->string('shipping_address')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_country', 80)->nullable();
            $table->string('tracking_number', 100)->nullable();

            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();  // extra flags e.g. table_id for restaurant

            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'sale_date']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'payment_status']);
            $table->index(['business_id', 'customer_id']);
            $table->index(['business_id', 'sale_type']);
            $table->index('invoice_number');
        });

        // ─── Sale Items ────────────────────────────────────────────────────
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('product_variation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Snapshots (price at time of sale — never join back to products for reporting)
            $table->string('product_name');
            $table->string('product_sku', 100)->nullable();
            $table->decimal('unit_price', 15, 4);
            $table->decimal('cost_price', 15, 4)->default(0);

            $table->decimal('quantity', 15, 4);
            $table->string('unit_name', 30)->nullable();    // "kg", "pcs"

            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive');

            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 10, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);

            $table->decimal('subtotal', 15, 4);     // after discount, before tax
            $table->decimal('total', 15, 4);        // final line total
            $table->decimal('profit', 15, 4)->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sale_id');
            $table->index('product_id');
        });

        // ─── Payments ──────────────────────────────────────────────────────
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable');              // sale, purchase, subscription
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->decimal('amount', 15, 4);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->default(1);

            $table->enum('method', [
                'cash',
                'card',
                'bank_transfer',
                'mobile_banking',
                'stripe',
                'paypal',
                'loyalty_points',
                'credit',
                'other'
            ])->default('cash');

            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])
                ->default('completed');

            $table->string('reference', 100)->nullable();       // card last4, txn ID
            $table->string('gateway_txn_id')->nullable();
            $table->string('gateway_response')->nullable();
            $table->string('receipt_number', 60)->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'method']);
            $table->index(['business_id', 'status']);
            // $table->index(['payable_type', 'payable_id']);
            $table->index('paid_at');
        });

        // ─── Sale Returns ──────────────────────────────────────────────────
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->string('reference_no', 60)->unique();
            $table->date('return_date');
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->decimal('refund_amount', 15, 4)->default(0);
            $table->enum('refund_method', [
                'cash',
                'card',
                'store_credit',
                'loyalty_points'
            ])->default('cash');
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'sale_id']);
            $table->index(['business_id', 'return_date']);
        });

        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_item_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_name');
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('subtotal', 15, 4);
            $table->boolean('restock')->default(true);
            $table->timestamps();

            $table->index('sale_return_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_return_items');
        Schema::dropIfExists('sale_returns');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('payments');
    }
};
