const params = new URLSearchParams(window.location.search);
const restaurantId = params.get('restaurantId') || params.get('restaurant_id') || params.get('restaurant') || '1';
let tableId = params.get('tableId') || params.get('table_id') || params.get('table') || '1';
const API_BASE = window.DINEFLOW_API_BASE || `${window.location.origin}/api`;

const translations = {
  en: { welcome: 'Smart QR Restaurant Ordering', table: 'Table', all: 'All', veg: '🟢 Veg', nonveg: '🔴 Non-Veg', menu: 'Our Menu', subtitle: 'Freshly prepared, just for you', search: 'Search delicious food...', callWaiter: 'Call Waiter', loading: 'Loading menu…', unavailable: 'Out of stock', available: 'Available', empty: 'No dishes found', emptyHint: 'Try changing your search or filters.', waiterSent: 'Your waiter has been called.', waiterError: 'Could not call waiter. Please try again.', calling: 'Calling…' },
  hi: { welcome: 'स्मार्ट QR रेस्टोरेंट ऑर्डरिंग', table: 'टेबल', all: 'सभी', veg: '🟢 वेज', nonveg: '🔴 नॉन-वेज', menu: 'हमारा मेनू', subtitle: 'आपके लिए ताज़ा तैयार', search: 'खाना खोजें...', callWaiter: 'वेटर बुलाएँ', loading: 'मेनू लोड हो रहा है…', unavailable: 'स्टॉक में नहीं', available: 'उपलब्ध', empty: 'कोई डिश नहीं मिली', emptyHint: 'खोज या फ़िल्टर बदलकर देखें।', waiterSent: 'वेटर को बुला दिया गया है।', waiterError: 'वेटर को नहीं बुला सके। फिर कोशिश करें।', calling: 'बुलाया जा रहा है…' },
  gu: { welcome: 'સ્માર્ટ QR રેસ્ટોરન્ટ ઓર્ડરિંગ', table: 'ટેબલ', all: 'બધા', veg: '🟢 વેજ', nonveg: '🔴 નોન-વેજ', menu: 'અમારું મેનૂ', subtitle: 'તમારા માટે તાજું બનાવેલું', search: 'ખાવાનું શોધો...', callWaiter: 'વેઇટરને બોલાવો', loading: 'મેનૂ લોડ થઈ રહ્યું છે…', unavailable: 'સ્ટોકમાં નથી', available: 'ઉપલબ્ધ', empty: 'કોઈ વાનગી મળી નથી', emptyHint: 'શોધ અથવા ફિલ્ટર બદલીને જુઓ.', waiterSent: 'વેઇટરને બોલાવવામાં આવ્યા છે.', waiterError: 'વેઇટરને બોલાવી શકાયા નથી. ફરી પ્રયાસ કરો.', calling: 'બોલાવવામાં આવે છે…' }
};

let language = localStorage.getItem('dineflow-language') || 'en';
let menuItems = [];
let category = 'All';
let diet = 'all';
let activeView = 'all';
let activeFilters = new Set();
let activeSuggestionIndex = -1;
const FAVORITES_STORAGE_KEY = `dineflow-favorites-${restaurantId}`;
const RECENT_STORAGE_KEY = `dineflow-recent-items-${restaurantId}`;
const THEME_STORAGE_KEY = 'dineflow-theme';
const CART_STORAGE_KEY = `dineflow-cart-${restaurantId}`;
const COMBO_OFFERS = [
  { id: 'combo-lunch', name: 'Lunch Combo', price: 499, includes: ['1 Pizza', '1 Soft Drink', '1 Dessert'], description: 'Perfect for a quick midday meal.' },
  { id: 'combo-family', name: 'Family Feast', price: 799, includes: ['2 Burgers', '2 Fries', '2 Drinks'], description: 'Great for sharing with friends or family.' },
  { id: 'combo-date', name: 'Date Night', price: 649, includes: ['1 Pasta', '2 Mocktails', '1 Brownie'], description: 'A cozy option for a relaxed evening.' }
];

const $ = (selector) => document.querySelector(selector);
const getText = (key) => translations[language][key] || translations.en[key] || key;
const escapeHtml = (value = '') => String(value).replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[char]);

function unwrap(response) { return response && response.data ? response.data : response; }

function getStoredItems(storageKey) {
  try {
    const stored = localStorage.getItem(storageKey);
    const values = stored ? JSON.parse(stored) : [];
    return Array.isArray(values) ? values : [];
  } catch (error) {
    console.warn('Unable to read local storage item:', error);
    return [];
  }
}

function saveStoredItems(storageKey, values) {
  localStorage.setItem(storageKey, JSON.stringify(values));
}

function getFavoriteIds() {
  return new Set(getStoredItems(FAVORITES_STORAGE_KEY));
}

function getRecentItemIds() {
  return getStoredItems(RECENT_STORAGE_KEY);
}

function getRecentItems() {
  const recentIds = getRecentItemIds();
  return recentIds.map(id => menuItems.find(item => String(item.id) === String(id))).filter(Boolean);
}

function toggleFavorite(itemId) {
  const normalizedId = String(itemId);
  const favorites = getStoredItems(FAVORITES_STORAGE_KEY).map(String);
  const isFavorite = favorites.includes(normalizedId);
  const nextFavorites = isFavorite ? favorites.filter(id => id !== normalizedId) : [...favorites, normalizedId];
  saveStoredItems(FAVORITES_STORAGE_KEY, nextFavorites);
  showToast(isFavorite ? 'Removed from Favorites' : 'Added to Favorites');
  renderMenu();
}

function removeFavorite(itemId) {
  const favorites = getStoredItems(FAVORITES_STORAGE_KEY).map(String).filter(id => id !== String(itemId));
  saveStoredItems(FAVORITES_STORAGE_KEY, favorites);
  showToast('Removed from Favorites');
  renderMenu();
}

function getFavoriteItems() {
  const favorites = getStoredItems(FAVORITES_STORAGE_KEY).map(String);
  return favorites.map(id => menuItems.find(item => String(item.id) === id)).filter(Boolean);
}

function renderRecommendedSection() {
  const section = $('#recommendedSection');
  if (!section) return;

  const recommendedItems = menuItems
    .filter(item => (item.available !== false && item.is_available !== false) && (item.popular || getFavoriteIds().has(String(item.id)) || getRecentItemIds().includes(String(item.id))))
    .slice(0, 4);

  if (!recommendedItems.length) {
    section.style.display = 'none';
    section.innerHTML = '';
    return;
  }

  section.style.display = 'block';
  section.innerHTML = `
    <div class="promo-section__header">
      <h3 class="promo-section__title">✨ Recommended for You</h3>
      <span class="promo-section__meta">Picked for your taste</span>
    </div>
    <div class="promo-grid">
      ${recommendedItems.map(item => {
        const isVeg = item.is_veg !== false && item.dietary !== 'non-veg';
        return `
        <div class="promo-card" onclick="openItemModal(menuItems.find(i => String(i.id) === '${escapeHtml(item.id)}'))" style="cursor:pointer;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <span class="promo-card__tag">${item.popular ? '🔥 Popular' : '✨ Chef Special'}</span>
            <span style="font-size:11px; font-weight:700;">${isVeg ? '🟢 Veg' : '🔴 Non-Veg'}</span>
          </div>
          <div class="promo-card__name">${escapeHtml(item.name)}</div>
          <div class="promo-card__desc">${escapeHtml(item.description || 'Freshly prepared specialty dish with signature seasoning.')}</div>
          <div style="display:flex; justify-content:space-between; align-items:center; margin-top:auto; padding-top:8px;">
            <strong style="color:#059669; font-size:1.05rem; font-weight:800;">₹${Number(item.price || 0).toFixed(2)}</strong>
            <button class="promo-card__btn" type="button" data-recommended-item-id="${escapeHtml(item.id)}" onclick="event.stopPropagation(); addToCart(menuItems.find(i => String(i.id) === '${escapeHtml(item.id)}'));">
              + Add
            </button>
          </div>
        </div>
      `;
      }).join('')}
    </div>
  `;
}

function renderComboOffersSection() {
  const section = $('#comboOffersSection');
  if (!section) return;

  section.style.display = 'block';
  section.innerHTML = `
    <div class="promo-section__header">
      <h3 class="promo-section__title">🍱 Combo Offers</h3>
      <span class="promo-section__meta">Great value deals</span>
    </div>
    <div class="promo-grid">
      ${COMBO_OFFERS.map(combo => `
        <div class="combo-card">
          <div class="combo-card__title">${escapeHtml(combo.name)}</div>
          <div class="combo-card__includes">${escapeHtml(combo.includes.join(' • '))}</div>
          <div style="display:flex; justify-content:space-between; align-items:center; margin-top:auto; padding-top:6px;">
            <div class="combo-card__price">₹${escapeHtml(combo.price)}</div>
            <button class="combo-card__btn" type="button" data-combo-add-id="${escapeHtml(combo.id)}">+ Add Combo</button>
          </div>
        </div>
      `).join('')}
    </div>
  `;
}

function renderFavoritesSection() {
  const favorites = getFavoriteItems();
  const section = $('#favoritesSection');
  if (!section) return;

  if (!favorites.length) {
    section.style.display = 'none';
    section.innerHTML = '';
    return;
  }

  section.style.display = 'block';
  section.innerHTML = `
    <div class="favorites-section__header">
      <h3>Favorites</h3>
      <span class="favorites-section__meta">${favorites.length} saved</span>
    </div>
    <div class="favorites-list">
      ${favorites.map(item => `
        <div class="favorite-chip" data-favorite-chip-id="${escapeHtml(item.id)}">
          <span>${escapeHtml(item.name)}</span>
          <button class="favorite-chip__remove" type="button" data-favorite-remove-id="${escapeHtml(item.id)}" aria-label="Remove ${escapeHtml(item.name)} from favorites">×</button>
        </div>
      `).join('')}
    </div>
  `;
}

function renderRecentSection() {
  const recentItems = getRecentItems();
  const section = $('#recentSection');
  if (!section) return;

  if (!recentItems.length) {
    section.style.display = 'none';
    section.innerHTML = '';
    return;
  }

  section.style.display = 'block';
  section.innerHTML = `
    <div class="favorites-section__header">
      <h3>Recently Viewed</h3>
      <button class="recent-clear" type="button" data-clear-recent>Clear History</button>
    </div>
    <div class="favorites-list">
      ${recentItems.map(item => `
        <button class="favorite-chip" type="button" data-recent-item-id="${escapeHtml(item.id)}" aria-label="View ${escapeHtml(item.name)}">
          <span>${escapeHtml(item.name)}</span>
        </button>
      `).join('')}
    </div>
  `;
}

function addRecentlyViewed(item) {
  if (!item?.id) return;
  const recent = getStoredItems(RECENT_STORAGE_KEY);
  const nextRecent = [String(item.id), ...recent.map(String).filter(id => id !== String(item.id))].slice(0, 5);
  saveStoredItems(RECENT_STORAGE_KEY, nextRecent);
}

function clearRecentlyViewed() {
  saveStoredItems(RECENT_STORAGE_KEY, []);
  renderMenu();
}

const DEFAULT_FOOD_PLACEHOLDER = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='400' height='300' fill='%231e293b'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' font-size='48' fill='%2364748b'>🍽️</text></svg>";

function resolveImagePath(image, fallback = DEFAULT_FOOD_PLACEHOLDER) {
  const value = typeof image === 'string' ? image.trim() : '';
  if (!value) return fallback;
  if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:') || value.startsWith('blob:')) return value;
  if (value.startsWith('/')) return value;
  return value;
}

function setTableOptions(tables = []) {
  const select = $('#tableNumber');
  if (!select) return;
  const availableTables = tables.length ? tables : Array.from({ length: 20 }, (_, index) => ({ id: String(index + 1), tableNumber: String(index + 1) }));
  select.innerHTML = availableTables.map(table => {
    const id = table.id || table._id || table.table_id || table.tableNumber || table.table_number;
    const number = table.table_number || table.tableNumber || id;
    return `<option value="${escapeHtml(id)}">#${escapeHtml(number)}</option>`;
  }).join('');
  if (![...select.options].some(option => option.value === String(tableId))) {
    select.insertAdjacentHTML('afterbegin', `<option value="${escapeHtml(tableId)}">#${escapeHtml(tableId)}</option>`);
  }
  select.value = String(tableId);
}

