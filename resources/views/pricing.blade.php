<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans — DineFlow AI Restaurant Management</title>
    <meta name="description" content="Simple, transparent restaurant software pricing. Start with our 100% Free Plan or view upcoming Pro plans.">
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
            font-size: 22px;
            font-weight: 800;
            color: #fff;
        }

        /* Pricing Section */
        .pricing-hero {
            padding: 80px 0 40px;
            text-align: center;
        }

        .pricing-tag {
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

        .pricing-title {
            font-family: 'Outfit', sans-serif;
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 12px;
        }

        .pricing-sub {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 540px;
            margin: 0 auto;
        }

        /* Pricing Cards Grid */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin: 50px 0 100px;
        }

        .pricing-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: all 0.3s ease;
        }

        .pricing-card.featured {
            border-color: var(--primary);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.2);
            transform: scale(1.03);
        }

        .badge-popular {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plan-header {
            margin-bottom: 24px;
        }

        .plan-name {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .plan-desc {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .plan-price-wrap {
            margin: 20px 0;
        }

        .price-amount {
            font-family: 'Outfit', sans-serif;
            font-size: 40px;
            font-weight: 900;
            color: #fff;
        }

        .price-period {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 32px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            color: #E2E8F0;
        }

        .feature-item i {
            color: var(--success);
            font-size: 13px;
        }

        .btn-plan {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            font-family: 'Outfit', sans-serif;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .btn-plan-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 4px 16px var(--primary-glow);
        }

        .btn-plan-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
        }

        .btn-plan-disabled {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            cursor: not-allowed;
        }

        @media (max-width: 960px) {
            .pricing-grid { grid-template-columns: 1fr; }
            .pricing-card.featured { transform: none; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-fire-burner"></i></div>
                <div class="brand-name">DineFlow AI</div>
            </a>

            <div style="display:flex; gap:12px; align-items:center;">
                <a href="{{ route('login') }}" style="color:var(--text-secondary); text-decoration:none; font-size:14px; font-weight:600;">Sign In</a>
                <a href="{{ route('register') }}" style="padding:8px 18px; background:var(--primary); color:#fff; text-decoration:none; border-radius:8px; font-size:13px; font-weight:700;">Start Free</a>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="container" style="flex:1;">
        <section class="pricing-hero">
            <div class="pricing-tag"><i class="fa-solid fa-tags"></i> Transparent Pricing</div>
            <h1 class="pricing-title">Simple Plans for Restaurants of Any Size</h1>
            <p class="pricing-sub">Get started on our 100% Free Plan with zero commitments or setup fees.</p>
        </section>

        <section class="pricing-grid">
            <!-- 1. FREE PLAN -->
            <div class="pricing-card featured">
                <div class="badge-popular">Current Plan</div>
                <div>
                    <div class="plan-header">
                        <h2 class="plan-name">Free Starter</h2>
                        <p class="plan-desc">Complete core restaurant POS and QR ordering essentials.</p>
                    </div>

                    <div class="plan-price-wrap">
                        <span class="price-amount">₹0</span>
                        <span class="price-period">/ forever</span>
                    </div>

                    <ul class="feature-list">
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>10 Tables</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>5 Staff Accounts</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>100 Menu Dishes</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>500 Orders / month</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Table QR Menu & Ordering</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Real-time Kitchen Display (KDS)</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> POS Terminal & UPI Billing</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Thermal Receipts & WhatsApp</li>
                    </ul>
                </div>

                <a href="{{ route('register') }}" class="btn-plan btn-plan-primary">
                    Get Started Free <i class="fa-solid fa-arrow-right" style="margin-left:6px;"></i>
                </a>
            </div>

            <!-- 2. PRO PLAN (COMING SOON) -->
            <div class="pricing-card">
                <div>
                    <div class="plan-header">
                        <h2 class="plan-name">Pro Growth</h2>
                        <p class="plan-desc">For busy dining restaurants and multi-floor operations.</p>
                    </div>

                    <div class="plan-price-wrap">
                        <span class="price-amount">₹1,499</span>
                        <span class="price-period">/ month</span>
                    </div>

                    <ul class="feature-list">
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>50 Tables</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>25 Staff Accounts</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>500 Menu Dishes</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>5,000 Orders / month</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Multi-station Kitchen KDS</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> AI Demand & Stock Forecasting</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Loyalty & Custom Coupons</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Priority 24/7 Support</li>
                    </ul>
                </div>

                <button class="btn-plan btn-plan-disabled" disabled>
                    Coming Soon
                </button>
            </div>

            <!-- 3. ENTERPRISE PLAN (COMING SOON) -->
            <div class="pricing-card">
                <div>
                    <div class="plan-header">
                        <h2 class="plan-name">Enterprise</h2>
                        <p class="plan-desc">Custom solutions for restaurant chains and franchises.</p>
                    </div>

                    <div class="plan-price-wrap">
                        <span class="price-amount">₹3,999</span>
                        <span class="price-period">/ month</span>
                    </div>

                    <ul class="feature-list">
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>Unlimited Tables</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>Unlimited Staff</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>Unlimited Menu Dishes</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> <strong>Unlimited Orders</strong></li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Multi-Outlet Central Dashboard</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Custom ERP & Accounting API</li>
                        <li class="feature-item"><i class="fa-solid fa-check"></i> Dedicated Account Manager</li>
                    </ul>
                </div>

                <button class="btn-plan btn-plan-disabled" disabled>
                    Coming Soon
                </button>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer style="background:#060913; border-top:1px solid var(--border); padding:30px 0; font-size:13px; color:var(--text-secondary); text-align:center;">
        <div class="container">
            &copy; 2026 DineFlow AI Inc. All rights reserved. • <a href="/" style="color:#818CF8; text-decoration:none;">Back to Home</a>
        </div>
    </footer>

</body>
</html>
