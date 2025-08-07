@extends('layouts.admin')

@section('title', 'Transactions Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Transactions Management</h1>
    <div class="flex space-x-3">
        <button onclick="openAnalyticsModal()" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span>Analytics</span>
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
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Deposits</dt>
                    <dd class="text-lg font-medium text-gray-900">${{ number_format($transactions->where('type', 'deposit')->sum('amount'), 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Withdrawals</dt>
                    <dd class="text-lg font-medium text-gray-900">${{ number_format($transactions->where('type', 'withdrawal')->sum('amount'), 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Trades</dt>
                    <dd class="text-lg font-medium text-gray-900">${{ number_format($transactions->where('type', 'trade')->sum('amount'), 2) }}</dd>
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
                    <dd class="text-lg font-medium text-gray-900">${{ number_format($transactions->sum('amount'), 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.transactions') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user, transaction ID, or description..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div class="flex space-x-4">
            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Types</option>
                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawal</option>
                <option value="trade" {{ request('type') == 'trade' ? 'selected' : '' }}>Trade</option>
                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="fee" {{ request('type') == 'fee' ? 'selected' : '' }}>Fee</option>
            </select>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                Search
            </button>
            @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
                <a href="{{ route('admin.transactions') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Transactions List</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-mono text-gray-900">{{ $transaction->transaction_id }}</div>
                        @if($transaction->description)
                            <div class="text-xs text-gray-500">{{ Str::limit($transaction->description, 30) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <span class="text-red-600 font-semibold text-sm">{{ strtoupper(substr($transaction->user->name, 0, 2)) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $transaction->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $transaction->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $typeClass = match($transaction->type) {
                                'deposit' => 'bg-green-100 text-green-800',
                                'withdrawal' => 'bg-red-100 text-red-800',
                                'trade' => 'bg-blue-100 text-blue-800',
                                'transfer' => 'bg-purple-100 text-purple-800',
                                'fee' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeClass }}">
                            {{ ucfirst($transaction->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold {{ $transaction->type == 'withdrawal' || $transaction->type == 'fee' ? 'text-red-600' : 'text-green-600' }}">
                            {{ $transaction->type == 'withdrawal' || $transaction->type == 'fee' ? '-' : '+' }}${{ number_format($transaction->amount, 2) }}
                        </div>
                        @if($transaction->fee && $transaction->fee > 0)
                            <div class="text-xs text-gray-500">Fee: ${{ number_format($transaction->fee, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClass = match($transaction->status) {
                                'completed' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'canceled' => 'bg-gray-100 text-gray-800',
                                'cancelled' => 'bg-gray-100 text-gray-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                'approved' => 'bg-blue-100 text-blue-800',
                                default => 'bg-yellow-100 text-yellow-800'
                            };
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $transaction->created_at->format('M d, Y') }}</div>
                        <div class="text-xs">{{ $transaction->created_at->format('H:i A') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="openTransactionModal({{ $transaction->id }})" class="text-blue-600 hover:text-blue-900 font-medium">
                            View Details
                        </button>
                        @if($transaction->status == 'pending' && in_array($transaction->type, ['deposit', 'withdrawal']))
                            <form method="POST" action="{{ route('admin.transactions.cancel', $transaction) }}" class="inline ml-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium"
                                        onclick="return confirm('Are you sure you want to cancel this transaction?')">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
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

<!-- Transaction Details Modal -->
<div id="transactionModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Transaction Details
                        </h3>
                        <div class="mt-4" id="transactionDetails">
                            <!-- Transaction details will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeTransactionModal()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openTransactionModal(transactionId) {
    document.getElementById('transactionModal').classList.remove('hidden');
    // Load transaction details via AJAX
    fetch(`/admin/transactions/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('transactionDetails').innerHTML = data.html;
        })
        .catch(error => {
            document.getElementById('transactionDetails').innerHTML = '<p class="text-red-600">Error loading transaction details.</p>';
        });
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
    document.getElementById('transactionDetails').innerHTML = '';
}

// Close modal when clicking outside
document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTransactionModal();
    }
});
</script>
@endsection
