<?php

namespace App\Http\Controllers\Api\V1\Reports;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Services\Report\SalesReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dashboard(Request $request, SalesReportService $reports)
    {
        $tenantId = $request->user()?->tenant_id ?? (int) $request->input('tenant_id');
        $storeId = (int) $request->input('store_id');

        if ($tenantId && $storeId) {
            return response()->json($reports->getDashboardSummary($tenantId, $storeId));
        }

        return response()->json([
            'orders' => Order::count(),
            'revenue' => (float) Order::completed()->sum('total'),
            'low_stock_items' => Inventory::lowStock()->count(),
        ]);
    }

    public function sales(Request $request, SalesReportService $reports)
    {
        $tenantId = $request->user()?->tenant_id ?? (int) $request->input('tenant_id');
        $storeId = (int) $request->input('store_id');

        if (! $tenantId || ! $storeId) {
            return response()->json([
                'message' => 'tenant_id and store_id are required for sales reports',
            ], 422);
        }

        return response()->json($reports->getSalesReport(
            $tenantId,
            $storeId,
            $request->input('from', now()->startOfMonth()->toDateString()),
            $request->input('to', now()->toDateString()),
            $request->input('group_by', 'day')
        ));
    }

    public function inventory(Request $request)
    {
        $query = Inventory::with(['product', 'store']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->integer('store_id'));
        }

        return response()->json([
            'total_items' => (clone $query)->count(),
            'low_stock_items' => (clone $query)->lowStock()->count(),
            'out_of_stock_items' => (clone $query)->outOfStock()->count(),
            'items' => $query->paginate($request->integer('per_page', 25)),
        ]);
    }
}
