/**
 * DineFlow AI - Table Management
 */

let allTables = [];
let currentSelectedTableId = null;
let currentSelectedTableNumber = null;
let restaurantBranding = {
  name: localStorage.getItem('dineflow_restaurant_name') || 'DineFlow Restaurant',
  logo: localStorage.getItem('dineflow_restaurant_logo') || ''
};

async function loadRestaurantBranding() {
  const restaurantId = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || '1';
  try {
    const res = await fetch(`/api/restaurants/${restaurantId}/settings`);
    if (res.ok) {
      const data = await res.json();
      if (data.success && data.data) {
        if (data.data.name) {
          restaurantBranding.name = data.data.name;
          localStorage.setItem('dineflow_restaurant_name', data.data.name);
        }
        if (data.data.logo) {
          restaurantBranding.logo = data.data.logo;
          localStorage.setItem(`dineflow_restaurant_logo_${restaurantId}`, data.data.logo);
          localStorage.setItem('dineflow_restaurant_logo', data.data.logo);
        }
      }
    }
  } catch (err) {
    console.log('Branding fetch fallback:', err);
  }
}

function showLoader() {
  const loader = document.querySelector('[data-loader]');
  if (loader) {
    loader.style.opacity = '1';
    loader.style.pointerEvents = 'all';
  }
}

function hideLoader() {
  const loader = document.querySelector('[data-loader]');
  if (loader) {
    loader.style.opacity = '0';
    loader.style.pointerEvents = 'none';
  }
}

async function loadTables() {
  showLoader();
  loadRestaurantBranding();
  const result = await APIService.getTables();
  hideLoader();

  if (!result.success) {
    Toast.error(result.error || 'Failed to load tables');
    return;
  }

  allTables = result.data || [];
  renderTables();
}

function getStatusBadge(status) {
  const normStatus = (status || 'active').toLowerCase();
  let bg = 'rgba(16, 185, 129, 0.12)';
  let color = '#34D399';
  let label = 'Available';

  if (normStatus === 'occupied' || normStatus === 'busy') {
    bg = 'rgba(239, 68, 68, 0.12)';
    color = '#F87171';
    label = 'Occupied';
  } else if (normStatus === 'inactive') {
    bg = 'rgba(148, 163, 184, 0.12)';
    color = '#94A3B8';
    label = 'Inactive';
  }

  return `<span style="background:${bg}; color:${color}; padding: 5px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; border: 1px solid rgba(${color === '#34D399' ? '52,211,153' : (color === '#F87171' ? '248,113,113' : '148,163,184')}, 0.2);">${label}</span>`;
}

