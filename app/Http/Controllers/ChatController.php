<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Get chat messages for the authenticated user
     */
    public function getMessages(Request $request)
    {
        try {
            $messages = ChatMessage::with(['admin'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Mark admin messages as read
            ChatMessage::where('user_id', Auth::id())
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'messages' => $messages
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get user chat messages error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load messages'
            ], 500);
        }
    }
    
    /**
     * Send a message from user
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);
        
        try {
            $message = ChatMessage::create([
                'user_id' => Auth::id(),
                'message' => $request->message,
                'sender_type' => 'user',
                'is_read' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $message->load(['user']);
            
            SystemLog::logInfo('user', 'send_chat_message', 'User sent chat message to admin', [
                'user_id' => Auth::id(),
                'message_length' => strlen($request->message)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            Log::error('Send user chat message error: ' . $e->getMessage());
            SystemLog::logError('user', 'send_chat_message_error', 'Error sending user chat message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }
    
    /**
     * Get unread message count for user
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $count = ChatMessage::where('user_id', Auth::id())
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get unread chat count error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }
}
