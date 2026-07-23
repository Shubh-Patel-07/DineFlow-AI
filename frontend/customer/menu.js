const params = new URLSearchParams(window.location.search);
const restaurantId = params.get('restaurantId') || '1';
let tableId = params.get('tableId') || params.get('table') || '1';
const API_BASE = window.DINEFLOW_API_BASE || `${window.location.origin}/api`;

const translations = {
  en: { welcome: 'Welcome to', table: 'Table', all: 'All', veg: 'Veg', nonveg: 'Non-Veg', menu: 'Our Menu', subtitle: 'Freshly prepared, just for you', search: 'Search delicious food...', callWaiter: 'Call Waiter', loading: 'Loading menu…', unavailable: 'Out of stock', available: 'Available', empty: 'No dishes found', emptyHint: 'Try changing your search or filters.', waiterSent: 'Your waiter has been called.', waiterError: 'Could not call waiter. Please try again.', calling: 'Calling…' },
  hi: { welcome: 'आपका स्वागत है', table: 'टेबल', all: 'सभी', veg: 'वेज', nonveg: 'नॉन-वेज', menu: 'हमारा मेनू', subtitle: 'आपके लिए ताज़ा तैयार', search: 'खाना खोजें...', callWaiter: 'वेटर बुलाएँ', loading: 'मेनू लोड हो रहा है…', unavailable: 'स्टॉक में नहीं', available: 'उपलब्ध', empty: 'कोई डिश नहीं मिली', emptyHint: 'खोज या फ़िल्टर बदलकर देखें।', waiterSent: 'वेटर को बुला दिया गया है।', waiterError: 'वेटर को नहीं बुला सके। फिर कोशिश करें।', calling: 'बुलाया जा रहा है…' },
  gu: { welcome: 'આપનું સ્વાગત છે', table: 'ટેબલ', all: 'બધા', veg: 'વેજ', nonveg: 'નોન-વેજ', menu: 'અમારું મેનૂ', subtitle: 'તમારા માટે તાજું બનાવેલું', search: 'ખાવાનું શોધો...', callWaiter: 'વેઇટરને બોલાવો', loading: 'મેનૂ લોડ થઈ રહ્યું છે…', unavailable: 'સ્ટોકમાં નથી', available: 'ઉપલબ્ધ', empty: 'કોઈ વાનગી મળી નથી', emptyHint: 'શોધ અથવા ફિલ્ટર બદલીને જુઓ.', waiterSent: 'વેઇટરને બોલાવવામાં આવ્યા છે.', waiterError: 'વેઇટરને બોલાવી શકાયા નથી. ફરી પ્રયાસ કરો.', calling: 'બોલાવવામાં આવે છે…' }
};

let language = localStorage.getItem('dineflow-language') || 'en';
let menuItems = typeof foodItems !== 'undefined' && Array.isArray(foodItems) ? foodItems : [];
let category = 'All';
let diet = 'all';
let activeView = 'all';
let activeSuggestionIndex = -1;
const activeFilters = new Set();
const FAVORITES_STORAGE_KEY = 'dineflow-favorites';
const RECENT_STORAGE_KEY = 'dineflow-recent-items';
const THEME_STORAGE_KEY = 'dineflow-theme';

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

function renderFavoritesSection() {
  const favorites = getFavoriteItems();
  const section = $('#favoritesSection');
  if (!section) return;

  if (!favorites.length) {
    section.innerHTML = `
      <div class="favorites-section__header">
        <h3>Favorites</h3>
        <span class="favorites-section__meta">0 saved</span>
      </div>
      <p class="favorites-empty">Tap the heart icon on any dish to save it here.</p>
    `;
    return;
  }

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
    section.innerHTML = `
      <div class="favorites-section__header">
        <h3>Recently Viewed</h3>
        <button class="recent-clear" type="button" data-clear-recent disabled>Clear History</button>
      </div>
      <p class="favorites-empty">Open any dish to see it appear here.</p>
    `;
    return;
  }

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

