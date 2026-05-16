@extends('backend.app')

@section('title', 'Customer Details')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Customer Details</h4>
                <p class="text-muted mb-0">View complete customer profile</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.customer.edit', $customer->id) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.customer.index') }}" class="btn btn-light">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Basic Information</h5>
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="200">Name</th>
                        <td>{{ $customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $customer->phone }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $customer->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>{{ $customer->address ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Loyalty</h5>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Points</span>
                    <span class="badge bg-success-subtle text-success fs-6">{{ $customer->loyalty_points ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
