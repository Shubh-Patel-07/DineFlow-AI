/**
 * STAFF & ROLES PERMISSION MANAGER
 */

const MODULE_DEFINITIONS = [
  { key: 'pos',       name: 'POS Terminal',       icon: 'fa-cash-register' },
  { key: 'menu',      name: 'Menu Items',         icon: 'fa-utensils' },
  { key: 'reports',   name: 'Sales Reports',      icon: 'fa-chart-line' },
  { key: 'inventory', name: 'Inventory',          icon: 'fa-boxes-stacked' },
  { key: 'coupons',   name: 'Coupons',            icon: 'fa-ticket' },
  { key: 'customers', name: 'Customers',          icon: 'fa-users' },
  { key: 'tables',    name: 'Table Manager',      icon: 'fa-table-cells-large' },
  { key: 'staff',     name: 'Staff & Roles',      icon: 'fa-user-shield' },
  { key: 'settings',  name: 'System Settings',    icon: 'fa-gear' },
  { key: 'feedback',  name: 'Reviews & Feedback', icon: 'fa-star' }
];

document.addEventListener('DOMContentLoaded', () => {
  renderActiveRoleSwitcherBar();
  renderRolePermissionToggles();
  loadSavedStaffMembers();
});

function openAddStaffModal() {
  const modal = document.getElementById('addStaffModal');
  if (!modal) return;
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
}

