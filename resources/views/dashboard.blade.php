<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard — DineFlow AI POS</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <script>window.AUTH_USER_ROLE = "{{ Auth::user()?->role ?? Auth::user()?->username ?? session('active_role') ?? '' }}";</script>
  <script src="/frontend/admin/utils.js"></script>

  <!-- Typography: Inter & Geist Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <!-- Chart.js 4.4.1 -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            canvas: {
              DEFAULT: '#FBF9F8',
              dark: '#0B0B0D'
            },
            surface: {
              DEFAULT: '#FFFFFF',
              dark: '#151517'
            },
            panel: {
              DEFAULT: '#FCFCFC',
              dark: '#101012'
            },
            brand: {
              DEFAULT: '#9F1239', // Velvet Wine / Deep Burgundy
              dark: '#881337',
              hover: '#BE123C',
              soft: 'rgba(159, 18, 57, 0.08)',
              rose: '#FB7185'
            },
            hairline: {
              DEFAULT: '#ECE6E4',
              dark: '#26262A'
            },
            obsidian: {
              DEFAULT: '#111827',
              muted: '#6B7280',
              light: '#F4F4F5'
            }
          },
          fontFamily: {
            sans: ['Inter', 'Outfit', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
            mono: ['Geist Mono', 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'monospace']
          },
          boxShadow: {
            'subtle': '0 1px 3px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02)',
            'elevated': '0 4px 14px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03)',
            'deep': '0 12px 32px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.04)'
          }
        }
      }
    }
  </script>

  <style>
    :root {
      --canvas: #FBF9F8;
      --surface: #FFFFFF;
      --panel: #FCFCFC;
      --brand: #9F1239;
      --brand-hover: #881337;
      --brand-soft: rgba(159, 18, 57, 0.08);
      --hairline: #ECE6E4;
      --text-primary: #111827;
      --text-muted: #6B7280;
      --success: #22C55E;
      --warning: #F59E0B;
      --danger: #EF4444;
      --primary: #9F1239;
      --primary-light: rgba(159, 18, 57, 0.08);
      --primary-dark: #881337;
      --sidebar-bg: #FCFCFC;
      --sidebar-hover: #F5F1EF;
      --sidebar-active: #9F1239;
      --bg: #FBF9F8;
      --card: #FFFFFF;
      --border: #ECE6E4;
      --text-secondary: #4B5563;
      --shadow-sm: 0 1px 3px rgba(0,0,0,0.03);
      --shadow-md: 0 4px 14px rgba(0,0,0,0.05);
      --shadow-lg: 0 12px 32px rgba(0,0,0,0.08);
    }

    /* Dark Mode Design Tokens */
    body:not(.light-theme),
    html:not(.light-theme) body {
      --canvas: #0B0B0D;
      --surface: #151517;
      --panel: #101012;
      --brand: #FB7185;
      --brand-hover: #F43F5E;
      --brand-soft: rgba(251, 113, 133, 0.12);
      --hairline: #26262A;
      --text-primary: #F4F4F5;
      --text-muted: #A1A1AA;
      --sidebar-bg: #101012;
      --sidebar-hover: #1C1C20;
      --sidebar-active: #FB7185;
      --bg: #0B0B0D;
      --card: #151517;
      --border: #26262A;
      --text-secondary: #D4D4D8;
      --primary: #FB7185;
      --primary-light: rgba(251, 113, 133, 0.12);
    }

    /* Explicit Light Mode Override when light-theme is active */
    body.light-theme {
      --canvas: #FBF9F8;
      --surface: #FFFFFF;
      --panel: #FCFCFC;
      --brand: #9F1239;
      --brand-hover: #881337;
      --brand-soft: rgba(159, 18, 57, 0.08);
      --hairline: #ECE6E4;
      --text-primary: #111827;
      --text-muted: #6B7280;
      --sidebar-bg: #FCFCFC;
      --sidebar-hover: #F5F1EF;
      --sidebar-active: #9F1239;
      --bg: #FBF9F8;
      --card: #FFFFFF;
      --border: #ECE6E4;
      --text-secondary: #4B5563;
      --primary: #9F1239;
      --primary-light: rgba(159, 18, 57, 0.08);
    }

    *, *::before, *::after { box-sizing: border-box; }
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--canvas);
      color: var(--text-primary);
      margin: 0;
      padding: 0;
      display: flex;
      height: 100vh;
      overflow: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* Tabular figures for financial and numerical precision */
    .tabular-nums {
      font-variant-numeric: tabular-nums;
    }

    /* Sidebar Transitions & Elevation */
    .sidebar {
      width: 256px;
      min-width: 256px;
      background: var(--sidebar-bg);
      border-right: 1px solid var(--hairline);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      transition: width 0.22s cubic-bezier(0.16, 1, 0.3, 1), min-width 0.22s cubic-bezier(0.16, 1, 0.3, 1);
      flex-shrink: 0;
      z-index: 40;
    }
    .sidebar.collapsed {
      width: 68px;
      min-width: 68px;
    }
    .sidebar.collapsed .brand-text,
    .sidebar.collapsed .sidebar-section-label,
    .sidebar.collapsed .nav-label,
    .sidebar.collapsed .nav-badge,
    .sidebar.collapsed .user-info {
      opacity: 0;
      display: none;
    }
    .sidebar.collapsed .sidebar-toggle i {
      transform: rotate(180deg);
    }
    .sidebar.collapsed .user-card {
      justify-content: center;
      padding: 8px 4px;
    }
    .sidebar.collapsed #dashLogoutForm {
      display: none !important;
    }

    .sidebar-nav {
      flex: 1;
      overflow-y: auto;
      overflow-x: hidden;
      scrollbar-width: thin;
      scrollbar-color: var(--hairline) transparent;
      padding: 12px 10px;
    }
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: var(--hairline); border-radius: 4px; }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px 12px;
      border-radius: 12px;
      color: var(--text-muted);
      cursor: pointer;
      font-size: 13.5px;
      font-weight: 500;
      transition: all 0.16s ease;
      margin-top: 2px;
      text-decoration: none;
      white-space: nowrap;
      position: relative;
    }
    .nav-item:hover {
      background: var(--sidebar-hover);
      color: var(--text-primary);
    }
    .nav-item.active {
      background: var(--brand-soft);
      color: var(--brand);
      font-weight: 600;
    }
    body:not(.light-theme) .nav-item.active {
      background: rgba(251, 113, 133, 0.15);
      color: #FB7185;
    }
    .nav-item i {
      width: 18px;
      min-width: 18px;
      text-align: center;
      font-size: 14px;
    }
    .nav-badge {
      margin-left: auto;
      font-family: 'Geist Mono', monospace;
      font-size: 11px;
      font-weight: 600;
      padding: 2px 7px;
      border-radius: 9999px;
      background: var(--brand-soft);
      color: var(--brand);
      border: 1px solid var(--hairline);
    }
    .nav-badge.green {
      background: rgba(34, 197, 94, 0.12);
      color: #16A34A;
      border-color: rgba(34, 197, 94, 0.25);
    }

    /* Main view container */
    .main {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      min-width: 0;
      background: var(--canvas);
    }

    /* Topbar */
    .topbar {
      background: var(--surface);
      border-bottom: 1px solid var(--hairline);
      padding: 0 28px;
      height: 60px;
      display: flex;
      align-items: center;
      gap: 14px;
      flex-shrink: 0;
    }

    /* Card Micro-Interactions */
    .bento-card {
      background: var(--surface);
      border: 1px solid var(--hairline);
      border-radius: 16px;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
      transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
    }
    .bento-card:hover {
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
      border-color: rgba(159, 18, 57, 0.2);
    }
    body:not(.light-theme) .bento-card:hover {
      box-shadow: 0 6px 24px rgba(0, 0, 0, 0.4);
      border-color: rgba(251, 113, 133, 0.25);
    }

    /* Mobile Responsive Sidebar Drawer */
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        width: 260px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      }
      .sidebar.mobile-open {
        transform: translateX(0);
      }
      .sidebar-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(4px);
        z-index: 45;
        display: none;
      }
      .sidebar-backdrop.active {
        display: block;
      }
    }

    /* Audio wave animation for Soundbox */
    @keyframes soundwave {
      0%, 100% { height: 4px; }
      50% { height: 16px; }
    }
    .soundwave-bar {
      width: 2.5px;
      background: #22C55E;
      border-radius: 2px;
      animation: soundwave 1.2s infinite ease-in-out;
    }
    .soundwave-bar:nth-child(2) { animation-delay: 0.2s; }
    .soundwave-bar:nth-child(3) { animation-delay: 0.4s; }
    .soundwave-bar:nth-child(4) { animation-delay: 0.1s; }
  </style>