function normalizeItem(item) {
  return { ...item, id: item.id || item._id, image: resolveImagePath(item.image), available: item.is_available ?? item.available ?? true, isVeg: item.is_veg ?? item.isVeg ?? true, rating: item.rating || '4.5', badge: item.badge || item.category || '' };
}

function applyLanguage() {
  document.documentElement.lang = language;
  document.querySelectorAll('[data-i18n]').forEach(el => { el.textContent = getText(el.dataset.i18n); });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => { el.placeholder = getText(el.dataset.i18nPlaceholder); });
  $('#languageSelect').value = language;
  renderCategories();
  renderMenu();
}

function renderCategories() {
  const categories = ['All', ...new Set(menuItems.map(item => item.category).filter(Boolean))];
  $('#categorySection').innerHTML = categories.map(item => `<button class="category-btn ${item === category ? 'active' : ''}" data-category="${escapeHtml(item)}">${item === 'All' ? getText('all') : escapeHtml(item)}</button>`).join('');
}

function filteredItems() {
  const search = $('#searchInput').value.trim().toLowerCase();
  const favorites = getFavoriteIds();
  const recentIds = new Set(getRecentItemIds());

  const matches = menuItems.filter(item => {
    const matchesCategory = category === 'All' || item.category === category;
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
  renderFavoritesSection();
  renderRecentSection();
  $('#loadingState').hidden = true;
  $('#foodGrid').innerHTML = items.length ? items.map(item => {
    const imageSrc = resolveImagePath(item.image);
    const isFavorite = favorites.has(String(item.id));
    return `<article class="food-card ${item.available ? '' : 'unavailable'}" data-item-id="${escapeHtml(item.id)}" tabindex="0" role="button" aria-label="View details for ${escapeHtml(item.name)}">
      <button class="favorite-btn ${isFavorite ? 'is-favorite' : ''}" type="button" data-favorite-toggle="${escapeHtml(item.id)}" aria-label="${isFavorite ? 'Remove from favorites' : 'Add to favorites'}">♡</button>
      <img class="food-image" src="${escapeHtml(imageSrc)}" alt="${escapeHtml(item.name)}">
      <div class="food-content">
        <div class="food-topline"><span class="food-type ${item.isVeg ? 'veg' : 'nonveg'}">${item.isVeg ? '● Veg' : '● Non-Veg'}</span><span>★ ${escapeHtml(item.rating)}</span></div>
        <h3>${escapeHtml(item.name)}</h3>
        <p>${escapeHtml(item.description || '')}</p>
        <div class="food-footer"><strong>₹${escapeHtml(item.price)}</strong><span class="stock ${item.available ? 'available' : 'out'}">${item.available ? getText('available') : getText('unavailable')}</span></div>
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

  $('#itemModal').hidden = false;
  $('#closeItemModal').focus();
}

function closeItemModal() { $('#itemModal').hidden = true; }

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
  $('#buildItemName').textContent = item.name + ` — ₹${Number(item.price).toFixed(2)}`;
  $('#buildSpicy').value = 'Medium';
  $('#buildPortion').value = 'full';
  document.querySelectorAll('.build-addon').forEach(chk => chk.checked = false);
  document.querySelectorAll('.build-remove').forEach(chk => chk.checked = false);
  $('#buildInstruction').value = '';
  updateBuildPrice();
  $('#buildPlateModal').hidden = false;
  $('#closeBuildPlateModal').focus();
}

function openBuildPlateModalFromDetails() {
  const name = $('#modalItemName').textContent;
  const item = menuItems.find(i => i.name === name) || menuItems.find(i => String(i.id) === String($('#modalItemName').dataset?.id));
  openBuildPlateModal(item);
}

function closeBuildPlateModal() { $('#buildPlateModal').hidden = true; currentBuildItem = null; }

function parseNumber(value) { const n = parseFloat(value); return isNaN(n) ? 0 : n; }

function updateBuildPrice() {
  if (!currentBuildItem) { $('#buildPrice').textContent = '₹0.00'; return; }
  const base = parseNumber(currentBuildItem.price);
  const portion = $('#buildPortion').value;
  const portionMultiplier = portion === 'half' ? 0.5 : 1;
  let addonsTotal = 0;
  document.querySelectorAll('.build-addon').forEach(chk => { if (chk.checked) addonsTotal += parseNumber(chk.dataset.price); });
  const subtotal = (base * portionMultiplier) + addonsTotal;
  $('#buildPrice').textContent = `₹${subtotal.toFixed(2)}`;
  return subtotal;
}

function buildDescription() {
  if (!currentBuildItem) return '';
  const parts = [];
  const spicy = $('#buildSpicy').value;
  const portion = $('#buildPortion').value;
  parts.push(`${spicy} spicy`);
  const removes = [...document.querySelectorAll('.build-remove')].filter(c => c.checked).map(c => c.dataset.remove);
  removes.forEach(r => parts.push(r));
  const addons = [...document.querySelectorAll('.build-addon')].filter(c => c.checked).map(c => `${c.dataset.addon}${c.dataset.price ? ` (+₹${c.dataset.price})` : ''}`);
  addons.forEach(a => parts.push(a));
  const instr = $('#buildInstruction').value.trim();
  if (instr) parts.push(`Note: ${instr}`);
  return parts.join(', ');
}

// Wire up build modal interactions
document.addEventListener('change', event => {
  if (event.target.closest('#buildPlateModal')) updateBuildPrice();
});
document.getElementById('closeBuildPlateModal').addEventListener('click', closeBuildPlateModal);
document.querySelector('#btnCancelBuild').addEventListener('click', closeBuildPlateModal);
document.getElementById('btnAddBuildToCart').addEventListener('click', () => {
  if (!currentBuildItem) return;
  const total = updateBuildPrice();
  const desc = buildDescription();
  const customized = {
    id: `${currentBuildItem.id}-${Date.now()}`,
    baseId: currentBuildItem.id,
    name: currentBuildItem.name,
    description: desc,
    price: Number(total.toFixed(2)),
    quantity: 1,
    portion: $('#buildPortion').value,
    spicy: $('#buildSpicy').value,
    addons: [...document.querySelectorAll('.build-addon')].filter(c => c.checked).map(c => ({ name: c.dataset.addon, price: parseNumber(c.dataset.price) })),
    removes: [...document.querySelectorAll('.build-remove')].filter(c => c.checked).map(c => c.dataset.remove),
    specialInstruction: $('#buildInstruction').value.trim(),
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
const saveBtn = document.getElementById('btnSavePreset');
if (saveBtn) saveBtn.addEventListener('click', saveCurrentPreset);

// Load addon availability & presets when opening build modal
const origOpenBuild = openBuildPlateModal;
openBuildPlateModal = function(item) {
  origOpenBuild(item);
  // load availability and presets
  loadAddonAvailability();
  renderPresets();
};


// --- Cart UI and interactions ---
function formatCurrency(amount) { return `₹${Number(amount || 0).toFixed(2)}`; }

function renderCart() {
  const cart = getCart();
  const badge = $('#cartBadge');
  const list = $('#cartList');
  const subtotalEl = $('#cartSubtotal');
  if (!badge || !list || !subtotalEl) return;
  badge.textContent = cart.length;
  if (!cart.length) {
    list.innerHTML = `<div style="text-align:center; opacity:0.7; padding:18px;">Your cart is empty</div>`;
    subtotalEl.textContent = formatCurrency(0);
    return;
  }
  let subtotal = 0;
  list.innerHTML = cart.map((item, idx) => {
    const name = escapeHtml(item.name);
    const desc = escapeHtml(item.description || '');
    const price = Number(item.price || 0);
    subtotal += price * (item.quantity || 1);
    return `
      <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; background: rgba(255,255,255,0.03); padding:8px; border-radius:8px;">
        <div style="flex:1">
          <div style="font-weight:700;">${name}</div>
          <div style="font-size:12px; opacity:0.8;">${desc}</div>
        </div>
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
          <div style="font-weight:800;">${formatCurrency(price)}</div>
          <div style="display:flex; gap:6px; align-items:center;">
            <select data-cart-idx="${idx}" class="cart-qty" style="background:transparent; color:inherit; border-radius:6px; padding:4px;">
              ${[1,2,3,4,5].map(q => `<option value="${q}" ${q === (item.quantity||1) ? 'selected' : ''}>${q}</option>`).join('')}
            </select>
            <button data-remove-idx="${idx}" class="cart-remove" style="background:transparent; border:none; color:#ff6b6b; font-weight:800;">Remove</button>
          </div>
        </div>
      </div>
    `;
  }).join('');
  subtotalEl.textContent = formatCurrency(subtotal);
}

function openCart() { $('#cartPanel').hidden = false; renderCart(); }
function closeCart() { $('#cartPanel').hidden = true; }

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

  const payload = {
    restaurant_id: String(restaurantId),
    table_id: String(tableId),
    special_instructions: cart.map(i => i.specialInstruction).filter(Boolean).join('; ') || '',
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
      saveCart([]);
      renderCart();
      closeCart();
      showToast(`✅ Order #${data.data.id} placed successfully! Sent to Kitchen KDS.`, false);
      startLiveOrderTracking(data.data.id);
    } else {
      showToast(data.message || 'Failed to place order', true);
    }
  } catch (err) {
    console.error('Checkout error:', err);
    showToast('Error connecting to backend API', true);
  } finally {
    if (checkoutBtn) {
      checkoutBtn.disabled = false;
      checkoutBtn.textContent = 'Checkout';
    }
  }
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
  tracker.style.display = 'block';

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


function showToast(message, isError = false) { const toast = $('#toast'); toast.textContent = message; toast.className = `toast show ${isError ? 'error' : ''}`; window.clearTimeout(showToast.timer); showToast.timer = window.setTimeout(() => { toast.className = 'toast'; }, 3500); }

async function loadMenu() {
  $('#loadingState').hidden = false;
  $('#foodGrid').innerHTML = `<div class="skeleton-grid" aria-label="Loading menu"><div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div></div>`;
  setTableOptions();
  try {
    const [restaurantResult, tableResult, menuResult, tablesResult] = await Promise.allSettled([
      request(`/restaurants/${encodeURIComponent(restaurantId)}`),
      request(`/tables/${encodeURIComponent(tableId)}`),
      request(`/menu?restaurantId=${encodeURIComponent(restaurantId)}`),
      request(`/tables/restaurant/${encodeURIComponent(restaurantId)}`)
    ]);
    if (restaurantResult.status === 'fulfilled') { const data = restaurantResult.value; $('#restaurantName').textContent = data.name || $('#restaurantName').textContent; $('#restaurantLocation').textContent = data.address || data.location || $('#restaurantLocation').textContent; if (data.logo) $('#restaurantLogo').src = resolveImagePath(data.logo); }
    const restaurantTables = tablesResult.status === 'fulfilled' && Array.isArray(tablesResult.value) ? tablesResult.value : [];
    setTableOptions(restaurantTables);
    if (tableResult.status === 'fulfilled') { const data = tableResult.value; const number = data.table_number || data.tableNumber || tableId; $('#tableNumber').options[$('#tableNumber').selectedIndex].text = `#${number}`; }
    if (menuResult.status === 'fulfilled' && Array.isArray(menuResult.value)) menuItems = menuResult.value.map(normalizeItem);
  } catch (_) {
    // Local sample menu remains available when the API server is not running.
  }
  menuItems = menuItems.map(normalizeItem);
  renderCategories(); renderMenu();
  checkTableActiveOrder();
}

async function checkTableActiveOrder() {
  try {
    const res = await fetch(`${API_BASE}/orders/restaurant/${restaurantId}`);
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      const activeOrder = data.data.find(o => String(o.table_id) === String(tableId) && o.payment_status === 'pending');
      if (activeOrder) {
        startLiveOrderTracking(activeOrder.id);
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
  const query = input.value.trim();
  const container = $('#searchSuggestions');
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
  toggle.setAttribute('aria-pressed', String(!isLight));
  toggle.setAttribute('aria-label', isLight ? 'Enable dark mode' : 'Enable light mode');
  toggle.querySelector('span').textContent = isLight ? 'Dark mode' : 'Light mode';
  toggle.firstChild.textContent = isLight ? '☾ ' : '☀ ';
  localStorage.setItem(THEME_STORAGE_KEY, selectedTheme);
}

$('#searchInput').addEventListener('input', () => { activeSuggestionIndex = -1; renderSuggestions(); renderMenu(); });
$('#searchInput').addEventListener('keydown', event => {
  const suggestions = [...document.querySelectorAll('.search-suggestion')];
  if (!suggestions.length) return;
  if (event.key === 'ArrowDown' || event.key === 'ArrowUp') { event.preventDefault(); activeSuggestionIndex = event.key === 'ArrowDown' ? (activeSuggestionIndex + 1) % suggestions.length : (activeSuggestionIndex - 1 + suggestions.length) % suggestions.length; renderSuggestions(); }
  if (event.key === 'Enter' && activeSuggestionIndex >= 0) { event.preventDefault(); selectSuggestion(suggestions[activeSuggestionIndex].dataset.suggestionId); }
  if (event.key === 'Escape') $('#searchSuggestions').hidden = true;
});
$('#searchSuggestions').addEventListener('click', event => { const suggestion = event.target.closest('[data-suggestion-id]'); if (suggestion) selectSuggestion(suggestion.dataset.suggestionId); });
document.addEventListener('click', event => { if (!event.target.closest('.search-wrapper')) $('#searchSuggestions').hidden = true; });
$('#themeToggle').addEventListener('click', () => applyTheme(document.body.classList.contains('light-theme') ? 'dark' : 'light'));
document.querySelectorAll('.filter-btn').forEach(button => button.addEventListener('click', () => { const { filter } = button.dataset; activeFilters.has(filter) ? activeFilters.delete(filter) : activeFilters.add(filter); button.classList.toggle('is-active', activeFilters.has(filter)); renderMenu(); }));
$('#sortSelect').addEventListener('change', renderMenu);
$('#languageSelect').addEventListener('change', event => { language = event.target.value; localStorage.setItem('dineflow-language', language); applyLanguage(); });
$('#tableNumber').addEventListener('change', event => { tableId = event.target.value; const nextUrl = new URL(window.location.href); nextUrl.searchParams.set('tableId', tableId); window.history.replaceState({}, '', nextUrl); loadMenu(); });
$('#categorySection').addEventListener('click', event => { const button = event.target.closest('[data-category]'); if (!button) return; category = button.dataset.category; renderCategories(); renderMenu(); });
document.querySelectorAll('.diet-btn').forEach(button => button.addEventListener('click', () => { diet = button.dataset.diet; document.querySelectorAll('.diet-btn').forEach(item => item.classList.toggle('active', item === button)); renderMenu(); }));
document.querySelectorAll('.view-btn').forEach(button => button.addEventListener('click', () => { activeView = button.dataset.view; document.querySelectorAll('.view-btn').forEach(item => item.classList.toggle('active', item === button)); renderMenu(); }));
$('#foodGrid').addEventListener('click', event => {
  const favoriteButton = event.target.closest('[data-favorite-toggle]');
  if (favoriteButton) {
    event.stopPropagation();
    toggleFavorite(favoriteButton.dataset.favoriteToggle);
    return;
  }

  const card = event.target.closest('[data-item-id]');
  if (card) openItemModal(menuItems.find(item => String(item.id) === card.dataset.itemId));
});

$('#favoritesSection').addEventListener('click', event => {
  const removeButton = event.target.closest('[data-favorite-remove-id]');
  if (!removeButton) return;
  event.stopPropagation();
  removeFavorite(removeButton.dataset.favoriteRemoveId);
});

$('#recentSection').addEventListener('click', event => {
  if (event.target.closest('[data-clear-recent]')) {
    clearRecentlyViewed();
    return;
  }
  const recentButton = event.target.closest('[data-recent-item-id]');
  if (!recentButton) return;
  const item = menuItems.find(entry => String(entry.id) === recentButton.dataset.recentItemId);
  if (item) openItemModal(item);
});
$('#foodGrid').addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { const card = event.target.closest('[data-item-id]'); if (card) { event.preventDefault(); openItemModal(menuItems.find(item => String(item.id) === card.dataset.itemId)); } } });
$('#closeItemModal').addEventListener('click', closeItemModal);
$('#itemModal').addEventListener('click', event => { if (event.target.hasAttribute('data-close-modal')) closeItemModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeItemModal(); });
$('#callWaiterBtn').addEventListener('click', async () => {
  const button = $('#callWaiterBtn');
  button.disabled = true;
  button.lastElementChild.textContent = getText('calling');
  try {
    await request('/waiter-calls', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ restaurant_id: String(restaurantId), table_id: String(tableId) }) });
    showToast(getText('waiterSent'));
  } catch (_) {
    showToast(getText('waiterError'), true);
  } finally {
    button.disabled = false;
    button.lastElementChild.textContent = getText('callWaiter');
  }
});

