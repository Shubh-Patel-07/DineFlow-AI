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
            --primary: #4F46E5;
            --primary-glow: rgba(79, 70, 229, 0.15);
            --primary-dark: #3730A3;
            --success: #10B981;
            --danger: #EF4444;
            --danger-light: #FEF2F2;
            --bg: #0F172A;
            --card: #1E293B;
            --border: #334155;
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
            overflow: hidden;
        }

        /* ─── SPLIT LAYOUT ─── */
        .split-left {
            flex: 1;
            background: linear-gradient(-45deg, #1e1b4b, #311042, #1e293b, #0f172a);
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .split-left::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at 20% 30%, rgba(79, 70, 229, 0.15) 0%, transparent 50%);
            pointer-events: none;
        }

        .left-content {
            max-width: 500px;
            z-index: 2;
        }

        .logo-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), #7C3AED);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 8px 16px rgba(79, 70, 229, 0.3);
        }

        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: #818CF8;
        }

        .left-title {
            font-family: 'Outfit', sans-serif;
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(to right, #fff, #94A3B8);
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

        .feature-slide i {
            color: #818CF8;
            font-size: 18px;
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
            width: 480px;
            background: #0B0F19;
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        /* ─── TABS SWITCHER ─── */
        .tab-group {
            display: flex;
            background: var(--card);
            padding: 4px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid var(--border);
        }

        .tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 7px;
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
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }

        /* ─── FORM ELEMENTS ─── */
        .form-panel {
            display: none;
        }

        .form-panel.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
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
            border-radius: 8px;
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
            padding: 12px;
            background: linear-gradient(135deg, var(--primary), #7C3AED);
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }

        .error-container {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--danger);
            color: #FCA5A5;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .error-container i {
            margin-top: 3px;
        }

        /* ─── PIN KEYPAD ─── */
        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin: 24px 0;
        }

        .pin-dot {
            width: 14px;
            height: 14px;
            border: 2px solid var(--border);
            border-radius: 50%;
            transition: all 0.15s;
        }

        .pin-dot.active {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 8px var(--primary);
        }

        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            max-width: 280px;
            margin: 0 auto;
        }

        .key {
            aspect-ratio: 1;
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text-primary);
            font-size: 20px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.1s;
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
            background: rgba(51, 65, 85, 0.4);
        }

        /* ─── DEMO CREDENTIALS BOX ─── */
        .credentials-box {
            background: rgba(79, 70, 229, 0.12);
            border: 1px dashed rgba(79, 70, 229, 0.4);
            border-radius: 12px;
            padding: 14px;
            margin-top: 24px;
            font-size: 12.5px;
            color: #A5B4FC;
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
            .split-left {
                display: none;
            }
            .split-right {
                width: 100%;
                padding: 30px;
            }
        }
    </style>
