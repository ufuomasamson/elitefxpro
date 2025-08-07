@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-wallet text-green-600 mr-3"></i>
            {{ __('My Wallet') }}
        </h2>
        <p class="text-gray-600 mt-2">{{ __('Manage your cryptocurrency portfolio') }}</p>
    </div>
<!-- Portfolio Summary -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="dashboard-card stat-card">
            <i class="fas fa-wallet metric-card-icon text-primary"></i>
            <div class="stat-value text-primary">${{ number_format($totalPortfolioValue, 2) }}</div>
            <div class="stat-label">{{ __('Total Portfolio Value') }}</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="dashboard-card stat-card">
            <i class="fas fa-coins metric-card-icon text-success"></i>
            <div class="stat-value text-success">{{ $wallets->count() }}</div>
            <div class="stat-label">{{ __('Active Wallets') }}</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="dashboard-card stat-card">
            <i class="fas fa-exchange-alt metric-card-icon text-info"></i>
            <div class="stat-value text-info">{{ $recentTransactions->count() }}</div>
            <div class="stat-label">{{ __('Recent Transactions') }}</div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Crypto Wallets -->
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-wallet text-primary me-2"></i>
                    {{ __('Your Crypto Wallets') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($wallets as $wallet)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="dashboard-card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        @switch($wallet->currency)
                                            @case('BTC')
                                                <i class="fab fa-bitcoin text-warning" style="font-size: 2rem;"></i>
                                                @break
                                            @case('ETH')
                                                <i class="fab fa-ethereum text-primary" style="font-size: 2rem;"></i>
                                                @break
                                            @case('USDT')
                                                <i class="fas fa-dollar-sign text-success" style="font-size: 2rem;"></i>
                                                @break
                                            @default
                                                <i class="fas fa-coins text-info" style="font-size: 2rem;"></i>
                                        @endswitch
                                    </div>
                                    <h6 class="fw-bold">{{ $wallet->currency }}</h6>
                                    <div class="fw-bold text-primary mb-2">{{ number_format($wallet->balance, 8) }}</div>
                                    @if($wallet->locked_balance > 0)
                                        <div class="small text-warning mb-2">
                                            {{ __('Locked') }}: {{ number_format($wallet->locked_balance, 8) }}
                                        </div>
                                    @endif
                                    <div class="small text-muted">
                                        ≈ ${{ number_format($wallet->balance * ($conversionRates[$wallet->currency] ?? 1), 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center p-5">
                                <i class="fas fa-wallet text-muted" style="font-size: 4rem;"></i>
                                <h4 class="mt-3 text-muted">{{ __('No Wallets Yet') }}</h4>
                                <p class="text-muted">{{ __('Start by making a deposit to create your first wallet') }}</p>
                                <a href="{{ route('deposit.index') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>{{ __('Make First Deposit') }}
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="col-lg-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history text-info me-2"></i>
                    {{ __('Recent Transactions') }}
                </h5>
            </div>
            <div class="card-body p-0">
                @forelse($recentTransactions as $transaction)
                    <div class="transaction-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">
                                    @switch($transaction->type)
                                        @case('fund')
                                            <span class="text-success">{{ __('Admin Fund') }}</span>
                                            @break
                                        @case('deposit')
                                            <span class="text-primary">{{ __('Deposit') }}</span>
                                            @break
                                        @case('withdrawal')
                                            <span class="text-warning">{{ __('Withdrawal') }}</span>
                                            @break
                                        @case('trade')
                                            <span class="text-info">{{ __('Trade') }}</span>
                                            @break
                                        @default
                                            <span class="text-secondary">{{ ucfirst($transaction->type) }}</span>
                                    @endswitch
                                </div>
                                <div class="small text-muted">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </div>
                                @if($transaction->metadata && isset($transaction->metadata['crypto_symbol']))
                                    <div class="small text-muted">
                                        {{ $transaction->metadata['crypto_symbol'] }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">
                                    @if(in_array($transaction->type, ['deposit', 'fund']))
                                        <span class="text-success">+${{ number_format($transaction->amount, 2) }}</span>
                                    @else
                                        <span class="text-danger">-${{ number_format($transaction->amount, 2) }}</span>
                                    @endif
                                </div>
                                <span class="status-badge status-{{ $transaction->status }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center p-4">
                        <i class="fas fa-history text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">{{ __('No transactions yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
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
                        <a href="{{ route('trade.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center">
                            <i class="fas fa-exchange-alt mb-2" style="font-size: 2rem;"></i>
                            <span>{{ __('Trade Crypto') }}</span>
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
    // Auto-refresh wallet balances every 30 seconds
    setInterval(function() {
        // This would refresh wallet data via AJAX
        console.log('Refreshing wallet data...');
    }, 30000);
</script>
@endpush
