<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Privacy Policy — DineFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0B0B0D;
            color: #F4F4F5;
            padding: clamp(30px, 5vw, 60px) clamp(16px, 4vw, 24px);
            line-height: 1.7;
            width: 100%;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-y;
        }
        .container { max-width: 800px; margin: 0 auto; width: 100%; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #FB7185;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 8px;
            background: rgba(159, 18, 57, 0.12);
            border: 1px solid rgba(251, 113, 133, 0.3);
            margin-bottom: 24px;
            transition: all 0.2s ease;
            min-height: 40px;
        }
        .back-link:hover { background: rgba(159, 18, 57, 0.22); color: #fff; }
        h1 { font-family: 'Outfit', sans-serif; color: #fff; font-size: clamp(24px, 4vw, 36px); margin: 12px 0 6px; }
        .updated-date { color: #71717A; font-size: 13px; margin-bottom: 24px; }
        h2 { font-family: 'Outfit', sans-serif; color: #fff; font-size: clamp(18px, 2.5vw, 22px); margin: 28px 0 10px; }
        p { color: #A1A1AA; margin-bottom: 16px; font-size: 14.5px; line-height: 1.65; }
        a { color: #FB7185; text-decoration: none; }
        a:hover { text-decoration: underline; color: #FDA4AF; }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to DineFlow</a>
        <h1>Privacy Policy</h1>
        <p class="updated-date">Last updated: August 2026</p>
        <h2>1. Information We Collect</h2>
        <p>We collect restaurant business details (name, email, phone number) provided during registration, along with dining orders placed via digital QR codes.</p>
        <h2>2. How We Protect Your Data</h2>
        <p>All passwords are encrypted with bcrypt hashing. Multi-tenant data segregation ensures no other restaurant can view or modify your data.</p>
        <h2>3. Data Ownership</h2>
        <p>You retain 100% ownership of your customer lists, sales reports, and menu catalog.</p>
        <h2>4. Contact</h2>
        <p>Questions regarding our privacy practices? Contact us at <a href="/contact">our contact page</a> or email <a href="mailto:privacy@dineflowai.duckdns.org">privacy@dineflowai.duckdns.org</a>.</p>
    </div>
</body>
</html>
