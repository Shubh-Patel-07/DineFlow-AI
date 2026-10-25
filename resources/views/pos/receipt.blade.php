<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #{{ $order->id }}</title>
    <!-- Google Fonts & FontAwesome -->
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
            padding: 30px 15px 80px;
        }

        /* Top Action Bar Toolbar */
        .top-action-bar {
            width: 100%;
            max-width: 560px;
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 18px;
            padding: 12px 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
            z-index: 100;
        }

        .btn-action {
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #FFFFFF;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            filter: brightness(1.15);
        }

        .btn-feed { background: linear-gradient(135deg, #10B981, #059669); }
        .btn-toggle { background: linear-gradient(135deg, #F59E0B, #D97706); }
        .btn-print { background: linear-gradient(135deg, #6366F1, #4F46E5); }

        /* 3D Hardware Thermal Printer Assembly Unit */
        .printer-3d-hardware-unit {
            width: 100%;
            max-width: 460px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        /* Top 3D Printer Casing Body (Layer 1) */
        .printer-top-chassis {
            width: 100%;
            background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
            border: 3px solid #334155;
            border-radius: 24px 24px 0 0;
            padding: 20px 22px 14px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.85);
            position: relative;
            z-index: 30;
        }

        .printer-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 800;
            color: #34D399;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.5px;
        }

        .led-panel {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .led-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        .led-power { background: #10B981; box-shadow: 0 0 8px #10B981; }
        .led-feed { background: #3B82F6; box-shadow: 0 0 8px #3B82F6; }
        .led-busy { background: #EF4444; box-shadow: 0 0 8px #EF4444; opacity: 0.3; }

        /* Paper Roll Chamber Window Inside Printer */
        .paper-chamber-box {
            width: 100%;
            height: 42px;
            background: rgba(15, 23, 42, 0.8);
            border: 1.5px dashed rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #94A3B8;
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 600;
        }

        .roll-core-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 3px solid #64748B;
            background: #CBD5E1;
        }

        /* Front Exit Mouth Slit & Cutter Lip (Layer 3 - Z-INDEX 50) */
        .cutter-mouth-lip {
            width: 100%;
            height: 18px;
            background: #090D16;
            border-bottom: 4px dashed #64748B;
            border-top: 2px solid #334155;
            box-shadow: 0 6px 12px rgba(0,0,0,0.8), inset 0 2px 4px rgba(0,0,0,0.9);
            position: relative;
            z-index: 50;
        }

        /* Paper Exit Viewport Container (Layer 2 - Z-INDEX 10) */
        .paper-exit-viewport {
            width: 100%;
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 10;
            margin-top: -6px;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.25);
            border-radius: 0 0 20px 20px;
            padding: 0 0 35px;
            border-left: 3px solid #334155;
            border-right: 3px solid #334155;
            border-bottom: 3px solid #334155;
        }

        /* Thermal Paper Roll Emerging Out of Slot */
        .thermal-receipt-paper {
            background: #FFFFFF;
            width: 58mm;
            padding: 20px 14px 28px;
            color: #000000;
            box-shadow: 0 20px 50px rgba(0,0,0,0.75), 0 0 0 1px rgba(0,0,0,0.06);
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

        @page {
            margin: 0;
            size: auto;
        }

        /* @media print: Hide hardware printer shell & buttons completely */
        @media print {
            @page {
                margin: 0;
            }
            html, body {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print, .top-action-bar, .printer-top-chassis, .cutter-mouth-lip {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
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

    <!-- 3D Hardware Thermal Printer Assembly Unit -->
    <div class="printer-3d-hardware-unit">
        <!-- Layer 1: Printer Top Chassis & Chamber -->
        <div class="printer-top-chassis no-print">
            <div class="printer-header-row">
                <div class="brand-name">
                    <i class="fa-solid fa-print" style="font-size: 16px;"></i>
                    DINEFLOW POS THERMAL PRINTER TP-80
                </div>
                <div class="led-panel">
                    <span class="led-dot led-power" title="POWER"></span>
                    <span class="led-dot led-feed" title="READY"></span>
                    <span class="led-dot led-busy" id="busyLed" title="PRINTING"></span>
                </div>
            </div>

            <!-- Paper Roll Chamber -->
            <div class="paper-chamber-box">
                <div class="roll-core-circle"></div>
                <span>Thermal Paper Roll Inside Printer Machine</span>
            </div>
        </div>

        <!-- Layer 3: Front Cutter Mouth Lip (Positioned OVER the Slot Mouth) -->
        <div class="cutter-mouth-lip no-print"></div>

        <!-- Layer 2: Viewport where receipt paper physically glides out from under the cutter lip -->
        <div class="paper-exit-viewport">
            <div class="thermal-receipt-paper" id="paperRollCard">
                <div class="header">
                    @php
                        $logoUrl = $restaurant->getEffectiveLogoUrl();
                    @endphp
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="{{ $restaurant->name }}" style="max-height: 40px; max-width: 140px; object-fit: contain; margin-bottom: 6px;" onerror="this.style.display='none'">
                    @endif
                    <h2>{{ $restaurant->name }}</h2>
                    <p>{{ $restaurant->address }}</p>
                    <p>Ph: {{ $restaurant->phone }}</p>
                    @if(!empty($restaurant->gstin))
                    <p style="font-weight: bold; font-size: 10px;">GSTIN: {{ $restaurant->gstin }}</p>
                    @endif
                    @if(!empty($restaurant->fssai_licence))
                    <p style="font-weight: bold; font-size: 9.5px;">FSSAI Lic: {{ $restaurant->fssai_licence }}</p>
                    @endif
                    <div style="margin-top:4px; font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:1px; border:1px solid #000; padding:2px 0;">
                        {{ !empty($order->customer_gstin) ? 'TAX INVOICE (B2B)' : 'RETAIL INVOICE' }}
                    </div>
                </div>

                <div class="divider"></div>

                <table class="info-table">
                    <tr>
                        <td><strong>Order ID:</strong></td>
                        <td class="text-right">#{{ $order->id }}</td>
                    </tr>
                    @if(!empty($order->customer_gstin))
                    <tr>
                        <td><strong>Billed To:</strong></td>
                        <td class="text-right">{{ $order->customer_company ?: ($order->customer_name ?: 'B2B Client') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Client GSTIN:</strong></td>
                        <td class="text-right"><strong>{{ $order->customer_gstin }}</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Table:</strong></td>
                        <td class="text-right">{{ $tableName ?? ($order->table ? ($order->table->table_number ?? $order->table->id) : ('Table #' . ($order->table_id ?? '1'))) }}</td>
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
                            <td>
                                {{ $item->name }}
                                @if(!empty($item->notes))
                                    <div style="font-size: 8.5px; font-weight: bold; color: #444;">* {{ $item->notes }}</div>
                                @endif
                            </td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-right">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="divider"></div>

                <table class="info-table total-section">
                    @php
                        $subtotal = $order->items->sum(fn($i) => $i->price * $i->quantity) ?: $order->total_amount;
                        $serviceCharge = $subtotal * 0.05;
                        $cgst = ($subtotal + $serviceCharge) * 0.09;
                        $sgst = ($subtotal + $serviceCharge) * 0.09;
                        $walletDiscount = floatval($order->wallet_discount ?? 0);
                        $grandTotal = max(0, $subtotal + $serviceCharge + $cgst + $sgst - $walletDiscount);
                    @endphp
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-right">₹{{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @if($walletDiscount > 0)
                    <tr style="color: #059669; font-weight: bold;">
                        <td>Wallet Redeemed:</td>
                        <td class="text-right">-₹{{ number_format($walletDiscount, 2) }}</td>
                    </tr>
                    @endif
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
                    @if(!empty($order->cashback_earned) && $order->cashback_earned > 0)
                    <tr style="border-top: 1px dotted #10B981; font-size: 9.5px; color: #047857;">
                        <td colspan="2" class="text-center" style="padding-top: 3px;">
                            🎉 <strong>Cashback Earned: ₹{{ number_format($order->cashback_earned, 2) }}</strong> credited to Wallet!
                        </td>
                    </tr>
                    @endif
                    @if($order->payment_method === 'split' && !empty($order->split_data))
                        @php
                            $splitObj = is_string($order->split_data) ? json_decode($order->split_data, true) : $order->split_data;
                        @endphp
                        @if(!empty($splitObj))
                            <tr style="border-top: 1px dashed #666;">
                                <td colspan="2" style="font-size:9.5px; padding-top:4px; color:#222;">
                                    <strong>Split Paid:</strong> 
                                    @if(!empty($splitObj['cash'])) Cash: ₹{{ number_format($splitObj['cash'], 2) }} @endif
                                    @if(!empty($splitObj['upi'])) | UPI: ₹{{ number_format($splitObj['upi'], 2) }} @endif
                                    @if(!empty($splitObj['card'])) | Card: ₹{{ number_format($splitObj['card'], 2) }} @endif
                                </td>
                            </tr>
                        @endif
                    @endif
                </table>

                <div class="divider"></div>

                <!-- Signature Section -->
                <div style="text-align: right; padding: 6px 0; margin-bottom: 4px;">
                    @if(!empty($isDemo))
                        <div style="font-family: 'Outfit', sans-serif; font-size: 13px; font-weight: 800; letter-spacing: 2px;">SMP</div>
                        <div style="font-size: 8.5px; font-weight: bold; border-top: 1px dashed #000; display: inline-block; padding-top: 2px; margin-top: 2px;">Authorized Signatory (SMP)</div>
                    @else
                        @php
                            $signImg = $restaurant->getEffectiveSignature();
                            $signName = $restaurant->getEffectiveSignatureName();
                        @endphp
                        @if(!empty($signImg))
                            <div style="margin-bottom: 2px;"><img src="{{ $signImg }}" alt="Signature" style="max-height: 26px; max-width: 110px; object-fit: contain;"></div>
                            <div style="font-size: 8.5px; font-weight: bold; border-top: 1px dashed #000; display: inline-block; padding-top: 2px;">Authorized Signatory @if(!empty($signName)) ({{ $signName }}) @endif</div>
                        @elseif(!empty($signName))
                            <div style="font-family: 'Great Vibes', cursive, sans-serif; font-size: 15px; margin-bottom: 2px;">{{ $signName }}</div>
                            <div style="font-size: 8.5px; font-weight: bold; border-top: 1px dashed #000; display: inline-block; padding-top: 2px;">Authorized Signatory ({{ $signName }})</div>
                        @else
                            <div style="font-size: 8.5px; font-weight: bold; border-top: 1px dashed #000; display: inline-block; padding-top: 2px; margin-top: 12px;">Authorized Signatory</div>
                        @endif
                    @endif
                </div>

                <div class="barcode">
                    <p>*DINEFLOW-{{ $order->id }}*</p>
                    <p style="font-size: 8px; margin-top: 4px; opacity: 0.7;">Powered by DineFlow AI POS</p>
                </div>

                <div class="footer">
                    <p>Thank You for Dining at {{ $restaurant->name }}!</p>
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

            // Retract paper completely inside the printer mouth slot behind the front lip
            paper.style.transition = 'none';
            paper.style.transform = 'translateY(-100%)';
            paper.style.opacity = '0';

            // Feed paper smoothly out of the slot behind the cutter lip
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
            console.log('3D Layered Physical POS Thermal Printer ready.');
        };
    </script>
</body>
</html>
