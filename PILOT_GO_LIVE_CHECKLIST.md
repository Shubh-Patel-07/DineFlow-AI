# DineFlow AI — Pilot Go-Live Checklist

Use this checklist to verify complete operational readiness before taking live customer orders at the pilot restaurant.

---

## Infrastructure & Environment
- [ ] Production Server Online (Ubuntu VPS with Nginx & PHP 8.2+)
- [ ] Custom Domain Configured & DNS A-Record Propagated
- [ ] Let's Encrypt SSL Active (`https://your-domain.com`)
- [ ] `APP_ENV=production` & `APP_DEBUG=false` in `.env`
- [ ] Production Database Migrations Applied (`php artisan migrate --force`)
- [ ] Daily Automated Backup Cron Active (`/usr/local/bin/dineflow_backup.sh`)
- [ ] Database Restore Tested Successfully

## Restaurant Configuration & Setup
- [ ] Restaurant Tenant Registered & Provisioned
- [ ] 8-Step Onboarding Completed
- [ ] Legal Business Profile & GSTIN Configured
- [ ] Logo Uploaded
- [ ] Dining Tables Configured with Capacity
- [ ] QR Code Table Standees Printed & Placed on Tables
- [ ] Menu Categories & Items Created (Prices, Veg/Non-Veg Badges)
- [ ] Initial Promotional Coupon Created & Activated (`LAUNCH20`)
- [ ] Staff Accounts & Numeric PINs Created (Manager, Cashier, Chef, Waiter)

## Hardware & Operational Stations
- [ ] Cashier Laptop / Tablet Running POS Terminal (`/pos`)
- [ ] Kitchen Tablet / Monitor Running KDS (`/kitchen`) with Audio Alerts
- [ ] Thermal Receipt Printer (58mm/80mm) Connected & Test Print Successful
- [ ] UPI QR / Soundbox Configured & Payment Alert Tested
- [ ] Waiter Mobile Devices Tested for Order Service

## Dry-Run Workflow Verification
- [ ] Test Mobile QR Scan ➔ Digital Menu Loaded
- [ ] Test Customer Order Placed with Cooking Notes & Coupon
- [ ] Order Immediately Appears on POS & KDS
- [ ] Kitchen Transitions Order: Pending ➔ Preparing ➔ Ready
- [ ] Order Settled with UPI/Cash & Thermal Receipt Printed
- [ ] End-of-Day Z-Report Generated Accurately
- [ ] Cross-Tenant Isolation Verified (Zero Access to Other Restaurant Data)
