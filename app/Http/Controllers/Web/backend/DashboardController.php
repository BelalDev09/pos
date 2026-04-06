<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Models\AgentSubscription;
use App\Models\MembershipPlan;
use App\Models\PaymentLog;
use App\Models\User;
use App\Traits\AuthorizesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Middleware: manager, admin, superadmin can access
        $this->middleware(['auth', 'role_or_permission:admin|superadmin']);
    }

    /**
     * Display Admin Panel
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Authorize: Only admin/superadmin can access
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to view dashboard access');
        }

        // Greeting based on time
        $greeting = $this->getGreeting();

        // Core Statistics
        $stats = [
            'total_agents' => User::where('role', 'agent')->count(),
            'active_agents' => User::where('role', 'agent')->where('status', 'active')->count(),
            'pending_agents' => User::where('role', 'agent')->where('status', 'pending')->count(),
            'suspended_agents' => User::where('role', 'agent')->where('status', 'suspended')->count(),
            'onboarding_complete' => User::where('role', 'agent')->where('onboard_complete', true)->count(),
        ];

        // Recent Agents (last 5)
        $recent_agents = User::where('role', 'agent')
            ->with('membershipPlan')
            ->latest('created_at')
            ->limit(5)
            ->get();



        return view('backend.dashboard', compact(
            'greeting',
            'stats',
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
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
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
