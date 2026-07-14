@extends('layouts.app')

@section('title', 'POS Terminal - RestoPOS')

@section('content')
<div class="pos-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
            <div class="brand-text">
                <h2>RestoPOS</h2>
                <span>POS Terminal v2.0</span>
            </div>
            <div class="sidebar-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
                <i class="fa-solid fa-chevron-left" id="sidebarToggleIcon"></i>
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="sidebar-section">
                <div class="sidebar-section-label">Main</div>
                <a class="nav-item" href="{{ route('dashboard') }}" title="Dashboard">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span class="nav-label">Dashboard</span>
                </a>
                <a class="nav-item active" href="{{ route('pos.terminal') }}" title="POS Terminal">
                    <i class="fa-solid fa-cash-register"></i>
                    <span class="nav-label">POS Terminal</span>
                    <span class="nav-badge green">Live</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-label">Operations</div>
                <a class="nav-item" href="#" title="Orders">
                    <i class="fa-solid fa-receipt"></i>
                    <span class="nav-label">Orders</span>
                    <span class="nav-badge">12</span>
                </a>
                <a class="nav-item" href="#" title="Tables">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span class="nav-label">Tables</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-label">Menu</div>
                <a class="nav-item" href="#" title="Menu Items">
                    <i class="fa-solid fa-utensils"></i>
                    <span class="nav-label">Menu Items</span>
                </a>
                <a class="nav-item" href="#" title="Categories">
                    <i class="fa-solid fa-layer-group"></i>
                    <span class="nav-label">Categories</span>
                </a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-label">Management</div>
                <a class="nav-item" href="#" title="Customers">
                    <i class="fa-solid fa-users"></i>
                    <span class="nav-label">Customers</span>
                </a>
                <a class="nav-item" href="#" title="Reports">
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="nav-label">Reports</span>
                </a>
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

    <!-- Main Content -->
    <div class="pos-main">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-title-group">
                <div class="topbar-title">POS Terminal</div>
                <div class="topbar-sub">{{ now()->format('l, d M Y') }} · Shift: Morning (8:00–16:00)</div>
            </div>
            <div class="topbar-btn topbar-mobile-menu" title="Menu" onclick="toggleSidebar()">
                <i class="fa-solid fa-bars"></i>
            </div>
            <div class="topbar-spacer"></div>
            <div class="topbar-btn" title="Full Screen" onclick="toggleFullscreen()">
                <i class="fa-solid fa-expand"></i>
            </div>
            <div style="width:1px;height:26px;background:var(--border);"></div>
            <div style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <div class="user-avatar" style="width:32px;height:32px;font-size:12px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}
                </div>
                <span style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ Auth::user()->name }}</span>
                <form action="{{ route('auth.logout') }}" method="POST" style="display:none;" id="logoutForm">
                    @csrf
                </form>
                <a href="#" onclick="document.getElementById('logoutForm').submit()" style="color:var(--text-muted);text-decoration:none;">
                    <i class="fa-solid fa-sign-out-alt" style="font-size:14px;"></i>
                </a>
            </div>
        </div>

        <!-- POS Body -->
        <div class="pos-wrapper">
            <!-- LEFT: Menu -->
            <div class="pos-left">
                <!-- Order Meta Bar -->
                <div class="order-meta">
                    <div class="meta-group">
                        <div class="delivery-tabs">
                            <div class="delivery-tab active" onclick="setDelivery(this,'dinein')">
                                <i class="fa-solid fa-chair"></i> Dine-In
                            </div>
                            <div class="delivery-tab" onclick="setDelivery(this,'takeaway')">
                                <i class="fa-solid fa-bag-shopping"></i> Pickup
                            </div>
                            <div class="delivery-tab" onclick="setDelivery(this,'delivery')">
                                <i class="fa-solid fa-motorcycle"></i> Delivery
                            </div>
                        </div>
                    </div>

                    <div class="meta-group" id="tableGroup">
                        <button type="button" class="meta-select meta-picker-btn" onclick="openModal('tableModal')">
                            <span class="meta-picker-inline">
                                <i class="fa-solid fa-table-cells"></i>
                                <span class="meta-picker-value">T-04</span>
                            </span>
                            <i class="fa-solid fa-chevron-down" style="font-size:10px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Search + Category -->
                <div class="menu-controls">
                    <div class="search-box">
                        <i class="fa-solid fa-search"></i>
                        <input class="search-input" placeholder="Search items…" oninput="filterItems(this.value)" />
                    </div>
                </div>

                <!-- Menu Grid -->
                <div class="menu-grid" id="menuGrid">
                    <!-- Sample items (will be populated from database) -->
                    @for ($i = 1; $i <= 12; $i++)
                        <div class="item-card" onclick="addToCart({{ $i }})">
                            <div class="item-img">🍕</div>
                            <div class="item-body">
                                <div class="item-name">Margherita Pizza</div>
                                <div class="item-footer">
                                    <div class="item-price">₹ {{ 250 + ($i * 10) }}<small>.00</small></div>
                                    <div class="item-footer-actions">
                                        <button class="item-add-btn" onclick="event.stopPropagation(); addToCart({{ $i }})">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- RIGHT: Cart -->
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
                        <div class="topbar-btn" title="Hold Order">
                            <i class="fa-solid fa-pause"></i>
                        </div>
                        <div class="topbar-btn" title="Print KOT">
                            <i class="fa-solid fa-print"></i>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="customer-strip">
                    <div class="cust-avatar">WO</div>
                    <div class="cust-info">
                        <h5>Walk-In Customer</h5>
                        <p>No phone · No email</p>
                    </div>
                    <span class="cust-change" onclick="openModal('customerModal')">Change</span>
                </div>

                <!-- Cart Items -->
                <div class="cart-items" id="cartItems">
                    <!-- Will be populated by JavaScript -->
                </div>

                <!-- Cart Note -->
                <div class="cart-note-area">
                    <input class="cart-note-input" placeholder="Add order notes (allergies, special requests…)" />
                </div>

                <!-- Totals -->
                <div class="cart-totals">
                    <div class="tot-row">
                        <span class="label">Subtotal</span>
                        <span class="val" id="subtotal">₹ 0.00</span>
                    </div>
                    <div class="tot-row">
                        <span class="label">Tax (18%)</span>
                        <span class="val" id="tax">₹ 0.00</span>
                    </div>
                    <hr class="tot-divider"/>
                    <div class="tot-row grand">
                        <span class="label">Grand Total</span>
                        <span class="val" id="grandTotal">₹ 0.00</span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="payment-section">
                    <div class="payment-label">Payment Method</div>
                    <div class="pay-methods">
                        <div class="pay-method active" onclick="selectPay(this)">
                            <i class="fa-solid fa-money-bill-wave"></i><span>Cash</span>
                        </div>
                        <div class="pay-method" onclick="selectPay(this)">
                            <i class="fa-solid fa-credit-card"></i><span>Card</span>
                        </div>
                        <div class="pay-method" onclick="selectPay(this)">
                            <i class="fa-brands fa-google-pay"></i><span>UPI</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="cart-actions">
                    <button class="btn-checkout" onclick="checkout()">
                        <i class="fa-solid fa-check-circle"></i> Place Order &nbsp;·&nbsp; <span id="btnTotal">₹ 0.00</span>
                    </button>
                    <div class="cart-btns-row">
                        <button class="btn-outline" onclick="printBill()">
                            <i class="fa-solid fa-print"></i> Print
                        </button>
                        <button class="btn-outline danger" onclick="clearCart()">
                            <i class="fa-solid fa-trash"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
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
            <input class="cust-search" placeholder="Search by name or phone…" />
            <div class="cust-list" id="custList">
                <div class="cust-row" onclick="selectCustomer(this)">
                    <div class="cr-avatar">RK</div>
                    <div class="cr-info"><h5>Rahul Kumar</h5><p>+91 98765 43210</p></div>
                </div>
            </div>
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
            <div class="table-grid">
                @for ($i = 1; $i <= 12; $i++)
                    <div class="table-cell available" onclick="selectTable(this,'T-{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}')">
                        T-{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}<span>{{ [2,4,6,8][rand(0,3)] }} PAX</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary: #4F46E5;
    --primary-light: #EEF2FF;
    --primary-dark: #3730A3;
    --success: #10B981;
    --danger: #EF4444;
    --danger-light: #FEF2F2;
    --warning: #F59E0B;
    --sidebar-bg: #1E1B4B;
    --sidebar-hover: #312E81;
    --sidebar-active: #4F46E5;
    --bg: #F8FAFC;
    --card: #FFFFFF;
    --border: #E2E8F0;
    --text-primary: #1E293B;
    --text-secondary: #64748B;
    --text-muted: #94A3B8;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.06);
    --shadow-md: 0 4px 6px rgba(0,0,0,.05);
    --shadow-lg: 0 10px 15px rgba(0,0,0,.07);
    --radius-sm: 6px;
    --radius-md: 10px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body.pos-full-screen { height: 100vh; overflow: hidden; }

