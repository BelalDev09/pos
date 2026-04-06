<?php

namespace App\Http\Controllers\Web\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        return view('inventory.index');
    }

    public function adjust(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'store_id'     => 'required|exists:stores,id',
            'new_quantity' => 'required|numeric|min:0',
            'reason'       => 'required|string|max:500',
        ]);

        // Will call InventoryService — stub for now
        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully.',
        ]);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id'   => 'required|exists:stores,id|different:from_store_id',
            'items'         => 'required|array|min:1',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transfer initiated.',
        ]);
    }
}
