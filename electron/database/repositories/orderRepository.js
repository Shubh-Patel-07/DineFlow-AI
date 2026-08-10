import { randomUUID } from 'crypto';
import * as database from '../database.js';

export function getLocalOrders(db) {
  const stmt = db.prepare('SELECT * FROM orders ORDER BY created_at DESC');
  return stmt.all();
}

export function getLocalOrderById(db, localId) {
  const stmt = db.prepare('SELECT * FROM orders WHERE local_id = ?');
  const order = stmt.get(localId);
  if (!order) return null;
  const items = db.prepare('SELECT * FROM order_items WHERE order_local_id = ? ORDER BY created_at ASC').all(localId);
  return { ...order, items };
}

export function createLocalOrder(db, order) {
  const now = new Date().toISOString();
  const localId = order.local_id || randomUUID();
  const clientOrderId = order.client_order_id || randomUUID();

  db.exec('BEGIN TRANSACTION');
  try {
    const orderStmt = db.prepare(`INSERT INTO orders (
      local_id, remote_id, client_order_id, restaurant_id, table_id, total_amount, status, payment_status, payment_method, special_instructions, sync_status, sync_attempts, last_sync_error, synced_at, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`);
    orderStmt.run(
      localId,
      order.remote_id || null,
      clientOrderId,
      order.restaurant_id,
      order.table_id,
      order.total_amount || 0,
      order.status || 'pending',
      order.payment_status || 'pending',
      order.payment_method || 'cash',
      order.special_instructions || null,
      order.sync_status || 'pending',
      order.sync_attempts || 0,
      order.last_sync_error || null,
      order.synced_at || null,
      now,
      now
    );

    if (!Array.isArray(order.items) || order.items.length === 0) {
      throw new Error('Order must include at least one item');
    }

    for (const item of order.items) {
      addLocalOrderItem(db, localId, item);
    }

    const queueStmt = db.prepare(`INSERT INTO sync_queue (
      local_id, entity_type, entity_id, operation, status, attempts, next_attempt_at, created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`);
    queueStmt.run(
      randomUUID(),
      'order',
      localId,
      'create',
      'pending',
      0,
      now,
      now,
      now
    );

    db.exec('COMMIT');
    database.save();
    return getLocalOrderById(db, localId);
  } catch (error) {
    db.exec('ROLLBACK');
    throw error;
  }
}

export function addLocalOrderItem(db, orderLocalId, item) {
  const now = new Date().toISOString();
  const localId = item.local_id || randomUUID();
  const stmt = db.prepare(`INSERT INTO order_items (
    local_id, remote_id, order_local_id, menu_item_local_id, menu_item_id, name, quantity, price, created_at, updated_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`);
  stmt.run(
    localId,
    item.remote_id || null,
    orderLocalId,
    item.menu_item_local_id || null,
    item.menu_item_id || null,
    item.name,
    item.quantity || 1,
    item.price || 0,
    now,
    now
  );
  return db.prepare('SELECT * FROM order_items WHERE local_id = ?').get(localId);
}

export function updateLocalOrder(db, localId, updates) {
  const now = new Date().toISOString();
  const existing = getLocalOrderById(db, localId);
  if (!existing) return null;

  const data = {
    restaurant_id: updates.restaurant_id ?? existing.restaurant_id,
    table_id: updates.table_id ?? existing.table_id,
    total_amount: updates.total_amount ?? existing.total_amount,
    status: updates.status ?? existing.status,
    payment_status: updates.payment_status ?? existing.payment_status,
    payment_method: updates.payment_method ?? existing.payment_method,
    special_instructions: updates.special_instructions ?? existing.special_instructions,
    sync_status: updates.sync_status ?? existing.sync_status,
    sync_attempts: updates.sync_attempts ?? existing.sync_attempts ?? existing.sync_attempts,
    last_sync_error: updates.last_sync_error ?? existing.last_sync_error,
    synced_at: updates.synced_at ?? existing.synced_at,
    remote_id: updates.remote_id ?? existing.remote_id,
    updated_at: now,
  };

  const stmt = db.prepare(`UPDATE orders SET
    restaurant_id = ?,
    table_id = ?,
    total_amount = ?,
    status = ?,
    payment_status = ?,
    payment_method = ?,
    special_instructions = ?,
    sync_status = ?,
    sync_attempts = ?,
    last_sync_error = ?,
    synced_at = ?,
    remote_id = ?,
    updated_at = ?
    WHERE local_id = ?`);
  stmt.run(
    data.restaurant_id,
    data.table_id,
    data.total_amount,
    data.status,
    data.payment_status,
    data.payment_method,
    data.special_instructions,
    data.sync_status,
    data.sync_attempts,
    data.last_sync_error,
    data.synced_at,
    data.remote_id,
    data.updated_at,
    localId
  );

  database.save();
  return getLocalOrderById(db, localId);
}
