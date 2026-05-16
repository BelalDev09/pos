@extends('backend.app')

@section('title', 'Inventory Report')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Inventory Report</h4>
                    <p class="text-muted mb-0">Monitor stock health across stores</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Products</p>
                    <h4 class="mb-0">{{ $summary['total_products'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Low Stock Items</p>
                    <h4 class="mb-0">{{ $summary['low_stock'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Out of Stock</p>
                    <h4 class="mb-0">{{ $summary['out_of_stock'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Low Stock Details</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Store</th>
                            <th>Qty</th>
                            <th>Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockItems as $item)
                            <tr>
                                <td>{{ $item->product?->name ?? '-' }}</td>
                                <td>{{ $item->product?->sku ?? '-' }}</td>
                                <td>{{ $item->store?->name ?? '-' }}</td>
                                <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                <td>{{ number_format((float) $item->reorder_level, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No low stock items right now.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection