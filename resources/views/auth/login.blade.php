<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default"
    data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In | {{ config('app.name', 'Laravel') }}</title>
    <meta content="Premium Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('Backend/assets/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('Backend/assets/js/layout.js') }}"></script>

    <!-- Bootstrap Css -->
    <link href="{{ asset('Backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('Backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('Backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
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
                                        <h5 class="text-primary">Welcome Back !</h5>
                                        <p class="text-muted">Sign in to continue to {{ config('app.name') }}.</p>
                                    </div>

                                    <!-- Laravel Session Status -->
                                    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <!-- Email / Username -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ __('Email') }}</label>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autofocus autocomplete="username"
                                                placeholder="Enter email">
                                            <x-input-error :messages="$errors->get('email')" class="invalid-feedback" />
                                        </div>

                                        <!-- Password with eye toggle -->
                                        <div class="mb-3">
                                            <div class="float-end">
                                                @if (Route::has('password.request'))
                                                    <a class="text-muted" href="{{ route('password.request') }}">
                                                        {{ __('Forgot your password?') }}
                                                    </a>
                                                @endif
                                            </div>
                                            <label class="form-label" for="password">{{ __('Password') }}</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input id="password" type="password"
                                                    class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="current-password"
                                                    placeholder="Enter password">
                                                <button
                                                    class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                    type="button" id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                                <x-input-error :messages="$errors->get('password')" class="invalid-feedback" />
                                            </div>
                                        </div>

                                        <!-- Remember Me -->
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                {{ __('Remember me') }}
                                            </label>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">
                                                {{ __('Sign In') }}
                                            </button>
                                        </div>

                                        <!-- Social Login (optional - implement if you have social auth) -->
                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 text-muted fw-medium">- Sign In with -</h5>
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

                                    <!-- Signup Link -->
                                    <div class="mt-5 text-center">
                                        <p class="mb-0 text-muted">
                                            Don't have an account ?
                                            <a href="{{ route('register') }}"
                                                class="fw-semibold text-primary text-decoration-underline"> Signup </a>
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
                                </script> {{ config('app.name') }}. Crafted with <i
                                    class="mdi mdi-heart text-danger"></i> by Themesbrand
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

    <!-- particles js -->
    <script src="{{ asset('Backend/assets/libs/particles.js/particles.js') }}"></script>
    <script src="{{ asset('Backend/assets/js/pages/particles.app.js') }}"></script>

    <!-- Password show/hide init -->
    <script src="{{ asset('Backend/assets/js/pages/password-addon.init.js') }}"></script>

</body>

</html>
