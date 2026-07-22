<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Receipt Printer Simulator - DineFlow AI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Courier New', Courier, monospace;
            background: radial-gradient(circle at top, #1E293B 0%, #0F172A 60%, #090D16 100%);
            min-height: 100vh;
            color: #000000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 25px 15px 80px;
        }

        /* Glassmorphic Header Toolbar */
        .toolbar-container {
            width: 100%;
            max-width: 580px;
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
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            z-index: 100;
        }

        .btn-ctrl {
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

        .btn-ctrl:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.4);
        }

        .btn-print-feed { background: linear-gradient(135deg, #10B981, #059669); }
        .btn-toggle-roll { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .btn-hard-print { background: linear-gradient(135deg, #6366F1, #4F46E5); }

        /* 3D Physical POS Printer Machine Unit */
        .pos-hardware-unit {
            width: 100%;
            max-width: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Heavy Dark Metallic Printer Chassis */
        .printer-chassis {
            width: 100%;
            background: linear-gradient(180deg, #111827 0%, #0F172A 100%);
            border: 3px solid #374151;
            border-radius: 24px 24px 8px 8px;
            padding: 20px 24px 14px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.8), inset 0 2px 4px rgba(255,255,255,0.1);
            position: relative;
            z-index: 30;
        }

        .chassis-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding-bottom: 12px;
        }

        .brand-badge {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #34D399;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.5px;
        }

        .led-panel {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(0,0,0,0.4);
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .led-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .led-power { background: #10B981; box-shadow: 0 0 10px #10B981; }
        .led-feed { background: #3B82F6; box-shadow: 0 0 10px #3B82F6; }
        .led-busy { background: #EF4444; box-shadow: 0 0 10px #EF4444; }

        /* Metallic Paper Exit Mouth / Serrated Teeth Slot */
        .printer-mouth-slot {
            width: 100%;
            height: 14px;
            background: #090D16;
            border-radius: 6px;
            border-bottom: 3px dashed #64748B;
            box-shadow: inset 0 4px 8px rgba(0,0,0,0.9);
            position: relative;
        }

        /* Paper Output Viewport Slot */
        .paper-exit-viewport {
            width: 100%;
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.25);
            border-radius: 0 0 20px 20px;
            padding: 0 0 35px;
            border-left: 2px solid rgba(255,255,255,0.05);
            border-right: 2px solid rgba(255,255,255,0.05);
            border-bottom: 2px solid rgba(255,255,255,0.05);
        }

        /* Thermal Paper Card Rolling Out */
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
            .no-print, .toolbar-container, .printer-chassis {
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
            .paper-exit-viewport {
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

    <!-- Floating Top Control Bar -->
    <div class="toolbar-container no-print">
        <button class="btn-ctrl btn-print-feed" onclick="triggerThermalPrintFeed()">
            <i class="fa-solid fa-play"></i> ▶️ Start Thermal Print Feed
        </button>
        <button class="btn-ctrl btn-toggle-roll" onclick="togglePaperWidth()" id="widthBtn">
            <i class="fa-solid fa-ruler-horizontal"></i> 📏 Switch to 80mm Roll
        </button>
        <button class="btn-ctrl btn-hard-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> 🖨️ Save PDF / Print Roll
        </button>
    </div>

    <!-- 3D Hardware Thermal Printer Device Unit -->
    <div class="pos-hardware-unit">
        <div class="printer-chassis no-print">
            <div class="chassis-top-row">
                <div class="brand-badge">
                    <i class="fa-solid fa-print" style="font-size: 18px;"></i>
                    DINEFLOW ESC/POS PRINTER TP-80
                </div>
                <div class="led-panel">
                    <span class="led-indicator led-power" id="powerLed" title="POWER"></span>
                    <span class="led-indicator led-feed" id="feedLed" title="FEED"></span>
                    <span class="led-indicator led-busy" id="busyLed" style="opacity: 0.3;" title="PRINTING"></span>
                </div>
            </div>
            <!-- Mouth Slot where paper emerges -->
            <div class="printer-mouth-slot"></div>
        </div>

        <!-- Viewport where paper rolls down out of the printer mouth slot -->
        <div class="paper-exit-viewport">
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
