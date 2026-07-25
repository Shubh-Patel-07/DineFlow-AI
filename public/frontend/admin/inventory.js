// DineFlow AI - Admin Inventory Controller (Fixed v2)

let inventoryItems = [];

function initInventory() {
  if (typeof Auth !== 'undefined' && Auth.checkAuth) Auth.checkAuth();
  loadInventory();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initInventory);
} else {
  initInventory();
}

async function loadInventory() {
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'flex';

  try {
    const res = await fetch('/api/inventory?restaurantId=1');
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      inventoryItems = data.data;
    } else if (Array.isArray(data)) {
      inventoryItems = data;
    } else {
      inventoryItems = [];
    }
  } catch (err) {
    console.error('Error fetching inventory:', err);
    inventoryItems = [];
  } finally {
    if (loader) loader.style.display = 'none';
    renderInventory();
  }
}

function renderInventory() {
  const tbody = document.getElementById('inventoryTableBody');
  if (!tbody) return;

  if (!inventoryItems.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:40px; color:#94A3B8;">
      <i class="fa-solid fa-box-open" style="font-size:32px; opacity:.3; display:block; margin-bottom:12px;"></i>
      No inventory items yet. Click "+ Add Item" to add your first item.
    </td></tr>`;
    return;
  }

  tbody.innerHTML = inventoryItems.map(item => {
    // Support both old schema (name/threshold) and new schema (item_name/low_stock_alert)
    const itemName  = item.item_name || item.name || 'Unknown';
    const category  = item.category  || 'General';
    const qty       = parseFloat(item.quantity || 0);
    const alert     = parseFloat(item.low_stock_alert || item.threshold || 10);
    const unit      = item.unit || 'units';
    const cost      = parseFloat(item.cost_per_unit || 0);

    let tag = `<span class="status-tag ok"><i class="fa-solid fa-check-circle"></i> In Stock</span>`;
    if (qty <= 0) {
      tag = `<span class="status-tag critical"><i class="fa-solid fa-xmark-circle"></i> Out of Stock</span>`;
    } else if (qty <= alert) {
      tag = `<span class="status-tag low"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock</span>`;
    }

    return `
      <tr>
        <td style="font-weight:700; color:#F8FAFC;">${escapeHtml(itemName)}</td>
        <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:rgba(99,102,241,0.15); color:#A5B4FC;">${escapeHtml(category)}</span></td>
        <td style="font-size:16px; font-weight:800; color:#6366F1;">${qty} <span style="font-size:11px; color:#64748B;">${unit}</span></td>
        <td style="color:#94A3B8;">${alert} ${unit}</td>
        <td style="color:#10B981; font-weight:600;">${cost > 0 ? '₹' + cost.toFixed(2) : '—'}</td>
        <td>${tag}</td>
        <td>
          <button onclick="adjustQty('${item.id}', 10)" title="Add 10" style="padding:4px 10px; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#10B981; border-radius:6px; font-weight:700; cursor:pointer; margin-right:4px;">+10</button>
          <button onclick="adjustQty('${item.id}', -5)" title="Use 5" style="padding:4px 10px; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#EF4444; border-radius:6px; font-weight:700; cursor:pointer; margin-right:4px;">-5</button>
          <button onclick="deleteInventoryItem('${item.id}')" title="Delete" style="padding:4px 8px; background:transparent; border:1px solid rgba(239,68,68,0.2); color:#EF4444; border-radius:6px; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
        </td>
      </tr>`;
  }).join('');
}

function openAddStockModal() {
  const modal = document.getElementById('stockModal');
  if (modal) modal.style.display = 'flex';
}
function closeStockModal() {
  const modal = document.getElementById('stockModal');
  if (modal) modal.style.display = 'none';
}

async function handleSaveStock(e) {
  e.preventDefault();
  const payload = {
    restaurant_id: 1,
    item_name:      (document.getElementById('stockName')?.value || '').trim(),
    category:       (document.getElementById('stockCategory')?.value || 'General'),
    quantity:       parseFloat(document.getElementById('stockQty')?.value || 0),
    unit:           (document.getElementById('stockUnit')?.value || 'units').trim(),
    low_stock_alert: parseFloat(document.getElementById('stockThreshold')?.value || 10),
    cost_per_unit:   parseFloat(document.getElementById('stockCost')?.value || 0),
  };

  if (!payload.item_name) { Toast.error('Item name is required!'); return; }

  try {
    const res = await fetch('/api/inventory', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
      body: JSON.stringify(payload)
    });
    if (res.ok) {
      Toast.success('Inventory item added!');
      closeStockModal();
      loadInventory();
      // Reset form
      ['stockName','stockQty','stockCost'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
    } else {
      const err = await res.json();
      Toast.error(err.message || 'Failed to add item.');
    }
  } catch (_) {
    Toast.error('Network error adding item.');
  }
}

async function adjustQty(id, delta) {
  const item = inventoryItems.find(i => String(i.id) === String(id));
  if (!item) return;
  const currentQty = parseFloat(item.quantity || 0);
  const newQty = Math.max(0, currentQty + delta);
  try {
    const res = await fetch(`/api/inventory/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
      body: JSON.stringify({ quantity: newQty })
    });
    if (res.ok) {
      item.quantity = newQty;
      renderInventory();
      Toast.success(`Stock updated: ${item.item_name || item.name} → ${newQty} ${item.unit}`);
    } else {
      Toast.error('Failed to update stock.');
    }
  } catch (_) {
    Toast.error('Network error updating stock.');
  }
}

// Keep old function name for backward compat
async function updateQty(id, delta) { return adjustQty(id, delta); }

async function deleteInventoryItem(id) {
  if (!confirm('Delete this inventory item?')) return;
  try {
    const res = await fetch(`/api/inventory/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
    });
    if (res.ok) {
      inventoryItems = inventoryItems.filter(i => String(i.id) !== String(id));
      renderInventory();
      Toast.success('Item deleted.');
    } else {
      Toast.error('Failed to delete item.');
    }
  } catch (_) {
    Toast.error('Network error.');
  }
}

function escapeHtml(str) {
  if (typeof str !== 'string') return '';
  return str.replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[match]);
}
