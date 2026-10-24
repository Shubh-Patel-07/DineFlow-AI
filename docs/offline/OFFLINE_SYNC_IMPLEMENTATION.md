# DineFlow AI — Hybrid Offline Desktop + Cloud Auto-Sync Implementation

This document describes the technical implementation of DineFlow AI's hybrid offline desktop and automatic cloud synchronization engine.

---

## 1. System Architecture

```
                       ┌──────────────────────────────────────┐
                       │          DINEFLOW CLOUD              │
                       │ - MySQL Database (Authoritative)     │
                       │ - POST /api/sync/orders              │
                       │ - GET /api/sync/orders/since         │
                       └──────────────────┬───────────────────┘
                                          │ HTTPS REST API
                       ┌──────────────────▼───────────────────┐
                       │     WINDOWS ELECTRON POS DESKTOP     │
                       │ - SQLite Local Database (sql.js)     │
                       │ - sync_queue Table                   │
                       │ - Background syncEngine.js Worker    │
                       └──────────────────────────────────────┘
```

---

## 2. Core Implementation Components

### A. Server Sync Gateway (`routes/web.php` & `OfflineSyncQueueService.php`)
- **`POST /api/sync/orders`**: Ingests offline order payloads. Resolves tenant from the authenticated user's session/token. Checks idempotency using client UUID (`ClientSyncID:xxx`). Recalculates prices server-side from authoritative `menu_items` table. Decrements inventory on first creation only. Returns HTTP 200 with cloud order ID.
- **`GET /api/sync/orders/since`**: Accepts a `since` ISO timestamp query parameter to return cloud customer orders placed while the desktop was offline, allowing immediate reconciliation.

### B. Client Sync Engine (`windows-app/sync/syncEngine.js`)
- Listens to network connectivity events and performs lightweight active health checks.
- When online, processes `sync_queue` items in FIFO order with exponential backoff (`2s`, `5s`, `10s`, `20s`, max `120s`).
- Emits real-time visual status updates (🟢 Online, 🟠 Syncing, 🔴 Offline, ⚠️ Sync Error).

### C. Local Order Repository (`windows-app/database/repositories/orderRepository.js`)
- Creates atomic SQLite transactions inserting the local order, order items, and an associated `sync_queue` entry.
- Operates with zero latency during complete internet disconnects.

---

## 3. Conflict Resolution Principles

1. **UUID Idempotency**: Offline orders generate client-side UUIDs. Repeated submissions return the existing order without duplicate records or extra stock deductions.
2. **Authoritative Pricing**: Server recalculates item prices against current restaurant menu records to prevent offline price tampering.
3. **Monotonic Status Progression**: Order statuses advance (`pending` ➔ `preparing` ➔ `ready` ➔ `served` ➔ `completed`) and reject regression attempts.
4. **Payment Immutability**: Paid orders cannot be reverted to unpaid by offline sync payloads.
