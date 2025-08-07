<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency',
        'currency_name',
        'wallet_address',
        'network',
        'qr_code_image',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active wallets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted currency name.
     */
    public function getFormattedNameAttribute()
    {
        return $this->currency_name . ' (' . strtoupper($this->currency) . ')';
    }
}
