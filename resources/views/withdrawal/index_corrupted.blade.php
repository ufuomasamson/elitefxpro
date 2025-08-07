<x-app-lay                <a href="{{ route('withdrawal.history') }}" class="text-sm text-blue-600 hover:text-blue-800">View History</a>ut>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Withdrawal
            </h2>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    Available Balance: <span class="font-bold text-green-600">${{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                </div>
                <a href="{{ route('withdrawal.            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Verifying...';istory') }}" class="text-sm text-blue-600 hover:text-blue-800">View History</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Withdrawal Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 bg-red-100 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Withdraw Funds</h3>
                                <p class="text-gray-600 text-sm">Send your cryptocurrency to an external wallet</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('withdrawal.store') }}" id="withdrawal-form">
                            @csrf

                        <form method="POST" action="{{ route('withdrawal.store') }}" id="withdrawal-form">
                            @csrf

                            <!-- Cryptocurrency Selection -->
                            <div class="mb-6">
                                <label for="crypto" class="block text-sm font-medium text-gray-700 mb-2">Select Cryptocurrency</label>
                                @if($userWallets->count() > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        @foreach($userWallets as $wallet)
                                        <label class="relative cursor-pointer">
                                            <input type="radio" name="crypto" value="{{ $wallet->currency }}" class="sr-only crypto-option" required>
                                            <div class="crypto-card p-3 border-2 border-gray-200 rounded-lg hover:border-blue-300 transition-colors">
                                                <div class="text-center">
                                                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                        <span class="text-orange-600 font-bold text-xs">{{ substr($wallet->currency, 0, 1) }}</span>
                                                    </div>
                                                    <p class="text-xs font-medium text-gray-800">{{ $wallet->currency }}</p>
                                                    <p class="text-xs text-gray-500">{{ $wallet->currency_name ?? $wallet->currency }}</p>
                                                    <p class="text-xs text-green-600 font-medium mt-1">{{ number_format($wallet->balance, 8) }}</p>
                                                </div>
                                            </div>
                                        </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                        <p class="text-sm">No cryptocurrency wallets with balance found.</p>
                                        <p class="text-xs mt-1">Please make a deposit first to have funds available for withdrawal.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Amount and Address -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                                    <div class="relative">
                                        <input type="number" id="amount" name="amount" step="0.00000001" min="0" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               placeholder="0.00000000" required>
                                        <span id="amount-unit" class="absolute right-3 top-3 text-gray-500 text-sm">BTC</span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                        <span>Available: <span id="available-balance">0.00000000</span> <span id="balance-unit">BTC</span></span>
                                        <button type="button" class="text-blue-600 hover:text-blue-800" onclick="useMaxBalance()">Use Max</button>
                                    </div>
                                </div>

                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Wallet Address</label>
                                    <input type="text" id="address" name="address" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           placeholder="Enter destination wallet address" required>
                                    <p class="mt-1 text-xs text-gray-500">Make sure this address supports the selected cryptocurrency</p>
                                </div>
                            </div>

                            <!-- Memo/Tag (Optional) -->
                            <div class="mb-6">
                                <label for="memo" class="block text-sm font-medium text-gray-700 mb-2">Memo/Tag (Optional)</label>
                                <input type="text" id="memo" name="memo" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Required for some currencies like XRP, XLM">
                                <p class="mt-1 text-xs text-gray-500">Some cryptocurrencies require a memo or destination tag</p>
                            </div>

                            <!-- Fee Information -->
                            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <span class="text-yellow-800 font-medium text-sm">Fee Information</span>
                                </div>
                                <div class="grid grid-cols-2 gap-4 text-xs text-yellow-700">
                                    <div>Network Fee: <span class="font-medium">Variable</span></div>
                                    <div>Processing Fee: <span class="font-medium">0.1%</span></div>
                                    <div>Minimum Amount: <span class="font-medium">0.001 BTC</span></div>
                                    <div>Processing Time: <span class="font-medium">1-24 hours</span></div>
                                </div>
                            </div>

                            <!-- Security Warning -->
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-red-800 font-medium text-sm">Important Security Notice</p>
                                        <p class="text-red-700 text-xs mt-1">Double-check your withdrawal address. Incorrect addresses may result in permanent loss of funds. All withdrawals are final and cannot be reversed.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" id="confirm" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" required>
                                    <label for="confirm" class="ml-2 block text-sm text-gray-700">
                                        I confirm that the withdrawal address is correct
                                    </label>
                                </div>
                                <button type="submit" class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" id="submit-btn" disabled>
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                    Submit Withdrawal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    
                    <!-- Account Balance -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Account Balance</h3>
                        </div>
                        
                        <div class="text-center mb-4">
                            <div class="text-2xl font-bold text-green-600">${{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</div>
                            <div class="text-sm text-gray-500">Available Balance</div>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Balance:</span>
                                <span class="font-medium">${{ number_format(Auth::user()->wallet_balance ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Locked Funds:</span>
                                <span class="font-medium text-orange-600">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Withdrawal Limits -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Withdrawal Limits</h3>
                        </div>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Daily Limit:</span>
                                <span class="font-medium">10 BTC</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Used Today:</span>
                                <span class="font-medium text-orange-600">0 BTC</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Remaining:</span>
                                <span class="font-medium text-green-600">10 BTC</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Times -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Processing Times</h3>
                        </div>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Bitcoin (BTC):</span>
                                <span class="font-medium">1-6 hours</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ethereum (ETH):</span>
                                <span class="font-medium">5-30 mins</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Other coins:</span>
                                <span class="font-medium">5 mins - 2 hours</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Withdrawals -->
                    <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Withdrawals</h3>
                            <a href="{{ route('withdrawal.history') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                        </div>
                        
                        @if(Auth::user()->withdrawals()->latest()->take(3)->count() > 0)
                            <div class="space-y-3">
                                @foreach(Auth::user()->withdrawals()->latest()->take(3)->get() as $withdrawal)
                                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $withdrawal->amount }} {{ $withdrawal->crypto_symbol ?? 'USD' }}</p>
                                        <p class="text-xs text-gray-500">{{ $withdrawal->created_at->format('M j, g:i A') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($withdrawal->status === 'completed') bg-green-100 text-green-800
                                            @elseif($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($withdrawal->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6 text-gray-500">
                                <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-sm">No withdrawals yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Code Modal -->
    <div id="verificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="verificationTitle" class="text-lg font-medium text-gray-900">Verification Required</h3>
                    <button onclick="closeVerificationModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p id="verificationMessage" class="text-sm text-gray-600 mb-4">Please enter your verification code to proceed.</p>
                    
                    <!-- Progress indicator -->
                    <div id="verificationProgress" class="mb-4">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Progress</span>
                            <span id="progressText">Step 1 of 3</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 33%"></div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-700 font-medium">Contact Support</p>
                                <p class="text-sm text-blue-600 mt-1">Please contact our support team to obtain your verification code. They will guide you through the verification process.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form id="verificationForm">
                    @csrf
                    <div class="mb-4">
                        <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Enter your verification code
                        </label>
                        <input type="text" id="verification_code" name="verification_code" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter the code provided by support">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeVerificationModal()" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Verify Code
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentWithdrawalData = null;
        let currentStep = 1;
        let totalSteps = 3;

        document.addEventListener('DOMContentLoaded', function() {
            const cryptoOptions = document.querySelectorAll('.crypto-option');
            const amountUnit = document.getElementById('amount-unit');
            const balanceUnit = document.getElementById('balance-unit');
            const availableBalance = document.getElementById('available-balance');
            const confirmCheckbox = document.getElementById('confirm');
            const submitBtn = document.getElementById('submit-btn');
            const amountInput = document.getElementById('amount');
            
            // Real balance data from backend
            const balances = @json($walletBalances ?? []);
            
            // Handle crypto selection
            cryptoOptions.forEach(option => {
                option.addEventListener('change', function() {
                    if (this.checked) {
                        const symbol = this.value;
                        
                        // Update UI
                        amountUnit.textContent = symbol;
                        balanceUnit.textContent = symbol;
                        availableBalance.textContent = (balances[symbol] || 0).toFixed(8);
                        
                        // Update card styling
                        document.querySelectorAll('.crypto-card').forEach(card => {
                            card.classList.remove('border-blue-500', 'bg-blue-50');
                            card.classList.add('border-gray-200');
                        });
                        
                        this.parentElement.querySelector('.crypto-card').classList.remove('border-gray-200');
                        this.parentElement.querySelector('.crypto-card').classList.add('border-blue-500', 'bg-blue-50');
                    }
                });
            });
            
            // Handle confirmation checkbox
            confirmCheckbox.addEventListener('change', function() {
                submitBtn.disabled = !this.checked;
            });
            
            // Use max balance function
            window.useMaxBalance = function() {
                const selectedCrypto = document.querySelector('.crypto-option:checked');
                if (selectedCrypto) {
                    const symbol = selectedCrypto.value;
                    const balance = parseFloat(balances[symbol] || 0);
                    amountInput.value = balance.toFixed(8);
                }
            };
            
            // Update withdrawal form to use AJAX
            document.getElementById('withdrawal-form').addEventListener('submit', function(e) {
                e.preventDefault();
                submitWithdrawal();
            });
        });

        function submitWithdrawal() {
            const form = document.getElementById('withdrawal-form');
            const formData = new FormData(form);
            
            // Client-side validation
            const selectedCrypto = document.querySelector('.crypto-option:checked');
            const amount = parseFloat(formData.get('amount'));
            const balances = @json($walletBalances ?? []);
            
            if (!selectedCrypto) {
                showErrorMessage('Please select a cryptocurrency');
                return;
            }
            
            const symbol = selectedCrypto.value;
            const availableAmount = parseFloat(balances[symbol] || 0);
            
            if (amount > availableAmount) {
                showErrorMessage(`Insufficient ${symbol} balance. Available: ${availableAmount.toFixed(8)} ${symbol}`);
                return;
            }
            
            if (amount < 0.001) {
                showErrorMessage('Minimum withdrawal amount is 0.001');
                return;
            }
            
            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
            submitButton.disabled = true;

            fetch('/withdrawal', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    crypto_type: formData.get('crypto_type'),
                    amount: formData.get('amount'),
                    wallet_address: formData.get('wallet_address')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.requires_verification) {
                    // Store withdrawal data for verification
                    currentWithdrawalData = {
                        crypto_type: formData.get('crypto_type'),
                        amount: formData.get('amount'),
                        wallet_address: formData.get('wallet_address')
                    };
                    currentStep = data.current_step || 1;
                    totalSteps = data.total_steps || 3;
                    showVerificationModal(data.verification_title, data.verification_message);
                } else if (data.success) {
                    showSuccessMessage(data.message);
                    form.reset();
                    setTimeout(() => location.reload(), 1500); // Refresh balances
                } else {
                    showErrorMessage(data.message || 'An error occurred while processing your withdrawal.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('An error occurred while processing your withdrawal.');
            })
            .finally(() => {
                // Restore button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        }

        function showVerificationModal(title, message) {
            document.getElementById('verificationTitle').textContent = title;
            document.getElementById('verificationMessage').textContent = message;
            document.getElementById('progressText').textContent = `Step ${currentStep} of ${totalSteps}`;
            
            // Update progress bar
            const progressPercentage = (currentStep / totalSteps) * 100;
            document.getElementById('progressBar').style.width = progressPercentage + '%';
            
            // Clear previous input
            document.getElementById('verification_code').value = '';
            
            document.getElementById('verificationModal').classList.remove('hidden');
            document.getElementById('verification_code').focus();
        }

        function closeVerificationModal() {
            document.getElementById('verificationModal').classList.add('hidden');
            currentWithdrawalData = null;
            currentStep = 1;
        }

        // Handle verification form submission
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = document.getElementById('verification_code').value;
            if (!code || !currentWithdrawalData) {
                showErrorMessage('Please enter a verification code.');
                return;
            }

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Verifying...';
            submitButton.disabled = true;

            fetch('/withdrawal/verify-code', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    code: code,
                    withdrawal_data: currentWithdrawalData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.completed) {
                        // Verification complete, withdrawal processed
                        closeVerificationModal();
                        showSuccessMessage(data.message);
                        document.getElementById('withdrawal-form').reset();
                        setTimeout(() => location.reload(), 1500); // Refresh balances
                    } else {
                        // Move to next verification step
                        currentStep = data.current_step;
                        showVerificationModal(data.verification_title, data.verification_message);
                    }
                } else {
                    showErrorMessage(data.message || 'Invalid verification code. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage('An error occurred during verification.');
            })
            .finally(() => {
                // Restore button state
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });

        function showSuccessMessage(message) {
            // Remove any existing alerts
            const existingAlert = document.querySelector('.alert-success');
            if (existingAlert) existingAlert.remove();

            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
            alertDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function showErrorMessage(message) {
            // Remove any existing alerts
            const existingAlert = document.querySelector('.alert-error');
            if (existingAlert) existingAlert.remove();

            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
            alertDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Close modal when clicking outside
        document.getElementById('verificationModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeVerificationModal();
            }
        });
    </script>

    <style>
        .crypto-card {
            transition: all 0.2s ease;
        }
        
        .crypto-card:hover {
            transform: translateY(-1px);
            shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .crypto-option:checked + .crypto-card {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }
    </style>
</x-app-layout>
