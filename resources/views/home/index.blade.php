@extends('layouts.app')

@section('title', __('Crypto Broker Platform - Trade with Confidence'))

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    {{ __('Trade Crypto with') }} <span class="text-warning">{{ __('Confidence') }}</span>
                </h1>
                <p class="lead mb-4">
                    {{ __('Secure, reliable, and user-friendly cryptocurrency trading platform. Start your crypto journey with professional-grade tools and 24/7 support.') }}
                </p>
                <div class="d-flex gap-3 mb-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-chart-line"></i> {{ __('Go to Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-warning btn-lg">
                            <i class="fas fa-rocket"></i> {{ __('Start Trading') }}
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
                        </a>
                    @endauth
                </div>
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="fw-bold">10K+</h4>
                        <small>{{ __('Active Users') }}</small>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold">$50M+</h4>
                        <small>{{ __('Volume Traded') }}</small>
                    </div>
                    <div class="col-4">
                        <h4 class="fw-bold">99.9%</h4>
                        <small>{{ __('Uptime') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="crypto-card p-4">
                    <h5 class="mb-3">{{ __('Live Crypto Prices') }}</h5>
                    @if(isset($cryptoPrices))
                        <div class="row">
                            @foreach($cryptoPrices as $coin => $data)
                                <div class="col-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ strtoupper(str_replace(['bitcoin', 'ethereum', 'tether', 'binancecoin'], ['BTC', 'ETH', 'USDT', 'BNB'], $coin)) }}</strong>
                                            <div class="small text-muted">{{ ucfirst($coin) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">${{ number_format($data['usd'], 2) }}</div>
                                            <div class="small {{ $data['usd_24h_change'] >= 0 ? 'price-up' : 'price-down' }}">
                                                <i class="fas fa-{{ $data['usd_24h_change'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                                {{ number_format(abs($data['usd_24h_change']), 2) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="text-center mt-3">
                        <small class="text-muted">{{ __('Data provided by CoinGecko') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TradingView Widget -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h3 class="text-center mb-4">{{ __('Real-Time Market Charts') }}</h3>
                <div class="crypto-card p-4">
                    <!-- TradingView Widget BEGIN -->
                    <div class="tradingview-widget-container">
                        <div id="tradingview_chart" style="height: 400px;"></div>
                        <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                        {
                            "autosize": true,
                            "symbol": "BINANCE:BTCUSDT",
                            "interval": "D",
                            "timezone": "Etc/UTC",
                            "theme": "light",
                            "style": "1",
                            "locale": "{{ app()->getLocale() }}",
                            "toolbar_bg": "#f1f3f6",
                            "enable_publishing": false,
                            "allow_symbol_change": true,
                            "container_id": "tradingview_chart"
                        }
                        </script>
                    </div>
                    <!-- TradingView Widget END -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">{{ __('Why Choose Our Platform?') }}</h2>
            <p class="text-muted">{{ __('Built for traders, by traders. Experience the difference.') }}</p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="crypto-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <h5>{{ __('Secure Trading') }}</h5>
                    <p class="text-muted">{{ __('Advanced security measures including 2FA, cold storage, and encrypted transactions.') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="crypto-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <h5>{{ __('Admin Approval') }}</h5>
                    <p class="text-muted">{{ __('Manual deposit verification ensures the highest level of fund security and compliance.') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="crypto-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <h5>{{ __('Real-Time Charts') }}</h5>
                    <p class="text-muted">{{ __('Professional TradingView charts with advanced technical analysis tools.') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="crypto-card p-4 text-center h-100">
                    <div class="feature-icon">
                        <i class="fas fa-globe text-white"></i>
                    </div>
                    <h5>{{ __('Multi-Language') }}</h5>
                    <p class="text-muted">{{ __('Available in English, Italian, French, German, and Russian.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">{{ __('Our Services') }}</h2>
            <p class="text-muted">{{ __('Everything you need for successful cryptocurrency trading') }}</p>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <h4>{{ __('Cryptocurrency Trading') }}</h4>
                <p class="text-muted mb-4">
                    {{ __('Trade major cryptocurrencies including Bitcoin, Ethereum, and many more with competitive fees and instant execution.') }}
                </p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Spot Trading') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Real-time Market Data') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Advanced Order Types') }}</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Portfolio Management') }}</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="crypto-card p-4">
                    <h6 class="mb-3">{{ __('Supported Cryptocurrencies') }}</h6>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle me-2" style="width: 24px; height: 24px;"></div>
                                <span>Bitcoin (BTC)</span>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary rounded-circle me-2" style="width: 24px; height: 24px;"></div>
                                <span>Ethereum (ETH)</span>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-success rounded-circle me-2" style="width: 24px; height: 24px;"></div>
                                <span>Tether (USDT)</span>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning rounded-circle me-2" style="width: 24px; height: 24px;"></div>
                                <span>Binance Coin (BNB)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <div class="crypto-card p-4">
                    <div class="text-center">
                        <i class="fas fa-users text-primary" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">{{ __('Trusted by Traders Worldwide') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">{{ __('About CryptoBroker') }}</h2>
                <p class="text-muted mb-4">
                    {{ __('We are a leading cryptocurrency trading platform dedicated to providing secure, reliable, and user-friendly services to traders worldwide. Our mission is to democratize access to cryptocurrency markets while maintaining the highest standards of security and compliance.') }}
                </p>
                <div class="row">
                    <div class="col-6">
                        <h5 class="text-primary">5+ {{ __('Years') }}</h5>
                        <p class="small text-muted">{{ __('Industry Experience') }}</p>
                    </div>
                    <div class="col-6">
                        <h5 class="text-primary">24/7</h5>
                        <p class="small text-muted">{{ __('Customer Support') }}</p>
                    </div>
                </div>
                <a href="{{ route('about') }}" class="btn btn-primary">{{ __('Learn More') }}</a>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">{{ __('Ready to Start Trading?') }}</h2>
        <p class="lead mb-4">
            {{ __('Join thousands of traders who trust our platform for their cryptocurrency trading needs.') }}
        </p>
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-warning btn-lg">
                <i class="fas fa-chart-line"></i> {{ __('Go to Dashboard') }}
            </a>
        @else
            <a href="{{ route('register') }}" class="btn btn-warning btn-lg me-3">
                <i class="fas fa-user-plus"></i> {{ __('Create Account') }}
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                <i class="fas fa-sign-in-alt"></i> {{ __('Login') }}
            </a>
        @endauth
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Auto-refresh crypto prices every 30 seconds
    setInterval(function() {
        // This would make an AJAX call to refresh prices
        // Implementation depends on your API setup
    }, 30000);
</script>
@endpush