let tableUnpaidItems = [];

async function openSplitBillModal() {
  $('#splitUpiSection').style.display = 'none';
  $('#btnGenerateSplitUpi').style.display = 'block';
  $('#splitSubtotal').textContent = '₹0.00';
  $('#splitTax').textContent = '₹0.00';
  $('#splitTotal').textContent = '₹0.00';

  $('#splitBillTableLabel').textContent = `Table #${tableId} - Active Order Items`;

  try {
    const res = await fetch(`${API_BASE}/orders/restaurant/${restaurantId}`);
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      const activeOrder = data.data.find(order => String(order.table_id) === String(tableId) && order.payment_status === 'pending');
      
      if (!activeOrder || !Array.isArray(activeOrder.items) || activeOrder.items.length === 0) {
        $('#splitBillItemsList').innerHTML = `
          <div style="text-align: center; padding: 30px 10px; opacity: 0.6; font-size: 13px;">
            <p>No active unpaid items found on this table!</p>
          </div>
        `;
        $('#btnGenerateSplitUpi').style.display = 'none';
        $('#splitBillModal').hidden = false;
        return;
      }

      tableUnpaidItems = activeOrder.items;

      $('#splitBillItemsList').innerHTML = tableUnpaidItems.map((item, idx) => `
        <label style="display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); padding: 10px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" class="split-item-chk" value="${idx}" onchange="recalculateSplit()" style="width: 16px; height: 16px; accent-color: var(--color-primary);">
            <div style="text-align: left; font-size: 13px;">
              <div style="font-weight: 700; color: white;">${item.name}</div>
              <div style="font-size: 10px; opacity: 0.6;">₹${item.price} each</div>
            </div>
          </div>
          <div style="font-size: 12px; font-weight: 700; color: white;">
            Qty: <select class="split-item-qty" onchange="recalculateSplit()" style="background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 4px; padding: 2px 4px; font-size: 11px;">
              ${Array.from({ length: item.quantity }, (_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
            </select>
          </div>
        </label>
      `).join('');
      
    }
  } catch (err) {
    console.log('Error loading split bill items:', err);
    $('#splitBillItemsList').innerHTML = `<p style="color:red; text-align:center; font-size:12px;">Failed to load order items.</p>`;
  }

  $('#splitBillModal').hidden = false;
}

