<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'sender_type',
        'admin_id',
        'is_read',
        'attachments',
        'ip_address',
        'user_agent',
        'read_at'
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Get the user that sent the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that replied (if applicable)
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Scope to get messages for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get admin messages
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }

    /**
     * Scope to get user messages
     */
    public function scopeFromUser($query)
    {
        return $query->where('sender_type', 'user');
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Check if message is from admin
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }

    /**
     * Check if message is from user
     */
    public function isFromUser(): bool
    {
        return $this->sender_type === 'user';
    }
}
