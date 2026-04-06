<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('action', 100);                   // created, updated, deleted, login, etc.
            $table->string('module', 100);                   // Product, Order, Customer, etc.
            $table->string('description');

            // Polymorphic subject
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            // Context
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('created_at')->nullable();

            // No soft deletes, no updated_at — logs are immutable
            $table->index(['tenant_id', 'user_id', 'created_at'], 'idx_logs_user');
            $table->index(['tenant_id', 'module', 'created_at'], 'idx_logs_module');
            $table->index(['subject_type', 'subject_id'], 'idx_logs_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
