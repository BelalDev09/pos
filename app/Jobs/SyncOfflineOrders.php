<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Pos\CheckoutService;
use App\Services\Pos\CartService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncOfflineOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'sync';
    public int    $tries = 5;

    public function __construct(
        private readonly array $offlineOrders,
        private readonly int   $tenantId,
        private readonly int   $storeId,
        private readonly int   $userId
    ) {}

    public function handle(): void
    {
        foreach ($this->offlineOrders as $offlineOrder) {
            // Skip if already synced (idempotency check by offline_id)
            if (Order::where('offline_id', $offlineOrder['offline_id'])->exists()) {
                continue;
            }

            $cartService = new CartService(
                $this->tenantId,
                $this->storeId,
                'offline_' . $offlineOrder['offline_id']
            );

            // Rebuild cart from offline order data
            foreach ($offlineOrder['items'] as $item) {
                $cartService->addItem($item['variant_id'], $item['quantity']);
            }

            $checkoutService = new CheckoutService($cartService, app(\App\Services\Inventory\InventoryService::class));

            try {
                $checkoutService->complete(array_merge($offlineOrder, [
                    'tenant_id'  => $this->tenantId,
                    'store_id'   => $this->storeId,
                    'cashier_id' => $this->userId,
                    'offline_id' => $offlineOrder['offline_id'],
                ]));
            } catch (\Exception $e) {
                // Log failure and continue with next order
                \Log::error("Failed to sync offline order {$offlineOrder['offline_id']}: {$e->getMessage()}");
            }
        }
    }
}
