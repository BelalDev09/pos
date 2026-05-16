@extends('backend.app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div>
                <h4 class="mb-sm-0">Purchase Order {{ $po->po_number }}</h4>
                <p class="text-muted mb-0">Status: {{ ucfirst((string) $po->status) }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.purchases.edit', $po->id) }}" class="btn btn-primary">Edit</a>
                <a href="{{ route('admin.purchases.index') }}" class="btn btn-light">Back</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Purchase Details</h5>
                <table class="table table-borderless mb-0">
                    <tr>
                        <th width="200">PO Number</th>
                        <td>{{ $po->po_number }}</td>
                    </tr>
                    <tr>
                        <th>Store</th>
                        <td>{{ $po->store?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Supplier</th>
                        <td>{{ $po->supplier?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td>{{ $po->createdBy?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $po->notes ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Summary</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Items</span>
                    <strong>{{ $po->items?->count() ?? 0 }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Total Amount</span>
                    <strong>{{ number_format((float) ($po->total_amount ?? 0), 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
