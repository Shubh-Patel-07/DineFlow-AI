# DineFlow AI — Production Infrastructure & Cost Architecture

This document breaks down the production infrastructure requirements, domain setup for `dineflowai.duckdns.org`, and categorizes services into **Free Tiers** and **Paid Production Options**.

---

## 1. Domain & DNS Architecture (`dineflowai.duckdns.org`)

We utilize the existing domain **`dineflowai.duckdns.org`** without purchasing additional domains.

```
dineflowai.duckdns.org (Root Domain)
  ├── dineflowai.duckdns.org       --> Pointed to VPS IP (Main Multi-Tenant SaaS Web App & Admin Dashboard)
  ├── api.dineflowai.duckdns.org       --> Pointed to VPS IP (REST API & Sync Gateway)
  └── dineflowai.duckdns.org/pos       --> Pointed to VPS IP (Dedicated POS Terminal Web Portal)
```

### DNS & CDN Configuration (Cloudflare)
- **Cloudflare DNS**: Free Plan provides DDoS protection, global CDN caching, and automated Universal SSL.
- **SSL/TLS Mode**: Full (Strict) between Cloudflare and the origin server Let's Encrypt SSL certificate.

---

## 2. Infrastructure Cost Breakdown (Free vs Paid Tiers)

| Component | Free / Pilot Option | Estimated Cost (Pilot) | Paid Production Tier (Scale > 50 Restaurants) | Estimated Cost (Scale) |
|---|---|---|---|---|
| **Domain** | Existing `dineflowai.duckdns.org` | **₹0 / mo** | Renew `dineflowai.duckdns.org` annually | ~₹850 / year |
| **DNS & SSL** | Cloudflare Free + Let's Encrypt | **₹0 / mo** | Cloudflare Free Tier | **₹0 / mo** |
| **VPS Server** | Oracle Cloud Free Tier (4 OCPU, 24GB RAM ARM) OR Hetzner €4.5/mo VPS | **₹0 to ₹400 / mo** | Hetzner Cloud / DigitalOcean (4 vCPU, 8GB RAM) | ~₹1,800 to ₹3,200 / mo |
| **Database** | Embedded Cloud MySQL 8 (on VPS) | **₹0 / mo** | Managed MySQL (AWS RDS / DigitalOcean) | ~₹2,500 / mo |
| **Transactional Email** | SendGrid / Mailgun Free Tier (100–300 emails/day) | **₹0 / mo** | Amazon SES (₹8 per 10,000 emails) | ~₹100 / mo |
| **Offline Desktop Engine**| Electron + SQLite (`sql.js`) Open-Source | **₹0 / mo** | Electron Open-Source | **₹0 / mo** |
| **Table QR Code Generation**| Local SVG/Canvas QR Generator (`api.qrserver.com`) | **₹0 / mo** | Built-in PHP QR Code Generator | **₹0 / mo** |
| **Total Estimated Cost** | **Free Pilot Stage** | **₹0 – ₹400 / mo** | **Scale Stage (50+ Restaurants)** | **~₹4,500 / mo** |

---

## 3. Server Resource Requirements for 1–2 Pilot Restaurants

- **vCPU**: 2 Cores
- **RAM**: 2 GB – 4 GB
- **Storage**: 20 GB SSD (with daily backup rotation)
- **Operating System**: Ubuntu 22.04 LTS or 24.04 LTS
- **Web Server**: Nginx 1.24+
- **PHP Version**: PHP 8.2-FPM (with `opcache` enabled)
- **Database Engine**: MySQL 8.0 Community Edition
