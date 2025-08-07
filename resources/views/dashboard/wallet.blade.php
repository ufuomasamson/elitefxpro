<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.my_wallet') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Total Balance Card -->
            <div class="balance-card rounded-xl p-8 text-white mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">{{ __('messages.total_portfolio_value') }}</p>
                        <p class="text-4xl font-bold mb-2">${{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</p>
                        <p class="text-blue-200 text-sm">+$127.43 (+2.5%) {{ __('messages.today') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="flex space-x-2 mb-3">
                            <a href="{{ route('deposit.index') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                {{ __('messages.deposit') }}
                            </a>
                            <a href="{{ route('withdrawal.index') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                {{ __('messages.withdraw') }}
                            </a>
                        </div>
                        <p class="text-blue-200 text-xs">{{ __('messages.last_updated') }}: {{ now()->format('M j, Y g:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cryptocurrency Balances -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.your_assets') }}</h3>
                            <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">{{ __('messages.view_all') }}</button>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Bitcoin -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                        <span class="text-orange-600 font-bold text-lg">₿</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ __('messages.bitcoin') }}</h4>
                                        <p class="text-sm text-gray-600">BTC</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-800">0.00000000 BTC</div>
                                    <div class="text-sm text-gray-600">≈ $0.00</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-green-600">+2.35%</div>
                                    <div class="text-xs text-gray-500">{{ __('messages.24h') }}</div>
                                </div>
                            </div>

                            <!-- Ethereum -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-lg">Ξ</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ __('messages.ethereum') }}</h4>
                                        <p class="text-sm text-gray-600">ETH</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-800">0.00000000 ETH</div>
                                    <div class="text-sm text-gray-600">≈ $0.00</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-green-600">+1.87%</div>
                                    <div class="text-xs text-gray-500">{{ __('messages.24h') }}</div>
                                </div>
                            </div>

                            <!-- Binance Coin -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <span class="text-yellow-600 font-bold text-lg">B</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ __('messages.binance_coin') }}</h4>
                                        <p class="text-sm text-gray-600">BNB</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-800">0.00000000 BNB</div>
                                    <div class="text-sm text-gray-600">≈ $0.00</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-red-600">-0.52%</div>
                                    <div class="text-xs text-gray-500">{{ __('messages.24h') }}</div>
                                </div>
                            </div>

                            <!-- Tether -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <span class="text-green-600 font-bold text-lg">₮</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ __('messages.tether') }}</h4>
                                        <p class="text-sm text-gray-600">USDT</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-800">0.00 USDT</div>
                                    <div class="text-sm text-gray-600">≈ $0.00</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">+0.01%</div>
                                    <div class="text-xs text-gray-500">{{ __('messages.24h') }}</div>
                                </div>
                            </div>

                            <!-- Cardano -->
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-bold text-lg">₳</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ __('messages.cardano') }}</h4>
                                        <p class="text-sm text-gray-600">ADA</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-800">0.00000000 ADA</div>
                                    <div class="text-sm text-gray-600">≈ $0.00</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-green-600">+3.21%</div>
                                    <div class="text-xs text-gray-500">{{ __('messages.24h') }}</div>
                                </div>
                            </div>

                            <!-- Empty State Message -->
                            <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm mb-2">{{ __('messages.wallet_empty') }}</p>
                                <p class="text-gray-400 text-xs mb-4">{{ __('messages.wallet_empty_description') }}</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('deposit.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                        {{ __('messages.deposit_funds') }}
                                    </a>
                                    <a href="{{ route('trade.index') }}" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition-colors">
                                        {{ __('messages.start_trading') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Portfolio Allocation -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.portfolio_allocation') }}</h3>
                        <div class="space-y-4">
                            <div class="text-center py-8">
                                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-sm">{{ __('messages.no_assets_to_display') }}</p>
                                <p class="text-gray-400 text-xs mt-1">{{ __('messages.portfolio_allocation_description') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.recent_activity') }}</h3>
                        <div class="space-y-3">
                            <div class="text-center py-6">
                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">{{ __('messages.no_recent_activity') }}</p>
                                <p class="text-gray-400 text-xs mt-1">{{ __('messages.transactions_appear_here') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.quick_actions') }}</h3>
                        <div class="space-y-3">
                            <a href="{{ route('deposit.index') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-green-100 group-hover:bg-green-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ __('messages.deposit') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('messages.add_funds') }}</p>
                                </div>
                            </a>

                            <a href="{{ route('trade.index') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-blue-100 group-hover:bg-blue-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ __('messages.trade') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('messages.buy_sell_crypto') }}</p>
                                </div>
                            </a>

                            <a href="{{ route('withdrawal.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-purple-100 group-hover:bg-purple-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ __('messages.withdraw') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('messages.transfer_funds') }}</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
