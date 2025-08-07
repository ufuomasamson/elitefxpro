<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'type',
        'action',
        'user_id',
        'user_email',
        'ip_address',
        'user_agent',
        'message',
        'context',
        'file',
        'line',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Static methods for logging
    public static function logInfo($type, $action, $message, $context = null, $userId = null)
    {
        return self::createLog('info', $type, $action, $message, $context, $userId);
    }

    public static function logWarning($type, $action, $message, $context = null, $userId = null)
    {
        return self::createLog('warning', $type, $action, $message, $context, $userId);
    }

    public static function logError($type, $action, $message, $context = null, $userId = null)
    {
        return self::createLog('error', $type, $action, $message, $context, $userId);
    }

    public static function logCritical($type, $action, $message, $context = null, $userId = null)
    {
        return self::createLog('critical', $type, $action, $message, $context, $userId);
    }

    private static function createLog($level, $type, $action, $message, $context = null, $userId = null)
    {
        $request = request();
        
        return self::create([
            'level' => $level,
            'type' => $type,
            'action' => $action,
            'user_id' => $userId ?: auth()->id(),
            'user_email' => $userId ? User::find($userId)?->email : auth()->user()?->email,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'message' => $message,
            'context' => $context,
            'file' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['file'] ?? null,
            'line' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['line'] ?? null,
        ]);
    }

    // Helper methods
    public function getLevelColorAttribute()
    {
        return match($this->level) {
            'info' => 'blue',
            'warning' => 'yellow',
            'error' => 'red',
            'critical' => 'purple',
            default => 'gray'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'admin' => 'shield-check',
            'trading' => 'chart-line',
            'wallet' => 'wallet',
            'security' => 'lock',
            'system' => 'cog',
            'user' => 'user',
            default => 'information-circle'
        };
    }
}
