<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact Sales &amp; Support — Tendorabuy POS</title>
    <meta name="description" content="Get in touch with Tendorabuy restaurant POS team. Book a guided demo, request custom pricing, or chat directly on WhatsApp at +91 7777938338.">

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
            --success-glow: rgba(16, 185, 129, 0.3);
            --whatsapp: #25D366;
            --whatsapp-dark: #128C7E;
            --whatsapp-glow: rgba(37, 211, 102, 0.35);
            --bg: #090D1A;
            --bg-card: #131A35;
            --border: rgba(255, 255, 255, 0.08);
            --border-focus: rgba(99, 102, 241, 0.6);
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
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
            overflow-x: hidden;
        }

        .container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 20px;
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
            position: sticky;
            top: 0;
            z-index: 100;
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
            box-shadow: 0 0 16px var(--primary-glow);
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
            gap: 10px;
        }

        .btn-ghost {
            padding: 8px 16px;
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

        .btn-wa-header {
            padding: 8px 16px;
            background: rgba(37, 211, 102, 0.15);
            border: 1px solid rgba(37, 211, 102, 0.35);
            color: #4ADE80;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 700;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-wa-header:hover {
            background: var(--whatsapp);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--whatsapp-glow);
        }

        .btn-primary {
            padding: 9px 18px;
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
            transform: translateY(-1px);
            box-shadow: 0 4px 16px var(--primary-glow);
        }

        /* Hero */
        .contact-hero {
            padding: 48px 0 32px;
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
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .contact-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 38px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .contact-hero p {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 620px;
            margin: 0 auto;
        }

        /* Grid */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 32px;
            margin-bottom: 70px;
            align-items: start;
        }

        /* Left Column Cards */
        .info-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 20px;
        }

        .wa-spotlight-card {
            background: linear-gradient(145deg, #064E3B 0%, #0D2818 50%, #131A35 100%);
            border: 1px solid rgba(37, 211, 102, 0.4);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: 0 8px 24px rgba(6, 78, 59, 0.4);
            position: relative;
            overflow: hidden;
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
        }

        .wa-spotlight-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }

        .wa-spotlight-card p {
            font-size: 14px;
            color: #D1FAE5;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .btn-wa-action {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff;
            text-decoration: none;
            padding: 13px 24px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.4);
            width: fit-content;
        }

        .btn-wa-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.5);
            background: #25D366;
        }

        .contact-channel {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 12px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        .contact-channel:hover {
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.08);
            transform: translateX(4px);
        }

        .channel-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(99, 102, 241, 0.15);
            color: #818CF8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .channel-info h4 {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .channel-info p {
            font-size: 14.5px;
            font-weight: 600;
            color: #fff;
        }

        /* Right Form Card */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.3);
        }

        .form-card h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .form-card > p {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 6px;
        }

        .form-group label span.req {
            color: #EF4444;
        }

        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
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
            padding: 14px 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
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
            padding: 24px;
            text-align: center;
            margin-top: 10px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .success-state {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        .success-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.2);
            color: #34D399;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 14px;
        }

        .success-state h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .success-state p {
            font-size: 14px;
            color: #A7F3D0;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .error-state {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #FCA5A5;
            font-size: 14px;
            margin-bottom: 16px;
        }

        /* Honeypot field (hidden from real users) */
        .hp-field {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
        }

        /* Footer */
        .footer {
            margin-top: auto;
            border-top: 1px solid var(--border);
            padding: 26px 0;
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Mobile & Tablet Responsiveness */
        @media (max-width: 900px) {
            .contact-grid { grid-template-columns: 1fr; }
            .contact-hero h1 { font-size: 30px; }
            .form-card { padding: 24px 18px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
        }

        @media (max-width: 480px) {
            .container { padding: 0 14px; }
            .brand-name { font-size: 18px; }
            .btn-wa-header span { display: none; }
            .btn-wa-header { padding: 8px 12px; }
            .contact-hero { padding: 32px 0 20px; }
            .contact-hero h1 { font-size: 26px; }
            .wa-spotlight-card { padding: 20px; }
            .wa-spotlight-card h3 { font-size: 20px; }
            .btn-wa-action { width: 100%; justify-content: center; font-size: 14px; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                <div class="brand-name">Tendorabuy</div>
            </a>
            <div class="nav-actions">
                <a href="/pricing" class="btn-ghost">Pricing</a>
                <a href="/demo" class="btn-ghost">Demo</a>
                <a href="https://wa.me/917777938338?text=Hello%20Tendorabuy%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20Tendorabuy%20for%20my%20restaurant." target="_blank" class="btn-wa-header" title="Chat on WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>+91 7777938338</span>
                </a>
                <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                <a href="{{ route('register') }}" class="btn-primary">Start Free</a>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- Hero Section -->
        <section class="contact-hero">
            <div class="section-tag"><i class="fa-solid fa-headset"></i> Dedicated Support &amp; Sales</div>
            <h1>Let’s Talk Restaurant Growth</h1>
            <p>Have questions about setting up Tendorabuy in your restaurant, QR billing, or want a custom enterprise plan? We respond within minutes.</p>
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
                    <a href="https://wa.me/917777938338?text=Hello%20Tendorabuy%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20Tendorabuy%20for%20my%20restaurant." target="_blank" class="btn-wa-action">
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

                    <a href="mailto:support@tendorabuy.in" class="contact-channel">
                        <div class="channel-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div class="channel-info">
                            <h4>Email Support &amp; Sales</h4>
                            <p>support@tendorabuy.in</p>
                        </div>
                    </a>

                    <div class="contact-channel" style="cursor:default;">
                        <div class="channel-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="channel-info">
                            <h4>Headquarters</h4>
                            <p>Tendorabuy Technologies, Gujarat, India</p>
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
                            <div class="field-error" id="err-phone">Please enter a valid 10-digit phone number.</div>
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
                    <p>Our Tendorabuy team will contact you shortly. Want an immediate response?</p>

                    <a id="successWhatsAppBtn" href="https://wa.me/917777938338?text=Hello%20Tendorabuy%20Team%2C%20I%20just%20submitted%20an%20enquiry%20for%20my%20restaurant." target="_blank" class="btn-wa-action" style="margin: 0 auto; display: inline-flex;">
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
            &copy; 2026 Tendorabuy. All rights reserved. &bull; <a href="/terms" style="color:#94A3B8; text-decoration:none;">Terms</a> &bull; <a href="/privacy" style="color:#94A3B8; text-decoration:none;">Privacy</a> &bull; <a href="/demo" style="color:#94A3B8; text-decoration:none;">Live Demo</a>
        </div>
    </footer>

    <!-- Form Handler Script -->
    <script>
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
                    const customWaText = encodeURIComponent(`Hello Tendorabuy Team, I just submitted an enquiry for my restaurant "${restaurantName}". My name is ${name}.`);
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
