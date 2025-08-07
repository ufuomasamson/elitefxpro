@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-wallet text-green-600 mr-3"></i>
            {{ __('Deposit Funds') }}
        </h2>
        <p class="text-gray-600 mt-2">{{ __('Add funds to your trading account') }}</p>
    </div>

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Deposit Funds</h1>
                    <p class="text-gray-500 mt-2">Choose your preferred deposit method</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Current Balance</p>
                    <p class="text-2xl font-bold text-green-600">{{ format_currency(auth()->user()->wallet_balance ?? 0) }}</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <strong class="font-bold">Validation Errors:</strong>
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Deposit Options -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Crypto Wallets Option -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300 cursor-pointer" onclick="openCryptoModal()">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-orange-100 mb-6">
                        <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Crypto Wallets</h3>
                    <p class="text-gray-600 mb-4">Deposit using cryptocurrency wallets</p>
                    <div class="flex flex-wrap justify-center gap-2 mb-6">
                        @if($cryptoWallets->count() > 0)
                            @foreach($cryptoWallets->take(6) as $wallet)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $wallet->currency }}
                                </span>
                            @endforeach
                            @if($cryptoWallets->count() > 6)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    +{{ $cryptoWallets->count() - 6 }} more
                                </span>
                            @endif
                        @else
                            <span class="text-sm text-gray-500">No crypto wallets available</span>
                        @endif
                    </div>
                    <button class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                        Choose Crypto Wallet
                    </button>
                </div>
            </div>

            <!-- Bank Transfer Option -->
            <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300 cursor-pointer" onclick="openBankModal()">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 mb-6">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Bank Transfer</h3>
                    <p class="text-gray-600 mb-4">Deposit via traditional bank transfer</p>
                    <div class="mb-6">
                        @if($bankDetails)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                ✓ Bank Details Available
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                ✗ Bank Details Not Available
                            </span>
                        @endif
                    </div>
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200" 
                            {{ !$bankDetails ? 'disabled' : '' }}>
                        @if($bankDetails)
                            View Bank Details
                        @else
                            Bank Transfer Unavailable
                        @endif
                    </button>
                </div>
            </div>
        </div>

        <!-- Deposit Instructions -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Important Notes</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>All deposits are subject to verification and may take 24-48 hours to process</li>
                            <li>Please ensure you upload proof of payment for faster processing</li>
                            <li>Minimum deposit amount is {{ format_currency(0.01) }}</li>
                            <li>Contact support if you encounter any issues</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Crypto Wallets Modal -->
<div id="cryptoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-medium text-gray-900">Select Cryptocurrency</h3>
                <button onclick="closeCryptoModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            @if($cryptoWallets->count() > 0)
                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    @foreach($cryptoWallets as $wallet)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-orange-500 hover:bg-orange-50 cursor-pointer transition-colors" 
                             onclick="selectCryptoWallet('{{ $wallet->currency }}', '{{ $wallet->currency_name }}', '{{ $wallet->wallet_address }}', '{{ $wallet->qr_code_image }}')">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                        <span class="text-orange-600 font-semibold text-sm">{{ $wallet->currency }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $wallet->currency_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $wallet->currency }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-gray-500">No cryptocurrency wallets are currently available</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Crypto Wallet Details Modal -->
<div id="walletDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1001;">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-medium text-gray-900">Deposit to <span id="selectedCurrency"></span></h3>
                <button onclick="closeWalletDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- QR Code -->
                <div class="text-center">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">QR Code</h4>
                    <div id="qrCodeContainer" class="mb-4">
                        <!-- QR code will be inserted here -->
                    </div>
                    <p class="text-sm text-gray-600">Scan with your wallet app</p>
                </div>
                
                <!-- Wallet Address -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Wallet Address</h4>
                    <div class="mb-4">
                        <div class="bg-gray-50 p-3 rounded border">
                            <p id="walletAddress" class="text-sm font-mono break-all"></p>
                        </div>
                        <button onclick="copyAddress()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            📋 Copy Address
                        </button>
                    </div>
                    
                    <!-- Deposit Form -->
                    <form id="cryptoDepositForm" method="POST" action="{{ route('deposit.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="deposit_method" value="crypto">
                        <input type="hidden" id="cryptoSymbol" name="crypto_symbol" value="">
                        
                        <div class="space-y-4">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                            </div>
                            
                            <div>
                                <label for="proof_image" class="block text-sm font-medium text-gray-700">Proof of Payment (Optional)</label>
                                <input type="file" id="proof_image" name="proof_image" accept="image/*"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500">
                                <p class="text-xs text-gray-500 mt-1">Upload a screenshot of your transaction</p>
                            </div>
                            
                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Submit Deposit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bank Transfer Modal -->
