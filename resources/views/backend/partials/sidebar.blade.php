{{-- SIDEBAR --}}
<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('Backend/assets/images/logo-sm.png') }}" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('Backend/assets/images/logo-dark.png') }}" height="17">
            </span>
        </a>

        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('Backend/assets/images/logo-sm.png') }}" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('Backend/assets/images/logo-light.png') }}" height="17">
            </span>
        </a>
    </div>

    <div id="scrollbar" style="max-height: calc(100vh - 120px); overflow-y: auto;"> {{-- auto fixed sidebar --}}
        <div class="container-fluid">
            <ul class="navbar-nav" id="navbar-nav" style="padding-bottom: 20px;">

                {{-- DASHBOARD --}}

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="ri-layout-grid-line"></i>
                        <span>Home</span>
                    </a>
                </li>

                {{-- MENU --}}
                <li class="menu-title"><span>Manager</span></li>


                {{-- APPROVALS --}}



                {{-- USER MANAGEMENT --}}

                {{-- CONTENT --}}
                <li class="menu-title"><span>Content</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarUser" data-bs-toggle="collapse"
                        data-parent="#navbar-nav">
                        <i class="ri-user-3-line"></i>
                        <span>User Management</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarUser">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('admin.user.list') }}"
                                    class="nav-link {{ request()->routeIs('admin.users.list') ? 'active' : '' }}">
                                    Users List
                                </a>
                            </li>
                            {{-- @can('roles.manage') --}}
                            <li class="nav-item">
                                <a href="{{ route('admin.roles.list') }}"
                                    class="nav-link {{ request()->routeIs('admin.roles.list') ? 'active' : '' }}">
                                    Roles
                                </a>
                            </li>
                            {{-- @endcan --}}
                            {{-- @can('permissions.manage') --}}
                            <li class="nav-item">
                                <a href="{{ route('admin.permissions.list') }}"
                                    class="nav-link {{ request()->routeIs('admin.permissions.list') ? 'active' : '' }}">
                                    Permissions
                                </a>
                            </li>
                            {{-- @endcan --}}
                        </ul>
                    </div>
                </li>


                {{-- Category --}}

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                        href="{{ route('admin.categories.index') }}">
                        <i class="ri-book-3-line"></i>
                        <span>Category</span>
                    </a>
                </li>

                {{-- Activity Logs --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.activity.history') ? 'active' : '' }}"
                        href="#">
                        <i class="ri-chat-check-line me-1"></i>
                        <span>Activity Logs</span>
                    </a>
                </li>

                {{-- Order History --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('admin.order.status.history') ? 'active' : '' }}"
                        href="#">
                        <i class="ri-file-warning-line me-1"></i>
                        <span>Order History</span>
                    </a>
                </li>


                {{-- Menu Page --}}

                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sideMenu" data-bs-toggle="collapse" data-parent="#navbar-nav">
                        <i class="ri-file-list-3-line"></i>
                        <span>Menu Page</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse menu-dropdown" id="sideMenu">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('menu_items.*') ? 'active' : '' }}">
                                    Menu Items
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('menu_item_ingredients.*') ? 'active' : '' }}">
                                    Menu Item Ingredients
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>



                {{-- Order Page --}}
                {{-- @can('support.manage')
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sideOrder" data-bs-toggle="collapse"
                            data-parent="#navbar-nav">
                            <i class="ri-file-list-3-line"></i>
                            <span>Order Page</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse menu-dropdown" id="sideOrder">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('order.index') }}"
                class="nav-link {{ request()->routeIs('order.*') ? 'active' : '' }}">
                Order
                </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('customer.index') }}"
                        class="nav-link {{ request()->routeIs('customer.*') ? 'active' : '' }}">
                        Customer
                    </a>
                </li>
            </ul>
        </div>
        </li>
        @endcan --}}

        {{-- Pages --}}
        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidePages" data-bs-toggle="collapse"
                data-parent="#navbar-nav">
                <i class="ri-file-list-3-line"></i>
                <span>Pages</span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse menu-dropdown" id="sidePages">
                <ul class="nav nav-sm flex-column">
                    <li class="nav-item">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('admin.restaurants.*') ? 'active' : '' }}">
                            Restaurant
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('restaurant_tables.*') ? 'active' : '' }}">
                            Restaurant Tables
                        </a>
                    </li>

                </ul>
            </div>
        </li>



        {{-- SETTINGS --}}

        <li class="nav-item">
            <a class="nav-link menu-link" href="#sidebarSettings" data-bs-toggle="collapse"
                data-parent="#navbar-nav">
                <i class="ri-settings-3-line"></i>
                <span>Settings</span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse menu-dropdown" id="sidebarSettings">
                <ul class="nav nav-sm flex-column">
                    {{-- @can('profile.manage') --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.profile') }}"
                            class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                            Profile Settings
                        </a>
                    </li>
                    {{-- @endcan --}}
                    {{-- @can('settings.system') --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.system.setting') }}"
                            class="nav-link {{ request()->routeIs('admin.system.setting.*') ? 'active' : '' }}">
                            System Setting
                        </a>
                    </li>
                    {{-- @endcan --}}
                    {{-- @can('settings.admin') --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.setting') }}"
                            class="nav-link {{ request()->routeIs('admin.setting.*') ? 'active' : '' }}">
                            Admin Setting
                        </a>
                    </li>
                    {{-- @endcan --}}
                    {{-- @can('setting.mail') --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.setting.mail') }}"
                            class="nav-link {{ request()->routeIs('admin.setting.mail.*') ? 'active' : '' }}">
                            Mail Setting
                        </a>
                    </li>
                    {{-- @endcan --}}
                </ul>
            </div>
        </li>



        </ul>
    </div>
</div>

<div class="sidebar-background"></div>
</div>

<!-- JavaScript for Persistent Collapse State -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('navbar-nav');
        const collapses = sidebar.querySelectorAll('.collapse.menu-dropdown');
        const links = sidebar.querySelectorAll('.nav-link');

        //  Load active link from localStorage
        const activeRoute = localStorage.getItem('activeSidebarLink');
        if (activeRoute) {
            links.forEach(link => {
                if (link.getAttribute('href') === activeRoute) {
                    link.classList.add('active');

                    // Open parent collapse if exists
                    const parentCollapse = link.closest('.collapse.menu-dropdown');
                    if (parentCollapse) {
                        parentCollapse.classList.add('show');
                        const toggler = parentCollapse.previousElementSibling;
                        if (toggler && toggler.tagName === 'A') {
                            toggler.classList.remove('collapsed');
                            toggler.setAttribute('aria-expanded', 'true');
                        }
                    }
                }
            });
        }

        //  Save active link on click
        links.forEach(link => {
            link.addEventListener('click', function() {
                // Remove previous active
                links.forEach(l => l.classList.remove('active'));
                // Set current active
                this.classList.add('active');
                localStorage.setItem('activeSidebarLink', this.getAttribute('href'));
            });
        });

        //  Collapse state persistence
        collapses.forEach(collapse => {
            const id = collapse.id;
            const isOpen = localStorage.getItem(`sidebar-${id}`) === 'true';
            if (isOpen) {
                collapse.classList.add('show');
                const toggler = sidebar.querySelector(`[href="#${id}"]`);
                if (toggler) toggler.classList.remove('collapsed');
                toggler?.setAttribute('aria-expanded', 'true');
            }

            collapse.addEventListener('show.bs.collapse', function() {
                localStorage.setItem(`sidebar-${id}`, 'true');

                // Close other open collapses (accordion behavior)
                collapses.forEach(other => {
                    if (other !== this && other.classList.contains('show')) {
                        const otherId = other.id;
                        localStorage.setItem(`sidebar-${otherId}`, 'false');
                        bootstrap.Collapse.getInstance(other)?.hide();
                    }
                });
            });

            collapse.addEventListener('hide.bs.collapse', function() {
                localStorage.setItem(`sidebar-${id}`, 'false');
            });
        });
    });
</script>

<style>
    /* Sidebar scrollbar styling */
    #scrollbar {
        scroll-behavior: smooth;
    }

    #scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    #scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    #scrollbar::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    #scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    /* Ensure dropdown menus are not clipped */
    .navbar-menu .menu-dropdown {
        position: relative;
        z-index: 1000;
    }

    .navbar-menu .nav-item {
        position: relative;
    }

    /* Prevent sidebar hover dropdown clipping */
    .app-menu {
        overflow: visible;
    }

    .navbar-menu {
        overflow: visible;
    }

    /* Smooth expand animation for dropdowns */
    .navbar-nav .collapse.menu-dropdown {
        overflow: hidden;
        transition: all 0.3s ease-in-out;
    }

    .navbar-nav .collapse.menu-dropdown.show {
        overflow: visible;
    }
</style>

{{-- <style>
    /* Active link style */
    .navbar-nav .nav-link.active {
        background-color: #2eece3;
        color: #0d6efd;
        font-weight: 600;
        border-radius: 5px;
    }

    /* Hover effect */
    .navbar-nav .nav-link:hover {
        background-color: #f1f3f5;
        color: #0d6efd;
    }

    .navbar-nav .collapse .nav-link:hover {
        background-color: #e9efec;
        color: #0d6efd;
    }
</style> --}}