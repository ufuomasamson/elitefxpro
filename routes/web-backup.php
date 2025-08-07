<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Web Routes - Emergency Simplified Version
|--------------------------------------------------------------------------
*/

// Simple test routes without middleware
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Elite Forex Pro is working!',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
})->name('api.test');

// Simplified auth routes without guest middleware
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
        'password' => \Hash::make($request->password),
        'email_verified_at' => now(),
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

Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Trade routes
    Route::get('/trade', [App\Http\Controllers\TradeController::class, 'index'])->name('trade.index');
    Route::post('/trade/execute', [App\Http\Controllers\TradeController::class, 'execute'])->name('trade.execute');
    
    // Wallet routes
    Route::get('/wallet', [App\Http\Controllers\WalletController::class, 'index'])->name('wallet.index');
    
    // Deposit routes
    Route::get('/deposit', [App\Http\Controllers\DepositController::class, 'index'])->name('deposit.index');
    Route::get('/deposit/create', [App\Http\Controllers\DepositController::class, 'create'])->name('deposit.create');
    Route::post('/deposit/store', [App\Http\Controllers\DepositController::class, 'store'])->name('deposit.store');
    Route::get('/deposit/history', [App\Http\Controllers\DepositController::class, 'history'])->name('deposit.history');
    
    // Withdrawal routes
    Route::get('/withdrawal', [App\Http\Controllers\WithdrawalController::class, 'index'])->name('withdrawal.index');
    Route::post('/withdrawal', [App\Http\Controllers\WithdrawalController::class, 'store'])->name('withdrawal.store.ajax');
    Route::get('/withdrawal/create', [App\Http\Controllers\WithdrawalController::class, 'create'])->name('withdrawal.create');
    Route::post('/withdrawal/store', [App\Http\Controllers\WithdrawalController::class, 'store'])->name('withdrawal.store');
    Route::get('/withdrawal/history', [App\Http\Controllers\WithdrawalController::class, 'history'])->name('withdrawal.history');
    Route::post('/withdrawal/verify-code', [App\Http\Controllers\WithdrawalController::class, 'verifyCode'])->name('withdrawal.verify-code');
    
    // History routes
    Route::get('/history', [App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
    
    // Settings routes
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::patch('/settings/password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::patch('/settings/trading', [App\Http\Controllers\SettingsController::class, 'updateTradingPreferences'])->name('settings.trading.update');
    Route::patch('/settings/notifications', [App\Http\Controllers\SettingsController::class, 'updateNotificationPreferences'])->name('settings.notifications.update');
    Route::post('/settings/2fa/enable', [App\Http\Controllers\SettingsController::class, 'enable2FA'])->name('settings.2fa.enable');
    Route::delete('/settings/2fa/disable', [App\Http\Controllers\SettingsController::class, 'disable2FA'])->name('settings.2fa.disable');
    Route::patch('/settings/api', [App\Http\Controllers\SettingsController::class, 'updateApiSettings'])->name('settings.api.update');
    Route::post('/settings/api/generate-key', [App\Http\Controllers\SettingsController::class, 'generateApiKey'])->name('settings.api.generate-key');
    
    // Chat routes
    Route::get('/chat/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/unread-count', [App\Http\Controllers\ChatController::class, 'getUnreadCount'])->name('chat.unread-count');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/transactions', [App\Http\Controllers\AdminController::class, 'transactions'])->name('admin.transactions');
    Route::patch('/transactions/{transaction}/cancel', [App\Http\Controllers\AdminController::class, 'cancelTransaction'])->name('admin.transactions.cancel');
    Route::get('/deposits', [App\Http\Controllers\AdminController::class, 'deposits'])->name('admin.deposits');
    Route::get('/withdrawals', [App\Http\Controllers\AdminController::class, 'withdrawals'])->name('admin.withdrawals');
    Route::get('/settings', [App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [App\Http\Controllers\AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/logs', [App\Http\Controllers\AdminController::class, 'logs'])->name('admin.logs');
    Route::get('/logs/{log}', [App\Http\Controllers\AdminController::class, 'getLogDetails'])->name('admin.logs.details');
    Route::post('/logs/clear-old', [App\Http\Controllers\AdminController::class, 'clearOldLogs'])->name('admin.logs.clear-old');
    Route::post('/logs/{log}/report', [App\Http\Controllers\AdminController::class, 'reportLogIssue'])->name('admin.logs.report');
    
    // Chat routes
    Route::get('/chat', [App\Http\Controllers\AdminController::class, 'chat'])->name('admin.chat');
    Route::get('/chat/{user}/messages', [App\Http\Controllers\AdminController::class, 'getChatMessages'])->name('admin.chat.messages');
    Route::post('/chat/{user}/send', [App\Http\Controllers\AdminController::class, 'sendChatMessage'])->name('admin.chat.send');
    Route::delete('/chat/{user}/delete', [App\Http\Controllers\AdminController::class, 'deleteChatConversation'])->name('admin.chat.delete');
    
    // User Management Routes
    Route::get('/users/{user}', [App\Http\Controllers\AdminController::class, 'userDetail'])->name('admin.user-detail');
    Route::post('/users/{user}/fund', [App\Http\Controllers\AdminController::class, 'fundUser'])->name('admin.fund-user');
    Route::patch('/users/{user}/toggle-status', [App\Http\Controllers\AdminController::class, 'updateUserStatus'])->name('admin.toggle-user-status');
    Route::patch('/users/{user}/edit-balance', [App\Http\Controllers\AdminController::class, 'editUserBalance'])->name('admin.edit-user-balance');
    Route::patch('/users/{user}/withdrawal-status', [App\Http\Controllers\AdminController::class, 'updateWithdrawalStatus'])->name('admin.update-withdrawal-status');
    Route::delete('/users/{user}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.delete-user');
    
    // Crypto Wallets Management
    Route::get('/crypto-wallets', [App\Http\Controllers\AdminController::class, 'cryptoWallets'])->name('admin.crypto-wallets');
    Route::post('/crypto-wallets', [App\Http\Controllers\AdminController::class, 'storeCryptoWallet'])->name('admin.store-crypto-wallet');
    Route::delete('/crypto-wallets', [App\Http\Controllers\AdminController::class, 'deleteCryptoWallet'])->name('admin.delete-crypto-wallet');
    
    // Action routes
    Route::post('/deposits/{deposit}/approve', [App\Http\Controllers\AdminController::class, 'approveDeposit'])->name('admin.deposits.approve');
    Route::post('/deposits/{deposit}/reject', [App\Http\Controllers\AdminController::class, 'rejectDeposit'])->name('admin.deposits.reject');
    Route::get('/deposits/{deposit}/view', [App\Http\Controllers\AdminController::class, 'viewDeposit'])->name('admin.deposits.view');
    Route::post('/withdrawals/{withdrawal}/approve', [App\Http\Controllers\AdminController::class, 'approveWithdrawal'])->name('admin.withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [App\Http\Controllers\AdminController::class, 'rejectWithdrawal'])->name('admin.withdrawals.reject');
});


