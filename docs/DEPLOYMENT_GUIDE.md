# 🚀 DineFlow AI - Production Deployment & Operations Runbook

## 1. System Requirements & Prerequisites

- **PHP Version**: PHP 8.2 or higher (Extensions: `pdo_sqlite`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`)
- **Web Server**: Nginx / Apache with URL rewrite support
- **Node.js**: Node.js 18.x or 20.x LTS & npm
- **Database**: SQLite (local/embedded) or MySQL 8.0+ / MariaDB 10.6+

---

## 2. Server Installation & Configuration

### 2.1 Clone Repository & Install Dependencies
```bash
git clone https://github.com/Shubh-Patel-07/DineFlow-AI.git /var/www/dineflow
cd /var/www/dineflow

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies
npm ci
npm run build
```

### 2.2 Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure `.env` settings:
```ini
APP_NAME="DineFlow AI"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://pos.dineflow.ai

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/dineflow/database/database.sqlite

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### 2.3 Database Setup & Optimization
```bash
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --class=ComprehensiveDemoSeeder --force

# Optimize Laravel Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2.4 File Permissions
```bash
chown -R www-data:www-data /var/www/dineflow
chmod -R 775 /var/www/dineflow/storage /var/www/dineflow/bootstrap/cache
```

---

## 3. Nginx Virtual Host Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name pos.dineflow.ai;
    root /var/www/dineflow/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
