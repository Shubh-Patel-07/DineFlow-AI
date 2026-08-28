<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Experience Live Interactive Demo — Tendorabuy POS</title>
    <meta name="description" content="Test drive Tendorabuy restaurant POS, QR ordering menu, and Kitchen KDS with our interactive live demo sandboxes. No sign-up or credit card required.">

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
            --border: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(99, 102, 241, 0.3);
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
            max-width: 1180px;
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
            width: 40px;
            height: 40px;
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

        .nav-actions {
            display: flex;
            align-items: center;
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
            padding: 10px 22px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px var(--primary-glow);
        }

        .btn-success {
            padding: 10px 22px;
            background: linear-gradient(135deg, #10B981, #059669);
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
        }

        /* Hero */
        .demo-hero {
            padding: 60px 0 40px;
            text-align: center;
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
            margin-bottom: 16px;
        }

        .demo-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 44px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .demo-hero p {
            font-size: 16.5px;
            color: var(--text-secondary);
            max-width: 680px;
            margin: 0 auto;
        }

        /* 3 Interactive Cards */
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin-bottom: 60px;
        }

        .demo-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            text-align: center;
            position: relative;
            transition: all 0.25s ease;
        }

        .demo-card:hover {
            transform: translateY(-6px);
            border-color: var(--border-glow);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
        }

        .demo-card.highlight {
            border-color: #6366F1;
            background: linear-gradient(180deg, #162044 0%, #131A35 100%);
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.2);
        }

        .demo-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .demo-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }

        .demo-card p {
            font-size: 13.5px;
            color: var(--text-secondary);
            margin-bottom: 24px;
            flex: 1;
        }

        .demo-card ul {
            list-style: none;
            text-align: left;
            margin-bottom: 24px;
            background: rgba(0, 0, 0, 0.2);
            padding: 16px;
            border-radius: 12px;
        }

        .demo-card ul li {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-card ul li:last-child { margin-bottom: 0; }
        .demo-card ul li i { color: #10B981; font-size: 12px; }

        /* Form + WhatsApp Section */
        .demo-booking-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 80px;
        }

        .booking-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
        }

        .booking-card h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }

        .booking-card p {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: var(--primary);
        }

        .whatsapp-card {
            background: linear-gradient(135deg, #064E3B 0%, #131A35 100%);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 20px;
            padding: 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .wa-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(16, 185, 129, 0.2);
            color: #34D399;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
            width: fit-content;
        }

        .whatsapp-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 12px;
        }

        .whatsapp-card p {
            font-size: 14.5px;
            color: #A7F3D0;
            margin-bottom: 28px;
            line-height: 1.6;
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
            .demo-grid { grid-template-columns: 1fr; }
            .demo-booking-wrap { grid-template-columns: 1fr; }
            .demo-hero h1 { font-size: 32px; }
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
                <a href="/pricing" class="btn-ghost">Pricing</a>
                <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                <a href="{{ route('register') }}" class="btn-primary">Start Free</a>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- 1. HERO -->
        <section class="demo-hero">
            <div class="section-tag"><i class="fa-solid fa-play"></i> Instant Sandbox</div>
            <h1>Experience Tendorabuy in Action</h1>
            <p>Try live interactive demos of Customer QR ordering, Cashier POS billing, and Kitchen KDS displays. No credit card or sign-up needed.</p>
        </section>

        <!-- 2. 3 INTERACTIVE SANDBOX CARDS -->
        <section class="demo-grid">
            <!-- Customer QR Demo -->
            <div class="demo-card highlight">
                <div class="demo-icon-wrap" style="background: rgba(99, 102, 241, 0.15); color: #818CF8;">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h3>1. Customer QR Menu Demo</h3>
                <p>Experience how your restaurant guests scan QR codes, browse veg/non-veg dishes, use voice search, customize items, and pay via UPI.</p>
                <ul>
                    <li><i class="fa-solid fa-check"></i> Live Digital Menu with Photos</li>
                    <li><i class="fa-solid fa-check"></i> Voice Ordering in Gujarati/Hindi/English</li>
                    <li><i class="fa-solid fa-check"></i> Real-time Cart &amp; Split Bill</li>
                    <li><i class="fa-solid fa-check"></i> Live Order Tracker</li>
                </ul>
                <a href="/customer/menu?restaurantId=1&tableId=1" target="_blank" class="btn-primary" style="width:100%;">
                    <span>Launch Customer QR Demo</span>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>

            <!-- POS Terminal Demo -->
            <div class="demo-card">
                <div class="demo-icon-wrap" style="background: rgba(244, 114, 182, 0.15); color: #F472B6;">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <h3>2. POS Billing Terminal Demo</h3>
                <p>Test drive the fast touch billing interface, table occupancy grid, discounts, dynamic UPI soundbox simulation, and thermal receipt generation.</p>
                <ul>
                    <li><i class="fa-solid fa-check"></i> Quick Touch Terminal</li>
                    <li><i class="fa-solid fa-check"></i> Interactive Floor Tables</li>
                    <li><i class="fa-solid fa-check"></i> 58mm/80mm Thermal Receipts</li>
                    <li><i class="fa-solid fa-check"></i> Multi-Payment Settlement</li>
                </ul>
                <a href="/pos-terminal" target="_blank" class="btn-primary" style="width:100%;">
                    <span>Launch POS Terminal Demo</span>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>

            <!-- Kitchen KDS Demo -->
            <div class="demo-card">
                <div class="demo-icon-wrap" style="background: rgba(16, 185, 129, 0.15); color: #34D399;">
                    <i class="fa-solid fa-fire-burner"></i>
                </div>
                <h3>3. Kitchen KDS Screen Demo</h3>
                <p>See how your kitchen chefs receive orders in real-time, monitor preparation countdown timers, hear sound alerts, and update dish readiness.</p>
                <ul>
                    <li><i class="fa-solid fa-check"></i> Live Ticket Inflow</li>
                    <li><i class="fa-solid fa-check"></i> Color-coded Order Aging</li>
                    <li><i class="fa-solid fa-check"></i> Audio Chime Alerts</li>
                    <li><i class="fa-solid fa-check"></i> Single-Tap Stage Progression</li>
                </ul>
                <a href="/kitchen" target="_blank" class="btn-primary" style="width:100%;">
                    <span>Launch Kitchen KDS Demo</span>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </section>

        <!-- 3. BOOK 1-ON-1 DEMO + WHATSAPP -->
        <section class="demo-booking-wrap">
            <!-- Booking Form -->
            <div class="booking-card">
                <h2>Request a Personalized Demo Call</h2>
                <p>Our restaurant specialists will give you a live guided tour and help customize your menu and floor layout.</p>

                <form id="demoRequestForm" onsubmit="submitDemoRequest(event)">
                    <div class="form-group">
                        <label>Your Full Name *</label>
                        <input type="text" id="demoName" required class="form-input" placeholder="e.g. Rahul Patel">
                    </div>

                    <div class="form-group">
                        <label>Restaurant / Business Name *</label>
                        <input type="text" id="demoRestName" required class="form-input" placeholder="e.g. Spice Symphony Bistro">
                    </div>

                    <div class="form-group">
                        <label>WhatsApp / Mobile Number *</label>
                        <input type="tel" id="demoPhone" required class="form-input" placeholder="e.g. +91 98765 43210">
                    </div>

                    <div class="form-group">
                        <label>City / Location</label>
                        <input type="text" id="demoCity" class="form-input" placeholder="e.g. Ahmedabad, Surat, Mumbai">
                    </div>

                    <button type="submit" id="demoSubmitBtn" class="btn-primary" style="width:100%; padding:13px; font-size:15px; margin-top:10px;">
                        <span>Schedule My Demo Call</span>
                        <i class="fa-solid fa-calendar-check"></i>
                    </button>
                </form>

                <div id="demoSuccessMsg" style="display:none; margin-top:20px; padding:16px; background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); border-radius:10px; color:#34D399; font-size:14px; text-align:center;">
                    <i class="fa-solid fa-circle-check" style="font-size:20px; display:block; margin-bottom:6px;"></i>
                    Thank you! Our specialist will call/WhatsApp you shortly for your live guided demo.
                </div>
            </div>

            <!-- Instant WhatsApp Demo -->
            <div class="whatsapp-card">
                <div class="wa-badge"><i class="fa-brands fa-whatsapp"></i> Instant Assistance</div>
                <h3>Want an Instant Demo on WhatsApp?</h3>
                <p>Chat directly with our product team. We can send you demo videos, sample QR codes to scan on your phone, and answer any setup questions immediately.</p>

                <a href="https://wa.me/919876543210?text=Hello%20Tendorabuy%2C%20I%20want%20to%20get%20a%20live%20demo%20walkthrough%20of%20the%20restaurant%20POS%20and%20QR%20ordering%20system." target="_blank" class="btn-success" style="padding: 14px 28px; font-size: 16px; width: fit-content;">
                    <i class="fa-brands fa-whatsapp" style="font-size: 20px;"></i>
                    <span>Chat on WhatsApp Now</span>
                </a>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            &copy; 2026 Tendorabuy. All rights reserved. &bull; <a href="/terms" style="color:#94A3B8; text-decoration:none;">Terms</a> &bull; <a href="/privacy" style="color:#94A3B8; text-decoration:none;">Privacy</a> &bull; <a href="/contact" style="color:#94A3B8; text-decoration:none;">Contact</a>
        </div>
    </footer>

    <script>
        async function submitDemoRequest(e) {
            e.preventDefault();
            const btn = document.getElementById('demoSubmitBtn');
            const successBox = document.getElementById('demoSuccessMsg');
            const form = document.getElementById('demoRequestForm');

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting…';

            const payload = {
                name: document.getElementById('demoName').value,
                restaurant_name: document.getElementById('demoRestName').value,
                phone: document.getElementById('demoPhone').value,
                city: document.getElementById('demoCity').value,
            };

            try {
                const res = await fetch('/api/demo/request', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    form.style.display = 'none';
                    successBox.style.display = 'block';
                } else {
                    alert(data.message || 'Could not submit demo request. Please try WhatsApp directly.');
                    btn.disabled = false;
                    btn.innerHTML = '<span>Schedule My Demo Call</span> <i class="fa-solid fa-calendar-check"></i>';
                }
            } catch (err) {
                console.error(err);
                form.style.display = 'none';
                successBox.style.display = 'block';
            }
        }
    </script>
</body>
</html>
