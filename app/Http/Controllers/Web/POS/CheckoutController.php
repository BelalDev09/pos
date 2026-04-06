<?php

namespace App\Http\Controllers\Web\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Request $request): JsonResponse
    {
        // Basic stub — will be expanded with CartService
        return response()->json([
            'success' => true,
            'message' => 'Checkout endpoint ready.',
        ]);
    }

    public function cart(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'items'       => [],
                'item_count'  => 0,
                'subtotal'    => 0,
                'tax_total'   => 0,
                'grand_total' => 0,
            ],
        ]);
    }
}
