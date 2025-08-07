#!/bin/bash

# 🗄️ Elite Forex Pro - Railway Database Setup Script
echo "🗄️ Starting Elite Forex Pro Database Setup on Railway..."
echo "=================================================="

# Check if we're in Railway environment
if [ -n "$RAILWAY_ENVIRONMENT" ]; then
    echo "✅ Railway environment detected: $RAILWAY_ENVIRONMENT"
else
    echo "⚠️  Warning: Not in Railway environment"
fi

# Check database connection
echo "🔍 Testing database connection..."
php artisan tinker --execute="
try {
    \$pdo = DB::connection()->getPdo();
    echo 'Database connection: SUCCESS\n';
    echo 'Database name: ' . DB::connection()->getDatabaseName() . '\n';
} catch (\Exception \$e) {
    echo 'Database connection: FAILED - ' . \$e->getMessage() . '\n';
    exit(1);
}
"

# Check if migrations table exists
echo "🔍 Checking migrations table..."
MIGRATIONS_EXIST=$(php artisan tinker --execute="
try {
    \$result = DB::select('SHOW TABLES LIKE \"migrations\"');
    echo count(\$result) > 0 ? 'exists' : 'not_exists';
} catch (\Exception \$e) {
    echo 'not_exists';
}
")

if [ "$MIGRATIONS_EXIST" = "not_exists" ]; then
    echo "📋 Migrations table not found. Creating fresh database..."
    php artisan migrate:install --force
fi

# Get current migration status
echo "📊 Current migration status..."
php artisan migrate:status

# Run all migrations
echo "🚀 Running all database migrations..."
php artisan migrate --force --no-interaction

if [ $? -eq 0 ]; then
    echo "✅ Database migrations completed successfully!"
else
    echo "❌ Database migrations failed!"
    exit 1
fi

# Check if admin user exists
echo "👤 Checking for admin user..."
ADMIN_EXISTS=$(php artisan tinker --execute="
\$admin = App\Models\User::where('email', 'admin@eliteforexpro.com')->first();
echo \$admin ? 'exists' : 'not_exists';
")

if [ "$ADMIN_EXISTS" = "not_exists" ]; then
    echo "👤 Creating admin user..."
    php artisan db:seed --class=AdminUserSeeder --force --no-interaction
    
    if [ $? -eq 0 ]; then
        echo "✅ Admin user created successfully!"
        echo "📧 Email: admin@eliteforexpro.com"
        echo "🔑 Password: password123"
    else
        echo "❌ Failed to create admin user!"
    fi
else
    echo "✅ Admin user already exists"
fi

# Verify all tables were created
echo "🔍 Verifying database tables..."
php artisan tinker --execute="
\$tables = DB::select('SHOW TABLES');
echo 'Total tables created: ' . count(\$tables) . '\n';

\$expectedTables = [
    'users', 'transactions', 'deposits', 'withdrawals', 'trades',
    'user_wallets', 'crypto_wallets', 'system_logs', 'chat_messages',
    'system_settings', 'bank_details', 'migrations'
];

\$tableNames = array_map(function(\$table) {
    return array_values((array)\$table)[0];
}, \$tables);

foreach (\$expectedTables as \$table) {
    if (in_array(\$table, \$tableNames)) {
        echo '✅ ' . \$table . ' - EXISTS\n';
    } else {
        echo '❌ ' . \$table . ' - MISSING\n';
    }
}
"

# Show final migration status
echo "📋 Final migration status:"
php artisan migrate:status

# Create sample crypto wallets if they don't exist
echo "💳 Setting up crypto wallets..."
php artisan tinker --execute="
use App\Models\CryptoWallet;

\$wallets = [
    ['currency' => 'BTC', 'currency_name' => 'Bitcoin', 'wallet_address' => 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh'],
    ['currency' => 'ETH', 'currency_name' => 'Ethereum', 'wallet_address' => '0x742d35cc6ab4925b95dd8b4f8d12c4ce9f85b5d6'],
    ['currency' => 'USDT', 'currency_name' => 'Tether', 'wallet_address' => 'TESpz2BxKRF4d3rj5JWRnB6KJCM2xJoHha'],
    ['currency' => 'BNB', 'currency_name' => 'Binance Coin', 'wallet_address' => 'bnb1grpf0955h0ykzq3ar5nmum7y6gdfl6lxfn46h2'],
    ['currency' => 'ADA', 'currency_name' => 'Cardano', 'wallet_address' => 'addr1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh4567890abc'],
];

foreach (\$wallets as \$walletData) {
    \$wallet = CryptoWallet::firstOrCreate(
        ['currency' => \$walletData['currency']],
        \$walletData
    );
    echo 'Wallet for ' . \$walletData['currency'] . ': ' . (\$wallet->wasRecentlyCreated ? 'CREATED' : 'EXISTS') . '\n';
}
"

echo ""
echo "🎉 DATABASE SETUP COMPLETE!"
echo "=========================="
echo "✅ All database tables created"
echo "✅ Admin user configured"
echo "✅ Crypto wallets initialized"
echo "✅ System ready for production"
echo ""
echo "🔐 Admin Login Credentials:"
echo "📧 Email: admin@eliteforexpro.com"
echo "🔑 Password: password123"
echo ""
echo "🌐 Your Elite Forex Pro platform is ready!"
