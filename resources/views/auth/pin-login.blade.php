<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineFlow AI POS - Quick Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #EEF2FF;
            --primary-dark: #3730A3;
            --success: #10B981;
            --danger: #EF4444;
            --danger-light: #FEF2F2;
            --bg: #F8FAFC;
            --card: #FFFFFF;
            --border: #E2E8F0;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --shadow-md: 0 4px 6px rgba(0,0,0,.05), 0 2px 4px rgba(0,0,0,.04);
            --shadow-lg: 0 10px 15px rgba(0,0,0,.07), 0 4px 6px rgba(0,0,0,.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { width: 100%; height: 100%; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, #7C3AED 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 24px;
        }

        .login-card {
            background: var(--card);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            padding: 48px 32px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            background: var(--success);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
            color: white;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .login-header p {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .user-info {
            background: var(--primary-light);
            border: 2px solid var(--primary);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            margin-bottom: 24px;
            font-size: 15px;
            font-weight: 600;
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .pin-input-group {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 24px;
        }

        .pin-digit {
            width: 50px;
            height: 60px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            background: var(--card);
        }

        .pin-digit:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .pin-input-container {
            position: relative;
            margin-bottom: 24px;
        }

        .pin-input-hidden {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .error-message {
            background: var(--danger-light);
            color: var(--danger);
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .error-message i {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: var(--success);
            color: white;
            margin-bottom: 12px;
        }

        .btn-primary:hover {
            background: #059669;
        }

        .btn-primary:disabled {
            background: #D1D5DB;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #F1F5F9;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .button-group .btn {
            margin: 0;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 20px;
            }

            .login-header h1 {
                font-size: 20px;
            }

            .pin-digit {
                width: 45px;
                height: 55px;
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="brand-logo">
                    <i class="fas fa-fingerprint"></i>
                </div>
                <h1>Quick Login</h1>
                <p>Enter your 4-6 digit PIN</p>
            </div>

            <div class="user-info">
                <i class="fas fa-user-circle"></i> {{ $username }}
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="/pin-login" id="pinForm">
                @csrf

                <input type="hidden" name="username" value="{{ $username }}">

                <div class="form-group">
                    <div class="pin-input-container">
                        <div class="pin-input-group" id="pinDisplay"></div>
                        <input 
                            type="text" 
                            id="numeric_pin" 
                            name="numeric_pin" 
                            class="pin-input-hidden"
                            inputmode="numeric"
                            autofocus
                            maxlength="6"
                        >
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-check"></i> Login
                    </button>
                    <a href="{{ route('login') }}" class="btn btn-secondary" style="display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const pinInput = document.getElementById('numeric_pin');
        const pinDisplay = document.getElementById('pinDisplay');
        const submitBtn = document.getElementById('submitBtn');

        // Create 6 pin digit boxes
        for (let i = 0; i < 6; i++) {
            const digit = document.createElement('input');
            digit.type = 'text';
            digit.className = 'pin-digit';
            digit.maxLength = '1';
            digit.inputMode = 'numeric';
            digit.disabled = true;
            pinDisplay.appendChild(digit);
        }

        const pinDigits = document.querySelectorAll('.pin-digit');

        pinInput.addEventListener('input', (e) => {
            const value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            pinInput.value = value;

            // Update display
            pinDigits.forEach((digit, index) => {
                digit.value = value[index] || '';
                digit.style.borderColor = value[index] ? '#4F46E5' : '#E2E8F0';
            });

            // Enable submit if 4-6 digits
            if (value.length >= 4) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }

            // Auto-submit on 6 digits
            if (value.length === 6) {
                setTimeout(() => {
                    document.getElementById('pinForm').submit();
                }, 300);
            }
        });

        // Focus on input field
        pinInput.focus();
    </script>
</body>
</html>
