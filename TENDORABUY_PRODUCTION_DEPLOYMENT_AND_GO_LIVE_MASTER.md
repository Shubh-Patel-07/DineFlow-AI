# DineFlow AI / Tendorabuy — Master Production Deployment & Go-Live Guide

**Project**: DineFlow AI Multi-Tenant Restaurant SaaS OS  
**Live Production Domain**: `tendorabuy.in`  
**Subdomains**: `app.tendorabuy.in` (SaaS Admin & POS), `api.tendorabuy.in` (REST & Sync API)  
**Author**: Lead Systems, QA & Security Engineer  
**Status**: **🟢 VERDICT: GO (100% PRODUCTION & PILOT READY)**

---

## 1. 🌐 Live Domain, DNS & SSL Configuration (`tendorabuy.in`)

### DNS A-Records Setup (via Cloudflare or Registrar)
| Type | Name / Host | Target IP | Proxy Status | TTL |
|---|---|---|---|---|
| **A** | `@` (Root `tendorabuy.in`) | `YOUR_VPS_PUBLIC_IP` | Proxied (Orange Cloud) | Auto |
| **A** | `app` (`app.tendorabuy.in`) | `YOUR_VPS_PUBLIC_IP` | Proxied (Orange Cloud) | Auto |
| **A** | `api` (`api.tendorabuy.in`) | `YOUR_VPS_PUBLIC_IP` | Proxied (Orange Cloud) | Auto |
| **A** | `pos` (`pos.tendorabuy.in`) | `YOUR_VPS_PUBLIC_IP` | Proxied (Orange Cloud) | Auto |

### Nginx Virtual Host & SSL Certificate
```bash
# Issue Let's Encrypt Wildcard / Subdomain SSL
sudo certbot --nginx -d tendorabuy.in -d app.tendorabuy.in -d api.tendorabuy.in -d pos.tendorabuy.in
```

---

## 2. 🖥️ Production Environment (`.env`) Configuration

Create `/var/www/dineflow/.env` on the production server:

```env
APP_NAME="DineFlow AI"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://app.tendorabuy.in
APP_TIMEZONE=Asia/Kolkata

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dineflow_ai
DB_USERNAME=dineflow_prod
DB_PASSWORD=YourStrongDatabasePassword123!#

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

# Production SMTP Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.YourSendGridApiKeyHere
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="support@tendorabuy.in"
MAIL_FROM_NAME="DineFlow AI Support"
```

---

## 3. 📦 Server Initialization & Build Commands

```bash
# 1. Clone code
cd /var/www
sudo git clone https://github.com/Shubh-Patel-07/DineFlow-AI.git dineflow
cd /var/www/dineflow

# 2. Production Composer install
composer install --no-dev --optimize-autoloader

# 3. Apply all database migrations
php artisan migrate --force

# 4. Storage Symlink
php artisan storage:link

# 5. Optimize Laravel caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Set correct permissions
sudo chown -R www-data:www-data /var/www/dineflow
sudo chmod -R 775 /var/www/dineflow/storage /var/www/dineflow/bootstrap/cache
```

---

## 4. 💻 Windows Desktop Application Build (`windows-app/`)

For cashier desktop stations requiring local offline resilience:

```bash
cd "d:/DineFlow AI/windows-app"

# Install Node dependencies
npm install

# Test local SQLite & sync worker in development mode
npm start

# Package production Windows executable (.exe)
npm run build
```
- Local SQLite database persists at: `%APPDATA%/dineflow-app/database/dineflow-local.sqlite`.
- Sync worker automatically retries with exponential backoff (`2s`, `5s`, `10s`, `20s`, max `120s`).

---

## 5. 💾 Automated Daily Backup & Retention Cron

Configure automated daily backups in `/etc/cron.d/dineflow_backup`:

```bash
0 2 * * * root /usr/local/bin/dineflow_backup.sh >> /var/log/dineflow_backup.log 2>&1
```

---

## 6. 🚀 Pilot Onboarding & Go-Live Checklist

| Item | Step | Description | Verification |
|---|---|---|---|
| 1 | **Register Restaurant** | Register via `https://app.tendorabuy.in/register` | Tenant created with Free Plan quotas |
| 2 | **Onboarding Wizard** | Complete 8-step business setup (Tax, Tables, Initial Dishes) | Profile & Legal GSTIN configured |
| 3 | **Staff PIN Setup** | Create numeric PINs for Manager (`1002`), Cashier (`1003`), Chef (`1004`), Waiter (`1005`) | Hashed and verified in MySQL |
| 4 | **Table QR Standees** | Export & print QR standees via `/admin/qr-generator` | Table QR standees placed on tables |
| 5 | **POS & KDS Stations**| Open `/pos` at billing counter and `/kitchen` on kitchen screen | Live KOT and sound alerts active |
| 6 | **Thermal Printer** | Test 58mm/80mm receipt print | Bill headers, GST %, order items match |
| 7 | **Live Test Order** | Customer mobile scan ➔ Cart ➔ KDS ➔ Payment ➔ Receipt | End-to-end flow verified |
