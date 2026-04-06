<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('name');
            $table->string('code', 50)->nullable();          // coupon code
            $table->enum('type', ['percentage', 'fixed', 'bogo', 'bundle'])->default('percentage');
            $table->decimal('value', 15, 4);                 // 10.00 = 10% or $10

            // Applicability
            $table->enum('applies_to', ['all', 'category', 'product', 'brand'])->default('all');
            $table->json('applicable_ids')->nullable();      // product/category IDs

            // Conditions
            $table->decimal('minimum_order_amount', 15, 4)->nullable();
            $table->decimal('maximum_discount_amount', 15, 4)->nullable(); // cap for %
            $table->integer('usage_limit')->nullable();      // total uses allowed
            $table->integer('usage_per_customer')->nullable();
            $table->integer('used_count')->default(0);

            $table->boolean('is_active')->default(true);
            $table->boolean('requires_coupon_code')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active', 'starts_at', 'expires_at'], 'idx_discounts_active');
            $table->unique(['tenant_id', 'code'], 'uq_discounts_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
