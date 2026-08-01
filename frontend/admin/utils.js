/**
 * Global System Theme Manager
 */
window.initGlobalSystemTheme = function() {
  const savedTheme = localStorage.getItem('posTheme') || localStorage.getItem('dineflow_theme');
  if (savedTheme === 'light') {
    document.documentElement.classList.add('light-theme');
    if (document.body) document.body.classList.add('light-theme');
  } else {
    document.documentElement.classList.remove('light-theme');
    if (document.body) document.body.classList.remove('light-theme');
  }
};

window.toggleGlobalSystemTheme = function() {
  const isLight = document.body ? document.body.classList.toggle('light-theme') : document.documentElement.classList.toggle('light-theme');
  const themeStr = isLight ? 'light' : 'dark';
  localStorage.setItem('dineflow_theme', themeStr);
  localStorage.setItem('posTheme', themeStr);
  window.initGlobalSystemTheme();
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', window.initGlobalSystemTheme);
} else {
  window.initGlobalSystemTheme();
}

window.toggleAdminSidebar = function() {
  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if (!sidebar) return;

  const isOpen = sidebar.classList.contains('mobile-open');
  if (isOpen) {
    sidebar.classList.remove('mobile-open');
    if (backdrop) backdrop.classList.remove('active');
  } else {
    sidebar.classList.add('mobile-open');
    if (backdrop) backdrop.classList.add('active');
  }
};

/**
 * Toast notification system
 */
class Toast {
  static show(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;

    const style = document.createElement('style');
    style.textContent = `
      .toast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
        max-width: 400px;
      }
      @keyframes slideIn {
        from {
          transform: translateX(400px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
      .toast-success {
        background: #10b981;
        color: white;
      }
      .toast-error {
        background: #ef4444;
        color: white;
      }
      .toast-info {
        background: #3b82f6;
        color: white;
      }
      .toast-warning {
        background: #f59e0b;
        color: white;
      }
    `;
    document.head.appendChild(style);
    document.body.appendChild(toast);

    setTimeout(() => {
      toast.style.animation = 'slideOut 0.3s ease-out';
      setTimeout(() => toast.remove(), 300);
    }, duration);
  }

  static success(message) {
    this.show(message, 'success');
  }

  static error(message) {
    this.show(message, 'error');
  }

  static info(message) {
    this.show(message, 'info');
  }

  static warning(message) {
    this.show(message, 'warning');
  }
}

/**
 * Loading spinner helper
 */
class Spinner {
  static show() {
    let spinner = document.getElementById('loading-spinner');
    if (!spinner) {
      spinner = document.createElement('div');
      spinner.id = 'loading-spinner';
      spinner.innerHTML = `
        <div class="spinner-overlay">
          <div class="spinner-content">
            <div class="spinner-ring"></div>
            <p>Loading...</p>
          </div>
        </div>
      `;
      document.body.appendChild(spinner);

      const style = document.createElement('style');
      style.textContent = `
        .spinner-overlay {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(10, 10, 12, 0.8);
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 9998;
          backdrop-filter: blur(4px);
        }
        .spinner-content {
          text-align: center;
        }
        .spinner-ring {
          width: 40px;
          height: 40px;
          border: 4px solid rgba(255, 255, 255, 0.1);
          border-top-color: #ff6600;
          border-radius: 50%;
          animation: spin 1s linear infinite;
          margin: 0 auto 10px;
        }
        @keyframes spin {
          to { transform: rotate(360deg); }
        }
        .spinner-content p {
          color: #f3f4f6;
          font-size: 14px;
        }
      `;
      document.head.appendChild(style);
    }
    spinner.style.display = 'flex';
  }

  static hide() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
      spinner.style.display = 'none';
    }
  }
}

/**
 * Format utilities
 */
class Format {
  static currency(amount) {
    return new Intl.NumberFormat('en-IN', {
      style: 'currency',
      currency: 'INR',
    }).format(amount);
  }

  static date(date) {
    return new Intl.DateTimeFormat('en-IN', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    }).format(new Date(date));
  }

  static datetime(date) {
    return new Intl.DateTimeFormat('en-IN', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(date));
  }

  static time(date) {
    return new Intl.DateTimeFormat('en-IN', {
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(date));
  }
}

