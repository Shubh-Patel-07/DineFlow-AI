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
    return `
      <div class="table-card">
        <button onclick="removeTable(${table.id})" class="btn-delete-table" style="position: absolute; top: 12px; right: 12px; border: none; background: transparent; color: var(--danger); font-size: 15px; cursor: pointer; opacity: 0.6; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.6" title="Remove Table">
          <i class="fa-solid fa-trash-can"></i>
        </button>
        <i class="fa-solid fa-table-cells-large" style="font-size: 32px; color: #38BDF8; margin-bottom: 12px; opacity: 0.85;"></i>
        <h3 style="font-family: var(--font-display); font-size: 20px; font-weight: 800; margin-bottom: 4px;">${table.table_number}</h3>
        <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 16px;">Status: ${table.status}</p>
        <a href="${table.qr_code_url}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: var(--primary); font-size: 12.5px; font-weight: 700;">
          <i class="fa-solid fa-qrcode"></i> View QR Code
        </a>
      </div>
    `;
  }).join('');
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