</head>
<body>
@include('partials.impersonate-banner')

@if(Auth::user()?->restaurant?->is_demo)
<div id="demoSandboxBanner" class="bg-amber-500 text-stone-900 px-5 py-2 text-xs font-semibold flex items-center justify-between flex-wrap gap-2 z-50 shadow-sm">
  <div class="flex items-center gap-2">
    <span class="bg-stone-900 text-amber-300 px-2 py-0.5 rounded text-[10px] font-mono font-bold tracking-wider uppercase">Demo Sandbox</span>
    <span>Your changes are isolated within your active session. Production catalog is protected.</span>
  </div>
  <div class="flex items-center gap-2">
    <a href="{{ route('demo.reset') }}" onclick="return confirm('Reset demo sandbox to fresh sample state?');" class="bg-stone-900/10 hover:bg-stone-900/20 text-stone-900 border border-stone-900/20 px-2.5 py-1 rounded-lg text-xs font-medium transition flex items-center gap-1.5">
      <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Demo
    </a>
    <a href="{{ route('demo.exit') }}" class="bg-stone-900 hover:bg-stone-800 text-white px-3 py-1 rounded-lg text-xs font-medium transition">
      Exit Demo
    </a>
  </div>
</div>
@endif

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- SIDEBAR: Boutique Linear & Fine Dining Hospitality Navigation -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">
  <div class="h-[60px] px-4 flex items-center justify-between border-b border-[var(--hairline)] flex-shrink-0">
    <div class="flex items-center gap-2.5 overflow-hidden">
      <div class="w-8 h-8 rounded-xl bg-[var(--brand)] text-white flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
        <i class="fa-solid fa-utensils text-xs"></i>
      </div>
      <div class="brand-text">
        <h2 class="text-sm font-semibold tracking-tight text-[var(--text-primary)] leading-tight">DineFlow AI</h2>
        <span class="text-[11px] font-mono text-[var(--text-muted)] tracking-wider">HOSPITALITY OS</span>
      </div>
    </div>
    <button class="sidebar-toggle w-7 h-7 rounded-lg border border-[var(--hairline)] hover:bg-[var(--sidebar-hover)] text-[var(--text-muted)] flex items-center justify-center transition" onclick="toggleSidebar()" title="Toggle sidebar">
      <i class="fa-solid fa-chevron-left text-[11px]" id="sidebarToggleIcon"></i>
    </button>
  </div>

  <div class="sidebar-nav">
    <div class="mb-4">
      <div class="sidebar-section-label text-[10px] font-mono uppercase tracking-wider text-[var(--text-muted)] px-3 mb-1.5">Terminal & Operations</div>
      <a class="nav-item active" href="/dashboard" data-path="/dashboard" data-tip="Dashboard">
        <i class="fa-solid fa-chart-pie"></i><span class="nav-label">Dashboard</span>
      </a>
      <a class="nav-item" href="/pos" data-path="/pos" data-tip="POS Terminal">
        <i class="fa-solid fa-cash-register"></i><span class="nav-label">POS Terminal</span>
        <span class="nav-badge green">Live</span>
      </a>
      <a class="nav-item" href="javascript:void(0)" onclick="openDashOrdersModal()" data-tip="Orders">
        <i class="fa-solid fa-receipt"></i><span class="nav-label">Orders</span>
        <span class="nav-badge" id="globalOrdersNavBadge">{{ $todayOrdersCount }}</span>
      </a>
      <a class="nav-item" href="/admin/tables" data-path="/admin/tables" data-tip="Table Manager">
        <i class="fa-solid fa-table-cells-large"></i><span class="nav-label">Table Manager</span>
      </a>
      <a class="nav-item" href="/kitchen" data-path="/kitchen" data-tip="Kitchen Display" target="_blank">
        <i class="fa-solid fa-fire-burner"></i><span class="nav-label">Kitchen Display</span>
        <span class="text-[10px] text-emerald-600 font-mono ml-auto"><i class="fa-solid fa-arrow-up-right-from-square"></i></span>
      </a>
    </div>

    <div class="mb-4">
      <div class="sidebar-section-label text-[10px] font-mono uppercase tracking-wider text-[var(--text-muted)] px-3 mb-1.5">Catalog & Stock</div>
      <a class="nav-item" href="/admin/menu-manager" data-path="/admin/menu-manager" data-tip="Menu Items">
        <i class="fa-solid fa-bowl-food"></i><span class="nav-label">Menu Catalog</span>
      </a>
      <a class="nav-item" href="/admin/inventory" data-path="/admin/inventory" data-tip="Inventory Stock">
        <i class="fa-solid fa-boxes-stacked"></i><span class="nav-label">Inventory & Stock</span>
      </a>
      <a class="nav-item" href="/admin/coupons" data-path="/admin/coupons" data-tip="Offers & Deals">
        <i class="fa-solid fa-tag"></i><span class="nav-label">Offers & Deals</span>
      </a>
    </div>

    <div class="mb-4">
      <div class="sidebar-section-label text-[10px] font-mono uppercase tracking-wider text-[var(--text-muted)] px-3 mb-1.5">Intelligence & Analytics</div>
      <a class="nav-item" href="/admin/sales-report" data-path="/admin/sales-report" data-tip="Sales Analytics">
        <i class="fa-solid fa-chart-line"></i><span class="nav-label">Sales Intelligence</span>
      </a>
      <a class="nav-item" href="/admin/expenses" data-path="/admin/expenses" data-tip="Expenses">
        <i class="fa-solid fa-file-invoice-dollar"></i><span class="nav-label">Expenses & P&L</span>
      </a>
      <a class="nav-item" href="/admin/customers" data-path="/admin/customers" data-tip="Customers & CRM">
        <i class="fa-solid fa-users"></i><span class="nav-label">Customers & Loyalty</span>
      </a>
      <a class="nav-item" href="/admin/qr-generator" data-path="/admin/qr-generator" data-tip="QR Generator">
        <i class="fa-solid fa-qrcode"></i><span class="nav-label">QR Posters</span>
      </a>
    </div>

    <div class="mb-2">
      <div class="sidebar-section-label text-[10px] font-mono uppercase tracking-wider text-[var(--text-muted)] px-3 mb-1.5">Administration</div>
      <a class="nav-item" href="/admin/staff" data-path="/admin/staff" data-tip="Staff & Roles">
        <i class="fa-solid fa-user-shield"></i><span class="nav-label">Staff & Roles</span>
      </a>
      <a class="nav-item" href="/admin/settings" data-path="/admin/settings" data-tip="System Settings">
        <i class="fa-solid fa-sliders"></i><span class="nav-label">System Settings</span>
      </a>
      <a class="nav-item" href="/admin/feedback" data-path="/admin/feedback" data-tip="Feedback & Reviews">
        <i class="fa-solid fa-star"></i><span class="nav-label">Reviews & CSAT</span>
      </a>
    </div>
  </div>

  <div class="p-3 border-t border-[var(--hairline)] flex-shrink-0">
    <div class="user-card flex items-center gap-2.5 p-2 rounded-xl hover:bg-[var(--sidebar-hover)] transition">
      <div class="w-8 h-8 rounded-full bg-[var(--brand)] text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
        {{ strtoupper(substr(Auth::user()?->name ?? 'Admin', 0, 1)) }}
      </div>
      <div class="user-info flex-1 min-w-0">
        <h4 class="text-xs font-semibold text-[var(--text-primary)] truncate">{{ Auth::user()?->name ?? 'Administrator' }}</h4>
        <p class="text-[11px] text-[var(--text-muted)] capitalize truncate">{{ Auth::user()?->role ?? 'Operator' }}</p>
      </div>
      <form method="POST" action="{{ route('auth.logout') }}" id="dashLogoutForm" class="m-0">
        @csrf
        <button type="submit" title="Logout" class="w-7 h-7 rounded-lg border border-[var(--hairline)] text-[var(--text-muted)] hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 flex items-center justify-center text-xs transition">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
      </form>
    </div>
  </div>