.pos-container {
    display: flex;
    height: 100vh;
    background: var(--bg);
    font-family: 'Inter', sans-serif;
    color: var(--text-primary);
}

/* ─── SIDEBAR ─────────────────────────────── */
.sidebar {
    width: 240px;
    background: var(--sidebar-bg);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: width .25s;
    flex-shrink: 0;
}

.sidebar-brand {
    padding: 14px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid rgba(255,255,255,.08);
    flex-shrink: 0;
    min-height: 58px;
}

.brand-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
    flex-shrink: 0;
}

.brand-text {
    flex: 1;
    overflow: hidden;
}

.brand-text h2 {
    font-size: 15px;
    font-weight: 700;
    color: white;
}

.brand-text span {
    font-size: 11px;
    color: rgba(255,255,255,.45);
    display: block;
}

.sidebar-toggle {
    width: 28px;
    height: 28px;
    background: rgba(255,255,255,.12);
    border: none;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: white;
    transition: background .15s;
}

.sidebar-toggle:hover { background: rgba(255,255,255,.25); }

.sidebar-nav {
    flex: 1;
    overflow-y: auto;
    padding: 12px 8px;
}

.sidebar-section {
    margin-bottom: 20px;
}

.sidebar-section-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    color: rgba(255,255,255,.3);
    padding: 8px 12px;
    letter-spacing: 1px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: var(--radius-sm);
    color: rgba(255,255,255,.55);
    cursor: pointer;
    font-size: 13.5px;
    font-weight: 500;
    transition: all .18s;
    text-decoration: none;
    white-space: nowrap;
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

