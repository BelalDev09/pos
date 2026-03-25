<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Expense Categories ────────────────────────────────────────────
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('color', 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('business_id');
        });

        // ─── Expenses ──────────────────────────────────────────────────────
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->string('reference_no', 60)->unique();
            $table->string('title', 150);
            $table->text('description')->nullable();

            $table->decimal('amount', 15, 4);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_method', [
                'cash',
                'card',
                'bank_transfer',
                'cheque',
                'other'
            ])->default('cash');

            $table->date('expense_date');
            $table->string('attachment')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence', 30)->nullable(); // daily, weekly, monthly
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'expense_date']);
            $table->index(['business_id', 'expense_category_id']);
        });

        // ─── CMS Banners ───────────────────────────────────────────────────
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('title', 150);
            $table->text('subtitle')->nullable();
            $table->string('image');
            $table->string('mobile_image')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_text', 80)->nullable();
            $table->string('button_color', 7)->nullable();
            $table->string('text_color', 7)->nullable();

            $table->enum('position', [
                'homepage_hero',
                'homepage_secondary',
                'pos_screen',
                'category_page',
                'product_page'
            ])->default('homepage_hero');

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'position', 'is_active']);
        });

        // ─── Business Settings (key-value store) ──────────────────────────
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('group', 60)->default('general');
            $table->timestamps();

            $table->unique(['business_id', 'key']);
            $table->index(['business_id', 'group']);
        });

        // ─── Invoice Templates ─────────────────────────────────────────────
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('template', 30)->default('default'); // default, thermal, a4
            $table->string('logo')->nullable();
            $table->text('header_text')->nullable();
            $table->text('footer_text')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_tax')->default(true);
            $table->boolean('show_discount')->default(true);
            $table->boolean('show_customer')->default(true);
            $table->boolean('show_barcode')->default(false);
            $table->string('primary_color', 7)->default('#1d4ed8');
            $table->string('font', 40)->default('sans-serif');
            $table->timestamps();

            $table->unique('business_id');
        });

        // ─── Notification Channels ─────────────────────────────────────────
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->string('event', 80);    // low_stock, new_order, sale_complete
            $table->boolean('email')->default(true);
            $table->boolean('sms')->default(false);
            $table->boolean('push')->default(false);
            $table->boolean('in_app')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'user_id', 'event']);
        });

        // ─── Audit Log ─────────────────────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('event', 60);            // created, updated, deleted, login
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->string('url', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'event']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['business_id', 'created_at']);
        });

        // ─── Notifications (in-app) ────────────────────────────────────────
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // $table->index(['notifiable_type', 'notifiable_id']);
        });

        // ─── Cache & Jobs (standard Laravel) ──────────────────────────────
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('invoice_settings');
        Schema::dropIfExists('business_settings');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('payments');
    }
};
