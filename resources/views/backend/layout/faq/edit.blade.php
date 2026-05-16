@extends('backend.app')

@section('title', 'Edit FAQ')

@section('content')

{{-- HEADER --}}
<div class="row align-items-center mb-3">
    <div class="col">
        <h5 class="mb-0">Edit FAQ</h5>
    </div>

    <div class="col-auto">
        <a href="javascript:history.back()" class="btn btn-danger btn-sm">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>
</div>

{{-- CARD --}}
<div class="card shadow-sm">
    <div class="card-body">

        <form action="{{ route('admin.faq.update', $faq->id) }}" method="POST">
            @csrf

            {{-- QUESTION --}}
            <div class="mb-3">
                <label class="form-label">Question</label>
                <textarea name="que" class="form-control" rows="2">{{ $faq->que }}</textarea>
            </div>

            {{-- ANSWER --}}
            <div class="mb-3">
                <label class="form-label">Answer</label>
                <textarea name="ans" class="form-control ck-editor" rows="4">{{ $faq->ans }}</textarea>
            </div>

            {{-- UPDATE --}}
            <div class="d-flex justify-content-end">
                <button class="btn btn-success btn-sm">
                    <i class="ri-check-line me-1"></i> Update
                </button>
            </div>

        </form>

    </div>
</div>

@endsection


@push('scripts')
@include('backend.partials.ckeditor')
@endpush