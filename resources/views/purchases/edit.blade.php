@extends('backend.app')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Purchase Order</h4>
            <a href="{{ route('admin.purchases.show', $po->id) }}" class="btn btn-light">Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.purchases.update', $po->id) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Store</label>
                    <select name="store_id" class="form-select" required>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}" @selected((int) $po->store_id === (int) $store->id)>{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((int) $po->supplier_id === (int) $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <input type="text" class="form-control" value="{{ $po->status }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">PO Number</label>
                    <input type="text" class="form-control" value="{{ $po->po_number }}" disabled>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $po->notes }}</textarea>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit">Update PO</button>
            </div>
        </form>
    </div>
</div>
@endsection
