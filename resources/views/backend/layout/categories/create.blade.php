@extends('backend.app')

@section('title', 'Create Category')
@push('styles')
<style>
    .dropify-wrapper .dropify-preview .dropify-render video {
        width: 100%;
        height: auto;
        max-height: 220px;
        object-fit: contain;
    }

    .dropify-wrapper .dropify-preview .dropify-render img {
        width: 100%;
        height: auto;
        max-height: 220px;
        object-fit: contain;
    }
</style>
@endpush

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Create Category</h5>

                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-secondary">
                    Back
                </a>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">



                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter category name"
                                required>
                        </div>

                        <!-- Image Upload Row -->
                        <label class="col-3 col-form-label">Image</label>
                        <input type="file" name="image" id="image" class="dropify" accept="image/*"
                            data-allowed-file-extensions="jpg jpeg png gif webp" data-max-file-size="5M">

                        <div class="d-flex justify-content-between mt-4">
                            <button class="btn btn-success px-4">
                                Create
                            </button>

                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {

        $('.dropify').dropify({
            messages: {
                default: 'Drag and drop or click',
                replace: 'Drag and drop or click to replace',
                remove: 'Remove',
                error: 'Invalid file'
            }
        });

    });
</script>
@endpush