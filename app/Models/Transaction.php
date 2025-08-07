<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'status',
        'method',
        'reference',
        'description',
        'metadata',
        'processed_at',
        'processed_by',
        'canceled_at',
        'canceled_by',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who processed the transaction.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }
}
