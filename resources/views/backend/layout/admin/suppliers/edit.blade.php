@extends('backend.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="container-fluid py-3 px-4" style="max-width:900px">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-semibold mb-0">
            <i class="ti ti-edit me-2"></i>Edit Supplier — {{ $supplier->business_name }}
        </h4>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}">
        @csrf @method('PUT')
        @include('backend.layout.admin.suppliers.form', ['supplier' => $supplier])
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Update Supplier</button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection