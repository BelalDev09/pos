@extends('backend.app')

@section('title', 'Create Order')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Create Order</h4>
            <a href="{{ route('admin.order.index') }}" class="btn btn-light">Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.order.store') }}">
            @include('backend.layout.orders._form')
        </form>
    </div>
</div>
@endsection