</aside>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MAIN: Executive Operations Center (Porcelain & Linear Style)    -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="flex items-center gap-3">
      <button class="md:hidden w-8 h-8 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-[var(--text-primary)]" onclick="toggleSidebar()">
        <i class="fa-solid fa-bars text-sm"></i>
      </button>
      <div>
        <h1 class="text-sm font-semibold tracking-tight text-[var(--text-primary)] flex items-center gap-2">
          <span>Executive Dashboard</span>
          <span class="text-[10px] font-mono text-stone-500 uppercase px-2 py-0.5 rounded-full border border-[var(--hairline)] bg-[var(--panel)]">Fine Dining Edition</span>
        </h1>
        <div class="text-[11px] text-[var(--text-muted)]" id="topbarDate">{{ now()->format('l, M d, Y') }} • Evening Shift</div>
      </div>
    </div>

    <div class="ml-auto flex items-center gap-2 sm:gap-3">
      <!-- Live Clock Pill (Monospaced & Tabular) -->
      <div id="liveClockBadge" class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-medium border border-[var(--hairline)] bg-[var(--surface)] text-[var(--text-primary)] shadow-subtle tabular-nums">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>--:--:-- --</span>
      </div>

      <!-- Weather Badge -->
      <div id="localWeatherBadge" class="hidden lg:flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border border-[var(--hairline)] bg-[var(--surface)] text-[var(--text-muted)] shadow-subtle">
        <i class="fa-solid fa-cloud-sun text-amber-500 text-xs"></i> Patan, GJ • 34°C
      </div>

      <!-- Vocal Soundbox Indicator Pill -->
      <button type="button" onclick="announcePaymentVoice('Test', 250, 'cash')" class="flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium border border-emerald-500/30 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-500/15 transition cursor-pointer shadow-subtle" title="Click to test Soundbox voice broadcast">
        <div class="flex items-center gap-0.5 h-3">
          <span class="soundwave-bar"></span>
          <span class="soundwave-bar"></span>
          <span class="soundwave-bar"></span>
          <span class="soundwave-bar"></span>
        </div>
        <span class="font-mono text-[11px] font-semibold tracking-wide">Soundbox Active</span>
      </button>

      <!-- Theme Switcher -->
      <button type="button" id="posThemeToggleBtn" title="Toggle Light/Dark Theme" onclick="toggleGlobalSystemTheme()" class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium border border-[var(--hairline)] bg-[var(--surface)] text-[var(--text-primary)] hover:bg-[var(--sidebar-hover)] transition cursor-pointer shadow-subtle">
        <i id="posThemeIcon" class="fa-solid fa-sun text-amber-500 text-xs"></i>
        <span id="posThemeText" class="text-[11px] font-semibold">Light</span>
      </button>

      <!-- Notification Bell -->
      <button type="button" class="relative w-8 h-8 rounded-full border border-[var(--hairline)] bg-[var(--surface)] hover:bg-[var(--sidebar-hover)] text-[var(--text-muted)] hover:text-[var(--text-primary)] flex items-center justify-center transition" onclick="openModal('notifModal')" title="Notifications">
        <i class="fa-regular fa-bell text-xs"></i>
        <span class="notif-dot absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-600 border border-white dark:border-stone-900"></span>
      </button>

      <!-- Fullscreen Toggle -->
      <button type="button" class="hidden sm:flex w-8 h-8 rounded-full border border-[var(--hairline)] bg-[var(--surface)] hover:bg-[var(--sidebar-hover)] text-[var(--text-muted)] hover:text-[var(--text-primary)] items-center justify-center transition" onclick="toggleFull()" title="Full Screen">
        <i class="fa-solid fa-expand text-xs"></i>
      </button>
    </div>
  </header>

  <!-- Dashboard Scrollable Body -->
  <main class="flex-1 p-5 sm:p-7 max-w-[1600px] w-full mx-auto space-y-6">

    @php
      $currentRest = \App\Models\Restaurant::find(Auth::user()?->restaurant_id);
      $restStatus = strtolower(trim($currentRest?->subscription_status ?? 'active'));
      $expiryDate = $currentRest?->subscription_expires_at ?? $currentRest?->trial_ends_at;
      $daysRemaining = $expiryDate ? \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($expiryDate), false) : null;
    @endphp

    @if($restStatus === 'grace_period')
      <div class="bento-card bg-amber-500/10 border-amber-500/30 p-4 sm:p-5 flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg flex-shrink-0">
            <i class="fa-solid fa-triangle-exclamation"></i>
          </div>
          <div>
            <h4 class="text-sm font-semibold text-amber-800 dark:text-amber-300">Subscription in Grace Period</h4>
            <p class="text-xs text-amber-900/70 dark:text-amber-400/80">{{ $currentRest->getCustomerFacingGracePeriodMessage() }}</p>
          </div>
        </div>
        <a href="https://wa.me/917777938338?text={{ urlencode('Hello DineFlow Team, I need to renew my restaurant subscription for ' . ($currentRest->name ?? '')) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
          <i class="fa-brands fa-whatsapp text-sm"></i> Renew on WhatsApp
        </a>
      </div>
    @elseif($restStatus === 'expiring' || ($daysRemaining !== null && $daysRemaining >= 0 && $daysRemaining <= 3))
      <div class="bento-card bg-orange-500/10 border-orange-500/30 p-4 sm:p-5 flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-orange-500/20 text-orange-600 dark:text-orange-400 flex items-center justify-center text-lg flex-shrink-0">
            <i class="fa-solid fa-clock"></i>
          </div>
          <div>
            <h4 class="text-sm font-semibold text-orange-800 dark:text-orange-300">Subscription Expiring Soon</h4>
            <p class="text-xs text-orange-900/70 dark:text-orange-400/80">Your subscription will expire in {{ max(0, intval($daysRemaining)) }} day(s). Renew now to avoid interruption.</p>
          </div>
        </div>
        <a href="https://wa.me/917777938338?text={{ urlencode('Hello DineFlow Team, I want to renew subscription for ' . ($currentRest->name ?? '')) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-2 transition shadow-sm">
          <i class="fa-brands fa-whatsapp text-sm"></i> Renew on WhatsApp
        </a>
      </div>
    @endif

    <!-- ═════════════════════════════════════════════════════════════ -->
    <!-- HERO COMMAND BAR: Hospitality Overview & Instant POS Links    -->
    <!-- ═════════════════════════════════════════════════════════════ -->
    <div class="bento-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-br from-[var(--surface)] to-[var(--panel)]">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-[var(--brand)] text-white flex items-center justify-center text-xl font-bold shadow-elevated flex-shrink-0">
          <i class="fa-solid fa-wine-glass-empty"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[var(--text-primary)]">
              Welcome back, {{ Auth::user()?->name ?? 'Manager' }}
            </h2>
            <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 font-mono font-medium border border-emerald-200 dark:border-emerald-800">SYSTEM OPERATIONAL</span>
          </div>
          <p class="text-xs sm:text-sm text-[var(--text-muted)] mt-1">
            Real-time floor intelligence, dynamic menu profitability, and Vocal Soundbox dispatch center.
          </p>
        </div>
      </div>

      <!-- Linear Style Quick Actions Bar -->
      <div class="flex items-center flex-wrap gap-2.5">
        <a href="/pos" class="px-4 py-2.5 rounded-xl bg-[var(--brand)] hover:bg-[var(--brand-hover)] text-white text-xs font-semibold flex items-center gap-2 transition shadow-sm active:scale-[0.98]">
          <i class="fa-solid fa-cash-register"></i> + New POS Bill
        </a>
        <button type="button" onclick="openDashOrdersModal('all')" class="px-3.5 py-2.5 rounded-xl border border-[var(--hairline)] bg-[var(--surface)] hover:bg-[var(--sidebar-hover)] text-[var(--text-primary)] text-xs font-medium flex items-center gap-1.5 transition active:scale-[0.98]">
          <i class="fa-solid fa-receipt text-[var(--text-muted)]"></i> Orders
        </button>
        <button type="button" onclick="openDashOffersModal()" class="px-3.5 py-2.5 rounded-xl border border-[var(--hairline)] bg-[var(--surface)] hover:bg-[var(--sidebar-hover)] text-[var(--text-primary)] text-xs font-medium flex items-center gap-1.5 transition active:scale-[0.98]">
          <i class="fa-solid fa-tag text-amber-500"></i> Deals
        </button>
        <a href="/admin/tables" class="px-3.5 py-2.5 rounded-xl border border-[var(--hairline)] bg-[var(--surface)] hover:bg-[var(--sidebar-hover)] text-[var(--text-primary)] text-xs font-medium flex items-center gap-1.5 transition active:scale-[0.98]">
          <i class="fa-solid fa-table-cells-large text-[var(--text-muted)]"></i> Tables
        </a>
        <a href="/kitchen" target="_blank" class="px-3.5 py-2.5 rounded-xl border border-[var(--hairline)] bg-[var(--surface)] hover:bg-[var(--sidebar-hover)] text-[var(--text-primary)] text-xs font-medium flex items-center gap-1.5 transition active:scale-[0.98]">
          <i class="fa-solid fa-fire-burner text-rose-500"></i> KDS Screen
        </a>
      </div>
    </div>

    @if(isset($usageStats))
    <!-- SaaS Resource Quotas Bar -->
    <div class="bento-card px-5 py-3.5 flex items-center justify-between flex-wrap gap-4 text-xs">
      <div class="flex items-center gap-2.5">
        <span class="px-2.5 py-0.5 rounded-full bg-[var(--brand-soft)] text-[var(--brand)] font-mono font-bold text-[10px] tracking-wider uppercase border border-[var(--hairline)]">
          {{ strtoupper($usageStats['plan_type'] ?? 'FREE') }} TIER
        </span>
        <span class="text-[var(--text-muted)] font-medium">Resource Consumption:</span>
      </div>
      <div class="flex items-center gap-4 sm:gap-6 flex-wrap font-mono text-[11px] text-[var(--text-muted)] tabular-nums">
        <div><strong class="text-[var(--text-primary)]">{{ $usageStats['usage']['tables']['current'] ?? 0 }}</strong>/{{ $usageStats['usage']['tables']['limit'] ?? 10 }} Tables</div>
        <div><strong class="text-[var(--text-primary)]">{{ $usageStats['usage']['staff']['current'] ?? 0 }}</strong>/{{ $usageStats['usage']['staff']['limit'] ?? 5 }} Staff</div>
        <div><strong class="text-[var(--text-primary)]">{{ $usageStats['usage']['menu_items']['current'] ?? 0 }}</strong>/{{ $usageStats['usage']['menu_items']['limit'] ?? 100 }} Dishes</div>
        <div><strong class="text-[var(--text-primary)]">{{ $usageStats['usage']['monthly_orders']['current'] ?? 0 }}</strong>/{{ $usageStats['usage']['monthly_orders']['limit'] ?? 500 }} Orders</div>
        <button type="button" onclick="alert('Enterprise plans available upon request.'); window.location.href='/pricing';" class="px-2.5 py-1 rounded-lg bg-[var(--brand)] text-white text-[11px] font-sans font-semibold hover:bg-[var(--brand-hover)] transition">
          Upgrade Plan
        </button>
      </div>
    </div>
    @endif

    <!-- ═════════════════════════════════════════════════════════════ -->
    <!-- BENTO GRID ROW 1: Revenue, Occupancy, Tickets, Soundbox Pill  -->
    <!-- ═════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">

      <!-- Card 1: Today's Revenue KPI -->
      <div class="bento-card p-5 relative overflow-hidden flex flex-col justify-between">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-[11px] font-mono tracking-wider uppercase text-[var(--text-muted)]">Net Revenue Today</div>
            <div class="text-3xl font-bold font-mono text-[var(--text-primary)] tracking-tight mt-1 tabular-nums">
              ₹ {{ number_format($todayRevenue) }}
            </div>
          </div>
          <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
            <i class="fa-solid fa-indian-rupee-sign"></i>
          </div>
        </div>
        <div class="mt-4 pt-3 border-t border-[var(--hairline)] flex items-center justify-between text-xs">
          <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-mono font-medium text-[11px]">
            <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> {{ $revenueDiffText }}
          </span>
          <span class="text-[11px] text-[var(--text-muted)]">Paid tickets</span>
        </div>
      </div>

      <!-- Card 2: Floor Occupancy Meter -->
      <div class="bento-card p-5 flex flex-col justify-between">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-[11px] font-mono tracking-wider uppercase text-[var(--text-muted)]">Floor Occupancy</div>
            <div class="text-3xl font-bold font-mono text-[var(--text-primary)] tracking-tight mt-1 tabular-nums">
              {{ $occupiedTablesCount }} <span class="text-base text-[var(--text-muted)] font-normal">/ {{ $totalTablesCount }} Tables</span>
            </div>
          </div>
          <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm border border-amber-500/20">
            <i class="fa-solid fa-chair"></i>
          </div>
        </div>
        
        <!-- Visual Occupancy Meter Bar -->
        <div class="mt-3">
          @php
            $occupancyRate = $totalTablesCount > 0 ? min(100, round(($occupiedTablesCount / $totalTablesCount) * 100)) : 0;
          @endphp
          <div class="w-full bg-stone-200 dark:bg-stone-800 rounded-full h-2 overflow-hidden">
            <div class="bg-[var(--brand)] h-2 rounded-full transition-all duration-500" style="width: {{ $occupancyRate }}%;"></div>
          </div>
        </div>

        <div class="mt-3 pt-3 border-t border-[var(--hairline)] flex items-center justify-between text-xs">
          <span class="text-[11px] text-[var(--text-muted)] font-medium">
            <i class="fa-solid fa-users text-amber-500 mr-1"></i> {{ $activeCustomers }} Guests Dining
          </span>
          <span class="font-mono text-[11px] font-semibold text-[var(--text-primary)]">{{ $occupancyRate }}% Full</span>
        </div>
      </div>

      <!-- Card 3: Today's Orders Velocity -->
      <div class="bento-card p-5 flex flex-col justify-between">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-[11px] font-mono tracking-wider uppercase text-[var(--text-muted)]">Completed Tickets</div>
            <div class="text-3xl font-bold font-mono text-[var(--text-primary)] tracking-tight mt-1 tabular-nums">
              {{ $todayOrdersCount }}
            </div>
          </div>
          <div class="w-10 h-10 rounded-xl bg-[var(--brand-soft)] text-[var(--brand)] flex items-center justify-center text-sm border border-[var(--hairline)]">
            <i class="fa-solid fa-receipt"></i>
          </div>
        </div>
        <div class="mt-4 pt-3 border-t border-[var(--hairline)] flex items-center justify-between text-xs">
          <span class="text-[11px] font-mono font-medium text-emerald-600 dark:text-emerald-400">
            {{ $ordersDiffText }}
          </span>
          <button type="button" onclick="openDashOrdersModal('all')" class="text-[11px] text-[var(--brand)] hover:underline font-medium">
            View Live Stack →
          </button>
        </div>
      </div>

      <!-- Card 4: Vocal Soundbox Active Widget -->
      <div class="bento-card p-5 flex flex-col justify-between border-emerald-500/30 bg-gradient-to-br from-[var(--surface)] to-emerald-500/5">
        <div class="flex items-start justify-between">
          <div>
            <div class="text-[11px] font-mono tracking-wider uppercase text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
              Vocal Soundbox
            </div>
            <div class="text-lg font-bold text-[var(--text-primary)] mt-1">
              Gujarati &amp; Hindi
            </div>
            <p class="text-[11px] text-[var(--text-muted)] mt-0.5">Instant audio alert on digital scan &amp; cash</p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm border border-emerald-500/20">
            <i class="fa-solid fa-volume-high"></i>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-[var(--hairline)] flex items-center justify-between">
          <span class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 uppercase font-semibold">Ready for Billing</span>
          <button type="button" onclick="announcePaymentVoice('Test', 250, 'online')" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-medium transition flex items-center gap-1">
            <i class="fa-solid fa-play text-[9px]"></i> Test Voice
          </button>
        </div>
      </div>

    </div>

    <!-- ═════════════════════════════════════════════════════════════ -->
    <!-- BENTO GRID ROW 2: Analytics Trajectory & Category Sales       -->
    <!-- ═════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

      <!-- 7-Day Revenue Trajectory -->
      <div class="bento-card p-6 lg:col-span-8 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-base font-semibold text-[var(--text-primary)] tracking-tight flex items-center gap-2">
              <i class="fa-solid fa-chart-line text-[var(--brand)]"></i> 7-Day Revenue Trajectory
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-0.5">Normalized indexed revenue trajectory for shift planning</p>
          </div>
          <span class="text-[11px] font-mono text-[var(--text-muted)] px-2 py-0.5 rounded-full border border-[var(--hairline)] bg-[var(--panel)]">INR (₹)</span>
        </div>
        <div class="h-[270px] relative w-full">
          <canvas id="salesTrendChart"></canvas>
        </div>
      </div>

      <!-- Category Sales Distribution -->
      <div class="bento-card p-6 lg:col-span-4 flex flex-col justify-between">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-base font-semibold text-[var(--text-primary)] tracking-tight flex items-center gap-2">
              <i class="fa-solid fa-chart-pie text-emerald-600"></i> Category Breakdown
            </h3>
            <p class="text-xs text-[var(--text-muted)] mt-0.5">Share of sales by cuisine &amp; menu section</p>
          </div>
        </div>
        <div class="h-[270px] relative w-full flex items-center justify-center">
          <canvas id="categorySalesChart"></canvas>
          <div id="dashCatEmptyState" class="hidden flex-col items-center justify-center text-center p-6 text-[var(--text-muted)] space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-[var(--panel)] border border-[var(--hairline)] flex items-center justify-center text-xl text-[var(--text-muted)]">
              <i class="fa-solid fa-chart-pie"></i>
            </div>
            <p class="text-xs font-medium text-[var(--text-primary)]">No Sales Recorded Today</p>
            <p class="text-[11px] text-[var(--text-muted)] max-w-[200px]">Category breakdown will appear dynamically once POS tickets are completed.</p>
          </div>
        </div>
      </div>

    </div>

    <!-- ═════════════════════════════════════════════════════════════ -->
    <!-- BENTO GRID ROW 3: AI Menu Engineering & Profitability Matrix -->
    <!-- ═════════════════════════════════════════════════════════════ -->
    <div class="bento-card p-6">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
        <div>
          <h3 class="text-base font-semibold text-[var(--text-primary)] tracking-tight flex items-center gap-2">
            <span>✨</span> AI Menu Engineering &amp; BCG Profitability Matrix
          </h3>
          <p class="text-xs text-[var(--text-muted)] mt-0.5">Automated classification of dishes (Popularity vs Profit Margin) to maximize menu gross yield.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap text-xs">
          <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30 font-medium text-[11px]">⭐ Star</span>
          <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/30 font-medium text-[11px]">🐎 Plowhorse</span>
          <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30 font-medium text-[11px]">🧩 Puzzle</span>
          <span class="px-2.5 py-1 rounded-full bg-stone-500/10 text-stone-700 dark:text-stone-400 border border-stone-500/30 font-medium text-[11px]">🐕 Dog</span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-[var(--hairline)] text-[11px] font-mono uppercase tracking-wider text-[var(--text-muted)]">
              <th class="py-3 px-3">Dish / Recipe</th>
              <th class="py-3 px-3">Category</th>
              <th class="py-3 px-3 font-mono">Retail Price</th>
              <th class="py-3 px-3 font-mono">Gross Margin</th>
              <th class="py-3 px-3 font-mono">Volume Sold</th>
              <th class="py-3 px-3">Quadrant</th>
              <th class="py-3 px-3">AI Recommendation</th>
            </tr>
          </thead>
          <tbody id="menuMatrixBody" class="divide-y divide-[var(--hairline)] text-[var(--text-primary)]">
            <tr>
              <td colspan="7" class="py-8 text-center text-[var(--text-muted)]">
                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Analyzing dish velocity and margins...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODALS PRESERVED: Zero-Breakage ID Fidelity                     -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- Notifications Modal -->
