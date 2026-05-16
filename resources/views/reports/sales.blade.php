@extends('backend.app')

@section('title', 'Sales Report')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Sales Report</h4>
                    <p class="text-muted mb-0">Today's sales snapshot</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Today's Revenue</p>
                    <h4 class="mb-0">{{ number_format((float) $todaySales, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Completed Orders</p>
                    <h4 class="mb-0">{{ $todayOrders }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Average Order Value</p>
                    <h4 class="mb-0">{{ number_format((float) $averageOrderValue, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent Completed Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Cashier</th>
                            <th>Total</th>
                            <th>Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer?->name ?? 'Walk-in' }}</td>
                                <td>{{ $order->cashier?->name ?? '-' }}</td>
                                <td>{{ number_format((float) $order->total, 2) }}</td>
                                <td>{{ $order->completed_at?->format('d M Y h:i A') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No completed orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection