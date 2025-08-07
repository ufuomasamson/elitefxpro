<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Elite Forex Pro') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            
            .auth-gradient-bg {
                background: linear-gradient(135deg, #1c42c2 0%, #4169e1 100%) !important;
                min-height: 100vh;
            }
            
            .auth-pattern {
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='m36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            
            .glass-effect {
                backdrop-filter: blur(20px);
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            
            /* Ensure full page gradient coverage */
            html, body {
                background: linear-gradient(135deg, #1c42c2 0%, #4169e1 100%) !important;
                min-height: 100vh;
            }
        </style>
    </head>
    <body class="font-sans antialiased auth-gradient-bg auth-pattern min-h-screen" style="background: linear-gradient(135deg, #1c42c2 0%, #4169e1 100%) !important;">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-blue-600 font-bold text-lg">TT</span>
                    </div>
                    <span class="text-2xl font-bold text-white">Elite Forex Pro</span>
                </a>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md glass-effect rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-8 py-8">
                    {{ $slot }}
                </div>
            </div>
            
            <!-- Footer Link -->
            <div class="mt-6 text-center">
                <a href="/" class="text-white/80 hover:text-white transition-colors text-sm">
                    ← Back to Homepage
                </a>
            </div>
        </div>
    </body>
</html>
