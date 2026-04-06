<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'barcode',
        'attributes',
        'cost_price',
        'selling_price',
        'price_adjustment',
        'weight',
        'image',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'attributes'       => 'array',
        'cost_price'       => 'decimal:4',
        'selling_price'    => 'decimal:4',
        'price_adjustment' => 'decimal:4',
        'weight'           => 'decimal:4',
        'is_default'       => 'boolean',
        'is_active'        => 'boolean',
    ];

    // ── Relationships 

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Accessors 

    public function getEffectivePriceAttribute(): float
    {
        if ($this->selling_price !== null) {
            return (float) $this->selling_price;
        }
        return (float) ($this->product->selling_price + $this->price_adjustment);
    }

    public function getEffectiveCostAttribute(): float
    {
        return (float) ($this->cost_price ?? $this->product->cost_price);
    }
}
