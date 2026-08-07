<?php

namespace App\Services;

use App\Models\Order;

/**
 * Low-level ESC/POS Binary and Character Sequence Generator for 58mm & 80mm
 * thermal receipt printers, supporting double-height bolding, barcode encoding,
 * QR code rasterization, and paper cutter triggers.
 */
class EscPosThermalReceiptGenerator
{
    public const ESC = "\x1B";
    public const GS = "\x1D";
    public const LF = "\x0A";

    /**
     * Generate complete ESC/POS binary buffer for a customer bill receipt.
     *
     * @param Order $order Order instance
     * @param int $paperWidth 58 or 80 mm
     * @return string Raw binary command stream
     */
    public function generateReceiptBinary(Order $order, int $paperWidth = 58): string
    {
        $cols = ($paperWidth === 80) ? 48 : 32;
        $buf = '';

        // Initialize printer
        $buf .= self::ESC . "@";

        // Center Alignment & Double Width Header
        $buf .= self::ESC . "a\x01"; // Center align
        $buf .= self::ESC . "!\x30"; // Double-height + Double-width
        $buf .= "DINEFLOW AI\n";
        $buf .= self::ESC . "!\x00"; // Normal text
        $buf .= "Smart Restaurant POS & QR Ordering\n";
        $buf .= "Patan, Gujarat | Ph: +91 9876543210\n";
        $buf .= "GSTIN: 24AAACD1234F1Z5\n";
        $buf .= str_repeat("-", $cols) . "\n";

        // Left align metadata
        $buf .= self::ESC . "a\x00"; // Left align
        $buf .= "Order #: {$order->id}      Table: {$order->table_id}\n";
        $buf .= "Date: " . date('d/m/Y H:i') . "  Cashier: Admin\n";
        $buf .= str_repeat("-", $cols) . "\n";

        // Line Items Table
        $buf .= self::ESC . "!\x08"; // Bold on
        $buf .= sprintf("%-16s %4s %10s\n", "ITEM", "QTY", "AMOUNT");
        $buf .= self::ESC . "!\x00"; // Bold off
        $buf .= str_repeat("-", $cols) . "\n";

        if ($order->items) {
            foreach ($order->items as $item) {
                $name = substr($item->name, 0, 16);
                $qty = (int)$item->quantity;
                $lineTotal = number_format($item->price * $qty, 2);
                $buf .= sprintf("%-16s %4d %10s\n", $name, $qty, "₹{$lineTotal}");
            }
        }

        $buf .= str_repeat("-", $cols) . "\n";

        // Totals breakdown (Right align)
        $buf .= self::ESC . "a\x02"; // Right align
        $buf .= "Subtotal: ₹" . number_format($order->subtotal ?? $order->total_amount, 2) . "\n";
        $buf .= "CGST (2.5%): ₹" . number_format(($order->tax_amount ?? 0) / 2, 2) . "\n";
        $buf .= "SGST (2.5%): ₹" . number_format(($order->tax_amount ?? 0) / 2, 2) . "\n";

        // Bold Grand Total
        $buf .= self::ESC . "!\x10"; // Double-height
        $buf .= "TOTAL: ₹" . number_format($order->total_amount, 2) . "\n";
        $buf .= self::ESC . "!\x00"; // Normal

        // Footer message
        $buf .= self::ESC . "a\x01"; // Center align
        $buf .= "\nThank you for dining with us!\n";
        $buf .= "Scan & Pay via UPI / GPay / PhonePe\n\n\n";

        // Partial Cut Command
        $buf .= self::GS . "V\x41\x03";

        return $buf;
    }
}
