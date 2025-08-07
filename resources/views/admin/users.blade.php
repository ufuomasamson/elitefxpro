@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<!-- Success/Error Messages -->
@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-green-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Success!</p>
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
    <div class="flex">
        <div class="py-1">
            <svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
            </svg>
        </div>
        <div>
            <p class="font-bold">Error!</p>
            <p class="text-sm">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
    <p class="mt-2 text-gray-600">Manage user accounts and funding</p>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-users text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Total Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Funded Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->filter(function($user) { $usdtWallet = $user->wallets->where('currency', 'USDT')->first(); return $usdtWallet && $usdtWallet->balance > 0; })->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Awaiting Funding</p>
                <p class="text-2xl font-bold text-gray-900">{{ $users->filter(function($user) { $usdtWallet = $user->wallets->where('currency', 'USDT')->first(); return !$usdtWallet || $usdtWallet->balance <= 0; })->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">All Users</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        User
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        USDT Balance
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Registered
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                @php
                    $usdtWallet = $user->wallets->where('currency', 'USDT')->first();
                    $balance = $usdtWallet ? $usdtWallet->balance : 0;
                    $isFunded = $balance > 0;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-semibold text-sm">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        @if($user->email_verified_at)
                            <div class="text-xs text-green-600">✓ Verified</div>
                        @else
                            <div class="text-xs text-gray-500">Unverified</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-gray-900">{{ number_format($balance, 2) }} USDT</div>
                        @if($balance > 0)
                            <div class="text-xs text-green-600">€{{ number_format($balance, 2) }} equivalent</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="space-y-1">
                            @if($isFunded)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Funded
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Unfunded
                                </span>
                            @endif
                            
                            @php
                                $withdrawalStatus = $user->withdrawal_status ?? 'active';
                                $statusClasses = [
                                    'active' => 'bg-blue-100 text-blue-800',
                                    'aml_kyc_verification' => 'bg-yellow-100 text-yellow-800',
                                    'aml_security_check' => 'bg-orange-100 text-orange-800',
                                    'regulatory_compliance' => 'bg-purple-100 text-purple-800'
                                ];
                                $statusIcons = [
                                    'active' => 'fa-unlock',
                                    'aml_kyc_verification' => 'fa-user-check',
                                    'aml_security_check' => 'fa-shield-alt',
                                    'regulatory_compliance' => 'fa-gavel'
                                ];
                                $statusText = [
                                    'active' => 'Active',
                                    'aml_kyc_verification' => 'KYC Required',
                                    'aml_security_check' => 'Security Check',
                                    'regulatory_compliance' => 'Compliance Hold'
                                ];
                            @endphp
                            
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$withdrawalStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                <i class="fas {{ $statusIcons[$withdrawalStatus] ?? 'fa-question' }} mr-1"></i>
                                {{ $statusText[$withdrawalStatus] ?? 'Unknown' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('M j, Y') }}
                        <br>
                        <span class="text-xs">{{ $user->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.user-detail', $user) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                View Profile
                            </a>
                            <button 
                                onclick="openFundModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', {{ $balance }})"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            >
                                <i class="fas fa-plus mr-1"></i>
                                Fund Account
                            </button>
                            <button 
                                onclick="openWithdrawalStatusModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->withdrawal_status ?? 'active' }}')"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                            >
                                <i class="fas fa-cog mr-1"></i>
                                Withdrawal
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Fund User Modal -->
<div id="fundModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Fund User Account</h3>
                <button onclick="closeFundModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="fundForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">User Details</label>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-sm"><strong>Name:</strong> <span id="modalUserName"></span></p>
                        <p class="text-sm"><strong>Email:</strong> <span id="modalUserEmail"></span></p>
                        <p class="text-sm"><strong>Current Balance:</strong> <span id="modalCurrentBalance"></span> USDT</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount to Add (USDT)</label>
                    <input 
                        type="number" 
                        id="amount" 
                        name="amount" 
                        step="0.01" 
                        min="0.01" 
                        max="1000000" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter amount"
                    >
                </div>

                <div class="mb-4">
                    <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Note (Optional)</label>
                    <input 
                        type="text" 
                        id="note" 
                        name="note" 
                        maxlength="255"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Reason for funding"
                    >
                </div>

                <div class="flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="closeFundModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <i class="fas fa-plus mr-1"></i>
                        Fund Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdrawal Status Modal -->
<div id="withdrawalStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Update Withdrawal Status</h3>
                <button onclick="closeWithdrawalStatusModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="withdrawalStatusForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">User Details</label>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-sm"><strong>Name:</strong> <span id="withdrawalModalUserName"></span></p>
                        <p class="text-sm"><strong>Current Status:</strong> <span id="withdrawalModalCurrentStatus"></span></p>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="withdrawal_status" class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Status</label>
                    <select 
                        id="withdrawal_status" 
                        name="withdrawal_status" 
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                        <option value="active">Active</option>
                        <option value="aml_kyc_verification">AML/KYC Verification Required</option>
                        <option value="aml_security_check">AML Security Check</option>
                        <option value="regulatory_compliance">Regulatory Compliance Hold</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="withdrawal_restriction_notes" class="block text-sm font-medium text-gray-700 mb-2">Restriction Notes (Optional)</label>
                    <textarea 
                        id="withdrawal_restriction_notes" 
                        name="withdrawal_restriction_notes" 
                        maxlength="500"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                        placeholder="Reason for restriction or notes"
                    ></textarea>
                </div>

                <!-- Verification Codes Section -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Verification Codes (Optional)</h4>
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label for="aml_verification_code" class="block text-xs font-medium text-gray-600 mb-1">AML Verification Code</label>
                            <input 
                                type="text" 
                                id="aml_verification_code" 
                                name="aml_verification_code" 
                                maxlength="20"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                                placeholder="AML code"
                            >
                        </div>
                        <div>
                            <label for="fwac_verification_code" class="block text-xs font-medium text-gray-600 mb-1">FWAC Verification Code</label>
                            <input 
                                type="text" 
                                id="fwac_verification_code" 
                                name="fwac_verification_code" 
                                maxlength="20"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                                placeholder="FWAC code"
                            >
                        </div>
                        <div>
                            <label for="tsc_verification_code" class="block text-xs font-medium text-gray-600 mb-1">TSC Verification Code</label>
                            <input 
                                type="text" 
                                id="tsc_verification_code" 
                                name="tsc_verification_code" 
                                maxlength="20"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-purple-500"
                                placeholder="TSC code"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="closeWithdrawalStatusModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
                    >
                        <i class="fas fa-save mr-1"></i>
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openFundModal(userId, userName, userEmail, currentBalance) {
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalUserEmail').textContent = userEmail;
    document.getElementById('modalCurrentBalance').textContent = currentBalance.toFixed(2);
    
    document.getElementById('fundForm').action = `/admin/users/${userId}/fund`;
    
    document.getElementById('fundModal').classList.remove('hidden');
    document.getElementById('amount').focus();
}

function closeFundModal() {
    document.getElementById('fundModal').classList.add('hidden');
    document.getElementById('fundForm').reset();
}

function openWithdrawalStatusModal(userId, userName, currentStatus) {
    document.getElementById('withdrawalModalUserName').textContent = userName;
    document.getElementById('withdrawalModalCurrentStatus').textContent = currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1).replace(/_/g, ' ');
    
    // Set the current status in the select dropdown
    document.getElementById('withdrawal_status').value = currentStatus;
    
    document.getElementById('withdrawalStatusForm').action = `/admin/users/${userId}/withdrawal-status`;
    
    document.getElementById('withdrawalStatusModal').classList.remove('hidden');
    document.getElementById('withdrawal_status').focus();
}

function closeWithdrawalStatusModal() {
    document.getElementById('withdrawalStatusModal').classList.add('hidden');
    document.getElementById('withdrawalStatusForm').reset();
}

// Close modal when clicking outside
document.getElementById('fundModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeFundModal();
    }
});

document.getElementById('withdrawalStatusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWithdrawalStatusModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFundModal();
        closeWithdrawalStatusModal();
    }
});
</script>
@endsection
