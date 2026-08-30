/**
 * DineFlow AI — Staff & Access Roles Controller v2.2.0
 * Email + Password Primary Login / Optional Quick PIN & Tenant-Isolated RBAC.
 */

const RESTAURANT_ID = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || new URLSearchParams(window.location.search).get('restaurantId') || '1';

const MODULE_DEFINITIONS = [
  { key: 'pos',       name: 'POS Terminal',        icon: 'fa-cash-register' },
  { key: 'menu',      name: 'Menu Items',          icon: 'fa-utensils' },
  { key: 'reports',   name: 'Sales Reports',       icon: 'fa-chart-line' },
  { key: 'expenses',  name: 'Expenses & Overhead', icon: 'fa-file-invoice-dollar' },
  { key: 'inventory', name: 'Inventory Stock',     icon: 'fa-boxes-stacked' },
  { key: 'coupons',   name: 'Offers & Deals',      icon: 'fa-gift' },
  { key: 'customers', name: 'Customers & CRM',     icon: 'fa-users' },
  { key: 'tables',    name: 'Table Manager',       icon: 'fa-table-cells-large' },
  { key: 'staff',     name: 'Staff & Roles',       icon: 'fa-user-shield' },
  { key: 'settings',  name: 'System Settings',     icon: 'fa-gear' },
  { key: 'feedback',  name: 'Reviews & Feedback',  icon: 'fa-star' }
];

let allStaffList = [];

document.addEventListener('DOMContentLoaded', () => {
  renderActiveRoleSwitcherBar();
  loadStaffMembers();
});

function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function showToast(msg, type = 'info') {
  if (typeof Toast !== 'undefined') {
    if (type === 'success' && Toast.success) Toast.success(msg);
    else if (type === 'error' && Toast.error) Toast.error(msg);
    else if (Toast.show) Toast.show(msg, type);
  } else {
    alert(msg);
  }
}

async function loadStaffMembers() {
  const grid = document.getElementById('staffGrid');
  const emptyState = document.getElementById('staffEmptyState');

  try {
    const res = await fetch(`/api/staff/restaurant/${RESTAURANT_ID}`);
    const result = await res.json();

    if (result.success && Array.isArray(result.data)) {
      allStaffList = result.data;
    } else {
      allStaffList = [];
    }
  } catch (err) {
    console.error('Error fetching staff list from server:', err);
    allStaffList = [];
  }

  if (!grid) return;
  grid.innerHTML = '';

  if (allStaffList.length === 0) {
    if (emptyState) emptyState.style.display = 'block';
    return;
  }

  if (emptyState) emptyState.style.display = 'none';
  allStaffList.forEach(staff => {
    grid.appendChild(createStaffCardElement(staff));
  });

  renderRolePermissionToggles();
}

