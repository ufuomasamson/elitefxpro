<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'wallet_balance',
        'withdrawal_status',
        'aml_verification_code',
        'fwac_verification_code',
        'tsc_verification_code',
        'aml_code_used',
        'fwac_code_used',
        'tsc_code_used',
        'withdrawal_restriction_notes',
        'language_preference',
        'is_admin',
        'is_active',
        'avatar',
        'phone',
        'country',
        'bio',
        'timezone',
        'last_login_at',
        'two_factor_enabled',
        'trading_settings',
        'notification_settings',
        'api_settings',
        'api_key',
        'api_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'trading_settings' => 'array',
        'notification_settings' => 'array',
        'api_settings' => 'array',
        'wallet_balance' => 'decimal:8',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'aml_code_used' => 'boolean',
        'fwac_code_used' => 'boolean',
        'tsc_code_used' => 'boolean',
        'aml_code_used_at' => 'datetime',
        'fwac_code_used_at' => 'datetime',
        'tsc_code_used_at' => 'datetime',
    ];

    /**
     * Get all transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all deposits for the user.
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    /**
     * Get all withdrawals for the user.
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get all trades for the user.
     */
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Get all user wallets.
     */
    public function wallets()
    {
        return $this->hasMany(UserWallet::class);
    }

    /**
     * Get all chat messages for the user.
     */
    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get wallet balance for specific crypto.
     */
    public function getCryptoBalance($symbol)
    {
        $wallet = $this->wallets()->where('currency', $symbol)->first();
        return $wallet ? $wallet->balance : 0;
    }

    /**
     * Ensure user has a USDT wallet for trading.
     */
    public function ensureUSDTWallet()
    {
        $usdtWallet = $this->wallets()->where('currency', 'USDT')->first();
        
        if (!$usdtWallet) {
            // Create USDT wallet with 0 balance - admin funding required
            $usdtWallet = UserWallet::create([
                'user_id' => $this->id,
                'currency' => 'USDT',
                'currency_name' => 'Tether USD',
                'balance' => 0, // Start with 0 - admin will fund manually
                'locked_balance' => 0,
                'balance_usd' => 0
            ]);
            
            // Log the wallet creation
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'user',
                'action' => 'usdt_wallet_created',
                'message' => 'USDT wallet created for user ' . $this->name . ' - awaiting admin funding',
                'user_id' => $this->id,
            ]);
        }
        
        return $usdtWallet;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Get total portfolio value in USD.
     */
    public function getTotalPortfolioValue()
    {
        // Always calculate fresh from crypto wallets only
        $this->load('wallets'); // Ensure wallets are loaded
        
        $cryptoValue = 0;
        
        foreach ($this->wallets as $wallet) {
            try {
                // Get real-time prices from CoinGecko
                $coinGeckoService = app(\App\Services\CoinGeckoService::class);
                $conversionRates = $coinGeckoService->getSimplePrices();
                
                // Map currency symbols to CoinGecko rates
                $currencyMap = [
                    'BTC' => 'bitcoin',
                    'ETH' => 'ethereum', 
                    'USDT' => 'tether',
                    'USDC' => 'usd-coin',
                    'BNB' => 'binancecoin',
                    'ADA' => 'cardano',
                    'SOL' => 'solana',
                    'DOT' => 'polkadot',
                    'LINK' => 'chainlink',
                    'LTC' => 'litecoin',
                    'XRP' => 'ripple',
                    'BCH' => 'bitcoin-cash'
                ];
                
                $coinId = $currencyMap[$wallet->currency] ?? strtolower($wallet->currency);
                $rate = $conversionRates[$coinId] ?? 1;
                
                $walletValue = $wallet->balance * $rate;
                $cryptoValue += $walletValue;
                
                // Update the wallet's USD value
                $wallet->balance_usd = $walletValue;
                $wallet->save();
                
            } catch (\Exception $e) {
                // Fallback: treat as 1:1 with USD if API fails
                $cryptoValue += $wallet->balance;
                Log::warning("CoinGecko API failed for {$wallet->currency}: " . $e->getMessage());
            }
        }
        
        return $cryptoValue;
    }

    /**
     * Check if user can make withdrawals.
     */
    public function canWithdraw()
    {
        return !$this->needsWithdrawalVerification();
    }

    /**
     * Get withdrawal restriction message.
     */
    public function getWithdrawalRestrictionMessage()
    {
        switch ($this->withdrawal_status) {
            case 'aml_kyc_verification':
                return [
                    'title' => 'AML/KYC Verification',
                    'message' => 'First-time withdrawals require a WAC. Contact support for your code.',
                    'code_field' => 'aml',
                    'required_code' => $this->aml_verification_code,
                    'code_used' => $this->aml_code_used,
                    'step' => 1,
                    'total_steps' => 3
                ];
            case 'aml_security_check':
                return [
                    'title' => 'AML Security Check',
                    'message' => 'First-time withdrawals require a FWAC. Contact support for your code.',
                    'code_field' => 'fwac',
                    'required_code' => $this->fwac_verification_code,
                    'code_used' => $this->fwac_code_used,
                    'step' => 2,
                    'total_steps' => 3
                ];
            case 'regulatory_compliance':
                return [
                    'title' => 'Regulatory Compliance',
                    'message' => "To proceed, you'll need a TSC. Contact support for your code.",
                    'code_field' => 'tsc',
                    'required_code' => $this->tsc_verification_code,
                    'code_used' => $this->tsc_code_used,
                    'step' => 3,
                    'total_steps' => 3
                ];
            default:
                return null;
        }
    }

    /**
     * Get the next required verification step.
     */
    public function getNextVerificationStep()
    {
        // Check in order: AML -> FWAC -> TSC
        if ($this->aml_verification_code && !$this->aml_code_used) {
            return [
                'title' => 'AML/KYC Verification',
                'message' => 'Step 1 of 3: First-time withdrawals require a WAC. Contact support for your code.',
                'code_field' => 'aml',
                'required_code' => $this->aml_verification_code,
                'step' => 1,
                'total_steps' => 3
            ];
        }
        
        if ($this->fwac_verification_code && !$this->fwac_code_used) {
            return [
                'title' => 'AML Security Check',
                'message' => 'Step 2 of 3: First-time withdrawals require a FWAC. Contact support for your code.',
                'code_field' => 'fwac',
                'required_code' => $this->fwac_verification_code,
                'step' => 2,
                'total_steps' => 3
            ];
        }
        
        if ($this->tsc_verification_code && !$this->tsc_code_used) {
            return [
                'title' => 'Regulatory Compliance',
                'message' => 'Step 3 of 3: To proceed, you\'ll need a TSC. Contact support for your code.',
                'code_field' => 'tsc',
                'required_code' => $this->tsc_verification_code,
                'step' => 3,
                'total_steps' => 3
            ];
        }
        
        return null; // All codes verified or none required
    }

    /**
     * Check if user needs verification for withdrawal.
     */
    public function needsWithdrawalVerification()
    {
        return $this->getNextVerificationStep() !== null;
    }

    /**
     * Verify withdrawal code and mark as used.
     */
    public function verifyWithdrawalCode($code)
    {
        $nextStep = $this->getNextVerificationStep();
        
        if (!$nextStep) {
            return false;
        }

        if ($code === $nextStep['required_code']) {
            $codeField = $nextStep['code_field'] . '_code_used';
            $timestampField = $nextStep['code_field'] . '_code_used_at';
            
            $this->$codeField = true;
            $this->$timestampField = now();
            
            // Only set to active if all codes are used
            if (!$this->needsWithdrawalVerification()) {
                $this->withdrawal_status = 'active';
            }
            
            $this->save();
            
            return true;
        }

        return false;
    }
}
