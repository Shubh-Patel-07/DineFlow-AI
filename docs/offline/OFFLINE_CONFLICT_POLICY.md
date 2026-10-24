# DineFlow AI — Offline Synchronization & Conflict Resolution Policy

This policy document establishes authoritative business rules for resolving data conflicts when offline desktop records synchronize with the cloud database.

---

## 1. Entity Conflict Resolution Matrix

| Entity | Conflict Strategy | Authoritative Source | Resolution Rule |
|---|---|---|---|
| **Orders** | Client UUID Idempotency | Cloud (after sync) | Local order identified by `client_order_id`. If record exists on server, skip duplicate. If missing, insert. |
| **Order Status** | Monotonic State Forwarding | Server Hierarchy | Status only moves forward: `pending` ➔ `preparing` ➔ `ready` ➔ `served` ➔ `completed`. An offline `pending` cannot overwrite a server `ready` or `completed`. |
| **Payments** | Immutable Append / Never Overwrite | Server Guard | Once an order is marked `paid` in cloud or locally, it cannot be reverted to `pending`. If payment method differs, preserve the first validated timestamp. |
| **Menu Items & Pricing** | Server-Authoritative | Cloud Database | Desktop never updates dish price/category while offline. Cloud menu catalog takes precedence during sync. |
| **Table Layout** | Merge by Table Number | Cloud Database | New tables created locally are merged by `table_number`. Local table status syncs based on active unpaid orders. |
| **Inventory Stock** | Delta-Event Deduction | Transaction Delta | Deduct stock based on `OrderItem.quantity` consumed per order event rather than overwriting absolute stock count. |
| **Customers & Loyalty** | Incremental Upsert | Phone Number Key | Increment visits (`total_visits + 1`) and points atomically (`increment('loyalty_points')`) based on the synced order value. |
| **Coupons** | Server-Validated Rules | Cloud Rules | Coupon usage limits are checked during synchronization. Expired/exhausted coupons will flag a sync warning rather than failing the entire order. |
| **Staff & Auth** | Server-Authoritative | Cloud Database | Staff credentials, roles, and PIN changes must be executed online. Local offline POS uses cached credential hashes. |
| **Expenses** | Append-Only by UUID | Client UUID | Offline expenses sync as new ledger records deduplicated by `expense_uuid`. |

---

## 2. Detailed Handling of High-Risk Conflict Scenarios

### Scenario A: Offline Cash Payment vs Online Customer UPI Attempt
- **Problem**: Customer scanned QR and attempts UPI payment while Cashier simultaneously accepted cash offline during a network drop.
- **Resolution**: The first payment received and settled marks `payment_status = 'paid'`. If duplicate payment is attempted, the second transaction is rejected with `"Order #123 has already been settled"`.

### Scenario B: Inventory Depletion during Network Outage
- **Problem**: Table 1 orders the last 2 portions of *Butter Paneer* on POS (offline) while Table 2 orders 1 portion on Mobile (cloud).
- **Resolution**: Inventory is decremented atomically via transaction events. When the offline order syncs, the server decrements stock into negative alert state (`quantity = -1`) and triggers an urgent KDS notification: `"Stock Depleted: Butter Paneer is now OUT OF STOCK"`.

### Scenario C: Unsynced App Crash / Power Cut
- **Problem**: POS laptop battery dies while 3 orders are in `LOCAL_PENDING_SYNC` state.
- **Resolution**: SQLite uses write-ahead logging (WAL) and `PRAGMA synchronous = FULL;`. Upon desktop restart, the background `syncEngine` detects pending queue items and resumes synchronization immediately upon internet restoration.
