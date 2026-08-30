<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status === 'SUSPENDED' ? 'Account Suspended' : 'Subscription Status' }} — Tendorabuy POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg: #0B0F19;
            --card-bg: #111827;
            --card-border: #1F2937;
            --text-main: #F9FAFB;
            --text-muted: #9CA3AF;
            --text-sub: #6B7280;
            --danger: #EF4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --danger-border: rgba(239, 68, 68, 0.35);
            --warning: #F59E0B;
            --warning-bg: rgba(245, 158, 11, 0.12);
            --warning-border: rgba(245, 158, 11, 0.35);
            --success: #10B981;
            --primary: #6366F1;
            --primary-hover: #4F46E5;
            --whatsapp: #25D366;
            --radius-lg: 20px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(239, 68, 68, 0.12) 0px, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            overflow-x: hidden;
        }

        .status-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .status-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 40px 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: {{ $status === 'SUSPENDED' ? 'linear-gradient(90deg, #EF4444, #DC2626)' : ($status === 'GRACE_PERIOD' ? 'linear-gradient(90deg, #F59E0B, #D97706)' : 'linear-gradient(90deg, #F97316, #EA580C)') }};
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 20px;
            font-weight: 800;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #FFFFFF;
        }

        .brand-name span {
            color: #818CF8;
        }

        .status-icon-wrapper {
            width: 76px;
            height: 76px;
            margin: 0 auto 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            background: {{ $status === 'SUSPENDED' ? 'var(--danger-bg)' : ($status === 'GRACE_PERIOD' ? 'var(--warning-bg)' : 'rgba(249, 115, 22, 0.15)') }};
            border: 1.5px solid {{ $status === 'SUSPENDED' ? 'var(--danger-border)' : ($status === 'GRACE_PERIOD' ? 'var(--warning-border)' : 'rgba(249, 115, 22, 0.4)') }};
            color: {{ $status === 'SUSPENDED' ? 'var(--danger)' : ($status === 'GRACE_PERIOD' ? 'var(--warning)' : '#FB923C') }};
            animation: pulse-slow 3s infinite ease-in-out;
        }

        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.04); opacity: 0.9; }
        }

        .status-title {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
            background: {{ $status === 'SUSPENDED' ? 'var(--danger-bg)' : ($status === 'GRACE_PERIOD' ? 'var(--warning-bg)' : 'rgba(249, 115, 22, 0.15)') }};
            border: 1px solid {{ $status === 'SUSPENDED' ? 'var(--danger-border)' : ($status === 'GRACE_PERIOD' ? 'var(--warning-border)' : 'rgba(249, 115, 22, 0.4)') }};
            color: {{ $status === 'SUSPENDED' ? 'var(--danger)' : ($status === 'GRACE_PERIOD' ? 'var(--warning)' : '#FB923C') }};
        }

        .details-grid {
            background: rgba(17, 24, 39, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-md);
            padding: 18px 20px;
            margin-bottom: 24px;
            text-align: left;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-sub);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
            word-break: break-word;
        }

        .message-box {
            background: rgba(255, 255, 255, 0.03);
            border-left: 3px solid {{ $status === 'SUSPENDED' ? 'var(--danger)' : ($status === 'GRACE_PERIOD' ? 'var(--warning)' : '#FB923C') }};
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            padding: 16px 20px;
            text-align: left;
            margin-bottom: 28px;
            font-size: 14px;
            line-height: 1.6;
            color: #E2E8F0;
        }

        .message-box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            color: var(--text-muted);
        }

        .actions-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(37, 211, 102, 0.3);
        }

        .btn-whatsapp:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            color: #FFFFFF;
        }

        .btn-secondary {
            background: #1F2937;
            border: 1px solid #374151;
            color: #F3F4F6;
        }

        .btn-secondary:hover {
            background: #374151;
            color: #FFFFFF;
        }

        .btn-logout {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid transparent;
            font-weight: 600;
            padding: 10px;
        }

        .btn-logout:hover {
            color: #EF4444;
            background: rgba(239, 68, 68, 0.08);
        }

        .support-channels {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 13px;
            color: var(--text-muted);
        }

        .support-link {
            color: #818CF8;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .support-link:hover {
            text-decoration: underline;
            color: #A5B4FC;
        }

        @media (max-width: 480px) {
            .status-card {
                padding: 28px 20px;
            }
            .details-grid {
                grid-template-columns: 1fr;
                gap: 12px;
                padding: 14px 16px;
            }
            .status-title {
                font-size: 20px;
            }
            .support-channels {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="status-container">
        <div class="status-card">
            
            <!-- Platform Brand -->
            <div class="brand-header">
                <div class="brand-logo">T</div>
                <div class="brand-name">TENDORA<span>BUY</span></div>
            </div>

            <!-- Status Icon -->
            <div class="status-icon-wrapper">
                @if($status === 'SUSPENDED')
                    <i class="fa-solid fa-ban"></i>
                @elseif($status === 'GRACE_PERIOD')
                    <i class="fa-solid fa-triangle-exclamation"></i>
                @else
                    <i class="fa-solid fa-hourglass-end"></i>
                @endif
            </div>

            <!-- Title -->
            <h1 class="status-title">
                @if($status === 'SUSPENDED')
                    Restaurant Account Suspended
                @elseif($status === 'GRACE_PERIOD')
                    Subscription in Grace Period
                @else
                    Subscription Expired
                @endif
            </h1>

            <!-- Status Badge -->
            <div class="status-badge">
                <i class="fa-solid fa-circle" style="font-size: 8px;"></i>
                Status: {{ $status }}
            </div>

            <!-- Restaurant Details -->
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Restaurant</span>
                    <span class="detail-value">{{ $restaurant->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Current Plan</span>
                    <span class="detail-value" style="text-transform: capitalize;">{{ $restaurant->plan_type ?? 'Free' }} Plan</span>
                </div>
                @if($status === 'SUSPENDED')
                    <div class="detail-item">
                        <span class="detail-label">Suspended On</span>
                        <span class="detail-value">{{ $suspendedAt ? \Carbon\Carbon::parse($suspendedAt)->format('d M Y, h:i A') : 'Recently' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Primary Reason</span>
                        <span class="detail-value" style="color: var(--danger);">{{ $suspensionReason ?: 'Administrative Review' }}</span>
                    </div>
                @else
                    <div class="detail-item">
                        <span class="detail-label">Expired On</span>
                        <span class="detail-value">{{ $effectiveExpiry ? \Carbon\Carbon::parse($effectiveExpiry)->format('d M Y') : 'Expired' }}</span>
                    </div>
                    @if($status === 'GRACE_PERIOD' && $graceEndsAt)
                        <div class="detail-item">
                            <span class="detail-label">Grace Period Ends</span>
                            <span class="detail-value" style="color: var(--warning);">{{ \Carbon\Carbon::parse($graceEndsAt)->format('d M Y') }} ({{ $daysRemainingGrace }} days left)</span>
                        </div>
                    @else
                        <div class="detail-item">
                            <span class="detail-label">Access State</span>
                            <span class="detail-value" style="color: #FB923C;">Operations Locked</span>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Customer Facing Message (Super Admin Custom or Safe Default) -->
            <div class="message-box">
                <div class="message-box-title">Important Notice</div>
                <div>{{ $customerMessage }}</div>
            </div>

            <!-- Actions Group -->
            <div class="actions-group">
                <!-- WhatsApp Support Direct Link -->
                <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-whatsapp">
                    <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                    <span>Contact Support on WhatsApp</span>
                </a>

                <!-- Phone Support -->
                <a href="tel:{{ $supportPhone }}" class="btn btn-secondary">
                    <i class="fa-solid fa-phone"></i>
                    <span>Call Support ({{ $supportPhone }})</span>
                </a>

                <!-- Logout -->
                <form action="{{ route('auth.logout') }}" method="POST" style="margin: 0; width: 100%;">
                    @csrf
                    <button type="submit" class="btn btn-logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Sign Out ({{ $user->email }})</span>
                    </button>
                </form>
            </div>

            <!-- Footer Support Channels -->
            <div class="support-channels">
                <span>Need assistance?</span>
                <a href="mailto:{{ $supportEmail }}" class="support-link">
                    <i class="fa-solid fa-envelope"></i>
                    <span>{{ $supportEmail }}</span>
                </a>
            </div>

        </div>
    </div>

</body>
</html>
