@extends('backend.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Purchase Orders</h4>
                <p class="text-muted mb-0">Track and manage supplier purchases</p>
            </div>
            <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary">
                <i class="ri-add-line align-bottom me-1"></i> Create PO
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>PO #</th>
                        <th>Status</th>
                        <th>Store</th>
                        <th>Supplier</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                        <tr>
                            <td class="fw-semibold">{{ $po->po_number }}</td>
                            <td><span class="badge bg-info-subtle text-info">{{ ucfirst((string) $po->status) }}</span></td>
                            <td>{{ $po->store?->name ?? '-' }}</td>
                            <td>{{ $po->supplier?->name ?? '-' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.purchases.show', $po->id) }}" class="btn btn-sm btn-soft-info">View</a>
                                <a href="{{ route('admin.purchases.edit', $po->id) }}" class="btn btn-sm btn-soft-primary">Edit</a>
                                <form action="{{ route('admin.purchases.destroy', $po->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Delete this purchase order?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-soft-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No purchase orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $purchaseOrders->withQueryString()->links() }}
    </div>
</div>
@endsection