<div class="modal-overlay fixed inset-0 bg-stone-900/40 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity" id="notifModal">
  <div class="bg-[var(--surface)] border border-[var(--hairline)] rounded-2xl w-[480px] max-w-[92vw] shadow-deep overflow-hidden">
    <div class="px-5 py-4 border-b border-[var(--hairline)] flex items-center justify-between">
      <div class="flex items-center gap-2 text-sm font-semibold text-[var(--text-primary)]">
        <i class="fa-regular fa-bell text-[var(--brand)]"></i> Live Notifications
        <span id="notifCountBadge" class="hidden text-[10px] font-mono px-2 py-0.5 rounded-full bg-rose-600 text-white font-bold"></span>
      </div>
      <div class="flex items-center gap-2">
        <button onclick="refreshDashNotifications()" title="Refresh" class="w-7 h-7 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <i class="fa-solid fa-arrows-rotate"></i>
        </button>
        <button onclick="closeModal('notifModal')" class="w-7 h-7 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs text-[var(--text-muted)] hover:text-rose-600">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>
    <div class="p-5 max-h-[380px] overflow-y-auto space-y-2.5 modal-body">
      <div class="text-center py-8 text-xs text-[var(--text-muted)]">
        <i class="fa-solid fa-spinner fa-spin text-lg mb-2 block"></i> Loading notifications...
      </div>
    </div>
    <div class="px-5 py-3 border-t border-[var(--hairline)] bg-[var(--panel)] flex items-center justify-between text-xs">
      <button type="button" onclick="markAllNotificationsAsRead()" class="text-[var(--brand)] hover:underline font-medium">Mark all as read</button>
      <button type="button" onclick="closeModal('notifModal')" class="px-3 py-1.5 rounded-lg border border-[var(--hairline)] bg-[var(--surface)] font-medium">Close</button>
    </div>
  </div>