.sidebar-footer {
    flex-shrink: 0;
    padding: 14px 8px;
    border-top: 1px solid rgba(255,255,255,.07);
}

.user-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: background .15s;
}

.user-card:hover { background: var(--sidebar-hover); }

.user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 13px;
    flex-shrink: 0;
}

.user-info {
    min-width: 0;
}

.user-info h4 {
    font-size: 13px;
    font-weight: 600;
    color: white;
}

.user-info p {
    font-size: 11px;
    color: rgba(255,255,255,.4);
}

/* ─── MAIN CONTENT ─────────────────────────── */
.pos-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.topbar {
    background: var(--card);
    border-bottom: 1px solid var(--border);
    padding: 0 24px;
    height: 58px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

.topbar-title-group {
    display: flex;
    flex-direction: column;
}

.topbar-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}

.topbar-sub {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 1px;
}

.topbar-spacer { flex: 1; }

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
}

.topbar-btn:hover {
    background: var(--primary-light);
    color: var(--primary);
    border-color: var(--primary);
}

.topbar-mobile-menu { display: none; }

/* ─── POS WRAPPER ─────────────────────────── */
.pos-wrapper {
    flex: 1;
    display: flex;
    overflow: hidden;
}

.pos-left {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: var(--bg);
}

.order-meta {
    display: flex;
    gap: 10px;
    padding: 12px 16px;
    background: var(--card);
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    align-items: center;
}

.meta-group { display: flex; gap: 8px; align-items: center; }

.delivery-tabs {
    display: flex;
    background: var(--bg);
    border-radius: var(--radius-sm);
    padding: 3px;
    border: 1px solid var(--border);
}

