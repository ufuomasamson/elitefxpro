@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-cog text-blue-600 mr-3"></i>
                    Account Settings
                </h2>
                <p class="text-gray-600 mt-2">{{ __('Manage your account preferences and security settings') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    Last login: {{ Auth::user()->last_login_at ? Auth::user()->last_login_at->diffForHumans() : 'Never' }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Success Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6" x-data="{ activeTab: 'profile' }">
                
                <!-- Settings Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6 sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Settings</h3>
                        <nav class="space-y-2">
                            <button @click="activeTab = 'profile'" 
                                    :class="activeTab === 'profile' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                    class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </div>
                            </button>
                            
                            <button @click="activeTab = 'security'" 
                                    :class="activeTab === 'security' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                    class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Security
                                </div>
                            </button>
                            
                            <button @click="activeTab = 'trading'" 
                                    :class="activeTab === 'trading' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                    class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                    Trading
                                </div>
                            </button>
                            
                            <button @click="activeTab = 'notifications'" 
                                    :class="activeTab === 'notifications' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                    class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.543 21C3.12 19.153 3.12 4.847 4.543 3c.72-.918 1.694-1.371 2.913-1.371.607 0 1.234.123 1.856.369M12 11l8-8"></path>
                                    </svg>
                                    Notifications
                                </div>
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-3 space-y-6">
                    
                    <!-- Profile Settings -->
                    <div x-show="activeTab === 'profile'" class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Profile Information</h3>
                        
                        <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            @method('PATCH')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                    <input type="text" id="country" name="country" value="{{ old('country', Auth::user()->country) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                            
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                                <textarea id="bio" name="bio" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('bio', Auth::user()->bio) }}</textarea>
                            </div>
                            
                            <div>
                                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                                <input type="file" id="avatar" name="avatar" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Settings -->
                    <div x-show="activeTab === 'security'" class="space-y-6">
                        <!-- Password Change -->
                        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Change Password</h3>
                            
                            <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-6">
                                @csrf
                                @method('PATCH')
                                
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" id="current_password" name="current_password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('current_password')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                        <input type="password" id="password" name="password"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('password')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                        Update Password
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Two-Factor Authentication</h3>
                            
                            <div class="flex items-center justify-between p-4 border rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800">2FA Status</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ Auth::user()->two_factor_enabled ? 'Enabled - Your account is protected' : 'Disabled - Enable for better security' }}
                                    </p>
                                </div>

                                @if(Auth::user()->two_factor_enabled)
                                    <form method="POST" action="{{ route('settings.2fa.disable') }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <input type="password" name="password" placeholder="Enter password" class="mr-2 px-3 py-1 border border-gray-300 rounded text-sm" required>
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 transition-colors">
                                            Disable
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('settings.2fa.enable') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 transition-colors">
                                            Enable
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Trading Preferences -->
                    <div x-show="activeTab === 'trading'" class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Trading Preferences</h3>

                        @php
                            $tradingSettings = Auth::user()->trading_settings ? json_decode(Auth::user()->trading_settings, true) : [];
                        @endphp

                        <form method="POST" action="{{ route('settings.trading.update') }}" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="default_trading_pair" class="block text-sm font-medium text-gray-700 mb-2">Default Trading Pair</label>
                                    <select id="default_trading_pair" name="default_trading_pair"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="BTC/USD" {{ ($tradingSettings['default_trading_pair'] ?? '') === 'BTC/USD' ? 'selected' : '' }}>BTC/USD</option>
                                        <option value="ETH/USD" {{ ($tradingSettings['default_trading_pair'] ?? '') === 'ETH/USD' ? 'selected' : '' }}>ETH/USD</option>
                                        <option value="BNB/USD" {{ ($tradingSettings['default_trading_pair'] ?? '') === 'BNB/USD' ? 'selected' : '' }}>BNB/USD</option>
                                        <option value="ADA/USD" {{ ($tradingSettings['default_trading_pair'] ?? '') === 'ADA/USD' ? 'selected' : '' }}>ADA/USD</option>
                                        <option value="SOL/USD" {{ ($tradingSettings['default_trading_pair'] ?? '') === 'SOL/USD' ? 'selected' : '' }}>SOL/USD</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="default_order_amount" class="block text-sm font-medium text-gray-700 mb-2">Default Order Amount ($)</label>
                                    <input type="number" id="default_order_amount" name="default_order_amount" 
                                           value="{{ old('default_order_amount', $tradingSettings['default_order_amount'] ?? 100) }}"
                                           min="0.01" step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div>
                                <label for="risk_tolerance" class="block text-sm font-medium text-gray-700 mb-2">Risk Tolerance</label>
                                <select id="risk_tolerance" name="risk_tolerance"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="low" {{ ($tradingSettings['risk_tolerance'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ ($tradingSettings['risk_tolerance'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ ($tradingSettings['risk_tolerance'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="auto_stop_loss" name="auto_stop_loss" value="1"
                                               {{ ($tradingSettings['auto_stop_loss'] ?? false) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="auto_stop_loss" class="ml-2 block text-sm text-gray-700">Enable Auto Stop Loss</label>
                                    </div>
                                    
                                    <div>
                                        <label for="stop_loss_percentage" class="block text-sm font-medium text-gray-700 mb-2">Stop Loss Percentage (%)</label>
                                        <input type="number" id="stop_loss_percentage" name="stop_loss_percentage" 
                                               value="{{ old('stop_loss_percentage', $tradingSettings['stop_loss_percentage'] ?? 5) }}"
                                               min="1" max="50" step="0.1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="auto_take_profit" name="auto_take_profit" value="1"
                                               {{ ($tradingSettings['auto_take_profit'] ?? false) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="auto_take_profit" class="ml-2 block text-sm text-gray-700">Enable Auto Take Profit</label>
                                    </div>
                                    
                                    <div>
                                        <label for="take_profit_percentage" class="block text-sm font-medium text-gray-700 mb-2">Take Profit Percentage (%)</label>
                                        <input type="number" id="take_profit_percentage" name="take_profit_percentage" 
                                               value="{{ old('take_profit_percentage', $tradingSettings['take_profit_percentage'] ?? 10) }}"
                                               min="1" max="100" step="0.1"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    Update Trading Preferences
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Notifications -->
                    <div x-show="activeTab === 'notifications'" class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Notification Preferences</h3>

                        @php
                            $notificationSettings = Auth::user()->notification_settings ? json_decode(Auth::user()->notification_settings, true) : [];
                        @endphp

                        <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-6">
                            @csrf
                            @method('PATCH')

                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 border rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">Email Notifications</p>
                                        <p class="text-sm text-gray-600">Receive general email notifications</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="email_notifications" value="1" class="sr-only peer"
                                               {{ ($notificationSettings['email_notifications'] ?? false) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 border rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">Trade Notifications</p>
                                        <p class="text-sm text-gray-600">Get notified when trades are executed</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="trade_notifications" value="1" class="sr-only peer"
                                               {{ ($notificationSettings['trade_notifications'] ?? false) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 border rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">Security Notifications</p>
                                        <p class="text-sm text-gray-600">Login attempts and security alerts</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="security_notifications" value="1" class="sr-only peer"
                                               {{ ($notificationSettings['security_notifications'] ?? false) ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    Update Notifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
