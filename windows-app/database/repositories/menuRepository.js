import { randomUUID } from 'crypto';

export function getAllMenuItems(db) {
  const stmt = db.prepare('SELECT * FROM menu_items ORDER BY name ASC');
  return stmt.all();
}

export function getMenuItemById(db, localId) {
  const stmt = db.prepare('SELECT * FROM menu_items WHERE local_id = ?');
  return stmt.get(localId);
}

export function createMenuItem(db, item) {
  const now = new Date().toISOString();
  const localId = item.local_id || randomUUID();
  const stmt = db.prepare(`INSERT INTO menu_items (
    local_id, remote_id, restaurant_id, name, description, price, category, image, is_available, is_veg, created_at, updated_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`);
  stmt.run(
    localId,
    item.remote_id || null,
    item.restaurant_id,
    item.name,
    item.description || null,
    item.price || 0,
    item.category || 'Mains',
    item.image || null,
    item.is_available ? 1 : 0,
    item.is_veg ? 1 : 0,
    now,
    now
  );
  return getMenuItemById(db, localId);
}
