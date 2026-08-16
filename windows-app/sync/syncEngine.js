import { isOnline } from './networkMonitor.js';
import * as queueRepo from './syncQueueRepository.js';
import * as orderRepo from '../database/repositories/orderRepository.js';
import fetch from 'node-fetch';

const SYNC_API_URL = 'http://127.0.0.1:8000/api/sync/orders';
let syncInProgress = false;

function calculateBackoffMs(attempts) {
  const base = 2000;
  const multipliers = [1, 2.5, 5, 10, 20];
  return Math.min(base * (multipliers[attempts] || 20), 120000);
}

export async function processQueue(db) {
  if (syncInProgress || !isOnline()) {
    return;
  }
  syncInProgress = true;

  try {
    const items = queueRepo.getPendingQueueItems(db, 5);
    for (const item of items) {
      if (!isOnline()) break;
      await processQueueItem(db, item);
    }
  } finally {
    syncInProgress = false;
  }
}

async function processQueueItem(db, item) {
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
        items: localOrder.items.map(item => ({
          menu_item_id: item.menu_item_id,
          name: item.name,
          quantity: item.quantity,
          price: item.price
        }))
      };

      const response = await fetch(SYNC_API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
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

      const serverOrderId = data.data.id;
      orderRepo.updateLocalOrder(db, item.entity_id, {
        remote_id: serverOrderId,
        sync_status: 'synced'
      });
      queueRepo.completeQueueItem(db, item.local_id);
    }
  } catch (error) {
    const attempts = item.attempts + 1;
    const delay = calculateBackoffMs(attempts);
    queueRepo.failQueueItem(db, item.local_id, String(error.message || error), delay);
  }
}
