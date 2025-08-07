<?php

namespace App\Traits;

trait HasDashboardData
{
    /**
     * Get the standard dashboard data that all dashboard views need.
     * Returns counts for notification badges.
     */
    protected function getDashboardData()
    {
        $user = auth()->user();
        
        return [
            'pendingDeposits' => $user->deposits()->where('status', 'pending')->count(),
            'pendingWithdrawals' => $user->withdrawals()->where('status', 'pending')->count(),
        ];
    }
    
    /**
     * Get dashboard data with collections for pages that need to display lists.
     */
    protected function getDashboardDataWithCollections()
    {
        $user = auth()->user();
        
        $pendingDepositsCollection = $user->deposits()->where('status', 'pending')->get();
        $pendingWithdrawalsCollection = $user->withdrawals()->where('status', 'pending')->get();
        
        return [
            // Counts for notifications
            'pendingDeposits' => $pendingDepositsCollection->count(),
            'pendingWithdrawals' => $pendingWithdrawalsCollection->count(),
            // Collections for view loops
            'pendingDepositsCollection' => $pendingDepositsCollection,
            'pendingWithdrawalsCollection' => $pendingWithdrawalsCollection,
        ];
    }
    
    /**
     * Merge dashboard data with custom data for views.
     */
    protected function viewWithDashboardData($view, $data = [])
    {
        return view($view, array_merge($this->getDashboardData(), $data));
    }
    
    /**
     * Merge dashboard data with collections and custom data for views.
     */
    protected function viewWithDashboardCollections($view, $data = [])
    {
        return view($view, array_merge($this->getDashboardDataWithCollections(), $data));
    }
}
