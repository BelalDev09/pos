<?php

namespace App\Services\Report;

use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InventoryReportService
{
    /**
     * Full inventory valuation report.
     */
    public function getValuationReport(int $tenantId, int $storeId): array
    {
        $inventories = Inventory::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->with(['product.category', 'product.brand'])
            ->get();

        $totalCostValue   = 0.0;
        $totalRetailValue = 0.0;
        $items            = [];

        foreach ($inventories as $inv) {
            $costValue   = (float) $inv->quantity * (float) $inv->product->cost_price;
            $retailValue = (float) $inv->quantity * (float) $inv->product->selling_price;

            $totalCostValue   += $costValue;
            $totalRetailValue += $retailValue;

            $items[] = [
                'product_id'     => $inv->product_id,
                'product_name'   => $inv->product->name,
                'sku'            => $inv->product->sku,
                'category'       => $inv->product->category?->name,
                'quantity'       => (float) $inv->quantity,
                'unit'           => $inv->product->unit,
                'cost_price'     => (float) $inv->product->cost_price,
                'retail_price'   => (float) $inv->product->selling_price,
                'cost_value'     => round($costValue, 4),
                'retail_value'   => round($retailValue, 4),
                'is_low_stock'   => $inv->is_low_stock,
                'reorder_level'  => (float) $inv->reorder_level,
            ];
        }

        return [
            'items'              => $items,
            'summary'            => [
                'total_products'    => count($items),
                'total_cost_value'  => round($totalCostValue, 4),
                'total_retail_value' => round($totalRetailValue, 4),
                'potential_profit'  => round($totalRetailValue - $totalCostValue, 4),
                'low_stock_count'   => collect($items)->where('is_low_stock', true)->count(),
                'out_of_stock'      => collect($items)->where('quantity', '<=', 0)->count(),
            ],
        ];
    }

    /**
     * Stock movement history with running balance.
     */
    public function getMovementReport(
        int    $tenantId,
        int    $storeId,
        string $from,
        string $to,
        ?int   $productId = null
    ): array {
        $movements = StockMovement::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->whereBetween('moved_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->with(['product', 'createdBy'])
            ->orderBy('moved_at', 'desc')
            ->get();

        $summary = $movements->groupBy('type')->map(fn($group) => [
            'count'    => $group->count(),
            'quantity' => $group->sum('quantity'),
        ]);

        return [
            'movements' => $movements->toArray(),
            'summary'   => $summary->toArray(),
        ];
    }

    /**
     * Low stock alert report.
     */
    public function getLowStockReport(int $tenantId, int $storeId): array
    {
        return Inventory::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->lowStock()
            ->with(['product.category', 'product.suppliers'])
            ->orderBy('quantity')
            ->get()
            ->map(fn($inv) => [
                'product_id'      => $inv->product_id,
                'product_name'    => $inv->product->name,
                'sku'             => $inv->product->sku,
                'category'        => $inv->product->category?->name,
                'current_stock'   => (float) $inv->quantity,
                'reorder_level'   => (float) $inv->reorder_level,
                'reorder_qty'     => (float) $inv->reorder_quantity,
                'shortage'        => max(0, (float) $inv->reorder_level - (float) $inv->quantity),
            ])
            ->toArray();
    }
}
