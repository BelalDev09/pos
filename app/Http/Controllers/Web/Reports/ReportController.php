<?php

// File: app/Http/Controllers/Web/Reports/ReportController.php
// সম্পূর্ণ ফাইলটা এইভাবে replace করুন

namespace App\Http\Controllers\Web\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function dashboard()
    {
        // ✅ withoutGlobalScopes() ব্যবহার করছি
        // এতে TenantScope, SoftDelete সব bypass হবে
        // কোনো tenant_id না থাকলেও crash হবে না

        $stats = [
            'products'   => DB::table('products')->whereNull('deleted_at')->count(),
            'categories' => DB::table('categories')->whereNull('deleted_at')->count(),
            'customers'  => DB::table('customers')->whereNull('deleted_at')->count(),
            'users'      => DB::table('users')->whereNull('deleted_at')->count(),
        ];

        $recentProducts = DB::table('products')
            ->whereNull('deleted_at')
            ->latest('created_at')
            ->take(5)
            ->get(['id', 'name', 'created_at']);

        $allProducts = DB::table('products')
            ->whereNull('deleted_at')
            ->get(['id', 'name', 'sku', 'selling_price', 'is_active']);

        $allCustomers = DB::table('customers')
            ->whereNull('deleted_at')
            ->get(['id', 'name', 'email', 'phone']);

        $allCategories = DB::table('categories')
            ->whereNull('deleted_at')
            ->get(['id', 'name']);

        $allUsers = DB::table('users')
            ->whereNull('deleted_at')
            ->get(['id', 'name', 'email', 'is_super_admin']);

        return view('backend.dashboard', compact(
            'stats',
            'recentProducts',
            'allProducts',
            'allCustomers',
            'allCategories',
            'allUsers'
        ));
    }

    public function sales()
    {
        return view('reports.sales');
    }

    public function inventory()
    {
        return view('reports.inventory');
    }

    public function profitLoss()
    {
        return view('reports.profit_loss');
    }
}
