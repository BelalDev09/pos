<?php

// File: app/Http/Controllers/Web/POS/PosController.php

namespace App\Http\Controllers\Web\POS;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // ── Stats 
        if ($user && $user->tenant_id) {
            $stats = [
                'products'   => Product::where('tenant_id', $user->tenant_id)->count(),
                'categories' => Category::where('tenant_id', $user->tenant_id)->count(),
                'customers'  => Customer::where('tenant_id', $user->tenant_id)->count(),
            ];

            $recentProducts = Product::where('tenant_id', $user->tenant_id)
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'created_at']);
        } else {
            // Super admin or no tenant — bypass scope
            $stats = [
                'products'   => Product::withoutGlobalScopes()->count(),
                'categories' => Category::withoutGlobalScopes()->count(),
                'customers'  => Customer::withoutGlobalScopes()->count(),
            ];

            $recentProducts = Product::withoutGlobalScopes()
                ->latest()
                ->take(5)
                ->get(['id', 'name', 'created_at']);
        }

        return view('pos.index', compact('stats', 'recentProducts'));
    }
}