function createStaffCardElement(staff) {
  const role = String(staff.role || 'staff').toLowerCase();
  const roleMeta = {
    admin:   { title: 'System Administrator', badge: 'Full Control', gradient: 'linear-gradient(135deg, #6366F1, #4F46E5)', icon: 'fa-crown', desc: 'Full control over billing, catalog, staff accounts, financial reports, and settings.' },
    owner:   { title: 'Restaurant Owner', badge: 'Owner Access', gradient: 'linear-gradient(135deg, #6366F1, #4F46E5)', icon: 'fa-crown', desc: 'Full administrative ownership and restaurant control.' },
    manager: { title: 'Store Manager', badge: 'Managerial Access', gradient: 'linear-gradient(135deg, #10B981, #059669)', icon: 'fa-user-tie', desc: 'Access to menu catalog, stock inventory, discount approvals, and POS terminal.' },
    cashier: { title: 'Head Cashier', badge: 'POS & Billing', gradient: 'linear-gradient(135deg, #F59E0B, #D97706)', icon: 'fa-calculator', desc: 'Access to live billing, customer payments, receipt printing, and floor table status.' },
    kitchen: { title: 'Kitchen Chef', badge: 'KOT & KDS', gradient: 'linear-gradient(135deg, #EF4444, #DC2626)', icon: 'fa-fire-burner', desc: 'Access to Kitchen Display System (KDS), KOT order tickets, and preparation status.' },
    chef:    { title: 'Kitchen Chef', badge: 'KOT & KDS', gradient: 'linear-gradient(135deg, #EF4444, #DC2626)', icon: 'fa-fire-burner', desc: 'Access to Kitchen Display System (KDS), KOT order tickets, and preparation status.' },
    waiter:  { title: 'Table Captain / Waiter', badge: 'Floor & Orders', gradient: 'linear-gradient(135deg, #06B6D4, #0891B2)', icon: 'fa-bell', desc: 'Access to table ordering, waiter call alerts, and table occupancy management.' },
  }[role] || { title: 'Staff Member', badge: staff.role, gradient: 'linear-gradient(135deg, #64748B, #475569)', icon: 'fa-user', desc: 'Standard staff access credentials.' };

  const isDeactivated = staff.is_active === false;
  const isPinConfigured = staff.has_pin !== false;

  const card = document.createElement('div');
  card.className = `staff-card ${isDeactivated ? 'deactivated' : ''}`;
  card.setAttribute('data-role-card', role);
  card.setAttribute('data-staff-id', staff.id);

  card.innerHTML = `
    <div class="staff-header" style="position:relative;">
      <div class="staff-avatar" style="background:${roleMeta.gradient};"><i class="fa-solid ${roleMeta.icon}"></i></div>
      <div style="flex:1; overflow:hidden;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:2px;">
          <h3 style="font-family:'Outfit',sans-serif; font-size:16.5px; font-weight:700; color:var(--text-primary); margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
            ${escapeHtml(staff.name || 'Staff Member')}
          </h3>
          ${isDeactivated ? '<span style="background:rgba(239,68,68,0.15); color:#EF4444; font-size:10px; font-weight:800; padding:2px 6px; border-radius:6px; text-transform:uppercase;">Inactive</span>' : '<span style="background:rgba(16,185,129,0.15); color:#10B981; font-size:10px; font-weight:800; padding:2px 6px; border-radius:6px; text-transform:uppercase;">Active</span>'}
        </div>
        <span class="role-badge ${role}">${roleMeta.badge}</span>
      </div>
    </div>

    <!-- Login ID & Contact Info -->
    <div style="background:var(--bg); padding:10px 14px; border-radius:12px; border:1px solid var(--border); display:flex; flex-direction:column; gap:4px; font-size:12px;">
      <div style="display:flex; align-items:center; justify-content:space-between;">
        <span style="color:var(--text-muted); font-weight:600;"><i class="fa-solid fa-envelope" style="margin-right:5px; color:var(--primary);"></i> Login Email:</span>
        <strong style="color:var(--text-primary); font-family:monospace; font-size:12.5px;">${escapeHtml(staff.email || '—')}</strong>
      </div>
      ${staff.phone ? `
        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:2px;">
          <span style="color:var(--text-muted); font-weight:600;"><i class="fa-solid fa-phone" style="margin-right:5px; color:#10B981;"></i> Phone:</span>
          <span style="color:var(--text-secondary);">${escapeHtml(staff.phone)}</span>
        </div>
      ` : ''}
    </div>

    <!-- Authentication Status Box -->
    <div class="pin-status-box" style="display:flex; flex-direction:column; gap:6px; align-items:stretch;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Primary Login:</span>
        <span style="color:#10B981; font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
          <i class="fa-solid fa-lock"></i> Password Protected
        </span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px dashed var(--border); padding-top:4px;">
        <span style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">Quick PIN:</span>
        ${isPinConfigured ? `
          <span class="pin-status-pill configured" style="font-size:11.5px;">
            <i class="fa-solid fa-circle-check"></i> Enabled
          </span>
        ` : `
          <span class="pin-status-pill missing" style="font-size:11.5px; color:var(--text-muted);">
            <i class="fa-solid fa-circle-minus"></i> Optional (Not Set)
          </span>
        `}
      </div>
    </div>

    <p style="font-size:12px; color:var(--text-secondary); margin:0; line-height:1.4;">${roleMeta.desc}</p>

    <div class="card-actions">
      <button class="card-btn" onclick="openEditStaffModal(${staff.id})">
        <i class="fa-solid fa-pen-to-square"></i> Edit
      </button>
      <button class="card-btn" onclick="openResetPasswordModal(${staff.id}, '${escapeHtml(staff.name)}', '${escapeHtml(staff.email)}')">
        <i class="fa-solid fa-key"></i> Password
      </button>
      <button class="card-btn" onclick="openResetPinModal(${staff.id}, '${escapeHtml(staff.name)}')">
        <i class="fa-solid fa-fingerprint"></i> PIN
      </button>
      <button class="card-btn" onclick="toggleStaffStatus(${staff.id})">
        <i class="fa-solid ${isDeactivated ? 'fa-toggle-off' : 'fa-toggle-on'}"></i> ${isDeactivated ? 'Activate' : 'Deactivate'}
      </button>
      <button class="card-btn danger" onclick="deleteStaffMember(${staff.id}, '${escapeHtml(staff.name)}')">
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  `;

  return card;
}

