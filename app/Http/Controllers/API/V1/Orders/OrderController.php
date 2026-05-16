<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Jobs\SyncOfflineOrders;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'cashier', 'items'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->integer('store_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    public function create()
    {
        return response()->json([
            'message' => 'Order creation form is not available for this API controller.',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'cashier_id' => ['nullable', 'integer', 'exists:users,id'],
            'order_number' => ['required', 'string', 'max:100', 'unique:orders,order_number'],
            'status' => ['nullable', 'string', 'max:50'],
            'source' => ['nullable', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric'],
            'discount_amount' => ['nullable', 'numeric'],
            'tax_amount' => ['nullable', 'numeric'],
            'total' => ['required', 'numeric'],
        ]);

        $validated['tenant_id'] ??= $request->user()?->tenant_id;
        $validated['cashier_id'] ??= $request->user()?->id;

        return response()->json(Order::create($validated), 201);
    }

    public function show(int $id)
    {
        return response()->json(Order::with(['customer', 'cashier', 'items', 'payments'])->findOrFail($id));
    }

    public function edit(int $id)
    {
        return $this->show($id);
    }

    public function update(Request $request, int $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->only([
            'customer_id',
            'status',
            'source',
            'subtotal',
            'discount_amount',
            'tax_amount',
            'total',
            'notes',
            'internal_notes',
        ]));

        return response()->json($order->fresh(['customer', 'cashier', 'items']));
    }

    public function destroy(int $id)
    {
        Order::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }

    public function changeStatus(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
        ]);

        $order = Order::findOrFail($id);
        $order->update($validated);

        return response()->json($order);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:orders,id'],
        ])['ids'];

        Order::whereIn('id', $ids)->delete();

        return response()->json([
            'message' => 'Orders deleted successfully',
        ]);
    }

    public function refund(Request $request, int $id)
    {
        $validated = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $order = Order::findOrFail($id);
        $refundAmount = $validated['amount'] ?? $order->total;
        $order->update([
            'status' => $refundAmount >= $order->total ? 'refunded' : 'partially_refunded',
            'total_refunded' => $refundAmount,
        ]);

        return response()->json($order);
    }

    public function void(int $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'void']);

        return response()->json($order);
    }

    public function syncOffline(Request $request)
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
        ]);

        SyncOfflineOrders::dispatch(
            $validated['orders'],
            $request->user()?->tenant_id ?? (int) $request->input('tenant_id'),
            (int) $request->input('store_id'),
            $request->user()?->id ?? 0
        );

        return response()->json([
            'message' => 'Offline orders queued for sync',
        ], 202);
    }
}
