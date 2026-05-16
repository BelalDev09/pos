@extends('backend.app')

@section('title', 'Orders')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Orders</h4>
                <p class="text-muted mb-0">Manage sales orders</p>
            </div>
            <a href="{{ route('admin.order.create') }}" class="btn btn-primary"><i class="ri-add-line me-1"></i> New Order</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form class="row g-2 mb-3">
            <div class="col-md-8">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by order number">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="status">
                    <option value="">All status</option>
                    @foreach (['pending', 'completed', 'cancelled', 'void'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-secondary w-100" type="submit">Go</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Store</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->customer?->name ?? 'Walk-in' }}</td>
                            <td>{{ $order->store?->name ?? '-' }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ ucfirst((string) $order->status) }}</span></td>
                            <td class="text-end">{{ number_format((float) $order->total, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-sm btn-soft-info">View</a>
                                <a href="{{ route('admin.order.edit', $order->id) }}" class="btn btn-sm btn-soft-primary">Edit</a>
                                <button type="button" class="btn btn-sm btn-soft-danger delete-order" data-id="{{ $order->id }}">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $orders->withQueryString()->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.delete-order', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Delete order?',
            text: "This action can't be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: "{{ route('admin.order.destroy', ['id' => '__id__']) }}".replace('__id__', id),
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        window.location.reload();
                    }
                },
                error: function() {
                    Toast.fire({
                        icon: 'error',
                        title: 'Failed to delete order.'
                    });
                }
            });
        });
    });
</script>
@endpush
