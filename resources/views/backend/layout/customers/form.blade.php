@extends('backend.app')

@section('title', isset($customer) ? 'Edit Customer' : 'Create Customer')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ isset($customer) ? 'Edit Customer' : 'Create Customer' }}</h4>
            <a href="{{ route('admin.customer.index') }}" class="btn btn-light">Back</a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    {{ isset($customer) ? 'Edit Customer' : 'Create Customer' }}
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ isset($customer) ? route('admin.customer.update', $customer->id) : route('admin.customer.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($customer))
                        @method('PUT')
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $customer->name ?? '') }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $customer->phone ?? '') }}" required>
                            @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $customer->email ?? '') }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loyalty Points</label>
                            <input type="number" name="loyalty_points" class="form-control"
                                value="{{ old('loyalty_points', $customer->loyalty_points ?? 0) }}" min="0">
                            @error('loyalty_points') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control">{{ old('address', $customer->address ?? '') }}</textarea>
                    @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-success">
                {{ isset($customer) ? 'Update Customer' : 'Create Customer' }}
            </button>

            <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary">Cancel</a>

            </form>

        </div>
    </div>

</div>
</div>

@endsection