<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Overview
            </h2>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    {{ now()->format('M j, Y') }}
                </div>
                <div class="text-sm text-gray-600">
                    Last updated: <span id="last-updated">{{ now()->format('H:i') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Portfolio Value Card -->
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-xs font-medium uppercase tracking-wide">Portfolio Value</p>
                            <p class="text-lg font-bold">${{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</p>
                            <p class="text-blue-200 text-xs mt-1">Available Balance</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Real-time Profit Card -->
                <div class="bg-white rounded-lg shadow-sm p-4" id="profit-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total P&L</p>
                            <p class="text-lg font-bold" id="total-profit" 
                               data-profit="{{ $profitData['total_profit'] }}">
                                ${{ number_format($profitData['total_profit'], 2) }}
                            </p>
                            <p class="text-xs mt-1" id="profit-percentage"
                               data-percentage="{{ $profitData['profit_percentage'] }}">
                                {{ $profitData['profit_percentage'] >= 0 ? '+' : '' }}{{ number_format($profitData['profit_percentage'], 2) }}%
                            </p>
                        </div>
                        <div class="p-2 rounded-lg" id="profit-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Investment Card -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Invested</p>
                            <p class="text-lg font-bold text-gray-800">${{ number_format($profitData['total_investment'], 2) }}</p>
                            <p class="text-gray-500 text-xs mt-1">In {{ count($profitData['holdings']) }} assets</p>
                        </div>
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Current Holdings Value -->
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Holdings Value</p>
                            <p class="text-lg font-bold text-gray-800" id="current-value">${{ number_format($profitData['current_value'], 2) }}</p>
                            <p class="text-gray-500 text-xs mt-1">Live market value</p>
                        </div>
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-base font-semibold text-gray-800">Market Overview</h3>
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
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-base font-semibold text-gray-800">Your Holdings</h3>
                            <button class="text-xs text-blue-600 hover:text-blue-800" onclick="refreshPrices()">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                        <div class="p-4">
                            @if(count($profitData['holdings']) > 0)
                                <div id="holdings-list" class="space-y-3">
                                    @foreach($profitData['holdings'] as $holding)
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
                                                <p class="text-sm font-medium text-gray-800 holding-value">${{ number_format($holding['current_value'], 2) }}</p>
                                                <p class="text-xs {{ $holding['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }} holding-profit">
                                                    {{ $holding['profit'] >= 0 ? '+' : '' }}${{ number_format($holding['profit'], 2) }}
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
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <h3 class="text-base font-semibold text-gray-800 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('trade.index') }}" class="flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-blue-100 group-hover:bg-blue-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">Trade Now</p>
                                    <p class="text-xs text-gray-500">Buy & sell crypto</p>
                                </div>
                            </a>

                            <a href="{{ route('deposit.index') }}" class="flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-green-100 group-hover:bg-green-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">Deposit</p>
                                    <p class="text-xs text-gray-500">Add funds</p>
                                </div>
                            </a>

                            <a href="{{ route('withdrawal.index') }}" class="flex items-center p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                                <div class="p-2 bg-purple-100 group-hover:bg-purple-200 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-800">Withdraw</p>
                                    <p class="text-xs text-gray-500">Cash out</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Live Prices -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-base font-semibold text-gray-800">Live Prices</h3>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        </div>
                        <div class="p-4">
                            <div id="live-prices" class="space-y-3">
                                <!-- Dynamic content will be loaded here -->
                                <div class="text-center py-4">
                                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
                                    <p class="text-xs text-gray-500 mt-2">Loading prices...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-base font-semibold text-gray-800">Recent Activity</h3>
                            <a href="{{ route('history.index') }}" class="text-xs text-blue-600 hover:text-blue-800">View All</a>
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
                                                <p class="text-xs font-medium text-gray-800">${{ number_format($trade->total_value, 2) }}</p>
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
                                    <p class="text-xs">No recent activity</p>
                                    <p class="text-xs text-gray-400 mt-1">Start trading to see activity</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                            <p class="text-gray-600 text-sm font-medium">Ethereum</p>
                            <p class="text-xl font-bold text-gray-800">0.0000 ETH</p>
                            <p class="text-green-600 text-xs mt-1">≈ $0.00</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <span class="text-blue-600 font-bold text-lg">Ξ</span>
                        </div>
                    </div>
                </div>

                <!-- USDT Balance -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">Tether</p>
                            <p class="text-xl font-bold text-gray-800">0.00 USDT</p>
                            <p class="text-green-600 text-xs mt-1">≈ $0.00</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <span class="text-green-600 font-bold text-lg">₮</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Market Overview Chart - Full Width -->
            <div class="mb-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="flex justify-between items-center p-6 pb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Market Overview</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">BTC/USD</button>
                            <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">ETH/USD</button>
                            <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">BNB/USD</button>
                        </div>
                    </div>
                    
                    <!-- TradingView Widget -->
                    <div class="w-full h-96">
                        <div id="tradingview_chart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Live Prices -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Live Prices</h3>
                    <div id="live-prices" class="space-y-3">
                        <!-- Dynamic content will be loaded here -->
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Transactions</h3>
                    <div class="space-y-3">
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-sm">No transactions yet</p>
                            <p class="text-xs text-gray-400 mt-1">Your trading activity will appear here</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions - Full Width -->
            <div class="mt-8">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('deposit.index') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                            <div class="p-3 bg-green-100 group-hover:bg-green-200 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Deposit Funds</p>
                                <p class="text-xs text-gray-500">Add money to your wallet</p>
                            </div>
                        </a>

                        <a href="{{ route('trade.index') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                            <div class="p-3 bg-blue-100 group-hover:bg-blue-200 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Start Trading</p>
                                <p class="text-xs text-gray-500">Buy and sell crypto</p>
                            </div>
                        </a>

                        <a href="{{ route('withdrawal.index') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                            <div class="p-3 bg-purple-100 group-hover:bg-purple-200 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">Withdraw Funds</p>
                                <p class="text-xs text-gray-500">Transfer to bank account</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TradingView Script -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize TradingView widget
            new TradingView.widget({
                "width": "100%",
                "height": 384,
                "symbol": "BINANCE:BTCUSDT",
                "interval": "1H",
                "timezone": "Etc/UTC",
                "theme": "light",
                "style": "1",
                "locale": "en",
                "toolbar_bg": "#f1f3f6",
                "enable_publishing": false,
                "withdateranges": true,
                "hide_side_toolbar": false,
                "allow_symbol_change": true,
                "container_id": "tradingview_chart"
            });

            // Load live crypto prices
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
                            item.className = 'flex justify-between items-center py-2 hover:bg-gray-50 px-2 rounded-lg transition-colors cursor-pointer';
                            item.innerHTML = `
                                <div>
                                    <div class="font-medium text-gray-800 text-sm">${crypto.symbol}</div>
                                    <div class="text-xs text-gray-500">${crypto.name}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-medium text-gray-800 text-sm">$${price.toLocaleString()}</div>
                                    <div class="text-xs ${isPositive ? 'text-green-600' : 'text-red-600'}">
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
</x-app-layout>
