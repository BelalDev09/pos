<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'type',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'phone',
        'email',
        'currency',
        'timezone',
        'tax_inclusive',
        'default_tax_rate',
        'is_active',
        'is_headquarters',
        'receipt_settings',
    ];

    protected $casts = [
        'tax_inclusive'    => 'boolean',
        'is_active'        => 'boolean',
        'is_headquarters'  => 'boolean',
        'default_tax_rate' => 'decimal:4',
        'latitude'         => 'decimal:7',
        'longitude'        => 'decimal:7',
        'receipt_settings' => 'array',
    ];

    // ── Relationships 

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function registers(): HasMany
    {
        return $this->hasMany(Register::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function posSessions(): HasMany
    {
        return $this->hasMany(PosSession::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // ── Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
