<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->nullOnDelete();

            // Core identity
            $table->string('name');
            $table->string('slug', 200);
            $table->string('sku', 100)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Pricing
            $table->decimal('cost_price', 15, 4)->default(0);
            $table->decimal('selling_price', 15, 4)->default(0);
            $table->decimal('wholesale_price', 15, 4)->nullable();
            $table->decimal('min_selling_price', 15, 4)->nullable(); // floor price

            // Physical
            $table->string('unit', 30)->default('pcs');      // pcs, kg, litre, box
            $table->decimal('weight', 10, 4)->nullable();    // in grams
            $table->string('weight_unit', 10)->nullable();

            // Inventory control
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_negative_stock')->default(false);
            $table->boolean('has_variants')->default(false);

            // POS display
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_pos_visible')->default(true);
            $table->integer('sort_order')->default(0);

            // Type
            $table->enum('product_type', ['standard', 'service', 'composite'])
                ->default('standard');

            // Expiry (pharmacy/food)
            $table->boolean('track_expiry')->default(false);
            $table->boolean('track_batch')->default(false);

            // Extra flexible fields
            $table->json('meta')->nullable();                // SEO, extra attributes

            $table->timestamps();
            $table->softDeletes();

            // Indexes — these are the most queried columns in a POS
            $table->index(['tenant_id', 'is_active', 'is_pos_visible'], 'idx_products_pos');
            $table->index(['tenant_id', 'category_id'], 'idx_products_category');
            $table->index(['tenant_id', 'brand_id'], 'idx_products_brand');
            $table->unique(['tenant_id', 'sku'], 'uq_products_sku');
            $table->unique(['tenant_id', 'barcode'], 'uq_products_barcode');
            $table->unique(['tenant_id', 'slug'], 'uq_products_slug');
        });

        // FULLTEXT for fast name/SKU/barcode search in POS
        DB::statement('ALTER TABLE products ADD FULLTEXT idx_products_search (name, sku, barcode)');
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
