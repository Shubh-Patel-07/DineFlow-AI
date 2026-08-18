import { randomUUID } from 'crypto';

export function getAllTables(db) {
  const stmt = db.prepare('SELECT * FROM tables ORDER BY table_number ASC');
  return stmt.all();
}

export function getTableById(db, localId) {
  const stmt = db.prepare('SELECT * FROM tables WHERE local_id = ?');
  return stmt.get(localId);
}

export function createTable(db, table) {
  const now = new Date().toISOString();
  const localId = table.local_id || randomUUID();
  const stmt = db.prepare(`INSERT INTO tables (
    local_id, remote_id, restaurant_id, table_number, status, qr_code_url, created_at, updated_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`);
  stmt.run(
    localId,
    table.remote_id || null,
    table.restaurant_id,
    table.table_number,
    table.status || 'active',
    table.qr_code_url || null,
    now,
    now
  );
  return getTableById(db, localId);
}
