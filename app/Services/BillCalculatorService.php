<?php

namespace App\Services;

use App\Models\Coupon;
use InvalidArgumentException;

/**
 * Service class for accurate restaurant bill calculations, GST tax computation,
 * service charges, itemized split billing, and promotional discount validation.
 */
class BillCalculatorService
{
    public const DEFAULT_GST_RATE = 0.05; // 5% GST (2.5% CGST + 2.5% SGST)
    public const DEFAULT_SERVICE_CHARGE_RATE = 0.05; // 5% optional service charge

    /**
     * Calculate full breakdown of bill items.
     *
     * @param array $items Array of items with 'price' and 'quantity'
     * @param Coupon|null $coupon Optional applied discount coupon
     * @param bool $applyServiceCharge Whether to include 5% service charge
     * @param float $taxRate Tax percentage rate
     * @return array Calculated summary breakdown
     */
    public function calculateBill(
        array $items,
        ?Coupon $coupon = null,
        bool $applyServiceCharge = true,
        float $taxRate = self::DEFAULT_GST_RATE
    ): array {
        if (empty($items)) {
            return [
                'subtotal' => 0.00,
                'discount_amount' => 0.00,
                'taxable_amount' => 0.00,
                'cgst_amount' => 0.00,
                'sgst_amount' => 0.00,
                'tax_amount' => 0.00,
                'service_charge' => 0.00,
                'grand_total' => 0.00,
                'rounded_total' => 0,
                'applied_coupon' => null,
            ];
        }

        $subtotal = 0.00;
        foreach ($items as $item) {
            $price = (float)($item['price'] ?? 0);
            $qty = (int)($item['quantity'] ?? 1);
            if ($price < 0 || $qty < 0) {
                throw new InvalidArgumentException("Item price and quantity cannot be negative.");
            }
            $subtotal += ($price * $qty);
        }

        $discountAmount = 0.00;
        if ($coupon && $coupon->is_active) {
            $discountAmount = $this->calculateDiscount($subtotal, $coupon);
        }

        $taxableAmount = max(0.00, $subtotal - $discountAmount);
        $taxAmount = round($taxableAmount * $taxRate, 2);
        $cgstAmount = round($taxAmount / 2, 2);
        $sgstAmount = round($taxAmount - $cgstAmount, 2);

        $serviceCharge = $applyServiceCharge ? round($taxableAmount * self::DEFAULT_SERVICE_CHARGE_RATE, 2) : 0.00;
        $grandTotal = round($taxableAmount + $taxAmount + $serviceCharge, 2);
        $roundedTotal = (int)round($grandTotal);

        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'cgst_amount' => $cgstAmount,
            'sgst_amount' => $sgstAmount,
            'tax_amount' => $taxAmount,
            'service_charge' => $serviceCharge,
            'grand_total' => $grandTotal,
            'rounded_total' => $roundedTotal,
            'applied_coupon' => $coupon ? $coupon->code : null,
        ];
    }

    /**
     * Calculate discount amount for a given coupon.
     */
    public function calculateDiscount(float $subtotal, Coupon $coupon): float
    {
        if ($subtotal < ($coupon->min_order_amount ?? 0)) {
            return 0.00;
        }

        $discount = 0.00;
        if ($coupon->discount_type === 'percentage') {
            $discount = ($subtotal * ($coupon->discount_value / 100));
            if ($coupon->max_discount && $discount > $coupon->max_discount) {
                $discount = (float)$coupon->max_discount;
            }
        } elseif ($coupon->discount_type === 'fixed') {
            $discount = (float)$coupon->discount_value;
        }

        return min($discount, $subtotal);
    }

    /**
     * Calculate equal split bill share for multiple people.
     */
    public function calculateEqualSplit(float $totalAmount, int $peopleCount): array
    {
        if ($peopleCount <= 0) {
            throw new InvalidArgumentException("People count must be at least 1.");
        }

        $sharePerPerson = round($totalAmount / $peopleCount, 2);
        $remainder = round($totalAmount - ($sharePerPerson * $peopleCount), 2);

        return [
            'people_count' => $peopleCount,
            'total_amount' => $totalAmount,
            'share_per_person' => $sharePerPerson,
            'rounding_adjustment' => $remainder,
        ];
    }
}
