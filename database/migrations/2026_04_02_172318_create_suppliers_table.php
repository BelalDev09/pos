<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('contact_id', 20)->nullable()->index();
            $table->string('business_name');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('tax_number', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->unsignedSmallInteger('payment_terms')->nullable()->comment('days');
            $table->decimal('opening_balance', 15, 4)->default(0);
            $table->decimal('advance_balance', 15, 4)->default(0);
            $table->decimal('total_purchase_due', 15, 4)->default(0);
            $table->decimal('total_return_due', 15, 4)->default(0);
            $table->decimal('credit_limit', 15, 4)->default(0);
            $table->decimal('outstanding_balance', 15, 4)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
