/**
 * DineFlow AI - Admin Utilities
 * Helper functions for admin modules
 */

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
    if (!this.isAuthenticated()) {
      window.location.href = 'login.html';
    }
  }
}
