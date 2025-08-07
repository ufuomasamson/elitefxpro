@extends('layouts.admin')

@section('title', 'Admin Settings')

@section('content')
<!-- Success/Error Messages -->
@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm-1 15l-5-5 1.41-1.41L9 12.17l7.59-7.59L18 6l-9 9z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Success!</p>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm5 13.59L13.59 15 10 11.41 6.41 15 5 13.59 8.59 10 5 6.41 6.41 5 10 8.59 13.59 5 15 6.41 11.41 10 15 13.59z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
        <p class="text-gray-600 mt-1">Configure and manage platform settings</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="backupSettings()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
            <span>Backup Settings</span>
        </button>
        <button onclick="clearSystemCache()" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Clear Cache</span>
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Settings Navigation -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-6">
            <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white">
                <h3 class="text-lg font-semibold">Settings Categories</h3>
            </div>
            <nav class="p-4 space-y-2">
                <button onclick="showTab('general')" class="settings-tab w-full text-left active bg-red-50 text-red-700 border-red-200 border group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all">
                    <svg class="text-red-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    General Settings
                </button>
                
                <button onclick="showTab('trading')" class="settings-tab w-full text-left text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all">
                    <svg class="text-gray-400 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Trading Settings
                </button>
                
                <button onclick="showTab('fees')" class="settings-tab w-full text-left text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all">
                    <svg class="text-gray-400 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    Fees & Limits
                </button>
                
                <button onclick="showTab('security')" class="settings-tab w-full text-left text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all">
                    <svg class="text-gray-400 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Security Settings
                </button>
                
                <button onclick="showTab('notifications')" class="settings-tab w-full text-left text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all">
                    <svg class="text-gray-400 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM7 7h11a3 3 0 013 3v6a3 3 0 01-3 3H7a3 3 0 01-3-3V10a3 3 0 013-3z" />
                    </svg>
                    Notifications
                </button>
                
                <button onclick="showTab('maintenance')" class="settings-tab w-full text-left text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-all">
                    <svg class="text-gray-400 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Maintenance
                </button>
            </nav>
        </div>
    </div>

    <!-- Settings Content -->
    <div class="lg:col-span-3">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- General Settings -->
            <div id="general-tab" class="settings-content bg-white rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-red-600 to-pink-600 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">General Settings</h3>
                        <p class="text-gray-600">Basic platform configuration and branding</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Platform Name</label>
                        <input type="text" name="site_name" id="site_name" value="{{ config('app.name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <p class="text-sm text-gray-500 mt-1">The name that appears across the platform</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">Platform Description</label>
                        <textarea name="site_description" id="site_description" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                  placeholder="Enter platform description...">{{ config('app.description', 'Your trusted cryptocurrency trading platform') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">Brief description of your trading platform</p>
                    </div>
                    
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ config('mail.support_email', 'support@eliteforexpro.com') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <p class="text-sm text-gray-500 mt-1">Support and contact email address</p>
                    </div>
                    
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Default Timezone</label>
                        <select name="timezone" id="timezone"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            <option value="UTC" {{ config('app.timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                            <option value="America/New_York" {{ config('app.timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            <option value="America/Chicago" {{ config('app.timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                            <option value="America/Denver" {{ config('app.timezone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                            <option value="America/Los_Angeles" {{ config('app.timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                            <option value="Europe/London" {{ config('app.timezone') == 'Europe/London' ? 'selected' : '' }}>London</option>
                            <option value="Europe/Paris" {{ config('app.timezone') == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                            <option value="Asia/Tokyo" {{ config('app.timezone') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="platform_logo" class="block text-sm font-medium text-gray-700 mb-2">Platform Logo</label>
                        <input type="file" name="platform_logo" id="platform_logo" accept="image/*"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                        <p class="text-sm text-gray-500 mt-1">Upload a new platform logo (PNG, JPG, max 2MB)</p>
                    </div>
                    
                    <div>
                        <label for="currency_display" class="block text-sm font-medium text-gray-700 mb-2">Default Currency</label>
                        <select name="currency_display" id="currency_display"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}" {{ $currentCurrency === $code ? 'selected' : '' }}>
                                    {{ $code }} ({{ $currency['symbol'] }}) - {{ $currency['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-sm text-gray-500 mt-1">Current: {{ $currentCurrency }} ({{ $currentSymbol }})</p>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <label for="maintenance_mode" class="text-lg font-medium text-gray-900">Maintenance Mode</label>
                            <p class="text-sm text-gray-600">Temporarily disable platform access for maintenance</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" id="maintenance_mode" class="sr-only peer" {{ config('app.maintenance_mode') ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Trading Settings -->
            <div id="trading-tab" class="settings-content bg-white rounded-xl shadow-lg p-8 hidden">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Trading Settings</h3>
                        <p class="text-gray-600">Configure trading limits and parameters</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="min_trade_amount" class="block text-sm font-medium text-gray-700 mb-2">Minimum Trade Amount ($)</label>
                        <input type="number" name="min_trade_amount" id="min_trade_amount" value="10.00" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="max_trade_amount" class="block text-sm font-medium text-gray-700 mb-2">Maximum Trade Amount ($)</label>
                        <input type="number" name="max_trade_amount" id="max_trade_amount" value="100000.00" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="default_leverage" class="block text-sm font-medium text-gray-700 mb-2">Default Leverage</label>
                        <select name="default_leverage" id="default_leverage"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="1">1:1 (No Leverage)</option>
                            <option value="2">1:2</option>
                            <option value="5">1:5</option>
                            <option value="10">1:10</option>
                            <option value="20">1:20</option>
                            <option value="50">1:50</option>
                            <option value="100">1:100</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="max_leverage" class="block text-sm font-medium text-gray-700 mb-2">Maximum Leverage</label>
                        <select name="max_leverage" id="max_leverage"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="10">1:10</option>
                            <option value="20">1:20</option>
                            <option value="50">1:50</option>
                            <option value="100">1:100</option>
                            <option value="200">1:200</option>
                            <option value="500">1:500</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="trading_enabled" class="text-lg font-medium text-gray-900">Enable Trading</label>
                                <p class="text-sm text-gray-600">Allow users to execute trades</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="trading_enabled" id="trading_enabled" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fees Settings -->
            <div id="fees-tab" class="settings-content bg-white rounded-xl shadow-lg p-8 hidden">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Fees & Limits</h3>
                        <p class="text-gray-600">Set platform fees and transaction limits</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="trading_fee" class="block text-sm font-medium text-gray-700 mb-2">Trading Fee (%)</label>
                        <input type="number" name="trading_fee" id="trading_fee" value="0.25" step="0.01" min="0" max="10"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="withdrawal_fee" class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Fee (%)</label>
                        <input type="number" name="withdrawal_fee" id="withdrawal_fee" value="0.1" step="0.01" min="0" max="5"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="min_withdrawal" class="block text-sm font-medium text-gray-700 mb-2">Minimum Withdrawal ($)</label>
                        <input type="number" name="min_withdrawal" id="min_withdrawal" value="50.00" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="max_withdrawal_daily" class="block text-sm font-medium text-gray-700 mb-2">Daily Withdrawal Limit ($)</label>
                        <input type="number" name="max_withdrawal_daily" id="max_withdrawal_daily" value="10000.00" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="min_deposit" class="block text-sm font-medium text-gray-700 mb-2">Minimum Deposit ($)</label>
                        <input type="number" name="min_deposit" id="min_deposit" value="10.00" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                    
                    <div>
                        <label for="max_deposit_daily" class="block text-sm font-medium text-gray-700 mb-2">Daily Deposit Limit ($)</label>
                        <input type="number" name="max_deposit_daily" id="max_deposit_daily" value="50000.00" step="0.01" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="security-tab" class="settings-content bg-white rounded-xl shadow-lg p-8 hidden">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Security Settings</h3>
                        <p class="text-gray-600">Configure platform security measures</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="session_timeout" class="block text-sm font-medium text-gray-700 mb-2">Session Timeout (minutes)</label>
                            <input type="number" name="session_timeout" id="session_timeout" value="30" min="5" max="480"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                        </div>
                        
                        <div>
                            <label for="max_login_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Login Attempts</label>
                            <input type="number" name="max_login_attempts" id="max_login_attempts" value="5" min="3" max="10"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="require_2fa" class="text-lg font-medium text-gray-900">Require 2FA</label>
                                <p class="text-sm text-gray-600">Force all users to enable 2FA</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="require_2fa" id="require_2fa" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="email_verification_required" class="text-lg font-medium text-gray-900">Email Verification</label>
                                <p class="text-sm text-gray-600">Require email verification for new accounts</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_verification_required" id="email_verification_required" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Settings -->
            <div id="notifications-tab" class="settings-content bg-white rounded-xl shadow-lg p-8 hidden">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-orange-600 to-red-600 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM7 7h11a3 3 0 013 3v6a3 3 0 01-3 3H7a3 3 0 01-3-3V10a3 3 0 013-3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Notification Settings</h3>
                        <p class="text-gray-600">Configure system notifications and alerts</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="email_notifications" class="text-lg font-medium text-gray-900">Email Notifications</label>
                                <p class="text-sm text-gray-600">Send notifications via email</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="email_notifications" id="email_notifications" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="sms_notifications" class="text-lg font-medium text-gray-900">SMS Notifications</label>
                                <p class="text-sm text-gray-600">Send notifications via SMS</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="sms_notifications" id="sms_notifications" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Settings -->
            <div id="maintenance-tab" class="settings-content bg-white rounded-xl shadow-lg p-8 hidden">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-gray-600 to-gray-800 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Maintenance & System</h3>
                        <p class="text-gray-600">System maintenance and administrative tools</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label for="maintenance_message" class="block text-sm font-medium text-gray-700 mb-2">Maintenance Message</label>
                        <textarea name="maintenance_message" id="maintenance_message" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-colors"
                                  placeholder="Enter maintenance message for users...">We are currently performing scheduled maintenance. Please check back shortly.</textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="debug_mode" class="text-lg font-medium text-gray-900">Debug Mode</label>
                                <p class="text-sm text-gray-600">Enable debug logging and error display</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="debug_mode" id="debug_mode" class="sr-only peer" {{ config('app.debug') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-600"></div>
                            </label>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="log_queries" class="text-lg font-medium text-gray-900">Log Database Queries</label>
                                <p class="text-sm text-gray-600">Log all database queries for debugging</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="log_queries" id="log_queries" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-600"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white font-bold py-3 px-8 rounded-lg flex items-center space-x-2 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Save Settings</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.settings-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all nav items
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-red-50', 'text-red-700', 'border-red-200', 'border');
        tab.classList.add('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to clicked nav item
    event.target.closest('.settings-tab').classList.add('active', 'bg-red-50', 'text-red-700', 'border-red-200', 'border');
    event.target.closest('.settings-tab').classList.remove('text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900');
}

function backupSettings() {
    if(confirm('Create a backup of current settings?')) {
        fetch('/admin/settings/backup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Settings backup created successfully!');
            } else {
                alert('Failed to create backup: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error creating backup: ' + error.message);
        });
    }
}

function clearSystemCache() {
    if(confirm('Clear all system cache? This will improve performance but may temporarily slow down the first few requests.')) {
        fetch('/admin/settings/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('System cache cleared successfully!');
            } else {
                alert('Failed to clear cache: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error clearing cache: ' + error.message);
        });
    }
}
</script>
@endsection