</div>

<!-- Recent Orders Modal -->
<div class="modal-overlay fixed inset-0 bg-stone-900/40 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity" id="dashOrdersModal">
  <div class="bg-[var(--surface)] border border-[var(--hairline)] rounded-2xl w-[920px] max-w-[94vw] max-h-[86vh] shadow-deep overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-[var(--hairline)] flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-2.5 text-base font-semibold text-[var(--text-primary)]" id="dashOrdersModalTitle">
        <i class="fa-solid fa-receipt text-[var(--brand)]"></i>
        <span>Live Restaurant Orders</span>
        <span id="dashOrdersCountBadge" class="text-xs font-mono px-2 py-0.5 rounded-full border border-[var(--hairline)] bg-[var(--panel)] text-[var(--text-muted)]">Loading...</span>
      </div>
      <div class="flex items-center gap-2">
        <button onclick="loadDashOrdersModalData()" title="Refresh" class="w-8 h-8 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs text-[var(--text-muted)] hover:text-[var(--text-primary)]">
          <i class="fa-solid fa-arrows-rotate"></i>
        </button>
        <button onclick="closeDashOrdersModal()" class="w-8 h-8 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs text-[var(--text-muted)] hover:text-rose-600">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>
    <div class="p-6 overflow-y-auto flex-1" id="dashOrdersModalBody">
      <div class="text-center py-12 text-xs text-[var(--text-muted)]">
        <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 block text-[var(--brand)]"></i> Fetching orders...
      </div>
    </div>
    <div class="px-6 py-3.5 border-t border-[var(--hairline)] bg-[var(--panel)] flex items-center justify-between text-xs flex-shrink-0">
      <span class="text-[var(--text-muted)]">Thermal receipts and A4 tax invoices are generated on-demand.</span>
      <button onclick="closeDashOrdersModal()" class="px-4 py-2 rounded-xl bg-[var(--brand)] text-white font-medium hover:bg-[var(--brand-hover)] transition">
        Close
      </button>
    </div>
  </div>
</div>

<!-- Admin Module Iframe Modal -->
<div class="modal-overlay fixed inset-0 bg-stone-900/40 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity" id="dashAdminModuleModal">
  <div class="bg-[var(--surface)] border border-[var(--hairline)] rounded-2xl w-[1320px] max-w-[96vw] h-[90vh] shadow-deep overflow-hidden flex flex-col">
    <div class="px-6 py-3.5 border-b border-[var(--hairline)] flex items-center justify-between flex-shrink-0">
      <div class="text-sm font-semibold text-[var(--text-primary)]" id="dashAdminModuleTitle">Admin Module</div>
      <div class="flex items-center gap-2">
        <button onclick="const f=document.getElementById('dashAdminModuleIframe'); if(f) f.contentWindow.location.reload();" class="w-8 h-8 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs">
          <i class="fa-solid fa-arrows-rotate"></i>
        </button>
        <button onclick="closeDashAdminModule()" class="w-8 h-8 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs hover:text-rose-600">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>
    <iframe id="dashAdminModuleIframe" src="about:blank" class="flex-1 w-full border-none bg-[var(--canvas)]"></iframe>
  </div>
</div>

<!-- Offers & Deals Modal -->
<div class="modal-overlay fixed inset-0 bg-stone-900/40 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity" id="dashOffersModal">
  <div class="bg-[var(--surface)] border border-[var(--hairline)] rounded-2xl w-[680px] max-w-[94vw] max-h-[85vh] shadow-deep overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-[var(--hairline)] flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-2 text-base font-semibold text-[var(--text-primary)]">
        <i class="fa-solid fa-tag text-amber-500"></i> Active Restaurant Offers
      </div>
      <button onclick="closeDashOffersModal()" class="w-8 h-8 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs hover:text-rose-600">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="p-6 overflow-y-auto flex-1 space-y-3">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div class="p-4 rounded-xl border border-[var(--hairline)] bg-[var(--panel)]">
          <div class="font-bold text-sm text-[var(--text-primary)]">WELCOME15</div>
          <div class="text-[var(--text-muted)] mt-0.5">Flat 15% discount for first-time guests</div>
        </div>
        <div class="p-4 rounded-xl border border-[var(--hairline)] bg-[var(--panel)]">
          <div class="font-bold text-sm text-[var(--text-primary)]">SAVE50</div>
          <div class="text-[var(--text-muted)] mt-0.5">₹50 off on orders above ₹300</div>
        </div>
        <div class="p-4 rounded-xl border border-[var(--hairline)] bg-[var(--panel)]">
          <div class="font-bold text-sm text-[var(--text-primary)]">VIP10</div>
          <div class="text-[var(--text-muted)] mt-0.5">10% off for verified loyalty guests</div>
        </div>
        <div class="p-4 rounded-xl border border-[var(--hairline)] bg-[var(--panel)]">
          <div class="font-bold text-sm text-[var(--text-primary)]">HAPPY10</div>
          <div class="text-[var(--text-muted)] mt-0.5">Happy hours 16:00 to 19:00 promotional rate</div>
        </div>
      </div>
    </div>
    <div class="px-6 py-3.5 border-t border-[var(--hairline)] bg-[var(--panel)] flex justify-end">
      <button onclick="closeDashOffersModal()" class="px-4 py-2 rounded-xl bg-[var(--brand)] text-white text-xs font-medium">Close</button>
    </div>
  </div>
