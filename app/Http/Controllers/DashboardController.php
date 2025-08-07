<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Trade;
use App\Models\UserWallet;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's wallet balances
        $wallets = $user->wallets()->get();
        
        // Get recent transactions (last 10)
        $recentTransactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get recent trades (last 5)
        $recentTrades = $user->trades()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate total portfolio value
        $totalPortfolioValue = $user->getTotalPortfolioValue();
        
        // Get pending deposits and withdrawals count
        $pendingDeposits = $user->deposits()->where('status', 'pending')->count();
        $pendingWithdrawals = $user->withdrawals()->where('status', 'pending')->count();
        
        // Calculate profit/loss data
        $profitData = $this->calculateUserProfits($user);
        
        return view('dashboard', compact(
            'user',
            'wallets',
            'recentTransactions',
            'recentTrades',
            'totalPortfolioValue',
            'pendingDeposits',
            'pendingWithdrawals',
            'profitData'
        ));
    }

    /**
     * Display transaction history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->transactions()->orderBy('created_at', 'desc');
        
        // Filter by type if specified
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by status if specified
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range if specified
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $transactions = $query->paginate(20);
        
        return view('dashboard.history', compact('transactions'));
    }

    /**
     * Get recent transactions for AJAX requests.
     */
    public function getRecentTransactions(Request $request)
    {
        $user = Auth::user();
        
        $transactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->limit($request->get('limit', 10))
            ->get();
        
        return response()->json($transactions);
    }
    
    /**
     * Calculate user's profit/loss data.
     */
    private function calculateUserProfits($user)
    {
        $totalInvestment = 0;
        $currentValue = 0;
        $totalProfit = 0;
        $profitPercentage = 0;
        $holdings = [];
        
        // Get all user's trades grouped by crypto
        $trades = $user->trades()
            ->where('status', 'completed')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('crypto_symbol');
        
        foreach ($trades as $symbol => $symbolTrades) {
            $cryptoId = $this->getCryptoId($symbol);
            $totalBought = 0;
            $totalSold = 0;
            $totalBuyValue = 0;
            $totalSellValue = 0;
            
            foreach ($symbolTrades as $trade) {
                if ($trade->direction === 'buy') {
                    $totalBought += $trade->amount;
                    $totalBuyValue += $trade->total_value;
                } else {
                    $totalSold += $trade->amount;
                    $totalSellValue += $trade->total_value;
                }
            }
            
            $remainingAmount = $totalBought - $totalSold;
            
            if ($remainingAmount > 0) {
                // Calculate average buy price for remaining holdings
                $avgBuyPrice = $totalBought > 0 ? ($totalBuyValue / $totalBought) : 0;
                $investedAmount = $remainingAmount * $avgBuyPrice;
                $totalInvestment += $investedAmount;
                
                // Get current price and calculate current value
                $currentPrice = $this->getCurrentPrice($cryptoId);
                $currentHoldingValue = $remainingAmount * $currentPrice;
                $currentValue += $currentHoldingValue;
                
                $holdingProfit = $currentHoldingValue - $investedAmount;
                
                $holdings[] = [
                    'symbol' => $symbol,
                    'amount' => $remainingAmount,
                    'invested_amount' => $investedAmount,
                    'current_value' => $currentHoldingValue,
                    'profit' => $holdingProfit,
                    'profit_percentage' => $investedAmount > 0 ? (($holdingProfit / $investedAmount) * 100) : 0,
                    'current_price' => $currentPrice,
                    'avg_buy_price' => $avgBuyPrice
                ];
            }
        }
        
        $totalProfit = $currentValue - $totalInvestment;
        $profitPercentage = $totalInvestment > 0 ? (($totalProfit / $totalInvestment) * 100) : 0;
        
        return [
            'total_investment' => $totalInvestment,
            'current_value' => $currentValue,
            'total_profit' => $totalProfit,
            'profit_percentage' => $profitPercentage,
            'holdings' => $holdings
        ];
    }
    
    /**
     * Get CoinGecko ID from crypto symbol.
     */
    private function getCryptoId($symbol)
    {
        $symbolMap = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'BNB' => 'binancecoin',
            'ADA' => 'cardano',
            'SOL' => 'solana',
            'DOT' => 'polkadot',
            'LINK' => 'chainlink',
            'LTC' => 'litecoin',
            'AVAX' => 'avalanche-2',
            'MATIC' => 'polygon',
            'UNI' => 'uniswap',
            'ATOM' => 'cosmos',
            'ALGO' => 'algorand',
            'XLM' => 'stellar',
            'VET' => 'vechain',
            'FIL' => 'filecoin',
            'TRX' => 'tron',
            'EOS' => 'eos',
            'XMR' => 'monero',
            'AAVE' => 'aave'
        ];
        
        return $symbolMap[$symbol] ?? 'bitcoin';
    }
    
    /**
     * Get current price from CoinGecko API.
     */
    private function getCurrentPrice($cryptoId)
    {
        try {
            $url = "https://api.coingecko.com/api/v3/simple/price?ids={$cryptoId}&vs_currencies=usd";
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            return $data[$cryptoId]['usd'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
