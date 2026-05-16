{{-- SIDEBAR --}}
<div class="app-menu navbar-menu border-end-dashed">

    {{-- LOGO --}}
    <div class="navbar-brand-box">
        <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ isset($setting) && $setting->admin_logo ? asset($setting->admin_logo) : asset('Backend/assets/images/no-image.png') }}" style="width:40px;height:40px;object-fit:contain;" alt="Logo">
            </span>
            <span class="logo-lg">
                <img src="{{ isset($setting) && $setting->admin_logo ? asset($setting->admin_logo) : asset('Backend/assets/images/no-image.png') }}" style="width:100%;max-height:50px;object-fit:contain;" alt="Logo">
            </span>
        </a>
    </div>

    <div id="scrollbar" data-simplebar class="h-100">
        <ul class="navbar-nav" id="navbar-nav">

            {{-- HOME --}}
            <li class="nav-item">
                <a class="nav-link menu-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="ri-home-4-line"></i> <span>🏠 Home</span>
                </a>
            </li>

            {{-- USER MANAGEMENT --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarUser" data-bs-toggle="collapse" role="button">
                    <i class="ri-user-settings-line"></i> <span>👥 User Management</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarUser">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">Users</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Roles</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Sales Commission Agents</a></li>
                    </ul>
                </div>
            </li>

            {{-- CONTACTS --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarContacts" data-bs-toggle="collapse" role="button">
                    <i class="ri-contacts-book-line"></i> <span>📇 Contacts</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarContacts">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="{{ route('admin.suppliers.index') }}" class="nav-link">Suppliers</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Customers</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Customer Groups</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Import Contacts</a></li>
                    </ul>
                </div>
            </li>

            {{-- PRODUCTS --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarProducts" data-bs-toggle="collapse" role="button">
                    <i class="ri-box-3-line"></i> <span>📦 Products</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarProducts">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">List Products</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Add Product</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Update Price</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Print Labels</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Variations</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Import Products</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Import Opening Stock</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Selling Price Group</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Units</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Categories</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Brands</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Warranties</a></li>
                    </ul>
                </div>
            </li>

            {{-- PURCHASES --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarPurchases" data-bs-toggle="collapse" role="button">
                    <i class="ri-shopping-cart-line"></i> <span>🛒 Purchases</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarPurchases">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">List Purchases</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Add Purchase</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Purchase Return</a></li>
                    </ul>
                </div>
            </li>

            {{-- SELL / SALES --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarSales" data-bs-toggle="collapse" role="button">
                    <i class="ri-money-dollar-box-line"></i> <span>💰 Sell / Sales</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarSales">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">All Sales</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Add Sale</a></li>
                        <li class="nav-item"><a href="#" class="nav-link fw-bold text-primary">POS</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Drafts</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Quotations</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Sell Return</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Shipments</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Discounts</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Import Sales</a></li>
                    </ul>
                </div>
            </li>

            {{-- STOCK TRANSFERS --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarStockTrans" data-bs-toggle="collapse" role="button">
                    <i class="ri-truck-line"></i> <span>🚚 Stock Transfers</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarStockTrans">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">List Stock Transfers</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Add Stock Transfer</a></li>
                    </ul>
                </div>
            </li>

            {{-- STOCK ADJUSTMENT --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarStockAdj" data-bs-toggle="collapse" role="button">
                    <i class="ri-equalizer-line"></i> <span>📊 Stock Adjustment</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarStockAdj">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">List Stock Adjustments</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Add Stock Adjustment</a></li>
                    </ul>
                </div>
            </li>

            {{-- EXPENSES --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarExpenses" data-bs-toggle="collapse" role="button">
                    <i class="ri-wallet-3-line"></i> <span>💵 Expenses</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarExpenses">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">List Expenses</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Add Expense</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Expense Categories</a></li>
                    </ul>
                </div>
            </li>

            {{-- ACCOUNTING --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarAccounting" data-bs-toggle="collapse" role="button">
                    <i class="ri-bank-line"></i> <span>🏦 Payment Accounts / Accounting</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarAccounting">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">List Accounts</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Balance Sheet</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Trial Balance</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Cash Flow</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Payment Account Report</a></li>
                    </ul>
                </div>
            </li>

            {{-- REPORTS --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarReports" data-bs-toggle="collapse" role="button">
                    <i class="ri-line-chart-line"></i> <span>📈 Reports</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarReports">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">Profit/Loss</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Purchase & Sale</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Tax Report</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Supplier & Customer Report</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Stock Report</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Trending Products</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Expense Report</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Register Report</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Activity Log</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">আরও অনেক রিপোর্ট</a></li>
                    </ul>
                </div>
            </li>

            {{-- NOTIFICATION --}}
            <li class="nav-item">
                <a class="nav-link menu-link" href="#">
                    <i class="ri-notification-badge-line"></i> <span>🔔 Notification Templates</span>
                </a>
            </li>

            {{-- SETTINGS --}}
            <li class="nav-item">
                <a class="nav-link menu-link collapsed" href="#sidebarSettings" data-bs-toggle="collapse" role="button">
                    <i class="ri-settings-5-line"></i> <span>⚙️ Settings</span>
                </a>
                <div class="collapse menu-dropdown" id="sidebarSettings">
                    <ul class="nav nav-sm flex-column">
                        <li class="nav-item"><a href="#" class="nav-link">Business Settings</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Business Locations</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Invoice Settings</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Barcode Settings</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Receipt Printers</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Tax Rates</a></li>
                        <li class="nav-item"><a href="#" class="nav-link">Package Subscription</a></li>
                    </ul>
                </div>
            </li>

            <li class="menu-title"><span>Modules</span></li>

            <li class="nav-item">
                <a class="nav-link menu-link" href="#">
                    <i class="ri-shield-user-line"></i> <span>👨‍💼 HRM Module</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#">
                    <i class="ri-task-line"></i> <span>✅ Essentials / Todo Module</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link menu-link" href="#">
                    <i class="ri-shopping-basket-line"></i> <span>🛍️ WooCommerce Integration</span>
                </a>
            </li>

        </ul>
    </div>
    <div class="sidebar-background"></div>
</div>