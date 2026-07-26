<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DineFlow AI POS — POS Terminal</title>
  <script>window.AUTH_USER_ROLE = "{{ Auth::user()->role ?? Auth::user()->username ?? session('active_role') ?? '' }}";</script>
  <script src="/frontend/admin/utils.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --primary:        #6366F1;
      --primary-light:  rgba(99, 102, 241, 0.15);
      --primary-dark:   #4F46E5;
      --success:        #10B981;
      --success-light:  rgba(16, 185, 129, 0.1);
      --danger:         #EF4444;
      --danger-light:   rgba(239, 68, 68, 0.1);
      --warning:        #F59E0B;
      --warning-light:  rgba(245, 158, 11, 0.1);
      --info:           #3B82F6;
      --info-light:     rgba(59, 130, 246, 0.1);
      --sidebar-bg:     #0B0F19;
      --sidebar-hover:  #1E293B;
      --sidebar-active: #6366F1;
      --bg:             #0F172A;
      --card:           #1E293B;
      --border:         #334155;
      --text-primary:   #F8FAFC;
      --text-secondary: #94A3B8;
      --text-muted:     #64748B;
      --shadow-sm:      0 1px 3px rgba(0,0,0,.3), 0 1px 2px rgba(0,0,0,.2);
      --shadow-md:      0 4px 6px rgba(0,0,0,.4), 0 2px 4px rgba(0,0,0,.3);
      --shadow-lg:      0 10px 15px rgba(0,0,0,.5), 0 4px 6px rgba(0,0,0,.4);
      --radius-sm:      8px;
      --radius-md:      12px;
      --radius-lg:      20px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text-primary); display: flex; height: 100vh; overflow: hidden; }

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

    /* Collapse toggle button */
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
    .sidebar.collapsed .nav-item .nav-label { opacity: 0; }
    .nav-badge { margin-left: auto; background: var(--danger); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 20px; transition: opacity .2s; }
    .nav-badge.green { background: var(--success); }
    .sidebar.collapsed .nav-badge { opacity: 0; }

    .sidebar-footer {
      flex-shrink: 0; padding: 14px 8px;
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
    .sidebar.collapsed .user-info { opacity: 0; width: 0; overflow: hidden; }

    /* ─── MAIN LAYOUT ─────────────────────────── */
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    .topbar {
      background: var(--card); border-bottom: 1px solid var(--border);
      padding: 0 24px; height: 58px; display: flex; align-items: center; gap: 16px;
      flex-shrink: 0; box-shadow: var(--shadow-sm);
    }
    .topbar-title { font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .topbar-sub   { font-size: 12px; color: var(--text-muted); margin-top: 1px; }
    .topbar-spacer { flex: 1; }
    .topbar-btn {
      width: 36px; height: 36px; border-radius: var(--radius-sm);
      background: var(--bg); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--text-secondary); position: relative;
      transition: all .15s;
    }
    .topbar-btn:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }
    .notif-dot { position: absolute; top: 6px; right: 6px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%; border: 2px solid var(--card); }

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
      display: flex; gap: 10px; padding: 12px 16px;
      background: var(--card); border-bottom: 1px solid var(--border);
      flex-wrap: wrap; align-items: center;
    }
    .meta-group { display: flex; gap: 8px; align-items: center; }
    .meta-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .5px; }

    /* Delivery Type Tabs */
    .delivery-tabs { display: flex; background: var(--bg); border-radius: var(--radius-sm); padding: 3px; border: 1px solid var(--border); }
    .delivery-tab {
      padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;
      cursor: pointer; color: var(--text-secondary); transition: all .15s; display: flex; align-items: center; gap: 5px;
    }
    .delivery-tab.active { background: var(--primary); color: #fff; box-shadow: var(--shadow-sm); }
    .delivery-tab i { font-size: 11px; }

    /* Select Dropdowns */
    .meta-select {
      height: 32px; padding: 0 10px; border: 1px solid var(--border);
      border-radius: var(--radius-sm); font-size: 12.5px; font-family: inherit;
      color: var(--text-primary); background: var(--card); outline: none; cursor: pointer;
      transition: border-color .15s; min-width: 30px;
    }
    .meta-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,.08); }
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
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }

    /* Search + Category */
    .menu-controls {
      padding: 12px 16px 8px;
      display: flex; gap: 10px; align-items: center;
      background: var(--bg); flex-wrap: wrap;
    }
    .search-box {
      flex: 1; min-width: 200px; position: relative;
    }
    .search-box i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px; }
    .search-input {
      width: 100%; height: 36px; padding: 0 12px 0 34px;
      border: 1px solid var(--border); border-radius: var(--radius-sm);
      font-size: 13px; font-family: inherit; background: var(--card); outline: none;
      transition: border-color .15s;
    }
    .search-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(79,70,229,.08); }

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

    /* Menu Grid */
    .menu-grid {
      flex: 1; overflow-y: auto; padding: 4px 12px 12px;
      display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px;
      align-content: start;
    }
    .menu-grid::-webkit-scrollbar { width: 5px; }
    .menu-grid::-webkit-scrollbar-track { background: transparent; }
    .menu-grid::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    .item-card {
      background: var(--card); border-radius: var(--radius-md);
      border: 1.5px solid var(--border); cursor: pointer;
      transition: all .18s; overflow: hidden;
      box-shadow: var(--shadow-sm);
    }
    .item-card:hover  { border-color: var(--primary); box-shadow: 0 4px 12px rgba(99,102,241,0.25); transform: translateY(-2px); }
    .item-card.in-cart { border-color: var(--success); background: linear-gradient(135deg, var(--card), var(--success-light)); }

    .item-img {
      height: 90px; background: linear-gradient(135deg, #1e293b, #0f172a);
      display: flex; align-items: center; justify-content: center;
      font-size: 34px; position: relative; overflow: hidden;
    }
    .item-badge {
      position: absolute; top: 6px; right: 6px;
      font-size: 9px; font-weight: 700; padding: 2px 6px;
      border-radius: 20px; letter-spacing: .3px;
    }
    .item-badge.veg   { background: var(--success-light); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3); }
    .item-badge.nonveg{ background: var(--danger-light);  color: var(--danger);  border: 1px solid rgba(239, 68, 68, 0.3); }
    .item-badge.new   { background: var(--warning-light); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.3); }
    .item-badge.best  { background: var(--primary-light); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.3); }

    .item-body { padding: 8px 9px; }
    .item-name { font-size: 12px; font-weight: 600; color: var(--text-primary); line-height: 1.3; margin-bottom: 6px; }
    .item-footer {
      display: flex; align-items: center; justify-content: space-between; gap: 8px; width: 100%;
    }
    .item-price { font-size: 13px; font-weight: 700; color: var(--primary); white-space: nowrap; }
    .item-price small { font-size: 9.5px; font-weight: 500; color: var(--text-muted); }
    .item-footer-actions { display: flex; align-items: center; justify-content: flex-end; flex-shrink: 0; }
    .item-add-btn {
      width: 28px; height: 28px; border-radius: 7px; border: none;
      background: var(--primary); color: #fff; font-size: 16px; font-weight: 400;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all .15s; flex-shrink: 0;
      line-height: 1;
    }
    .item-add-btn:hover { background: var(--primary-dark); transform: scale(1.08); }
    .item-in-cart-badge {
      width: 28px; height: 28px; border-radius: 7px; flex-shrink: 0;
      background: var(--success); color: #fff; font-size: 11px; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
    }

    /* ─── RIGHT PANEL (CART) ──────────────────── */
    .pos-right {
      width: 320px; min-width: 300px;
      background: var(--card); border-left: 1px solid var(--border);
      display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden;
      scrollbar-width: thin;
      -webkit-overflow-scrolling: touch;
    }
    .pos-right::-webkit-scrollbar { width: 4px; }
    .pos-right::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

    .cart-header {
      padding: 12px 14px 10px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .cart-title { font-size: 15px; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 8px; }
    .cart-count { background: var(--primary); color: #fff; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 20px; }
    .cart-order-no { font-size: 11px; color: var(--text-muted); font-weight: 500; }

    /* Customer Strip */
    .customer-strip {
      padding: 8px 14px;
      background: var(--bg); border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 8px;
    }
    .cust-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
    .cust-info h5 { font-size: 12.5px; font-weight: 600; color: var(--text-primary); }
    .cust-info p  { font-size: 11px; color: var(--text-muted); }
    .cust-change { margin-left: auto; font-size: 11px; color: var(--primary); font-weight: 600; cursor: pointer; }

    /* Cart Items */
    .cart-items { flex: none; overflow: visible; padding: 4px 0; }

    .cart-item {
      padding: 8px 14px; border-bottom: 1px solid var(--border);
      display: flex; align-items: flex-start;
    }
    .ci-info { flex: 1; min-width: 0; }
    .ci-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 10px;
    }
    .ci-main { flex: 1; min-width: 0; }
    .ci-name { font-size: 12.5px; font-weight: 600; color: var(--text-primary); margin-bottom: 2px; }
    .ci-variant {
      font-size: 10.5px; color: var(--text-muted); margin-bottom: 6px;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .ci-note { font-size: 10.5px; color: var(--text-muted); font-style: italic; }
    .ci-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }
    .ci-controls { display: flex; align-items: center; gap: 0; }
    .ci-btn {
      width: 22px; height: 22px; border: 1px solid var(--border); background: var(--bg);
      border-radius: 6px; display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 12px; color: var(--text-secondary); transition: all .12s;
    }
    .ci-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .ci-qty { width: 28px; text-align: center; font-size: 12px; font-weight: 700; color: var(--text-primary); }
    .ci-price { font-size: 13px; font-weight: 700; color: var(--text-primary); flex-shrink: 0; text-align: right; white-space: nowrap; }
    .ci-del { width: 22px; height: 22px; border: none; background: none; color: var(--text-muted); cursor: pointer; font-size: 12px; transition: color .12s; }
    .ci-del:hover { color: var(--danger); }

    /* Cart Note */
    .cart-note-area {
      padding: 6px 14px;
      border-top: 1px solid var(--border);
    }
    .cart-note-input {
      width: 100%; height: 36px; border: 1px solid var(--border); border-radius: var(--radius-sm);
      padding: 0 10px; font-size: 12px; font-family: inherit; outline: none; color: var(--text-primary);
      transition: border-color .15s; resize: none;
    }
    .cart-note-input::placeholder { color: var(--text-muted); }
    .cart-note-input:focus { border-color: var(--primary); }

    /* Totals */
    .cart-totals {
      padding: 8px 14px 4px;
      border-top: 1px solid var(--border);
    }
    .tot-row { display: flex; justify-content: space-between; font-size: 12.5px; margin-bottom: 5px; }
    .tot-row .label { color: var(--text-secondary); }
    .tot-row .val   { font-weight: 600; color: var(--text-primary); }
    .tot-row.discount .val { color: var(--danger); }
    .tot-divider { border: none; border-top: 1px dashed var(--border); margin: 8px 0; }
    .tot-row.grand .label { font-size: 14px; font-weight: 700; color: var(--text-primary); }
    .tot-row.grand .val   { font-size: 15px; font-weight: 800; color: var(--primary); }

    /* Coupon */
    .coupon-row {
      padding: 6px 14px;
      display: flex; gap: 8px;
    }
    .coupon-input {
      flex: 1; height: 30px; padding: 0 10px; border: 1px dashed var(--primary);
      border-radius: var(--radius-sm); font-size: 12px; font-family: inherit;
      outline: none; color: var(--text-primary); background: var(--primary-light);
    }
    .coupon-btn {
      height: 30px; padding: 0 12px; border: none; border-radius: var(--radius-sm);
      background: var(--primary); color: #fff; font-size: 12px; font-weight: 600;
      cursor: pointer; transition: background .15s;
    }
    .coupon-btn:hover { background: var(--primary-dark); }

    /* Payment */
    .payment-section {
      padding: 8px 14px;
      border-top: 1px solid var(--border);
    }
    .payment-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--text-muted); margin-bottom: 8px; }
    .pay-methods { display: flex; gap: 6px; flex-wrap: wrap; }
    .pay-method {
      flex: 1; min-width: 60px; padding: 7px 6px;
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      text-align: center; cursor: pointer; transition: all .15s;
    }
    .pay-method i { font-size: 16px; color: var(--text-muted); display: block; margin-bottom: 3px; }
    .pay-method span { font-size: 10px; font-weight: 600; color: var(--text-muted); display: block; }
    .pay-method:hover { border-color: var(--primary); }
    .pay-method.active { border-color: var(--primary); background: var(--primary-light); }
    .pay-method.active i, .pay-method.active span { color: var(--primary); }

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
      width: 460px; max-height: 90vh; overflow-y: auto;
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
    .table-popup-modal { width: 420px; }
    .meta-popup-modal  { width: 360px; }

    /* Table Modal */
    .table-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; }
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

    /* ─── RESPONSIVE ─────────────────────────── */
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
  </style>
