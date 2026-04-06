<?php

namespace App\Services\Report;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SalesReportService
{
    /**
     * Dashboard summary — real-time metrics for today vs yesterday.
     */
    public function getDashboardSummary(int $tenantId, int $storeId): array
    {
        $todayStart     = now()->startOfDay();
        $todayEnd       = now()->endOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();
        $yesterdayEnd   = now()->subDay()->endOfDay();

        $todayStats = $this->getPeriodStats($tenantId, $storeId, $todayStart, $todayEnd);
        $yestStats  = $this->getPeriodStats($tenantId, $storeId, $yesterdayStart, $yesterdayEnd);

        return [
            'today'            => $todayStats,
            'yesterday'        => $yestStats,
            'revenue_change'   => $this->percentageChange($yestStats['revenue'], $todayStats['revenue']),
            'orders_change'    => $this->percentageChange($yestStats['orders'], $todayStats['orders']),
            'hourly_sales'     => $this->getHourlySales($tenantId, $storeId, $todayStart, $todayEnd),
            'top_products'     => $this->getTopProducts($tenantId, $storeId, $todayStart, $todayEnd, 5),
            'payment_breakdown' => $this->getPaymentBreakdown($tenantId, $storeId, $todayStart, $todayEnd),
        ];
    }

    /**
     * Detailed sales report by date range.
     */
    public function getSalesReport(
        int    $tenantId,
        int    $storeId,
        string $from,
        string $to,
        string $groupBy = 'day'
    ): array {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate   = Carbon::parse($to)->endOfDay();

        $groupByExpr = match ($groupBy) {
            'hour'  => "DATE_FORMAT(completed_at, '%Y-%m-%d %H:00')",
            'week'  => "YEARWEEK(completed_at, 1)",
            'month' => "DATE_FORMAT(completed_at, '%Y-%m')",
            default => "DATE(completed_at)",
        };

        $salesOverTime = Order::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$fromDate, $toDate])
            ->select([
                DB::raw("{$groupByExpr} as period"),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(discount_amount) as discounts'),
                DB::raw('SUM(tax_amount) as tax'),
                DB::raw('SUM(total - COALESCE(total_refunded, 0)) as net_revenue'),
            ])
            ->groupBy(DB::raw($groupByExpr))
            ->orderBy('period')
            ->get();

        $totals = $this->getPeriodStats($tenantId, $storeId, $fromDate, $toDate);

        return [
            'period'        => ['from' => $from, 'to' => $to],
            'totals'        => $totals,
            'sales_over_time' => $salesOverTime,
            'top_products'  => $this->getTopProducts($tenantId, $storeId, $fromDate, $toDate, 10),
            'top_categories' => $this->getTopCategories($tenantId, $storeId, $fromDate, $toDate),
            'cashier_sales' => $this->getCashierSales($tenantId, $storeId, $fromDate, $toDate),
            'payment_breakdown' => $this->getPaymentBreakdown($tenantId, $storeId, $fromDate, $toDate),
            'refunds'       => $this->getRefundStats($tenantId, $storeId, $fromDate, $toDate),
        ];
    }

    /**
     * Profit & Loss report.
     */
    public function getProfitLossReport(
        int    $tenantId,
        int    $storeId,
        string $from,
        string $to
    ): array {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate   = Carbon::parse($to)->endOfDay();

        $salesData = Order::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$fromDate, $toDate])
            ->select([
                DB::raw('SUM(total) as gross_revenue'),
                DB::raw('SUM(discount_amount) as total_discounts'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(COALESCE(total_refunded,0)) as total_refunds'),
            ])
            ->first();

        $cogs = OrderItem::whereHas(
            'order',
            fn($q) =>
            $q->where('tenant_id', $tenantId)
                ->where('store_id', $storeId)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$fromDate, $toDate])
        )->select(DB::raw('SUM(unit_cost * quantity) as cogs'))->first();

        $grossRevenue  = (float) ($salesData->gross_revenue ?? 0);
        $totalTax      = (float) ($salesData->total_tax ?? 0);
        $totalDiscounts = (float) ($salesData->total_discounts ?? 0);
        $totalRefunds  = (float) ($salesData->total_refunds ?? 0);
        $cogsTotal     = (float) ($cogs->cogs ?? 0);

        $netRevenue    = $grossRevenue - $totalRefunds;
        $grossProfit   = $netRevenue - $cogsTotal;
        $netProfit     = $grossProfit - $totalTax;

        return [
            'period'          => ['from' => $from, 'to' => $to],
            'gross_revenue'   => round($grossRevenue, 4),
            'total_discounts' => round($totalDiscounts, 4),
            'total_refunds'   => round($totalRefunds, 4),
            'net_revenue'     => round($netRevenue, 4),
            'cogs'            => round($cogsTotal, 4),
            'gross_profit'    => round($grossProfit, 4),
            'total_tax'       => round($totalTax, 4),
            'net_profit'      => round($netProfit, 4),
            'gross_margin'    => $netRevenue > 0
                ? round(($grossProfit / $netRevenue) * 100, 2)
                : 0,
        ];
    }

    public function generateDailySummary(int $tenantId, string $date): void
    {
        $stores = \App\Models\Store::where('tenant_id', $tenantId)->get();

        foreach ($stores as $store) {
            $summary = $this->getSalesReport($tenantId, $store->id, $date, $date);

            \Log::info("Daily summary for tenant {$tenantId}, store {$store->id}: " .
                json_encode($summary['totals']));
        }
    }

    // ── Private Helpers 

    private function getPeriodStats(int $tenantId, int $storeId, $from, $to): array
    {
        $stats = Order::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->select([
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('AVG(total) as avg_order_value'),
                DB::raw('SUM(discount_amount) as discounts'),
                DB::raw('SUM(tax_amount) as tax'),
                DB::raw('SUM(COALESCE(total_refunded,0)) as refunds'),
            ])
            ->first();

        return [
            'orders'          => (int) ($stats->orders ?? 0),
            'revenue'         => round((float) ($stats->revenue ?? 0), 4),
            'avg_order_value' => round((float) ($stats->avg_order_value ?? 0), 4),
            'discounts'       => round((float) ($stats->discounts ?? 0), 4),
            'tax'             => round((float) ($stats->tax ?? 0), 4),
            'refunds'         => round((float) ($stats->refunds ?? 0), 4),
        ];
    }

    private function getHourlySales(int $tenantId, int $storeId, $from, $to): array
    {
        return Order::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->select([
                DB::raw('HOUR(completed_at) as hour'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(total) as revenue'),
            ])
            ->groupBy(DB::raw('HOUR(completed_at)'))
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    private function getTopProducts(int $tenantId, int $storeId, $from, $to, int $limit = 10): array
    {
        return OrderItem::whereHas(
            'order',
            fn($q) =>
            $q->where('tenant_id', $tenantId)
                ->where('store_id', $storeId)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$from, $to])
        )
            ->select([
                'product_id',
                'product_name',
                DB::raw('SUM(quantity) as qty_sold'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM((unit_price - unit_cost) * quantity) as profit'),
            ])
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getTopCategories(int $tenantId, int $storeId, $from, $to): array
    {
        return OrderItem::whereHas(
            'order',
            fn($q) =>
            $q->where('tenant_id', $tenantId)
                ->where('store_id', $storeId)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$from, $to])
        )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select([
                'categories.name as category',
                DB::raw('SUM(order_items.total) as revenue'),
                DB::raw('COUNT(order_items.id) as items_sold'),
            ])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getCashierSales(int $tenantId, int $storeId, $from, $to): array
    {
        return Order::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->join('users', 'orders.cashier_id', '=', 'users.id')
            ->select([
                'users.name as cashier',
                DB::raw('COUNT(orders.id) as orders'),
                DB::raw('SUM(orders.total) as revenue'),
            ])
            ->groupBy('orders.cashier_id', 'users.name')
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }

    private function getPaymentBreakdown(int $tenantId, int $storeId, $from, $to): array
    {
        return \App\Models\Payment::whereHas(
            'order',
            fn($q) =>
            $q->where('tenant_id', $tenantId)
                ->where('store_id', $storeId)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$from, $to])
        )
            ->where('payments.status', 'completed')
            ->select([
                'method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total'),
            ])
            ->groupBy('method')
            ->get()
            ->toArray();
    }

    private function getRefundStats(int $tenantId, int $storeId, $from, $to): array
    {
        $stats = Order::where('tenant_id', $tenantId)
            ->where('store_id', $storeId)
            ->whereIn('status', ['refunded', 'partially_refunded'])
            ->whereBetween('updated_at', [$from, $to])
            ->select([
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_refunded) as total_refunded'),
            ])
            ->first();

        return [
            'count'          => (int) ($stats->count ?? 0),
            'total_refunded' => round((float) ($stats->total_refunded ?? 0), 4),
        ];
    }

    private function percentageChange(float $old, float $new): float
    {
        if ($old == 0) {
            return $new > 0 ? 100.0 : 0.0;
        }
        return round((($new - $old) / $old) * 100, 2);
    }
}
