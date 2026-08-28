<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans — Tendorabuy Restaurant Management</title>
    <meta name="description" content="Simple, transparent restaurant software pricing. Start with our 100% Free Plan or choose Pro and Enterprise growth tiers.">
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
            --bg: #090D1A;
            --card: #131A35;
            --border: rgba(255, 255, 255, 0.08);
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.6;
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 24px;
            width: 100%;
        }

        /* Top Bar */
        .topbar {
            height: 76px;
            background: rgba(9, 13, 26, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #6366F1, #818CF8);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
        }

        .btn-ghost {
            padding: 9px 18px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }

        .btn-primary {
            padding: 9px 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
        }

        /* Hero */
        .pricing-hero {
            padding: 60px 0 40px;
            text-align: center;
        }

        .pricing-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .pricing-hero p {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Grid */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin-bottom: 60px;
        }

        .pricing-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            display: flex;
            flex-direction: column;
            position: relative;
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
        }

        .card-price {
            font-family: 'Outfit', sans-serif;
            font-size: 40px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 24px;
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
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-list li i {
            color: var(--success);
            font-size: 14px;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            border-top: 1px solid var(--border);
            padding: 30px 0;
            text-align: center;
            font-size: 13px;
            color: #64748B;
        }

        @media (max-width: 900px) {
            .pricing-grid { grid-template-columns: 1fr; }
            .pricing-hero h1 { font-size: 32px; }
        }
    </style>
</head>
<body>

    <header class="topbar">
        <div class="container topbar-inner">
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                <div class="brand-name">Tendorabuy</div>
            </a>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                <a href="{{ route('register') }}" class="btn-primary">Start Free</a>
            </div>
        </div>
    </header>

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
                <a href="/contact" class="btn-ghost" style="width:100%; border:1px solid var(--border); text-align:center;">Contact Sales</a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            &copy; 2026 Tendorabuy. All rights reserved. &bull; <a href="/terms" style="color:#94A3B8; text-decoration:none;">Terms</a> &bull; <a href="/privacy" style="color:#94A3B8; text-decoration:none;">Privacy</a> &bull; <a href="/contact" style="color:#94A3B8; text-decoration:none;">Contact</a>
        </div>
    </footer>

</body>
</html>
