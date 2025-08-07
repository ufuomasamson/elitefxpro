@extends('layouts.dashboard')

@section('title', __('Withdraw Funds'))
@section('page-title', __('Withdraw Funds'))

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-minus-circle text-warning me-2"></i>
                    {{ __('Withdrawal Request') }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('withdrawal.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="crypto" class="form-label">{{ __('Cryptocurrency') }}</label>
                            <select id="crypto" name="crypto" class="form-select" required>
                                <option value="">{{ __('Select cryptocurrency') }}</option>
                                <option value="BTC">Bitcoin (BTC)</option>
                                <option value="ETH">Ethereum (ETH)</option>
                                <option value="ADA">Cardano (ADA)</option>
                                <option value="DOT">Polkadot (DOT)</option>
                                <option value="LTC">Litecoin (LTC)</option>
                                <option value="XRP">Ripple (XRP)</option>
                                <option value="LINK">Chainlink (LINK)</option>
                                <option value="BCH">Bitcoin Cash (BCH)</option>
                                <option value="BNB">Binance Coin (BNB)</option>
                                <option value="DOGE">Dogecoin (DOGE)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">{{ __('Amount') }}</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amount" name="amount" step="0.00000001" min="0" required>
                                <span class="input-group-text">BTC</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">{{ __('Withdrawal Address') }}</label>
                        <input type="text" class="form-control" id="address" name="address" required placeholder="{{ __('Enter your wallet address') }}">
                        <div class="form-text">{{ __('Make sure this address supports the selected cryptocurrency') }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="tag" class="form-label">{{ __('Memo/Tag (Optional)') }}</label>
                        <input type="text" class="form-control" id="tag" name="tag" placeholder="{{ __('Required for some currencies like XRP, XLM') }}">
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>{{ __('Important:') }}</strong> {{ __('Please double-check your withdrawal address. Incorrect addresses may result in permanent loss of funds.') }}
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-minus-circle me-2"></i>
                            {{ __('Submit Withdrawal Request') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Withdrawal Info -->
        <div class="dashboard-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    {{ __('Withdrawal Information') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>{{ __('Processing Time:') }}</strong> 24-48 hours</p>
                    <p><strong>{{ __('Minimum Amount:') }}</strong> 0.001 BTC</p>
                    <p><strong>{{ __('Network Fee:') }}</strong> Variable</p>
                    <p><strong>{{ __('Daily Limit:') }}</strong> 10 BTC</p>
                </div>
            </div>
        </div>

        <!-- Available Balance -->
        <div class="dashboard-card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-wallet text-success me-2"></i>
                    {{ __('Available Balance') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="h4 text-success fw-bold">${{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</div>
                    <div class="text-muted">{{ __('Total Balance') }}</div>
                </div>
                
                <hr>
                
                <div class="small">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('Available:') }}</span>
                        <span class="fw-bold">${{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('Locked:') }}</span>
                        <span class="text-warning fw-bold">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cryptoSelect = document.getElementById('crypto');
        const amountUnit = document.querySelector('.input-group-text');
        
        cryptoSelect.addEventListener('change', function() {
            if (this.value) {
                amountUnit.textContent = this.value;
            } else {
                amountUnit.textContent = 'BTC';
            }
        });
    });
</script>
@endpush
