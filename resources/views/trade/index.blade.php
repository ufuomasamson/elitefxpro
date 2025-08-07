@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Trade Cryptocurrencies') }}</h1>
            <p class="mt-2 text-gray-600">{{ __('Buy and sell cryptocurrencies with real-time market data') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Main Trading Area -->
            <div class="lg:col-span-8">
                <!-- Cryptocurrency Selection -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-coins text-blue-600 mr-2"></i>
                            {{ __('Select Cryptocurrency') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="crypto_selector" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Choose Cryptocurrency') }}</label>
                                <select id="crypto_selector" class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                                    @foreach($marketData as $symbol => $data)
                                    <option value="{{ $symbol }}" 
                                            data-price="{{ $data['usd'] }}" 
                                            data-change="{{ $data['usd_24h_change'] }}"
                                            data-name="{{ $data['name'] }}"
                                            {{ $symbol === 'BTC' ? 'selected' : '' }}>
                                        {{ $data['name'] }} ({{ $symbol }}) - {{ format_currency($data['usd']) }}
                                        @if($data['usd_24h_change'] !== 0)
                                            ({{ $data['usd_24h_change'] >= 0 ? '+' : '' }}{{ number_format($data['usd_24h_change'], 2) }}%)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center">
                                <div class="bg-gray-50 rounded-lg p-4 w-full">
                                    <div class="text-sm text-gray-600 mb-1">{{ __('Current Market Price') }}</div>
                                    <div id="selected_price" class="text-2xl font-bold text-gray-900">{{ format_currency($marketData['BTC']['usd']) }}</div>
                                    <div id="selected_change" class="text-sm {{ $marketData['BTC']['usd_24h_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $marketData['BTC']['usd_24h_change'] >= 0 ? '+' : '' }}{{ number_format($marketData['BTC']['usd_24h_change'], 2) }}% (24h)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TradingView Chart -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-chart-area text-green-600 mr-2"></i>
                                <span id="selected-crypto">Bitcoin (BTC)</span>
                            </h3>
                            <div class="text-right">
                                <div id="chart-price" class="text-xl font-bold text-gray-900">{{ format_currency($marketData['BTC']['usd']) }}</div>
                                <div id="chart-change" class="text-sm {{ $marketData['BTC']['usd_24h_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $marketData['BTC']['usd_24h_change'] >= 0 ? '+' : '' }}{{ number_format($marketData['BTC']['usd_24h_change'], 2) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-0">
                        <div id="tradingview_chart" style="height: 500px;"></div>
                    </div>
                </div>

                <!-- Trading Form -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-exchange-alt text-blue-600 mr-2"></i>
                            {{ __('Place Order') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <!-- Low Balance Warning -->
                        @if($usdtBalance <= 0)
                            <div class="mb-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
                                    <div>
                                        <span class="text-orange-800 font-medium">{{ __('Account Funding Required') }}</span>
                                        <p class="text-orange-700 text-sm mt-1">
                                            {{ __('Your account needs to be funded by an administrator before you can start trading. Please contact support.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-green-800">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                                    <span class="text-red-800">{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('trade.execute') }}" id="tradeForm" class="space-y-6">
                            @csrf
                            
                            <input type="hidden" id="crypto_pair" name="crypto_pair" value="BTC/USDT">
                            
                            <!-- Order Type Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Order Type') }}</label>
                                <div class="flex space-x-3">
                                    <label class="flex items-center flex-1">
                                        <input type="radio" name="order_type" value="market" checked class="sr-only">
                                        <div class="flex items-center justify-center w-full py-3 px-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <span class="font-medium text-gray-700">{{ __('Market') }}</span>
                                        </div>
                                    </label>
                                    
                                    <label class="flex items-center flex-1">
                                        <input type="radio" name="order_type" value="limit" class="sr-only">
                                        <div class="flex items-center justify-center w-full py-3 px-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <span class="font-medium text-gray-700">{{ __('Limit') }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Direction Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('Direction') }}</label>
                                <div class="flex space-x-3">
                                    <label class="flex items-center flex-1">
                                        <input type="radio" name="direction" value="buy" checked class="sr-only">
                                        <div class="flex items-center justify-center w-full py-3 px-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-green-50 transition-colors">
                                            <i class="fas fa-arrow-up text-green-600 mr-2"></i>
                                            <span class="font-medium text-gray-700">{{ __('Buy') }}</span>
                                        </div>
                                    </label>
                                    
                                    <label class="flex items-center flex-1">
                                        <input type="radio" name="direction" value="sell" class="sr-only">
                                        <div class="flex items-center justify-center w-full py-3 px-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-red-50 transition-colors">
                                            <i class="fas fa-arrow-down text-red-600 mr-2"></i>
                                            <span class="font-medium text-gray-700">{{ __('Sell') }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Amount and Price -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Amount') }}</label>
                                    <div class="relative">
                                        <input type="number" id="amount" name="amount" step="0.00000001" min="0" required 
                                               placeholder="0.00000000" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-16">
                                        <span id="amount_currency" class="absolute right-3 top-2 text-gray-500 font-medium">BTC</span>
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <span>{{ __('Available:') }} </span>
                                        <span id="available_balance" class="font-medium">0.00000000</span> 
                                        <span id="available_currency">BTC</span>
                                        <span class="text-gray-400">(<span id="available_balance_usd">{{ get_currency_symbol() }}0.00</span>)</span>
                                    </div>
                                </div>
                                
                                <div id="price_field" class="hidden">
                                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Limit Price') }}</label>
                                    <div class="relative">
                                        <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-16">
                                        <span class="absolute right-3 top-2 text-gray-500 font-medium">USDT</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Quick Amount') }}</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <button type="button" class="quick-amount-btn bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg py-2 px-3 text-sm transition-colors" data-percentage="25">
                                        25%
                                    </button>
                                    <button type="button" class="quick-amount-btn bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg py-2 px-3 text-sm transition-colors" data-percentage="50">
                                        50%
                                    </button>
                                    <button type="button" class="quick-amount-btn bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg py-2 px-3 text-sm transition-colors" data-percentage="75">
                                        75%
                                    </button>
                                    <button type="button" class="quick-amount-btn bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg py-2 px-3 text-sm transition-colors" data-percentage="100">
                                        100%
                                    </button>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">{{ __('Order Summary') }}</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Order Type:') }}</span>
                                        <span id="summary_order_type" class="font-medium">Market</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Price:') }}</span>
                                        <span id="summary_price" class="font-medium">Market Price</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Amount:') }}</span>
                                        <span id="summary_amount" class="font-medium">0.00000000 BTC</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Total:') }}</span>
                                        <span id="summary_total" class="font-medium">{{ get_currency_symbol() }}0.00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">{{ __('Fee (0.1%):') }}</span>
                                        <span id="summary_fee" class="font-medium">{{ get_currency_symbol() }}0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="flex justify-between font-semibold">
                                        <span class="text-gray-900">{{ __('Final Total:') }}</span>
                                        <span id="summary_final_total" class="text-gray-900">{{ get_currency_symbol() }}0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="trade_button" class="w-full py-4 px-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <i class="fas fa-arrow-up mr-2"></i>
                                <span id="trade_button_text">{{ __('Buy BTC') }}</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Recent Trades -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-history text-gray-600 mr-2"></i>
                            {{ __('Recent Trades') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        @if($userTrades && $userTrades->count() > 0)
                            <div class="space-y-3">
                                @foreach($userTrades->take(5) as $trade)
                                <div class="flex items-center justify-between py-2">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 {{ $trade->direction === 'buy' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center mr-3">
                                            @if($trade->direction === 'buy')
                                                <i class="fas fa-arrow-up text-green-600 text-xs"></i>
                                            @else
                                                <i class="fas fa-arrow-down text-red-600 text-xs"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($trade->direction) }} {{ $trade->crypto_symbol }}</p>
                                            <p class="text-xs text-gray-500">{{ $trade->created_at->format('M j, H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">{{ format_currency($trade->total_value) }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($trade->amount, 6) }} {{ $trade->crypto_symbol }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500">
                                <i class="fas fa-chart-line text-4xl text-gray-300 mb-3"></i>
                                <p class="text-sm">{{ __('No trades yet') }}</p>
                                <p class="text-xs text-gray-400">{{ __('Your trading history will appear here') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-4">
                <!-- Portfolio Summary -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                <i class="fas fa-wallet text-blue-600 mr-2"></i>
                                {{ __('Portfolio Summary') }}
                            </h3>
                            <button onclick="refreshPortfolio()" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                <i class="fas fa-sync-alt mr-1"></i>
                                {{ __('Refresh') }}
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Total Portfolio Value -->
                        @php
                            // Calculate portfolio value correctly (avoid double-counting USDT)
                            $totalPortfolioValue = 0;
                            foreach ($userWallets as $wallet) {
                                if ($wallet->currency === 'USDT') {
                                    // For USDT, use available_balance (not balance_usd to avoid double-counting)
                                    $totalPortfolioValue += $wallet->available_balance;
                                } else if ($wallet->balance > 0) {
                                    // For crypto, use balance_usd
                                    $totalPortfolioValue += $wallet->balance_usd ?? 0;
                                }
                            }
                        @endphp
                        <div class="text-center mb-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg">
                            <div class="text-sm text-gray-600 mb-1">{{ __('Total Portfolio Value') }}</div>
                            <div class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                                {{ format_currency($totalPortfolioValue ?? 0) }}
                            </div>
                            @php
                                $portfolioChange = 0; // This could be calculated based on historical data
                                $changeClass = $portfolioChange >= 0 ? 'text-green-600' : 'text-red-600';
                                $changeIcon = $portfolioChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                            @endphp
                            <div class="text-sm {{ $changeClass }} mt-1">
                                <i class="fas {{ $changeIcon }} mr-1"></i>
                                {{ $portfolioChange >= 0 ? '+' : '' }}{{ number_format($portfolioChange, 2) }}% (24h)
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-green-50 rounded-lg p-3 text-center">
                                <div class="text-xs text-green-600 font-medium mb-1">{{ __('Available to Trade') }}</div>
                                <div class="text-lg font-bold text-green-700">{{ format_currency($usdtBalance ?? 0) }}</div>
                                <div class="text-xs text-green-600">
                                    USDT 
                                    @if($usdtBalance <= 0)
                                        <span class="text-red-600">({{ __('Unfunded') }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-3 text-center">
                                <div class="text-xs text-blue-600 font-medium mb-1">{{ __('Total Assets') }}</div>
                                <div class="text-lg font-bold text-blue-700">{{ $userWallets->where('balance', '>', 0)->count() + 1 }}</div>
                                <div class="text-xs text-blue-600">{{ __('Holdings') }}</div>
                            </div>
                        </div>
                        
                        <!-- Asset Allocation -->
                        @if($userWallets->where('balance', '>', 0)->count() > 0 || $usdtBalance > 0)
                        <div class="border-t pt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-700">{{ __('Asset Allocation') }}</h4>
                                <span class="text-xs text-gray-500">{{ __('by Value') }}</span>
                            </div>
                            <div class="space-y-3">
                                <!-- USDT Balance -->
                                @if($usdtBalance > 0)
                                @php
                                    $usdtPercentage = $totalPortfolioValue > 0 ? ($usdtBalance / $totalPortfolioValue) * 100 : 0;
                                @endphp
                                <div class="asset-item flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-dollar-sign text-yellow-600 text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-medium text-gray-900">USDT</span>
                                                <span class="text-sm font-semibold text-gray-900">{{ format_currency($usdtBalance) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mr-2">
                                                    <div class="bg-yellow-500 h-1.5 rounded-full asset-progress-bar" style="width: {{ $usdtPercentage }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ number_format($usdtPercentage, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Crypto Holdings -->
                                @foreach($userWallets->where('balance', '>', 0)->sortByDesc('balance_usd') as $wallet)
                                @php
                                    $walletValue = $wallet->balance_usd ?? 0;
                                    $walletPercentage = $totalPortfolioValue > 0 ? ($walletValue / $totalPortfolioValue) * 100 : 0;
                                    $cryptoColor = ['bg-blue-500', 'bg-purple-500', 'bg-indigo-500', 'bg-pink-500', 'bg-red-500', 'bg-orange-500', 'bg-green-500', 'bg-teal-500'][$loop->index % 8];
                                    $cryptoColorLight = ['bg-blue-100', 'bg-purple-100', 'bg-indigo-100', 'bg-pink-100', 'bg-red-100', 'bg-orange-100', 'bg-green-100', 'bg-teal-100'][$loop->index % 8];
                                    $cryptoColorText = ['text-blue-600', 'text-purple-600', 'text-indigo-600', 'text-pink-600', 'text-red-600', 'text-orange-600', 'text-green-600', 'text-teal-600'][$loop->index % 8];
                                @endphp
                                <div class="asset-item flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <div class="w-8 h-8 {{ $cryptoColorLight }} rounded-full flex items-center justify-center mr-3">
                                            <span class="{{ $cryptoColorText }} text-xs font-bold">{{ substr(strtoupper($wallet->currency), 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-medium text-gray-900">{{ strtoupper($wallet->currency) }}</span>
                                                    <span class="text-xs text-gray-500 ml-1">{{ number_format($wallet->balance, 6) }}</span>
                                                </div>
                                                <span class="text-sm font-semibold text-gray-900">{{ format_currency($walletValue) }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mr-2">
                                                    <div class="{{ $cryptoColor }} h-1.5 rounded-full asset-progress-bar" style="width: {{ $walletPercentage }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ number_format($walletPercentage, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-wallet text-4xl text-gray-300 mb-3"></i>
                            <p class="text-sm">{{ __('No assets in portfolio') }}</p>
                            <p class="text-xs text-gray-400">{{ __('Start trading to build your portfolio') }}</p>
                        </div>
                        @endif
                        
                        <!-- Portfolio Actions -->
                        <div class="border-t pt-4 mt-4">
                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="showDepositModal()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-plus mr-1"></i>
                                    {{ __('Deposit') }}
                                </button>
                                <button onclick="showWithdrawModal()" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-minus mr-1"></i>
                                    {{ __('Withdraw') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trading Stats & Quick Info -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                            {{ __('Trading Statistics') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-xs text-green-600 font-medium mb-1">{{ __('Today\'s P&L') }}</div>
                                <div class="text-lg font-bold text-green-700">+{{ get_currency_symbol() }}0.00</div>
                                <div class="text-xs text-green-600">+0.00%</div>
                            </div>
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-xs text-blue-600 font-medium mb-1">{{ __('Total Trades') }}</div>
                                <div class="text-lg font-bold text-blue-700">{{ is_countable($userTrades) ? count($userTrades) : (isset($userTrades) && method_exists($userTrades, 'count') ? $userTrades->count() : 0) }}</div>
                                <div class="text-xs text-blue-600">{{ __('Executed') }}</div>
                            </div>
                        </div>
                        
                        <!-- Quick Market Info -->
                        <div class="border-t pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Market Summary') }}</h4>
                            <div class="space-y-2">
                                @php
                                    $marketDataSlice = array_slice($marketData, 0, 3, true);
                                @endphp
                                @foreach($marketDataSlice as $symbol => $data)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-medium">{{ $symbol }}/USDT</span>
                                    <div class="text-right">
                                        <div class="font-semibold">{{ format_currency($data['usd']) }}</div>
                                        <div class="text-xs {{ $data['usd_24h_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $data['usd_24h_change'] >= 0 ? '+' : '' }}{{ number_format($data['usd_24h_change'], 2) }}%
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="border-t pt-4 mt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Quick Actions') }}</h4>
                            <div class="space-y-2">
                                <button onclick="quickBuy()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-bolt mr-1"></i>
                                    {{ __('Quick Buy ') }}{{ get_currency_symbol() }}100
                                </button>
                                <button onclick="window.location.href='{{ route('profile.edit') }}'" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-cog mr-1"></i>
                                    {{ __('Account Settings') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TradingView Scripts -->
<script src="https://s3.tradingview.com/tv.js"></script>

<script>
    // Global variables
    let currentTradeWidget = null;
    let currentSymbol = 'BTC';
    let currentPrice = {{ $marketData['BTC']['usd'] ?? 50000 }};
    let marketData = @json($marketData);
    let userWallets = @json($userWallets ?? []);
    let usdtBalance = {{ $usdtBalance ?? 0 }};
    let currencySymbol = '{{ get_currency_symbol() }}';
    
    // Debug: Log initial data immediately
    console.log('=== GLOBAL VARIABLES INITIALIZED ===');
    console.log('usdtBalance:', usdtBalance, typeof usdtBalance);
    console.log('userWallets:', userWallets);
    console.log('currencySymbol:', currencySymbol);
    console.log('marketData:', marketData);
    console.log('currentPrice:', currentPrice);
    console.log('=====================================');
    
    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeTradingPage();
        initializeTradeChart('BINANCE:BTCUSDT');
        updateAvailableBalance();
        setupEventListeners();
    });
    
    function initializeTradingPage() {
        console.log('=== initializeTradingPage() START ===');
        
        // Set initial values
        document.getElementById('crypto_pair').value = 'BTC/USDT';
        document.getElementById('amount_currency').textContent = 'BTC';
        document.getElementById('available_currency').textContent = 'USDT'; // Start with USDT since default is buy
        
        // Debug: Log the data from server
        console.log('USDT Balance from server:', usdtBalance);
        console.log('User wallets from server:', userWallets);
        console.log('Currency symbol:', currencySymbol);
        
        // Ensure buy direction is selected by default
        const buyRadio = document.querySelector('input[name="direction"][value="buy"]');
        if (buyRadio) {
            buyRadio.checked = true;
            console.log('Set buy direction as default');
        }
        
        // Initialize with Bitcoin data
        const btcOption = document.querySelector('#crypto_selector option[value="BTC"]');
        if (btcOption) {
            const price = parseFloat(btcOption.dataset.price);
            const change = parseFloat(btcOption.dataset.change);
            const name = btcOption.dataset.name;
            console.log('Initializing with BTC data:', {price, change, name});
            selectCrypto('BTC', price, change, name);
        }
        
        // Force immediate balance update
        console.log('Forcing initial balance update...');
        updateAvailableBalance();
        updateOrderSummary();
        
        console.log('=== initializeTradingPage() END ===');
    }
    
    function initializeTradeChart(symbol) {
        const container = document.getElementById('tradingview_chart');
        if (!container) return;
        
        // Clear existing chart
        container.innerHTML = '';
        
        // Create new TradingView widget
        if (window.TradingView && window.TradingView.widget) {
            currentTradeWidget = new TradingView.widget({
                autosize: true,
                symbol: symbol,
                interval: "5",
                timezone: "Etc/UTC",
                theme: "light",
                style: "1",
                locale: "en",
                toolbar_bg: "#ffffff",
                enable_publishing: false,
                hide_top_toolbar: false,
                hide_legend: false,
                save_image: false,
                allow_symbol_change: false,
                calendar: false,
                support_host: "https://www.tradingview.com",
                studies: [
                    "Volume@tv-basicstudies"
                ],
                show_popup_button: false,
                popup_width: "1000",
                popup_height: "650",
                withdateranges: true,
                hide_side_toolbar: false,
                details: true,
                hotlist: false,
                calendar: true,
                width: "100%",
                height: "500",
                container_id: "tradingview_chart"
            });
        } else {
            // Fallback if TradingView is not loaded
            container.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-gray-500">Loading chart...</p></div>';
            setTimeout(() => initializeTradeChart(symbol), 1000);
        }
    }
    
    function setupEventListeners() {
        // Cryptocurrency selection dropdown
        document.getElementById('crypto_selector').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const symbol = selectedOption.value;
            const price = parseFloat(selectedOption.dataset.price);
            const change = parseFloat(selectedOption.dataset.change);
            const name = selectedOption.dataset.name;
            
            selectCrypto(symbol, price, change, name);
        });
        
        // Order type change
        document.querySelectorAll('input[name="order_type"]').forEach(input => {
            input.addEventListener('change', function() {
                updateOrderTypeUI();
                updateOrderSummary();
            });
        });
        
        // Direction change
        document.querySelectorAll('input[name="direction"]').forEach(input => {
            input.addEventListener('change', function() {
                updateDirectionUI();
                updateAvailableBalance();
                updateTradeButtonText();
                updateOrderSummary();
            });
        });
        
        // Amount input
        document.getElementById('amount').addEventListener('input', function() {
            updateOrderSummary();
        });
        
        // Price input (for limit orders)
        document.getElementById('price').addEventListener('input', function() {
            updateOrderSummary();
        });
        
        // Quick amount buttons
        document.querySelectorAll('.quick-amount-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const percentage = parseInt(this.dataset.percentage);
                setAmountByPercentage(percentage);
            });
        });
        
        // Form submission
        document.getElementById('tradeForm').addEventListener('submit', function(e) {
            if (!validateTrade()) {
                e.preventDefault();
            }
        });
    }
    
    function selectCrypto(symbol, price, change, name) {
        currentSymbol = symbol;
        currentPrice = price;
        
        // Update UI elements
        document.getElementById('crypto_pair').value = symbol + '/USDT';
        document.getElementById('amount_currency').textContent = symbol;
        document.getElementById('available_currency').textContent = symbol;
        document.getElementById('selected-crypto').textContent = name + ' (' + symbol + ')';
        document.getElementById('chart-price').textContent = currencySymbol + price.toLocaleString();
        document.getElementById('chart-change').textContent = (change >= 0 ? '+' : '') + change.toFixed(2) + '%';
        document.getElementById('chart-change').className = 'text-sm ' + (change >= 0 ? 'text-green-600' : 'text-red-600');
        
        // Update price display in selection area
        document.getElementById('selected_price').textContent = currencySymbol + price.toLocaleString();
        document.getElementById('selected_change').textContent = (change >= 0 ? '+' : '') + change.toFixed(2) + '% (24h)';
        document.getElementById('selected_change').className = 'text-sm ' + (change >= 0 ? 'text-green-600' : 'text-red-600');
        
        // Update chart
        initializeTradeChart('BINANCE:' + symbol + 'USDT');
        
        // Update available balance and order summary
        updateAvailableBalance();
        updateTradeButtonText();
        updateOrderSummary();
    }
    
    function updateOrderTypeUI() {
        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        const priceField = document.getElementById('price_field');
        
        // Update order type buttons
        document.querySelectorAll('input[name="order_type"]').forEach(input => {
            const label = input.parentElement.querySelector('div');
            if (input.checked) {
                label.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-700');
                label.classList.remove('border-gray-300', 'text-gray-700');
            } else {
                label.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-700');
                label.classList.add('border-gray-300', 'text-gray-700');
            }
        });
        
        // Show/hide price field for limit orders
        if (orderType === 'limit') {
            priceField.classList.remove('hidden');
            document.getElementById('price').required = true;
            document.getElementById('price').value = currentPrice.toFixed(2);
        } else {
            priceField.classList.add('hidden');
            document.getElementById('price').required = false;
            document.getElementById('price').value = '';
        }
    }
    
    function updateDirectionUI() {
        const direction = document.querySelector('input[name="direction"]:checked').value;
        
        // Update direction buttons
        document.querySelectorAll('input[name="direction"]').forEach(input => {
            const label = input.parentElement.querySelector('div');
            if (input.checked) {
                if (direction === 'buy') {
                    label.classList.add('bg-green-50', 'border-green-500', 'text-green-700');
                    label.classList.remove('border-gray-300', 'text-gray-700');
                } else {
                    label.classList.add('bg-red-50', 'border-red-500', 'text-red-700');
                    label.classList.remove('border-gray-300', 'text-gray-700');
                }
            } else {
                label.classList.remove('bg-green-50', 'border-green-500', 'text-green-700', 'bg-red-50', 'border-red-500', 'text-red-700');
                label.classList.add('border-gray-300', 'text-gray-700');
            }
        });
        
        // Update trade button
        const tradeButton = document.getElementById('trade_button');
        if (direction === 'buy') {
            tradeButton.className = 'w-full py-4 px-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2';
            tradeButton.querySelector('i').className = 'fas fa-arrow-up mr-2';
        } else {
            tradeButton.className = 'w-full py-4 px-4 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold rounded-lg transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2';
            tradeButton.querySelector('i').className = 'fas fa-arrow-down mr-2';
        }
    }
    
    function updateAvailableBalance() {
        const direction = document.querySelector('input[name="direction"]:checked').value;
        let availableBalance = 0;
        let availableBalanceUSD = 0;
        
        console.log('=== updateAvailableBalance() START ===');
        console.log('Direction:', direction);
        console.log('Current Symbol:', currentSymbol);
        console.log('USDT Balance from server:', usdtBalance);
        console.log('User Wallets:', userWallets);
        
        if (direction === 'buy') {
            // For buying, show USDT balance
            availableBalance = parseFloat(usdtBalance);
            availableBalanceUSD = parseFloat(usdtBalance);
            document.getElementById('available_currency').textContent = 'USDT';
            console.log('BUY MODE - Using USDT balance:', availableBalance);
        } else {
            // For selling, show crypto balance
            const cryptoWallet = userWallets.find(wallet => 
                wallet.currency.toLowerCase() === currentSymbol.toLowerCase()
            );
            console.log('SELL MODE - Found crypto wallet:', cryptoWallet);
            
            if (cryptoWallet) {
                // Try available_balance first, then balance, then 0
                availableBalance = parseFloat(cryptoWallet.available_balance || cryptoWallet.balance || 0);
                console.log('Crypto wallet balance:', availableBalance);
            } else {
                availableBalance = 0;
                console.log('No crypto wallet found for', currentSymbol);
            }
            
            availableBalanceUSD = availableBalance * currentPrice;
            document.getElementById('available_currency').textContent = currentSymbol;
        }
        
        // Update DOM elements
        const balanceElement = document.getElementById('available_balance');
        const usdElement = document.getElementById('available_balance_usd');
        
        if (balanceElement) {
            balanceElement.textContent = availableBalance.toFixed(8);
            console.log('Updated balance display to:', availableBalance.toFixed(8));
        }
        
        if (usdElement) {
            usdElement.textContent = currencySymbol + availableBalanceUSD.toFixed(2);
            console.log('Updated USD display to:', currencySymbol + availableBalanceUSD.toFixed(2));
        }
        
        console.log('=== updateAvailableBalance() END ===');
        console.log('Final Balance:', availableBalance);
        console.log('Final USD Value:', availableBalanceUSD);
    }
    
    function updateTradeButtonText() {
        const direction = document.querySelector('input[name="direction"]:checked').value;
        const text = direction === 'buy' ? 'Buy ' + currentSymbol : 'Sell ' + currentSymbol;
        document.getElementById('trade_button_text').textContent = text;
    }
    
    function setAmountByPercentage(percentage) {
        const direction = document.querySelector('input[name="direction"]:checked').value;
        let maxAmount = 0;
        
        if (direction === 'buy') {
            // For buying, calculate max amount based on USDT balance
            maxAmount = (usdtBalance * (percentage / 100)) / currentPrice;
        } else {
            // For selling, use crypto balance
            const cryptoWallet = userWallets.find(wallet => wallet.currency === currentSymbol || wallet.currency === currentSymbol.toLowerCase());
            const cryptoBalance = cryptoWallet ? (cryptoWallet.available_balance || cryptoWallet.balance || 0) : 0;
            maxAmount = cryptoBalance * (percentage / 100);
        }
        
        document.getElementById('amount').value = maxAmount.toFixed(8);
        updateOrderSummary();
    }
    
    function updateOrderSummary() {
        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        const direction = document.querySelector('input[name="direction"]:checked').value;
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const priceInput = document.getElementById('price');
        const price = orderType === 'limit' ? (parseFloat(priceInput.value) || 0) : currentPrice;
        
        const total = amount * price;
        const fee = total * 0.001; // 0.1% fee
        const finalTotal = direction === 'buy' ? total + fee : total - fee;
        
        // Update summary elements
        document.getElementById('summary_order_type').textContent = orderType.charAt(0).toUpperCase() + orderType.slice(1);
        document.getElementById('summary_price').textContent = orderType === 'limit' ? currencySymbol + price.toFixed(2) : 'Market Price';
        document.getElementById('summary_amount').textContent = amount.toFixed(8) + ' ' + currentSymbol;
        document.getElementById('summary_total').textContent = currencySymbol + total.toFixed(2);
        document.getElementById('summary_fee').textContent = currencySymbol + fee.toFixed(2);
        document.getElementById('summary_final_total').textContent = currencySymbol + Math.abs(finalTotal).toFixed(2);
    }
    
    function validateTrade() {
        const direction = document.querySelector('input[name="direction"]:checked').value;
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const orderType = document.querySelector('input[name="order_type"]:checked').value;
        
        console.log('=== validateTrade() START ===');
        console.log('Direction:', direction);
        console.log('Amount:', amount);
        console.log('Order Type:', orderType);
        console.log('USDT Balance:', usdtBalance);
        
        if (amount <= 0) {
            alert('Please enter a valid amount');
            return false;
        }
        
        if (orderType === 'limit') {
            const price = parseFloat(document.getElementById('price').value) || 0;
            if (price <= 0) {
                alert('Please enter a valid limit price');
                return false;
            }
        }
        
        // Check balance
        if (direction === 'buy') {
            const total = amount * currentPrice * 1.001; // Include 0.1% fee
            const availableUSDT = parseFloat(usdtBalance);
            
            console.log('BUY validation:');
            console.log('  Required total:', total);
            console.log('  Available USDT:', availableUSDT);
            
            if (availableUSDT <= 0) {
                alert('No USDT balance available. Please contact support to fund your account before trading.');
                return false;
            }
            
            if (total > availableUSDT) {
                const message = 'Insufficient USDT balance. Required: ' + currencySymbol + total.toFixed(2) + ', Available: ' + currencySymbol + availableUSDT.toFixed(2);
                console.log('BUY validation FAILED:', message);
                alert(message);
                return false;
            }
        } else {
            const cryptoWallet = userWallets.find(wallet => 
                wallet.currency.toLowerCase() === currentSymbol.toLowerCase()
            );
            const cryptoBalance = cryptoWallet ? parseFloat(cryptoWallet.available_balance || cryptoWallet.balance || 0) : 0;
            
            console.log('SELL validation:');
            console.log('  Required amount:', amount);
            console.log('  Available crypto:', cryptoBalance);
            console.log('  Crypto wallet:', cryptoWallet);
            
            if (amount > cryptoBalance) {
                const message = 'Insufficient ' + currentSymbol + ' balance. Required: ' + amount.toFixed(8) + ', Available: ' + cryptoBalance.toFixed(8);
                console.log('SELL validation FAILED:', message);
                alert(message);
                return false;
            }
        }
        
        console.log('validateTrade() PASSED');
        return true;
    }
    
    // Portfolio management functions
    function refreshPortfolio() {
        // Show loading state
        const refreshBtn = document.querySelector('button[onclick="refreshPortfolio()"]');
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Refreshing...';
        refreshBtn.disabled = true;
        
        // Reload the page to get fresh data
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
    
    function showDepositModal() {
        // For now, redirect to deposit page (you can replace with modal later)
        if (confirm('Redirect to deposit page?')) {
            window.location.href = '/deposit';
        }
    }
    
    function showWithdrawModal() {
        // For now, redirect to withdraw page (you can replace with modal later)
        if (confirm('Redirect to withdrawal page?')) {
            window.location.href = '/withdraw';
        }
    }
    
    function quickBuy() {
        // Set amount to €100 (or whatever the currency is) worth of current crypto
        const amount = 100 / currentPrice;
        document.getElementById('amount').value = amount.toFixed(8);
        document.querySelector('input[name="direction"][value="buy"]').checked = true;
        document.querySelector('input[name="direction"][value="buy"]').dispatchEvent(new Event('change'));
        updateOrderSummary();
        
        // Scroll to the trading form
        document.getElementById('tradeForm').scrollIntoView({ behavior: 'smooth' });
    }
</script>

<style>
    .quick-amount-btn:hover {
        background-color: #f3f4f6;
        transform: translateY(-1px);
        transition: all 0.2s ease;
    }
    
    .tradingview-widget-container {
        background: #ffffff;
    }
    
    #tradingview_chart {
        border-radius: 0;
    }
    
    #crypto_selector {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
    
    /* Portfolio Summary Enhancements */
    .portfolio-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .asset-progress-bar {
        transition: width 0.3s ease-in-out;
    }
    
    .portfolio-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .refresh-btn:hover {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }
    
    /* Asset allocation animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .asset-item {
        animation: slideIn 0.3s ease-out;
    }
    
    .asset-item:nth-child(1) { animation-delay: 0.1s; }
    .asset-item:nth-child(2) { animation-delay: 0.2s; }
    .asset-item:nth-child(3) { animation-delay: 0.3s; }
    .asset-item:nth-child(4) { animation-delay: 0.4s; }
    .asset-item:nth-child(5) { animation-delay: 0.5s; }
    
    /* Pulse animation for refresh button */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    .refresh-btn.loading {
        animation: pulse 1.5s infinite;
    }
</style>
@endsection
