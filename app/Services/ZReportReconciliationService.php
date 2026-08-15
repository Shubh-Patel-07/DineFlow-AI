<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Collection;

/**
 * End-of-Day Financial Z-Report Reconciliation Service for POS Cash Drawers,
 * Digital UPI settlement matching, Tax liability breakdown, and Shift Audits.
 */
class ZReportReconciliationService
{
    /**
     * Generate comprehensive daily Z-Report summary for a specific business date.
     *
     * @param Collection $orders Paid orders on that day
     * @param float $openingCashBalance Starting cash in drawer
     * @return array Z-Report financial reconciliation metrics
     */
    public function generateDailyZReport(Collection $orders, float $openingCashBalance = 2000.00): array
    {
        $grossSales = 0.00;
        $netSubtotal = 0.00;
        $taxTotal = 0.00;
        $serviceChargeTotal = 0.00;
        $discountsTotal = 0.00;

        $cashTotal = 0.00;
        $upiTotal = 0.00;
        $cardTotal = 0.00;

        $completedOrdersCount = 0;

        foreach ($orders as $order) {
            if ($order->payment_status === 'paid' || $order->status === 'completed') {
                $completedOrdersCount++;
                $total = (float)$order->total_amount;
                $grossSales += $total;
                $netSubtotal += (float)($order->subtotal ?? ($total / 1.10));
                $taxTotal += (float)($order->tax_amount ?? 0);
                $serviceChargeTotal += (float)($order->service_charge ?? 0);
                $discountsTotal += (float)($order->discount_amount ?? 0);

                $method = strtolower($order->payment_method ?? 'cash');
                if ($method === 'cash') {
                    $cashTotal += $total;
                } elseif ($method === 'upi') {
                    $upiTotal += $total;
                } elseif ($method === 'card') {
                    $cardTotal += $total;
                } else {
                    $cashTotal += $total;
                }
            }
        }

        $expectedDrawerCash = $openingCashBalance + $cashTotal;

        return [
            'report_timestamp' => now()->toIso8601String(),
            'business_date' => date('Y-m-d'),
            'total_completed_orders' => $completedOrdersCount,
            'financial_summary' => [
                'gross_sales' => round($grossSales, 2),
                'net_subtotal' => round($netSubtotal, 2),
                'total_gst_collected' => round($taxTotal, 2),
                'total_service_charge' => round($serviceChargeTotal, 2),
                'total_discounts_granted' => round($discountsTotal, 2),
            ],
            'payment_breakdown' => [
                'cash_collection' => round($cashTotal, 2),
                'upi_digital_collection' => round($upiTotal, 2),
                'card_collection' => round($cardTotal, 2),
            ],
            'cash_drawer_reconciliation' => [
                'opening_float' => round($openingCashBalance, 2),
                'cash_sales' => round($cashTotal, 2),
                'expected_cash_in_drawer' => round($expectedDrawerCash, 2),
            ],
        ];
    }
}