function closeAddStaffModal() {
  const modal = document.getElementById('addStaffModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

function saveNewStaffMember(event) {
  event.preventDefault();
  const name = document.getElementById('staffNameInput').value.trim();
  const role = document.getElementById('staffRoleSelect').value;
  const pin = document.getElementById('staffPinInput').value.trim();

  if (!name || !pin) {
    if (typeof Toast !== 'undefined') Toast.error('Please fill in Staff Name and PIN');
    return;
  }

  const roleMeta = {
    admin:   { title: 'System Administrator', badge: 'Full Control', gradient: 'linear-gradient(135deg, #6366F1, #4F46E5)', icon: 'fa-crown', desc: 'Full access to financial analytics, inventory, and POS.' },
    manager: { title: 'Store Manager', badge: 'Managerial Access', gradient: 'linear-gradient(135deg, #10B981, #059669)', icon: 'fa-user-tie', desc: 'Access to menu manager, stock inventory, and Z-Reports.' },
    cashier: { title: 'Head Cashier', badge: 'POS & Billing', gradient: 'linear-gradient(135deg, #F59E0B, #D97706)', icon: 'fa-calculator', desc: 'Access to real-time billing, thermal receipt printing, and WhatsApp invoice.' },
    kitchen: { title: 'Kitchen Master Chef', badge: 'KOT & Kitchen Display', gradient: 'linear-gradient(135deg, #EF4444, #DC2626)', icon: 'fa-fire-burner', desc: 'Access to live KDS, KOT tickets, and dish prep status.' },
    waiter:  { title: 'Table Captain / Waiter', badge: 'Floor & Table Orders', gradient: 'linear-gradient(135deg, #06B6D4, #0891B2)', icon: 'fa-person-walking', desc: 'Access to table ordering, waiter call response, and status updates.' }
  }[role] || { title: name, badge: role, gradient: 'linear-gradient(135deg, #6366F1, #4F46E5)', icon: 'fa-user', desc: 'Custom staff access credentials.' };

  const newStaff = { id: Date.now(), name, role, pin, meta: roleMeta };
  
  const savedStaff = JSON.parse(localStorage.getItem('dineflow_staff_members') || '[]');
  savedStaff.push(newStaff);
  localStorage.setItem('dineflow_staff_members', JSON.stringify(savedStaff));

  appendStaffCardToGrid(newStaff);
  closeAddStaffModal();
  document.getElementById('addStaffForm').reset();

  if (typeof Toast !== 'undefined') {
    Toast.success(`New Staff Member "${name}" added successfully! PIN: ${pin}`);
  } else {
    alert(`New Staff Member "${name}" added successfully! Quick PIN: ${pin}`);
  }
}

function appendStaffCardToGrid(staff) {
  const grid = document.getElementById('staffGrid');
  if (!grid) return;

  const card = document.createElement('div');
  card.className = 'staff-card';
  card.setAttribute('data-role-card', staff.role);
  card.innerHTML = `
    <div class="staff-header">
      <div class="staff-avatar" style="background:${staff.meta.gradient};"><i class="fa-solid ${staff.meta.icon}"></i></div>
      <div>
        <h3 style="font-family:'Outfit',sans-serif; font-size:17px; font-weight:700; color:var(--text-primary); margin:0 0 4px;">${staff.name}</h3>
        <span class="role-badge ${staff.role}">${staff.meta.badge}</span>
      </div>
    </div>
    <div class="pin-box">
      <span style="font-size:11.5px; color:var(--text-muted); font-weight:600;">QUICK LOGIN PIN</span>
      <span class="pin-code">${staff.pin}</span>
    </div>
    <p style="font-size:12px; color:var(--text-muted); margin:0;">${staff.meta.desc}</p>
  `;
  grid.appendChild(card);
}

function loadSavedStaffMembers() {
  const savedStaff = JSON.parse(localStorage.getItem('dineflow_staff_members') || '[]');
  savedStaff.forEach(appendStaffCardToGrid);
}

function renderActiveRoleSwitcherBar() {
  const container = document.getElementById('roleSwitcherBarContainer');
  if (!container) return;

  const activeRole = window.DineFlowRBAC ? window.DineFlowRBAC.getActiveRole() : 'admin';

  const roles = [
    { key: 'admin',   label: 'Admin (Owner)', icon: 'fa-crown', color: '#818CF8' },
    { key: 'manager', label: 'Manager',       icon: 'fa-user-tie', color: '#34D399' },
    { key: 'cashier', label: 'Cashier',       icon: 'fa-calculator', color: '#FBBF24' },
    { key: 'kitchen', label: 'Kitchen Chef',   icon: 'fa-fire-burner', color: '#F87171' },
    { key: 'waiter',  label: 'Waiter',        icon: 'fa-person-walking', color: '#22D3EE' }
  ];

  container.innerHTML = `
    <div class="role-switcher-bar" style="background:var(--card); backdrop-filter:blur(12px); border:1px solid var(--border); border-radius:16px; padding:14px 20px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-user-lock" style="color:#6366F1; font-size:18px;"></i>
        <div>
          <h4 style="margin:0; font-size:13.5px; font-weight:700; color:var(--text-primary);">Simulate Logged-In User Role:</h4>
          <span style="font-size:11px; color:var(--text-muted);">Test how different staff roles experience page access and sidebar limits</span>
        </div>
      </div>
      <div style="display:flex; gap:8px; flex-wrap:wrap;">
        ${roles.map(r => `
          <button onclick="window.DineFlowRBAC.setActiveRole('${r.key}')" style="padding:8px 14px; background:${activeRole === r.key ? r.color + '25' : 'var(--bg)'}; border:1px solid ${activeRole === r.key ? r.color : 'var(--border)'}; color:${activeRole === r.key ? r.color : 'var(--text-secondary)'}; border-radius:10px; font-weight:700; font-size:12px; cursor:pointer; display:flex; align-items:center; gap:6px;">
            <i class="fa-solid ${r.icon}"></i> ${r.label} ${activeRole === r.key ? '✓' : ''}
          </button>
        `).join('')}
      </div>
    </div>
  `;
}

function renderRolePermissionToggles() {
  const perms = window.DineFlowRBAC ? window.DineFlowRBAC.getPermissions() : {};
  const roles = ['manager', 'cashier', 'kitchen', 'waiter'];

  roles.forEach(role => {
    const cardEl = document.querySelector(`[data-role-card="${role}"]`);
    if (!cardEl) return;

    const rolePerms = perms[role] || {};

    const toggleHTML = `
      <div style="margin-top:14px; border-top:1px solid var(--border); padding-top:14px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <span style="font-size:11.5px; font-weight:700; color:#818CF8; text-transform:uppercase; letter-spacing:0.5px;">⚙️ Access Permissions</span>
          <span style="font-size:10.5px; color:var(--text-muted);">Toggle ON/OFF</span>
        </div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
          ${MODULE_DEFINITIONS.map(mod => {
            const isChecked = rolePerms[mod.key] !== false;
            return `
              <label style="display:flex; align-items:center; justify-content:space-between; background:var(--bg); padding:6px 10px; border-radius:8px; border:1px solid var(--border); font-size:11.5px; color:var(--text-primary); cursor:pointer;">
                <span><i class="fa-solid ${mod.icon}" style="margin-right:6px; opacity:0.7;"></i> ${mod.name}</span>
                <input type="checkbox" onchange="toggleModulePermission('${role}', '${mod.key}', this.checked)" ${isChecked ? 'checked' : ''} style="accent-color:#6366F1; width:14px; height:14px;">
              </label>
            `;
          }).join('')}
        </div>
      </div>
    `;

    cardEl.insertAdjacentHTML('beforeend', toggleHTML);
  });
}

function toggleModulePermission(role, moduleKey, isEnabled) {
  const perms = window.DineFlowRBAC.getPermissions();
  if (!perms[role]) perms[role] = {};
  perms[role][moduleKey] = isEnabled;
  window.DineFlowRBAC.savePermissions(perms);
  if (typeof Toast !== 'undefined' && Toast.success) {
    Toast.success(`Permission updated: [${role.toUpperCase()}] -> ${moduleKey.toUpperCase()} = ${isEnabled ? 'ON' : 'OFF'}`);
  }
}
