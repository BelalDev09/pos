@extends('backend.app')

@section('title', 'Edit Permissions')

@section('content')
    {{-- <main class="app-content content"> --}}
    <h2 class="section-title">Edit Permissions</h2>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-end">
                            <a href="javascript:history.back()" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">Permission Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $permission->name }}">
                            </div>

                            <button type="submit" class="btn btn-success">Update</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- </main> --}}
@endsection
