@extends('layouts.admin')

@section('title', 'Deposits Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Deposits Management</h1>
    <div class="flex space-x-3">
        <button onclick="openBulkActionModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span>Bulk Actions</span>
        </button>
        <button onclick="openModal('exportModal')" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Export</span>
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $deposits->where('status', 'pending')->count() }}</dd>
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
                    <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $deposits->where('status', 'approved')->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $deposits->where('status', 'rejected')->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

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
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Volume</dt>
                    <dd class="text-lg font-medium text-gray-900">${{ number_format($deposits->sum('amount'), 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.deposits') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user, transaction ID, or amount..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div class="flex space-x-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="method" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Methods</option>
                <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="crypto" {{ request('method') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                <option value="card" {{ request('method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                Search
            </button>
            @if(request()->hasAny(['search', 'status', 'method', 'date_from', 'date_to']))
                <a href="{{ route('admin.deposits') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Deposits Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Deposits List</h3>
        <div class="flex items-center space-x-2">
            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
            <label for="selectAll" class="text-sm text-gray-600">Select All</label>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($deposits as $deposit)
                <tr class="hover:bg-gray-50" data-deposit-id="{{ $deposit->id }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="deposit-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500" value="{{ $deposit->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <span class="text-red-600 font-semibold text-sm">{{ strtoupper(substr($deposit->user->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $deposit->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $deposit->user->email }}</div>
                                <div class="text-xs text-gray-400">ID: {{ $deposit->transaction_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">${{ number_format($deposit->amount, 2) }}</div>
                        @if($deposit->fee > 0)
                            <div class="text-xs text-gray-500">Fee: ${{ number_format($deposit->fee, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $deposit->method) }}</div>
                        @if($deposit->crypto_type)
                            <div class="text-xs text-gray-500">{{ strtoupper($deposit->crypto_type) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClass = match($deposit->status) {
                                'approved' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                default => 'bg-yellow-100 text-yellow-800'
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst($deposit->status) }}
                        </span>
                        @if($deposit->status == 'pending')
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $deposit->created_at->diffForHumans() }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $deposit->created_at->format('M d, Y') }}</div>
                        <div class="text-xs">{{ $deposit->created_at->format('H:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            @if($deposit->status === 'pending')
                                <button onclick="openApproveModal({{ $deposit->id }}, '{{ $deposit->user->name }}', '{{ $deposit->amount }}', '{{ $deposit->crypto_symbol }}', '{{ $deposit->created_at->format('M d, Y') }}')" class="text-green-600 hover:text-green-900 font-medium">
                                    Approve
                                </button>
                                <button onclick="openRejectModal({{ $deposit->id }})" class="text-red-600 hover:text-red-900 font-medium">
                                    Reject
                                </button>
                            @endif
                            <button onclick="openViewModal({{ $deposit->id }})" class="text-blue-600 hover:text-blue-900 font-medium">
                                View
                            </button>
                            @if($deposit->proof_file)
                                <a href="{{ asset('storage/' . $deposit->proof_file) }}" target="_blank" class="text-purple-600 hover:text-purple-900 font-medium">
                                    Proof
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        No deposits found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($deposits->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $deposits->links() }}
    </div>
    @endif
</div>

<!-- Reject Deposit Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Reject Deposit
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Please provide a reason for rejecting this deposit.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label for="reject_reason" class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                                <textarea name="reject_reason" id="reject_reason" rows="4" required
                                          placeholder="Enter reason for rejection..."
                                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reject Deposit
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Deposit Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Deposit Details
                        </h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Transaction ID</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono" id="view_transaction_id">-</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">User</label>
                                    <p class="mt-1 text-sm text-gray-900" id="view_user_name">-</p>
                                    <p class="text-xs text-gray-500" id="view_user_email">-</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Amount</label>
                                    <p class="mt-1 text-lg font-bold text-gray-900" id="view_amount">-</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cryptocurrency</label>
                                    <p class="mt-1 text-sm text-gray-900" id="view_crypto_symbol">-</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Method</label>
                                    <p class="mt-1 text-sm text-gray-900" id="view_method">-</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full" id="view_status">-</span>
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Submitted</label>
                                    <p class="mt-1 text-sm text-gray-900" id="view_created_at">-</p>
                                </div>
                                <div id="view_processed_section" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700">Processed</label>
                                    <p class="mt-1 text-sm text-gray-900" id="view_processed_at">-</p>
                                    <p class="text-xs text-gray-500">by <span id="view_processed_by">-</span></p>
                                </div>
                                <div id="view_rejection_section" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700">Rejection Reason</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-red-50 p-2 rounded" id="view_rejection_reason">-</p>
                                </div>
                                <div id="view_proof_section" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700">Proof of Payment</label>
                                    <div class="mt-1">
                                        <a href="#" target="_blank" id="view_proof_link" class="text-blue-600 hover:text-blue-800 text-sm">
                                            View Proof File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Proof Image Preview -->
                        <div id="view_proof_image_section" class="hidden mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Proof of Payment Preview</label>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <img id="view_proof_image" src="#" alt="Proof of Payment" class="max-w-full h-auto rounded shadow">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeViewModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Deposit Modal -->
<div id="approveModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form id="approveForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <!-- Header -->
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                ✅ Approve Deposit
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Review the deposit details below and confirm approval. This action will add the funds to the user's wallet.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Deposit Details Card -->
                    <div class="mt-6 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 px-4 py-3 border-b border-gray-200">
                            <h4 class="text-lg font-semibold text-gray-800">💰 Deposit Information</h4>
                        </div>
                        <div class="bg-white p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- User Information -->
                                <div class="space-y-3">
                                    <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wide">👤 User Details</h5>
                                    <div class="bg-gray-50 rounded-lg p-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <span class="text-green-600 font-medium text-sm" id="approveUserInitials"></span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900" id="approveUserName"></p>
                                                <p class="text-xs text-gray-500">Account Holder</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Amount Information -->
                                <div class="space-y-3">
                                    <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wide">💵 Amount Details</h5>
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <div class="text-center">
                                            <p class="text-2xl font-bold text-green-600" id="approveAmount"></p>
                                            <p class="text-sm text-gray-600">
                                                <span id="approveCurrency" class="font-medium"></span> Deposit
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Transaction Details -->
                                <div class="space-y-3">
                                    <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wide">📅 Transaction Info</h5>
                                    <div class="bg-blue-50 rounded-lg p-3 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-xs text-gray-600">Date:</span>
                                            <span class="text-xs font-medium text-gray-900" id="approveDate"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-xs text-gray-600">Status:</span>
                                            <span class="text-xs font-medium text-yellow-600">Pending Approval</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Impact Summary -->
                                <div class="space-y-3">
                                    <h5 class="text-sm font-medium text-gray-500 uppercase tracking-wide">⚡ Impact Summary</h5>
                                    <div class="bg-yellow-50 rounded-lg p-3 space-y-2">
                                        <div class="flex items-center text-xs text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Funds will be added to user's wallet
                                        </div>
                                        <div class="flex items-center text-xs text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Deposit status will be marked as approved
                                        </div>
                                        <div class="flex items-center text-xs text-gray-600">
                                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Transaction will be recorded
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Warning Notice -->
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Important:</strong> This action cannot be undone. Please verify all details before proceeding.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Deposit
                    </button>
                    <button type="button" onclick="closeApproveModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentDepositId = null;

function openViewModal(depositId) {
    currentDepositId = depositId;
    
    // Show loading state
    document.getElementById('viewModal').classList.remove('hidden');
    
    // Fetch deposit details
    fetch(`/admin/deposits/${depositId}/view`)
        .then(response => response.json())
        .then(data => {
            // Fill in the data
            document.getElementById('view_transaction_id').textContent = data.transaction_id || 'N/A';
            document.getElementById('view_user_name').textContent = data.user.name;
            document.getElementById('view_user_email').textContent = data.user.email;
            document.getElementById('view_amount').textContent = `$${parseFloat(data.amount).toFixed(2)}`;
            document.getElementById('view_crypto_symbol').textContent = data.crypto_symbol || 'N/A';
            document.getElementById('view_method').textContent = data.method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            document.getElementById('view_created_at').textContent = data.created_at;
            
            // Set status with appropriate styling
            const statusEl = document.getElementById('view_status');
            statusEl.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            statusEl.className = 'mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ' + getStatusClass(data.status);
            
            // Show processed information if available
            if (data.processed_at) {
                document.getElementById('view_processed_at').textContent = data.processed_at;
                document.getElementById('view_processed_by').textContent = data.processed_by || 'Unknown';
                document.getElementById('view_processed_section').classList.remove('hidden');
            } else {
                document.getElementById('view_processed_section').classList.add('hidden');
            }
            
            // Show rejection reason if rejected
            if (data.status === 'rejected' && data.rejection_reason) {
                document.getElementById('view_rejection_reason').textContent = data.rejection_reason;
                document.getElementById('view_rejection_section').classList.remove('hidden');
            } else {
                document.getElementById('view_rejection_section').classList.add('hidden');
            }
            
            // Show proof file if available
            if (data.proof_file) {
                document.getElementById('view_proof_link').href = data.proof_file;
                document.getElementById('view_proof_section').classList.remove('hidden');
                
                // Show image preview if it's an image
                if (data.proof_file.match(/\.(jpg|jpeg|png|gif)$/i)) {
                    document.getElementById('view_proof_image').src = data.proof_file;
                    document.getElementById('view_proof_image_section').classList.remove('hidden');
                } else {
                    document.getElementById('view_proof_image_section').classList.add('hidden');
                }
            } else {
                document.getElementById('view_proof_section').classList.add('hidden');
                document.getElementById('view_proof_image_section').classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error fetching deposit details:', error);
            alert('Error loading deposit details');
            closeViewModal();
        });
}

function closeViewModal() {
    document.getElementById('viewModal').classList.add('hidden');
    currentDepositId = null;
}

function getStatusClass(status) {
    switch(status) {
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-yellow-100 text-yellow-800';
    }
}

function openRejectModal(depositId) {
    document.getElementById('rejectForm').action = `/admin/deposits/${depositId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('reject_reason').focus();
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').reset();
}

function openApproveModal(depositId, userName, amount, currency, date) {
    // Set the form action
    document.getElementById('approveForm').action = `/admin/deposits/${depositId}/approve`;
    
    // Populate the modal with deposit data
    document.getElementById('approveUserName').textContent = userName;
    document.getElementById('approveUserInitials').textContent = userName.substring(0, 2).toUpperCase();
    document.getElementById('approveAmount').textContent = '$' + parseFloat(amount).toFixed(2);
    document.getElementById('approveCurrency').textContent = currency;
    document.getElementById('approveDate').textContent = date;
    
    // Show the modal
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.getElementById('approveForm').reset();
}

// Handle select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.deposit-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});

document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});

document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeViewModal();
    }
});
</script>
@endsection
