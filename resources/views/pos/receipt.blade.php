<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Order #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0 auto;
            padding: 10px;
            color: #000;
            background: #fff;
            font-size: 12px;
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
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .info-table, .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
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
            padding: 4px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }

        .barcode {
            text-align: center;
            margin: 15px 0 5px 0;
            font-size: 10px;
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
            background: #4F46E5;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>
    </div>

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
            <td class="text-right">{{ $order->created_at->format('d-M-Y H:i') }}</td>
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
            $grandTotal = $taxableAmount + $cgst + $sgst;
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
        <tr style="font-size: 13px;">
            <td>GRAND TOTAL:</td>
            <td class="text-right">₹{{ number_format($grandTotal, 2) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="barcode">
        <p>*DINEFLOW-{{ $order->id }}*</p>
        <p style="font-size: 8px; margin-top: 5px; opacity: 0.7;">Powered by DineFlow AI POS</p>
    </div>

    <div class="footer">
        <p>Thank You for Dining With Us!</p>
        <p>Please Visit Again!</p>
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
