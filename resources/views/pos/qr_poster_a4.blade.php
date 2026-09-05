<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $restaurant->name }} — Table QR Poster (A4 Print)</title>
  
  <!-- Google Fonts & FontAwesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #0F172A;
      color: #0F172A;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 24px 16px;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    /* Screen Action Bar */
    .screen-action-bar {
      width: 100%;
      max-width: 650px;
      background: #1E293B;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      padding: 12px 20px;
      margin-bottom: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
    .action-title {
      color: #F8FAFC;
      font-weight: 700;
      font-size: 13.5px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .action-btns {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .btn-action {
      padding: 8px 16px;
      border-radius: 8px;
      border: none;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
    }
    .btn-print {
      background: linear-gradient(135deg, #6366F1, #4F46E5);
      color: #FFFFFF;
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    .btn-print:hover {
      filter: brightness(1.1);
      transform: translateY(-1px);
    }
    .btn-close {
      background: rgba(255, 255, 255, 0.08);
      color: #94A3B8;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .btn-close:hover {
      background: rgba(255, 255, 255, 0.15);
      color: #FFFFFF;
    }

    /* A4 Sheet Container */
    .a4-container {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 32px;
    }

    /* Single A4 Poster Page */
    .poster-page {
      width: 210mm;
      min-height: 297mm;
      max-height: 297mm;
      background: #FFFFFF;
      box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
      border-radius: 4px;
      padding: 10mm 12mm;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      box-sizing: border-box;
      position: relative;
      page-break-inside: avoid;
      break-inside: avoid;
      overflow: hidden;
    }

    /* Internal Decorative Border Frame */
    .poster-card {
      width: 100%;
      height: 100%;
      border: 3px solid #0F172A;
      border-radius: 28px;
      padding: 24px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-between;
      text-align: center;
      box-sizing: border-box;
      background: #FFFFFF;
      position: relative;
    }

    /* Outer Corner Accents */
    .poster-card::before, .poster-card::after {
      content: '';
      position: absolute;
      width: 14px;
      height: 14px;
      border: 2px solid #6366F1;
      border-radius: 4px;
    }
    .poster-card::before { top: 8px; left: 8px; }
    .poster-card::after { bottom: 8px; right: 8px; }

    /* --- 1. TOP SECTION: RESTAURANT BRANDING --- */
    .top-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      width: 100%;
    }

    .rest-logo-container {
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 2px;
    }
    .rest-logo-img {
      max-height: 60px;
      max-width: 180px;
      object-fit: contain;
    }
    .rest-monogram {
      width: 54px;
      height: 54px;
      border-radius: 14px;
      background: #0F172A;
      color: #FFFFFF;
      font-size: 26px;
      font-weight: 900;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
    }

    .rest-name {
      font-family: 'Outfit', sans-serif;
      font-size: 25px;
      font-weight: 900;
      color: #0F172A;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 1.2;
      word-break: break-word;
      max-width: 90%;
    }

    .instruction-text {
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 600;
      color: #475569;
      letter-spacing: 0.3px;
      margin-top: 2px;
    }

    /* --- 2. MIDDLE SECTION: QR CODE & TABLE BADGE --- */
    .middle-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 14px;
      margin: 10px 0;
    }

    .qr-frame {
      width: 220px;
      height: 220px;
      background: #FFFFFF;
      border: 2px solid #E2E8F0;
      border-radius: 18px;
      padding: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 24px -6px rgba(15, 23, 42, 0.1);
    }
    .qr-img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      display: block;
      image-rendering: -webkit-optimize-contrast;
      image-rendering: crisp-edges;
    }
    .qr-canvas-fallback {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .qr-canvas-fallback canvas {
      max-width: 100% !important;
      height: auto !important;
    }

    .table-badge {
      background: #0F172A;
      color: #FFFFFF;
      font-family: 'Outfit', sans-serif;
      font-size: 18px;
      font-weight: 900;
      padding: 6px 30px;
      border-radius: 24px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
    }

    /* Dividers */
    .divider-line {
      width: 82%;
      height: 1.5px;
      background: #E2E8F0;
      margin: 4px 0;
    }

    /* --- 3. BOTTOM SECTION: DINEFLOW & INSTAGRAM FOOTER --- */
    .bottom-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      width: 100%;
    }

    .powered-row {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
    }
    .powered-title {
      font-family: 'Inter', sans-serif;
      font-size: 11px;
      font-weight: 700;
      color: #64748B;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .powered-brand-line {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .dineflow-logo-img {
      height: 18px;
      object-fit: contain;
    }
    .smp-pill {
      font-size: 9px;
      font-weight: 900;
      color: #4F46E5;
      background: #EEF2FF;
      border: 1px solid #C7D2FE;
      padding: 1px 6px;
      border-radius: 4px;
      letter-spacing: 0.5px;
      font-family: 'Inter', sans-serif;
    }

    .instagram-row {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 2px;
    }
    .instagram-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      color: #0F172A;
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 0.3px;
      transition: opacity 0.2s;
    }
    .instagram-link:hover {
      opacity: 0.8;
    }
    .instagram-icon {
      font-size: 15px;
      color: #E1306C;
    }

    /* --- PRINT OPTIMIZATION (EXACT A4 PORTRAIT - STRICT 1-PAGE GUARANTEE) --- */
    @page {
      size: A4 portrait;
      margin: 0;
    }

    @media print {
      html, body {
        background: #FFFFFF !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 210mm !important;
        height: 297mm !important;
        max-height: 297mm !important;
        overflow: hidden !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .no-print, .screen-action-bar {
        display: none !important;
      }

      .a4-container {
        display: block !important;
        width: 210mm !important;
        margin: 0 !important;
        padding: 0 !important;
        gap: 0 !important;
      }

      .poster-page {
        box-shadow: none !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 8mm 12mm !important;
        width: 210mm !important;
        max-width: 210mm !important;
        height: 297mm !important;
        max-height: 297mm !important;
        min-height: 297mm !important;
        box-sizing: border-box !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        overflow: hidden !important;
        page-break-before: avoid !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        @if(!$isBatch)
        page-break-after: avoid !important;
        break-after: avoid !important;
        @else
        page-break-after: always !important;
        break-after: page !important;
        @endif
      }

      .poster-page:last-child {
        page-break-after: avoid !important;
        break-after: avoid !important;
      }

      .poster-card {
        border-width: 2.5px !important;
        border-color: #0F172A !important;
        border-radius: 26px !important;
        padding: 20px 22px 18px !important;
        height: 100% !important;
        max-height: 100% !important;
      }
    }

    /* Responsive Screen Scaling */
    @media screen and (max-width: 820px) {
      body {
        padding: 16px 8px;
      }
      .poster-page {
        width: 100%;
        max-width: 420px;
        min-height: auto;
        max-height: none;
        padding: 16px;
        aspect-ratio: 210 / 297;
      }
      .poster-card {
        padding: 18px 14px;
      }
      .rest-name {
        font-size: 20px;
      }
      .qr-frame {
        width: 180px;
        height: 180px;
      }
      .table-badge {
        font-size: 15px;
        padding: 5px 22px;
      }
    }
  </style>
</head>
<body>

  <!-- Screen Floating Control Bar (Hidden when Printing) -->
  <div class="screen-action-bar no-print">
    <div class="action-title">
      <i class="fa-solid fa-qrcode" style="color: #818CF8;"></i>
      <span>Table QR Poster (A4 Portrait)</span>
    </div>

    <div class="action-btns">
      @if(isset($tables) && count($tables) > 1 && !$isBatch)
        <select onchange="window.location.href='/admin/qr-poster/' + this.value" style="background:#0F172A; color:#F8FAFC; border:1px solid #334155; padding:6px 10px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;">
          @foreach($tables as $t)
            <option value="{{ $t->id }}" {{ (isset($table) && $table->id == $t->id) ? 'selected' : '' }}>
              {{ $t->table_number ?: ('Table ' . $t->id) }}
            </option>
          @endforeach
        </select>
      @endif

      <button class="btn-action btn-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print A4 Poster / PDF
      </button>

      <button class="btn-action btn-close" onclick="window.close()">
        <i class="fa-solid fa-xmark"></i> Close
      </button>
    </div>
  </div>

  <!-- A4 Poster Sheets Container -->
  <div class="a4-container">
    @php
      $postersList = $isBatch ? $tables : collect([$table]);
      $instaHandle = config('dineflow.instagram.handle', '@smp.officials');
      $instaUrl = config('dineflow.instagram.url', 'https://www.instagram.com/smp.officials/');
      $dineLogo = config('dineflow.logo', '/images/dineflow_logo.png');
      $dineLogoAlt = config('dineflow.logo_alt', '/images/dineflow_logo.jpg');
    @endphp

    @foreach($postersList as $t)
      @php
        $targetTableUrl = $baseUrl . "/customer/menu?restaurantId={$restaurant->id}&tableId={$t->id}";
        $rawNum = $t->table_number ?: (string)$t->id;
        $tBadge = stripos($rawNum, 'table') !== false ? strtoupper($rawNum) : 'TABLE: ' . strtoupper($rawNum);
        $logoUrl = $restaurant->getEffectiveLogoUrl();
      @endphp

      <div class="poster-page">
        <div class="poster-card">
          
          <!-- 1. TOP SECTION: RESTAURANT BRANDING (PRIMARY) -->
          <div class="top-section">
            <div class="rest-logo-container">
              @if(!empty($logoUrl))
                <img src="{{ $logoUrl }}" alt="{{ $restaurant->name }}" class="rest-logo-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="rest-monogram" style="display:none;">{{ strtoupper(substr($restaurant->name, 0, 1)) }}</div>
              @else
                <div class="rest-monogram">{{ strtoupper(substr($restaurant->name, 0, 1)) }}</div>
              @endif
            </div>

            <h1 class="rest-name">{{ $restaurant->name }}</h1>
            <p class="instruction-text">Scan to View Menu &amp; Order</p>
          </div>

          <!-- 2. MIDDLE SECTION: QR CODE & TABLE BADGE (PRIMARY FUNCTIONAL) -->
          <div class="middle-section">
            <div class="qr-frame">
              <img src="https://api.qrserver.com/v1/create-qr-code/?size=500x500&margin=1&data={{ urlencode($targetTableUrl) }}" 
                   alt="Table QR Code" 
                   class="qr-img" 
                   id="qrImg_{{ $t->id }}"
                   onerror="renderQrFallback('{{ $t->id }}', '{{ $targetTableUrl }}')">
              <div id="qrCanvas_{{ $t->id }}" class="qr-canvas-fallback" style="display:none;"></div>
            </div>

            <div class="table-badge">{{ $tBadge }}</div>
          </div>

          <!-- 3. DIVIDER -->
          <div class="divider-line"></div>

          <!-- 4. BOTTOM SECTION: DINEFLOW & INSTAGRAM (SECONDARY) -->
          <div class="bottom-section">
            <div class="powered-row">
              <span class="powered-title">Powered by DineFlow</span>
              <div class="powered-brand-line">
                <img src="{{ $dineLogo }}" alt="DineFlow" class="dineflow-logo-img" onerror="this.src='{{ $dineLogoAlt }}'">
                <span class="smp-pill">SMP</span>
              </div>
            </div>

            <div class="instagram-row">
              <a href="{{ $instaUrl }}" target="_blank" rel="noopener noreferrer" class="instagram-link" title="Follow on Instagram">
                <i class="fa-brands fa-instagram instagram-icon"></i>
                <span>{{ $instaHandle }}</span>
              </a>
            </div>
          </div>



        </div>
      </div>
    @endforeach
  </div>

  <script>
    function renderQrFallback(tableId, url) {
      const img = document.getElementById('qrImg_' + tableId);
      const canvasContainer = document.getElementById('qrCanvas_' + tableId);
      if (img && canvasContainer) {
        img.style.display = 'none';
        canvasContainer.style.display = 'flex';
        canvasContainer.innerHTML = '';
        try {
          new QRCode(canvasContainer, {
            text: url,
            width: 200,
            height: 200,
            colorDark: '#000000',
            colorLight: '#FFFFFF',
            correctLevel: QRCode.CorrectLevel.H
          });
        } catch (e) {
          console.error('Offline QRCode fallback error:', e);
        }
      }
    }
  </script>

</body>
</html>