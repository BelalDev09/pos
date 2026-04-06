<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_variant_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'tax_rate',
        'tax_amount',
        'discount_rate',
        'discount_amount',
        'total',
        'batch_number',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'quantity_ordered'  => 'decimal:4',
        'quantity_received' => 'decimal:4',
        'unit_cost'         => 'decimal:4',
        'tax_rate'          => 'decimal:4',
        'tax_amount'        => 'decimal:4',
        'discount_rate'     => 'decimal:4',
        'discount_amount'   => 'decimal:4',
        'total'             => 'decimal:4',
        'expiry_date'       => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getRemainingQuantityAttribute(): float
    {
        return max(0, (float) $this->quantity_ordered - (float) $this->quantity_received);
    }
}
