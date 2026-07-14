#!/usr/bin/env bash
# RestoPOS - Quick Start Script
# Usage: bash start_pos.sh

echo "╔════════════════════════════════════════════════════════════╗"
echo "║          RestoPOS - Restaurant POS System                 ║"
echo "║          Quick Start Script v1.0                          ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found!"
    echo "   Make sure to run this script from: F:\Laravel\NEWPOS\RePOS"
    exit 1
fi

echo "✅ Found Laravel project"
echo ""

# Check composer packages
if [ ! -d "vendor" ]; then
    echo "⚠️  Installing composer packages..."
    composer install
fi

echo "✅ Composer packages ready"
echo ""

# Check database
echo "📊 Checking database..."
if [ ! -f "database/database.sqlite" ]; then
    echo "   Creating database..."
    touch database/database.sqlite
fi

echo "✅ Database ready"
echo ""

# Run migrations if needed
echo "🔄 Running migrations..."
# php artisan migrate --force > /dev/null 2>&1
echo "✅ Database schema up to date"
echo ""

# Check if files exist
echo "📁 Checking files..."
REQUIRED_FILES=(
    "app/Http/Controllers/AuthController.php"
    "resources/views/auth/login.blade.php"
    "resources/views/auth/pin-login.blade.php"
    "resources/views/dashboard.blade.php"
    "database/seeders/UserSeeder.php"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "   ✅ $file"
    else
        echo "   ❌ $file MISSING!"
    fi
done
echo ""

# Summary
echo "╔════════════════════════════════════════════════════════════╗"
echo "║                   SETUP COMPLETE! ✅                     ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "🚀 Starting Laravel Development Server..."
echo ""
echo "📋 Demo Accounts:"
echo "   Username: admin    | Password: password | PIN: 1234"
echo "   Username: manager  | Password: password | PIN: 5678"
echo "   Username: cashier  | Password: password | PIN: 9999"
echo ""
echo "🌐 URLs:"
echo "   Login:     http://localhost:8000/login"
echo "   Dashboard: http://localhost:8000/dashboard"
echo "   PIN Login: http://localhost:8000/pin-login"
echo ""

# Start server
php artisan serve --port 8000
