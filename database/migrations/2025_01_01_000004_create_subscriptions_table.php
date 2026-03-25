<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();

            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', [
                'trialing', 'active', 'past_due', 'cancelled', 'expired'
            ])->default('trialing');

            // Payment gateway reference
            $table->string('gateway', 30)->nullable();       // stripe, paypal, manual
            $table->string('gateway_subscription_id')->nullable();
            $table->string('gateway_customer_id')->nullable();

            $table->unsignedInteger('amount');               // cents
            $table->string('currency', 3)->default('USD');

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index('status');
            $table->index('current_period_end');
        });

        // Track every invoice / payment attempt for a subscription
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('invoice_number')->unique();
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'open', 'paid', 'uncollectible', 'void'])
                  ->default('open');

            $table->string('gateway_invoice_id')->nullable();
            $table->string('hosted_invoice_url')->nullable();
            $table->string('invoice_pdf')->nullable();

            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('subscriptions');
    }
};
