<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Supported languages.
     */
    protected $supportedLanguages = [
        'en' => 'English',
        'it' => 'Italiano',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'ru' => 'Русский',
    ];

    /**
     * Switch language.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'language' => 'required|string|in:' . implode(',', array_keys($this->supportedLanguages))
        ]);

        $language = $request->language;

        // Set the application locale
        app()->setLocale($language);

        // Store in session for guest users
        Session::put('locale', $language);

        // Update user preference if authenticated
        if (Auth::check()) {
            Auth::user()->update([
                'language_preference' => $language
            ]);
        }

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Language changed successfully.'),
                'language' => $language
            ]);
        }

        // Return redirect for regular requests
        return back()->with('success', __('Language changed successfully.'));
    }

    /**
     * Get browser language preference.
     */
    public function detectBrowserLanguage(Request $request)
    {
        $browserLanguage = $request->server('HTTP_ACCEPT_LANGUAGE');
        
        if (!$browserLanguage) {
            return response()->json(['detected' => 'en']);
        }

        // Parse the Accept-Language header
        $languages = [];
        preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)\s*(?:;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $browserLanguage, $matches);

        if (count($matches[1])) {
            $languages = array_combine($matches[1], $matches[2]);
            
            foreach ($languages as $lang => $q) {
                $lang = strtolower($lang);
                
                // Check if we support this language
                if (isset($this->supportedLanguages[$lang])) {
                    return response()->json(['detected' => $lang]);
                }
                
                // Check for language family (e.g., en-US -> en)
                $langFamily = substr($lang, 0, 2);
                if (isset($this->supportedLanguages[$langFamily])) {
                    return response()->json(['detected' => $langFamily]);
                }
            }
        }

        return response()->json(['detected' => 'en']); // Default to English
    }

    /**
     * Get supported languages.
     */
    public function getSupportedLanguages()
    {
        return $this->supportedLanguages;
    }
}
