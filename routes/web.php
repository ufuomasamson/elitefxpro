<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes - Clean Working Version
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Test route
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Elite Forex Pro is working!',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
})->name('api.test');

// Language Routes
Route::post('/language/detect', function () {
    $request = request();
    
    // Get browser language from Accept-Language header
    $acceptLanguage = $request->header('Accept-Language', 'en');
    $browserLang = substr($acceptLanguage, 0, 2);
    
    // Supported languages
    $supportedLanguages = ['en', 'it', 'fr', 'de', 'ru'];
    
    // Check if detected language is supported
    $detected = in_array($browserLang, $supportedLanguages) ? $browserLang : 'en';
    
    return response()->json([
        'success' => true,
        'detected' => $detected
    ]);
})->name('language.detect');

Route::post('/language/switch', function () {
    $request = request();
    
    $request->validate([
        'language' => 'required|string|in:en,it,fr,de,ru'
    ]);
    
    // Store language preference in session
    session(['language' => $request->language]);
    
    return response()->json([
        'success' => true,
        'language' => $request->language
    ]);
})->name('language.switch');

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    $request = request();
    
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function () {
    $request = request();
    
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'email_verified_at' => now(),
        'wallet_balance' => 0, // Start with 0 main balance
    ]);

    // Create USDT wallet with 0 balance - admin will fund manually
    \App\Models\UserWallet::create([
        'user_id' => $user->id,
        'currency' => 'USDT',
        'currency_name' => 'Tether USD',
        'balance' => 0, // Start with 0 - admin will fund after verification
        'locked_balance' => 0,
        'balance_usd' => 0
    ]);

    // Log the new user setup
    \App\Models\SystemLog::create([
        'level' => 'info',
        'type' => 'user',
        'action' => 'new_user_registration',
        'message' => 'New user ' . $user->name . ' registered - awaiting admin funding',
        'user_id' => $user->id,
    ]);

    Auth::login($user);

    return redirect('/dashboard');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dashboard
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    
    $user = Auth::user();
    
    // Get real profit data from user's trades and holdings
    $userTrades = \App\Models\Trade::where('user_id', $user->id)->get();
    $totalInvested = $userTrades->sum('total_value') ?: 0;
    
    // Check if user has actually made trades
    $hasActiveTrades = $userTrades->count() > 0;
    
    if ($hasActiveTrades) {
        // User has trades - calculate ONLY realized profits from completed sell transactions
        $totalRealizedProfit = 0;
        
        // Simple approach: Only count profit when selling crypto
        $sellTrades = $userTrades->where('direction', 'sell');
        $buyTrades = $userTrades->where('direction', 'buy');
        
        // For each sell trade, calculate profit by comparing to average buy cost
        foreach ($sellTrades as $sellTrade) {
            $cryptoSymbol = $sellTrade->crypto_symbol;
            $sellAmount = $sellTrade->amount;
            $sellValue = $sellTrade->total_value - $sellTrade->fee; // Net received
            
            // Find all buy trades for this crypto to calculate average cost
            $buysForThisCrypto = $buyTrades->where('crypto_symbol', $cryptoSymbol);
            
            if ($buysForThisCrypto->count() > 0) {
                $totalBoughtAmount = $buysForThisCrypto->sum('amount');
                $totalBoughtCost = $buysForThisCrypto->sum(function($trade) {
                    return $trade->total_value + $trade->fee; // Total spent including fees
                });
                
                if ($totalBoughtAmount > 0) {
                    $avgCostPerUnit = $totalBoughtCost / $totalBoughtAmount;
                    $costOfSoldUnits = $sellAmount * $avgCostPerUnit;
                    $profitFromThisSell = $sellValue - $costOfSoldUnits;
                    
                    $totalRealizedProfit += $profitFromThisSell;
                }
            }
        }
        
        // Set values - ONLY use realized profit, ignore all portfolio values
        $totalProfit = $totalRealizedProfit;
        $totalInvested = $buyTrades->sum(function($trade) {
            return $trade->total_value + $trade->fee;
        });
        
        // Calculate percentage based on total invested
        if ($totalInvested > 0) {
            $profitPercentage = (($totalProfit / $totalInvested) * 100);
        } else {
            $profitPercentage = 0;
        }
        
        // Get current USDT balance for display only (not used in profit calculation)
        $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
        $usdtBalance = $usdtWallet ? $usdtWallet->balance : 0;
        
        // Include main wallet balance in total current value
        $mainWalletBalance = $user->wallet_balance ?? 0;
        $totalCurrentValue = $usdtBalance + $mainWalletBalance;
        
        // Ensure calculated values are valid numbers
        $totalInvested = is_numeric($totalInvested) ? (float)$totalInvested : 0;
        $totalProfit = is_numeric($totalProfit) && is_finite($totalProfit) ? (float)$totalProfit : 0;
        $profitPercentage = is_numeric($profitPercentage) && is_finite($profitPercentage) ? (float)$profitPercentage : 0;
    } else {
        // User has no trades - show account balance but zero profit
        $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
        $currentUsdtBalance = $usdtWallet ? $usdtWallet->balance : 0;
        
        // Include main wallet balance in total current value
        $mainWalletBalance = $user->wallet_balance ?? 0;
        $totalCurrentValue = $currentUsdtBalance + $mainWalletBalance;
        
        $totalInvested = 0;
        $totalProfit = 0;  // Always 0 for users without trades
        $profitPercentage = 0;  // Always 0 for users without trades
    }
    
    // Ensure we have usdtWallet available for profitData
    if (!isset($usdtWallet)) {
        $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
    }
    
    // Get user's crypto holdings for display
    $holdings = [];
    $userWallets = $user->wallets()->where('balance', '>', 0)->get();
    foreach ($userWallets as $wallet) {
        $balance = is_numeric($wallet->balance) ? (float)$wallet->balance : 0;
        $balanceUsd = is_numeric($wallet->balance_usd) ? (float)$wallet->balance_usd : $balance;
        
        $holdings[] = [
            'symbol' => $wallet->currency,
            'name' => $wallet->currency_name,
            'amount' => $balance,
            'current_value' => $balanceUsd,
            'invested_amount' => 0, // Calculate based on purchase history
            'profit' => 0, // Calculate based on purchase price vs current price
            'profit_percentage' => 0
        ];
    }
    
    $profitData = [
        'total_profit' => $totalProfit,
        'profit_percentage' => $profitPercentage,
        'total_investment' => $totalInvested,
        'current_value' => $totalCurrentValue,
        'main_wallet_balance' => $user->wallet_balance ?? 0,
        'crypto_balance' => $usdtWallet ? $usdtWallet->balance : 0,
        'holdings' => $holdings,
        'has_trades' => $hasActiveTrades  // Flag to indicate if user has made trades
    ];
    
    // Get real recent trades (last 10)
    $recentTrades = \App\Models\Trade::where('user_id', $user->id)
        ->latest()
        ->take(10)
        ->get();
    
    // Get pending counts for notifications
    $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
        ->where('status', 'pending')->count();
    $pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
        ->where('status', 'pending')->count();
    
    return view('dashboard', compact('user', 'profitData', 'recentTrades', 'pendingDeposits', 'pendingWithdrawals'));
})->name('dashboard');

