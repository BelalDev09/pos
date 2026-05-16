@extends('backend.app')

@section('title', 'Categories')
@push('styles')
{{-- SweetAlert2 CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css">
<style>
    .dropify-wrapper {
        height: auto !important;
    }
</style>
<style>
    .swal2-show-custom {
        animation: slideInRight 0.35s ease-out;
    }

    .swal2-hide-custom {
        animation: fadeOut 0.2s ease-in;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }
</style>
@endpush
@section('content')

{{-- HEADER --}}
<div class="row align-items-center mb-3">
    <div class="col">
        <h5 class="mb-0">Categories</h5>
    </div>

    <div class="col-auto">
        <div class="d-flex gap-2">

            <button class="btn btn-soft-danger btn-sm d-none" id="bulkDeleteBtn">
                <i class="ri-delete-bin-line me-1"></i> Delete Selected
            </button>

            <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-sm">
                <i class="ri-add-line me-1"></i> Add Category
            </a>

        </div>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="card shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle table-nowrap" id="categories-table">

                <thead class="table-light text-center">
                    <tr>
                        <th>#</th>
                        <th>
                            <input type="checkbox" id="select_all" class="form-check-input">
                        </th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

            </table>
        </div>

    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@if (session('success') || session('error') || session('warning') || session('message') || session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const toastConfig = {
            success: {
                background: '#ecfdf5',
                color: '#065f46',
                border: '#10b981',
                iconColor: '#10b981'
            },
            error: {
                background: '#fef2f2',
                color: '#991b1b',
                border: '#ef4444',
                iconColor: '#ef4444'
            },
            warning: {
                background: '#fffbeb',
                color: '#92400e',
                border: '#f59e0b',
                iconColor: '#f59e0b'
            },
            info: {
                background: '#eff6ff',
                color: '#1e3a8a',
                border: '#3b82f6',
                iconColor: '#3b82f6'
            }
        };

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showClass: {
                popup: 'swal2-show-custom'
            },
            hideClass: {
                popup: 'swal2-hide-custom'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        window.showToast = function(type, message) {

            const config = toastConfig[type] || toastConfig.info;

            Toast.fire({
                icon: type,
                title: message,
                background: config.background,
                color: config.color,
                didOpen: (toast) => {
                    toast.style.borderLeft = `6px solid ${config.border}`;
                    toast.style.borderRadius = '10px';
                    toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.08)';

                    const icon = toast.querySelector('.swal2-icon');
                    if (icon) {
                        icon.style.color = config.iconColor;
                    }
                }
            });
        };

        // Laravel session auto trigger
        @if(session('success'))
        showToast('success', @json(session('success')));
        @endif

        @if(session('error'))
        showToast('error', @json(session('error')));
        @endif

        @if(session('warning'))
        showToast('warning', @json(session('warning')));
        @endif

        @if(session('info'))
        showToast('info', @json(session('info')));
        @endif

    });
</script>
@endif
<script>
    let selectedIds = [];

    $(function() {

        $('#categories-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,

            ajax: "{{ route('admin.categories.index') }}",

            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'bulk_check',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'image',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name'
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

        // SELECT ALL
        $('#select_all').on('change', function() {
            $('.select_data').prop('checked', this.checked);

            selectedIds = this.checked ?
                $('.select_data').map(function() {
                    return $(this).val();
                }).get() : [];

            toggleBulkButton();
        });

    });

    function select_single_item(id) {
        if (selectedIds.includes(id)) {
            selectedIds = selectedIds.filter(i => i != id);
        } else {
            selectedIds.push(id);
        }
        toggleBulkButton();
    }

    function toggleBulkButton() {
        $('#bulkDeleteBtn').toggleClass('d-none', selectedIds.length === 0);
    }

    // DELETE SINGLE
    function showDeleteAlert(id) {

        Swal.fire({
            title: 'Are you sure?',
            text: "This category will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f06548',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: `/admin/categories/delete/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });

                        $('#categories-table').DataTable().ajax.reload(null, false);

                        selectedIds = [];
                        toggleBulkButton();
                    }
                });

            }
        });
    }

    // STATUS CHANGE
    function changeStatus(id) {

        Swal.fire({
            title: 'Change status?',
            text: "Update category status",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0ab39c',
            confirmButtonText: 'Yes, change it'
        }).then((result) => {

            if (result.isConfirmed) {

                $.post(`/admin/categories/status/${id}`, {
                    _token: '{{ csrf_token() }}'
                }, function(res) {

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    });

                    $('#categories-table').DataTable().ajax.reload(null, false);
                });
            }
        });
    }

    // BULK DELETE
    $('#bulkDeleteBtn').on('click', function() {

        if (selectedIds.length === 0) return;

        Swal.fire({
            title: 'Delete selected categories?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete'
        }).then((result) => {

            if (result.isConfirmed) {

                $.post(`{{ route('admin.categories.bulk-delete') }}`, {
                    _token: '{{ csrf_token() }}',
                    ids: selectedIds
                }, function(res) {

                    Swal.fire('Deleted!', res.message, 'success');

                    selectedIds = [];
                    toggleBulkButton();

                    $('#select_all').prop('checked', false);

                    $('#categories-table').DataTable().ajax.reload();
                });
            }
        });
    });
</script>
@endpush