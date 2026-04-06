@extends('backend.app')

@section('title', 'Admin Dashboard')

{{-- @push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .stat-card {
            border-left: 5px solid #007bff;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .greeting-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .progress {
            height: 25px;
            font-weight: bold;
        }
    </style>
@endpush --}}

{{-- @section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <!-- Greeting Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card greeting-card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-1">{{ $greeting['message'] }}, {{ auth()->user()->name }}!</h3>
                                <p class="mb-0">Here's what's happening with your platform today</p>
                            </div>
                            <div>
                                <i class="fa-solid {{ $greeting['icon'] }} fa-4x {{ $greeting['color'] }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            {{-- <div class="row">
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Agents</h5>
                            <h2 class="font-weight-bold">{{ number_format($stats['total_agents']) }}</h2>
                            <p class="mb-0">
                                <span class="text-success"><i class="fa-solid fa-arrow-up"></i>
                                    {{ $stats['active_agents'] }} Active</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card success shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Active Subscriptions</h5>
                            <h2 class="font-weight-bold">{{ number_format($stats['active_subscriptions']) }}</h2>
                            <p class="mb-0">
                                {{ $stats['active_subscriptions'] > 0 ? round(($stats['active_subscriptions'] / $stats['total_agents']) * 100, 1) : 0 }}%
                                of total agents
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card warning shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Monthly Revenue</h5>
                            <h2 class="font-weight-bold">${{ number_format($stats['monthly_revenue'], 2) }}</h2>
                            <p class="mb-0">
                                Total: ${{ number_format($stats['total_revenue'], 2) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card stat-card danger shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Pending Agents</h5>
                            <h2 class="font-weight-bold">{{ number_format($stats['pending_agents']) }}</h2>
                            <p class="mb-0">Awaiting onboarding completion</p>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Main Content -->
            {{-- <div class="row mt-4"> --}}
                <!-- Recent Agents -->
                {{-- <div class="col-lg-6 col-12">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Agents</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Plan</th>
                                            <th>Status</th>
                                            <th>Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody> --}}
                                        {{-- @forelse($recent_agents as $agent) --}}
                                        {{-- <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $agent->avatar ?? asset('https://static.vecteezy.com/system/resources/previews/048/926/084/non_2x/silver-membership-icon-default-avatar-profile-icon-membership-icon-social-media-user-image-illustration-vector.jpg') }}"
                                                        alt="Avatar" class="rounded-circle mr-2" width="40"
                                                        height="40"> --}}
                                                    {{-- <strong>{{ $agent->name }}</strong> --}}
                                                {{-- </div>
                                            </td> --}}
                                            {{-- <td>{{ $agent->email }}</td>
                                            <td>{{ $agent->membershipPlan?->name ?? 'N/A' }}</td> --}}
                                            {{-- <td>
                                                <span
                                                    class="badge bg-{{ $agent->status == 'active' ? 'success' : ($agent->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($agent->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $agent->created_at->diffForHumans() }}</td>
                                        </tr>
                                        {{-- @empty --}}
                                        {{-- <tr>
                                            <td colspan="5" class="text-center py-4">No recent agents</td>
                                        </tr> --}}
                                        {{-- @endforelse --}}
                                    {{-- </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <!-- Recent Payments -->
                {{-- <div class="col-lg-6 col-12">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Payments</h5>
                            <span class="badge bg-success">{{ $recent_payments->count() }} Recent</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Agent</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recent_payments as $payment)
                                            <tr>
                                                <td>{{ $payment->user?->name ?? 'Unknown' }}</td>
                                                <td class="text-success font-weight-bold">
                                                    ${{ number_format($payment->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($payment->payment_type) }}</span>
                                                </td>
                                                <td>{{ $payment->paid_at?->diffForHumans() ?? 'N/A' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">No recent payments</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

                <!-- Top Plans & Quick Actions -->
                {{-- <div class="row mt-4">
                    <div class="col-lg-6 col-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">Top Membership Plans</h5>
                            </div> --}}
                            {{-- <div class="card-body">
                            @forelse($top_plans as $plan)
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>{{ $plan->name }}</strong>
                                        <span class="badge bg-primary">{{ $plan->agent_subscriptions_count }} Agents</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        @php
                                            $max = $top_plans->max('agent_subscriptions_count');
                                            $percentage =
                                                $max > 0 ? ($plan->agent_subscriptions_count / $max) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%">
                                            ${{ number_format($plan->base_price, 2) }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">No active plans available</p>
                            @endforelse
                        </div> --}}
                        {{-- </div>
                    </div>


                </div>
            </div>
        </div>
    @endsection  --}}

    {{-- @push('script') --}}
        <!-- Chart.js if you want revenue chart -->
        {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
        <script>
            // Optional: Revenue Chart

            // const labels = revenueData.map(item => item.date);
            // const amounts = revenueData.map(item => item.amount);

            // new Chart(document.getElementById('revenueChart'), {
            //     type: 'line',
            //     data: {
            //         labels: labels,
            //         datasets: [{
            //             label: 'Revenue ($)',
            //             data: amounts,
            //             borderColor: '#28a745',
            //             backgroundColor: 'rgba(40, 167, 69, 0.1)',
            //             tension: 0.4,
            //             fill: true
            //         }]
            //     },
            // options: {
            // responsive: true,
            // scales: {
            //     y: {
            //         beginAtZero: true
            //     }
            // }
            // }
            // });
        </script>
    {{-- @endpush --}}
