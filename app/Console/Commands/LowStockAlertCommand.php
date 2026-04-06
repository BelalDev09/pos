<?php

namespace App\Console\Commands;

use App\Events\StockLow;
use App\Repositories\InventoryRepository;
use Illuminate\Console\Command;

class LowStockAlertCommand extends Command
{
    protected $signature   = 'pos:low-stock-alerts';
    protected $description = 'Check all inventories and fire low stock alerts for items below reorder level';

    public function handle(InventoryRepository $inventoryRepository): int
    {
        $this->info('Checking low stock levels across all tenants...');

        // Get all active tenants
        $tenants = \App\Models\Tenant::active()->get();

        $alertCount = 0;

        foreach ($tenants as $tenant) {
            $lowStockItems = $inventoryRepository->getLowStockItems($tenant->id);

            foreach ($lowStockItems as $inventory) {
                event(new StockLow($inventory));
                $alertCount++;
                $this->line("  ⚠ Low stock: {$inventory->product->name} at {$inventory->store->name} — Qty: {$inventory->quantity}");
            }
        }

        $this->info("Done. Fired {$alertCount} low stock alerts.");
        return Command::SUCCESS;
    }
}
