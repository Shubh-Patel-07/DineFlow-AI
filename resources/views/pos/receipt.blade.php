<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Receipt - Order #{{ $order->id }} - DineFlow AI</title>
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            background: linear-gradient(135deg, #090D16 0%, #111827 50%, #1E293B 100%);
            min-height: 100vh;
            color: #000000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 20px 15px 50px;
        }

        /* Hardware Printer Frame Container (Screen Only) */
        .printer-workbench {
            width: 100%;
            max-width: 520px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Top Action Bar */
        .top-action-bar {
            width: 100%;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 12px 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        }

        .action-btn {
            border: none;
            border-radius: 10px;
            padding: 9px 16px;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: #FFFFFF;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .btn-feed { background: linear-gradient(135deg, #10B981, #059669); }
        .btn-toggle { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .btn-print { background: linear-gradient(135deg, #6366F1, #4F46E5); }

        /* Hardware POS Printer Machine Shell */
        .printer-machine-head {
            width: 100%;
            max-width: 420px;
            background: #0F172A;
            border: 2px solid #334155;
            border-radius: 20px 20px 6px 6px;
            padding: 16px 20px 10px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            position: relative;
            z-index: 10;
        }

        .printer-header-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .printer-brand {
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            font-weight: 800;
            color: #34D399;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.5px;
        }

        .led-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .led-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .led-power { background: #10B981; box-shadow: 0 0 8px #10B981; }
        .led-feed { background: #3B82F6; box-shadow: 0 0 8px #3B82F6; }
        .led-error { background: #F59E0B; box-shadow: 0 0 8px #F59E0B; }

        /* Cutter Exit Slot Teeth */
        .cutter-slot {
            width: 100%;
            height: 12px;
            background: #1E293B;
            border-radius: 6px;
            border-bottom: 3px dashed #475569;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.5);
            position: relative;
        }

        /* Thermal Paper Container Card */
        .paper-viewport {
            width: 100%;
            display: flex;
            justify-content: center;
            position: relative;
            margin-top: -4px;
            z-index: 5;
            overflow: hidden;
            padding-bottom: 20px;
        }

        .thermal-paper-card {
            background: #FFFFFF;
            width: 58mm;
            padding: 16px 12px 24px;
            color: #000000;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5), 0 0 0 1px rgba(0,0,0,0.05);
            border-bottom: 6px dashed #CBD5E1;
            font-size: 11.5px;
            line-height: 1.4;
            transition: width 0.3s ease, transform 1.2s cubic-bezier(0.1, 0.9, 0.2, 1);
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

        /* @media print: Hide all printer hardware shells & buttons completely */
        @media print {
            .no-print, .printer-workbench, .printer-machine-head, .top-action-bar {
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
            .paper-viewport {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }
            .thermal-paper-card {
                box-shadow: none !important;
                border-bottom: none !important;
                width: 58mm !important;
                padding: 4px !important;
                margin: 0 auto !important;
            }
        }
    </style>
</head>
<body>

    <div class="printer-workbench no-print">
        <!-- Top Control Bar -->
        <div class="top-action-bar">
            <button class="action-btn btn-feed" onclick="runPaperFeedAnimation()">
                <i class="fa-solid fa-play"></i> Run Paper Feed Animation
            </button>
            <button class="action-btn btn-toggle" onclick="toggleRollWidth()" id="widthToggleBtn">
                <i class="fa-solid fa-ruler-horizontal"></i> Switch to 80mm Roll
            </button>
            <button class="action-btn btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Hard Copy / Save PDF
            </button>
        </div>

        <!-- ESC/POS Printer Hardware Unit Shell -->
        <div class="printer-machine-head">
            <div class="printer-header-flex">
                <div class="printer-brand">
                    <i class="fa-solid fa-print" style="font-size: 16px;"></i>
                    ESC/POS Thermal Printer TP-80
                </div>
                <div class="led-group">
                    <span class="led-dot led-power" title="POWER"></span>
                    <span class="led-dot led-feed" title="FEED"></span>
                    <span class="led-dot led-error" title="ONLINE"></span>
                </div>
            </div>
            <div class="cutter-slot"></div>
        </div>
    </div>

    <!-- Paper Output Exit Viewport -->
    <div class="paper-viewport">
        <div class="thermal-paper-card" id="thermalPaperContainer">
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

    <script>
        let currentWidth = 58;

        function toggleRollWidth() {
            const paper = document.getElementById('thermalPaperContainer');
            const btn = document.getElementById('widthToggleBtn');
            if (currentWidth === 58) {
                paper.style.width = '80mm';
                currentWidth = 80;
                btn.innerHTML = '<i class="fa-solid fa-ruler-horizontal"></i> Switch to 58mm Roll';
            } else {
                paper.style.width = '58mm';
                currentWidth = 58;
                btn.innerHTML = '<i class="fa-solid fa-ruler-horizontal"></i> Switch to 80mm Roll';
            }
        }

        function playThermalMotorSound() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(160, ctx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(75, ctx.currentTime + 1.3);
                gain.gain.setValueAtTime(0.08, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 1.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 1.3);
            } catch(e){}
        }

        function runPaperFeedAnimation() {
            const paper = document.getElementById('thermalPaperContainer');
            if (!paper) return;

            playThermalMotorSound();

            paper.style.transition = 'none';
            paper.style.transform = 'translateY(-140px)';
            paper.style.opacity = '0.1';

            setTimeout(() => {
                paper.style.transition = 'all 1.3s cubic-bezier(0.1, 0.9, 0.2, 1)';
                paper.style.transform = 'translateY(0)';
                paper.style.opacity = '1';
            }, 60);
        }
    </script>
</body>
</html>