async function request(path, options) {
  const response = await fetch(`${API_BASE}${path}`, options);
  const payload = await response.json().catch(() => ({}));
  if (!response.ok || payload.success === false) throw new Error(payload.message || 'Request failed');
  return unwrap(payload);
}

function getStaticFoodItemsList() {
  if (typeof foodItems !== 'undefined' && Array.isArray(foodItems) && foodItems.length > 0) return foodItems;
  if (window.foodItems && Array.isArray(window.foodItems) && window.foodItems.length > 0) return window.foodItems;
  return [];
}

function parseBoolean(val, defaultVal = true) {
  if (val === undefined || val === null) return defaultVal;
  if (typeof val === 'boolean') return val;
  if (typeof val === 'number') return val === 1;
  if (typeof val === 'string') {
    const s = val.trim().toLowerCase();
    if (s === '0' || s === 'false' || s === 'no' || s === 'unavailable' || s === 'sold_out' || s === 'out_of_stock') return false;
    if (s === '1' || s === 'true' || s === 'yes' || s === 'available' || s === 'in_stock') return true;
  }
  return defaultVal;
}

function normalizeItem(item) {
  let cat = String(item.category || 'Mains').trim();
  if (cat.toLowerCase() === 'main course' || cat.toLowerCase() === 'mains') cat = 'Mains';
  if (cat.toLowerCase() === 'beverage' || cat.toLowerCase() === 'drinks') cat = 'Drinks';
  if (cat.toLowerCase() === 'starter' || cat.toLowerCase() === 'starters') cat = 'Starters';
  if (cat.toLowerCase() === 'bread' || cat.toLowerCase() === 'breads') cat = 'Breads';
  if (cat.toLowerCase() === 'dessert' || cat.toLowerCase() === 'desserts') cat = 'Desserts';

  const isPop = Boolean(item.popular || (item.badge && String(item.badge).toLowerCase().includes('popular')) || (item.badge && String(item.badge).toLowerCase().includes('seller')) || Number(item.rating) >= 4.8);

  const isAvail = parseBoolean(item.is_available ?? item.available ?? (item.status === 'out_of_stock' ? false : true), true);

  // Bulletproof Veg / Non-Veg detection
  let isVeg = true;
  if (item.is_veg !== undefined && item.is_veg !== null) {
    isVeg = parseBoolean(item.is_veg, true);
  } else if (item.isVeg !== undefined && item.isVeg !== null) {
    isVeg = parseBoolean(item.isVeg, true);
  } else if (item.diet) {
    isVeg = !String(item.diet).toLowerCase().includes('non');
  } else if (item.food_type) {
    isVeg = !String(item.food_type).toLowerCase().includes('non');
  } else if (item.type) {
    isVeg = !String(item.type).toLowerCase().includes('non');
  }

  let badgeText = item.badge || '';
  if (isAvail && (badgeText.toLowerCase().includes('sold out') || badgeText.toLowerCase().includes('out of stock'))) {
    badgeText = isPop ? 'Popular' : 'Special';
  }
  if (!badgeText) badgeText = cat;

  return {
    ...item,
    id: item.id || item._id,
    category: cat,
    image: resolveImagePath(item.image),
    available: isAvail,
    isVeg: isVeg,
    rating: String(item.rating || '4.8'),
    badge: badgeText,
    popular: isPop
  };
}

function applyLanguage() {
  document.documentElement.lang = language;
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.dataset.i18n;
    const txt = getText(key);
    if (el.classList.contains('diet-btn')) {
      const dietType = el.dataset.diet;
      if (dietType === 'all') el.innerHTML = `🍽️ <span>${txt}</span>`;
      else if (dietType === 'veg') el.innerHTML = `🟢 <span>${txt}</span>`;
      else if (dietType === 'nonveg') el.innerHTML = `🔴 <span>${txt}</span>`;
      else el.textContent = txt;
    } else {
      el.textContent = txt;
    }
  });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = getText(el.dataset.i18nPlaceholder); });
  const langSel = $('#languageSelect');
  if (langSel) langSel.value = language;
  if (menuItems.length > 0) { renderCategories(); renderMenu(); }
}

function renderCategories() {
  const categoryIcons = {
    'all': '🍽️',
    'mains': '🍲',
    'main course': '🍲',
    'starters': '🥟',
    'starter': '🥟',
    'breads': '🍞',
    'bread': '🍞',
    'rice': '🍚',
    'biryani': '🍚',
    'beverages': '🥤',
    'beverage': '🥤',
    'drinks': '🥤',
    'desserts': '🍨',
    'dessert': '🍨',
    'pizza': '🍕',
    'burger': '🍔'
  };

  const rawCategories = menuItems.map(item => item.category).filter(Boolean);
  const normalizedCategories = [];
  rawCategories.forEach(cat => {
    let cleanCat = String(cat).trim();
    if (!normalizedCategories.includes(cleanCat)) normalizedCategories.push(cleanCat);
  });

  const categories = ['All', ...normalizedCategories];
  const catSection = $('#categorySection');
  if (catSection) {
    catSection.innerHTML = categories.map(item => {
      const key = item.toLowerCase().trim();
      const icon = categoryIcons[key] || '🍴';
      const label = item === 'All' ? getText('all') : escapeHtml(item);
      const isAct = String(item).toLowerCase() === String(category).toLowerCase();
      return `<button class="category-btn ${isAct ? 'active' : ''}" data-category="${escapeHtml(item)}" type="button"><span class="cat-icon">${icon}</span> <span>${label}</span></button>`;
    }).join('');
  }
}

function filteredItems() {
  const searchInput = $('#searchInput');
  const search = searchInput ? searchInput.value.trim().toLowerCase() : '';
  const favorites = getFavoriteIds();
  const recentIds = new Set(getRecentItemIds());

  let matches = menuItems.filter(item => {
    // 1. Category Filter
    let matchesCategory = true;
    if (category && String(category).toLowerCase() !== 'all') {
      const itemCat = String(item.category || '').toLowerCase().trim();
      const selCat = String(category).toLowerCase().trim();
      matchesCategory = (itemCat === selCat) ||
        (selCat === 'mains' && (itemCat.includes('main') || itemCat.includes('curry') || itemCat.includes('thali') || itemCat.includes('paneer'))) ||
        (selCat === 'drinks' && (itemCat.includes('drink') || itemCat.includes('bever'))) ||
        (selCat === 'beverages' && (itemCat.includes('drink') || itemCat.includes('bever'))) ||
        (selCat === 'starters' && (itemCat.includes('start') || itemCat.includes('snack') || itemCat.includes('appetizer'))) ||
        (selCat === 'breads' && (itemCat.includes('bread') || itemCat.includes('roti') || itemCat.includes('naan'))) ||
        (selCat === 'desserts' && (itemCat.includes('dessert') || itemCat.includes('sweet') || itemCat.includes('ice')));
    }

    // 2. Diet Filter
    let matchesDiet = true;
    if (diet === 'veg') {
      matchesDiet = Boolean(item.isVeg);
    } else if (diet === 'nonveg') {
      matchesDiet = !item.isVeg;
    }

    // 3. Search Query
    const matchesSearch = !search || `${item.name} ${item.description || ''} ${item.category || ''}`.toLowerCase().includes(search);

    // 4. View filters (Favorites / Recent)
    const isFavorite = favorites.has(String(item.id));
    const isRecent = recentIds.has(String(item.id));
    const matchesView = activeView === 'all' || (activeView === 'favorites' && isFavorite) || (activeView === 'recent' && isRecent) || (activeView === 'popular' && Boolean(item.popular));

    // 5. Quick Tag Filters
    const matchesPopular = !activeFilters.has('popular') || Boolean(item.popular);
    const matchesAvailable = !activeFilters.has('available') || Boolean(item.available);
    const matchesRating = !activeFilters.has('rating') || Number(item.rating || 0) >= 4.5;

    return matchesCategory && matchesDiet && matchesSearch && matchesView && matchesPopular && matchesAvailable && matchesRating;
  });

  // Sort
  const sort = $('#sortSelect')?.value;
  if (sort === 'price-asc') matches.sort((a, b) => Number(a.price || 0) - Number(b.price || 0));
  if (sort === 'price-desc') matches.sort((a, b) => Number(b.price || 0) - Number(a.price || 0));

  return matches;
}

function resetAllFilters() {
  category = 'All';
  diet = 'all';
  activeFilters.clear();
  const searchInput = $('#searchInput');
  if (searchInput) searchInput.value = '';
  const sortSelect = $('#sortSelect');
  if (sortSelect) sortSelect.value = 'default';
  document.querySelectorAll('.diet-btn').forEach(b => b.classList.toggle('active', b.dataset.diet === 'all'));
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('is-active', 'active'));
  renderCategories();
  renderMenu();
}

function renderMenu() {
  const items = filteredItems();
  const favorites = getFavoriteIds();
  renderRecommendedSection();
  renderComboOffersSection();
  renderFavoritesSection();
  renderRecentSection();
  const loadingEl = $('#loadingState');
  if (loadingEl) {
    loadingEl.hidden = true;
    loadingEl.style.display = 'none';
  }

  const foodGrid = $('#foodGrid');
  if (!foodGrid) return;

  if (!items.length) {
    foodGrid.innerHTML = `
      <div style="grid-column: 1 / -1; text-align:center; padding: 48px 20px; background:var(--card-bg); border-radius:20px; border:1px solid var(--card-border); box-shadow:var(--shadow-card);">
        <div style="font-size:44px; margin-bottom:12px;">🔍</div>
        <h3 style="font-size:18px; font-weight:800; color:var(--text-main); margin-bottom:6px;">No Dishes Found</h3>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">Try adjusting your search keywords or active filters.</p>
        <button type="button" onclick="resetAllFilters()" style="background:linear-gradient(135deg, #6366F1, #4F46E5); border:none; color:#fff; padding:10px 22px; border-radius:12px; font-size:13px; font-weight:800; cursor:pointer; box-shadow:0 4px 12px rgba(99,102,241,0.35);">Reset All Filters</button>
      </div>
    `;
    return;
  }

  foodGrid.innerHTML = items.map(item => {
    const imageSrc = resolveImagePath(item.image);
    const isFavorite = favorites.has(String(item.id));
    return `<article class="food-card ${item.available ? '' : 'unavailable'}" data-item-id="${escapeHtml(item.id)}" tabindex="0" role="button" aria-label="View details for ${escapeHtml(item.name)}" style="position:relative;">
      ${!item.available ? `<div style="position:absolute; top:12px; left:12px; z-index:5; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:4px 8px; border-radius:6px; box-shadow:0 4px 10px rgba(239,68,68,0.4); letter-spacing:0.5px;">🚫 SOLD OUT</div>` : ''}
      <button class="favorite-btn ${isFavorite ? 'is-favorite' : ''}" type="button" data-favorite-toggle="${escapeHtml(item.id)}" aria-label="${isFavorite ? 'Remove from favorites' : 'Add to favorites'}">♡</button>
      <img class="food-image" src="${escapeHtml(imageSrc)}" alt="${escapeHtml(item.name)}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/images/placeholder-food.svg';" style="${!item.available ? 'filter: grayscale(0.8) opacity(0.6);' : ''}">
      <div class="food-content">
        <div class="food-topline">
          <span class="food-type ${item.isVeg ? 'veg' : 'nonveg'}">${item.isVeg ? '● Veg' : '● Non-Veg'}</span>
          <span class="rating-tag">★ ${escapeHtml(item.rating)}</span>
        </div>
        <h3 style="${!item.available ? 'color:var(--text-muted); text-decoration:line-through;' : ''}">${escapeHtml(item.name)}</h3>
        <p>${escapeHtml(item.description || '')}</p>
        <div class="food-footer">
          <strong>₹${escapeHtml(item.price)}</strong>
          ${item.available ? `<button class="btn-add-to-order" type="button" data-add-cart="${escapeHtml(item.id)}">+ Add</button>` : `<button class="btn-add-to-order sold-out" type="button" disabled style="opacity:0.6;cursor:not-allowed;background:#EF4444;color:#fff;pointer-events:none;">Sold Out</button>`}
        </div>
      </div>
    </article>`;
  }).join('');
}

