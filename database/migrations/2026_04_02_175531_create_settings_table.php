<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('store_id')->nullable()->constrained('stores')->cascadeOnDelete();

            $table->string('group', 100)->default('general'); // general|pos|receipt|inventory|tax
            $table->string('key', 150);
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string');    // string|boolean|integer|json|array

            $table->timestamps();

            // null tenant_id = global system settings
            $table->unique(['tenant_id', 'store_id', 'group', 'key'], 'uq_settings_key');
            $table->index(['tenant_id', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