</div>

<!-- Delivery Modal -->
<div class="modal-overlay fixed inset-0 bg-stone-900/40 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity" id="dashDeliveryModal">
  <div class="bg-[var(--surface)] border border-[var(--hairline)] rounded-2xl w-[780px] max-w-[94vw] max-h-[85vh] shadow-deep overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-[var(--hairline)] flex items-center justify-between flex-shrink-0">
      <div class="flex items-center gap-2 text-base font-semibold text-[var(--text-primary)]">
        <i class="fa-solid fa-motorcycle text-sky-500"></i> Delivery &amp; Takeaway Dispatch
      </div>
      <button onclick="closeDashDeliveryModal()" class="w-8 h-8 rounded-lg border border-[var(--hairline)] flex items-center justify-center text-xs hover:text-rose-600">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="p-6 overflow-y-auto flex-1" id="dashDeliveryModalBody">
      <div class="text-center py-10 text-xs text-[var(--text-muted)]">
        <i class="fa-solid fa-spinner fa-spin text-xl mb-2 block"></i> Loading delivery queue...
      </div>
    </div>
    <div class="px-6 py-3.5 border-t border-[var(--hairline)] bg-[var(--panel)] flex justify-end">
      <button onclick="closeDashDeliveryModal()" class="px-4 py-2 rounded-xl bg-[var(--brand)] text-white text-xs font-medium">Close</button>
    </div>
  </div>
</div>