async function openItemModal(item) {
  if (!item) return;
  addRecentlyViewed(item);
  renderMenu();
  $('#modalItemImage').src = resolveImagePath(item.image);
  $('#modalItemImage').alt = item.name;
  $('#modalItemCategory').textContent = item.category || '';
  $('#modalItemName').textContent = item.name;
  $('#modalItemType').textContent = item.isVeg ? '● Veg' : '● Non-Veg';
  $('#modalItemType').className = `item-modal__type ${item.isVeg ? 'veg' : 'nonveg'}`;
  $('#modalItemDescription').textContent = item.description || '';
  $('#modalItemPrice').textContent = `₹${item.price}`;

  // Estimate prep time based on real active kitchen load
  let activeKotsCount = 0;

  const prepBadge = $('#kitchenTrafficBadge');
  if (prepBadge) {
    let trafficText = 'Clear';
    let prepTime = 10;
    let badgeColor = '#10B981';
    let bgLight = 'rgba(16, 185, 129, 0.1)';

    if (activeKotsCount > 0 && activeKotsCount <= 2) {
      trafficText = 'Low Traffic';
      prepTime = 12;
      badgeColor = '#34D399';
    } else if (activeKotsCount > 2 && activeKotsCount <= 5) {
      trafficText = 'Moderate';
      prepTime = 18;
      badgeColor = '#F59E0B';
      bgLight = 'rgba(245, 158, 11, 0.12)';
    } else if (activeKotsCount > 5) {
      trafficText = 'Busy Kitchen';
      prepTime = 25;
      badgeColor = '#EF4444';
      bgLight = 'rgba(239, 68, 68, 0.12)';
    }

    prepBadge.innerHTML = `<span style="margin-right:6px;">⏱️</span> ~${prepTime} mins prep (${trafficText})`;
    prepBadge.style.color = badgeColor;
    prepBadge.style.background = bgLight;
    prepBadge.style.borderColor = badgeColor;
  }

  const modal = $('#itemModal');
  if (modal) modal.hidden = false;

  const addBtn = $('#modalAddToCartBtn');
  if (addBtn) {
    addBtn.dataset.modalItemId = item.id;
    if (item.available === false || item.is_available === false) {
      addBtn.disabled = true;
      addBtn.setAttribute('disabled', 'disabled');
      addBtn.textContent = '🚫 Currently Out of Stock (Sold Out)';
      addBtn.style.opacity = '0.6';
      addBtn.style.cursor = 'not-allowed';
      addBtn.style.background = '#EF4444';
      addBtn.style.pointerEvents = 'none';
    } else {
      addBtn.disabled = false;
      addBtn.removeAttribute('disabled');
      addBtn.textContent = '+ Add to Order Basket';
      addBtn.style.opacity = '1';
      addBtn.style.cursor = 'pointer';
      addBtn.style.background = 'linear-gradient(135deg, #10B981, #059669)';
      addBtn.style.pointerEvents = 'all';
    }
  }

  const closeBtn = $('#btnCloseModal') || $('#closeItemModal');
  if (closeBtn) closeBtn.focus();
}

function closeItemModal() { const m = $('#itemModal'); if (m) m.hidden = true; }

// Wire modal Add to Cart button
document.addEventListener('click', event => {
  const recommendedButton = event.target.closest('[data-recommended-item-id]');
  if (recommendedButton) {
    event.stopPropagation();
    const item = menuItems.find(i => String(i.id) === String(recommendedButton.dataset.recommendedItemId));
    if (item) {
      addToCart(item);
    }
    return;
  }

  const comboButton = event.target.closest('[data-combo-add-id]');
  if (comboButton) {
    const combo = COMBO_OFFERS.find(item => item.id === comboButton.dataset.comboAddId);
    if (combo) {
      addToCart({
        id: combo.id,
        baseId: combo.id,
        name: combo.name,
        description: combo.description,
        price: combo.price,
        quantity: 1,
        isCombo: true,
        includes: combo.includes
      });
      showToast(`${combo.name} added to cart`);
    }
    return;
  }

  const modalAddBtn = event.target.closest('#modalAddToCartBtn');
  if (modalAddBtn) {
    const itemId = modalAddBtn.dataset.modalItemId;
    const name = $('#modalItemName')?.textContent;
    const item = (itemId ? menuItems.find(i => String(i.id) === String(itemId)) : null) || menuItems.find(i => i.name === name);
    if (item) {
      if (item.available === false || item.is_available === false) {
        showToast(`Sorry, "${item.name}" is currently sold out!`, true);
        return;
      }
      addToCart({ ...item, quantity: 1 });
      closeItemModal();
    }
    return;
  }
});


// --- Cart helpers (local session-isolated with TTL) ---
const CART_TTL_MS = 30 * 60 * 1000; // 30 minutes max inactivity

function getCart() {
  try {
    let raw = sessionStorage.getItem(CART_STORAGE_KEY) || localStorage.getItem(CART_STORAGE_KEY);
    if (!raw) return [];

    const parsed = JSON.parse(raw);
    if (parsed && typeof parsed === 'object' && Array.isArray(parsed.items)) {
      if (parsed.timestamp && (Date.now() - parsed.timestamp > CART_TTL_MS)) {
        clearCustomerSessionCart();
        return [];
      }
      return parsed.items;
    }
    if (Array.isArray(parsed)) return parsed;
    return [];
  } catch {
    return [];
  }
}

function saveCart(cart) {
  const payload = {
    items: cart || [],
    timestamp: Date.now()
  };
  try {
    sessionStorage.setItem(CART_STORAGE_KEY, JSON.stringify(payload));
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(payload));
  } catch (e) {
    console.warn('Storage save failed:', e);
  }
}

function clearCustomerSessionCart() {
  try {
    sessionStorage.removeItem(CART_STORAGE_KEY);
    localStorage.removeItem(CART_STORAGE_KEY);
  } catch (e) {}
}
function addToCart(customizedItem) {
  if (!customizedItem) return;
  const masterItem = menuItems.find(i => 
    String(i.id) === String(customizedItem.baseId || customizedItem.id) || 
    (customizedItem.name && i.name && i.name.trim().toLowerCase() === customizedItem.name.trim().toLowerCase())
  );
  
  const isMasterSoldOut = masterItem ? (masterItem.available === false || masterItem.is_available === false || masterItem.is_available === 0 || masterItem.is_available === '0' || masterItem.status === 'out_of_stock') : false;
  const isCustomSoldOut = customizedItem.available === false || customizedItem.is_available === false || customizedItem.is_available === 0 || customizedItem.is_available === '0' || customizedItem.status === 'out_of_stock';

  if (isMasterSoldOut || isCustomSoldOut) {
    showToast(`🚫 Sorry, "${customizedItem.name || 'This item'}" is currently SOLD OUT and cannot be added!`, true);
    return;
  }

  const cart = getCart();
  const existingItemIndex = cart.findIndex(item => 
    String(item.id) === String(customizedItem.id) ||
    (item.baseId && customizedItem.baseId && String(item.baseId) === String(customizedItem.baseId)) ||
    (item.name && customizedItem.name && item.name.trim().toLowerCase() === customizedItem.name.trim().toLowerCase())
  );

  if (existingItemIndex > -1) {
    cart[existingItemIndex].quantity = (cart[existingItemIndex].quantity || 1) + (customizedItem.quantity || 1);
  } else {
    cart.push({ ...customizedItem, quantity: customizedItem.quantity || 1, available: true });
  }
  saveCart(cart);
  showToast(`🛒 Added ${customizedItem.name || 'Item'} to basket!`);
  renderCart();
  openCart();
}

// --- Cart UI and interactions ---
function formatCurrency(amount) { return `₹${Number(amount || 0).toFixed(2)}`; }

function addCookingChip(chipText) {
  const input = $('#cartSpecialInstructions');
  if (!input) return;
  if (input.value && !input.value.includes(chipText)) {
    input.value += ', ' + chipText;
  } else if (!input.value) {
    input.value = chipText;
  }
}

function renderCart() {
  const cart = getCart();
  const badge = $('#cartBadge');
  const list = $('#cartList');
  const subtotalEl = $('#cartSubtotal');
  const taxEl = $('#cartTax');
  const grandTotalEl = $('#cartGrandTotal');

  if (!badge || !list || !subtotalEl) return;
  
  const totalCount = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
  badge.textContent = totalCount;

  const headerCountEl = $('#cartHeaderItemCount');
  if (headerCountEl) {
    headerCountEl.textContent = `${totalCount} ${totalCount === 1 ? 'item' : 'items'}`;
  }

  if (!cart.length) {
    list.innerHTML = `
      <div class="cart-empty-state">
        <div class="cart-empty-icon">🛒</div>
        <div class="cart-empty-title">Your basket is empty</div>
        <div class="cart-empty-subtitle">Explore our delicious menu & add items to start ordering!</div>
      </div>
    `;
    subtotalEl.textContent = formatCurrency(0);
    if (taxEl) taxEl.textContent = formatCurrency(0);
    if (grandTotalEl) grandTotalEl.textContent = formatCurrency(0);
    const tray = $('#cartSmartUpsellTray');
    if (tray) tray.style.display = 'none';
    return;
  }

  let subtotal = 0;
  let hasSoldOutItem = false;
  list.innerHTML = cart.map((item, idx) => {
    const master = menuItems.find(i => String(i.id) === String(item.baseId || item.id) || (item.name && i.name.trim().toLowerCase() === item.name.trim().toLowerCase()));
    const isSoldOut = (master && (master.available === false || master.is_available === false || master.is_available === 0 || master.is_available === '0' || master.status === 'out_of_stock')) || item.available === false || item.is_available === false;
    if (isSoldOut) hasSoldOutItem = true;

    const name = escapeHtml(item.name);
    const price = Number(item.price || 0);
    const qty = item.quantity || 1;
    const itemTotal = price * qty;
    subtotal += itemTotal;

    const imageSrc = resolveImagePath(item.image || master?.image);
    const isVeg = item.is_veg ?? item.isVeg ?? master?.isVeg ?? true;

    return `
      <div class="cart-item-card ${isSoldOut ? 'sold-out-card' : ''}">
        <img class="cart-item-thumb" src="${escapeHtml(imageSrc)}" alt="${name}" loading="lazy" onerror="this.onerror=null;this.src='/images/placeholder-food.svg';">
        <div class="cart-item-details">
          <div class="cart-item-header-row">
            <span class="food-type ${isVeg ? 'veg' : 'nonveg'}" style="font-size:10px; padding:1px 5px; border-radius:4px;">${isVeg ? '● Veg' : '● Non-Veg'}</span>
            ${isSoldOut ? `<span class="cart-sold-out-badge">SOLD OUT</span>` : ''}
          </div>
          <div class="cart-item-name" title="${name}">
            <span>${name}</span>
          </div>
          <div class="cart-item-calc">
            <span class="cart-item-unit-price">${formatCurrency(price)}</span>
            <span class="cart-item-qty-x">× ${qty}</span>
            <span class="cart-item-calc-sep">=</span>
            <span class="cart-item-subtotal">${formatCurrency(itemTotal)}</span>
          </div>
        </div>
        <div class="cart-item-actions">
          <div class="cart-stepper-box">
            <button class="cart-stepper-btn" type="button" aria-label="Decrease quantity" onclick="changeCartQty(${idx}, ${qty - 1})">-</button>
            <span class="cart-stepper-val">${qty}</span>
            <button class="cart-stepper-btn" type="button" aria-label="Increase quantity" onclick="changeCartQty(${idx}, ${qty + 1})" ${isSoldOut ? 'disabled style="opacity:0.4;cursor:not-allowed;"' : ''}>+</button>
          </div>
          <button data-remove-idx="${idx}" class="cart-remove" type="button" title="Remove item from basket" onclick="changeCartQty(${idx}, 0)">✕</button>
        </div>
      </div>
    `;
  }).join('');

  const tax = subtotal * 0.05;
  const grandTotal = subtotal + tax;

  subtotalEl.textContent = formatCurrency(subtotal);
  if (taxEl) taxEl.textContent = formatCurrency(tax);
  if (grandTotalEl) grandTotalEl.textContent = formatCurrency(grandTotal);

  if (hasSoldOutItem) {
    const banner = `
      <div style="background:rgba(239,68,68,0.12); border:1.5px solid rgba(239,68,68,0.3); border-radius:14px; padding:12px 14px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center; gap:8px;">
        <div style="font-size:12.5px; color:#EF4444; font-weight:700; display:flex; align-items:center; gap:8px;">
          <span style="font-size:16px;">⚠️</span> <span>Some items in basket are Sold Out!</span>
        </div>
        <button type="button" onclick="removeSoldOutCartItems()" style="background:#EF4444; border:none; color:#fff; font-size:11.5px; font-weight:800; padding:6px 12px; border-radius:8px; cursor:pointer; white-space:nowrap; box-shadow:0 2px 8px rgba(239,68,68,0.4);">
          Remove Sold Out
        </button>
      </div>
    `;
    list.insertAdjacentHTML('afterbegin', banner);
  }

  renderCartSmartUpselling(cart);
}

