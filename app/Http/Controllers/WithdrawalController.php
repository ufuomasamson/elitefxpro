<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    /**
     * Display the withdrawal page with form.
     */
    public function index()
    {
        $user = Auth::user();
        $userWallets = $user->wallets()->where('balance', '>', 0)->get(); // Only show wallets with balance
        $recentWithdrawals = $user->withdrawals()->latest()->take(5)->get();
        
        // Create an associative array for easy access in the view
        $walletBalances = $userWallets ? $userWallets->pluck('balance', 'currency')->toArray() : [];
        
        // Check withdrawal verification requirement
        $verificationStep = $user->getNextVerificationStep();
        
        return view('withdrawal.index', compact('userWallets', 'recentWithdrawals', 'walletBalances', 'verificationStep'));
    }
    
    /**
     * Process a withdrawal request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'crypto_type' => 'required|string',
            'amount' => 'required|numeric|min:0.00001',
            'wallet_address' => 'required|string|min:20',
        ]);
        
        $symbol = $validated['crypto_type'];
        $amount = $validated['amount'];
        
        // Check if user needs verification
        if ($user->needsWithdrawalVerification()) {
            $nextStep = $user->getNextVerificationStep();
            if ($nextStep) {
                return response()->json([
                    'success' => false,
                    'requires_verification' => true,
                    'current_step' => $nextStep['step'],
                    'total_steps' => $nextStep['total_steps'],
                    'verification_title' => $nextStep['title'],
                    'verification_message' => $nextStep['message']
                ]);
            } else {
                // Fallback if verification is needed but no step found
                return response()->json([
                    'success' => false,
                    'message' => 'Verification required but configuration error. Please contact support.'
                ]);
            }
        }
        
        // Check minimum withdrawal amount
        if ($amount < 0.001) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum withdrawal amount is 0.001'
            ]);
        }
        
        // Check if user has enough balance for the selected crypto
        $userWallet = $user->wallets()->where('currency', $symbol)->first();
        
        if (!$userWallet || $userWallet->available_balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient {$symbol} balance for withdrawal. Available: " . ($userWallet ? number_format($userWallet->available_balance, 8) : '0.00000000') . " {$symbol}"
            ]);
        }
        
        // Calculate withdrawal fee (1% of amount)
        $fee = $amount * 0.01;
        $totalRequired = $amount + $fee;
        
        if ($userWallet->available_balance < $totalRequired) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient balance including fee. Required: " . number_format($totalRequired, 8) . " {$symbol} (including " . number_format($fee, 8) . " fee)"
            ]);
        }
        
        // Begin transaction
        \DB::beginTransaction();
        
        try {
            // Lock the balance for withdrawal
            $userWallet->lockBalance($totalRequired);
            
            // Create withdrawal record
            $withdrawal = \App\Models\Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'crypto_symbol' => $symbol,
                'withdrawal_address' => $validated['wallet_address'],
                'fee' => $fee,
                'status' => 'pending',
                'reference' => \App\Models\Withdrawal::generateReference()
            ]);
            
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'description' => 'Withdrawal request: ' . number_format($amount, 8) . ' ' . $symbol . ' to ' . substr($validated['wallet_address'], 0, 10) . '...',
                'status' => 'pending',
                'reference' => $withdrawal->reference,
                'metadata' => json_encode([
                    'withdrawal_id' => $withdrawal->id,
                    'crypto_symbol' => $symbol,
                    'wallet_address' => $validated['wallet_address'],
                    'fee' => $fee
                ])
            ]);
            
            // Log the withdrawal request
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'user',
                'action' => 'withdrawal_request',
                'message' => 'User ' . $user->name . ' requested withdrawal of ' . number_format($amount, 8) . ' ' . $symbol,
                'user_id' => $user->id,
            ]);
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully! Your funds have been locked and are pending admin approval.'
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Withdrawal request failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request failed: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Show withdrawal history.
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $query = $user->withdrawals()->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $withdrawals = $query->paginate(15);
        
        return view('withdrawal.history', compact('withdrawals'));
    }

    /**
     * Verify withdrawal code.
     */
    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'withdrawal_data' => 'required|array',
            'withdrawal_data.crypto_type' => 'required|string',
            'withdrawal_data.amount' => 'required|numeric',
            'withdrawal_data.wallet_address' => 'required|string',
        ]);

        $user = Auth::user();
        
        if ($user->verifyWithdrawalCode($validated['code'])) {
            // Check if there are more steps
            if ($user->needsWithdrawalVerification()) {
                $nextStep = $user->getNextVerificationStep();
                if ($nextStep) {
                    return response()->json([
                        'success' => true,
                        'completed' => false,
                        'current_step' => $nextStep['step'],
                        'verification_title' => $nextStep['title'],
                        'verification_message' => $nextStep['message']
                    ]);
                } else {
                    // Edge case: needs verification but no step found
                    return response()->json([
                        'success' => false,
                        'message' => 'Verification configuration error. Please contact support.'
                    ]);
                }
            } else {
                // All verification complete, process the withdrawal
                return $this->processVerifiedWithdrawal($validated['withdrawal_data']);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid verification code. Please contact support.'
        ], 400);
    }

    /**
     * Process withdrawal after all verification steps are complete.
     */
    private function processVerifiedWithdrawal($withdrawalData)
    {
        $user = Auth::user();
        $symbol = $withdrawalData['crypto_type'];
        $amount = $withdrawalData['amount'];
        
        // Final validation
        $userWallet = $user->wallets()->where('currency', $symbol)->first();
        
        if (!$userWallet || $userWallet->available_balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient {$symbol} balance for withdrawal. Available: " . ($userWallet ? number_format($userWallet->available_balance, 8) : '0.00000000') . " {$symbol}"
            ]);
        }
        
        // Calculate withdrawal fee (1% of amount)
        $fee = $amount * 0.01;
        $totalRequired = $amount + $fee;
        
        if ($userWallet->available_balance < $totalRequired) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient balance including fee. Required: " . number_format($totalRequired, 8) . " {$symbol} (including " . number_format($fee, 8) . " fee)"
            ]);
        }
        
        // Begin transaction
        \DB::beginTransaction();
        
        try {
            // Lock the balance for withdrawal
            $userWallet->lockBalance($totalRequired);
            
            // Create withdrawal record
            $withdrawal = \App\Models\Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'crypto_symbol' => $symbol,
                'withdrawal_address' => $withdrawalData['wallet_address'],
                'fee' => $fee,
                'status' => 'pending',
                'reference' => \App\Models\Withdrawal::generateReference()
            ]);
            
            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'description' => 'Verified withdrawal request: ' . number_format($amount, 8) . ' ' . $symbol . ' to ' . substr($withdrawalData['wallet_address'], 0, 10) . '...',
                'status' => 'pending',
                'reference' => $withdrawal->reference,
                'metadata' => json_encode([
                    'withdrawal_id' => $withdrawal->id,
                    'crypto_symbol' => $symbol,
                    'wallet_address' => $withdrawalData['wallet_address'],
                    'fee' => $fee,
                    'verified' => true
                ])
            ]);
            
            // Log the verified withdrawal request
            \App\Models\SystemLog::create([
                'level' => 'info',
                'type' => 'user',
                'action' => 'withdrawal_verified',
                'message' => 'User ' . $user->name . ' completed verification and requested withdrawal of ' . number_format($amount, 8) . ' ' . $symbol,
                'user_id' => $user->id,
            ]);
            
            \DB::commit();
            
            return response()->json([
                'success' => true,
                'completed' => true,
                'message' => "🎉 Verification completed successfully! Your withdrawal request for " . number_format($amount, 8) . " {$symbol} has been submitted and is pending admin approval. Reference: " . $withdrawal->reference
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Verified withdrawal request failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Withdrawal request failed: ' . $e->getMessage()
            ]);
        }
    }
}
