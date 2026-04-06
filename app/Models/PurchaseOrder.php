<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'store_id',
        'supplier_id',
        'created_by',
        'approved_by',
        'po_number',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'total',
        'amount_paid',
        'currency',
        'exchange_rate',
        'payment_status',
        'payment_terms',
        'due_date',
        'expected_delivery_date',
        'notes',
        'supplier_reference',
        'ordered_at',
        'received_at',
    ];

    protected $casts = [
        'subtotal'               => 'decimal:4',
        'tax_amount'             => 'decimal:4',
        'shipping_cost'          => 'decimal:4',
        'discount_amount'        => 'decimal:4',
        'total'                  => 'decimal:4',
        'amount_paid'            => 'decimal:4',
        'exchange_rate'          => 'decimal:8',
        'due_date'               => 'date',
        'expected_delivery_date' => 'date',
        'ordered_at'             => 'datetime',
        'received_at'            => 'datetime',
    ];

    // ── Relationships 

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // ── Scopes 

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'approved', 'ordered']);
    }

    // ── Accessors 

    public function getAmountDueAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->amount_paid);
    }

    public function getIsFullyReceivedAttribute(): bool
    {
        return $this->items->every(
            fn($item) =>
            (float) $item->quantity_received >= (float) $item->quantity_ordered
        );
    }
}