.delivery-tab {
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: var(--text-secondary);
    transition: all .15s;
    display: flex;
    align-items: center;
    gap: 5px;
}

.delivery-tab.active {
    background: var(--primary);
    color: white;
    box-shadow: var(--shadow-sm);
}

.meta-select {
    height: 32px;
    padding: 0 10px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 12.5px;
    color: var(--text-primary);
    background: var(--card);
    outline: none;
    cursor: pointer;
    transition: border-color .15s;
}

.meta-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79,70,229,.08);
}

.meta-picker-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 0 10px;
    min-height: 32px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--card);
    color: var(--text-secondary);
    font-size: 12.5px;
    cursor: pointer;
    transition: all .15s;
}

.meta-picker-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
}

.menu-controls {
    padding: 12px 16px 8px;
    display: flex;
    gap: 10px;
    background: var(--bg);
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    min-width: 200px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 13px;
}

.search-input {
    width: 100%;
    height: 36px;
    padding: 0 12px 0 34px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 13px;
    background: var(--card);
    outline: none;
    transition: border-color .15s;
}

.search-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79,70,229,.08);
}

.menu-grid {
    flex: 1;
    overflow-y: auto;
    padding: 4px 12px 12px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
    align-content: start;
}

.item-card {
    background: var(--card);
    border-radius: var(--radius-md);
    border: 1.5px solid var(--border);
    cursor: pointer;
    transition: all .18s;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.item-card:hover {
    border-color: var(--primary);
    box-shadow: 0 4px 12px rgba(79,70,229,.12);
    transform: translateY(-1px);
}

.item-img {
    height: 90px;
    background: linear-gradient(135deg,#f1f5f9,#e2e8f0);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
}

.item-body {
    padding: 8px 9px;
}

.item-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.3;
    margin-bottom: 6px;
}

.item-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.item-price {
    font-size: 13px;
    font-weight: 700;
    color: var(--primary);
}

.item-add-btn {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    border: none;
    background: var(--primary);
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: all .15s;
}

.item-add-btn:hover {
    background: var(--primary-dark);
    transform: scale(1.08);
}

/* ─── CART PANEL ──────────────────────────── */
.pos-right {
    width: 320px;
    min-width: 300px;
    background: var(--card);
    border-left: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.cart-header {
    padding: 12px 14px 10px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.cart-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.cart-count {
    background: var(--primary);
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
}

.cart-order-no {
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
}

.customer-strip {
    padding: 8px 14px;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
}

.cust-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--primary-light);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
}

.cust-info h5 {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-primary);
}

.cust-info p {
    font-size: 11px;
    color: var(--text-muted);
}

.cust-change {
    margin-left: auto;
    font-size: 11px;
    color: var(--primary);
    font-weight: 600;
    cursor: pointer;
}

.cart-items {
    flex: 1;
    overflow-y: auto;
    padding: 4px 0;
}

.cart-item {
    display: flex;
    padding: 8px 10px;
    border-bottom: 1px solid var(--border);
    gap: 8px;
    align-items: center;
}

.ci-info {
    flex: 1;
    min-width: 0;
}

.ci-name {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
}

.ci-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 6px;
}

.ci-controls {
    display: flex;
    align-items: center;
    gap: 5px;
    background: var(--bg);
    border-radius: 4px;
    padding: 2px;
}

.ci-btn {
    width: 24px;
    height: 24px;
    border: none;
    background: var(--primary);
    color: white;
    border-radius: 3px;
    font-size: 12px;
    cursor: pointer;
    font-weight: 600;
    transition: background .15s;
}

.ci-btn:hover { background: var(--primary-dark); }

.ci-qty {
    width: 24px;
    text-align: center;
    font-size: 11px;
    font-weight: 600;
}

.ci-price {
    font-size: 12px;
    font-weight: 700;
    color: var(--primary);
}

.cart-note-area {
    padding: 6px 14px;
    border-top: 1px solid var(--border);
}

.cart-note-input {
    width: 100%;
    height: 36px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0 10px;
    font-size: 12px;
    outline: none;
    transition: border-color .15s;
}

