@extends('backend.app')
@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('backend/assets/datatable/css/datatables.min.css') }}"> --}}
@endpush
@section('title', 'Users')
@section('content')
    {{-- <div class="app-content content "> --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Users List</h3>
            <div>
                <button type='button' style='min-width: 115px;' class='btn btn-danger delete_btn d-none'
                    onclick='multi_delete()'>Bulk Delete</button>
                <a href="{{ route('admin.user.create') }}" class="btn btn-primary" type="button">
                    <span>Add User</span>
                </a>
            </div>
        </div>

        <div class="card-body">

            <div class="table-responsive mt-4 p-4 card-datatable table-responsive pt-0">
                <table class="table align-middle table-nowrap mb-0 table-hover" id="data-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-checkbox">
                                    <input type="checkbox" class="form-check-input" id="select_all" onclick="select_all()">
                                    <label class="form-check-label" for="select_all"></label>
                                </div>
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- </div> --}}

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    }
                });

                if (!$.fn.DataTable.isDataTable('#data-table')) {

                    $('#data-table').DataTable({

                        processing: true,
                        serverSide: true,
                        responsive: true,
                        order: [],

                        lengthMenu: [
                            [25, 50, 100, 200, 500, -1],
                            [25, 50, 100, 200, 500, "All"]
                        ],

                        language: {
                            processing: `<div class="text-center">
                    <div class="spinner-border text-primary" style="width:3rem;height:3rem">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`
                        },

                        ajax: {
                            url: "{{ route('admin.user.list') }}",
                            type: "GET"
                        },

                        columns: [{
                                data: 'bulk_check',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'name'
                            },
                            {
                                data: 'email'
                            },
                            {
                                data: 'role'
                            },
                            {
                                data: 'status',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'action',
                                orderable: false,
                                searchable: false
                            }
                        ]

                    });

                }

            });
        </script>


        {{-- STATUS CHANGE --}}
        <script>
            function changeStatus(id) {

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You want to update the status?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes'
                }).then((result) => {

                    if (result.isConfirmed) {

                        let url = "{{ route('admin.user.status', ':id') }}".replace(':id', id);

                        $.ajax({

                            url: url,
                            type: 'GET',

                            success: function(resp) {

                                if (resp.success) {

                                    Swal.fire({
                                        icon: 'success',
                                        title: resp.message,
                                        showConfirmButton: false,
                                        timer: 1000
                                    });

                                } else {

                                    Swal.fire('Error', resp.message, 'error');

                                }

                                $('#data-table').DataTable().ajax.reload();

                            }

                        });

                    }

                });

            }
        </script>


        {{-- DELETE USER --}}
        <script>
            async function deleteUser(id) {

                const {
                    value: password
                } = await Swal.fire({

                    title: "Delete Account?",
                    input: "password",
                    inputLabel: "Enter your password",
                    inputPlaceholder: "Enter password",
                    showCancelButton: true,
                    confirmButtonText: "Delete",
                    cancelButtonText: "Cancel"

                })

                if (password) {

                    let formData = new FormData();

                    formData.append('id', id)
                    formData.append('password', password)
                    formData.append('_token', '{{ csrf_token() }}')

                    $.ajax({

                        url: "{{ route('admin.user.destroy') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,

                        success: function(response) {

                            if (response.success) {

                                Swal.fire('Deleted!', response.message, 'success')

                                $('#data-table').DataTable().ajax.reload()

                            } else {

                                Swal.fire('Error', response.message, 'error')

                            }

                        },

                        error: function() {

                            Swal.fire('Error', 'Something went wrong', 'error')

                        }

                    })

                }

            }
        </script>


        {{-- BULK DELETE --}}
        <script>
            function multi_delete() {

                let ids = []

                $('.select_data:checked').each(function() {
                    ids.push($(this).val())
                })

                if (ids.length === 0) {

                    Swal.fire('Error', 'Please select at least one record', 'warning')

                    return

                }

                Swal.fire({

                    title: 'Are you sure?',
                    text: 'Selected users will be deleted',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete'

                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({

                            url: "{{ route('admin.user.bulk-delete') }}",
                            type: "POST",
                            data: {
                                ids: ids,
                                _token: "{{ csrf_token() }}"
                            },

                            success: function(resp) {

                                Swal.fire('Deleted', resp.message, 'success')

                                $('#data-table').DataTable().ajax.reload()

                            }

                        })

                    }

                })

            }
        </script>
    @endpush
@endsection
