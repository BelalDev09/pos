<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'custom_domain',
        'plan',
        'trial_ends_at',
        'subscription_ends_at',
        'is_active',
        'business_type',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'timezone',
        'locale',
        'currency',
        'settings',
    ];

    protected $casts = [
        'is_active'               => 'boolean',
        'trial_ends_at'           => 'datetime',
        'subscription_ends_at'    => 'datetime',
        'settings'                => 'array',
    ];

    // ── Relationships 

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function currencies(): HasMany
    {
        return $this->hasMany(Currency::class);
    }

    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function headquarters(): HasOne
    {
        return $this->hasOne(Store::class)->where('is_headquarters', true);
    }

    // ── Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnPlan($query, string $plan)
    {
        return $query->where('plan', $plan);
    }

    // ── Accessors 

    public function getIsOnTrialAttribute(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getIsSubscriptionActiveAttribute(): bool
    {
        return $this->subscription_ends_at && $this->subscription_ends_at->isFuture();
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
