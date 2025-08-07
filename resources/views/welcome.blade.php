<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Elite Forex Pro') }} - {{ __('messages.hero_title') }}</title>

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
        
        <!-- Custom Styles - Updated Cache Buster 2025-07-31-v2 -->
        <style>
            body { font-family: 'Inter', sans-serif; }
            
            /* Hero Section - Blue Gradient */
            .hero-gradient-bg {
                background: linear-gradient(135deg, #1c42c2 0%, #4169e1 100%) !important;
            }
            
            /* CTA Section - Purple Gradient */
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .hero-pattern {
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            
            .crypto-glow {
                box-shadow: 0 0 20px rgba(28, 66, 194, 0.4);
            }
            
            .stats-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
        </style>
    </head>
    <body class="antialiased">
        <!-- Navigation -->
        <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-gray-200" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-700 to-blue-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">TT</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">Elite Forex Pro</span>
                        </a>
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#home" class="text-gray-600 hover:text-blue-600 transition-colors">{{ __('messages.home') }}</a>
                        <a href="#features" class="text-gray-600 hover:text-blue-600 transition-colors">{{ __('messages.features') }}</a>
                        <a href="#services" class="text-gray-600 hover:text-blue-600 transition-colors">{{ __('messages.services') }}</a>
                        <a href="#about" class="text-gray-600 hover:text-blue-600 transition-colors">{{ __('messages.about') }}</a>
                        <a href="#contact" class="text-gray-600 hover:text-blue-600 transition-colors">{{ __('messages.contact') }}</a>
                    </div>

                    <!-- Auth Links -->
                    <div class="hidden md:flex items-center space-x-4">
                        <!-- Language Switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 hover:text-blue-600 focus:outline-none transition ease-in-out duration-150">
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
                                <a href="{{ url('/?lang=en') }}" 
                                   class="language-option flex items-center px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'en' ? 'bg-gray-50' : '' }}">
                                    <span class="flag-icon flag-icon-us mr-2"></span>
                                    English
                                </a>
                                
                                <a href="{{ url('/?lang=it') }}" 
                                   class="language-option flex items-center px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'it' ? 'bg-gray-50' : '' }}">
                                    <span class="flag-icon flag-icon-it mr-2"></span>
                                    Italiano
                                </a>
                                
                                <a href="{{ url('/?lang=fr') }}" 
                                   class="language-option flex items-center px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'fr' ? 'bg-gray-50' : '' }}">
                                    <span class="flag-icon flag-icon-fr mr-2"></span>
                                    Français
                                </a>
                                
                                <a href="{{ url('/?lang=de') }}" 
                                   class="language-option flex items-center px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'de' ? 'bg-gray-50' : '' }}">
                                    <span class="flag-icon flag-icon-de mr-2"></span>
                                    Deutsch
                                </a>
                                
                                <a href="{{ url('/?lang=ru') }}" 
                                   class="language-option flex items-center px-3 py-1 text-sm text-gray-700 hover:bg-gray-100 {{ app()->getLocale() === 'ru' ? 'bg-gray-50' : '' }}">
                                    <span class="flag-icon flag-icon-ru mr-2"></span>
                                    Русский
                                </a>
                            </div>
                        </div>
                        
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-blue-600 transition-colors">{{ __('messages.dashboard') }}</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition-colors">{{ __('messages.login') }}</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">{{ __('messages.get_started') }}</a>
                                @endif
                            @endauth
                        @endif
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button @click="open = !open" class="text-gray-600 hover:text-gray-900">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="open" class="md:hidden bg-white border-t border-gray-200">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="#home" class="block px-3 py-2 text-gray-600 hover:text-blue-600">{{ __('messages.home') }}</a>
                    <a href="#features" class="block px-3 py-2 text-gray-600 hover:text-blue-600">{{ __('messages.features') }}</a>
                    <a href="#services" class="block px-3 py-2 text-gray-600 hover:text-blue-600">{{ __('messages.services') }}</a>
                    <a href="#about" class="block px-3 py-2 text-gray-600 hover:text-blue-600">{{ __('messages.about') }}</a>
                    <a href="#contact" class="block px-3 py-2 text-gray-600 hover:text-blue-600">{{ __('messages.contact') }}</a>
                    
                    <!-- Language Switcher for Mobile -->
                    <div class="border-t border-gray-200 pt-2">
                        <div class="px-3 py-2 text-xs text-gray-500 uppercase tracking-wider">{{ __('messages.language') }}</div>
                        <a href="{{ url('/?lang=en') }}" 
                           class="mobile-language-option flex items-center px-3 py-2 text-gray-600 hover:text-blue-600 {{ app()->getLocale() === 'en' ? 'text-blue-600 font-medium' : '' }}">
                            <span class="flag-icon flag-icon-us mr-2"></span>
                            English
                        </a>
                        <a href="{{ url('/?lang=it') }}" 
                           class="mobile-language-option flex items-center px-3 py-2 text-gray-600 hover:text-blue-600 {{ app()->getLocale() === 'it' ? 'text-blue-600 font-medium' : '' }}">
                            <span class="flag-icon flag-icon-it mr-2"></span>
                            Italiano
                        </a>
                        <a href="{{ url('/?lang=fr') }}" 
                           class="mobile-language-option flex items-center px-3 py-2 text-gray-600 hover:text-blue-600 {{ app()->getLocale() === 'fr' ? 'text-blue-600 font-medium' : '' }}">
                            <span class="flag-icon flag-icon-fr mr-2"></span>
                            Français
                        </a>
                        <a href="{{ url('/?lang=de') }}" 
                           class="mobile-language-option flex items-center px-3 py-2 text-gray-600 hover:text-blue-600 {{ app()->getLocale() === 'de' ? 'text-blue-600 font-medium' : '' }}">
                            <span class="flag-icon flag-icon-de mr-2"></span>
                            Deutsch
                        </a>
                        <a href="{{ url('/?lang=ru') }}" 
                           class="mobile-language-option flex items-center px-3 py-2 text-gray-600 hover:text-blue-600 {{ app()->getLocale() === 'ru' ? 'text-blue-600 font-medium' : '' }}">
                            <span class="flag-icon flag-icon-ru mr-2"></span>
                            Русский
                        </a>
                    </div>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="block px-3 py-2 text-gray-600 hover:text-blue-600">{{ __('messages.dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-600 hover:text-blue-600">{{ __('messages.login') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block px-3 py-2 bg-blue-600 text-white rounded-lg ml-3 mr-3">{{ __('messages.get_started') }}</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="pt-16 hero-gradient-bg hero-pattern min-h-screen flex items-center" style="background: linear-gradient(135deg, #1c42c2 0%, #4169e1 100%) !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-white">
                        <h1 class="text-5xl lg:text-6xl font-bold leading-tight mb-6 text-white">
                            {{ __('messages.hero_title') }}
                        </h1>
                        <p class="text-xl text-gray-100 mb-8 leading-relaxed">
                            {{ __('messages.hero_subtitle') }}
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 mb-12">
                            <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors crypto-glow">
                                {{ __('messages.get_started') }}
                            </a>
                            <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                                {{ __('messages.learn_more') }}
                            </a>
                        </div>

                        <!-- Statistics -->
                        <div class="grid grid-cols-3 gap-8">
                            <div class="stats-card rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">1M+</div>
                                <div class="text-sm text-gray-200">{{ __('messages.active_users') }}</div>
                            </div>
                            <div class="stats-card rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">$2B+</div>
                                <div class="text-sm text-gray-200">{{ __('messages.trading_volume') }}</div>
                            </div>
                            <div class="stats-card rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">99.9%</div>
                                <div class="text-sm text-gray-200">{{ __('messages.uptime') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <!-- Live Crypto Prices Widget -->
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20">
                            <h3 class="text-white font-semibold mb-4">{{ __('messages.live_crypto_prices') }}</h3>
                            <div id="live-prices" class="space-y-3">
                                <!-- Dynamic content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TradingView Widget Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ __('messages.real_time_market_data') }}</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">
                        {{ __('messages.stay_ahead_market') }}
                    </p>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <!-- TradingView Widget -->
                    <div class="tradingview-widget-container">
                        <div id="tradingview_widget"></div>
                        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                        <script type="text/javascript">
                            new TradingView.widget({
                                "width": "100%",
                                "height": 500,
                                "symbol": "BINANCE:BTCUSDT",
                                "interval": "D",
                                "timezone": "Etc/UTC",
                                "theme": "light",
                                "style": "1",
                                "locale": "en",
                                "toolbar_bg": "#f1f3f6",
                                "enable_publishing": false,
                                "withdateranges": true,
                                "hide_side_toolbar": false,
                                "allow_symbol_change": true,
                                "container_id": "tradingview_widget"
                            });
                        </script>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">{{ __('messages.why_choose_us') }}</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        {{ __('messages.next_generation_trading') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center group">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('messages.secure_trading') }}</h3>
                        <p class="text-gray-600">{{ __('messages.bank_level_security') }}</p>
                    </div>

                    <div class="text-center group">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-green-200 transition-colors">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('messages.admin_approved_funding') }}</h3>
                        <p class="text-gray-600">{{ __('messages.manual_verification') }}</p>
                    </div>

                    <div class="text-center group">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-200 transition-colors">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('messages.real_time_charts') }}</h3>
                        <p class="text-gray-600">{{ __('messages.professional_tradingview') }}</p>
                    </div>

                    <div class="text-center group">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-200 transition-colors">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ __('messages.multi_language') }}</h3>
                        <p class="text-gray-600">{{ __('messages.available_languages') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">{{ __('messages.services') }}</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Comprehensive trading solutions designed for both beginners and professional traders
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Spot Trading</h3>
                        <p class="text-gray-600 mb-6">Trade over 100+ cryptocurrency pairs with competitive fees and deep liquidity</p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Real-time order execution
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Advanced order types
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Low trading fees
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Secure Wallet</h3>
                        <p class="text-gray-600 mb-6">Multi-currency wallet with industry-leading security and instant transfers</p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Cold storage security
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Multi-signature protection
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Instant deposits/withdrawals
                            </li>
                        </ul>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">24/7 Support</h3>
                        <p class="text-gray-600 mb-6">Round-the-clock customer support with dedicated account managers</p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Live chat support
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Multi-language support
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Educational resources
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6">About Elite Forex Pro</h2>
                        <p class="text-lg text-gray-600 mb-6">
                            {{ __('messages.founded_description') }}
                        </p>
                        <p class="text-gray-600 mb-8">
                            {{ __('messages.platform_combines') }}
                        </p>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">1M+</div>
                                <div class="text-gray-600">Registered Users</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">100+</div>
                                <div class="text-gray-600">Trading Pairs</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">$2B+</div>
                                <div class="text-gray-600">Daily Volume</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 mb-2">5</div>
                                <div class="text-gray-600">Languages</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
                            <h3 class="text-2xl font-bold mb-4">Our Mission</h3>
                            <p class="text-blue-100 mb-6">
                                To democratize access to cryptocurrency trading by providing a secure, user-friendly, 
                                and professionally managed platform that empowers individuals to participate in the 
                                digital economy with confidence.
                            </p>
                            
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Regulatory Compliance</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>Advanced Security</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>User-Centric Design</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 gradient-bg">
            <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
                <h2 class="text-4xl font-bold text-white mb-6">
                    Ready to Start Trading?
                </h2>
                <p class="text-xl text-gray-200 mb-8">
                    Join millions of traders who trust Elite Forex Pro for their cryptocurrency trading needs. 
                    Sign up today and start your trading journey with industry-leading security and support.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors crypto-glow">
                        {{ __('messages.register') }}
                    </a>
                    <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                        {{ __('messages.login') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-700 to-blue-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">TT</span>
                            </div>
                            <span class="text-xl font-bold">Elite Forex Pro</span>
                        </div>
                        <p class="text-gray-400 mb-6 max-w-md">
                            {{ __('messages.worlds_most_secure') }}
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Platform</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">Trading</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Wallet</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">API</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Support</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                            <li><a href="#contact" class="hover:text-white transition-colors">Contact Us</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                    <p>{!! __('messages.copyright_notice') !!} {{ __('messages.all_rights_reserved') }}</p>
                </div>
            </div>
        </footer>

        <!-- Live Prices Script -->
        <script>
            // Load live crypto prices
            document.addEventListener('DOMContentLoaded', function() {
                loadLivePrices();
                setInterval(loadLivePrices, 30000); // Update every 30 seconds
            });

            function loadLivePrices() {
                fetch('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,binancecoin,cardano,solana&vs_currencies=usd&include_24hr_change=true')
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('live-prices');
                        container.innerHTML = '';
                        
                        const cryptos = [
                            { id: 'bitcoin', name: 'Bitcoin', symbol: 'BTC' },
                            { id: 'ethereum', name: 'Ethereum', symbol: 'ETH' },
                            { id: 'binancecoin', name: 'BNB', symbol: 'BNB' },
                            { id: 'cardano', name: 'Cardano', symbol: 'ADA' },
                            { id: 'solana', name: 'Solana', symbol: 'SOL' }
                        ];
                        
                        cryptos.forEach(crypto => {
                            if (data[crypto.id]) {
                                const price = data[crypto.id].usd;
                                const change = data[crypto.id].usd_24h_change;
                                const isPositive = change >= 0;
                                
                                const item = document.createElement('div');
                                item.className = 'flex justify-between items-center py-2';
                                item.innerHTML = `
                                    <div>
                                        <div class="font-medium text-white">${crypto.symbol}</div>
                                        <div class="text-xs text-gray-300">${crypto.name}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-medium text-white">$${price.toLocaleString()}</div>
                                        <div class="text-xs ${isPositive ? 'text-green-400' : 'text-red-400'}">
                                            ${isPositive ? '+' : ''}${change.toFixed(2)}%
                                        </div>
                                    </div>
                                `;
                                container.appendChild(item);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error loading prices:', error);
                    });
            }
        </script>
        
        <!-- Language Selection Modal -->
        @include('components.language-modal')
        
        <!-- Flag Icons CSS and Language Switcher JavaScript -->
        <style>
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
        
        <script>
            // Mobile menu functionality and other scripts can go here
        </script>
        
        <!-- Language Selection Modal -->
        @include('components.language-modal')
    </body>
</html>
