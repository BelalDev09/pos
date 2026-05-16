@extends('backend.app')

@section('title', 'Roles')
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
        <h5 class="mb-0">Roles</h5>
    </div>

    <div class="col-auto">
        <div class="d-flex gap-2">

            <a href="{{ route('admin.roles.create') }}" class="btn btn-success btn-sm">
                <i class="ri-add-line me-1"></i> Add Role
            </a>

        </div>
    </div>
</div>

{{-- TABLE CARD --}}
<div class="card shadow-sm">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle table-nowrap" id="roles-table">

                <thead class="table-light text-center">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($roles as $index => $role)
                    <tr id="role-{{ $role->id }}">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $role->name }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">

                                <a href="{{ route('admin.roles.edit', $role->id) }}"
                                    class="btn btn-sm btn-soft-success" title="Edit Role">
                                    <i class="ri-edit-2-line"></i>
                                </a>

                                {{-- <button onclick="deleteRole({{ $role->id }})" class="btn btn-sm btn-soft-danger"
                                title="Delete Role">
                                <i class="ri-delete-bin-line"></i>
                                </button> --}}

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

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
    function deleteRole(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This role will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f06548',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: '/admin/roles/destroy/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#role-' + id).remove();

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1200,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush