        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="{{ asset('Backend/assets/pages/index.html') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('Backend/assets/images/logo-sm.png') }}" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('Backend/assets/images/logo-dark.png') }}" alt=""
                                        height="17">
                                </span>
                            </a>

                            <a href="{{ asset('Backend/assets/pages/index.html') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('Backend/assets/images/logo-sm.png') }}" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('Backend/assets/images/logo-light.png') }}" alt=""
                                        height="17">
                                </span>
                            </a>
                        </div>
                        {{-- <div class="app-menu navbar-menu" id="sidebar-menu"> --}}
                        {{-- TOPNAV HAMBURGER --}}
                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                        <!-- </div> -->

                        <!-- Clock and Date/time-->

                        <div class="bookmark-wrapper d-flex align-items-center">

                            <ul class="nav navbar-nav d-xl-none">
                                <li class="nav-item">
                                    <a class="nav-link menu-toggle" href="javascript:void(0);">
                                        <i class="ficon" data-feather="menu"></i>
                                    </a>
                                </li>
                            </ul>

                            <ul class="nav navbar-nav bookmark-icons">
                                <li class="nav-item d-none d-lg-block">

                                    <div class="dashboard-datetime">

                                        <div class="date-part">
                                            <i class="ri-calendar-2-line"></i>
                                            <span>{{ date('D, d M Y') }}</span>
                                        </div>

                                        <div class="divider"></div>

                                        <div class="time-part">
                                            <i class="ri-time-line"></i>
                                            <span id="timer"></span>
                                        </div>

                                    </div>

                                </li>
                            </ul>

                        </div>

                    </div>

                    <div class="d-flex align-items-center">
                        {{-- light mode and dark mode --}}

                        <div class="ms-1 header-item d-none d-sm-flex mx-4">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>

                        <div class="dropdown d-md-none topbar-head-dropdown header-item">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="bx bx-search fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">
                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..."
                                                aria-label="Recipient's username">
                                            <button class="btn btn-primary" type="submit"><i
                                                    class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- notification -->
                        <li class="nav-item dropdown border-left">
                            @auth
                                @php
                                    $user = auth()->user();

                                    $unread = $user->unreadNotifications->sortByDesc('created_at');
                                    $read = $user->readNotifications->sortByDesc('created_at');

                                    $notifications = $unread->merge($read)->take(5);
                                    $unreadCount = $unread->count();
                                @endphp
                            @endauth


                            <a class="nav-link count-indicator dropdown-toggle position-relative"
                                href="javascript:void(0)" data-bs-toggle="dropdown">

                                <i class="mdi mdi-bell-outline fs-4"></i>

                                @if ($unreadCount > 0)
                                    <span class="notification-dot">{{ $unreadCount }}</span>
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-end navbar-dropdown notification-dropdown">

                                <div class="notification-header d-flex justify-content-between">
                                    <span>Notifications</span>

                                    @if ($unreadCount > 0)
                                        <a href="{{ route('notifications.markAllRead') }}">Mark all</a>
                                    @endif
                                </div>

                                @forelse ($notifications as $notification)
                                    <a href="{{ route('notifications.read', $notification->id) }}"
                                        class="notification-item d-flex align-items-start
                {{ $notification->read_at ? '' : 'unread' }}">

                                        <div class="icon-wrapper me-2">
                                            <i class="mdi mdi-bell-ring-outline"></i>
                                        </div>

                                        <div class="content flex-grow-1">
                                            <p class="title mb-0 fw-semibold">
                                                {{ $notification->data['title'] ?? 'Notification' }}
                                            </p>

                                            @if (!empty($notification->data['thankyou']))
                                                <small class="text-muted">
                                                    {{ $notification->data['thankyou'] }}
                                                </small>
                                            @endif

                                            <br>
                                            <small class="text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>

                                        @if (!$notification->read_at)
                                            <span class="badge bg-primary ms-2">NEW</span>
                                        @endif
                                    </a>
                                @empty
                                    <div class="notification-empty text-center p-3">
                                        No notifications
                                    </div>
                                @endforelse
                            </div>
                        </li>




                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            @php
                                $user = Auth::user();
                            @endphp

                            <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <span class="d-flex align-items-center">
                                    {{-- Avatar --}}
                                    <img class="rounded-circle header-profile-user"
                                        src="{{ auth()->user()?->avatar
                                            ? asset('storage/' . auth()->user()->avatar)
                                            : asset('backend/assets/images/users/avatar-1.jpg') }}"
                                        alt="User Avatar">

                                    <span class="text-start ms-xl-2">
                                        {{-- Name --}}
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                            {{ auth()->user()?->name }}
                                        </span>

                                        {{-- Role / Designation --}}
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">
                                            {{ ucfirst($user->role ?? 'User') }}
                                        </span>
                                    </span>
                                </span>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <h6 class="dropdown-header">Welcome {{ auth()->user()?->name }}!</h6>
                                @can('profile.view')
                                    <a class="dropdown-item" href="{{ route('admin.profile') }}"><i
                                            class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                            class="align-middle">Profile Settings</span></a>
                                @endcan
                                {{-- @can('profile.edit')
                                    <a class="dropdown-item" href="{{ route('profile.update') }}"><i
                                            class="mdi mdi-account-edit text-muted fs-16 align-middle me-1"></i> <span
                                            class="align-middle">Profile Settings</span></a>
                                @endcan --}}
                                <div class="dropdown-divider"></div>
                                {{-- <a class="dropdown-item" href="#"><span
                                        class="badge bg-success-subtle text-success mt-1 float-end">New</span><i
                                        class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle">Settings</span></a> --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                        <i class="ri-logout-box-line align-middle me-1"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- sidebar --}}

        </header>
        <!-- End Header -->
