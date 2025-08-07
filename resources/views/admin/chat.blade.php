@extends('layouts.admin')

@section('title', 'Live Chat')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Live Chat</h1>
        <p class="text-gray-600 mt-1">Manage customer conversations and support requests</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="refreshConversations()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refresh</span>
        </button>
        <button onclick="exportChatData()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span>Export Chat Data</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Conversations</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $totalChats ?? 0 }}</dd>
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
                    <dt class="text-sm font-medium text-gray-500 truncate">Unread Messages</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $unreadChats ?? 0 }}</dd>
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
                    <dt class="text-sm font-medium text-gray-500 truncate">Today's Chats</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $todayChats ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Active Chats (24h)</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ $activeChats ?? 0 }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Chat Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 chat-container" style="height: calc(100vh - 400px); min-height: 600px; max-height: 800px;">
    <!-- Conversations List -->
    <div class="lg:col-span-1 bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
        <!-- Filter Header -->
        <div class="bg-gray-50 p-4 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.chat') }}" class="space-y-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search conversations..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Chats</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                    <select name="days" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                        <option value="1" {{ request('days') == '1' ? 'selected' : '' }}>Today</option>
                        <option value="7" {{ request('days') == '7' ? 'selected' : '' }}>7 days</option>
                        <option value="30" {{ request('days') == '30' ? 'selected' : '' }}>30 days</option>
                        <option value="all" {{ request('days') == 'all' ? 'selected' : '' }}>All time</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200 text-sm">
                    Filter
                </button>
            </form>
        </div>
        
        <!-- Conversations -->
        <div class="flex-1 overflow-y-auto conversation-list-area" id="conversations-list" style="height: calc(100% - 180px); min-height: 300px;">
            @forelse($conversations ?? [] as $conversation)
            <div class="conversation-item p-5 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors duration-200" 
                 data-user-id="{{ $conversation->user_id }}"
                 onclick="openConversation({{ $conversation->user_id }})">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 relative">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <span class="text-red-600 font-medium text-base">
                                {{ $conversation->user ? substr($conversation->user->name, 0, 1) : 'U' }}
                            </span>
                        </div>
                        @if($conversation->unread_count > 0)
                            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                {{ $conversation->unread_count }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-base font-semibold text-gray-900 truncate">
                                {{ $conversation->user->name ?? 'Unknown User' }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($conversation->last_message_time)->diffForHumans() }}
                            </p>
                        </div>
                        <p class="text-sm text-gray-500 mb-2">{{ $conversation->user->email ?? 'No email' }}</p>
                        <p class="text-sm text-gray-700 truncate leading-relaxed mb-2">
                            {{ $conversation->last_user_message ?? $conversation->last_admin_message ?? 'No messages yet' }}
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ $conversation->message_count }} messages</span>
                            @if($conversation->unread_count > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $conversation->unread_count }} unread
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No conversations</h3>
                <p class="text-gray-500">No chat conversations found matching your filters.</p>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Chat Messages Area -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-lg overflow-hidden flex flex-col">
        <!-- Chat Header -->
        <div class="bg-gray-50 p-4 border-b border-gray-200 flex-shrink-0" id="chat-header" style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-red-600 font-medium text-sm" id="chat-user-avatar"></span>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900" id="chat-user-name"></h3>
                        <p class="text-sm text-gray-500" id="chat-user-email"></p>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button onclick="deleteConversation()" class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 chat-messages-area" id="chat-messages" style="height: calc(100% - 140px); min-height: 300px;">
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-500 mb-2">Select a conversation</h3>
                <p class="text-gray-400">Choose a conversation from the list to start chatting</p>
            </div>
        </div>
        
        <!-- Message Input -->
        <div class="border-t border-gray-200 p-4 bg-gray-50 flex-shrink-0" id="message-input-area" style="display: none;">
            <form id="send-message-form" class="flex space-x-3">
                <div class="flex-1">
                    <textarea id="message-input" rows="2" placeholder="Type your message..." 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none text-base"
                              maxlength="1000"></textarea>
                </div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-200 flex items-center space-x-2 self-end">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Send</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let currentUserId = null;
let chatRefreshInterval = null;

// Add CSS for better scrolling
const style = document.createElement('style');
style.textContent = `
    .chat-container {
        overflow: hidden;
    }
    .chat-messages-area {
        max-height: 100%;
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    .conversation-list-area {
        max-height: 100%;
        overflow-y: auto;
    }
    @media (max-height: 700px) {
        .chat-container {
            height: calc(100vh - 250px) !important;
            min-height: 500px !important;
        }
    }
`;
document.head.appendChild(style);

