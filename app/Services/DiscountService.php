<?php

namespace App\Services;

use App\Models\Discount;
use App\Repositories\DiscountRepository;
use InvalidArgumentException;

class DiscountService
{
    public function __construct(
        private readonly DiscountRepository $discountRepository
    ) {}

    public function applyCouponToCart(array $cart, int $tenantId, string $code): array
    {
        $discount = $this->discountRepository->findActiveCouponByCode($tenantId, $code);

        if (! $discount || $discount->isUsageLimitReached()) {
            throw new InvalidArgumentException('Invalid or expired coupon code.');
        }

        $cart['__coupon'] = $this->snapshot($discount);

        return $cart;
    }

    public function removeCouponFromCart(array $cart): array
    {
        unset($cart['__coupon']);

        return $cart;
    }

    public function calculateCartTotals(array $cart, int $tenantId): array
    {
        $subtotal = 0.0;
        $taxTotal = 0.0;
        $itemDiscountTotal = 0.0;
        $items = [];

        foreach ($cart as $key => $item) {
            if (str_starts_with((string) $key, '__')) {
                continue;
            }

            $lineSubtotal = (float) $item['unit_price'] * (float) $item['quantity'];
            $itemDiscount = $this->calculateManualItemDiscount($item, $lineSubtotal);
            $taxable = max(0, $lineSubtotal - $itemDiscount);
            $lineTax = $taxable * ((float) $item['tax_rate'] / 100);
            $lineTotal = $taxable + $lineTax;

            $subtotal += $lineSubtotal;
            $itemDiscountTotal += $itemDiscount;
            $taxTotal += $lineTax;

            $items[] = array_merge($item, [
                'line_subtotal' => round($lineSubtotal, 4),
                'line_discount' => round($itemDiscount, 4),
                'line_tax' => round($lineTax, 4),
                'line_total' => round($lineTotal, 4),
            ]);
        }

        $discountableSubtotal = max(0, $subtotal - $itemDiscountTotal);
        $couponDiscount = $this->calculateCouponDiscount($cart['__coupon'] ?? null, $items, $discountableSubtotal);
        $automaticDiscounts = $this->calculateAutomaticDiscounts($tenantId, $items, $discountableSubtotal, $couponDiscount);
        $automaticDiscountTotal = array_sum(array_column($automaticDiscounts, 'amount'));
        $orderDiscountTotal = min($discountableSubtotal, $couponDiscount + $automaticDiscountTotal);
        $grandTotal = max(0, $subtotal - $itemDiscountTotal - $orderDiscountTotal + $taxTotal);

        return [
            'items' => $items,
            'coupon' => $cart['__coupon'] ?? null,
            'automatic_discounts' => $automaticDiscounts,
            'applied_discount_ids' => $this->appliedDiscountIds($cart['__coupon'] ?? null, $automaticDiscounts),
            'subtotal' => round($subtotal, 4),
            'item_discounts' => round($itemDiscountTotal, 4),
            'coupon_discount' => round($couponDiscount, 4),
            'automatic_discount' => round($automaticDiscountTotal, 4),
            'discount_total' => round($itemDiscountTotal + $orderDiscountTotal, 4),
            'tax_total' => round($taxTotal, 4),
            'grand_total' => round($grandTotal, 4),
            'item_count' => count($items),
        ];
    }

    public function recordUsage(int $tenantId, array $discountIds): void
    {
        $this->discountRepository->incrementUsage($tenantId, $discountIds);
    }

    private function calculateManualItemDiscount(array $item, float $lineSubtotal): float
    {
        $discount = (float) ($item['discount'] ?? 0);

        if ($discount <= 0) {
            return 0.0;
        }

        $amount = match ($item['discount_type'] ?? 'flat') {
            'percent' => $lineSubtotal * ($discount / 100),
            default => $discount * (float) $item['quantity'],
        };

        return min($lineSubtotal, round($amount, 4));
    }

