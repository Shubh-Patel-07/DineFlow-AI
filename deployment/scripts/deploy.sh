#!/bin/bash
# ==============================================================================
# TENDORABUY / DINEFLOW AI — ZERO-DOWNTIME PRODUCTION DEPLOYMENT SCRIPT
# ==============================================================================

set -e

APP_DIR="/var/www/tendorabuy"
PHP_FPM_SERVICE="php8.2-fpm"

echo "=========================================================="
echo " Starting Tendorabuy Production Deployment...             "
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
systemctl reload $PHP_FPM_SERVICE

# 9. Bring application back live
echo ">> Bringing application back online..."
php artisan up

echo "=========================================================="
echo " Deployment Successfully Completed!                      "
echo " Tendorabuy is LIVE and operational.                     "
echo "=========================================================="
