@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-history text-blue-600 mr-3"></i>
                    Transaction History
                </h2>
                <p class="text-gray-600 mt-2">{{ __('View all your trading activities and transactions') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-600">
                    Total Transactions: {{ ($deposits->count() ?? 0) + ($withdrawals->count() ?? 0) + ($trades->count() ?? 0) + ($transactions->count() ?? 0) }}
                </div>
                <div class="text-sm text-gray-600">
                    Net P&L: <span class="font-bold {{ ($netPL ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ format_currency($netPL ?? 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-xs font-medium uppercase tracking-wide">Total Deposits</p>
                            <p class="text-lg font-bold">{{ format_currency($totalDeposits ?? 0) }}</p>
                            <p class="text-green-200 text-xs mt-1">{{ ($deposits->where('status', 'approved')->count() ?? 0) }} transactions</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-xs font-medium uppercase tracking-wide">Total Withdrawals</p>
                            <p class="text-lg font-bold">{{ format_currency($totalWithdrawals ?? 0) }}</p>
                            <p class="text-red-200 text-xs mt-1">{{ ($withdrawals->where('status', 'approved')->count() ?? 0) }} transactions</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-xs font-medium uppercase tracking-wide">Total Trades</p>
                            <p class="text-lg font-bold">{{ number_format($totalTrades ?? 0) }}</p>
                            <p class="text-blue-200 text-xs mt-1">{{ ($trades->where('direction', 'buy')->count() ?? 0) }} buys, {{ ($trades->where('direction', 'sell')->count() ?? 0) }} sells</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg shadow-lg p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-xs font-medium uppercase tracking-wide">Net P&L</p>
                            <p class="text-lg font-bold">${{ number_format($netPL, 2) }}</p>
                            <p class="text-purple-200 text-xs mt-1">{{ $netPL >= 0 ? 'Profit' : 'Loss' }}</p>
                        </div>
                        <div class="p-2 bg-white/20 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6 mb-6">
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                        <select id="transaction_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Transactions</option>
                            <option value="deposit">Deposits</option>
                            <option value="withdrawal">Withdrawals</option>
                            <option value="trade">Trades</option>
                            <option value="fund">Admin Funding</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 min-w-64">
                        <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 min-w-64">
                        <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                        <select id="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button onclick="applyFilters()" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Apply Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">All Transactions</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full" id="transactions-table">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="transactions-body">
                            @php
                                $allTransactions = collect();
                                
                                // Add general transactions (includes admin funding)
                                foreach($transactions as $transaction) {
                                    $allTransactions->push([
                                        'date' => $transaction->created_at,
                                        'type' => $transaction->type,
                                        'description' => $transaction->description ?? ucfirst($transaction->type),
                                        'amount' => $transaction->amount,
                                        'status' => $transaction->status,
                                        'reference' => $transaction->reference ?? 'TXN-' . $transaction->id,
                                        'crypto' => 'USD'
                                    ]);
                                }
                                
                                // Add deposits
                                foreach($deposits as $deposit) {
                                    $allTransactions->push([
                                        'date' => $deposit->created_at,
                                        'type' => 'deposit',
                                        'description' => 'Crypto Deposit',
                                        'amount' => $deposit->amount,
                                        'status' => $deposit->status,
                                        'reference' => $deposit->reference ?? 'DEP-' . $deposit->id,
                                        'crypto' => $deposit->crypto_symbol ?? 'USD'
                                    ]);
                                }
                                
                                // Add withdrawals
                                foreach($withdrawals as $withdrawal) {
                                    $allTransactions->push([
                                        'date' => $withdrawal->created_at,
                                        'type' => 'withdrawal',
                                        'description' => 'Crypto Withdrawal',
                                        'amount' => $withdrawal->amount,
                                        'status' => $withdrawal->status,
                                        'reference' => $withdrawal->reference ?? 'WD-' . $withdrawal->id,
                                        'crypto' => 'USD'
                                    ]);
                                }
                                
                                // Add trades
                                foreach($trades as $trade) {
                                    $allTransactions->push([
                                        'date' => $trade->created_at,
                                        'type' => 'trade',
                                        'description' => ucfirst($trade->direction) . ' ' . $trade->crypto_symbol,
                                        'amount' => $trade->total_value,
                                        'status' => 'completed',
                                        'reference' => 'TRD-' . $trade->id,
                                        'crypto' => $trade->crypto_symbol,
                                        'direction' => $trade->direction
                                    ]);
                                }
                                
                                $allTransactions = $allTransactions->sortByDesc('date');
                            @endphp

                            @foreach($allTransactions as $transaction)
                            <tr class="hover:bg-gray-50/50 transition-colors transaction-row" 
                                data-type="{{ $transaction['type'] }}" 
                                data-status="{{ $transaction['status'] }}"
                                data-date="{{ $transaction['date']->format('Y-m-d') }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction['date']->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction['date']->format('g:i A') }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($transaction['type'] === 'deposit') bg-green-100 text-green-800
                                        @elseif($transaction['type'] === 'withdrawal') bg-red-100 text-red-800
                                        @elseif($transaction['type'] === 'trade') bg-blue-100 text-blue-800
                                        @elseif($transaction['type'] === 'fund') bg-purple-100 text-purple-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        
                                        @if($transaction['type'] === 'deposit')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($transaction['type'] === 'withdrawal')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM9 13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif($transaction['type'] === 'fund')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 2h12v11H4V4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        
                                        {{ ucfirst($transaction['type']) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction['description'] }}</div>
                                    @if(isset($transaction['crypto']))
                                        <div class="text-xs text-gray-500">{{ $transaction['crypto'] }}</div>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium
                                        @if($transaction['type'] === 'deposit') text-green-600
                                        @elseif($transaction['type'] === 'withdrawal') text-red-600
                                        @elseif($transaction['type'] === 'trade' && isset($transaction['direction']) && $transaction['direction'] === 'buy') text-blue-600
                                        @elseif($transaction['type'] === 'trade' && isset($transaction['direction']) && $transaction['direction'] === 'sell') text-orange-600
                                        @else text-gray-900
                                        @endif">
                                        @if($transaction['type'] === 'deposit') + @elseif($transaction['type'] === 'withdrawal') - @endif
                                        ${{ number_format($transaction['amount'], 2) }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($transaction['status'] === 'completed' || $transaction['status'] === 'approved') bg-green-100 text-green-800
                                        @elseif($transaction['status'] === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transaction['status'] === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($transaction['status']) }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-mono">{{ $transaction['reference'] }}</div>
                                </td>
                            </tr>
                            @endforeach

                            @if($allTransactions->isEmpty())
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                                    <p class="text-gray-500 mb-6">You haven't made any transactions yet.</p>
                                    <div class="flex justify-center space-x-4">
                                        <a href="{{ route('deposit.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                                            Make Deposit
                                        </a>
                                        <a href="{{ route('trade.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                            Start Trading
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            const typeFilter = document.getElementById('transaction_type').value;
            const statusFilter = document.getElementById('status_filter').value;
            const dateFilter = document.getElementById('date_range').value;
            
            const rows = document.querySelectorAll('.transaction-row');
            const today = new Date();
            
            rows.forEach(row => {
                let show = true;
                
                // Type filter
                if (typeFilter !== 'all' && row.dataset.type !== typeFilter) {
                    show = false;
                }
                
                // Status filter
                if (statusFilter !== 'all' && row.dataset.status !== statusFilter) {
                    show = false;
                }
                
                // Date filter
                if (dateFilter !== 'all') {
                    const rowDate = new Date(row.dataset.date);
                    const daysDiff = Math.floor((today - rowDate) / (1000 * 60 * 60 * 24));
                    
                    switch(dateFilter) {
                        case 'today':
                            if (daysDiff > 0) show = false;
                            break;
                        case 'week':
                            if (daysDiff > 7) show = false;
                            break;
                        case 'month':
                            if (daysDiff > 30) show = false;
                            break;
                        case 'year':
                            if (daysDiff > 365) show = false;
                            break;
                    }
                }
                
                row.style.display = show ? '' : 'none';
            });
            
            // Update visible count
            const visibleRows = document.querySelectorAll('.transaction-row:not([style*="display: none"])');
            console.log(`Showing ${visibleRows.length} transactions`);
        }
        
        // Real-time search
        function addSearchFilter() {
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.placeholder = 'Search transactions...';
            searchInput.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.transaction-row');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const isVisible = text.includes(searchTerm);
                    row.style.display = isVisible ? '' : 'none';
                });
            });
            
            // Add to filter section
            const filterSection = document.querySelector('.bg-white\\/80.backdrop-blur-sm .flex-wrap');
            const searchDiv = document.createElement('div');
            searchDiv.className = 'flex-1 min-w-64';
            searchDiv.innerHTML = '<label class="block text-sm font-medium text-gray-700 mb-1">Search</label>';
            searchDiv.appendChild(searchInput);
            filterSection.insertBefore(searchDiv, filterSection.lastElementChild);
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            addSearchFilter();
        });
    </script>
</div>
@endsection
