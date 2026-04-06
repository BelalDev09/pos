<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'tax_number',
        'credit_limit',
        'outstanding_balance',
        'payment_terms',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'credit_limit'        => 'decimal:4',
        'outstanding_balance' => 'decimal:4',
        'is_active'           => 'boolean',
    ];

    // ── Relationships 

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // ── Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Accessors 

    public function getAvailableCreditAttribute(): float
    {
        return max(0, (float) $this->credit_limit - (float) $this->outstanding_balance);
    }
}