@include('partials.order-management')

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- JAVASCRIPT: High-Performance Zero-Breakage Engine                -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<script>
// 1. Live Monospaced Clock Ticker
function updateLiveClock() {
  const clockEl = document.getElementById('liveClockBadge');
  if (clockEl) {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('en-US', { hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    clockEl.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>${timeStr}</span>`;
  }
}
updateLiveClock();
setInterval(updateLiveClock, 1000);

// 2. Chart.js Engine with Wine / Fine-Dining Palette
let salesTrendChartInstance = null;
let categorySalesChartInstance = null;

function initDashboardCharts() {
  if (typeof Chart === 'undefined') {
    setTimeout(initDashboardCharts, 250);
    return;
  }

  try {
    const isLight = document.body.classList.contains('light-theme') || document.documentElement.classList.contains('light-theme');
    const tickColor = isLight ? '#6B7280' : '#A1A1AA';
    const gridColor = isLight ? '#ECE6E4' : '#26262A';
    const brandColor = isLight ? '#9F1239' : '#FB7185';

    // 1. Sales Trend Chart
    const trendEl = document.getElementById('salesTrendChart');
    if (trendEl) {
      const ctxTrend = trendEl.getContext('2d');
      const trendGrad = ctxTrend.createLinearGradient(0, 0, 0, 260);
      trendGrad.addColorStop(0, isLight ? 'rgba(159, 18, 57, 0.20)' : 'rgba(251, 113, 133, 0.25)');
      trendGrad.addColorStop(1, 'rgba(159, 18, 57, 0.00)');

      const weeklyLabels = {!! json_encode($weeklySalesLabels) !!};
      let weeklyData = {!! json_encode($weeklySalesData) !!};

      if (!weeklyData || !Array.isArray(weeklyData) || weeklyData.length === 0) {
        weeklyData = [0, 0, 0, 0, 0, 0, 0];
      }

      if (salesTrendChartInstance) {
        salesTrendChartInstance.destroy();
      }

      salesTrendChartInstance = new Chart(ctxTrend, {
        type: 'line',
        data: {
          labels: weeklyLabels && weeklyLabels.length ? weeklyLabels : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          datasets: [{
            label: 'Net Revenue (₹)',
            data: weeklyData,
            borderColor: brandColor,
            borderWidth: 2.5,
            backgroundColor: trendGrad,
            fill: true,
            tension: 0.35,
            pointBackgroundColor: brandColor,
            pointBorderColor: isLight ? '#FFFFFF' : '#151517',
            pointBorderWidth: 2,
            pointRadius: 3.5,
            pointHoverRadius: 6
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: isLight ? '#111827' : '#FFFFFF',
              titleColor: isLight ? '#FFFFFF' : '#111827',
              bodyColor: isLight ? '#E5E7EB' : '#4B5563',
              titleFont: { family: 'Inter', size: 12, weight: '600' },
              bodyFont: { family: 'Geist Mono', size: 12, weight: '500' },
              padding: 10,
              cornerRadius: 8,
              callbacks: {
                label: function(context) {
                  return ' ₹ ' + Number(context.parsed.y).toLocaleString('en-IN');
                }
              }
            }
          },
          scales: {
            y: {
              grid: { color: gridColor },
              ticks: {
                color: tickColor,
                font: { family: 'Geist Mono', size: 11 },
                callback: function(val) { return '₹' + val; }
              }
            },
            x: {
              grid: { display: false },
              ticks: { color: tickColor, font: { family: 'Inter', size: 11, weight: '500' } }
            }
          }
        }
      });
    }

    // 2. Category Sales Chart
    const catEl = document.getElementById('categorySalesChart');
    const dashCatEmpty = document.getElementById('dashCatEmptyState');
    if (catEl) {
      const catLabels = {!! json_encode($categorySalesLabels) !!};
      let catData = {!! json_encode($categorySalesData) !!};
      const totalCatSales = (catData || []).reduce((a, b) => a + Number(b || 0), 0);

      if (categorySalesChartInstance) {
        categorySalesChartInstance.destroy();
        categorySalesChartInstance = null;
      }

      if (totalCatSales === 0 || !catLabels || !catLabels.length) {
        catEl.style.display = 'none';
        if (dashCatEmpty) dashCatEmpty.style.display = 'flex';
      } else {
        catEl.style.display = 'block';
        if (dashCatEmpty) dashCatEmpty.style.display = 'none';
        const ctxCat = catEl.getContext('2d');

        categorySalesChartInstance = new Chart(ctxCat, {
          type: 'doughnut',
          data: {
            labels: catLabels,
            datasets: [{
              data: catData,
              backgroundColor: [
                '#9F1239', // Velvet Wine
                '#10B981', // Emerald
                '#F59E0B', // Amber
                '#3B82F6', // Cobalt
                '#8B5CF6', // Violet
                '#EC4899', // Rose
                '#64748B'  // Slate
              ],
              borderColor: isLight ? '#FFFFFF' : '#151517',
              borderWidth: 2
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  color: isLight ? '#111827' : '#F4F4F5',
                  font: { family: 'Inter', size: 11, weight: '500' },
                  padding: 10,
                  usePointStyle: true,
                  pointStyle: 'circle'
                }
              },
              tooltip: {
                backgroundColor: isLight ? '#111827' : '#FFFFFF',
                titleColor: isLight ? '#FFFFFF' : '#111827',
                bodyColor: isLight ? '#E5E7EB' : '#4B5563',
                titleFont: { family: 'Inter', size: 12, weight: '600' },
                bodyFont: { family: 'Geist Mono', size: 12, weight: '500' },
                padding: 10,
                cornerRadius: 8,
                callbacks: {
                  label: function(context) {
                    return ' ' + context.label + ': ₹' + Number(context.parsed).toLocaleString('en-IN');
                  }
                }
              }
            },
            cutout: '72%'
          }
        });
      }
    }
  } catch (err) {
    console.error('Error initializing charts:', err);
  }
}

// 3. Menu Engineering BCG Matrix Loader
async function loadMenuMatrix() {
  const tbody = document.getElementById('menuMatrixBody');
  if (!tbody) return;

  try {
    const res = await fetch('/api/analytics/menu-engineering/1');
    const json = await res.json();
    if (json.success && Array.isArray(json.data) && json.data.length > 0) {
      renderMenuMatrix(json.data);
    } else {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" class="py-8 text-center text-[var(--text-muted)]">
            <div class="flex flex-col items-center justify-center space-y-1">
              <i class="fa-solid fa-utensils text-lg text-[var(--text-muted)] mb-1"></i>
              <span class="font-medium">No sales data recorded yet for BCG matrix</span>
              <span class="text-[11px]">Once orders are completed, items will automatically populate the matrix.</span>
            </div>
          </td>
        </tr>
      `;
    }
  } catch (err) {
    console.warn('Matrix fetch error:', err);
  }
}

function renderMenuMatrix(items) {
  const tbody = document.getElementById('menuMatrixBody');
  if (!tbody) return;

  const quadConfig = {
    'Star':      { badge: '⭐ Star',       class: 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/30' },
    'Plowhorse': { badge: '🐎 Plowhorse',  class: 'bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-500/30' },
    'Puzzle':    { badge: '🧩 Puzzle',     class: 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/30' },
    'Dog':       { badge: '🐕 Dog',        class: 'bg-stone-500/10 text-stone-700 dark:text-stone-400 border border-stone-500/30' }
  };

  tbody.innerHTML = items.map(item => {
    const q = quadConfig[item.matrix_quad] || { badge: item.matrix_quad, class: 'bg-stone-500/10 text-stone-600' };
    return `
      <tr class="hover:bg-[var(--sidebar-hover)] transition">
        <td class="py-3 px-3 font-semibold text-[var(--text-primary)]">${item.name}</td>
        <td class="py-3 px-3 text-[var(--text-muted)]">${item.category_name || 'General'}</td>
        <td class="py-3 px-3 font-mono font-medium text-[var(--text-primary)] tabular-nums">₹${item.price}</td>
        <td class="py-3 px-3 font-mono text-emerald-600 dark:text-emerald-400 font-medium tabular-nums">₹${item.margin} <span class="text-[10px] text-[var(--text-muted)]">(${item.margin_pct}%)</span></td>
        <td class="py-3 px-3 font-mono font-medium text-[var(--text-primary)] tabular-nums">${item.qty_sold} sold</td>
        <td class="py-3 px-3">
          <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold ${q.class}">
            ${q.badge}
          </span>
        </td>
        <td class="py-3 px-3 text-[11px] text-[var(--text-muted)]">${item.action_hint}</td>
      </tr>
    `;
  }).join('');
}

document.addEventListener("DOMContentLoaded", function() {
  initDashboardCharts();
  loadMenuMatrix();
});

window.addEventListener('themechange', () => {
  setTimeout(initDashboardCharts, 50);
});

// 4. Sidebar Controls
const DASHBOARD_RESTAURANT_ID = '{{ Auth::user()->restaurant_id ?? '' }}';
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

// 5. Fullscreen Toggle
function toggleFull() {
  const doc = document.documentElement;
  if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
    if (doc.requestFullscreen) doc.requestFullscreen();
    else if (doc.webkitRequestFullscreen) doc.webkitRequestFullscreen();
  } else {
    if (document.exitFullscreen) document.exitFullscreen();
    else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
  }
}

// 6. Vocal Soundbox Engine (Gujarati / Hindi / English)
let isPosVoiceMuted = localStorage.getItem('dineflow_voice_muted') === 'true';

function announcePaymentVoice(tableNumber, amount, method = 'online') {
  if (isPosVoiceMuted) return;
  if (!('speechSynthesis' in window)) return;
  
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
  
  const voices = window.speechSynthesis.getVoices();
  const guVoice = voices.find(v => v.lang.includes('gu') || v.name.includes('Gujarati') || v.lang.includes('hi') || v.lang.includes('IN'));
  if (guVoice) utterance.voice = guVoice;
  
  window.speechSynthesis.speak(utterance);
}

// 7. Modals Management
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) {
    modal.style.display = 'flex';
    requestAnimationFrame(() => { modal.style.opacity = '1'; });
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
  toast.className = 'fixed top-5 right-5 z-50 px-4 py-3 rounded-xl shadow-deep text-xs font-semibold flex items-center gap-2 border bg-[var(--surface)] text-[var(--text-primary)] border-[var(--hairline)]';
  toast.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500"></i><span>${msg}</span>`;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3200);
}

function markAllNotificationsAsRead() {
  const body = document.querySelector('#notifModal .modal-body');
  if (body) {
    body.innerHTML = `
      <div class="text-center py-8 text-xs text-[var(--text-muted)] space-y-1">
        <i class="fa-solid fa-bell-slash text-xl block mb-1"></i>
        <div class="font-semibold text-[var(--text-primary)]">All Caught Up!</div>
        <div>All notifications have been marked as read.</div>
      </div>
    `;
  }

  document.querySelectorAll('.notif-dot').forEach(dot => dot.style.display = 'none');
  const badge = document.getElementById('notifCountBadge');
  if (badge) badge.style.display = 'none';

  dashNotifDismissedIds = [...dashNotifAllIds];
  localStorage.setItem('dineflow_dismissed_notifs', JSON.stringify(dashNotifDismissedIds));
  showToast('Notifications marked as read');
  setTimeout(() => closeModal('notifModal'), 400);
}

// 8. Live Notification Poller
let dashNotifDismissedIds = [];
let dashNotifAllIds = [];
try { dashNotifDismissedIds = JSON.parse(localStorage.getItem('dineflow_dismissed_notifs') || '[]'); } catch(e) { dashNotifDismissedIds = []; }

function timeAgo(dateStr) {
  const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
  if (diff < 60) return 'Just now';
  if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
  if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
  return Math.floor(diff / 86400) + 'd ago';
}

function buildNotifCard(icon, iconColor, title, desc, timeStr, id) {
  return `
    <div class="p-3 rounded-xl border border-[var(--hairline)] bg-[var(--panel)] flex items-start gap-3 text-xs" data-notif-id="${id}">
      <div class="w-7 h-7 rounded-lg bg-[var(--surface)] border border-[var(--hairline)] ${iconColor} flex items-center justify-center flex-shrink-0">
        <i class="fa-solid ${icon} text-xs"></i>
      </div>
      <div class="flex-1 min-w-0">
        <div class="font-semibold text-[var(--text-primary)] truncate">${title}</div>
        <div class="text-[11px] text-[var(--text-muted)] mt-0.5 truncate">${desc}</div>
        <div class="text-[10px] text-[var(--text-muted)] font-mono mt-1"><i class="fa-regular fa-clock mr-1"></i>${timeStr}</div>
      </div>
    </div>
  `;
}

async function fetchLiveNotifications() {
  const notifications = [];
  try {
    const wcRes = await fetch(`/api/waiter-calls/restaurant/${DASHBOARD_RESTAURANT_ID}`);
    const wcData = await wcRes.json();
    if (wcData.success && wcData.data) {
      wcData.data.forEach(call => {
        if (call.status !== 'resolved') {
          notifications.push({
            id: 'wc-' + call.id,
            icon: 'fa-bell-concierge',
            iconColor: 'text-rose-500',
            title: 'Waiter Called — Table ' + (call.table_id || '?'),
            desc: 'Guest requested service',
            time: call.created_at
          });
        }
      });
    }
  } catch(e) {}

  try {
    const orRes = await fetch(`/api/orders/restaurant/${DASHBOARD_RESTAURANT_ID}`);
    const orData = await orRes.json();
    const orOrdersList = Array.isArray(orData.data) ? orData.data : (orData.data?.data || []);
    if (orData.success && Array.isArray(orOrdersList)) {
      const thirtyMinsAgo = Date.now() - (30 * 60 * 1000);
      orOrdersList.forEach(order => {
        const orderTime = new Date(order.created_at).getTime();
        if (orderTime > thirtyMinsAgo && order.status === 'pending') {
          notifications.push({
            id: 'order-new-' + order.id,
            icon: 'fa-receipt',
            iconColor: 'text-amber-500',
            title: 'New Order #' + order.id,
            desc: '₹' + Number(order.total_amount).toLocaleString('en-IN') + ' • Waiting for kitchen',
            time: order.created_at
          });
        }
      });
    }
  } catch(e) {}

  return notifications;
}

function renderNotifications(notifs) {
  const body = document.querySelector('#notifModal .modal-body');
  if (!body) return;

  dashNotifAllIds = notifs.map(n => n.id);
  const unreadNotifs = notifs.filter(n => !dashNotifDismissedIds.includes(n.id));

  const badge = document.getElementById('notifCountBadge');
  const dots = document.querySelectorAll('.notif-dot');

  if (unreadNotifs.length > 0) {
    if (badge) {
      badge.style.display = 'inline-flex';
      badge.innerText = unreadNotifs.length;
    }
    dots.forEach(dot => dot.style.display = 'block');
  } else {
    if (badge) badge.style.display = 'none';
    dots.forEach(dot => dot.style.display = 'none');
  }

  if (notifs.length === 0) {
    body.innerHTML = `
      <div class="text-center py-8 text-xs text-[var(--text-muted)] space-y-1">
        <i class="fa-regular fa-bell-slash text-xl block mb-1"></i>
        <div class="font-semibold text-[var(--text-primary)]">No New Alerts</div>
        <div>All operations running smoothly.</div>
      </div>
    `;
    return;
  }

  body.innerHTML = notifs.map(n => buildNotifCard(n.icon, n.iconColor, n.title, n.desc, timeAgo(n.time), n.id)).join('');
}

async function refreshDashNotifications() {
  try {
    const notifs = await fetchLiveNotifications();
    renderNotifications(notifs);
  } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
  refreshDashNotifications();
  setInterval(refreshDashNotifications, 20000);
});

