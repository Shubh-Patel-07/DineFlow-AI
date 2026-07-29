const params = new URLSearchParams(window.location.search);
const restaurantId = params.get('restaurantId') || '1';
let tableId = params.get('tableId') || params.get('table') || '1';
const API_BASE = window.DINEFLOW_API_BASE || `${window.location.origin}/api`;

const translations = {
  en: { welcome: 'Smart QR Restaurant Ordering', table: 'Table', all: 'All', veg: 'Veg', nonveg: 'Non-Veg', menu: 'Our Menu', subtitle: 'Freshly prepared, just for you', search: 'Search delicious food...', callWaiter: 'Call Waiter', loading: 'Loading menu…', unavailable: 'Out of stock', available: 'Available', empty: 'No dishes found', emptyHint: 'Try changing your search or filters.', waiterSent: 'Your waiter has been called.', waiterError: 'Could not call waiter. Please try again.', calling: 'Calling…' },
  hi: { welcome: 'स्मार्ट QR रेस्टोरेंट ऑर्डरिंग', table: 'टेबल', all: 'सभी', veg: 'वेज', nonveg: 'नॉन-वेज', menu: 'हमारा मेनू', subtitle: 'आपके लिए ताज़ा तैयार', search: 'खाना खोजें...', callWaiter: 'वेटर बुलाएँ', loading: 'मेनू लोड हो रहा है…', unavailable: 'स्टॉक में नहीं', available: 'उपलब्ध', empty: 'कोई डिश नहीं मिली', emptyHint: 'खोज या फ़िल्टर बदलकर देखें।', waiterSent: 'वेटर को बुला दिया गया है।', waiterError: 'वेटर को नहीं बुला सके। फिर कोशिश करें।', calling: 'बुलाया जा रहा है…' },
  gu: { welcome: 'સ્માર્ટ QR રેસ્ટોરન્ટ ઓર્ડરિંગ', table: 'ટેબલ', all: 'બધા', veg: 'વેજ', nonveg: 'નોન-વેજ', menu: 'અમારું મેનૂ', subtitle: 'તમારા માટે તાજું બનાવેલું', search: 'ખાવાનું શોધો...', callWaiter: 'વેઇટરને બોલાવો', loading: 'મેનૂ લોડ થઈ રહ્યું છે…', unavailable: 'સ્ટોકમાં નથી', available: 'ઉપલબ્ધ', empty: 'કોઈ વાનગી મળી નથી', emptyHint: 'શોધ અથવા ફિલ્ટર બદલીને જુઓ.', waiterSent: 'વેઇટરને બોલાવવામાં આવ્યા છે.', waiterError: 'વેઇટરને બોલાવી શકાયા નથી. ફરી પ્રયાસ કરો.', calling: 'બોલાવવામાં આવે છે…' }
};

let language = localStorage.getItem('dineflow-language') || 'en';
let menuItems = [];
let category = 'All';
let diet = 'all';
let activeView = 'all';
let activeSuggestionIndex = -1;
const activeFilters = new Set();
const FAVORITES_STORAGE_KEY = 'dineflow-favorites';
const RECENT_STORAGE_KEY = 'dineflow-recent-items';
const THEME_STORAGE_KEY = 'dineflow-theme';
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
    .filter(item => item.popular || getFavoriteIds().has(String(item.id)) || getRecentItemIds().includes(String(item.id)))
    .slice(0, 4);

  if (!recommendedItems.length) {
    section.style.display = 'none';
    section.innerHTML = '';
    return;
  }

  section.style.display = 'block';
  section.innerHTML = `
    <div class="promo-section__header">
      <h3 class="promo-section__title">Recommended for You</h3>
      <span class="promo-section__meta">Picked for your taste</span>
    </div>
    <div class="promo-grid">
      ${recommendedItems.map(item => `
        <div class="promo-card">
          <span class="promo-card__tag">${escapeHtml(item.popular ? 'Popular' : 'Suggested')}</span>
          <div class="promo-card__name">${escapeHtml(item.name)}</div>
          <div class="promo-card__desc">${escapeHtml(item.description || 'A fresh pick from our menu')}</div>
          <button class="promo-card__btn" type="button" data-recommended-item-id="${escapeHtml(item.id)}">View Item</button>
        </div>
      `).join('')}
    </div>
  `;
}