// Basic protected routes (simplified for now)
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile.edit', ['user' => Auth::user()]);
    })->name('profile.edit');
    
    Route::get('/trade', function () {
        $user = Auth::user();
        
        // Ensure user has a USDT wallet for trading
        $user->ensureUSDTWallet();
        
        // Get pending counts for notifications
        $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        
        // Get real market data from CoinGecko
        $coinGeckoService = app(\App\Services\CoinGeckoService::class);
        $marketPrices = $coinGeckoService->getCurrentPrices();
        
        // Verify we got real prices (not fallback)
        if (empty($marketPrices) || !isset($marketPrices['BTC'])) {
            // Force a fresh API call if we don't have data
            \Illuminate\Support\Facades\Cache::forget('coingecko_prices_*');
            $marketPrices = $coinGeckoService->getCurrentPrices();
        }
        
        // Format market data with proper names
        $marketData = [];
        $cryptoNames = [
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum', 
            'ADA' => 'Cardano',
            'DOT' => 'Polkadot',
            'LTC' => 'Litecoin',
            'XRP' => 'Ripple',
            'BCH' => 'Bitcoin Cash',
            'LINK' => 'Chainlink',
            'BNB' => 'BNB'
        ];
        
        foreach ($marketPrices as $symbol => $data) {
            if (isset($cryptoNames[$symbol])) {
                $marketData[$symbol] = [
                    'name' => $cryptoNames[$symbol],
                    'usd' => $data['usd'],
                    'usd_24h_change' => $data['usd_24h_change'] ?? 0
                ];
            }
        }
        
        // Get user's portfolio data
        $userWallets = $user->wallets()->get();
        // Remove portfolio value calculation - not needed for trading interface
        
        // Get USDT balance (this would be the user's fiat/stable coin balance for trading)
        $usdtWallet = $userWallets->where('currency', 'USDT')->first();
        $usdtBalance = $usdtWallet ? $usdtWallet->available_balance : 0;
        
        // If user has no USDT balance, give them a warning about demo trading
        if ($usdtBalance <= 0 && !$usdtWallet) {
            // Create USDT wallet with demo balance
            $user->ensureUSDTWallet();
            // Refresh the wallet data
            $userWallets = $user->wallets()->get();
            $usdtWallet = $userWallets->where('currency', 'USDT')->first();
            $usdtBalance = $usdtWallet ? $usdtWallet->available_balance : 0;
        }
        
        // Update wallet USD values with current prices
        foreach ($userWallets as $wallet) {
            if (isset($marketData[$wallet->currency])) {
                $wallet->balance_usd = $wallet->balance * $marketData[$wallet->currency]['usd'];
                $wallet->save();
            }
        }
        
        // Get user's recent trades
        $userTrades = $user->trades()->latest()->take(10)->get();
        
        // Current market data for the default selected crypto (BTC)
        $currentMarketData = $marketData['BTC'] ?? null;
        
        return view('trade.index', compact(
            'pendingDeposits', 
            'pendingWithdrawals', 
            'marketData', 
            'userWallets',
            'usdtBalance',
            'userTrades',
            'currentMarketData'
        ));
    })->name('trade.index');
    
    Route::post('/trade/execute', function () {
        $request = request();
        $user = Auth::user();
        
        $request->validate([
            'crypto_pair' => 'required|string',
            'direction' => 'required|in:buy,sell',
            'amount' => 'required|numeric|min:0.00000001',
            'price' => 'nullable|numeric|min:0.01',
            'order_type' => 'required|in:market,limit'
        ]);
        
        // Extract crypto symbol from pair (e.g., "BTC/USDT" -> "BTC")
        $cryptoSymbol = explode('/', $request->crypto_pair)[0];
        
        // Get real-time price from CoinGecko
        $coinGeckoService = app(\App\Services\CoinGeckoService::class);
        $marketData = $coinGeckoService->getCurrentPrices();
        
        $currentPrice = $marketData[$cryptoSymbol]['usd'] ?? 50000;
        
        // Determine execution price
        if ($request->order_type === 'limit' && $request->price) {
            $executionPrice = $request->price;
            // For limit orders, check if price is reasonable (within 10% of market price)
            if (abs($executionPrice - $currentPrice) / $currentPrice > 0.1) {
                return redirect()->back()->with('error', 'Limit price is too far from market price. Please adjust your limit order.');
            }
        } else {
            $executionPrice = $currentPrice;
        }
        
        $amount = $request->amount;
        $totalValue = $amount * $executionPrice;
        $fee = $totalValue * 0.001; // 0.1% trading fee
        
        // Balance validation
        if ($request->direction === 'buy') {
            // Check USDT balance for buying
            $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
            $availableUSDT = $usdtWallet ? $usdtWallet->available_balance : ($user->wallet_balance ?? 0);
            
            $totalCost = $totalValue + $fee;
            if ($totalCost > $availableUSDT) {
                return redirect()->back()->with('error', 'Insufficient USDT balance. Required: ' . format_currency($totalCost) . ', Available: ' . format_currency($availableUSDT));
            }
            
            // Deduct USDT balance
            if ($usdtWallet && $usdtWallet->available_balance >= $totalCost) {
                $usdtWallet->subtractBalance($totalCost);
            } else {
                // Fallback to main wallet balance
                $user->wallet_balance = max(0, ($user->wallet_balance ?? 0) - $totalCost);
                $user->save();
            }
            
            // Add crypto to user's wallet
            $cryptoWallet = $user->wallets()->where('currency', $cryptoSymbol)->first();
            if (!$cryptoWallet) {
                $cryptoWallet = \App\Models\UserWallet::create([
                    'user_id' => $user->id,
                    'currency' => $cryptoSymbol,
                    'currency_name' => $cryptoSymbol,
                    'balance' => 0,
                    'locked_balance' => 0,
                    'balance_usd' => 0
                ]);
            }
            $cryptoWallet->addBalance($amount);
            
        } else {
            // Check crypto balance for selling
            $cryptoWallet = $user->wallets()->where('currency', $cryptoSymbol)->first();
            if (!$cryptoWallet || $cryptoWallet->available_balance < $amount) {
                $available = $cryptoWallet ? $cryptoWallet->available_balance : 0;
                return redirect()->back()->with('error', 'Insufficient ' . $cryptoSymbol . ' balance. Required: ' . number_format($amount, 8) . ', Available: ' . number_format($available, 8));
            }
            
            // Deduct crypto balance
            $cryptoWallet->subtractBalance($amount);
            
            // Add USDT to user's wallet (minus fee)
            $usdtReceived = $totalValue - $fee;
            $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
            if (!$usdtWallet) {
                $usdtWallet = \App\Models\UserWallet::create([
                    'user_id' => $user->id,
                    'currency' => 'USDT',
                    'currency_name' => 'Tether USD',
                    'balance' => 0,
                    'locked_balance' => 0,
                    'balance_usd' => 0
                ]);
            }
            $usdtWallet->addBalance($usdtReceived);
        }
        
        // Create trade record
        $trade = \App\Models\Trade::create([
            'user_id' => $user->id,
            'crypto_symbol' => $cryptoSymbol,
            'amount' => $amount,
            'direction' => $request->direction,
            'price_at_time' => $executionPrice,
            'total_value' => $totalValue,
            'status' => 'completed',
            'fee' => $fee,
            'executed_at' => now()
        ]);
        
        // Create transaction record for tracking
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'trade',
            'amount' => $totalValue,
            'description' => ucfirst($request->direction) . ' ' . number_format($amount, 8) . ' ' . $cryptoSymbol . ' at $' . number_format($executionPrice, 2),
            'status' => 'completed',
            'metadata' => json_encode([
                'trade_id' => $trade->id,
                'direction' => $request->direction,
                'crypto_symbol' => $cryptoSymbol,
                'amount' => $amount,
                'price' => $executionPrice,
                'fee' => $fee
            ])
        ]);
        
        $action = $request->direction === 'buy' ? 'purchased' : 'sold';
        $message = 'Successfully ' . $action . ' ' . number_format($amount, 8) . ' ' . $cryptoSymbol . ' at ' . format_currency($executionPrice) . '. Fee: ' . format_currency($fee);
        
        return redirect()->route('trade.index')->with('success', $message);
    })->name('trade.execute');
    
    // Chat routes (using ChatController)
    Route::get('/chat/messages', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    
    Route::post('/chat/send', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    
    Route::get('/chat/unread-count', [\App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
    
    Route::get('/wallet', function () {
        $user = Auth::user();
        $wallets = $user->wallets()->get();
        
        // Calculate total portfolio value including main wallet
        $portfolioValue = 0;
        try {
            $portfolioValue = $user->getTotalPortfolioValue();
        } catch (Exception $e) {
            $portfolioValue = 0;
        }
        $totalPortfolioValue = $portfolioValue + ($user->wallet_balance ?? 0);
        
        $recentTransactions = $user->transactions()->latest()->take(5)->get();
        
        // Get pending counts for notifications
        $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        
        return view('wallet.index', compact('wallets', 'totalPortfolioValue', 'recentTransactions', 'pendingDeposits', 'pendingWithdrawals'));
    })->name('wallet.index');
    
    Route::get('/deposit', function () {
        $user = Auth::user();
        $cryptoWallets = \App\Models\CryptoWallet::active()->get();
        $bankDetails = \App\Models\BankDetail::getActive();
        
        // Get pending counts for notifications
        $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        
        return view('dashboard.deposit', compact('cryptoWallets', 'bankDetails', 'pendingDeposits', 'pendingWithdrawals'));
    })->name('deposit.index');
    
    Route::post('/deposit/store', function () {
        $request = request();
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'proof_image' => 'nullable|image|max:2048',
            'deposit_method' => 'required|string|in:crypto,bank_transfer',
            'crypto_symbol' => 'required_if:deposit_method,crypto|string',
        ]);
        
        $deposit = \App\Models\Deposit::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'crypto_symbol' => $request->crypto_symbol ?? 'BANK_TRANSFER',
            'status' => 'pending',
            'transaction_id' => 'DEP' . strtoupper(uniqid()) . time(),
            'method' => $request->deposit_method,
            'proof_file' => $request->hasFile('proof_image') ? $request->file('proof_image')->store('deposits') : null
        ]);
        
        return redirect()->route('deposit.index')->with('success', 'Deposit request submitted successfully!');
    })->name('deposit.store');
    
    Route::get('/withdrawal', function () {
        $user = Auth::user();
        
        // Get user's withdrawals
        $withdrawals = $user->withdrawals()->latest()->paginate(10);
        
        // Get only crypto wallets with positive balance for withdrawal options
        // Exclude non-crypto currencies like BANK_TRANSFER
        $availableWallets = $user->wallets()
            ->where('balance', '>', 0)
            ->whereNotIn('currency', ['BANK_TRANSFER', 'FIAT', 'USD', 'EUR'])
            ->orderBy('balance_usd', 'desc')
            ->get();
        
        // Check if user needs withdrawal verification
        $verificationStep = $user->getNextVerificationStep();
        $canWithdraw = !$user->needsWithdrawalVerification();
        
        // Get pending counts for notifications
        $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        
        return view('dashboard.withdrawal', compact(
            'withdrawals', 
            'availableWallets', 
            'verificationStep', 
            'canWithdraw', 
            'pendingDeposits', 
            'pendingWithdrawals'
        ));
    })->name('withdrawal.index');
    
    // AJAX route for withdrawal submissions (handles verification modal)
    Route::post('/withdrawal/submit', [\App\Http\Controllers\WithdrawalController::class, 'store'])->name('withdrawal.submit');
    
    // Verification code route (AJAX)
    Route::post('/withdrawal/verify-code', [\App\Http\Controllers\WithdrawalController::class, 'verifyCode'])->name('withdrawal.verify-code');
    
    Route::post('/withdrawal/store', function () {
        $request = request();
        $user = Auth::user();
        
        $request->validate([
            'crypto_symbol' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'wallet_address' => 'required|string|min:26'
        ]);
        
        // Check if user needs verification
        if ($user->needsWithdrawalVerification()) {
            return redirect()->back()->with('error', 'Withdrawal verification required. Please complete the verification process first.');
        }
        
        // Get the user's wallet for this crypto
        $wallet = $user->wallets()->where('currency', $request->crypto_symbol)->first();
        
        if (!$wallet) {
            return redirect()->back()->with('error', 'You do not have a wallet for ' . $request->crypto_symbol);
        }
        
        // Check balance
        if ($wallet->available_balance < $request->amount) {
            return redirect()->back()->with('error', 'Insufficient balance. Available: ' . number_format($wallet->available_balance, 8) . ' ' . $request->crypto_symbol);
        }
        
        // Calculate withdrawal fee (1% of amount)
        $fee = $request->amount * 0.01;
        $totalRequired = $request->amount + $fee;
        
        if ($wallet->available_balance < $totalRequired) {
            return redirect()->back()->with('error', 'Insufficient balance including fee. Required: ' . number_format($totalRequired, 8) . ' ' . $request->crypto_symbol . ' (including ' . number_format($fee, 8) . ' fee)');
        }
        
        \DB::beginTransaction();
        
        try {
            // Lock the balance for withdrawal
            $wallet->lockBalance($totalRequired);
            
            // Create withdrawal record
            $withdrawal = \App\Models\Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'crypto_symbol' => $request->crypto_symbol,
                'withdrawal_address' => $request->wallet_address,
                'fee' => $fee,
                'status' => 'pending',
                'reference' => \App\Models\Withdrawal::generateReference()
            ]);
            
            // Create transaction record
            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'description' => 'Withdrawal request: ' . number_format($request->amount, 8) . ' ' . $request->crypto_symbol . ' to ' . substr($request->wallet_address, 0, 10) . '...',
                'status' => 'pending',
                'reference' => $withdrawal->reference,
                'metadata' => json_encode([
                    'withdrawal_id' => $withdrawal->id,
                    'crypto_symbol' => $request->crypto_symbol,
                    'wallet_address' => $request->wallet_address,
                    'fee' => $fee
                ])
            ]);
            
            // Log the withdrawal request
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'user',
                'action' => 'withdrawal_request',
                'message' => 'User ' . $user->name . ' requested withdrawal of ' . number_format($request->amount, 8) . ' ' . $request->crypto_symbol,
                'user_id' => $user->id,
            ]);
            
            \DB::commit();
            
            return redirect()->route('withdrawal.index')->with('success', 'Withdrawal request submitted successfully! Your funds have been locked and are pending admin approval.');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Withdrawal request failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Withdrawal request failed: ' . $e->getMessage());
        }
    })->name('withdrawal.store');
    
    Route::post('/withdrawal/verify', function () {
        $request = request();
        $user = Auth::user();
        
        $request->validate([
            'verification_code' => 'required|string'
        ]);
        
        if ($user->verifyWithdrawalCode($request->verification_code)) {
            $nextStep = $user->getNextVerificationStep();
            
            if ($nextStep) {
                $message = 'Verification successful! Please proceed to the next step: ' . $nextStep['title'];
            } else {
                $message = 'All verification steps completed! You can now make withdrawals.';
            }
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'user',
                'action' => 'withdrawal_verification',
                'message' => 'User ' . $user->name . ' completed withdrawal verification step',
                'user_id' => $user->id,
            ]);
            
            return redirect()->route('withdrawal.index')->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Invalid verification code. Please check and try again.');
        }
    })->name('withdrawal.verify');
    
    Route::get('/history', function () {
        $user = Auth::user();
        $deposits = $user->deposits()->latest()->get();
        $withdrawals = $user->withdrawals()->latest()->get();
        $trades = $user->trades()->latest()->get();
        $transactions = $user->transactions()->latest()->get(); // Add transactions (includes admin funding)
        
        $totalDeposits = $deposits->where('status', 'approved')->sum('amount');
        $totalWithdrawals = $withdrawals->where('status', 'approved')->sum('amount');
        $totalTrades = $trades->count();
        $netPL = $totalDeposits - $totalWithdrawals;
        
        // Get pending counts for notifications
        $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        
        return view('history.index', compact('deposits', 'withdrawals', 'trades', 'transactions', 'totalDeposits', 'totalWithdrawals', 'totalTrades', 'netPL', 'pendingDeposits', 'pendingWithdrawals'));
    })->name('history.index');
    
    Route::get('/settings', function () {
        $user = Auth::user();
        
        // Get pending counts for notifications
        $pendingDeposits = \App\Models\Deposit::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        $pendingWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')->count();
        
        return view('settings.index', compact('pendingDeposits', 'pendingWithdrawals'));
    })->name('settings.index');
    
    Route::patch('/settings/profile', function () {
        $request = request();
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:2048'
        ]);
        
        $updateData = $request->only(['name', 'email', 'phone', 'country', 'bio']);
        
        if ($request->hasFile('avatar')) {
            $updateData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }
        
        $user->update($updateData);
        
        return redirect()->route('settings.index')->with('success', 'Profile updated successfully!');
    })->name('settings.profile.update');
    
    Route::patch('/settings/password', function () {
        $request = request();
        
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed'
        ]);
        
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        Auth::user()->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('settings.index')->with('success', 'Password updated successfully!');
    })->name('settings.password.update');
    
    Route::post('/settings/2fa/enable', function () {
        $user = Auth::user();
        
        // Simple 2FA enable (mock implementation)
        $user->update(['two_factor_enabled' => true]);
        
        return redirect()->route('settings.index')->with('success', 'Two-factor authentication enabled successfully!');
    })->name('settings.2fa.enable');
    
    Route::delete('/settings/2fa/disable', function () {
        $request = request();
        
        $request->validate([
            'password' => 'required'
        ]);
        
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }
        
        $user = Auth::user();
        $user->update(['two_factor_enabled' => false]);
        
        return redirect()->route('settings.index')->with('success', 'Two-factor authentication disabled successfully!');
    })->name('settings.2fa.disable');
    
    Route::patch('/settings/trading', function () {
        $request = request();
        $user = Auth::user();
        
        $request->validate([
            'default_trading_pair' => 'nullable|string|in:BTC/USD,ETH/USD,BNB/USD,ADA/USD,SOL/USD',
            'default_order_amount' => 'nullable|numeric|min:0.01',
            'risk_tolerance' => 'nullable|string|in:low,medium,high',
            'auto_stop_loss' => 'nullable|boolean',
            'stop_loss_percentage' => 'nullable|numeric|min:1|max:50',
            'auto_take_profit' => 'nullable|boolean',
            'take_profit_percentage' => 'nullable|numeric|min:1|max:100'
        ]);
        
        // Prepare trading settings
        $tradingSettings = [
            'default_trading_pair' => $request->default_trading_pair ?? 'BTC/USD',
            'default_order_amount' => $request->default_order_amount ?? 100,
            'risk_tolerance' => $request->risk_tolerance ?? 'medium',
            'auto_stop_loss' => $request->boolean('auto_stop_loss'),
            'stop_loss_percentage' => $request->stop_loss_percentage ?? 5,
            'auto_take_profit' => $request->boolean('auto_take_profit'),
            'take_profit_percentage' => $request->take_profit_percentage ?? 10
        ];
        
        $user->update(['trading_settings' => json_encode($tradingSettings)]);
        
        return redirect()->route('settings.index')->with('success', 'Trading preferences updated successfully!');
    })->name('settings.trading.update');
    
    Route::patch('/settings/notifications', function () {
        $request = request();
        $user = Auth::user();
        
        // No validation needed as all fields are optional checkboxes
        
        // Prepare notification settings
        $notificationSettings = [
            'email_notifications' => $request->boolean('email_notifications'),
            'trade_notifications' => $request->boolean('trade_notifications'),
            'deposit_notifications' => $request->boolean('deposit_notifications'),
            'withdrawal_notifications' => $request->boolean('withdrawal_notifications'),
            'security_notifications' => $request->boolean('security_notifications'),
            'marketing_notifications' => $request->boolean('marketing_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'news_notifications' => $request->boolean('news_notifications'),
            'price_alerts' => $request->boolean('price_alerts')
        ];
        
        $user->update(['notification_settings' => json_encode($notificationSettings)]);
        
        return redirect()->route('settings.index')->with('success', 'Notification preferences updated successfully!');
    })->name('settings.notifications.update');
    
    // Admin routes (requires admin privilege)
    Route::middleware('auth')->group(function () {
        Route::get('/admin', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            // Get real data for admin dashboard
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::where('is_active', true)->count();
            $pendingDeposits = \App\Models\Deposit::where('status', 'pending')->count();
            $pendingWithdrawals = \App\Models\Withdrawal::where('status', 'pending')->count();
            
            // Recent transactions (last 5)
            $recentTransactions = \App\Models\Transaction::with('user')
                ->latest()
                ->take(5)
                ->get();
            
            // Platform statistics
            $totalVolume24h = \App\Models\Transaction::where('created_at', '>=', now()->subDay())
                ->sum('amount');
            $totalDepositsAmount = \App\Models\Deposit::where('status', 'approved')->sum('amount');
            $totalWithdrawalsAmount = \App\Models\Withdrawal::where('status', 'completed')->sum('amount');
            $platformFeeRevenue = \App\Models\Trade::sum('fee');
            $netRevenue = $totalDepositsAmount - $totalWithdrawalsAmount + $platformFeeRevenue;
            
            return view('admin.dashboard', compact(
                'totalUsers',
                'activeUsers', 
                'pendingDeposits',
                'pendingWithdrawals',
                'recentTransactions',
                'totalVolume24h',
                'totalDepositsAmount',
                'totalWithdrawalsAmount',
                'platformFeeRevenue',
                'netRevenue'
            ));
        })->name('admin.dashboard');
        
        Route::get('/admin/users', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            $users = \App\Models\User::paginate(15);
            return view('admin.users', compact('users'));
        })->name('admin.users');
        
        Route::get('/admin/transactions', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            $transactions = \App\Models\Transaction::with('user')->paginate(15);
            return view('admin.transactions', compact('transactions'));
        })->name('admin.transactions');
        
        Route::get('/admin/deposits', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            $deposits = \App\Models\Deposit::with('user')->paginate(15);
            return view('admin.deposits', compact('deposits'));
        })->name('admin.deposits');
        
        Route::get('/admin/withdrawals', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            $withdrawals = \App\Models\Withdrawal::with('user')->paginate(15);
            return view('admin.withdrawals', compact('withdrawals'));
        })->name('admin.withdrawals');
        
        Route::get('/admin/crypto-wallets', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $cryptoWallets = \App\Models\CryptoWallet::all();
            
            // Supported cryptocurrencies list
            $supportedCryptos = [
                'BTC' => 'Bitcoin',
                'ETH' => 'Ethereum',
                'USDT' => 'Tether',
                'BNB' => 'Binance Coin',
                'USDC' => 'USD Coin',
                'XRP' => 'Ripple',
                'ADA' => 'Cardano',
                'SOL' => 'Solana',
                'DOGE' => 'Dogecoin',
                'DOT' => 'Polkadot',
                'MATIC' => 'Polygon',
                'SHIB' => 'Shiba Inu',
                'AVAX' => 'Avalanche',
                'LTC' => 'Litecoin',
                'UNI' => 'Uniswap',
                'LINK' => 'Chainlink',
                'ATOM' => 'Cosmos',
                'XLM' => 'Stellar',
                'BCH' => 'Bitcoin Cash',
                'NEAR' => 'NEAR Protocol'
            ];
            
            return view('admin.crypto-wallets', compact('cryptoWallets', 'supportedCryptos'));
        })->name('admin.crypto-wallets');
        
        Route::get('/admin/settings', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            // Mock settings data - in real app you'd have a settings table
            $settings = [
                'company_name' => config('app.name', 'Elite Forex Pro'),
                'company_logo' => null,
                'support_email' => 'support@eliteforexpro.com',
                'support_phone' => '+1-234-567-8900',
                'maintenance_mode' => false,
                'registration_enabled' => true,
                'kyc_required' => false,
            ];
            
            // Currency data for the settings form
            $currencies = [
                'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
                'EUR' => ['name' => 'Euro', 'symbol' => '€'],
                'GBP' => ['name' => 'British Pound', 'symbol' => '£'],
                'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥'],
                'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$'],
                'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$'],
                'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF'],
                'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥'],
                'KRW' => ['name' => 'South Korean Won', 'symbol' => '₩'],
                'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹']
            ];
            
            $currentCurrency = \App\Models\SystemSetting::get('default_currency', 'EUR');
            $currentSymbol = \App\Models\SystemSetting::get('currency_symbol', '€');
            
            return view('admin.settings', compact('settings', 'currencies', 'currentCurrency', 'currentSymbol'));
        })->name('admin.settings');
        
        Route::get('/admin/logs', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            $logs = \App\Models\SystemLog::latest()->paginate(50);
            return view('admin.logs', compact('logs'));
        })->name('admin.logs');
        
        Route::get('/admin/chat', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            // Get all users who have sent messages, grouped by conversation
            $conversations = \App\Models\User::whereHas('chatMessages')
                ->withCount(['chatMessages as message_count'])
                ->with(['chatMessages' => function($query) {
                    $query->latest()->limit(1);
                }])
                ->get()
                ->map(function($user) {
                    $lastMessage = $user->chatMessages->first();
                    $unreadCount = \App\Models\ChatMessage::where('user_id', $user->id)
                        ->where('sender_type', 'user')
                        ->where('is_read', false)
                        ->count();
                    
                    return (object) [
                        'user_id' => $user->id, // Add the missing user_id property
                        'user' => $user,
                        'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                        'last_user_message' => \App\Models\ChatMessage::where('user_id', $user->id)
                            ->where('sender_type', 'user')
                            ->latest()
                            ->value('message'),
                        'last_admin_message' => \App\Models\ChatMessage::where('user_id', $user->id)
                            ->where('sender_type', 'admin')
                            ->latest()
                            ->value('message'),
                        'message_count' => $user->message_count,
                        'unread_count' => $unreadCount
                    ];
                })
                ->sortByDesc('last_message_time');
            
            // Get total stats
            $totalChats = $conversations->count();
            $unreadChats = $conversations->where('unread_count', '>', 0)->count();
            $totalMessages = \App\Models\ChatMessage::count();
            $avgResponseTime = '< 1 hour'; // Mock data for now
            
            return view('admin.chat', compact('conversations', 'totalChats', 'unreadChats', 'totalMessages', 'avgResponseTime'));
        })->name('admin.chat');
        
        // Admin chat send message route
        Route::post('/admin/chat/{user}/send', [\App\Http\Controllers\AdminController::class, 'sendChatMessage'])->name('admin.chat.send');
        
        // Admin chat get messages route
        Route::get('/admin/chat/{user}/messages', [\App\Http\Controllers\AdminController::class, 'getChatMessages'])->name('admin.chat.messages');
        
        Route::get('/admin/user/{user}', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $userTrades = \App\Models\Trade::where('user_id', $user->id)->latest()->take(10)->get();
            $userDeposits = \App\Models\Deposit::where('user_id', $user->id)->latest()->take(10)->get();
            $userWithdrawals = \App\Models\Withdrawal::where('user_id', $user->id)->latest()->take(10)->get();
            $transactions = \App\Models\Transaction::where('user_id', $user->id)->latest()->paginate(10);
            $wallets = \App\Models\UserWallet::where('user_id', $user->id)->get();
            
            return view('admin.user-detail', compact('user', 'userTrades', 'userDeposits', 'userWithdrawals', 'transactions', 'wallets'));
        })->name('admin.user-detail');
        
        Route::patch('/admin/user/{user}/toggle-status', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $user->is_active = !$user->is_active;
            $user->save();
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'user_status_toggle',
                'message' => 'User ' . $user->name . ' status changed to ' . ($user->is_active ? 'active' : 'inactive'),
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'User status updated successfully.');
        })->name('admin.toggle-user-status');
        
        Route::patch('/admin/user/{user}/update-balance', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $validated = request()->validate([
                'wallet_balance' => 'required|numeric|min:0'
            ]);
            
            $oldBalance = $user->wallet_balance ?? 0;
            $user->wallet_balance = $validated['wallet_balance'];
            $user->save();
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'balance_update',
                'message' => 'User ' . $user->name . ' balance updated from $' . number_format($oldBalance, 2) . ' to $' . number_format($user->wallet_balance, 2),
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'User balance updated successfully.');
        })->name('admin.update-user-balance');
        
        Route::patch('/admin/users/{user}/edit-balance', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $validated = request()->validate([
                'wallet_balance' => 'required|numeric|min:0'
            ]);
            
            $oldBalance = $user->wallet_balance ?? 0;
            $user->wallet_balance = $validated['wallet_balance'];
            $user->save();
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'balance_edit',
                'message' => 'User ' . $user->name . ' balance updated from $' . number_format($oldBalance, 2) . ' to $' . number_format($user->wallet_balance, 2),
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'User balance updated successfully.');
        })->name('admin.edit-user-balance');
        
        Route::patch('/admin/user/{user}/fund', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $validated = request()->validate([
                'amount' => 'required|numeric|min:0.01'
            ]);
            
            $oldBalance = $user->wallet_balance ?? 0;
            $user->wallet_balance = $oldBalance + $validated['amount'];
            $user->save();
            
            // Create a transaction record
            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'type' => 'fund',
                'amount' => $validated['amount'],
                'description' => 'Admin funding - Balance adjustment',
                'status' => 'completed',
                'reference' => 'ADMIN_FUND_' . time(),
            ]);
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'user_funding',
                'message' => 'User ' . $user->name . ' funded with $' . number_format($validated['amount'], 2) . ' (Balance: $' . number_format($user->wallet_balance, 2) . ')',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'User funded successfully.');
        })->name('admin.fund-user');
        
        Route::patch('/admin/users/{user}/fund', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $validated = request()->validate([
                'amount' => 'required|numeric|min:0.01',
                'crypto_symbol' => 'required|string',
                'note' => 'nullable|string'
            ]);
            
            $oldBalance = $user->wallet_balance ?? 0;
            $user->wallet_balance = $oldBalance + $validated['amount'];
            $user->save();
            
            // Create a transaction record
            \App\Models\Transaction::create([
                'user_id' => $user->id,
                'type' => 'fund',
                'amount' => $validated['amount'],
                'description' => 'Admin funding - ' . ($validated['note'] ?? 'Balance adjustment') . ' (' . $validated['crypto_symbol'] . ')',
                'status' => 'completed',
                'reference' => 'ADMIN_FUND_' . time(),
            ]);
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'user_funding',
                'message' => 'User ' . $user->name . ' funded with $' . number_format($validated['amount'], 2) . ' (' . $validated['crypto_symbol'] . ') - Balance: $' . number_format($user->wallet_balance, 2),
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'User funded successfully.');
        })->name('admin.fund-user-modal');
        
        Route::patch('/admin/users/{user}/withdrawal-status', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $validated = request()->validate([
                'withdrawal_status' => 'required|string|in:active,aml_kyc_verification,aml_security_check,regulatory_compliance',
                'aml_verification_code' => 'nullable|string|max:20',
                'fwac_verification_code' => 'nullable|string|max:20',
                'tsc_verification_code' => 'nullable|string|max:20',
                'withdrawal_restriction_notes' => 'nullable|string'
            ]);
            
            $oldStatus = $user->withdrawal_status;
            
            // Update withdrawal status and verification codes
            $user->withdrawal_status = $validated['withdrawal_status'];
            $user->aml_verification_code = $validated['aml_verification_code'];
            $user->fwac_verification_code = $validated['fwac_verification_code'];
            $user->tsc_verification_code = $validated['tsc_verification_code'];
            $user->withdrawal_restriction_notes = $validated['withdrawal_restriction_notes'];
            
            // Reset code usage flags when status changes
            if ($oldStatus !== $validated['withdrawal_status']) {
                $user->aml_code_used = false;
                $user->fwac_code_used = false;
                $user->tsc_code_used = false;
                $user->aml_code_used_at = null;
                $user->fwac_code_used_at = null;
                $user->tsc_code_used_at = null;
            }
            
            $user->save();
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'withdrawal_status_update',
                'message' => 'User ' . $user->name . ' withdrawal status updated from "' . $oldStatus . '" to "' . $validated['withdrawal_status'] . '"',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'Withdrawal status updated successfully.');
        })->name('admin.update-withdrawal-status');
        
        Route::delete('/admin/user/{user}', function (\App\Models\User $user) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            if ($user->is_admin) {
                return redirect()->back()->with('error', 'Cannot delete admin users.');
            }
            
            $userName = $user->name;
            $user->delete();
            
            \App\Models\SystemLog::create([
                'level' => 'warning',
                'type' => 'admin',
                'action' => 'user_deletion',
                'message' => 'User ' . $userName . ' was deleted',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'User deleted successfully.');
        })->name('admin.delete-user');
        
        // Admin Settings Routes
        Route::post('/admin/settings', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $request = request();
            
            $request->validate([
                'company_name' => 'nullable|string|max:255',
                'company_logo' => 'nullable|image|max:2048',
                'support_email' => 'nullable|email',
                'support_phone' => 'nullable|string|max:20',
                'maintenance_mode' => 'nullable|boolean',
                'registration_enabled' => 'nullable|boolean',
                'kyc_required' => 'nullable|boolean',
            ]);
            
            // This is a mock implementation - in real app you'd store these in a settings table
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'settings_update',
                'message' => 'Admin settings updated',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
        })->name('admin.settings.update');
        
        // Transaction Management Routes
        Route::patch('/admin/transactions/{transaction}/cancel', function (\App\Models\Transaction $transaction) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            if ($transaction->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending transactions can be cancelled.');
            }
            
            $transaction->update(['status' => 'cancelled']);
            
            \App\Models\SystemLog::create([
                'level' => 'warning',
                'type' => 'admin',
                'action' => 'transaction_cancel',
                'message' => 'Transaction #' . $transaction->id . ' cancelled by admin',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'Transaction cancelled successfully.');
        })->name('admin.transactions.cancel');
        
        // Deposit Management Routes
        Route::get('/admin/deposits/{deposit}/view', function (\App\Models\Deposit $deposit) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $deposit->load('user');
            
            return response()->json([
                'id' => $deposit->id,
                'user' => [
                    'name' => $deposit->user->name,
                    'email' => $deposit->user->email,
                ],
                'amount' => $deposit->amount,
                'crypto_symbol' => $deposit->crypto_symbol,
                'method' => $deposit->method,
                'status' => $deposit->status,
                'transaction_id' => $deposit->transaction_id,
                'created_at' => $deposit->created_at->format('M d, Y H:i'),
                'processed_at' => $deposit->processed_at ? $deposit->processed_at->format('M d, Y H:i') : null,
                'processed_by' => $deposit->processedBy ? $deposit->processedBy->name : null,
                'approved_at' => $deposit->approved_at ? $deposit->approved_at->format('M d, Y H:i') : null,
                'approved_by' => $deposit->approvedBy ? $deposit->approvedBy->name : null,
                'rejection_reason' => $deposit->rejection_reason,
                'proof_file' => $deposit->proof_file ? asset('storage/' . $deposit->proof_file) : null,
                'admin_notes' => $deposit->admin_notes,
            ]);
        })->name('admin.deposits.view');
        
        Route::patch('/admin/deposits/{deposit}/approve', function (\App\Models\Deposit $deposit) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            if ($deposit->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending deposits can be approved.');
            }
            
            DB::beginTransaction();
            
            try {
                // Update deposit status to approved with full approval info
                $deposit->status = 'approved';
                $deposit->approved_at = now();
                $deposit->approved_by = auth()->id();
                $deposit->processed_at = now();
                $deposit->processed_by = auth()->id();
                $deposit->save();
                
                $user = $deposit->user;
                $symbol = $deposit->crypto_symbol;
                $amount = $deposit->amount;
                
                // Find or create wallet for the specific crypto
                $wallet = \App\Models\UserWallet::where('user_id', $user->id)
                                   ->where('currency', $symbol)
                                   ->first();
                
                if (!$wallet) {
                    $wallet = \App\Models\UserWallet::create([
                        'user_id' => $user->id,
                        'currency' => $symbol,
                        'currency_name' => $symbol,
                        'balance' => 0,
                        'balance_usd' => 0
                    ]);
                }
                
                // Add amount to crypto wallet only (not to main wallet to avoid double counting)
                $wallet->balance += $amount;
                $wallet->balance_usd += $amount;
                $wallet->save();
                
                // Do NOT add to main wallet balance - this would cause double counting
                // The crypto wallet balance is already included in portfolio calculations
                
                // Update transaction status if exists
                $transaction = \App\Models\Transaction::where('transaction_id', $deposit->transaction_id)
                    ->where('type', 'deposit')
                    ->where('status', 'pending')
                    ->first();
                    
                if ($transaction) {
                    $transaction->status = 'completed';
                    $transaction->description = "Deposit of ${amount} via {$symbol} - Approved and processed";
                    $transaction->save();
                } else {
                    // Create new transaction record if none exists
                    \App\Models\Transaction::create([
                        'user_id' => $deposit->user_id,
                        'type' => 'deposit',
                        'amount' => $deposit->amount,
                        'description' => 'Deposit approved - ' . $deposit->crypto_symbol,
                        'status' => 'completed',
                        'transaction_id' => $deposit->transaction_id,
                        'reference' => 'DEP_' . $deposit->id,
                    ]);
                }
                
                \App\Models\SystemLog::create([
                    'level' => 'info',
                    'type' => 'admin',
                    'action' => 'deposit_approval',
                    'message' => 'Deposit #' . $deposit->id . ' approved ($' . number_format($deposit->amount, 2) . ') - Balance updated',
                    'user_id' => Auth::id(),
                ]);
                
                DB::commit();
                return redirect()->back()->with('success', "Deposit of ${amount} approved and {$amount} {$symbol} added to user's wallet");
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Deposit approval failed', [
                    'deposit_id' => $deposit->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->back()->with('error', 'Approval failed: ' . $e->getMessage());
            }
        })->name('admin.deposits.approve');
        
        Route::patch('/admin/deposits/{deposit}/reject', function (\App\Models\Deposit $deposit) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            if ($deposit->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending deposits can be rejected.');
            }
            
            $deposit->update(['status' => 'rejected']);
            
            \App\Models\SystemLog::create([
                'level' => 'warning',
                'type' => 'admin',
                'action' => 'deposit_rejection',
                'message' => 'Deposit #' . $deposit->id . ' rejected ($' . number_format($deposit->amount, 2) . ')',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->back()->with('success', 'Deposit rejected successfully.');
        })->name('admin.deposits.reject');
        
        // Withdrawal Management Routes
        Route::get('/admin/withdrawals/{withdrawal}/view', function (\App\Models\Withdrawal $withdrawal) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $withdrawal->load('user');
            
            return response()->json([
                'id' => $withdrawal->id,
                'user' => [
                    'name' => $withdrawal->user->name,
                    'email' => $withdrawal->user->email,
                ],
                'amount' => $withdrawal->amount,
                'crypto_symbol' => $withdrawal->crypto_symbol,
                'withdrawal_address' => $withdrawal->withdrawal_address,
                'fee' => $withdrawal->fee,
                'net_amount' => $withdrawal->net_amount,
                'status' => $withdrawal->status,
                'reference' => $withdrawal->reference,
                'created_at' => $withdrawal->created_at->format('M d, Y H:i'),
                'processed_at' => $withdrawal->processed_at ? $withdrawal->processed_at->format('M d, Y H:i') : null,
                'processed_by' => $withdrawal->processedBy ? $withdrawal->processedBy->name : null,
                'admin_notes' => $withdrawal->admin_notes,
                'transaction_hash' => $withdrawal->transaction_hash,
            ]);
        })->name('admin.withdrawals.view');
        
        Route::patch('/admin/withdrawals/{withdrawal}/approve', function (\App\Models\Withdrawal $withdrawal) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            if ($withdrawal->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending withdrawals can be approved.');
            }
            
            \DB::beginTransaction();
            
            try {
                // Update withdrawal status
                $withdrawal->status = 'approved';
                $withdrawal->processed_by = auth()->id();
                $withdrawal->processed_at = now();
                $withdrawal->save();
                
                \App\Models\SystemLog::create([
                    'level' => 'info',
                    'type' => 'admin',
                    'action' => 'withdrawal_approval',
                    'message' => 'Withdrawal #' . $withdrawal->id . ' approved (' . number_format($withdrawal->amount, 8) . ' ' . $withdrawal->crypto_symbol . ')',
                    'user_id' => Auth::id(),
                ]);
                
                \DB::commit();
                return redirect()->back()->with('success', 'Withdrawal approved successfully. Funds remain locked until completion.');
                
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Withdrawal approval failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', 'Approval failed: ' . $e->getMessage());
            }
        })->name('admin.withdrawals.approve');
        
        Route::patch('/admin/withdrawals/{withdrawal}/complete', function (\App\Models\Withdrawal $withdrawal) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            if (!in_array($withdrawal->status, ['approved', 'processing'])) {
                return redirect()->back()->with('error', 'Only approved or processing withdrawals can be completed.');
            }
            
            \DB::beginTransaction();
            
            try {
                $user = $withdrawal->user;
                $cryptoSymbol = $withdrawal->crypto_symbol;
                $totalAmount = $withdrawal->amount + $withdrawal->fee;
                
                // Find the user's wallet for this crypto
                $wallet = $user->wallets()->where('currency', $cryptoSymbol)->first();
                
                if (!$wallet) {
                    throw new \Exception("User wallet not found for {$cryptoSymbol}");
                }
                
                // Unlock and remove the locked balance
                $wallet->unlockBalance($totalAmount);
                $wallet->balance -= $totalAmount;
                $wallet->save();
                
                // Update withdrawal status
                $withdrawal->status = 'completed';
                $withdrawal->completed_at = now();
                $withdrawal->save();
                
                // Update transaction if exists
                $transaction = \App\Models\Transaction::where('reference', $withdrawal->reference)
                    ->where('type', 'withdrawal')
                    ->first();
                    
                if ($transaction) {
                    $transaction->status = 'completed';
                    $transaction->save();
                }
                
                \App\Models\SystemLog::create([
                    'level' => 'info',
                    'type' => 'admin',
                    'action' => 'withdrawal_completion',
                    'message' => 'Withdrawal #' . $withdrawal->id . ' completed (' . number_format($withdrawal->amount, 8) . ' ' . $withdrawal->crypto_symbol . ')',
                    'user_id' => Auth::id(),
                ]);
                
                \DB::commit();
                return redirect()->back()->with('success', 'Withdrawal completed successfully. Funds have been deducted from user account.');
                
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Withdrawal completion failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', 'Completion failed: ' . $e->getMessage());
            }
        })->name('admin.withdrawals.complete');
        
        Route::patch('/admin/withdrawals/{withdrawal}/reject', function (\App\Models\Withdrawal $withdrawal) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            if ($withdrawal->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending withdrawals can be rejected.');
            }
            
            \DB::beginTransaction();
            
            try {
                $user = $withdrawal->user;
                $cryptoSymbol = $withdrawal->crypto_symbol;
                $totalAmount = $withdrawal->amount + $withdrawal->fee;
                
                // Find the user's wallet for this crypto
                $wallet = $user->wallets()->where('currency', $cryptoSymbol)->first();
                
                if ($wallet) {
                    // Unlock the previously locked balance
                    $wallet->unlockBalance($totalAmount);
                }
                
                // Update withdrawal status
                $withdrawal->status = 'rejected';
                $withdrawal->processed_by = auth()->id();
                $withdrawal->processed_at = now();
                $withdrawal->save();
                
                // Update transaction if exists
                $transaction = \App\Models\Transaction::where('reference', $withdrawal->reference)
                    ->where('type', 'withdrawal')
                    ->first();
                    
                if ($transaction) {
                    $transaction->status = 'rejected';
                    $transaction->save();
                }
                
                \App\Models\SystemLog::create([
                    'level' => 'warning',
                    'type' => 'admin',
                    'action' => 'withdrawal_rejection',
                    'message' => 'Withdrawal #' . $withdrawal->id . ' rejected (' . number_format($withdrawal->amount, 8) . ' ' . $withdrawal->crypto_symbol . ')',
                    'user_id' => Auth::id(),
                ]);
                
                \DB::commit();
                return redirect()->back()->with('success', 'Withdrawal rejected successfully. Funds have been unlocked in user account.');
                
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Withdrawal rejection failed', [
                    'withdrawal_id' => $withdrawal->id,
                    'error' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', 'Rejection failed: ' . $e->getMessage());
            }
        })->name('admin.withdrawals.reject');
        
        // Crypto Wallet Management Routes
        Route::post('/admin/crypto-wallets', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $request = request();
            
            $request->validate([
                'currency' => 'required|string|max:10|unique:crypto_wallets,currency',
                'wallet_address' => 'required|string|max:255',
                'network' => 'required|string|max:100',
                'qr_code_image' => 'nullable|image|max:2048',
                'is_active' => 'boolean'
            ]);
            
            // Get currency name from supported cryptos list
            $supportedCryptos = [
                'BTC' => 'Bitcoin',
                'ETH' => 'Ethereum',
                'USDT' => 'Tether',
                'BNB' => 'Binance Coin',
                'USDC' => 'USD Coin',
                'XRP' => 'Ripple',
                'ADA' => 'Cardano',
                'SOL' => 'Solana',
                'DOGE' => 'Dogecoin',
                'DOT' => 'Polkadot',
                'MATIC' => 'Polygon',
                'SHIB' => 'Shiba Inu',
                'AVAX' => 'Avalanche',
                'LTC' => 'Litecoin',
                'UNI' => 'Uniswap',
                'LINK' => 'Chainlink',
                'ATOM' => 'Cosmos',
                'XLM' => 'Stellar',
                'BCH' => 'Bitcoin Cash',
                'NEAR' => 'NEAR Protocol'
            ];
            
            $data = [
                'currency' => strtoupper($request->currency),
                'currency_name' => $supportedCryptos[$request->currency] ?? $request->currency,
                'wallet_address' => $request->wallet_address,
                'network' => $request->network,
                'is_active' => $request->boolean('is_active', true)
            ];
            
            if ($request->hasFile('qr_code_image')) {
                $data['qr_code_image'] = $request->file('qr_code_image')->store('qr_codes', 'public');
            }
            
            \App\Models\CryptoWallet::create($data);
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'crypto_wallet_create',
                'message' => 'New crypto wallet added: ' . $request->currency . ' (' . ($supportedCryptos[$request->currency] ?? $request->currency) . ')',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('admin.crypto-wallets')->with('success', 'Crypto wallet added successfully.');
        })->name('admin.store-crypto-wallet');
        
        Route::delete('/admin/crypto-wallets/{wallet}', function (\App\Models\CryptoWallet $wallet) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $walletName = $wallet->currency . ' (' . $wallet->currency_name . ')';
            $wallet->delete();
            
            \App\Models\SystemLog::create([
                'level' => 'warning',
                'type' => 'admin',
                'action' => 'crypto_wallet_delete',
                'message' => 'Crypto wallet deleted: ' . $walletName,
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('admin.crypto-wallets')->with('success', 'Crypto wallet deleted successfully.');
        })->name('admin.delete-crypto-wallet');
        
        // Bank Details Management Routes
        Route::get('/admin/bank-details', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $bankDetails = \App\Models\BankDetail::all();
            
            return view('admin.bank-details', compact('bankDetails'));
        })->name('admin.bank-details');
        
        Route::post('/admin/bank-details', function () {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $request = request();
            
            $request->validate([
                'bank_details' => 'required|string',
                'is_active' => 'boolean'
            ]);
            
            // If setting as active, deactivate all others
            if ($request->boolean('is_active', true)) {
                \App\Models\BankDetail::where('is_active', true)->update(['is_active' => false]);
            }
            
            \App\Models\BankDetail::create([
                'bank_details' => $request->bank_details,
                'is_active' => $request->boolean('is_active', true)
            ]);
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'bank_details_create',
                'message' => 'New bank details added',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('admin.bank-details')->with('success', 'Bank details added successfully.');
        })->name('admin.store-bank-details');
        
        Route::patch('/admin/bank-details/{bankDetail}', function (\App\Models\BankDetail $bankDetail) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $request = request();
            
            $request->validate([
                'bank_details' => 'required|string',
                'is_active' => 'boolean'
            ]);
            
            // If setting as active, deactivate all others
            if ($request->boolean('is_active', false)) {
                \App\Models\BankDetail::where('id', '!=', $bankDetail->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
            
            $bankDetail->update([
                'bank_details' => $request->bank_details,
                'is_active' => $request->boolean('is_active', false)
            ]);
            
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'admin',
                'action' => 'bank_details_update',
                'message' => 'Bank details updated',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('admin.bank-details')->with('success', 'Bank details updated successfully.');
        })->name('admin.update-bank-details');
        
        Route::delete('/admin/bank-details/{bankDetail}', function (\App\Models\BankDetail $bankDetail) {
            if (!Auth::user()->is_admin) {
                abort(403, 'Unauthorized');
            }
            
            $bankDetail->delete();
            
            \App\Models\SystemLog::create([
                'level' => 'warning',
                'type' => 'admin',
                'action' => 'bank_details_delete',
                'message' => 'Bank details deleted',
                'user_id' => Auth::id(),
            ]);
            
            return redirect()->route('admin.bank-details')->with('success', 'Bank details deleted successfully.');
        })->name('admin.delete-bank-details');
    });
});

