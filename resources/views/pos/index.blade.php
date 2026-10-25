<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>DineFlow AI POS — POS Terminal</title>
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#6366F1">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="DineFlow AI">
  <link rel="apple-touch-icon" href="/images/dineflow_logo.jpg">
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <script>
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
      });
    }
  </script>
  <script src="/frontend/admin/utils.js"></script>
  <script src="/js/escpos-printer.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --primary:        #9F1239; /* Deep Burgundy / Velvet Wine */
      --primary-light:  rgba(159, 18, 57, 0.08);
      --primary-dark:   #881337;
      --success:        #22C55E;
      --success-light:  rgba(34, 197, 94, 0.1);
      --danger:         #EF4444;
      --danger-light:   rgba(239, 68, 68, 0.1);
      --warning:        #F59E0B;
      --warning-light:  rgba(245, 158, 11, 0.1);
      --info:           #3B82F6;
      --info-light:     rgba(59, 130, 246, 0.1);
      --sidebar-bg:     #101012;
      --sidebar-hover:  #1C1C20;
      --sidebar-active: #FB7185;
      --bg:             #0B0B0D;
      --card:           #151517;
      --border:         #26262A;
      --text-primary:   #F4F4F5;
      --text-secondary: #D4D4D8;
      --text-muted:     #A1A1AA;
      --shadow-sm:      0 1px 3px rgba(0,0,0,.3), 0 1px 2px rgba(0,0,0,.2);
      --shadow-md:      0 4px 6px rgba(0,0,0,.4), 0 2px 4px rgba(0,0,0,.3);
      --shadow-lg:      0 10px 15px rgba(0,0,0,.5), 0 4px 6px rgba(0,0,0,.4);
      --radius-sm:      8px;
      --radius-md:      12px;
      --radius-lg:      20px;
    }

    /* Global Select Dropdown Option Visibility Fix */
    select option {
      background-color: #151517 !important;
      color: #F4F4F5 !important;
    }
    body.light-theme select option {
      background-color: #FFFFFF !important;
      color: #111827 !important;
    }

    /* ─── 100% FULL LIGHT THEME ENGINE OVERRIDES (Warm Porcelain & Velvet Wine) ─── */
    body.light-theme {
      --sidebar-bg:     #FCFCFC;
      --sidebar-hover:  #F5F1EF;
      --sidebar-active: #9F1239;
      --bg:             #FBF9F8;
      --card:           #FFFFFF;
      --border:         #ECE6E4;
      --text-primary:   #111827;
      --text-secondary: #4B5563;
      --text-muted:     #6B7280;
      --primary:        #9F1239;
      --primary-light:  rgba(159, 18, 57, 0.08);
      --primary-dark:   #881337;
      --shadow-sm:      0 1px 3px rgba(0,0,0,.03);
      --shadow-md:      0 4px 14px rgba(0,0,0,.05);
      --shadow-lg:      0 12px 32px rgba(0,0,0,.08);
    }
    body.light-theme .sidebar {
      background: #FFFFFF !important;
      border-right: 1px solid #E2E8F0 !important;
    }
    body.light-theme .sidebar-brand {
      border-bottom-color: #E2E8F0 !important;
    }
    body.light-theme .brand-text h2 {
      color: #0F172A !important;
    }
    body.light-theme .brand-text span {
      color: #64748B !important;
    }
    body.light-theme .sidebar-toggle {
      background: #F1F5F9 !important;
    }
    body.light-theme .sidebar-toggle i {
      color: #475569 !important;
    }
    body.light-theme .sidebar-section-label {
      color: #94A3B8 !important;
    }
    body.light-theme .nav-item {
      color: #475569 !important;
    }
    body.light-theme .nav-item:hover {
      background: #F1F5F9 !important;
      color: #0F172A !important;
    }
    body.light-theme .nav-item.active {
      background: var(--primary) !important;
      color: #FFFFFF !important;
    }
    body.light-theme .sidebar-footer {
      border-top-color: #E2E8F0 !important;
    }
    body.light-theme .user-card:hover {
      background: #F1F5F9 !important;
    }
    body.light-theme .user-info h4 {
      color: #0F172A !important;
    }
    body.light-theme .user-info p {
      color: #64748B !important;
    }
    body.light-theme .topbar {
      background: #FFFFFF !important;
      border-bottom-color: #E2E8F0 !important;
    }
    body.light-theme .order-meta {
      background: #FFFFFF !important;
      border-bottom-color: #E2E8F0 !important;
    }
    body.light-theme .delivery-tabs {
      background: #F1F5F9 !important;
      border-color: #E2E8F0 !important;
    }
    body.light-theme .delivery-tab {
      color: #64748B !important;
    }
    body.light-theme .delivery-tab.active {
      background: #FFFFFF !important;
      color: var(--primary) !important;
      box-shadow: 0 2px 6px rgba(0,0,0,0.06) !important;
    }
    body.light-theme .meta-picker-btn {
      background: #F8FAFC !important;
      border-color: #E2E8F0 !important;
      color: #0F172A !important;
    }
    body.light-theme .search-box input {
      background: #F1F5F9 !important;
      color: #0F172A !important;
      border-color: #CBD5E1 !important;
    }
    body.light-theme .cat-btn {
      background: #FFFFFF !important;
      color: #475569 !important;
      border-color: #E2E8F0 !important;
    }
    body.light-theme .cat-btn.active {
      background: var(--primary) !important;
      color: #FFFFFF !important;
    }
    body.light-theme .item-card {
      background: #FFFFFF !important;
      border: 1.5px solid #CBD5E1 !important;
      border-radius: 14px !important;
      min-height: 200px !important;
      height: 100% !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04) !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
      transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease !important;
      overflow: hidden !important;
      content-visibility: auto !important;
      contain-intrinsic-size: 200px 150px !important;
      transform: translateZ(0) !important;
      will-change: transform !important;
    }
    body.light-theme .item-card:hover {
      border-color: #6366F1 !important;
      box-shadow: 0 8px 24px rgba(99,102,241,0.18) !important;
      transform: translateY(-3px) scale(1.01) !important;
    }
    body.light-theme .item-card.in-cart {
      background: #F0FDF4 !important;
      border-color: #10B981 !important;
      box-shadow: 0 4px 16px rgba(16, 185, 129, 0.2) !important;
    }
    body.light-theme .item-img {
      height: 90px !important;
      min-height: 90px !important;
      background: linear-gradient(135deg, #F8FAFC 0%, #EEF2FF 100%) !important;
      border-bottom: 1.5px solid #E2E8F0 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-size: 38px !important;
    }
    body.light-theme .item-body {
      padding: 10px 12px 12px !important;
      display: flex !important;
      flex-direction: column !important;
      justify-content: space-between !important;
      flex: 1 !important;
      min-height: 105px !important;
    }
    body.light-theme .item-name {
      color: #0F172A !important;
      font-size: 13px !important;
      font-weight: 800 !important;
      line-height: 1.35 !important;
      margin: 4px 0 6px !important;
      word-break: break-word !important;
      overflow-wrap: break-word !important;
      display: -webkit-box !important;
      -webkit-line-clamp: 2 !important;
      -webkit-box-orient: vertical !important;
      overflow: hidden !important;
      height: 2.7em !important;
    }
    body.light-theme .item-price {
      color: #4338CA !important;
      font-size: 15px !important;
      font-weight: 800 !important;
    }
    body.light-theme .item-price small {
      color: #64748B !important;
      font-size: 9.5px !important;
    }
    body.light-theme .item-category-tag {
      color: #6366F1 !important;
      background: rgba(99, 102, 241, 0.08) !important;
      font-size: 10px !important;
      font-weight: 700 !important;
      padding: 2px 7px !important;
      border-radius: 6px !important;
      display: inline-block !important;
      width: fit-content !important;
    }
    body.light-theme .topbar-title {
      color: #0F172A !important;
    }
    body.light-theme .topbar-sub {
      color: #64748B !important;
    }
    body.light-theme .pos-skeleton-card {
      background: #FFFFFF !important;
      border: 1.5px solid #E2E8F0 !important;
      border-radius: 14px !important;
      min-height: 200px !important;
      height: 100% !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
    }
    body.light-theme .pos-skeleton-img {
      background: linear-gradient(90deg, #F1F5F9 25%, #E2E8F0 50%, #F1F5F9 75%) !important;
      background-size: 200% 100% !important;
      height: 90px !important;
    }
    body.light-theme .pos-skeleton-line {
      background: linear-gradient(90deg, #F1F5F9 25%, #E2E8F0 50%, #F1F5F9 75%) !important;
      background-size: 200% 100% !important;
    }

    /* Topbar cluster layouts & resilient right-side anchoring */
    .topbar-left-cluster {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-shrink: 0 !important;
    }
    .topbar-center-cluster {
      display: flex;
      align-items: center;
      gap: 5px;
      flex-shrink: 1;
      min-width: 0;
      overflow: hidden;
      white-space: nowrap;
    }
    .topbar-right-cluster {
      display: flex;
      align-items: center;
      gap: 7px;
      flex-shrink: 0 !important;
      margin-left: auto;
      background: var(--card);
      position: relative;
      z-index: 5;
    }
    .pos-quick-logout-form {
      margin: 0;
      flex-shrink: 0 !important;
    }
    .pos-quick-logout-btn {
      background: rgba(239, 68, 68, 0.12);
      border: 1.5px solid rgba(239, 68, 68, 0.35);
      color: #EF4444;
      height: 33px;
      padding: 0 11px;
      border-radius: 9px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
      font-size: 11.5px;
      font-weight: 800;
      transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
      flex-shrink: 0 !important;
      white-space: nowrap;
      user-select: none;
    }
    .pos-quick-logout-btn:hover {
      background: #EF4444 !important;
      border-color: #EF4444 !important;
      color: #FFFFFF !important;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
    }
    .pos-quick-logout-btn:active {
      transform: translateY(0);
    }
    body.light-theme .pos-quick-logout-btn {
      background: #FEF2F2 !important;
      border: 1.5px solid #FECACA !important;
      color: #DC2626 !important;
    }
    body.light-theme .pos-quick-logout-btn:hover {
      background: #EF4444 !important;
      border-color: #EF4444 !important;
      color: #FFFFFF !important;
    }
    /* Cashier User Pill (Base & Light Theme) */
    .user-pill-btn {
      display: inline-flex !important;
      align-items: center !important;
      gap: 7px !important;
      padding: 3px 9px 3px 6px !important;
      height: 33px !important;
      border-radius: 9px !important;
      cursor: pointer !important;
      background: var(--bg) !important;
      border: 1.5px solid var(--border) !important;
      transition: all 0.15s ease !important;
      flex-shrink: 0 !important;
      user-select: none !important;
      box-sizing: border-box !important;
    }
    .user-pill-btn:hover {
      border-color: var(--primary) !important;
      background: var(--card) !important;
    }
    .user-pill-btn i.fa-chevron-down {
      font-size: 9px !important;
      color: var(--text-muted) !important;
      margin-left: 2px !important;
      align-self: center !important;
    }
    .user-pill-text {
      display: flex !important;
      flex-direction: column !important;
      line-height: 1.15 !important;
      min-width: 0 !important;
      justify-content: center !important;
    }
    .user-pill-text span:first-child {
      font-size: 11.5px !important;
      font-weight: 700 !important;
      color: var(--text-primary) !important;
      max-width: 95px !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      white-space: nowrap !important;
    }
    .user-pill-text span:last-child {
      font-size: 9.5px !important;
      color: var(--text-muted) !important;
      text-transform: capitalize !important;
      font-weight: 600 !important;
    }
    body.light-theme .user-pill-btn {
      background: #F8FAFC !important;
      border: 1.5px solid #E2E8F0 !important;
    }
    body.light-theme .user-pill-btn:hover {
      background: #FFFFFF !important;
      border-color: #6366F1 !important;
      box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08) !important;
    }

    /* Topbar responsive compactness on 1366px - 1600px laptop/desktop screens */
    @media (max-width: 1600px) {
      .topbar {
        padding: 0 10px !important;
        gap: 6px !important;
      }
      .pos-quick-tool-btn {
        padding: 0 8px !important;
        font-size: 11px !important;
        gap: 5px !important;
        height: 32px !important;
      }
    }

    @media (max-width: 1440px) {
      .pos-quick-tool-btn span:not([id*="Badge"]):not([id*="badge"]):not([id*="Count"]) {
        display: none !important;
      }
      #posShiftText {
        display: none !important;
      }
      .pos-quick-tool-btn {
        width: 32px !important;
        padding: 0 !important;
        justify-content: center !important;
      }
      .user-pill-text span:first-child {
        max-width: 75px !important;
      }
    }

    @media (max-width: 1366px) {
      .pos-quick-logout-btn {
        width: 33px !important;
        padding: 0 !important;
        justify-content: center !important;
      }
      .quick-logout-text {
        display: none !important;
      }
      .user-pill-text span:first-child {
        max-width: 65px !important;
      }
    }

    @media (max-width: 1200px) {
      #posNetworkStatusText {
        display: none !important;
      }
      #posNetworkStatusBadge {
        padding: 0 8px !important;
        width: 33px !important;
        justify-content: center !important;
      }
      .user-pill-text {
        display: none !important;
      }
    }

    /* Sidebar In/Out & Header Brand syncing */
    @media (min-width: 1024px) {
      /* When sidebar is expanded, hide duplicate brand name in topbar */
      body:not(.sidebar-collapsed-mode) .topbar-duplicate-brand {
        display: none !important;
      }
    }
    /* When sidebar is collapsed on desktop, omit duplicate avatar because the 64px rail already renders it */
    @media (min-width: 769px) {
      .topbar-duplicate-brand .topbar-brand-avatar {
        display: none !important;
      }
    }
    #liveClockBadge {
      background: rgba(99, 102, 241, 0.15);
      border: 1px solid rgba(99, 102, 241, 0.35);
      color: #818CF8;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 700;
      font-family: monospace;
      letter-spacing: 0.5px;
    }
    body.light-theme #liveClockBadge {
      background: #EEF2FF !important;
      border: 1.5px solid #C7D2FE !important;
      color: #4338CA !important;
      font-weight: 800 !important;
      box-shadow: 0 2px 6px rgba(99, 102, 241, 0.08) !important;
    }
    #localWeatherBadge {
      background: rgba(16, 185, 129, 0.15);
      border: 1px solid rgba(16, 185, 129, 0.35);
      color: #34D399;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11.5px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    body.light-theme #localWeatherBadge {
      background: #ECFDF5 !important;
      border: 1.5px solid #A7F3D0 !important;
      color: #065F46 !important;
      font-weight: 800 !important;
      box-shadow: 0 2px 6px rgba(16, 185, 129, 0.08) !important;
    }
    body.light-theme .pos-right {
      background: #FFFFFF !important;
      border-left-color: #E2E8F0 !important;
    }
    body.light-theme .cart-header {
      background: #FFFFFF !important;
      border-bottom-color: #E2E8F0 !important;
    }
    body.light-theme .customer-strip {
      background: #F8FAFC !important;
      border-bottom-color: #E2E8F0 !important;
    }
    body.light-theme .cart-item {
      background: #F8FAFC !important;
      border-color: #E2E8F0 !important;
    }
    body.light-theme .cart-footer {
      background: #FFFFFF !important;
      border-top-color: #E2E8F0 !important;
    }
    body.light-theme .modal-dialog,
    body.light-theme .modal-content,
    body.light-theme .modal,
    body.light-theme .modal-overlay .modal {
      background: #FFFFFF !important;
      color: #0F172A !important;
      border-color: #E2E8F0 !important;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05) !important;
    }
    body.light-theme .modal-header {
      background: #FFFFFF !important;
      border-bottom-color: #E2E8F0 !important;
    }
    body.light-theme .modal-title {
      color: #0F172A !important;
    }
    body.light-theme .modal-body {
      background: #FFFFFF !important;
      color: #0F172A !important;
    }
    body.light-theme .modal-footer {
      background: #F8FAFC !important;
      border-top-color: #E2E8F0 !important;
    }
    body.light-theme .modal-close {
      background: #F1F5F9 !important;
      color: #475569 !important;
      border-color: #CBD5E1 !important;
    }
    body.light-theme .modal-close:hover {
      background: #E2E8F0 !important;
      color: #0F172A !important;
    }

    /* Customer Modal & Search Inputs Light Mode */
    body.light-theme .cust-search,
    body.light-theme input[type="text"],
    body.light-theme input[type="search"],
    body.light-theme input[type="number"],
    body.light-theme input[type="email"],
    body.light-theme input[type="password"],
    body.light-theme input[type="tel"],
    body.light-theme textarea,
    body.light-theme select,
    body.light-theme .form-control {
      background: #F1F5F9 !important;
      color: #0F172A !important;
      border-color: #CBD5E1 !important;
    }
    body.light-theme .cust-search::placeholder,
    body.light-theme input::placeholder,
    body.light-theme textarea::placeholder {
      color: #94A3B8 !important;
    }
    body.light-theme .cust-row {
      background: #F8FAFC !important;
      border-color: #E2E8F0 !important;
    }
    body.light-theme .cust-row:hover {
      background: #EEF2FF !important;
      border-color: #C7D2FE !important;
    }
    body.light-theme .cust-row.selected {
      background: #E0E7FF !important;
      border-color: #818CF8 !important;
    }
    body.light-theme .cr-info h5 {
      color: #0F172A !important;
    }
    body.light-theme .cr-info p {
      color: #64748B !important;
    }

    /* Form Labels, Buttons & Table Picker Cards Light Mode */
    body.light-theme label,
    body.light-theme .form-label {
      color: #334155 !important;
    }
    body.light-theme .btn-outline {
      background: #FFFFFF !important;
      color: #475569 !important;
      border-color: #CBD5E1 !important;
    }
    body.light-theme .btn-outline:hover {
      background: #F1F5F9 !important;
      color: #0F172A !important;
    }
    body.light-theme .tbl-card,
    body.light-theme .t-card {
      background: #FFFFFF !important;
      border-color: #E2E8F0 !important;
      color: #0F172A !important;
    }
    body.light-theme .tbl-no {
      color: #0F172A !important;
    }
    body.light-theme .tbl-seats {
      color: #64748B !important;
    }
    body.light-theme table,
    body.light-theme th,
    body.light-theme td {
      color: #0F172A !important;
      border-color: #E2E8F0 !important;
    }
    body.light-theme th {
      background: #F1F5F9 !important;
      color: #475569 !important;
    }
    body.light-theme tr:hover {
      background: #F8FAFC !important;
    }

    .tag-chip-btn {
      background: var(--primary-light);
      color: var(--primary);
      border: 1px solid var(--primary-light);
      padding: 6px 12px;
      border-radius: 12px;
      font-size: 11.5px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.18s ease;
    }
    .tag-chip-btn:hover {
      background: var(--primary);
      color: #ffffff;
      transform: translateY(-1px);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Outfit', 'Inter', sans-serif; background: var(--bg); color: var(--text-primary); display: flex; height: 100vh; overflow: hidden; }

    /* ─── SIDEBAR ─────────────────────────────── */
    .sidebar {
      width: 240px; min-width: 240px;
      background: var(--sidebar-bg);
      display: flex; flex-direction: column;
      overflow: hidden;
      transition: width .25s cubic-bezier(.4,0,.2,1), min-width .25s cubic-bezier(.4,0,.2,1);
      flex-shrink: 0;
    }
    .sidebar-nav {
      flex: 1; overflow-y: auto; overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: rgba(255,255,255,.15) transparent;
    }
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }
    .sidebar.collapsed { width: 64px; min-width: 64px; }

    .sidebar-brand {
      padding: 12px 10px;
      display: flex; align-items: center; gap: 10px;
      border-bottom: 1px solid rgba(255,255,255,.08);
      flex-shrink: 0; position: relative; min-height: 56px;
    }
    .brand-icon {
      width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
      background: var(--primary); display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: #fff;
    }
    .brand-text { overflow: hidden; white-space: nowrap; transition: opacity .2s, width .25s; }
    .brand-text h2 { font-size: 15px; font-weight: 700; color: #fff; letter-spacing: .3px; }
    .brand-text span { font-size: 11px; color: rgba(255,255,255,.45); }
    .sidebar.collapsed .brand-text { opacity: 0; width: 0; overflow: hidden; }
    .sidebar.collapsed .brand-icon { display: flex; margin: 0 auto; }

    /* Collapse toggle button */
    .sidebar-toggle {
      margin-left: auto; flex-shrink: 0;
      width: 28px; height: 28px; border-radius: 8px;
      background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.14);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar-toggle:hover {
      background: rgba(99,102,241,.35);
      border-color: #6366F1;
      transform: scale(1.05);
    }
    .sidebar-toggle i {
      font-size: 11px;
      color: rgba(255,255,255,.9);
      transition: transform .25s cubic-bezier(0.4, 0, 0.2, 1);
      display: inline-block;
    }
    .sidebar.collapsed .sidebar-brand {
      justify-content: center;
      padding: 10px 0 8px;
      flex-direction: column;
      gap: 6px;
      min-height: 72px;
    }
    .sidebar.collapsed .brand-icon {
      margin: 0 auto;
      cursor: pointer;
      transition: transform 0.18s ease;
    }
    .sidebar.collapsed .brand-icon:hover {
      transform: scale(1.06);
    }
    .sidebar.collapsed .sidebar-toggle {
      display: flex;
      margin: 0 auto;
      width: 34px;
      height: 22px;
      border-radius: 6px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.15);
    }
    .sidebar.collapsed .sidebar-toggle:hover {
      background: rgba(99,102,241,.3);
      border-color: #6366F1;
      color: #818CF8;
    }
    .sidebar.collapsed .sidebar-toggle i {
      transform: rotate(180deg);
    }
    body.light-theme .sidebar-toggle {
      background: #F1F5F9;
      border: 1px solid #CBD5E1;
    }
    body.light-theme .sidebar-toggle i {
      color: #475569;
    }
    body.light-theme .sidebar-toggle:hover {
      background: #EEF2FF;
      border-color: #6366F1;
      color: #4338CA;
    }
    body.light-theme .sidebar.collapsed .sidebar-toggle {
      background: #F1F5F9 !important;
      border: 1px solid #CBD5E1 !important;
    }
    body.light-theme .sidebar.collapsed .sidebar-toggle:hover {
      background: #EEF2FF !important;
      border-color: #6366F1 !important;
      color: #4338CA !important;
    }

    .sidebar-section { padding: 12px 8px 4px; overflow: hidden; }
    .sidebar-section-label {
      font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
      color: rgba(255,255,255,.3); padding: 0 6px; white-space: nowrap;
      transition: opacity .2s;
    }
    .sidebar.collapsed .sidebar-section-label { opacity: 0; }

    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 10px; border-radius: var(--radius-sm);
      color: rgba(255,255,255,.55); cursor: pointer;
      font-size: 13.5px; font-weight: 500;
      transition: all .18s; margin-top: 2px;
      text-decoration: none; white-space: nowrap; overflow: hidden;
      position: relative;
    }
    .nav-item:hover { background: var(--sidebar-hover); color: #fff; }
    .nav-item.active { background: var(--sidebar-active); color: #fff; box-shadow: 0 2px 8px rgba(79,70,229,.4); }
    .nav-item i { width: 20px; min-width: 20px; text-align: center; font-size: 14px; }
    .nav-item .nav-label { transition: opacity .2s; }
    .sidebar.collapsed .nav-item { justify-content: center; padding: 10px 0; }
    .sidebar.collapsed .nav-item i { font-size: 16px; margin: 0; }
    .sidebar.collapsed .nav-item .nav-label { opacity: 0; width: 0; display: none; }
    .nav-badge { margin-left: auto; background: var(--danger); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 20px; transition: opacity .2s; }
    .nav-badge.green { background: var(--success); }
    .sidebar.collapsed .nav-badge { opacity: 0; display: none; }

    /* Collapsed Sidebar Hover Tooltips */
    .sidebar.collapsed .nav-item:hover::after {
      content: attr(data-tip);
      position: absolute;
      left: calc(100% + 10px);
      top: 50%;
      transform: translateY(-50%);
      background: #1E293B;
      color: #F8FAFC;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      white-space: nowrap;
      z-index: 99999;
      box-shadow: 0 4px 16px rgba(0,0,0,0.4);
      border: 1px solid #334155;
      pointer-events: none;
    }
    body.light-theme .sidebar.collapsed .nav-item:hover::after {
      background: #FFFFFF;
      color: #0F172A;
      border-color: #E2E8F0;
      box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .sidebar-footer {
      flex-shrink: 0; padding: 10px 8px;
      border-top: 1px solid rgba(255,255,255,.07); overflow: hidden;
    }
    .user-card {
      display: flex; align-items: center; gap: 10px;
      padding: 8px 10px; border-radius: var(--radius-sm);
      cursor: pointer; transition: background .15s; white-space: nowrap;
    }
    .user-card:hover { background: var(--sidebar-hover); }
    .user-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0; }
    .user-info { transition: opacity .2s; }
    .user-info h4 { font-size: 13px; font-weight: 600; color: #fff; }
    .user-info p  { font-size: 11px; color: rgba(255,255,255,.4); }
    .sidebar.collapsed .user-info { opacity: 0; width: 0; overflow: hidden; display: none; }
    .sidebar.collapsed .sidebar-footer { padding: 10px 0; display: flex; justify-content: center; }
    .sidebar.collapsed .user-card { padding: 0; justify-content: center; }
    .sidebar.collapsed .user-card form { display: none; }

    /* ─── MAIN LAYOUT ─────────────────────────── */
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    /* ─── DINEFLOW STYLE TOPBAR ─── */
    .topbar {
      background: var(--card); border-bottom: 1.5px solid var(--border);
      padding: 0 16px; height: 56px; min-height: 56px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
      flex-shrink: 0; box-shadow: var(--shadow-sm); overflow: visible !important;
      position: relative; z-index: 50;
    }
    .topbar-title { font-size: 15px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.2px; }
    .topbar-sub   { font-size: 11px; color: var(--text-muted); }
    .topbar-btn {
      width: 36px; height: 36px; border-radius: 10px;
      background: var(--bg); border: 1.5px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--text-secondary); position: relative;
      transition: all .15s ease;
    }
    .topbar-btn:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); transform: translateY(-1px); }
    .notif-dot { position: absolute; top: 7px; right: 7px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%; border: 2px solid var(--card); }

    /* DineFlow Quick Action Control Buttons */
    .pos-quick-tool-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      height: 36px;
      padding: 0 12px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.18s ease;
      border: 1.5px solid transparent;
      user-select: none;
      white-space: nowrap;
      position: relative;
      background: var(--bg);
    }
    .pos-quick-tool-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .pos-quick-tool-btn.shift-btn {
      background: rgba(16, 185, 129, 0.12);
      color: #10B981;
      border-color: rgba(16, 185, 129, 0.35);
    }
    .pos-quick-tool-btn.shift-btn:hover {
      background: #10B981;
      color: #FFFFFF;
    }
    .pos-quick-tool-btn.online-btn {
      background: rgba(245, 158, 11, 0.12);
      color: #F59E0B;
      border-color: rgba(245, 158, 11, 0.35);
    }
    .pos-quick-tool-btn.online-btn:hover {
      background: #F59E0B;
      color: #FFFFFF;
    }
    .pos-quick-tool-btn.hold-btn {
      background: rgba(236, 72, 153, 0.12);
      color: #EC4899;
      border-color: rgba(236, 72, 153, 0.35);
    }
    .pos-quick-tool-btn.hold-btn:hover {
      background: #EC4899;
      color: #FFFFFF;
    }
    .pos-quick-tool-btn.tools-btn {
      background: rgba(99, 102, 241, 0.12);
      color: #818CF8;
      border-color: rgba(99, 102, 241, 0.35);
    }
    .pos-quick-tool-btn.tools-btn:hover {
      background: #6366F1;
      color: #FFFFFF;
    }
    .pos-quick-tool-btn.stock-btn {
      background: rgba(14, 165, 233, 0.12);
      color: #0EA5E9;
      border-color: rgba(14, 165, 233, 0.35);
    }
    .pos-quick-tool-btn.stock-btn:hover {
      background: #0EA5E9;
      color: #FFFFFF;
    }
    .pos-quick-tool-btn.orders-btn {
      background: rgba(99, 102, 241, 0.12);
      color: #818CF8;
      border-color: rgba(99, 102, 241, 0.35);
    }
    .pos-quick-tool-btn.orders-btn:hover {
      background: #6366F1;
      color: #FFFFFF;
    }
    .pos-quick-tool-btn.kds-btn {
      background: rgba(249, 115, 22, 0.12);
      color: #F97316;
      border-color: rgba(249, 115, 22, 0.35);
      text-decoration: none;
    }
    .pos-quick-tool-btn.kds-btn:hover {
      background: #F97316;
      color: #FFFFFF;
    }

    /* Dish Card Interactive Stepper & Add Button */
    .item-card-stepper {
      display: inline-flex;
      align-items: center;
      background: var(--bg);
      border: 1.5px solid #10B981;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(16,185,129,0.25);
    }
    .card-step-btn {
      width: 26px;
      height: 26px;
      border: none;
      background: #10B981;
      color: #fff;
      font-size: 14px;
      font-weight: 800;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.12s ease;
    }
    .card-step-btn:hover {
      background: #059669;
    }
    .card-step-qty {
      min-width: 24px;
      padding: 0 4px;
      text-align: center;
      font-size: 12.5px;
      font-weight: 800;
      color: var(--text-primary);
    }
    .item-add-btn-pill {
      height: 28px;
      padding: 0 10px;
      border-radius: 8px;
      border: none;
      background: linear-gradient(135deg, #6366F1, #4F46E5);
      color: #fff;
      font-size: 11.5px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      cursor: pointer;
      box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);
      transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .item-add-btn-pill:hover {
      background: linear-gradient(135deg, #4F46E5, #4338CA);
      transform: translateY(-1px);
      box-shadow: 0 5px 14px rgba(99, 102, 241, 0.45);
    }

    /* Operations Dropdown */
    .pos-dropdown-wrap {
      position: relative;
    }
    .pos-dropdown-menu {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      right: 0;
      min-width: 210px;
      background: var(--card);
      border: 1.5px solid var(--border);
      border-radius: 12px;
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
      z-index: 99999;
      padding: 6px;
      flex-direction: column;
      gap: 2px;
    }
    .pos-dropdown-menu.show {
      display: flex !important;
      animation: posDropFadeIn 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes posDropFadeIn {
      from { opacity: 0; transform: translateY(-6px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .pos-dropdown-item {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-radius: 8px;
      font-size: 12.5px;
      font-weight: 600;
      color: var(--text-primary);
      cursor: pointer;
      transition: all 0.15s ease;
      user-select: none;
    }
    .pos-dropdown-item:hover {
      background: var(--primary-light);
      color: var(--primary);
    }
    body.light-theme .pos-dropdown-item:hover {
      background: #EEF2FF;
      color: var(--primary);
    }

    /* DineFlow Standard Veg / Non-Veg Badges */
    .pos-diet-icon {
      width: 14px;
      height: 14px;
      border-radius: 3px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1.5px solid #10B981;
      background: rgba(16,185,129,0.08);
      flex-shrink: 0;
    }
    .pos-diet-icon.nonveg {
      border-color: #EF4444;
      background: rgba(239,68,68,0.08);
    }
    .pos-diet-icon .diet-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #10B981;
    }
    .pos-diet-icon.nonveg .diet-dot {
      background: #EF4444;
    }

    /* ─── POS WRAPPER ─────────────────────────── */
    .pos-wrapper {
      flex: 1; display: flex; overflow: hidden; min-width: 0;
    }

    /* ─── LEFT PANEL ──────────────────────────── */
    .pos-left {
      flex: 1; display: flex; flex-direction: column;
      overflow: hidden; background: var(--bg); min-width: 0;
    }

    /* Order Meta Bar */
    .order-meta {
      display: flex; gap: 10px; padding: 10px 16px;
      background: var(--card); border-bottom: 1.5px solid var(--border);
      flex-wrap: wrap; align-items: center;
    }
    .meta-group { display: flex; gap: 8px; align-items: center; }
    .meta-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }

    /* Delivery Type Tabs */
    .delivery-tabs { display: flex; background: var(--bg); border-radius: 10px; padding: 3px; border: 1.5px solid var(--border); height: 36px; }
    .delivery-tab {
      padding: 0 14px; border-radius: 7px; font-size: 12px; font-weight: 700;
      cursor: pointer; color: var(--text-secondary); transition: all .15s; display: flex; align-items: center; gap: 6px;
    }
    .delivery-tab.active { background: var(--primary); color: #fff; box-shadow: var(--shadow-sm); }
    .delivery-tab i { font-size: 11px; }

    /* Select Dropdowns */
    .meta-select {
      height: 36px; padding: 0 12px; border: 1.5px solid var(--border);
      border-radius: 10px; font-size: 12.5px; font-weight: 600; font-family: inherit;
      color: var(--text-primary); background: var(--card); outline: none; cursor: pointer;
      transition: all .15s ease; min-width: 30px;
    }
    .meta-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
    .meta-picker-btn {
      display: flex; align-items: center; justify-content: space-between; gap: 10px;
      text-align: left;
    }
    .meta-picker-btn:hover {
      border-color: var(--primary); color: var(--primary); background: var(--primary-light);
    }
    .meta-picker-btn i { color: var(--text-muted); transition: color .15s; }
    .meta-picker-btn:hover i { color: var(--primary); }
    .meta-picker-inline {
      display: flex; align-items: center; gap: 7px; min-width: 0;
    }
    .meta-picker-value {
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 700;
    }

    /* Search + Category */
    .menu-controls {
      padding: 12px 16px 8px;
      display: flex; gap: 10px; align-items: center;
      background: var(--bg); flex-wrap: wrap;
    }
    .search-box {
      flex: 1; min-width: 200px; position: relative; display: flex; align-items: center;
    }
    .search-box > i.fa-search {
      position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; pointer-events: none;
    }
    .search-input {
      width: 100%; height: 36px; padding: 0 40px 0 34px;
      border: 1px solid var(--border); border-radius: var(--radius-sm);
      font-size: 13px; font-family: inherit; background: var(--card); outline: none;
      transition: border-color .15s;
    }
    .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,.08); }
    #posVoiceBtn {
      position: absolute; right: 5px; top: 50%; transform: translateY(-50%);
      background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3);
      color: var(--primary); width: 28px; height: 28px; border-radius: 6px;
      cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center;
      transition: all 0.2s; z-index: 3;
    }
    #posVoiceBtn i {
      position: static !important; transform: none !important; font-size: 12px !important; color: inherit !important;
    }

    /* Category Pills */
    .cat-pills { display: flex; gap: 6px; flex-wrap: nowrap; overflow-x: auto; padding: 0 16px 10px; scrollbar-width: none; }
    .cat-pills::-webkit-scrollbar { display: none; }
    .cat-pill {
      padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
      white-space: nowrap; cursor: pointer; border: 1.5px solid var(--border);
      color: var(--text-secondary); background: var(--card); transition: all .15s;
      display: flex; align-items: center; gap: 5px;
    }
    .cat-pill:hover  { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .cat-pill.active { border-color: var(--primary); color: #fff; background: var(--primary); }
    .cat-pill .dot   { width: 7px; height: 7px; border-radius: 50%; background: currentColor; opacity: .7; }

    /* Menu Grid - Hardware Accelerated & Virtualized */
    .menu-grid {
      flex: 1; overflow-y: auto; padding: 12px;
      display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      grid-auto-rows: minmax(200px, auto);
      gap: 12px;
      align-content: start;
      contain: layout style;
      transform: translateZ(0);
      -webkit-overflow-scrolling: touch;
    }
    .menu-grid::-webkit-scrollbar { width: 6px; }
    .menu-grid::-webkit-scrollbar-track { background: transparent; }
    .menu-grid::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    .item-card {
      background: #1E293B;
      border-radius: 14px;
      border: 1.5px solid rgba(255, 255, 255, 0.08);
      cursor: pointer;
      transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
      overflow: hidden;
      box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.25);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      min-height: 200px;
      height: 100%;
      content-visibility: auto;
      contain-intrinsic-size: 200px 150px;
      transform: translateZ(0);
      will-change: transform;
    }
    .item-card:hover {
      border-color: rgba(99, 102, 241, 0.7);
      box-shadow: 0 8px 24px -2px rgba(99, 102, 241, 0.35);
      transform: translateY(-3px);
    }
    .item-card.in-cart {
      border-color: #10B981;
      background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(15, 23, 42, 0.85));
      box-shadow: 0 6px 20px -2px rgba(16, 185, 129, 0.3);
    }

    .item-img {
      height: 84px;
      background: radial-gradient(circle at center, rgba(99, 102, 241, 0.15) 0%, rgba(15, 23, 42, 0.95) 100%);
      display: flex; align-items: center; justify-content: center;
      font-size: 34px; position: relative; overflow: hidden;
      transition: transform 0.2s ease;
      flex-shrink: 0;
    }
    .item-card:hover .item-img {
      transform: scale(1.04);
    }

    .item-badge {
      position: absolute; top: 6px; right: 6px;
      font-size: 8.5px; font-weight: 800; padding: 2px 7px;
      border-radius: 20px; letter-spacing: .5px;
      text-transform: uppercase;
    }
    .item-badge.veg   { background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.45); box-shadow: 0 0 12px rgba(16, 185, 129, 0.25); }
    .item-badge.nonveg{ background: rgba(239, 68, 68, 0.2);  color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.45);  box-shadow: 0 0 12px rgba(239, 68, 68, 0.25); }
    .item-badge.new   { background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.45); box-shadow: 0 0 12px rgba(245, 158, 11, 0.25); }
    .item-badge.best  { background: rgba(99, 102, 241, 0.2); color: #A5B4FC; border: 1px solid rgba(99, 102, 241, 0.45); box-shadow: 0 0 12px rgba(99, 102, 241, 0.25); }

    .item-body { padding: 10px 12px 14px; display: flex; flex-direction: column; flex: 1; }
    .item-name {
      font-size: 12.5px; font-weight: 700; color: #F8FAFC; line-height: 1.3; margin-bottom: 6px;
      word-break: break-word; overflow-wrap: break-word;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
      overflow: hidden; height: 2.6em;
    }
    .item-footer {
      display: flex; align-items: center; justify-content: space-between; gap: 8px; width: 100%; margin-top: auto; padding-top: 4px; padding-bottom: 2px;
    }
    .item-price { font-size: 13.5px; font-weight: 800; color: #818CF8; white-space: nowrap; }
    .item-price small { font-size: 9px; font-weight: 500; color: var(--text-muted); }
    .item-footer-actions { display: flex; align-items: center; justify-content: flex-end; flex-shrink: 0; }
    .item-add-btn {
      width: 28px; height: 28px; border-radius: 8px; border: none;
      background: linear-gradient(135deg, #6366F1, #4F46E5); color: #fff; font-size: 15px; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all .2s cubic-bezier(0.4, 0, 0.2, 1); flex-shrink: 0;
      box-shadow: 0 3px 10px rgba(99, 102, 241, 0.35);
    }
    .item-add-btn:hover { background: linear-gradient(135deg, #4F46E5, #4338CA); transform: scale(1.1); box-shadow: 0 5px 14px rgba(99, 102, 241, 0.45); }
    .item-in-cart-badge {
      width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
      background: linear-gradient(135deg, #10B981, #059669); color: #fff; font-size: 11.5px; font-weight: 800;
      display: flex; align-items: center; justify-content: center;
      box-shadow: 0 3px 10px rgba(16, 185, 129, 0.35);
    }

    /* POS Menu Skeleton Loader */
    .pos-skeleton-card {
      background: rgba(30, 41, 59, 0.55);
      border-radius: var(--radius-lg);
      border: 1px solid rgba(255, 255, 255, 0.06);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 165px;
      position: relative;
    }
    .pos-skeleton-img {
      height: 84px;
      background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%);
      background-size: 200% 100%;
      animation: posShimmer 1.5s infinite;
    }
    .pos-skeleton-body {
      padding: 10px 12px;
      display: flex;
      flex-direction: column;
      gap: 7px;
      flex: 1;
    }
    .pos-skeleton-line {
      height: 11px;
      border-radius: 6px;
      background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.03) 75%);
      background-size: 200% 100%;
      animation: posShimmer 1.5s infinite;
    }
    .pos-skeleton-line.short { width: 45%; }
    .pos-skeleton-line.price { width: 35%; height: 15px; margin-top: auto; }

    @keyframes posShimmer {
      0% { background-position: -200% 0; }
      100% { background-position: 200% 0; }
    }

    /* POS Menu Empty & Error States */
    .pos-empty-state, .pos-error-state {
      grid-column: 1 / -1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 48px 20px;
      text-align: center;
      background: rgba(30, 41, 59, 0.35);
      border: 1.5px dashed var(--border);
      border-radius: 20px;
      margin: 16px 0;
    }
    .pos-empty-icon {
      width: 60px;
      height: 60px;
      border-radius: 18px;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      margin-bottom: 14px;
    }
    .pos-empty-title {
      font-family: 'Outfit', sans-serif;
      font-size: 17px;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 6px;
    }
    .pos-empty-sub {
      font-size: 13px;
      color: var(--text-muted);
      max-width: 360px;
      margin-bottom: 16px;
      line-height: 1.4;
    }
    .pos-empty-action {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 9px 18px;
      border-radius: 12px;
      background: linear-gradient(135deg, #6366F1, #4F46E5);
      color: #fff;
      font-size: 13px;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
      border: none;
      box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
      transition: all 0.2s;
    }
    .pos-empty-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(99, 102, 241, 0.5);
    }
    .item-category-tag {
      font-size: 9.5px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .item-card-unavailable {
      opacity: 0.65;
      filter: grayscale(0.4);
    }
    .item-soldout-overlay {
      position: absolute;
      inset: 0;
      background: rgba(15, 23, 42, 0.78);
      color: #EF4444;
      font-size: 11px;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      letter-spacing: 0.8px;
    }

    /* ─── RIGHT PANEL (CART) ──────────────────── */
    .pos-right {
      width: 360px; min-width: 330px;
      background: var(--card); border-left: 1.5px solid var(--border);
      display: flex; flex-direction: column; height: 100%; max-height: 100%;
      overflow: hidden; flex-shrink: 0; position: relative;
    }
    .cart-scroll-body {
      flex: 1; min-height: 0;
      overflow-y: auto; overflow-x: hidden;
      scrollbar-width: thin;
      -webkit-overflow-scrolling: touch;
    }
    .cart-scroll-body::-webkit-scrollbar { width: 4px; }
    .cart-scroll-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
    .cart-bottom-fixed {
      flex-shrink: 0;
      margin-top: auto;
      border-top: 1.5px solid var(--border);
      background: var(--card);
    }

    .cart-header {
      padding: 10px 14px 8px;
      background: var(--bg);
      border-bottom: 1.5px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      flex-shrink: 0;
    }
    .cart-title { font-size: 14.5px; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
    .cart-count { background: var(--primary); color: #fff; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 20px; }
    .cart-order-no { font-size: 10.5px; color: var(--text-muted); font-weight: 600; margin-top: 2px; }

    /* Customer Strip */
    .customer-strip {
      padding: 6px 14px;
      background: var(--card); border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 8px;
      flex-shrink: 0;
    }
    .cust-avatar { width: 30px; height: 30px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
    .cust-info h5 { font-size: 12px; font-weight: 700; color: var(--text-primary); }
    .cust-info p  { font-size: 10.5px; color: var(--text-muted); }
    .cust-change { margin-left: auto; font-size: 11px; color: var(--primary); font-weight: 700; cursor: pointer; }

    /* Cart Items */
    .cart-items { flex: none; overflow: visible; padding: 4px 0; }

    .cart-item {
      padding: 10px 14px; border-bottom: 1px solid var(--border);
      display: flex; align-items: flex-start;
      transition: background 0.15s ease;
    }
    .cart-item:hover { background: rgba(255,255,255,0.02); }
    .ci-info { flex: 1; min-width: 0; }
    .ci-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
    }
    .ci-main { flex: 1; min-width: 0; }
    .ci-name { font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px; line-height: 1.3; }
    .ci-variant {
      font-size: 10.5px; color: var(--text-muted); margin-bottom: 4px;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .ci-note { font-size: 10.5px; color: var(--text-muted); font-style: italic; }
    .ci-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-top: 4px;
    }
    .ci-controls { display: flex; align-items: center; gap: 2px; }
    .ci-btn {
      width: 28px; height: 28px; border: 1.5px solid var(--border); background: var(--bg);
      border-radius: 8px; display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 14px; font-weight: 700; color: var(--text-primary); transition: all .12s;
    }
    .ci-btn:hover { border-color: var(--primary); color: #fff; background: var(--primary); }
    .ci-qty { width: 30px; text-align: center; font-size: 13.5px; font-weight: 800; color: var(--text-primary); }
    .ci-price { font-size: 13.5px; font-weight: 800; color: var(--text-primary); flex-shrink: 0; text-align: right; white-space: nowrap; }
    .ci-del { width: 28px; height: 28px; border: none; background: none; color: var(--text-muted); cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; transition: all .12s; border-radius: 6px; }
    .ci-del:hover { color: var(--danger); background: var(--danger-light); }

    /* Cart Note */
    .cart-note-area {
      padding: 6px 14px;
      border-top: 1px solid var(--border);
    }
    .cart-note-input {
      width: 100%; height: 34px; border: 1px solid var(--border); border-radius: var(--radius-sm);
      padding: 0 10px; font-size: 12px; font-family: inherit; outline: none; color: var(--text-primary);
      transition: border-color .15s; resize: none; background: var(--bg);
    }
    .cart-note-input::placeholder { color: var(--text-muted); }
    .cart-note-input:focus { border-color: var(--primary); }

    /* Totals */
    .cart-totals {
      padding: 6px 12px 3px;
      background: rgba(0,0,0,0.03);
    }
    .tot-row { display: flex; justify-content: space-between; font-size: 11.5px; margin-bottom: 2px; }
    .tot-row .label { color: var(--text-secondary); font-weight: 500; }
    .tot-row .val   { font-weight: 700; color: var(--text-primary); }
    .tot-row.discount .val { color: var(--danger); font-weight: 800; }
    .tot-divider { border: none; border-top: 1px dashed var(--border); margin: 3px 0; }
    .tot-row.grand {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: var(--bg);
      padding: 5px 10px;
      border-radius: 8px;
      border: 1.5px solid var(--border);
      margin-top: 3px;
      margin-bottom: 2px;
    }
    .tot-row.grand .label { font-size: 12px; font-weight: 800; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.5px; }
    .tot-row.grand .val   { font-size: 16px; font-weight: 900; color: #10B981; }

    /* Coupon */
    .coupon-row {
      padding: 6px 14px;
      display: flex; gap: 8px;
    }
    .coupon-input {
      flex: 1; height: 32px; padding: 0 10px; border: 1px dashed var(--primary);
      border-radius: var(--radius-sm); font-size: 12px; font-family: inherit;
      outline: none; color: var(--text-primary); background: var(--primary-light);
    }
    .coupon-btn {
      height: 32px; padding: 0 12px; border: none; border-radius: var(--radius-sm);
      background: var(--primary); color: #fff; font-size: 12px; font-weight: 700;
      cursor: pointer; transition: background .15s;
    }
    .coupon-btn:hover { background: var(--primary-dark); }

    /* Payment */
    .payment-section {
      padding: 5px 12px;
      border-top: 1px solid var(--border);
    }
    .payment-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--text-muted); margin-bottom: 4px; }
    .pay-methods { display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; }
    .pay-method {
      padding: 4px 2px;
      border: 1.5px solid var(--border); border-radius: 8px;
      text-align: center; cursor: pointer; transition: all .15s ease;
      background: var(--bg);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      height: 32px;
    }
    .pay-method i { font-size: 13px; color: var(--text-muted); display: block; margin-bottom: 1px; }
    .pay-method span { font-size: 10px; font-weight: 700; color: var(--text-muted); display: block; }
    .pay-method:hover { border-color: var(--primary); transform: translateY(-1px); }
    .pay-method.active { border-color: #10B981; background: rgba(16,185,129,0.12); }
    .pay-method.active i, .pay-method.active span { color: #10B981; font-weight: 800; }

    /* Split Payment */
    .split-row {
      display: flex; gap: 8px; margin-top: 8px; align-items: center;
    }
    .split-input {
      flex: 1; height: 30px; padding: 0 8px; border: 1px solid var(--border);
      border-radius: var(--radius-sm); font-size: 12px; font-family: inherit; outline: none;
    }
    .split-input:focus { border-color: var(--primary); }

    /* Action Buttons */
    .cart-actions {
      padding: 10px 14px 12px;
      display: flex; gap: 8px; flex-direction: column;
    }
    .btn-checkout {
      width: 100%; height: 44px; border: none; border-radius: var(--radius-md);
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff; font-size: 14px; font-weight: 700; cursor: pointer;
      transition: all .18s; display: flex; align-items: center; justify-content: center; gap: 8px;
      box-shadow: 0 4px 12px rgba(79,70,229,.3);
    }
    .btn-checkout:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(79,70,229,.35); }
    .cart-btns-row { display: flex; gap: 8px; }
    .btn-outline {
      flex: 1; height: 34px; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      background: var(--card); color: var(--text-secondary); font-size: 12px; font-weight: 600;
      cursor: pointer; transition: all .15s; display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .btn-outline.danger:hover { border-color: var(--danger); color: var(--danger); background: var(--danger-light); }

    /* ─── MODALS ──────────────────────────────── */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(15,23,42,.45);
      display: flex; align-items: center; justify-content: center;
      z-index: 1000; backdrop-filter: blur(3px); opacity: 0; pointer-events: none; transition: opacity .2s;
    }
    .modal-overlay.show { opacity: 1; pointer-events: all; }
    .modal {
      background: var(--card); border-radius: var(--radius-lg); padding: 0;
      width: 460px; max-width: calc(100vw - 24px); max-height: 90vh; overflow-y: auto;
      box-shadow: 0 24px 48px rgba(0,0,0,.18); transform: scale(.95); transition: transform .2s;
    }
    .modal-overlay.show .modal { transform: scale(1); }
    .modal-header {
      padding: 18px 22px 14px; border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title { font-size: 16px; font-weight: 700; }
    .modal-close { width: 30px; height: 30px; border: none; background: var(--bg); border-radius: 6px; cursor: pointer; color: var(--text-secondary); font-size: 15px; display: flex; align-items: center; justify-content: center; transition: all .12s; }
    .modal-close:hover { background: var(--danger-light); color: var(--danger); }
    .modal-body { padding: 18px 22px; }
    .modal-footer { padding: 14px 22px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end; }

    .selection-popup-modal {
      max-height: min(88vh, 720px);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .selection-popup-modal .modal-body {
      flex: 1;
      min-height: 0;
      overflow-y: auto;
    }
    .table-popup-modal { width: 420px; max-width: 95vw; }
    .meta-popup-modal  { width: 360px; max-width: 95vw; }

    /* Table Modal */
    .table-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(75px, 1fr)); gap: 8px; }
    .table-cell {
      aspect-ratio: 1; border: 2px solid var(--border); border-radius: var(--radius-sm);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      cursor: pointer; transition: all .15s; font-size: 14px; font-weight: 700; color: var(--text-secondary);
    }
    .table-cell span { font-size: 10px; font-weight: 500; margin-top: 2px; }
    .table-cell.available:hover { border-color: var(--success); color: var(--success); background: var(--success-light); }
    .table-cell.occupied { background: #FEF2F2; border-color: var(--danger); color: var(--danger); }
    .table-cell.occupied span { color: var(--danger); }
    .table-cell.reserved { background: var(--warning-light); border-color: var(--warning); color: var(--warning); }
    .table-cell.selected { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }
    .table-legend { display: flex; gap: 14px; margin-top: 14px; }
    .tl-item { display: flex; align-items: center; gap: 5px; font-size: 11.5px; color: var(--text-secondary); }
    .tl-dot  { width: 10px; height: 10px; border-radius: 3px; }

    /* Customer Modal & Search */
    .cust-search {
      width: 100%;
      background: #1E293B;
      border: 1px solid #334155;
      color: #F8FAFC;
      padding: 10px 14px;
      border-radius: 10px;
      font-size: 13px;
      margin-bottom: 14px;
      outline: none;
      font-family: inherit;
    }
    .cust-search:focus {
      border-color: var(--primary);
    }
    .cust-list {
      max-height: 320px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .cust-row {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 12px;
      border-radius: 12px;
      background: rgba(255,255,255,0.03);
      border: 1px solid rgba(255,255,255,0.06);
      cursor: pointer;
      transition: all 0.2s;
    }
    .cust-row:hover, .cust-row.selected {
      background: rgba(99,102,241,0.15);
      border-color: rgba(99,102,241,0.4);
    }
    .cr-avatar {
      width: 40px !important;
      height: 40px !important;
      min-width: 40px !important;
      border-radius: 10px !important;
      background: linear-gradient(135deg, #4F46E5, #6366F1);
      color: #FFFFFF !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-weight: 800 !important;
      font-size: 13px !important;
      flex-shrink: 0 !important;
    }
    .cr-info {
      flex: 1;
      min-width: 0;
    }
    .cr-info h5 {
      font-size: 13.5px;
      font-weight: 700;
      color: #F8FAFC;
      margin: 0 0 2px 0;
    }
    .cr-info p {
      font-size: 11.5px;
      color: #94A3B8;
      margin: 0;
    }
    .cr-tag {
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      margin-left: auto;
      flex-shrink: 0;
    }
    .cr-tag.loyal { background: rgba(16,185,129,0.15); color: #34D399; border: 1px solid rgba(16,185,129,0.3); }
    .cr-tag.vip   { background: rgba(236,72,153,0.15); color: #F472B6; border: 1px solid rgba(236,72,153,0.3); }
    .cr-tag.new   { background: rgba(6,182,212,0.15);  color: #22D3EE; border: 1px solid rgba(6,182,212,0.3); }

    /* Meta Popup */
    .meta-option-list { display: flex; flex-direction: column; gap: 8px; }
    .meta-option-btn {
      width: 100%; padding: 11px 14px;
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      background: var(--card); color: var(--text-primary);
      font-size: 13px; font-weight: 600; font-family: inherit;
      display: flex; align-items: center; justify-content: space-between;
      cursor: pointer; transition: all .15s; text-align: left;
    }
    .meta-option-btn:hover {
      border-color: var(--primary); color: var(--primary); background: var(--primary-light);
    }
    .meta-option-btn.active {
      border-color: var(--primary); color: var(--primary); background: var(--primary-light);
    }
    .meta-option-check { opacity: 0; transition: opacity .15s; }
    .meta-option-btn.active .meta-option-check { opacity: 1; }

    /* KOT Modal */
    .kot-item-row { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px dashed var(--border); font-size: 13px; }
    .kot-item-row:last-child { border-bottom: none; }

    /* Toast */
    .toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
    .toast {
      background: var(--text-primary); color: #fff; padding: 10px 16px;
      border-radius: var(--radius-sm); font-size: 13px; font-weight: 500;
      display: flex; align-items: center; gap: 8px; box-shadow: var(--shadow-lg);
      animation: slideIn .25s ease; min-width: 240px;
    }
    .toast.success { background: var(--success); }
    .toast.error   { background: var(--danger); }
    @keyframes slideIn { from { transform: translateX(60px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

    /* Scrollbar global */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }

    /* ─── CATEGORY POPUP BUTTON ──────────────── */
    .cat-popup-wrap { position: relative; }
    .cat-popup-btn {
      height: 36px; padding: 0 14px; border: 1px solid var(--border);
      border-radius: var(--radius-sm); background: var(--card);
      color: var(--text-secondary); font-size: 13px; font-weight: 600;
      cursor: pointer; display: flex; align-items: center; gap: 7px;
      transition: all .15s; font-family: inherit; white-space: nowrap;
    }
    .cat-popup-btn:hover, .cat-popup-btn.open {
      border-color: var(--primary); color: var(--primary); background: var(--primary-light);
    }
    .cat-popup-btn .cat-active-dot {
      width: 8px; height: 8px; border-radius: 50%; background: var(--primary); display: none;
    }
    .cat-popup-btn.filtered .cat-active-dot { display: block; }

    .cat-popup-modal {
      width: min(560px, calc(100vw - 24px));
      max-height: min(88vh, 720px);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .cat-popup-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .cat-popup-clear {
      font-size: 11px;
      color: var(--primary);
      font-weight: 600;
      cursor: pointer;
      transition: color .15s;
    }
    .cat-popup-clear:hover { color: var(--primary-dark); }
    .cat-popup-modal .modal-body {
      flex: 1;
      min-height: 0;
      padding-top: 16px;
      overflow-y: auto;
    }

    .cat-popup-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 10px;
    }
    .cat-popup-item {
      display: flex; align-items: center; gap: 10px; padding: 12px 14px;
      border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);
      cursor: pointer; transition: all 0.2s;
    }
    .cat-popup-item:hover {
      border-color: var(--primary); background: var(--primary-light);
    }
    .cat-popup-item.active {
      background: rgba(99,102,241,0.2); border-color: rgba(99,102,241,0.4);
    }
    .cpi-name {
      font-size: 13.5px; font-weight: 700; color: var(--text-primary);
    }
    .cpi-count {
      font-size: 11px; color: var(--text-muted); margin-top: 2px;
    }

    body.light-theme .cat-popup-item {
      background: #F8FAFC !important;
      border-color: #E2E8F0 !important;
    }
    body.light-theme .cat-popup-item:hover {
      background: #EEF2FF !important;
      border-color: #C7D2FE !important;
    }
    body.light-theme .cat-popup-item.active {
      background: #E0E7FF !important;
      border-color: #818CF8 !important;
    }
    body.light-theme .cpi-name {
      color: #0F172A !important;
    }
    body.light-theme .cpi-count {
      color: #64748B !important;
    }

    /* ─── RESPONSIVE & TOPBAR COMPACT LAYOUT ─────────────────────────── */
    .pos-quick-tool-btn {
      white-space: nowrap;
      flex-shrink: 0;
    }

    @media (max-width: 1600px) {
      .pos-quick-tool-btn {
        padding: 0 8px;
        gap: 5px;
        font-size: 11px;
        height: 32px;
      }
      .topbar {
        padding: 0 10px;
        gap: 6px;
      }
    }

    @media (max-width: 1400px) {
      /* Hide text labels on standard topbar action buttons to save space */
      .pos-quick-tool-btn.shift-btn span,
      .pos-quick-tool-btn.online-btn span:not(#onlinePendingBadge),
      .pos-quick-tool-btn.hold-btn span:not(#holdCartsCountBadge),
      .pos-quick-tool-btn.stock-btn span:not(#topbarLowStockBadge),
      .pos-quick-tool-btn.orders-btn span,
      .pos-quick-tool-btn.kds-btn span {
        display: none !important;
      }
      .pos-quick-tool-btn {
        width: 34px !important;
        height: 34px !important;
        padding: 0 !important;
        justify-content: center !important;
        border-radius: 8px !important;
      }
      .pos-quick-tool-btn.tools-btn {
        width: auto !important;
        padding: 0 8px !important;
      }
      .pos-quick-tool-btn.tools-btn span,
      .pos-quick-tool-btn.tools-btn i.fa-chevron-down {
        display: inline-block !important;
      }
      #posNetworkStatusText {
        display: none !important;
      }
      #posNetworkStatusBadge {
        width: 34px !important;
        height: 34px !important;
        padding: 0 !important;
        justify-content: center !important;
      }
    }

    @media (max-width: 1100px) {
      .pos-right { width: 300px; min-width: 300px; }
    }

    /* ─── MOBILE CART PANEL ───────────────────── */
    @media (max-width: 768px) {
      .pos-left {
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch;
      }
      .pos-right {
        position: fixed;
        top: 0; right: 0; bottom: 0;
        width: 320px; min-width: 0;
        z-index: 300;
        transform: translateX(100%);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
        box-shadow: -4px 0 24px rgba(0,0,0,.15);
      }
      .pos-right.cart-open {
        transform: translateX(0);
      }

      /* Mobile topbar styles */
      .topbar {
        position: sticky;
        top: 0;
        z-index: 250;
        padding: 0 12px;
        gap: 10px;
      }
      .topbar-title-group { display: none; }
      .topbar-btn {
        width: 34px;
        height: 34px;
      }

      /* Backdrop */
      .cart-backdrop {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.35);
        z-index: 299;
      }
      .cart-backdrop.active { display: block; }

      /* Mobile sidebar */
      .sidebar {
        display: flex !important;
        position: fixed;
        top: 0; left: 0; bottom: 0;
        width: 260px !important;
        min-width: 260px !important;
        z-index: 310;
        transform: translateX(-100%);
        transition: transform .28s cubic-bezier(.4,0,.2,1);
        box-shadow: 10px 0 30px rgba(15,23,42,.22);
      }
      .sidebar.mobile-open {
        transform: translateX(0);
      }
      .sidebar.collapsed {
        width: 260px !important;
        min-width: 260px !important;
      }
      .sidebar.collapsed .brand-text,
      .sidebar.collapsed .user-info,
      .sidebar.collapsed .nav-item .nav-label,
      .sidebar.collapsed .nav-badge,
      .sidebar.collapsed .sidebar-section-label {
        opacity: 1;
        width: auto;
      }
      .sidebar.collapsed .brand-icon {
        display: flex;
      }
      .sidebar.collapsed .sidebar-brand {
        justify-content: flex-start;
      }
      .sidebar.collapsed .sidebar-toggle {
        margin-left: auto;
      }
      .sidebar.collapsed .nav-item:hover::after {
        display: none;
      }

      .order-meta {
        padding: 10px 12px;
        gap: 8px;
        align-items: stretch;
      }
      .order-meta .meta-group {
        width: 100%;
        min-width: 0;
      }
      .delivery-tabs {
        width: 100%;
        overflow-x: auto;
      }
      .delivery-tab {
        flex: 1;
        justify-content: center;
        white-space: nowrap;
      }
      .meta-group .meta-select {
        width: 100%;
        min-width: 0;
      }
      .menu-controls {
        padding: 10px 12px 8px;
        gap: 8px;
      }
      .search-box {
        min-width: 0;
        width: 100%;
        flex: 1 1 100%;
      }
      .cat-popup-wrap {
        width: 100%;
      }
      .cat-popup-btn {
        width: 100%;
        justify-content: space-between;
      }

      /* Item grid — 2 columns across full screen width */
      .menu-grid {
        flex: none;
        overflow: visible;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        padding: 8px 12px 92px;
        gap: 10px;
      }

      /* Card layout */
      .item-card {
        display: flex;
        flex-direction: column;
        min-width: 0;
        min-height: 210px;
        border-radius: 14px;
      }
      .item-img {
        height: 112px;
        min-height: 112px;
        aspect-ratio: auto;
        font-size: 34px;
      }

      .item-body {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 8px 10px 9px;
        min-width: 0;
        min-height: 88px;
        flex: 1;
      }

      .item-name {
        font-size: 12.5px;
        line-height: 1.3;
        margin-bottom: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 30px;
      }

      .item-footer {
        gap: 6px;
        margin-top: 0;
      }

      .item-price { font-size: 13px; }
      .item-price small { font-size: 9px; }
      .item-badge {
        top: 7px;
        right: 7px;
        font-size: 8px;
        padding: 2px 5px;
      }

      .item-add-btn,
      .item-in-cart-badge {
        width: 30px; height: 30px;
        font-size: 15px; border-radius: 8px;
      }

      .cat-popup-modal,
      .table-popup-modal,
      .meta-popup-modal {
        width: calc(100vw - 20px) !important;
      }

      .cart-header,
      .customer-strip,
      .cart-note-area,
      .coupon-row,
      .cart-totals,
      .payment-section,
      .cart-actions {
        padding-left: 12px;
        padding-right: 12px;
      }
      .cart-item {
        padding-left: 12px;
        padding-right: 12px;
      }
    }

    @media (max-width: 420px) {
      .topbar {
        padding: 0 10px;
      }
      .order-meta,
      .menu-controls {
        padding-left: 10px;
        padding-right: 10px;
      }
      .menu-grid {
        padding-left: 10px;
        padding-right: 10px;
        gap: 8px;
      }
      .item-card {
        border-radius: 12px;
        min-height: 194px;
      }
      .item-img {
        height: 100px;
        min-height: 100px;
        font-size: 30px;
      }
      .item-body {
        gap: 4px;
        padding: 7px 9px 8px;
        min-height: 80px;
      }
      .item-name {
        font-size: 12px;
        min-height: 28px;
      }
      .item-price {
        font-size: 12px;
      }
      .item-price small {
        font-size: 8.5px;
      }
      .item-add-btn,
      .item-in-cart-badge {
        width: 28px;
        height: 28px;
        font-size: 14px;
      }
    }

    /* Topbar cart badge */
    .topbar-cart-badge {
      position: absolute; top: 4px; right: 4px;
      width: 16px; height: 16px; border-radius: 50%;
      background: var(--danger); color: #fff;
      font-size: 9px; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      border: 2px solid var(--card);
    }
    .sidebar-backdrop {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,.35);
      z-index: 309;
    }
    .sidebar-backdrop.active { display: block; }

    /* Hide close btn on larger screens */
    @media (min-width: 769px) {
      .cart-backdrop        { display: none !important; }
      .sidebar-backdrop     { display: none !important; }
      .mobile-close-cart    { display: none !important; }
      .topbar-mobile-cart   { display: none !important; }
      .topbar-mobile-menu   { display: none !important; }
    }
    @media (max-width: 768px) {
      .mobile-close-cart    { display: flex !important; }
      .topbar-desktop-only  { display: none !important; }
      .topbar-mobile-cart   { display: flex !important; }
      .topbar-mobile-menu   { display: flex !important; }
    }

    /* ═════════════════════════════════════════════════════════════
       POS MOBILE RESPONSIVE ENGINE (320px to 768px)
       ═════════════════════════════════════════════════════════════ */
    @media (max-width: 768px) {
      /* Main Container & Layout */
      body {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        width: 100% !important;
        height: 100% !important;
        min-height: 100vh !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-y !important;
      }
      .pos-wrapper {
        flex-direction: column !important;
        width: 100% !important;
        overflow-x: hidden !important;
        overflow-y: visible !important;
        min-height: 100% !important;
      }
      .pos-left {
        width: 100% !important;
        flex: 1 !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-y !important;
        padding-bottom: 90px !important;
      }

      /* Search Bar Full Width */
      .search-box, .search-wrapper, .menu-search-input {
        width: 100% !important;
        box-sizing: border-box !important;
      }
      .search-box input {
        font-size: 14px !important;
        padding: 10px 14px !important;
      }

      /* Category Bar Touch Horizontal Scroll */
      .cat-bar, .category-bar-scroll, .categories-container {
        display: flex !important;
        overflow-x: auto !important;
        white-space: nowrap !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-x !important;
        scrollbar-width: none !important;
        gap: 8px !important;
        padding: 6px 4px !important;
        width: 100% !important;
      }
      .cat-bar::-webkit-scrollbar, .category-bar-scroll::-webkit-scrollbar {
        display: none !important;
      }
      .cat-btn, .category-tab-btn {
        flex-shrink: 0 !important;
        min-height: 44px !important;
        padding: 0 16px !important;
        border-radius: 12px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
      }

      /* Menu Grid - 2 Columns on Most Phones (360px - 480px) */
      .menu-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 10px !important;
        padding: 10px !important;
        width: 100% !important;
        box-sizing: border-box !important;
        touch-action: pan-y !important;
      }

      /* Card equal height & image scaling */
      .item-card {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        height: 100% !important;
        min-height: 200px !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        background: var(--card) !important;
        border: 1px solid var(--border) !important;
      }
      .item-img {
        width: 100% !important;
        height: 105px !important;
        min-height: 105px !important;
        object-fit: cover !important;
        border-radius: 14px 14px 0 0 !important;
      }
      .item-body {
        padding: 8px 10px !important;
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
      }
      .item-name {
        font-size: 12.5px !important;
        font-weight: 700 !important;
        line-height: 1.3 !important;
        word-break: break-word !important;
        margin-bottom: 4px !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
      }
      .item-footer {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        margin-top: 6px !important;
      }
      .item-price {
        font-size: 13px !important;
        font-weight: 800 !important;
      }

      /* Touch-Friendly Action Buttons (>= 44px) */
      .topbar-btn, .btn-touch-action, .item-add-btn {
        min-height: 44px !important;
        min-width: 44px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 12px !important;
      }

      /* Mobile Slide-In Cart Drawer */
      .pos-right {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 88vw !important;
        max-width: 380px !important;
        z-index: 400 !important;
        transform: translateX(100%) !important;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: -10px 0 35px rgba(0, 0, 0, 0.6) !important;
        background: var(--card) !important;
        display: flex !important;
        flex-direction: column !important;
      }
      .pos-right.cart-open {
        transform: translateX(0) !important;
      }
      .cart-backdrop {
        display: none !important;
        position: fixed !important;
        inset: 0 !important;
        background: rgba(15, 23, 42, 0.8) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 395 !important;
        pointer-events: none !important;
      }
      .cart-backdrop.active {
        display: block !important;
        pointer-events: auto !important;
      }
      .sidebar-backdrop {
        display: none !important;
        position: fixed !important;
        inset: 0 !important;
        background: rgba(15, 23, 42, 0.8) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 300 !important;
        pointer-events: none !important;
      }
      .sidebar-backdrop.active {
        display: block !important;
        pointer-events: auto !important;
      }

      /* Mobile Sticky Checkout Button */
      .cart-footer, .cart-actions, .checkout-btn-container {
        position: sticky !important;
        bottom: 0 !important;
        background: var(--card) !important;
        padding: 12px !important;
        border-top: 1px solid var(--border) !important;
        z-index: 10 !important;
      }
      .checkout-btn, .btn-place-order {
        min-height: 48px !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        border-radius: 14px !important;
        width: 100% !important;
      }

      /* All Popups & Modals Fit Responsive Viewport */
      .modal-dialog, .modal-content, .qr-modal-card, .payment-modal, .kot-modal {
        max-width: 94vw !important;
        max-height: 90vh !important;
        overflow-y: auto !important;
        border-radius: 20px !important;
        margin: auto !important;
        padding: 18px 14px !important;
      }
    }

    /* 1 Column on Very Small Phones (<= 359px) */
    @media (max-width: 359px) {
      .menu-grid {
        grid-template-columns: 1fr !important;
      }
      .item-img {
        height: 120px !important;
      }
    }
  </style>
</head>
<body>
@include('partials.impersonate-banner')

<!-- ══════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════ -->
@php
  $posRest = $restaurant ?? (auth()->user() ? auth()->user()->restaurant : null) ?? \App\Models\Restaurant::find($restaurantId ?? 1);
  $posRestName = $posRest ? $posRest->name : 'DineFlow AI POS';
  $posRestLogo = $posRest ? $posRest->getEffectiveLogoUrl() : null;
@endphp
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon" onclick="if(document.getElementById('sidebar')?.classList.contains('collapsed')) toggleSidebar();" title="{{ $posRestName }}" style="background: transparent; box-shadow: none; width: 34px; height: 34px; overflow: hidden; border-radius: 8px; margin-right: 2px; cursor: pointer;">
      @if($posRestLogo)
        <img src="{{ $posRestLogo }}" alt="{{ $posRestName }}" style="width: 100%; height: 100%; object-fit: contain; background: #fff; padding: 2px; border-radius: 6px;">
      @else
        <div style="width: 100%; height: 100%; border-radius: 6px; background: linear-gradient(135deg, #6366F1, #4F46E5); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; font-family: 'Outfit', sans-serif;">{{ substr($posRestName, 0, 1) }}</div>
      @endif
    </div>
    <div class="brand-text">
      <h2 style="font-size: 14px; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;" title="{{ $posRestName }}">{{ $posRestName }}</h2>
      <span style="font-size: 10.5px; opacity: 0.7;">POS Terminal &bull; DineFlow</span>
    </div>
    <button class="sidebar-toggle" onclick="toggleSidebar()" id="posSidebarToggleBtn" title="Collapse sidebar (Ctrl+B)">
      <i class="fa-solid fa-chevron-left" id="sidebarToggleIcon"></i>
    </button>
  </div>

  <div class="sidebar-nav">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Main</div>
      <a class="nav-item" href="/dashboard" data-path="/dashboard" data-tip="Dashboard"><i class="fa-solid fa-chart-pie"></i><span class="nav-label"> Dashboard</span></a>
      <a class="nav-item active" href="/pos" data-path="/pos" data-tip="POS Terminal"><i class="fa-solid fa-cash-register"></i><span class="nav-label"> POS Terminal</span> <span class="nav-badge green">Live</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openModal('ordersModal')" style="cursor:pointer;" data-tip="Orders"><i class="fa-solid fa-receipt"></i><span class="nav-label"> Orders</span> <span class="nav-badge" id="globalOrdersNavBadge">{{ $todayOrdersCount ?? 0 }}</span></a>
      <a class="nav-item" href="/admin/tables" data-path="/admin/tables" data-tip="Table Manager"><i class="fa-solid fa-table-cells-large"></i><span class="nav-label"> Table Manager</span></a>
      <a class="nav-item" href="/kitchen" data-path="/kitchen" data-tip="Kitchen Display" target="_blank"><i class="fa-solid fa-fire-burner"></i><span class="nav-label"> Kitchen Display</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Menu &amp; Catalog</div>
      <a class="nav-item" href="/admin/menu-manager" data-path="/admin/menu-manager" data-tip="Menu Items"><i class="fa-solid fa-utensils"></i><span class="nav-label"> Menu Items</span></a>
      <a class="nav-item" href="/admin/inventory" data-path="/admin/inventory" data-tip="Inventory Stock"><i class="fa-solid fa-boxes-stacked"></i><span class="nav-label"> Inventory Stock</span></a>
      <a class="nav-item" href="/admin/coupons" data-path="/admin/coupons" data-tip="Offers &amp; Deals"><i class="fa-solid fa-gift"></i><span class="nav-label"> Offers &amp; Deals</span> <span class="nav-badge red" style="background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:10px;">Deals</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Operations &amp; Sales</div>
      <a class="nav-item" href="/admin/sales-report" data-path="/admin/sales-report" data-tip="Sales Analytics"><i class="fa-solid fa-chart-line"></i><span class="nav-label"> Sales Analytics</span></a>
      <a class="nav-item" href="/admin/expenses" data-path="/admin/expenses" data-tip="Expenses"><i class="fa-solid fa-file-invoice-dollar"></i><span class="nav-label"> Expenses</span></a>
      <a class="nav-item" href="/admin/customers" data-path="/admin/customers" data-tip="Customers &amp; CRM"><i class="fa-solid fa-users"></i><span class="nav-label"> Customers &amp; CRM</span></a>
      <a class="nav-item" href="/admin/qr-generator" data-path="/admin/qr-generator" data-tip="QR Generator"><i class="fa-solid fa-qrcode"></i><span class="nav-label"> QR Generator</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Settings &amp; Admin</div>
      <a class="nav-item" href="/admin/staff" data-path="/admin/staff" data-tip="Staff &amp; Roles"><i class="fa-solid fa-user-shield"></i><span class="nav-label"> Staff &amp; Roles</span></a>
      <a class="nav-item" href="/admin/settings" data-path="/admin/settings" data-tip="System Settings"><i class="fa-solid fa-gear"></i><span class="nav-label"> System Settings</span></a>
      <a class="nav-item" href="/admin/feedback" data-path="/admin/feedback" data-tip="Feedback &amp; Reviews"><i class="fa-solid fa-star"></i><span class="nav-label"> Feedback &amp; Reviews</span></a>
    </div>
  </div>



  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">{{ substr(auth()->user()?->name ?? 'Admin', 0, 1) }}</div>
      <div class="user-info">
        <h4>{{ auth()->user()?->name ?? 'Administrator' }}</h4>
        <p style="text-transform:capitalize;">{{ auth()->user()?->role ?? 'User' }}</p>
      </div>
      <form method="POST" action="{{ route('auth.logout') }}" style="margin-left:auto;">
        @csrf
        <button type="submit" title="Logout"
          style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#F87171;
                 width:32px; height:32px; border-radius:8px; cursor:pointer;
                 display:flex; align-items:center; justify-content:center; font-size:14px;
                 transition:all .2s;"
          onmouseover="this.style.background='rgba(239,68,68,0.35)'"
          onmouseout="this.style.background='rgba(239,68,68,0.15)'">
          <i class="fa-solid fa-right-from-bracket"></i>
        </button>
      </form>
    </div>
  </div>
</aside>

<!-- ══════════════════════════════════════════════
     MAIN
══════════════════════════════════════════════ -->
<div class="main">

  <!-- Topbar -->
  <div class="topbar">
    <!-- Left: Sidebar Toggle, Restaurant Brand & Live Clock -->
    <div class="topbar-left-cluster">
      <button class="topbar-btn" type="button" onclick="toggleSidebar()" id="posTopbarMenuToggle" title="Toggle Sidebar Navigation (Ctrl+B)" style="cursor:pointer; flex-shrink:0;">
        <i class="fa-solid fa-bars" id="topbarHamburgerIcon"></i>
      </button>

      <div style="display:flex; align-items:center; gap:8px;">
        <div class="topbar-duplicate-brand" style="display:flex; align-items:center; gap:6px;">
          <div class="topbar-brand-avatar" style="flex-shrink:0;">
            @if($posRestLogo)
              <img src="{{ $posRestLogo }}" alt="{{ $posRestName }}" style="width: 28px; height: 28px; border-radius: 7px; object-fit: contain; background: #fff; padding: 2px; border: 1px solid var(--border);">
            @else
              <div style="width: 28px; height: 28px; border-radius: 7px; background: linear-gradient(135deg, #6366F1, #4F46E5); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; font-family: 'Outfit', sans-serif;">{{ substr($posRestName, 0, 1) }}</div>
            @endif
          </div>
          <div style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 13.5px; line-height: 1.2; color: var(--text-primary); display: flex; align-items: center; gap: 6px;">
            <span style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $posRestName }}</span>
            <span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:#10B981; box-shadow:0 0 6px #10B981;" title="POS Terminal Live"></span>
          </div>
        </div>
        <div style="display:flex; flex-direction:column; justify-content:center; line-height:1.2;">
          <div style="font-size: 10px; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 5px;">
            <span id="topbarDate">{{ now()->format('D, M d') }}</span>
            <span id="liveClockBadge">--:--:--</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Center: DineFlow Quick Action Control Cluster -->
    <div class="topbar-center-cluster">
      <!-- Cash Register & Shift Drawer -->
      <button type="button" class="pos-quick-tool-btn shift-btn" id="posShiftBtn" title="Cash Register & Shift Control" onclick="openShiftRegisterModal()">
        <i class="fa-solid fa-cash-register"></i>
        <span id="posShiftText">Shift</span>
      </button>

      <!-- Online Delivery & Aggregator Hub (Zomato / Swiggy / Riders) -->
      <button type="button" class="pos-quick-tool-btn online-btn" id="posDeliveryHubBtn" title="Online Orders & Delivery Hub" onclick="openDeliveryHubModal()">
        <i class="fa-solid fa-motorcycle"></i>
        <span>Online</span>
        <span id="onlinePendingBadge" style="display:none; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:1px 6px; border-radius:10px; margin-left:3px;">0</span>
      </button>

      <!-- Multi-Cart Hold & Recall Hub (F4) -->
      <button type="button" class="pos-quick-tool-btn hold-btn" id="posHoldOrdersBtn" title="Hold & Recall Orders (F4)" onclick="openHoldOrdersModal()">
        <i class="fa-solid fa-circle-pause"></i>
        <span>Hold</span>
        <span id="holdCartsCountBadge" style="display:none; background:#EC4899; color:#fff; font-size:10px; font-weight:800; padding:1px 6px; border-radius:10px; margin-left:3px;">0</span>
      </button>

      <!-- Live Stock & Ingredients Inventory Alert (DineFlow Stock Feature) -->
      <button type="button" class="pos-quick-tool-btn stock-btn" id="posInventoryBtn" title="Live Inventory Stock & Low-Stock Alerts" onclick="openInventoryQuickModal()">
        <i class="fa-solid fa-boxes-stacked"></i>
        <span>Stock</span>
        <span id="topbarLowStockBadge" style="display:none; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:1px 6px; border-radius:10px; margin-left:3px;">0</span>
      </button>

      <!-- Today's Orders / Bills (F3) -->
      <button type="button" class="pos-quick-tool-btn orders-btn" id="posOrdersModalBtn" title="View Today's Bills & Orders (F3)" onclick="openModal('ordersModal')">
        <i class="fa-solid fa-receipt"></i>
        <span>Orders</span>
      </button>

      <!-- Kitchen Display System (KDS) Direct Link -->
      <a href="/kitchen" target="_blank" class="pos-quick-tool-btn kds-btn" title="Open Kitchen Display System (KDS) in New Tab">
        <i class="fa-solid fa-fire-burner"></i>
        <span>KDS</span>
      </a>

      <!-- Staff Shift Clock-In/Out Quick Button -->
      <button type="button" class="pos-quick-tool-btn staff-btn" id="posAttendanceQuickBtn" title="Staff Attendance & Clock In/Out" onclick="openStaffAttendanceModal()">
        <i class="fa-solid fa-user-clock"></i>
        <span>Staff</span>
      </button>
    </div>

    <!-- Right: Operations Dropdown, Network Sentinel, User Menu & Quick Logout -->
    <div class="topbar-right-cluster">
      <!-- Operations Dropdown (Recipe Costing, GST Tax, Shortcuts, Themes) -->
      <div class="pos-dropdown-wrap">
        <button type="button" class="pos-quick-tool-btn tools-btn" id="posMoreToolsBtn" onclick="toggleMoreToolsMenu(event)" title="Additional Management Tools">
          <i class="fa-solid fa-sliders"></i>
          <span>Tools</span>
          <i class="fa-solid fa-chevron-down" style="font-size:9px; margin-left:2px;"></i>
        </button>
        <div class="pos-dropdown-menu" id="posMoreToolsMenu">
          <div class="pos-dropdown-item" id="posRecipeBtn" onclick="openRecipeModal(); closeMoreToolsMenu();">
            <i class="fa-solid fa-bowl-rice" style="color:#8B5CF6; width:18px;"></i>
            <span>Recipe & Costing</span>
          </div>
          <div class="pos-dropdown-item" id="posGstBtn" onclick="openGstReportModal(); closeMoreToolsMenu();">
            <i class="fa-solid fa-file-invoice-dollar" style="color:#0EA5E9; width:18px;"></i>
            <span>GSTR-1 Tax Report</span>
          </div>
          <div class="pos-dropdown-item" id="posAttendanceBtn" onclick="openStaffAttendanceModal(); closeMoreToolsMenu();">
            <i class="fa-solid fa-user-clock" style="color:#10B981; width:18px;"></i>
            <span>Staff Attendance</span>
          </div>
          <div class="pos-dropdown-item" id="posPrinterBtn" onclick="openPrinterConfigModal(); closeMoreToolsMenu();">
            <i class="fa-solid fa-print" style="color:#6366F1; width:18px;"></i>
            <span>Thermal Printer Driver</span>
          </div>
          <div class="pos-dropdown-item" id="posProcurementBtn" onclick="window.open('/procurement/suppliers', '_blank'); closeMoreToolsMenu();">
            <i class="fa-solid fa-truck-ramp-box" style="color:#F59E0B; width:18px;"></i>
            <span>Suppliers & PO</span>
          </div>
          <div class="pos-dropdown-item" id="posShortcutsBtn" onclick="openShortcutsModal(); closeMoreToolsMenu();">
            <i class="fa-solid fa-keyboard" style="color:#38BDF8; width:18px;"></i>
            <span>Shortcuts Guide</span>
            <kbd style="font-size:9.5px; background:var(--bg); border:1px solid var(--border); padding:1px 5px; border-radius:4px; margin-left:auto; color:var(--text-muted);">F1</kbd>
          </div>
          <div class="pos-dropdown-item" id="posThemeToggleBtn" onclick="togglePosTheme();">
            <i id="posThemeIcon" class="fa-solid fa-sun" style="color:#F59E0B; width:18px;"></i>
            <span id="posThemeText">Light Mode</span>
          </div>
          <div style="height:1px; background:var(--border); margin:3px 0;"></div>
          <div class="pos-dropdown-item" onclick="toggleFull(); closeMoreToolsMenu();">
            <i class="fa-solid fa-expand" style="color:var(--text-secondary); width:18px;"></i>
            <span>Fullscreen Mode</span>
          </div>
        </div>
      </div>

      <!-- Offline-Ready Network Sentinel Indicator -->
      <div class="topbar-btn topbar-desktop-only" id="posNetworkStatusBadge" title="Network: Online & Synced" style="width:auto; padding:0 10px; gap:6px; cursor:default; border-radius:10px; background:rgba(16,185,129,0.1); border-color:rgba(16,185,129,0.3); color:#10B981;">
        <i id="posNetworkStatusIcon" class="fa-solid fa-wifi" style="font-size:11px;"></i>
        <span id="posNetworkStatusText" style="font-size:11px; font-weight:700;">Online</span>
      </div>

      <!-- Notifications -->
      <div class="topbar-btn topbar-desktop-only" title="Notifications" onclick="openModal('notifModal')" style="cursor:pointer; position:relative;">
        <i class="fa-solid fa-bell"></i>
        <span class="notif-dot" id="notifDot" style="display:none;"></span>
        <span id="notifCountBadge" style="display:none; position:absolute; top:-4px; right:-4px; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:1px 5px; border-radius:10px; border:2px solid var(--card);">0</span>
      </div>

      <!-- Cashier User Pill with Interactive Dropdown & Quick Logout -->
      <div class="pos-dropdown-wrap pos-user-profile-wrap topbar-desktop-only" style="position:relative; flex-shrink:0;">
        <div class="user-pill-btn" onclick="toggleUserMenu(event)" title="User Profile & Quick Actions">
          <div class="user-avatar" style="width:28px; height:28px; font-size:12px; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; flex-shrink:0;">
            {{ strtoupper(substr(Auth::user()?->name ?? 'Admin', 0, 1)) }}
          </div>
          <div class="user-pill-text" style="display:flex; flex-direction:column; line-height:1.2; min-width:0;">
            <span style="font-size:11.5px; font-weight:700; color:var(--text-primary); max-width:90px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ Auth::user()?->name ?? 'Admin' }}">{{ Auth::user()?->name ?? 'Admin' }}</span>
            <span style="font-size:9.5px; color:var(--text-muted); text-transform:capitalize; font-weight:600;">{{ Auth::user()?->role ?? 'admin' }}</span>
          </div>
          <i class="fa-solid fa-chevron-down" style="font-size:9px; color:var(--text-muted); margin-left:2px;"></i>
        </div>

        <!-- User Profile Dropdown Menu -->
        <div class="pos-dropdown-menu" id="posUserMenu" style="min-width:240px; right:0; top:calc(100% + 8px); padding:10px; z-index:999999;">
          <div style="display:flex; align-items:center; gap:10px; padding:6px 8px 10px; border-bottom:1px solid var(--border); margin-bottom:6px;">
            <div class="user-avatar" style="width:36px; height:36px; font-size:14px; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; flex-shrink:0;">
              {{ strtoupper(substr(Auth::user()?->name ?? 'Admin', 0, 1)) }}
            </div>
            <div style="display:flex; flex-direction:column; min-width:0; overflow:hidden;">
              <span style="font-size:13px; font-weight:800; color:var(--text-primary); text-overflow:ellipsis; overflow:hidden; white-space:nowrap;">{{ Auth::user()?->name ?? 'Admin' }}</span>
              <span style="font-size:11px; color:var(--text-muted);">{{ Auth::user()?->email ?? '' }}</span>
              <span style="font-size:9.5px; font-weight:700; color:#10B981; background:rgba(16,185,129,0.12); padding:1px 6px; border-radius:6px; display:inline-block; margin-top:3px; width:fit-content;">{{ strtoupper(Auth::user()?->role ?? 'ADMIN') }}</span>
            </div>
          </div>
          
          <a href="/dashboard" class="pos-dropdown-item" style="text-decoration:none; color:inherit;">
            <i class="fa-solid fa-gauge" style="color:#6366F1; width:18px;"></i>
            <span>Main Dashboard</span>
          </a>
          <a href="/kitchen" target="_blank" class="pos-dropdown-item" style="text-decoration:none; color:inherit;">
            <i class="fa-solid fa-fire-burner" style="color:#F97316; width:18px;"></i>
            <span>Kitchen Display (KDS)</span>
          </a>
          <a href="/admin/settings" class="pos-dropdown-item" style="text-decoration:none; color:inherit;">
            <i class="fa-solid fa-gear" style="color:#64748B; width:18px;"></i>
            <span>Settings</span>
          </a>

          <div style="height:1px; background:var(--border); margin:6px 0;"></div>

          <!-- Prominent Logout Form Action -->
          <form method="POST" action="{{ route('auth.logout') }}" style="margin:0; width:100%;">
            @csrf
            <button type="submit" class="pos-dropdown-item" style="width:100%; border:none; background:rgba(239,68,68,0.12); color:#EF4444; font-weight:700; cursor:pointer; border-radius:8px; padding:9px 12px; display:flex; align-items:center; gap:10px; transition:all 0.15s ease;"
              onmouseover="this.style.background='#EF4444'; this.style.color='#fff';"
              onmouseout="this.style.background='rgba(239,68,68,0.12)'; this.style.color='#EF4444';">
              <i class="fa-solid fa-arrow-right-from-bracket" style="width:18px;"></i>
              <span>Sign Out / Logout</span>
            </button>
          </form>
        </div>
      </div>

      <!-- Quick 1-Click Logout Button (Always Visible & Pinned to Top Right) -->
      <form method="POST" action="{{ route('auth.logout') }}" class="pos-quick-logout-form">
        @csrf
        <button type="submit" class="pos-quick-logout-btn" title="Quick Logout / Sign Out from POS">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span class="quick-logout-text">Logout</span>
        </button>
      </form>

      <!-- Mobile-only cart button in topbar -->
      <div class="topbar-btn topbar-mobile-cart" title="Cart" onclick="toggleMobileCart()" style="position:relative;">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="topbar-cart-badge" id="topbarCartBadge">0</span>
      </div>
    </div>
  </div>

  <!-- CSS Animations & Floating Drawer for Waiter Alerts -->
  <style>
    @keyframes slideInRightAlert {
      from { transform: translateX(120%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRightAlert {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(120%); opacity: 0; }
    }
  </style>

  <!-- Floating Waiter Alerts Drawer -->
  <div id="waiterAlertsDrawer" style="position: fixed; bottom: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 12px; max-width: 320px; width: 100%; pointer-events: auto;">
  </div>

@if(Auth::user()?->restaurant?->is_demo)
<div id="demoSandboxBanner" style="background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%); color: #000; padding: 4px 16px; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; z-index: 99999; border-bottom: 1px solid rgba(0,0,0,0.15);">
  <div style="display:flex; align-items:center; gap:8px;">
    <span style="background:#000; color:#FBBF24; padding:1px 6px; border-radius:4px; font-size:9.5px; font-weight:900;">DEMO POS</span>
    <span>Isolated sandbox terminal. Real restaurant orders are not affected.</span>
  </div>
  <div style="display:flex; align-items:center; gap:6px;">
    <a href="{{ route('demo.reset') }}" onclick="return confirm('Reset demo sandbox to fresh state?');" style="background: rgba(0,0,0,0.15); color: #000; border: 1px solid rgba(0,0,0,0.3); padding: 2px 7px; border-radius: 4px; text-decoration: none; font-size: 10.5px; font-weight: 800;">
      <i class="fa-solid fa-rotate-left"></i> Reset
    </a>
    <a href="{{ route('demo.exit') }}" style="background: #000; color: #fff; padding: 2px 8px; border-radius: 4px; text-decoration: none; font-size: 10.5px; font-weight: 800;">
      Exit Demo
    </a>
  </div>
</div>
@endif

  <!-- POS Body -->
  <div class="pos-wrapper">

    <!-- ── LEFT ── -->
    <div class="pos-left">

      <!-- Order Meta Bar -->
      <div class="order-meta">
        <!-- Delivery Type -->
        <div class="meta-group">
          <div class="delivery-tabs">
            <div class="delivery-tab active" onclick="setDelivery(this,'dinein')"><i class="fa-solid fa-chair"></i> Dine-In</div>
            <div class="delivery-tab" onclick="setDelivery(this,'takeaway')"><i class="fa-solid fa-bag-shopping"></i> Pickup</div>
            <div class="delivery-tab" onclick="setDelivery(this,'delivery')"><i class="fa-solid fa-motorcycle"></i> Delivery</div>
          </div>
        </div>

        <!-- Table -->
        <div class="meta-group" id="tableGroup">
          <button type="button" class="meta-select meta-picker-btn" onclick="openModal('tableModal')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-table-cells"></i>
              <span class="meta-picker-value" id="selectedTableNo">T-01</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>

        <!-- Waiter -->
        <div class="meta-group">
          <button type="button" class="meta-select meta-picker-btn" style="min-width:140px;" onclick="openMetaPopup('waiter')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-user-tie"></i>
              <span class="meta-picker-value" id="waiterValue">{{ Auth::user()->name ?? 'Staff' }}</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>

        <!-- Covers -->
        <div class="meta-group" id="coversGroup">
          <button type="button" class="meta-select meta-picker-btn" style="min-width:90px;" onclick="openMetaPopup('covers')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-users"></i>
              <span class="meta-picker-value" id="coversValue">2</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>

        <!-- Priority -->
        <div class="meta-group">
          <button type="button" class="meta-select meta-picker-btn" style="min-width:110px;" onclick="openMetaPopup('priority')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-bolt"></i>
              <span class="meta-picker-value" id="priorityValue">Normal</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>
      </div>

      <!-- DineFlow Fast Search & Quick Filters -->
      <div class="menu-controls" style="padding: 10px 14px 6px; display:flex; flex-direction:column; gap:8px;">
        <div style="display:flex; gap:8px; align-items:center; width:100%;">
          <div class="search-box" style="flex:1;">
            <i class="fa-solid fa-search"></i>
            <input class="search-input" id="posSearchInput" placeholder="🔍 Search dishes (name, code, ingredient) or press F1…" oninput="filterItems(this.value)" />
            <button type="button" onclick="togglePosVoiceOrder()" id="posVoiceBtn" title="AI Voice Order (Gujarati/Hindi/English)">
              <i class="fa-solid fa-microphone"></i>
            </button>
          </div>

          <!-- Veg / Non-Veg Quick Dietary Toggles (DineFlow Standard) -->
          <div style="display:flex; gap:4px; align-items:center;">
            <button type="button" id="posFilterAllDietBtn" onclick="setDietaryFilter('all')" class="cat-pill active" style="padding:4px 10px; font-size:11px; font-weight:700; border-radius:8px;">
              All
            </button>
            <button type="button" id="posFilterVegBtn" onclick="setDietaryFilter('veg')" class="cat-pill" style="padding:4px 10px; font-size:11px; font-weight:700; color:#10B981; border-color:rgba(16,185,129,0.3); border-radius:8px;">
              🟢 Veg
            </button>
            <button type="button" id="posFilterNonVegBtn" onclick="setDietaryFilter('nonveg')" class="cat-pill" style="padding:4px 10px; font-size:11px; font-weight:700; color:#EF4444; border-color:rgba(239,68,68,0.3); border-radius:8px;">
              🔴 Non-Veg
            </button>
          </div>

          <!-- Category Full Picker Dropdown -->
          <div class="cat-popup-wrap">
            <button class="cat-popup-btn" id="catPopupBtn" onclick="toggleCatPopup()" style="height:36px; padding:0 12px; border-radius:8px; font-size:12px; font-weight:700;">
              <i class="fa-solid fa-layer-group"></i>
              <span id="catPopupLabel">All Categories</span>
              <span class="cat-active-dot"></span>
              <i class="fa-solid fa-chevron-down" style="font-size:10px;margin-left:2px;"></i>
            </button>
            <div class="modal-overlay" id="catPopupModal">
              <div class="modal cat-popup-modal">
                <div class="modal-header">
                  <div class="modal-title"><i class="fa-solid fa-layer-group" style="color:var(--primary);"></i>&nbsp; Select Category</div>
                  <div class="cat-popup-actions">
                    <span class="cat-popup-clear" onclick="resetCat()">Clear</span>
                    <button class="modal-close" onclick="closeModal('catPopupModal')"><i class="fa-solid fa-xmark"></i></button>
                  </div>
                </div>
                <div class="modal-body">
                  <div class="cat-popup-grid" id="catPopupGrid">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- DineFlow Fast Category Ribbon (Scrollable 1-Click Pills) -->
        <div id="posCategoryRibbon" style="display:flex; gap:6px; overflow-x:auto; padding-bottom:4px; scrollbar-width:none; -webkit-overflow-scrolling:touch; width:100%;">
          <!-- Dynamically filled with top categories -->
        </div>
      </div>

      <!-- Menu Grid -->
      <div class="menu-grid" id="menuGrid">
        @if(isset($menuItems) && count($menuItems) > 0)
          @foreach($menuItems as $item)
            @php
              $isVeg = $item->is_veg ?? true;
              $isAvailable = $item->is_available ?? true;
              $category = $item->category ?? 'Mains';
              $price = (float)($item->price ?? 0);
            @endphp
            <div class="item-card {{ !$isAvailable ? 'item-card-unavailable' : '' }}" onclick="{{ $isAvailable ? "addQuickItem('{$item->id}')" : "showToast('" . addslashes($item->name) . " is Out of Stock!', 'error')" }}">
              <div class="item-img">
                @if(!empty($item->image))
                  <img src="{{ $item->image }}" alt="{{ $item->name }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                  <span style="display:none; font-size:34px;">🍛</span>
                @else
                  <span>🍛</span>
                @endif
                @if(!$isAvailable)
                  <span class="item-badge out">SOLD OUT</span>
                  <div class="item-soldout-overlay">OUT OF STOCK</div>
                @elseif($isVeg)
                  <span class="item-badge veg">VEG</span>
                @else
                  <span class="item-badge nonveg">NON-VEG</span>
                @endif
              </div>
              <div class="item-body">
                <div style="display:flex; align-items:center; gap:5px; margin-bottom:3px;">
                  <span class="pos-diet-icon {{ $isVeg ? 'veg' : 'nonveg' }}" title="{{ $isVeg ? 'Vegetarian' : 'Non-Vegetarian' }}"><span class="diet-dot"></span></span>
                  <span class="item-category-tag">{{ strtoupper($category) }}</span>
                </div>
                <div class="item-name" title="{{ $item->name }}">{{ $item->name }}</div>
                <div class="item-footer">
                  <div class="item-price">₹ {{ number_format($price, 0) }}<small>/dish</small></div>
                  <div class="item-footer-actions">
                    @if($isAvailable)
                      <button class="item-add-btn-pill" type="button" onclick="event.stopPropagation();addQuickItem('{{ $item->id }}')" title="Add to Cart"><i class="fa-solid fa-plus"></i> Add</button>
                    @else
                      <span style="font-size:10px; color:#EF4444; font-weight:700;"><i class="fa-solid fa-ban"></i></span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        @else
          @for($s = 0; $s < 12; $s++)
            <div class="pos-skeleton-card">
              <div class="pos-skeleton-img"></div>
              <div class="pos-skeleton-body">
                <div class="pos-skeleton-line short"></div>
                <div class="pos-skeleton-line" style="width:85%;"></div>
                <div class="pos-skeleton-line price"></div>
              </div>
            </div>
          @endfor
        @endif
      </div>
    </div>

    <!-- ── RIGHT (CART) ── -->
    <div class="pos-right">

      <!-- Cart Header -->
      <div class="cart-header">
        <div>
          <div class="cart-title">
            <i class="fa-solid fa-cart-shopping" style="color:var(--primary);"></i>
            Order Cart
            <span class="cart-count" id="cartCount">0</span>
          </div>
          <div class="cart-order-no">#ORD-20260313-0001 &nbsp;·&nbsp; <span id="cartTableNo">T-01</span></div>
        </div>
        <div style="display:flex;gap:6px;">
          <div class="topbar-btn" title="Hold Order (F4)" onclick="holdCurrentCart()" style="cursor:pointer;"><i class="fa-solid fa-pause"></i></div>
          <div class="topbar-btn" title="Print KOT" onclick="openPrintHub(lastPlacedOrderId)" style="cursor:pointer;"><i class="fa-solid fa-print"></i></div>
          <div class="topbar-btn mobile-close-cart" title="Close Cart" onclick="toggleMobileCart()" style="display:none;"><i class="fa-solid fa-xmark"></i></div>
        </div>
      </div>

      <!-- Customer Strip -->
      <div class="customer-strip">
        <div class="cust-avatar">WC</div>
        <div class="cust-info">
          <h5>Walk-in Customer</h5>
          <p>+91 — &nbsp;·&nbsp; <span style="color:var(--primary);">Walk-in</span></p>
        </div>
        <span class="cust-change" onclick="openModal('customerModal')">Change</span>
      </div>

      <!-- Scrollable Middle Cart Body -->
      <div class="cart-scroll-body">
        <!-- Customer Loyalty Wallet & Birthday Ribbon -->
        <div id="customerWalletStrip" style="display:none; margin: 6px 14px 2px; padding: 6px 10px; background: rgba(16,185,129,0.12); border: 1px dashed rgba(16,185,129,0.3); border-radius: 8px; justify-content: space-between; align-items: center;">
          <div style="font-size: 11.5px; font-weight: 700; color: #10B981; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-wallet"></i>
            <span>Wallet: <b id="custWalletAmount">₹0.00</b></span>
            <span id="custBirthdayBadge" style="display:none; font-size:10px; background:#EC4899; color:#fff; padding:1px 6px; border-radius:6px; font-weight:800;">🎂 Birthday Special!</span>
          </div>
          <button id="custRedeemWalletBtn" type="button" onclick="applyCustomerWalletDiscount()" style="background: #10B981; color: #fff; border: none; border-radius: 6px; padding: 3px 10px; font-size: 11px; font-weight: 800; cursor: pointer;">
            Redeem
          </button>
        </div>

        <!-- Home Delivery Address & Rider Dispatch Strip (DineFlow Delivery Module) -->
        <div id="deliveryDetailsStrip" style="display:none; background:rgba(245,158,11,0.06); border-bottom:1px solid rgba(245,158,11,0.25); padding:10px 14px; flex-direction:column; gap:8px;">
          <div style="display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:11.5px; font-weight:800; color:#F59E0B; display:flex; align-items:center; gap:5px;">
              <i class="fa-solid fa-motorcycle"></i> Home Delivery Details
            </span>
            <span style="font-size:10.5px; color:var(--text-muted);">Required for Delivery</span>
          </div>
          <div style="display:grid; grid-template-columns: 1fr 1fr; gap:6px;">
            <input type="text" id="deliveryCustName" placeholder="Customer Name *" style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:6px 8px; font-size:11.5px; color:var(--text-primary);" />
            <input type="tel" id="deliveryCustPhone" placeholder="Mobile No. *" style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:6px 8px; font-size:11.5px; color:var(--text-primary);" />
          </div>
          <input type="text" id="deliveryCustAddress" placeholder="Flat/House, Society/Street, Landmark *" style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:6px 8px; font-size:11.5px; color:var(--text-primary);" />
          <div style="display:grid; grid-template-columns: 1fr 1.2fr; gap:6px; align-items:center;">
            <div style="display:flex; align-items:center; gap:4px;">
              <span style="font-size:11px; color:var(--text-secondary); white-space:nowrap;">Delivery Fee ₹:</span>
              <input type="number" id="deliveryFeeInput" value="30" min="0" step="5" oninput="updateCartUI()" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:5px 8px; font-size:11.5px; color:var(--text-primary);" />
            </div>
            <div style="display:flex; align-items:center; gap:4px;">
              <select id="deliveryRiderSelect" style="width:100%; background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:5px 8px; font-size:11px; color:var(--text-primary);">
                <option value="">🛵 Auto Assign Rider</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Cart Items -->
        <div class="cart-items" id="cartItems">
        </div>

        <!-- Cart Note -->
        <div class="cart-note-area">
          <input class="cart-note-input" placeholder="Add order notes (allergies, special requests…)" id="cartNote" />
        </div>

        <!-- B2B GST Tax Billing (DineFlow B2B Invoicing Feature) -->
        <div style="padding: 4px 14px 2px;">
          <label style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; cursor: pointer;">
            <input type="checkbox" id="b2bInvoiceToggle" onchange="toggleB2bInvoiceForm()" style="accent-color: #0EA5E9;">
            <span style="color:#0EA5E9;"><i class="fa-solid fa-file-invoice"></i> Add B2B Tax Invoice (Customer GSTIN)</span>
          </label>
          <div id="b2bInvoiceForm" style="display: none; margin-top: 6px; background: rgba(14,165,233,0.08); border: 1px dashed rgba(14,165,233,0.3); padding: 8px; border-radius: 8px; flex-direction: column; gap: 6px;">
            <input type="text" id="b2bCompanyInput" placeholder="Company / Legal Trade Name *" style="background: var(--card); border: 1px solid var(--border); border-radius: 6px; padding: 5px 8px; font-size: 11px; color: var(--text-primary);" />
            <input type="text" id="b2bGstinInput" placeholder="Customer 15-digit GSTIN *" maxlength="15" style="background: var(--card); border: 1px solid var(--border); border-radius: 6px; padding: 5px 8px; font-size: 11px; color: var(--text-primary); text-transform: uppercase;" />
          </div>
        </div>

        <!-- DineFlow Fast Quick-Discount Strip -->
        <div style="padding: 4px 14px 2px; display:flex; align-items:center; justify-content:space-between; gap:6px;">
          <span style="font-size:10.5px; font-weight:700; color:var(--text-muted);"><i class="fa-solid fa-bolt" style="color:#F59E0B;"></i> Quick Disc:</span>
          <div style="display:flex; gap:4px; flex-wrap:wrap;">
            <button type="button" onclick="applyQuickPresetDiscount(5)" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#F59E0B; border-radius:6px; padding:2px 7px; font-size:10.5px; font-weight:800; cursor:pointer;">5%</button>
            <button type="button" onclick="applyQuickPresetDiscount(10)" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#F59E0B; border-radius:6px; padding:2px 7px; font-size:10.5px; font-weight:800; cursor:pointer;">10%</button>
            <button type="button" onclick="applyQuickPresetDiscount(15)" style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#F59E0B; border-radius:6px; padding:2px 7px; font-size:10.5px; font-weight:800; cursor:pointer;">15%</button>
            <button type="button" onclick="applyQuickPresetDiscount(0, 50)" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:#10B981; border-radius:6px; padding:2px 7px; font-size:10.5px; font-weight:800; cursor:pointer;">₹50</button>
            <button type="button" onclick="applyQuickPresetDiscount(0, 100)" style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:#10B981; border-radius:6px; padding:2px 7px; font-size:10.5px; font-weight:800; cursor:pointer;">₹100</button>
            <button type="button" onclick="openModal('posOffersModal')" style="background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.3); color:#818CF8; border-radius:6px; padding:2px 7px; font-size:10.5px; font-weight:800; cursor:pointer;" title="All Offers (F8)"><i class="fa-solid fa-gift"></i></button>
          </div>
        </div>

        <!-- Coupon Row -->
        <div class="coupon-row">
          <input class="coupon-input" placeholder="Have a coupon code?" id="couponInput" />
          <button class="coupon-btn" onclick="applyCoupon()"><i class="fa-solid fa-check"></i> Apply</button>
        </div>
      </div><!-- end .cart-scroll-body -->

      <!-- Fixed Pinned Bottom Checkout Section -->
      <div class="cart-bottom-fixed">
        <!-- Totals -->
        <div class="cart-totals">
          <div class="tot-row"><span class="label">Subtotal (<span id="subItemCount">0</span> items)</span><span class="val" id="subtotal">₹ 0.00</span></div>
          <div class="tot-row discount" id="discountRow" style="display:none;"><span class="label"><i class="fa-solid fa-tag"></i>&nbsp;Discount</span><span class="val" id="discountAmount">₹ 0.00</span></div>
          <div class="tot-row"><span class="label">Service Charge (5%)</span><span class="val" id="serviceCharge">₹ 0.00</span></div>
          <div class="tot-row"><span class="label">GST (18%)</span><span class="val" id="gstAmount">₹ 0.00</span></div>
          <hr class="tot-divider"/>
          <div class="tot-row grand"><span class="label">Grand Total</span><span class="val" id="grandTotal">₹ 0.00</span></div>
        </div>

        <!-- Payment Methods -->
        <div class="payment-section">
          <div class="payment-label">Payment Method</div>
          <div class="pay-methods">
            <div class="pay-method active" onclick="selectPay(this)">
              <i class="fa-solid fa-money-bill-wave" style="pointer-events:none;"></i><span style="pointer-events:none;">Cash</span>
            </div>
            <div class="pay-method" onclick="selectPay(this)">
              <i class="fa-solid fa-credit-card" style="pointer-events:none;"></i><span style="pointer-events:none;">Card</span>
            </div>
            <div class="pay-method" onclick="selectPay(this)" style="border-color:rgba(16,185,129,0.35);">
              <i class="fa-solid fa-qrcode" style="pointer-events:none; color:#34D399;"></i><span style="pointer-events:none;">UPI QR</span>
            </div>
            <div class="pay-method" onclick="selectPay(this)">
              <i class="fa-solid fa-code-branch" style="pointer-events:none;"></i><span style="pointer-events:none;">Split</span>
            </div>
          </div>
          <div class="split-row" id="splitRow" style="display:none; flex-direction:column; gap:8px; margin-top:8px; background:rgba(99,102,241,0.06); padding:8px; border-radius:8px; border:1px dashed rgba(99,102,241,0.3);">
            <div style="font-size:11px; font-weight:700; color:var(--text-secondary); display:flex; justify-content:space-between;">
              <span>Split Payment Allocation</span>
              <span id="splitStatusNotice" style="color:#F59E0B;">Enter split amounts</span>
            </div>
            <div style="display:flex; gap:6px;">
              <input class="split-input" id="splitCashInput" placeholder="Cash ₹" style="flex:1;" oninput="calculateSplitRemaining()" />
              <input class="split-input" id="splitUpiInput" placeholder="UPI ₹" style="flex:1;" oninput="calculateSplitRemaining()" />
              <input class="split-input" id="splitCardInput" placeholder="Card ₹" style="flex:1;" oninput="calculateSplitRemaining()" />
            </div>
          </div>
        </div>

        <!-- Clean & Professional DineFlow POS Action Section -->
        <div class="cart-actions" style="display:flex; flex-direction:column; gap:6px; padding:7px 12px 9px; border-top:1px solid var(--border); background:rgba(0,0,0,0.03);">
          <!-- DineFlow Dual Primary Action: Save & Print KOT (F1) + Pay & Place Order (F2) -->
          <div style="display:grid; grid-template-columns: 1fr 1.35fr; gap:7px; width:100%;">
            <button type="button" class="btn-checkout" onclick="sendKOT()" style="height:40px; border-radius:10px; background:linear-gradient(135deg, #F59E0B, #D97706); color:#FFFFFF; font-size:13px; font-weight:800; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 4px 12px rgba(245,158,11,0.25); transition:all 0.18s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'" title="Send KOT to Kitchen without settling payment (F1)">
              <i class="fa-solid fa-fire-burner" style="font-size:15px;"></i>
              <span>SAVE &amp; KOT <small style="font-size:9.5px; opacity:0.85;">(F1)</small></span>
            </button>
            
            <button type="button" class="btn-checkout" onclick="checkout()" style="height:40px; border-radius:10px; background:linear-gradient(135deg, #10B981, #059669); color:#FFFFFF; font-size:13.5px; font-weight:800; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 4px 14px rgba(16,185,129,0.3); transition:all 0.18s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'" title="Settle & Pay Bill (F2)">
              <i class="fa-solid fa-circle-check" style="font-size:15px;"></i>
              <span>PAY &amp; BILL &bull; <span id="checkoutAmount">₹ 0.00</span></span>
            </button>
          </div>

          <!-- Secondary 4-Button Fast Action Toolbar -->
          <div style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap:5px; width:100%;">
            <!-- Hold Cart Button (F4) -->
            <button onclick="holdCurrentCart()" style="height:31px; border-radius:7px; background:rgba(236,72,153,0.12); border:1px solid rgba(236,72,153,0.3); color:#F472B6; font-size:10.5px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:3px; transition:all 0.15s;" onmouseover="this.style.background='rgba(236,72,153,0.22)'" onmouseout="this.style.background='rgba(236,72,153,0.12)'" title="Hold Current Cart (F4)">
              <i class="fa-solid fa-circle-pause" style="color:#EC4899; font-size:11px;"></i> Hold <small style="font-size:9px; opacity:0.8;">(F4)</small>
            </button>

            <!-- Print & KOT Hub -->
            <button onclick="openPrintHub(lastPlacedOrderId)" style="height:31px; border-radius:7px; background:rgba(99,102,241,0.12); border:1px solid rgba(99,102,241,0.3); color:#A5B4FC; font-size:10.5px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:3px; transition:all 0.15s;" onmouseover="this.style.background='rgba(99,102,241,0.22)'" onmouseout="this.style.background='rgba(99,102,241,0.12)'" title="Print Bill or KOT (F9)">
              <i class="fa-solid fa-print" style="color:#FCD34D; font-size:11px;"></i> Print <small style="font-size:9px; opacity:0.8;">(F9)</small>
            </button>
            
            <!-- WhatsApp Invoice -->
            <button onclick="sendWhatsAppInvoice()" style="height:31px; border-radius:7px; background:rgba(37,211,102,0.12); border:1px solid rgba(37,211,102,0.3); color:#4ADE80; font-size:10.5px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:3px; transition:all 0.15s;" onmouseover="this.style.background='rgba(37,211,102,0.22)'" onmouseout="this.style.background='rgba(37,211,102,0.12)'" title="Send WhatsApp Bill">
              <i class="fa-brands fa-whatsapp" style="color:#25D366; font-size:12px;"></i> WhatsApp
            </button>
            
            <!-- Clear Cart -->
            <button onclick="clearCart()" style="height:31px; border-radius:7px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#FCA5A5; font-size:10.5px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:3px; transition:all 0.15s;" onmouseover="this.style.background='rgba(239,68,68,0.22)'" onmouseout="this.style.background='rgba(239,68,68,0.12)'" title="Clear Cart">
              <i class="fa-solid fa-trash" style="font-size:11px;"></i> Clear
            </button>
          </div>
        </div>
      </div><!-- end .cart-bottom-fixed -->
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════ -->

<!-- WhatsApp Invoice Modal -->
<div class="modal-overlay" id="whatsappModal">
  <div class="modal" style="width:440px; max-width:95vw;">
    <div class="modal-header">
      <div class="modal-title" style="color:#25D366; display:flex; align-items:center; gap:8px;">
        <i class="fa-brands fa-whatsapp" style="font-size:20px;"></i>
        <span>Send WhatsApp Invoice</span>
      </div>
      <button class="modal-close" onclick="closeModal('whatsappModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Customer Mobile / WhatsApp Number:</label>
      <div style="display:flex; gap:8px; margin-bottom:14px;">
        <span style="background:var(--bg); border:1px solid var(--border); padding:8px 12px; border-radius:var(--radius-sm); font-size:13px; font-weight:700; color:var(--text-muted);">+91</span>
        <input id="waPhoneInput" type="tel" class="search-input" placeholder="Enter 10-digit mobile number" style="padding-left:12px; font-size:14px; font-weight:600;" value="9876543210" />
      </div>

      <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Invoice Message Preview:</label>
      <div id="waPreviewBox" style="background:#075E54; color:#DCF8C6; padding:12px; border-radius:10px; font-size:11.5px; font-family:monospace; white-space:pre-wrap; max-height:180px; overflow-y:auto; border:1px solid rgba(255,255,255,0.1);"></div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" style="flex:none;padding:0 16px;height:36px;" onclick="closeModal('whatsappModal')">Cancel</button>
      <button class="btn-checkout" style="flex:none;padding:0 20px;height:38px; background:linear-gradient(135deg, #25D366, #128C7E); color:white; border:none;" onclick="triggerWhatsAppRedirect()">
        <i class="fa-brands fa-whatsapp"></i> Open WhatsApp
      </button>
    </div>
  </div>
</div>

<!-- Live Inventory Stock & Ingredients Modal (DineFlow Stock Management) -->
<div class="modal-overlay" id="inventoryModal">
  <div class="modal" style="width:720px; max-width:95vw; max-height:88vh; display:flex; flex-direction:column; border-radius:18px;">
    <div class="modal-header">
      <div class="modal-title" style="display:flex; align-items:center; gap:8px; color:var(--text-primary); font-size:15px; font-weight:800;">
        <i class="fa-solid fa-boxes-stacked" style="color:#0EA5E9;"></i>
        <span>Live Stock &amp; Ingredient Inventory</span>
        <span id="invLowStockAlertBadge" style="display:none; background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 8px; border-radius:12px;">0 Low Stock</span>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <a href="/admin/inventory" target="_blank" style="padding:6px 12px; background:rgba(14,165,233,0.12); border:1px solid rgba(14,165,233,0.3); border-radius:8px; color:#0EA5E9; font-size:11.5px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:5px;">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Full Manager
        </a>
        <button type="button" class="modal-close" onclick="closeModal('inventoryModal')"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <div class="modal-body" style="flex:1; overflow-y:auto; padding:16px 20px;">
      <div style="display:flex; gap:10px; margin-bottom:14px; align-items:center; justify-content:space-between; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px; position:relative;">
          <i class="fa-solid fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px;"></i>
          <input type="text" id="invQuickSearchInput" placeholder="Filter ingredients (e.g. Paneer, Cheese, Milk)…" oninput="filterQuickInventory(this.value)" style="width:100%; height:36px; padding:0 12px 0 34px; background:var(--bg); border:1px solid var(--border); border-radius:8px; font-size:12.5px; color:var(--text-primary); outline:none;">
        </div>
        <div style="display:flex; gap:8px;">
          <span style="font-size:11.5px; font-weight:700; padding:6px 10px; border-radius:8px; background:rgba(16,185,129,0.1); color:#10B981; border:1px solid rgba(16,185,129,0.3);" id="invTotalCountBadge">0 Total Items</span>
          <span style="font-size:11.5px; font-weight:700; padding:6px 10px; border-radius:8px; background:rgba(239,68,68,0.1); color:#EF4444; border:1px solid rgba(239,68,68,0.3);" id="invLowCountBadge">0 Low / Out</span>
        </div>
      </div>
      
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
          <thead>
            <tr style="border-bottom:1.5px solid var(--border); text-align:left; color:var(--text-muted); font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">
              <th style="padding:8px 10px;">Ingredient / Item</th>
              <th style="padding:8px 10px;">Category</th>
              <th style="padding:8px 10px; text-align:right;">Current Stock</th>
              <th style="padding:8px 10px; text-align:right;">Alert Level</th>
              <th style="padding:8px 10px; text-align:center;">Status</th>
            </tr>
          </thead>
          <tbody id="inventoryModalTbody">
            <tr>
              <td colspan="5" style="text-align:center; padding:30px; color:var(--text-muted);">
                <i class="fa-solid fa-spinner fa-spin" style="font-size:20px; margin-bottom:8px; display:block; color:var(--primary);"></i> Loading live inventory...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer" style="padding:12px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <span style="font-size:11px; color:var(--text-muted);"><i class="fa-solid fa-circle-info"></i> Stock deducts automatically upon KOT placement based on Recipe BOM.</span>
      <button type="button" class="btn-outline" style="height:34px; padding:0 16px; font-size:12px; border-radius:8px;" onclick="closeModal('inventoryModal')">Close</button>
    </div>
  </div>
</div>

<!-- Customer Modal -->
<div class="modal-overlay" id="customerModal">
  <div class="modal" style="width:500px; max-width:94%;">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-user" style="color:var(--primary);"></i>&nbsp; Select Customer</div>
      <div style="display:flex; align-items:center; gap:8px;">
        <button type="button" onclick="openModal('newCustomerModal')" style="padding:6px 14px; background:linear-gradient(135deg,#10B981,#059669); border:none; border-radius:10px; color:#fff; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(16,185,129,0.3);">
          <i class="fa-solid fa-user-plus"></i> + New Customer
        </button>
        <button type="button" class="modal-close" onclick="closeModal('customerModal')"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <div class="modal-body">
      <input class="cust-search" id="custSearchInput" placeholder="Search by name, phone, or email…" onkeyup="filterCustomerModalList()" />
      <div class="cust-list" id="custModalList">
        <div style="text-align:center; padding:28px 16px; color:var(--text-muted); font-size:13px;">
          <i class="fa-solid fa-spinner fa-spin" style="font-size:22px; margin-bottom:10px; display:block; color:var(--primary);"></i>
          Loading customers…
        </div>
      </div>
    </div>
    <div class="modal-footer" style="padding:14px 20px; border-top:1px solid var(--border); display:flex; gap:10px; justify-content:flex-end; align-items:center;">
      <button type="button" class="btn-outline" style="flex:none; padding:0 16px; height:38px; border-radius:10px;" onclick="closeModal('customerModal')"><i class="fa-solid fa-xmark"></i>&nbsp; Cancel</button>
      <button type="button" style="flex:none; padding:0 20px; height:38px; border-radius:10px; background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; font-weight:700; font-size:13px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; box-shadow:0 4px 12px rgba(99,102,241,0.3);" onclick="confirmCustomerSelection()"><i class="fa-solid fa-check"></i>&nbsp; Confirm</button>
    </div>
  </div>
</div>

<!-- Register New Customer Modal -->
<div class="modal-overlay" id="newCustomerModal">
  <div class="modal" style="width:440px; max-width:94%; border-radius:20px; overflow:hidden;">
    <div class="modal-header" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-user-plus" style="color:#6366F1;"></i>
        <span>Register New Customer</span>
      </div>
      <button class="modal-close" onclick="closeModal('newCustomerModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:20px;">
      <div style="display:flex; flex-direction:column; gap:14px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Full Name *</label>
          <input type="text" id="newCustNameInput" placeholder="e.g. Ramesh Bhai" style="width:100%; height:40px; border-radius:10px; padding:0 12px; font-size:13px;" required />
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Mobile / WhatsApp Number *</label>
          <input type="tel" id="newCustPhoneInput" placeholder="e.g. 9898012345" style="width:100%; height:40px; border-radius:10px; padding:0 12px; font-size:13px;" required />
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:600; color:#94A3B8; margin-bottom:6px;">Email Address (Optional)</label>
          <input type="email" id="newCustEmailInput" placeholder="e.g. ramesh@gmail.com" style="width:100%; height:40px; border-radius:10px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 12px; font-size:13px;" />
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
          <div>
            <label style="display:block; font-size:11.5px; font-weight:600; color:#EC4899; margin-bottom:6px;"><i class="fa-solid fa-cake-candles"></i> Birthday (DOB)</label>
            <input type="date" id="newCustDobInput" style="width:100%; height:38px; border-radius:8px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 10px; font-size:12px;" />
          </div>
          <div>
            <label style="display:block; font-size:11.5px; font-weight:600; color:#F59E0B; margin-bottom:6px;"><i class="fa-solid fa-ring"></i> Anniversary</label>
            <input type="date" id="newCustAnnivInput" style="width:100%; height:38px; border-radius:8px; background:rgba(0,0,0,0.2); border:1px solid rgba(255,255,255,0.1); color:#fff; padding:0 10px; font-size:12px;" />
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="padding:14px 20px; border-top:1px solid rgba(255,255,255,0.06); display:flex; justify-content:flex-end; gap:10px;">
      <button class="btn-outline" style="height:36px; padding:0 16px; font-size:12.5px;" onclick="closeModal('newCustomerModal')">Cancel</button>
      <button class="btn-checkout" style="height:36px; padding:0 20px; font-size:12.5px; background:linear-gradient(135deg,#6366F1,#4F46E5);" onclick="saveNewCustomer()"><i class="fa-solid fa-user-check"></i>&nbsp; Save &amp; Select</button>
    </div>
  </div>
</div>

<!-- Smart Offers & Discounts Suite Modal -->
<div class="modal-overlay" id="posOffersModal">
  <div class="modal" style="width:560px; max-width:95%; border-radius:24px; overflow:hidden;">
    
    <!-- Modal Header -->
    <div class="modal-header" style="background:linear-gradient(135deg,#D97706,#B45309); padding:18px 22px; color:#fff; display:flex; align-items:center; justify-content:space-between;">
      <div class="modal-title" style="color:#fff; font-size:17px; font-weight:800; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-gift" style="font-size:20px;"></i>
        <span>Offers &amp; Discounts</span>
      </div>
      <div style="display:flex; align-items:center; gap:10px;">
        <a href="/admin/coupons" target="_blank" style="background:rgba(255,255,255,0.22); border:1px solid rgba(255,255,255,0.4); color:#fff; padding:5px 12px; border-radius:8px; font-size:11.5px; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s;" title="Edit or Create New Offers">
          <i class="fa-solid fa-pen-to-square"></i> Edit &amp; Add Offers
        </a>
        <button class="modal-close" onclick="closeModal('posOffersModal')" style="color:#fff;"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>

    <div class="modal-body" style="padding:22px; max-height:460px; overflow-y:auto;">
      
      <!-- 🎟️ Integrated Coupon Code Input Section -->
      <div style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:14px; margin-bottom:18px;">
        <label style="display:block; font-size:12px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">
          <i class="fa-solid fa-ticket" style="color:#F59E0B;"></i> Have a Custom Coupon Code?
        </label>
        <div style="display:flex; gap:8px;">
          <input class="cust-search" id="modalCouponInput" placeholder="Enter coupon code (e.g. WELCOME15, SAVE50)" style="margin-bottom:0; flex:1; height:42px; font-weight:700; text-transform:uppercase;" />
          <button onclick="applyModalCoupon()" style="background:linear-gradient(135deg,#F59E0B,#D97706); color:#fff; border:none; padding:0 20px; border-radius:10px; font-size:13px; font-weight:800; cursor:pointer; flex-shrink:0;">
            <i class="fa-solid fa-check"></i> Apply Code
          </button>
        </div>
      </div>

      <!-- ⭐ Customer Loyalty Points Redemption Section -->
      <div style="background:linear-gradient(135deg, rgba(236,72,153,0.12), rgba(168,85,247,0.12)); border:1px solid rgba(236,72,153,0.3); border-radius:16px; padding:14px; margin-bottom:18px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
          <div style="display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-star" style="color:#F59E0B; font-size:16px;"></i>
            <span style="font-size:13px; font-weight:800; color:var(--text-primary);">Loyalty Points Redemption</span>
          </div>
          <span style="font-size:11.5px; font-weight:700; color:#EC4899; background:rgba(236,72,153,0.15); padding:2px 8px; border-radius:8px;" id="modalLoyaltyPointsBadge">
            Available: <span id="modalCustPointsCount">0</span> pts
          </span>
        </div>
        
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
          <button onclick="redeemCustomerLoyaltyPoints(50, 50)" style="background:var(--card); border:1px solid var(--border); color:var(--text-primary); border-radius:10px; padding:8px 10px; font-size:11.5px; font-weight:700; cursor:pointer; text-align:left; display:flex; flex-direction:column; gap:2px; transition:all 0.2s;">
            <span>⭐ Redeem 50 Pts</span>
            <small style="color:#10B981; font-weight:800;">Get ₹50 Instant OFF</small>
          </button>
          
          <button onclick="redeemCustomerLoyaltyPoints(100, 100)" style="background:var(--card); border:1px solid var(--border); color:var(--text-primary); border-radius:10px; padding:8px 10px; font-size:11.5px; font-weight:700; cursor:pointer; text-align:left; display:flex; flex-direction:column; gap:2px; transition:all 0.2s;">
            <span>⭐ Redeem 100 Pts</span>
            <small style="color:#10B981; font-weight:800;">Get ₹100 Instant OFF</small>
          </button>
        </div>
      </div>

      <!-- Active Offer Indicator Banner (If Applied) -->
      <div id="activeOfferBanner" style="display:none; background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); border-radius:14px; padding:12px 16px; margin-bottom:18px; align-items:center; justify-content:space-between;">
        <div style="display:flex; align-items:center; gap:10px;">
          <i class="fa-solid fa-circle-check" style="color:#10B981; font-size:18px;"></i>
          <div>
            <div style="font-size:13px; font-weight:800; color:var(--text-primary);" id="activeOfferBannerTitle">Offer Applied!</div>
            <div style="font-size:11px; color:var(--text-muted);" id="activeOfferBannerSub">Discount active on current cart</div>
          </div>
        </div>
        <button onclick="removeActiveOffer()" style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#F87171; border-radius:8px; padding:5px 12px; font-size:11.5px; font-weight:700; cursor:pointer;">
          Remove Offer
        </button>
      </div>

      <div style="font-size:12px; font-weight:700; color:var(--text-secondary); margin-bottom:12px; display:flex; align-items:center; justify-content:space-between;">
        <span>Available Promotional Deals</span>
        <span style="font-size:11px; color:var(--text-muted);">Click to apply instantly</span>
      </div>

      <!-- Dynamic Offers Grid -->
      <div id="dynamicPosOffersContainer" style="display:flex; flex-direction:column; gap:12px;">
        
        <!-- Default Fallback 1: First-Time Customer Discount -->
        <div class="offer-card-item" onclick="applySmartOffer('WELCOME15', '15% Welcome Discount (First-Time Customer)', 0, 15, 0)" style="display:flex; align-items:center; justify-content:space-between; padding:14px; border-radius:14px; background:var(--card); border:1px solid var(--border); cursor:pointer; transition:all 0.2s;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(99,102,241,0.15); color:#6366F1; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
              🆕
            </div>
            <div>
              <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);">First Order Welcome Discount</div>
              <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Get 15% OFF on 1st Order for new registered customers</div>
              <span style="font-size:10px; font-weight:800; color:#6366F1; background:rgba(99,102,241,0.1); padding:2px 7px; border-radius:6px; margin-top:4px; display:inline-block;">Code: WELCOME15</span>
            </div>
          </div>
          <button style="background:linear-gradient(135deg,#6366F1,#4F46E5); color:#fff; border:none; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; cursor:pointer;">Apply</button>
        </div>

        <!-- Default Fallback 2: Min Order Tier: Spend ₹500+ Get ₹50 OFF -->
        <div class="offer-card-item" onclick="applySmartOffer('SAVE50', '₹50 OFF (Min Order ₹500)', 50, 0, 500)" style="display:flex; align-items:center; justify-content:space-between; padding:14px; border-radius:14px; background:var(--card); border:1px solid var(--border); cursor:pointer; transition:all 0.2s;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(16,185,129,0.15); color:#10B981; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
              💵
            </div>
            <div>
              <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);">Spend ₹500+ Save ₹50</div>
              <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Flat ₹50 OFF on cart subtotal above ₹500</div>
              <span style="font-size:10px; font-weight:800; color:#10B981; background:rgba(16,185,129,0.1); padding:2px 7px; border-radius:6px; margin-top:4px; display:inline-block;">Code: SAVE50</span>
            </div>
          </div>
          <button style="background:linear-gradient(135deg,#10B981,#059669); color:#fff; border:none; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; cursor:pointer;">Apply</button>
        </div>

        <!-- Default Fallback 3: Min Order Tier: Spend ₹1000+ Get ₹120 OFF -->
        <div class="offer-card-item" onclick="applySmartOffer('SAVE120', '₹120 OFF (Min Order ₹1000)', 120, 0, 1000)" style="display:flex; align-items:center; justify-content:space-between; padding:14px; border-radius:14px; background:var(--card); border:1px solid var(--border); cursor:pointer; transition:all 0.2s;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(245,158,11,0.15); color:#F59E0B; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
              🎉
            </div>
            <div>
              <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);">Spend ₹1000+ Save ₹120</div>
              <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Flat ₹120 OFF on cart subtotal above ₹1000</div>
              <span style="font-size:10px; font-weight:800; color:#F59E0B; background:rgba(245,158,11,0.1); padding:2px 7px; border-radius:6px; margin-top:4px; display:inline-block;">Code: SAVE120</span>
            </div>
          </div>
          <button style="background:linear-gradient(135deg,#F59E0B,#D97706); color:#fff; border:none; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; cursor:pointer;">Apply</button>
        </div>

        <!-- Default Fallback 4: VIP Loyalty Member Discount -->
        <div class="offer-card-item" onclick="applySmartOffer('VIP10', '10% VIP Loyalty Discount', 0, 10, 0)" style="display:flex; align-items:center; justify-content:space-between; padding:14px; border-radius:14px; background:var(--card); border:1px solid var(--border); cursor:pointer; transition:all 0.2s;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(236,72,153,0.15); color:#EC4899; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
              👑
            </div>
            <div>
              <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);">VIP Gold Member Special</div>
              <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Flat 10% OFF for Loyalty &amp; VIP Members</div>
              <span style="font-size:10px; font-weight:800; color:#EC4899; background:rgba(236,72,153,0.1); padding:2px 7px; border-radius:6px; margin-top:4px; display:inline-block;">Code: VIP10</span>
            </div>
          </div>
          <button style="background:linear-gradient(135deg,#EC4899,#D946EF); color:#fff; border:none; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; cursor:pointer;">Apply</button>
        </div>

      </div>
    </div>
    <div class="modal-footer" style="padding:14px 20px; background:var(--bg); border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <button onclick="removeActiveOffer()" style="background:none; border:1px solid var(--border); color:var(--text-secondary); padding:8px 16px; font-size:12px; font-weight:700; border-radius:10px; cursor:pointer;">Remove Active Offer</button>
      <button class="btn-outline" onclick="closeModal('posOffersModal')" style="padding:8px 18px; font-size:12px; font-weight:700; border-radius:10px;">Close</button>
    </div>
  </div>
</div>

<!-- Notifications Modal -->
<div class="modal-overlay" id="notifModal">
  <div class="modal" style="width:450px; max-width:95vw; background:var(--card); color:var(--text-primary); border:1px solid var(--border); border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,0.6); overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg,#1E1B4B,#312E81); border-bottom:1px solid rgba(255,255,255,0.1); padding:16px 20px;">
      <div class="modal-title" style="color:#A5B4FC; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-bell" style="color:#818CF8;"></i>
        Notifications &amp; Live Alerts
      </div>
      <button class="modal-close" onclick="closeModal('notifModal')" style="color:#94A3B8;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" id="notifModalBody" style="padding:16px 20px; max-height:360px; overflow-y:auto;">
      <div id="notifModalList" style="display:flex; flex-direction:column; gap:10px;"></div>
    </div>
    <div class="modal-footer" style="padding:14px 20px; background:var(--bg); border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <button class="btn-outline" onclick="markAllNotificationsAsRead()" style="padding:8px 14px; font-size:12px; font-weight:600; border-radius:8px;">Mark All as Read</button>
      <button class="btn-outline" onclick="closeModal('notifModal')" style="padding:8px 14px; font-size:12px; font-weight:600; border-radius:8px;">Close</button>
    </div>
  </div>
</div>

<!-- Table Modal -->
<div class="modal-overlay" id="tableModal">
  <div class="modal selection-popup-modal table-popup-modal">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-table-cells" style="color:var(--primary);"></i>&nbsp; Select Table</div>
      <button class="modal-close" onclick="closeModal('tableModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <!-- DineFlow Shift & Merge Table Quick Bar -->
      <div style="display:flex; gap:8px; margin-bottom:12px;">
        <button type="button" class="btn-outline" onclick="openShiftModal()" style="flex:1; height:36px; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; gap:6px; color:#818CF8; border-color:rgba(99,102,241,0.3); background:rgba(99,102,241,0.08); border-radius:10px; cursor:pointer;">
          <i class="fa-solid fa-right-left"></i> Shift Table
        </button>
        <button type="button" class="btn-outline" onclick="openMergeModal()" style="flex:1; height:36px; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; gap:6px; color:#10B981; border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.08); border-radius:10px; cursor:pointer;">
          <i class="fa-solid fa-object-group"></i> Merge Tables
        </button>
      </div>

      <div style="display:flex;gap:8px;margin-bottom:14px;">
        <select class="meta-select" id="tableFloorSelect" style="flex:1;" onchange="filterTables()">
          <option value="all">All Floors</option>
          <option value="ground">Ground Floor</option>
          <option value="first">First Floor</option>
          <option value="terrace">Terrace</option>
        </select>
        <select class="meta-select" id="tableSectionSelect" style="flex:1;" onchange="filterTables()">
          <option value="all">All Sections</option>
          <option value="indoor">Indoor</option>
          <option value="outdoor">Outdoor</option>
          <option value="private">Private</option>
        </select>
      </div>
      <div class="table-grid">
        <!-- Loaded dynamically from database -->
      </div>
      <div class="table-legend">
        <div class="tl-item"><div class="tl-dot" style="background:var(--border);"></div>Available</div>
        <div class="tl-item"><div class="tl-dot" style="background:var(--danger);"></div>Occupied</div>
        <div class="tl-item"><div class="tl-dot" style="background:var(--warning);"></div>Reserved</div>
        <div class="tl-item"><div class="tl-dot" style="background:var(--primary);"></div>Selected</div>
      </div>
    </div>
  </div>
</div>

<!-- Table Shift & Merge Modal (DineFlow Feature) -->
<div class="modal-overlay" id="shiftMergeModal">
  <div class="modal" style="width:420px; max-width:92vw; background:var(--card); border:1px solid var(--border); border-radius:18px; overflow:hidden;">
    <div class="modal-header" style="padding:14px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" id="shiftMergeModalTitle" style="font-size:15px; font-weight:800; color:var(--text-primary);"><i class="fa-solid fa-right-left" style="color:var(--primary);"></i>&nbsp; Shift Table</div>
      <button class="modal-close" onclick="closeModal('shiftMergeModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:16px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:16px 18px;">
      <input type="hidden" id="shiftMergeAction" value="shift">
      <div style="margin-bottom:14px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Source Table (Currently Occupied):</label>
        <select id="shiftSourceTable" class="meta-select" style="width:100%; height:40px; border-radius:8px;"></select>
      </div>
      <div style="margin-bottom:14px;">
        <label id="shiftTargetLabel" style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Destination Table (Available):</label>
        <select id="shiftTargetTable" class="meta-select" style="width:100%; height:40px; border-radius:8px;"></select>
      </div>
      <div id="shiftMergeNote" style="font-size:11.5px; color:var(--text-muted); background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:10px; line-height:1.5;">
        All active items and bills will be moved seamlessly.
      </div>
    </div>
    <div class="modal-footer" style="padding:12px 18px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;">
      <button class="btn-outline" onclick="closeModal('shiftMergeModal')" style="height:36px; padding:0 14px; border-radius:8px; cursor:pointer;">Cancel</button>
      <button class="btn-checkout" id="shiftMergeSubmitBtn" onclick="executeShiftMerge()" style="padding:0 18px; height:36px; border-radius:8px; font-weight:700; cursor:pointer;">Confirm</button>
    </div>
  </div>
</div>

<!-- Cash Register & Shift Modal (DineFlow Feature) -->
<div class="modal-overlay" id="shiftRegisterModal">
  <div class="modal" style="width:480px; max-width:94vw; background:var(--card); border:1px solid var(--border); border-radius:20px; overflow:hidden;">
    <div class="modal-header" style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="font-size:16px; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-cash-register" style="color:#10B981;"></i>
        <span>Cash Register &amp; Shift Control</span>
      </div>
      <button class="modal-close" onclick="closeModal('shiftRegisterModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:16px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:20px;" id="shiftRegisterModalBody">
      <!-- Loaded dynamically via JS -->
      <div style="text-align:center; padding:30px 0;"><i class="fa-solid fa-spinner fa-spin" style="font-size:24px; color:var(--primary);"></i><div style="margin-top:10px; font-size:12px; color:var(--text-muted);">Checking shift status...</div></div>
    </div>
  </div>
</div>

<!-- Petty Cash Modal (DineFlow Cash-In / Cash-Out) -->
<div class="modal-overlay" id="pettyCashModal">
  <div class="modal" style="width:380px; max-width:92vw; background:var(--card); border:1px solid var(--border); border-radius:18px; overflow:hidden;">
    <div class="modal-header" style="padding:14px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="font-size:15px; font-weight:800; color:var(--text-primary);"><i class="fa-solid fa-wallet" style="color:#F59E0B;"></i>&nbsp; Record Petty Cash</div>
      <button class="modal-close" onclick="closeModal('pettyCashModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:16px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:16px 18px;">
      <div style="display:flex; gap:10px; margin-bottom:14px;">
        <label style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; padding:10px; border-radius:10px; border:1px solid var(--border); cursor:pointer; background:rgba(239,68,68,0.08); color:#EF4444; font-weight:700; font-size:12.5px;">
          <input type="radio" name="pettyType" value="cash_out" checked style="accent-color:#EF4444;"> Cash Out (Expense)
        </label>
        <label style="flex:1; display:flex; align-items:center; justify-content:center; gap:6px; padding:10px; border-radius:10px; border:1px solid var(--border); cursor:pointer; background:rgba(16,185,129,0.08); color:#10B981; font-weight:700; font-size:12.5px;">
          <input type="radio" name="pettyType" value="cash_in" style="accent-color:#10B981;"> Cash In (Top Up)
        </label>
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:5px;">Amount (₹):</label>
        <input type="number" id="pettyAmountInput" class="split-input" placeholder="e.g. 150" style="width:100%; height:40px; border-radius:8px; font-size:14px; font-weight:700;" />
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:5px;">Category:</label>
        <select id="pettyCategorySelect" class="meta-select" style="width:100%; height:40px; border-radius:8px;">
          <option value="milk_dairy">🥛 Milk &amp; Dairy</option>
          <option value="ice">🧊 Ice Purchase</option>
          <option value="vegetables">🥬 Vegetables / Emergency Groceries</option>
          <option value="cleaning">🧹 Cleaning / Laundry</option>
          <option value="courier">📦 Parcel &amp; Transport</option>
          <option value="salary_advance">💵 Staff Advance</option>
          <option value="misc" selected>📝 Miscellaneous</option>
        </select>
      </div>
      <div style="margin-bottom:14px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:5px;">Description / Reason:</label>
        <input type="text" id="pettyReasonInput" class="split-input" placeholder="e.g. 2 packets Amul Gold milk" style="width:100%; height:40px; border-radius:8px;" />
      </div>
    </div>
    <div class="modal-footer" style="padding:12px 18px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;">
      <button class="btn-outline" onclick="closeModal('pettyCashModal')" style="height:36px; padding:0 14px; border-radius:8px;">Cancel</button>
      <button class="btn-checkout" onclick="submitPettyCash()" style="height:36px; padding:0 18px; border-radius:8px; font-weight:700;">Save Entry</button>
    </div>
  </div>
</div>

<!-- Anti-Theft: KOT Item Cancellation with Manager PIN Modal -->
<div class="modal-overlay" id="cancelKotItemModal">
  <div class="modal" style="width:400px; max-width:92vw; background:var(--card); border:1px solid var(--border); border-radius:18px; overflow:hidden;">
    <div class="modal-header" style="padding:14px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="font-size:15px; font-weight:800; color:#EF4444; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Manager Security Authorization</span>
      </div>
      <button class="modal-close" onclick="closeModal('cancelKotItemModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:16px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:16px 18px;">
      <input type="hidden" id="cancelKotOrderId">
      <input type="hidden" id="cancelKotItemId">
      <div id="cancelKotItemNotice" style="font-size:12.5px; font-weight:700; color:var(--text-primary); margin-bottom:12px; background:rgba(239,68,68,0.1); padding:10px; border-radius:8px; border:1px solid rgba(239,68,68,0.25);">
        Cancelling Item: <span id="cancelKotItemName" style="color:#EF4444;"></span>
      </div>
      <div style="margin-bottom:12px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:5px;">Mandatory Cancellation Reason:</label>
        <select id="cancelKotReasonSelect" class="meta-select" style="width:100%; height:40px; border-radius:8px;">
          <option value="Customer Changed Mind">Customer Changed Mind</option>
          <option value="Kitchen Delay / Food Not Prepared">Kitchen Delay / Food Not Prepared</option>
          <option value="Wrong Item Punched by Staff">Wrong Item Punched by Staff</option>
          <option value="Ingredient Out of Stock">Ingredient Out of Stock</option>
          <option value="Quality / Taste Issue">Quality / Taste Issue</option>
          <option value="Guest Left Without Eating">Guest Left Without Eating</option>
        </select>
      </div>
      <div style="margin-bottom:14px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:5px;">Manager 4-Digit Security PIN:</label>
        <input type="password" id="cancelKotManagerPin" maxlength="6" class="split-input" placeholder="••••" style="width:100%; height:42px; border-radius:8px; font-size:18px; letter-spacing:6px; text-align:center; font-weight:800;" />
        <div style="font-size:11px; color:var(--text-muted); margin-top:4px; text-align:center;">Default Master PIN is: <b style="color:var(--primary);">1234</b></div>
      </div>
    </div>
    <div class="modal-footer" style="padding:12px 18px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;">
      <button class="btn-outline" onclick="closeModal('cancelKotItemModal')" style="height:36px; padding:0 14px; border-radius:8px;">Dismiss</button>
      <button class="btn-checkout" onclick="confirmCancelKotItem()" style="height:36px; padding:0 18px; border-radius:8px; background:#EF4444; font-weight:700;">Authorize Cancel</button>
    </div>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════════════
     DINEFLOW ONLINE AGGREGATOR & DELIVERY HUB MODAL (Zomato / Swiggy / Delivery Boys)
════════════════════════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="deliveryHubModal">
  <div class="modal" style="width:780px; max-width:96vw; max-height:90vh; background:var(--card); border:1px solid var(--border); border-radius:20px; overflow:hidden; display:flex; flex-direction:column;">
    
    <!-- Header -->
    <div class="modal-header" style="padding:14px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:rgba(245,158,11,0.05);">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, #F59E0B, #D97706); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <i class="fa-solid fa-motorcycle"></i>
        </div>
        <div>
          <div class="modal-title" style="font-size:16px; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
            <span>Online Orders &amp; Delivery Hub</span>
            <span style="font-size:11px; font-weight:700; color:#10B981; background:rgba(16,185,129,0.12); padding:2px 8px; border-radius:12px; border:1px solid rgba(16,185,129,0.3);">
              <i class="fa-solid fa-circle-dot fa-fade"></i> Live Feed
            </span>
          </div>
          <div style="font-size:11px; color:var(--text-muted); margin-top:1px;">Zomato, Swiggy &amp; In-House Delivery Dispatch Control</div>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <button onclick="simulateOnlineOrderTest()" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#EF4444; border-radius:10px; padding:6px 12px; font-size:11.5px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;" title="Simulate incoming order with Siren Alarm">
          <i class="fa-solid fa-bell fa-shake"></i> Test Order (Alarm)
        </button>
        <button class="modal-close" onclick="closeModal('deliveryHubModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:18px;"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div style="display:flex; border-bottom:1px solid var(--border); background:var(--bg); padding:0 20px;">
      <button id="delivTabAggregators" onclick="switchDeliveryHubTab('aggregators')" style="padding:12px 16px; border:none; background:none; color:var(--primary); font-size:13px; font-weight:800; border-bottom:2px solid var(--primary); cursor:pointer; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-utensils"></i> Live Zomato &amp; Swiggy Feed
        <span id="tabPendingBadge" style="background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:1px 6px; border-radius:10px; display:none;">0</span>
      </button>
      <button id="delivTabRiders" onclick="switchDeliveryHubTab('riders')" style="padding:12px 16px; border:none; background:none; color:var(--text-muted); font-size:13px; font-weight:700; border-bottom:2px solid transparent; cursor:pointer; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-person-biking"></i> Delivery Riders &amp; Dispatches
      </button>
    </div>

    <!-- Body Panels -->
    <div class="modal-body" style="padding:18px 20px; overflow-y:auto; flex:1; min-height:360px;">
      
      <!-- Panel 1: Live Zomato / Swiggy Orders -->
      <div id="panelAggregators" style="display:block;">
        <div id="aggregatorOrdersContainer" style="display:flex; flex-direction:column; gap:12px;">
          <div style="text-align:center; padding:40px 0; color:var(--text-muted);">
            <i class="fa-solid fa-spinner fa-spin" style="font-size:24px; color:var(--primary);"></i>
            <div style="margin-top:10px; font-size:13px;">Checking live online orders...</div>
          </div>
        </div>
      </div>

      <!-- Panel 2: Home Delivery Riders & Dispatch -->
      <div id="panelRiders" style="display:none;">
        <!-- Rider Management Header -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; background:var(--bg); padding:10px 14px; border-radius:12px; border:1px solid var(--border);">
          <div>
            <div style="font-size:13px; font-weight:700; color:var(--text-primary);">Delivery Fleet</div>
            <div style="font-size:11px; color:var(--text-muted);">Assign delivery boys &amp; track cash-on-delivery (COD) settlements</div>
          </div>
          <button onclick="toggleAddRiderForm()" style="background:var(--primary); color:#fff; border:none; border-radius:8px; padding:6px 12px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
            <i class="fa-solid fa-plus"></i> Add Rider
          </button>
        </div>

        <!-- Add Rider Form (Collapsible) -->
        <div id="addRiderFormWrap" style="display:none; margin-bottom:14px; padding:12px; background:rgba(99,102,241,0.06); border:1px dashed rgba(99,102,241,0.3); border-radius:12px;">
          <div style="font-size:12px; font-weight:800; color:var(--primary); margin-bottom:8px;">New Delivery Boy Registration</div>
          <div style="display:grid; grid-template-columns: 1fr 1fr 1fr auto; gap:8px;">
            <input type="text" id="newRiderName" placeholder="Rider Name (e.g. Ramesh Patel)" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:6px 10px; font-size:12px; color:var(--text-primary);" />
            <input type="tel" id="newRiderPhone" placeholder="Mobile Number" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:6px 10px; font-size:12px; color:var(--text-primary);" />
            <input type="text" id="newRiderVehicle" placeholder="Vehicle No. (GJ-01-AB-1234)" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:6px 10px; font-size:12px; color:var(--text-primary);" />
            <button onclick="submitNewRider()" style="background:#10B981; color:#fff; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer;">Save</button>
          </div>
        </div>

        <!-- Active Deliveries List -->
        <div style="font-size:12.5px; font-weight:800; color:var(--text-secondary); margin-bottom:10px;">Ongoing Deliveries &amp; Dispatch</div>
        <div id="activeDeliveriesContainer" style="display:flex; flex-direction:column; gap:10px;">
          <!-- Loaded via JS -->
          <div style="text-align:center; padding:30px 0; color:var(--text-muted); font-size:12.5px;">Loading delivery dispatches...</div>
        </div>

        <!-- Fleet Overview -->
        <div style="font-size:12.5px; font-weight:800; color:var(--text-secondary); margin-top:20px; margin-bottom:10px;">Active Rider Roster</div>
        <div id="ridersRosterContainer" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:10px;">
          <!-- Loaded via JS -->
        </div>
      </div>

    </div>

    <!-- Footer -->
    <div class="modal-footer" style="padding:12px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:var(--bg);">
      <div style="font-size:11.5px; color:var(--text-muted); display:flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-volume-high" style="color:var(--primary);"></i> Siren alert automatically rings when new online order arrives.
      </div>
      <button class="btn-outline" onclick="closeModal('deliveryHubModal')" style="height:34px; padding:0 16px; border-radius:8px; font-weight:700; cursor:pointer;">Close</button>
    </div>

  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════════════
     DINEFLOW RECIPE & FOOD COSTING MODAL (BOM, Profit Margins & Kitchen Wastage)
════════════════════════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="recipeModal">
  <div class="modal" style="width:860px; max-width:96vw; max-height:90vh; background:var(--card); border:1px solid var(--border); border-radius:20px; overflow:hidden; display:flex; flex-direction:column;">
    
    <!-- Header -->
    <div class="modal-header" style="padding:14px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:rgba(139,92,246,0.06);">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, #8B5CF6, #6D28D9); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <i class="fa-solid fa-bowl-rice"></i>
        </div>
        <div>
          <div class="modal-title" style="font-size:16px; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
            <span>Recipes &amp; Food Costing</span>
            <span style="font-size:11px; font-weight:700; color:#8B5CF6; background:rgba(139,92,246,0.12); padding:2px 8px; border-radius:12px; border:1px solid rgba(139,92,246,0.3);">
              BOM Engine
            </span>
          </div>
          <div style="font-size:11px; color:var(--text-muted); margin-top:1px;">Auto-deduct raw materials on sale &amp; track accurate food cost margins</div>
        </div>
      </div>
      <button class="modal-close" onclick="closeModal('recipeModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:18px;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <!-- Tabs -->
    <div style="display:flex; border-bottom:1px solid var(--border); background:var(--bg); padding:0 20px;">
      <button id="recipeTabMenu" onclick="switchRecipeTab('menu')" style="padding:12px 16px; border:none; background:none; color:var(--primary); font-size:13px; font-weight:800; border-bottom:2px solid var(--primary); cursor:pointer; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-list-check"></i> Dish Recipes &amp; Margins
      </button>
      <button id="recipeTabWastage" onclick="switchRecipeTab('wastage')" style="padding:12px 16px; border:none; background:none; color:var(--text-muted); font-size:13px; font-weight:700; border-bottom:2px solid transparent; cursor:pointer; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-trash-can-arrow-up"></i> Kitchen Food Wastage Log
      </button>
    </div>

    <!-- Body -->
    <div class="modal-body" style="padding:18px 20px; overflow-y:auto; flex:1;">
      
      <!-- Panel 1: Recipes & Margins List -->
      <div id="recipePanelMenu" style="display:block;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
          <input type="text" id="recipeSearchInput" placeholder="Filter dishes..." oninput="filterRecipeTable(this.value)" style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:6px 12px; font-size:12px; color:var(--text-primary); width:240px;" />
          <div style="font-size:11.5px; color:var(--text-muted);">
            💡 Green margin indicates high profit (>60%). Click <b>Configure</b> to set ingredients.
          </div>
        </div>

        <div style="overflow-x:auto;">
          <table style="width:100%; border-collapse:collapse; font-size:12px; text-align:left;">
            <thead>
              <tr style="background:var(--bg); border-bottom:1px solid var(--border); color:var(--text-secondary);">
                <th style="padding:10px 12px;">Dish Name</th>
                <th style="padding:10px 12px;">Category</th>
                <th style="padding:10px 12px; text-align:right;">Selling Price</th>
                <th style="padding:10px 12px; text-align:right;">Raw Food Cost</th>
                <th style="padding:10px 12px; text-align:right;">Gross Profit</th>
                <th style="padding:10px 12px; text-align:center;">Margin %</th>
                <th style="padding:10px 12px; text-align:center;">Action</th>
              </tr>
            </thead>
            <tbody id="recipeTableBody">
              <tr>
                <td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">
                  <i class="fa-solid fa-spinner fa-spin"></i> Loading recipes and food costings...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Panel 2: Wastage Logs -->
      <div id="recipePanelWastage" style="display:none;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; background:var(--bg); padding:10px 14px; border-radius:12px; border:1px solid var(--border);">
          <div>
            <div style="font-size:13px; font-weight:700; color:var(--text-primary);">Total Kitchen Loss: <span id="wastageTotalLossLabel" style="color:#EF4444; font-weight:800;">₹0.00</span></div>
            <div style="font-size:11px; color:var(--text-muted);">Log burnt items, spilled milk, spoiled veggies, or expired stock</div>
          </div>
          <button onclick="toggleRecordWastageForm()" style="background:#EF4444; color:#fff; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
            <i class="fa-solid fa-plus"></i> Record Wastage
          </button>
        </div>

        <!-- Add Wastage Form (Collapsible) -->
        <div id="recordWastageFormWrap" style="display:none; margin-bottom:14px; padding:12px; background:rgba(239,68,68,0.06); border:1px dashed rgba(239,68,68,0.3); border-radius:12px;">
          <div style="font-size:12px; font-weight:800; color:#EF4444; margin-bottom:8px;">New Wastage Entry</div>
          <div style="display:grid; grid-template-columns: 1.5fr 1fr 1.5fr auto; gap:8px;">
            <select id="wastageInventorySelect" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:6px 10px; font-size:12px; color:var(--text-primary);">
              <option value="">Select Raw Ingredient</option>
            </select>
            <input type="number" id="wastageQtyInput" placeholder="Quantity (e.g. 1.5)" step="0.1" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:6px 10px; font-size:12px; color:var(--text-primary);" />
            <select id="wastageReasonSelect" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:6px 10px; font-size:12px; color:var(--text-primary);">
              <option value="Spoiled / Expired">Spoiled / Expired</option>
              <option value="Burnt in Kitchen">Burnt in Kitchen</option>
              <option value="Dropped / Spilled">Dropped / Spilled</option>
              <option value="Quality / Taste Issue">Quality / Taste Issue</option>
              <option value="Staff Meal / Testing">Staff Meal / Testing</option>
            </select>
            <button onclick="submitWastageEntry()" style="background:#EF4444; color:#fff; border:none; border-radius:8px; padding:6px 16px; font-size:12px; font-weight:700; cursor:pointer;">Save</button>
          </div>
        </div>

        <!-- Wastage Table -->
        <div style="overflow-x:auto;">
          <table style="width:100%; border-collapse:collapse; font-size:12px; text-align:left;">
            <thead>
              <tr style="background:var(--bg); border-bottom:1px solid var(--border); color:var(--text-secondary);">
                <th style="padding:8px 12px;">Date &amp; Time</th>
                <th style="padding:8px 12px;">Ingredient</th>
                <th style="padding:8px 12px;">Quantity Wasted</th>
                <th style="padding:8px 12px;">Reason</th>
                <th style="padding:8px 12px; text-align:right;">Cost Loss (₹)</th>
                <th style="padding:8px 12px;">Recorded By</th>
              </tr>
            </thead>
            <tbody id="wastageTableBody">
              <!-- Loaded via JS -->
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <div class="modal-footer" style="padding:12px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:var(--bg);">
      <div style="font-size:11.5px; color:var(--text-muted);">
        ✨ Raw stock will automatically be deducted from inventory as orders are billed.
      </div>
      <button class="btn-outline" onclick="closeModal('recipeModal')" style="height:34px; padding:0 16px; border-radius:8px; font-weight:700; cursor:pointer;">Close</button>
    </div>

  </div>
</div>

<!-- Inline Recipe Editor Sub-Modal -->
<div class="modal-overlay" id="editRecipeSubModal">
  <div class="modal" style="width:620px; max-width:94vw; background:var(--card); border:1px solid var(--border); border-radius:18px; overflow:hidden;">
    <div class="modal-header" style="padding:14px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div>
        <div class="modal-title" id="editRecipeModalTitle" style="font-size:15px; font-weight:800; color:var(--text-primary);">Configure Dish Recipe</div>
        <div style="font-size:11px; color:var(--text-muted);">Define raw materials consumed to prepare 1 serving of this dish</div>
      </div>
      <button class="modal-close" onclick="closeModal('editRecipeSubModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:16px;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:16px 18px;">
      <input type="hidden" id="editRecipeMenuItemId">
      
      <div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:12px; font-weight:700; color:var(--text-secondary);">Raw Ingredients Required:</span>
        <button onclick="addRecipeIngredientRow()" style="background:rgba(139,92,246,0.15); border:1px solid rgba(139,92,246,0.3); color:#8B5CF6; border-radius:6px; padding:4px 10px; font-size:11.5px; font-weight:700; cursor:pointer;">
          <i class="fa-solid fa-plus"></i> Add Ingredient
        </button>
      </div>

      <div id="recipeIngredientRowsContainer" style="display:flex; flex-direction:column; gap:8px; max-height:260px; overflow-y:auto; padding-right:4px;">
        <!-- Dynamic rows rendered here -->
      </div>

      <div id="recipeCostEstimateBox" style="margin-top:14px; padding:10px 14px; background:var(--bg); border-radius:10px; border:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; font-size:12px;">
        <div>Selling Price: <b id="recipeSellingPriceLabel">₹0</b></div>
        <div>Estimated Food Cost: <b id="recipeFoodCostLabel" style="color:#F59E0B;">₹0.00</b></div>
        <div>Estimated Margin: <b id="recipeMarginLabel" style="color:#10B981;">0%</b></div>
      </div>
    </div>
    <div class="modal-footer" style="padding:12px 18px; border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:10px;">
      <button class="btn-outline" onclick="closeModal('editRecipeSubModal')" style="height:36px; padding:0 14px; border-radius:8px;">Cancel</button>
      <button class="btn-checkout" onclick="submitRecipeIngredients()" style="height:36px; padding:0 20px; border-radius:8px; font-weight:700; background:#8B5CF6;">Save Recipe</button>
    </div>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════════════
     DINEFLOW GST & TAX COMPLIANCE REPORT MODAL (B2B, B2C GSTR-1 & CSV EXPORT)
════════════════════════════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="gstReportModal">
  <div class="modal" style="width:900px; max-width:96vw; max-height:90vh; background:var(--card); border:1px solid var(--border); border-radius:20px; overflow:hidden; display:flex; flex-direction:column;">
    
    <!-- Header -->
    <div class="modal-header" style="padding:14px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:rgba(14,165,233,0.06);">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg, #0EA5E9, #0284C7); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div>
          <div class="modal-title" style="font-size:16px; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
            <span>GST &amp; Tax Compliance Report</span>
            <span style="font-size:11px; font-weight:700; color:#0EA5E9; background:rgba(14,165,233,0.12); padding:2px 8px; border-radius:12px; border:1px solid rgba(14,165,233,0.3);">
              GSTR-1 Ready
            </span>
          </div>
          <div style="font-size:11px; color:var(--text-muted); margin-top:1px;">CA-audited tax statements, CGST (2.5%) &amp; SGST (2.5%) summary</div>
        </div>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <button onclick="downloadGstCsvReport()" style="background:#10B981; color:#fff; border:none; border-radius:10px; padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px;">
          <i class="fa-solid fa-file-csv"></i> Download CSV for CA
        </button>
        <button class="modal-close" onclick="closeModal('gstReportModal')" style="cursor:pointer; background:none; border:none; color:var(--text-muted); font-size:18px;"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>

    <!-- Date Filter Bar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 20px; background:var(--bg); border-bottom:1px solid var(--border); flex-wrap:wrap; gap:10px;">
      <div style="display:flex; align-items:center; gap:8px; font-size:12px;">
        <span style="font-weight:700; color:var(--text-secondary);">Period:</span>
        <input type="date" id="gstStartDate" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:5px 8px; font-size:12px; color:var(--text-primary);" />
        <span style="color:var(--text-muted);">to</span>
        <input type="date" id="gstEndDate" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:5px 8px; font-size:12px; color:var(--text-primary);" />
        <button onclick="loadGstReportData()" style="background:var(--primary); color:#fff; border:none; border-radius:8px; padding:5px 12px; font-size:12px; font-weight:700; cursor:pointer;">
          Filter
        </button>
      </div>
      <div id="gstPeriodLabel" style="font-size:12px; font-weight:700; color:var(--text-muted);">This Month</div>
    </div>

    <!-- Body -->
    <div class="modal-body" style="padding:18px 20px; overflow-y:auto; flex:1;">
      
      <!-- Summary Cards -->
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:12px; margin-bottom:18px;">
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
          <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Total Invoices</div>
          <div id="gstCardTotalInvoices" style="font-size:18px; font-weight:800; color:var(--text-primary); margin-top:4px;">0</div>
          <div style="font-size:10px; color:var(--text-secondary); margin-top:2px;"><span id="gstCardB2bCount">0</span> B2B | <span id="gstCardB2cCount">0</span> B2C</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
          <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Taxable Value</div>
          <div id="gstCardTaxable" style="font-size:18px; font-weight:800; color:var(--text-primary); margin-top:4px;">₹0.00</div>
          <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">Net sales turnover</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
          <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; font-weight:700;">CGST (2.5%)</div>
          <div id="gstCardCgst" style="font-size:18px; font-weight:800; color:#0EA5E9; margin-top:4px;">₹0.00</div>
          <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">Central Tax</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
          <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; font-weight:700;">SGST (2.5%)</div>
          <div id="gstCardSgst" style="font-size:18px; font-weight:800; color:#0EA5E9; margin-top:4px;">₹0.00</div>
          <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">State Tax</div>
        </div>
        <div style="background:rgba(16,185,129,0.06); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:12px;">
          <div style="font-size:11px; color:#10B981; text-transform:uppercase; font-weight:800;">Total Gross Turn.</div>
          <div id="gstCardGross" style="font-size:18px; font-weight:800; color:#10B981; margin-top:4px;">₹0.00</div>
          <div style="font-size:10px; color:#10B981; margin-top:2px;">Taxable + 5% GST</div>
        </div>
      </div>

      <!-- Invoice Breakdown Table -->
      <div style="font-size:12.5px; font-weight:800; color:var(--text-secondary); margin-bottom:8px;">GSTR-1 Tax Invoice Register</div>
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:11.5px; text-align:left;">
          <thead>
            <tr style="background:var(--bg); border-bottom:1px solid var(--border); color:var(--text-secondary);">
              <th style="padding:8px 10px;">Invoice #</th>
              <th style="padding:8px 10px;">Date</th>
              <th style="padding:8px 10px;">Type</th>
              <th style="padding:8px 10px;">Customer / Trade Name</th>
              <th style="padding:8px 10px;">Customer GSTIN</th>
              <th style="padding:8px 10px; text-align:right;">Taxable ₹</th>
              <th style="padding:8px 10px; text-align:right;">CGST (2.5%)</th>
              <th style="padding:8px 10px; text-align:right;">SGST (2.5%)</th>
              <th style="padding:8px 10px; text-align:right;">Invoice Total ₹</th>
              <th style="padding:8px 10px; text-align:center;">Mode</th>
            </tr>
          </thead>
          <tbody id="gstInvoicesTableBody">
            <!-- Loaded via JS -->
          </tbody>
        </table>
      </div>

    </div>

    <!-- Footer -->
    <div class="modal-footer" style="padding:12px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; background:var(--bg);">
      <div style="font-size:11.5px; color:var(--text-muted);">
        ⚖️ Complies with GST Council composition rules for restaurants (5% GST without input tax credit).
      </div>
      <button class="btn-outline" onclick="closeModal('gstReportModal')" style="height:34px; padding:0 16px; border-radius:8px; font-weight:700; cursor:pointer;">Close</button>
    </div>

  </div>
</div>

<!-- Meta Popup Modal -->
<div class="modal-overlay" id="metaPopupModal">
  <div class="modal selection-popup-modal meta-popup-modal">
    <div class="modal-header">
      <div class="modal-title" id="metaPopupTitle"><i class="fa-solid fa-list" style="color:var(--primary);"></i>&nbsp; Select Option</div>
      <button class="modal-close" onclick="closeModal('metaPopupModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="meta-option-list" id="metaPopupList"></div>
    </div>
  </div>
</div>

<!-- KOT Modal -->
<div class="modal-overlay" id="kotModal">
  <div class="modal" style="width:360px; max-width:95vw;">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-fire-burner" style="color:var(--warning);"></i>&nbsp; Kitchen Order Ticket</div>
      <button class="modal-close" onclick="closeModal('kotModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div style="text-align:center;padding:10px 0 18px;">
        <div style="font-size:12px;color:var(--text-muted);">Order #ORD-20260313-0001</div>
        <div style="font-size:13px;font-weight:600;margin-top:2px;">Table: T-04 &nbsp;|&nbsp; Waiter: <span id="kotWaiterName">Ravi M.</span></div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;" id="kotDateTime">13 Mar 2026 · 12:45 PM</div>
      </div>
      <div id="kotList">
      </div>
      <div style="margin-top:14px;border:1px dashed var(--border);border-radius:var(--radius-sm);padding:10px;">
        <div style="font-size:11px;font-weight:600;color:var(--text-muted);margin-bottom:5px;">ORDER NOTES</div>
        <div style="font-size:12.5px;color:var(--text-primary);" id="kotNotes">No special notes</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" style="flex:none;padding:0 14px;height:36px;" onclick="closeModal('kotModal')">Cancel</button>
      <button class="btn-checkout" style="flex:none;padding:0 16px;height:36px;background:linear-gradient(135deg,var(--warning),#D97706);" onclick="sendKOT()">
        <i class="fa-solid fa-fire"></i>&nbsp; Send to Kitchen
      </button>
    </div>
  </div>
</div>

<!-- Orders Manager Modal -->
<div class="modal-overlay" id="ordersModal">
  <div class="modal selection-popup-modal" style="width:960px; max-width:94vw; max-height:85vh; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; display:flex; flex-direction:column;">
    <div class="modal-header" style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:var(--primary); font-size:17px; font-weight:800; font-family:'Outfit',sans-serif; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-receipt" style="color:var(--primary);"></i>
        <span>Recent Restaurant Orders</span>
        <span id="posOrdersCountBadge" style="background:var(--primary-light); color:var(--primary); font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; border:1px solid var(--border);">Loading...</span>
      </div>
      <div style="display:flex; align-items:center; gap:10px;">
        <button onclick="loadOrdersData()" title="Refresh Orders" style="background:var(--primary-light); border:1px solid var(--border); color:var(--primary); width:34px; height:34px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; transition:all 0.2s;"><i class="fa-solid fa-arrows-rotate"></i></button>
        <button class="modal-close" onclick="closeModal('ordersModal')" style="background:var(--primary-light); border:1px solid var(--border); color:var(--text-muted); width:34px; height:34px; border-radius:10px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.color='#F87171'" onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--text-muted)'"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <div class="modal-body" id="posOrdersModalBody" style="padding:20px 24px; overflow-y:auto; flex:1;">
      <div style="overflow-x:auto; width:100%; -webkit-overflow-scrolling:touch;">
        <table style="width:100%; min-width:620px; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:1px solid var(--border); color:var(--primary); text-align:left; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
              <th style="padding:10px 12px;">Order ID</th>
              <th style="padding:10px 12px;">Type / Table</th>
              <th style="padding:10px 12px;">Amount</th>
              <th style="padding:10px 12px;">Payment</th>
              <th style="padding:10px 12px;">Status</th>
              <th style="padding:10px 12px; text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody id="ordersModalTableBody">
            <!-- Loaded dynamically from API -->
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer" style="padding:16px 24px; background:var(--bg); border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div style="color:var(--text-muted); font-size:12px;">💡 Click any action button to open Receipts, Invoices, or Kitchen KOT</div>
      <button onclick="closeModal('ordersModal')" style="padding:9px 20px; font-size:13px; font-weight:700; border-radius:10px; background:linear-gradient(135deg,#6366F1,#4F46E5); color:#FFFFFF; border:none; cursor:pointer; box-shadow:0 4px 14px rgba(99,102,241,0.35); transition:all 0.2s;">Close</button>
    </div>
  </div>
</div>

@include('partials.order-management')

<!-- Delivery & Online Orders Modal -->
<div class="modal-overlay" id="deliveryOrdersModal">
  <div class="modal selection-popup-modal" style="width:860px; max-width:94vw; max-height:85vh; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; display:flex; flex-direction:column;">
    <div class="modal-header" style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:var(--primary); font-size:17px; font-weight:800; font-family:'Outfit',sans-serif; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-motorcycle" style="color:var(--primary);"></i>
        <span>Delivery &amp; Takeaway Dispatch Terminal</span>
      </div>
      <button class="modal-close" onclick="closeModal('deliveryOrdersModal')" style="background:var(--primary-light); border:1px solid var(--border); color:var(--text-muted); width:34px; height:34px; border-radius:10px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.color='#F87171'" onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--text-muted)'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:18px;">
      <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:12px; margin-bottom:16px;">
        <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:12px; text-align:center;">
          <div style="font-size:11px; font-weight:700; color:#34D399; text-transform:uppercase;">Active Deliveries</div>
          <div style="font-size:22px; font-weight:800; color:#F8FAFC; margin-top:2px;">3 Orders</div>
        </div>
        <div style="background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.3); border-radius:12px; padding:12px; text-align:center;">
          <div style="font-size:11px; font-weight:700; color:#A5B4FC; text-transform:uppercase;">Riders En-Route</div>
          <div style="font-size:22px; font-weight:800; color:#F8FAFC; margin-top:2px;">2 Partners</div>
        </div>
        <div style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); border-radius:12px; padding:12px; text-align:center;">
          <div style="font-size:11px; font-weight:700; color:#FCD34D; text-transform:uppercase;">Avg Delivery Time</div>
          <div style="font-size:22px; font-weight:800; color:#F8FAFC; margin-top:2px;">24 Mins</div>
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table style="width:100%; min-width:600px; border-collapse:collapse; text-align:left; font-size:12.5px; color:var(--text-primary);">
        <thead>
          <tr style="border-bottom:2px solid var(--border); color:var(--text-secondary); text-transform:uppercase; font-size:11px;">
            <th style="padding:10px;">Dispatch ID</th>
            <th style="padding:10px;">Customer &amp; Address</th>
            <th style="padding:10px;">Partner / Mode</th>
            <th style="padding:10px;">Total</th>
            <th style="padding:10px;">Dispatch Status</th>
            <th style="padding:10px;">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:12px 10px; font-weight:700; color:#6EE7B7;">#DEL-1092</td>
            <td style="padding:12px 10px;">
              <div style="font-weight:700; color:#F8FAFC;">Rahul Kumar</div>
              <div style="font-size:11px; color:#94A3B8;">Sector 4, Block B, Patan • +91 98765 43210</div>
            </td>
            <td style="padding:12px 10px;"><span style="background:rgba(16,185,129,0.15); color:#34D399; padding:3px 8px; border-radius:10px; font-weight:700; font-size:11px;">Direct Rider</span></td>
            <td style="padding:12px 10px; font-weight:700; color:#818CF8;">₹ 660</td>
            <td style="padding:12px 10px;"><span style="background:rgba(59,130,246,0.15); color:#60A5FA; padding:3px 8px; border-radius:10px; font-weight:700; font-size:11px;">Out for Delivery</span></td>
            <td style="padding:12px 10px;"><button onclick="showToast('Rider tracked live on map', 'info')" style="padding:4px 10px; background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3); color:#A5B4FC; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer;">Track Rider</button></td>
          </tr>
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:12px 10px; font-weight:700; color:#6EE7B7;">#DEL-1091</td>
            <td style="padding:12px 10px;">
              <div style="font-weight:700; color:#F8FAFC;">Priya Sharma</div>
              <div style="font-size:11px; color:#94A3B8;">College Road, Near Circle, Patan • +91 87654 32109</div>
            </td>
            <td style="padding:12px 10px;"><span style="background:rgba(245,158,11,0.15); color:#FCD34D; padding:3px 8px; border-radius:10px; font-weight:700; font-size:11px;">Swiggy Express</span></td>
            <td style="padding:12px 10px; font-weight:700; color:#818CF8;">₹ 480</td>
            <td style="padding:12px 10px;"><span style="background:rgba(16,185,129,0.15); color:#34D399; padding:3px 8px; border-radius:10px; font-weight:700; font-size:11px;">Delivered</span></td>
            <td style="padding:12px 10px;"><button onclick="showToast('Delivery completed &amp; feedback recorded', 'success')" style="padding:4px 10px; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#34D399; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer;">Completed</button></td>
          </tr>
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:12px 10px; font-weight:700; color:#6EE7B7;">#DEL-1090</td>
            <td style="padding:12px 10px;">
              <div style="font-weight:700; color:#F8FAFC;">Virendra Singh</div>
              <div style="font-size:11px; color:#94A3B8;">High Street Plaza, Shop 12 • +91 95456 78901</div>
            </td>
            <td style="padding:12px 10px;"><span style="background:rgba(239,68,68,0.15); color:#FCA5A5; padding:3px 8px; border-radius:10px; font-weight:700; font-size:11px;">Zomato Delivery</span></td>
            <td style="padding:12px 10px; font-weight:700; color:#818CF8;">₹ 890</td>
            <td style="padding:12px 10px;"><span style="background:rgba(245,158,11,0.15); color:#FCD34D; padding:3px 8px; border-radius:10px; font-weight:700; font-size:11px;">Packing Food</span></td>
            <td style="padding:12px 10px;"><button onclick="showToast('Kitchen notified to expedite packing', 'info')" style="padding:4px 10px; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); color:#FCD34D; border-radius:8px; font-size:11px; font-weight:600; cursor:pointer;">Assign Rider</button></td>
          </tr>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>

<!-- Modifiers & Customizations Modal -->
<div class="modal-overlay" id="modifiersModal">
  <div class="modal selection-popup-modal" style="width:740px; max-width:95%;">
    <div class="modal-header" style="background:linear-gradient(135deg,#312E81,#4338CA); border-bottom:1px solid rgba(255,255,255,0.1); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:#A5B4FC; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-puzzle-piece" style="color:#818CF8; font-size:18px;"></i>
        <span>Dish Modifiers &amp; Customization Manager</span>
      </div>
      <button class="modal-close" onclick="closeModal('modifiersModal')" style="background:none; border:none; color:#94A3B8; font-size:18px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:20px;">
      <p style="font-size:12px; color:#94A3B8; margin:-4px 0 16px;">Click any modifier to attach it live to the active cart item!</p>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">
        <div style="background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.25); border-radius:14px; padding:14px;">
          <div style="font-size:13px; font-weight:700; color:#F8FAFC; margin-bottom:6px;">🧀 Extra Cheese Topping</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-bottom:10px;">Applicable for Pizza, Pasta &amp; Burgers</div>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:800; color:#818CF8;">+ ₹ 40.00</span>
            <button onclick="applyModifierToCart('Extra Cheese', 40)" style="padding:6px 14px; background:linear-gradient(135deg,#6366F1,#4F46E5); border:none; border-radius:8px; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer;">+ Apply to Cart</button>
          </div>
        </div>

        <div style="background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.25); border-radius:14px; padding:14px;">
          <div style="font-size:13px; font-weight:700; color:#F8FAFC; margin-bottom:6px;">🌶️ Extra Spicy / Tikha</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-bottom:10px;">Applicable for all Starters &amp; Curries</div>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:800; color:#FCD34D;">Free Add-on</span>
            <button onclick="applyModifierToCart('Spicy', 0)" style="padding:6px 14px; background:linear-gradient(135deg,#F59E0B,#D97706); border:none; border-radius:8px; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer;">+ Apply to Cart</button>
          </div>
        </div>

        <div style="background:rgba(6,182,212,0.08); border:1px solid rgba(6,182,212,0.25); border-radius:14px; padding:14px;">
          <div style="font-size:13px; font-weight:700; color:#F8FAFC; margin-bottom:6px;">🫓 Extra Butter Naan</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-bottom:10px;">Applicable for Mains &amp; Thali Meals</div>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:800; color:#67E8F9;">+ ₹ 30.00</span>
            <button onclick="applyModifierToCart('Extra Naan', 30)" style="padding:6px 14px; background:linear-gradient(135deg,#06B6D4,#0891B2); border:none; border-radius:8px; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer;">+ Apply to Cart</button>
          </div>
        </div>

        <div style="background:rgba(236,72,153,0.08); border:1px solid rgba(236,72,153,0.25); border-radius:14px; padding:14px;">
          <div style="font-size:13px; font-weight:700; color:#F8FAFC; margin-bottom:6px;">🥗 Jain Prep (No Onion/Garlic)</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-bottom:10px;">Dietary special preparation preference</div>
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <span style="font-weight:800; color:#F472B6;">Free Add-on</span>
            <button onclick="applyModifierToCart('Jain Prep', 0)" style="padding:6px 14px; background:linear-gradient(135deg,#EC4899,#DB2777); border:none; border-radius:8px; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer;">+ Apply to Cart</button>
          </div>
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end;">
        <button onclick="closeModal('modifiersModal');" style="padding:10px 20px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); border-radius:10px; color:#fff; font-weight:700; font-size:13px; cursor:pointer;">Done</button>
      </div>
    </div>
  </div>
</div>

<!-- Combos & Deals Modal -->
<div class="modal-overlay" id="combosModal">
  <div class="modal selection-popup-modal" style="width:760px; max-width:95%;">
    <div class="modal-header" style="background:linear-gradient(135deg,#065F46,#047857); border-bottom:1px solid rgba(255,255,255,0.1); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:#34D399; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-tags" style="color:#6EE7B7; font-size:18px;"></i>
        <span>Special Combos &amp; Value Deals Manager</span>
      </div>
      <button class="modal-close" onclick="closeModal('combosModal')" style="background:none; border:none; color:#94A3B8; font-size:18px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:20px;">
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div style="display:flex; align-items:center; justify-content:space-between; background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.25); border-radius:14px; padding:14px;">
          <div>
            <div style="font-size:14px; font-weight:800; color:#F8FAFC;">🥘 Punjabi Thali Deluxe Combo</div>
            <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">Paneer Butter Masala + 2 Garlic Naan + Veg Biryani + Mango Lassi</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:16px; font-weight:800; color:#34D399;">₹ 380 <small style="text-decoration:line-through; color:#64748B; font-size:11px;">₹ 460</small></div>
            <button onclick="addComboToCart('Punjabi Thali Deluxe', 380)" style="margin-top:6px; padding:5px 12px; background:linear-gradient(135deg,#10B981,#059669); border:none; border-radius:8px; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer;">+ Add Combo</button>
          </div>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; background:rgba(99,102,241,0.08); border:1px solid rgba(99,102,241,0.25); border-radius:14px; padding:14px;">
          <div>
            <div style="font-size:14px; font-weight:800; color:#F8FAFC;">🍗 Chicken Tikka Feast Combo</div>
            <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">Chicken Tikka Masala + 2 Laccha Paratha + Chicken Biryani + Cold Drink</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:16px; font-weight:800; color:#818CF8;">₹ 490 <small style="text-decoration:line-through; color:#64748B; font-size:11px;">₹ 580</small></div>
            <button onclick="addComboToCart('Chicken Tikka Feast', 490)" style="margin-top:6px; padding:5px 12px; background:linear-gradient(135deg,#6366F1,#4F46E5); border:none; border-radius:8px; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer;">+ Add Combo</button>
          </div>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.25); border-radius:14px; padding:14px;">
          <div>
            <div style="font-size:14px; font-weight:800; color:#F8FAFC;">🍟 Starter Snack Combo</div>
            <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">Veg Spring Rolls + Paneer Tikka + Cold Coffee</div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:16px; font-weight:800; color:#FCD34D;">₹ 290 <small style="text-decoration:line-through; color:#64748B; font-size:11px;">₹ 350</small></div>
            <button onclick="addComboToCart('Starter Snack Combo', 290)" style="margin-top:6px; padding:5px 12px; background:linear-gradient(135deg,#F59E0B,#D97706); border:none; border-radius:8px; color:#fff; font-size:11.5px; font-weight:700; cursor:pointer;">+ Add Combo</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ✏️ Custom Currency Setter Modal -->
<div class="modal-overlay" id="posCustomCurrencyModal">
  <div class="modal" style="width:420px; max-width:94%; border-radius:20px; overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg,#6366F1,#4F46E5); padding:16px 20px; color:#fff; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:#fff; font-size:15px; font-weight:800; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-coins"></i>
        <span>Set Custom Currency</span>
      </div>
      <button class="modal-close" onclick="closeModal('posCustomCurrencyModal')" style="color:#fff;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:20px;">
      <div style="display:flex; flex-direction:column; gap:14px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Currency Name / Code (e.g. AUD, CAD, BDT, OMR)</label>
          <input type="text" id="customCurrencyCodeInput" placeholder="e.g. AUD" style="width:100%; height:40px; border-radius:10px; padding:0 12px; font-size:13px; text-transform:uppercase; font-weight:700;" />
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Currency Symbol / Sign (e.g. $, ৳, QR, د.ع)</label>
          <input type="text" id="customCurrencySymbolInput" placeholder="e.g. $" style="width:100%; height:40px; border-radius:10px; padding:0 12px; font-size:14px; font-weight:700;" />
        </div>
      </div>
    </div>
    <div class="modal-footer" style="padding:14px 20px; display:flex; justify-content:flex-end; gap:10px;">
      <button class="btn-outline" style="height:36px; padding:0 16px; font-size:12.5px;" onclick="closeModal('posCustomCurrencyModal')">Cancel</button>
      <button class="btn-checkout" style="height:36px; padding:0 20px; font-size:12.5px; background:linear-gradient(135deg,#6366F1,#4F46E5);" onclick="saveCustomCurrency()"><i class="fa-solid fa-check"></i>&nbsp; Save &amp; Apply</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     DINEFLOW MULTI-CART HOLD & RECALL HUB MODAL
══════════════════════════════════════════════ -->
<div class="modal-overlay" id="holdOrdersModal">
  <div class="modal" style="width:680px; max-width:95vw; max-height:85vh; border-radius:24px; overflow:hidden; display:flex; flex-direction:column; background:var(--card); border:1px solid var(--border); box-shadow:var(--shadow-lg);">
    <div class="modal-header" style="padding:16px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="font-size:16px; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:50%; background:rgba(236,72,153,0.15); color:#EC4899;">
          <i class="fa-solid fa-circle-pause"></i>
        </span>
        <span>Hold &amp; Recall Carts</span>
        <span id="holdCartsModalBadge" style="font-size:11px; font-weight:700; color:#EC4899; background:rgba(236,72,153,0.12); padding:2px 8px; border-radius:12px; border:1px solid rgba(236,72,153,0.3);">0 Held</span>
      </div>
      <button class="modal-close" onclick="closeModal('holdOrdersModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:18px 22px; overflow-y:auto; flex:1;">
      <div id="holdCartsListContainer" style="display:flex; flex-direction:column; gap:12px;">
        <!-- Dynamically populated held carts -->
      </div>
    </div>
    <div class="modal-footer" style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <span style="font-size:12px; color:var(--text-muted);"><i class="fa-solid fa-keyboard" style="color:#EC4899;"></i> Press <b>F4</b> to hold current active cart anytime.</span>
      <button class="btn-outline" onclick="closeModal('holdOrdersModal')" style="height:36px; padding:0 18px; border-radius:10px; font-weight:700; cursor:pointer;">Close</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     KEYBOARD SHORTCUTS CHEAT SHEET MODAL
══════════════════════════════════════════════ -->
<div class="modal-overlay" id="shortcutsModal">
  <div class="modal" style="width:620px; max-width:95vw; max-height:85vh; border-radius:24px; overflow:hidden; display:flex; flex-direction:column; background:var(--card); border:1px solid var(--border); box-shadow:var(--shadow-lg);">
    <div class="modal-header" style="padding:16px 22px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="font-size:16px; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:10px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:50%; background:rgba(56,189,248,0.15); color:#38BDF8;">
          <i class="fa-solid fa-keyboard"></i>
        </span>
        <span>Keyboard Shortcuts &amp; Barcode Scanner</span>
      </div>
      <button class="modal-close" onclick="closeModal('shortcutsModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:20px 22px; overflow-y:auto; flex:1;">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-magnifying-glass" style="color:var(--primary); width:18px;"></i> Search Menu / Item</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#38BDF8; box-shadow:0 2px 0 var(--border);">F1</kbd>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-circle-check" style="color:#10B981; width:18px;"></i> Place Order / Pay</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#10B981; box-shadow:0 2px 0 var(--border);">F2</kbd>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-user" style="color:#F59E0B; width:18px;"></i> Select Customer</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#F59E0B; box-shadow:0 2px 0 var(--border);">F3</kbd>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-circle-pause" style="color:#EC4899; width:18px;"></i> Hold Current Cart</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#EC4899; box-shadow:0 2px 0 var(--border);">F4</kbd>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-gift" style="color:#8B5CF6; width:18px;"></i> Offers &amp; Discounts</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#8B5CF6; box-shadow:0 2px 0 var(--border);">F8</kbd>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-print" style="color:#34D399; width:18px;"></i> Print Hub (Bill/KOT)</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#34D399; box-shadow:0 2px 0 var(--border);">F9</kbd>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-trash" style="color:#EF4444; width:18px;"></i> Clear Active Cart</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#EF4444; box-shadow:0 2px 0 var(--border);">Alt + C</kbd>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:14px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between;">
          <span style="font-size:13px; font-weight:600; color:var(--text-primary);"><i class="fa-solid fa-xmark" style="color:#94A3B8; width:18px;"></i> Close Any Modal</span>
          <kbd style="background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; font-size:12px; font-weight:800; color:#94A3B8; box-shadow:0 2px 0 var(--border);">Esc</kbd>
        </div>
      </div>
      
      <!-- Barcode Scanner Tip Banner -->
      <div style="margin-top:16px; padding:12px 16px; background:rgba(56,189,248,0.08); border:1px dashed rgba(56,189,248,0.3); border-radius:14px; display:flex; align-items:center; gap:12px;">
        <i class="fa-solid fa-barcode" style="font-size:24px; color:#38BDF8;"></i>
        <div style="font-size:12px; color:var(--text-primary); line-height:1.5;">
          <b>Hardware Barcode Scanner Supported:</b> Just scan any barcode anytime! The system automatically detects scanner keystrokes and instantly adds the matching item to your cart.
        </div>
      </div>
    </div>
    <div class="modal-footer" style="padding:14px 22px; border-top:1px solid var(--border); display:flex; justify-content:flex-end;">
      <button class="btn-checkout" onclick="closeModal('shortcutsModal')" style="height:36px; padding:0 20px; font-size:12.5px;">Got It!</button>
    </div>
  </div>
</div>

<!-- Admin Module Iframe Modal -->
<div class="modal-overlay" id="adminModuleModal">
  <div class="modal" style="width:96vw; max-width:1240px; height:92vh; max-height:860px; display:flex; flex-direction:column; padding:0; border-radius:20px; overflow:hidden; background:var(--card); border:1px solid var(--border); box-shadow:0 24px 60px rgba(0,0,0,0.6);">
    <div class="modal-header" style="padding:14px 22px; background:var(--card); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
      <div class="modal-title" id="adminModuleModalTitle" style="font-size:16px; font-weight:700; color:#fff; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-layer-group" style="color:var(--primary);"></i> Module View
      </div>
      <div style="display:flex; gap:12px; align-items:center;">
        <a id="adminModuleExternalLink" href="#" target="_blank" style="color:var(--primary); font-size:12px; font-weight:600; text-decoration:none; display:flex; align-items:center; gap:6px; background:var(--primary-light); padding:6px 14px; border-radius:8px; transition:all 0.2s;">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Open in New Tab
        </a>
        <button class="modal-close" type="button" onclick="closeModal('adminModuleModal')"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <div class="modal-body" style="padding:0; flex:1; overflow-y:auto; -webkit-overflow-scrolling:touch; background:#090D1A;">
      <iframe id="adminModuleIframe" src="" style="width:100%; height:100%; min-height:100%; border:none; background:#090D1A;"></iframe>
    </div>

  </div>
</div>

<!-- Print Hub Modal -->
<div class="modal-overlay" id="printHubModal">
  <div class="modal print-hub-popup" style="width:400px; max-width:94%; max-height:88vh; background:rgba(30,41,59,0.95); backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.1); box-shadow:0 24px 60px rgba(0,0,0,0.6); border-radius:24px; padding:0; overflow:hidden; display:flex; flex-direction:column;">
    <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,0.06); padding:14px 20px; display:flex; align-items:center; justify-content:space-between; width: 100%; flex-shrink:0;">
      <div class="modal-title" style="color:#34D399; font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; background:rgba(52,211,153,0.15); box-shadow:0 0 12px rgba(52,211,153,0.25);"><i class="fa-solid fa-check" style="font-size:12px;"></i></span>
        Order Placed Successfully!
      </div>
      <button class="modal-close" onclick="closeModal('printHubModal')" style="color:var(--text-secondary); opacity:0.6; transition:opacity .18s; cursor:pointer;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.6"><i class="fa-solid fa-xmark"></i></button>
    </div>
    
    <div class="modal-body" style="text-align:center; padding:18px 20px; overflow-y:auto; flex:1; scrollbar-width:thin;">
      <!-- Glowing Icon Wrapper -->
      <div style="position:relative; width:64px; height:64px; margin:0 auto 10px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, rgba(16,185,129,0.12), rgba(5,150,105,0.12)); border:1px solid rgba(16,185,129,0.25); border-radius:50%; box-shadow:0 0 24px rgba(16,185,129,0.2);">
        <i class="fa-solid fa-print" style="font-size:30px; color:#34D399; text-shadow:0 0 10px rgba(52,211,153,0.4);"></i>
      </div>
      
      <h3 style="font-size:17px; font-weight:800; color:#F8FAFC; margin-bottom:2px; font-family:'Outfit',sans-serif; letter-spacing:0.3px;" id="printHubOrderId">Order ID: #--</h3>
      <p style="font-size:12px; color:#94A3B8; margin-bottom:16px; font-weight:500;">Choose tickets to print for this order.</p>
      
      <div style="display:flex; flex-direction:column; gap:10px;">
        <!-- Print All Tickets -->
        <button onclick="triggerPrintHubAction('all')" class="ph-btn" style="width:100%; height:42px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #10B981, #059669); color:#FFFFFF; font-size:13.5px; font-weight:700; cursor:pointer; box-shadow:0 4px 15px rgba(16,185,129,0.3); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16,185,129,0.45)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(16,185,129,0.3)';">
          <i class="fa-solid fa-print" style="font-size:14px;"></i> Print All Tickets
        </button>

        <!-- Print KOTs Only -->
        <button id="printHubKOTsOnlyBtn" onclick="triggerPrintHubAction('kots_only')" class="ph-btn" style="width:100%; height:42px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #F59E0B, #D97706); color:#FFFFFF; font-size:13.5px; font-weight:700; cursor:pointer; box-shadow:0 4px 15px rgba(245,158,11,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(245,158,11,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(245,158,11,0.25)';">
          <i class="fa-solid fa-fire-burner" style="font-size:14px;"></i> Print KOTs Only (Kitchen + Bar)
        </button>
        
        <div style="display:flex; align-items:center; justify-content:center; margin:4px 0;">
          <div style="flex:1; height:1px; background:rgba(255,255,255,0.06);"></div>
          <span style="font-size:10px; font-weight:700; text-transform:uppercase; color:#64748B; letter-spacing:1px; padding:0 10px;">or print individual</span>
          <div style="flex:1; height:1px; background:rgba(255,255,255,0.06);"></div>
        </div>

        <!-- Receipt -->
        <button onclick="triggerPrintHubAction('receipt')" class="ph-btn" style="width:100%; height:38px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#FFFFFF; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 12px rgba(99,102,241,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(99,102,241,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(99,102,241,0.25)';">
          <i class="fa-solid fa-file-invoice-dollar" style="font-size:13px;"></i> Print Thermal Receipt
        </button>

        <!-- Designed A4 Tax Bill -->
        <button onclick="printA4Invoice()" class="ph-btn" style="width:100%; height:40px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #8B5CF6, #6366F1); color:#FFFFFF; font-size:13px; font-weight:700; cursor:pointer; box-shadow:0 4px 15px rgba(139,92,246,0.35); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(139,92,246,0.5)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(139,92,246,0.35)';">
          <i class="fa-solid fa-file-pdf" style="font-size:14px; color:#FCD34D;"></i> Print Designed A4 Tax Bill
        </button>
        
        <!-- Kitchen KOT -->
        <button id="printHubKitchenBtn" onclick="triggerPrintHubAction('kitchen')" class="ph-btn" style="width:100%; height:38px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #F59E0B, #D97706); color:#FFFFFF; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 12px rgba(245,158,11,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(245,158,11,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.25)';">
          <i class="fa-solid fa-fire-burner" style="font-size:13px;"></i> Print Kitchen KOT
        </button>
        
        <!-- Bar KOT -->
        <button id="printHubBarBtn" onclick="triggerPrintHubAction('bar')" class="ph-btn" style="width:100%; height:38px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #06B6D4, #0891B2); color:#FFFFFF; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 12px rgba(6,182,212,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(6,182,212,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(6,182,212,0.25)';">
          <i class="fa-solid fa-glass-water" style="font-size:13px;"></i> Print Bar KOT
        </button>
      </div>
    </div>
    <div class="modal-footer" style="justify-content:center; border-top:1px solid rgba(255,255,255,0.06); padding:14px 20px; background:rgba(0,0,0,0.15); flex-shrink:0;">
      <button class="btn-outline" style="min-width:140px; height:38px; border:1px solid rgba(255,255,255,0.15); background:rgba(255,255,255,0.08); color:#FFFFFF; font-weight:700; border-radius:12px; font-size:13px; flex:none; transition:all 0.2s; cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)';" onmouseout="this.style.background='rgba(255,255,255,0.08)';" onclick="closeModal('printHubModal')">Done</button>
    </div>
  </div>
</div>

<!-- Live UPI Payment QR Code Modal (Ultra-Premium Redesign) -->
<div class="modal-overlay" id="upiQrModal">
  <div class="modal" style="width:440px; max-width:95%; max-height:92vh; display:flex; flex-direction:column; background:var(--card); color:var(--text-primary); border-radius:24px; border:1px solid rgba(16,185,129,0.3); box-shadow:0 25px 60px rgba(0,0,0,0.8), 0 0 30px rgba(16,185,129,0.15); overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg, #064E3B, #022C22); border-bottom:1px solid rgba(16,185,129,0.3); padding:14px 20px; flex-shrink:0;">
      <div class="modal-title" style="color:#A7F3D0; font-size:16px; font-weight:800; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-qrcode" style="font-size:22px; color:#34D399;"></i>
        Live UPI Payment Terminal
      </div>
      <button class="modal-close" onclick="closeModal('upiQrModal')" style="color:#94A3B8;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    
    <div class="modal-body" style="padding:16px 20px; text-align:center; overflow-y:auto; flex:1;">
      <!-- Accepted Payment Apps Row -->
      <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:10px; font-size:11px; color:var(--text-muted); background:var(--bg); padding:6px 12px; border-radius:20px; border:1px solid var(--border);">
        <span style="font-weight:700; color:#10B981;">Accepts:</span>
        <span style="display:inline-flex; align-items:center; gap:4px; font-weight:700; color:var(--text-primary);"><i class="fa-brands fa-google-pay" style="color:#4285F4; font-size:14px;"></i> GPay</span>
        <span>•</span>
        <span style="font-weight:700; color:var(--text-primary);">PhonePe</span>
        <span>•</span>
        <span style="font-weight:700; color:var(--text-primary);">Paytm</span>
        <span>•</span>
        <span style="font-weight:700; color:var(--text-primary);">BHIM UPI</span>
      </div>

      <div style="font-size:12px; font-weight:700; color:var(--text-muted); margin-bottom:2px;" id="upiModalTable">Table 1</div>
      <div style="font-size:26px; font-weight:900; color:#10B981; font-family:'Outfit',sans-serif; letter-spacing:-0.5px;" id="upiModalTotal">₹0.00</div>
      <div style="font-size:11px; color:var(--text-muted); margin-bottom:10px;">Scan with any UPI app to pay instantly</div>

      <!-- High Res Live UPI QR Code Container with Glowing Effect -->
      <div style="position:relative; display:inline-block; margin-bottom:10px;">
        <div style="background:#FFFFFF; padding:10px; border-radius:18px; box-shadow:0 10px 25px rgba(0,0,0,0.15), 0 0 20px rgba(16,185,129,0.3); border:3px solid #10B981; position:relative; z-index:2;">
          <img id="upiQrImg" src="" alt="UPI Payment QR Code" style="width:160px; height:160px; display:block; border-radius:8px;" />
        </div>
      </div>

      <!-- Receiver Merchant VPA Handle Input -->
      <div id="merchantVpaRow" style="margin-bottom:10px; display:flex; align-items:center; justify-content:center; gap:6px; font-size:11.5px; color:var(--text-muted); background:var(--bg); padding:6px 12px; border-radius:12px; border:1px solid var(--border);">
        <i class="fa-solid fa-building-columns" style="color:#10B981;"></i>
        <span style="font-weight:700; color:var(--text-primary);">UPI ID:</span>
        <input type="text" id="merchantVpaInput" value="7777938338@omni" onchange="updateUpiQrCodeFromInput()" onkeyup="updateUpiQrCodeFromInput()" style="background:var(--card); color:var(--text-primary); border:1px solid var(--border); padding:4px 8px; border-radius:8px; font-weight:800; font-size:12px; width:170px; text-align:center; outline:none;" placeholder="7777938338@omni" />
        <button onclick="copyUpiVpa()" style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#10B981; padding:3px 7px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer;" title="Copy UPI ID"><i class="fa-regular fa-copy"></i></button>
      </div>

      <!-- Itemized Price Breakdown Table -->
      <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:10px 14px; font-size:12px; color:var(--text-primary);">
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
          <span style="color:var(--text-muted);">Subtotal:</span>
          <span id="upiModalSubtotal" style="font-weight:700; color:var(--text-primary);">₹0.00</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
          <span style="color:var(--text-muted);">Service Charge (5%):</span>
          <span id="upiModalService" style="font-weight:700; color:var(--text-primary);">₹0.00</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:var(--text-muted);">GST (18%):</span>
          <span id="upiModalGst" style="font-weight:700; color:var(--text-primary);">₹0.00</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-top:1px dashed var(--border); padding-top:6px; font-weight:900; color:#10B981; font-size:14px;">
          <span>Total Payable Amount:</span>
          <span id="upiModalBreakdownTotal">₹0.00</span>
        </div>
      </div>
    </div>

    <div class="modal-footer" style="padding:12px 20px; background:var(--card); border-top:1px solid var(--border); display:flex; gap:10px; align-items:center; flex-shrink:0;">
      <button class="btn-outline" onclick="closeModal('upiQrModal')" style="flex:1; padding:8px; font-weight:700; border-radius:10px; height:40px; font-size:12.5px;">Cancel</button>
      <button onclick="confirmUpiPaymentCompleted()" class="btn-checkout" style="flex:2; padding:8px 14px; font-size:12.5px; font-weight:800; border-radius:12px; background:linear-gradient(135deg, #10B981, #059669); color:#fff; display:flex; align-items:center; justify-content:center; gap:6px; border:none; cursor:pointer; height:40px; box-shadow:0 4px 16px rgba(16,185,129,0.4);">
        <i class="fa-solid fa-volume-high" style="font-size:14px;"></i> Verify &amp; Announce Voice 🔊
      </button>
    </div>
  </div>
</div>

<!-- Item Preference & Cooking Note Customizer Modal -->
<div id="posItemNoteModal" class="modal-overlay" style="display:none; align-items:center; justify-content:center; z-index:999999; backdrop-filter:blur(6px);">
  <div class="modal-dialog" style="background:var(--card); color:var(--text-primary); width:420px; max-width:92vw; border-radius:20px; border:1px solid var(--border); padding:22px; box-shadow:var(--shadow-lg);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <div>
        <h3 id="noteModalItemName" style="font-family:'Outfit',sans-serif; font-size:18px; font-weight:800; color:var(--text-primary);">Customize Item</h3>
        <p style="font-size:11.5px; color:var(--text-secondary); margin-top:2px;">Select cooking preference tags or add specific instructions</p>
      </div>
      <button onclick="closeModal('posItemNoteModal')" style="background:none; border:none; color:var(--text-muted); font-size:22px; cursor:pointer; padding:0 4px;">&times;</button>
    </div>
    <input type="hidden" id="noteModalItemId" value="">
    
    <!-- Quick Preference Tag Chips -->
    <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:8px;">Quick Cooking Preference Tags:</label>
    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px;">
      <button type="button" class="tag-chip-btn" onclick="addNoteTag('🌶️ Extra Spicy')">🌶️ Extra Spicy</button>
      <button type="button" class="tag-chip-btn" onclick="addNoteTag('🧅 No Onion/Garlic')">🧅 No Onion/Garlic</button>
      <button type="button" class="tag-chip-btn" onclick="addNoteTag('🧀 Extra Cheese')">🧀 Extra Cheese</button>
      <button type="button" class="tag-chip-btn" onclick="addNoteTag('🌾 Less Oil / Jain')">🌾 Less Oil / Jain</button>
      <button type="button" class="tag-chip-btn" onclick="addNoteTag('🧊 Extra Ice')">🧊 Extra Ice</button>
      <button type="button" class="tag-chip-btn" onclick="addNoteTag('📦 Pack Separately')">📦 Pack Separately</button>
    </div>

    <!-- Custom Note Input Textarea -->
    <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Custom Instructions / Specific Note:</label>
    <textarea id="noteModalInput" rows="3" placeholder="e.g. Make it extra spicy, sauce on the side..." style="width:100%; border-radius:12px; border:1px solid var(--border); background:var(--bg); color:var(--text-primary); padding:10px; font-size:13px; font-family:inherit; outline:none; resize:none; margin-bottom:18px;"></textarea>

    <div style="display:flex; gap:10px;">
      <button onclick="clearItemNote()" style="flex:1; height:42px; border-radius:12px; border:1px solid var(--border); background:none; color:var(--text-secondary); font-weight:700; cursor:pointer; font-size:12.5px;">Clear Note</button>
      <button onclick="saveItemNote()" style="flex:2; height:42px; border-radius:12px; border:none; background:linear-gradient(135deg,#6366F1,#4F46E5); color:#fff; font-weight:800; cursor:pointer; box-shadow:0 4px 14px rgba(99,102,241,0.35); font-size:13px;">Save Preference</button>
    </div>
  </div>
</div>

<!-- Menu Item Modifiers & Add-ons Selection Modal -->
<div id="posItemModifierModal" class="modal-overlay" style="display:none; align-items:center; justify-content:center; z-index:999999; backdrop-filter:blur(6px);">
  <div class="modal-dialog" style="background:var(--card); color:var(--text-primary); width:480px; max-width:94vw; max-height:85vh; border-radius:20px; border:1px solid var(--border); padding:22px; box-shadow:var(--shadow-lg); display:flex; flex-direction:column;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <div>
        <h3 id="modModalItemName" style="font-family:'Outfit',sans-serif; font-size:18px; font-weight:800; color:var(--text-primary); margin:0;">Customize Dish</h3>
        <p id="modModalItemBasePrice" style="font-size:12px; color:var(--text-secondary); margin:3px 0 0 0;">Base Price: ₹0</p>
      </div>
      <button onclick="closeModal('posItemModifierModal')" style="background:none; border:none; color:var(--text-muted); font-size:22px; cursor:pointer; padding:0 4px;">&times;</button>
    </div>
    <input type="hidden" id="modModalItemId" value="">

    <!-- Modifier Groups Container (Scrollable) -->
    <div id="modModalGroupsContainer" style="overflow-y:auto; flex:1; padding-right:6px; margin-bottom:16px;">
      <div style="text-align:center; padding:30px; color:var(--text-muted);">
        <i class="fa-solid fa-spinner fa-spin" style="font-size:24px;"></i>
        <p style="margin-top:8px; font-size:12px;">Loading options & add-ons...</p>
      </div>
    </div>

    <!-- Modal Footer with Total & Add to Cart -->
    <div style="border-top:1px solid var(--border); padding-top:14px; display:flex; justify-content:space-between; align-items:center; gap:12px;">
      <div>
        <span style="font-size:11px; color:var(--text-muted); display:block;">Total Line Price</span>
        <span id="modModalCalculatedPrice" style="font-size:19px; font-weight:800; color:var(--primary);">₹ 0</span>
      </div>
      <button type="button" onclick="applyModifiersAndAddToCart()" style="height:44px; padding:0 22px; border-radius:12px; border:none; background:linear-gradient(135deg,#6366F1,#4F46E5); color:#fff; font-weight:800; cursor:pointer; box-shadow:0 4px 14px rgba(99,102,241,0.35); font-size:13.5px; display:flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-plus"></i> Add to Order
      </button>
    </div>
  </div>
</div>

<!-- Staff Attendance & Shift Clock-In/Out Modal -->
<div id="posStaffAttendanceModal" class="modal-overlay" style="display:none; align-items:center; justify-content:center; z-index:999999; backdrop-filter:blur(6px);">
  <div class="modal-dialog" style="background:var(--card); color:var(--text-primary); width:520px; max-width:94vw; max-height:85vh; border-radius:20px; border:1px solid var(--border); padding:22px; box-shadow:var(--shadow-lg); display:flex; flex-direction:column;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:10px; background:rgba(16,185,129,0.15); color:#10B981; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <i class="fa-solid fa-user-clock"></i>
        </div>
        <div>
          <h3 style="font-family:'Outfit',sans-serif; font-size:18px; font-weight:800; color:var(--text-primary); margin:0;">Staff Shift Attendance</h3>
          <p style="font-size:11.5px; color:var(--text-secondary); margin:2px 0 0 0;">Clock-in, Clock-out & Real-Time Shift Tracking</p>
        </div>
      </div>
      <button onclick="closeModal('posStaffAttendanceModal')" style="background:none; border:none; color:var(--text-muted); font-size:22px; cursor:pointer; padding:0 4px;">&times;</button>
    </div>

    <!-- Quick Action Card -->
    <div style="background:var(--bg); border:1px solid var(--border); border-radius:14px; padding:14px; margin-bottom:16px;">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
        <div>
          <label style="font-size:11px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:4px;">Select Staff Member</label>
          <select id="attendanceStaffSelect" style="width:100%; height:38px; border-radius:8px; border:1px solid var(--border); background:var(--card); color:var(--text-primary); padding:0 10px; font-size:12.5px; outline:none;">
            <option value="">Select staff member...</option>
          </select>
        </div>
        <div>
          <label style="font-size:11px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:4px;">Shift Slot</label>
          <select id="attendanceShiftSelect" style="width:100%; height:38px; border-radius:8px; border:1px solid var(--border); background:var(--card); color:var(--text-primary); padding:0 10px; font-size:12.5px; outline:none;">
            <option value="REGULAR">Regular Shift (8h)</option>
            <option value="MORNING">Morning Shift</option>
            <option value="EVENING">Evening Shift</option>
            <option value="NIGHT">Night Shift</option>
          </select>
        </div>
      </div>
      <div style="display:flex; gap:10px;">
        <button type="button" onclick="performStaffClockIn()" style="flex:1; height:40px; border-radius:10px; border:none; background:#10B981; color:#fff; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; font-size:13px; box-shadow:0 3px 10px rgba(16,185,129,0.3);">
          <i class="fa-solid fa-right-to-bracket"></i> Clock In
        </button>
        <button type="button" onclick="performStaffClockOut()" style="flex:1; height:40px; border-radius:10px; border:none; background:#EF4444; color:#fff; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; font-size:13px; box-shadow:0 3px 10px rgba(239,68,68,0.3);">
          <i class="fa-solid fa-right-from-bracket"></i> Clock Out
        </button>
      </div>
    </div>

    <!-- Today's Attendance Table -->
    <h4 style="font-size:12px; font-weight:800; color:var(--text-secondary); margin:0 0 8px 0; text-transform:uppercase; letter-spacing:0.5px;">Today's On-Duty Staff</h4>
    <div id="attendanceTodayListContainer" style="overflow-y:auto; flex:1; max-height:220px; border:1px solid var(--border); border-radius:12px; background:var(--card);">
      <div style="padding:15px; text-align:center; color:var(--text-muted); font-size:12px;">Loading attendance records...</div>
    </div>
  </div>
</div>

<!-- Direct Silent Thermal Printer Configuration Modal -->
<div id="posPrinterConfigModal" class="modal-overlay" style="display:none; align-items:center; justify-content:center; z-index:999999; backdrop-filter:blur(6px);">
  <div class="modal-dialog" style="background:var(--card); color:var(--text-primary); width:460px; max-width:92vw; border-radius:20px; border:1px solid var(--border); padding:22px; box-shadow:var(--shadow-lg);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid var(--border); padding-bottom:12px;">
      <div style="display:flex; align-items:center; gap:10px;">
        <div style="width:36px; height:36px; border-radius:10px; background:rgba(99,102,241,0.15); color:#6366F1; display:flex; align-items:center; justify-content:center; font-size:16px;">
          <i class="fa-solid fa-print"></i>
        </div>
        <div>
          <h3 style="font-family:'Outfit',sans-serif; font-size:18px; font-weight:800; color:var(--text-primary); margin:0;">Thermal Printer Driver</h3>
          <p style="font-size:11.5px; color:var(--text-secondary); margin:2px 0 0 0;">Direct Silent ESC/POS (0.1s Fast Print)</p>
        </div>
      </div>
      <button onclick="closeModal('posPrinterConfigModal')" style="background:none; border:none; color:var(--text-muted); font-size:22px; cursor:pointer; padding:0 4px;">&times;</button>
    </div>

    <div style="margin-bottom:14px;">
      <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Printing Mode:</label>
      <select id="escposPrinterModeSelect" onchange="togglePrinterModeInputs()" style="width:100%; height:40px; border-radius:10px; border:1px solid var(--border); background:var(--bg); color:var(--text-primary); padding:0 10px; font-size:13px; outline:none;">
        <option value="browser">Standard Browser Print (Dialog Popup)</option>
        <option value="usb">Direct WebUSB Thermal Printer (Silent ESC/POS)</option>
        <option value="serial">Direct WebSerial (COM / RS232 Port)</option>
        <option value="network">Network IP Thermal Printer (LAN Socket 9100)</option>
      </select>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:14px;">
      <div>
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Paper Width:</label>
        <select id="escposPaperWidthSelect" style="width:100%; height:40px; border-radius:10px; border:1px solid var(--border); background:var(--bg); color:var(--text-primary); padding:0 10px; font-size:13px; outline:none;">
          <option value="80">80 mm (3 Inch - Standard)</option>
          <option value="58">58 mm (2 Inch - Compact)</option>
        </select>
      </div>
      <div id="escposIpGroup" style="display:none;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Printer IP Address:</label>
        <input type="text" id="escposPrinterIpInput" placeholder="192.168.1.100" style="width:100%; height:40px; border-radius:10px; border:1px solid var(--border); background:var(--bg); color:var(--text-primary); padding:0 10px; font-size:13px; outline:none;">
      </div>
    </div>

    <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px; margin-bottom:18px; font-size:11.5px; color:var(--text-secondary); line-height:1.5;">
      <i class="fa-solid fa-lightbulb" style="color:#F59E0B; margin-right:4px;"></i>
      Direct printing sends native ESC/POS thermal commands directly to Epson, TVS, Star, Xprinter, and Posiflex printers without opening browser preview.
    </div>

    <div style="display:flex; gap:10px;">
      <button type="button" onclick="testThermalPrinter()" style="flex:1; height:42px; border-radius:12px; border:1px solid var(--border); background:none; color:var(--text-primary); font-weight:700; cursor:pointer; font-size:12.5px; display:flex; align-items:center; justify-content:center; gap:6px;">
        <i class="fa-solid fa-vial"></i> Test Print
      </button>
      <button type="button" onclick="savePrinterSettings()" style="flex:1.5; height:42px; border-radius:12px; border:none; background:linear-gradient(135deg,#6366F1,#4F46E5); color:#fff; font-weight:800; cursor:pointer; font-size:13px; box-shadow:0 4px 14px rgba(99,102,241,0.35);">
        Save Configuration
      </button>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Mobile cart FAB + backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeMobileSidebar()"></div>
<div class="cart-backdrop" id="cartBackdrop" onclick="toggleMobileCart()"></div>

<script>
  const RESTAURANT_ID = '{{ $restaurantId ?? (Auth::user()?->restaurant_id ?? '') }}';
  const SERVER_MENU_ITEMS = @json($menuItems ?? []);

  function escapeHtml(str) {
      if (!str) return '';
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
  }

  function escapeAttr(str) {
      if (str === null || str === undefined) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
  }

  let selectedCustomerData = { name: 'Rahul Kumar', phone: '+91 98765 43210', points: 0, avatar: 'RK' };
  let currentOrderType = 'dinein';

  // ─── AI VOICE-TO-ORDER ENGINE FOR POS ───
  let posVoiceRecognition = null;
  let isPosVoiceListening = false;

  function togglePosVoiceOrder() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
      alert('Speech Recognition is not supported by your browser. Please use Chrome.');
      return;
    }

    const btn = document.getElementById('posVoiceBtn');
    const input = document.getElementById('posSearchInput');

    if (isPosVoiceListening) {
      if (posVoiceRecognition) posVoiceRecognition.stop();
      isPosVoiceListening = false;
      if (btn) {
        btn.style.background = 'rgba(99,102,241,0.15)';
        btn.style.color = 'var(--primary)';
      }
      if (input) input.placeholder = 'Search items or tap mic to speak…';
      return;
    }

    posVoiceRecognition = new SpeechRecognition();
    posVoiceRecognition.continuous = false;
    posVoiceRecognition.interimResults = false;
    posVoiceRecognition.lang = 'gu-IN';

    posVoiceRecognition.onstart = () => {
      isPosVoiceListening = true;
      if (btn) {
        btn.style.background = '#EF4444';
        btn.style.color = '#FFFFFF';
      }
      if (input) input.placeholder = '🎙️ Listening... (બોલો જેમ કે: "બે પિઝા ઉમેરો")';
    };

    posVoiceRecognition.onresult = (e) => {
      const transcript = e.results[0][0].transcript.toLowerCase();
      if (input) input.value = transcript;
      processPosVoiceCommand(transcript);
    };

    posVoiceRecognition.onerror = () => {
      if (btn) {
        btn.style.background = 'rgba(99,102,241,0.15)';
        btn.style.color = 'var(--primary)';
      }
      if (input) input.placeholder = 'Search items or tap mic to speak…';
      isPosVoiceListening = false;
    };

    posVoiceRecognition.onend = () => {
      if (btn) {
        btn.style.background = 'rgba(99,102,241,0.15)';
        btn.style.color = 'var(--primary)';
      }
      if (input) input.placeholder = 'Search items or tap mic to speak…';
      isPosVoiceListening = false;
    };

    posVoiceRecognition.start();
  }

  function processPosVoiceCommand(text) {
    if (!MENU || !MENU.length) return;
    
    let qty = 1;
    const numMap = { 'એક':1, 'બે':2, 'ત્રણ':3, 'ચાર':4, 'પાંચ':5, 'एक':1, 'दो':2, 'तीन':3, 'चार':4, 'पाँच':5, 'one':1, 'two':2, 'three':3, 'four':4, 'five':5 };
    for (const [k, v] of Object.entries(numMap)) {
      if (text.includes(k)) { qty = v; break; }
    }
    const digitMatch = text.match(/\b\d+\b/);
    if (digitMatch) qty = parseInt(digitMatch[0]);

    let matchedItem = null;
    for (const item of MENU) {
      const cleanName = item.name.toLowerCase();
      if (text.includes(cleanName) || cleanName.split(' ').some(word => word.length > 3 && text.includes(word))) {
        matchedItem = item;
        break;
      }
    }

    if (matchedItem) {
      if (matchedItem.is_available === false || matchedItem.available === false) {
        showToast(matchedItem.name + ' is currently Out of Stock!', 'error');
        if ('speechSynthesis' in window) {
          const utter = new SpeechSynthesisUtterance(`${matchedItem.name} is currently out of stock`);
          utter.lang = 'en-IN';
          window.speechSynthesis.speak(utter);
        }
        return;
      }
      for (let i = 0; i < qty; i++) {
        addQuickItem(matchedItem.id);
      }
      if ('speechSynthesis' in window) {
        const utter = new SpeechSynthesisUtterance(`${qty} ${matchedItem.name} added to cart`);
        utter.lang = 'en-IN';
        window.speechSynthesis.speak(utter);
      }
    } else {
      filterItems(text);
    }
  }

  function sendPosWhatsAppBill(orderId, customerPhone, totalAmount) {
    const phone = (customerPhone || '').replace(/[^0-9]/g, '');
    const billText = encodeURIComponent(
      `🧾 *DineFlow AI — Official Tax Invoice*\n` +
      `----------------------------------------\n` +
      `🔢 Order ID: #${orderId}\n` +
      `💳 Total Paid: ₹${totalAmount}\n` +
      `✅ Payment Status: Completed\n\n` +
      `🙏 Thank you for dining with us! We hope to serve you again.\n` +
      `⭐ Rate your experience: https://dineflow.ai/feedback`
    );
    if (phone) {
      window.open(`https://api.whatsapp.com/send?phone=91${phone}&text=${billText}`, '_blank');
    } else {
      window.open(`https://api.whatsapp.com/send?text=${billText}`, '_blank');
    }
  }

  // ─── CORE MODAL UTILITIES & ADMIN MODULE LAUNCHERS ───
  function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.style.display = 'flex';
      modal.style.opacity = '1';
      modal.style.pointerEvents = 'all';
      modal.classList.add('show');
      if (id === 'tableModal' && typeof loadTablesData === 'function') loadTablesData();
      if (id === 'ordersModal' && typeof loadOrdersData === 'function') loadOrdersData();
      if (id === 'customerModal' && typeof fetchAndRenderPosCustomers === 'function') fetchAndRenderPosCustomers();
      if (id === 'inventoryModal' && typeof openInventoryQuickModal === 'function') openInventoryQuickModal();
      if (id === 'catPopupModal') { const btn = document.getElementById('catPopupBtn'); if (btn) btn.classList.add('open'); }
      if (id === 'posOffersModal') {
        const ptsEl = document.getElementById('modalCustPointsCount');
        if (ptsEl) ptsEl.textContent = (typeof selectedCustomerData !== 'undefined' && selectedCustomerData && selectedCustomerData.points) ? selectedCustomerData.points : 0;
        fetchAndRenderDynamicPosOffers();
      }
    }
  }

  function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
      modal.classList.remove('show');
      modal.style.opacity = '0';
      modal.style.pointerEvents = 'none';
      setTimeout(() => {
        if (!modal.classList.contains('show')) {
          modal.style.display = 'none';
        }
      }, 200);
      if (id === 'catPopupModal') { const btn = document.getElementById('catPopupBtn'); if (btn) btn.classList.remove('open'); }
    }
  }


  function openAdminModule(url, titleHtml) {
    try {
      const modal = document.getElementById('adminModuleModal');
      const titleEl = document.getElementById('adminModuleModalTitle');
      const iframeEl = document.getElementById('adminModuleIframe');
      const extEl = document.getElementById('adminModuleExternalLink');
      if (titleEl) titleEl.innerHTML = titleHtml;
      if (iframeEl) iframeEl.src = url;
      if (extEl) extEl.href = url;
      if (modal) {
        modal.classList.add('show');
      } else {
        window.location.href = url;
      }
    } catch(err) {
      console.error('Error opening module:', err);
      window.location.href = url;
    }
  }

  function openMenuManager() {
    openAdminModule('/admin/menu-manager', '<i class="fa-solid fa-utensils" style="color:var(--primary);"></i> 🍽️ Menu Items Management');
  }
  function openInventory() {
    openAdminModule('/admin/inventory', '<i class="fa-solid fa-box-open" style="color:var(--primary);"></i> 📦 Raw Ingredient Inventory');
  }
  function openCoupons() {
    openAdminModule('/admin/coupons', '<i class="fa-solid fa-ticket" style="color:var(--primary);"></i> 🎟️ Promo Coupons');
  }
  function openCustomers() {
    openAdminModule('/admin/customers', '<i class="fa-solid fa-users" style="color:var(--primary);"></i> 👥 Customer Directory');
  }
  function openZReport() {
    openAdminModule('/admin/sales-report', '<i class="fa-solid fa-chart-line" style="color:var(--primary);"></i> 📊 Sales Analytics & Reports');
  }
  function openFeedbackModule() {
    openAdminModule('/admin/feedback', '<i class="fa-solid fa-comment-dots" style="color:var(--primary);"></i> ⭐ Reviews & Feedback');
  }
  function openTableManager() {
    openAdminModule('/admin/tables', '<i class="fa-solid fa-table-cells-large" style="color:var(--primary);"></i> 🪑 Table Manager & QR Generator');
  }


  // ─── MULTI-CURRENCY GLOBAL ENGINE ───
  const CURRENCY_MAP = {
    'INR': { symbol: '₹', code: 'INR' },
    'USD': { symbol: '$', code: 'USD' },
    'AUD': { symbol: '$', code: 'AUD' },
    'EUR': { symbol: '€', code: 'EUR' },
    'GBP': { symbol: '£', code: 'GBP' },
    'AED': { symbol: 'د.إ', code: 'AED' },
    'CAD': { symbol: '$', code: 'CAD' }
  };
  let currentPosCurrency = localStorage.getItem('dineflow_currency') || 'INR';

  function getCurrencySymbol() {
    if (currentPosCurrency === 'CUSTOM') {
      return localStorage.getItem('dineflow_custom_currency_symbol') || '$';
    }
    return (CURRENCY_MAP[currentPosCurrency] || CURRENCY_MAP['INR']).symbol;
  }

  function saveCustomCurrency() {
    const code = (document.getElementById('customCurrencyCodeInput')?.value || 'CUSTOM').trim().toUpperCase();
    const symbol = (document.getElementById('customCurrencySymbolInput')?.value || '$').trim();
    if (!symbol) {
      showToast('Please enter a currency symbol!', 'error');
      return;
    }
    currentPosCurrency = 'CUSTOM';
    localStorage.setItem('dineflow_currency', 'CUSTOM');
    localStorage.setItem('dineflow_custom_currency_symbol', symbol);
    localStorage.setItem('dineflow_custom_currency_code', code);

    const sel = document.getElementById('posCurrencySelect');
    if (sel) sel.value = 'CUSTOM';

    closeModal('posCustomCurrencyModal');
    renderMenu();
    renderCart();
    showToast(`Custom currency set: ${code} (${symbol})`, 'success');
  }

  function changePosCurrency(newCurr) {
    if (newCurr === 'CUSTOM') {
      openModal('posCustomCurrencyModal');
      return;
    }
    if (!CURRENCY_MAP[newCurr]) return;
    currentPosCurrency = newCurr;
    localStorage.setItem('dineflow_currency', newCurr);
    
    const sel = document.getElementById('posCurrencySelect');
    if (sel) sel.value = newCurr;

    renderMenu();
    renderCart();
    showToast(`Currency changed to ${newCurr} (${getCurrencySymbol()})`, 'success');
  }

  // ─── VOCAL SOUNDBOX STATE & HELPER ───
  let isPosVoiceMuted = localStorage.getItem('dineflow_voice_muted') === 'true';

  let announcedOrderIds = JSON.parse(localStorage.getItem('dineflow_announced_orders') || '[]').map(String);

  let lastVoiceAnnounceTime = 0;

  function announcePaymentVoice(tableNumber, amount, method = 'online') {
    if (isPosVoiceMuted) return;

    const now = Date.now();
    if (tableNumber !== 'Test' && (now - lastVoiceAnnounceTime < 3500)) {
      console.log('Voice announcement debounced to prevent duplicate speech');
      return;
    }
    lastVoiceAnnounceTime = now;

    if (!('speechSynthesis' in window)) {
      console.log('Text-to-speech not supported in this browser.');
      return;
    }
    
    // Clear any stuck/queued audio speech synthesis
    window.speechSynthesis.cancel();
    
    let cleanTableNum = String(tableNumber).replace(/table/gi, '').replace(/#/g, '').replace(/T-/gi, '').trim();
    if (!cleanTableNum || cleanTableNum === 'Walk-In') cleanTableNum = '1';

    const lang = localStorage.getItem('dineflow_restaurant_lang') || localStorage.getItem('dineflow_soundbox_lang') || 'gu';
    let msgText = "";
    let langTag = "gu-IN";

    if (lang === 'hi') {
      langTag = "hi-IN";
      const cashOrOnline = (method === 'cash') ? 'नकद' : 'ऑनलाइन';
      msgText = (tableNumber === 'Test') 
        ? "डाइनफ्लो एआई स्मार्ट साउंडबॉक्स सफलतापूर्वक सक्रिय है." 
        : `डाइनफ्लो एआई पर टेबल नंबर ${cleanTableNum} के ${amount} रुपये ${cashOrOnline} सफलतापूर्वक प्राप्त हुए हैं.`;
    } else if (lang === 'en') {
      langTag = "en-IN";
      const cashOrOnline = (method === 'cash') ? 'Cash' : 'Online';
      msgText = (tableNumber === 'Test') 
        ? "DineFlow AI smart soundbox active successfully." 
        : `DineFlow AI soundbox received ${amount} rupees via ${cashOrOnline} for table ${cleanTableNum}.`;
    } else {
      langTag = "gu-IN";
      const cashOrOnline = (method === 'cash') ? 'રોકડા' : 'ઓનલાઇન';
      msgText = (tableNumber === 'Test') 
        ? "ડાઇનફ્લો એઆઇ સ્માર્ટ સાઉન્ડબોક્સ સફળતાપૂર્વક સક્રિય છે." 
        : `ડાઇનફ્લો એઆઇ પર ટેબલ નંબર ${cleanTableNum} ના ${amount} રૂપિયા ${cashOrOnline} સફળતાપૂર્વક પ્રાપ્ત થયા છે.`;
    }
    
    const utterance = new SpeechSynthesisUtterance(msgText);
    utterance.lang = langTag;
    utterance.rate = 0.95;
    utterance.pitch = 1.0;
    
    const voices = window.speechSynthesis.getVoices();
    if (voices && voices.length > 0) {
      const targetVoice = voices.find(v => v.lang.toLowerCase().includes(lang.toLowerCase())) || 
                           voices.find(v => v.lang.toLowerCase().includes('hi')) ||
                           voices[0];
      if (targetVoice) {
        utterance.voice = targetVoice;
      }
    }
    
    window.speechSynthesis.speak(utterance);
  }



  function announceWaiterCallVoice(tableNumber) {
    if (isPosVoiceMuted) return;
    try {
      const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.connect(gain); gain.connect(audioCtx.destination);
      osc.type = 'sine';
      osc.frequency.setValueAtTime(880, audioCtx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(440, audioCtx.currentTime + 0.15);
      gain.gain.setValueAtTime(0.35, audioCtx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
      osc.start(); osc.stop(audioCtx.currentTime + 0.15);
    } catch (e) {
      console.log('Pop chime error:', e);
    }
  }


  /* ─── DATA & API SYNC ────────────────────────── */
  function normalizeCatName(name) {
    if (!name) return '';
    let s = String(name).trim().toLowerCase();
    s = s.replace(/[\-_]/g, ' ');
    if (s.endsWith('s') && s.length > 3) {
      s = s.slice(0, -1);
    }
    return s;
  }

  function areCategoriesMatching(itemCatRaw, targetCatRaw) {
    if (!targetCatRaw || targetCatRaw.toLowerCase() === 'all') return true;
    if (!itemCatRaw) return false;

    const itemCat = String(itemCatRaw).trim().toLowerCase();
    const targetCat = String(targetCatRaw).trim().toLowerCase();

    if (itemCat === targetCat) return true;

    const nItem = normalizeCatName(itemCat);
    const nTarget = normalizeCatName(targetCat);
    if (nItem === nTarget) return true;

    const synonymGroups = [
      ['drink', 'drinks', 'beverage', 'beverages', 'cold drink', 'cold drinks', 'milk shek', 'shake', 'lassi'],
      ['starter', 'starters', 'appetizer', 'appetizers', 'snack', 'snacks', 'subh sharuat'],
      ['main', 'mains', 'main course', 'curry', 'curries', 'panjabi', 'paneer', 'dal', 'kofta', 'kaju special', 'special cheese'],
      ['bread', 'breads', 'roti', 'rotis', 'naan', 'paratha', 'kulcha'],
      ['dessert', 'desserts', 'sweet', 'sweets', 'ice cream', 'ice creams'],
      ['soup', 'soups'],
      ['rice', 'biryani', 'chinese rice', 'pulao']
    ];

    for (const group of synonymGroups) {
      const hasItem = group.some(g => g === itemCat || g === nItem);
      const hasTarget = group.some(g => g === targetCat || g === nTarget);
      if (hasItem && hasTarget) return true;
    }

    return false;
  }

  function getCatEmoji(category) {
    const c = String(category || '').toLowerCase();
    if (c.includes('soup')) return '🥣';
    if (c.includes('starter') || c.includes('sharuat') || c.includes('appetizer')) return '🥢';
    if (c.includes('paneer') || c.includes('cheese')) return '🧀';
    if (c.includes('panjabi') || c.includes('main') || c.includes('curry')) return '🍛';
    if (c.includes('dal')) return '🫕';
    if (c.includes('roti') || c.includes('bread') || c.includes('naan')) return '🫓';
    if (c.includes('rice') || c.includes('biryani')) return '🍚';
    if (c.includes('sizzler')) return '♨️';
    if (c.includes('drink') || c.includes('beverage') || c.includes('lassi')) return '🥤';
    if (c.includes('shek') || c.includes('shake') || c.includes('milk')) return '🥛';
    if (c.includes('ice cream') || c.includes('dessert') || c.includes('sweet')) return '🍨';
    if (c.includes('backed') || c.includes('bake')) return '🥘';
    if (c.includes('kofta')) return '🥟';
    if (c.includes('kaju')) return '🥜';
    if (c.includes('pentry') || c.includes('salad') || c.includes('papad')) return '🥗';
    return '🍽️';
  }

  function formatCatTitle(str) {
    if (!str) return 'Other';
    return str.toLowerCase().split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
  }

  function transformMenuItem(item) {
    const rawCat = (item.category || item.cat || 'Mains').trim();
    const name = item.name || 'Dish';
    const nameLower = name.toLowerCase();
    const isVeg = (item.is_veg === true || item.is_veg === 1 || item.is_veg === '1' || item.is_veg === 'true' || item.is_veg === null);
    const isAvailable = (item.is_available !== false && item.is_available !== 0 && item.is_available !== '0');

    let emoji = getCatEmoji(rawCat);
    if (nameLower.includes('naan') || nameLower.includes('roti') || nameLower.includes('paratha') || nameLower.includes('kulcha')) emoji = '🫓';
    else if (nameLower.includes('soup')) emoji = '🥣';
    else if (nameLower.includes('lassi') || nameLower.includes('water') || nameLower.includes('soda')) emoji = '🥤';
    else if (nameLower.includes('shake') || nameLower.includes('shek')) emoji = '🥛';
    else if (nameLower.includes('ice cream') || nameLower.includes('kulfi')) emoji = '🍨';
    else if (nameLower.includes('biryani') || nameLower.includes('pulao') || nameLower.includes('rice')) emoji = '🍚';
    else if (nameLower.includes('sizzler')) emoji = '♨️';
    else if (nameLower.includes('dal')) emoji = '🫕';
    else if (nameLower.includes('paneer')) emoji = '🧀';

    let badge = isVeg ? 'veg' : 'nonveg';
    if (!isAvailable) badge = 'out';

    return {
      id: item.id,
      name: name,
      cat: rawCat,
      category: rawCat,
      price: parseFloat(item.price || 0),
      basePrice: parseFloat(item.price || 0),
      image: item.image || '',
      emoji: emoji,
      badge: badge,
      desc: item.description || '',
      qty: 0,
      is_available: isAvailable,
      is_veg: isVeg,
      has_modifiers: Boolean(item.has_modifiers),
      is_combo: Boolean(item.is_combo),
      selected_modifiers: [],
      cookingNote: ''
    };
  }

  function renderSkeleton(count = 12) {
    const grid = document.getElementById('menuGrid');
    if (!grid) return;
    let html = '';
    for (let i = 0; i < count; i++) {
      html += `
        <div class="pos-skeleton-card">
          <div class="pos-skeleton-img"></div>
          <div class="pos-skeleton-body">
            <div class="pos-skeleton-line short"></div>
            <div class="pos-skeleton-line" style="width:85%;"></div>
            <div class="pos-skeleton-line price"></div>
          </div>
        </div>
      `;
    }
    grid.innerHTML = html;
  }

  // POS Menu State & Filter Variables
  let currentCat = 'all';
  let searchTerm = '';
  let currentDietaryFilter = 'all'; // 'all', 'veg', 'nonveg'

  // Pre-seed MENU from server-injected items (zero-delay hydration, no fake items)
  let MENU = Array.isArray(SERVER_MENU_ITEMS) && SERVER_MENU_ITEMS.length > 0
    ? SERVER_MENU_ITEMS.map(transformMenuItem)
    : [];

  function getCats() {
    const catMap = new Map();
    const cats = [
      { key: 'all', label: 'All Items', emoji: '🍽️', count: (MENU || []).length }
    ];

    (MENU || []).forEach(item => {
      const rawCat = (item.category || item.cat || '').trim();
      if (!rawCat) return;
      const upperCat = rawCat.toUpperCase();
      if (!catMap.has(upperCat)) {
        catMap.set(upperCat, {
          key: upperCat,
          label: formatCatTitle(upperCat),
          emoji: getCatEmoji(upperCat),
          count: 0
        });
      }
      catMap.get(upperCat).count++;
    });

    const sortedCats = Array.from(catMap.values()).sort((a, b) => a.label.localeCompare(b.label));
    return cats.concat(sortedCats);
  }

  /* ─── RENDER MENU ────────────────────────── */
  function renderMenu() {
    const grid = document.getElementById('menuGrid');
    if (!grid) return;

    const query = (searchTerm || '').trim().toLowerCase();
    const targetCat = (currentCat || 'all').toLowerCase();

    const items = (MENU || []).filter(i => {
      const matchCat = targetCat === 'all' || areCategoriesMatching(i.category || i.cat, targetCat);
      const matchDiet = currentDietaryFilter === 'all' 
        ? true 
        : (currentDietaryFilter === 'veg' ? (i.badge === 'veg' || i.is_veg === true) : (i.badge === 'nonveg' || i.is_veg === false));
      const matchSearch = !query ||
        (i.name && i.name.toLowerCase().includes(query)) ||
        (i.category && i.category.toLowerCase().includes(query)) ||
        (i.desc && i.desc.toLowerCase().includes(query));
      return matchCat && matchDiet && matchSearch;
    });

    if (items.length === 0) {
      if (!MENU || MENU.length === 0) {
        grid.innerHTML = `
          <div class="pos-empty-state">
            <div class="pos-empty-icon"><i class="fa-solid fa-utensils"></i></div>
            <div class="pos-empty-title">No Menu Items Found</div>
            <div class="pos-empty-sub">This restaurant doesn't have any menu items configured in the system yet.</div>
            <a href="/admin/menu-manager" class="pos-empty-action"><i class="fa-solid fa-plus"></i> Add Menu Item</a>
          </div>`;
      } else {
        grid.innerHTML = `
          <div class="pos-empty-state">
            <div class="pos-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
            <div class="pos-empty-title">No Matching Dishes</div>
            <div class="pos-empty-sub">No dishes found matching "${escapeHtml(searchTerm || currentCat)}". Try clearing your search or category filter.</div>
            <button type="button" class="pos-empty-action" onclick="resetCat(); const si = document.getElementById('posSearchInput'); if (si) si.value=''; filterItems('');"><i class="fa-solid fa-rotate-left"></i> Reset Filter</button>
          </div>`;
      }
      return;
    }

    grid.innerHTML = items.map(item => {
      try {
        const isSoldOut = !item.is_available;
        const isVeg = item.badge === 'veg' || item.is_veg === true;
        const hasImg = item.image && String(item.image).trim().length > 5;
        const cardClass = `item-card ${item.qty > 0 ? 'in-cart' : ''} ${isSoldOut ? 'item-card-unavailable' : ''}`;
        const safeName = escapeAttr(item.name || 'Dish');
        const safeJsName = String(item.name || 'Dish').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const safeId = escapeAttr(String(item.id || ''));
        const clickAction = isSoldOut 
          ? `showToast('${safeJsName} is Out of Stock!', 'error')` 
          : `addQuickItem('${safeId}')`;

        return `
          <div class="${cardClass}" onclick="${clickAction}">
            <div class="item-img">
              ${hasImg ? `
                <img src="${escapeAttr(item.image)}" alt="${safeName}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'; if (this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                <div class="item-emoji-circle" style="display:none; width:50px; height:50px; border-radius:50%; background:rgba(255,255,255,0.95); box-shadow:0 2px 8px rgba(0,0,0,0.08); align-items:center; justify-content:center; font-size:30px;">${item.emoji || '🍽️'}</div>
              ` : `
                <div class="item-emoji-circle" style="width:50px; height:50px; border-radius:50%; background:rgba(255,255,255,0.95); box-shadow:0 2px 8px rgba(0,0,0,0.08); display:flex; align-items:center; justify-content:center; font-size:30px;">${item.emoji || '🍽️'}</div>
              `}
              ${item.badge === 'out' || isSoldOut
                ? `<span class="item-badge out">SOLD OUT</span>`
                : (item.badge === 'new' ? `<span class="item-badge new">NEW</span>` : (item.badge === 'best' ? `<span class="item-badge best">BEST</span>` : ''))
              }
              ${item.qty > 0 ? `<span class="item-badge" style="background:#10B981; color:#fff; font-size:10px; font-weight:800; top:6px; left:6px; right:auto; padding:2px 8px; border-radius:12px; box-shadow:0 2px 8px rgba(16,185,129,0.4);"><i class="fa-solid fa-check"></i> ${item.qty} in cart</span>` : ''}
              ${isSoldOut ? `<div class="item-soldout-overlay">OUT OF STOCK</div>` : ''}
            </div>
            <div class="item-body">
              <div style="display:flex; align-items:center; justify-content:space-between; gap:5px; margin-bottom:4px;">
                <span class="item-category-tag">${escapeHtml(item.category || item.cat || '')}</span>
                <span class="pos-diet-icon ${isVeg ? 'veg' : 'nonveg'}" title="${isVeg ? 'Vegetarian' : 'Non-Vegetarian'}"><span class="diet-dot"></span></span>
              </div>
              <div class="item-name" title="${safeName}">${safeName}</div>
              <div class="item-footer">
                <div class="item-price">₹ ${Number(item.price || 0).toFixed(0)}<small>/dish</small></div>
                <div class="item-footer-actions">
                  ${isSoldOut
                    ? `<span style="font-size:10px; color:#EF4444; font-weight:700;"><i class="fa-solid fa-ban"></i> Out of Stock</span>`
                    : (item.qty > 0
                        ? `
                          <div class="item-card-stepper" onclick="event.stopPropagation()">
                            <button type="button" class="card-step-btn" onclick="updateQty('${safeId}', -1)" title="Decrease Qty">−</button>
                            <span class="card-step-qty">${item.qty}</span>
                            <button type="button" class="card-step-btn" onclick="updateQty('${safeId}', 1)" title="Increase Qty">+</button>
                          </div>
                        `
                        : `<button class="item-add-btn-pill" type="button" onclick="event.stopPropagation();addQuickItem('${safeId}')" title="Add to Cart"><i class="fa-solid fa-plus"></i> Add</button>`
                      )
                  }
                </div>
              </div>
            </div>
          </div>
        `;
      } catch (itemErr) {
        console.warn('Error rendering dish card:', item, itemErr);
        return '';
      }
    }).join('');
  }

  async function syncPosMenuWithDatabase() {
    if (!MENU || MENU.length === 0) {
      renderSkeleton(12);
    }
    try {
      const res = await fetch(`/api/menu?restaurantId=${RESTAURANT_ID}`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      const rawItems = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : []);

      // Preserve existing cart quantities & notes
      const existingQtys = new Map();
      const existingNotes = new Map();
      if (Array.isArray(MENU)) {
        MENU.forEach(i => {
          if (i.qty > 0) existingQtys.set(String(i.id), i.qty);
          if (i.cookingNote) existingNotes.set(String(i.id), i.cookingNote);
        });
      }

      MENU = rawItems.map(item => {
        const transformed = transformMenuItem(item);
        if (existingQtys.has(String(transformed.id))) {
          transformed.qty = existingQtys.get(String(transformed.id));
        }
        if (existingNotes.has(String(transformed.id))) {
          transformed.cookingNote = existingNotes.get(String(transformed.id));
        }
        return transformed;
      });

      renderMenu();
      if (typeof renderCatPopup === 'function') {
        renderCatPopup();
      }
      if (typeof renderPosCategoryRibbon === 'function') {
        renderPosCategoryRibbon();
      }
      if (typeof renderCart === 'function') {
        renderCart();
      }
    } catch (err) {
      console.warn('POS dynamic menu sync warning:', err);
      const grid = document.getElementById('menuGrid');
      if (grid && (!MENU || MENU.length === 0)) {
        grid.innerHTML = `
          <div class="pos-error-state">
            <div class="pos-empty-icon" style="background:rgba(239,68,68,0.15); color:#EF4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="pos-empty-title">Unable to Load Menu Items</div>
            <div class="pos-empty-sub">Failed to fetch menu items from server (${err.message || 'Network error'}). Please check connectivity.</div>
            <button type="button" class="pos-empty-action" onclick="syncPosMenuWithDatabase()"><i class="fa-solid fa-rotate-right"></i> Retry Loading</button>
          </div>
        `;
      }
    }
  }

  // Zero-delay immediate initial render if items pre-seeded from server
  if (Array.isArray(MENU) && MENU.length > 0) {
    try {
      renderMenu();
      if (typeof renderPosCategoryRibbon === 'function') renderPosCategoryRibbon();
    } catch (e) {
      console.warn('Initial renderMenu error:', e);
    }
  }

  // Trigger sync on DOMContentLoaded or immediately
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => syncPosMenuWithDatabase());
  } else {
    syncPosMenuWithDatabase();
  }

  // Failsafe timeout: If skeleton is still visible after 1.5s, force renderMenu() or sync
  setTimeout(() => {
    const grid = document.getElementById('menuGrid');
    if (grid && grid.querySelector('.pos-skeleton-card')) {
      if (Array.isArray(MENU) && MENU.length > 0) {
        renderMenu();
      } else {
        syncPosMenuWithDatabase();
      }
    }
  }, 1500);

  const DEFAULT_WAITER_NAME = @json(Auth::user()->name ?? 'Staff');
  const META_PICKERS = {
    waiter: {
      title: 'Select Waiter',
      icon: 'fa-user-tie',
      label: 'Waiter',
      options: [DEFAULT_WAITER_NAME, 'Counter Staff'],
    },
    covers: {
      title: 'Select Covers',
      icon: 'fa-users',
      label: 'Covers',
      options: ['1', '2', '3', '4', '5', '6'],
    },
    priority: {
      title: 'Select Priority',
      icon: 'fa-bolt',
      label: 'Priority',
      options: ['Normal', 'Rush', 'VIP'],
    },
  };

  const metaSelections = {
    waiter: DEFAULT_WAITER_NAME,
    covers: '2',
    priority: 'Normal',
  };

  /* ─── RENDER CART WITH ITEM COOKING PREFERENCES ─── */
  function renderCart() {
    const cartItems = MENU.filter(i => i.qty > 0);
    const el = document.getElementById('cartItems');

    el.innerHTML = cartItems.map(item => `
      <div class="cart-item">
        <div class="ci-info">
          <div class="ci-top">
            <div class="ci-main">
              <div class="ci-name">${escapeHtml(item.name)}</div>
              ${item.selected_modifiers && item.selected_modifiers.length > 0 ? `
                <div style="font-size: 10px; font-weight:700; color: #10B981; margin-top: 3px; display: flex; flex-wrap: wrap; gap: 4px;">
                  ${item.selected_modifiers.map(m => `<span style="background:rgba(16,185,129,0.12); padding:1px 6px; border-radius:4px; border:1px solid rgba(16,185,129,0.3);"><i class="fa-solid fa-plus" style="font-size:8px;"></i> ${escapeHtml(m.name)} (+₹${m.price})</span>`).join('')}
                </div>
              ` : `<div class="ci-variant">${item.has_modifiers ? 'Standard' : 'Regular'}</div>`}
              ${item.cookingNote ? `
                <div style="font-size: 11px; font-weight:700; color: #F59E0B; background: rgba(245,158,11,0.12); padding: 3px 8px; border-radius: 6px; margin-top: 5px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(245,158,11,0.3);">
                  <i class="fa-solid fa-pen-nib"></i> ${escapeHtml(item.cookingNote)}
                </div>
              ` : ''}
            </div>
            <div class="ci-price">₹ ${(item.price * item.qty).toFixed(0)}</div>
          </div>
          <div class="ci-bottom">
            <div class="ci-controls">
              <button type="button" class="ci-btn" onclick="updateQty('${item.id}',-1)" title="Decrease Qty">−</button>
              <div class="ci-qty">${item.qty}</div>
              <button type="button" class="ci-btn" onclick="updateQty('${item.id}',1)" title="Increase Qty">+</button>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              <button class="ci-note-btn" onclick="openItemCustomizerModal('${item.id}')" title="Custom Cooking Preference" style="background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3); color:#818CF8; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:4px;"><i class="fa-solid fa-pen-to-square"></i> Preference</button>
              <button class="ci-del" onclick="removeItem('${item.id}')" title="Remove item"><i class="fa-solid fa-trash-can"></i></button>
            </div>
          </div>
        </div>
      </div>
    `).join('');

    updateTotals();
  }

  /* ─── DINEFLOW OPERATIONS & USER DROPDOWN CONTROLLERS ─── */
  function toggleMoreToolsMenu(event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    closeUserMenu();
    const menu = document.getElementById('posMoreToolsMenu');
    if (menu) {
      menu.classList.toggle('show');
    }
  }
  function closeMoreToolsMenu() {
    const menu = document.getElementById('posMoreToolsMenu');
    if (menu) menu.classList.remove('show');
  }

  function toggleUserMenu(event) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }
    closeMoreToolsMenu();
    const menu = document.getElementById('posUserMenu');
    if (menu) {
      menu.classList.toggle('show');
    }
  }
  function closeUserMenu() {
    const menu = document.getElementById('posUserMenu');
    if (menu) menu.classList.remove('show');
  }

  window.addEventListener('click', (e) => {
    if (!e.target.closest('.pos-dropdown-wrap')) {
      closeMoreToolsMenu();
      closeUserMenu();
    }
  });

  /* ─── POS LIGHT/DARK THEME SWITCHER ─── */
  function initPosTheme() {
    const savedTheme = localStorage.getItem('posTheme');
    if (savedTheme === 'light') {
      document.body.classList.add('light-theme');
      updateThemeBtnUI(true);
    } else {
      document.body.classList.remove('light-theme');
      updateThemeBtnUI(false);
    }
  }
  function togglePosTheme() {
    const isLight = document.body.classList.toggle('light-theme');
    localStorage.setItem('posTheme', isLight ? 'light' : 'dark');
    updateThemeBtnUI(isLight);
    showToast(isLight ? 'Switched to Light Mode ☀️' : 'Switched to Dark Mode 🌙', 'info');
  }
  function updateThemeBtnUI(isLight) {
    const icon = document.getElementById('posThemeIcon');
    const text = document.getElementById('posThemeText');
    if (icon && text) {
      icon.className = isLight ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
      text.textContent = isLight ? 'Dark Mode 🌙' : 'Light Mode ☀️';
    }
  }

  /* ─── ITEM PREFERENCE & COOKING NOTE FUNCTIONS ─── */
  function openItemCustomizerModal(id) {
    const item = MENU.find(i => String(i.id) === String(id));
    if (!item) return;
    document.getElementById('noteModalItemId').value = item.id;
    document.getElementById('noteModalItemName').textContent = 'Customize: ' + item.name;
    document.getElementById('noteModalInput').value = item.cookingNote || '';
    openModal('posItemNoteModal');
  }
  function addNoteTag(tag) {
    const input = document.getElementById('noteModalInput');
    if (!input.value.includes(tag)) {
      input.value = input.value ? (input.value + ', ' + tag) : tag;
    }
  }
  function clearItemNote() {
    document.getElementById('noteModalInput').value = '';
    saveItemNote();
  }
  function saveItemNote() {
    const id = document.getElementById('noteModalItemId').value;
    const note = document.getElementById('noteModalInput').value.trim();
    const item = MENU.find(i => String(i.id) === String(id));
    if (item) {
      item.cookingNote = note;
      renderCart();
      closeModal('posItemNoteModal');
      showToast('Preference saved for ' + item.name, 'success');
    }
  }

  /* ─── SMART OFFERS & DISCOUNT ENGINE ─── */
  let activeOffer = null;

  async function fetchAndRenderDynamicPosOffers() {
    const container = document.getElementById('dynamicPosOffersContainer');
    if (!container) return;

    try {
      const res = await fetch(`/api/coupons?restaurantId=${RESTAURANT_ID}`);
      const data = await res.json();
      if (data.success && Array.isArray(data.data) && data.data.length > 0) {
        const now = new Date();
        const activeCoupons = data.data.filter(c => c.is_active && (!c.expires_at || new Date(c.expires_at) >= now));
        
        if (activeCoupons.length > 0) {
          container.innerHTML = activeCoupons.map(c => {
            const isPct = c.discount_type === 'percentage';
            const flatAmt = isPct ? 0 : parseFloat(c.discount_value);
            const pctAmt = isPct ? parseFloat(c.discount_value) : 0;
            const minSub = parseFloat(c.min_order_amount || 0);
            const badgeColor = isPct ? '#6366F1' : '#10B981';
            const icon = isPct ? '⚡' : '💸';
            
            return `
            <div class="offer-card-item" onclick="applySmartOffer('${escapeHtml(c.code)}', '${escapeHtml(c.name || c.code)}', ${flatAmt}, ${pctAmt}, ${minSub})" style="display:flex; align-items:center; justify-content:space-between; padding:14px; border-radius:14px; background:var(--card); border:1px solid var(--border); cursor:pointer; transition:all 0.2s;">
              <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:42px; height:42px; border-radius:12px; background:rgba(99,102,241,0.15); color:${badgeColor}; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
                  ${icon}
                </div>
                <div>
                  <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);">${escapeHtml(c.name || c.code)}</div>
                  <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                    ${isPct ? `Get ${c.discount_value}% OFF` : `Flat ₹${c.discount_value} OFF`} ${minSub > 0 ? `on order >= ₹${minSub}` : ''}
                  </div>
                  <span style="font-size:10px; font-weight:800; color:${badgeColor}; background:rgba(99,102,241,0.1); padding:2px 7px; border-radius:6px; margin-top:4px; display:inline-block;">Code: ${escapeHtml(c.code)}</span>
                </div>
              </div>
              <button style="background:linear-gradient(135deg,#6366F1,#4F46E5); color:#fff; border:none; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; cursor:pointer;">Apply</button>
            </div>`;
          }).join('');
        }
      }
    } catch (err) {
      console.warn('Failed to load dynamic POS coupons:', err);
    }
  }

  function applySmartOffer(code, label, flatAmount, pctAmount, minSubtotal) {
    const cartItems = MENU.filter(i => i.qty > 0);
    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);

    if (subtotal <= 0) {
      showToast('Cart is empty! Add items first.', 'warning');
      return;
    }

    if (minSubtotal > 0 && subtotal < minSubtotal) {
      showToast(`Minimum order subtotal must be ₹${minSubtotal} to apply ${code}!`, 'error');
      return;
    }

    activeOffer = { code, label, flatAmount, pctAmount, minSubtotal };
    updateTotals();
    closeModal('posOffersModal');
    showToast(`🎁 Applied Offer "${code}": ${label}!`, 'success');
  }

  function removeActiveOffer() {
    activeOffer = null;
    updateTotals();
    closeModal('posOffersModal');
    showToast('Offer removed from cart.', 'info');
  }

  function redeemCustomerLoyaltyPoints(pts, discountAmt) {
    if (typeof selectedCustomerData === 'undefined' || !selectedCustomerData || !selectedCustomerData.name) {
      showToast('Please select a customer first to redeem Loyalty Points!', 'warning');
      openModal('customerModal');
      return;
    }

    const currentPts = selectedCustomerData.points || 0;
    if (currentPts < pts) {
      showToast(`Customer has only ${currentPts} points. Requires ${pts} points to redeem!`, 'error');
      return;
    }

    applySmartOffer(`PTS-${pts}`, `Redeemed ${pts} Loyalty Points`, discountAmt, 0, 0);
    showToast(`⭐ Redeemed ${pts} Loyalty Points for ₹${discountAmt} Discount!`, 'success');
  }

  let userManuallySelectedOffer = false;

  function getSubtotal() {
    const cartItems = MENU.filter(i => i.qty > 0);
    return cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
  }

  async function checkAndAutoApplyCoupon(subtotal) {
    if (userManuallySelectedOffer || subtotal <= 0) return;

    const isNewCust = (typeof selectedCustomerData !== 'undefined' && selectedCustomerData) ? (selectedCustomerData.isNew || selectedCustomerData.is_new || !selectedCustomerData.phone) : true;
    const ordType = typeof currentOrderType !== 'undefined' ? currentOrderType : 'all';

    try {
      const res = await fetch('/api/coupons/auto-apply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        body: JSON.stringify({
          order_amount: subtotal,
          is_new_customer: isNewCust,
          order_type: ordType
        })
      });
      const data = await res.json();
      if (data.success && data.data && data.data.coupon) {
        const c = data.data.coupon;
        if (!activeOffer || activeOffer.code !== c.code) {
          activeOffer = {
            code: c.code,
            label: c.name || (c.code + ' Auto-Apply Offer'),
            flatAmount: c.discount_type === 'fixed' ? c.discount_value : 0,
            pctAmount: c.discount_type === 'percentage' ? c.discount_value : 0,
            maxDiscount: c.max_discount || 0,
            minSubtotal: c.min_order_amount || 0,
            isAuto: true
          };
          showToast(`⚡ Auto-Applied Offer: ${c.code} (Saved ₹${data.data.discount})`, 'success');
          updateTotals();
        }
      }
    } catch (_) {
      // Local fallback auto-apply for new customer
      if (isNewCust && (!activeOffer || activeOffer.code !== 'WELCOME100')) {
        applySmartOffer('WELCOME100', '15% New Customer Welcome Offer', 0, 15, 100);
      }
    }
  }

  async function applyCoupon() {
    const input = document.getElementById('couponInput');
    const code = (input ? input.value : '').trim().toUpperCase();
    if (!code) {
      showToast('Please enter a coupon code or click Offers!', 'warning');
      return;
    }

    try {
      const isNewCust = (typeof selectedCustomerData !== 'undefined' && selectedCustomerData) ? (selectedCustomerData.isNew || selectedCustomerData.is_new || !selectedCustomerData.phone) : true;
      const ordType = typeof currentOrderType !== 'undefined' ? currentOrderType : 'all';

      const res = await fetch('/api/coupons/validate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
        body: JSON.stringify({
          code: code,
          order_amount: getSubtotal(),
          is_new_customer: isNewCust,
          order_type: ordType
        })
      });
      const data = await res.json();
      if (data.success && data.data) {
        const c = data.data.coupon;
        userManuallySelectedOffer = true;
        activeOffer = {
          code: c.code,
          label: c.name || (c.code + ' Offer'),
          flatAmount: c.discount_type === 'fixed' ? c.discount_value : 0,
          pctAmount: c.discount_type === 'percentage' ? c.discount_value : 0,
          maxDiscount: c.max_discount || 0,
          minSubtotal: c.min_order_amount || 0,
          isAuto: false
        };
        showToast(`✅ Coupon "${c.code}" applied! Saved ₹${data.data.discount}`, 'success');
        updateTotals();
        return;
      } else {
        showToast(data.message || 'Invalid coupon code', 'error');
        return;
      }
    } catch (_) {}

    // Fallback static codes
    userManuallySelectedOffer = true;
    if (code === 'WELCOME15' || code === 'FIRSTORDER' || code === 'WELCOME100') {
      applySmartOffer('WELCOME15', '15% Welcome Discount (First-Time Customer)', 0, 15, 0);
    } else if (code === 'SAVE50') {
      applySmartOffer('SAVE50', '₹50 OFF (Min Order ₹500)', 50, 0, 500);
    } else if (code === 'SAVE120') {
      applySmartOffer('SAVE120', '₹120 OFF (Min Order ₹1000)', 120, 0, 1000);
    } else if (code === 'VIP10') {
      applySmartOffer('VIP10', '10% VIP Loyalty Discount', 0, 10, 0);
    } else if (code === 'HAPPY10') {
      applySmartOffer('HAPPY10', '10% Off-Peak Happy Hour Discount', 0, 10, 0);
    } else {
      applySmartOffer(code, `Promo Coupon (${code})`, 0, 10, 0);
    }
  }

  function applyModalCoupon() {
    const input = document.getElementById('modalCouponInput');
    const code = (input ? input.value : '').trim().toUpperCase();
    if (!code) {
      showToast('Please enter a coupon code!', 'warning');
      return;
    }
    document.getElementById('couponInput').value = code;
    applyCoupon();
  }

  function updateTotals() {
    const cartItems = MENU.filter(i => i.qty > 0);
    const total = cartItems.reduce((sum, item) => sum + item.qty, 0);
    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);

    // Auto-apply check if no manual offer selected
    if ((!activeOffer || activeOffer.isAuto) && subtotal > 0) {
      checkAndAutoApplyCoupon(subtotal);
    }

    let discountVal = 0;
    if (activeOffer) {
      if (activeOffer.minSubtotal > 0 && subtotal < activeOffer.minSubtotal) {
        activeOffer = null;
        userManuallySelectedOffer = false;
        showToast('Offer removed because subtotal fell below minimum threshold', 'warning');
      } else if (activeOffer.pctAmount > 0) {
        discountVal = (subtotal * activeOffer.pctAmount) / 100;
        if (activeOffer.maxDiscount && activeOffer.maxDiscount > 0) {
          discountVal = Math.min(discountVal, activeOffer.maxDiscount);
        }
      } else if (activeOffer.flatAmount > 0) {
        discountVal = activeOffer.flatAmount;
      }
    }
    if (discountVal > subtotal) discountVal = subtotal;

    const discountRow = document.getElementById('discountRow');
    const discountAmtEl = document.getElementById('discountAmount');
    if (discountRow && discountAmtEl) {
      if (discountVal > 0) {
        discountRow.style.display = 'flex';
        discountAmtEl.innerHTML = `- ₹ ${discountVal.toFixed(2)} <small style="font-size:10px; color:#F59E0B; font-weight:700;">(${activeOffer.code})</small>`;
      } else {
        discountRow.style.display = 'none';
      }
    }

    const banner = document.getElementById('activeOfferBanner');
    const bannerTitle = document.getElementById('activeOfferBannerTitle');
    const bannerSub = document.getElementById('activeOfferBannerSub');
    if (banner && bannerTitle && bannerSub) {
      if (activeOffer && discountVal > 0) {
        banner.style.display = 'flex';
        bannerTitle.textContent = `Offer Applied: ${activeOffer.code}`;
        bannerSub.textContent = `${activeOffer.label} • Saved ₹${discountVal.toFixed(2)}`;
      } else {
        banner.style.display = 'none';
      }
    }

    const discountedSubtotal = Math.max(0, subtotal - discountVal);
    const serviceCharge = discountedSubtotal * 0.05;
    const gst = (discountedSubtotal + serviceCharge) * 0.18;
    const deliveryFee = (typeof currentOrderType !== 'undefined' && currentOrderType === 'delivery') ? (parseFloat(document.getElementById('deliveryFeeInput')?.value || 0) || 0) : 0;
    const grandTotal = Math.round(discountedSubtotal + serviceCharge + gst + deliveryFee);

    document.getElementById('cartCount').textContent = total;
    document.getElementById('topbarCartBadge').textContent = total;
    document.getElementById('topbarCartBadge').style.display = total > 0 ? '' : 'none';
    document.getElementById('subItemCount').textContent = total;
    document.getElementById('subtotal').textContent = `₹ ${subtotal.toFixed(2)}`;
    document.getElementById('serviceCharge').textContent = `₹ ${serviceCharge.toFixed(2)}`;
    document.getElementById('gstAmount').textContent = `₹ ${gst.toFixed(2)}`;
    document.getElementById('grandTotal').textContent = `₹ ${grandTotal.toFixed(0)}`;
    document.getElementById('checkoutAmount').textContent = `₹ ${grandTotal.toFixed(0)}`;
  }

  function addQuickItem(id) {
    const item = MENU.find(i => String(i.id) === String(id));
    if (item) {
      if (item.is_available === false || item.available === false) {
        showToast(item.name + ' is currently Out of Stock!', 'error');
        return;
      }
      if (item.has_modifiers) {
        openItemModifierModal(item.id);
        return;
      }
      item.qty += 1;
      renderMenu();
      renderCart();
      showToast(item.name + ' added to cart', 'success');
    }
  }

  function updateQty(id, delta) {
    const item = MENU.find(i => String(i.id) === String(id));
    if (item) {
      if (delta > 0 && (item.is_available === false || item.available === false)) {
        showToast(item.name + ' is currently Out of Stock!', 'error');
        return;
      }
      item.qty += delta;
      if (item.qty < 0) item.qty = 0;
      renderMenu();
      renderCart();
    }
  }

  function removeItem(id) {
    const item = MENU.find(i => String(i.id) === String(id));
    if (item) {
      showToast(item.name + ' removed', 'error');
      item.qty = 0;
      renderMenu();
      renderCart();
    }
  }

  function clearCart() {
    const hasItems = MENU.some(i => (i.qty || 0) > 0);
    if (hasItems && !confirm('Are you sure you want to clear all items from the cart?')) {
      return;
    }
    MENU.forEach(i => i.qty = 0);
    const cartNote = document.getElementById('cartNote');
    if (cartNote) cartNote.value = '';
    const couponInput = document.getElementById('couponInput');
    if (couponInput) couponInput.value = '';
    activeOffer = null;
    userManuallySelectedOffer = false;
    renderMenu();
    renderCart();
    showToast('Cart cleared', 'warning');
  }

  function updateMetaSelections() {
    document.getElementById('waiterValue').textContent = metaSelections.waiter;
    document.getElementById('coversValue').textContent = metaSelections.covers;
    document.getElementById('priorityValue').textContent = metaSelections.priority;
    document.getElementById('kotWaiterName').textContent = metaSelections.waiter;
  }

  function openMetaPopup(type) {
    const config = META_PICKERS[type];
    if (!config) return;

    document.getElementById('metaPopupTitle').innerHTML =
      `<i class="fa-solid ${config.icon}" style="color:var(--primary);"></i>&nbsp; ${config.title}`;

    document.getElementById('metaPopupList').innerHTML = config.options.map(option => `
      <button
        type="button"
        class="meta-option-btn ${metaSelections[type] === option ? 'active' : ''}"
        onclick='selectMetaOption("${type}", "${option}")'
      >
        <span>${option}</span>
        <i class="fa-solid fa-check meta-option-check"></i>
      </button>
    `).join('');

    openModal('metaPopupModal');
  }

  function selectMetaOption(type, value) {
    metaSelections[type] = value;
    updateMetaSelections();
    closeModal('metaPopupModal');
    showToast(META_PICKERS[type].label + ' set to ' + value, 'success');
  }

  /* ─── CATEGORY POPUP ─────────────────────── */
  function renderCatPopup() {
    const categories = typeof getCats === 'function' ? getCats() : [];
    const grid = document.getElementById('catPopupGrid');
    if (!grid) return;

    grid.innerHTML = categories.map(c => {
      const isAct = currentCat.toLowerCase() === c.key.toLowerCase();
      return `
        <div class="cat-popup-item ${isAct ? 'active' : ''}" onclick="selectCat('${escapeHtml(c.key)}','${escapeHtml(c.label)}')">
          <span class="cpi-emoji" style="font-size:22px;">${c.emoji}</span>
          <div style="flex:1; min-width:0;">
            <div class="cpi-name" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escapeHtml(c.label)}</div>
            <div class="cpi-count">${c.count} items</div>
          </div>
          ${isAct ? '<i class="fa-solid fa-circle-check" style="color:var(--primary); font-size:16px;"></i>' : ''}
        </div>
      `;
    }).join('');
  }

  function toggleCatPopup() {
    const modal = document.getElementById('catPopupModal');
    if (modal.classList.contains('show')) {
      closeModal('catPopupModal');
      return;
    }
    renderCatPopup();
    openModal('catPopupModal');
  }

  function setDietaryFilter(diet) {
    currentDietaryFilter = diet || 'all';
    document.getElementById('posFilterAllDietBtn')?.classList.toggle('active', currentDietaryFilter === 'all');
    document.getElementById('posFilterVegBtn')?.classList.toggle('active', currentDietaryFilter === 'veg');
    document.getElementById('posFilterNonVegBtn')?.classList.toggle('active', currentDietaryFilter === 'nonveg');
    renderMenu();
  }

  function renderPosCategoryRibbon() {
    const ribbon = document.getElementById('posCategoryRibbon');
    if (!ribbon) return;
    const categories = typeof getCats === 'function' ? getCats() : [];
    if (categories.length === 0) return;

    ribbon.innerHTML = categories.map(c => {
      const isAct = currentCat.toLowerCase() === c.key.toLowerCase();
      const activeStyle = isAct 
        ? 'background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; border-color:var(--primary); box-shadow:0 2px 8px rgba(99,102,241,0.3);' 
        : 'background:var(--card); color:var(--text-secondary); border-color:var(--border);';

      return `
        <button type="button" onclick="selectCat('${escapeHtml(c.key)}','${escapeHtml(c.label)}')" 
          style="flex-shrink:0; height:32px; padding:0 12px; border-radius:18px; border:1px solid; font-size:11.5px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px; transition:all 0.15s; ${activeStyle}">
          <span>${c.emoji}</span>
          <span>${escapeHtml(c.label)}</span>
          <span style="font-size:9.5px; opacity:0.75; background:rgba(0,0,0,0.2); padding:1px 5px; border-radius:10px;">${c.count}</span>
        </button>
      `;
    }).join('');
  }

  function applyQuickPresetDiscount(pct = 0, flat = 0) {
    const cartItems = MENU.filter(i => i.qty > 0);
    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
    if (subtotal <= 0) {
      showToast('Cart is empty! Add items first to apply discount.', 'warning');
      return;
    }

    if (pct > 0) {
      applySmartOffer(`QUICK${pct}`, `${pct}% Quick Bill Discount`, 0, pct, 0);
    } else if (flat > 0) {
      applySmartOffer(`FLAT${flat}`, `₹${flat} Flat Quick Discount`, flat, 0, 0);
    }
  }

  function selectCat(key, label) {
    currentCat = key || 'all';
    const lbl = document.getElementById('catPopupLabel');
    if (lbl) {
      lbl.textContent = currentCat.toLowerCase() === 'all' ? 'All Categories' : label;
    }
    const btn = document.getElementById('catPopupBtn');
    if (btn) {
      btn.classList.toggle('filtered', currentCat.toLowerCase() !== 'all');
    }
    closeModal('catPopupModal');
    renderPosCategoryRibbon();
    renderMenu();
  }

  function resetCat() { 
    currentCat = 'all';
    selectCat('all','All Categories'); 
  }

  /* ─── FILTER (DEBOUNCED & 60FPS SMOOTH) ──── */
  let filterDebounceTimer = null;
  function filterItems(val) {
    searchTerm = val || '';
    if (filterDebounceTimer) cancelAnimationFrame(filterDebounceTimer);
    filterDebounceTimer = requestAnimationFrame(() => {
      renderMenu();
    });
  }

  function focusItemSearch() {
    const input = document.querySelector('.search-input');
    if (input) {
      input.focus();
      input.select();
      showToast('Search POS Menu Items', 'info');
    }
  }

  /* ─── DELIVERY TYPE ──────────────────────── */
  function setDelivery(el, type) {
    currentOrderType = type;
    document.querySelectorAll('.delivery-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const tableGrp  = document.getElementById('tableGroup');
    const coversGrp = document.getElementById('coversGroup');
    const delivStrip = document.getElementById('deliveryDetailsStrip');
    tableGrp.style.display  = (type === 'dinein') ? 'flex' : 'none';
    coversGrp.style.display = (type === 'dinein') ? 'flex' : 'none';
    if (delivStrip) {
      delivStrip.style.display = (type === 'delivery') ? 'flex' : 'none';
    }
    if (type === 'delivery' && typeof loadDeliveryRiders === 'function') {
      loadDeliveryRiders();
    }
    updateTotals();
  }

  /* ─── TABLE ──────────────────────────────── */
  let allTablesData = [];

  function getTableMetadata(tableNum) {
    const idNum = parseInt(tableNum.replace('T-', ''));
    let floor = 'ground';
    let section = 'indoor';
    let capacity = '4 PAX';
    
    if (idNum >= 1 && idNum <= 4) {
      floor = 'ground';
      section = 'indoor';
      capacity = (idNum % 2 === 0) ? '2 PAX' : '4 PAX';
    } else if (idNum >= 5 && idNum <= 8) {
      floor = 'first';
      section = 'outdoor';
      capacity = (idNum <= 6) ? '6 PAX' : '4 PAX';
    } else if (idNum >= 9 && idNum <= 12) {
      floor = 'terrace';
      section = 'private';
      capacity = (idNum <= 10) ? '2 PAX' : '8 PAX';
    }
    return { floor, section, capacity };
  }

  async function loadTablesData() {
    try {
      const response = await fetch(`/api/tables/restaurant/${RESTAURANT_ID}`);
      const data = await response.json();
      if (data.success && Array.isArray(data.data)) {
        allTablesData = data.data;
        // Reset filters
        const floorSel = document.getElementById('tableFloorSelect');
        if (floorSel) floorSel.value = 'all';
        const sectionSel = document.getElementById('tableSectionSelect');
        if (sectionSel) sectionSel.value = 'all';
        renderTablesGrid(allTablesData);
      }
    } catch (err) {
      console.log('Error loading tables:', err);
    }
  }

  function renderTablesGrid(tables) {
    const grid = document.querySelector('#tableModal .table-grid');
    if (tables.length === 0) {
      grid.innerHTML = `<div style="grid-column: 1 / -1; padding: 24px; text-align: center; color: var(--text-secondary); font-size: 13px; font-weight: 500;"><i class="fa-solid fa-triangle-exclamation" style="margin-bottom: 6px; font-size: 20px; color: var(--warning);"></i><br>No tables match the selected filters.</div>`;
      return;
    }
    
    grid.innerHTML = tables.map(table => {
      const meta = getTableMetadata(table.table_number);
      let statusClass = 'available';
      let statusText = meta.capacity;
      
      if (table.status === 'occupied') {
        statusClass = 'occupied';
        statusText = `Occupied (₹${table.active_order_amount})`;
      }
      
      const currentTableNo = document.getElementById('selectedTableNo')?.textContent || 'T-01';
      if (table.table_number === currentTableNo) {
        statusClass = 'selected';
        statusText = 'Selected';
      }
      
      return `
        <div class="table-cell ${statusClass}" onclick="selectTable(this, '${table.table_number}', ${table.id})">
          <i class="fa-solid fa-table-cells-large" style="font-size:18px; margin-bottom:4px; opacity:0.8;"></i>
          <span style="font-weight:700; font-size:13.5px; letter-spacing:0.3px;">${table.table_number}</span>
          <span style="font-size:9.5px; opacity:0.85; margin-top:2px; font-weight:600;">${statusText}</span>
        </div>
      `;
    }).join('');
  }

  function filterTables() {
    const floor = document.getElementById('tableFloorSelect').value;
    const section = document.getElementById('tableSectionSelect').value;
    
    const filtered = allTablesData.filter(table => {
      const meta = getTableMetadata(table.table_number);
      const matchFloor = (floor === 'all' || meta.floor === floor);
      const matchSection = (section === 'all' || meta.section === section);
      return matchFloor && matchSection;
    });
    
    renderTablesGrid(filtered);
  }

  /* ─── DINEFLOW TABLE SHIFT & MERGE FUNCTIONS ─── */
  function openShiftModal() {
    if (!allTablesData || allTablesData.length === 0) {
      showToast('No table data loaded yet', 'warning');
      return;
    }
    const occupiedTables = allTablesData.filter(t => t.status === 'occupied');
    const availableTables = allTablesData.filter(t => t.status !== 'occupied');

    if (occupiedTables.length === 0) {
      showToast('No occupied tables to shift! Start an order on a table first.', 'warning');
      return;
    }
    if (availableTables.length === 0) {
      showToast('No available tables to shift to!', 'warning');
      return;
    }

    document.getElementById('shiftMergeAction').value = 'shift';
    document.getElementById('shiftMergeModalTitle').innerHTML = '<i class="fa-solid fa-right-left" style="color:var(--primary);"></i>&nbsp; Shift Table (Move Running Order)';
    document.getElementById('shiftTargetLabel').textContent = 'Destination Table (Available):';
    document.getElementById('shiftMergeNote').textContent = 'The active order, items, and KOTs will move directly to the destination table.';
    document.getElementById('shiftMergeSubmitBtn').textContent = 'Confirm Shift';

    const srcSel = document.getElementById('shiftSourceTable');
    srcSel.innerHTML = occupiedTables.map(t => `<option value="${t.id}">${t.table_number} (Active Bill: ₹${t.active_order_amount || 0})</option>`).join('');

    const tgtSel = document.getElementById('shiftTargetTable');
    tgtSel.innerHTML = availableTables.map(t => `<option value="${t.id}">${t.table_number} (Available)</option>`).join('');

    openModal('shiftMergeModal');
  }

  function openMergeModal() {
    if (!allTablesData || allTablesData.length === 0) {
      showToast('No table data loaded yet', 'warning');
      return;
    }
    const occupiedTables = allTablesData.filter(t => t.status === 'occupied');

    if (occupiedTables.length < 2) {
      showToast('Need at least 2 occupied tables to merge orders!', 'warning');
      return;
    }

    document.getElementById('shiftMergeAction').value = 'merge';
    document.getElementById('shiftMergeModalTitle').innerHTML = '<i class="fa-solid fa-object-group" style="color:#10B981;"></i>&nbsp; Merge Tables (Combine Orders)';
    document.getElementById('shiftTargetLabel').textContent = 'Merge Into Table:';
    document.getElementById('shiftMergeNote').textContent = 'All items from Source Table will merge into Destination Table, and Source Table will be released.';
    document.getElementById('shiftMergeSubmitBtn').textContent = 'Confirm Merge';

    const srcSel = document.getElementById('shiftSourceTable');
    srcSel.innerHTML = occupiedTables.map(t => `<option value="${t.id}">Source: ${t.table_number} (₹${t.active_order_amount || 0})</option>`).join('');

    const tgtSel = document.getElementById('shiftTargetTable');
    tgtSel.innerHTML = occupiedTables.map(t => `<option value="${t.id}">Target: ${t.table_number} (₹${t.active_order_amount || 0})</option>`).join('');
    if (tgtSel.options.length > 1) {
      tgtSel.selectedIndex = 1;
    }

    openModal('shiftMergeModal');
  }

  async function executeShiftMerge() {
    const action = document.getElementById('shiftMergeAction').value;
    const srcId = document.getElementById('shiftSourceTable').value;
    const tgtId = document.getElementById('shiftTargetTable').value;

    if (!srcId || !tgtId) {
      showToast('Please select both tables', 'warning');
      return;
    }

    if (srcId === tgtId) {
      showToast('Source and Destination tables cannot be the same', 'error');
      return;
    }

    const btn = document.getElementById('shiftMergeSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

    const url = action === 'shift' ? '/api/tables/shift' : '/api/tables/merge';
    const payload = action === 'shift' 
      ? { restaurant_id: RESTAURANT_ID, from_table_id: srcId, to_table_id: tgtId }
      : { restaurant_id: RESTAURANT_ID, source_table_id: srcId, target_table_id: tgtId };

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message || (action === 'shift' ? 'Table shifted successfully!' : 'Tables merged successfully!'), 'success');
        closeModal('shiftMergeModal');
        closeModal('tableModal');
        if (typeof loadTablesData === 'function') await loadTablesData();
        if (typeof loadOrdersData === 'function') await loadOrdersData();
      } else {
        showToast(data.message || 'Operation failed', 'error');
      }
    } catch (err) {
      console.error(err);
      showToast('Network error during table operation', 'error');
    } finally {
      btn.disabled = false;
      btn.textContent = action === 'shift' ? 'Confirm Shift' : 'Confirm Merge';
    }
  }

  /* ─── DINEFLOW SHIFT & CASH REGISTER LOGIC ─── */
  let currentShiftData = null;

  async function checkShiftStatusOnLoad() {
    try {
      const res = await fetch('/api/cash-register/current');
      const data = await res.json();
      const shiftBtn = document.getElementById('posShiftBtn');
      const shiftText = document.getElementById('posShiftText');
      if (data.success && data.has_open_shift && data.data) {
        currentShiftData = data.data;
        if (shiftBtn && shiftText) {
          shiftBtn.style.color = '#10B981';
          shiftBtn.style.background = 'rgba(16,185,129,0.15)';
          shiftBtn.style.borderColor = 'rgba(16,185,129,0.4)';
          shiftText.textContent = `Shift #${data.data.register.id}`;
          shiftBtn.title = `Shift #${data.data.register.id} Active (Cash Register & Shift Control)`;
        }
      } else {
        currentShiftData = null;
        if (shiftBtn && shiftText) {
          shiftBtn.style.color = '#F59E0B';
          shiftBtn.style.background = 'rgba(245,158,11,0.15)';
          shiftBtn.style.borderColor = 'rgba(245,158,11,0.4)';
          shiftText.textContent = `Shift`;
          shiftBtn.title = 'Open Cash Register & Shift Drawer';
        }
      }
    } catch (e) {
      console.log('Error checking shift status:', e);
    }
  }

  async function openShiftRegisterModal() {
    openModal('shiftRegisterModal');
    const body = document.getElementById('shiftRegisterModalBody');
    body.innerHTML = `<div style="text-align:center; padding:30px 0;"><i class="fa-solid fa-spinner fa-spin" style="font-size:24px; color:var(--primary);"></i><div style="margin-top:10px; font-size:12px; color:var(--text-muted);">Fetching cash register details...</div></div>`;

    try {
      const res = await fetch('/api/cash-register/current');
      const data = await res.json();

      if (!data.success || !data.has_open_shift || !data.data) {
        body.innerHTML = `
          <div style="text-align:center; padding:10px 0 20px;">
            <div style="width:60px; height:60px; border-radius:50%; background:rgba(245,158,11,0.15); color:#F59E0B; font-size:26px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
              <i class="fa-solid fa-lock-open"></i>
            </div>
            <h3 style="font-family:'Outfit',sans-serif; font-size:18px; margin:0 0 6px; color:var(--text-primary);">Start Cashier Shift</h3>
            <p style="font-size:12px; color:var(--text-muted); margin:0;">Enter the opening cash float in the cash drawer to start billing.</p>
          </div>
          <div style="margin-bottom:16px;">
            <label style="font-size:12.5px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Opening Cash Float (₹):</label>
            <input type="number" id="openingCashInput" class="split-input" placeholder="e.g. 2000" style="width:100%; height:44px; border-radius:10px; font-size:16px; font-weight:800;" />
          </div>
          <div style="margin-bottom:20px;">
            <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Notes (Optional):</label>
            <input type="text" id="openingNotesInput" class="split-input" placeholder="e.g. Morning Shift - Cashier" style="width:100%; height:40px; border-radius:8px;" />
          </div>
          <button class="btn-checkout" onclick="submitOpenShift()" style="width:100%; height:44px; border-radius:10px; font-weight:700;">
            <i class="fa-solid fa-play"></i>&nbsp; Open Shift &amp; Start Register
          </button>
        `;
        return;
      }

      const s = data.data;
      currentShiftData = s;
      const openedTime = new Date(s.register.opened_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

      body.innerHTML = `
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; padding-bottom:12px; border-bottom:1px solid var(--border);">
          <div>
            <span style="font-size:11px; font-weight:800; color:#10B981; background:rgba(16,185,129,0.15); padding:3px 10px; border-radius:12px; border:1px solid rgba(16,185,129,0.3);">🟢 SHIFT #${s.register.id} ACTIVE</span>
            <div style="font-size:12px; color:var(--text-muted); margin-top:6px;">Cashier: <b>${s.register.user_name}</b> · Started at ${openedTime}</div>
          </div>
          <button class="btn-outline" onclick="openPettyCashModal()" style="height:34px; padding:0 12px; font-size:11.5px; font-weight:700; color:#F59E0B; border-color:rgba(245,158,11,0.4); background:rgba(245,158,11,0.08); display:flex; align-items:center; gap:5px; cursor:pointer;">
            <i class="fa-solid fa-wallet"></i> Petty Cash
          </button>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Opening Float</div>
            <div style="font-size:18px; font-weight:800; color:var(--text-primary); margin-top:2px;">₹${s.opening_cash.toFixed(2)}</div>
          </div>
          <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Cash Sales</div>
            <div style="font-size:18px; font-weight:800; color:#10B981; margin-top:2px;">₹${s.cash_sales.toFixed(2)}</div>
          </div>
          <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">UPI / Online Sales</div>
            <div style="font-size:18px; font-weight:800; color:#6366F1; margin-top:2px;">₹${s.upi_sales.toFixed(2)}</div>
          </div>
          <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px;">
            <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">Card Sales</div>
            <div style="font-size:18px; font-weight:800; color:#F59E0B; margin-top:2px;">₹${s.card_sales.toFixed(2)}</div>
          </div>
        </div>

        <div style="background:rgba(99,102,241,0.06); border:1.5px dashed rgba(99,102,241,0.3); border-radius:14px; padding:14px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-size:11.5px; font-weight:700; color:var(--text-secondary);">EXPECTED CASH IN DRAWER:</div>
            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Opening + Cash Sales (${s.cash_sales}) + In (${s.cash_in}) - Out (${s.cash_out})</div>
          </div>
          <div style="font-size:24px; font-weight:900; color:#10B981; font-family:'Outfit',sans-serif;">₹${s.expected_cash.toFixed(2)}</div>
        </div>

        <div style="display:flex; gap:10px;">
          <a href="/z-report" target="_blank" class="btn-outline" style="flex:1; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; gap:6px; font-size:12px; font-weight:700; text-decoration:none;">
            <i class="fa-solid fa-chart-line"></i> View Z-Report
          </a>
          <button class="btn-checkout" onclick="openCloseShiftForm()" style="flex:1.2; height:40px; border-radius:10px; background:linear-gradient(135deg, #EF4444, #DC2626); font-weight:700; font-size:12.5px; cursor:pointer;">
            <i class="fa-solid fa-power-off"></i> Close Shift
          </button>
        </div>
      `;

    } catch (err) {
      console.error(err);
      body.innerHTML = `<div style="text-align:center; padding:20px; color:#EF4444;">Failed to load shift details. Please check network.</div>`;
    }
  }

  async function submitOpenShift() {
    const cashVal = parseFloat(document.getElementById('openingCashInput')?.value || 0);
    const notesVal = document.getElementById('openingNotesInput')?.value || '';

    if (isNaN(cashVal) || cashVal < 0) {
      showToast('Please enter a valid opening cash amount (e.g. 0 or 2000)', 'error');
      return;
    }

    try {
      const res = await fetch('/api/cash-register/open', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          restaurant_id: RESTAURANT_ID,
          opening_cash: cashVal,
          notes: notesVal
        })
      });
      const data = await res.json();
      if (data.success) {
        showToast('✅ ' + data.message, 'success');
        await checkShiftStatusOnLoad();
        await openShiftRegisterModal();
      } else {
        showToast(data.message || 'Failed to open shift', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('Network error opening shift', 'error');
    }
  }

  function openPettyCashModal() {
    openModal('pettyCashModal');
    document.getElementById('pettyAmountInput').value = '';
    document.getElementById('pettyReasonInput').value = '';
  }

  async function submitPettyCash() {
    const type = document.querySelector('input[name="pettyType"]:checked')?.value || 'cash_out';
    const amount = parseFloat(document.getElementById('pettyAmountInput')?.value || 0);
    const category = document.getElementById('pettyCategorySelect')?.value || 'misc';
    const reason = document.getElementById('pettyReasonInput')?.value.trim();

    if (isNaN(amount) || amount <= 0) {
      showToast('Please enter a valid amount', 'warning');
      return;
    }
    if (!reason) {
      showToast('Please enter a reason or description', 'warning');
      return;
    }

    try {
      const res = await fetch('/api/cash-register/petty-cash', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          restaurant_id: RESTAURANT_ID,
          type,
          amount,
          category,
          reason
        })
      });
      const data = await res.json();
      if (data.success) {
        showToast('✅ ' + data.message, 'success');
        closeModal('pettyCashModal');
        await openShiftRegisterModal();
      } else {
        showToast(data.message || 'Error recording petty cash', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('Network error recording petty cash', 'error');
    }
  }

  function openCloseShiftForm() {
    if (!currentShiftData) return;
    const body = document.getElementById('shiftRegisterModalBody');
    body.innerHTML = `
      <div style="text-align:center; padding:10px 0 16px;">
        <h3 style="font-family:'Outfit',sans-serif; font-size:18px; margin:0 0 4px; color:var(--text-primary);">Close Shift &amp; Reconcile Drawer</h3>
        <p style="font-size:12px; color:var(--text-muted); margin:0;">Count the physical currency notes in the cash drawer and enter below.</p>
      </div>

      <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px 16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center;">
        <div>
          <div style="font-size:11px; font-weight:700; color:var(--text-muted);">SYSTEM EXPECTED CASH:</div>
          <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">(Float + Sales + In - Out)</div>
        </div>
        <div style="font-size:20px; font-weight:800; color:#10B981;">₹${currentShiftData.expected_cash.toFixed(2)}</div>
      </div>

      <div style="margin-bottom:14px;">
        <label style="font-size:12.5px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Actual Physical Cash Counted (₹):</label>
        <input type="number" id="closingCountedCashInput" class="split-input" placeholder="e.g. ${currentShiftData.expected_cash}" oninput="updateDrawerDifference()" style="width:100%; height:44px; border-radius:10px; font-size:16px; font-weight:800;" />
        <div id="drawerDifferenceBadge" style="margin-top:6px; font-size:11.5px; font-weight:700;"></div>
      </div>

      <div style="margin-bottom:20px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-secondary); display:block; margin-bottom:6px;">Closing Remarks / Notes:</label>
        <input type="text" id="closingNotesInput" class="split-input" placeholder="e.g. All settled, no discrepancies" style="width:100%; height:40px; border-radius:8px;" />
      </div>

      <div style="display:flex; gap:10px;">
        <button class="btn-outline" onclick="openShiftRegisterModal()" style="flex:1; height:42px; border-radius:10px; cursor:pointer;">Back</button>
        <button class="btn-checkout" onclick="submitCloseShift()" style="flex:1.4; height:42px; border-radius:10px; background:linear-gradient(135deg, #EF4444, #DC2626); font-weight:700; cursor:pointer;">
          <i class="fa-solid fa-check-double"></i> Confirm &amp; Close Shift
        </button>
      </div>
    `;
  }

  function updateDrawerDifference() {
    if (!currentShiftData) return;
    const counted = parseFloat(document.getElementById('closingCountedCashInput')?.value || 0);
    const expected = currentShiftData.expected_cash;
    const diff = counted - expected;
    const badge = document.getElementById('drawerDifferenceBadge');
    if (!badge) return;

    if (Math.abs(diff) < 0.5) {
      badge.style.color = '#10B981';
      badge.textContent = '✅ Exact Match! Zero discrepancy.';
    } else if (diff > 0) {
      badge.style.color = '#3B82F6';
      badge.textContent = `ℹ️ ₹${diff.toFixed(2)} Excess Cash in Drawer`;
    } else {
      badge.style.color = '#EF4444';
      badge.textContent = `⚠️ ₹${Math.abs(diff).toFixed(2)} Shortage / Missing Cash!`;
    }
  }

  async function submitCloseShift() {
    const counted = parseFloat(document.getElementById('closingCountedCashInput')?.value || 0);
    const notes = document.getElementById('closingNotesInput')?.value || '';

    if (isNaN(counted) || counted < 0) {
      showToast('Please enter counted cash in drawer', 'warning');
      return;
    }

    try {
      const res = await fetch('/api/cash-register/close', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          restaurant_id: RESTAURANT_ID,
          closing_cash: counted,
          notes: notes
        })
      });
      const data = await res.json();
      if (data.success) {
        showToast('Shift closed successfully! Opening Z-Report & WhatsApp...', 'success');
        closeModal('shiftRegisterModal');
        await checkShiftStatusOnLoad();
        window.open('/z-report', '_blank');

        if (data.data && data.data.whatsapp_url) {
          setTimeout(() => {
            if (confirm('🚀 Would you like to send this Day-End Z-Report Summary directly to Restaurant Owner WhatsApp now?')) {
              window.open(data.data.whatsapp_url, '_blank');
            }
          }, 800);
        }
      } else {
        showToast(data.message || 'Error closing shift', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('Network error closing shift', 'error');
    }
  }

  /* ─── DINEFLOW ANTI-THEFT KOT CANCELLATION LOGIC ─── */
  function openCancelKotItemModal(orderId, itemId, itemName) {
    document.getElementById('cancelKotOrderId').value = orderId;
    document.getElementById('cancelKotItemId').value = itemId;
    document.getElementById('cancelKotItemName').textContent = itemName;
    document.getElementById('cancelKotManagerPin').value = '';
    openModal('cancelKotItemModal');
    setTimeout(() => document.getElementById('cancelKotManagerPin')?.focus(), 200);
  }

  async function confirmCancelKotItem() {
    const orderId = document.getElementById('cancelKotOrderId').value;
    const itemId = document.getElementById('cancelKotItemId').value;
    const reason = document.getElementById('cancelKotReasonSelect').value;
    const pin = document.getElementById('cancelKotManagerPin').value.trim();

    if (!pin) {
      showToast('Please enter Manager Security PIN', 'warning');
      return;
    }

    try {
      const res = await fetch('/api/orders/cancel-kot-item', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          restaurant_id: RESTAURANT_ID,
          order_id: orderId,
          item_id: itemId,
          reason: reason,
          manager_pin: pin
        })
      });
      const data = await res.json();
      if (data.success) {
        showToast('🛡️ ' + data.message, 'success');
        closeModal('cancelKotItemModal');
        if (typeof loadOrdersData === 'function') loadOrdersData();
        if (typeof loadTablesData === 'function') loadTablesData();
      } else {
        showToast(data.message || 'Manager authorization failed', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('Network error authorizing cancellation', 'error');
    }
  }

  /* ════════════════════════════════════════════════════════════════════════════════
     DINEFLOW ONLINE AGGREGATOR & DELIVERY HUB JAVASCRIPT MODULE
  ════════════════════════════════════════════════════════════════════════════════ */
  let activeDeliveryHubTab = 'aggregators';
  let lastKnownPendingAggregatorCount = 0;
  let cachedDeliveryRiders = [];

  function openDeliveryHubModal() {
    openModal('deliveryHubModal');
    loadAggregatorOrders();
    loadDeliveryRiders();
    loadActiveDeliveries();
  }

  function switchDeliveryHubTab(tabName) {
    activeDeliveryHubTab = tabName;
    const btnAgg = document.getElementById('delivTabAggregators');
    const btnRiders = document.getElementById('delivTabRiders');
    const panelAgg = document.getElementById('panelAggregators');
    const panelRiders = document.getElementById('panelRiders');

    if (tabName === 'aggregators') {
      btnAgg.style.color = 'var(--primary)';
      btnAgg.style.borderBottomColor = 'var(--primary)';
      btnRiders.style.color = 'var(--text-muted)';
      btnRiders.style.borderBottomColor = 'transparent';
      panelAgg.style.display = 'block';
      panelRiders.style.display = 'none';
      loadAggregatorOrders();
    } else {
      btnRiders.style.color = 'var(--primary)';
      btnRiders.style.borderBottomColor = 'var(--primary)';
      btnAgg.style.color = 'var(--text-muted)';
      btnAgg.style.borderBottomColor = 'transparent';
      panelRiders.style.display = 'block';
      panelAgg.style.display = 'none';
      loadDeliveryRiders();
      loadActiveDeliveries();
    }
  }

  // Authentic Web Audio API Synthesizer Siren / Chime
  function playOnlineOrderSiren() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      if (!AudioCtx) return;
      const ctx = new AudioCtx();
      
      const playTone = (freq, startTime, duration) => {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, startTime);
        gain.gain.setValueAtTime(0.3, startTime);
        gain.gain.exponentialRampToValueAtTime(0.01, startTime + duration);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(startTime);
        osc.stop(startTime + duration);
      };

      const now = ctx.currentTime;
      playTone(784, now + 0.0, 0.25); // G5
      playTone(1046, now + 0.25, 0.35); // C6
      playTone(880, now + 0.65, 0.25); // A5
      playTone(1174, now + 0.90, 0.45); // D6
    } catch (e) {
      console.log('Audio playback not permitted yet:', e);
    }
  }

  async function loadAggregatorOrders() {
    const container = document.getElementById('aggregatorOrdersContainer');
    try {
      const res = await fetch('/api/aggregators/orders');
      const data = await res.json();
      if (!data.success) return;

      const pendingCount = data.pending_count || 0;
      const badge = document.getElementById('onlinePendingBadge');
      const tabBadge = document.getElementById('tabPendingBadge');

      if (badge) {
        if (pendingCount > 0) {
          badge.style.display = 'inline-block';
          badge.textContent = pendingCount;
        } else {
          badge.style.display = 'none';
        }
      }
      if (tabBadge) {
        if (pendingCount > 0) {
          tabBadge.style.display = 'inline-block';
          tabBadge.textContent = pendingCount;
        } else {
          tabBadge.style.display = 'none';
        }
      }

      if (pendingCount > lastKnownPendingAggregatorCount) {
        playOnlineOrderSiren();
        showToast('🔔 New Online Food Order Received!', 'success');
      }
      lastKnownPendingAggregatorCount = pendingCount;

      const orders = data.data || [];
      if (orders.length === 0) {
        container.innerHTML = `
          <div style="text-align:center; padding:50px 0; color:var(--text-muted);">
            <div style="width:64px; height:64px; border-radius:50%; background:rgba(245,158,11,0.12); color:#F59E0B; font-size:26px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
              <i class="fa-solid fa-utensils"></i>
            </div>
            <div style="font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">No Online Orders Yet</div>
            <div style="font-size:12px;">Live incoming Zomato &amp; Swiggy orders will stream here instantly.</div>
            <button onclick="simulateOnlineOrderTest()" style="margin-top:16px; background:var(--primary); color:#fff; border:none; border-radius:10px; padding:8px 16px; font-size:12.5px; font-weight:700; cursor:pointer;">
              <i class="fa-solid fa-bell"></i> Simulate Test Order
            </button>
          </div>
        `;
        return;
      }

      container.innerHTML = orders.map(ord => {
        const isZomato = (ord.order_type || '').toLowerCase().includes('zomato');
        const brandColor = isZomato ? '#E23744' : '#FC8019';
        const brandName = isZomato ? 'Zomato' : ((ord.order_type || '').toLowerCase().includes('swiggy') ? 'Swiggy' : 'Delivery');
        const code = ord.aggregator_order_id || ('#ORD-' + ord.id);
        const status = ord.status || 'pending';

        const itemsHtml = (ord.items || []).map(it => `
          <div style="display:flex; justify-content:space-between; align-items:center; font-size:12.5px; padding:4px 0; border-bottom:1px dashed var(--border);">
            <div>
              <span style="font-weight:700; color:var(--text-primary);">${it.quantity}x</span>
              <span style="color:var(--text-secondary); margin-left:6px;">${it.name}</span>
              ${it.notes ? `<span style="font-size:11px; color:#F59E0B; margin-left:6px;">(${it.notes})</span>` : ''}
            </div>
            <div style="font-weight:700; color:var(--text-primary);">₹${(it.price * it.quantity).toFixed(0)}</div>
          </div>
        `).join('');

        let actionArea = '';
        if (status === 'pending') {
          actionArea = `
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:12px; padding-top:10px; border-top:1px solid var(--border);">
              <div style="display:flex; align-items:center; gap:6px;">
                <span style="font-size:11.5px; font-weight:700; color:var(--text-secondary);">Prep Time:</span>
                <select id="prepSelect_${ord.id}" style="background:var(--bg); border:1px solid var(--border); border-radius:8px; padding:5px 8px; font-size:12px; color:var(--text-primary); font-weight:700;">
                  <option value="15">15 Mins</option>
                  <option value="20" selected>20 Mins</option>
                  <option value="30">30 Mins</option>
                  <option value="45">45 Mins</option>
                </select>
              </div>
              <div style="display:flex; gap:8px;">
                <button onclick="rejectOnlineOrder(${ord.id})" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#EF4444; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer;">
                  Reject
                </button>
                <button onclick="acceptOnlineOrder(${ord.id})" style="background:#10B981; color:#fff; border:none; border-radius:8px; padding:6px 16px; font-size:12px; font-weight:800; cursor:pointer; display:flex; align-items:center; gap:6px;">
                  <i class="fa-solid fa-check"></i> Accept Order
                </button>
              </div>
            </div>
          `;
        } else if (status === 'preparing') {
          actionArea = `
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid var(--border);">
              <div style="font-size:12px; color:#F59E0B; font-weight:700;">
                <i class="fa-solid fa-fire-burner"></i> In Kitchen Preparation (${ord.prep_time_minutes || 20}m)
              </div>
              <button onclick="markOnlineFoodReady(${ord.id})" style="background:linear-gradient(135deg, #6366F1, #4F46E5); color:#fff; border:none; border-radius:8px; padding:7px 16px; font-size:12.5px; font-weight:800; cursor:pointer; display:flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-bell-concierge"></i> Food Ready (Notify Rider)
              </button>
            </div>
          `;
        } else if (status === 'ready') {
          actionArea = `
            <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid var(--border);">
              <div style="font-size:12px; color:#10B981; font-weight:800; display:flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-circle-check"></i> Food Packed &amp; Ready for Pickup
              </div>
              <button onclick="markOrderDelivered(${ord.id})" style="background:#10B981; color:#fff; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700; cursor:pointer;">
                Handed Over to Rider
              </button>
            </div>
          `;
        }

        return `
          <div style="background:var(--bg); border:1px solid var(--border); border-radius:14px; padding:14px; border-left:4px solid ${brandColor};">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="background:${brandColor}; color:#fff; font-size:11px; font-weight:800; padding:2px 8px; border-radius:6px; letter-spacing:0.5px;">${brandName.toUpperCase()}</span>
                <span style="font-size:14px; font-weight:800; color:var(--text-primary); font-family:'Outfit',sans-serif;">${code}</span>
              </div>
              <div style="text-align:right;">
                <div style="font-size:15px; font-weight:800; color:#10B981;">₹${parseFloat(ord.total_amount).toFixed(0)}</div>
                <div style="font-size:10px; color:#10B981; font-weight:700;">PREPAID ONLINE</div>
              </div>
            </div>

            <div style="font-size:12px; color:var(--text-secondary); margin-bottom:10px; display:flex; flex-direction:column; gap:2px;">
              <div><i class="fa-solid fa-user" style="width:14px; color:var(--text-muted);"></i> <b>${ord.customer_name || 'Online Customer'}</b> &nbsp;•&nbsp; 📞 ${ord.customer_phone || 'N/A'}</div>
              <div><i class="fa-solid fa-location-dot" style="width:14px; color:var(--text-muted);"></i> ${ord.customer_address || 'Delivery Address'}</div>
              ${ord.special_instructions ? `<div style="color:#F59E0B; margin-top:2px;"><i class="fa-solid fa-comment-dots" style="width:14px;"></i> Note: "${ord.special_instructions}"</div>` : ''}
            </div>

            <div style="background:var(--card); padding:8px 10px; border-radius:8px; border:1px solid var(--border); margin-bottom:4px;">
              ${itemsHtml}
            </div>

            ${actionArea}
          </div>
        `;
      }).join('');

    } catch (err) {
      console.error('Aggregator orders error:', err);
    }
  }

  async function simulateOnlineOrderTest() {
    try {
      showToast('Simulating incoming online order...', 'info');
      const res = await fetch('/api/aggregators/simulate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ restaurant_id: RESTAURANT_ID })
      });
      const data = await res.json();
      if (data.success) {
        playOnlineOrderSiren();
        showToast(data.message, 'success');
        loadAggregatorOrders();
      } else {
        showToast(data.message || 'Simulation failed', 'error');
      }
    } catch (e) {
      console.error(e);
      showToast('Error simulating order', 'error');
    }
  }

  async function acceptOnlineOrder(orderId) {
    const prepSelect = document.getElementById(`prepSelect_${orderId}`);
    const prepTime = prepSelect ? prepSelect.value : 20;

    try {
      const res = await fetch(`/api/aggregators/orders/${orderId}/accept`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ prep_time: prepTime })
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        loadAggregatorOrders();
      } else {
        showToast(data.message || 'Failed to accept order', 'error');
      }
    } catch (e) {
      showToast('Network error accepting order', 'error');
    }
  }

  async function markOnlineFoodReady(orderId) {
    try {
      const res = await fetch(`/api/aggregators/orders/${orderId}/ready`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        loadAggregatorOrders();
      } else {
        showToast(data.message || 'Failed to mark ready', 'error');
      }
    } catch (e) {
      showToast('Network error updating order', 'error');
    }
  }

  async function rejectOnlineOrder(orderId) {
    const reason = prompt('Please enter rejection reason (e.g. Kitchen overloaded):', 'Kitchen Overloaded');
    if (!reason) return;

    try {
      const res = await fetch(`/api/aggregators/orders/${orderId}/reject`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ reason: reason })
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'warning');
        loadAggregatorOrders();
      } else {
        showToast(data.message || 'Failed to reject order', 'error');
      }
    } catch (e) {
      showToast('Network error rejecting order', 'error');
    }
  }

  async function loadDeliveryRiders() {
    try {
      const res = await fetch('/api/delivery/riders');
      const data = await res.json();
      if (!data.success) return;

      cachedDeliveryRiders = data.data || [];

      // Update POS cart dropdown
      const sel = document.getElementById('deliveryRiderSelect');
      if (sel) {
        sel.innerHTML = '<option value="">🛵 Auto Assign Rider</option>' + cachedDeliveryRiders.map(r => `
          <option value="${r.id}">${r.name} (${r.status === 'available' ? 'Available' : 'Busy'})</option>
        `).join('');
      }

      // Update Roster list in Delivery Hub
      const roster = document.getElementById('ridersRosterContainer');
      if (roster) {
        roster.innerHTML = cachedDeliveryRiders.map(r => `
          <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:10px 12px; display:flex; align-items:center; justify-content:space-between;">
            <div>
              <div style="font-size:12.5px; font-weight:800; color:var(--text-primary);">${r.name}</div>
              <div style="font-size:11px; color:var(--text-muted);"><i class="fa-solid fa-phone"></i> ${r.phone}</div>
              <div style="font-size:10.5px; color:var(--text-secondary); margin-top:2px;">🛵 ${r.vehicle_number || 'N/A'}</div>
            </div>
            <div style="text-align:right;">
              <span style="font-size:10px; font-weight:800; padding:2px 7px; border-radius:10px; background:${r.status === 'available' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)'}; color:${r.status === 'available' ? '#10B981' : '#F59E0B'};">
                ${r.status === 'available' ? 'Available' : 'On Run'}
              </span>
              <div style="font-size:10.5px; color:var(--text-muted); margin-top:4px;">${r.active_orders_count || 0} active</div>
            </div>
          </div>
        `).join('');
      }

    } catch (e) {
      console.error('Riders load error:', e);
    }
  }

  async function loadActiveDeliveries() {
    const container = document.getElementById('activeDeliveriesContainer');
    if (!container) return;

    try {
      const res = await fetch('/api/orders/restaurant/' + RESTAURANT_ID);
      const data = await res.json();
      if (!data.success) return;

      const allOrders = data.data || [];
      const deliveryOrders = allOrders.filter(o => o.order_type === 'delivery' && o.status !== 'completed' && o.status !== 'cancelled');

      if (deliveryOrders.length === 0) {
        container.innerHTML = `
          <div style="text-align:center; padding:30px 0; color:var(--text-muted); font-size:12.5px;">
            <i class="fa-solid fa-circle-check" style="color:#10B981; font-size:22px; margin-bottom:6px; display:block;"></i>
            All home deliveries are dispatched and completed!
          </div>
        `;
        return;
      }

      container.innerHTML = deliveryOrders.map(ord => {
        const riderOptions = cachedDeliveryRiders.map(r => `
          <option value="${r.id}" ${ord.rider_id == r.id ? 'selected' : ''}>${r.name}</option>
        `).join('');

        return `
          <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
              <div style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:13px; font-weight:800; color:var(--text-primary);">#ORD-${ord.id}</span>
                <span style="font-size:10.5px; font-weight:800; background:rgba(245,158,11,0.15); color:#F59E0B; padding:2px 6px; border-radius:6px; text-transform:uppercase;">${ord.delivery_status || 'Pending'}</span>
              </div>
              <div style="font-size:12px; color:var(--text-secondary); margin-top:3px;">
                👤 <b>${ord.customer_name || 'Customer'}</b> • 📞 ${ord.customer_phone || 'N/A'}
              </div>
              <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                📍 ${ord.customer_address || 'No address provided'}
              </div>
            </div>
            
            <div style="display:flex; align-items:center; gap:8px;">
              <div style="text-align:right; margin-right:8px;">
                <div style="font-size:14px; font-weight:800; color:#10B981;">₹${parseFloat(ord.total_amount).toFixed(0)}</div>
                <div style="font-size:10px; color:var(--text-muted);">${ord.payment_status === 'paid' ? 'Paid' : 'COD (Cash on Delivery)'}</div>
              </div>
              
              <select onchange="assignRiderToOrder(${ord.id}, this.value)" style="background:var(--card); border:1px solid var(--border); border-radius:8px; padding:6px 8px; font-size:11.5px; color:var(--text-primary);">
                <option value="">Select Rider</option>
                ${riderOptions}
              </select>

              <button onclick="markOrderDelivered(${ord.id})" style="background:#10B981; color:#fff; border:none; border-radius:8px; padding:6px 12px; font-size:12px; font-weight:700; cursor:pointer;" title="Settle bill & mark delivered">
                <i class="fa-solid fa-check"></i> Delivered
              </button>
            </div>
          </div>
        `;
      }).join('');

    } catch (e) {
      console.error('Active deliveries error:', e);
    }
  }

  async function assignRiderToOrder(orderId, riderId) {
    if (!riderId) return;
    try {
      const res = await fetch('/api/delivery/assign', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ order_id: orderId, rider_id: riderId })
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        loadActiveDeliveries();
        loadDeliveryRiders();
      } else {
        showToast(data.message || 'Assignment failed', 'error');
      }
    } catch (e) {
      showToast('Network error assigning rider', 'error');
    }
  }

  async function markOrderDelivered(orderId) {
    try {
      const res = await fetch(`/api/delivery/${orderId}/delivered`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        loadActiveDeliveries();
        loadDeliveryRiders();
        loadAggregatorOrders();
      } else {
        showToast(data.message || 'Failed to settle delivery', 'error');
      }
    } catch (e) {
      showToast('Network error settling delivery', 'error');
    }
  }

  function toggleAddRiderForm() {
    const wrap = document.getElementById('addRiderFormWrap');
    if (wrap) wrap.style.display = (wrap.style.display === 'none' || !wrap.style.display) ? 'block' : 'none';
  }

  async function submitNewRider() {
    const name = document.getElementById('newRiderName')?.value.trim();
    const phone = document.getElementById('newRiderPhone')?.value.trim();
    const vehicle = document.getElementById('newRiderVehicle')?.value.trim();

    if (!name || !phone) {
      showToast('Rider Name & Phone are required', 'warning');
      return;
    }

    try {
      const res = await fetch('/api/delivery/riders', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ name: name, phone: phone, vehicle_number: vehicle })
      });
      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        document.getElementById('newRiderName').value = '';
        document.getElementById('newRiderPhone').value = '';
        document.getElementById('newRiderVehicle').value = '';
        toggleAddRiderForm();
        loadDeliveryRiders();
      } else {
        showToast(data.message || 'Failed to add rider', 'error');
      }
    } catch (e) {
      showToast('Network error saving rider', 'error');
    }
  }

  // Background Auto-Polling for Incoming Aggregator Orders (every 20 seconds, throttled & guarded)
  let isCheckingAggregators = false;
  setInterval(() => {
    if (document.visibilityState === 'hidden') return;
    if (isCheckingAggregators) return;
    isCheckingAggregators = true;
    fetch('/api/aggregators/orders')
      .then(r => r.json())
      .then(data => {
        if (!data || !data.success) return;
        const pendingCount = data.pending_count || 0;
        const badge = document.getElementById('onlinePendingBadge');
        if (badge) {
          if (pendingCount > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = pendingCount;
          } else {
            badge.style.display = 'none';
          }
        }
        if (pendingCount > lastKnownPendingAggregatorCount) {
          playOnlineOrderSiren();
          showToast(`🔔 Incoming Online Order (${pendingCount} pending)!`, 'success');
          // If modal is open, auto refresh
          const modal = document.getElementById('deliveryHubModal');
          if (modal && modal.classList.contains('active')) {
            loadAggregatorOrders();
          }
        }
        lastKnownPendingAggregatorCount = pendingCount;
      })
      .catch(e => {})
      .finally(() => {
        isCheckingAggregators = false;
      });
  }, 20000);

  /* ════════════════════════════════════════════════════════════════════════════════
     DINEFLOW RECIPE, FOOD COSTING, WASTAGE & GST REPORT JAVASCRIPT MODULE
  ════════════════════════════════════════════════════════════════════════════════ */
  let cachedRecipeItems = [];
  let cachedRecipeInventory = [];
  let activeRecipeTab = 'menu';

  function toggleB2bInvoiceForm() {
    const isChecked = document.getElementById('b2bInvoiceToggle')?.checked;
    const form = document.getElementById('b2bInvoiceForm');
    if (form) form.style.display = isChecked ? 'flex' : 'none';
  }

  function openRecipeModal() {
    openModal('recipeModal');
    loadRecipeData();
  }

  function switchRecipeTab(tab) {
    activeRecipeTab = tab;
    const btnMenu = document.getElementById('recipeTabMenu');
    const btnWastage = document.getElementById('recipeTabWastage');
    const panelMenu = document.getElementById('recipePanelMenu');
    const panelWastage = document.getElementById('recipePanelWastage');

    if (tab === 'menu') {
      btnMenu.style.color = 'var(--primary)';
      btnMenu.style.borderBottomColor = 'var(--primary)';
      btnWastage.style.color = 'var(--text-muted)';
      btnWastage.style.borderBottomColor = 'transparent';
      panelMenu.style.display = 'block';
      panelWastage.style.display = 'none';
      loadRecipeData();
    } else {
      btnWastage.style.color = 'var(--primary)';
      btnWastage.style.borderBottomColor = 'var(--primary)';
      btnMenu.style.color = 'var(--text-muted)';
      btnMenu.style.borderBottomColor = 'transparent';
      panelWastage.style.display = 'block';
      panelMenu.style.display = 'none';
      loadWastageData();
    }
  }

  async function loadRecipeData() {
    const tbody = document.getElementById('recipeTableBody');
    try {
      const res = await fetch('/api/recipes');
      const data = await res.json();
      if (!data.success) return;

      cachedRecipeItems = data.data || [];
      cachedRecipeInventory = data.inventory || [];
      renderRecipeTable(cachedRecipeItems);
    } catch (e) {
      console.error('Error loading recipes:', e);
      if (tbody) tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:20px; color:#EF4444;">Failed to load recipes.</td></tr>`;
    }
  }

  function renderRecipeTable(items) {
    const tbody = document.getElementById('recipeTableBody');
    if (!tbody) return;

    if (items.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:30px; color:var(--text-muted);">No menu items found.</td></tr>`;
      return;
    }

    tbody.innerHTML = items.map(item => {
      const marginColor = item.margin_percentage >= 60 ? '#10B981' : (item.margin_percentage >= 40 ? '#F59E0B' : '#EF4444');
      const statusBadge = item.has_recipe
        ? `<span style="font-size:10px; font-weight:800; padding:2px 7px; border-radius:10px; background:rgba(16,185,129,0.15); color:#10B981;">Configured (${item.ingredients.length} items)</span>`
        : `<span style="font-size:10px; font-weight:800; padding:2px 7px; border-radius:10px; background:rgba(239,68,68,0.12); color:#EF4444;">No Recipe</span>`;

      return `
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:10px 12px; font-weight:700; color:var(--text-primary);">
            <div>${item.menu_item_name}</div>
            <div style="margin-top:2px;">${statusBadge}</div>
          </td>
          <td style="padding:10px 12px; color:var(--text-secondary); text-transform:capitalize;">${item.category || 'General'}</td>
          <td style="padding:10px 12px; text-align:right; font-weight:800; color:var(--text-primary);">₹${item.selling_price.toFixed(2)}</td>
          <td style="padding:10px 12px; text-align:right; font-weight:700; color:#F59E0B;">₹${item.raw_food_cost.toFixed(2)}</td>
          <td style="padding:10px 12px; text-align:right; font-weight:800; color:${marginColor};">₹${item.gross_profit.toFixed(2)}</td>
          <td style="padding:10px 12px; text-align:center;">
            <b style="color:${marginColor}; font-size:12.5px;">${item.margin_percentage}%</b>
          </td>
          <td style="padding:10px 12px; text-align:center;">
            <button onclick="openRecipeEditor(${item.menu_item_id}, '${escape(item.menu_item_name)}', ${item.selling_price})" style="background:rgba(139,92,246,0.15); border:1px solid rgba(139,92,246,0.3); color:#8B5CF6; border-radius:6px; padding:4px 10px; font-size:11.5px; font-weight:700; cursor:pointer;">
              <i class="fa-solid fa-pen-to-square"></i> Configure
            </button>
          </td>
        </tr>
      `;
    }).join('');
  }

  function filterRecipeTable(query) {
    const q = (query || '').toLowerCase().trim();
    if (!q) {
      renderRecipeTable(cachedRecipeItems);
      return;
    }
    const filtered = cachedRecipeItems.filter(i => 
      (i.menu_item_name || '').toLowerCase().includes(q) || 
      (i.category || '').toLowerCase().includes(q)
    );
    renderRecipeTable(filtered);
  }

  function openRecipeEditor(menuItemId, encodedName, price) {
    const itemName = unescape(encodedName);
    document.getElementById('editRecipeMenuItemId').value = menuItemId;
    document.getElementById('editRecipeModalTitle').textContent = 'Recipe: ' + itemName;
    document.getElementById('recipeSellingPriceLabel').textContent = '₹' + parseFloat(price).toFixed(2);

    const container = document.getElementById('recipeIngredientRowsContainer');
    container.innerHTML = '';

    const currentItem = cachedRecipeItems.find(i => i.menu_item_id == menuItemId);
    if (currentItem && currentItem.ingredients && currentItem.ingredients.length > 0) {
      currentItem.ingredients.forEach(ing => {
        addRecipeIngredientRow(ing);
      });
    } else {
      addRecipeIngredientRow();
    }

    calcRecipeFoodCost();
    openModal('editRecipeSubModal');
  }

  function addRecipeIngredientRow(existingData = null) {
    const container = document.getElementById('recipeIngredientRowsContainer');
    const row = document.createElement('div');
    row.className = 'recipe-ing-row';
    row.style.display = 'grid';
    row.style.gridTemplateColumns = '2fr 1fr 1fr auto';
    row.style.gap = '8px';
    row.style.alignItems = 'center';

    const invOptions = cachedRecipeInventory.map(inv => {
      const isSelected = existingData && existingData.inventory_id == inv.id ? 'selected' : '';
      return `<option value="${inv.id}" data-cost="${inv.cost_per_unit || 0}" data-unit="${inv.unit || 'pcs'}" ${isSelected}>${inv.item_name} (₹${inv.cost_per_unit || 0}/${inv.unit || 'u'})</option>`;
    }).join('');

    row.innerHTML = `
      <select class="recipe-inv-select" onchange="onRecipeInvSelectChange(this)" style="background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:6px 8px; font-size:11.5px; color:var(--text-primary);">
        <option value="">Select Raw Ingredient</option>
        ${invOptions}
      </select>
      <input type="number" step="0.01" class="recipe-qty-input" value="${existingData ? existingData.quantity_required : '1'}" oninput="calcRecipeFoodCost()" placeholder="Quantity" style="background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:6px 8px; font-size:11.5px; color:var(--text-primary);" />
      <input type="text" class="recipe-unit-input" value="${existingData ? existingData.unit : 'pcs'}" placeholder="Unit (kg/gm/ml)" style="background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:6px 8px; font-size:11.5px; color:var(--text-primary);" />
      <button type="button" onclick="removeRecipeIngredientRow(this)" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#EF4444; border-radius:6px; width:28px; height:28px; cursor:pointer; display:flex; align-items:center; justify-content:center;">
        <i class="fa-solid fa-trash-can" style="font-size:11px;"></i>
      </button>
    `;

    container.appendChild(row);
    calcRecipeFoodCost();
  }

  function onRecipeInvSelectChange(selectEl) {
    const selectedOpt = selectEl.options[selectEl.selectedIndex];
    const defaultUnit = selectedOpt.getAttribute('data-unit');
    const row = selectEl.closest('.recipe-ing-row');
    if (row && defaultUnit) {
      const unitInput = row.querySelector('.recipe-unit-input');
      if (unitInput && !unitInput.value) unitInput.value = defaultUnit;
    }
    calcRecipeFoodCost();
  }

  function removeRecipeIngredientRow(btn) {
    const row = btn.closest('.recipe-ing-row');
    if (row) {
      row.remove();
      calcRecipeFoodCost();
    }
  }

  function calcRecipeFoodCost() {
    let totalCost = 0;
    const rows = document.querySelectorAll('.recipe-ing-row');

    rows.forEach(row => {
      const select = row.querySelector('.recipe-inv-select');
      const qtyInput = row.querySelector('.recipe-qty-input');
      if (!select || !qtyInput) return;

      const opt = select.options[select.selectedIndex];
      const costPerUnit = parseFloat(opt ? opt.getAttribute('data-cost') : 0) || 0;
      const qty = parseFloat(qtyInput.value) || 0;
      totalCost += (costPerUnit * qty);
    });

    const priceText = document.getElementById('recipeSellingPriceLabel')?.textContent || '0';
    const sellingPrice = parseFloat(priceText.replace('₹', '')) || 0;
    const profit = Math.max(0, sellingPrice - totalCost);
    const margin = sellingPrice > 0 ? Math.round((profit / sellingPrice) * 100) : 0;

    const costLabel = document.getElementById('recipeFoodCostLabel');
    const marginLabel = document.getElementById('recipeMarginLabel');
    if (costLabel) costLabel.textContent = '₹' + totalCost.toFixed(2);
    if (marginLabel) {
      marginLabel.textContent = margin + '%';
      marginLabel.style.color = margin >= 60 ? '#10B981' : (margin >= 40 ? '#F59E0B' : '#EF4444');
    }
  }

  async function submitRecipeIngredients() {
    const menuItemId = document.getElementById('editRecipeMenuItemId').value;
    const rows = document.querySelectorAll('.recipe-ing-row');
    const ingredients = [];

    rows.forEach(row => {
      const select = row.querySelector('.recipe-inv-select');
      const qtyInput = row.querySelector('.recipe-qty-input');
      const unitInput = row.querySelector('.recipe-unit-input');
      if (select && select.value && qtyInput && parseFloat(qtyInput.value) > 0) {
        ingredients.push({
          inventory_id: select.value,
          quantity_required: parseFloat(qtyInput.value),
          unit: unitInput ? unitInput.value.trim() : 'pcs'
        });
      }
    });

    try {
      const res = await fetch('/api/recipes', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          restaurant_id: RESTAURANT_ID,
          menu_item_id: menuItemId,
          ingredients: ingredients
        })
      });

      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        closeModal('editRecipeSubModal');
        loadRecipeData();
      } else {
        showToast(data.message || 'Failed to save recipe', 'error');
      }
    } catch (e) {
      showToast('Network error saving recipe', 'error');
    }
  }

  async function loadWastageData() {
    const tbody = document.getElementById('wastageTableBody');
    const totalLossLabel = document.getElementById('wastageTotalLossLabel');
    const invSelect = document.getElementById('wastageInventorySelect');

    try {
      // Populate raw ingredients in wastage form
      if (invSelect && cachedRecipeInventory.length > 0) {
        invSelect.innerHTML = '<option value="">Select Raw Ingredient</option>' + cachedRecipeInventory.map(i => `
          <option value="${i.id}">${i.item_name} (${i.quantity} ${i.unit} in stock)</option>
        `).join('');
      }

      const res = await fetch('/api/wastage');
      const data = await res.json();
      if (!data.success) return;

      if (totalLossLabel) totalLossLabel.textContent = '₹' + parseFloat(data.total_loss || 0).toFixed(2);

      const logs = data.data || [];
      if (logs.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">No kitchen wastage recorded yet.</td></tr>`;
        return;
      }

      tbody.innerHTML = logs.map(l => `
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:8px 12px; color:var(--text-muted);">${new Date(l.created_at).toLocaleDateString()} ${new Date(l.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</td>
          <td style="padding:8px 12px; font-weight:700; color:var(--text-primary);">${l.inventory?.item_name || 'Item'}</td>
          <td style="padding:8px 12px; font-weight:700; color:#EF4444;">${l.quantity} ${l.unit}</td>
          <td style="padding:8px 12px; color:var(--text-secondary);">${l.reason}</td>
          <td style="padding:8px 12px; text-align:right; font-weight:800; color:#EF4444;">₹${parseFloat(l.cost_loss).toFixed(2)}</td>
          <td style="padding:8px 12px; color:var(--text-muted);">${l.recorded_by || 'Staff'}</td>
        </tr>
      `).join('');

    } catch (e) {
      console.error('Wastage load error:', e);
    }
  }

  function toggleRecordWastageForm() {
    const wrap = document.getElementById('recordWastageFormWrap');
    if (wrap) wrap.style.display = (wrap.style.display === 'none' || !wrap.style.display) ? 'block' : 'none';
  }

  async function submitWastageEntry() {
    const invId = document.getElementById('wastageInventorySelect')?.value;
    const qty = document.getElementById('wastageQtyInput')?.value;
    const reason = document.getElementById('wastageReasonSelect')?.value;

    if (!invId || !qty || parseFloat(qty) <= 0) {
      showToast('Please select ingredient and enter quantity', 'warning');
      return;
    }

    try {
      const res = await fetch('/api/wastage', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({
          restaurant_id: RESTAURANT_ID,
          inventory_id: invId,
          quantity: qty,
          reason: reason
        })
      });

      const data = await res.json();
      if (data.success) {
        showToast(data.message, 'success');
        document.getElementById('wastageQtyInput').value = '';
        toggleRecordWastageForm();
        loadWastageData();
      } else {
        showToast(data.message || 'Failed to record wastage', 'error');
      }
    } catch (e) {
      showToast('Network error recording wastage', 'error');
    }
  }

  /* ─── GST & TAX REPORT (GSTR-1 COMPLIANCE) ─── */
  function openGstReportModal() {
    const today = new Date().toISOString().split('T')[0];
    const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];

    if (!document.getElementById('gstStartDate').value) {
      document.getElementById('gstStartDate').value = firstDay;
      document.getElementById('gstEndDate').value = today;
    }

    openModal('gstReportModal');
    loadGstReportData();
  }

  async function loadGstReportData() {
    const startDate = document.getElementById('gstStartDate')?.value;
    const endDate = document.getElementById('gstEndDate')?.value;
    const tbody = document.getElementById('gstInvoicesTableBody');

    try {
      const res = await fetch(`/api/reports/gst-summary?start_date=${startDate}&end_date=${endDate}`);
      const data = await res.json();
      if (!data.success) return;

      const s = data.summary;
      document.getElementById('gstCardTotalInvoices').textContent = s.total_invoices;
      document.getElementById('gstCardB2bCount').textContent = s.b2b_invoices;
      document.getElementById('gstCardB2cCount').textContent = s.b2c_invoices;
      document.getElementById('gstCardTaxable').textContent = '₹' + s.total_taxable_value.toFixed(2);
      document.getElementById('gstCardCgst').textContent = '₹' + s.total_cgst.toFixed(2);
      document.getElementById('gstCardSgst').textContent = '₹' + s.total_sgst.toFixed(2);
      document.getElementById('gstCardGross').textContent = '₹' + s.total_gross_turnover.toFixed(2);
      document.getElementById('gstPeriodLabel').textContent = s.period;

      const invs = data.invoices || [];
      if (invs.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" style="text-align:center; padding:30px; color:var(--text-muted);">No finalized invoices in this period.</td></tr>`;
        return;
      }

      tbody.innerHTML = invs.map(i => `
        <tr style="border-bottom:1px solid var(--border);">
          <td style="padding:8px 10px; font-weight:800; color:var(--text-primary);">${i.invoice_number}</td>
          <td style="padding:8px 10px; color:var(--text-muted);">${i.date}</td>
          <td style="padding:8px 10px;">
            <span style="font-size:10px; font-weight:800; padding:2px 6px; border-radius:6px; background:${i.invoice_type === 'B2B' ? 'rgba(14,165,233,0.15)' : 'rgba(100,116,139,0.15)'}; color:${i.invoice_type === 'B2B' ? '#0EA5E9' : '#64748B'};">${i.invoice_type}</span>
          </td>
          <td style="padding:8px 10px; font-weight:700; color:var(--text-primary);">${i.customer_company !== '-' ? i.customer_company : i.customer_name}</td>
          <td style="padding:8px 10px; font-family:monospace; font-weight:700; color:#0EA5E9;">${i.customer_gstin}</td>
          <td style="padding:8px 10px; text-align:right;">₹${i.taxable_amount.toFixed(2)}</td>
          <td style="padding:8px 10px; text-align:right; color:#0EA5E9;">₹${i.cgst.toFixed(2)}</td>
          <td style="padding:8px 10px; text-align:right; color:#0EA5E9;">₹${i.sgst.toFixed(2)}</td>
          <td style="padding:8px 10px; text-align:right; font-weight:800; color:#10B981;">₹${i.total_amount.toFixed(2)}</td>
          <td style="padding:8px 10px; text-align:center; font-size:10.5px; color:var(--text-muted);">${i.payment_method}</td>
        </tr>
      `).join('');

    } catch (e) {
      console.error('GST load error:', e);
    }
  }

  function downloadGstCsvReport() {
    const startDate = document.getElementById('gstStartDate')?.value || '';
    const endDate = document.getElementById('gstEndDate')?.value || '';
    window.location.href = `/api/reports/gst/export?start_date=${startDate}&end_date=${endDate}`;
  }

  document.addEventListener('DOMContentLoaded', () => {
    setTimeout(checkShiftStatusOnLoad, 500);
    setTimeout(loadDeliveryRiders, 800);
  });

  function clearCartSilent() {
    MENU.forEach(i => i.qty = 0);
    renderMenu();
    renderCart();
  }

  function selectTable(el, tno, id) {
    selectedTableId = id;
    const noEl = document.getElementById('selectedTableNo');
    if (noEl) noEl.textContent = tno;
    const labelEl = document.getElementById('selectedTableLabel');
    if (labelEl) labelEl.textContent = tno;
    const cartTableEl = document.getElementById('cartTableNo');
    if (cartTableEl) cartTableEl.textContent = tno;

    document.querySelectorAll('.table-cell').forEach(c => c.classList.remove('selected'));
    el.className = 'table-cell selected';
    el.querySelector('span:last-child').textContent = 'Selected';
    
    // Dynamically sync and load existing unpaid items for the selected table
    fetch(`/api/orders/restaurant/${RESTAURANT_ID}`)
      .then(res => res.json())
      .then(data => {
        const ordersList = Array.isArray(data.data) ? data.data : (data.data?.data || []);
        if (data.success && Array.isArray(ordersList)) {
          const activeOrder = ordersList.find(order => String(order.table_id) === String(id) && order.payment_status === 'pending');
          if (activeOrder && Array.isArray(activeOrder.items)) {
            MENU.forEach(item => item.qty = 0);
            activeOrder.items.forEach(orderItem => {
              const menuItem = MENU.find(m => String(m.id) === String(orderItem.menu_item_id));
              if (menuItem) {
                menuItem.qty = orderItem.quantity;
              }
            });
            renderMenu();
            renderCart();
            showToast('Loaded active items for Table ' + tno, 'info');
          } else {
            clearCartSilent();
          }
        }
      })
      .catch(err => {
        console.log('Error syncing table order:', err);
      });

    showToast('Table ' + tno + ' selected', 'success');
    setTimeout(() => closeModal('tableModal'), 600);
  }

  let allOrdersData = [];

  async function loadOrdersData() {
    const tbody = document.getElementById('ordersModalTableBody');
    const badge = document.getElementById('posOrdersCountBadge');
    if (!tbody) return;

    try {
      const response = await fetch(`/api/orders/restaurant/${RESTAURANT_ID}`);
      const data = await response.json();
      const ordersList = Array.isArray(data.data) ? data.data : (Array.isArray(data) ? data : (data.data?.data || []));
      
      allOrdersData = ordersList;
      if (badge) badge.innerText = `${ordersList.length} Orders`;

      if (ordersList.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align:center; padding:36px 20px; color:var(--text-muted);">
              <i class="fa-solid fa-receipt" style="font-size:36px; margin-bottom:10px; display:block; opacity:0.4;"></i>
              <div style="font-size:14px; font-weight:700; color:var(--text-primary);">No Orders Recorded</div>
              <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">New customer orders will appear here in real-time.</div>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = ordersList.map(order => {
        const isPaid = (String(order.payment_status).toLowerCase() === 'paid');
        const payBg = isPaid ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)';
        const payColor = isPaid ? '#34D399' : '#FBBF24';
        const payBorder = isPaid ? 'rgba(16,185,129,0.3)' : 'rgba(245,158,11,0.3)';
        const payText = isPaid ? 'Paid' : 'Pending';

        const statusColor = (order.status === 'completed') ? '#34D399' : ((order.status === 'cooking' || order.status === 'preparing') ? '#FBBF24' : '#818CF8');
        const tableName = order.table ? `Table ${order.table.table_number || order.table_id}` : (order.table_id && order.table_id != '0' ? `Table ${order.table_id}` : '🛵 Takeaway / Delivery');

        return `
          <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='transparent'">
            <td style="padding:12px; font-weight:700; color:var(--text-primary);">
              #${order.id}
              <div style="font-size:10.5px; color:var(--text-muted); font-weight:400; margin-top:2px;">${new Date(order.created_at || Date.now()).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
            </td>
            <td style="padding:12px; color:var(--text-secondary); font-weight:600;">${tableName}</td>
            <td style="padding:12px; font-weight:800; color:var(--text-primary);">₹${parseFloat(order.total_amount || 0).toFixed(2)}</td>
            <td style="padding:12px;">
              <span style="background:${payBg}; color:${payColor}; border:1px solid ${payBorder}; font-size:11px; font-weight:700; padding:3px 8px; border-radius:12px;">${payText}</span>
            </td>
            <td style="padding:12px; font-weight:700; color:${statusColor}; text-transform:capitalize;">${order.status || 'Received'}</td>
            <td style="padding:12px; text-align:right;">
              ${typeof renderOrderActionsHtml === 'function' ? renderOrderActionsHtml(order) : `
                <div style="display:inline-flex; gap:6px; align-items:center;">
                  ${!isPaid ? `<button onclick="payOrder(${order.id})" style="background:rgba(56,189,248,0.15); border:1px solid rgba(56,189,248,0.3); color:#38BDF8; padding:5px 10px; border-radius:8px; font-size:11.5px; font-weight:700; cursor:pointer;" title="Mark Paid"><i class="fa-solid fa-money-bill-wave"></i> Pay</button>` : ''}
                  ${!isPaid ? `<button onclick="viewOrderItemsModal(${order.id})" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#F87171; padding:5px 10px; border-radius:8px; font-size:11.5px; font-weight:700; cursor:pointer;" title="Cancel KOT Item with Manager PIN"><i class="fa-solid fa-ban"></i> Cancel Item</button>` : ''}
                  <a href="/orders/${order.id}/receipt" target="_blank" style="background:var(--primary-light); border:1px solid var(--border); color:var(--primary); padding:5px 10px; border-radius:8px; text-decoration:none; font-size:11.5px; font-weight:600; transition:all 0.2s;" title="Print Thermal Receipt"><i class="fa-solid fa-print"></i> Receipt</a>
                  <a href="/orders/${order.id}/invoice-a4" target="_blank" style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#34D399; padding:5px 10px; border-radius:8px; text-decoration:none; font-size:11.5px; font-weight:600; transition:all 0.2s;" title="View A4 PDF Invoice"><i class="fa-solid fa-file-invoice"></i> A4 PDF</a>
                  <a href="/orders/${order.id}/receipt/kitchen" target="_blank" style="background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); color:#FBBF24; padding:5px 10px; border-radius:8px; text-decoration:none; font-size:11.5px; font-weight:600; transition:all 0.2s;" title="View Kitchen KOT"><i class="fa-solid fa-fire"></i> KOT</a>
                </div>
              `}
            </td>
          </tr>
        `;
      }).join('');
    } catch (err) {
      console.log('Error loading orders:', err);
      tbody.innerHTML = `<tr><td colspan="6" style="padding:24px; text-align:center; color:#F87171;"><i class="fa-solid fa-triangle-exclamation" style="font-size:24px; margin-bottom:6px; display:block;"></i>Failed to load orders data.</td></tr>`;
    }
  }

  async function viewOrderItemsModal(orderId) {
    try {
      const res = await fetch(`/api/orders/${orderId}`);
      const data = await res.json();
      if (!data.success || !data.data) {
        showToast('Could not load order items', 'error');
        return;
      }
      const order = data.data;
      const activeItems = (order.items || []).filter(i => i.kot_status !== 'cancelled');
      if (activeItems.length === 0) {
        showToast('No active items to cancel in this order', 'info');
        return;
      }

      let promptHtml = activeItems.map(item => `
        <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 14px; border-bottom:1px solid var(--border);">
          <div>
            <div style="font-weight:700; color:var(--text-primary); font-size:13.5px;">${item.name}</div>
            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">Quantity: <b>${item.quantity}</b> · Price: <b>₹${item.price}</b></div>
            ${item.notes ? `<div style="font-size:11px; color:#F59E0B; margin-top:2px;">Note: ${item.notes}</div>` : ''}
          </div>
          <button onclick="closeModal('metaPopupModal'); openCancelKotItemModal(${order.id}, ${item.id}, '${escapeHtml(item.name)}')" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#EF4444; border-radius:8px; padding:6px 12px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:5px;">
            <i class="fa-solid fa-trash-can"></i> Cancel
          </button>
        </div>
      `).join('');

      document.getElementById('metaPopupTitle').innerHTML = `<i class="fa-solid fa-shield-halved" style="color:#EF4444;"></i> Select Item to Cancel (#${order.id})`;
      document.getElementById('metaPopupList').innerHTML = promptHtml;
      openModal('metaPopupModal');
    } catch (e) {
      console.error(e);
      showToast('Error loading order items', 'error');
    }
  }

  async function payOrder(orderId) {
    try {
      const response = await fetch(`/api/orders/${orderId}/pay`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ payment_method: 'cash' })
      });
      const data = await response.json();
      if (data.success) {
        showToast('Payment verified successfully!', 'success');
        
        // Verbal announcement for manual cash payment
        if (data.data) {
          const order = data.data;
          const table = allTablesData.find(t => String(t.id) === String(order.table_id));
          const tableNumber = table ? table.table_number : `T-${order.table_id}`;
          
          // Calculate proper grand total (subtotal + 5% service charge + 18% GST)
          const subtotal = parseFloat(order.total_amount);
          const serviceCharge = subtotal * 0.05;
          const gst = (subtotal + serviceCharge) * 0.18;
          const grandTotal = Math.round(subtotal + serviceCharge + gst);
          
          announcePaymentVoice(tableNumber, grandTotal, 'cash');
          if (!announcedOrderIds.includes(order.id)) {
            announcedOrderIds.push(order.id);
          }
        }
        
        loadOrdersData();
        loadTablesData();
      }
    } catch (err) {
      showToast('Payment verification failed', 'error');
    }
  }

  function reprintOrder(orderId) {
    lastPlacedOrderId = orderId;
    activeOrderHasKitchen = true;
    activeOrderHasBar = true;
    printBill();
  }

  const RESTAURANT_UPI_QR = @json($restaurant ? $restaurant->getEffectiveUpiQr() : null);
  const RESTAURANT_UPI_VPA = @json($restaurant ? $restaurant->getEffectiveUpiVpa() : null);
  const RESTAURANT_IS_DEMO = @json($restaurant ? $restaurant->isDemo() : false);
  const RESTAURANT_NAME = @json($restaurant ? $restaurant->name : 'Restaurant');

  let currentUpiPayAmount = 0;

  function copyUpiVpa() {
    const input = document.getElementById('merchantVpaInput');
    if (input) {
      navigator.clipboard.writeText(input.value);
      showToast('UPI ID Copied: ' + input.value, 'success');
    }
  }

  function updateUpiQrCodeFromInput() {
    try {
      const imgEl = document.getElementById('upiQrImg');
      const container = imgEl?.parentElement;
      const vpaRow = document.getElementById('merchantVpaRow');

      // 1. If restaurant has uploaded its own static UPI QR image
      if (RESTAURANT_UPI_QR) {
        if (imgEl) {
          imgEl.src = RESTAURANT_UPI_QR;
          imgEl.style.display = 'block';
        }
        if (vpaRow && !RESTAURANT_UPI_VPA) {
          vpaRow.style.display = 'none';
        }
        const notConfiguredMsg = document.getElementById('upiNotConfiguredMsg');
        if (notConfiguredMsg) notConfiguredMsg.style.display = 'none';
        return;
      }

      // 2. If real restaurant without uploaded QR or VPA
      if (!RESTAURANT_IS_DEMO && !RESTAURANT_UPI_QR && !RESTAURANT_UPI_VPA) {
        if (imgEl) imgEl.style.display = 'none';
        let notConfiguredMsg = document.getElementById('upiNotConfiguredMsg');
        if (!notConfiguredMsg && container) {
          notConfiguredMsg = document.createElement('div');
          notConfiguredMsg.id = 'upiNotConfiguredMsg';
          notConfiguredMsg.style.cssText = 'padding:20px 10px; text-align:center; color:#EF4444; font-weight:700; font-size:12.5px;';
          notConfiguredMsg.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="font-size:24px; display:block; margin-bottom:8px;"></i>UPI QR is not configured for this restaurant.<br><span style="font-size:11px; color:var(--text-muted); font-weight:normal;">Configure in Settings &rarr; Payments</span>';
          container.appendChild(notConfiguredMsg);
        }
        if (notConfiguredMsg) notConfiguredMsg.style.display = 'block';
        if (vpaRow) vpaRow.style.display = 'none';
        return;
      }

      // 3. Dynamic QR with VPA (For Demo or configured restaurant VPA)
      const notConfiguredMsg = document.getElementById('upiNotConfiguredMsg');
      if (notConfiguredMsg) notConfiguredMsg.style.display = 'none';
      if (imgEl) imgEl.style.display = 'block';
      if (vpaRow) vpaRow.style.display = 'flex';

      const inputVal = document.getElementById('merchantVpaInput')?.value.trim() || RESTAURANT_UPI_VPA || (RESTAURANT_IS_DEMO ? '7777938338@omni' : '');
      if (inputVal) localStorage.setItem('dineflow_merchant_vpa', inputVal);
      const tableNumber = document.getElementById('selectedTableNo')?.textContent || document.getElementById('selectedTableLabel')?.textContent || 'Walk-In';
      
      const payAmount = (currentUpiPayAmount && currentUpiPayAmount > 0) ? Math.round(currentUpiPayAmount) : 1;
      const merchantName = RESTAURANT_NAME || 'DineFlow Restaurant';
      
      const upiString = `upi://pay?pa=${encodeURIComponent(inputVal)}&pn=${encodeURIComponent(merchantName)}&am=${payAmount}&cu=INR&tn=${encodeURIComponent('Bill Payment ' + tableNumber)}`;
      const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=${encodeURIComponent(upiString)}`;
      
      if (imgEl) {
        imgEl.src = qrUrl;
        imgEl.onerror = function() {
          this.onerror = null;
          this.src = `https://chart.googleapis.com/chart?chs=280x280&cht=qr&chl=${encodeURIComponent(upiString)}&choe=UTF-8`;
        };
      }
    } catch (e) {
      console.warn('QR update error:', e);
    }
  }

  function openUpiQrModal(customAmount = null, customOrderId = null, customTableNo = null) {
    // 1. Force Modal Open IMMEDIATELY
    const modal = document.getElementById('upiQrModal');
    if (modal) {
      modal.style.zIndex = '99999';
      modal.style.display = 'flex';
      modal.style.opacity = '1';
      modal.style.pointerEvents = 'all';
      modal.classList.add('show');
    }

    try {
      showToast('Opening Live UPI QR Terminal...', 'info');

      let subtotal = 0;
      let serviceCharge = 0;
      let gst = 0;
      let finalGrandTotal = 0;
      let tableNumber = customTableNo || document.getElementById('selectedTableLabel')?.textContent || document.getElementById('selectedTableNo')?.textContent || 'Walk-In';

      if (customAmount !== null && parseFloat(customAmount) > 0) {
        finalGrandTotal = Math.round(parseFloat(customAmount));
        subtotal = Math.round(finalGrandTotal / 1.239);
        serviceCharge = Math.round(subtotal * 0.05);
        gst = Math.round(subtotal * 0.18);
      } else {
        const activeCartItems = (typeof MENU !== 'undefined' && Array.isArray(MENU)) ? MENU.filter(i => i.qty > 0) : [];
        if (activeCartItems.length > 0) {
          subtotal = activeCartItems.reduce((sum, item) => sum + (parseFloat(item.price || 0) * item.qty), 0);
          serviceCharge = subtotal * 0.05;
          gst = (subtotal + serviceCharge) * 0.18;
          finalGrandTotal = Math.round(subtotal + serviceCharge + gst);
        } else if (lastPlacedOrderId && typeof allOrdersData !== 'undefined' && Array.isArray(allOrdersData)) {
          const lastOrder = allOrdersData.find(o => String(o.id) === String(lastPlacedOrderId));
          if (lastOrder) {
            subtotal = parseFloat(lastOrder.total_amount || 0);
            serviceCharge = subtotal * 0.05;
            gst = (subtotal + serviceCharge) * 0.18;
            finalGrandTotal = Math.round(subtotal + serviceCharge + gst);
          }
        } else if (selectedTableId && typeof allOrdersData !== 'undefined' && Array.isArray(allOrdersData)) {
          const activeOrder = allOrdersData.find(o => String(o.table_id) === String(selectedTableId) && String(o.payment_status).toLowerCase() === 'pending');
          if (activeOrder) {
            subtotal = parseFloat(activeOrder.total_amount || 0);
            serviceCharge = subtotal * 0.05;
            gst = (subtotal + serviceCharge) * 0.18;
            finalGrandTotal = Math.round(subtotal + serviceCharge + gst);
          }
        }

        if (finalGrandTotal === 0) {
          const grandTotalText = document.getElementById('grandTotal')?.textContent || '0';
          const parsedVal = parseFloat(grandTotalText.replace(/[^0-9.]/g, ''));
          if (parsedVal > 0) {
            finalGrandTotal = Math.round(parsedVal);
            subtotal = Math.round(finalGrandTotal / 1.239);
            serviceCharge = subtotal * 0.05;
            gst = subtotal * 0.18;
          }
        }
      }

      if (customOrderId) {
        lastPlacedOrderId = customOrderId;
      }

      currentUpiPayAmount = finalGrandTotal;

      const tblEl = document.getElementById('upiModalTable');
      if (tblEl) tblEl.textContent = tableNumber;

      const subEl = document.getElementById('upiModalSubtotal');
      if (subEl) subEl.textContent = '₹' + subtotal.toFixed(2);

      const servEl = document.getElementById('upiModalService');
      if (servEl) servEl.textContent = '₹' + serviceCharge.toFixed(2);

      const gstEl = document.getElementById('upiModalGst');
      if (gstEl) gstEl.textContent = '₹' + gst.toFixed(2);

      const totEl = document.getElementById('upiModalTotal');
      if (totEl) totEl.textContent = '₹' + finalGrandTotal.toFixed(2);

      const brkEl = document.getElementById('upiModalBreakdownTotal');
      if (brkEl) brkEl.textContent = '₹' + finalGrandTotal.toFixed(2);
      const defaultVpa = RESTAURANT_UPI_VPA || (RESTAURANT_IS_DEMO ? '7777938338@omni' : '');
      const savedVpa = RESTAURANT_IS_DEMO ? (localStorage.getItem('dineflow_merchant_vpa') || '7777938338@omni') : defaultVpa;
      if (document.getElementById('merchantVpaInput')) {
        document.getElementById('merchantVpaInput').value = savedVpa;
      }

      updateUpiQrCodeFromInput();
    } catch (err) {
      console.error('Error opening UPI QR Modal:', err);
    }
  }

  async function confirmUpiPaymentCompleted() {
    try {
      const tableNumber = document.getElementById('selectedTableLabel')?.textContent || document.getElementById('selectedTableNo')?.textContent || 'Walk-In';

      document.querySelectorAll('.pay-method').forEach(p => p.classList.remove('active'));
      const upiPayBtn = Array.from(document.querySelectorAll('.pay-method')).find(p => p.textContent.includes('UPI') || p.textContent.includes('GPay'));
      if (upiPayBtn) {
        upiPayBtn.classList.add('active');
      }

      let targetOrderId = lastPlacedOrderId;
      if (!targetOrderId && selectedTableId && typeof allOrdersData !== 'undefined' && Array.isArray(allOrdersData)) {
        const activeOrder = allOrdersData.find(o => String(o.table_id) === String(selectedTableId) && String(o.payment_status).toLowerCase() === 'pending');
        if (activeOrder) targetOrderId = activeOrder.id;
      }

      if (!targetOrderId) {
        try {
          const res = await fetch(`/api/orders/restaurant/${RESTAURANT_ID}`);
          const ordersRes = await res.json();
          const ordersList = Array.isArray(ordersRes.data) ? ordersRes.data : (ordersRes.data?.data || []);
          if (ordersRes.success && Array.isArray(ordersList)) {
            const unpaidOrder = ordersList.find(o => String(o.payment_status).toLowerCase() === 'pending');
            if (unpaidOrder) targetOrderId = unpaidOrder.id;
          }
        } catch(e) {}
      }

      if (targetOrderId) {
        await fetch(`/api/orders/${targetOrderId}/pay`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({ payment_method: 'upi' })
        });

        const targetIdStr = String(targetOrderId);
        if (!announcedOrderIds.includes(targetIdStr)) {
          announcedOrderIds.push(targetIdStr);
          localStorage.setItem('dineflow_announced_orders', JSON.stringify(announcedOrderIds));
        }
      }

      // Live Refresh Orders History List & Table Status!
      if (typeof loadOrdersData === 'function') await loadOrdersData();
      if (typeof loadTablesData === 'function') loadTablesData();

      announcePaymentVoice(tableNumber, currentUpiPayAmount, 'online');
      showToast(`✅ UPI Payment of ₹${currentUpiPayAmount} Verified & Marked PAID for ${tableNumber}!`, 'success');
      closeModal('upiQrModal');

      // Auto open KOT & Print choices modal for the completed order!
      setTimeout(() => {
        openPrintHub(targetOrderId);
      }, 400);
    } catch (err) {
      console.error('Error confirming UPI payment:', err);
      if (typeof loadOrdersData === 'function') await loadOrdersData();
      showToast('Payment verified!', 'success');
      closeModal('upiQrModal');
    }
  }

  /* ─── PAYMENT ────────────────────────────── */
  function selectPay(el) {
    try {
      document.querySelectorAll('.pay-method').forEach(p => p.classList.remove('active'));
      const targetDiv = (el && el.closest) ? el.closest('.pay-method') : el;
      if (targetDiv) {
        targetDiv.classList.add('active');
      }
      
      const labelText = targetDiv ? (targetDiv.textContent || '').trim() : '';
      const splitRow = document.getElementById('splitRow');
      if (splitRow) {
        const isSplit = labelText.toLowerCase().includes('split');
        splitRow.style.display = isSplit ? 'flex' : 'none';
        if (isSplit) {
          calculateSplitRemaining();
        }
      }
    } catch (err) {
      console.error('Error in selectPay:', err);
    }
  }

  function calculateSplitRemaining() {
    const totalEl = document.getElementById('checkoutAmount');
    const totalText = totalEl ? totalEl.textContent.replace(/[^\d.]/g, '') : '0';
    const total = parseFloat(totalText) || 0;

    const cash = parseFloat(document.getElementById('splitCashInput')?.value || 0) || 0;
    const upi = parseFloat(document.getElementById('splitUpiInput')?.value || 0) || 0;
    const card = parseFloat(document.getElementById('splitCardInput')?.value || 0) || 0;

    const entered = cash + upi + card;
    const diff = total - entered;
    const notice = document.getElementById('splitStatusNotice');
    if (!notice) return;

    if (entered === 0) {
      notice.style.color = '#F59E0B';
      notice.textContent = 'Enter amounts for Cash, UPI & Card';
    } else if (Math.abs(diff) < 0.5) {
      notice.style.color = '#10B981';
      notice.innerHTML = '✅ Matched exact ₹' + total.toFixed(0);
    } else if (diff > 0) {
      notice.style.color = '#F59E0B';
      notice.innerHTML = '⚠️ ₹' + diff.toFixed(0) + ' remaining';
    } else {
      notice.style.color = '#EF4444';
      notice.innerHTML = '❌ Over by ₹' + Math.abs(diff).toFixed(0);
    }
  }

  /* ─── COUPON ─────────────────────────────── */
  // Real applyCoupon() is defined above with backend API validation

  /* ─── KOT (DINEFLOW LIVE KITCHEN PIPELINE) ────────────────── */
  function sendKOT() {
    const cartItems = MENU.filter(i => i.qty > 0);
    if (cartItems.length === 0) {
      showToast('Cart is empty! Select items to send KOT.', 'error');
      return;
    }

    const isDelivery = (typeof currentOrderType !== 'undefined' && currentOrderType === 'delivery');
    const isTakeaway = (typeof currentOrderType !== 'undefined' && currentOrderType === 'takeaway');

    const payload = {
      restaurant_id: RESTAURANT_ID,
      table_id: (isDelivery || isTakeaway) ? '0' : String(selectedTableId),
      order_type: currentOrderType || 'dinein',
      customer_name: isDelivery ? (document.getElementById('deliveryCustName')?.value.trim() || selectedCustomerData?.name || 'Home Delivery') : (selectedCustomerData?.name || ''),
      customer_phone: isDelivery ? (document.getElementById('deliveryCustPhone')?.value.trim() || selectedCustomerData?.phone || '') : (selectedCustomerData?.phone || ''),
      customer_address: isDelivery ? (document.getElementById('deliveryCustAddress')?.value.trim() || '') : '',
      special_instructions: document.getElementById('cartNote')?.value || '',
      items: cartItems.map(item => ({
        menu_item_id: item.id,
        name: item.name,
        price: item.price,
        quantity: item.qty,
        notes: item.cookingNote || ''
      }))
    };

    fetch('/api/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const orderIdVal = (data.data && typeof data.data === 'object' && data.data.id) ? data.data.id : (data.data || lastPlacedOrderId);
        lastPlacedOrderId = orderIdVal;
        showToast('🔥 KOT #' + lastPlacedOrderId + ' sent to Kitchen!', 'success');
        clearCart();
        closeModal('kotModal');
        if (typeof loadOrdersData === 'function') loadOrdersData();
        if (typeof loadTablesData === 'function') loadTablesData();
        // Auto open KOT print slip
        if (typeof openPrintHub === 'function') {
          setTimeout(() => openPrintHub(lastPlacedOrderId), 200);
        }
      } else {
        showToast(data.message || 'Failed to send KOT', 'error');
      }
    })
    .catch(err => {
      console.error('KOT error:', err);
      showToast('Network error sending KOT', 'error');
    });
  }

  /* ─── DINEFLOW MULTI-CART HOLD & RECALL ENGINE ─── */
  const HOLD_CARTS_STORAGE_KEY = `dineflow_held_carts_${RESTAURANT_ID}`;

  function getHeldCarts() {
    try {
      const data = localStorage.getItem(HOLD_CARTS_STORAGE_KEY);
      return data ? JSON.parse(data) : [];
    } catch(e) {
      return [];
    }
  }

  function saveHeldCarts(carts) {
    try {
      localStorage.setItem(HOLD_CARTS_STORAGE_KEY, JSON.stringify(carts));
      updateHeldCartsCountBadge();
    } catch(e) {
      console.error('Failed to save held carts:', e);
    }
  }

  function updateHeldCartsCountBadge() {
    const carts = getHeldCarts();
    const count = carts.length;
    const badge = document.getElementById('holdCartsCountBadge');
    const modalBadge = document.getElementById('holdCartsModalBadge');
    if (badge) {
      badge.textContent = count;
      badge.style.display = count > 0 ? 'inline-block' : 'none';
    }
    if (modalBadge) {
      modalBadge.textContent = `${count} Held`;
    }
  }

  // Alias for backward compatibility with topbar / cart triggers
  function holdOrder() {
    holdCurrentCart();
  }

  // ─── LIVE DINEFLOW-STYLE INVENTORY QUICK VIEW IN POS ───
  let posInventoryCache = [];

  async function openInventoryQuickModal() {
    const tbody = document.getElementById('inventoryModalTbody');
    const searchInput = document.getElementById('invQuickSearchInput');
    if (searchInput) searchInput.value = '';

    if (tbody) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted);">
            <i class="fa-solid fa-spinner fa-spin" style="font-size:22px; margin-bottom:10px; display:block; color:var(--primary);"></i>
            Fetching live ingredient & stock levels...
          </td>
        </tr>
      `;
    }

    try {
      const restId = CURRENT_RESTAURANT_ID || '{{ Auth::user()->restaurant_id ?? 1 }}';
      const res = await fetch(`/inventory?restaurant_id=${encodeURIComponent(restId)}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data && data.success && Array.isArray(data.data)) {
        posInventoryCache = data.data;
      } else if (Array.isArray(data)) {
        posInventoryCache = data;
      } else {
        posInventoryCache = [];
      }
      renderQuickInventoryTable(posInventoryCache);
      updateInventoryBadgeCounts(posInventoryCache);
    } catch (err) {
      console.warn('Inventory fetch error, checking local fallback:', err);
      // Fallback: derive from items in current menu with low stock indicator if available
      renderQuickInventoryTable(posInventoryCache);
    }
  }

  function updateInventoryBadgeCounts(items) {
    const lowCount = items.filter(i => {
      const qty = parseFloat(i.quantity ?? 0);
      const alert = parseFloat(i.low_stock_alert ?? 10);
      return qty <= alert;
    }).length;

    const topBadge = document.getElementById('posInventoryLowCountBadge');
    if (topBadge) {
      topBadge.textContent = lowCount;
      topBadge.style.display = lowCount > 0 ? 'inline-block' : 'none';
    }

    const modalAlertBadge = document.getElementById('invLowStockAlertBadge');
    if (modalAlertBadge) {
      modalAlertBadge.textContent = `${lowCount} Low Stock`;
      modalAlertBadge.style.display = lowCount > 0 ? 'inline-block' : 'none';
    }

    const totalBadge = document.getElementById('invTotalCountBadge');
    if (totalBadge) totalBadge.textContent = `${items.length} Total Items`;

    const modalLowBadge = document.getElementById('invLowCountBadge');
    if (modalLowBadge) modalLowBadge.textContent = `${lowCount} Low / Out`;
  }

  function renderQuickInventoryTable(items) {
    const tbody = document.getElementById('inventoryModalTbody');
    if (!tbody) return;

    if (!items || items.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="5" style="text-align:center; padding:36px; color:var(--text-muted);">
            <i class="fa-solid fa-box-open" style="font-size:28px; margin-bottom:8px; display:block; opacity:0.4;"></i>
            No inventory items or ingredients found for this branch.<br>
            <a href="/admin/inventory" target="_blank" style="color:var(--primary); font-weight:700; text-decoration:underline; font-size:12px; display:inline-block; margin-top:8px;">Manage in Full Inventory &rarr;</a>
          </td>
        </tr>
      `;
      return;
    }

    tbody.innerHTML = items.map(item => {
      const qty = parseFloat(item.quantity ?? 0);
      const alertLevel = parseFloat(item.low_stock_alert ?? 10);
      const unit = item.unit || 'units';
      const isOut = qty <= 0;
      const isLow = !isOut && qty <= alertLevel;

      let statusBadge = `<span style="background:rgba(16,185,129,0.12); color:#10B981; font-weight:700; font-size:11px; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid fa-circle-check" style="font-size:8px;"></i> In Stock</span>`;
      if (isOut) {
        statusBadge = `<span style="background:rgba(239,68,68,0.14); color:#EF4444; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid fa-circle-xmark" style="font-size:8px;"></i> Out of Stock</span>`;
      } else if (isLow) {
        statusBadge = `<span style="background:rgba(245,158,11,0.14); color:#D97706; font-weight:800; font-size:11px; padding:3px 8px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid fa-triangle-exclamation" style="font-size:8px;"></i> Low Alert</span>`;
      }

      return `
        <tr style="border-bottom:1px solid var(--border); transition:background 0.15s ease;" onmouseover="this.style.background='var(--hover-bg, rgba(0,0,0,0.02))'" onmouseout="this.style.background='transparent'">
          <td style="padding:10px 10px; font-weight:700; color:var(--text-primary);">
            <div style="display:flex; align-items:center; gap:8px;">
              <span style="width:8px; height:8px; border-radius:50%; background:${isOut ? '#EF4444' : isLow ? '#F59E0B' : '#10B981'}; display:inline-block;"></span>
              <span>${escapeHtml(item.item_name || 'Unnamed')}</span>
            </div>
          </td>
          <td style="padding:10px 10px; color:var(--text-muted); font-size:12px;">
            ${escapeHtml(item.category || 'General')}
          </td>
          <td style="padding:10px 10px; text-align:right; font-weight:800; font-size:13px; color:${isOut ? '#EF4444' : isLow ? '#D97706' : 'var(--text-primary)'};">
            ${qty.toLocaleString()} <span style="font-size:10.5px; font-weight:500; color:var(--text-muted);">${escapeHtml(unit)}</span>
          </td>
          <td style="padding:10px 10px; text-align:right; color:var(--text-muted); font-size:12px;">
            ${alertLevel} ${escapeHtml(unit)}
          </td>
          <td style="padding:10px 10px; text-align:center;">
            ${statusBadge}
          </td>
        </tr>
      `;
    }).join('');
  }

  function filterQuickInventory(query) {
    const q = (query || '').toLowerCase().trim();
    if (!q) {
      renderQuickInventoryTable(posInventoryCache);
      return;
    }
    const filtered = posInventoryCache.filter(item => 
      (item.item_name || '').toLowerCase().includes(q) ||
      (item.category || '').toLowerCase().includes(q)
    );
    renderQuickInventoryTable(filtered);
  }

  function holdCurrentCart() {
    const activeItems = MENU.filter(i => i.qty > 0);
    if (activeItems.length === 0) {
      showToast('Cannot hold an empty cart! Add items first.', 'warning');
      return;
    }

    const cartSubtotal = activeItems.reduce((sum, item) => sum + (parseFloat(item.price || 0) * item.qty), 0);
    const holdTitle = prompt('Enter a label/note for this held cart (e.g. "Table 4 Phone Call" or "Rahul Bhai"):', 
      (selectedCustomerData?.name && selectedCustomerData.name !== 'Walk-in Customer') ? selectedCustomerData.name : `Cart #${Date.now().toString().slice(-4)}`
    );

    if (holdTitle === null) return; // User cancelled prompt

    const heldCartObj = {
      id: 'hold_' + Date.now(),
      title: holdTitle.trim() || `Held Cart (${new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' })})`,
      timestamp: new Date().toISOString(),
      timeFormatted: new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit' }),
      tableId: selectedTableId || 1,
      orderType: currentOrderType || 'dinein',
      customer: selectedCustomerData ? { ...selectedCustomerData } : null,
      note: document.getElementById('cartNote')?.value || '',
      items: activeItems.map(i => ({
        id: i.id,
        name: i.name,
        price: i.price,
        qty: i.qty,
        cookingNote: i.cookingNote || ''
      })),
      totalAmount: cartSubtotal
    };

    const carts = getHeldCarts();
    carts.unshift(heldCartObj);
    saveHeldCarts(carts);

    clearCart();
    showToast(`⏸️ Cart "${heldCartObj.title}" placed on Hold!`, 'success');
    addPosNotification({
      id: `hold-${Date.now()}`,
      title: `⏸️ Cart Held: ${heldCartObj.title}`,
      message: `${heldCartObj.items.length} items saved. You can recall it anytime from Hold Carts (F4).`,
      time: 'Just now',
      type: 'warning',
      icon: 'fa-solid fa-circle-pause'
    });
  }

  function openHoldOrdersModal() {
    renderHoldOrdersList();
    openModal('holdOrdersModal');
  }

  function renderHoldOrdersList() {
    const container = document.getElementById('holdCartsListContainer');
    if (!container) return;
    const carts = getHeldCarts();
    updateHeldCartsCountBadge();

    if (carts.length === 0) {
      container.innerHTML = `
        <div style="text-align:center; padding:48px 16px; color:var(--text-muted);">
          <i class="fa-solid fa-circle-pause" style="font-size:36px; margin-bottom:12px; display:block; color:#EC4899; opacity:0.6;"></i>
          <h4 style="margin:0 0 4px; font-size:15px; color:var(--text-primary);">No Carts On Hold</h4>
          <p style="font-size:12.5px; margin:0;">When a customer pauses their order, click <b>Hold (F4)</b> in the cart to hold it here.</p>
        </div>
      `;
      return;
    }

    container.innerHTML = carts.map((c, idx) => {
      const itemsListText = c.items.map(i => `${i.qty}x ${escapeHtml(i.name)}`).join(', ');
      return `
        <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border); border-radius:16px; padding:14px 18px; display:flex; justify-content:space-between; align-items:center; gap:14px; transition:all 0.2s;" onmouseover="this.style.borderColor='rgba(236,72,153,0.4)'" onmouseout="this.style.borderColor='var(--border)'">
          <div style="flex:1; min-width:0;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
              <span style="font-size:14px; font-weight:800; color:var(--text-primary);">${escapeHtml(c.title)}</span>
              <span style="font-size:10px; font-weight:700; color:#EC4899; background:rgba(236,72,153,0.12); padding:1px 6px; border-radius:6px;">🕒 ${c.timeFormatted}</span>
              ${c.customer?.name && c.customer.name !== 'Walk-in Customer' ? `<span style="font-size:10px; font-weight:700; color:#6366F1; background:rgba(99,102,241,0.12); padding:1px 6px; border-radius:6px;">👤 ${escapeHtml(c.customer.name)}</span>` : ''}
            </div>
            <div style="font-size:11.5px; color:var(--text-muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
              ${itemsListText}
            </div>
            <div style="font-size:12px; font-weight:700; color:#10B981; margin-top:4px;">
              Subtotal: ₹${parseFloat(c.totalAmount || 0).toFixed(2)} (${c.items.reduce((s,i)=>s+i.qty,0)} items)
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
            <button onclick="recallHeldCart('${c.id}')" style="background:linear-gradient(135deg, #EC4899, #DB2777); border:none; border-radius:10px; color:#fff; font-size:12px; font-weight:700; padding:8px 14px; cursor:pointer; display:flex; align-items:center; gap:6px; box-shadow:0 4px 12px rgba(236,72,153,0.3);">
              <i class="fa-solid fa-arrow-rotate-left"></i> Recall
            </button>
            <button onclick="deleteHeldCart('${c.id}')" style="background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:#EF4444; border-radius:10px; width:34px; height:34px; cursor:pointer; display:flex; align-items:center; justify-content:center;" title="Discard this held cart">
              <i class="fa-solid fa-trash-can" style="font-size:12px;"></i>
            </button>
          </div>
        </div>
      `;
    }).join('');
  }

  function recallHeldCart(holdId) {
    const carts = getHeldCarts();
    const cartIdx = carts.findIndex(c => c.id === holdId);
    if (cartIdx === -1) {
      showToast('Held cart not found', 'error');
      return;
    }

    const currentActiveItems = MENU.filter(i => i.qty > 0);
    if (currentActiveItems.length > 0) {
      if (!confirm('Current active cart has items. Replacing it with the recalled cart. Do you want to proceed?')) {
        return;
      }
    }

    const targetCart = carts[cartIdx];

    // Reset MENU qty
    MENU.forEach(item => { item.qty = 0; item.cookingNote = ''; });

    // Populate recalled items
    targetCart.items.forEach(hItem => {
      const match = MENU.find(m => String(m.id) === String(hItem.id));
      if (match) {
        match.qty = hItem.qty;
        if (hItem.cookingNote) match.cookingNote = hItem.cookingNote;
      }
    });

    // Restore table & customer
    if (targetCart.tableId) selectedTableId = targetCart.tableId;
    if (targetCart.customer) {
      selectedCustomerData = targetCart.customer;
      const strip = document.querySelector('.customer-strip');
      if (strip) {
        const avatarEl = strip.querySelector('.cust-avatar');
        const nameEl = strip.querySelector('.cust-info h5');
        const subEl = strip.querySelector('.cust-info p');
        if (avatarEl) avatarEl.textContent = selectedCustomerData.avatar || 'WC';
        if (nameEl) nameEl.textContent = selectedCustomerData.name;
        if (subEl) subEl.innerHTML = `${selectedCustomerData.phone || 'Walk-in'} &nbsp;·&nbsp; <span style="color:var(--primary);">⭐ ${selectedCustomerData.points || 0} pts</span>`;
      }
    }

    const noteEl = document.getElementById('cartNote');
    if (noteEl && targetCart.note) noteEl.value = targetCart.note;

    // Remove from held carts list
    carts.splice(cartIdx, 1);
    saveHeldCarts(carts);

    closeModal('holdOrdersModal');
    renderMenu();
    renderCart();
    showToast(`⚡ Recalled cart "${targetCart.title}"! Ready for checkout.`, 'success');
  }

  function deleteHeldCart(holdId) {
    if (!confirm('Are you sure you want to discard this held cart?')) return;
    let carts = getHeldCarts();
    carts = carts.filter(c => c.id !== holdId);
    saveHeldCarts(carts);
    renderHoldOrdersList();
    showToast('Held cart discarded', 'info');
  }
  
  let lastPlacedOrderId = null;
  let activeOrderHasKitchen = true; // Fallback to true if manually clicked
  let activeOrderHasBar = true;
  let selectedTableId = 1;

  function checkout() {
    const cartItems = MENU.filter(i => i.qty > 0);
    if (cartItems.length === 0) {
      showToast('Cart is empty! Please select items from menu first.', 'warning');
      return;
    }

    const isDelivery = (typeof currentOrderType !== 'undefined' && currentOrderType === 'delivery');
    const isTakeaway = (typeof currentOrderType !== 'undefined' && currentOrderType === 'takeaway');
    const deliveryFee = isDelivery ? (parseFloat(document.getElementById('deliveryFeeInput')?.value || 0) || 0) : 0;

    // Calculate exact cart total before clearing cart
    const cartSubtotal = cartItems.reduce((sum, item) => sum + (parseFloat(item.price || 0) * item.qty), 0);
    const cartServiceCharge = cartSubtotal * 0.05;
    const cartGst = (cartSubtotal + cartServiceCharge) * 0.18;
    const calculatedOrderTotal = Math.round(cartSubtotal + cartServiceCharge + cartGst + deliveryFee);

    // Determine if we need to print Kitchen KOT or Bar KOT based on cart categories
    activeOrderHasKitchen = cartItems.some(item => ['mains', 'starters', 'breads', 'rice', 'pizza', 'burger', 'pasta', 'soup'].includes((item.cat || '').toLowerCase()));
    activeOrderHasBar = cartItems.some(item => ['beverages', 'desserts', 'drinks', 'dessert'].includes((item.cat || '').toLowerCase()));

    const activePayMethod = document.querySelector('.pay-method.active')?.textContent.trim() || 'Cash';
    const isSplit = activePayMethod.toLowerCase().includes('split');
    let splitDataObj = null;

    if (isSplit) {
      const cash = parseFloat(document.getElementById('splitCashInput')?.value || 0) || 0;
      const upi = parseFloat(document.getElementById('splitUpiInput')?.value || 0) || 0;
      const card = parseFloat(document.getElementById('splitCardInput')?.value || 0) || 0;
      const totalSplit = cash + upi + card;
      if (Math.abs(totalSplit - calculatedOrderTotal) > 1) {
        showToast(`Split total (₹${totalSplit.toFixed(0)}) does not match bill total (₹${calculatedOrderTotal.toFixed(0)})!`, 'error');
        return;
      }
      splitDataObj = { cash, upi, card };
    }

    const payload = {
      restaurant_id: RESTAURANT_ID,
      table_id: (isDelivery || isTakeaway) ? '0' : String(selectedTableId),
      order_type: currentOrderType || 'dinein',
      payment_method: isSplit ? 'split' : (activePayMethod.toLowerCase().includes('upi') ? 'upi' : (activePayMethod.toLowerCase().includes('card') ? 'card' : 'cash')),
      split_data: splitDataObj ? JSON.stringify(splitDataObj) : null,
      customer_name: isDelivery ? (document.getElementById('deliveryCustName')?.value.trim() || selectedCustomerData?.name || 'Home Delivery') : (selectedCustomerData?.name || ''),
      customer_phone: isDelivery ? (document.getElementById('deliveryCustPhone')?.value.trim() || selectedCustomerData?.phone || '') : (selectedCustomerData?.phone || ''),
      customer_address: isDelivery ? (document.getElementById('deliveryCustAddress')?.value.trim() || '') : '',
      customer_gstin: document.getElementById('b2bInvoiceToggle')?.checked ? (document.getElementById('b2bGstinInput')?.value.trim().toUpperCase() || null) : null,
      customer_company: document.getElementById('b2bInvoiceToggle')?.checked ? (document.getElementById('b2bCompanyInput')?.value.trim() || null) : null,
      delivery_fee: deliveryFee,
      wallet_discount: appliedWalletDiscount || 0,
      cashback_earned: Math.round(calculatedOrderTotal * 0.05 * 100) / 100,
      rider_id: isDelivery ? (document.getElementById('deliveryRiderSelect')?.value || null) : null,
      special_instructions: document.getElementById('cartNote')?.value || '',
      items: cartItems.map(item => ({
        menu_item_id: item.id,
        name: item.name,
        price: item.price,
        quantity: item.qty,
        notes: item.cookingNote || ''
      }))
    };

    fetch('/api/orders', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const orderIdVal = (data.data && typeof data.data === 'object' && data.data.id) ? data.data.id : (data.data || lastPlacedOrderId);
        lastPlacedOrderId = orderIdVal;
        showToast('Order placed successfully! ID: #' + lastPlacedOrderId, 'success');

        // 🌟 Auto-credit 5% Digital Cashback to Customer Wallet
        const custPhone = payload.customer_phone;
        if (custPhone && custPhone !== '') {
          fetch('/api/customers/credit-cashback', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
              restaurant_id: RESTAURANT_ID,
              phone: custPhone,
              order_amount: calculatedOrderTotal,
              name: payload.customer_name || 'Customer'
            })
          })
          .then(r => r.json())
          .then(cData => {
            if (cData.success) {
              showToast(`🎁 Credited ₹${cData.cashback_earned} (5% Cashback) to customer wallet!`, 'success');
              if (selectedCustomerData) {
                selectedCustomerData.wallet_balance = cData.new_balance;
                const wAmt = document.getElementById('custWalletAmount');
                if (wAmt) wAmt.textContent = `₹${cData.new_balance.toFixed(2)}`;
              }
            }
          })
          .catch(e => console.log('Cashback credit error:', e));
        }

        // 🌟 Auto-Calculate & Award Customer Loyalty Points based on System Settings Rate
        const earnRate = Math.max(1, parseInt(localStorage.getItem('dineflow_point_earn_rate')) || 100);
        const earnedPts = Math.floor((calculatedOrderTotal || 0) / earnRate);

        if (earnedPts > 0 && selectedCustomerData && selectedCustomerData.name && selectedCustomerData.name !== 'Walk-in Customer') {
          fetch('/api/customers/earn-points', {
            method: 'POST',
            headers: { 
              'Content-Type': 'application/json', 
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' 
            },
            body: JSON.stringify({
              name: selectedCustomerData.name,
              phone: selectedCustomerData.phone || '',
              earned_points: earnedPts,
              order_amount: calculatedOrderTotal
            })
          })
          .then(r => r.json())
          .then(resData => {
            if (resData.success) {
              selectedCustomerData.points = (selectedCustomerData.points || 0) + earnedPts;
              showToast(`⭐ ${selectedCustomerData.name} earned +${earnedPts} Loyalty Points on this ₹${calculatedOrderTotal.toFixed(0)} order!`, 'success');
              addPosNotification({
                id: `points-${Date.now()}`,
                title: `⭐ +${earnedPts} Points Earned!`,
                message: `${selectedCustomerData.name} earned +${earnedPts} reward points on bill checkout.`,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                type: 'warning',
                icon: 'fa-solid fa-star'
              });
              if (typeof fetchAndRenderPosCustomers === 'function') fetchAndRenderPosCustomers();
            }
          })
          .catch(e => console.log('Auto points error:', e));
        }

        appliedWalletDiscount = 0;

        clearCart();
        if (typeof loadOrdersData === 'function') loadOrdersData();
        if (typeof loadTablesData === 'function') loadTablesData();
        
        const activePayMethod = document.querySelector('.pay-method.active')?.textContent || '';
        if (activePayMethod.toLowerCase().includes('upi') || activePayMethod.toLowerCase().includes('qr') || activePayMethod.toLowerCase().includes('gpay')) {
          const tableLabel = document.getElementById('selectedTableLabel')?.textContent || document.getElementById('selectedTableNo')?.textContent || 'Walk-In';
          setTimeout(() => {
            openUpiQrModal(calculatedOrderTotal, lastPlacedOrderId, tableLabel);
          }, 150);
        } else {
          setTimeout(() => {
            openPrintHub(lastPlacedOrderId);
          }, 150);
        }
      } else {
        showToast(data.message || 'Failed to place order', 'error');
      }
    })
    .catch(err => {
      showToast('Error connecting to backend', 'error');
    });
  }

  function printBill() {
    if (!lastPlacedOrderId) {
      showToast('Please place an order first to print bill!', 'error');
      return;
    }
    // 1. Customer Bill Invoice (Always prints)
    window.open('/orders/' + lastPlacedOrderId + '/receipt', '_blank');
  }

  function printA4Invoice() {
    if (lastPlacedOrderId) {
      window.open('/orders/' + lastPlacedOrderId + '/invoice-a4', '_blank');
      return;
    }

    const activeCartItems = MENU.filter(i => i.qty > 0);
    const tableNumber = document.getElementById('selectedTableNo')?.textContent || document.getElementById('selectedTableLabel')?.textContent || 'Walk-In';

    if (activeCartItems.length > 0) {
      const itemsPayload = activeCartItems.map(i => ({ name: i.name, qty: i.qty, price: i.price }));
      const itemsJson = encodeURIComponent(JSON.stringify(itemsPayload));
      window.open('/orders/live/invoice-a4?table=' + encodeURIComponent(tableNumber) + '&items=' + itemsJson, '_blank');
    } else if (typeof allOrdersData !== 'undefined' && Array.isArray(allOrdersData) && allOrdersData.length > 0) {
      window.open('/orders/' + allOrdersData[0].id + '/invoice-a4', '_blank');
    } else {
      showToast('Please place an order or select items to print Designed A4 Tax Bill!', 'error');
    }
  }

  let currentWaMessage = "";

  function sendWhatsAppInvoice(customOrderData = null) {
    try {
      let tableNumber = document.getElementById('selectedTableNo')?.textContent || document.getElementById('selectedTableLabel')?.textContent || 'Walk-In';
      let subtotal = 0;
      let itemsText = "";
      const activeCartItems = MENU.filter(i => i.qty > 0);

      if (customOrderData) {
        tableNumber = customOrderData.table_id ? 'Table #' + customOrderData.table_id : 'Walk-In';
        if (Array.isArray(customOrderData.items)) {
          customOrderData.items.forEach(item => {
            const lTotal = (item.unit_price || item.price || 0) * (item.quantity || item.qty || 1);
            subtotal += lTotal;
            itemsText += `• ${item.quantity || item.qty}x ${item.name || item.menu_item?.name || 'Item'} - ₹${lTotal}\n`;
          });
        }
      } else if (activeCartItems.length > 0) {
        activeCartItems.forEach(item => {
          const lineTotal = item.price * item.qty;
          subtotal += lineTotal;
          itemsText += `• ${item.qty}x ${item.name} - ₹${lineTotal}\n`;
        });
      } else if (typeof lastPlacedOrderId !== 'undefined' && lastPlacedOrderId) {
        window.open('/orders/' + lastPlacedOrderId + '/invoice-a4', '_blank');
        return;
      } else {
        showToast('Please add items to cart first to send WhatsApp bill!', 'error');
        return;
      }

      const serviceCharge = subtotal * 0.05;
      const gst = (subtotal + serviceCharge) * 0.18;
      const grandTotal = Math.round(subtotal + serviceCharge + gst);

      currentWaMessage = `🍽️ *DineFlow AI Restaurant*\n` +
                      `*Official WhatsApp Invoice & Receipt*\n` +
                      `-----------------------------------------\n` +
                      `📍 Table: ${tableNumber}\n` +
                      `-----------------------------------------\n` +
                      `*ORDER ITEMS:*\n` +
                      `${itemsText}` +
                      `-----------------------------------------\n` +
                      `Subtotal: ₹${subtotal.toFixed(2)}\n` +
                      `Service Charge (5%): ₹${serviceCharge.toFixed(2)}\n` +
                      `GST (18%): ₹${gst.toFixed(2)}\n` +
                      `*GRAND TOTAL: ₹${grandTotal}*\n` +
                      `-----------------------------------------\n` +
                      `Thank you for dining with us! Have a great day! 🙏`;

      const previewBox = document.getElementById('waPreviewBox');
      if (previewBox) previewBox.textContent = currentWaMessage;
      openModal('whatsappModal');
    } catch (err) {
      alert("WhatsApp Invoice Error: " + err.message);
    }
  }

  function triggerWhatsAppRedirect() {
    try {
      let phoneInput = document.getElementById('waPhoneInput');
      let phone = phoneInput ? phoneInput.value.trim() : '9876543210';
      if (!phone) {
        alert('Please enter a valid 10-digit mobile number!');
        return;
      }
      
      phone = phone.replace(/\D/g, '');
      if (phone.length === 10) {
        phone = '91' + phone;
      }

      const waUrl = `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(currentWaMessage)}`;
      window.open(waUrl, '_blank');
      closeModal('whatsappModal');
    } catch (err) {
      alert("WhatsApp Open Error: " + err.message);
    }
  }

  async function openPrintHub(orderId = null) {
    if (orderId) {
      lastPlacedOrderId = orderId;
    } else if (!lastPlacedOrderId && typeof allOrdersData !== 'undefined' && Array.isArray(allOrdersData) && allOrdersData.length > 0) {
      lastPlacedOrderId = allOrdersData[0].id;
    }
    
    if (!lastPlacedOrderId) {
      try {
        const res = await fetch(`/api/orders/restaurant/${RESTAURANT_ID}`);
        const data = await res.json();
        const ordersList = Array.isArray(data.data) ? data.data : (data.data?.data || []);
        if (data.success && Array.isArray(ordersList) && ordersList.length > 0) {
          allOrdersData = ordersList;
          lastPlacedOrderId = ordersList[0].id;
        }
      } catch (e) {
        console.log('Error fetching order for print:', e);
      }
    }

    if (!lastPlacedOrderId) {
      showToast('No order available to print. Please place an order first!', 'warning');
      return;
    }

    const orderIdLabel = document.getElementById('printHubOrderId');
    if (orderIdLabel) {
      orderIdLabel.textContent = 'Order ID: #' + (lastPlacedOrderId || '--');
    }
    const kBtn = document.getElementById('printHubKitchenBtn');
    if (kBtn) kBtn.style.display = 'flex';
    const bBtn = document.getElementById('printHubBarBtn');
    if (bBtn) bBtn.style.display = 'flex';

    const modal = document.getElementById('printHubModal');
    if (modal) {
      modal.style.zIndex = '999999';
      modal.style.display = 'flex';
      modal.style.opacity = '1';
      modal.style.pointerEvents = 'all';
      modal.classList.add('show');
    } else {
      openModal('printHubModal');
    }
  }

  async function triggerPrintHubAction(type) {
    if (!lastPlacedOrderId) return;

    // Direct Silent ESC/POS Thermal Printing Driver
    if (window.dineFlowPrinter && window.dineFlowPrinter.isDirectPrintingEnabled()) {
      try {
        const res = await fetch(`/api/orders/${lastPlacedOrderId}`);
        const data = await res.json();
        const orderData = data.data || data;
        const restData = {
          name: '{{ $restaurant->name ?? "DineFlow Restaurant" }}',
          address: '{{ $restaurant->address ?? "" }}',
          phone: '{{ $restaurant->phone ?? "" }}',
          gstin: '{{ $restaurant->gstin ?? "" }}'
        };

        if (type === 'receipt' || type === 'all') {
          await window.dineFlowPrinter.printReceipt(orderData, restData);
          showToast('⚡ Silent Receipt Printed on Thermal Printer!', 'success');
        }
        if (type === 'kitchen' || type === 'kots_only' || type === 'all') {
          await window.dineFlowPrinter.printKot(orderData, restData.name);
          showToast('🔥 Silent KOT Printed on Thermal Printer!', 'success');
        }
        closeModal('printHubModal');
        return;
      } catch (err) {
        console.warn('Direct ESC/POS print error, fallback to browser print:', err);
      }
    }

    if (type === 'all') {
      window.open('/orders/' + lastPlacedOrderId + '/receipt', '_blank');
      if (activeOrderHasKitchen) {
        window.open('/orders/' + lastPlacedOrderId + '/receipt/kitchen', '_blank');
      }
      if (activeOrderHasBar) {
        window.open('/orders/' + lastPlacedOrderId + '/receipt/bar', '_blank');
      }
    } else if (type === 'kots_only') {
      if (activeOrderHasKitchen) {
        window.open('/orders/' + lastPlacedOrderId + '/receipt/kitchen', '_blank');
      }
      if (activeOrderHasBar) {
        window.open('/orders/' + lastPlacedOrderId + '/receipt/bar', '_blank');
      }
    } else if (type === 'receipt') {
      window.open('/orders/' + lastPlacedOrderId + '/receipt', '_blank');
    } else if (type === 'kitchen') {
      window.open('/orders/' + lastPlacedOrderId + '/receipt/kitchen', '_blank');
    } else if (type === 'bar') {
      window.open('/orders/' + lastPlacedOrderId + '/receipt/bar', '_blank');
    }
  }

  function logout() {
    if (confirm('Are you sure you want to logout?')) {
      // Create a form and submit it as POST with CSRF token
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route("auth.logout") }}';
      form.innerHTML = '@csrf';
      document.body.appendChild(form);
      form.submit();
    }
  }

  // Merged openModal - handles catPopupBtn + null safety
  // (primary definitions already hoisted to top of script)
  document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) closeModal(o.id); });
  });




  /* ─── TOAST ──────────────────────────────── */
  function showToast(msg, type='') {
    const container = document.getElementById('toastContainer');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    const icons = { success:'fa-circle-check', error:'fa-circle-xmark', warning:'fa-triangle-exclamation', '':'fa-info-circle' };
    t.innerHTML = `<i class="fa-solid ${icons[type]||icons['']}"></i> ${msg}`;
    container.appendChild(t);
    setTimeout(() => t.remove(), 2800);
  }

  /* ─── SIDEBAR TOGGLE & STATE MANAGER ─────────────────────── */
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    if (window.innerWidth <= 768) {
      const willOpen = !sidebar.classList.contains('mobile-open');
      if (willOpen && typeof setMobileCart === 'function') setMobileCart(false);
      setMobileSidebar(willOpen);
      return;
    }

    const isCollapsed = sidebar.classList.toggle('collapsed');
    document.body.classList.toggle('sidebar-collapsed-mode', isCollapsed);
    try {
      localStorage.setItem('dineflow_pos_sidebar_collapsed', isCollapsed ? '1' : '0');
    } catch (e) {}

    const toggleBtn = document.getElementById('posSidebarToggleBtn');
    if (toggleBtn) {
      toggleBtn.title = isCollapsed ? 'Expand sidebar (Ctrl+B)' : 'Collapse sidebar (Ctrl+B)';
    }
  }

  function initSidebarState() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;

    if (window.innerWidth <= 768) {
      sidebar.classList.remove('collapsed');
      document.body.classList.remove('sidebar-collapsed-mode');
      return;
    }

    let saved = null;
    try {
      saved = localStorage.getItem('dineflow_pos_sidebar_collapsed');
    } catch (e) {}

    // On desktop, default to expanded (showing full labels) unless user explicitly collapsed it
    const shouldCollapse = saved === '1';

    sidebar.classList.toggle('collapsed', shouldCollapse);
    document.body.classList.toggle('sidebar-collapsed-mode', shouldCollapse);

    const toggleBtn = document.getElementById('posSidebarToggleBtn');
    if (toggleBtn) {
      toggleBtn.title = shouldCollapse ? 'Expand sidebar (Ctrl+B)' : 'Collapse sidebar (Ctrl+B)';
    }
  }

  // Keyboard shortcut Ctrl+B / Cmd+B for sidebar toggle
  window.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
      e.preventDefault();
      toggleSidebar();
    }
  });

  // Self-execute immediately
  initSidebarState();

  function setMobileSidebar(isOpen) {
    const sidebar  = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    sidebar.classList.remove('collapsed');
    sidebar.classList.toggle('mobile-open', isOpen);
    backdrop.classList.toggle('active', isOpen);
  }

  function closeMobileSidebar() {
    setMobileSidebar(false);
  }

  /* ─── MOBILE CART TOGGLE ─────────────────── */
  function setMobileCart(isOpen) {
    const cart     = document.querySelector('.pos-right');
    const backdrop = document.getElementById('cartBackdrop');
    cart.classList.toggle('cart-open', isOpen);
    backdrop.classList.toggle('active', isOpen);
  }

  function toggleMobileCart() {
    const cart   = document.querySelector('.pos-right');
    const isOpen = cart.classList.contains('cart-open');
    if (!isOpen) closeMobileSidebar();
    setMobileCart(!isOpen);
  }

  /* ─── INIT ───────────────────────────────── */
  window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
      closeMobileSidebar();
      setMobileCart(false);
    }
  });

  document.querySelectorAll('#sidebar .nav-item').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 768) closeMobileSidebar();
    });
  });

  function toggleFull() {
    if (!document.fullscreenElement) document.documentElement.requestFullscreen();
    else document.exitFullscreen();
  }

  updateMetaSelections();
  renderMenu();
  renderCart();

  /* ─── WAITER ALERTS POLLING ──────────────── */
  let activeWaiterCallId = null;
  let isWaiterCallActive = false;

  // Synthesize a beautiful, harmonious bell chime natively (no file downloads, no CORS issues)
  function playChimeSound() {
    try {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      if (!AudioContext) return;
      const ctx = new AudioContext();

      // Primary note (B5)
      const osc1 = ctx.createOscillator();
      const gain1 = ctx.createGain();
      osc1.type = 'sine';
      osc1.frequency.setValueAtTime(987.77, ctx.currentTime);
      gain1.gain.setValueAtTime(0.2, ctx.currentTime);
      gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.8);
      osc1.connect(gain1);
      gain1.connect(ctx.destination);

      // Harmonious higher note (E6)
      const osc2 = ctx.createOscillator();
      const gain2 = ctx.createGain();
      osc2.type = 'sine';
      osc2.frequency.setValueAtTime(1318.51, ctx.currentTime);
      gain2.gain.setValueAtTime(0.15, ctx.currentTime);
      gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.0);
      osc2.connect(gain2);
      gain2.connect(ctx.destination);

      osc1.start();
      osc2.start();
      osc1.stop(ctx.currentTime + 0.8);
      osc2.stop(ctx.currentTime + 1.0);
    } catch (e) {
      console.log('Web Audio API chime playback error:', e);
    }
  }

  let activeCallsSet = new Set();
  let isFirstWaiterLoad = true;
  let isCheckingWaiterCalls = false;

  async function checkWaiterCalls() {
    if (isCheckingWaiterCalls) return;
    isCheckingWaiterCalls = true;
    try {
      const response = await fetch(`/api/waiter-calls/restaurant/${RESTAURANT_ID}`);
      const data = await response.json();
      if (data.success && Array.isArray(data.data)) {
        // Filter unresolved (pending) calls
        const pendingCalls = data.data.filter(c => c.status === 'pending');
        const currentCallIds = new Set();

        pendingCalls.forEach(call => {
          currentCallIds.add(call.id);
          if (!activeCallsSet.has(call.id)) {
            if (!isFirstWaiterLoad) {
              playChimeSound();
              announceWaiterCallVoice(call.table_id);
            }
          }
        });

        isFirstWaiterLoad = false;
        activeCallsSet = currentCallIds;

        // Render cards inside the drawer
        const drawer = document.getElementById('waiterAlertsDrawer');
        if (pendingCalls.length > 0) {
          drawer.innerHTML = pendingCalls.map(call => `
            <div id="waiter-call-card-${call.id}" style="background: linear-gradient(135deg, #EF4444, #F59E0B); color: white; padding: 12px 16px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2); display: flex; justify-content: space-between; align-items: center; animation: slideInRightAlert 0.3s ease-out; border-left: 4px solid #fff;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-bell fa-bounce" style="font-size: 16px;"></i>
                <div>
                  <h4 style="margin: 0; font-size: 13px; font-weight: 700;">Table #${call.table_id}</h4>
                  <p style="margin: 2px 0 0 0; font-size: 10px; opacity: 0.85;">Requesting assistance</p>
                </div>
              </div>
              <button onclick="dismissWaiterCall(${call.id})" style="background: rgba(255, 255, 255, 0.25); border: 1px solid rgba(255, 255, 255, 0.4); color: white; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: 700; transition: all 0.2s; pointer-events: auto;">Resolve</button>
            </div>
          `).join('');
        } else {
          drawer.innerHTML = '';
        }
      }
    } catch (err) {
      console.log('Error checking waiter calls:', err);
    } finally {
      isCheckingWaiterCalls = false;
    }
  }

  async function dismissWaiterCall(callId) {
    try {
      const response = await fetch(`/api/waiter-calls/${callId}/resolve`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });
      const data = await response.json();
      if (data.success) {
        showToast('Waiter call resolved!', 'success');
        const card = document.getElementById(`waiter-call-card-${callId}`);
        if (card) {
          card.style.animation = 'slideOutRightAlert 0.3s ease-in forwards';
          setTimeout(() => {
            checkWaiterCalls();
          }, 300);
        } else {
          checkWaiterCalls();
        }
      }
    } catch (err) {
      showToast('Failed to resolve waiter call', 'error');
    }
  }

  function addComboToCart(name, price) {
    const existing = MENU.find(m => m.name === name);
    if (existing) {
      existing.qty += 1;
    } else {
      MENU.push({
        id: Date.now(),
        name: name,
        cat: 'Mains',
        price: price,
        is_veg: true,
        badge: 'BEST',
        qty: 1
      });
    }
    renderMenu();
    renderCart();
    showToast(`✅ ${name} added to cart!`, 'success');
    closeModal('combosModal');
  }

  function applyModifierToCart(modName, modPrice) {
    const cartItems = MENU.filter(i => i.qty > 0);
    if (cartItems.length === 0) {
      showToast('Please add items to cart first to apply modifiers!', 'warning');
      return;
    }

    const lastItem = cartItems[cartItems.length - 1];
    if (modPrice > 0) {
      lastItem.price += modPrice;
    }
    const currentNote = lastItem.cookingNote || '';
    if (!currentNote.includes(modName)) {
      lastItem.cookingNote = currentNote ? `${currentNote}, +${modName}` : `+${modName}`;
    }

    renderCart();
    showToast(`✅ Attached modifier "${modName}" to ${lastItem.name}!`, 'success');
  }

  // Poll for waiter calls and live notifications (throttled to 15s to eliminate server lag)
  setInterval(() => { if (document.visibilityState === 'hidden') return; checkWaiterCalls(); }, 15000);
  checkWaiterCalls();
  loadTablesData(); // Populate allTablesData array immediately on page load!

  function updateLiveClock() {
    const clock = document.getElementById('liveClockBadge');
    const dateSub = document.getElementById('topbarDate');
    
    const now = new Date();
    
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    if (clock) {
      clock.textContent = `${String(hours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
    }
    
    const options = { weekday: 'short', month: 'short', day: 'numeric' };
    const dateString = now.toLocaleDateString('en-US', options);
    
    let shift = 'General';
    const curHour = now.getHours();
    if (curHour >= 8 && curHour < 16) {
      shift = 'Morning';
    } else if (curHour >= 16 && curHour < 24) {
      shift = 'Evening';
    } else {
      shift = 'Night';
    }
    
    if (dateSub) {
      dateSub.innerHTML = `<span>${dateString} &bull; Shift: ${shift}</span>`;
    }
  }
  updateLiveClock();
  setInterval(() => { if (document.visibilityState === 'hidden') return; updateLiveClock(); }, 1000);

  // Initial immediate menu render
  if (typeof renderMenu === 'function') renderMenu();

  // Open modal automatically if query parameter is set
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('openModal') === 'orders') {
    openModal('ordersModal');
    // Clear URL query parameters from browser address bar to prevent opening again on refresh
    window.history.replaceState({}, document.title, window.location.pathname);
  }

  // ─── VOCAL SOUNDBOX ANNOUNCEMENTS ───
  let isCheckingNewPayments = false;

  async function checkNewPayments() {
    if (isCheckingNewPayments) return;
    isCheckingNewPayments = true;
    try {
      const response = await fetch(`/api/orders/restaurant/${RESTAURANT_ID}`);
      const data = await response.json();
      const ordersList = Array.isArray(data.data) ? data.data : (data.data?.data || []);
      if (data.success && Array.isArray(ordersList)) {
        // Initialize announced list on first run to avoid repeating old orders
        if (announcedOrderIds.length === 0) {
          announcedOrderIds = ordersList
            .filter(order => order.payment_status === 'paid')
            .map(order => String(order.id));
          localStorage.setItem('dineflow_announced_orders', JSON.stringify(announcedOrderIds));
          return;
        }

        ordersList.forEach(order => {
          const orderIdStr = String(order.id);
          if (order.payment_status === 'paid' && !announcedOrderIds.includes(orderIdStr)) {
            // Find table number
            const table = allTablesData.find(t => String(t.id) === String(order.table_id));
            const tableNumber = table ? table.table_number : `T-${order.table_id}`;
            
            // Calculate proper grand total (subtotal + 5% service charge + 18% GST)
            const subtotal = parseFloat(order.total_amount);
            const serviceCharge = subtotal * 0.05;
            const gst = (subtotal + serviceCharge) * 0.18;
            const grandTotal = Math.round(subtotal + serviceCharge + gst);
            
            // Announce payment vocally!
            announcePaymentVoice(tableNumber, grandTotal, 'digital scan');
            
            // Add to announced list & persist
            announcedOrderIds.push(orderIdStr);
            localStorage.setItem('dineflow_announced_orders', JSON.stringify(announcedOrderIds));
          }
        });
      }
    } catch (err) {
      console.log('Error checking new payments:', err);
    } finally {
      isCheckingNewPayments = false;
    }
  }

  // Initial startup load
  if (typeof syncPosMenuWithDatabase === 'function') syncPosMenuWithDatabase();
  if (typeof loadTablesData === 'function') loadTablesData();
  if (typeof loadOrdersData === 'function') loadOrdersData();

  // Background polling for payment soundbox announcements (optimized to 45s to eliminate server lag)
  setInterval(() => { if (document.visibilityState === 'hidden') return; checkNewPayments(); }, 45000);

  // Customer Modal Selection & Filter Logic (Tenant-Scoped)
  selectedCustomerData = { name: 'Walk-in Customer', phone: '', points: 0, avatar: 'WC', isNew: true, ordersCount: 0 };

  async function fetchAndRenderPosCustomers() {
    try {
      const listEl = document.getElementById('custModalList');
      if (!listEl) return;

      const res = await fetch(`/api/customers?restaurantId=${RESTAURANT_ID}`);
      const data = await res.json();
      if (data.success && Array.isArray(data.data) && data.data.length > 0) {
        listEl.innerHTML = data.data.map((c, idx) => {
          const initials = c.name ? c.name.split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase() : '?';
          const points = parseInt(c.loyalty_points || 0);
          const visits = parseInt(c.total_visits || 0);
          const isSelected = idx === 0 ? 'selected' : '';
          
          if (idx === 0 && selectedCustomerData.name === 'Walk-in Customer') {
            selectedCustomerData = { name: c.name, phone: c.phone || '', points: points, avatar: initials, isNew: visits === 0, ordersCount: visits };
            const strip = document.querySelector('.customer-strip');
            if (strip) {
              const avatarEl = strip.querySelector('.cust-avatar');
              const nameEl = strip.querySelector('.cust-info h5');
              const subEl = strip.querySelector('.cust-info p');
              if (avatarEl) avatarEl.textContent = initials;
              if (nameEl) nameEl.textContent = c.name;
              if (subEl) subEl.innerHTML = `${c.phone || ''} &nbsp;·&nbsp; <span style="color:var(--primary);">⭐ ${points} pts</span>`;
            }
          }

          let tagClass = 'loyal';
          let tagText = 'Loyal';
          if (visits === 0) { tagClass = 'new'; tagText = 'New'; }
          else if (points >= 300) { tagClass = 'vip'; tagText = 'VIP Gold'; }

          const safeName = (c.name || '').replace(/'/g, "\\'");
          const safePhone = (c.phone || '').replace(/'/g, "\\'");

          return `
            <div class="cust-row ${isSelected}" onclick="selectCustomerItem(this, '${safeName}', '${safePhone}', ${points}, '${initials}', ${visits === 0}, ${visits})">
              <div class="cr-avatar" style="background:linear-gradient(135deg,#4F46E5,#6366F1);">${initials}</div>
              <div class="cr-info"><h5>${escapeHtml(c.name)}</h5><p>${escapeHtml(c.phone||'—')} &nbsp;·&nbsp; ${escapeHtml(c.email||'')}</p></div>
              <span class="cr-tag ${tagClass}"><i class="fa-solid fa-star" style="font-size:9px; margin-right:3px;"></i> ${points} pts (${tagText})</span>
            </div>
          `;
        }).join('');
      } else {
        listEl.innerHTML = `
          <div style="text-align:center; padding:32px 16px; color:var(--text-muted); font-size:13px;">
            <i class="fa-solid fa-user-plus" style="font-size:28px; margin-bottom:10px; display:block; color:var(--primary); opacity:0.6;"></i>
            No registered customers found.<br>
            <span style="font-size:12px; opacity:0.8;">Click <b>+ New Customer</b> above to register one for this restaurant.</span>
          </div>
        `;
      }
    } catch (e) {
      console.log('Error loading POS customers:', e);
    }
  }

  async function fetchAndRenderPosWaiters() {
    try {
      const res = await fetch(`/api/staff/restaurant/${RESTAURANT_ID}`);
      const data = await res.json();
      if (data.success && Array.isArray(data.data) && data.data.length > 0) {
        const waiterNames = data.data
          .filter(s => ['waiter', 'staff', 'cashier', 'admin', 'manager'].includes(s.role))
          .map(s => s.name.trim())
          .filter(Boolean);
        if (waiterNames.length > 0) {
          META_PICKERS.waiter.options = Array.from(new Set(waiterNames));
          if (!META_PICKERS.waiter.options.includes(metaSelections.waiter)) {
            metaSelections.waiter = META_PICKERS.waiter.options[0];
            const wVal = document.getElementById('waiterValue');
            if (wVal) wVal.textContent = metaSelections.waiter;
            const kotW = document.getElementById('kotWaiterName');
            if (kotW) kotW.textContent = metaSelections.waiter;
          }
        }
      }
    } catch (e) {
      console.log('Error loading POS waiters:', e);
    }
  }

  async function syncPosRestaurantSettings() {
    try {
      const res = await fetch(`/api/restaurants/${RESTAURANT_ID}/settings`);
      const data = await res.json();
      if (data.success && data.data) {
        const s = data.data;
        const cfg = s.settings || {};
        if (cfg.language) localStorage.setItem('dineflow_restaurant_lang', cfg.language);
        if (cfg.soundbox_lang) localStorage.setItem('dineflow_soundbox_lang', cfg.soundbox_lang);
        if (cfg.printer_type) localStorage.setItem('dineflow_printer_type', cfg.printer_type);
        if (s.name) localStorage.setItem('dineflow_restaurant_name', s.name);
      }
    } catch(e) {
      console.log('POS settings sync fallback:', e);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    syncPosRestaurantSettings();
    fetchAndRenderPosCustomers();
    fetchAndRenderPosWaiters();
  });

  let appliedWalletDiscount = 0;

  function selectCustomerItem(el, name, phone, points, avatar, isNew = false, ordersCount = null) {
    document.querySelectorAll('#custModalList .cust-row').forEach(r => r.classList.remove('selected'));
    el.classList.add('selected');
    selectedCustomerData = { name, phone, points, avatar, isNew, ordersCount, wallet_balance: 0 };
  }

  async function confirmCustomerSelection() {
    if (!selectedCustomerData) return;
    const strip = document.querySelector('.customer-strip');
    if (strip) {
      const avatarEl = strip.querySelector('.cust-avatar');
      const nameEl = strip.querySelector('.cust-info h5');
      const subEl = strip.querySelector('.cust-info p');
      if (avatarEl) avatarEl.textContent = selectedCustomerData.avatar;
      if (nameEl) nameEl.textContent = selectedCustomerData.name;
      if (subEl) subEl.innerHTML = `${selectedCustomerData.phone || 'Walk-in'} &nbsp;·&nbsp; <span style="color:var(--primary);">⭐ ${selectedCustomerData.points || 0} pts</span>`;
    }

    // Lookup customer from backend to get live wallet balance & birthday/anniversary promo status
    if (selectedCustomerData.phone) {
      try {
        const lookupRes = await fetch(`/api/customers/lookup?phone=${encodeURIComponent(selectedCustomerData.phone)}&restaurant_id=${RESTAURANT_ID}`);
        const lookupJson = await lookupRes.json();
        if (lookupJson.success && lookupJson.data) {
          const cData = lookupJson.data;
          selectedCustomerData.id = cData.id;
          selectedCustomerData.wallet_balance = parseFloat(cData.wallet_balance || 0);
          selectedCustomerData.is_birthday = !!cData.is_birthday;
          selectedCustomerData.is_anniversary = !!cData.is_anniversary;

          // Render Wallet Strip
          const walletStrip = document.getElementById('customerWalletStrip');
          const walletAmtEl = document.getElementById('custWalletAmount');
          const bdayBadge = document.getElementById('custBirthdayBadge');
          if (walletStrip && walletAmtEl) {
            walletAmtEl.textContent = `₹${selectedCustomerData.wallet_balance.toFixed(2)}`;
            walletStrip.style.display = 'flex';
            if (bdayBadge) {
              if (selectedCustomerData.is_birthday) {
                bdayBadge.style.display = 'inline-block';
                bdayBadge.textContent = '🎂 Birthday Special!';
                showToast(`🎉 Today is ${selectedCustomerData.name}'s Birthday! Special Treats available!`, 'info');
              } else if (selectedCustomerData.is_anniversary) {
                bdayBadge.style.display = 'inline-block';
                bdayBadge.textContent = '💍 Anniversary Special!';
                showToast(`💖 Happy Anniversary to ${selectedCustomerData.name}!`, 'info');
              } else {
                bdayBadge.style.display = 'none';
              }
            }
          }
        }
      } catch (err) {
        console.log('Customer lookup error:', err);
      }
    }

    showToast(`✅ Customer selected: ${selectedCustomerData.name}`, 'success');
    closeModal('customerModal');
    // Auto detect new vs returning and apply offer
    autoDetectCustomerDiscount(selectedCustomerData);
  }

  function applyCustomerWalletDiscount() {
    if (!selectedCustomerData || !selectedCustomerData.phone) {
      showToast('Please select a customer first!', 'warning');
      return;
    }
    const available = parseFloat(selectedCustomerData.wallet_balance || 0);
    if (available <= 0) {
      showToast('No wallet balance available to redeem.', 'warning');
      return;
    }

    const cartItems = MENU.filter(i => i.qty > 0);
    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
    if (subtotal <= 0) {
      showToast('Please add items to cart before redeeming wallet!', 'warning');
      return;
    }

    // Apply up to available or up to subtotal
    const redeemAmount = Math.min(available, Math.floor(subtotal));
    appliedWalletDiscount = redeemAmount;

    applySmartOffer(`WALLET-${redeemAmount}`, `Customer Wallet Cashback Redeemed`, redeemAmount, 0, 0);
    showToast(`💰 Redeemed ₹${redeemAmount} from Customer Wallet!`, 'success');

    const redeemBtn = document.getElementById('custRedeemWalletBtn');
    if (redeemBtn) {
      redeemBtn.textContent = 'Applied ✓';
      redeemBtn.style.background = '#059669';
    }
  }

  async function saveNewCustomer() {
    const nameInput = document.getElementById('newCustNameInput');
    const phoneInput = document.getElementById('newCustPhoneInput');
    const emailInput = document.getElementById('newCustEmailInput');
    const dobInput = document.getElementById('newCustDobInput');
    const annivInput = document.getElementById('newCustAnnivInput');
    const name = nameInput ? nameInput.value.trim() : '';
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const email = emailInput ? emailInput.value.trim() : '';
    const dob = dobInput ? dobInput.value : null;
    const anniv = annivInput ? annivInput.value : null;
    if (!name || !phone) {
      showToast('Name and Mobile Number are required!', 'error');
      return;
    }
    const avatar = name.charAt(0).toUpperCase();

    try {
      const posCsrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                            (typeof CSRF_TOKEN !== 'undefined' ? CSRF_TOKEN : '');
      const res = await fetch('/api/customers', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': posCsrfToken
        },
        body: JSON.stringify({
          restaurant_id: RESTAURANT_ID,
          name: name,
          phone: phone,
          email: email,
          date_of_birth: dob,
          anniversary_date: anniv
        })
      });
      const resData = await res.json();
      if (resData.success) {
        selectedCustomerData = { name, phone, email, points: 0, avatar, isNew: true, ordersCount: 0, wallet_balance: 0 };
        const strip = document.querySelector('.customer-strip');
        if (strip) {
          const avatarEl = strip.querySelector('.cust-avatar');
          const nameEl = strip.querySelector('.cust-info h5');
          const subEl = strip.querySelector('.cust-info p');
          if (avatarEl) avatarEl.textContent = avatar;
          if (nameEl) nameEl.textContent = name;
          if (subEl) subEl.innerHTML = `${phone} &nbsp;·&nbsp; <span style="color:#6366F1;">🆕 New Customer</span>`;
        }
        if (nameInput) nameInput.value = '';
        if (phoneInput) phoneInput.value = '';
        if (emailInput) emailInput.value = '';
        if (dobInput) dobInput.value = '';
        if (annivInput) annivInput.value = '';
        closeModal('newCustomerModal');
        closeModal('customerModal');
        showToast(`🎉 Customer ${name} registered successfully!`, 'success');
        fetchAndRenderPosCustomers();
        autoDetectCustomerDiscount(selectedCustomerData);
      } else {
        showToast(resData.message || 'Failed to save customer', 'error');
      }
    } catch (err) {
      showToast('Network error while saving customer', 'error');
    }
  }

  function autoDetectCustomerDiscount(customer) {
    if (!customer) return;
    const isNew = customer.isNew === true || customer.ordersCount === 0;
    const pts = customer.points || 0;
    const cartItems = MENU.filter(i => i.qty > 0);
    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);

    if (isNew) {
      // New Customer → Auto apply 15% Welcome Discount
      setTimeout(() => {
        applySmartOffer('WELCOME15', '15% Welcome Discount (First-Time Customer)', 0, 15, 0);
        showToast(`🎉 Welcome ${customer.name}! 15% First-Order Discount automatically applied!`, 'success');
        addPosNotification({
          id: `welcome-${Date.now()}`,
          title: `🆕 New Customer: ${customer.name}`,
          message: 'First-time visitor! 15% Welcome Discount auto-applied to cart.',
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          type: 'info',
          icon: 'fa-solid fa-user-plus'
        });
      }, 500);
      return;
    }

    // Returning Customer → Suggest loyalty points or min-order deals
    if (pts >= 100) {
      setTimeout(() => {
        showToast(`⭐ ${customer.name} has ${pts} loyalty points! Open Offers to redeem them.`, 'info');
        addPosNotification({
          id: `loyalty-${customer.phone}-${Date.now()}`,
          title: `⭐ Loyalty Points Available: ${pts} pts`,
          message: `${customer.name} can redeem ${pts} points for up to ₹${Math.floor(pts/50)*50} OFF.`,
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          type: 'warning',
          icon: 'fa-solid fa-star'
        });
      }, 500);
    } else if (subtotal >= 500) {
      setTimeout(() => {
        showToast(`🎁 ${customer.name} qualifies for Spend & Save! Open Offers section.`, 'info');
      }, 500);
    } else {
      setTimeout(() => {
        showToast(`👋 Welcome back, ${customer.name}! (${pts} loyalty pts)`, 'success');
      }, 300);
    }
  }

  function filterCustomerModalList() {
    const q = (document.getElementById('custSearchInput')?.value || '').toLowerCase();
    document.querySelectorAll('#custModalList .cust-row').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(q) ? 'flex' : 'none';
    });
  }
  /* ─── REAL-TIME LIVE NOTIFICATION ENGINE ─── */
  let posNotifications = [
    {
      id: 'notif-sys-1',
      title: 'POS Terminal Live Active',
      message: 'Real-time order monitoring & waiter alert engine connected.',
      time: 'Just now',
      type: 'info',
      icon: 'fa-solid fa-circle-check',
      read: false
    },
    {
      id: 'notif-sys-2',
      title: 'Gujarati Soundbox Audio Engine',
      message: 'Payment voice feedback & kitchen alerts ready.',
      time: '5 mins ago',
      type: 'success',
      icon: 'fa-solid fa-volume-high',
      read: false
    }
  ];

  function renderPosNotifications() {
    const listEl = document.getElementById('notifModalList');
    const badgeCountEl = document.getElementById('notifCountBadge');
    const dotEl = document.getElementById('notifDot');
    if (!listEl) return;

    const unreadCount = posNotifications.filter(n => !n.read).length;
    if (badgeCountEl) {
      badgeCountEl.textContent = unreadCount;
      badgeCountEl.style.display = unreadCount > 0 ? 'inline-block' : 'none';
    }
    if (dotEl) {
      dotEl.style.display = unreadCount > 0 ? 'block' : 'none';
    }

    if (posNotifications.length === 0) {
      listEl.innerHTML = `
        <div style="text-align:center; padding:36px 20px; color:var(--text-muted);">
          <i class="fa-solid fa-bell-slash" style="font-size:36px; margin-bottom:12px; color:var(--text-secondary); display:block;"></i>
          <div style="font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">All Caught Up!</div>
          <div style="font-size:12px; color:var(--text-muted);">No active notifications or live alerts.</div>
        </div>
      `;
      return;
    }

    listEl.innerHTML = posNotifications.map(n => `
      <div style="display:flex; align-items:flex-start; gap:12px; background:${n.type === 'danger' ? 'rgba(239,68,68,0.1)' : n.type === 'success' ? 'rgba(16,185,129,0.1)' : n.type === 'warning' ? 'rgba(245,158,11,0.1)' : 'rgba(99,102,241,0.1)'}; border:1px solid ${n.type === 'danger' ? 'rgba(239,68,68,0.25)' : n.type === 'success' ? 'rgba(16,185,129,0.25)' : n.type === 'warning' ? 'rgba(245,158,11,0.25)' : 'rgba(99,102,241,0.25)'}; border-radius:12px; padding:12px; opacity:${n.read ? 0.6 : 1}; transition:all 0.2s;">
        <div style="width:34px; height:34px; border-radius:10px; background:${n.type === 'danger' ? 'rgba(239,68,68,0.2)' : n.type === 'success' ? 'rgba(16,185,129,0.2)' : n.type === 'warning' ? 'rgba(245,158,11,0.2)' : 'rgba(99,102,241,0.2)'}; color:${n.type === 'danger' ? '#F87171' : n.type === 'success' ? '#34D399' : n.type === 'warning' ? '#FCD34D' : '#818CF8'}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <i class="${n.icon}"></i>
        </div>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:var(--text-primary);">${n.title}</div>
          <div style="font-size:11.5px; color:var(--text-secondary); margin-top:2px;">${n.message}</div>
          <div style="font-size:10.5px; color:var(--text-muted); margin-top:4px;">${n.time}</div>
        </div>
        ${!n.read ? `<button onclick="markSingleNotifRead('${n.id}')" title="Mark Read" style="background:rgba(255,255,255,0.08); border:1px solid var(--border); color:var(--text-secondary); border-radius:6px; padding:3px 7px; cursor:pointer; font-size:11px;"><i class="fa-solid fa-check"></i></button>` : ''}
      </div>
    `).join('');
  }

  function addPosNotification(notif) {
    const exists = posNotifications.find(n => n.id === notif.id);
    if (!exists) {
      posNotifications.unshift({
        id: notif.id || ('notif-' + Date.now()),
        title: notif.title || 'System Notification',
        message: notif.message || '',
        time: notif.time || 'Just now',
        type: notif.type || 'info',
        icon: notif.icon || 'fa-solid fa-bell',
        read: false
      });
      renderPosNotifications();
    }
  }

  function markSingleNotifRead(id) {
    posNotifications = posNotifications.filter(x => x.id !== id);
    renderPosNotifications();
  }

  function markAllNotificationsAsRead() {
    posNotifications = [];
    renderPosNotifications();
    if (typeof showToast === 'function') {
      showToast('✅ All notifications cleared & marked as read!', 'success');
    }
  }

  let knownOrdersSet = new Set();
  let isFirstNotificationSync = true;
  let isSyncingLiveAlerts = false;

  async function syncRealTimeLiveAlerts() {
    if (isSyncingLiveAlerts) return;
    isSyncingLiveAlerts = true;
    try {
      const oRes = await fetch(`/api/orders/restaurant/${RESTAURANT_ID}`);
      const oData = await oRes.json();
      const ordersList = Array.isArray(oData.data) ? oData.data : (oData.data?.data || []);
      if (oData.success && Array.isArray(ordersList)) {
        ordersList.slice(0, 12).forEach(order => {
          if (!knownOrdersSet.has(order.id)) {
            knownOrdersSet.add(order.id);

            if (!isFirstNotificationSync) {
              if (typeof showToast === 'function') {
                showToast(`🛍️ New Order #${order.id} Placed!`, 'info');
              }
            }

            addPosNotification({
              id: `order-alert-${order.id}`,
              title: `🛍️ New Order #${order.id}`,
              message: `Table #${order.table_id || 'Takeaway'} • Total: ₹${order.total_amount || 0}`,
              time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
              type: order.payment_status === 'paid' ? 'success' : 'warning',
              icon: 'fa-solid fa-receipt'
            });
          }
        });
      }
      isFirstNotificationSync = false;
    } catch (err) {
      console.log('Error syncing live order alerts:', err);
    } finally {
      isSyncingLiveAlerts = false;
    }
  }

  /* ─── DINEFLOW KEYBOARD SHORTCUTS & BARCODE SCANNER ENGINE ─── */
  function openShortcutsModal() {
    openModal('shortcutsModal');
  }

  // Barcode scanner buffer & timer
  let barcodeBuffer = '';
  let lastKeyTime = Date.now();

  window.addEventListener('keydown', (e) => {
    // If user is typing in an input/textarea and it's NOT an F-key or Alt shortcut, let default input happen
    const activeTagName = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
    const isTyping = (activeTagName === 'input' || activeTagName === 'textarea' || activeTagName === 'select');

    // F1: Save & Send KOT (if cart has items), or Focus Search Bar
    if (e.key === 'F1') {
      e.preventDefault();
      const cartCount = MENU.filter(i => i.qty > 0).length;
      if (cartCount > 0) {
        sendKOT();
      } else {
        focusItemSearch();
      }
      return;
    }

    // F2: Place Order / Checkout
    if (e.key === 'F2') {
      e.preventDefault();
      checkout();
      return;
    }

    // F3: Select Customer Modal
    if (e.key === 'F3') {
      e.preventDefault();
      openModal('customerModal');
      return;
    }

    // F4: Hold Current Cart
    if (e.key === 'F4') {
      e.preventDefault();
      holdCurrentCart();
      return;
    }

    // F8: Smart Offers & Discounts
    if (e.key === 'F8') {
      e.preventDefault();
      openModal('posOffersModal');
      return;
    }

    // F9: Print Hub
    if (e.key === 'F9') {
      e.preventDefault();
      openPrintHub(lastPlacedOrderId);
      return;
    }

    // Alt + C: Clear Cart
    if (e.altKey && (e.key === 'c' || e.key === 'C')) {
      e.preventDefault();
      clearCart();
      return;
    }

    // Press '?' (Shift + /) for Shortcuts Cheat Sheet when not typing in an input
    if (e.key === '?' && !isTyping) {
      e.preventDefault();
      openShortcutsModal();
      return;
    }

    // Escape: Close topmost modal
    if (e.key === 'Escape') {
      const openModals = document.querySelectorAll('.modal-overlay.show, .modal-overlay[style*="display: flex"]');
      if (openModals.length > 0) {
        e.preventDefault();
        openModals.forEach(m => {
          m.classList.remove('show');
          m.style.display = 'none';
        });
      }
      return;
    }

    // ─── HARDWARE BARCODE SCANNER DETECTOR ───
    // Fast keystroke stream (<50ms between keys) ending with Enter
    const currentTime = Date.now();
    if (currentTime - lastKeyTime > 120) {
      barcodeBuffer = '';
    }
    lastKeyTime = currentTime;

    if (e.key === 'Enter') {
      if (barcodeBuffer.length >= 3 && !isTyping) {
        e.preventDefault();
        const scannedCode = barcodeBuffer.trim();
        barcodeBuffer = '';
        handleBarcodeScannedItem(scannedCode);
      }
    } else if (e.key.length === 1) {
      barcodeBuffer += e.key;
    }
  });

  function handleBarcodeScannedItem(code) {
    const item = MENU.find(m => 
      (m.barcode && String(m.barcode).toLowerCase() === code.toLowerCase()) ||
      (m.sku && String(m.sku).toLowerCase() === code.toLowerCase()) ||
      String(m.id) === code
    );

    if (item) {
      addQuickItem(item.id);
      showToast(`🎯 Barcode Scanned: Added ${item.name} (₹${item.price}) to cart!`, 'success');
    } else {
      showToast(`⚠️ Scanned Barcode "${code}" not found in Menu!`, 'warning');
    }
  }

  /* ─── OFFLINE-READY NETWORK SENTINEL ─── */
  function updateNetworkStatusDisplay(isOnline) {
    const badge = document.getElementById('posNetworkStatusBadge');
    const icon = document.getElementById('posNetworkStatusIcon');
    const text = document.getElementById('posNetworkStatusText');
    if (!badge || !icon || !text) return;

    if (isOnline) {
      badge.style.color = '#10B981';
      badge.style.borderColor = 'rgba(16,185,129,0.3)';
      badge.style.background = 'rgba(16,185,129,0.1)';
      badge.title = 'Network Sentinel: Online & Synced';
      icon.className = 'fa-solid fa-wifi';
      text.textContent = 'Online';
    } else {
      badge.style.color = '#EF4444';
      badge.style.borderColor = 'rgba(239,68,68,0.4)';
      badge.style.background = 'rgba(239,68,68,0.18)';
      badge.title = 'Network Sentinel: Offline Mode Active. Local data preserved.';
      icon.className = 'fa-solid fa-triangle-exclamation fa-fade';
      text.textContent = 'Offline Mode';
      showToast('⚠️ Network connection lost! Offline Mode active — local billing continues safely.', 'warning');
    }
  }

  /* ═════════════════════════════════════════════════════════════════════════
     DINEFLOW ENTERPRISE - MENU MODIFIERS & COMBOS CONTROLLER
     ═════════════════════════════════════════════════════════════════════════ */
  let currentCustomizingItem = null;
  let currentCustomizingGroups = [];

  async function openItemModifierModal(itemId) {
    const item = MENU.find(i => String(i.id) === String(itemId));
    if (!item) return;

    currentCustomizingItem = item;
    document.getElementById('modModalItemId').value = item.id;
    document.getElementById('modModalItemName').textContent = 'Customize ' + item.name;
    document.getElementById('modModalItemBasePrice').textContent = `Base Price: ₹${(item.basePrice || item.price).toFixed(0)}`;
    document.getElementById('modModalCalculatedPrice').textContent = `₹ ${(item.basePrice || item.price).toFixed(0)}`;

    const container = document.getElementById('modModalGroupsContainer');
    container.innerHTML = `
      <div style="text-align:center; padding:30px; color:var(--text-muted);">
        <i class="fa-solid fa-spinner fa-spin" style="font-size:24px;"></i>
        <p style="margin-top:8px; font-size:12px;">Loading options & add-ons...</p>
      </div>`;
    openModal('posItemModifierModal');

    try {
      const res = await fetch(`/api/menu-items/${item.id}/modifiers`);
      const data = await res.json();
      if (data.status === 'success' && Array.isArray(data.modifier_groups) && data.modifier_groups.length > 0) {
        currentCustomizingGroups = data.modifier_groups;
        renderModifierGroups(data.modifier_groups);
      } else {
        container.innerHTML = `
          <div style="text-align:center; padding:24px; color:var(--text-secondary); font-size:13px;">
            <i class="fa-solid fa-circle-info" style="font-size:22px; color:var(--primary); margin-bottom:8px; display:block;"></i>
            No extra add-on groups configured for this dish. Adding as standard regular dish.
          </div>`;
        setTimeout(() => {
          applyModifiersAndAddToCart();
          closeModal('posItemModifierModal');
        }, 1200);
      }
    } catch (err) {
      console.error('Failed to load modifiers:', err);
      container.innerHTML = `<div style="color:#EF4444; padding:15px; font-size:12.5px;">Error loading modifiers. Please try again.</div>`;
    }
  }

  function renderModifierGroups(groups) {
    const container = document.getElementById('modModalGroupsContainer');
    container.innerHTML = groups.map((g, gIdx) => {
      const isSingle = g.selection_type === 'single';
      const isRequired = g.is_required || false;
      return `
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:12px; padding:12px; margin-bottom:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <h4 style="font-size:13px; font-weight:800; color:var(--text-primary); margin:0;">
              ${escapeHtml(g.name)}
              ${isRequired ? '<span style="color:#EF4444; font-size:11px; margin-left:4px;">*Required</span>' : '<span style="color:var(--text-muted); font-size:11px; margin-left:4px;">(Optional)</span>'}
            </h4>
            <span style="font-size:10.5px; font-weight:700; color:var(--text-muted); background:var(--card); padding:2px 6px; border-radius:4px; border:1px solid var(--border);">
              ${isSingle ? 'Choose 1' : 'Choose Multiple'}
            </span>
          </div>
          <div style="display:flex; flex-direction:column; gap:6px;">
            ${(g.options || []).map((opt, oIdx) => {
              const inputType = isSingle ? 'radio' : 'checkbox';
              const inputName = `mod_grp_${g.id}`;
              const optId = `mod_opt_${opt.id}`;
              const isChecked = isSingle && (opt.is_default || oIdx === 0);
              return `
                <label for="${optId}" style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px; border-radius:8px; background:var(--card); border:1px solid var(--border); cursor:pointer; transition:all 0.15s;">
                  <div style="display:flex; align-items:center; gap:8px;">
                    <input type="${inputType}" id="${optId}" name="${inputName}" value="${opt.id}" data-group-name="${escapeAttr(g.name)}" data-name="${escapeAttr(opt.name)}" data-price="${opt.price}" onchange="recalculateModalModifiersPrice()" ${isChecked ? 'checked' : ''} style="cursor:pointer; accent-color:var(--primary);">
                    <span style="font-size:12.5px; font-weight:600; color:var(--text-primary);">${escapeHtml(opt.name)}</span>
                  </div>
                  <span style="font-size:12px; font-weight:700; color:${opt.price > 0 ? '#10B981' : 'var(--text-muted)'};">
                    ${opt.price > 0 ? '+₹' + parseFloat(opt.price).toFixed(0) : 'Free'}
                  </span>
                </label>
              `;
            }).join('')}
          </div>
        </div>
      `;
    }).join('');
    recalculateModalModifiersPrice();
  }

  function recalculateModalModifiersPrice() {
    if (!currentCustomizingItem) return;
    const base = currentCustomizingItem.basePrice || currentCustomizingItem.price || 0;
    let extra = 0;
    const checkedInputs = document.querySelectorAll('#modModalGroupsContainer input:checked');
    checkedInputs.forEach(inp => {
      extra += parseFloat(inp.getAttribute('data-price') || 0);
    });
    const total = base + extra;
    document.getElementById('modModalCalculatedPrice').textContent = `₹ ${total.toFixed(0)}`;
  }

  function applyModifiersAndAddToCart() {
    if (!currentCustomizingItem) return;

    const checkedInputs = document.querySelectorAll('#modModalGroupsContainer input:checked');
    const selectedMods = [];
    let extraTotal = 0;

    checkedInputs.forEach(inp => {
      const p = parseFloat(inp.getAttribute('data-price') || 0);
      extraTotal += p;
      selectedMods.push({
        group: inp.getAttribute('data-group-name'),
        name: inp.getAttribute('data-name'),
        price: p
      });
    });

    currentCustomizingItem.selected_modifiers = selectedMods;
    currentCustomizingItem.price = (currentCustomizingItem.basePrice || currentCustomizingItem.price) + extraTotal;
    currentCustomizingItem.qty += 1;

    renderMenu();
    renderCart();
    closeModal('posItemModifierModal');
    showToast(`${currentCustomizingItem.name} customized and added to cart!`, 'success');
  }

  /* ═════════════════════════════════════════════════════════════════════════
     DINEFLOW ENTERPRISE - STAFF ATTENDANCE CONTROLLER
     ═════════════════════════════════════════════════════════════════════════ */
  async function openStaffAttendanceModal() {
    openModal('posStaffAttendanceModal');
    await loadStaffMembersForAttendance();
    await loadTodayStaffAttendance();
  }

  async function loadStaffMembersForAttendance() {
    const sel = document.getElementById('attendanceStaffSelect');
    try {
      const res = await fetch(`/staff?restaurant_id=${RESTAURANT_ID}`);
      const data = await res.json();
      const staff = data.data || [];
      if (staff.length > 0) {
        sel.innerHTML = '<option value="">-- Choose Staff Member --</option>' +
          staff.map(s => `<option value="${s.id}">${escapeHtml(s.name)} (${s.role.toUpperCase()})</option>`).join('');
      } else {
        sel.innerHTML = '<option value="">No staff members found</option>';
      }
    } catch (e) {
      sel.innerHTML = '<option value="">Error loading staff</option>';
    }
  }

  async function loadTodayStaffAttendance() {
    const container = document.getElementById('attendanceTodayListContainer');
    try {
      const res = await fetch(`/api/staff/attendance-today`, {
        headers: { 'X-Restaurant-ID': RESTAURANT_ID, 'Accept': 'application/json' }
      });
      const data = await res.json();
      const records = data.data || [];
      if (records.length === 0) {
        container.innerHTML = `<div style="padding:18px; text-align:center; color:var(--text-muted); font-size:12px;">No shift attendance recorded today yet.</div>`;
        return;
      }

      container.innerHTML = `
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
          <thead>
            <tr style="background:var(--bg); border-bottom:1px solid var(--border); text-align:left; color:var(--text-muted); font-size:11px;">
              <th style="padding:8px 12px;">Staff Member</th>
              <th style="padding:8px 12px;">Clock-In</th>
              <th style="padding:8px 12px;">Clock-Out</th>
              <th style="padding:8px 12px;">Hours</th>
              <th style="padding:8px 12px;">Status</th>
            </tr>
          </thead>
          <tbody>
            ${records.map(r => {
              const statusColor = r.is_clocked_in ? '#10B981' : (r.is_clocked_out ? '#6366F1' : '#9CA3AF');
              const statusLabel = r.is_clocked_in ? 'ON SHIFT' : (r.is_clocked_out ? 'COMPLETED' : 'ABSENT');
              return `
                <tr style="border-bottom:1px solid var(--border);">
                  <td style="padding:8px 12px; font-weight:700; color:var(--text-primary);">${escapeHtml(r.name)} <small style="color:var(--text-muted); display:block;">${r.role}</small></td>
                  <td style="padding:8px 12px; color:var(--text-primary);">${r.clock_in || '--'}</td>
                  <td style="padding:8px 12px; color:var(--text-primary);">${r.clock_out || '--'}</td>
                  <td style="padding:8px 12px; font-weight:700; color:var(--text-primary);">${r.total_hours || 0}h ${r.overtime_hours > 0 ? `<span style="color:#F59E0B; font-size:10px;">(+${r.overtime_hours}h OT)</span>` : ''}</td>
                  <td style="padding:8px 12px;">
                    <span style="background:${statusColor}22; color:${statusColor}; border:1px solid ${statusColor}44; padding:2px 8px; border-radius:12px; font-size:10px; font-weight:800;">
                      ${statusLabel}
                    </span>
                  </td>
                </tr>
              `;
            }).join('')}
          </tbody>
        </table>
      `;
    } catch (e) {
      container.innerHTML = `<div style="padding:15px; color:#EF4444; font-size:12px;">Failed to load attendance list.</div>`;
    }
  }

  async function performStaffClockIn() {
    const userId = document.getElementById('attendanceStaffSelect').value;
    const shift = document.getElementById('attendanceShiftSelect').value;
    if (!userId) {
      showToast('Please select a staff member to Clock In', 'warning');
      return;
    }

    try {
      const res = await fetch(`/api/staff/clock-in`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Restaurant-ID': RESTAURANT_ID,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ user_id: userId, shift: shift })
      });
      const data = await res.json();
      if (data.status === 'success') {
        showToast(data.message, 'success');
        await loadTodayStaffAttendance();
      } else {
        showToast(data.message || 'Clock In failed', 'error');
      }
    } catch (e) {
      showToast('Clock in request failed', 'error');
    }
  }

  async function performStaffClockOut() {
    const userId = document.getElementById('attendanceStaffSelect').value;
    if (!userId) {
      showToast('Please select a staff member to Clock Out', 'warning');
      return;
    }

    try {
      const res = await fetch(`/api/staff/clock-out`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Restaurant-ID': RESTAURANT_ID,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify({ user_id: userId })
      });
      const data = await res.json();
      if (data.status === 'success') {
        showToast(data.message, 'success');
        await loadTodayStaffAttendance();
      } else {
        showToast(data.message || 'Clock Out failed', 'error');
      }
    } catch (e) {
      showToast('Clock out request failed', 'error');
    }
  }

  /* ═════════════════════════════════════════════════════════════════════════
     DINEFLOW ENTERPRISE - DIRECT THERMAL PRINTER CONFIGURATION
     ═════════════════════════════════════════════════════════════════════════ */
  function openPrinterConfigModal() {
    const currentMode = localStorage.getItem('dineflow_printer_type') || 'browser';
    const currentWidth = localStorage.getItem('dineflow_printer_width') || '80';
    const currentIp = localStorage.getItem('dineflow_printer_ip') || '';

    document.getElementById('escposPrinterModeSelect').value = currentMode;
    document.getElementById('escposPaperWidthSelect').value = currentWidth;
    document.getElementById('escposPrinterIpInput').value = currentIp;
    togglePrinterModeInputs();
    openModal('posPrinterConfigModal');
  }

  function togglePrinterModeInputs() {
    const mode = document.getElementById('escposPrinterModeSelect').value;
    const ipGroup = document.getElementById('escposIpGroup');
    if (ipGroup) {
      ipGroup.style.display = (mode === 'network') ? 'block' : 'none';
    }
  }

  async function savePrinterSettings() {
    const mode = document.getElementById('escposPrinterModeSelect').value;
    const width = document.getElementById('escposPaperWidthSelect').value;
    const ip = document.getElementById('escposPrinterIpInput').value.trim();

    if (window.dineFlowPrinter) {
      window.dineFlowPrinter.setPrinterMode(mode, { paperWidth: width, printerIp: ip });

      if (mode === 'usb') {
        try {
          await window.dineFlowPrinter.connectUsb();
          showToast('🟢 WebUSB Thermal Printer Connected successfully!', 'success');
        } catch (e) {
          showToast('USB Printer connection cancelled or failed.', 'warning');
        }
      } else if (mode === 'serial') {
        try {
          await window.dineFlowPrinter.connectSerial();
          showToast('🟢 WebSerial Thermal Printer Connected successfully!', 'success');
        } catch (e) {
          showToast('Serial Printer connection cancelled or failed.', 'warning');
        }
      } else {
        showToast('Thermal printer settings saved!', 'success');
      }
    }
    closeModal('posPrinterConfigModal');
  }

  async function testThermalPrinter() {
    if (!window.dineFlowPrinter) {
      showToast('Printer driver not initialized', 'error');
      return;
    }
    try {
      showToast('Sending test print voucher...', 'info');
      await window.dineFlowPrinter.testPrint();
      showToast('✅ Test Voucher Printed successfully!', 'success');
    } catch (e) {
      showToast('Test print failed: ' + e.message, 'error');
    }
  }

  window.addEventListener('online', () => {
    updateNetworkStatusDisplay(true);
    showToast('🟢 Internet connection restored! Online sync resumed.', 'success');
    if (typeof syncRealTimeLiveAlerts === 'function') syncRealTimeLiveAlerts();
  });

  window.addEventListener('offline', () => {
    updateNetworkStatusDisplay(false);
  });

  document.addEventListener('DOMContentLoaded', () => {
    initSidebarState();
    renderPosNotifications();
    updateHeldCartsCountBadge();
    updateNetworkStatusDisplay(navigator.onLine);
    if (typeof renderPosCategoryRibbon === 'function') renderPosCategoryRibbon();
    syncRealTimeLiveAlerts();
    setInterval(() => { if (document.visibilityState === 'hidden') return; syncRealTimeLiveAlerts(); }, 15000);
  });
</script>
</body>
</html>

