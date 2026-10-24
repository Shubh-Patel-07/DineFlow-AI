# 🚀 DineFlow AI — Production Deployment & Operations Guide

This document provides complete, step-by-step instructions for deploying and running **DineFlow AI** on a production Linux server (Ubuntu 22.04 / 24.04 LTS) with Nginx, PHP 8.2+, MySQL 8+, and Let's Encrypt SSL.

---

## 1. System Requirements & Architecture

- **Operating System**: Ubuntu 22.04 or 24.04 LTS (64-bit)
- **Web Server**: Nginx (1.18+)
- **PHP Version**: PHP 8.2 or 8.3 (with `php-fpm`, `php-mysql`, `php-mbstring`, `php-xml`, `php-bcmath`, `php-curl`, `php-zip`, `php-gd`, `php-intl`)
- **Database**: MySQL 8.0+ or MariaDB 10.6+
- **Process Manager**: Systemd / Supervisor (for queue workers)
- **SSL**: Let's Encrypt Certbot
- **Package Manager**: Composer 2.x

---

## 2. Server Provisioning & Package Installation

Run the following commands as `root` or `sudo` user:

```bash
# 1. Update system packages
sudo apt update && sudo apt upgrade -y

# 2. Install Nginx, Git, Unzip, and Curl
sudo apt install -y nginx git unzip curl certbot python3-certbot-nginx

# 3. Add Ondrej PHP repository and install PHP 8.2 + extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl \
    php8.2-sqlite3

# 4. Install Composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 5. Install MySQL Server
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

---

## 3. Production MySQL Database Setup

Log into MySQL as root and provision a dedicated database and user:

```sql
CREATE DATABASE dineflow_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dineflow_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_DB_PASSWORD';
GRANT ALL PRIVILEGES ON dineflow_production.* TO 'dineflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 4. Application Deployment & Configuration

```bash
# 1. Clone the repository into web root
sudo mkdir -p /var/www/dineflow
sudo git clone https://github.com/Shubh-Patel-07/DineFlow-AI.git /var/www/dineflow
cd /var/www/dineflow

# 2. Checkout the verified release branch
git checkout feature-teamlead

# 3. Install production PHP dependencies
composer install --no-dev --optimize-autoloader

# 4. Configure Production Environment File
cp .env.example .env

# Edit .env with your actual production settings:
nano .env
```

### Required `.env` Variables for Production:
```dotenv
APP_NAME="DineFlow AI"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://YOUR_DOMAIN.com
APP_TIMEZONE=Asia/Kolkata

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dineflow_production
DB_USERNAME=dineflow_user
DB_PASSWORD=YOUR_STRONG_DB_PASSWORD

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

# SMTP Mail Settings (e.g. AWS SES / Mailgun / SendGrid)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=YOUR_SMTP_USERNAME
MAIL_PASSWORD=YOUR_SMTP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="support@YOUR_DOMAIN.com"
MAIL_FROM_NAME="${APP_NAME}"
```

```bash
# 5. Generate secure Application Encryption Key
php artisan key:generate

# 6. Run Database Migrations & Seed Baseline Data
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force

# 7. Create Storage Symlink & Set Secure Directory Permissions
php artisan storage:link
sudo chown -R www-data:www-data /var/www/dineflow/storage /var/www/dineflow/bootstrap/cache
sudo chmod -R 775 /var/www/dineflow/storage /var/www/dineflow/bootstrap/cache

# 8. Cache Config, Routes & Views for Ultra-Fast Performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Nginx Web Server Configuration

Create a new Nginx virtual host configuration file:

```bash
sudo nano /etc/nginx/sites-available/dineflow.conf
```

Paste the following configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name YOUR_DOMAIN.com www.YOUR_DOMAIN.com;
    root /var/www/dineflow/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    index index.php index.html;

    charset utf-8;

    # Client body size for menu images / logos upload (Max 10M)
    client_max_body_size 10M;

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

    # Static assets caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }
}
```

Enable the site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/dineflow.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 6. SSL Certificate Installation (Let's Encrypt)

Obtain and configure a free, auto-renewing SSL certificate:

```bash
sudo certbot --nginx -d YOUR_DOMAIN.com -d www.YOUR_DOMAIN.com
```

Select option `2` to automatically redirect all HTTP traffic to HTTPS.

---

## 7. Production Database Backup Strategy

Create an automated daily backup script:

```bash
sudo mkdir -p /var/backups/dineflow
sudo nano /usr/local/bin/dineflow-backup.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/dineflow"
DATE=$(date +"%Y%m%d_%H%M%S")
DB_NAME="dineflow_production"
DB_USER="dineflow_user"
DB_PASS="YOUR_STRONG_DB_PASSWORD"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > "$BACKUP_DIR/backup_${DB_NAME}_${DATE}.sql.gz"

# Retain backups for 14 days
find $BACKUP_DIR -type f -name "*.sql.gz" -mtime +14 -delete
```

Make executable and register in cron:

```bash
sudo chmod +x /usr/local/bin/dineflow-backup.sh
(crontab -l 2>/dev/null; echo "0 3 * * * /usr/local/bin/dineflow-backup.sh") | crontab -
```

### Restore Procedure:
```bash
gunzip < /var/backups/dineflow/backup_dineflow_production_YYYYMMDD_HHMMSS.sql.gz | mysql -u dineflow_user -p dineflow_production
```

---

## 8. Rollback & Maintenance Procedures

### Deploying Future Updates:
```bash
cd /var/www/dineflow
php artisan down --secret="secret-maintenance-bypass"
git pull origin feature-teamlead
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

### Emergency Rollback:
```bash
cd /var/www/dineflow
git checkout <PREVIOUS_STABLE_COMMIT_HASH>
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.2-fpm
```
