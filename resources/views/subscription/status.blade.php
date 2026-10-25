<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status === 'SUSPENDED' ? 'Account Suspended' : ($status === 'GRACE_PERIOD' ? 'Subscription Grace Period' : 'Subscription Expired') }} — DineFlow POS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg: #0B0F19;
            --card-bg: #111827;
            --card-surface: #1E293B;
            --card-border: #334155;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --text-sub: #64748B;
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
            --radius-xl: 24px;
            --radius-lg: 16px;
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
            padding: 32px 16px;
            overflow-x: hidden;
        }

        .status-container {
            width: 100%;
            max-width: {{ in_array($statusRaw, ['expired', 'cancelled', 'grace_period']) ? '920px' : '640px' }};
            margin: 0 auto;
        }

        .status-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-xl);
            padding: 40px 36px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 640px) {
            .status-card {
                padding: 28px 20px;
            }
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: {{ $status === 'SUSPENDED' ? 'linear-gradient(90deg, #EF4444, #DC2626)' : ($status === 'GRACE_PERIOD' ? 'linear-gradient(90deg, #F59E0B, #D97706)' : 'linear-gradient(90deg, #F97316, #EA580C, #6366F1)') }};
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 22px;
            font-weight: 800;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4);
        }

        .brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.5px;
        }

        .status-badge-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .status-badge.danger {
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: #F87171;
        }

        .status-badge.warning {
            background: var(--warning-bg);
            border: 1px solid var(--warning-border);
            color: #FBBF24;
        }

        .status-badge.expired {
            background: rgba(249, 115, 22, 0.15);
            border: 1px solid rgba(249, 115, 22, 0.4);
            color: #FB923C;
        }

        .status-title {
            font-size: 26px;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
            text-align: center;
        }

        .status-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.6;
            text-align: center;
        }

        .status-gujarati-sub {
            font-size: 14px;
            color: #CBD5E1;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Message Box */
        .message-box {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 24px;
            text-align: left;
        }

        .message-box-header {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .message-box-content {
            font-size: 14px;
            line-height: 1.6;
            color: #F1F5F9;
            font-weight: 500;
        }

        /* Details Grid */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 28px;
            text-align: left;
        }

        .detail-item {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
            padding: 12px 16px;
        }

        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-sub);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* SaaS Plan Cards Section */
        .plans-section-title {
            font-size: 18px;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 6px;
            text-align: center;
        }

        .plans-section-sub {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
            text-align: center;
        }

        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .plan-card {
            background: var(--card-surface);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
            text-align: left;
        }

        .plan-card:hover {
            transform: translateY(-3px);
            border-color: #6366F1;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .plan-card.popular {
            border: 2px solid #6366F1;
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.12) 0%, rgba(30, 41, 59, 0.95) 100%);
        }

        .popular-tag {
            position: absolute;
            top: -11px;
            right: 18px;
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            color: #FFFFFF;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 10px;
            border-radius: 9999px;
        }

        .plan-card-name {
            font-size: 17px;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 4px;
        }

        .plan-card-tagline {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 14px;
            min-height: 28px;
        }

        .plan-price-box {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .plan-price-num {
            font-size: 24px;
            font-weight: 800;
            color: #FFFFFF;
        }

        .plan-price-period {
            font-size: 12px;
            color: var(--text-muted);
        }

        .plan-features-list {
            list-style: none;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }

        .plan-feature-item {
            font-size: 12px;
            color: #CBD5E1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .plan-feature-item i {
            color: #10B981;
            font-size: 11px;
            flex-shrink: 0;
        }

        .btn-plan-cta {
            display: block;
            width: 100%;
            padding: 11px 16px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-plan-cta.primary {
            background: linear-gradient(135deg, #10B981, #059669);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }

        .btn-plan-cta.primary:hover {
            opacity: 0.95;
            transform: scale(1.02);
        }

        .btn-plan-cta.secondary {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #F8FAFC;
        }

        .btn-plan-cta.secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Action Buttons Grid */
        .actions-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }

        .btn-whatsapp:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .btn-secondary-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        @media (max-width: 480px) {
            .btn-secondary-actions {
                grid-template-columns: 1fr;
            }
        }

        .btn-phone {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
        }

        .btn-phone:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-email {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-main);
        }

        .btn-email:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .btn-logout {
            background: transparent;
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #F87171;
            margin-top: 6px;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .btn-dashboard-grace {
            background: linear-gradient(135deg, #6366F1, #4F46E5);
            color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .footer-note {
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-sub);
            text-align: center;
        }
    </style>
</head>
<body>

<div class="status-container">
    <div class="status-card">
        <!-- Brand Header -->
        <div class="brand-header">
            <div class="brand-logo">
                <i class="fa-solid fa-utensils"></i>
            </div>
            <div class="brand-name">DineFlow POS</div>
        </div>

        <!-- Status Badge -->
        <div class="status-badge-wrapper">
            @if($status === 'DEACTIVATED')
                <div class="status-badge danger">
                    <i class="fa-solid fa-user-slash"></i> User Account Deactivated
                </div>
            @elseif($status === 'SUSPENDED')
                <div class="status-badge danger">
                    <i class="fa-solid fa-ban"></i> Account Suspended
                </div>
            @elseif($status === 'GRACE_PERIOD')
                <div class="status-badge warning">
                    <i class="fa-solid fa-hourglass-half"></i> Grace Period Active ({{ $daysRemainingGrace }} Days Left)
                </div>
            @else
                <div class="status-badge expired">
                    <i class="fa-solid fa-clock-rotate-left"></i> Subscription Expired
                </div>
            @endif
        </div>

        <!-- Main Title & Subtitle -->
        @if($status === 'DEACTIVATED')
            <h1 class="status-title">Staff Account Deactivated</h1>
            <p class="status-subtitle">
                The account for <strong>{{ $user->name ?? 'User' }}</strong> has been temporarily deactivated by your administrator.
                <br>
                <span class="status-gujarati-sub">તમારું સ્ટાફ એકાઉન્ટ ડિએક્ટિવેટ કરવામાં આવ્યું છે. એક્સેસ પાછો મેળવવા કૃપા કરીને તમારા એડમિનનો સંપર્ક કરો.</span>
            </p>
        @elseif($status === 'SUSPENDED')
            <h1 class="status-title">Restaurant Account Suspended</h1>
            <p class="status-subtitle">
                Access to <strong>{{ $restaurant->name }}</strong> has been temporarily placed on hold by DineFlow Administration.
                <br>
                <span class="status-gujarati-sub">તમારું રેસ્ટોરન્ટ એકાઉન્ટ કામચલાઉ રીતે સ્થગિત કરવામાં આવ્યું છે. કૃપા કરીને સપોર્ટનો સંપર્ક કરો.</span>
            </p>
        @elseif($status === 'GRACE_PERIOD')
            <h1 class="status-title">Subscription In Grace Period</h1>
            <p class="status-subtitle">
                The subscription for <strong>{{ $restaurant->name }}</strong> has reached its renewal date.
                <br>
                <span class="status-gujarati-sub">તમારો સબ્સ્ક્રિપ્શન પ્લાન ગ્રેસ પિરિયડમાં છે. સેવા અવિરત રાખવા કૃપા કરીને રિન્યૂ કરો.</span>
            </p>
        @else
            <h1 class="status-title">Your Plan Has Expired</h1>
            <p class="status-subtitle">
                The subscription for <strong>{{ $restaurant->name }}</strong> has expired. Continue using DineFlow POS by renewing your plan.
                <br>
                <span class="status-gujarati-sub">તમારો DineFlow subscription plan પૂર્ણ થઈ ગયો છે. સિસ્ટમ ચાલુ રાખવા માટે કૃપા કરીને તમારો પ્લાન Renew કરો.</span>
            </p>
        @endif

        <!-- Customer-Facing Message Box -->
        <div class="message-box">
            <div class="message-box-header">
                <i class="fa-solid fa-envelope-open-text"></i> Message from DineFlow Team
            </div>
            <div class="message-box-content">
                {{ $customerMessage }}
            </div>
        </div>

        <!-- Details Grid -->
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-label">Restaurant</div>
                <div class="detail-value">{{ $restaurant->name }} (ID #{{ $restaurant->id }})</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Current Plan</div>
                <div class="detail-value">{{ strtoupper($restaurant->plan_type ?? 'STARTER') }}</div>
            </div>
            @if($status === 'SUSPENDED')
                <div class="detail-item">
                    <div class="detail-label">Suspended Date</div>
                    <div class="detail-value">{{ $suspendedAt ? \Carbon\Carbon::parse($suspendedAt)->format('d M Y') : 'Recent' }}</div>
                </div>
                @if(!empty($suspensionReason))
                    <div class="detail-item">
                        <div class="detail-label">Reason</div>
                        <div class="detail-value">{{ $suspensionReason }}</div>
                    </div>
                @endif
            @else
                <div class="detail-item">
                    <div class="detail-label">Expiry Date</div>
                    <div class="detail-value">{{ $effectiveExpiry ? $effectiveExpiry->format('d M Y') : 'Expired' }}</div>
                </div>
                @if($status === 'GRACE_PERIOD')
                    <div class="detail-item">
                        <div class="detail-label">Grace Period Ends</div>
                        <div class="detail-value" style="color: #FBBF24;">{{ $graceEndsAt ? $graceEndsAt->format('d M Y') : 'Soon' }}</div>
                    </div>
                @endif
            @endif
        </div>

        <!-- SaaS Plan Upgrade / Renewal Comparison (Shown for Expired & Grace Period) -->
        @if($status !== 'SUSPENDED' && isset($availablePlans))
            <h2 class="plans-section-title">Available Subscription Plans</h2>
            <p class="plans-section-sub">Choose a plan to instantly renew or upgrade your restaurant capabilities</p>

            <div class="plans-grid">
                @foreach($availablePlans as $pKey => $pData)
                    <div class="plan-card {{ $pData['popular'] ? 'popular' : '' }}">
                        @if($pData['popular'])
                            <div class="popular-tag">Recommended</div>
                        @endif
                        <div>
                            <div class="plan-card-name">{{ $pData['name'] }}</div>
                            <div class="plan-card-tagline">{{ $pData['tagline'] }}</div>
                            <div class="plan-price-box">
                                <span class="plan-price-num">{{ $pData['price'] }}</span>
                                <span class="plan-price-period">{{ $pData['period'] }}</span>
                            </div>
                            <ul class="plan-features-list">
                                <li class="plan-feature-item"><i class="fa-solid fa-check"></i> {{ $pData['tables'] }}</li>
                                <li class="plan-feature-item"><i class="fa-solid fa-check"></i> {{ $pData['staff'] }}</li>
                                <li class="plan-feature-item"><i class="fa-solid fa-check"></i> {{ $pData['orders'] }}</li>
                                @foreach($pData['features'] as $feature)
                                    <li class="plan-feature-item"><i class="fa-solid fa-check"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="{{ $pData['cta_url'] }}" target="_blank" class="btn-plan-cta {{ $pData['popular'] ? 'primary' : 'secondary' }}">
                            <i class="fa-brands fa-whatsapp"></i> {{ $pData['cta_text'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="actions-section">
            @if($status === 'GRACE_PERIOD')
                <a href="{{ route('dashboard') }}" class="btn-action btn-dashboard-grace">
                    <i class="fa-solid fa-arrow-right"></i> Continue to Dashboard (Grace Period Active)
                </a>
            @endif

            <a href="{{ $whatsappUrl }}" target="_blank" class="btn-action btn-whatsapp">
                <i class="fa-brands fa-whatsapp"></i> Contact Billing on WhatsApp (+91 7777938338)
            </a>

            <div class="btn-secondary-actions">
                <a href="tel:{{ $supportPhone }}" class="btn-action btn-phone">
                    <i class="fa-solid fa-phone"></i> Call Support
                </a>
                <a href="mailto:{{ $supportEmail }}?subject=Subscription%20Assistance%20for%20{{ urlencode($restaurant->name) }}" class="btn-action btn-email">
                    <i class="fa-solid fa-envelope"></i> Email Billing
                </a>
            </div>

            <form action="{{ route('auth.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-action btn-logout" style="width: 100%;">
                    <i class="fa-solid fa-right-from-bracket"></i> Sign Out of Account
                </button>
            </form>
        </div>

        <div class="footer-note">
            &copy; {{ date('Y') }} DineFlow AI Platform. All restaurant operational data is securely preserved.
        </div>
    </div>
</div>

</body>
</html>
