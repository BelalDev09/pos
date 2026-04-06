<?php

namespace App\Services\Pos;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;

class CartService
{
    private string $cartKey;

    public function __construct(
        private readonly int    $tenantId,
        private readonly int    $storeId,
        private readonly string $sessionId
    ) {
        $this->cartKey = "cart:{$tenantId}:{$storeId}:{$sessionId}";
    }

    // ── Cart State 

    public function getCart(): array
    {
        return Cache::get($this->cartKey, []);
    }

    private function saveCart(array $cart): void
    {
        Cache::put($this->cartKey, $cart, now()->addHours(8));
    }

    public function clear(): void
    {
        Cache::forget($this->cartKey);
    }

    // ── Item Operations 

    public function addItem(int $variantId, float $qty = 1): array
    {
        $variant = ProductVariant::with('product.taxRate')
            ->whereHas(
                'product',
                fn($q) =>
                $q->where('tenant_id', $this->tenantId)->where('is_active', true)
            )
            ->findOrFail($variantId);

        $cart = $this->getCart();
        $key  = "v{$variantId}";

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
        } else {
            $cart[$key] = $this->buildCartItem($variant, $qty);
        }

        $this->saveCart($cart);
        return $this->totals();
    }

    public function updateQuantity(int $variantId, float $qty): array
    {
        $cart = $this->getCart();
        $key  = "v{$variantId}";

        if ($qty <= 0) {
            unset($cart[$key]);
        } elseif (isset($cart[$key])) {
            $cart[$key]['quantity'] = $qty;
        }

        $this->saveCart($cart);
        return $this->totals();
    }

    public function removeItem(int $variantId): array
    {
        $cart = $this->getCart();
        unset($cart["v{$variantId}"]);
        $this->saveCart($cart);
        return $this->totals();
    }

    public function applyItemDiscount(int $variantId, float $discount, string $type = 'flat'): array
    {
        $cart = $this->getCart();
        $key  = "v{$variantId}";

        if (isset($cart[$key])) {
            $cart[$key]['discount']      = $discount;
            $cart[$key]['discount_type'] = $type; // flat|percent
        }

        $this->saveCart($cart);
        return $this->totals();
    }

    // ── Coupon Application 

    public function applyCoupon(string $code): array
    {
        $discount = Discount::where('tenant_id', $this->tenantId)
            ->where('code', $code)
            ->where('is_active', true)
            ->where('requires_coupon_code', true)
            ->where(
                fn($q) =>
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now())
            )
            ->where(
                fn($q) =>
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now())
            )
            ->first();

        if (!$discount) {
            throw new \InvalidArgumentException('Invalid or expired coupon code.');
        }

        $cart = $this->getCart();
        $cart['__coupon'] = [
            'id'    => $discount->id,
            'code'  => $discount->code,
            'type'  => $discount->type,
            'value' => (float) $discount->value,
        ];

        $this->saveCart($cart);
        return $this->totals();
    }

    public function removeCoupon(): array
    {
        $cart = $this->getCart();
        unset($cart['__coupon']);
        $this->saveCart($cart);
        return $this->totals();
    }

    // ── Totals Calculation 

    public function totals(): array
    {
        $cart          = $this->getCart();
        $subtotal      = 0.0;
        $taxTotal      = 0.0;
        $discountTotal = 0.0;
        $items         = [];

        foreach ($cart as $key => $item) {
            // Skip meta keys like __coupon
            if (str_starts_with($key, '__')) {
                continue;
            }

            $lineSubtotal = $item['unit_price'] * $item['quantity'];

            $itemDiscount = match ($item['discount_type'] ?? 'flat') {
                'percent' => $lineSubtotal * ($item['discount'] / 100),
                default   => (float) ($item['discount'] ?? 0) * $item['quantity'],
            };

            $taxable  = $lineSubtotal - $itemDiscount;
            $lineTax  = $taxable * ($item['tax_rate'] / 100);
            $lineTotal = $taxable + $lineTax;

            $subtotal      += $lineSubtotal;
            $discountTotal += $itemDiscount;
            $taxTotal      += $lineTax;

            $items[] = array_merge($item, [
                'line_subtotal' => round($lineSubtotal, 4),
                'line_discount' => round($itemDiscount, 4),
                'line_tax'      => round($lineTax, 4),
                'line_total'    => round($lineTotal, 4),
            ]);
        }

        // Apply coupon-level discount
        $couponDiscount = 0.0;
        if (isset($cart['__coupon'])) {
            $coupon         = $cart['__coupon'];
            $couponDiscount = $coupon['type'] === 'percentage'
                ? ($subtotal - $discountTotal) * ($coupon['value'] / 100)
                : (float) $coupon['value'];
        }

        $grandTotal = max(0, $subtotal - $discountTotal - $couponDiscount + $taxTotal);

        return [
            'items'           => $items,
            'coupon'          => $cart['__coupon'] ?? null,
            'subtotal'        => round($subtotal, 4),
            'item_discounts'  => round($discountTotal, 4),
            'coupon_discount' => round($couponDiscount, 4),
            'discount_total'  => round($discountTotal + $couponDiscount, 4),
            'tax_total'       => round($taxTotal, 4),
            'grand_total'     => round($grandTotal, 4),
            'item_count'      => count($items),
        ];
    }

    // ── Private Helpers 

    private function buildCartItem(ProductVariant $variant, float $qty): array
    {
        return [
            'variant_id'   => $variant->id,
            'product_id'   => $variant->product_id,
            'name'         => $variant->product->name,
            'variant_name' => $variant->name !== $variant->product->name
                ? $variant->name
                : null,
            'sku'          => $variant->sku ?? $variant->product->sku,
            'barcode'      => $variant->barcode ?? $variant->product->barcode,
            'image'        => $variant->image ?? $variant->product->image,
            'unit_price'   => (float) $variant->effective_price,
            'unit_cost'    => (float) $variant->effective_cost,
            'tax_rate'     => (float) ($variant->product->taxRate?->rate ?? 0),
            'tax_inclusive' => (bool) ($variant->product->taxRate?->is_inclusive ?? false),
            'quantity'     => $qty,
            'discount'     => 0.0,
            'discount_type' => 'flat',
        ];
    }
}
