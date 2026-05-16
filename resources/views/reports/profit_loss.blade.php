@extends('backend.app')

@section('title', 'Profit & Loss')

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Profit & Loss</h4>
                    <p class="text-muted mb-0">Revenue, cost and gross margin overview</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Revenue</p>
                    <h4 class="mb-0">{{ number_format((float) $totalRevenue, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Cost</p>
                    <h4 class="mb-0">{{ number_format((float) $totalCost, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Gross Profit</p>
                    <h4 class="mb-0">{{ number_format((float) $grossProfit, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-animate">
                <div class="card-body">
                    <p class="text-muted mb-1">Profit Margin</p>
                    <h4 class="mb-0">{{ number_format((float) $profitMargin, 2) }}%</h4>
                </div>
            </div>
        </div>
    </div>
@endsection