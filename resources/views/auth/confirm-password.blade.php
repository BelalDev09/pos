<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default"
    data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirm Password | {{ config('app.name', 'Laravel') }}</title>
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
                        <div class="card overflow-hidden mt-4">

                            <div class="card-body p-4">
                                <!-- Logo & Title -->
                                <div class="text-center mt-2">
                                    <a href="{{ url('/') }}" class="d-inline-block auth-logo">
                                        <img src="{{ asset('Backend/assets/images/logo-light.png') }}" alt=""
                                            height="20">
                                    </a>
                                    <p class="text-muted mt-3 fs-15 fw-medium">Premium Admin & Dashboard Template</p>
                                </div>

                                <div class="p-2 mt-4">
                                    <div class="text-center">
                                        <h5 class="text-primary">Confirm Password</h5>
                                        <p class="text-muted">This is a secure area of the application. Please confirm
                                            your password before continuing.</p>

                                        <!-- Security Icon (Velzon style) -->
                                        <lord-icon src="https://cdn.lordicon.com/kkcllwsu.json" trigger="loop"
                                            colors="primary:#0ab39c" class="avatar-xl mt-4"></lord-icon>
                                    </div>

                                    <!-- Laravel Errors -->
                                    <x-input-error class="text-danger text-center mb-3" :messages="$errors->get('password')" />

                                    <form method="POST" action="{{ route('password.confirm') }}">
                                        @csrf

                                        <!-- Password -->
                                        <div class="mb-4">
                                            <label for="password" class="form-label">{{ __('Password') }}</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input id="password" type="password"
                                                    class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="current-password"
                                                    placeholder="Enter your password">
                                                <button
                                                    class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                    type="button" id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                                <x-input-error :messages="$errors->get('password')" class="invalid-feedback" />
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="text-center mt-4">
                                            <button class="btn btn-success w-100" type="submit">
                                                {{ __('Confirm') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card-body -->
                        </div>
                        <!-- end card -->

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

    <!-- Scripts -->
    <script src="{{ asset('Backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/plugins.js') }}"></script>

    <!-- Particles -->
    <script src="{{ asset('Backend/assets/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/pages/particles.app.js') }}"></script>

    <!-- Password eye toggle -->
    <script src="{{ asset('Backend/assets/js/pages/password-addon.init.js') }}"></script>
</body>

</html>
