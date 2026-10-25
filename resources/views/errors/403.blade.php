<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Forbidden | DineFlow AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #090D1A; color: #F8FAFC; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; text-align: center; }
        .error-card { background: #131A35; border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 48px 32px; max-width: 480px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .error-code { font-family: 'Outfit', sans-serif; font-size: 72px; font-weight: 900; color: #EF4444; line-height: 1; margin-bottom: 12px; }
        h1 { font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; margin-bottom: 8px; color: #fff; }
        p { color: #94A3B8; font-size: 14px; margin-bottom: 28px; line-height: 1.5; }
        .btn-home { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: linear-gradient(135deg, #6366F1, #4338CA); color: #fff; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 16px rgba(99,102,241,0.4); }
    </style>
</head>
<body>
    @php
        // SAFETY NET: If the authenticated user belongs to a suspended/expired restaurant,
        // redirect them to the professional subscription status page instead of showing raw 403.
        $authUser = auth()->user();
        if ($authUser && $authUser->restaurant_id && strtolower(trim($authUser->role ?? '')) !== 'superadmin') {
            $restaurant403 = \App\Models\Restaurant::find($authUser->restaurant_id);
            if ($restaurant403) {
                $restStatus403 = strtolower(trim($restaurant403->subscription_status ?? 'active'));
                if (in_array($restStatus403, ['suspended', 'expired', 'cancelled'])) {
                    header('Location: /subscription/status');
                    exit;
                }
            }
        }
    @endphp
    <div class="error-card">
        <div class="error-code">403</div>
        <h1>Access Denied / Forbidden</h1>
        <p>You do not have permission or tenant authorization to view this restaurant's workspace.</p>
        <a href="/login" class="btn-home"><i class="fa-solid fa-lock"></i> Re-authenticate</a>
    </div>
</body>
</html>
