@extends('layouts.dashboard')

@section('title', __('Trade Crypto'))

@section('dashboard-content')
<div class="container-fluid">
    <!-- Trading Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Trade Crypto') }}</h1>
        <div>
            <a href="{{ route('trade.history') }}" class="btn btn-outline-primary">
                <i class="fas fa-history me-1"></i> {{ __('Trade History') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Trading Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <ul class="nav nav-tabs card-header-tabs" id="trading-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="btc-tab" data-bs-toggle="tab" data-bs-target="#btc-chart" type="button" role="tab" aria-selected="true">BTC/USDT</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="eth-tab" data-bs-toggle="tab" data-bs-target="#eth-chart" type="button" role="tab" aria-selected="false">ETH/USDT</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="bnb-tab" data-bs-toggle="tab" data-bs-target="#bnb-chart" type="button" role="tab" aria-selected="false">BNB/USDT</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="xrp-tab" data-bs-toggle="tab" data-bs-target="#xrp-chart" type="button" role="tab" aria-selected="false">XRP/USDT</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="trading-chart-tabs">
                        <div class="tab-pane fade show active" id="btc-chart" role="tabpanel" aria-labelledby="btc-tab">
                            <div id="btc-trading-chart" style="height: 500px;">
                                <!-- TradingView Widget BEGIN -->
                                <div class="tradingview-widget-container">
                                    <div id="btc-chart-container"></div>
                                    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                                    <script type="text/javascript">
                                        new TradingView.widget({
                                            "autosize": true,
                                            "symbol": "BINANCE:BTCUSDT",
                                            "interval": "15",
                                            "timezone": "Etc/UTC",
                                            "theme": "light",
                                            "style": "1",
                                            "locale": "en",
                                            "toolbar_bg": "#f1f3f6",
                                            "enable_publishing": false,
                                            "withdateranges": true,
                                            "hide_side_toolbar": false,
                                            "allow_symbol_change": false,
                                            "container_id": "btc-chart-container"
                                        });
                                    </script>
                                </div>
                                <!-- TradingView Widget END -->
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="eth-chart" role="tabpanel" aria-labelledby="eth-tab">
                            <div id="eth-trading-chart" style="height: 500px;">
                                <!-- Will be initialized via JavaScript when tab becomes active -->
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="bnb-chart" role="tabpanel" aria-labelledby="bnb-tab">
                            <div id="bnb-trading-chart" style="height: 500px;">
                                <!-- Will be initialized via JavaScript when tab becomes active -->
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="xrp-chart" role="tabpanel" aria-labelledby="xrp-tab">
                            <div id="xrp-trading-chart" style="height: 500px;">
                                <!-- Will be initialized via JavaScript when tab becomes active -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trading Form -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <ul class="nav nav-pills" id="trading-type-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="buy-tab" data-bs-toggle="tab" data-bs-target="#buy-form" type="button" role="tab" aria-selected="true">{{ __('Buy') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sell-tab" data-bs-toggle="tab" data-bs-target="#sell-form" type="button" role="tab" aria-selected="false">{{ __('Sell') }}</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="trading-form-tabs">
                        <!-- Buy Form -->
                        <div class="tab-pane fade show active" id="buy-form" role="tabpanel" aria-labelledby="buy-tab">
                            <form action="{{ route('trade.buy') }}" method="POST" id="buy-crypto-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="buy-crypto-select" class="form-label">{{ __('Select Cryptocurrency') }}</label>
                                    <select class="form-select" id="buy-crypto-select" name="crypto_symbol" required>
                                        <option value="BTC">Bitcoin (BTC)</option>
                                        <option value="ETH">Ethereum (ETH)</option>
                                        <option value="BNB">Binance Coin (BNB)</option>
                                        <option value="XRP">Ripple (XRP)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="buy-current-price" class="form-label">{{ __('Current Price') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="buy-current-price" name="price" readonly>
                                        <span class="input-group-text">USDT</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="buy-amount" class="form-label">{{ __('Amount to Buy') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.00000001" min="0.00000001" class="form-control" id="buy-amount" name="amount" required>
                                        <span class="input-group-text crypto-symbol">BTC</span>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="buy-total-cost" class="form-label">{{ __('Total Cost') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="buy-total-cost" readonly>
                                        <span class="input-group-text">USDT</span>
                                    </div>
                                    <div class="form-text">{{ __('Available Balance') }}: <span id="usdt-available-balance">0</span> USDT</div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">{{ __('Buy Now') }}</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Sell Form -->
                        <div class="tab-pane fade" id="sell-form" role="tabpanel" aria-labelledby="sell-tab">
                            <form action="{{ route('trade.sell') }}" method="POST" id="sell-crypto-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="sell-crypto-select" class="form-label">{{ __('Select Cryptocurrency') }}</label>
                                    <select class="form-select" id="sell-crypto-select" name="crypto_symbol" required>
                                        <option value="BTC">Bitcoin (BTC)</option>
                                        <option value="ETH">Ethereum (ETH)</option>
                                        <option value="BNB">Binance Coin (BNB)</option>
                                        <option value="XRP">Ripple (XRP)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="sell-current-price" class="form-label">{{ __('Current Price') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sell-current-price" name="price" readonly>
                                        <span class="input-group-text">USDT</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="sell-amount" class="form-label">{{ __('Amount to Sell') }}</label>
                                    <div class="input-group">
                                        <input type="number" step="0.00000001" min="0.00000001" class="form-control" id="sell-amount" name="amount" required>
                                        <span class="input-group-text sell-crypto-symbol">BTC</span>
                                    </div>
                                    <div class="form-text">{{ __('Available') }}: <span id="crypto-available-balance">0</span> <span class="available-crypto-symbol">BTC</span></div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="sell-total-value" class="form-label">{{ __('Total Value') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sell-total-value" readonly>
                                        <span class="input-group-text">USDT</span>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger">{{ __('Sell Now') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Crypto Balance Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">{{ __('Your Balances') }}</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($wallets as $wallet)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="
                                    @if($wallet->crypto_symbol == 'BTC') fab fa-bitcoin text-warning
                                    @elseif($wallet->crypto_symbol == 'ETH') fab fa-ethereum text-info
                                    @elseif($wallet->crypto_symbol == 'USDT') fas fa-dollar-sign text-success
                                    @elseif($wallet->crypto_symbol == 'BNB') fas fa-coins text-warning
                                    @elseif($wallet->crypto_symbol == 'XRP') fas fa-chart-line text-primary
                                    @else fas fa-circle text-secondary
                                    @endif me-2"></i>
                                {{ $wallet->crypto_symbol }}
                            </div>
                            <div class="text-end">
                                <span class="fw-medium">{{ $wallet->balance }}</span>
                                <div class="small text-muted crypto-usd-value" data-symbol="{{ $wallet->crypto_symbol }}">
                                    ≈ $0.00
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cryptoPrices = {
            BTC: 0,
            ETH: 0,
            BNB: 0,
            XRP: 0,
            USDT: 1
        };
        
        // Fetch crypto prices from API
        function fetchCryptoPrices() {
            fetch('/api/crypto-prices')
                .then(response => response.json())
                .then(data => {
                    // Update prices
                    Object.assign(cryptoPrices, data);
                    
                    // Update current price fields
                    document.getElementById('buy-current-price').value = cryptoPrices[document.getElementById('buy-crypto-select').value];
                    document.getElementById('sell-current-price').value = cryptoPrices[document.getElementById('sell-crypto-select').value];
                    
                    // Update USD values
                    document.querySelectorAll('.crypto-usd-value').forEach(element => {
                        const symbol = element.getAttribute('data-symbol');
                        const parentListItem = element.closest('.list-group-item');
                        const balanceElement = parentListItem.querySelector('.fw-medium');
                        const balance = parseFloat(balanceElement.textContent);
                        
                        if (cryptoPrices[symbol]) {
                            const usdValue = balance * cryptoPrices[symbol];
                            element.textContent = `≈ $${usdValue.toFixed(2)}`;
                        }
                    });
                    
                    // Update available balance displays
                    updateAvailableBalances();
                })
                .catch(error => console.error('Error fetching crypto prices:', error));
        }
        
        // Fetch wallet balances
        function fetchWalletBalances() {
            // Set USDT available balance
            fetch('/api/wallet/balance/USDT')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('usdt-available-balance').textContent = data.balance;
                })
                .catch(error => console.error('Error fetching USDT balance:', error));
                
            updateAvailableBalances();
        }
        
        // Update available balances based on selected crypto
        function updateAvailableBalances() {
            const sellCryptoSymbol = document.getElementById('sell-crypto-select').value;
            document.querySelectorAll('.sell-crypto-symbol').forEach(el => el.textContent = sellCryptoSymbol);
            document.querySelectorAll('.available-crypto-symbol').forEach(el => el.textContent = sellCryptoSymbol);
            
            fetch(`/api/wallet/balance/${sellCryptoSymbol}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('crypto-available-balance').textContent = data.balance;
                })
                .catch(error => console.error('Error fetching balance:', error));
        }
        
        // Calculate buy total
        document.getElementById('buy-amount').addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const price = parseFloat(document.getElementById('buy-current-price').value) || 0;
            const total = amount * price;
            document.getElementById('buy-total-cost').value = total.toFixed(2);
        });
        
        // Calculate sell total
        document.getElementById('sell-amount').addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const price = parseFloat(document.getElementById('sell-current-price').value) || 0;
            const total = amount * price;
            document.getElementById('sell-total-value').value = total.toFixed(2);
        });
        
        // Handle crypto selection changes
        document.getElementById('buy-crypto-select').addEventListener('change', function() {
            const symbol = this.value;
            document.querySelectorAll('.crypto-symbol').forEach(el => el.textContent = symbol);
            document.getElementById('buy-current-price').value = cryptoPrices[symbol] || 0;
            
            // Recalculate total
            const buyAmountInput = document.getElementById('buy-amount');
            if (buyAmountInput.value) {
                const event = new Event('input');
                buyAmountInput.dispatchEvent(event);
            }
        });
        
        document.getElementById('sell-crypto-select').addEventListener('change', function() {
            const symbol = this.value;
            document.querySelectorAll('.sell-crypto-symbol').forEach(el => el.textContent = symbol);
            document.querySelectorAll('.available-crypto-symbol').forEach(el => el.textContent = symbol);
            document.getElementById('sell-current-price').value = cryptoPrices[symbol] || 0;
            
            // Update available balance
            updateAvailableBalances();
            
            // Recalculate total
            const sellAmountInput = document.getElementById('sell-amount');
            if (sellAmountInput.value) {
                const event = new Event('input');
                sellAmountInput.dispatchEvent(event);
            }
        });
        
        // Initialize TradingView charts for other tabs
        document.getElementById('eth-tab').addEventListener('click', function() {
            if (!document.getElementById('eth-chart-container')) {
                const container = document.createElement('div');
                container.id = 'eth-chart-container';
                document.getElementById('eth-trading-chart').appendChild(container);
                
                new TradingView.widget({
                    "autosize": true,
                    "symbol": "BINANCE:ETHUSDT",
                    "interval": "15",
                    "timezone": "Etc/UTC",
                    "theme": "light",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "withdateranges": true,
                    "hide_side_toolbar": false,
                    "allow_symbol_change": false,
                    "container_id": "eth-chart-container"
                });
            }
        });
        
        document.getElementById('bnb-tab').addEventListener('click', function() {
            if (!document.getElementById('bnb-chart-container')) {
                const container = document.createElement('div');
                container.id = 'bnb-chart-container';
                document.getElementById('bnb-trading-chart').appendChild(container);
                
                new TradingView.widget({
                    "autosize": true,
                    "symbol": "BINANCE:BNBUSDT",
                    "interval": "15",
                    "timezone": "Etc/UTC",
                    "theme": "light",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "withdateranges": true,
                    "hide_side_toolbar": false,
                    "allow_symbol_change": false,
                    "container_id": "bnb-chart-container"
                });
            }
        });
        
        document.getElementById('xrp-tab').addEventListener('click', function() {
            if (!document.getElementById('xrp-chart-container')) {
                const container = document.createElement('div');
                container.id = 'xrp-chart-container';
                document.getElementById('xrp-trading-chart').appendChild(container);
                
                new TradingView.widget({
                    "autosize": true,
                    "symbol": "BINANCE:XRPUSDT",
                    "interval": "15",
                    "timezone": "Etc/UTC",
                    "theme": "light",
                    "style": "1",
                    "locale": "en",
                    "toolbar_bg": "#f1f3f6",
                    "enable_publishing": false,
                    "withdateranges": true,
                    "hide_side_toolbar": false,
                    "allow_symbol_change": false,
                    "container_id": "xrp-chart-container"
                });
            }
        });
        
        // Initial calls
        fetchCryptoPrices();
        fetchWalletBalances();
        
        // Update prices every 30 seconds
        setInterval(fetchCryptoPrices, 30000);
    });
</script>
@endsection
