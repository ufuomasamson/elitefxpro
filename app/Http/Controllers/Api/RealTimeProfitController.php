<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trade;
use App\Models\User;
use App\Services\CoinGeckoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RealTimeProfitController extends Controller
{
    protected $coinGeckoService;

    public function __construct(CoinGeckoService $coinGeckoService)
    {
        $this->coinGeckoService = $coinGeckoService;
    }

    /**
     * Get real-time profit/loss data for the authenticated user
     */
    public function getRealTimeProfit(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Get all user's trades
            $allTrades = Trade::where('user_id', $user->id)
                ->where('status', 'completed')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($allTrades->isEmpty()) {
                return response()->json([
                    'total_profit' => 0,
                    'profit_percentage' => 0,
                    'total_investment' => 0,
                    'current_value' => $user->wallet_balance ?? 0,
                    'has_trades' => false,
                    'holdings' => []
                ]);
            }

        // Get current market prices
        $marketPrices = $this->coinGeckoService->getSimplePrices();            // Calculate holdings and unrealized P&L
            $holdings = [];
            $totalRealizedProfit = 0;
            $totalInvested = 0;
            $totalCurrentValue = 0;
            
            // Group trades by crypto symbol
            $tradesBySymbol = $allTrades->groupBy('crypto_symbol');
            
            foreach ($tradesBySymbol as $symbol => $trades) {
                $buyTrades = $trades->where('direction', 'buy');
                $sellTrades = $trades->where('direction', 'sell');
                
                // Calculate total bought and sold amounts
                $totalBought = $buyTrades->sum('amount');
                $totalSold = $sellTrades->sum('amount');
                $currentHolding = $totalBought - $totalSold;
                
                // Calculate total invested in this crypto
                $totalBoughtCost = $buyTrades->sum(function($trade) {
                    return $trade->total_value + $trade->fee;
                });
                $totalInvested += $totalBoughtCost;
                
                // Calculate realized profit from completed sells
                if ($sellTrades->count() > 0) {
                    $totalBoughtAmount = $buyTrades->sum('amount');
                    $totalBoughtValue = $buyTrades->sum('total_value');
                    
                    if ($totalBoughtAmount > 0) {
                        $avgCostPerUnit = $totalBoughtValue / $totalBoughtAmount;
                        
                        foreach ($sellTrades as $sellTrade) {
                            $sellAmount = $sellTrade->amount;
                            $sellValue = $sellTrade->total_value - $sellTrade->fee;
                            $costOfSoldUnits = $sellAmount * $avgCostPerUnit;
                            $profitFromThisSell = $sellValue - $costOfSoldUnits;
                            $totalRealizedProfit += $profitFromThisSell;
                        }
                    }
                }
                
                // If user still holds this crypto, calculate unrealized P&L
                if ($currentHolding > 0) {
                    $currentPrice = $marketPrices[$symbol] ?? 0;
                    $currentValue = $currentHolding * $currentPrice;
                    $totalCurrentValue += $currentValue;
                    
                    // Calculate unrealized P&L for current holdings
                    $totalBoughtAmount = $buyTrades->sum('amount');
                    $totalBoughtValue = $buyTrades->sum('total_value');
                    
                    if ($totalBoughtAmount > 0) {
                        $avgCostPerUnit = $totalBoughtValue / $totalBoughtAmount;
                        $totalCostForCurrentHolding = $currentHolding * $avgCostPerUnit;
                        $unrealizedPnL = $currentValue - $totalCostForCurrentHolding;
                        $unrealizedPnLPercentage = $totalCostForCurrentHolding > 0 ? 
                            ($unrealizedPnL / $totalCostForCurrentHolding) * 100 : 0;
                        
                        $holdings[] = [
                            'symbol' => $symbol,
                            'amount' => $currentHolding,
                            'avg_cost' => $avgCostPerUnit,
                            'current_price' => $currentPrice,
                            'current_value' => $currentValue,
                            'invested_amount' => $totalCostForCurrentHolding,
                            'unrealized_pnl' => $unrealizedPnL,
                            'unrealized_pnl_percentage' => $unrealizedPnLPercentage
                        ];
                    }
                }
            }
            
            // Add wallet balance to current value
            $walletBalance = $user->wallet_balance ?? 0;
            $totalCurrentValue += $walletBalance;
            
            // Calculate total profit (realized + unrealized)
            $totalUnrealizedPnL = array_sum(array_column($holdings, 'unrealized_pnl'));
            $totalProfit = $totalRealizedProfit + $totalUnrealizedPnL;
            
            // Calculate overall profit percentage
            $profitPercentage = $totalInvested > 0 ? ($totalProfit / $totalInvested) * 100 : 0;
            
            return response()->json([
                'total_profit' => round($totalProfit, 2),
                'profit_percentage' => round($profitPercentage, 2),
                'realized_profit' => round($totalRealizedProfit, 2),
                'unrealized_profit' => round($totalUnrealizedPnL, 2),
                'total_investment' => round($totalInvested, 2),
                'current_value' => round($totalCurrentValue, 2),
                'wallet_balance' => round($walletBalance, 2),
                'has_trades' => true,
                'holdings' => $holdings,
                'last_updated' => now()->format('H:i:s')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Real-time profit calculation error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to calculate real-time profit'], 500);
        }
    }
}
