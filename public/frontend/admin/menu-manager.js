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
    renderCategoryPills();
    renderMenuItems();
    populateCategoryDropdown();
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

function normalizeCatName(name) {
  if (!name) return '';
  let s = String(name).trim().toLowerCase();
  if (s.endsWith('s') && s.length > 3) {
    s = s.slice(0, -1);
  }
  return s;
}

function areCategoriesMatching(itemCatRaw, targetCatRaw) {
  if (!targetCatRaw || targetCatRaw.toLowerCase() === 'all') return true;
  if (!itemCatRaw) return false;

  const itemCat = String(itemCatRaw).trim().toLowerCase();
  const targetCat = String(targetCatRaw).trim().toLowerCase();

  if (itemCat === targetCat) return true;

  const nItem = normalizeCatName(itemCat);
  const nTarget = normalizeCatName(targetCat);
  if (nItem === nTarget) return true;

  const synonymGroups = [
    ['drink', 'drinks', 'beverage', 'beverages', 'cold drink', 'cold drinks', 'milk shek'],
    ['starter', 'starters', 'appetizer', 'appetizers', 'snack', 'snacks'],
    ['main', 'mains', 'main course', 'curry', 'curries'],
    ['bread', 'breads', 'roti', 'rotis', 'naan'],
    ['dessert', 'desserts', 'sweet', 'sweets', 'ice cream', 'ice creams'],
    ['soup', 'soups'],
    ['rice', 'biryani']
  ];

  for (const group of synonymGroups) {
    const hasItem = group.some(g => g === itemCat || g === nItem);
    const hasTarget = group.some(g => g === targetCat || g === nTarget);
    if (hasItem && hasTarget) return true;
  }

  return false;
}

function renderCategoryPills() {
  const container = document.getElementById('categoryPills');
  if (!container) return;

  const cats = [];
  const seen = new Set();
  allMenuItems.forEach(item => {
    const cat = (item.category || '').trim();
    if (cat && !seen.has(cat.toLowerCase())) {
      seen.add(cat.toLowerCase());
      cats.push(cat);
    }
  });

  const total = allMenuItems.length;
  const isAllActive = !activeCategory || activeCategory.toLowerCase() === 'all';

  let html = `<button class="cat-pill ${isAllActive ? 'active' : ''}" data-category="all" onclick="setCategoryFilter('all')">All Items (${total})</button>`;

  cats.forEach(cat => {
    const count = allMenuItems.filter(i => areCategoriesMatching(i.category, cat)).length;
    const isAct = !isAllActive && areCategoriesMatching(cat, activeCategory);
    html += `<button class="cat-pill ${isAct ? 'active' : ''}" data-category="${escapeHtml(cat)}" onclick="setCategoryFilter(this.dataset.category)">${escapeHtml(cat)} (${count})</button>`;
  });

  container.innerHTML = html;
}

