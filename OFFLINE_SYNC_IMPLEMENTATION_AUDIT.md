# DineFlow AI — Phase 2 Hybrid Offline Desktop + Cloud Auto-Sync Implementation Audit

**Audit Date**: August 27, 2026  
**Auditor**: Lead Systems & Architecture Engineer  
**Scope**: SQLite Local Schema, Sync Queue, Idempotency Engine, Desktop POS, Cloud REST Gateway, Multi-Tenant Boundary

---

## 1. Executive Summary

This audit assesses the current state of DineFlow AI's offline desktop and cloud auto-sync architecture. The core foundation exists in `windows-app/` and `app/Services/OfflineSyncQueueService.php`. This document identifies complete, partial, and missing components prior to implementing Phase 2.

---

## 2. Component-by-Component Classification

| Component | Current State | Classification | Action Required |
|---|---|---|---|
| **Local SQLite Engine** (`windows-app/database/database.js`) | Embedded `sql.js` SQLite with file persistence in `%APPDATA%` | 🟢 **ALREADY COMPLETE** | Fully functional. Auto-creates local directory and migrates schema. |
| **Local Schema & Tables** (`windows-app/database/migrations.js`) | Tables for `orders`, `order_items`, `sync_queue`, `menu_items`, `tables`, `customers` | 🟢 **ALREADY COMPLETE** | Local schema includes `client_order_id`, `sync_status`, `sync_queue`. |
| **Local Order Repository** (`windows-app/database/repositories/orderRepository.js`) | Atomic SQLite order creation & queue insertion | 🟢 **ALREADY COMPLETE** | Generates client UUIDs, writes items, and inserts `sync_queue` item. |
| **Sync Queue Repository** (`windows-app/sync/syncQueueRepository.js`) | CRUD on `sync_queue`, status updates, backoff | 🟢 **ALREADY COMPLETE** | Clean up duplicate `randomUUID` import. |
| **Cloud Sync Gateway Route** (`routes/web.php`) | Missing `POST /api/sync/orders` and `GET /api/sync/sync-orders/since` | 🔴 **MISSING** | Register routes under `auth` and `tenant` middleware. |
| **Cloud Idempotency Service** (`app/Services/OfflineSyncQueueService.php`) | Handles basic order insertion with `ClientSyncID` check | 🟡 **PARTIALLY COMPLETE** | Upgrade to enforce authenticated tenant context, authoritative menu price lookup, delta inventory deduction, and monotonic status validation. |
| **Cloud-to-Desktop Reconciliation** | Missing cursor-based delta sync endpoint | 🔴 **MISSING** | Implement `/api/sync/orders/since` to reconcile cloud customer orders with the local desktop POS. |
| **Network Monitor & UI Badge** | Basic online check in `networkMonitor.js` | 🟡 **PARTIALLY COMPLETE** | Enhance with 4-state visual indicator (🟢 Online, 🟠 Syncing, 🔴 Offline, ⚠️ Sync Error). |
| **Cross-Tenant Security** | `TenantMiddleware` blocks cross-tenant access | 🟢 **ALREADY COMPLETE** | Ensure sync endpoint resolves tenant strictly from authenticated session/token. |

---

## 3. Risk Assessment & Mitigation

1. **Price Tampering in Offline Payloads (RISK: HIGH)**:
   - *Risk*: A rogue client modifying local SQLite could submit an offline order with price ₹0.
   - *Mitigation*: The cloud sync service must recalculate all line items against the authoritative `menu_items` database table for that restaurant.
2. **Duplicate Inventory Deductions on Sync Retries (RISK: HIGH)**:
   - *Risk*: An idempotent retry of the same offline UUID could deduct stock multiple times.
   - *Mitigation*: Only deduct inventory upon initial insertion. Idempotent hits return the existing order without triggering inventory operations.
3. **Monotonic Status Regression (RISK: MEDIUM)**:
   - *Risk*: An offline order marked `pending` might overwrite a cloud order that kitchen staff already marked `ready`.
   - *Mitigation*: Enforce state machine rules: `pending` < `preparing` < `ready` < `served` < `completed`.
