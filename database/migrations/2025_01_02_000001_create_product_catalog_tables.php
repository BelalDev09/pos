<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Categories ────────────────────────────────────────────────────
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();

            $table->string('name', 120);
            $table->string('slug', 140);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'slug']);
            $table->index(['business_id', 'parent_id']);
            $table->index(['business_id', 'is_active']);
        });

        // ─── Units ─────────────────────────────────────────────────────────
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);          // "Kilogram"
            $table->string('short_name', 20);    // "kg"
            $table->string('base_unit', 20)->nullable(); // for conversion e.g. "g"
            $table->decimal('conversion_factor', 10, 4)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['business_id', 'short_name']);
            $table->index('business_id');
        });

        // ─── Tax Rates ─────────────────────────────────────────────────────
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);          // "VAT 15%"
            $table->decimal('rate', 5, 2);       // 15.00
            $table->enum('type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('business_id');
        });

        // ─── Products ──────────────────────────────────────────────────────
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();
            $table->foreignId('unit_id')
                  ->nullable()
                  ->constrained('units')
                  ->nullOnDelete();
            $table->foreignId('tax_rate_id')
                  ->nullable()
                  ->constrained('tax_rates')
                  ->nullOnDelete();

            // Identity
            $table->string('name');
            $table->string('slug');
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->enum('barcode_type', ['EAN13', 'EAN8', 'UPC', 'CODE128', 'QR'])
                  ->nullable();

            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();       // additional images

            // Pricing
            $table->decimal('cost_price', 15, 4)->default(0);
            $table->decimal('selling_price', 15, 4)->default(0);
            $table->decimal('wholesale_price', 15, 4)->nullable();
            $table->decimal('discount_price', 15, 4)->nullable();
            $table->timestamp('discount_starts_at')->nullable();
            $table->timestamp('discount_ends_at')->nullable();

            // Inventory
            $table->decimal('stock_quantity', 15, 4)->default(0);
            $table->decimal('alert_quantity', 15, 4)->default(5);
            $table->decimal('reorder_quantity', 15, 4)->nullable();
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_negative_stock')->default(false);

            // Type flags
            $table->boolean('has_variations')->default(false);
            $table->boolean('is_composite')->default(false); // for recipe-based products
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('sold_by_weight')->default(false);

            // eCommerce
            $table->boolean('show_in_store')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'slug']);
            $table->index(['business_id', 'category_id']);
            $table->index(['business_id', 'is_active']);
            $table->index(['business_id', 'barcode']);
            $table->index(['business_id', 'sku']);
            $table->index(['business_id', 'show_in_store']);
        });

        // ─── Product Variations ────────────────────────────────────────────
        // e.g. "Large / Red" with its own price and stock
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            $table->string('name');               // "Large Red"
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->json('attributes');           // {"size":"L","color":"Red"}

            $table->decimal('cost_price', 15, 4)->nullable();
            $table->decimal('selling_price', 15, 4)->nullable();
            $table->decimal('stock_quantity', 15, 4)->default(0);
            $table->decimal('alert_quantity', 15, 4)->default(5);

            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'sku']);
            $table->index(['product_id', 'is_active']);
            $table->index(['business_id', 'barcode']);
        });

        // ─── Product Composites (recipes / bundles) ────────────────────────
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete()
                  ->comment('The composite/recipe product');
            $table->foreignId('ingredient_product_id')
                  ->constrained('products')
                  ->cascadeOnDelete()
                  ->comment('Raw material or sub-product');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->timestamps();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('products');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('units');
        Schema::dropIfExists('categories');
    }
};
