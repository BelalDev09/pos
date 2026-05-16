@extends('backend.app')

@section('title', 'FAQ Detail')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="row align-items-center mb-3">
        <div class="col-md-6 d-flex align-items-center gap-2">

            <a href="{{ route('admin.faq.index') }}" class="btn btn-light btn-sm">
                <i class="ri-arrow-left-line"></i>
            </a>

            <div>
                <h5 class="mb-0">FAQ Detail</h5>
                <small class="text-muted">ID #{{ str_pad($faq->id, 3, '0', STR_PAD_LEFT) }}</small>
            </div>

        </div>
    </div>

    <div class="row g-3">

        {{-- LEFT CONTENT --}}
        <div class="col-xl-8">

            {{-- QUESTION --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">

                    <h6 class="text-muted mb-2">
                        <i class="ri-question-line me-1"></i> Question
                    </h6>

                    <h5 class="mb-0 fw-semibold">
                        {{ $faq->que }}
                    </h5>

                </div>
            </div>

            {{-- ANSWER --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">

                    <h6 class="text-muted mb-2">
                        <i class="ri-message-2-line me-1"></i> Answer
                    </h6>

                    <div class="text-muted" style="line-height:1.7">
                        {!! $faq->ans !!}
                    </div>

                </div>
            </div>

            {{-- STATUS --}}
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        @if ($faq->status === 'active')
                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                            Active
                        </span>
                        @else
                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">
                            Inactive
                        </span>
                        @endif
                    </div>

                    <button class="btn btn-outline-primary btn-sm" onclick="toggleStatus({{ $faq->id }})">
                        <i class="ri-refresh-line me-1"></i> Toggle Status
                    </button>

                </div>
            </div>

        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="col-xl-4">

            {{-- META --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">

                    <h6 class="text-muted mb-3">
                        <i class="ri-information-line me-1"></i> Meta Info
                    </h6>

                    <div class="d-flex justify-content-between small py-1">
                        <span class="text-muted">FAQ ID</span>
                        <span class="fw-medium">#{{ $faq->id }}</span>
                    </div>

                    <div class="d-flex justify-content-between small py-1">
                        <span class="text-muted">Created</span>
                        <span class="fw-medium">{{ $faq->created_at->format('d M Y, h:i A') }}</span>
                    </div>

                    <div class="d-flex justify-content-between small py-1">
                        <span class="text-muted">Updated</span>
                        <span class="fw-medium">{{ $faq->updated_at->format('d M Y, h:i A') }}</span>
                    </div>

                </div>
            </div>

            {{-- DANGER ZONE --}}
            <div class="card shadow-sm border-danger">
                <div class="card-body">

                    <h6 class="text-danger mb-2">
                        <i class="ri-error-warning-line me-1"></i> Danger Zone
                    </h6>

                    <p class="text-muted small">
                        This action will permanently delete this FAQ.
                    </p>

                    <form method="POST" action="{{ route('admin.faq.destroy', $faq->id) }}"
                        onsubmit="return confirm('Are you sure?')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm w-100">
                            <i class="ri-delete-bin-line me-1"></i> Delete FAQ
                        </button>

                    </form>

                </div>
            </div>

        </div>

    </div>
</div>

<script>
    function toggleStatus(id) {
        fetch("/admin/faq/status", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id: id
                })
            })
            .then(res => res.json())
            .then(() => location.reload());
    }
</script>

@endsection