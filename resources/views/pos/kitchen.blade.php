<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DineFlow AI - Kitchen Display System</title>
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

  <div id="soundPermission" onclick="enableSound()">
    <i class="fa-solid fa-volume-high"></i> Click here to enable Sound Alerts for new KOTs!
  </div>

  <header>
    <h1><img src="/images/dineflow_logo.jpg" alt="Logo" style="width: 28px; height: 28px; border-radius: 6px; vertical-align: middle; margin-right: 8px;"> KOT Monitor</h1>
    <div class="status-badge" id="syncStatus">
      <i class="fa-solid fa-circle-dot fa-fade"></i> Live Monitoring
    </div>
  </header>

  <div class="kds-grid" id="kdsGrid">
    <div class="no-orders">
      <i class="fa-solid fa-spinner fa-spin"></i>
      <p>Initializing KOT screen...</p>
    </div>
  </div>

  <script>
    let lastKnownOrderId = 0;
    let isSoundEnabled = false;
    let processedOrders = new Set();

    // Sound alert using Web Audio API
    async function playChime() {
      if (!isSoundEnabled) return;
      try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        if (audioCtx.state === 'suspended') {
          await audioCtx.resume();
        }
        
        // Note 1 (E5)
        const osc1 = audioCtx.createOscillator();
        const gain1 = audioCtx.createGain();
        osc1.connect(gain1);
        gain1.connect(audioCtx.destination);
        osc1.frequency.value = 659.25; // E5
        gain1.gain.setValueAtTime(0.4, audioCtx.currentTime);
        gain1.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
        osc1.start();
        osc1.stop(audioCtx.currentTime + 0.3);

        // Note 2 (G5) at 0.15s
        setTimeout(async () => {
          try {
            if (audioCtx.state === 'suspended') {
              await audioCtx.resume();
            }
            const osc2 = audioCtx.createOscillator();
            const gain2 = audioCtx.createGain();
            osc2.connect(gain2);
            gain2.connect(audioCtx.destination);
            osc2.frequency.value = 783.99; // G5
            gain2.gain.setValueAtTime(0.4, audioCtx.currentTime);
            gain2.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
            osc2.start();
            osc2.stop(audioCtx.currentTime + 0.4);
          } catch (err) {
            console.log('Osc2 audio context error:', err);
          }
        }, 150);
      } catch (e) {
        console.log('Audio playback failed:', e);
      }
    }

    function enableSound() {
      isSoundEnabled = true;
      document.getElementById('soundPermission').style.display = 'none';
      playChime();
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

    async function loadKOTs() {
      try {
        const statusBadge = document.getElementById('syncStatus');
        statusBadge.style.color = '#F59E0B';
        statusBadge.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing';

        const response = await fetch('/api/orders/restaurant/1');
        const data = await response.json();
        
        statusBadge.style.color = 'var(--success)';
        statusBadge.innerHTML = '<i class="fa-solid fa-circle-dot fa-fade"></i> Live Monitoring';

        if (data.success && Array.isArray(data.data)) {
          // Show all active orders that are pending, preparing or served (both paid and unpaid)
          const activeOrders = data.data.filter(order => order.status !== 'completed' && order.status !== 'cancelled' && order.status !== 'served');
          
          if (activeOrders.length === 0) {
            document.getElementById('kdsGrid').innerHTML = `
              <div class="no-orders">
                <i class="fa-solid fa-utensils"></i>
                <p>No active KOTs found.<br><span style="font-size:11px;opacity:0.6;">Waiting for new orders...</span></p>
              </div>
            `;
            return;
          }

          // Check if there is a new order
          let hasNewOrder = false;
          activeOrders.forEach(order => {
            if (!processedOrders.has(order.id)) {
              processedOrders.add(order.id);
              if (lastKnownOrderId !== 0 && order.id > lastKnownOrderId) {
                hasNewOrder = true;
              }
            }
          });

          // Update last known ID
          const orderIds = activeOrders.map(o => o.id);
          if (orderIds.length > 0) {
            const maxId = Math.max(...orderIds);
            if (maxId > lastKnownOrderId) {
              lastKnownOrderId = maxId;
            }
          }

          if (hasNewOrder) {
            playChime();
          }

          // Render KDS cards
          document.getElementById('kdsGrid').innerHTML = activeOrders.map(order => {
            const tableNum = order.table ? order.table.table_number : 'Table #' + order.table_id;
            const itemsHtml = order.items.map(item => `
              <div class="item-row">
                <div>
                  <span class="item-name">${item.name}</span>
                  <span class="item-category">${item.menu_item?.category || 'food'}</span>
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
                </div>
                <div class="card-footer">
                  <button class="btn btn-warning" onclick="printKOT(${order.id})">
                    <i class="fa-solid fa-print"></i> Print KOT
                  </button>
                  <button class="btn btn-success" onclick="completeKOT(${order.id})">
                    <i class="fa-solid fa-circle-check"></i> Done
                  </button>
                </div>
              </div>
            `;
          }).join('');
        }
      } catch (err) {
        console.log('Error refreshing KDS:', err);
      }
    }

    function printKOT(orderId) {
      // Opens the combined KOT print dialog directly in a new window/tab
      window.open('/orders/' + orderId + '/receipt/kot-combined', '_blank');
    }

    async function completeKOT(orderId) {
      if (confirm('Mark Order #' + orderId + ' as completed?')) {
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
            body: JSON.stringify({ status: 'completed' })
          });
          const resData = await response.json();
          if (resData.success) {
            processedOrders.delete(orderId);
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

    // Refresh every 5 seconds
    setInterval(loadKOTs, 5000);
    // Initial fetch
    loadKOTs();
  </script>
</body>
</html>
