@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-money-bill-wave text-red-600 mr-3"></i>
            {{ __('messages.withdraw_funds') }}
        </h2>
        <p class="text-gray-600 mt-2">{{ __('Withdraw funds from your trading account') }}</p>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Instructions Card -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl shadow-lg p-6 mb-8">
                <div class="flex items-center mb-4">
                    <div class="p-3 bg-purple-100 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">How to Withdraw</h3>
                </div>
                <p class="text-gray-600 mb-4">Follow these steps to withdraw funds from your Elite Forex Pro account:</p>
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>Select the cryptocurrency you wish to withdraw</li>
                    <li>Enter the amount you want to withdraw</li>
                    <li>Provide your wallet address for the selected cryptocurrency</li>
                    <li>Review the withdrawal fee and net amount</li>
                    <li>Submit your withdrawal request for admin approval</li>
                </ol>
                <div class="mt-4 p-4 bg-purple-100 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-purple-800 text-sm font-medium">Processing Time: 24-48 hours after approval</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Withdrawal Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Withdrawal Form</h3>
                        
                        <form id="withdrawal-form" class="space-y-6" x-data="withdrawalForm()">
                            @csrf
                            
                            <!-- Cryptocurrency Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Cryptocurrency</label>
                                @if($availableWallets->count() > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($availableWallets as $wallet)
                                            <label class="relative cursor-pointer">
                                                <input type="radio" name="crypto_type" value="{{ $wallet->currency }}" class="sr-only peer" required x-model="selectedCrypto">
                                                <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all">
                                                    <div class="text-center">
                                                        <div class="w-8 h-8 
                                                            @if($wallet->currency == 'BTC') bg-orange-100 @endif
                                                            @if($wallet->currency == 'ETH') bg-blue-100 @endif
                                                            @if($wallet->currency == 'USDT') bg-green-100 @endif
                                                            @if($wallet->currency == 'BNB') bg-yellow-100 @endif
                                                            @if($wallet->currency == 'ADA') bg-purple-100 @endif
                                                            @if($wallet->currency == 'DOT') bg-pink-100 @endif
                                                            @if($wallet->currency == 'XRP') bg-indigo-100 @endif
                                                            @if($wallet->currency == 'BCH') bg-orange-100 @endif
                                                            @if($wallet->currency == 'LTC') bg-gray-100 @endif
                                                            rounded-full flex items-center justify-center mx-auto mb-2">
                                                            <span class="
                                                                @if($wallet->currency == 'BTC') text-orange-600 @endif
                                                                @if($wallet->currency == 'ETH') text-blue-600 @endif
                                                                @if($wallet->currency == 'USDT') text-green-600 @endif
                                                                @if($wallet->currency == 'BNB') text-yellow-600 @endif
                                                                @if($wallet->currency == 'ADA') text-purple-600 @endif
                                                                @if($wallet->currency == 'DOT') text-pink-600 @endif
                                                                @if($wallet->currency == 'XRP') text-indigo-600 @endif
                                                                @if($wallet->currency == 'BCH') text-orange-600 @endif
                                                                @if($wallet->currency == 'LTC') text-gray-600 @endif
                                                                font-bold text-xs">
                                                                @if($wallet->currency == 'BTC') ₿
                                                                @elseif($wallet->currency == 'ETH') Ξ
                                                                @elseif($wallet->currency == 'USDT') ₮
                                                                @elseif($wallet->currency == 'BNB') B
                                                                @else {{ substr($wallet->currency, 0, 1) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <span class="text-sm font-medium">{{ $wallet->currency }}</span>
                                                        <p class="text-xs text-gray-500">Available: {{ number_format($wallet->available_balance, 8) }}</p>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <p class="text-gray-500 font-medium">No crypto assets available for withdrawal</p>
                                        <p class="text-sm text-gray-400 mt-1">You need to have a positive balance in a cryptocurrency wallet to make withdrawals</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (USD)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" step="0.01" min="10" 
                                           class="block w-full pl-7 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" 
                                           placeholder="0.00" required x-model="amount" @input="calculateFee()">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">USD</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Minimum withdrawal: $10.00</p>
                            </div>

                            <!-- Wallet Address -->
                            <div>
                                <label for="wallet_address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Recipient Wallet Address
                                    <span x-show="selectedCrypto" x-text="'(' + selectedCrypto + ')'"></span>
                                </label>
                                <input type="text" name="wallet_address" id="wallet_address" 
                                       class="block w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500" 
                                       placeholder="Enter wallet address" required>
                                <p class="mt-1 text-xs text-red-500">⚠️ Please double-check the address. Transactions cannot be reversed!</p>
                            </div>

                            <!-- Fee Calculation -->
                            <div class="bg-gray-50 rounded-lg p-4" x-show="amount > 0">
                                <h4 class="font-medium text-gray-800 mb-3">Withdrawal Summary</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Withdrawal Amount:</span>
                                        <span class="font-medium" x-text="'$' + parseFloat(amount || 0).toFixed(2)"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Processing Fee (2%):</span>
                                        <span class="font-medium text-red-600" x-text="'$' + fee.toFixed(2)"></span>
                                    </div>
                                    <div class="border-t pt-2 flex justify-between font-semibold">
                                        <span class="text-gray-800">Net Amount:</span>
                                        <span class="text-green-600" x-text="'$' + netAmount.toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4">
                                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 px-6 rounded-lg font-medium hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-[1.02] shadow-lg">
                                    Submit Withdrawal Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Withdrawal Info & Pending -->
                <div class="space-y-6">
                    <!-- Withdrawal Fees -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Withdrawal Fees</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Processing Fee</span>
                                <span class="font-medium text-gray-800">2%</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Minimum Withdrawal</span>
                                <span class="font-medium text-gray-800">$10.00</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Processing Time</span>
                                <span class="font-medium text-gray-800">24-48 hours</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Notice -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="font-medium text-red-800">Security Notice</span>
                        </div>
                        <ul class="text-sm text-red-700 space-y-1">
                            <li>• Always verify wallet addresses before submitting</li>
                            <li>• Transactions cannot be reversed once processed</li>
                            <li>• Contact support if you need assistance</li>
                        </ul>
                    </div>

                    <!-- Pending Withdrawals -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pending Withdrawals</h3>
                        <div class="space-y-3">
                            @forelse($withdrawals->where('status', 'pending') as $withdrawal)
                                <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ number_format($withdrawal->amount, 8) }} {{ $withdrawal->crypto_symbol }}</p>
                                            <p class="text-sm text-gray-600">{{ $withdrawal->created_at->format('M d, Y H:i') }}</p>
                                            <p class="text-xs text-gray-500">Ref: {{ $withdrawal->reference }}</p>
                                        </div>
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">{{ ucfirst($withdrawal->status) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                                    </svg>
                                    <p class="text-sm">No pending withdrawals</p>
                                </div>
                            @endforelse
                        </div>
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

        // Simple console logger to replace the undefined AppLogger
        const Logger = {
            logs: [],
            log: function(level, message, data = null) {
                const entry = {
                    timestamp: new Date().toISOString(),
                    level: level,
                    message: message,
                    data: data
                };
                this.logs.push(entry);
                console.log(`[${level.toUpperCase()}] ${message}`, data || '');
            },
            info: function(message, data) { this.log('info', message, data); },
            error: function(message, data) { this.log('error', message, data); },
            getAll: function() { return this.logs; },
            clear: function() { this.logs = []; }
        };

        function withdrawalForm() {
            return {
                selectedCrypto: '',
                amount: 0,
                fee: 0,
                netAmount: 0,
                
                calculateFee() {
                    this.fee = this.amount * 0.02; // 2% fee
                    this.netAmount = this.amount - this.fee;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            Logger.info('🚀 Withdrawal page JavaScript loaded');
            
            // Handle withdrawal form submission
            const withdrawalForm = document.getElementById('withdrawal-form');
            Logger.info('🔍 Withdrawal form element found', !!withdrawalForm);
            
            if (withdrawalForm) {
                Logger.info('✅ Found withdrawal form, attaching event listener');
                
                withdrawalForm.addEventListener('submit', function(e) {
                    Logger.info('📝 Form submission triggered');
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    Logger.info('📦 Form data', {
                        crypto_type: formData.get('crypto_type'),
                        amount: formData.get('amount'),
                        wallet_address: formData.get('wallet_address')
                    });
                    
                    // Show loading state
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...</span>';
                    submitButton.disabled = true;
                    
                    Logger.info('🌐 Making AJAX request to withdrawal submit endpoint');
                    
                    // Submit via AJAX to check verification
                    fetch('{{ route("withdrawal.submit") }}', {
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
                    .then(response => {
                        Logger.info('📡 Response received', { status: response.status, ok: response.ok });
                        return response.json();
                    })
                    .then(data => {
                        Logger.info('📋 Response data', data);
                        
                        if (data.requires_verification) {
                            Logger.info('🔐 Verification required! Showing modal...');
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
                            Logger.info('✅ Success!');
                            showSuccessMessage(data.message || 'Withdrawal request submitted successfully!');
                            this.reset();
                            setTimeout(() => location.reload(), 1500); // Refresh balances
                        } else {
                            Logger.error('❌ Error:', data.message);
                            showErrorMessage(data.message || 'An error occurred while processing your withdrawal.');
                        }
                    })
                    .catch(error => {
                        Logger.error('💥 JavaScript Error:', error);
                        showErrorMessage('An error occurred while processing your withdrawal.');
                    })
                    .finally(() => {
                        // Restore button state
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    });
                });
            } else {
                Logger.error('❌ Withdrawal form not found!');
            }

            // Handle verification form submission
            const verificationForm = document.getElementById('verificationForm');
            if (verificationForm) {
                verificationForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const code = document.getElementById('verification_code').value;
                    if (!code || !currentWithdrawalData) {
                        showErrorMessage('Please enter a verification code.');
                        return;
                    }

                    // Show loading state
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = 'Verifying...';
                    submitButton.disabled = true;

                    fetch('{{ route("withdrawal.verify-code") }}', {
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
                                showSuccessMessage(data.message || 'Withdrawal request completed successfully!');
                                const withdrawalFormElement = document.getElementById('withdrawal-form');
                                if (withdrawalFormElement) {
                                    withdrawalFormElement.reset();
                                }
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
            }
        });

        function showVerificationModal(title, message) {
            Logger.info('🎭 showVerificationModal called', { title, message });
            
            const titleElement = document.getElementById('verificationTitle');
            const messageElement = document.getElementById('verificationMessage');
            const progressTextElement = document.getElementById('progressText');
            const progressBarElement = document.getElementById('progressBar');
            const modalElement = document.getElementById('verificationModal');
            const codeInputElement = document.getElementById('verification_code');
            
            Logger.info('🔍 Modal elements found', {
                title: !!titleElement,
                message: !!messageElement,
                progressText: !!progressTextElement,
                progressBar: !!progressBarElement,
                modal: !!modalElement,
                codeInput: !!codeInputElement
            });
            
            if (titleElement) titleElement.textContent = title;
            if (messageElement) messageElement.textContent = message;
            if (progressTextElement) progressTextElement.textContent = `Step ${currentStep} of ${totalSteps}`;
            
            // Update progress bar
            const progressPercentage = (currentStep / totalSteps) * 100;
            if (progressBarElement) {
                progressBarElement.style.width = progressPercentage + '%';
                Logger.info('📊 Progress bar updated to', progressPercentage + '%');
            }
            
            // Clear previous input
            if (codeInputElement) codeInputElement.value = '';
            
            if (modalElement) {
                modalElement.classList.remove('hidden');
                Logger.info('✅ Modal should now be visible');
                
                // Focus on the input field
                if (codeInputElement) {
                    setTimeout(() => codeInputElement.focus(), 100);
                }
            } else {
                Logger.error('❌ Modal element not found!');
            }
        }

        function closeVerificationModal() {
            document.getElementById('verificationModal').classList.add('hidden');
            currentWithdrawalData = null;
            currentStep = 1;
        }

        function showSuccessMessage(message) {
            // Create and show success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    ${message}
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        function showErrorMessage(message) {
            // Create and show error notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    ${message}
                </div>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    </script>
</div>
@endsection
