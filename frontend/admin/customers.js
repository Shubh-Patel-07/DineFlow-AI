// DineFlow AI - Customer & Loyalty Controller (Fixed v2 - uses real DB API)

let customerList = [];

function initCustomers() {
  if (typeof Auth !== 'undefined' && Auth.checkAuth) Auth.checkAuth();
  loadCustomers();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCustomers);
} else {
  initCustomers();
}

async function loadCustomers() {
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'flex';

  try {
    const res = await fetch('/api/customers?restaurantId=1');
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      customerList = data.data;
    } else {
      customerList = [];
    }
  } catch (err) {
    console.error('Error loading customers:', err);
    customerList = [];
  } finally {
    if (loader) loader.style.display = 'none';
    renderCustomers();
  }
}

function getTier(spent) {
  if (spent >= 5000)  return { label: 'Platinum', color: '#7C3AED', bg: 'rgba(124,58,237,0.15)' };
  if (spent >= 2000)  return { label: 'VIP Gold', color: '#D97706', bg: 'rgba(245,158,11,0.15)' };
  if (spent >= 500)   return { label: 'Silver',   color: '#475569', bg: 'rgba(100,116,139,0.15)' };
  return { label: 'New',       color: '#6366F1', bg: 'rgba(99,102,241,0.15)' };
}

function renderCustomers() {
  const tbody = document.getElementById('customerTableBody');
  if (!tbody) return;

  if (!customerList.length) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">
      <i class="fa-solid fa-users" style="font-size:32px; opacity:.3; display:block; margin-bottom:12px;"></i>
      No customers yet. Click "+ Add / Edit Customer Points" above to add your first customer.
    </td></tr>`;
    return;
  }

  tbody.innerHTML = customerList.map(c => {
    const spent  = parseFloat(c.total_spent || 0);
    const tier   = getTier(spent);
    const visits = parseInt(c.total_visits || 0);
    const points = parseInt(c.loyalty_points || 0);
    const lastVisit = c.last_visit
      ? new Date(c.last_visit).toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' })
      : '—';

    return `
    <tr>
      <td>
        <div style="display:flex; align-items:center; gap:10px;">
          <div style="width:34px; height:34px; border-radius:50%; background:rgba(99,102,241,0.15); color:var(--primary); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px;">
            ${escapeHtml((c.name || '?')[0].toUpperCase())}
          </div>
          <div>
            <div style="font-weight:700; color:var(--text-primary);">${escapeHtml(c.name)}</div>
            <div style="font-size:11px; color:var(--text-muted);">${escapeHtml(c.email || '')}</div>
          </div>
        </div>
      </td>
      <td style="color:var(--text-primary); font-weight:600;">${escapeHtml(c.phone || '—')}</td>
      <td style="font-weight:600; color:#6366F1;">${visits} visits</td>
      <td style="font-weight:800; color:#10B981;">₹${spent.toLocaleString('en-IN', {minimumFractionDigits:0})}</td>
      <td style="font-weight:800; color:#F59E0B;"><i class="fa-solid fa-star" style="margin-right:4px;"></i>${points} pts</td>
      <td style="font-size:11px; color:var(--text-muted);">${lastVisit}</td>
      <td>
        <span style="padding:4px 10px; border-radius:12px; font-size:11px; font-weight:800; background:${tier.bg}; color:${tier.color};">
          ${tier.label}
        </span>
      </td>
      <td>
        <div style="display:flex; gap:6px;">
          <button onclick="editCustomerPoints(${c.id})" style="background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.3); color:#D97706; padding:5px 10px; border-radius:8px; font-size:11px; font-weight:800; cursor:pointer;" title="Edit Points">
            <i class="fa-solid fa-pen-to-square"></i> Edit Points
          </button>
          <button onclick="deleteCustomer(${c.id})" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#EF4444; padding:5px 9px; border-radius:8px; font-size:11px; font-weight:800; cursor:pointer;" title="Delete">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function openAddCustomerModal() {
  document.getElementById('custEditId').value = '';
  document.getElementById('custName').value = '';
  document.getElementById('custPhone').value = '';
  document.getElementById('custEmail').value = '';
  document.getElementById('custPoints').value = '100';
  document.getElementById('custNotes').value = '';

  const modal = document.getElementById('customerModal') || document.getElementById('addCustomerModal');
  if (modal) modal.style.display = 'flex';
}

function editCustomerPoints(id) {
  const c = customerList.find(cust => String(cust.id) === String(id));
  if (!c) return;
  document.getElementById('custEditId').value = c.id;
  document.getElementById('custName').value = c.name;
  document.getElementById('custPhone').value = c.phone || '';
  document.getElementById('custEmail').value = c.email || '';
  document.getElementById('custPoints').value = c.loyalty_points || c.points || 0;
  document.getElementById('custNotes').value = c.notes || '';

  const modal = document.getElementById('customerModal') || document.getElementById('addCustomerModal');
  if (modal) modal.style.display = 'flex';
}

function closeCustomerModal() {
  const modal = document.getElementById('customerModal') || document.getElementById('addCustomerModal');
  if (modal) modal.style.display = 'none';
}

async function handleSaveCustomer(e) {
  e.preventDefault();
  const id     = document.getElementById('custEditId')?.value;
  const name   = (document.getElementById('custName')?.value || '').trim();
  const phone  = (document.getElementById('custPhone')?.value || '').trim();
  const email  = (document.getElementById('custEmail')?.value || '').trim();
  const points = parseInt(document.getElementById('custPoints')?.value || '0');
  const notes  = (document.getElementById('custNotes')?.value || '').trim();

  if (!name) { 
    if (typeof Toast !== 'undefined') Toast.error('Customer name is required!'); 
    else alert('Customer name is required!');
    return; 
  }

  const payload = { restaurant_id: 1, name, phone, email, loyalty_points: points, notes };
  const url = id ? `/api/customers/${id}` : '/api/customers';
  const method = id ? 'PUT' : 'POST';

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

    if (res.ok) {
      if (typeof Toast !== 'undefined') Toast.success(`Customer "${name}" saved!`);
      else alert(`Customer "${name}" saved!`);
      closeCustomerModal();
      loadCustomers();
    } else {
      const err = await res.json();
      if (typeof Toast !== 'undefined') Toast.error(err.message || 'Failed to save customer.');
      else alert(err.message || 'Failed to save customer.');
    }
  } catch (err) {
    if (typeof Toast !== 'undefined') Toast.error('Error saving customer: ' + (err.message || 'Network error'));
    else alert('Error saving customer.');
  }
}

async function deleteCustomer(id) {
  if (!confirm('Delete this customer record?')) return;
  try {
    const res = await fetch(`/api/customers/${id}`, {
      method: 'DELETE',
      headers: { 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    });
    if (res.ok) {
      if (typeof Toast !== 'undefined') Toast.success('Customer deleted.');
      loadCustomers();
    }
  } catch (_) { 
    if (typeof Toast !== 'undefined') Toast.error('Network error.'); 
  }
}

function escapeHtml(str) {
  return typeof str === 'string' ? str.replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]) : '';
}
