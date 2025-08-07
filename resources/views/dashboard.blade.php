@extends('layouts.app')

@section('content')
<div class="py-6 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-tachometer-alt text-blue-600 mr-3"></i>
                    {{ __('messages.dashboard_overview') }}
                </h2>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-600">
                        {{ now()->format('M j, Y') }}
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ __('messages.last_updated') }}: <span id="last-updated">{{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Portfolio Value Card -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-xs font-medium uppercase tracking-wide">{{ __('messages.portfolio_value') }}</p>
                            <p class="text-lg font-bold">{{ format_currency(is_numeric($profitData['current_value'] ?? 0) && is_finite($profitData['current_value'] ?? 0) ? $profitData['current_value'] : 0) }}</p>
                            <p class="text-blue-200 text-xs mt-1">{{ __('messages.available_balance') }}</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Real-time Profit Card -->
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg p-4 text-white" id="profit-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-xs font-medium uppercase tracking-wider">{{ __('messages.total_profits') }}</p>
                            @if(($profitData['has_trades'] ?? false))
                                <p class="text-lg font-bold" id="total-profit" 
                                   data-profit="{{ is_numeric($profitData['total_profit'] ?? 0) && is_finite($profitData['total_profit'] ?? 0) ? number_format($profitData['total_profit'], 2) : '0.00' }}">
                                    {{ format_currency(is_numeric($profitData['total_profit'] ?? 0) && is_finite($profitData['total_profit'] ?? 0) ? $profitData['total_profit'] : 0) }}
                                </p>
                                <p class="text-xs mt-1 text-emerald-100" id="profit-percentage"
                                   data-percentage="{{ is_numeric($profitData['profit_percentage'] ?? 0) && is_finite($profitData['profit_percentage'] ?? 0) ? number_format($profitData['profit_percentage'], 2) : '0.00' }}">
                                    {{ is_numeric($profitData['profit_percentage'] ?? 0) && is_finite($profitData['profit_percentage'] ?? 0) ? (($profitData['profit_percentage'] ?? 0) >= 0 ? '+' : '') . number_format($profitData['profit_percentage'], 2) . '%' : '0.00%' }}
                                </p>
                            @else
                                <p class="text-lg font-bold" id="total-profit" data-profit="0.00">
                                    €0.00
                                </p>
                                <p class="text-xs mt-1 text-emerald-100" id="profit-percentage" data-percentage="0.00">
                                    {{ __('messages.start_trading_to_see_profits', [], 'Start trading to see profits') }}
                                </p>
                            @endif
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg" id="profit-icon">
                            @if(($profitData['has_trades'] ?? false))
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Investment Card -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-xs font-medium uppercase tracking-wide">{{ __('messages.total_invested') }}</p>
                            <p class="text-lg font-bold">{{ format_currency(is_numeric($profitData['total_investment'] ?? 0) && is_finite($profitData['total_investment'] ?? 0) ? $profitData['total_investment'] : 0) }}</p>
                            <p class="text-amber-200 text-xs mt-1">{{ __('messages.in_assets', ['count' => count($profitData['holdings'] ?? [])]) }}</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Current Holdings Value -->
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-xs font-medium uppercase tracking-wide">{{ __('messages.holdings_value') }}</p>
                            <p class="text-lg font-bold" id="current-value">{{ format_currency(is_numeric($profitData['current_value'] ?? 0) && is_finite($profitData['current_value'] ?? 0) ? $profitData['current_value'] : 0) }}</p>
                            <p class="text-purple-200 text-xs mt-1">{{ __('messages.live_market_value') }}</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column - 2/3 width -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- TradingView Chart -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                        <div class="flex justify-between items-center p-4 border-b border-gray-200/50">
                            <h3 class="text-base font-semibold text-gray-800">{{ __('messages.market_overview') }}</h3>
                            <div class="flex space-x-2" id="chart-symbols">
                                <button class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors symbol-btn active" data-symbol="BINANCE:BTCUSDT">BTC</button>
                                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors symbol-btn" data-symbol="BINANCE:ETHUSDT">ETH</button>
                                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors symbol-btn" data-symbol="BINANCE:BNBUSDT">BNB</button>
                                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors symbol-btn" data-symbol="BINANCE:SOLUSDT">SOL</button>
                            </div>
                        </div>
                        
                        <!-- TradingView Widget -->
                        <div class="w-full h-80">
                            <div id="tradingview_chart" class="w-full h-full"></div>
                        </div>
                    </div>

                    <!-- Your Holdings -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                        <div class="flex justify-between items-center p-4 border-b border-gray-200/50">
                            <h3 class="text-base font-semibold text-gray-800">{{ __('messages.your_holdings') }}</h3>
                            <button class="text-xs text-blue-600 hover:text-blue-800" onclick="refreshPrices()">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                {{ __('messages.refresh') }}
                            </button>
                        </div>
                        <div class="p-4">
                            @if(count($profitData['holdings'] ?? []) > 0)
                                <div id="holdings-list" class="space-y-3">
                                    @foreach(($profitData['holdings'] ?? []) as $holding)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg holding-item" data-symbol="{{ $holding['symbol'] }}">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                                    <span class="text-orange-600 font-bold text-sm">{{ substr($holding['symbol'], 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800">{{ $holding['symbol'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ number_format($holding['amount'], 6) }} tokens</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-800 holding-value">{{ format_currency($holding['current_value']) }}</p>
                                                <p class="text-xs {{ $holding['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }} holding-profit">
                                                    {{ $holding['profit'] >= 0 ? '+' : '' }}{{ format_currency($holding['profit']) }}
                                                    ({{ $holding['profit_percentage'] >= 0 ? '+' : '' }}{{ number_format($holding['profit_percentage'], 2) }}%)
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <p class="text-sm">No cryptocurrency holdings</p>
                                    <p class="text-xs text-gray-400 mt-1">Start trading to see your portfolio here</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - 1/3 width -->
                <div class="space-y-6">
                    
                    <!-- Quick Actions -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-4">
                        <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('messages.quick_actions') }}</h3>
                        <div class="space-y-3">
                            <a href="{{ route('trade.index') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-blue-100 group-hover:bg-blue-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">{{ __('messages.trade_now') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('messages.buy_sell_crypto') }}</p>
                                </div>
                            </a>

                            <a href="{{ route('deposit.index') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-green-100 group-hover:bg-green-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">{{ __('messages.deposit') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('messages.add_funds') }}</p>
                                </div>
                            </a>

                            <a href="{{ route('withdrawal.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-purple-100 group-hover:bg-purple-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">{{ __('messages.withdrawal') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('messages.cash_out') }}</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Live Prices -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                        <div class="flex justify-between items-center p-4 border-b border-gray-200/50">
                            <h3 class="text-base font-semibold text-gray-800">{{ __('messages.live_prices') }}</h3>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                        <div class="p-4">
                            <div id="live-prices" class="space-y-3">
                                <!-- Dynamic content will be loaded here -->
                                <div class="text-center py-4">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
                                    <p class="text-xs text-gray-500 mt-2">{{ __('messages.loading_prices') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                        <div class="flex justify-between items-center p-4 border-b border-gray-200/50">
                            <h3 class="text-base font-semibold text-gray-800">{{ __('messages.recent_activity') }}</h3>
                            <a href="{{ route('history.index') }}" class="text-xs text-blue-600 hover:text-blue-800">{{ __('messages.view_all') }}</a>
                        </div>
                        <div class="p-4">
                            @if($recentTrades->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recentTrades->take(5) as $trade)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-6 h-6 {{ $trade->direction === 'buy' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                                                    @if($trade->direction === 'buy')
                                                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-xs font-medium text-gray-800">{{ ucfirst($trade->direction) }} {{ $trade->crypto_symbol }}</p>
                                                    <p class="text-xs text-gray-500">{{ $trade->created_at->format('M j, g:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs font-medium text-gray-800">{{ format_currency($trade->total_value) }}</p>
                                                <p class="text-xs text-gray-500">{{ number_format($trade->amount, 4) }} {{ $trade->crypto_symbol }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-xs">{{ __('messages.no_recent_activity') }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ __('messages.start_trading_to_see_activity') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TradingView Script -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script>
        // TradingView Widget
        let tradingViewWidget = null;
        let currentSymbol = 'BINANCE:BTCUSDT';
        
        // Initialize TradingView chart
        function initChart() {
            tradingViewWidget = new TradingView.widget({
                "width": "100%",
                "height": 320,
                "symbol": currentSymbol,
                "interval": "1H",
                "timezone": "Etc/UTC",
                "theme": "light",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "#f1f3f6",
                "enable_publishing": false,
                "withdateranges": true,
                "hide_side_toolbar": false,
                "allow_symbol_change": false,
                "container_id": "tradingview_chart"
            });
        }
        
        // Update chart symbol
        function updateChart(symbol) {
            currentSymbol = symbol;
            
            // Update active button
            document.querySelectorAll('.symbol-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-100', 'text-blue-600');
                btn.classList.add('text-gray-600');
            });
            
            event.target.classList.add('active', 'bg-blue-100', 'text-blue-600');
            event.target.classList.remove('text-gray-600');
            
            // Remove old chart and create new one
            document.getElementById('tradingview_chart').innerHTML = '';
            
            setTimeout(() => {
                tradingViewWidget = new TradingView.widget({
                    "width": "100%",
                    "height": 320,
                    "symbol": symbol,
                    "interval": "1H",
                    "timezone": "Etc/UTC",
                    "theme": "light",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "withdateranges": true,
                    "hide_side_toolbar": false,
                    "allow_symbol_change": false,
                    "container_id": "tradingview_chart"
                });
            }, 100);
        }
        
        // Add event listeners to symbol buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.symbol-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    updateChart(this.dataset.symbol);
                });
            });
        });
        
        // Live price data
        const cryptoPrices = {};
        
        // Safely parse holdings data with validation
        let holdings = [];
        let hasTrades = false;
        try {
            const rawHoldings = @json($profitData['holdings'] ?? []);
            hasTrades = {{ ($profitData['has_trades'] ?? false) ? 'true' : 'false' }};
            
            if (Array.isArray(rawHoldings)) {
                holdings = rawHoldings.map(holding => ({
                    symbol: holding.symbol || '',
                    name: holding.name || '',
                    amount: parseFloat(holding.amount) || 0,
                    current_value: parseFloat(holding.current_value) || 0,
                    invested_amount: parseFloat(holding.invested_amount) || 0,
                    profit: parseFloat(holding.profit) || 0,
                    profit_percentage: parseFloat(holding.profit_percentage) || 0
                })).filter(holding => holding.symbol && !isNaN(holding.amount));
            }
        } catch (e) {
            console.error('Error parsing holdings data:', e);
            holdings = [];
            hasTrades = false;
        }
        
        console.log('Parsed holdings:', holdings);
        console.log('Has trades:', hasTrades);
        
        // Fetch live prices
        async function fetchLivePrices() {
            try {
                const response = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin,ethereum,binancecoin,solana,cardano,polkadot,chainlink,litecoin&vs_currencies=usd&include_24hr_change=true');
                const data = await response.json();
                
                cryptoPrices.bitcoin = data.bitcoin;
                cryptoPrices.ethereum = data.ethereum;
                cryptoPrices.binancecoin = data.binancecoin;
                cryptoPrices.solana = data.solana;
                cryptoPrices.cardano = data.cardano;
                cryptoPrices.polkadot = data.polkadot;
                cryptoPrices.chainlink = data.chainlink;
                cryptoPrices.litecoin = data.litecoin;
                
                updateLivePrices();
                updateProfitData();
                
                // Update last updated time
                document.getElementById('last-updated').textContent = new Date().toLocaleTimeString('en-US', {
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
            } catch (error) {
                console.error('Error fetching prices:', error);
            }
        }
        
        // Update live prices display
        function updateLivePrices() {
            const container = document.getElementById('live-prices');
            
            const cryptoNames = {
                'bitcoin': { name: 'Bitcoin', symbol: 'BTC', color: 'orange' },
                'ethereum': { name: 'Ethereum', symbol: 'ETH', color: 'blue' },
                'binancecoin': { name: 'BNB', symbol: 'BNB', color: 'yellow' },
                'solana': { name: 'Solana', symbol: 'SOL', color: 'purple' }
            };
            
            let html = '';
            
            Object.keys(cryptoNames).forEach(cryptoId => {
                if (cryptoPrices[cryptoId]) {
                    const crypto = cryptoNames[cryptoId];
                    const price = cryptoPrices[cryptoId];
                    const changeClass = price.usd_24h_change >= 0 ? 'text-green-600' : 'text-red-600';
                    
                    html += `
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-6 h-6 bg-${crypto.color}-100 rounded-full flex items-center justify-center">
                                    <span class="text-${crypto.color}-600 font-bold text-xs">${crypto.symbol.charAt(0)}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">${crypto.name}</p>
                                    <p class="text-xs text-gray-500">${crypto.symbol}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-medium text-gray-800">€${price.usd.toLocaleString()}</p>
                                <p class="text-xs ${changeClass}">
                                    ${price.usd_24h_change >= 0 ? '+' : ''}${price.usd_24h_change.toFixed(2)}%
                                </p>
                            </div>
                        </div>
                    `;
                }
            });
            
            container.innerHTML = html;
        }
        
        // Update profit data in real-time
        async function updateProfitData() {
            try {
                // Fetch real-time profit data from the API
                const response = await fetch('/api/realtime-profit', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const profitData = await response.json();
                console.log('Real-time profit data:', profitData);
                
                // Update Total Profits card
                const profitElement = document.getElementById('total-profit');
                const percentageElement = document.getElementById('profit-percentage');
                const profitCard = document.getElementById('profit-card');
                const profitIcon = document.getElementById('profit-icon');
                
                if (profitElement && percentageElement) {
                    const profit = parseFloat(profitData.total_profit) || 0;
                    const percentage = parseFloat(profitData.profit_percentage) || 0;
                    
                    // Update profit amount
                    profitElement.textContent = `€${profit.toFixed(2)}`;
                    profitElement.setAttribute('data-profit', profit.toFixed(2));
                    
                    // Update profit percentage
                    if (profitData.has_trades) {
                        percentageElement.textContent = `${percentage >= 0 ? '+' : ''}${percentage.toFixed(2)}%`;
                        percentageElement.setAttribute('data-percentage', percentage.toFixed(2));
                    } else {
                        percentageElement.textContent = 'Start trading to see profits';
                    }
                    
                    // Update card styling based on profit/loss
                    if (profitCard && profitIcon) {
                        if (profit > 0) {
                            // Positive profit - green theme
                            profitCard.className = 'bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg p-4 text-white';
                            profitIcon.innerHTML = `
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            `;
                        } else if (profit < 0) {
                            // Negative profit (loss) - red theme
                            profitCard.className = 'bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-4 text-white';
                            profitIcon.innerHTML = `
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            `;
                        } else {
                            // No profit/loss - neutral blue/teal theme
                            profitCard.className = 'bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg p-4 text-white';
                            profitIcon.innerHTML = `
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            `;
                        }
                    }
                }
                
                // Update current value card
                const currentValueElement = document.getElementById('current-value');
                if (currentValueElement && profitData.current_value !== undefined) {
                    currentValueElement.textContent = `€${parseFloat(profitData.current_value).toFixed(2)}`;
                }
                
                // Update holdings with real-time data
                if (profitData.holdings && Array.isArray(profitData.holdings)) {
                    profitData.holdings.forEach(holding => {
                        const holdingElement = document.querySelector(`[data-symbol="${holding.symbol}"]`);
                        if (holdingElement) {
                            const valueElement = holdingElement.querySelector('.holding-value');
                            const profitElement = holdingElement.querySelector('.holding-profit');
                            
                            if (valueElement) {
                                valueElement.textContent = `€${parseFloat(holding.current_value).toFixed(2)}`;
                            }
                            
                            if (profitElement) {
                                const unrealizedPnL = parseFloat(holding.unrealized_pnl) || 0;
                                const unrealizedPnLPercentage = parseFloat(holding.unrealized_pnl_percentage) || 0;
                                
                                profitElement.textContent = `${unrealizedPnL >= 0 ? '+' : ''}€${unrealizedPnL.toFixed(2)} (${unrealizedPnLPercentage >= 0 ? '+' : ''}${unrealizedPnLPercentage.toFixed(2)}%)`;
                                profitElement.className = `text-xs ${unrealizedPnL >= 0 ? 'text-green-600' : 'text-red-600'} holding-profit`;
                            }
                        }
                    });
                }
                
                console.log('✅ Real-time profit data updated successfully');
                
            } catch (error) {
                console.error('❌ Error fetching real-time profit data:', error);
                
                // Fallback to existing logic if API fails
                updateProfitDataFallback();
            }
        }
        
        // Fallback profit update function (original logic)
        function updateProfitDataFallback() {
            // IMPORTANT: Only use server-side calculated profits, disable client-side recalculation
            // This prevents massive profit displays from unrealized portfolio value calculations
            
            // If user has no trades, keep profit at €0.00
            if (!hasTrades) {
                const profitElement = document.getElementById('total-profit');
                const percentageElement = document.getElementById('profit-percentage');
                
                if (profitElement) {
                    profitElement.textContent = '€0.00';
                    profitElement.setAttribute('data-profit', '0.00');
                }
                if (percentageElement) {
                    percentageElement.textContent = 'Start trading to see profits';
                }
                
                console.log('User has no trades - keeping profit at €0.00');
                return;
            }
            
            // For users with trades, keep the server-calculated realized profit values
            // Do NOT recalculate profit using live prices as this can cause massive incorrect values
            const profitElement = document.getElementById('total-profit');
            const percentageElement = document.getElementById('profit-percentage');
            
            if (profitElement && percentageElement) {
                // Get the original server-calculated values from data attributes
                const serverProfit = parseFloat(profitElement.getAttribute('data-profit')) || 0;
                const serverPercentage = parseFloat(percentageElement.getAttribute('data-percentage')) || 0;
                
                // Keep the server values - they are based on actual realized profits from completed sells
                profitElement.textContent = `€${serverProfit.toFixed(2)}`;
                percentageElement.textContent = `${serverPercentage >= 0 ? '+' : ''}${serverPercentage.toFixed(2)}%`;
                
                console.log('Keeping server-calculated profit:', serverProfit);
            }
            
            // Still update individual holding current values for display, but don't use for total profit
            if (!holdings || !Array.isArray(holdings)) {
                console.log('No holdings data available');
                return;
            }
            
            // Update each holding display with current market values
            holdings.forEach(holding => {
                if (!holding || !holding.symbol || isNaN(holding.amount)) {
                    return;
                }
                
                const cryptoId = getCryptoId(holding.symbol);
                
                if (cryptoPrices[cryptoId] && cryptoPrices[cryptoId].usd) {
                    const currentPrice = parseFloat(cryptoPrices[cryptoId].usd) || 0;
                    const amount = parseFloat(holding.amount) || 0;
                    const currentValue = amount * currentPrice;
                    
                    // Update holding display (current value only, not profit calculation)
                    const holdingElement = document.querySelector(`[data-symbol="${holding.symbol}"]`);
                    if (holdingElement) {
                        const valueElement = holdingElement.querySelector('.holding-value');
                        
                        if (valueElement && !isNaN(currentValue) && isFinite(currentValue)) {
                            valueElement.textContent = `€${currentValue.toFixed(2)}`;
                        }
                    }
                }
            });
            
            console.log('Updated holding values without recalculating total profit');
        }
        
        // Helper function to get crypto ID from symbol
        function getCryptoId(symbol) {
            const symbolMap = {
                'BTC': 'bitcoin',
                'ETH': 'ethereum',
                'BNB': 'binancecoin',
                'ADA': 'cardano',
                'SOL': 'solana',
                'DOT': 'polkadot',
                'LINK': 'chainlink',
                'LTC': 'litecoin',
                'AVAX': 'avalanche-2',
                'MATIC': 'polygon',
                'UNI': 'uniswap',
                'ATOM': 'cosmos',
                'ALGO': 'algorand',
                'XLM': 'stellar',
                'VET': 'vechain',
                'FIL': 'filecoin',
                'TRX': 'tron',
                'EOS': 'eos',
                'XMR': 'monero',
                'AAVE': 'aave'
            };
            return symbolMap[symbol] || 'bitcoin';
        }
        
        // Refresh prices manually
        function refreshPrices() {
            fetchLivePrices();
        }
        
        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initChart();
            fetchLivePrices();
            updateProfitData(); // Initial profit update
            
            // Auto-refresh prices every 30 seconds
            setInterval(fetchLivePrices, 30000);
            
            // Auto-refresh profit data every 10 seconds for real-time updates
            setInterval(updateProfitData, 10000);
        });
    </script>

    <style>
        .balance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .symbol-btn.active {
            background-color: #dbeafe !important;
            color: #2563eb !important;
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
    </div>
</div>
@endsection
