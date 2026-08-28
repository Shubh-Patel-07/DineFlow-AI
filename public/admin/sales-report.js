/**
 * DineFlow AI — Sales Analytics & Financial Reports Engine v2.1.0
 * 100% Real Database Calculations & Clean Zero-Start Integration.
 */

let revenueChartInstance = null;
let categoryChartInstance = null;
let currentFilter = 'today';
const RESTAURANT_ID = window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || new URLSearchParams(window.location.search).get('restaurantId') || '1';

document.addEventListener('DOMContentLoaded', () => {
  initDateInput();
  refreshSalesAnalytics();
});

function initDateInput() {
  const input = document.getElementById('customDateInput');
  if (input) {
    input.value = new Date().toISOString().split('T')[0];
  }
}

function setAnalyticsDateFilter(filterType, btnElem) {
  currentFilter = filterType;
  document.querySelectorAll('.date-pill-btn').forEach(btn => btn.classList.remove('active'));
  if (btnElem) btnElem.classList.add('active');
  refreshSalesAnalytics();
}

function fetchCustomDateReport(selectedDate) {
  if (!selectedDate) return;
  currentFilter = 'custom';
  document.querySelectorAll('.date-pill-btn').forEach(btn => btn.classList.remove('active'));
  refreshSalesAnalytics(selectedDate);
}

async function refreshSalesAnalytics(customDate = null) {
  const targetDateStr = customDate || getDateStringForFilter(currentFilter);

  try {
    // 1. Fetch Authoritative Sales Report from Server API
    const response = await fetch(`/api/analytics/restaurant/${RESTAURANT_ID}/sales-report?filter=${currentFilter}${targetDateStr ? '&date=' + targetDateStr : ''}`);
    const result = await response.json();

    if (result.success && result.data) {
      const data = result.data;
      const summary = data.summary || {};
      const trajectory = data.revenue_trajectory || {};
      const catShare = data.category_share || {};
      const settledOrders = data.settled_orders || [];

      // Update DOM Metrics
      updateMetricCards(summary);

      // Render Charts
      renderRevenueTrajectoryChart(trajectory);
      renderCategoryShareChart(catShare);

      // Render Orders Table
      renderSettledOrdersTable(settledOrders);
      return;
    }
  } catch (err) {
    console.warn('API sales report fetch failed, falling back to orders endpoint:', err);
  }

  // 2. Client-Side Fallback via /api/orders (Zero Mock Data Guarantee)
  try {
    const ordersRes = await fetch(`/api/orders?restaurant_id=${RESTAURANT_ID}`);
    const ordersJson = await ordersRes.json();
    const ordersList = Array.isArray(ordersJson.data) ? ordersJson.data : (Array.isArray(ordersJson) ? ordersJson : []);

    computeClientSideAnalytics(ordersList, targetDateStr);
  } catch (e) {
    console.error('Failed to load orders for sales analytics:', e);
    renderZeroStateAnalytics();
  }
}

