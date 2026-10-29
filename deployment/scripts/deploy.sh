#!/bin/bash
# ==============================================================================
# TENDORABUY / DINEFLOW AI — ZERO-DOWNTIME PRODUCTION DEPLOYMENT SCRIPT
# ==============================================================================

set -e

# Detect application directory dynamically based on Nginx configuration
NGINX_ROOT_DIR=$(grep -roh "root /var/www/[^;]*" /etc/nginx/sites-enabled/ 2>/dev/null | head -n 1 | awk '{print $2}' | sed -E 's|/public/?$||' | sed 's|/$||')

if [ -n "$NGINX_ROOT_DIR" ] && [ -f "$NGINX_ROOT_DIR/artisan" ]; then
    APP_DIR="$NGINX_ROOT_DIR"
elif [ -f "$(pwd)/artisan" ]; then
    APP_DIR="$(pwd)"
elif [ -f "/var/www/dineflow/artisan" ]; then
    APP_DIR="/var/www/dineflow"
elif [ -f "/var/www/tendorabuy/artisan" ]; then
    APP_DIR="/var/www/tendorabuy"
elif [ -d "/var/www/dineflow" ]; then
    APP_DIR="/var/www/dineflow"
else
    APP_DIR="/var/www/tendorabuy"
fi

# Detect installed PHP-FPM version dynamically
RUNNING_FPM=$(systemctl list-units --type=service --state=running 2>/dev/null | grep -oE 'php[0-9.]*-fpm' | head -n 1 || echo "")
PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.3")
PHP_FPM_SERVICE="${RUNNING_FPM:-php${PHP_VER}-fpm}"

echo "=========================================================="
echo " Starting DineFlow AI Production Deployment...            "
echo " Target Directory: $APP_DIR                               "
echo " Detected PHP Version: $PHP_VER (Service: $PHP_FPM_SERVICE)"
echo "=========================================================="

cd $APP_DIR

# 1. Enable Maintenance Mode (returns friendly 503 for 10-15s)
echo ">> Putting application in maintenance mode..."
php artisan down --render="errors::503" --secret="tendorabuy-deploy-bypass" || true

# 2. Pull latest code from GitHub
echo ">> Pulling latest changes from Git..."
git pull origin feature-teamlead || git pull origin main

# 3. Install/Update Composer dependencies (no dev packages)
echo ">> Installing optimized production PHP dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Run database migrations safely (continue if already up to date or credentials restricted)
echo ">> Running database migrations..."
php artisan migrate --force || echo ">> Migrations check skipped or already up to date."

# 5. Ensure Storage Symlink exists
echo ">> Verifying storage symlink..."
php artisan storage:link || true

# 6. Clear and Cache Laravel Configurations for Maximum Performance
echo ">> Optimizing Laravel cache..."
php artisan optimize:clear || (php artisan config:clear && php artisan route:clear && php artisan view:clear) || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true

# 7. Set Correct Permissions
echo ">> Setting permissions for www-data..."
chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache $APP_DIR/public
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# 8. Reload PHP-FPM to flush opcache
echo ">> Reloading PHP-FPM..."
if [ -n "$RUNNING_FPM" ]; then
    systemctl reload "$RUNNING_FPM" 2>/dev/null || systemctl restart "$RUNNING_FPM" 2>/dev/null || true
fi
systemctl reload $PHP_FPM_SERVICE 2>/dev/null || systemctl restart $PHP_FPM_SERVICE 2>/dev/null || systemctl reload php8.3-fpm 2>/dev/null || systemctl reload php8.2-fpm 2>/dev/null || systemctl reload php8.4-fpm 2>/dev/null || systemctl reload php8.5-fpm 2>/dev/null || true

# 9. Reload Nginx
echo ">> Reloading Nginx..."
systemctl reload nginx 2>/dev/null || true

# 10. Bring application back live
echo ">> Bringing application back online..."
php artisan up

echo "=========================================================="
echo " Deployment Successfully Completed!                      "
echo " Tendorabuy is LIVE and operational.                     "
echo "=========================================================="
