<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Starter, Pro, Enterprise
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price_monthly');        // stored in cents
            $table->unsignedInteger('price_yearly');
            $table->string('currency', 3)->default('USD');

            // Feature limits (null = unlimited)
            $table->unsignedSmallInteger('max_users')->nullable();
            $table->unsignedSmallInteger('max_products')->nullable();
            $table->unsignedSmallInteger('max_locations')->nullable();
            $table->boolean('pos_enabled')->default(true);
            $table->boolean('ecommerce_enabled')->default(false);
            $table->boolean('restaurant_enabled')->default(false);
            $table->boolean('api_enabled')->default(false);
            $table->boolean('reports_advanced')->default(false);

            $table->json('features')->nullable();            // extra feature flags
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
