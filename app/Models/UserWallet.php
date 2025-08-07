<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency',
        'currency_name',
        'balance',
        'locked_balance',
        'balance_usd',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'locked_balance' => 'decimal:8',
        'balance_usd' => 'decimal:2',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get available balance (total - locked).
     */
    public function getAvailableBalanceAttribute()
    {
        return $this->balance - $this->locked_balance;
    }

    /**
     * Lock balance for pending trades.
     */
    public function lockBalance($amount)
    {
        if ($this->available_balance >= $amount) {
            $this->locked_balance += $amount;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Unlock balance after trade completion/cancellation.
     */
    public function unlockBalance($amount)
    {
        $this->locked_balance = max(0, $this->locked_balance - $amount);
        $this->save();
    }

    /**
     * Add to balance.
     */
    public function addBalance($amount)
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Subtract from balance.
     */
    public function subtractBalance($amount)
    {
        if ($this->available_balance >= $amount) {
            $this->balance -= $amount;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Get formatted crypto symbol.
     */
    public function getFormattedSymbolAttribute()
    {
        return strtoupper($this->currency);
    }

    /**
     * Get crypto symbol (alias for currency).
     */
    public function getCryptoSymbolAttribute()
    {
        return $this->currency;
    }
}
