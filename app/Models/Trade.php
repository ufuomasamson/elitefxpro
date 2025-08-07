<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crypto_symbol',
        'amount',
        'direction',
        'price_at_time',
        'total_value',
        'status',
        'fee',
        'executed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'price_at_time' => 'decimal:8',
        'total_value' => 'decimal:8',
        'fee' => 'decimal:8',
        'executed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the trade.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for buy trades.
     */
    public function scopeBuys($query)
    {
        return $query->where('direction', 'buy');
    }

    /**
     * Scope for sell trades.
     */
    public function scopeSells($query)
    {
        return $query->where('direction', 'sell');
    }

    /**
     * Scope for completed trades.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if trade is a buy.
     */
    public function isBuy()
    {
        return $this->direction === 'buy';
    }

    /**
     * Check if trade is a sell.
     */
    public function isSell()
    {
        return $this->direction === 'sell';
    }

    /**
     * Get total with fee.
     */
    public function getTotalWithFeeAttribute()
    {
        return $this->total_value + $this->fee;
    }

    /**
     * Get formatted crypto symbol.
     */
    public function getFormattedSymbolAttribute()
    {
        return strtoupper($this->crypto_symbol);
    }

    /**
     * Accessor for type (alias for direction).
     */
    public function getTypeAttribute()
    {
        return $this->direction;
    }

    /**
     * Accessor for price_eur (alias for price_at_time).
     */
    public function getPriceEurAttribute()
    {
        return $this->price_at_time;
    }
}
