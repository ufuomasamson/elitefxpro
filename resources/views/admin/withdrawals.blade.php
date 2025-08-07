@extends('layouts.admin')

@section('title', 'Withdrawals Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Withdrawals Management</h1>
    <div class="flex space-x-3">
        <button onclick="openBulkActionModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span>Bulk Process</span>
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
<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
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
                    <dd class="text-lg font-medium text-gray-900">{{ $withdrawals->where('status', 'pending')->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Processing</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $withdrawals->where('status', 'processing')->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $withdrawals->where('status', 'completed')->count() }}</dd>
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
                    <dd class="text-lg font-medium text-gray-900">{{ $withdrawals->where('status', 'rejected')->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Volume</dt>
                    <dd class="text-lg font-medium text-gray-900">${{ number_format($withdrawals->sum('amount'), 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.withdrawals') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user, transaction ID, or amount..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div class="flex space-x-4">
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="method" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Methods</option>
                <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="crypto" {{ request('method') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                Search
            </button>
            @if(request()->hasAny(['search', 'status', 'method', 'date_from', 'date_to']))
                <a href="{{ route('admin.withdrawals') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Withdrawals Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Withdrawals List</h3>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($withdrawals as $withdrawal)
                <tr class="hover:bg-gray-50" data-withdrawal-id="{{ $withdrawal->id }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="withdrawal-checkbox rounded border-gray-300 text-red-600 focus:ring-red-500" value="{{ $withdrawal->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <span class="text-red-600 font-semibold text-sm">{{ strtoupper(substr($withdrawal->user->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $withdrawal->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $withdrawal->user->email }}</div>
                                <div class="text-xs text-gray-400">ID: {{ $withdrawal->transaction_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">${{ number_format($withdrawal->amount, 2) }}</div>
                        @if($withdrawal->fee > 0)
                            <div class="text-xs text-gray-500">Fee: ${{ number_format($withdrawal->fee, 2) }}</div>
                            <div class="text-xs text-green-600">Net: ${{ number_format($withdrawal->amount - $withdrawal->fee, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $withdrawal->method) }}</div>
                        @if($withdrawal->crypto_type)
                            <div class="text-xs text-gray-500">{{ strtoupper($withdrawal->crypto_type) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($withdrawal->method == 'bank_transfer')
                            <div class="text-sm text-gray-900">{{ $withdrawal->bank_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $withdrawal->account_number ?? 'N/A' }}</div>
                        @elseif($withdrawal->method == 'crypto')
                            <div class="text-sm text-gray-900 font-mono">{{ substr($withdrawal->wallet_address, 0, 20) }}...</div>
                            <div class="text-xs text-gray-500">{{ $withdrawal->network ?? 'N/A' }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClass = match($withdrawal->status) {
                                'completed' => 'bg-green-100 text-green-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                default => 'bg-yellow-100 text-yellow-800'
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst($withdrawal->status) }}
                        </span>
                        @if($withdrawal->status == 'pending')
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $withdrawal->created_at->diffForHumans() }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $withdrawal->created_at->format('M d, Y') }}</div>
                        <div class="text-xs">{{ $withdrawal->created_at->format('H:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex flex-col space-y-1">
                            @if($withdrawal->status === 'pending')
                                <div class="flex space-x-2">
                                    <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 font-medium text-xs"
                                                onclick="return confirm('Are you sure you want to approve this withdrawal?')">
                                            Approve
                                        </button>
                                    </form>
                                    <button onclick="openRejectModal({{ $withdrawal->id }})" class="text-red-600 hover:text-red-900 font-medium text-xs">
                                        Reject
                                    </button>
                                </div>
                            @elseif($withdrawal->status === 'processing')
                                <form method="POST" action="{{ route('admin.withdrawals.complete', $withdrawal) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 font-medium text-xs"
                                            onclick="return confirm('Mark this withdrawal as completed?')">
                                        Mark Complete
                                    </button>
                                </form>
                            @endif
                            <button onclick="openWithdrawalModal({{ $withdrawal->id }})" class="text-purple-600 hover:text-purple-900 font-medium text-xs">
                                View Details
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        No withdrawals found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($withdrawals->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $withdrawals->links() }}
    </div>
    @endif
</div>

<!-- Reject Withdrawal Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Reject Withdrawal
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Please provide a reason for rejecting this withdrawal.
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
                        Reject Withdrawal
                    </button>
                    <button type="button" onclick="closeRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(withdrawalId) {
    document.getElementById('rejectForm').action = `/admin/withdrawals/${withdrawalId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('reject_reason').focus();
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectForm').reset();
}

// Handle select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.withdrawal-checkbox');
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
</script>
@endsection