function removeSoldOutCartItems() {
  const cart = getCart();
  const validCart = cart.filter(item => {
    const master = menuItems.find(i => String(i.id) === String(item.baseId || item.id) || (item.name && i.name.toLowerCase() === item.name.toLowerCase()));
    const isSoldOut = (master && (master.available === false || master.is_available === false || master.is_available === 0 || master.is_available === '0' || master.status === 'out_of_stock')) || item.available === false || item.is_available === false;
    return !isSoldOut;
  });
  saveCart(validCart);
  showToast('Removed sold-out items from basket');
  renderCart();
}

function renderCartSmartUpselling(cart) {
  const tray = $('#cartSmartUpsellTray');
  const container = $('#cartUpsellItemsList');
  if (!tray || !container) return;

  if (!cart.length || !menuItems.length) {
    tray.style.display = 'none';
    return;
  }

  const cartItemIds = new Set(cart.map(i => String(i.baseId || i.id)));
  const cartItemNames = new Set(cart.map(i => (i.name || '').toLowerCase().trim()));

  // Dynamic Smart Pairing: find complementary items not in cart
  const availableCandidates = menuItems.filter(item => 
    !cartItemIds.has(String(item.id)) &&
    !cartItemNames.has((item.name || '').toLowerCase().trim()) &&
    item.available !== false &&
    item.is_available !== false
  );

  if (!availableCandidates.length) {
    tray.style.display = 'none';
    return;
  }

  // Prioritize Drinks, Breads, Starters, Desserts
  const priorityCategories = ['drinks', 'beverage', 'breads', 'starters', 'desserts'];
  let suggestions = availableCandidates.filter(item => 
    priorityCategories.includes(String(item.category || '').toLowerCase())
  );

  if (suggestions.length < 4) {
    const remaining = availableCandidates.filter(item => !suggestions.includes(item));
    suggestions = suggestions.concat(remaining);
  }

  suggestions = suggestions.slice(0, 5);

  if (!suggestions.length) {
    tray.style.display = 'none';
    return;
  }

  container.innerHTML = suggestions.map(item => {
    const img = resolveImagePath(item.image);
    return `
      <div class="cart-upsell-card">
        <img src="${escapeHtml(img)}" alt="${escapeHtml(item.name)}" style="width:100%; height:62px; object-fit:cover; border-radius:10px; margin-bottom:6px;" onerror="this.onerror=null;this.src='/images/placeholder-food.svg';">
        <div style="font-size:11.5px; font-weight:800; color:var(--text-main, #0F172A); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
          <span style="font-size:11.5px; font-weight:800; color:#059669;">₹${Number(item.price || 0).toFixed(2)}</span>
          <button class="cart-upsell-btn" onclick="addUpsellItemToCart('${escapeHtml(item.id)}')" type="button">+ Add</button>
        </div>
      </div>
    `;
  }).join('');

  tray.style.display = 'block';
}

function addUpsellItemToCart(itemId) {
  const item = menuItems.find(i => String(i.id) === String(itemId));
  if (item) {
    if (item.available === false || item.is_available === false || item.is_available === 0 || item.is_available === '0' || item.status === 'out_of_stock') {
      showToast(`🚫 Sorry, "${item.name}" is currently Sold Out!`, true);
      return;
    }
    addToCart(item);

    // Smoothly highlight the newly added item in the basket list
    setTimeout(() => {
      const cartList = $('#cartList');
      if (cartList && cartList.lastElementChild) {
        cartList.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        cartList.lastElementChild.classList.add('cart-item-highlight');
        setTimeout(() => cartList.lastElementChild?.classList.remove('cart-item-highlight'), 900);
      }
    }, 80);
  }
}

function changeCartQty(idx, newQty) {
  const cart = getCart();
  if (!cart[idx]) return;
  if (newQty <= 0) {
    cart.splice(idx, 1);
    showToast('Removed from cart');
  } else {
    const item = cart[idx];
    const master = menuItems.find(i => String(i.id) === String(item.baseId || item.id) || (item.name && i.name.toLowerCase() === item.name.toLowerCase()));
    if (master && (master.available === false || master.is_available === false || master.is_available === 0 || master.is_available === '0')) {
      showToast(`🚫 Cannot increase quantity: "${item.name}" is Sold Out!`, true);
      return;
    }
    cart[idx].quantity = newQty;
  }
  saveCart(cart);
  renderCart();
}

let selectedCartPayMethod = 'cash';

function selectCartPayMethod(method) {
  selectedCartPayMethod = method;
  const cashLabel = $('#payMethodCashLabel');
  const upiLabel = $('#payMethodUpiLabel');
  const cashRadio = cashLabel?.querySelector('input[type="radio"]');
  const upiRadio = upiLabel?.querySelector('input[type="radio"]');
  
  if (cashLabel && upiLabel) {
    cashLabel.classList.toggle('active', method === 'cash');
    upiLabel.classList.toggle('active', method === 'upi');
    if (cashRadio) cashRadio.checked = (method === 'cash');
    if (upiRadio) upiRadio.checked = (method === 'upi');
  }
}

function openCart() {
  const p = $('#cartPanel');
  if (p) {
    p.hidden = false;
    renderCart();
    const scrollArea = p.querySelector('.cart-scroll-area');
    if (scrollArea) scrollArea.scrollTop = 0;
  }
}
function closeCart() { const p = $('#cartPanel'); if (p) p.hidden = true; }

async function handleCheckout() {
  const cart = getCart();
  if (!cart || !cart.length) {
    showToast('Your cart is empty! Add items first.', true);
    return;
  }

  // 🚫 Real-time Sold-out / Out of stock validation
  const soldOutItems = cart.filter(cartItem => {
    const master = menuItems.find(i => String(i.id) === String(cartItem.baseId || cartItem.id));
    return (master && master.available === false) || cartItem.available === false;
  });

  if (soldOutItems.length > 0) {
    const names = soldOutItems.map(i => i.name).join(', ');
    showToast(`⚠️ Cannot place order: "${names}" is currently sold out. Please remove it from basket.`, true);
    renderCart();
    return;
  }

  const checkoutBtn = $('#checkoutBtn');
  if (checkoutBtn) {
    checkoutBtn.disabled = true;
    checkoutBtn.textContent = 'Placing Order...';
  }

  const customNotes = $('#cartSpecialInstructions') ? $('#cartSpecialInstructions').value.trim() : '';
  const itemNotes = cart.map(i => i.specialInstruction).filter(Boolean).join('; ');
  const combinedInstructions = [customNotes, itemNotes].filter(Boolean).join(' | ');

  const payload = {
    restaurant_id: String(restaurantId),
    restaurantId: String(restaurantId),
    table_id: String(tableId),
    tableId: String(tableId),
    payment_method: selectedCartPayMethod || 'cash',
    paymentMethod: selectedCartPayMethod || 'cash',
    special_instructions: combinedInstructions,
    specialInstructions: combinedInstructions,
    items: cart.map(item => ({
      menu_item_id: item.baseId || item.id,
      menuItemId: item.baseId || item.id,
      name: item.name,
      price: Number(item.price) || 0,
      quantity: Number(item.quantity) || 1
    }))
  };

  try {
    const res = await fetch(`${API_BASE}/orders`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success && data.data) {
      const orderId = data.data.id || data.data._id;
      const amount = data.data.total_amount ?? data.data.totalAmount ?? 0;
      
      // 1. Clear cart completely
      saveCart([]);
      clearCustomerSessionCart();
      renderCart();
      closeCart();

      // 2. Clear cooking notes & special instructions
      const notesInput = $('#cartSpecialInstructions');
      if (notesInput) notesInput.value = '';
      const chips = document.querySelectorAll('.cooking-chip.active');
      chips.forEach(c => c.classList.remove('active'));

      // 3. Reset selected coupon
      selectedCoupon = null;

      // 4. Save session order with timestamp
      sessionStorage.setItem(`dineflow_session_order_${restaurantId}_${tableId}`, orderId);
      sessionStorage.setItem(`dineflow_session_order_time_${restaurantId}_${tableId}`, Date.now());
      localStorage.setItem(`dineflow_session_order_${restaurantId}_${tableId}`, orderId);
      localStorage.setItem(`dineflow_session_order_time_${restaurantId}_${tableId}`, Date.now());

      if (selectedCartPayMethod === 'upi') {
        openCustomerUpiModal(orderId, amount);
      } else {
        showToast(`✅ Order #${orderId} placed successfully! Sent to Kitchen KDS.`, false);
        startLiveOrderTracking(orderId);
      }
    } else {
      showToast(data.message || 'Failed to place order', true);
    }
  } catch (err) {
    console.error('Checkout error:', err);
    showToast('Error connecting to backend API', true);
  } finally {
    if (checkoutBtn) {
      checkoutBtn.disabled = false;
      checkoutBtn.textContent = '🚀 Confirm & Place Order';
    }
  }
}

let currentCustomerUpiOrderId = null;

function getEffectiveCustomerUpiConfig() {
  const rest = window.restaurant || {};
  const isDemo = Boolean(rest.is_demo || rest.demo_session_id || String(restaurantId) === '1');
  const customQr = rest.upi_qr || rest.settings?.upi_qr || null;
  const customVpa = rest.upi_vpa || rest.settings?.upi_vpa || null;
  const demoVpa = '7777938338@omni';

  if (!isDemo) {
    if (customQr) {
      return { type: 'static_image', qrUrl: resolveImagePath(customQr), vpa: customVpa, name: rest.name || 'Restaurant' };
    }
    if (customVpa) {
      return { type: 'vpa', vpa: customVpa, name: rest.name || 'Restaurant' };
    }
    return { type: 'unconfigured', message: 'UPI QR is not configured for this restaurant. Please pay at billing counter.' };
  }

  // Demo restaurant fallback
  if (customQr) {
    return { type: 'static_image', qrUrl: resolveImagePath(customQr), vpa: customVpa || demoVpa, name: rest.name || 'DineFlow AI Restaurant' };
  }
  return { type: 'vpa', vpa: customVpa || demoVpa, name: rest.name || 'DineFlow AI Restaurant' };
}

function openCustomerUpiModal(orderId, amount) {
  currentCustomerUpiOrderId = orderId;
  const modal = $('#customerUpiModal');
  const amountEl = $('#customerUpiAmount');
  const qrImg = $('#customerUpiQrImg');
  const deepLink = $('#customerUpiDeepLink');

  const formattedAmount = Number(amount || 0).toFixed(2);
  if (amountEl) amountEl.textContent = `₹${formattedAmount}`;

  const config = getEffectiveCustomerUpiConfig();

  if (config.type === 'unconfigured') {
    if (qrImg) {
      qrImg.style.display = 'none';
      const parent = qrImg.parentElement;
      if (parent) {
        parent.innerHTML = `
          <div style="padding:20px 16px;text-align:center;">
            <p style="font-size:28px;margin:0 0 8px;">⚠️</p>
            <p style="color:#F59E0B;font-weight:700;font-size:14px;margin:0 0 6px;">Payment QR Not Configured</p>
            <p style="color:#94A3B8;font-size:12px;margin:0;">${config.message}</p>
          </div>`;
      }
    }
    if (deepLink) deepLink.style.display = 'none';
  } else if (config.type === 'static_image') {
    if (qrImg) {
      qrImg.style.display = 'block';
      qrImg.src = config.qrUrl;
    }
    if (deepLink) {
      if (config.vpa) {
        const upiUrl = `upi://pay?pa=${encodeURIComponent(config.vpa)}&pn=${encodeURIComponent(config.name)}&am=${formattedAmount}&cu=INR&tn=${encodeURIComponent('Order #' + orderId)}`;
        deepLink.href = upiUrl;
        deepLink.style.display = 'inline-flex';
      } else {
        deepLink.style.display = 'none';
      }
    }
  } else {
    // Dynamic generated QR via VPA
    const upiUrl = `upi://pay?pa=${encodeURIComponent(config.vpa)}&pn=${encodeURIComponent(config.name)}&am=${formattedAmount}&cu=INR&tn=${encodeURIComponent('Order #' + orderId)}`;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(upiUrl)}`;
    if (qrImg) {
      qrImg.style.display = 'block';
      qrImg.src = qrUrl;
      qrImg.onerror = () => {
        const parent = qrImg.parentElement;
        if (parent) {
          parent.innerHTML = `
            <div style="padding:16px;text-align:center;">
              <p style="color:#818CF8;font-weight:700;font-size:13px;margin:0 0 4px;">Pay via UPI ID</p>
              <p style="color:#34D399;font-size:16px;font-weight:800;margin:0;">${config.vpa}</p>
              <p style="opacity:0.6;font-size:11px;margin:6px 0 0;">Amount: ₹${formattedAmount}</p>
            </div>`;
        }
      };
    }
    if (deepLink) {
      deepLink.href = upiUrl;
      deepLink.style.display = 'inline-flex';
    }
  }

  if (modal) modal.hidden = false;
}

function closeCustomerUpiModal() {
  const modal = $('#customerUpiModal');
  if (modal) modal.hidden = true;
  if (currentCustomerUpiOrderId) {
    startLiveOrderTracking(currentCustomerUpiOrderId);
  }
}

async function confirmCustomerUpiPaid() {
  if (!currentCustomerUpiOrderId) {
    closeCustomerUpiModal();
    return;
  }

  try {
    const res = await fetch(`${API_BASE}/orders/${currentCustomerUpiOrderId}/pay`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ payment_method: 'upi' })
    });
    if (!res.ok) throw new Error('Payment failed');
    showToast(`🎉 Order #${currentCustomerUpiOrderId} Paid! Voice alert sent to POS Soundbox.`, false);
  } catch(e) {
    console.error('UPI pay error:', e);
    showToast(`Order #${currentCustomerUpiOrderId} submitted!`, false);
  }

  closeCustomerUpiModal();
}