// Open conversation
function openConversation(userId) {
    currentUserId = userId;
    
    // Update UI to show loading
    document.getElementById('chat-messages').innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600 mx-auto"></div></div>';
    
    fetch(`/admin/chat/${userId}/messages`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessages(data.messages, data.user);
                document.getElementById('chat-header').style.display = 'block';
                document.getElementById('message-input-area').style.display = 'block';
                
                // Update conversation item to remove unread indicator
                const conversationItem = document.querySelector(`[data-user-id="${userId}"]`);
                if (conversationItem) {
                    const unreadBadges = conversationItem.querySelectorAll('.bg-red-500, .bg-red-100');
                    unreadBadges.forEach(badge => badge.remove());
                }
                
                // Start auto-refresh for this conversation
                startChatRefresh();
            } else {
                alert('Failed to load conversation');
            }
        })
        .catch(error => {
            console.error('Error loading conversation:', error);
            alert('Error loading conversation');
        });
}

// Display messages
function displayMessages(messages, user) {
    const chatMessages = document.getElementById('chat-messages');
    const chatUserName = document.getElementById('chat-user-name');
    const chatUserEmail = document.getElementById('chat-user-email');
    const chatUserAvatar = document.getElementById('chat-user-avatar');
    
    // Update user info
    chatUserName.textContent = user.name;
    chatUserEmail.textContent = user.email;
    chatUserAvatar.textContent = user.name.charAt(0);
    
    // Display messages
    let messagesHtml = '';
    messages.forEach(message => {
        const isAdmin = message.sender_type === 'admin';
        const messageClass = isAdmin ? 'justify-end' : 'justify-start';
        const bubbleClass = isAdmin ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-900';
        const senderName = isAdmin ? (message.admin ? message.admin.name : 'Admin') : user.name;
        const avatarClass = isAdmin ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600';
        
        messagesHtml += `
            <div class="flex ${messageClass} mb-6">
                <div class="max-w-md lg:max-w-lg">
                    <div class="flex items-center mb-2 ${isAdmin ? 'justify-end' : 'justify-start'}">
                        ${!isAdmin ? `<div class="w-8 h-8 ${avatarClass} rounded-full flex items-center justify-center mr-2">
                            <span class="text-sm font-medium">${user.name.charAt(0)}</span>
                        </div>` : ''}
                        <div class="text-sm font-medium text-gray-700 ${isAdmin ? 'text-right' : 'text-left'}">
                            ${senderName}
                        </div>
                        ${isAdmin ? `<div class="w-8 h-8 ${avatarClass} rounded-full flex items-center justify-center ml-2">
                            <span class="text-sm font-medium">A</span>
                        </div>` : ''}
                    </div>
                    <div class="${bubbleClass} rounded-2xl px-6 py-4 break-words shadow-sm">
                        <div class="text-base leading-relaxed">${message.message}</div>
                    </div>
                    <div class="text-xs text-gray-500 mt-2 ${isAdmin ? 'text-right' : 'text-left'}">
                        ${new Date(message.created_at).toLocaleString()}
                    </div>
                </div>
            </div>
        `;
    });
    
    chatMessages.innerHTML = messagesHtml || '<div class="text-center py-8 text-gray-500">No messages yet</div>';
    
    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Send message
document.getElementById('send-message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!currentUserId) return;
    
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Disable form while sending
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mx-auto"></div>';
    
    fetch(`/admin/chat/${currentUserId}/send`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            // Refresh conversation to show new message
            openConversation(currentUserId);
        } else {
            alert('Failed to send message: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Error sending message');
    })
    .finally(() => {
        // Re-enable form
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg><span>Send</span>';
    });
});

// Delete conversation
function deleteConversation() {
    if (!currentUserId) return;
    
    if (confirm('Are you sure you want to delete this entire conversation? This action cannot be undone.')) {
        fetch(`/admin/chat/${currentUserId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Conversation deleted successfully');
                window.location.reload();
            } else {
                alert('Failed to delete conversation: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting conversation:', error);
            alert('Error deleting conversation');
        });
    }
}

// Auto-refresh chat
function startChatRefresh() {
    if (chatRefreshInterval) {
        clearInterval(chatRefreshInterval);
    }
    
    chatRefreshInterval = setInterval(() => {
        if (currentUserId) {
            // Silently refresh messages
            fetch(`/admin/chat/${currentUserId}/messages`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayMessages(data.messages, data.user);
                    }
                })
                .catch(error => console.error('Auto-refresh error:', error));
        }
    }, 5000); // Refresh every 5 seconds
}

// Refresh conversations
function refreshConversations() {
    window.location.reload();
}

// Export chat data
function exportChatData() {
    alert('Chat export functionality will be implemented in a future update.');
}

// Auto-submit on Enter (but not Shift+Enter)
document.getElementById('message-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('send-message-form').dispatchEvent(new Event('submit'));
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (chatRefreshInterval) {
        clearInterval(chatRefreshInterval);
    }
});
</script>
@endsection
