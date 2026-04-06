<?php

namespace App\Traits;

use App\Scopes\TenantScope;

trait BelongsToTenant
{
    /**
     * Boot the trait — auto-applies TenantScope and auto-fills tenant_id on creation.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Auto-apply global scope to every query
        static::addGlobalScope(new TenantScope());

        // Auto-fill tenant_id when creating a new record
        static::creating(function (self $model) {
            if (empty($model->tenant_id) && auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * Bypass the tenant scope for a single query (use with extreme caution).
     */
    public static function withoutTenantScope(): \Illuminate\Database\Eloquent\Builder
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}
