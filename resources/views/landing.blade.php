<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>DineFlow AI — Complete Restaurant Operating System & Smart QR POS</title>
    <meta name="description" content="DineFlow AI is the complete restaurant operating system. Streamline table QR ordering, lightning-fast POS billing, real-time Kitchen Display (KDS), inventory control, and staff management.">
    
    <!-- Open Graph SEO -->
    <meta property="og:title" content="DineFlow AI — Complete Restaurant Operating System">
    <meta property="og:description" content="Launch your smart restaurant with digital QR menus, live KDS, POS billing, and staff management on our generous Free Plan.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #6366F1;
            --primary-glow: rgba(99, 102, 241, 0.35);
            --primary-dark: #4338CA;
            --success: #10B981;
            --accent: #EC4899;
            --bg: #090D1A;
            --bg-alt: #0D1326;
            --card: #131A35;
            --card-glass: rgba(19, 26, 53, 0.75);
            --border: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(99, 102, 241, 0.3);
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            overflow-x: hidden;
            width: 100%;
            line-height: 1.6;
        }

        /* ─── CONTAINER & SHARED ─── */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 30px;
            color: #A5B4FC;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 38px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .section-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto 48px;
        }

        /* ─── NAVBAR ─── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 76px;
            background: rgba(9, 13, 26, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366F1, #818CF8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 0 16px var(--primary-glow);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-ghost {
            padding: 10px 20px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .btn-primary {
            padding: 11px 24px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            border-radius: 10px;
            box-shadow: 0 4px 16px var(--primary-glow);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.5);
        }

        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 22px;
            cursor: pointer;
        }

        /* ─── HERO SECTION ─── */
        .hero-section {
            padding: 160px 0 100px;
            position: relative;
            text-align: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -10%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 500px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.18) 0%, rgba(0,0,0,0) 70%);
            z-index: 0;
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 840px;
            margin: 0 auto;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 56px;
            font-weight: 900;
            letter-spacing: -1.5px;
            line-height: 1.12;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #FFFFFF 40%, #A5B4FC 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 18px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 36px;
            max-width: 680px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-ctas {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .hero-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Hero Preview Window */
        .hero-mockup-wrapper {
            margin-top: 50px;
            position: relative;
            z-index: 1;
            padding: 12px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.6);
        }

        .hero-mockup {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 32px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: left;
        }

        .mock-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
        }

        .mock-icon {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .mock-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #fff;
        }

        .mock-sub {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* ─── FEATURES GRID ─── */
        .features-section {
            padding: 100px 0;
            background: var(--bg-alt);
            position: relative;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            text-align: left;
        }

        .feature-card {
            background: var(--card-glass);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: var(--border-glow);
            box-shadow: 0 12px 30px rgba(0,0,0,0.4);
        }

        .feature-icon-wrap {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #818CF8;
            margin-bottom: 20px;
        }

        .feature-title {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 10px;
            color: #fff;
        }

        .feature-desc {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ─── HOW IT WORKS ─── */
        .how-section {
            padding: 100px 0;
            text-align: center;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: left;
            position: relative;
        }

        .step-box {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            position: relative;
        }

        .step-num {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            margin-bottom: 16px;
        }

        .step-box-title {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #fff;
        }

        .step-box-desc {
            font-size: 13px;
            color: var(--text-secondary);
        }

        /* ─── WORKFLOW FLOWCHART ─── */
        .workflow-section {
            padding: 100px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .workflow-flow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 36px;
        }

        .workflow-node {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .workflow-arrow {
            color: #818CF8;
            font-size: 14px;
        }

        /* ─── FREE PLAN SPOTLIGHT ─── */
        .pricing-spotlight {
            padding: 100px 0;
            text-align: center;
        }

        .plan-card {
            background: linear-gradient(145deg, rgba(19, 26, 53, 0.9), rgba(15, 23, 42, 0.9));
            border: 2px solid #6366F1;
            border-radius: 24px;
            padding: 48px;
            max-width: 680px;
            margin: 0 auto;
            box-shadow: 0 20px 50px rgba(99, 102, 241, 0.2);
            position: relative;
        }

        .plan-price {
            font-family: 'Outfit', sans-serif;
            font-size: 48px;
            font-weight: 900;
            color: #fff;
            margin: 16px 0;
        }

        .plan-price span {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .plan-features-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            text-align: left;
            margin: 32px 0;
        }

        .plan-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #E2E8F0;
        }

        .plan-item i {
            color: #34D399;
        }

        /* ─── FAQ ─── */
        .faq-section {
            padding: 100px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .faq-container {
            max-width: 720px;
            margin: 0 auto;
            text-align: left;
        }

        .faq-item {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            margin-bottom: 14px;
            overflow: hidden;
        }

        .faq-question {
            padding: 20px;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .faq-answer {
            padding: 0 20px 20px;
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
            display: none;
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        /* ─── CTA BANNER ─── */
        .cta-section {
            padding: 100px 0;
            text-align: center;
        }

        .cta-box {
            background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);
            border: 1px solid var(--border-glow);
            border-radius: 28px;
            padding: 60px 32px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        /* ─── FOOTER ─── */
        .footer {
            background: #060913;
            border-top: 1px solid var(--border);
            padding: 60px 0 30px;
            font-size: 13.5px;
            color: var(--text-muted);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 48px;
            text-align: left;
        }

        .footer-col h4 {
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-col a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: #fff;
        }

        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        /* ─── RESPONSIVE BREAKPOINTS ─── */
        @media (max-width: 1024px) {
            .hero-title { font-size: 46px; }
            .steps-grid { grid-template-columns: 1fr 1fr; }
            .hero-mockup { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .nav-links, .nav-actions .btn-ghost { display: none; }
            .mobile-menu-btn { display: block; }
            .hero-title { font-size: 34px; }
            .section-title { font-size: 28px; }
            .plan-features-list { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .hero-title { font-size: 28px; }
            .hero-ctas { flex-direction: column; width: 100%; }
            .hero-ctas .btn-primary, .hero-ctas .btn-ghost { width: 100%; justify-content: center; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container nav-inner">
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-fire-burner"></i></div>
                <div class="brand-name">DineFlow AI</div>
            </a>

            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#workflow">Workflow</a></li>
                <li><a href="/pricing">Pricing</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>

            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary">
                    <span>Start Free</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <button class="mobile-menu-btn" onclick="toggleMobileNav()"><i class="fa-solid fa-bars"></i></button>
            </div>
        </div>
    </nav>

    <!-- MOBILE MENU DRAWER -->
    <div id="mobileDrawer" style="display:none; position:fixed; top:76px; left:0; right:0; background:#090D1A; border-bottom:1px solid var(--border); padding:20px; z-index:999;">
        <ul style="list-style:none; display:flex; flex-direction:column; gap:16px;">
            <li><a href="#features" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">Features</a></li>
            <li><a href="#how-it-works" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">How It Works</a></li>
            <li><a href="#workflow" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">Workflow</a></li>
            <li><a href="/pricing" style="color:#fff; text-decoration:none; font-size:16px;">Pricing</a></li>
            <li><a href="#faq" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">FAQ</a></li>
            <li style="padding-top:10px; display:flex; gap:10px;">
                <a href="{{ route('login') }}" class="btn-ghost" style="flex:1; text-align:center;">Sign In</a>
                <a href="{{ route('register') }}" class="btn-primary" style="flex:1; justify-content:center;">Start Free</a>
            </li>
        </ul>
    </div>

    <!-- 1. HERO SECTION -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="section-tag"><i class="fa-solid fa-bolt"></i> Complete Restaurant Operating System</div>
            <h1 class="hero-title">Automate Orders, Kitchen & Billing in One Unified Platform.</h1>
            <p class="hero-desc">
                DineFlow AI empowers restaurants, bistros, and cafés with smart table QR menus, real-time Kitchen Display (KDS), lightning-fast POS billing, and intelligent inventory tracking.
            </p>

            <div class="hero-ctas">
                <a href="{{ route('register') }}" class="btn-primary" style="padding: 14px 32px; font-size: 16px;">
                    <span>Create Free Restaurant</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="{{ route('login') }}" class="btn-ghost" style="padding: 14px 28px; font-size: 15px; border:1px solid var(--border);">
                    <i class="fa-solid fa-desktop" style="margin-right:8px;"></i> Live POS Terminal
                </a>
            </div>

            <div class="hero-pill-badge">
                <i class="fa-solid fa-check-circle" style="color:#10B981;"></i> 100% Free Starter Plan • Zero Credit Card • Setup in 2 Minutes
            </div>

            <!-- Visual Showcase Mockup -->
            <div class="hero-mockup-wrapper">
                <div class="hero-mockup">
                    <div class="mock-card">
                        <div class="mock-icon" style="color:#818CF8;"><i class="fa-solid fa-qrcode"></i></div>
                        <div class="mock-title">Digital QR Menus</div>
                        <div class="mock-sub">Instant contactless guest ordering directly from dining tables with multilingual voice search.</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-icon" style="color:#34D399;"><i class="fa-solid fa-fire-burner"></i></div>
                        <div class="mock-title">Live Kitchen Display (KDS)</div>
                        <div class="mock-sub">Zero lost tickets. Color-coded order aging, audio chimes, and instant chef completion statuses.</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-icon" style="color:#F472B6;"><i class="fa-solid fa-cash-register"></i></div>
                        <div class="mock-title">POS Billing & Soundbox</div>
                        <div class="mock-sub">Dynamic UPI QR payments, split bills, multi-language soundbox announcements, and thermal receipts.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. FEATURES SECTION -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-wand-magic-sparkles"></i> Everything You Need</div>
            <h2 class="section-title">Built for Modern High-Volume Restaurants</h2>
            <p class="section-subtitle">A comprehensive suite designed to eliminate wait times and boost table turnover.</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-cash-register"></i></div>
                    <h3 class="feature-title">Fast POS Terminal</h3>
                    <p class="feature-desc">Sub-second order creation, table floor plan layout, quick-add modifiers, and keyboard shortcuts.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-qrcode"></i></div>
                    <h3 class="feature-title">Smart QR Digital Menu</h3>
                    <p class="feature-desc">Guests order from their phones, call waiters, apply coupon discounts, and request item split bills.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-tv"></i></div>
                    <h3 class="feature-title">Kitchen Display (KDS)</h3>
                    <p class="feature-desc">Real-time KOT display with preparation timers, course management, and kitchen sound alerts.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <h3 class="feature-title">Real-Time Inventory</h3>
                    <p class="feature-desc">Automated ingredient depletion, low-stock threshold notifications, and purchase order tracking.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-users-gear"></i></div>
                    <h3 class="feature-title">Staff & Quick PINs</h3>
                    <p class="feature-desc">Role-based permissions for Admins, Managers, Cashiers, Kitchen, and Waiters with instant 4-digit PIN switching.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-receipt"></i></div>
                    <h3 class="feature-title">Thermal & WhatsApp Invoices</h3>
                    <p class="feature-desc">Auto-print standard thermal receipts, 80mm KOTs, A4 tax invoices, or send instant invoices via WhatsApp.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <h3 class="feature-title">Expense & Cost Tracking</h3>
                    <p class="feature-desc">Record daily restaurant expenses, categorize grocery vs utility costs, and track true net margins.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap"><i class="fa-solid fa-chart-pie"></i></div>
                    <h3 class="feature-title">AI Menu Engineering</h3>
                    <p class="feature-desc">BCG matrix classification identifying your Star dishes, Plowhorses, and Puzzles for menu optimization.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. HOW IT WORKS -->
    <section class="how-section" id="how-it-works">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-route"></i> Simple 4-Step Process</div>
            <h2 class="section-title">Up and Running in 4 Quick Steps</h2>
            <p class="section-subtitle">No complicated installations or server setups required.</p>

            <div class="steps-grid">
                <div class="step-box">
                    <div class="step-num">1</div>
                    <h3 class="step-box-title">Create Account</h3>
                    <p class="step-box-desc">Sign up in seconds and get your isolated restaurant workspace under the Free Plan.</p>
                </div>
                <div class="step-box">
                    <div class="step-num">2</div>
                    <h3 class="step-box-title">Add Tables & Menu</h3>
                    <p class="step-box-desc">Configure your dining floor layout, upload your menu items, and set pricing.</p>
                </div>
                <div class="step-box">
                    <div class="step-num">3</div>
                    <h3 class="step-box-title">Print QR Codes</h3>
                    <p class="step-box-desc">Download ready-to-print table QR stands for instant guest ordering and waiter calls.</p>
                </div>
                <div class="step-box">
                    <div class="step-num">4</div>
                    <h3 class="step-box-title">Take Live Orders</h3>
                    <p class="step-box-desc">Receive orders directly on your POS and KDS screens with real-time kitchen tracking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. RESTAURANT WORKFLOW -->
    <section class="workflow-section" id="workflow">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-arrows-spin"></i> End-to-End Operation</div>
            <h2 class="section-title">Seamless Restaurant Workflow</h2>
            <p class="section-subtitle">How orders flow smoothly from customer smartphone to kitchen to settlement.</p>

            <div class="workflow-flow">
                <div class="workflow-node"><i class="fa-solid fa-qrcode" style="color:#818CF8;"></i> Customer QR</div>
                <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="workflow-node"><i class="fa-solid fa-book-open" style="color:#34D399;"></i> Digital Menu</div>
                <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="workflow-node"><i class="fa-solid fa-cart-shopping" style="color:#FBBF24;"></i> Cart & Notes</div>
                <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="workflow-node"><i class="fa-solid fa-cash-register" style="color:#818CF8;"></i> POS Terminal</div>
                <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="workflow-node"><i class="fa-solid fa-kitchen-set" style="color:#F87171;"></i> KDS / KOT</div>
                <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="workflow-node"><i class="fa-solid fa-indian-rupee-sign" style="color:#34D399;"></i> UPI / Cash</div>
                <div class="workflow-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                <div class="workflow-node"><i class="fa-solid fa-receipt" style="color:#A5B4FC;"></i> Tax Invoice</div>
            </div>
        </div>
    </section>

    <!-- 5. FREE PLAN SPOTLIGHT -->
    <section class="pricing-spotlight" id="pricing">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-tag"></i> 100% Free Forever</div>
            <h2 class="section-title">Generous Free Starter Plan</h2>
            <p class="section-subtitle">Everything you need to launch and scale your restaurant with zero fees.</p>

            <div class="plan-card">
                <div style="font-size:13px; font-weight:800; color:#818CF8; text-transform:uppercase; letter-spacing:1px;">Starter Plan</div>
                <div class="plan-price">₹0 <span>/ forever</span></div>
                <p style="font-size:14px; color:var(--text-secondary);">Ideal for independent cafés, diners, food trucks, and bistros.</p>

                <div class="plan-features-list">
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> <strong>10 Tables</strong></div>
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> <strong>5 Staff Accounts</strong></div>
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> <strong>100 Menu Items</strong></div>
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> <strong>500 Monthly Orders</strong></div>
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> Table QR Ordering</div>
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> Real-Time KDS</div>
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> Thermal Printing</div>
                    <div class="plan-item"><i class="fa-solid fa-circle-check"></i> Soundbox Alerts</div>
                </div>

                <a href="{{ route('register') }}" class="btn-primary" style="width:100%; padding:16px; justify-content:center; font-size:16px;">
                    <span>Get Started Free</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 6. FAQ SECTION -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-circle-question"></i> Got Questions?</div>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Clear answers about DineFlow AI features and plans.</p>

            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Is DineFlow AI really free to use?</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes! Our Free Starter Plan includes up to 10 tables, 5 staff members, 100 menu dishes, and 500 monthly orders completely free forever.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Do I need special hardware to run the POS?</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        No special proprietary hardware is required. DineFlow AI runs smoothly on any Android tablet, iPad, laptop, desktop, or smartphone with a modern web browser.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>How does customer QR ordering work?</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Each dining table has a unique QR code. When guests scan it with their smartphone camera, your digital menu opens instantly without downloading any mobile app.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span>Can I print KOTs and thermal receipts?</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Yes. DineFlow AI supports standard 58mm and 80mm thermal receipt printers, network ESC/POS printers, browser printing, and A4 tax invoices.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FINAL CTA -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <h2 style="font-family:'Outfit',sans-serif; font-size:36px; font-weight:800; margin-bottom:16px;">Ready to Modernize Your Restaurant?</h2>
                <p style="font-size:16px; color:var(--text-secondary); max-width:560px; margin:0 auto 32px;">
                    Join hundreds of forward-thinking restaurants accelerating their floor operations with DineFlow AI today.
                </p>
                <a href="{{ route('register') }}" class="btn-primary" style="padding:16px 36px; font-size:16px;">
                    <span>Launch Your Free Restaurant</span>
                    <i class="fa-solid fa-rocket"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- 8. FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="/" class="brand-logo" style="margin-bottom:16px;">
                        <div class="brand-icon"><i class="fa-solid fa-fire-burner"></i></div>
                        <div class="brand-name">DineFlow AI</div>
                    </a>
                    <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.6; max-width:320px;">
                        The next-generation cloud restaurant operating system and POS terminal for modern hospitality brands.
                    </p>
                </div>

                <div class="footer-col">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#workflow">Workflow</a></li>
                        <li><a href="/pricing">Pricing Plans</a></li>
                        <li><a href="{{ route('login') }}">POS Terminal</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                        <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Get Started</h4>
                    <ul>
                        <li><a href="{{ route('register') }}">Create Free Account</a></li>
                        <li><a href="{{ route('login') }}">Staff Login</a></li>
                        <li><a href="{{ route('login') }}">PIN Login</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; 2026 DineFlow AI Inc. All rights reserved.</div>
                <div style="display:flex; gap:16px;">
                    <a href="{{ route('terms') }}" style="color:var(--text-muted); text-decoration:none;">Terms</a>
                    <a href="{{ route('privacy') }}" style="color:var(--text-muted); text-decoration:none;">Privacy</a>
                    <a href="{{ route('contact') }}" style="color:var(--text-muted); text-decoration:none;">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileNav() {
            const drawer = document.getElementById('mobileDrawer');
            if (drawer) {
                drawer.style.display = drawer.style.display === 'none' ? 'block' : 'none';
            }
        }

        function toggleFaq(elem) {
            const item = elem.parentElement;
            item.classList.toggle('active');
            const icon = elem.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-chevron-down');
                icon.classList.toggle('fa-chevron-up');
            }
        }
    </script>
</body>
</html>
