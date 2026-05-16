@extends('backend.app')

@section('title', 'Create Dynamic Page')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-12 col-lg-12">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0 fw-semibold">Create Dynamic Page</h5>

                <a href="{{ route('admin.dynamicpages.index') }}" class="btn btn-sm btn-secondary">
                    Back
                </a>
            </div>

            {{-- CARD --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <form action="{{ route('admin.dynamicpages.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            {{-- TITLE --}}
                            <div class="col-12">
                                <label class="form-label">Page Title</label>
                                <input type="text" name="page_title"
                                    class="form-control @error('page_title') is-invalid @enderror"
                                    value="{{ old('page_title') }}" placeholder="Enter page title">
                                @error('page_title')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CONTENT --}}
                            <div class="col-12">
                                <label class="form-label">Page Content</label>
                                <textarea name="page_content" id="page_content"
                                    class="form-control ck-editor @error('page_content') is-invalid @enderror" rows="10">{{ old('page_content') }}</textarea>

                                @error('page_content')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        {{-- ACTION --}}
                        <div class="d-flex justify-content-between  mt-4">
                            <button type="submit" class="btn btn-success px-4">
                                Save Page
                            </button>

                            <a href="{{ route('admin.dynamicpages.index') }}" class="btn btn-outline-secondary">
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
@include('backend.partials.ckeditor')
@endpush