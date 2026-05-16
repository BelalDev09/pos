<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreWebOrderRequest;
use App\Http\Requests\Order\UpdateWebOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['customer:id,name', 'store:id,name', 'cashier:id,name'])
            ->when($request->filled('search'), static function ($query) use ($request): void {
                $term = (string) $request->input('search');
                $query->where('order_number', 'like', "%{$term}%");
            })
            ->when($request->filled('status'), static function ($query) use ($request): void {
                $query->where('status', $request->input('status'));
            })
            ->latest()
            ->paginate(20);

        return view('backend.layout.orders.index', compact('orders'));
    }

    public function create(): View
    {
        $customers = Customer::query()->orderBy('name')->get(['id', 'name']);
        $stores = Store::query()->active()->orderBy('name')->get(['id', 'name']);

        return view('backend.layout.orders.create', compact('customers', 'stores'));
    }

    public function store(StoreWebOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['cashier_id'] = auth()->id();

        Order::query()->create($validated);

        return redirect()
            ->route('admin.order.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(int $id): View
    {
        $order = Order::query()
            ->with(['customer:id,name,email,phone', 'store:id,name', 'cashier:id,name', 'items'])
            ->findOrFail($id);

        return view('backend.layout.orders.show', compact('order'));
    }

    public function edit(int $id): View
    {
        $order = Order::query()->findOrFail($id);
        $customers = Customer::query()->orderBy('name')->get(['id', 'name']);
        $stores = Store::query()->active()->orderBy('name')->get(['id', 'name']);

        return view('backend.layout.orders.edit', compact('order', 'customers', 'stores'));
    }

    public function update(UpdateWebOrderRequest $request, int $id): RedirectResponse
    {
        $order = Order::query()->findOrFail($id);
        $order->update($request->validated());

        return redirect()
            ->route('admin.order.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $order = Order::query()->findOrFail($id);
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ]);
    }

    public function changeStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,completed,cancelled,void'],
        ]);

        $order = Order::query()->findOrFail($id);
        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated.',
        ]);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:orders,id'],
        ]);

        Order::query()->whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected orders deleted.',
        ]);
    }
}
