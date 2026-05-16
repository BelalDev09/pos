@extends('backend.app')

@section('title', 'Edit Permission')

@section('content')

{{-- HEADER --}}
<div class="row align-items-center mb-3">
    <div class="col">
        <h5 class="mb-0">Edit Permission</h5>
    </div>

    <div class="col-auto">
        <a href="javascript:history.back()" class="btn btn-danger btn-sm">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>
</div>

{{-- CARD --}}
<div class="card shadow-sm">
    <div class="card-body">

        <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Permission Name</label>
                <input type="text" name="name" class="form-control" value="{{ $permission->name }}">
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="ri-check-line me-1"></i> Update
                </button>
            </div>

        </form>

    </div>
</div>

@endsection