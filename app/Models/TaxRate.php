<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxRate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'rate',
        'type',
        'is_inclusive',
        'is_default',
        'is_active',
        'description',
    ];

    protected $casts = [
        'rate'         => 'decimal:4',
        'is_inclusive' => 'boolean',
        'is_default'   => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate tax amount from a given price.
     */
    public function calculateTax(float $price): float
    {
        if ($this->is_inclusive) {
            // Extract tax from inclusive price
            return round($price - ($price / (1 + $this->rate / 100)), 4);
        }

        return round($price * ($this->rate / 100), 4);
    }
}
