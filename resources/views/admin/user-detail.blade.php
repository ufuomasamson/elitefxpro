@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="space-y-6">
    <!-- User Header -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <span class="text-red-600 font-bold text-xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    <p class="text-sm text-gray-400">User ID: {{ $user->id }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    ← Back to Users
                </a>
                <button onclick="openEditBalanceModal({{ $user->id }}, '{{ $user->name }}', {{ $user->wallet_balance ?? 0 }})" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Edit Balance
                </button>
                <button onclick="openFundModal({{ $user->id }}, '{{ $user->name }}')" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Fund Account
                </button>
            </div>
        </div>
    </div>

    <!-- User Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Balance</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ format_currency($user->wallet_balance ?? 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Status</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $user->is_active ? 'Active' : 'Inactive' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">KYC Status</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ ucfirst($user->kyc_status ?? 'pending') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 9v2m-6 1h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Withdrawal Status</dt>
                        <dd class="text-lg font-medium text-gray-900">
                            @switch($user->withdrawal_status)
                                @case('active')
                                    <span class="text-green-600">Active</span>
                                    @break
                                @case('aml_kyc_verification')
                                    <span class="text-red-600">AML/KYC Required</span>
                                    @break
                                @case('aml_security_check')
                                    <span class="text-red-600">AML Security Check</span>
                                    @break
                                @case('regulatory_compliance')
                                    <span class="text-red-600">Regulatory Compliance</span>
                                    @break
                                @default
                                    <span class="text-gray-600">Unknown</span>
                            @endswitch
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-100 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 9v2m-6 1h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Joined</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Status Management -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Withdrawal Status Management</h3>
            <button onclick="openWithdrawalStatusModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->withdrawal_status }}', '{{ $user->aml_verification_code }}', '{{ $user->fwac_verification_code }}', '{{ $user->tsc_verification_code }}', '{{ addslashes($user->withdrawal_restriction_notes) }}')" 
                    class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                Update Withdrawal Status
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Current Status -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Current Status</h4>
                @switch($user->withdrawal_status)
                    @case('active')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            ✅ Active (No Restrictions)
                        </span>
                        @break
                    @case('aml_kyc_verification')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            🔒 AML/KYC Verification Required
                        </span>
                        @break
                    @case('aml_security_check')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            🔒 AML Security Check Required
                        </span>
                        @break
                    @case('regulatory_compliance')
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            🔒 Regulatory Compliance Required
                        </span>
                        @break
                @endswitch
            </div>

            <!-- AML Code Status -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">AML Code</h4>
                <div class="space-y-1">
                    <p class="text-sm font-mono">{{ $user->aml_verification_code ?: 'Not Set' }}</p>
                    @if($user->aml_code_used)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                            ✅ Used ({{ $user->aml_code_used_at?->format('M d, Y H:i') }})
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                            ⏳ Unused
                        </span>
                    @endif
                </div>
            </div>

            <!-- FWAC Code Status -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">FWAC Code</h4>
                <div class="space-y-1">
                    <p class="text-sm font-mono">{{ $user->fwac_verification_code ?: 'Not Set' }}</p>
                    @if($user->fwac_code_used)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                            ✅ Used ({{ $user->fwac_code_used_at?->format('M d, Y H:i') }})
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                            ⏳ Unused
                        </span>
                    @endif
                </div>
            </div>

            <!-- TSC Code Status -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-2">TSC Code</h4>
                <div class="space-y-1">
                    <p class="text-sm font-mono">{{ $user->tsc_verification_code ?: 'Not Set' }}</p>
                    @if($user->tsc_code_used)
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">
                            ✅ Used ({{ $user->tsc_code_used_at?->format('M d, Y H:i') }})
                        </span>
                    @else
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">
                            ⏳ Unused
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($user->withdrawal_restriction_notes)
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h4 class="text-sm font-medium text-yellow-800 mb-2">Admin Notes</h4>
            <p class="text-sm text-yellow-700">{{ $user->withdrawal_restriction_notes }}</p>
        </div>
        @endif
    </div>

    <!-- User Wallets -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Cryptocurrency Wallets</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Currency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">USD Value</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($wallets as $wallet)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-600 font-semibold text-sm">{{ $wallet->currency }}</span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $wallet->currency_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $wallet->currency }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($wallet->balance, 8) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ format_currency($wallet->balance_usd) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-blue-600 hover:text-blue-900">Adjust</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            No wallets found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">{{ $transaction->transaction_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeClass = match($transaction->type) {
                                    'deposit' => 'bg-green-100 text-green-800',
                                    'withdrawal' => 'bg-red-100 text-red-800',
                                    'trade' => 'bg-blue-100 text-blue-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeClass }}">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ format_currency($transaction->amount) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($transaction->status) {
                                    'completed' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-yellow-100 text-yellow-800'
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No transactions found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Fund User Modal -->
<div id="fundModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="fundForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Fund User Account
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="fundUserName">
                                    Add funds to user account
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="crypto_symbol" class="block text-sm font-medium text-gray-700">Cryptocurrency</label>
                                <select name="crypto_symbol" id="crypto_symbol" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                                    <option value="">Select Cryptocurrency</option>
                                    <option value="BTC">Bitcoin (BTC)</option>
                                    <option value="ETH">Ethereum (ETH)</option>
                                    <option value="ADA">Cardano (ADA)</option>
                                    <option value="DOT">Polkadot (DOT)</option>
                                    <option value="LTC">Litecoin (LTC)</option>
                                    <option value="XRP">Ripple (XRP)</option>
                                    <option value="BCH">Bitcoin Cash (BCH)</option>
                                    <option value="LINK">Chainlink (LINK)</option>
                                    <option value="BNB">Binance Coin (BNB)</option>
                                    <option value="USDT">Tether (USDT)</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Amount (Crypto)</label>
                                <input type="number" name="amount" id="amount" step="0.00000001" min="0.00000001" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"
                                       placeholder="0.00000000">
                            </div>
                            <div class="mt-4">
                                <label for="note" class="block text-sm font-medium text-gray-700">Note (Optional)</label>
                                <textarea name="note" id="note" rows="3" placeholder="Reason for funding..."
                                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Fund Account
                    </button>
                    <button type="button" onclick="closeFundModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Balance Modal -->
<div id="editBalanceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit User Balance</h3>
                <button onclick="closeEditBalanceModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="editBalanceForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label for="edit_balance" class="block text-sm font-medium text-gray-700">New Balance ($)</label>
                        <input type="number" id="edit_balance" name="balance" step="0.01" min="0" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter new balance">
                        <p class="mt-1 text-sm text-gray-500">Current balance: <span id="currentBalance">$0.00</span></p>
                    </div>
                    
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-yellow-700 font-medium">Balance Change Notice</p>
                                <p class="text-sm text-yellow-600 mt-1">This action will be logged and tracked automatically for security purposes.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditBalanceModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Update Balance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdrawal Status Modal -->
<div id="withdrawalStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Withdrawal Status</h3>
                <button onclick="closeWithdrawalStatusModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="withdrawalStatusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="space-y-6">
                    <!-- Withdrawal Status Selection -->
                    <div>
                        <label for="withdrawal_status" class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Status</label>
                        <select id="withdrawal_status" name="withdrawal_status" required
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                onchange="updateWithdrawalStatusForm()">
                            <option value="active">Active (No Restrictions)</option>
                            <option value="aml_kyc_verification">AML/KYC Verification Required</option>
                            <option value="aml_security_check">AML Security Check Required</option>
                            <option value="regulatory_compliance">Regulatory Compliance Required</option>
                        </select>
                    </div>

                    <!-- Verification Codes Section -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="aml_verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                                AML Verification Code
                                <span class="text-xs text-gray-500">(for AML/KYC status)</span>
                            </label>
                            <input type="text" id="aml_verification_code" name="aml_verification_code" maxlength="20"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="e.g., AML123456">
                        </div>

                        <div>
                            <label for="fwac_verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                                FWAC Verification Code
                                <span class="text-xs text-gray-500">(for AML Security status)</span>
                            </label>
                            <input type="text" id="fwac_verification_code" name="fwac_verification_code" maxlength="20"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="e.g., FWAC789012">
                        </div>

                        <div>
                            <label for="tsc_verification_code" class="block text-sm font-medium text-gray-700 mb-2">
                                TSC Verification Code
                                <span class="text-xs text-gray-500">(for Regulatory status)</span>
                            </label>
                            <input type="text" id="tsc_verification_code" name="tsc_verification_code" maxlength="20"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                   placeholder="e.g., TSC345678">
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div>
                        <label for="withdrawal_restriction_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Admin Notes (Optional)
                        </label>
                        <textarea id="withdrawal_restriction_notes" name="withdrawal_restriction_notes" rows="3"
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500"
                                  placeholder="Add any additional notes about this restriction..."></textarea>
                    </div>

                    <!-- Info Panel -->
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">How it works:</h4>
                        <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                            <li><strong>AML/KYC Verification:</strong> User will see "First-time withdrawals require a WAC. Contact support for your code."</li>
                            <li><strong>AML Security Check:</strong> User will see "First-time withdrawals require a FWAC. Contact support for your code."</li>
                            <li><strong>Regulatory Compliance:</strong> User will see "To proceed, you'll need a TSC. Contact support for your code."</li>
                            <li><strong>Active:</strong> User can make withdrawals normally without restrictions.</li>
                        </ul>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeWithdrawalStatusModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openFundModal(userId, userName) {
    document.getElementById('fundForm').action = `/admin/users/${userId}/fund`;
    document.getElementById('fundUserName').textContent = `Add funds to ${userName}'s account`;
    document.getElementById('fundModal').classList.remove('hidden');
    document.getElementById('amount').focus();
}

function closeFundModal() {
    document.getElementById('fundModal').classList.add('hidden');
    document.getElementById('fundForm').reset();
}

function openEditBalanceModal(userId, userName, currentBalance) {
    document.getElementById('editBalanceForm').action = `/admin/users/${userId}/edit-balance`;
    document.getElementById('currentBalance').textContent = `$${parseFloat(currentBalance).toFixed(2)}`;
    document.getElementById('edit_balance').value = parseFloat(currentBalance).toFixed(2);
    document.getElementById('editBalanceModal').classList.remove('hidden');
    document.getElementById('edit_balance').focus();
}

function closeEditBalanceModal() {
    document.getElementById('editBalanceModal').classList.add('hidden');
    document.getElementById('editBalanceForm').reset();
}

function openWithdrawalStatusModal(userId, userName, currentStatus, amlCode, fwacCode, tscCode, notes) {
    document.getElementById('withdrawalStatusForm').action = `/admin/users/${userId}/withdrawal-status`;
    document.getElementById('withdrawal_status').value = currentStatus || 'active';
    document.getElementById('aml_verification_code').value = amlCode || '';
    document.getElementById('fwac_verification_code').value = fwacCode || '';
    document.getElementById('tsc_verification_code').value = tscCode || '';
    document.getElementById('withdrawal_restriction_notes').value = notes || '';
    
    document.getElementById('withdrawalStatusModal').classList.remove('hidden');
    document.getElementById('withdrawal_status').focus();
}

function closeWithdrawalStatusModal() {
    document.getElementById('withdrawalStatusModal').classList.add('hidden');
    document.getElementById('withdrawalStatusForm').reset();
}

function updateWithdrawalStatusForm() {
    // This function could be used to show/hide relevant code fields based on selected status
    // For now, we'll keep all fields visible for admin flexibility
}

// Close modal when clicking outside
document.getElementById('fundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFundModal();
    }
});

document.getElementById('editBalanceModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditBalanceModal();
    }
});

document.getElementById('withdrawalStatusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWithdrawalStatusModal();
    }
});
</script>
@endsection
