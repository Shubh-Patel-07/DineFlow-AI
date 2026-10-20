<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Acquire Complete Source Code — DineFlow AI & Tendorabuy Restaurant SaaS</title>
    <meta name="description" content="Acquire the complete unencrypted source code of DineFlow AI & Tendorabuy. Web POS, QR Table Ordering, Kitchen KDS, Windows Desktop, and Multi-tenant SaaS with Commercial License.">
    
    <!-- Open Graph Metadata -->
    <meta property="og:title" content="DineFlow AI Source Code Sale — Multi-Tenant Restaurant SaaS Platform">
    <meta property="og:description" content="Unencrypted production codebase. Web POS, QR Menu, Live KDS, Electron Desktop, Multi-Tenancy. Keep 100% of your SaaS revenue.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/buy-source-code') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600&family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #6366F1;
            --primary-glow: rgba(99, 102, 241, 0.5);
            --primary-dark: #4338CA;
            --accent: #EC4899;
            --accent-glow: rgba(236, 72, 153, 0.45);
            --cyan: #06B6D4;
            --cyan-glow: rgba(6, 182, 212, 0.4);
            --success: #10B981;
            --success-glow: rgba(16, 185, 129, 0.4);
            --whatsapp: #25D366;
            --whatsapp-dark: #128C7E;
            --warning: #F59E0B;
            --bg: #070A14;
            --bg-alt: #0C1022;
            --card: #121832;
            --card-glass: rgba(18, 24, 50, 0.75);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-primary: #F8FAFC;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
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
            position: relative;
            background-image: 
                radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                radial-gradient(rgba(99, 102, 241, 0.06) 1px, transparent 1px);
            background-size: 36px 36px, 72px 72px;
            background-position: 0 0, 18px 18px;
        }

        /* ─── DYNAMIC FLOATING AMBIENT GLOW ORBS ─── */
        .ambient-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.35;
            pointer-events: none;
            z-index: 0;
            will-change: transform;
        }
        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #6366F1, transparent 70%);
            top: -100px;
            left: -100px;
            animation: orbFloat1 18s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, #EC4899, transparent 70%);
            top: 30%;
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

        .container {
            width: 100%;
            max-width: 1220px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        /* ─── SCROLL REVEAL ANIMATIONS ─── */
        .reveal {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ─── NAVBAR ─── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 72px;
            background: rgba(7, 10, 20, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1000;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        .navbar.scrolled {
            background: rgba(7, 10, 20, 0.96);
            border-bottom-color: rgba(99, 102, 241, 0.25);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
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
            gap: 12px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #EC4899, #6366F1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: #fff;
            box-shadow: 0 0 20px var(--accent-glow);
            position: relative;
            overflow: hidden;
        }
        .brand-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(60deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(30deg);
            animation: shimmer 4s infinite linear;
        }

        @keyframes shimmer {
            0% { transform: translateX(-150%) rotate(30deg); }
            100% { transform: translateX(150%) rotate(30deg); }
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            background: rgba(236, 72, 153, 0.2);
            color: #F472B6;
            border: 1px solid rgba(236, 72, 153, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: pulseGlow 2.5s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 rgba(236, 72, 153, 0); }
            50% { box-shadow: 0 0 12px rgba(236, 72, 153, 0.6); }
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 22px;
            list-style: none;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #EC4899, #6366F1);
            transition: width 0.3s ease;
        }

        .nav-links a:hover { color: #fff; }
        .nav-links a:hover::after { width: 100%; }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-header-demo {
            padding: 8px 15px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.35);
            color: #A5B4FC;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .btn-header-demo:hover {
            background: rgba(99, 102, 241, 0.25);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-wa-header {
            padding: 8px 16px;
            background: linear-gradient(135deg, rgba(37, 211, 102, 0.2), rgba(18, 140, 126, 0.2));
            border: 1px solid rgba(37, 211, 102, 0.4);
            color: #4ADE80;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.25s ease;
        }
        .btn-wa-header:hover {
            background: #25D366;
            color: #064E3B;
            box-shadow: 0 4px 18px rgba(37, 211, 102, 0.4);
            transform: translateY(-1px);
        }

        /* ─── HERO SECTION ─── */
        .hero {
            padding: 140px 0 80px;
            text-align: center;
            position: relative;
        }

        .sale-ping-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 20px;
            background: rgba(236, 72, 153, 0.12);
            border: 1.5px solid rgba(236, 72, 153, 0.4);
            border-radius: 30px;
            color: #F472B6;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-bottom: 26px;
            box-shadow: 0 0 25px rgba(236, 72, 153, 0.25);
            animation: floatSlow 4s ease-in-out infinite;
        }

        @keyframes floatSlow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .ping-dot {
            width: 8px;
            height: 8px;
            background: #EC4899;
            border-radius: 50%;
            position: relative;
        }
        .ping-dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: inherit;
            border-radius: inherit;
            animation: ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        @keyframes ping {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(3.5); opacity: 0; }
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(34px, 5.5vw, 62px);
            font-weight: 900;
            line-height: 1.12;
            letter-spacing: -1.2px;
            margin-bottom: 24px;
            max-width: 1050px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, #EC4899 0%, #818CF8 45%, #38BDF8 90%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: gradientShift 6s linear infinite alternate;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .hero-subtitle {
            font-size: clamp(16px, 2.1vw, 20px);
            color: var(--text-secondary);
            max-width: 820px;
            margin: 0 auto 38px;
            line-height: 1.65;
            font-weight: 400;
        }

        .hero-pills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-bottom: 42px;
        }

        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            color: #CBD5E1;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }
        .pill-badge:hover {
            border-color: rgba(99, 102, 241, 0.5);
            background: rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
            color: #fff;
        }
        .pill-badge i { color: #818CF8; }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 18px;
            margin-bottom: 40px;
        }

        .btn-hero-primary {
            padding: 16px 34px;
            background: linear-gradient(135deg, #EC4899, #8B5CF6);
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 800;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 35px rgba(236, 72, 153, 0.45);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-hero-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 45px rgba(236, 72, 153, 0.6);
            color: #fff;
        }
        .btn-hero-primary:hover::after {
            transform: translateX(100%);
        }

        .btn-hero-whatsapp {
            padding: 16px 32px;
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 800;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.35);
            transition: all 0.3s ease;
        }
        .btn-hero-whatsapp:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 40px rgba(37, 211, 102, 0.55);
            color: #fff;
        }

        .btn-hero-demo {
            padding: 16px 30px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #F8FAFC;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }
        .btn-hero-demo:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #fff;
            transform: translateY(-2px);
        }

        /* ─── ANIMATED INTERACTIVE CODE TERMINAL WINDOW ─── */
        .terminal-wrapper {
            max-width: 960px;
            margin: 45px auto 0;
            background: rgba(12, 16, 34, 0.85);
            border: 1.5px solid rgba(99, 102, 241, 0.3);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.55), 0 0 40px rgba(99, 102, 241, 0.15);
            backdrop-filter: blur(16px);
            text-align: left;
            position: relative;
        }

        .terminal-header {
            background: rgba(8, 11, 24, 0.95);
            padding: 12px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .terminal-dots {
            display: flex;
            gap: 8px;
        }
        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .dot-red { background: #FF5F56; }
        .dot-yellow { background: #FFBD2E; }
        .dot-green { background: #27C93F; }

        .terminal-tabs {
            display: flex;
            gap: 6px;
        }
        .terminal-tab {
            padding: 6px 14px;
            font-size: 12px;
            font-family: 'Fira Code', monospace;
            border-radius: 6px;
            color: #94A3B8;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .terminal-tab.active {
            background: rgba(99, 102, 241, 0.15);
            border-color: rgba(99, 102, 241, 0.3);
            color: #A5B4FC;
            font-weight: 600;
        }

        .terminal-status {
            font-size: 11.5px;
            color: #4ADE80;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .terminal-body {
            padding: 24px;
            font-family: 'Fira Code', monospace;
            font-size: 13.5px;
            line-height: 1.7;
            color: #E2E8F0;
            overflow-x: auto;
            max-height: 340px;
        }

        .code-keyword { color: #EC4899; font-weight: 600; }
        .code-fn { color: #60A5FA; }
        .code-str { color: #34D399; }
        .code-comment { color: #64748B; font-style: italic; }
        .code-num { color: #F59E0B; }
        .code-var { color: #F472B6; }

        .cursor {
            display: inline-block;
            width: 8px;
            height: 15px;
            background: #818CF8;
            vertical-align: middle;
            animation: blink 1s step-end infinite;
        }
        @keyframes blink {
            from, to { opacity: 1; }
            50% { opacity: 0; }
        }

        /* ─── HIGHLIGHT STATS COUNTERS ─── */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin: 60px auto 0;
            max-width: 1080px;
        }

        .stat-item {
            background: var(--card-glass);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #818CF8, transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .stat-item:hover {
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.4);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
        }
        .stat-item:hover::before { opacity: 1; }

        .stat-item .stat-num {
            font-family: 'Outfit', sans-serif;
            font-size: 34px;
            font-weight: 900;
            color: #fff;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .stat-item .stat-desc {
            font-size: 13.5px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ─── SECTION STYLING ─── */
        .section {
            padding: 100px 0;
            position: relative;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(99, 102, 241, 0.12);
            border: 1px solid rgba(99, 102, 241, 0.35);
            border-radius: 30px;
            color: #A5B4FC;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 16px;
        }

        .section-header {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 55px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(28px, 4.2vw, 42px);
            font-weight: 800;
            letter-spacing: -0.6px;
            margin-bottom: 16px;
            line-height: 1.22;
            color: #fff;
        }

        .section-subtitle {
            font-size: 16.5px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ─── LICENSING CARD RULES ─── */
        .licensing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 28px;
            margin-top: 20px;
        }

        .license-card {
            background: var(--card-glass);
            border-radius: 20px;
            padding: 36px 30px;
            position: relative;
            backdrop-filter: blur(14px);
            transition: all 0.3s ease;
        }

        .license-card.allowed {
            border: 1.5px solid rgba(16, 185, 129, 0.35);
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.07) 0%, rgba(18, 24, 50, 0.9) 100%);
        }
        .license-card.allowed:hover {
            border-color: rgba(16, 185, 129, 0.6);
            box-shadow: 0 15px 45px rgba(16, 185, 129, 0.2);
            transform: translateY(-4px);
        }

        .license-card.prohibited {
            border: 1.5px solid rgba(239, 68, 68, 0.35);
            background: linear-gradient(180deg, rgba(239, 68, 68, 0.07) 0%, rgba(18, 24, 50, 0.9) 100%);
        }
        .license-card.prohibited:hover {
            border-color: rgba(239, 68, 68, 0.6);
            box-shadow: 0 15px 45px rgba(239, 68, 68, 0.2);
            transform: translateY(-4px);
        }

        .license-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--card-border);
        }

        .license-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        .allowed .license-icon-box {
            background: rgba(16, 185, 129, 0.18);
            color: #34D399;
            box-shadow: 0 0 20px var(--success-glow);
        }
        .prohibited .license-icon-box {
            background: rgba(239, 68, 68, 0.18);
            color: #F87171;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.35);
        }

        .license-header h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 21px;
            font-weight: 800;
            color: #fff;
        }

        .license-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .license-list li {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-size: 14.5px;
            line-height: 1.55;
            color: #E2E8F0;
        }

        .license-list li i {
            margin-top: 4px;
            flex-shrink: 0;
            font-size: 16px;
        }
        .allowed .license-list li i { color: #10B981; }
        .prohibited .license-list li i { color: #EF4444; }

        .legal-notice-box {
            margin-top: 36px;
            background: rgba(99, 102, 241, 0.08);
            border: 1.5px solid rgba(99, 102, 241, 0.35);
            border-radius: 16px;
            padding: 22px 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
            backdrop-filter: blur(10px);
        }

        /* ─── DYNAMIC CURRENCY SWITCHER ─── */
        .currency-switch-box {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .currency-btn {
            padding: 8px 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #CBD5E1;
            font-size: 13.5px;
            font-weight: 700;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .currency-btn.active {
            background: linear-gradient(135deg, #EC4899, #8B5CF6);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 16px rgba(236, 72, 153, 0.4);
        }
        .currency-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        /* ─── PRICING CARDS WITH CONIC GLOW ─── */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 28px;
            align-items: stretch;
            margin-top: 10px;
        }

        .pricing-card {
            background: var(--card-glass);
            border: 1.5px solid var(--card-border);
            border-radius: 24px;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            position: relative;
            backdrop-filter: blur(14px);
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .pricing-card:hover {
            transform: translateY(-6px);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.4);
        }

        /* GLOWING ANIMATED BORDER FOR FEATURED CARD */
        .pricing-card.featured {
            border: 1.5px solid #EC4899;
            background: linear-gradient(180deg, rgba(236, 72, 153, 0.14) 0%, rgba(18, 24, 50, 0.95) 100%);
            box-shadow: 0 20px 50px rgba(236, 72, 153, 0.3);
            transform: scale(1.03);
            z-index: 2;
        }
        .pricing-card.featured:hover {
            transform: scale(1.03) translateY(-6px);
            box-shadow: 0 25px 60px rgba(236, 72, 153, 0.45);
        }

        .featured-ribbon {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #EC4899, #8B5CF6);
            color: #fff;
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            box-shadow: 0 4px 18px rgba(236, 72, 153, 0.5);
            white-space: nowrap;
        }

        .tier-name {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
        }

        .tier-tagline {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 24px;
            min-height: 42px;
            line-height: 1.5;
        }

        .tier-price-box {
            display: flex;
            align-items: baseline;
            gap: 10px;
            margin-bottom: 28px;
            padding-bottom: 22px;
            border-bottom: 1px solid var(--card-border);
        }

        .price-curr {
            font-size: 28px;
            font-weight: 800;
            color: #A5B4FC;
            transition: all 0.3s;
        }

        .price-val {
            font-family: 'Outfit', sans-serif;
            font-size: 48px;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            transition: all 0.3s;
        }

        .price-original {
            font-size: 18px;
            color: var(--text-muted);
            text-decoration: line-through;
            font-weight: 600;
        }

        .price-term {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .tier-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 34px;
            flex-grow: 1;
        }

        .tier-features li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            color: #CBD5E1;
            line-height: 1.45;
        }

        .tier-features li i {
            font-size: 14px;
            color: #10B981;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .pricing-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-pricing-primary {
            padding: 14px;
            background: linear-gradient(135deg, #6366F1, #4338CA);
            color: #fff;
            text-decoration: none;
            font-size: 15px;
            font-weight: 800;
            border-radius: 12px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: all 0.25s ease;
        }

        .pricing-card.featured .btn-pricing-primary {
            background: linear-gradient(135deg, #EC4899, #8B5CF6);
            box-shadow: 0 6px 22px rgba(236, 72, 153, 0.45);
        }

        .btn-pricing-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(99, 102, 241, 0.4);
            color: #fff;
        }

        .btn-pricing-whatsapp {
            padding: 13px;
            background: rgba(37, 211, 102, 0.15);
            border: 1px solid rgba(37, 211, 102, 0.35);
            color: #4ADE80;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
        }

        .btn-pricing-whatsapp:hover {
            background: #25D366;
            color: #064E3B;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.35);
        }

        /* ─── INTERACTIVE MODULES SHOWCASE (TABS) ─── */
        .module-showcase {
            background: var(--card-glass);
            border: 1.5px solid var(--card-border);
            border-radius: 24px;
            padding: 36px;
            backdrop-filter: blur(14px);
            margin-top: 30px;
        }

        .module-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
        }

        .module-tab-btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #CBD5E1;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .module-tab-btn.active {
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
        }

        .module-tab-pane {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .module-tab-pane.active {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 36px;
            align-items: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .module-info h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 14px;
        }
        .module-info p {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.65;
            margin-bottom: 22px;
        }
        .module-checklist {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .module-checklist li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14.5px;
            color: #E2E8F0;
        }
        .module-checklist li i { color: #34D399; font-size: 15px; }

        .module-preview-card {
            background: rgba(8, 11, 24, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        /* ─── TECH STACK ─── */
        .tech-box {
            background: var(--card-glass);
            border: 1.5px solid var(--card-border);
            border-radius: 20px;
            padding: 36px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 30px;
            backdrop-filter: blur(14px);
        }

        .tech-col h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: #818CF8;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tech-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .tech-col ul li {
            font-size: 14px;
            color: #CBD5E1;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tech-col ul li i { color: #10B981; font-size: 13px; }

        /* ─── PROCESS STEPS ─── */
        .process-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 22px;
            margin-top: 30px;
        }

        .process-card {
            background: var(--card-glass);
            border: 1.5px solid var(--card-border);
            border-radius: 18px;
            padding: 30px 24px;
            position: relative;
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
        }
        .process-card:hover {
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.45);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
        }

        .step-num {
            position: absolute;
            top: 18px;
            right: 20px;
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.08);
        }

        .process-card h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 10px;
        }

        .process-card p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.55;
        }

        /* ─── FAQ ACCORDION ─── */
        .faq-grid {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-width: 880px;
            margin: 0 auto;
        }

        .faq-item {
            background: var(--card-glass);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            overflow: hidden;
            backdrop-filter: blur(12px);
            transition: all 0.25s ease;
        }
        .faq-item:hover {
            border-color: rgba(99, 102, 241, 0.35);
        }

        .faq-question {
            padding: 22px 24px;
            font-family: 'Outfit', sans-serif;
            font-size: 17.5px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            user-select: none;
        }

        .faq-question i.faq-chevron {
            color: #818CF8;
            transition: transform 0.3s ease;
            font-size: 14px;
        }
        .faq-item.active .faq-chevron {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1), padding 0.3s ease;
            padding: 0 24px;
            color: var(--text-secondary);
            font-size: 14.5px;
            line-height: 1.65;
        }
        .faq-item.active .faq-answer {
            max-height: 250px;
            padding-bottom: 22px;
        }

        /* ─── FOOTER ─── */
        .footer {
            padding: 70px 0 35px;
            background: #04070E;
            border-top: 1px solid var(--card-border);
            position: relative;
            z-index: 1;
        }

        .footer-inner {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 26px;
            padding-bottom: 35px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            list-style: none;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .footer-links a:hover { color: #fff; }

        .footer-bottom {
            padding-top: 28px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            font-size: 13.5px;
            color: var(--text-muted);
        }

        @media (max-width: 900px) {
            .nav-links { display: none; }
            .hero-title { font-size: 34px; }
            .pricing-card.featured { transform: none; }
            .module-tab-pane.active { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- ─── FLOATING GLOWING AMBIENT ORBS ─── -->
    <div class="ambient-orb orb-1"></div>
    <div class="ambient-orb orb-2"></div>
    <div class="ambient-orb orb-3"></div>

    <!-- ─── TOP NAVBAR ─── -->
    <nav class="navbar" id="topNavbar">
        <div class="container nav-inner">
            <a href="/" class="brand-logo">
                <div class="brand-icon"><i class="fa-solid fa-code"></i></div>
                <div class="brand-name">
                    <span>Tendorabuy</span>
                    <span class="brand-badge">Source Sale</span>
                </div>
            </a>

            <ul class="nav-links">
                <li><a href="/">Home</a></li>
                <li><a href="#license-terms">License Rights</a></li>
                <li><a href="#pricing">Pricing &amp; Tiers</a></li>
                <li><a href="#modules">Interactive Demo</a></li>
                <li><a href="#tech-specs">Tech Architecture</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>

            <div class="nav-actions">
                <a href="/demo" class="btn-header-demo" title="Try Sandbox Demo">
                    <i class="fa-solid fa-play" style="font-size: 11px;"></i>
                    <span>Test Sandbox</span>
                </a>
                <a href="https://wa.me/917777938338?text=Hello%20Patel%20Shubh!%20I%20am%20interested%20in%20acquiring%20the%20DineFlow%20AI%20%2F%20Tendorabuy%20Source%20Code." class="btn-wa-header" target="_blank">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>+91 7777938338</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- ─── HERO SECTION ─── -->
    <header class="hero">
        <div class="container">
            <div class="sale-ping-badge">
                <div class="ping-dot"></div>
                <span>Exclusive Commercial Code Sale • 100% Unencrypted Source</span>
            </div>

            <h1 class="hero-title reveal active">
                Acquire the Complete <span class="gradient-text">Restaurant POS &amp; SaaS Codebase</span>
            </h1>

            <p class="hero-subtitle reveal active">
                Launch your own branded restaurant tech agency or multi-tenant SaaS in 24 hours. Includes Cloud POS billing, Table QR digital menu, Real-Time Kitchen KDS, Windows Desktop App, and Super Admin Management.
            </p>

            <div class="hero-pills reveal active">
                <div class="pill-badge"><i class="fa-solid fa-layer-group"></i> Laravel 11 + Vanilla JS + Electron</div>
                <div class="pill-badge"><i class="fa-solid fa-shield-halved"></i> Non-Exclusive Commercial License</div>
                <div class="pill-badge"><i class="fa-solid fa-sack-dollar"></i> Keep 100% Client Subscription Revenue</div>
                <div class="pill-badge"><i class="fa-solid fa-bolt"></i> Instant GitHub Repo Access</div>
            </div>

            <div class="hero-actions reveal active">
                <a href="#pricing" class="btn-hero-primary">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span>View Pricing &amp; Acquire Code</span>
                </a>
                <a href="https://wa.me/917777938338?text=Hello%20Patel%20Shubh!%20I%20want%20to%20buy%20the%20DineFlow%20AI%20source%20code.%20Please%20share%20contract%20and%20payment%20details." class="btn-hero-whatsapp" target="_blank">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Chat on WhatsApp (+91 7777938338)</span>
                </a>
                <a href="/demo" class="btn-hero-demo">
                    <i class="fa-solid fa-circle-play"></i>
                    <span>Test Drive Live Demo</span>
                </a>
            </div>

            <!-- ANIMATED CODE TERMINAL WINDOW PREVIEW -->
            <div class="terminal-wrapper reveal active">
                <div class="terminal-header">
                    <div class="terminal-dots">
                        <span class="dot dot-red"></span>
                        <span class="dot dot-yellow"></span>
                        <span class="dot dot-green"></span>
                    </div>
                    <div class="terminal-tabs">
                        <span class="terminal-tab active" onclick="switchCodeTab('order')">OrderService.php</span>
                        <span class="terminal-tab" onclick="switchCodeTab('pos')">posTerminal.js</span>
                        <span class="terminal-tab" onclick="switchCodeTab('printer')">EscPosDriver.php</span>
                    </div>
                    <div class="terminal-status">
                        <i class="fa-solid fa-circle-check"></i> 100% Production Tested
                    </div>
                </div>
                <div class="terminal-body" id="codeTerminalBody">
                    <pre><code id="codeSnippet"><span class="code-comment">// High-Concurrency Transaction with Atomic lockForUpdate</span>
<span class="code-keyword">public function</span> <span class="code-fn">createOrder</span>(array <span class="code-var">$payload</span>, <span class="code-keyword">int</span> <span class="code-var">$tenantId</span>): Order 
{
    <span class="code-keyword">return</span> DB::<span class="code-fn">transaction</span>(<span class="code-keyword">function</span> () <span class="code-keyword">use</span> (<span class="code-var">$payload</span>, <span class="code-var">$tenantId</span>) {
        <span class="code-comment">// Strict tenant isolation &amp; atomic order counter</span>
        <span class="code-var">$table</span> = Table::<span class="code-fn">where</span>(<span class="code-str">'restaurant_id'</span>, <span class="code-var">$tenantId</span>)
                      -><span class="code-fn">lockForUpdate</span>()
                      -><span class="code-fn">findOrFail</span>(<span class="code-var">$payload</span>[<span class="code-str">'table_id'</span>]);

        <span class="code-var">$order</span> = Order::<span class="code-fn">create</span>([
            <span class="code-str">'restaurant_id'</span> => <span class="code-var">$tenantId</span>,
            <span class="code-str">'table_id'</span>      => <span class="code-var">$table</span>->id,
            <span class="code-str">'total_amount'</span>  => <span class="code-var">$payload</span>[<span class="code-str">'grand_total'</span>],
            <span class="code-str">'status'</span>        => <span class="code-str">'received'</span>
        ]);

        <span class="code-comment">// Dispatch real-time KOT to kitchen printer &amp; KDS socket</span>
        event(<span class="code-keyword">new</span> <span class="code-fn">NewOrderPlaced</span>(<span class="code-var">$order</span>));
        <span class="code-keyword">return</span> <span class="code-var">$order</span>;
    });
}</code><span class="cursor"></span></pre>
                </div>
            </div>

            <!-- STATS COUNTER STRIP -->
            <div class="stats-strip reveal">
                <div class="stat-item">
                    <div class="stat-num"><span class="counter" data-target="100">0</span>%</div>
                    <div class="stat-desc">Unencrypted Source Code</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num"><span class="counter" data-target="50">0</span>+</div>
                    <div class="stat-desc">Production Modules Included</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num"><span class="counter" data-target="0">0</span>%</div>
                    <div class="stat-desc">Royalties or Revenue Sharing</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">&lt; <span class="counter" data-target="35">0</span>ms</div>
                    <div class="stat-desc">Sub-Second Fast API Dispatch</div>
                </div>
            </div>
        </div>
    </header>

    <!-- ─── STRICT LEGAL LICENSING RULES ─── -->
    <section class="section" id="license-terms">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag"><i class="fa-solid fa-file-contract"></i> Legal Licensing Framework</div>
                <h2 class="section-title">Transparent Commercial Freedom</h2>
                <p class="section-subtitle">
                    Clear, unambiguous terms designed for technical entrepreneurs and agencies to scale restaurant clients without legal risks.
                </p>
            </div>

            <div class="licensing-grid">
                <!-- WHAT YOU CAN DO -->
                <div class="license-card allowed reveal">
                    <div class="license-header">
                        <div class="license-icon-box"><i class="fa-solid fa-circle-check"></i></div>
                        <div>
                            <h3>What You CAN Do (Permitted)</h3>
                            <span style="font-size:12px; color:#34D399; font-weight:700; text-transform:uppercase;">Full SaaS Autonomy</span>
                        </div>
                    </div>
                    <ul class="license-list">
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span><strong>Unlimited Server Deployments:</strong> Host on AWS, DigitalOcean, Hetzner, GCP, or private on-premise Linux/Windows servers.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span><strong>Complete White-Labeling:</strong> Replace branding, logos, app titles, colors, domains, and email SMTP with your own identity.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span><strong>Unlimited Restaurant Clients:</strong> Charge restaurants monthly ($49/mo, $99/mo, $199/mo) or annual SaaS retainers.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span><strong>Keep 100% Client Revenue:</strong> Zero revenue sharing, zero royalties, zero recurring platform fees back to the original author.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span><strong>Modify &amp; Extend Code:</strong> Add custom integrations, connect local payment gateways (Stripe, Razorpay, UPI), or build new modules freely.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-check"></i>
                            <span><strong>Turnkey Client Installations:</strong> Deliver private standalone instances for individual hotel chains as custom turnkey projects.</span>
                        </li>
                    </ul>
                </div>

                <!-- STRICTLY PROHIBITED -->
                <div class="license-card prohibited reveal">
                    <div class="license-header">
                        <div class="license-icon-box"><i class="fa-solid fa-circle-xmark"></i></div>
                        <div>
                            <h3>Strictly PROHIBITED (Violations)</h3>
                            <span style="font-size:12px; color:#F87171; font-weight:700; text-transform:uppercase;">Strict IP Enforcement</span>
                        </div>
                    </div>
                    <ul class="license-list">
                        <li>
                            <i class="fa-solid fa-xmark"></i>
                            <span><strong>NO Reselling Raw Source Code:</strong> You cannot resell, distribute, or sub-license the raw source code files to third-party developers.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-xmark"></i>
                            <span><strong>NO Public Repositories:</strong> You may never upload the codebase to public GitHub, GitLab, Bitbucket, or open-source torrent networks.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-xmark"></i>
                            <span><strong>NO Code Marketplace Sales:</strong> Listing the code on Envato, CodeCanyon, Codester, Flippa, or similar marketplaces as code is strictly banned.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-xmark"></i>
                            <span><strong>Original Authorship Protected:</strong> Original authorship and foundational copyright remains with Patel Shubh (Patel-Shubh-07) unless an Exclusive IP Buyout contract is executed.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-xmark"></i>
                            <span><strong>Violators Subject to DMCA &amp; Legal Action:</strong> Unauthorized code redistribution will result in immediate license termination and copyright statutory damages.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="legal-notice-box reveal">
                <div class="notice-text">
                    <i class="fa-solid fa-shield-halved" style="color: #818CF8; margin-right: 8px;"></i>
                    <strong>Legal Protection Guarantee:</strong> Every purchase includes an official, counter-signed PDF Commercial License Agreement specifying your business name, domain whitelist, and authorized usage terms.
                </div>
                <a href="mailto:shubhmpatel77@gmail.com?subject=Request%20Sample%20Commercial%20License%20Agreement" class="btn-header-demo" style="border-color:#818CF8; color:#fff;">
                    <i class="fa-solid fa-envelope"></i> Request Sample Contract
                </a>
            </div>
        </div>
    </section>

    <!-- ─── PRICING TIERS WITH CURRENCY CONVERTER ─── -->
    <section class="section" id="pricing" style="background: rgba(12, 16, 34, 0.6);">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag"><i class="fa-solid fa-tags"></i> Direct Acquisition Packages</div>
                <h2 class="section-title">Select Your Acquisition Tier</h2>
                <p class="section-subtitle">
                    One-time investment. Zero recurring royalties. Delivered via private GitHub repository and download zip archive within 1 hour.
                </p>

                <!-- CURRENCY TOGGLE BUTTONS -->
                <div class="currency-switch-box">
                    <button class="currency-btn active" onclick="setCurrency('USD', '$', 1, '$4,999', '$6,999')">USD ($)</button>
                    <button class="currency-btn" onclick="setCurrency('INR', '₹', 80, '₹3,99,000', '₹5,50,000')">INR (₹)</button>
                    <button class="currency-btn" onclick="setCurrency('AED', 'AED ', 3.67, '18,300 AED', '25,600 AED')">AED (د.إ)</button>
                    <button class="currency-btn" onclick="setCurrency('EUR', '€', 0.92, '€4,599', '€6,450')">EUR (€)</button>
                </div>
            </div>

            <div class="pricing-grid">
                <!-- TIER 1: COMMERCIAL LICENSE -->
                <div class="pricing-card featured reveal">
                    <div class="featured-ribbon">🔥 Most Popular • 50% Off Launch Deal</div>
                    <div class="tier-name">Commercial License</div>
                    <div class="tier-tagline">Ideal for software agencies, startup founders, and technical entrepreneurs launching a SaaS.</div>
                    
                    <div class="tier-price-box">
                        <span class="price-curr" id="curr1">$</span>
                        <span class="price-val" id="price1">2,499</span>
                        <div>
                            <div class="price-original" id="orig1">$4,999</div>
                            <div class="price-term">One-time payment</div>
                        </div>
                    </div>

                    <ul class="tier-features">
                        <li><i class="fa-solid fa-check"></i> <strong>Complete Unencrypted Source Code</strong> (Laravel 11 + Vanilla JS + Electron)</li>
                        <li><i class="fa-solid fa-check"></i> <strong>All Modules Included:</strong> POS, QR Menu, Live KDS, Soundbox, Admin, Super Admin</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Non-Exclusive Commercial License</strong> for unlimited client restaurants</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Full White-Labeling Rights</strong> (Your Logo, Branding &amp; Domains)</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Clean Multi-Tenant Migrations</strong> &amp; Production Database Seeds</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Thermal Printer Drivers:</strong> 80mm/58mm ESC/POS &amp; A4 GST Invoice engines</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Comprehensive Setup Documentation</strong> &amp; Architecture README</li>
                        <li><i class="fa-solid fa-check"></i> <strong>6 Months Core Security</strong> &amp; Bug Fix Updates via GitHub</li>
                    </ul>

                    <div class="pricing-actions">
                        <a href="https://wa.me/917777938338?text=Hello%20Patel%20Shubh!%20I%20want%20to%20purchase%20the%20Commercial%20License%20for%20DineFlow%20AI." class="btn-pricing-primary" id="waBtn1" target="_blank">
                            <i class="fa-solid fa-bolt"></i> Buy Commercial License
                        </a>
                        <a href="mailto:shubhmpatel77@gmail.com?subject=Purchase%20Inquiry%20-%20Commercial%20License" class="btn-pricing-whatsapp">
                            <i class="fa-solid fa-envelope"></i> Inquire via Email
                        </a>
                    </div>
                </div>

                <!-- TIER 2: TURNKEY AGENCY SETUP -->
                <div class="pricing-card reveal">
                    <div class="tier-name">Turnkey Agency Setup</div>
                    <div class="tier-tagline">For agencies who want the core developer to deploy, configure, and brand everything hands-off.</div>
                    
                    <div class="tier-price-box">
                        <span class="price-curr" id="curr2">$</span>
                        <span class="price-val" id="price2">3,499</span>
                        <div>
                            <div class="price-original" id="orig2">$6,999</div>
                            <div class="price-term">One-time payment</div>
                        </div>
                    </div>

                    <ul class="tier-features">
                        <li><i class="fa-solid fa-check"></i> <strong>Everything in Commercial License</strong></li>
                        <li><i class="fa-solid fa-check"></i> <strong>AWS / Cloud VPS Deployment</strong> executed by core author Patel Shubh</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Custom Domain, SSL, &amp; Nginx</strong> Server Optimization</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Full Re-branding Applied:</strong> Custom Logo, App Title, Colors &amp; Favicons</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Payment Gateway Configured:</strong> Razorpay / Stripe / Custom UPI</li>
                        <li><i class="fa-solid fa-check"></i> <strong>2-Hour Dedicated Handover Call:</strong> Deep Architectural Zoom Walkthrough</li>
                        <li><i class="fa-solid fa-check"></i> <strong>1 Year Priority Updates</strong> &amp; Direct WhatsApp Support Channel</li>
                    </ul>

                    <div class="pricing-actions">
                        <a href="https://wa.me/917777938338?text=Hello%20Patel%20Shubh!%20I%20want%20the%20Turnkey%20Agency%20Setup%20with%20AWS%20Deployment." class="btn-pricing-primary" style="background: linear-gradient(135deg, #10B981, #059669);" target="_blank">
                            <i class="fa-solid fa-server"></i> Choose Turnkey Setup
                        </a>
                        <a href="https://wa.me/917777938338?text=Hello%20Shubh!%20I%20have%20questions%20about%20Turnkey%20Deployment." class="btn-pricing-whatsapp" target="_blank">
                            <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
                        </a>
                    </div>
                </div>

                <!-- TIER 3: EXCLUSIVE IP BUYOUT -->
                <div class="pricing-card reveal">
                    <div class="tier-name">Exclusive IP Buyout</div>
                    <div class="tier-tagline">100% exclusive copyright transfer. Platform is removed from the market permanently.</div>
                    
                    <div class="tier-price-box">
                        <span class="price-curr" id="curr3">$</span>
                        <span class="price-val" id="price3">19,999</span>
                        <div>
                            <div class="price-original">Valued $35K+</div>
                            <div class="price-term">Total Buyout</div>
                        </div>
                    </div>

                    <ul class="tier-features">
                        <li><i class="fa-solid fa-check"></i> <strong>100% Exclusive Ownership &amp; Full Copyright Transfer</strong></li>
                        <li><i class="fa-solid fa-check"></i> <strong>Code Removed from Market Permanently</strong> (No further copies sold)</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Complete Git History Transfer</strong> &amp; Repository Ownership Grant</li>
                        <li><i class="fa-solid fa-check"></i> <strong>Signed IP Assignment Contract &amp; Mutual NDA</strong></li>
                        <li><i class="fa-solid fa-check"></i> <strong>Full Freedom to Resell, License, or Patent</strong></li>
                        <li><i class="fa-solid fa-check"></i> <strong>30 Days Direct Developer Transition Period</strong> (Technical consultations)</li>
                        <li><i class="fa-solid fa-check"></i> <strong>All Raw Graphics, Figma/CSS Assets &amp; Architecture Blueprints</strong></li>
                    </ul>

                    <div class="pricing-actions">
                        <a href="https://wa.me/917777938338?text=Hello%20Patel%20Shubh!%20I%20am%20interested%20in%20the%20Exclusive%20IP%20Buyout%20for%20DineFlow%20AI." class="btn-pricing-primary" style="background: linear-gradient(135deg, #F59E0B, #D97706);" target="_blank">
                            <i class="fa-solid fa-crown"></i> Inquire for IP Buyout
                        </a>
                        <a href="mailto:shubhmpatel77@gmail.com?subject=Exclusive%20IP%20Buyout%20Inquiry" class="btn-pricing-whatsapp">
                            <i class="fa-solid fa-briefcase"></i> Legal Consultation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── INTERACTIVE MODULE SHOWCASE (TABS) ─── -->
    <section class="section" id="modules">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag"><i class="fa-solid fa-cubes"></i> Interactive Architecture Tour</div>
                <h2 class="section-title">Explore Production Modules</h2>
                <p class="section-subtitle">
                    Click through the core engine components to inspect their architecture and operational capabilities.
                </p>
            </div>

            <div class="module-showcase reveal">
                <div class="module-tabs">
                    <button class="module-tab-btn active" onclick="switchModuleTab('pos', this)">
                        <i class="fa-solid fa-cash-register"></i> Cloud POS Billing
                    </button>
                    <button class="module-tab-btn" onclick="switchModuleTab('qr', this)">
                        <i class="fa-solid fa-qrcode"></i> QR Table Menu
                    </button>
                    <button class="module-tab-btn" onclick="switchModuleTab('kds', this)">
                        <i class="fa-solid fa-kitchen-set"></i> Kitchen Display (KDS)
                    </button>
                    <button class="module-tab-btn" onclick="switchModuleTab('desktop', this)">
                        <i class="fa-brands fa-windows"></i> Desktop Offline App
                    </button>
                    <button class="module-tab-btn" onclick="switchModuleTab('admin', this)">
                        <i class="fa-solid fa-shield-halved"></i> Super Admin SaaS
                    </button>
                </div>

                <!-- TAB 1: POS -->
                <div class="module-tab-pane active" id="pane-pos">
                    <div class="module-info">
                        <h3>High-Speed Cloud POS Billing</h3>
                        <p>Optimized for high-volume cashier terminals. Supports touchscreen quick-add, split bills, multi-table orders, customer credit, daily Z-report reconciliation, and instant thermal printing.</p>
                        <ul class="module-checklist">
                            <li><i class="fa-solid fa-circle-check"></i> Sub-30ms table switching and order hold/resume</li>
                            <li><i class="fa-solid fa-circle-check"></i> Split billing by items or equally across guests</li>
                            <li><i class="fa-solid fa-circle-check"></i> Cash drawer kick-out pulse via RJ11 printer cable</li>
                            <li><i class="fa-solid fa-circle-check"></i> Dual KOT routing (Separate Kitchen vs Bar slips)</li>
                        </ul>
                    </div>
                    <div class="module-preview-card">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:10px;">
                            <span style="font-weight:800; color:#fff; font-size:14px;"><i class="fa-solid fa-receipt" style="color:#6366F1; margin-right:6px;"></i> Active Bill #DF-9281</span>
                            <span style="background:rgba(16,185,129,0.2); color:#34D399; font-size:11px; font-weight:800; padding:2px 8px; border-radius:6px;">TABLE 04 • DINE-IN</span>
                        </div>
                        <div style="font-size:13px; color:#CBD5E1; display:flex; flex-direction:column; gap:8px;">
                            <div style="display:flex; justify-content:space-between;"><span>2x Paneer Butter Masala</span><strong style="color:#fff;">₹560.00</strong></div>
                            <div style="display:flex; justify-content:space-between;"><span>4x Butter Naan</span><strong style="color:#fff;">₹180.00</strong></div>
                            <div style="display:flex; justify-content:space-between;"><span>1x Veg Biryani (Large)</span><strong style="color:#fff;">₹290.00</strong></div>
                            <div style="border-top:1px dashed rgba(255,255,255,0.15); margin-top:8px; padding-top:8px; display:flex; justify-content:space-between; font-weight:800; font-size:15px; color:#34D399;">
                                <span>Total Paid (UPI)</span>
                                <span>₹1,030.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: QR -->
                <div class="module-tab-pane" id="pane-qr">
                    <div class="module-info">
                        <h3>Contactless QR Table Ordering</h3>
                        <p>Zero-app customer mobile experience. Guests scan table QR code to view live digital menu, filter Veg/Non-Veg, listen to AI voice ordering, add cooking notes, and pay via instant UPI QR.</p>
                        <ul class="module-checklist">
                            <li><i class="fa-solid fa-circle-check"></i> Multi-language: English, Gujarati, and Hindi</li>
                            <li><i class="fa-solid fa-circle-check"></i> Web Speech API voice ordering assistant</li>
                            <li><i class="fa-solid fa-circle-check"></i> Real-time live order progress tracker</li>
                            <li><i class="fa-solid fa-circle-check"></i> Call Waiter &amp; Request Bill instant notification</li>
                        </ul>
                    </div>
                    <div class="module-preview-card">
                        <div style="text-align:center; padding:10px;">
                            <i class="fa-solid fa-mobile-screen" style="font-size:36px; color:#EC4899; margin-bottom:10px;"></i>
                            <h4 style="color:#fff; font-size:16px; margin-bottom:4px;">Guest Mobile Web App</h4>
                            <p style="font-size:13px; color:#94A3B8;">Zero download required. Opens instantly on iOS &amp; Android browsers.</p>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: KDS -->
                <div class="module-tab-pane" id="pane-kds">
                    <div class="module-info">
                        <h3>Live Kitchen Display System (KDS)</h3>
                        <p>Replaces paper clutter in the kitchen with high-contrast, real-time ticket cards. Features color-coded urgency countdowns, bump bar actions, audio alerts, and station filtering.</p>
                        <ul class="module-checklist">
                            <li><i class="fa-solid fa-circle-check"></i> Color-coded prep urgency (Green &lt; 5m, Amber &lt; 15m, Red &gt; 20m)</li>
                            <li><i class="fa-solid fa-circle-check"></i> Soundbox chime on new incoming tickets</li>
                            <li><i class="fa-solid fa-circle-check"></i> One-click ticket completion &amp; recall</li>
                            <li><i class="fa-solid fa-circle-check"></i> Touchscreen or physical bump bar keyboard shortcuts</li>
                        </ul>
                    </div>
                    <div class="module-preview-card">
                        <div style="background:#1E1B4B; border:1px solid #4338CA; border-radius:12px; padding:16px;">
                            <div style="display:flex; justify-content:space-between; color:#A5B4FC; font-size:12px; font-weight:700;">
                                <span>KOT #108 • Table 2</span>
                                <span style="color:#F59E0B;"><i class="fa-solid fa-clock"></i> 04:32</span>
                            </div>
                            <div style="color:#fff; font-weight:700; margin-top:8px;">1x Dal Makhani (No Onion)</div>
                            <div style="color:#fff; font-weight:700;">2x Tandoori Roti</div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: DESKTOP -->
                <div class="module-tab-pane" id="pane-desktop">
                    <div class="module-info">
                        <h3>Windows Desktop Offline Client</h3>
                        <p>Standalone Electron application for restaurant cashiers. Protects against internet outages with local SQLite offline buffering and background auto-sync to the cloud.</p>
                        <ul class="module-checklist">
                            <li><i class="fa-solid fa-circle-check"></i> Operates 100% offline during internet failures</li>
                            <li><i class="fa-solid fa-circle-check"></i> Native USB &amp; Network ESC/POS thermal printing</li>
                            <li><i class="fa-solid fa-circle-check"></i> Background bidirectional cloud synchronization</li>
                            <li><i class="fa-solid fa-circle-check"></i> Auto-updates via GitHub releases</li>
                        </ul>
                    </div>
                    <div class="module-preview-card">
                        <div style="text-align:center; padding:10px;">
                            <i class="fa-brands fa-windows" style="font-size:38px; color:#38BDF8; margin-bottom:10px;"></i>
                            <h4 style="color:#fff; font-size:16px;">Electron Native Shell</h4>
                            <p style="font-size:13px; color:#94A3B8;">Pre-configured Windows setup package (`setup.exe`) included.</p>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: ADMIN -->
                <div class="module-tab-pane" id="pane-admin">
                    <div class="module-info">
                        <h3>Multi-Tenant Super Admin SaaS HQ</h3>
                        <p>Global command center to operate your software company. Provision new restaurants, set subscription plans, monitor platform MRR, and automate suspension after grace period.</p>
                        <ul class="module-checklist">
                            <li><i class="fa-solid fa-circle-check"></i> Complete tenant database isolation</li>
                            <li><i class="fa-solid fa-circle-check"></i> Automated subscription expiry cron scheduler</li>
                            <li><i class="fa-solid fa-circle-check"></i> Global platform revenue &amp; order volume metrics</li>
                            <li><i class="fa-solid fa-circle-check"></i> One-click tenant suspension &amp; reactivation</li>
                        </ul>
                    </div>
                    <div class="module-preview-card">
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            <div style="background:rgba(255,255,255,0.05); padding:12px; border-radius:10px; display:flex; justify-content:space-between;">
                                <span>Platform MRR</span><strong style="color:#34D399;">$14,850.00</strong>
                            </div>
                            <div style="background:rgba(255,255,255,0.05); padding:12px; border-radius:10px; display:flex; justify-content:space-between;">
                                <span>Active Tenants</span><strong style="color:#60A5FA;">142 Restaurants</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── TECH STACK ─── -->
    <section class="section" id="tech-specs" style="background: rgba(12, 16, 34, 0.6);">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag"><i class="fa-solid fa-microchip"></i> Modern Engineering</div>
                <h2 class="section-title">Built with Clean, Enterprise Tech</h2>
                <p class="section-subtitle">
                    Zero bloated frameworks. Engineered for sub-50ms query response times and effortless deployment on standard Linux VPS hosting.
                </p>
            </div>

            <div class="tech-box reveal">
                <div class="tech-col">
                    <h4><i class="fa-brands fa-laravel"></i> Backend Core</h4>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> PHP 8.2+ / Laravel 11 framework</li>
                        <li><i class="fa-solid fa-check"></i> RESTful JSON APIs &amp; CSRF protection</li>
                        <li><i class="fa-solid fa-check"></i> Concurrency locking (<code>lockForUpdate</code>)</li>
                        <li><i class="fa-solid fa-check"></i> Multi-tenant middleware scoping</li>
                        <li><i class="fa-solid fa-check"></i> Automated daily cron task scheduler</li>
                    </ul>
                </div>

                <div class="tech-col">
                    <h4><i class="fa-brands fa-js"></i> Frontend UX</h4>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> Pure Vanilla JavaScript (Ultra-fast)</li>
                        <li><i class="fa-solid fa-check"></i> Zero heavy Node/React build steps</li>
                        <li><i class="fa-solid fa-check"></i> Fluid CSS3 Design &amp; Dark/Light modes</li>
                        <li><i class="fa-solid fa-check"></i> Web Speech API voice ordering</li>
                        <li><i class="fa-solid fa-check"></i> High-contrast accessibility compliant</li>
                    </ul>
                </div>

                <div class="tech-col">
                    <h4><i class="fa-solid fa-database"></i> Storage &amp; Cloud</h4>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> MySQL 8.0 / MariaDB / PostgreSQL</li>
                        <li><i class="fa-solid fa-check"></i> AWS EC2 / RDS / S3 compatible</li>
                        <li><i class="fa-solid fa-check"></i> Nginx &amp; Apache web server configs</li>
                        <li><i class="fa-solid fa-check"></i> Redis cache and session support</li>
                        <li><i class="fa-solid fa-check"></i> Automated database backup ready</li>
                    </ul>
                </div>

                <div class="tech-col">
                    <h4><i class="fa-brands fa-windows"></i> Desktop &amp; Hardware</h4>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> Electron.js standalone Windows wrapper</li>
                        <li><i class="fa-solid fa-check"></i> ESC/POS thermal printer engine</li>
                        <li><i class="fa-solid fa-check"></i> Cash drawer kick-out via RJ11 pulse</li>
                        <li><i class="fa-solid fa-check"></i> Local offline storage fallback</li>
                        <li><i class="fa-solid fa-check"></i> Hardware barcode / QR scanner input</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── HOW PURCHASE WORKS ─── -->
    <section class="section">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag"><i class="fa-solid fa-handshake"></i> Simple 4-Step Acquisition</div>
                <h2 class="section-title">How to Complete Your Purchase</h2>
                <p class="section-subtitle">
                    Straightforward, secure, and rapid code transfer within 1 hour of confirmation.
                </p>
            </div>

            <div class="process-grid">
                <div class="process-card reveal">
                    <div class="step-num">01</div>
                    <h4>Choose Your License</h4>
                    <p>Select Commercial ($2,499), Turnkey ($3,499), or Buyout ($19,999) based on your roadmap.</p>
                </div>

                <div class="process-card reveal">
                    <div class="step-num">02</div>
                    <h4>Contact on WhatsApp</h4>
                    <p>Message directly at <strong>+91 7777938338</strong> or email <strong>shubhmpatel77@gmail.com</strong> with your preferred package.</p>
                </div>

                <div class="process-card reveal">
                    <div class="step-num">03</div>
                    <h4>Secure Payment &amp; Contract</h4>
                    <p>Sign the Commercial Agreement and send payment via Wire, Wise, USDT (Crypto), Stripe, or Bank Transfer.</p>
                </div>

                <div class="process-card reveal">
                    <div class="step-num">04</div>
                    <h4>Instant Repository Invite</h4>
                    <p>Receive immediate GitHub private repository collaborator invite, complete zip package, and setup guide.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FAQ ACCORDION ─── -->
    <section class="section" id="faq" style="background: rgba(12, 16, 34, 0.6);">
        <div class="container">
            <div class="section-header reveal">
                <div class="section-tag"><i class="fa-solid fa-circle-question"></i> Common Questions</div>
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-subtitle">Everything you need to know about purchasing and commercializing the code.</p>
            </div>

            <div class="faq-grid reveal">
                <div class="faq-item active">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span><i class="fa-solid fa-circle-info" style="color:#818CF8; margin-right:8px;"></i> Is the source code fully unencrypted?</span>
                        <i class="fa-solid fa-chevron-down faq-chevron"></i>
                    </div>
                    <div class="faq-answer">
                        Yes, 100%. There are zero IonCube loaders, zero obfuscated scripts, and no external DRM callbacks. You receive the exact, clean PHP and JavaScript code as written by the engineering team.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span><i class="fa-solid fa-circle-info" style="color:#818CF8; margin-right:8px;"></i> Can I charge my restaurant clients any subscription price?</span>
                        <i class="fa-solid fa-chevron-down faq-chevron"></i>
                    </div>
                    <div class="faq-answer">
                        Absolutely. You can charge $49/mo, $99/mo, $299/mo or setup one-time turnkey fees. You keep 100% of all customer revenues with zero royalties back to us.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span><i class="fa-solid fa-circle-info" style="color:#818CF8; margin-right:8px;"></i> Can I resell or distribute the source code itself?</span>
                        <i class="fa-solid fa-chevron-down faq-chevron"></i>
                    </div>
                    <div class="faq-answer">
                        No. Under the Non-Exclusive Commercial License, you have full rights to deploy, white-label, and sell subscriptions as an operating SaaS, but you CANNOT sell, re-distribute, or open-source the raw code itself to other programmers. If you require exclusive rights to the code, choose the <strong>Exclusive IP Buyout</strong> package.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span><i class="fa-solid fa-circle-info" style="color:#818CF8; margin-right:8px;"></i> What payment methods do you accept?</span>
                        <i class="fa-solid fa-chevron-down faq-chevron"></i>
                    </div>
                    <div class="faq-answer">
                        We accept International Bank Wire Transfer (USD / EUR / GBP / INR), Wise (formerly TransferWise), USDT / USDC (Crypto), Stripe invoice, and domestic UPI / NEFT.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFaq(this)">
                        <span><i class="fa-solid fa-circle-info" style="color:#818CF8; margin-right:8px;"></i> How difficult is it to deploy on a VPS?</span>
                        <i class="fa-solid fa-chevron-down faq-chevron"></i>
                    </div>
                    <div class="faq-answer">
                        Very simple. A standard Ubuntu 22.04 or 24.04 VPS with PHP 8.2 and MySQL is all that is required. If you choose the <strong>Turnkey Agency Setup</strong>, our core author Patel Shubh will handle the entire AWS/VPS deployment for you free of hassle.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FOOTER ─── -->
    <footer class="footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-brand">
                    <a href="/" class="brand-logo">
                        <div class="brand-icon"><i class="fa-solid fa-utensils"></i></div>
                        <div class="brand-name">
                            <span>Tendorabuy</span>
                            <span class="brand-badge">POS</span>
                        </div>
                    </a>
                    <p style="font-size: 13.5px; color: var(--text-secondary); max-width: 440px; margin-top: 8px;">
                        Enterprise restaurant software ecosystem. Table QR ordering, cloud POS billing, live KDS, thermal printing, and multi-tenant SaaS management.
                    </p>
                </div>

                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/demo">Sandbox Demo</a></li>
                    <li><a href="/buy-source-code">Buy Source Code</a></li>
                    <li><a href="/privacy">Privacy Policy</a></li>
                    <li><a href="/terms">Terms of Service</a></li>
                    <li><a href="/contact">Contact</a></li>
                </ul>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; {{ date('Y') }} Tendorabuy / DineFlow AI. Created &amp; Engineered by <strong>Patel Shubh</strong> (<code>Patel-Shubh-07</code>).
                </div>
                <div style="display: flex; gap: 18px; align-items: center;">
                    <a href="mailto:shubhmpatel77@gmail.com" style="color: #A5B4FC; text-decoration: none;"><i class="fa-solid fa-envelope"></i> shubhmpatel77@gmail.com</a>
                    <a href="https://wa.me/917777938338" style="color: #4ADE80; text-decoration: none;" target="_blank"><i class="fa-brands fa-whatsapp"></i> +91 7777938338</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ─── JAVASCRIPT ANIMATIONS & INTERACTIONS ─── -->
    <script>
        // 1. Scroll Reveal Observer
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.15 });

            reveals.forEach(el => observer.observe(el));

            // Animated Number Counter
            const counters = document.querySelectorAll('.counter');
            let counted = false;
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !counted) {
                        counted = true;
                        counters.forEach(counter => {
                            const target = +counter.getAttribute('data-target');
                            let count = 0;
                            const speed = target === 0 ? 1 : Math.max(1, Math.floor(target / 40));
                            const timer = setInterval(() => {
                                count += speed;
                                if (count >= target) {
                                    counter.innerText = target;
                                    clearInterval(timer);
                                } else {
                                    counter.innerText = count;
                                }
                            }, 25);
                        });
                    }
                });
            }, { threshold: 0.5 });

            const statsStrip = document.querySelector('.stats-strip');
            if (statsStrip) counterObserver.observe(statsStrip);
        });

        // 2. Navbar Scroll Glassmorphism
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('topNavbar');
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // 3. Interactive Code Snippet Tabs
        const snippets = {
            order: `<span class="code-comment">// High-Concurrency Transaction with Atomic lockForUpdate</span>
<span class="code-keyword">public function</span> <span class="code-fn">createOrder</span>(array <span class="code-var">$payload</span>, <span class="code-keyword">int</span> <span class="code-var">$tenantId</span>): Order 
{
    <span class="code-keyword">return</span> DB::<span class="code-fn">transaction</span>(<span class="code-keyword">function</span> () <span class="code-keyword">use</span> (<span class="code-var">$payload</span>, <span class="code-var">$tenantId</span>) {
        <span class="code-comment">// Strict tenant isolation &amp; atomic order counter</span>
        <span class="code-var">$table</span> = Table::<span class="code-fn">where</span>(<span class="code-str">'restaurant_id'</span>, <span class="code-var">$tenantId</span>)
                      -><span class="code-fn">lockForUpdate</span>()
                      -><span class="code-fn">findOrFail</span>(<span class="code-var">$payload</span>[<span class="code-str">'table_id'</span>]);

        <span class="code-var">$order</span> = Order::<span class="code-fn">create</span>([
            <span class="code-str">'restaurant_id'</span> => <span class="code-var">$tenantId</span>,
            <span class="code-str">'table_id'</span>      => <span class="code-var">$table</span>->id,
            <span class="code-str">'total_amount'</span>  => <span class="code-var">$payload</span>[<span class="code-str">'grand_total'</span>],
            <span class="code-str">'status'</span>        => <span class="code-str">'received'</span>
        ]);

        <span class="code-comment">// Dispatch real-time KOT to kitchen printer &amp; KDS socket</span>
        event(<span class="code-keyword">new</span> <span class="code-fn">NewOrderPlaced</span>(<span class="code-var">$order</span>));
        <span class="code-keyword">return</span> <span class="code-var">$order</span>;
    });
}`,
            pos: `<span class="code-comment">// POS Fast Checkout Engine with Soundbox Alert</span>
<span class="code-keyword">async function</span> <span class="code-fn">processPosCheckout</span>(cart, tableId, paymentMode) {
    <span class="code-var">const</span> response = <span class="code-keyword">await</span> fetch(<span class="code-str">'/api/orders'</span>, {
        method: <span class="code-str">'POST'</span>,
        headers: {
            <span class="code-str">'Content-Type'</span>: <span class="code-str">'application/json'</span>,
            <span class="code-str">'X-CSRF-TOKEN'</span>: getCsrfToken()
        },
        body: JSON.stringify({ items: cart.items, table_id: tableId, payment_mode: paymentMode })
    });
    <span class="code-var">const</span> data = <span class="code-keyword">await</span> response.json();
    <span class="code-keyword">if</span> (data.success) {
        playPaymentChime();
        printThermalReceipt(data.order_id);
        showPosToast(<span class="code-str">'Order settled successfully!'</span>);
    }
}`,
            printer: `<span class="code-comment">// ESC/POS Raw Thermal Printer Driver (80mm / 58mm)</span>
<span class="code-keyword">public function</span> <span class="code-fn">printReceipt</span>(Order <span class="code-var">$order</span>): void 
{
    <span class="code-var">$connector</span> = <span class="code-keyword">new</span> WindowsPrintConnector(<span class="code-var">$this</span>->printerShareName);
    <span class="code-var">$printer</span>   = <span class="code-keyword">new</span> Printer(<span class="code-var">$connector</span>);

    <span class="code-var">$printer</span>-><span class="code-fn">setJustification</span>(Printer::JUSTIFY_CENTER);
    <span class="code-var">$printer</span>-><span class="code-fn">text</span>(<span class="code-var">$order</span>->restaurant->name . <span class="code-str">"\n"</span>);
    <span class="code-var">$printer</span>-><span class="code-fn">text</span>(<span class="code-str">"TAX INVOICE / CASH BILL\n"</span>);
    <span class="code-var">$printer</span>-><span class="code-fn">feed</span>(<span class="code-num">1</span>);

    <span class="code-comment">// Pulse kick-out cash drawer</span>
    <span class="code-var">$printer</span>-><span class="code-fn">pulse</span>();
    <span class="code-var">$printer</span>-><span class="code-fn">cut</span>();
    <span class="code-var">$printer</span>-><span class="code-fn">close</span>();
}`
        };

        function switchCodeTab(tab) {
            document.querySelectorAll('.terminal-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            const codeEl = document.getElementById('codeSnippet');
            if (codeEl && snippets[tab]) {
                codeEl.innerHTML = snippets[tab];
            }
        }

        // 4. Interactive Module Showcase Tabs
        function switchModuleTab(paneId, btn) {
            document.querySelectorAll('.module-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.module-tab-pane').forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const targetPane = document.getElementById('pane-' + paneId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        }

        // 5. Dynamic Currency Switcher
        function setCurrency(currCode, symbol, rate, orig1, orig2) {
            document.querySelectorAll('.currency-btn').forEach(b => b.classList.remove('active'));
            event.target.classList.add('active');

            const p1 = Math.round(2499 * rate).toLocaleString();
            const p2 = Math.round(3499 * rate).toLocaleString();
            const p3 = Math.round(19999 * rate).toLocaleString();

            document.getElementById('curr1').innerText = symbol;
            document.getElementById('curr2').innerText = symbol;
            document.getElementById('curr3').innerText = symbol;

            document.getElementById('price1').innerText = p1;
            document.getElementById('price2').innerText = p2;
            document.getElementById('price3').innerText = p3;

            document.getElementById('orig1').innerText = orig1;
            document.getElementById('orig2').innerText = orig2;

            const wa1 = document.getElementById('waBtn1');
            if (wa1) {
                wa1.href = `https://wa.me/917777938338?text=Hello%20Patel%20Shubh!%20I%20want%20to%20purchase%20the%20Commercial%20License%20(${symbol}${p1})%20for%20DineFlow%20AI.`;
            }
        }

        // 6. Interactive FAQ Accordion
        function toggleFaq(element) {
            const item = element.parentElement;
            const isActive = item.classList.contains('active');
            document.querySelectorAll('.faq-item').forEach(f => f.classList.remove('active'));
            if (!isActive) {
                item.classList.add('active');
            }
        }
    </script>

</body>
</html>