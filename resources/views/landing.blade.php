<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Tendorabuy — Restaurant POS, QR Ordering & Kitchen Management</title>
    <meta name="description" content="Tendorabuy is the complete all-in-one restaurant management platform. Streamline table QR digital menus, lightning-fast POS billing, real-time Kitchen Display (KDS), inventory control, offline desktop sync, and staff management.">
    
    <!-- Open Graph / SEO Metadata -->
    <meta property="og:title" content="Tendorabuy — Restaurant POS, QR Ordering & Kitchen Management">
    <meta property="og:description" content="Complete restaurant operating system. Streamline QR table ordering, fast POS billing, real-time KDS, offline desktop sync, and staff management.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="robots" content="index, follow">

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
            max-width: 650px;
            margin: 0 auto 48px;
            text-align: center;
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
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #6366F1, #818CF8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 0 16px var(--primary-glow);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            justify-content: center;
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
            padding: 8px;
        }

        /* ─── 1. HERO SECTION ─── */
        .hero-section {
            padding: 140px 0 80px;
            background: radial-gradient(circle at 50% 20%, rgba(99, 102, 241, 0.15) 0%, rgba(9, 13, 26, 0) 70%);
            position: relative;
        }

        .hero-content {
            text-align: center;
            max-width: 920px;
            margin: 0 auto;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 48px;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #FFFFFF 30%, #A5B4FC 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 18px;
            color: var(--text-secondary);
            margin-bottom: 36px;
            max-width: 720px;
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
            gap: 10px;
            padding: 8px 20px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 30px;
            color: #34D399;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 48px;
        }

        /* ─── SHOWCASE MOCKUP ─── */
        .hero-mockup-wrapper {
            position: relative;
            margin-top: 10px;
        }

        .hero-mockup {
            background: var(--card-glass);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6), 0 0 40px rgba(99, 102, 241, 0.15);
            backdrop-filter: blur(12px);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .mock-card {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            text-align: left;
        }

        .mock-icon {
            font-size: 28px;
            margin-bottom: 12px;
        }

        .mock-title {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .mock-sub {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ─── TRUST STATS ─── */
        .trust-section {
            padding: 40px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: var(--bg-alt);
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            text-align: center;
        }

        .trust-item h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 4px;
        }

        .trust-item p {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ─── FEATURES ─── */
        .features-section {
            padding: 90px 0;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            text-align: left;
        }

        .feature-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            transition: all 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            border-color: var(--border-glow);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .feat-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(99, 102, 241, 0.12);
            color: #818CF8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 19px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ─── HOW IT WORKS ─── */
        .steps-section {
            padding: 90px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            text-align: left;
        }

        .step-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            position: relative;
        }

        .step-num {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 900;
            color: rgba(99, 102, 241, 0.3);
            margin-bottom: 12px;
        }

        .step-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .step-card p {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ─── ONLINE + OFFLINE WORKFLOW ─── */
        .sync-section {
            padding: 90px 0;
            text-align: center;
        }

        .sync-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
            text-align: left;
        }

        .sync-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
        }

        .sync-card.cloud { border-left: 4px solid #6366F1; }
        .sync-card.offline { border-left: 4px solid #10B981; }

        .sync-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .sync-card.cloud .sync-badge { background: rgba(99, 102, 241, 0.15); color: #818CF8; }
        .sync-card.offline .sync-badge { background: rgba(16, 185, 129, 0.15); color: #34D399; }

        .sync-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 12px;
        }

        .sync-flow {
            margin-top: 20px;
            padding: 16px;
            background: rgba(0, 0, 0, 0.25);
            border-radius: 12px;
            font-family: monospace;
            font-size: 13.5px;
            color: #A5B4FC;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* ─── BENEFITS ─── */
        .benefits-section {
            padding: 90px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 18px;
            text-align: left;
        }

        .benefit-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
        }

        .benefit-card h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .benefit-card p {
            font-size: 12.5px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ─── PRICING PREVIEW ─── */
        .pricing-section {
            padding: 90px 0;
            text-align: center;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            text-align: left;
        }

        .price-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .price-card.featured {
            border-color: #6366F1;
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.25);
            background: linear-gradient(180deg, #18224B 0%, #131A35 100%);
        }

        .featured-tag {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #6366F1;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .plan-name {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }

        .plan-price {
            font-family: 'Outfit', sans-serif;
            font-size: 40px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 16px;
        }

        .plan-price span {
            font-size: 15px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 30px;
            flex: 1;
        }

        .plan-features li {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-features li i {
            color: #10B981;
            font-size: 14px;
        }

        /* ─── FAQ ─── */
        .faq-section {
            padding: 90px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .faq-list {
            max-width: 800px;
            margin: 0 auto;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .faq-item {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
        }

        .faq-item h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 16.5px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .faq-item p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ─── CTA BANNER ─── */
        .cta-banner {
            padding: 80px 0;
            text-align: center;
            background: radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.2) 0%, rgba(9, 13, 26, 0) 70%);
        }

        .cta-box {
            background: linear-gradient(135deg, #18224B, #131A35);
            border: 1px solid var(--border-glow);
            border-radius: 24px;
            padding: 60px 40px;
            max-width: 900px;
            margin: 0 auto;
        }

        .cta-box h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 38px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 16px;
        }

        .cta-box p {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 32px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ─── FOOTER ─── */
        .footer {
            padding: 60px 0 30px;
            background: #060913;
            border-top: 1px solid var(--border);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 48px;
        }

        .footer-brand p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 12px;
            max-width: 320px;
        }

        .footer-col h5 {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 16px;
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
            font-size: 13.5px;
            transition: color 0.2s ease;
        }

        .footer-col a:hover {
            color: #fff;
        }

        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-muted);
        }

        /* ─── RESPONSIVE BREAKPOINTS (320px - 1024px) ─── */
        @media (max-width: 1024px) {
            .hero-mockup { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .benefits-grid { grid-template-columns: repeat(3, 1fr); }
            .trust-grid { grid-template-columns: repeat(3, 1fr); }
            .pricing-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .nav-links, .nav-actions .btn-ghost { display: none; }
            .mobile-menu-btn { display: block; }
            .hero-title { font-size: 34px; }
            .hero-desc { font-size: 15.5px; }
            .section-title { font-size: 28px; }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .sync-grid { grid-template-columns: 1fr; }
            .benefits-grid { grid-template-columns: 1fr; }
            .trust-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr; }
            .cta-box { padding: 40px 20px; }
            .cta-box h2 { font-size: 28px; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container nav-inner">
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                <div class="brand-name">Tendorabuy</div>
            </a>

            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#workflow">Online + Offline</a></li>
                <li><a href="/pricing">Pricing</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>

            <div class="nav-actions">
                @auth
                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-ghost"><i class="fa-solid fa-shield-halved" style="margin-right:6px;"></i> Super Admin</a>
                    @elseif(Auth::user()->role === 'kitchen')
                        <a href="/kitchen" class="btn-ghost"><i class="fa-solid fa-fire-burner" style="margin-right:6px;"></i> Kitchen KDS</a>
                    @elseif(in_array(Auth::user()->role, ['cashier', 'waiter']))
                        <a href="/pos" class="btn-ghost"><i class="fa-solid fa-cash-register" style="margin-right:6px;"></i> POS</a>
                    @else
                        <a href="/dashboard" class="btn-ghost"><i class="fa-solid fa-gauge" style="margin-right:6px;"></i> Dashboard</a>
                    @endif
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span>+ Register Restaurant</span>
                        <i class="fa-solid fa-plus"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span>Start Free</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @endauth
                <button class="mobile-menu-btn" onclick="toggleMobileNav()"><i class="fa-solid fa-bars"></i></button>
            </div>
        </div>
    </nav>

    <!-- MOBILE MENU DRAWER -->
    <div id="mobileDrawer" style="display:none; position:fixed; top:76px; left:0; right:0; background:#090D1A; border-bottom:1px solid var(--border); padding:20px; z-index:999;">
        <ul style="list-style:none; display:flex; flex-direction:column; gap:16px;">
            <li><a href="#features" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">Features</a></li>
            <li><a href="#how-it-works" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">How It Works</a></li>
            <li><a href="#workflow" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">Online + Offline</a></li>
            <li><a href="/pricing" style="color:#fff; text-decoration:none; font-size:16px;">Pricing</a></li>
            <li><a href="#faq" onclick="toggleMobileNav()" style="color:#fff; text-decoration:none; font-size:16px;">FAQ</a></li>
            <li><a href="/contact" style="color:#fff; text-decoration:none; font-size:16px;">Contact</a></li>
            <li style="padding-top:10px; display:flex; gap:10px;">
                @auth
                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-ghost" style="flex:1; text-align:center; border:1px solid var(--border);">Super Admin</a>
                    @else
                        <a href="/dashboard" class="btn-ghost" style="flex:1; text-align:center; border:1px solid var(--border);">Dashboard</a>
                    @endif
                    <a href="{{ route('register') }}" class="btn-primary" style="flex:1; justify-content:center;">+ Register</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost" style="flex:1; text-align:center; border:1px solid var(--border);">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary" style="flex:1; justify-content:center;">Start Free</a>
                @endauth
            </li>
        </ul>
    </div>

    <!-- 1. HERO SECTION -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="section-tag"><i class="fa-solid fa-bolt"></i> Complete Restaurant Platform</div>
            <h1 class="hero-title">Complete Restaurant Management, POS, QR Ordering & Kitchen in One Platform.</h1>
            <p class="hero-desc">
                Tendorabuy empowers restaurants, cafés, and food businesses with instant contactless table QR ordering, real-time Kitchen Display (KDS), lightning-fast POS billing, and intelligent inventory tracking.
            </p>

            <div class="hero-ctas">
                <a href="{{ route('register') }}" class="btn-primary" style="padding: 14px 32px; font-size: 16px;">
                    <span>START FREE</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="{{ route('login') }}" class="btn-ghost" style="padding: 14px 28px; font-size: 15px; border:1px solid var(--border);">
                    <i class="fa-solid fa-right-to-bracket" style="margin-right:8px;"></i> LOGIN
                </a>
            </div>

            <div class="hero-pill-badge">
                <i class="fa-solid fa-circle-check" style="color:#10B981;"></i> Free Starter Plan • No Credit Card Required • Setup in 2 Minutes
            </div>

            <!-- Visual Showcase Mockup -->
            <div class="hero-mockup-wrapper">
                <div class="hero-mockup">
                    <div class="mock-card">
                        <div class="mock-icon" style="color:#818CF8;"><i class="fa-solid fa-qrcode"></i></div>
                        <div class="mock-title">Smart QR Ordering</div>
                        <div class="mock-sub">Instant contactless guest ordering from dining tables with multi-language voice search and UPI QR payments.</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-icon" style="color:#34D399;"><i class="fa-solid fa-fire-burner"></i></div>
                        <div class="mock-title">Live Kitchen Display (KDS)</div>
                        <div class="mock-sub">Zero lost tickets. Color-coded order aging, audio chime alerts, and instant chef completion transitions.</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-icon" style="color:#F472B6;"><i class="fa-solid fa-cash-register"></i></div>
                        <div class="mock-title">POS Billing & Soundbox</div>
                        <div class="mock-sub">Dynamic UPI QR payments, split bills, multi-language soundbox announcements, and thermal receipt printing.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST / VALUE SECTION -->
    <section class="trust-section">
        <div class="container">
            <div class="trust-grid">
                <div class="trust-item">
                    <h3>Free Starter</h3>
                    <p>₹0 / Forever Free Plan</p>
                </div>
                <div class="trust-item">
                    <h3>No Credit Card</h3>
                    <p>Instant Onboarding</p>
                </div>
                <div class="trust-item">
                    <h3>2 Min Setup</h3>
                    <p>Ready Out of the Box</p>
                </div>
                <div class="trust-item">
                    <h3>Cloud + Offline</h3>
                    <p>Works Without Internet</p>
                </div>
                <div class="trust-item">
                    <h3>Multi-Tenant</h3>
                    <p>Strict Data Isolation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. FEATURES SECTION -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-wand-magic-sparkles"></i> Everything You Need</div>
            <h2 class="section-title">Built for Modern High-Volume Restaurants</h2>
            <p class="section-subtitle">A comprehensive suite designed to eliminate wait times and maximize table turnover.</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feat-icon-wrap"><i class="fa-solid fa-cash-register"></i></div>
                    <h3>POS Billing</h3>
                    <p>Lightning-fast touch terminal with quick item search, modifiers, combo deals, discounts, and one-click receipt printing.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(16,185,129,0.12); color:#34D399;"><i class="fa-solid fa-qrcode"></i></div>
                    <h3>QR Digital Menu</h3>
                    <p>Dynamic contactless menu ordering with veg/non-veg filters, dietary badges, live order tracking, and voice recognition.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(236,72,153,0.12); color:#F472B6;"><i class="fa-solid fa-utensils"></i></div>
                    <h3>Kitchen KDS</h3>
                    <p>Paperless kitchen display system with order aging timers, sound alerts, preparation stages, and single-tap item completions.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(245,158,11,0.12); color:#FBBF24;"><i class="fa-solid fa-chair"></i></div>
                    <h3>Table Management</h3>
                    <p>Real-time visual table floor layout with live occupancy status, occupied order amounts, bill settlement, and auto-release.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(59,130,246,0.12); color:#60A5FA;"><i class="fa-solid fa-user-shield"></i></div>
                    <h3>Staff & Roles (RBAC)</h3>
                    <p>Strict role boundaries for Admin, Manager, Cashier, Kitchen Chef, Waiter, and Customer with fast numeric PIN switching.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(139,92,246,0.12); color:#A78BFA;"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <h3>Inventory Control</h3>
                    <p>Real-time stock deduction on menu item orders, reorder level alerts, supplier tracking, and waste management.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(20,184,166,0.12); color:#2DD4BF;"><i class="fa-solid fa-receipt"></i></div>
                    <h3>Expense Management</h3>
                    <p>Record daily operational expenses (groceries, utilities, salaries, maintenance) categorized per restaurant tenant.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(244,63,94,0.12); color:#FB7185;"><i class="fa-solid fa-tags"></i></div>
                    <h3>Offers & Coupons</h3>
                    <p>Create percentage and flat discount coupons with expiry dates, minimum bill thresholds, and auto-apply logic.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(16,185,129,0.12); color:#10B981;"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3>Offline Desktop Sync</h3>
                    <p>Windows Desktop POS continues running offline during internet outages and auto-syncs with cloud MySQL when reconnected.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. HOW IT WORKS -->
    <section class="steps-section" id="how-it-works">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-compass"></i> Simple Onboarding</div>
            <h2 class="section-title">How Tendorabuy Works</h2>
            <p class="section-subtitle">Get your restaurant up and running in 4 quick steps.</p>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-num">01</div>
                    <h3>Create Account</h3>
                    <p>Sign up for your free restaurant account with your restaurant name, contact, and preferred currency.</p>
                </div>

                <div class="step-card">
                    <div class="step-num">02</div>
                    <h3>Add Menu & Tables</h3>
                    <p>Easily input your food categories, dishes, prices, and configure your dining floor tables.</p>
                </div>

                <div class="step-card">
                    <div class="step-num">03</div>
                    <h3>Generate QR Codes</h3>
                    <p>Print high-resolution table QR codes that direct guests to your live digital menu.</p>
                </div>

                <div class="step-card">
                    <div class="step-num">04</div>
                    <h3>Start Taking Orders</h3>
                    <p>Receive orders on POS and Kitchen KDS in real-time, collect payments, and track revenue.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. ONLINE + OFFLINE ARCHITECTURE -->
    <section class="sync-section" id="workflow">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-arrows-rotate"></i> Hybrid Reliability</div>
            <h2 class="section-title">Uninterrupted Operations: Online + Offline</h2>
            <p class="section-subtitle">Never lose an order, even if your restaurant internet connection drops.</p>

            <div class="sync-grid">
                <div class="sync-card cloud">
                    <span class="sync-badge">Cloud Mode (Online)</span>
                    <h3>Real-Time Cloud Workflow</h3>
                    <p>Orders flow instantly from guest table QR scans to POS and Kitchen displays over high-speed cloud infrastructure.</p>
                    <div class="sync-flow">
                        <span>1. Customer scans QR &amp; submits order</span>
                        <span>2. Order saves to Cloud Database</span>
                        <span>3. POS &amp; KDS notify kitchen staff</span>
                        <span>4. Instant UPI payment verification</span>
                    </div>
                </div>

                <div class="sync-card offline">
                    <span class="sync-badge">Offline Mode (Desktop POS)</span>
                    <h3>Local Desktop Resiliency</h3>
                    <p>When internet disconnects, the Windows Desktop POS continues billing locally into SQLite and auto-syncs when back online.</p>
                    <div class="sync-flow">
                        <span>1. POS creates local order in SQLite</span>
                        <span>2. Receipts print via local thermal printer</span>
                        <span>3. Internet returns ➔ Auto-Sync queue fires</span>
                        <span>4. Reconciled with Cloud MySQL (Zero Duplicates)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. RESTAURANT BENEFITS -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-users"></i> Tailored Experience</div>
            <h2 class="section-title">Benefits for Your Entire Team</h2>
            <p class="section-subtitle">Designed specifically for every restaurant stakeholder.</p>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div style="font-size:24px; margin-bottom:10px;">👔</div>
                    <h4>Restaurant Owner</h4>
                    <p>Live sales analytics, expense tracking, menu engineering matrix, and multi-branch control.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size:24px; margin-bottom:10px;">💳</div>
                    <h4>Cashier</h4>
                    <p>Speedy billing, split bills, UPI QR soundbox confirmation, and instant thermal receipt printing.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size:24px; margin-bottom:10px;">🏃</div>
                    <h4>Waiter</h4>
                    <p>Digital waiter calls with table numbers, mobile table ordering, and instant dish ready notifications.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size:24px; margin-bottom:10px;">👨‍🍳</div>
                    <h4>Kitchen Chef</h4>
                    <p>Color-coded KDS screen, zero missed paper tickets, preparation countdown, and single-click completion.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size:24px; margin-bottom:10px;">📱</div>
                    <h4>Customer</h4>
                    <p>Zero wait time, contactless digital menu with voice ordering, live order tracker, and easy UPI payment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. PRICING -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-gem"></i> Transparent Pricing</div>
            <h2 class="section-title">Simple Plans for Restaurants of Any Size</h2>
            <p class="section-subtitle">Start 100% free with no credit card required. Upgrade as your restaurant expands.</p>

            <div class="pricing-grid">
                <!-- Free Plan -->
                <div class="price-card">
                    <div class="plan-name">Free Starter</div>
                    <div class="plan-price">₹0 <span>/ forever</span></div>
                    <ul class="plan-features">
                        <li><i class="fa-solid fa-check"></i> Up to 10 Dining Tables</li>
                        <li><i class="fa-solid fa-check"></i> Up to 5 Staff Accounts</li>
                        <li><i class="fa-solid fa-check"></i> Up to 100 Menu Items</li>
                        <li><i class="fa-solid fa-check"></i> 500 Orders / Month</li>
                        <li><i class="fa-solid fa-check"></i> QR Code Menus &amp; KDS</li>
                        <li><i class="fa-solid fa-check"></i> Standard POS Billing</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-primary" style="width:100%;">Get Started Free</a>
                </div>

                <!-- Pro Plan -->
                <div class="price-card featured">
                    <div class="featured-tag">Most Popular</div>
                    <div class="plan-name">Pro Growth</div>
                    <div class="plan-price">₹1,499 <span>/ month</span></div>
                    <ul class="plan-features">
                        <li><i class="fa-solid fa-check"></i> Up to 50 Dining Tables</li>
                        <li><i class="fa-solid fa-check"></i> Up to 20 Staff Accounts</li>
                        <li><i class="fa-solid fa-check"></i> Up to 500 Menu Items</li>
                        <li><i class="fa-solid fa-check"></i> 5,000 Orders / Month</li>
                        <li><i class="fa-solid fa-check"></i> Offline Desktop Sync</li>
                        <li><i class="fa-solid fa-check"></i> Advanced Sales &amp; Matrix Analytics</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-primary" style="width:100%;">Start 14-Day Trial</a>
                </div>

                <!-- Enterprise Plan -->
                <div class="price-card">
                    <div class="plan-name">Enterprise</div>
                    <div class="plan-price">₹3,999 <span>/ month</span></div>
                    <ul class="plan-features">
                        <li><i class="fa-solid fa-check"></i> Unlimited Tables &amp; Staff</li>
                        <li><i class="fa-solid fa-check"></i> Unlimited Menu Items</li>
                        <li><i class="fa-solid fa-check"></i> Unlimited Monthly Orders</li>
                        <li><i class="fa-solid fa-check"></i> Multi-Branch Management</li>
                        <li><i class="fa-solid fa-check"></i> Dedicated Account Manager</li>
                        <li><i class="fa-solid fa-check"></i> Priority 24/7 Phone Support</li>
                    </ul>
                    <a href="/contact" class="btn-ghost" style="width:100%; border:1px solid var(--border); text-align:center;">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FAQ -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-circle-question"></i> Questions &amp; Answers</div>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Everything you need to know about Tendorabuy.</p>

            <div class="faq-list">
                <div class="faq-item">
                    <h4>Is Tendorabuy free to use?</h4>
                    <p>Yes! Our Free Starter Plan is 100% free forever with no credit card required. It includes up to 10 tables, 5 staff accounts, 100 menu items, POS billing, and QR ordering.</p>
                </div>

                <div class="faq-item">
                    <h4>Do customers need to download an application to order?</h4>
                    <p>No. Customers simply scan the QR code on their dining table with their phone camera. The digital menu opens instantly in any mobile browser.</p>
                </div>

                <div class="faq-item">
                    <h4>Can POS work offline when internet is down?</h4>
                    <p>Yes. Our Windows Desktop POS app saves orders locally in an offline SQLite database. When internet connection returns, all orders automatically synchronize to the cloud.</p>
                </div>

                <div class="faq-item">
                    <h4>Can I connect standard thermal receipt printers?</h4>
                    <p>Yes. Tendorabuy supports standard 58mm and 80mm ESC/POS thermal printers, network printers, and standard A4 tax invoices.</p>
                </div>

                <div class="faq-item">
                    <h4>Can multiple independent restaurants use the platform?</h4>
                    <p>Yes. Tendorabuy features strict multi-tenant data isolation. Each restaurant has its own private menu, customer base, orders, inventory, and staff.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FINAL CTA -->
    <section class="cta-banner">
        <div class="container">
            <div class="cta-box">
                <h2>Start Your Restaurant Free Today</h2>
                <p>Join hundreds of modern restaurants accelerating their table turnover and kitchen speed with Tendorabuy.</p>
                <a href="{{ route('register') }}" class="btn-primary" style="padding: 16px 36px; font-size: 17px;">
                    <span>START FREE</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="brand-logo">
                        <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                        <div class="brand-name">Tendorabuy</div>
                    </a>
                    <p>Complete restaurant management platform, smart table QR ordering, kitchen display system, and POS billing.</p>
                </div>

                <div class="footer-col">
                    <h5>Product</h5>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#workflow">Online + Offline</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Account &amp; Auth</h5>
                    <ul>
                        <li><a href="{{ route('register') }}">Start Free</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('auth.pin-login') }}">Quick PIN Login</a></li>
                        <li><a href="/contact">Support</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Legal &amp; Info</h5>
                    <ul>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="/contact">Contact Sales</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; 2026 Tendorabuy. All rights reserved.</div>
                <div>Designed for High-Performance Restaurant Operations</div>
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
    </script>
</body>
</html>
