<?php

namespace App\Http\Controllers;

use App\Models\UserWallet;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class WalletController extends Controller
{
    /**
     * Display the wallet overview.
     */
    public function index()
    {
        $user = Auth::user();
        $wallets = $user->wallets()->get();
        
        // Get recent wallet transactions (funding, trades, etc.)
        $recentTransactions = $user->transactions()
            ->whereIn('type', ['fund', 'deposit', 'withdrawal', 'trade'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        // Calculate total portfolio value
        $totalPortfolioValue = $user->getTotalPortfolioValue();
        
        // Get real-time conversion rates for USD display
        $coinGeckoService = app(\App\Services\CoinGeckoService::class);
        $conversionRates = $coinGeckoService->getSimplePrices();
        
        // Get pending deposits and withdrawals for notifications
        $pendingDeposits = $user->deposits()->where('status', 'pending')->count();
        $pendingWithdrawals = $user->withdrawals()->where('status', 'pending')->count();
        
        return view('wallet.index', compact('wallets', 'recentTransactions', 'totalPortfolioValue', 'conversionRates', 'pendingDeposits', 'pendingWithdrawals'));
    }
    
    /**
     * Get balance for a specific crypto.
     */
    public function balance($symbol)
    {
        $user = Auth::user();
        $wallet = $user->wallets()->where('crypto_symbol', $symbol)->first();
        
        if (!$wallet) {
            return response()->json(['balance' => 0]);
        }
        
        return response()->json(['balance' => $wallet->balance]);
    }
    
    /**
     * API endpoint for crypto prices.
     */
    public function getCryptoPrices()
    {
        // This would make an API call to CoinGecko or another service
        // Here's a simplified mock implementation
        try {
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'bitcoin,ethereum,tether,binancecoin,ripple',
                'vs_currencies' => 'usd'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Format the data for frontend use
                $prices = [
                    'BTC' => $data['bitcoin']['usd'] ?? 0,
                    'ETH' => $data['ethereum']['usd'] ?? 0,
                    'USDT' => $data['tether']['usd'] ?? 0,
                    'BNB' => $data['binancecoin']['usd'] ?? 0,
                    'XRP' => $data['ripple']['usd'] ?? 0
                ];
                
                return $prices;
            }
            
            // Fallback if API call fails
            return [
                'BTC' => 50000,
                'ETH' => 3000,
                'USDT' => 1,
                'BNB' => 300,
                'XRP' => 0.5
            ];
            
        } catch (\Exception $e) {
            // If the API fails, return default values
            return [
                'BTC' => 50000,
                'ETH' => 3000,
                'USDT' => 1,
                'BNB' => 300,
                'XRP' => 0.5
            ];
        }
    }
    
    /**
     * Calculate portfolio value in USD.
     */
    public function getPortfolioValue()
    {
        $user = Auth::user();
        $wallets = $user->wallets;
        $prices = $this->getCryptoPrices();
        
        $totalValue = 0;
        
        foreach ($wallets as $wallet) {
            $symbol = $wallet->crypto_symbol;
            if (isset($prices[$symbol])) {
                $totalValue += $wallet->balance * $prices[$symbol];
            }
        }
        
        return response()->json([
            'portfolio_value' => $totalValue,
            'wallets' => $wallets,
            'prices' => $prices
        ]);
    }
}
