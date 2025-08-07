@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Users</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $activeUsers ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Pending Deposits -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pending Deposits</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $pendingDeposits ?? 0 }}</p>
                        <p class="text-xs text-yellow-600">Requires attention</p>
                    </div>
                </div>
            </div>

            <!-- Pending Withdrawals -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg mr-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pending Withdrawals</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $pendingWithdrawals ?? 0 }}</p>
                        <p class="text-xs text-red-600">Requires attention</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users') }}" class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                    <div class="p-3 bg-blue-100 group-hover:bg-blue-200 rounded-lg mb-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Manage Users</span>
                </a>

                <a href="{{ route('admin.deposits') }}" class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                    <div class="p-3 bg-green-100 group-hover:bg-green-200 rounded-lg mb-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Review Deposits</span>
                </a>

                <a href="{{ route('admin.withdrawals') }}" class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                    <div class="p-3 bg-purple-100 group-hover:bg-purple-200 rounded-lg mb-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Review Withdrawals</span>
                </a>

                <a href="{{ route('admin.settings') }}" class="flex flex-col items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors group">
                    <div class="p-3 bg-gray-100 group-hover:bg-gray-200 rounded-lg mb-2">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-800">Platform Settings</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
                    <a href="{{ route('admin.transactions') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentTransactions ?? [] as $transaction)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-medium text-sm">{{ substr($transaction->user->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $transaction->user->name ?? 'Unknown User' }}</p>
                                    <p class="text-sm text-gray-600">{{ $transaction->type ?? 'Transaction' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-800">{{ format_currency($transaction->amount ?? 0) }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M d, Y') ?? 'Today' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-sm">No recent transactions</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Platform Statistics -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Platform Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Volume (24h)</span>
                        <span class="font-bold text-gray-800">{{ format_currency($totalVolume24h ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Deposits</span>
                        <span class="font-bold text-green-600">{{ format_currency($totalDepositsAmount ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Withdrawals</span>
                        <span class="font-bold text-red-600">{{ format_currency($totalWithdrawalsAmount ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Platform Fee Revenue</span>
                        <span class="font-bold text-blue-600">{{ format_currency($platformFeeRevenue ?? 0) }}</span>
                    </div>
                    <div class="pt-3 border-t">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-800 font-medium">Net Revenue</span>
                            <span class="font-bold {{ ($netRevenue ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }} text-lg">
                                {{ format_currency($netRevenue ?? 0) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center p-4 bg-green-50 rounded-lg">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                    <div>
                        <p class="font-medium text-gray-800">API Status</p>
                        <p class="text-sm text-green-600">Operational</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-green-50 rounded-lg">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                    <div>
                        <p class="font-medium text-gray-800">Database</p>
                        <p class="text-sm text-green-600">Operational</p>
                    </div>
                </div>
                
                <div class="flex items-center p-4 bg-yellow-50 rounded-lg">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                    <div>
                        <p class="font-medium text-gray-800">Email Service</p>
                        <p class="text-sm text-yellow-600">Degraded Performance</p>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection
