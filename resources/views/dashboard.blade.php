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
      .topbar-title-group { display: none; }
      .topbar-mobile-menu { display: flex !important; }
    }
    .topbar-mobile-menu { display: none; }
  </style>
</head>
<body>

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
      <a class="nav-item" href="/pos?openModal=orders" data-tip="Orders"><i class="fa-solid fa-receipt"></i><span class="nav-label"> Orders</span> <span class="nav-badge">{{ $todayOrdersCount }}</span></a>
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
      <a class="nav-item" href="/pos?openModal=orders" data-tip="Delivery Orders"><i class="fa-solid fa-motorcycle"></i><span class="nav-label"> Delivery Orders</span></a>
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
      <a class="nav-item" href="/admin/settings" data-tip="Printers"><i class="fa-solid fa-print"></i><span class="nav-label"> Printers</span></a>
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
    
    <!-- Gujarati Soundbox Voice Mute Toggle -->
    <div class="topbar-btn topbar-desktop-only" id="posVoiceMuteBtn" onclick="togglePosVoiceMute()" title="Gujarati Soundbox Toggle" style="color:#10B981; border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.08); width:auto; padding: 0 10px; border-radius: 20px; gap: 6px; margin-right: 4px; cursor:pointer;">
      <i id="posMuteIcon" class="fa-solid fa-volume-high"></i>
      <span id="posMuteText" style="font-size:11px; font-weight:700;">ગુજરાતી સાઉન્ડબોક્સ: ઓન 🔊</span>
    </div>

    <!-- Test Voice Sound Button -->
    <div class="topbar-btn topbar-desktop-only" title="Test Soundbox Voice" onclick="announcePaymentVoice('Test', 99, 'test mode')" style="color:#6366F1; border-color:rgba(99,102,241,0.3); background:rgba(99,102,241,0.08); width:auto; padding: 0 10px; border-radius: 20px; gap: 6px; margin-right: 8px; cursor:pointer;">
      <i class="fa-solid fa-comment-dots"></i>&nbsp;<span style="font-size:11px;font-weight:700;">ટેસ્ટ સાઉન્ડ</span>
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
    <div style="background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.95)); backdrop-filter: blur(20px); border: 1px solid rgba(99,102,241,0.3); border-radius: 24px; padding: 24px 28px; box-shadow: 0 20px 45px -10px rgba(0,0,0,0.6); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
      <div style="display: flex; align-items: center; gap: 16px;">
        <img src="/images/dineflow_logo.jpg" alt="Logo" style="width: 52px; height: 52px; border-radius: 14px; box-shadow: 0 6px 20px rgba(99,102,241,0.4); border: 2px solid rgba(99,102,241,0.5);">
        <div>
          <h2 style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#F8FAFC; margin:0 0 4px; display:flex; align-items:center; gap:8px;">
            Welcome back, {{ Auth::user()->name }}! <span style="font-size:18px;">👋</span>
          </h2>
          <p style="margin:0; color:#94A3B8; font-size:13px; font-weight:500;">DineFlow AI POS Operations Center • Live Real-Time Analytics & Soundbox Enabled</p>
        </div>
      </div>
      <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="/pos" style="padding: 11px 20px; background: linear-gradient(135deg, #6366F1, #4F46E5); color: #fff; text-decoration: none; border-radius: 12px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(99,102,241,0.4);"><i class="fa-solid fa-cash-register"></i> + New POS Bill</a>
        <a href="/admin/tables" style="padding: 11px 18px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #F8FAFC; text-decoration: none; border-radius: 12px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-table-cells-large"></i> Table Floor</a>
        <a href="/admin/menu-manager" style="padding: 11px 18px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #F8FAFC; text-decoration: none; border-radius: 12px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-utensils"></i> Menu Items</a>
        <a href="/z-report" style="padding: 11px 18px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #F8FAFC; text-decoration: none; border-radius: 12px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-chart-line"></i> Z-Report</a>
      </div>
    </div>

    <!-- 4 Stats Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 20px;">
      <!-- Card 1 -->
      <div style="background: rgba(30,41,59,0.75); backdrop-filter: blur(16px); border: 1px solid rgba(99,102,241,0.25); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: 0 10px 25px rgba(0,0,0,0.3);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); color: #818CF8; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-receipt"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px;">Today's Orders</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: #F8FAFC; margin: 2px 0;">{{ $todayOrdersCount }}</div>
            <div style="font-size: 11.5px; color: #34D399; font-weight: 600;">{{ $ordersDiffText }}</div>
          </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div style="background: rgba(30,41,59,0.75); backdrop-filter: blur(16px); border: 1px solid rgba(16,185,129,0.25); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: 0 10px 25px rgba(0,0,0,0.3);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #34D399; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-indian-rupee-sign"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px;">Total Revenue</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: #F8FAFC; margin: 2px 0;">₹ {{ number_format($todayRevenue) }}</div>
            <div style="font-size: 11.5px; color: #34D399; font-weight: 600;">{{ $revenueDiffText }}</div>
          </div>
        </div>
      </div>

      <!-- Card 3 -->
      <div style="background: rgba(30,41,59,0.75); backdrop-filter: blur(16px); border: 1px solid rgba(245,158,11,0.25); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: 0 10px 25px rgba(0,0,0,0.3);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.3); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-users"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px;">Active Customers</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: #F8FAFC; margin: 2px 0;">{{ $activeCustomers }}</div>
            <div style="font-size: 11.5px; color: #94A3B8; font-weight: 500;">Currently dining at tables</div>
          </div>
        </div>
      </div>

      <!-- Card 4 -->
      <div style="background: rgba(30,41,59,0.75); backdrop-filter: blur(16px); border: 1px solid rgba(239,68,68,0.25); border-radius: 20px; padding: 22px; transition: transform 0.25s, box-shadow 0.25s; box-shadow: 0 10px 25px rgba(0,0,0,0.3);" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'">
        <div style="display: flex; align-items: center; gap: 14px;">
          <div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #F87171; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;"><i class="fa-solid fa-table-cells-large"></i></div>
          <div>
            <div style="font-size: 12px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5px;">Tables Occupied</div>
            <div style="font-family:'Outfit',sans-serif; font-size: 26px; font-weight: 800; color: #F8FAFC; margin: 2px 0;">{{ $occupiedTablesCount }} / {{ $totalTablesCount }}</div>
            <div style="font-size: 11.5px; color: #64748B; font-weight: 500;">Real-time floor occupancy</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
      <div style="background: rgba(30,41,59,0.75); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; margin-bottom: 20px; color: #F8FAFC; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-chart-line" style="color:#818CF8;"></i> Last 7 Days Revenue Trend
        </h3>
        <div style="height: 280px; position: relative;">
          <canvas id="salesTrendChart"></canvas>
        </div>
      </div>

      <div style="background: rgba(30,41,59,0.75); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.4);">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; margin-bottom: 20px; color: #F8FAFC; display: flex; align-items: center; gap: 8px;">
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
  const isMobile = window.innerWidth <= 768;
  
  if (isMobile) {
    sidebar.classList.toggle('mobile-open');
  } else {
    sidebar.classList.toggle('collapsed');
  }
}

