// DineFlow AI - Admin Inventory Controller

let inventoryItems = [];

document.addEventListener('DOMContentLoaded', () => {
  Auth.checkAuth();
  loadInventory();
});

async function loadInventory() {
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'flex';

  try {
    const res = await fetch('/api/inventory');
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      inventoryItems = data.data;
    } else if (Array.isArray(data)) {
      inventoryItems = data;
    } else {
      // Sample fallback items if empty
      inventoryItems = [
        { id: 1, name: 'Mozzarella Cheese', quantity: 45, unit: 'kg', threshold: 10 },
        { id: 2, name: 'Pizza Sauce', quantity: 20, unit: 'liters', threshold: 5 },
        { id: 3, name: 'Burger Buns', quantity: 8, unit: 'pcs', threshold: 15 },
        { id: 4, name: 'French Fries', quantity: 30, unit: 'kg', threshold: 10 },
        { id: 5, name: 'Paneer Cubes', quantity: 12, unit: 'kg', threshold: 5 }
      ];
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
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:#94A3B8;">No inventory items recorded yet.</td></tr>`;
    return;
  }

  tbody.innerHTML = inventoryItems.map(item => {
    const qty = parseInt(item.quantity || 0);
    const threshold = parseInt(item.threshold || 10);
    let tag = `<span class="status-tag ok">In Stock</span>`;
    if (qty <= 0) {
      tag = `<span class="status-tag critical">Out of Stock</span>`;
    } else if (qty <= threshold) {
      tag = `<span class="status-tag low">Low Stock Warning</span>`;
    }

    return `
      <tr>
        <td style="font-weight:700; color:#F8FAFC;">${escapeHtml(item.name)}</td>
        <td style="font-size:15px; font-weight:800; color:#6366F1;">${qty}</td>
        <td>${escapeHtml(item.unit || 'units')}</td>
        <td style="color:#94A3B8;">${threshold}</td>
        <td>${tag}</td>
        <td>
          <button onclick="updateQty('${item.id}', 10)" style="padding:4px 10px; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#10B981; border-radius:6px; font-weight:700; cursor:pointer; margin-right:4px;">+10</button>
          <button onclick="updateQty('${item.id}', -5)" style="padding:4px 10px; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#EF4444; border-radius:6px; font-weight:700; cursor:pointer;">-5</button>
        </td>
      </tr>`;
  }).join('');
}

function openAddStockModal() {
  document.getElementById('stockModal').style.display = 'flex';
}
function closeStockModal() {
  document.getElementById('stockModal').style.display = 'none';
}

async function handleSaveStock(e) {
  e.preventDefault();
  const payload = {
    restaurant_id: 1,
    name: document.getElementById('stockName').value.trim(),
    quantity: parseInt(document.getElementById('stockQty').value),
    unit: document.getElementById('stockUnit').value.trim(),
    threshold: parseInt(document.getElementById('stockThreshold').value)
  };

  try {
    const res = await fetch('/api/inventory', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    if (res.ok) {
      Toast.success('Stock ingredient added!');
      closeStockModal();
      loadInventory();
    } else {
      Toast.error('Failed to add stock item.');
    }
  } catch (_) {
    Toast.error('Error adding stock item.');
  }
}

async function updateQty(id, delta) {
  const item = inventoryItems.find(i => String(i.id) === String(id));
  if (!item) return;
  const newQty = Math.max(0, parseInt(item.quantity || 0) + delta);
  try {
    await fetch(`/api/inventory/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ quantity: newQty })
    });
    item.quantity = newQty;
    renderInventory();
    Toast.success(`Stock updated to ${newQty} ${item.unit}`);
  } catch (_) {}
}

function escapeHtml(str) {
  if (typeof str !== 'string') return '';
  return str.replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[match]);
}
