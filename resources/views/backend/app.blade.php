<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default"
    data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard')</title>
    {{-- header --}}
    @include('backend.partials.header')

    <!-- Page Specific Styles -->
    @stack('styles')
</head>

<body>

    <div id="layout-wrapper">

        {{-- TOP NAVBAR --}}
        @include('backend.partials.navbar')

        {{-- sidebar --}}
        @include('backend.partials.sidebar')

        <div class="vertical-overlay"></div>

        {{-- MAIN CONTENT --}}
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    {{-- PAGE CONTENT --}}
                    @yield('content')

                </div>
            </div>

            {{-- FOOTER --}}
            @include('backend.partials.footer')
        </div>

    </div>

    {{-- BACK TO TOP --}}
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    {{-- PRELOADER --}}
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm"></div>
        </div>
    </div>
    {{--  scripts --}}
    @include('backend.partials.scripts')
    <!-- Page Specific Scripts -->
    @stack('scripts')

</body>

</html>
