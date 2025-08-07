<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Elite Forex Pro') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Custom CSS -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Custom JavaScript -->
        <script src="{{ asset('js/app.js') }}"></script>
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            
            .sidebar-gradient {
                background: linear-gradient(180deg, #1c42c2 0%, #2563eb 100%);
            }
            
            .crypto-glow {
                box-shadow: 0 0 20px rgba(28, 66, 194, 0.4);
            }
            
            .balance-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .transaction-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            
            /* Flag Icons CSS */
            .flag-icon {
                width: 1.33em;
                height: 1em;
                background-size: contain;
                background-position: 50%;
                background-repeat: no-repeat;
                display: inline-block;
            }

            .flag-icon-us {
                background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjQjIyMjM0Ii8+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMy44NDYxNSIgZmlsbD0id2hpdGUiLz4KPHJlY3QgeT0iNy42OTIzMSIgd2lkdGg9IjEwMCIgaGVpZ2h0PSIzLjg0NjE1IiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K');
            }

            .flag-icon-it {
                background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMzMuMzMiIGhlaWdodD0iNTAiIGZpbGw9IiMwMDk2NDYiLz4KPHJlY3QgeD0iMzMuMzMiIHdpZHRoPSIzMy4zNCIgaGVpZ2h0PSI1MCIgZmlsbD0id2hpdGUiLz4KPHJlY3QgeD0iNjYuNjciIHdpZHRoPSIzMy4zMyIgaGVpZ2h0PSI1MCIgZmlsbD0iI0NFMkIzNyIvPgo8L3N2Zz4K');
            }

            .flag-icon-fr {
                background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMzMuMzMiIGhlaWdodD0iNTAiIGZpbGw9IiMwMDJBOEYiLz4KPHJlY3QgeD0iMzMuMzMiIHdpZHRoPSIzMy4zNCIgaGVpZ2h0PSI1MCIgZmlsbD0id2hpdGUiLz4KPHJlY3QgeD0iNjYuNjciIHdpZHRoPSIzMy4zMyIgaGVpZ2h0PSI1MCIgZmlsbD0iI0VGMzMzOSIvPgo8L3N2Zz4K');
            }

            .flag-icon-de {
                background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY3IiBmaWxsPSIjMDAwMDAwIi8+CjxyZWN0IHk9IjE2LjY3IiB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY2IiBmaWxsPSIjRkYwMDAwIi8+CjxyZWN0IHk9IjMzLjMzIiB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY3IiBmaWxsPSIjRkZDQzAwIi8+Cjwvc3ZnPgo=');
            }

            .flag-icon-ru {
                background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY3IiBmaWxsPSJ3aGl0ZSIvPgo8cmVjdCB5PSIxNi42NyIgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxNi42NiIgZmlsbD0iIzAwNTJCNCIvPgo8cmVjdCB5PSIzMy4zMyIgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxNi42NyIgZmlsbD0iI0Q1MkIzMSIvPgo8L3N2Zz4K');
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
            <!-- Sidebar -->
            <div class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" 
                 :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
                
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 border-b border-blue-600">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-sm">TT</span>
                        </div>
                        <span class="text-xl font-bold text-white">Elite Forex Pro</span>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="mt-8 px-4 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        {{ __('messages.overview') }}
                    </a>
                    
                    <a href="{{ route('trade.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('trade.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        {{ __('messages.trade') }}
                    </a>
                    
                    <a href="{{ route('wallet.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('wallet.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        {{ __('messages.wallet') }}
                    </a>
                    
                    <a href="{{ route('deposit.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('deposit.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('messages.deposit') }}
                    </a>
                    
                    <a href="{{ route('withdrawal.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('withdrawal.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                        </svg>
                        {{ __('messages.withdrawal') }}
                    </a>
                    
                    <a href="{{ route('history.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('history.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        {{ __('messages.history') }}
                    </a>
                    
                    <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('settings.*') ? 'bg-white/20' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ __('messages.settings') }}
                    </a>
                    
                    @if(Auth::user()->is_admin)
                    <!-- Admin Section Separator -->
                    <div class="my-4 border-t border-blue-600"></div>
                    <div class="px-4 mb-2">
                        <span class="text-xs font-semibold text-blue-200 uppercase tracking-wide">{{ __('messages.administration') }}</span>
                    </div>
                    
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-white rounded-lg hover:bg-red-500/20 transition-colors {{ request()->routeIs('admin.*') ? 'bg-red-500/30 border border-red-400' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="font-medium">{{ __('messages.admin_panel') }}</span>
                        <svg class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    @endif
                </nav>

                <!-- User Info -->
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-600">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-blue-200 truncate">{{ Auth::user()->email }}</p>
                        </div>
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
                                
                                <!-- Desktop Logo (hidden on lg+ since sidebar shows it) -->
                                <div class="hidden lg:flex items-center ml-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-gradient-to-r from-blue-700 to-blue-500 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold text-sm">TT</span>
                                        </div>
                                        <span class="text-xl font-bold text-gray-900">Elite Forex Pro</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Language Switcher -->
                            <div class="flex items-center space-x-4">
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150">
                                        <!-- Current Language Flag -->
                                        <span class="flag-icon flag-icon-{{ app()->getLocale() === 'en' ? 'us' : app()->getLocale() }} mr-2"></span>
                                        
                                        <!-- Current Language Name -->
                                        @switch(app()->getLocale())
                                            @case('en')
                                                EN
                                                @break
                                            @case('it')
                                                IT
                                                @break
                                            @case('fr')
                                                FR
                                                @break
                                            @case('de')
                                                DE
                                                @break
                                            @case('ru')
                                                RU
                                                @break
                                            @default
                                                EN
                                        @endswitch
                                        
                                        <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 z-50 mt-2 w-32 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        
                                        <!-- Language Options -->
                                        <a href="{{ url(request()->getPathInfo() . '?lang=en') }}" 
                                           class="language-option flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'bg-gray-50' : '' }}">
                                            <span class="flag-icon flag-icon-us mr-2"></span>
                                            English
                                        </a>
                                        
                                        <a href="{{ url(request()->getPathInfo() . '?lang=it') }}" 
                                           class="language-option flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'it' ? 'bg-gray-50' : '' }}">
                                            <span class="flag-icon flag-icon-it mr-2"></span>
                                            Italiano
                                        </a>
                                        
                                        <a href="{{ url(request()->getPathInfo() . '?lang=fr') }}" 
                                           class="language-option flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'fr' ? 'bg-gray-50' : '' }}">
                                            <span class="flag-icon flag-icon-fr mr-2"></span>
                                            Français
                                        </a>
                                        
                                        <a href="{{ url(request()->getPathInfo() . '?lang=de') }}" 
                                           class="language-option flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'de' ? 'bg-gray-50' : '' }}">
                                            <span class="flag-icon flag-icon-de mr-2"></span>
                                            Deutsch
                                        </a>
                                        
                                        <a href="{{ url(request()->getPathInfo() . '?lang=ru') }}" 
                                           class="language-option flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'ru' ? 'bg-gray-50' : '' }}">
                                            <span class="flag-icon flag-icon-ru mr-2"></span>
                                            Русский
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white shadow-sm">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    @yield('content')
                </main>
            </div>
        </div>
        
        <!-- Live Chat Widget -->
        <div id="chat-widget" class="fixed bottom-6 right-6 z-50">
            <!-- Chat Button -->
            <button id="chat-toggle" class="bg-red-600 hover:bg-red-700 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-2xl transition-all duration-300 hover:scale-110 relative">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <span id="chat-unread-badge" class="absolute -top-2 -right-2 bg-yellow-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center hidden">0</span>
            </button>
            
            <!-- Chat Window -->
            <div id="chat-window" class="absolute bottom-20 right-0 w-80 h-96 bg-white rounded-2xl shadow-2xl border border-gray-200 hidden overflow-hidden">
                <!-- Chat Header -->
                <div class="bg-red-600 text-white p-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold">Live Support</h3>
                            <p class="text-red-100 text-sm">We're here to help!</p>
                        </div>
                        <button id="chat-close" class="text-red-200 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Chat Messages -->
                <div id="chat-messages-container" class="flex-1 p-4 space-y-3 overflow-y-auto h-64">
                    <div class="text-center py-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-3">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 text-sm">Start a conversation with our support team. We're online and ready to help!</p>
                    </div>
                </div>
                
                <!-- Chat Input -->
                <div class="border-t border-gray-200 p-3">
                    <form id="user-chat-form" class="flex space-x-2">
                        <input type="text" id="user-message-input" placeholder="Type your message..." 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"
                               maxlength="1000">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
        // Chat Widget Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const chatToggle = document.getElementById('chat-toggle');
            const chatWindow = document.getElementById('chat-window');
            const chatClose = document.getElementById('chat-close');
            const chatForm = document.getElementById('user-chat-form');
            const messageInput = document.getElementById('user-message-input');
            const messagesContainer = document.getElementById('chat-messages-container');
            const unreadBadge = document.getElementById('chat-unread-badge');
            
            let chatOpen = false;
            let chatInitialized = false;
            let chatRefreshInterval = null;
            
            // Toggle chat window
            chatToggle.addEventListener('click', function() {
                if (chatOpen) {
                    closeChat();
                } else {
                    openChat();
                }
            });
            
            // Close chat
            chatClose.addEventListener('click', closeChat);
            
            // Open chat
            function openChat() {
                chatWindow.classList.remove('hidden');
                chatOpen = true;
                
                if (!chatInitialized) {
                    loadChatMessages();
                    chatInitialized = true;
                    startChatRefresh();
                }
                
                // Mark messages as read when opening
                markMessagesAsRead();
            }
            
            // Close chat
            function closeChat() {
                chatWindow.classList.add('hidden');
                chatOpen = false;
            }
            
            // Load chat messages
            function loadChatMessages() {
                fetch('/chat/messages')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayChatMessages(data.messages);
                        }
                    })
                    .catch(error => console.error('Error loading chat messages:', error));
            }
            
            // Display chat messages
            function displayChatMessages(messages) {
                if (!messages || messages.length === 0) {
                    messagesContainer.innerHTML = `
                        <div class="text-center py-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-3">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 text-sm">Start a conversation with our support team. We're online and ready to help!</p>
                        </div>
                    `;
                    return;
                }
                
                let messagesHtml = '';
                messages.forEach(message => {
                    const isAdmin = message.sender_type === 'admin';
                    const messageClass = isAdmin ? 'ml-auto bg-gray-100 text-gray-900' : 'mr-auto bg-red-600 text-white';
                    const senderName = isAdmin ? (message.admin ? message.admin.name : 'Support') : 'You';
                    
                    messagesHtml += `
                        <div class="flex ${isAdmin ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-xs">
                                <div class="text-xs text-gray-500 mb-1 ${isAdmin ? 'text-right' : 'text-left'}">
                                    ${senderName} • ${new Date(message.created_at).toLocaleTimeString()}
                                </div>
                                <div class="${messageClass} rounded-lg px-3 py-2 break-words text-sm">
                                    ${message.message}
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                messagesContainer.innerHTML = messagesHtml;
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            
            // Send message
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = messageInput.value.trim();
                if (!message) return;
                
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                
                fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        loadChatMessages();
                    } else {
                        alert('Failed to send message: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    alert('Error sending message');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                });
            });
            
            // Mark messages as read
            function markMessagesAsRead() {
                // This happens automatically when loading messages
                updateUnreadBadge();
            }
            
            // Update unread badge
            function updateUnreadBadge() {
                fetch('/chat/unread-count')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.count > 0) {
                            unreadBadge.textContent = data.count;
                            unreadBadge.classList.remove('hidden');
                        } else {
                            unreadBadge.classList.add('hidden');
                        }
                    })
                    .catch(error => console.error('Error getting unread count:', error));
            }
            
            // Start auto-refresh
            function startChatRefresh() {
                if (chatRefreshInterval) {
                    clearInterval(chatRefreshInterval);
                }
                
                chatRefreshInterval = setInterval(() => {
                    if (chatInitialized) {
                        loadChatMessages();
                    }
                    updateUnreadBadge();
                }, 5000);
            }
            
            // Close chat when clicking outside
            document.addEventListener('click', function(e) {
                if (chatOpen && !document.getElementById('chat-widget').contains(e.target)) {
                    closeChat();
                }
            });
            
            // Initial unread count check
            updateUnreadBadge();
            
            // Start checking for unread messages
            setInterval(updateUnreadBadge, 10000); // Check every 10 seconds
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (chatRefreshInterval) {
                    clearInterval(chatRefreshInterval);
                }
            });
        });
        </script>
        
        <!-- Error Logging Script -->
        <script>
        // Enhanced Error Logging for Laravel Application
        window.AppLogger = {
            errors: [],
            warnings: [],
            info: [],
            
            log: function(level, message, data = null) {
                const timestamp = new Date().toISOString();
                const logEntry = {
                    timestamp: timestamp,
                    level: level,
                    message: message,
                    data: data,
                    url: window.location.href,
                    userAgent: navigator.userAgent
                };
                
                // Store in appropriate array
                this[level + 's'].push(logEntry);
                
                // Console output with styling
                const styles = {
                    error: 'color: #dc3545; font-weight: bold;',
                    warning: 'color: #ffc107; font-weight: bold;',
                    info: 'color: #0dcaf0; font-weight: bold;'
                };
                
                console.group(`%c[${level.toUpperCase()}] ${timestamp}`, styles[level] || '');
                console.log(`%cMessage: ${message}`, 'font-weight: bold;');
                if (data) {
                    console.log('%cData:', 'font-weight: bold;', data);
                }
                console.log(`%cURL: ${window.location.href}`, 'color: #6c757d;');
                console.groupEnd();
                
                // Send to server if error is critical
                if (level === 'error') {
                    this.sendToServer(logEntry);
                }
            },
            
            error: function(message, data = null) {
                this.log('error', message, data);
            },
            
            warning: function(message, data = null) {
                this.log('warning', message, data);
            },
            
            info: function(message, data = null) {
                this.log('info', message, data);
            },
            
            sendToServer: function(logEntry) {
                // Send error to Laravel backend for logging
                fetch('/api/log-error', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(logEntry)
                }).catch(err => {
                    console.error('Failed to send error to server:', err);
                });
            },
            
            getAll: function() {
                return {
                    errors: this.errors,
                    warnings: this.warnings,
                    info: this.info
                };
            },
            
            clear: function() {
                this.errors = [];
                this.warnings = [];
                this.info = [];
                console.clear();
                console.log('%cLogs cleared!', 'color: #28a745; font-weight: bold;');
            }
        };
        
        // Capture JavaScript errors
        window.addEventListener('error', function(e) {
            AppLogger.error('JavaScript Error', {
                message: e.message,
                filename: e.filename,
                lineno: e.lineno,
                colno: e.colno,
                stack: e.error?.stack
            });
        });
        
        // Capture unhandled promise rejections
        window.addEventListener('unhandledrejection', function(e) {
            AppLogger.error('Unhandled Promise Rejection', {
                reason: e.reason,
                promise: e.promise
            });
        });
        
        // Capture fetch errors
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            return originalFetch.apply(this, args)
                .then(response => {
                    if (!response.ok) {
                        AppLogger.warning(`HTTP ${response.status} Error`, {
                            url: args[0],
                            status: response.status,
                            statusText: response.statusText
                        });
                    }
                    return response;
                })
                .catch(error => {
                    AppLogger.error('Fetch Error', {
                        url: args[0],
                        error: error.message
                    });
                    throw error;
                });
        };
        
        // Laravel specific error handling
        document.addEventListener('DOMContentLoaded', function() {
            // Check for Laravel validation errors
            const errorElements = document.querySelectorAll('.alert-danger, .text-danger, .is-invalid');
            if (errorElements.length > 0) {
                const errors = Array.from(errorElements).map(el => el.textContent.trim());
                AppLogger.warning('Laravel Validation Errors Found', errors);
            }
            
            // Check for success messages
            const successElements = document.querySelectorAll('.alert-success, .text-success');
            if (successElements.length > 0) {
                const messages = Array.from(successElements).map(el => el.textContent.trim());
                AppLogger.info('Success Messages Found', messages);
            }
            
            AppLogger.info('Application Loaded', {
                page: window.location.pathname,
                title: document.title,
                timestamp: new Date().toISOString()
            });
        });
        
        // Console commands for debugging
        console.log('%cElite Forex Pro - Debug Console Ready!', 'color: #007bff; font-size: 16px; font-weight: bold;');
        console.log('%cUse AppLogger.getAll() to see all logs', 'color: #6c757d;');
        console.log('%cUse AppLogger.clear() to clear logs', 'color: #6c757d;');
        console.log('%cUse AppLogger.error("message", data) to log errors', 'color: #6c757d;');
        </script>
        
        <!-- Language Selection Modal -->
        @include('components.language-modal')
    </body>
</html>
