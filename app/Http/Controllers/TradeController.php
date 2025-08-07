<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\UserWallet;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TradeController extends Controller
{
    /**
     * Display the trade page with chart and trade form.
     */
    public function index()
    {
        $user = Auth::user();
        $wallets = $user->wallets ?? collect();
        
        // Get available crypto symbols for trading
        $availableCryptos = ['BTC', 'ETH', 'USDT', 'BNB', 'XRP']; // This could come from a config or DB
        
        // Get pending deposits and withdrawals for notifications
        $pendingDeposits = $user->deposits()->where('status', 'pending')->count();
        $pendingWithdrawals = $user->withdrawals()->where('status', 'pending')->count();
        
        return view('dashboard.trade', compact('wallets', 'availableCryptos', 'pendingDeposits', 'pendingWithdrawals'));
    }
    
    /**
     * Process a buy trade.
     */
    public function buy(Request $request)
    {
        $validated = $request->validate([
            'crypto_symbol' => 'required|string',
            'amount' => 'required|numeric|min:0.00000001',
            'price' => 'required|numeric|min:0',
        ]);
        
        $user = Auth::user();
        $symbol = $validated['crypto_symbol'];
        $amount = $validated['amount'];
        $price = $validated['price'];
        $totalCost = $amount * $price;
        
        // Check if user has enough USDT balance
        $usdtWallet = $user->wallets()->where('crypto_symbol', 'USDT')->first();
        
        if (!$usdtWallet || $usdtWallet->balance < $totalCost) {
            return back()->with('error', 'Insufficient USDT balance for this trade');
        }
        
        // Begin transaction
        \DB::beginTransaction();
        
        try {
            // Deduct from USDT wallet
            $usdtWallet->balance -= $totalCost;
            $usdtWallet->save();
            
            // Add to crypto wallet (create if doesn't exist)
            $cryptoWallet = $user->wallets()->firstOrCreate(
                ['crypto_symbol' => $symbol],
                ['balance' => 0]
            );
            
            $cryptoWallet->balance += $amount;
            $cryptoWallet->save();
            
            // Create trade record
            $trade = new Trade([
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'direction' => 'buy',
                'price_at_time' => $price,
                'total_amount' => $totalCost,
                'status' => 'completed'
            ]);
            $trade->save();
            
            // Log the buy trade
            Log::channel('trading')->info('Buy trade executed', [
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'total_cost' => $totalCost,
                'trade_id' => $trade->id,
                'usdt_balance_before' => $usdtWallet->balance + $totalCost,
                'usdt_balance_after' => $usdtWallet->balance,
                'crypto_balance_before' => $cryptoWallet->balance - $amount,
                'crypto_balance_after' => $cryptoWallet->balance
            ]);
            
            // Create transaction record
            $transaction = new Transaction([
                'user_id' => $user->id,
                'type' => 'trade',
                'amount' => $totalCost,
                'description' => "Bought {$amount} {$symbol} at {$price} USDT each",
                'status' => 'completed'
            ]);
            $transaction->save();
            
            \DB::commit();
            return back()->with('success', "Successfully bought {$amount} {$symbol}");
            
        } catch (\Exception $e) {
            // Log the error
            Log::channel('error')->error('Buy trade failed', [
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            \DB::rollBack();
            return back()->with('error', 'Trade failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Execute a trade (unified method for both buy and sell).
     */
    public function execute(Request $request)
    {
        $validated = $request->validate([
            'trade_type' => 'required|in:buy,sell',
            'crypto_symbol' => 'required|string',
            'crypto_id' => 'required|string',
            'amount' => 'required|numeric|min:0.00000001',
            'price' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'trading_fee' => 'required|numeric|min:0',
            'estimated_receive' => 'required|numeric|min:0',
        ]);
        
        $user = Auth::user();
        $tradeType = $validated['trade_type'];
        $symbol = $validated['crypto_symbol'];
        $cryptoId = $validated['crypto_id'];
        $amount = $validated['amount'];
        $price = $validated['price'];
        $totalCost = $validated['total_cost'];
        $tradingFee = $validated['trading_fee'];
        $estimatedReceive = $validated['estimated_receive'];
        
        // Begin transaction
        \DB::beginTransaction();
        
        try {
            if ($tradeType === 'buy') {
                // Check if user has enough balance
                if ($user->wallet_balance < $totalCost) {
                    return back()->with('error', 'Insufficient balance for this trade');
                }
                
                // Deduct from user's wallet balance
                $user->wallet_balance -= $totalCost;
                $user->save();
                
                // Add to crypto wallet (create if doesn't exist)
                $cryptoWallet = $user->wallets()->firstOrCreate(
                    ['currency' => $symbol],
                    ['balance' => 0, 'currency_name' => $this->getCurrencyName($symbol)]
                );
                
                $cryptoWallet->balance += $estimatedReceive;
                $cryptoWallet->save();
                
                $description = "Bought {$estimatedReceive} {$symbol} at $" . number_format($price, 2) . " each";
                
            } else { // sell
                // Check if user has enough crypto
                $cryptoWallet = $user->wallets()->where('currency', $symbol)->first();
                
                if (!$cryptoWallet || $cryptoWallet->balance < $amount) {
                    return back()->with('error', 'Insufficient ' . $symbol . ' balance for this trade');
                }
                
                // Deduct from crypto wallet
                $cryptoWallet->balance -= $amount;
                $cryptoWallet->save();
                
                // Add to user's wallet balance (total cost already includes fee deduction for sell)
                $user->wallet_balance += $totalCost;
                $user->save();
                
                $description = "Sold {$amount} {$symbol} at $" . number_format($price, 2) . " each";
            }
            
            // Create trade record
            $trade = new Trade([
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $tradeType === 'buy' ? $estimatedReceive : $amount,
                'direction' => $tradeType,
                'price_at_time' => $price,
                'total_value' => $tradeType === 'buy' ? $totalCost : $estimatedReceive,
                'fee' => $tradingFee,
                'status' => 'completed',
                'executed_at' => now()
            ]);
            $trade->save();
            
            // Create transaction record
            $transaction = new Transaction([
                'user_id' => $user->id,
                'type' => 'trade',
                'amount' => $tradeType === 'buy' ? $totalCost : $estimatedReceive,
                'description' => $description,
                'status' => 'completed'
            ]);
            $transaction->save();
            
            // Log the trade
            Log::channel('trading')->info(ucfirst($tradeType) . ' trade executed', [
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'total_cost' => $totalCost,
                'trading_fee' => $tradingFee,
                'trade_id' => $trade->id,
                'transaction_id' => $transaction->id
            ]);
            
            \DB::commit();
            return back()->with('success', $description . ' successfully!');
            
        } catch (\Exception $e) {
            // Log the error
            Log::channel('error')->error(ucfirst($tradeType) . ' trade failed', [
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            \DB::rollBack();
            return back()->with('error', 'Trade failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Process a sell trade.
     */
    public function sell(Request $request)
    {
        $validated = $request->validate([
            'crypto_symbol' => 'required|string',
            'amount' => 'required|numeric|min:0.00000001',
            'price' => 'required|numeric|min:0',
        ]);
        
        $user = Auth::user();
        $symbol = $validated['crypto_symbol'];
        $amount = $validated['amount'];
        $price = $validated['price'];
        $totalValue = $amount * $price;
        
        // Check if user has enough of the crypto
        $cryptoWallet = $user->wallets()->where('crypto_symbol', $symbol)->first();
        
        if (!$cryptoWallet || $cryptoWallet->balance < $amount) {
            return back()->with('error', "Insufficient {$symbol} balance for this trade");
        }
        
        // Begin transaction
        \DB::beginTransaction();
        
        try {
            // Deduct from crypto wallet
            $cryptoWallet->balance -= $amount;
            $cryptoWallet->save();
            
            // Add to USDT wallet (create if doesn't exist)
            $usdtWallet = $user->wallets()->firstOrCreate(
                ['crypto_symbol' => 'USDT'],
                ['balance' => 0]
            );
            
            $usdtWallet->balance += $totalValue;
            $usdtWallet->save();
            
            // Create trade record
            $trade = new Trade([
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'direction' => 'sell',
                'price_at_time' => $price,
                'total_amount' => $totalValue,
                'status' => 'completed'
            ]);
            $trade->save();
            
            // Log the sell trade
            Log::channel('trading')->info('Sell trade executed', [
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'total_value' => $totalValue,
                'trade_id' => $trade->id,
                'crypto_balance_before' => $cryptoWallet->balance + $amount,
                'crypto_balance_after' => $cryptoWallet->balance,
                'usdt_balance_before' => $usdtWallet->balance - $totalValue,
                'usdt_balance_after' => $usdtWallet->balance
            ]);
            
            // Create transaction record
            $transaction = new Transaction([
                'user_id' => $user->id,
                'type' => 'trade',
                'amount' => $totalValue,
                'description' => "Sold {$amount} {$symbol} at {$price} USDT each",
                'status' => 'completed'
            ]);
            $transaction->save();
            
            \DB::commit();
            return back()->with('success', "Successfully sold {$amount} {$symbol}");
            
        } catch (\Exception $e) {
            // Log the error
            Log::channel('error')->error('Sell trade failed', [
                'user_id' => $user->id,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'price' => $price,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            \DB::rollBack();
            return back()->with('error', 'Trade failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Show trade history.
     */
    public function history()
    {
        $user = Auth::user();
        $trades = $user->trades()->orderBy('created_at', 'desc')->paginate(15);
        
        return view('dashboard.trade.history', compact('trades'));
    }
    
    /**
     * Get currency name from symbol.
     */
    private function getCurrencyName($symbol)
    {
        $currencyNames = [
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum',
            'BNB' => 'Binance Coin',
            'ADA' => 'Cardano',
            'SOL' => 'Solana',
            'DOT' => 'Polkadot',
            'LINK' => 'Chainlink',
            'LTC' => 'Litecoin',
            'AVAX' => 'Avalanche',
            'MATIC' => 'Polygon',
            'UNI' => 'Uniswap',
            'ATOM' => 'Cosmos',
            'ALGO' => 'Algorand',
            'XLM' => 'Stellar',
            'VET' => 'VeChain',
            'FIL' => 'Filecoin',
            'TRX' => 'TRON',
            'EOS' => 'EOS',
            'XMR' => 'Monero',
            'AAVE' => 'Aave'
        ];
        
        return $currencyNames[$symbol] ?? $symbol;
    }
}
