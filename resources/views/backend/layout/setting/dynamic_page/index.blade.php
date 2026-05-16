@extends('backend.app')

@section('title', 'Dynamic Pages')
@push('styles')
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

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">

        <h5 class="mb-0">Dynamic Pages</h5>

        <div class="d-flex gap-2">

            <button type="button" class="btn btn-soft-danger btn-sm delete_btn d-none" onclick="multi_delete()">
                <i class="ri-delete-bin-line me-1"></i> Delete Selected
            </button>

            <a href="{{ route('admin.dynamicpages.create') }}" class="btn btn-success btn-sm">
                <i class="ri-add-line me-1"></i> Add Page
            </a>

        </div>

    </div>

    {{-- TABLE CARD --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="table-responsive">

                <table id="data-table" class="table table-hover align-middle table-nowrap">

                    <thead class="table-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" class="form-check-input" id="select_all">
                            </th>

                            <th>Page Title</th>
                            <th>Content</th>
                            <th>Status</th>
                            <th width="120" class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                </table>

            </div>

        </div>
    </div>

</div>

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(function() {

        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: "{{ route('admin.dynamicpages.index') }}",

            order: [],

            columns: [{
                    data: 'bulk_check',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'page_title'
                },
                {
                    data: 'page_content'
                },
                {
                    data: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    className: "text-end"
                }
            ]
        });

    });


    // ================= DELETE =================
    $(document).on('click', '.deleteBtn', function() {

        let id = $(this).data('id');
        let row = $(this).closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f06548',
            cancelButtonColor: '#299cdb',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (!result.isConfirmed) return;

            let url = '{{ route('
            admin.dynamicpages.destroy ', ': id ') }}'.replace(':id', id);

            fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {

                    if (data.success) {

                        Swal.fire({
                            icon: 'success',
                            title: data.message,
                            timer: 1200,
                            showConfirmButton: false
                        });

                        $('#data-table').DataTable()
                            .row(row)
                            .remove()
                            .draw(false);

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: data.message
                        });
                    }

                })
                .catch(() => Swal.fire({
                    icon: 'error',
                    title: 'Unexpected error'
                }));

        });

    });


    // ================= STATUS =================
    $(document).on('change', '.status-toggle', function() {

        let id = $(this).data('id');
        let isChecked = $(this).is(':checked');

        let url = '{{ route('
        admin.dynamicpages.status ', ': id ') }}'.replace(':id', id);

        let $row = $(this).closest('tr');
        let $badge = $row.find('.status-text');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {

                if (res.success) {

                    if (isChecked) {
                        $badge.removeClass('bg-danger').addClass('bg-success').text('Active');
                    } else {
                        $badge.removeClass('bg-success').addClass('bg-danger').text('Inactive');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: res.message,
                        toast: true,
                        position: 'top-end',
                        timer: 1200,
                        showConfirmButton: false
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message
                    });
                }
            }
        });

    });


    // ================= BULK DELETE =================
    function multi_delete() {

        let ids = [];

        $('.select_data:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Please select at least one item',
                toast: true,
                position: 'top-end',
                timer: 1500,
                showConfirmButton: false
            });
            return;
        }

        Swal.fire({
            title: 'Delete Selected Items?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f06548',
            confirmButtonText: 'Yes, delete all!'
        }).then((result) => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ route('admin.dynamicpages.bulk-delete') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids
                },
                success: function(res) {

                    Swal.fire({
                        icon: 'success',
                        title: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    });

                    $('#data-table').DataTable().ajax.reload();

                }
            });

        });

    }
</script>
@endpush