#!/bin/bash
# ==============================================================================
# DINEFLOW AI — AUTOMATIC DATABASE PERMISSION & 500 ERROR RESOLVER
# ==============================================================================

set -e

echo "=========================================================="
echo " 🛠️ Resolving MySQL Credentials & 500 Internal Error...    "
echo "=========================================================="

# 1. Determine active directory
if [ -d "/var/www/dineflow" ]; then
    APP_DIR="/var/www/dineflow"
elif [ -d "/var/www/tendorabuy" ]; then
    APP_DIR="/var/www/tendorabuy"
else
    APP_DIR="$(pwd)"
fi

echo ">> Target Directory: $APP_DIR"
cd "$APP_DIR"

# 2. Extract database credentials from .env
DB_USER=$(grep -E '^DB_USERNAME=' "$APP_DIR/.env" | head -n 1 | cut -d '=' -f2- | tr -d ' "' | tr -d '\r')
DB_PASS=$(grep -E '^DB_PASSWORD=' "$APP_DIR/.env" | head -n 1 | cut -d '=' -f2- | tr -d ' "' | tr -d '\r')
DB_NAME=$(grep -E '^DB_DATABASE=' "$APP_DIR/.env" | head -n 1 | cut -d '=' -f2- | tr -d ' "' | tr -d '\r')

echo ">> Configuring MySQL for User: [${DB_USER:-dineflow_user}] on Database: [${DB_NAME:-dineflow_ai}]..."

# 3. Synchronize MySQL internal credentials with .env
sudo mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME:-dineflow_ai}\`;"
sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER:-dineflow_user}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "ALTER USER '${DB_USER:-dineflow_user}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO '${DB_USER:-dineflow_user}'@'localhost' WITH GRANT OPTION;"

sudo mysql -e "CREATE USER IF NOT EXISTS '${DB_USER:-dineflow_user}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "ALTER USER '${DB_USER:-dineflow_user}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO '${DB_USER:-dineflow_user}'@'127.0.0.1' WITH GRANT OPTION;"

sudo mysql -e "FLUSH PRIVILEGES;"
echo ">> MySQL User & Permissions updated successfully!"

# 4. Fix storage and cache permissions
echo ">> Setting filesystem permissions..."
sudo mkdir -p "$APP_DIR/storage/framework/cache/data" "$APP_DIR/storage/framework/sessions" "$APP_DIR/storage/framework/views" "$APP_DIR/storage/logs" "$APP_DIR/bootstrap/cache"
sudo chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
sudo chmod -R 777 "$APP_DIR/storage/logs" "$APP_DIR/storage/framework"

# 4.1 Ensure valid APP_KEY
if ! grep -q "APP_KEY=base64:" "$APP_DIR/.env"; then
    echo ">> Generating missing APP_KEY..."
    php artisan key:generate --force
fi

# 5. Clear and Cache Laravel Configuration
echo ">> Refreshing Laravel config & routes..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# 6. Ensure Nginx points to the active directory
echo ">> Syncing Nginx root to $APP_DIR/public..."
sudo sed -i "s|root /var/www/.*/public;|root $APP_DIR/public;|g" /etc/nginx/sites-available/* 2>/dev/null || true
sudo sed -i "s|root /var/www/.*/public;|root $APP_DIR/public;|g" /etc/nginx/sites-enabled/* 2>/dev/null || true

# Test & reload Nginx
sudo nginx -t && sudo systemctl reload nginx
sudo systemctl reload php*-fpm 2>/dev/null || true

# 7. Bring application online
php artisan up

echo "=========================================================="
echo " ✅ 500 Error Fix Completed! DineFlow AI is LIVE!         "
echo "=========================================================="
