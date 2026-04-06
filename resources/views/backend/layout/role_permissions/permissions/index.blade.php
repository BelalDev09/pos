@extends('backend.app')

@section('title', 'Permission Page')

@section('content')
    {{-- <main class="app-content content"> --}}
    <h2 class="section-title">All Permissions</h2>

    <div class="card p-3 border rounded shadow-sm">
        <div class="card-body">
            <div class="table-responsive p-4">
                <!-- Button Positioned at Top Right -->
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary" type="button">
                        <span>Add Permissions</span>
                    </a>
                </div>
                <br>
                <table id="basic_tables" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $index => $permission)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                        class="btn btn-warning">Edit</a>
                                    <button class="btn btn-danger deleteBtn" data-id="{{ $permission->id }}"
                                        data-url="{{ route('admin.permissions.destroy', $permission->id) }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- </main> --}}
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.deleteBtn').forEach(button => {
                button.addEventListener('click', function() {

                    let url = this.getAttribute('data-url');
                    let row = this.closest('tr');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will delete permanently!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        if (result.isConfirmed) {

                            fetch(url, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {

                                    Swal.fire(
                                        'Deleted!',
                                        data.message,
                                        'success'
                                    );

                                    // Remove row from table
                                    row.remove();
                                })
                                .catch(error => {
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong.',
                                        'error'
                                    );
                                });
                        }
                    });
                });
            });

        });
    </script>
@endpush
