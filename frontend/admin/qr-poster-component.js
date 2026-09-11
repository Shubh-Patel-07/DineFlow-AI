/**
 * DineFlow AI — Shared Master QR Poster Component
 * Produces 100% consistent, dynamic table QR posters across:
 * - Table preview modals
 * - Quick QR prints
 * - Standee generator preview & exports
 * - Bulk/individual table cards
 */

(function(window) {
  'use strict';

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function getFirstLetter(name) {
    if (!name) return 'D';
    return name.trim().charAt(0).toUpperCase() || 'D';
  }

  /**
   * Builds the complete HTML string for the Master Table QR Poster
   */
  window.buildDineflowQrPosterHtml = function(options) {
    const opt = options || {};
    const restName = opt.restaurantName || localStorage.getItem('dineflow_restaurant_name') || 'DineFlow Restaurant';
    const restLogo = opt.restaurantLogo || localStorage.getItem('dineflow_restaurant_logo') || '';
    const rawTable = opt.tableNumber || '1';
    const tableNumber = String(rawTable).toUpperCase().startsWith('T-') || String(rawTable).toUpperCase().startsWith('TABLE') 
      ? String(rawTable) 
      : `T-${String(rawTable).padStart(2, '0')}`;
    const qrUrl = opt.qrUrl || '';
    const qrId = opt.qrElementId || `dineflow_qr_${Math.random().toString(36).substr(2, 9)}`;
    const sizeClass = opt.size ? `size-${opt.size}` : 'size-preview';
    const extraClass = opt.customClass || '';

    // Logo / Monogram HTML
    let logoHtml = '';
    if (restLogo) {
      logoHtml = `
        <div class="dineflow-qr-logo-box">
          <img class="dineflow-qr-logo-img" src="${escapeHtml(restLogo)}" alt="${escapeHtml(restName)} Logo" onerror="this.parentElement.innerHTML='<div class=\\'dineflow-qr-monogram\\'>${getFirstLetter(restName)}</div>'">
        </div>
      `;
    } else {
      logoHtml = `
        <div class="dineflow-qr-logo-box">
          <div class="dineflow-qr-monogram">${getFirstLetter(restName)}</div>
        </div>
      `;
    }

    // QR Image / Canvas HTML
    let qrHtml = '';
    if (qrUrl) {
      qrHtml = `<img class="dineflow-qr-code-img" src="${escapeHtml(qrUrl)}" alt="QR Code for Table ${escapeHtml(tableNumber)}" id="${qrId}_img">`;
    } else {
      qrHtml = `<div id="${qrId}" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;"></div>`;
    }

    return `
      <div class="dineflow-qr-poster-card ${sizeClass} ${extraClass}">
        <!-- 0. TOP ACCENT PILL -->
        <div class="dineflow-qr-accent-pill"></div>

        <!-- 1. TOP SECTION: RESTAURANT BRANDING -->
        <div class="dineflow-qr-poster-header">
          ${logoHtml}
          <h2 class="dineflow-qr-rest-name">${escapeHtml(restName)}</h2>
          <div class="dineflow-qr-tagline">
            <span class="dineflow-qr-sparkle">✨</span> Scan to View Menu &amp; Order &bull; Digital Menu
          </div>
        </div>

        <!-- 2. MIDDLE SECTION: HERO QR CARD CONTAINER -->
        <div class="dineflow-qr-body">
          <div class="dineflow-qr-card-container">
            <div class="dineflow-qr-frame">
              ${qrHtml}
            </div>
            <div class="dineflow-qr-table-badge">
              Table: ${escapeHtml(tableNumber)}
            </div>
            <p class="dineflow-qr-instruction">
              <i class="fa-solid fa-camera"></i> Point your phone camera to scan &amp; view menu
            </p>
          </div>
        </div>

        <!-- 3. DIVIDER -->
        <div class="dineflow-qr-divider"></div>

        <!-- 4. BOTTOM SECTION: DINEFLOW & INSTAGRAM FOOTER -->
        <div class="dineflow-qr-footer">
          <div class="dineflow-qr-powered-row">
            <span class="dineflow-qr-powered-text">Powered by DineFlow</span>
            <img class="dineflow-qr-tiny-logo" src="/images/dineflow_logo.jpg" alt="DineFlow" onerror="this.style.display='none'">
            <span class="dineflow-qr-smp-pill">SMP</span>
          </div>
          <div class="dineflow-qr-instagram-row">
            <a href="https://www.instagram.com/smp.officials/" target="_blank" rel="noopener noreferrer" class="dineflow-qr-instagram-link">
              <i class="fa-brands fa-instagram dineflow-qr-instagram-icon"></i>
              <span>@smp.officials</span>
            </a>
          </div>
          <p class="dineflow-qr-subtext">Thank you for dining with us! No app download required.</p>
        </div>
      </div>
    `;
  };

  /**
   * Renders the poster into a specified DOM container
   */
  window.renderDineflowQrPoster = function(containerOrId, options) {
    const container = typeof containerOrId === 'string' ? document.getElementById(containerOrId) : containerOrId;
    if (!container) return;

    const opt = options || {};
    const qrId = opt.qrElementId || `dineflow_qr_${Math.random().toString(36).substr(2, 9)}`;
    opt.qrElementId = qrId;

    container.innerHTML = window.buildDineflowQrPosterHtml(opt);

    // If QR was not an image URL, render via QRCodeJS if available
    if (!opt.qrUrl && opt.qrDataUrl && typeof QRCode !== 'undefined') {
      const qrBox = document.getElementById(qrId);
      if (qrBox) {
        qrBox.innerHTML = '';
        new QRCode(qrBox, {
          text: opt.qrDataUrl,
          width: 180,
          height: 180,
          colorDark: '#0F172A',
          colorLight: '#FFFFFF',
          correctLevel: QRCode.CorrectLevel.H
        });
      }
    }
  };

})(window);
