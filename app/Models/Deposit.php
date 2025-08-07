<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'method',
        'crypto_symbol',
        'proof_file',
        'status',
        'admin_notes',
        'rejection_reason',
        'approved_by',
        'processed_by',
        'approved_at',
        'processed_at',
        'deposit_address',
        'transaction_hash',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the deposit.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the deposit.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the admin who processed the deposit.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for pending deposits.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved deposits.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if deposit is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if deposit is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Get proof file URL.
     */
    public function getProofFileUrlAttribute()
    {
        return $this->proof_file ? asset('storage/' . $this->proof_file) : null;
    }
}