function closeSplitBillModal() {
  $('#splitBillModal').hidden = true;
}

function recalculateSplit() {
  let subtotal = 0;
  const checkboxes = document.querySelectorAll('.split-item-chk');
  const qtySelects = document.querySelectorAll('.split-item-qty');

  checkboxes.forEach((chk, index) => {
    if (chk.checked) {
      const idx = parseInt(chk.value);
      const item = tableUnpaidItems[idx];
      const selectedQty = parseInt(qtySelects[index].value);
      subtotal += parseFloat(item.price) * selectedQty;
    }
  });

  const tax = subtotal * 0.05; // 5% GST
  const total = subtotal + tax;

  $('#splitSubtotal').textContent = `₹${subtotal.toFixed(2)}`;
  $('#splitTax').textContent = `₹${tax.toFixed(2)}`;
  $('#splitTotal').textContent = `₹${total.toFixed(2)}`;
}

function generateSplitUpi() {
  const totalStr = $('#splitTotal').textContent.replace('₹', '');
  const totalAmount = parseFloat(totalStr);

  if (isNaN(totalAmount) || totalAmount <= 0) {
    alert('Please select at least one item to split!');
    return;
  }

  const merchantUpi = "7777938338@omni";
  const merchantName = "DineFlow AI Restaurant";
  const upiUrl = `upi://pay?pa=${encodeURIComponent(merchantUpi)}&pn=${encodeURIComponent(merchantName)}&am=${totalAmount.toFixed(2)}&cu=INR&tn=SplitBillTable${tableId}`;
  const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(upiUrl)}`;

  $('#splitUpiQrImg').src = qrUrl;
  $('#splitUpiSection').style.display = 'block';
  $('#btnGenerateSplitUpi').style.display = 'none';
}

applyTheme(localStorage.getItem(THEME_STORAGE_KEY) || 'dark');
applyLanguage();
loadMenu();

async function openPayFullBillModal() {
  $('#fullSubtotal').textContent = '₹0.00';
  $('#fullTax').textContent = '₹0.00';
  $('#fullTotal').textContent = '₹0.00';
  $('#fullBillTableLabel').textContent = `Table #${tableId} - Loading Bill...`;
  $('#fullUpiQrImg').src = '';

  try {
    const res = await fetch(`${API_BASE}/orders/restaurant/${restaurantId}`);
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      const activeOrder = data.data.find(order => String(order.table_id) === String(tableId) && order.payment_status === 'pending');
      
      if (!activeOrder || !Array.isArray(activeOrder.items) || activeOrder.items.length === 0) {
        alert('No active unpaid items found on this table!');
        return;
      }

      // Calculate total amount for all items in the order
      let subtotal = 0;
      activeOrder.items.forEach(item => {
        subtotal += parseFloat(item.price) * parseInt(item.quantity);
      });

      const tax = subtotal * 0.05; // 5% GST
      const total = subtotal + tax;

      $('#fullSubtotal').textContent = `₹${subtotal.toFixed(2)}`;
      $('#fullTax').textContent = `₹${tax.toFixed(2)}`;
      $('#fullTotal').textContent = `₹${total.toFixed(2)}`;
      $('#fullBillTableLabel').textContent = `Table #${tableId} - Active Order #${activeOrder.id}`;

      // Generate UPI QR for full amount!
      const merchantUpi = "7777938338@omni";
      const merchantName = "DineFlow AI Restaurant";
      const upiUrl = `upi://pay?pa=${encodeURIComponent(merchantUpi)}&pn=${encodeURIComponent(merchantName)}&am=${total.toFixed(2)}&cu=INR&tn=Table${tableId}Order${activeOrder.id}`;
      const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(upiUrl)}`;

      $('#fullUpiQrImg').src = qrUrl;
      $('#payFullBillModal').hidden = false;
    }
  } catch (err) {
    console.log('Error loading full bill:', err);
    alert('Failed to load total bill details.');
  }
}

function closePayFullBillModal() {
  $('#payFullBillModal').hidden = true;
}
