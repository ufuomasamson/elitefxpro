<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserWallet;
use App\Models\User;

class FixWalletBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:fix-balances {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix unrealistic wallet balances and reset calculated portfolio values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for unrealistic wallet balances...');
        
        // Define reasonable maximum balances for demo purposes
        $maxBalances = [
            'BTC' => 2.0,      // Max 2 BTC ($130,000)
            'ETH' => 30.0,     // Max 30 ETH ($96,000)
            'ADA' => 10000.0,  // Max 10,000 ADA ($4,500)
            'DOT' => 1000.0,   // Max 1,000 DOT ($7,200)
            'LTC' => 100.0,    // Max 100 LTC ($9,000)
            'XRP' => 5000.0,   // Max 5,000 XRP ($3,000)
            'BCH' => 25.0,     // Max 25 BCH ($9,500)
            'LINK' => 500.0,   // Max 500 LINK ($7,000)
            'BNB' => 15.0,     // Max 15 BNB ($8,700)
            'USDT' => 50000.0, // Max 50,000 USDT ($50,000)
        ];

        $walletsToFix = UserWallet::whereIn('currency', array_keys($maxBalances))
            ->get()
            ->filter(function ($wallet) use ($maxBalances) {
                return $wallet->balance > $maxBalances[$wallet->currency];
            });

        if ($walletsToFix->isEmpty()) {
            $this->info('No unrealistic wallet balances found.');
        } else {
            $this->warn("Found {$walletsToFix->count()} wallets with unrealistic balances:");
            
            foreach ($walletsToFix as $wallet) {
                $user = $wallet->user;
                $oldBalance = $wallet->balance;
                $newBalance = $maxBalances[$wallet->currency];
                $usdValue = $oldBalance * $this->getConversionRate($wallet->currency);
                
                $this->line("  User: {$user->name} ({$user->email})");
                $this->line("  {$wallet->currency}: {$oldBalance} → {$newBalance} (was ~$" . number_format($usdValue, 2) . ")");
                
                if (!$dryRun) {
                    $wallet->balance = $newBalance;
                    $wallet->save();
                }
            }
            
            if (!$dryRun) {
                $this->info("Fixed {$walletsToFix->count()} wallet balances.");
                
                // Reset user wallet_balance fields to be recalculated
                User::whereNotNull('wallet_balance')->update(['wallet_balance' => 0]);
                $this->info('Reset user wallet_balance fields for recalculation.');
            } else {
                $this->comment('Run without --dry-run to apply these changes.');
            }
        }
        
        $this->info('Wallet balance check completed.');
    }
    
    private function getConversionRate($currency)
    {
        try {
            $coinGeckoService = app(\App\Services\CoinGeckoService::class);
            $rates = $coinGeckoService->getSimplePrices();
            return $rates[$currency] ?? 1;
        } catch (\Exception $e) {
            // Fallback rates if service fails
            $fallbackRates = [
                'BTC' => 43000, 'ETH' => 2600, 'ADA' => 0.45, 'DOT' => 7.20,
                'LTC' => 90, 'XRP' => 0.60, 'BCH' => 380, 'LINK' => 14,
                'BNB' => 300, 'USDT' => 1
            ];
            
            return $fallbackRates[$currency] ?? 1;
        }
    }
}
