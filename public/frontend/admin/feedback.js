/**
 * CUSTOMER FEEDBACK SYSTEM - Fixed v2
 * Fixed: customer_name vs customerName field mismatch
 */

let allFeedback = [];

function showLoader() {
  const loader = document.querySelector('[data-loader]');
  if (loader) { loader.style.opacity = '1'; loader.style.display = 'flex'; }
}

function hideLoader() {
  const loader = document.querySelector('[data-loader]');
  if (loader) { loader.style.opacity = '0'; loader.style.display = 'none'; }
}

function toast(msg, type = 'info') {
  if (typeof Toast !== 'undefined') {
    if (Toast.show)   Toast.show(msg, type);
    else if (type === 'error' && Toast.error)   Toast.error(msg);
    else if (type === 'success' && Toast.success) Toast.success(msg);
  }
}

function getStars(rating) {
  const n = Math.min(5, Math.max(1, parseInt(rating) || 5));
  return '⭐'.repeat(n) + (n < 5 ? '☆'.repeat(5 - n) : '');
}

async function loadFeedback() {
  showLoader();
  const restaurantId = window.AUTH_USER_RESTAURANT_ID
    || new URLSearchParams(window.location.search).get('restaurant_id')
    || window.APP_CONFIG?.restaurant_id
    || '1';

  try {
    const res = await fetch(`/api/feedback/restaurant/${restaurantId}`);
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      allFeedback = data.data;
    } else if (Array.isArray(data)) {
      allFeedback = data;
    } else {
      allFeedback = [];
    }
  } catch (err) {
    console.error('Error loading feedback:', err);
    allFeedback = [];
  } finally {
    hideLoader();
    renderFeedback();
  }
}

function renderFeedback() {
  const container = document.querySelector('[data-feedback-container]');
  if (!container) return;

  if (!allFeedback || allFeedback.length === 0) {
    container.innerHTML = `
      <div class="empty-state" style="text-align:center; padding:60px; color:#94A3B8;">
        <i class="fa-solid fa-comment-dots" style="font-size:48px; opacity:.3; display:block; margin-bottom:16px;"></i>
        <p style="font-size:14px;">📝 No customer feedback yet</p>
        <p style="font-size:12px; margin-top:8px;">Feedback submitted via QR menu will appear here.</p>
      </div>`;
    return;
  }

  // Average rating
  const avg = (allFeedback.reduce((s, f) => s + (parseInt(f.rating) || 5), 0) / allFeedback.length).toFixed(1);

  // Update stats if elements exist
  const avgEl  = document.getElementById('avgRating')  || document.querySelector('[data-avg-rating]');
  const cntEl  = document.getElementById('totalReviews') || document.querySelector('[data-total-reviews]');
  if (avgEl) avgEl.textContent = avg + '⭐';
  if (cntEl) cntEl.textContent = allFeedback.length;

  container.innerHTML = allFeedback
    .sort((a, b) => new Date(b.created_at || b.createdAt) - new Date(a.created_at || a.createdAt))
    .map(feedback => {
      // Fix field name: DB uses customer_name, old code used customerName
      const name    = feedback.customer_name || feedback.customerName || 'Anonymous';
      const rating  = parseInt(feedback.rating) || 5;
      const comment = feedback.comment || 'No comment provided';
      const date    = feedback.created_at || feedback.createdAt;
      const dateStr = date ? new Date(date).toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' }) : '—';

      return `
      <div class="feedback-card" style="background:var(--card); border:1px solid var(--border); border-radius:14px; padding:16px; margin-bottom:12px;">
        <div class="feedback-header" style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:36px; height:36px; border-radius:50%; background:rgba(99,102,241,0.2); color:var(--primary); display:flex; align-items:center; justify-content:center; font-weight:700;">
              ${(name[0] || '?').toUpperCase()}
            </div>
            <h4 style="font-size:14px; font-weight:700; color:var(--text-primary);">${escapeHtml(name)}</h4>
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            <div class="stars" style="font-size:14px;">${getStars(rating)}</div>
            <span style="font-size:12px; font-weight:700; color:#F59E0B;">${rating}/5</span>
          </div>
        </div>
        <p class="feedback-comment" style="font-size:13.5px; color:var(--text-primary); line-height:1.5; margin-bottom:8px; font-weight:500;">${escapeHtml(comment)}</p>
        <p class="feedback-date" style="font-size:11px; color:var(--text-muted);"><i class="fa-regular fa-clock" style="margin-right:4px;"></i>${dateStr}</p>
      </div>`;
    }).join('');
}

function initFeedback() {
  if (typeof Auth !== 'undefined' && Auth.checkAuth) Auth.checkAuth();
  loadFeedback();
  setInterval(loadFeedback, 60000); // Auto-refresh every 60s
}

function escapeHtml(str) {
  return typeof str === 'string' ? str.replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]) : '';
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initFeedback);
} else {
  initFeedback();
}
