# DineFlow AI / DineFlow — Super Admin Implementation Architecture

**Status**: 🟢 **COMPLETE (Production Ready)**  
**Target URL**: `https://dineflowai.duckdns.org/superadmin`  
**Authentication**: Multi-Tenant SaaS Super Admin Role (`role = 'superadmin'`)

---

## 1. Overview & Architecture

The Super Admin Control Center provides SaaS operators with centralized, platform-wide visibility and administrative authority over all onboarded restaurants, subscription plans, usage quotas, and administrative audit trails.

```
                      ┌──────────────────────────────────────────────┐
                      │          SUPER ADMIN CONTROL CENTER          │
                      │         GET /superadmin (Blade UI)           │
                      │         GET /api/superadmin/*                │
                      └──────────────────────┬───────────────────────┘
                                             │
                       ┌─────────────────────┴───────────────────────┐
                       │           SuperAdminController              │
                       │ - Platform Statistics (GMV, Active, Orders) │
                       │ - Restaurant CRUD & Status Lifecycle        │
                       │ - SaaS Tier Plan & Custom Quota Engine      │
                       │ - Immutable Audit Logging                   │
                       └─────────────────────┬───────────────────────┘
                                             │
             ┌───────────────────────────────┼───────────────────────────────┐
             ▼                               ▼                               ▼
    ┌─────────────────┐             ┌─────────────────┐             ┌─────────────────┐
    │  Restaurant #1  │             │  Restaurant #2  │             │  Restaurant #3  │
    │  (Free Tier)    │             │   (Pro Tier)    │             │(Enterprise Tier)│
    └─────────────────┘             └─────────────────┘             └─────────────────┘
```

---

## 2. API Endpoints Specification

All endpoints are protected by `auth` and `role:superadmin` middleware. Standard tenant users (admin, manager, cashier, waiter) receive `403 Forbidden`.

| HTTP Verb | Endpoint | Description |
|---|---|---|
| `GET` | `/superadmin` | Super Admin Dashboard UI (Blade view) |
| `GET` | `/api/superadmin/dashboard` | Aggregated cross-tenant platform KPIs (Total restaurants, GMV, Orders) |
| `GET` | `/api/superadmin/stats` | Detailed platform revenue, monthly GMV, and plan breakdown |
| `GET` | `/api/superadmin/restaurants` | Paginated, searchable list of all restaurants with plan and owner data |
| `GET` | `/api/superadmin/restaurants/{id}` | Detailed single restaurant view with live usage percentages |
| `POST` | `/api/superadmin/restaurants` | Atomically provisions a new Restaurant, Owner account, and quota limits |
| `PUT` | `/api/superadmin/restaurants/{id}` | Updates restaurant legal, contact, and tax settings |
| `POST` | `/api/superadmin/restaurants/{id}/activate` | Activates a suspended restaurant and logs the event |
| `POST` | `/api/superadmin/restaurants/{id}/suspend` | Suspends a restaurant, immediately blocking tenant operations |
| `POST` | `/api/superadmin/restaurants/{id}/plan` | Changes SaaS plan tier (Free / Pro / Enterprise) and resets quotas |
| `POST` | `/api/superadmin/restaurants/{id}/limits` | Sets custom quotas (`tables`, `staff`, `menu_items`, `monthly_orders`) |
| `GET` | `/api/superadmin/orders` | Real-time global order stream across all restaurants |
| `GET` | `/api/superadmin/audit-logs` | Filterable audit trail of all Super Admin administrative actions |

---

## 3. Plan & Quota Management

The Super Admin panel reuses `App\Services\PlanLimitService.php` to define and enforce tiered plan limits:

| Plan Tier | Max Tables | Max Staff Accounts | Max Menu Dishes | Monthly Orders |
|---|---|---|---|---|
| **Free Tier** | 10 | 5 | 100 | 500 / month |
| **Pro Tier** | 100 | 50 | 1,000 | 10,000 / month |
| **Enterprise Tier** | 500+ | 250+ | 5,000+ | Unlimited |

---

## 4. Audit Log System

Every administrative action is recorded in the `audit_logs` database table with:
- `user_id`: ID of the executing Super Admin
- `action`: `restaurant_created`, `restaurant_updated`, `restaurant_activated`, `restaurant_suspended`, `plan_changed`, `limits_changed`
- `restaurant_id`: ID of the affected tenant
- `metadata`: JSON payload of previous and new state changes
- `ip_address`: Remote client IP address
- `user_agent`: Browser / client signature
