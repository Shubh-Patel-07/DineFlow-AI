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
    const statusBadge = getStatusBadge(table.status);
    return `
      <div class="table-card" style="background: rgba(30, 41, 59, 0.45); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; padding: 28px 24px; position: relative; transition: all 0.25s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <!-- Delete Button -->
        <button onclick="removeTable(${table.id})" class="btn-delete-table" style="position: absolute; top: 14px; right: 14px; border: none; background: rgba(239, 68, 68, 0.15); color: #EF4444; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#EF4444'; this.style.color='white';" onmouseout="this.style.background='rgba(239, 68, 68, 0.15)'; this.style.color='#EF4444';" title="Remove Table">
          <i class="fa-solid fa-trash-can" style="font-size: 12px;"></i>
        </button>
        
        <!-- Table Icon & Label -->
        <div style="width: 58px; height: 58px; border-radius: 16px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; box-shadow: 0 8px 20px rgba(56, 189, 248, 0.05);">
          <i class="fa-solid fa-table-cells-large" style="font-size: 24px; color: #38BDF8;"></i>
        </div>
        
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: #FFFFFF; margin: 0 0 6px 0;">${table.table_number}</h3>
        
        <div style="margin-bottom: 24px;">
          ${statusBadge}
        </div>
        
        <!-- View QR Button -->
        <button onclick="showQrModal('${table.table_number}', '${table.qr_code_url}')" class="btn-primary" style="width: 100%; height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 12px; background: linear-gradient(135deg, #6366F1, #4F46E5); border: none; font-size: 13px; font-weight: 700; color: #FFFFFF; cursor: pointer; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 18px rgba(99, 102, 241, 0.45)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(99, 102, 241, 0.3)';">
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

async function openAddTableModal() {
  const tableNumber = prompt('Enter Table Number (e.g. T-13):');
  if (!tableNumber) return;

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
  // Let's load the tables list
  loadTables();
});
