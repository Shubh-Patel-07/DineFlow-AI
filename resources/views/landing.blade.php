<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>DineFlow — Modern Restaurant POS, QR Ordering & Kitchen Management</title>
    <meta name="description" content="DineFlow is the all-in-one restaurant platform. Table QR digital ordering, fast POS billing, real-time Kitchen Display (KDS), inventory control, and offline desktop sync.">
    
    <!-- Open Graph / SEO Metadata -->
    <meta property="og:title" content="DineFlow — Modern Restaurant POS, QR Ordering & Kitchen Management">
    <meta property="og:description" content="Complete restaurant operating system. Streamline QR table ordering, fast POS billing, real-time KDS, offline desktop sync, and staff management.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="robots" content="index, follow">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #9F1239;
            --primary-glow: rgba(159, 18, 57, 0.4);
            --primary-dark: #881337;
            --primary-rose: #FB7185;
            --success: #22C55E;
            --success-glow: rgba(34, 197, 94, 0.3);
            --whatsapp: #25D366;
            --whatsapp-dark: #128C7E;
            --accent: #FB7185;
            --bg: #0B0B0D;
            --bg-alt: #101012;
            --card: #151517;
            --card-glass: rgba(21, 21, 23, 0.85);
            --border: #26262A;
            --border-glow: rgba(159, 18, 57, 0.35);
            --text-primary: #F4F4F5;
            --text-secondary: #A1A1AA;
            --text-muted: #71717A;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-primary);
            overflow-x: hidden;
            width: 100%;
            line-height: 1.6;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-y;
        }

        /* ─── CONTAINER & SHARED ─── */
        .container {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .navbar .container {
            max-width: 1400px;
            padding: 0 24px;
        }

        /* ─── DYNAMIC FLOATING AMBIENT GLOW ORBS ─── */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.32;
            pointer-events: none;
            z-index: 0;
            will-change: transform;
        }
        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #9F1239, transparent 70%);
            top: -100px;
            left: -100px;
            animation: orbFloat1 18s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, #EC4899, transparent 70%);
            top: 25%;
            right: -150px;
            animation: orbFloat2 22s ease-in-out infinite alternate;
        }
        .orb-3 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, #06B6D4, transparent 70%);
            bottom: 10%;
            left: 10%;
            animation: orbFloat3 20s ease-in-out infinite alternate;
        }

        @keyframes orbFloat1 {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(120px, 160px) scale(1.15); }
            100% { transform: translate(60px, 80px) scale(0.95); }
        }
        @keyframes orbFloat2 {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-140px, 120px) scale(1.1); }
            100% { transform: translate(-80px, -60px) scale(0.9); }
        }
        @keyframes orbFloat3 {
            0% { transform: translate(0, 0) scale(0.9); }
            50% { transform: translate(150px, -100px) scale(1.2); }
            100% { transform: translate(40px, -50px) scale(1); }
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(159, 18, 57, 0.15);
            border: 1px solid rgba(251, 113, 133, 0.3);
            border-radius: 30px;
            color: #FDA4AF;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(26px, 3.5vw, 36px);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 14px;
            line-height: 1.25;
            color: #fff;
        }

        .section-subtitle {
            font-size: clamp(14.5px, 1.6vw, 16px);
            color: var(--text-secondary);
            max-width: 620px;
            margin: 0 auto 44px;
            text-align: center;
            line-height: 1.55;
        }

        /* ─── NAVBAR ─── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 72px;
            background: rgba(9, 13, 26, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            gap: 16px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #9F1239, #BE123C);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #fff;
            box-shadow: 0 0 12px var(--primary-glow);
            border: 1px solid rgba(251, 113, 133, 0.3);
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-name .brand-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 6px;
            background: rgba(159, 18, 57, 0.2);
            color: #FB7185;
            border: 1px solid rgba(251, 113, 133, 0.3);
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 24px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-header-demo {
            padding: 7px 14px;
            background: rgba(159, 18, 57, 0.15);
            border: 1px solid rgba(251, 113, 133, 0.35);
            color: #FDA4AF;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-header-demo:hover {
            background: rgba(159, 18, 57, 0.25);
            border-color: #9F1239;
            color: #fff;
        }

        .btn-wa-header {
            padding: 7px 12px;
            background: rgba(37, 211, 102, 0.12);
            border: 1px solid rgba(37, 211, 102, 0.3);
            color: #4ADE80;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-wa-header:hover {
            background: rgba(37, 211, 102, 0.2);
            border-color: #25D366;
            color: #fff;
        }

        .btn-ghost {
            padding: 7px 14px;
            color: #CBD5E1;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            background: rgba(255, 255, 255, 0.03);
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .btn-primary {
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            border-radius: 8px;
            box-shadow: 0 4px 14px var(--primary-glow);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45);
        }

        .mobile-menu-btn {
            display: none;
            width: 36px;
            height: 36px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
        }

        /* ─── MOBILE DRAWER ─── */
        .mobile-drawer-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(9, 13, 26, 0.7);
            backdrop-filter: blur(4px);
            z-index: 998;
            pointer-events: none;
        }

        .mobile-drawer-backdrop.active {
            display: block;
            pointer-events: auto;
        }

        .mobile-drawer {
            display: none;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            background: #0D1326;
            border-bottom: 1px solid var(--border);
            padding: 20px;
            z-index: 999;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.4);
            max-height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .mobile-drawer.active {
            display: block;
        }

        .mobile-drawer ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mobile-drawer ul li a {
            color: #CBD5E1;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.15s;
        }

        .mobile-drawer ul li a:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
        }

        /* ─── 1. HERO SECTION ─── */
        .hero-section {
            padding: 120px 0 70px;
            background: radial-gradient(circle at 50% 25%, rgba(99, 102, 241, 0.14) 0%, rgba(9, 13, 26, 0) 70%);
            position: relative;
        }

        .hero-content {
            text-align: center;
            max-width: 880px;
            margin: 0 auto;
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(28px, 4.2vw, 44px);
            font-weight: 900;
            line-height: 1.2;
            letter-spacing: -0.6px;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #FFFFFF 0%, #A5B4FC 25%, #EC4899 50%, #818CF8 75%, #FFFFFF 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: textShine 8s linear infinite;
        }

        @keyframes textShine {
            to { background-position: 200% center; }
        }

        .btn-source-glow {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #EC4899, #8B5CF6) !important;
            box-shadow: 0 4px 22px rgba(236, 72, 153, 0.45) !important;
            animation: pulseSource 3s infinite alternate;
        }
        .btn-source-glow::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(60deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transform: rotate(30deg);
            animation: sweepShine 3.5s infinite linear;
        }
        @keyframes sweepShine {
            0% { transform: translateX(-150%) rotate(30deg); }
            100% { transform: translateX(150%) rotate(30deg); }
        }
        @keyframes pulseSource {
            0% { box-shadow: 0 4px 20px rgba(236, 72, 153, 0.4); transform: scale(1); }
            100% { box-shadow: 0 6px 30px rgba(236, 72, 153, 0.7); transform: scale(1.02); }
        }

        /* ─── FLOATING LIVE BADGES ─── */
        .floating-badge {
            position: absolute;
            background: rgba(18, 24, 50, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 9px 18px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45), 0 0 18px rgba(99, 102, 241, 0.25);
            z-index: 5;
            pointer-events: none;
            will-change: transform;
        }
        .badge-top-left {
            top: -22px;
            left: -20px;
            animation: floatLevitate1 4s ease-in-out infinite alternate;
        }
        .badge-top-right {
            top: -18px;
            right: -20px;
            animation: floatLevitate2 4.5s ease-in-out infinite alternate;
        }
        .badge-bottom-left {
            bottom: -20px;
            left: 24px;
            animation: floatLevitate2 5s ease-in-out infinite alternate;
        }
        @keyframes floatLevitate1 {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-12px) rotate(-1.5deg); }
        }
        @keyframes floatLevitate2 {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-10px) rotate(1.5deg); }
        }
        .pulse-dot-green {
            width: 8px;
            height: 8px;
            background: #10B981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #10B981;
            animation: dotBlink 1.5s infinite;
        }
        @keyframes dotBlink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .hero-desc {
            font-size: clamp(15px, 1.7vw, 17px);
            color: var(--text-secondary);
            margin-bottom: 32px;
            max-width: 660px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .hero-ctas {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .hero-ctas .btn-primary {
            padding: 12px 28px;
            font-size: 15px;
            border-radius: 10px;
        }

        .hero-ctas .btn-ghost {
            padding: 12px 24px;
            font-size: 15px;
            border-radius: 10px;
        }

        .hero-pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 18px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 30px;
            color: #34D399;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 40px;
        }

        /* ─── SHOWCASE MOCKUP ─── */
        .hero-mockup-wrapper {
            position: relative;
            margin-top: 10px;
        }

        .hero-mockup {
            background: var(--card-glass);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 22px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), 0 0 30px rgba(99, 102, 241, 0.12);
            backdrop-filter: blur(12px);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .mock-card {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 22px;
            text-align: left;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }

        .mock-card:hover {
            transform: translateY(-6px);
            border-color: rgba(99, 102, 241, 0.45);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4), 0 0 24px rgba(99, 102, 241, 0.2);
        }

        .mock-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .mock-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .mock-sub {
            font-size: 12.5px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ─── TRUST STATS ─── */
        .trust-section {
            padding: 36px 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: var(--bg-alt);
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            text-align: center;
        }

        .trust-item h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 2px;
        }

        .trust-item p {
            font-size: 12.5px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ─── FEATURES ─── */
        .features-section {
            padding: 80px 0;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: left;
        }

        .feature-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            transition: all 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            border-color: var(--border-glow);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.35);
        }

        .feat-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(99, 102, 241, 0.12);
            color: #818CF8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            margin-bottom: 16px;
        }

        .feature-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .feature-card p {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.55;
        }

        /* ─── HOW IT WORKS ─── */
        .steps-section {
            padding: 80px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: left;
        }

        .step-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            position: relative;
        }

        .step-num {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 900;
            color: rgba(99, 102, 241, 0.35);
            margin-bottom: 10px;
        }

        .step-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .step-card p {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ─── ONLINE + OFFLINE WORKFLOW ─── */
        .sync-section {
            padding: 80px 0;
            text-align: center;
        }

        .sync-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            text-align: left;
        }

        .sync-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 30px;
        }

        .sync-card.cloud { border-left: 4px solid #FB7185; }
        .sync-card.offline { border-left: 4px solid #22C55E; }

        .sync-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .sync-card.cloud .sync-badge { background: rgba(159, 18, 57, 0.15); color: #FB7185; }
        .sync-card.offline .sync-badge { background: rgba(34, 197, 94, 0.15); color: #34D399; }

        .sync-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 10px;
        }

        .sync-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.55;
            margin-bottom: 16px;
        }

        .sync-flow {
            padding: 14px 16px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            font-family: monospace;
            font-size: 13px;
            color: #A5B4FC;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        /* ─── BENEFITS ─── */
        .benefits-section {
            padding: 80px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            text-align: left;
        }

        .benefit-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
        }

        .benefit-card h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 15.5px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .benefit-card p {
            font-size: 12.5px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* ─── PRICING PREVIEW ─── */
        .pricing-section {
            padding: 80px 0;
            text-align: center;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            text-align: left;
        }

        .price-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .price-card.featured {
            border-color: #FB7185;
            box-shadow: 0 0 24px rgba(159, 18, 57, 0.25);
            background: linear-gradient(180deg, #1E0A12 0%, #151517 100%);
        }

        .featured-tag {
            position: absolute;
            top: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: #9F1239;
            color: #fff;
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 3px 12px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .plan-name {
            font-family: 'Outfit', sans-serif;
            font-size: 19px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 6px;
        }

        .plan-price {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 14px;
        }

        .plan-price span {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 24px;
            flex: 1;
        }

        .plan-features li {
            font-size: 13.5px;
            color: var(--text-secondary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-features li i {
            color: #10B981;
            font-size: 13px;
        }

        /* ─── FAQ ─── */
        .faq-section {
            padding: 80px 0;
            background: var(--bg-alt);
            text-align: center;
        }

        .faq-list {
            max-width: 760px;
            margin: 0 auto;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .faq-item {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
        }

        .faq-item h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
        }

        .faq-item p {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.55;
        }

        /* ─── CTA BANNER ─── */
        .cta-banner {
            padding: 70px 0;
            text-align: center;
            background: radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.16) 0%, rgba(9, 13, 26, 0) 70%);
        }

        .cta-box {
            background: linear-gradient(135deg, #18224B, #131A35);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 48px 30px;
            max-width: 860px;
            margin: 0 auto;
        }

        .cta-box h2 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(24px, 3.2vw, 34px);
            font-weight: 900;
            color: #fff;
            margin-bottom: 12px;
        }

        .cta-box p {
            font-size: 15px;
            color: var(--text-secondary);
            margin-bottom: 28px;
            max-width: 560px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ─── FOOTER ─── */
        .footer {
            padding: 50px 0 24px;
            background: #060913;
            border-top: 1px solid var(--border);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 32px;
            margin-bottom: 40px;
        }

        .footer-brand p {
            font-size: 13.5px;
            color: var(--text-muted);
            margin-top: 10px;
            max-width: 300px;
            line-height: 1.5;
        }

        .footer-col h5 {
            font-family: 'Outfit', sans-serif;
            font-size: 14.5px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 14px;
        }

        .footer-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-col a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s ease;
        }

        .footer-col a:hover {
            color: #fff;
        }

        .footer-bottom {
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12.5px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ─── RESPONSIVE BREAKPOINTS (320px - 1280px) ─── */
        @media (max-width: 1280px) {
            .wa-text { display: none; }
            .nav-links { gap: 16px; }
            .nav-links a { font-size: 13.5px; }
        }

        @media (max-width: 1100px) {
            .nav-links { gap: 12px; }
            .nav-links a { font-size: 13px; }
            .btn-header-demo span { font-size: 12px; }
            .wa-text { display: none; }
        }

        @media (max-width: 960px) {
            .nav-links { display: none; }
            .desktop-auth-btns { display: none !important; }
            .mobile-menu-btn { display: flex; }
            .btn-header-demo { display: none; }
            .floating-badge { display: none !important; }
            .hero-mockup { grid-template-columns: 1fr; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .benefits-grid { grid-template-columns: repeat(3, 1fr); }
            .trust-grid { grid-template-columns: repeat(3, 1fr); }
            .pricing-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .navbar { height: 64px; }
            .mobile-drawer { top: 64px; }
            .hero-section { padding: 90px 0 50px; }
            .features-grid { grid-template-columns: 1fr; }
            .steps-grid { grid-template-columns: 1fr; }
            .sync-grid { grid-template-columns: 1fr; }
            .benefits-grid { grid-template-columns: 1fr; }
            .trust-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr; }
            .hero-ctas { flex-direction: column; width: 100%; }
            .hero-ctas .btn-primary,
            .hero-ctas .btn-ghost { width: 100%; justify-content: center; }
            .cta-box { padding: 36px 18px; }
        }
    </style>
</head>
<body>

    <!-- DYNAMIC FLOATING AMBIENT GLOW ORBS -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="ambient-orb orb-3"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container nav-inner">
            <!-- Brand Logo -->
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                <div class="brand-name">
                    <span>DineFlow</span>
                    <span class="brand-badge">POS</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#workflow">Online + Offline</a></li>
                <li><a href="/pricing">Pricing</a></li>
                <li><a href="#faq">FAQ</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>

            <!-- Header Actions -->
            <div class="nav-actions">
                <!-- Buy Source Code Pill -->
                <a href="/buy-source-code" class="btn-header-demo btn-source-glow" style="border-color: rgba(236, 72, 153, 0.5); color: #fff; font-weight: 800;" title="Buy Complete Unencrypted Source Code">
                    <i class="fa-solid fa-fire" style="font-size: 11px; color: #fff;"></i>
                    <span>Buy Source Code</span>
                </a>

                <!-- Live Demo Pill -->
                <a href="/demo" class="btn-header-demo" title="Try Live Sandbox Demo">
                    <i class="fa-solid fa-play" style="font-size: 11px; color: #818CF8;"></i>
                    <span>Live Demo</span>
                </a>

                <!-- WhatsApp Click to Chat -->
                <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20for%20my%20restaurant." target="_blank" class="btn-wa-header" title="Chat on WhatsApp (+91 7777938338)">
                    <i class="fa-brands fa-whatsapp" style="font-size: 16px; color: #25D366;"></i>
                    <span class="wa-text">+91 7777938338</span>
                </a>

                <div class="desktop-auth-btns" style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                @auth
                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-ghost"><i class="fa-solid fa-shield-halved" style="margin-right: 6px;"></i> Super Admin</a>
                    @elseif(Auth::user()->role === 'kitchen')
                        <a href="/kitchen" class="btn-ghost"><i class="fa-solid fa-fire-burner" style="margin-right: 6px;"></i> Kitchen KDS</a>
                    @elseif(in_array(Auth::user()->role, ['cashier', 'waiter']))
                        <a href="/pos" class="btn-ghost"><i class="fa-solid fa-cash-register" style="margin-right: 6px;"></i> POS</a>
                    @else
                        <a href="/dashboard" class="btn-ghost"><i class="fa-solid fa-gauge" style="margin-right: 6px;"></i> Dashboard</a>
                    @endif
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span>+ Register</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span>Start Free</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <button class="mobile-menu-btn" onclick="toggleMobileNav()" aria-label="Toggle Navigation Menu">
                    <i class="fa-solid fa-bars" id="mobileMenuIcon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- MOBILE MENU DRAWER & BACKDROP -->
    <div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop" onclick="toggleMobileNav()"></div>
    <div class="mobile-drawer" id="mobileDrawer">
        <ul>
            <li><a href="/buy-source-code" onclick="toggleMobileNav()" style="color: #F472B6; font-weight: 800;"><i class="fa-solid fa-fire" style="color: #EC4899;"></i> Buy Source Code ($2,499)</a></li>
            <li><a href="/demo" onclick="toggleMobileNav()" style="color: #A5B4FC; font-weight: 700;"><i class="fa-solid fa-play" style="color: #818CF8;"></i> Live Sandbox Demo</a></li>
            <li><a href="#features" onclick="toggleMobileNav()"><i class="fa-solid fa-wand-magic-sparkles"></i> Features</a></li>
            <li><a href="#how-it-works" onclick="toggleMobileNav()"><i class="fa-solid fa-compass"></i> How It Works</a></li>
            <li><a href="#workflow" onclick="toggleMobileNav()"><i class="fa-solid fa-arrows-rotate"></i> Online + Offline</a></li>
            <li><a href="/pricing"><i class="fa-solid fa-gem"></i> Pricing Plans</a></li>
            <li><a href="#faq" onclick="toggleMobileNav()"><i class="fa-solid fa-circle-question"></i> FAQ</a></li>
            <li><a href="/contact"><i class="fa-solid fa-headset"></i> Contact Support</a></li>
            <li>
                <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20for%20my%20restaurant." target="_blank" style="color: #4ADE80; font-weight: 700;">
                    <i class="fa-brands fa-whatsapp" style="color: #25D366; font-size: 18px;"></i> WhatsApp (+91 7777938338)
                </a>
            </li>
            <li style="padding-top: 8px; display: flex; gap: 10px;">
                @auth
                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-ghost" style="flex: 1; justify-content: center;">Super Admin</a>
                    @else
                        <a href="/dashboard" class="btn-ghost" style="flex: 1; justify-content: center;">Dashboard</a>
                    @endif
                    <a href="{{ route('register') }}" class="btn-primary" style="flex: 1; justify-content: center;">+ Register</a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost" style="flex: 1; justify-content: center;">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary" style="flex: 1; justify-content: center;">Start Free</a>
                @endauth
            </li>
        </ul>
    </div>

    <!-- 1. HERO SECTION -->
    <section class="hero-section">
        <div class="container hero-content">
            <!-- Commercial Code Sale Announcement Banner -->
            <a href="/buy-source-code" style="text-decoration:none; display:inline-block; margin-bottom: 20px;">
                <div style="display:inline-flex; align-items:center; gap:10px; padding:8px 22px; background: linear-gradient(135deg, rgba(236,72,153,0.18), rgba(99,102,241,0.18)); border: 1.5px solid rgba(236,72,153,0.45); border-radius:30px; color:#F472B6; font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:0.6px; box-shadow: 0 0 22px rgba(236,72,153,0.3); transition: all 0.3s ease;">
                    <span style="width:8px; height:8px; background:#EC4899; border-radius:50%; display:inline-block; box-shadow:0 0 10px #EC4899;"></span>
                    <span>🔥 Commercial Source Code &amp; White-Label Sale Active • 50% Off</span>
                    <i class="fa-solid fa-arrow-right" style="font-size:11px; margin-left:4px;"></i>
                </div>
            </a>

            <div class="section-tag"><i class="fa-solid fa-bolt"></i> Complete Restaurant Platform</div>
            <h1 class="hero-title">Complete Restaurant Management, POS, QR Ordering & Kitchen in One Platform.</h1>
            <p class="hero-desc">
                DineFlow empowers restaurants, cafés, and food businesses with instant contactless table QR ordering, real-time Kitchen Display (KDS), lightning-fast POS billing, and intelligent inventory tracking.
            </p>

            <div class="hero-ctas">
                <a href="/buy-source-code" class="btn-primary btn-source-glow" style="font-weight: 800; padding: 12px 26px;">
                    <i class="fa-solid fa-fire" style="margin-right: 6px;"></i>
                    <span>Buy Source Code ($2,499)</span>
                </a>
                @auth
                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('superadmin.dashboard') }}" class="btn-primary">
                            <span>Open Super Admin</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @elseif(Auth::user()->role === 'kitchen')
                        <a href="/kitchen" class="btn-primary">
                            <span>Open Kitchen KDS</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @elseif(in_array(Auth::user()->role, ['cashier', 'waiter']))
                        <a href="/pos" class="btn-primary">
                            <span>Open POS Billing</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @else
                        <a href="/dashboard" class="btn-primary">
                            <span>Go to Dashboard</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn-primary">
                        <span>START FREE</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @endauth
                <a href="/demo" class="btn-ghost" style="border: 1px solid #9F1239; color: #FDA4AF; background: rgba(159, 18, 57, 0.12);">
                    <i class="fa-solid fa-play" style="margin-right: 6px; color: #FB7185;"></i> Try Live Demo
                </a>
                @guest
                <a href="{{ route('login') }}" class="btn-ghost">
                    <i class="fa-solid fa-right-to-bracket" style="margin-right: 6px;"></i> Login
                </a>
                @endguest
            </div>

            <div class="hero-pill-badge">
                <i class="fa-solid fa-circle-check" style="color: #10B981;"></i> Free Starter Plan • No Credit Card Required • Setup in 2 Minutes
            </div>

            <!-- Visual Showcase Mockup -->
            <div class="hero-mockup-wrapper">
                <div class="floating-badge badge-top-left">
                    <span class="pulse-dot-green"></span>
                    <span>⚡ 0.8s Ultra-Fast Billing</span>
                </div>
                <div class="floating-badge badge-top-right">
                    <i class="fa-solid fa-code" style="color: #EC4899;"></i>
                    <span>⭐ 100% Full Source Code</span>
                </div>
                <div class="floating-badge badge-bottom-left">
                    <i class="fa-solid fa-qrcode" style="color: #818CF8;"></i>
                    <span>📱 Contactless UPI QR Menu</span>
                </div>

                <div class="hero-mockup">
                    <div class="mock-card">
                        <div class="mock-icon" style="color: #818CF8;"><i class="fa-solid fa-qrcode"></i></div>
                        <div class="mock-title">Smart QR Ordering</div>
                        <div class="mock-sub">Instant contactless guest ordering from dining tables with multi-language voice search and UPI QR payments.</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-icon" style="color: #34D399;"><i class="fa-solid fa-fire-burner"></i></div>
                        <div class="mock-title">Live Kitchen Display (KDS)</div>
                        <div class="mock-sub">Zero lost tickets. Color-coded order aging, audio chime alerts, and instant chef completion transitions.</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-icon" style="color: #F472B6;"><i class="fa-solid fa-cash-register"></i></div>
                        <div class="mock-title">POS Billing & Soundbox</div>
                        <div class="mock-sub">Dynamic UPI QR payments, split bills, multi-language soundbox announcements, and thermal receipt printing.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST / VALUE SECTION -->
    <section class="trust-section">
        <div class="container">
            <div class="trust-grid">
                <div class="trust-item">
                    <h3>Free Starter</h3>
                    <p>₹0 / Forever Free Plan</p>
                </div>
                <div class="trust-item">
                    <h3>No Credit Card</h3>
                    <p>Instant Onboarding</p>
                </div>
                <div class="trust-item">
                    <h3>2 Min Setup</h3>
                    <p>Ready Out of the Box</p>
                </div>
                <div class="trust-item">
                    <h3>Cloud + Offline</h3>
                    <p>Works Without Internet</p>
                </div>
                <div class="trust-item">
                    <h3>Multi-Tenant</h3>
                    <p>Strict Data Isolation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. FEATURES SECTION -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-wand-magic-sparkles"></i> Everything You Need</div>
            <h2 class="section-title">Built for Modern High-Volume Restaurants</h2>
            <p class="section-subtitle">A comprehensive suite designed to eliminate wait times and maximize table turnover.</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feat-icon-wrap"><i class="fa-solid fa-cash-register"></i></div>
                    <h3>POS Billing</h3>
                    <p>Lightning-fast touch terminal with quick item search, modifiers, combo deals, discounts, and one-click receipt printing.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #34D399;"><i class="fa-solid fa-qrcode"></i></div>
                    <h3>QR Digital Menu</h3>
                    <p>Dynamic contactless menu ordering with veg/non-veg filters, dietary badges, live order tracking, and voice recognition.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(236, 72, 153, 0.12); color: #F472B6;"><i class="fa-solid fa-utensils"></i></div>
                    <h3>Kitchen KDS</h3>
                    <p>Paperless kitchen display system with order aging timers, sound alerts, preparation stages, and single-tap item completions.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(245, 158, 11, 0.12); color: #FBBF24;"><i class="fa-solid fa-chair"></i></div>
                    <h3>Table Management</h3>
                    <p>Real-time visual table floor layout with live occupancy status, occupied order amounts, bill settlement, and auto-release.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(59, 130, 246, 0.12); color: #60A5FA;"><i class="fa-solid fa-user-shield"></i></div>
                    <h3>Staff & Roles (RBAC)</h3>
                    <p>Strict role boundaries for Admin, Manager, Cashier, Kitchen Chef, Waiter, and Customer with fast numeric PIN switching.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(139, 92, 246, 0.12); color: #A78BFA;"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <h3>Inventory Control</h3>
                    <p>Real-time stock deduction on menu item orders, reorder level alerts, supplier tracking, and waste management.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(20, 184, 166, 0.12); color: #2DD4BF;"><i class="fa-solid fa-receipt"></i></div>
                    <h3>Expense Management</h3>
                    <p>Record daily operational expenses (groceries, utilities, salaries, maintenance) categorized per restaurant tenant.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(244, 63, 94, 0.12); color: #FB7185;"><i class="fa-solid fa-tags"></i></div>
                    <h3>Offers & Coupons</h3>
                    <p>Create percentage and flat discount coupons with expiry dates, minimum bill thresholds, and auto-apply logic.</p>
                </div>

                <div class="feature-card">
                    <div class="feat-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #10B981;"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <h3>Offline Desktop Sync</h3>
                    <p>Windows Desktop POS continues running offline during internet outages and auto-syncs with cloud MySQL when reconnected.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. HOW IT WORKS -->
    <section class="steps-section" id="how-it-works">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-compass"></i> Simple Onboarding</div>
            <h2 class="section-title">How DineFlow Works</h2>
            <p class="section-subtitle">Get your restaurant up and running in 4 quick steps.</p>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-num">01</div>
                    <h3>Create Account</h3>
                    <p>Sign up for your free restaurant account with your restaurant name, contact, and preferred currency.</p>
                </div>

                <div class="step-card">
                    <div class="step-num">02</div>
                    <h3>Add Menu & Tables</h3>
                    <p>Easily input your food categories, dishes, prices, and configure your dining floor tables.</p>
                </div>

                <div class="step-card">
                    <div class="step-num">03</div>
                    <h3>Generate QR Codes</h3>
                    <p>Print high-resolution table QR codes that direct guests to your live digital menu.</p>
                </div>

                <div class="step-card">
                    <div class="step-num">04</div>
                    <h3>Start Taking Orders</h3>
                    <p>Receive orders on POS and Kitchen KDS in real-time, collect payments, and track revenue.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. ONLINE + OFFLINE ARCHITECTURE -->
    <section class="sync-section" id="workflow">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-arrows-rotate"></i> Hybrid Reliability</div>
            <h2 class="section-title">Uninterrupted Operations: Online + Offline</h2>
            <p class="section-subtitle">Never lose an order, even if your restaurant internet connection drops.</p>

            <div class="sync-grid">
                <div class="sync-card cloud">
                    <span class="sync-badge">Cloud Mode (Online)</span>
                    <h3>Real-Time Cloud Workflow</h3>
                    <p>Orders flow instantly from guest table QR scans to POS and Kitchen displays over high-speed cloud infrastructure.</p>
                    <div class="sync-flow">
                        <span>1. Customer scans QR &amp; submits order</span>
                        <span>2. Order saves to Cloud Database</span>
                        <span>3. POS &amp; KDS notify kitchen staff</span>
                        <span>4. Instant UPI payment verification</span>
                    </div>
                </div>

                <div class="sync-card offline">
                    <span class="sync-badge">Offline Mode (Desktop POS)</span>
                    <h3>Local Desktop Resiliency</h3>
                    <p>When internet disconnects, the Windows Desktop POS continues billing locally into SQLite and auto-syncs when back online.</p>
                    <div class="sync-flow">
                        <span>1. POS creates local order in SQLite</span>
                        <span>2. Receipts print via local thermal printer</span>
                        <span>3. Internet returns ➔ Auto-Sync queue fires</span>
                        <span>4. Reconciled with Cloud MySQL (Zero Duplicates)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. RESTAURANT BENEFITS -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-users"></i> Tailored Experience</div>
            <h2 class="section-title">Benefits for Your Entire Team</h2>
            <p class="section-subtitle">Designed specifically for every restaurant stakeholder.</p>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div style="font-size: 22px; margin-bottom: 8px;">👔</div>
                    <h4>Restaurant Owner</h4>
                    <p>Live sales analytics, expense tracking, menu engineering matrix, and multi-branch control.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size: 22px; margin-bottom: 8px;">💳</div>
                    <h4>Cashier</h4>
                    <p>Speedy billing, split bills, UPI QR soundbox confirmation, and instant thermal receipt printing.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size: 22px; margin-bottom: 8px;">🏃</div>
                    <h4>Waiter</h4>
                    <p>Digital waiter calls with table numbers, mobile table ordering, and instant dish ready notifications.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size: 22px; margin-bottom: 8px;">👨‍🍳</div>
                    <h4>Kitchen Chef</h4>
                    <p>Color-coded KDS screen, zero missed paper tickets, preparation countdown, and single-click completion.</p>
                </div>
                <div class="benefit-card">
                    <div style="font-size: 22px; margin-bottom: 8px;">📱</div>
                    <h4>Customer</h4>
                    <p>Zero wait time, contactless digital menu with voice ordering, live order tracker, and easy UPI payment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. PRICING -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-gem"></i> Transparent Pricing</div>
            <h2 class="section-title">Simple Plans for Restaurants of Any Size</h2>
            <p class="section-subtitle">Start 100% free with no credit card required. Upgrade as your restaurant expands.</p>

            <div class="pricing-grid">
                <!-- Free Plan -->
                <div class="price-card">
                    <div class="plan-name">Free Starter</div>
                    <div class="plan-price">₹0 <span>/ forever</span></div>
                    <ul class="plan-features">
                        <li><i class="fa-solid fa-check"></i> Up to 10 Dining Tables</li>
                        <li><i class="fa-solid fa-check"></i> Up to 5 Staff Accounts</li>
                        <li><i class="fa-solid fa-check"></i> Up to 100 Menu Items</li>
                        <li><i class="fa-solid fa-check"></i> 500 Orders / Month</li>
                        <li><i class="fa-solid fa-check"></i> QR Code Menus &amp; KDS</li>
                        <li><i class="fa-solid fa-check"></i> Standard POS Billing</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-primary" style="width: 100%;">Get Started Free</a>
                </div>

                <!-- Pro Plan -->
                <div class="price-card featured">
                    <div class="featured-tag">Most Popular</div>
                    <div class="plan-name">Pro Growth</div>
                    <div class="plan-price">₹1,499 <span>/ month</span></div>
                    <ul class="plan-features">
                        <li><i class="fa-solid fa-check"></i> Up to 50 Dining Tables</li>
                        <li><i class="fa-solid fa-check"></i> Up to 20 Staff Accounts</li>
                        <li><i class="fa-solid fa-check"></i> Up to 500 Menu Items</li>
                        <li><i class="fa-solid fa-check"></i> 5,000 Orders / Month</li>
                        <li><i class="fa-solid fa-check"></i> Offline Desktop Sync</li>
                        <li><i class="fa-solid fa-check"></i> Advanced Sales Analytics</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn-primary" style="width: 100%;">Start 14-Day Trial</a>
                </div>

                <!-- Enterprise Plan -->
                <div class="price-card">
                    <div class="plan-name">Enterprise</div>
                    <div class="plan-price">₹3,999 <span>/ month</span></div>
                    <ul class="plan-features">
                        <li><i class="fa-solid fa-check"></i> Unlimited Tables &amp; Staff</li>
                        <li><i class="fa-solid fa-check"></i> Unlimited Menu Items</li>
                        <li><i class="fa-solid fa-check"></i> Unlimited Monthly Orders</li>
                        <li><i class="fa-solid fa-check"></i> Multi-Branch Management</li>
                        <li><i class="fa-solid fa-check"></i> Dedicated Account Manager</li>
                        <li><i class="fa-solid fa-check"></i> Priority 24/7 Phone Support</li>
                    </ul>
                    <a href="/contact" class="btn-ghost" style="width: 100%; justify-content: center;">Contact Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. FAQ -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="section-tag"><i class="fa-solid fa-circle-question"></i> Questions &amp; Answers</div>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Everything you need to know about DineFlow.</p>

            <div class="faq-list">
                <div class="faq-item">
                    <h4>Is DineFlow free to use?</h4>
                    <p>Yes! Our Free Starter Plan is 100% free forever with no credit card required. It includes up to 10 tables, 5 staff accounts, 100 menu items, POS billing, and QR ordering.</p>
                </div>

                <div class="faq-item">
                    <h4>Do customers need to download an application to order?</h4>
                    <p>No. Customers simply scan the QR code on their dining table with their phone camera. The digital menu opens instantly in any mobile browser.</p>
                </div>

                <div class="faq-item">
                    <h4>Can POS work offline when internet is down?</h4>
                    <p>Yes. Our Windows Desktop POS app saves orders locally in an offline SQLite database. When internet connection returns, all orders automatically synchronize to the cloud.</p>
                </div>

                <div class="faq-item">
                    <h4>Can I connect standard thermal receipt printers?</h4>
                    <p>Yes. DineFlow supports standard 58mm and 80mm ESC/POS thermal printers, network printers, and standard A4 tax invoices.</p>
                </div>

                <div class="faq-item">
                    <h4>Can multiple independent restaurants use the platform?</h4>
                    <p>Yes. DineFlow features strict multi-tenant data isolation. Each restaurant has its own private menu, customer base, orders, inventory, and staff.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FINAL CTA -->
    <section class="cta-banner">
        <div class="container">
            <div class="cta-box">
                <h2>Start Your Restaurant Free Today</h2>
                <p>Join hundreds of modern restaurants accelerating their table turnover and kitchen speed with DineFlow.</p>
                <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('register') }}" class="btn-primary" style="padding: 13px 28px; font-size: 15px; border-radius: 10px;">
                        <span>START FREE TODAY</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="https://wa.me/917777938338?text=Hello%20DineFlow%20Team%2C%20I%20would%20like%20to%20know%20more%20about%20DineFlow%20for%20my%20restaurant." target="_blank" class="btn-primary" style="padding: 13px 24px; font-size: 15px; border-radius: 10px; background: linear-gradient(135deg, #25D366, #128C7E); box-shadow: 0 4px 16px rgba(37, 211, 102, 0.4);">
                        <i class="fa-brands fa-whatsapp" style="font-size: 18px;"></i>
                        <span>Chat on WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="/" class="brand-logo">
                        <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                        <div class="brand-name">
                            <span>DineFlow</span>
                            <span class="brand-badge">POS</span>
                        </div>
                    </a>
                    <p>Complete restaurant management platform, smart table QR ordering, kitchen display system, and POS billing.</p>
                </div>

                <div class="footer-col">
                    <h5>Product</h5>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#how-it-works">How It Works</a></li>
                        <li><a href="#workflow">Online + Offline</a></li>
                        <li><a href="/pricing">Pricing</a></li>
                        <li><a href="/buy-source-code" style="color: #F472B6; font-weight: 700;">Buy Source Code</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Account &amp; Auth</h5>
                    <ul>
                        <li><a href="{{ route('register') }}">Start Free</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('auth.pin-login') }}">Quick PIN Login</a></li>
                        <li><a href="/contact">Support</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h5>Legal &amp; Info</h5>
                    <ul>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="/contact">Contact Sales</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div>&copy; 2026 DineFlow. All rights reserved.</div>
                <div>Designed for High-Performance Restaurant Operations</div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileNav() {
            const drawer = document.getElementById('mobileDrawer');
            const backdrop = document.getElementById('mobileDrawerBackdrop');
            const icon = document.getElementById('mobileMenuIcon');
            
            if (drawer && backdrop) {
                const isActive = drawer.classList.contains('active');
                if (isActive) {
                    drawer.classList.remove('active');
                    backdrop.classList.remove('active');
                    if (icon) icon.className = 'fa-solid fa-bars';
                } else {
                    drawer.classList.add('active');
                    backdrop.classList.add('active');
                    if (icon) icon.className = 'fa-solid fa-xmark';
                }
            }
        }
    </script>
</body>
</html>