/* =========================================================
 * ADD STAFF MODAL (Email + Password Primary)
 * ========================================================= */
function openAddStaffModal() {
  const modal = document.getElementById('addStaffModal');
  if (!modal) return;
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
  document.getElementById('addStaffForm')?.reset();
}

function closeAddStaffModal() {
  const modal = document.getElementById('addStaffModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

async function saveNewStaffMember(event) {
  event.preventDefault();
  const name = document.getElementById('staffNameInput')?.value.trim();
  const email = document.getElementById('staffEmailInput')?.value.trim();
  const role = document.getElementById('staffRoleSelect')?.value;
  const password = document.getElementById('staffPasswordInput')?.value;
  const passwordConfirm = document.getElementById('staffPasswordConfirmInput')?.value;
  const phone = document.getElementById('staffPhoneInput')?.value.trim();
  const pin = document.getElementById('staffPinInput')?.value.trim();
  const submitBtn = document.getElementById('saveStaffSubmitBtn');

  if (!name) {
    showToast('Please enter the staff member full name.', 'error');
    return;
  }
  if (!email || !email.includes('@')) {
    showToast('Please enter a valid staff email address for login.', 'error');
    return;
  }
  if (!password || password.length < 6) {
    showToast('Password must be at least 6 characters long.', 'error');
    return;
  }
  if (password !== passwordConfirm) {
    showToast('Passwords do not match. Please re-type password.', 'error');
    return;
  }
  if (pin && (pin.length < 4 || pin.length > 8 || !/^[0-9]+$/.test(pin))) {
    showToast('Quick PIN must be 4 to 8 numeric digits if provided.', 'error');
    return;
  }

  if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Creating Account...'; }

  try {
    const res = await fetch('/api/staff', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({
        restaurant_id: RESTAURANT_ID,
        name: name,
        email: email,
        role: role,
        password: password,
        password_confirmation: passwordConfirm,
        phone: phone || null,
        numeric_pin: pin || null
      })
    });

    const data = await res.json();
    if (data.success) {
      showToast(data.message || `Staff account "${name}" created successfully!`, 'success');
      closeAddStaffModal();
      loadStaffMembers();
    } else {
      showToast(data.message || 'Failed to create staff account.', 'error');
    }
  } catch (err) {
    console.error('Error creating staff account:', err);
    showToast('Network error while creating staff account.', 'error');
  } finally {
    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Save Staff Account'; }
  }
}

/* =========================================================
 * RESET PASSWORD MODAL (Primary Login Password Reset)
 * ========================================================= */
function openResetPasswordModal(staffId, staffName, staffEmail) {
  const modal = document.getElementById('resetPasswordModal');
  if (!modal) return;

  document.getElementById('resetPasswordStaffId').value = staffId;
  const label = document.getElementById('resetPasswordStaffLabel');
  if (label) label.textContent = `Set a new login password for ${staffName} (${staffEmail}).`;

  document.getElementById('resetPasswordForm')?.reset();
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
}

function closeResetPasswordModal() {
  const modal = document.getElementById('resetPasswordModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

async function submitResetPassword(event) {
  event.preventDefault();
  const id = document.getElementById('resetPasswordStaffId')?.value;
  const password = document.getElementById('newStaffPasswordInput')?.value;
  const confirmPassword = document.getElementById('confirmNewStaffPasswordInput')?.value;

  if (!password || password.length < 6) {
    showToast('New password must be at least 6 characters long.', 'error');
    return;
  }
  if (password !== confirmPassword) {
    showToast('Passwords do not match.', 'error');
    return;
  }

  try {
    const res = await fetch(`/api/staff/${id}/reset-password`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({
        password: password,
        password_confirmation: confirmPassword
      })
    });

    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Staff password reset successfully.', 'success');
      closeResetPasswordModal();
      loadStaffMembers();
    } else {
      showToast(data.message || 'Failed to reset password.', 'error');
    }
  } catch (err) {
    showToast('Network error while resetting password.', 'error');
  }
}

/* =========================================================
 * RESET PIN MODAL (Optional Quick PIN)
 * ========================================================= */
function openResetPinModal(staffId, staffName) {
  const modal = document.getElementById('resetPinModal');
  if (!modal) return;

  document.getElementById('resetPinStaffId').value = staffId;
  const label = document.getElementById('resetPinStaffLabel');
  if (label) label.textContent = `Set a 4-6 digit Quick PIN for ${staffName} (for fast POS terminal switching).`;

  document.getElementById('resetPinForm')?.reset();
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
}

function closeResetPinModal() {
  const modal = document.getElementById('resetPinModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

async function submitResetPin(event) {
  event.preventDefault();
  const id = document.getElementById('resetPinStaffId')?.value;
  const pin = document.getElementById('newPinInput')?.value.trim();
  const pinConfirm = document.getElementById('confirmNewPinInput')?.value.trim();

  if (!pin || pin.length < 4 || pin.length > 8 || !/^[0-9]+$/.test(pin)) {
    showToast('Quick PIN must be 4 to 8 numeric digits.', 'error');
    return;
  }
  if (pin !== pinConfirm) {
    showToast('PIN and Confirm PIN do not match.', 'error');
    return;
  }

  try {
    const res = await fetch(`/api/staff/${id}/reset-pin`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({ numeric_pin: pin })
    });

    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Quick PIN updated successfully.', 'success');
      closeResetPinModal();
      loadStaffMembers();
    } else {
      showToast(data.message || 'Failed to update PIN.', 'error');
    }
  } catch (err) {
    showToast('Network error while updating PIN.', 'error');
  }
}

/* =========================================================
 * EDIT STAFF MODAL
 * ========================================================= */
function openEditStaffModal(staffId) {
  const staff = allStaffList.find(s => String(s.id) === String(staffId));
  if (!staff) return;

  const modal = document.getElementById('editStaffModal');
  if (!modal) return;

  document.getElementById('editStaffId').value = staff.id;
  document.getElementById('editStaffName').value = staff.name || '';
  document.getElementById('editStaffEmail').value = staff.email || '';
  document.getElementById('editStaffRole').value = staff.role || 'cashier';
  document.getElementById('editStaffPhone').value = staff.phone || '';

  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
}

function closeEditStaffModal() {
  const modal = document.getElementById('editStaffModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

async function submitEditStaff(event) {
  event.preventDefault();
  const id = document.getElementById('editStaffId')?.value;
  const name = document.getElementById('editStaffName')?.value.trim();
  const email = document.getElementById('editStaffEmail')?.value.trim();
  const role = document.getElementById('editStaffRole')?.value;
  const phone = document.getElementById('editStaffPhone')?.value.trim();

  try {
    const res = await fetch(`/api/staff/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      },
      body: JSON.stringify({ name, email, role, phone })
    });

    const data = await res.json();
    if (data.success) {
      showToast('Staff details updated successfully.', 'success');
      closeEditStaffModal();
      loadStaffMembers();
    } else {
      showToast(data.message || 'Failed to update staff member.', 'error');
    }
  } catch (err) {
    showToast('Network error while updating staff member.', 'error');
  }
}

/* =========================================================
 * TOGGLE STATUS & DELETE
 * ========================================================= */
async function toggleStaffStatus(staffId) {
  try {
    const res = await fetch(`/api/staff/${staffId}/toggle-status`, {
      method: 'PUT',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    });

    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Staff status updated.', 'success');
      loadStaffMembers();
    } else {
      showToast(data.message || 'Failed to update staff status.', 'error');
    }
  } catch (err) {
    showToast('Network error while updating status.', 'error');
  }
}

async function deleteStaffMember(staffId, staffName) {
  if (!confirm(`Are you sure you want to permanently remove "${staffName}" from your restaurant staff?`)) return;

  try {
    const res = await fetch(`/api/staff/${staffId}`, {
      method: 'DELETE',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    });

    const data = await res.json();
    if (data.success) {
      showToast(data.message || 'Staff member removed successfully.', 'success');
      loadStaffMembers();
    } else {
      showToast(data.message || 'Failed to remove staff member.', 'error');
    }
  } catch (err) {
    showToast('Network error while deleting staff member.', 'error');
  }
}

/* =========================================================
 * RBAC SIMULATION & PERMISSION TOGGLES
 * ========================================================= */
function renderActiveRoleSwitcherBar() {
  const container = document.getElementById('roleSwitcherBarContainer');
  if (!container) return;

  const activeRole = window.DineFlowRBAC ? window.DineFlowRBAC.getActiveRole() : 'admin';

  const roles = [
    { key: 'admin',   label: 'Admin (Owner)', icon: 'fa-crown', color: '#818CF8' },
    { key: 'manager', label: 'Manager',       icon: 'fa-user-tie', color: '#34D399' },
    { key: 'cashier', label: 'Cashier',       icon: 'fa-calculator', color: '#FBBF24' },
    { key: 'kitchen', label: 'Kitchen Chef',   icon: 'fa-fire-burner', color: '#F87171' },
    { key: 'waiter',  label: 'Waiter',        icon: 'fa-bell', color: '#22D3EE' }
  ];

  container.innerHTML = `
    <div class="role-switcher-bar" style="background:var(--card); backdrop-filter:blur(12px); border:1px solid var(--border); border-radius:16px; padding:14px 20px; margin-bottom:24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-user-lock" style="color:#6366F1; font-size:18px;"></i>
        <div>
          <h4 style="margin:0; font-size:13.5px; font-weight:700; color:var(--text-primary);">Simulate Active Staff Role:</h4>
          <span style="font-size:11px; color:var(--text-muted);">Preview how different staff access levels experience POS & Admin navigation</span>
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

    const existingToggles = cardEl.querySelector('.perm-toggles-section');
    if (existingToggles) existingToggles.remove();

    const rolePerms = perms[role] || {};

    const toggleHTML = `
      <div class="perm-toggles-section" style="margin-top:14px; border-top:1px solid var(--border); padding-top:14px;">
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

function escapeHtml(text) {
  if (!text) return '';
  return String(text).replace(/[&<>"']/g, function(m) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
  });
}
