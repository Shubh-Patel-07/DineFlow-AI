# 🏗️ DineFlow AI - Enterprise System Architecture & Technical Specification

## 1. System Overview & Executive Summary

**DineFlow AI** is a next-generation, omnichannel restaurant operating system and point-of-sale platform designed to streamline digital QR ordering, kitchen order routing (KDS), smart upselling, split billing, customer loyalty, and real-time inventory management.

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                 DINEFLOW AI ECOSYSTEM                                  │
└────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
         ┌─────────────────────────────────┼─────────────────────────────────┐
         ▼                                 ▼                                 ▼
┌──────────────────┐             ┌──────────────────┐             ┌──────────────────┐
│  Customer Mobile │             │   POS Cashier    │             │   Kitchen KDS    │
│  QR Ordering UI  │             │     Terminal     │             │  Live Monitor    │
└──────────────────┘             └──────────────────┘             └──────────────────┘
         │                                 │                                 │
         └─────────────────────────────────┼─────────────────────────────────┘
                                           │ (HTTP REST / JSON / Server-Sent Events)
                                           ▼
                       ┌───────────────────────────────────────┐
                       │       Laravel REST API Core           │
                       │ (Multi-Tenant, RBAC, Bill Calculator) │
                       └───────────────────────────────────────┘
                                           │
         ┌─────────────────────────────────┼─────────────────────────────────┐
         ▼                                 ▼                                 ▼
┌──────────────────┐             ┌──────────────────┐             ┌──────────────────┐
│ SQLite / MySQL   │             │ Web Speech Synth │             │ Offline Electron │
│ Primary Database │             │ Soundbox Audio   │             │   Sync Engine    │
└──────────────────┘             └──────────────────┘             └──────────────────┘
```

---

## 2. Architectural Layers

### 2.1 Presentation Layer (Frontend)
- **Customer Mobile QR Client (`frontend/customer/`)**:
  - Responsive single-page web app built with modern vanilla JavaScript (ES6+), optimized for mobile browser runtimes without heavy framework overhead.
  - Multi-language engine supporting **English, Hindi, and Gujarati**.
  - Client-side real-time filters: Category pills, dietary preferences (🟢 Veg / 🔴 Non-Veg), smart search, and sort criteria.
  - Built-in Voice-to-Order Assistant powered by the Web Speech API.
  - Live order tracking with progress steps (Received -> Preparing -> Ready -> Served).

- **POS Billing Terminal (`resources/views/pos/`)**:
  - Cashier desktop interface for fast table-side billing, multi-floor table selection, split billing, customer phone loyalty lookups, and auto-print hub.
  - Multi-printer routing: Concurrent Kitchen KOT (Kitchen Order Ticket), Bar KOT, and Customer Thermal Receipt (58mm/80mm ESC/POS).

- **Kitchen Display System (KDS - `resources/views/pos/kitchen.blade.php`)**:
  - Real-time order queue for chef preparation.
  - Color-coded SLA timers: Normal (Green), Warning (Amber), Urgent (Red > 15 mins).
  - Audio chime synthesizer for hands-free audible notifications on incoming tickets.

- **Admin Management Portal (`frontend/admin/`)**:
  - Menu engineering matrix, raw material stock deductions, customer loyalty tier management, staff PIN authentication, discount coupon campaigns, and financial analytics.

---

## 3. Data & State Management

### 3.1 Multi-Tenancy & Tenant Isolation
All database queries enforce strict multi-tenant scope via `restaurant_id`:
```php
$restaurantId = $request->input('restaurant_id', '1');
$orders = Order::where('restaurant_id', $restaurantId)->with('items')->get();
```

### 3.2 Offline-First Sync Architecture (Windows Desktop App)
- The Electron Windows Desktop App embeds a local SQLite database (`dineflow-local.sqlite`) via `sql.js`.
- When connectivity drops, orders are written locally to `local_orders` and queued in `sync_queue`.
- The `SyncEngine` polls network state via `NetworkMonitor` and pushes queued records with exponential backoff retry algorithms when connection is re-established.

---

## 4. Security & Compliance
1. **CSRF Protection**: All state-modifying requests (`POST`, `PUT`, `DELETE`) require a valid `X-CSRF-TOKEN` or session authentication.
2. **Role-Based Access Control (RBAC)**: Enforced via `RoleMiddleware` (`admin`, `manager`, `cashier`, `kitchen`, `waiter`).
3. **Session Fixation Prevention**: Session regeneration triggered on credential and PIN logins.
4. **Input Sanitization**: HTML escaping to mitigate XSS vulnerabilities across order notes and customer reviews.
