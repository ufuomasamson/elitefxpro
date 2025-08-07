<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CoinGeckoService
{
    private $apiKey;
    private $baseUrl = 'https://api.coingecko.com/api/v3';
    
    public function __construct()
    {
        $this->apiKey = config('services.coingecko.api_key');
    }
    
    /**
     * Get current prices for multiple cryptocurrencies
     */
    public function getCurrentPrices($symbols = [])
    {
        $defaultSymbols = ['bitcoin', 'ethereum', 'cardano', 'polkadot', 'litecoin', 'ripple', 'bitcoin-cash', 'chainlink', 'binancecoin', 'tether'];
        $coinIds = empty($symbols) ? $defaultSymbols : $this->mapSymbolsToCoinIds($symbols);
        
        // Cache key for prices
        $cacheKey = 'coingecko_prices_' . md5(implode('_', $coinIds));
        
        // Try to get from cache first (cache for 2 minutes)
        $prices = Cache::get($cacheKey);
        
        if (!$prices) {
            try {
                $response = Http::withOptions([
                    'verify' => false, // Disable SSL verification for development
                ])->withHeaders([
                    'X-CG-Demo-API-Key' => $this->apiKey
                ])->timeout(10)->get($this->baseUrl . '/simple/price', [
                    'ids' => implode(',', $coinIds),
                    'vs_currencies' => 'usd',
                    'include_24hr_change' => 'true'
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $prices = $this->formatPricesResponse($data);
                    
                    // Cache for 2 minutes
                    Cache::put($cacheKey, $prices, 120);
                    
                    Log::channel('wallet')->info('CoinGecko prices fetched successfully', [
                        'coins_count' => count($prices),
                        'prices' => $prices
                    ]);
                } else {
                    Log::channel('error')->error('CoinGecko API request failed', [
                        'status' => $response->status(),
                        'response' => $response->body()
                    ]);
                    
                    // Return fallback prices
                    return $this->getFallbackPrices();
                }
            } catch (\Exception $e) {
                Log::channel('error')->error('CoinGecko API exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Return fallback prices
                return $this->getFallbackPrices();
            }
        }
        
        return $prices;
    }
    
    /**
     * Get price for a single cryptocurrency
     */
    public function getPrice($symbol)
    {
        $prices = $this->getCurrentPrices();
        $priceData = $prices[strtoupper($symbol)] ?? ['usd' => 0];
        return $priceData['usd'] ?? 0;
    }
    
    /**
     * Map crypto symbols to CoinGecko coin IDs
     */
    private function mapSymbolsToCoinIds($symbols)
    {
        $mapping = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'ADA' => 'cardano',
            'DOT' => 'polkadot',
            'LTC' => 'litecoin',
            'XRP' => 'ripple',
            'BCH' => 'bitcoin-cash',
            'LINK' => 'chainlink',
            'BNB' => 'binancecoin',
            'USDT' => 'tether',
        ];
        
        $coinIds = [];
        foreach ($symbols as $symbol) {
            $symbol = strtoupper($symbol);
            if (isset($mapping[$symbol])) {
                $coinIds[] = $mapping[$symbol];
            }
        }
        
        return $coinIds;
    }
    
    /**
     * Format the API response to symbol => price format
     */
    private function formatPricesResponse($data)
    {
        $symbolMapping = [
            'bitcoin' => 'BTC',
            'ethereum' => 'ETH',
            'cardano' => 'ADA',
            'polkadot' => 'DOT',
            'litecoin' => 'LTC',
            'ripple' => 'XRP',
            'bitcoin-cash' => 'BCH',
            'chainlink' => 'LINK',
            'binancecoin' => 'BNB',
            'tether' => 'USDT',
        ];
        
        $prices = [];
        foreach ($data as $coinId => $priceData) {
            $symbol = $symbolMapping[$coinId] ?? strtoupper($coinId);
            $prices[$symbol] = [
                'usd' => $priceData['usd'] ?? 0,
                'usd_24h_change' => $priceData['usd_24h_change'] ?? 0,
            ];
        }
        
        return $prices;
    }
    
    /**
     * Fallback prices in case API fails
     */
    private function getFallbackPrices()
    {
        return [
            'BTC' => ['usd' => 43000, 'usd_24h_change' => 0],
            'ETH' => ['usd' => 2600, 'usd_24h_change' => 0],
            'ADA' => ['usd' => 0.45, 'usd_24h_change' => 0],
            'DOT' => ['usd' => 7.20, 'usd_24h_change' => 0],
            'LTC' => ['usd' => 90, 'usd_24h_change' => 0],
            'XRP' => ['usd' => 0.60, 'usd_24h_change' => 0],
            'BCH' => ['usd' => 380, 'usd_24h_change' => 0],
            'LINK' => ['usd' => 14, 'usd_24h_change' => 0],
            'BNB' => ['usd' => 300, 'usd_24h_change' => 0],
            'USDT' => ['usd' => 1, 'usd_24h_change' => 0],
        ];
    }
    
    /**
     * Get simple price array for backward compatibility
     */
    public function getSimplePrices()
    {
        $prices = $this->getCurrentPrices();
        $simplePrices = [];
        
        foreach ($prices as $symbol => $data) {
            $simplePrices[$symbol] = $data['usd'];
        }
        
        return $simplePrices;
    }
}
