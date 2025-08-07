<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Supported languages
     */
    protected $supportedLanguages = ['en', 'it', 'fr', 'de', 'ru'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next): Response
    {
        $locale = $this->getPreferredLanguage($request);
        
        // Set the application locale
        App::setLocale($locale);
        
        // Store in session for future requests
        Session::put('locale', $locale);
        
        // Update user preference if logged in
        if (Auth::check() && Auth::user()->language_preference !== $locale) {
            Auth::user()->update(['language_preference' => $locale]);
        }
        
        return $next($request);
    }
    
    /**
     * Get the preferred language for the user
     */
    protected function getPreferredLanguage(\Illuminate\Http\Request $request): string
    {
        // 1. Check URL parameter (for manual switching)
        if ($request->has('lang') && in_array($request->get('lang'), $this->supportedLanguages)) {
            return $request->get('lang');
        }
        
        // 2. Check authenticated user's preference
        if (Auth::check() && Auth::user()->language_preference) {
            return Auth::user()->language_preference;
        }
        
        // 3. Check session
        if (Session::has('locale') && in_array(Session::get('locale'), $this->supportedLanguages)) {
            return Session::get('locale');
        }
        
        // 4. Detect from browser headers
        $browserLanguage = $this->detectBrowserLanguage($request);
        if ($browserLanguage && in_array($browserLanguage, $this->supportedLanguages)) {
            return $browserLanguage;
        }
        
        // 5. Default to English
        return 'en';
    }
    
    /**
     * Detect language from browser headers
     */
    protected function detectBrowserLanguage(\Illuminate\Http\Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return null;
        }
        
        // Parse Accept-Language header
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $lang) {
            $parts = explode(';', $lang);
            $code = trim($parts[0]);
            $priority = 1.0;
            
            if (isset($parts[1]) && strpos($parts[1], 'q=') === 0) {
                $priority = floatval(substr($parts[1], 2));
            }
            
            // Extract language code (e.g., 'en-US' -> 'en')
            $langCode = strtolower(substr($code, 0, 2));
            $languages[$langCode] = $priority;
        }
        
        // Sort by priority
        arsort($languages);
        
        // Return first supported language
        foreach ($languages as $langCode => $priority) {
            if (in_array($langCode, $this->supportedLanguages)) {
                return $langCode;
            }
        }
        
        return null;
    }
}
