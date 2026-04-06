<?php

namespace App\Services\Pos;

use App\Events\OrderCompleted;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\Inventory\InventoryService;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private readonly CartService       $cartService,
        private readonly InventoryService  $inventoryService
    ) {}

    /**
     * Complete a POS sale. This is the most critical method in the system.
     * Everything runs in a single database transaction.
     */
    public function complete(array $payload): Order
    {
        return DB::transaction(function () use ($payload) {
            $cart = $this->cartService->totals();

            if (empty($cart['items'])) {
                throw new \RuntimeException('Cart is empty. Add products before checkout.');
            }

            // ── 1. Validate stock for all items BEFORE creating any records ──
            foreach ($cart['items'] as $item) {
                if ($item['unit_price'] <= 0 && $item['quantity'] > 0) {
                    // Service items might be free — skip stock check
                    continue;
                }
                $this->inventoryService->assertStock(
                    $item['product_id'],
                    $payload['store_id'],
                    $item['quantity']
                );
            }

            // ── 2. Create the Order 
            $order = Order::create([
                'tenant_id'             => $payload['tenant_id'],
                'store_id'              => $payload['store_id'],
                'register_id'           => $payload['register_id'] ?? null,
                'pos_session_id'        => $payload['pos_session_id'] ?? null,
                'customer_id'           => $payload['customer_id'] ?? null,
                'cashier_id'            => $payload['cashier_id'],
                'order_number'          => $this->generateOrderNumber($payload['store_id']),
                'status'                => 'completed',
                'source'                => 'pos',
                'subtotal'              => $cart['subtotal'],
                'discount_amount'       => $cart['discount_total'],
                'tax_amount'            => $cart['tax_total'],
                'total'                 => $cart['grand_total'],
                'amount_tendered'       => $payload['amount_tendered'],
                'change_given'          => max(0, $payload['amount_tendered'] - $cart['grand_total']),
                'currency'              => $payload['currency'] ?? 'USD',
                'discount_code'         => $cart['coupon']['code'] ?? null,
                'loyalty_points_used'   => $payload['loyalty_points_used'] ?? 0,
                'notes'                 => $payload['notes'] ?? null,
                'completed_at'          => now(),
            ]);

            // ── 3. Create Order Items + Deduct Stock 
            foreach ($cart['items'] as $item) {
                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'product_name'       => $item['name'],
                    'product_sku'        => $item['sku'],
                    'product_barcode'    => $item['barcode'],
                    'variant_name'       => $item['variant_name'],
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $item['unit_price'],
                    'unit_cost'          => $item['unit_cost'],
                    'discount_amount'    => $item['line_discount'],
                    'tax_rate'           => $item['tax_rate'],
                    'tax_amount'         => $item['line_tax'],
                    'total'              => $item['line_total'],
                ]);

                $this->inventoryService->deduct(
                    productId: $item['product_id'],
                    storeId: $payload['store_id'],
                    quantity: $item['quantity'],
                    reference: $order->order_number,
                    userId: $payload['cashier_id'],
                    tenantId: $payload['tenant_id'],
                    orderId: $order->id,
                    unitCost: $item['unit_cost']
                );
            }

            // ── 4. Record Payments 
            foreach ($payload['payments'] as $paymentData) {
                Payment::create([
                    'order_id'     => $order->id,
                    'tenant_id'    => $payload['tenant_id'],
                    'processed_by' => $payload['cashier_id'],
                    'method'       => $paymentData['method'],
                    'amount'       => $paymentData['amount'],
                    'tendered'     => $paymentData['tendered'] ?? $paymentData['amount'],
                    'change_given' => $paymentData['change'] ?? 0,
                    'currency'     => $payload['currency'] ?? 'USD',
                    'status'       => 'completed',
                    'paid_at'      => now(),
                ]);
            }

            // ── 5. Clear Cart 
            $this->cartService->clear();

            // ── 6. Fire completion event (listeners handle loyalty, receipts) ─
            event(new OrderCompleted($order->load(['items', 'customer', 'cashier', 'payments'])));

            return $order;
        });
    }

    private function generateOrderNumber(int $storeId): string
    {
        $prefix = 'ORD-' . str_pad($storeId, 3, '0', STR_PAD_LEFT) . '-';
        $count  = Order::where('store_id', $storeId)->count() + 1;
        return $prefix . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
}
