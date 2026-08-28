# DineFlow AI / Tendorabuy — Super Admin SaaS Subscription Implementation

**Status**: 🟢 **COMPLETE (Production Ready)**  
**Version**: 2.0.0 (Multi-Tenant SaaS Control)  
**Author**: Lead SaaS Engineer  

---

## 1. Architectural Overview

The Super Admin SaaS Platform Control System introduces an authoritative, server-driven subscription lifecycle engine for DineFlow AI / Tendorabuy. It enables SaaS administrators to manage tenant onboarding, multi-tiered subscription plans (`free`, `pro`, `enterprise`, `custom`), automated grace periods, expiration policies, and offline manual payment recordings.

```
                               ┌─────────────────────────────────────────┐
                               │       SUPER ADMIN CONTROL CENTER        │
                               │           GET /superadmin               │
                               └────────────────────┬────────────────────┘
                                                    │
                               ┌────────────────────┴────────────────────┐
                               │          SuperAdminController           │
                               │ - Global KPIs & Cross-Tenant Analytics  │
                               │ - Tenant Onboarding & Owner Provisioning│
                               │ - Subscription Lifecycle & Overrides    │
                               │ - Custom Quota Management               │
                               │ - Manual Payment Recording              │
                               └────────────────────┬────────────────────┘
                                                    │
                 ┌──────────────────────────────────┼──────────────────────────────────┐
                 ▼                                  ▼                                  ▼
      ┌──────────────────────┐           ┌──────────────────────┐           ┌──────────────────────┐
      │   TenantMiddleware   │           │ SubscriptionHistory  │           │   ProcessExpiryJob   │
      │ Real-time Auth Guard │           │ Immutable Audit Log  │           │ Scheduled Lifecycle  │
      └──────────────────────┘           └──────────────────────┘           └──────────────────────┘
```

---

## 2. Subscription Lifecycle States

```
           ┌──────────┐
           │  Trial   ├──────────────┐
           └────┬─────┘              │
                │                    ▼
                │             ┌──────────────┐
                ├────────────►│    Active    │
                │             └──────┬───────┘
                │                    │ (Expires in <= 3 days)
                │                    ▼
                │             ┌──────────────┐
                ├────────────►│   Expiring   │
                │             └──────┬───────┘
                │                    │ (ExpiresAt reached)
                │                    ▼
                │             ┌──────────────┐
                └────────────►│ Grace Period │ (Configurable 1–60 days)
                              └──────┬───────┘
                                     │ (Grace period exceeded)
                                     ▼
                              ┌──────────────┐
                              │   Expired    │◄──────── (Suspended by Admin)
                              └──────────────┘
```

| Lifecycle State | Operational Access | Tenant Capabilities |
|---|---|---|
| `active` | ✅ Full Access | All POS, KDS, Customer QR, Menu, Tables, and Reports allowed |
| `trial` | ✅ Full Access | Same as active until `trial_ends_at` |
| `expiring` | ✅ Full Access | Operational with warning banner indicating upcoming renewal |
| `grace_period` | ✅ Temporary Full Access | Operational during grace period window (default 7 days) |
| `expired` | 🚫 Blocked | Restricted operational access (cannot place new orders/bills). Historical data preserved. |
| `suspended` | 🚫 Blocked | Administrative suspension with reason. Zero data deleted. |
| `cancelled` | 🚫 Blocked | Tenant churn / offboarding. |

---

## 3. Quota Management Matrix

The system enforces plan quotas via `App\Services\PlanLimitService.php`:

| Plan Tier | Max Tables | Max Staff | Max Menu Items | Monthly Orders Quota |
|---|---|---|---|---|
| **Free** | 10 | 5 | 100 | 500 orders/mo |
| **Pro** | 50 | 25 | 500 | 5,000 orders/mo |
| **Enterprise** | 200 | 100 | 2,000 | 25,000 orders/mo |
| **Custom** | *Configurable* | *Configurable* | *Configurable* | *Configurable* |

---

## 4. API Endpoints Reference

All endpoints reside under `Route::middleware(['auth', 'role:superadmin'])`:

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | `/superadmin` | Super Admin Web Control Center |
| `GET` | `/api/superadmin/dashboard` | Platform metrics & business GMV |
| `GET` | `/api/superadmin/restaurants` | Paginated, searchable restaurant list |
| `GET` | `/api/superadmin/restaurants/{id}` | Detailed tenant inspection drawer with live usage % |
| `POST` | `/api/superadmin/restaurants` | Provisions restaurant, owner user, and quotas |
| `PUT` | `/api/superadmin/restaurants/{id}` | Updates address, phone, GSTIN, tax settings |
| `POST` | `/api/superadmin/restaurants/{id}/activate` | Restores active status & clears suspension reason |
| `POST` | `/api/superadmin/restaurants/{id}/suspend` | Suspends restaurant with mandatory reason |
| `POST` | `/api/superadmin/restaurants/{id}/plan` | Changes plan tier & resets default limits |
| `POST` | `/api/superadmin/restaurants/{id}/limits` | Applies custom quota overrides |
| `POST` | `/api/superadmin/restaurants/{id}/subscription`| Full subscription lifecycle override (dates, grace, status) |
| `POST` | `/api/superadmin/restaurants/{id}/payment` | Records offline payment (Cash, Bank, UPI) and extends expiry |
| `GET` | `/api/superadmin/orders` | Live stream of recent orders across all restaurants |
| `GET` | `/api/superadmin/audit-logs` | Platform audit log stream with metadata |

---

## 5. Scheduled Background Command

To automatically process subscription expirations in production, register the command in your crontab:

```bash
* * * * * cd /path-to-dineflow && php artisan schedule:run >> /dev/null 2>&1
```

Or run manually:
```bash
php artisan subscriptions:process-expiry
```