<div id="bankModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-medium text-gray-900">Bank Transfer Details</h3>
                <button onclick="closeBankModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            @if($bankDetails)
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Bank Details -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Bank Account Information</h4>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-4">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ $bankDetails->bank_details }}</pre>
                        </div>
                        <button onclick="copyBankDetails()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            📋 Copy Bank Details
                        </button>
                    </div>
                    
                    <!-- Deposit Form -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Submit Deposit</h4>
                        <form id="bankDepositForm" method="POST" action="{{ route('deposit.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="deposit_method" value="bank_transfer">
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="bank_amount" class="block text-sm font-medium text-gray-700">Amount Transferred</label>
                                    <input type="number" id="bank_amount" name="amount" step="0.01" min="0.01" required 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <div>
                                    <label for="bank_proof_image" class="block text-sm font-medium text-gray-700">Bank Receipt/Proof (Required)</label>
                                    <input type="file" id="bank_proof_image" name="proof_image" accept="image/*" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Upload bank transfer receipt or screenshot</p>
                                </div>
                                
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                    Submit Bank Transfer Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <p class="text-gray-500">Bank transfer is not currently available</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function openCryptoModal() {
    document.getElementById('cryptoModal').classList.remove('hidden');
}

function closeCryptoModal() {
    document.getElementById('cryptoModal').classList.add('hidden');
}

function selectCryptoWallet(currency, currencyName, walletAddress, qrCodeImage) {
    document.getElementById('selectedCurrency').textContent = `${currency} (${currencyName})`;
    document.getElementById('walletAddress').textContent = walletAddress;
    document.getElementById('cryptoSymbol').value = currency;
    
    // Update QR code
    const qrContainer = document.getElementById('qrCodeContainer');
    if (qrCodeImage) {
        qrContainer.innerHTML = `<img src="/storage/${qrCodeImage}" alt="QR Code for ${currency}" class="mx-auto max-w-full h-48 w-48 border rounded">`;
    } else {
        qrContainer.innerHTML = `<div class="mx-auto h-48 w-48 bg-gray-100 border rounded flex items-center justify-center"><span class="text-gray-500">No QR Code</span></div>`;
    }
    
    closeCryptoModal();
    document.getElementById('walletDetailsModal').classList.remove('hidden');
}

function closeWalletDetailsModal() {
    document.getElementById('walletDetailsModal').classList.add('hidden');
}

function openBankModal() {
    @if($bankDetails)
        document.getElementById('bankModal').classList.remove('hidden');
    @else
        alert('Bank transfer is not currently available. Please contact support.');
    @endif
}

function closeBankModal() {
    document.getElementById('bankModal').classList.add('hidden');
}

function copyAddress() {
    const address = document.getElementById('walletAddress').textContent;
    navigator.clipboard.writeText(address).then(function() {
        alert('Wallet address copied to clipboard!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

function copyBankDetails() {
    const bankDetails = @json($bankDetails ? $bankDetails->bank_details : '');
    if (bankDetails) {
        navigator.clipboard.writeText(bankDetails).then(function() {
            alert('Bank details copied to clipboard!');
        }).catch(function(err) {
            console.error('Could not copy text: ', err);
        });
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const cryptoModal = document.getElementById('cryptoModal');
    const walletDetailsModal = document.getElementById('walletDetailsModal');
    const bankModal = document.getElementById('bankModal');
    
    if (event.target === cryptoModal) {
        closeCryptoModal();
    }
    if (event.target === walletDetailsModal) {
        closeWalletDetailsModal();
    }
    if (event.target === bankModal) {
        closeBankModal();
    }
}
</script>
</div>
@endsection