let liveOrderPollInterval = null;

async function checkLiveOrderStatus(orderId) {
  try {
    const res = await fetch(`${API_BASE}/orders/${orderId}`);
    if (!res.ok) {
      if (liveOrderPollInterval) { clearInterval(liveOrderPollInterval); liveOrderPollInterval = null; }
      return;
    }
    const data = await res.json();
    if (data.success && data.data) {
      const status = (data.data.status || '').toLowerCase();
      if (status === 'cancelled') {
        if (liveOrderPollInterval) { clearInterval(liveOrderPollInterval); liveOrderPollInterval = null; }
        const tracker = $('#liveOrderTracker');
        if (tracker) tracker.hidden = true;
        localStorage.removeItem(`dineflow_session_order_${restaurantId}_${tableId}`);
        showToast('Order was cancelled by restaurant.', true);
        return;
      }
      updateLiveOrderTrackerUI(data.data);
    }
  } catch (err) {
    console.log('Error polling order status:', err);
  }
}

function updateLiveOrderTrackerUI(order) {
  const tracker = $('#liveOrderTracker');
  if (!tracker) return;
  tracker.hidden = false;
  tracker.style.setProperty('display', 'block', 'important');

  const badge = $('#trackerOrderBadge');
  if (badge) badge.textContent = `#${order.id}`;

  const status = (order.status || 'pending').toLowerCase();

  const step1 = $('#step1');
  const step2 = $('#step2');
  const step3 = $('#step3');
  const stepIcon1 = $('#stepIcon1');
  const stepIcon2 = $('#stepIcon2');
  const stepIcon3 = $('#stepIcon3');
  const fill = $('#progressFill');
  const statusMsg = $('#trackerStatusMsg');

  // Reset any previous inline colors
  if (stepIcon1) { stepIcon1.style.background = ''; stepIcon1.style.color = ''; }
  if (stepIcon2) { stepIcon2.style.background = ''; stepIcon2.style.color = ''; }
  if (stepIcon3) { stepIcon3.style.background = ''; stepIcon3.style.color = ''; }

  if (status === 'pending') {
    if (fill) fill.style.width = '0%';
    step1?.classList.add('active');
    step1?.classList.remove('completed');
    step2?.classList.remove('active', 'completed');
    step3?.classList.remove('active', 'completed');
    if (stepIcon1) stepIcon1.innerHTML = '1';
    if (stepIcon2) stepIcon2.innerHTML = '2';
    if (stepIcon3) stepIcon3.innerHTML = '3';
    if (statusMsg) statusMsg.innerHTML = '<span>🟡</span> <span>Order received! Sent to kitchen chef.</span>';
  } else if (status === 'preparing') {
    if (fill) fill.style.width = '50%';
    step1?.classList.remove('active');
    step1?.classList.add('completed');
    step2?.classList.add('active');
    step2?.classList.remove('completed');
    step3?.classList.remove('active', 'completed');
    if (stepIcon1) stepIcon1.innerHTML = '✓';
    if (stepIcon2) stepIcon2.innerHTML = '2';
    if (stepIcon3) stepIcon3.innerHTML = '3';
    if (statusMsg) statusMsg.innerHTML = '<span>🔥</span> <span>Chef is cooking your delicious meal in the kitchen!</span>';
  } else if (status === 'served' || status === 'completed') {
    if (fill) fill.style.width = '100%';
    step1?.classList.remove('active');
    step1?.classList.add('completed');
    step2?.classList.remove('active');
    step2?.classList.add('completed');
    step3?.classList.remove('active');
    step3?.classList.add('completed');
    if (stepIcon1) stepIcon1.innerHTML = '✓';
    if (stepIcon2) stepIcon2.innerHTML = '✓';
    if (stepIcon3) stepIcon3.innerHTML = '✓';
    if (statusMsg) statusMsg.innerHTML = '<span>🎉</span> <span>Food is ready & served at your table! Enjoy your meal!</span>';

    if (liveOrderPollInterval) {
      clearInterval(liveOrderPollInterval);
      liveOrderPollInterval = null;
    }

    // Auto-dismiss/hide tracker banner after 8 seconds when served/completed
    setTimeout(() => {
      hideLiveOrderTracker();
      localStorage.removeItem(`dineflow_session_order_${restaurantId}_${tableId}`);
    }, 8000);
  }
}

function hideLiveOrderTracker() {
  const tracker = $('#liveOrderTracker');
  if (tracker) {
    tracker.style.opacity = '0';
    tracker.style.transition = 'opacity 0.4s ease';
    setTimeout(() => {
      tracker.style.setProperty('display', 'none', 'important');
      tracker.hidden = true;
      tracker.style.opacity = '1';
    }, 400);
  }
  if (liveOrderPollInterval) {
    clearInterval(liveOrderPollInterval);
    liveOrderPollInterval = null;
  }
}

function startLiveOrderTracking(orderId) {
  if (liveOrderPollInterval) clearInterval(liveOrderPollInterval);
  checkLiveOrderStatus(orderId);
  liveOrderPollInterval = setInterval(() => checkLiveOrderStatus(orderId), 4000);
}

// Event delegation for cart interactions
document.addEventListener('click', event => {
  if (event.target.closest('#floatingCartBtn')) { openCart(); return; }
  if (event.target.closest('[data-close-cart]')) { closeCart(); return; }
  if (event.target.closest('#btnCloseCart')) { closeCart(); return; }
  if (event.target.closest('#clearCartBtn')) { saveCart([]); renderCart(); showToast('Cart cleared'); return; }
  if (event.target.closest('#checkoutBtn')) { handleCheckout(); return; }
  const removeBtn = event.target.closest('[data-remove-idx]');
  if (removeBtn) {
    const idx = Number(removeBtn.dataset.removeIdx);
    const cart = getCart();
    if (cart[idx]) { cart.splice(idx,1); saveCart(cart); renderCart(); showToast('Removed from cart'); }
  }
});

// Quick test helper
window.__printCart = function() { console.log('Cart contents:', getCart()); };

// Initialize cart UI on load
document.addEventListener('DOMContentLoaded', () => renderCart());


function showToast(message, isError = false) { const toast = $('#toast'); if (!toast) return; toast.textContent = message; toast.className = `toast show ${isError ? 'error' : ''}`; window.clearTimeout(showToast.timer); showToast.timer = window.setTimeout(() => { toast.className = 'toast'; }, 3500); }

async function loadMenu() {
  const initialItems = getStaticFoodItemsList();
  if (initialItems.length > 0) {
    menuItems = initialItems.map(normalizeItem);
    renderCategories();
    renderMenu();
  }

  setTableOptions();
  checkTableActiveOrder();

  try {
    const [restaurantResult, tableResult, menuResult, tablesResult] = await Promise.allSettled([
      request(`/restaurants/${encodeURIComponent(restaurantId)}`),
      request(`/tables/${encodeURIComponent(tableId)}`),
      request(`/menu?restaurantId=${encodeURIComponent(restaurantId)}`),
      request(`/tables/restaurant/${encodeURIComponent(restaurantId)}`)
    ]);
    if (restaurantResult.status === 'fulfilled' && restaurantResult.value) {
      const rData = restaurantResult.value;
      window.restaurant = rData;
      const restName = rData.name || 'Restaurant';
      const nameEl = $('#restaurantName'); if (nameEl) nameEl.textContent = restName;
      const titleEl = $('#restaurantMainTitle'); if (titleEl) titleEl.textContent = restName;
      document.title = `${restName} | Digital Menu`;

      const locEl = $('#restaurantLocation'); if (locEl && (rData.address || rData.location)) locEl.textContent = rData.address || rData.location;
      
      const logoEl = $('#restaurantLogo');
      const monogramEl = $('#restaurantMonogram');
      if (rData.logo) {
        if (logoEl) {
          logoEl.src = resolveImagePath(rData.logo);
          logoEl.style.display = 'block';
        }
        if (monogramEl) monogramEl.style.display = 'none';
      } else {
        if (logoEl) logoEl.style.display = 'none';
        if (monogramEl) {
          monogramEl.style.display = 'flex';
          monogramEl.textContent = restName.charAt(0).toUpperCase() || 'R';
        }
      }
    }
    const restaurantTables = tablesResult.status === 'fulfilled' && Array.isArray(tablesResult.value) ? tablesResult.value : [];
    setTableOptions(restaurantTables);
    if (tableResult.status === 'fulfilled' && tableResult.value) {
      const tData = tableResult.value;
      const number = tData.table_number || tData.tableNumber || tableId;
      const tSelect = $('#tableNumber');
      if (tSelect && tSelect.options[tSelect.selectedIndex]) {
        tSelect.options[tSelect.selectedIndex].text = `#${number}`;
      }
    }
    if (menuResult.status === 'fulfilled' && Array.isArray(menuResult.value) && menuResult.value.length > 0) {
      const apiRawItems = menuResult.value;
      const staticFoodItems = getStaticFoodItemsList();
      menuItems = apiRawItems.map(apiItem => {
        const match = staticFoodItems.find(s => s.name.toLowerCase() === apiItem.name.toLowerCase() || String(s.id) === String(apiItem.id));
        const merged = match ? { ...match, ...apiItem, image: apiItem.image || match.image } : { ...apiItem };
        const isApiAvail = parseBoolean(apiItem.is_available ?? apiItem.available, true);
        merged.is_available = isApiAvail;
        merged.available = isApiAvail;
        if (isApiAvail && merged.badge && (merged.badge.toLowerCase().includes('sold') || merged.badge.toLowerCase().includes('stock'))) {
          merged.badge = match?.badge && !match.badge.toLowerCase().includes('sold') && !match.badge.toLowerCase().includes('stock') ? match.badge : 'Special';
        }
        return normalizeItem(merged);
      });
      renderCategories();
      renderMenu();
    }
  } catch (_) {}
}

async function checkTableActiveOrder() {
  if (liveOrderPollInterval) {
    clearInterval(liveOrderPollInterval);
    liveOrderPollInterval = null;
  }

  const tracker = $('#liveOrderTracker');
  if (tracker) {
    tracker.hidden = true;
    tracker.style.setProperty('display', 'none', 'important');
  }

  // 30-minute TTL for order tracking
  const ORDER_TTL_MS = 30 * 60 * 1000;
  const sessionPlacedOrderId = sessionStorage.getItem(`dineflow_session_order_${restaurantId}_${tableId}`) || localStorage.getItem(`dineflow_session_order_${restaurantId}_${tableId}`);
  const sessionOrderTime = parseInt(sessionStorage.getItem(`dineflow_session_order_time_${restaurantId}_${tableId}`) || localStorage.getItem(`dineflow_session_order_time_${restaurantId}_${tableId}`) || '0', 10);

  if (!sessionPlacedOrderId) return;

  if (sessionOrderTime && (Date.now() - sessionOrderTime > ORDER_TTL_MS)) {
    clearCustomerSessionOrder();
    return;
  }

  try {
    const res = await fetch(`${API_BASE}/orders/${sessionPlacedOrderId}`);
    const data = await res.json();
    if (data.success && data.data) {
      const order = data.data;
      if (order.status !== 'completed' && order.status !== 'served' && order.status !== 'cancelled') {
        startLiveOrderTracking(order.id);
      } else {
        clearCustomerSessionOrder();
      }
    } else {
      clearCustomerSessionOrder();
    }
  } catch (err) {
    console.log('Error checking table active order:', err);
  }
}

