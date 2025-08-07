<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Factory as Http;

class HomeController extends Controller
{
    /**
     * Display the landing page.
     */
    public function index()
    {
        // Get some basic crypto prices for the landing page
        $cryptoPrices = $this->getBasicCryptoPrices();
        
        return view('home.index', compact('cryptoPrices'));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('home.about');
    }

    /**
     * Display the services page.
     */
    public function services()
    {
        return view('home.services');
    }

    /**
     * Display the why us page.
     */
    public function whyUs()
    {
        return view('home.why-us');
    }

    /**
     * Get basic crypto prices from CoinGecko API.
     */
    private function getBasicCryptoPrices()
    {
        try {
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'bitcoin,ethereum,tether,binancecoin',
                'vs_currencies' => 'usd',
                'include_24hr_change' => 'true'
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Log error and return dummy data
            \Log::error('Failed to fetch crypto prices: ' . $e->getMessage());
        }

        // Return dummy data if API fails
        return [
            'bitcoin' => ['usd' => 45000, 'usd_24h_change' => 2.5],
            'ethereum' => ['usd' => 3200, 'usd_24h_change' => -1.2],
            'tether' => ['usd' => 1.00, 'usd_24h_change' => 0.1],
            'binancecoin' => ['usd' => 420, 'usd_24h_change' => 3.8],
        ];
    }
}
