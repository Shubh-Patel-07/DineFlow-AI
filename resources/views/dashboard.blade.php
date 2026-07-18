<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — DineFlow AI POS</title>
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
      --sidebar-bg:     #0f172a;
      --sidebar-hover:  #1e293b;
      --sidebar-active: #6366F1;
      --bg:             #0b0f19;
      --card:           #1e293b;
      --border:         #334155;
      --text-primary:   #f8fafc;
      --text-secondary: #94a3b8;
      --text-muted:     #64748b;
      --shadow-sm:      0 4px 10px rgba(0,0,0,.3);
      --shadow-md:      0 8px 20px rgba(0,0,0,.4);
      --shadow-lg:      0 16px 36px rgba(0,0,0,.5);
      --radius-sm:      8px;
      --radius-md:      12px;
      --radius-lg:      20px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text-primary); display: flex; height: 100vh; overflow: hidden; }

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
      cursor: pointer; transition: background .15s; white-space: nowrap;
    }
    .user-card:hover { background: var(--sidebar-hover); }
    .user-avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 13px; flex-shrink: 0; }
    .user-info { transition: opacity .2s; }
    .user-info h4 { font-size: 13px; font-weight: 600; color: #fff; }
    .user-info p  { font-size: 11px; color: rgba(255,255,255,.4); }
    .sidebar.collapsed .user-info { opacity: 0; width: 0; overflow: hidden; }

    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

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
    <div class="brand-icon"><i class="fa-solid fa-bowl-food"></i></div>
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
      <a class="nav-item" href="#" data-tip="Table Manager"><i class="fa-solid fa-table-cells-large"></i><span class="nav-label"> Table Manager</span></a>
      <a class="nav-item" href="#" data-tip="Kitchen Display"><i class="fa-solid fa-fire-burner"></i><span class="nav-label"> Kitchen Display</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Menu</div>
      <a class="nav-item" href="#" data-tip="Menu Items"><i class="fa-solid fa-utensils"></i><span class="nav-label"> Menu Items</span></a>
      <a class="nav-item" href="#" data-tip="Categories"><i class="fa-solid fa-layer-group"></i><span class="nav-label"> Categories</span></a>
      <a class="nav-item" href="#" data-tip="Modifiers"><i class="fa-solid fa-puzzle-piece"></i><span class="nav-label"> Modifiers</span></a>
      <a class="nav-item" href="#" data-tip="Combos & Deals"><i class="fa-solid fa-tags"></i><span class="nav-label"> Combos &amp; Deals</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Operations</div>
      <a class="nav-item" href="#" data-tip="Delivery Orders"><i class="fa-solid fa-motorcycle"></i><span class="nav-label"> Delivery Orders</span></a>
      <a class="nav-item" href="#" data-tip="Customers"><i class="fa-solid fa-users"></i><span class="nav-label"> Customers</span></a>
      <a class="nav-item" href="#" data-tip="Loyalty Program"><i class="fa-solid fa-star"></i><span class="nav-label"> Loyalty Program</span></a>
      <a class="nav-item" href="#" data-tip="Coupons"><i class="fa-solid fa-ticket"></i><span class="nav-label"> Coupons</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Reports</div>
      <a class="nav-item" href="#" data-tip="Sales Reports"><i class="fa-solid fa-chart-line"></i><span class="nav-label"> Sales Reports</span></a>
      <a class="nav-item" href="#" data-tip="Inventory"><i class="fa-solid fa-box-open"></i><span class="nav-label"> Inventory</span></a>
      <a class="nav-item" href="#" data-tip="Expenses"><i class="fa-solid fa-file-invoice-dollar"></i><span class="nav-label"> Expenses</span></a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Settings</div>
      <a class="nav-item" href="#" data-tip="Settings"><i class="fa-solid fa-gear"></i><span class="nav-label"> Settings</span></a>
      <a class="nav-item" href="#" data-tip="Staff & Roles"><i class="fa-solid fa-user-shield"></i><span class="nav-label"> Staff &amp; Roles</span></a>
      <a class="nav-item" href="#" data-tip="Printers"><i class="fa-solid fa-print"></i><span class="nav-label"> Printers</span></a>
      <form action="{{ route('auth.logout') }}" method="POST" style="display: flex; width: 100%;">
        @csrf
        <button type="submit" class="nav-item" data-tip="Logout" style="border: none; background: none; cursor: pointer; width: 100%; justify-content: flex-start;">
          <i class="fa-solid fa-sign-out-alt"></i><span class="nav-label"> Logout</span>
        </button>
      </form>
    </div>
  </div>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}</div>
      <div class="user-info">
        <h4>{{ Auth::user()->name }}</h4>
        <p>Staff</p>
      </div>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div class="main">

  <!-- Topbar -->
  <div class="topbar">
    <div class="topbar-title-group">
      <div class="topbar-title">Dashboard</div>
      <div class="topbar-sub">{{ now()->format('l, d M Y') }} · System Overview</div>
    </div>
    <div class="topbar-btn topbar-mobile-menu" title="Menu" onclick="toggleSidebar()">
      <i class="fa-solid fa-bars"></i>
    </div>
    <div class="topbar-spacer"></div>
    <div class="topbar-btn" title="Search"><i class="fa-solid fa-magnifying-glass"></i></div>
    <div class="topbar-btn" title="Notifications">
      <i class="fa-solid fa-bell"></i>
      <span class="notif-dot"></span>
    </div>
    <div class="topbar-btn" title="Full Screen" onclick="toggleFull()"><i class="fa-solid fa-expand"></i></div>
    <div style="width:1px;height:26px;background:var(--border);"></div>
    <div style="display:flex;align-items:center;gap:8px;cursor:pointer;">
      <div class="user-avatar" style="width:32px;height:32px;font-size:12px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
      <span style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ Auth::user()->name }}</span>
      <i class="fa-solid fa-chevron-down" style="font-size:11px;color:var(--text-muted);"></i>
    </div>
  </div>

  <!-- Dashboard Content -->
  <div class="dashboard-content">
    <div class="dashboard-card">
      <div class="card-header">
        <div class="card-icon primary"><i class="fa-solid fa-receipt"></i></div>
        <div>
          <div class="card-title">Today's Orders</div>
          <div class="card-value">{{ $todayOrdersCount }}</div>
          <div class="card-sub">{{ $ordersDiffText }}</div>
        </div>
      </div>
    </div>

    <div class="dashboard-card">
      <div class="card-header">
        <div class="card-icon success"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
          <div class="card-title">Total Revenue</div>
          <div class="card-value">₹ {{ number_format($todayRevenue) }}</div>
          <div class="card-sub">{{ $revenueDiffText }}</div>
        </div>
      </div>
    </div>

    <div class="dashboard-card">
      <div class="card-header">
        <div class="card-icon warning"><i class="fa-solid fa-users"></i></div>
        <div>
          <div class="card-title">Active Customers</div>
          <div class="card-value">{{ $activeCustomers }}</div>
          <div class="card-sub">Currently dining</div>
        </div>
      </div>
    </div>

    <div class="dashboard-card">
      <div class="card-header">
        <div class="card-icon danger"><i class="fa-solid fa-table-cells"></i></div>
        <div>
          <div class="card-title">Tables Occupied</div>
          <div class="card-value">{{ $occupiedTablesCount }} / {{ $totalTablesCount }}</div>
          <div class="card-sub">Real-time status</div>
        </div>
      </div>
    </div>
  </div>
</div>

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

function toggleFull() {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen();
    document.body.style.height = '100vh';
    document.body.style.overflow = 'hidden';
  } else {
    document.exitFullscreen();
    document.body.style.height = 'auto';
    document.body.style.overflow = 'auto';
  }
}
</script>

</body>
</html>
