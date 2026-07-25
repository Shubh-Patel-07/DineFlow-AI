// DineFlow AI - Admin Coupons Controller (Fixed v2 - uses real DB API)

let couponsList = [];

function initCoupons() {
  if (typeof Auth !== 'undefined' && Auth.checkAuth) Auth.checkAuth();
  loadCoupons();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCoupons);
} else {
  initCoupons();
}

async function loadCoupons() {
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'flex';

  try {
    const res = await fetch('/api/coupons?restaurantId=1');
    const data = await res.json();
    if (data.success && Array.isArray(data.data)) {
      couponsList = data.data;
    } else {
      couponsList = [];
    }
  } catch (err) {
    console.error('Error loading coupons:', err);
    couponsList = [];
  } finally {
    if (loader) loader.style.display = 'none';
    renderCoupons();
  }
}

function renderCoupons() {
  const container = document.getElementById('couponGridContainer');
  if (!container) return;

  if (!couponsList.length) {
    container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:60px; color:#94A3B8;">
      <i class="fa-solid fa-ticket" style="font-size:40px; opacity:.3; display:block; margin-bottom:12px;"></i>
      <p style="font-size:14px;">No coupons yet. Click "+ Add Coupon" to create one.</p>
    </div>`;
    return;
  }

  const now = new Date();
  container.innerHTML = couponsList.map(c => {
    const isExpired = c.expires_at && new Date(c.expires_at) < now;
    const isActive  = c.is_active && !isExpired;
    const discountLabel = c.discount_type === 'percentage'
      ? `${c.discount_value}% OFF`
      : `₹${c.discount_value} OFF`;
    const expLabel = c.expires_at
      ? `Expires: ${new Date(c.expires_at).toLocaleDateString('en-IN')}`
      : 'No Expiry';
    const usageLabel = c.usage_limit
      ? `${c.used_count || 0}/${c.usage_limit} used`
      : `${c.used_count || 0} used (unlimited)`;

    return `
    <div class="coupon-card" style="position:relative; overflow:hidden;">
      ${!isActive ? `<div style="position:absolute; top:10px; right:10px; background:rgba(239,68,68,0.2); color:#F87171; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:700;">${isExpired ? 'EXPIRED' : 'INACTIVE'}</div>` : ''}
      <div class="coupon-code"><i class="fa-solid fa-ticket" style="margin-right:6px;"></i>${escapeHtml(c.code)}</div>
      <div class="coupon-discount">${discountLabel}</div>
      <p style="font-size:12px; color:#94A3B8; margin:4px 0;">Min Order: ₹${c.min_order_amount || 0}</p>
      ${c.max_discount ? `<p style="font-size:12px; color:#94A3B8; margin:2px 0;">Max Discount: ₹${c.max_discount}</p>` : ''}
      <p style="font-size:11px; color:#64748B; margin:4px 0;">${expLabel}</p>
      <p style="font-size:11px; color:#64748B; margin:2px 0 12px;">${usageLabel}</p>
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:11px; padding:3px 8px; border-radius:12px;
          background:${isActive ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)'};
          color:${isActive ? '#10B981' : '#EF4444'}; font-weight:700;">
          ${isActive ? '✅ ACTIVE' : '❌ INACTIVE'}
        </span>
        <div style="display:flex; gap:6px;">
          <button onclick="toggleCoupon(${c.id}, ${isActive ? 0 : 1})"
            style="background:transparent; border:none; color:${isActive ? '#F59E0B' : '#10B981'}; cursor:pointer; font-size:13px;" title="${isActive ? 'Deactivate' : 'Activate'}">
            <i class="fa-solid fa-${isActive ? 'pause' : 'play'}"></i>
          </button>
          <button onclick="deleteCoupon(${c.id})"
            style="background:transparent; border:none; color:#EF4444; cursor:pointer; font-size:13px;" title="Delete">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
    </div>`;
  }).join('');
}

function openAddCouponModal() {
  const modal = document.getElementById('couponModal');
  if (modal) modal.style.display = 'flex';
}
function closeCouponModal() {
  const modal = document.getElementById('couponModal');
  if (modal) modal.style.display = 'none';
}

async function handleSaveCoupon(e) {
  e.preventDefault();
  const code     = (document.getElementById('cpCode')?.value || '').trim().toUpperCase();
  const name     = code + ' Discount';
  const type     = document.getElementById('cpType')?.value || 'percentage';
  const value    = parseFloat(document.getElementById('cpValue')?.value || 0);
  const minAmt   = parseFloat(document.getElementById('cpMinAmount')?.value || 0);
  const expiry   = document.getElementById('cpExpiry')?.value || null;

  if (!code) { Toast.error('Coupon code is required!'); return; }
  if (!value || value <= 0) { Toast.error('Discount value must be > 0'); return; }

  const payload = {
    restaurant_id: 1,
    code, name,
    discount_type: type,
    discount_value: value,
    min_order_amount: minAmt,
    is_active: 1,
    expires_at: expiry || null,
  };

  try {
    const res = await fetch('/api/coupons', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
      body: JSON.stringify(payload)
    });
    if (res.ok) {
      Toast.success(`Coupon "${code}" created!`);
      closeCouponModal();
      loadCoupons();
      e.target.reset();
    } else {
      const err = await res.json();
      Toast.error(err.message || 'Failed to create coupon. Code may already exist.');
    }
  } catch (_) {
    Toast.error('Network error saving coupon.');
  }
}

async function toggleCoupon(id, newActive) {
  try {
    const res = await fetch(`/api/coupons/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
      body: JSON.stringify({ is_active: newActive })
    });
    if (res.ok) {
      Toast.success(newActive ? 'Coupon activated!' : 'Coupon deactivated!');
      loadCoupons();
    }
  } catch (_) { Toast.error('Network error.'); }
}

async function deleteCoupon(id) {
  if (!confirm('Delete this coupon? This cannot be undone.')) return;
  try {
    const res = await fetch(`/api/coupons/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
    });
    if (res.ok) {
      Toast.success('Coupon deleted.');
      loadCoupons();
    } else {
      Toast.error('Failed to delete coupon.');
    }
  } catch (_) { Toast.error('Network error.'); }
}

function escapeHtml(str) {
  return typeof str === 'string' ? str.replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]) : '';
}
