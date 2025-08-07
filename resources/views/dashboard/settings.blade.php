<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.settings') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Settings Navigation -->
                <div class="bg-white rounded-xl shadow-lg p-6" x-data="{ activeSection: 'profile' }">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Settings</h3>
                    <nav class="space-y-2">
                        <button @click="activeSection = 'profile'" 
                                :class="activeSection === 'profile' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Information
                            </div>
                        </button>
                        
                        <button @click="activeSection = 'security'" 
                                :class="activeSection === 'security' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Security
                            </div>
                        </button>
                        
                        <button @click="activeSection = 'trading'" 
                                :class="activeSection === 'trading' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                Trading Preferences
                            </div>
                        </button>
                        
                        <button @click="activeSection = 'notifications'" 
                                :class="activeSection === 'notifications' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.343 2.343l1.414 1.414m9.899 9.899l1.414 1.414m-3.536 3.536l1.414 1.414M6.343 6.343l1.414 1.414m7.071 7.071l1.414 1.414m-9.899 0l1.414-1.414M20 12h-2M6 12H4m3-6V4m6 16v-2"></path>
                                </svg>
                                Notifications
                            </div>
                        </button>
                        
                        <button @click="activeSection = 'kyc'" 
                                :class="activeSection === 'kyc' ? 'bg-blue-50 text-blue-600 border-blue-200' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 rounded-lg border transition-colors">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                KYC Verification
                            </div>
                        </button>
                    </nav>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Profile Information Section -->
                    <div x-show="activeSection === 'profile'" class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Profile Information</h3>
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <!-- Security Section -->
                    <div x-show="activeSection === 'security'" class="space-y-6">
                        <!-- Change Password -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Change Password</h3>
                            @include('profile.partials.update-password-form')
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Two-Factor Authentication</h3>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-800">2FA Status</h4>
                                    <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                                </div>
                                <button class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors">
                                    Enable 2FA
                                </button>
                            </div>
                        </div>

                        <!-- Login Sessions -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Active Sessions</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 border rounded-lg">
                                    <div class="flex items-center">
                                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">Current Session</p>
                                            <p class="text-sm text-gray-600">Windows • Chrome • 192.168.1.1</p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Account -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-lg font-semibold text-red-600 mb-6">Delete Account</h3>
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                    <!-- Trading Preferences Section -->
                    <div x-show="activeSection === 'trading'" class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Trading Preferences</h3>
                        <form class="space-y-6">
                            <!-- Default Trading Pair -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Default Trading Pair</label>
                                <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option>BTC/USD</option>
                                    <option>ETH/USD</option>
                                    <option>USDT/USD</option>
                                    <option>BNB/USD</option>
                                </select>
                            </div>

                            <!-- Trading Limits -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Daily Trading Limit</label>
                                    <input type="number" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="10000">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Single Trade Limit</label>
                                    <input type="number" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="1000">
                                </div>
                            </div>

                            <!-- Trading Confirmations -->
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700">Require confirmation for trades above $500</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700">Show advanced trading options</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label class="ml-2 text-sm text-gray-700">Enable stop-loss orders</label>
                                </div>
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                                Save Trading Preferences
                            </button>
                        </form>
                    </div>

                    <!-- Notifications Section -->
                    <div x-show="activeSection === 'notifications'" class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Notification Preferences</h3>
                        <form class="space-y-6">
                            <!-- Email Notifications -->
                            <div>
                                <h4 class="font-medium text-gray-800 mb-3">Email Notifications</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm text-gray-700">Trade confirmations</label>
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm text-gray-700">Deposit/Withdrawal updates</label>
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm text-gray-700">Price alerts</label>
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm text-gray-700">Weekly portfolio summary</label>
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- SMS Notifications -->
                            <div>
                                <h4 class="font-medium text-gray-800 mb-3">SMS Notifications</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm text-gray-700">Security alerts</label>
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm text-gray-700">Large transactions</label>
                                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                                Save Notification Preferences
                            </button>
                        </form>
                    </div>

                    <!-- KYC Verification Section -->
                    <div x-show="activeSection === 'kyc'" class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">KYC Verification</h3>
                        
                        <!-- Verification Status -->
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="font-medium text-yellow-800">Verification Pending</span>
                            </div>
                            <p class="text-sm text-yellow-700 mt-1">Complete KYC verification to unlock full trading features</p>
                        </div>

                        <!-- Verification Steps -->
                        <div class="space-y-4">
                            <div class="flex items-center p-4 border rounded-lg">
                                <div class="p-2 bg-green-100 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">Personal Information</h4>
                                    <p class="text-sm text-gray-600">Name, date of birth, address</p>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Complete</span>
                            </div>

                            <div class="flex items-center p-4 border rounded-lg">
                                <div class="p-2 bg-yellow-100 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">Identity Verification</h4>
                                    <p class="text-sm text-gray-600">Government-issued ID document</p>
                                </div>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Pending</span>
                            </div>

                            <div class="flex items-center p-4 border rounded-lg opacity-50">
                                <div class="p-2 bg-gray-100 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-800">Final Approval</h4>
                                    <p class="text-sm text-gray-600">Administrative review and approval</p>
                                </div>
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">Waiting</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                                Continue Verification
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
