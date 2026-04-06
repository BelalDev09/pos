<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeductInventory implements ShouldQueue
{
    public string $queue = 'inventory';

    public function handle(OrderCompleted $event): void
    {
        // Inventory deduction now happens synchronously in CheckoutService
        // This listener handles post-deduction tasks like updating analytics
        $order = $event->order;

        // Update POS session totals
        if ($order->pos_session_id) {
            \App\Models\PosSession::where('id', $order->pos_session_id)
                ->increment('total_transactions')
                ->increment('total_sales', $order->total);
        }
    }
}
