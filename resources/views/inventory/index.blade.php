@extends('backend.app')

@section('title', 'Inventory')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Inventory Management</h4>
                    <p class="text-muted mb-0">Adjust stock and manage transfers</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Products</p>
                        <h4 class="mb-0">{{ $summary['total_products'] }}</h4>
                    </div>
                    <i class="ri-box-3-line fs-2 text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Low Stock</p>
                        <h4 class="mb-0">{{ $summary['low_stock'] }}</h4>
                    </div>
                    <i class="ri-alert-line fs-2 text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-animate">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Out of Stock</p>
                        <h4 class="mb-0">{{ $summary['out_of_stock'] }}</h4>
                    </div>
                    <i class="ri-error-warning-line fs-2 text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stock Adjustment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.inventory.adjust') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">Select product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku ?: 'N/A' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Store</label>
                            <select name="store_id" class="form-select" required>
                                <option value="">Select store</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Quantity</label>
                            <input type="number" step="0.01" min="0" name="new_quantity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="2" required></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit">Adjust Stock</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Store Transfer</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.inventory.transfer') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">From Store</label>
                            <select name="from_store_id" class="form-select" required>
                                <option value="">Select store</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">To Store</label>
                            <select name="to_store_id" class="form-select" required>
                                <option value="">Select store</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Transfer Quantity</label>
                            <input type="number" step="0.01" min="0.01" name="items[0][qty]" class="form-control" required>
                            <input type="hidden" name="items[0][product_id]" value="{{ $products->first()->id ?? '' }}">
                        </div>
                        <button class="btn btn-primary" type="submit">Initiate Transfer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent Inventory Rows</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Store</th>
                            <th>Quantity</th>
                            <th>Reserved</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockRows as $row)
                            <tr>
                                <td>{{ $row->product?->name ?? '-' }}</td>
                                <td>{{ $row->store?->name ?? '-' }}</td>
                                <td>{{ number_format((float) $row->quantity, 2) }}</td>
                                <td>{{ number_format((float) $row->reserved_quantity, 2) }}</td>
                                <td>{{ $row->updated_at?->diffForHumans() ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No inventory rows found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection