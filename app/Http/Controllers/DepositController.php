<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DepositController extends Controller
{
    /**
     * Display the deposit page with options.
     */
    public function index()
    {
        $user = Auth::user();
        $pendingDepositsCollection = $user->deposits()->where('status', 'pending')->get(); // Collection for view loop
        $pendingDeposits = $pendingDepositsCollection->count(); // Count for notifications
        $pendingWithdrawals = $user->withdrawals()->where('status', 'pending')->count(); // Count for notifications
        
        // Get active crypto wallets from database
        $cryptoWallets = \DB::table('crypto_wallets')
            ->where('is_active', true)
            ->orderBy('currency')
            ->get();
        
        return view('dashboard.deposit', compact('pendingDepositsCollection', 'pendingDeposits', 'pendingWithdrawals', 'cryptoWallets'));
    }
    
    /**
     * Process a deposit request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10', // Minimum deposit of $10
            'crypto_symbol' => 'required|string',
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);
        
        $user = Auth::user();
        
        // Handle file upload
        $path = $request->file('proof_file')->store('deposit_proofs', 'public');
        
        // Generate unique transaction ID
        $transactionId = 'DEP' . strtoupper(uniqid()) . time();
        
        // Create deposit record
        $deposit = new Deposit([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'crypto_symbol' => $validated['crypto_symbol'],
            'method' => 'manual',
            'proof_file' => $path,
            'status' => 'pending',
            'transaction_id' => $transactionId
        ]);
        
        $deposit->save();
        
        // Create transaction record
        $transaction = new Transaction([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'description' => "Manual deposit of {$validated['amount']} {$validated['crypto_symbol']} - Pending approval",
            'status' => 'pending',
            'transaction_id' => $transactionId
        ]);
        
        $transaction->save();
        
        return redirect()->route('deposit.index')
            ->with('success', 'Your deposit request has been submitted and is pending approval.');
    }
    
    /**
     * Process a manual deposit request.
     */
    public function manualDeposit(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10', // Minimum deposit of $10
            'crypto_symbol' => 'required|string',
            'proof_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
        ]);
        
        $user = Auth::user();
        
        // Handle file upload
        $path = $request->file('proof_file')->store('deposit_proofs', 'public');
        
        // Generate unique transaction ID
        $transactionId = 'DEP' . strtoupper(uniqid()) . time();
        
        // Create deposit record
        $deposit = new Deposit([
            'user_id' => $user->id,
            'amount' => $validated['amount'],
            'crypto_symbol' => $validated['crypto_symbol'],
            'method' => 'manual',
            'proof_file' => $path,
            'status' => 'pending',
            'transaction_id' => $transactionId
        ]);
        
        $deposit->save();
        
        // Create transaction record
        $transaction = new Transaction([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'description' => "Manual deposit of {$validated['amount']} {$validated['crypto_symbol']} - Pending approval",
            'status' => 'pending',
            'transaction_id' => $transactionId
        ]);
        
        $transaction->save();
        
        return redirect()->route('deposit.index')
            ->with('success', 'Your deposit request has been submitted and is pending approval.');
    }
    
    /**
     * Show deposit history.
     */
    public function history()
    {
        $user = Auth::user();
        $deposits = $user->deposits()->orderBy('created_at', 'desc')->paginate(15);
        
        return view('dashboard.deposit.history', compact('deposits'));
    }
}