function renderComboOffersSection() {
  const section = $('#comboOffersSection');
  if (!section) return;

  section.style.display = 'block';
  section.innerHTML = `
    <div class="promo-section__header">
      <h3 class="promo-section__title">Combo Offers</h3>
      <span class="promo-section__meta">Great value deals</span>
    </div>
    <div class="promo-grid">
      ${COMBO_OFFERS.map(combo => `
        <div class="combo-card">
          <div class="combo-card__title">${escapeHtml(combo.name)}</div>
          <div class="combo-card__includes">${escapeHtml(combo.includes.join(' • '))}</div>
          <div class="combo-card__price">₹${escapeHtml(combo.price)}</div>
          <button class="combo-card__btn" type="button" data-combo-add-id="${escapeHtml(combo.id)}">Add Combo</button>
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

function resolveImagePath(image, fallback = 'assests/images/placeholder-food.svg') {
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

function normalizeItem(item) {
  let cat = String(item.category || 'Mains').trim();
  if (cat.toLowerCase() === 'main course' || cat.toLowerCase() === 'mains') cat = 'Mains';
  if (cat.toLowerCase() === 'beverage' || cat.toLowerCase() === 'drinks') cat = 'Drinks';
  if (cat.toLowerCase() === 'starter' || cat.toLowerCase() === 'starters') cat = 'Starters';
  if (cat.toLowerCase() === 'bread' || cat.toLowerCase() === 'breads') cat = 'Breads';
  if (cat.toLowerCase() === 'dessert' || cat.toLowerCase() === 'desserts') cat = 'Desserts';

  const isPop = Boolean(item.popular || (item.badge && String(item.badge).toLowerCase().includes('popular')) || (item.badge && String(item.badge).toLowerCase().includes('seller')) || Number(item.rating) >= 4.8);

  return {
    ...item,
    id: item.id || item._id,
    category: cat,
    image: resolveImagePath(item.image),
    available: item.is_available ?? item.available ?? true,
    isVeg: item.is_veg ?? item.isVeg ?? true,
    rating: String(item.rating || '4.8'),
    badge: item.badge || cat,
    popular: isPop
  };
}

function applyLanguage() {
  document.documentElement.lang = language;
  document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = getText(el.dataset.i18n); });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = getText(el.dataset.i18nPlaceholder); });
  const langSel = $('#languageSelect');
  if (langSel) langSel.value = language;
  if (menuItems.length > 0) { renderCategories(); renderMenu(); }
}

function renderCategories() {
  const rawCategories = menuItems.map(item => item.category).filter(Boolean);
  const normalizedCategories = [];
  rawCategories.forEach(cat => {
    let cleanCat = String(cat).trim();
    if (!normalizedCategories.includes(cleanCat)) normalizedCategories.push(cleanCat);
  });

  const categories = ['All', ...normalizedCategories];
  $('#categorySection').innerHTML = categories.map(item => `<button class="category-btn ${item === category ? 'active' : ''}" data-category="${escapeHtml(item)}">${item === 'All' ? getText('all') : escapeHtml(item)}</button>`).join('');
}

function filteredItems() {
  const search = $('#searchInput').value.trim().toLowerCase();
  const favorites = getFavoriteIds();
  const recentIds = new Set(getRecentItemIds());

  const matches = menuItems.filter(item => {
    const matchesCategory = category === 'All' || (item.category && item.category.toLowerCase() === category.toLowerCase());
    const matchesDiet = diet === 'all' || (diet === 'veg' ? item.isVeg : !item.isVeg);
    const matchesSearch = !search || `${item.name} ${item.description || ''}`.toLowerCase().includes(search);
    const isFavorite = favorites.has(String(item.id));
    const isRecent = recentIds.has(String(item.id));
    const matchesView = activeView === 'all' || (activeView === 'favorites' && isFavorite) || (activeView === 'recent' && isRecent) || (activeView === 'popular' && Boolean(item.popular));

    const matchesPopular = !activeFilters.has('popular') || Boolean(item.popular);
    const matchesAvailable = !activeFilters.has('available') || Boolean(item.available);
    const matchesRating = !activeFilters.has('rating') || Number(item.rating) >= 4.5;
    return matchesCategory && matchesDiet && matchesSearch && matchesView && matchesPopular && matchesAvailable && matchesRating;
  });
  const sort = $('#sortSelect')?.value;
  if (sort === 'price-asc') return matches.sort((a, b) => Number(a.price) - Number(b.price));
  if (sort === 'price-desc') return matches.sort((a, b) => Number(b.price) - Number(a.price));
  return matches;
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
  $('#foodGrid').innerHTML = items.length ? items.map(item => {
    const imageSrc = resolveImagePath(item.image);
    const isFavorite = favorites.has(String(item.id));
    return `<article class="food-card ${item.available ? '' : 'unavailable'}" data-item-id="${escapeHtml(item.id)}" tabindex="0" role="button" aria-label="View details for ${escapeHtml(item.name)}">
      <button class="favorite-btn ${isFavorite ? 'is-favorite' : ''}" type="button" data-favorite-toggle="${escapeHtml(item.id)}" aria-label="${isFavorite ? 'Remove from favorites' : 'Add to favorites'}">♡</button>
      <img class="food-image" src="${escapeHtml(imageSrc)}" alt="${escapeHtml(item.name)}" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/images/placeholder-food.svg';">
      <div class="food-content">
        <div class="food-topline">
          <span class="food-type ${item.isVeg ? 'veg' : 'nonveg'}">${item.isVeg ? '● Veg' : '● Non-Veg'}</span>
          <span class="rating-tag">★ ${escapeHtml(item.rating)}</span>
        </div>
        <h3>${escapeHtml(item.name)}</h3>
        <p>${escapeHtml(item.description || '')}</p>
        <div class="food-footer">
          <strong>₹${escapeHtml(item.price)}</strong>
          <button class="btn-add-to-order" type="button" data-add-cart="${escapeHtml(item.id)}">+ Add</button>
        </div>
      </div>
    </article>`;
  }).join('') : `<div class="empty-state"><span class="empty-illustration" aria-hidden="true">🍽️</span><h3>${getText('empty')}</h3><p>${getText('emptyHint')}</p></div>`;
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
  try {
    const activeOrdersRes = await fetch(`${API_BASE}/orders/restaurant/${restaurantId}`);
    const activeOrdersData = await activeOrdersRes.json();
    if (activeOrdersData.success && Array.isArray(activeOrdersData.data)) {
      activeKotsCount = activeOrdersData.data.filter(o => o.status !== 'completed' && o.status !== 'cancelled' && o.status !== 'served').length;
    }
  } catch (err) {
    console.log('Error fetching active orders count:', err);
  }

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
      bgLight = 'rgba(52, 211, 153, 0.1)';
    } else if (activeKotsCount > 2 && activeKotsCount <= 5) {
      trafficText = 'Medium Traffic';
      prepTime = 18;
      badgeColor = '#F59E0B';
      bgLight = 'rgba(245, 158, 11, 0.1)';
    } else if (activeKotsCount > 5) {
      trafficText = 'High Traffic / Busy';
      prepTime = 25;
      badgeColor = '#EF4444';
      bgLight = 'rgba(239, 68, 68, 0.1)';
    }

    prepBadge.style.background = bgLight;
    prepBadge.style.border = `1px solid ${badgeColor}`;
    prepBadge.style.color = badgeColor;
    prepBadge.innerHTML = `
      <span style="font-size: 14px;">⏱️</span>
      <div>
        <div style="font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Est. Prep Time: ~${prepTime} Mins</div>
        <div style="font-size: 9px; opacity: 0.8; margin-top: 1px;">Kitchen Load: ${trafficText} (${activeKotsCount} active KOTs)</div>
      </div>
    `;
  }

  const modal = $('#itemModal');
  if (modal) modal.hidden = false;
  const closeBtn = $('#btnCloseModal') || $('#closeItemModal');
  if (closeBtn) closeBtn.focus();
}