function renderMenuItems() {
  updateStatsHeader();
  const container = document.getElementById('menuGridContainer');
  if (!container) return;

  const searchQuery = document.getElementById('menuSearchInput')?.value.trim().toLowerCase() || '';

  const filtered = allMenuItems.filter(item => {
    const matchesCat = areCategoriesMatching(item.category, activeCategory);
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
  activeCategory = cat || 'all';
  const pills = document.querySelectorAll('.cat-pill');
  pills.forEach(btn => {
    const btnCat = btn.dataset.category || btn.textContent.replace(/\s*\(\d+\)$/, '').trim();
    if (activeCategory.toLowerCase() === 'all') {
      btn.classList.toggle('active', btnCat.toLowerCase() === 'all' || btnCat.toLowerCase().startsWith('all items'));
    } else {
      const isMatch = areCategoriesMatching(btnCat, activeCategory);
      btn.classList.toggle('active', isMatch);
    }
  });
  renderMenuItems();
}

function filterMenuItems() {
  renderMenuItems();
}

function populateCategoryDropdown(selectedCategory = '') {
  const select = document.getElementById('itemCategory');
  if (!select) return;

  const cats = [];
  const seen = new Set();
  allMenuItems.forEach(item => {
    const cat = (item.category || '').trim();
    if (cat && !seen.has(cat.toLowerCase())) {
      seen.add(cat.toLowerCase());
      cats.push(cat);
    }
  });

  const defaults = ['Starters', 'Mains', 'Rice', 'Breads', 'Desserts', 'Drinks', 'Soups'];
  defaults.forEach(d => {
    if (!seen.has(d.toLowerCase())) {
      seen.add(d.toLowerCase());
      cats.push(d);
    }
  });

  if (selectedCategory && !seen.has(selectedCategory.toLowerCase())) {
    cats.unshift(selectedCategory);
  }

  select.innerHTML = cats.map(c => `
    <option value="${escapeHtml(c)}" ${selectedCategory && selectedCategory.toLowerCase() === c.toLowerCase() ? 'selected' : ''}>
      ${escapeHtml(c)}
    </option>
  `).join('') + `<option value="__new__">+ Add New Category...</option>`;
}

function handleCategorySelectChange(select) {
  if (select.value === '__new__') {
    const newCat = prompt('Enter new category name:');
    if (newCat && newCat.trim()) {
      const clean = newCat.trim();
      const opt = document.createElement('option');
      opt.value = clean;
      opt.textContent = clean;
      opt.selected = true;
      select.insertBefore(opt, select.lastElementChild);
    } else {
      select.selectedIndex = 0;
    }
  }
}

function openAddItemModal() {
  document.getElementById('modalTitle').textContent = 'Add New Menu Item';
  document.getElementById('itemId').value = '';
  document.getElementById('itemName').value = '';
  populateCategoryDropdown();
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
  populateCategoryDropdown(item.category || '');
  if (item.category) {
    document.getElementById('itemCategory').value = item.category;
  }
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
  const isAvailableSelected = document.getElementById('itemIsAvailable')?.checked ?? true;
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
    is_available: isAvailableSelected,
    isAvailable: isAvailableSelected
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

// ─────────────────────────────────────────────────────────────
// BULK IMPORT EXCEL / CSV CONTROLLER
// ─────────────────────────────────────────────────────────────

let parsedCsvItems = [];

function downloadSampleMenuCsv() {
  const rows = [
    ["Name", "Category", "Price", "Is_Veg", "Description", "Is_Available"],
    ["Paneer Butter Masala", "Main Course", "240", "Yes", "Rich tomato and cashew butter gravy", "Yes"],
    ["Butter Naan", "Breads", "45", "Yes", "Crispy tandoori baked bread with butter", "Yes"],
    ["Veg Biryani", "Rice & Biryani", "190", "Yes", "Aromatic basmati rice with spices", "Yes"],
    ["Masala Dosa", "South Indian", "120", "Yes", "Crispy crepe with spiced potato masala", "Yes"],
    ["Cold Coffee with Ice Cream", "Beverages", "90", "Yes", "Chilled brewed coffee with vanilla ice cream", "Yes"],
    ["Chicken Tikka Masala", "Non-Veg", "280", "No", "Tandoor roasted chicken in spicy gravy", "Yes"],
    ["Margherita Pizza", "Pizza", "220", "Yes", "Classic cheese and herb pizza", "Yes"],
    ["Gulab Jamun (2 Pcs)", "Desserts", "60", "Yes", "Hot sweet syrup dumplings", "Yes"]
  ];

  let csvContent = "\uFEFF"; // UTF-8 BOM
  rows.forEach(row => {
    csvContent += row.map(val => `"${val.replace(/"/g, '""')}"`).join(",") + "\r\n";
  });

  const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.setAttribute("href", url);
  link.setAttribute("download", "sample_menu_template.csv");
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

function openBulkImportModal() {
  parsedCsvItems = [];
  const modal = document.getElementById('bulkImportModal');
  const preview = document.getElementById('csvPreviewSection');
  const fileInput = document.getElementById('csvFileInput');
  const label = document.getElementById('uploadFileLabel');
  
  if (fileInput) fileInput.value = '';
  if (preview) preview.style.display = 'none';
  if (label) label.textContent = 'Click or Drag & Drop CSV File Here';
  if (modal) modal.style.display = 'flex';
}

function closeBulkImportModal() {
  const modal = document.getElementById('bulkImportModal');
  if (modal) modal.style.display = 'none';
}

function handleCsvFileSelected(event) {
  const file = event.target.files[0];
  if (!file) return;

  const label = document.getElementById('uploadFileLabel');
  if (label) label.textContent = `Selected: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;

  const reader = new FileReader();
  reader.onload = function(e) {
    const text = e.target.result;
    parseCsvClientSide(text);
  };
  reader.readAsText(file);
}

function parseCsvClientSide(csvText) {
  parsedCsvItems = [];
  const lines = csvText.split(/\r\n|\n/);
  if (lines.length < 2) return;

  const headers = lines[0].split(',').map(h => h.trim().toLowerCase().replace(/['"]/g, ''));
  const nameIdx = headers.findIndex(h => h.includes('name'));
  const catIdx = headers.findIndex(h => h.includes('cat'));
  const priceIdx = headers.findIndex(h => h.includes('price'));
  const vegIdx = headers.findIndex(h => h.includes('veg'));
  const descIdx = headers.findIndex(h => h.includes('desc'));

  for (let i = 1; i < lines.length; i++) {
    const line = lines[i].trim();
    if (!line) continue;
    
    // Split by comma ignoring commas inside quotes
    const row = line.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/).map(v => v.trim().replace(/^"|"$/g, ''));
    if (row.length === 0 || !row[nameIdx >= 0 ? nameIdx : 0]) continue;

    const name = row[nameIdx >= 0 ? nameIdx : 0];
    const category = catIdx >= 0 && row[catIdx] ? row[catIdx] : 'General';
    const rawPrice = priceIdx >= 0 && row[priceIdx] ? row[priceIdx].replace(/[^0-9.]/g, '') : '0';
    const price = parseFloat(rawPrice) || 0;
    const rawVeg = vegIdx >= 0 && row[vegIdx] ? row[vegIdx].toLowerCase() : 'yes';
    const isVeg = !(rawVeg === 'no' || rawVeg === '0' || rawVeg === 'false' || rawVeg === 'non-veg' || rawVeg === 'nonveg');
    const desc = descIdx >= 0 && row[descIdx] ? row[descIdx] : '';

    parsedCsvItems.push({ name, category, price, is_veg: isVeg, description: desc, is_available: true });
  }

  const preview = document.getElementById('csvPreviewSection');
  const tbody = document.getElementById('csvPreviewTableBody');
  const countEl = document.getElementById('csvParsedCount');

  if (countEl) countEl.textContent = parsedCsvItems.length;
  if (tbody) {
    tbody.innerHTML = parsedCsvItems.slice(0, 10).map(item => `
      <tr style="border-bottom:1px solid var(--border);">
        <td style="padding:8px 12px; font-weight:600; color:var(--text-primary);">${escapeHtml(item.name)}</td>
        <td style="padding:8px 12px; color:var(--text-secondary);">${escapeHtml(item.category)}</td>
        <td style="padding:8px 12px; font-weight:700; color:#10B981;">₹${item.price.toFixed(2)}</td>
        <td style="padding:8px 12px;">${item.is_veg ? '<span style="color:#34D399;">🟢 Veg</span>' : '<span style="color:#F87171;">🔴 Non-Veg</span>'}</td>
      </tr>
    `).join('') + (parsedCsvItems.length > 10 ? `<tr><td colspan="4" style="padding:8px; text-align:center; color:var(--text-muted); font-size:11px;">...and ${parsedCsvItems.length - 10} more items</td></tr>` : '');
  }

  if (preview) preview.style.display = 'block';
}

async function handleBulkImportSubmit(event) {
  event.preventDefault();
  const fileInput = document.getElementById('csvFileInput');
  const btn = document.getElementById('btnConfirmBulkImport');

  if (parsedCsvItems.length === 0 && (!fileInput || !fileInput.files[0])) {
    alert('Please select a valid CSV file with menu items first.');
    return;
  }

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Importing Items...';
  }

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let res;

    if (parsedCsvItems.length > 0) {
      res = await fetch('/api/menu/bulk-import', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ items: parsedCsvItems })
      });
    } else {
      const formData = new FormData();
      formData.append('file', fileInput.files[0]);
      res = await fetch('/api/menu/bulk-import', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: formData
      });
    }

    const data = await res.json();
    if (res.ok && data.success) {
      alert(`🎉 Success! ${data.count || parsedCsvItems.length} menu items imported successfully!`);
      closeBulkImportModal();
      loadMenuItems();
    } else {
      alert(data.message || 'Failed to import menu items. Please check your plan limits or CSV format.');
    }
  } catch (err) {
    console.error('Bulk import error:', err);
    alert('Network error while uploading menu items. Please try again.');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Confirm &amp; Import Items';
    }
  }
}

// ─────────────────────────────────────────────────────────────
// AI PHOTO MENU SCANNER CONTROLLER
// ─────────────────────────────────────────────────────────────

let aiDetectedDishesList = [];

function openAiMenuScanModal() {
  aiDetectedDishesList = [];
  const modal = document.getElementById('aiScanModal');
  const results = document.getElementById('aiScanResultsSection');
  const saveBtn = document.getElementById('btnSaveAiDishes');
  const fileInput = document.getElementById('menuPhotoInput');
  const rawText = document.getElementById('rawMenuText');
  const label = document.getElementById('aiPhotoLabel');

  if (fileInput) fileInput.value = '';
  if (rawText) rawText.value = '';
  if (results) results.style.display = 'none';
  if (saveBtn) saveBtn.style.display = 'none';
  if (label) label.textContent = 'Click or Drag Menu Card Photo Here';
  if (modal) modal.style.display = 'flex';
}

function closeAiMenuScanModal() {
  const modal = document.getElementById('aiScanModal');
  if (modal) modal.style.display = 'none';
}

function handleMenuPhotoSelected(event) {
  const file = event.target.files[0];
  if (!file) return;

  const label = document.getElementById('aiPhotoLabel');
  if (label) label.innerHTML = `📸 <b>${escapeHtml(file.name)}</b> (${(file.size / 1024).toFixed(1)} KB) Selected!`;
}

async function runAiMenuScan() {
  const fileInput = document.getElementById('menuPhotoInput');
  const rawText = document.getElementById('rawMenuText')?.value || '';
  const btn = document.getElementById('btnScanMenuAi');

  if ((!fileInput || !fileInput.files[0]) && !rawText.trim()) {
    alert('Please upload a photo of your menu card or paste the menu text first.');
    return;
  }

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> AI Analyzing Dishes &amp; Prices...';
  }

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const formData = new FormData();
    if (fileInput && fileInput.files[0]) {
      formData.append('image', fileInput.files[0]);
    }
    if (rawText.trim()) {
      formData.append('raw_text', rawText);
    }

    const res = await fetch('/api/menu/ai-scan', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: formData
    });

    const data = await res.json();
    if (res.ok && data.success && Array.isArray(data.items) && data.items.length > 0) {
      aiDetectedDishesList = data.items;
      renderAiScanResultsTable();
    } else {
      alert(data.message || 'No dishes detected. Please ensure the menu photo is clear with legible text and prices.');
    }
  } catch (err) {
    console.error('AI Scan error:', err);
    alert('Failed to process menu photo. Please try again.');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-microchip"></i> Start AI Dish Detection';
    }
  }
}

function renderAiScanResultsTable() {
  const section = document.getElementById('aiScanResultsSection');
  const tbody = document.getElementById('aiScanTableBody');
  const countEl = document.getElementById('aiDetectedCount');
  const saveBtn = document.getElementById('btnSaveAiDishes');

  if (countEl) countEl.textContent = aiDetectedDishesList.length;

  if (tbody) {
    tbody.innerHTML = aiDetectedDishesList.map((item, idx) => `
      <tr style="border-bottom:1px solid var(--border);" id="aiRow_${idx}">
        <td style="padding:6px 8px; text-align:center;">
          <input type="checkbox" class="ai-item-check" data-index="${idx}" checked style="accent-color:#6366F1; width:15px; height:15px;">
        </td>
        <td style="padding:6px 8px;">
          <input type="text" value="${escapeHtml(item.name)}" oninput="updateAiItemField(${idx}, 'name', this.value)" style="width:100%; box-sizing:border-box; padding:6px 8px; background:var(--bg); border:1px solid var(--border); border-radius:6px; color:var(--text-primary); font-size:12px;">
        </td>
        <td style="padding:6px 8px;">
          <input type="text" value="${escapeHtml(item.category)}" oninput="updateAiItemField(${idx}, 'category', this.value)" style="width:100%; box-sizing:border-box; padding:6px 8px; background:var(--bg); border:1px solid var(--border); border-radius:6px; color:var(--text-secondary); font-size:12px;">
        </td>
        <td style="padding:6px 8px;">
          <input type="number" step="0.5" value="${item.price}" oninput="updateAiItemField(${idx}, 'price', parseFloat(this.value) || 0)" style="width:100%; box-sizing:border-box; padding:6px 8px; background:var(--bg); border:1px solid var(--border); border-radius:6px; color:#10B981; font-weight:700; font-size:12px;">
        </td>
        <td style="padding:6px 8px; text-align:center;">
          <button type="button" onclick="toggleAiItemVeg(${idx})" style="border:none; background:transparent; cursor:pointer; font-size:16px;" title="Click to toggle Veg / Non-Veg">
            ${item.is_veg ? '🟢' : '🔴'}
          </button>
        </td>
      </tr>
    `).join('');
  }

  if (section) section.style.display = 'block';
  if (saveBtn) saveBtn.style.display = 'inline-flex';
}

function updateAiItemField(idx, field, val) {
  if (aiDetectedDishesList[idx]) {
    aiDetectedDishesList[idx][field] = val;
  }
}

function toggleAiItemVeg(idx) {
  if (aiDetectedDishesList[idx]) {
    aiDetectedDishesList[idx].is_veg = !aiDetectedDishesList[idx].is_veg;
    renderAiScanResultsTable();
  }
}

function toggleSelectAllAiItems(master) {
  const checks = document.querySelectorAll('.ai-item-check');
  checks.forEach(c => c.checked = master.checked);
}

function addCustomRowToAiTable() {
  aiDetectedDishesList.unshift({
    name: 'New Custom Dish',
    category: 'Main Course',
    price: 150,
    is_veg: true,
    description: '',
    is_available: true
  });
  renderAiScanResultsTable();
}

async function saveAiDetectedDishes() {
  const checks = document.querySelectorAll('.ai-item-check:checked');
  const selectedIndices = Array.from(checks).map(c => parseInt(c.getAttribute('data-index')));
  const itemsToSave = selectedIndices.map(idx => aiDetectedDishesList[idx]).filter(Boolean);

  if (itemsToSave.length === 0) {
    alert('Please select at least one item from the table to save.');
    return;
  }

  const saveBtn = document.getElementById('btnSaveAiDishes');
  if (saveBtn) {
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving Items to Menu...';
  }

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const res = await fetch('/api/menu/bulk-import', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ items: itemsToSave })
    });

    const data = await res.json();
    if (res.ok && data.success) {
      alert(`🎉 Awesome! ${data.count || itemsToSave.length} dishes have been added to your menu!`);
      closeAiMenuScanModal();
      loadMenuItems();
    } else {
      alert(data.message || 'Failed to save menu items.');
    }
  } catch (err) {
    console.error('Save AI dishes error:', err);
    alert('Network error while saving dishes.');
  } finally {
    if (saveBtn) {
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Save All Selected Items (1-Click)';
    }
  }
}
