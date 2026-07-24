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
});

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
    <div style="background:rgba(15,23,42,0.8); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:14px 20px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-user-lock" style="color:#6366F1; font-size:18px;"></i>
        <div>
          <h4 style="margin:0; font-size:13.5px; font-weight:700; color:#F8FAFC;">Simulate Logged-In User Role:</h4>
          <span style="font-size:11px; color:#94A3B8;">Test how different staff roles experience page access and sidebar limits</span>
        </div>
      </div>
      <div style="display:flex; gap:8px; flex-wrap:wrap;">
        ${roles.map(r => `
          <button onclick="window.DineFlowRBAC.setActiveRole('${r.key}')" style="padding:8px 14px; background:${activeRole === r.key ? r.color + '25' : 'rgba(255,255,255,0.05)'}; border:1px solid ${activeRole === r.key ? r.color : 'rgba(255,255,255,0.1)'}; color:${activeRole === r.key ? r.color : '#94A3B8'}; border-radius:10px; font-weight:700; font-size:12px; cursor:pointer; display:flex; align-items:center; gap:6px;">
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
      <div style="margin-top:14px; border-top:1px solid rgba(255,255,255,0.08); padding-top:14px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
          <span style="font-size:11.5px; font-weight:700; color:#818CF8; text-transform:uppercase; letter-spacing:0.5px;">⚙️ Access Permissions</span>
          <span style="font-size:10.5px; color:#64748B;">Toggle ON/OFF</span>
        </div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
          ${MODULE_DEFINITIONS.map(mod => {
            const isChecked = rolePerms[mod.key] !== false;
            return `
              <label style="display:flex; align-items:center; justify-content:space-between; background:rgba(15,23,42,0.5); padding:6px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.05); font-size:11.5px; color:#E2E8F0; cursor:pointer;">
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
