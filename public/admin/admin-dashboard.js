/**
 * DineFlow AI — Admin Dashboard Controller
 */

document.addEventListener('DOMContentLoaded', () => {
  loadDashboardStats();
});

const RESTAURANT_ID = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || '1';

function loadDashboardStats() {
  fetch(`/api/analytics/restaurant/${RESTAURANT_ID}/sales-report?filter=today`)
    .then(res => res.json())
    .then(data => {
      if (data.success && data.data && data.data.summary) {
        const s = data.data.summary;
        if (document.getElementById('totalOrders')) document.getElementById('totalOrders').textContent = s.total_orders || 0;
        if (document.getElementById('totalRevenue')) document.getElementById('totalRevenue').textContent = '₹' + (s.gross_revenue ? s.gross_revenue.toFixed(2) : '0.00');
        if (document.getElementById('activeUsers')) document.getElementById('activeUsers').textContent = s.total_orders > 0 ? (s.total_orders * 2) : 0;
        if (document.getElementById('pendingOrders')) document.getElementById('pendingOrders').textContent = 0;
      }
    })
    .catch(() => {
      if (document.getElementById('totalOrders')) document.getElementById('totalOrders').textContent = '0';
      if (document.getElementById('totalRevenue')) document.getElementById('totalRevenue').textContent = '₹0.00';
      if (document.getElementById('activeUsers')) document.getElementById('activeUsers').textContent = '0';
      if (document.getElementById('pendingOrders')) document.getElementById('pendingOrders').textContent = '0';
    });
}

function logout() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  window.location.href = '/login';
}

function openAddUserModal() {
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'flex';
}

function closeAddUserModal() {
  const modal = document.getElementById('addUserModal');
  if (modal) modal.style.display = 'none';
}

function handleAddUser(event) {
  event.preventDefault();
  const name = document.getElementById('userName')?.value;
  const email = document.getElementById('userEmail')?.value;
  const role = document.getElementById('userRole')?.value;
  const status = document.getElementById('userStatus')?.value;

  fetch('/api/users', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({ name, email, role, status })
  })
  .then(res => res.json())
  .then(() => {
    alert('User added successfully!');
    closeAddUserModal();
  })
  .catch(() => {
    alert('Failed to add user. Please try again.');
  });
}
