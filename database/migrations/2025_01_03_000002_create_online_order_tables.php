<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Promo Codes ───────────────────────────────────────────────────
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('code', 50);
            $table->string('name', 150)->nullable();
            $table->text('description')->nullable();

            $table->enum('discount_type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('discount_value', 10, 4);
            $table->decimal('max_discount', 15, 4)->nullable(); // cap for percentage

            $table->decimal('min_order_amount', 15, 4)->default(0);
            $table->unsignedInteger('usage_limit')->nullable();         // total uses allowed
            $table->unsignedInteger('usage_limit_per_customer')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            $table->boolean('applies_to_all_products')->default(true);
            $table->json('product_ids')->nullable();
            $table->json('category_ids')->nullable();
            $table->json('customer_group_ids')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['business_id', 'code']);
            $table->index(['business_id', 'is_active']);
            $table->index('expires_at');
        });

        // ─── Online Orders ─────────────────────────────────────────────────
        // Separate from `sales` to track the full eCommerce lifecycle
        Schema::create('online_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            // After acceptance this links to a sale record
            $table->foreignId('sale_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('order_number', 60)->unique();
            $table->string('channel', 30)->default('website'); // website, qr_menu, app

            $table->enum('status', [
                'pending', 'accepted', 'preparing', 'ready',
                'out_for_delivery', 'delivered', 'cancelled', 'refunded'
            ])->default('pending');

            $table->enum('fulfillment_type', ['delivery', 'pickup', 'dine_in'])
                  ->default('delivery');

            // Customer snapshot
            $table->string('customer_name');
            $table->string('customer_phone', 30)->nullable();
            $table->string('customer_email')->nullable();

            // Delivery address
            $table->string('delivery_address')->nullable();
            $table->string('delivery_city', 100)->nullable();
            $table->string('delivery_state', 100)->nullable();
            $table->string('delivery_postal_code', 20)->nullable();
            $table->string('delivery_country', 80)->nullable();
            $table->decimal('delivery_lat', 10, 7)->nullable();
            $table->decimal('delivery_lng', 10, 7)->nullable();

            // Financials
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('delivery_fee', 15, 4)->default(0);
            $table->decimal('grand_total', 15, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('promocode', 50)->nullable();

            // Payment
            $table->enum('payment_method', [
                'cash_on_delivery', 'card', 'stripe',
                'paypal', 'mobile_banking', 'pay_on_pickup'
            ])->default('cash_on_delivery');
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->string('gateway_txn_id')->nullable();

            // Scheduling
            $table->timestamp('scheduled_for')->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('tracking_url')->nullable();

            $table->unsignedSmallInteger('estimated_minutes')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->text('customer_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'payment_status']);
            $table->index(['business_id', 'created_at']);
            $table->index(['business_id', 'channel']);
        });

        Schema::create('online_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variation_id')->nullable()->constrained()->nullOnDelete();

            $table->string('product_name');
            $table->string('product_sku', 100)->nullable();
            $table->string('product_image')->nullable();
            $table->json('variation_attributes')->nullable();

            $table->decimal('unit_price', 15, 4);
            $table->decimal('quantity', 15, 4);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('subtotal', 15, 4);

            $table->text('notes')->nullable();   // customer special instructions per item
            $table->timestamps();

            $table->index('online_order_id');
        });

        // ─── Order Tracking Events ─────────────────────────────────────────
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->morphs('orderable');          // online_orders or kds_orders
            $table->string('status', 40);
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // $table->index(['orderable_type', 'orderable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('online_order_items');
        Schema::dropIfExists('online_orders');
        Schema::dropIfExists('promocodes');
    }
};
