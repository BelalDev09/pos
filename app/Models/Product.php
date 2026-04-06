<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
// use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'brand_id',
        'tax_rate_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'image',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'min_selling_price',
        'unit',
        'weight',
        'weight_unit',
        'track_stock',
        'allow_negative_stock',
        'has_variants',
        'is_active',
        'is_featured',
        'is_pos_visible',
        'sort_order',
        'product_type',
        'track_expiry',
        'track_batch',
        'meta',
    ];

    protected $casts = [
        'cost_price'           => 'decimal:4',
        'selling_price'        => 'decimal:4',
        'wholesale_price'      => 'decimal:4',
        'min_selling_price'    => 'decimal:4',
        'weight'               => 'decimal:4',
        'track_stock'          => 'boolean',
        'allow_negative_stock' => 'boolean',
        'has_variants'         => 'boolean',
        'is_active'            => 'boolean',
        'is_featured'          => 'boolean',
        'is_pos_visible'       => 'boolean',
        'track_expiry'         => 'boolean',
        'track_batch'          => 'boolean',
        'meta'                 => 'array',
    ];

    // protected static function booted(): void
    // {
    //     static::observe(ProductObserver::class);
    // }

    // ── Relationships 

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant(): HasOne
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePosVisible($query)
    {
        return $query->where('is_pos_visible', true)->where('is_active', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->whereRaw(
            'MATCH(name, sku, barcode) AGAINST(? IN BOOLEAN MODE)',
            [$term . '*']
        );
    }

    // ── Accessors 

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0) {
            return 0;
        }
        return round(
            (($this->selling_price - $this->cost_price) / $this->cost_price) * 100,
            2
        );
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) $this->selling_price;
    }
}
