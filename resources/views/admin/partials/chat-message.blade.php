<div class="flex {{ $message->isFromAdmin() ? 'justify-end' : 'justify-start' }}">
    <div class="max-w-xs lg:max-w-md">
        <div class="text-xs text-gray-500 mb-1 {{ $message->isFromAdmin() ? 'text-right' : 'text-left' }}">
            {{ $message->isFromAdmin() ? ($message->admin ? $message->admin->name : 'Admin') : $message->user->name }} 
            • {{ $message->created_at->format('g:i A') }}
        </div>
        <div class="{{ $message->isFromAdmin() ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2 break-words">
            {{ $message->message }}
        </div>
    </div>
</div>
