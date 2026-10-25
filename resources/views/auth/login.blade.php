<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineFlow AI POS - Premium Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #9F1239;
            --primary-glow: rgba(159, 18, 57, 0.35);
            --primary-dark: #881337;
            --primary-rose: #FB7185;
            --success: #22C55E;
            --danger: #EF4444;
            --bg: #0B0B0D;
            --card: #151517;
            --border: #26262A;
            --text-primary: #F4F4F5;
            --text-secondary: #A1A1AA;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            overflow-y: auto;
        }


        /* ─── SPLIT LAYOUT ─── */
        .split-left {
            flex: 1;
            background: linear-gradient(135deg, #0B0B0D 0%, #1E0A12 50%, #12060B 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        .split-left::before {
            content: '';
            position: absolute;
            top: -200px; left: -200px; width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(159, 18, 57, 0.22) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-content {
            max-width: 520px;
            z-index: 2;
        }

        .logo-group {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 36px;
        }

        /* Animated Glowing Brand Logo */
        .logo-img-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .logo-img-wrapper::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 18px;
            background: linear-gradient(135deg, #FB7185, #9F1239, #E11D48, #FB7185);
            background-size: 300% 300%;
            animation: logoGradientRotate 4s linear infinite;
            opacity: 0.85;
            filter: blur(8px);
            z-index: 0;
        }

        @keyframes logoGradientRotate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .logo-img {
            position: relative;
            z-index: 1;
            height: 64px;
            width: 64px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(159, 18, 57, 0.5);
            object-fit: cover;
            border: 1px solid rgba(251, 113, 133, 0.3);
            animation: logoFloatPulse 3s ease-in-out infinite alternate;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        @keyframes logoFloatPulse {
            0% {
                transform: translateY(0px) scale(1);
                box-shadow: 0 8px 24px rgba(159, 18, 57, 0.4), 0 0 15px rgba(251, 113, 133, 0.3);
            }
            100% {
                transform: translateY(-5px) scale(1.04);
                box-shadow: 0 16px 36px rgba(225, 29, 72, 0.65), 0 0 25px rgba(251, 113, 133, 0.55);
            }
        }

        .logo-img-wrapper:hover .logo-img {
            transform: translateY(-7px) scale(1.09) rotate(3deg);
            box-shadow: 0 20px 45px rgba(159, 18, 57, 0.85), 0 0 35px rgba(251, 113, 133, 0.85);
        }

        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #fff;
        }

        .logo-text span {
            background: linear-gradient(135deg, #FB7185, #F43F5E, #FDA4AF);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shineText 3s linear infinite;
        }

        @keyframes shineText {
            to { background-position: 200% center; }
        }


        .left-title {
            font-family: 'Outfit', sans-serif;
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(to right, #FFFFFF, #E4E4E7, #A1A1AA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-slider {
            height: 100px;
            margin-top: 30px;
            position: relative;
            overflow: hidden;
        }

        .feature-slide {
            position: absolute;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-in-out;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .feature-slide.active {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-slide h3 {
            font-size: 18px;
            font-weight: 600;
            color: #E2E8F0;
        }

        .feature-slide p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .split-right {
            width: 490px;
            background: #0E0E11;
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 44px;
            position: relative;
            overflow-y: auto;
        }

        /* ─── TABS SWITCHER ─── */
        .tab-group {
            display: flex;
            background: var(--card);
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
        }

        .tab-btn {
            flex: 1;
            padding: 11px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .tab-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        /* ─── FORM ELEMENTS ─── */
        .form-panel {
            display: none;
        }

        .form-panel.active {
            display: block;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 15px;
        }

        .form-control {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #9F1239 0%, #BE123C 100%);
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(159, 18, 57, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 22px rgba(159, 18, 57, 0.55);
            background: linear-gradient(135deg, #881337 0%, #9F1239 100%);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .error-container {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            color: #FCA5A5;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        /* ─── ROLE SELECTOR PILLS ─── */
        .role-pills {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .role-pill {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-pill.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 4px 10px var(--primary-glow);
        }

        /* ─── PIN KEYPAD ─── */
        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin: 20px 0;
        }

        .pin-dot {
            width: 16px;
            height: 16px;
            border: 2px solid var(--border);
            border-radius: 50%;
            transition: all 0.18s ease;
        }

        .pin-dot.active {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 12px var(--primary);
            transform: scale(1.1);
        }

        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            max-width: 310px;
            margin: 0 auto;
        }

        .key {
            height: 52px;
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text-primary);
            font-size: 20px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.12s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .key:active {
            background: var(--primary);
            transform: scale(0.95);
        }

        .key-action {
            font-size: 14px;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.05);
        }

        /* ─── DEMO CREDENTIALS DRAWER ─── */
        .credentials-box {
            background: rgba(159, 18, 57, 0.08);
            border: 1px solid rgba(251, 113, 133, 0.22);
            border-radius: 12px;
            padding: 14px;
            margin-top: 24px;
            font-size: 12.5px;
            color: #FDA4AF;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .demo-chip {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            color: #E2E8F0;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .demo-chip:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            transform: translateY(-1px);
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            body {
                height: 100% !important;
                min-height: 100vh !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                touch-action: pan-y !important;
            }
            .split-left { display: none; }
            .split-right {
                width: 100%;
                min-height: 100vh;
                justify-content: flex-start !important;
                padding: 36px 20px;
                overflow-y: visible !important;
                -webkit-overflow-scrolling: touch !important;
                touch-action: pan-y !important;
            }
        }
        @media (max-width: 480px) {
            .split-right { padding: 24px 14px; }
        }
    </style>
</head>
<body>

    <!-- Left Split -->
    <div class="split-left">
        <div class="left-content">
            <div class="logo-group">
                <div class="logo-img-wrapper">
                    <img src="/images/dineflow_logo.jpg" alt="DineFlow AI Logo" class="logo-img" onerror="this.src='/images/dineflow_logo.png'">
                </div>
                <div class="logo-text">Dine<span>Flow</span> AI</div>
            </div>


            <h1 class="left-title">Next-Gen Restaurant POS Suite</h1>
            
            <div class="feature-slider" id="slider">
                <div class="feature-slide active">
                    <h3>⚡ Walk-In POS Terminal</h3>
                    <p>Speed up checkout with instant order billing, table assignments, and inventory tracking.</p>
                </div>
                <div class="feature-slide">
                    <h3>📱 QR Menu Ordering</h3>
                    <p>Contactless guest table orders and quick translations served directly from customer phones.</p>
                </div>
                <div class="feature-slide">
                    <h3>🖨️ Multi-Printer KOT Routing</h3>
                    <p>Auto-print specific order tickets to Kitchen KOT and Bar KOT printers concurrently.</p>
                </div>
                <div class="feature-slide">
                    <h3>🔔 Live Waiter Chime Alerts</h3>
                    <p>Instant table alerts with synthetic bell sounds displaying on the cashier screen.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Split -->
    <div class="split-right">
        <div style="margin-bottom: 24px; display:flex; align-items:center; gap:14px;">
            <img src="/images/dineflow_logo.jpg" alt="DineFlow AI Logo" style="height:50px; width:50px; border-radius:12px; box-shadow:0 4px 18px rgba(159,18,57,0.4); object-fit:cover; border:1px solid rgba(251,113,133,0.35);" onerror="this.src='/images/dineflow_logo.png'">
            <div>
                <h2 style="font-family:'Outfit', sans-serif; font-size:24px; font-weight:800; margin-bottom:2px; color:#fff;">Welcome Back</h2>
                <p style="color:var(--text-secondary); font-size:13px;">Sign in to access your DineFlow POS dashboard.</p>
            </div>
        </div>


        @if (isset($errors) && $errors->any())
            <div class="error-container">
                <i class="fas fa-exclamation-circle" style="color:var(--danger);"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- PRIMARY LOGIN FORM (EMAIL/USERNAME + PASSWORD) -->
        <div class="form-panel active" id="panelPassword" style="display:block;">
            <form method="POST" action="/login">
                @csrf
                <div class="form-group">
                    <label>Staff Email or Username</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="text" name="username" class="form-control" placeholder="e.g. cashier@restaurant.com or username" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter your staff password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In to Restaurant
                </button>

                <div style="text-align:center; margin-top:16px; font-size:13px; color:var(--text-secondary);">
                    New to DineFlow AI? <a href="/register" style="color:#FB7185; font-weight:700; text-decoration:none;">Start Free Restaurant Account →</a>
                </div>
            </form>
        </div>

        <!-- Login help text -->
        <div class="credentials-box" style="text-align:center; margin-top:16px;">
            <div style="font-weight:600; color:var(--text-secondary); font-size:12px; display:flex; align-items:center; justify-content:center; gap:6px;">
                <i class="fa-solid fa-shield-halved" style="color:var(--primary);"></i> Secure Enterprise Role-Based Authentication
            </div>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        // Feature Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.feature-slide');
        setInterval(() => {
            if (slides.length) {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }
        }, 4000);
    </script>
</body>
</html>

