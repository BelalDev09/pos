<?php

namespace App\Observers;

use App\Models\Order;
use App\Jobs\ProcessReceipt;

class OrderObserver
{
    public function created(Order $order): void
    {
        // Log order creation
        \Log::info("Order created: {$order->order_number} | Tenant: {$order->tenant_id}");
    }

    public function updated(Order $order): void
    {
        // When an order is completed, queue receipt processing
        if ($order->wasChanged('status') && $order->status === 'completed') {
            if ($order->customer?->email) {
                ProcessReceipt::dispatch($order->id, 'email')->onQueue('receipts');
            }
        }
    }
}
