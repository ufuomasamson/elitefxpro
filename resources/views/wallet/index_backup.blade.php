<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Wallet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-2">
                            <h3 class="text-lg font-medium mb-4">Your Wallet</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse($wallets as $wallet)
                                    <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4 border-l-4 border-indigo-500">
                                        <div class="flex justify-between items-center mb-2">
                                            <h4 class="text-lg font-bold">{{ $wallet->currency }}</h4>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $wallet->currency_name }}
                                            </div>
                                        </div>
                                        <div class="text-2xl font-bold">
                                            {{ number_format($wallet->balance, 8) }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            ≈ ${{ number_format($wallet->balance_usd, 2) }}
                                        </div>
                                        <div class="mt-4 flex space-x-2">
                                            <a href="{{ route('deposit.create', ['currency' => $wallet->currency]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Deposit
                                            </a>
                                            <a href="{{ route('withdrawal.create', ['currency' => $wallet->currency]) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Withdraw
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-3">
                                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-400 p-4">
                                            <div class="flex">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                                        You don't have any wallets yet. Contact support to create your first wallet.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium mb-4">Market Overview</h3>
                            
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4">
                                <div id="market-overview">
                                    <!-- This would be populated via JS with CoinGecko API data -->
                                    <div class="animate-pulse space-y-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="h-4 w-24 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                            <div class="ml-auto h-4 w-20 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="h-4 w-24 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                            <div class="ml-auto h-4 w-20 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                            <div class="h-4 w-24 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                            <div class="ml-auto h-4 w-20 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <script>
                                    // We would add code to fetch from CoinGecko API here
                                    document.addEventListener('DOMContentLoaded', function() {
                                        fetch('https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=10&page=1')
                                            .then(response => response.json())
                                            .then(data => {
                                                const marketOverview = document.getElementById('market-overview');
                                                marketOverview.innerHTML = '';
                                                
                                                data.slice(0, 5).forEach(coin => {
                                                    const priceChange = coin.price_change_percentage_24h;
                                                    const isPositive = priceChange >= 0;
                                                    
                                                    const coinElement = document.createElement('div');
                                                    coinElement.className = 'flex items-center py-2 border-b border-gray-200 dark:border-gray-600';
                                                    coinElement.innerHTML = `
                                                        <img src="${coin.image}" alt="${coin.name}" class="h-8 w-8 rounded-full mr-2">
                                                        <div>
                                                            <div class="font-medium">${coin.symbol.toUpperCase()}</div>
                                                            <div class="text-xs text-gray-500">${coin.name}</div>
                                                        </div>
                                                        <div class="ml-auto text-right">
                                                            <div class="font-medium">$${coin.current_price.toLocaleString()}</div>
                                                            <div class="text-xs ${isPositive ? 'text-green-500' : 'text-red-500'}">
                                                                ${isPositive ? '+' : ''}${priceChange.toFixed(2)}%
                                                            </div>
                                                        </div>
                                                    `;
                                                    marketOverview.appendChild(coinElement);
                                                });
                                            })
                                            .catch(error => {
                                                console.error('Error fetching market data:', error);
                                                document.getElementById('market-overview').innerHTML = `
                                                    <div class="text-center p-4 text-gray-500">
                                                        Unable to load market data
                                                    </div>
                                                `;
                                            });
                                    });
                                </script>
                            </div>
                            
                            <div class="mt-6 bg-white dark:bg-gray-700 rounded-lg shadow-md p-4">
                                <h4 class="font-medium mb-2">Quick Actions</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('trade.index') }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                        Trade Now
                                    </a>
                                    <a href="{{ route('deposit.create') }}" class="block w-full text-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Deposit Funds
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-medium mb-4">Recent Transactions</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Currency</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ $transaction->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-800' : 
                                                       ($transaction->type === 'withdrawal' ? 'bg-red-100 text-red-800' : 
                                                        'bg-blue-100 text-blue-800') }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ $transaction->currency }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                                {{ number_format($transaction->amount, 8) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                        'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No transactions found
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
