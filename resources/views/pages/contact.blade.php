<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact Sales &amp; Support — DineFlow POS</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="description" content="Get in touch with DineFlow restaurant POS team. Book a guided demo, request custom pricing, or chat directly on WhatsApp at +91 7777938338.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #9F1239;
            --primary-glow: rgba(159, 18, 57, 0.4);
            --primary-dark: #881337;
            --primary-rose: #FB7185;
            --success: #22C55E;
            --success-glow: rgba(34, 197, 94, 0.3);
            --whatsapp: #25D366;
            --whatsapp-dark: #128C7E;
            --whatsapp-glow: rgba(37, 211, 102, 0.35);
            --bg: #0B0B0D;
            --bg-card: #151517;
            --border: #26262A;
            --border-focus: rgba(251, 113, 133, 0.6);
            --text-primary: #F4F4F5;
            --text-secondary: #A1A1AA;
            --text-muted: #71717A;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
            width: 100%;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-y;
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 clamp(14px, 4vw, 24px);
            width: 100%;
        }

        /* ─── TOP BAR / NAVBAR ─── */
        .topbar {
            height: 72px;
            background: rgba(9, 13, 26, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            gap: 12px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #9F1239, #BE123C);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #fff;
            box-shadow: 0 0 12px var(--primary-glow);
            border: 1px solid rgba(251, 113, 133, 0.3);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
        }

        /* Desktop Nav Items */
        .nav-links-desktop {
            display: flex;
            align-items: center;
            gap: 20px;
            list-style: none;
        }

        .nav-links-desktop a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        .nav-links-desktop a:hover {
            color: #fff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-ghost {
            padding: 8px 16px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            background: rgba(255, 255, 255, 0.03);
            min-height: 40px;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-wa-header {
            padding: 8px 14px;
            background: rgba(37, 211, 102, 0.12);
            border: 1px solid rgba(37, 211, 102, 0.3);
            color: #4ADE80;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
            min-height: 40px;
        }

        .btn-wa-header:hover {
            background: rgba(37, 211, 102, 0.22);
            border-color: #25D366;
            color: #fff;
        }

        .btn-primary {
            padding: 9px 18px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            border-radius: 8px;
            box-shadow: 0 4px 14px var(--primary-glow);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            justify-content: center;
            white-space: nowrap;
            min-height: 40px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(159, 18, 57, 0.5);
        }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ─── MOBILE DRAWER & BACKDROP ─── */
        .mobile-drawer-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(9, 13, 26, 0.75);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 998;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        .mobile-drawer-backdrop.active {
            display: block;
            pointer-events: auto;
            opacity: 1;
        }

        .mobile-drawer {
            display: none;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            background: #0D1326;
            border-bottom: 1px solid var(--border);
            padding: 20px 16px;
            z-index: 999;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.5);
            max-height: calc(100vh - 72px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .mobile-drawer.active {
            display: block;
        }

        .mobile-drawer ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .mobile-drawer ul li a {
            color: #CBD5E1;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            transition: background 0.15s;
            min-height: 44px;
        }

        .mobile-drawer ul li a:hover,
        .mobile-drawer ul li a.active {
            background: rgba(159, 18, 57, 0.15);
            color: #fff;
        }

        .mobile-drawer-actions {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ─── HERO SECTION ─── */
        .contact-hero {
            padding: clamp(32px, 5vw, 48px) 0 clamp(20px, 3.5vw, 32px);
            text-align: center;
            width: 100%;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(159, 18, 57, 0.15);
            border: 1px solid rgba(251, 113, 133, 0.3);
            border-radius: 30px;
            color: #FDA4AF;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .contact-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(26px, 4.2vw, 38px);
            font-weight: 900;
            line-height: 1.2;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            color: #fff;
        }

        .contact-hero p {
            font-size: clamp(14.5px, 1.8vw, 16px);
            color: var(--text-secondary);
            max-width: 640px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ─── CONTACT GRID ─── */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(20px, 3vw, 32px);
            margin-bottom: clamp(40px, 6vw, 80px);
            width: 100%;
        }

        /* WhatsApp Spotlight Card */
        .wa-spotlight-card {
            background: linear-gradient(145deg, #064E3B 0%, #0D2818 50%, #131A35 100%);
            border: 1px solid rgba(37, 211, 102, 0.4);
            border-radius: 20px;
            padding: clamp(20px, 3vw, 32px);
            margin-bottom: 20px;
            width: 100%;
            min-width: 0;
        }

        .wa-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(37, 211, 102, 0.2);
            color: #86EFAC;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 14px;
            width: fit-content;
        }

        .wa-spotlight-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(20px, 2.5vw, 24px);
            font-weight: 800;
            color: #fff;
            margin-bottom: 10px;
            line-height: 1.25;
        }

        .wa-spotlight-card p {
            font-size: clamp(13.5px, 1.6vw, 14.5px);
            color: #D1FAE5;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .btn-wa-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff;
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            padding: 12px 24px;
            border-radius: 10px;
            box-shadow: 0 4px 16px var(--whatsapp-glow);
            transition: all 0.2s ease;
            width: 100%;
            min-height: 44px;
        }

        .btn-wa-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
        }

        /* Direct Channels List */
        .info-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: clamp(16px, 2.5vw, 24px);
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            min-width: 0;
        }

        .contact-channel {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
            min-height: 52px;
        }

        .contact-channel:hover {
            border-color: rgba(251, 113, 133, 0.4);
            background: rgba(159, 18, 57, 0.08);
            transform: translateX(3px);
        }

        .channel-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(159, 18, 57, 0.15);
            color: #FB7185;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .channel-info h4 {
            font-size: 13.5px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 2px;
        }

        .channel-info p {
            font-size: 12.5px;
            color: var(--text-secondary);
        }

        /* Form Card */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: clamp(20px, 3.5vw, 36px);
            width: 100%;
            min-width: 0;
        }

        .form-card h2 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(20px, 2.5vw, 26px);
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
            line-height: 1.25;
        }

        .form-card p {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.55;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 14px;
            width: 100%;
        }

        .form-group label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 6px;
        }

        .form-group label .req {
            color: #F87171;
        }

        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 11px 14px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
            min-height: 44px;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(159, 18, 57, 0.25);
            background: rgba(15, 23, 42, 1);
        }

        .form-input.error {
            border-color: #EF4444;
            background: rgba(239, 68, 68, 0.05);
        }

        .field-error {
            font-size: 12px;
            color: #F87171;
            margin-top: 4px;
            display: none;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 90px;
        }

        .btn-submit {
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            margin-top: 8px;
            min-height: 44px;
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Success & Error State */
        .state-banner {
            display: none;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin-top: 10px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .success-state {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .success-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.2);
            color: #34D399;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 12px;
        }

        .success-state h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .success-state p {
            font-size: 14px;
            color: #A7F3D0;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .error-state {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #FCA5A5;
            font-size: 13.5px;
            margin-bottom: 14px;
        }

        .hp-field {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
        }

        /* ─── FOOTER ─── */
        .footer {
            margin-top: auto;
            border-top: 1px solid var(--border);
            padding: 24px 0;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            width: 100%;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ─── RESPONSIVE BREAKPOINTS (320px - 960px) ─── */
        @media (max-width: 960px) {
            .nav-links-desktop { display: none; }
            .desktop-auth-btns { display: none; }
            .mobile-menu-btn { display: flex; }
            .contact-grid { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
        }

        @media (max-width: 640px) {
            .topbar { height: 64px; }
            .mobile-drawer { top: 64px; }
            .btn-wa-header span { display: none; }
            .btn-wa-header { padding: 8px 10px; }
        }

        @media (max-width: 380px) {
            .brand-name { font-size: 18px; }
            .brand-icon { width: 34px; height: 34px; font-size: 15px; }
            .btn-wa-header { display: none; }
        }
    </style>
</head>
<body>

    <!-- TOPBAR / NAVBAR -->
    <header class="topbar">
        <div class="container topbar-inner">
            <!-- Brand Logo -->
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                <div class="brand-name">DineFlow</div>
            </a>

            <!-- Desktop Nav Links -->
            <ul class="nav-links-desktop">
                <li><a href="/#features">Features</a></li>
                <li><a href="/#how-it-works">How It Works</a></li>
                <li><a href="/#workflow">Online + Offline</a></li>
                <li><a href="/pricing">Pricing</a></li>
                <li><a href="/demo">Live Demo</a></li>
                <li><a href="/#faq">FAQ</a></li>
            </ul>

            <!-- Header Actions -->
            <div class="nav-actions">
                <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20for%20my%20restaurant." target="_blank" class="btn-wa-header" title="Chat on WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>+91 7777938338</span>
                </a>
                <div class="desktop-auth-btns" style="display:flex; align-items:center; gap:8px;">
                    <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Start Free</a>
                </div>
                <!-- Mobile Hamburger Button -->
                <button type="button" class="mobile-menu-btn" onclick="toggleMobileDrawer()" aria-label="Toggle navigation menu">
                    <i class="fa-solid fa-bars" id="menuToggleIcon"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- MOBILE DRAWER BACKDROP -->
    <div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop" onclick="toggleMobileDrawer()"></div>

    <!-- MOBILE DRAWER -->
    <nav class="mobile-drawer" id="mobileDrawer" aria-label="Mobile Navigation">
        <ul>
            <li><a href="/"><i class="fa-solid fa-house" style="color:var(--primary);"></i> Home</a></li>
            <li><a href="/#features" onclick="toggleMobileDrawer()"><i class="fa-solid fa-sparkles" style="color:var(--primary);"></i> Features</a></li>
            <li><a href="/#how-it-works" onclick="toggleMobileDrawer()"><i class="fa-solid fa-list-check" style="color:var(--primary);"></i> How It Works</a></li>
            <li><a href="/#workflow" onclick="toggleMobileDrawer()"><i class="fa-solid fa-cloud-arrow-down" style="color:var(--primary);"></i> Online + Offline</a></li>
            <li><a href="/demo"><i class="fa-solid fa-play" style="color:var(--primary);"></i> Live Demo Sandboxes</a></li>
            <li><a href="/pricing"><i class="fa-solid fa-tags" style="color:var(--primary);"></i> Pricing Plans</a></li>
            <li><a href="/#faq" onclick="toggleMobileDrawer()"><i class="fa-solid fa-circle-question" style="color:var(--primary);"></i> FAQ</a></li>
            <li><a href="/contact" class="active"><i class="fa-solid fa-envelope" style="color:var(--primary);"></i> Contact Sales</a></li>
            <li>
                <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20for%20my%20restaurant." target="_blank" style="color: #4ADE80;">
                    <i class="fa-brands fa-whatsapp" style="color: var(--whatsapp); font-size: 16px;"></i> WhatsApp (+91 7777938338)
                </a>
            </li>
        </ul>
        <div class="mobile-drawer-actions">
            <a href="{{ route('login') }}" class="btn-ghost" style="width:100%;">
                <i class="fa-solid fa-right-to-bracket"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn-primary" style="width:100%;">
                <span>Start Free 14-Day Trial</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </nav>

    <main class="container">
        <!-- Hero Section -->
        <section class="contact-hero">
            <div class="section-tag"><i class="fa-solid fa-headset"></i> Dedicated Support &amp; Sales</div>
            <h1>Let’s Talk Restaurant Growth</h1>
            <p>Have questions about setting up DineFlow in your restaurant, QR billing, or want a custom enterprise plan? We respond within minutes.</p>
        </section>

        <!-- Main Content Grid -->
        <div class="contact-grid">
            <!-- Left Column: Direct WhatsApp & Info -->
            <div>
                <!-- WhatsApp Spotlight Card -->
                <div class="wa-spotlight-card">
                    <div class="wa-badge-pill">
                        <i class="fa-brands fa-whatsapp"></i> Instant Response
                    </div>
                    <h3>Chat with Us on WhatsApp</h3>
                    <p>Connect directly with our founding &amp; product team for instant queries, quick video walkthroughs, and custom setup guidance.</p>
                    <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20for%20my%20restaurant." target="_blank" class="btn-wa-action">
                        <i class="fa-brands fa-whatsapp" style="font-size: 20px;"></i>
                        <span>Chat on WhatsApp (+91 7777938338)</span>
                    </a>
                </div>

                <!-- Direct Channels -->
                <div class="info-card">
                    <a href="tel:+917777938338" class="contact-channel">
                        <div class="channel-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="channel-info">
                            <h4>Direct Phone</h4>
                            <p>+91 7777938338</p>
                        </div>
                    </a>

                    <a href="mailto:shubhmpatel77@gmail.com" class="contact-channel">
                        <div class="channel-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div class="channel-info">
                            <h4>Email Support &amp; Sales</h4>
                            <p>shubhmpatel77@gmail.com</p>
                        </div>
                    </a>

                    <div class="contact-channel" style="cursor:default;">
                        <div class="channel-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="channel-info">
                            <h4>Headquarters</h4>
                            <p>DineFlow Technologies, Gujarat, India</p>
                        </div>
                    </div>

                    <div class="contact-channel" style="cursor:default;">
                        <div class="channel-icon"><i class="fa-solid fa-clock"></i></div>
                        <div class="channel-info">
                            <h4>Support Hours</h4>
                            <p>Mon – Sat, 9:00 AM – 9:00 PM IST</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Contact Form -->
            <div class="form-card">
                <h2>Send Us a Message</h2>
                <p>Fill out this form and our restaurant consultant will contact you promptly.</p>

                <div id="contactErrorBanner" class="state-banner error-state"></div>

                <form id="contactForm" onsubmit="handleContactSubmit(event)" novalidate>
                    @csrf
                    <!-- Honeypot anti-spam field -->
                    <input type="text" name="website_hp" id="website_hp" class="hp-field" tabindex="-1" autocomplete="off">
                    <input type="hidden" name="source" value="contact_page">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactName">Your Name <span class="req">*</span></label>
                            <input type="text" id="contactName" name="name" class="form-input" placeholder="e.g. Rahul Patel" required>
                            <div class="field-error" id="err-name">Please enter your name.</div>
                        </div>

                        <div class="form-group">
                            <label for="contactRestName">Restaurant Name <span class="req">*</span></label>
                            <input type="text" id="contactRestName" name="restaurant_name" class="form-input" placeholder="e.g. Spice Symphony Bistro" required>
                            <div class="field-error" id="err-restaurant_name">Please enter your restaurant name.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactPhone">Phone / WhatsApp Number <span class="req">*</span></label>
                            <input type="tel" id="contactPhone" name="phone" class="form-input" placeholder="e.g. 9876543210" required>
                            <div class="field-error" id="err-phone">Please enter a valid phone number.</div>
                        </div>

                        <div class="form-group">
                            <label for="contactEmail">Email Address</label>
                            <input type="email" id="contactEmail" name="email" class="form-input" placeholder="e.g. owner@restaurant.com">
                            <div class="field-error" id="err-email">Please enter a valid email address.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactCity">City / Location</label>
                            <input type="text" id="contactCity" name="city" class="form-input" placeholder="e.g. Ahmedabad, Surat, Mumbai">
                        </div>

                        <div class="form-group">
                            <label for="contactTime">Preferred Time for Call / Demo</label>
                            <input type="text" id="contactTime" name="preferred_time" class="form-input" placeholder="e.g. Tomorrow 3 PM or Morning">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contactMessage">How can we help your restaurant?</label>
                        <textarea id="contactMessage" name="message" class="form-input" placeholder="Tell us about your restaurant requirements, tables, or questions..."></textarea>
                    </div>

                    <button type="submit" id="contactSubmitBtn" class="btn-submit">
                        <span>Submit Enquiry</span>
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>

                <!-- Success Confirmation State -->
                <div id="contactSuccessState" class="state-banner success-state">
                    <div class="success-icon"><i class="fa-solid fa-check"></i></div>
                    <h3>Thank you! Your enquiry has been received.</h3>
                    <p>Our DineFlow team will contact you shortly. Want an immediate response?</p>

                    <a id="successWhatsAppBtn" href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20just%20submitted%20an%20enquiry%20for%20my%20restaurant." target="_blank" class="btn-wa-action" style="margin: 0 auto; display: inline-flex;">
                        <i class="fa-brands fa-whatsapp" style="font-size: 20px;"></i>
                        <span>Chat on WhatsApp (+91 7777938338)</span>
                    </a>

                    <div style="margin-top: 20px;">
                        <button type="button" onclick="resetContactForm()" style="background:none; border:none; color:#94A3B8; font-size:13px; cursor:pointer; text-decoration:underline;">
                            Submit another enquiry
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            &copy; 2026 DineFlow. All rights reserved. &bull; <a href="/terms" style="color:#94A3B8; text-decoration:none;">Terms</a> &bull; <a href="/privacy" style="color:#94A3B8; text-decoration:none;">Privacy</a> &bull; <a href="/demo" style="color:#94A3B8; text-decoration:none;">Live Demo</a>
        </div>
    </footer>

    <!-- Form Handler Script -->
    <script>
        function toggleMobileDrawer() {
            const drawer = document.getElementById('mobileDrawer');
            const backdrop = document.getElementById('mobileDrawerBackdrop');
            const icon = document.getElementById('menuToggleIcon');
            const isOpen = drawer.classList.contains('active');

            if (isOpen) {
                drawer.classList.remove('active');
                backdrop.classList.remove('active');
                icon.className = 'fa-solid fa-bars';
                document.body.style.overflow = '';
            } else {
                drawer.classList.add('active');
                backdrop.classList.add('active');
                icon.className = 'fa-solid fa-xmark';
                document.body.style.overflow = 'hidden';
            }
        }

        async function handleContactSubmit(e) {
            e.preventDefault();

            const form = document.getElementById('contactForm');
            const submitBtn = document.getElementById('contactSubmitBtn');
            const successState = document.getElementById('contactSuccessState');
            const errorBanner = document.getElementById('contactErrorBanner');

            // Reset errors
            document.querySelectorAll('.field-error').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.form-input').forEach(el => el.classList.remove('error'));
            errorBanner.style.display = 'none';

            const name = document.getElementById('contactName').value.trim();
            const restaurantName = document.getElementById('contactRestName').value.trim();
            const phone = document.getElementById('contactPhone').value.trim();
            const email = document.getElementById('contactEmail').value.trim();
            const city = document.getElementById('contactCity').value.trim();
            const preferredTime = document.getElementById('contactTime').value.trim();
            const message = document.getElementById('contactMessage').value.trim();
            const honeypot = document.getElementById('website_hp').value;

            // Client Validation
            let hasError = false;

            if (!name) {
                showFieldError('contactName', 'err-name', 'Please enter your name.');
                hasError = true;
            }

            if (!restaurantName) {
                showFieldError('contactRestName', 'err-restaurant_name', 'Please enter your restaurant name.');
                hasError = true;
            }

            const phoneDigits = phone.replace(/[^0-9]/g, '');
            if (!phone || phoneDigits.length < 8 || phoneDigits.length > 15) {
                showFieldError('contactPhone', 'err-phone', 'Please enter a valid phone number (8-15 digits).');
                hasError = true;
            }

            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showFieldError('contactEmail', 'err-email', 'Please enter a valid email address.');
                hasError = true;
            }

            if (hasError) return;

            // Disable submit button & show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting…';

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const payload = {
                name: name,
                restaurant_name: restaurantName,
                phone: phone,
                email: email || null,
                city: city || null,
                preferred_time: preferredTime || null,
                message: message || null,
                source: 'contact_page',
                website_hp: honeypot,
                _token: csrfToken
            };

            try {
                const response = await fetch('/api/contact/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    form.style.display = 'none';
                    successState.style.display = 'block';

                    // Customize WhatsApp button with restaurant details
                    const customWaText = encodeURIComponent(`Hello DineFlow Team, I just submitted an enquiry for my restaurant "${restaurantName}". My name is ${name}.`);
                    document.getElementById('successWhatsAppBtn').href = `https://wa.me/917777938338?text=${customWaText}`;
                } else {
                    errorBanner.textContent = data.message || 'There was an issue submitting your enquiry. Please reach out via WhatsApp at +91 7777938338.';
                    errorBanner.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span>Submit Enquiry</span> <i class="fa-solid fa-paper-plane"></i>';
                }
            } catch (err) {
                console.error('Contact submission error:', err);
                errorBanner.textContent = 'Network error occurred. You can chat with us directly on WhatsApp at +91 7777938338.';
                errorBanner.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Submit Enquiry</span> <i class="fa-solid fa-paper-plane"></i>';
            }
        }

        function showFieldError(inputId, errorId, message) {
            const input = document.getElementById(inputId);
            const error = document.getElementById(errorId);
            if (input) input.classList.add('error');
            if (error) {
                error.textContent = message;
                error.style.display = 'block';
            }
        }

        function resetContactForm() {
            const form = document.getElementById('contactForm');
            const successState = document.getElementById('contactSuccessState');
            const submitBtn = document.getElementById('contactSubmitBtn');
            form.reset();
            form.style.display = 'block';
            successState.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span>Submit Enquiry</span> <i class="fa-solid fa-paper-plane"></i>';
        }
    </script>
</body>
</html>
