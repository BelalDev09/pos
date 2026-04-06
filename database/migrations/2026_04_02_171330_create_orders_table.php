<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('register_id')->nullable()->constrained('registers')->nullOnDelete();
            $table->foreignId('pos_session_id')->nullable();
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->nullOnDelete();
            $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('discount_id')->nullable();

            $table->string('order_number', 50)->unique();

            $table->enum('status', [
                'pending',      // created but not completed
                'completed',    // fully paid and stock deducted
                'partially_refunded',
                'refunded',     // fully refunded
                'voided',       // cancelled before payment
                'on_hold',      // parked/suspended
            ])->default('pending');

            $table->enum('source', ['pos', 'online', 'phone', 'manual'])->default('pos');

            // Financials
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('rounding_amount', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('amount_tendered', 15, 4)->default(0);
            $table->decimal('change_given', 15, 4)->default(0);
            $table->decimal('total_refunded', 15, 4)->default(0);

            $table->string('currency', 3)->default('USD');

            // Discount details snapshot
            $table->string('discount_code', 50)->nullable();
            $table->decimal('loyalty_points_used', 15, 4)->default(0);
            $table->decimal('loyalty_points_earned', 15, 4)->default(0);

            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            // Offline sync support
            $table->string('offline_id', 100)->nullable();  // client-generated ID for offline orders
            $table->boolean('is_synced')->default(true);

            $table->timestamp('completed_at')->nullable();

            // NO soft deletes on orders — use void/refund status instead
            $table->timestamps();

            // Critical indexes for reporting — tenant_id always leads
            $table->index(['tenant_id', 'store_id', 'completed_at'], 'idx_orders_reporting');
            $table->index(['tenant_id', 'store_id', 'status'], 'idx_orders_status');
            $table->index(['tenant_id', 'customer_id', 'completed_at'], 'idx_orders_customer');
            $table->index(['tenant_id', 'cashier_id', 'completed_at'], 'idx_orders_cashier');
            $table->index(['pos_session_id'], 'idx_orders_session');
            $table->index('order_number');
            $table->index('offline_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
