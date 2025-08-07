<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Dashboard') . ' - ' . config('app.name'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- TradingView Library -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    
    <!-- Custom Dashboard CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--light-color);
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1c42c2 0%, #2563eb 100%);
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-header h5 {
            color: white;
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .sidebar-header small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.875rem;
        }

        .sidebar-menu {
            padding: 1rem;
        }

        .sidebar-menu .nav-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-menu .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .sidebar-menu .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: white;
            border-radius: 0 3px 3px 0;
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-area {
            padding: 2rem;
        }

        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-card {
            text-align: center;
            padding: 2rem 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-card-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            opacity: 0.1;
            font-size: 2.5rem;
        }

        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px 16px 0 0 !important;
            padding: 1.25rem 1.5rem;
            margin-bottom: 0;
        }

        .card-header h5 {
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }

        .dashboard-card .card-body {
            padding: 1.5rem;
        }

        /* TradingView container styling */
        .tradingview-widget-container {
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
            width: 100%;
            height: 350px;
            position: relative;
        }

        .tradingview-widget-container iframe {
            width: 100% !important;
            height: 350px !important;
            border: none;
            border-radius: 8px;
        }

        #tradingview_dashboard_chart,
        #tradingview_trade_chart {
            width: 100% !important;
            height: 350px !important;
            border-radius: 8px;
            background: #ffffff;
        }

        /* Ensure TradingView loads properly */
        .tradingview-widget-container:empty::after {
            content: 'Loading chart...';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #6c757d;
            font-size: 1rem;
        }

        /* Ensure proper chart loading */
        .tradingview-widget-container script {
            display: none;
        }

        /* Smooth page transitions */
        .content-area {
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .nav-link {
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-color);
            border-radius: 0 3px 3px 0;
        }

        /* Enhanced transaction items */
        .transaction-item {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .transaction-item:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transform: translateX(4px);
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        /* Quick action buttons */
        .btn-outline-primary:hover,
        .btn-outline-success:hover,
        .btn-outline-warning:hover,
        .btn-outline-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .price-up {
            color: var(--success-color);
        }

        .price-down {
            color: var(--danger-color);
        }

        .transaction-item {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s;
        }

        .transaction-item:hover {
            background-color: #f8fafc;
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-area {
                padding: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="d-flex align-items-center justify-content-center">
                    <div class="bg-white rounded me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <span class="text-primary fw-bold">TT</span>
                    </div>
                    <div>
                        <h5 class="mb-0">Elite Forex Pro</h5>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i>
                            {{ __('Overview') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('trade.*') ? 'active' : '' }}" href="{{ route('trade.index') }}">
                            <i class="fas fa-exchange-alt"></i>
                            {{ __('Trade') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wallet.*') ? 'active' : '' }}" href="{{ route('wallet.index') }}">
                            <i class="fas fa-wallet"></i>
                            {{ __('Wallet') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('deposit.*') ? 'active' : '' }}" href="{{ route('deposit.index') }}">
                            <i class="fas fa-plus-circle"></i>
                            {{ __('Deposit') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('withdrawal.*') ? 'active' : '' }}" href="{{ route('withdrawal.index') }}">
                            <i class="fas fa-minus-circle"></i>
                            {{ __('Withdrawal') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}" href="{{ route('history.index') }}">
                            <i class="fas fa-history"></i>
                            {{ __('History') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                            <i class="fas fa-cog"></i>
                            {{ __('Settings') }}
                        </a>
                    </li>
                    
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item mt-3">
                        <h6 class="px-3 text-muted">{{ __('Admin') }}</h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-shield-alt"></i>
                            {{ __('Admin Panel') }}
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary d-md-none me-3" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0">@yield('page-title', __('Dashboard'))</h4>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary position-relative" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            @if($pendingDeposits > 0 || $pendingWithdrawals > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $pendingDeposits + $pendingWithdrawals }}
                            </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if($pendingDeposits > 0)
                            <li><a class="dropdown-item" href="{{ route('deposit.history') }}">
                                <i class="fas fa-plus-circle text-warning me-2"></i>
                                {{ $pendingDeposits }} {{ __('pending deposits') }}
                            </a></li>
                            @endif
                            @if($pendingWithdrawals > 0)
                            <li><a class="dropdown-item" href="{{ route('withdrawal.history') }}">
                                <i class="fas fa-minus-circle text-info me-2"></i>
                                {{ $pendingWithdrawals }} {{ __('pending withdrawals') }}
                            </a></li>
                            @endif
                            @if($pendingDeposits == 0 && $pendingWithdrawals == 0)
                            <li><span class="dropdown-item-text">{{ __('No new notifications') }}</span></li>
                            @endif
                        </ul>
                    </div>
                    
                    <!-- User Menu -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="fas fa-cog me-2"></i>{{ __('Settings') }}
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Auto-hide sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = event.target.closest('[onclick="toggleSidebar()"]');
            
            if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggleBtn) {
                sidebar.classList.remove('show');
            }
        });

        // Prevent unnecessary reloads on current page
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                const linkPath = new URL(link.href).pathname;
                if (linkPath === currentPath) {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        // Add a subtle visual feedback instead of reloading
                        this.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            this.style.transform = '';
                        }, 150);
                    });
                }
            });
        });

        // Auto-refresh data every 30 seconds
        setInterval(function() {
            // Refresh crypto prices and portfolio value
            // This would make AJAX calls to update data
        }, 30000);
    </script>

    @stack('scripts')
</body>
</html>