function closeItemModal() { const m = $('#itemModal'); if (m) m.hidden = true; }

// Wire modal Add to Cart button
document.addEventListener('click', event => {
  const recommendedButton = event.target.closest('[data-recommended-item-id]');
  if (recommendedButton) {
    const item = menuItems.find(i => String(i.id) === String(recommendedButton.dataset.recommendedItemId));
    if (item) openItemModal(item);
    return;
  }

  const comboButton = event.target.closest('[data-combo-add-id]');
  if (comboButton) {
    const combo = COMBO_OFFERS.find(item => item.id === comboButton.dataset.comboAddId);
    if (combo) {
      addToCart({
        id: `${combo.id}-${Date.now()}`,
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

  if (event.target.closest('#modalAddToCartBtn')) {
    const name = $('#modalItemName')?.textContent;
    const item = menuItems.find(i => i.name === name);
    if (item) { addToCart({ ...item, quantity: 1 }); closeItemModal(); }
  }
});


// --- Cart helpers (local) ---
const CART_STORAGE_KEY = 'dineflow-cart';
function getCart() {
  try { return JSON.parse(localStorage.getItem(CART_STORAGE_KEY) || '[]'); } catch { return []; }
}
function saveCart(cart) { localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart)); }
function addToCart(customizedItem) {
  const cart = getCart();
  cart.push(customizedItem);
  saveCart(cart);
  showToast('Added to cart');
  renderCart();
}

// --- Build Your Plate feature ---
let currentBuildItem = null;
function openBuildPlateModal(item) {
  if (!item) return;
  currentBuildItem = item;
  const buildModal = $('#buildPlateModal');
  if (!buildModal) { addToCart({ ...item, quantity: 1 }); return; }
  const nameEl = $('#buildItemName');
  if (nameEl) nameEl.textContent = item.name + ` — ₹${Number(item.price).toFixed(2)}`;
  const spicyEl = $('#buildSpicy'); if (spicyEl) spicyEl.value = 'Medium';
  const portionEl = $('#buildPortion'); if (portionEl) portionEl.value = 'full';
  document.querySelectorAll('.build-addon').forEach(chk => chk.checked = false);
  document.querySelectorAll('.build-remove').forEach(chk => chk.checked = false);
  const instrEl = $('#buildInstruction'); if (instrEl) instrEl.value = '';
  updateBuildPrice();
  buildModal.hidden = false;
  const closeBtn = $('#closeBuildPlateModal'); if (closeBtn) closeBtn.focus();
}

function openBuildPlateModalFromDetails() {
  const name = $('#modalItemName').textContent;
  const item = menuItems.find(i => i.name === name) || menuItems.find(i => String(i.id) === String($('#modalItemName').dataset?.id));
  openBuildPlateModal(item);
}

function closeBuildPlateModal() { const m = $('#buildPlateModal'); if (m) m.hidden = true; currentBuildItem = null; }

function parseNumber(value) { const n = parseFloat(value); return isNaN(n) ? 0 : n; }

function updateBuildPrice() {
  const priceEl = $('#buildPrice');
  if (!currentBuildItem) { if (priceEl) priceEl.textContent = '₹0.00'; return 0; }
  const base = parseNumber(currentBuildItem.price);
  const portionEl = $('#buildPortion');
  const portion = portionEl ? portionEl.value : 'full';
  const portionMultiplier = portion === 'half' ? 0.5 : 1;
  let addonsTotal = 0;
  document.querySelectorAll('.build-addon').forEach(chk => { if (chk.checked) addonsTotal += parseNumber(chk.dataset.price); });
  const subtotal = (base * portionMultiplier) + addonsTotal;
  if (priceEl) priceEl.textContent = `₹${subtotal.toFixed(2)}`;
  return subtotal;
}

function buildDescription() {
  if (!currentBuildItem) return '';
  const parts = [];
  const spicyEl = $('#buildSpicy');
  const portionEl = $('#buildPortion');
  const spicy = spicyEl ? spicyEl.value : 'Medium';
  const portion = portionEl ? portionEl.value : 'full';
  parts.push(`${spicy} spicy`);
  const removes = [...document.querySelectorAll('.build-remove')].filter(c => c.checked).map(c => c.dataset.remove);
  removes.forEach(r => parts.push(r));
  const addons = [...document.querySelectorAll('.build-addon')].filter(c => c.checked).map(c => `${c.dataset.addon}${c.dataset.price ? ` (+₹${c.dataset.price})` : ''}`);
  addons.forEach(a => parts.push(a));
  const instrEl = $('#buildInstruction');
  const instr = instrEl ? instrEl.value.trim() : '';
  if (instr) parts.push(`Note: ${instr}`);
  return parts.join(', ');
}

// Wire up build modal interactions
document.addEventListener('change', event => {
  if (event.target.closest('#buildPlateModal')) updateBuildPrice();
});
const closesBuildBtn = document.getElementById('closeBuildPlateModal');
if (closesBuildBtn) closesBuildBtn.addEventListener('click', closeBuildPlateModal);
const cancelBuildBtn = document.querySelector('#btnCancelBuild');
if (cancelBuildBtn) cancelBuildBtn.addEventListener('click', closeBuildPlateModal);
const addBuildBtn = document.getElementById('btnAddBuildToCart');
if (addBuildBtn) addBuildBtn.addEventListener('click', () => {
  if (!currentBuildItem) return;
  const total = updateBuildPrice();
  const desc = buildDescription();
  const portionEl = $('#buildPortion');
  const spicyEl = $('#buildSpicy');
  const instrEl = $('#buildInstruction');
  const customized = {
    id: `${currentBuildItem.id}-${Date.now()}`,
    baseId: currentBuildItem.id,
    name: currentBuildItem.name,
    description: desc,
    price: Number((total || 0).toFixed(2)),
    quantity: 1,
    portion: portionEl ? portionEl.value : 'full',
    spicy: spicyEl ? spicyEl.value : 'Medium',
    addons: [...document.querySelectorAll('.build-addon')].filter(c => c.checked).map(c => ({ name: c.dataset.addon, price: parseNumber(c.dataset.price) })),
    removes: [...document.querySelectorAll('.build-remove')].filter(c => c.checked).map(c => c.dataset.remove),
    specialInstruction: instrEl ? instrEl.value.trim() : '',
  };
  addToCart(customized);
  closeBuildPlateModal();
  closeItemModal();
});

// --- Add-on availability (from API) and Presets ---
let addonAvailability = {};
async function loadAddonAvailability() {
  try {
    const res = await fetch(`${API_BASE}/addons/availability?restaurantId=${encodeURIComponent(restaurantId)}`);
    const data = await res.json().catch(() => ({}));
    if (data && data.success && data.data) addonAvailability = data.data;
    else addonAvailability = {};
  } catch (err) {
    addonAvailability = {};
  }
  applyAddonAvailability();
}

function applyAddonAvailability() {
  document.querySelectorAll('.build-addon').forEach(chk => {
    const name = chk.dataset.addon;
    const label = chk.closest('label');
    const available = addonAvailability && Object.prototype.hasOwnProperty.call(addonAvailability, name) ? Boolean(addonAvailability[name]) : true;
    chk.disabled = !available;
    // update label status
    if (label) {
      let status = label.querySelector('.addon-status');
      if (!status) { status = document.createElement('span'); status.className = 'addon-status'; status.style.marginLeft = '8px'; status.style.fontSize = '12px'; status.style.opacity = '0.85'; label.appendChild(status); }
      status.textContent = available ? '' : ' (unavailable)';
      label.style.opacity = available ? '1' : '0.6';
    }
  });
}

// Presets storage
const PRESETS_KEY = 'dineflow-presets';
function getPresets() { try { return JSON.parse(localStorage.getItem(PRESETS_KEY) || '[]'); } catch { return []; } }
function savePresets(presets) { localStorage.setItem(PRESETS_KEY, JSON.stringify(presets)); }

function renderPresets() {
  const list = $('#buildPresetsList');
  if (!list) return;
  const presets = getPresets();
  if (!presets.length) { list.innerHTML = `<div style="opacity:0.7; font-size:13px;">No saved presets</div>`; return; }
  list.innerHTML = presets.map((p, idx) => `<button type="button" class="preset-btn" data-preset-idx="${idx}" style="padding:6px 8px; border-radius:8px; background: rgba(255,255,255,0.04); font-size:13px;">${escapeHtml(p.name)}</button>`).join('');
}

function applyPreset(idx) {
  const presets = getPresets();
  const p = presets[idx];
  if (!p || !currentBuildItem) return;
  // apply options
  $('#buildSpicy').value = p.spicy || 'Medium';
  $('#buildPortion').value = p.portion || 'full';
  document.querySelectorAll('.build-addon').forEach(chk => chk.checked = false);
  (p.addons || []).forEach(a => {
    const chk = Array.from(document.querySelectorAll('.build-addon')).find(c => c.dataset.addon === a.name);
    if (chk && !chk.disabled) chk.checked = true;
  });
  document.querySelectorAll('.build-remove').forEach(chk => chk.checked = false);
  (p.removes || []).forEach(r => {
    const chk = Array.from(document.querySelectorAll('.build-remove')).find(c => c.dataset.remove === r);
    if (chk) chk.checked = true;
  });
  $('#buildInstruction').value = p.specialInstruction || '';
  updateBuildPrice();
}

function saveCurrentPreset() {
  if (!currentBuildItem) { showToast('Open an item first', true); return; }
  const presets = getPresets();
  const name = `${currentBuildItem.name} Preset ${presets.length + 1}`;
  const preset = {
    id: `preset-${Date.now()}`,
    name,
    baseId: currentBuildItem.id,
    spicy: $('#buildSpicy').value,
    portion: $('#buildPortion').value,
    addons: [...document.querySelectorAll('.build-addon')].filter(c => c.checked).map(c => ({ name: c.dataset.addon, price: parseNumber(c.dataset.price) })),
    removes: [...document.querySelectorAll('.build-remove')].filter(c => c.checked).map(c => c.dataset.remove),
    specialInstruction: $('#buildInstruction').value.trim(),
    price: updateBuildPrice()
  };
  presets.push(preset);
  savePresets(presets);
  renderPresets();
  showToast('Preset saved');
}

// Wire preset clicks
document.addEventListener('click', event => {
  const btn = event.target.closest('[data-preset-idx]');
  if (btn) { applyPreset(Number(btn.dataset.presetIdx)); }
});

// Hook save preset button
const savePBtn = document.getElementById('btnSavePreset');
if (savePBtn) savePBtn.addEventListener('click', saveCurrentPreset);

// Load addon availability & presets when opening build modal - only if build modal exists
if (document.getElementById('buildPlateModal')) {
  const origOpenBuild = openBuildPlateModal;
  openBuildPlateModal = function(item) {
    origOpenBuild(item);
    loadAddonAvailability();
    renderPresets();
  };
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

  if (!cart.length) {
    list.innerHTML = `
      <div style="text-align:center; padding:36px 20px; color:#64748B;">
        <span style="font-size:36px; display:block; margin-bottom:8px;">🛒</span>
        <div style="font-size:14px; font-weight:700; color:#F8FAFC;">Your basket is empty</div>
        <div style="font-size:12px; color:#94A3B8; margin-top:2px;">Add delicious items from menu to start ordering!</div>
      </div>
    `;
    subtotalEl.textContent = formatCurrency(0);
    if (taxEl) taxEl.textContent = formatCurrency(0);
    if (grandTotalEl) grandTotalEl.textContent = formatCurrency(0);
    return;
  }

  let subtotal = 0;
  list.innerHTML = cart.map((item, idx) => {
    const name = escapeHtml(item.name);
    const price = Number(item.price || 0);
    const qty = item.quantity || 1;
    const itemTotal = price * qty;
    subtotal += itemTotal;

    return `
      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); padding:10px 14px; border-radius:14px; transition:all 0.2s;">
        <div style="flex:1;">
          <div style="font-weight:700; color:#F8FAFC; font-size:13.5px;">${name}</div>
          <div style="font-size:12px; color:#818CF8; font-weight:700; margin-top:2px;">${formatCurrency(price)} × ${qty} = <span style="color:#F8FAFC;">${formatCurrency(itemTotal)}</span></div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
          <div style="display:flex; align-items:center; background:rgba(15,23,42,0.8); border:1px solid rgba(255,255,255,0.1); border-radius:10px; padding:2px 4px;">
            <button onclick="changeCartQty(${idx}, ${qty - 1})" style="background:none; border:none; color:#F8FAFC; font-weight:800; width:26px; height:26px; cursor:pointer; font-size:14px;">-</button>
            <span style="font-size:12.5px; font-weight:700; color:#F8FAFC; min-width:20px; text-align:center;">${qty}</span>
            <button onclick="changeCartQty(${idx}, ${qty + 1})" style="background:none; border:none; color:#F8FAFC; font-weight:800; width:26px; height:26px; cursor:pointer; font-size:14px;">+</button>
          </div>
          <button data-remove-idx="${idx}" class="cart-remove" title="Remove item" style="background:rgba(239,68,68,0.15); border:none; color:#F87171; width:28px; height:28px; border-radius:8px; font-size:13px; cursor:pointer; display:flex; align-items:center; justify-content:center;">✕</button>
        </div>
      </div>
    `;
  }).join('');

  const tax = subtotal * 0.18;
  const grandTotal = subtotal + tax;

  subtotalEl.textContent = formatCurrency(subtotal);
  if (taxEl) taxEl.textContent = formatCurrency(tax);
  if (grandTotalEl) grandTotalEl.textContent = formatCurrency(grandTotal);
}

function changeCartQty(idx, newQty) {
  const cart = getCart();
  if (!cart[idx]) return;
  if (newQty <= 0) {
    cart.splice(idx, 1);
    showToast('Removed from cart');
  } else {
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
  
  if (cashLabel && upiLabel) {
    if (method === 'upi') {
      upiLabel.style.background = 'rgba(16,185,129,0.15)';
      upiLabel.style.borderColor = '#10B981';
      cashLabel.style.background = 'rgba(255,255,255,0.03)';
      cashLabel.style.borderColor = 'rgba(255,255,255,0.1)';
    } else {
      cashLabel.style.background = 'rgba(99,102,241,0.15)';
      cashLabel.style.borderColor = '#6366F1';
      upiLabel.style.background = 'rgba(255,255,255,0.03)';
      upiLabel.style.borderColor = 'rgba(255,255,255,0.1)';
    }
  }
}

function openCart() { const p = $('#cartPanel'); if (p) { p.hidden = false; renderCart(); } }
function closeCart() { const p = $('#cartPanel'); if (p) p.hidden = true; }

async function handleCheckout() {
  const cart = getCart();
  if (!cart || !cart.length) {
    showToast('Your cart is empty! Add items first.', true);
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
    table_id: String(tableId),
    payment_method: selectedCartPayMethod || 'cash',
    special_instructions: combinedInstructions,
    items: cart.map(item => ({
      menu_item_id: item.baseId || item.id,
      quantity: item.quantity || 1
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
      const orderId = data.data.id;
      const amount = data.data.total_amount || 0;
      saveCart([]);
      renderCart();
      closeCart();
      localStorage.setItem(`dineflow_session_order_${tableId}`, orderId);

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

function openCustomerUpiModal(orderId, amount) {
  currentCustomerUpiOrderId = orderId;
  const modal = $('#customerUpiModal');
  const amountEl = $('#customerUpiAmount');
  const qrImg = $('#customerUpiQrImg');

  const formattedAmount = Number(amount || 0).toFixed(2);
  if (amountEl) amountEl.textContent = `₹${formattedAmount}`;
  if (qrImg) {
    qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=upi://pay?pa=dineflow@upi%26pn=DineFlow%20AI%26am=${formattedAmount}%26cu=INR`;
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
    await fetch(`${API_BASE}/orders/${currentCustomerUpiOrderId}/pay`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ payment_method: 'upi' })
    });
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
    const data = await res.json();
    if (data.success && data.data) {
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

  if (status === 'pending') {
    if (fill) fill.style.width = '0%';
    if (step1) step1.classList.add('active');
    if (step2) step2.classList.remove('active');
    if (step3) step3.classList.remove('active');
    if (stepIcon1) { stepIcon1.style.background = '#F59E0B'; stepIcon1.style.color = '#000'; }
    if (stepIcon2) { stepIcon2.style.background = 'rgba(255,255,255,0.1)'; stepIcon2.style.color = 'var(--text-muted)'; }
    if (stepIcon3) { stepIcon3.style.background = 'rgba(255,255,255,0.1)'; stepIcon3.style.color = 'var(--text-muted)'; }
    if (statusMsg) statusMsg.textContent = '🟡 Order received! Sent to kitchen chef.';
  } else if (status === 'preparing') {
    if (fill) fill.style.width = '50%';
    if (step1) step1.classList.add('active');
    if (step2) step2.classList.add('active');
    if (step3) step3.classList.remove('active');
    if (stepIcon1) { stepIcon1.style.background = '#F59E0B'; stepIcon1.style.color = '#000'; }
    if (stepIcon2) { stepIcon2.style.background = '#F97316'; stepIcon2.style.color = '#fff'; }
    if (stepIcon3) { stepIcon3.style.background = 'rgba(255,255,255,0.1)'; stepIcon3.style.color = 'var(--text-muted)'; }
    if (statusMsg) statusMsg.textContent = '🟠 Chef is cooking your delicious meal in the kitchen!';
  } else if (status === 'served' || status === 'completed') {
    if (fill) fill.style.width = '100%';
    if (step1) step1.classList.add('active');
    if (step2) step2.classList.add('active');
    if (step3) step3.classList.add('active');
    if (stepIcon1) { stepIcon1.style.background = '#10B981'; stepIcon1.style.color = '#fff'; }
    if (stepIcon2) { stepIcon2.style.background = '#10B981'; stepIcon2.style.color = '#fff'; }
    if (stepIcon3) { stepIcon3.style.background = '#10B981'; stepIcon3.style.color = '#fff'; }
    if (statusMsg) statusMsg.textContent = '🟢 Food is ready & served at your table! Enjoy your meal!';

    if (liveOrderPollInterval) {
      clearInterval(liveOrderPollInterval);
      liveOrderPollInterval = null;
    }

    // Auto-dismiss/hide tracker banner after 6 seconds when served/completed
    setTimeout(() => {
      hideLiveOrderTracker();
      localStorage.removeItem(`dineflow_session_order_${tableId}`);
    }, 6000);
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

document.addEventListener('change', event => {
  const sel = event.target.closest('.cart-qty');
  if (!sel) return;
  const idx = Number(sel.dataset.cartIdx);
  const qty = Number(sel.value);
  const cart = getCart();
  if (cart[idx]) { cart[idx].quantity = qty; saveCart(cart); renderCart(); }
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
      const nameEl = $('#restaurantName'); if (nameEl && rData.name) nameEl.textContent = rData.name;
      const locEl = $('#restaurantLocation'); if (locEl && (rData.address || rData.location)) locEl.textContent = rData.address || rData.location;
      const logoEl = $('#restaurantLogo'); if (logoEl && rData.logo) logoEl.src = resolveImagePath(rData.logo);
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
      const apiItems = menuResult.value.map(normalizeItem);
      const staticFoodItems = getStaticFoodItemsList().map(normalizeItem);
      menuItems = apiItems.map(apiItem => {
        const match = staticFoodItems.find(s => s.name.toLowerCase() === apiItem.name.toLowerCase() || String(s.id) === String(apiItem.id));
        if (match) {
          return { ...match, ...apiItem, image: apiItem.image || match.image };
        }
        return apiItem;
      });
      renderCategories();
      renderMenu();
    }
  } catch (_) {}
}

async function checkTableActiveOrder() {
  const tracker = $('#liveOrderTracker');
  if (tracker) {
    tracker.hidden = true;
    tracker.style.setProperty('display', 'none', 'important');
  }

  const sessionPlacedOrderId = localStorage.getItem(`dineflow_session_order_${tableId}`);
  if (!sessionPlacedOrderId) return;

  try {
    const res = await fetch(`${API_BASE}/orders/${sessionPlacedOrderId}`);
    const data = await res.json();
    if (data.success && data.data) {
      const order = data.data;
      if (order.status !== 'completed' && order.status !== 'served' && order.status !== 'cancelled') {
        startLiveOrderTracking(order.id);
      } else {
        localStorage.removeItem(`dineflow_session_order_${tableId}`);
      }
    }
  } catch (err) {
    console.log('Error checking table active order:', err);
  }
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
    const span = toggle.querySelector('span');
    if (span) span.textContent = isLight ? 'Dark mode' : 'Light mode';
    if (toggle.firstChild) toggle.firstChild.textContent = isLight ? '☾ ' : '☀ ';
  }
  localStorage.setItem(THEME_STORAGE_KEY, selectedTheme);
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
document.addEventListener('click', event => { if (!event.target.closest('.search-wrapper') && _searchSuggestions) _searchSuggestions.hidden = true; });
if (_themeToggle) _themeToggle.addEventListener('click', () => applyTheme(document.body.classList.contains('light-theme') ? 'dark' : 'light'));
document.querySelectorAll('.filter-btn').forEach(button => button.addEventListener('click', () => { const { filter } = button.dataset; activeFilters.has(filter) ? activeFilters.delete(filter) : activeFilters.add(filter); button.classList.toggle('is-active', activeFilters.has(filter)); renderMenu(); }));
if (_sortSelect) _sortSelect.addEventListener('change', renderMenu);
if (_languageSelect) _languageSelect.addEventListener('change', event => { language = event.target.value; localStorage.setItem('dineflow-language', language); applyLanguage(); });
if (_tableNumber) _tableNumber.addEventListener('change', event => { tableId = event.target.value; const nextUrl = new URL(window.location.href); nextUrl.searchParams.set('tableId', tableId); window.history.replaceState({}, '', nextUrl); loadMenu(); });
if (_categorySection) _categorySection.addEventListener('click', event => { const button = event.target.closest('[data-category]'); if (!button) return; category = button.dataset.category; renderCategories(); renderMenu(); });
document.querySelectorAll('.diet-btn').forEach(button => button.addEventListener('click', () => { diet = button.dataset.diet; document.querySelectorAll('.diet-btn').forEach(item => item.classList.toggle('active', item === button)); renderMenu(); }));
document.querySelectorAll('.view-btn').forEach(button => button.addEventListener('click', () => { activeView = button.dataset.view; document.querySelectorAll('.view-btn').forEach(item => item.classList.toggle('active', item === button)); renderMenu(); }));
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
    if (item) addToCart(item);
    return;
  }

  const card = event.target.closest('[data-item-id]');
  if (card) openItemModal(menuItems.find(item => String(item.id) === card.dataset.itemId));
});

const _favSection = $('#favoritesSection');
if (_favSection) _favSection.addEventListener('click', event => {
  const removeButton = event.target.closest('[data-favorite-remove-id]');
  if (!removeButton) return;
  event.stopPropagation();
  removeFavorite(removeButton.dataset.favoriteRemoveId);
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
  if (item) openItemModal(item);
});
if (_foodGrid) _foodGrid.addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { const card = event.target.closest('[data-item-id]'); if (card) { event.preventDefault(); openItemModal(menuItems.find(item => String(item.id) === card.dataset.itemId)); } } });

const btnCloseModal = $('#btnCloseModal') || $('#closeItemModal');
if (btnCloseModal) btnCloseModal.addEventListener('click', closeItemModal);

const itemModalEl = $('#itemModal');
if (itemModalEl) itemModalEl.addEventListener('click', event => { if (event.target.hasAttribute('data-close-modal')) closeItemModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeItemModal(); });

const callWaiterButton = $('#callWaiterBtn');
if (callWaiterButton) {
  callWaiterButton.addEventListener('click', async () => {
    callWaiterButton.disabled = true;
    const labelSpan = callWaiterButton.querySelector('span:last-child');
    if (labelSpan) labelSpan.textContent = getText('calling');
    try {
      await request('/waiter-calls', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ restaurant_id: String(restaurantId), table_id: String(tableId) }) });
      showToast(getText('waiterSent'));
    } catch (_) {
      showToast(getText('waiterError'), true);
    } finally {
      callWaiterButton.disabled = false;
      if (labelSpan) labelSpan.textContent = getText('callWaiter');
    }
  });
}

