<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        return view('settings.index', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
            'bio' => ['nullable', 'string', 'max:500'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $user = $request->user();
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('settings.index')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.index')->with('status', 'password-updated');
    }

    /**
     * Update trading preferences.
     */
    public function updateTradingPreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_trading_pair' => ['required', 'string', 'max:20'],
            'daily_trading_limit' => ['nullable', 'numeric', 'min:0'],
            'max_position_size' => ['nullable', 'numeric', 'min:0'],
            'auto_trading' => ['boolean'],
            'stop_loss_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'take_profit_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $user = $request->user();
        
        // Store trading preferences in user's settings JSON field or separate table
        $tradingSettings = [
            'default_trading_pair' => $validated['default_trading_pair'],
            'daily_trading_limit' => $validated['daily_trading_limit'],
            'max_position_size' => $validated['max_position_size'],
            'auto_trading' => $validated['auto_trading'] ?? false,
            'stop_loss_percentage' => $validated['stop_loss_percentage'],
            'take_profit_percentage' => $validated['take_profit_percentage'],
        ];

        $user->update(['trading_settings' => json_encode($tradingSettings)]);

        return redirect()->route('settings.index')->with('status', 'trading-updated');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_notifications' => ['boolean'],
            'trade_alerts' => ['boolean'],
            'price_alerts' => ['boolean'],
            'deposit_notifications' => ['boolean'],
            'withdrawal_notifications' => ['boolean'],
            'security_alerts' => ['boolean'],
            'marketing_emails' => ['boolean'],
        ]);

        $user = $request->user();
        
        $notificationSettings = [
            'email_notifications' => $validated['email_notifications'] ?? false,
            'trade_alerts' => $validated['trade_alerts'] ?? false,
            'price_alerts' => $validated['price_alerts'] ?? false,
            'deposit_notifications' => $validated['deposit_notifications'] ?? false,
            'withdrawal_notifications' => $validated['withdrawal_notifications'] ?? false,
            'security_alerts' => $validated['security_alerts'] ?? true, // Always default to true for security
            'marketing_emails' => $validated['marketing_emails'] ?? false,
        ];

        $user->update(['notification_settings' => json_encode($notificationSettings)]);

        return redirect()->route('settings.index')->with('status', 'notifications-updated');
    }

    /**
     * Enable two-factor authentication.
     */
    public function enableTwoFactor(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // In a real application, you would implement proper 2FA setup
        // For now, we'll just update a flag
        $user->update(['two_factor_enabled' => true]);

        return redirect()->route('settings.index')->with('status', '2fa-enabled');
    }

    /**
     * Disable two-factor authentication.
     */
    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->update(['two_factor_enabled' => false]);

        return redirect()->route('settings.index')->with('status', '2fa-disabled');
    }

    /**
     * Update API settings.
     */
    public function updateApiSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'api_enabled' => ['boolean'],
            'api_trading_enabled' => ['boolean'],
            'api_withdrawal_enabled' => ['boolean'],
        ]);

        $user = $request->user();
        
        $apiSettings = [
            'api_enabled' => $validated['api_enabled'] ?? false,
            'api_trading_enabled' => $validated['api_trading_enabled'] ?? false,
            'api_withdrawal_enabled' => $validated['api_withdrawal_enabled'] ?? false,
        ];

        $user->update(['api_settings' => json_encode($apiSettings)]);

        return redirect()->route('settings.index')->with('status', 'api-updated');
    }

    /**
     * Generate new API key.
     */
    public function generateApiKey(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Generate a new API key
        $apiKey = 'ttk_' . bin2hex(random_bytes(32));
        $apiSecret = bin2hex(random_bytes(32));
        
        $user->update([
            'api_key' => $apiKey,
            'api_secret' => Hash::make($apiSecret),
        ]);

        return redirect()->route('settings.index')
            ->with('status', 'api-key-generated')
            ->with('new_api_secret', $apiSecret); // Show once, then never again
    }
}
