// DineFlow AI - Admin Menu Manager JS Controller

let allMenuItems = [];
let activeCategory = 'all';

document.addEventListener('DOMContentLoaded', () => {
  Auth.checkAuth();
  loadMenuItems();
});

async function loadMenuItems() {
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'flex';

  try {
    const res = await fetch('/api/menu?restaurantId=1');
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      allMenuItems = data.data;
    } else if (Array.isArray(data)) {
      allMenuItems = data;
    } else {
      allMenuItems = [];
    }
  } catch (err) {
    console.error('Error fetching menu items:', err);
    allMenuItems = [];
  } finally {
    if (loader) loader.style.display = 'none';
    renderMenuItems();
  }
}

function renderMenuItems() {
  const container = document.getElementById('menuGridContainer');
  if (!container) return;

  const searchQuery = document.getElementById('menuSearchInput')?.value.trim().toLowerCase() || '';

  const filtered = allMenuItems.filter(item => {
    const matchesCat = activeCategory === 'all' || item.category?.toLowerCase() === activeCategory.toLowerCase();
    const matchesSearch = !searchQuery || `${item.name} ${item.category} ${item.description}`.toLowerCase().includes(searchQuery);
    return matchesCat && matchesSearch;
  });

  if (!filtered.length) {
    container.innerHTML = `
      <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:#94A3B8;">
        <i class="fa-solid fa-utensils" style="font-size:40px; margin-bottom:12px; opacity:0.5;"></i>
        <h3 style="font-family:'Outfit',sans-serif; color:#F8FAFC; margin:0 0 6px;">No Menu Items Found</h3>
        <p style="font-size:13px; margin:0;">Try adjusting your search or category filter, or add a new menu item.</p>
      </div>`;
    return;
  }

  container.innerHTML = filtered.map(item => `
    <div class="item-card">
      <div class="item-img-wrap">
        <img src="${escapeHtml(item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400')}" alt="${escapeHtml(item.name)}" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400'">
        <span class="item-badge ${item.is_veg ? 'veg' : 'nonveg'}">${item.is_veg ? 'VEG' : 'NON-VEG'}</span>
      </div>
      <div class="item-card-body">
        <h3 class="item-card-title">${escapeHtml(item.name)}</h3>
        <p class="item-card-desc">${escapeHtml(item.description || 'Fresh gourmet dish prepared with finest ingredients.')}</p>
        <div class="item-card-footer">
          <span class="item-price">₹${parseFloat(item.price || 0).toFixed(2)}</span>
          <div class="item-actions">
            <label class="toggle-switch" title="Toggle Available/Sold Out">
              <input type="checkbox" ${item.is_available ? 'checked' : ''} onchange="toggleItemAvailability('${item.id}', this.checked)">
              <span class="slider"></span>
            </label>
            <button class="action-btn" onclick="openEditItemModal('${item.id}')" title="Edit Item"><i class="fa-solid fa-pen"></i></button>
            <button class="action-btn delete" onclick="deleteMenuItem('${item.id}')" title="Delete Item"><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>
      </div>
    </div>
  `).join('');
}

function setCategoryFilter(cat) {
  activeCategory = cat;
  document.querySelectorAll('.cat-pill').forEach(btn => {
    btn.classList.toggle('active', btn.textContent.toLowerCase() === cat.toLowerCase() || (cat === 'all' && btn.textContent.includes('All')));
  });
  renderMenuItems();
}

function filterMenuItems() {
  renderMenuItems();
}

function openAddItemModal() {
  document.getElementById('modalTitle').textContent = 'Add New Menu Item';
  document.getElementById('itemId').value = '';
  document.getElementById('itemName').value = '';
  document.getElementById('itemCategory').value = 'Mains';
  document.getElementById('itemPrice').value = '';
  document.getElementById('itemImage').value = '';
  document.getElementById('itemDesc').value = '';
  document.getElementById('itemIsVeg').checked = true;
  document.getElementById('itemIsAvailable').checked = true;
  document.getElementById('itemModal').style.display = 'flex';
}

function openEditItemModal(id) {
  const item = allMenuItems.find(i => String(i.id) === String(id));
  if (!item) return;

  document.getElementById('modalTitle').textContent = 'Edit Menu Item';
  document.getElementById('itemId').value = item.id;
  document.getElementById('itemName').value = item.name || '';
  document.getElementById('itemCategory').value = item.category || 'Mains';
  document.getElementById('itemPrice').value = item.price || '';
  document.getElementById('itemImage').value = item.image || '';
  document.getElementById('itemDesc').value = item.description || '';
  document.getElementById('itemIsVeg').checked = item.is_veg !== false;
  document.getElementById('itemIsAvailable').checked = item.is_available !== false;
  document.getElementById('itemModal').style.display = 'flex';
}

function closeItemModal() {
  document.getElementById('itemModal').style.display = 'none';
}

async function handleSaveItem(event) {
  event.preventDefault();
  const id = document.getElementById('itemId').value;
  const payload = {
    restaurant_id: 1,
    name: document.getElementById('itemName').value.trim(),
    category: document.getElementById('itemCategory').value,
    price: parseFloat(document.getElementById('itemPrice').value),
    image: document.getElementById('itemImage').value.trim(),
    description: document.getElementById('itemDesc').value.trim(),
    is_veg: document.getElementById('itemIsVeg').checked,
    is_available: document.getElementById('itemIsAvailable').checked
  };

  const isEdit = Boolean(id);
  const url = isEdit ? `/api/menu/${id}` : '/api/menu';
  const method = isEdit ? 'PUT' : 'POST';

  try {
    const res = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (res.ok || data.success) {
      Toast.success(isEdit ? 'Menu item updated successfully!' : 'New menu item added!');
      closeItemModal();
      loadMenuItems();
    } else {
      Toast.error(data.message || 'Failed to save menu item.');
    }
  } catch (err) {
    Toast.error('Network error saving menu item.');
  }
}

async function toggleItemAvailability(id, isAvailable) {
  try {
    const res = await fetch(`/api/menu/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ is_available: isAvailable })
    });
    if (res.ok) {
      Toast.success(isAvailable ? 'Item marked Available!' : 'Item marked Out of Stock!');
      const item = allMenuItems.find(i => String(i.id) === String(id));
      if (item) item.is_available = isAvailable;
    }
  } catch (_) {}
}

async function deleteMenuItem(id) {
  if (!confirm('Are you sure you want to delete this menu item?')) return;
  try {
    const res = await fetch(`/api/menu/${id}`, { method: 'DELETE' });
    if (res.ok) {
      Toast.success('Menu item deleted.');
      loadMenuItems();
    } else {
      Toast.error('Failed to delete menu item.');
    }
  } catch (_) {
    Toast.error('Error deleting menu item.');
  }
}

function escapeHtml(str) {
  if (typeof str !== 'string') return '';
  return str.replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[match]);
}
