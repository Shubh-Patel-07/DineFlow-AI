<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RestoPOS - Login</title>
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
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 32px;
            color: white;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 14px;
            color: var(--text-secondary);
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

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-group input::placeholder {
            color: var(--text-secondary);
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

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .form-footer label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            cursor: pointer;
            color: var(--text-secondary);
        }

        .form-footer input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
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
            background: var(--primary);
            color: white;
            margin-bottom: 12px;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: #F1F5F9;
            border-color: #CBD5E1;
        }

        .info-box {
            background: var(--primary-light);
            color: var(--primary);
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            margin-top: 20px;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 32px 20px;
            }

            .login-header h1 {
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
                    <i class="fas fa-utensils"></i>
                </div>
                <h1>RestoPOS</h1>
                <p>Restaurant Point of Sale</p>
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

            <form method="POST" action="{{ route('auth.login') }}">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username"
                        value="{{ old('username') }}"
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <div class="form-footer">
                    <label>
                        <input type="checkbox" name="remember" value="on">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>

                @if (session('last_username'))
                    <a href="{{ route('auth.pin-login') }}" class="btn btn-secondary">
                        <i class="fas fa-fingerprint"></i> Quick PIN Login
                    </a>
                @endif
            </form>

            <div class="info-box">
                <i class="fas fa-info-circle"></i> Demo: username: <strong>admin</strong> | password: <strong>password</strong>
            </div>
        </div>
    </div>
</body>
</html>
