import { randomUUID } from 'crypto';

import * as database from '../database.js';
import { randomUUID } from 'crypto';

export function createSyncQueueItem(db, item) {
  const now = new Date().toISOString();
  const localId = randomUUID();
  const stmt = db.prepare(`INSERT INTO sync_queue (
    local_id, entity_type, entity_id, operation, status, attempts, last_attempt_at,
    next_attempt_at, last_error, created_at, updated_at
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`);
  stmt.run(
    localId,
    item.entity_type,
    item.entity_id,
    item.operation,
    item.status || 'pending',
    item.attempts || 0,
    item.last_attempt_at || null,
    item.next_attempt_at || now,
    item.last_error || null,
    now,
    now
  );
  database.save();
  return getSyncQueueItemById(db, localId);
}

export function getSyncQueueItemById(db, localId) {
  const stmt = db.prepare('SELECT * FROM sync_queue WHERE local_id = ?');
  return stmt.get(localId);
}

export function getPendingQueueItems(db, limit = 10) {
  const now = new Date().toISOString();
  const stmt = db.prepare(`SELECT * FROM sync_queue WHERE status IN ('pending', 'failed') AND next_attempt_at <= ? ORDER BY created_at ASC LIMIT ?`);
  return stmt.all(now, limit);
}

export function markQueueItemProcessing(db, localId) {
  const now = new Date().toISOString();
  const stmt = db.prepare(`UPDATE sync_queue SET status = 'syncing', last_attempt_at = ?, attempts = attempts + 1, updated_at = ? WHERE local_id = ?`);
  stmt.run(now, now, localId);
  database.save();
}

export function completeQueueItem(db, localId) {
  const now = new Date().toISOString();
  const stmt = db.prepare(`UPDATE sync_queue SET status = 'completed', updated_at = ? WHERE local_id = ?`);
  stmt.run(now, localId);
  database.save();
}

export function failQueueItem(db, localId, error, nextAttemptDelayMs) {
  const now = new Date().toISOString();
  const nextAttemptAt = new Date(Date.now() + nextAttemptDelayMs).toISOString();
  const stmt = db.prepare(`UPDATE sync_queue SET status = 'failed', last_error = ?, next_attempt_at = ?, updated_at = ? WHERE local_id = ?`);
  stmt.run(error, nextAttemptAt, now, localId);
  database.save();
}

export function getQueueStats(db) {
  const pending = db.prepare(`SELECT COUNT(*) AS count FROM sync_queue WHERE status = 'pending'`).get().count;
  const failed = db.prepare(`SELECT COUNT(*) AS count FROM sync_queue WHERE status = 'failed'`).get().count;
  const completed = db.prepare(`SELECT COUNT(*) AS count FROM sync_queue WHERE status = 'completed'`).get().count;
  return { pending, failed, completed };
}