    private function calculateCouponDiscount(?array $coupon, array $items, float $discountableSubtotal): float
    {
        if (! $coupon || $discountableSubtotal <= 0) {
            return 0.0;
        }

        $eligibleSubtotal = $this->eligibleSubtotal($coupon, $items);

        if ($eligibleSubtotal <= 0 || $eligibleSubtotal < (float) ($coupon['minimum_order_amount'] ?? 0)) {
            return 0.0;
        }

        return $this->calculateOrderDiscountAmount($coupon, $eligibleSubtotal, $discountableSubtotal);
    }

    private function calculateAutomaticDiscounts(
        int $tenantId,
        array $items,
        float $discountableSubtotal,
        float $couponDiscount
    ): array {
        $remainingDiscountable = max(0, $discountableSubtotal - $couponDiscount);
        $applied = [];

        if ($remainingDiscountable <= 0) {
            return $applied;
        }

        foreach ($this->discountRepository->getAutomaticDiscounts($tenantId) as $discount) {
            if ($discount->isUsageLimitReached()) {
                continue;
            }

            $snapshot = $this->snapshot($discount);
            $eligibleSubtotal = min($remainingDiscountable, $this->eligibleSubtotal($snapshot, $items));

            if ($eligibleSubtotal <= 0 || $eligibleSubtotal < (float) ($snapshot['minimum_order_amount'] ?? 0)) {
                continue;
            }

            $amount = $this->calculateOrderDiscountAmount($snapshot, $eligibleSubtotal, $remainingDiscountable);

            if ($amount <= 0) {
                continue;
            }

            $applied[] = [
                'id' => $snapshot['id'],
                'name' => $snapshot['name'],
                'code' => $snapshot['code'],
                'type' => $snapshot['type'],
                'amount' => round($amount, 4),
            ];

            $remainingDiscountable -= $amount;

            if ($remainingDiscountable <= 0) {
                break;
            }
        }

        return $applied;
    }

    private function calculateOrderDiscountAmount(array $discount, float $eligibleSubtotal, float $maxAmount): float
    {
        $amount = match ($discount['type']) {
            'percentage' => $eligibleSubtotal * ((float) $discount['value'] / 100),
            'fixed' => (float) $discount['value'],
            default => 0.0,
        };

        if (! empty($discount['maximum_discount_amount'])) {
            $amount = min($amount, (float) $discount['maximum_discount_amount']);
        }

        return round(min($amount, $eligibleSubtotal, $maxAmount), 4);
    }

    private function eligibleSubtotal(array $discount, array $items): float
    {
        return array_reduce($items, function (float $total, array $item) use ($discount): float {
            if (! $this->isItemEligible($discount, $item)) {
                return $total;
            }

            return $total + max(0, (float) $item['line_subtotal'] - (float) $item['line_discount']);
        }, 0.0);
    }

    private function isItemEligible(array $discount, array $item): bool
    {
        $appliesTo = $discount['applies_to'] ?? 'all';
        $applicableIds = $discount['applicable_ids'] ?? [];

        if ($appliesTo === 'all') {
            return true;
        }

        return match ($appliesTo) {
            'product' => in_array((int) $item['product_id'], $applicableIds, true),
            'category' => in_array((int) ($item['category_id'] ?? 0), $applicableIds, true),
            'brand' => in_array((int) ($item['brand_id'] ?? 0), $applicableIds, true),
            default => false,
        };
    }

    private function appliedDiscountIds(?array $coupon, array $automaticDiscounts): array
    {
        $ids = array_column($automaticDiscounts, 'id');

        if (! empty($coupon['id'])) {
            $ids[] = $coupon['id'];
        }

        return array_values(array_unique(array_filter($ids)));
    }

    private function snapshot(Discount $discount): array
    {
        return [
            'id' => $discount->id,
            'name' => $discount->name,
            'code' => $discount->code,
            'type' => $discount->type,
            'value' => (float) $discount->value,
            'applies_to' => $discount->applies_to,
            'applicable_ids' => array_map('intval', $discount->applicable_ids ?? []),
            'minimum_order_amount' => (float) ($discount->minimum_order_amount ?? 0),
            'maximum_discount_amount' => $discount->maximum_discount_amount !== null
                ? (float) $discount->maximum_discount_amount
                : null,
        ];
    }
}
