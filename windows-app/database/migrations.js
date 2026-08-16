export const migrations = [
  {
    version: 1,
    name: 'initial_local_schema',
    up(db) {
      db.prepare(`CREATE TABLE IF NOT EXISTS local_meta (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL
      );`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS restaurants (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        name TEXT NOT NULL,
        address TEXT,
        phone TEXT,
        logo TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS users (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        username TEXT NOT NULL UNIQUE,
        password_hash TEXT,
        numeric_pin TEXT,
        role TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS tables (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        restaurant_id TEXT NOT NULL,
        table_number TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'active',
        qr_code_url TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_tables_restaurant_id ON tables (restaurant_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS menu_items (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        restaurant_id TEXT NOT NULL,
        name TEXT NOT NULL,
        description TEXT,
        price REAL NOT NULL DEFAULT 0,
        category TEXT,
        image TEXT,
        is_available INTEGER NOT NULL DEFAULT 1,
        is_veg INTEGER NOT NULL DEFAULT 1,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_menu_items_restaurant_id ON menu_items (restaurant_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS customers (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        restaurant_id TEXT NOT NULL,
        name TEXT NOT NULL,
        phone TEXT,
        email TEXT,
        loyalty_points INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_customers_restaurant_id ON customers (restaurant_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS inventories (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        restaurant_id TEXT NOT NULL,
        item_name TEXT NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 0,
        unit TEXT NOT NULL DEFAULT 'units',
        threshold INTEGER NOT NULL DEFAULT 10,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_inventories_restaurant_id ON inventories (restaurant_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS coupons (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        restaurant_id TEXT NOT NULL,
        code TEXT NOT NULL,
        discount_type TEXT,
        discount_value REAL NOT NULL DEFAULT 0,
        min_order_amount REAL NOT NULL DEFAULT 0,
        max_discount REAL NOT NULL DEFAULT 0,
        is_active INTEGER NOT NULL DEFAULT 1,
        expires_at TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_coupons_restaurant_id ON coupons (restaurant_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS feedback (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        restaurant_id TEXT NOT NULL,
        customer_name TEXT NOT NULL DEFAULT 'Anonymous',
        rating INTEGER NOT NULL,
        comment TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_feedback_restaurant_id ON feedback (restaurant_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS waiter_calls (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        restaurant_id TEXT NOT NULL,
        table_id TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'pending',
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE,
        FOREIGN KEY (table_id) REFERENCES tables(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_waiter_calls_restaurant_id ON waiter_calls (restaurant_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS orders (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        client_order_id TEXT UNIQUE,
        restaurant_id TEXT NOT NULL,
        table_id TEXT NOT NULL,
        total_amount REAL NOT NULL DEFAULT 0,
        status TEXT NOT NULL DEFAULT 'pending',
        payment_status TEXT NOT NULL DEFAULT 'pending',
        payment_method TEXT NOT NULL DEFAULT 'cash',
        special_instructions TEXT,
        sync_status TEXT NOT NULL DEFAULT 'pending',
        sync_attempts INTEGER NOT NULL DEFAULT 0,
        last_sync_error TEXT,
        synced_at TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(local_id) ON DELETE CASCADE,
        FOREIGN KEY (table_id) REFERENCES tables(local_id) ON DELETE CASCADE
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_orders_restaurant_id ON orders (restaurant_id);`).run();
      db.prepare(`CREATE INDEX IF NOT EXISTS idx_orders_table_id ON orders (table_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS order_items (
        local_id TEXT PRIMARY KEY,
        remote_id TEXT,
        order_local_id TEXT NOT NULL,
        menu_item_local_id TEXT,
        menu_item_id TEXT,
        name TEXT NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        price REAL NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY (order_local_id) REFERENCES orders(local_id) ON DELETE CASCADE,
        FOREIGN KEY (menu_item_local_id) REFERENCES menu_items(local_id) ON DELETE SET NULL
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_order_items_order_local_id ON order_items (order_local_id);`).run();
      db.prepare(`CREATE INDEX IF NOT EXISTS idx_order_items_menu_item_local_id ON order_items (menu_item_local_id);`).run();

      db.prepare(`CREATE TABLE IF NOT EXISTS sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        local_id TEXT UNIQUE,
        entity_type TEXT NOT NULL,
        entity_id TEXT NOT NULL,
        operation TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'pending',
        attempts INTEGER NOT NULL DEFAULT 0,
        last_attempt_at TEXT,
        next_attempt_at TEXT,
        last_error TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL
      );`).run();

      db.prepare(`CREATE INDEX IF NOT EXISTS idx_sync_queue_status ON sync_queue (status);`).run();
      db.prepare(`CREATE INDEX IF NOT EXISTS idx_sync_queue_next_attempt_at ON sync_queue (next_attempt_at);`).run();
    }
  }
];
