<?php

namespace App\Services;

use App\Models\Order;

/**
 * Service for Spooling Print Jobs to Multiple Thermal Printers (Customer Bill, Kitchen KOT, Bar KOT)
 * with character formatting, alignment tags, and printer status monitoring.
 */
class MultiPrinterSpoolerService
{
    public const PRINTER_TYPE_CUSTOMER_INVOICE = 'customer_invoice';
    public const PRINTER_TYPE_KITCHEN_KOT = 'kitchen_kot';
    public const PRINTER_TYPE_BAR_KOT = 'bar_kot';

    /**
     * Dispatch print jobs across all designated restaurant printers concurrently.
     *
     * @param Order $order Order model instance
     * @param array $options Routing options (e.g. skipBar, printBoth)
     * @return array Array of spool ticket payloads
     */
    public function spoolOrderPrintJobs(Order $order, array $options = []): array
    {
        $spoolJobs = [];

        // 1. Customer Thermal Invoice (Always generated on checkout)
        if (!($options['kot_only'] ?? false)) {
            $spoolJobs[] = [
                'printer_id' => 'PRINTER-COUNTER-01',
                'printer_type' => self::PRINTER_TYPE_CUSTOMER_INVOICE,
                'paper_width_mm' => 58,
                'job_id' => "INV-{$order->id}-" . time(),
                'payload' => $this->formatCustomerInvoiceText($order),
            ];
        }

        // 2. Kitchen KOT (Hot food, Curries, Breads)
        $kitchenItems = $order->items ? $order->items->filter(fn($i) => !str_contains(strtolower($i->name), 'lassi') && !str_contains(strtolower($i->name), 'drink')) : collect();
        if ($kitchenItems->isNotEmpty()) {
            $spoolJobs[] = [
                'printer_id' => 'PRINTER-KITCHEN-01',
                'printer_type' => self::PRINTER_TYPE_KITCHEN_KOT,
                'paper_width_mm' => 80,
                'job_id' => "KOT-KITCHEN-{$order->id}-" . time(),
                'payload' => $this->formatKotText($order, $kitchenItems, 'KITCHEN KOT'),
            ];
        }

        // 3. Bar KOT (Beverages, Shakes, Mocktails)
        $barItems = $order->items ? $order->items->filter(fn($i) => str_contains(strtolower($i->name), 'lassi') || str_contains(strtolower($i->name), 'drink') || str_contains(strtolower($i->name), 'beverage')) : collect();
        if ($barItems->isNotEmpty()) {
            $spoolJobs[] = [
                'printer_id' => 'PRINTER-BAR-01',
                'printer_type' => self::PRINTER_TYPE_BAR_KOT,
                'paper_width_mm' => 58,
                'job_id' => "KOT-BAR-{$order->id}-" . time(),
                'payload' => $this->formatKotText($order, $barItems, 'BAR KOT'),
            ];
        }

        return [
            'order_id' => $order->id,
            'total_jobs_spooled' => count($spoolJobs),
            'spool_jobs' => $spoolJobs,
        ];
    }

    /**
     * Format plaintext thermal receipt layout.
     */
    protected function formatCustomerInvoiceText(Order $order): string
    {
        $lines = [];
        $lines[] = "================================";
        $lines[] = "     DINEFLOW GOURMET REST.     ";
        $lines[] = "  Patan, Gujarat - Ph: 9876543210 ";
        $lines[] = "================================";
        $lines[] = "Order #: {$order->id}   Table: {$order->table_id}";
        $lines[] = "Date: " . ($order->created_at ? $order->created_at->format('d/m/Y H:i') : date('d/m/Y H:i'));
        $lines[] = "--------------------------------";
        $lines[] = "Item             Qty   Price";
        $lines[] = "--------------------------------";

        if ($order->items) {
            foreach ($order->items as $item) {
                $name = str_pad(substr($item->name, 0, 16), 16);
                $qty = str_pad($item->quantity, 4, ' ', STR_PAD_LEFT);
                $price = str_pad(number_format($item->price * $item->quantity, 2), 10, ' ', STR_PAD_LEFT);
                $lines[] = "{$name}{$qty}{$price}";
            }
        }

        $lines[] = "--------------------------------";
        $lines[] = "Subtotal:        ₹" . number_format($order->subtotal ?? $order->total_amount, 2);
        $lines[] = "GST (5%):        ₹" . number_format($order->tax_amount ?? 0, 2);
        $lines[] = "Grand Total:     ₹" . number_format($order->total_amount, 2);
        $lines[] = "================================";
        $lines[] = "      THANK YOU! VISIT AGAIN    ";
        $lines[] = "================================";

        return implode("\n", $lines);
    }

    /**
     * Format Kitchen Order Ticket (KOT).
     */
    protected function formatKotText(Order $order, $items, string $headerTitle): string
    {
        $lines = [];
        $lines[] = "********************************";
        $lines[] = "         {$headerTitle}         ";
        $lines[] = "********************************";
        $lines[] = "KOT #: {$order->id}   Table: {$order->table_id}";
        $lines[] = "Time: " . date('H:i:s');
        if (!empty($order->special_instructions)) {
            $lines[] = "NOTE: {$order->special_instructions}";
        }
        $lines[] = "--------------------------------";
        $lines[] = "Qty   Item Name";
        $lines[] = "--------------------------------";

        foreach ($items as $item) {
            $lines[] = "[{$item->quantity}]   {$item->name}";
        }

        $lines[] = "********************************";
        return implode("\n", $lines);
    }
}
