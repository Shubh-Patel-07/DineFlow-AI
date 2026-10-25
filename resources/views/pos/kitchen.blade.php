<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  $kdsRest = $restaurant ?? (auth()->user() ? auth()->user()->restaurant : null) ?? \App\Models\Restaurant::find($restaurantId ?? 1);
  $kdsRestName = $kdsRest ? $kdsRest->name : 'DineFlow';
  $kdsRestLogo = $kdsRest ? $kdsRest->getEffectiveLogoUrl() : null;
@endphp
  <title>{{ $kdsRestName }} — Kitchen Display System (KDS)</title>
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="shortcut icon" href="/favicon.ico">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            kds: {
              bg: '#0B0B0D',
              card: '#151517',
              panel: '#1A1A1E',
              border: '#26262A',
              wine: '#9F1239',
              rose: '#FB7185'
            }
          },
          fontFamily: {
            sans: ['Inter', '-apple-system', 'sans-serif'],
            mono: ['Geist Mono', 'ui-monospace', 'monospace'],
            display: ['Outfit', 'Inter', 'sans-serif']
          }
        }
      }
    }
  </script>

  <style>
    :root {
      --bg: #0B0B0D;
      --card: #151517;
      --border: #26262A;
      --brand: #9F1239;
      --text-primary: #F4F4F5;
      --text-muted: #A1A1AA;
    }
    body {
      background: var(--bg);
      color: var(--text-primary);
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
    }
    .kds-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 18px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
      animation: fadeInCard 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes fadeInCard {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .tabular-nums {
      font-variant-numeric: tabular-nums;
    }
    @keyframes pulseGlow {
      0%, 100% { opacity: 1; box-shadow: 0 0 16px rgba(239, 68, 68, 0.4); }
      50% { opacity: 0.85; box-shadow: 0 0 4px rgba(239, 68, 68, 0.1); }
    }
    .overdue-glow {
      animation: pulseGlow 1.8s infinite ease-in-out;
    }
  </style>
</head>
<body class="p-3 sm:p-5 flex flex-col">
@include('partials.impersonate-banner')

  <!-- Gujarati / Hindi Soundbox Audio Permission Banner -->
  <div id="soundPermission" onclick="enableSound()" class="hidden mb-4 p-3.5 rounded-2xl bg-gradient-to-r from-[var(--brand)] to-rose-700 text-white text-center text-sm font-semibold cursor-pointer shadow-lg items-center justify-center gap-2 border border-rose-500/30">
    <i class="fa-solid fa-volume-high text-base fa-beat"></i>
    <span>અહીં ક્લિક કરી ગુજરાતી સાઉન્ડબોક્સ ચાલુ કરો (Click to Enable Kitchen Gujarati Voice Announcements)</span>
  </div>

  <!-- KDS Header Bar -->
  <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-3 border-b border-[var(--border)]">
    <div class="flex items-center gap-3">
      @if($kdsRestLogo)
        <img src="{{ $kdsRestLogo }}" alt="{{ $kdsRestName }}" class="w-10 h-10 rounded-xl object-contain bg-white p-1 border border-[var(--border)]">
      @else
        <div class="w-10 h-10 rounded-xl bg-[var(--brand)] text-white flex items-center justify-center font-extrabold text-base font-display">
          {{ substr($kdsRestName, 0, 1) }}
        </div>
      @endif
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white flex items-center gap-2 font-display">
          {{ $kdsRestName }}
          <span class="text-xs font-mono font-medium px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 tracking-wider uppercase">Live KOT</span>
        </h1>
        <div class="text-xs text-[var(--text-muted)] font-mono">Kitchen Display System &bull; High-Contrast Monitor</div>
      </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
      <!-- Voice Soundbox Toggle Button -->
      <button id="voiceMuteToggleBtn" onclick="toggleVoiceMute()" class="px-3.5 py-1.5 rounded-full text-xs font-medium border border-[var(--border)] bg-[#1A1A1E] text-white hover:bg-stone-800 transition flex items-center gap-2 cursor-pointer shadow-sm">
        <i id="voiceMuteIcon" class="fa-solid fa-volume-high text-emerald-400"></i>
        <span id="voiceMuteText" class="font-mono text-[11px]">ગુજરાતી અવાજ: ચાલુ (🔊)</span>
      </button>

      <!-- Sync Status Badge -->
      <div class="status-badge px-3 py-1.5 rounded-full text-xs font-mono font-medium border border-emerald-500/30 bg-emerald-500/10 text-emerald-400 flex items-center gap-1.5 shadow-sm" id="syncStatus">
        <i class="fa-solid fa-circle-dot text-[10px] animate-pulse"></i> Live Monitoring
      </div>
    </div>
  </header>

  <!-- KDS Grid (Legible from 2 to 3 meters away) -->
  <main class="flex-1">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="kdsGrid">
      <div class="col-span-full text-center py-16 text-[var(--text-muted)] space-y-2">
        <i class="fa-solid fa-spinner fa-spin text-3xl mb-2 block text-emerald-400"></i>
        <p class="font-medium">Initializing high-contrast KOT cards...</p>
      </div>
    </div>
  </main>

  <!-- ═══════════════════════════════════════════════════════════ -->
  <!-- KDS JAVASCRIPT ENGINE WITH ZERO-BREAKAGE AUDIO & POLLING     -->
  <!-- ═══════════════════════════════════════════════════════════ -->
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
        icon.className = 'fa-solid fa-volume-xmark text-rose-500';
        text.textContent = 'સાઉન્ડ મ્યૂટ (🔇)';
        btn.style.borderColor = 'rgba(239, 68, 68, 0.4)';
      } else {
        icon.className = 'fa-solid fa-volume-high text-emerald-400';
        text.textContent = 'ગુજરાતી અવાજ: ચાલુ (🔊)';
        btn.style.borderColor = 'rgba(34, 197, 94, 0.4)';
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

    function speakGujarati(text) {
      if (isSoundMuted) return;
      try {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'gu-IN';
        utterance.rate = 0.95;
        utterance.pitch = 1.0;
        
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
      const spokenText = "ડાઇનફ્લો કિચન: નવો ઓર્ડર આવ્યો છે.";
      speakGujarati(spokenText);
    }

    function enableSound() {
      isSoundEnabled = true;
      document.getElementById('soundPermission').style.display = 'none';
      updateMuteButtonUI();
      speakGujarati('ડાઇનફ્લો કિચન સાઉન્ડબોક્સ સક્રિય છે.');
    }

    window.addEventListener('click', () => {
      if (!isSoundEnabled) enableSound();
    }, { once: true });

    setTimeout(() => {
      if (!isSoundEnabled) {
        const p = document.getElementById('soundPermission');
        if (p) p.style.display = 'flex';
      }
    }, 2000);

    let isLoadingKOTs = false;

    async function loadKOTs() {
      if (isLoadingKOTs) return;
      isLoadingKOTs = true;
      try {
        const statusBadge = document.getElementById('syncStatus');
        if (statusBadge) {
          statusBadge.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-amber-400"></i> Refreshing';
        }

        const response = await fetch('/api/orders/restaurant/{{ Auth::user()?->restaurant_id ?? '' }}');
        const data = await response.json();
        
        if (statusBadge) {
          statusBadge.innerHTML = '<i class="fa-solid fa-circle-dot text-[10px] animate-pulse text-emerald-400"></i> Live Monitoring';
        }

        const ordersList = Array.isArray(data.data) ? data.data : (data.data?.data || []);
        if (data.success && Array.isArray(ordersList)) {
          const activeOrders = ordersList.filter(order => order.status !== 'completed' && order.status !== 'cancelled' && order.status !== 'served');
          
          if (activeOrders.length === 0) {
            document.getElementById('kdsGrid').innerHTML = `
              <div class="col-span-full text-center py-24 text-[var(--text-muted)] space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-[#1A1A1E] border border-[var(--border)] flex items-center justify-center mx-auto text-2xl text-[var(--text-muted)]">
                  <i class="fa-solid fa-utensils"></i>
                </div>
                <h3 class="text-base font-semibold text-white">No Active KOT Orders</h3>
                <p class="text-xs text-[var(--text-muted)]">New tickets sent from POS or QR menu will appear here instantly.</p>
              </div>
            `;
            isFirstLoad = false;
            return;
          }

          // Check for new orders to announce
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

          // Render high-contrast 2-3 meter legible KDS cards
          document.getElementById('kdsGrid').innerHTML = activeOrders.map(order => {
            const tableNum = order.table ? (order.table.table_number || 'T-' + order.table_id) : 'Table #' + order.table_id;
            
            // Elapsed time & Urgency indicators (Green < 10m, Amber 10-20m, Red > 20m)
            const createdTime = new Date(order.created_at).getTime();
            const elapsedMins = Math.max(0, Math.floor((Date.now() - createdTime) / 60000));
            
            let urgencyBadge = '';
            let cardBorderClass = 'border-[var(--border)]';
            if (elapsedMins >= 20) {
              urgencyBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 animate-pulse"><i class="fa-solid fa-triangle-exclamation mr-1"></i>${elapsedMins}m Overdue</span>`;
              cardBorderClass = 'border-rose-600/70 overdue-glow';
            } else if (elapsedMins >= 10) {
              urgencyBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40"><i class="fa-solid fa-fire mr-1"></i>${elapsedMins}m Cooking</span>`;
              cardBorderClass = 'border-amber-500/40';
            } else {
              urgencyBadge = `<span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40"><i class="fa-regular fa-clock mr-1"></i>${elapsedMins}m Fresh</span>`;
            }

            const itemsHtml = (order.items || []).map(item => `
              <div class="flex items-start justify-between py-2 border-b border-white/5 last:border-b-0">
                <div class="flex-1 pr-3">
                  <div class="text-base font-bold text-white tracking-tight leading-snug">${item.name}</div>
                  <div class="text-[10px] font-mono text-[var(--text-muted)] uppercase tracking-wider mt-0.5">${item.menu_item?.category || 'Kitchen'}</div>
                  ${item.notes ? `<div class="mt-1 px-2 py-0.5 rounded-md bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-semibold inline-block"><i class="fa-solid fa-pen mr-1"></i>${item.notes}</div>` : ''}
                </div>
                <div class="px-2.5 py-1 rounded-lg bg-[var(--brand)] text-white font-mono font-black text-lg tracking-tight tabular-nums flex-shrink-0">
                  x${item.quantity}
                </div>
              </div>
            `).join('');

            const date = new Date(order.created_at);
            const timeStr = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

            return `
              <div class="kds-card ${cardBorderClass}" id="kds-card-${order.id}">
                <!-- Card Header -->
                <div class="p-4 bg-white/[0.03] border-b border-[var(--border)] flex items-center justify-between">
                  <div>
                    <div class="text-2xl font-black font-display text-white tracking-tight">${tableNum}</div>
                    <div class="text-xs font-mono text-[var(--text-muted)] mt-0.5"><i class="fa-regular fa-clock mr-1"></i>${timeStr} (#${order.id})</div>
                  </div>
                  <div>${urgencyBadge}</div>
                </div>

                <!-- Card Body (Items) -->
                <div class="p-4 flex-1 space-y-1.5 overflow-y-auto max-h-[360px]">
                  ${itemsHtml}
                  ${order.special_instructions ? `
                    <div class="mt-3 p-2.5 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300 text-xs font-medium">
                      <i class="fa-solid fa-note-sticky mr-1.5"></i> ${order.special_instructions}
                    </div>
                  ` : ''}
                </div>

                <!-- Card Footer (Status Progression Buttons) -->
                <div class="p-3 bg-black/30 border-t border-[var(--border)] flex items-center gap-2">
                  <button class="px-3 py-2 rounded-xl border border-[var(--border)] bg-[#1A1A1E] hover:bg-stone-800 text-xs font-mono text-white transition flex items-center justify-center gap-1.5" onclick="printKOT(${order.id})" title="Print KOT Ticket">
                    <i class="fa-solid fa-print"></i> KOT
                  </button>

                  ${order.status === 'preparing' || order.status === 'cooking' ? `
                    <span class="flex-1 py-2 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-bold font-mono text-center flex items-center justify-center gap-1.5">
                      <i class="fa-solid fa-fire text-amber-400"></i> Cooking
                    </span>
                  ` : `
                    <button class="flex-1 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition flex items-center justify-center gap-1.5" onclick="startCookingKOT(${order.id})">
                      <i class="fa-solid fa-fire"></i> Start
                    </button>
                  `}

                  <button class="flex-1 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center justify-center gap-1.5" onclick="completeKOT(${order.id})" title="Mark Ready / Served">
                    <i class="fa-solid fa-circle-check"></i> Ready
                  </button>

                  <button class="w-9 h-9 rounded-xl border border-rose-500/30 bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 flex items-center justify-center text-xs transition" onclick="cancelKOT(${order.id})" title="Reject / Cancel Order">
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
      }
    }

    function printKOT(orderId) {
      window.open('/orders/' + orderId + '/receipt/kot-combined', '_blank');
    }

    async function cancelKOT(orderId) {
      const reason = prompt('Cancellation reason for Order #' + orderId + ':', 'Kitchen item unavailable');
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

    // Refresh every 12 seconds with visibility check
    setInterval(() => {
      if (document.visibilityState === 'hidden') return;
      loadKOTs();
    }, 12000);

    // Initial fetch
    loadKOTs();
  </script>
</body>
</html>
