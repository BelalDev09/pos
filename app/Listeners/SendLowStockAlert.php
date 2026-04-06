<?php

namespace App\Listeners;

use App\Events\StockLow;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;

class SendLowStockAlert implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(StockLow $event): void
    {
        $inventory = $event->inventory;

        // Notify store managers and inventory managers
        $managers = User::where('tenant_id', $inventory->tenant_id)
            ->where('store_id', $inventory->store_id)
            ->whereHas(
                'roles',
                fn($q) =>
                $q->whereIn('name', ['store_manager', 'inventory_manager', 'tenant_owner'])
            )
            ->get();

        Notification::send($managers, new LowStockNotification($inventory));
    }
}
