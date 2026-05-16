<?php

namespace App\Services\Pos;

use App\Models\ProductVariant;
use App\Services\DiscountService;
use Illuminate\Support\Facades\Cache;

class CartService
{
    private string $cartKey;

    public function __construct(
        private readonly int $tenantId,
        private readonly int $storeId,
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
                fn ($q) => $q->where('tenant_id', $this->tenantId)->where('is_active', true)
            )
            ->findOrFail($variantId);

        $cart = $this->getCart();
        $key = "v{$variantId}";

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
        $key = "v{$variantId}";

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
        $key = "v{$variantId}";

        if (isset($cart[$key])) {
            $cart[$key]['discount'] = $discount;
            $cart[$key]['discount_type'] = $type; // flat|percent
        }

        $this->saveCart($cart);

        return $this->totals();
    }

    // ── Coupon Application

    public function applyCoupon(string $code): array
    {
        $cart = $this->discountService()->applyCouponToCart(
            $this->getCart(),
            $this->tenantId,
            $code
        );

        $this->saveCart($cart);

        return $this->totals();
    }

    public function removeCoupon(): array
    {
        $cart = $this->discountService()->removeCouponFromCart($this->getCart());
        $this->saveCart($cart);

        return $this->totals();
    }

    // ── Totals Calculation

    public function totals(): array
    {
        return $this->discountService()->calculateCartTotals(
            $this->getCart(),
            $this->tenantId
        );
    }

    // ── Private Helpers

    private function buildCartItem(ProductVariant $variant, float $qty): array
    {
        return [
            'variant_id' => $variant->id,
            'product_id' => $variant->product_id,
            'category_id' => $variant->product->category_id,
            'brand_id' => $variant->product->brand_id,
            'name' => $variant->product->name,
            'variant_name' => $variant->name !== $variant->product->name
                ? $variant->name
                : null,
            'sku' => $variant->sku ?? $variant->product->sku,
            'barcode' => $variant->barcode ?? $variant->product->barcode,
            'image' => $variant->image ?? $variant->product->image,
            'unit_price' => (float) $variant->effective_price,
            'unit_cost' => (float) $variant->effective_cost,
            'tax_rate' => (float) ($variant->product->taxRate?->rate ?? 0),
            'tax_inclusive' => (bool) ($variant->product->taxRate?->is_inclusive ?? false),
            'quantity' => $qty,
            'discount' => 0.0,
            'discount_type' => 'flat',
        ];
    }

    private function discountService(): DiscountService
    {
        return app(DiscountService::class);
    }
}
