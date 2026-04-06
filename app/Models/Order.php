<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, BelongsToTenant;

    // Orders are NEVER soft-deleted — use status changes instead
    protected $fillable = [
        'tenant_id',
        'store_id',
        'register_id',
        'pos_session_id',
        'customer_id',
        'cashier_id',
        'discount_id',
        'order_number',
        'status',
        'source',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'rounding_amount',
        'total',
        'amount_tendered',
        'change_given',
        'total_refunded',
        'currency',
        'discount_code',
        'loyalty_points_used',
        'loyalty_points_earned',
        'notes',
        'internal_notes',
        'offline_id',
        'is_synced',
        'completed_at',
    ];

    protected $casts = [
        'subtotal'             => 'decimal:4',
        'discount_amount'      => 'decimal:4',
        'tax_amount'           => 'decimal:4',
        'rounding_amount'      => 'decimal:4',
        'total'                => 'decimal:4',
        'amount_tendered'      => 'decimal:4',
        'change_given'         => 'decimal:4',
        'total_refunded'       => 'decimal:4',
        'loyalty_points_used'  => 'decimal:4',
        'loyalty_points_earned' => 'decimal:4',
        'is_synced'            => 'boolean',
        'completed_at'         => 'datetime',
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(Register::class);
    }

    public function posSession(): BelongsTo
    {
        return $this->belongsTo(PosSession::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ── Scopes 

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('completed_at', today());
    }

    public function scopeBetweenDates($query, string $from, string $to)
    {
        return $query->whereBetween('completed_at', [$from, $to]);
    }

    // ── Accessors 

    public function getNetAmountAttribute(): float
    {
        return (float) $this->total - (float) $this->total_refunded;
    }
}
