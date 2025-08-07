<?php

if (!function_exists('format_currency')) {
    /**
     * Format amount with current currency symbol.
     */
    function format_currency($amount, $precision = 2)
    {
        // Simple fallback to avoid database dependency issues
        try {
            $currency = \App\Models\SystemSetting::getCurrencySettings();
            return $currency['symbol'] . number_format($amount, $precision);
        } catch (\Exception $e) {
            // Fallback to EUR symbol if database is not available
            return '€' . number_format($amount, $precision);
        }
    }
}

if (!function_exists('get_currency_symbol')) {
    /**
     * Get current currency symbol.
     */
    function get_currency_symbol()
    {
        return \App\Models\SystemSetting::get('currency_symbol', '€');
    }
}

if (!function_exists('get_currency_code')) {
    /**
     * Get current currency code.
     */
    function get_currency_code()
    {
        return \App\Models\SystemSetting::get('currency_code', 'EUR');
    }
}
