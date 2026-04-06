@extends('backend.app')

@section('title', 'Edit Category')

@section('content')

    <div class="container">

        <h3>Edit Category</h3>

        <form action="{{ route('admin.categories.update', $data->id) }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')


            <div class="mb-3">
                <label>Name</label>

                <input type="text" name="name" class="form-control" value="{{ $data->name }}">
            </div>


            {{-- <div class="mb-3">
                <label>Description</label>

                <textarea name="description" class="form-control">{{ $data->description }}</textarea>
            </div>


            <div class="mb-3">
                <label>Image</label>

                <input type="file" name="image" class="form-control">

                <br>

                @if ($data->image)
                    <img src="{{ asset($data->image) }}" width="100">
                @endif

            </div> --}}


            <button class="btn btn-primary">
                Update Category
            </button>

        </form>

    </div>

@endsection
