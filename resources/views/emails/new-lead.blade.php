<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Lead Notification</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 24px; color: #1e293b; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #4f46e5, #6366f1); padding: 24px; color: #ffffff; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; font-weight: 800; }
        .header p { margin: 6px 0 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 24px; }
        .field { margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
        .field:last-child { border-bottom: none; }
        .label { font-size: 12px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
        .value { font-size: 15px; font-weight: 600; color: #0f172a; margin-top: 4px; }
        .btn-wa { display: inline-block; background-color: #10b981; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px; margin-top: 16px; }
        .footer { background: #f8fafc; padding: 16px 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>🚀 New Lead Received!</h1>
            <p>A new enquiry was submitted via Tendorabuy website.</p>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Restaurant / Business Name</div>
                <div class="value">{{ $lead->restaurant_name }}</div>
            </div>
            <div class="field">
                <div class="label">Contact Person</div>
                <div class="value">{{ $lead->name }}</div>
            </div>
            <div class="field">
                <div class="label">Phone / WhatsApp</div>
                <div class="value">
                    <a href="tel:{{ $lead->phone }}" style="color:#4f46e5; text-decoration:none;">{{ $lead->phone }}</a>
                </div>
            </div>
            @if($lead->email)
            <div class="field">
                <div class="label">Email</div>
                <div class="value">
                    <a href="mailto:{{ $lead->email }}" style="color:#4f46e5; text-decoration:none;">{{ $lead->email }}</a>
                </div>
            </div>
            @endif
            @if($lead->city)
            <div class="field">
                <div class="label">City / Location</div>
                <div class="value">{{ $lead->city }}</div>
            </div>
            @endif
            @if($lead->preferred_time)
            <div class="field">
                <div class="label">Preferred Demo Time / Slot</div>
                <div class="value">{{ $lead->preferred_time }}</div>
            </div>
            @endif
            @if($lead->message)
            <div class="field">
                <div class="label">Message / Notes</div>
                <div class="value" style="font-weight: 400; line-height: 1.5;">{{ $lead->message }}</div>
            </div>
            @endif
            <div class="field">
                <div class="label">Source / Submitted At</div>
                <div class="value">{{ ucfirst(str_replace('_', ' ', $lead->source ?? 'Website')) }} &bull; {{ $lead->created_at->format('d M Y, h:i A') }}</div>
            </div>

            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $lead->phone);
                if (strlen($cleanPhone) === 10) {
                    $cleanPhone = '91' . $cleanPhone;
                }
                $waText = urlencode("Hello " . $lead->name . ", thank you for reaching out to Tendorabuy regarding your restaurant " . $lead->restaurant_name . ". How can we assist you today?");
            @endphp
            <div style="text-align: center; margin-top: 20px;">
                <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" class="btn-wa" target="_blank">
                    💬 Chat with {{ $lead->name }} on WhatsApp
                </a>
            </div>
        </div>
        <div class="footer">
            Tendorabuy Lead Notification System &bull; Automatic Notification
        </div>
    </div>
</body>
</html>
