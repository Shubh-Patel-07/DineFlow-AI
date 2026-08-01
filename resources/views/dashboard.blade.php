<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — DineFlow AI POS</title>
  <script>window.AUTH_USER_ROLE = "{{ Auth::user()->role ?? Auth::user()->username ?? session('active_role') ?? '' }}";</script>
  <script src="/frontend/admin/utils.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <style>
    :root {
      --primary:        #6366F1;
      --primary-light:  rgba(99, 102, 241, 0.15);
      --primary-dark:   #4F46E5;
      --success:        #10B981;
      --success-light:  rgba(16, 185, 129, 0.15);
      --danger:         #EF4444;
      --danger-light:   rgba(239, 68, 68, 0.15);
      --warning:        #F59E0B;
      --warning-light:  rgba(245, 158, 11, 0.15);
      --info:           #06B6D4;
      --info-light:     rgba(6, 182, 212, 0.15);
      --sidebar-bg:     #0B0F19;
      --sidebar-hover:  #1E293B;
      --sidebar-active: #6366F1;
      --bg:             #0F172A;
      --card:           #1E293B;
      --border:         #334155;
      --text-primary:   #F8FAFC;
      --text-secondary: #94A3B8;
      --text-muted:     #64748B;
      --shadow-sm:      0 1px 3px rgba(0,0,0,.3);
      --shadow-md:      0 4px 6px rgba(0,0,0,.4);
      --shadow-lg:      0 10px 15px rgba(0,0,0,.5);
      --radius-sm:      8px;
      --radius-md:      12px;
      --radius-lg:      20px;
    }

    /* ─── 100% FULL LIGHT THEME ENGINE OVERRIDES FOR DASHBOARD ─── */
    body.light-theme {
      --sidebar-bg:     #FFFFFF;
      --sidebar-hover:  #F1F5F9;
      --sidebar-active: #6366F1;
      --bg:             #F8FAFC;
      --card:           #FFFFFF;
      --border:         #E2E8F0;
      --text-primary:   #0F172A;
      --text-secondary: #475569;
      --text-muted:     #64748B;
      --shadow-sm:      0 1px 3px rgba(0,0,0,.06);
      --shadow-md:      0 4px 6px rgba(0,0,0,.08);
      --shadow-lg:      0 10px 15px rgba(0,0,0,.1);
    }
    body.light-theme .sidebar { background: #FFFFFF !important; border-right: 1px solid #E2E8F0 !important; }
    body.light-theme .sidebar-brand { border-bottom-color: #E2E8F0 !important; }
    body.light-theme .brand-text h2 { color: #0F172A !important; }
    body.light-theme .brand-text span { color: #64748B !important; }
    body.light-theme .sidebar-toggle { background: #F1F5F9 !important; }
    body.light-theme .sidebar-toggle i { color: #475569 !important; }
    body.light-theme .sidebar-section-label { color: #94A3B8 !important; }
    body.light-theme .nav-item { color: #475569 !important; }
    body.light-theme .nav-item:hover { background: #F1F5F9 !important; color: #0F172A !important; }
    body.light-theme .nav-item.active { background: var(--primary) !important; color: #FFFFFF !important; }
    body.light-theme .sidebar-footer { border-top-color: #E2E8F0 !important; }
    body.light-theme .user-card:hover { background: #F1F5F9 !important; }
    body.light-theme .user-info h4 { color: #0F172A !important; }
    body.light-theme .user-info p { color: #64748B !important; }
    body.light-theme .topbar { background: #FFFFFF !important; border-bottom-color: #E2E8F0 !important; }
    body.light-theme .topbar-title { color: #0F172A !important; }
    body.light-theme .topbar-sub { color: #64748B !important; }

    /* Override inline hardcoded white colors & dark backgrounds for light mode */
    body.light-theme [style*="color: #F8FAFC"],
    body.light-theme [style*="color:#F8FAFC"] {
      color: #0F172A !important;
    }
    body.light-theme [style*="background: rgba(30,41,59"],
    body.light-theme [style*="background: linear-gradient(135deg, rgba(30,41,59"] {
      background: #FFFFFF !important;
      border-color: #E2E8F0 !important;
      box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
    }
    body.light-theme [style*="background: rgba(255,255,255,0.06)"] {
      background: #F1F5F9 !important;
      color: #475569 !important;
      border-color: #CBD5E1 !important;
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

    .sidebar.collapsed .nav-item:hover::after {
      content: attr(data-tip);
      position: absolute; left: 54px; top: 50%; transform: translateY(-50%);
      background: #1E293B; color: #fff; font-size: 12px; font-weight: 500;
      padding: 5px 10px; border-radius: 6px; white-space: nowrap;
      box-shadow: var(--shadow-md); pointer-events: none; z-index: 200;
    }

    .sidebar-footer {
      flex-shrink: 0; padding: 14px 8px;
      border-top: 1px solid rgba(255,255,255,.07); overflow: hidden;
    }
    .user-card {
      display: flex; align-items: center; gap: 10px;
      padding: 8px 10px; border-radius: var(--radius-sm);
      transition: background .15s; white-space: nowrap;
    }
    .user-card:hover { background: var(--sidebar-hover); }
    .user-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0; }
    .user-info { transition: opacity .2s; overflow: hidden; }
    .user-info h4 { font-size: 13px; font-weight: 600; color: #fff; }
    .user-info p  { font-size: 11px; color: rgba(255,255,255,.4); }
    .sidebar.collapsed .user-info { opacity: 0; width: 0; }
    .sidebar.collapsed #dashLogoutForm { margin-left: 0 !important; }

    .main { flex: 1; display: flex; flex-direction: column; overflow-y: auto; min-width: 0; }

    .topbar {
      background: var(--sidebar-bg); border-bottom: 1px solid var(--border);
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
    .notif-dot { position: absolute; top: 6px; right: 6px; width: 8px; height: 8px; background: var(--danger); border-radius: 50%; border: 2px solid var(--sidebar-bg); }

    .dashboard-content {
      flex: 1; overflow-y: auto; padding: 24px;
      display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;
      align-content: start;
    }

    .dashboard-card {
      background: var(--card); border-radius: var(--radius-lg); padding: 24px;
      box-shadow: var(--shadow-sm); border: 1px solid var(--border);
      display: flex; flex-direction: column; gap: 12px;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dashboard-card:hover {
      transform: translateY(-3px);
      border-color: var(--primary);
      box-shadow: 0 8px 24px rgba(99, 102, 241, 0.15);
    }

    .card-header { display: flex; align-items: center; gap: 12px; }
    .card-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 24px;
    }
    .card-icon.primary { background: var(--primary-light); color: var(--primary); }
    .card-icon.success { background: var(--success-light); color: var(--success); }
    .card-icon.warning { background: var(--warning-light); color: var(--warning); }
    .card-icon.danger  { background: var(--danger-light);  color: var(--danger); }

    .card-title { font-size: 14px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .5px; }
    .card-value { font-size: 32px; font-weight: 700; color: var(--text-primary); }
    .card-sub   { font-size: 12px; color: var(--text-muted); }

    @media (max-width: 768px) {
      .sidebar {
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
      .topbar {
        padding: 0 12px;
      }
      .topbar-title-group { display: flex !important; }
      .topbar-sub { display: none !important; }
      .topbar-mobile-menu { display: flex !important; }
      .topbar-desktop-only { display: none !important; }
      .main { margin-left: 0 !important; width: 100% !important; }
      .dashboard-content { padding: 14px !important; gap: 16px !important; }
      .metrics-grid { grid-template-columns: 1fr !important; gap: 12px !important; }
    }
    .topbar-mobile-menu { display: none; }

    /* ─── FULL MULTI-DEVICE RESPONSIVE ENGINE STYLES ─── */
    .sidebar-backdrop {
      display: none;
      position: fixed; inset: 0;
      background: rgba(15,23,42,0.75);
      backdrop-filter: blur(4px);
      z-index: 305;
    }
    .sidebar-backdrop.active { display: block; }

    @media (max-width: 1024px) {
      .metrics-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 16px !important; }
      .charts-grid { grid-template-columns: 1fr !important; gap: 20px !important; }
    }

    @media (max-width: 480px) {
      .card-value { font-size: 26px !important; }
    }
  </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
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
      <a class="nav-item active" href="/dashboard" data-tip="Dashboard"><i class="fa-solid fa-chart-pie"></i><span class="nav-label"> Dashboard</span></a>
      <a class="nav-item" href="/pos" data-tip="POS Terminal"><i class="fa-solid fa-cash-register"></i><span class="nav-label"> POS Terminal</span> <span class="nav-badge green">Live</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashOrdersModal()" data-tip="Orders"><i class="fa-solid fa-receipt"></i><span class="nav-label"> Orders</span> <span class="nav-badge">{{ $todayOrdersCount }}</span></a>
      <a class="nav-item" href="/admin/tables" data-tip="Table Manager"><i class="fa-solid fa-table-cells-large"></i><span class="nav-label"> Table Manager</span></a>
      <a class="nav-item" href="/kitchen" data-tip="Kitchen Display" target="_blank"><i class="fa-solid fa-fire-burner"></i><span class="nav-label"> Kitchen Display</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Menu</div>
      <a class="nav-item" href="/admin/menu-manager" data-tip="Menu Items"><i class="fa-solid fa-utensils"></i><span class="nav-label"> Menu Items</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashAdminModule('/admin/menu-manager', '📂 Categories Manager')" data-tip="Categories"><i class="fa-solid fa-layer-group"></i><span class="nav-label"> Categories</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashAdminModule('/admin/menu-manager', '🧩 Modifiers & Add-ons')" data-tip="Modifiers"><i class="fa-solid fa-puzzle-piece"></i><span class="nav-label"> Modifiers</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashAdminModule('/admin/menu-manager', '🏷️ Combos & Deals')" data-tip="Combos & Deals"><i class="fa-solid fa-tags"></i><span class="nav-label"> Combos &amp; Deals</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Operations</div>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashDeliveryModal()" data-tip="Delivery Orders"><i class="fa-solid fa-motorcycle"></i><span class="nav-label"> Delivery Orders</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashAdminModule('/admin/customers', '👥 Customer Directory')" data-tip="Customers"><i class="fa-solid fa-users"></i><span class="nav-label"> Customers</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashAdminModule('/admin/customers', '⭐ Loyalty Program')" data-tip="Loyalty Program"><i class="fa-solid fa-star"></i><span class="nav-label"> Loyalty Program</span></a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashOffersModal()" data-tip="Offers"><i class="fa-solid fa-gift"></i><span class="nav-label"> Offers</span> <span class="nav-badge red" style="background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 6px; border-radius:10px;">Deals</span></a>
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
      <a class="nav-item" href="javascript:void(0)" onclick="openDashAdminModule('/admin/settings', '🖨️ Printers & Print Hub')" data-tip="Printers"><i class="fa-solid fa-print"></i><span class="nav-label"> Printers</span></a>
      <a class="nav-item" href="/admin/feedback" data-tip="Reviews & Feedback"><i class="fa-solid fa-comment-dots"></i><span class="nav-label"> Feedback</span></a>
    </div>
  </div>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar" style="background:var(--primary);">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
      <div class="user-info">
        <h4>{{ Auth::user()->name }}</h4>
        <p style="text-transform:capitalize;">{{ Auth::user()->role ?? 'User' }}</p>
      </div>
      <form method="POST" action="{{ route('auth.logout') }}" id="dashLogoutForm" style="margin-left:auto;">
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

<!-- MAIN -->
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
      <div class="topbar-sub" id="topbarDate">{{ now()->format('l, M d, Y') }} • Shift: Evening (16:00 - 24:00)</div>
    </div>
    <div class="topbar-btn topbar-mobile-menu" title="Menu" onclick="toggleSidebar()">
      <i class="fa-solid fa-bars"></i>
    </div>
    <div class="topbar-spacer"></div>
    
    <!-- Light/Dark Mode Theme Switcher -->
    <div class="topbar-btn" id="posThemeToggleBtn" title="Toggle Light/Dark Theme" onclick="toggleGlobalSystemTheme()" style="color:var(--primary); border-color:rgba(99,102,241,0.3); background:var(--primary-light); width:auto; padding: 0 12px; border-radius: 20px; gap: 6px; margin-right: 4px; cursor:pointer;">
      <i id="posThemeIcon" class="fa-solid fa-sun"></i>
      <span id="posThemeText" style="font-size:11.5px; font-weight:700;">Light Mode</span>
    </div>

    <div class="topbar-btn topbar-desktop-only" title="Notifications" onclick="openModal('notifModal')" style="cursor:pointer;">
      <i class="fa-solid fa-bell"></i>
      <span class="notif-dot"></span>
    </div>
    <div class="topbar-btn topbar-desktop-only" title="Full Screen" onclick="toggleFull()" style="cursor:pointer;"><i class="fa-solid fa-expand"></i></div>
    <div class="topbar-desktop-only" style="width:1px;height:26px;background:var(--border);"></div>
    <div class="topbar-desktop-only" style="display:flex;align-items:center;gap:8px;">
      <div class="user-avatar" style="width:32px;height:32px;font-size:12px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
      <div style="display:flex;flex-direction:column;">
        <span style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ Auth::user()->name }}</span>
        <span style="font-size:10px;color:var(--text-muted);text-transform:capitalize;">{{ Auth::user()->role ?? 'staff' }}</span>
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
  </div>

  <!-- Dashboard Content -->
  <div class="dashboard-content" style="padding: 24px; display: flex; flex-direction: column; gap: 24px;">

    <!-- Welcome Hero Banner -->
    <div style="background: var(--card); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: 24px; padding: 24px 28px; box-shadow: var(--shadow-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <div style="display: flex; align-items: center; gap: 16px;">
        <img src="/images/dineflow_logo.jpg" alt="Logo" style="width: 52px; height: 52px; border-radius: 14px; box-shadow: 0 6px 20px rgba(99,102,241,0.4); border: 2px solid rgba(99,102,241,0.5);">
        <div>
          <h2 style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:var(--text-primary); margin:0 0 4px; display:flex; align-items:center; gap:8px;">
            Welcome back, {{ Auth::user()->name }}! <span style="font-size:18px;">👋</span>
          </h2>
          <p style="margin:0; color:var(--text-muted); font-size:13px; font-weight:500;">DineFlow AI POS Operations Center • Live Real-Time Analytics & Soundbox Enabled</p>
        </div>
      </div>
      <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="/pos" style="padding: 10px 18px; background: linear-gradient(135deg, #6366F1, #4F46E5); color: #fff; text-decoration: none; border-radius: 12px; font-size: 12.5px; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(99,102,241,0.4);"><i class="fa-solid fa-cash-register"></i> + New POS Bill</a>
        <button type="button" onclick="openDashOrdersModal('all')" style="padding: 10px 16px; background: var(--primary-light); border: 1px solid var(--border); color: var(--primary); border-radius: 12px; font-size: 12.5px; font-weight: 600; cursor:pointer; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-receipt"></i> Orders</button>
        <button type="button" onclick="openDashOffersModal()" style="padding: 10px 16px; background: var(--primary-light); border: 1px solid var(--border); color: var(--primary); border-radius: 12px; font-size: 12.5px; font-weight: 600; cursor:pointer; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-tags"></i> Offers</button>
        <a href="/admin/tables" style="padding: 10px 16px; background: var(--primary-light); border: 1px solid var(--border); color: var(--primary); text-decoration: none; border-radius: 12px; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-table-cells-large"></i> Table Floor</a>
        <a href="/admin/menu-manager" style="padding: 10px 16px; background: var(--primary-light); border: 1px solid var(--border); color: var(--primary); text-decoration: none; border-radius: 12px; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-utensils"></i> Menu Items</a>
        <a href="/z-report" style="padding: 10px 16px; background: var(--primary-light); border: 1px solid var(--border); color: var(--primary); text-decoration: none; border-radius: 12px; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-chart-line"></i> Z-Report</a>
      </div>
    </div>

    <!-- 4 Stats Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px;">
      <!-- Card 1 -->
      <div style="background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: var(--shadow-sm);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); color: #818CF8; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-receipt"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Today's Orders</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: var(--text-primary); margin: 2px 0;">{{ $todayOrdersCount }}</div>
            <div style="font-size: 11.5px; color: #34D399; font-weight: 600;">{{ $ordersDiffText }}</div>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div style="background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: var(--shadow-sm);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34D399; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-indian-rupee-sign"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Revenue</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: var(--text-primary); margin: 2px 0;">₹ {{ number_format($todayRevenue) }}</div>
            <div style="font-size: 11.5px; color: #34D399; font-weight: 600;">{{ $revenueDiffText }}</div>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div style="background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: var(--shadow-sm);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.3); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-users"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Active Customers</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: var(--text-primary); margin: 2px 0;">{{ $activeCustomers }}</div>
            <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 500;">Currently dining at tables</div>
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div style="background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: var(--shadow-sm);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #F87171; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-table-cells-large"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Tables Occupied</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: var(--text-primary); margin: 2px 0;">{{ $occupiedTablesCount }} / {{ $totalTablesCount }}</div>
            <div style="font-size: 11.5px; color: var(--text-muted); font-weight: 500;">Real-time floor occupancy</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
      <div style="background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); border-radius: 24px; padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-chart-line" style="color:#818CF8;"></i> Last 7 Days Revenue Trend
        </h3>
        <div style="height: 280px; position: relative;">
          <canvas id="salesTrendChart"></canvas>
        </div>
      </div>

      <div style="background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); border-radius: 24px; padding: 24px; box-shadow: var(--shadow-sm);">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; margin-bottom: 20px; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-chart-pie" style="color:#34D399;"></i> Sales Category Distribution
        </h3>
        <div style="height: 280px; position: relative;">
          <canvas id="categorySalesChart"></canvas>
        </div>
      </div>
    </div>

  </div>
</div>
</div>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  // Sales Trend Chart
  const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
  const trendGrad = ctxTrend.createLinearGradient(0, 0, 0, 280);
  trendGrad.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
  trendGrad.addColorStop(1, 'rgba(99, 102, 241, 0.01)');

  new Chart(ctxTrend, {
    type: 'line',
    data: {
      labels: {!! json_encode($weeklySalesLabels) !!},
      datasets: [{
        label: 'Revenue (₹)',
        data: {!! json_encode($weeklySalesData) !!},
        borderColor: '#6366F1',
        borderWidth: 3,
        backgroundColor: trendGrad,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#818CF8',
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: {
          grid: { color: 'rgba(255, 255, 255, 0.05)' },
          ticks: { color: '#9CA3AF', font: { family: 'Outfit', size: 11 } }
        },
        x: {
          grid: { display: false },
          ticks: { color: '#9CA3AF', font: { family: 'Outfit', size: 11 } }
        }
      }
    }
  });

  // Category Sales Chart
  const ctxCat = document.getElementById('categorySalesChart').getContext('2d');
  new Chart(ctxCat, {
    type: 'doughnut',
    data: {
      labels: {!! json_encode($categorySalesLabels) !!},
      datasets: [{
        data: {!! json_encode($categorySalesData) !!},
        backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#06B6D4', '#EC4899'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: { color: '#9CA3AF', font: { family: 'Outfit', size: 11 } }
        }
      },
      cutout: '70%'
    }
  });

  // Live Clock Ticker
  function updateLiveClock() {
    const now = new Date();
    const clockEl = document.getElementById('liveClockBadge');
    if (clockEl) {
      clockEl.innerText = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
  }
  updateLiveClock();
  setInterval(updateLiveClock, 1000);

});
</script>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  const isMobile = window.innerWidth <= 768;
  
  if (isMobile) {
    sidebar.classList.toggle('mobile-open');
    if (backdrop) backdrop.classList.toggle('active');
  } else {
    sidebar.classList.toggle('collapsed');
  }
}

let isPosVoiceMuted = localStorage.getItem('dineflow_voice_muted') === 'true';


function toggleFull() {
  const doc = document.documentElement;
  if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
    if (doc.requestFullscreen) {
      doc.requestFullscreen();
    } else if (doc.webkitRequestFullscreen) {
      doc.webkitRequestFullscreen();
    } else if (doc.msRequestFullscreen) {
      doc.msRequestFullscreen();
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    }
  }
}



function announcePaymentVoice(tableNumber, amount, method = 'online') {

  if (isPosVoiceMuted) return;

  if (!('speechSynthesis' in window)) {
    console.log('Text-to-speech not supported in this browser.');
    return;
  }
  
  window.speechSynthesis.cancel();
  
  let cleanTableNum = String(tableNumber).replace(/table/gi, '').replace(/#/g, '').replace(/T-/gi, '').trim();
  if (!cleanTableNum || cleanTableNum === 'Walk-In') cleanTableNum = '1';

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
  
  let guVoice = window.speechSynthesis.getVoices().find(v => v.lang.includes('gu') || v.name.includes('Gujarati') || v.lang.includes('hi') || v.lang.includes('IN'));
  if (guVoice) utterance.voice = guVoice;
  
  window.speechSynthesis.speak(utterance);
}



function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.style.display = 'flex';
    setTimeout(() => { modal.style.opacity = '1'; }, 10);
  }
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.style.opacity = '0';
    setTimeout(() => { modal.style.display = 'none'; }, 200);
  }
}

function showToast(msg, type = 'success') {
  const existing = document.getElementById('dineflow-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'dineflow-toast';
  const bgMap = { success: 'linear-gradient(135deg,#059669,#10B981)', error: 'linear-gradient(135deg,#DC2626,#EF4444)', info: 'linear-gradient(135deg,#2563EB,#3B82F6)', warning: 'linear-gradient(135deg,#D97706,#F59E0B)' };
  const bg = bgMap[type] || bgMap.success;
  toast.style.cssText = `position:fixed; top:20px; right:20px; z-index:99999; background:${bg}; color:#fff; padding:14px 22px; border-radius:14px; font-family:'Outfit',sans-serif; font-size:13.5px; font-weight:600; box-shadow:0 8px 32px rgba(0,0,0,0.35); display:flex; align-items:center; gap:10px; opacity:0; transform:translateY(-12px) scale(0.95); transition:all 0.35s cubic-bezier(0.4,0,0.2,1); max-width:420px;`;
  toast.textContent = msg;
  document.body.appendChild(toast);
  requestAnimationFrame(() => {
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0) scale(1)';
  });
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-12px) scale(0.95)';
    setTimeout(() => toast.remove(), 350);
  }, 3500);
}

function markAllNotificationsAsRead() {
  const body = document.querySelector('#notifModal .modal-body');
  if (body) {
    body.innerHTML = `
      <div style="text-align:center; padding:36px 20px; color:#64748B;">
        <i class="fa-solid fa-bell-slash" style="font-size:36px; margin-bottom:12px; color:#475569; display:block;"></i>
        <div style="font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">All Caught Up!</div>
        <div style="font-size:12px; color:#94A3B8;">All notifications have been marked as read.</div>
      </div>
    `;
  }

  document.querySelectorAll('.notif-dot').forEach(dot => {
    dot.style.display = 'none';
  });

  const badge = document.getElementById('notifCountBadge');
  if (badge) badge.style.display = 'none';

  dashNotifDismissedIds = [...dashNotifAllIds];
  localStorage.setItem('dineflow_dismissed_notifs', JSON.stringify(dashNotifDismissedIds));

  showToast('✅ All notifications cleared & marked as read!', 'success');

  setTimeout(() => {
    closeModal('notifModal');
  }, 400);
}

/* ─── LIVE NOTIFICATION POLLING SYSTEM ─── */
let dashNotifDismissedIds = [];
let dashNotifAllIds = [];

try {
  dashNotifDismissedIds = JSON.parse(localStorage.getItem('dineflow_dismissed_notifs') || '[]');
} catch(e) { dashNotifDismissedIds = []; }

function timeAgo(dateStr) {
  const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
  if (diff < 60) return 'Just now';
  if (diff < 3600) return Math.floor(diff / 60) + ' mins ago';
  if (diff < 86400) return Math.floor(diff / 3600) + ' hrs ago';
  return Math.floor(diff / 86400) + ' days ago';
}

function buildNotifCard(icon, iconBg, iconColor, borderColor, title, desc, timeStr, id) {
  return `
    <div class="live-notif-item" data-notif-id="${id}" style="display:flex; align-items:flex-start; gap:12px; background:${iconBg}; border:1px solid ${borderColor}; border-radius:12px; padding:12px; animation: notifSlideIn 0.3s ease;">
      <div style="width:34px; height:34px; border-radius:10px; background:${iconBg}; color:${iconColor}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
        <i class="fa-solid ${icon}"></i>
      </div>
      <div style="flex:1;">
        <div style="font-size:13px; font-weight:700; color:var(--text-primary);">${title}</div>
        <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">${desc}</div>
        <div style="font-size:10.5px; color:#64748B; margin-top:4px;"><i class="fa-regular fa-clock" style="margin-right:3px;"></i>${timeStr}</div>
      </div>
    </div>
  `;
}

async function fetchLiveNotifications() {
  const notifications = [];

  try {
    // 1. Waiter Calls (unresolved)
    const wcRes = await fetch('/api/waiter-calls/restaurant/1');
    const wcData = await wcRes.json();
    if (wcData.success && wcData.data) {
      wcData.data.forEach(call => {
        if (call.status !== 'resolved') {
          notifications.push({
            id: 'wc-' + call.id,
            icon: 'fa-bell-concierge',
            iconBg: 'rgba(239,68,68,0.1)',
            iconColor: '#F87171',
            borderColor: 'rgba(239,68,68,0.25)',
            title: '🔔 Waiter Called — Table ' + (call.table_id || '?'),
            desc: 'Customer is requesting waiter service',
            time: call.created_at,
            priority: 1
          });
        }
      });
    }
  } catch(e) { console.log('Waiter calls fetch skipped:', e.message); }

  try {
    // 2. Recent Orders (last 30 min, pending/preparing)
    const orRes = await fetch('/api/orders/restaurant/1');
    const orData = await orRes.json();
    if (orData.success && orData.data) {
      const now = Date.now();
      const thirtyMinsAgo = now - (30 * 60 * 1000);
      orData.data.forEach(order => {
        const orderTime = new Date(order.created_at).getTime();
        if (orderTime > thirtyMinsAgo) {
          if (order.status === 'pending') {
            notifications.push({
              id: 'order-new-' + order.id,
              icon: 'fa-receipt',
              iconBg: 'rgba(99,102,241,0.1)',
              iconColor: '#818CF8',
              borderColor: 'rgba(99,102,241,0.25)',
              title: '🆕 New Order #' + order.id + (order.table_id ? ' — Table ' + order.table_id : ''),
              desc: '₹' + Number(order.total_amount).toLocaleString('en-IN') + ' • Waiting for kitchen confirmation',
              time: order.created_at,
              priority: 2
            });
          }
          if (order.status === 'ready') {
            notifications.push({
              id: 'order-ready-' + order.id,
              icon: 'fa-utensils',
              iconBg: 'rgba(16,185,129,0.1)',
              iconColor: '#34D399',
              borderColor: 'rgba(16,185,129,0.25)',
              title: '✅ Order #' + order.id + ' Ready' + (order.table_id ? ' — Table ' + order.table_id : ''),
              desc: 'Kitchen has prepared this order. Ready to serve!',
              time: order.updated_at || order.created_at,
              priority: 2
            });
          }
          if (order.payment_status === 'paid' && order.status === 'completed') {
            notifications.push({
              id: 'order-paid-' + order.id,
              icon: 'fa-indian-rupee-sign',
              iconBg: 'rgba(16,185,129,0.1)',
              iconColor: '#34D399',
              borderColor: 'rgba(16,185,129,0.25)',
              title: '💰 Payment Received — Order #' + order.id,
              desc: '₹' + Number(order.total_amount).toLocaleString('en-IN') + ' via ' + (order.payment_method || 'Online'),
              time: order.updated_at || order.created_at,
              priority: 3
            });
          }
        }
      });
    }
  } catch(e) { console.log('Orders fetch skipped:', e.message); }

  try {
    // 3. Low Stock Alerts
    const invRes = await fetch('/api/inventory?restaurantId=1');
    const invData = await invRes.json();
    if (invData.success && invData.data) {
      invData.data.forEach(item => {
        if (item.quantity <= (item.low_stock_alert || 10)) {
          notifications.push({
            id: 'inv-' + item.id,
            icon: 'fa-triangle-exclamation',
            iconBg: 'rgba(245,158,11,0.1)',
            iconColor: '#FCD34D',
            borderColor: 'rgba(245,158,11,0.25)',
            title: '⚠️ Low Stock — ' + item.item_name,
            desc: 'Only ' + item.quantity + ' ' + (item.unit || 'units') + ' remaining (threshold: ' + (item.low_stock_alert || 10) + ')',
            time: item.updated_at || item.created_at,
            priority: 2
          });
        }
      });
    }
  } catch(e) { console.log('Inventory fetch skipped:', e.message); }

  // Sort by priority then time
  notifications.sort((a, b) => {
    if (a.priority !== b.priority) return a.priority - b.priority;
    return new Date(b.time) - new Date(a.time);
  });

  return notifications;
}

function renderNotifications(notifications) {
  const body = document.querySelector('#notifModal .modal-body');
  if (!body) return;

  dashNotifAllIds = notifications.map(n => n.id);
  const unreadNotifs = notifications.filter(n => !dashNotifDismissedIds.includes(n.id));

  // Update badge count
  const badge = document.getElementById('notifCountBadge');
  const dots = document.querySelectorAll('.notif-dot');

  if (unreadNotifs.length > 0) {
    dots.forEach(dot => { dot.style.display = 'block'; });
    if (badge) {
      badge.textContent = unreadNotifs.length > 9 ? '9+' : unreadNotifs.length;
      badge.style.display = 'flex';
    }
  } else {
    dots.forEach(dot => { dot.style.display = 'none'; });
    if (badge) badge.style.display = 'none';
  }

  if (notifications.length === 0) {
    body.innerHTML = `
      <div style="text-align:center; padding:36px 20px; color:#64748B;">
        <i class="fa-solid fa-bell-slash" style="font-size:36px; margin-bottom:12px; color:#475569; display:block;"></i>
        <div style="font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">All Quiet!</div>
        <div style="font-size:12px; color:#94A3B8;">No active alerts right now. Everything is running smoothly.</div>
      </div>
    `;
    return;
  }

  let html = '';
  notifications.forEach(n => {
    const opacity = dashNotifDismissedIds.includes(n.id) ? 'opacity:0.45;' : '';
    html += `
      <div class="live-notif-item" data-notif-id="${n.id}" style="display:flex; align-items:flex-start; gap:12px; background:${n.iconBg}; border:1px solid ${n.borderColor}; border-radius:12px; padding:12px; transition:all 0.2s; ${opacity}">
        <div style="width:34px; height:34px; border-radius:10px; background:${n.iconBg}; color:${n.iconColor}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
          <i class="fa-solid ${n.icon}"></i>
        </div>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:var(--text-primary);">${n.title}</div>
          <div style="font-size:11.5px; color:#94A3B8; margin-top:2px;">${n.desc}</div>
          <div style="font-size:10.5px; color:#64748B; margin-top:4px;"><i class="fa-regular fa-clock" style="margin-right:3px;"></i>${timeAgo(n.time)}</div>
        </div>
      </div>
    `;
  });
  body.innerHTML = html;
}

async function refreshDashNotifications() {
  try {
    const notifs = await fetchLiveNotifications();
    renderNotifications(notifs);
  } catch(e) {
    console.log('Notification refresh error:', e);
  }
}

// Initial fetch + poll every 15 seconds
document.addEventListener('DOMContentLoaded', () => {
  refreshDashNotifications();
  setInterval(refreshDashNotifications, 15000);
});

// Also refresh when bell icon is clicked
const origOpenModal = openModal;
openModal = function(id) {
  origOpenModal(id);
  if (id === 'notifModal') {
    refreshDashNotifications();
  }
};
</script>

<!-- Notifications Modal -->
<div class="modal-overlay" id="notifModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; z-index:10000; opacity:0; transition:all 0.2s;">
  <div class="modal" style="width:480px; background:var(--card); border:1px solid var(--border); border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,0.6); overflow:hidden;">
    <div class="modal-header" style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:#A5B4FC; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-bell" style="color:#818CF8;"></i>
        Live Notifications
        <span id="notifCountBadge" style="background:#EF4444; color:#fff; font-size:10px; font-weight:800; padding:2px 7px; border-radius:20px; display:none; align-items:center; justify-content:center; min-width:20px; animation: pulse 2s infinite;"></span>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <button onclick="refreshDashNotifications()" title="Refresh" style="background:rgba(99,102,241,0.15); border:1px solid rgba(99,102,241,0.3); color:#818CF8; width:30px; height:30px; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px; transition:all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.3)'" onmouseout="this.style.background='rgba(99,102,241,0.15)'"><i class="fa-solid fa-arrows-rotate"></i></button>
        <button class="modal-close" onclick="closeModal('notifModal')" style="background:none; border:none; color:#94A3B8; font-size:18px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <div class="modal-body" style="padding:16px 20px; max-height:400px; overflow-y:auto; display:flex; flex-direction:column; gap:10px;">
      <div style="text-align:center; padding:30px 20px; color:#64748B;">
        <i class="fa-solid fa-spinner fa-spin" style="font-size:28px; color:#818CF8; display:block; margin-bottom:10px;"></i>
        <div style="font-size:12px; color:#94A3B8;">Loading live notifications...</div>
      </div>
    </div>
    <div class="modal-footer" style="padding:14px 20px; background:var(--bg); border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <button class="btn-outline" onclick="markAllNotificationsAsRead()" style="padding:8px 14px; font-size:12px; font-weight:600; border-radius:8px; background:rgba(99,102,241,0.1); color:#818CF8; border:1px solid rgba(99,102,241,0.3); cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='rgba(99,102,241,0.25)'" onmouseout="this.style.background='rgba(99,102,241,0.1)'">Mark All as Read</button>
      <div style="display:flex; align-items:center; gap:4px; color:#475569; font-size:10.5px;"><i class="fa-solid fa-circle" style="font-size:6px; color:#10B981; animation:pulse 2s infinite;"></i> Auto-refresh every 15s</div>
      <button class="btn-outline" onclick="closeModal('notifModal')" style="padding:8px 14px; font-size:12px; font-weight:600; border-radius:8px; background:rgba(255,255,255,0.05); color:#94A3B8; border:1px solid rgba(255,255,255,0.1); cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.12)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">Close</button>
    </div>
  </div>
</div>

<style>
  @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:0.5; } }
  @keyframes notifSlideIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
  .live-notif-item:hover { transform:translateX(3px); filter:brightness(1.1); }
</style>

<!-- Dashboard Recent Orders Modal -->
<div class="modal-overlay" id="dashOrdersModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.75); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:10000; opacity:0; transition:all 0.25s;">
  <div class="modal" style="width:880px; max-width:94vw; max-height:85vh; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; display:flex; flex-direction:column;">
    <div class="modal-header" style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" id="dashOrdersModalTitle" style="color:var(--primary); font-size:17px; font-weight:800; font-family:'Outfit',sans-serif; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-receipt" style="color:var(--primary);"></i>
        <span>Recent Restaurant Orders</span>
        <span id="dashOrdersCountBadge" style="background:var(--primary-light); color:var(--primary); font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; border:1px solid var(--border);">Loading...</span>
      </div>
      <div style="display:flex; align-items:center; gap:10px;">
        <button onclick="loadDashOrdersModalData()" title="Refresh Orders" style="background:var(--primary-light); border:1px solid var(--border); color:var(--primary); width:34px; height:34px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px; transition:all 0.2s;"><i class="fa-solid fa-arrows-rotate"></i></button>
        <button class="modal-close" onclick="closeDashOrdersModal()" style="background:var(--primary-light); border:1px solid var(--border); color:var(--text-muted); width:34px; height:34px; border-radius:10px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.color='#F87171'" onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--text-muted)'"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <div class="modal-body" id="dashOrdersModalBody" style="padding:20px 24px; overflow-y:auto; flex:1;">
      <div style="text-align:center; padding:40px 20px; color:var(--text-muted);">
        <i class="fa-solid fa-spinner fa-spin" style="font-size:32px; color:var(--primary); display:block; margin-bottom:12px;"></i>
        <div style="font-size:13px; color:var(--text-muted);">Fetching recent orders...</div>
      </div>
    </div>
    <div class="modal-footer" style="padding:16px 24px; background:var(--bg); border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div style="color:var(--text-muted); font-size:12px;">💡 Click any action button to open Receipts or Tax Invoices</div>
      <button onclick="closeDashOrdersModal()" style="padding:9px 20px; font-size:13px; font-weight:700; border-radius:10px; background:linear-gradient(135deg,#6366F1,#4F46E5); color:#FFFFFF; border:none; cursor:pointer; box-shadow:0 4px 14px rgba(99,102,241,0.35); transition:all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">Close</button>
    </div>
  </div>
</div>

<script>
let currentDashOrderFilter = 'all';

function openDashOrdersModal(filterType = 'all') {
  currentDashOrderFilter = filterType;
  const modal = document.getElementById('dashOrdersModal');
  const titleEl = document.getElementById('dashOrdersModalTitle');
  
  if (titleEl) {
    if (filterType === 'delivery') {
      titleEl.innerHTML = `
        <i class="fa-solid fa-motorcycle" style="color:#38BDF8;"></i>
        <span>Delivery & Takeaway Orders</span>
        <span id="dashOrdersCountBadge" style="background:rgba(56,189,248,0.25); color:#38BDF8; font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; border:1px solid rgba(56,189,248,0.4);">Loading...</span>
      `;
    } else {
      titleEl.innerHTML = `
        <i class="fa-solid fa-receipt" style="color:var(--primary);"></i>
        <span>Recent Restaurant Orders</span>
        <span id="dashOrdersCountBadge" style="background:var(--primary-light); color:var(--primary); font-size:11px; font-weight:800; padding:3px 10px; border-radius:20px; border:1px solid var(--border);">Loading...</span>
      `;
    }
  }

  if (!modal) return;
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
  loadDashOrdersModalData();
}

function closeDashOrdersModal() {
  const modal = document.getElementById('dashOrdersModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

async function loadDashOrdersModalData() {
  const body = document.getElementById('dashOrdersModalBody');
  const badge = document.getElementById('dashOrdersCountBadge');
  if (!body) return;

  body.innerHTML = `
    <div style="text-align:center; padding:40px 20px; color:var(--text-muted);">
      <i class="fa-solid fa-spinner fa-spin" style="font-size:32px; color:var(--primary); display:block; margin-bottom:12px;"></i>
      <div style="font-size:13px; color:var(--text-muted);">Loading orders...</div>
    </div>
  `;

  try {
    const res = await fetch('/api/orders/restaurant/1');
    if (!res.ok) throw new Error('API request failed');
    const rawData = await res.json();
    
    // Support both direct array or Laravel object { success: true, data: [...] }
    let orders = Array.isArray(rawData) ? rawData : (rawData.data || []);
    
    if (currentDashOrderFilter === 'delivery') {
      orders = orders.filter(o => {
        const isDeliveryType = (o.order_type === 'delivery' || o.order_type === 'takeaway');
        const isNoTable = !o.table_id || o.table_id == '0';
        const tableStr = o.table ? (o.table.table_number || '').toLowerCase() : '';
        const isDeliveryTable = tableStr.includes('del') || tableStr.includes('take') || tableStr.includes('online');
        return isDeliveryType || isNoTable || isDeliveryTable;
      });
    }

    if (orders.length === 0) {
      if (badge) badge.innerText = '0 Orders';
      body.innerHTML = `
        <div style="text-align:center; padding:48px 20px; color:var(--text-muted);">
          <i class="fa-solid ${currentDashOrderFilter === 'delivery' ? 'fa-motorcycle' : 'fa-receipt'}" style="font-size:42px; margin-bottom:14px; color:var(--text-muted); display:block;"></i>
          <div style="font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">No ${currentDashOrderFilter === 'delivery' ? 'Delivery' : ''} Orders Found</div>
          <div style="font-size:12.5px; color:var(--text-muted); margin-bottom:16px;">There are no ${currentDashOrderFilter === 'delivery' ? 'delivery or takeaway' : 'recent'} orders recorded today.</div>
          ${currentDashOrderFilter === 'delivery' ? `<button onclick="openDashOrdersModal('all')" style="padding:8px 16px; font-size:12px; font-weight:700; border-radius:8px; background:var(--primary-light); color:var(--primary); border:1px solid var(--border); cursor:pointer;">View All Orders</button>` : ''}
        </div>
      `;
      return;
    }

    if (badge) badge.innerText = `${orders.length} ${currentDashOrderFilter === 'delivery' ? 'Delivery' : ''} Orders`;

    let html = `
      <table style="width:100%; border-collapse:collapse; font-size:13px;">
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
        <tbody>
    `;

    orders.forEach(o => {
      const isPaid = (o.payment_status === 'paid');
      const payBg = isPaid ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)';
      const payColor = isPaid ? '#34D399' : '#FBBF24';
      const payBorder = isPaid ? 'rgba(16,185,129,0.3)' : 'rgba(245,158,11,0.3)';
      const payText = isPaid ? 'Paid' : 'Pending';

      const statusColor = (o.status === 'completed') ? '#34D399' : ((o.status === 'cooking' || o.status === 'preparing') ? '#FBBF24' : '#818CF8');

      const tableName = o.table ? `Table ${o.table.table_number || o.table_id}` : (o.table_id && o.table_id != '0' ? `Table ${o.table_id}` : '🛵 Takeaway / Delivery');

      html += `
        <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;" onmouseover="this.style.background='var(--primary-light)'" onmouseout="this.style.background='transparent'">
          <td style="padding:12px; font-weight:700; color:var(--text-primary);">
            #${o.id}
            <div style="font-size:10.5px; color:var(--text-muted); font-weight:400; margin-top:2px;">${new Date(o.created_at || Date.now()).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}</div>
          </td>
          <td style="padding:12px; color:var(--text-secondary); font-weight:600;">${tableName}</td>
          <td style="padding:12px; font-weight:800; color:var(--text-primary);">₹${parseFloat(o.total_amount || 0).toFixed(2)}</td>
          <td style="padding:12px;">
            <span style="background:${payBg}; color:${payColor}; border:1px solid ${payBorder}; font-size:11px; font-weight:700; padding:3px 8px; border-radius:12px;">${payText}</span>
          </td>
          <td style="padding:12px; font-weight:700; color:${statusColor}; text-transform:capitalize;">${o.status || 'Received'}</td>
          <td style="padding:12px; text-align:right;">
            <div style="display:inline-flex; gap:6px;">
              <a href="/orders/${o.id}/receipt" target="_blank" style="background:var(--primary-light); border:1px solid var(--border); color:var(--primary); padding:5px 10px; border-radius:8px; text-decoration:none; font-size:11.5px; font-weight:600; transition:all 0.2s;" title="Print Thermal Receipt"><i class="fa-solid fa-print"></i> Receipt</a>
              <a href="/orders/${o.id}/invoice-a4" target="_blank" style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#34D399; padding:5px 10px; border-radius:8px; text-decoration:none; font-size:11.5px; font-weight:600; transition:all 0.2s;" title="View A4 PDF Invoice"><i class="fa-solid fa-file-invoice"></i> A4 PDF</a>
              <a href="/orders/${o.id}/receipt/kitchen" target="_blank" style="background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); color:#FBBF24; padding:5px 10px; border-radius:8px; text-decoration:none; font-size:11.5px; font-weight:600; transition:all 0.2s;" title="View Kitchen KOT"><i class="fa-solid fa-fire"></i> KOT</a>
            </div>
          </td>
        </tr>
      `;
    });

    html += `
        </tbody>
      </table>
    `;

    body.innerHTML = html;
  } catch(err) {
    console.error('Error loading dashboard orders modal:', err);
    body.innerHTML = `
      <div style="text-align:center; padding:36px 20px; color:#F87171;">
        <i class="fa-solid fa-circle-exclamation" style="font-size:32px; margin-bottom:10px; display:block;"></i>
        <div style="font-size:14px; font-weight:700;">Failed to load orders</div>
        <div style="font-size:12px; color:#94A3B8; margin-top:4px;">Please check server connection or refresh.</div>
      </div>
    `;
  }
}

// Handle ESC key to close modal
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeDashOrdersModal();
    closeDashAdminModule();
    closeDashOffersModal();
    closeDashDeliveryModal();
  }
});

// ═══════════════════════════════════════════════════════════════
// ADMIN MODULE IFRAME MODAL — Load any admin page inside Dashboard
// ═══════════════════════════════════════════════════════════════
function openDashAdminModule(url, titleHtml) {
  const modal = document.getElementById('dashAdminModuleModal');
  const titleEl = document.getElementById('dashAdminModuleTitle');
  const iframe = document.getElementById('dashAdminModuleIframe');
  if (!modal || !iframe) return;
  titleEl.innerHTML = titleHtml || 'Admin Module';
  iframe.src = url;
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
}
function closeDashAdminModule() {
  const modal = document.getElementById('dashAdminModuleModal');
  const iframe = document.getElementById('dashAdminModuleIframe');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; if (iframe) iframe.src = 'about:blank'; }, 250);
}

// ═══════════════════════════════════════════════════════════════
// OFFERS & DEALS MODAL — Same promos as POS posOffersModal
// ═══════════════════════════════════════════════════════════════
function openDashOffersModal() {
  const modal = document.getElementById('dashOffersModal');
  if (!modal) return;
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
}
function closeDashOffersModal() {
  const modal = document.getElementById('dashOffersModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 250);
}

// ═══════════════════════════════════════════════════════════════
// DELIVERY ORDERS MODAL — Shows delivery/takeaway orders from API
// ═══════════════════════════════════════════════════════════════
function openDashDeliveryModal() {
  const modal = document.getElementById('dashDeliveryModal');
  if (!modal) return;
  modal.style.display = 'flex';
  setTimeout(() => { modal.style.opacity = '1'; }, 10);
  loadDashDeliveryData();
}
function closeDashDeliveryModal() {
  const modal = document.getElementById('dashDeliveryModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 250);
}
async function loadDashDeliveryData() {
  const body = document.getElementById('dashDeliveryModalBody');
  if (!body) return;
  body.innerHTML = '<div style="text-align:center; padding:40px; color:var(--text-muted);"><i class="fa-solid fa-spinner fa-spin" style="font-size:28px; color:var(--primary); display:block; margin-bottom:12px;"></i>Fetching delivery orders...</div>';
  try {
    const resp = await fetch('/api/orders/restaurant/1');
    const rawData = await resp.json();
    const allOrders = Array.isArray(rawData) ? rawData : (rawData.data || []);
    const deliveryOrders = allOrders.filter(o => o.order_type === 'delivery' || o.order_type === 'takeaway');
    if (!deliveryOrders.length) {
      body.innerHTML = '<div style="text-align:center; padding:40px; color:var(--text-muted);"><i class="fa-solid fa-motorcycle" style="font-size:36px; display:block; margin-bottom:12px; opacity:0.4;"></i><div style="font-weight:700; font-size:14px;">No Active Delivery Orders</div><div style="font-size:12px; margin-top:4px;">Delivery and takeaway orders will appear here in real-time</div></div>';
      return;
    }
    let html = '<div style="display:flex; flex-direction:column; gap:12px;">';
    deliveryOrders.forEach(o => {
      const statusColor = o.status === 'completed' ? '#10B981' : o.status === 'preparing' ? '#F59E0B' : o.status === 'ready' ? '#06B6D4' : '#6366F1';
      const payBadge = o.payment_status === 'paid' ? '<span style="background:rgba(16,185,129,0.15); color:#10B981; padding:3px 10px; border-radius:8px; font-size:10px; font-weight:700;">PAID</span>' : '<span style="background:rgba(245,158,11,0.15); color:#F59E0B; padding:3px 10px; border-radius:8px; font-size:10px; font-weight:700;">UNPAID</span>';
      html += `
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
          <div style="display:flex; align-items:center; gap:12px;">
            <div style="width:42px; height:42px; border-radius:12px; background:${statusColor}20; display:flex; align-items:center; justify-content:center;"><i class="fa-solid ${o.order_type === 'delivery' ? 'fa-motorcycle' : 'fa-bag-shopping'}" style="color:${statusColor}; font-size:16px;"></i></div>
            <div>
              <div style="font-weight:700; font-size:14px; color:var(--text-primary);">#${o.id} — ${(o.order_type || 'delivery').toUpperCase()}</div>
              <div style="font-size:11px; color:var(--text-muted);">Table: ${o.table_number || 'N/A'} • ₹${parseFloat(o.total_amount || 0).toFixed(2)}</div>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:8px;">
            ${payBadge}
            <span style="background:${statusColor}20; color:${statusColor}; padding:3px 10px; border-radius:8px; font-size:10px; font-weight:700; text-transform:uppercase;">${o.status}</span>
          </div>
        </div>`;
    });
    html += '</div>';
    body.innerHTML = html;
  } catch(err) {
    body.innerHTML = '<div style="text-align:center; padding:36px; color:#F87171;"><i class="fa-solid fa-circle-exclamation" style="font-size:28px; display:block; margin-bottom:10px;"></i><div style="font-weight:700;">Failed to load delivery orders</div></div>';
  }
}
</script>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- ADMIN MODULE IFRAME MODAL — Loads admin pages inside Dashboard -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="dashAdminModuleModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.75); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:10000; opacity:0; transition:all 0.25s;">
  <div class="modal" style="width:1320px; max-width:96vw; height:90vh; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; display:flex; flex-direction:column;">
    <div class="modal-header" style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:14px 24px; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
      <div class="modal-title" id="dashAdminModuleTitle" style="color:var(--primary); font-size:16px; font-weight:800; font-family:'Outfit',sans-serif; display:flex; align-items:center; gap:10px;">Admin Module</div>
      <div style="display:flex; align-items:center; gap:8px;">
        <button onclick="const f=document.getElementById('dashAdminModuleIframe'); if(f) f.contentWindow.location.reload();" title="Reload" style="background:var(--primary-light); border:1px solid var(--border); color:var(--primary); width:32px; height:32px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:13px;"><i class="fa-solid fa-arrows-rotate"></i></button>
        <button onclick="closeDashAdminModule()" style="background:var(--primary-light); border:1px solid var(--border); color:var(--text-muted); width:32px; height:32px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:15px;" onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.color='#F87171'" onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--text-muted)'"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <iframe id="dashAdminModuleIframe" src="about:blank" style="flex:1; width:100%; border:none; background:var(--bg);"></iframe>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- OFFERS & DEALS MODAL — Identical to POS posOffersModal -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="dashOffersModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.75); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:10000; opacity:0; transition:all 0.25s;">
  <div class="modal" style="width:700px; max-width:94vw; max-height:85vh; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; display:flex; flex-direction:column;">
    <div class="modal-header" style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:var(--primary); font-size:17px; font-weight:800; font-family:'Outfit',sans-serif; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-gift" style="color:var(--primary);"></i>
        <span>Smart Offers & Discount Deals</span>
        <span style="background:linear-gradient(135deg,#EF4444,#DC2626); color:#fff; font-size:10px; font-weight:800; padding:3px 10px; border-radius:20px;">DEALS</span>
      </div>
      <button onclick="closeDashOffersModal()" style="background:var(--primary-light); border:1px solid var(--border); color:var(--text-muted); width:34px; height:34px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:16px;" onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.color='#F87171'" onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--text-muted)'"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="padding:20px 24px; overflow-y:auto; flex:1;">
      <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:14px;">
        <!-- Offer Cards -->
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; display:flex; flex-direction:column; gap:8px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(16,185,129,0.15); display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-percentage" style="color:#10B981; font-size:16px;"></i></div>
            <div>
              <div style="font-weight:800; font-size:14px; color:var(--text-primary);">WELCOME15</div>
              <div style="font-size:11px; color:var(--text-muted);">15% off for new customers</div>
            </div>
          </div>
          <div style="font-size:12px; color:var(--success); font-weight:700;">Flat 15% Discount • No Minimum</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; display:flex; flex-direction:column; gap:8px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(99,102,241,0.15); display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-indian-rupee-sign" style="color:#6366F1; font-size:16px;"></i></div>
            <div>
              <div style="font-weight:800; font-size:14px; color:var(--text-primary);">SAVE50</div>
              <div style="font-size:11px; color:var(--text-muted);">₹50 off on orders above ₹300</div>
            </div>
          </div>
          <div style="font-size:12px; color:var(--primary); font-weight:700;">₹50 Flat Off • Min ₹300</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; display:flex; flex-direction:column; gap:8px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-fire" style="color:#F59E0B; font-size:16px;"></i></div>
            <div>
              <div style="font-weight:800; font-size:14px; color:var(--text-primary);">SAVE120</div>
              <div style="font-size:11px; color:var(--text-muted);">₹120 off on orders above ₹600</div>
            </div>
          </div>
          <div style="font-size:12px; color:#F59E0B; font-weight:700;">₹120 Flat Off • Min ₹600</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; display:flex; flex-direction:column; gap:8px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(239,68,68,0.15); display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-crown" style="color:#EF4444; font-size:16px;"></i></div>
            <div>
              <div style="font-weight:800; font-size:14px; color:var(--text-primary);">VIP10</div>
              <div style="font-size:11px; color:var(--text-muted);">10% off for VIP loyalty members</div>
            </div>
          </div>
          <div style="font-size:12px; color:#EF4444; font-weight:700;">10% VIP Discount • Loyalty Only</div>
        </div>
        <div style="background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px; display:flex; flex-direction:column; gap:8px;">
          <div style="display:flex; align-items:center; gap:10px;">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(6,182,212,0.15); display:flex; align-items:center; justify-content:center;"><i class="fa-solid fa-clock" style="color:#06B6D4; font-size:16px;"></i></div>
            <div>
              <div style="font-weight:800; font-size:14px; color:var(--text-primary);">HAPPY10</div>
              <div style="font-size:11px; color:var(--text-muted);">10% off during Happy Hours</div>
            </div>
          </div>
          <div style="font-size:12px; color:#06B6D4; font-weight:700;">10% Happy Hour Discount</div>
        </div>
      </div>
      <!-- Custom Coupon Input -->
      <div style="margin-top:20px; background:var(--bg); border:1px solid var(--border); border-radius:16px; padding:16px;">
        <div style="font-weight:700; font-size:13px; color:var(--text-primary); margin-bottom:10px;"><i class="fa-solid fa-ticket" style="color:var(--primary); margin-right:6px;"></i> Enter Custom Coupon Code</div>
        <div style="display:flex; gap:10px;">
          <input type="text" placeholder="Enter promo code..." style="flex:1; padding:10px 14px; background:var(--card); border:1px solid var(--border); border-radius:10px; color:var(--text-primary); font-size:13px; font-weight:600; outline:none;">
          <button style="padding:10px 20px; background:linear-gradient(135deg,#6366F1,#4F46E5); border:none; border-radius:10px; color:#fff; font-weight:700; font-size:13px; cursor:pointer;">Apply</button>
        </div>
      </div>
    </div>
    <div class="modal-footer" style="padding:16px 24px; background:var(--bg); border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div style="color:var(--text-muted); font-size:12px;">🎁 Apply offers from the POS Terminal during billing</div>
      <button onclick="closeDashOffersModal()" style="padding:9px 20px; font-size:13px; font-weight:700; border-radius:10px; background:linear-gradient(135deg,#6366F1,#4F46E5); color:#FFFFFF; border:none; cursor:pointer;">Close</button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- DELIVERY ORDERS MODAL — Shows delivery/takeaway orders -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="dashDeliveryModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.75); backdrop-filter:blur(8px); display:none; align-items:center; justify-content:center; z-index:10000; opacity:0; transition:all 0.25s;">
  <div class="modal" style="width:780px; max-width:94vw; max-height:85vh; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; display:flex; flex-direction:column;">
    <div class="modal-header" style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:18px 24px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:var(--primary); font-size:17px; font-weight:800; font-family:'Outfit',sans-serif; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-motorcycle" style="color:var(--primary);"></i>
        <span>Delivery & Takeaway Dispatch Terminal</span>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <button onclick="loadDashDeliveryData()" title="Refresh" style="background:var(--primary-light); border:1px solid var(--border); color:var(--primary); width:34px; height:34px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:14px;"><i class="fa-solid fa-arrows-rotate"></i></button>
        <button onclick="closeDashDeliveryModal()" style="background:var(--primary-light); border:1px solid var(--border); color:var(--text-muted); width:34px; height:34px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:16px;" onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.color='#F87171'" onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--text-muted)'"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>
    <div class="modal-body" id="dashDeliveryModalBody" style="padding:20px 24px; overflow-y:auto; flex:1;">
      <div style="text-align:center; padding:40px; color:var(--text-muted);">
        <i class="fa-solid fa-spinner fa-spin" style="font-size:28px; color:var(--primary); display:block; margin-bottom:12px;"></i>Fetching delivery orders...
      </div>
    </div>
    <div class="modal-footer" style="padding:16px 24px; background:var(--bg); border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <div style="display:flex; gap:14px; align-items:center;">
        <span style="display:flex; align-items:center; gap:4px; font-size:11px; color:var(--text-muted);"><span style="width:8px; height:8px; border-radius:50%; background:#10B981;"></span> Completed</span>
        <span style="display:flex; align-items:center; gap:4px; font-size:11px; color:var(--text-muted);"><span style="width:8px; height:8px; border-radius:50%; background:#F59E0B;"></span> Preparing</span>
        <span style="display:flex; align-items:center; gap:4px; font-size:11px; color:var(--text-muted);"><span style="width:8px; height:8px; border-radius:50%; background:#06B6D4;"></span> Ready</span>
      </div>
      <button onclick="closeDashDeliveryModal()" style="padding:9px 20px; font-size:13px; font-weight:700; border-radius:10px; background:linear-gradient(135deg,#6366F1,#4F46E5); color:#FFFFFF; border:none; cursor:pointer;">Close</button>
    </div>
  </div>
</div>

</body>
</html>
