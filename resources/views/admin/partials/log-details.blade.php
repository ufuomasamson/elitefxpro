<div class="space-y-6">
    <!-- Log Header -->
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">Log Information</h4>
                <div class="space-y-2 text-sm">
                    <div><span class="font-medium">ID:</span> {{ $log->id }}</div>
                    <div><span class="font-medium">Level:</span> 
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
                    </div>
                    <div><span class="font-medium">Type:</span> {{ ucfirst($log->type) }}</div>
                    <div><span class="font-medium">Action:</span> {{ $log->action }}</div>
                    <div><span class="font-medium">Timestamp:</span> {{ $log->created_at->format('M d, Y H:i:s') }}</div>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-gray-900 mb-2">User Information</h4>
                <div class="space-y-2 text-sm">
                    @if($log->user)
                        <div><span class="font-medium">User:</span> {{ $log->user->name }}</div>
                        <div><span class="font-medium">Email:</span> {{ $log->user->email }}</div>
                        <div><span class="font-medium">User ID:</span> {{ $log->user->id }}</div>
                    @elseif($log->user_email)
                        <div><span class="font-medium">Email:</span> {{ $log->user_email }}</div>
                        <div><span class="text-gray-500">User account may have been deleted</span></div>
                    @else
                        <div><span class="text-gray-500">System-generated log</span></div>
                    @endif
                    
                    @if($log->ip_address)
                        <div><span class="font-medium">IP Address:</span> {{ $log->ip_address }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Message -->
    <div>
        <h4 class="font-semibold text-gray-900 mb-2">Message</h4>
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-gray-800">{{ $log->message }}</p>
        </div>
    </div>
    
    <!-- Context (if available) -->
    @if($log->context)
    <div>
        <h4 class="font-semibold text-gray-900 mb-2">Additional Context</h4>
        <div class="bg-gray-50 rounded-lg p-4">
            <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre>
        </div>
    </div>
    @endif
    
    <!-- Technical Information -->
    @if($log->file || $log->line || $log->user_agent)
    <div>
        <h4 class="font-semibold text-gray-900 mb-2">Technical Information</h4>
        <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
            @if($log->file)
                <div><span class="font-medium">File:</span> {{ $log->file }}</div>
            @endif
            @if($log->line)
                <div><span class="font-medium">Line:</span> {{ $log->line }}</div>
            @endif
            @if($log->user_agent)
                <div><span class="font-medium">User Agent:</span> <span class="break-all">{{ $log->user_agent }}</span></div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Actions -->
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
        @if($log->level === 'error' || $log->level === 'critical')
            <button onclick="reportIssue({{ $log->id }}); closeLogDetailsModal();" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Report Issue
            </button>
        @endif
        <button onclick="closeLogDetailsModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
            Close
        </button>
    </div>
</div>
