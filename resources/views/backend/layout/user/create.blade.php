@extends('backend.app')

@section('title', 'Create User')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-semibold">Create User</h5>

                <a href="{{ route('admin.user.list') }}" class="btn btn-sm btn-secondary">
                    Back
                </a>
            </div>

            {{-- CARD --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <form action="{{ route('admin.user.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            {{-- ROLE --}}
                            <div class="col-12">
                                <label class="form-label">User Role</label>
                                <select name="role" class="form-select">
                                    <option value="">-- Select Role --</option>
                                    @foreach ($roles as $r)
                                    <option value="{{ $r->name }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- NAME --}}
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter name">
                            </div>

                            {{-- EMAIL --}}
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Enter email">
                            </div>

                            {{-- PASSWORD --}}
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            {{-- CONFIRM PASSWORD --}}
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                        </div>

                        {{-- ACTION --}}
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-success px-4">
                                Create User
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