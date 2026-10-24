# DineFlow AI / Tendorabuy — Production Pre-Deployment Checklist (Phase 1)

This checklist provides a clear, step-by-step verification framework for deploying DineFlow AI to production on `tendorabuy.in`.

---

## A. Domain
- [ ] Root Domain `tendorabuy.in` active and registered
- [ ] Subdomain `app.tendorabuy.in` configured for SaaS Multi-Tenant Web App & POS
- [ ] Subdomain `api.tendorabuy.in` configured for REST & Offline Sync Gateway
- [ ] Subdomain `pos.tendorabuy.in` configured (optional alias for direct POS access)

## B. VPS Server
- [ ] Cloud VPS provisioned (Ubuntu 22.04 LTS or 24.04 LTS)
- [ ] Minimum 2 vCPU, 4GB RAM, 20GB SSD allocated
- [ ] Static Public IPv4 assigned
- [ ] SSH Key Authentication configured (root password login disabled)
- [ ] UFW Firewall active: ports 22 (SSH), 80 (HTTP), 443 (HTTPS) open

## C. Ubuntu Packages & Dependencies
- [ ] System updated (`sudo apt update && sudo apt upgrade -y`)
- [ ] Git, Curl, Unzip, Htop installed
- [ ] Node.js (v18+ / v20+) & npm installed
- [ ] Composer 2.x installed globally

## D. Nginx Web Server
- [ ] Nginx installed and running (`systemctl status nginx`)
- [ ] Server blocks configured for `tendorabuy.in`, `app.tendorabuy.in`, `api.tendorabuy.in`
- [ ] Document root pointed to `/var/www/dineflow/public`
- [ ] `try_files $uri $uri/ /index.php?$query_string;` configured
- [ ] Security headers active (`X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`)

## E. PHP 8.2+
- [ ] PHP 8.2-FPM installed and running
- [ ] Required PHP extensions installed: `php8.2-fpm`, `php8.2-mysql`, `php8.2-mbstring`, `php8.2-xml`, `php8.2-bcmath`, `php8.2-curl`, `php8.2-zip`, `php8.2-intl`, `php8.2-gd`
- [ ] `upload_max_filesize = 10M` and `post_max_size = 12M` configured in `php.ini`
- [ ] OPcache enabled for production performance

## F. MySQL 8.0 Database
- [ ] MySQL 8.0 Server installed and secured (`mysql_secure_installation`)
- [ ] Database `dineflow_ai` created with `utf8mb4` character set
- [ ] Dedicated user `dineflow_prod` created with strong password
- [ ] Remote root access disabled; bound to `127.0.0.1`

## G. Laravel Production Application
- [ ] Repository cloned to `/var/www/dineflow`
- [ ] Production dependencies installed (`composer install --no-dev --optimize-autoloader`)
- [ ] `APP_KEY` generated (`php artisan key:generate`)
- [ ] Storage symlink created (`php artisan storage:link`)
- [ ] Directory permissions set: `chown -R www-data:www-data /var/www/dineflow` & `chmod -R 775 storage bootstrap/cache`
- [ ] Route, config, and view caches generated (`php artisan config:cache`, `route:cache`, `view:cache`)

## H. SSL / HTTPS
- [ ] Let's Encrypt Certbot installed (`sudo apt install certbot python3-certbot-nginx`)
- [ ] Wildcard / Subdomain SSL certificate issued (`certbot --nginx -d tendorabuy.in -d app.tendorabuy.in -d api.tendorabuy.in`)
- [ ] Automatic HTTPS redirect enabled
- [ ] Certbot auto-renewal timer active (`systemctl status certbot.timer`)

## I. SMTP & Transactional Email
- [ ] Transactional email provider active (SendGrid / Mailgun / AWS SES)
- [ ] SMTP host, port (587 TLS), username, and API key configured in `.env`
- [ ] Sender email `support@tendorabuy.in` verified in email provider
- [ ] Test email sent successfully via `php artisan tinker`

## J. Environment Variables (.env)
- [ ] `APP_ENV=production` set
- [ ] `APP_DEBUG=false` set
- [ ] `APP_URL=https://app.tendorabuy.in` set
- [ ] `SESSION_DRIVER=database` or `SESSION_DRIVER=file`
- [ ] `CACHE_STORE=database` or `CACHE_STORE=file`
- [ ] `QUEUE_CONNECTION=database`
- [ ] No test passwords or fallback keys present

## K. Database Migrations & Data Integrity
- [ ] All 22 migrations applied in non-destructive mode (`php artisan migrate --force`)
- [ ] Coupon per-tenant unique composite index verified (`['restaurant_id', 'code']`)
- [ ] Order items `menu_item_id` nullable migration verified
- [ ] Zero mock data injected into production tables

## L. Backup & Disaster Recovery
- [ ] Automated daily backup script created at `/usr/local/bin/dineflow_backup.sh`
- [ ] Daily 2:00 AM cron job scheduled
- [ ] 14-day retention rotation policy active
- [ ] Test database dump and restore verified without data loss

## M. Windows POS Desktop Application
- [ ] `windows-app/` Node dependencies installed (`npm install`)
- [ ] Embedded SQLite engine initialized (`sql.js`)
- [ ] Production Windows executable built (`npm run build`)
- [ ] POS terminal connects to `https://api.tendorabuy.in` when online

## N. Offline Sync Engine
- [ ] `sync_queue` table active in local SQLite
- [ ] Exponential backoff retry verified (`2s`, `5s`, `10s`, `20s`, max `120s`)
- [ ] Idempotency verified on server (`POST /api/sync/orders` with client UUID)
- [ ] Price recalculation from authoritative menu catalog verified
- [ ] Monotonic status progression verified (rejects status regression)
- [ ] Cloud-to-desktop order reconciliation verified (`GET /api/sync/orders/since`)

## O. Pilot Restaurant Onboarding (#1)
- [ ] Restaurant tenant registered via `/register`
- [ ] 8-step onboarding wizard completed
- [ ] Legal GSTIN & currency (INR ₹) configured
- [ ] Table QR codes generated & printed via `/admin/qr-generator`
- [ ] Staff PINs created for Manager, Cashier, Chef, Waiter
- [ ] POS terminal opened at cashier counter (`/pos`)
- [ ] Kitchen KDS screen opened in kitchen (`/kitchen`)
- [ ] Thermal receipt printer (58mm/80mm) tested

## P. Final Go-Live & Handover
- [ ] Live customer mobile QR scan ➔ Cart ➔ Order ➔ POS ➔ KDS ➔ Payment ➔ Receipt verified
- [ ] End-of-day Z-report reviewed
- [ ] Restaurant owner and staff trained on system operations
- [ ] Go-live sign-off completed