// 9. Orders Modal Logic
let currentDashOrderFilter = 'all';

function openDashOrdersModal(filterType = 'all') {
  currentDashOrderFilter = filterType;
  const modal = document.getElementById('dashOrdersModal');
  const titleEl = document.getElementById('dashOrdersModalTitle');
  if (titleEl) {
    titleEl.innerHTML = `
      <i class="fa-solid fa-receipt text-[var(--brand)]"></i>
      <span>Live Restaurant Orders</span>
      <span id="dashOrdersCountBadge" class="text-xs font-mono px-2 py-0.5 rounded-full border border-[var(--hairline)] bg-[var(--panel)] text-[var(--text-muted)]">Loading...</span>
    `;
  }
  if (!modal) return;
  modal.style.display = 'flex';
  requestAnimationFrame(() => { modal.style.opacity = '1'; });
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

  body.innerHTML = '<div class="text-center py-12 text-xs text-[var(--text-muted)]"><i class="fa-solid fa-spinner fa-spin text-xl block mb-2 text-[var(--brand)]"></i> Loading tickets...</div>';

  try {
    const res = await fetch(`/api/orders/restaurant/${DASHBOARD_RESTAURANT_ID}`);
    const rawData = await res.json();
    let orders = Array.isArray(rawData) ? rawData : (Array.isArray(rawData.data) ? rawData.data : (rawData.data?.data || []));

    if (orders.length === 0) {
      if (badge) badge.innerText = '0 Tickets';
      body.innerHTML = `
        <div class="text-center py-12 text-xs text-[var(--text-muted)] space-y-2">
          <div class="w-12 h-12 rounded-2xl bg-[var(--panel)] border border-[var(--hairline)] flex items-center justify-center mx-auto text-lg">
            <i class="fa-solid fa-receipt"></i>
          </div>
          <div class="font-semibold text-sm text-[var(--text-primary)]">No Active Orders Found</div>
          <div>All order tickets for today will appear here in real-time.</div>
        </div>
      `;
      return;
    }

    if (badge) badge.innerText = `${orders.length} Tickets`;

    let html = `
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-[var(--hairline)] text-[11px] font-mono uppercase tracking-wider text-[var(--text-muted)]">
              <th class="py-2.5 px-3">Ticket ID</th>
              <th class="py-2.5 px-3">Table / Type</th>
              <th class="py-2.5 px-3 font-mono">Amount</th>
              <th class="py-2.5 px-3">Payment</th>
              <th class="py-2.5 px-3">Kitchen Status</th>
              <th class="py-2.5 px-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[var(--hairline)] text-[var(--text-primary)]">
    `;

    orders.forEach(o => {
      const isPaid = (o.payment_status === 'paid');
      const payBadge = isPaid
        ? '<span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono font-medium text-[10px] border border-emerald-500/20">PAID</span>'
        : '<span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 font-mono font-medium text-[10px] border border-amber-500/20">PENDING</span>';
      
      const tableName = o.table ? `Table ${o.table.table_number || o.table_id}` : (o.table_id && o.table_id != '0' ? `Table ${o.table_id}` : 'Takeaway / Delivery');

      html += `
        <tr class="hover:bg-[var(--sidebar-hover)] transition">
          <td class="py-3 px-3 font-mono font-semibold">#${o.id}</td>
          <td class="py-3 px-3 text-[var(--text-muted)] font-medium">${tableName}</td>
          <td class="py-3 px-3 font-mono font-bold tabular-nums">₹${parseFloat(o.total_amount || 0).toFixed(2)}</td>
          <td class="py-3 px-3">${payBadge}</td>
          <td class="py-3 px-3 font-semibold capitalize">${o.status || 'Received'}</td>
          <td class="py-3 px-3 text-right">
            ${typeof renderOrderActionsHtml === 'function' ? renderOrderActionsHtml(o) : `
              <div class="inline-flex items-center gap-1.5">
                <a href="/orders/${o.id}/receipt" target="_blank" class="px-2.5 py-1 rounded-lg border border-[var(--hairline)] hover:bg-[var(--sidebar-hover)] text-[11px] font-medium" title="Print Thermal Receipt"><i class="fa-solid fa-print"></i> KOT</a>
                <a href="/orders/${o.id}/invoice-a4" target="_blank" class="px-2.5 py-1 rounded-lg border border-[var(--hairline)] hover:bg-[var(--sidebar-hover)] text-[11px] font-medium text-emerald-600" title="A4 PDF Tax Invoice"><i class="fa-solid fa-file-invoice"></i> PDF</a>
              </div>
            `}
          </td>
        </tr>
      `;
    });

    html += `</tbody></table></div>`;
    body.innerHTML = html;
  } catch(e) {
    body.innerHTML = '<div class="text-center py-10 text-xs text-rose-500">Failed to load orders. Please refresh.</div>';
  }
}

// 10. Admin Module Iframe Modal
function openDashAdminModule(url, titleHtml) {
  const modal = document.getElementById('dashAdminModuleModal');
  const titleEl = document.getElementById('dashAdminModuleTitle');
  const iframe = document.getElementById('dashAdminModuleIframe');
  if (!modal || !iframe) return;
  titleEl.innerHTML = titleHtml || 'Admin Module';
  iframe.src = url;
  modal.style.display = 'flex';
  requestAnimationFrame(() => { modal.style.opacity = '1'; });
}
function closeDashAdminModule() {
  const modal = document.getElementById('dashAdminModuleModal');
  const iframe = document.getElementById('dashAdminModuleIframe');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; if (iframe) iframe.src = 'about:blank'; }, 200);
}

// 11. Offers Modal
function openDashOffersModal() {
  const modal = document.getElementById('dashOffersModal');
  if (!modal) return;
  modal.style.display = 'flex';
  requestAnimationFrame(() => { modal.style.opacity = '1'; });
}
function closeDashOffersModal() {
  const modal = document.getElementById('dashOffersModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

// 12. Delivery Modal
function openDashDeliveryModal() {
  const modal = document.getElementById('dashDeliveryModal');
  if (!modal) return;
  modal.style.display = 'flex';
  requestAnimationFrame(() => { modal.style.opacity = '1'; });
  loadDashDeliveryData();
}
function closeDashDeliveryModal() {
  const modal = document.getElementById('dashDeliveryModal');
  if (!modal) return;
  modal.style.opacity = '0';
  setTimeout(() => { modal.style.display = 'none'; }, 200);
}

async function loadDashDeliveryData() {
  const body = document.getElementById('dashDeliveryModalBody');
  if (!body) return;
  body.innerHTML = '<div class="text-center py-10 text-xs text-[var(--text-muted)]"><i class="fa-solid fa-spinner fa-spin text-xl block mb-2"></i> Loading delivery queue...</div>';
  try {
    const resp = await fetch(`/api/orders/restaurant/${DASHBOARD_RESTAURANT_ID}`);
    const rawData = await resp.json();
    const allOrders = Array.isArray(rawData) ? rawData : (rawData.data || []);
    const deliveryOrders = allOrders.filter(o => o.order_type === 'delivery' || o.order_type === 'takeaway');
    if (!deliveryOrders.length) {
      body.innerHTML = '<div class="text-center py-12 text-xs text-[var(--text-muted)]">No active takeaway or delivery orders.</div>';
      return;
    }
    let html = '<div class="space-y-2.5">';
    deliveryOrders.forEach(o => {
      html += `
        <div class="p-3.5 rounded-xl border border-[var(--hairline)] bg-[var(--panel)] flex items-center justify-between text-xs">
          <div>
            <div class="font-bold text-[var(--text-primary)]">#${o.id} • ${(o.order_type || 'Delivery').toUpperCase()}</div>
            <div class="text-[var(--text-muted)] mt-0.5 font-mono">₹${parseFloat(o.total_amount || 0).toFixed(2)}</div>
          </div>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-stone-200 dark:bg-stone-800 text-[var(--text-primary)]">${o.status}</span>
        </div>
      `;
    });
    html += '</div>';
    body.innerHTML = html;
  } catch(err) {
    body.innerHTML = '<div class="text-center py-10 text-xs text-rose-500">Failed to load delivery orders.</div>';
  }
}

// Global ESC Key Close
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeDashOrdersModal();
    closeDashAdminModule();
    closeDashOffersModal();
    closeDashDeliveryModal();
    closeModal('notifModal');
  }
});
</script>
</body>
</html>
