@extends('backend.app')

@section('title', 'Suppliers')

@section('content')
<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-sm bg-soft-primary rounded">
                    <i class="ri-store-2-line fs-18 text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-0">Suppliers</h4>
                    <p class="text-muted mb-0">Manage supplier database</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">
                <i class="ri-user-add-line me-1"></i> Add Supplier
            </a>
        </div>
    </div>

    {{-- STATS --}}
    <div class="row g-3 mb-3">

        @foreach([
        ['title'=>'Total Suppliers','value'=>$totals?->total_count ?? 0,'icon'=>'ri-group-line','color'=>'primary'],
        ['title'=>'Purchase Due','value'=>number_format($totals?->total_purchase_due ?? 0,2),'icon'=>'ri-money-dollar-circle-line','color'=>'warning'],
        ['title'=>'Return Due','value'=>number_format($totals?->total_return_due ?? 0,2),'icon'=>'ri-arrow-go-back-line','color'=>'danger'],
        ['title'=>'Advance','value'=>number_format($totals?->total_advance ?? 0,2),'icon'=>'ri-wallet-3-line','color'=>'success'],
        ] as $stat)

        <div class="col-xl-3 col-md-6">
            <div class="card card-animate border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-1">
                                <i class="ri-circle-fill text-{{$stat['color']}} me-1"></i>
                                {{ $stat['title'] }}
                            </p>
                            <h4 class="mb-0">{{ $stat['value'] }}</h4>
                        </div>
                        <div class="avatar-sm bg-soft-{{$stat['color']}} rounded">
                            <i class="{{ $stat['icon'] }} fs-20 text-{{$stat['color']}}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @endforeach

    </div>

    {{-- MAIN CARD --}}
    <div class="card card-height-100 border-0 shadow-sm">

        {{-- TOOLBAR --}}
        <div class="card-header bg-white border-bottom-0">

            <form class="row g-2 align-items-center" method="GET">

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-soft-light">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search suppliers..."
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status')=='active' )>Active</option>
                        <option value="inactive" @selected(request('status')=='inactive' )>Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="per_page" class="form-select">
                        @foreach([10,25,50,100] as $n)
                        <option value="{{ $n }}" @selected(request('per_page',25)==$n)>
                            {{ $n }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 text-end">

                    <a href="{{ route('admin.suppliers.export.csv') }}"
                        class="btn btn-soft-success btn-sm">
                        <i class="ri-file-excel-2-line me-1"></i> CSV
                    </a>

                    <a href="{{ route('admin.suppliers.export.pdf') }}"
                        class="btn btn-soft-danger btn-sm">
                        <i class="ri-file-pdf-line me-1"></i> PDF
                    </a>

                    <button type="button"
                        onclick="window.print()"
                        class="btn btn-soft-secondary btn-sm">
                        <i class="ri-printer-line me-1"></i> Print
                    </button>

                </div>

            </form>

        </div>

        {{-- TABLE --}}
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Business</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Pay Term</th>
                        <th>Opening</th>
                        <th>Advance</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($suppliers as $supplier)

                    <tr class="align-middle">

                        <td class="text-muted">{{ $supplier->contact_id }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs bg-soft-primary rounded-circle text-primary d-flex align-items-center justify-content-center">
                                    {{ strtoupper(substr($supplier->business_name,0,1)) }}
                                </div>
                                <span class="fw-semibold">{{ $supplier->business_name }}</span>
                            </div>
                        </td>

                        <td>{{ $supplier->name }}</td>
                        <td>{{ $supplier->email ?? '—' }}</td>
                        <td>{{ $supplier->phone ?? '—' }}</td>

                        <td>
                            <span class="badge bg-soft-info text-info">
                                {{ $supplier->payment_terms ?? '—' }} days
                            </span>
                        </td>

                        <td>${{ number_format($supplier->opening_balance,2) }}</td>
                        <td>${{ number_format($supplier->advance_balance,2) }}</td>

                        <td>
                            <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td class="text-end">

                            <div class="dropdown">
                                <button class="btn btn-soft-light btn-sm" data-bs-toggle="dropdown">
                                    <i class="ri-more-2-fill"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.suppliers.show',$supplier) }}">
                                            <i class="ri-eye-line me-2 text-primary"></i> View
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.suppliers.edit',$supplier) }}">
                                            <i class="ri-edit-line me-2 text-warning"></i> Edit
                                        </a>
                                    </li>

                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    <li>
                                        <form method="POST" action="{{ route('admin.suppliers.destroy',$supplier) }}">
                                            @csrf @method('DELETE')
                                            <button class="dropdown-item text-danger">
                                                <i class="ri-delete-bin-line me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>

                                </ul>
                            </div>

                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="ri-inbox-line fs-2"></i>
                            <p>No suppliers found</p>
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- FOOTER --}}
        <div class="card-footer d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $suppliers->firstItem() }} - {{ $suppliers->lastItem() }}
            </small>

            {{ $suppliers->links() }}
        </div>

    </div>

</div>
@endsection