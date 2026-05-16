<?php

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'store']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->integer('store_id'));
        }

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'quantity' => ['required', 'numeric'],
            'reserved_quantity' => ['nullable', 'numeric'],
            'reorder_level' => ['nullable', 'numeric'],
            'reorder_quantity' => ['nullable', 'numeric'],
            'location_code' => ['nullable', 'string', 'max:100'],
        ]);

        return response()->json(Inventory::create($validated), 201);
    }

    public function adjust(Request $request, InventoryService $inventoryService)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'quantity' => ['required', 'numeric'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $inventory = $inventoryService->adjust(
            productId: $validated['product_id'],
            storeId: $validated['store_id'],
            newQuantity: (float) $validated['quantity'],
            reason: $validated['reason'] ?? 'Manual adjustment',
            userId: $request->user()?->id ?? 0,
            tenantId: $request->user()?->tenant_id ?? (int) $request->input('tenant_id')
        );

        return response()->json($inventory);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'from_store_id' => ['required', 'integer', 'exists:stores,id'],
            'to_store_id' => ['required', 'integer', 'exists:stores,id', 'different:from_store_id'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
        ]);

        return response()->json([
            'message' => 'Transfer request accepted',
            'data' => $validated,
        ], 202);
    }
}
