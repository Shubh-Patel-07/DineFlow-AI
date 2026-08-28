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
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }

    .metric-card {
      background: var(--bg-card);
      backdrop-filter: blur(12px);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 22px;
      display: flex;
      flex-direction: column;
      gap: 14px;
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
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    .metric-label {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-secondary);
    }

    .metric-value {
      font-family: 'Outfit', sans-serif;
      font-size: 28px;
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

    .status-badge {
      padding: 4px 10px;
      border-radius: 8px;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .status-active { background: rgba(16, 185, 129, 0.15); color: #34D399; }
    .status-suspended { background: rgba(239, 68, 68, 0.15); color: #F87171; }
    .status-trial { background: rgba(6, 182, 212, 0.15); color: #22D3EE; }

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
      max-width: 550px;
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

    .form-input, .form-select {
      background: var(--bg-primary);
      border: 1px solid var(--border-color);
      border-radius: 10px;
      padding: 10px 14px;
      color: var(--text-primary);
      font-size: 13px;
      outline: none;
    }

    .form-input:focus, .form-select:focus {
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

    /* ─── RESPONSIVE BREAKPOINTS ─────────────────────────── */
    @media (max-width: 1024px) {
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .main-wrapper {
        margin-left: 0;
      }
      .menu-toggle {
        display: block;
      }
      .content-area {
        padding: 20px;
      }
    }

    @media (max-width: 640px) {
      .topbar {
        padding: 0 16px;
      }
      .content-area {
        padding: 16px;
        gap: 20px;
      }
      .metrics-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

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
      <a class="nav-item" onclick="switchSection('analytics', this)">
        <i class="fa-solid fa-chart-line"></i>
        <span>Global Analytics</span>
      </a>
      <a class="nav-item" onclick="switchSection('audit-logs', this)">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>Audit Logs</span>
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
            <div class="metric-value" id="kpi-total-restaurants">{{ $stats['total_restaurants'] ?? 0 }}</div>
            <div>
              <span class="metric-badge" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">
                <i class="fa-solid fa-circle-check"></i> {{ $stats['active_restaurants'] ?? 0 }} Active
              </span>
              <span class="metric-badge" style="background: rgba(239, 68, 68, 0.15); color: #F87171; margin-left: 4px;">
                {{ $stats['suspended_restaurants'] ?? 0 }} Suspended
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
              <span class="metric-label">Total Orders</span>
              <div class="metric-icon" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24;">
                <i class="fa-solid fa-receipt"></i>
              </div>
            </div>
            <div class="metric-value">{{ number_format($stats['orders']['total'] ?? 0) }}</div>
            <div>
              <span class="metric-badge" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24;">
                Today: {{ $stats['orders']['today'] ?? 0 }} orders
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
            <div>
              <span class="metric-badge" style="background: rgba(99, 102, 241, 0.15); color: #818CF8;">
                {{ $stats['plans']['pro'] ?? 0 }} Pro
              </span>
              <span class="metric-badge" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24; margin-left: 4px;">
                {{ $stats['plans']['enterprise'] ?? 0 }} Enterprise
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
                  <th>Orders</th>
                  <th>Revenue</th>
                </tr>
              </thead>
              <tbody>
                @forelse($stats['top_restaurants'] ?? [] as $top)
                <tr>
                  <td style="font-weight: 700; color: var(--text-primary);">{{ $top['name'] }}</td>
                  <td><span class="plan-badge plan-{{ $top['plan_type'] }}">{{ $top['plan_type'] }}</span></td>
                  <td>{{ $top['orders_count'] }}</td>
                  <td style="font-weight: 700; color: var(--success);">₹{{ number_format($top['revenue'], 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align: center; color: var(--text-muted);">No restaurant data yet.</td></tr>
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
              </select>
              <select id="statusFilter" class="filter-select" onchange="fetchRestaurants()">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
                <option value="trial">Trial</option>
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
                  <th>Status</th>
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
              <option value="pro">Pro Tier (100 tables, 50 staff, 1000 items)</option>
              <option value="enterprise">Enterprise Tier</option>
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

  <!-- ── MODAL: CHANGE PLAN ── -->
  <div class="modal-overlay" id="planModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Change SaaS Plan</h3>
        <button class="modal-close" onclick="closeModal('planModal')">&times;</button>
      </div>
      <form id="changePlanForm" onsubmit="submitChangePlan(event)">
        <input type="hidden" id="planModalRestaurantId">
        <div class="modal-body">
          <p id="planModalRestaurantName" style="font-weight: 700; font-size: 14px; margin-bottom: 8px;"></p>
          <div class="form-group">
            <label>Select New Plan</label>
            <select id="planModalSelect" class="form-select">
              <option value="free">Free Plan (10 tables, 5 staff, 100 menu items, 500 orders/mo)</option>
              <option value="pro">Pro Plan (100 tables, 50 staff, 1000 menu items, 10,000 orders/mo)</option>
              <option value="enterprise">Enterprise Plan (Unlimited)</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeModal('planModal')">Cancel</button>
          <button type="submit" class="btn-primary">Update Plan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ── MODAL: CUSTOM LIMITS ── -->
  <div class="modal-overlay" id="limitsModal">
    <div class="modal-card">
      <div class="modal-header">
        <h3>Customize Plan Quotas</h3>
        <button class="modal-close" onclick="closeModal('limitsModal')">&times;</button>
      </div>
      <form id="customLimitsForm" onsubmit="submitCustomLimits(event)">
        <input type="hidden" id="limitsModalRestaurantId">
        <div class="modal-body">
          <div class="form-group">
            <label>Max Tables</label>
            <input type="number" id="limitTables" class="form-input" min="1" max="1000" required>
          </div>
          <div class="form-group">
            <label>Max Staff Accounts</label>
            <input type="number" id="limitStaff" class="form-input" min="1" max="500" required>
          </div>
          <div class="form-group">
            <label>Max Menu Items</label>
            <input type="number" id="limitMenuItems" class="form-input" min="1" max="5000" required>
          </div>
          <div class="form-group">
            <label>Max Monthly Orders</label>
            <input type="number" id="limitMonthlyOrders" class="form-input" min="10" max="100000" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary" onclick="closeModal('limitsModal')">Cancel</button>
          <button type="submit" class="btn-primary">Save Custom Quotas</button>
        </div>
      </form>
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
      document.getElementById('sidebar').classList.toggle('open');
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
      document.querySelectorAll('.sidebar-nav .nav-item').forEach(el => el.classList.remove('active'));
      if (navEl) navEl.classList.add('active');

      document.querySelectorAll('main > section').forEach(sec => sec.style.display = 'none');
      const targetSec = document.getElementById(`section-${sectionId}`);
      if (targetSec) targetSec.style.display = 'block';

      const titles = {
        'dashboard': 'Platform Overview',
        'restaurants': 'Restaurants Management',
        'analytics': 'Global Analytics',
        'audit-logs': 'System Audit Logs'
      };
      document.getElementById('currentSectionTitle').textContent = titles[sectionId] || 'Super Admin';

      if (sectionId === 'restaurants') fetchRestaurants();
      if (sectionId === 'audit-logs') fetchAuditLogs();
      if (sectionId === 'dashboard') fetchRecentOrders();

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
      const search = document.getElementById('restaurantSearch').value;
      const plan = document.getElementById('planFilter').value;
      const status = document.getElementById('statusFilter').value;

      const url = new URL('/api/superadmin/restaurants', window.location.origin);
      if (search) url.searchParams.set('search', search);
      if (plan) url.searchParams.set('plan', plan);
      if (status) url.searchParams.set('status', status);

      try {
        const res = await fetch(url.toString());
        const json = await res.json();
        const tbody = document.getElementById('restaurantsTableBody');

        if (json.success && json.data.length > 0) {
          tbody.innerHTML = json.data.map(r => `
            <tr>
              <td>#${r.id}</td>
              <td>
                <strong style="color: var(--text-primary); font-size: 14px;">${r.name}</strong>
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
              </td>
              <td><strong>${r.orders_count}</strong></td>
              <td style="color: var(--success); font-weight: 700;">₹${r.total_revenue.toLocaleString('en-IN')}</td>
              <td>
                <div class="actions-group">
                  ${r.subscription_status === 'suspended' ? `
                    <button class="action-btn" title="Activate Restaurant" onclick="toggleStatus(${r.id}, 'activate')">
                      <i class="fa-solid fa-play" style="color: var(--success);"></i>
                    </button>
                  ` : `
                    <button class="action-btn" title="Suspend Restaurant" onclick="toggleStatus(${r.id}, 'suspend')">
                      <i class="fa-solid fa-pause" style="color: var(--danger);"></i>
                    </button>
                  `}
                  <button class="action-btn" title="Change Plan" onclick="openPlanModal(${r.id}, '${r.name}', '${r.plan_type}')">
                    <i class="fa-solid fa-layer-group"></i>
                  </button>
                  <button class="action-btn" title="Custom Quota Limits" onclick="openLimitsModal(${r.id}, ${JSON.stringify(r.plan_limits).replace(/"/g, '&quot;')})">
                    <i class="fa-solid fa-sliders"></i>
                  </button>
                </div>
              </td>
            </tr>
          `).join('');
        } else {
          tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">No restaurants found.</td></tr>';
        }
      } catch (err) {
        console.error('Failed to fetch restaurants:', err);
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
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
          },
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

    async function toggleStatus(restaurantId, action) {
      if (!confirm(`Are you sure you want to ${action} this restaurant?`)) return;
      try {
        const res = await fetch(`/api/superadmin/restaurants/${restaurantId}/${action}`, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
          }
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message);
          fetchRestaurants();
        } else {
          showToast(json.message || 'Failed', 'error');
        }
      } catch (err) {
        showToast('Operation failed', 'error');
      }
    }

    function openPlanModal(id, name, currentPlan) {
      document.getElementById('planModalRestaurantId').value = id;
      document.getElementById('planModalRestaurantName').textContent = `Restaurant: ${name}`;
      document.getElementById('planModalSelect').value = currentPlan || 'free';
      openModal('planModal');
    }

    async function submitChangePlan(e) {
      e.preventDefault();
      const id = document.getElementById('planModalRestaurantId').value;
      const plan = document.getElementById('planModalSelect').value;

      try {
        const res = await fetch(`/api/superadmin/restaurants/${id}/plan`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
          },
          body: JSON.stringify({ plan_type: plan })
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message);
          closeModal('planModal');
          fetchRestaurants();
        } else {
          showToast(json.message || 'Failed', 'error');
        }
      } catch (err) {
        showToast('Plan update failed', 'error');
      }
    }

    function openLimitsModal(id, limits) {
      document.getElementById('limitsModalRestaurantId').value = id;
      document.getElementById('limitTables').value = limits?.tables || 10;
      document.getElementById('limitStaff').value = limits?.staff || 5;
      document.getElementById('limitMenuItems').value = limits?.menu_items || 100;
      document.getElementById('limitMonthlyOrders').value = limits?.monthly_orders || 500;
      openModal('limitsModal');
    }

    async function submitCustomLimits(e) {
      e.preventDefault();
      const id = document.getElementById('limitsModalRestaurantId').value;
      const payload = {
        tables: parseInt(document.getElementById('limitTables').value),
        staff: parseInt(document.getElementById('limitStaff').value),
        menu_items: parseInt(document.getElementById('limitMenuItems').value),
        monthly_orders: parseInt(document.getElementById('limitMonthlyOrders').value),
      };

      try {
        const res = await fetch(`/api/superadmin/restaurants/${id}/limits`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN
          },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.success) {
          showToast(json.message);
          closeModal('limitsModal');
          fetchRestaurants();
        } else {
          showToast(json.message || 'Failed', 'error');
        }
      } catch (err) {
        showToast('Quota update failed', 'error');
      }
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

    document.addEventListener('DOMContentLoaded', () => {
      fetchRecentOrders();
    });
  </script>
</body>
</html>