let tableUnpaidItems = [];

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
              <div style="font-weight: 700; color: white;">${escapeHtml(item.name || item.menu_item?.name || 'Item')}</div>
              <div style="font-size: 10px; opacity: 0.6;">₹${Number(item.price || 0).toFixed(2)} each</div>
            </div>
          </div>
          <div style="font-size: 12px; font-weight: 700; color: white;">
            Qty: <select class="split-item-qty" onchange="recalculateSplit()" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 4px; padding: 2px 4px; font-size: 11px;">
              ${Array.from({ length: item.quantity || 1 }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
            </select>
          </div>
        </label>
      `).join('');
    }
  }

  // Try DB order first
  try {
    const res = await fetch(`${API_BASE}/orders/restaurant/${restaurantId}`);
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      const activeOrder = data.data.find(order => String(order.table_id) === String(tableId) && order.payment_status === 'pending');
      if (activeOrder && Array.isArray(activeOrder.items) && activeOrder.items.length > 0) {
        renderSplitItems(activeOrder.items, 'order');
        const modal = $('#splitBillModal');
        if (modal) modal.hidden = false;
        return;
      }
    }
  } catch (err) {
    console.log('Split bill DB fetch failed, trying cart:', err);
  }

  // Fallback: use cart items
  const cart = getCart();
  if (cart && cart.length > 0) {
    renderSplitItems(cart, 'cart');
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
  const total = subtotal + tax;

  setEl('#splitSubtotal', `₹${subtotal.toFixed(2)}`);
  setEl('#splitTax', `₹${tax.toFixed(2)}`);
  setEl('#splitTotal', `₹${total.toFixed(2)}`);

  const merchantUpi = '7777938338@omni';
  const merchantName = 'DineFlow AI Restaurant';
  const upiUrl = `upi://pay?pa=${encodeURIComponent(merchantUpi)}&pn=${encodeURIComponent(merchantName)}&am=${total.toFixed(2)}&cu=INR&tn=SplitBillTable${tableId}`;
  const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(upiUrl)}`;

  const qrImg = $('#splitUpiQrImg');
  if (qrImg) {
    if (total > 0) {
      qrImg.src = qrUrl;
      qrImg.style.display = 'block';
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

    const merchantUpi = '7777938338@omni';
    const merchantName = 'DineFlow AI Restaurant';
    const note = orderId ? `Table${tableId}Order${orderId}` : `Table${tableId}Cart`;
    const upiUrl = `upi://pay?pa=${encodeURIComponent(merchantUpi)}&pn=${encodeURIComponent(merchantName)}&am=${total.toFixed(2)}&cu=INR&tn=${note}`;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(upiUrl)}`;

    if (qrImg) {
      qrImg.style.display = 'block';
      qrImg.src = qrUrl;
      qrImg.onerror = () => {
        // Fallback if QR server unreachable
        const parent = qrImg.parentElement;
        if (parent) parent.innerHTML = `
          <div style="padding:20px;text-align:center;">
            <p style="color:#818CF8;font-weight:800;font-size:13px;margin:0 0 6px;">Pay via UPI ID</p>
            <p style="color:#34D399;font-size:18px;font-weight:800;margin:0;">${merchantUpi}</p>
            <p style="opacity:0.6;font-size:11px;margin:8px 0 0;">Amount: ₹${total.toFixed(2)}</p>
          </div>`;
      };
    }
    if (upiSection) upiSection.style.display = 'block';
  }

  // Try to fetch from DB first
  try {
    const res = await fetch(`${API_BASE}/orders/restaurant/${restaurantId}`);
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      const activeOrder = data.data.find(o =>
        String(o.table_id) === String(tableId) && o.payment_status === 'pending'
      );
      if (activeOrder && Array.isArray(activeOrder.items) && activeOrder.items.length > 0) {
        setEl('#fullBillTableLabel', `Table #${tableId} — Order #${activeOrder.id}`);
        let subtotal = 0;
        if (itemListEl) {
          itemListEl.innerHTML = activeOrder.items.map(item => {
            const name = item.name || item.menu_item?.name || 'Item';
            const lineTotal = parseFloat(item.price || 0) * parseInt(item.quantity || 1);
            subtotal += lineTotal;
            return `<div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.04);padding:8px 12px;border-radius:8px;font-size:13px;margin-bottom:4px;">
              <span style="font-weight:600;">${escapeHtml(name)} × ${item.quantity || 1}</span>
              <span style="color:#34D399;font-weight:800;">₹${lineTotal.toFixed(2)}</span>
            </div>`;
          }).join('');
          subtotal = 0;
          activeOrder.items.forEach(item => { subtotal += parseFloat(item.price || 0) * parseInt(item.quantity || 1); });
        }
        generateAndShowQR(subtotal, activeOrder.id);
        return;
      }
    }
  } catch (err) {
    console.log('DB fetch failed, using cart fallback:', err);
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
      subtotal = 0;
      cart.forEach(item => { subtotal += parseFloat(item.price || 0) * parseInt(item.quantity || 1); });
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
    restaurant_id: 1,
    customer_name: customerName,
    rating: rating,
    comment: comment
  };

  try {
    const res = await fetch('/api/feedback', {
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
    alert('Thank you for rating! Your 5-Star review was saved locally.');
    closeCustomerReviewModal();
  }
}

