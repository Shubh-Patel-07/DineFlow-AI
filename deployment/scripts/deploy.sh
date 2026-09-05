#!/bin/bash
# ==============================================================================
# TENDORABUY / DINEFLOW AI — ZERO-DOWNTIME PRODUCTION DEPLOYMENT SCRIPT
# ==============================================================================

set -e

APP_DIR="/var/www/tendorabuy"

# Detect installed PHP-FPM version dynamically (e.g. php8.5-fpm, php8.3-fpm, php8.2-fpm)
PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "8.5")
PHP_FPM_SERVICE="php${PHP_VER}-fpm"

echo "=========================================================="
echo " Starting Tendorabuy Production Deployment...             "
echo " Detected PHP Version: $PHP_VER (Service: $PHP_FPM_SERVICE)"
echo "=========================================================="

cd $APP_DIR

# 1. Enable Maintenance Mode (returns friendly 503 for 10-15s)
echo ">> Putting application in maintenance mode..."
php artisan down --render="errors::503" --secret="tendorabuy-deploy-bypass" || true

# 2. Pull latest code from GitHub main/production branch
echo ">> Pulling latest changes from Git..."
git pull origin feature-teamlead

# 3. Install/Update Composer dependencies (no dev packages)
echo ">> Installing optimized production PHP dependencies..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# 4. Run database migrations safely
echo ">> Running database migrations..."
php artisan migrate --force

# 5. Ensure Storage Symlink exists
echo ">> Verifying storage symlink..."
php artisan storage:link || true

# 6. Clear and Cache Laravel Configurations for Maximum Performance
echo ">> Optimizing Laravel cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Set Correct Permissions
echo ">> Setting permissions for www-data..."
chown -R www-data:www-data $APP_DIR/storage $APP_DIR/bootstrap/cache
chmod -R 775 $APP_DIR/storage $APP_DIR/bootstrap/cache

# 8. Reload PHP-FPM to flush opcache
echo ">> Reloading PHP-FPM..."
systemctl reload $PHP_FPM_SERVICE 2>/dev/null || systemctl restart $PHP_FPM_SERVICE 2>/dev/null || systemctl reload php8.5-fpm 2>/dev/null || systemctl reload php8.3-fpm 2>/dev/null || systemctl reload php8.2-fpm 2>/dev/null || true

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