.cart-note-input:focus {
    border-color: var(--primary);
}

.cart-totals {
    padding: 8px 14px 4px;
    border-top: 1px solid var(--border);
}

.tot-row {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    margin-bottom: 5px;
}

.tot-row .label { color: var(--text-secondary); }
.tot-row .val { font-weight: 600; color: var(--text-primary); }

.tot-divider {
    border: none;
    border-top: 1px dashed var(--border);
    margin: 8px 0;
}

.tot-row.grand .label {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
}

.tot-row.grand .val {
    font-size: 15px;
    font-weight: 800;
    color: var(--primary);
}

.payment-section {
    padding: 8px 14px;
    border-top: 1px solid var(--border);
}

.payment-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--text-muted);
    margin-bottom: 8px;
}

.pay-methods {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.pay-method {
    flex: 1;
    min-width: 60px;
    padding: 7px 6px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    text-align: center;
    cursor: pointer;
    transition: all .15s;
}

.pay-method i {
    font-size: 16px;
    color: var(--text-muted);
    display: block;
    margin-bottom: 3px;
}

.pay-method span {
    font-size: 10px;
    font-weight: 600;
    color: var(--text-muted);
    display: block;
}

.pay-method:hover { border-color: var(--primary); }
.pay-method.active {
    border-color: var(--primary);
    background: var(--primary-light);
}

.pay-method.active i, .pay-method.active span {
    color: var(--primary);
}

.cart-actions {
    padding: 10px 14px 12px;
    display: flex;
    gap: 8px;
    flex-direction: column;
    flex-shrink: 0;
}

.btn-checkout {
    width: 100%;
    height: 44px;
    border: none;
    border-radius: var(--radius-md);
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all .18s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(79,70,229,.3);
}

.btn-checkout:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(79,70,229,.35);
}

.cart-btns-row {
    display: flex;
    gap: 8px;
}

.btn-outline {
    flex: 1;
    height: 34px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--card);
    color: var(--text-secondary);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-outline:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
}

.btn-outline.danger:hover {
    border-color: var(--danger);
    color: var(--danger);
    background: var(--danger-light);
}

/* ─── MODALS ──────────────────────────────── */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    backdrop-filter: blur(3px);
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s;
}

.modal-overlay.show {
    opacity: 1;
    pointer-events: all;
}

.modal {
    background: var(--card);
    border-radius: var(--radius-md);
    padding: 0;
    width: 460px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 48px rgba(0,0,0,.18);
    transform: scale(.95);
    transition: transform .2s;
}

.modal-overlay.show .modal { transform: scale(1); }

.modal-header {
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    font-size: 16px;
    font-weight: 700;
}

.modal-close {
    width: 30px;
    height: 30px;
    border: none;
    background: var(--bg);
    border-radius: 6px;
    cursor: pointer;
    color: var(--text-secondary);
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .12s;
}

.modal-close:hover {
    background: var(--danger-light);
    color: var(--danger);
}

.modal-body {
    padding: 18px 22px;
}

.modal-footer {
    padding: 14px 22px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

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

.cust-search {
    width: 100%;
    height: 38px;
    padding: 0 12px;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 13px;
    outline: none;
    margin-bottom: 14px;
}

.cust-search:focus {
    border-color: var(--primary);
}

.cust-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.cust-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all .15s;
}

.cust-row:hover {
    border-color: var(--primary);
    background: var(--primary-light);
}

.cr-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg,#4F46E5,#7C3AED);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}

.cr-info h5 {
    font-size: 13px;
    font-weight: 600;
}

.cr-info p {
    font-size: 11px;
    color: var(--text-muted);
}

.table-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 8px;
}

.table-cell {
    aspect-ratio: 1;
    border: 2px solid var(--border);
    border-radius: var(--radius-sm);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s;
    font-size: 14px;
    font-weight: 700;
    color: var(--text-secondary);
}

.table-cell span {
    font-size: 10px;
    font-weight: 500;
    margin-top: 2px;
}

.table-cell.available:hover {
    border-color: var(--success);
    color: var(--success);
    background: var(--success-light);
}

.table-cell.occupied {
    background: #FEF2F2;
    border-color: var(--danger);
    color: var(--danger);
}