function clearCustomerSessionOrder() {
  try {
    sessionStorage.removeItem(`dineflow_session_order_${restaurantId}_${tableId}`);
    sessionStorage.removeItem(`dineflow_session_order_time_${restaurantId}_${tableId}`);
    localStorage.removeItem(`dineflow_session_order_${restaurantId}_${tableId}`);
    localStorage.removeItem(`dineflow_session_order_time_${restaurantId}_${tableId}`);
  } catch (e) {}
}

function highlightSuggestion(value, query) {
  const safeValue = escapeHtml(value);
  const safeQuery = escapeHtml(query).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return safeQuery ? safeValue.replace(new RegExp(`(${safeQuery})`, 'ig'), '<mark>$1</mark>') : safeValue;
}

function renderSuggestions() {
  const input = $('#searchInput');
  const container = $('#searchSuggestions');
  if (!input || !container) return;
  const query = input.value.trim();
  if (!query) { container.hidden = true; container.innerHTML = ''; activeSuggestionIndex = -1; return; }
  const suggestions = menuItems.filter(item => `${item.name} ${item.category}`.toLowerCase().includes(query.toLowerCase())).slice(0, 6);
  container.innerHTML = suggestions.map((item, index) => `<button type="button" class="search-suggestion ${index === activeSuggestionIndex ? 'is-active' : ''}" role="option" aria-selected="${index === activeSuggestionIndex}" data-suggestion-id="${escapeHtml(item.id)}"><span>${highlightSuggestion(item.name, query)}</span><small>${escapeHtml(item.category || '')}</small></button>`).join('');
  container.hidden = !suggestions.length;
}

function selectSuggestion(itemId) {
  const item = menuItems.find(candidate => String(candidate.id) === String(itemId));
  if (!item) return;
  $('#searchInput').value = item.name;
  $('#searchSuggestions').hidden = true;
  activeSuggestionIndex = -1;
  renderMenu();
}

function applyTheme(theme) {
  const selectedTheme = theme === 'light' ? 'light' : 'dark';
  const isLight = selectedTheme === 'light';
  document.body.classList.toggle('light-theme', isLight);
  document.body.classList.toggle('dark-theme', !isLight);
  const toggle = $('#themeToggle');
  if (toggle) {
    toggle.setAttribute('aria-pressed', String(!isLight));
    toggle.setAttribute('aria-label', isLight ? 'Enable dark mode' : 'Enable light mode');
    toggle.innerHTML = isLight ? '<span>🌙</span> <span>Dark mode</span>' : '<span>☀️</span> <span>Light mode</span>';
  }
  localStorage.setItem(THEME_STORAGE_KEY, selectedTheme);
  localStorage.setItem('posTheme', selectedTheme);
  localStorage.setItem('dineflow_theme', selectedTheme);
}

// --- Safe event listener bindings (null-guarded) ---
const _searchInput = $('#searchInput');
const _searchSuggestions = $('#searchSuggestions');
const _themeToggle = $('#themeToggle');
const _sortSelect = $('#sortSelect');
const _languageSelect = $('#languageSelect');
const _tableNumber = $('#tableNumber');
const _categorySection = $('#categorySection');

if (_searchInput) {
  _searchInput.addEventListener('input', () => { activeSuggestionIndex = -1; renderSuggestions(); renderMenu(); });
  _searchInput.addEventListener('keydown', event => {
    const suggestions = [...document.querySelectorAll('.search-suggestion')];
    if (!suggestions.length) return;
    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') { event.preventDefault(); activeSuggestionIndex = event.key === 'ArrowDown' ? (activeSuggestionIndex + 1) % suggestions.length : (activeSuggestionIndex - 1 + suggestions.length) % suggestions.length; renderSuggestions(); }
    if (event.key === 'Enter' && activeSuggestionIndex >= 0) { event.preventDefault(); selectSuggestion(suggestions[activeSuggestionIndex].dataset.suggestionId); }
    if (event.key === 'Escape' && _searchSuggestions) _searchSuggestions.hidden = true;
  });
}
if (_searchSuggestions) _searchSuggestions.addEventListener('click', event => { const suggestion = event.target.closest('[data-suggestion-id]'); if (suggestion) selectSuggestion(suggestion.dataset.suggestionId); });
document.addEventListener('click', event => {
  if (!event.target.closest('.search-wrapper') && _searchSuggestions) _searchSuggestions.hidden = true;

  // 1. Diet Button Click
  const dietBtn = event.target.closest('.diet-btn');
  if (dietBtn) {
    event.preventDefault();
    diet = dietBtn.dataset.diet || 'all';
    document.querySelectorAll('.diet-btn').forEach(b => {
      b.classList.toggle('active', (b.dataset.diet || 'all') === diet);
    });
    renderMenu();
    return;
  }

  // 2. Quick Filter Button Click
  const filterBtn = event.target.closest('.filter-btn');
  if (filterBtn) {
    event.preventDefault();
    const filter = filterBtn.dataset.filter;
    if (filter) {
      if (activeFilters.has(filter)) {
        activeFilters.delete(filter);
        filterBtn.classList.remove('is-active', 'active');
      } else {
        activeFilters.add(filter);
        filterBtn.classList.add('is-active', 'active');
      }
      renderMenu();
    }
    return;
  }

  // 3. Category Button Click
  const catBtn = event.target.closest('.category-btn');
  if (catBtn) {
    event.preventDefault();
    category = catBtn.dataset.category || 'All';
    renderCategories();
    renderMenu();
    return;
  }
});

if (_themeToggle) _themeToggle.addEventListener('click', () => applyTheme(document.body.classList.contains('light-theme') ? 'dark' : 'light'));
if (_sortSelect) _sortSelect.addEventListener('change', renderMenu);
if (_languageSelect) _languageSelect.addEventListener('change', event => { language = event.target.value; localStorage.setItem('dineflow-language', language); applyLanguage(); });
if (_tableNumber) _tableNumber.addEventListener('change', event => { tableId = event.target.value; const nextUrl = new URL(window.location.href); nextUrl.searchParams.set('tableId', tableId); window.history.replaceState({}, '', nextUrl); loadMenu(); });
const _foodGrid = $('#foodGrid');
if (_foodGrid) _foodGrid.addEventListener('click', event => {
  const favoriteButton = event.target.closest('[data-favorite-toggle]');
  if (favoriteButton) {
    event.stopPropagation();
    toggleFavorite(favoriteButton.dataset.favoriteToggle);
    return;
  }

  const addCartBtn = event.target.closest('[data-add-cart]');
  if (addCartBtn) {
    event.stopPropagation();
    const item = menuItems.find(i => String(i.id) === String(addCartBtn.dataset.addCart));
    if (item) {
      if (item.available === false || item.is_available === false) {
        showToast(`Sorry, "${item.name}" is currently sold out!`, true);
        return;
      }
      addToCart(item);
    }
    return;
  }

  const card = event.target.closest('[data-item-id]');
  if (card) {
    const item = menuItems.find(item => String(item.id) === card.dataset.itemId);
    if (item) {
      if (item.available === false || item.is_available === false) {
        showToast(`Sorry, "${item.name}" is currently sold out and out of stock!`, true);
        return;
      }
      openItemModal(item);
    }
  }
});

const _favSection = $('#favoritesSection');
if (_favSection) _favSection.addEventListener('click', event => {
  const removeButton = event.target.closest('[data-favorite-remove-id]');
  if (removeButton) {
    event.stopPropagation();
    removeFavorite(removeButton.dataset.favoriteRemoveId);
    return;
  }
  const chip = event.target.closest('[data-favorite-chip-id]');
  if (chip) {
    const item = menuItems.find(entry => String(entry.id) === chip.dataset.favoriteChipId);
    if (item) {
      if (item.available === false || item.is_available === false) {
        showToast(`Sorry, "${item.name}" is currently sold out!`, true);
        return;
      }
      openItemModal(item);
    }
  }
});

const _recentSection = $('#recentSection');
if (_recentSection) _recentSection.addEventListener('click', event => {
  if (event.target.closest('[data-clear-recent]')) {
    clearRecentlyViewed();
    return;
  }
  const recentButton = event.target.closest('[data-recent-item-id]');
  if (!recentButton) return;
  const item = menuItems.find(entry => String(entry.id) === recentButton.dataset.recentItemId);
  if (item) {
    if (item.available === false || item.is_available === false) {
      showToast(`🚫 Sorry, "${item.name}" is currently Sold Out!`, true);
      return;
    }
    openItemModal(item);
  }
});
if (_foodGrid) _foodGrid.addEventListener('keydown', event => { 
  if (event.key === 'Enter' || event.key === ' ') { 
    const card = event.target.closest('[data-item-id]'); 
    if (card) { 
      event.preventDefault(); 
      const item = menuItems.find(item => String(item.id) === card.dataset.itemId);
      if (item) {
        if (item.available === false || item.is_available === false) {
          showToast(`🚫 Sorry, "${item.name}" is currently Sold Out!`, true);
          return;
        }
        openItemModal(item);
      }
    } 
  } 
});

const btnCloseModal = $('#btnCloseModal') || $('#closeItemModal');
if (btnCloseModal) btnCloseModal.addEventListener('click', closeItemModal);

const itemModalEl = $('#itemModal');
if (itemModalEl) itemModalEl.addEventListener('click', event => { if (event.target.hasAttribute('data-close-modal')) closeItemModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeItemModal(); });

function openWaiterModal() {
  const modal = $('#waiterModal');
  if (modal) modal.hidden = false;
}

function closeWaiterModal() {
  const modal = $('#waiterModal');
  if (modal) modal.hidden = true;
}

async function sendWaiterRequest(reason) {
  closeWaiterModal();
  try {
    await request('/waiter-calls', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        restaurant_id: String(restaurantId),
        table_id: String(tableId),
        reason: reason || 'General Assistance'
      })
    });
    showToast(`🔔 Waiter notified for "${reason}"!`, false);
  } catch (err) {
    showToast(getText('waiterError'), true);
  }
}

const callWaiterButton = $('#callWaiterBtn');
if (callWaiterButton) {
  callWaiterButton.addEventListener('click', () => {
    openWaiterModal();
  });
}

let tableUnpaidItems = [];
let currentSplitPeopleCount = 2;

function changeSplitPeople(delta) {
  currentSplitPeopleCount = Math.max(1, Math.min(10, currentSplitPeopleCount + delta));
  const el = $('#splitPeopleCount');
  if (el) el.textContent = currentSplitPeopleCount;
  recalculateSplit();
}

