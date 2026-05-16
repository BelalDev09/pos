<?php

namespace App\Repositories;

use App\Models\Discount;
use Illuminate\Database\Eloquent\Collection;

class DiscountRepository
{
    public function __construct(
        private readonly Discount $model
    ) {}

    public function findActiveCouponByCode(int $tenantId, string $code): ?Discount
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('code', $code)
            ->where('requires_coupon_code', true)
            ->active()
            ->first();
    }

    public function getAutomaticDiscounts(int $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->automaticDiscounts()
            ->orderByDesc('value')
            ->get();
    }

    public function incrementUsage(int $tenantId, array $discountIds): void
    {
        $ids = array_values(array_unique(array_filter($discountIds)));

        if ($ids === []) {
            return;
        }

        $this->model
            ->where('tenant_id', $tenantId)
            ->whereIn('id', $ids)
            ->increment('used_count');
    }
}
