import { randomUUID } from 'crypto';

export function getInventory(db) {
  const stmt = db.prepare('SELECT * FROM inventories ORDER BY item_name ASC');
  return stmt.all();
}

export function getInventoryItemById(db, localId) {
  const stmt = db.prepare('SELECT * FROM inventories WHERE local_id = ?');
  return stmt.get(localId);
}

export function createInventoryItem(db, item) {
  const now = new Date().toISOString();
  const localId = item.local_id || randomUUID();
  const stmt = db.prepare(`INSERT INTO inventories (
    local_id, remote_id, restaurant_id, item_name, quantity, unit, threshold, created_at, updated_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`);
  stmt.run(
    localId,
    item.remote_id || null,
    item.restaurant_id,
    item.item_name,
    item.quantity || 0,
    item.unit || 'units',
    item.threshold || 10,
    now,
    now
  );
  return getInventoryItemById(db, localId);
}