async function openSplitBillModal() {
  const setEl = (id, val) => { const el = $(id); if (el) el.textContent = val; };
  const upiSec = $('#splitUpiSection');
  if (upiSec) upiSec.style.display = 'block';
  const listEl = $('#splitBillItemList') || $('#splitBillItemsList');
  const qrImg = $('#splitUpiQrImg');
  if (qrImg) qrImg.src = '';
  setEl('#splitSubtotal', '₹0.00');
  setEl('#splitTax', '₹0.00');
  setEl('#splitTotal', '₹0.00');

  function renderSplitItems(items, source) {
    tableUnpaidItems = items;
    if (listEl) {
      listEl.innerHTML = items.map((item, idx) => `
        <label style="display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); padding: 10px 12px; border-radius: 8px; cursor: pointer;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" class="split-item-chk" value="${idx}" onchange="recalculateSplit()" style="width: 16px; height: 16px; accent-color: #6366F1;">
            <div style="text-align: left; font-size: 13px;">
              <div class="split-item-name" style="font-weight: 700; color: var(--text-main);">${escapeHtml(item.name || item.menu_item?.name || 'Item')}</div>
              <div style="font-size: 10px; opacity: 0.6; color: var(--text-muted);">₹${Number(item.price || 0).toFixed(2)} each</div>
            </div>
          </div>
          <div class="split-item-qty" style="font-size: 12px; font-weight: 700; color: var(--text-main);">
            Qty: <select class="split-item-qty-select" onchange="recalculateSplit()" style="background: var(--input-bg); border: 1px solid var(--input-border); color: var(--input-text); border-radius: 4px; padding: 2px 4px; font-size: 11px;">
              ${Array.from({ length: item.quantity || 1 }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
            </select>
          </div>
        </label>
      `).join('');
    }
  }

  // Try session order first (saved when customer placed order)
  const sessionOrderId = localStorage.getItem(`dineflow_session_order_${restaurantId}_${tableId}`);
  if (sessionOrderId) {
    try {
      const res = await fetch(`${API_BASE}/orders/${sessionOrderId}`);
      if (res.ok) {
        const data = await res.json();
        const order = data.data || data;
        if (order && order.payment_status === 'pending' && Array.isArray(order.items) && order.items.length > 0) {
          renderSplitItems(order.items, 'order');
          const modal = $('#splitBillModal');
          if (modal) modal.hidden = false;
          // Auto-check all items by default for equal split
          setTimeout(() => {
            document.querySelectorAll('.split-item-chk').forEach(chk => { chk.checked = true; });
            recalculateSplit();
          }, 100);
          return;
        }
      }
    } catch (err) {
      console.log('Session order fetch failed:', err);
    }
  }

  // Fallback: use cart items
  const cart = getCart();
  if (cart && cart.length > 0) {
    renderSplitItems(cart, 'cart');
    // Auto-check all items by default
    setTimeout(() => {
      document.querySelectorAll('.split-item-chk').forEach(chk => { chk.checked = true; });
      recalculateSplit();
    }, 100);
  } else {
    tableUnpaidItems = [];
    if (listEl) {
      listEl.innerHTML = `<div style="text-align: center; padding: 20px 10px; opacity: 0.6; font-size: 13px;"><p>No items found. Add items to your cart first!</p></div>`;
    }
    if (upiSec) upiSec.style.display = 'none';
  }

  const modal = $('#splitBillModal');
  if (modal) modal.hidden = false;
}

function closeSplitBillModal() {
  const modal = $('#splitBillModal');
  if (modal) modal.hidden = true;
}

function recalculateSplit() {
  const setEl = (id, val) => { const el = $(id); if (el) el.textContent = val; };
  let subtotal = 0;
  const checkboxes = document.querySelectorAll('.split-item-chk');
  const qtySelects = document.querySelectorAll('.split-item-qty');

  checkboxes.forEach((chk, index) => {
    if (chk.checked) {
      const idx = parseInt(chk.value);
      const item = tableUnpaidItems[idx];
      if (!item) return;
      const selectedQty = parseInt(qtySelects[index]?.value || 1);
      subtotal += parseFloat(item.price || 0) * selectedQty;
    }
  });

  const tax = subtotal * 0.05;
  const total = (subtotal + tax) / currentSplitPeopleCount;

  setEl('#splitSubtotal', `₹${subtotal.toFixed(2)}`);
  setEl('#splitTax', `₹${tax.toFixed(2)}`);
  setEl('#splitTotal', `₹${total.toFixed(2)}`);

  const config = getEffectiveCustomerUpiConfig();
  const qrImg = $('#splitUpiQrImg');

  if (qrImg) {
    if (total > 0 && config.type !== 'unconfigured') {
      qrImg.style.display = 'block';
      if (config.type === 'static_image') {
        qrImg.src = config.qrUrl;
      } else {
        const upiUrl = `upi://pay?pa=${encodeURIComponent(config.vpa)}&pn=${encodeURIComponent(config.name)}&am=${total.toFixed(2)}&cu=INR&tn=SplitBillTable${tableId}`;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(upiUrl)}`;
        qrImg.src = qrUrl;
        qrImg.onerror = () => {
          const parent = qrImg.parentElement;
          if (parent) {
            parent.innerHTML = `
              <div style="padding:16px;text-align:center;">
                <p style="color:#818CF8;font-weight:700;font-size:13px;margin:0 0 4px;">Pay via UPI ID</p>
                <p style="color:#34D399;font-size:16px;font-weight:800;margin:0;">${config.vpa}</p>
                <p style="opacity:0.6;font-size:11px;margin:6px 0 0;">Your Share: ₹${total.toFixed(2)}</p>
              </div>`;
          }
        };
      }
    } else if (total > 0 && config.type === 'unconfigured') {
      qrImg.style.display = 'none';
      const parent = qrImg.parentElement;
      if (parent) {
        parent.innerHTML = `
          <div style="padding:16px;text-align:center;">
            <p style="color:#F59E0B;font-weight:700;font-size:13px;margin:0 0 4px;">⚠️ UPI Not Configured</p>
            <p style="color:#94A3B8;font-size:11px;margin:0;">Please pay your share at billing counter.</p>
          </div>`;
      }
    } else {
      qrImg.src = '';
      qrImg.style.display = 'none';
    }
  }
  const upiSec = $('#splitUpiSection');
  if (upiSec) upiSec.style.display = total > 0 ? 'block' : 'none';
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    applyTheme(localStorage.getItem(THEME_STORAGE_KEY) || 'dark');
    applyLanguage();
    loadMenu();
  });
} else {
  applyTheme(localStorage.getItem(THEME_STORAGE_KEY) || 'dark');
  applyLanguage();
  loadMenu();
}

async function openPayFullBillModal() {
  const setEl = (id, val) => { const el = $(id); if (el) el.textContent = val; };
  setEl('#fullSubtotal', '₹0.00');
  setEl('#fullTax', '₹0.00');
  setEl('#fullTotal', '₹0.00');
  const qrImg = $('#fullUpiQrImg');
  if (qrImg) qrImg.src = '';
  const upiSection = $('#fullUpiSection');
  const itemListEl = $('#fullBillItemList');
  if (itemListEl) itemListEl.innerHTML = '';

  const payModal = $('#payFullBillModal');
  if (payModal) payModal.hidden = false;

  function generateAndShowQR(subtotal, orderId) {
    const tax = subtotal * 0.05;
    const total = subtotal + tax;
    setEl('#fullSubtotal', `₹${subtotal.toFixed(2)}`);
    setEl('#fullTax', `₹${tax.toFixed(2)}`);
    setEl('#fullTotal', `₹${total.toFixed(2)}`);

    if (total <= 0) { if (upiSection) upiSection.style.display = 'none'; return; }

    const config = getEffectiveCustomerUpiConfig();

    if (qrImg) {
      if (config.type === 'unconfigured') {
        qrImg.style.display = 'none';
        const parent = qrImg.parentElement;
        if (parent) {
          parent.innerHTML = `
            <div style="padding:16px;text-align:center;">
              <p style="color:#F59E0B;font-weight:700;font-size:13px;margin:0 0 4px;">⚠️ UPI Not Configured</p>
              <p style="color:#94A3B8;font-size:11px;margin:0;">Please pay total bill at cashier counter.</p>
            </div>`;
        }
      } else if (config.type === 'static_image') {
        qrImg.style.display = 'block';
        qrImg.src = config.qrUrl;
      } else {
        const note = orderId ? `Table${tableId}Order${orderId}` : `Table${tableId}Cart`;
        const upiUrl = `upi://pay?pa=${encodeURIComponent(config.vpa)}&pn=${encodeURIComponent(config.name)}&am=${total.toFixed(2)}&cu=INR&tn=${note}`;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(upiUrl)}`;
        qrImg.style.display = 'block';
        qrImg.src = qrUrl;
        qrImg.onerror = () => {
          const parent = qrImg.parentElement;
          if (parent) parent.innerHTML = `
            <div style="padding:20px;text-align:center;">
              <p style="color:#818CF8;font-weight:800;font-size:13px;margin:0 0 6px;">Pay via UPI ID</p>
              <p style="color:#34D399;font-size:18px;font-weight:800;margin:0;">${config.vpa}</p>
              <p style="opacity:0.6;font-size:11px;margin:8px 0 0;">Amount: ₹${total.toFixed(2)}</p>
            </div>`;
        };
      }
    }
    if (upiSection) upiSection.style.display = 'block';
  }

  // Try session order first (saved when customer placed order)
  const sessionOrderId = localStorage.getItem(`dineflow_session_order_${restaurantId}_${tableId}`);
  if (sessionOrderId) {
    try {
      const res = await fetch(`${API_BASE}/orders/${sessionOrderId}`);
      if (res.ok) {
        const data = await res.json();
        const order = data.data || data;
        if (order && order.payment_status === 'pending' && Array.isArray(order.items) && order.items.length > 0) {
          setEl('#fullBillTableLabel', `Table #${tableId} — Order #${order.id}`);
          let subtotal = 0;
          if (itemListEl) {
            itemListEl.innerHTML = order.items.map(item => {
              const name = item.name || item.menu_item?.name || 'Item';
              const lineTotal = parseFloat(item.price || 0) * parseInt(item.quantity || 1);
              subtotal += lineTotal;
              return `<div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.04);padding:8px 12px;border-radius:8px;font-size:13px;margin-bottom:4px;">
                <span style="font-weight:600;">${escapeHtml(name)} × ${item.quantity || 1}</span>
                <span style="color:#34D399;font-weight:800;">₹${lineTotal.toFixed(2)}</span>
              </div>`;
            }).join('');
          }
          generateAndShowQR(subtotal, order.id);
          return;
        }
      }
    } catch (err) {
      console.log('Session order fetch failed:', err);
    }
  }

  // Fallback: use local cart
  const cart = getCart();
  if (cart && cart.length > 0) {
    setEl('#fullBillTableLabel', `Table #${tableId} — Cart Preview`);
    let subtotal = 0;
    if (itemListEl) {
      itemListEl.innerHTML = cart.map(item => {
        const lineTotal = parseFloat(item.price || 0) * parseInt(item.quantity || 1);
        subtotal += lineTotal;
        return `<div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.04);padding:8px 12px;border-radius:8px;font-size:13px;margin-bottom:4px;">
          <span style="font-weight:600;">${escapeHtml(item.name)} × ${item.quantity || 1}</span>
          <span style="color:#34D399;font-weight:800;">₹${lineTotal.toFixed(2)}</span>
        </div>`;
      }).join('');
    }
    generateAndShowQR(subtotal, null);
    return;
  }

  // Nothing found
  setEl('#fullBillTableLabel', `Table #${tableId} — No active order`);
  if (itemListEl) itemListEl.innerHTML = `<p style="text-align:center;opacity:0.6;font-size:13px;padding:20px 0;">No active order found. Add items to your cart and place an order first.</p>`;
  if (upiSection) upiSection.style.display = 'none';
}

function closePayFullBillModal() {
  const modal = $('#payFullBillModal');
  if (modal) modal.hidden = true;
}

// --- Customer Rating & Review Modal Logic ---
function openCustomerReviewModal() {
  const modal = document.getElementById('customerReviewModal');
  if (modal) modal.style.display = 'flex';
  setStarRating(5);
}

function closeCustomerReviewModal() {
  const modal = document.getElementById('customerReviewModal');
  if (modal) modal.style.display = 'none';
}

function setStarRating(rating) {
  document.getElementById('reviewRatingInput').value = rating;
  const stars = document.querySelectorAll('#starPickerGroup .star-item');
  stars.forEach((star, index) => {
    if (index < rating) {
      star.style.color = '#FBBF24';
      star.style.opacity = '1';
      star.style.transform = 'scale(1.15)';
    } else {
      star.style.color = '#475569';
      star.style.opacity = '0.5';
      star.style.transform = 'scale(1)';
    }
  });
}

