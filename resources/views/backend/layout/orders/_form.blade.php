@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Order Number</label>
        <input type="text" name="order_number" class="form-control" value="{{ old('order_number', $order->order_number ?? '') }}" required>
        @error('order_number') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Store</label>
        <select name="store_id" class="form-select" required>
            <option value="">Select store</option>
            @foreach ($stores as $store)
                <option value="{{ $store->id }}" @selected(old('store_id', $order->store_id ?? '') == $store->id)>{{ $store->name }}</option>
            @endforeach
        </select>
        @error('store_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select">
            <option value="">Walk-in customer</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $order->customer_id ?? '') == $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
        @error('customer_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach (['pending', 'completed', 'cancelled', 'void'] as $status)
                <option value="{{ $status }}" @selected(old('status', $order->status ?? 'pending') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Source</label>
        <input type="text" name="source" class="form-control" value="{{ old('source', $order->source ?? 'pos') }}">
        @error('source') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Subtotal</label>
        <input type="number" step="0.01" min="0" name="subtotal" class="form-control" value="{{ old('subtotal', $order->subtotal ?? 0) }}" required>
        @error('subtotal') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Discount Amount</label>
        <input type="number" step="0.01" min="0" name="discount_amount" class="form-control" value="{{ old('discount_amount', $order->discount_amount ?? 0) }}">
        @error('discount_amount') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Tax Amount</label>
        <input type="number" step="0.01" min="0" name="tax_amount" class="form-control" value="{{ old('tax_amount', $order->tax_amount ?? 0) }}">
        @error('tax_amount') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Total</label>
        <input type="number" step="0.01" min="0" name="total" class="form-control" value="{{ old('total', $order->total ?? 0) }}" required>
        @error('total') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Note</label>
        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order->notes ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Internal Note</label>
        <textarea name="internal_notes" class="form-control" rows="2">{{ old('internal_notes', $order->internal_notes ?? '') }}</textarea>
    </div>
</div>

<div class="mt-4">
    <button class="btn btn-primary" type="submit">{{ isset($order) ? 'Update Order' : 'Create Order' }}</button>
    <a href="{{ route('admin.order.index') }}" class="btn btn-light">Cancel</a>
</div>
