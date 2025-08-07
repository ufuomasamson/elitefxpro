<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'crypto_symbol',
        'withdrawal_address',
        'status',
        'reference',
        'admin_notes',
        'processed_by',
        'processed_at',
        'transaction_hash',
        'fee',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'fee' => 'decimal:8',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the withdrawal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who processed the withdrawal.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for pending withdrawals.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved withdrawals.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if withdrawal is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Get net amount after fee.
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->fee;
    }

    /**
     * Generate unique reference number.
     */
    public static function generateReference()
    {
        return 'WD-' . strtoupper(uniqid());
    }
}
