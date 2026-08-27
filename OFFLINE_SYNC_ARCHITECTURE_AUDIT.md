# DineFlow AI — Online Cloud + Offline Desktop + Auto Sync Architecture Audit

**Date**: August 27, 2026  
**Author**: Lead Systems & Architecture Engineer  
**System**: DineFlow AI Restaurant Management OS  
**Status**: Architecture Audit & Specification Document (Pre-Implementation Phase)

---

## 1. Architectural Overview & Context

DineFlow AI is designed to support a **Hybrid Cloud-First + Edge-Resilient Architecture** for modern restaurant operations. 

```
                               ┌───────────────────────────────────────────────┐
                               │           DINEFLOW CLOUD (PRIMARY)            │
                               │  - Authoritative Cloud MySQL Database         │
                               │  - Laravel REST & Sync Gateway API            │
                               │  - Multi-Tenant Isolation & Global Analytics   │
                               └──────────────────────┬────────────────────────┘
                                                      │ HTTPS / JSON API
                     ┌────────────────────────────────┼────────────────────────────────┐
                     │                                │                                │
       ┌─────────────▼─────────────┐    ┌─────────────▼─────────────┐    ┌─────────────▼─────────────┐
       │     CUSTOMER SMARTPHONE   │    │    CASHIER DESKTOP / POS  │    │     KITCHEN DISPLAY (KDS) │
       │  - Scans Table QR Code    │    │  - Online: Cloud Direct   │    │  - Real-time KOT display  │
       │  - Submits Cloud Order    │    │  - Offline: Local SQLite  │    │  - Acoustic sound alerts  │
       │  - Online 4G/5G/WiFi      │    │  - Auto Background Sync   │    │  - LAN / Cloud Connected  │
       └───────────────────────────┘    └───────────────────────────┘    └───────────────────────────┘
```

---

## 2. Full Codebase Inventory for Offline & Sync Capabilities

| Component | Location | Current State | Audit Finding |
|---|---|---|---|
| **Local Desktop DB Engine** | `windows-app/database/database.js` | SQLite (`sql.js`) | Functional embedded local database storing tables, menu, orders, customers, and sync queue. |
| **Local DB Migrations** | `windows-app/database/migrations.js` | 1 Migration (`version: 1`) | Defines local tables: `restaurants`, `users`, `tables`, `menu_items`, `customers`, `inventories`, `coupons`, `orders`, `order_items`, `sync_queue`. |
| **Sync Queue Engine** | `windows-app/sync/syncEngine.js` | Electron Background Worker | Exponential backoff algorithm (`2s`, `5s`, `10s`, `20s`, max `120s`). Reads pending items from `sync_queue`. |
| **Network Monitor** | `windows-app/sync/networkMonitor.js` | Network Poller | Listens to `window.addEventListener('online')` and `offline` events. |
| **Server Idempotency Service**| `app/Services/OfflineSyncQueueService.php`| Laravel Backend Service | Implements idempotency check on `client_order_id`, deduplicates repeated sync attempts, updates table status, and writes `OrderItem` records. |
| **Sync Route Definition** | `routes/web.php` | Not Yet Registered | Need to register `Route::post('/api/sync/orders', ...)` pointing to `OfflineSyncQueueService` behind auth/tenant guard. |
| **Web Browser POS** | `resources/views/pos/index.blade.php` | Cloud Online First | Operates directly against `/api/orders` when loaded in a standard web browser. |

---

## 3. Authoritative Data Model & Globally Unique Identifiers (UUID)

To guarantee that an offline order created on a desktop terminal is never duplicated when synchronized with the cloud database, the system uses **Client-Generated UUIDs (`order_uuid` / `client_order_id`)**.

```
Client Order Generation
  ↳ Generates UUID: e.g. "ord_f81d4fae-7dec-11d0-a765-00a0c91e6bf6"
    ↳ Stored in Local SQLite `orders` table (status: `LOCAL_PENDING_SYNC`)
      ↳ Enqueued into `sync_queue` table
        ↳ POST to `/api/sync/orders`
          ↳ Cloud checks: `WHERE client_order_id = 'ord_f81d4fae...'`
            ├─ If Exists: Returns HTTP 200 with existing order ID (Idempotent Hit)
            └─ If New: Inserts into MySQL, calculates taxes, updates table, returns HTTP 201
```

---

## 4. Local Database Schema & `sync_queue` Specification

The local SQLite database maintains the following authoritative structure for offline reliability:

```sql
CREATE TABLE IF NOT EXISTS sync_queue (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    local_id TEXT UNIQUE,
    restaurant_id TEXT NOT NULL,
    entity_type TEXT NOT NULL,       -- 'order', 'payment', 'customer', 'expense'
    entity_id TEXT NOT NULL,         -- UUID of local record
    operation TEXT NOT NULL,         -- 'create', 'update', 'delete'
    payload TEXT,                    -- JSON serialized data snapshot
    status TEXT NOT NULL DEFAULT 'pending', -- 'pending', 'syncing', 'synced', 'failed'
    attempts INTEGER NOT NULL DEFAULT 0,
    last_attempt_at TEXT,
    next_attempt_at TEXT,
    last_error TEXT,
    synced_at TEXT,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_sync_queue_status ON sync_queue (status);
CREATE INDEX IF NOT EXISTS idx_sync_queue_next_attempt ON sync_queue (next_attempt_at);
```

---

## 5. Network Detection & Health Check Strategy

Relying solely on `navigator.onLine` can cause false positives (e.g. connected to a local router with no internet uplink).

### Dual-Layer Health Verification
1. **Event Trigger**: Browser `online` / `offline` events.
2. **Active Ping (Heartbeat)**: Every 30 seconds when idle, or immediately upon `online` event, the client performs a lightweight HEAD request to `/api/health`.

### UI Visual Indicators (Non-Intrusive Banner)
- 🟢 **Online Mode**: Cloud connected, latency < 200ms.
- 🟠 **Syncing Mode**: Network recovered, processing `sync_queue` records.
- 🔴 **Offline Mode**: Local SQLite active, zero disruption to cashier order taking.
- ⚠️ **Sync Conflict**: Network error or business rule exception requiring manual review.

---

## 6. Pilot Restaurant Operational Recommendation

```
┌────────────────────────────────────────────────────────────────────────┐
│                        PILOT EXECUTION PHASING                         │
├────────────────────────────────────────────────────────────────────────┤
│ PHASE 1: FREE CLOUD PILOT (Current Ready State)                        │
│   - 1–2 Restaurants operate on high-availability Cloud API.            │
│   - Customer QR, Cashier POS, and Kitchen KDS communicate in real-time │
│     via Cloud MySQL.                                                   │
│   - 100% verified, 170/170 tests passing, 0 launch blockers.           │
├────────────────────────────────────────────────────────────────────────┤
│ PHASE 2: HYBRID DESKTOP OFFLINE SYNC (Controlled Staged Rollout)       │
│   - Deploy Electron desktop wrapper with embedded SQLite engine.       │
│   - Enable local order queuing for internet outage resilience.         │
│   - Enable auto-reconciliation when internet connectivity restores.    │
└────────────────────────────────────────────────────────────────────────┘
```