// Frontend Error Logging API
Route::post('/api/log-error', function () {
    $request = request();
    
    $request->validate([
        'timestamp' => 'required|string',
        'level' => 'required|string|in:error,warning,info',
        'message' => 'required|string',
        'data' => 'nullable',
        'url' => 'required|string',
        'userAgent' => 'required|string'
    ]);
    
    // Log to Laravel log file
    $logMessage = sprintf(
        "[FRONTEND %s] %s - URL: %s - User: %s - Data: %s",
        strtoupper($request->level),
        $request->message,
        $request->url,
        Auth::user()->email ?? 'Guest',
        json_encode($request->data)
    );
    
    if ($request->level === 'error') {
        \Log::error($logMessage);
    } elseif ($request->level === 'warning') {
        \Log::warning($logMessage);
    } else {
        \Log::info($logMessage);
    }
    
    return response()->json(['success' => true]);
})->name('api.log-error');

// Admin routes for user funding
Route::middleware(['auth'])->group(function () {
    // Admin user funding interface
    Route::get('/admin/users', function () {
        // Only allow admin users
        if (!auth()->user()->is_admin) {
            abort(403, 'Admin access required');
        }

        $users = \App\Models\User::with(['wallets' => function($query) {
            $query->where('currency', 'USDT');
        }])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('admin.users', compact('users'));
    })->name('admin.users');

    // Fund user account
    Route::post('/admin/users/{user}/fund', function (\App\Models\User $user, Request $request) {
        // Only allow admin users
        if (!auth()->user()->is_admin) {
            abort(403, 'Admin access required');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:1000000',
            'note' => 'nullable|string|max:255'
        ]);

        $amount = floatval($request->amount);
        $note = $request->note ?? 'Admin web funding';

        // Get or create USDT wallet
        $usdtWallet = $user->wallets()->where('currency', 'USDT')->first();
        if (!$usdtWallet) {
            $usdtWallet = \App\Models\UserWallet::create([
                'user_id' => $user->id,
                'currency' => 'USDT',
                'currency_name' => 'Tether USD',
                'balance' => 0,
                'locked_balance' => 0,
                'balance_usd' => 0
            ]);
        }

        $oldBalance = $usdtWallet->balance;
        $newBalance = $oldBalance + $amount;

        // Update wallet balance
        $usdtWallet->update([
            'balance' => $newBalance,
            'balance_usd' => $newBalance
        ]);

        // Log the funding
        \App\Models\SystemLog::create([
            'level' => 'info',
            'type' => 'admin',
            'action' => 'user_funding_web',
            'message' => "Admin " . auth()->user()->name . " funded user {$user->name} with {$amount} USDT via web interface. Balance: {$oldBalance} -> {$newBalance}. Note: {$note}",
            'user_id' => $user->id,
            'data' => [
                'admin_user_id' => auth()->id(),
                'admin_name' => auth()->user()->name,
                'old_balance' => $oldBalance,
                'amount_added' => $amount,
                'new_balance' => $newBalance,
                'note' => $note,
                'funded_at' => now()->toDateTimeString()
            ]
        ]);

        return redirect()->back()->with('success', "Successfully funded {$user->name} with " . number_format($amount, 2) . " USDT");
    })->name('admin.users.fund');
});
