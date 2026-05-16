
<!-- ================= PRODUCTS GRID (PRO LEVEL) ================= -->

@extends('layouts.app')

@section('title','Products')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h5>Products</h5>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">Add Product</a>
</div>

<div class="row g-3">

    @foreach($products as $product)
    <div class="col-xl-3 col-md-4">
        <div class="card product-card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold">{{ $product->name }}</h6>
                <p class="text-muted mb-1">৳ {{ $product->selling_price }}</p>
                <span class="badge bg-success">Active</span>
            </div>
        </div>
    </div>
    @endforeach

</div>

@endsection
