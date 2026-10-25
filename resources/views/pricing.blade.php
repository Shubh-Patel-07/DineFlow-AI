<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Pricing Plans — DineFlow Restaurant Management</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="description" content="Simple, transparent restaurant software pricing. Start with our 100% Free Plan or choose Pro and Enterprise growth tiers. WhatsApp: +91 7777938338.">

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
            --whatsapp: #25D366;
            --whatsapp-dark: #128C7E;
            --bg: #090D1A;
            --card: #131A35;
            --border: rgba(255, 255, 255, 0.08);
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
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
            background: linear-gradient(135deg, #6366F1, #818CF8);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #fff;
            box-shadow: 0 0 12px var(--primary-glow);
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
            justify-content: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            min-height: 40px;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
        }

        .mobile-menu-btn {
            display: none;
            width: 40px;
            height: 40px;
            background: var(--card);
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
            background: rgba(99, 102, 241, 0.12);
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
        .pricing-hero {
            padding: clamp(36px, 6vw, 60px) 0 clamp(24px, 4vw, 40px);
            text-align: center;
            width: 100%;
        }

        .pricing-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(26px, 4.5vw, 42px);
            font-weight: 900;
            line-height: 1.2;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            color: #fff;
        }

        .pricing-hero p {
            font-size: clamp(14.5px, 1.8vw, 16px);
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ─── PRICING GRID ─── */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: clamp(16px, 2.5vw, 28px);
            margin-bottom: clamp(40px, 6vw, 60px);
            width: 100%;
        }

        .pricing-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: clamp(24px, 3.5vw, 36px);
            display: flex;
            flex-direction: column;
            position: relative;
            width: 100%;
            min-width: 0;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .pricing-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.4);
        }

        .pricing-card.featured {
            border-color: var(--primary);
            box-shadow: 0 0 30px var(--primary-glow);
            background: linear-gradient(180deg, #18224B 0%, #131A35 100%);
        }

        .featured-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px var(--primary-glow);
        }

        .card-header h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .card-header p {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .card-price {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(32px, 4vw, 40px);
            font-weight: 900;
            color: #fff;
            margin-bottom: 24px;
            letter-spacing: -0.5px;
        }

        .card-price span {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .feature-list {
            list-style: none;
            margin-bottom: 32px;
            flex: 1;
        }

        .feature-list li {
            font-size: 13.5px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.45;
        }

        .feature-list li i {
            color: var(--success);
            font-size: 13px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        /* ─── FOOTER ─── */
        .footer {
            margin-top: auto;
            border-top: 1px solid var(--border);
            padding: 24px 0;
            text-align: center;
            font-size: 13px;
            color: #64748B;
            width: 100%;
        }

        /* ─── RESPONSIVE BREAKPOINTS (320px - 960px) ─── */
        @media (max-width: 960px) {
            .nav-links-desktop { display: none; }
            .desktop-auth-btns { display: none; }
            .mobile-menu-btn { display: flex; }
            .pricing-grid { grid-template-columns: 1fr; max-width: 480px; margin-left: auto; margin-right: auto; }
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
                <li><a href="/demo">Live Demo</a></li>
                <li><a href="/#faq">FAQ</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>

            <!-- Header Actions -->
            <div class="nav-actions">
                <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20pricing%20plans." target="_blank" class="btn-wa-header" title="Chat on WhatsApp">
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
            <li><a href="/pricing" class="active"><i class="fa-solid fa-tags" style="color:var(--primary);"></i> Pricing Plans</a></li>
            <li><a href="/#faq" onclick="toggleMobileDrawer()"><i class="fa-solid fa-circle-question" style="color:var(--primary);"></i> FAQ</a></li>
            <li><a href="/contact"><i class="fa-solid fa-envelope" style="color:var(--primary);"></i> Contact Sales</a></li>
            <li>
                <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20pricing%20plans." target="_blank" style="color: #4ADE80;">
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
        <section class="pricing-hero">
            <h1>Simple, Predictable Restaurant Pricing</h1>
            <p>Every plan includes POS billing, QR digital menu, kitchen KDS, and table management.</p>
        </section>

        <section class="pricing-grid">
            <!-- Free Plan -->
            <div class="pricing-card">
                <div class="card-header">
                    <h3>Free Starter</h3>
                    <p>Perfect for food trucks, cafés, and emerging restaurants.</p>
                </div>
                <div class="card-price">₹0 <span>/ forever</span></div>
                <ul class="feature-list">
                    <li><i class="fa-solid fa-check"></i> Up to 10 Dining Tables</li>
                    <li><i class="fa-solid fa-check"></i> Up to 5 Staff Accounts</li>
                    <li><i class="fa-solid fa-check"></i> Up to 100 Menu Items</li>
                    <li><i class="fa-solid fa-check"></i> 500 Orders / Month</li>
                    <li><i class="fa-solid fa-check"></i> Smart QR Menus &amp; KDS</li>
                    <li><i class="fa-solid fa-check"></i> Standard POS Billing</li>
                </ul>
                <a href="{{ route('register') }}" class="btn-primary" style="width:100%;">Create Free Account</a>
            </div>

            <!-- Pro Plan -->
            <div class="pricing-card featured">
                <div class="featured-badge">Most Popular</div>
                <div class="card-header">
                    <h3>Pro Growth</h3>
                    <p>For high-volume restaurants requiring offline sync and deeper insights.</p>
                </div>
                <div class="card-price">₹1,499 <span>/ month</span></div>
                <ul class="feature-list">
                    <li><i class="fa-solid fa-check"></i> Up to 50 Dining Tables</li>
                    <li><i class="fa-solid fa-check"></i> Up to 20 Staff Accounts</li>
                    <li><i class="fa-solid fa-check"></i> Up to 500 Menu Items</li>
                    <li><i class="fa-solid fa-check"></i> 5,000 Orders / Month</li>
                    <li><i class="fa-solid fa-check"></i> Offline Windows Desktop Sync</li>
                    <li><i class="fa-solid fa-check"></i> Advanced Sales &amp; Matrix Analytics</li>
                </ul>
                <a href="{{ route('register') }}" class="btn-primary" style="width:100%;">Start 14-Day Trial</a>
            </div>

            <!-- Enterprise Plan -->
            <div class="pricing-card">
                <div class="card-header">
                    <h3>Enterprise</h3>
                    <p>For restaurant chains, hotel dining, and multi-branch franchises.</p>
                </div>
                <div class="card-price">₹3,999 <span>/ month</span></div>
                <ul class="feature-list">
                    <li><i class="fa-solid fa-check"></i> Unlimited Tables &amp; Staff</li>
                    <li><i class="fa-solid fa-check"></i> Unlimited Menu Items</li>
                    <li><i class="fa-solid fa-check"></i> Unlimited Monthly Orders</li>
                    <li><i class="fa-solid fa-check"></i> Multi-Branch Control</li>
                    <li><i class="fa-solid fa-check"></i> Dedicated Account Manager</li>
                    <li><i class="fa-solid fa-check"></i> Priority 24/7 Phone Support</li>
                </ul>
                <a href="/contact" class="btn-ghost" style="width:100%; margin-bottom:10px;">Contact Sales Form</a>
                <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20am%20interested%20in%20the%20Enterprise%20Plan%20for%20my%20restaurant." target="_blank" class="btn-primary" style="width:100%; background:linear-gradient(135deg, #25D366, #128C7E); box-shadow: 0 4px 14px rgba(37,211,102,0.3);"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
            </div>
        </section>

        <!-- Buy Source Code Callout Banner -->
        <section style="margin-top: 48px; background: linear-gradient(135deg, rgba(236,72,153,0.12), rgba(99,102,241,0.12)); border: 1.5px solid rgba(236,72,153,0.35); border-radius: 18px; padding: 30px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
            <div>
                <div style="display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:800; color:#F472B6; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                    <i class="fa-solid fa-fire"></i> Developer &amp; Agency Special
                </div>
                <h3 style="font-family:'Outfit',sans-serif; font-size:22px; font-weight:800; color:#fff; margin-bottom:6px;">
                    Looking to Acquire the Complete Source Code?
                </h3>
                <p style="font-size:14px; color:#CBD5E1; max-width:640px; margin:0;">
                    Own the full, unencrypted codebase of DineFlow &amp; DineFlow AI. White-label it, launch your own SaaS brand, and retain 100% client subscription revenues with a Commercial License.
                </p>
            </div>
            <a href="/buy-source-code" class="btn-primary" style="background: linear-gradient(135deg, #EC4899, #8B5CF6); box-shadow: 0 4px 20px rgba(236,72,153,0.35); font-weight:800; padding: 12px 24px; white-space:nowrap;">
                <span>🔥 Buy Source Code ($2,499)</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            &copy; 2026 DineFlow. All rights reserved. &bull; <a href="/terms" style="color:#94A3B8; text-decoration:none;">Terms</a> &bull; <a href="/privacy" style="color:#94A3B8; text-decoration:none;">Privacy</a> &bull; <a href="/contact" style="color:#94A3B8; text-decoration:none;">Contact</a>
        </div>
    </footer>

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
    </script>
</body>
</html>
