@extends('backend.app')

@section('title', 'Customers List')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Customers</h4>
                <p class="text-muted mb-0">Manage your customer records</p>
            </div>
            <a href="{{ route('admin.customer.create') }}" class="btn btn-primary">
                <i class="ri-user-add-line align-bottom me-1"></i> Add Customer
            </a>
        </div>
    </div>
    </div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="customers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Loyalty Points</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        const destroyUrlTemplate = "{{ route('admin.customer.destroy', ['customer' => '__id__']) }}";

        $('#customers-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.customer.index') }}",
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'email',
                    name: 'email'
                },

                {
                    data: 'loyalty_points',
                    name: 'loyalty_points'
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-end',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $(document).on('click', '.delete-customer', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Delete customer?',
                text: "This action can't be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: destroyUrlTemplate.replace('__id__', id),
                        type: 'DELETE',
                        success: function(response) {
                            $('#customers-table').DataTable().ajax.reload(null, false);
                            if (response.success) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.message || 'Delete failed.'
                                });
                            }
                        },
                        error: function() {
                            Toast.fire({
                                icon: 'error',
                                title: 'Something went wrong. Please try again.'
                            });
                        }
                    }
                }
            });
        });
    });
</script>
@endpush