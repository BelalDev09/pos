<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Services\Inventory\InventoryService;
use App\Services\Pos\CartService;
use App\Services\Pos\CheckoutService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($this->cartService($request)->totals());
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'numeric', 'min:0.0001'],
        ]);

        return response()->json(
            $this->cartService($request)->addItem($validated['variant_id'], (float) ($validated['quantity'] ?? 1))
        );
    }

    public function addToCart(Request $request)
    {
        return $this->addItem($request);
    }

    public function updateItem(Request $request, int $variantId)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0'],
        ]);

        return response()->json(
            $this->cartService($request)->updateQuantity($variantId, (float) $validated['quantity'])
        );
    }

    public function removeItem(Request $request, int $variantId)
    {
        return response()->json($this->cartService($request)->removeItem($variantId));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100'],
        ]);

        return response()->json($this->cartService($request)->applyCoupon($validated['code']));
    }

    public function clearCart(Request $request)
    {
        $this->cartService($request)->clear();

        return response()->json([
            'message' => 'Cart cleared',
        ]);
    }

    public function checkout(Request $request, InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'string', 'max:50'],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.tendered' => ['nullable', 'numeric', 'min:0'],
            'amount_tendered' => ['required', 'numeric', 'min:0'],
        ]);

        $checkoutService = new CheckoutService($this->cartService($request), $inventoryService);

        $order = $checkoutService->complete(array_merge($validated, [
            'tenant_id' => $request->user()?->tenant_id ?? (int) $request->input('tenant_id'),
            'store_id' => (int) $request->input('store_id'),
            'cashier_id' => $request->user()?->id ?? 0,
        ]));

        return response()->json($order, 201);
    }

    private function cartService(Request $request): CartService
    {
        return new CartService(
            $request->user()?->tenant_id ?? (int) $request->input('tenant_id'),
            (int) $request->input('store_id'),
            $request->header('X-Cart-Session', $request->user()?->id ? 'user_'.$request->user()->id : $request->ip())
        );
    }
}
