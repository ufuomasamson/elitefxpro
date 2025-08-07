<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('messages.trade_cryptocurrencies') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Trading Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6" x-data="tradingForm()">
                        <div class="flex justify-center mb-6">
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button @click="tradeType = 'buy'" 
                                        :class="tradeType === 'buy' ? 'bg-green-500 text-white' : 'text-gray-600'"
                                        class="px-6 py-2 rounded-md font-medium transition-colors">
                                    {{ __('messages.buy') }}
                                </button>
                                <button @click="tradeType = 'sell'" 
                                        :class="tradeType === 'sell' ? 'bg-red-500 text-white' : 'text-gray-600'"
                                        class="px-6 py-2 rounded-md font-medium transition-colors">
                                    {{ __('messages.sell') }}
                                </button>
                            </div>
                        </div>

                        <form @submit.prevent="submitTrade">
                            <!-- Cryptocurrency Selection -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Cryptocurrency</label>
                                <select x-model="selectedCrypto" @change="updatePrice(); updateChart()" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="bitcoin">Bitcoin (BTC)</option>
                                    <option value="ethereum">Ethereum (ETH)</option>
                                    <option value="binancecoin">Binance Coin (BNB)</option>
                                    <option value="cardano">Cardano (ADA)</option>
                                    <option value="solana">Solana (SOL)</option>
                                    <option value="polkadot">Polkadot (DOT)</option>
                                    <option value="chainlink">Chainlink (LINK)</option>
                                    <option value="litecoin">Litecoin (LTC)</option>
                                    <option value="avalanche-2">Avalanche (AVAX)</option>
                                    <option value="polygon">Polygon (MATIC)</option>
                                    <option value="uniswap">Uniswap (UNI)</option>
                                    <option value="cosmos">Cosmos (ATOM)</option>
                                    <option value="algorand">Algorand (ALGO)</option>
                                    <option value="stellar">Stellar (XLM)</option>
                                    <option value="vechain">VeChain (VET)</option>
                                    <option value="filecoin">Filecoin (FIL)</option>
                                    <option value="tron">TRON (TRX)</option>
                                    <option value="eos">EOS (EOS)</option>
                                    <option value="monero">Monero (XMR)</option>
                                    <option value="aave">Aave (AAVE)</option>
                                </select>
                            </div>

                            <!-- Current Price Display -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">{{ __('messages.current_price') }}:</span>
                                    <span class="text-lg font-bold text-gray-800" x-text="'$' + currentPrice.toLocaleString()"></span>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-500">24h Change:</span>
                                    <span :class="priceChange >= 0 ? 'text-green-600' : 'text-red-600'" 
                                          class="text-xs font-medium" 
                                          x-text="(priceChange >= 0 ? '+' : '') + priceChange.toFixed(2) + '%'"></span>
                                </div>
                            </div>

                            <!-- Amount Input -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span x-text="tradeType === 'buy' ? 'Amount to Spend (USD)' : 'Amount to Sell'"></span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                           x-model="amount" 
                                           @input="calculateEstimate()"
                                           step="0.01" 
                                           min="0.01"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           :placeholder="tradeType === 'buy' ? 'Enter USD amount' : 'Enter crypto amount'">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 text-sm" x-text="tradeType === 'buy' ? 'USD' : getSymbol()"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Amount Buttons -->
                            <div class="mb-6" x-show="quickAmounts.length > 0">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quick Amounts</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <template x-for="quickAmount in quickAmounts" :key="quickAmount">
                                        <button type="button" 
                                                @click="setQuickAmount(quickAmount)"
                                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                                x-text="'$' + quickAmount"></button>
                                    </template>
                                </div>
                            </div>

                            <!-- Trade Preview -->
                            <div x-show="amount > 0" class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h4 class="text-sm font-medium text-blue-800 mb-3">Trade Preview</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">You Pay:</span>
                                        <span class="font-medium text-blue-800" x-text="tradeType === 'buy' ? '$' + parseFloat(amount || 0).toFixed(2) : parseFloat(amount || 0).toFixed(8) + ' ' + getSymbol()"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">You Receive:</span>
                                        <span class="font-medium text-blue-800" x-text="tradeType === 'buy' ? parseFloat(estimatedReceive).toFixed(8) + ' ' + getSymbol() : '$' + parseFloat(estimatedReceive).toFixed(2)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Trading Fee (0.1%):</span>
                                        <span class="font-medium text-blue-800" x-text="'$' + parseFloat(tradingFee).toFixed(2)"></span>
                                    </div>
                                    <hr class="border-blue-200">
                                    <div class="flex justify-between font-semibold">
                                        <span class="text-blue-800">Total Cost:</span>
                                        <span class="text-blue-800" x-text="'$' + parseFloat(totalCost).toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Available Balance -->
                            <div class="mb-6">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Available Balance:</span>
                                    <span class="font-medium" :class="userBalance > 0 ? 'text-green-600' : 'text-red-600'">${{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                                </div>
                                <div x-show="tradeType === 'buy' && amount > 0 && totalCost > userBalance" class="mt-2 text-sm text-red-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Insufficient balance for this trade
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                    :disabled="!amount || amount <= 0 || currentPrice <= 0 || (tradeType === 'buy' && totalCost > userBalance) || isSubmitting"
                                    :class="tradeType === 'buy' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'"
                                    class="w-full text-white py-3 px-4 rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-gray-400">
                                <span x-show="!isSubmitting && currentPrice > 0" x-text="tradeType === 'buy' ? 'Buy Now' : 'Sell Now'"></span>
                                <span x-show="!isSubmitting && currentPrice <= 0" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading Price...
                                </span>
                                <span x-show="isSubmitting" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing Trade...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- TradingView Chart -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6" x-data="{ chartCrypto: 'Bitcoin (BTC)' }">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Price Chart</h3>
                                <p class="text-sm text-gray-600" x-text="chartCrypto" x-init="$watch('$parent.selectedCrypto', value => { 
                                    const names = {
                                        'bitcoin': 'Bitcoin (BTC)',
                                        'ethereum': 'Ethereum (ETH)',
                                        'binancecoin': 'Binance Coin (BNB)',
                                        'cardano': 'Cardano (ADA)',
                                        'solana': 'Solana (SOL)',
                                        'polkadot': 'Polkadot (DOT)',
                                        'chainlink': 'Chainlink (LINK)',
                                        'litecoin': 'Litecoin (LTC)',
                                        'avalanche-2': 'Avalanche (AVAX)',
                                        'polygon': 'Polygon (MATIC)',
                                        'uniswap': 'Uniswap (UNI)',
                                        'cosmos': 'Cosmos (ATOM)',
                                        'algorand': 'Algorand (ALGO)',
                                        'stellar': 'Stellar (XLM)',
                                        'vechain': 'VeChain (VET)',
                                        'filecoin': 'Filecoin (FIL)',
                                        'tron': 'TRON (TRX)',
                                        'eos': 'EOS (EOS)',
                                        'monero': 'Monero (XMR)',
                                        'aave': 'Aave (AAVE)'
                                    };
                                    chartCrypto = names[value] || 'Unknown';
                                })"></p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-lg">1H</button>
                                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg">4H</button>
                                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg">1D</button>
                                <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-100 rounded-lg">1W</button>
                            </div>
                        </div>
                        
                        <!-- TradingView Widget -->
                        <div class="h-96 bg-gray-50 rounded-lg relative">
                            <div id="tradingview_trade_chart" class="w-full h-full"></div>
                            <!-- Loading overlay -->
                            <div x-show="$parent.chartLoading" x-transition class="absolute inset-0 bg-gray-50 bg-opacity-90 flex items-center justify-center rounded-lg">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto mb-2"></div>
                                    <p class="text-sm text-gray-600">Loading chart...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TradingView Script -->
    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script>
        function tradingForm() {
            return {
                tradeType: 'buy',
                selectedCrypto: 'bitcoin',
                amount: '',
                currentPrice: 0,
                priceChange: 0,
                estimatedReceive: 0,
                tradingFee: 0,
                totalCost: 0,
                quickAmounts: [],
                userBalance: {{ Auth::user()->wallet_balance ?? 0 }},
                tradingViewWidget: null,
                chartLoading: false,
                isSubmitting: false,
                
                init() {
                    this.updatePrice();
                    this.calculateQuickAmounts();
                    this.initChart();
                },
                
                calculateQuickAmounts() {
                    // Calculate quick amounts based on user's balance
                    const balance = this.userBalance;
                    if (balance > 0) {
                        this.quickAmounts = [
                            Math.round(balance * 0.1),  // 10% of balance
                            Math.round(balance * 0.25), // 25% of balance
                            Math.round(balance * 0.5),  // 50% of balance
                            Math.round(balance * 0.75)  // 75% of balance
                        ].filter(amount => amount >= 10); // Only show amounts >= $10
                    } else {
                        this.quickAmounts = [10, 25, 50, 100]; // Default amounts for new users
                    }
                },
                
                initChart() {
                    // Initialize TradingView
                    this.tradingViewWidget = new TradingView.widget({
                        "width": "100%",
                        "height": 384,
                        "symbol": this.getTradingViewSymbol(),
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
                        "container_id": "tradingview_trade_chart"
                    });
                },
                
                updateChart() {
                    this.chartLoading = true;
                    
                    // Remove the old chart
                    const chartContainer = document.getElementById('tradingview_trade_chart');
                    chartContainer.innerHTML = '';
                    
                    // Create new chart with updated symbol
                    setTimeout(() => {
                        this.tradingViewWidget = new TradingView.widget({
                            "width": "100%",
                            "height": 384,
                            "symbol": this.getTradingViewSymbol(),
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
                            "container_id": "tradingview_trade_chart",
                            "onChartReady": () => {
                                this.chartLoading = false;
                            }
                        });
                        
                        // Fallback to hide loading after 3 seconds
                        setTimeout(() => {
                            this.chartLoading = false;
                        }, 3000);
                    }, 100);
                },
                
                getTradingViewSymbol() {
                    const symbolMap = {
                        'bitcoin': 'BINANCE:BTCUSDT',
                        'ethereum': 'BINANCE:ETHUSDT',
                        'binancecoin': 'BINANCE:BNBUSDT',
                        'cardano': 'BINANCE:ADAUSDT',
                        'solana': 'BINANCE:SOLUSDT',
                        'polkadot': 'BINANCE:DOTUSDT',
                        'chainlink': 'BINANCE:LINKUSDT',
                        'litecoin': 'BINANCE:LTCUSDT',
                        'avalanche-2': 'BINANCE:AVAXUSDT',
                        'polygon': 'BINANCE:MATICUSDT',
                        'uniswap': 'BINANCE:UNIUSDT',
                        'cosmos': 'BINANCE:ATOMUSDT',
                        'algorand': 'BINANCE:ALGOUSDT',
                        'stellar': 'BINANCE:XLMUSDT',
                        'vechain': 'BINANCE:VETUSDT',
                        'filecoin': 'BINANCE:FILUSDT',
                        'tron': 'BINANCE:TRXUSDT',
                        'eos': 'BINANCE:EOSUSDT',
                        'monero': 'BINANCE:XMRUSDT',
                        'aave': 'BINANCE:AAVEUSDT'
                    };
                    return symbolMap[this.selectedCrypto] || 'BINANCE:BTCUSDT';
                },
                
                updatePrice() {
                    // Fetch current price from API
                    fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${this.selectedCrypto}&vs_currencies=usd&include_24hr_change=true`)
                        .then(response => response.json())
                        .then(data => {
                            this.currentPrice = data[this.selectedCrypto].usd;
                            this.priceChange = data[this.selectedCrypto].usd_24h_change;
                            this.calculateEstimate();
                        })
                        .catch(error => console.error('Error fetching price:', error));
                },
                
                calculateEstimate() {
                    if (!this.amount || this.amount <= 0 || this.currentPrice <= 0) {
                        this.estimatedReceive = 0;
                        this.tradingFee = 0;
                        this.totalCost = 0;
                        return;
                    }
                    
                    if (this.tradeType === 'buy') {
                        this.tradingFee = this.amount * 0.001; // 0.1% fee
                        this.totalCost = parseFloat(this.amount) + this.tradingFee;
                        this.estimatedReceive = this.amount / this.currentPrice;
                    } else {
                        this.estimatedReceive = this.amount * this.currentPrice;
                        this.tradingFee = this.estimatedReceive * 0.001; // 0.1% fee
                        this.totalCost = this.estimatedReceive - this.tradingFee;
                    }
                },
                
                setQuickAmount(amount) {
                    this.amount = amount;
                    this.calculateEstimate();
                },
                
                getSymbol() {
                    const symbols = {
                        'bitcoin': 'BTC',
                        'ethereum': 'ETH',
                        'binancecoin': 'BNB',
                        'cardano': 'ADA',
                        'solana': 'SOL',
                        'polkadot': 'DOT',
                        'chainlink': 'LINK',
                        'litecoin': 'LTC',
                        'avalanche-2': 'AVAX',
                        'polygon': 'MATIC',
                        'uniswap': 'UNI',
                        'cosmos': 'ATOM',
                        'algorand': 'ALGO',
                        'stellar': 'XLM',
                        'vechain': 'VET',
                        'filecoin': 'FIL',
                        'tron': 'TRX',
                        'eos': 'EOS',
                        'monero': 'XMR',
                        'aave': 'AAVE'
                    };
                    return symbols[this.selectedCrypto] || 'CRYPTO';
                },
                
                submitTrade() {
                    if (this.isSubmitting) return;
                    
                    if (!this.amount || this.amount <= 0) {
                        alert('Please enter a valid amount');
                        return;
                    }
                    
                    if (this.currentPrice <= 0) {
                        alert('Unable to get current price. Please try again.');
                        return;
                    }
                    
                    if (this.tradeType === 'buy' && this.totalCost > this.userBalance) {
                        alert('Insufficient balance for this trade');
                        return;
                    }
                    
                    this.isSubmitting = true;
                    
                    // Create a form and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("trade.execute") }}';
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Add trade data
                    const fields = {
                        trade_type: this.tradeType,
                        crypto_symbol: this.getSymbol(),
                        crypto_id: this.selectedCrypto,
                        amount: this.amount,
                        price: this.currentPrice,
                        total_cost: this.totalCost,
                        trading_fee: this.tradingFee,
                        estimated_receive: this.estimatedReceive
                    };
                    
                    Object.keys(fields).forEach(key => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = fields[key];
                        form.appendChild(input);
                    });
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    </script>
</x-app-layout>