.table-cell.selected {
    background: var(--primary-light);
    border-color: var(--primary);
    color: var(--primary);
}

/* ─── RESPONSIVE ─────────────────────────── */
@media (max-width: 768px) {
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 300;
        transform: translateX(-100%);
        transition: transform .3s;
    }

    .sidebar.mobile-open { transform: translateX(0); }

    .topbar-mobile-menu { display: flex; }

    .pos-right {
        position: fixed;
        right: 0;
        top: 58px;
        bottom: 0;
        width: 320px;
        z-index: 299;
        transform: translateX(100%);
        transition: transform .3s;
    }

    .pos-right.cart-open { transform: translateX(0); }

    .menu-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .topbar {
        padding: 0 12px;
    }

    .topbar-title {
        font-size: 14px;
    }

    .topbar-sub {
        font-size: 11px;
    }

    .sidebar {
        width: 100%;
    }

    .pos-right {
        width: 100%;
    }

    .menu-grid {
        padding: 8px 8px 12px;
        gap: 6px;
    }

    .item-card {
        border-radius: 12px;
    }

    .item-img {
        height: 80px;
        font-size: 28px;
    }
}
</style>

<script>
// Cart state
let cart = {};
let currentPayment = 'cash';

function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('mobile-open');
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        document.body.classList.add('pos-full-screen');
    } else {
        document.exitFullscreen();
        document.body.classList.remove('pos-full-screen');
    }
}

function openModal(id) {
    document.getElementById(id).classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

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

function addToCart(id) {
    if (!cart[id]) {
        cart[id] = { name: 'Margherita Pizza', price: 250 + (id * 10), qty: 1 };
    } else {
        cart[id].qty++;
    }
    updateCart();
}

function removeFromCart(id) {
    delete cart[id];
    updateCart();
}

function updateCart() {
    const items = Object.entries(cart);
    let subtotal = 0;

    document.getElementById('cartCount').textContent = items.length;
    document.getElementById('cartItems').innerHTML = items.map(([id, item]) => {
        const total = item.price * item.qty;
        subtotal += total;
        return `
            <div class="cart-item">
                <div class="ci-info">
                    <div class="ci-name">${item.name}</div>
                    <div class="ci-bottom">
                        <div class="ci-controls">
                            <button class="ci-btn" onclick="decrementCart(${id})">−</button>
                            <div class="ci-qty">${item.qty}</div>
                            <button class="ci-btn" onclick="addToCart(${id})">+</button>
                        </div>
                        <div class="ci-price">₹ ${total}</div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    const tax = subtotal * 0.18;
    const total = subtotal + tax;

    document.getElementById('subtotal').textContent = '₹ ' + subtotal.toFixed(2);
    document.getElementById('tax').textContent = '₹ ' + tax.toFixed(2);
    document.getElementById('grandTotal').textContent = '₹ ' + total.toFixed(2);
    document.getElementById('btnTotal').textContent = '₹ ' + total.toFixed(2);
}

function decrementCart(id) {
    if (cart[id]) {
        cart[id].qty--;
        if (cart[id].qty === 0) delete cart[id];
        updateCart();
    }
}

function clearCart() {
    if (confirm('Clear cart?')) {
        cart = {};
        updateCart();
    }
}

function selectTable(el, table) {
    document.querySelectorAll('.table-cell').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    document.querySelector('.meta-picker-value').textContent = table;
    closeModal('tableModal');
}

function selectCustomer(el) {
    document.querySelectorAll('.cust-row').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    closeModal('customerModal');
}

function selectPay(el) {
    document.querySelectorAll('.pay-method').forEach(e => e.classList.remove('active'));
    el.classList.add('active');
}

function filterItems(query) {
    // Simple filter implementation
}

async function checkout() {
    if (Object.keys(cart).length === 0) {
        alert('Cart is empty!');
        return;
    }

    alert('Order placed successfully!');
    cart = {};
    updateCart();
}

function printBill() {
    alert('Print bill - Feature coming soon');
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) {
            overlay.classList.remove('show');
        }
    });
});
</script>
@endsection
