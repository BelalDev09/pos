<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Models\AgentSubscription;
use App\Models\Category;
use App\Models\Customer;
use App\Models\MembershipPlan;
use App\Models\PaymentLog;
use App\Models\Product;
use App\Models\User;
use App\Traits\AuthorizesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware: manager, admin, super_admin can access
        $this->middleware(['auth', 'role_or_permission:admin|super_admin']);
    }

    /**
     * Display Admin Panel
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Authorize: Only admin/super_admin can access
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to view dashboard access');
        }

        // Greeting based on time
        $greeting = $this->getGreeting();

        // Core Statistics
        // $stats = [
        //     'total_agents' => User::where('role', 'agent')->count(),
        //     'active_agents' => User::where('role', 'agent')->where('status', 'active')->count(),
        //     'pending_agents' => User::where('role', 'agent')->where('status', 'pending')->count(),
        //     'suspended_agents' => User::where('role', 'agent')->where('status', 'suspended')->count(),
        //     'onboarding_complete' => User::where('role', 'agent')->where('onboard_complete', true)->count(),
        // ];




        /**
         * @var mixed
         * pso dashboard
         */
        $categories = Category::all();
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


        return view('backend.dashboard', compact(
            'greeting',
            'stats',
            'categories',
            'recentProducts'
            // 'recent_agents',
            // 'recent_payments',
            // 'top_plans',
            // 'revenue_chart'
        ));
    }

    /**
     * Dynamic Greeting based on time
     */
    private function getGreeting()
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to view dashboard');
        }
        $hour = now()->hour;
        if ($hour < 12) {
            return ['message' => 'Good Morning', 'icon' => 'fa-sun', 'color' => 'text-warning'];
        } elseif ($hour < 17) {
            return ['message' => 'Good Afternoon', 'icon' => 'fa-cloud-sun', 'color' => 'text-primary'];
        } else {
            return ['message' => 'Good Evening', 'icon' => 'fa-moon', 'color' => 'text-info'];
        }
    }
}
