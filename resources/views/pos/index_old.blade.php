<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DineFlow AI POS — POS Terminal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --primary:        #4F46E5;
      --primary-light:  #EEF2FF;
      --primary-dark:   #3730A3;
      --success:        #10B981;
      --success-light:  #ECFDF5;
      --danger:         #EF4444;
      --danger-light:   #FEF2F2;
      --warning:        #F59E0B;
      --warning-light:  #FFFBEB;
      --info:           #3B82F6;
      --info-light:     #EFF6FF;
      --sidebar-bg:     #1E1B4B;
      --sidebar-hover:  #312E81;
      --sidebar-active: #4F46E5;
      --bg:             #F8FAFC;
      --card:           #FFFFFF;
      --border:         #E2E8F0;
      --text-primary:   #1E293B;
      --text-secondary: #64748B;
      --text-muted:     #94A3B8;
      --shadow-sm:      0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
      --shadow-md:      0 4px 6px rgba(0,0,0,.05), 0 2px 4px rgba(0,0,0,.04);
      --shadow-lg:      0 10px 15px rgba(0,0,0,.07), 0 4px 6px rgba(0,0,0,.05);
      --radius-sm:      6px;
      --radius-md:      10px;
      --radius-lg:      16px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { 
      font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-primary); 
      display: flex; height: 100vh; overflow: hidden; 
    }

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
    .sidebar.collapsed { width: 60px; min-width: 60px; }

    .sidebar-brand {
      padding: 14px 12px;
      display: flex; align-items: center; gap: 10px;
      border-bottom: 1px solid rgba(255,255,255,.08);
      flex-shrink: 0; position: relative; min-height: 58px;
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
    .sidebar.collapsed .brand-icon { display: none; }

    .sidebar-toggle {
      margin-left: auto; flex-shrink: 0;
      width: 28px; height: 28px; border-radius: 8px;
      background: rgba(255,255,255,.12); border: none;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: background .15s;
    }
    .sidebar-toggle:hover { background: rgba(255,255,255,.25); }
    .sidebar-toggle i { font-size: 11px; color: rgba(255,255,255,.9); transition: transform .25s; }
    .sidebar.collapsed .sidebar-toggle i { transform: rotate(180deg); }
    .sidebar.collapsed .sidebar-brand { justify-content: center; }
    .sidebar.collapsed .sidebar-toggle { margin-left: 0; }

    .sidebar-section { padding: 14px 8px 4px; overflow: hidden; }
    .sidebar-section-label {
      font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;
      color: rgba(255,255,255,.3); padding: 0 6px; white-space: nowrap;
      transition: opacity .2s;
    }
    .sidebar.collapsed .sidebar-section-label { opacity: 0; }

    .nav-item {
      display: flex; align-items: center; gap: 10px;
      padding: 9px 10px; border-radius: var(--radius-sm);
      color: rgba(255,255,255,.55); cursor: pointer; font-size: 13.5px; font-weight: 500;
      transition: all .18s; text-decoration: none; white-space: nowrap;
      margin-top: 2px;
    }
    .nav-item:hover {
      background: var(--sidebar-hover);
      color: white;
    }
    .nav-item.active {
      background: var(--sidebar-active);
      color: white;
      box-shadow: 0 2px 8px rgba(79,70,229,.4);
    }
    .nav-item i {
      width: 20px;
      text-align: center;
      font-size: 14px;
    }
    .nav-badge {
      margin-left: auto;
      background: var(--danger);
      color: white;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 6px;
      border-radius: 20px;
    }
    .nav-badge.green { background: var(--success); }

    /* ─── MAIN CONTENT ─────────────────────────── */
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .topbar {
      background: var(--card);
      border-bottom: 1px solid var(--border);
      padding: 0 16px;
      height: 58px;
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
      box-shadow: var(--shadow-sm);
    }

    .topbar-title-group {
      display: flex;
      flex-direction: column;
      flex: 1;
    }
    .topbar-title { font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .topbar-sub { font-size: 11px; color: var(--text-muted); }

    .topbar-btn {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-sm);
      background: var(--bg);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: var(--text-secondary);
      transition: all .15s;
      position: relative;
      font-size: 16px;
    }
    .topbar-btn:hover {
      background: var(--primary-light);
      color: var(--primary);
      border-color: var(--primary);
    }

    #topbarCartBadge {
      position: absolute;
      top: -4px;
      right: -4px;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--danger);
      color: white;
      font-size: 9px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ─── POS WRAPPER ─────────────────────────── */
    .pos-wrapper {
      flex: 1;
      display: flex;
      overflow: hidden;
      gap: 10px;
      padding: 10px;
    }

    .pos-left {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .order-meta {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
      padding: 10px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      margin-bottom: 10px;
    }

    .meta-group {
      display: flex;
      align-items: center;
    }

    .delivery-tabs {
      display: flex;
      gap: 4px;
    }

    .delivery-tab {
      padding: 6px 12px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--bg);
      cursor: pointer;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-secondary);
      transition: all .15s;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .delivery-tab:hover, .delivery-tab.active {
      border-color: var(--primary);
      background: var(--primary-light);
      color: var(--primary);
    }

    .meta-select {
      height: 36px;
      padding: 0 12px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--card);
      color: var(--text-secondary);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all .15s;
      font-family: inherit;
    }

    .meta-select:hover, .meta-select.open {
      border-color: var(--primary);
      color: var(--primary);
      background: var(--primary-light);
    }

    .meta-picker-btn {
      min-width: 120px;
      justify-content: space-between;
    }

    .meta-picker-inline {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* ─── MENU CONTROLS ─────────────────────────── */
    .menu-controls {
      display: flex;
      gap: 10px;
      align-items: center;
      margin-bottom: 10px;
    }

    .search-box {
      flex: 1;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 0 12px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      height: 36px;
    }

    .search-input {
      flex: 1;
      border: none;
      outline: none;
      background: transparent;
      font-size: 13px;
      color: var(--text-primary);
    }

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
      grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      gap: 8px;
    }
    .cat-popup-item {
      padding: 10px 8px; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      text-align: center; cursor: pointer; transition: all .15s;
    }
    .cat-popup-item:hover { border-color: var(--primary); background: var(--primary-light); }
    .cat-popup-item.active { border-color: var(--primary); background: var(--primary); }
    .cat-popup-item .cpi-emoji { font-size: 22px; display: block; margin-bottom: 4px; }
    .cat-popup-item .cpi-name  { font-size: 11px; font-weight: 600; color: var(--text-secondary); display: block; }
    .cat-popup-item.active .cpi-name { color: #fff; }
    .cat-popup-item .cpi-count { font-size: 10px; color: var(--text-muted); margin-top: 2px; display: block; }
    .cat-popup-item.active .cpi-count { color: rgba(255,255,255,.7); }

    /* ─── MENU GRID ─────────────────────────── */
    .menu-grid {
      flex: 1;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      gap: 10px;
      overflow-y: auto;
      padding: 10px;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
    }

    .item-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
      cursor: pointer;
      transition: all .15s;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .item-card:hover {
      border-color: var(--primary);
      box-shadow: 0 4px 12px rgba(79,70,229,.15);
    }

    .item-img {
      background: linear-gradient(135deg, var(--primary-light), var(--info-light));
      height: 100px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      border-bottom: 1px solid var(--border);
      position: relative;
    }

    .item-badge {
      position: absolute;
      top: 6px;
      right: 6px;
      background: var(--warning);
      color: white;
      font-size: 8px;
      font-weight: 700;
      padding: 3px 6px;
      border-radius: 4px;
      text-transform: uppercase;
    }

    .item-body {
      padding: 8px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      flex: 1;
    }

    .item-name {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-primary);
      line-height: 1.3;
    }

    .item-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 6px;
      margin-top: auto;
    }

    .item-price {
      font-size: 13px;
      font-weight: 700;
      color: var(--primary);
    }

    .item-price small {
      font-size: 10px;
      font-weight: 600;
    }

    .item-footer-actions {
      display: flex;
      gap: 4px;
    }

    .item-add-btn {
      width: 28px;
      height: 28px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: var(--radius-sm);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      transition: all .15s;
    }

    .item-add-btn:hover {
      background: var(--primary-dark);
      transform: scale(1.05);
    }

    .item-in-cart-badge {
      width: 28px;
      height: 28px;
      background: var(--success);
      color: white;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 12px;
    }

    /* ─── CART (RIGHT) ─────────────────────────── */
    .pos-right {
      width: 350px;
      min-width: 350px;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius-md);
    }

    .cart-header {
      padding: 14px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .cart-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .cart-count {
      background: var(--danger);
      color: white;
      border-radius: 50%;
      width: 22px;
      height: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      font-weight: 700;
    }

    .cart-order-no {
      font-size: 11px;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .cart-header > div:last-child {
      display: flex;
      gap: 6px;
    }

    .customer-strip {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 14px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
    }

    .cust-avatar {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--primary);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 12px;
      flex-shrink: 0;
    }

    .cust-info {
      flex: 1;
      min-width: 0;
    }

    .cust-info h5 {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .cust-info p {
      font-size: 10px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .cust-change {
      font-size: 11px;
      color: var(--primary);
      font-weight: 600;
      cursor: pointer;
    }

    .cart-items {
      flex: 1;
      overflow-y: auto;
      padding: 10px 14px;
    }

    .cart-item {
      display: flex;
      gap: 10px;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
      font-size: 12px;
    }

    .cart-item:last-child {
      border-bottom: none;
    }

    .ci-info {
      flex: 1;
      min-width: 0;
    }

    .ci-name {
      font-weight: 600;
      color: var(--text-primary);
    }

    .ci-variant {
      font-size: 10px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .ci-bottom {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 6px;
      justify-content: space-between;
    }

    .ci-controls {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .ci-btn {
      width: 24px;
      height: 24px;
      border: 1px solid var(--border);
      border-radius: 4px;
      background: var(--bg);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      color: var(--text-primary);
      transition: all .1s;
    }

    .ci-btn:hover {
      border-color: var(--primary);
      background: var(--primary-light);
      color: var(--primary);
    }

    .ci-qty {
      min-width: 24px;
      text-align: center;
      font-weight: 700;
    }

    .ci-price {
      font-weight: 700;
      color: var(--primary);
    }

    .ci-del {
      width: 24px;
      height: 24px;
      border: 1px solid var(--border);
      border-radius: 4px;
      background: var(--danger-light);
      color: var(--danger);
      border-color: var(--danger);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all .1s;
    }

    .ci-del:hover {
      background: var(--danger);
      color: white;
    }

    .cart-note-area {
      padding: 10px 14px;
      border-bottom: 1px solid var(--border);
    }

    .cart-note-input {
      width: 100%;
      padding: 8px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--bg);
      font-size: 12px;
      color: var(--text-primary);
      font-family: inherit;
    }

    .coupon-row {
      display: flex;
      gap: 6px;
      padding: 10px 14px;
      border-bottom: 1px solid var(--border);
    }

    .coupon-input {
      flex: 1;
      padding: 8px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--bg);
      font-size: 12px;
      color: var(--text-primary);
      font-family: inherit;
    }

    .coupon-btn {
      padding: 8px 12px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: var(--radius-sm);
      font-size: 11px;
      font-weight: 600;
      cursor: pointer;
      transition: all .15s;
    }

    .coupon-btn:hover {
      background: var(--primary-dark);
    }

    .cart-totals {
      padding: 10px 14px;
      border-bottom: 1px solid var(--border);
      font-size: 12px;
    }

    .tot-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
    }

    .tot-row.grand {
      font-weight: 700;
      color: var(--primary);
      font-size: 14px;
      padding: 10px 0 0;
    }

    .tot-divider {
      border: none;
      border-top: 1px solid var(--border);
      margin: 8px 0;
    }

    .payment-section {
      padding: 12px 14px;
      border-bottom: 1px solid var(--border);
    }

    .payment-label {
      font-size: 11px;
      font-weight: 600;
      color: var(--text-secondary);
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .pay-methods {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 6px;
    }

    .pay-method {
      padding: 8px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--bg);
      cursor: pointer;
      text-align: center;
      font-size: 11px;
      font-weight: 600;
      color: var(--text-secondary);
      transition: all .15s;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
    }

    .pay-method:hover {
      border-color: var(--primary);
      background: var(--primary-light);
      color: var(--primary);
    }

    .pay-method.active {
      border-color: var(--primary);
      background: var(--primary);
      color: white;
    }

    .pay-method i {
      font-size: 16px;
    }

    .split-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      padding: 10px 0 0;
    }

    .split-input {
      padding: 8px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--bg);
      font-size: 12px;
      color: var(--text-primary);
      font-family: inherit;
    }

    .cart-actions {
      padding: 10px 14px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .btn-checkout {
      background: var(--primary);
      color: white;
      border: none;
      padding: 12px;
      border-radius: var(--radius-md);
      font-weight: 700;
      cursor: pointer;
      transition: all .15s;
      font-family: inherit;
      font-size: 13px;
    }

    .btn-checkout:hover {
      background: var(--primary-dark);
    }

    .cart-btns-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 6px;
    }

    .btn-outline {
      background: var(--bg);
      color: var(--text-secondary);
      border: 1px solid var(--border);
      padding: 8px;
      border-radius: var(--radius-sm);
      font-weight: 600;
      cursor: pointer;
      transition: all .15s;
      font-family: inherit;
      font-size: 11px;
    }

    .btn-outline:hover {
      border-color: var(--primary);
      color: var(--primary);
      background: var(--primary-light);
    }

    .btn-outline.danger {
      border-color: var(--danger);
      color: var(--danger);
    }

    .btn-outline.danger:hover {
      background: var(--danger-light);
    }

    /* ─── MODALS ─────────────────────────────── */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal-overlay.show {
      display: flex;
    }

    .modal {
      background: var(--card);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      display: flex;
      flex-direction: column;
      max-height: 90vh;
      overflow: hidden;
      width: 90%;
      max-width: 500px;
    }

    .selection-popup-modal {
      width: min(560px, calc(100vw - 40px)) !important;
      max-height: 600px !important;
    }

    .table-popup-modal {
      width: min(600px, calc(100vw - 40px)) !important;
    }

    .meta-popup-modal {
      width: min(400px, calc(100vw - 40px)) !important;
    }

    .modal-header {
      padding: 16px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .modal-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: var(--text-muted);
      transition: color .15s;
    }

    .modal-close:hover {
      color: var(--text-primary);
    }

    .modal-body {
      flex: 1;
      overflow-y: auto;
      padding: 16px;
      min-height: 0;
    }

    .modal-footer {
      padding: 16px;
      border-top: 1px solid var(--border);
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      flex-shrink: 0;
    }

    /* ─── TABLE MODAL ─────────────────────────── */
    .table-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
      gap: 8px;
    }

    .table-cell {
      padding: 16px 8px;
      border: 2px solid var(--border);
      border-radius: var(--radius-md);
      text-align: center;
      cursor: pointer;
      transition: all .15s;
      background: var(--bg);
      color: var(--text-primary);
      font-weight: 600;
      font-size: 12px;
    }

    .table-cell span {
      display: block;
      font-size: 9px;
      color: var(--text-muted);
      margin-top: 4px;
      font-weight: 500;
    }

    .table-cell.available {
      border-color: var(--border);
      background: var(--bg);
      color: var(--text-primary);
    }

    .table-cell.available:hover {
      border-color: var(--primary);
      background: var(--primary-light);
      color: var(--primary);
    }

    .table-cell.occupied {
      border-color: var(--danger);
      background: var(--danger-light);
      color: var(--danger);
      cursor: not-allowed;
    }

    .table-cell.reserved {
      border-color: var(--warning);
      background: var(--warning-light);
      color: var(--warning);
      cursor: not-allowed;
    }

    .table-cell.selected {
      border-color: var(--primary);
      background: var(--primary);
      color: white;
    }

    .table-legend {
      display: flex;
      gap: 16px;
      margin-top: 16px;
      padding: 12px;
      background: var(--bg);
      border-radius: var(--radius-md);
      font-size: 12px;
    }

    .tl-item {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .tl-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
    }

    /* ─── META OPTION LIST ──────────────────── */
    .meta-option-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .meta-option-btn {
      padding: 12px;
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--bg);
      cursor: pointer;
      font-size: 13px;
      color: var(--text-primary);
      text-align: left;
      transition: all .15s;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-family: inherit;
    }

    .meta-option-btn:hover {
      border-color: var(--primary);
      background: var(--primary-light);
      color: var(--primary);
    }

    .meta-option-btn.active {
      border-color: var(--primary);
      background: var(--primary);
      color: white;
    }

    .meta-option-check {
      display: none;
    }

    .meta-option-btn.active .meta-option-check {
      display: block;
    }

    /* ─── KOT MODAL ────────────────────────────── */
    .kot-item-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px dashed var(--border);
      font-size: 12px;
    }

    .kot-item-row:last-child {
      border-bottom: none;
    }

    /* ─── TOAST ────────────────────────────────── */
    .toast-container {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .toast {
      background: var(--text-primary);
      color: #fff;
      padding: 12px 16px;
      border-radius: var(--radius-sm);
      font-size: 13px;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: var(--shadow-lg);
      animation: slideIn .25s ease;
      min-width: 240px;
    }

    .toast.success { background: var(--success); }
    .toast.error { background: var(--danger); }
    .toast.warning { background: var(--warning); }

    @keyframes slideIn {
      from { transform: translateX(400px); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    /* ─── RESPONSIVE ──────────────────────────── */
    @media (max-width: 1100px) {
      .pos-right { width: 300px; min-width: 300px; }
    }

    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 310;
        transform: translateX(-100%);
        transition: transform .3s;
      }
      .sidebar.mobile-open {
        transform: translateX(0);
      }

      .pos-wrapper {
        flex-direction: column;
        padding: 0;
      }

      .pos-left {
        display: none;
      }

      .pos-right {
        width: 100%;
        min-width: 100%;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        top: 58px;
        border-radius: 0;
      }
    }
  </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="fa-solid fa-bowl-food"></i></div>
    <div class="brand-text">
      <h2>DineFlow AI POS</h2>
      <span>v2.0</span>
    </div>
    <div class="sidebar-toggle" onclick="toggleSidebar()">
      <i class="fa-solid fa-chevron-left"></i>
    </div>
  </div>

  <div class="sidebar-nav">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Main</div>
      <a class="nav-item" href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-pie"></i><span class="nav-label">Dashboard</span></a>
      <a class="nav-item active" href="{{ route('pos.index') }}"><i class="fa-solid fa-cash-register"></i><span class="nav-label">POS Terminal</span><span class="nav-badge green">Live</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-receipt"></i><span class="nav-label">Orders</span><span class="nav-badge">12</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-table-cells-large"></i><span class="nav-label">Table Manager</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-fire-burner"></i><span class="nav-label">Kitchen Display</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Menu</div>
      <a class="nav-item" href="#"><i class="fa-solid fa-utensils"></i><span class="nav-label">Menu Items</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-layer-group"></i><span class="nav-label">Categories</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-puzzle-piece"></i><span class="nav-label">Modifiers</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-tags"></i><span class="nav-label">Combos & Deals</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Operations</div>
      <a class="nav-item" href="#"><i class="fa-solid fa-motorcycle"></i><span class="nav-label">Delivery Orders</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-users"></i><span class="nav-label">Customers</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-star"></i><span class="nav-label">Loyalty Program</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-ticket"></i><span class="nav-label">Coupons</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Reports</div>
      <a class="nav-item" href="#"><i class="fa-solid fa-chart-line"></i><span class="nav-label">Sales Reports</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-box-open"></i><span class="nav-label">Inventory</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-file-invoice-dollar"></i><span class="nav-label">Expenses</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Settings</div>
      <a class="nav-item" href="#"><i class="fa-solid fa-gear"></i><span class="nav-label">Settings</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-user-shield"></i><span class="nav-label">Staff & Roles</span></a>
      <a class="nav-item" href="#"><i class="fa-solid fa-print"></i><span class="nav-label">Printers</span></a>
    </div>
  </div>
</aside>

<div class="main">
  <div class="topbar">
    <div class="topbar-title-group">
      <div class="topbar-title">POS Terminal</div>
      <div class="topbar-sub">{{ Auth::user() ? Auth::user()->name . ' • ' . now()->format('l, d M Y') : 'Welcome' }}</div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-left:auto;">
      <div class="topbar-btn" title="Fullscreen" onclick="toggleFull()"><i class="fa-solid fa-expand"></i></div>
      <div class="topbar-btn" title="Menu" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></div>
    </div>
  </div>

  <div class="pos-wrapper">
    <div class="pos-left">
      <!-- Order Meta Bar -->
      <div class="order-meta">
        <!-- Delivery Type -->
        <div class="meta-group">
          <div class="delivery-tabs">
            <div class="delivery-tab active" onclick="setDelivery(this, 'dinein')"><i class="fa-solid fa-chair"></i> Dine-In</div>
            <div class="delivery-tab" onclick="setDelivery(this, 'takeaway')"><i class="fa-solid fa-bag-shopping"></i> Pickup</div>
            <div class="delivery-tab" onclick="setDelivery(this, 'delivery')"><i class="fa-solid fa-motorcycle"></i> Delivery</div>
          </div>
        </div>

        <!-- Table -->
        <div class="meta-group" id="tableGroup">
          <button type="button" class="meta-select meta-picker-btn" onclick="openModal('tableModal')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-table-cells"></i>
              <span class="meta-picker-value">T-04</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>

        <!-- Waiter -->
        <div class="meta-group">
          <button type="button" class="meta-select meta-picker-btn" onclick="openMetaPopup('waiter')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-user-tie"></i>
              <span class="meta-picker-value" id="waiterValue">Ravi M.</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>

        <!-- Covers -->
        <div class="meta-group">
          <button type="button" class="meta-select meta-picker-btn" onclick="openMetaPopup('covers')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-users"></i>
              <span class="meta-picker-value" id="coversValue">2</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>

        <!-- Priority -->
        <div class="meta-group">
          <button type="button" class="meta-select meta-picker-btn" onclick="openMetaPopup('priority')">
            <span class="meta-picker-inline">
              <i class="fa-solid fa-bolt"></i>
              <span class="meta-picker-value" id="priorityValue">Normal</span>
            </span>
            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
          </button>
        </div>
      </div>

      <!-- Search + Category Popup -->
      <div class="menu-controls">
        <div class="search-box">
          <i class="fa-solid fa-search"></i>
          <input class="search-input" placeholder="Search items…" oninput="filterItems(this.value)" />
        </div>
        <div class="cat-popup-wrap">
          <button class="cat-popup-btn" id="catPopupBtn" onclick="toggleCatPopup()">
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
                <div class="cat-popup-grid" id="catPopupGrid"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Menu Grid -->
      <div class="menu-grid" id="menuGrid"></div>
    </div>

    <!-- RIGHT (CART) -->
    <div class="pos-right">
      <!-- Cart Header -->
      <div class="cart-header">
        <div>
          <div class="cart-title">
            <i class="fa-solid fa-cart-shopping" style="color:var(--primary);"></i>
            Order Cart
            <span class="cart-count" id="cartCount">0</span>
          </div>
          <div class="cart-order-no">#ORD-{{ date('Ymd') }}-{{ rand(1000, 9999) }}</div>
        </div>
        <div style="display:flex;gap:6px;">
          <div class="topbar-btn" title="Hold Order" onclick="holdOrder()"><i class="fa-solid fa-pause"></i></div>
          <div class="topbar-btn" title="Print KOT" onclick="openModal('kotModal')"><i class="fa-solid fa-print"></i></div>
          <div class="topbar-btn mobile-close-cart" title="Close Cart" onclick="toggleMobileCart()" style="display:none;"><i class="fa-solid fa-xmark"></i></div>
        </div>
      </div>

      <!-- Customer Strip -->
      <div class="customer-strip">
        <div class="cust-avatar">RK</div>
        <div class="cust-info">
          <h5>Rahul Kumar</h5>
          <p>+91 98765 43210 · <span style="color:var(--primary);">⭐ 420 pts</span></p>
        </div>
        <span class="cust-change" onclick="openModal('customerModal')">Change</span>
      </div>

      <!-- Cart Items -->
      <div class="cart-items" id="cartItems"></div>

      <!-- Cart Note -->
      <div class="cart-note-area">
        <input class="cart-note-input" placeholder="Add order notes (allergies, special requests…)" />
      </div>

      <!-- Coupon -->
      <div class="coupon-row">
        <input class="coupon-input" placeholder="Have a coupon code?" />
        <button class="coupon-btn" onclick="applyCoupon()"><i class="fa-solid fa-check"></i> Apply</button>
      </div>

      <!-- Totals -->
      <div class="cart-totals">
        <div class="tot-row"><span class="label">Subtotal</span><span class="val" id="subtotal">₹ 0.00</span></div>
        <div class="tot-row"><span class="label">Service Charge</span><span class="val" id="serviceCharge">₹ 0.00</span></div>
        <div class="tot-row"><span class="label">GST (18%)</span><span class="val" id="tax">₹ 0.00</span></div>
        <hr class="tot-divider"/>
        <div class="tot-row grand"><span class="label">Grand Total</span><span class="val" id="grandTotal">₹ 0.00</span></div>
      </div>

      <!-- Payment Methods -->
      <div class="payment-section">
        <div class="payment-label">Payment Method</div>
        <div class="pay-methods">
          <div class="pay-method active" onclick="selectPay(this, 'cash')">
            <i class="fa-solid fa-money-bill-wave"></i><span>Cash</span>
          </div>
          <div class="pay-method" onclick="selectPay(this, 'card')">
            <i class="fa-solid fa-credit-card"></i><span>Card</span>
          </div>
          <div class="pay-method" onclick="selectPay(this, 'upi')">
            <i class="fa-brands fa-google-pay"></i><span>UPI</span>
          </div>
          <div class="pay-method" onclick="selectPay(this, 'wallet')">
            <i class="fa-solid fa-wallet"></i><span>Wallet</span>
          </div>
          <div class="pay-method" onclick="selectPay(this, 'split')">
            <i class="fa-solid fa-code-branch"></i><span>Split</span>
          </div>
        </div>
        <div class="split-row" id="splitRow" style="display:none;">
          <input class="split-input" placeholder="Cash ₹" />
          <input class="split-input" placeholder="Card ₹" />
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="cart-actions">
        <button class="btn-checkout" onclick="checkout()">
          <i class="fa-solid fa-check-circle"></i> Place Order · <span id="btnTotal">₹ 0.00</span>
        </button>
        <div class="cart-btns-row">
          <button class="btn-outline" onclick="sendKOT()"><i class="fa-solid fa-fire"></i> Send KOT</button>
          <button class="btn-outline" onclick="printBill()"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn-outline danger" onclick="clearCart()"><i class="fa-solid fa-trash"></i> Clear</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ════════════════════════════════════════════════ MODALS ════════════════════════════════════════════════ -->

<!-- Table Modal -->
<div class="modal-overlay" id="tableModal">
  <div class="modal table-popup-modal">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-table-cells" style="color:var(--primary);"></i>&nbsp; Select Table</div>
      <button class="modal-close" onclick="closeModal('tableModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div style="display:flex;gap:8px;margin-bottom:14px;">
        <select class="meta-select" style="flex:1;">
          <option>All Floors</option><option>Ground Floor</option><option>First Floor</option><option>Terrace</option>
        </select>
        <select class="meta-select" style="flex:1;">
          <option>All Sections</option><option>Indoor</option><option>Outdoor</option><option>Private</option>
        </select>
      </div>
      <div class="table-grid">
        <div class="table-cell available" onclick="selectTable(this,'T-01')">T-01<span>4 PAX</span></div>
        <div class="table-cell available" onclick="selectTable(this,'T-02')">T-02<span>2 PAX</span></div>
        <div class="table-cell occupied"  >T-03<span>Occupied</span></div>
        <div class="table-cell selected"  onclick="selectTable(this,'T-04')">T-04<span>Selected</span></div>
        <div class="table-cell reserved"  >T-05<span>Reserved</span></div>
        <div class="table-cell available" onclick="selectTable(this,'T-06')">T-06<span>6 PAX</span></div>
        <div class="table-cell occupied"  >T-07<span>Occupied</span></div>
        <div class="table-cell available" onclick="selectTable(this,'T-08')">T-08<span>4 PAX</span></div>
        <div class="table-cell available" onclick="selectTable(this,'T-09')">T-09<span>2 PAX</span></div>
        <div class="table-cell reserved"  >T-10<span>Reserved</span></div>
        <div class="table-cell available" onclick="selectTable(this,'T-11')">T-11<span>8 PAX</span></div>
        <div class="table-cell occupied"  >T-12<span>Occupied</span></div>
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

<!-- Meta Popup Modal -->
<div class="modal-overlay" id="metaPopupModal">
  <div class="modal meta-popup-modal">
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
  <div class="modal" style="width:360px;">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-fire-burner" style="color:var(--warning);"></i>&nbsp; Kitchen Order Ticket</div>
      <button class="modal-close" onclick="closeModal('kotModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div style="text-align:center;padding:10px 0 18px;">
        <div style="font-size:12px;color:var(--text-muted);">Order #ORD-20260313-0047</div>
        <div style="font-size:13px;font-weight:600;margin-top:2px;">Table: <span id="kotTable">T-04</span> · Waiter: <span id="kotWaiterName">Ravi M.</span></div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">{{ now()->format('d M Y · H:i A') }}</div>
      </div>
      <div id="kotList"></div>
      <div style="margin-top:14px;border:1px dashed var(--border);border-radius:var(--radius-sm);padding:10px;">
        <div style="font-size:11px;font-weight:600;color:var(--text-muted);margin-bottom:5px;">ORDER NOTES</div>
        <div style="font-size:12.5px;color:var(--text-primary);" id="kotNotes">No special notes</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" style="flex:none;padding:0 14px;height:36px;" onclick="closeModal('kotModal')">Cancel</button>
      <button class="btn-checkout" style="flex:none;padding:0 16px;height:36px;background:linear-gradient(135deg,var(--warning),#D97706);" onclick="sendKOT(); closeModal('kotModal');">
        <i class="fa-solid fa-fire"></i>&nbsp; Send to Kitchen
      </button>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script>
  // Menu Data
  const MENU = [
    { id: 1, name: 'Paneer Butter Masala', cat: 'mains', price: 240, emoji: '🍛', badge: 'veg', inCart: false, qty: 0 },
    { id: 2, name: 'Garlic Naan', cat: 'breads', price: 80, emoji: '🫓', badge: 'veg', inCart: false, qty: 0 },
    { id: 3, name: 'Mango Lassi', cat: 'beverages', price: 120, emoji: '🥭', badge: 'veg', inCart: false, qty: 0 },
    { id: 4, name: 'Chicken Tikka Masala', cat: 'mains', price: 320, emoji: '🍗', badge: 'nonveg', inCart: false, qty: 0 },
    { id: 5, name: 'Samosa', cat: 'starters', price: 50, emoji: '🥟', badge: 'veg', inCart: false, qty: 0 },
    { id: 6, name: 'Biryani', cat: 'rice', price: 280, emoji: '🍚', badge: 'best', inCart: false, qty: 0 },
    { id: 7, name: 'Gulab Jamun', cat: 'desserts', price: 100, emoji: '🍮', badge: 'new', inCart: false, qty: 0 },
    { id: 8, name: 'Spring Roll', cat: 'starters', price: 90, emoji: '🌮', badge: 'veg', inCart: false, qty: 0 },
    { id: 9, name: 'Butter Chicken', cat: 'mains', price: 350, emoji: '🍖', badge: 'nonveg', inCart: false, qty: 0 },
    { id: 10, name: 'Roti', cat: 'breads', price: 20, emoji: '🥙', badge: 'veg', inCart: false, qty: 0 },
    { id: 11, name: 'Chai', cat: 'beverages', price: 30, emoji: '☕', badge: 'veg', inCart: false, qty: 0 },
    { id: 12, name: 'Family Combo', cat: 'combo', price: 699, emoji: '🍱', badge: 'best', inCart: false, qty: 0 },
  ];

  const META_PICKERS = {
    waiter: {
      title: 'Select Waiter',
      icon: 'fa-user-tie',
      label: 'Waiter',
      options: ['Ravi M.', 'Sunita K.', 'Priya L.', 'Manoj T.'],
    },
    covers: {
      title: 'Select Covers',
      icon: 'fa-users',
      label: 'Covers',
      options: ['1', '2', '3', '4', '5', '6', '8', '10'],
    },
    priority: {
      title: 'Select Priority',
      icon: 'fa-bolt',
      label: 'Priority',
      options: ['Normal', 'Rush', 'VIP'],
    },
  };

  const CATS = [
    { key: 'all', label: 'All Items', emoji: '🍽️', count: MENU.length },
    { key: 'starters', label: 'Starters', emoji: '🥢', count: MENU.filter(i => i.cat === 'starters').length },
    { key: 'mains', label: 'Main Course', emoji: '🍛', count: MENU.filter(i => i.cat === 'mains').length },
    { key: 'breads', label: 'Breads', emoji: '🫓', count: MENU.filter(i => i.cat === 'breads').length },
    { key: 'rice', label: 'Rice & Biryani', emoji: '🍚', count: MENU.filter(i => i.cat === 'rice').length },
    { key: 'desserts', label: 'Desserts', emoji: '🍮', count: MENU.filter(i => i.cat === 'desserts').length },
    { key: 'beverages', label: 'Beverages', emoji: '🥭', count: MENU.filter(i => i.cat === 'beverages').length },
    { key: 'combo', label: 'Combo Deals', emoji: '🍱', count: MENU.filter(i => i.cat === 'combo').length },
  ];

  let metaSelections = {
    waiter: 'Ravi M.',
    covers: '2',
    priority: 'Normal',
  };

  let currentCat = 'all';
  let searchTerm = '';
  let currentPayment = 'cash';

  // Render Menu
  function renderMenu() {
    const grid = document.getElementById('menuGrid');
    const items = MENU.filter(i => {
      const matchCat = currentCat === 'all' || i.cat === currentCat;
      const matchSearch = i.name.toLowerCase().includes(searchTerm.toLowerCase());
      return matchCat && matchSearch;
    });

    grid.innerHTML = items.map(item => `
      <div class="item-card" onclick="quickAdd(${item.id})">
        <div class="item-img">
          ${item.emoji}
          ${item.badge ? `<span class="item-badge ${item.badge}">${item.badge.toUpperCase()}</span>` : ''}
        </div>
        <div class="item-body">
          <div class="item-name">${item.name}</div>
          <div class="item-footer">
            <div class="item-price">₹ ${item.price}</div>
            <div class="item-footer-actions">
              ${item.inCart ? `<div class="item-in-cart-badge">${item.qty}</div>` : `<button class="item-add-btn" onclick="event.stopPropagation(); quickAdd(${item.id})">+</button>`}
            </div>
          </div>
        </div>
      </div>
    `).join('');
  }

  // Render Cart
  function renderCart() {
    const cartItems = MENU.filter(i => i.inCart);
    const el = document.getElementById('cartItems');

    if (cartItems.length === 0) {
      el.innerHTML = '<div style="text-align:center;padding:20px;color:var(--text-muted);"><p>No items in cart</p></div>';
    } else {
      el.innerHTML = cartItems.map(item => `
        <div class="cart-item">
          <div class="ci-info">
            <div class="ci-name">${item.name}</div>
            <div class="ci-variant">Regular · No onions</div>
            <div class="ci-bottom">
              <div class="ci-controls">
                <button class="ci-btn" onclick="updateQty(${item.id}, -1)">−</button>
                <div class="ci-qty">${item.qty}</div>
                <button class="ci-btn" onclick="updateQty(${item.id}, 1)">+</button>
              </div>
              <div class="ci-price">₹ ${item.price * item.qty}</div>
              <button class="ci-del" onclick="removeItem(${item.id})"><i class="fa-solid fa-trash-can"></i></button>
            </div>
          </div>
        </div>
      `).join('');
    }

    updateTotals();
  }

  // Update Totals
  function updateTotals() {
    const cartItems = MENU.filter(i => i.inCart);
    let subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
    const serviceCharge = subtotal * 0.05;
    const tax = (subtotal + serviceCharge) * 0.18;
    const total = subtotal + serviceCharge + tax;

    document.getElementById('cartCount').textContent = cartItems.length;
    document.getElementById('subtotal').textContent = '₹ ' + subtotal.toFixed(2);
    document.getElementById('serviceCharge').textContent = '₹ ' + serviceCharge.toFixed(2);
    document.getElementById('tax').textContent = '₹ ' + tax.toFixed(2);
    document.getElementById('grandTotal').textContent = '₹ ' + total.toFixed(2);
    document.getElementById('btnTotal').textContent = '₹ ' + total.toFixed(2);
  }

  // Meta Popup
  function openMetaPopup(type) {
    const config = META_PICKERS[type];
    if (!config) return;

    document.getElementById('metaPopupTitle').innerHTML = `<i class="fa-solid ${config.icon}" style="color:var(--primary);"></i>&nbsp; ${config.title}`;
    document.getElementById('metaPopupList').innerHTML = config.options.map(option => `
      <button type="button" class="meta-option-btn ${metaSelections[type] === option ? 'active' : ''}" onclick="selectMetaOption('${type}', '${option}')">
        <span>${option}</span>
        <i class="fa-solid fa-check meta-option-check"></i>
      </button>
    `).join('');

    openModal('metaPopupModal');
  }

  function selectMetaOption(type, value) {
    metaSelections[type] = value;
    document.getElementById(`${type}Value`).textContent = value;
    document.getElementById('metaPopupList').querySelectorAll('.meta-option-btn').forEach(btn => btn.classList.remove('active'));
    event.target.closest('.meta-option-btn').classList.add('active');
    closeModal('metaPopupModal');
    showToast(`${META_PICKERS[type].label} set to ${value}`, 'success');
  }

  // Category Popup
  function renderCatPopup() {
    document.getElementById('catPopupGrid').innerHTML = CATS.map(c => `
      <div class="cat-popup-item ${currentCat === c.key ? 'active' : ''}" onclick="selectCat('${c.key}', '${c.label}')">
        <span class="cpi-emoji">${c.emoji}</span>
        <span class="cpi-name">${c.label}</span>
        <span class="cpi-count">${c.count} items</span>
      </div>
    `).join('');
  }

  function toggleCatPopup() {
    const modal = document.getElementById('catPopupModal');
    const btn = document.getElementById('catPopupBtn');
    if (modal.classList.contains('show')) {
      closeModal('catPopupModal');
      btn.classList.remove('open');
    } else {
      renderCatPopup();
      openModal('catPopupModal');
      btn.classList.add('open');
    }
  }

  function selectCat(key, label) {
    currentCat = key;
    if (key !== 'all') {
      document.getElementById('catPopupBtn').classList.add('filtered');
    } else {
      document.getElementById('catPopupBtn').classList.remove('filtered');
    }
    document.getElementById('catPopupLabel').textContent = label;
    renderMenu();
    renderCatPopup();
  }

  function resetCat() {
    selectCat('all', 'All Categories');
  }

  // Cart Functions
  function quickAdd(id) {
    const item = MENU.find(i => i.id === id);
    item.inCart = true;
    item.qty = 1;
    renderMenu();
    renderCart();
    showToast(item.name + ' added to cart', 'success');
  }

  function updateQty(id, delta) {
    const item = MENU.find(i => i.id === id);
    item.qty += delta;
    if (item.qty <= 0) {
      item.inCart = false;
      item.qty = 0;
    }
    renderMenu();
    renderCart();
  }

  function removeItem(id) {
    const item = MENU.find(i => i.id === id);
    item.inCart = false;
    item.qty = 0;
    renderMenu();
    renderCart();
    showToast(item.name + ' removed', 'error');
  }

  function clearCart() {
    if (confirm('Clear all items from cart?')) {
      MENU.forEach(i => { i.inCart = false; i.qty = 0; });
      renderMenu();
      renderCart();
      showToast('Cart cleared', 'warning');
    }
  }

  function filterItems(term) {
    searchTerm = term;
    renderMenu();
  }

  // Delivery Type
  function setDelivery(el, type) {
    document.querySelectorAll('.delivery-tab').forEach(e => e.classList.remove('active'));
    el.classList.add('active');
    const tableGroup = document.getElementById('tableGroup');
    if (type === 'dinein') {
      tableGroup.style.display = 'flex';
    } else {
      tableGroup.style.display = 'none';
    }
  }

  // Table Selection
  function selectTable(el, table) {
    document.querySelectorAll('.table-cell').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    document.querySelector('.meta-picker-value').textContent = table;
    document.getElementById('kotTable').textContent = table;
    closeModal('tableModal');
  }

  // Payment Methods
  function selectPay(el, method) {
    document.querySelectorAll('.pay-method').forEach(e => e.classList.remove('active'));
    el.classList.add('active');
    currentPayment = method;
    document.getElementById('splitRow').style.display = method === 'split' ? 'grid' : 'none';
  }

  // Action Buttons
  function holdOrder() {
    showToast('Order placed on hold', 'warning');
  }

  function sendKOT() {
    const cartItems = MENU.filter(i => i.inCart);
    if (cartItems.length === 0) {
      showToast('Cart is empty', 'error');
      return;
    }
    document.getElementById('kotList').innerHTML = cartItems.map(item => `
      <div class="kot-item-row"><span><b>${item.qty}x</b> ${item.name}</span><span>Special note</span></div>
    `).join('');
    showToast('KOT sent to kitchen', 'success');
  }

  function applyCoupon() {
    showToast('Coupon applied successfully', 'success');
  }

  function printBill() {
    showToast('Bill sent to printer', 'success');
  }

  function checkout() {
    const cartItems = MENU.filter(i => i.inCart);
    if (cartItems.length === 0) {
      showToast('Cart is empty', 'error');
      return;
    }
    showToast('Order placed successfully', 'success');
    setTimeout(() => {
      MENU.forEach(i => { i.inCart = false; i.qty = 0; });
      renderMenu();
      renderCart();
    }, 1000);
  }

  // Modal Functions
  function openModal(id) {
    document.getElementById(id).classList.add('show');
  }

  function closeModal(id) {
    document.getElementById(id).classList.remove('show');
  }

  // Toast Notification
  function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i class="fa-solid fa-${type === 'error' ? 'triangle-exclamation' : type === 'warning' ? 'circle-exclamation' : type === 'success' ? 'circle-check' : 'info'}""></i> ${message}`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
  }

  // Sidebar Toggle
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const isMobile = window.innerWidth <= 768;
    if (isMobile) {
      sidebar.classList.toggle('mobile-open');
    } else {
      sidebar.classList.toggle('collapsed');
    }
  }

  // Fullscreen Toggle
  function toggleFull() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen();
    } else {
      document.exitFullscreen();
    }
  }

  // Mobile Cart Toggle
  function toggleMobileCart() {
    const cart = document.querySelector('.pos-right');
    cart.classList.toggle('cart-open');
  }

  // Initialize
  renderMenu();
  renderCart();
</script>

</body>
</html>
