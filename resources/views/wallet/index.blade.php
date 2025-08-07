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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-blue-600">{{ format_currency($totalPortfolioValue ?? 0) }}</div>
                    <div class="text-sm text-gray-600">{{ __('Total Portfolio Value') }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg mr-4">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600">{{ ($wallets->count() ?? 0) }}</div>
                    <div class="text-sm text-gray-600">{{ __('Active Wallets') }}</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-600">{{ ($recentTransactions->count() ?? 0) }}</div>
                    <div class="text-sm text-gray-600">{{ __('Recent Transactions') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Crypto Wallets -->
        <div class="lg:col-span-2">
            <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-wallet text-green-600 mr-2"></i>
                        {{ __('Your Crypto Wallets') }}
                    </h3>
                </div>
                <div class="p-6">
                    @forelse(($wallets ?? []) as $wallet)
                        <div class="mb-4 last:mb-0">
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @switch($wallet->currency)
                                                @case('BTC')
                                                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                                        <i class="fab fa-bitcoin text-orange-600 text-xl"></i>
                                                    </div>
                                                    @break
                                                @case('ETH')
                                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <i class="fab fa-ethereum text-blue-600 text-xl"></i>
                                                    </div>
                                                    @break
                                                @case('USDT')
                                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                                        <span class="text-green-600 font-bold text-xl">₮</span>
                                                    </div>
                                                    @break
                                                @default
                                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                                                        <span class="text-gray-600 font-bold text-xl">{{ substr($wallet->currency, 0, 1) }}</span>
                                                    </div>
                                            @endswitch
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $wallet->currency_name ?? $wallet->currency }}</h4>
                                            <p class="text-sm text-gray-600">{{ $wallet->currency }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900">{{ number_format($wallet->balance ?? 0, 8) }}</div>
                                        <div class="text-sm text-gray-600">{{ format_currency($wallet->balance_usd ?? 0) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-wallet text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No Wallets Yet') }}</h3>
                            <p class="text-gray-600 mb-4">{{ __('Start trading to create your first crypto wallet') }}</p>
                            <a href="{{ route('trade.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                {{ __('Start Trading') }}
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="lg:col-span-1">
            <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-history text-blue-600 mr-2"></i>
                        {{ __('Recent Transactions') }}
                    </h3>
                </div>
                <div class="p-6">
                    @forelse(($recentTransactions ?? []) as $transaction)
                        <div class="mb-4 last:mb-0">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($transaction->type === 'deposit')
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-arrow-down text-green-600 text-sm"></i>
                                            </div>
                                        @elseif($transaction->type === 'withdrawal')
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-arrow-up text-red-600 text-sm"></i>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-exchange-alt text-blue-600 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ ucfirst($transaction->type) }}</div>
                                        <div class="text-xs text-gray-600">{{ $transaction->created_at->format('M j, g:i A') }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium {{ $transaction->type === 'deposit' ? 'text-green-600' : ($transaction->type === 'withdrawal' ? 'text-red-600' : 'text-blue-600') }}">
                                        {{ $transaction->type === 'withdrawal' ? '-' : '+' }}{{ format_currency($transaction->amount ?? 0) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6">
                            <div class="text-gray-400 mb-2">
                                <i class="fas fa-history text-2xl"></i>
                            </div>
                            <p class="text-sm text-gray-600">{{ __('No recent transactions') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                    {{ __('Quick Actions') }}
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('trade.index') }}" class="flex items-center justify-center w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        {{ __('Trade Now') }}
                    </a>
                    <a href="{{ route('deposit.index') }}" class="flex items-center justify-center w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('Deposit Funds') }}
                    </a>
                    <a href="{{ route('withdrawal.index') }}" class="flex items-center justify-center w-full px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-minus mr-2"></i>
                        {{ __('Withdraw Funds') }}
                    </a>
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