/**
 * Status badge helper
 */
class StatusBadge {
  static getClass(status) {
    const statusMap = {
      pending: 'badge-warning',
      preparing: 'badge-info',
      served: 'badge-success',
      cancelled: 'badge-error',
      available: 'badge-success',
      'out-of-stock': 'badge-error',
    };
    return statusMap[status] || 'badge-default';
  }

  static getColor(status) {
    const colorMap = {
      pending: '#f59e0b',
      preparing: '#3b82f6',
      served: '#10b981',
      cancelled: '#ef4444',
      available: '#10b981',
      'out-of-stock': '#ef4444',
    };
    return colorMap[status] || '#6b7280';
  }

  static render(status) {
    const badgeClass = this.getClass(status);
    return `<span class="badge ${badgeClass}">${status}</span>`;
  }
}

/**
 * Modal helper
 */
class Modal {
  static show(title, content, buttons = []) {
    let modal = document.getElementById('admin-modal');
    if (!modal) {
      modal = document.createElement('div');
      modal.id = 'admin-modal';
      document.body.appendChild(modal);

      const style = document.createElement('style');
      style.textContent = `
        #admin-modal {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(10, 10, 12, 0.7);
          z-index: 9997;
          align-items: center;
          justify-content: center;
          backdrop-filter: blur(4px);
        }
        #admin-modal.show {
          display: flex;
        }
        .modal-content {
          background: #121216;
          border: 1px solid rgba(255, 255, 255, 0.1);
          border-radius: 12px;
          padding: 24px;
          max-width: 500px;
          width: 90%;
          box-shadow: 0 20px 25px rgba(0, 0, 0, 0.5);
        }
        .modal-header {
          font-size: 20px;
          font-weight: 600;
          color: #f3f4f6;
          margin-bottom: 16px;
        }
        .modal-body {
          color: #d1d5db;
          margin-bottom: 24px;
          font-size: 14px;
        }
        .modal-footer {
          display: flex;
          gap: 12px;
          justify-content: flex-end;
        }
        .btn-modal {
          padding: 8px 16px;
          border-radius: 6px;
          border: none;
          font-weight: 500;
          cursor: pointer;
          transition: all 0.2s;
        }
        .btn-modal-primary {
          background: #ff6600;
          color: white;
        }
        .btn-modal-primary:hover {
          background: #ff5500;
        }
        .btn-modal-secondary {
          background: rgba(255, 255, 255, 0.1);
          color: #f3f4f6;
        }
        .btn-modal-secondary:hover {
          background: rgba(255, 255, 255, 0.15);
        }
      `;
      document.head.appendChild(style);
    }

    const footer = buttons
      .map(
        (btn) =>
          `<button class="btn-modal btn-modal-${btn.type}" onclick="${btn.action}">${btn.label}</button>`
      )
      .join('');

    modal.innerHTML = `
      <div class="modal-content">
        <div class="modal-header">${title}</div>
        <div class="modal-body">${content}</div>
        <div class="modal-footer">${footer}</div>
      </div>
    `;
    modal.classList.add('show');
  }

  static hide() {
    const modal = document.getElementById('admin-modal');
    if (modal) {
      modal.classList.remove('show');
    }
  }
}

/**
 * Form validation helper
 */
class Validation {
  static isEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  static isPositiveNumber(num) {
    return !isNaN(num) && num > 0;
  }

  static isURL(url) {
    try {
      new URL(url);
      return true;
    } catch {
      return false;
    }
  }

  static isEmpty(value) {
    return !value || value.trim() === '';
  }

  static validate(data, rules) {
    const errors = {};
    for (const [field, rule] of Object.entries(rules)) {
      const value = data[field];
      if (rule.required && this.isEmpty(value)) {
        errors[field] = `${field} is required`;
      }
      if (rule.type === 'email' && value && !this.isEmail(value)) {
        errors[field] = `Invalid email`;
      }
      if (rule.type === 'number' && value && !this.isPositiveNumber(value)) {
        errors[field] = `Must be a positive number`;
      }
      if (rule.type === 'url' && value && !this.isURL(value)) {
        errors[field] = `Invalid URL`;
      }
      if (rule.minLength && value && value.length < rule.minLength) {
        errors[field] = `Minimum ${rule.minLength} characters`;
      }
      if (rule.maxLength && value && value.length > rule.maxLength) {
        errors[field] = `Maximum ${rule.maxLength} characters`;
      }
    }
    return errors;
  }
}

