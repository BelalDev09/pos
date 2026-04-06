<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default"
    data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up | {{ config('app.name', 'Your App') }}</title>
    <meta content="Premium Admin & Dashboard Template" name="description" />
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
                                        <h5 class="text-primary">Create New Account</h5>
                                        <p class="text-muted">Get your free {{ config('app.name') }} account now.</p>
                                    </div>

                                    <form method="POST" action="{{ route('register') }}" class="needs-validation"
                                        novalidate>
                                        @csrf

                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">{{ __('Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}" required autofocus autocomplete="name"
                                                placeholder="Enter your name">
                                            <x-input-error :messages="$errors->get('name')" class="invalid-feedback" />
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ __('Email') }} <span
                                                    class="text-danger">*</span></label>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="username"
                                                placeholder="Enter email address">
                                            <x-input-error :messages="$errors->get('email')" class="invalid-feedback" />
                                        </div>

                                        <!-- Password with eye toggle -->
                                        <div class="mb-3">
                                            <label class="form-label" for="password">{{ __('Password') }}</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input id="password" type="password"
                                                    class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="new-password"
                                                    placeholder="Enter password" onpaste="return false"
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                                                <button
                                                    class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                    type="button" id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                                <x-input-error :messages="$errors->get('password')" class="invalid-feedback" />
                                            </div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="mb-3">
                                            <label for="password_confirmation"
                                                class="form-label">{{ __('Confirm Password') }}</label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input id="password_confirmation" type="password"
                                                    class="form-control pe-5 @error('password_confirmation') is-invalid @enderror"
                                                    name="password_confirmation" required autocomplete="new-password"
                                                    placeholder="Confirm password">
                                                <x-input-error :messages="$errors->get('password_confirmation')" class="invalid-feedback" />
                                            </div>
                                        </div>

                                        <!-- Terms -->
                                        <div class="mb-4">
                                            <p class="mb-0 fs-12 text-muted fst-italic">
                                                By registering you agree to the
                                                <a href="#"
                                                    class="text-primary text-decoration-underline fst-normal fw-medium">Terms
                                                    of Use</a>
                                            </p>
                                        </div>

                                        <!-- Password strength hints (Velzon style) -->
                                        <div id="password-contain" class="p-3 bg-light mb-4 rounded">
                                            <h5 class="fs-13">Password must contain:</h5>
                                            <p id="pass-length" class="invalid fs-12 mb-2">Minimum <b>8 characters</b>
                                            </p>
                                            <p id="pass-lower" class="invalid fs-12 mb-2">At least <b>lowercase</b>
                                                letter (a-z)</p>
                                            <p id="pass-upper" class="invalid fs-12 mb-2">At least <b>uppercase</b>
                                                letter (A-Z)</p>
                                            <p id="pass-number" class="invalid fs-12 mb-0">At least <b>number</b>
                                                (0-9)</p>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-success w-100">
                                                {{ __('Sign Up') }}
                                            </button>
                                        </div>

                                        <!-- Social Signup (optional) -->
                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 text-muted fw-medium">Create account with</h5>
                                            </div>
                                            <div>
                                                <button type="button"
                                                    class="btn btn-primary btn-icon waves-effect waves-light"><i
                                                        class="ri-facebook-fill fs-16"></i></button>
                                                <button type="button"
                                                    class="btn btn-danger btn-icon waves-effect waves-light"><i
                                                        class="ri-google-fill fs-16"></i></button>
                                                <button type="button"
                                                    class="btn btn-dark btn-icon waves-effect waves-light"><i
                                                        class="ri-github-fill fs-16"></i></button>
                                                <button type="button"
                                                    class="btn btn-info btn-icon waves-effect waves-light"><i
                                                        class="ri-twitter-fill fs-16"></i></button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Already have account -->
                                    <div class="mt-5 text-center">
                                        <p class="mb-0 text-muted">
                                            Already have an account ?
                                            <a href="{{ route('login') }}"
                                                class="fw-semibold text-primary text-decoration-underline"> Signin </a>
                                        </p>
                                    </div>
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
    {{-- <script src="{{ asset('Backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/plugins.js') }}"></script> --}}

    <!-- Particles -->
    {{-- <script src="{{ asset('Backend/assets/libs/particles.js/particles.js') }}"></script> --}}
    {{-- <script src="{{ asset('Backend/assets/js/pages/particles.app.js') }}"></script> --}}

    <!-- Password eye toggle -->
    {{-- <script src="{{ asset('Backend/assets/js/pages/password-addon.init.js') }}"></script> --}}

    <!-- Password strength validation hints (Velzon script) -->
    {{-- <script src="{{ asset('Backend/assets/js/pages/passowrd-create.init.js') }}"></script> --}}

    <!-- Form validation (for HTML5 required + custom messages) -->
    {{-- <script src="{{ asset('Backend/assets/js/pages/form-validation.init.js') }}"></script> --}}

</body>

</html>
