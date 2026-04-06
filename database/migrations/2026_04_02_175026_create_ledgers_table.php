<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->enum('type', [
                'sale',
                'refund',
                'purchase',
                'expense',
                'adjustment',
                'transfer',
                'opening'
            ]);
            $table->decimal('debit', 15, 4)->default(0);
            $table->decimal('credit', 15, 4)->default(0);
            $table->decimal('balance', 15, 4);               // running balance

            // Polymorphic reference
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');

            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['tenant_id', 'store_id', 'recorded_at'], 'idx_ledger_reporting');
            $table->index(['reference_type', 'reference_id'], 'idx_ledger_reference');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
