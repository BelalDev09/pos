<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('name');
            $table->string('slug', 100)->unique();           // URL-safe identifier
            $table->string('subdomain', 100)->unique();      // acme.yourpos.com
            $table->string('custom_domain')->nullable();     // pos.acmecorp.com

            // Subscription
            $table->enum('plan', ['trial', 'starter', 'professional', 'enterprise'])
                ->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->boolean('is_active')->default(true);

            // Business identity
            $table->string('business_type')
                ->default('retail');                       // retail|restaurant|pharmacy|supermarket
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('timezone', 50)->default('UTC');
            $table->string('locale', 10)->default('en');
            $table->string('currency', 3)->default('USD');

            // Flexible configuration per tenant
            $table->json('settings')->nullable();            // receipt_footer, logo_url, etc.

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
            $table->index('plan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
