<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'type',
        'value',
        'applies_to',
        'applicable_ids',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit',
        'usage_per_customer',
        'used_count',
        'is_active',
        'requires_coupon_code',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value'                   => 'decimal:4',
        'minimum_order_amount'    => 'decimal:4',
        'maximum_discount_amount' => 'decimal:4',
        'applicable_ids'          => 'array',
        'is_active'               => 'boolean',
        'requires_coupon_code'    => 'boolean',
        'starts_at'               => 'datetime',
        'expires_at'              => 'datetime',
        'used_count'              => 'integer',
        'usage_limit'             => 'integer',
    ];

    // ── Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(
                fn($q) =>
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now())
            )
            ->where(
                fn($q) =>
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now())
            );
    }

    public function scopeAutomaticDiscounts($query)
    {
        return $query->active()->where('requires_coupon_code', false);
    }

    // ── Helpers 

    public function isUsageLimitReached(): bool
    {
        return $this->usage_limit !== null && $this->used_count >= $this->usage_limit;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        if ($this->minimum_order_amount && $orderTotal < $this->minimum_order_amount) {
            return 0.0;
        }

        $discount = match ($this->type) {
            'percentage' => $orderTotal * ($this->value / 100),
            'fixed'      => (float) $this->value,
            default      => 0.0,
        };

        if ($this->maximum_discount_amount) {
            $discount = min($discount, (float) $this->maximum_discount_amount);
        }

        return round($discount, 4);
    }
}
