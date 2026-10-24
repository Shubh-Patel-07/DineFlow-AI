<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Locked - Unauthorized Deployment | DineFlow AI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #080C15;
            color: #F1F5F9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow-x: hidden;
            position: relative;
        }
        /* Background Glow */
        .ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 0;
        }
        .lock-container {
            position: relative;
            z-index: 1;
            background: #0F172A;
            border: 1px solid rgba(239, 68, 68, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 30px rgba(239, 68, 68, 0.12);
            border-radius: 24px;
            max-width: 620px;
            width: 100%;
            padding: 44px 36px;
            text-align: center;
        }
        .shield-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            color: #EF4444;
            font-size: 38px;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.2);
        }
        h1 {
            font-size: 26px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 14.5px;
            color: #94A3B8;
            line-height: 1.6;
            margin-bottom: 28px;
        }
        .diag-box {
            background: #131D33;
            border: 1px solid #1E293B;
            border-radius: 14px;
            padding: 16px 20px;
            text-align: left;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .diag-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }
        .diag-row:last-child { border-bottom: none; }
        .diag-label { color: #64748B; font-weight: 600; }
        .diag-val {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: #F87171;
        }
        .diag-val.clean { color: #CBD5E1; }
        .legal-notice {
            background: rgba(239, 68, 68, 0.05);
            border-left: 3px solid #EF4444;
            padding: 12px 16px;
            border-radius: 0 10px 10px 0;
            text-align: left;
            font-size: 12px;
            color: #FDA4AF;
            line-height: 1.5;
            margin-bottom: 28px;
        }
        .contact-card {
            background: linear-gradient(135deg, #1E1B4B 0%, #172554 100%);
            border: 1px solid #3730A3;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }
        .contact-card h3 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #A5B4FC;
            margin-bottom: 6px;
        }
        .author-name {
            font-size: 18px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 12px;
        }
        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #10B981;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transition: all 0.2s ease;
        }
        .btn-whatsapp:hover {
            background: #059669;
            transform: translateY(-1px);
        }
        .direct-phone {
            margin-top: 10px;
            font-size: 13px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>
    <div class="lock-container">
        <div class="shield-icon">🔒</div>
        <h1>License Lockdown Engaged</h1>
        <p class="subtitle">
            This deployment of <strong>DineFlow AI</strong> is not authorized to execute on this host. 
            Cryptographic domain verification failed. Reselling or distributing this source code is strictly prohibited.
        </p>

        <div class="diag-box">
            <div class="diag-row">
                <span class="diag-label">Current Server Host:</span>
                <span class="diag-val">{{ request()->getHost() }}</span>
            </div>
            <div class="diag-row">
                <span class="diag-label">Client IP Address:</span>
                <span class="diag-val clean">{{ request()->ip() }}</span>
            </div>
            <div class="diag-row">
                <span class="diag-label">Lock Reason:</span>
                <span class="diag-val">{{ $licenseResult['reason'] ?? 'Unauthorized Host Binding' }}</span>
            </div>
            <div class="diag-row">
                <span class="diag-label">Intellectual Property:</span>
                <span class="diag-val clean">SMP Technologies / Shubh Patel</span>
            </div>
        </div>

        <div class="legal-notice">
            ⚖️ <strong>Legal Notice:</strong> DineFlow AI is proprietary software. Reverse engineering, unauthorized cloning, or sublicensing violates commercial software agreements and international copyright law.
        </div>

        <div class="contact-card">
            <h3>Rights Holder & Official Support</h3>
            <div class="author-name">Shubh Patel (Project Architect)</div>
            <a href="https://wa.me/917777938338?text=Hello%20Shubh,%20my%20DineFlow%20license%20shows%20locked%20for%20domain:%20{{ urlencode(request()->getHost()) }}" 
               class="btn-whatsapp" target="_blank" rel="noopener noreferrer">
                <span>💬 Contact Rights Owner on WhatsApp</span>
            </a>
            <div class="direct-phone">WhatsApp / Direct: +91 77779 38338 &nbsp;|&nbsp; shubhmpatel77@gmail.com</div>
        </div>
    </div>
</body>
</html>
