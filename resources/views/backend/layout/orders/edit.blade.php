@extends('backend.app')

@section('title', 'Edit Order')

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Order</h4>
            <a href="{{ route('admin.order.show', $order->id) }}" class="btn btn-light">Back</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.order.update', $order->id) }}">
            @method('PUT')
            @include('backend.layout.orders._form')
        </form>
    </div>
</div>
@endsection
