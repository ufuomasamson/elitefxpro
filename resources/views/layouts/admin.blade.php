<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Elite Forex Pro') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        <!-- Custom CSS -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Custom JavaScript -->
        <script src="{{ asset('js/app.js') }}"></script>
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            
            .admin-sidebar-gradient {
                background: linear-gradient(180deg, #dc2626 0%, #991b1b 100%);
            }
            
            .admin-glow {
                box-shadow: 0 0 20px rgba(220, 38, 38, 0.4);
            }
            
            .admin-card {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
            <!-- Admin Sidebar -->
            <div class="fixed inset-y-0 left-0 z-50 w-64 admin-sidebar-gradient transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" 
                 :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
                
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 border-b border-red-600">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                            <span class="text-red-600 font-bold text-sm">TA</span>
                        </div>
                        <span class="text-xl font-bold text-white">Elite Forex Pro Admin</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-8 px-4 space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.users') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        Users
                    </a>
                    
                    <a href="{{ route('admin.transactions') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.transactions') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Transactions
                    </a>
                    
                    <a href="{{ route('admin.deposits') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.deposits') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Deposits
                    </a>
                    
                    <a href="{{ route('admin.withdrawals') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.withdrawals') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                        </svg>
                        Withdrawals
                    </a>
                    
                    <a href="{{ route('admin.crypto-wallets') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.crypto-wallets') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Crypto Wallets
                    </a>
                    
                    <a href="{{ route('admin.bank-details') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.bank-details') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Bank Details
                    </a>
                    
                    <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.settings') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                    
                    <a href="{{ route('admin.logs') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.logs') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        System Logs
                    </a>
                    
                    <a href="{{ route('admin.chat') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.chat') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Live Chat
                        @if(isset($unreadMessages) && $unreadMessages > 0)
                            <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-yellow-500 rounded-full">{{ $unreadMessages }}</span>
                        @endif
                    </a>
                </nav>

                <!-- User Info -->
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-red-600">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-red-200 truncate">Administrator</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('dashboard') }}" class="text-white/70 hover:text-white transition-colors" title="User Dashboard">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-white/70 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile sidebar overlay -->
            <div x-show="sidebarOpen" class="fixed inset-0 z-40 lg:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-gray-600 opacity-75" @click="sidebarOpen = false"></div>
            </div>

            <!-- Main content -->
            <div class="flex-1 flex flex-col lg:ml-0">
                <!-- Top navigation -->
                <div class="bg-white shadow-sm border-b border-gray-200">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex items-center">
                                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-600 lg:hidden">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div class="lg:ml-0">
                                    @if (isset($header))
                                        {{ $header }}
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="flex items-center space-x-4">
                                <!-- Notifications -->
                                <div class="relative">
                                    <button class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.343 2.343l1.414 1.414m9.899 9.899l1.414 1.414m-3.536 3.536l1.414 1.414M6.343 6.343l1.414 1.414m7.071 7.071l1.414 1.414m-9.899 0l1.414-1.414M20 12h-2M6 12H4m3-6V4m6 16v-2"></path>
                                        </svg>
                                    </button>
                                    <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                                </div>
                                
                                <!-- Search -->
                                <div class="hidden md:block">
                                    <input type="text" placeholder="Quick search..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Header -->
                @hasSection('header')
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
