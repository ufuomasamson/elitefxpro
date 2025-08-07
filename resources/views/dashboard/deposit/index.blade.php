@extends('layouts.dashboard')

@section('title', __('Deposit Funds'))
@section('page-title', __('Deposit Funds'))

@section('content')
<div class="container-fluid">
    <!-- Deposit Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ __('Deposit Funds') }}</h1>
        <a href="{{ route('deposit.history') }}" class="btn btn-outline-primary">
            <i class="fas fa-history me-1"></i> {{ __('Deposit History') }}
        </a>
    </div>

    <!-- Instructions Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('How to Deposit') }}</h5>
            <p class="card-text">{{ __('Follow these steps to deposit funds into your Trade Trust account:') }}</p>
            <ol>
                <li>{{ __('Select the cryptocurrency you wish to deposit.') }}</li>
                <li>{{ __('Enter the amount you want to deposit.') }}</li>
                <li>{{ __('Make a payment to our company account using your preferred payment method.') }}</li>
                <li>{{ __('Upload the proof of payment (receipt or screenshot).') }}</li>
                <li>{{ __('Submit your deposit request for admin approval.') }}</li>
            </ol>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('Once approved by our team, your funds will be credited to your account. This process usually takes 1-2 business days.') }}
            </div>
        </div>
    </div>

    <!-- Deposit Form -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">{{ __('Manual Deposit Form') }}</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('deposit.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="crypto_symbol" class="form-label">{{ __('Select Cryptocurrency') }}</label>
                            <select class="form-select" id="crypto_symbol" name="crypto_symbol" required>
                                <option value="BTC" @if(request()->get('crypto') == 'BTC') selected @endif>Bitcoin (BTC)</option>
                                <option value="ETH" @if(request()->get('crypto') == 'ETH') selected @endif>Ethereum (ETH)</option>
                                <option value="USDT" @if(request()->get('crypto') == 'USDT') selected @endif>Tether USD (USDT)</option>
                                <option value="BNB" @if(request()->get('crypto') == 'BNB') selected @endif>Binance Coin (BNB)</option>
                                <option value="XRP" @if(request()->get('crypto') == 'XRP') selected @endif>Ripple (XRP)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ __('Amount') }}</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amount" name="amount" min="10" step="0.01" required placeholder="0.00">
                                <span class="input-group-text crypto-symbol">BTC</span>
                            </div>
                            <div class="form-text">{{ __('Minimum deposit amount is 10 USD equivalent') }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="proof_file" class="form-label">{{ __('Proof of Payment') }}</label>
                            <input class="form-control" type="file" id="proof_file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">{{ __('Upload receipt or screenshot of your payment (JPG, PNG, PDF only, max 5MB)') }}</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Submit Deposit Request') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Deposit Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">{{ __('Payment Details') }}</h5>
                </div>
                <div class="card-body">
                    <div id="btc-payment-details" class="payment-details">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fab fa-bitcoin fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Bitcoin (BTC)</h6>
                                <p class="text-muted mb-0">{{ __('Bitcoin Network') }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('BTC Wallet Address') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh" readonly>
                                <button class="btn btn-outline-secondary copy-btn" type="button" data-clipboard-text="bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <img src="{{ asset('img/qr/btc-qr.png') }}" alt="BTC QR Code" class="img-fluid mb-3" style="max-width: 180px;">
                        </div>
                    </div>

                    <div id="eth-payment-details" class="payment-details d-none">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fab fa-ethereum fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Ethereum (ETH)</h6>
                                <p class="text-muted mb-0">{{ __('ERC-20 Network') }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('ETH Wallet Address') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="0x1234567890abcdef1234567890abcdef12345678" readonly>
                                <button class="btn btn-outline-secondary copy-btn" type="button" data-clipboard-text="0x1234567890abcdef1234567890abcdef12345678">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <img src="{{ asset('img/qr/eth-qr.png') }}" alt="ETH QR Code" class="img-fluid mb-3" style="max-width: 180px;">
                        </div>
                    </div>

                    <div id="usdt-payment-details" class="payment-details d-none">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="fas fa-dollar-sign fa-2x text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Tether USD (USDT)</h6>
                                <p class="text-muted mb-0">{{ __('TRC-20 Network') }}</p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('USDT Wallet Address') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="TJDENsfBJs4RFzRRsULn2PQKfNaHJQpnca" readonly>
                                <button class="btn btn-outline-secondary copy-btn" type="button" data-clipboard-text="TJDENsfBJs4RFzRRsULn2PQKfNaHJQpnca">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-center">
                            <img src="{{ asset('img/qr/usdt-qr.png') }}" alt="USDT QR Code" class="img-fluid mb-3" style="max-width: 180px;">
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ __('Please ensure you send only the selected cryptocurrency to its corresponding address. Sending the wrong cryptocurrency may result in permanent loss of funds.') }}
                    </div>
                </div>
            </div>

            <!-- Pending Deposits -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">{{ __('Pending Deposits') }}</h5>
                </div>
                <div class="card-body p-0">
                    @if($pendingDeposits->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($pendingDeposits as $deposit)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 fw-medium">{{ $deposit->amount }} {{ $deposit->crypto_symbol }}</p>
                                        <small class="text-muted">{{ $deposit->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-2x text-muted mb-2"></i>
                            <p class="mb-0">{{ __('No pending deposits') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize clipboard.js
        new ClipboardJS('.copy-btn').on('success', function(e) {
            // Add feedback for copy
            const button = e.trigger;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            
            setTimeout(function() {
                button.innerHTML = originalText;
            }, 2000);
            
            e.clearSelection();
        });
        
        // Switch payment details based on selected cryptocurrency
        const cryptoSelect = document.getElementById('crypto_symbol');
        const cryptoSymbolElements = document.querySelectorAll('.crypto-symbol');
        
        function updatePaymentDetails() {
            const selectedCrypto = cryptoSelect.value;
            
            // Update the symbol in the form
            cryptoSymbolElements.forEach(el => {
                el.textContent = selectedCrypto;
            });
            
            // Show the appropriate payment details
            document.querySelectorAll('.payment-details').forEach(el => {
                el.classList.add('d-none');
            });
            
            const paymentDetailsElement = document.getElementById(selectedCrypto.toLowerCase() + '-payment-details');
            if (paymentDetailsElement) {
                paymentDetailsElement.classList.remove('d-none');
            } else {
                document.getElementById('btc-payment-details').classList.remove('d-none');
            }
        }
        
        // Initial update
        updatePaymentDetails();
        
        // Update when selection changes
        cryptoSelect.addEventListener('change', updatePaymentDetails);
    });
</script>
@endsection
