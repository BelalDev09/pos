<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('type', [
                'percentage_off',   // 20% off selected products
                'fixed_off',        // $5 off total
                'buy_x_get_y',      // Buy 2 get 1 free
                'bundle',           // 3 for price of 2
                'free_shipping',    // free delivery
            ]);

            $table->json('conditions')->nullable();          // rules engine conditions
            $table->json('actions')->nullable();             // discount actions

            $table->boolean('is_active')->default(true);
            $table->boolean('is_automatic')->default(false); // auto-apply or requires trigger
            $table->integer('priority')->default(0);         // lower = higher priority
            $table->boolean('is_exclusive')->default(false); // cannot combine with others

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active', 'starts_at', 'expires_at'], 'idx_promotions_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
