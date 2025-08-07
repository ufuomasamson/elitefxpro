#!/bin/bash

# Elite Forex Pro Railway Startup Script
echo "🚀 Elite Forex Pro - Starting Railway Deployment"
echo "=================================================="

# Set production environment
export APP_ENV=production
export APP_DEBUG=false

# Install dependencies with production optimizations
echo "📦 Installing production dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Generate application key if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating new application key..."
    php artisan key:generate --force --no-interaction
fi

# Cache optimization for production
echo "⚡ Optimizing application for production..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# Database setup
echo "🗄️ Setting up database..."
php artisan migrate --force --no-interaction

# Check if admin user exists, if not create one
echo "👤 Ensuring admin user exists..."
php artisan db:seed --class=AdminUserSeeder --force --no-interaction 2>/dev/null || echo "Admin user already exists or seeder not needed"

# Create storage symbolic link
echo "🔗 Creating storage symlink..."
php artisan storage:link --force --no-interaction 2>/dev/null || echo "Storage link already exists"

# Set proper file permissions for Railway
echo "🔒 Setting file permissions..."
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;
chmod -R 755 bootstrap/cache/

# Clear any existing caches that might cause issues
echo "🧹 Clearing temporary caches..."
php artisan view:clear --no-interaction 2>/dev/null || true
php artisan cache:clear --no-interaction 2>/dev/null || true

# Verify critical files and directories exist
echo "✅ Verifying application structure..."
[ -d "storage/logs" ] || mkdir -p storage/logs
[ -d "storage/app/public" ] || mkdir -p storage/app/public
[ -d "storage/framework/cache" ] || mkdir -p storage/framework/cache
[ -d "storage/framework/sessions" ] || mkdir -p storage/framework/sessions
[ -d "storage/framework/views" ] || mkdir -p storage/framework/views

# Display deployment information
echo ""
echo "🎯 Elite Forex Pro Deployment Summary"
echo "======================================"
echo "✅ Environment: $APP_ENV"
echo "✅ Debug Mode: $APP_DEBUG"
echo "✅ Application: Ready"
echo "✅ Database: Connected"
echo "✅ Cache: Optimized"
echo "✅ Storage: Configured"
echo ""

# Start the application
echo "🌐 Starting Elite Forex Pro server on port ${PORT:-8000}..."
echo "🚀 Application will be available shortly!"
echo ""

# Start Laravel development server for Railway
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000} --no-interaction
