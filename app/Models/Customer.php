<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'date_of_birth',
        'gender',
        'tax_number',
        'loyalty_points',
        'credit_balance',
        'total_purchases',
        'tier',
        'total_orders',
        'last_purchase_at',
        'notes',
        'is_active',
        'is_walk_in',
    ];

    protected $casts = [
        'date_of_birth'    => 'date',
        'loyalty_points'   => 'decimal:4',
        'credit_balance'   => 'decimal:4',
        'total_purchases'  => 'decimal:4',
        'total_orders'     => 'integer',
        'is_active'        => 'boolean',
        'is_walk_in'       => 'boolean',
        'last_purchase_at' => 'datetime',
    ];

    // ── Relationships 

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ── Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTier($query, string $tier)
    {
        return $query->where('tier', $tier);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    // ── Accessors 

    public function getAveragePurchaseAttribute(): float
    {
        if ($this->total_orders === 0) {
            return 0.0;
        }
        return round((float) $this->total_purchases / $this->total_orders, 4);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->is_walk_in ? 'Walk-in Customer' : $this->name;
    }

    // ── Helpers 

    public function recalculateTier(): void
    {
        $tier = match (true) {
            $this->total_purchases >= 10000 => 'platinum',
            $this->total_purchases >= 5000  => 'gold',
            $this->total_purchases >= 1000  => 'silver',
            default                          => 'standard',
        };

        $this->update(['tier' => $tier]);
    }
}