</head>
<body>

<!-- ══════════════════════════════════════════════
     SIDEBAR
══════════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon" style="background: transparent; box-shadow: none; width: 34px; height: 34px; overflow: hidden; border-radius: 8px; margin-right: 2px;">
      <img src="/images/dineflow_logo.jpg" alt="DineFlow Logo" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
    <div class="brand-text">
      <h2>DineFlow AI POS</h2>
      <span>Restaurant Suite v2.0</span>
    </div>
    <div class="sidebar-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
      <i class="fa-solid fa-chevron-left" id="sidebarToggleIcon"></i>
    </div>
  </div>

  <div class="sidebar-nav">
    <div class="sidebar-section">
      <div class="sidebar-section-label">Main</div>
      <a class="nav-item" href="/dashboard" data-tip="Dashboard"><i class="fa-solid fa-chart-pie"></i><span class="nav-label"> Dashboard</span></a>
      <a class="nav-item active" href="/pos" data-tip="POS Terminal"><i class="fa-solid fa-cash-register"></i><span class="nav-label"> POS Terminal</span> <span class="nav-badge green">Live</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openModal('ordersModal')" style="cursor:pointer;" data-tip="Orders"><i class="fa-solid fa-receipt"></i><span class="nav-label"> Orders</span></a>
      <a class="nav-item" href="/admin/tables" data-tip="Table Manager"><i class="fa-solid fa-table-cells-large"></i><span class="nav-label"> Table Manager</span></a>
      <a class="nav-item" href="/kitchen" data-tip="Kitchen Display" target="_blank"><i class="fa-solid fa-fire-burner"></i><span class="nav-label"> Kitchen Display</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Menu</div>
      <a class="nav-item" href="/admin/menu-manager" data-tip="Menu Items"><i class="fa-solid fa-utensils"></i><span class="nav-label"> Menu Items</span></a>
      <a class="nav-item" href="/admin/menu-manager" data-tip="Categories"><i class="fa-solid fa-layer-group"></i><span class="nav-label"> Categories</span></a>
      <a class="nav-item" href="/admin/menu-manager" data-tip="Modifiers"><i class="fa-solid fa-puzzle-piece"></i><span class="nav-label"> Modifiers</span></a>
      <a class="nav-item" href="/admin/menu-manager" data-tip="Combos & Deals"><i class="fa-solid fa-tags"></i><span class="nav-label"> Combos &amp; Deals</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Operations</div>
      <a class="nav-item" href="javascript:void(0)" onclick="openModal('ordersModal')" style="cursor:pointer;" data-tip="Delivery Orders"><i class="fa-solid fa-motorcycle"></i><span class="nav-label"> Delivery Orders</span></a>
      <a class="nav-item" href="/admin/customers" data-tip="Customers"><i class="fa-solid fa-users"></i><span class="nav-label"> Customers</span></a>
      <a class="nav-item" href="/admin/customers" data-tip="Loyalty Program"><i class="fa-solid fa-star"></i><span class="nav-label"> Loyalty Program</span></a>
      <a class="nav-item" href="/admin/coupons" data-tip="Coupons"><i class="fa-solid fa-ticket"></i><span class="nav-label"> Coupons</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Reports</div>
      <a class="nav-item" href="/z-report" data-tip="Sales Reports"><i class="fa-solid fa-chart-line"></i><span class="nav-label"> Sales Reports</span></a>
      <a class="nav-item" href="/admin/inventory" data-tip="Inventory"><i class="fa-solid fa-box-open"></i><span class="nav-label"> Inventory</span></a>
      <a class="nav-item" href="/admin/expenses" data-tip="Expenses"><i class="fa-solid fa-file-invoice-dollar"></i><span class="nav-label"> Expenses</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Settings & Admin</div>
      <a class="nav-item" href="/admin/staff" data-tip="Staff & Roles"><i class="fa-solid fa-user-shield"></i><span class="nav-label"> Staff &amp; Roles</span></a>
      <a class="nav-item" href="/admin/settings" data-tip="Settings"><i class="fa-solid fa-gear"></i><span class="nav-label"> Settings</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openModal('printHubModal')" style="cursor:pointer;" data-tip="Printers"><i class="fa-solid fa-print"></i><span class="nav-label"> Printers</span></a>
      <a class="nav-item" href="/admin/feedback" data-tip="Reviews & Feedback"><i class="fa-solid fa-comment-dots"></i><span class="nav-label"> Feedback</span></a>
    </div>
  </div>



  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
      <div class="user-info">
        <h4>{{ auth()->user()->name }}</h4>
        <p style="text-transform:capitalize;">{{ auth()->user()->role ?? 'User' }}</p>
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
    <div class="topbar-title-group">
      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 8px;">
          <img src="/images/dineflow_logo.jpg" alt="DineFlow Logo" style="width: 28px; height: 28px; border-radius: 6px; object-fit: cover;">
          <div class="topbar-title" style="font-family: 'Outfit', sans-serif; font-weight: 800;">DineFlow AI POS</div>
        </div>
        <div id="liveClockBadge" style="background: rgba(79, 70, 229, 0.1); border: 1px solid rgba(79, 70, 229, 0.3); color: #818CF8; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; font-family: monospace; letter-spacing: 0.5px;">--:--:-- --</div>
        <div id="localWeatherBadge" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #34D399; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 4px;"><i class="fa-solid fa-cloud-sun-rain"></i> Patan, GJ • 34°C</div>
      </div>
      <div class="topbar-sub" id="topbarDate">Loading date...</div>
    </div>
    <div class="topbar-btn topbar-mobile-menu" title="Menu" onclick="toggleSidebar()">
      <i class="fa-solid fa-bars"></i>
    </div>
    <div class="topbar-spacer"></div>
    <!-- Gujarati Soundbox Voice Mute Toggle -->
    <div class="topbar-btn topbar-desktop-only" id="posVoiceMuteBtn" title="Gujarati Soundbox Toggle" onclick="togglePosVoiceMute()" style="color:#10B981; border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.08); width:auto; padding: 0 10px; border-radius: 20px; gap: 6px; margin-right: 4px;">
      <i id="posMuteIcon" class="fa-solid fa-volume-high"></i>
      <span id="posMuteText" style="font-size:11px; font-weight:700;">ગુજરાતી સાઉન્ડબોક્સ: ઓન 🔊</span>
    </div>

    <!-- Test Voice Sound Button -->
    <div class="topbar-btn topbar-desktop-only" title="Test Soundbox Voice" onclick="announcePaymentVoice('Test', 99, 'test mode')" style="color:#6366F1; border-color:rgba(99,102,241,0.3); background:rgba(99,102,241,0.08); width:auto; padding: 0 10px; border-radius: 20px; gap: 6px; margin-right: 8px;">
      <i class="fa-solid fa-comment-dots"></i>&nbsp;<span style="font-size:11px;font-weight:700;">ટેસ્ટ સાઉન્ડ</span>
    </div>
    <div class="topbar-btn topbar-desktop-only" title="Search" onclick="focusItemSearch()" style="cursor:pointer;"><i class="fa-solid fa-magnifying-glass"></i></div>
    <div class="topbar-btn topbar-desktop-only" title="Notifications" onclick="openModal('notifModal')" style="cursor:pointer;">
      <i class="fa-solid fa-bell"></i>
      <span class="notif-dot"></span>
    </div>
    <div class="topbar-btn topbar-desktop-only" title="Full Screen" onclick="toggleFull()"><i class="fa-solid fa-expand"></i></div>
    <div class="topbar-desktop-only" style="width:1px;height:26px;background:var(--border);"></div>
    <div class="topbar-desktop-only" style="display:flex;align-items:center;gap:8px;">
      <div class="user-avatar" style="width:32px;height:32px;font-size:12px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ substr(auth()->user()->name, 0, 1) }}</div>
      <div style="display:flex;flex-direction:column;">
        <span style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ auth()->user()->name }}</span>
        <span style="font-size:10px;color:var(--text-muted);text-transform:capitalize;">{{ auth()->user()->role ?? 'staff' }}</span>
      </div>
      <form method="POST" action="{{ route('auth.logout') }}" style="margin-left:4px;">
        @csrf
        <button type="submit" title="Logout"
          style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#F87171;
                 width:30px; height:30px; border-radius:8px; cursor:pointer;
                 display:flex; align-items:center; justify-content:center; font-size:13px;"
          onmouseover="this.style.background='rgba(239,68,68,0.35)'"
          onmouseout="this.style.background='rgba(239,68,68,0.15)'">
          <i class="fa-solid fa-right-from-bracket"></i>
        </button>
      </form>
    </div>
    <!-- Mobile-only cart button in topbar -->
    <div class="topbar-btn topbar-mobile-cart" title="Cart" onclick="toggleMobileCart()" style="position:relative;">
      <i class="fa-solid fa-cart-shopping"></i>
      <span class="topbar-cart-badge" id="topbarCartBadge">0</span>
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
              <span class="meta-picker-value" id="waiterValue">Ravi M.</span>
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
          <button type="button" class="meta-select meta-picker-btn" style="min-width:120px;" onclick="openMetaPopup('priority')">
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
                <div class="cat-popup-grid" id="catPopupGrid">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Menu Grid -->
      <div class="menu-grid" id="menuGrid">
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
          <div class="cart-order-no">#ORD-20260313-0001 &nbsp;·&nbsp; T-04</div>
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
          <p>+91 98765 43210 &nbsp;·&nbsp; <span style="color:var(--primary);">⭐ 420 pts</span></p>
        </div>
        <span class="cust-change" onclick="openModal('customerModal')">Change</span>
      </div>

      <!-- Cart Items -->
      <div class="cart-items" id="cartItems">
      </div>

      <!-- Cart Note -->
      <div class="cart-note-area">
        <input class="cart-note-input" placeholder="Add order notes (allergies, special requests…)" id="cartNote" />
      </div>

      <!-- Coupon -->
      <div class="coupon-row">
        <input class="coupon-input" placeholder="Have a coupon code?" id="couponInput" />
        <button class="coupon-btn" onclick="applyCoupon()"><i class="fa-solid fa-check"></i> Apply</button>
      </div>

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
          <div class="pay-method" onclick="selectPay(this); openUpiQrModal();" style="border-color:rgba(16,185,129,0.35);">
            <i class="fa-solid fa-qrcode" style="pointer-events:none; color:#34D399;"></i><span style="pointer-events:none;">UPI QR</span>
          </div>
          <div class="pay-method" onclick="selectPay(this)">
            <i class="fa-solid fa-code-branch" style="pointer-events:none;"></i><span style="pointer-events:none;">Split</span>
          </div>
        </div>
        <div class="split-row" id="splitRow" style="display:none;">
          <input class="split-input" placeholder="Cash ₹" value="0" />
          <input class="split-input" placeholder="Card ₹" value="0" />
        </div>
      </div>

      <!-- Clean & Professional POS Action Section -->
      <div class="cart-actions" style="display:flex; flex-direction:column; gap:10px; padding-top:14px; border-top:1px solid rgba(255,255,255,0.08);">
        
        <!-- Primary Single Checkout Button -->
        <button class="btn-checkout" onclick="checkout()" style="width:100%; height:48px; border-radius:14px; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#FFFFFF; font-size:15px; font-weight:800; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 6px 20px rgba(99,102,241,0.35); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
          <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
          <span>Place Order &nbsp;·&nbsp; <span id="checkoutAmount">₹ 0.00</span></span>
        </button>

        <!-- Secondary 4-Button Utility Toolbar -->
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:6px;">
          <!-- Print -->
          <button onclick="printBill()" style="height:38px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:var(--text-primary); font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'" title="Print Receipt">
            <i class="fa-solid fa-print" style="color:#FCD34D;"></i> Print
          </button>
          
          <!-- WhatsApp -->
          <button onclick="sendWhatsAppInvoice()" style="height:38px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:var(--text-primary); font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'" title="WhatsApp Bill">
            <i class="fa-brands fa-whatsapp" style="color:#4ADE80;"></i> WhatsApp
          </button>
          
          <!-- KOT -->
          <button onclick="openModal('kotModal')" style="height:38px; border-radius:10px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:var(--text-primary); font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'" title="Send KOT">
            <i class="fa-solid fa-fire" style="color:#FB923C;"></i> KOT
          </button>
          
          <!-- Clear -->
          <button onclick="clearCart()" style="height:38px; border-radius:10px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#FCA5A5; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; transition:all 0.15s;" onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'" title="Clear Cart">
            <i class="fa-solid fa-trash"></i> Clear
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════ -->

