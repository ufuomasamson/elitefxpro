#!/bin/bash

# Railway Deployment Script for Elite Forex Pro
echo "🚀 Starting Elite Forex Pro deployment on Railway..."

# Install PHP dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configurations
echo "⚡ Optimizing Laravel for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed admin user if needed
echo "👤 Setting up admin user..."
php artisan db:seed --class=AdminUserSeeder --force

# Set proper permissions
echo "🔒 Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

echo "✅ Elite Forex Pro deployment completed successfully!"
echo "🌐 Your application should now be accessible via Railway URL"

# Start the application
echo "🚀 Starting Elite Forex Pro server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
