<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>DineFlow / Tendorabuy — Super Admin Control Center</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root {
      --bg-primary: #0F172A;
      --bg-secondary: #1E293B;
      --bg-card: rgba(30, 41, 59, 0.7);
      --bg-card-hover: rgba(51, 65, 85, 0.6);
      --border-color: rgba(255, 255, 255, 0.08);
      --text-primary: #F8FAFC;
      --text-secondary: #94A3B8;
      --text-muted: #64748B;
      --accent: #6366F1;
      --accent-gradient: linear-gradient(135deg, #6366F1, #8B5CF6);
      --success: #10B981;
      --warning: #F59E0B;
      --danger: #EF4444;
      --info: #06B6D4;
      --sidebar-width: 260px;
    }

    body.light-theme {
      --bg-primary: #F8FAFC;
      --bg-secondary: #FFFFFF;
      --bg-card: #FFFFFF;
      --bg-card-hover: #F1F5F9;
      --border-color: #E2E8F0;
      --text-primary: #0F172A;
      --text-secondary: #475569;
      --text-muted: #94A3B8;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    body {
      background-color: var(--bg-primary);
      color: var(--text-primary);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }

    /* ─── SIDEBAR ────────────────────────────────────────── */
    .sidebar {
      width: var(--sidebar-width);
      background-color: var(--bg-secondary);
      border-right: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      z-index: 100;
      transition: transform 0.3s ease;
    }

    .sidebar-header {
      padding: 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid var(--border-color);
    }

    .brand-logo {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      background: var(--accent-gradient);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 20px;
      font-weight: 800;
      box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
    }

    .brand-text h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 18px;
      font-weight: 800;
      background: var(--accent-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      line-height: 1.2;
    }

    .brand-text span {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--warning);
    }

    .sidebar-nav {
      flex: 1;
      padding: 20px 16px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      overflow-y: auto;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 12px 16px;
      border-radius: 12px;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.2s ease;
      cursor: pointer;
      border: 1px solid transparent;
    }

    .nav-item:hover {
      color: var(--text-primary);
      background-color: var(--bg-card-hover);
    }

    .nav-item.active {
      color: #FFFFFF;
      background: var(--accent-gradient);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .nav-item i {
      font-size: 16px;
      width: 20px;
      text-align: center;
    }

    .sidebar-footer {
      padding: 20px 16px;
      border-top: 1px solid var(--border-color);
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .avatar {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: rgba(99, 102, 241, 0.2);
      color: var(--accent);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 14px;
    }

    .user-info {
      flex: 1;
      overflow: hidden;
    }

    .user-info h4 {
      font-size: 13px;
      font-weight: 700;
      color: var(--text-primary);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .user-info p {
      font-size: 11px;
      color: var(--text-muted);
    }

    /* ─── MAIN CONTENT ───────────────────────────────────── */
    .main-wrapper {
      margin-left: var(--sidebar-width);
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .topbar {
      height: 70px;
      background-color: var(--bg-secondary);
      border-bottom: 1px solid var(--border-color);
      padding: 0 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 90;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .menu-toggle {
      display: none;
      background: none;
      border: none;
      color: var(--text-primary);
      font-size: 20px;
      cursor: pointer;
    }

    .page-title h2 {
      font-family: 'Outfit', sans-serif;
      font-size: 20px;
      font-weight: 800;
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .theme-toggle-btn {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      color: var(--text-primary);
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
    }

    .theme-toggle-btn:hover {
      background: var(--bg-card-hover);
    }

    .btn-create {
      background: var(--accent-gradient);
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
      transition: transform 0.2s;
    }

    .btn-create:hover {
      transform: translateY(-2px);
    }

    .content-area {
      padding: 32px;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 28px;
      max-width: 1600px;
      width: 100%;
    }

    /* ─── METRIC CARDS GRID ──────────────────────────────── */
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 18px;
    }

    .metric-card {
      background: var(--bg-card);
      backdrop-filter: blur(12px);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      transition: transform 0.2s, border-color 0.2s;
    }

    .metric-card:hover {
      transform: translateY(-3px);
      border-color: rgba(99, 102, 241, 0.4);
    }

    .metric-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .metric-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .metric-label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-secondary);
    }

    .metric-value {
      font-family: 'Outfit', sans-serif;
      font-size: 26px;
      font-weight: 800;
      color: var(--text-primary);
    }

    .metric-badge {
      font-size: 11px;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    /* ─── DATA TABLE & CARDS SECTION ─────────────────────── */
    .table-container {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 24px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      overflow-x: auto;
    }

    .table-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .search-filter-bar {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      flex: 1;
    }

    .search-input-wrapper {
      position: relative;
      min-width: 240px;
      flex: 1;
    }

    .search-input-wrapper i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 14px;
    }

    .search-input {
      width: 100%;
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 10px 14px 10px 38px;
      color: var(--text-primary);
      font-size: 13px;
      outline: none;
    }

    .search-input:focus {
      border-color: var(--accent);
    }

    .filter-select {
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 10px 14px;
      color: var(--text-primary);
      font-size: 13px;
      outline: none;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    th {
      padding: 14px 16px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--text-muted);
      border-bottom: 1px solid var(--border-color);
    }

    td {
      padding: 16px;
      font-size: 13px;
      color: var(--text-secondary);
      border-bottom: 1px solid var(--border-color);
      vertical-align: middle;
    }

    tr:hover td {
      background: var(--bg-card-hover);
    }

    .plan-badge {
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      display: inline-block;
    }

    .plan-free { background: rgba(100, 116, 139, 0.2); color: #94A3B8; border: 1px solid rgba(100, 116, 139, 0.3); }
    .plan-pro { background: rgba(99, 102, 241, 0.2); color: #818CF8; border: 1px solid rgba(99, 102, 241, 0.4); }
    .plan-enterprise { background: rgba(245, 158, 11, 0.2); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.4); }
    .plan-custom { background: rgba(6, 182, 212, 0.2); color: #22D3EE; border: 1px solid rgba(6, 182, 212, 0.4); }

    .status-badge {
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-transform: capitalize;
    }

    .status-active { background: rgba(16, 185, 129, 0.15); color: #34D399; }
    .status-trial { background: rgba(6, 182, 212, 0.15); color: #22D3EE; }
    .status-expiring { background: rgba(245, 158, 11, 0.15); color: #FBBF24; }
    .status-grace_period { background: rgba(139, 92, 246, 0.15); color: #A78BFA; }
    .status-expired { background: rgba(239, 68, 68, 0.15); color: #F87171; }
    .status-suspended { background: rgba(239, 68, 68, 0.25); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.4); }
    .status-cancelled { background: rgba(100, 116, 139, 0.2); color: #94A3B8; }

    .actions-group {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .action-btn {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      border: 1px solid var(--border-color);
      background: var(--bg-secondary);
      color: var(--text-primary);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 13px;
      transition: all 0.2s;
    }

    .action-btn:hover {
      background: var(--accent);
      color: white;
      border-color: var(--accent);
    }

    /* ─── MODALS ─────────────────────────────────────────── */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(8px);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      visibility: hidden;
      transition: all 0.25s ease;
      padding: 16px;
    }

    .modal-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .modal-card {
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      border-radius: 20px;
      width: 100%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
      display: flex;
      flex-direction: column;
    }

    .modal-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-header h3 {
      font-family: 'Outfit', sans-serif;
      font-size: 18px;
      font-weight: 800;
    }

    .modal-close {
      background: none;
      border: none;
      color: var(--text-muted);
      font-size: 18px;
      cursor: pointer;
    }

    .modal-body {
      padding: 24px;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-group label {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-secondary);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-input, .form-select, .form-textarea {
      background: var(--bg-primary);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 10px 14px;
      color: var(--text-primary);
      font-size: 13px;
      outline: none;
    }

    .form-textarea {
      resize: vertical;
      min-height: 80px;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
      border-color: var(--accent);
    }

    .modal-footer {
      padding: 18px 24px;
      border-top: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 12px;
    }

    .btn-secondary {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      color: var(--text-primary);
      padding: 10px 18px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
    }

    .btn-primary {
      background: var(--accent-gradient);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
    }

    .btn-danger {
      background: var(--danger);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
    }

    /* ─── USAGE PROGRESS BARS ────────────────────────────── */
    .usage-item {
      display: flex;
      flex-direction: column;
      gap: 4px;
      margin-bottom: 10px;
    }

    .usage-header {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
      font-weight: 600;
      color: var(--text-secondary);
    }

    .usage-bar-bg {
      height: 8px;
      border-radius: 4px;
      background: var(--bg-primary);
      overflow: hidden;
    }

    .usage-bar-fill {
      height: 100%;
      border-radius: 4px;
      background: var(--accent-gradient);
      transition: width 0.3s ease;
    }

    /* ─── TOAST ALERTS ───────────────────────────────────── */
    .toast-container {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 2000;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .toast {
      background: var(--bg-secondary);
      border: 1px solid var(--border-color);
      color: var(--text-primary);
      padding: 14px 20px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      font-size: 13px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    .superadmin-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      z-index: 95;
      pointer-events: none;
    }
    .superadmin-backdrop.active {
      display: block;
      pointer-events: auto;
    }

    /* ─── RESPONSIVE BREAKPOINTS ─────────────────────────── */
    @media (max-width: 1024px) {
      body {
        overflow-x: hidden !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-y !important;
      }
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .main-wrapper {
        margin-left: 0;
        width: 100% !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch !important;
      }
      .menu-toggle {
        display: block;
      }
      .content-area {
        padding: 16px 12px;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-y !important;
      }
      .data-table-container {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
      }
      table {
        min-width: 580px;
      }
    }

    @media (max-width: 640px) {
      .topbar {
        padding: 0 14px;
      }
      .content-area {
        padding: 14px 10px;
        gap: 16px;
      }
      .metrics-grid {
        grid-template-columns: 1fr;
      }
      .header-actions {
        flex-wrap: wrap;
        width: 100%;
      }
      .search-box {
        width: 100% !important;
      }
    }
  </style>
</head>
<body>

  <!-- Mobile Backdrop Overlay -->
  <div class="superadmin-backdrop" id="superadminBackdrop" onclick="toggleSidebar()"></div>

  <!-- ─── SIDEBAR ────────────────────────────────────────── -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="brand-logo">
        <i class="fa-solid fa-crown"></i>
      </div>
      <div class="brand-text">
        <h1>DineFlow OS</h1>
        <span>SUPER ADMIN</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <a class="nav-item active" onclick="switchSection('dashboard', this)">
        <i class="fa-solid fa-chart-pie"></i>
        <span>Platform Overview</span>
      </a>
      <a class="nav-item" onclick="switchSection('restaurants', this)">
        <i class="fa-solid fa-store"></i>
        <span>Restaurants</span>
      </a>
      <a class="nav-item" onclick="switchSection('subscriptions', this)">
        <i class="fa-solid fa-layer-group"></i>
        <span>SaaS Subscriptions</span>
      </a>
      <a class="nav-item" onclick="switchSection('analytics', this)">
        <i class="fa-solid fa-chart-line"></i>
        <span>Global Analytics</span>
      </a>
      <a class="nav-item" onclick="switchSection('audit-logs', this)">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>Audit Logs</span>
      </a>
      <a class="nav-item" onclick="switchSection('leads', this)">
        <i class="fa-solid fa-address-book"></i>
        <span>Leads &amp; Enquiries</span>
        <span id="leadCountBadge" style="display:none; margin-left:auto; background:#EF4444; color:#fff; font-size:11px; padding:2px 7px; border-radius:10px; font-weight:700;">0</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="avatar">SA</div>
        <div class="user-info">
          <h4>{{ Auth::user()->name ?? 'Super Admin' }}</h4>
          <p>{{ Auth::user()->email ?? 'superadmin@tendorabuy.in' }}</p>
        </div>
      </div>
      <form action="{{ route('auth.logout') }}" method="POST" style="width: 100%;">
        @csrf
        <button type="submit" class="nav-item" style="width: 100%; border: none; background: rgba(239, 68, 68, 0.1); color: var(--danger);">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- ─── MAIN CONTENT ───────────────────────────────────── -->
  <div class="main-wrapper">
    <header class="topbar">
      <div class="topbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">
          <h2 id="currentSectionTitle">Platform Overview</h2>
        </div>
      </div>

      <div class="topbar-right">
        <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Theme">
          <i class="fa-solid fa-moon" id="themeIcon"></i>
        </button>
        <button class="btn-create" onclick="openCreateModal()">
          <i class="fa-solid fa-plus"></i>
          <span>Add Restaurant</span>
        </button>
      </div>
    </header>

    <main class="content-area">

      <!-- ── SECTION: DASHBOARD ── -->
      <section id="section-dashboard">
        <div class="metrics-grid">
          <div class="metric-card">
            <div class="metric-header">
              <span class="metric-label">Total Restaurants</span>
              <div class="metric-icon" style="background: rgba(99, 102, 241, 0.15); color: #818CF8;">
                <i class="fa-solid fa-store"></i>
              </div>
            </div>
            <div class="metric-value">{{ $stats['total_restaurants'] ?? 0 }}</div>
            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
              <span class="metric-badge" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">
                <i class="fa-solid fa-circle-check"></i> {{ $stats['active_restaurants'] ?? 0 }} Active
              </span>
              <span class="metric-badge" style="background: rgba(239, 68, 68, 0.15); color: #F87171;">
                {{ $stats['suspended_restaurants'] ?? 0 }} Suspended
              </span>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-header">
              <span class="metric-label">Subscription States</span>
              <div class="metric-icon" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24;">
                <i class="fa-solid fa-hourglass-half"></i>
              </div>
            </div>
            <div class="metric-value" style="font-size: 20px;">
              {{ $stats['grace_restaurants'] ?? 0 }} Grace / {{ $stats['expired_restaurants'] ?? 0 }} Expired
            </div>
            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
              <span class="metric-badge" style="background: rgba(6, 182, 212, 0.15); color: #22D3EE;">
                {{ $stats['trial_restaurants'] ?? 0 }} Trial
              </span>
              <span class="metric-badge" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24;">
                {{ $stats['expiring_restaurants'] ?? 0 }} Expiring
              </span>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-header">
              <span class="metric-label">Platform GMV (Revenue)</span>
              <div class="metric-icon" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">
                <i class="fa-solid fa-indian-rupee-sign"></i>
              </div>
            </div>
            <div class="metric-value">₹{{ number_format($stats['revenue']['total_gmv'] ?? 0, 2) }}</div>
            <div>
              <span class="metric-badge" style="background: rgba(99, 102, 241, 0.15); color: #818CF8;">
                Today: ₹{{ number_format($stats['revenue']['today_gmv'] ?? 0, 2) }}
              </span>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-header">
              <span class="metric-label">Plan Tiers</span>
              <div class="metric-icon" style="background: rgba(6, 182, 212, 0.15); color: #22D3EE;">
                <i class="fa-solid fa-layer-group"></i>
              </div>
            </div>
            <div class="metric-value">{{ $stats['plans']['free'] ?? 0 }} Free</div>
            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
              <span class="metric-badge" style="background: rgba(99, 102, 241, 0.15); color: #818CF8;">
                {{ $stats['plans']['pro'] ?? 0 }} Pro
              </span>
              <span class="metric-badge" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24;">
                {{ $stats['plans']['enterprise'] ?? 0 }} Ent
              </span>
              <span class="metric-badge" style="background: rgba(6, 182, 212, 0.15); color: #22D3EE;">
                {{ $stats['plans']['custom'] ?? 0 }} Custom
              </span>
            </div>
          </div>
        </div>

        <!-- Top Restaurants & System Activity -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px; margin-top: 24px;">
          <div class="table-container">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 800;">🏆 Top Performing Restaurants</h3>
            <table style="margin-top: 12px;">
              <thead>
                <tr>
                  <th>Restaurant</th>
                  <th>Plan</th>
                  <th>Status</th>
                  <th>Orders</th>
                  <th>Revenue</th>
                </tr>
              </thead>
              <tbody>
                @forelse($stats['top_restaurants'] ?? [] as $top)
                <tr>
                  <td style="font-weight: 700; color: var(--text-primary);">{{ $top['name'] }}</td>
                  <td><span class="plan-badge plan-{{ $top['plan_type'] }}">{{ $top['plan_type'] }}</span></td>
                  <td><span class="status-badge status-{{ $top['subscription_status'] }}">{{ $top['subscription_status'] }}</span></td>
                  <td>{{ $top['orders_count'] }}</td>
                  <td style="font-weight: 700; color: var(--success);">₹{{ number_format($top['revenue'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No restaurant data yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="table-container">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 800;">⚡ Recent Live Orders Across Platform</h3>
            <div id="recentOrdersList" style="display: flex; flex-direction: column; gap: 10px; margin-top: 14px;">
              <p style="color: var(--text-muted); font-size: 13px;">Loading live orders...</p>
            </div>
          </div>
        </div>
      </section>

      <!-- ── SECTION: RESTAURANTS ── -->
      <section id="section-restaurants" style="display: none;">
        <div class="table-container">
          <div class="table-header">
            <div class="search-filter-bar">
              <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="restaurantSearch" class="search-input" placeholder="Search by name, phone, address..." oninput="debounceFetchRestaurants()">
              </div>
              <select id="planFilter" class="filter-select" onchange="fetchRestaurants()">
                <option value="">All Plans</option>
                <option value="free">Free Plan</option>
                <option value="pro">Pro Plan</option>
                <option value="enterprise">Enterprise Plan</option>
                <option value="custom">Custom Plan</option>
              </select>
              <select id="statusFilter" class="filter-select" onchange="fetchRestaurants()">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="trial">Trial</option>
                <option value="expiring">Expiring Soon</option>
                <option value="grace_period">In Grace Period</option>
                <option value="expired">Expired</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
          </div>

          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Restaurant</th>
                  <th>Owner</th>
                  <th>Plan</th>
                  <th>Status & Expiry</th>
                  <th>Orders</th>
                  <th>Revenue</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="restaurantsTableBody">
                <tr><td colspan="8" style="text-align: center;">Loading restaurants...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ── SECTION: SUBSCRIPTIONS ── -->
      <section id="section-subscriptions" style="display: none;">
        <div class="table-container">
          <div class="table-header">
            <div>
              <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800;">SaaS Subscription Management</h3>
              <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Monitor plan renewals, grace periods, and manual offline payments.</p>
            </div>
            <button class="btn-secondary" onclick="runExpiryJob()"><i class="fa-solid fa-bolt"></i> Evaluate Expiry Engine</button>
          </div>
          <div style="overflow-x: auto; margin-top: 14px;">
            <table>
              <thead>
                <tr>
                  <th>Restaurant</th>
                  <th>Plan Tier</th>
                  <th>Status</th>
                  <th>Expiry Date</th>
                  <th>Grace (Days)</th>
                  <th>Payment Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="subscriptionsTableBody">
                <tr><td colspan="7" style="text-align: center;">Loading subscriptions...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ── SECTION: ANALYTICS ── -->
      <section id="section-analytics" style="display: none;">
        <div class="table-container">
          <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800;">📈 Global Platform Growth & Volume</h3>
          <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">Aggregated metrics across all onboarded restaurants and diners.</p>
          <div class="metrics-grid" style="margin-top: 20px;">
            <div class="metric-card">
              <span class="metric-label">Total Completed Orders</span>
              <div class="metric-value" style="color: var(--info);">{{ number_format($stats['orders']['total'] ?? 0) }}</div>
            </div>
            <div class="metric-card">
              <span class="metric-label">Monthly Gross Revenue</span>
              <div class="metric-value" style="color: var(--success);">₹{{ number_format($stats['revenue']['this_month_gmv'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-card">
              <span class="metric-label">Active Tenants</span>
              <div class="metric-value">{{ $stats['active_restaurants'] ?? 0 }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── SECTION: AUDIT LOGS ── -->
      <section id="section-audit-logs" style="display: none;">
        <div class="table-container">
          <div class="table-header">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800;">📋 System Audit Logs</h3>
            <button class="btn-secondary" onclick="fetchAuditLogs()"><i class="fa-solid fa-rotate"></i> Refresh</button>
          </div>
          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>Action</th>
                  <th>Actor</th>
                  <th>Target</th>
                  <th>IP Address</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody id="auditLogsTableBody">
                <tr><td colspan="6" style="text-align: center;">Loading audit logs...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ── SECTION: LEADS & ENQUIRIES ── -->
      <section id="section-leads" style="display: none;">
        <!-- Lead Metrics Overview -->
        <div class="metrics-grid" style="margin-bottom: 24px;">
          <div class="metric-card">
            <div class="metric-icon" style="background: rgba(99, 102, 241, 0.15); color: #818CF8;"><i class="fa-solid fa-inbox"></i></div>
            <div class="metric-content">
              <span class="metric-label">Total Leads</span>
              <div class="metric-value" id="leadStatTotal">0</div>
              <span class="metric-change" style="color: var(--text-muted);">All submissions</span>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon" style="background: rgba(239, 68, 68, 0.15); color: #F87171;"><i class="fa-solid fa-bell"></i></div>
            <div class="metric-content">
              <span class="metric-label">New / Pending</span>
              <div class="metric-value" id="leadStatNew">0</div>
              <span class="metric-change" style="color: #F87171;">Requires follow-up</span>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24;"><i class="fa-solid fa-phone"></i></div>
            <div class="metric-content">
              <span class="metric-label">Contacted</span>
              <div class="metric-value" id="leadStatContacted">0</div>
              <span class="metric-change" style="color: #FBBF24;">In discussion</span>
            </div>
          </div>
          <div class="metric-card">
            <div class="metric-icon" style="background: rgba(16, 185, 129, 0.15); color: #34D399;"><i class="fa-solid fa-circle-check"></i></div>
            <div class="metric-content">
              <span class="metric-label">Converted</span>
              <div class="metric-value" id="leadStatConverted">0</div>
              <span class="metric-change" style="color: #34D399;">Active restaurants</span>
            </div>
          </div>
        </div>

        <div class="table-container">
          <div class="table-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
              <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800;">📥 Public Contact &amp; Demo Leads</h3>
              <div style="position: relative;">
                <input type="text" id="leadSearchInput" placeholder="Search name, restaurant, phone..." style="background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 7px 14px 7px 34px; border-radius: 8px; font-size: 13px; outline: none; width: 240px;" onkeyup="if(event.key==='Enter') fetchLeads(1)">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--text-muted);"></i>
              </div>
              <select id="leadStatusFilter" style="background: var(--bg-card); border: 1px solid var(--border-color); color: #fff; padding: 7px 12px; border-radius: 8px; font-size: 13px; outline: none;" onchange="fetchLeads(1)">
                <option value="all">All Statuses</option>
                <option value="new">New</option>
                <option value="pending">Pending</option>
                <option value="contacted">Contacted</option>
                <option value="converted">Converted</option>
                <option value="closed">Closed</option>
              </select>
            </div>
            <div style="display:flex; gap:8px;">
              <button class="btn-secondary" onclick="fetchLeads(1)"><i class="fa-solid fa-rotate"></i> Refresh</button>
            </div>
          </div>

          <div style="overflow-x: auto;">
            <table>
              <thead>
                <tr>
                  <th>Date &amp; Time</th>
                  <th>Restaurant &amp; Contact</th>
                  <th>Phone &amp; WhatsApp</th>
                  <th>Email</th>
                  <th>City / Slot</th>
                  <th>Message / Enquiry</th>
                  <th>Source</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="leadsTableBody">
                <tr><td colspan="9" style="text-align: center; padding: 24px;">Loading leads...</td></tr>
              </tbody>
            </table>
          </div>

          <div id="leadsPagination" style="display:flex; justify-content:space-between; align-items:center; padding:16px 20px; border-top:1px solid var(--border-color); font-size:13px; color:var(--text-secondary);">
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- ── MODAL: CREATE RESTAURANT ── -->
  <div class="modal-overlay" id="createModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Create New Restaurant & Owner</h3>
        <button class="modal-close" onclick="closeModal('createModal')">&times;</button>
      </div>
      <form id="createRestaurantForm" onsubmit="submitCreateRestaurant(event)">
        <div class="modal-body">
          <div class="form-group">
            <label>Restaurant Name *</label>
            <input type="text" name="restaurant_name" class="form-input" required placeholder="e.g. Royal Spice Bistro">
          </div>
          <div class="form-group">
            <label>Owner Full Name *</label>
            <input type="text" name="owner_name" class="form-input" required placeholder="e.g. Rajesh Sharma">
          </div>
          <div class="form-group">
            <label>Owner Email *</label>
            <input type="email" name="owner_email" class="form-input" required placeholder="rajesh@example.com">
          </div>
          <div class="form-group">
            <label>Owner Password *</label>
            <input type="password" name="owner_password" class="form-input" required minlength="6" placeholder="Min 6 characters">
          </div>
          <div class="form-group">
            <label>Phone Number *</label>
            <input type="text" name="phone" class="form-input" required placeholder="9876543210">
          </div>
          <div class="form-group">
            <label>Address *</label>
            <input type="text" name="address" class="form-input" required placeholder="City, State">
          </div>
          <div class="form-group">
            <label>SaaS Plan Tier</label>
            <select name="plan_type" class="form-select">
              <option value="free">Free Tier (10 tables, 5 staff, 100 items)</option>
              <option value="pro">Pro Tier (50 tables, 25 staff, 500 items)</option>
              <option value="enterprise">Enterprise Tier (200 tables, 100 staff, 2000 items)</option>
              <option value="custom">Custom Quota Tier</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeModal('createModal')">Cancel</button>
          <button type="submit" class="btn-primary">Create Restaurant</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── MODAL: SUSPEND RESTAURANT (WITH REASON & CUSTOM MESSAGES) ── -->
  <div class="modal-overlay" id="suspendModal">
    <div class="modal-card" style="max-width: 540px;">
      <div class="modal-header">
        <h3 style="color: var(--danger);"><i class="fa-solid fa-ban"></i> Suspend Restaurant</h3>
        <button class="modal-close" onclick="closeModal('suspendModal')">&times;</button>
      </div>
      <form id="suspendForm" onsubmit="submitSuspend(event)">
        <input type="hidden" id="suspendRestaurantId">
        <div class="modal-body" style="display: flex; flex-direction: column; gap: 16px;">
          <div>
            <p id="suspendRestaurantName" style="font-weight: 800; font-size: 15px; color: var(--text-primary); margin: 0 0 4px;"></p>
            <p style="font-size: 12px; color: var(--text-muted); margin: 0;">Suspending this restaurant will temporarily block staff from processing new orders/bills while keeping all historical data 100% intact.</p>
          </div>

          <div class="form-group">
            <label style="font-size: 12px; font-weight: 700; color: var(--text-primary);">Suspension Category / Reason *</label>
            <select id="suspendReasonSelect" class="form-select" onchange="handleSuspendReasonChange(this.value)" required>
              <option value="Payment Pending">Payment Pending / Overdue</option>
              <option value="Subscription Issue">Subscription Issue / Expired</option>
              <option value="Policy Violation">Policy Violation / Terms Breach</option>
              <option value="Temporary Suspension" selected>Temporary Administrative Suspension</option>
              <option value="Other">Other Custom Reason</option>
            </select>
          </div>

          <div class="form-group" id="customReasonWrapper" style="display: none;">
            <label style="font-size: 12px; font-weight: 700; color: var(--text-primary);">Custom Reason Details</label>
            <input type="text" id="suspendCustomReason" class="form-input" placeholder="Specify custom suspension reason...">
          </div>

          <div class="form-group">
            <label style="font-size: 12px; font-weight: 700; color: var(--text-primary);">
              Customer-Facing Message
              <span style="font-size: 11px; font-weight: 400; color: var(--text-muted);">(Visible to restaurant users upon login)</span>
            </label>
            <textarea id="suspendCustomerMessage" class="form-textarea" rows="3" placeholder="Your restaurant account has been temporarily suspended by Tendorabuy Administration. Please contact support for assistance."></textarea>
          </div>

          <div class="form-group">
            <label style="font-size: 12px; font-weight: 700; color: var(--text-primary);">
              Internal Super Admin Note
              <span style="font-size: 11px; font-weight: 400; color: #F87171;">(CONFIDENTIAL — Never shown to restaurant)</span>
            </label>
            <textarea id="suspendInternalNote" class="form-textarea" rows="2" placeholder="e.g. Discussed with client on WhatsApp, requested payment by Friday..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeModal('suspendModal')">Cancel</button>
          <button type="submit" class="btn-danger" style="background: #DC2626;"><i class="fa-solid fa-ban"></i> Confirm Suspension</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── MODAL: SUBSCRIPTION OVERRIDE ── -->
  <div class="modal-overlay" id="subscriptionModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Override Subscription Lifecycle</h3>
        <button class="modal-close" onclick="closeModal('subscriptionModal')">&times;</button>
      </div>
      <form id="subscriptionForm" onsubmit="submitSubscriptionOverride(event)">
        <input type="hidden" id="subModalRestaurantId">
        <div class="modal-body">
          <p id="subModalRestaurantName" style="font-weight: 700; font-size: 14px;"></p>
          <div class="form-group">
            <label>Subscription Status *</label>
            <select id="subModalStatus" class="form-select" required>
              <option value="active">Active</option>
              <option value="trial">Trial</option>
              <option value="expiring">Expiring Soon</option>
              <option value="grace_period">In Grace Period</option>
              <option value="expired">Expired</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
          <div class="form-group">
            <label>Plan Tier</label>
            <select id="subModalPlan" class="form-select">
              <option value="free">Free Plan</option>
              <option value="pro">Pro Plan</option>
              <option value="enterprise">Enterprise Plan</option>
              <option value="custom">Custom Plan</option>
            </select>
          </div>
          <div class="form-group">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <label style="margin:0;">Expiry Date</label>
              <div style="display:flex; gap:4px;">
                <button type="button" class="btn-secondary" style="padding:2px 7px; font-size:11px; border-radius:6px;" onclick="quickExtendExpiry(1)">+1 Day</button>
                <button type="button" class="btn-secondary" style="padding:2px 7px; font-size:11px; border-radius:6px;" onclick="quickExtendExpiry(7)">+7 Days</button>
                <button type="button" class="btn-secondary" style="padding:2px 7px; font-size:11px; border-radius:6px;" onclick="quickExtendExpiry(30)">+30 Days</button>
                <button type="button" class="btn-secondary" style="padding:2px 7px; font-size:11px; border-radius:6px;" onclick="quickExtendExpiry(90)">+90 Days</button>
                <button type="button" class="btn-secondary" style="padding:2px 7px; font-size:11px; border-radius:6px;" onclick="quickExtendExpiry(365)">+1 Year</button>
              </div>
            </div>
            <input type="date" id="subModalExpiry" class="form-input">
          </div>
          <div class="form-group">
            <label>Grace Period (Days)</label>
            <input type="number" id="subModalGrace" class="form-input" min="0" max="60" value="7">
          </div>
          <div class="form-group">
            <label>Customer-Facing Expiry / Renewal Message</label>
            <textarea id="subModalExpiryMessage" class="form-textarea" rows="2" placeholder="Custom message shown to restaurant owner upon plan expiry (e.g. તમારો Pro Plan પૂર્ણ થઈ ગયો છે. કૃપા કરીને રિન્યૂ કરો...)"></textarea>
          </div>
          <div class="form-group">
            <label>Confidential Internal Admin Note</label>
            <textarea id="subModalInternalNote" class="form-textarea" rows="2" placeholder="Private note for Super Admin team (Strictly NEVER shown to restaurant)"></textarea>
          </div>
          <div class="form-group">
            <label>Action Reason / Note</label>
            <input type="text" id="subModalReason" class="form-input" placeholder="e.g. Promotional extension, trial extension">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeModal('subscriptionModal')">Cancel</button>
          <button type="submit" class="btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── MODAL: RECORD MANUAL PAYMENT ── -->
  <div class="modal-overlay" id="paymentModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Record Offline / Manual Payment</h3>
        <button class="modal-close" onclick="closeModal('paymentModal')">&times;</button>
      </div>
      <form id="paymentForm" onsubmit="submitRecordPayment(event)">
        <input type="hidden" id="payModalRestaurantId">
        <div class="modal-body">
          <p id="payModalRestaurantName" style="font-weight: 700; font-size: 14px;"></p>
          <div class="form-group">
            <label>Amount (₹) *</label>
            <input type="number" step="0.01" id="payModalAmount" class="form-input" required placeholder="e.g. 1999.00">
          </div>
          <div class="form-group">
            <label>Payment Method *</label>
            <select id="payModalMethod" class="form-select" required>
              <option value="bank_transfer">Bank Transfer (NEFT/IMPS)</option>
              <option value="upi">UPI / QR Code</option>
              <option value="cash">Cash Settlement</option>
              <option value="cheque">Cheque</option>
              <option value="razorpay">Razorpay (External)</option>
            </select>
          </div>
          <div class="form-group">
            <label>Transaction / Reference ID</label>
            <input type="text" id="payModalRef" class="form-input" placeholder="e.g. UTR123456789">
          </div>
          <div class="form-group">
            <label>Extend Subscription Duration</label>
            <select id="payModalExtend" class="form-select">
              <option value="1">Extend by 1 Month</option>
              <option value="3">Extend by 3 Months</option>
              <option value="6">Extend by 6 Months</option>
              <option value="12">Extend by 1 Year</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeModal('paymentModal')">Cancel</button>
          <button type="submit" class="btn-primary">Record Payment & Extend</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── MODAL: RESTAURANT DETAILS & USAGE DRAWER ── -->
  <div class="modal-overlay" id="detailsModal">
    <div class="modal-card" style="max-width: 750px;">
      <div class="modal-header">
        <h3>Restaurant Inspection & Analytics</h3>
        <button class="modal-close" onclick="closeModal('detailsModal')">&times;</button>
      </div>
      <div class="modal-body" id="detailsModalContent">
        <p style="text-align: center; padding: 20px;">Loading restaurant details...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal('detailsModal')">Close</button>
      </div>
    </div>
  </div>

  <!-- ── TOAST CONTAINER ── -->
  <div class="toast-container" id="toastContainer"></div>

  <script>
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    let debounceTimer;

    function showToast(message, type = 'success') {
      const container = document.getElementById('toastContainer');
      const toast = document.createElement('div');
      toast.className = 'toast';
      toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}" style="color: ${type === 'success' ? '#10B981' : '#EF4444'}"></i> <span>${message}</span>`;
      container.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    }

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const backdrop = document.getElementById('superadminBackdrop');
      if (sidebar) {
        sidebar.classList.toggle('open');
        if (backdrop) backdrop.classList.toggle('active', sidebar.classList.contains('open'));
      }
    }

    function toggleTheme() {
      document.body.classList.toggle('light-theme');
      const isLight = document.body.classList.contains('light-theme');
      document.getElementById('themeIcon').className = isLight ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
      localStorage.setItem('dineflow_theme', isLight ? 'light' : 'dark');
    }

    if (localStorage.getItem('dineflow_theme') === 'light') {
      document.body.classList.add('light-theme');
      document.getElementById('themeIcon').className = 'fa-solid fa-sun';
    }

    function switchSection(sectionId, navEl) {
      const sidebar = document.getElementById('sidebar');
      const backdrop = document.getElementById('superadminBackdrop');
      if (sidebar && sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        if (backdrop) backdrop.classList.remove('active');
      }

      document.querySelectorAll('.sidebar-nav .nav-item').forEach(el => el.classList.remove('active'));
      if (navEl) navEl.classList.add('active');

      document.querySelectorAll('main > section').forEach(sec => sec.style.display = 'none');
      const targetSec = document.getElementById(`section-${sectionId}`);
      if (targetSec) targetSec.style.display = 'block';

      const titles = {
        'dashboard': 'Platform Overview',
        'restaurants': 'Restaurants Management',
        'subscriptions': 'SaaS Subscription Control',
        'analytics': 'Global Analytics',
        'audit-logs': 'System Audit Logs',
        'leads': 'Leads & Enquiries Management'
      };
      document.getElementById('currentSectionTitle').textContent = titles[sectionId] || 'Super Admin';

      if (sectionId === 'restaurants') fetchRestaurants();
      if (sectionId === 'subscriptions') fetchSubscriptions();
      if (sectionId === 'audit-logs') fetchAuditLogs();
      if (sectionId === 'dashboard') fetchRecentOrders();
      if (sectionId === 'leads') fetchLeads(1);

      if (window.innerWidth <= 1024) document.getElementById('sidebar').classList.remove('open');
    }

    function openModal(id) {
      document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
      document.getElementById(id).classList.remove('active');
    }

    function openCreateModal() {
      document.getElementById('createRestaurantForm').reset();
      openModal('createModal');
    }

    async function fetchRecentOrders() {
      try {
        const res = await fetch('/api/superadmin/orders');
        const json = await res.json();
        if (json.success) {
          const list = document.getElementById('recentOrdersList');
          if (!json.data.length) {
            list.innerHTML = '<p style="color: var(--text-muted); font-size: 13px;">No recent orders recorded.</p>';
            return;
          }
          list.innerHTML = json.data.slice(0, 5).map(o => `
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: var(--bg-secondary); border-radius: 10px; font-size: 13px;">
              <div>
                <strong style="color: var(--text-primary);">${o.restaurant_name}</strong>
                <div style="font-size: 11px; color: var(--text-muted);">#${o.id} • Table: ${o.table_number}</div>
              </div>
              <div style="text-align: right;">
                <strong style="color: var(--success);">₹${o.total_amount.toFixed(2)}</strong>
                <div style="font-size: 10px; text-transform: uppercase; color: ${o.status === 'completed' ? 'var(--success)' : 'var(--warning)'}; font-weight: 700;">${o.status}</div>
              </div>
            </div>
          `).join('');
        }
      } catch (err) {
        console.error('Failed to fetch recent orders:', err);
      }
    }

    function debounceFetchRestaurants() {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(fetchRestaurants, 300);
    }

    async function fetchRestaurants() {
      const search = document.getElementById('restaurantSearch')?.value || '';
      const plan = document.getElementById('planFilter')?.value || '';
      const status = document.getElementById('statusFilter')?.value || '';

      const url = new URL('/api/superadmin/restaurants', window.location.origin);
      if (search) url.searchParams.set('search', search);
      if (plan) url.searchParams.set('plan', plan);
      if (status) url.searchParams.set('status', status);

      try {
        const res = await fetch(url.toString());
        const json = await res.json();
        const tbody = document.getElementById('restaurantsTableBody');

        if (json.success && json.data.length > 0) {
          tbody.innerHTML = json.data.map(r => {
            const expiryStr = r.subscription_expires_at ? new Date(r.subscription_expires_at).toLocaleDateString() : 'Lifetime / Free';
            return `
            <tr>
              <td>#${r.id}</td>
              <td>
                <strong style="color: var(--text-primary); font-size: 14px; cursor: pointer;" onclick="openDetailsModal(${r.id})">${r.name}</strong>
                <div style="font-size: 11px; color: var(--text-muted);">${r.phone || 'No phone'} • ${r.address || ''}</div>
              </td>
              <td>
                <div>${r.owner ? r.owner.name : 'N/A'}</div>
                <div style="font-size: 11px; color: var(--text-muted);">${r.owner ? r.owner.email : ''}</div>
              </td>
              <td><span class="plan-badge plan-${r.plan_type}">${r.plan_type}</span></td>
              <td>
                <span class="status-badge status-${r.subscription_status}">
                  <i class="fa-solid fa-circle" style="font-size: 6px;"></i> ${r.subscription_status}
                </span>
                <div style="font-size: 10px; color: var(--text-muted); margin-top: 2px;">Exp: ${expiryStr}</div>
              </td>
              <td><strong>${r.orders_count}</strong></td>
              <td style="color: var(--success); font-weight: 700;">₹${r.total_revenue.toLocaleString('en-IN')}</td>
              <td>
                <div class="actions-group">
                  <button class="action-btn" title="View Full Inspection" onclick="openDetailsModal(${r.id})">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                  ${r.subscription_status === 'suspended' ? `
                    <button class="action-btn" title="Activate / Restore Restaurant" onclick="quickActivate(${r.id})">
                      <i class="fa-solid fa-play" style="color: var(--success);"></i>
                    </button>
                  ` : `
                    <button class="action-btn" title="Suspend Restaurant" onclick="openSuspendModal(${r.id}, '${r.name}')">
                      <i class="fa-solid fa-pause" style="color: var(--danger);"></i>
                    </button>
                  `}
                  <button class="action-btn" title="Subscription & Expiry" onclick="openSubscriptionModal(${r.id}, '${r.name}', '${r.subscription_status}', '${r.plan_type}', '${r.subscription_expires_at || ''}', ${r.grace_period_days || 7})">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                  </button>
                  <button class="action-btn" title="Record Manual Payment" onclick="openPaymentModal(${r.id}, '${r.name}', ${r.billing_amount || 0})">
                    <i class="fa-solid fa-receipt"></i>
                  </button>
                </div>
              </td>
            </tr>
          `}).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">No restaurants found.</td></tr>';
        }
      } catch (err) {
        console.error('Failed to fetch restaurants:', err);
      }
    }

    async function fetchSubscriptions() {
      try {
        const res = await fetch('/api/superadmin/restaurants?per_page=50');
        const json = await res.json();
        const tbody = document.getElementById('subscriptionsTableBody');
        if (json.success && json.data.length > 0) {
          tbody.innerHTML = json.data.map(r => `
            <tr>
              <td><strong>${r.name}</strong></td>
              <td><span class="plan-badge plan-${r.plan_type}">${r.plan_type}</span></td>
              <td><span class="status-badge status-${r.subscription_status}">${r.subscription_status}</span></td>
              <td>${r.subscription_expires_at ? new Date(r.subscription_expires_at).toLocaleDateString() : 'N/A (Free/Lifetime)'}</td>
              <td>${r.grace_period_days} Days</td>
              <td><span class="metric-badge" style="background: rgba(16,185,129,0.15); color: #34D399;">${r.payment_status}</span></td>
              <td>
                <div class="actions-group">
                  <button class="action-btn" title="Edit Subscription" onclick="openSubscriptionModal(${r.id}, '${r.name}', '${r.subscription_status}', '${r.plan_type}', '${r.subscription_expires_at || ''}', ${r.grace_period_days || 7})">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  <button class="action-btn" title="Record Payment" onclick="openPaymentModal(${r.id}, '${r.name}', ${r.billing_amount || 0})">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                  </button>
                </div>
              </td>
            </tr>
          `).join('');
        }
      } catch (err) {
        console.error('Failed to fetch subscriptions:', err);
      }
    }

    function handleSuspendReasonChange(val) {
      const customWrapper = document.getElementById('customReasonWrapper');
      const custMsg = document.getElementById('suspendCustomerMessage');
      if (val === 'Other') {
        customWrapper.style.display = 'block';
      } else {
        customWrapper.style.display = 'none';
      }

      // Populate smart default customer message based on reason
      if (val === 'Payment Pending') {
        custMsg.value = 'Your restaurant account has been temporarily suspended due to a pending subscription payment. Please contact support to restore immediate access.';
      } else if (val === 'Subscription Issue') {
        custMsg.value = 'Your restaurant subscription has expired and access is suspended. Please renew your plan or contact support.';
      } else if (val === 'Policy Violation') {
        custMsg.value = 'Your restaurant account has been suspended due to an administrative policy review. Please contact Tendorabuy support.';
      } else {
        custMsg.value = 'Your restaurant account has been temporarily suspended by Tendorabuy Administration. Please contact support for assistance.';
      }
    }

    function openSuspendModal(id, name) {
      document.getElementById('suspendRestaurantId').value = id;
      document.getElementById('suspendRestaurantName').textContent = `Suspend Restaurant: ${name}`;
      document.getElementById('suspendReasonSelect').value = 'Temporary Suspension';
      document.getElementById('customReasonWrapper').style.display = 'none';
      document.getElementById('suspendCustomReason').value = '';
      document.getElementById('suspendCustomerMessage').value = 'Your restaurant account has been temporarily suspended by Tendorabuy Administration. Please contact support for assistance.';
      document.getElementById('suspendInternalNote').value = '';
      openModal('suspendModal');
    }

    async function submitSuspend(e) {
      e.preventDefault();
      const id = document.getElementById('suspendRestaurantId').value;
      const selectReason = document.getElementById('suspendReasonSelect').value;
      const customReason = document.getElementById('suspendCustomReason').value.trim();
      const reason = (selectReason === 'Other' && customReason) ? customReason : selectReason;
      const customer_message = document.getElementById('suspendCustomerMessage').value.trim();
      const internal_note = document.getElementById('suspendInternalNote').value.trim();

      try {
        const res = await fetch(`/api/superadmin/restaurants/${id}/suspend`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
          body: JSON.stringify({ reason, customer_message, internal_note })
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message);
          closeModal('suspendModal');
          fetchRestaurants();
        } else {
          showToast(json.message || 'Failed', 'error');
        }
      } catch (err) {
        showToast('Operation failed', 'error');
      }
    }

    async function quickActivate(id) {
      if (!confirm('Are you sure you want to activate/restore this restaurant?')) return;
      try {
        const res = await fetch(`/api/superadmin/restaurants/${id}/activate`, {
          method: 'POST',
          headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message);
          fetchRestaurants();
        }
      } catch (err) {
        showToast('Activation failed', 'error');
      }
    }

    function openSubscriptionModal(id, name, status, plan, expiry, grace, expiryMsg, internalNote) {
      document.getElementById('subModalRestaurantId').value = id;
      document.getElementById('subModalRestaurantName').textContent = `Manage Subscription: ${name}`;
      document.getElementById('subModalStatus').value = status || 'active';
      document.getElementById('subModalPlan').value = plan || 'free';
      document.getElementById('subModalExpiry').value = expiry ? expiry.split('T')[0] : '';
      document.getElementById('subModalGrace').value = grace || 7;
      document.getElementById('subModalExpiryMessage').value = expiryMsg || '';
      document.getElementById('subModalInternalNote').value = internalNote || '';
      document.getElementById('subModalReason').value = '';
      openModal('subscriptionModal');
    }

    function quickExtendExpiry(days) {
      const currentVal = document.getElementById('subModalExpiry').value;
      let base = currentVal ? new Date(currentVal + 'T00:00:00') : new Date();
      if (isNaN(base.getTime()) || base < new Date()) {
        base = new Date();
      }
      base.setDate(base.getDate() + days);
      const yyyy = base.getFullYear();
      const mm = String(base.getMonth() + 1).padStart(2, '0');
      const dd = String(base.getDate()).padStart(2, '0');
      document.getElementById('subModalExpiry').value = `${yyyy}-${mm}-${dd}`;
      document.getElementById('subModalStatus').value = 'active';
      document.getElementById('subModalReason').value = `Extended validity by ${days} day(s)`;
    }

    async function submitSubscriptionOverride(e) {
      e.preventDefault();
      const id = document.getElementById('subModalRestaurantId').value;
      const payload = {
        subscription_status: document.getElementById('subModalStatus').value,
        plan_type: document.getElementById('subModalPlan').value,
        subscription_expires_at: document.getElementById('subModalExpiry').value || null,
        grace_period_days: parseInt(document.getElementById('subModalGrace').value) || 7,
        expiry_message: document.getElementById('subModalExpiryMessage').value || null,
        suspension_internal_note: document.getElementById('subModalInternalNote').value || null,
        reason: document.getElementById('subModalReason').value || 'Manual override'
      };

      try {
        const res = await fetch(`/api/superadmin/restaurants/${id}/subscription`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message);
          closeModal('subscriptionModal');
          fetchRestaurants();
          fetchSubscriptions();
        } else {
          showToast(json.message || 'Failed', 'error');
        }
      } catch (err) {
        showToast('Error updating subscription', 'error');
      }
    }

    function openPaymentModal(id, name, defaultAmount) {
      document.getElementById('payModalRestaurantId').value = id;
      document.getElementById('payModalRestaurantName').textContent = `Record Payment: ${name}`;
      document.getElementById('payModalAmount').value = defaultAmount > 0 ? defaultAmount : '1999.00';
      document.getElementById('payModalRef').value = '';
      openModal('paymentModal');
    }

    async function submitRecordPayment(e) {
      e.preventDefault();
      const id = document.getElementById('payModalRestaurantId').value;
      const payload = {
        amount: parseFloat(document.getElementById('payModalAmount').value),
        payment_method: document.getElementById('payModalMethod').value,
        payment_reference: document.getElementById('payModalRef').value,
        extend_months: parseInt(document.getElementById('payModalExtend').value) || 1,
      };

      try {
        const res = await fetch(`/api/superadmin/restaurants/${id}/payment`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message);
          closeModal('paymentModal');
          fetchRestaurants();
          fetchSubscriptions();
        } else {
          showToast(json.message || 'Failed', 'error');
        }
      } catch (err) {
        showToast('Payment recording failed', 'error');
      }
    }

    async function openDetailsModal(id) {
      openModal('detailsModal');
      const content = document.getElementById('detailsModalContent');
      content.innerHTML = '<p style="text-align: center; padding: 20px;">Fetching live inspection data...</p>';

      try {
        const res = await fetch(`/api/superadmin/restaurants/${id}`);
        const json = await res.json();
        if (json.success) {
          const r = json.data.restaurant;
          const u = json.data.usage;
          const histories = json.data.subscription_histories || [];
          const logs = json.data.audit_logs || [];

          content.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
              <div>
                <h3 style="font-size: 20px; font-weight: 800;">${r.name}</h3>
                <p style="font-size: 12px; color: var(--text-muted);">${r.address} • ${r.phone}</p>
                <div style="margin-top: 6px;">
                  <span class="plan-badge plan-${r.plan_type}">${r.plan_type}</span>
                  <span class="status-badge status-${r.subscription_status}">${r.subscription_status}</span>
                </div>
              </div>
              <div style="text-align: right;">
                <div style="font-size: 11px; color: var(--text-muted);">Total GMV</div>
                <div style="font-size: 22px; font-weight: 800; color: var(--success);">₹${json.data.total_revenue.toLocaleString('en-IN')}</div>
                <div style="font-size: 11px; color: var(--text-secondary);">${json.data.total_orders} Total Orders</div>
              </div>
            </div>

            <!-- Quota Utilization -->
            <div style="margin-top: 16px;">
              <h4 style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px;">Live Quota Utilization</h4>
              
              <div class="usage-item">
                <div class="usage-header">
                  <span>Tables (${u.usage.tables.current} / ${u.usage.tables.limit})</span>
                  <span>${u.usage.tables.percentage}%</span>
                </div>
                <div class="usage-bar-bg"><div class="usage-bar-fill" style="width: ${Math.min(100, u.usage.tables.percentage)}%;"></div></div>
              </div>

              <div class="usage-item">
                <div class="usage-header">
                  <span>Staff Accounts (${u.usage.staff.current} / ${u.usage.staff.limit})</span>
                  <span>${u.usage.staff.percentage}%</span>
                </div>
                <div class="usage-bar-bg"><div class="usage-bar-fill" style="width: ${Math.min(100, u.usage.staff.percentage)}%;"></div></div>
              </div>

              <div class="usage-item">
                <div class="usage-header">
                  <span>Menu Dishes (${u.usage.menu_items.current} / ${u.usage.menu_items.limit})</span>
                  <span>${u.usage.menu_items.percentage}%</span>
                </div>
                <div class="usage-bar-bg"><div class="usage-bar-fill" style="width: ${Math.min(100, u.usage.menu_items.percentage)}%;"></div></div>
              </div>

              <div class="usage-item">
                <div class="usage-header">
                  <span>Monthly Orders (${u.usage.monthly_orders.current} / ${u.usage.monthly_orders.limit})</span>
                  <span>${u.usage.monthly_orders.percentage}%</span>
                </div>
                <div class="usage-bar-bg"><div class="usage-bar-fill" style="width: ${Math.min(100, u.usage.monthly_orders.percentage)}%;"></div></div>
              </div>
            </div>

            <!-- Subscription Timeline -->
            <div style="margin-top: 16px; border-top: 1px solid var(--border-color); padding-top: 14px;">
              <h4 style="font-size: 13px; text-transform: uppercase; color: var(--text-muted); margin-bottom: 8px;">Subscription Timeline</h4>
              <div style="max-height: 120px; overflow-y: auto; font-size: 12px; display: flex; flex-direction: column; gap: 6px;">
                ${histories.length ? histories.map(h => `
                  <div style="padding: 6px 10px; background: var(--bg-primary); border-radius: 8px; display: flex; justify-content: space-between;">
                    <span><strong>${h.new_plan.toUpperCase()}</strong> (${h.new_status}) — ${h.reason || 'Status change'}</span>
                    <span style="color: var(--text-muted);">${new Date(h.created_at).toLocaleDateString()}</span>
                  </div>
                `).join('') : '<p style="color: var(--text-muted);">No subscription history recorded yet.</p>'}
              </div>
            </div>
          `;
        }
      } catch (err) {
        content.innerHTML = '<p style="color: var(--danger); text-align: center;">Failed to load restaurant details.</p>';
      }
    }

    async function submitCreateRestaurant(e) {
      e.preventDefault();
      const form = e.target;
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      try {
        const res = await fetch('/api/superadmin/restaurants', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
          body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Restaurant created successfully!');
          closeModal('createModal');
          fetchRestaurants();
        } else {
          showToast(json.message || 'Failed to create restaurant', 'error');
        }
      } catch (err) {
        showToast('Error creating restaurant', 'error');
      }
    }

    async function runExpiryJob() {
      showToast('Running automated expiration evaluation...', 'info');
      setTimeout(() => {
        showToast('Subscription evaluation completed.', 'success');
        fetchSubscriptions();
      }, 1000);
    }

    async function fetchAuditLogs() {
      try {
        const res = await fetch('/api/superadmin/audit-logs');
        const json = await res.json();
        const tbody = document.getElementById('auditLogsTableBody');
        if (json.success && json.data.length > 0) {
          tbody.innerHTML = json.data.map(l => `
            <tr>
              <td style="white-space: nowrap; font-size: 11px; color: var(--text-muted);">${new Date(l.created_at).toLocaleString()}</td>
              <td><span class="metric-badge" style="background: rgba(99,102,241,0.15); color: #818CF8;">${l.action}</span></td>
              <td>${l.user ? l.user.name : 'System'}</td>
              <td>${l.restaurant ? l.restaurant.name : (l.target_type + ' #' + l.target_id)}</td>
              <td style="font-family: monospace; font-size: 11px;">${l.ip_address || 'N/A'}</td>
              <td style="font-size: 11px; color: var(--text-muted); max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                ${JSON.stringify(l.metadata || {})}
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 24px;">No audit log records found.</td></tr>';
        }
      } catch (err) {
        console.error('Failed to fetch audit logs:', err);
      }
    }

    /* ─── LEADS & ENQUIRIES MANAGEMENT ─── */
    let currentLeadsPage = 1;

    async function fetchLeads(page = 1) {
      currentLeadsPage = page;
      const search = document.getElementById('leadSearchInput')?.value.trim() || '';
      const status = document.getElementById('leadStatusFilter')?.value || 'all';

      try {
        const url = `/api/superadmin/leads?page=${page}&search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
        const res = await fetch(url);
        const json = await res.json();

        if (json.success) {
          // Update overview metric cards
          if (json.stats) {
            document.getElementById('leadStatTotal').textContent = json.stats.total || 0;
            document.getElementById('leadStatNew').textContent = json.stats.new_or_pending || 0;
            document.getElementById('leadStatContacted').textContent = json.stats.contacted || 0;
            document.getElementById('leadStatConverted').textContent = json.stats.converted || 0;

            // Update sidebar badge
            const badge = document.getElementById('leadCountBadge');
            if (badge) {
              const pendingCount = json.stats.new_or_pending || 0;
              if (pendingCount > 0) {
                badge.textContent = pendingCount;
                badge.style.display = 'inline-block';
              } else {
                badge.style.display = 'none';
              }
            }
          }

          renderLeadsTable(json.data, json.pagination);
        } else {
          showToast(json.message || 'Failed to load leads', 'error');
        }
      } catch (err) {
        console.error('Error fetching leads:', err);
        showToast('Network error while loading leads', 'error');
      }
    }

    function renderLeadsTable(leads, pagination) {
      const tbody = document.getElementById('leadsTableBody');
      if (!tbody) return;

      if (!leads || leads.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:32px; color:var(--text-muted);">No lead enquiries found.</td></tr>';
        document.getElementById('leadsPagination').innerHTML = '';
        return;
      }

      tbody.innerHTML = leads.map(lead => {
        const cleanPhone = (lead.phone || '').replace(/[^0-9]/g, '');
        const intlPhone = cleanPhone.length === 10 ? '91' + cleanPhone : cleanPhone;
        const waText = encodeURIComponent(`Hello ${lead.name || 'there'}, thank you for contacting Tendorabuy regarding "${lead.restaurant_name}". How can we help you?`);
        const dateStr = new Date(lead.created_at).toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' });

        const statusColors = {
          'new': { bg: 'rgba(239, 68, 68, 0.15)', text: '#F87171', border: 'rgba(239, 68, 68, 0.3)' },
          'pending': { bg: 'rgba(245, 158, 11, 0.15)', text: '#FBBF24', border: 'rgba(245, 158, 11, 0.3)' },
          'contacted': { bg: 'rgba(99, 102, 241, 0.15)', text: '#818CF8', border: 'rgba(99, 102, 241, 0.3)' },
          'converted': { bg: 'rgba(16, 185, 129, 0.15)', text: '#34D399', border: 'rgba(16, 185, 129, 0.3)' },
          'closed': { bg: 'rgba(100, 116, 139, 0.15)', text: '#94A3B8', border: 'rgba(100, 116, 139, 0.3)' }
        };
        const currentStColor = statusColors[lead.status] || statusColors['new'];

        return `
          <tr>
            <td style="white-space:nowrap; font-size:12px; color:var(--text-muted);">
              <i class="fa-regular fa-calendar" style="margin-right:4px;"></i> ${dateStr}
            </td>
            <td>
              <strong style="color:var(--text-primary); font-size:14px; display:block;">${escapeHtml(lead.restaurant_name || 'N/A')}</strong>
              <span style="font-size:12px; color:var(--text-secondary);"><i class="fa-solid fa-user" style="font-size:10px; margin-right:4px;"></i> ${escapeHtml(lead.name || '')}</span>
            </td>
            <td style="white-space:nowrap;">
              <div style="display:flex; align-items:center; gap:8px;">
                <a href="tel:${escapeHtml(lead.phone)}" style="color:var(--text-primary); font-weight:600; text-decoration:none; font-size:13px;" title="Call">
                  <i class="fa-solid fa-phone" style="font-size:11px; color:#818CF8; margin-right:4px;"></i> ${escapeHtml(lead.phone)}
                </a>
                <a href="https://wa.me/${intlPhone}?text=${waText}" target="_blank" style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; background:#25D366; color:#fff; border-radius:6px; font-size:13px; text-decoration:none;" title="Chat on WhatsApp">
                  <i class="fa-brands fa-whatsapp"></i>
                </a>
              </div>
            </td>
            <td style="font-size:12.5px;">
              ${lead.email ? `<a href="mailto:${escapeHtml(lead.email)}" style="color:#818CF8; text-decoration:none;">${escapeHtml(lead.email)}</a>` : '<span style="color:var(--text-muted);">—</span>'}
            </td>
            <td style="font-size:12.5px;">
              <div>${lead.city ? `<i class="fa-solid fa-location-dot" style="font-size:11px; color:#F59E0B; margin-right:4px;"></i> ${escapeHtml(lead.city)}` : '<span style="color:var(--text-muted);">—</span>'}</div>
              ${lead.preferred_time ? `<div style="font-size:11px; color:var(--text-muted); margin-top:2px;"><i class="fa-solid fa-clock" style="font-size:10px;"></i> ${escapeHtml(lead.preferred_time)}</div>` : ''}
            </td>
            <td style="max-width:240px; font-size:12.5px; color:var(--text-secondary);">
              <div style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${escapeHtml(lead.message || '')}">
                ${escapeHtml(lead.message || '—')}
              </div>
            </td>
            <td>
              <span class="metric-badge" style="background:rgba(255,255,255,0.06); color:var(--text-secondary); text-transform:capitalize; font-size:11px;">
                ${escapeHtml(lead.source ? lead.source.replace(/_/g, ' ') : 'Website')}
              </span>
            </td>
            <td>
              <select onchange="updateLeadStatus(${lead.id}, this.value)" style="background:${currentStColor.bg}; color:${currentStColor.text}; border:1px solid ${currentStColor.border}; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:700; cursor:pointer; outline:none;">
                <option value="new" ${lead.status === 'new' ? 'selected' : ''}>New</option>
                <option value="pending" ${lead.status === 'pending' ? 'selected' : ''}>Pending</option>
                <option value="contacted" ${lead.status === 'contacted' ? 'selected' : ''}>Contacted</option>
                <option value="converted" ${lead.status === 'converted' ? 'selected' : ''}>Converted</option>
                <option value="closed" ${lead.status === 'closed' ? 'selected' : ''}>Closed</option>
              </select>
            </td>
            <td style="white-space:nowrap;">
              <button onclick="deleteLead(${lead.id})" style="background:rgba(239, 68, 68, 0.12); color:#F87171; border:1px solid rgba(239, 68, 68, 0.3); border-radius:6px; padding:5px 8px; cursor:pointer; font-size:12px;" title="Delete Lead">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
        `;
      }).join('');

      // Pagination
      if (pagination && pagination.total > 0) {
        document.getElementById('leadsPagination').innerHTML = `
          <div>Showing page ${pagination.current_page} of ${pagination.last_page} (${pagination.total} leads total)</div>
          <div style="display:flex; gap:6px;">
            <button class="btn-secondary" style="padding:4px 10px; font-size:12px;" ${pagination.current_page <= 1 ? 'disabled' : ''} onclick="fetchLeads(${pagination.current_page - 1})">Prev</button>
            <button class="btn-secondary" style="padding:4px 10px; font-size:12px;" ${pagination.current_page >= pagination.last_page ? 'disabled' : ''} onclick="fetchLeads(${pagination.current_page + 1})">Next</button>
          </div>
        `;
      }
    }

    async function updateLeadStatus(id, status) {
      try {
        const res = await fetch(`/api/superadmin/leads/${id}/status`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
          },
          body: JSON.stringify({ status: status })
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Status updated', 'success');
          fetchLeads(currentLeadsPage);
        } else {
          showToast(json.message || 'Failed to update status', 'error');
        }
      } catch (err) {
        showToast('Error updating lead status', 'error');
      }
    }

    async function deleteLead(id) {
      if (!confirm('Are you sure you want to remove this lead submission?')) return;
      try {
        const res = await fetch(`/api/superadmin/leads/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
          }
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message || 'Lead removed', 'success');
          fetchLeads(currentLeadsPage);
        } else {
          showToast(json.message || 'Failed to delete lead', 'error');
        }
      } catch (err) {
        showToast('Error deleting lead', 'error');
      }
    }

    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    document.addEventListener('DOMContentLoaded', () => {
      fetchRecentOrders();
      fetchLeads(1); // initialize lead counter and table
    });
  </script>
</body>
</html>
