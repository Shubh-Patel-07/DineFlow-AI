import { randomUUID } from 'crypto';

export function getAllCustomers(db) {
  const stmt = db.prepare('SELECT * FROM customers ORDER BY name ASC');
  return stmt.all();
}

export function getCustomerById(db, localId) {
  const stmt = db.prepare('SELECT * FROM customers WHERE local_id = ?');
  return stmt.get(localId);
}

export function createCustomer(db, customer) {
  const now = new Date().toISOString();
  const localId = customer.local_id || randomUUID();
  const stmt = db.prepare(`INSERT INTO customers (
    local_id, remote_id, restaurant_id, name, phone, email, loyalty_points, created_at, updated_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`);
  stmt.run(
    localId,
    customer.remote_id || null,
    customer.restaurant_id,
    customer.name,
    customer.phone || null,
    customer.email || null,
    customer.loyalty_points || 0,
    now,
    now
  );
  return getCustomerById(db, localId);
}
