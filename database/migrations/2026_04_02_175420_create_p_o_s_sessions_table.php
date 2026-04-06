<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('p_o_s_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('register_id')->constrained('registers')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('opened_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', ['open', 'closed', 'suspended'])->default('open');

            // Float amounts
            $table->decimal('opening_float', 15, 4)->default(0);
            $table->decimal('closing_float', 15, 4)->nullable();
            $table->decimal('expected_cash', 15, 4)->nullable();    // calculated from sales
            $table->decimal('cash_difference', 15, 4)->nullable();  // closing - expected

            // Session totals (denormalized for fast Z-report)
            $table->decimal('total_sales', 15, 4)->default(0);
            $table->decimal('total_refunds', 15, 4)->default(0);
            $table->decimal('total_discounts', 15, 4)->default(0);
            $table->decimal('total_tax', 15, 4)->default(0);
            $table->integer('total_transactions')->default(0);

            $table->json('payment_summary')->nullable();    // {"cash": 500, "card": 300}

            $table->text('notes')->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'store_id', 'status'], 'idx_p_o_s_sessions_active');
            $table->index(['register_id', 'status']);
            $table->index(['opened_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('p_o_s_sessions');
    }
};