async function submitCustomerReview(event) {
  event.preventDefault();
  const customerName = document.getElementById('reviewCustomerName').value.trim();
  const rating = parseInt(document.getElementById('reviewRatingInput').value) || 5;
  const comment = document.getElementById('reviewComment').value.trim();

  const payload = {
    restaurant_id: restaurantId,
    customer_name: customerName,
    rating: rating,
    comment: comment
  };

  try {
    const res = await fetch(`${API_BASE}/feedback`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (res.ok || data.success) {
      alert(`⭐ Thank you ${customerName}! Your ${rating}-Star review has been submitted successfully.`);
      closeCustomerReviewModal();
      document.getElementById('reviewCustomerName').value = '';
      document.getElementById('reviewComment').value = '';
    } else {
      alert('Failed to submit review: ' + (data.message || 'Error'));
    }
  } catch (err) {
    alert('Failed to submit review. Please try again.');
    closeCustomerReviewModal();
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// 📅 TABLE RESERVATION SYSTEM
// ══════════════════════════════════════════════════════════════════════════════
function openTableReservationModal() {
  const modal = document.getElementById('tableReservationModal');
  if (!modal) return;
  modal.hidden = false;
  
  // Set default date to today
  const dateInput = document.getElementById('resDate');
  if (dateInput && !dateInput.value) {
    const today = new Date().toISOString().split('T')[0];
    dateInput.value = today;
    dateInput.min = today;
  }
}

function closeTableReservationModal() {
  const modal = document.getElementById('tableReservationModal');
  if (modal) modal.hidden = true;
}

async function submitTableReservation(event) {
  event.preventDefault();
  const date = document.getElementById('resDate').value;
  const timeSlot = document.getElementById('resTimeSlot').value;
  const pax = document.getElementById('resPax').value;
  const occasion = document.getElementById('resOccasion').value;
  const name = document.getElementById('resName').value.trim();
  const phone = document.getElementById('resPhone').value.trim();

  const payload = {
    restaurant_id: restaurantId,
    customer_name: name,
    customer_phone: phone,
    pax_count: pax,
    reservation_date: date,
    time_slot: timeSlot,
    special_requests: `Occasion: ${occasion}`,
    status: 'pending'
  };

  try {
    const res = await fetch(`${API_BASE}/reservations`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok && data.success === false) {
      showToast(data.message || 'Could not submit reservation. Please check your details.', true);
      return;
    }
    
    // Save to local storage for user's own reference
    const savedReservations = JSON.parse(localStorage.getItem('dineflow_my_reservations') || '[]');
    savedReservations.push({ ...payload, id: Date.now(), created_at: new Date().toISOString() });
    localStorage.setItem('dineflow_my_reservations', JSON.stringify(savedReservations));

    closeTableReservationModal();
    
    // WhatsApp Confirmation link
    const waMsg = encodeURIComponent(
      `🎉 *Table Reservation Request — DineFlow AI*\n\n` +
      `👤 *Name:* ${name}\n` +
      `📞 *Phone:* ${phone}\n` +
      `📅 *Date:* ${date}\n` +
      `⏰ *Time Slot:* ${timeSlot}\n` +
      `👥 *Guests:* ${pax} Person(s)\n` +
      `✨ *Occasion:* ${occasion}\n\n` +
      `_Please confirm my table reservation. Thank you!_`
    );
    
    const confirmWa = confirm(`🎉 Reservation request sent for ${pax} guests on ${date} (${timeSlot})!\n\nWould you like to send this confirmation to restaurant WhatsApp?`);
    if (confirmWa) {
      window.open(`https://api.whatsapp.com/send?text=${waMsg}`, '_blank');
    }
  } catch (err) {
    showToast('Reservation noted successfully!');
    closeTableReservationModal();
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// 🎙️ AI VOICE-TO-ORDER MULTILINGUAL RECOGNITION ENGINE
// ══════════════════════════════════════════════════════════════════════════════
let voiceRecognitionInstance = null;
let isVoiceListening = false;

function toggleVoiceOrderRecognition() {
  if (isVoiceListening) {
    stopVoiceOrderRecognition();
  } else {
    startVoiceOrderRecognition();
  }
}

function startVoiceOrderRecognition() {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    alert('Speech Recognition is not supported in this browser. Please use Chrome, Edge, or Safari on mobile/desktop.');
    return;
  }

  try {
    voiceRecognitionInstance = new SpeechRecognition();
    voiceRecognitionInstance.continuous = true;
    voiceRecognitionInstance.interimResults = true;

    // Detect language preference
    const lang = language || 'en';
    if (lang === 'gu') {
      voiceRecognitionInstance.lang = 'gu-IN';
    } else if (lang === 'hi') {
      voiceRecognitionInstance.lang = 'hi-IN';
    } else {
      voiceRecognitionInstance.lang = 'en-IN';
    }

    voiceRecognitionInstance.onstart = () => {
      isVoiceListening = true;
      const overlay = document.getElementById('aiVoiceOverlay');
      if (overlay) overlay.style.display = 'flex';
      const transcriptEl = document.getElementById('voiceLiveTranscript');
      if (transcriptEl) transcriptEl.textContent = 'Listening... Speak now';
      const micBtn = document.getElementById('voiceOrderBtn');
      if (micBtn) micBtn.style.background = '#EF4444';
    };

    voiceRecognitionInstance.onresult = (event) => {
      let finalTranscript = '';
      for (let i = event.resultIndex; i < event.results.length; ++i) {
        if (event.results[i].isFinal) {
          finalTranscript += event.results[i][0].transcript;
        } else {
          const interim = event.results[i][0].transcript;
          const transcriptEl = document.getElementById('voiceLiveTranscript');
          if (transcriptEl) transcriptEl.textContent = `"${interim}"`;
        }
      }

      if (finalTranscript) {
        const transcriptEl = document.getElementById('voiceLiveTranscript');
        if (transcriptEl) transcriptEl.textContent = `"${finalTranscript}"`;
        processVoiceOrderSpeech(finalTranscript);
      }
    };

    voiceRecognitionInstance.onerror = (event) => {
      console.warn('Speech recognition event:', event.error);
      if (event.error === 'not-allowed') {
        alert('Microphone permission was denied. Please allow mic access in your browser.');
        stopVoiceOrderRecognition();
      }
    };

    voiceRecognitionInstance.onend = () => {
      if (isVoiceListening) {
        stopVoiceOrderRecognition();
      }
    };

    voiceRecognitionInstance.start();
  } catch (err) {
    console.error('Error starting voice recognition:', err);
    stopVoiceOrderRecognition();
  }
}

function stopVoiceOrderRecognition() {
  isVoiceListening = false;
  if (voiceRecognitionInstance) {
    try { voiceRecognitionInstance.stop(); } catch (_) {}
    voiceRecognitionInstance = null;
  }
  const overlay = document.getElementById('aiVoiceOverlay');
  if (overlay) overlay.style.display = 'none';
  const micBtn = document.getElementById('voiceOrderBtn');
  if (micBtn) micBtn.style.background = 'linear-gradient(135deg, #6366F1, #4F46E5)';
}

function processVoiceOrderSpeech(text) {
  if (!text || !menuItems.length) return;
  const lowerText = text.toLowerCase();
  
  // Multilingual number word mappings
  const qtyMap = {
    'એક': 1, 'બે': 2, 'ત્રણ': 3, 'ચાર': 4, 'પાંચ': 5, 'છ': 6,
    'ek': 1, 'be': 2, 'tran': 3, 'char': 4, 'paanch': 5,
    'एक': 1, 'दो': 2, 'तीन': 3, 'चार': 4, 'पांच': 5, 'छह': 6,
    'do': 2, 'teen': 3,
    'one': 1, 'two': 2, 'three': 3, 'four': 4, 'five': 5, 'six': 6,
    '1': 1, '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8
  };

  // Find quantity in string if present
  let matchedQty = 1;
  for (const [word, num] of Object.entries(qtyMap)) {
    const regex = new RegExp(`\\b${word}\\b`, 'i');
    if (regex.test(lowerText)) {
      matchedQty = num;
      break;
    }
  }

  // Dish keyword matching aliases
  const matchedItems = [];
  menuItems.forEach(item => {
    const itemNameLower = item.name.toLowerCase();
    const itemWords = itemNameLower.split(' ').filter(w => w.length > 2);
    
    // Check direct substring or key word presence
    let isMatch = lowerText.includes(itemNameLower);
    if (!isMatch) {
      isMatch = itemWords.some(w => lowerText.includes(w));
    }
    
    // Extra multilingual phonetics
    if (!isMatch) {
      if (itemNameLower.includes('pizza') && (lowerText.includes('પીઝા') || lowerText.includes('पिज़्ज़ा') || lowerText.includes('piza'))) isMatch = true;
      if (itemNameLower.includes('burger') && (lowerText.includes('બર્ગર') || lowerText.includes('બર્ગ') || lowerText.includes('बर्गर'))) isMatch = true;
      if (itemNameLower.includes('pasta') && (lowerText.includes('પાસ્તા') || lowerText.includes('पास्ता'))) isMatch = true;
      if (itemNameLower.includes('coffee') && (lowerText.includes('કોફી') || lowerText.includes('कॉफ़ी') || lowerText.includes('કૉફી'))) isMatch = true;
      if (itemNameLower.includes('paneer') && (lowerText.includes('પનીર') || lowerText.includes('पनीर'))) isMatch = true;
      if (itemNameLower.includes('fries') && (lowerText.includes('ફ્રાઈસ') || lowerText.includes('ફ્રેન્ચ ફ્રાઈસ') || lowerText.includes('chips'))) isMatch = true;
      if (itemNameLower.includes('brownie') && (lowerText.includes('બ્રાઉની') || lowerText.includes('ચોકલેટ'))) isMatch = true;
      if (itemNameLower.includes('drink') && (lowerText.includes('કોલ્ડ્રિંક') || lowerText.includes('સોફ્ટ ડ્રિંક') || lowerText.includes('pepsi') || lowerText.includes('coke'))) isMatch = true;
    }

    if (isMatch) {
      if (item.available === false || item.is_available === false) {
        const outMessage = `Sorry, ${item.name} is currently sold out!`;
        showToast(`🚫 AI Voice: ${outMessage}`, true);
        if ('speechSynthesis' in window) {
          try {
            const utterance = new SpeechSynthesisUtterance(outMessage);
            utterance.rate = 1.05;
            window.speechSynthesis.speak(utterance);
          } catch (_) {}
        }
        stopVoiceOrderRecognition();
        return;
      }
      matchedItems.push(item);
    }
  });

  if (matchedItems.length > 0) {
    const targetItem = matchedItems[0];
    addToCart({ ...targetItem, quantity: matchedQty });
    
    const confirmMessage = `Added ${matchedQty} × ${targetItem.name} to your basket!`;
    showToast(`🤖 AI Voice: ${confirmMessage}`);
    
    // Voice Feedback (SpeechSynthesis)
    if ('speechSynthesis' in window) {
      try {
        const utterance = new SpeechSynthesisUtterance(confirmMessage);
        utterance.rate = 1.05;
        window.speechSynthesis.speak(utterance);
      } catch (_) {}
    }

    setTimeout(() => {
      stopVoiceOrderRecognition();
      // Auto open cart to show items
      openCart();
    }, 1200);
  } else {
    showToast('Could not match item name. Please speak dish name clearly.', true);
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// 💬 WHATSAPP AUTOMATED DIGITAL INVOICE GENERATOR
// ══════════════════════════════════════════════════════════════════════════════
function generateWhatsAppBillLink(orderId, orderDetails) {
  const restName = window.restaurant?.name || 'DineFlow Gourmet Restaurant';
  const table = orderDetails.table_id || '1';
  const items = orderDetails.items || [];
  const subtotal = orderDetails.total_amount || 0;
  const tax = (subtotal * 0.05).toFixed(2);
  const total = (Number(subtotal) + Number(tax)).toFixed(2);
  
  let itemLines = items.map(i => `• ${i.name || i.menu_item_name} × ${i.quantity} — ₹${(i.price * i.quantity).toFixed(2)}`).join('\n');
  if (!itemLines) itemLines = `• Order #${orderId} (Dine-in meal)`;

  const message = 
    `🍽️ *${restName}*\n` +
    `🧾 *Digital Bill & Receipt — Order #${orderId}*\n` +
    `📍 *Table:* #${table} | *Date:* ${new Date().toLocaleDateString('en-IN')}\n` +
    `━━━━━━━━━━━━━━━━━━━━\n` +
    `${itemLines}\n` +
    `━━━━━━━━━━━━━━━━━━━━\n` +
    `💵 *Subtotal:* ₹${Number(subtotal).toFixed(2)}\n` +
    `🏷️ *GST (5%):* ₹${tax}\n` +
    `💰 *Grand Total:* ₹${total} (PAID ✅)\n\n` +
    `🌟 *Rate your dining experience:* \n` +
    `https://dineflow.ai/review?order=${orderId}\n\n` +
    `_Thank you for dining with us! Visit again._ 🙏`;

  return `https://api.whatsapp.com/send?text=${encodeURIComponent(message)}`;
}

