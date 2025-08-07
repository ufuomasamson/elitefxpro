@extends('layouts.dashboard')

@section('title', __('Dashboard Overview'))
@section('page-title', __('Dashboard Overview'))

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="dashboard-card stat-card">
            <i class="fas fa-wallet metric-card-icon text-primary"></i>
            <div class="stat-value text-primary">${{ number_format($user->wallet_balance, 2) }}</div>
            <div class="stat-label">{{ __('Total Balance') }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="dashboard-card stat-card">
            <i class="fas fa-chart-line metric-card-icon text-success"></i>
            <div class="stat-value text-success">${{ number_format($totalPortfolioValue, 2) }}</div>
            <div class="stat-label">{{ __('Portfolio Value') }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="dashboard-card stat-card">
            <i class="fas fa-clock metric-card-icon text-warning"></i>
            <div class="stat-value text-warning">{{ $pendingDeposits }}</div>
            <div class="stat-label">{{ __('Pending Deposits') }}</div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="dashboard-card stat-card">
            <i class="fas fa-hourglass-half metric-card-icon text-info"></i>
            <div class="stat-value text-info">{{ $pendingWithdrawals }}</div>
            <div class="stat-label">{{ __('Pending Withdrawals') }}</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Wallet Balances -->
    <div class="col-lg-4 mb-4">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-coins text-warning me-2"></i>
                    {{ __('Wallet Balances') }}
                </h5>
                <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="card-body p-0">
                @if($wallets->count() > 0)
                    @foreach($wallets as $wallet)
                    <div class="transaction-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $wallet->formatted_symbol }}</strong>
                                <div class="small text-muted">{{ $wallet->crypto_symbol }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ number_format($wallet->balance, 8) }}</div>
                                @if($wallet->locked_balance > 0)
                                <div class="small text-warning">
                                    {{ __('Locked') }}: {{ number_format($wallet->locked_balance, 8) }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center p-4">
                        <i class="fas fa-wallet text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">{{ __('No crypto balances yet') }}</p>
                        <a href="{{ route('deposit.index') }}" class="btn btn-primary btn-sm">
                            {{ __('Make a Deposit') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- TradingView Chart -->
    <div class="col-lg-8 mb-4">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-chart-area text-primary me-2"></i>
                    {{ __('Market Chart') }}
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" id="cryptoSelector" style="width: auto;">
                        <option value="BINANCE:BTCUSDT">Bitcoin (BTC/USDT)</option>
                        <option value="BINANCE:ETHUSDT">Ethereum (ETH/USDT)</option>
                        <option value="BINANCE:ADAUSDT">Cardano (ADA/USDT)</option>
                        <option value="BINANCE:SOLUSDT">Solana (SOL/USDT)</option>
                        <option value="BINANCE:DOTUSDT">Polkadot (DOT/USDT)</option>
                        <option value="BINANCE:LINKUSDT">Chainlink (LINK/USDT)</option>
                        <option value="BINANCE:BNBUSDT">BNB (BNB/USDT)</option>
                        <option value="BINANCE:XRPUSDT">Ripple (XRP/USDT)</option>
                    </select>
                    <div class="badge bg-primary">{{ __('Live') }}</div>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- TradingView Widget BEGIN -->
                <div class="tradingview-widget-container" style="height: 350px;">
                    <!-- Chart will be loaded here dynamically -->
                </div>
                <!-- TradingView Widget END -->
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Transactions -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history text-info me-2"></i>
                    {{ __('Recent Transactions') }}
                </h5>
                <a href="{{ route('history.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentTransactions->count() > 0)
                    @foreach($recentTransactions as $transaction)
                    <div class="transaction-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">{{ ucfirst($transaction->type) }}</div>
                                <div class="small text-muted">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">
                                    @if($transaction->type == 'deposit' || $transaction->type == 'fund')
                                        <span class="text-success">+${{ number_format($transaction->amount, 2) }}</span>
                                    @else
                                        <span class="text-danger">-${{ number_format($transaction->amount, 2) }}</span>
                                    @endif
                                </div>
                                <span class="status-badge status-{{ $transaction->status }}">
                                    {{ $transaction->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center p-4">
                        <i class="fas fa-history text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">{{ __('No transactions yet') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Trades -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt text-success me-2"></i>
                    {{ __('Recent Trades') }}
                </h5>
                <a href="{{ route('history.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentTrades->count() > 0)
                    @foreach($recentTrades as $trade)
                    <div class="transaction-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">
                                    <span class="{{ $trade->direction == 'buy' ? 'text-success' : 'text-danger' }}">
                                        {{ strtoupper($trade->direction) }}
                                    </span>
                                    {{ $trade->formatted_symbol }}
                                </div>
                                <div class="small text-muted">
                                    {{ $trade->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ number_format($trade->amount, 8) }}</div>
                                <div class="small text-muted">
                                    @${{ number_format($trade->price_at_time, 2) }}
                                </div>
                                <span class="status-badge status-{{ $trade->status }}">
                                    {{ $trade->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center p-4">
                        <i class="fas fa-exchange-alt text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">{{ __('No trades yet') }}</p>
                        <a href="{{ route('trade.index') }}" class="btn btn-primary btn-sm">
                            {{ __('Start Trading') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    {{ __('Quick Actions') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('trade.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="fas fa-exchange-alt mb-2" style="font-size: 2rem;"></i>
                            <span>{{ __('Trade Crypto') }}</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('deposit.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="fas fa-plus-circle mb-2" style="font-size: 2rem;"></i>
                            <span>{{ __('Deposit Funds') }}</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('withdrawal.index') }}" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="fas fa-minus-circle mb-2" style="font-size: 2rem;"></i>
                            <span>{{ __('Withdraw Funds') }}</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <a href="{{ route('history.index') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="fas fa-history mb-2" style="font-size: 2rem;"></i>
                            <span>{{ __('View History') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Store the current chart widget
    let currentDashboardWidget = null;
    
    // Crypto selector functionality
    document.addEventListener('DOMContentLoaded', function() {
        const cryptoSelector = document.getElementById('cryptoSelector');
        
        // Initialize the first chart
        initializeDashboardChart('BINANCE:BTCUSDT');
        
        cryptoSelector.addEventListener('change', function() {
            const selectedSymbol = this.value;
            // Update the chart with new symbol
            initializeDashboardChart(selectedSymbol);
        });
        
        function initializeDashboardChart(symbol) {
            // Clear the container
            const container = document.querySelector('.tradingview-widget-container');
            container.innerHTML = '';
            
            // Create a unique container ID
            const containerId = 'tradingview_dashboard_' + Date.now();
            
            // Create new div for the chart
            const chartDiv = document.createElement('div');
            chartDiv.id = containerId;
            chartDiv.style.height = '350px';
            container.appendChild(chartDiv);
            
            // Initialize TradingView widget
            if (window.TradingView && window.TradingView.widget) {
                currentDashboardWidget = new TradingView.widget({
                    autosize: false,
                    symbol: symbol,
                    interval: "1",
                    timezone: "Etc/UTC",
                    theme: "light",
                    style: "1",
                    locale: "en",
                    toolbar_bg: "#ffffff",
                    enable_publishing: false,
                    hide_top_toolbar: false,
                    hide_legend: false,
                    save_image: false,
                    allow_symbol_change: true,
                    calendar: false,
                    support_host: "https://www.tradingview.com",
                    studies: [
                        "Volume@tv-basicstudies"
                    ],
                    show_popup_button: true,
                    popup_width: "1000",
                    popup_height: "650",
                    withdateranges: true,
                    hide_side_toolbar: false,
                    details: true,
                    hotlist: true,
                    calendar: true,
                    width: "100%",
                    height: "350",
                    container_id: containerId
                });
            } else {
                // Fallback: Load the TradingView library and try again
                loadTradingViewLibrary(() => {
                    initializeDashboardChart(symbol);
                });
            }
        }
        
        function loadTradingViewLibrary(callback) {
            if (!document.querySelector('script[src*="tv.js"]')) {
                const script = document.createElement('script');
                script.src = 'https://s3.tradingview.com/tv.js';
                script.onload = callback;
                document.head.appendChild(script);
            }
        }
    });

    // Auto-refresh dashboard data
    setInterval(function() {
        // This would refresh wallet balances and recent transactions
        // via AJAX calls without reloading the page
    }, 60000); // Refresh every minute
</script>
@endpush
