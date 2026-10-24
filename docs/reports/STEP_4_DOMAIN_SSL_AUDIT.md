# DineFlow AI / DineFlow — Step 4: Domain DNS & SSL Readiness Audit

**Audit Date**: August 28, 2026  
**Auditor**: Lead Security & Infrastructure Engineer  
**Target Server IP**: `3.110.208.210`  
**Target Domain**: `dineflowai.duckdns.org` / `dineflowai.duckdns.org`

---

## 1. Domain & SSL Audit Matrix

| # | Audit Item | Current State | Status |
|---|---|---|---|
| 1 | **`dineflowai.duckdns.org` DNS A-Record** | Currently `NXDOMAIN` (awaiting DNS propagation from registrar) | 🟡 **NEEDS MANUAL ACTION** |
| 2 | **`dineflowai.duckdns.org` DNS A-Record** | Currently `NXDOMAIN` (awaiting DNS propagation from registrar) | 🟡 **NEEDS MANUAL ACTION** |
| 3 | **HTTPS Active** | Port 443 SSL listener not yet active | 🟡 **NEEDS MANUAL ACTION** |
| 4 | **SSL Certificate Installed** | Certbot certificate pending domain challenge verification | 🟡 **NEEDS MANUAL ACTION** |
| 5 | **HTTP to HTTPS Redirect** | Plain HTTP (Port 80) active; redirect pending SSL installation | 🟡 **NEEDS MANUAL ACTION** |
| 6 | **Nginx Configuration** | `/etc/nginx/sites-available/dineflow` configured for `dineflowai.duckdns.org`, `dineflowai.duckdns.org`, `3.110.208.210`, and `_` | 🟢 **VERIFIED** |
| 7 | **Laravel Application State** | All 25 migrations, UserSeeder, POS, KDS, Super Admin live & operational on `http://3.110.208.210` | 🟢 **VERIFIED** |
| 8 | **Scheduler / Expiration Cron** | Cron job active for `php artisan schedule:run` | 🟢 **VERIFIED** |

---

## 2. DNS Verification Details

When querying DNS root nameservers:
```
Query: dineflowai.duckdns.org ➔ NXDOMAIN (Non-Existent Domain / In Propagation)
Query: dineflowai.duckdns.org ➔ NXDOMAIN
```

### 📋 Required DNS Configuration at Domain Provider:

Ensure the following 3 records exist in your domain registrar's DNS panel (e.g. GoDaddy / Hostinger) and any conflicting old A-records (e.g. Shopify IPs `23.227.38.32`) are removed:

| Record Type | Host / Name | Value / Points To (AWS VPS IP) | TTL |
|---|---|---|---|
| **A** | `@` | **`3.110.208.210`** | 600s / 1/2 Hour |
| **A** | `app` | **`3.110.208.210`** | 600s / 1/2 Hour |
| **A** | `www` | **`3.110.208.210`** | 600s / 1/2 Hour |

---

## 3. Safe Command to Run AFTER DNS Propagation

Once DNS lookup returns `3.110.208.210`, execute the following single command in the AWS terminal to issue Let's Encrypt SSL and enable automatic HTTPS redirection:

```bash
sudo certbot --nginx -d dineflowai.duckdns.org -d dineflowai.duckdns.org -d www.dineflowai.duckdns.org --non-interactive --agree-tos -m shubhmpatel77@gmail.com --redirect
```

And update `APP_URL` in `.env`:
```bash
sudo sed -i "s|APP_URL=.*|APP_URL=https://dineflowai.duckdns.org|g" /var/www/dineflow/.env && sudo php artisan config:clear
```
