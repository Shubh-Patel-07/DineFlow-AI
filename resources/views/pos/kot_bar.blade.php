<!DOCTYPE html>
<html lang="{{ $lang ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $kotDict['bar_kot'] ?? 'Bar KOT' }} - Order #{{ $order->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Noto+Sans+Gujarati:wght@500;700;800&family=Noto+Sans+Devanagari:wght@500;700;800&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Courier Prime', 'Noto Sans Gujarati', 'Noto Sans Devanagari', 'Courier New', monospace;
            width: 58mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
            background: #fff;
            font-size: 13px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            border: 2px solid #000;
            padding: 4px;
            letter-spacing: 0.5px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .info-table td {
            padding: 2px 0;
        }

        .items-table th {
            border-bottom: 1px dashed #000;
            text-align: left;
            padding: 4px 0;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 6px 0;
            vertical-align: top;
            font-size: 14px;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                width: 58mm;
                margin: 0;
                padding: 5px;
            }
        }

        .print-btn-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .print-btn {
            background: #3B82F6;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">{{ $kotDict['print_btn_bar'] ?? '🖨️ Print Bar KOT' }}</button>
    </div>

    <div class="header">
        @php
            $logoUrl = isset($restaurant) ? $restaurant->getEffectiveLogoUrl() : null;
        @endphp
        @if(!empty($logoUrl))
            <img src="{{ $logoUrl }}" alt="{{ $restaurant->name ?? 'Restaurant' }}" style="max-height: 36px; max-width: 130px; object-fit: contain; margin-bottom: 4px;" onerror="this.style.display='none'">
        @endif
        @if(!empty($restaurant->name))
            <h3 style="font-size: 15px; font-weight: 800; text-transform: uppercase; margin: 0 0 2px;">{{ $restaurant->name }}</h3>
        @endif
        @if(!empty($restaurant->phone))
            <p style="font-size: 10px; margin: 0 0 5px;">Ph: {{ $restaurant->phone }}</p>
        @endif
        <h2>{{ $kotDict['bar_kot'] ?? 'BAR KOT' }}</h2>
        <p style="margin-top: 5px; font-weight: bold; font-size: 11px;">{{ $kotDict['drinks_only'] ?? '*** DRINKS & DESSERTS ONLY ***' }}</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td><strong>{{ $kotDict['order_id'] ?? 'Order ID' }}:</strong></td>
            <td class="text-right">#{{ $order->id }}</td>
        </tr>
        <tr>
            <td><strong>{{ $kotDict['table'] ?? 'Table' }}:</strong></td>
            <td class="text-right" style="font-size: 16px; font-weight: 800;">{{ $tableName }}</td>
        </tr>
        <tr>
            <td><strong>{{ $kotDict['time'] ?? 'Time' }}:</strong></td>
            <td class="text-right">{{ $order->created_at->format('H:i') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>{{ $kotDict['item_name'] ?? 'Item Name' }}</th>
                <th class="text-right">{{ $kotDict['qty'] ?? 'Qty' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barItems as $item)
            <tr>
                <td>
                    {{ $item->name }}
                    @if(!empty($item->notes))
                        <div style="font-size: 11px; font-weight: 700; color: #000; background: #eee; padding: 2px 4px; margin-top: 3px; border-left: 2px solid #000; letter-spacing: 0.3px;">
                            👉 [NOTE: {{ $item->notes }}]
                        </div>
                    @endif
                </td>
                <td class="text-right" style="font-size: 16px;">{{ $item->quantity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center; font-weight: normal; font-size: 11px;">{{ $kotDict['no_items'] ?? 'No drinks/dessert items' }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="divider"></div>

    @if($order->special_instructions)
    <div style="border: 1px solid #000; padding: 6px; margin-top: 8px;">
        <strong style="font-size: 11px;">{{ $kotDict['instructions'] ?? 'INSTRUCTIONS' }}:</strong><br>
        <span style="font-size: 12px; font-weight: bold;">{{ $order->special_instructions }}</span>
    </div>
    <div class="divider"></div>
    @endif

    <div class="header" style="margin-top: 12px;">
        <p style="font-size: 9.5px; font-weight: 700;">{{ $restaurant->name ?? 'DineFlow POS' }} - Bar Slip</p>
        <p style="font-size: 8px; opacity: 0.6;">Powered by DineFlow AI POS</p>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
