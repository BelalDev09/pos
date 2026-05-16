@extends('backend.app')

@section('title', 'Order Details')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Order {{ $order->order_number }}</h4>
                <p class="text-muted mb-0">Complete order details</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.order.edit', $order->id) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.order.index') }}" class="btn btn-light">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Order Information</h5>
                <table class="table table-borderless mb-0">
                    <tr><th width="200">Order Number</th><td>{{ $order->order_number }}</td></tr>
                    <tr><th>Status</th><td>{{ ucfirst((string) $order->status) }}</td></tr>
                    <tr><th>Store</th><td>{{ $order->store?->name ?? '-' }}</td></tr>
                    <tr><th>Customer</th><td>{{ $order->customer?->name ?? 'Walk-in' }}</td></tr>
                    <tr><th>Cashier</th><td>{{ $order->cashier?->name ?? '-' }}</td></tr>
                    <tr><th>Source</th><td>{{ $order->source ?: '-' }}</td></tr>
                    <tr><th>Notes</th><td>{{ $order->notes ?: '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Summary</h5>
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong>{{ number_format((float) $order->subtotal, 2) }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Discount</span><strong>{{ number_format((float) $order->discount_amount, 2) }}</strong></div>
                <div class="d-flex justify-content-between mb-2"><span>Tax</span><strong>{{ number_format((float) $order->tax_amount, 2) }}</strong></div>
                <div class="d-flex justify-content-between"><span>Total</span><strong>{{ number_format((float) $order->total, 2) }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection
