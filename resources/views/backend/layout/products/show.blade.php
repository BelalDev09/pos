@extends('backend.app')

@section('title', $product->name)

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $product->name }}</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">Edit</a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light">Back</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Product Details</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="180">SKU</th>
                            <td>{{ $product->sku ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Barcode</th>
                            <td>{{ $product->barcode ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{{ $product->category?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Brand</th>
                            <td>{{ $product->brand?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $product->description ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pricing</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Cost</span>
                        <strong>{{ number_format((float) $product->cost_price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Selling Price</span>
                        <strong>{{ number_format((float) $product->selling_price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status</span>
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
