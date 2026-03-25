<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Floor Sections ────────────────────────────────────────────────
        Schema::create('floor_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);           // "Ground Floor", "Terrace"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('business_id');
        });

        // ─── Tables ────────────────────────────────────────────────────────
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floor_section_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('table_number', 30);
            $table->string('name', 80)->nullable();      // e.g. "Window Table"
            $table->unsignedSmallInteger('capacity')->default(4);

            $table->enum('shape', ['square', 'round', 'rectangle'])->default('square');
            // position for floor-plan editor
            $table->unsignedSmallInteger('pos_x')->default(0);
            $table->unsignedSmallInteger('pos_y')->default(0);
            $table->unsignedSmallInteger('width')->default(80);
            $table->unsignedSmallInteger('height')->default(80);

            $table->enum('status', [
                'available', 'occupied', 'reserved', 'cleaning', 'inactive'
            ])->default('available');

            $table->string('qr_code_path')->nullable();
            $table->string('qr_token', 64)->unique()->nullable(); // for QR menu URL
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'table_number']);
            $table->index(['business_id', 'status']);
            $table->index('qr_token');
        });

        // ─── Table Bookings / Reservations ────────────────────────────────
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_table_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('reference_no', 40)->unique();
            $table->string('guest_name', 120);
            $table->string('guest_phone', 30)->nullable();
            $table->string('guest_email')->nullable();

            $table->unsignedSmallInteger('guest_count')->default(2);
            $table->timestamp('booked_for');        // reservation date/time
            $table->unsignedSmallInteger('duration_minutes')->default(90);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();

            $table->enum('status', [
                'pending', 'confirmed', 'seated', 'completed', 'no_show', 'cancelled'
            ])->default('pending');

            $table->text('notes')->nullable();
            $table->string('source', 30)->default('walk_in'); // online, phone, walk_in
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index(['business_id', 'booked_for']);
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'restaurant_table_id']);
        });

        // ─── KDS Orders (Kitchen Display System) ──────────────────────────
        Schema::create('kds_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('restaurant_table_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->string('order_number', 40);
            $table->string('ticket_number', 20)->nullable();   // short kitchen-facing number

            $table->enum('status', [
                'pending', 'preparing', 'ready', 'served', 'cancelled'
            ])->default('pending');

            $table->string('station', 60)->nullable(); // "Grill", "Cold", "Beverage"
            $table->unsignedSmallInteger('priority')->default(0);

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->unsignedSmallInteger('prep_time_estimate')->nullable(); // minutes

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'station']);
            $table->index(['business_id', 'created_at']);
        });

        Schema::create('kds_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kds_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->string('product_name');
            $table->decimal('quantity', 10, 2);
            $table->text('modifiers')->nullable();   // "no onion, extra spicy"

            $table->enum('status', ['waiting', 'preparing', 'ready', 'served'])
                  ->default('waiting');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('kds_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kds_order_items');
        Schema::dropIfExists('kds_orders');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('restaurant_tables');
        Schema::dropIfExists('floor_sections');
    }
};
