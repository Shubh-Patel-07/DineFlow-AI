#!/bin/bash
# ==============================================================================
# TENDORABUY / DINEFLOW AI — PRODUCTION 500 ERROR AUTO-DIAGNOSTIC & FIX
# Run on AWS EC2 Terminal:
#   sudo bash fix_production_500.sh
# ==============================================================================

set -e

echo "================================================================="
echo " 🔍 STARTING TENDORABUY PRODUCTION 500 AUTO-DIAGNOSTIC & FIX     "
echo "================================================================="

# 1. Locate App Directory
APP_DIR="/var/www/tendorabuy"
if [ ! -d "$APP_DIR" ]; then
    if [ -d "/var/www/dineflow" ]; then
        APP_DIR="/var/www/dineflow"
    elif [ -d "$HOME/DineFlow-AI" ]; then
        APP_DIR="$HOME/DineFlow-AI"
    elif [ -d "$HOME/tendorabuy" ]; then
        APP_DIR="$HOME/tendorabuy"
    else
        echo "❌ Error: Could not locate project directory."
        exit 1
    fi
fi

echo ">> Application Directory: $APP_DIR"
cd "$APP_DIR"

# 2. Fix Directory Permissions
echo ">> Fixing Storage and Bootstrap Cache Permissions..."
mkdir -p "$APP_DIR/storage/logs" "$APP_DIR/storage/framework/cache/data" "$APP_DIR/storage/framework/sessions" "$APP_DIR/storage/framework/views" "$APP_DIR/bootstrap/cache"
sudo chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 777 "$APP_DIR/storage/logs"

# 3. Verify .env & Application Key
echo ">> Checking .env and Application Key..."
if [ ! -f "$APP_DIR/.env" ]; then
    if [ -f "$APP_DIR/deployment/.env.production.template" ]; then
        cp "$APP_DIR/deployment/.env.production.template" "$APP_DIR/.env"
        echo ">> Created .env from production template."
    elif [ -f "$APP_DIR/.env.example" ]; then
        cp "$APP_DIR/.env.example" "$APP_DIR/.env"
        echo ">> Created .env from .env.example."
    fi
fi

# Ensure APP_KEY exists
if ! grep -q "APP_KEY=base64:" "$APP_DIR/.env"; then
    echo ">> Generating missing APP_KEY..."
    php artisan key:generate --force
fi

# 4. Detect Active PHP-FPM Version & Socket
echo ">> Detecting PHP-FPM socket..."
PHP_SOCK=$(ls /var/run/php/php*-fpm.sock 2>/dev/null | head -n 1)

if [ -z "$PHP_SOCK" ]; then
    echo "⚠️ Warning: No PHP-FPM socket found in /var/run/php/. Trying to start PHP-FPM service..."
    sudo systemctl restart php*-fpm 2>/dev/null || true
    PHP_SOCK=$(ls /var/run/php/php*-fpm.sock 2>/dev/null | head -n 1)
fi

echo ">> Active PHP-FPM Socket: $PHP_SOCK"

# 5. Fix Nginx Configuration
if [ -n "$PHP_SOCK" ]; then
    NGINX_CONF="/etc/nginx/sites-available/tendorabuy.conf"
    if [ -f "$NGINX_CONF" ]; then
        echo ">> Syncing Nginx fastcgi_pass socket to $PHP_SOCK..."
        sudo sed -i "s|fastcgi_pass unix:/var/run/php/.*\.sock;|fastcgi_pass unix:$PHP_SOCK;|g" "$NGINX_CONF"
        sudo ln -sf "$NGINX_CONF" /etc/nginx/sites-enabled/tendorabuy.conf
        sudo rm -f /etc/nginx/sites-enabled/default
    fi
fi

# Test Nginx
sudo nginx -t && sudo systemctl reload nginx

# 6. Pull Latest Production Code
echo ">> Pulling latest code from GitHub..."
git checkout feature-teamlead 2>/dev/null || git checkout main 2>/dev/null || true
git pull origin feature-teamlead 2>/dev/null || git pull origin main 2>/dev/null || true

# 7. Install / Update Dependencies
echo ">> Installing optimized production Composer packages..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 8. Run Database Migrations
echo ">> Running database migrations..."
php artisan migrate --force

# 9. Clear and Optimize Caches
echo ">> Rebuilding production caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Re-assert Permissions
sudo chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

# 11. Final Health Check
echo ">> Verifying HTTP 200 response on localhost..."
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1/ || true)

echo "================================================================="
if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "301" ] || [ "$HTTP_CODE" = "302" ]; then
    echo " 🎉 SUCCESS! Local Web Server returned HTTP $HTTP_CODE (Online) "
    echo " Tendorabuy is LIVE and operational!                             "
else
    echo " ⚠️ Local curl returned HTTP $HTTP_CODE. Check /var/log/nginx/error.log and storage/logs/laravel.log"
fi
echo "================================================================="
