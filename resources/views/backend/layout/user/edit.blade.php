@extends('backend.app')

@section('title', 'Edit User')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
<style>
    .dropify-wrapper {
        height: auto !important;
    }
</style>
@endpush
@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-semibold">Edit User</h5>

                <a href="{{ route('admin.user.list') }}" class="btn btn-sm btn-secondary">
                    Back
                </a>
            </div>

            {{-- CARD --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <form action="{{ route('admin.user.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="id" value="{{ $user->id }}">

                        <div class="row g-3">

                            {{-- AVATAR --}}
                            <div class="col-12 text-center">
                                <input type="file" name="avatar" class="dropify mt-2"
                                    data-default-file="{{ $user->avatar ? asset($user->avatar) : '' }}"
                                    data-height="120" />
                            </div>

                            {{-- ROLE --}}
                            <div class="col-12">
                                <label class="form-label">User Role</label>
                                <select name="role" class="form-select">
                                    <option value="">-- Select Role --</option>
                                    @foreach ($roles as $r)
                                    <option value="{{ $r->name }}"
                                        {{ $user->getRoleNames()->first() == $r->name ? 'selected' : '' }}>
                                        {{ $r->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- NAME --}}
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}">
                            </div>

                            {{-- EMAIL --}}
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}">
                            </div>

                            {{-- PASSWORD --}}
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            {{-- CONFIRM --}}
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                        </div>

                        {{-- ACTION --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success px-4">
                                Update User
                            </button>

                            <a href="{{ route('admin.user.list') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script>
    $(document).ready(function() {
        $('.dropify').dropify();
    });
</script>
@endpush