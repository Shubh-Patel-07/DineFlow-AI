/**
 * DineFlow AI - Table Management
 */

let allTables = [];

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
      <div class="table-card" data-status="${normStatus}" style="background: rgba(30, 41, 59, 0.45); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 20px; text-align: center; position: relative; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: flex; flex-direction: column; justify-content: space-between; min-height: 190px;">
        
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
          <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; letter-spacing: 0.3px;">${table.table_number}</h3>
        </div>

        <!-- Action Button -->
        <button onclick="showQrModal(this.dataset.tablenum, this.dataset.qr)" data-tablenum="${String(table.table_number).replace(/\"/g, '&quot;')}" data-qr="${String(table.qr_code_url).replace(/\"/g, '&quot;')}" class="btn-primary" style="width: 100%; height: 34px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; border-radius: 10px; background: linear-gradient(135deg, #6366F1, #4F46E5); border: none; font-size: 11.5px; font-weight: 700; color: #FFFFFF; cursor: pointer; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)';" onmouseout="this.style.transform='none';">
          <i class="fa-solid fa-qrcode"></i> View QR Code
        </button>
      </div>
    `;
  }).join('');
}

function showQrModal(tableNumber, qrCodeUrl) {
  document.getElementById('qrModalTitle').textContent = `QR Code for Table ${tableNumber}`;
  document.getElementById('qrModalImage').src = qrCodeUrl;
  document.getElementById('qrModal').hidden = false;
}

function closeQrModal() {
  document.getElementById('qrModal').hidden = true;
}

function printQrCode() {
  const qrUrl = document.getElementById('qrModalImage').src;
  const tableTitle = document.getElementById('qrModalTitle').textContent;
  const tableNumber = tableTitle.replace('QR Code for Table ', '');

  document.getElementById('qrPrintImage').src = qrUrl;
  document.getElementById('qrPrintTableNumber').textContent = tableNumber;

  window.print();
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
  // Let's load the tables list
  loadTables();
});

function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const isMobile = window.innerWidth <= 768;
  
  if (isMobile) {
    sidebar.classList.toggle('mobile-open');
  } else {
    sidebar.classList.toggle('collapsed');
  }
}
