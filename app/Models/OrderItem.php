<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_barcode',
        'variant_name',
        'quantity',
        'unit_price',
        'unit_cost',
        'discount_amount',
        'discount_percent',
        'tax_rate',
        'tax_amount',
        'total',
        'quantity_refunded',
        'is_refunded',
    ];

    protected $casts = [
        'quantity'          => 'decimal:4',
        'unit_price'        => 'decimal:4',
        'unit_cost'         => 'decimal:4',
        'discount_amount'   => 'decimal:4',
        'discount_percent'  => 'decimal:4',
        'tax_rate'          => 'decimal:4',
        'tax_amount'        => 'decimal:4',
        'total'             => 'decimal:4',
        'quantity_refunded' => 'decimal:4',
        'is_refunded'       => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getProfitAttribute(): float
    {
        return ((float) $this->unit_price - (float) $this->unit_cost)
            * (float) $this->quantity;
    }
}
