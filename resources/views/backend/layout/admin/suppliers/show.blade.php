@extends('backend.app')
@section('title', $supplier->business_name)

@push('styles')
<style>
    .info-label {
        font-size: .73rem;
        color: var(--bs-secondary-color);
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .2rem;
    }

    .info-value {
        font-size: .93rem;
        font-weight: 500;
        color: var(--bs-body-color);
    }

    .stat-card {
        background: var(--bs-secondary-bg);
        border-radius: .75rem;
        padding: 1.1rem 1.25rem;
    }

    .stat-label {
        font-size: .72rem;
        color: var(--bs-secondary-color);
        margin-bottom: .3rem;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 600;
    }

    .avatar-lg {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 1.1rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .badge-active {
        background: #dcfce7;
        color: #166534;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: .72rem;
        font-weight: 500;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: .72rem;
        font-weight: 500;
    }

    .section-title {
        font-size: .8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--bs-secondary-color);
        margin-bottom: 1rem;
    }

    .timeline-item {
        display: flex;
        gap: 12px;
        padding: .75rem 0;
        border-bottom: .5px solid var(--bs-border-color);
    }

    .timeline-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3 px-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-lg">
                {{ strtoupper(substr($supplier->name, 0, 1)) }}{{ strtoupper(substr(strstr($supplier->name.' ', ' '), 1, 1)) }}
            </div>
            <div>
                <h4 class="fw-semibold mb-0">{{ $supplier->business_name }}</h4>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <small class="text-muted font-monospace">{{ $supplier->contact_id }}</small>
                    <span class="{{ $supplier->is_active ? 'badge-active' : 'badge-inactive' }}">
                        {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-primary btn-sm">
                <i class="ti ti-edit me-1"></i>Edit
            </a>
            <form method="POST" action="{{ route('admin.suppliers.toggle', $supplier) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="ti ti-{{ $supplier->is_active ? 'ban' : 'check' }} me-1"></i>
                    {{ $supplier->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label"><i class="ti ti-shopping-cart me-1"></i>Total Purchase Due</div>
                <div class="stat-value text-primary">${{ number_format($supplier->total_purchase_due, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label"><i class="ti ti-arrow-back-up me-1"></i>Purchase Return Due</div>
                <div class="stat-value">${{ number_format($supplier->total_return_due, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label"><i class="ti ti-wallet me-1"></i>Advance Balance</div>
                <div class="stat-value">${{ number_format($supplier->advance_balance, 2) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label"><i class="ti ti-credit-card me-1"></i>Available Credit</div>
                <div class="stat-value text-success">${{ number_format($supplier->available_credit, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- Left col: info cards --}}
        <div class="col-lg-4">

            {{-- Contact info --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <p class="section-title"><i class="ti ti-user me-1"></i>Contact Info</p>
                    <div class="mb-3">
                        <div class="info-label">Contact Name</div>
                        <div class="info-value">{{ $supplier->name }}</div>
                    </div>
                    @if($supplier->email)
                    <div class="mb-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <a href="mailto:{{ $supplier->email }}" class="text-primary text-decoration-none">{{ $supplier->email }}</a>
                        </div>
                    </div>
                    @endif
                    @if($supplier->phone)
                    <div class="mb-3">
                        <div class="info-label">Phone</div>
                        <div class="info-value">
                            <a href="tel:{{ $supplier->phone }}" class="text-decoration-none">{{ $supplier->phone }}</a>
                        </div>
                    </div>
                    @endif
                    @if($supplier->tax_number)
                    <div>
                        <div class="info-label">Tax Number</div>
                        <div class="info-value font-monospace">{{ $supplier->tax_number }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Address --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <p class="section-title"><i class="ti ti-map-pin me-1"></i>Address</p>
                    @if($supplier->address || $supplier->city)
                    <address class="mb-0" style="font-style:normal;line-height:1.7">
                        @if($supplier->address)<div>{{ $supplier->address }}</div>@endif
                        @if($supplier->city || $supplier->state)
                        <div>{{ collect([$supplier->city, $supplier->state])->filter()->implode(', ') }}</div>
                        @endif
                        @if($supplier->country)<div>{{ $supplier->country }} @if($supplier->postal_code) — {{ $supplier->postal_code }}@endif</div>@endif
                    </address>
                    @else
                    <span class="text-muted small">No address on record.</span>
                    @endif
                </div>
            </div>

            {{-- Financial settings --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="section-title"><i class="ti ti-settings me-1"></i>Financial Settings</p>
                    <table class="table table-sm mb-0" style="font-size:.85rem">
                        <tr>
                            <td class="text-muted ps-0">Payment Terms</td>
                            <td class="text-end fw-medium pe-0">
                                {{ $supplier->payment_terms ? $supplier->payment_terms.' Days' : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Opening Balance</td>
                            <td class="text-end fw-medium pe-0">${{ number_format($supplier->opening_balance, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">Credit Limit</td>
                            <td class="text-end fw-medium pe-0">${{ number_format($supplier->credit_limit, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0 border-0">Added On</td>
                            <td class="text-end fw-medium pe-0 border-0">{{ $supplier->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        {{-- Right col: purchase orders --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-2">
                    <span class="fw-semibold"><i class="ti ti-file-invoice me-2"></i>Purchase Orders</span>
                    <span class="badge bg-secondary rounded-pill">{{ $supplier->purchaseOrders->count() }}</span>
                </div>
                <div class="card-body p-0">
                    @if($supplier->purchaseOrders->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-file-off" style="font-size:2rem"></i>
                        <p class="mt-2 mb-0 small">No purchase orders yet.</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size:.84rem">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier->purchaseOrders->sortByDesc('created_at') as $order)
                                <tr>
                                    <td class="font-monospace">{{ $order->reference ?? '#'.$order->id }}</td>
                                    <td>{{ $order->created_at->format('d M Y') }}</td>
                                    <td>{{ $order->items_count ?? '—' }}</td>
                                    <td>${{ number_format($order->total_amount ?? 0, 2) }}</td>
                                    <td class="text-success">${{ number_format($order->paid_amount ?? 0, 2) }}</td>
                                    <td class="text-danger">${{ number_format(($order->total_amount ?? 0) - ($order->paid_amount ?? 0), 2) }}</td>
                                    <td>
                                        @php
                                        $statusMap = [
                                        'received' => ['bg-success','Received'],
                                        'pending' => ['bg-warning text-dark','Pending'],
                                        'ordered' => ['bg-info text-dark','Ordered'],
                                        'cancelled' => ['bg-secondary','Cancelled'],
                                        ];
                                        [$cls, $label] = $statusMap[$order->status ?? 'pending'] ?? ['bg-secondary','Unknown'];
                                        @endphp
                                        <span class="badge {{ $cls }} rounded-pill" style="font-size:.7rem">{{ $label }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            @if($supplier->notes)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <p class="section-title"><i class="ti ti-notes me-1"></i>Notes</p>
                    <p class="mb-0 small" style="line-height:1.7">{{ $supplier->notes }}</p>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection