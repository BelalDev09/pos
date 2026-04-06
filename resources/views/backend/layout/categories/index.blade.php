@extends('backend.app')

@section('title', 'Categories List')

@section('content')
    <div class="container">

        <div class="d-flex justify-content-between mb-3">
            <h3>Categories</h3>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Add Category</a>
        </div>

        <table class="table table-striped table-nowrap align-middle mb-0" id="categories-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>
                        <input type="checkbox" id="select_all">
                    </th>
                    <th>Name</th>
                    {{-- <th>Image</th> --}}
                    <th>Status</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
        </table>

    </div>
@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            $('#categories-table').DataTable({

                processing: true,
                serverSide: true,

                ajax: "{{ route('admin.categories.index') }}",

                columns: [

                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'bulk_check',
                        name: 'bulk_check',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'name',
                        name: 'name'
                    },

                    // {
                    //     data: 'image',
                    //     name: 'image',
                    //     orderable: false,
                    //     searchable: false
                    // },

                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]

            });

        });

        function showDeleteAlert(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteFinal(id);
                }
            });
        }

        function deleteFinal(id) {
            let deleteUrl = '{{ route('admin.categories.destroy', ':id') }}'.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.message,
                                'success'
                            );
                            // Reload DataTable
                            $('#categories-table').DataTable().ajax.reload(null, false);
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                response.responseJSON.error || 'An error occurred.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        // Use the status change alert
        function changeStatus(event, id) {
            event.preventDefault();
            let statusUrl = '{{ route('admin.categories.status', ':id') }}'.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to change the status of this category.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: statusUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: response.success ? 'success' : 'error',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 800
                            });

                            // Reload DataTable from server
                            $('#categories-table').DataTable().ajax.reload(null, false);
                            // null = callback, false = don’t reset pagination
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                response.responseJSON.error || 'An error occurred.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        // Use the delete confirm alert
        function deleteRecord(event, id) {
            event.preventDefault();
            let deleteUrl = '{{ route('admin.categories.destroy', ':id') }}'.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            );
                            $('#basic_tables').DataTable().ajax.reload(); // Reload DataTable
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                response.responseJSON.error || 'An error occurred.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        function multi_delete() {
            let ids = [];
            let rows;
            // Use the DataTable instance
            let dTable = $('#data-table').DataTable();

            $('.select_data:checked').each(function() {
                ids.push($(this).val());
                rows = dTable.rows($(this).parents('tr')); // Use DataTable rows() method
            });

            if (ids.length == 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'Please check at least one row of the table!',
                });
            } else {
                let url = "{{ route('admin.categories.bulk-delete') }}";
                bulk_delete(ids, url, rows, dTable);
            }
        }
    </script>
@endpush
