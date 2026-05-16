{{-- Reusable form partial for create & edit --}}
@php $val = fn($field) => old($field, $supplier?->$field ?? ''); @endphp

@if($errors->any())
<div class="alert alert-danger mb-3">
  <ul class="mb-0 ps-3">
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="card border-0 shadow-sm mb-3">
  <div class="card-header bg-transparent fw-semibold py-2">
    <i class="ti ti-building me-2"></i>Business Info
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Business Name <span class="text-danger">*</span></label>
        <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror"
          value="{{ $val('business_name') }}" placeholder="Manhattan Clothing Ltd." required>
        @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Contact Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
          value="{{ $val('name') }}" placeholder="John Doe" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
          value="{{ $val('email') }}" placeholder="contact@business.com">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ $val('phone') }}" placeholder="(378) 400-1234">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tax Number</label>
        <input type="text" name="tax_number" class="form-control" value="{{ $val('tax_number') }}">
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm mb-3">
  <div class="card-header bg-transparent fw-semibold py-2">
    <i class="ti ti-map-pin me-2"></i>Address
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Street Address</label>
        <input type="text" name="address" class="form-control" value="{{ $val('address') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">City</label>
        <input type="text" name="city" class="form-control" value="{{ $val('city') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">State</label>
        <input type="text" name="state" class="form-control" value="{{ $val('state') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Country</label>
        <input type="text" name="country" class="form-control" value="{{ $val('country') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Postal Code</label>
        <input type="text" name="postal_code" class="form-control" value="{{ $val('postal_code') }}">
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm mb-3">
  <div class="card-header bg-transparent fw-semibold py-2">
    <i class="ti ti-wallet me-2"></i>Financial Settings
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Payment Terms (days)</label>
        <input type="number" name="payment_terms" class="form-control" value="{{ $val('payment_terms') }}" min="0" placeholder="30">
      </div>
      <div class="col-md-3">
        <label class="form-label">Opening Balance</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input type="number" name="opening_balance" step="0.01" class="form-control"
            value="{{ $val('opening_balance') ?: '0.00' }}" min="0">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Advance Balance</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input type="number" name="advance_balance" step="0.01" class="form-control"
            value="{{ $val('advance_balance') ?: '0.00' }}" min="0">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Credit Limit</label>
        <div class="input-group">
          <span class="input-group-text">$</span>
          <input type="number" name="credit_limit" step="0.01" class="form-control"
            value="{{ $val('credit_limit') ?: '0.00' }}" min="0">
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-transparent fw-semibold py-2">
    <i class="ti ti-notes me-2"></i>Additional Info
  </div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-9">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Internal notes…">{{ $val('notes') }}</textarea>
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <div class="form-check form-switch mt-2">
          <input class="form-check-input" type="checkbox" name="is_active" value="1"
            id="isActive" {{ old('is_active', $supplier?->is_active ?? true) ? 'checked' : '' }}>
          <label class="form-check-label" for="isActive">Active</label>
        </div>
      </div>
    </div>
  </div>
</div>