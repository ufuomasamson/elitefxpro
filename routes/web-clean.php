<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    return view('dashboard', compact('user'));
})->name('dashboard');

// Basic protected routes (simplified for now)
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        return view('profile.edit', ['user' => Auth::user()]);
    })->name('profile.edit');
    
    Route::get('/trade', function () {
        return view('trade.index');
    })->name('trade.index');
    
    Route::get('/wallet', function () {
        return view('wallet.index');
    })->name('wallet.index');
    
    Route::get('/deposit', function () {
        return view('dashboard.deposit');
    })->name('deposit.index');
    
    Route::get('/withdrawal', function () {
        return view('dashboard.withdrawal');
    })->name('withdrawal.index');
    
    Route::get('/history', function () {
        return view('history.index');
    })->name('history.index');
    
    Route::get('/settings', function () {
        return view('settings.index');
    })->name('settings.index');
});
