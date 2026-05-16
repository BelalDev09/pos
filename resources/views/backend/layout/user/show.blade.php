@extends('backend.app')
@section('title', 'General Setting')

@push('styles')
<style>
    .profile-card {
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .profile-avatar {
        position: relative;
        width: 130px;
        height: 130px;
        margin: auto;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f1f1f1;
    }

    .profile-avatar input {
        position: absolute;
        bottom: 0;
        right: 0;
        opacity: 0;
        width: 40px;
        height: 40px;
        cursor: pointer;
    }

    .profile-avatar .edit-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #405189;
        color: #fff;
        border-radius: 50%;
        padding: 6px;
        font-size: 14px;
    }

    .profile-info p {
        margin-bottom: 6px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mt-4">

    <div class="row">
        <div class="col-12">
            <h5>Show Profile</h5>
            <div class="card profile-card">
                <div class="card-body">

                    <div class="row align-items-center">

                        <!-- Avatar -->
                        <div class="col-lg-3 text-center">
                            <div class="profile-avatar">
                                <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/no-image.png') }}">

                                <span class="edit-icon">
                                    <i class="ri-camera-line"></i>
                                </span>

                                <input type="file" name="avatar">
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="col-lg-9">
                            <h4 class="fw-semibold mb-2">{{ $user->name ?? 'No Name' }}</h4>

                            <div class="profile-info">
                                <p><strong>Email:</strong> {{ $user->email }}</p>
                                <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                                <p><strong>Role:</strong> {{ $user->role ?? 'N/A' }}</p>

                                <p>
                                    <strong>Status:</strong>
                                    @if ($user->status == 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-primary btn-sm">
                                    <i class="ri-edit-line me-1"></i> Edit Profile
                                </a>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection