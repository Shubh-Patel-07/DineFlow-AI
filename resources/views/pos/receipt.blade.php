<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Thermal Receipt Printer Device - DineFlow AI</title>
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            background: radial-gradient(circle at top, #1E293B 0%, #0F172A 50%, #090D16 100%);
            min-height: 100vh;
            color: #000000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 25px 15px 80px;
        }

        /* Top Action Bar */
        .top-action-bar {
            width: 100%;
            max-width: 600px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            padding: 14px 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            z-index: 100;
        }

        .btn-action {
            border: none;
            border-radius: 12px;
            padding: 10px 18px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #FFFFFF;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }

        .btn-action:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.4);
        }

        .btn-feed { background: linear-gradient(135deg, #10B981, #059669); }
        .btn-toggle { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .btn-print { background: linear-gradient(135deg, #6366F1, #4F46E5); }

        /* 3D Physical POS Thermal Printer Machine Frame */
        .hardware-printer-container {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Metallic Dark Printer Body Chassis */
        .printer-body-3d {
            width: 100%;
            background: linear-gradient(180deg, #1E293B 0%, #0F172A 60%, #090D16 100%);
            border: 3px solid #334155;
            border-radius: 28px 28px 12px 12px;
            padding: 22px 24px 16px;
            box-shadow: 0 35px 80px rgba(0, 0, 0, 0.85), inset 0 2px 5px rgba(255, 255, 255, 0.12);
            position: relative;
            z-index: 30;
        }

        /* Top Brand & Model Header */
        .printer-top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 12px;
        }

        .printer-model-name {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #34D399;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }

        /* Control Panel LED & Buttons on Printer Body */
        .printer-controls-panel {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0, 0, 0, 0.5);
            padding: 8px 14px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .led-indicator-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .led-power { background: #10B981; box-shadow: 0 0 10px #10B981; }
        .led-feed { background: #3B82F6; box-shadow: 0 0 10px #3B82F6; }
        .led-busy { background: #EF4444; box-shadow: 0 0 10px #EF4444; opacity: 0.3; }

        /* Hardware FEED Button on Printer Machine Body */
        .hardware-feed-btn {
            background: #334155;
            color: #F8FAFC;
            border: 1px solid #475569;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-size: 10px;
            font-weight: 800;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: all 0.15s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }

        .hardware-feed-btn:active {
            transform: scale(0.95);
            background: #1E293B;
        }

        /* Paper Chamber Hood & Glass Window */
        .paper-chamber-lid {
            width: 100%;
            height: 48px;
            background: rgba(15, 23, 42, 0.7);
            border: 2px dashed rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            position: relative;
            overflow: hidden;
        }

        .paper-roll-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #94A3B8;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 600;
        }

        .roll-icon-graphic {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 3px solid #64748B;
            background: #CBD5E1;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 4px rgba(0,0,0,0.5);
        }

        .roll-center-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #475569;
        }

        .latch-release-btn {
            background: #1E293B;
            color: #64748B;
            border: 1px solid #334155;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 800;
        }

        /* Metallic Serrated Teeth Cutter Slot Mouth */
        .cutter-mouth-slot {
            width: 100%;
            height: 16px;
            background: #090D16;
            border-radius: 8px;
            border-bottom: 4px dashed #64748B;
            box-shadow: inset 0 4px 8px rgba(0,0,0,0.95);
            position: relative;
        }

        /* Viewport Paper Mouth Slot */
        .paper-output-viewport {
            width: 100%;
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 0 0 24px 24px;
            padding: 0 0 35px;
            border-left: 2px solid rgba(255,255,255,0.06);
            border-right: 2px solid rgba(255,255,255,0.06);
            border-bottom: 2px solid rgba(255,255,255,0.06);
        }

        /* Thermal Receipt Paper Roll Card */
        .thermal-receipt-paper {
            background: #FFFFFF;
            width: 58mm;
            padding: 20px 14px 28px;
            color: #000000;
            box-shadow: 0 20px 50px rgba(0,0,0,0.7), 0 0 0 1px rgba(0,0,0,0.08);
            border-bottom: 8px dashed #94A3B8;
            font-size: 11.5px;
            line-height: 1.4;
            transform-origin: top center;
            transition: width 0.3s ease, transform 1.5s cubic-bezier(0.1, 0.9, 0.2, 1);
        }

        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 15px; text-transform: uppercase; font-family: 'Courier New', monospace; font-weight: 800; }
        .header p { margin: 2px 0; font-size: 10px; }

        .divider { border-top: 1px dashed #000000; margin: 8px 0; }

        .info-table, .items-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .info-table td { padding: 2px 0; }
        .items-table th { border-bottom: 1px dashed #000000; text-align: left; padding: 4px 0; font-weight: 800; }
        .items-table td { padding: 4px 0; vertical-align: top; }

        .text-right { text-align: right; }
        .total-section { margin-top: 8px; font-size: 11.5px; font-weight: bold; }

        .barcode { text-align: center; margin: 14px 0 6px 0; font-size: 10px; }
        .footer { text-align: center; margin-top: 12px; font-size: 9.5px; }

        /* @media print: Hide hardware printer shell & buttons completely */
        @media print {
            .no-print, .top-action-bar, .printer-body-3d {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            body {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .paper-output-viewport {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
                background: transparent !important;
                border: none !important;
            }
            .thermal-receipt-paper {
                box-shadow: none !important;
                border-bottom: none !important;
                width: 58mm !important;
                padding: 4px !important;
                margin: 0 auto !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Top Bar Toolbar Controls -->
    <div class="top-action-bar no-print">
        <button class="btn-action btn-feed" onclick="triggerThermalPrintFeed()">
            <i class="fa-solid fa-play"></i> ▶️ Start Thermal Print Feed
        </button>
        <button class="btn-action btn-toggle" onclick="togglePaperWidth()" id="widthBtn">
            <i class="fa-solid fa-ruler-horizontal"></i> 📏 Switch to 80mm Roll
        </button>
        <button class="btn-action btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> 🖨️ Save PDF / Print Roll
        </button>
    </div>

    <!-- 3D Hardware Thermal Printer Machine Unit -->
    <div class="hardware-printer-container">
        <!-- Main Metallic Printer Casing -->
        <div class="printer-body-3d no-print">
            <div class="printer-top-bar">
                <div class="printer-model-name">
                    <i class="fa-solid fa-print" style="font-size: 18px;"></i>
                    DINEFLOW POS THERMAL PRINTER TP-80
                </div>
                <div class="printer-controls-panel">
                    <span class="led-indicator-dot led-power" title="POWER"></span>
                    <span class="led-indicator-dot led-feed" title="FEED"></span>
                    <span class="led-indicator-dot led-busy" id="busyLed" title="PRINTING"></span>
                    <button class="hardware-feed-btn" onclick="triggerThermalPrintFeed()">FEED</button>
                </div>
            </div>

            <!-- Paper Roll Chamber Lid -->
            <div class="paper-chamber-lid">
                <div class="paper-roll-preview">
                    <div class="roll-icon-graphic">
                        <div class="roll-center-dot"></div>
                    </div>
                    <span>58mm/80mm Thermal Paper Roll Chamber</span>
                </div>
                <div class="latch-release-btn">PUSH LATCH</div>
            </div>

            <!-- Serrated Cutter Exit Mouth Slot -->
            <div class="cutter-mouth-slot"></div>
        </div>

        <!-- Viewport where paper rolls down out of the printer mouth slot -->
        <div class="paper-output-viewport">
            <div class="thermal-receipt-paper" id="paperRollCard">
                <div class="header">
                    <h2>{{ $restaurant->name }}</h2>
                    <p>{{ $restaurant->address }}</p>
                    <p>Ph: {{ $restaurant->phone }}</p>
                </div>

                <div class="divider"></div>

                <table class="info-table">
                    <tr>
                        <td><strong>Order ID:</strong></td>
                        <td class="text-right">#{{ $order->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Table:</strong></td>
                        <td class="text-right">{{ $tableName }}</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td class="text-right">{{ optional($order->created_at)->format('d-M-Y H:i') ?? now()->format('d-M-Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td class="text-right">{{ strtoupper($order->status) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Payment:</strong></td>
                        <td class="text-right">{{ strtoupper($order->payment_status) }} ({{ strtoupper($order->payment_method) }})</td>
                    </tr>
                </table>

                <div class="divider"></div>

                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-right">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="divider"></div>

                <table class="info-table total-section">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">₹{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                    @php
                        $serviceCharge = $order->total_amount * 0.05; // 5% Service Charge
                        $taxableAmount = $order->total_amount + $serviceCharge;
                        $cgst = $taxableAmount * 0.09; // 9% CGST (18% Total GST)
                        $sgst = $taxableAmount * 0.09; // 9% SGST (18% Total GST)
                        $grandTotal = round($taxableAmount + $cgst + $sgst);
                    @endphp
                    <tr>
                        <td>Service Charge (5%):</td>
                        <td class="text-right">₹{{ number_format($serviceCharge, 2) }}</td>
                    </tr>
                    <tr>
                        <td>CGST (9%):</td>
                        <td class="text-right">₹{{ number_format($cgst, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SGST (9%):</td>
                        <td class="text-right">₹{{ number_format($sgst, 2) }}</td>
                    </tr>
                    <tr style="font-size: 12.5px;">
                        <td>GRAND TOTAL:</td>
                        <td class="text-right">₹{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </table>

                <div class="divider"></div>

                <div class="barcode">
                    <p>*DINEFLOW-{{ $order->id }}*</p>
                    <p style="font-size: 8px; margin-top: 4px; opacity: 0.7;">Powered by DineFlow AI POS</p>
                </div>

                <div class="footer">
                    <p>Thank You for Dining With Us!</p>
                    <p>Please Visit Again!</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentWidth = 58;

        function togglePaperWidth() {
            const paper = document.getElementById('paperRollCard');
            const btn = document.getElementById('widthBtn');
            if (currentWidth === 58) {
                paper.style.width = '80mm';
                currentWidth = 80;
                btn.innerHTML = '<i class="fa-solid fa-ruler-horizontal"></i> 📏 Switch to 58mm Roll';
            } else {
                paper.style.width = '58mm';
                currentWidth = 58;
                btn.innerHTML = '<i class="fa-solid fa-ruler-horizontal"></i> 📏 Switch to 80mm Roll';
            }
        }

        function playThermalMotorAudio() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(170, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(70, ctx.currentTime + 1.4);
                gain.gain.setValueAtTime(0.09, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.4);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 1.4);
            } catch(e){}
        }

        function triggerThermalPrintFeed() {
            const paper = document.getElementById('paperRollCard');
            const busyLed = document.getElementById('busyLed');
            if (!paper) return;

            // Flash RED PRINTING LED during printing
            if (busyLed) busyLed.style.opacity = '1';

            playThermalMotorAudio();

            // Retract paper into printer mouth slot
            paper.style.transition = 'none';
            paper.style.transform = 'translateY(-100%)';
            paper.style.opacity = '0.05';

            // Feed paper smoothly out of the printer cutter slot
            setTimeout(() => {
                paper.style.transition = 'all 1.5s cubic-bezier(0.1, 0.9, 0.2, 1)';
                paper.style.transform = 'translateY(0)';
                paper.style.opacity = '1';

                setTimeout(() => {
                    if (busyLed) busyLed.style.opacity = '0.3';
                }, 1500);
            }, 60);
        }

        window.onload = function() {
            console.log('Photorealistic 3D Thermal Printer Device ready.');
        };
    </script>
</body>
</html>
