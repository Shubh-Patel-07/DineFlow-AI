/**
 * DineFlow AI - Table Management
 * Manage restaurant tables with real-time status
 */

let allTables = [];
let filteredTables = [];
let currentEditTable = null;

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => document.querySelectorAll(sel);

const toast = (msg, type = 'success') => {
  const el = document.createElement('div');
  el.className = `toast toast-${type}`;
  el.textContent = msg;
  el.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    z-index: 9999;
    animation: slideIn 0.3s ease-out;
  `;
  document.body.appendChild(el);
  setTimeout(() => el.remove(), 3000);
};

const showLoader = () => {
  const loader = $('[data-loader]');
  if (loader) loader.style.display = 'flex';
};

const hideLoader = () => {
  const loader = $('[data-loader]');
  if (loader) loader.style.display = 'none';
};

// Load all tables
async function loadTables() {
  showLoader();
  const result = await APIService.getTables();
  hideLoader();

  if (!result.success) {
    toast(result.error || 'Failed to load tables', 'error');
    return;
  }

  allTables = result.data || [];
  filteredTables = [...allTables];
  renderTables();
}

// Render tables in grid
function renderTables() {
  const container = $('[data-tables-container]');

  if (!filteredTables || filteredTables.length === 0) {
    container.innerHTML = `
      <div class="empty-state" style="grid-column: 1/-1; text-align: center; padding: 40px;">
        <p style="font-size: 48px;">🪑</p>
        <p style="color: #9ca3af;">No tables found</p>
      </div>
    `;
    return;
  }

  container.innerHTML = filteredTables
    .map((table) => {
      const statusColor = getStatusColor(table.status || 'available');
      const statusLabel = getStatusLabel(table.status || 'available');

      return `
        <div class="table-card" style="
          background: #121216;
          border: 1px solid rgba(255,255,255,0.1);
          border-radius: 12px;
          padding: 20px;
          text-align: center;
          transition: all 0.3s;
        ">
          <div style="font-size: 36px; margin-bottom: 10px;">🪑</div>
          <h3 style="color: #f3f4f6; margin: 10px 0;">Table ${table.tableNumber || table.table_number || table._id}</h3>
          
          <div style="margin: 15px 0;">
            <span style="
              display: inline-block;
              padding: 6px 12px;
              border-radius: 6px;
              background: ${statusColor};
              color: white;
              font-size: 12px;
              font-weight: 600;
            ">${statusLabel}</span>
          </div>

          <div style="color: #9ca3af; font-size: 14px; margin: 10px 0;">
            <p>Capacity: ${table.capacity || '-'} Seats</p>
            <p>Location: ${table.location || 'Not specified'}</p>
          </div>

          <div style="display: flex; gap: 8px; margin-top: 15px;">
            <button class="btn-icon" onclick="editTable('${table._id || table.id}')" style="flex: 1; padding: 8px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer;">✏️ Edit</button>
            <button class="btn-icon" onclick="deleteTable('${table._id || table.id}')" style="flex: 1; padding: 8px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">🗑️ Delete</button>
          </div>
        </div>
      `;
    })
    .join('');
}

// Get status color
function getStatusColor(status) {
  const colors = {
    'available': '#10b981',
    'occupied': '#f59e0b',
    'reserved': '#3b82f6',
    'maintenance': '#6b7280'
  };
  return colors[status] || '#6b7280';
}

// Get status label
function getStatusLabel(status) {
  const labels = {
    'available': '✅ Available',
    'occupied': '🍽️ Occupied',
    'reserved': '📅 Reserved',
    'maintenance': '🔧 Maintenance'
  };
  return labels[status] || 'Unknown';
}

// Search tables
function searchTables(query) {
  filteredTables = allTables.filter((table) => {
    const tableNum = (table.tableNumber || table.table_number || '').toString().toLowerCase();
    const location = (table.location || '').toLowerCase();
    return tableNum.includes(query.toLowerCase()) || location.includes(query.toLowerCase());
  });
  renderTables();
}

// Filter by status
function filterByStatus(status) {
  if (status === 'all') {
    filteredTables = [...allTables];
  } else {
    filteredTables = allTables.filter((table) => (table.status || 'available') === status);
  }
  renderTables();
}

// Open add table modal
function openAddTableModal() {
  currentEditTable = null;
  const modal = $('[data-form-modal]');
  if (modal) {
    $('[data-modal-title]').textContent = 'Add New Table';
    $$('[data-form-input]').forEach((input) => (input.value = ''));
    modal.style.display = 'flex';
  }
}

// Edit table
function editTable(tableId) {
  const table = allTables.find((t) => (t._id || t.id) === tableId);
  if (!table) return;

  currentEditTable = table;
  $('[data-modal-title]').textContent = 'Edit Table';
  $('[data-input-number]').value = table.tableNumber || table.table_number || '';
  $('[data-input-capacity]').value = table.capacity || '';
  $('[data-input-location]').value = table.location || '';
  $('[data-input-status]').value = table.status || 'available';

  $('[data-form-modal]').style.display = 'flex';
}

// Delete table
async function deleteTable(tableId) {
  if (!confirm('Are you sure you want to delete this table?')) return;

  showLoader();
  const result = await APIService.deleteTable(tableId);
  hideLoader();

  if (!result.success) {
    toast(result.error || 'Failed to delete table', 'error');
    return;
  }

  toast('Table deleted successfully', 'success');
  loadTables();
}

// Save table
async function saveTable() {
  const tableNumber = $('[data-input-number]').value.trim();
  const capacity = $('[data-input-capacity]').value;
  const location = $('[data-input-location]').value.trim();
  const status = $('[data-input-status]').value;

  if (!tableNumber || !capacity || !location) {
    toast('Please fill all required fields', 'error');
    return;
  }

  if (isNaN(capacity) || capacity <= 0) {
    toast('Capacity must be a positive number', 'error');
    return;
  }

  const data = {
    tableNumber,
    capacity: parseInt(capacity),
    location,
    status
  };

  showLoader();
  let result;
  if (currentEditTable) {
    result = await APIService.updateTable(currentEditTable._id || currentEditTable.id, data);
  } else {
    result = await APIService.createTable(data);
  }
  hideLoader();

  if (!result.success) {
    toast(result.error || 'Failed to save table', 'error');
    return;
  }

  toast(currentEditTable ? 'Table updated successfully' : 'Table created successfully', 'success');
  closeModal();
  loadTables();
}

// Close modal
function closeModal() {
  const modal = $('[data-form-modal]');
  if (modal) modal.style.display = 'none';
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  Auth.checkAuth();
  loadTables();
  
  // Setup event listeners
  const searchInput = $('[data-search-tables]');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => searchTables(e.target.value));
  }

  const statusFilter = $('[data-filter-status]');
  if (statusFilter) {
    statusFilter.addEventListener('change', (e) => filterByStatus(e.target.value));
  }

  const addBtn = $('[data-add-table-btn]');
  if (addBtn) {
    addBtn.addEventListener('click', openAddTableModal);
  }

  const saveBtn = $('[data-save-btn]');
  if (saveBtn) {
    saveBtn.addEventListener('click', saveTable);
  }

  const cancelBtn = $('[data-cancel-btn]');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', closeModal);
  }
});

// Auto-refresh every 10 seconds
setInterval(loadTables, 10000);
