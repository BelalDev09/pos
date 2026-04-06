<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default"
    data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verify Email | {{ config('app.name', 'Laravel') }}</title>
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('Backend/assets/images/favicon.ico') }}">

    <!-- Layout config -->
    <script src="{{ asset('Backend/assets/js/layout.js') }}"></script>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('Backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons CSS -->
    <link href="{{ asset('Backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App CSS -->
    <link href="{{ asset('Backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Custom CSS -->
    <link href="{{ asset('Backend/assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body>

    <div class="auth-page-wrapper pt-5 d-flex justify-content-center align-items-center min-vh-100">

        <!-- Background with particles -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- Auth content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="card overflow-hidden mt-4 card-bg-fill">

                            <div class="card-body p-4">
                                <!-- Logo & Title -->
                                <div class="text-center mt-2">
                                    <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                                        <img src="{{ asset('Backend/assets/images/logo-light.png') }}" alt=""
                                            height="20">
                                    </a>
                                    <p class="text-muted mt-3 fs-15 fw-medium">Premium Admin & Dashboard Template</p>
                                </div>

                                <div class="mb-4">
                                    <!-- Email verification icon -->
                                    <div class="avatar-lg mx-auto">
                                        <div class="avatar-title bg-light text-primary display-5 rounded-circle">
                                            <i class="ri-mail-line"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <!-- Main instruction message -->
                                    <div class="text-muted text-center mb-4 mx-lg-3">
                                        <h4>{{ __('Verify Your Email') }}</h4>
                                        <p class="fs-15">
                                            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                                        </p>
                                    </div>

                                    <!-- Success status message -->
                                    @if (session('status') == 'verification-link-sent')
                                        <div class="alert border-0 alert-success text-center mb-4 mx-2" role="alert">
                                            <i class="ri-check-double-line me-2 align-middle"></i>
                                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                                        </div>
                                    @endif

                                    <!-- Action buttons -->
                                    <div class="row g-3 justify-content-center">
                                        <!-- Resend Verification Email -->
                                        <div class="col-6">
                                            <form method="POST" action="{{ route('verification.send') }}"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success w-100">
                                                    <i class="ri-mail-send-line align-middle me-1"></i>
                                                    {{ __('Resend Verification Email') }}
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Logout -->
                                        <div class="col-6">
                                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-outline-light w-100 text-white-50 border-light">
                                                    <i class="ri-logout-box-line align-middle me-1"></i>
                                                    {{ __('Log Out') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card-body -->
                        </div>
                        <!-- end card -->

                        <!-- Resend info -->
                        <div class="mt-4 text-center">
                            <p class="mb-0 text-muted">
                                {{ __('Didn\'t receive a code?') }}
                                <a href="{{ route('verification.send') }}"
                                    class="fw-semibold text-primary text-decoration-underline">
                                    {{ __('Resend') }}
                                </a>
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 text-center">
                            <p class="mb-0 text-muted">
                                ©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{ config('app.name') }}.
                                Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('Backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/plugins.js') }}"></script>

    <!-- Particles -->
    <script src="{{ asset('Backend/assets/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/pages/particles.app.js') }}"></script>

    <!-- Two-step verification JS (Velzon style) -->
    <script src="{{ asset('Backend/assets/js/pages/two-step-verification.init.js') }}"></script>
</body>

</html>
