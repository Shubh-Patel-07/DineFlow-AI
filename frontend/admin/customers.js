// DineFlow AI - Customer & Loyalty Controller

let customerList = [
  { name: 'Rahul Kumar', phone: '+91 98765 43210', visits: 14, spent: 4850, points: 485, tier: 'VIP Gold' },
  { name: 'Priya Sharma', phone: '+91 98123 45678', visits: 8, spent: 2900, points: 290, tier: 'Silver' },
  { name: 'Amit Patel', phone: '+91 97234 56789', visits: 22, spent: 9400, points: 940, tier: 'Platinum' },
  { name: 'Neha Gupta', phone: '+91 96345 67890', visits: 5, spent: 1450, points: 145, tier: 'Silver' },
  { name: 'Virendra Singh', phone: '+91 95456 78901', visits: 11, spent: 4100, points: 410, tier: 'VIP Gold' }
];

function initCustomers() {
  if (typeof Auth !== 'undefined' && Auth.checkAuth) Auth.checkAuth();
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'none';
  renderCustomers();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCustomers);
} else {
  initCustomers();
}


function renderCustomers() {
  const tbody = document.getElementById('customerTableBody');
  if (!tbody) return;

  tbody.innerHTML = customerList.map(c => `
    <tr>
      <td style="font-weight:700; color:#F8FAFC;">${escapeHtml(c.name)}</td>
      <td style="color:#94A3B8;">${escapeHtml(c.phone)}</td>
      <td style="font-weight:600; color:#6366F1;">${c.visits} visits</td>
      <td style="font-weight:800; color:#10B981;">₹${c.spent.toFixed(2)}</td>
      <td style="font-weight:700; color:#F59E0B;"><i class="fa-solid fa-star" style="margin-right:4px;"></i> ${c.points} pts</td>
      <td><span style="padding:4px 10px; border-radius:12px; font-size:11px; font-weight:700; background:rgba(99,102,241,0.15); color:#818CF8;">${c.tier}</span></td>
    </tr>
  `).join('');
}

function escapeHtml(str) { return typeof str === 'string' ? str.replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]) : ''; }