/**
 * Storage helper
 */
class Storage {
  static setItem(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }

  static getItem(key) {
    const item = localStorage.getItem(key);
    return item ? JSON.parse(item) : null;
  }

  static removeItem(key) {
    localStorage.removeItem(key);
  }

  static clear() {
    localStorage.clear();
  }
}

/**
 * Auth helper
 */
class Auth {
  static getToken() {
    return localStorage.getItem('token');
  }

  static getUser() {
    return JSON.parse(localStorage.getItem('user') || '{}');
  }

  static isAuthenticated() {
    return !!this.getToken();
  }

  static logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = 'login.html';
  }

  static checkAuth() {
    return true;
  }
}

/**
 * 🛡️ DINEFLOW AI - ROLE-BASED ACCESS CONTROL (RBAC) ENGINE
 */
const DEFAULT_ROLE_PERMISSIONS = {
  admin:   { pos: true, menu: true, reports: true, inventory: true, coupons: true, customers: true, tables: true, staff: true, settings: true, feedback: true },
  manager: { pos: true, menu: true, reports: true, inventory: true, coupons: true, customers: true, tables: true, staff: false, settings: false, feedback: true },
  cashier: { pos: true, menu: false, reports: true, inventory: false, coupons: false, customers: false, tables: true, staff: false, settings: false, feedback: false },
  kitchen: { pos: false, menu: true, reports: false, inventory: true, coupons: false, customers: false, tables: false, staff: false, settings: false, feedback: false },
  waiter:  { pos: true, menu: false, reports: false, inventory: false, coupons: false, customers: false, tables: true, staff: false, settings: false, feedback: false }
};

