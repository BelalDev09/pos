<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCustomerPoints implements ShouldQueue
{
    public string $queue = 'default';

    public function handle(OrderCompleted $event): void
    {
        $order = $event->order;

        if (!$order->customer_id) {
            return;
        }

        // Award 1 loyalty point per currency unit spent (configurable)
        $pointsEarned = floor($order->total);

        $order->customer()->increment('loyalty_points', $pointsEarned);
        $order->customer()->increment('total_purchases', $order->total);
        $order->customer()->increment('total_orders');

        $order->customer()->update(['last_purchase_at' => now()]);

        $order->update(['loyalty_points_earned' => $pointsEarned]);
    }
}
