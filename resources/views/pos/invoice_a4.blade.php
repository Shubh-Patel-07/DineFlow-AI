<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TAX INVOICE - Order #{{ $order->id }} - DineFlow AI</title>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', sans-serif;
      background: #F1F5F9;
      color: #1E293B;
      padding: 30px 15px;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    /* Action Bar (Screen Only) */
    .action-bar {
      max-width: 800px;
      margin: 0 auto 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #1E293B;
      padding: 12px 20px;
      border-radius: 14px;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
    }
    .action-bar .title { color: #F8FAFC; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; }
    .action-btns { display: flex; gap: 10px; }
    .btn-action {
      padding: 8px 16px; border-radius: 8px; border: none; font-size: 12px; font-weight: 700;
      cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; text-decoration: none;
    }
    .btn-print { background: linear-gradient(135deg, #6366F1, #4F46E5); color: #fff; }
    .btn-whatsapp { background: #25D366; color: #fff; }
    .btn-close { background: rgba(255,255,255,0.1); color: #94A3B8; }
    .btn-action:hover { transform: translateY(-1px); filter: brightness(1.1); }

    /* A4 Container */
    .invoice-card {
      max-width: 800px;
      margin: 0 auto;
      background: #FFFFFF;
      border-radius: 16px;
      box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1);
      padding: 28px 32px;
      position: relative;
    }

    /* Invoice Header */
    .header-grid {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #E2E8F0;
      padding-bottom: 24px;
      margin-bottom: 28px;
    }
    .brand-section { display: flex; align-items: center; gap: 16px; }
    .brand-logo { width: 64px; height: 64px; border-radius: 12px; object-fit: cover; border: 1px solid #E2E8F0; }
    .brand-info h1 { font-family: 'Outfit', sans-serif; font-size: 24px; font-weight: 800; color: #0F172A; }
    .brand-info p { font-size: 12px; color: #64748B; margin-top: 2px; }

    .invoice-title-badge { text-align: right; }
    .invoice-title-badge h2 { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; color: #4F46E5; letter-spacing: 1px; }
    .invoice-title-badge .inv-no { font-size: 13px; font-weight: 700; color: #0F172A; margin-top: 4px; }
    .invoice-title-badge .inv-date { font-size: 11px; color: #64748B; margin-top: 2px; }

    /* Info Meta Section */
    .meta-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      background: #F8FAFC;
      border-radius: 12px;
      padding: 18px 22px;
      border: 1px solid #E2E8F0;
      margin-bottom: 28px;
    }
    .meta-box h4 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #64748B; margin-bottom: 6px; font-weight: 700; }
    .meta-box p { font-size: 13px; color: #0F172A; font-weight: 600; line-height: 1.5; }

    .badge-status {
      display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase;
    }
    .badge-paid { background: #DCFCE7; color: #15803D; border: 1px solid #86EFAC; }
    .badge-pending { background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; }

    /* Table */
    .table-container { margin-bottom: 28px; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th {
      background: #0F172A; color: #FFFFFF; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
      padding: 12px 16px; text-align: left;
    }
    .items-table th:first-child { border-top-left-radius: 8px; }
    .items-table th:last-child { border-top-right-radius: 8px; text-align: right; }
    .items-table td {
      padding: 12px 16px; font-size: 13px; color: #334155; border-bottom: 1px solid #E2E8F0; font-weight: 500;
    }
    .items-table tr:nth-child(even) { background: #F8FAFC; }
    .items-table .text-right { text-align: right; }

    /* Calculations Footer Grid */
    .calc-grid {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 30px;
      margin-bottom: 30px;
    }
    .notes-box {
      background: #F8FAFC; border-radius: 12px; padding: 16px; border: 1px solid #E2E8F0;
    }
    .notes-box h5 { font-size: 12px; font-weight: 700; color: #0F172A; margin-bottom: 6px; }
    .notes-box p { font-size: 11px; color: #64748B; line-height: 1.5; }

    .totals-box {
      background: #FFFFFF; border-radius: 12px; padding: 16px 20px; border: 1px solid #E2E8F0;
    }
    .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; color: #475569; }
    .totals-row.grand-total {
      border-top: 2px solid #0F172A; margin-top: 8px; padding-top: 10px; font-size: 16px; font-weight: 800; color: #4F46E5;
    }

    /* Sign & QR Barcode Footer */
    .footer-sign-grid {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      border-top: 2px dashed #CBD5E1;
      padding-top: 24px;
      margin-top: 40px;
    }
    .qr-box { display: flex; align-items: center; gap: 12px; }
    .qr-img { width: 70px; height: 70px; border-radius: 8px; border: 1px solid #E2E8F0; }
    .qr-text h6 { font-size: 12px; font-weight: 700; color: #0F172A; }
    .qr-text p { font-size: 10px; color: #64748B; }

    .sign-box { text-align: right; }
    .sign-space { height: 40px; }
    .sign-box p { font-size: 12px; font-weight: 700; color: #0F172A; border-top: 1px solid #94A3B8; padding-top: 4px; display: inline-block; width: 160px; text-align: center; }

    .watermark-footer {
      text-align: center; font-size: 10px; color: #94A3B8; margin-top: 20px; font-weight: 600;
    }

    /* Mobile Responsive Optimizations */
    @media screen and (max-width: 640px) {
      body {
        padding: 10px 8px !important;
      }
      .action-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 12px 14px;
      }
      .action-bar .title {
        justify-content: center;
        text-align: center;
        font-size: 13px;
      }
      .action-btns {
        justify-content: center;
        width: 100%;
        flex-wrap: wrap;
        gap: 8px;
      }
      .btn-action {
        flex: 1;
        min-width: 100px;
        justify-content: center;
        font-size: 11px;
        padding: 8px 10px;
      }
      .invoice-card {
        padding: 18px 14px !important;
        border-radius: 12px;
        min-height: auto !important;
      }
      .header-grid {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 16px;
      }
      .brand-section {
        flex-direction: column;
        text-align: center;
      }
      .invoice-title-badge {
        text-align: center !important;
        width: 100%;
      }
      .meta-grid {
        grid-template-columns: 1fr !important;
        gap: 14px;
        padding: 14px;
      }
      .calc-grid {
        grid-template-columns: 1fr !important;
        gap: 16px;
      }
      .footer-sign-grid {
        flex-direction: column-reverse;
        align-items: center;
        text-align: center;
        gap: 20px;
      }
      .sign-box {
        text-align: center !important;
        width: 100%;
      }
      .qr-box {
        flex-direction: column;
        text-align: center;
      }
      .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
      .items-table th, .items-table td {
        padding: 8px 10px;
        font-size: 11px;
      }
    }

    /* Print Setup for Clean A4 (Strict 1-Page Guarantee for Standard Invoices) */
    @page {
      size: A4 portrait;
      margin: 8mm 10mm;
    }

    @media print {
      html, body {
        background: #FFFFFF !important;
        margin: 0 !important;
        padding: 0 !important;
        color: #0F172A !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .no-print, .action-bar {
        display: none !important;
      }

      .invoice-card {
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        margin: 0 auto !important;
        width: 100% !important;
        max-width: 100% !important;
        min-height: auto !important;
      }

      .header-grid {
        padding-bottom: 12px !important;
        margin-bottom: 14px !important;
      }

      .meta-grid {
        padding: 10px 14px !important;
        margin-bottom: 14px !important;
        gap: 12px !important;
      }

      .table-container {
        margin-bottom: 14px !important;
      }

      .items-table th, .items-table td {
        padding: 7px 10px !important;
        font-size: 11.5px !important;
      }

      .items-table tr {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      .calc-grid {
        margin-bottom: 14px !important;
        gap: 16px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      .notes-box, .totals-box {
        padding: 10px 14px !important;
      }

      .footer-sign-grid {
        margin-top: 16px !important;
        padding-top: 12px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
      }

      .watermark-footer {
        margin-top: 10px !important;
        font-size: 9px !important;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Action Bar -->
  <div class="action-bar no-print">
    <div class="title">
      <i class="fa-solid fa-file-invoice" style="color: #818CF8;"></i>
      <span>DineFlow AI Official A4 Tax Invoice</span>
    </div>
    <div class="action-btns">
      <button class="btn-action btn-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print A4 PDF
      </button>
      <button class="btn-action btn-whatsapp" onclick="sendWhatsAppA4Invoice()">
        <i class="fa-brands fa-whatsapp"></i> Share on WhatsApp
      </button>
      <button class="btn-action btn-close" onclick="window.close()">
        <i class="fa-solid fa-xmark"></i> Close
      </button>
    </div>
  </div>

  <!-- A4 Invoice Container -->
  <div class="invoice-card">
    
    <!-- Header -->
    <div class="header-grid">
      <div class="brand-section">
        @php
          $logoUrl = $restaurant->getEffectiveLogoUrl();
        @endphp
        @if(!empty($logoUrl))
          <img src="{{ $logoUrl }}" alt="{{ $restaurant->name }} Logo" class="brand-logo" onerror="this.style.display='none'; document.getElementById('logoFallbackAvatar').style.display='flex';">
          <div id="logoFallbackAvatar" class="brand-logo" style="display:none; align-items:center; justify-content:center; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#fff; font-size:24px; font-weight:800; font-family:'Outfit',sans-serif;">
            {{ strtoupper(substr($restaurant->name, 0, 1)) }}
          </div>
        @else
          <div class="brand-logo" style="display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #6366F1, #4F46E5); color:#fff; font-size:24px; font-weight:800; font-family:'Outfit',sans-serif;">
            {{ strtoupper(substr($restaurant->name, 0, 1)) }}
          </div>
        @endif
        <div class="brand-info">
          <h1>{{ $restaurant->name }}</h1>
          <p><i class="fa-solid fa-location-dot"></i> {{ $restaurant->address }}</p>
          <p><i class="fa-solid fa-phone"></i> {{ $restaurant->phone }} @if(!empty($restaurant->email)) &nbsp;•&nbsp; <i class="fa-solid fa-envelope"></i> {{ $restaurant->email }} @endif</p>
          @if(!empty($restaurant->gstin))
          <p style="font-weight: 700; color: #334155; margin-top: 3px;">GSTIN: {{ $restaurant->gstin }}</p>
          @endif
        </div>
      </div>
      <div class="invoice-title-badge">
        <h2>TAX INVOICE</h2>
        <div class="inv-no">Invoice #: INV-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
        <div class="inv-date">Date: {{ optional($order->created_at)->format('d M, Y • h:i A') ?? now()->format('d M, Y • h:i A') }}</div>
      </div>
    </div>

    <!-- Meta Details -->
    <div class="meta-grid">
      <div class="meta-box">
        <h4>Billed To (Customer)</h4>
        <p>Guest Customer</p>
        <p style="font-size: 11px; color: #64748B;">Table: <strong>{{ $tableName ?? ($order->table ? ($order->table->table_number ?? $order->table->id) : ('Table #' . ($order->table_id ?? '1'))) }}</strong></p>
      </div>
      <div class="meta-box" style="text-align: right;">
        <h4>Payment Status</h4>
        <p>
          <span class="badge-status {{ strtolower($order->payment_status) == 'paid' ? 'badge-paid' : 'badge-pending' }}">
            {{ strtoupper($order->payment_status) }} ({{ strtoupper($order->payment_method) }})
          </span>
        </p>
        <p style="font-size: 11px; color: #64748B; margin-top: 4px;">Order Status: <strong>{{ strtoupper($order->status) }}</strong></p>
      </div>
    </div>

    <!-- Item Table -->
    <div class="table-container">
      <table class="items-table">
        <thead>
          <tr>
            <th style="width: 8%;">S.No</th>
            <th>Item Description</th>
            <th class="text-right" style="width: 12%;">Qty</th>
            <th class="text-right" style="width: 18%;">Unit Price</th>
            <th class="text-right" style="width: 20%;">Total</th>
          </tr>
        </thead>
        <tbody>
          @php $itemIndex = 1; @endphp
          @foreach($order->items as $item)
          <tr>
            <td>{{ $itemIndex++ }}</td>
            <td><strong>{{ $item->name }}</strong></td>
            <td class="text-right">{{ $item->quantity }}</td>
            <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
            <td class="text-right">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Calculations -->
    <div class="calc-grid">
      <div class="notes-box">
        <h5>Terms & Instructions</h5>
        <p>1. All taxes are calculated as per GST norms.</p>
        <p>2. Goods once sold will not be taken back.</p>
        <p>3. This is a computer-generated tax invoice and requires no physical signature for digital copy.</p>
      </div>

      <div class="totals-box">
        @php
          $subtotal = $order->total_amount;
          $serviceCharge = $subtotal * 0.05;
          $taxable = $subtotal + $serviceCharge;
          $cgst = $taxable * 0.09;
          $sgst = $taxable * 0.09;
          $grandTotal = round($taxable + $cgst + $sgst);
        @endphp
        <div class="totals-row">
          <span>Subtotal:</span>
          <span>₹{{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="totals-row">
          <span>Service Charge (5%):</span>
          <span>₹{{ number_format($serviceCharge, 2) }}</span>
        </div>
        <div class="totals-row">
          <span>CGST (9%):</span>
          <span>₹{{ number_format($cgst, 2) }}</span>
        </div>
        <div class="totals-row">
          <span>SGST (9%):</span>
          <span>₹{{ number_format($sgst, 2) }}</span>
        </div>
        <div class="totals-row grand-total">
          <span>Grand Total:</span>
          <span>₹{{ number_format($grandTotal, 2) }}</span>
        </div>
      </div>
    </div>

    <!-- Footer Signature & QR -->
    <div class="footer-sign-grid">
      <div class="qr-box">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(request()->fullUrl()) }}" alt="QR Code" class="qr-img">
        <div class="qr-text">
          <h6>Scan to Verify Invoice</h6>
          <p>DineFlow AI Digital Receipt</p>
        </div>
      </div>
      <div class="sign-box" style="text-align: center; width: 180px;">
        @if(!empty($isDemo))
          {{-- DEMO MODE SIGNATURE: Preserves existing SMP demo signature --}}
          <div style="font-family: 'Outfit', sans-serif; font-size: 22px; font-weight: 800; color: #1E293B; letter-spacing: 3px; margin-bottom: 4px;">
            SMP
          </div>
          <div style="border-top: 1.5px solid #475569; padding-top: 4px; font-size: 11px; font-weight: 700; color: #0F172A;">
            Authorized Signatory (SMP)
          </div>
        @else
          {{-- REAL RESTAURANT SIGNATURE --}}
          @php
            $signImg = $restaurant->getEffectiveSignature();
            $signName = $restaurant->getEffectiveSignatureName();
          @endphp
          @if(!empty($signImg))
            <div style="height: 48px; display: flex; align-items: center; justify-content: center; margin-bottom: 4px;">
              <img src="{{ $signImg }}" alt="Authorized Signature" style="max-height: 44px; max-width: 160px; object-fit: contain;">
            </div>
            <div style="border-top: 1.5px solid #475569; padding-top: 4px; font-size: 11px; font-weight: 700; color: #0F172A;">
              Authorized Signatory @if(!empty($signName)) ({{ $signName }}) @endif
            </div>
          @elseif(!empty($signName))
            <div style="font-family: 'Great Vibes', cursive; font-size: 26px; color: #1E293B; margin-bottom: 2px; line-height: 1.2;">
              {{ $signName }}
            </div>
            <div style="border-top: 1.5px solid #475569; padding-top: 4px; font-size: 11px; font-weight: 700; color: #0F172A;">
              Authorized Signatory ({{ $signName }})
            </div>
          @else
            <div style="height: 38px; display: flex; align-items: flex-end; justify-content: center; padding-bottom: 4px; font-size: 13px; font-weight: 600; color: #475569;">
              {{ $restaurant->name }}
            </div>
            <div style="border-top: 1.5px solid #475569; padding-top: 4px; font-size: 11px; font-weight: 700; color: #0F172A;">
              Authorized Signatory
            </div>
          @endif
        @endif
      </div>
    </div>

    <div class="watermark-footer">
      Thank you for dining with us! Powered by DineFlow AI Restaurant Suite v2.0
    </div>

  </div>

  <script>
    function sendWhatsAppA4Invoice() {
      const phone = prompt("Enter Customer WhatsApp Mobile Number:", "9876543210");
      if (!phone) return;
      let cleanPhone = phone.replace(/\D/g, '');
      if (cleanPhone.length === 10) cleanPhone = '91' + cleanPhone;

      let invoiceUrl = window.location.href;
      const hostIp = @json(request()->getHost() == 'localhost' || request()->getHost() == '127.0.0.1' ? gethostbyname(gethostname()) : request()->getHost());
      if (hostIp && hostIp !== '127.0.0.1') {
        invoiceUrl = invoiceUrl.replace('localhost', hostIp).replace('127.0.0.1', hostIp);
      }
      const msg = `🍽️ *${@json($restaurant->name)}*\n` +
                  `*Official Tax Invoice #INV-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}*\n` +
                  `-----------------------------------------\n` +
                  `📍 Table: ${@json($tableName ?? ($order->table ? ($order->table->table_number ?? $order->table->id) : ('Table #' . ($order->table_id ?? '1'))))}\n` +
                  `💰 *Grand Total: ₹{{ number_format($grandTotal, 2) }}*\n` +
                  `-----------------------------------------\n` +
                  `📄 View Digital PDF Invoice: ${invoiceUrl}\n\n` +
                  `Thank you for dining with us! 🙏`;

      window.open(`https://api.whatsapp.com/send?phone=${cleanPhone}&text=${encodeURIComponent(msg)}`, '_blank');
    }
  </script>

</body>
</html>
