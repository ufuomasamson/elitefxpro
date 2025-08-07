<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Trade') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- TradingView Widget BEGIN -->
                    <div class="tradingview-widget-container mb-6">
                        <div id="tradingview_chart"></div>
                        <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                        <script type="text/javascript">
                            new TradingView.widget({
                                "width": "100%",
                                "height": 500,
                                "symbol": "BINANCE:BTCUSDT",
                                "interval": "D",
                                "timezone": "Etc/UTC",
                                "theme": document.documentElement.classList.contains('dark') ? "dark" : "light",
                                "style": "1",
                                "locale": "en",
                                "toolbar_bg": "#f1f3f6",
                                "enable_publishing": false,
                                "withdateranges": true,
                                "hide_side_toolbar": false,
                                "allow_symbol_change": true,
                                "container_id": "tradingview_chart"
                            });
                        </script>
                    </div>
                    <!-- TradingView Widget END -->
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-2">
                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Place Order</h3>
                                
                                <form method="POST" action="{{ route('trade.execute') }}">
                                    @csrf
                                    
                                    <div class="flex space-x-4 mb-4">
                                        <div class="flex-1">
                                            <x-input-label for="crypto_pair" :value="__('Trading Pair')" />
                                            <select id="crypto_pair" name="crypto_pair" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                                <option value="BTC/USDT">BTC/USDT</option>
                                                <option value="ETH/USDT">ETH/USDT</option>
                                                <option value="BNB/USDT">BNB/USDT</option>
                                                <option value="SOL/USDT">SOL/USDT</option>
                                            </select>
                                        </div>
                                        
                                        <div class="flex-1">
                                            <x-input-label for="type" :value="__('Order Type')" />
                                            <select id="type" name="type" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full">
                                                <option value="buy">Buy</option>
                                                <option value="sell">Sell</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="flex space-x-4 mb-4">
                                        <div class="flex-1">
                                            <x-input-label for="amount" :value="__('Amount')" />
                                            <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" step="0.00000001" required />
                                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                                        </div>
                                        
                                        <div class="flex-1">
                                            <x-input-label for="price" :value="__('Price')" />
                                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" step="0.00000001" required />
                                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-end mt-4">
                                        <x-primary-button class="ml-4">
                                            {{ __('Execute Trade') }}
                                        </x-primary-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Your Wallets</h3>
                                
                                <div class="space-y-3">
                                    @forelse($wallets as $wallet)
                                        <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-800 rounded-md shadow-sm">
                                            <div>
                                                <p class="text-sm font-medium">{{ $wallet->currency }}</p>
                                                <p class="text-lg font-bold">{{ number_format($wallet->balance, 8) }}</p>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ≈ ${{ number_format($wallet->balance_usd, 2) }}
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center py-4 text-gray-500">No wallets found</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-lg font-medium mb-4">Recent Trades</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pair</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @forelse($trades as $trade)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ $trade->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ $trade->crypto_pair }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $trade->type === 'buy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($trade->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ number_format($trade->amount, 8) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ number_format($trade->price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ number_format($trade->total, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ ucfirst($trade->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No trades found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
