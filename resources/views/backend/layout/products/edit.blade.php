@extends('backend.app')

@section('title', 'Edit Product')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit Product</h4>
                <a href="{{ route('admin.products.index') }}" class="btn btn-light">Back</a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @include('backend.layout.products._form')
    </form>
@endsection