<!-- WhatsApp Invoice Modal -->
<div class="modal-overlay" id="whatsappModal">
  <div class="modal" style="width:440px;">
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

<!-- Customer Modal -->
<div class="modal-overlay" id="customerModal">
  <div class="modal" style="width:480px;">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-user" style="color:var(--primary);"></i>&nbsp; Select Customer</div>
      <button class="modal-close" onclick="closeModal('customerModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <input class="cust-search" id="custSearchInput" placeholder="Search by name, phone, or email…" onkeyup="filterCustomerModalList()" />
      <div class="cust-list" id="custModalList">
        <div class="cust-row selected" onclick="selectCustomerItem(this, 'Rahul Kumar', '+91 98765 43210', 420, 'RK')">
          <div class="cr-avatar" style="background:linear-gradient(135deg,#4F46E5,#6366F1);">RK</div>
          <div class="cr-info"><h5>Rahul Kumar</h5><p>+91 98765 43210 &nbsp;·&nbsp; rahul@gmail.com</p></div>
          <span class="cr-tag loyal">Loyal</span>
        </div>
        <div class="cust-row" onclick="selectCustomerItem(this, 'Priya Sharma', '+91 87654 32109', 290, 'PS')">
          <div class="cr-avatar" style="background:linear-gradient(135deg,#7C3AED,#EC4899);">PS</div>
          <div class="cr-info"><h5>Priya Sharma</h5><p>+91 87654 32109 &nbsp;·&nbsp; priya@gmail.com</p></div>
          <span class="cr-tag vip">VIP</span>
        </div>
        <div class="cust-row" onclick="selectCustomerItem(this, 'Anil Mehta', '+91 76543 21098', 120, 'AM')">
          <div class="cr-avatar" style="background:linear-gradient(135deg,#0891B2,#06B6D4);">AM</div>
          <div class="cr-info"><h5>Anil Mehta</h5><p>+91 76543 21098 &nbsp;·&nbsp; anil@gmail.com</p></div>
          <span class="cr-tag new">New</span>
        </div>
        <div class="cust-row" onclick="selectCustomerItem(this, 'Neha Gupta', '+91 96345 67890', 145, 'NG')">
          <div class="cr-avatar" style="background:linear-gradient(135deg,#F59E0B,#EF4444);">NG</div>
          <div class="cr-info"><h5>Neha Gupta</h5><p>+91 96345 67890 &nbsp;·&nbsp; neha@yahoo.com</p></div>
          <span class="cr-tag loyal">Loyal</span>
        </div>
        <div class="cust-row" onclick="selectCustomerItem(this, 'Virendra Singh', '+91 95456 78901', 410, 'VS')">
          <div class="cr-avatar" style="background:linear-gradient(135deg,#10B981,#059669);">VS</div>
          <div class="cr-info"><h5>Virendra Singh</h5><p>+91 95456 78901 &nbsp;·&nbsp; vsingh@gmail.com</p></div>
          <span class="cr-tag vip">VIP Gold</span>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" style="flex:none;padding:0 16px;height:36px;" onclick="closeModal('customerModal')"><i class="fa-solid fa-times"></i>&nbsp; Close</button>
      <button class="btn-checkout" style="flex:none;padding:0 20px;height:36px;" onclick="confirmCustomerSelection()"><i class="fa-solid fa-check"></i>&nbsp; Confirm</button>
    </div>
  </div>
