<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Trade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Display the transaction history page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get all transactions for the user
        $deposits = $user->deposits()->latest()->get();
        $withdrawals = $user->withdrawals()->latest()->get();
        $trades = $user->trades()->latest()->get();
        
        // Calculate statistics
        $totalDeposits = $deposits->where('status', 'approved')->sum('amount');
        $totalWithdrawals = $withdrawals->where('status', 'approved')->sum('amount');
        $totalTrades = $trades->count();
        $netPL = $totalDeposits - $totalWithdrawals;
        
        // Get pending counts for notifications
        $pendingDeposits = $user->deposits()->where('status', 'pending')->count();
        $pendingWithdrawals = $user->withdrawals()->where('status', 'pending')->count();
        
        return view('history.index', compact(
            'deposits',
            'withdrawals', 
            'trades',
            'totalDeposits',
            'totalWithdrawals',
            'totalTrades',
            'netPL',
            'pendingDeposits',
            'pendingWithdrawals'
        ));
    }
}
