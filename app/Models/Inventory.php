<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory, BelongsToTenant;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'store_id',
        'tenant_id',
        'quantity',
        'reserved_quantity',
        'reorder_level',
        'reorder_quantity',
        'location_code',
        'last_counted_at',
        'updated_at',
    ];

    protected $casts = [
        'quantity'          => 'decimal:4',
        'reserved_quantity' => 'decimal:4',
        'reorder_level'     => 'decimal:4',
        'reorder_quantity'  => 'decimal:4',
        'last_counted_at'   => 'datetime',
        'updated_at'        => 'datetime',
    ];

    // ── Relationships 

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    // ── Scopes 

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level')
            ->where('reorder_level', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    // ── Accessors 

    public function getAvailableQuantityAttribute(): float
    {
        return max(0, (float) $this->quantity - (float) $this->reserved_quantity);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->reorder_level > 0 && $this->quantity <= $this->reorder_level;
    }
}