let isPosVoiceMuted = localStorage.getItem('dineflow_voice_muted') === 'true';

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

function playTestChimeSound() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.type = 'sine';
    osc.frequency.setValueAtTime(587.33, ctx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
    osc.connect(gain);
    gain.connect(ctx.destination);
    osc.start();
    osc.stop(ctx.currentTime + 0.35);
  } catch (e) {
    console.log('AudioContext error:', e);
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
  playTestChimeSound();

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

document.addEventListener('DOMContentLoaded', () => {
  updatePosMuteUI();
});

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
        <div style="font-size:14px; font-weight:700; color:#F8FAFC; margin-bottom:4px;">All Caught Up!</div>
        <div style="font-size:12px; color:#94A3B8;">All notifications have been marked as read.</div>
      </div>
    `;
  }

  document.querySelectorAll('.notif-dot').forEach(dot => {
    dot.style.display = 'none';
  });

  showToast('✅ All notifications cleared & marked as read!', 'success');

  setTimeout(() => {
    closeModal('notifModal');
  }, 400);
}
</script>

<!-- Notifications Modal -->
<div class="modal-overlay" id="notifModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.6); backdrop-filter:blur(4px); display:none; align-items:center; justify-content:center; z-index:10000; opacity:0; transition:all 0.2s;">
  <div class="modal" style="width:450px; background:#0F172A; border:1px solid rgba(255,255,255,0.1); border-radius:20px; box-shadow:0 25px 50px rgba(0,0,0,0.6); overflow:hidden;">
    <div class="modal-header" style="background:linear-gradient(135deg,#1E1B4B,#312E81); border-bottom:1px solid rgba(255,255,255,0.1); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
      <div class="modal-title" style="color:#A5B4FC; font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-bell" style="color:#818CF8;"></i>
        Notifications &amp; Live Alerts
      </div>
      <button class="modal-close" onclick="closeModal('notifModal')" style="background:none; border:none; color:#94A3B8; font-size:18px; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
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
      <button class="btn-outline" onclick="markAllNotificationsAsRead()" style="padding:8px 14px; font-size:12px; font-weight:600; border-radius:8px; background:rgba(99,102,241,0.1); color:#818CF8; border:1px solid rgba(99,102,241,0.3); cursor:pointer;">Mark All as Read</button>
      <button class="btn-outline" onclick="closeModal('notifModal')" style="padding:8px 14px; font-size:12px; font-weight:600; border-radius:8px; background:rgba(255,255,255,0.05); color:#94A3B8; border:1px solid rgba(255,255,255,0.1); cursor:pointer;">Close</button>
    </div>
  </div>
</div>

</body>
</html>
