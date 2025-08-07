<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Withdrawal History
            </h2>
            <div class="flex items-center space-x-4">
                <a href="{{ route('withdrawal.index') }}" class="text-sm text-blue-600 hover:text-blue-800">New Withdrawal</a>
                <div class="text-sm text-gray-600">
                    Total Withdrawals: {{ $withdrawals->total() }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Filter Section -->
            <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 p-6 mb-6">
                <form method="GET" action="{{ route('withdrawal.history') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 min-w-64">
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="flex-1 min-w-64">
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('withdrawal.history') }}" class="px-4 py-2 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition-colors">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Withdrawals Table -->
            <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg border border-white/20 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Your Withdrawals</h3>
                </div>
                
                @if($withdrawals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($withdrawals as $withdrawal)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $withdrawal->reference ?? 'WD-' . $withdrawal->id }}</div>
                                        @if($withdrawal->transaction_hash)
                                            <div class="text-xs text-blue-600">
                                                <a href="#" class="hover:underline">{{ substr($withdrawal->transaction_hash, 0, 20) }}...</a>
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ number_format($withdrawal->amount, 8) }}</div>
                                        <div class="text-xs text-gray-500">~${{ number_format($withdrawal->amount * 50000, 2) }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">{{ $withdrawal->withdrawal_address }}</div>
                                        <button onclick="copyToClipboard('{{ $withdrawal->withdrawal_address }}')" class="text-xs text-blue-600 hover:text-blue-800">Copy</button>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($withdrawal->status === 'completed') bg-green-100 text-green-800
                                            @elseif($withdrawal->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($withdrawal->status === 'approved') bg-blue-100 text-blue-800
                                            @elseif($withdrawal->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            
                                            @if($withdrawal->status === 'completed')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @elseif($withdrawal->status === 'pending')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                            @elseif($withdrawal->status === 'rejected')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                            
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${{ number_format($withdrawal->fee ?? 0, 2) }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $withdrawal->created_at->format('M j, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $withdrawal->created_at->format('g:i A') }}</div>
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="showDetails({{ $withdrawal->id }})" class="text-blue-600 hover:text-blue-900">View</button>
                                        @if($withdrawal->status === 'pending')
                                            <button onclick="cancelWithdrawal({{ $withdrawal->id }})" class="ml-3 text-red-600 hover:text-red-900">Cancel</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No withdrawals found</h3>
                        <p class="text-gray-500 mb-6">You haven't made any withdrawal requests yet.</p>
                        <a href="{{ route('withdrawal.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Make First Withdrawal
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-lg max-w-lg w-full m-4">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Withdrawal Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent" class="p-6">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetails(withdrawalId) {
            // In a real app, you'd fetch this data via AJAX
            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('modalContent');
            
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Reference:</span>
                        <span class="font-medium">WD-${withdrawalId}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="font-medium">Pending</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium">0.05000000 BTC</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fee:</span>
                        <span class="font-medium">$2.50</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Address:</span>
                        <span class="font-medium text-sm break-all">1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium">${new Date().toLocaleDateString()}</span>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
        
        function cancelWithdrawal(withdrawalId) {
            if (confirm('Are you sure you want to cancel this withdrawal request?')) {
                // In a real app, you'd send an AJAX request to cancel
                alert('Withdrawal cancellation request submitted');
            }
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'Address copied to clipboard';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            });
        }
        
        // Close modal when clicking outside
        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</x-app-layout>
