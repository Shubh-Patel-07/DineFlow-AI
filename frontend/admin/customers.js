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
  if (spent >= 5000)  return { label: 'Platinum', color: '#E2E8F0' };
  if (spent >= 2000)  return { label: 'VIP Gold', color: '#F59E0B' };
  if (spent >= 500)   return { label: 'Silver',   color: '#94A3B8' };
  return { label: 'New',       color: '#6366F1' };
}

function renderCustomers() {
  const tbody = document.getElementById('customerTableBody');
  if (!tbody) return;

  if (!customerList.length) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:40px; color:#94A3B8;">
      <i class="fa-solid fa-users" style="font-size:32px; opacity:.3; display:block; margin-bottom:12px;"></i>
      No customers yet.
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
          <div style="width:34px; height:34px; border-radius:50%; background:rgba(99,102,241,0.2); color:#A5B4FC; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px;">
            ${escapeHtml((c.name || '?')[0].toUpperCase())}
          </div>
          <div>
            <div style="font-weight:700; color:#F8FAFC;">${escapeHtml(c.name)}</div>
            <div style="font-size:11px; color:#64748B;">${escapeHtml(c.email || '')}</div>
          </div>
        </div>
      </td>
      <td style="color:#94A3B8;">${escapeHtml(c.phone || '—')}</td>
      <td style="font-weight:600; color:#6366F1;">${visits} visits</td>
      <td style="font-weight:800; color:#10B981;">₹${spent.toLocaleString('en-IN', {minimumFractionDigits:0})}</td>
      <td style="font-weight:700; color:#F59E0B;"><i class="fa-solid fa-star" style="margin-right:4px;"></i>${points} pts</td>
      <td style="font-size:11px; color:#64748B;">${lastVisit}</td>
      <td>
        <span style="padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700; background:rgba(99,102,241,0.15); color:${tier.color};">
          ${tier.label}
        </span>
      </td>
    </tr>`;
  }).join('');
}

function openAddCustomerModal() {
  const modal = document.getElementById('customerModal') || document.getElementById('addCustomerModal');
  if (modal) modal.style.display = 'flex';
}
function closeCustomerModal() {
  const modal = document.getElementById('customerModal') || document.getElementById('addCustomerModal');
  if (modal) modal.style.display = 'none';
}

async function handleSaveCustomer(e) {
  e.preventDefault();
  const name  = (document.getElementById('custName')?.value || '').trim();
  const phone = (document.getElementById('custPhone')?.value || '').trim();
  const email = (document.getElementById('custEmail')?.value || '').trim();
  const notes = (document.getElementById('custNotes')?.value || '').trim();

  if (!name) { Toast.error('Customer name is required!'); return; }

  const payload = { restaurant_id: 1, name, phone, email, notes };

  try {
    const res = await fetch('/api/customers', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
      body: JSON.stringify(payload)
    });
    if (res.ok) {
      Toast.success(`Customer "${name}" added!`);
      closeCustomerModal();
      loadCustomers();
    } else {
      const err = await res.json();
      Toast.error(err.message || 'Failed to add customer.');
    }
  } catch (_) {
    Toast.error('Network error.');
  }
}

async function deleteCustomer(id) {
  if (!confirm('Delete this customer record?')) return;
  try {
    const res = await fetch(`/api/customers/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
    });
    if (res.ok) {
      Toast.success('Customer deleted.');
      loadCustomers();
    }
  } catch (_) { Toast.error('Network error.'); }
}

function escapeHtml(str) {
  return typeof str === 'string' ? str.replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]) : '';
}