</head>
<body>

    <!-- Left Split -->
    <div class="split-left">
        <div class="left-content">
            <div class="logo-group">
                <div class="logo-icon">
                    <i class="fa-solid fa-bowl-food"></i>
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
        <div style="margin-bottom: 30px;">
            <h2 style="font-family:'Outfit', sans-serif; font-size:26px; font-weight:800; margin-bottom:6px;">Welcome Back</h2>
            <p style="color:var(--text-secondary); font-size:14px;">Sign in to access your dashboard.</p>
        </div>

        @if ($errors->any())
            <div class="error-container">
                <i class="fas fa-exclamation-circle" style="color:var(--danger);"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Switcher Tabs -->
        <div class="tab-group">
            <button class="tab-btn active" id="tabPassword" onclick="switchTab('password')">
                <i class="fa-solid fa-key"></i> Password
            </button>
            <button class="tab-btn" id="tabPin" onclick="switchTab('pin')">
                <i class="fa-solid fa-fingerprint"></i> Quick PIN
            </button>
        </div>

        <!-- PASSWORD FORM -->
        <div class="form-panel active" id="panelPassword">
            <form method="POST" action="/login">
                @csrf
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" value="{{ old('username') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </button>
            </form>
        </div>

        <!-- PIN FORM -->
        <div class="form-panel" id="panelPin">
            <form method="POST" action="/pin-login" id="pinSubmitForm">
                @csrf
                <div class="form-group" style="margin-bottom: 12px;">
                    <label>Select Username</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user-tag"></i>
                        <select name="username" id="pinUsername" class="form-control" style="padding-left:42px; appearance:none; -webkit-appearance:none;">
                            <option value="admin">admin (Admin User)</option>
                            <option value="manager">manager (Manager)</option>
                            <option value="cashier">cashier (Cashier)</option>
                            <option value="kitchen">kitchen (Chef)</option>
                            <option value="waiter">waiter (Waiter)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down" style="left:auto; right:14px; pointer-events:none;"></i>
                    </div>
                </div>

                <input type="hidden" name="numeric_pin" id="hiddenPinInput">

                <div class="pin-dots">
                    <div class="pin-dot" id="dot1"></div>
                    <div class="pin-dot" id="dot2"></div>
                    <div class="pin-dot" id="dot3"></div>
                    <div class="pin-dot" id="dot4"></div>
                    <div class="pin-dot" id="dot5"></div>
                    <div class="pin-dot" id="dot6"></div>
                </div>

                <div class="keypad">
                    <button type="button" class="key" onclick="pressKey(1)">1</button>
                    <button type="button" class="key" onclick="pressKey(2)">2</button>
                    <button type="button" class="key" onclick="pressKey(3)">3</button>
                    <button type="button" class="key" onclick="pressKey(4)">4</button>
                    <button type="button" class="key" onclick="pressKey(5)">5</button>
                    <button type="button" class="key" onclick="pressKey(6)">6</button>
                    <button type="button" class="key" onclick="pressKey(7)">7</button>
                    <button type="button" class="key" onclick="pressKey(8)">8</button>
                    <button type="button" class="key" onclick="pressKey(9)">9</button>
                    <button type="button" class="key key-action" onclick="clearPin()">C</button>
                    <button type="button" class="key" onclick="pressKey(0)">0</button>
                    <button type="button" class="key key-action" onclick="backspacePin()"><i class="fa-solid fa-delete-left"></i></button>
                    <button type="button" class="key key-action" style="grid-column: 1 / -1; background: var(--primary); color: white;" onclick="manualPinSubmit()">Submit</button>
                </div>
            </form>
        </div>

        <!-- Quick Login Demo Drawer -->
        <div class="credentials-box">
            <div style="font-weight:700; color:#fff; font-size:12px; display:flex; align-items:center; gap:6px;">
                <i class="fa-solid fa-bolt" style="color:var(--success);"></i> 1-Click Quick Demo Login:
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                <button type="button" onclick="quickFillPassword('admin', 'password')" class="demo-chip">🔑 Admin</button>
                <button type="button" onclick="quickFillPin('admin', '1234')" class="demo-chip">🔢 Admin PIN (1234)</button>
                <button type="button" onclick="quickFillPin('manager', '5678')" class="demo-chip">🔢 Manager PIN (5678)</button>
                <button type="button" onclick="quickFillPin('cashier', '9999')" class="demo-chip">🔢 Cashier PIN (9999)</button>
            </div>
        </div>
    </div>


    <!-- JavaScript Slider & Tab Control -->
    <script>
        // Left Slide Loop
        let currentSlide = 0;
        const slides = document.querySelectorAll('.feature-slide');
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 4000);

        // Tab Switching
        function switchTab(type) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.form-panel').forEach(panel => panel.classList.remove('active'));

            if (type === 'password') {
                document.getElementById('tabPassword').classList.add('active');
                document.getElementById('panelPassword').classList.add('active');
            } else {
                document.getElementById('tabPin').classList.add('active');
                document.getElementById('panelPin').classList.add('active');
                clearPin();
            }
        }

        // 1-Click Quick Demo Login Fillers
        function quickFillPassword(username, password) {
            switchTab('password');
            const userInput = document.querySelector('input[name="username"]');
            const passInput = document.querySelector('input[name="password"]');
            if (userInput) userInput.value = username;
            if (passInput) passInput.value = password;
            setTimeout(() => {
                const form = document.querySelector('#panelPassword form');
                if (form) form.submit();
            }, 150);
        }

        function quickFillPin(username, pin) {
            switchTab('pin');
            const sel = document.getElementById('pinUsername');
            if (sel) sel.value = username;
            clearPin();
            for (let char of String(pin)) {
                pressKey(parseInt(char));
            }
        }

        // Keyboard support for Quick PIN
        document.addEventListener('keydown', (e) => {
            if (document.getElementById('panelPin').classList.contains('active')) {
                if (e.key >= 0 && e.key <= 9) pressKey(e.key);
                if (e.key === 'Backspace') backspacePin();
            }
        });

        // PIN Keypad Logic
        let enteredPin = '';
        const hiddenPinInput = document.getElementById('hiddenPinInput');
        const pinSubmitForm = document.getElementById('pinSubmitForm');

        function updatePinDisplay() {
            for (let i = 1; i <= 6; i++) {
                const dot = document.getElementById('dot' + i);
                if (enteredPin.length >= i) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            }
        }

        function pressKey(num) {
            if (enteredPin.length < 6) {
                enteredPin += num;
                updatePinDisplay();
                
                if (enteredPin.length >= 6) {
                    hiddenPinInput.value = enteredPin;
                    // Auto submit form when 6 digits are completed!
                    setTimeout(() => {
                        pinSubmitForm.submit();
                    }, 200);
                }
            }
        }

        function manualPinSubmit() {
            if (enteredPin.length >= 4) {
                hiddenPinInput.value = enteredPin;
                pinSubmitForm.submit();
            }
        }

        function clearPin() {
            enteredPin = '';
            updatePinDisplay();
        }

        function backspacePin() {
            if (enteredPin.length > 0) {
                enteredPin = enteredPin.slice(0, -1);
                updatePinDisplay();
            }
        }
    </script>
</body>
</html>
