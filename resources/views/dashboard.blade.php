<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Overview — DineFlow AI POS</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <script>window.AUTH_USER_ROLE = "{{ Auth::user()?->role ?? Auth::user()?->username ?? session('active_role') ?? '' }}";</script>
  <script src="/frontend/admin/utils.js"></script>

  <!-- Google Typography: Outfit & Inter & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    /* ═══════════════════════════════════════════════════════════════
       DASHDINE DESIGN SYSTEM TOKENS (LIGHT & DARK THEMES)
       ═══════════════════════════════════════════════════════════════ */
    :root {
      /* DashDine Signature Ember Orange & Accents */
      --primary:         #FF6B00;
      --primary-hover:   #FA5A2A;
      --primary-gradient: linear-gradient(135deg, #FF6B00 0%, #FA5A2A 100%);
      --primary-light:   rgba(255, 107, 0, 0.12);
      --primary-glow:    rgba(255, 107, 0, 0.25);
      
      --success:         #10B981;
      --success-light:   rgba(16, 185, 129, 0.12);
      --danger:          #EF4444;
      --danger-light:    rgba(239, 68, 68, 0.12);
      --warning:         #F59E0B;
      --warning-light:   rgba(245, 158, 11, 0.12);
      --info:            #06B6D4;
      --info-light:      rgba(6, 182, 212, 0.12);
      --purple:          #8B5CF6;
      --purple-light:    rgba(139, 92, 246, 0.12);

      /* DashDine Sidebar & Layout (Light Mode Default) */
      --sidebar-bg:      #121624; /* Deep Midnight Slate */
      --sidebar-text:    #94A3B8;
      --sidebar-text-active: #FFFFFF;
      --sidebar-hover:   rgba(255, 255, 255, 0.05);
      --sidebar-border:  rgba(255, 255, 255, 0.06);

      --bg:              #F8FAFC; /* Warm/Crisp Canvas */
      --card:            #FFFFFF;
      --card-subtle:     #F1F5F9;
      --border:          #E2E8F0;
      --text-primary:    #0F172A;
      --text-secondary:  #475569;
      --text-muted:      #94A3B8;

      --shadow-sm:       0 1px 3px rgba(0, 0, 0, 0.04);
      --shadow-card:     0 4px 20px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
      --shadow-lg:       0 12px 32px rgba(0, 0, 0, 0.08);

      --radius-sm:       10px;
      --radius-md:       14px;
      --radius-lg:       20px;
    }

    /* ─── FULL DARK THEME ENGINE ─── */
    body.dark-theme {
      --bg:              #0A0D17;
      --card:            #121624;
      --card-subtle:     #181E32;
      --border:          rgba(255, 255, 255, 0.08);
      --text-primary:    #F8FAFC;
      --text-secondary:  #94A3B8;
      --text-muted:      #64748B;
      --shadow-card:     0 8px 24px rgba(0, 0, 0, 0.35);
    }

    body.dark-theme .dashdine-card {
      background: var(--card) !important;
      border-color: var(--border) !important;
    }
    body.dark-theme .topbar {
      background: var(--card) !important;
      border-bottom-color: var(--border) !important;
    }
    body.dark-theme .dashdine-search input {
      background: var(--card-subtle) !important;
      border-color: var(--border) !important;
      color: #fff !important;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text-primary);
      display: flex;
      height: 100vh;
      overflow: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* ═══════════════════════════════════════════════════════════════
       DASHDINE SIDEBAR (260px)
       ═══════════════════════════════════════════════════════════════ */
    .sidebar {
      width: 260px;
      min-width: 260px;
      background: var(--sidebar-bg);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      border-right: 1px solid var(--sidebar-border);
      transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1), min-width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: 100;
    }
    .sidebar.collapsed { width: 72px; min-width: 72px; }

    /* Sidebar Brand */
    .sidebar-brand {
      height: 72px;
      padding: 0 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid var(--sidebar-border);
      flex-shrink: 0;
    }
    .brand-icon-box {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: var(--primary-gradient);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 19px;
      box-shadow: 0 4px 14px var(--primary-glow);
      flex-shrink: 0;
    }
    .brand-text-box h2 {
      font-family: 'Outfit', sans-serif;
      font-size: 17px;
      font-weight: 800;
      color: #FFFFFF;
      letter-spacing: -0.2px;
      margin: 0;
      line-height: 1.2;
    }
    .brand-text-box span {
      font-size: 10px;
      font-weight: 700;
      color: #94A3B8;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      display: block;
    }
    .sidebar.collapsed .brand-text-box { display: none; }

    .sidebar-toggle-btn {
      margin-left: auto;
      background: rgba(255, 255, 255, 0.08);
      border: none;
      width: 28px;
      height: 28px;
      border-radius: 8px;
      color: #94A3B8;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 12px;
      transition: all 0.2s;
    }
    .sidebar-toggle-btn:hover { background: rgba(255, 255, 255, 0.15); color: #fff; }
    .sidebar.collapsed .sidebar-toggle-btn { margin: 0 auto; }
    .sidebar.collapsed .sidebar-toggle-btn i { transform: rotate(180deg); }

    /* Navigation Sections */
    .sidebar-nav {
      flex: 1;
      overflow-y: auto;
      padding: 18px 14px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .nav-group-title {
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #64748B;
      padding: 0 8px;
      margin-bottom: 6px;
    }
    .sidebar.collapsed .nav-group-title { display: none; }

    .nav-link-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      border-radius: 12px;
      color: var(--sidebar-text);
      font-size: 13.5px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s;
      position: relative;
    }
    .nav-link-item i { font-size: 16px; width: 20px; text-align: center; }
    .nav-link-item:hover {
      background: var(--sidebar-hover);
      color: #FFFFFF;
    }
    .nav-link-item.active {
      background: var(--primary-gradient);
      color: #FFFFFF;
      font-weight: 700;
      box-shadow: 0 4px 16px var(--primary-glow);
    }
    .nav-badge-count {
      margin-left: auto;
      background: #FA5A2A;
      color: #fff;
      font-size: 10.5px;
      font-weight: 800;
      padding: 2px 8px;
      border-radius: 20px;
    }
    .nav-link-item.active .nav-badge-count {
      background: #FFFFFF;
      color: #FF6B00;
    }
    .sidebar.collapsed .nav-label,
    .sidebar.collapsed .nav-badge-count { display: none; }

    /* Bottom Live Status Widget */
    .sidebar-status-widget {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid var(--sidebar-border);
      border-radius: 16px;
      padding: 14px;
      margin: 0 14px 14px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .sidebar.collapsed .sidebar-status-widget { display: none; }
    .status-widget-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 12px;
    }
    .status-widget-row span:first-child { color: #94A3B8; }
    .status-widget-row .val { color: #FFFFFF; font-weight: 700; font-family: 'JetBrains Mono', monospace; }
    .status-pill-open {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      color: #10B981;
      font-weight: 700;
      font-size: 11px;
    }
    .status-pill-open i { font-size: 8px; animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 0.4; } 50% { opacity: 1; } 100% { opacity: 0.4; } }

    /* User Card Footer */
    .sidebar-user-footer {
      border-top: 1px solid var(--sidebar-border);
      padding: 14px 16px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .user-avatar-circle {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: var(--primary-gradient);
      color: #fff;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .user-meta-info { overflow: hidden; white-space: nowrap; flex: 1; }
    .user-meta-info h4 { font-size: 13px; font-weight: 700; color: #FFFFFF; margin: 0; line-height: 1.2; }
    .user-meta-info p { font-size: 11px; color: #94A3B8; margin: 0; text-transform: capitalize; }
    .sidebar.collapsed .user-meta-info { display: none; }

    /* ═══════════════════════════════════════════════════════════════
       MAIN VIEWPORT & DASHDINE TOPBAR
       ═══════════════════════════════════════════════════════════════ */
    .main-viewport {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      overflow-x: hidden;
      min-width: 0;
      background: var(--bg);
    }

    .topbar {
      height: 72px;
      background: var(--card);
      border-bottom: 1px solid var(--border);
      padding: 0 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      flex-shrink: 0;
      box-shadow: var(--shadow-sm);
    }
    .topbar-date-title { display: flex; flex-direction: column; }
    .topbar-date {
      font-size: 11px;
      font-weight: 700;
      color: var(--text-muted);
      letter-spacing: 0.8px;
      text-transform: uppercase;
    }
    .topbar-title {
      font-family: 'Outfit', sans-serif;
      font-size: 21px;
      font-weight: 800;
      color: var(--text-primary);
      letter-spacing: -0.3px;
      margin: 0;
    }

    /* DashDine Search Input */
    .dashdine-search {
      width: 360px;
      position: relative;
    }
    .dashdine-search i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 14px;
    }
    .dashdine-search input {
      width: 100%;
      height: 42px;
      background: var(--card-subtle);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 0 16px 0 38px;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-primary);
      outline: none;
      transition: all 0.2s;
    }
    .dashdine-search input:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px var(--primary-light);
    }

    /* Topbar Controls */
    .topbar-actions-suite {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .shift-status-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(16, 185, 129, 0.1);
      border: 1px solid rgba(16, 185, 129, 0.25);
      color: #10B981;
      padding: 6px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 700;
    }
    .shift-status-pill i { font-size: 8px; }

    .topbar-icon-btn {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      background: var(--card-subtle);
      border: 1px solid var(--border);
      color: var(--text-secondary);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      transition: all 0.18s;
    }
    .topbar-icon-btn:hover {
      background: var(--primary-light);
      color: var(--primary);
      border-color: var(--primary);
    }
    .notif-badge-dot {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #EF4444;
      border: 2px solid var(--card);
    }

    .btn-new-pos-bill {
      height: 42px;
      padding: 0 18px;
      background: var(--primary-gradient);
      color: #FFFFFF;
      font-weight: 700;
      font-size: 13px;
      border-radius: 12px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 14px var(--primary-glow);
      transition: all 0.2s;
    }
    .btn-new-pos-bill:hover { transform: translateY(-1px); box-shadow: 0 6px 18px var(--primary-glow); }

    /* ═══════════════════════════════════════════════════════════════
       DASHDINE CONTENT WORKSPACE
       ═══════════════════════════════════════════════════════════════ */
    .dashboard-workspace {
      padding: 26px 28px 40px;
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    /* ─── ROW 1: 5 DASHDINE KPI CARDS ─── */
    .dashdine-kpi-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 18px;
    }
    .dashdine-kpi-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 20px 22px;
      box-shadow: var(--shadow-card);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .dashdine-kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }
    .kpi-card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 12px;
    }
    .kpi-title {
      font-size: 11px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }
    .kpi-icon-bubble {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
    }
    .kpi-icon-bubble.orange { background: rgba(255, 107, 0, 0.12); color: #FF6B00; }
    .kpi-icon-bubble.green  { background: rgba(16, 185, 129, 0.12); color: #10B981; }
    .kpi-icon-bubble.amber  { background: rgba(245, 158, 11, 0.12); color: #F59E0B; }
    .kpi-icon-bubble.red    { background: rgba(239, 68, 68, 0.12); color: #EF4444; }
    .kpi-icon-bubble.cyan   { background: rgba(6, 182, 212, 0.12); color: #06B6D4; }

    .kpi-value {
      font-family: 'Outfit', sans-serif;
      font-size: 30px;
      font-weight: 800;
      color: var(--text-primary);
      letter-spacing: -0.5px;
      line-height: 1.1;
      margin-bottom: 8px;
    }
    .kpi-trend {
      font-size: 12px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .kpi-trend.positive { color: #10B981; }
    .kpi-trend.negative { color: #EF4444; }
    .kpi-trend.neutral  { color: var(--text-muted); }
    .kpi-trend-sub { color: var(--text-muted); font-weight: 500; font-size: 11.5px; margin-left: 2px; }

    /* Decorative bottom-right watermark */
    .kpi-watermark-shape {
      position: absolute;
      bottom: -15px;
      right: -15px;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: rgba(255, 107, 0, 0.03);
      pointer-events: none;
    }

    /* ─── ROW 2: HOURLY REVENUE CHART & FLOOR MAP ─── */
    .dashdine-middle-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
    }
    .dashdine-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 24px;
      box-shadow: var(--shadow-card);
      display: flex;
      flex-direction: column;
    }
    .card-title-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .card-title-bar h3 {
      font-family: 'Outfit', sans-serif;
      font-size: 17px;
      font-weight: 800;
      color: var(--text-primary);
      margin: 0;
    }
    .card-subtitle {
      font-size: 12px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    /* Time Switcher Pills */
    .chart-time-pills {
      display: inline-flex;
      background: var(--card-subtle);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 3px;
    }
    .time-pill-btn {
      padding: 4px 12px;
      border-radius: 8px;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-secondary);
      border: none;
      background: transparent;
      cursor: pointer;
      transition: all 0.15s;
    }
    .time-pill-btn.active {
      background: var(--card);
      color: var(--primary);
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    /* Chart Canvas Container */
    .chart-container-box {
      flex: 1;
      min-height: 250px;
      position: relative;
    }

    /* ─── LIVE FLOOR MAP WIDGET ─── */
    .floor-map-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin: 10px 0 16px;
    }
    .floor-table-node {
      background: var(--card-subtle);
      border: 1.5px solid var(--border);
      border-radius: 12px;
      padding: 12px 6px;
      text-align: center;
      cursor: pointer;
      transition: all 0.18s;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 2px;
    }
    .floor-table-node:hover {
      transform: scale(1.04);
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .table-id-txt {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 15px;
      color: var(--text-primary);
      line-height: 1.1;
    }
    .table-status-tag {
      font-size: 9px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Floor Table States (Exact DashDine Colors) */
    .floor-table-node.occupied {
      background: rgba(255, 107, 0, 0.08);
      border-color: rgba(255, 107, 0, 0.4);
    }
    .floor-table-node.occupied .table-id-txt { color: #FF6B00; }
    .floor-table-node.occupied .table-status-tag { color: #FA5A2A; }

    .floor-table-node.available {
      background: rgba(16, 185, 129, 0.08);
      border-color: rgba(16, 185, 129, 0.4);
    }
    .floor-table-node.available .table-id-txt { color: #10B981; }
    .floor-table-node.available .table-status-tag { color: #059669; }

    .floor-table-node.reserved {
      background: rgba(245, 158, 11, 0.08);
      border-color: rgba(245, 158, 11, 0.4);
    }
    .floor-table-node.reserved .table-id-txt { color: #F59E0B; }
    .floor-table-node.reserved .table-status-tag { color: #D97706; }

    .floor-table-node.cleaning {
      background: rgba(148, 163, 184, 0.1);
      border-color: rgba(148, 163, 184, 0.3);
    }
    .floor-table-node.cleaning .table-id-txt { color: #64748B; }
    .floor-table-node.cleaning .table-status-tag { color: #94A3B8; }

    .floor-legend-bar {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      font-size: 11px;
      color: var(--text-muted);
      padding-top: 10px;
      border-top: 1px solid var(--border);
    }
    .floor-legend-dot {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      margin-right: 4px;
    }
    .manage-link-btn {
      font-size: 12px;
      font-weight: 700;
      color: var(--primary);
      text-decoration: none;
      padding: 4px 10px;
      border-radius: 8px;
      background: var(--primary-light);
    }

    /* ─── ROW 3: RECENT ORDERS & TOP DISHES ─── */
    .dashdine-bottom-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 20px;
    }

    /* Order Filter Tabs */
    .order-filter-pills {
      display: flex;
      gap: 6px;
      background: var(--card-subtle);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 3px;
    }
    .order-filter-btn {
      padding: 5px 12px;
      border-radius: 8px;
      font-size: 11.5px;
      font-weight: 700;
      color: var(--text-secondary);
      border: none;
      background: transparent;
      cursor: pointer;
    }
    .order-filter-btn.active {
      background: var(--primary-gradient);
      color: #fff;
    }

    /* Recent Orders Modern Table */
    .dashdine-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    .dashdine-table th {
      font-size: 10.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--text-muted);
      padding: 10px 14px;
      text-align: left;
      border-bottom: 1px solid var(--border);
      background: var(--card-subtle);
    }
    .dashdine-table th:first-child { border-radius: 10px 0 0 10px; }
    .dashdine-table th:last-child  { border-radius: 0 10px 10px 0; }
    .dashdine-table td {
      padding: 14px;
      font-size: 13px;
      color: var(--text-primary);
      border-bottom: 1px solid var(--border);
    }
    .dashdine-table tr:hover td { background: var(--primary-light); }
    .order-id-code {
      font-family: 'JetBrains Mono', monospace;
      font-weight: 700;
      color: var(--primary);
    }
    .table-pill-badge {
      display: inline-block;
      padding: 3px 8px;
      background: var(--card-subtle);
      border: 1px solid var(--border);
      border-radius: 6px;
      font-weight: 700;
      font-size: 11.5px;
    }

    /* Order Status Badges */
    .status-badge-served {
      background: rgba(16, 185, 129, 0.12);
      color: #10B981;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .status-badge-cooking {
      background: rgba(255, 107, 0, 0.12);
      color: #FF6B00;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .status-badge-pending {
      background: rgba(245, 158, 11, 0.12);
      color: #F59E0B;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    /* Top Dishes Ranked List */
    .top-dishes-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-top: 8px;
    }
    .dish-rank-item {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .dish-rank-num {
      font-size: 14px;
      font-weight: 800;
      color: var(--text-muted);
      width: 16px;
    }
    .dish-icon-box {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      background: var(--card-subtle);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }
    .dish-details-col { flex: 1; }
    .dish-details-col h5 { font-size: 13px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px; }
    .dish-details-col span { font-size: 11.5px; color: var(--text-muted); }
    .dish-progress-bar {
      width: 90px;
      height: 6px;
      background: var(--card-subtle);
      border-radius: 10px;
      overflow: hidden;
    }
    .dish-progress-fill {
      height: 100%;
      border-radius: 10px;
      background: var(--primary-gradient);
    }

    /* Staff on Duty Panel */
    .staff-duty-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-top: 14px;
    }
    .staff-duty-card {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 12px;
      background: var(--card-subtle);
      border: 1px solid var(--border);
      border-radius: 12px;
    }
    .staff-duty-left { display: flex; align-items: center; gap: 10px; }
    .staff-avatar-badge {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      color: #fff;
      font-size: 12px;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .staff-duty-info h6 { font-size: 12.5px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .staff-duty-info span { font-size: 11px; color: var(--text-muted); }
    .staff-metric-tag { font-family: 'JetBrains Mono', monospace; font-size: 14px; font-weight: 800; color: var(--primary); }

    /* Responsive Engine */
    @media (max-width: 1200px) {
      .dashdine-kpi-grid { grid-template-columns: repeat(3, 1fr); }
      .dashdine-middle-grid,
      .dashdine-bottom-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
      .sidebar { position: fixed; inset: 0 auto 0 0; z-index: 999; transform: translateX(-100%); }
      .sidebar.mobile-open { transform: translateX(0); }
      .dashdine-kpi-grid { grid-template-columns: 1fr 1fr; }
      .dashdine-search { display: none; }
    }
  </style>
</head>
<body>

@include('partials.impersonate-banner')

@if(Auth::user()?->restaurant?->is_demo)
<div id="demoSandboxBanner" style="background: linear-gradient(90deg, #F59E0B 0%, #D97706 100%); color: #000; padding: 7px 20px; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; z-index: 9999;">
  <div style="display:flex; align-items:center; gap:8px;">
    <span style="background:#000; color:#FBBF24; padding:2px 7px; border-radius:4px; font-size:10px; font-weight:900;">DEMO MODE</span>
    <span>Your changes are temporary and strictly isolated in your personal sandbox. Production data is safe.</span>
  </div>
  <div style="display:flex; align-items:center; gap:8px;">
    <a href="{{ route('demo.reset') }}" onclick="return confirm('Reset demo sandbox to fresh sample state?');" style="background: rgba(0,0,0,0.15); color: #000; border: 1px solid rgba(0,0,0,0.3); padding: 4px 10px; border-radius: 6px; text-decoration: none; font-size: 11.5px; font-weight: 800; display:flex; align-items:center; gap:4px;">
      <i class="fa-solid fa-rotate-left"></i> Reset Demo
    </a>
    <a href="{{ route('demo.exit') }}" style="background: #000; color: #fff; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-size: 11.5px; font-weight: 800;">
      Exit Demo
    </a>
  </div>
</div>
@endif

<!-- ═══════════════════════════════════════════════════════════════
     DASHDINE SIDEBAR NAVIGATION
     ═══════════════════════════════════════════════════════════════ -->
<aside class="sidebar" id="dashdineSidebar">
  
  <!-- Sidebar Brand Header -->
  <div class="sidebar-brand">
    <div class="brand-icon-box">
      <i class="fa-solid fa-fire-flame-curved"></i>
    </div>
    <div class="brand-text-box">
      <h2>DineFlow AI</h2>
      <span>RESTAURANT SUITE</span>
    </div>
    <button type="button" class="sidebar-toggle-btn" onclick="toggleSidebar()" title="Toggle Sidebar">
      <i class="fa-solid fa-chevron-left"></i>
    </button>
  </div>

  <!-- Sidebar Navigation Body -->
  <div class="sidebar-nav">
    
    <!-- OPERATIONS GROUP -->
    <div class="nav-group-section">
      <div class="nav-group-title">Operations</div>
      <a class="nav-link-item active" href="/dashboard" title="Dashboard Overview">
        <i class="fa-solid fa-table-cells-large"></i>
        <span class="nav-label">Overview</span>
      </a>
      <a class="nav-link-item" href="/pos" title="POS Billing Terminal">
        <i class="fa-solid fa-cash-register"></i>
        <span class="nav-label">POS Terminal</span>
        <span class="nav-badge-count" style="background:#10B981;">LIVE</span>
      </a>
      <a class="nav-link-item" href="javascript:void(0)" onclick="openDashOrdersModal()" title="Orders Log">
        <i class="fa-solid fa-receipt"></i>
        <span class="nav-label">Orders</span>
        <span class="nav-badge-count" id="globalOrdersNavBadge">{{ $todayOrdersCount }}</span>
      </a>
      <a class="nav-link-item" href="/admin/tables" title="Table Floor Manager">
        <i class="fa-solid fa-border-all"></i>
        <span class="nav-label">Tables Floor</span>
      </a>
      <a class="nav-link-item" href="/admin/menu-manager" title="Menu Catalog">
        <i class="fa-solid fa-utensils"></i>
        <span class="nav-label">Menu Items</span>
      </a>
      <a class="nav-link-item" href="/admin/modifiers" title="Modifiers & Combos">
        <i class="fa-solid fa-layer-group"></i>
        <span class="nav-label">Modifiers &amp; Combos</span>
      </a>
      <a class="nav-link-item" href="/kitchen" target="_blank" title="Kitchen Display System">
        <i class="fa-solid fa-fire-burner"></i>
        <span class="nav-label">Kitchen Display</span>
        <span class="nav-badge-count">KDS</span>
      </a>
      <a class="nav-link-item" href="/admin/inventory" title="Inventory Stock">
        <i class="fa-solid fa-boxes-stacked"></i>
        <span class="nav-label">Inventory Stock</span>
      </a>
      <a class="nav-link-item" href="/admin/procurement" title="Suppliers & PO">
        <i class="fa-solid fa-truck-ramp-box"></i>
        <span class="nav-label">Suppliers &amp; PO</span>
      </a>
    </div>

    <!-- MANAGEMENT GROUP -->
    <div class="nav-group-section">
      <div class="nav-group-title">Management</div>
      <a class="nav-link-item" href="/admin/attendance" title="Staff Attendance & Payroll">
        <i class="fa-solid fa-user-clock"></i>
        <span class="nav-label">Staff Attendance</span>
      </a>
      <a class="nav-link-item" href="/admin/staff" title="Staff & Roles">
        <i class="fa-solid fa-user-shield"></i>
        <span class="nav-label">Staff &amp; Roles</span>
      </a>
      <a class="nav-link-item" href="/admin/sales-report" title="Sales Analytics & Reports">
        <i class="fa-solid fa-chart-line"></i>
        <span class="nav-label">Revenue Reports</span>
      </a>
      <a class="nav-link-item" href="/admin/expenses" title="Operational Expenses">
        <i class="fa-solid fa-file-invoice-dollar"></i>
        <span class="nav-label">Expenses</span>
      </a>
      <a class="nav-link-item" href="/admin/customers" title="Customers CRM">
        <i class="fa-solid fa-users"></i>
        <span class="nav-label">Customers CRM</span>
      </a>
      <a class="nav-link-item" href="/admin/qr-generator" title="QR Menu Generator">
        <i class="fa-solid fa-qrcode"></i>
        <span class="nav-label">QR Generator</span>
      </a>
      <a class="nav-link-item" href="/admin/settings" title="System Settings">
        <i class="fa-solid fa-gear"></i>
        <span class="nav-label">Settings</span>
      </a>
    </div>

  </div>

  <!-- Bottom Live Restaurant Status Widget (DashDine Showcase) -->
  <div class="sidebar-status-widget">
    <div class="status-widget-row">
      <span>Restaurant</span>
      <span class="status-pill-open"><i class="fa-solid fa-circle"></i> Open</span>
    </div>
    <div class="status-widget-row">
      <span>Tables Active</span>
      <span class="val">{{ $occupiedTablesCount }} / {{ $totalTablesCount }}</span>
    </div>
    <div class="status-widget-row">
      <span>Kitchen Queue</span>
      <span class="val">{{ $todayOrdersCount > 0 ? $todayOrdersCount . ' orders' : '0 active' }}</span>
    </div>
  </div>

  <!-- User Card Footer -->
  <div class="sidebar-user-footer">
    <div class="user-avatar-circle">
      {{ strtoupper(substr(Auth::user()?->name ?? 'Admin', 0, 2)) }}
    </div>
    <div class="user-meta-info">
      <h4>{{ Auth::user()?->name ?? 'Administrator' }}</h4>
      <p>{{ Auth::user()?->role ?? 'Head Manager' }}</p>
    </div>
    <form method="POST" action="{{ route('auth.logout') }}" style="margin-left:auto;">
      @csrf
      <button type="submit" title="Sign Out" style="background:rgba(239,68,68,0.12); border:none; width:32px; height:32px; border-radius:8px; color:#EF4444; cursor:pointer; display:flex; align-items:center; justify-content:center;">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
      </button>
    </form>
  </div>

</aside>

<!-- ═══════════════════════════════════════════════════════════════
     MAIN VIEWPORT
     ═══════════════════════════════════════════════════════════════ -->
<div class="main-viewport">
  
  <!-- DASHDINE TOPBAR -->
  <header class="topbar">
    <div class="topbar-date-title">
      <span class="topbar-date">{{ strtoupper(\Carbon\Carbon::today()->format('l, d F Y')) }}</span>
      <h1 class="topbar-title">Dashboard Overview</h1>
    </div>

    <!-- DashDine Global Search -->
    <div class="dashdine-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" id="globalDashSearch" placeholder="Search orders, tables, staff, menu..." oninput="handleGlobalSearch(this.value)">
    </div>

    <!-- Action Controls Suite -->
    <div class="topbar-actions-suite">
      <span class="shift-status-pill">
        <i class="fa-solid fa-circle"></i> Active Shift
      </span>

      <button type="button" class="topbar-icon-btn" onclick="toggleDashTheme()" title="Toggle Dark/Light Mode">
        <i class="fa-solid fa-moon" id="themeToggleIcon"></i>
      </button>

      <button type="button" class="topbar-icon-btn" onclick="openModal('notifModal')" title="Notifications">
        <i class="fa-solid fa-bell"></i>
        <span class="notif-badge-dot"></span>
      </button>

      <a href="/pos" class="btn-new-pos-bill" title="Open POS Billing Counter">
        <i class="fa-solid fa-plus"></i> New POS Bill
      </a>
    </div>
  </header>

  <!-- DASHDINE WORKSPACE CONTENT -->
  <main class="dashboard-workspace">

    <!-- ═══════════════════════════════════════════════════════════
         ROW 1: THE 5 DASHDINE KPI METRIC CARDS
         ═══════════════════════════════════════════════════════════ -->
    <section class="dashdine-kpi-grid">
      
      <!-- Card 1: Today's Revenue -->
      <div class="dashdine-kpi-card">
        <div>
          <div class="kpi-card-header">
            <span class="kpi-title">Today's Revenue</span>
            <div class="kpi-icon-bubble orange"><i class="fa-solid fa-dollar-sign"></i></div>
          </div>
          <div class="kpi-value">₹ {{ number_format($todayRevenue, 2) }}</div>
        </div>
        <div class="kpi-trend positive">
          <i class="fa-solid fa-arrow-trend-up"></i> {{ $revenueDiffText }}
        </div>
        <div class="kpi-watermark-shape"></div>
      </div>

      <!-- Card 2: Orders Served -->
      <div class="dashdine-kpi-card">
        <div>
          <div class="kpi-card-header">
            <span class="kpi-title">Orders Served</span>
            <div class="kpi-icon-bubble green"><i class="fa-solid fa-receipt"></i></div>
          </div>
          <div class="kpi-value">{{ $todayOrdersCount }}</div>
        </div>
        <div class="kpi-trend positive">
          <i class="fa-solid fa-arrow-trend-up"></i> {{ $ordersDiffText }}
        </div>
        <div class="kpi-watermark-shape" style="background:rgba(16,185,129,0.03);"></div>
      </div>

      <!-- Card 3: Avg. Order Value -->
      <div class="dashdine-kpi-card">
        <div>
          <div class="kpi-card-header">
            <span class="kpi-title">Avg. Order Value</span>
            <div class="kpi-icon-bubble amber"><i class="fa-solid fa-chart-line"></i></div>
          </div>
          <div class="kpi-value">₹ {{ number_format($avgOrderValue ?? ($todayOrdersCount > 0 ? $todayRevenue / $todayOrdersCount : 0), 2) }}</div>
        </div>
        <div class="kpi-trend positive">
          <i class="fa-solid fa-arrow-trend-up"></i> +4.2% <span class="kpi-trend-sub">vs last week</span>
        </div>
        <div class="kpi-watermark-shape" style="background:rgba(245,158,11,0.03);"></div>
      </div>

      <!-- Card 4: Cancellations -->
      <div class="dashdine-kpi-card">
        <div>
          <div class="kpi-card-header">
            <span class="kpi-title">Cancellations</span>
            <div class="kpi-icon-bubble red"><i class="fa-solid fa-circle-xmark"></i></div>
          </div>
          <div class="kpi-value">{{ $cancellationsCount ?? 0 }}</div>
        </div>
        <div class="kpi-trend {{ ($cancellationsCount ?? 0) > 0 ? 'negative' : 'neutral' }}">
          <i class="fa-solid {{ ($cancellationsCount ?? 0) > 0 ? 'fa-arrow-trend-down' : 'fa-check' }}"></i> {{ ($cancellationsCount ?? 0) > 0 ? ($cancellationsCount . ' today') : 'Zero refunds' }}
        </div>
        <div class="kpi-watermark-shape" style="background:rgba(239,68,68,0.03);"></div>
      </div>

      <!-- Card 5: Guest Covers -->
      <div class="dashdine-kpi-card">
        <div>
          <div class="kpi-card-header">
            <span class="kpi-title">Guest Covers</span>
            <div class="kpi-icon-bubble cyan"><i class="fa-solid fa-users"></i></div>
          </div>
          <div class="kpi-value">{{ $activeCustomers ?? ($occupiedTablesCount * 3) }}</div>
        </div>
        <div class="kpi-trend positive">
          <i class="fa-solid fa-arrow-trend-up"></i> +12.4% <span class="kpi-trend-sub">active diners</span>
        </div>
        <div class="kpi-watermark-shape" style="background:rgba(6,182,212,0.03);"></div>
      </div>

    </section>

    <!-- ═══════════════════════════════════════════════════════════
         ROW 2: HOURLY REVENUE CHART & LIVE FLOOR MAP
         ═══════════════════════════════════════════════════════════ -->
    <section class="dashdine-middle-grid">
      
      <!-- Hourly Revenue Chart -->
      <div class="dashdine-card">
        <div class="card-title-bar">
          <div>
            <h3>Hourly Revenue</h3>
            <p class="card-subtitle">Dine-in vs Takeaway &bull; Today's performance</p>
          </div>
          <div style="display:flex; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; gap:12px; font-size:12px; font-weight:600;">
              <span style="display:inline-flex; align-items:center; gap:5px;"><span style="width:10px; height:10px; border-radius:3px; background:#FF6B00;"></span> Dine-in</span>
              <span style="display:inline-flex; align-items:center; gap:5px;"><span style="width:10px; height:10px; border-radius:3px; background:#FBBF24;"></span> Takeaway</span>
            </div>
            <div class="chart-time-pills">
              <button class="time-pill-btn active" onclick="switchChartTime('day')">Day</button>
              <button class="time-pill-btn" onclick="switchChartTime('week')">Week</button>
              <button class="time-pill-btn" onclick="switchChartTime('month')">Month</button>
            </div>
          </div>
        </div>

        <div class="chart-container-box">
          <canvas id="dashHourlyRevenueChart"></canvas>
        </div>
      </div>

      <!-- Live Floor Map Widget (DashDine Showcase) -->
      <div class="dashdine-card">
        <div class="card-title-bar">
          <div>
            <h3>Floor Map</h3>
            <p class="card-subtitle">Real-time table occupancy</p>
          </div>
          <a href="/admin/tables" class="manage-link-btn" title="Manage Tables Layout">Manage &rarr;</a>
        </div>

        <!-- 12 Interactive Tables Grid -->
        <div class="floor-map-grid" id="dashdineFloorMapGrid">
          @php
            $displayTables = [
              ['id' => 'T1',  'name' => 'T1',  'status' => 'occupied'],
              ['id' => 'T2',  'name' => 'T2',  'status' => 'occupied'],
              ['id' => 'T3',  'name' => 'T3',  'status' => 'available'],
              ['id' => 'T4',  'name' => 'T4',  'status' => 'reserved'],
              ['id' => 'T5',  'name' => 'T5',  'status' => 'available'],
              ['id' => 'T6',  'name' => 'T6',  'status' => 'occupied'],
              ['id' => 'T7',  'name' => 'T7',  'status' => 'occupied'],
              ['id' => 'T8',  'name' => 'T8',  'status' => 'cleaning'],
              ['id' => 'T9',  'name' => 'T9',  'status' => 'reserved'],
              ['id' => 'T10', 'name' => 'T10', 'status' => 'occupied'],
              ['id' => 'T11', 'name' => 'T11', 'status' => 'available'],
              ['id' => 'T12', 'name' => 'T12', 'status' => 'available'],
            ];
            if (isset($floorTables) && count($floorTables) > 0) {
              $displayTables = [];
              foreach ($floorTables as $idx => $ft) {
                $isOcc = in_array($ft->id, $occupiedTableIds ?? []);
                $displayTables[] = [
                  'id' => $ft->id,
                  'name' => $ft->table_number ?? ('T' . ($idx + 1)),
                  'status' => $isOcc ? 'occupied' : 'available'
                ];
              }
            }
          @endphp

          @foreach($displayTables as $tbl)
            <div class="floor-table-node {{ $tbl['status'] }}" onclick="handleTableClick('{{ $tbl['name'] }}', '{{ $tbl['status'] }}')" title="Table {{ $tbl['name'] }} ({{ ucfirst($tbl['status']) }})">
              <span class="table-id-txt">{{ $tbl['name'] }}</span>
              <span class="table-status-tag">{{ $tbl['status'] }}</span>
            </div>
          @endforeach
        </div>

        <!-- Floor Legend Summary -->
        <div class="floor-legend-bar">
          <span><span class="floor-legend-dot" style="background:#FF6B00;"></span> Occupied ({{ $occupiedTablesCount ?: 5 }})</span>
          <span><span class="floor-legend-dot" style="background:#10B981;"></span> Available ({{ max(1, $totalTablesCount - $occupiedTablesCount) }})</span>
          <span><span class="floor-legend-dot" style="background:#F59E0B;"></span> Reserved (2)</span>
          <span><span class="floor-legend-dot" style="background:#94A3B8;"></span> Cleaning (1)</span>
        </div>
      </div>

    </section>

    <!-- ═══════════════════════════════════════════════════════════
         ROW 3: RECENT ORDERS & TOP DISHES / STAFF ON DUTY
         ═══════════════════════════════════════════════════════════ -->
    <section class="dashdine-bottom-grid">
      
      <!-- Recent Orders Log -->
      <div class="dashdine-card">
        <div class="card-title-bar">
          <div>
            <h3>Recent Orders</h3>
            <p class="card-subtitle">Live order stream &bull; Updating in real-time</p>
          </div>
          <div style="display:flex; align-items:center; gap:12px;">
            <div class="order-filter-pills">
              <button class="order-filter-btn active" onclick="filterRecentOrders('all', this)">All</button>
              <button class="order-filter-btn" onclick="filterRecentOrders('dine_in', this)">Dine-in</button>
              <button class="order-filter-btn" onclick="filterRecentOrders('takeaway', this)">Takeaway</button>
              <button class="order-filter-btn" onclick="filterRecentOrders('delivery', this)">Delivery</button>
            </div>
            <button type="button" class="manage-link-btn" onclick="openDashOrdersModal('all')" style="border:none; cursor:pointer;">
              <i class="fa-solid fa-list-check"></i> View All
            </button>
          </div>
        </div>

        <div style="overflow-x:auto;">
          <table class="dashdine-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Table</th>
                <th>Items Summary</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="dashRecentOrdersTbody">
              @if(isset($recentOrdersList) && count($recentOrdersList) > 0)
                @foreach($recentOrdersList as $ro)
                  @php
                    $itemsTxt = $ro->orderItems->map(fn($oi) => ($oi->quantity . 'x ' . ($oi->menuItem->name ?? 'Dish')))->join(', ');
                    if (!$itemsTxt) $itemsTxt = 'Special Chef Course';
                    $statusBadge = '<span class="status-badge-served"><i class="fa-solid fa-circle-check"></i> ' . ucfirst($ro->status) . '</span>';
                    if ($ro->status === 'cooking' || $ro->status === 'preparing') {
                      $statusBadge = '<span class="status-badge-cooking"><i class="fa-solid fa-fire"></i> Cooking</span>';
                    } elseif ($ro->status === 'pending') {
                      $statusBadge = '<span class="status-badge-pending"><i class="fa-solid fa-clock"></i> Pending</span>';
                    }
                  @endphp
                  <tr data-type="{{ $ro->order_type ?? 'dine_in' }}">
                    <td class="order-id-code">#{{ str_pad($ro->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td><span class="table-pill-badge">{{ $ro->table->table_number ?? 'T1' }}</span></td>
                    <td style="max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $itemsTxt }}</td>
                    <td style="font-weight:700; font-family:'JetBrains Mono',monospace;">₹ {{ number_format($ro->total_amount, 2) }}</td>
                    <td><span style="font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">{{ $ro->order_type ?? 'dine-in' }}</span></td>
                    <td style="color:var(--text-muted); font-size:12px;">{{ $ro->created_at->format('h:i A') }}</td>
                    <td>{!! $statusBadge !!}</td>
                  </tr>
                @endforeach
              @else
                <!-- High-Fidelity DashDine Sample Orders -->
                <tr data-type="dine_in">
                  <td class="order-id-code">#0841</td>
                  <td><span class="table-pill-badge">T6</span></td>
                  <td>Truffle Risotto, Tiramisu, House Wine</td>
                  <td style="font-weight:700; font-family:'JetBrains Mono',monospace;">₹ 685.00</td>
                  <td><span style="font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">Dine-in</span></td>
                  <td style="color:var(--text-muted); font-size:12px;">07:42 PM</td>
                  <td><span class="status-badge-served"><i class="fa-solid fa-circle-check"></i> Served</span></td>
                </tr>
                <tr data-type="dine_in">
                  <td class="order-id-code">#0842</td>
                  <td><span class="table-pill-badge">T2</span></td>
                  <td>Grilled Salmon, Caesar Salad, Espresso</td>
                  <td style="font-weight:700; font-family:'JetBrains Mono',monospace;">₹ 540.00</td>
                  <td><span style="font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">Dine-in</span></td>
                  <td style="color:var(--text-muted); font-size:12px;">07:51 PM</td>
                  <td><span class="status-badge-cooking"><i class="fa-solid fa-fire"></i> Cooking</span></td>
                </tr>
                <tr data-type="takeaway">
                  <td class="order-id-code">#0843</td>
                  <td><span class="table-pill-badge">TAK</span></td>
                  <td>Margherita Pizza, Garlic Bread x2</td>
                  <td style="font-weight:700; font-family:'JetBrains Mono',monospace;">₹ 317.50</td>
                  <td><span style="font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">Takeaway</span></td>
                  <td style="color:var(--text-muted); font-size:12px;">07:55 PM</td>
                  <td><span class="status-badge-pending"><i class="fa-solid fa-clock"></i> Pending</span></td>
                </tr>
                <tr data-type="dine_in">
                  <td class="order-id-code">#0844</td>
                  <td><span class="table-pill-badge">T10</span></td>
                  <td>Beef Tenderloin, Red Wine, Panna Cotta</td>
                  <td style="font-weight:700; font-family:'JetBrains Mono',monospace;">₹ 920.00</td>
                  <td><span style="font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">Dine-in</span></td>
                  <td style="color:var(--text-muted); font-size:12px;">08:03 PM</td>
                  <td><span class="status-badge-cooking"><i class="fa-solid fa-fire"></i> Cooking</span></td>
                </tr>
                <tr data-type="delivery">
                  <td class="order-id-code">#0845</td>
                  <td><span class="table-pill-badge">DEL</span></td>
                  <td>BBQ Chicken Burger, Peri Fries, Shake</td>
                  <td style="font-weight:700; font-family:'JetBrains Mono',monospace;">₹ 284.00</td>
                  <td><span style="font-size:11px; font-weight:700; color:var(--text-secondary); text-transform:uppercase;">Delivery</span></td>
                  <td style="color:var(--text-muted); font-size:12px;">08:07 PM</td>
                  <td><span class="status-badge-pending"><i class="fa-solid fa-clock"></i> Pending</span></td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Menu Items & Staff on Duty -->
      <div style="display:flex; flex-direction:column; gap:20px;">
        
        <!-- Top Menu Items -->
        <div class="dashdine-card">
          <div class="card-title-bar">
            <div>
              <h3>Top Menu Items</h3>
              <p class="card-subtitle">Highest volume dishes</p>
            </div>
            <span style="font-size:11px; font-weight:800; color:#10B981; background:rgba(16,185,129,0.12); padding:3px 8px; border-radius:6px;">TODAY</span>
          </div>

          <div class="top-dishes-list">
            <div class="dish-rank-item">
              <span class="dish-rank-num">1</span>
              <div class="dish-icon-box">🍝</div>
              <div class="dish-details-col">
                <h5>Pasta Carbonara</h5>
                <span>48 orders</span>
              </div>
              <div class="dish-progress-bar"><div class="dish-progress-fill" style="width: 88%;"></div></div>
            </div>

            <div class="dish-rank-item">
              <span class="dish-rank-num">2</span>
              <div class="dish-icon-box">🍕</div>
              <div class="dish-details-col">
                <h5>Margherita Pizza</h5>
                <span>41 orders</span>
              </div>
              <div class="dish-progress-bar"><div class="dish-progress-fill" style="width: 74%;"></div></div>
            </div>

            <div class="dish-rank-item">
              <span class="dish-rank-num">3</span>
              <div class="dish-icon-box">🥩</div>
              <div class="dish-details-col">
                <h5>Paneer Butter Masala</h5>
                <span>34 orders</span>
              </div>
              <div class="dish-progress-bar"><div class="dish-progress-fill" style="width: 62%;"></div></div>
            </div>

            <div class="dish-rank-item">
              <span class="dish-rank-num">4</span>
              <div class="dish-icon-box">🍹</div>
              <div class="dish-details-col">
                <h5>Virgin Mojito Mocktail</h5>
                <span>29 orders</span>
              </div>
              <div class="dish-progress-bar"><div class="dish-progress-fill" style="width: 50%;"></div></div>
            </div>
          </div>
        </div>

        <!-- Staff on Duty Panel (Attendance ERP Integration) -->
        <div class="dashdine-card">
          <div class="card-title-bar">
            <div>
              <h3>Staff on Duty</h3>
              <p class="card-subtitle">Shift clock-in attendance</p>
            </div>
            <a href="/admin/attendance" class="manage-link-btn" title="View Shift Payroll">Payroll &rarr;</a>
          </div>

          <div class="staff-duty-list">
            <div class="staff-duty-card">
              <div class="staff-duty-left">
                <div class="staff-avatar-badge" style="background:#FF6B00;">LC</div>
                <div class="staff-duty-info">
                  <h6>Lucia Chen</h6>
                  <span>Head Waiter</span>
                </div>
              </div>
              <span class="staff-metric-tag">34 <span style="font-size:10px; color:var(--text-muted);">orders</span></span>
            </div>

            <div class="staff-duty-card">
              <div class="staff-duty-left">
                <div class="staff-avatar-badge" style="background:#10B981;">JM</div>
                <div class="staff-duty-info">
                  <h6>James Moore</h6>
                  <span>Chef de Partie</span>
                </div>
              </div>
              <span class="staff-metric-tag">52 <span style="font-size:10px; color:var(--text-muted);">dishes</span></span>
            </div>

            <div class="staff-duty-card">
              <div class="staff-duty-left">
                <div class="staff-avatar-badge" style="background:#8B5CF6;">SR</div>
                <div class="staff-duty-info">
                  <h6>Sofia Rossi</h6>
                  <span>Cashier Desk</span>
                </div>
              </div>
              <span class="staff-metric-tag">28 <span style="font-size:10px; color:var(--text-muted);">bills</span></span>
            </div>
          </div>
        </div>

      </div>

    </section>

  </main>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     PRESERVED OPERATIONAL MODALS (ORDERS, DEALS, DISPATCH, NOTIFS)
     ═══════════════════════════════════════════════════════════════ -->

<!-- NOTIFICATION MODAL -->
<div class="modal-overlay" id="notifModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.75); backdrop-filter:blur(6px); display:none; align-items:center; justify-content:center; z-index:10000;">
  <div class="modal" style="width:480px; max-width:92vw; background:var(--card); border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden;">
    <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
      <h3 style="font-family:'Outfit',sans-serif; font-size:16px; font-weight:800; color:var(--text-primary); margin:0;">Notifications</h3>
      <button onclick="closeModal('notifModal')" style="background:none; border:none; color:var(--text-muted); font-size:20px; cursor:pointer;">&times;</button>
    </div>
    <div style="padding:20px 24px; max-height:60vh; overflow-y:auto;" id="notifListContainer">
      <div style="text-align:center; padding:30px; color:var(--text-muted); font-size:13px;">
        <i class="fa-solid fa-bell-slash" style="font-size:28px; margin-bottom:8px; display:block; opacity:0.4;"></i>
        All clear! No pending urgent notifications.
      </div>
    </div>
  </div>
</div>

<!-- FULL ORDERS DETAIL MODAL -->
<div class="modal-overlay" id="dashOrdersModal" style="position:fixed; inset:0; background:rgba(15,23,42,0.75); backdrop-filter:blur(6px); display:none; align-items:center; justify-content:center; z-index:10000; opacity:0; transition:all 0.25s;">
  <div class="modal" style="width:980px; max-width:95vw; height:85vh; background:var(--card); border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; display:flex; flex-direction:column;">
    <div style="background:var(--primary-light); border-bottom:1px solid var(--border); padding:16px 24px; display:flex; justify-content:space-between; align-items:center;">
      <div id="dashOrdersModalTitle" style="color:var(--primary); font-size:17px; font-weight:800; font-family:'Outfit',sans-serif; display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-receipt"></i> Active Order Log
      </div>
      <button onclick="closeDashOrdersModal()" style="background:none; border:none; color:var(--text-muted); font-size:24px; cursor:pointer;">&times;</button>
    </div>
    <div style="padding:20px 24px; overflow-y:auto; flex:1;" id="dashOrdersModalBody">
      <div style="text-align:center; padding:50px; color:var(--text-muted);">
        <i class="fa-solid fa-spinner fa-spin" style="font-size:28px; color:var(--primary); display:block; margin-bottom:12px;"></i>
        Loading live orders...
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     CLIENT-SIDE CHART & INTERACTIVITY SCRIPTS
     ═══════════════════════════════════════════════════════════════ -->
<script>
const DASHBOARD_RESTAURANT_ID = {{ Auth::user()?->restaurant_id ?? 1 }};
let hourlyChartInstance = null;

// Initialize DashDine Stacked Revenue Chart
document.addEventListener('DOMContentLoaded', () => {
  initDashHourlyChart();
  initThemeState();
});

function initDashHourlyChart() {
  const ctx = document.getElementById('dashHourlyRevenueChart');
  if (!ctx) return;

  const labels = ['10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm'];
  const dineInData = [420, 780, 1420, 1380, 1250, 1100, 1340, 1180, 1450, 890];
  const takeawayData = [210, 340, 580, 520, 480, 430, 510, 620, 540, 310];

  hourlyChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Takeaway',
          data: takeawayData,
          backgroundColor: '#FBBF24',
          borderRadius: 6,
          borderSkipped: false
        },
        {
          label: 'Dine-in',
          data: dineInData,
          backgroundColor: '#FF6B00',
          borderRadius: 6,
          borderSkipped: false
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#121624',
          titleFont: { family: 'Outfit', size: 13, weight: 'bold' },
          bodyFont: { family: 'Inter', size: 12 },
          padding: 12,
          cornerRadius: 10,
          callbacks: {
            label: (ctx) => `${ctx.dataset.label}: ₹ ${ctx.raw}`
          }
        }
      },
      scales: {
        x: {
          stacked: true,
          grid: { display: false },
          ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11, weight: '600' } }
        },
        y: {
          stacked: true,
          grid: { color: 'rgba(226, 232, 240, 0.4)', drawBorder: false },
          ticks: {
            color: '#94A3B8',
            font: { family: 'JetBrains Mono', size: 10.5 },
            callback: (v) => '₹' + v
          }
        }
      }
    }
  });
}

function switchChartTime(timeframe) {
  document.querySelectorAll('.time-pill-btn').forEach(b => b.classList.remove('active'));
  event.target.classList.add('active');

  if (!hourlyChartInstance) return;
  if (timeframe === 'week') {
    hourlyChartInstance.data.labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    hourlyChartInstance.data.datasets[1].data = [4800, 5200, 6100, 5800, 7400, 9200, 8400];
    hourlyChartInstance.data.datasets[0].data = [1200, 1400, 1800, 1600, 2200, 3100, 2900];
  } else if (timeframe === 'month') {
    hourlyChartInstance.data.labels = ['W1', 'W2', 'W3', 'W4'];
    hourlyChartInstance.data.datasets[1].data = [28000, 34000, 39000, 42000];
    hourlyChartInstance.data.datasets[0].data = [9000, 11000, 13000, 14500];
  } else {
    hourlyChartInstance.data.labels = ['10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm'];
    hourlyChartInstance.data.datasets[1].data = [420, 780, 1420, 1380, 1250, 1100, 1340, 1180, 1450, 890];
    hourlyChartInstance.data.datasets[0].data = [210, 340, 580, 520, 480, 430, 510, 620, 540, 310];
  }
  hourlyChartInstance.update();
}

function filterRecentOrders(type, btn) {
  document.querySelectorAll('.order-filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const rows = document.querySelectorAll('#dashRecentOrdersTbody tr');
  rows.forEach(r => {
    if (type === 'all' || r.getAttribute('data-type') === type) {
      r.style.display = '';
    } else {
      r.style.display = 'none';
    }
  });
}

function handleTableClick(tableName, status) {
  if (status === 'occupied') {
    openDashOrdersModal(tableName);
  } else {
    window.location.href = `/pos?table=${encodeURIComponent(tableName)}`;
  }
}

function handleGlobalSearch(query) {
  const q = query.toLowerCase().trim();
  const rows = document.querySelectorAll('#dashRecentOrdersTbody tr');
  rows.forEach(r => {
    const text = r.textContent.toLowerCase();
    r.style.display = text.includes(q) ? '' : 'none';
  });
}

// Sidebar Toggle
function toggleSidebar() {
  const sb = document.getElementById('dashdineSidebar');
  if (sb) sb.classList.toggle('collapsed');
}

// Dark / Light Theme Toggle
function toggleDashTheme() {
  const isDark = document.body.classList.toggle('dark-theme');
  localStorage.setItem('dineflow_theme', isDark ? 'dark' : 'light');
  updateThemeIcon(isDark);
}

function initThemeState() {
  const saved = localStorage.getItem('dineflow_theme');
  const isDark = saved === 'dark';
  if (isDark) {
    document.body.classList.add('dark-theme');
  }
  updateThemeIcon(isDark);
}

function updateThemeIcon(isDark) {
  const icon = document.getElementById('themeToggleIcon');
  if (icon) {
    icon.className = isDark ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
  }
}

// Preserved Modal Helpers
function openModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'flex';
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'none';
}

function openDashOrdersModal(filter) {
  const modal = document.getElementById('dashOrdersModal');
  const body = document.getElementById('dashOrdersModalBody');
  if (!modal) return;
  modal.style.display = 'flex';
  setTimeout(() => modal.style.opacity = '1', 10);
  loadDashOrdersData(filter);
}
function closeDashOrdersModal() {
  const modal = document.getElementById('dashOrdersModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => modal.style.display = 'none', 250);
}
async function loadDashOrdersData(filter) {
  const body = document.getElementById('dashOrdersModalBody');
  if (!body) return;
  try {
    const res = await fetch(`/api/orders/restaurant/${DASHBOARD_RESTAURANT_ID}`);
    const data = await res.json();
    const orders = Array.isArray(data) ? data : (data.data || []);
    if (!orders.length) {
      body.innerHTML = '<div style="text-align:center; padding:40px; color:var(--text-muted);"><i class="fa-solid fa-receipt" style="font-size:32px; opacity:0.3; margin-bottom:10px; display:block;"></i>No orders recorded today yet.</div>';
      return;
    }
    let html = '<div style="display:flex; flex-direction:column; gap:10px;">';
    orders.forEach(o => {
      html += `
        <div style="background:var(--card-subtle); border:1px solid var(--border); border-radius:14px; padding:14px 18px; display:flex; justify-content:space-between; align-items:center;">
          <div>
            <div style="font-weight:800; font-family:'Outfit',sans-serif; color:var(--text-primary); font-size:14px;">Order #${o.id} &bull; ${o.table_number || 'Takeaway'}</div>
            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">₹ ${parseFloat(o.total_amount).toFixed(2)} &bull; ${(o.order_type || 'dine-in').toUpperCase()} &bull; ${o.payment_status.toUpperCase()}</div>
          </div>
          <span style="padding:4px 12px; border-radius:20px; font-size:11px; font-weight:800; background:rgba(255,107,0,0.12); color:#FF6B00; text-transform:uppercase;">${o.status}</span>
        </div>`;
    });
    html += '</div>';
    body.innerHTML = html;
  } catch(e) {
    body.innerHTML = '<div style="text-align:center; padding:30px; color:#EF4444;">Failed to fetch live orders.</div>';
  }
}
</script>

</body>
</html>
