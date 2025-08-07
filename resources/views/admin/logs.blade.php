@extends('layouts.admin')

@section('title', 'System Logs')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">System Logs</h1>
        <p class="text-gray-600 mt-1">Monitor and track all platform activities</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="exportLogs()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Export Logs</span>
        </button>
        <button onclick="clearOldLogs()" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            <span>Clear Old Logs</span>
        </button>
        <button onclick="refreshLogs()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refresh</span>
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Logs Today</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $todayLogs ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Errors Today</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $todayErrors ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Warnings Today</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $todayWarnings ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Critical Today</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $todayCritical ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="bg-white rounded-xl shadow-lg p-6 mb-6">
    <form method="GET" action="{{ route('admin.logs') }}" class="space-y-4 md:space-y-0 md:flex md:items-center md:space-x-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs by message, action, or user..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div class="flex space-x-4">
            <select name="level" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Levels</option>
                <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                <option value="critical" {{ request('level') == 'critical' ? 'selected' : '' }}>Critical</option>
            </select>
            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Types</option>
                <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="trading" {{ request('type') == 'trading' ? 'selected' : '' }}>Trading</option>
                <option value="wallet" {{ request('type') == 'wallet' ? 'selected' : '' }}>Wallet</option>
                <option value="security" {{ request('type') == 'security' ? 'selected' : '' }}>Security</option>
                <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                <option value="user" {{ request('type') == 'user' ? 'selected' : '' }}>User</option>
            </select>
            <select name="days" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="1" {{ request('days', '1') == '1' ? 'selected' : '' }}>Today</option>
                <option value="7" {{ request('days') == '7' ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ request('days') == '30' ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ request('days') == '90' ? 'selected' : '' }}>Last 90 days</option>
            </select>
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                Filter
            </button>
            @if(request()->hasAny(['search', 'level', 'type', 'days']))
                <a href="{{ route('admin.logs') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Log Tabs -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6">
            <button onclick="showLogTab('all')" class="log-tab-btn active border-red-500 text-red-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                All Logs
            </button>
            <button onclick="showLogTab('admin')" class="log-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Admin Actions
            </button>
            <button onclick="showLogTab('trading')" class="log-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Trading Activity
            </button>
            <button onclick="showLogTab('wallet')" class="log-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Wallet Operations
            </button>
            <button onclick="showLogTab('security')" class="log-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Security Events
            </button>
            <button onclick="showLogTab('errors')" class="log-tab-btn border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Errors & Critical
            </button>
        </nav>
    </div>

    <!-- Logs Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="logs-table-body">
                @forelse($logs ?? [] as $log)
                <tr class="hover:bg-gray-50 log-row" data-type="{{ $log->type }}" data-level="{{ $log->level }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $levelColors = [
                                'info' => 'bg-blue-100 text-blue-800',
                                'warning' => 'bg-yellow-100 text-yellow-800',
                                'error' => 'bg-red-100 text-red-800',
                                'critical' => 'bg-purple-100 text-purple-800'
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $levelColors[$log->level] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($log->level) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @php
                                $typeColors = [
                                    'admin' => 'text-red-600',
                                    'trading' => 'text-blue-600',
                                    'wallet' => 'text-green-600',
                                    'security' => 'text-purple-600',
                                    'system' => 'text-gray-600',
                                    'user' => 'text-indigo-600'
                                ];
                            @endphp
                            <svg class="w-5 h-5 mr-2 {{ $typeColors[$log->type] ?? 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($log->type === 'admin')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                @elseif($log->type === 'trading')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                @elseif($log->type === 'wallet')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                @elseif($log->type === 'security')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                @elseif($log->type === 'system')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                @endif
                            </svg>
                            <span class="text-sm font-medium text-gray-900">{{ ucfirst($log->type) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $log->action }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($log->user)
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                <div class="text-gray-500">{{ $log->user->email }}</div>
                            </div>
                        @elseif($log->user_email)
                            <div class="text-sm text-gray-500">{{ $log->user_email }}</div>
                        @else
                            <span class="text-sm text-gray-400">System</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $log->message }}">
                            {{ $log->message }}
                        </div>
                        @if($log->ip_address)
                            <div class="text-xs text-gray-500 mt-1">IP: {{ $log->ip_address }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $log->created_at->format('M d, Y') }}</div>
                        <div class="text-xs">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="viewLogDetails({{ $log->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                            View Details
                        </button>
                        @if($log->level === 'error' || $log->level === 'critical')
                            <button onclick="reportIssue({{ $log->id }})" class="text-red-600 hover:text-red-900">
                                Report
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">No logs found</h3>
                            <p class="text-gray-500">No system logs match your current filters.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($logs) && $logs->hasPages())
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<!-- Log Details Modal -->
<div id="logDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Log Details</h3>
                <button onclick="closeLogDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="logDetailsContent">
                <!-- Log details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Tab functionality
function showLogTab(tabType) {
    // Update tab buttons
    document.querySelectorAll('.log-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-red-500', 'text-red-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    event.target.classList.add('active', 'border-red-500', 'text-red-600');
    event.target.classList.remove('border-transparent', 'text-gray-500');
    
    // Filter rows
    const rows = document.querySelectorAll('.log-row');
    rows.forEach(row => {
        if (tabType === 'all') {
            row.style.display = '';
        } else if (tabType === 'errors') {
            const level = row.dataset.level;
            row.style.display = (level === 'error' || level === 'critical') ? '' : 'none';
        } else {
            const type = row.dataset.type;
            row.style.display = (type === tabType) ? '' : 'none';
        }
    });
}

// Log details modal
function viewLogDetails(logId) {
    fetch(`/admin/logs/${logId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('logDetailsContent').innerHTML = data.html;
                document.getElementById('logDetailsModal').classList.remove('hidden');
            } else {
                alert('Failed to load log details');
            }
        })
        .catch(error => {
            alert('Error loading log details: ' + error.message);
        });
}

function closeLogDetailsModal() {
    document.getElementById('logDetailsModal').classList.add('hidden');
}

// Actions
function refreshLogs() {
    window.location.reload();
}

function exportLogs() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'true');
    window.open(`{{ route('admin.logs') }}?${params.toString()}`, '_blank');
}

function clearOldLogs() {
    if (confirm('Are you sure you want to clear logs older than 90 days? This action cannot be undone.')) {
        fetch('/admin/logs/clear-old', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Successfully cleared ${data.count} old logs`);
                window.location.reload();
            } else {
                alert('Failed to clear logs: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error clearing logs: ' + error.message);
        });
    }
}

function reportIssue(logId) {
    if (confirm('Report this issue to the development team?')) {
        fetch(`/admin/logs/${logId}/report`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Issue reported successfully');
            } else {
                alert('Failed to report issue: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error reporting issue: ' + error.message);
        });
    }
}

// Close modal when clicking outside
document.getElementById('logDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogDetailsModal();
    }
});
</script>
@endsection