function renderTables() {
  const container = document.querySelector('[data-tables-container]');
  if (!container) return;

  if (allTables.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-secondary);">
        <i class="fa-solid fa-table-cells" style="font-size: 40px; opacity: 0.15; margin-bottom: 12px;"></i>
        <p>No tables configured yet.</p>
      </div>
    `;
    return;
  }

  container.innerHTML = allTables.map(table => {
    const normStatus = (table.status || 'active').toLowerCase();
    let statusColor = '#34D399';
    let statusLabel = 'Available';
    let statusClass = 'status-dot-green';
    let statusGlow = 'rgba(52, 211, 153, 0.12)';

    if (normStatus === 'occupied' || normStatus === 'busy') {
      statusColor = '#F87171';
      statusLabel = 'Occupied';
      statusClass = 'status-dot-red';
      statusGlow = 'rgba(248, 113, 113, 0.12)';
    } else if (normStatus === 'inactive') {
      statusColor = '#94A3B8';
      statusLabel = 'Inactive';
      statusClass = 'status-dot-gray';
      statusGlow = 'rgba(148, 163, 184, 0.12)';
    }

    return `
      <div class="table-card" data-status="${normStatus}" style="background: var(--card); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 20px; text-align: center; position: relative; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; flex-direction: column; justify-content: space-between; min-height: 190px;">
        
        <!-- Header status bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 12px;">
          <span style="font-size: 9px; font-weight: 800; color: ${statusColor}; text-transform: uppercase; letter-spacing: 0.8px; background: ${statusGlow}; padding: 3px 8px; border-radius: 8px; border: 1px solid rgba(${statusColor === '#34D399' ? '52,211,153' : (statusColor === '#F87171' ? '248,113,113' : '148,163,184')}, 0.2);">${statusLabel}</span>
          <button onclick="removeTable(${table.id})" style="border: none; background: transparent; color: #64748B; cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#F87171';" onmouseout="this.style.color='#64748B';" title="Delete Table"><i class="fa-solid fa-trash-can" style="font-size: 13px;"></i></button>
        </div>

        <!-- Table Graphic Representation -->
        <div style="margin: 8px 0 16px 0; display: flex; flex-direction: column; align-items: center; gap: 6px;">
          <div style="position: relative; width: 50px; height: 50px; border-radius: 50%; background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0.02) 100%); border: 2px dashed ${statusColor}; box-shadow: 0 0 15px ${statusGlow}; display: flex; align-items: center; justify-content: center; transition: all 0.25s;">
            <i class="fa-solid fa-utensils" style="font-size: 16px; color: ${statusColor}; opacity: 0.8;"></i>
          </div>
          <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0; letter-spacing: 0.3px;">${table.table_number}</h3>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 8px;">
          <button onclick="showQrModal(this.dataset.tablenum, this.dataset.qr, this.dataset.tableid)" data-tablenum="${String(table.table_number).replace(/\"/g, '&quot;')}" data-qr="${String(table.qr_code_url).replace(/\"/g, '&quot;')}" data-tableid="${table.id}" class="btn-primary" style="flex: 1; height: 34px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; border-radius: 10px; background: linear-gradient(135deg, #6366F1, #4F46E5); border: none; font-size: 11.5px; font-weight: 700; color: #FFFFFF; cursor: pointer; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='none';">
            <i class="fa-solid fa-qrcode"></i> View QR
          </button>
          <a href="/admin/qr-poster/${table.id}" target="_blank" class="btn-secondary" style="height: 34px; padding: 0 10px; display: inline-flex; align-items: center; justify-content: center; gap: 4px; border-radius: 10px; text-decoration: none; font-size: 11px; font-weight: 700; color: var(--text-primary); border: 1px solid var(--border); background: var(--card);" title="A4 Print Poster">
            <i class="fa-solid fa-file-pdf" style="color: #EF4444;"></i> A4
          </a>
        </div>
      </div>
    `;
  }).join('');
}

function showQrModal(tableNumber, qrCodeUrl, tableId = null) {
  currentSelectedTableId = tableId;
  currentSelectedTableNumber = tableNumber;
  document.getElementById('qrModalTitle').textContent = `QR Code for Table ${tableNumber}`;
  const imgEl = document.getElementById('qrModalImage');
  if (imgEl) {
    const restaurantId = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || '1';
    imgEl.src = qrCodeUrl || `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(window.location.origin + '/customer/menu?restaurantId=' + restaurantId + '&tableId=' + (tableId || tableNumber || '1'))}`;
  }
  document.getElementById('qrModal').hidden = false;
}

function closeQrModal() {
  document.getElementById('qrModal').hidden = true;
}

function printQrCode() {
  const qrUrl = document.getElementById('qrModalImage').src;
  const tableTitle = document.getElementById('qrModalTitle').textContent;
  const tableNumber = tableTitle.replace('QR Code for Table ', '');

  const printImage = document.getElementById('qrPrintImage');
  if (printImage) printImage.src = qrUrl;
  const printTableNum = document.getElementById('qrPrintTableNumber');
  if (printTableNum) printTableNum.textContent = tableNumber;

  // Apply dynamic restaurant branding
  const restaurantId = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || '1';
  const restName = restaurantBranding.name || localStorage.getItem('dineflow_restaurant_name') || 'DineFlow Restaurant';
  const restLogo = restaurantBranding.logo || localStorage.getItem(`dineflow_restaurant_logo_${restaurantId}`) || localStorage.getItem('dineflow_restaurant_logo') || '';

  const nameEl = document.getElementById('qrPrintRestaurantName');
  if (nameEl) nameEl.textContent = restName;

  const logoEl = document.getElementById('qrPrintLogo');
  const logoBox = document.getElementById('qrPrintLogoContainer');
  if (logoEl) {
    if (restLogo) {
      logoEl.src = restLogo;
      logoEl.style.display = 'block';
      if (logoBox) logoBox.style.display = 'flex';
    } else {
      logoEl.src = '/images/dineflow_logo.jpg';
      logoEl.style.display = 'block';
      if (logoBox) logoBox.style.display = 'flex';
    }
  }

  window.print();
}

function openTableA4Poster() {
  if (currentSelectedTableId) {
    window.open(`/admin/qr-poster/${currentSelectedTableId}`, '_blank');
  } else {
    const matched = allTables.find(t => String(t.table_number) === String(currentSelectedTableNumber));
    const tId = matched ? matched.id : 1;
    window.open(`/admin/qr-poster/${tId}`, '_blank');
  }
}

function openAddTableModal() {
  document.getElementById('newTableNumberInput').value = '';
  document.getElementById('addTableModal').hidden = false;
  setTimeout(() => {
    document.getElementById('newTableNumberInput').focus();
  }, 100);
}

function closeAddTableModal() {
  document.getElementById('addTableModal').hidden = true;
}

async function submitAddTable() {
  const tableNumber = document.getElementById('newTableNumberInput').value.trim();
  if (!tableNumber) {
    Toast.error('Please enter a table number!');
    return;
  }

  closeAddTableModal();
  showLoader();
  const result = await APIService.addTable(tableNumber);
  hideLoader();

  if (result.success) {
    Toast.success('Table added successfully!');
    loadTables();
  } else {
    Toast.error(result.error || 'Failed to add table');
  }
}

async function removeTable(tableId) {
  if (!confirm('Are you sure you want to remove this table? This will delete the QR code as well.')) return;

  showLoader();
  const result = await APIService.deleteTable(tableId);
  hideLoader();

  if (result.success) {
    Toast.success('Table removed successfully!');
    loadTables();
  } else {
    Toast.error(result.error || 'Failed to remove table');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  Auth.checkAuth();
  loadTables();
  loadReservations();
});

let allReservations = [];

async function loadReservations() {
  const tbody = document.getElementById('reservationsTableBody');
  if (!tbody) return;

  try {
    const res = await fetch('/api/reservations/restaurant/1', {
      headers: { 'Accept': 'application/json' }
    });
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      allReservations = data.data;
    } else {
      allReservations = [];
    }
  } catch (err) {
    allReservations = JSON.parse(localStorage.getItem('dineflow_my_reservations') || '[]');
  }

  renderReservations();
}

function renderReservations() {
  const tbody = document.getElementById('reservationsTableBody');
  if (!tbody) return;

  if (!allReservations.length) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-muted);">
          <div style="font-size: 28px; opacity: 0.3; margin-bottom: 8px;">📅</div>
          <p style="margin: 0;">No table reservations received yet.</p>
        </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = allReservations.map(r => {
    const statusMeta = {
      pending:   { label: 'Pending',   color: '#F59E0B', bg: 'rgba(245,158,11,0.12)' },
      confirmed: { label: 'Confirmed', color: '#10B981', bg: 'rgba(16,185,129,0.12)' },
      seated:    { label: 'Seated',    color: '#6366F1', bg: 'rgba(99,102,241,0.12)' },
      cancelled: { label: 'Cancelled', color: '#EF4444', bg: 'rgba(239,68,68,0.12)' }
    }[r.status] || { label: r.status, color: '#94A3B8', bg: 'rgba(148,163,184,0.12)' };

    return `
      <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
        <td style="padding: 14px 16px; font-weight: 700; color: var(--text-primary);">${escapeHtml(r.customer_name)}</td>
        <td style="padding: 14px 16px; color: var(--text-secondary);"><a href="tel:${r.customer_phone}" style="color:#818CF8; text-decoration:none;">${escapeHtml(r.customer_phone)}</a></td>
        <td style="padding: 14px 16px; color: var(--text-primary);">${r.reservation_date} <span style="font-size: 11px; color: var(--text-muted);">(${r.time_slot})</span></td>
        <td style="padding: 14px 16px; font-weight: 700; color: var(--text-primary);">${r.pax_count} PAX</td>
        <td style="padding: 14px 16px; color: var(--text-muted); font-size: 12px;">${escapeHtml(r.special_requests || '—')}</td>
        <td style="padding: 14px 16px;">
          <span style="background: ${statusMeta.bg}; color: ${statusMeta.color}; border: 1px solid ${statusMeta.color}40; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700;">
            ${statusMeta.label}
          </span>
        </td>
        <td style="padding: 14px 16px; text-align: right;">
          <div style="display: inline-flex; gap: 6px;">
            ${r.status === 'pending' ? `
              <button onclick="updateReservationStatus(${r.id}, 'confirmed')" title="Confirm Booking" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34D399; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">Confirm</button>
              <button onclick="updateReservationStatus(${r.id}, 'cancelled')" title="Decline" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #F87171; padding: 5px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">✕</button>
            ` : ''}
            ${r.status === 'confirmed' ? `
              <button onclick="updateReservationStatus(${r.id}, 'seated')" title="Seat Guest at Table" style="background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); color: #A5B4FC; padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer;">🪑 Seat Guest</button>
            ` : ''}
          </div>
        </td>
      </tr>
    `;
  }).join('');
}

async function updateReservationStatus(id, newStatus) {
  try {
    const res = await fetch(`/api/reservations/${id}/status`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      },
      body: JSON.stringify({ status: newStatus })
    });
    if (res.ok) {
      Toast.success(`Reservation marked as ${newStatus}!`);
      loadReservations();
    }
  } catch (err) {
    Toast.error('Failed to update reservation');
  }
}

function escapeHtml(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const isMobile = window.innerWidth <= 768;
  
  if (isMobile) {
    sidebar.classList.toggle('mobile-open');
  } else {
    sidebar.classList.toggle('collapsed');
  }
}
