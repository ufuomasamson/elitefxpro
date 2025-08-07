<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Models\SystemLog;
use App\Models\ChatMessage;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        // Get real stats for admin dashboard
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $pendingDeposits = Deposit::where('status', 'pending')->count();
        $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();
        
        // Platform statistics
        $totalVolume24h = Transaction::where('created_at', '>=', now()->subDay())
                                   ->where('status', 'completed')
                                   ->sum('amount');
        
        $totalDepositsAmount = Deposit::where('status', 'approved')->sum('amount');
        $totalWithdrawalsAmount = Withdrawal::where('status', 'completed')->sum('amount');
        
        $platformFeeRevenue = Transaction::where('type', 'fee')
                                       ->where('status', 'completed')
                                       ->sum('amount');
        
        $netRevenue = $totalDepositsAmount - $totalWithdrawalsAmount;
        
        // Recent transactions
        $recentTransactions = Transaction::with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->take(10)
                                        ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'pendingDeposits',
            'pendingWithdrawals',
            'totalVolume24h',
            'totalDepositsAmount',
            'totalWithdrawalsAmount',
            'platformFeeRevenue',
            'netRevenue',
            'recentTransactions'
        ));
    }
    
    /**
     * List all users.
     */
    public function users(Request $request)
    {
        $query = User::query();
        
        // Handle search
        if ($request->has('search') && $request->input('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        // Handle status filter
        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->input('status') === 'active');
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.users', compact('users'));
    }
    
    /**
     * Show user details.
     */
    public function userDetail(User $user)
    {
        $wallets = $user->wallets;
        $transactions = $user->transactions()->latest()->paginate(15);
        
        return view('admin.user-detail', compact('user', 'wallets', 'transactions'));
    }
    
    /**
     * Fund a user's wallet.
     */
    public function fundUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'crypto_symbol' => 'required|string|in:BTC,ETH,ADA,DOT,LTC,XRP,BCH,LINK,BNB,USDT',
            'amount' => 'required|numeric|min:0.00000001',
            'note' => 'nullable|string|max:500',
        ]);
        
        $symbol = $validated['crypto_symbol'];
        $amount = $validated['amount'];
        $note = $validated['note'] ?? "Admin funded {$amount} {$symbol}";
        
        DB::beginTransaction();
        
        try {
            // Find or create wallet
            $wallet = UserWallet::where('user_id', $user->id)
                               ->where('currency', $symbol)
                               ->first();
            
            if (!$wallet) {
                $wallet = UserWallet::create([
                    'user_id' => $user->id,
                    'currency' => $symbol,
                    'currency_name' => $this->getCurrencyName($symbol),
                    'balance' => 0
                ]);
            }
            
            // Add amount
            $wallet->balance += $amount;
            $wallet->save();
            
            // Update main wallet_balance with USD equivalent using real prices
            $coinGeckoService = app(\App\Services\CoinGeckoService::class);
            $conversionRates = $coinGeckoService->getSimplePrices();
            
            $usdEquivalent = $amount * ($conversionRates[$symbol] ?? 1);
            $user->wallet_balance = ($user->wallet_balance ?? 0) + $usdEquivalent;
            $user->save();
            
            // Log the funding operation
            Log::channel('admin')->info('Admin wallet funding completed', [
                'admin_user_id' => auth()->id(),
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'crypto_symbol' => $symbol,
                'amount' => $amount,
                'usd_equivalent' => $usdEquivalent,
                'note' => $note,
                'timestamp' => now()
            ]);
            
            // Log to wallet specific logs
            Log::channel('wallet')->info('Wallet funded by admin', [
                'user_id' => $user->id,
                'currency' => $symbol,
                'amount' => $amount,
                'previous_balance' => $wallet->balance - $amount,
                'new_balance' => $wallet->balance,
                'funded_by' => auth()->id()
            ]);
            
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'fund',
                'amount' => $amount,
                'status' => 'completed',
                'method' => 'admin_fund',
                'description' => $note,
                'metadata' => [
                    'crypto_symbol' => $symbol,
                    'currency' => $symbol,
                    'funded_by_admin' => auth()->id()
                ],
                'processed_at' => now(),
                'processed_by' => auth()->id()
            ]);
            
            DB::commit();
            return back()->with('success', "Successfully funded {$amount} {$symbol} to {$user->name}'s wallet");
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin funding error: ' . $e->getMessage());
            return back()->with('error', 'Funding failed. Please try again.');
        }
    }
    
    /**
     * Update user status (active/inactive).
     */
    public function updateUserStatus(Request $request, User $user)
    {
        // Toggle the user's active status
        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "User has been {$status}");
    }

    /**
     * Delete a user (soft delete).
     */
    public function deleteUser(User $user)
    {
        // Prevent deletion of admin users
        if ($user->is_admin) {
            return back()->with('error', 'Cannot delete admin users');
        }
        
        // Prevent deleting the currently logged-in user
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account');
        }
        
        $userName = $user->name;
        $user->delete();
        
        return back()->with('success', "User '{$userName}' has been deleted");
    }

    /**
     * Update user withdrawal status and codes.
     */
    public function updateWithdrawalStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'withdrawal_status' => 'required|string|in:active,aml_kyc_verification,aml_security_check,regulatory_compliance',
            'aml_verification_code' => 'nullable|string|max:20',
            'fwac_verification_code' => 'nullable|string|max:20',
            'tsc_verification_code' => 'nullable|string|max:20',
            'withdrawal_restriction_notes' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();
        
        try {
            $user->update([
                'withdrawal_status' => $validated['withdrawal_status'],
                'aml_verification_code' => $validated['aml_verification_code'],
                'fwac_verification_code' => $validated['fwac_verification_code'],
                'tsc_verification_code' => $validated['tsc_verification_code'],
                'withdrawal_restriction_notes' => $validated['withdrawal_restriction_notes'],
                // Reset code usage when admin updates
                'aml_code_used' => false,
                'fwac_code_used' => false,
                'tsc_code_used' => false,
                'aml_code_used_at' => null,
                'fwac_code_used_at' => null,
                'tsc_code_used_at' => null,
            ]);

            // Log the withdrawal status change
            Log::channel('admin')->info('Admin updated user withdrawal status', [
                'admin_user_id' => auth()->id(),
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'old_status' => $user->getOriginal('withdrawal_status'),
                'new_status' => $validated['withdrawal_status'],
                'notes' => $validated['withdrawal_restriction_notes']
            ]);

            DB::commit();
            
            $statusText = match($validated['withdrawal_status']) {
                'active' => 'Active (No Restrictions)',
                'aml_kyc_verification' => 'AML/KYC Verification Required',
                'aml_security_check' => 'AML Security Check Required',
                'regulatory_compliance' => 'Regulatory Compliance Required',
                default => 'Updated'
            };

            return back()->with('success', "Withdrawal status updated to: {$statusText}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin withdrawal status update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update withdrawal status. Please try again.');
        }
    }
    
    /**
     * List pending deposits.
     */
    public function deposits(Request $request)
    {
        $query = Deposit::with('user');
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', 'pending'); // Default to pending
        }
        
        $deposits = $query->latest()->paginate(15);
        
        return view('admin.deposits', compact('deposits'));
    }
    
    /**
     * Approve a deposit.
     */
    public function approveDeposit(Deposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return back()->with('error', 'This deposit has already been processed');
        }
        
        DB::beginTransaction();
        
        try {
            // Update deposit status to approved
            $deposit->status = 'approved';
            $deposit->approved_at = now();
            $deposit->approved_by = auth()->id();
            $deposit->processed_at = now();
            $deposit->processed_by = auth()->id();
            $deposit->save();
            
            $user = $deposit->user;
            $symbol = $deposit->crypto_symbol;
            $amount = $deposit->amount;
            
            // Find or create wallet
            $wallet = UserWallet::where('user_id', $user->id)
                               ->where('currency', $symbol)
                               ->first();
            
            if (!$wallet) {
                $wallet = UserWallet::create([
                    'user_id' => $user->id,
                    'currency' => $symbol,
                    'currency_name' => $this->getCurrencyName($symbol),
                    'balance' => 0,
                    'balance_usd' => 0
                ]);
            }
            
            // Add amount to wallet (convert USD to crypto amount - for demo using 1:1 ratio)
            $cryptoAmount = $amount; // In real implementation, you'd calculate actual crypto amount
            $wallet->balance += $cryptoAmount;
            $wallet->balance_usd += $amount;
            $wallet->save();
            
            // Update user's main wallet balance as well
            $user->wallet_balance = ($user->wallet_balance ?? 0) + $amount;
            $user->save();
            
            // Update transaction status
            $transaction = Transaction::where('transaction_id', $deposit->transaction_id)
                ->where('type', 'deposit')
                ->where('status', 'pending')
                ->first();
                
            if ($transaction) {
                $transaction->status = 'approved';
                $transaction->description = "Deposit of ${amount} via {$symbol} - Approved and processed";
                $transaction->save();
            }
            
            // Log the approval
            Log::info('Deposit approved', [
                'deposit_id' => $deposit->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'crypto_symbol' => $symbol,
                'approved_by' => auth()->id(),
                'approved_at' => now()
            ]);
            
            DB::commit();
            return back()->with('success', "Deposit of ${amount} approved and {$cryptoAmount} {$symbol} added to user's wallet");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Deposit approval failed', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a deposit.
     */
    public function rejectDeposit(Request $request, Deposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return back()->with('error', 'This deposit has already been processed');
        }
        
        $validated = $request->validate([
            'reject_reason' => 'required|string|min:5|max:500'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update deposit status
            $deposit->status = 'rejected';
            $deposit->rejection_reason = $validated['reject_reason'];
            $deposit->processed_at = now();
            $deposit->processed_by = auth()->id();
            $deposit->save();
            
            // Update transaction status
            $transaction = Transaction::where('transaction_id', $deposit->transaction_id)
                ->where('type', 'deposit')
                ->where('status', 'pending')
                ->first();
                
            if ($transaction) {
                $transaction->status = 'rejected';
                $transaction->description = "Deposit of ${deposit->amount} via {$deposit->crypto_symbol} - Rejected: " . $validated['reject_reason'];
                $transaction->save();
            }
            
            // Log the rejection
            Log::info('Deposit rejected', [
                'deposit_id' => $deposit->id,
                'user_id' => $deposit->user_id,
                'amount' => $deposit->amount,
                'crypto_symbol' => $deposit->crypto_symbol,
                'rejection_reason' => $validated['reject_reason'],
                'rejected_by' => auth()->id(),
                'rejected_at' => now()
            ]);
            
            DB::commit();
            return back()->with('success', 'Deposit has been rejected');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Deposit rejection failed', [
                'deposit_id' => $deposit->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get deposit details for viewing.
     */
    public function viewDeposit(Deposit $deposit)
    {
        $deposit->load(['user', 'processedBy']);
        
        return response()->json([
            'success' => true,
            'deposit' => [
                'id' => $deposit->id,
                'transaction_id' => $deposit->transaction_id,
                'user' => [
                    'id' => $deposit->user->id,
                    'name' => $deposit->user->name,
                    'email' => $deposit->user->email
                ],
                'amount' => $deposit->amount,
                'crypto_symbol' => $deposit->crypto_symbol,
                'method' => $deposit->method,
                'status' => $deposit->status,
                'proof_file' => $deposit->proof_file ? asset('storage/' . $deposit->proof_file) : null,
                'rejection_reason' => $deposit->rejection_reason,
                'created_at' => $deposit->created_at->format('M d, Y H:i A'),
                'processed_at' => $deposit->processed_at ? $deposit->processed_at->format('M d, Y H:i A') : null,
                'processed_by' => $deposit->processedBy ? $deposit->processedBy->name : null
            ]
        ]);
    }
    
    /**
     * List withdrawals.
     */
    public function withdrawals(Request $request)
    {
        $query = Withdrawal::with('user');
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        } else {
            $query->where('status', 'pending'); // Default to pending
        }
        
        $withdrawals = $query->latest()->paginate(15);
        
        return view('admin.withdrawals', compact('withdrawals'));
    }
    
    /**
     * Approve a withdrawal.
     */
    public function approveWithdrawal(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed');
        }
        
        DB::beginTransaction();
        
        try {
            $user = $withdrawal->user;
            $symbol = $withdrawal->crypto_symbol;
            $amount = $withdrawal->amount;
            
            // Check if user has sufficient balance
            $wallet = $user->wallets()->where('currency', $symbol)->first();
            
            if (!$wallet || $wallet->balance < $amount) {
                return back()->with('error', 'User has insufficient balance for this withdrawal');
            }
            
            // Deduct from wallet
            $wallet->balance -= $amount;
            $wallet->save();
            
            // Update withdrawal status
            $withdrawal->status = 'approved';
            $withdrawal->save();
            
            // Update transaction status
            $transaction = Transaction::where('user_id', $user->id)
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->where('amount', $amount)
                ->first();
                
            if ($transaction) {
                $transaction->status = 'processing';
                $transaction->description = "Withdrawal of {$amount} {$symbol} - Processing";
                $transaction->save();
            }
            
            DB::commit();
            return back()->with('success', 'Withdrawal approved and ready for processing');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject a withdrawal.
     */
    public function rejectWithdrawal(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'This withdrawal has already been processed');
        }
        
        DB::beginTransaction();
        
        try {
            // Update withdrawal status
            $withdrawal->status = 'rejected';
            $withdrawal->save();
            
            // Update transaction status
            $transaction = Transaction::where('user_id', $withdrawal->user_id)
                ->where('type', 'withdrawal')
                ->where('status', 'pending')
                ->where('amount', $withdrawal->amount)
                ->first();
                
            if ($transaction) {
                $transaction->status = 'rejected';
                $transaction->description = "Withdrawal of {$withdrawal->amount} {$withdrawal->crypto_symbol} - Rejected";
                $transaction->save();
            }
            
            DB::commit();
            return back()->with('success', 'Withdrawal has been rejected');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Mark a withdrawal as completed (sent to blockchain).
     */
    public function completeWithdrawal(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'This withdrawal must be approved before marking as completed');
        }
        
        DB::beginTransaction();
        
        try {
            // Update withdrawal status
            $withdrawal->status = 'completed';
            $withdrawal->save();
            
            // Update transaction status
            $transaction = Transaction::where('user_id', $withdrawal->user_id)
                ->where('type', 'withdrawal')
                ->where('status', 'processing')
                ->where('amount', $withdrawal->amount)
                ->first();
                
            if ($transaction) {
                $transaction->status = 'completed';
                $transaction->description = "Withdrawal of {$withdrawal->amount} {$withdrawal->crypto_symbol} - Completed";
                $transaction->save();
            }
            
            DB::commit();
            return back()->with('success', 'Withdrawal has been marked as completed');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Completion failed: ' . $e->getMessage());
        }
    }
    
    /**
     * View all transactions.
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with('user');
        
        // Handle filtering
        if ($request->has('type')) {
            $query->where('type', $request->input('type'));
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        
        $transactions = $query->latest()->paginate(20);
        
        return view('admin.transactions', compact('transactions'));
    }
    
    /**
     * Cancel a pending transaction.
     */
    public function cancelTransaction(Request $request, Transaction $transaction)
    {
        // Only allow canceling pending transactions
        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be canceled.');
        }
        
        // Only allow canceling certain transaction types
        if (!in_array($transaction->type, ['deposit', 'withdrawal'])) {
            return back()->with('error', 'This transaction type cannot be canceled.');
        }
        
        DB::beginTransaction();
        
        try {
            // Update transaction status
            $transaction->status = 'canceled';
            $transaction->canceled_at = now();
            $transaction->canceled_by = auth()->id();
            $transaction->save();
            
            // If it was a withdrawal, restore the user's balance
            if ($transaction->type === 'withdrawal') {
                $user = $transaction->user;
                $user->wallet_balance += $transaction->amount;
                $user->save();
                
                // Log the balance restoration
                Log::channel('admin')->info('Balance restored due to withdrawal cancellation', [
                    'admin_user_id' => auth()->id(),
                    'admin_user_name' => auth()->user()->name,
                    'transaction_id' => $transaction->id,
                    'user_id' => $user->id,
                    'amount_restored' => $transaction->amount,
                    'timestamp' => now()
                ]);
            }
            
            // Log the transaction cancellation
            Log::channel('admin')->info('Transaction canceled by admin', [
                'admin_user_id' => auth()->id(),
                'admin_user_name' => auth()->user()->name,
                'transaction_id' => $transaction->id,
                'transaction_type' => $transaction->type,
                'transaction_amount' => $transaction->amount,
                'user_id' => $transaction->user_id,
                'timestamp' => now()
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Transaction has been canceled successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel transaction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'admin_user_id' => auth()->id()
            ]);
            
            return back()->with('error', 'Failed to cancel transaction. Please try again.');
        }
    }
    
    /**
     * View and edit system settings.
     */
    public function settings()
    {
        // Get all settings grouped by category
        $settings = SystemSetting::getAllGrouped();
        
        // Add current currency info
        $currentCurrency = SystemSetting::get('default_currency', 'EUR');
        $currentSymbol = SystemSetting::get('currency_symbol', '€');
        
        $currencies = [
            'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
            'EUR' => ['name' => 'Euro', 'symbol' => '€'],
            'GBP' => ['name' => 'British Pound', 'symbol' => '£'],
            'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥'],
            'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'C$'],
            'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$'],
            'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'Fr'],
            'CNY' => ['name' => 'Chinese Yuan', 'symbol' => '¥'],
        ];
        
        return view('admin.settings', compact('settings', 'currentCurrency', 'currentSymbol', 'currencies'));
    }
    
    /**
     * Update system settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'currency_display' => 'required|string|in:USD,EUR,GBP,JPY,CAD,AUD,CHF,CNY',
            'maintenance_mode' => 'sometimes|boolean',
            'minimum_deposit' => 'sometimes|numeric|min:0',
            'minimum_withdrawal' => 'sometimes|numeric|min:0',
            'withdrawal_fee' => 'sometimes|numeric|min:0',
        ]);
        
        // Currency mapping
        $currencyMap = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'Fr',
            'CNY' => '¥',
        ];
        
        // Update currency settings
        if (isset($validated['currency_display'])) {
            $currency = $validated['currency_display'];
            $symbol = $currencyMap[$currency];
            
            SystemSetting::set('default_currency', $currency, 'string', 'Default currency for the platform', 'currency');
            SystemSetting::set('currency_symbol', $symbol, 'string', 'Currency symbol to display', 'currency');
        }
        
        // Update other settings
        foreach (['maintenance_mode', 'minimum_deposit', 'minimum_withdrawal', 'withdrawal_fee'] as $key) {
            if (isset($validated[$key])) {
                $type = in_array($key, ['maintenance_mode']) ? 'boolean' : 'number';
                $group = in_array($key, ['maintenance_mode']) ? 'general' : 'trading';
                
                SystemSetting::set($key, $validated[$key], $type, ucwords(str_replace('_', ' ', $key)), $group);
            }
        }
        
        return back()->with('success', 'Settings updated successfully! Currency changed to ' . ($validated['currency_display'] ?? 'current setting') . '.');
    }
    
    /**
     * Display system logs.
     */
    public function logs(Request $request)
    {
        $query = SystemLog::with('user')->latest();
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('days')) {
            $days = (int) $request->days;
            $query->where('created_at', '>=', now()->subDays($days));
        } else {
            // Default to today's logs
            $query->where('created_at', '>=', now()->startOfDay());
        }
        
        // Export functionality
        if ($request->has('export')) {
            return $this->exportLogs($query);
        }
        
        $logs = $query->paginate(50)->withQueryString();
        
        // Calculate statistics for today
        $today = now()->startOfDay();
        $todayLogs = SystemLog::where('created_at', '>=', $today)->count();
        $todayErrors = SystemLog::where('created_at', '>=', $today)->where('level', 'error')->count();
        $todayWarnings = SystemLog::where('created_at', '>=', $today)->where('level', 'warning')->count();
        $todayCritical = SystemLog::where('created_at', '>=', $today)->where('level', 'critical')->count();
        
        return view('admin.logs', compact('logs', 'todayLogs', 'todayErrors', 'todayWarnings', 'todayCritical'));
    }
    
    /**
     * Get log details for modal.
     */
    public function getLogDetails($logId)
    {
        $log = SystemLog::with('user')->findOrFail($logId);
        
        $html = view('admin.partials.log-details', compact('log'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
    
    /**
     * Clear old logs.
     */
    public function clearOldLogs()
    {
        $cutoffDate = now()->subDays(90);
        $deletedCount = SystemLog::where('created_at', '<', $cutoffDate)->delete();
        
        // Log this action
        SystemLog::logInfo('admin', 'clear_logs', "Cleared {$deletedCount} old log entries");
        
        return response()->json([
            'success' => true,
            'count' => $deletedCount,
            'message' => "Successfully cleared {$deletedCount} old log entries"
        ]);
    }
    
    /**
     * Report an issue from a log entry.
     */
    public function reportLogIssue($logId)
    {
        $log = SystemLog::findOrFail($logId);
        
        // In a real application, you would send this to your issue tracking system
        // For now, we'll just log it as a critical issue
        SystemLog::logCritical('admin', 'issue_reported', "Issue reported for log ID: {$logId}", [
            'original_log' => $log->toArray(),
            'reported_by' => auth()->user()->email,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Issue reported successfully'
        ]);
    }
    
    /**
     * Export logs to CSV.
     */
    private function exportLogs($query)
    {
        $logs = $query->get();
        
        $filename = 'system_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'Level', 'Type', 'Action', 'User Email', 'Message', 
                'IP Address', 'User Agent', 'File', 'Line', 'Context', 'Created At'
            ]);
            
            // Add data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->level,
                    $log->type,
                    $log->action,
                    $log->user_email,
                    $log->message,
                    $log->ip_address,
                    $log->user_agent,
                    $log->file,
                    $log->line,
                    json_encode($log->context),
                    $log->created_at->toDateTimeString(),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Edit a user's total balance.
     */
    public function editUserBalance(Request $request, User $user)
    {
        $validated = $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);
        
        $oldBalance = $user->wallet_balance ?? 0;
        $newBalance = $validated['balance'];
        $note = "Balance adjusted by admin: " . auth()->user()->name;
        
        DB::beginTransaction();
        
        try {
            // Update user balance
            $user->wallet_balance = $newBalance;
            $user->save();
            
            // Log the balance change
            Log::channel('admin')->info('Admin balance adjustment', [
                'admin_user_id' => auth()->id(),
                'admin_user_name' => auth()->user()->name,
                'target_user_id' => $user->id,
                'target_user_email' => $user->email,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
                'difference' => $newBalance - $oldBalance,
                'note' => $note,
                'timestamp' => now()
            ]);
            
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'balance_adjustment',
                'amount' => abs($newBalance - $oldBalance),
                'status' => 'completed',
                'method' => 'admin_adjustment',
                'description' => $note,
                'metadata' => [
                    'old_balance' => $oldBalance,
                    'new_balance' => $newBalance,
                    'adjusted_by_admin' => auth()->id(),
                    'admin_name' => auth()->user()->name
                ],
                'processed_at' => now(),
                'processed_by' => auth()->id()
            ]);
            
            DB::commit();
            
            $action = $newBalance > $oldBalance ? 'increased' : 'decreased';
            $amount = abs($newBalance - $oldBalance);
            
            return back()->with('success', "User balance {$action} by \${$amount}. New balance: \${$newBalance}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin balance adjustment error: ' . $e->getMessage());
            return back()->with('error', 'Balance adjustment failed. Please try again.');
        }
    }
    
    /**
     * Display crypto wallets management page.
     */
    public function cryptoWallets()
    {
        // Get all crypto wallets from database
        $cryptoWallets = \DB::table('crypto_wallets')->get();
        
        // Default supported cryptocurrencies if no wallets exist
        $supportedCryptos = [
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum',
            'ADA' => 'Cardano',
            'DOT' => 'Polkadot',
            'LTC' => 'Litecoin',
            'XRP' => 'Ripple',
            'BCH' => 'Bitcoin Cash',
            'LINK' => 'Chainlink',
            'BNB' => 'Binance Coin',
            'USDT' => 'Tether'
        ];
        
        return view('admin.crypto-wallets', compact('cryptoWallets', 'supportedCryptos'));
    }
    
    /**
     * Store or update a crypto wallet.
     */
    public function storeCryptoWallet(Request $request)
    {
        // Debug: Log the incoming request
        Log::info('Crypto wallet store request', [
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('qr_code_image'),
            'user_id' => auth()->id()
        ]);
        
        $validated = $request->validate([
            'currency' => 'required|string|in:BTC,ETH,ADA,DOT,LTC,XRP,BCH,LINK,BNB,USDT',
            'wallet_address' => 'required|string|max:255',
            'network' => 'nullable|string|max:100',
            'qr_code_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB max
            'is_active' => 'nullable|boolean',
        ]);
        
        // Convert is_active to boolean
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        try {
            // Handle QR code upload
            $qrCodePath = null;
            if ($request->hasFile('qr_code_image')) {
                Log::info('QR code file upload attempt', [
                    'file_name' => $request->file('qr_code_image')->getClientOriginalName(),
                    'file_size' => $request->file('qr_code_image')->getSize()
                ]);
                
                $qrCodePath = $request->file('qr_code_image')->store('qr_codes', 'public');
                
                Log::info('QR code uploaded successfully', ['path' => $qrCodePath]);
            }
            
            // Check if wallet already exists
            $existingWallet = \DB::table('crypto_wallets')
                ->where('currency', $validated['currency'])
                ->first();
            
            Log::info('Existing wallet check', [
                'currency' => $validated['currency'],
                'exists' => $existingWallet ? true : false
            ]);
            
            if ($existingWallet) {
                // Delete old QR code if uploading new one
                if ($qrCodePath && $existingWallet->qr_code_image) {
                    Storage::disk('public')->delete($existingWallet->qr_code_image);
                    Log::info('Old QR code deleted', ['old_path' => $existingWallet->qr_code_image]);
                }
                
                // Update existing wallet
                $updateData = [
                    'wallet_address' => $validated['wallet_address'],
                    'network' => $validated['network'] ?? null,
                    'is_active' => $validated['is_active'],
                    'updated_at' => now()
                ];
                
                if ($qrCodePath) {
                    $updateData['qr_code_image'] = $qrCodePath;
                }
                
                $updated = \DB::table('crypto_wallets')
                    ->where('currency', $validated['currency'])
                    ->update($updateData);
                
                Log::info('Wallet update result', ['updated_rows' => $updated, 'update_data' => $updateData]);
                
                $message = "Crypto wallet for {$validated['currency']} updated successfully";
            } else {
                // Create new wallet
                $insertData = [
                    'currency' => $validated['currency'],
                    'currency_name' => $this->getCurrencyName($validated['currency']),
                    'wallet_address' => $validated['wallet_address'],
                    'qr_code_image' => $qrCodePath,
                    'network' => $validated['network'] ?? null,
                    'is_active' => $validated['is_active'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                $inserted = \DB::table('crypto_wallets')->insert($insertData);
                
                Log::info('Wallet insert result', ['inserted' => $inserted, 'insert_data' => $insertData]);
                
                $message = "Crypto wallet for {$validated['currency']} created successfully";
            }
            
            // Log the wallet action
            Log::channel('admin')->info('Crypto wallet updated', [
                'admin_user_id' => auth()->id(),
                'currency' => $validated['currency'],
                'wallet_address' => $validated['wallet_address'],
                'qr_code_uploaded' => $qrCodePath ? true : false,
                'action' => $existingWallet ? 'updated' : 'created',
                'timestamp' => now()
            ]);
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Crypto wallet management error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Operation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a crypto wallet.
     */
    public function deleteCryptoWallet(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string',
        ]);
        
        try {
            // Get the wallet to delete QR code
            $wallet = \DB::table('crypto_wallets')
                ->where('currency', $validated['currency'])
                ->first();
            
            if ($wallet) {
                // Delete QR code image if exists
                if ($wallet->qr_code_image) {
                    Storage::disk('public')->delete($wallet->qr_code_image);
                }
                
                // Delete wallet record
                \DB::table('crypto_wallets')
                    ->where('currency', $validated['currency'])
                    ->delete();
                
                Log::channel('admin')->info('Crypto wallet deleted', [
                    'admin_user_id' => auth()->id(),
                    'currency' => $validated['currency'],
                    'qr_code_deleted' => $wallet->qr_code_image ? true : false,
                    'timestamp' => now()
                ]);
                
                return back()->with('success', "Crypto wallet for {$validated['currency']} deleted successfully");
            } else {
                return back()->with('error', 'Wallet not found');
            }
            
        } catch (\Exception $e) {
            Log::error('Crypto wallet deletion error: ' . $e->getMessage());
            return back()->with('error', 'Deletion failed. Please try again.');
        }
    }
    
    /**
     * Get the full name for a cryptocurrency symbol.
     */
    private function getCurrencyName($symbol)
    {
        $currencyNames = [
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum',
            'ADA' => 'Cardano',
            'DOT' => 'Polkadot',
            'LTC' => 'Litecoin',
            'XRP' => 'Ripple',
            'BCH' => 'Bitcoin Cash',
            'LINK' => 'Chainlink',
            'BNB' => 'Binance Coin',
            'USDT' => 'Tether'
        ];
        
        return $currencyNames[$symbol] ?? $symbol;
    }
    
    /**
     * Display the admin chat interface
     */
    public function chat(Request $request)
    {
        try {
            // Get filter parameters
            $search = $request->get('search');
            $status = $request->get('status', 'all');
            $days = $request->get('days', 7);
            
            // Get chat statistics
            $totalChats = ChatMessage::distinct('user_id')->count();
            $unreadChats = ChatMessage::fromUser()->unread()->distinct('user_id')->count();
            $todayChats = ChatMessage::whereDate('created_at', today())->distinct('user_id')->count();
            $activeChats = ChatMessage::where('created_at', '>=', now()->subHours(24))->distinct('user_id')->count();
            
            // Get conversations grouped by user
            $conversationsQuery = DB::table('chat_messages')
                ->select([
                    'user_id',
                    DB::raw('MAX(created_at) as last_message_time'),
                    DB::raw('COUNT(*) as message_count'),
                    DB::raw('SUM(CASE WHEN sender_type = "user" AND is_read = 0 THEN 1 ELSE 0 END) as unread_count'),
                    DB::raw('MAX(CASE WHEN sender_type = "user" THEN message ELSE NULL END) as last_user_message'),
                    DB::raw('MAX(CASE WHEN sender_type = "admin" THEN message ELSE NULL END) as last_admin_message')
                ])
                ->groupBy('user_id')
                ->orderBy('last_message_time', 'desc');
            
            // Apply filters
            if ($status === 'unread') {
                $conversationsQuery->having('unread_count', '>', 0);
            } elseif ($status === 'active') {
                $conversationsQuery->where('chat_messages.created_at', '>=', now()->subHours(24));
            }
            
            if ($days && $days !== 'all') {
                $conversationsQuery->where('chat_messages.created_at', '>=', now()->subDays($days));
            }
            
            $conversations = $conversationsQuery->paginate(20);
            
            // Load user information for conversations
            $userIds = $conversations->pluck('user_id');
            $users = User::whereIn('id', $userIds)->get()->keyBy('id');
            
            // Add user information to conversations
            foreach ($conversations as $conversation) {
                $conversation->user = $users->get($conversation->user_id);
            }
            
            SystemLog::logInfo('admin', 'view_chat', 'Administrator accessed live chat interface', [
                'total_conversations' => $totalChats,
                'unread_conversations' => $unreadChats
            ]);
            
            return view('admin.chat', compact(
                'conversations',
                'totalChats',
                'unreadChats', 
                'todayChats',
                'activeChats',
                'search',
                'status',
                'days'
            ));
            
        } catch (\Exception $e) {
            Log::error('Admin chat view error: ' . $e->getMessage());
            SystemLog::logError('admin', 'chat_error', 'Error accessing chat interface: ' . $e->getMessage());
            return back()->with('error', 'Failed to load chat interface. Please try again.');
        }
    }
    
    /**
     * Get messages for a specific user conversation
     */
    public function getChatMessages(Request $request, $userId)
    {
        try {
            $messages = ChatMessage::with(['user', 'admin'])
                ->where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->get();
            
            // Mark user messages as read
            ChatMessage::where('user_id', $userId)
                ->where('sender_type', 'user')
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
            
            $user = User::findOrFail($userId);
            
            return response()->json([
                'success' => true,
                'messages' => $messages,
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get chat messages error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load messages'
            ], 500);
        }
    }
    
    /**
     * Send a message as admin
     */
    public function sendChatMessage(Request $request, $userId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);
        
        try {
            $user = User::findOrFail($userId);
            
            $message = ChatMessage::create([
                'user_id' => $userId,
                'admin_id' => auth()->id(),
                'message' => $request->message,
                'sender_type' => 'admin',
                'is_read' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $message->load(['user', 'admin']);
            
            SystemLog::logInfo('admin', 'send_chat_message', 'Administrator sent chat message to user', [
                'user_id' => $userId,
                'user_email' => $user->email,
                'message_length' => strlen($request->message)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'html' => view('admin.partials.chat-message', compact('message'))->render()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Send admin chat message error: ' . $e->getMessage());
            SystemLog::logError('admin', 'send_chat_message_error', 'Error sending admin chat message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }
    
    /**
     * Delete a chat conversation
     */
    public function deleteChatConversation($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $messageCount = ChatMessage::where('user_id', $userId)->count();
            
            ChatMessage::where('user_id', $userId)->delete();
            
            SystemLog::logWarning('admin', 'delete_chat_conversation', 'Administrator deleted chat conversation', [
                'user_id' => $userId,
                'user_email' => $user->email,
                'deleted_messages' => $messageCount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Chat conversation deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete chat conversation error: ' . $e->getMessage());
            SystemLog::logError('admin', 'delete_chat_error', 'Error deleting chat conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete conversation'
            ], 500);
        }
    }
}
