@extends('backend.app')

@section('title', 'Create Category')

@section('content')

    <div class="container">

        <h3>Create Category</h3>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="mb-3">
                <label>Name</label>

                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            </div>


            {{-- <div class="mb-3">
                <label>Description</label>

                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>


            <div class="mb-3">
                <label>Image</label>

                <input type="file" name="image" class="form-control">
            </div> --}}

            <div><a href="{{ route('admin.categories.index') }}"> <button class="btn btn-primary">
                        Create Category
                    </button></a>
            </div>


        </form>

    </div>

@endsection
