<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 — Too Many Requests | DineFlow AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0B0B0D; color: #F4F4F5; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; text-align: center; }
        .error-card { background: #151517; border: 1px solid #26262A; border-radius: 24px; padding: 48px 32px; max-width: 480px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.6); }
        .error-code { font-family: 'Outfit', sans-serif; font-size: 72px; font-weight: 900; color: #FB7185; line-height: 1; margin-bottom: 12px; }
        h1 { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; margin-bottom: 8px; color: #fff; }
        p { color: #A1A1AA; font-size: 14px; margin-bottom: 28px; line-height: 1.5; }
        .btn-home { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #9F1239, #BE123C); color: #fff; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 16px rgba(159,18,57,0.4); }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">429</div>
        <h1>Rate Limit Exceeded</h1>
        <p>You have submitted too many requests in a short period. Please wait 60 seconds before trying again.</p>
        <a href="/" class="btn-home"><i class="fa-solid fa-house"></i> Return to Homepage</a>
    </div>
</body>
</html>
