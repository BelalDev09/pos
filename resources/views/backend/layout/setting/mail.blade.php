@extends('backend.app')

@section('title', 'Mail Setting')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css">
<style>
    .dropify-wrapper {
        height: auto !important;
    }
</style>
<style>
    .swal2-show-custom {
        animation: slideInRight 0.35s ease-out;
    }

    .swal2-hide-custom {
        animation: fadeOut 0.2s ease-in;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }
</style>
@endpush
@section('content')

{{-- HEADER --}}
<div class="row align-items-center mb-3">
    <div class="col">
        <h5 class="mb-0">Mail Setting</h5>
        <small class="text-muted">Configure system email credentials</small>
    </div>
</div>

{{-- CARD --}}
<div class="card shadow-sm">
    <div class="card-body">

        <form action="{{ route('admin.setting.mailstore') }}" method="POST">
            @csrf

            <p class="text-muted mb-3">
                Setup your system mail configuration properly.
            </p>

            {{-- ROW 1 --}}
            <div class="row g-3 mb-3">

                <div class="col-md-6">
                    <label class="form-label">Mail Mailer</label>
                    <input type="text" class="form-control" name="mail_mailer" value="{{ env('MAIL_MAILER') }}"
                        placeholder="smtp">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Mail Host</label>
                    <input type="text" class="form-control" name="mail_host" value="{{ env('MAIL_HOST') }}"
                        placeholder="smtp.gmail.com">
                </div>

            </div>

            {{-- ROW 2 --}}
            <div class="row g-3 mb-3">

                <div class="col-md-6">
                    <label class="form-label">Mail Port</label>
                    <input type="text" class="form-control" name="mail_port" value="{{ env('MAIL_PORT') }}"
                        placeholder="587">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Mail Username</label>
                    <input type="text" class="form-control" name="mail_username" value="{{ env('MAIL_USERNAME') }}">
                </div>

            </div>

            {{-- ROW 3 --}}
            <div class="row g-3 mb-3">

                <div class="col-md-6">
                    <label class="form-label">Mail Password</label>
                    <input type="password" class="form-control" name="mail_password" value="{{ env('MAIL_PASSWORD') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Mail Encryption</label>
                    <input type="text" class="form-control" name="mail_encryption"
                        value="{{ env('MAIL_ENCRYPTION') }}" placeholder="tls / ssl">
                </div>

            </div>

            {{-- ROW 4 --}}
            <div class="row g-3 mb-4">

                <div class="col-md-6">
                    <label class="form-label">Mail From Address</label>
                    <input type="email" class="form-control" name="mail_from_address"
                        value="{{ env('MAIL_FROM_ADDRESS') }}">
                </div>

            </div>

            {{-- SUBMIT --}}
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="ri-check-line me-1"></i> Save Settings
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.js"></script>
@if (session('success') || session('error') || session('warning') || session('message') || session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const toastConfig = {
            success: {
                background: '#ecfdf5',
                color: '#065f46',
                border: '#10b981',
                iconColor: '#10b981'
            },
            error: {
                background: '#fef2f2',
                color: '#991b1b',
                border: '#ef4444',
                iconColor: '#ef4444'
            },
            warning: {
                background: '#fffbeb',
                color: '#92400e',
                border: '#f59e0b',
                iconColor: '#f59e0b'
            },
            info: {
                background: '#eff6ff',
                color: '#1e3a8a',
                border: '#3b82f6',
                iconColor: '#3b82f6'
            }
        };

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            showClass: {
                popup: 'swal2-show-custom'
            },
            hideClass: {
                popup: 'swal2-hide-custom'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        window.showToast = function(type, message) {

            const config = toastConfig[type] || toastConfig.info;

            Toast.fire({
                icon: type,
                title: message,
                background: config.background,
                color: config.color,
                didOpen: (toast) => {
                    toast.style.borderLeft = `6px solid ${config.border}`;
                    toast.style.borderRadius = '10px';
                    toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.08)';

                    const icon = toast.querySelector('.swal2-icon');
                    if (icon) {
                        icon.style.color = config.iconColor;
                    }
                }
            });
        };

        // Laravel session auto trigger
        @if(session('success'))
        showToast('success', @json(session('success')));
        @endif

        @if(session('error'))
        showToast('error', @json(session('error')));
        @endif

        @if(session('warning'))
        showToast('warning', @json(session('warning')));
        @endif

        @if(session('info'))
        showToast('info', @json(session('info')));
        @endif

    });
</script>
@endif
@endpush