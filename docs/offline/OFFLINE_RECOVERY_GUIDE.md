# DineFlow AI — Offline Desktop Recovery & Troubleshooting Guide

This guide outlines standard operating procedures for cashier staff and system administrators when managing desktop POS operations during network outages or unexpected hardware crashes.

---

## 1. Operating the Desktop POS During Internet Outages

1. **Automatic Detection**:
   - The topbar status indicator will automatically switch from 🟢 **Online** to 🔴 **Offline**.
   - No action is needed from the cashier.
2. **Order Placement**:
   - Continue taking orders, applying notes, and seating tables normally.
   - Orders are instantly saved to the local SQLite database and queued for synchronization.
3. **Cash Payments & KOT**:
   - Cash payments can be settled locally.
   - Local thermal receipt and kitchen KOT printing continue without interruption.
4. **Internet Restoration**:
   - As soon as the network reconnects, the badge switches to 🟠 **Syncing** and automatically uploads all pending tickets.
   - Once the queue is clear, the badge returns to 🟢 **Online**.

---

## 2. Emergency Recovery from Desktop Hardware Crash / Sudden Power Loss

If a cashier terminal crashes or loses power while orders are pending in the sync queue:

1. **Reboot the Computer**: Turn on the terminal and launch the DineFlow AI Desktop Application.
2. **Automated SQLite WAL Recovery**: SQLite automatically replays the Write-Ahead Log (`.sqlite-wal`), restoring uncorrupted order records.
3. **Automatic Queue Resumption**: The `syncEngine` detects all `pending` and `failed` queue items and transmits them to the cloud gateway.
4. **Database File Location**:
   - `%APPDATA%/dineflow-app/database/dineflow-local.sqlite`
   - In the rare event of disk failure, backup copies can be restored directly to this path.