function updateMetricCards(summary) {
  const grossRev = parseFloat(summary.gross_revenue || 0);
  const totalOrders = parseInt(summary.total_orders || 0);
  const taxCollected = parseFloat(summary.tax_collected || 0);
  const avgTicket = parseFloat(summary.avg_order_value || 0);
  const upiShare = parseInt(summary.upi_share || 0);

  const elGross = document.getElementById('statGrossRevenue');
  const elOrders = document.getElementById('statTotalOrders');
  const elTax = document.getElementById('statTaxCollected');
  const elAvg = document.getElementById('statAvgOrderValue');
  const elUpi = document.getElementById('statUpiShare');

  if (elGross) elGross.textContent = `₹${grossRev.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  if (elOrders) elOrders.textContent = totalOrders;
  if (elTax) elTax.textContent = `₹${taxCollected.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  if (elAvg) elAvg.textContent = `₹${avgTicket.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  if (elUpi) elUpi.textContent = `${upiShare}%`;
}

function getDateStringForFilter(filter) {
  const d = new Date();
  if (filter === 'yesterday') {
    d.setDate(d.getDate() - 1);
  }
  return d.toISOString().split('T')[0];
}

function renderRevenueTrajectoryChart(trajectory) {
  const canvas = document.getElementById('revenueTrendChart');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  const labels = (trajectory && trajectory.labels && trajectory.labels.length) ? trajectory.labels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  const dataPoints = (trajectory && trajectory.data && trajectory.data.length) ? trajectory.data : [0, 0, 0, 0, 0, 0, 0];
  const totalTraj = dataPoints.reduce((a, b) => a + Number(b || 0), 0);

  const subtitle = document.getElementById('salesTrendSubtitle');
  if (subtitle) {
    subtitle.textContent = `7-Day Trend (₹${totalTraj.toLocaleString('en-IN')} Total)`;
  }

  if (revenueChartInstance) {
    revenueChartInstance.destroy();
    revenueChartInstance = null;
  }

  const isLight = document.documentElement.classList.contains('light-theme') || document.body.classList.contains('light-theme');
  const tickColor = isLight ? '#64748B' : '#94A3B8';
  const gridColor = isLight ? '#E2E8F0' : 'rgba(255,255,255,0.05)';

  revenueChartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Daily Revenue (₹)',
        data: dataPoints,
        borderColor: '#6366F1',
        backgroundColor: isLight ? 'rgba(99, 102, 241, 0.10)' : 'rgba(99, 102, 241, 0.18)',
        fill: true,
        tension: 0.35,
        borderWidth: 3,
        pointBackgroundColor: '#6366F1',
        pointRadius: 4,
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(context) {
              return ' Revenue: ₹' + Number(context.parsed.y).toLocaleString('en-IN');
            }
          }
        }
      },
      scales: {
        x: {
          grid: { color: gridColor },
          ticks: { color: tickColor, font: { family: "'Outfit', sans-serif", weight: '600' } }
        },
        y: {
          beginAtZero: true,
          grid: { color: gridColor },
          ticks: {
            color: tickColor,
            font: { family: "'Outfit', sans-serif", weight: '600' },
            callback: (val) => `₹${val}`
          }
        }
      }
    }
  });
}

function renderCategoryShareChart(catShare) {
  const canvas = document.getElementById('categoryShareChart');
  const emptyState = document.getElementById('categoryEmptyState');
  if (!canvas) return;

  if (categoryChartInstance) {
    categoryChartInstance.destroy();
    categoryChartInstance = null;
  }

  const hasData = catShare && catShare.has_data && Array.isArray(catShare.data) && catShare.data.length > 0 && catShare.data.some(v => v > 0);

  if (!hasData) {
    canvas.style.display = 'none';
    if (emptyState) emptyState.style.display = 'flex';
    return;
  }

  canvas.style.display = 'block';
  if (emptyState) emptyState.style.display = 'none';

  const ctx = canvas.getContext('2d');
  const isLight = document.documentElement.classList.contains('light-theme') || document.body.classList.contains('light-theme');

  categoryChartInstance = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: catShare.labels,
      datasets: [{
        data: catShare.data,
        backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#06B6D4', '#EC4899', '#8B5CF6', '#F97316'],
        borderColor: isLight ? '#FFFFFF' : '#111827',
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 12,
            font: { size: 11, family: "'Outfit', sans-serif", weight: '600' },
            color: isLight ? '#475569' : '#94A3B8'
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return ` ${context.label}: ₹${Number(context.parsed).toLocaleString('en-IN')}`;
            }
          }
        }
      },
      cutout: '65%'
    }
  });
}

function renderSettledOrdersTable(orders) {
  const tbody = document.getElementById('settledOrdersTbody');
  const countBadge = document.getElementById('tableCountBadge');
  if (!tbody) return;

  if (!orders || orders.length === 0) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; color:var(--text-muted); padding:36px;">
      <i class="fa-solid fa-receipt" style="font-size:28px; opacity:0.3; display:block; margin-bottom:8px;"></i>
      No settled orders recorded for the selected date.
    </td></tr>`;
    if (countBadge) countBadge.textContent = '0 Orders';
    return;
  }

  if (countBadge) countBadge.textContent = `${orders.length} Settled Orders`;

  tbody.innerHTML = orders.slice(0, 25).map(o => {
    let itemsSummary = 'Standard Dine-In';
    if (o.items && o.items.length > 0) {
      itemsSummary = o.items.map(i => `${i.quantity}x ${i.name || 'Dish'}`).slice(0, 2).join(', ');
      if (o.items.length > 2) itemsSummary += `, +${o.items.length - 2} more`;
    }

    const isUpi = String(o.payment_method || '').toLowerCase().includes('upi') || String(o.payment_method || '').toLowerCase().includes('online');
    const payBadge = isUpi ? `<span class="badge-status paid">📱 UPI</span>` : `<span class="badge-status paid">💵 Cash</span>`;
    const orderTime = o.created_at_time || (o.created_at ? new Date(o.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '--:--');
    const tblLabel = o.table_number || `Table ${o.table_id || '1'}`;
    const totAmount = parseFloat(o.total_amount || 0);
    const taxAmount = parseFloat(o.tax_amount || (totAmount * 0.05 / 1.05));

    return `
      <tr>
        <td style="font-weight:700; font-family:'Outfit',sans-serif; color:var(--primary);">#${o.id}</td>
        <td><span style="font-weight:700;">${escapeHtml(tblLabel)}</span></td>
        <td style="color:var(--text-muted); font-size:12px;">${orderTime}</td>
        <td style="max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(itemsSummary)}">${escapeHtml(itemsSummary)}</td>
        <td>${payBadge}</td>
        <td style="color:var(--text-muted);">₹${taxAmount.toFixed(2)}</td>
        <td style="font-weight:800; font-family:'Outfit',sans-serif; color:#10B981;">₹${totAmount.toFixed(2)}</td>
        <td>
          <a href="/orders/${o.id}/receipt" target="_blank" style="padding:4px 10px; background:var(--primary-light); color:var(--primary); border-radius:8px; text-decoration:none; font-size:11.5px; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
            <i class="fa-solid fa-receipt"></i> Bill
          </a>
        </td>
      </tr>
    `;
  }).join('');
}

function computeClientSideAnalytics(orders, targetDateStr) {
  const filteredOrders = filterOrdersByDate(orders, currentFilter, targetDateStr);
  const paidOrders = filteredOrders.filter(o => o.payment_status === 'paid' || o.status === 'completed');

  let grossRev = 0;
  let taxCollected = 0;
  let upiCount = 0;
  let totalPaid = paidOrders.length;
  const catTotals = {};

  paidOrders.forEach(o => {
    const tot = parseFloat(o.total_amount || 0);
    const tax = parseFloat(o.tax_amount || (tot * 0.05 / 1.05));
    grossRev += tot;
    taxCollected += tax;
    const method = String(o.payment_method || '').toLowerCase();
    if (method.includes('upi') || method.includes('online') || method.includes('gpay')) {
      upiCount++;
    }

    if (Array.isArray(o.items)) {
      o.items.forEach(i => {
        const cat = i.menu_item?.category || i.category || 'General';
        const lineVal = parseFloat(i.price || 0) * parseInt(i.quantity || 1);
        catTotals[cat] = (catTotals[cat] || 0) + lineVal;
      });
    }
  });

  const avgTicket = totalPaid > 0 ? (grossRev / totalPaid) : 0;
  const upiPct = totalPaid > 0 ? Math.round((upiCount / totalPaid) * 100) : 0;

  updateMetricCards({
    gross_revenue: grossRev,
    total_orders: totalPaid,
    tax_collected: taxCollected,
    avg_order_value: avgTicket,
    upi_share: upiPct
  });

  // Build past 7 days trajectory from client orders
  const refDate = new Date();
  const trajLabels = [];
  const trajData = [];
  for (let i = 6; i >= 0; i--) {
    const d = new Date(refDate);
    d.setDate(d.getDate() - i);
    const dayStr = d.toISOString().split('T')[0];
    const dayLabel = d.toLocaleDateString('en-US', { weekday: 'short' });
    trajLabels.push(dayLabel);

    const daySum = orders
      .filter(o => (o.payment_status === 'paid' || o.status === 'completed') && o.created_at && o.created_at.startsWith(dayStr))
      .reduce((sum, o) => sum + parseFloat(o.total_amount || 0), 0);
    trajData.push(daySum);
  }

  renderRevenueTrajectoryChart({ labels: trajLabels, data: trajData });

  const catLabels = Object.keys(catTotals);
  const catData = Object.values(catTotals);
  renderCategoryShareChart({
    labels: catLabels,
    data: catData,
    has_data: catLabels.length > 0 && catData.some(v => v > 0)
  });

  renderSettledOrdersTable(paidOrders);
}

function renderZeroStateAnalytics() {
  updateMetricCards({ gross_revenue: 0, total_orders: 0, tax_collected: 0, avg_order_value: 0, upi_share: 0 });
  renderRevenueTrajectoryChart({ labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], data: [0, 0, 0, 0, 0, 0, 0] });
  renderCategoryShareChart({ labels: [], data: [], has_data: false });
  renderSettledOrdersTable([]);
}

function filterOrdersByDate(orders, filter, targetDateStr) {
  const now = new Date();
  return orders.filter(o => {
    if (!o.created_at) return true;
    const orderDate = new Date(o.created_at);
    const orderDateStr = orderDate.toISOString().split('T')[0];

    if (filter === 'today' || filter === 'yesterday' || filter === 'custom') {
      return orderDateStr === targetDateStr;
    } else if (filter === '7days') {
      const diffTime = Math.abs(now - orderDate);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return diffDays <= 7;
    } else if (filter === 'month') {
      return orderDate.getMonth() === now.getMonth() && orderDate.getFullYear() === now.getFullYear();
    }
    return true;
  });
}

function escapeHtml(text) {
  if (!text) return '';
  return String(text).replace(/[&<>"']/g, function(m) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
  });
}
