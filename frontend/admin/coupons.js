// DineFlow AI - Admin Coupons Controller

let couponsList = [
  { id: 1, code: 'WELCOME10', type: 'percent', value: 10, minAmount: 200, active: true },
  { id: 2, code: 'FLAT50', type: 'flat', value: 50, minAmount: 350, active: true },
  { id: 3, code: 'FESTIVE20', type: 'percent', value: 20, minAmount: 500, active: true }
];

function initCoupons() {
  if (typeof Auth !== 'undefined' && Auth.checkAuth) Auth.checkAuth();
  const saved = localStorage.getItem('dineflow_coupons');
  if (saved) {
    try { couponsList = JSON.parse(saved); } catch (_) {}
  }
  const loader = document.querySelector('[data-loader]');
  if (loader) loader.style.display = 'none';
  renderCoupons();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCoupons);
} else {
  initCoupons();
}


function renderCoupons() {
  const container = document.getElementById('couponGridContainer');
  if (!container) return;

  if (!couponsList.length) {
    container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:40px; color:#94A3B8;">No active coupons found.</div>`;
    return;
  }

  container.innerHTML = couponsList.map(c => `
    <div class="coupon-card">
      <div class="coupon-code"><i class="fa-solid fa-ticket" style="margin-right:6px;"></i> ${escapeHtml(c.code)}</div>
      <div class="coupon-discount">${c.type === 'percent' ? `${c.value}% OFF` : `₹${c.value} OFF`}</div>
      <p style="font-size:12px; color:#94A3B8; margin:0 0 14px;">Min Order: ₹${c.minAmount}</p>
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:11px; padding:3px 8px; border-radius:12px; background:${c.active ? 'rgba(16,185,129,0.15)' : 'rgba(239,68,68,0.15)'}; color:${c.active ? '#10B981' : '#EF4444'}; font-weight:700;">${c.active ? 'ACTIVE' : 'EXPIRED'}</span>
        <button onclick="deleteCoupon(${c.id})" style="background:transparent; border:none; color:#EF4444; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
      </div>
    </div>
  `).join('');
}

function openAddCouponModal() { document.getElementById('couponModal').style.display = 'flex'; }
function closeCouponModal() { document.getElementById('couponModal').style.display = 'none'; }

function handleSaveCoupon(e) {
  e.preventDefault();
  const newCoupon = {
    id: Date.now(),
    code: document.getElementById('cpCode').value.trim().toUpperCase(),
    type: document.getElementById('cpType').value,
    value: parseFloat(document.getElementById('cpValue').value),
    minAmount: parseFloat(document.getElementById('cpMinAmount').value),
    active: true
  };
  couponsList.push(newCoupon);
  localStorage.setItem('dineflow_coupons', JSON.stringify(couponsList));
  renderCoupons();
  closeCouponModal();
  Toast.success('Promo coupon created!');
}

function deleteCoupon(id) {
  couponsList = couponsList.filter(c => c.id !== id);
  localStorage.setItem('dineflow_coupons', JSON.stringify(couponsList));
  renderCoupons();
  Toast.success('Coupon removed.');
}

function escapeHtml(str) { return typeof str === 'string' ? str.replace(/[&<>"']/g, m => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[m]) : ''; }
