@extends('backend.app')

@section('title','Dashboard')

@section('content')

<!-- ====== PAGE HEADER ====== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Dashboard</h4>
        <small class="text-muted">Overview of your business</small>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-soft-primary">
            <i class="ri-refresh-line"></i>
        </button>
    </div>
</div>


<!-- ====== STATS CARDS ====== -->
<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-0">Products</p>
                        <h4 class="fw-bold">{{ $stats['products'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-primary-subtle rounded">
                        <i class="ri-shopping-bag-line fs-20 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-0">Customers</p>
                        <h4 class="fw-bold">{{ $stats['customers'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-success-subtle rounded">
                        <i class="ri-user-3-line fs-20 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-animate shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-0">Categories</p>
                        <h4 class="fw-bold">{{ $stats['categories'] }}</h4>
                    </div>
                    <div class="avatar-sm bg-warning-subtle rounded">
                        <i class="ri-apps-line fs-20 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection