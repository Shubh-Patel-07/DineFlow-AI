# DineFlow AI — Production Cloud Deployment Guide

This guide details the end-to-end production deployment on an Ubuntu 22.04 / 24.04 LTS VPS with Nginx, PHP 8.2+, MySQL 8+, and Let's Encrypt SSL.

---

## 1. Server Prerequisites & Package Installation

```bash
# Update Ubuntu package lists
sudo apt update && sudo apt upgrade -y

# Install Nginx, Git, Unzip, and MySQL Server
sudo apt install -y nginx git unzip curl mysql-server

# Add PHP 8.2 Repository & Extensions
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-intl php8.2-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 2. MySQL Database Setup

```sql
sudo mysql
CREATE DATABASE dineflow_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dineflow_user'@'localhost' IDENTIFIED BY 'StrongDBPassword123!#';
GRANT ALL PRIVILEGES ON dineflow_ai.* TO 'dineflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 3. Clone Repository & Setup Application

```bash
# Navigate to web root
cd /var/www
sudo git clone https://github.com/Shubh-Patel-07/DineFlow-AI.git dineflow
cd /var/www/dineflow

# Install Composer dependencies (Production)
composer install --no-dev --optimize-autoloader

# Setup Environment Configuration
cp .env.example .env
php artisan key:generate

# Edit .env with production database credentials
nano .env
# Set:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://your-restaurant-domain.com
# DB_DATABASE=dineflow_ai
# DB_USERNAME=dineflow_user
# DB_PASSWORD=StrongDBPassword123!#

# Run Database Migrations
php artisan migrate --force

# Create Storage Symlink
php artisan storage:link

# Set Correct File Permissions
sudo chown -R www-data:www-data /var/www/dineflow
sudo chmod -R 775 /var/www/dineflow/storage /var/www/dineflow/bootstrap/cache
```

---

## 4. Nginx Server Configuration

Create `/etc/nginx/sites-available/dineflow.conf`:

```nginx
server {
    listen 80;
    server_name your-restaurant-domain.com;
    root /var/www/dineflow/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";

    index index.php index.html;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/dineflow.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## 5. SSL / HTTPS Setup via Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-restaurant-domain.com
```

---

## 6. Laravel Optimization & Service Verification

```bash
cd /var/www/dineflow
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```
