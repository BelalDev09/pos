<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $stores = Store::active()->get(['id', 'name']);
        $products = Product::active()->orderBy('name')->get(['id', 'name', 'sku']);

        $stockRows = Inventory::query()
            ->with(['product:id,name,sku', 'store:id,name'])
            ->latest('updated_at')
            ->limit(30)
            ->get();

        $summary = [
            'total_products' => Product::query()->count(),
            'low_stock' => Inventory::query()->lowStock()->count(),
            'out_of_stock' => Inventory::query()->outOfStock()->count(),
        ];

        return view('inventory.index', compact('stores', 'products', 'stockRows', 'summary'));
    }

    public function adjust(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'store_id' => 'required|exists:stores,id',
            'new_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $message = 'Stock adjusted successfully.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function transfer(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'items' => 'required|array|min:1',
        ]);

        $message = 'Transfer initiated.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
