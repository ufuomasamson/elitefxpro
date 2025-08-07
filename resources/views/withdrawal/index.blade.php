<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Withdrawal
            </h2>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    Available Balance: <span class="font-bold text-green-600">{{ format_currency(Auth::user()->wallet_balance ?? 0) }}</span>
                </div>
                <a href="{{ route('withdrawal.history') }}" class="text-sm text-blue-600 hover:text-blue-800">View History</a>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Withdrawal Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-600 to-red-700 px-8 py-6">
                            <h3 class="text-2xl font-bold text-white flex items-center">
                                <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Withdraw Cryptocurrency
                            </h3>
                            <p class="text-red-100 mt-2">Request a withdrawal to your external wallet</p>
                        </div>

                        <div class="p-8">
                            <form id="withdrawal-form" class="space-y-6">
                                @csrf
                                <!-- Cryptocurrency Selection -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-4">Select Cryptocurrency</label>
                                    @if($userWallets && $userWallets->count() > 0)
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($userWallets as $wallet)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="crypto_type" value="{{ $wallet->currency }}" class="crypto-option sr-only" required>
                                                    <div class="crypto-card border-2 border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-all duration-200">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <h4 class="font-bold text-gray-900">{{ strtoupper($wallet->currency) }}</h4>
                                                                <p class="text-sm text-gray-600">{{ ucfirst($wallet->currency) }}</p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm font-medium text-gray-900">{{ number_format($wallet->balance, 8) }}</p>
                                                                <p class="text-xs text-gray-500">{{ strtoupper($wallet->currency) }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-yellow-800">You don't have any cryptocurrency balances available for withdrawal.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Amount -->
                                <div>
                                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                                    <div class="relative">
                                        <input type="number" id="amount" name="amount" step="0.00000001" min="0.001" placeholder="0.00000000" required
                                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 pr-20">
                                        <div class="absolute inset-y-0 right-0 flex items-center">
                                            <span id="amount-unit" class="text-gray-500 sm:text-sm pr-3">Select crypto</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <p class="text-sm text-gray-600">
                                            Available: <span id="available-balance">0.00000000</span> <span id="balance-unit">-</span>
                                        </p>
                                        <button type="button" onclick="useMaxBalance()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                            Use Max
                                        </button>
                                    </div>
                                </div>

                                <!-- Wallet Address -->
                                <div>
                                    <label for="wallet_address" class="block text-sm font-semibold text-gray-700 mb-2">Destination Wallet Address</label>
                                    <input type="text" id="wallet_address" name="wallet_address" placeholder="Enter your wallet address" required
                                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                                    <p class="text-sm text-gray-500 mt-1">Make sure this address supports the selected cryptocurrency</p>
                                </div>

                                <!-- Terms Confirmation -->
                                <div class="border-t pt-6">
                                    <div class="flex items-start">
                                        <input type="checkbox" id="confirm" class="mt-1 rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                        <label for="confirm" class="ml-3 text-sm text-gray-700">
                                            I confirm that the wallet address is correct and understand that cryptocurrency transactions are irreversible. I also acknowledge that a small network fee may be deducted from my withdrawal.
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="pt-4">
                                    <button type="submit" id="submit-btn" disabled
                                            class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                                        Request Withdrawal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Recent Withdrawals -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b">
                            <h4 class="text-lg font-semibold text-gray-900">Recent Withdrawals</h4>
                        </div>
                        <div class="p-6">
                            @if($recentWithdrawals && $recentWithdrawals->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentWithdrawals as $withdrawal)
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ format_currency($withdrawal->amount) }}</p>
                                                <p class="text-sm text-gray-500">{{ $withdrawal->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($withdrawal->status === 'completed') bg-green-100 text-green-800
                                                @elseif($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($withdrawal->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 pt-4 border-t">
                                    <a href="{{ route('withdrawal.history') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        View All Withdrawals →
                                    </a>
                                </div>
                            @else
                                <p class="text-gray-500 text-center py-8">No recent withdrawals</p>
                            @endif
                        </div>
                    </div>

                    <!-- Important Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-blue-900 mb-4">Important Information</h4>
                        <ul class="text-sm text-blue-800 space-y-2">
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Minimum withdrawal amount is 0.001
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                All withdrawals require admin approval
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Processing time: 1-3 business days
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Double-check wallet addresses before submitting
                            </li>
                        </ul>
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
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';
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
            submitButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 718-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Verifying...';
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
            
            const container = document.querySelector('.max-w-6xl');
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
            
            const container = document.querySelector('.max-w-6xl');
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
