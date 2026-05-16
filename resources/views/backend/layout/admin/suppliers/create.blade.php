@extends('backend.app')
@section('title', 'Add Supplier')

@section('content')
<div class="container-fluid py-3 px-4" style="max-width:900px">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-semibold mb-0">
            <i class="ti ti-plus me-2"></i>Add Supplier
        </h4>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.suppliers.store') }}">
        @csrf
        @include('backend.layout.admin.suppliers.form', ['supplier' => null])
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i>Save Supplier</button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection