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

const $ = (selector) => document.querySelector(selector);
const getText = (key) => translations[language][key] || translations.en[key] || key;
const escapeHtml = (value = '') => String(value).replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[char]);

function unwrap(response) { return response && response.data ? response.data : response; }

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
  return { ...item, id: item.id || item._id, image: item.image || 'assests/images/p1.png', available: item.is_available ?? item.available ?? true, isVeg: item.is_veg ?? item.isVeg ?? true, rating: item.rating || '4.5', badge: item.badge || item.category || '' };
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
  return menuItems.filter(item => (category === 'All' || item.category === category) && (diet === 'all' || (diet === 'veg' ? item.isVeg : !item.isVeg)) && (!search || `${item.name} ${item.description || ''}`.toLowerCase().includes(search)));
}

function renderMenu() {
  const items = filteredItems();
  $('#loadingState').hidden = true;
  $('#foodGrid').innerHTML = items.length ? items.map(item => `<article class="food-card ${item.available ? '' : 'unavailable'}" data-item-id="${escapeHtml(item.id)}" tabindex="0" role="button" aria-label="View details for ${escapeHtml(item.name)}"><img class="food-image" src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}"><div class="food-content"><div class="food-topline"><span class="food-type ${item.isVeg ? 'veg' : 'nonveg'}">${item.isVeg ? '● Veg' : '● Non-Veg'}</span><span>★ ${escapeHtml(item.rating)}</span></div><h3>${escapeHtml(item.name)}</h3><p>${escapeHtml(item.description || '')}</p><div class="food-footer"><strong>₹${escapeHtml(item.price)}</strong><span class="stock ${item.available ? 'available' : 'out'}">${item.available ? getText('available') : getText('unavailable')}</span></div></div></article>`).join('') : `<div class="empty-state"><h3>${getText('empty')}</h3><p>${getText('emptyHint')}</p></div>`;
}

async function openItemModal(item) {
  if (!item) return;
  $('#modalItemImage').src = item.image;
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

function showToast(message, isError = false) { const toast = $('#toast'); toast.textContent = message; toast.className = `toast show ${isError ? 'error' : ''}`; window.clearTimeout(showToast.timer); showToast.timer = window.setTimeout(() => { toast.className = 'toast'; }, 3500); }

async function loadMenu() {
  $('#loadingState').hidden = false;
  setTableOptions();
  try {
    const [restaurantResult, tableResult, menuResult, tablesResult] = await Promise.allSettled([
      request(`/restaurants/${encodeURIComponent(restaurantId)}`),
      request(`/tables/${encodeURIComponent(tableId)}`),
      request(`/menu?restaurantId=${encodeURIComponent(restaurantId)}`),
      request(`/tables/restaurant/${encodeURIComponent(restaurantId)}`)
    ]);
    if (restaurantResult.status === 'fulfilled') { const data = restaurantResult.value; $('#restaurantName').textContent = data.name || $('#restaurantName').textContent; $('#restaurantLocation').textContent = data.address || data.location || $('#restaurantLocation').textContent; if (data.logo) $('#restaurantLogo').src = data.logo; }
    const restaurantTables = tablesResult.status === 'fulfilled' && Array.isArray(tablesResult.value) ? tablesResult.value : [];
    setTableOptions(restaurantTables);
    if (tableResult.status === 'fulfilled') { const data = tableResult.value; const number = data.table_number || data.tableNumber || tableId; $('#tableNumber').options[$('#tableNumber').selectedIndex].text = `#${number}`; }
    if (menuResult.status === 'fulfilled' && Array.isArray(menuResult.value)) menuItems = menuResult.value.map(normalizeItem);
  } catch (_) {
    // Local sample menu remains available when the API server is not running.
  }
  menuItems = menuItems.map(normalizeItem);
  renderCategories(); renderMenu();
}

$('#searchInput').addEventListener('input', renderMenu);
$('#languageSelect').addEventListener('change', event => { language = event.target.value; localStorage.setItem('dineflow-language', language); applyLanguage(); });
$('#tableNumber').addEventListener('change', event => { tableId = event.target.value; const nextUrl = new URL(window.location.href); nextUrl.searchParams.set('tableId', tableId); window.history.replaceState({}, '', nextUrl); loadMenu(); });
$('#categorySection').addEventListener('click', event => { const button = event.target.closest('[data-category]'); if (!button) return; category = button.dataset.category; renderCategories(); renderMenu(); });
document.querySelectorAll('.diet-btn').forEach(button => button.addEventListener('click', () => { diet = button.dataset.diet; document.querySelectorAll('.diet-btn').forEach(item => item.classList.toggle('active', item === button)); renderMenu(); }));
$('#foodGrid').addEventListener('click', event => { const card = event.target.closest('[data-item-id]'); if (card) openItemModal(menuItems.find(item => String(item.id) === card.dataset.itemId)); });
$('#foodGrid').addEventListener('keydown', event => { if (event.key === 'Enter' || event.key === ' ') { const card = event.target.closest('[data-item-id]'); if (card) { event.preventDefault(); openItemModal(menuItems.find(item => String(item.id) === card.dataset.itemId)); } } });
$('#closeItemModal').addEventListener('click', closeItemModal);
$('#itemModal').addEventListener('click', event => { if (event.target.hasAttribute('data-close-modal')) closeItemModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeItemModal(); });
$('#callWaiterBtn').addEventListener('click', async () => { const button = $('#callWaiterBtn'); button.disabled = true; button.lastElementChild.textContent = getText('calling'); try { await request('/waiter-calls', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ restaurant_id: String(restaurantId), table_id: String(tableId) }) }); showToast(getText('waiterSent')); } catch (_) { showToast(getText('waiterError'), true); } finally { button.disabled = false; button.lastElementChild.textContent = getText('callWaiter'); } });

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
