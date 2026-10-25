<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
@php
  $kdsRest = $restaurant ?? (auth()->user() ? auth()->user()->restaurant : null) ?? \App\Models\Restaurant::find($restaurantId ?? 1);
  $kdsRestName = $kdsRest ? $kdsRest->name : 'DineFlow';
  $kdsRestLogo = $kdsRest ? $kdsRest->getEffectiveLogoUrl() : null;
@endphp
  <title>{{ $kdsRestName }} — Kitchen Display System</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    :root {
      --bg: #0B1329;
      --card: #1C2541;
      --border: rgba(255, 255, 255, 0.08);
      --primary: #6366F1;
      --primary-glow: rgba(99, 102, 241, 0.15);
      --success: #10B981;
      --warning: #F59E0B;
      --danger: #EF4444;
      --text-primary: #F8FAFC;
      --text-secondary: #94A3B8;
      --radius: 16px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--bg);
      color: var(--text-primary);
      padding: 16px;
      min-height: 100vh;
      overflow-x: hidden;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 12px;
    }

    header h1 {
      font-family: 'Outfit', sans-serif;
      font-size: 20px;
      font-weight: 800;
      color: #34D399;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    header .status-badge {
      background: rgba(16, 185, 129, 0.15);
      color: var(--success);
      font-size: 11px;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 12px;
      border: 1px solid rgba(16, 185, 129, 0.3);
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .kds-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px;
    }

    /* ─── KDS RESPONSIVE RULES ─── */
    @media (max-width: 768px) {
      body { padding: 10px; }
      header { flex-direction: column; align-items: stretch; gap: 10px; text-align: center; }
      header h1 { justify-content: center; }
      .kds-grid { grid-template-columns: 1fr; gap: 12px; }
    }

    .kds-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 10px 20px rgba(0,0,0,0.15);
      animation: fadeInCard 0.3s ease-out;
    }

    @keyframes fadeInCard {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .card-header {
      background: rgba(255, 255, 255, 0.02);
      border-bottom: 1px solid var(--border);
      padding: 12px 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-header .table-no {
      font-family: 'Outfit', sans-serif;
      font-size: 18px;
      font-weight: 800;
      color: #38BDF8;
    }

    .card-header .order-time {
      font-size: 11px;
      color: var(--text-secondary);
      font-weight: 600;
    }

    .card-body {
      padding: 16px;
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .item-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      padding-bottom: 8px;
    }

    .item-row:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .item-name {
      font-size: 13.5px;
      font-weight: 700;
      color: var(--text-primary);
    }

    .item-category {
      font-size: 9px;
      color: var(--text-secondary);
      text-transform: uppercase;
      display: block;
      margin-top: 2px;
    }

    .item-qty {
      background: rgba(99, 102, 241, 0.15);
      color: #818CF8;
      font-size: 14px;
      font-weight: 800;
      padding: 2px 8px;
      border-radius: 6px;
      border: 1px solid rgba(99, 102, 241, 0.25);
    }

    .card-footer {
      padding: 12px 16px;
      background: rgba(0,0,0,0.1);
      border-top: 1px solid var(--border);
      display: flex;
      gap: 8px;
    }

    .btn {
      flex: 1;
      border: none;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s;
    }

    .btn-success {
      background: var(--success);
      color: white;
    }

    .btn-warning {
      background: var(--warning);
      color: white;
    }

    .btn-success:hover, .btn-warning:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    .no-orders {
      grid-column: 1 / -1;
      text-align: center;
      padding: 60px 20px;
      color: var(--text-secondary);
    }

    .no-orders i {
      font-size: 40px;
      color: rgba(255,255,255,0.06);
      margin-bottom: 12px;
    }

    /* Notification chime status */
    #soundPermission {
      background: var(--warning);
      color: white;
      padding: 10px;
      text-align: center;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      display: none;
    }
  </style>
</head>
<body>
@include('partials.impersonate-banner')

  <div id="soundPermission" onclick="enableSound()" style="background: linear-gradient(135deg, #6366F1, #4F46E5); color: white; padding: 12px; text-align: center; border-radius: 10px; margin-bottom: 15px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);">
    <i class="fa-solid fa-volume-high fa-beat"></i> <span>અહીં ક્લિક કરી ગુજરાતી સાઉન્ડબોક્સ ચાલુ કરો (Click to Enable Gujarati Voice Alerts)</span>
  </div>

  <header>
    <div style="display: flex; align-items: center; gap: 10px;">
      @if($kdsRestLogo)
        <img src="{{ $kdsRestLogo }}" alt="{{ $kdsRestName }}" style="width: 34px; height: 34px; border-radius: 8px; object-fit: contain; background: #fff; padding: 2px;">
      @else
        <div style="width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, #10B981, #059669); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; font-family: 'Outfit', sans-serif;">{{ substr($kdsRestName, 0, 1) }}</div>
      @endif
      <div>
        <h1 style="font-size: 19px; line-height: 1.1; margin: 0; color: #34D399; font-family: 'Outfit', sans-serif; font-weight: 800;">{{ $kdsRestName }}</h1>
        <span style="font-size: 11px; color: var(--text-secondary); font-weight: 600;">Kitchen Display System &bull; KOT Monitor</span>
      </div>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
      <!-- Gujarati Voice Soundbox Mute/Unmute Toggle Button -->
      <button id="voiceMuteToggleBtn" onclick="toggleVoiceMute()" style="background: rgba(255, 255, 255, 0.06); border: 1px solid var(--border); color: #F8FAFC; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s;">
        <i id="voiceMuteIcon" class="fa-solid fa-volume-high" style="color: #10B981;"></i>
        <span id="voiceMuteText">ગુજરાતી અવાજ: ચાલુ (🔊)</span>
      </button>

      <div class="status-badge" id="syncStatus">
        <i class="fa-solid fa-circle-dot fa-fade"></i> Live Monitoring
      </div>
    </div>
  </header>

  <div class="kds-grid" id="kdsGrid">
    <div class="no-orders">
      <i class="fa-solid fa-spinner fa-spin"></i>
      <p>Initializing KOT screen...</p>
    </div>
  </div>

  <script>
    let isSoundMuted = localStorage.getItem('dineflow_voice_muted') === 'true';
    let isSoundEnabled = false;
    let processedOrders = new Set(JSON.parse(localStorage.getItem('dineflow_kitchen_processed') || '[]').map(String));
    let isFirstLoad = true;

    function updateMuteButtonUI() {
      const icon = document.getElementById('voiceMuteIcon');
      const text = document.getElementById('voiceMuteText');
      const btn = document.getElementById('voiceMuteToggleBtn');
      if (isSoundMuted) {
        icon.className = 'fa-solid fa-volume-xmark';
        icon.style.color = '#EF4444';
        text.textContent = 'સાઉન્ડ મ્યૂટ (🔇)';
        btn.style.borderColor = 'rgba(239, 68, 68, 0.4)';
      } else {
        icon.className = 'fa-solid fa-volume-high';
        icon.style.color = '#10B981';
        text.textContent = 'ગુજરાતી અવાજ: ચાલુ (🔊)';
        btn.style.borderColor = 'rgba(16, 185, 129, 0.4)';
      }
    }

    function toggleVoiceMute() {
      isSoundMuted = !isSoundMuted;
      localStorage.setItem('dineflow_voice_muted', isSoundMuted ? 'true' : 'false');
      updateMuteButtonUI();
      if (!isSoundMuted) {
        speakGujarati('ડાઇનફ્લો કિચન સાઉન્ડબોક્સ સક્રિય છે.');
      }
    }

    // Gujarati Voice Speech Synthesis
    function speakGujarati(text) {
      if (isSoundMuted) return;
      try {
        window.speechSynthesis.cancel(); // Cancel active speech
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'gu-IN'; // Gujarati Language
        utterance.rate = 0.95; // Clear natural rate
        utterance.pitch = 1.0;
        
        // Find Gujarati voice if available, else fallback
        const voices = window.speechSynthesis.getVoices();
        const guVoice = voices.find(v => v.lang.toLowerCase().includes('gu')) || 
                             voices.find(v => v.lang.toLowerCase().includes('hi')) ||
                             voices[0];
        if (guVoice) {
          utterance.voice = guVoice;
        }
        window.speechSynthesis.speak(utterance);
      } catch (err) {
        console.log('Gujarati Speech Synthesis error:', err);
      }
    }

    function playChimeAndSpeakOrder() {
      if (isSoundMuted) return;
      // Play crisp professional Gujarati Speech Announcement for Kitchen
      const spokenText = "ડાઇનફ્લો કિચન: નવો ઓર્ડર આવ્યો છે.";
      speakGujarati(spokenText);
    }

    function enableSound() {
      isSoundEnabled = true;
      document.getElementById('soundPermission').style.display = 'none';
      updateMuteButtonUI();
      speakGujarati('ડાઇનફ્લો કિચન સાઉન્ડબોક્સ સક્રિય છે.');
    }

    // Request audio playback permission on page load
    window.addEventListener('click', () => {
      if (!isSoundEnabled) {
        enableSound();
      }
    }, { once: true });

    // Show sound permission bar if not interactable yet
    setTimeout(() => {
      if (!isSoundEnabled) {
        document.getElementById('soundPermission').style.display = 'block';
      }
    }, 2000);

    let isLoadingKOTs = false;

    async function loadKOTs() {
      if (isLoadingKOTs) return;
      isLoadingKOTs = true;
      try {
        const statusBadge = document.getElementById('syncStatus');
        if (statusBadge) {
          statusBadge.style.color = '#F59E0B';
          statusBadge.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing';
        }

        const response = await fetch('/api/orders/restaurant/{{ Auth::user()?->restaurant_id ?? '' }}');
        const data = await response.json();
        
        if (statusBadge) {
          statusBadge.style.color = 'var(--success)';
          statusBadge.innerHTML = '<i class="fa-solid fa-circle-dot fa-fade"></i> Live Monitoring';
        }

        const ordersList = Array.isArray(data.data) ? data.data : (data.data?.data || []);
        if (data.success && Array.isArray(ordersList)) {
          // Show all active orders that are pending, preparing or cooking
          const activeOrders = ordersList.filter(order => order.status !== 'completed' && order.status !== 'cancelled' && order.status !== 'served');
          
          if (activeOrders.length === 0) {
            document.getElementById('kdsGrid').innerHTML = `
              <div class="no-orders">
                <i class="fa-solid fa-utensils"></i>
                <p>No active KOTs found.<br><span style="font-size:11px;opacity:0.6;">Waiting for new orders...</span></p>
              </div>
            `;
            isFirstLoad = false;
            return;
          }

          // Check if there is a new order
          activeOrders.forEach(order => {
            const orderIdStr = String(order.id);
            if (!processedOrders.has(orderIdStr)) {
              processedOrders.add(orderIdStr);
              localStorage.setItem('dineflow_kitchen_processed', JSON.stringify(Array.from(processedOrders)));
              if (!isFirstLoad) {
                playChimeAndSpeakOrder();
              }
            }
          });

          if (isFirstLoad) {
            activeOrders.forEach(order => processedOrders.add(String(order.id)));
            localStorage.setItem('dineflow_kitchen_processed', JSON.stringify(Array.from(processedOrders)));
            isFirstLoad = false;
          }

          // Render KDS cards
          document.getElementById('kdsGrid').innerHTML = activeOrders.map(order => {
            const tableNum = order.table ? order.table.table_number : 'Table #' + order.table_id;
            const itemsHtml = (order.items || []).map(item => `
              <div class="item-row">
                <div>
                  <span class="item-name">${item.name}</span>
                  <span class="item-category">${item.menu_item?.category || 'food'}</span>
                  ${item.notes ? `<div style="margin-top:3px; font-size:11px; font-weight:700; color:#F59E0B; background:rgba(245,158,11,0.15); padding:2px 6px; border-radius:4px; display:inline-block;"><i class="fa-solid fa-utensils"></i> ${item.notes}</div>` : ''}
                </div>
                <span class="item-qty">x${item.quantity}</span>
              </div>
            `).join('');

            // Format date/time
            const date = new Date(order.created_at);
            const timeStr = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

            return `
              <div class="kds-card" id="kds-card-${order.id}">
                <div class="card-header">
                  <span class="table-no">${tableNum}</span>
                  <span class="order-time"><i class="fa-regular fa-clock"></i> ${timeStr} (#${order.id})</span>
                </div>
                <div class="card-body">
                  ${itemsHtml}
                  ${order.special_instructions ? `<div style="margin-top:8px; padding:6px 8px; border-radius:6px; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); font-size:11.5px; color:#FBBF24;"><i class="fa-solid fa-note-sticky"></i> ${order.special_instructions}</div>` : ''}
                </div>
                <div class="card-footer" style="gap: 6px;">
                  <button class="btn btn-warning" onclick="printKOT(${order.id})" style="padding: 6px 10px; font-size: 12px;" title="Print Kitchen KOT">
                    <i class="fa-solid fa-print"></i> KOT
                  </button>
                  ${order.status === 'preparing' || order.status === 'cooking' ? `
                    <span style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #F59E0B; padding: 5px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
                      🍳 Cooking...
                    </span>
                  ` : `
                    <button class="btn" style="background: #3B82F6; color: white; padding: 6px 10px; font-size: 12px;" onclick="startCookingKOT(${order.id})">
                      🍳 Start Cooking
                    </button>
                  `}
                  <button class="btn btn-success" onclick="completeKOT(${order.id})" style="padding: 6px 10px; font-size: 12px;" title="Mark Ready">
                    <i class="fa-solid fa-circle-check"></i> Ready
                  </button>
                  <button class="btn" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.35); color: #FCA5A5; padding: 6px 9px; font-size: 12px;" onclick="cancelKOT(${order.id})" title="Reject / Cancel Order">
                    <i class="fa-solid fa-ban"></i>
                  </button>
                </div>
              </div>
            `;
          }).join('');
        }
      } catch (err) {
        console.log('Error refreshing KDS:', err);
      } finally {
        isLoadingKOTs = false;
      }
    }

    async function startCookingKOT(orderId) {
      try {
        const response = await fetch('/api/orders/' + orderId + '/status', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({ status: 'preparing' })
        });
        const resData = await response.json();
        if (resData.success) {
          loadKOTs();
        } else {
          alert('Failed to start cooking: ' + (resData.message || 'Unknown error'));
        }
      } catch (err) {
        console.log('Error starting cooking:', err);
        alert('Error starting cooking. Please try again.');
      }
    }

    function printKOT(orderId) {
      window.open('/orders/' + orderId + '/receipt/kot-combined', '_blank');
    }

    async function cancelKOT(orderId) {
      const reason = prompt('Cancellation reason for Order #' + orderId + ':', 'Kitchen item unavailable / rejected');
      if (!reason) return;
      try {
        const response = await fetch('/api/orders/' + orderId + '/cancel', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({ reason: reason })
        });
        const data = await response.json();
        if (data.success) {
          processedOrders.delete(String(orderId));
          localStorage.setItem('dineflow_kitchen_processed', JSON.stringify(Array.from(processedOrders)));
          loadKOTs();
        } else {
          alert('Failed to cancel order: ' + (data.message || 'Unknown error'));
        }
      } catch (err) {
        alert('Error cancelling order: ' + err.message);
      }
    }

    async function completeKOT(orderId) {
      if (confirm('Mark Order #' + orderId + ' as Ready / Served?')) {
        const card = document.getElementById('kds-card-' + orderId);
        if (card) {
          card.style.opacity = '0.3';
          card.style.pointerEvents = 'none';
        }
        try {
          const response = await fetch('/api/orders/' + orderId + '/status', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ status: 'served' })
          });
          const resData = await response.json();
          if (resData.success) {
            processedOrders.delete(String(orderId));
            localStorage.setItem('dineflow_kitchen_processed', JSON.stringify(Array.from(processedOrders)));
            loadKOTs();
          } else {
            alert('Failed to update order status');
            if (card) {
              card.style.opacity = '1';
              card.style.pointerEvents = 'auto';
            }
          }
        } catch (err) {
          console.log('Error completing KOT:', err);
          if (card) {
            card.style.opacity = '1';
            card.style.pointerEvents = 'auto';
          }
        }
      }
    }

    // Refresh every 12 seconds with visibility check to eliminate lag
    setInterval(() => {
      if (document.visibilityState === 'hidden') return;
      loadKOTs();
    }, 12000);
    // Initial fetch
    loadKOTs();
  </script>
</body>
</html>
