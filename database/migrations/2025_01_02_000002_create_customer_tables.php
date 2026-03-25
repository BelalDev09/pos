<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Customers ─────────────────────────────────────────────────────
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('email', 191)->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('avatar')->nullable();

            // Address
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 80)->nullable();

            // Finance
            $table->decimal('credit_limit', 15, 4)->default(0);
            $table->decimal('outstanding_balance', 15, 4)->default(0);
            $table->decimal('total_spent', 15, 4)->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->decimal('loyalty_points', 15, 2)->default(0);

            // Group / tier
            $table->string('customer_group', 60)->nullable(); // VIP, Wholesale, Walk-in
            $table->decimal('discount_percentage', 5, 2)->default(0);

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_purchase_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'email']);
            $table->index(['business_id', 'phone']);
            $table->index(['business_id', 'is_active']);
            $table->index(['business_id', 'customer_group']);
        });

        // ─── Loyalty Points Ledger ─────────────────────────────────────────
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->enum('type', ['earned', 'redeemed', 'adjusted', 'expired']);
            $table->decimal('points', 15, 2);      // positive = earn, negative = redeem
            $table->decimal('balance_after', 15, 2);

            // Reference
            $table->string('reference_type')->nullable(); // sale, purchase, manual
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('note')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['customer_id', 'type']);
            $table->index(['business_id', 'customer_id']);
            $table->index('expires_at');
        });

        // ─── Customer Groups ───────────────────────────────────────────────
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('loyalty_multiplier', 5, 2)->default(1);
            $table->decimal('credit_limit', 15, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('business_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_groups');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('customers');
    }
};
