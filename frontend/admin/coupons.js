// DineFlow AI - Admin Coupons Controller (Fixed v2 - uses real DB API)

let couponsList = [];

function getCurrentRestaurantId() {
  return window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || new URLSearchParams(window.location.search).get('restaurantId') || '1';
}

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
    const res = await fetch(`/api/coupons?restaurantId=${getCurrentRestaurantId()}`);
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
      ? `Expires: ${new Date(c.expires_at).toLocaleString('en-IN', { dateStyle: 'short', timeStyle: 'short' })}`
      : 'No Expiry';
    const usageLabel = c.usage_limit
      ? `${c.used_count || 0}/${c.usage_limit} used`
      : `${c.used_count || 0} used (unlimited)`;

    return `
    <div class="coupon-card" style="position:relative; overflow:hidden; background:var(--card); border:1px solid var(--border); border-radius:18px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:8px;">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
        <div class="coupon-code" style="font-family:'Outfit',sans-serif; font-size:18px; font-weight:800; color:var(--primary); letter-spacing:0.5px;"><i class="fa-solid fa-ticket" style="margin-right:6px; color:#6366F1;"></i>${escapeHtml(c.code)}</div>
        ${!isActive ? `<div style="background:rgba(239,68,68,0.15); color:#EF4444; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:800;">${isExpired ? 'EXPIRED' : 'INACTIVE'}</div>` : ''}
      </div>

      <div style="font-size:13px; font-weight:700; color:var(--text-primary); margin-top:-4px;">${escapeHtml(c.name || (c.code + ' Offer'))}</div>

      <div class="coupon-discount" style="font-size:26px; font-weight:800; color:#10B981; margin:2px 0;">${discountLabel}</div>

      <div style="display:flex; flex-wrap:wrap; gap:6px; margin:4px 0;">
        ${c.is_auto_apply ? `<span style="background:rgba(245,158,11,0.15); color:#F59E0B; border:1px solid rgba(245,158,11,0.3); padding:2px 8px; border-radius:12px; font-size:10.5px; font-weight:700;"><i class="fa-solid fa-bolt"></i> AUTO-APPLY</span>` : ''}
        ${c.first_time_only ? `<span style="background:rgba(99,102,241,0.15); color:#818CF8; border:1px solid rgba(99,102,241,0.3); padding:2px 8px; border-radius:12px; font-size:10.5px; font-weight:700;"><i class="fa-solid fa-user-plus"></i> NEW CUSTOMERS</span>` : ''}
        ${c.category && c.category !== 'all' ? `<span style="background:rgba(6,182,212,0.15); color:#06B6D4; border:1px solid rgba(6,182,212,0.3); padding:2px 8px; border-radius:12px; font-size:10.5px; font-weight:700;"><i class="fa-solid fa-filter"></i> ${escapeHtml(c.category.toUpperCase())}</span>` : ''}
        ${c.order_type && c.order_type !== 'all' ? `<span style="background:rgba(168,85,247,0.15); color:#C084FC; border:1px solid rgba(168,85,247,0.3); padding:2px 8px; border-radius:12px; font-size:10.5px; font-weight:700;"><i class="fa-solid fa-utensils"></i> ${escapeHtml(c.order_type.toUpperCase())}</span>` : ''}
      </div>

      <div style="font-size:12px; color:var(--text-muted); display:flex; flex-direction:column; gap:3px; margin-top:4px;">
        <div>Min Order: <strong>₹${c.min_order_amount || 0}</strong></div>
        ${c.max_discount ? `<div>Max Discount Cap: <strong>₹${c.max_discount}</strong></div>` : ''}
        <div>${expLabel}</div>
        <div>${usageLabel}</div>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; margin-top:auto; padding-top:10px; border-top:1px solid var(--border);">
        <span style="font-size:11px; padding:3px 8px; border-radius:12px;
          background:${isActive ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)'};
          color:${isActive ? '#10B981' : '#EF4444'}; font-weight:700;">
          ${isActive ? '✅ ACTIVE' : '❌ INACTIVE'}
        </span>
        <div style="display:flex; gap:8px;">
          <button onclick="toggleCoupon(${c.id}, ${isActive ? 0 : 1})"
            style="background:var(--bg); border:1px solid var(--border); color:${isActive ? '#F59E0B' : '#10B981'}; width:30px; height:30px; border-radius:8px; cursor:pointer; font-size:13px; display:flex; align-items:center; justify-content:center;" title="${isActive ? 'Deactivate' : 'Activate'}">
            <i class="fa-solid fa-${isActive ? 'pause' : 'play'}"></i>
          </button>
          <button onclick="deleteCoupon(${c.id})"
            style="background:var(--bg); border:1px solid var(--border); color:#EF4444; width:30px; height:30px; border-radius:8px; cursor:pointer; font-size:13px; display:flex; align-items:center; justify-content:center;" title="Delete">
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
  const code           = (document.getElementById('cpCode')?.value || '').trim().toUpperCase();
  const name           = (document.getElementById('cpName')?.value || '').trim() || (code + ' Offer');
  const type           = document.getElementById('cpType')?.value || 'percentage';
  const value          = parseFloat(document.getElementById('cpValue')?.value || 0);
  const minAmt         = parseFloat(document.getElementById('cpMinAmount')?.value || 0);
  const maxDiscount    = parseFloat(document.getElementById('cpMaxDiscount')?.value || 0) || null;
  const orderType      = document.getElementById('cpOrderType')?.value || 'all';
  const category       = document.getElementById('cpCategory')?.value || 'all';
  const usageLimit     = parseInt(document.getElementById('cpUsageLimit')?.value || 0) || null;
  const expiry         = document.getElementById('cpExpiry')?.value || null;
  const isAutoApply    = document.getElementById('cpAutoApply')?.checked ? 1 : 0;
  const firstTimeOnly  = document.getElementById('cpFirstTimeOnly')?.checked ? 1 : 0;

  if (!code) { Toast.error('Coupon code is required!'); return; }
  if (!value || value <= 0) { Toast.error('Discount value must be > 0'); return; }

  const payload = {
    restaurant_id: getCurrentRestaurantId(),
    restaurantId: getCurrentRestaurantId(),
    code, name,
    discount_type: type,
    discountType: type,
    discount_value: value,
    discountValue: value,
    min_order_amount: minAmt,
    minOrderAmount: minAmt,
    max_discount: maxDiscount,
    maxDiscount: maxDiscount,
    order_type: orderType,
    orderType: orderType,
    category: category,
    usage_limit: usageLimit,
    usageLimit: usageLimit,
    is_active: 1,
    isActive: 1,
    is_auto_apply: isAutoApply,
    isAutoApply: isAutoApply,
    first_time_only: firstTimeOnly,
    firstTimeOnly: firstTimeOnly,
    expires_at: expiry || null,
    expiresAt: expiry || null,
  };

  try {
    const res = await fetch('/api/coupons', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
      body: JSON.stringify(payload)
    });
    if (res.ok) {
      Toast.success(`Coupon "${code}" created successfully!`);
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
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' },
      body: JSON.stringify({ is_active: newActive, isActive: newActive })
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

function applyCouponPreset(code, name, type, value, minOrder) {
  document.getElementById('cpCode').value = code;
  document.getElementById('cpName').value = name;
  document.getElementById('cpType').value = type;
  document.getElementById('cpValue').value = value;
  document.getElementById('cpMinAmount').value = minOrder;
  if (typeof Toast !== 'undefined' && Toast.success) {
    Toast.success(`Preset applied: ${code} (${name})`);
  }
}

function escapeHtml(str) {
  return typeof str === 'string' ? str.replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]) : '';
}