window.DineFlowRBAC = {
  getPermissions() {
    try {
      const stored = localStorage.getItem('dineflow_role_permissions');
      if (stored) return JSON.parse(stored);
    } catch(e) {}
    return JSON.parse(JSON.stringify(DEFAULT_ROLE_PERMISSIONS));
  },

  savePermissions(perms) {
    localStorage.setItem('dineflow_role_permissions', JSON.stringify(perms));
  },

  getActiveRole() {
    if (typeof window !== 'undefined' && window.AUTH_USER_ROLE && window.AUTH_USER_ROLE !== '' && window.AUTH_USER_ROLE !== 'undefined') {
      const roleLower = String(window.AUTH_USER_ROLE).toLowerCase().trim();
      localStorage.setItem('dineflow_active_role', roleLower);
      return roleLower;
    }
    try {
      const stored = localStorage.getItem('dineflow_active_role');
      if (stored) return stored.toLowerCase();
    } catch(e) {}
    return 'admin';
  },

  setActiveRole(role) {
    window.AUTH_USER_ROLE = role;
    localStorage.setItem('dineflow_active_role', role);
    location.reload();
  },

  checkPermission(moduleKey) {
    const role = this.getActiveRole();
    if (role === 'admin') return true; // Admin has full access
    const perms = this.getPermissions();
    return Boolean(perms[role] && perms[role][moduleKey]);
  },

  enforcePageAccess() {
    const path = window.location.pathname.toLowerCase();
    let requiredModule = null;

    if (path.includes('/admin/staff')) requiredModule = 'staff';
    else if (path.includes('/admin/settings')) requiredModule = 'settings';
    else if (path.includes('/admin/inventory')) requiredModule = 'inventory';
    else if (path.includes('/admin/menu-manager')) requiredModule = 'menu';
    else if (path.includes('/admin/coupons')) requiredModule = 'coupons';
    else if (path.includes('/admin/customers')) requiredModule = 'customers';
    else if (path.includes('/admin/tables')) requiredModule = 'tables';
    else if (path.includes('/admin/feedback')) requiredModule = 'feedback';
    else if (path.includes('/z-report')) requiredModule = 'reports';

    if (!requiredModule) return;

    const activeRole = this.getActiveRole();
    const hasAccess = this.checkPermission(requiredModule);

    // Apply sidebar link filtering
    this.applySidebarFilters();

    if (!hasAccess) {
      const main = document.querySelector('main');
      if (main) {
        main.innerHTML = `
          <div style="max-width:550px; margin:80px auto; background:rgba(30,41,59,0.9); backdrop-filter:blur(20px); border:1px solid rgba(239,68,68,0.4); border-radius:24px; padding:40px; text-align:center; box-shadow:0 25px 60px rgba(0,0,0,0.7);">
            <div style="width:70px; height:70px; margin:0 auto 20px; background:rgba(239,68,68,0.15); border-radius:20px; color:#F87171; font-size:32px; display:flex; align-items:center; justify-content:center; border:1px solid rgba(239,68,68,0.3);">
              <i class="fa-solid fa-lock"></i>
            </div>
            <h2 style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#F8FAFC; margin:0 0 10px;">⛔ Access Restricted</h2>
            <p style="color:#94A3B8; font-size:14px; margin:0 0 24px; line-height:1.6;">
              Your current logged-in role <strong style="color:#F87171; text-transform:uppercase;">[${activeRole}]</strong> does not have permission to access the <strong>${requiredModule.toUpperCase()}</strong> module.
            </p>
            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
              <a href="/pos" style="padding:12px 24px; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#fff; text-decoration:none; border-radius:12px; font-weight:700; font-size:13px;">Return to Allowed Module</a>
              <a href="/login" style="padding:12px 20px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.12); border-radius:12px; color:#94A3B8; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;"><i class="fa-solid fa-right-from-bracket"></i> Logout & Switch PIN</a>
            </div>
          </div>
        `;
      }
    }
  },

  applySidebarFilters() {
    const role = this.getActiveRole();
    const perms = (this.getPermissions()[role]) || {};
    const moduleMap = {
      '/pos': 'pos',
      '/admin/menu-manager': 'menu',
      '/z-report': 'reports',
      '/admin/inventory': 'inventory',
      '/admin/coupons': 'coupons',
      '/admin/customers': 'customers',
      '/admin/tables': 'tables',
      '/admin/staff': 'staff',
      '/admin/settings': 'settings',
      '/admin/feedback': 'feedback'
    };

    document.querySelectorAll('.sidebar nav a.nav-item, .sidebar-nav a.nav-item, nav a.nav-item, a[href^="/admin/"], a[href="/pos"], a[href="/z-report"]').forEach(link => {
      const href = link.getAttribute('href');
      if (!href) return;

      const modKey = moduleMap[href];
      if (!modKey) return;

      const isAllowed = role === 'admin' || perms[modKey] !== false;

      if (!isAllowed) {
        link.style.opacity = '0.35';
        link.style.cursor = 'not-allowed';
        link.style.filter = 'grayscale(1)';
        link.removeAttribute('href'); // Remove link destination to prevent navigation!

        // Add 🔒 lock badge if not already present
        if (!link.querySelector('.sidebar-lock-badge')) {
          const lockSpan = document.createElement('span');
          lockSpan.className = 'sidebar-lock-badge';
          lockSpan.innerHTML = '🔒';
          lockSpan.style.marginLeft = 'auto';
          lockSpan.style.fontSize = '12px';
          lockSpan.style.opacity = '0.8';
          link.appendChild(lockSpan);
        }

        link.onclick = (e) => {
          e.preventDefault();
          e.stopPropagation();
          if (typeof Toast !== 'undefined' && Toast.error) {
            Toast.error(`🔒 Access Locked: Role [${role.toUpperCase()}] does not have permission for this module.`);
          } else {
            alert(`🔒 Access Locked: Role [${role.toUpperCase()}] does not have permission for this module.`);
          }
          return false;
        };
      }
    });
  }
};

document.addEventListener('DOMContentLoaded', () => {
  DineFlowRBAC.enforcePageAccess();
  DineFlowRBAC.applySidebarFilters();
});
