<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('processed_by')->constrained('users')->restrictOnDelete();

            $table->enum('method', [
                'cash',
                'card',
                'mobile_money',
                'bank_transfer',
                'loyalty_points',
                'store_credit',
                'cheque',
                'other'
            ]);

            $table->decimal('amount', 15, 4);
            $table->decimal('tendered', 15, 4)->nullable();    // cash tendered
            $table->decimal('change_given', 15, 4)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 20, 8)->default(1.00000000);

            // Card/digital payment reference
            $table->string('transaction_reference', 200)->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('card_type', 50)->nullable();      // visa, mastercard, amex

            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');

            $table->text('notes')->nullable();
            $table->timestamp('paid_at');

            // NO soft deletes — payments are immutable financial records
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['tenant_id', 'method', 'paid_at'], 'idx_payments_method_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
