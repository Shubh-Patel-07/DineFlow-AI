<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Started with DineFlow AI — Free Restaurant Signup</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary: #6366F1;
            --primary-glow: rgba(99, 102, 241, 0.3);
            --primary-dark: #4338CA;
            --success: #10B981;
            --danger: #EF4444;
            --bg: #090D1A;
            --card: #131A35;
            --card-glass: rgba(19, 26, 53, 0.7);
            --border: rgba(255, 255, 255, 0.08);
            --border-glow: rgba(99, 102, 241, 0.25);
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
            overflow-x: hidden;
        }

        /* ─── SPLIT HERO LEFT ─── */
        .split-left {
            flex: 1.1;
            background: linear-gradient(135deg, #090D1A 0%, #1e1b4b 50%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            position: relative;
        }

        .split-left::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -20%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(0,0,0,0) 70%);
            z-index: 0;
            pointer-events: none;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 1;
            text-decoration: none;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #6366F1, #818CF8);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 0 20px var(--primary-glow);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 40%, #818CF8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content {
            margin: 40px 0;
            z-index: 1;
        }

        .badge-pill {
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
            margin-bottom: 24px;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: 40px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }

        .hero-subtitle {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
            max-width: 480px;
            margin-bottom: 32px;
        }

        .feature-bullets {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .feature-check {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34D399;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .feature-text {
            font-size: 14px;
            font-weight: 500;
            color: #E2E8F0;
        }

        .hero-footer {
            z-index: 1;
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* ─── FORM RIGHT ─── */
        .split-right {
            flex: 1;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            position: relative;
            overflow-y: auto;
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
        }

        .auth-card {
            background: var(--card-glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .form-sub {
            font-size: 13.5px;
            color: var(--text-secondary);
        }

        .alert-box {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #FCA5A5;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
            background: rgba(15, 23, 42, 0.9);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12.5px;
            color: var(--text-secondary);
            cursor: pointer;
            margin: 18px 0;
            line-height: 1.4;
        }

        .checkbox-label input {
            margin-top: 2px;
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            box-shadow: 0 8px 24px var(--primary-glow);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(99, 102, 241, 0.45);
        }

        .signin-link {
            text-align: center;
            margin-top: 20px;
            font-size: 13.5px;
            color: var(--text-secondary);
        }

        .signin-link a {
            color: #818CF8;
            font-weight: 600;
            text-decoration: none;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 960px) {
            body {
                flex-direction: column;
                height: auto !important;
                min-height: 100vh !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                touch-action: pan-y !important;
            }
            .split-left { padding: 24px 20px; flex: none; }
            .split-left .hero-content,
            .split-left .hero-footer { display: none; }
            .split-right { padding: 20px 16px 40px; overflow-y: visible !important; }
            .form-row { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .split-left { padding: 18px 14px; }
            .split-right { padding: 14px 12px 32px; }
            .auth-card { padding: 24px 16px; }
        }
    </style>
</head>
<body>

    <!-- LEFT PROMO PANEL -->
    <div class="split-left">
        <a href="/" class="brand-logo">
            <div class="brand-icon"><i class="fa-solid fa-fire-burner"></i></div>
            <div class="brand-name">DineFlow AI</div>
        </a>

        <div class="hero-content">
            <div class="badge-pill"><i class="fa-solid fa-bolt"></i> 100% Free Starter Plan • Instant Setup</div>
            <h1 class="hero-title">Launch Your Smart Restaurant in Under 2 Minutes.</h1>
            <p class="hero-subtitle">
                Join modern restaurants automating table orders, digital QR menus, live kitchen display (KDS), and seamless UPI billing.
            </p>

            <div class="feature-bullets">
                <div class="feature-item">
                    <div class="feature-check"><i class="fa-solid fa-check"></i></div>
                    <span class="feature-text">Digital Table QR Ordering & Waiter Calling</span>
                </div>
                <div class="feature-item">
                    <div class="feature-check"><i class="fa-solid fa-check"></i></div>
                    <span class="feature-text">Real-Time Kitchen Display System (KDS) & KOT</span>
                </div>
                <div class="feature-item">
                    <div class="feature-check"><i class="fa-solid fa-check"></i></div>
                    <span class="feature-text">Thermal Receipts, WhatsApp & A4 Invoices</span>
                </div>
                <div class="feature-item">
                    <div class="feature-check"><i class="fa-solid fa-check"></i></div>
                    <span class="feature-text">Staff PIN Logins & Live Inventory Tracking</span>
                </div>
            </div>
        </div>

        <div class="hero-footer">
            &copy; 2026 DineFlow AI Inc. All rights reserved. Zero credit card required.
        </div>
    </div>

    <!-- RIGHT REGISTER FORM -->
    <div class="split-right">
        <div class="auth-container">
            <div class="auth-card">
                <div class="form-header">
                    <h2 class="form-title">Create Restaurant Account</h2>
                    <p class="form-sub">Start managing tables and live orders today</p>
                </div>

                @if (isset($errors) && $errors->any())
                    <div class="alert-box alert-danger">
                        <i class="fa-solid fa-circle-exclamation" style="margin-top:2px;"></i>
                        <div>
                            <strong>Registration error:</strong>
                            <ul style="margin-left: 16px; margin-top: 4px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="restaurant_name">Restaurant / Café Name</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-store input-icon"></i>
                            <input type="text" id="restaurant_name" name="restaurant_name" class="form-input" placeholder="e.g. Royal Heritage Bistro" value="{{ old('restaurant_name') }}" required autofocus>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">Owner Full Name</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-user input-icon"></i>
                                <input type="text" id="name" name="name" class="form-input" placeholder="e.g. Rajesh Patel" value="{{ old('name') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone">Mobile Number</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-phone input-icon"></i>
                                <input type="tel" id="phone" name="phone" class="form-input" placeholder="9876543210" value="{{ old('phone') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Business Email</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="form-input" placeholder="owner@restaurant.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" class="form-input" placeholder="Min. 6 chars" oninput="checkPasswordStrength(this.value)" required>
                                <i class="fa-solid fa-eye" id="togglePassword" onclick="togglePasswordVisibility('password', this)" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#64748B; cursor:pointer; font-size:14px;"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-shield-halved input-icon"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Re-type password" oninput="checkPasswordMatch()" required>
                                <i class="fa-solid fa-eye" id="togglePasswordConf" onclick="togglePasswordVisibility('password_confirmation', this)" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#64748B; cursor:pointer; font-size:14px;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Password Strength Meter -->
                    <div id="strengthWrapper" style="margin-top:-6px; margin-bottom:14px; display:none;">
                        <div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:4px;">
                            <span style="color:var(--text-secondary);">Password Strength:</span>
                            <span id="strengthLabel" style="font-weight:700; color:#EF4444;">Weak</span>
                        </div>
                        <div style="height:4px; background:rgba(255,255,255,0.1); border-radius:4px; overflow:hidden;">
                            <div id="strengthBar" style="height:100%; width:25%; background:#EF4444; transition:all 0.3s ease;"></div>
                        </div>
                    </div>

                    <div id="matchNotice" style="font-size:11.5px; margin-top:-6px; margin-bottom:12px; display:none;"></div>

                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                        <span>I agree to the <a href="{{ route('terms') }}" target="_blank" style="color:#818CF8;">Terms of Service</a> & <a href="{{ route('privacy') }}" target="_blank" style="color:#818CF8;">Privacy Policy</a>.</span>
                    </label>

                    <button type="submit" id="btnSubmitReg" class="btn-submit">
                        <span id="btnSubmitText">Create Restaurant Account</span>
                        <i class="fa-solid fa-arrow-right" id="btnSubmitIcon"></i>
                    </button>
                </form>

                <div class="signin-link">
                    Already have a restaurant account? <a href="{{ route('login') }}">Sign In</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconElem) {
            const input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                iconElem.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                iconElem.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        function checkPasswordStrength(val) {
            const wrap = document.getElementById('strengthWrapper');
            const label = document.getElementById('strengthLabel');
            const bar = document.getElementById('strengthBar');
            if (!val) {
                wrap.style.display = 'none';
                return;
            }
            wrap.style.display = 'block';

            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 10) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            if (score <= 1) {
                label.textContent = 'Weak (min. 6 chars)';
                label.style.color = '#EF4444';
                bar.style.width = '25%';
                bar.style.background = '#EF4444';
            } else if (score === 2) {
                label.textContent = 'Fair';
                label.style.color = '#F59E0B';
                bar.style.width = '50%';
                bar.style.background = '#F59E0B';
            } else if (score === 3) {
                label.textContent = 'Good';
                label.style.color = '#10B981';
                bar.style.width = '75%';
                bar.style.background = '#10B981';
            } else {
                label.textContent = 'Strong 🔒';
                label.style.color = '#34D399';
                bar.style.width = '100%';
                bar.style.background = '#34D399';
            }
        }

        function checkPasswordMatch() {
            const pw = document.getElementById('password').value;
            const pwConf = document.getElementById('password_confirmation').value;
            const notice = document.getElementById('matchNotice');
            if (!pwConf) {
                notice.style.display = 'none';
                return;
            }
            notice.style.display = 'block';
            if (pw === pwConf) {
                notice.textContent = '✓ Passwords match';
                notice.style.color = '#34D399';
            } else {
                notice.textContent = '✗ Passwords do not match';
                notice.style.color = '#EF4444';
            }
        }

        document.querySelector('form')?.addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmitReg');
            const txt = document.getElementById('btnSubmitText');
            const icon = document.getElementById('btnSubmitIcon');
            if (btn && txt) {
                btn.disabled = true;
                btn.style.opacity = '0.85';
                txt.textContent = 'Provisioning Restaurant Tenant...';
                if (icon) icon.className = 'fa-solid fa-spinner fa-spin';
            }
        });
    </script>
</body>
</html>
