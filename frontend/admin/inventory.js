// DineFlow AI - Admin Inventory Controller (Fixed v2)

let inventoryItems = [];

function getCurrentRestaurantId() {
  return window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || new URLSearchParams(window.location.search).get('restaurantId') || '1';
}

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
    const res = await fetch(`/api/inventory?restaurantId=${getCurrentRestaurantId()}`);
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
    loadAiDemandForecast();
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
          ${qty <= alert ? `<a href="/admin/procurement" title="Order from Supplier" style="padding:4px 8px; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); color:#F59E0B; border-radius:6px; text-decoration:none; display:inline-block; font-size:11px; font-weight:700; margin-right:4px;"><i class="fa-solid fa-truck-ramp-box"></i> Order</a>` : ''}
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
    restaurant_id: getCurrentRestaurantId(),
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

// ══════════════════════════════════════════════════════════════════════════════
// 📈 AI DEMAND PREDICTION & INGREDIENT CONSUMPTION HORIZON
// ══════════════════════════════════════════════════════════════════════════════
async function loadAiDemandForecast() {
  const grid = document.getElementById('aiForecastGrid');
  if (!grid) return;

  try {
    const res = await fetch(`/api/analytics/demand-forecast/${getCurrentRestaurantId()}`);
    const json = await res.json();
    if (json.success && json.data && json.data.items) {
      renderAiForecast(json.data);
    }
  } catch (err) {
    console.warn('AI Forecast fetch error:', err);
  }
}

function renderAiForecast(forecast) {
  const grid = document.getElementById('aiForecastGrid');
  const badge = document.getElementById('surgeBadge');
  if (!grid) return;

  if (badge && forecast.weekend_surge_factor > 1.2) {
    badge.innerHTML = `🔥 Weekend Rush Active (+${Math.round((forecast.weekend_surge_factor - 1) * 100)}% Surge)`;
    badge.style.background = 'rgba(239,68,68,0.15)';
    badge.style.color = '#EF4444';
    badge.style.borderColor = 'rgba(239,68,68,0.3)';
  }

  const items = forecast.items || [];
  if (!items.length) {
    grid.innerHTML = `<div style="color:var(--text-muted); font-size:12px; grid-column:1/-1; text-align:center;">No ingredient data available for forecast analysis.</div>`;
    return;
  }

  grid.innerHTML = items.slice(0, 6).map(item => {
    const isUrgent = item.status === 'restock_urgently';
    return `
      <div style="background:var(--card); border:1px solid ${isUrgent ? 'rgba(239,68,68,0.4)' : 'var(--border)'}; border-radius:12px; padding:14px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:var(--shadow-sm);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
          <div>
            <div style="font-weight:700; font-size:13.5px; color:var(--text-primary);">${escapeHtml(item.name)}</div>
            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Stock: <span style="font-weight:700; color:var(--text-primary);">${item.current_stock} ${item.unit}</span></div>
          </div>
          <span style="background:${isUrgent ? 'rgba(239,68,68,0.15)' : 'rgba(16,185,129,0.15)'}; color:${isUrgent ? '#EF4444' : '#10B981'}; font-size:10px; font-weight:800; padding:2px 6px; border-radius:6px; border:1px solid ${isUrgent ? 'rgba(239,68,68,0.3)' : 'rgba(16,185,129,0.3)'};">
            ${isUrgent ? '⚠️ RESTOCK' : '✅ OPTIMAL'}
          </span>
        </div>

        <div style="background:rgba(255,255,255,0.02); border:1px solid rgba(255,255,255,0.05); border-radius:8px; padding:8px 10px; margin-bottom:10px;">
          <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--text-muted);">
            <span>Predicted 3-Day Need:</span>
            <span style="font-weight:700; color:#818CF8;">~${item.predicted_3day_need} ${item.unit}</span>
          </div>
          <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--text-muted); margin-top:4px;">
            <span>Projected Balance:</span>
            <span style="font-weight:700; color:${item.projected_balance <= 5 ? '#EF4444' : '#10B981'};">${item.projected_balance} ${item.unit}</span>
          </div>
        </div>

        ${isUrgent ? `
          <button onclick="adjustQty('${item.id}', ${item.suggested_reorder})" style="padding:6px; background:linear-gradient(135deg,#EF4444,#DC2626); border:none; border-radius:8px; color:#fff; font-size:11px; font-weight:800; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px;">
            <i class="fa-solid fa-cart-plus"></i> Auto-Reorder +${item.suggested_reorder} ${item.unit}
          </button>
        ` : `
          <div style="text-align:center; font-size:11px; color:#10B981; font-weight:700;">Stock Sufficient for 3 Days</div>
        `}
      </div>
    `;
  }).join('');
}
