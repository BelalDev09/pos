<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('po_number', 50)->unique();
            $table->enum('status', [
                'draft',
                'pending',
                'approved',
                'ordered',
                'partial',
                'received',
                'cancelled'
            ])->default('draft');

            // Financials
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('shipping_cost', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('amount_paid', 15, 4)->default(0);
            $table->decimal('amount_due', 15, 4)->storedAs('total - amount_paid');

            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 20, 8)->default(1.00000000);

            // Payment
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('payment_terms', ['immediate', 'net_7', 'net_15', 'net_30', 'net_60'])
                ->default('net_30');
            $table->date('due_date')->nullable();

            $table->date('expected_delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('supplier_reference')->nullable();  // supplier's invoice/reference number

            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'store_id', 'status'], 'idx_po_tenant_status');
            $table->index(['tenant_id', 'supplier_id'], 'idx_po_supplier');
            $table->index(['tenant_id', 'created_at'], 'idx_po_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