</div>

<!-- Notifications Modal -->
<div class="modal-overlay" id="notifModal">
  <div class="modal" style="width:450px; background:#0F172A; border:1px solid rgba(255,255,255,0.1); border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,0.6); overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg,#1E1B4B,#312E81); border-bottom:1px solid rgba(255,255,255,0.1); padding:16px 20px;">
      <div class="modal-title" style="color:#A5B4FC; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-bell" style="color:#818CF8;"></i>
        Notifications &amp; Live Alerts
      </div>
      <button class="modal-close" onclick="closeModal('notifModal')" style="color:#94A3B8;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:16px 20px; max-height:360px; overflow-y:auto; display:flex; flex-direction:column; gap:10px;">
      <div style="display:flex; align-items:flex-start; gap:12px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); border-radius:12px; padding:12px;">
        <div style="width:34px; height:34px; border-radius:10px; background:rgba(239,68,68,0.2); color:#F87171; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <i class="fa-solid fa-bell-concierge"></i>
        </div>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:#F8FAFC;">Waiter Called — Table 4</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">Customer requested waiter service at Table 4</div>
          <div style="font-size:10.5px; color:#64748B; margin-top:4px;">2 mins ago</div>
        </div>
      </div>

      <div style="display:flex; align-items:flex-start; gap:12px; background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.25); border-radius:12px; padding:12px;">
        <div style="width:34px; height:34px; border-radius:10px; background:rgba(16,185,129,0.2); color:#34D399; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <i class="fa-solid fa-utensils"></i>
        </div>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:#F8FAFC;">KOT Ready — Table 2</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">Paneer Butter Masala &amp; Garlic Naan prepared by Kitchen</div>
          <div style="font-size:10.5px; color:#64748B; margin-top:4px;">8 mins ago</div>
        </div>
      </div>

      <div style="display:flex; align-items:flex-start; gap:12px; background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.25); border-radius:12px; padding:12px;">
        <div style="width:34px; height:34px; border-radius:10px; background:rgba(245,158,11,0.2); color:#FCD34D; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:#F8FAFC;">Low Stock Alert</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">Milk inventory is below 2 Litres threshold</div>
          <div style="font-size:10.5px; color:#64748B; margin-top:4px;">15 mins ago</div>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="padding:14px 20px; background:#0B0F19; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center;">
      <button class="btn-outline" onclick="showToast('All notifications marked as read', 'success'); closeModal('notifModal');" style="padding:8px 14px; font-size:12px; font-weight:600; border-radius:8px;">Mark All as Read</button>
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
  <div class="modal" style="width:360px;">
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
  <div class="modal selection-popup-modal" style="width:780px; max-width:95%;">
    <div class="modal-header">
      <div class="modal-title"><i class="fa-solid fa-receipt" style="color:var(--primary);"></i>&nbsp; Recent Orders History</div>
      <button class="modal-close" onclick="closeModal('ordersModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:15px; overflow-x:auto;">
      <table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px; color:var(--text-primary);">
        <thead>
          <tr style="border-bottom:2px solid var(--border); color:var(--text-secondary); text-transform:uppercase; font-size:11px;">
            <th style="padding:10px;">ID</th>
            <th style="padding:10px;">Table</th>
            <th style="padding:10px;">Items Summary</th>
            <th style="padding:10px;">Amount</th>
            <th style="padding:10px;">Payment</th>
            <th style="padding:10px;">Actions</th>
          </tr>
        </thead>
        <tbody id="ordersModalTableBody">
          <!-- Loaded dynamically from API -->
        </tbody>
      </table>
    </div>
</div>

<!-- Admin Module Iframe Modal -->
<div class="modal-overlay" id="adminModuleModal">
  <div class="modal" style="width:96vw; max-width:1240px; height:92vh; max-height:860px; display:flex; flex-direction:column; padding:0; border-radius:20px; overflow:hidden; background:var(--card); border:1px solid var(--border); box-shadow:0 24px 60px rgba(0,0,0,0.6);">
    <div class="modal-header" style="padding:14px 22px; background:#0B0F19; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
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
  <div class="modal print-hub-popup" style="width:380px; max-width:92%; background:rgba(30,41,59,0.95); backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.08); box-shadow:0 24px 60px rgba(0,0,0,0.5); border-radius:24px; padding:0; overflow:hidden;">
    <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,0.06); padding:16px 20px; display:flex; align-items:center; justify-content:space-between; width: 100%;">
      <div class="modal-title" style="color:#34D399; font-family:'Outfit',sans-serif; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:50%; background:rgba(52,211,153,0.15); box-shadow:0 0 12px rgba(52,211,153,0.25);"><i class="fa-solid fa-check" style="font-size:12px;"></i></span>
        Order Placed Successfully!
      </div>
      <button class="modal-close" onclick="closeModal('printHubModal')" style="color:var(--text-secondary); opacity:0.6; transition:opacity .18s; cursor:pointer;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.6"><i class="fa-solid fa-xmark"></i></button>
    </div>
    
    <div class="modal-body" style="text-align:center; padding:24px 20px;">
      <!-- Glowing Icon Wrapper -->
      <div style="position:relative; width:80px; height:80px; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.1)); border:1px solid rgba(16,185,129,0.2); border-radius:50%; box-shadow:0 0 30px rgba(16,185,129,0.15);">
        <i class="fa-solid fa-print" style="font-size:36px; color:#34D399; text-shadow:0 0 10px rgba(52,211,153,0.4);"></i>
      </div>
      
      <h3 style="font-size:18px; font-weight:800; color:#F8FAFC; margin-bottom:4px; font-family:'Outfit',sans-serif; letter-spacing:0.3px;" id="printHubOrderId">Order ID: #--</h3>
      <p style="font-size:12.5px; color:#94A3B8; margin-bottom:24px; font-weight:500;">Choose tickets to print for this order.</p>
      
      <div style="display:flex; flex-direction:column; gap:12px;">
        <!-- Print All Tickets -->
        <button onclick="triggerPrintHubAction('all')" class="ph-btn" style="width:100%; height:44px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #10B981, #059669); color:#FFFFFF; font-size:13.5px; font-weight:700; cursor:pointer; box-shadow:0 4px 15px rgba(16,185,129,0.3); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16,185,129,0.45)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(16,185,129,0.3)';">
          <i class="fa-solid fa-print" style="font-size:14px;"></i> Print All Tickets
        </button>

        <!-- Print KOTs Only -->
        <button id="printHubKOTsOnlyBtn" onclick="triggerPrintHubAction('kots_only')" class="ph-btn" style="width:100%; height:44px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #F59E0B, #D97706); color:#FFFFFF; font-size:13.5px; font-weight:700; cursor:pointer; box-shadow:0 4px 15px rgba(245,158,11,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(245,158,11,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(245,158,11,0.25)';">
          <i class="fa-solid fa-fire-burner" style="font-size:14px;"></i> Print KOTs Only (Kitchen + Bar)
        </button>
        
        <div style="display:flex; align-items:center; justify-content:center; margin:6px 0;">
          <div style="flex:1; height:1px; background:rgba(255,255,255,0.06);"></div>
          <span style="font-size:10px; font-weight:700; text-transform:uppercase; color:#64748B; letter-spacing:1px; padding:0 10px;">or print individual</span>
          <div style="flex:1; height:1px; background:rgba(255,255,255,0.06);"></div>
        </div>

        <!-- Receipt -->
        <button onclick="triggerPrintHubAction('receipt')" class="ph-btn" style="width:100%; height:40px; display:flex; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#FFFFFF; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 12px rgba(99,102,241,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(99,102,241,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(99,102,241,0.25)';">
          <i class="fa-solid fa-file-invoice-dollar" style="font-size:13px;"></i> Print Customer Receipt
        </button>
        
        <!-- Kitchen -->
        <button id="printHubKitchenBtn" onclick="triggerPrintHubAction('kitchen')" class="ph-btn" style="width:100%; height:40px; display:none; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #F59E0B, #D97706); color:#FFFFFF; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 12px rgba(245,158,11,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(245,158,11,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.25)';">
          <i class="fa-solid fa-fire-burner" style="font-size:13px;"></i> Print Kitchen KOT
        </button>
        
        <!-- Bar -->
        <button id="printHubBarBtn" onclick="triggerPrintHubAction('bar')" class="ph-btn" style="width:100%; height:40px; display:none; align-items:center; justify-content:center; gap:8px; border:none; border-radius:12px; background:linear-gradient(135deg, #06B6D4, #0891B2); color:#FFFFFF; font-size:13px; font-weight:600; cursor:pointer; box-shadow:0 4px 12px rgba(6,182,212,0.25); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 18px rgba(6,182,212,0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(6,182,212,0.25)';">
          <i class="fa-solid fa-glass-water" style="font-size:13px;"></i> Print Bar KOT
        </button>
      </div>
    </div>
    <div class="modal-footer" style="justify-content:center; border-top:1px solid rgba(255,255,255,0.06); padding:16px 20px; background:rgba(0,0,0,0.1);">
      <button class="btn-outline" style="min-width:110px; height:36px; border:1px solid rgba(255,255,255,0.12); background:rgba(255,255,255,0.05); color:#E2E8F0; font-weight:600; border-radius:10px; font-size:12.5px; flex:none; transition:all 0.2s; cursor:pointer;" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#FFFFFF';" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#E2E8F0';" onclick="closeModal('printHubModal')">Done</button>
    </div>
  </div>
