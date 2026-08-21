// DineFlow AI - Admin Menu Manager JS Controller

let allMenuItems = [];
let activeCategory = 'all';

function initMenuManager() {
  if (typeof Auth !== 'undefined' && Auth.checkAuth) Auth.checkAuth();
  loadMenuItems();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initMenuManager);
} else {
  initMenuManager();
}


async function loadMenuItems() {
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'flex';

  const currentRestaurantId = window.AUTH_USER_RESTAURANT_ID || (typeof Auth !== 'undefined' && Auth.getUser()?.restaurant_id) || new URLSearchParams(window.location.search).get('restaurantId') || '1';

  try {
    const res = await fetch(`/api/menu?restaurantId=${currentRestaurantId}`);
    const data = await res.json();
    if (data.success && Array.isArray(data.data) && data.data.length > 0) {
      allMenuItems = data.data;
    } else if (Array.isArray(data) && data.length > 0) {
      allMenuItems = data;
    } else {
      allMenuItems = getStaticMenuItems();
    }
  } catch (err) {
    console.error('Error fetching menu items, using fallback:', err);
    allMenuItems = getStaticMenuItems();
  } finally {
    if (loader) loader.style.display = 'none';
    renderMenuItems();
  }
}

function getStaticMenuItems() {
  return [
    { id: 1, name: 'Paneer Butter Masala', category: 'Mains', price: 240.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?w=500', description: 'Rich cottage cheese cubes cooked in butter tomato gravy.' },
    { id: 2, name: 'Chicken Tikka Masala', category: 'Mains', price: 320.00, is_veg: false, is_available: true, image: 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=500', description: 'Tender chicken pieces simmered in spiced tikka gravy.' },
    { id: 3, name: 'Garlic Naan', category: 'Breads', price: 60.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?w=500', description: 'Clay oven baked flatbread brushed with butter and minced garlic.' },
    { id: 4, name: 'Veg Spring Rolls', category: 'Starters', price: 180.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=500', description: 'Crispy fried rolls filled with seasoned vegetables.' },
    { id: 5, name: 'Chicken Biryani', category: 'Rice', price: 380.00, is_veg: false, is_available: true, image: 'https://images.unsplash.com/photo-1633945274405-b6c8069047b0?w=500', description: 'Fragrant basmati rice layered with spiced marinated chicken.' },
    { id: 6, name: 'Mango Lassi', category: 'Drinks', price: 120.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500', description: 'Creamy yogurt drink blended with fresh sweet Alphonso mango pulp.' },
    { id: 7, name: 'Gulab Jamun', category: 'Desserts', price: 80.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=500', description: 'Soft milk dumplings soaked in cardamom sugar syrup.' },
    { id: 8, name: 'Dal Tadka', category: 'Mains', price: 180.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=500', description: 'Yellow lentils tempered with ghee, cumin seeds, garlic and chili.' },
    { id: 9, name: 'Margherita Pizza', category: 'Pizza', price: 299.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=500', description: 'Classic Italian pizza topped with mozzarella and fresh basil.' },
    { id: 10, name: 'Cheese Burger', category: 'Burger', price: 199.00, is_veg: true, is_available: true, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500', description: 'Juicy patty filled with melted cheese, lettuce and signature sauce.' }
  ];
}


function updateStatsHeader() {
  const total = allMenuItems.length;
  const veg = allMenuItems.filter(i => i.is_veg).length;
  const nonVeg = total - veg;
  const unavailable = allMenuItems.filter(i => !i.is_available).length;

  const totalEl = document.getElementById('statTotalItems');
  const vegEl = document.getElementById('statVegItems');
  const nonVegEl = document.getElementById('statNonVegItems');
  const outEl = document.getElementById('statOutItems');

  if (totalEl) totalEl.textContent = total;
  if (vegEl) vegEl.textContent = veg;
  if (nonVegEl) nonVegEl.textContent = nonVeg;
  if (outEl) outEl.textContent = unavailable;
}

function renderMenuItems() {
  updateStatsHeader();
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
      <div style="grid-column:1/-1; text-align:center; padding:60px 20px; background:var(--card); border-radius:20px; border:1px dashed var(--border); color:var(--text-muted);">
        <div style="width:64px; height:64px; margin:0 auto 16px; border-radius:18px; background:rgba(99,102,241,0.1); color:var(--primary); display:flex; align-items:center; justify-content:center; font-size:28px;">
          <i class="fa-solid fa-utensils"></i>
        </div>
        <h3 style="font-family:'Outfit',sans-serif; font-size:18px; font-weight:700; color:var(--text-primary); margin:0 0 6px;">No Menu Items Found</h3>
        <p style="font-size:13px; margin:0; color:var(--text-muted);">No dishes match your selected filter. Click "Add New Menu Item" to create one.</p>
      </div>`;
    return;
  }

  container.innerHTML = filtered.map(item => `
    <div class="item-card ${!item.is_available ? 'unavailable-card' : ''}">
      <div class="item-img-wrap">
        <img src="${escapeHtml(item.image || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400')}" alt="${escapeHtml(item.name)}" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400'">
        <div class="item-badges-group">
          <span class="item-badge ${item.is_veg ? 'veg' : 'nonveg'}">
            <span class="badge-dot"></span> ${item.is_veg ? 'VEG' : 'NON-VEG'}
          </span>
          <span class="category-badge">${escapeHtml(item.category || 'Mains')}</span>
        </div>
        ${!item.is_available ? '<div class="sold-out-overlay">SOLD OUT</div>' : ''}
      </div>
      <div class="item-card-body">
        <div class="item-header-row">
          <h3 class="item-card-title">${escapeHtml(item.name)}</h3>
        </div>
        <p class="item-card-desc">${escapeHtml(item.description || 'Fresh gourmet dish prepared with finest ingredients.')}</p>
        <div class="item-card-footer">
          <div class="price-box">
            <span class="currency-symbol">₹</span>
            <span class="item-price">${parseFloat(item.price || 0).toFixed(2)}</span>
          </div>
          <div class="item-actions">
            <label class="toggle-switch" title="${item.is_available ? 'Available (Click to mark Sold Out)' : 'Sold Out (Click to mark Available)'}">
              <input type="checkbox" ${item.is_available ? 'checked' : ''} onchange="toggleItemAvailability('${item.id}', this.checked)">
              <span class="slider"></span>
            </label>
            <button class="action-btn edit-btn" onclick="openEditItemModal('${item.id}')" title="Edit Item"><i class="fa-solid fa-pen-to-square"></i></button>
            <button class="action-btn delete-btn" onclick="deleteMenuItem('${item.id}')" title="Delete Item"><i class="fa-solid fa-trash-can"></i></button>
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
  const vegRadio = document.getElementById('typeVegRadio');
  if (vegRadio) vegRadio.checked = true;
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
  
  if (item.is_veg !== false) {
    const vRadio = document.getElementById('typeVegRadio');
    if (vRadio) vRadio.checked = true;
  } else {
    const nvRadio = document.getElementById('typeNonVegRadio');
    if (nvRadio) nvRadio.checked = true;
  }

  document.getElementById('itemIsAvailable').checked = item.is_available !== false;
  document.getElementById('itemModal').style.display = 'flex';
}

function closeItemModal() {
  document.getElementById('itemModal').style.display = 'none';
}

async function handleSaveItem(event) {
  event.preventDefault();
  const id = document.getElementById('itemId').value;
  const isVegSelected = document.getElementById('typeVegRadio')?.checked ?? true;
  const currentRestaurantId = window.AUTH_USER_RESTAURANT_ID || (typeof Auth !== 'undefined' && Auth.getUser()?.restaurant_id) || new URLSearchParams(window.location.search).get('restaurantId') || '1';
  const payload = {
    restaurant_id: currentRestaurantId,
    restaurantId: currentRestaurantId,
    name: document.getElementById('itemName').value.trim(),
    category: document.getElementById('itemCategory').value,
    price: parseFloat(document.getElementById('itemPrice').value),
    image: document.getElementById('itemImage').value.trim(),
    description: document.getElementById('itemDesc').value.trim(),
    is_veg: isVegSelected,
    isVeg: isVegSelected,
    is_available: isAvailable,
    isAvailable: isAvailable
  };

  const isEdit = Boolean(id);
  const url = isEdit ? `/api/menu/${id}` : '/api/menu';
  const method = isEdit ? 'PUT' : 'POST';

  try {
    const res = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (res.ok || data.success) {
      if (typeof Toast !== 'undefined') Toast.success(isEdit ? 'Menu item updated successfully!' : 'New menu item added!');
      else alert(isEdit ? 'Menu item updated successfully!' : 'New menu item added!');
      closeItemModal();
      loadMenuItems();
    } else {
      if (typeof Toast !== 'undefined') Toast.error(data.message || 'Failed to save menu item.');
      else alert(data.message || 'Failed to save menu item.');
    }
  } catch (err) {
    if (typeof Toast !== 'undefined') Toast.error('Network error saving menu item.');
    else alert('Network error saving menu item.');
  }
}

async function toggleItemAvailability(id, isAvailable) {
  try {
    const res = await fetch(`/api/menu/${id}`, {
      method: 'PUT',
      headers: { 
        'Content-Type': 'application/json', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({ is_available: isAvailable, isAvailable: isAvailable })
    });
    if (res.ok) {
      if (typeof Toast !== 'undefined') Toast.success(isAvailable ? 'Item marked Available!' : 'Item marked Out of Stock!');
      const item = allMenuItems.find(i => String(i.id || i._id) === String(id));
      if (item) {
        item.is_available = isAvailable;
        item.isAvailable = isAvailable;
      }
      renderMenuItems();
    } else {
      if (typeof Toast !== 'undefined') Toast.error('Failed to update item availability.');
      renderMenuItems();
    }
  } catch (err) {
    if (typeof Toast !== 'undefined') Toast.error('Network error updating item availability.');
    renderMenuItems();
  }
}

async function deleteMenuItem(id) {
  if (!confirm('Are you sure you want to delete this menu item?')) return;
  try {
    const res = await fetch(`/api/menu/${id}`, { 
      method: 'DELETE', 
      headers: { 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      } 
    });
    if (res.ok) {
      if (typeof Toast !== 'undefined') Toast.success('Menu item deleted.');
      else alert('Menu item deleted.');
      loadMenuItems();
    } else {
      if (typeof Toast !== 'undefined') Toast.error('Failed to delete menu item.');
      else alert('Failed to delete menu item.');
    }
  } catch (_) {
    if (typeof Toast !== 'undefined') Toast.error('Error deleting menu item.');
    else alert('Error deleting menu item.');
  }
}

function escapeHtml(str) {
  if (typeof str !== 'string') return '';
  return str.replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[match]);
}
