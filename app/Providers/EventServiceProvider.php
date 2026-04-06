<?php

namespace App\Providers;

use App\Events\OrderCompleted;
use App\Events\StockLow;
use App\Events\StockTransferred;
use App\Listeners\DeductInventory;
use App\Listeners\SendLowStockAlert;
use App\Listeners\UpdateCustomerPoints;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCompleted::class => [
            DeductInventory::class,
            UpdateCustomerPoints::class,
        ],
        StockLow::class => [
            SendLowStockAlert::class,
        ],
        StockTransferred::class => [
            // Add StockTransferListener here
        ],
    ];
}
