{{-- File: resources/views/inventory/index.blade.php --}}

@extends('backend.app')

@section('title', 'Inventory')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Inventory Management</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Stock Levels</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Inventory module loading...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection