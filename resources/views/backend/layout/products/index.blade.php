@extends('backend.app')

@section('title', 'Products')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Products</h4>
                    <p class="text-muted mb-0">{{ $products->total() }} products available</p>
                </div>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="ri-add-line align-bottom me-1"></i> Add Product
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="Search name, SKU, barcode">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="brand_id" class="form-select">
                        <option value="">All Brands</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(request('brand_id') == $brand->id)>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-secondary w-100" type="submit">Filter</button>
                    <a class="btn btn-light" href="{{ route('admin.products.index') }}">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU / Barcode</th>
                            <th>Category</th>
                            <th class="text-end">Cost</th>
                            <th class="text-end">Price</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-light rounded d-flex align-items-center justify-content-center">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded"
                                                    alt="{{ $product->name }}">
                                            @else
                                                <i class="ri-box-3-line fs-4 text-muted"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.products.show', $product->id) }}"
                                                class="fw-semibold text-body">{{ $product->name }}</a>
                                            <div class="text-muted small">{{ $product->unit }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $product->sku ?? '-' }}</div>
                                    <div class="text-muted small">{{ $product->barcode ?? '-' }}</div>
                                </td>
                                <td>{{ $product->category?->name ?? '-' }}</td>
                                <td class="text-end">{{ number_format((float) $product->cost_price, 2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format((float) $product->selling_price, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                        class="btn btn-sm btn-soft-primary">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-soft-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->withQueryString()->links() }}
        </div>
    </div>
@endsection
