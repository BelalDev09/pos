<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = $this->resolveTenantId();

        if ($tenantId !== null) {
            $builder->where(
                $model->getTable() . '.tenant_id',
                $tenantId
            );
        }
        // ✅ If tenantId is null (unauthenticated or super admin) — do NOT apply scope
        // This prevents the 500 crash on login page, welcome page, etc.
    }

    private function resolveTenantId(): ?int
    {
        // ✅ Guard: auth() facade can throw if not yet booted
        try {
            // 1. Web user
            if (auth()->check()) {
                $user = auth()->user();
                // Super admins see everything — no scope
                if ($user->is_super_admin) {
                    return null;
                }
                return $user->tenant_id;
            }

            // 2. Sanctum API token
            if (request()->bearerToken()) {
                $user = auth('sanctum')->user();
                if ($user && !$user->is_super_admin) {
                    return $user->tenant_id;
                }
            }
        } catch (\Exception $e) {
            // Auth not booted yet (e.g. during migrations/seeders)
            return null;
        }

        return null;
    }
}
