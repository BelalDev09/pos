<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('tax_number', 100)->nullable();

            // Loyalty & credit
            $table->decimal('loyalty_points', 15, 4)->default(0);
            $table->decimal('credit_balance', 15, 4)->default(0);  // store credit
            $table->decimal('total_purchases', 15, 4)->default(0); // lifetime value

            $table->enum('tier', ['standard', 'silver', 'gold', 'platinum'])->default('standard');
            $table->integer('total_orders')->default(0);
            $table->timestamp('last_purchase_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_walk_in')->default(false);  // default walk-in customer per tenant

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'email']);
            $table->index(['tenant_id', 'phone']);
            $table->index(['tenant_id', 'tier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
