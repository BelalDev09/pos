<?php

namespace App\Http\Controllers\Web\Reports;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function sales(): View
    {
        $todaySales = Order::query()->completed()->today()->sum('total');
        $todayOrders = Order::query()->completed()->today()->count();
        $averageOrderValue = $todayOrders > 0 ? ($todaySales / $todayOrders) : 0;

        $recentOrders = Order::query()
            ->with(['customer:id,name', 'cashier:id,name'])
            ->completed()
            ->latest('completed_at')
            ->limit(10)
            ->get(['id', 'order_number', 'customer_id', 'cashier_id', 'total', 'completed_at']);

        return view('reports.sales', compact('todaySales', 'todayOrders', 'averageOrderValue', 'recentOrders'));
    }

    public function inventory(): View
    {
        $summary = [
            'total_products' => Product::query()->count(),
            'low_stock' => Inventory::query()->lowStock()->count(),
            'out_of_stock' => Inventory::query()->outOfStock()->count(),
        ];

        $lowStockItems = Inventory::query()
            ->with(['product:id,name,sku', 'store:id,name'])
            ->lowStock()
            ->latest('updated_at')
            ->limit(20)
            ->get();

        return view('reports.inventory', compact('summary', 'lowStockItems'));
    }

    public function profitLoss(): View
    {
        $totalRevenue = Order::query()->completed()->sum('total');
        $totalCost = OrderItem::query()
            ->whereHas('order', static function ($query): void {
                $query->where('status', 'completed');
            })
            ->select(DB::raw('COALESCE(SUM(unit_cost * quantity),0) as total_cost'))
            ->value('total_cost');

        $grossProfit = $totalRevenue - $totalCost;
        $profitMargin = $totalRevenue > 0 ? (($grossProfit / $totalRevenue) * 100) : 0;

        return view('reports.profit_loss', compact('totalRevenue', 'totalCost', 'grossProfit', 'profitMargin'));
    }
}