</div>

<!-- Live UPI Payment QR Code Modal (Ultra-Premium Redesign) -->
<div class="modal-overlay" id="upiQrModal">
  <div class="modal" style="width:440px; max-width:95%; background:linear-gradient(145deg, #0F172A, #0B0F19); color:#F8FAFC; border-radius:24px; border:1px solid rgba(16,185,129,0.3); box-shadow:0 25px 60px rgba(0,0,0,0.8), 0 0 30px rgba(16,185,129,0.15); overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg, #064E3B, #022C22); border-bottom:1px solid rgba(16,185,129,0.3); padding:16px 20px;">
      <div class="modal-title" style="color:#A7F3D0; font-size:16px; font-weight:800; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-qrcode" style="font-size:22px; color:#34D399;"></i>
        Live UPI Payment Terminal
      </div>
      <button class="modal-close" onclick="closeModal('upiQrModal')" style="color:#94A3B8;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    
    <div class="modal-body" style="padding:20px; text-align:center;">
      <!-- Accepted Payment Apps Row -->
      <div style="display:flex; justify-content:center; align-items:center; gap:12px; margin-bottom:12px; font-size:11px; color:#94A3B8; background:rgba(255,255,255,0.03); padding:6px 12px; border-radius:20px; border:1px solid rgba(255,255,255,0.06);">
        <span style="font-weight:700; color:#34D399;">Accepts:</span>
        <span style="display:inline-flex; align-items:center; gap:4px; font-weight:600; color:#E2E8F0;"><i class="fa-brands fa-google-pay" style="color:#4285F4; font-size:14px;"></i> GPay</span>
        <span>•</span>
        <span style="font-weight:600; color:#E2E8F0;">PhonePe</span>
        <span>•</span>
        <span style="font-weight:600; color:#E2E8F0;">Paytm</span>
        <span>•</span>
        <span style="font-weight:600; color:#E2E8F0;">BHIM UPI</span>
      </div>

      <div style="font-size:12px; color:#94A3B8; margin-bottom:2px;" id="upiModalTable">Table 1</div>
      <div style="font-size:28px; font-weight:900; color:#34D399; font-family:'Outfit',sans-serif; letter-spacing:-0.5px;" id="upiModalTotal">₹0.00</div>
      <div style="font-size:11px; color:#64748B; margin-bottom:14px;">Scan with any UPI app to pay instantly</div>

      <!-- High Res Live UPI QR Code Container with Glowing Effect -->
      <div style="position:relative; display:inline-block; margin-bottom:12px;">
        <div style="background:#FFFFFF; padding:12px; border-radius:20px; box-shadow:0 12px 30px rgba(0,0,0,0.6), 0 0 20px rgba(52,211,153,0.3); border:3px solid #34D399; position:relative; z-index:2;">
          <img id="upiQrImg" src="" alt="UPI Payment QR Code" style="width:190px; height:190px; display:block; border-radius:8px;" />
        </div>
      </div>

      <!-- Receiver Merchant VPA Handle Input -->
      <div style="margin-bottom:14px; display:flex; align-items:center; justify-content:center; gap:6px; font-size:11.5px; color:#94A3B8; background:rgba(255,255,255,0.03); padding:8px 12px; border-radius:12px; border:1px solid rgba(255,255,255,0.08);">
        <i class="fa-solid fa-building-columns" style="color:#34D399;"></i>
        <span style="font-weight:600;">UPI ID:</span>
        <input type="text" id="merchantVpaInput" value="7777938338@omni" onchange="updateUpiQrCodeFromInput()" onkeyup="updateUpiQrCodeFromInput()" style="background:#1E293B; color:#34D399; border:1px solid #334155; padding:5px 10px; border-radius:8px; font-weight:800; font-size:12px; width:180px; text-align:center; outline:none;" placeholder="7777938338@omni" />
        <button onclick="copyUpiVpa()" style="background:rgba(52,211,153,0.15); border:1px solid rgba(52,211,153,0.3); color:#34D399; padding:4px 8px; border-radius:6px; font-size:11px; font-weight:700; cursor:pointer;" title="Copy UPI ID"><i class="fa-regular fa-copy"></i></button>
      </div>

      <!-- Itemized Price Breakdown Table -->
      <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:14px; padding:12px 14px; font-size:12px; color:#CBD5E1;">
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
          <span>Subtotal:</span>
          <span id="upiModalSubtotal" style="font-weight:600;">₹0.00</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
          <span>Service Charge (5%):</span>
          <span id="upiModalService" style="font-weight:600;">₹0.00</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span>GST (18%):</span>
          <span id="upiModalGst" style="font-weight:600;">₹0.00</span>
        </div>
        <div style="display:flex; justify-content:space-between; border-top:1px dashed rgba(255,255,255,0.15); padding-top:6px; font-weight:800; color:#34D399; font-size:14px;">
          <span>Total Payable Amount:</span>
          <span id="upiModalBreakdownTotal">₹0.00</span>
        </div>
      </div>
    </div>

    <div class="modal-footer" style="padding:16px 20px; background:#0B0F19; border-top:1px solid rgba(255,255,255,0.08); display:flex; gap:12px; align-items:center;">
      <button class="btn-outline" onclick="closeModal('upiQrModal')" style="flex:1; padding:10px; font-weight:700; border-radius:10px; height:44px; font-size:13px;">Cancel</button>
      <button onclick="confirmUpiPaymentCompleted()" class="btn-checkout" style="flex:2; padding:10px 16px; font-size:13px; font-weight:800; border-radius:12px; background:linear-gradient(135deg, #10B981, #059669); color:#fff; display:flex; align-items:center; justify-content:center; gap:8px; border:none; cursor:pointer; height:44px; box-shadow:0 4px 16px rgba(16,185,129,0.4);">
        <i class="fa-solid fa-volume-high" style="font-size:15px;"></i> Verify &amp; Announce Voice 🔊
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
      if (id === 'catPopupModal') { const btn = document.getElementById('catPopupBtn'); if (btn) btn.classList.add('open'); }
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
    openAdminModule('/z-report', '<i class="fa-solid fa-chart-line" style="color:var(--primary);"></i> 📊 Daily Sales Z-Report');
  }
  function openFeedbackModule() {
    openAdminModule('/admin/feedback', '<i class="fa-solid fa-comment-dots" style="color:var(--primary);"></i> ⭐ Reviews & Feedback');
  }
  function openTableManager() {
    openAdminModule('/admin/tables', '<i class="fa-solid fa-table-cells-large" style="color:var(--primary);"></i> 🪑 Table Manager & QR Generator');
  }


  // ─── VOCAL SOUNDBOX STATE & HELPER ───
  let isPosVoiceMuted = localStorage.getItem('dineflow_voice_muted') === 'true';

  let announcedOrderIds = JSON.parse(localStorage.getItem('dineflow_announced_orders') || '[]').map(String);

  function updatePosMuteUI() {
    const icon = document.getElementById('posMuteIcon');
    const text = document.getElementById('posMuteText');
    const btn = document.getElementById('posVoiceMuteBtn');
    if (!icon || !text || !btn) return;

    if (isPosVoiceMuted) {
      icon.className = 'fa-solid fa-volume-xmark';
      icon.style.color = '#EF4444';
      text.textContent = 'સાઉન્ડબોક્સ મ્યૂટ 🔇';
      btn.style.borderColor = 'rgba(239, 68, 68, 0.4)';
      btn.style.background = 'rgba(239, 68, 68, 0.08)';
      btn.style.color = '#EF4444';
    } else {
      icon.className = 'fa-solid fa-volume-high';
      icon.style.color = '#10B981';
      text.textContent = 'ગુજરાતી સાઉન્ડબોક્સ: ઓન 🔊';
      btn.style.borderColor = 'rgba(16, 185, 129, 0.4)';
      btn.style.background = 'rgba(16, 185, 129, 0.08)';
      btn.style.color = '#10B981';
    }
  }

  function togglePosVoiceMute() {
    isPosVoiceMuted = !isPosVoiceMuted;
    localStorage.setItem('dineflow_voice_muted', isPosVoiceMuted ? 'true' : 'false');
    updatePosMuteUI();
    if (!isPosVoiceMuted) {
      announcePaymentVoice('Test', 0, 'test mode');
    }
  }

  function announcePaymentVoice(tableNumber, amount, method = 'online') {
    if (isPosVoiceMuted) return;

    if (!('speechSynthesis' in window)) {
      console.log('Text-to-speech not supported in this browser.');
      return;
    }
    
    // Clear any stuck/queued audio speech synthesis
    window.speechSynthesis.cancel();
    
    // Extract clean numeric/name table number (e.g. "Table #1" -> "1", "Table 5" -> "5")
    let cleanTableNum = String(tableNumber).replace(/table/gi, '').replace(/#/g, '').replace(/T-/gi, '').trim();
    if (!cleanTableNum || cleanTableNum === 'Walk-In') cleanTableNum = '1';

    // Construct ultra-professional announcement in Gujarati!
    let msgText = "";
    if (tableNumber === 'Test') {
      msgText = "ડાઇનફ્લો એઆઇ સ્માર્ટ સાઉન્ડબોક્સ સફળતાપૂર્વક સક્રિય છે.";
    } else {
      const cashOrOnline = (method === 'cash') ? 'રોકડા' : 'ઓનલાઇન';
      msgText = `ડાઇનફ્લો એઆઇ પર ટેબલ નંબર ${cleanTableNum} ના ${amount} રૂપિયા ${cashOrOnline} સફળતાપૂર્વક પ્રાપ્ત થયા છે.`;
    }
    
    const utterance = new SpeechSynthesisUtterance(msgText);
    utterance.lang = 'gu-IN';
    utterance.rate = 0.95;
    utterance.pitch = 1.0;
    
    const voices = window.speechSynthesis.getVoices();
    if (voices && voices.length > 0) {
      const targetVoice = voices.find(v => v.lang.toLowerCase().includes('gu')) || 
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

  document.addEventListener('DOMContentLoaded', updatePosMuteUI);
  updatePosMuteUI();

  /* ─── DATA ─────────────────────────────────── */
  /* ─── DATA & API SYNC ────────────────────────── */
  let MENU = [
    { id:1,  name:'Paneer Butter Masala', cat:'mains',     price:240, emoji:'🍛', badge:'veg',    desc:'Creamy tomato butter curry',   qty:0 },
    { id:2,  name:'Chicken Tikka Masala',  cat:'mains',     price:320, emoji:'🍗', badge:'nonveg', desc:'Smoky grilled chicken curry',   qty:0 },
    { id:3,  name:'Garlic Naan',           cat:'breads',    price:60,  emoji:'🫓', badge:'veg',    desc:'Soft buttery garlic flatbread', qty:0 },
    { id:4,  name:'Veg Spring Rolls',      cat:'starters',  price:180, emoji:'🥢', badge:'veg',    desc:'Crispy vegetable rolls',        qty:0 },
    { id:5,  name:'Chicken Biryani',       cat:'rice',      price:380, emoji:'🍚', badge:'nonveg', desc:'Fragrant basmati rice',         qty:0 },
    { id:6,  name:'Mango Lassi',           cat:'beverages', price:120, emoji:'🥭', badge:'veg',    desc:'Chilled mango yogurt drink',    qty:0 },
    { id:7,  name:'Gulab Jamun',           cat:'desserts',  price:80,  emoji:'🍮', badge:'new',    desc:'Soft milk solid dumplings',     qty:0 },
    { id:8,  name:'Dal Tadka',             cat:'mains',     price:180, emoji:'🫕', badge:'veg',    desc:'Tempered yellow lentils',       qty:0 },
    { id:9,  name:'Fish Fry',              cat:'starters',  price:280, emoji:'🐟', badge:'nonveg', desc:'Spiced crispy coastal fish',    qty:0 },
    { id:10, name:'Laccha Paratha',        cat:'breads',    price:70,  emoji:'🥙', badge:'veg',    desc:'Layered whole wheat bread',     qty:0 },
    { id:11, name:'Veg Biryani',           cat:'rice',      price:280, emoji:'🍙', badge:'best',   desc:'Aromatic vegetable biryani',    qty:0 },
    { id:12, name:'Cold Coffee',           cat:'beverages', price:140, emoji:'☕', badge:'veg',    desc:'Rich blended cold coffee',      qty:0 },
  ];

  async function syncPosMenuWithDatabase() {
    try {
      const res = await fetch('/api/menu/restaurant/1');
      const data = await res.json();
      if (res.ok && (data.success || Array.isArray(data.data)) && Array.isArray(data.data) && data.data.length > 0) {
        MENU = data.data.map(item => {
          const catLower = (item.category || 'mains').toLowerCase();
          let catKey = 'mains';
          let emoji = item.is_veg !== false ? '🟢' : '🔴';
          
          if (catLower.includes('starter') || catLower.includes('appetizer')) { catKey = 'starters'; emoji = '🥢'; }
          else if (catLower.includes('bread') || catLower.includes('roti') || catLower.includes('naan')) { catKey = 'breads'; emoji = '🫓'; }
          else if (catLower.includes('rice') || catLower.includes('biryani')) { catKey = 'rice'; emoji = '🍚'; }
          else if (catLower.includes('dessert') || catLower.includes('sweet')) { catKey = 'desserts'; emoji = '🍮'; }
          else if (catLower.includes('drink') || catLower.includes('beverage')) { catKey = 'beverages'; emoji = '🍹'; }
          else if (catLower.includes('pizza')) { catKey = 'mains'; emoji = '🍕'; }
          else if (catLower.includes('burger')) { catKey = 'starters'; emoji = '🍔'; }

          return {
            id: item.id,
            name: item.name,
            cat: catKey,
            price: parseFloat(item.price),
            emoji: emoji,
            badge: item.is_veg !== false ? 'veg' : 'nonveg',
            desc: item.description || '',
            qty: 0,
            is_available: item.is_available !== false
          };
        });
        renderMenu();
        renderCategories();
      }
    } catch (err) {
      console.warn('POS dynamic menu sync warning, keeping fallback', err);
    }
  }

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
    waiter: 'Ravi M.',
    covers: '2',
    priority: 'Normal',
  };

  let currentCat = 'all';
  let searchTerm = '';

  function getCats() {
    return [
      { key:'all',       label:'All Items',      emoji:'🍽️', count: MENU.length },
      { key:'starters',  label:'Starters',       emoji:'🥢', count: MENU.filter(i=>(i.cat||'').toLowerCase()==='starters').length },
      { key:'mains',     label:'Main Course',    emoji:'🍛', count: MENU.filter(i=>(i.cat||'').toLowerCase()==='mains').length },
      { key:'breads',    label:'Breads',         emoji:'🫓', count: MENU.filter(i=>(i.cat||'').toLowerCase()==='breads').length },
      { key:'rice',      label:'Rice & Biryani', emoji:'🍚', count: MENU.filter(i=>(i.cat||'').toLowerCase()==='rice').length },
      { key:'desserts',  label:'Desserts',       emoji:'🍮', count: MENU.filter(i=>(i.cat||'').toLowerCase()==='desserts').length },
      { key:'beverages', label:'Beverages',      emoji:'🥭', count: MENU.filter(i=>(i.cat||'').toLowerCase()==='beverages' || (i.cat||'').toLowerCase()==='drinks').length },
    ];
  }

  /* ─── RENDER MENU ────────────────────────── */
  function renderMenu() {
    const grid = document.getElementById('menuGrid');
    const items = MENU.filter(i => {
      const itemCat = (i.cat || '').toLowerCase();
      const targetCat = currentCat.toLowerCase();
      const matchCat  = targetCat === 'all' || itemCat === targetCat || (targetCat === 'beverages' && itemCat === 'drinks');
      const matchSearch = i.name.toLowerCase().includes(searchTerm.toLowerCase());
      return matchCat && matchSearch;
    });

    grid.innerHTML = items.map(item => `
      <div class="item-card ${item.qty > 0 ? 'in-cart' : ''}" onclick="addQuickItem('${item.id}')">
        <div class="item-img">
          ${item.emoji}
          <span class="item-badge ${item.badge}">${item.badge.toUpperCase()}</span>
        </div>
        <div class="item-body">
          <div class="item-name">${item.name}</div>
          <div class="item-footer">
            <div class="item-price">₹ ${item.price}<small>/plate</small></div>
            <div class="item-footer-actions">
              ${item.qty > 0
                ? `<div class="item-in-cart-badge">${item.qty}</div>`
                : `<button class="item-add-btn" onclick="event.stopPropagation();addQuickItem('${item.id}')">+</button>`
              }
            </div>
          </div>
        </div>
      </div>
    `).join('');
  }

  /* ─── RENDER CART ────────────────────────── */
  function renderCart() {
    const cartItems = MENU.filter(i => i.qty > 0);
    const el = document.getElementById('cartItems');

    el.innerHTML = cartItems.map(item => `
      <div class="cart-item">
        <div class="ci-info">
          <div class="ci-top">
            <div class="ci-main">
              <div class="ci-name">${item.name}</div>
              <div class="ci-variant">Regular</div>
            </div>
            <div class="ci-price">₹ ${item.price * item.qty}</div>
          </div>
          <div class="ci-bottom">
            <div class="ci-controls">
              <div class="ci-btn" onclick="updateQty('${item.id}',-1)">−</div>
              <div class="ci-qty">${item.qty}</div>
              <div class="ci-btn" onclick="updateQty('${item.id}',1)">+</div>
            </div>
            <button class="ci-del" onclick="removeItem('${item.id}')"><i class="fa-solid fa-trash-can"></i></button>
          </div>
        </div>
      </div>
    `).join('');

    updateTotals();
  }

  function updateTotals() {
    const cartItems = MENU.filter(i => i.qty > 0);
    const total = cartItems.reduce((sum, item) => sum + item.qty, 0);
    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.qty, 0);
    const serviceCharge = subtotal * 0.05;
    const gst = (subtotal + serviceCharge) * 0.18;
    const grandTotal = Math.round(subtotal + serviceCharge + gst);

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
      item.qty += 1;
      renderMenu();
      renderCart();
      showToast(item.name + ' added to cart', 'success');
    }
  }

  function updateQty(id, delta) {
    const item = MENU.find(i => String(i.id) === String(id));
    if (item) {
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
    MENU.forEach(i => i.qty = 0);
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

    grid.innerHTML = categories.map(c => `
      <div class="cat-popup-item ${currentCat === c.key ? 'active' : ''}" onclick="selectCat('${c.key}','${c.label}')" style="display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:12px; background:${currentCat === c.key ? 'rgba(99,102,241,0.2)' : 'rgba(255,255,255,0.03)'}; border:1px solid ${currentCat === c.key ? 'rgba(99,102,241,0.4)' : 'rgba(255,255,255,0.06)'}; cursor:pointer; transition:all 0.2s;">
        <span class="cpi-emoji" style="font-size:22px;">${c.emoji}</span>
        <div style="flex:1;">
          <div class="cpi-name" style="font-size:13.5px; font-weight:700; color:#F8FAFC;">${c.label}</div>
          <div class="cpi-count" style="font-size:11px; color:#94A3B8;">${c.count} items</div>
        </div>
        ${currentCat === c.key ? '<i class="fa-solid fa-check-circle" style="color:var(--primary); font-size:16px;"></i>' : ''}
      </div>
    `).join('');
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

  function selectCat(key, label) {
    currentCat = key;
    document.getElementById('catPopupLabel').textContent = key === 'all' ? 'All Categories' : label;
    document.getElementById('catPopupBtn').classList.toggle('filtered', key !== 'all');
    closeModal('catPopupModal');
    renderMenu();
  }

  function resetCat() { selectCat('all','All Items'); }

  /* ─── FILTER ─────────────────────────────── */
  function filterItems(val) {
    searchTerm = val || '';
    renderMenu();
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
    document.querySelectorAll('.delivery-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const tableGrp  = document.getElementById('tableGroup');
    const coversGrp = document.getElementById('coversGroup');
    tableGrp.style.display  = (type === 'dinein') ? 'flex' : 'none';
    coversGrp.style.display = (type === 'dinein') ? 'flex' : 'none';
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
      const response = await fetch('/api/tables/restaurant/1');
      const data = await response.json();
      if (data.success && Array.isArray(data.data)) {
        allTablesData = data.data;
        // Reset filters
        document.getElementById('tableFloorSelect').value = 'all';
        document.getElementById('tableSectionSelect').value = 'all';
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

    document.querySelectorAll('.table-cell').forEach(c => c.classList.remove('selected'));
    el.className = 'table-cell selected';
    el.querySelector('span:last-child').textContent = 'Selected';
    
    // Dynamically sync and load existing unpaid items for the selected table
    fetch('/api/orders/restaurant/1')
      .then(res => res.json())
      .then(data => {
        if (data.success && Array.isArray(data.data)) {
          const activeOrder = data.data.find(order => String(order.table_id) === String(id) && order.payment_status === 'pending');
          if (activeOrder && Array.isArray(activeOrder.items)) {
            MENU.forEach(item => item.qty = 0);
            activeOrder.items.forEach(orderItem => {
              const menuItem = MENU.find(m => m.id === orderItem.menu_item_id);
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

  /* ─── ORDERS HISTORY ──────────────────────── */
  async function loadOrdersData() {
    try {
      const response = await fetch('/api/orders/restaurant/1');
      const data = await response.json();
      if (data.success && Array.isArray(data.data)) {
        const tbody = document.getElementById('ordersModalTableBody');
        if (data.data.length === 0) {
          tbody.innerHTML = `<tr><td colspan="6" style="padding:20px; text-align:center; color:var(--text-secondary);">No orders found.</td></tr>`;
          return;
        }
        
        const sortedOrders = [...data.data].reverse();
        
        tbody.innerHTML = sortedOrders.map(order => {
          const itemsSummary = order.items ? order.items.map(item => `${item.name} (x${item.quantity})`).join(', ') : 'Order Items';
          const isPaid = (String(order.payment_status).toLowerCase() === 'paid');
          
          let payBadge = `<span style="background:rgba(239,68,68,0.1); color:#FCA5A5; border:1px solid rgba(239,68,68,0.3); padding:2px 8px; border-radius:12px; font-size:11px; font-weight:700;">Unpaid</span>`;
          let actionBtns = `
            <button onclick="payOrder(${order.id})" class="btn-outline" style="display:inline-flex; align-items:center; justify-content:center; flex:none; padding:4px 10px; height:26px; font-size:11px; color:#10B981; border-color:rgba(16,185,129,0.4); background:rgba(16,185,129,0.1); margin-right:5px; cursor:pointer;">
              <i class="fa-solid fa-circle-check"></i>&nbsp;Pay
            </button>
          `;
          
          if (isPaid) {
            payBadge = `<span style="background:rgba(16,185,129,0.1); color:#34D399; border:1px solid rgba(16,185,129,0.3); padding:2px 8px; border-radius:12px; font-size:11px; font-weight:700;">Paid</span>`;
            actionBtns = '';
          }
          
          actionBtns += `
            <button onclick="reprintOrder(${order.id})" class="btn-outline" style="display:inline-flex; align-items:center; justify-content:center; flex:none; padding:4px 10px; height:26px; font-size:11px; color:#818CF8; border-color:rgba(99,102,241,0.4); background:rgba(99,102,241,0.1); cursor:pointer;">
              <i class="fa-solid fa-print"></i>&nbsp;Print
            </button>
          `;
          
          const tableNum = order.table ? order.table.table_number : 'Table #' + order.table_id;
          
          let grandTotal = parseFloat(order.total_amount || 0);
          if (Array.isArray(order.items) && order.items.length > 0) {
            const rawItemsSum = order.items.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
            const serviceCharge = rawItemsSum * 0.05;
            const gst = (rawItemsSum + serviceCharge) * 0.18;
            grandTotal = Math.round(rawItemsSum + serviceCharge + gst);
          } else {
            grandTotal = Math.round(grandTotal);
          }

          return `
            <tr style="border-bottom:1px solid var(--border);">
              <td style="padding:12px 10px; font-weight:700;">#${order.id}</td>
              <td style="padding:12px 10px; font-weight:600;">${tableNum}</td>
              <td style="padding:12px 10px; max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--text-secondary);" title="${itemsSummary}">${itemsSummary}</td>
              <td style="padding:12px 10px; font-weight:700; color:var(--primary);">₹${grandTotal}</td>
              <td style="padding:12px 10px;">${payBadge}</td>
              <td style="padding:12px 10px; white-space:nowrap;">${actionBtns}</td>
            </tr>
          `;
        }).join('');
      }
    } catch (err) {
      console.log('Error loading orders:', err);
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

  let currentUpiPayAmount = 0;

  function copyUpiVpa() {
    const input = document.getElementById('merchantVpaInput');
    if (input) {
      navigator.clipboard.writeText(input.value);
      showToast('UPI ID Copied: ' + input.value, 'success');
    }
  }

  function updateUpiQrCodeFromInput() {
    const inputVal = document.getElementById('merchantVpaInput')?.value.trim() || '7777938338@omni';
    localStorage.setItem('dineflow_merchant_vpa', inputVal);
    const tableNumber = document.getElementById('selectedTableLabel')?.textContent || 'Walk-In';
    const upiString = `upi://pay?pa=${encodeURIComponent(inputVal)}&pn=${encodeURIComponent('DineFlow AI Restaurant')}&am=${currentUpiPayAmount}.00&cu=INR&tn=${encodeURIComponent('Bill Payment for ' + tableNumber)}`;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(upiString)}`;
    document.getElementById('upiQrImg').src = qrUrl;
  }

  function openUpiQrModal() {
    try {
      showToast('Opening Live UPI QR Terminal...', 'info');

      let activeCartItems = (typeof MENU !== 'undefined' && Array.isArray(MENU)) ? MENU.filter(i => i.qty > 0) : [];
      if (activeCartItems.length === 0 && typeof MENU !== 'undefined' && MENU.length > 0) {
        MENU[0].qty = 1;
        if (typeof renderCart === 'function') renderCart();
        if (typeof renderMenu === 'function') renderMenu();
        activeCartItems = MENU.filter(i => i.qty > 0);
      }
      let subtotal = 0;
      let serviceCharge = 0;
      let gst = 0;
      let finalGrandTotal = 0;

      if (activeCartItems.length > 0) {
        subtotal = activeCartItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
        serviceCharge = subtotal * 0.05;
        gst = (subtotal + serviceCharge) * 0.18;
        finalGrandTotal = Math.round(subtotal + serviceCharge + gst);
      } else if (selectedTableId && typeof allOrdersData !== 'undefined' && Array.isArray(allOrdersData)) {
        const activeOrder = allOrdersData.find(o => String(o.table_id) === String(selectedTableId) && o.payment_status === 'pending');
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
          finalGrandTotal = parsedVal;
          subtotal = Math.round(finalGrandTotal / 1.239);
          serviceCharge = subtotal * 0.05;
          gst = subtotal * 0.18;
        }
      }

      const tableNumber = document.getElementById('selectedTableLabel')?.textContent || document.getElementById('selectedTableNo')?.textContent || 'Walk-In';
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
      if (totEl) totEl.textContent = finalGrandTotal > 0 ? '₹' + finalGrandTotal.toFixed(2) : '₹ 0.00';

      const brkEl = document.getElementById('upiModalBreakdownTotal');
      if (brkEl) brkEl.textContent = finalGrandTotal > 0 ? '₹' + finalGrandTotal.toFixed(2) : '₹ 0.00';

      const savedVpa = localStorage.getItem('dineflow_merchant_vpa') || '7777938338@omni';
      if (document.getElementById('merchantVpaInput')) {
        document.getElementById('merchantVpaInput').value = savedVpa;
      }

      updateUpiQrCodeFromInput();

      const modal = document.getElementById('upiQrModal');
      if (modal) {
        modal.style.zIndex = '99999';
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        modal.style.pointerEvents = 'all';
        modal.classList.add('show');
      } else {
        openModal('upiQrModal');
      }
    } catch (err) {
      console.error('Error opening UPI QR Modal:', err);
      openModal('upiQrModal');
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

      if (selectedTableId && typeof allOrdersData !== 'undefined' && Array.isArray(allOrdersData)) {
        const activeOrder = allOrdersData.find(o => String(o.table_id) === String(selectedTableId) && o.payment_status === 'pending');
        if (activeOrder) {
          await fetch(`/api/orders/${activeOrder.id}/pay`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ payment_method: 'upi' })
          });
        }
      }

      announcePaymentVoice(tableNumber, currentUpiPayAmount, 'online');
      showToast(`✅ UPI Payment of ₹${currentUpiPayAmount} Verified & Marked PAID for ${tableNumber}!`, 'success');
      closeModal('upiQrModal');
    } catch (err) {
      console.error('Error confirming UPI payment:', err);
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
        splitRow.style.display = labelText.toLowerCase().includes('split') ? 'flex' : 'none';
      }

      if (labelText.toLowerCase().includes('upi') || labelText.toLowerCase().includes('gpay') || labelText.toLowerCase().includes('qr')) {
        openUpiQrModal();
      }
    } catch (err) {
      console.error('Error in selectPay:', err);
      openUpiQrModal();
    }
  }

  /* ─── COUPON ─────────────────────────────── */
  function applyCoupon() { showToast('Coupon applied! 20% off', 'success'); }

  /* ─── KOT ────────────────────────────────── */
  function sendKOT() {
    const cartItems = MENU.filter(i => i.qty > 0);
    if (cartItems.length === 0) {
      showToast('Cart is empty', 'error');
      return;
    }
    
    let kotHTML = '';
    cartItems.forEach(item => {
      kotHTML += `<div class="kot-item-row"><span><b>${item.qty}x</b> ${item.name}</span><span></span></div>`;
    });
    document.getElementById('kotList').innerHTML = kotHTML;
    
    showToast('KOT sent to kitchen!', 'success');
    closeModal('kotModal');
  }

  /* ─── ACTIONS ────────────────────────────── */
  function holdOrder() { showToast('Order placed on hold', 'warning'); }
  
  let lastPlacedOrderId = null;
  let activeOrderHasKitchen = true; // Fallback to true if manually clicked
  let activeOrderHasBar = true;
  let selectedTableId = 1;

  function checkout() {
    let cartItems = MENU.filter(i => i.qty > 0);
    if (cartItems.length === 0) {
      if (MENU.length > 0) {
        MENU[0].qty = 1;
        renderCart();
        renderMenu();
        cartItems = MENU.filter(i => i.qty > 0);
        showToast('Auto-selected ' + MENU[0].name + ' for checkout', 'info');
      } else {
        showToast('Please select any menu item to place order', 'warning');
        return;
      }
    }

    // Determine if we need to print Kitchen KOT or Bar KOT based on cart categories
    activeOrderHasKitchen = cartItems.some(item => ['mains', 'starters', 'breads', 'rice', 'pizza', 'burger', 'pasta', 'soup'].includes(item.cat.toLowerCase()));
    activeOrderHasBar = cartItems.some(item => ['beverages', 'desserts', 'drinks', 'dessert'].includes(item.cat.toLowerCase()));

    const payload = {
      restaurant_id: "1",
      table_id: String(selectedTableId),
      special_instructions: document.querySelector('.bottom-meta textarea')?.value || '',
      items: cartItems.map(item => ({
        menu_item_id: item.id,
        name: item.name,
        price: item.price,
        quantity: item.qty
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
        lastPlacedOrderId = data.data.id;
        showToast('Order placed successfully! ID: #' + lastPlacedOrderId, 'success');
        clearCart();
        
        // If GPay / UPI was active payment method, open QR modal for payment collection!
        const activePayMethod = document.querySelector('.pay-method.active')?.textContent || '';
        if (activePayMethod.toLowerCase().includes('upi') || activePayMethod.toLowerCase().includes('gpay')) {
          openUpiQrModal();
        } else {
          document.getElementById('printHubOrderId').textContent = 'Order ID: #' + lastPlacedOrderId;
          document.getElementById('printHubKitchenBtn').style.display = activeOrderHasKitchen ? 'flex' : 'none';
          document.getElementById('printHubBarBtn').style.display = activeOrderHasBar ? 'flex' : 'none';
          openModal('printHubModal');
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
    const activeCartItems = MENU.filter(i => i.qty > 0);
    const tableNumber = document.getElementById('selectedTableLabel')?.textContent || 'Walk-In';

    if (activeCartItems.length > 0) {
      const itemsPayload = activeCartItems.map(i => ({ name: i.name, qty: i.qty, price: i.price }));
      const itemsJson = encodeURIComponent(JSON.stringify(itemsPayload));
      window.open('/orders/live/invoice-a4?table=' + encodeURIComponent(tableNumber) + '&items=' + itemsJson, '_blank');
    } else if (lastPlacedOrderId) {
      window.open('/orders/' + lastPlacedOrderId + '/invoice-a4', '_blank');
    } else {
      showToast('Please add items to cart first to generate real-time A4 bill!', 'error');
    }
  }

  let currentWaMessage = "";

  function sendWhatsAppInvoice(customOrderData = null) {
    try {
      let tableNumber = document.getElementById('selectedTableLabel')?.textContent || 'Walk-In';
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

  function triggerPrintHubAction(type) {
    if (!lastPlacedOrderId) return;
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

  /* ─── SIDEBAR TOGGLE ─────────────────────── */
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');

    if (window.innerWidth <= 768) {
      const willOpen = !sidebar.classList.contains('mobile-open');
      if (willOpen) setMobileCart(false);
      setMobileSidebar(willOpen);
      return;
    }

    sidebar.classList.toggle('collapsed');
  }

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

  async function checkWaiterCalls() {
    try {
      const response = await fetch('/api/waiter-calls/restaurant/1');
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

  // Poll for waiter calls every 12 seconds to save server threads and DB resources
  setInterval(checkWaiterCalls, 12000);
  // Initial check on load
  checkWaiterCalls();
  loadTablesData(); // Populate allTablesData array immediately on page load!

  function updateLiveClock() {
    const clock = document.getElementById('liveClockBadge');
    const dateSub = document.getElementById('topbarDate');
    if (!clock) return;
    
    const now = new Date();
    
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    clock.textContent = `${String(hours).padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;
    
    const options = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
    const dateString = now.toLocaleDateString('en-US', options);
    
    let shift = 'General';
    const curHour = now.getHours();
    if (curHour >= 8 && curHour < 16) {
      shift = 'Morning (08:00 - 16:00)';
    } else if (curHour >= 16 && curHour < 24) {
      shift = 'Evening (16:00 - 24:00)';
    } else {
      shift = 'Night (00:00 - 08:00)';
    }
    
    if (dateSub) {
      dateSub.innerHTML = `${dateString} &nbsp;·&nbsp; Shift: ${shift}`;
    }
  }
  setInterval(updateLiveClock, 1000);
  updateLiveClock();

  // Sync POS menu items dynamically from Database API (Menu Manager)
  syncPosMenuWithDatabase();

  // Open modal automatically if query parameter is set
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('openModal') === 'orders') {
    openModal('ordersModal');
    // Clear URL query parameters from browser address bar to prevent opening again on refresh
    window.history.replaceState({}, document.title, window.location.pathname);
  }

  // ─── VOCAL SOUNDBOX ANNOUNCEMENTS ───

  async function checkNewPayments() {
    try {
      const response = await fetch('/api/orders/restaurant/1');
      const data = await response.json();
      if (data.success && Array.isArray(data.data)) {
        // Initialize announced list on first run to avoid repeating old orders
        if (announcedOrderIds.length === 0) {
          announcedOrderIds = data.data
            .filter(order => order.payment_status === 'paid')
            .map(order => String(order.id));
          localStorage.setItem('dineflow_announced_orders', JSON.stringify(announcedOrderIds));
          return;
        }

        data.data.forEach(order => {
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
    }
  }

  // Poll for payments every 6 seconds (fast and responsive!)
  setInterval(checkNewPayments, 6000);

  // Customer Modal Selection & Filter Logic
  let selectedCustomerData = { name: 'Rahul Kumar', phone: '+91 98765 43210', points: 420, avatar: 'RK' };

  function selectCustomerItem(el, name, phone, points, avatar) {
    document.querySelectorAll('#custModalList .cust-row').forEach(r => r.classList.remove('selected'));
    el.classList.add('selected');
    selectedCustomerData = { name, phone, points, avatar };
  }

  function confirmCustomerSelection() {
    if (!selectedCustomerData) return;
    const strip = document.querySelector('.customer-strip');
    if (strip) {
      const avatarEl = strip.querySelector('.cust-avatar');
      const nameEl = strip.querySelector('.cust-info h5');
      const subEl = strip.querySelector('.cust-info p');
      if (avatarEl) avatarEl.textContent = selectedCustomerData.avatar;
      if (nameEl) nameEl.textContent = selectedCustomerData.name;
      if (subEl) subEl.innerHTML = `${selectedCustomerData.phone} &nbsp;·&nbsp; <span style="color:var(--primary);">⭐ ${selectedCustomerData.points} pts</span>`;
    }
    showToast(`Selected Customer: ${selectedCustomerData.name}`, 'success');
    closeModal('customerModal');
  }

  function filterCustomerModalList() {
    const q = (document.getElementById('custSearchInput')?.value || '').toLowerCase();
    document.querySelectorAll('#custModalList .cust-row').forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(q) ? 'flex' : 'none';
    });
  }
</script>
</body>
</html>

