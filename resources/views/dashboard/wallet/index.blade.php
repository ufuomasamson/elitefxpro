@extends('layouts.dashboard')

@section('title', __('Wallet Overview'))

@section('dashboard-content')
<div class="container-fluid">
    <!-- Wallet Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Wallet Overview') }}</h1>
    </div>

    <!-- Portfolio Value Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">{{ __('Portfolio Value') }}</h5>
                    <h2 class="display-6 fw-bold mb-0">$<span id="portfolio-total-value">{{ number_format($user->getTotalPortfolioValue(), 2) }}</span></h2>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="walletActions" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="walletActions">
                                <li><a class="dropdown-item" href="{{ route('deposit.index') }}"><i class="fas fa-arrow-down me-2"></i>{{ __('Deposit Funds') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('withdrawal.index') }}"><i class="fas fa-arrow-up me-2"></i>{{ __('Withdraw Funds') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('trade.index') }}"><i class="fas fa-exchange-alt me-2"></i>{{ __('Trade') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Balances -->
    <div class="row g-4">
        @forelse($wallets as $wallet)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="crypto-icon me-3">
                            @if($wallet->crypto_symbol == 'BTC')
                                <i class="fab fa-bitcoin fa-2x text-warning"></i>
                            @elseif($wallet->crypto_symbol == 'ETH')
                                <i class="fab fa-ethereum fa-2x text-info"></i>
                            @elseif($wallet->crypto_symbol == 'USDT')
                                <i class="fas fa-dollar-sign fa-2x text-success"></i>
                            @elseif($wallet->crypto_symbol == 'BNB')
                                <i class="fas fa-coins fa-2x text-warning"></i>
                            @elseif($wallet->crypto_symbol == 'XRP')
                                <i class="fas fa-chart-line fa-2x text-primary"></i>
                            @else
                                <i class="fas fa-circle fa-2x text-secondary"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="card-title mb-0">{{ $wallet->crypto_symbol }}</h5>
                            <p class="text-muted mb-0">
                                @if($wallet->crypto_symbol == 'BTC')
                                    {{ __('Bitcoin') }}
                                @elseif($wallet->crypto_symbol == 'ETH')
                                    {{ __('Ethereum') }}
                                @elseif($wallet->crypto_symbol == 'USDT')
                                    {{ __('Tether USD') }}
                                @elseif($wallet->crypto_symbol == 'BNB')
                                    {{ __('Binance Coin') }}
                                @elseif($wallet->crypto_symbol == 'XRP')
                                    {{ __('Ripple') }}
                                @else
                                    {{ $wallet->crypto_symbol }}
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h2 class="fw-bold mb-0">{{ $wallet->balance }}</h2>
                        <p class="text-muted mb-0">≈ $<span class="crypto-usd-value" data-symbol="{{ $wallet->crypto_symbol }}" data-balance="{{ $wallet->balance }}">0.00</span></p>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('deposit.index') }}?crypto={{ $wallet->crypto_symbol }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="fas fa-arrow-down me-1"></i> {{ __('Deposit') }}
                        </a>
                        <a href="{{ route('withdrawal.index') }}?crypto={{ $wallet->crypto_symbol }}" class="btn btn-sm btn-outline-danger flex-grow-1">
                            <i class="fas fa-arrow-up me-1"></i> {{ __('Withdraw') }}
                        </a>
                        <a href="{{ route('trade.index') }}?crypto={{ $wallet->crypto_symbol }}" class="btn btn-sm btn-outline-success flex-grow-1">
                            <i class="fas fa-exchange-alt me-1"></i> {{ __('Trade') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-wallet fa-4x text-muted mb-3"></i>
                    <h5>{{ __('No Crypto Assets Yet') }}</h5>
                    <p class="text-muted">{{ __('Start your crypto journey by depositing or buying some assets.') }}</p>
                    <div class="mt-3">
                        <a href="{{ route('deposit.index') }}" class="btn btn-primary me-2">
                            <i class="fas fa-arrow-down me-1"></i> {{ __('Make a Deposit') }}
                        </a>
                        <a href="{{ route('trade.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-exchange-alt me-1"></i> {{ __('Trade Now') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Price Chart -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Market Overview') }}</h5>
        </div>
        <div class="card-body">
            <div style="height: 400px;">
                <!-- TradingView Widget BEGIN -->
                <div class="tradingview-widget-container">
                    <div id="tradingview-market-overview"></div>
                    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                    <script type="text/javascript">
                        new TradingView.MediumWidget({
                            "symbols": [
                                ["Bitcoin", "BINANCE:BTCUSDT|1D"],
                                ["Ethereum", "BINANCE:ETHUSDT|1D"],
                                ["Binance Coin", "BINANCE:BNBUSDT|1D"],
                                ["Ripple", "BINANCE:XRPUSDT|1D"]
                            ],
                            "chartOnly": false,
                            "width": "100%",
                            "height": 400,
                            "locale": "en",
                            "colorTheme": "light",
                            "autosize": true,
                            "showVolume": false,
                            "hideDateRanges": false,
                            "scalePosition": "right",
                            "scaleMode": "Normal",
                            "fontFamily": "-apple-system, BlinkMacSystemFont, Trebuchet MS, Roboto, Ubuntu, sans-serif",
                            "fontSize": "12",
                            "noTimeScale": false,
                            "valuesTracking": "1",
                            "container_id": "tradingview-market-overview"
                        });
                    </script>
                </div>
                <!-- TradingView Widget END -->
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{ __('Recent Transactions') }}</h5>
            <a href="{{ route('history') }}" class="btn btn-sm btn-link">{{ __('View All') }}</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions ?? [] as $transaction)
                        <tr>
                            <td>
                                @if($transaction->type === 'deposit')
                                    <span class="badge bg-success-soft text-success">
                                        <i class="fas fa-arrow-down me-1"></i> {{ __('Deposit') }}
                                    </span>
                                @elseif($transaction->type === 'withdrawal')
                                    <span class="badge bg-danger-soft text-danger">
                                        <i class="fas fa-arrow-up me-1"></i> {{ __('Withdrawal') }}
                                    </span>
                                @elseif($transaction->type === 'trade')
                                    <span class="badge bg-primary-soft text-primary">
                                        <i class="fas fa-exchange-alt me-1"></i> {{ __('Trade') }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-soft text-secondary">
                                        <i class="fas fa-circle me-1"></i> {{ ucfirst($transaction->type) }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                @if($transaction->type === 'deposit')
                                    <span class="text-success">+{{ $transaction->amount }}</span>
                                @elseif($transaction->type === 'withdrawal')
                                    <span class="text-danger">-{{ $transaction->amount }}</span>
                                @else
                                    {{ $transaction->amount }}
                                @endif
                            </td>
                            <td>
                                @if($transaction->status === 'completed')
                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                @elseif($transaction->status === 'pending')
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @elseif($transaction->status === 'rejected')
                                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-receipt fa-2x text-muted mb-2"></i>
                                <p class="mb-0">{{ __('No transactions yet') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch crypto prices and update USD values
        function updateWalletValues() {
            fetch('/api/crypto-prices')
                .then(response => response.json())
                .then(data => {
                    let portfolioValue = 0;
                    
                    document.querySelectorAll('.crypto-usd-value').forEach(element => {
                        const symbol = element.getAttribute('data-symbol');
                        const balance = parseFloat(element.getAttribute('data-balance')) || 0;
                        
                        if (data[symbol]) {
                            const usdValue = balance * data[symbol];
                            element.textContent = usdValue.toFixed(2);
                            portfolioValue += usdValue;
                        }
                    });
                    
                    document.getElementById('portfolio-total-value').textContent = portfolioValue.toFixed(2);
                })
                .catch(error => console.error('Error fetching crypto prices:', error));
        }
        
        // Initial update
        updateWalletValues();
        
        // Update values every 30 seconds
        setInterval(updateWalletValues, 30000);
    });
</script>
@endsection
