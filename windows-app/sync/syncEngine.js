import { isOnline } from './networkMonitor.js';
import * as queueRepo from './syncQueueRepository.js';
import * as orderRepo from '../database/repositories/orderRepository.js';
import fetch from 'node-fetch';

const BASE_API_URL = process.env.DINEFLOW_API_URL || 'http://127.0.0.1:8000/api';
let syncInProgress = false;
let syncStatusListener = null;

export function setSyncStatusListener(callback) {
  syncStatusListener = callback;
}

function notifyStatus(status, details = {}) {
  if (typeof syncStatusListener === 'function') {
    syncStatusListener({ status, ...details });
  }
}

function calculateBackoffMs(attempts) {
  const base = 2000;
  const multipliers = [1, 2.5, 5, 10, 20];
  return Math.min(base * (multipliers[attempts] || 20), 120000);
}

export async function processQueue(db, authToken = '') {
  if (syncInProgress || !isOnline()) {
    notifyStatus(isOnline() ? 'idle' : 'offline');
    return;
  }
  syncInProgress = true;
  notifyStatus('syncing');

  try {
    const items = queueRepo.getPendingQueueItems(db, 10);
    if (items.length === 0) {
      notifyStatus('online', { pendingCount: 0 });
      return;
    }

    for (const item of items) {
      if (!isOnline()) {
        notifyStatus('offline');
        break;
      }
      await processQueueItem(db, item, authToken);
    }

    const remaining = queueRepo.getPendingQueueItems(db, 1);
    notifyStatus(remaining.length > 0 ? 'syncing' : 'online', { pendingCount: remaining.length });
  } catch (err) {
    notifyStatus('error', { error: err.message });
  } finally {
    syncInProgress = false;
  }
}

async function processQueueItem(db, item, authToken = '') {
  try {
    queueRepo.markQueueItemProcessing(db, item.local_id);

    if (item.entity_type === 'order' && item.operation === 'create') {
      const localOrder = orderRepo.getLocalOrderById(db, item.entity_id);
      if (!localOrder) {
        queueRepo.completeQueueItem(db, item.local_id);
        return;
      }

      const payload = {
        client_order_id: localOrder.client_order_id,
        restaurant_id: localOrder.restaurant_id,
        table_id: localOrder.table_id,
        special_instructions: localOrder.special_instructions || '',
        items: (localOrder.items || []).map(i => ({
          menu_item_id: i.menu_item_id || null,
          name: i.name,
          quantity: i.quantity,
          price: i.price
        }))
      };

      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      };
      if (authToken) {
        headers['Authorization'] = `Bearer ${authToken}`;
      }

      const response = await fetch(`${BASE_API_URL}/sync/orders`, {
        method: 'POST',
        headers,
        body: JSON.stringify(payload)
      });

      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`Sync failed ${response.status}: ${errorText}`);
      }

      const data = await response.json();
      if (!data.success) {
        throw new Error(data.message || 'Unknown sync failure');
      }

      const serverOrderId = data.order_id || (data.data && data.data.id);
      orderRepo.updateLocalOrder(db, item.entity_id, {
        remote_id: serverOrderId,
        sync_status: 'synced',
        synced_at: new Date().toISOString()
      });
      queueRepo.completeQueueItem(db, item.local_id);
    }
  } catch (error) {
    const attempts = (item.attempts || 0) + 1;
    const delay = calculateBackoffMs(attempts);
    queueRepo.failQueueItem(db, item.local_id, String(error.message || error), delay);
  }
}

/**
 * Reconcile orders placed on Cloud while desktop was offline
 */
export async function reconcileCloudOrders(db, authToken = '', sinceTimestamp = null) {
  if (!isOnline()) return { success: false, message: 'Offline' };

  try {
    const headers = {
      'Accept': 'application/json',
    };
    if (authToken) {
      headers['Authorization'] = `Bearer ${authToken}`;
    }

    const url = new URL(`${BASE_API_URL}/sync/orders/since`);
    if (sinceTimestamp) {
      url.searchParams.set('since', sinceTimestamp);
    }

    const response = await fetch(url.toString(), { method: 'GET', headers });
    if (!response.ok) throw new Error(`Fetch failed: ${response.status}`);

    const result = await response.json();
    return result;
  } catch (err) {
    return { success: false, error: err.message };
  }
}
