<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light"
    data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable"
    data-theme="default" data-theme-colors="default" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS')</title>
    @include('backend.partials.header')
    @stack('styles')
</head>

<body>
    <div id="layout-wrapper">
        @include('backend.partials.navbar')
        @include('backend.partials.sidebar')

        <div class="vertical-overlay"></div>

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            @include('backend.partials.footer')
        </div>
    </div>

    @include('backend.partials.scripts')
    @stack('scripts')
</body>

</html>
