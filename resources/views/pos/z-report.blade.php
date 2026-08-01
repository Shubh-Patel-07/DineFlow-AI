@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daily Sales Z-Report | DineFlow AI</title>
  <script>
    (function() {
      var t = localStorage.getItem('posTheme') || localStorage.getItem('dineflow_theme');
      if (t === 'light') {
        document.documentElement.classList.add('light-theme');
      }
      document.addEventListener('DOMContentLoaded', function() {
        if (t === 'light' && document.body) {
          document.body.classList.add('light-theme');
        }
      });
    })();
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root {
      --bg: #0F172A;
      --card: #1E293B;
      --border: #334155;
      --primary: #6366F1;
      --success: #10B981;
      --text: #F8FAFC;
      --muted: #94A3B8;
      --input-bg: rgba(255,255,255,0.08);
      --stat-bg: rgba(255,255,255,0.04);
      --th-bg: rgba(255,255,255,0.05);
    }
    html.light-theme, body.light-theme {
      --bg: #F8FAFC !important;
      --card: #FFFFFF !important;
      --border: #E2E8F0 !important;
      --text: #0F172A !important;
      --muted: #64748B !important;
      --input-bg: #F1F5F9 !important;
      --stat-bg: #F8FAFC !important;
      --th-bg: #F1F5F9 !important;
    }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); padding: 24px; transition: all 0.2s; }
    .z-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 24px; max-width: 900px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .z-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 24px; }
    .z-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .z-stat-box { background: var(--stat-bg); border: 1px solid var(--border); border-radius: 12px; padding: 16px; }
    .z-stat-box label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .z-stat-box val { display: block; font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: var(--success); margin-top: 4px; }
    .z-table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
    .z-table th, .z-table td { padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border); color: var(--text); }
    .z-table th { background: var(--th-bg); color: var(--muted); font-weight: 600; text-transform: uppercase; font-size: 11px; }
    @media print {
      body { background: white !important; color: black !important; padding: 0; }
      .z-card { border: none; box-shadow: none; max-width: 100%; }
      .no-print { display: none !important; }
      .z-stat-box { border: 1px solid #ccc; color: black; }
      .z-stat-box val { color: black; }
    }
  </style>
</head>
<body>
  <div class="z-card">
    <div class="z-header">
      <div>
        <h1 style="font-family:'Outfit',sans-serif; font-size:22px; margin:0 0 4px; color:var(--text);">📊 Daily Sales Z-Report</h1>
        <p style="margin:0; font-size:12px; color:var(--muted);">End of Day Financial Summary — {{ \Carbon\Carbon::parse($reportDate)->format('d M Y') }}</p>
      </div>
      <div class="no-print" style="display:flex; gap:10px;">
        <button onclick="window.print()" style="padding:10px 18px; background:var(--primary); border:none; border-radius:8px; color:white; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(99,102,241,0.3);">
          <i class="fa-solid fa-print"></i> Print Z-Report
        </button>
        <a href="/pos" style="padding:10px 18px; background:var(--input-bg); border:1px solid var(--border); border-radius:8px; color:var(--text); font-weight:600; text-decoration:none;">Back to POS</a>
      </div>
    </div>

    <!-- Date Selector Form (No print) -->
    <form method="GET" action="/z-report" class="no-print" style="margin-bottom:24px; display:flex; gap:12px; align-items:center;">
      <label style="font-size:13px; font-weight:600; color:var(--text);">Report Date:</label>
      <input type="date" name="date" value="{{ $reportDate }}" onchange="this.form.submit()" style="padding:8px 12px; background:var(--input-bg); border:1px solid var(--border); border-radius:8px; color:var(--text); font-weight:600; outline:none;">
    </form>

    <!-- Key Metrics Grid -->
    <div class="z-grid">
      <div class="z-stat-box">
        <label>Total Gross Revenue</label>
        <val>₹{{ number_format($grossRevenue, 2) }}</val>
      </div>
      <div class="z-stat-box">
        <label>Net Subtotal</label>
        <val style="color:var(--text);">₹{{ number_format($netSubtotal, 2) }}</val>
      </div>
      <div class="z-stat-box">
        <label>Taxes Collected (GST 18%)</label>
        <val style="color:#F59E0B;">₹{{ number_format($taxTotal, 2) }}</val>
      </div>
      <div class="z-stat-box">
        <label>Total Completed Orders</label>
        <val style="color:#6366F1;">{{ $totalOrdersCount }}</val>
      </div>
    </div>

    <!-- Payment Breakdown -->
    <h3 style="font-family:'Outfit',sans-serif; font-size:16px; margin:20px 0 10px;">💳 Payment Method Breakdown</h3>
    <table class="z-table">
      <thead>
        <tr>
          <th>Payment Method</th>
          <th>Transactions</th>
          <th>Total Collected</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><i class="fa-solid fa-money-bill" style="color:#10B981; margin-right:8px;"></i> Cash</td>
          <td>{{ $paymentSplit['cash']['count'] ?? 0 }}</td>
          <td style="font-weight:700;">₹{{ number_format($paymentSplit['cash']['amount'] ?? 0, 2) }}</td>
        </tr>
        <tr>
          <td><i class="fa-solid fa-qrcode" style="color:#6366F1; margin-right:8px;"></i> UPI / Online</td>
          <td>{{ $paymentSplit['online']['count'] ?? 0 }}</td>
          <td style="font-weight:700;">₹{{ number_format($paymentSplit['online']['amount'] ?? 0, 2) }}</td>
        </tr>
        <tr>
          <td><i class="fa-solid fa-credit-card" style="color:#F59E0B; margin-right:8px;"></i> Card</td>
          <td>{{ $paymentSplit['card']['count'] ?? 0 }}</td>
          <td style="font-weight:700;">₹{{ number_format($paymentSplit['card']['amount'] ?? 0, 2) }}</td>
        </tr>
      </tbody>
    </table>

    <!-- Category Sales -->
    <h3 style="font-family:'Outfit',sans-serif; font-size:16px; margin:28px 0 10px;">🍕 Category-Wise Sales Distribution</h3>
    <table class="z-table">
      <thead>
        <tr>
          <th>Category</th>
          <th>Items Sold</th>
          <th>Revenue Generated</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categoryBreakdown as $cat)
        <tr>
          <td style="font-weight:600;">{{ $cat->category }}</td>
          <td>{{ $cat->total_qty }}</td>
          <td style="font-weight:700; color:var(--success);">₹{{ number_format($cat->total_sales, 2) }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="3" style="text-align:center; color:var(--muted);">No sales recorded for this date</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <script src="/frontend/admin/utils.js"></script>
</body>
</html>
@endsection
