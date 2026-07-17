<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen KOT - Order #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
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
            font-size: 18px;
            text-transform: uppercase;
            border: 2px solid #000;
            padding: 4px;
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

        /* Hide screen elements during print */
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
            background: #EF4444;
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
        <button class="print-btn" onclick="window.print()">🖨️ Print Kitchen KOT</button>
    </div>

    <div class="header">
        <h2>KITCHEN KOT</h2>
        <p style="margin-top: 5px; font-weight: bold;">*** FOOD ITEMS ONLY ***</p>
    </div>

    <div class="divider"></div>

    <table class="info-table">
        <tr>
            <td><strong>Order ID:</strong></td>
            <td class="text-right">#{{ $order->id }}</td>
        </tr>
        <tr>
            <td><strong>Table:</strong></td>
            <td class="text-right" style="font-size: 16px; font-weight: 800;">{{ $tableName }}</td>
        </tr>
        <tr>
            <td><strong>Time:</strong></td>
            <td class="text-right">{{ $order->created_at->format('H:i') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Name</th>
                <th class="text-right">Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kitchenItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td class="text-right" style="font-size: 16px;">{{ $item->quantity }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center; font-weight: normal; font-size: 11px;">No food items for Kitchen</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="divider"></div>

    @if($order->special_instructions)
    <div style="border: 1px solid #000; padding: 6px; margin-top: 10px;">
        <strong>INSTRUCTIONS:</strong><br>
        <span style="font-size: 12px;">{{ $order->special_instructions }}</span>
    </div>
    <div class="divider"></div>
    @endif

    <div class="header" style="margin-top: 15px;">
        <p style="font-size: 9px; opacity: 0.7;">DineFlow AI POS - Kitchen Copy</p>
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
